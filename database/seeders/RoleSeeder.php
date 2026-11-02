<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's roles.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            1 => 'admin',
            2 => 'produksi',
            3 => 'QC Inspector',
            4 => 'SPV QC',
            5 => 'superadmin',
            6 => 'AUDITOR',
            7 => 'MANAGER',
        ];

        foreach ($roles as $id => $role) {
            Role::updateOrCreate(['id' => $id], ['role' => $role]);
        }
    }
}
