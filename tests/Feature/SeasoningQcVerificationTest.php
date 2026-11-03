<?php

namespace Tests\Feature;

use App\Models\DataShift;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Seasoning;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SeasoningQcVerificationTest extends TestCase
{
    use DatabaseTransactions;

    protected Plan $plan;
    protected DataShift $shift;
    protected User $produksiUser;
    protected User $qcUser;
    protected User $spvUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::first() ?? Plan::create(['nama_plan' => 'Plan Testing Seasoning']);
        $this->shift = DataShift::where('id_plan', $this->plan->id)->first() ?? DataShift::create([
            'uuid' => (string) Str::uuid(),
            'shift' => '1',
            'id_plan' => $this->plan->id,
            'user_id' => 1,
        ]);

        $produksiRole = Role::firstOrCreate(['id' => 2], ['role' => 'produksi']);
        $qcRole = Role::firstOrCreate(['id' => 3], ['role' => 'QC Inspector']);
        $spvRole = Role::firstOrCreate(['id' => 4], ['role' => 'SPV QC']);

        $createPerm = Permission::firstOrCreate(['name' => 'create-seasoning', 'guard_name' => 'web']);
        $editPerm = Permission::firstOrCreate(['name' => 'edit-seasoning', 'guard_name' => 'web']);
        $viewPerm = Permission::firstOrCreate(['name' => 'view-seasoning', 'guard_name' => 'web']);

        $produksiRole->permissions()->syncWithoutDetaching([$createPerm->id, $editPerm->id, $viewPerm->id]);
        $qcRole->permissions()->syncWithoutDetaching([$viewPerm->id]);
        $spvRole->permissions()->syncWithoutDetaching([$viewPerm->id]);

        $this->produksiUser = $this->makeUser($produksiRole, 'produksi');
        $this->qcUser = $this->makeUser($qcRole, 'QC Inspector');
        $this->spvUser = $this->makeUser($spvRole, 'SPV QC');
    }

    private function makeUser(Role $role, string $roleName): User
    {
        $suffix = Str::random(8);

        return User::create([
            'name' => $roleName.' Tester '.$suffix,
            'username' => 'seasoning.'.$suffix,
            'email' => 'seasoning.'.$suffix.'@test.com',
            'password' => bcrypt('password'),
            'id_role' => $role->id,
            'role' => $roleName,
            'id_plan' => $this->plan->id,
        ]);
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

    public function test_produksi_create_page_does_not_show_sensori_input(): void
    {
        $response = $this->actingAs($this->produksiUser)->get(route('seasoning.create'));

        $response->assertStatus(200);
        $response->assertDontSee('entries[0][sensori]', false);
    }

    public function test_produksi_can_store_seasoning_without_sensori_input(): void
    {
        $response = $this->actingAs($this->produksiUser)->post(route('seasoning.store'), [
            'tanggal' => '2026-11-03',
            'jam' => '09:00',
            'shift_id' => $this->shift->id,
            'entries' => [[
                'nama_rm' => 'Seasoning Tanpa Sensori',
                'kode_produksi' => 'SEA-NO-SENSORI',
                'berat' => '25',
                'kemasan' => '✔',
                'keterangan' => 'Input produksi',
            ]],
        ]);

        $response->assertRedirect(route('seasoning.index'));
        $this->assertDatabaseHas('seasonings', [
            'kode_produksi' => 'SEA-NO-SENSORI',
            'sensori' => '-',
        ]);
    }

    public function test_qc_can_submit_verification_for_all_groups(): void
    {
        $seasoning = $this->makeSeasoning();

        $response = $this->actingAs($this->qcUser)->post(route('seasoning.qc-verifications.store', $seasoning->uuid), [
            'data_status' => 'OK',
            'sensori_status' => 'OK',
            'kemasan_status' => 'OK',
            'kondisi_fisik_status' => 'OK',
            'note' => 'Semua sesuai spesifikasi',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('seasoning_qc_verifications', [
            'seasoning_id' => $seasoning->id,
            'data_status' => 'OK',
            'sensori_status' => 'OK',
            'kemasan_status' => 'OK',
            'kondisi_fisik_status' => 'OK',
            'note' => 'Semua sesuai spesifikasi',
            'verified_by' => $this->qcUser->id,
        ]);

        $seasoning->refresh();
        $this->assertTrue($seasoning->approved_by_qc);
        $this->assertEquals($this->qcUser->id, $seasoning->qc_approved_by);
        $this->assertEquals('✔', $seasoning->sensori);
    }

    public function test_produksi_cannot_submit_qc_verification(): void
    {
        $seasoning = $this->makeSeasoning();

        $response = $this->actingAs($this->produksiUser)->post(route('seasoning.qc-verifications.store', $seasoning->uuid), [
            'data_status' => 'OK',
            'sensori_status' => 'OK',
            'kemasan_status' => 'OK',
        ]);

        $response->assertForbidden();
    }

    public function test_spv_cannot_submit_qc_verification(): void
    {
        $seasoning = $this->makeSeasoning();

        $response = $this->actingAs($this->spvUser)->post(route('seasoning.qc-verifications.store', $seasoning->uuid), [
            'data_status' => 'OK',
            'sensori_status' => 'OK',
            'kemasan_status' => 'OK',
        ]);

        $response->assertForbidden();
    }

    public function test_spv_can_approve_after_qc_verification(): void
    {
        $seasoning = $this->makeSeasoning([
            'approved_by_qc' => true,
            'qc_approved_by' => $this->qcUser->id,
            'qc_approved_at' => now(),
        ]);

        $response = $this->actingAs($this->spvUser)->postJson(route('seasoning.approve', $seasoning->uuid), [
            'type' => 'spv',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertTrue($seasoning->fresh()->approved_by_spv);
    }

    public function test_spv_cannot_approve_before_qc_verification(): void
    {
        $seasoning = $this->makeSeasoning();

        $response = $this->actingAs($this->spvUser)->postJson(route('seasoning.approve', $seasoning->uuid), [
            'type' => 'spv',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'Data harus diverifikasi oleh QC terlebih dahulu sebelum dapat disetujui oleh SPV',
        ]);
    }

    public function test_overall_status_is_not_ok_when_any_group_not_ok(): void
    {
        $seasoning = $this->makeSeasoning();

        $this->actingAs($this->qcUser)->post(route('seasoning.qc-verifications.store', $seasoning->uuid), [
            'data_status' => 'OK',
            'sensori_status' => 'NOT_OK',
            'kemasan_status' => 'OK',
            'kondisi_fisik_status' => 'OK',
            'note' => 'Sensori basah',
        ]);

        $verification = $seasoning->fresh()->latestQcVerification;
        $this->assertNotNull($verification);
        $this->assertEquals('NOT_OK', $verification->overall_status);
        $this->assertEquals('✘', $seasoning->fresh()->sensori);
    }

    public function test_overall_status_is_not_ok_when_kondisi_fisik_not_ok(): void
    {
        $seasoning = $this->makeSeasoning();

        $this->actingAs($this->qcUser)->post(route('seasoning.qc-verifications.store', $seasoning->uuid), [
            'data_status' => 'OK',
            'sensori_status' => 'OK',
            'kemasan_status' => 'OK',
            'kondisi_fisik_status' => 'NOT_OK',
            'note' => 'Bahan menggumpal',
        ]);

        $verification = $seasoning->fresh()->latestQcVerification;
        $this->assertNotNull($verification);
        $this->assertEquals('NOT_OK', $verification->overall_status);
        $this->assertEquals('NOT_OK', $verification->kondisi_fisik_status);
    }

    public function test_qc_verification_requires_kondisi_fisik_status(): void
    {
        $seasoning = $this->makeSeasoning();

        $response = $this->actingAs($this->qcUser)->post(route('seasoning.qc-verifications.store', $seasoning->uuid), [
            'data_status' => 'OK',
            'sensori_status' => 'OK',
            'kemasan_status' => 'OK',
        ]);

        $response->assertSessionHasErrors('kondisi_fisik_status');
    }

    public function test_index_no_longer_shows_bulk_approval_buttons(): void
    {
        $response = $this->actingAs($this->qcUser)->get(route('seasoning.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Approve QC');
        $response->assertDontSee('Approve Produksi');
        $response->assertDontSee('Approve SPV');
    }

    public function test_qc_show_page_displays_verification_form_and_production_data(): void
    {
        $seasoning = $this->makeSeasoning(['nama_rm' => 'Seasoning Show']);

        $response = $this->actingAs($this->qcUser)->get(route('seasoning.show', $seasoning->uuid));

        $response->assertStatus(200);
        $response->assertSee('Seasoning Show');
        $response->assertSee('Verifikasi QC');
        $response->assertSee('Waktu Pengecekan');
        $response->assertSee('Kondisi Fisik');
        $response->assertSee('tidak menggumpal');
        $response->assertSee('Simpan Verifikasi QC');
    }

    public function test_produksi_show_page_does_not_show_qc_submit_form(): void
    {
        $seasoning = $this->makeSeasoning();

        $response = $this->actingAs($this->produksiUser)->get(route('seasoning.show', $seasoning->uuid));

        $response->assertStatus(200);
        $response->assertDontSee('Simpan Verifikasi QC');
    }

    public function test_show_displays_latest_qc_verification_result(): void
    {
        $seasoning = $this->makeSeasoning();

        $this->actingAs($this->qcUser)->post(route('seasoning.qc-verifications.store', $seasoning->uuid), [
            'data_status' => 'OK',
            'sensori_status' => 'NOT_OK',
            'kemasan_status' => 'OK',
            'kondisi_fisik_status' => 'OK',
            'note' => 'Sensori basah',
        ]);

        $response = $this->actingAs($this->produksiUser)->get(route('seasoning.show', $seasoning->uuid));

        $response->assertStatus(200);
        $response->assertSee('Tidak Oke');
        $response->assertSee('Sensori basah');
    }
}
