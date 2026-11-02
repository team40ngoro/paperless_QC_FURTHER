<?php

namespace Tests\Feature;

use App\Models\ReceivingInspection;
use App\Services\ReceivingInspection\ReceivingInspectionRegistry;
use App\Services\ReceivingInspection\ReceivingInspectionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Concerns\InteractsWithReceivingInspections;
use Tests\TestCase;

class ReceivingInspectionWorkflowTest extends TestCase
{
    use DatabaseTransactions;
    use InteractsWithReceivingInspections;

    public function test_production_and_qc_submission_completes_record_and_writes_audit(): void
    {
        $production = $this->productionUser();
        $qc = $this->qcUser();
        $record = $this->createRecord($production);
        $service = app(ReceivingInspectionService::class);

        $service->submitSection($production, $record, 'PRODUCTION', $this->chillroomProductionPayload());
        $service->submitSection($qc, $record->fresh(), 'QC', $this->chillroomQcPayload());

        $record->refresh();
        $this->assertSame('SUBMITTED', $record->prod_status);
        $this->assertSame('SUBMITTED', $record->qc_status);
        $this->assertTrue($record->is_complete);
        $this->assertSame('COMPLETE', $record->status);
        $this->assertSame($production->id, $record->prod_filled_by);
        $this->assertSame($qc->id, $record->qc_filled_by);

        $this->assertDatabaseHas('inspection_audit_logs', [
            'receiving_inspection_id' => $record->id,
            'section' => 'PRODUCTION',
            'action' => 'SUBMIT',
            'actor_user_id' => $production->id,
        ]);
        $this->assertDatabaseHas('inspection_audit_logs', [
            'receiving_inspection_id' => $record->id,
            'section' => 'QC',
            'action' => 'SUBMIT',
            'actor_user_id' => $qc->id,
        ]);
    }

    public function test_admin_reopen_preserves_data_and_allows_section_owner_to_edit_again(): void
    {
        $production = $this->productionUser();
        $qc = $this->qcUser();
        $admin = $this->adminUser();
        $record = $this->createRecord($production);
        $service = app(ReceivingInspectionService::class);

        $service->submitSection($production, $record, 'PRODUCTION', $this->chillroomProductionPayload());
        $service->reopen($admin, $record->fresh(), 'PRODUCTION', 'Correct arrival time');

        $record->refresh();
        $this->assertSame('DRAFT', $record->prod_status);
        $this->assertNull($record->prod_submitted_at);
        $this->assertSame('08:00', $record->production_data['prod_arrival_time']);

        $payload = $this->chillroomProductionPayload();
        $payload['prod_arrival_time'] = '09:15';
        $service->updateSection($production, $record, 'PRODUCTION', $payload);

        $this->assertSame('09:15', $record->fresh()->production_data['prod_arrival_time']);
        $this->assertDatabaseHas('inspection_audit_logs', [
            'receiving_inspection_id' => $record->id,
            'section' => 'PRODUCTION',
            'action' => 'REOPEN',
            'actor_user_id' => $admin->id,
            'reason' => 'Correct arrival time',
        ]);
    }

    public function test_admin_soft_delete_keeps_audit_rows(): void
    {
        $production = $this->productionUser();
        $admin = $this->adminUser();
        $record = $this->createRecord($production);
        $service = app(ReceivingInspectionService::class);

        $service->delete($admin, $record, 'Created in error');

        $this->assertSoftDeleted('receiving_inspections', ['id' => $record->id]);
        $this->assertDatabaseHas('inspection_audit_logs', [
            'receiving_inspection_id' => $record->id,
            'action' => 'DELETE',
            'actor_user_id' => $admin->id,
            'reason' => 'Created in error',
        ]);
    }

    public function test_web_index_create_show_audit_and_print_pages_render(): void
    {
        $production = $this->productionUser();
        $record = $this->createRecord($production);

        $this->actingAs($production)->get('/receiving-inspections')
            ->assertOk()->assertSee('Incoming Inspection');

        $this->actingAs($production)->get('/receiving-inspections/create')
            ->assertOk()->assertSee('Create Incoming Inspection');

        $this->actingAs($production)->get("/receiving-inspections/{$record->uuid}")
            ->assertOk()->assertSee('Production')->assertSee('QC');

        $this->actingAs($production)->get("/receiving-inspections/{$record->uuid}/audit")
            ->assertOk()->assertSee('Audit Trail');

        $this->actingAs($production)->get("/receiving-inspections/{$record->uuid}/print")
            ->assertOk()->assertSee('FP-01');
    }

