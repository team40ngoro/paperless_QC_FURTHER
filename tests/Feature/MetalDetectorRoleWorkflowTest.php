<?php

namespace Tests\Feature;

use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\InputMetalDetector;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class MetalDetectorRoleWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected function createTestData(): array
    {
        $plan = Plan::first() ?: Plan::create([
            'uuid' => (string) Str::uuid(),
            'nama_plan' => 'Plan MD Test',
        ]);

        $produksiUser = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Operator Produksi MD Test',
            'username' => 'prod_md_' . Str::random(6),
            'email' => 'prod_md_' . Str::random(6) . '@test.local',
            'password' => bcrypt('password'),
            'role' => 'produksi',
            'id_role' => 2,
            'id_plan' => $plan->id,
        ]);

        $qcUser = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'QC Inspector MD Test',
            'username' => 'qc_md_' . Str::random(6),
            'email' => 'qc_md_' . Str::random(6) . '@test.local',
            'password' => bcrypt('password'),
            'role' => 'qc',
            'id_role' => 3,
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
            'nama_produk' => 'Nugget MD Test ' . Str::random(4),
        ]);

        return compact('plan', 'produksiUser', 'qcUser', 'shift', 'produk');
    }

    public function test_qc_inspector_cannot_access_create_and_store(): void
    {
        $data = $this->createTestData();

        // 1. QC Inspector ditolak mengakses form create (403)
        $this->actingAs($data['qcUser'])
            ->get(route('input-metal-detector.create'))
            ->assertStatus(403);

        // 2. QC Inspector ditolak menyimpan data dari form store (403)
        $this->actingAs($data['qcUser'])
            ->post(route('input-metal-detector.store'), [
                'tanggal' => ['2026-11-05'],
                'jam' => ['08:00'],
                'id_shift' => [$data['shift']->id],
                'line' => ['1'],
                'id_produk' => [$data['produk']->id],
                'kode_produksi' => ['261105 TEST'],
                'berat_produk' => ['250'],
            ])
            ->assertStatus(403);
    }

    public function test_produksi_can_create_without_qc_specimen_fields(): void
    {
        $data = $this->createTestData();

        // Produksi membuat data Bagian A dan Bagian B tanpa field uji spesimen QC
        $response = $this->actingAs($data['produksiUser'])
            ->post(route('input-metal-detector.store'), [
                'tanggal' => ['2026-11-05'],
                'jam' => ['08:00'],
                'id_shift' => [$data['shift']->id],
                'line' => ['1'],
                'id_produk' => [$data['produk']->id],
                'kode_produksi' => ['261105 MD01'],
                'berat_produk' => ['250'],
                'keterangan' => ['Ket Bagian A'],
                'merk' => ['Anritsu'],
                'waktu_mulai' => ['08:00'],
                'waktu_selesai' => ['09:00'],
                'gramasi' => ['250'],
                'jumlah_reject' => [2],
                'nama_operator' => ['Operator Budi'],
                'spv_foreman' => ['SPV Andi'],
                'keterangan_b' => ['Ket Bagian B Reject Logam'],
            ]);

        $response->assertRedirect(route('input-metal-detector.index'));
        $response->assertSessionHas('success');

        $record = InputMetalDetector::where('kode_produksi', '261105 MD01')->first();
        $this->assertNotNull($record);
        $this->assertEquals('Anritsu', $record->merk);
        $this->assertEquals(2, $record->jumlah_reject);
        $this->assertEquals('Operator Budi', $record->nama_operator);
        $this->assertEquals('SPV Andi', $record->spv_foreman);

        // Pastikan spesimen dan verifikasi QC masih null
        $this->assertNull($record->verifikasi_detail);
        $this->assertNull($record->verifikasi_hasil_proses);
        $this->assertNull($record->nama_qc);
        $this->assertNull($record->fe_depan_aktual);
    }

    public function test_qc_can_input_specimen_and_verification_via_show(): void
    {
        $data = $this->createTestData();

        // Buat record awal oleh Produksi
        $record = InputMetalDetector::create([
            'id_plan' => $data['plan']->id,
            'user_id' => $data['produksiUser']->id,
            'uuid' => (string) Str::uuid(),
            'id_shift' => $data['shift']->id,
            'line' => '1',
            'tanggal' => now(),
            'jam' => '08:30:00',
            'id_produk' => $data['produk']->id,
            'kode_produksi' => '261105 MD02',
            'berat_produk' => '500',
            'merk' => 'Ishida',
            'waktu_mulai' => '08:30:00',
            'waktu_selesai' => '09:30:00',
            'gramasi' => '500',
            'jumlah_reject' => 0,
            'nama_operator' => 'Operator Joko',
            'spv_foreman' => 'SPV Rian',
        ]);

        // QC melihat show page (icon mata)
        $showResponse = $this->actingAs($data['qcUser'])
            ->get(route('input-metal-detector.show', $record->uuid));
        $showResponse->assertStatus(200);
        $showResponse->assertViewHas('canEditQcVerification', true);

        // QC mengirim verifikasi spesimen dan verifikasi detail/proses
        $putResponse = $this->actingAs($data['qcUser'])
            ->put(route('input-metal-detector.qc-verification', $record->uuid), [
                'fe_depan_aktual' => '✔',
                'fe_tengah_aktual' => '✔',
                'fe_belakang_aktual' => '✔',
                'non_fe_depan_aktual' => '✔',
                'non_fe_tengah_aktual' => '✔',
                'non_fe_belakang_aktual' => '✔',
                'sus_depan_aktual' => '✔',
                'sus_tengah_aktual' => '✔',
                'sus_belakang_aktual' => '✔',
                'verifikasi_detail' => '✔',
                'verifikasi_hasil_proses' => '✔',
            ]);

        $putResponse->assertRedirect(route('input-metal-detector.show', $record->uuid));
        $putResponse->assertSessionHas('success');

        $record->refresh();
        $this->assertEquals('✔', $record->fe_depan_aktual);
        $this->assertEquals('✔', $record->sus_belakang_aktual);
        $this->assertEquals('✔', $record->verifikasi_detail);
        $this->assertEquals('✔', $record->verifikasi_hasil_proses);
        $this->assertEquals($data['qcUser']->name, $record->nama_qc);
        $this->assertNotNull($record->qc_verified_at);
    }

    public function test_produksi_cannot_submit_qc_verification(): void
    {
        $data = $this->createTestData();

        $record = InputMetalDetector::create([
            'id_plan' => $data['plan']->id,
            'user_id' => $data['produksiUser']->id,
            'uuid' => (string) Str::uuid(),
            'id_shift' => $data['shift']->id,
            'line' => '1',
            'tanggal' => now(),
            'jam' => '08:30:00',
            'id_produk' => $data['produk']->id,
            'kode_produksi' => '261105 MD03',
            'berat_produk' => '250',
        ]);

        // Produksi ditolak mengakses endpoint qc-verification (403)
        $this->actingAs($data['produksiUser'])
            ->put(route('input-metal-detector.qc-verification', $record->uuid), [
                'fe_depan_aktual' => '✔',
                'fe_tengah_aktual' => '✔',
                'fe_belakang_aktual' => '✔',
                'non_fe_depan_aktual' => '✔',
                'non_fe_tengah_aktual' => '✔',
                'non_fe_belakang_aktual' => '✔',
                'sus_depan_aktual' => '✔',
                'sus_tengah_aktual' => '✔',
                'sus_belakang_aktual' => '✔',
                'verifikasi_detail' => '✔',
                'verifikasi_hasil_proses' => '✔',
            ])
            ->assertStatus(403);
    }

    public function test_qc_inspector_is_redirected_to_show_when_accessing_edit(): void
    {
        $data = $this->createTestData();

        $record = InputMetalDetector::create([
            'id_plan' => $data['plan']->id,
            'user_id' => $data['produksiUser']->id,
            'uuid' => (string) Str::uuid(),
            'id_shift' => $data['shift']->id,
            'line' => '1',
            'tanggal' => now(),
            'jam' => '08:30:00',
            'id_produk' => $data['produk']->id,
            'kode_produksi' => '261105 MD04',
            'berat_produk' => '250',
        ]);

        // QC Inspector mengakses edit diarahkan ke halaman show
        $this->actingAs($data['qcUser'])
            ->get(route('input-metal-detector.edit', $record->uuid))
            ->assertRedirect(route('input-metal-detector.show', $record->uuid));
    }
}
