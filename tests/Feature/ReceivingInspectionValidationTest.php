<?php

namespace Tests\Feature;

use App\Services\ReceivingInspection\ReceivingInspectionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\InteractsWithReceivingInspections;
use Tests\TestCase;

class ReceivingInspectionValidationTest extends TestCase
{
    use DatabaseTransactions;
    use InteractsWithReceivingInspections;

    public function test_temperature_outside_registry_range_is_rejected(): void
    {
        $production = $this->productionUser();
        $record = $this->createRecord($production);

        Sanctum::actingAs($production);
        $this->patchJson("/api/inspections/{$record->uuid}/production", [
            'prod_rm_temp_c' => 61,
        ])->assertStatus(400)->assertJsonStructure(['message', 'errors' => ['prod_rm_temp_c']]);

        $this->patchJson("/api/inspections/{$record->uuid}/production", [
            'prod_rm_temp_c' => -61,
        ])->assertStatus(400)->assertJsonStructure(['errors' => ['prod_rm_temp_c']]);
    }

    public function test_weight_must_be_positive(): void
    {
        $production = $this->productionUser();
        $record = $this->createRecord($production);

        Sanctum::actingAs($production);
        $this->patchJson("/api/inspections/{$record->uuid}/production", [
            'prod_weight_per_pack' => 0,
        ])->assertStatus(400)->assertJsonStructure(['errors' => ['prod_weight_per_pack']]);
    }

    public function test_meat_sampling_fields_are_required_when_toggle_is_on(): void
    {
        $production = $this->productionUser();
        $record = $this->createRecord($production);

        $payload = $this->chillroomProductionPayload();
        $payload['prod_is_meat_rm'] = true;

        Sanctum::actingAs($production);
        $this->postJson("/api/inspections/{$record->uuid}/production/submit", [
            'fields' => $payload,
        ])->assertStatus(400)->assertJsonStructure(['errors' => ['prod_sample_count']]);
    }

    public function test_shoestring_expiry_cannot_precede_production_date(): void
    {
        $production = $this->productionUser();
        $record = $this->createRecord($production, 'SHOESTRING');

        $payload = $this->shoestringProductionPayload();
        $payload['prod_expiry_date'] = '2026-10-01';

        Sanctum::actingAs($production);
        $this->postJson("/api/inspections/{$record->uuid}/production/submit", [
            'fields' => $payload,
        ])->assertStatus(400)->assertJsonStructure(['errors' => ['prod_expiry_date']]);
    }

    public function test_premix_qc_not_ok_requires_notes(): void
    {
        $production = $this->productionUser();
        $qc = $this->qcUser();
        $record = $this->createRecord($production, 'PREMIX');

        $payload = $this->premixQcPayload();
        $payload['qc_sensory'] = 'NOT_OK';

        Sanctum::actingAs($qc);
        $this->postJson("/api/inspections/{$record->uuid}/qc/submit", [
            'fields' => $payload,
        ])->assertStatus(400)->assertJsonStructure(['errors' => ['qc_notes']]);
    }

    public function test_qc_not_ok_with_notes_is_accepted_and_flagged(): void
    {
        $production = $this->productionUser();
        $qc = $this->qcUser();
        $record = $this->createRecord($production, 'PREMIX');

        $payload = $this->premixQcPayload();
        $payload['qc_sensory'] = 'NOT_OK';
        $payload['qc_notes'] = 'Sensory deviation, hold for review.';

        app(ReceivingInspectionService::class)->submitSection($qc, $record, 'QC', $payload);

        $record->refresh();
        $this->assertSame('SUBMITTED', $record->qc_status);
        $this->assertTrue($record->has_qc_finding);
    }

    public function test_shoestring_qc_requires_at_least_one_defect_when_no_defects_is_false(): void
    {
        $production = $this->productionUser();
        $qc = $this->qcUser();
        $record = $this->createRecord($production, 'SHOESTRING');

        Sanctum::actingAs($qc);
        $this->postJson("/api/inspections/{$record->uuid}/qc/submit", [
            'fields' => $this->shoestringQcPayload(),
            'defects' => [],
        ])->assertStatus(400);
    }

    public function test_shoestring_qc_defects_are_persisted_and_replace_previous_rows(): void
    {
        $production = $this->productionUser();
        $qc = $this->qcUser();
        $record = $this->createRecord($production, 'SHOESTRING');

        app(ReceivingInspectionService::class)->updateSection($qc, $record, 'QC', $this->shoestringQcPayload(), [
            ['defect_type' => 'Rot', 'quantity' => 2],
            ['defect_type' => 'Discoloration', 'quantity' => 1],
        ]);

        $this->assertSame(2, $record->defects()->count());
        $this->assertTrue($record->fresh()->has_qc_finding);

        app(ReceivingInspectionService::class)->updateSection($qc, $record->fresh(), 'QC', $this->shoestringQcPayload(), [
            ['defect_type' => 'Foreign matter', 'quantity' => 3],
        ]);

        $this->assertSame(1, $record->fresh()->defects()->count());
        $this->assertDatabaseHas('shoestring_qc_defects', [
            'receiving_inspection_id' => $record->id,
            'defect_type' => 'Foreign matter',
            'quantity' => 3,
        ]);
    }
}
