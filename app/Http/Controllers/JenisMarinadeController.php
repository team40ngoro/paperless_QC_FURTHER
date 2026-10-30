<?php

namespace App\Http\Controllers;

use App\Models\JenisMarinade;
use App\Models\Plan;
use App\Models\JenisProduk;
use Illuminate\Http\Request;

class JenisMarinadeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = JenisMarinade::with(['plan', 'user', 'jenisProduk']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $jenis_marinade = $query->get();
        return view('super-admin.jenis_marinade.index', compact('jenis_marinade'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->role == 'superadmin') {
            $plans = Plan::all();
            $jenis_produk = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $jenis_produk = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        return view('super-admin.jenis_marinade.create', compact('plans', 'jenis_produk'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'jenis_marinade' => 'required|string|max:255',
        ]);

        // Set user_id via controller as requested
        $data['user_id'] = auth()->id();
        
        JenisMarinade::create($data);

        return redirect()->route('jenis-marinade.index')->with('success', 'Jenis marinade berhasil ditambahkan');
    }

    public function show($uuid)
    {
        $jenis_marinade = JenisMarinade::with(['plan', 'user', 'jenisProduk'])
        ->where('uuid', $uuid)
        ->firstOrFail();
        return view('super-admin.jenis_marinade.show', compact('jenis_marinade'));
    }

    public function edit($uuid)
    {
        $jenis_marinade = JenisMarinade::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        if ($user->role == 'superadmin') {
            $plans = Plan::all();
            $jenis_produk = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $jenis_produk = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        return view('super-admin.jenis_marinade.edit', compact('jenis_marinade', 'plans', 'jenis_produk'));
    }

    public function update(Request $request, $uuid)
    {
        $jenis_marinade = JenisMarinade::where('uuid', $uuid)->firstOrFail();
        
        $data = $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'jenis_marinade' => 'required|string|max:255',
        ]);

        // Keep the original user_id, don't update it
        $jenis_marinade->update($data);

        return redirect()->route('jenis-marinade.index')->with('success', 'Jenis marinade berhasil diperbarui');
    }

    public function destroy($uuid)
    {
        $jenis_marinade = JenisMarinade::where('uuid', $uuid)->firstOrFail();
        $jenis_marinade->delete();

        return redirect()->route('jenis-marinade.index')->with('success', 'Jenis marinade berhasil dihapus');
    }
}
