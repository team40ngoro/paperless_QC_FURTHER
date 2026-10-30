<?php

namespace App\Http\Controllers;

use App\Models\StdSuhuPusatRoasting;
use App\Models\Plan;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StdSuhuPusatRoastingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $data = StdSuhuPusatRoasting::with(['plan', 'produk'])->get();
        } else {
            $data = StdSuhuPusatRoasting::with(['plan', 'produk'])
                ->where('id_plan', $user->id_plan)
                ->get();
        }

        return view('super-admin.std_suhu_pusat_roasting.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $products = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('super-admin.std_suhu_pusat_roasting.create', compact('plans', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'std_suhu_pusat_roasting' => 'required|string|max:255',
        ]);

        StdSuhuPusatRoasting::create([
            'uuid' => Str::uuid(),
            'id_plan' => $request->id_plan,
            'id_produk' => $request->id_produk,
            'std_suhu_pusat_roasting' => $request->std_suhu_pusat_roasting,
        ]);

        return redirect()->route('std-suhu-pusat-roasting.index')
            ->with('success', 'Data Standar Suhu Pusat Roasting berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $item = StdSuhuPusatRoasting::with(['plan', 'produk'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return view('super-admin.std_suhu_pusat_roasting.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $item = StdSuhuPusatRoasting::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $products = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('super-admin.std_suhu_pusat_roasting.edit', compact('item', 'plans', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'std_suhu_pusat_roasting' => 'required|string|max:255',
        ]);

        $item = StdSuhuPusatRoasting::where('uuid', $uuid)->firstOrFail();
        
        $item->update([
            'id_plan' => $request->id_plan,
            'id_produk' => $request->id_produk,
            'std_suhu_pusat_roasting' => $request->std_suhu_pusat_roasting,
        ]);

        return redirect()->route('std-suhu-pusat-roasting.index')
            ->with('success', 'Data Standar Suhu Pusat Roasting berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $item = StdSuhuPusatRoasting::where('uuid', $uuid)->firstOrFail();
        $item->delete();

        return redirect()->route('std-suhu-pusat-roasting.index')
            ->with('success', 'Data Standar Suhu Pusat Roasting berhasil dihapus.');
    }
}
