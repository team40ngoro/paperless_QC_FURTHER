<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::all();
        return view('super-admin.plan.index', compact('plans'));
    }

    public function create()
    {
        return view('super-admin.plan.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_plan' => 'required|string|max:255',
        ]);
        $data['user_id'] = auth()->id();
        Plan::create($data);
        return redirect()->route('plan.index')->with('success', 'Plan berhasil ditambahkan');
    }

    public function edit($uuid)
    {
        $plan = Plan::where('uuid', $uuid)->firstOrFail();
        return view('super-admin.plan.edit', compact('plan'));
    }

    public function update(Request $request, $uuid)
    {
        $plan = Plan::where('uuid', $uuid)->firstOrFail();
        $data = $request->validate([
            'nama_plan' => 'required|string|max:255',
        ]);
        $plan->update($data);
        return redirect()->route('plan.index')->with('success', 'Plan berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $plan = Plan::where('uuid', $uuid)->firstOrFail();
        $plan->delete();
        return redirect()->route('plan.index')->with('success', 'Plan berhasil dihapus');
    }

    public function profile()
    {
        $users = User::with('plan')->get();
        $plans = Plan::all();
        return view('super-admin.profile', compact('users', 'plans'));
    }
    
public function get_by_plant_name($plant_name)
{
    $plan = Plan::where('nama_plan', 'like', '%' . $plant_name . '%')->first();
    return $plan ? $plan->id : null;
}
}
