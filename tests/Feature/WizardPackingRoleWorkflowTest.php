<?php

namespace Tests\Feature;

use App\Models\BeratProdukBag;
use App\Models\BeratProdukBox;
use App\Models\DataBag;
use App\Models\DataBox;
use App\Models\DataShift;
use App\Models\Dokumentasi;
use App\Models\JenisProduk;
use App\Models\PengemasanKarton;
use App\Models\PengemasanPlastik;
use App\Models\PengemasanProduk;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class WizardPackingRoleWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected function createTestMasterData(): array
    {
        $plan = Plan::first() ?: Plan::create([
            'uuid' => (string) Str::uuid(),
            'nama_plan' => 'Plan Test Packing',
        ]);

        $produksiUser = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Operator Produksi Packing',
            'username' => 'prod_pack_' . Str::random(6),
            'email' => 'prod_pack_' . Str::random(6) . '@test.local',
            'password' => bcrypt('password'),
            'role' => 'produksi',
            'id_role' => 2,
            'id_plan' => $plan->id,
        ]);

        $qcUser = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'QC Inspector Packing',
            'username' => 'qc_pack_' . Str::random(6),
            'email' => 'qc_pack_' . Str::random(6) . '@test.local',
            'password' => bcrypt('password'),
            'role' => 'qc',
            'id_role' => 3,
            'id_plan' => $plan->id,
        ]);

        $spvUser = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'SPV Packing Test',
            'username' => 'spv_pack_' . Str::random(6),
            'email' => 'spv_pack_' . Str::random(6) . '@test.local',
            'password' => bcrypt('password'),
            'role' => 'spv',
            'id_role' => 4,
            'id_plan' => $plan->id,
        ]);

        $shift = DataShift::where('id_plan', $plan->id)->first() ?: DataShift::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $plan->id,
            'user_id' => $produksiUser->id,
            'shift' => '1',
        ]);

        $produk = JenisProduk::where('id_plan', $plan->id)->first() ?: JenisProduk::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $plan->id,
            'user_id' => $produksiUser->id,
            'nama_produk' => 'Nugget Packing Test',
        ]);

        $dataBag = DataBag::where('id_plan', $plan->id)->first() ?: DataBag::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $plan->id,
            'user_id' => $produksiUser->id,
            'id_produk' => $produk->id,
            'std_bag' => '500g',
            'berat' => 500,
        ]);

        $dataBox = DataBox::where('id_plan', $plan->id)->first() ?: DataBox::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $plan->id,
            'user_id' => $produksiUser->id,
            'id_produk' => $produk->id,
            'std_box' => '10kg',
            'berat' => 500,
            'std_isi_pack_karton' => '20',
        ]);

        return compact('plan', 'produksiUser', 'qcUser', 'spvUser', 'shift', 'produk', 'dataBag', 'dataBox');
    }

    public function test_qc_cannot_access_create_and_cannot_store_step_1(): void
    {
        $data = $this->createTestMasterData();

        // QC on index does NOT see create button
        $indexQc = $this->actingAs($data['qcUser'])->get(route('wizards.packing.index'));
        $indexQc->assertStatus(200);
        $indexQc->assertDontSee(route('wizards.packing.create'));

        // Produksi on index DOES see create button
        $indexProd = $this->actingAs($data['produksiUser'])->get(route('wizards.packing.index'));
        $indexProd->assertStatus(200);
        $indexProd->assertSee(route('wizards.packing.create'));

        // QC redirected away from create page
        $response = $this->actingAs($data['qcUser'])->get(route('wizards.packing.create'));
        $response->assertRedirect(route('wizards.packing.index'));
        $response->assertSessionHas('error');

        // QC receives 403 when trying to submit storePengemasanProduk
        $storeResponse = $this->actingAs($data['qcUser'])->postJson(route('wizards.packing.store-pengemasan-produk'), [
            'id_produk' => $data['produk']->id,
            'id_shift' => $data['shift']->id,
            'tanggal' => now()->format('Y-m-d'),
            'jam' => '08:00',
            'kode_produksi' => 'KP-TEST-QC',
        ]);
        $storeResponse->assertStatus(403);
    }

    public function test_produksi_can_fill_produksi_steps_and_auto_generate_root_record(): void
    {
        $data = $this->createTestMasterData();

        // 1. Step 1 + 2: storePengemasanProduk by Produksi
        $res1 = $this->actingAs($data['produksiUser'])->postJson(route('wizards.packing.store-pengemasan-produk'), [
            'id_produk' => $data['produk']->id,
            'id_shift' => $data['shift']->id,
            'tanggal' => now()->format('Y-m-d'),
            'jam' => '08:00',
            'berat_produk' => '500',
            'std_suhu_produk_iqf' => '-18°C',
            'tanggal_expired' => now()->addMonths(6)->format('Y-m-d'),
            'kode_produksi' => 'KP-PROD-001',
            'waktu_awal_packing' => '08:15',
            'aktual_suhu_produk' => ['-19.5'], // Should be nulled out by controller for Produksi
        ]);
        $res1->assertStatus(200)->assertJson(['success' => true]);
        $idPengemasanProduk = $res1->json('id');

        $pengemasanProduk = PengemasanProduk::find($idPengemasanProduk);
        $this->assertNotNull($pengemasanProduk);
        $this->assertNull($pengemasanProduk->aktual_suhu_produk, 'Produksi must not fill aktual_suhu_produk');
        $this->assertEquals('08:15', $pengemasanProduk->waktu_awal_packing);

        // 2. Step 3: storePengemasanPlastik by Produksi
        $res2 = $this->actingAs($data['produksiUser'])->postJson(route('wizards.packing.store-pengemasan-plastik'), [
            'id_pengemasan_produk' => $idPengemasanProduk,
            'tanggal' => now()->format('Y-m-d'),
            'jam' => '08:20',
            'line' => ['1', '2'],
            'waktu_pengemasan' => '08:20 - 10:00',
            'proses_penimbangan' => 'mhw',
            'proses_sealing' => 'bag-sealer',
            'kode_kemasan_plastik' => 'KEM-01',
            'identitas_produk' => '✔', // QC field, should be nulled out
            'kesesuaian_label_plastik' => 'OK', // QC field, should be nulled out
            'kekuatan_seal' => '✔', // QC field, should be nulled out
        ]);
        $res2->assertStatus(200)->assertJson(['success' => true]);
        $idPlastik = $res2->json('id');

        $plastik = PengemasanPlastik::find($idPlastik);
        $this->assertNotNull($plastik);
        $this->assertEquals('mhw', $plastik->proses_penimbangan);
        $this->assertNull($plastik->identitas_produk, 'Produksi cannot set identitas_produk');
        $this->assertNull($plastik->kesesuaian_label_plastik, 'Produksi cannot set kesesuaian_label_plastik');

        // 3. Step 6: storePengemasanKarton by Produksi
        $res3 = $this->actingAs($data['produksiUser'])->postJson(route('wizards.packing.store-pengemasan-karton'), [
            'id_pengemasan_produk' => $idPengemasanProduk,
            'id_pengemasan_plastik' => $idPlastik,
            'tanggal' => now()->format('Y-m-d'),
            'jam' => '08:30',
            'aktual_jumlah_karton' => '20 pack',
            'identitas_produk_pada_karton' => '✔', // QC field, nulled
            'kesesuaian_isi_pack' => ['✔'], // QC field, nulled
        ]);
        $res3->assertStatus(200)->assertJson(['success' => true]);
        $idKarton = $res3->json('id');

        $karton = PengemasanKarton::find($idKarton);
        $this->assertEquals('20 pack', $karton->aktual_jumlah_karton);
        $this->assertNull($karton->identitas_produk_pada_karton, 'Produksi cannot set identitas_produk_pada_karton');

        // 4. Finalize Produksi via storeDokumentasi
        $resFinal = $this->actingAs($data['produksiUser'])->postJson(route('wizards.packing.store-dokumentasi'), [
            'id_pengemasan_produk' => $idPengemasanProduk,
            'id_pengemasan_plastik' => $idPlastik,
            'id_pengemasan_karton' => $idKarton,
            'tanggal' => now()->format('Y-m-d'),
            'jam' => '08:35',
        ]);
        $resFinal->assertStatus(200)->assertJson(['success' => true]);
        $uuid = $resFinal->json('uuid');

        // Verify Dokumentasi root record is generated
        $dokumentasi = Dokumentasi::where('uuid', $uuid)->first();
        $this->assertNotNull($dokumentasi);
        $this->assertNotNull($dokumentasi->id_berat_produk_bag);
        $this->assertNotNull($dokumentasi->id_berat_produk_box);
        $this->assertNull($dokumentasi->foto_kode_produksi, 'Foto must be null before QC verification');

        // 5. QC verifies and updates data via show page (Ikon Mata - qc-verification route)
        // Produksi is forbidden from submitting qc-verification
        $prodForbidden = $this->actingAs($data['produksiUser'])->put(route('wizards.packing.qc-verification', $uuid), [
            'status_release_produk' => 'ya',
        ]);
        $prodForbidden->assertStatus(403);

        $updateResponse = $this->actingAs($data['qcUser'])->put(route('wizards.packing.qc-verification', $uuid), [
            'aktual_suhu_produk' => ['-18.4', '-18.7'],
            'identitas_produk' => '✔',
            'kesesuaian_label_plastik' => 'OK',
            'kekuatan_seal' => '✔',
            'step3' => [
                $dokumentasi->id_berat_produk_bag => [
                    'id_data_bag' => $data['dataBag']->id,
                    'berat_aktual' => [501, 502, 500],
                    'rata_rata_berat' => 501.0,
                ],
            ],
            'step4' => [
                'id_data_box' => $data['dataBox']->id,
                'berat_aktual' => [10.05, 10.02],
                'rata_rata_berat' => 10.03,
            ],
            'identitas_produk_pada_karton' => '✔',
            'standar_jumlah_karton' => 20,
            'kesesuaian_isi_pack' => ['✔'],
            'foto_kode_produksi_sekunder_status' => 'OK',
            'status_release_produk' => 'ya',
            'tindakan_koreksi' => 'Tidak ada',
            'keterangan' => 'Sesuai standar mutu QC',
        ]);
        $updateResponse->assertRedirect(route('wizards.packing.show', $uuid));

        // Refresh and check values updated by QC
        $pengemasanProduk->refresh();
        $this->assertEquals('-18.4, -18.7', $pengemasanProduk->aktual_suhu_produk);
        $this->assertEquals('ya', $pengemasanProduk->status_release_produk);
        $this->assertEquals('Tidak ada', $pengemasanProduk->tindakan_koreksi);

        $plastik->refresh();
        $this->assertEquals('✔', $plastik->identitas_produk);
        $this->assertEquals('OK', $plastik->kesesuaian_label_plastik);
        $this->assertEquals('✔', $plastik->kekuatan_seal);

        $karton->refresh();
        $this->assertEquals('✔', $karton->identitas_produk_pada_karton);

        $dokumentasi->refresh();
        $this->assertEquals('Sesuai standar mutu QC', $dokumentasi->keterangan);
        $this->assertEquals('OK', $dokumentasi->foto_kode_produksi_sekunder);

        // 6. Test approval sequential: Produksi cannot approve before QC
        $prodApprove = $this->actingAs($data['produksiUser'])->postJson(route('wizards.packing.approve', $uuid), [
            'type' => 'produksi',
        ]);
        $prodApprove->assertStatus(400);

        // QC approves
        $qcApprove = $this->actingAs($data['qcUser'])->postJson(route('wizards.packing.approve', $uuid), [
            'type' => 'qc',
        ]);
        $qcApprove->assertStatus(200)->assertJson(['success' => true]);

        // Produksi approves
        $prodApprove2 = $this->actingAs($data['produksiUser'])->postJson(route('wizards.packing.approve', $uuid), [
            'type' => 'produksi',
        ]);
        $prodApprove2->assertStatus(200)->assertJson(['success' => true]);

        // SPV approves
        $spvApprove = $this->actingAs($data['spvUser'])->postJson(route('wizards.packing.approve', $uuid), [
            'type' => 'spv',
        ]);
        $spvApprove->assertStatus(200)->assertJson(['success' => true]);

        // Role cannot approve other type (e.g. Produksi cannot approve spv)
        $invalidApprove = $this->actingAs($data['produksiUser'])->postJson(route('wizards.packing.approve', $uuid), [
            'type' => 'spv',
        ]);
        $invalidApprove->assertStatus(403);
    }

    public function test_tamper_protection_on_update(): void
    {
        $data = $this->createTestMasterData();

        // Produksi creates data
        $res1 = $this->actingAs($data['produksiUser'])->postJson(route('wizards.packing.store-pengemasan-produk'), [
            'id_produk' => $data['produk']->id,
            'id_shift' => $data['shift']->id,
            'tanggal' => now()->format('Y-m-d'),
            'jam' => '08:00',
            'berat_produk' => '500',
            'std_suhu_produk_iqf' => '-18°C',
            'tanggal_expired' => now()->addMonths(6)->format('Y-m-d'),
            'kode_produksi' => 'KP-PROD-ORIGINAL',
            'waktu_awal_packing' => '08:15',
        ]);
        $idPengemasanProduk = $res1->json('id');

        $res2 = $this->actingAs($data['produksiUser'])->postJson(route('wizards.packing.store-pengemasan-plastik'), [
            'id_pengemasan_produk' => $idPengemasanProduk,
            'tanggal' => now()->format('Y-m-d'),
            'jam' => '08:20',
            'line' => ['1'],
            'waktu_pengemasan' => '08:20 - 10:00',
            'proses_penimbangan' => 'mhw',
            'proses_sealing' => 'bag-sealer',
            'kode_kemasan_plastik' => 'KEM-ORIGINAL',
        ]);
        $idPlastik = $res2->json('id');

        $res3 = $this->actingAs($data['produksiUser'])->postJson(route('wizards.packing.store-pengemasan-karton'), [
            'id_pengemasan_produk' => $idPengemasanProduk,
            'id_pengemasan_plastik' => $idPlastik,
            'tanggal' => now()->format('Y-m-d'),
            'jam' => '08:30',
            'aktual_jumlah_karton' => '20 pack',
        ]);
        $idKarton = $res3->json('id');

        $resFinal = $this->actingAs($data['produksiUser'])->postJson(route('wizards.packing.store-dokumentasi'), [
            'id_pengemasan_produk' => $idPengemasanProduk,
            'id_pengemasan_plastik' => $idPlastik,
            'id_pengemasan_karton' => $idKarton,
            'tanggal' => now()->format('Y-m-d'),
            'jam' => '08:35',
        ]);
        $uuid = $resFinal->json('uuid');

        $pengemasanProduk = PengemasanProduk::find($idPengemasanProduk);
        $plastik = PengemasanPlastik::find($idPlastik);
        $karton = PengemasanKarton::find($idKarton);

        // First, QC sets verification fields
        $this->actingAs($data['qcUser'])->put(route('wizards.packing.update', $uuid), [
            'step1' => ['aktual_suhu_produk' => ['-18.5']],
            'step2' => ['identitas_produk' => '✔', 'kesesuaian_label_plastik' => 'OK', 'kekuatan_seal' => '✔'],
            'step5' => ['identitas_produk_pada_karton' => '✔', 'standar_jumlah_karton' => 20, 'kesesuaian_isi_pack' => ['✔']],
            'step6' => ['status_release_produk' => 'ya', 'tindakan_koreksi' => 'QC OK', 'keterangan' => 'QC verified'],
        ]);

        $pengemasanProduk->refresh();
        $this->assertEquals('-18.5', $pengemasanProduk->aktual_suhu_produk);
        $this->assertEquals('ya', $pengemasanProduk->status_release_produk);

        // Now Produksi tries to update, attempting to tamper with QC fields
        $this->actingAs($data['produksiUser'])->put(route('wizards.packing.update', $uuid), [
            'step1' => [
                'kode_produksi' => 'KP-PROD-UPDATED', // Produksi field: allowed to change
                'aktual_suhu_produk' => ['-25.0'], // QC field: must NOT change
            ],
            'step2' => [
                'waktu_pengemasan' => '08:20 - 11:00', // Produksi field: allowed to change
                'kesesuaian_label_plastik' => 'Not OK', // QC field: must NOT change
            ],
            'step6' => [
                'status_release_produk' => 'tidak', // QC field: must NOT change
                'tindakan_koreksi' => 'Tampered',   // QC field: must NOT change
            ],
        ]);

        $pengemasanProduk->refresh();
        $this->assertEquals('KP-PROD-UPDATED', $pengemasanProduk->kode_produksi, 'Produksi can update its own kode_produksi');
        $this->assertEquals('-18.5', $pengemasanProduk->aktual_suhu_produk, 'Produksi cannot tamper with aktual_suhu_produk');
        $this->assertEquals('ya', $pengemasanProduk->status_release_produk, 'Produksi cannot tamper with status_release_produk');

        $plastik->refresh();
        $this->assertEquals('08:20 - 11:00', $plastik->waktu_pengemasan, 'Produksi can update its own waktu_pengemasan');
        $this->assertEquals('OK', $plastik->kesesuaian_label_plastik, 'Produksi cannot tamper with kesesuaian_label_plastik');

        // Now QC tries to update, attempting to tamper with Produksi fields
        $this->actingAs($data['qcUser'])->put(route('wizards.packing.update', $uuid), [
            'step1' => [
                'kode_produksi' => 'KP-QC-TAMPERED', // Produksi field: must NOT change
                'aktual_suhu_produk' => ['-19.0'],   // QC field: allowed to change
            ],
            'step2' => [
                'waktu_pengemasan' => 'Tampered Time', // Produksi field: must NOT change
                'kekuatan_seal' => '✔',                // QC field: allowed to change
            ],
        ]);

        $pengemasanProduk->refresh();
        $this->assertEquals('KP-PROD-UPDATED', $pengemasanProduk->kode_produksi, 'QC cannot tamper with Produksi kode_produksi');
        $this->assertEquals('-19.0', $pengemasanProduk->aktual_suhu_produk, 'QC can update its own aktual_suhu_produk');

        $plastik->refresh();
        $this->assertEquals('08:20 - 11:00', $plastik->waktu_pengemasan, 'QC cannot tamper with Produksi waktu_pengemasan');
    }
}
