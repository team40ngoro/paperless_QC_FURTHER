<?php

namespace App\Http\Controllers;

use App\Models\Seasoning;
use App\Models\SeasoningLog;
use App\Models\DataShift;
use App\Models\Plan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\DataSeasoning;
// use PDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SeasoningTemplateExport;
use App\Imports\SeasoningImport;
use App\Http\Controllers\Traits\HasAuditController;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Http\Controllers\Traits\HasExcelImport;
use App\Services\BulkExport\SeasoningBulkExportService;

class SeasoningController extends Controller
{
    use HasAuditController, HasNoDataHtmlResponse, HasExcelImport;

    protected function auditModel(): string
    {
        return Seasoning::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'seasoning';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.seasoning';
    }

    protected function auditRelations(): array
    {
        return ['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'createdBy', 'originalVersion', 'latestQcVerification'];
    }

    protected function auditSearchColumns(): array
    {
        return ['nama_rm', 'kode_produksi', 'keterangan'];
    }

    protected function auditDateColumn(): string
    {
        return 'tanggal';
    }

    protected function auditTimeColumn(): string
    {
        return 'jam';
    }

    protected function auditShiftRelation(): string
    {
        return 'shift';
    }

    protected function auditShiftColumn(): string
    {
        return 'shift_id';
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Auditor langsung diarahkan ke halaman audit
        if ($user->id_role == 6) {
            return redirect()->route('seasoning.audit');
        }

        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        $query = Seasoning::with(['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'createdBy', 'auditVersion', 'latestQcVerification'])
            ->operasional() // filter is_audit = false via HasAudit scope
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_rm', 'LIKE', '%' . $search . '%')
                  ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%')
                  ->orWhere('keterangan', 'LIKE', '%' . $search . '%');
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
                         ->orderBy('jam', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->orderBy('id', 'desc')
                         ->paginate($perPage);
        
        $groupedData = $records->getCollection()->groupBy(function($item) {
            $dateStr = $item->tanggal ? (is_string($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : $item->tanggal->format('Y-m-d')) : 'no_date';
            return $dateStr . '_' . ($item->shift_id ?? 'no_shift');
        });
        
        $records->setCollection($groupedData);

        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.seasoning.index', compact('records', 'search', 'perPage', 'shifts'));
    }

    public function searchAjax(Request $request)
    {
        try {
            $user = auth()->user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 5);
            $page = (int) $request->get('page', 1);

            $query = Seasoning::with(['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'createdBy', 'auditVersion', 'latestQcVerification'])
                ->operasional() // filter is_audit = false via HasAudit scope
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_rm', 'LIKE', '%' . $search . '%')
                      ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%')
                      ->orWhere('keterangan', 'LIKE', '%' . $search . '%');
                });
            }

            $records = $query->orderBy('tanggal', 'desc')
                             ->orderBy('jam', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->orderBy('id', 'desc')
                             ->paginate($perPage, ['*'], 'page', $page);

            $total = $records->total();
            $groupedData = $records->getCollection()->groupBy(function($item) {
                $dateStr = $item->tanggal ? (is_string($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : $item->tanggal->format('Y-m-d')) : 'no_date';
                return $dateStr . '_' . ($item->shift_id ?? 'no_shift');
            });
            $records->setCollection($groupedData);

            return response()->json([
                'html' => view('qc-sistem.seasoning._table', compact('records'))->render(),
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => (int) ceil($total / $perPage),
                    'per_page' => $perPage,
                    'total' => $total,
                    'from' => $records->firstItem(),
                    'to' => $records->lastItem(),
                    'has_pages' => $total > $perPage
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
            $dataSeasoning = DataSeasoning::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $dataSeasoning = DataSeasoning::where('id_plan', $user->id_plan)->get();
        }
        
        return view('qc-sistem.seasoning.create', compact('shifts', 'plans', 'dataSeasoning'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // Common fields (shared across all entries)
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'shift_id' => 'required|integer',
            
            // Entry arrays (repeater fields)
            'entries' => 'required|array|min:1',
            'entries.*.nama_rm' => 'required|string',
            'entries.*.kode_produksi' => 'required|string',
            'entries.*.berat' => 'required|string',
            'entries.*.kemasan' => 'required|string',
            'entries.*.keterangan' => 'nullable|string',
        ]);

        $user = auth()->user();
        $successCount = 0;
        $errors = [];

        try {
            foreach ($data['entries'] as $index => $entry) {
                try {
                    Seasoning::create([
                        'uuid' => Str::uuid(),
                        'id_plan' => $user->id_plan,
                        'created_by' => $user->id,
                        'tanggal' => Carbon::parse($data['tanggal'])->setTimeFrom(now())->format('Y-m-d H:i:s'),
                        'jam' => $data['jam'],
                        'shift_id' => $data['shift_id'],
                        'nama_rm' => $entry['nama_rm'],
                        'kode_produksi' => $entry['kode_produksi'],
                        'berat' => $entry['berat'],
                        'sensori' => '-',
                        'kemasan' => $entry['kemasan'],
                        'keterangan' => $entry['keterangan'] ?? null,
                    ]);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Entry " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            if ($successCount > 0) {
                $message = "Berhasil menyimpan {$successCount} data seasoning";
                if (!empty($errors)) {
                    $message .= ". Beberapa data gagal disimpan: " . implode(', ', $errors);
                }
                return redirect()->route('seasoning.index')->with('success', $message);
            } else {
                return redirect()->back()->withErrors($errors)->withInput();
            }

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Menampilkan detail data seasoning
     */
    public function show($uuid)
    {
        $seasoning = Seasoning::with(['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'createdBy', 'qcVerifications.verifiedBy'])
            ->where('uuid', $uuid)->firstOrFail();
        
        $user = auth()->user();
        if ($user->role !== 'superadmin' && $seasoning->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('seasoning.show', $seasoning)) {
            return $redirect;
        }

        return view('qc-sistem.seasoning.show', compact('seasoning'));
    }

    public function edit($uuid)
    {
        $seasoning = Seasoning::where('uuid', $uuid)->firstOrFail();
        
        // Check edit time limit using global config
        $user = auth()->user();
        if ($redirect = $this->redirectToCanonicalAuditUrl('seasoning.edit', $seasoning)) {
            return $redirect;
        }

        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($seasoning->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('seasoning.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }
        
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
            $dataSeasoning = DataSeasoning::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $dataSeasoning = DataSeasoning::where('id_plan', $user->id_plan)->get();
        }
        
        return view('qc-sistem.seasoning.edit', compact('seasoning', 'shifts', 'plans', 'dataSeasoning'));
    }
    public function update(Request $request, $uuid)
    {
        $seasoning = Seasoning::where('uuid', $uuid)->firstOrFail();

        // Check edit time limit using global config
        $user = auth()->user();
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($seasoning->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('seasoning.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        $validated = $request->validate([
            'nama_rm' => 'required|string',
            'kode_produksi' => 'required|string',
            'shift_id' => 'required',
            'tanggal_seasoning' => 'required|date',
            'jam_seasoning' => 'required|date_format:H:i',
            'berat' => 'required|string',
            'kemasan' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $seasoning->update([
            'shift_id' => $request->shift_id,
            'nama_rm' => $request->nama_rm,
            'kode_produksi' => $request->kode_produksi,
            'berat' => $request->berat,
            'tanggal' => Carbon::parse($request->tanggal_seasoning)->setTimeFrom(now())->format('Y-m-d H:i:s'),
            'jam' => $request->jam_seasoning,
            'kemasan' => $request->kemasan,
            'keterangan' => $request->keterangan,
        ]);
        // var_dump($data);
        // exit;
        // $seasoning->update($data);
        return redirect()->route('seasoning.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $seasoning = Seasoning::where('uuid', $uuid)->firstOrFail();
        $seasoning->delete();
        return redirect()->route('seasoning.index')->with('success', 'Data berhasil dihapus');
    }

    /**
     * Menampilkan riwayat log perubahan data
     */
    public function showLogs($uuid)
    {
        $item = Seasoning::where('uuid', $uuid)->firstOrFail();
        
        // Check authorization
        $user = auth()->user();
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized');
        }
        
        $logs = SeasoningLog::where('seasoning_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->paginate(5);
        
        return view('qc-sistem.seasoning.logs', compact('item', 'logs'));
    }

    /**
     * API untuk mendapatkan log dalam format JSON (untuk AJAX)
     */
    public function getLogsJson($uuid)
    {
        $item = Seasoning::where('uuid', $uuid)->firstOrFail();
        
        // Check authorization
        $user = auth()->user();
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $logs = SeasoningLog::where('seasoning_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($log) {
                        return [
                            'id' => $log->id,
                            'tanggal' => $log->created_at->format('d/m/Y H:i:s'),
                            'user' => $log->user_name,
                            'role' => $log->user_role,
                            'aksi' => ucfirst($log->aksi),
                            'field_diubah' => implode(', ', $log->field_yang_diubah ?? []),
                            'deskripsi' => $log->deskripsi_perubahan,
                            'ip_address' => $log->ip_address
                        ];
                    });
        
        return response()->json($logs);
    }

    /**
     * Simpan verifikasi QC untuk data Seasoning.
     */
    public function storeQcVerification(Request $request, $uuid)
    {
        $item = Seasoning::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        if (! $user || (! $user->isQcInspector() && ! $user->isSuperAdmin())) {
            abort(403, 'Hanya role QC Inspector yang dapat melakukan verifikasi ini.');
        }

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'data_status' => 'required|in:OK,NOT_OK',
            'sensori_status' => 'required|in:OK,NOT_OK',
            'kemasan_status' => 'required|in:OK,NOT_OK',
            'kondisi_fisik_status' => 'required|in:OK,NOT_OK',
            'note' => 'nullable|string|max:1000',
        ]);

        $latest = $item->latestQcVerification;
        $oldSensori = $item->sensori;

        $verification = $item->qcVerifications()->create([
            'seasoning_uuid' => $item->uuid,
            'data_status' => $validated['data_status'],
            'sensori_status' => $validated['sensori_status'],
            'kemasan_status' => $validated['kemasan_status'],
            'kondisi_fisik_status' => $validated['kondisi_fisik_status'],
            'note' => $validated['note'] ?? null,
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        $item->update([
            'approved_by_qc' => true,
            'qc_approved_by' => $user->id,
            'qc_approved_at' => now(),
            'sensori' => $validated['sensori_status'] === 'OK' ? '✔' : '✘',
        ]);

        SeasoningLog::create([
            'uuid' => (string) Str::uuid(),
            'seasoning_id' => $item->id,
            'seasoning_uuid' => $item->uuid,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role ?? 'QC',
            'aksi' => 'qc_verification',
            'field_yang_diubah' => ['qc_data_status', 'qc_sensori_status', 'qc_kemasan_status', 'qc_kondisi_fisik_status', 'qc_verification_note', 'sensori'],
            'nilai_lama' => [
                'qc_data_status' => $latest->data_status ?? '-',
                'qc_sensori_status' => $latest->sensori_status ?? '-',
                'qc_kemasan_status' => $latest->kemasan_status ?? '-',
                'qc_kondisi_fisik_status' => $latest->kondisi_fisik_status ?? '-',
                'qc_verification_note' => $latest->note ?? '-',
                'sensori' => $oldSensori,
            ],
            'nilai_baru' => [
                'qc_data_status' => $verification->data_status,
                'qc_sensori_status' => $verification->sensori_status,
                'qc_kemasan_status' => $verification->kemasan_status,
                'qc_kondisi_fisik_status' => $verification->kondisi_fisik_status,
                'qc_verification_note' => $verification->note ?? '-',
                'sensori' => $item->sensori,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'keterangan' => $validated['note'] ?? 'Verifikasi QC',
        ]);

        return redirect()->back()->with('success', 'Verifikasi QC berhasil disimpan.');
    }

    /**
     * Tampilkan log verifikasi QC untuk data Seasoning.
     */
    public function qcVerificationLogs($uuid)
    {
        $item = Seasoning::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403, 'Unauthorized access');
        }

        $verifications = $item->qcVerifications()
            ->with('verifiedBy')
            ->orderByDesc('verified_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('qc-sistem.seasoning.qc-verification-logs', compact('item', 'verifications'));
    }

    /**
     * Bulk export PDF with filters
     */
    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal_mulai' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
                'shift_id' => 'nullable|integer',
                'kode_form' => 'required|string|max:50'
            ]);

            $user = auth()->user();
            
            $query = Seasoning::with(['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover'])
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            $this->applyAuditPdfScope($query, $request);

            // Apply filters
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai)
                  ->whereDate('tanggal', '<=', $request->tanggal_akhir);
            
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
                
                if ($request->tanggal_mulai && $request->tanggal_akhir) {
                    $filterInfo[] = 'Periode: ' . Carbon::parse($request->tanggal_mulai)->format('d-m-Y') . ' sampai ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
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
            $filters = [
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir,
                'shift_id' => $request->shift_id,
                'kode_form' => $request->kode_form
            ];

            $kode_form = $request->kode_form;
            $pdf = \PDF::loadView('qc-sistem.seasoning.export_pdf', compact('data', 'kode_form', 'filters'));
            $pdf->setPaper('A4', 'landscape');
            
            $filename = 'seasoning_' . date('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save kode form for specific record
     */
    public function saveKode(Request $request)
    {
        try {
            $request->validate([
                'uuid' => 'required|string',
                'kode_form' => 'required|string|max:50'
            ]);

            $item = Seasoning::where('uuid', $request->uuid)->firstOrFail();
            
            // Update kode form
            $item->update([
                'kode_form' => $request->kode_form
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kode form berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan kode form: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve data
     */
    public function approve(Request $request, $uuid)
    {
        try {
            $request->validate([
                'type' => 'required|in:spv'
            ]);

            $user = auth()->user();
            $userRole = $user->id_role;
            $type = $request->type;

            // Approval SPV dilakukan di halaman detail (show).
            $allowedRoles = [
                'spv' => [4, 5]
            ];

            if (!in_array($userRole, $allowedRoles[$type] ?? [])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melakukan persetujuan ini'
                ], 403);
            }

            $item = Seasoning::where('uuid', $uuid)->firstOrFail();

            if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke data ini'
                ], 403);
            }

            // Sequential approval validation: QC harus verifikasi terlebih dahulu.
            if (!$item->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus diverifikasi oleh QC terlebih dahulu sebelum dapat disetujui oleh SPV'
                ], 422);
            }

            // Check if already approved
            $approvalField = 'approved_by_' . $type;
            if ($item->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui oleh ' . strtoupper($type)
                ], 422);
            }
            
            // Update field approval yang sesuai
            $approverField = $type . '_approved_by';
            $approvalDateField = $type . '_approved_at';
            
            $item->update([
                $approvalField => true,
                $approverField => $user->id,
                $approvalDateField => now()
            ]);

            // Log approval activity
            SeasoningLog::create([
                'uuid' => Str::uuid(),
                'seasoning_id' => $item->id,
                'seasoning_uuid' => $item->uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role ?? 'Unknown',
                'aksi' => 'approval',
                'field_yang_diubah' => [$approvalField],
                'nilai_lama' => [$approvalField => false],
                'nilai_baru' => [$approvalField => true],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'keterangan' => 'Approved by ' . strtoupper($type)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui oleh ' . strtoupper($type)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        $user = auth()->user();
        $filename = 'template_seasoning.xlsx';

        return Excel::download(new SeasoningTemplateExport($user), $filename);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $user = auth()->user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('seasoning.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new SeasoningImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('seasoning.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift/Seasoning sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data seasoning berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('seasoning.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('seasoning.index')->with('success', $successMessage);
    }
    /**
     * Bulk approve per shift sekuensial
     */
    public function bulkApprove(Request $request)
    {
        try {
            $user = auth()->user();
            $type = $request->input('type'); // qc, produksi, spv
            $tanggal = $request->input('tanggal');
            $shift_id = $request->input('shift_id');

            // Role check
            $userRole = $user->id_role;
            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5]
            ];

            if (!in_array($userRole, $allowedRoles[$type] ?? [])) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki otoritas untuk verifikasi ini.'], 403);
            }

            // Find records in this shift
            $query = Seasoning::whereDate('tanggal', $tanggal)
                ->where('shift_id', $shift_id)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            $items = $query->get();
            if ($items->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan untuk shift ini.'], 404);
            }

            // Check Sequential
            if ($type === 'produksi') {
                if ($items->contains(fn($i) => !$i->approved_by_qc)) {
                    return response()->json(['success' => false, 'message' => 'Verifikasi QC belum lengkap untuk shift ini.'], 422);
                }
            } elseif ($type === 'spv') {
                if ($items->contains(fn($i) => !$i->approved_by_produksi)) {
                    return response()->json(['success' => false, 'message' => 'Verifikasi Produksi belum lengkap untuk shift ini.'], 422);
                }
            }

            // Update
            $updateData = [
                'approved_by_' . $type => true,
                $type . '_approved_by' => $user->id,
                $type . '_approved_at' => now(),
            ];

            $query->update($updateData);
            // SYNC to audit versions: Update audit records dengan source_uuid yang sama
            foreach ($items as $item) {
                if ($item->auditVersion) {
                    $item->auditVersion->update($updateData);
                }
            }
            
            // Log
            foreach ($items as $item) {
                SeasoningLog::create([
                    'uuid' => (string) Str::uuid(),
                    'seasoning_id' => $item->id,
                    'seasoning_uuid' => $item->uuid,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role ?? 'Unknown',
                    'aksi' => 'bulk_approval',
                    'field_yang_diubah' => ['approved_by_' . $type],
                    'nilai_lama' => ['approved_by_' . $type => false],
                    'nilai_baru' => ['approved_by_' . $type => true],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'keterangan' => 'Bulk Approved by ' . strtoupper($type)
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Berhasil verifikasi masal ' . strtoupper($type)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Bulk approve berdasarkan data yang dipilih via checkbox (per-row selection).
     * Ini adalah pengganti sistem bulkApprove() lama (range-date/per-shift).
     * - Tidak ada sequential check: QC, Produksi, SPV independen.
     * - Smart auto-skip: hanya update data yang belum approved_by_{type}.
     * - Approver original TIDAK tertimpa.
     */
    public function bulkApproveSelected(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv',
                'uuids' => 'required|array|min:1',
                'uuids.*' => 'required|uuid'
            ]);

            $user = auth()->user();
            $type = $request->input('type'); // qc, produksi, spv
            $uuids = $request->input('uuids', []);

            // Role check
            $userRole = $user->id_role;
            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5]
            ];

            if (!in_array($userRole, $allowedRoles[$type] ?? [])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki otoritas untuk verifikasi ini.'
                ], 403);
            }

            $approvalField = 'approved_by_' . $type;

            // Find records - HANYA yang belum approved_by_{type} (smart auto-skip)
            $query = Seasoning::whereIn('uuid', $uuids)
                ->where($approvalField, false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            $itemsToUpdate = $query->get();
            $count = $itemsToUpdate->count();

            if ($count === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Semua data yang dipilih sudah diverifikasi sebelumnya.'
                ]);
            }

            // Update hanya data yang belum approved (approver lama tidak tertimpa
            // karena where($approvalField, false) mengecualikan data yang sudah approved)
            $updateData = [
                $approvalField => true,
                $type . '_approved_by' => $user->id,
                $type . '_approved_at' => now(),
            ];

            Seasoning::whereIn('uuid', $uuids)
                ->where($approvalField, false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // SYNC to audit versions: Update audit records dengan source_uuid yang sama
            foreach ($itemsToUpdate as $item) {
                if ($item->auditVersion) {
                    $item->auditVersion->update($updateData);
                }
            }

            // Log setiap approval
            foreach ($itemsToUpdate as $item) {
                SeasoningLog::create([
                    'uuid' => (string) Str::uuid(),
                    'seasoning_id' => $item->id,
                    'seasoning_uuid' => $item->uuid,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role ?? 'Unknown',
                    'aksi' => 'bulk_approval_selected',
                    'field_yang_diubah' => [$approvalField],
                    'nilai_lama' => [$approvalField => false],
                    'nilai_baru' => [$approvalField => true],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'keterangan' => 'Bulk Approved Selected by ' . strtoupper($type)
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil verifikasi ' . $count . ' data ' . strtoupper($type)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses persetujuan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk export Excel with filters
     */
    public function bulkExportExcel(Request $request)
    {
        return (new SeasoningBulkExportService())->handle($request, fn($msg) => $this->noDataHtmlResponse($msg));
    }

    /**
     * ============================================================
     * EXCEL IMPORT METHODS (via HasExcelImport trait)
     * ============================================================
     */

    /**
     * Get the model class for import
     */
    protected function getImportModelClass(): string
    {
        return Seasoning::class;
    }

    /**
     * Get the template filename
     */
    protected function getTemplateFilename(): string
    {
        return 'Seasoning_Template.xlsx';
    }
}