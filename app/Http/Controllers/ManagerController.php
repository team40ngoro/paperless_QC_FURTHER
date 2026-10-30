<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    /**
     * Pindah ke plan lain (Switch Plant)
     * Hanya bisa dilakukan oleh Manager yang punya akses ke plan tersebut
     */
    public function switchPlan(Request $request)
    {
        $user = Auth::user();

        if (!$user->isManager()) {
            abort(403, 'Hanya Manager yang dapat melakukan switch plan.');
        }

        $request->validate([
            'plan_id' => 'required|exists:plan,id',
        ]);

        $planId = (int) $request->plan_id;

        if (!$user->canAccessPlan($planId)) {
            return redirect()->back()->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses plan ini.');
        }

        $plan = Plan::findOrFail($planId);
        $user->update(['active_plan_id' => $plan->id]);

        return redirect()->back()->with('success', "Berhasil berpindah ke Plan: {$plan->nama_plan}");
    }

    /**
     * Kembali ke plan asal (Reset Plant)
     */
    public function resetPlan(Request $request)
    {
        $user = Auth::user();

        if (!$user->isManager()) {
            abort(403, 'Hanya Manager yang dapat melakukan reset plan.');
        }

        $user->update(['active_plan_id' => null]);

        return redirect()->back()->with('success', 'Berhasil kembali ke Plan asal.');
    }
}
