<?php

namespace App\Http\Controllers;

use App\Models\ProsesMarinadeModel;
use App\Models\BahanBakuTumbling;
use App\Models\DataShift;
use App\Models\Plan;
use App\Models\User;
use App\Models\JenisMarinade; // Corrected: Was JenisMarinadeModel
use App\Models\JenisProduk;
use App\Models\ProsesMarinadeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProsesMarinadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = ProsesMarinadeModel::with(['shift', 'plan', 'user', 'jenisMarinade']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if ($search) {
            $query->where('kode_produksi', 'LIKE', '%' . $search . '%');
        }

        $prosesMarinades = $query->orderBy('created_at', 'desc')
                                ->paginate(10);

        return view('qc-sistem.proses_marinade.index', compact('prosesMarinades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $bahanBakuTumbling = null;
        
        // Jika ada parameter bahan_baku_uuid, ambil data bahan baku
        if ($request->has('bahan_baku_uuid')) {
            $bahanBakuTumbling = BahanBakuTumbling::with('produk')->where('uuid', $request->bahan_baku_uuid)->first();
            if (!$bahanBakuTumbling) {
                return redirect()->route('bahan-baku-tumbling.index')
                    ->with('error', 'Data Bahan Baku Tumbling tidak ditemukan.');
            }
        }
        
        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
            $plans = Plan::all();
            $jenisMarinades = JenisMarinade::all();
            $produks = JenisProduk::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $jenisMarinades = JenisMarinade::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.proses_marinade.create', compact(
            'shifts', 'plans', 'jenisMarinades', 'produks', 'bahanBakuTumbling'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_shift' => 'required|exists:data_shift,id',
            'id_jenis_marinade' => 'required|exists:jenis_marinade,id',
            'kode_produksi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'hasil_pencampuran' => 'required|string'
        ]);

        try {
            $prosesMarinadeData = $request->except(['tanggal', 'bahan_baku_uuid']);
            $prosesMarinadeData['id_plan'] = Auth::user()->id_plan;
            $prosesMarinadeData['id_user'] = Auth::id();
            $prosesMarinadeData['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');

            // Jika ada parameter bahan_baku_uuid dari form atau URL, otomatis set relasi
            $bahanBakuUuid = $request->input('bahan_baku_uuid') ?: $request->query('bahan_baku_uuid');
            if ($bahanBakuUuid) {
                $bahanBaku = BahanBakuTumbling::where('uuid', $bahanBakuUuid)->first();
                if ($bahanBaku) {
                    $prosesMarinadeData['bahan_baku_tumbling_id'] = $bahanBaku->id;
                    $prosesMarinadeData['bahan_baku_tumbling_uuid'] = $bahanBaku->uuid;
                }
            }

            ProsesMarinadeModel::create($prosesMarinadeData);

            return redirect()->route('proses-marinade.index')
                ->with('success', 'Data Proses Marinade berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        // Corrected relationship name to 'jenisMarinade'
        $prosesMarinadeModel = ProsesMarinadeModel::with(['shift', 'plan', 'user', 'jenisMarinade'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Authorization Check
        $user = Auth::user();
        if ($user->role !== 'superadmin' && $prosesMarinadeModel->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        return view('qc-sistem.proses_marinade.show', compact('prosesMarinadeModel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        $prosesMarinadeModel = ProsesMarinadeModel::with('jenisMarinade')->where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Authorization Check
        if ($user->role !== 'superadmin' && $prosesMarinadeModel->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
            $plans = Plan::all();
            $jenisMarinades = JenisMarinade::all(); // Corrected: Was JenisMarinadeModel
            $produks = JenisProduk::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $jenisMarinades = JenisMarinade::where('id_plan', $user->id_plan)->get(); // Corrected: Was JenisMarinadeModel
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.proses_marinade.edit', compact('prosesMarinadeModel', 'shifts', 'plans', 'jenisMarinades', 'produks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        $prosesMarinadeModel = ProsesMarinadeModel::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Authorization Check
        if ($user->role !== 'superadmin' && $prosesMarinadeModel->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui data ini.');
        }

        $request->validate([
            'id_shift' => 'required|exists:data_shift,id',
            'id_jenis_marinade' => 'required|exists:jenis_marinade,id',
            'kode_produksi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'hasil_pencampuran' => 'required|string'
        ]);

        try {
            $updateData = $request->except(['id_user', 'id_plan', 'tanggal']);
            $updateData['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
            $prosesMarinadeModel->update($updateData);

            return redirect()->route('proses-marinade.index')
                ->with('success', 'Data Proses Marinade berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        try {
            $prosesMarinadeModel = ProsesMarinadeModel::where('uuid', $uuid)->firstOrFail();
            $user = Auth::user();

            // Authorization Check
            if ($user->role !== 'superadmin' && $prosesMarinadeModel->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
            }

            $prosesMarinadeModel->delete();

            return redirect()->route('proses-marinade.index')
                ->with('success', 'Data Proses Marinade berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

        /**
     * Get Jenis Marinade by Produk for AJAX request.
     */
    public function getJenisMarinadeByProduk(Request $request)
    {
        $user = Auth::user();
        $produk = JenisProduk::find($request->id_produk);

        if ($user->role !== 'superadmin' && (!$produk || $produk->id_plan !== $user->id_plan)) {
            return response()->json([], 403);
        }

        $jenisMarinade = JenisMarinade::where('id_produk', $request->id_produk)->pluck('jenis_marinade', 'id');
        return response()->json($jenisMarinade);
    }

    /**
     * Tampilkan halaman logs untuk Proses Marinade
     */
    public function showLogs($uuid)
    {
        $prosesMarinadeModel = ProsesMarinadeModel::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $prosesMarinadeModel->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat log data ini.');
        }

        $logs = ProsesMarinadeLog::with('user')
            ->where('proses_marinade_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('qc-sistem.proses_marinade.logs', compact('prosesMarinadeModel', 'logs'));
    }

    /**
     * API untuk DataTables logs Proses Marinade
     */
    public function getLogsJson($uuid)
    {
        $prosesMarinadeModel = ProsesMarinadeModel::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $prosesMarinadeModel->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = ProsesMarinadeLog::with('user')
            ->where('proses_marinade_uuid', $uuid)
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