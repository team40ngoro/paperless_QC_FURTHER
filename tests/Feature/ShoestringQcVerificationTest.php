<?php

namespace Tests\Feature;

use App\Models\DataShift;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Shoestring;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ShoestringQcVerificationTest extends TestCase
{
    use DatabaseTransactions;

    protected Plan $plan;
    protected DataShift $shift;
    protected User $produksiUser;
    protected User $qcUser;
    protected User $spvUser;
    protected User $produksiSpvUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::first() ?? Plan::create(['nama_plan' => 'Plan Testing Shoestring']);
        $this->shift = DataShift::where('id_plan', $this->plan->id)->first() ?? DataShift::create([
            'uuid' => (string) Str::uuid(),
            'shift' => '1',
            'id_plan' => $this->plan->id,
            'user_id' => 1,
        ]);

        $produksiRole = Role::firstOrCreate(['id' => 2], ['role' => 'produksi']);
        $qcRole = Role::firstOrCreate(['id' => 3], ['role' => 'QC Inspector']);
        $spvRole = Role::firstOrCreate(['id' => 4], ['role' => 'SPV QC']);
        $produksiSpvRole = Role::firstOrCreate(['id' => 8], ['role' => 'Produksi SPV']);

        $createPerm = Permission::firstOrCreate(['name' => 'create-shoestring', 'guard_name' => 'web']);
        $editPerm = Permission::firstOrCreate(['name' => 'edit-shoestring', 'guard_name' => 'web']);
        $viewPerm = Permission::firstOrCreate(['name' => 'view-shoestring', 'guard_name' => 'web']);

        foreach ([$produksiRole, $qcRole, $spvRole, $produksiSpvRole] as $role) {
            $role->permissions()->syncWithoutDetaching([$createPerm->id, $editPerm->id, $viewPerm->id]);
        }

        $this->produksiUser = $this->makeUser($produksiRole, 'produksi');
        $this->qcUser = $this->makeUser($qcRole, 'QC Inspector');
        $this->spvUser = $this->makeUser($spvRole, 'SPV QC');
        $this->produksiSpvUser = $this->makeUser($produksiSpvRole, 'Produksi SPV');
    }

    private function makeUser(Role $role, string $roleName): User
    {
        $suffix = Str::random(8);

        return User::create([
            'name' => $roleName.' Tester '.$suffix,
            'username' => 'shoestring.'.$suffix,
            'email' => 'shoestring.'.$suffix.'@test.com',
            'password' => bcrypt('password'),
            'id_role' => $role->id,
            'role' => $roleName,
            'id_plan' => $this->plan->id,
        ]);
    }

    private function makeShoestring(array $overrides = []): Shoestring
    {
        return Shoestring::create(array_merge([
            'uuid' => (string) Str::uuid(),
            'id_plan' => $this->plan->id,
            'created_by' => $this->produksiUser->id,
            'nama_produsen' => 'Produsen Kentang Test',
            'kode_produksi' => 'SHS-'.Str::random(6),
            'best_before' => now()->addDays(3)->format('Y-m-d'),
            'berat_sampling' => '100',
            'catatan' => 'Data produksi',
            'shift_id' => $this->shift->id,
            'tanggal' => now()->format('Y-m-d H:i:s'),
            'jam' => '09:00',
        ], $overrides));
    }

    public function test_produksi_create_page_does_not_show_defect_input(): void
    {
        $response = $this->actingAs($this->produksiUser)->get(route('shoestring.create'));

        $response->assertStatus(200);
        $response->assertDontSee('sampling_defect_qty', false);
        $response->assertDontSee('Pemeriksaan Defect', false);
    }

    public function test_qc_can_submit_verification_with_defects(): void
    {
        $shoestring = $this->makeShoestring();

        $response = $this->actingAs($this->qcUser)->post(route('shoestring.qc-verifications.store', $shoestring->uuid), [
            'verifikasi' => 'NOT_OK',
            'sampling_defect_qty' => [
                'Berkulit - Utuh' => '2',
                'Busuk' => '1',
            ],
            'note' => 'Ada kentang busuk',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shoestring_qc_verifications', [
            'shoestring_id' => $shoestring->id,
            'shoestring_uuid' => $shoestring->uuid,
            'verifikasi' => 'NOT_OK',
            'total_defect' => '3',
            'note' => 'Ada kentang busuk',
            'verified_by' => $this->qcUser->id,
        ]);

        $this->assertNotNull($shoestring->fresh()->latestQcVerification);
    }

    public function test_produksi_cannot_submit_qc_verification(): void
    {
        $shoestring = $this->makeShoestring();

        $response = $this->actingAs($this->produksiUser)->post(route('shoestring.qc-verifications.store', $shoestring->uuid), [
            'verifikasi' => 'OK',
        ]);

        $response->assertForbidden();
    }

    public function test_spv_qc_cannot_submit_qc_verification(): void
    {
        $shoestring = $this->makeShoestring();

        $response = $this->actingAs($this->spvUser)->post(route('shoestring.qc-verifications.store', $shoestring->uuid), [
            'verifikasi' => 'OK',
        ]);

        $response->assertForbidden();
    }

    public function test_qc_can_approve_as_qc_inspector(): void
    {
        $shoestring = $this->makeShoestring();

        $response = $this->actingAs($this->qcUser)->postJson(route('shoestring.approve', $shoestring->uuid), [
            'type' => 'qc',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $shoestring->refresh();
        $this->assertTrue($shoestring->approved_by_qc);
        $this->assertEquals($this->qcUser->id, $shoestring->qc_approved_by);
    }

    public function test_produksi_spv_can_approve_as_diketahui(): void
    {
        $shoestring = $this->makeShoestring();

        $response = $this->actingAs($this->produksiSpvUser)->postJson(route('shoestring.approve', $shoestring->uuid), [
            'type' => 'produksi',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $shoestring->refresh();
        $this->assertTrue($shoestring->approved_by_produksi);
        $this->assertEquals($this->produksiSpvUser->id, $shoestring->produksi_approved_by);
    }

    public function test_regular_produksi_cannot_approve_as_diketahui(): void
    {
        $shoestring = $this->makeShoestring();

        $response = $this->actingAs($this->produksiUser)->postJson(route('shoestring.approve', $shoestring->uuid), [
            'type' => 'produksi',
        ]);

        $response->assertForbidden();
        $this->assertFalse($shoestring->fresh()->approved_by_produksi);
    }

    public function test_spv_qc_can_approve_as_disetujui(): void
    {
        $shoestring = $this->makeShoestring();

        $response = $this->actingAs($this->spvUser)->postJson(route('shoestring.approve', $shoestring->uuid), [
            'type' => 'spv',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $shoestring->refresh();
        $this->assertTrue($shoestring->approved_by_spv);
        $this->assertEquals($this->spvUser->id, $shoestring->spv_approved_by);
    }

    public function test_show_page_displays_qc_verification_form_for_qc(): void
    {
        $shoestring = $this->makeShoestring();

        $response = $this->actingAs($this->qcUser)->get(route('shoestring.show', $shoestring->uuid));

        $response->assertStatus(200);
        $response->assertSee('Verifikasi QC');
        $response->assertSee(route('shoestring.qc-verifications.store', $shoestring->uuid), false);
    }
}
