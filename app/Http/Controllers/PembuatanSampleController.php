<?php

namespace App\Http\Controllers;

use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\PembuatanSample;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PembuatanSampleLog;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PembuatanSampleTemplateExport;
use App\Imports\PembuatanSampleImport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Str;
use App\Http\Controllers\Traits\HasExcelImport;

class PembuatanSampleController extends Controller
{
    use HasExcelImport;
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->id_role == 6) {
            return redirect()->route('pembuatan-sample.audit');
        }
        $search = trim((string) request('search', ''));
        
        $query = PembuatanSample::with(['plan', 'produk', 'shift', 'createdBy', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->where('is_audit', false);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('kode_produksi', 'like', "%{$search}%")
                    ->orWhere('jenis_sample', 'like', "%{$search}%")
                    ->orWhereHas('produk', function ($qp) use ($search) {
                        $qp->where('nama_produk', 'like', "%{$search}%");
                    });
            });
        }

        // Pagination on records (not groups)
        $perPage = request('per_page', 10);
        $this->applyDateRangeFilter(request(), $query);
        $data = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        // Group the paginated data for display
        $groupedData = $data->items();
        $groupedData = collect($groupedData)->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') . '_' . $item->id_shift;
        });

        return view('qc-sistem.pembuatan_sample.index', [
            'data' => $data,
            'groupedData' => $groupedData,
            'search' => $search,
            'perPage' => $perPage
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            $shifts = DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.pembuatan_sample.create', compact('produks', 'shifts'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'id_shift'       => 'required|exists:data_shift,id',
            'tanggal'        => 'required|date',
            'jam'            => 'required',
            'items'          => 'required|array|min:1',
            'items.*.id_produk'      => 'required|exists:jenis_produk,id',
            'items.*.kode_produksi'  => 'required|string|max:255',
            'items.*.tanggal_expired' => 'required|date',
            'items.*.jumlah'         => 'required|integer',
            'items.*.berat_sampling' => 'nullable|string',
            'items.*.berat'          => 'required|numeric',
            'items.*.jenis_sample'   => 'required|string|max:255',
        ]);

        $tanggalData = Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s');
        $itemsData = $request->items;
        $firstItem = $itemsData[0] ?? [];

        PembuatanSample::create([
            'uuid'              => Str::uuid(),
            'created_by'        => $user->id,
            'id_plan'           => $user->id_plan,
            'id_shift'          => $request->id_shift,
            'id_produk'         => $firstItem['id_produk'] ?? null,
            'kode_produksi'     => $firstItem['kode_produksi'] ?? '',
            'tanggal'           => $tanggalData,
            'tanggal_expired'   => $firstItem['tanggal_expired'] ?? now(),
            'jumlah'            => $firstItem['jumlah'] ?? 0,
            'berat'             => $firstItem['berat'] ?? 0,
            'berat_sampling'    => $firstItem['berat_sampling'] ?? null,
            'jenis_sample'      => $firstItem['jenis_sample'] ?? '',
            'jam'               => $request->jam,
            'items_json'        => $itemsData,
            'kode_form'         => $request->kode_form ?? 'QF 25/00',
        ]);

        return redirect()->route('pembuatan-sample.index')->with('success', 'Data Sample berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $user = Auth::user();
        $item = PembuatanSample::where('uuid', $uuid)
            ->with(['produk', 'plan', 'shift', 'createdBy', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->firstOrFail();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('pembuatan-sample.show', $item)) {
            return $redirect;
        }

        return view('qc-sistem.pembuatan_sample.show', compact('item'));
    }

    public function edit($uuid)
    {
        $user = Auth::user();

        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            $shifts = DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        $pembuatanSample = PembuatanSample::where('uuid', $uuid)->firstOrFail();

        $isAuditMode = (bool) $pembuatanSample->is_audit;
        $queryParameters = request()->query();
        $hasAuditFlag = array_key_exists('is_audit', $queryParameters);

        if (($isAuditMode && request()->query('is_audit') !== '1') || (!$isAuditMode && $hasAuditFlag)) {
            unset($queryParameters['is_audit'], $queryParameters['uuid']);
            if ($isAuditMode) {
                $queryParameters['is_audit'] = '1';
            }

            return redirect()->route(
                'pembuatan-sample.edit',
                array_merge(['uuid' => $pembuatanSample->uuid], $queryParameters)
            );
        }

        // Cek batas waktu edit untuk role yang dibatasi (global config)
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($pembuatanSample->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('pembuatan-sample.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }
        return view('qc-sistem.pembuatan_sample.edit', compact('pembuatanSample', 'produks', 'shifts'));
    }

    public function update(Request $request, $uuid)
    {
        $pembuatanSample = PembuatanSample::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Cek batas waktu edit untuk role yang dibatasi (global config)
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($pembuatanSample->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('pembuatan-sample.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        $request->validate([
            'id_shift'       => 'required|exists:data_shift,id',
            'tanggal'        => 'required|date',
            'jam'            => 'required',
            'items'          => 'required|array|min:1',
            'items.*.id_produk'      => 'required|exists:jenis_produk,id',
            'items.*.kode_produksi'  => 'required|string|max:255',
            'items.*.tanggal_expired' => 'required|date',
            'items.*.jumlah'         => 'required|integer',
            'items.*.berat_sampling' => 'nullable|string',
            'items.*.berat'          => 'required|numeric',
            'items.*.jenis_sample'   => 'required|string|max:255',
        ]);

                $tanggalData = Carbon::parse($request->tanggal)->toDateString();

        $pembuatanSample->update([
            'id_shift'   => $request->id_shift,
            'id_produk'  => ($request->items[0]['id_produk'] ?? null),
            'kode_produksi' => ($request->items[0]['kode_produksi'] ?? ''),
            'tanggal_expired' => ($request->items[0]['tanggal_expired'] ?? now()),
            'jumlah'     => ($request->items[0]['jumlah'] ?? 0),
            'berat'      => ($request->items[0]['berat'] ?? 0),
            'berat_sampling' => ($request->items[0]['berat_sampling'] ?? null),
            'jenis_sample' => ($request->items[0]['jenis_sample'] ?? ''),
            'tanggal'    => $tanggalData,
            'jam'        => $request->jam,
            'items_json' => $request->items,
        ]);

        // Smart redirect: jika audit -> ke audit page, jika operasional -> ke index page
        $redirectRoute = $pembuatanSample->is_audit ? 'pembuatan-sample.audit' : 'pembuatan-sample.index';

        return redirect()->route($redirectRoute)->with('success', 'Data Sample berhasil diperbarui.');
    }

    public function destroy($uuid)
    {   
        $pembuatanSample = PembuatanSample::where('uuid', $uuid)->firstOrFail();
        $pembuatanSample->delete();
        return redirect()->route('pembuatan-sample.index')->with('success', 'Data Sample berhasil dihapus.');
    }
    /**
     * Tampilkan halaman logs untuk pembuatan sample
     */
    public function showLogs($uuid)
    {
        $item = PembuatanSample::where('uuid', $uuid)->firstOrFail();
        
        $logs = PembuatanSampleLog::where('pembuatan_sample_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(5);
        
        return view('qc-sistem.pembuatan_sample.logs', compact('item', 'logs'));
    }

    /**
     * Get logs data untuk DataTables (jika diperlukan)
     */
    public function getLogsJson($uuid)
    {
        $pembuatanSample = PembuatanSample::where('uuid', $uuid)->firstOrFail();
        
        $logs = PembuatanSampleLog::where('pembuatan_sample_uuid', $uuid)
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

    public function bulkExportPdf(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'id_shift' => 'nullable|exists:data_shift,id',
            'id_produk' => 'nullable|exists:jenis_produk,id',
            'kode_form' => 'required|string|max:255',
            'is_audit' => 'nullable|boolean'
        ]);

        $user = Auth::user();
        $query = PembuatanSample::with(['plan', 'produk', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover']);

        if ($request->has('is_audit')) {
            $query->where('is_audit', $request->boolean('is_audit'));
        }

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $tanggalAwal = Carbon::parse($request->tanggal_awal)->startOfDay();
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        } elseif ($request->tanggal_awal) {
            $tanggal = Carbon::parse($request->tanggal_awal);
            $query->whereDate('tanggal', $tanggal);
        }

        if ($request->id_shift) {
            $query->where('id_shift', $request->id_shift);
        }

        if ($request->id_produk) {
            $query->where('id_produk', $request->id_produk);
        }

        $data = $query->orderBy('jenis_sample', 'asc')->orderBy('tanggal', 'asc')->orderBy('created_at', 'asc')->get();
        
        // Group data by jenis_sample for PDF display
        $groupedData = $data->groupBy('jenis_sample');

        // Update kode_form for all filtered data (only from modal)
        if (!$data->isEmpty()) {
            $dataIds = $data->pluck('id')->toArray();
            PembuatanSample::whereIn('id', $dataIds)->update(['kode_form' => $request->kode_form]);
            
            // Refresh data to include updated kode_form
            $data = $data->map(function ($item) use ($request) {
                $item->kode_form = $request->kode_form;
                return $item;
            });
        }

        // Check if no data found
        if ($data->isEmpty()) {
            $html = '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Data Tidak Ditemukan</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                    .no-data { color: #666; font-size: 18px; }
                </style>
            </head>
            <body>
                <div class="no-data">
                    <h2>Tidak Ada Data Ditemukan</h2>
                    <p>Tidak ada data Pembuatan Sample yang sesuai dengan filter yang dipilih.</p>
                    <p><strong>Filter:</strong></p>
                    <p>Tanggal: ' . ($request->tanggal_awal && $request->tanggal_akhir ? Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y') : ($request->tanggal_awal ? Carbon::parse($request->tanggal_awal)->format('d-m-Y') : 'Semua')) . '</p>
                    <p>Shift: ' . ($request->id_shift ? DataShift::find($request->id_shift)->shift ?? 'Tidak Diketahui' : 'Semua') . '</p>
                    <p>Produk: ' . ($request->id_produk ? JenisProduk::find($request->id_produk)->nama_produk ?? 'Tidak Diketahui' : 'Semua') . '</p>
                    <p>Kode Form: ' . $request->kode_form . '</p>
                </div>
            </body>
            </html>';
            
            return response($html)->header('Content-Type', 'text/html');
        }

        // Prepare filter info for PDF
        $filterInfo = [
            'tanggal' => $request->tanggal_awal && $request->tanggal_akhir ? Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y') : ($request->tanggal_awal ? Carbon::parse($request->tanggal_awal)->format('d-m-Y') : 'Semua Tanggal'),
            'shift' => $request->id_shift ? DataShift::find($request->id_shift)->shift ?? 'Tidak Diketahui' : 'Semua Shift',
            'produk' => $request->id_produk ? JenisProduk::find($request->id_produk)->nama_produk ?? 'Tidak Diketahui' : 'Semua Produk',
            'kode_form' => $request->kode_form
        ];

        // Generate PDF
        $pdf = PDF::loadView('qc-sistem.pembuatan_sample.export_pdf', compact('data', 'groupedData', 'filterInfo'));
        $pdf->setPaper('A4', 'landscape');

        $safeKodeForm = (string) ($filterInfo['kode_form'] ?? '');
        $safeKodeForm = str_replace(['/', '\\', ':'], '-', $safeKodeForm);
        $safeKodeForm = trim($safeKodeForm);
        
        $filename = 'Pembuatan_Sample_' . $safeKodeForm . '_' . date('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }



    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $pembuatanSample = PembuatanSample::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control following Seasoning pattern
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
        if ($type === 'produksi' && !$pembuatanSample->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$pembuatanSample->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($pembuatanSample->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $pembuatanSample->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'id_shift' => 'required|exists:data_shift,id',
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $user = Auth::user();
        $type = $request->type;
        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
        $shiftId = $request->id_shift;

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

        // Get items to approve
        $query = PembuatanSample::whereDate('tanggal', $tanggal)
            ->where('id_shift', $shiftId);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data untuk disetujui.'
            ], 404);
        }

        $count = 0;
        foreach ($items as $item) {
            // Check sequential approval
            if ($type === 'produksi' && !$item->approved_by_qc) continue;
            if ($type === 'spv' && !$item->approved_by_produksi) continue;

            // Check if already approved
            $approvalField = "approved_by_{$type}";
            if ($item->$approvalField) continue;

            // Update item
            $item->update([
                $approvalField => true,
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now()
            ]);
            $count++;
        }

        if ($count === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Semua data sudah disetujui atau belum memenuhi syarat approval berurutan.'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil menyetujui {$count} data."
        ]);
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
            $query = PembuatanSample::whereIn('uuid', $uuids)
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

            PembuatanSample::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            // Find all source_uuids from the updated records
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();
            
            // Update audit copies with same approval data
            PembuatanSample::where('is_audit', true)
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

    // ===================== AUDIT METHODS =====================

    public function copyToAudit($uuid)
    {
        $user = Auth::user();
        
        // Auditor (id_role 6) bisa akses audit, atau user dengan permission
        if ($user->id_role != 6 && !$user->hasPermissionTo('create-pembuatan-sample')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk membuat data audit.');
        }

        $item = PembuatanSample::where('uuid', $uuid)->firstOrFail();

        // Jika item yang diklik sudah merupakan record audit
        if ($item->is_audit) {
            return redirect()->route('pembuatan-sample.edit', $item->uuid);
        }

        // Jika data operasional ini sudah memiliki versi audit sebelumnya
        if ($item->auditVersion) {
            return redirect()->route('pembuatan-sample.edit', $item->auditVersion->uuid);
        }

        // Jika belum memiliki versi audit, buatkan salinan audit secara otomatis
        $clone = $item->copyToAudit();

        return redirect()->route('pembuatan-sample.edit', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    public function auditIndex(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $auditedSourceUuids = PembuatanSample::where('is_audit', true)->whereNotNull('source_uuid')->pluck('source_uuid')->toArray();

        $query = PembuatanSample::with(['plan', 'produk', 'shift', 'createdBy'])
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
            $query->where(function ($q) use ($search) {
                $q->where('kode_produksi', 'like', "%{$search}%")
                    ->orWhere('jenis_sample', 'like', "%{$search}%")
                    ->orWhereHas('produk', function ($qp) use ($search) {
                        $qp->where('nama_produk', 'like', "%{$search}%");
                    });
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return view('qc-sistem.pembuatan_sample.index_audit', compact('records', 'search', 'perPage'));
    }

    public function auditSearchAjax(Request $request)
    {
        try {
            $user = Auth::user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 10);
            $page = (int) $request->get('page', 1);

            $query = PembuatanSample::with(['plan', 'produk', 'shift', 'createdBy'])
                ->where('is_audit', true)
                ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan));

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('jenis_sample', 'like', "%{$search}%")
                        ->orWhereHas('produk', function ($qp) use ($search) {
                            $qp->where('nama_produk', 'like', "%{$search}%");
                        });
                });
            }

            $records = $query->orderBy('tanggal', 'desc')
                ->orderBy('jam', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'html' => view('qc-sistem.pembuatan_sample._table', compact('records'))->render(),
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => (int) ceil($records->total() / $perPage),
                    'per_page' => $perPage,
                    'total' => $records->total(),
                    'from' => $records->firstItem(),
                    'to' => $records->lastItem(),
                    'has_pages' => $records->total() > $perPage,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkExportExcel(Request $request, \App\Services\BulkExport\PembuatanSampleBulkExportService $exportService)
    {
        return $exportService->exportExcel($request);
    }

    /**
     * Get import model class (for HasExcelImport trait)
     */
    protected function getImportModelClass(): string
    {
        return PembuatanSample::class;
    }

    /**
     * Get template filename (for HasExcelImport trait)
     */
    protected function getTemplateFilename(): string
    {
        return 'PembuatanSample_Template_' . date('YmdHis') . '.xlsx';
    }
}
