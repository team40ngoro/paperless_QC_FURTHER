<?php

namespace Tests\Feature;

use App\Models\DataShift;
use App\Models\PemeriksaanBahanKemas;
use App\Models\PenerimaanChillroom;
use App\Models\Plan;
use App\Models\Rebox;
use App\Models\Role;
use App\Models\Seasoning;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class SpvProduksiApprovalTest extends TestCase
{
    use DatabaseTransactions;

    protected Plan $plan;
    protected DataShift $shift;
    protected User $produksiUser;
    protected User $qcUser;
    protected User $spvQcUser;
    protected User $spvProduksiUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::first() ?? Plan::create(['nama_plan' => 'Plan Testing SPV Produksi']);
        $this->shift = DataShift::where('id_plan', $this->plan->id)->first() ?? DataShift::create([
            'uuid' => (string) Str::uuid(),
            'shift' => '1',
            'id_plan' => $this->plan->id,
            'user_id' => 1,
        ]);

        $produksiRole = Role::firstOrCreate(['id' => 2], ['role' => 'produksi']);
        $qcRole = Role::firstOrCreate(['id' => 3], ['role' => 'QC Inspector']);
        $spvQcRole = Role::firstOrCreate(['id' => 4], ['role' => 'SPV QC']);
        $spvProduksiRole = Role::firstOrCreate(['id' => 11], ['role' => 'SPV PRODUKSI']);

        $this->produksiUser = $this->makeUser($produksiRole, 'produksi');
        $this->qcUser = $this->makeUser($qcRole, 'QC Inspector');
        $this->spvQcUser = $this->makeUser($spvQcRole, 'SPV QC');
        $this->spvProduksiUser = $this->makeUser($spvProduksiRole, 'SPV PRODUKSI');
    }

    private function makeUser(Role $role, string $roleName): User
    {
        $suffix = Str::random(8);

        return User::create([
            'name' => $roleName.' Tester '.$suffix,
            'username' => 'spv-produksi.'.$suffix,
            'email' => 'spv-produksi.'.$suffix.'@test.com',
            'password' => bcrypt('password'),
            'id_role' => $role->id,
            'role' => $roleName,
            'id_plan' => $this->plan->id,
        ]);
    }

    private function makeChillroom(array $overrides = []): PenerimaanChillroom
    {
        return PenerimaanChillroom::create(array_merge([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $this->plan->id,
            'user_id' => $this->produksiUser->id,
            'tanggal' => now(),
            'shift_id' => $this->shift->id,
            'jam_kedatangan' => '08:00',
            'nama_rm' => 'Daging Ayam Test',
            'kode_produksi' => 'CHL-'.Str::random(6),
            'berat' => '50',
            'suhu' => 2.0,
            'sensori' => '✔',
            'kemasan' => '✔',
            'standar_berat' => '450',
            'status_rm' => 'diterima',
            'catatan_rm' => 'Catatan',
        ], $overrides));
    }

    private function makeSeasoning(array $overrides = []): Seasoning
    {
        return Seasoning::create(array_merge([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $this->plan->id,
            'created_by' => $this->produksiUser->id,
            'nama_rm' => 'Seasoning Test',
            'kode_produksi' => 'SEA-'.Str::random(6),
            'berat' => '25',
            'sensori' => '-',
            'kemasan' => '✔',
            'keterangan' => 'Data produksi',
            'shift_id' => $this->shift->id,
            'tanggal' => now()->format('Y-m-d H:i:s'),
            'jam' => '09:00',
        ], $overrides));
    }

    private function makeRebox(array $overrides = []): Rebox
    {
        return Rebox::create(array_merge([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $this->plan->id,
            'created_by' => $this->produksiUser->id,
            'nama_produk' => 'Produk Rebox Test',
            'kode_produksi' => 'RBX-'.Str::random(6),
            'best_before' => now()->addDays(3)->format('Y-m-d'),
            'isi_jumlah' => '10',
            'labelisasi' => 'OK',
            'shift_id' => $this->shift->id,
            'tanggal' => now()->format('Y-m-d H:i:s'),
            'jam' => '09:00',
        ], $overrides));
    }

    private function makeBahanKemas(array $overrides = []): PemeriksaanBahanKemas
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

    private function qcVerified(): array
    {
        return [
            'approved_by_qc' => true,
            'qc_approved_by' => $this->qcUser->id,
            'qc_approved_at' => now(),
        ];
    }

    public function test_spv_produksi_can_approve_produksi_on_chillroom(): void
    {
        $item = $this->makeChillroom($this->qcVerified());

        $response = $this->actingAs($this->spvProduksiUser)->postJson(route('chillroom.approve', $item->uuid), [
            'type' => 'produksi',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertTrue($item->fresh()->approved_by_produksi);
        $this->assertEquals($this->spvProduksiUser->id, $item->fresh()->produksi_approved_by);
    }

    public function test_regular_produksi_cannot_approve_produksi_on_chillroom(): void
    {
        $item = $this->makeChillroom($this->qcVerified());

        $response = $this->actingAs($this->produksiUser)->postJson(route('chillroom.approve', $item->uuid), [
            'type' => 'produksi',
        ]);

        $response->assertForbidden();
        $this->assertFalse($item->fresh()->approved_by_produksi);
    }

    public function test_spv_produksi_can_approve_produksi_on_seasoning(): void
    {
        $item = $this->makeSeasoning($this->qcVerified());

        $response = $this->actingAs($this->spvProduksiUser)->postJson(route('seasoning.approve', $item->uuid), [
            'type' => 'produksi',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertTrue($item->fresh()->approved_by_produksi);
        $this->assertEquals($this->spvProduksiUser->id, $item->fresh()->produksi_approved_by);
    }

    public function test_regular_produksi_cannot_approve_produksi_on_seasoning(): void
    {
        $item = $this->makeSeasoning($this->qcVerified());

        $response = $this->actingAs($this->produksiUser)->postJson(route('seasoning.approve', $item->uuid), [
            'type' => 'produksi',
        ]);

        $response->assertForbidden();
        $this->assertFalse($item->fresh()->approved_by_produksi);
    }

    public function test_spv_produksi_can_approve_produksi_on_rebox(): void
    {
        $item = $this->makeRebox($this->qcVerified());

        $response = $this->actingAs($this->spvProduksiUser)->postJson(route('rebox.approve', $item->uuid), [
            'type' => 'produksi',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertTrue($item->fresh()->approved_by_produksi);
        $this->assertEquals($this->spvProduksiUser->id, $item->fresh()->produksi_approved_by);
    }

    public function test_regular_produksi_cannot_approve_produksi_on_rebox(): void
    {
        $item = $this->makeRebox($this->qcVerified());

        $response = $this->actingAs($this->produksiUser)->postJson(route('rebox.approve', $item->uuid), [
            'type' => 'produksi',
        ]);

        $response->assertForbidden();
        $this->assertFalse($item->fresh()->approved_by_produksi);
    }

    public function test_spv_produksi_can_approve_produksi_on_bahan_kemas(): void
    {
        $item = $this->makeBahanKemas($this->qcVerified());

        $response = $this->actingAs($this->spvProduksiUser)->postJson(route('pemeriksaan-bahan-kemas.approve', $item->uuid), [
            'type' => 'produksi',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertTrue($item->fresh()->approved_by_produksi);
        $this->assertEquals($this->spvProduksiUser->id, $item->fresh()->produksi_approved_by);
    }

    public function test_regular_produksi_cannot_approve_produksi_on_bahan_kemas(): void
    {
        $item = $this->makeBahanKemas($this->qcVerified());

        $response = $this->actingAs($this->produksiUser)->postJson(route('pemeriksaan-bahan-kemas.approve', $item->uuid), [
            'type' => 'produksi',
        ]);

        $response->assertForbidden();
        $this->assertFalse($item->fresh()->approved_by_produksi);
    }

    public function test_spv_produksi_index_shows_approval_action_on_chillroom(): void
    {
        $this->makeChillroom();

        $response = $this->actingAs($this->spvProduksiUser)->get(route('chillroom.index'));

        $response->assertOk();
        $response->assertSee('Diketahui Produksi');
        $response->assertSee('bulk-approval-checkbox', false);
    }

    public function test_spv_produksi_index_shows_approval_action_on_rebox(): void
    {
        $this->makeRebox();

        $response = $this->actingAs($this->spvProduksiUser)->get(route('rebox.index'));

        $response->assertOk();
        $response->assertSee('Diketahui Produksi');
        $response->assertSee('row-checkbox', false);
    }

    public function test_spv_produksi_index_shows_approval_action_on_bahan_kemas(): void
    {
        $this->makeBahanKemas();

        $response = $this->actingAs($this->spvProduksiUser)->get(route('pemeriksaan-bahan-kemas.index'));

        $response->assertOk();
        $response->assertSee('Diketahui Produksi');
        $response->assertSee('row-checkbox', false);
    }

    public function test_spv_produksi_show_page_shows_approval_action_on_seasoning(): void
    {
        $item = $this->makeSeasoning($this->qcVerified());

        $response = $this->actingAs($this->spvProduksiUser)->get(route('seasoning.show', $item->uuid));

        $response->assertOk();
        $response->assertSee('Diketahui Produksi');
        $response->assertSee('btnApproveProduksi', false);
    }
}
