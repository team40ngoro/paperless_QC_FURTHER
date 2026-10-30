<?php

namespace App\Http\Controllers;

use App\Models\SuhuBlok;
use App\Models\Plan;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuhuBlokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = SuhuBlok::with(['plan', 'user', 'produk']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $suhuBlok = $query->get();
        return view('super-admin.suhu_blok.index', compact('suhuBlok'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $products = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        return view('super-admin.suhu_blok.create', compact('plans', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'suhu_blok' => 'required|string|max:255',
        ]);

        $data = $request->all();
        $user = Auth::user();

        if ($user->role !== 'superadmin') {
            $data['id_plan'] = $user->id_plan;
        }
        $data['user_id'] = $user->id;

        SuhuBlok::create($data);

        return redirect()->route('suhu-blok.index')
                        ->with('success', 'Data Suhu Blok berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        $suhuBlok = SuhuBlok::where('uuid', $uuid)->with(['plan', 'user', 'produk'])->firstOrFail();
        $user = Auth::user();
        if ($user->role !== 'superadmin' && $suhuBlok->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }
        return view('super-admin.suhu_blok.show', compact('suhuBlok'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        $suhuBlok = SuhuBlok::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        if ($user->role !== 'superadmin' && $suhuBlok->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $products = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        return view('super-admin.suhu_blok.edit', compact('suhuBlok', 'plans', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        $suhuBlok = SuhuBlok::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        if ($user->role !== 'superadmin' && $suhuBlok->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui data ini.');
        }

        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'suhu_blok' => 'required|string|max:255',
        ]);
        
        $data = $request->only(['id_plan', 'id_produk', 'suhu_blok']);
        if ($user->role !== 'admin') {
            $data['id_plan'] = $user->id_plan;
        }
        
        $suhuBlok->update($data);

        return redirect()->route('suhu-blok.index')
                        ->with('success', 'Data Suhu Blok berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        $suhuBlok = SuhuBlok::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        if ($user->role !== 'superadmin' && $suhuBlok->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }
        $suhuBlok->delete();

        return redirect()->route('suhu-blok.index')
                        ->with('success', 'Data Suhu Blok berhasil dihapus.');
    }
}
