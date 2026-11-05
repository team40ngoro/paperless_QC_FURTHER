<?php

namespace Tests\Feature;

use App\Models\DataShift;
use App\Models\PemeriksaanBahanKemas;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class PemeriksaanBahanKemasVerificationTest extends TestCase
{
    use DatabaseTransactions;

    protected Plan $plan;
    protected DataShift $shift;
    protected User $produksiUser;
    protected User $qcUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::first() ?? Plan::create(['nama_plan' => 'Plan Testing Bahan Kemas']);
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
            'username' => 'bahan-kemas.'.$suffix,
            'email' => 'bahan-kemas.'.$suffix.'@test.com',
            'password' => bcrypt('password'),
            'id_role' => $role->id,
            'role' => $roleName,
            'id_plan' => $this->plan->id,
        ]);
    }

    private function makeItem(array $overrides = []): PemeriksaanBahanKemas
    {
        return PemeriksaanBahanKemas::create(array_merge([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $this->plan->id,
            'user_id' => $this->produksiUser->id,
            'shift_id' => $this->shift->id,
            'tanggal' => now()->format('Y-m-d H:i:s'),
            'jam' => '09:00',
            'nama_kemasan' => 'Kemasan Test',
            'kode_produksi' => 'KMS-'.Str::random(6),
            'kondisi_bahan_kemasan' => 'OK',
            'keterangan' => 'Data produksi',
        ], $overrides));
    }

    public function test_qc_can_store_verifikasi_kedatangan_bahan_kemas(): void
    {
        $item = $this->makeItem();

        $response = $this->actingAs($this->qcUser)->post(
            route('pemeriksaan-bahan-kemas.verifikasi-kedatangan.store', $item->uuid),
            ['verifikasi_kedatangan' => 'OK']
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('pemeriksaan_bahan_kemas', [
            'id' => $item->id,
            'verifikasi_kedatangan' => 'OK',
            'verifikasi_kedatangan_by' => $this->qcUser->id,
        ]);

        $this->assertDatabaseHas('pemeriksaan_bahan_kemas_logs', [
            'pemeriksaan_bahan_kemas_id' => $item->id,
            'aksi' => 'verifikasi_kedatangan',
            'user_id' => $this->qcUser->id,
        ]);
    }

    public function test_qc_can_store_tidak_oke_verifikasi_kedatangan_bahan_kemas(): void
    {
        $item = $this->makeItem();

        $this->actingAs($this->qcUser)->post(
            route('pemeriksaan-bahan-kemas.verifikasi-kedatangan.store', $item->uuid),
            ['verifikasi_kedatangan' => 'NOT_OK']
        );

        $this->assertEquals('NOT_OK', $item->fresh()->verifikasi_kedatangan);
    }

    public function test_produksi_cannot_store_verifikasi_kedatangan_bahan_kemas(): void
    {
        $item = $this->makeItem();

        $response = $this->actingAs($this->produksiUser)->post(
            route('pemeriksaan-bahan-kemas.verifikasi-kedatangan.store', $item->uuid),
            ['verifikasi_kedatangan' => 'OK']
        );

        $response->assertForbidden();
    }

    public function test_verifikasi_kedatangan_status_is_required(): void
    {
        $item = $this->makeItem();

        $response = $this->actingAs($this->qcUser)->post(
            route('pemeriksaan-bahan-kemas.verifikasi-kedatangan.store', $item->uuid),
            []
        );

        $response->assertSessionHasErrors('verifikasi_kedatangan');
    }

    public function test_show_displays_verifikasi_kedatangan_group_and_criteria(): void
    {
        $item = $this->makeItem();

        $response = $this->actingAs($this->qcUser)->get(route('pemeriksaan-bahan-kemas.show', $item->uuid));

        $response->assertStatus(200);
        $response->assertSee('Verifikasi Kedatangan Bahan Kemas');
        $response->assertSee('v = Oke');
        $response->assertSee('sesuai spesifikasi');
        $response->assertSee('x = Tidak Oke');
        $response->assertSee('tidak sesuai spesifikasi');
    }

    public function test_show_displays_saved_verifikasi_kedatangan_status(): void
    {
        $item = $this->makeItem([
            'verifikasi_kedatangan' => 'NOT_OK',
            'verifikasi_kedatangan_by' => $this->qcUser->id,
            'verifikasi_kedatangan_at' => now(),
        ]);

        $response = $this->actingAs($this->produksiUser)->get(route('pemeriksaan-bahan-kemas.show', $item->uuid));

        $response->assertStatus(200);
        $response->assertSee('Tidak Oke');
    }
}
