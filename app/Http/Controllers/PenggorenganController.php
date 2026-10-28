<?php
namespace App\Http\Controllers;

use App\Models\Penggorengan;
use App\Models\PenggorenganLog;
use App\Models\JenisProduk;
use App\Models\DataShift;
use App\Models\PembuatanPredust;
use App\Models\ProsesBattering;
use App\Models\ProsesBreader;
use App\Models\ProsesFrayer;
use App\Models\HasilPenggorengan;
use App\Models\PembekuanIqfPenggorengan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PenggorenganController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $query = Penggorengan::with(['produk', 'shift', 'plan', 'user'])
                             ->withCount(['pembuatanPredust', 'prosesBattering']);
                             
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $search = request('search');

        if ($search) {
            $query->where(function($q) use ($search) {
                // Cari berdasarkan kode produksi
                $q->where('kode_produksi', 'LIKE', '%' . $search . '%')
                  // Cari berdasarkan relasi nama produk
                  ->orWhereHas('produk', function($qProd) use ($search) {
                      $qProd->where('nama_produk', 'LIKE', '%' . $search . '%');
                  });
            });
        }
        
        $data = $query->orderBy('tanggal', 'desc')->orderBy('jam', 'desc')->paginate(10);
        return view('qc-sistem.proses_penggorengan.index', compact('data'));
    }
    public function create()
    {
        $user = auth()->user();
        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
            $produks = JenisProduk::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        return view('qc-sistem.proses_penggorengan.create', compact('produks', 'shifts'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'shift_id' => 'required|exists:data_shift,id',
            'kode_produksi' => 'required|string',
            'berat_produk' => 'required|string|max:255',
            'no_of_strokes' => 'required|string',
            'waktu_pemasakan' => 'required|string',
            'jam' => 'required|string',
            // 'waktu_selesai_pemasakan' => 'nullable|string',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'hasil_pencetakan' => 'required|string',
        ]);
        Penggorengan::create([
            'uuid' => Str::uuid(),
            'id_produk' => $request->id_produk,
            'shift_id' => $request->shift_id,
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'kode_produksi' => $request->kode_produksi,
            'berat_produk' => $request->berat_produk,
            'no_of_strokes' => $request->no_of_strokes,
            'waktu_pemasakan' => $request->waktu_pemasakan,
            'jam' => $request->jam,
            // 'waktu_selesai_pemasakan' => $request->waktu_selesai_pemasakan,
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
            'hasil_pencetakan' => $request->hasil_pencetakan,
        ]);
        return redirect()->route('penggorengan.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit($uuid)
    {
        $user = auth()->user();
        $item = Penggorengan::where('uuid', $uuid)->firstOrFail();
        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
            $produks = JenisProduk::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        return view('qc-sistem.proses_penggorengan.edit', compact('item', 'produks', 'shifts'));
    }

    public function update(Request $request, $uuid)
    {
        $user = auth()->user();
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'shift_id' => 'required|exists:data_shift,id',
            'kode_produksi' => 'required|string',
            'berat_produk' => 'required|string|max:255',
            'no_of_strokes' => 'required|string',
            'waktu_pemasakan' => 'required|string',
            // 'waktu_selesai_pemasakan' => 'nullable|string',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'hasil_pencetakan' => 'required|string',
        ]);
        $item = Penggorengan::where('uuid', $uuid)->firstOrFail();
        
        // Update akan otomatis trigger observer untuk logging
        $item->update([
            'id_produk' => $request->id_produk,
            'shift_id' => $request->shift_id,
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'kode_produksi' => $request->kode_produksi,
            'berat_produk' => $request->berat_produk,
            'no_of_strokes' => $request->no_of_strokes,
            'waktu_pemasakan' => $request->waktu_pemasakan,
            // 'waktu_selesai_pemasakan' => $request->waktu_selesai_pemasakan,
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
            'hasil_pencetakan' => $request->hasil_pencetakan,
        ]);
        return redirect()->route('penggorengan.index')->with('success', 'Data berhasil diupdate');
    }

    public function showLogs($uuid)
    {
        $penggorengan = Penggorengan::where('uuid', $uuid)->firstOrFail();
        $logs = PenggorenganLog::where('penggorengan_id', $penggorengan->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('qc-sistem.proses_penggorengan.logs', compact('penggorengan', 'logs'));
    }

    public function destroy($uuid)
    {
        $item = Penggorengan::where('uuid', $uuid)->firstOrFail();

        $isReferenced = PembuatanPredust::where('penggorengan_uuid', $uuid)->exists()
            || ProsesBattering::where('penggorengan_uuid', $uuid)->exists()
            || ProsesBreader::where('penggorengan_uuid', $uuid)->exists()
            || ProsesFrayer::where('penggorengan_uuid', $uuid)->exists()
            || HasilPenggorengan::where('penggorengan_uuid', $uuid)->exists()
            || PembekuanIqfPenggorengan::where('penggorengan_uuid', $uuid)->exists();

        if ($isReferenced) {
            return redirect()->route('penggorengan.index')
                ->with('error', 'Data tidak dapat dihapus karena sudah terhubung dengan proses selanjutnya.');
        }

        $item->delete();
        return redirect()->route('penggorengan.index')->with('success', 'Data berhasil dihapus');
    }
}