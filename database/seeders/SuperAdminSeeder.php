<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Buat atau temukan role superadmin
        $role = Role::firstOrCreate(
            ['role' => 'superadmin'],
        );

        // Buat atau update user super admin
        $user = User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name'              => 'Super Admin',
                'email'             => 'superadmin@example.com',
                'password'          => Hash::make('superadmin123'),
                'role'              => 'superadmin',
                'id_role'           => $role->id,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Super Admin berhasil dibuat!');
        $this->command->table(
            ['Field', 'Value'],
            [
                ['Name',     $user->name],
                ['Username', $user->username],
                ['Email',    $user->email],
                ['Password', 'superadmin123'],
                ['Role',     $role->role],
            ]
        );
    }
}
