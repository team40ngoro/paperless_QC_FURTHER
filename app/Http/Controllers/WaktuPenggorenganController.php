<?php
namespace App\Http\Controllers;

use App\Models\WaktuPenggorengan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Plan;
use App\Models\JenisProduk;
use App\Models\SuhuFrayer1;
use Illuminate\Support\Facades\Auth;

class WaktuPenggorenganController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = WaktuPenggorengan::with(['user', 'produk', 'plan', 'suhuFrayer']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $data = $query->get();
        return view('super-admin.waktu_penggorengan.index', compact('data'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        $suhuFrayers = [];

        return view('super-admin.waktu_penggorengan.create', compact('plans', 'produks', 'suhuFrayers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_suhu_frayer_1' => 'required|exists:suhu_frayer_1,id',
            'waktu_penggorengan' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $data = $request->all();

        if ($user->role !== 'superadmin') {
            $data['id_plan'] = $user->id_plan;
        }

        $data['user_id'] = $user->id;
        $data['uuid'] = Str::uuid();

        WaktuPenggorengan::create($data);

        return redirect()->route('waktu-penggorengan.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit($uuid)
    {
        $data = WaktuPenggorengan::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $data->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
            $suhuFrayers = SuhuFrayer1::where('id_produk', $data->id_produk)->get();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $suhuFrayers = SuhuFrayer1::where('id_plan', $user->id_plan)->where('id_produk', $data->id_produk)->get();
        }
        return view('super-admin.waktu_penggorengan.edit', compact('data', 'plans', 'produks', 'suhuFrayers'));
    }

    public function update(Request $request, $uuid)
    {
        $data = WaktuPenggorengan::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $data->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui data ini.');
        }

        $validated = $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_suhu_frayer_1' => 'required|exists:suhu_frayer_1,id',
            'waktu_penggorengan' => 'required|string|max:255',
        ]);

        if ($user->role !== 'superadmin') {
            $validated['id_plan'] = $user->id_plan;
        }

        $data->update($validated);
        return redirect()->route('waktu-penggorengan.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $data = WaktuPenggorengan::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $data->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }
        $data->delete();
        return redirect()->route('waktu-penggorengan.index')->with('success', 'Data berhasil dihapus');
    }

    /**
     * AJAX endpoint to get Suhu Frayer by Product ID
     */
    public function getSuhuFrayerByProduk($id_produk)
    {
        $user = Auth::user();
        
        $query = SuhuFrayer1::where('id_produk', $id_produk);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        $suhuFrayers = $query->get(['id', 'suhu_frayer_1']);
        
        return response()->json($suhuFrayers);
    }
}