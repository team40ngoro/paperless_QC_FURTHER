<?php

namespace App\Http\Controllers;

use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\ProsesBattering;
use App\Models\ProsesBatteringLog;
use App\Models\JenisBetter;
use App\Models\PembuatanPredust;
use App\Models\ProsesBreader;
use App\Models\ProsesFrayer;
use App\Models\HasilPenggorengan;
use App\Models\PembekuanIqfPenggorengan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProsesBatteringController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = ProsesBattering::with(['produk', 'penggorengan.shift', 'user', 'plan']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if($search) {
            $query->whereHas('produk', function($q) use ($search) {
                $q->where('nama_produk', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('qc-sistem.proses_battering.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        // If user is superadmin, get all products. Otherwise, get products by user's plan.
        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        
        $jenis_batters = JenisBetter::all();
        
        // Get penggorengan data if UUID is provided
        $penggorenganData = null;
        if ($request->has('penggorengan_uuid')) {
            $penggorenganData = \App\Models\Penggorengan::where('uuid', $request->penggorengan_uuid)->first();
        }
        
        // Get predust data if UUID is provided
        $predustData = null;
        if ($request->has('predust_uuid')) {
            $predustData = PembuatanPredust::with(['produk', 'jenisPredust', 'penggorengan.produk', 'penggorengan.shift'])
                ->where('uuid', $request->predust_uuid)
                ->first();
                
            // If coming from predust, also get the original penggorengan data
            if ($predustData && $predustData->penggorengan) {
                $penggorenganData = $predustData->penggorengan;
            }
        }
        
        return view('qc-sistem.proses_battering.create', compact('produks', 'jenis_batters', 'penggorenganData', 'predustData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'kode_produksi_better' => 'required|string|max:255',
            'id_jenis_better' => 'required|exists:jenis_better,id',
            'hasil_better' => 'required|string|max:255',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required|date_format:H:i',
        ]);
        $data = [
            'uuid' => Str::uuid(),
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'id_produk' => $request->id_produk,
            'kode_produksi_better' => $request->kode_produksi_better,
            'id_jenis_better' => $request->id_jenis_better,
            'hasil_better' => $request->hasil_better,
            'tanggal' =>Carbon::createFromFormat('d-m-Y H:i:s', $request['tanggal'])->format('Y-m-d H:i:s'),
            'jam' => $request->jam,
            'penggorengan_uuid' => $request->penggorengan_uuid ?: null,
            'predust_uuid' => $request->predust_uuid ?: null,
            'kode_produksi_better' => $request->kode_produksi_better,
        ];
        
        
        ProsesBattering::create($data);

        return redirect()->route('proses-battering.index')
            ->with('success', 'Data Proses Battering berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     * (Not typically used, redirecting to a more useful page)
     */
    public function show($uuid)
    {
        return redirect()->route('proses-battering.edit', $uuid);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $item = ProsesBattering::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Authorization Check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Apply the same product filtering logic as in create
        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        
        $jenis_batters = JenisBetter::all();

        return view('qc-sistem.proses_battering.edit', compact('item', 'produks', 'jenis_batters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $item = ProsesBattering::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'kode_produksi_better' => 'required|string|max:255',
            'id_jenis_better' => 'required|exists:jenis_better,id',
            'hasil_better' => 'required|string|max:255',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
        ]);

        // user_id and id_plan should not be changed on update
        $item->update([
            'id_produk' => $request->id_produk,
            'kode_produksi_better' => $request->kode_produksi_better,
            'id_jenis_better' => $request->id_jenis_better,
            'hasil_better' => $request->hasil_better,
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
        ]);

        return redirect()->route('proses-battering.index')
            ->with('success', 'Data Proses Battering berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $item = ProsesBattering::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Authorization Check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $isReferenced = ProsesBreader::where('battering_uuid', $uuid)->exists()
            || ProsesFrayer::where('battering_uuid', $uuid)->exists()
            || HasilPenggorengan::where('battering_uuid', $uuid)->exists()
            || PembekuanIqfPenggorengan::where('battering_uuid', $uuid)->exists();

        if ($isReferenced) {
            return redirect()->route('proses-battering.index')
                ->with('error', 'Data tidak dapat dihapus karena sudah terhubung dengan proses selanjutnya.');
        }

        $item->delete();

        return redirect()->route('proses-battering.index')
            ->with('success', 'Data Proses Battering berhasil dihapus.');
    }

    public function getJenisBetterByProduk($id_produk)
    {
        $user = Auth::user();
        $produk = JenisProduk::find($id_produk);

        if ($user->role !== 'superadmin' && (!$produk || $produk->id_plan !== $user->id_plan)) {
            return response()->json([], 403);
        }

        $jenis_better = JenisBetter::where('id_produk', $id_produk)->pluck('jenis_better', 'id');
        return response()->json($jenis_better);
    }

    /**
     * Show logs for a specific Proses Battering record
     */
    public function showLogs($uuid)
    {
        $item = ProsesBattering::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Authorization Check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat log data ini.');
        }

        $logs = ProsesBatteringLog::where('proses_battering_uuid', $uuid)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        return view('qc-sistem.proses_battering.logs', compact('item', 'logs'));
    }

    /**
     * Get logs data in JSON format for DataTables
     */
    public function getLogsJson($uuid)
    {
        $item = ProsesBattering::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Authorization Check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = ProsesBatteringLog::where('proses_battering_uuid', $uuid)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $logs->map(function ($log) {
                return [
                    'created_at' => $log->created_at->format('d/m/Y H:i:s'),
                    'user_name' => $log->user->name ?? 'System',
                    'user_role' => $log->user_role ?? 'N/A',
                    'nama_field' => $log->nama_field,
                    'field_yang_diubah' => $log->field_yang_diubah,
                    'nilai_lama' => $log->nilai_lama,
                    'nilai_baru' => $log->nilai_baru,
                    'ip_address' => $log->ip_address
                ];
            })
        ]);
    }
}