<?php

namespace Tests\Feature;

use App\Models\DataShift;
use App\Models\JenisBetter;
use App\Models\JenisBreader;
use App\Models\JenisPredust;
use App\Models\JenisProduk;
use App\Models\PembekuanIqfPenggorengan;
use App\Models\PembuatanPredust;
use App\Models\Penggorengan;
use App\Models\Plan;
use App\Models\ProsesBattering;
use App\Models\ProsesBreader;
use App\Models\StdSuhuPusat;
use App\Models\SuhuFrayer1;
use App\Models\User;
use App\Models\WaktuPenggorengan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class WizardProses1RoleWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected function createTestMasterData(): array
    {
        $plan = Plan::first() ?: Plan::create([
            'uuid' => (string) Str::uuid(),
            'nama_plan' => 'Plan Test',
        ]);

        $produksiUser = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Operator Produksi Test',
            'username' => 'prod_test_' . Str::random(6),
            'email' => 'prod_' . Str::random(6) . '@test.local',
            'password' => bcrypt('password'),
            'role' => 'produksi',
            'id_role' => 2,
            'id_plan' => $plan->id,
        ]);

        $qcUser = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'QC Inspector Test',
            'username' => 'qc_test_' . Str::random(6),
            'email' => 'qc_' . Str::random(6) . '@test.local',
            'password' => bcrypt('password'),
            'role' => 'qc',
            'id_role' => 3,
            'id_plan' => $plan->id,
        ]);

        $spvUser = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'SPV QC Test',
            'username' => 'spv_test_' . Str::random(6),
            'email' => 'spv_' . Str::random(6) . '@test.local',
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
            'nama_produk' => 'Nugget Test',
        ]);

        $jenisPredust = JenisPredust::where('id_plan', $plan->id)->first() ?: JenisPredust::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $plan->id,
            'user_id' => $produksiUser->id,
            'id_produk' => $produk->id,
            'jenis_predust' => 'Predust Test',
        ]);

        $jenisBetter = JenisBetter::where('id_plan', $plan->id)->first() ?: JenisBetter::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $plan->id,
            'user_id' => $produksiUser->id,
            'id_produk' => $produk->id,
            'nama_better' => 'Better Test',
            'berat' => 10,
        ]);

        $jenisBreader = JenisBreader::where('id_plan', $plan->id)->first() ?: JenisBreader::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $plan->id,
            'user_id' => $produksiUser->id,
            'id_produk' => $produk->id,
            'jenis_breader' => 'Breader Test',
        ]);

        $suhuFrayer1 = SuhuFrayer1::where('id_produk', $produk->id)->first() ?: SuhuFrayer1::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $plan->id,
            'user_id' => $produksiUser->id,
            'id_produk' => $produk->id,
            'suhu_frayer' => '180',
            'suhu_frayer_3' => '180',
            'suhu_frayer_4' => '180',
            'suhu_frayer_5' => '180',
        ]);

        $waktu1 = WaktuPenggorengan::where('id_suhu_frayer_1', $suhuFrayer1->id)->first() ?: WaktuPenggorengan::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $plan->id,
            'user_id' => $produksiUser->id,
            'id_produk' => $produk->id,
            'id_suhu_frayer_1' => $suhuFrayer1->id,
            'waktu_penggorengan' => '45',
        ]);

        $stdSuhuPusat = StdSuhuPusat::where('id_produk', $produk->id)->first() ?: StdSuhuPusat::create([
            'id_plan' => $plan->id,
            'user_id' => $produksiUser->id,
            'id_produk' => $produk->id,
            'std_suhu_pusat' => ['75', '75', '75', '75', '75'],
        ]);

        return compact(
            'plan',
            'shift',
            'produk',
            'jenisPredust',
            'jenisBetter',
            'jenisBreader',
            'suhuFrayer1',
            'waktu1',
            'stdSuhuPusat',
            'produksiUser',
            'qcUser',
            'spvUser'
        );
    }

    public function test_full_proses1_produksi_then_qc_then_approval_workflow(): void
    {
        $data = $this->createTestMasterData();

        // 1. QC Inspector TIDAK BOLEH mengakses halaman create atau menyimpan step produksi
        $this->actingAs($data['qcUser'])
            ->get(route('wizard.proses1.create'))
            ->assertStatus(403);

        $this->actingAs($data['qcUser'])
            ->postJson(route('wizard.proses1.store-penggorengan'), [
                'id_produk' => $data['produk']->id,
                'shift_id' => $data['shift']->id,
                'tanggal' => '2026-11-02 08:00:00',
                'jam' => '08:00',
                'kode_produksi' => 'KP-001',
                'no_of_strokes' => '40',
            ])
            ->assertStatus(403);

        // 2. Role Produksi membuat Step 1–4 tanpa field QC (hasil_pencetakan, kondisi_predust, hasil_better, hasil_breader = null)
        $resStep1 = $this->actingAs($data['produksiUser'])
            ->postJson(route('wizard.proses1.store-penggorengan'), [
                'id_produk' => $data['produk']->id,
                'shift_id' => $data['shift']->id,
                'tanggal' => '2026-11-02 08:00:00',
                'jam' => '08:00',
                'kode_produksi' => 'KP-PROD-01',
                'berat_produk' => '250',
                'no_of_strokes' => '45',
                'waktu_pemasakan' => '120',
                'hasil_pencetakan' => '✔', // Harus diabaikan untuk role produksi
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $penggorenganUuid = $resStep1->json('uuid');
        $penggorengan = Penggorengan::where('uuid', $penggorenganUuid)->firstOrFail();
        $this->assertNull($penggorengan->hasil_pencetakan);

        $resStep2 = $this->actingAs($data['produksiUser'])
            ->postJson(route('wizard.proses1.store-predust'), [
                'penggorengan_uuid' => $penggorenganUuid,
                'id_produk' => $data['produk']->id,
                'tanggal' => '2026-11-02 08:00:00',
                'jam' => '08:05',
                'id_jenis_predust' => $data['jenisPredust']->id,
                'kode_produksi' => 'PRD-01',
                'hasil_predust' => '✔', // Harus diabaikan untuk role produksi
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $predustUuid = $resStep2->json('uuid');
        $predust = PembuatanPredust::where('uuid', $predustUuid)->firstOrFail();
        $this->assertNull($predust->kondisi_predust);

        $resStep3 = $this->actingAs($data['produksiUser'])
            ->postJson(route('wizard.proses1.store-battering'), [
                'penggorengan_uuid' => $penggorenganUuid,
                'predust_uuid' => $predustUuid,
                'id_produk' => $data['produk']->id,
                'tanggal' => '2026-11-02 08:00:00',
                'jam' => '08:10',
                'id_jenis_better' => [$data['jenisBetter']->id],
                'kode_produksi_better' => [$data['jenisBetter']->id => 'BTR-01'],
                'hasil_better' => '✔', // Harus diabaikan untuk role produksi
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $batteringUuid = $resStep3->json('uuid');
        $battering = ProsesBattering::where('uuid', $batteringUuid)->firstOrFail();
        $this->assertNull($battering->hasil_better);

        $resStep4 = $this->actingAs($data['produksiUser'])
            ->postJson(route('wizard.proses1.store-breader'), [
                'penggorengan_uuid' => $penggorenganUuid,
                'predust_uuid' => $predustUuid,
                'battering_uuid' => $batteringUuid,
                'id_produk' => $data['produk']->id,
                'tanggal' => '2026-11-02 08:00:00',
                'jam' => '08:15',
                'id_jenis_breader' => [$data['jenisBreader']->id],
                'kode_produksi_breader' => [$data['jenisBreader']->id => 'BRD-01'],
                'hasil_breader' => '✔', // Harus diabaikan untuk role produksi
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $breaderUuid = $resStep4->json('uuid');
        $pembekuanUuid = $resStep4->json('pembekuan_uuid');
        $this->assertNotEmpty($pembekuanUuid);

        $breader = ProsesBreader::where('uuid', $breaderUuid)->firstOrFail();
        $this->assertNull($breader->hasil_breader);

        // Pastikan record PembekuanIqfPenggorengan langsung terbentuk di akhir Step 4 tanpa perlu Step 7 IQF
        $pembekuan = PembekuanIqfPenggorengan::where('uuid', $pembekuanUuid)->firstOrFail();
        $this->assertEquals($penggorenganUuid, $pembekuan->penggorengan_uuid);

        // 3. Role Produksi TIDAK BOLEH memanggil endpoint qc-verification atau store-frayer
        $this->actingAs($data['produksiUser'])
            ->put(route('wizard.proses1.qc-verification', $pembekuanUuid), [
                'hasil_pencetakan' => '✔',
                'hasil_better' => '✔',
                'hasil_breader' => '✔',
            ])
            ->assertStatus(403);

        // 4. Role QC Inspector membuka halaman show (ikon mata) dan menginput verifikasi QC (Pencetakan, Predust, Battering, Breading, Frying, Hasil Penggorengan)
        $this->actingAs($data['qcUser'])
            ->get(route('wizard.proses1.show', $pembekuanUuid))
            ->assertOk()
            ->assertSee('Form Input / Update Verifikasi QC Inspector');

        $this->actingAs($data['qcUser'])
            ->put(route('wizard.proses1.qc-verification', $pembekuanUuid), [
                'hasil_pencetakan' => '✔',
                'kondisi_predust' => '✔',
                'hasil_better' => '✔',
                'hasil_breader' => '✘',
                'selected_frayers' => [1],
                'frayer_1_suhu' => $data['suhuFrayer1']->id,
                'frayer_1_waktu' => $data['waktu1']->id,
                'frayer_1_aktual_suhu' => '181',
                'frayer_1_aktual_waktu' => '45',
                'frayer_1_tpm' => '12',
                'frayer_1_ffa' => '0.25',
                'frayer_1_keterangan' => 'Minyak bagus',
                'id_std_suhu_pusat' => $data['stdSuhuPusat']->id,
                'aktual_suhu_pusat' => ['78.5', '79.0'],
                'sensori_kematangan' => '✔',
                'sensori_kenampakan' => '✔',
                'sensori_warna' => '✔',
                'sensori_rasa' => '✔',
                'sensori_bau' => '✔',
                'sensori_tekstur' => '✔',
                'keterangan_hasil' => 'Sesuai standar QC',
            ])
            ->assertRedirect(route('wizard.proses1.show', $pembekuanUuid));

        $pembekuan->refresh();
        $this->assertEquals('✔', $pembekuan->penggorenganData->hasil_pencetakan);
        $this->assertEquals('✔', $pembekuan->predustData->kondisi_predust);
        $this->assertEquals('✔', $pembekuan->batteringData->hasil_better);
        $this->assertEquals('✘', $pembekuan->breaderData->hasil_breader);
        $this->assertNotNull($pembekuan->frayer_uuid);
        $this->assertNotNull($pembekuan->hasil_penggorengan_uuid);
        $this->assertEquals('181', $pembekuan->frayerData->aktual_suhu_penggorengan);

        // 5. Approval Workflow: QC Inspector -> SPV QC (Tanpa Approval Produksi)
        // SPV QC belum bisa approve sebelum QC Inspector approve
        $this->actingAs($data['spvUser'])
            ->postJson(route('wizard.proses1.approve', $pembekuanUuid), ['type' => 'spv'])
            ->assertStatus(403);

        // Approval tipe 'produksi' ditolak (400) karena sudah dihapus dari alur
        $this->actingAs($data['produksiUser'])
            ->postJson(route('wizard.proses1.approve', $pembekuanUuid), ['type' => 'produksi'])
            ->assertStatus(400);

        // QC Inspector melakukan approval pertama
        $this->actingAs($data['qcUser'])
            ->postJson(route('wizard.proses1.approve', $pembekuanUuid), ['type' => 'qc'])
            ->assertOk()
            ->assertJsonPath('success', true);

        $pembekuan->refresh();
        $this->assertTrue((bool) $pembekuan->approved_by_qc);

        // SPV QC langsung dapat melakukan approval setelah QC Inspector tanpa perlu approval Produksi
        $this->actingAs($data['spvUser'])
            ->postJson(route('wizard.proses1.approve', $pembekuanUuid), ['type' => 'spv'])
            ->assertOk()
            ->assertJsonPath('success', true);

        $pembekuan->refresh();
        $this->assertTrue((bool) $pembekuan->approved_by_spv);
    }
}
