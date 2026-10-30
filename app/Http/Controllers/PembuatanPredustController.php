<?php

namespace App\Http\Controllers;

use App\Models\PembuatanPredust;
use App\Models\PembuatanPredustLog;
use App\Models\JenisProduk;
use App\Models\JenisPredust;
use App\Models\Penggorengan;
use App\Models\ProsesBattering;
use App\Models\ProsesBreader;
use App\Models\ProsesFrayer;
use App\Models\HasilPenggorengan;
use App\Models\PembekuanIqfPenggorengan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembuatanPredustController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = PembuatanPredust::with(['plan', 'user', 'produk', 'jenisPredust']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if (!empty($search)) {
            $query->whereHas('produk', function ($produkQuery) use ($search) {
                $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
            });
        }

        $perPage = request()->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50, 100]) ? $perPage : 10;

        $pembuatanPredust = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return view('qc-sistem.pembuatan_predust.index', compact('pembuatanPredust', 'search', 'perPage'));
    }

    public function create(Request $request)
    {
        $penggorenganData = null;
        
        // Check if coming from Penggorengan
        if ($request->has('penggorengan_uuid')) {
            $penggorenganData = Penggorengan::with(['produk', 'shift'])
                ->where('uuid', $request->penggorengan_uuid)
                ->first();
        }
        
        $jenisProduk = JenisProduk::all();
        $produks = $jenisProduk; // Add alias for consistency with view
        $jenisPredust = JenisPredust::all();
        
        return view('qc-sistem.pembuatan_predust.create', compact('jenisProduk', 'produks', 'jenisPredust', 'penggorenganData'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_jenis_predust' => 'required|exists:jenis_predust,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'kondisi_predust' => 'required|string|max:255',
            'hasil_pencetakan' => 'required|string|in:oke,tidak ok',
            'kode_produksi' => 'required|string|max:255',
        ]);

        PembuatanPredust::create([
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'penggorengan_uuid' => $request->penggorengan_uuid,
            'id_produk' => $request->id_produk,
            'id_jenis_predust' => $request->id_jenis_predust,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'kondisi_predust' => $request->kondisi_predust,
            'hasil_pencetakan' => $request->hasil_pencetakan,
            'kode_produksi' => $request->kode_produksi,
        ]);

        return redirect()->route('pembuatan-predust.index')
            ->with('success', 'Data pembuatan predust berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $pembuatanPredust = PembuatanPredust::where('uuid', $uuid)->firstOrFail();
        return view('qc-sistem.pembuatan_predust.show', compact('pembuatanPredust'));
    }

    public function edit($uuid)
    {
        $pembuatanPredust = PembuatanPredust::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        $query = JenisProduk::query();

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $produks = $query->get();
        
        // Get jenis predust based on selected product
        $jenisPredust = JenisPredust::where('id_produk', $pembuatanPredust->id_produk)->get();
        
        return view('qc-sistem.pembuatan_predust.edit', compact('pembuatanPredust', 'produks', 'jenisPredust'));
    }

    public function update(Request $request, $uuid)
    {
        $pembuatanPredust = PembuatanPredust::where('uuid', $uuid)->firstOrFail();
        $validatedData = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_jenis_predust' => 'required|exists:jenis_predust,id',
            'tanggal' => 'required|date',
            'kondisi_predust' => 'required|string|max:255',
            'hasil_pencetakan' => 'required|string|in:oke,tidak ok',
            'kode_produksi' => 'required|string|max:255',
        ]);

        $pembuatanPredust->update([
            'id_produk' => $request->id_produk,
            'id_jenis_predust' => $request->id_jenis_predust,
            'tanggal' => $request->tanggal,
            'kondisi_predust' => $request->kondisi_predust,
            'hasil_pencetakan' => $request->hasil_pencetakan,
            'kode_produksi' => $request->kode_produksi,
        ]);

        return redirect()->route('pembuatan-predust.index')
            ->with('success', 'Data pembuatan predust berhasil diperbarui.');
    }

    public function showLogs($uuid)
    {
        $pembuatanPredust = PembuatanPredust::where('uuid', $uuid)->firstOrFail();
        $logs = PembuatanPredustLog::where('pembuatan_predust_id', $pembuatanPredust->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('qc-sistem.pembuatan_predust.logs', compact('pembuatanPredust', 'logs'));
    }

    public function destroy($uuid)
    {
        $pembuatanPredust = PembuatanPredust::where('uuid', $uuid)->firstOrFail();

        $isReferenced = ProsesBattering::where('predust_uuid', $uuid)->exists()
            || ProsesBreader::where('predust_uuid', $uuid)->exists()
            || ProsesFrayer::where('predust_uuid', $uuid)->exists()
            || HasilPenggorengan::where('predust_uuid', $uuid)->exists()
            || PembekuanIqfPenggorengan::where('predust_uuid', $uuid)->exists();

        if ($isReferenced) {
            return redirect()->route('pembuatan-predust.index')
                ->with('error', 'Data tidak dapat dihapus karena sudah terhubung dengan proses selanjutnya.');
        }

        $pembuatanPredust->delete();

        return redirect()->route('pembuatan-predust.index')
            ->with('success', 'Data pembuatan predust berhasil dihapus.');
    }

    // AJAX method to get jenis predust by product
    public function getJenisPredustByProduk(Request $request)
    {
        $jenisPredust = JenisPredust::where('id_produk', $request->id_produk)->get();
        return response()->json($jenisPredust);
    }
}
