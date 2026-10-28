<?php

namespace App\Http\Controllers;

use App\Models\JenisPredust;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JenisPredustController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = JenisPredust::with(['plan', 'user', 'produk']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $jenisPredust = $query->get();
        return view('super-admin.jenis_predust.index', compact('jenisPredust'));
    }

    public function create()
    {
        $user = auth()->user();
        $query = JenisProduk::query();

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $produks = $query->get();
        return view('super-admin.jenis_predust.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'jenis_predust' => 'required|string|max:255',
        ]);

        JenisPredust::create([
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'id_produk' => $request->id_produk,
            'jenis_predust' => $request->jenis_predust,
        ]);

        return redirect()->route('jenis-predust.index')
            ->with('success', 'Data jenis predust berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $data = JenisPredust::where('uuid', $uuid)->firstOrFail();
        return view('super-admin.jenis_predust.show', compact('data'));
    }

    public function edit($uuid)
    {
        $data = JenisPredust::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        $query = JenisProduk::query();

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $produks = $query->get();
        return view('super-admin.jenis_predust.edit', compact('data', 'produks'));
    }

    public function update(Request $request, $uuid)
    {
        $data = JenisPredust::where('uuid', $uuid)->firstOrFail();
        
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'jenis_predust' => 'required|string|max:255',
        ]);

        $data->update([
            'id_produk' => $request->id_produk,
            'jenis_predust' => $request->jenis_predust,
        ]);

        return redirect()->route('jenis-predust.index')
            ->with('success', 'Data jenis predust berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $data = JenisPredust::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Check authorization - only superadmin or user from same plan can delete
        if ($user->role !== 'superadmin' && $data->id_plan !== $user->id_plan) {
            return redirect()->route('jenis-predust.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $data->delete();

        return redirect()->route('jenis-predust.index')
            ->with('success', 'Data jenis predust berhasil dihapus.');
    }
}
