<?php

namespace Tests\Feature;

use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\LabelStartUp;
use App\Models\LabelStartUpDetail;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class LabelStartUpRoleWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected function createTestData(): array
    {
        $plan = Plan::first() ?: Plan::create([
            'uuid' => (string) Str::uuid(),
            'nama_plan' => 'Plan Test',
        ]);

        $produksiUser = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Operator Produksi Test',
            'username' => 'prod_' . Str::random(6),
            'email' => 'prod_' . Str::random(6) . '@test.local',
            'password' => bcrypt('password'),
            'role' => 'produksi',
            'id_role' => 2,
            'id_plan' => $plan->id,
        ]);

        $qcUser = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'QC Inspector Test',
            'username' => 'qc_' . Str::random(6),
            'email' => 'qc_' . Str::random(6) . '@test.local',
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
            'nama_produk' => 'Nugget Test ' . Str::random(4),
        ]);

        return compact('plan', 'produksiUser', 'qcUser', 'shift', 'produk');
    }

    public function test_qc_inspector_cannot_access_create_and_store(): void
    {
        $data = $this->createTestData();

        // 1. QC Inspector ditolak mengakses halaman create (403)
        $this->actingAs($data['qcUser'])
            ->get(route('label-pemeriksaan.create'))
            ->assertStatus(403);

        // 2. QC Inspector ditolak menyimpan data dari form create (403)
        $this->actingAs($data['qcUser'])
            ->post(route('label-pemeriksaan.store'), [
                'tanggal' => '2026-11-04',
                'id_shift' => $data['shift']->id,
                'id_produk' => [$data['produk']->id],
                'label_produk' => ['PL'],
                'jam' => ['08:00'],
                'nama_operator' => ['Operator 1'],
                'ttd_operator' => ['data:image/png;base64,sample'],
            ])
            ->assertStatus(403);
    }

    public function test_produksi_can_create_without_qc_fields(): void
    {
        $data = $this->createTestData();

        // Role Produksi membuat record baru tanpa field QC
        $response = $this->actingAs($data['produksiUser'])
            ->post(route('label-pemeriksaan.store'), [
                'tanggal' => '2026-11-04',
                'id_shift' => $data['shift']->id,
                'id_produk' => [$data['produk']->id],
                'berat' => ['250'],
                'label_produk' => ['PL'],
                'jam' => ['08:00'],
                'nama_operator' => ['Budi Santoso'],
                'ttd_operator' => ['data:image/png;base64,operator_signature'],
                'tindakan_koreksi' => ['Sesuai standar operasional'],
            ]);

        $response->assertRedirect(route('label-pemeriksaan.index'));

        $header = LabelStartUp::where('user_id', $data['produksiUser']->id)->latest('id')->firstOrFail();
        $detail = $header->details()->firstOrFail();

        $this->assertEquals('Budi Santoso', $detail->nama_operator);
        $this->assertEquals('PL', $detail->label_produk);
        // Field QC harus null pada saat dibuat oleh Produksi
        $this->assertNull($detail->hasil_pengecekan);
        $this->assertNull($detail->nama_qc);
        $this->assertNull($detail->ttd_qc);
    }

    public function test_qc_inspector_can_view_show_and_submit_verification(): void
    {
        $data = $this->createTestData();

        // Buat record awal oleh Produksi
        $header = LabelStartUp::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $data['plan']->id,
            'id_shift' => $data['shift']->id,
            'user_id' => $data['produksiUser']->id,
            'tanggal' => '2026-11-04',
        ]);

        $detail = LabelStartUpDetail::create([
            'label_start_up_id' => $header->id,
            'id_produk' => $data['produk']->id,
            'berat' => 500,
            'label_produk' => 'MC',
            'jam' => '09:00',
            'nama_operator' => 'Operator Prod',
            'ttd_operator' => 'data:image/png;base64,ttd_op',
            'hasil_pengecekan' => null,
            'nama_qc' => null,
        ]);

        // QC Inspector membuka halaman show (icon mata)
        $this->actingAs($data['qcUser'])
            ->get(route('label-pemeriksaan.show', $header->uuid))
            ->assertOk()
            ->assertSee('Form Input / Update Verifikasi QC Inspector');

        // QC Inspector menginput verifikasi hasil (OK)
        $response = $this->actingAs($data['qcUser'])
            ->put(route('label-pemeriksaan.qc-verification', $header->uuid), [
                'hasil_pengecekan' => [
                    $detail->id => 'OK',
                ],
            ]);

        $response->assertRedirect(route('label-pemeriksaan.show', $header->uuid));

        $detail->refresh();
        $this->assertEquals('OK', $detail->hasil_pengecekan);
        $this->assertEquals($data['qcUser']->name, $detail->nama_qc);
    }

    public function test_produksi_cannot_submit_qc_verification(): void
    {
        $data = $this->createTestData();

        $header = LabelStartUp::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $data['plan']->id,
            'id_shift' => $data['shift']->id,
            'user_id' => $data['produksiUser']->id,
            'tanggal' => '2026-11-04',
        ]);

        $detail = LabelStartUpDetail::create([
            'label_start_up_id' => $header->id,
            'id_produk' => $data['produk']->id,
            'label_produk' => 'PL',
            'jam' => '09:00',
            'nama_operator' => 'Operator',
            'ttd_operator' => 'data:image/png;base64,ttd',
        ]);

        // Role Produksi mencoba submit verifikasi QC -> ditolak (403)
        $this->actingAs($data['produksiUser'])
            ->put(route('label-pemeriksaan.qc-verification', $header->uuid), [
                'hasil_pengecekan' => [
                    $detail->id => 'OK',
                ],
            ])
            ->assertStatus(403);
    }

    public function test_produksi_edit_preserves_existing_qc_verification(): void
    {
        $data = $this->createTestData();

        $header = LabelStartUp::create([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $data['plan']->id,
            'id_shift' => $data['shift']->id,
            'user_id' => $data['produksiUser']->id,
            'tanggal' => '2026-11-04',
        ]);

        $detail = LabelStartUpDetail::create([
            'label_start_up_id' => $header->id,
            'id_produk' => $data['produk']->id,
            'berat' => 250,
            'label_produk' => 'PL',
            'jam' => '08:30',
            'nama_operator' => 'Operator Awal',
            'ttd_operator' => 'data:image/png;base64,ttd_awal',
            'hasil_pengecekan' => 'OK',
            'nama_qc' => 'QC Inspector Handal',
        ]);

        // Produksi memperbarui data operasional
        $response = $this->actingAs($data['produksiUser'])
            ->put(route('label-pemeriksaan.update', $header->id), [
                'tanggal' => '2026-11-04',
                'id_shift' => $data['shift']->id,
                'id_produk' => [$data['produk']->id],
                'berat' => ['300'],
                'label_produk' => ['MC'],
                'jam' => ['08:45'],
                'nama_operator' => ['Operator Baru'],
                'ttd_operator' => ['data:image/png;base64,ttd_baru'],
                'old_foto' => [null],
                'hasil_pengecekan' => ['OK'],
                'nama_qc' => ['QC Inspector Handal'],
            ]);

        $response->assertRedirect(route('label-pemeriksaan.index'));

        $detailBaru = $header->fresh()->details()->firstOrFail();
        $this->assertEquals('Operator Baru', $detailBaru->nama_operator);
        $this->assertEquals('MC', $detailBaru->label_produk);
        // Verifikasi QC tetap dipertahankan
        $this->assertEquals('OK', $detailBaru->hasil_pengecekan);
        $this->assertEquals('QC Inspector Handal', $detailBaru->nama_qc);
    }
}
