<?php

namespace Tests\Feature;

use App\Exports\PemeriksaanBahanKemasExport;
use App\Models\DataDefect;
use App\Models\DataShift;
use App\Models\PemeriksaanBahanKemas;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PemeriksaanBahanKemasProdusenTest extends TestCase
{
    use DatabaseTransactions;

    protected Plan $plan;
    protected DataShift $shift;
    protected User $qcUser;
    protected string $produsen = 'PT Produsen Kemasan';

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::first() ?? Plan::create(['nama_plan' => 'Plan Testing Produsen Bahan Kemas']);
        $this->shift = DataShift::where('id_plan', $this->plan->id)->first() ?? DataShift::create([
            'uuid' => (string) Str::uuid(),
            'shift' => '1',
            'id_plan' => $this->plan->id,
            'user_id' => 1,
        ]);

        $qcRole = Role::firstOrCreate(['id' => 3], ['role' => 'QC Inspector']);

        $createPerm = Permission::firstOrCreate(['name' => 'create-pemeriksaan-bahan-kemas', 'guard_name' => 'web']);
        $editPerm = Permission::firstOrCreate(['name' => 'edit-pemeriksaan-bahan-kemas', 'guard_name' => 'web']);
        $qcRole->permissions()->syncWithoutDetaching([$createPerm->id, $editPerm->id]);

        $this->qcUser = $this->makeUser($qcRole, 'QC Inspector');

        DataDefect::create([
            'id_plan' => $this->plan->id,
            'user_id' => $this->qcUser->id,
            'nama_produsen' => $this->produsen,
            'detail_defect' => [],
        ]);
    }

    private function makeUser(Role $role, string $roleName): User
    {
        $suffix = Str::random(8);

        return User::create([
            'name' => $roleName.' Tester '.$suffix,
            'username' => 'produsen-bahan-kemas.'.$suffix,
            'email' => 'produsen-bahan-kemas.'.$suffix.'@test.com',
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
            'user_id' => $this->qcUser->id,
            'shift_id' => $this->shift->id,
            'tanggal' => now()->format('Y-m-d H:i:s'),
            'jam' => '09:00',
            'nama_kemasan' => 'Kemasan Test',
            'kode_produksi' => 'KMS-'.Str::random(6),
            'kondisi_bahan_kemasan' => 'OK',
            'keterangan' => 'Data produksi',
            'nama_produsen' => $this->produsen,
        ], $overrides));
    }

    private function storePayload(array $overrides = []): array
    {
        return array_merge([
            'shift_id' => $this->shift->id,
            'tanggal' => now()->format('Y-m-d'),
            'nama_produsen' => $this->produsen,
            'items' => [
                [
                    'jam' => '08:00',
                    'nama_kemasan' => 'Kemasan A',
                    'kode_produksi' => 'A-001',
                    'kondisi_bahan_kemasan' => 'OK',
                    'keterangan' => 'Baik',
                ],
                [
                    'jam' => '09:00',
                    'nama_kemasan' => 'Kemasan B',
                    'kode_produksi' => 'B-001',
                    'kondisi_bahan_kemasan' => 'Tidak OK',
                    'keterangan' => 'Rusak',
                ],
            ],
        ], $overrides);
    }

    public function test_create_page_shows_produsen_dropdown_from_master_data(): void
    {
        $response = $this->actingAs($this->qcUser)->get(route('pemeriksaan-bahan-kemas.create'));

        $response->assertOk();
        $response->assertSee('Nama Produsen');
        $response->assertSee('name="nama_produsen"', false);
        $response->assertSee($this->produsen);
    }

    public function test_store_saves_nama_produsen_for_all_items(): void
    {
        $response = $this->actingAs($this->qcUser)->post(
            route('pemeriksaan-bahan-kemas.store'),
            $this->storePayload()
        );

        $response->assertRedirect(route('pemeriksaan-bahan-kemas.index'));
        $this->assertDatabaseHas('pemeriksaan_bahan_kemas', [
            'nama_kemasan' => 'Kemasan A',
            'nama_produsen' => $this->produsen,
        ]);
        $this->assertDatabaseHas('pemeriksaan_bahan_kemas', [
            'nama_kemasan' => 'Kemasan B',
            'nama_produsen' => $this->produsen,
        ]);
    }

    public function test_store_requires_nama_produsen(): void
    {
        $payload = $this->storePayload();
        unset($payload['nama_produsen']);

        $response = $this->actingAs($this->qcUser)->post(
            route('pemeriksaan-bahan-kemas.store'),
            $payload
        );

        $response->assertSessionHasErrors('nama_produsen');
    }

    public function test_edit_page_shows_saved_nama_produsen(): void
    {
        $item = $this->makeItem();

        $response = $this->actingAs($this->qcUser)->get(
            route('pemeriksaan-bahan-kemas.edit', $item->uuid)
        );

        $response->assertOk();
        $response->assertSee('Nama Produsen');
        $response->assertSee($this->produsen);
    }

    public function test_update_saves_nama_produsen(): void
    {
        $item = $this->makeItem(['nama_produsen' => 'Produsen Lama']);

        $response = $this->actingAs($this->qcUser)->put(
            route('pemeriksaan-bahan-kemas.update', $item->uuid),
            [
                'shift_id' => $this->shift->id,
                'tanggal' => now()->format('d-m-Y H:i:s'),
                'jam' => '10:30',
                'nama_produsen' => $this->produsen,
                'nama_kemasan' => 'Kemasan Update',
                'kode_produksi' => 'UPD-001',
                'kondisi_bahan_kemasan' => 'OK',
                'keterangan' => 'Diperbarui',
            ]
        );

        $response->assertRedirect(route('pemeriksaan-bahan-kemas.index'));
        $this->assertDatabaseHas('pemeriksaan_bahan_kemas', [
            'id' => $item->id,
            'nama_produsen' => $this->produsen,
        ]);
    }

    public function test_show_displays_nama_produsen(): void
    {
        $item = $this->makeItem();

        $response = $this->actingAs($this->qcUser)->get(
            route('pemeriksaan-bahan-kemas.show', $item->uuid)
        );

        $response->assertOk();
        $response->assertSee('Nama Produsen');
        $response->assertSee($this->produsen);
    }

    public function test_index_displays_nama_produsen(): void
    {
        $this->makeItem();

        $response = $this->actingAs($this->qcUser)->get(route('pemeriksaan-bahan-kemas.index'));

        $response->assertOk();
        $response->assertSee('Nama Produsen');
        $response->assertSee($this->produsen);
    }

    public function test_excel_export_includes_nama_produsen(): void
    {
        $item = $this->makeItem();

        $export = new PemeriksaanBahanKemasExport(collect([$item]), ['plan_nama' => 'Salatiga']);

        $this->assertContains('Nama Produsen', $export->headings());
        $this->assertContains($this->produsen, $export->collection()->first());
    }

    public function test_pdf_export_includes_nama_produsen(): void
    {
        $item = $this->makeItem();
        $this->actingAs($this->qcUser);

        $html = view('qc-sistem.pemeriksaan_bahan_kemas.export_pdf', [
            'data' => collect([$item]),
            'kode_form' => 'QF 03/00',
            'filters' => [],
        ])->render();

        $this->assertStringContainsString('Nama Produsen', $html);
        $this->assertStringContainsString($this->produsen, $html);
    }
}
