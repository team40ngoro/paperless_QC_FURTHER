<?php

namespace App\Http\Controllers;

use App\Models\BahanBakuTumbling;
use App\Models\BahanBakuTumblingLog;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\ProsesTumbling;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BahanBakuTumblingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
        {

            $user = auth()->user();
            $query = BahanBakuTumbling::with(['plan', 'user','shift', 'produk']);
            
            if( $user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }

            $search = request('search');
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('kode_produksi', 'LIKE', '%' . $search . '%')
                      ->orWhereHas('produk', function($qp) use ($search) {
                          $qp->where('nama_produk', 'LIKE', '%' . $search . '%');
                      });
                });
            }

            $bahanBakuTumbling = $query->orderBy('created_at', 'desc')->paginate(10);


            return view('qc-sistem.bahan_baku_tumbling.index', compact('bahanBakuTumbling'));
        }

     public function create()
        {

            $user = auth()->user();

            if ($user->role === 'superadmin') {
                $produks = JenisProduk::all();
                $plans = Plan::all();
                $shifts = DataShift::all();
            } else {
            
                $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
                $plans = Plan::where('id', $user->id_plan)->get();
                $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            
            }

            return view('qc-sistem.bahan_baku_tumbling.create', compact('plans', 'shifts', 'produks'));
        }
        
        public function store(Request $request)
        {
            $user = auth()->user();
            
            // DEBUG: Log all request data
            \Log::info('BahanBakuTumbling Store Request', [
                'all_data' => $request->all(),
                'has_manual_bahan' => $request->has('manual_bahan'),
                'has_database_bahan' => $request->has('database_bahan'),
                'manual_bahan' => $request->input('manual_bahan'),
                'database_bahan' => $request->input('database_bahan'),
            ]);
            
            // Validate basic fields
            $request->validate([
                'shift_id' => 'required|exists:data_shift,id',
                'id_produk' => 'required|exists:jenis_produk,id',
                'kode_produksi' => 'required|string|max:255',
                'tanggal' => 'required|date_format:d-m-Y H:i:s',
                'jam' => 'required|date_format:H:i',
                'salinity' => 'required|string|max:255',
                'hasil_pencampuran' => 'required|string|max:255',
                'keterangan' => 'nullable|string',
                'manual_bahan' => 'nullable|array',
                'manual_bahan.*.nama_bahan_baku' => 'required_with:manual_bahan|string|max:255',
                'manual_bahan.*.jumlah' => 'required_with:manual_bahan|numeric',
                'manual_bahan.*.kode_produksi_bahan_baku' => 'required_with:manual_bahan|string|max:255',
                'manual_bahan.*.suhu' => 'required_with:manual_bahan|string|max:50',
                'manual_bahan.*.kondisi_daging' => 'nullable|string|max:255',
                'database_bahan' => 'nullable|array',
                'database_bahan.*.nama_bahan_baku' => 'nullable|string|max:255',
                'database_bahan.*.jumlah' => 'nullable|string|max:50',
                'database_bahan.*.kode_produksi_bahan_baku' => 'nullable|string|max:255',
                'database_bahan.*.suhu' => 'nullable|string|max:50',
                'database_bahan.*.kondisi_daging' => 'nullable|string|max:255',
                'id_bahan_nonforming' => 'nullable|exists:bahan_rm_non_forming,id',
            ]);
            
            // Check if manual mode (ada manual_bahan array)
            if ($request->has('manual_bahan') && is_array($request->manual_bahan) && count($request->manual_bahan) > 0) {
                // Mode Manual - create 1 record dengan manual_bahan_data sebagai JSON
                $bahanBakuTumbling = BahanBakuTumbling::create([
                    'uuid' => Str::uuid(),
                    'id_plan' => $user->id_plan,
                    'shift_id' => $request->shift_id,
                    'id_produk' => $request->id_produk,
                    'user_id' => $user->id,
                    'kode_produksi' => $request->kode_produksi,
                    'salinity' => $request->salinity,
                    'hasil_pencampuran' => $request->hasil_pencampuran,
                    'keterangan' => $request->keterangan,
                    'manual_bahan_data' => $request->manual_bahan,
                    'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
                    'jam' => Carbon::createFromFormat('H:i', $request->jam)->format('H:i'),
                ]);
            } elseif ($request->has('database_bahan') && is_array($request->database_bahan) && count($request->database_bahan) > 0) {
                // Mode Database - create 1 record dengan semua bahan sebagai JSON array
                $bahanBakuTumbling = BahanBakuTumbling::create([
                    'uuid' => Str::uuid(),
                    'id_plan' => $user->id_plan,
                    'shift_id' => $request->shift_id,
                    'id_produk' => $request->id_produk,
                    'id_bahan_nonforming' => $request->id_bahan_nonforming,
                    'user_id' => $user->id,
                    'kode_produksi' => $request->kode_produksi,
                    'salinity' => $request->salinity,
                    'hasil_pencampuran' => $request->hasil_pencampuran,
                    'keterangan' => $request->keterangan,
                    'manual_bahan_data' => $request->database_bahan,
                    'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
                    'jam' => Carbon::createFromFormat('H:i', $request->jam)->format('H:i'),
                ]);
            } else {
                return back()->with('error', 'Pilih minimal satu bahan baku');
            }
            return redirect()->route('bahan-baku-tumbling.index')
                ->with('success', 'Data bahan baku tumbling berhasil ditambahkan.');
        }
        /**
         * Display the specified resource.
         */
        public function show($uuid)
        {
            $bahanBakuTumbling = BahanBakuTumbling::where('uuid', $uuid)->firstOrFail();
            
            return view('qc-sistem.bahan_baku_tumbling.show', compact('bahanBakuTumbling'));
        }
        public function edit($uuid)
        {
            $user = auth()->user();
            $bahanBakuTumbling = BahanBakuTumbling::where('uuid', $uuid)->firstOrFail();
        
            if ($user->role === 'superadmin') {
                $produks = JenisProduk::all();
                $plans = Plan::all();
                $shifts = DataShift::all();
            } else {
            
                $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
                $plans = Plan::where('id', $user->id_plan)->get();
                $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            
            }

            return view('qc-sistem.bahan_baku_tumbling.edit', compact('bahanBakuTumbling', 'plans', 'shifts', 'produks'));
        }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $user = auth()->user();
        $bahanBakuTumbling = BahanBakuTumbling::where('uuid', $uuid)->firstOrFail();
        
        // Validate basic fields
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'kode_produksi' => 'required|string|max:255',
            'salinity' => 'required|string|max:255',
            'hasil_pencampuran' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'manual_bahan' => 'nullable|array',
            'manual_bahan.*.jumlah' => 'nullable|string|max:50',
            'manual_bahan.*.kode_produksi_bahan_baku' => 'nullable|string|max:255',
            'manual_bahan.*.suhu' => 'nullable|string|max:50',
            'manual_bahan.*.kondisi_daging' => 'nullable|string|max:255',
        ]);

        // Update dengan manual_bahan array
        $bahanBakuTumbling->update([
            'id_plan' => $user->id_plan,
            'shift_id' => $request->shift_id,
            'id_produk' => $request->id_produk,
            'user_id' => $user->id,
            'kode_produksi' => $request->kode_produksi,
            'salinity' => $request->salinity,
            'hasil_pencampuran' => $request->hasil_pencampuran,
            'keterangan' => $request->keterangan,
            'manual_bahan_data' => $request->has('manual_bahan') && is_array($request->manual_bahan) ? $request->manual_bahan : null,
        ]);

        return redirect()->route('bahan-baku-tumbling.index')
            ->with('success', 'Data bahan baku tumbling berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
        {
            $bahanBakuTumbling = BahanBakuTumbling::where('uuid', $uuid)->firstOrFail();

            $isReferenced = ProsesTumbling::where('bahan_baku_tumbling_uuid', $uuid)->exists();
            if ($isReferenced) {
                return redirect()->route('bahan-baku-tumbling.index')
                    ->with('error', 'Data tidak dapat dihapus karena sudah terhubung dengan proses selanjutnya.');
            }

            $bahanBakuTumbling->delete();

            return redirect()->route('bahan-baku-tumbling.index')
                ->with('success', 'Data bahan baku tumbling berhasil dihapus.');
        }

    /**
     * Get Nomor Formula Non Forming by Produk (API)
     */
    public function getNomorFormulaByProduk($produkId)
    {
        $formulas = \App\Models\MasterProdukNonForming::where('id_produk', $produkId)->get(['id', 'nomor_formula']);
        return response()->json($formulas);
    }

    /**
     * Get Bahan Non Forming by Formula (API)
     */
    public function getBahanNonFormingByFormula($formulaId)
    {
        $bahans = \App\Models\BahanNonForming::where('id_no_formula_non_forming', $formulaId)->get(['id', 'nama_rm', 'berat_rm']);
        return response()->json($bahans);
    }

    /**
     * Tampilkan halaman logs untuk Bahan Baku Tumbling
     */
    public function showLogs($uuid)
        {
            $bahanBakuTumbling = BahanBakuTumbling::where('uuid', $uuid)->firstOrFail();
            $user = Auth::user();

            if ($user->role !== 'superadmin' && $bahanBakuTumbling->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses untuk melihat log data ini.');
            }

            $logs = BahanBakuTumblingLog::with('user')
                ->where('bahan_baku_tumbling_uuid', $uuid)
                ->orderBy('created_at', 'desc')
                ->paginate(5);

            return view('qc-sistem.bahan_baku_tumbling.logs', compact('bahanBakuTumbling', 'logs'));
        }

    /**
     * API untuk DataTables logs Bahan Baku Tumbling
     */
    public function getLogsJson($uuid)
        {
            $bahanBakuTumbling = BahanBakuTumbling::where('uuid', $uuid)->firstOrFail();
            $user = Auth::user();

            if ($user->role !== 'superadmin' && $bahanBakuTumbling->id_plan !== $user->id_plan) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $logs = BahanBakuTumblingLog::with('user')
                ->where('bahan_baku_tumbling_uuid', $uuid)
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
