<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Plan;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat akun user untuk masing-masing role yang ada.
     *
     * @return void
     */
    public function run()
    {
        $firstPlan = Plan::first();
        $allPlanIds = Plan::pluck('id')->toArray();

        $usersData = [
            [
                'role_name'  => 'superadmin',
                'name'       => 'Super Admin',
                'username'   => 'superadmin',
                'email'      => 'superadmin@example.com',
                'password'   => 'superadmin123',
                'id_plan'    => null,
                'is_manager' => false,
            ],
            [
                'role_name'  => 'admin',
                'name'       => 'Admin Plan',
                'username'   => 'admin',
                'email'      => 'admin@example.com',
                'password'   => 'admin123',
                'id_plan'    => $firstPlan ? $firstPlan->id : null,
                'is_manager' => false,
            ],
            [
                'role_name'  => 'produksi',
                'name'       => 'User Produksi',
                'username'   => 'produksi',
                'email'      => 'produksi@example.com',
                'password'   => 'produksi123',
                'id_plan'    => $firstPlan ? $firstPlan->id : null,
                'is_manager' => false,
            ],
            [
                'role_name'  => 'QC Inspector',
                'name'       => 'QC Inspector User',
                'username'   => 'qcinspector',
                'email'      => 'qcinspector@example.com',
                'password'   => 'qcinspector123',
                'id_plan'    => $firstPlan ? $firstPlan->id : null,
                'is_manager' => false,
            ],
            [
                'role_name'  => 'SPV QC',
                'name'       => 'Supervisor QC',
                'username'   => 'spvqc',
                'email'      => 'spvqc@example.com',
                'password'   => 'spvqc123',
                'id_plan'    => $firstPlan ? $firstPlan->id : null,
                'is_manager' => false,
            ],
            [
                'role_name'  => 'AUDITOR',
                'name'       => 'Auditor User',
                'username'   => 'auditor',
                'email'      => 'auditor@example.com',
                'password'   => 'auditor123',
                'id_plan'    => $firstPlan ? $firstPlan->id : null,
                'is_manager' => false,
            ],
            [
                'role_name'  => 'MANAGER',
                'name'       => 'Manager User',
                'username'   => 'manager',
                'email'      => 'manager@example.com',
                'password'   => 'manager123',
                'id_plan'    => $firstPlan ? $firstPlan->id : null,
                'is_manager' => true,
            ],
        ];

        $tableData = [];

        foreach ($usersData as $item) {
            // Temukan atau buat role
            $role = Role::whereRaw('LOWER(role) = ?', [strtolower($item['role_name'])])->first();
            if (!$role) {
                $role = Role::create([
                    'role' => $item['role_name'],
                ]);
            }

            // Temukan user jika sudah ada (termasuk trashed)
            $user = User::withTrashed()
                ->where('username', $item['username'])
                ->orWhere('email', $item['email'])
                ->first();

            $userData = [
                'name'              => $item['name'],
                'username'          => $item['username'],
                'email'             => $item['email'],
                'password'          => Hash::make($item['password']),
                'role'              => $role->role,
                'id_role'           => $role->id,
                'id_plan'           => $item['id_plan'],
                'email_verified_at' => now(),
            ];

            if ($item['is_manager']) {
                $userData['active_plan_id'] = $item['id_plan'];
            }

            if ($user) {
                if ($user->trashed()) {
                    $user->restore();
                }
                $user->update($userData);
            } else {
                $user = User::create($userData);
            }

            // Jika user manager, assign semua plan yang ada agar bisa switch plan
            if ($item['is_manager'] && count($allPlanIds) > 0) {
                $user->allowedPlans()->sync($allPlanIds);
            }

            $planName = $user->plan ? $user->plan->nama_plan : ($item['role_name'] === 'superadmin' ? 'Semua Plan (Akses Penuh)' : '-');

            $tableData[] = [
                $user->name,
                $user->username,
                $user->email,
                $item['password'],
                $role->role,
                $planName,
            ];
        }

        $this->command->info('✅ Berhasil membuat user untuk setiap role!');
        $this->command->table(
            ['Nama', 'Username', 'Email', 'Password', 'Role', 'Plan'],
            $tableData
        );
    }
}
