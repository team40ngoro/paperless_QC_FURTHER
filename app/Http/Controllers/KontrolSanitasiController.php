<?php

namespace App\Http\Controllers;

use App\Models\KontrolSanitasi;
use App\Models\DataShift;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\KontrolSanitasiLog;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KontrolSanitasiTemplateExport;
use App\Imports\KontrolSanitasiImport;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Traits\HasAuditController;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Http\Controllers\Traits\HasExcelImport;
use App\Services\BulkExport\KontrolSanitasiBulkExportService;

class KontrolSanitasiController extends Controller
{
    use HasAuditController, HasNoDataHtmlResponse, HasExcelImport;

    protected function auditModel(): string
    {
        return KontrolSanitasi::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'kontrol-sanitasi';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.kontrol_sanitasi';
    }

    protected function auditRelations(): array
    {
        return ['user', 'plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover'];
    }

    protected function auditSearchColumns(): array
    {
        return ['kode_form'];
    }

    protected function auditDateColumn(): string
    {
        return 'tanggal';
    }

    protected function auditTimeColumn(): string
    {
        return 'jam';
    }

    protected function auditShiftColumn(): string
    {
        return 'shift_id';
    }

    /**
     * Ambil daftar shift sesuai role user.
     * Superadmin melihat semua shift, user lain hanya shift di plan-nya.
     */
    protected function getShiftsForUser($user)
    {
        if ($user->role === 'superadmin') {
            return DataShift::orderBy('shift')->get();
        }

        return DataShift::where('id_plan', $user->id_plan)
            ->orderBy('shift')
            ->get();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Redirect auditor role (role 6) to audit page
        if ($user->id_role == 6) {
            return redirect()->route('kontrol-sanitasi.audit');
        }

        $search = trim((string) $request->input('search', ''));
        $perPage = (int) ($request->input('per_page') ?? 5);
        if (!in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 5;
        }

        // Daftar shift untuk modal Export PDF di view
        $shifts = $this->getShiftsForUser($user);

        $query = KontrolSanitasi::with(['user', 'plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover'])->operasional();

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search !== '') {
            $query->where('tanggal', 'LIKE', '%' . $search . '%');
        }
        $dateRange = $this->applyDateRangeFilter($request, $query);

        // Data datar: satu baris = satu record
        $kontrolSanitasi = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Alias agar view yang memakai $items maupun $kontrolSanitasi sama-sama jalan
        $items = $kontrolSanitasi;

