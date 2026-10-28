<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Plan;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $query = User::with(['plan', 'roleModel', 'allowedPlans']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('username', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%');
            });
        }

        $users = $query->latest()->paginate($perPage);
        $plans = Plan::all();
        $roles = Role::all();

        return view('super-admin.profile', compact('users', 'plans', 'roles', 'search', 'perPage'));
    }

    public function create()
    {
        $plans = Plan::all();
        $roles = Role::all();
        return view('super-admin.user.create', compact('plans', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users',
            'username' => 'required|unique:users',
            'password' => 'required',
            'email' => 'required|email|unique:users',
            'id_role' => 'required|exists:roles,id',
            'id_plan' => 'nullable|exists:plan,id'
        ]);
        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'id_role' => $request->id_role,
            'id_plan' => $request->id_plan
        ]);
        return redirect()->route('users.index')->with('success', 'User berhasil ditambah');
    }

    public function edit(User $user)
    {
        $plans = Plan::all();
        $roles = Role::all();
        return view('super-admin.user.edit', compact('user', 'plans', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|unique:users,name,'.$user->id,
            'username' => 'required|unique:users,username,'.$user->id,
            'email' => 'required|email|unique:users,email,'.$user->id,
            'id_role' => 'required|exists:roles,id',
            'id_plan' => 'nullable|exists:plan,id'
        ]);
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->id_role = $request->id_role;
        $user->id_plan = $request->id_plan;
        if($request->password){
            $user->password = Hash::make($request->password);
        }
        $user->save();
        return redirect()->route('users.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }

    /**
     * Tampilkan form assign plans ke Manager
     */
    public function assignPlans(User $user)
    {
        $plans = Plan::orderBy('nama_plan')->get();
        $assignedPlanIds = $user->allowedPlans()->pluck('plan.id')->toArray();

        return view('super-admin.user.assign-plans', compact('user', 'plans', 'assignedPlanIds'));
    }

    /**
     * Simpan assign plans ke Manager
     */
    public function saveAssignPlans(Request $request, User $user)
    {
        $request->validate([
            'plan_ids'   => 'nullable|array',
            'plan_ids.*' => 'exists:plan,id',
        ]);

        $user->allowedPlans()->sync($request->plan_ids ?? []);

        // Jika plan aktif saat ini tidak lagi ada di daftar yang di-assign, reset
        if ($user->active_plan_id && !in_array($user->active_plan_id, $request->plan_ids ?? [])) {
            $user->update(['active_plan_id' => null]);
        }

        return redirect()->route('users.profile')->with('success', "Akses Plan untuk {$user->name} berhasil diperbarui.");
    }
}
