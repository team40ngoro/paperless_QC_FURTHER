<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Plan;
use Carbon\Carbon;



class ApiController extends Controller
{
    public function syncUser(Request $request)
    {
        try {
            $data = $request->json()->all();


            if (empty($data['user'])) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid payload'
                ], 400);
            }


            $user = $data['user'];


            if (empty($user['department']['plant'])) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Missing department or plant information'
                ], 400);
            }


         


            $role = $this->resolveRole($user['project_role']['role'] ?? null);

            $userData = [
                'name'            => $user['name'] ?? null,
                'email'           => $user['email'] ?? null,
                'username'        => $user['username'] ?? null,
                'password'        => $user['password'] ?? null,
                'id_plan'         => Plan::where('nama_plan', 'like', '%' . $user['department']['plant'] . '%')->value('id'),
                'id_role'         => $role->id ?? null,
                'role'            => $role->role ?? null,

            ];
            // Set email_verified_at based on activation status
            $activation = $user['activation'] ?? false;
            
            // Handle various activation value types
            if ($activation === 1 || $activation === '1' || $activation === true || $activation === 'true' || $activation === 'True') {
                $userData['email_verified_at'] = Carbon::now()->format('Y-m-d H:i:s');
            } else {
                $userData['email_verified_at'] = null;   
            }
           

            $existingUser = User::withTrashed()->where('uuid', $user['uuid'])->first();


            if ($existingUser) {
                if ($existingUser->trashed()) {
                    $existingUser->restore();
                }
                $existingUser->update($userData);
                // if (!empty($user['project_role']['role'])) {
                //     $existingUser->assignRole($user['project_role']['role']);
                // }
            } 
            else {
                 $newUser = User::create(array_merge(['uuid' => $user['uuid']], $userData));
                // if (!empty($user['project_role']['role'])) {
                //     $newUser->assignRole($user['project_role']['role']);
                // }
            }


            return response()->json([
                'status'  => 'success',
                'message' => 'User synced successfully: ' . $user['uuid']
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage() // only the message, no trace/HTML
            ], 200);
        }
    }


    public function desyncUser(Request $request)
    {
        try {
            $data = $request->json()->all();


            if (empty($data['user_uuid'])) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid payload'
                ], 400);
            }


            $userUuid = $data['user_uuid'];
            $user = User::where('uuid', $userUuid)->first();


            if (!$user) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'User not found'
                ], 404);
            }


            $deleted = $user->delete();


            if ($deleted) {
                return response()->json([
                    'status'  => 'success',
                    'message' => "User desynced successfully: {$userUuid}"
                ]);
            }


            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to desync user'
            ], 500);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 200);
        }
    }

    public function passwordChange(Request $request)
    {
        try {
            $data = $request->json()->all();


            if (empty($data['user'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid Payload'
                ], 400);
            }


            $userData = $data['user'];


            $user = User::firstWhere('uuid', $userData['uuid']);


            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'user tidak ditemukan'
                ], 400);
            }


            $user->update([
                'password' => $userData['password']
            ]);


            return response()->json([
                'status' => 'success',
                'message' => 'Password Change Success'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 200);
        }
    }

    public function activation(Request $request)
    {
        try {
            $data = $request->json()->all();


            if (empty($data['user'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid Payload'
                ], 400);
            }


            $userData = $data['user'];


            $user = User::firstWhere('uuid', $userData['uuid']);


            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'user tidak ditemukan'
                ], 400);
            }


            $user->email_verified_at = Carbon::now()->format('Y-m-d H:i:s');
            $user->save();


            return response()->json([
                'status' => 'success',
                'message' => 'User Activation Success'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 200);
        }
    }

    public function syncPlant(Request $request)
    {
        try {
            $data = $request->json()->all();

            if (empty($data['plant'])) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid payload'
                ], 400);
            }

            $plantData = $data['plant'];

            // Upsert by uuid — more reliable than a LIKE name search
            $plant = Plan::where('uuid', $plantData['uuid'])
                ->orWhere('nama_plan', $plantData['plant'])
                ->first();

            if (!$plant) {
                Plan::create([
                    'uuid'        => $plantData['uuid'],
                    'nama_plan'   => $plantData['plant'],
                    'abbrivation' => $plantData['abbrivation'] ?? null,
                ]);
            } else {
                $plant->update([
                    'nama_plan'   => $plantData['plant'],
                    'abbrivation' => $plantData['abbrivation'] ?? $plant->abbrivation,
                ]);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Plant synced successfully'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 200);
        }
    }
    


    /**
     * Resolve role dari SSO ke role lokal.
     */
    private function resolveRole(?string $roleName): ?Role
    {
        if (empty($roleName)) {
            return null;
        }

        $normalized = strtolower(trim($roleName));

        $roleMapping = [
            'admin'         => 'admin',
            'fm/fl'         => 'produksi',
            'produksi'      => 'produksi',
            'produksi spv'  => 'Produksi SPV',
            'qc'            => 'QC Inspector',
            'qc inspector'  => 'QC Inspector',
            'spv'           => 'SPV QC',
            'spv qc'        => 'SPV QC',
        ];

        $targetRole = $roleMapping[$normalized] ?? $roleName;

        $role = Role::whereRaw('LOWER(role) = ?', [strtolower($targetRole)])->first();

        if (! $role && $targetRole === 'Produksi SPV') {
            $role = Role::firstOrCreate(['role' => 'Produksi SPV']);
        }

        return $role;
    }
}
