<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanProdukCookingMixerFla;
use App\Models\NamaFormulaFla;
use App\Models\NomorStepFormulaFla;
use App\Models\BahanFormulaFla;
use App\Models\DataShift;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use App\Models\PemeriksaanProdukCookingMixerFlaLog;
use Illuminate\Support\Facades\Auth;
use App\Services\BulkExport\PemeriksaanProdukCookingMixerFlaBulkExportService;
use App\Http\Controllers\Traits\HasAuditController;

class PemeriksaanProdukCookingMixerFlaController extends Controller
{
    use HasAuditController;

    public function bulkExportExcel(Request $request, PemeriksaanProdukCookingMixerFlaBulkExportService $exportService)
    {
        return $exportService->exportExcel($request);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        // Auditor langsung diarahkan ke halaman audit
        if ($user->id_role == 6) {
            return redirect()->route('pemeriksaan-produk-cooking-mixer-fla.audit');
        }

        $query = PemeriksaanProdukCookingMixerFla::with([
            'plan', 'user', 'shift', 'namaFormulaFla.produk',
            'nomorStepFormulaFla', 'bahanFormulaFla',
            'qcApprover', 'produksiApprover', 'spvApprover'
        ])->where('is_audit', false);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if (!empty($search)) {
            $query->whereHas('namaFormulaFla.produk', function ($produkQuery) use ($search) {
                $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
            });
        }

        $perPage = request()->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50, 100]) ? $perPage : 10;

        $this->applyDateRangeFilter(request(), $query);
        $items = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return view('qc-sistem.pemeriksaan-produk-cooking-mixer-fla.index', compact('items', 'search', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Get shifts based on user's plan
        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        // Get unique products from nama_formula_fla using relationship
        if ($user->role === 'superadmin') {
            $products = NamaFormulaFla::with('produk')
                ->get()
                ->pluck('produk')
                ->filter()
                ->unique('id')
                ->values();
        } else {
            $products = NamaFormulaFla::with('produk')
                ->where('id_plan', $user->id_plan)
                ->get()
                ->pluck('produk')
                ->filter()
                ->unique('id')
                ->values();
        }
      

        return view('qc-sistem.pemeriksaan-produk-cooking-mixer-fla.create', compact('shifts', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'shift_id'             => 'required|exists:data_shift,id',
            'tanggal'              => 'required|date',
            'jam'                  => 'required',
            'id_nama_formula_fla'  => 'required|exists:nama-formula-fla,id',
            'id_stp_frm_fla'       => 'required|exists:nomor_step_formula_fla,id',
            'id_frm_fla'           => 'required|exists:bahan_formula_fla,id',
            'kode_produksi_bahan'  => 'nullable|array',
            'kode_produksi_bahan.*'=> 'nullable|string|max:255',
            'kode_produksi'        => 'required|string|max:255',
            'berat'                => 'required|string|max:255',
            'waktu_start'          => 'required|string|max:255',
            'waktu_stop'           => 'required|string|max:255',
            'sensori_kondisi'      => 'required|string|max:255',
            'status_gas'           => 'required|boolean',
            'lama_proses'          => 'required|string|max:255',
            'speed'                => 'required|string|max:255',
            'temp_std_1'           => 'required|string|max:255',
            'temp_std_2'           => 'required|string|max:255',
            'temp_std_3'           => 'required|string|max:255',
            'organo_warna'         => 'required|in:OK,Tidak OK',
            'organo_aroma'         => 'required|in:OK,Tidak OK',
            'organo_tekstur'       => 'required|in:OK,Tidak OK',
            'organo_rasa'          => 'required|in:OK,Tidak OK',
            'catatan'              => 'nullable|string',
        ], [
            'shift_id.required'           => 'Shift harus dipilih.',
            'tanggal.required'            => 'Tanggal harus diisi.',
            'jam.required'                => 'Jam harus diisi.',
            'id_nama_formula_fla.required' => 'Nama formula FLA harus dipilih.',
            'id_frm_fla.required'         => 'Bahan formula FLA harus dipilih.',
            'kode_produksi.required'      => 'Kode produksi harus diisi.',
            'berat.required'              => 'Berat harus dipilih.',
            'waktu_start.required'        => 'Waktu start harus diisi.',
            'waktu_stop.required'         => 'Waktu stop harus diisi.',
            'sensori_kondisi.required'    => 'Sensori kondisi harus diisi.',
            'status_gas.required'         => 'Status gas harus dipilih.',
            'lama_proses.required'        => 'Lama proses harus diisi.',
            'speed.required'              => 'Speed harus diisi.',
            'temp_std_1.required'         => 'Temperature standard 1 harus diisi.',
            'temp_std_2.required'         => 'Temperature standard 2 harus diisi.',
            'temp_std_3.required'         => 'Temperature standard 3 harus diisi.',
            'organo_warna.required'       => 'Organoleptic warna harus dipilih.',
            'organo_aroma.required'       => 'Organoleptic aroma harus dipilih.',
            'organo_tekstur.required'     => 'Organoleptic tekstur harus dipilih.',
            'organo_rasa.required'        => 'Organoleptic rasa harus dipilih.',
        ]);

        $tanggalData = \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now());

        // Authorization check for non-superadmin
        if ($user->role !== 'superadmin') {
            $shift = DataShift::findOrFail($request->shift_id);
            if ($shift->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses untuk menambah data ini.');
            }
        }

        PemeriksaanProdukCookingMixerFla::create([
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'shift_id' => $request->shift_id,
            'tanggal' => $tanggalData,
            'jam' => $request->jam,
            'id_nama_formula_fla' => $request->id_nama_formula_fla,
            'id_stp_frm_fla' => $request->id_stp_frm_fla,
            'id_frm_fla' => $request->id_frm_fla,
            'kode_produksi_bahan' => $request->kode_produksi_bahan,
            'kode_produksi' => $request->kode_produksi,
            'berat' => $request->berat,
            'waktu_start' => $request->waktu_start,
            'waktu_stop' => $request->waktu_stop,
            'sensori_kondisi' => $request->sensori_kondisi,
            'status_gas' => $request->status_gas,
            'lama_proses' => $request->lama_proses,
            'speed' => $request->speed,
            'temp_std_1' => $request->temp_std_1,
            'temp_std_2' => $request->temp_std_2,
            'temp_std_3' => $request->temp_std_3,
            'organo_warna' => $request->organo_warna,
            'organo_aroma' => $request->organo_aroma,
            'organo_tekstur' => $request->organo_tekstur,
            'organo_rasa' => $request->organo_rasa,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('pemeriksaan-produk-cooking-mixer-fla.index')
            ->with('success', 'Data pemeriksaan produk cooking mixer FLA berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $user = auth()->user();
        
        $pemeriksaanProduk = PemeriksaanProdukCookingMixerFla::where('uuid', $uuid)->firstOrFail();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $pemeriksaanProduk->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('pemeriksaan-produk-cooking-mixer-fla.show', $pemeriksaanProduk)) {
            return $redirect;
        }

        $item = $pemeriksaanProduk->load([
            'plan', 'user', 'shift', 'namaFormulaFla.produk', 
            'nomorStepFormulaFla', 'bahanFormulaFla'
        ]);

        return view('qc-sistem.pemeriksaan-produk-cooking-mixer-fla.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $user = auth()->user();
        
        $pemeriksaanProduk = PemeriksaanProdukCookingMixerFla::where('uuid', $uuid)->firstOrFail();

        if ($redirect = $this->redirectToCanonicalAuditUrl('pemeriksaan-produk-cooking-mixer-fla.edit', $pemeriksaanProduk)) {
            return $redirect;
        }
        
        // Check edit time limit using global config
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($pemeriksaanProduk->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('pemeriksaan-produk-cooking-mixer-fla.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        // Authorization check
        if ($user->role !== 'superadmin' && $pemeriksaanProduk->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Get shifts based on user's plan
        if ($user->role === 'superadmin') {
            $shifts = DataShift::with('plan')->get();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        // Get unique products from nama_formula_fla using relationship
        if ($user->role === 'superadmin') {
            $products = NamaFormulaFla::with('produk')
                ->get()
                ->pluck('produk')
                ->filter()
                ->unique('id')
                ->values();
        } else {
            $products = NamaFormulaFla::with('produk')
                ->where('id_plan', $user->id_plan)
                ->get()
                ->pluck('produk')
                ->filter()
                ->unique('id')
                ->values();
        }

        $item = $pemeriksaanProduk;

        return view('qc-sistem.pemeriksaan-produk-cooking-mixer-fla.edit', compact('item', 'shifts', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $user = auth()->user();
        
        $pemeriksaanProduk = PemeriksaanProdukCookingMixerFla::where('uuid', $uuid)->firstOrFail();
        
        // Check edit time limit using global config
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($pemeriksaanProduk->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('pemeriksaan-produk-cooking-mixer-fla.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        // Authorization check
        if ($user->role !== 'superadmin' && $pemeriksaanProduk->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate data ini.');
        }

        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'nullable|string',
            'id_nama_formula_fla' => 'required|exists:nama-formula-fla,id',
            'id_stp_frm_fla' => 'required|exists:nomor_step_formula_fla,id',
            'id_frm_fla' => 'required|exists:bahan_formula_fla,id',
            'kode_produksi_bahan' => 'nullable|array',
            'kode_produksi_bahan.*' => 'nullable|string|max:255',
            'kode_produksi' => 'required|string|max:255',
            'berat' => 'required|string|max:255',
            'waktu_start' => 'required|string|max:255',
            'waktu_stop' => 'required|string|max:255',
            'sensori_kondisi' => 'required|string|max:255',
            'status_gas' => 'required|boolean',
            'lama_proses' => 'required|string|max:255',
            'speed' => 'required|string|max:255',
            'temp_std_1' => 'required|string|max:255',
            'temp_std_2' => 'required|string|max:255',
            'temp_std_3' => 'required|string|max:255',
            'organo_warna' => 'required|in:OK,Tidak OK',
            'organo_aroma' => 'required|in:OK,Tidak OK',
            'organo_tekstur' => 'required|in:OK,Tidak OK',
            'organo_rasa' => 'required|in:OK,Tidak OK',
            'catatan' => 'nullable|string',
        ]);

        // Authorization check for non-superadmin
        if ($user->role !== 'superadmin') {
            $shift = DataShift::findOrFail($request->shift_id);
            if ($shift->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses untuk mengupdate data ini.');
            }
        }

        $pemeriksaanProduk->update([
            'shift_id' => $request->shift_id,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'id_nama_formula_fla' => $request->id_nama_formula_fla,
            'id_stp_frm_fla' => $request->id_stp_frm_fla,
            'id_frm_fla' => $request->id_frm_fla,
            'kode_produksi_bahan' => $request->kode_produksi_bahan,
            'kode_produksi' => $request->kode_produksi,
            'berat' => $request->berat,
            'waktu_start' => $request->waktu_start,
            'waktu_stop' => $request->waktu_stop,
            'sensori_kondisi' => $request->sensori_kondisi,
            'status_gas' => $request->status_gas,
            'lama_proses' => $request->lama_proses,
            'speed' => $request->speed,
            'temp_std_1' => $request->temp_std_1,
            'temp_std_2' => $request->temp_std_2,
            'temp_std_3' => $request->temp_std_3,
            'organo_warna' => $request->organo_warna,
            'organo_aroma' => $request->organo_aroma,
            'organo_tekstur' => $request->organo_tekstur,
            'organo_rasa' => $request->organo_rasa,
            'catatan' => $request->catatan,
        ]);

        // Smart redirect: jika audit -> ke audit page, jika operasional -> ke index page
        $redirectRoute = $pemeriksaanProduk->is_audit ? 'pemeriksaan-produk-cooking-mixer-fla.audit' : 'pemeriksaan-produk-cooking-mixer-fla.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Data pemeriksaan produk cooking mixer FLA berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $user = auth()->user();
        
        $pemeriksaanProduk = PemeriksaanProdukCookingMixerFla::where('uuid', $uuid)->firstOrFail();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $pemeriksaanProduk->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $pemeriksaanProduk->delete();

        return redirect()->route('pemeriksaan-produk-cooking-mixer-fla.index')
            ->with('success', 'Data pemeriksaan produk cooking mixer FLA berhasil dihapus.');
    }
    /**
     * Tampilkan halaman logs untuk pemeriksaan produk cooking mixer fla
     */
    public function showLogs($uuid)
    {
        $item = PemeriksaanProdukCookingMixerFla::where('uuid', $uuid)->firstOrFail();
        
        $logs = PemeriksaanProdukCookingMixerFlaLog::where('pemeriksaan_produk_cooking_mixer_fla_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(5);
        
        return view('qc-sistem.pemeriksaan-produk-cooking-mixer-fla.logs', compact('item', 'logs'));
    }

    /**
     * Get logs data untuk DataTables (jika diperlukan)
     */
    public function getLogsJson($uuid)
    {
        $pemeriksaanProdukCookingMixerFla = PemeriksaanProdukCookingMixerFla::where('uuid', $uuid)->firstOrFail();
        
        $logs = PemeriksaanProdukCookingMixerFlaLog::where('pemeriksaan_produk_cooking_mixer_fla_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'tanggal' => $log->created_at->format('d/m/Y H:i:s'),
                    'user' => $log->user_name,
                    'field' => $log->nama_field,
                    'perubahan' => $log->deskripsi_perubahan,
                    'ip_address' => $log->ip_address
                ];
            });

        return response()->json(['data' => $logs]);
    }

    /**
     * Get formula by product ID (AJAX)
     */
    public function getFormulaByProduct($productId)
    {
        $user = auth()->user();
        
        $query = NamaFormulaFla::where('id_produk', $productId);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        $formulas = $query->get(['id', 'nama_formula_fla']);
        
        return response()->json($formulas);
    }

    /**
     * Get steps by formula ID (AJAX)
     */
    public function getStepsByFormula($formulaId)
    {
        $user = auth()->user();
        
        $query = NomorStepFormulaFla::where('id_nama_formula_fla', $formulaId);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        $steps = $query->get(['id', 'nomor_step', 'proses']);
        
        return response()->json($steps);
    }

    /**
     * Get bahan by step ID (AJAX)
     */
    public function getBahanByStep($stepId)
    {
        $user = auth()->user();
        
        $query = BahanFormulaFla::where('id_nomor_step_formula_fla', $stepId)
            ->with(['namaFormulaFla.produk', 'nomorStepFormulaFla']);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        $bahanData = $query->first();
        
        if ($bahanData) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $bahanData->id,
                    'bahan_formula_fla' => $bahanData->getBahanFormulaArray(),
                    'berat_formula_fla' => $bahanData->getBeratFormulaArray(),
                    'step' => $bahanData->nomorStepFormulaFla->nomor_step ?? '-',
                    'proses' => $bahanData->nomorStepFormulaFla->proses ?? '-',
                    'nama_rm' => $bahanData->namaFormulaFla->nama_formula_fla ?? '-'
                ]
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
    }

    /**
     * Get bahan by formula ID (AJAX) - for when step is not required
     */
    public function getBahanByFormula($formulaId)
    {
        $user = auth()->user();
        
        $query = BahanFormulaFla::where('id_nama_formula_fla', $formulaId)
            ->with(['namaFormulaFla.produk', 'nomorStepFormulaFla']);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        // Get the first bahan for this formula (or latest if multiple)
        $bahanData = $query->first();
        
        if ($bahanData) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $bahanData->id,
                    'id_nomor_step_formula_fla' => $bahanData->id_nomor_step_formula_fla,
                    'bahan_formula_fla' => $bahanData->getBahanFormulaArray(),
                    'berat_formula_fla' => $bahanData->getBeratFormulaArray(),
                    'step' => $bahanData->nomorStepFormulaFla->nomor_step ?? '-',
                    'proses' => $bahanData->nomorStepFormulaFla->proses ?? '-',
                    'nama_rm' => $bahanData->namaFormulaFla->nama_formula_fla ?? '-'
                ]
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
    }

    /**
     * Export PDF with filters
     */
    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal_awal' => 'nullable|date',
                'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
                'shift_id' => 'nullable|integer',
                'kode_form' => 'required|string|max:50'
            ]);

            $user = auth()->user();
            
            $query = PemeriksaanProdukCookingMixerFla::with([
                'plan', 'user', 'shift', 'namaFormulaFla.produk', 
                'nomorStepFormulaFla', 'bahanFormulaFla',
                'qcApprover', 'produksiApprover', 'spvApprover'
            ])
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($request->has('is_audit') && $request->is_audit == 1) {
                $query->where('is_audit', true);
            } else {
                $query->operasional();
            }

            // Apply filters
            if ($request->tanggal_awal && $request->tanggal_akhir) {
                $tanggalAwal = \Carbon\Carbon::parse($request->tanggal_awal)->startOfDay();
                $tanggalAkhir = \Carbon\Carbon::parse($request->tanggal_akhir)->endOfDay();
                $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            } elseif ($request->tanggal_awal) {
                $query->whereDate('tanggal', $request->tanggal_awal);
            }
            
            if ($request->shift_id) {
                $query->where('shift_id', $request->shift_id);
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            // Save kode_form to all filtered records
            if ($data->isNotEmpty()) {
                $query->update(['kode_form' => $request->kode_form]);
            }

            if ($data->isEmpty()) {
                $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
                $filterInfo = [];
                
                if ($request->tanggal_awal && $request->tanggal_akhir) {
                    $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
                } elseif ($request->tanggal_awal) {
                    $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y');
                }
                if ($request->shift_id) {
                    $shift = DataShift::find($request->shift_id);
                    $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
                }
                
                $html = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>Data Tidak Ditemukan</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 40px; text-align: center; background-color: #f8f9fa; }
                        .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                        .icon { font-size: 64px; color: #ffc107; margin-bottom: 20px; }
                        h1 { color: #495057; margin-bottom: 20px; }
                        .message { color: #6c757d; margin-bottom: 30px; font-size: 16px; }
                        .filter-info { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0; text-align: left; }
                        .filter-info h4 { margin: 0 0 10px 0; color: #495057; }
                        .filter-info ul { margin: 0; padding-left: 20px; }
                        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
                        .btn:hover { background: #0056b3; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="icon">⚠️</div>
                        <h1>Data Tidak Ditemukan</h1>
                        <p class="message">' . $errorMessage . '</p>';
                        
                if (!empty($filterInfo)) {
                    $html .= '
                        <div class="filter-info">
                            <h4>Filter yang digunakan:</h4>
                            <ul>';
                    foreach ($filterInfo as $info) {
                        $html .= '<li>' . $info . '</li>';
                    }
                    $html .= '
                            </ul>
                        </div>';
                }
                
                $html .= '
                        <p style="color: #6c757d; font-size: 14px;">
                            Silakan coba dengan filter yang berbeda atau pastikan data sudah tersedia di sistem.
                        </p>
                        <a href="javascript:window.close()" class="btn">Tutup</a>
                    </div>
                </body>
                </html>';
                
                return response($html)->header('Content-Type', 'text/html');
            }

            // Prepare filters for PDF view
            $tanggalFilter = 'Semua Tanggal';
            if ($request->tanggal_awal && $request->tanggal_akhir) {
                $tanggalFilter = \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
            } elseif ($request->tanggal_awal) {
                $tanggalFilter = \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y');
            }
            
            $filters = [
                'tanggal' => $tanggalFilter,
                'shift_id' => $request->shift_id,
                'kode_form' => $request->kode_form
            ];

            $kode_form = $request->kode_form;
            $pdf = PDF::loadView('qc-sistem.pemeriksaan-produk-cooking-mixer-fla.export_pdf', compact('data', 'kode_form', 'filters'));
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'pemeriksaan_produk_cooking_mixer_fla_' . date('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve data with role-based validation
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $pemeriksaanProduk = PemeriksaanProdukCookingMixerFla::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control following Produk Forming pattern
        $allowedRoles = [
            'qc' => [1, 3, 5], // Role 1&5 (QC only), Role 3 (QC only)
            'produksi' => [2], // Role 2 (produksi only)
            'spv' => [4] // Role 4 (SPV only)
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan approval ini.'
            ], 403);
        }

        // Sequential approval validation (QC → Produksi → SPV)
        if ($type === 'produksi' && !$pemeriksaanProduk->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$pemeriksaanProduk->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($pemeriksaanProduk->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $pemeriksaanProduk->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }

    /**
     * Bulk Approve data by date and shift
     */
    public function bulkApproveSelected(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:qc,produksi,spv',
            'uuids' => 'required|array|min:1',
            'uuids.*' => 'required|uuid',
        ]);

        $user = Auth::user();
        $type = $validated['type'];
        $allowedRoles = ['qc' => [1, 3, 5], 'produksi' => [2], 'spv' => [4]];
        if (!in_array((int) $user->id_role, $allowedRoles[$type], true)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk approval ini.'], 403);
        }

        $query = PemeriksaanProdukCookingMixerFla::whereIn('uuid', $validated['uuids']);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $items = $query->get();
        if ($items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Data terpilih tidak ditemukan.'], 404);
        }

        $approvalField = "approved_by_{$type}";
        $pending = $items->where($approvalField, false);
        if ($pending->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Semua data terpilih sudah diverifikasi sebelumnya.']);
        }
        $updateData = [
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now(),
        ];
        \DB::transaction(function () use ($pending, $updateData) {
            foreach ($pending as $item) {
                $item->update($updateData);
                $sourceUuid = $item->is_audit ? $item->source_uuid : $item->uuid;
                if ($sourceUuid) {
                    PemeriksaanProdukCookingMixerFla::where('is_audit', false)->where('uuid', $sourceUuid)->update($updateData);
                    PemeriksaanProdukCookingMixerFla::where('is_audit', true)->where('source_uuid', $sourceUuid)->update($updateData);
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Berhasil memverifikasi ' . $pending->count() . ' data ' . strtoupper($type) . '.']);
    }

    public function bulkApprove(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'shift_id' => 'required',
                'type' => 'required|in:qc,produksi,spv'
            ]);

            $user = auth()->user();
            $userRole = $user->id_role;
            $type = $request->type;
            $tanggal = $request->tanggal;
            $shift_id = $request->shift_id;

            // Role validation
            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5]
            ];

            if (!in_array($userRole, $allowedRoles[$type])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized role'], 403);
            }

            // Get records to approve
            $query = PemeriksaanProdukCookingMixerFla::whereDate('tanggal', $tanggal)->where('shift_id', $shift_id);

            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }

            $allRecords = $query->get();

            if ($allRecords->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data untuk disetujui'], 404);
            }

            // Sequential validation check for the WHOLE group
            if ($type === 'produksi') {
                $anyMissingQc = $allRecords->contains(fn($r) => !$r->approved_by_qc);
                if ($anyMissingQc) {
                    return response()->json(['success' => false, 'message' => 'Beberapa data belum disetujui oleh QC'], 400);
                }
            }

            if ($type === 'spv') {
                $anyMissingProduksi = $allRecords->contains(fn($r) => !$r->approved_by_produksi);
                if ($anyMissingProduksi) {
                    return response()->json(['success' => false, 'message' => 'Beberapa data belum disetujui oleh Produksi'], 400);
                }
            }

            $approvalField = 'approved_by_' . $type;
            $approverField = $type . '_approved_by';
            $approvedAtField = $type . '_approved_at';

            \DB::beginTransaction();

            $count = 0;
            foreach ($allRecords as $record) {
                if (!$record->$approvalField) {
                    $record->update([
                        $approvalField => true,
                        $approverField => $user->id,
                        $approvedAtField => now()
                    ]);
                    $count++;
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menyetujui {$count} data sebagai " . strtoupper($type)
            ]);

        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Copy to audit with smart logic
     */
    public function copyToAudit($uuid)
    {
        $user = auth()->user();

        // Auditor (id_role 6) bisa akses audit, atau user dengan permission
        if ($user->id_role != 6 && !$user->hasPermissionTo('create-pemeriksaan-produk-cooking-mixer-fla')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk membuat data audit.');
        }

        $item = PemeriksaanProdukCookingMixerFla::where('uuid', $uuid)->firstOrFail();

        // Jika item yang diklik sudah merupakan record audit
        if ($item->is_audit) {
            return redirect()->route('pemeriksaan-produk-cooking-mixer-fla.edit', $item->uuid);
        }

        // Jika data operasional ini sudah memiliki versi audit sebelumnya
        if ($item->auditVersion) {
            return redirect()->route('pemeriksaan-produk-cooking-mixer-fla.edit', $item->auditVersion->uuid);
        }

        // Jika belum memiliki versi audit, buatkan salinan audit secara otomatis
        $clone = $item->copyToAudit();

        return redirect()->route('pemeriksaan-produk-cooking-mixer-fla.edit', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    // --- AUDIT TRAIL METHODS ---

    protected function auditModel(): string
    {
        return PemeriksaanProdukCookingMixerFla::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'pemeriksaan-produk-cooking-mixer-fla';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.pemeriksaan-produk-cooking-mixer-fla';
    }

    protected function auditRelations(): array
    {
        return [
            'plan', 'user', 'shift', 'namaFormulaFla.produk', 
            'nomorStepFormulaFla', 'bahanFormulaFla',
            'qcApprover', 'produksiApprover', 'spvApprover'
        ];
    }

    protected function auditSearchColumns(): array
    {
        return ['kode_produksi'];
    }

    protected function auditTimeColumn(): string
    {
        return 'jam';
    }
}
