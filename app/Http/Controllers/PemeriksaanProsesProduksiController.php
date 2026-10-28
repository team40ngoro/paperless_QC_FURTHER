<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanProsesProduksi;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\InputArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\PemeriksaanProsesProduksiLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PemeriksaanProsesProduksiTemplateExport;
use App\Imports\PemeriksaanProsesProduksiImport;
use App\Http\Controllers\Traits\HasAuditController;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Http\Controllers\Traits\HasExcelImport;
use App\Services\BulkExport\PemeriksaanProsesProduksiBulkExportService;

class PemeriksaanProsesProduksiController extends Controller
{
    use HasAuditController, HasNoDataHtmlResponse, HasExcelImport;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->id_role == 6) {
            return redirect()->route('pemeriksaan-proses-produksi.audit');
        }

        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        $query = PemeriksaanProsesProduksi::with(['plan', 'shift', 'area', 'user', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->where('is_audit', false)
            ->filterByRole();

        if($search) {
            $query->where('uraian_permasalahan', 'LIKE', '%' . $search . '%');
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
                        ->orderBy('jam', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate($perPage);

        return view('qc-sistem.pemeriksaan_proses_produksi.index', compact('records', 'search', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        
        // Filter data berdasarkan role
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $areas = InputArea::all();
            $shifts = DataShift::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $areas = InputArea::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        
        $ketidaksesuaianOptions = PemeriksaanProsesProduksi::getKetidaksesuaianOptions();
        $disposisiOptions = PemeriksaanProsesProduksi::getDisposisiOptions();
        
        return view('qc-sistem.pemeriksaan_proses_produksi.create', compact('plans', 'shifts', 'areas',  'ketidaksesuaianOptions', 'disposisiOptions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'id_area'  => 'required|exists:input_area,id',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal'  => 'required|date',
            'jam'      => 'required',
            'ketidaksesuaian'    => 'required|in:' . implode(',', array_keys(PemeriksaanProsesProduksi::getKetidaksesuaianOptions())),
            'uraian_permasalahan' => 'required|string',
            'analisa_penyebab'   => 'required|string',
            'disposisi'          => 'nullable|string',
            'tindakan_koreksi'   => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['uuid']    = Str::uuid();
        $data['user_id'] = $user->id;
        $data['id_plan'] = $user->id_plan;
        $data['tanggal'] = \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s');
        $data['jam']     = $request->jam;
        PemeriksaanProsesProduksi::create($data);

        return redirect()->route('pemeriksaan-proses-produksi.index')
            ->with('success', 'Data Pemeriksaan Proses Produksi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $data = PemeriksaanProsesProduksi::with(['plan', 'shift', 'area', 'user'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Cek akses data
        if (!$data->canAccess()) {
            abort(403, 'Unauthorized access');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('pemeriksaan-proses-produksi.show', $data)) {
            return $redirect;
        }

        return view('qc-sistem.pemeriksaan_proses_produksi.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $data = PemeriksaanProsesProduksi::where('uuid', $uuid)->firstOrFail();

        // Cek akses data
        if (!$data->canAccess()) {
            abort(403, 'Unauthorized access');
        }

        $user = Auth::user();
        $isAuditMode = (bool) $data->is_audit;
        $queryParameters = request()->query();
        $hasAuditFlag = array_key_exists('is_audit', $queryParameters);

        if (($isAuditMode && request()->query('is_audit') !== '1') || (!$isAuditMode && $hasAuditFlag)) {
            unset($queryParameters['is_audit'], $queryParameters['uuid']);
            if ($isAuditMode) {
                $queryParameters['is_audit'] = '1';
            }

            return redirect()->route(
                'pemeriksaan-proses-produksi.edit',
                array_merge(['uuid' => $data->uuid], $queryParameters)
            );
        }
        
        // Filter data berdasarkan role
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $areas = InputArea::all();
            $shifts = DataShift::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $areas = InputArea::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        $ketidaksesuaianOptions = PemeriksaanProsesProduksi::getKetidaksesuaianOptions();
        $disposisiOptions = PemeriksaanProsesProduksi::getDisposisiOptions();

        return view('qc-sistem.pemeriksaan_proses_produksi.edit', compact('data', 'plans', 'shifts', 'areas', 'ketidaksesuaianOptions', 'disposisiOptions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        $data = PemeriksaanProsesProduksi::where('uuid', $uuid)->firstOrFail();

        // Cek akses data
        if (!$data->canAccess()) {
            abort(403, 'Unauthorized access');
        }

        $validator = Validator::make($request->all(), [
            'id_area' => 'required|exists:input_area,id',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'ketidaksesuaian' => 'required|in:' . implode(',', array_keys(PemeriksaanProsesProduksi::getKetidaksesuaianOptions())),
            'uraian_permasalahan' => 'required|string',
            'analisa_penyebab' => 'required|string',
            'disposisi' => 'nullable|string',
            'tindakan_koreksi' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = $request->except(['uuid', 'user_id', 'id_plan']);
        $data->update($updateData);

        $user = Auth::user();
        if ($user->id_role == 6 || $data->is_audit) {
            return redirect()->route('pemeriksaan-proses-produksi.audit')
                ->with('success', 'Data Pemeriksaan Proses Produksi berhasil diupdate.');
        }

        return redirect()->route('pemeriksaan-proses-produksi.index')
            ->with('success', 'Data Pemeriksaan Proses Produksi berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $data = PemeriksaanProsesProduksi::where('uuid', $uuid)->firstOrFail();

        // Cek akses data
        if (!$data->canAccess()) {
            abort(403, 'Unauthorized access');
        }

        $data->delete();

        return redirect()->route('pemeriksaan-proses-produksi.index')
            ->with('success', 'Data Pemeriksaan Proses Produksi berhasil dihapus.');
    }
    /**
     * Show logs for the specified resource.
     */
    public function showLogs($uuid)
    {
        $item = PemeriksaanProsesProduksi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if (!$item->canAccess($user)) {
            abort(403, 'Unauthorized action.');
        }

        $logs = PemeriksaanProsesProduksiLog::where('pemeriksaan_proses_produksi_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.pemeriksaan_proses_produksi.logs', compact('item', 'logs'));
    }

    /**
     * Get logs JSON for the specified resource.
     */
    public function getLogsJson($uuid)
    {
        $item = PemeriksaanProsesProduksi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if (!$item->canAccess($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = PemeriksaanProsesProduksiLog::where('pemeriksaan_proses_produksi_id', $item->id)
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
                'id_area' => 'nullable|integer',
                'kode_form' => 'nullable|string|max:50'
            ]);

            $user = Auth::user();
            
            $query = PemeriksaanProsesProduksi::with(['plan', 'user', 'shift', 'area', 'qcApprover', 'produksiApprover', 'spvApprover'])
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($request->has('is_audit') && $request->is_audit == 1) {
                $query->where('is_audit', true);
            } else {
                $query->operasional();
            }

            // Apply filters
            if ($uuid) {
                $query->where('uuid', $uuid);
            } else {
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
                
                if ($request->id_area) {
                    $query->where('id_area', $request->id_area);
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
                    $filterInfo[] = 'Tanggal: ' . Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
                } elseif ($request->tanggal_awal) {
                    $filterInfo[] = 'Tanggal: ' . Carbon::parse($request->tanggal_awal)->format('d-m-Y');
                }
                if ($request->shift_id) {
                    $shift = DataShift::find($request->shift_id);
                    $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
                }
                if ($request->id_area) {
                    $area = InputArea::find($request->id_area);
                    $filterInfo[] = 'Area: ' . ($area ? $area->area : 'Unknown');
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

            $tanggalFilter = $uuid ? $data->first()->tanggal : 'Semua Tanggal';
            if (!$uuid && $request->tanggal_awal && $request->tanggal_akhir) {
                $tanggalFilter = Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
            } elseif (!$uuid && $request->tanggal_awal) {
                $tanggalFilter = Carbon::parse($request->tanggal_awal)->format('d-m-Y');
            }

            $filters = [
                'tanggal' => $tanggalFilter,
                'shift_id' => $uuid ? $data->first()->shift_id : $request->shift_id,
                'id_area' => $uuid ? $data->first()->id_area : $request->id_area,
                'kode_form' => $uuid ? ($data->first()->kode_form ?? 'QF 24/00') : ($request->kode_form ?? 'QF 24/00')
            ];

            // Generate PDF
            $pdf = Pdf::loadView('qc-sistem.pemeriksaan_proses_produksi.export_pdf', compact('data', 'filters'))
                ->setPaper('a4', 'portrait');

            $filename = 'pemeriksaan_proses_produksi_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
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

        $pemeriksaanProsesProduksi = PemeriksaanProsesProduksi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control
        $allowedRoles = [
            'qc' => [1, 3, 5],
            'produksi' => [1, 2, 5],
            'spv' => [1, 4, 5]
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan approval ini.'
            ], 403);
        }

        // Sequential approval validation
        if ($type === 'produksi' && !$pemeriksaanProsesProduksi->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$pemeriksaanProsesProduksi->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($pemeriksaanProsesProduksi->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $pemeriksaanProsesProduksi->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        // Log the approval activity
        PemeriksaanProsesProduksiLog::create([
            'uuid' => Str::uuid(),
            'pemeriksaan_proses_produksi_id' => $pemeriksaanProsesProduksi->id,
            'pemeriksaan_proses_produksi_uuid' => $pemeriksaanProsesProduksi->uuid,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'aksi' => 'approve',
            'field_yang_diubah' => [$approvalField],
            'nilai_lama' => ['false'],
            'nilai_baru' => ['true'],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'keterangan' => "Data disetujui oleh {$type}: {$user->name}"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }

    public function auditIndex(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        // Ambil daftar source_uuid yang sudah di-copy ke audit
        $auditedSourceUuids = PemeriksaanProsesProduksi::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $query = PemeriksaanProsesProduksi::with(['plan', 'shift', 'area', 'user', 'qcApprover', 'produksiApprover', 'spvApprover', 'originalVersion'])
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

        if ($search) {
            $query->where('uraian_permasalahan', 'LIKE', '%' . $search . '%');
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
                         ->orderBy('jam', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->orderBy('id', 'desc')
                         ->paginate($perPage);

        return view('qc-sistem.pemeriksaan_proses_produksi.index_audit', compact('records', 'search', 'perPage'));
    }

    /**
     * Bulk Approve Selected - checkbox-based per-row approval
     * Replaces shift-based approval with flexible checkbox selection
     */
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
            $query = PemeriksaanProsesProduksi::whereIn('uuid', $uuids)
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

            PemeriksaanProsesProduksi::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            // Find all source_uuids from the updated records
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();

            // Update audit copies with same approval data
            PemeriksaanProsesProduksi::where('is_audit', true)
                ->whereIn('source_uuid', $updatedSourceUuids)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // Log activity
            foreach ($itemsToUpdate as $item) {
                PemeriksaanProsesProduksiLog::create([
                    'uuid' => Str::uuid(),
                    'pemeriksaan_proses_produksi_id' => $item->id,
                    'pemeriksaan_proses_produksi_uuid' => $item->uuid,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role,
                    'aksi' => 'bulk_approval_selected',
                    'field_yang_diubah' => ["approved_by_{$type}"],
                    'nilai_lama' => ['false'],
                    'nilai_baru' => ['true'],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'keterangan' => 'Bulk Approved Selected by ' . strtoupper($type)
                ]);
            }

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

    public function bulkApprove(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'shift_id' => 'required|exists:data_shift,id',
                'type' => 'required|in:qc,produksi,spv',
            ]);

            $user = Auth::user();
            $userRole = $user->id_role ?? null;
            $type = $request->type;

            // Validasi role
            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5]
            ];

            if (!isset($allowedRoles[$type]) || !in_array($userRole, $allowedRoles[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melakukan bulk approval ini.'
                ], 403);
            }

            // Cari data yang belum di-approve untuk filter ini
            $query = PemeriksaanProsesProduksi::whereDate('tanggal', $request->tanggal)
                ->where('shift_id', $request->shift_id);

            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }

            $items = $query->get();

            if ($items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data untuk disetujui pada filter ini.'
                ], 404);
            }

            // Validasi sekuensial
            if ($type === 'produksi') {
                $unapprovedByQc = $items->where('approved_by_qc', false);
                if ($unapprovedByQc->isNotEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Beberapa data belum disetujui oleh QC. Bulk approval Produksi gagal.'
                    ], 400);
                }
            }

            if ($type === 'spv') {
                $unapprovedByProd = $items->where('approved_by_produksi', false);
                if ($unapprovedByProd->isNotEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Beberapa data belum disetujui oleh Produksi. Bulk approval SPV gagal.'
                    ], 400);
                }
            }

            $approvalField = "approved_by_{$type}";
            $approvedAtField = "{$type}_approved_at";
            $approvedByField = "{$type}_approved_by";

            $updatedCount = 0;
            foreach ($items as $item) {
                if (!$item->$approvalField) {
                    $item->update([
                        $approvalField => true,
                        $approvedAtField => now(),
                        $approvedByField => $user->id,
                    ]);

                    // Log activity
                    PemeriksaanProsesProduksiLog::create([
                        'uuid' => Str::uuid(),
                        'pemeriksaan_proses_produksi_id' => $item->id,
                        'pemeriksaan_proses_produksi_uuid' => $item->uuid,
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_role' => $user->role,
                        'aksi' => 'bulk_approve',
                        'field_yang_diubah' => [$approvalField],
                        'nilai_lama' => [$approvalField => 'false'],
                        'nilai_baru' => [$approvalField => 'true'],
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'keterangan' => "Bulk Approval {$type} oleh {$user->name}"
                    ]);

                    $updatedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil menyetujui {$updatedCount} data secara massal."
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
        $filename = 'template_pemeriksaan_proses_produksi.xlsx';

        return Excel::download(new PemeriksaanProsesProduksiTemplateExport($user), $filename);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $user = auth()->user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('pemeriksaan-proses-produksi.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new PemeriksaanProsesProduksiImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('pemeriksaan-proses-produksi.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift/Area sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data pemeriksaan proses produksi berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('pemeriksaan-proses-produksi.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('pemeriksaan-proses-produksi.index')->with('success', $successMessage);
    }

    // --- AUDIT TRAIL METHODS ---

    protected function auditModel(): string
    {
        return PemeriksaanProsesProduksi::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'pemeriksaan-proses-produksi';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.pemeriksaan_proses_produksi';
    }

    protected function auditRelations(): array
    {
        return ['plan', 'shift', 'area', 'user', 'qcApprover', 'produksiApprover', 'spvApprover'];
    }

    protected function auditSearchColumns(): array
    {
        return ['uraian_permasalahan', 'analisa_penyebab'];
    }

    protected function auditTimeColumn(): string
    {
        return 'jam';
    }

    /**
     * Get the model class for Excel import (required by HasExcelImport trait)
     */
    protected function getImportModelClass(): string
    {
        return PemeriksaanProsesProduksi::class;
    }

    /**
     * Get template filename with timestamp (required by HasExcelImport trait)
     */
    protected function getTemplateFilename(): string
    {
        return 'Template_Pemeriksaan_Proses_Produksi_' . date('YmdHis') . '.xlsx';
    }

    public function bulkExportExcel(Request $request, PemeriksaanProsesProduksiBulkExportService $service)
    {
        return $service->handle($request, fn ($msg) => $this->buildNoDataHtmlResponse($msg));
    }
}
