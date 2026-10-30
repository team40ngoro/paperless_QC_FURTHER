<?php

namespace App\Http\Controllers;

use App\Models\StdSalinitasViskositas;
use App\Models\Plan;
use App\Models\JenisProduk;
use App\Models\JenisBetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StdSalinitasViskositasController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = StdSalinitasViskositas::with(['user', 'plan', 'produk', 'better']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $data = $query->get();
        return view('super-admin.std_salinitas_viskositas.index', compact('data'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
            $betters = JenisBetter::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $betters = JenisBetter::whereHas('produk', function ($query) use ($user) {
                $query->where('id_plan', $user->id_plan);
            })->get();
        }
        return view('super-admin.std_salinitas_viskositas.create', compact('plans', 'produks', 'betters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_better' => 'required|exists:jenis_better,id',
            'std_viskositas' => 'required|string',
            'std_salinitas' => 'required|string',
            'std_suhu_akhir' => 'required|string',
        ]);

        $user = Auth::user();
        if ($user->role !== 'superadmin') {
            $data['id_plan'] = $user->id_plan;
        }
        $data['user_id'] = $user->id; // otomatis user login

        StdSalinitasViskositas::create($data);
        return redirect()->route('std-salinitas-viskositas.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit($uuid)
    {
        $item = StdSalinitasViskositas::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'admin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
            $betters = JenisBetter::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $betters = JenisBetter::whereHas('produk', function ($query) use ($user) {
                $query->where('id_plan', $user->id_plan);
            })->get();
        }
        return view('super-admin.std_salinitas_viskositas.edit', compact('item', 'plans', 'produks', 'betters'));
    }

    public function update(Request $request, $uuid)
    {
        $item = StdSalinitasViskositas::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui data ini.');
        }

        $data = $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_better' => 'required|exists:jenis_better,id',
            'std_viskositas' => 'required|string',
            'std_salinitas' => 'required|string',
            'std_suhu_akhir' => 'required|string',
        ]);

        if ($user->role !== 'superadmin') {
            $data['id_plan'] = $user->id_plan;
        }

        $item->update($data);
        return redirect()->route('std-salinitas-viskositas.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $item = StdSalinitasViskositas::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $item->delete();
        return redirect()->route('std-salinitas-viskositas.index')->with('success', 'Data berhasil dihapus');
    }
    
}
