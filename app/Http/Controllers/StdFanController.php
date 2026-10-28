<?php

namespace App\Http\Controllers;

use App\Models\StdFan;
use App\Models\Plan;
use App\Models\JenisProduk;
use App\Models\SuhuBlok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StdFanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = StdFan::with(['plan', 'produk', 'user', 'suhuBlok']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $stdFan = $query->get();
        return view('super-admin.std_fan.index', compact('stdFan'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'supeadmin') {
            $plans = Plan::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        return view('super-admin.std_fan.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_suhu_blok' => 'required|exists:suhu_blok,id',
            'std_fan' => 'required|string|max:255',
            'std_fan_2' => 'required|string|max:255',
            'std_lama_proses' => 'required|string|max:255',
            'fan_3' => 'nullable|string|max:255',
            'fan_4' => 'nullable|string|max:255',
            'std_humadity' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $user = Auth::user();

        if ($user->role !== 'superadmin') {
            $data['id_plan'] = $user->id_plan;
        }

        $data['user_id'] = $user->id;

        StdFan::create($data);

        return redirect()->route('std-fan.index')
                        ->with('success', 'Data Std Fan berhasil ditambahkan.');
    }

    public function show(StdFan $stdFan)
    {
        $user = Auth::user();
        if ($user->role !== 'superadmin' && $stdFan->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        $stdFan->load(['plan', 'produk', 'user', 'suhuBlok']);
        return view('super-admin.std_fan.show', compact('stdFan'));
    }

    public function edit($uuid)
    {
        $user = Auth::user();
        $stdFan = StdFan::where('uuid', $uuid)->firstOrFail();
        if ($user->role !== 'admin' && $stdFan->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $products = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        
        $suhuBloks = SuhuBlok::where('id_produk', $stdFan->id_produk)->get();
        
        return view('super-admin.std_fan.edit', compact('stdFan', 'plans', 'products', 'suhuBloks'));
    }

    public function update(Request $request, $uuid)
    {
        $user = Auth::user();
        $stdFan = StdFan::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'admin' && $stdFan->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui data ini.');
        }

        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_suhu_blok' => 'required|exists:suhu_blok,id',
            'std_fan' => 'required|string|max:255',
            'std_fan_2' => 'required|string|max:255',
            'std_lama_proses' => 'required|string|max:255',
            'fan_3' => 'nullable|string|max:255',
            'fan_4' => 'nullable|string|max:255',
            'std_humadity' => 'nullable|string|max:255',
        ]);

        $data = $request->except(['user_id']);

        if ($user->role !== 'superadmin') {
            $data['id_plan'] = $user->id_plan;
        }

        $stdFan->update($data);

        return redirect()->route('std-fan.index')
                        ->with('success', 'Data Std Fan berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $user = Auth::user();
        $stdFan = StdFan::where('uuid', $uuid)->firstOrFail();
        if ($user->role !== 'superadmin' && $stdFan->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $stdFan->delete();

        return redirect()->route('std-fan.index')
                        ->with('success', 'Data Std Fan berhasil dihapus.');
    }

    // AJAX endpoints for cascading dropdowns
    public function getProductsByPlan(Request $request)
    {
        $planId = $request->get('plan_id');
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $planId != $user->id_plan) {
            return response()->json([], 403);
        }
        
        // Get products that have suhu_blok records for this plan
        $products = JenisProduk::whereHas('suhuBlok', function($query) use ($planId) {
            $query->where('id_plan', $planId);
        })->get();

        return response()->json($products);
    }

    public function getSuhuBlokByProduct(Request $request)
    {
        $productId = $request->get('product_id');
        $planId = $request->get('plan_id');
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $planId != $user->id_plan) {
            return response()->json([], 403);
        }
        
        $suhuBloks = SuhuBlok::where('id_produk', $productId)
                            ->where('id_plan', $planId)
                            ->with(['plan', 'produk'])
                            ->get();

        return response()->json($suhuBloks);
    }
}
