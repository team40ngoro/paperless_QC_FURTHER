<?php

namespace App\Http\Controllers;

use App\Models\GmpKaryawan;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\InputArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\GmpKaryawanLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GmpKaryawanTemplateExport;
use App\Imports\GmpKaryawanImport;
use App\Http\Controllers\Traits\HasAuditController;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Http\Controllers\Traits\HasExcelImport;
use App\Services\BulkExport\GmpKaryawanBulkExportService;

class GmpKaryawanController extends Controller
{
    use HasAuditController, HasNoDataHtmlResponse, HasExcelImport;

    protected function auditModel(): string
    {
        return GmpKaryawan::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'gmp-karyawan';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.gmp_karyawan';
    }

    protected function auditRelations(): array
    {
        return ['plan', 'user', 'shift', 'area'];
    }

    protected function auditSearchColumns(): array
    {
        return ['nama_karyawan', 'keterangan'];
    }

    protected function auditTimeColumn(): string
    {
        return 'jam';
    }

    public function auditIndex(Request $request)
    {
        $user = Auth::user();
        $search = trim((string) $request->input('search', ''));
        $perPage = (int) ($request->input('per_page') ?? 5);
        if (!in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 5;
        }

        $auditedSourceUuids = GmpKaryawan::where('is_audit', true)->whereNotNull('source_uuid')->pluck('source_uuid')->toArray();

        $query = GmpKaryawan::with($this->auditRelations())
            ->where(function ($q) use ($auditedSourceUuids) {
                $q->where('is_audit', true)
                  ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                      $subQ->where('is_audit', false);
                      if (!empty($auditedSourceUuids)) {
                          $subQ->whereNotIn('uuid', $auditedSourceUuids);
                      }
                  });
            })
            ->when($user->role !== 'superadmin', function ($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

        if ($search !== '') {
            $query->where('nama_karyawan', 'LIKE', '%' . $search . '%');
        }

        // Data datar: satu baris = satu record
        $this->applyDateRangeFilter(request(), $query);
        $items = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $cachedShifts = DataShift::orderBy('shift', 'asc')->get();
        $areas = InputArea::when($user->role !== 'superadmin', function($q) use ($user) {
            $q->where('id_plan', $user->id_plan);
        })->get();
        $temuanOptions = GmpKaryawan::getTemuanOptions();

        return view('qc-sistem.gmp_karyawan.index_audit', compact('items', 'search', 'perPage', 'cachedShifts', 'areas', 'temuanOptions'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->id_role == 6) {
            return redirect()->route('gmp-karyawan.audit');
        }

        $search = trim((string) $request->input('search', ''));
        $perPage = (int) ($request->input('per_page') ?? 5);
        if (!in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 5;
        }

        $query = GmpKaryawan::with(['plan', 'user', 'shift', 'area'])->operasional();
        
        if ($user->role != 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search !== '') {
            $query->where('nama_karyawan', 'LIKE', '%' . $search . '%');
        }
        
        // Data datar: satu baris = satu record
        $this->applyDateRangeFilter(request(), $query);
        $items = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $cachedShifts = DataShift::orderBy('shift', 'asc')->get();
        $areas = InputArea::where('id_plan', $user->id_plan)->get();
        $temuanOptions = GmpKaryawan::getTemuanOptions();

        return view('qc-sistem.gmp_karyawan.index', compact('items', 'search', 'perPage', 'cachedShifts', 'areas', 'temuanOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get plans based on user role
        if ($user->role == 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
            $areas = InputArea::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $areas = InputArea::where('id_plan', $user->id_plan)->get();
        }
        
        $temuanOptions = GmpKaryawan::getTemuanOptions();
        
        return view('qc-sistem.gmp_karyawan.create', compact('plans', 'shifts', 'areas', 'temuanOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);

        $rules = [
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal'  => 'required|date',
            'entries'  => 'required|array|min:1',
            'entries.*.id_area' => 'required|exists:input_area,id',
            'entries.*.jam' => 'required|date_format:H:i',
            'entries.*.nama_karyawan' => 'required|string|max:255',
            'entries.*.temuan_ketidaksesuaian' => 'required|in:' . implode(',', array_keys(GmpKaryawan::getTemuanOptions())),
            'entries.*.verifikasi' => 'required|in:ok,tidak_ok',
            'entries.*.koreksi_lanjutan' => 'nullable|in:ok,tidak_ok',
            'entries.*.keterangan' => 'nullable|string',
            'entries.*.tindakan_koreksi' => 'nullable|string',
            'entries.*.keterangan_koreksi_lanjutan' => 'nullable|string',
            'entries.*.tindakan_koreksi_lanjutan' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $tanggalData = Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s');

        // Save as ONE JSON record
        $firstEntry = $request->entries[0] ?? [];
        
        GmpKaryawan::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'id_plan' => $user->id_plan,
            'shift_id' => $request->shift_id,
            'tanggal' => $tanggalData,
            'jam' => $firstEntry['jam'] ?? null,
            'id_area' => $firstEntry['id_area'] ?? null,
            'nama_karyawan' => $firstEntry['nama_karyawan'] ?? null,
            'temuan_id' => $firstEntry['temuan_ketidaksesuaian'] ?? null,
            'ket' => $firstEntry['keterangan'] ?? null,
            'verification' => $firstEntry['verifikasi'] ?? null,
            'correction' => $firstEntry['tindakan_koreksi'] ?? null,
            'entries' => $request->entries, // Save the whole array
        ]);

        $count = count($request->entries);

        return redirect()->route('gmp-karyawan.index')
            ->with('success', 'Total ' . $count . ' data GMP Karyawan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        $gmpKaryawan = GmpKaryawan::where('uuid', $uuid)->firstOrFail();
        
        $user = Auth::user();
        if ($user->role != 'superadmin' && $gmpKaryawan->id_plan != $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('gmp-karyawan.show', $gmpKaryawan)) {
            return $redirect;
        }

        $areas = InputArea::all()->pluck('area', 'id');
        $temuanOptions = GmpKaryawan::getTemuanOptions();
        
        return view('qc-sistem.gmp_karyawan.show', compact('gmpKaryawan', 'areas', 'temuanOptions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        $gmpKaryawan = GmpKaryawan::where('uuid', $uuid)->firstOrFail();
        
        $user = Auth::user();
        if ($user->role != 'superadmin' && $gmpKaryawan->id_plan != $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        $isAuditMode = (bool) $gmpKaryawan->is_audit;
        $queryParameters = request()->query();
        $hasAuditFlag = array_key_exists('is_audit', $queryParameters);

        if (($isAuditMode && request()->query('is_audit') !== '1') || (!$isAuditMode && $hasAuditFlag)) {
            unset($queryParameters['is_audit'], $queryParameters['uuid']);
            if ($isAuditMode) {
                $queryParameters['is_audit'] = '1';
            }

            return redirect()->route(
                'gmp-karyawan.edit',
                array_merge(['uuid' => $gmpKaryawan->uuid], $queryParameters)
            );
        }
        
        // Get plans based on user role
        if ($user->role == 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
            $areas = InputArea::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $areas = InputArea::where('id_plan', $user->id_plan)->get();
        }
        $temuanOptions = GmpKaryawan::getTemuanOptions();
        
        return view('qc-sistem.gmp_karyawan.edit', compact('gmpKaryawan', 'plans', 'shifts', 'areas', 'temuanOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        $gmpKaryawan = GmpKaryawan::where('uuid', $uuid)->firstOrFail();
        
        $user = Auth::user();
        if ($user->role != 'superadmin' && $gmpKaryawan->id_plan != $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }
        
        $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);

        $validator = Validator::make($request->all(), [
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal'  => 'required|date',
            'entries'  => 'required|array|min:1',
            'entries.*.id_area' => 'required|exists:input_area,id',
            'entries.*.jam' => 'required|date_format:H:i',
            'entries.*.nama_karyawan' => 'required|string|max:255',
            'entries.*.temuan_ketidaksesuaian' => 'required|in:' . implode(',', array_keys(GmpKaryawan::getTemuanOptions())),
            'entries.*.verifikasi' => 'required|in:ok,tidak_ok',
            'entries.*.koreksi_lanjutan' => 'nullable|in:ok,tidak_ok',
            'entries.*.keterangan' => 'nullable|string',
            'entries.*.tindakan_koreksi' => 'nullable|string',
            'entries.*.keterangan_koreksi_lanjutan' => 'nullable|string',
            'entries.*.tindakan_koreksi_lanjutan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $tanggalData = Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s');

        $firstEntry = $request->entries[0] ?? [];

        $gmpKaryawan->update([
            'shift_id' => $request->shift_id,
            'tanggal' => $tanggalData,
            'jam' => $firstEntry['jam'] ?? null,
            'id_area' => $firstEntry['id_area'] ?? null,
            'nama_karyawan' => $firstEntry['nama_karyawan'] ?? null,
            'temuan_ketidaksesuaian' => $firstEntry['temuan_ketidaksesuaian'] ?? null,
            'keterangan' => $firstEntry['keterangan'] ?? null,
            'tindakan_koreksi' => $firstEntry['tindakan_koreksi'] ?? null,
            'verifikasi' => $firstEntry['verifikasi'] ?? null,
            'koreksi_lanjutan' => $firstEntry['koreksi_lanjutan'] ?? null,
            'entries' => $request->entries,
        ]);

        if ($user->id_role == 6 || $gmpKaryawan->is_audit) {
            return redirect()->route('gmp-karyawan.audit')
                ->with('success', 'Data GMP Karyawan berhasil diperbarui.');
        }

        return redirect()->route('gmp-karyawan.index')
            ->with('success', 'Data GMP Karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        $gmpKaryawan = GmpKaryawan::where('uuid', $uuid)->firstOrFail();
        
        $user = Auth::user();
        if ($user->role != 'superadmin' && $gmpKaryawan->id_plan != $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }
        
        $gmpKaryawan->delete();

        return redirect()->route('gmp-karyawan.index')
            ->with('success', 'Data GMP Karyawan berhasil dihapus.');
    }

    /**
    * Show logs for the specified resource.
    */
    public function showLogs($uuid)
    {
    $item = GmpKaryawan::where('uuid', $uuid)->firstOrFail();
    $user = auth()->user();

    // Authorization check
    if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
        abort(403, 'Unauthorized action.');
    }

    $logs = GmpKaryawanLog::where('gmp_karyawan_id', $item->id)
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('qc-sistem.gmp_karyawan.logs', compact('item', 'logs'));
    }

    /**
    * Get logs JSON for the specified resource.
    */
    public function getLogsJson($uuid)
    {
    $item = GmpKaryawan::where('uuid', $uuid)->firstOrFail();
    $user = auth()->user();

    // Authorization check
    if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $logs = GmpKaryawanLog::where('gmp_karyawan_id', $item->id)
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($logs);
    }
        /**
     * Export PDF with filters
     */
    public function exportPdf(Request $request, $uuid = null)
    {
        try {
            $request->validate([
                'tanggal_awal' => 'nullable|date',
                'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
                'shift_id' => 'nullable|integer',
                'kode_form' => 'nullable|string|max:50'
            ]);

            $user = auth()->user();
            
            $query = GmpKaryawan::with(['area', 'shift', 'user']);

            if ($uuid) {
                // Export single record by UUID
                $query->where('uuid', $uuid);
            } else {
                // Export bulk by filters
                if ($request->has('is_audit') && $request->is_audit == 1) {
                    $query->where('is_audit', true);
                } else {
                    $query->operasional();
                }
                
                $query->whereHas('user', function ($q) use ($user) {
                    if ($user->role !== 'superadmin') {
                        $q->where('id_plan', $user->id_plan);
                    }
                });

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

            $kode_form = $uuid ? ($data->first()->kode_form ?? 'QF 28/00') : ($request->kode_form ?? 'QF 28/00');
            $areas = InputArea::all()->pluck('area', 'id');
            $temuanOptions = GmpKaryawan::getTemuanOptions();

            $pdf = PDF::loadView('qc-sistem.gmp_karyawan.export_pdf', compact('data', 'kode_form', 'areas', 'temuanOptions'));
            $pdf->setPaper('A4', 'landscape');
            
            $filename = 'gmp_karyawan_' . date('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve data by specific role
     */
    public function approve(Request $request, $uuid)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv'
            ]);

            $gmpKaryawan = GmpKaryawan::where('uuid', $uuid)->firstOrFail();
            $user = Auth::user();
            $userRole = $user->id_role ?? null;

            // Check authorization based on user role and approval type
            $type = $request->type;
            
            // Role-based authorization
            switch ($type) {
                case 'qc':
                    if (!in_array($userRole, [1, 3, 5])) { // Superadmin, QC, atau role 5
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai QC'
                        ], 403);
                    }
                    break;
                case 'produksi':
                    if ($userRole != 2) { // Hanya role Produksi
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai Produksi'
                        ], 403);
                    }
                    // Check if QC approval exists first
                    if (!$gmpKaryawan->approved_by_qc) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data harus disetujui oleh QC terlebih dahulu'
                        ], 400);
                    }
                    break;
                case 'spv':
                    if ($userRole != 4) { // Hanya role SPV
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai SPV'
                        ], 403);
                    }
                    // Check if Produksi approval exists first
                    if (!$gmpKaryawan->approved_by_produksi) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data harus disetujui oleh Produksi terlebih dahulu'
                        ], 400);
                    }
                    break;
            }

            // Check if already approved
            $approvalField = "approved_by_{$type}";
            if ($gmpKaryawan->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui sebelumnya'
                ], 400);
            }

            // Update approval
            $updateData = [
                $approvalField => true,
                "approved_by_{$type}_at" => now(),
                "approved_by_{$type}_user_id" => $user->id
            ];

            $gmpKaryawan->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui',
                'data' => $gmpKaryawan->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        $user = auth()->user();
        $filename = 'template_gmp_karyawan.xlsx';

        return Excel::download(new GmpKaryawanTemplateExport($user), $filename);
    }

    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return redirect()->route('gmp-karyawan.index')->with('error', 'File yang diupload harus berupa Excel.');
        }

        $user = auth()->user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('gmp-karyawan.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new GmpKaryawanImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('gmp-karyawan.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift/Area sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data GMP Karyawan berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('gmp-karyawan.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('gmp-karyawan.index')->with('success', $successMessage);
    }
    public function bulkApprove(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'shift_id' => 'required|exists:data_shift,id',
                'type' => 'required|in:qc,produksi,spv'
            ]);

            $user = Auth::user();
            $type = $request->type;
            $tanggal = $request->tanggal;
            $shiftId = $request->shift_id;

            $areaId = $request->id_area;

            $items = GmpKaryawan::whereDate('tanggal', $tanggal)
                ->where('shift_id', $shiftId)
                ->where('id_area', $areaId)
                ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
                ->get();

            if ($items->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data untuk disetujui.']);
            }

            foreach ($items as $item) {
                // Sequential validation
                if ($type === 'produksi' && !$item->approved_by_qc) continue;
                if ($type === 'spv' && !$item->approved_by_produksi) continue;

                $approvalField = "approved_by_{$type}";
                if (!$item->$approvalField) {
                    $item->update([
                        $approvalField => true,
                        "approved_by_{$type}_at" => now(),
                        "approved_by_{$type}_user_id" => $user->id,
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Berhasil menyetujui data secara massal.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function bulkExportExcel(Request $request, GmpKaryawanBulkExportService $service)
    {
        return $service->handle($request, fn ($msg) => $this->buildNoDataHtmlResponse($msg));
    }

    public function bulkApproveSelected(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv',
                'uuids' => 'required|array|min:1',
                'uuids.*' => 'required|uuid'
            ]);

            $user = Auth::user();
            $type = $request->input('type');
            $uuids = $request->input('uuids', []);

            // Role-based permission check
            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5]
            ];

            if (!in_array($user->id_role, $allowedRoles[$type] ?? [])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki otoritas untuk verifikasi ini.'
                ], 403);
            }

            // Find records - ONLY unapproved ones (smart auto-skip)
            $query = GmpKaryawan::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            $itemsToUpdate = $query->get();
            $count = $itemsToUpdate->count();

            if ($count === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Semua data sudah diverifikasi sebelumnya.'
                ]);
            }

            // Update operasional records
            $updateData = [
                "approved_by_{$type}" => true,
                "approved_by_{$type}_user_id" => $user->id,
                "approved_by_{$type}_at" => now(),
            ];

            GmpKaryawan::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();

            // Update audit copies with same approval data
            GmpKaryawan::where('is_audit', true)
                ->whereIn('source_uuid', $updatedSourceUuids)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => "Berhasil verifikasi {$count} data " . strtoupper($type)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the model class for Excel import (required by HasExcelImport trait)
     */
    protected function getImportModelClass(): string
    {
        return GmpKaryawan::class;
    }

    /**
     * Get template filename with timestamp (required by HasExcelImport trait)
     */
    protected function getTemplateFilename(): string
    {
        return 'Template_GMP_Karyawan_' . date('YmdHis') . '.xlsx';
    }
}