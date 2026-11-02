<?php

namespace Tests\Feature\Concerns;

use App\Models\ReceivingInspection;
use App\Models\Role;
use App\Models\User;
use App\Services\ReceivingInspection\ReceivingInspectionService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait InteractsWithReceivingInspections
{
    protected function roleUser(int $roleId, string $roleName): User
    {
        Role::firstOrCreate(['id' => $roleId], ['role' => $roleName]);

        return User::create([
            'name' => 'Test ' . $roleName,
            'username' => 'test_' . Str::random(10),
            'email' => Str::random(10) . '@example.test',
            'password' => Hash::make('password'),
            'id_role' => $roleId,
            'role' => $roleName,
        ]);
    }

    protected function productionUser(): User
    {
        return $this->roleUser(2, 'produksi');
    }

    protected function qcUser(): User
    {
        return $this->roleUser(3, 'QC Inspector');
    }

    protected function spvQcUser(): User
    {
        return $this->roleUser(4, 'SPV QC');
    }

    protected function adminUser(): User
    {
        return $this->roleUser(1, 'admin');
    }

    protected function createRecord(User $user, string $area = 'CHILLROOM', string $date = '2026-10-30'): ReceivingInspection
    {
        return app(ReceivingInspectionService::class)->create($user, [
            'area' => $area,
            'inspection_date' => $date,
        ]);
    }

    protected function chillroomProductionPayload(): array
    {
        return [
            'prod_arrival_time' => '08:00',
            'prod_rm_name' => 'Chicken Breast',
            'prod_production_code' => '24A01',
            'prod_weight_per_pack' => 10.5,
            'prod_rm_temp_c' => -18.0,
            'prod_sensory' => 'OK',
            'prod_packaging' => 'OK',
            'prod_rm_condition' => 'FROZEN',
            'prod_notes' => 'ok',
            'prod_is_meat_rm' => false,
        ];
    }

    protected function chillroomQcPayload(): array
    {
        return [
            'qc_rm_name_code_verified' => 'OK',
            'qc_weight_per_pack_verified' => 'OK',
            'qc_rm_temp_c' => -18.5,
            'qc_sensory' => 'OK',
        ];
    }

    protected function premixProductionPayload(): array
    {
        return [
            'prod_check_time' => '08:00',
            'prod_rm_name' => 'Flour',
            'prod_production_code' => 'P-01',
            'prod_weight_per_pack' => 25,
            'prod_shift' => 'SHIFT_1',
            'prod_sensory' => 'OK',
            'prod_packaging' => 'OK',
        ];
    }

    protected function premixQcPayload(): array
    {
        return [
            'qc_identity_weight_verified' => 'OK',
            'qc_sensory' => 'OK',
            'qc_packaging' => 'OK',
        ];
    }

    protected function shoestringProductionPayload(): array
    {
        return [
            'prod_shift' => 'SHIFT_1',
            'prod_production_date' => '2026-10-29',
            'prod_production_time' => '07:00',
            'prod_producer_name' => 'Producer A',
            'prod_production_code' => 'S-01',
            'prod_expiry_date' => '2026-11-30',
            'prod_gramasi' => 500,
        ];
    }

    protected function shoestringQcPayload(): array
    {
        return [
            'qc_producer_name' => 'Producer A',
            'qc_producer_rm_code' => 'RM-01',
            'qc_producer_rm_date' => '2026-10-29',
            'qc_no_defects' => false,
        ];
    }

    protected function kemasanProductionPayload(): array
    {
        return [
            'prod_shift' => 'SHIFT_1',
            'prod_production_code' => 'K-01',
            'prod_producer_name' => 'Producer K',
            'prod_packaging_name' => 'Carton',
            'prod_packaging_condition' => 'Good',
        ];
    }

    protected function kemasanQcPayload(): array
    {
        return [
            'qc_packaging_arrival_verified' => 'OK',
        ];
    }
}
