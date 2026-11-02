<?php

namespace Tests\Feature;

use App\Models\DataRm;
use App\Models\DataShift;
use App\Models\PenerimaanChillroom;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ChillroomQcRoleRestrictionTest extends TestCase
{
    use DatabaseTransactions;

    protected User $qcUser;
    protected User $adminUser;
    protected User $produksiUser;
    protected Plan $plan;
    protected DataShift $shift;
    protected DataRm $dataRm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::first() ?? Plan::create(['nama_plan' => 'Plan Testing']);
        $this->shift = DataShift::where('id_plan', $this->plan->id)->first() ?? DataShift::create([
            'uuid' => (string) Str::uuid(),
            'shift' => '1',
            'id_plan' => $this->plan->id,
            'user_id' => 1,
        ]);
        $this->dataRm = DataRm::where('id_plan', $this->plan->id)->first() ?? DataRm::create([
            'uuid' => (string) Str::uuid(),
            'nama_rm' => 'Daging Ayam Paha Test',
            'id_plan' => $this->plan->id,
        ]);

        $qcRole = Role::firstOrCreate(['id' => 3], ['role' => 'QC Inspector']);
        $adminRole = Role::firstOrCreate(['id' => 1], ['role' => 'admin']);
        $prodRole = Role::firstOrCreate(['id' => 2], ['role' => 'produksi']);

        $createPerm = Permission::firstOrCreate(['name' => 'create-chillroom', 'guard_name' => 'web']);
        $editPerm = Permission::firstOrCreate(['name' => 'edit-chillroom', 'guard_name' => 'web']);
        $viewPerm = Permission::firstOrCreate(['name' => 'view-chillroom', 'guard_name' => 'web']);

        $qcRole->permissions()->syncWithoutDetaching([$createPerm->id, $editPerm->id, $viewPerm->id]);
        $adminRole->permissions()->syncWithoutDetaching([$createPerm->id, $editPerm->id, $viewPerm->id]);
        $prodRole->permissions()->syncWithoutDetaching([$createPerm->id, $editPerm->id, $viewPerm->id]);

        $this->qcUser = User::create([
            'name' => 'QC Tester',
            'username' => 'qc.tester.' . Str::random(5),
            'email' => 'qc.' . Str::random(5) . '@test.com',
            'password' => bcrypt('password'),
            'id_role' => 3,
            'role' => 'QC Inspector',
            'id_plan' => $this->plan->id,
        ]);

        $this->adminUser = User::create([
            'name' => 'Admin Tester',
            'username' => 'admin.tester.' . Str::random(5),
            'email' => 'admin.' . Str::random(5) . '@test.com',
            'password' => bcrypt('password'),
            'id_role' => 1,
            'role' => 'admin',
            'id_plan' => $this->plan->id,
        ]);

        $this->produksiUser = User::create([
            'name' => 'Produksi Tester',
            'username' => 'prod.tester.' . Str::random(5),
            'email' => 'prod.' . Str::random(5) . '@test.com',
            'password' => bcrypt('password'),
            'id_role' => 2,
            'role' => 'produksi',
            'id_plan' => $this->plan->id,
        ]);
    }

    public function test_qc_user_sees_sampling_spo_as_readonly_on_create_page(): void
    {
        $response = $this->actingAs($this->qcUser)->get(route('chillroom.create'));

        $response->assertStatus(200);
        $response->assertSee('sampling-spo-muted');
        $response->assertSee('Readonly (Role QC)');
        $response->assertDontSee('Tambah Sampling Berat (Frozen)');
    }

    public function test_admin_user_sees_sampling_spo_editable_on_create_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('chillroom.create'));

        $response->assertStatus(200);
        $response->assertSee('Tambah Sampling Berat (Frozen)');
        $response->assertDontSee('Readonly (Role QC)');
    }

    public function test_qc_user_can_input_detail_bahan_baku_and_sampling_spo_is_blanked_on_store(): void
    {
        $payload = [
            'tanggal' => '2026-10-30',
            'shift_id' => $this->shift->id,
            'jam_kedatangan' => '09:00',
            'entries' => [
                [
                    'nama_rm' => $this->dataRm->nama_rm,
                    'kode_produksi' => 'PROD-QC-001',
                    'berat' => '100',
                    'suhu' => 3.5,
                    'sensori' => '✔',
                    'kemasan' => '✔',
                    'keterangan' => 'Bahan bagus',
                    // Attempt to send sampling SPO fields:
                    'standar_berat' => '500',
                    'berat_atas' => 10,
                    'berat_std' => 20,
                    'berat_bawah' => 5,
                    'status_rm' => 'diterima',
                    'catatan_rm' => 'Sampling bagus',
                    'jumlah_rm_value' => [501, 502],
                ],
            ],
        ];

        $response = $this->actingAs($this->qcUser)->post(route('chillroom.store'), $payload);

        $response->assertRedirect(route('chillroom.index'));

        $created = PenerimaanChillroom::where('kode_produksi', 'PROD-QC-001')->first();
        $this->assertNotNull($created);
        // Detail Bahan Baku should be saved correctly:
        $this->assertEquals($this->dataRm->nama_rm, $created->nama_rm);
        $this->assertEquals('100', $created->berat);
        $this->assertEquals(3.5, (float) $created->suhu);
        $this->assertEquals('✔', $created->sensori);

        // Sampling SPO fields should be blanked / muted for QC:
        $this->assertEquals('-', $created->standar_berat);
        $this->assertEquals('-', $created->status_rm);
        $this->assertEquals('-', $created->catatan_rm);

        $jumlahRm = json_decode($created->jumlah_rm, true);
        $this->assertEquals(0, $jumlahRm['entry_1']['berat_atas'] ?? null);
        $this->assertEquals(0, $jumlahRm['entry_1']['berat_std'] ?? null);
        $this->assertEquals(0, $jumlahRm['entry_1']['berat_bawah'] ?? null);

        $nilaiSampel = json_decode($created->nilai_jumlah_rm, true);
        $this->assertEmpty($nilaiSampel['entry_1'] ?? null);
    }

    public function test_qc_user_sees_sampling_spo_as_readonly_on_edit_page(): void
    {
        $item = PenerimaanChillroom::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $this->plan->id,
            'user_id' => $this->adminUser->id,
            'tanggal' => now(),
            'shift_id' => $this->shift->id,
            'jam_kedatangan' => '08:00',
            'nama_rm' => $this->dataRm->nama_rm,
            'kode_produksi' => 'EDIT-001',
            'berat' => '50',
            'suhu' => 2.0,
            'sensori' => '✔',
            'kemasan' => '✔',
            'standar_berat' => '450',
            'status_rm' => 'diterima',
            'catatan_rm' => 'Catatan awal',
            'jumlah_rm' => json_encode(['entry_1' => ['berat_atas' => 2, 'berat_std' => 10, 'berat_bawah' => 1]]),
            'nilai_jumlah_rm' => json_encode(['entry_1' => [450.5]]),
        ]);

        $response = $this->actingAs($this->qcUser)->get(route('chillroom.edit', $item->uuid));

        $response->assertStatus(200);
        $response->assertSee('sampling-spo-muted');
        $response->assertSee('Readonly (Role QC)');
    }

    public function test_qc_update_preserves_existing_sampling_spo_data(): void
    {
        $initialJumlahRm = ['entry_1' => ['berat_atas' => 2, 'berat_std' => 10, 'berat_bawah' => 1]];
        $initialNilaiSampel = ['entry_1' => [450.5]];

        $item = PenerimaanChillroom::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $this->plan->id,
            'user_id' => $this->adminUser->id,
            'tanggal' => now(),
            'shift_id' => $this->shift->id,
            'jam_kedatangan' => '08:00',
            'nama_rm' => $this->dataRm->nama_rm,
            'kode_produksi' => 'PRESERVE-001',
            'berat' => '50',
            'suhu' => 2.0,
            'sensori' => '✔',
            'kemasan' => '✔',
            'standar_berat' => '450',
            'status_rm' => 'diterima',
            'catatan_rm' => 'Catatan sampling penting',
            'jumlah_rm' => json_encode($initialJumlahRm),
            'nilai_jumlah_rm' => json_encode($initialNilaiSampel),
        ]);

        // QC only updates Detail Bahan Baku
        $payload = [
            'tanggal' => '2026-10-30',
            'shift_id' => $this->shift->id,
            'jam_kedatangan' => '10:30',
            'nama_rm' => $this->dataRm->nama_rm,
            'kode_produksi' => 'PRESERVE-001-MODIFIED',
            'berat' => '75',
            'suhu' => 3.0,
            'sensori' => '✔',
            'kemasan' => '✔',
            'keterangan' => 'QC updated notes',
            // Even if someone tries to submit modified sampling fields:
            'standar_berat' => '999',
            'status_rm' => 'diretur',
            'catatan_rm' => 'Hacked notes',
            'berat_samples' => [999],
        ];

        $response = $this->actingAs($this->qcUser)->put(route('chillroom.update', $item->uuid), $payload);

        $response->assertRedirect(route('chillroom.index'));

        $item->refresh();

        // Detail Bahan Baku should be updated:
        $this->assertEquals('PRESERVE-001-MODIFIED', $item->kode_produksi);
        $this->assertEquals('75', $item->berat);
        $this->assertEquals(3.0, (float) $item->suhu);
        $this->assertEquals('QC updated notes', $item->keterangan);

        // Sampling SPO fields must remain UNCHANGED:
        $this->assertEquals('450', $item->standar_berat);
        $this->assertEquals('diterima', $item->status_rm);
        $this->assertEquals('Catatan sampling penting', $item->catatan_rm);
        $this->assertEquals($initialJumlahRm, json_decode($item->jumlah_rm, true));
        $this->assertEquals($initialNilaiSampel, json_decode($item->nilai_jumlah_rm, true));
    }

    public function test_produksi_user_sees_sampling_spo_as_editable_on_create_page(): void
    {
        $response = $this->actingAs($this->produksiUser)->get(route('chillroom.create'));

        $response->assertStatus(200);
        $response->assertDontSee('sampling-spo-card-section sampling-spo-muted');
        $response->assertDontSee('Readonly (Role QC)');
        $response->assertSee('Tambah Sampling Berat (Frozen)');
    }

    public function test_produksi_user_can_input_both_detail_bahan_baku_and_sampling_spo_on_store(): void
    {
        $payload = [
            'tanggal' => '2026-10-30',
            'shift_id' => $this->shift->id,
            'jam_kedatangan' => '09:30',
            'entries' => [
                [
                    'nama_rm' => $this->dataRm->nama_rm,
                    'kode_produksi' => 'PROD-PROD-001',
                    'berat' => '120',
                    'suhu' => 3.2,
                    'sensori' => '✔',
                    'kemasan' => '✔',
                    'keterangan' => 'Produksi normal input',
                    'standar_berat' => '500',
                    'berat_atas' => 3,
                    'berat_std' => 15,
                    'berat_bawah' => 2,
                    'status_rm' => 'diterima',
                    'catatan_rm' => 'Sampling produksi lengkap',
                    'jumlah_rm_value' => [500, 502, 498],
                ],
            ],
        ];

        $response = $this->actingAs($this->produksiUser)->post(route('chillroom.store'), $payload);

        $response->assertRedirect(route('chillroom.index'));

        $created = PenerimaanChillroom::where('kode_produksi', 'PROD-PROD-001')->first();
        $this->assertNotNull($created);
        $this->assertEquals('120', $created->berat);
        $this->assertEquals('500', $created->standar_berat);
        $this->assertEquals('diterima', $created->status_rm);
        $this->assertEquals('Sampling produksi lengkap', $created->catatan_rm);

        $jumlahRm = json_decode($created->jumlah_rm, true);
        $this->assertEquals(3, $jumlahRm['entry_1']['berat_atas'] ?? null);
        $this->assertEquals(15, $jumlahRm['entry_1']['berat_std'] ?? null);
        $this->assertEquals(2, $jumlahRm['entry_1']['berat_bawah'] ?? null);

        $nilaiSampel = json_decode($created->nilai_jumlah_rm, true);
        $this->assertEquals([500, 502, 498], $nilaiSampel['entry_1'] ?? null);
    }

    public function test_produksi_user_sees_sampling_spo_as_editable_on_edit_page(): void
    {
        $item = PenerimaanChillroom::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $this->plan->id,
            'user_id' => $this->produksiUser->id,
            'tanggal' => now(),
            'shift_id' => $this->shift->id,
            'jam_kedatangan' => '08:00',
            'nama_rm' => $this->dataRm->nama_rm,
            'kode_produksi' => 'EDIT-PROD-001',
            'berat' => '50',
            'suhu' => 2.0,
            'sensori' => '✔',
            'kemasan' => '✔',
            'standar_berat' => '450',
            'status_rm' => 'diterima',
            'catatan_rm' => 'Awal',
            'jumlah_rm' => json_encode(['entry_1' => ['berat_atas' => 1, 'berat_std' => 5, 'berat_bawah' => 0]]),
            'nilai_jumlah_rm' => json_encode(['entry_1' => [450]]),
        ]);

        $response = $this->actingAs($this->produksiUser)->get(route('chillroom.edit', $item->uuid));

        $response->assertStatus(200);
        $response->assertDontSee('card-body sampling-spo-muted');
        $response->assertDontSee('Readonly (Role QC)');
    }

    public function test_produksi_user_can_update_sampling_spo_data(): void
    {
        $item = PenerimaanChillroom::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $this->plan->id,
            'user_id' => $this->produksiUser->id,
            'tanggal' => now(),
            'shift_id' => $this->shift->id,
            'jam_kedatangan' => '08:00',
            'nama_rm' => $this->dataRm->nama_rm,
            'kode_produksi' => 'UPDATE-PROD-001',
            'berat' => '50',
            'suhu' => 2.0,
            'sensori' => '✔',
            'kemasan' => '✔',
            'standar_berat' => '450',
            'status_rm' => 'diterima',
            'catatan_rm' => 'Sebelum update',
            'jumlah_rm' => json_encode(['entry_1' => ['berat_atas' => 1, 'berat_std' => 5, 'berat_bawah' => 0]]),
            'nilai_jumlah_rm' => json_encode(['entry_1' => [450]]),
        ]);

        $payload = [
            'tanggal' => '2026-10-30',
            'shift_id' => $this->shift->id,
            'jam_kedatangan' => '11:00',
            'nama_rm' => $this->dataRm->nama_rm,
            'kode_produksi' => 'UPDATE-PROD-001',
            'berat' => '80',
            'suhu' => 2.5,
            'sensori' => '✔',
            'kemasan' => '✔',
            'standar_berat' => '460',
            'berat_atas' => 5,
            'berat_std' => 25,
            'berat_bawah' => 2,
            'status_rm' => 'diterima',
            'catatan_rm' => 'Sesudah update oleh produksi',
            'berat_samples' => [460, 462],
        ];

        $response = $this->actingAs($this->produksiUser)->put(route('chillroom.update', $item->uuid), $payload);

        $response->assertRedirect(route('chillroom.index'));

        $item->refresh();
        $this->assertEquals('80', $item->berat);
        $this->assertEquals('460', $item->standar_berat);
        $this->assertEquals('Sesudah update oleh produksi', $item->catatan_rm);

        $jumlahRm = json_decode($item->jumlah_rm, true);
        $this->assertEquals(5, $jumlahRm['entry_1']['berat_atas'] ?? null);
        $this->assertEquals(25, $jumlahRm['entry_1']['berat_std'] ?? null);
        $this->assertEquals(2, $jumlahRm['entry_1']['berat_bawah'] ?? null);
    }

    public function test_produksi_user_sees_original_incoming_inspection_and_meat_prep_menus_in_navbar(): void
    {
        $response = $this->actingAs($this->produksiUser)->get(route('chillroom.index'));

        $response->assertStatus(200);
        $response->assertSee('[QF-01/00] Chillroom');
        $response->assertSee('[QF-01/00] Seasoning');
        $response->assertSee('[QF-02/00] Shoestring');
        $response->assertSee('[QF-03/00] Kemasan');
        $response->assertSee('[QF-34/00] Rebox');
        $response->assertSee('Meat Preparation');
        $response->assertDontSee('Merged Receiving Inspection');
    }

    public function test_show_page_does_not_render_production_verification_block(): void
    {
        $item = $this->createChillroomRecord();

        $response = $this->actingAs($this->adminUser)->get(route('chillroom.show', $item->uuid));

        $response->assertStatus(200);
        $response->assertDontSee('<small class="d-block text-muted">Produksi</small>', false);
        $response->assertSee('<small class="d-block text-muted">QC</small>', false);
        $response->assertSee('<small class="d-block text-muted">SPV</small>', false);
    }

    public function test_index_page_does_not_render_approve_produksi_button(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('chillroom.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Approve Produksi');
    }

    public function test_single_approve_rejects_production_type(): void
    {
        $item = $this->createChillroomRecord();

        $response = $this->actingAs($this->adminUser)->postJson(route('chillroom.approve', $item->uuid), [
            'type' => 'produksi',
        ]);

        $response->assertStatus(422);
    }

    public function test_bulk_approve_rejects_production_type(): void
    {
        $response = $this->actingAs($this->adminUser)->postJson(route('chillroom.bulk-approve'), [
            'type' => 'produksi',
            'tanggal' => now()->format('Y-m-d'),
            'shift_id' => $this->shift->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_bulk_approve_selected_rejects_production_type(): void
    {
        $response = $this->actingAs($this->adminUser)->postJson(route('chillroom.bulk-approve-selected'), [
            'type' => 'produksi',
            'uuids' => [(string) Str::uuid()],
        ]);

        $response->assertStatus(422);
    }

    public function test_production_role_does_not_see_approval_checkboxes(): void
    {
        $this->createChillroomRecord();

        $response = $this->actingAs($this->produksiUser)->get(route('chillroom.index'));

        $response->assertStatus(200);
        $response->assertDontSee('<input type="checkbox" id="selectAllCheckbox"', false);
        $response->assertDontSee('<input type="checkbox" class="form-check-input row-checkbox bulk-approval-checkbox"', false);
    }

    public function test_admin_role_still_sees_approval_checkboxes(): void
    {
        $this->createChillroomRecord();

        $response = $this->actingAs($this->adminUser)->get(route('chillroom.index'));

        $response->assertStatus(200);
        $response->assertSee('<input type="checkbox" id="selectAllCheckbox"', false);
        $response->assertSee('<input type="checkbox" class="form-check-input row-checkbox bulk-approval-checkbox"', false);
    }

    public function test_qc_role_can_store_verification_and_view_the_log(): void
    {
        $item = $this->createChillroomRecord();

        $response = $this->actingAs($this->qcUser)->post(route('chillroom.qc-verifications.store', $item->uuid), [
            'status' => 'OK',
            'note' => 'Data sudah sesuai standar.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('chillroom_qc_verifications', [
            'penerimaan_chillroom_id' => $item->id,
            'status' => 'OK',
            'note' => 'Data sudah sesuai standar.',
            'verified_by' => $this->qcUser->id,
        ]);
        $this->assertDatabaseHas('chillroom_logs', [
            'penerimaan_chillroom_id' => $item->id,
            'aksi' => 'qc_verification',
        ]);

        $this->actingAs($this->qcUser)
            ->get(route('chillroom.qc-verifications.index', $item->uuid))
            ->assertStatus(200)
            ->assertSee('Oke')
            ->assertSee('Data sudah sesuai standar.');
    }

    public function test_non_qc_role_cannot_store_qc_verification(): void
    {
        $item = $this->createChillroomRecord();

        $this->actingAs($this->produksiUser)
            ->post(route('chillroom.qc-verifications.store', $item->uuid), [
                'status' => 'NOT_OK',
                'note' => 'Tidak boleh.',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('chillroom_qc_verifications', [
            'penerimaan_chillroom_id' => $item->id,
        ]);
    }

    public function test_qc_verification_form_is_only_rendered_for_qc_role(): void
    {
        $item = $this->createChillroomRecord();

        $this->actingAs($this->qcUser)
            ->get(route('chillroom.show', $item->uuid))
            ->assertStatus(200)
            ->assertSee('id="qcStatusOk"', false)
            ->assertSee('Simpan Verifikasi QC');

        $this->actingAs($this->produksiUser)
            ->get(route('chillroom.show', $item->uuid))
            ->assertStatus(200)
            ->assertDontSee('id="qcStatusOk"', false)
            ->assertSee('Hanya role QC yang dapat mengubah verifikasi.');
    }

    public function test_qc_verification_log_link_is_available_beside_view_action(): void
    {
        $item = $this->createChillroomRecord();

        $this->actingAs($this->qcUser)
            ->get(route('chillroom.index'))
            ->assertStatus(200)
            ->assertSee(route('chillroom.qc-verifications.index', $item->uuid), false);
    }

    private function createChillroomRecord(): PenerimaanChillroom
    {
        return PenerimaanChillroom::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $this->plan->id,
            'user_id' => $this->adminUser->id,
            'tanggal' => now(),
            'shift_id' => $this->shift->id,
            'jam_kedatangan' => '08:00',
            'nama_rm' => $this->dataRm->nama_rm,
            'kode_produksi' => 'VERIF-REMOVE-001',
            'berat' => '50',
            'suhu' => 2.0,
            'sensori' => '✔',
            'kemasan' => '✔',
            'standar_berat' => '450',
            'status_rm' => 'diterima',
            'catatan_rm' => 'Catatan',
        ]);
    }
}
