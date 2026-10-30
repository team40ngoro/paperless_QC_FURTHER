<?php

namespace App\Http\Controllers;

use App\Models\WaktuPenggorengan2;
use App\Models\JenisProduk;
use App\Models\SuhuFrayer2;
use App\Models\SuhuFrayer1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaktuPenggorengan2Controller extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = WaktuPenggorengan2::with(['produk', 'plan', 'user', 'suhuFrayer2']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $data = $query->paginate(10);
        return view('super-admin.waktu_penggorengan_2.index', compact('data'));
    }

    public function create()
    {
        $user = auth()->user();
        $query = JenisProduk::query();

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $products = $query->get();
        return view('super-admin.waktu_penggorengan_2.create', compact('products'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_suhu_frayer_2' => 'required|exists:suhu_frayer_2,id',
            'waktu_penggorengan_2' => 'required|string|max:255',
        ]);

        WaktuPenggorengan2::create([
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'id_produk' => $request->id_produk,
            'id_suhu_frayer_2' => $request->id_suhu_frayer_2,
            'waktu_penggorengan_2' => $request->waktu_penggorengan_2,
        ]);

        return redirect()->route('waktu-penggorengan-2.index')
            ->with('success', 'Data waktu penggorengan 2 berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $data = WaktuPenggorengan2::where('uuid', $uuid)->firstOrFail();
        return view('super-admin.waktu_penggorengan_2.show', compact('data'));
    }

    public function edit($uuid)
    {
        $data = WaktuPenggorengan2::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        $query = JenisProduk::query();

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $products = $query->get();
        
        // Get suhu frayer 2 data for the selected product
        $suhuFrayer2Options = SuhuFrayer2::where('id_produk', $data->id_produk)
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            })
            ->get();

        return view('super-admin.waktu_penggorengan_2.edit', compact('data', 'products', 'suhuFrayer2Options'));
    }

    public function update(Request $request, $uuid)
    {
        $data = WaktuPenggorengan2::where('uuid', $uuid)->firstOrFail();
        
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_suhu_frayer_2' => 'required|exists:suhu_frayer_2,id',
            'waktu_penggorengan_2' => 'required|string|max:255',
        ]);

        $data->update([
            'id_produk' => $request->id_produk,
            'id_suhu_frayer_2' => $request->id_suhu_frayer_2,
            'waktu_penggorengan_2' => $request->waktu_penggorengan_2,
        ]);

        return redirect()->route('waktu-penggorengan-2.index')
            ->with('success', 'Data waktu penggorengan 2 berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $data = WaktuPenggorengan2::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Check authorization - only superadmin or user from same plan can delete
        if ($user->role !== 'superadmin' && $data->id_plan !== $user->id_plan) {
            return redirect()->route('waktu-penggorengan-2.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $data->delete();

        return redirect()->route('waktu-penggorengan-2.index')
            ->with('success', 'Data waktu penggorengan 2 berhasil dihapus.');
    }

    // AJAX method to get suhu frayer 2 by product
    public function getSuhuFrayer2ByProduk($id_produk)
    {
        $user = auth()->user();
        $query = SuhuFrayer1::where('id_produk', $id_produk)->whereNotNull('suhu_frayer_2');

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        return response()->json($query->get(['id', 'suhu_frayer_2']));
    }

    // AJAX method to get waktu penggorengan 2 by suhu frayer 2
    public function getWaktuPenggorengan2BySuhu($id_suhu)
    {
        $user = auth()->user();
        $suhu = SuhuFrayer1::find($id_suhu) ?? SuhuFrayer2::find($id_suhu);
        
        if (!$suhu) {
            return response()->json([]);
        }

        if ($user->role !== 'superadmin' && $suhu->id_plan !== $user->id_plan) {
            return response()->json([], 403);
        }

        $waktu = trim((string) ($suhu->waktu_penggorengan_2 ?? ''));
        if ($waktu === '') {
            return response()->json([]);
        }

        // Pastikan record di waktu_penggorengan_2 tersinkron dengan master suhu_frayer_1
        $waktuPenggorengan2 = WaktuPenggorengan2::updateOrCreate(
            [
                'id_suhu_frayer_2' => $suhu->id,
            ],
            [
                'id_plan' => $suhu->id_plan,
                'user_id' => $user ? $user->id : null,
                'id_produk' => $suhu->id_produk,
                'waktu_penggorengan_2' => $waktu,
            ]
        );

        return response()->json([
            [
                'id' => $waktuPenggorengan2->id,
                'waktu_penggorengan_2' => $waktuPenggorengan2->waktu_penggorengan_2,
            ]
        ]);
    }
}
