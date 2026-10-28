<?php

namespace App\Http\Controllers;

use App\Models\DataBag;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\PengemasanProduk;
use App\Models\PengemasanProdukLog;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengemasanProdukController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = PengemasanProduk::with(['plan', 'produk', 'shift'])
            ->withCount('pengemasanPlastik');

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

        $data = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('qc-sistem.pengemasan_produk.index', compact('data', 'search', 'perPage'));
    }

    public function create()
    {
        $user = Auth::user();
        $produks = [];

        // Jika role adalah superadmin, tampilkan semua produk.
        // Jika tidak, tampilkan produk berdasarkan id_plan user.
        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            $shifts = DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        $plan = Plan::find($user->id_plan);

        return view('qc-sistem.pengemasan_produk.create', compact('produks', 'shifts', 'plan'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);

        // $validatedData = $request->validate([
        //     'id_produk' => 'required|exists:jenis_produk,id',
        //     'id_shift' => 'required|exists:data_shift,id',
        //     'tanggal' => 'required|date_format:d-m-Y H:i:s',
        //        'jam' => 'required',
        // //    'tanggal_expired' => 'required|date',
        //     'berat_produk' => 'required|string|max:255',
        //     'kode_produksi' => 'required|string|max:255',
        //     'aktual_suhu_produk' => 'required|numeric',
        //     'waktu_awal_packing' => 'required',
        //     'waktu_selesai_packing' => 'required',
        // ]);

          if ($isSpecialRole) {
        $validatedData = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date_format:d-m-Y', // Hanya validasi format tanggal saja
            'jam' => 'required',
            'berat_produk' => 'required|string|max:255',
            'kode_produksi' => 'required|string|max:255',
            'aktual_suhu_produk' => 'required|array',
            'aktual_suhu_produk.*' => 'required',
            'waktu_awal_packing' => 'required',
            'waktu_selesai_packing' => 'nullable',
        ]);
    } else {
        $validatedData = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required',
            'berat_produk' => 'required|string|max:255',
            'kode_produksi' => 'required|string|max:255',
            'aktual_suhu_produk' => 'required|array',
            'aktual_suhu_produk.*' => 'required',
            'waktu_awal_packing' => 'required',
            'waktu_selesai_packing' => 'nullable',
        ]);
    }

        // Transform the date format
     if ($isSpecialRole) {
        // Untuk user dengan role 2 atau 3, gunakan format tanggal dari request tapi waktu dari now()
        $dateOnly = \Carbon\Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
        $timeNow = now()->format('H:i:s');
        $validatedData['tanggal'] = $dateOnly . ' ' . $timeNow;
    } else {
        // Untuk user lain, gunakan format tanggal dan waktu dari request
        $validatedData['tanggal'] = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
    }

        
        $validatedData['jam'] = $request->jam;
        $validatedData['tanggal_expired'] = $request->tanggal_expired ? \Carbon\Carbon::parse($request->tanggal_expired)->format('Y-m-d') : null;
        $validatedData['berat'] = $request->berat_produk;
        $validatedData['is_kombinasi'] = ($request->has('is_kombinasi') && $request->is_kombinasi == 1) ? 1 : 0;
        $validatedData['id_produk_2'] = ($request->has('is_kombinasi') && $request->is_kombinasi == 1) ? $request->id_produk_2 : null;
        $validatedData['kode_produksi_2'] = ($request->has('is_kombinasi') && $request->is_kombinasi == 1) ? $request->kode_produksi_2 : null;
        $validatedData['berat_produk_2'] = ($request->has('is_kombinasi') && $request->is_kombinasi == 1) ? $request->berat_produk_2 : null;
        $validatedData['tanggal_expired_2'] = ($request->has('is_kombinasi') && $request->is_kombinasi == 1 && $request->tanggal_expired_2) ? \Carbon\Carbon::parse($request->tanggal_expired_2)->format('Y-m-d') : null;
        // Tambahkan id_plan dari user yang sedang login
        $validatedData['id_plan'] = Auth::user()->id_plan;
        
        // Berikan nilai default jika waktu selesai packing tidak diisi
        $validatedData['waktu_selesai_packing'] = $request->waktu_selesai_packing ?? '-';

        PengemasanProduk::create($validatedData);

        return redirect()->route('pengemasan-produk.index')->with('success', 'Data Pengemasan Produk berhasil ditambahkan.');
    }

    public function edit($uuid)
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        $shifts = DataShift::all();
        $dataBags = DataBag::all();
        $pengemasanProduk = PengemasanProduk::where('uuid', $uuid)->firstOrFail();
        return view('qc-sistem.pengemasan_produk.edit', compact('pengemasanProduk', 'produks', 'shifts', 'dataBags'));
    }

    public function update(Request $request, $uuid)
    {
        $pengemasanProduk = PengemasanProduk::where('uuid', $uuid)->firstOrFail();

        $validatedData = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'tanggal_expired' => 'required|date',
            'kode_produksi' => 'required|string|max:255',
            'aktual_suhu_produk' => 'required|array',
            'aktual_suhu_produk.*' => 'required',
            'waktu_awal_packing' => 'required',
            'berat_produk' => 'required|string|max:255',
            'waktu_selesai_packing' => 'nullable',
            'is_kombinasi' => 'nullable|boolean',
            'id_produk_2' => 'nullable|exists:jenis_produk,id',
            'kode_produksi_2' => 'nullable|string|max:255',
            'berat_produk_2' => 'nullable|string|max:255',
            'tanggal_expired_2' => 'nullable|date',
        ]);

        // Transform the date format
        $validatedData['tanggal'] = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
        $validatedData['tanggal_expired'] = $request->tanggal_expired ? \Carbon\Carbon::parse($request->tanggal_expired)->format('Y-m-d') : null;
        $validatedData['berat'] = $request->berat_produk;
        $validatedData['is_kombinasi'] = ($request->has('is_kombinasi') && $request->is_kombinasi == 1) ? 1 : 0;
        $validatedData['id_produk_2'] = ($request->has('is_kombinasi') && $request->is_kombinasi == 1) ? $request->id_produk_2 : null;
        $validatedData['kode_produksi_2'] = ($request->has('is_kombinasi') && $request->is_kombinasi == 1) ? $request->kode_produksi_2 : null;
        $validatedData['berat_produk_2'] = ($request->has('is_kombinasi') && $request->is_kombinasi == 1) ? $request->berat_produk_2 : null;
        $validatedData['tanggal_expired_2'] = ($request->has('is_kombinasi') && $request->is_kombinasi == 1 && $request->tanggal_expired_2) ? \Carbon\Carbon::parse($request->tanggal_expired_2)->format('Y-m-d') : null;
        
        // Berikan nilai default jika waktu selesai packing tidak diisi
        $validatedData['waktu_selesai_packing'] = $request->waktu_selesai_packing ?? '-';
        
        $pengemasanProduk->update($validatedData);

        return redirect()->route('pengemasan-produk.index')->with('success', 'Data Pengemasan Produk berhasil diubah.');
    }

    public function destroy($uuid)
    {
        $pengemasanProduk = PengemasanProduk::where('uuid', $uuid)->firstOrFail();
        $pengemasanProduk->delete();
        return redirect()->route('pengemasan-produk.index')->with('success', 'Data Pengemasan Produk berhasil dihapus.');
    }

    /**
     * Tampilkan halaman logs untuk Pengemasan Produk
     */
    public function showLogs($uuid)
    {
        $pengemasanProduk = PengemasanProduk::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $pengemasanProduk->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat log data ini.');
        }

        $logs = PengemasanProdukLog::with('user')
            ->where('pengemasan_produk_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('qc-sistem.pengemasan_produk.logs', compact('pengemasanProduk', 'logs'));
    }

    /**
     * API untuk DataTables logs Pengemasan Produk
     */
    public function getLogsJson($uuid)
    {
        $pengemasanProduk = PengemasanProduk::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $pengemasanProduk->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = PengemasanProdukLog::with('user')
            ->where('pengemasan_produk_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $logs->map(function ($log) {
                return [
                    'tanggal' => $log->created_at->format('d-m-Y H:i:s'),
                    'user' => $log->user->name ?? 'System',
                    'role' => $log->user->role ?? '-',
                    'field_yang_diubah' => $log->nama_field,
                    'deskripsi_perubahan' => $log->deskripsi_perubahan,
                    'ip_address' => $log->ip_address,
                ];
            })
        ]);
    }
}
