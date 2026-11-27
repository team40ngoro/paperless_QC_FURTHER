<?php

namespace Tests\Feature;

use App\Models\AktualBetter;
use App\Models\DataShift;
use App\Models\JenisBetter;
use App\Models\JenisProduk;
use App\Models\PersiapanBahanBetter;
use App\Models\Plan;
use App\Models\Role;
use App\Models\StdSalinitasViskositas;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PersiapanBahanBetterQcVerificationTest extends TestCase
{
    use DatabaseTransactions;

    protected Plan $plan;
    protected JenisProduk $produk;
    protected JenisBetter $better;
    protected DataShift $shift;
    protected StdSalinitasViskositas $std;
    protected PersiapanBahanBetter $item;
    protected User $produksiUser;
    protected User $qcUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::first() ?? Plan::create(['nama_plan' => 'Plan Testing Better']);

        $produksiRole = Role::firstOrCreate(['id' => 2], ['role' => 'produksi']);
        $qcRole = Role::firstOrCreate(['id' => 3], ['role' => 'QC Inspector']);

        $viewPerm = Permission::firstOrCreate(['name' => 'view-persiapan-bahan-better', 'guard_name' => 'web']);
        $editPerm = Permission::firstOrCreate(['name' => 'edit-persiapan-bahan-better', 'guard_name' => 'web']);

        foreach ([$produksiRole, $qcRole] as $role) {
            $role->permissions()->syncWithoutDetaching([$viewPerm->id, $editPerm->id]);
        }

        $this->produksiUser = $this->makeUser($produksiRole, 'produksi');
        $this->qcUser = $this->makeUser($qcRole, 'QC Inspector');

        $this->shift = DataShift::create([
            'uuid' => (string) Str::uuid(),
            'shift' => '1',
            'id_plan' => $this->plan->id,
            'user_id' => $this->produksiUser->id,
        ]);

        $this->produk = JenisProduk::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $this->plan->id,
            'nama_produk' => 'Produk Better Test',
            'user_id' => $this->produksiUser->id,
            'status_bahan' => 'better',
        ]);

        $this->better = JenisBetter::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $this->produksiUser->id,
            'id_plan' => $this->plan->id,
            'id_produk' => $this->produk->id,
            'nama_better' => 'Better Test',
        ]);

        $this->std = StdSalinitasViskositas::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $this->produksiUser->id,
            'id_plan' => $this->plan->id,
            'id_produk' => $this->produk->id,
            'id_better' => $this->better->id,
            'std_viskositas' => '20',
            'std_salinitas' => '2',
            'std_suhu_akhir' => '8',
        ]);

        $this->item = PersiapanBahanBetter::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $this->produksiUser->id,
            'id_plan' => $this->plan->id,
            'id_produk' => $this->produk->id,
            'id_better' => $this->better->id,
            'berat_better' => 100,
            'gramase' => 100,
            'suhu_air' => 8,
            'shift_id' => $this->shift->id,
            'tanggal' => now()->format('Y-m-d H:i:s'),
            'jam' => '09:00',
            'kode_produksi_produk' => 'KP-TEST',
            'better_rows' => [
                ['master_nama_formula_better' => 'Bahan A', 'master_berat' => 100, 'suhu_air' => 8],
            ],
        ]);

        AktualBetter::create([
            'uuid' => (string) Str::uuid(),
            'id_std_salinitas_viskositas' => $this->std->id,
            'id_persiapan_bahan_better' => $this->item->id,
            'aktual_vis' => '',
            'aktual_sal' => '',
            'aktual_suhu_air' => '',
        ]);
    }

    private function makeUser(Role $role, string $roleName): User
    {
        $suffix = Str::random(8);

        return User::create([
            'name' => $roleName.' Tester '.$suffix,
            'username' => 'better.'.$suffix,
            'email' => 'better.'.$suffix.'@test.com',
            'password' => bcrypt('password'),
            'id_role' => $role->id,
            'role' => $roleName,
            'id_plan' => $this->plan->id,
        ]);
    }

    private function verificationUrl(): string
    {
        return route('persiapan-bahan-better.qc-verification.store', $this->item->uuid);
    }

    public function test_qc_inspector_can_save_verification_from_show_and_auto_approve(): void
    {
        $response = $this->actingAs($this->qcUser)->post($this->verificationUrl(), [
            'global_sensori' => '✔',
            'id_std_salinitas_viskositas' => [$this->std->id],
            'aktual_vis' => ['25.5'],
            'aktual_sal' => ['2.1'],
            'aktual_suhu_air' => ['8.5'],
        ]);

        $response->assertRedirect(route('persiapan-bahan-better.show', $this->item->uuid));

        $item = $this->item->fresh();
        $this->assertSame('✔', $item->sensori);
        $this->assertTrue((bool) $item->approved_by_qc);
        $this->assertSame($this->qcUser->id, $item->qc_approved_by);
        $this->assertNotNull($item->qc_approved_at);

        $this->assertDatabaseHas('aktual_better', [
            'id_persiapan_bahan_better' => $this->item->id,
            'aktual_vis' => '25.5',
            'aktual_sal' => '2.1',
            'aktual_suhu_air' => '8.5',
        ]);
    }

    public function test_non_qc_user_cannot_save_verification(): void
    {
        $response = $this->actingAs($this->produksiUser)->post($this->verificationUrl(), [
            'global_sensori' => '✔',
            'id_std_salinitas_viskositas' => [$this->std->id],
            'aktual_vis' => ['25.5'],
            'aktual_sal' => ['2.1'],
            'aktual_suhu_air' => ['8.5'],
        ]);

        $response->assertForbidden();

        $item = $this->item->fresh();
        $this->assertFalse((bool) $item->approved_by_qc);
        $this->assertNull($item->qc_approved_by);
    }

    public function test_show_page_shows_verification_form_only_for_qc_inspector(): void
    {
        $this->actingAs($this->qcUser)
            ->get(route('persiapan-bahan-better.show', $this->item->uuid))
            ->assertOk()
            ->assertSee('Simpan Verifikasi QC');

        $this->actingAs($this->produksiUser)
            ->get(route('persiapan-bahan-better.show', $this->item->uuid))
            ->assertOk()
            ->assertDontSee('Simpan Verifikasi QC');
    }

    public function test_qc_inspector_is_redirected_from_edit_to_show(): void
    {
        $this->actingAs($this->qcUser)
            ->get(route('persiapan-bahan-better.edit', $this->item->uuid))
            ->assertRedirect(route('persiapan-bahan-better.show', $this->item->uuid));
    }
}
