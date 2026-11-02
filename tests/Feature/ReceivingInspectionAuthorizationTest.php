<?php

namespace Tests\Feature;

use App\Models\ReceivingInspection;
use App\Services\ReceivingInspection\ReceivingInspectionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\InteractsWithReceivingInspections;
use Tests\TestCase;

class ReceivingInspectionAuthorizationTest extends TestCase
{
    use DatabaseTransactions;
    use InteractsWithReceivingInspections;

    public function test_only_production_can_create_records(): void
    {
        $production = $this->productionUser();
        $qc = $this->qcUser();

        Sanctum::actingAs($production);
        $this->postJson('/api/inspections', [
            'area' => 'CHILLROOM',
            'inspection_date' => '2026-10-30',
        ])->assertStatus(201)->assertJsonPath('area', 'CHILLROOM');

        Sanctum::actingAs($qc);
        $this->postJson('/api/inspections', [
            'area' => 'CHILLROOM',
            'inspection_date' => '2026-10-30',
        ])->assertStatus(403);
    }

    public function test_production_cannot_write_qc_and_qc_cannot_write_production(): void
    {
        $production = $this->productionUser();
        $qc = $this->qcUser();
        $record = $this->createRecord($production);

        Sanctum::actingAs($production);
        $this->patchJson("/api/inspections/{$record->uuid}/qc", [
            'qc_sensory' => 'OK',
        ])->assertStatus(403);

        Sanctum::actingAs($qc);
        $this->patchJson("/api/inspections/{$record->uuid}/production", [
            'prod_rm_temp_c' => -18,
        ])->assertStatus(403);
    }

    public function test_qc_cannot_smuggle_production_keys_into_qc_payload(): void
    {
        $production = $this->productionUser();
        $qc = $this->qcUser();
        $record = $this->createRecord($production);

        Sanctum::actingAs($qc);
        $response = $this->patchJson("/api/inspections/{$record->uuid}/qc", [
            'qc_sensory' => 'OK',
            'prod_rm_temp_c' => 5,
        ]);

        $response->assertStatus(403)->assertJsonPath('rejectedFields.0', 'prod_rm_temp_c');
    }

    public function test_submitted_section_is_immutable_until_reopened(): void
    {
        $production = $this->productionUser();
        $record = $this->createRecord($production);

        app(ReceivingInspectionService::class)->submitSection(
            $production,
            $record,
            'PRODUCTION',
            $this->chillroomProductionPayload()
        );

        Sanctum::actingAs($production);
        $this->patchJson("/api/inspections/{$record->uuid}/production", [
            'prod_notes' => 'late edit',
        ])->assertStatus(409);
    }

    public function test_admin_reopen_requires_reason(): void
    {
        $production = $this->productionUser();
        $admin = $this->adminUser();
        $record = $this->createRecord($production);

        app(ReceivingInspectionService::class)->submitSection(
            $production,
            $record,
            'PRODUCTION',
            $this->chillroomProductionPayload()
        );

        Sanctum::actingAs($admin);
        $this->postJson("/api/inspections/{$record->uuid}/reopen", [
            'section' => 'PRODUCTION',
            'reason' => '',
        ])->assertStatus(400);
    }

    public function test_admin_cannot_edit_section_fields(): void
    {
        $production = $this->productionUser();
        $admin = $this->adminUser();
        $record = $this->createRecord($production);

        Sanctum::actingAs($admin);
        $this->patchJson("/api/inspections/{$record->uuid}/production", [
            'prod_rm_temp_c' => -18,
        ])->assertStatus(403);
    }

    public function test_unknown_fields_are_rejected_with_400(): void
    {
        $production = $this->productionUser();
        $record = $this->createRecord($production);

        Sanctum::actingAs($production);
        $this->patchJson("/api/inspections/{$record->uuid}/production", [
            'qc_status' => 'SUBMITTED',
        ])->assertStatus(400)->assertJsonPath('unknownFields.0', 'qc_status');
    }

    public function test_show_exposes_role_specific_permissions(): void
    {
        $production = $this->productionUser();
        $qc = $this->qcUser();
        $record = $this->createRecord($production);

        Sanctum::actingAs($production);
        $this->getJson("/api/inspections/{$record->uuid}")
            ->assertOk()
            ->assertJsonPath('permissions.canEditProduction', true)
            ->assertJsonPath('permissions.canEditQc', false)
            ->assertJsonPath('permissions.canViewProduction', true)
            ->assertJsonPath('permissions.canViewQc', false);

        Sanctum::actingAs($qc);
        $this->getJson("/api/inspections/{$record->uuid}")
            ->assertOk()
            ->assertJsonPath('permissions.canEditProduction', false)
            ->assertJsonPath('permissions.canEditQc', true)
            ->assertJsonPath('permissions.canViewProduction', false)
            ->assertJsonPath('permissions.canViewQc', true);
    }
}