    public function test_csv_and_xlsx_exports_are_available(): void
    {
        $production = $this->productionUser();
        $this->createRecord($production);

        $this->actingAs($production)->get('/receiving-inspections/export?area=CHILLROOM')
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $this->actingAs($production)->get('/receiving-inspections/export?area=CHILLROOM&format=xlsx')
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_frontend_only_renders_the_section_owned_by_the_role(): void
    {
        $production = $this->productionUser();
        $qc = $this->qcUser();
        $admin = $this->adminUser();
        $record = $this->createRecord($production);

        $this->actingAs($production)->get("/receiving-inspections/{$record->uuid}")
            ->assertOk()
            ->assertSee('Writer: Production Input Checker')
            ->assertDontSee('Writer: QC Auditor')
            ->assertDontSee('Verifikasi Status');

        $this->actingAs($production)->get("/receiving-inspections/{$record->uuid}/print")
            ->assertOk()
            ->assertSee('PRODUCTION section')
            ->assertDontSee('QC section');

        $this->actingAs($qc)->get("/receiving-inspections/{$record->uuid}")
            ->assertOk()
            ->assertSee('Writer: QC Auditor')
            ->assertDontSee('Writer: Production Input Checker');

        $this->actingAs($qc)->get("/receiving-inspections/{$record->uuid}/print")
            ->assertOk()
            ->assertSee('QC section')
            ->assertDontSee('PRODUCTION section');

        $this->actingAs($admin)->get("/receiving-inspections/{$record->uuid}")
            ->assertOk()
            ->assertSee('Writer: Production Input Checker')
            ->assertSee('Writer: QC Auditor');
    }

    public function test_index_filters_and_columns_follow_section_access(): void
    {
        $production = $this->productionUser();
        $qc = $this->qcUser();
        $admin = $this->adminUser();
        $this->createRecord($production);

        $this->actingAs($production)->get('/receiving-inspections')
            ->assertOk()
            ->assertSee('Production status')
            ->assertDontSee('QC status')
            ->assertDontSee('QC finding');

        $this->actingAs($qc)->get('/receiving-inspections')
            ->assertOk()
            ->assertSee('QC status')
            ->assertSee('QC finding')
            ->assertDontSee('Production status');

        $this->actingAs($admin)->get('/receiving-inspections')
            ->assertOk()
            ->assertSee('Production status')
            ->assertSee('QC status');
    }

    public function test_production_cannot_view_another_production_users_audit_trail(): void
    {
        $owner = $this->productionUser();
        $other = $this->productionUser();
        $record = $this->createRecord($owner);

        $this->actingAs($other)->get("/receiving-inspections/{$record->uuid}/audit")
            ->assertForbidden();
    }

    public function test_registry_drives_allowed_fields_and_labels(): void
    {
        $registry = app(ReceivingInspectionRegistry::class);

        $productionFields = $registry->fieldKeys('CHILLROOM', 'PRODUCTION');
        $qcFields = $registry->fieldKeys('CHILLROOM', 'QC');

        $this->assertContains('prod_rm_temp_c', $productionFields);
        $this->assertNotContains('prod_verification_status', $productionFields);
        $this->assertNotContains('prod_rm_temp_c', $qcFields);
        $this->assertContains('qc_sensory', $qcFields);
        $this->assertSame('E-QF 01', $registry->formCodes('CHILLROOM')['qc_form_code']);
        $this->assertSame('Jumat', $registry->weekdayId('2026-10-30'));
        $this->assertSame('NOT_OK', $registry->normalizeOkNotOk('x'));
    }

    public function test_navbar_keeps_original_menus_for_production_user(): void
    {
        $production = $this->productionUser();

        $this->actingAs($production)->get('/receiving-inspections')
            ->assertOk()
            ->assertSee('[QF-01/00] Chillroom')
            ->assertSee('[QF-01/00] Seasoning')
            ->assertSee('[QF-02/00] Shoestring')
            ->assertSee('[QF-03/00] Kemasan')
            ->assertSee('[QF-34/00] Rebox')
            ->assertSee('Meat Preparation');
    }

    public function test_navbar_shows_qc_menus_to_qc_user(): void
    {
        $qc = $this->qcUser();

        $this->actingAs($qc)->get('/receiving-inspections')
            ->assertOk()
            ->assertSee('Merged Receiving Inspection')
            ->assertSee('[QF-01/00] Chillroom')
            ->assertSee('[QF-01/00] Seasoning')
            ->assertSee('[QF-02/00] Shoestring')
            ->assertSee('[QF-03/00] Kemasan')
            ->assertSee('[QF-34/00] Rebox')
            ->assertSee('Meat Preparation');
    }

    public function test_spv_qc_role_accesses_qc_inspection(): void
    {
        $production = $this->productionUser();
        $spvQc = $this->spvQcUser();
        $record = $this->createRecord($production);

        $this->actingAs($spvQc)->get('/receiving-inspections')
            ->assertOk()
            ->assertSee('QC status')
            ->assertSee('QC finding')
            ->assertDontSee('Production status');

        $this->actingAs($spvQc)->get("/receiving-inspections/{$record->uuid}")
            ->assertOk()
            ->assertSee('Writer: QC Auditor')
            ->assertDontSee('Writer: Production Input Checker');
    }
}
