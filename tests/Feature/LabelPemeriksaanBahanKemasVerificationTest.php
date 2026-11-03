<?php

namespace Tests\Feature;

use App\Models\DataShift;
use App\Models\LabelStartUp;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class LabelPemeriksaanBahanKemasVerificationTest extends TestCase
{
    use DatabaseTransactions;

    protected Plan $plan;
    protected DataShift $shift;
    protected User $produksiUser;
    protected User $qcUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::first() ?? Plan::create(['nama_plan' => 'Plan Testing Label']);
        $this->shift = DataShift::where('id_plan', $this->plan->id)->first() ?? DataShift::create([
            'uuid' => (string) Str::uuid(),
            'shift' => '1',
            'id_plan' => $this->plan->id,
            'user_id' => 1,
        ]);

        $produksiRole = Role::firstOrCreate(['id' => 2], ['role' => 'produksi']);
        $qcRole = Role::firstOrCreate(['id' => 3], ['role' => 'QC Inspector']);

        $this->produksiUser = $this->makeUser($produksiRole, 'produksi');
        $this->qcUser = $this->makeUser($qcRole, 'QC Inspector');
    }

    private function makeUser(Role $role, string $roleName): User
    {
        $suffix = Str::random(8);

        return User::create([
            'name' => $roleName.' Tester '.$suffix,
            'username' => 'label.'.$suffix,
            'email' => 'label.'.$suffix.'@test.com',
            'password' => bcrypt('password'),
            'id_role' => $role->id,
            'role' => $roleName,
            'id_plan' => $this->plan->id,
        ]);
    }

    private function makeLabel(array $overrides = []): LabelStartUp
    {
        return LabelStartUp::create(array_merge([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $this->plan->id,
            'id_shift' => $this->shift->id,
            'user_id' => $this->produksiUser->id,
            'tanggal' => now()->format('Y-m-d'),
        ], $overrides));
    }

    public function test_qc_can_store_verifikasi_kedatangan_bahan_kemas(): void
    {
        $label = $this->makeLabel();

        $response = $this->actingAs($this->qcUser)->post(
            route('label-pemeriksaan.verifikasi-bahan-kemas.store', $label->uuid),
            ['verifikasi_bahan_kemas' => 'OK']
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('label_start_ups', [
            'id' => $label->id,
            'verifikasi_bahan_kemas' => 'OK',
            'verifikasi_bahan_kemas_by' => $this->qcUser->id,
        ]);
    }

    public function test_qc_can_store_tidak_oke_verifikasi_kedatangan_bahan_kemas(): void
    {
        $label = $this->makeLabel();

        $this->actingAs($this->qcUser)->post(
            route('label-pemeriksaan.verifikasi-bahan-kemas.store', $label->uuid),
            ['verifikasi_bahan_kemas' => 'NOT_OK']
        );

        $this->assertEquals('NOT_OK', $label->fresh()->verifikasi_bahan_kemas);
    }

    public function test_produksi_cannot_store_verifikasi_kedatangan_bahan_kemas(): void
    {
        $label = $this->makeLabel();

        $response = $this->actingAs($this->produksiUser)->post(
            route('label-pemeriksaan.verifikasi-bahan-kemas.store', $label->uuid),
            ['verifikasi_bahan_kemas' => 'OK']
        );

        $response->assertForbidden();
    }

    public function test_verifikasi_bahan_kemas_status_is_required(): void
    {
        $label = $this->makeLabel();

        $response = $this->actingAs($this->qcUser)->post(
            route('label-pemeriksaan.verifikasi-bahan-kemas.store', $label->uuid),
            []
        );

        $response->assertSessionHasErrors('verifikasi_bahan_kemas');
    }

    public function test_show_displays_verifikasi_kedatangan_bahan_kemas_group_and_criteria(): void
    {
        $label = $this->makeLabel();

        $response = $this->actingAs($this->qcUser)->get(route('label-pemeriksaan.show', $label->uuid));

        $response->assertStatus(200);
        $response->assertSee('Verifikasi Kedatangan Bahan Kemas');
        $response->assertSee('v = Oke');
        $response->assertSee('sesuai spesifikasi');
        $response->assertSee('x = Tidak Oke');
        $response->assertSee('tidak sesuai spesifikasi');
    }

    public function test_show_displays_saved_verifikasi_bahan_kemas_status(): void
    {
        $label = $this->makeLabel([
            'verifikasi_bahan_kemas' => 'NOT_OK',
            'verifikasi_bahan_kemas_by' => $this->qcUser->id,
            'verifikasi_bahan_kemas_at' => now(),
        ]);

        $response = $this->actingAs($this->produksiUser)->get(route('label-pemeriksaan.show', $label->uuid));

        $response->assertStatus(200);
        $response->assertSee('Tidak Oke');
    }
}