        return view('qc-sistem.kontrol_sanitasi.index', array_merge(
            compact('kontrolSanitasi', 'shifts', 'items', 'search', 'perPage'),
            $dateRange
        ));
    }

    /**
     * Display audit listing (data datar: satu baris = satu record).
     * Menimpa auditIndex dari trait HasAuditController.
     */
    public function auditIndex(Request $request)
    {
        $user = Auth::user();
        $search = trim((string) $request->input('search', ''));
        $perPage = (int) ($request->input('per_page') ?? 5);
        if (!in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 5;
        }

        // Daftar shift untuk modal Cetak PDF di view audit
        $shifts = $this->getShiftsForUser($user);

        // Daftar source_uuid yang sudah di-copy ke audit
        $auditedSourceUuids = KontrolSanitasi::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $query = KontrolSanitasi::with($this->auditRelations())
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
            $query->where(function ($q) use ($search) {
                $q->where('tanggal', 'LIKE', '%' . $search . '%')
                  ->orWhere('kode_form', 'LIKE', '%' . $search . '%');
            });
        }
        $dateRange = $this->applyDateRangeFilter($request, $query);

        $items = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Alias agar view yang memakai $records maupun $items sama-sama jalan
        $records = $items;

        return view('qc-sistem.kontrol_sanitasi.index_audit', array_merge(
            compact('items', 'records', 'search', 'perPage', 'shifts'),
            $dateRange
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        // Get shifts based on user role
        if ($user->role === 'superadmin') {
            $shifts = DataShift::with('plan')->get();
            $plans = Plan::all();
        } else {
            $shifts = DataShift::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('qc-sistem.kontrol_sanitasi.create', compact('shifts', 'plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required',
            'entries' => 'required|array|min:1',
            'entries.*.jam' => 'required',
            'entries.*.suhu_air' => 'required|string|max:255',
            'entries.*.kadar_klorin_food_basin' => 'required|string|max:255',
            'entries.*.kadar_klorin_hand_basin' => 'required|string|max:255',
            'entries.*.hasil_verifikasi' => 'required|string|max:500',
            'entries.*.keterangan' => 'nullable|string|max:500',
        ]);

        $selectedShift = DataShift::findOrFail($request->shift_id);

        if ($user->role !== 'superadmin' && $selectedShift->id_plan !== $user->id_plan) {
            return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.']);
        }

        $tanggalData = Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s');

        $firstEntry = $request->entries[0] ?? [];

        KontrolSanitasi::create([
            'user_id' => $user->id,
            'id_plan' => $selectedShift->id_plan,
            'shift_id' => $request->shift_id,
            'tanggal' => $tanggalData,
            'entries' => $request->entries,
            // Populate individual columns from first entry for DB constraints and legacy support
            'jam' => $firstEntry['jam'] ?? null,
            'suhu_air' => $firstEntry['suhu_air'] ?? null,
            'kadar_klorin_food_basin' => $firstEntry['kadar_klorin_food_basin'] ?? null,
            'kadar_klorin_hand_basin' => $firstEntry['kadar_klorin_hand_basin'] ?? null,
            'hasil_verifikasi' => $firstEntry['hasil_verifikasi'] ?? null,
        ]);

        return redirect()->route('kontrol-sanitasi.index')
            ->with('success', 'Data kontrol sanitasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $kontrolSanitasi = KontrolSanitasi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $kontrolSanitasi->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('kontrol-sanitasi.show', $kontrolSanitasi)) {
            return $redirect;
        }

        return view('qc-sistem.kontrol_sanitasi.show', compact('kontrolSanitasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $kontrolSanitasi = KontrolSanitasi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $kontrolSanitasi->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        $isAuditMode = (bool) $kontrolSanitasi->is_audit;
        $queryParameters = request()->query();
        $hasAuditFlag = array_key_exists('is_audit', $queryParameters);

        if (($isAuditMode && request()->query('is_audit') !== '1') || (!$isAuditMode && $hasAuditFlag)) {
            unset($queryParameters['is_audit'], $queryParameters['uuid']);
            if ($isAuditMode) {
                $queryParameters['is_audit'] = '1';
            }

            return redirect()->route(
                'kontrol-sanitasi.edit',
                array_merge(['uuid' => $kontrolSanitasi->uuid], $queryParameters)
            );
        }

        // Get shifts based on user role
        if ($user->role === 'superadmin') {
            $shifts = DataShift::with('plan')->get();
            $plans = Plan::all();
        } else {
            $shifts = DataShift::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('qc-sistem.kontrol_sanitasi.edit', compact('kontrolSanitasi', 'shifts', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $kontrolSanitasi = KontrolSanitasi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $kontrolSanitasi->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required',
            'entries' => 'required|array|min:1',
            'entries.*.jam' => 'required',
            'entries.*.suhu_air' => 'required|string|max:255',
            'entries.*.kadar_klorin_food_basin' => 'required|string|max:255',
            'entries.*.kadar_klorin_hand_basin' => 'required|string|max:255',
            'entries.*.hasil_verifikasi' => 'required|string|max:500',
            'entries.*.keterangan' => 'nullable|string|max:500',
        ]);

        $selectedShift = DataShift::findOrFail($request->shift_id);

        if ($user->role !== 'superadmin' && $selectedShift->id_plan !== $user->id_plan) {
            return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.']);
        }

        $tanggalData = Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s');

        $kontrolSanitasi->update([
            'id_plan' => $selectedShift->id_plan,
            'shift_id' => $request->shift_id,
            'tanggal' => $tanggalData,
            'entries' => $request->entries,
        ]);

        if ($user->id_role == 6 || $kontrolSanitasi->is_audit) {
            return redirect()->route('kontrol-sanitasi.audit')
                ->with('success', 'Data kontrol sanitasi berhasil diperbarui.');
        }

        return redirect()->route('kontrol-sanitasi.index')
            ->with('success', 'Data kontrol sanitasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $kontrolSanitasi = KontrolSanitasi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $kontrolSanitasi->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $kontrolSanitasi->delete();

        return redirect()->route('kontrol-sanitasi.index')
            ->with('success', 'Data kontrol sanitasi berhasil dihapus.');
    }

    /**
     * Show logs for the specified resource.
     */
    public function showLogs($uuid)
    {
        $item = KontrolSanitasi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $logs = KontrolSanitasiLog::where('kontrol_sanitasi_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.kontrol_sanitasi.logs', compact('item', 'logs'));
    }

    /**
     * Get logs JSON for the specified resource.
     */
    public function getLogsJson($uuid)
    {
        $item = KontrolSanitasi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = KontrolSanitasiLog::where('kontrol_sanitasi_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    /**
     * Export single PDF
     */
    public function exportPdf($uuid)
    {
        try {
            $user = auth()->user();
            $kontrolSanitasi = KontrolSanitasi::with(['plan', 'user', 'shift'])
                ->where('uuid', $uuid)
                ->firstOrFail();

            // Check access for non-superadmin users
            if ($user->role !== 'superadmin' && $kontrolSanitasi->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }

            // Wrap in collection to reuse the same PDF blade
            $data = collect([$kontrolSanitasi]);
            $kode_form = "QF 29/00";

            $pdf = \PDF::loadView('qc-sistem.kontrol_sanitasi.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A4', 'landscape');

            $filename = 'kontrol_sanitasi_' . ($kontrolSanitasi->tanggal ? \Carbon\Carbon::parse($kontrolSanitasi->tanggal)->format('Y-m-d') : date('Y-m-d')) . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
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

            $query = KontrolSanitasi::with(['plan', 'user', 'shift']);

            if ($request->has('is_audit') && $request->is_audit == 1) {
                $query->where('is_audit', true);
            } else {
                $query->operasional();
            }

            $query->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

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

            $kode_form = $request->kode_form;
            $pdf = \PDF::loadView('qc-sistem.kontrol_sanitasi.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A4', 'landscape');

            $filename = 'kontrol_sanitasi_' . date('Y-m-d_H-i-s') . '.pdf';
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

        $kontrolSanitasi = KontrolSanitasi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control following ProdukForming pattern
        $allowedRoles = [
            'qc' => [1, 3, 5], // Role 1&5 (all buttons), Role 3 (QC only)
            'produksi' => [1, 2, 5], // Role 1&5 (all buttons), Role 2 (produksi only)
            'spv' => [1, 4, 5] // Role 1&5 (all buttons), Role 4 (SPV only)
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan approval ini.'
            ], 403);
        }

        // Sequential approval validation (QC → Produksi → SPV)
        if ($type === 'produksi' && !$kontrolSanitasi->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$kontrolSanitasi->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($kontrolSanitasi->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $kontrolSanitasi->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }

    public function downloadTemplate()
    {
        $user = auth()->user();
        $filename = 'template_kontrol_sanitasi.xlsx';

        return Excel::download(new KontrolSanitasiTemplateExport($user), $filename);
    }

    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return redirect()->route('kontrol-sanitasi.index')->with('error', 'File yang diupload harus berupa Excel.');
        }

        $user = auth()->user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('kontrol-sanitasi.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new KontrolSanitasiImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('kontrol-sanitasi.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data Kontrol Sanitasi berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('kontrol-sanitasi.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('kontrol-sanitasi.index')->with('success', $successMessage);
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

            $query = KontrolSanitasi::whereDate('tanggal', $tanggal)
                ->where('shift_id', $shiftId);

            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }

            $items = $query->get();

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
                        "{$type}_approved_by" => $user->id,
                        "{$type}_approved_at" => now()
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Berhasil menyetujui data secara massal.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function bulkExportExcel(Request $request, KontrolSanitasiBulkExportService $service)
    {
        return $service->handle($request, fn ($msg) => $this->buildNoDataHtmlResponse($msg));
    }

    /**
     * Get the model class for Excel import (required by HasExcelImport trait)
     */
    protected function getImportModelClass(): string
    {
        return KontrolSanitasi::class;
    }

    /**
     * Get template filename with timestamp (required by HasExcelImport trait)
     */
    protected function getTemplateFilename(): string
    {
        return 'Template_Kontrol_Sanitasi_' . date('YmdHis') . '.xlsx';
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
            $query = KontrolSanitasi::whereIn('uuid', $uuids)
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
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now(),
            ];

            KontrolSanitasi::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();

            // Update audit copies with same approval data
            KontrolSanitasi::where('is_audit', true)
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
}