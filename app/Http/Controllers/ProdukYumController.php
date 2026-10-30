<?php

namespace App\Http\Controllers;

use App\Models\ProdukYum;
use App\Models\ProdukYumLog;
use App\Models\JenisProduk;
use App\Models\DataShift;
use App\Models\DataBag;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Traits\HasAuditController;
use App\Services\BulkExport\ProdukYumBulkExportService;

class ProdukYumController extends Controller
{
    use HasAuditController;
    public function index()
    {
        $user = Auth::user();

        // Auditor langsung diarahkan ke halaman audit
        if ($user->id_role == 6) {
            return redirect()->route('produk-yum.audit');
        }
        
        $query = ProdukYum::with(['produk', 'plan', 'shift', 'user', 'dataBag', 'qcApprover', 'produksiApprover', 'spvApprover'])->operasional();
        
        // Filter berdasarkan plan user jika bukan superadmin
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        // Search functionality
        $search = request('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })->orWhere('kode_produksi', 'like', '%' . $search . '%');
            });
        }

        // Per page handling
        $perPage = request()->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50, 100]) ? $perPage : 10;
        
        // Ambil data yang diurutkan untuk grouping
        $this->applyDateRangeFilter(request(), $query);
        $items = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Grouping logic: Date + Shift
        $grouped = $items->groupBy(function($item) {
            $date = $item->tanggal ? $item->tanggal->format('Y-m-d') : 'no_date';
            $shift = $item->shift_id ?? 'no_shift';
            return $date . '_' . $shift;
        });

        // Manual Pagination for Grouped Collection
        $page = request()->get('page', 1);
        $groupedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $grouped->forPage($page, $perPage),
            $grouped->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('qc-sistem.produk_yum.index', compact('groupedData', 'search', 'perPage'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Filter produk berdasarkan plan user jika bukan superadmin
        $query = JenisProduk::query();
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $produks = $query->get();
        
        // Filter shift berdasarkan plan user jika bukan superadmin
        $shiftQuery = DataShift::query();
        if ($user->role !== 'superadmin') {
            $shiftQuery->where('id_plan', $user->id_plan);
        }
        $shifts = $shiftQuery->get();
        
        // Filter data bags berdasarkan plan user jika bukan superadmin
        $dataBagQuery = DataBag::query();
        if ($user->role !== 'superadmin') {
            $dataBagQuery->where('id_plan', $user->id_plan);
        }
        $dataBags = $dataBagQuery->get();

        return view('qc-sistem.produk_yum.create', compact('produks', 'shifts', 'dataBags'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'id_produk'     => 'required|exists:jenis_produk,id',
            'shift_id'      => 'required|exists:data_shift,id',
            'id_data_bag'   => 'required|exists:data_bag,id',
            'kode_produksi' => 'required|string|max:255',
            'berat_pcs'     => 'nullable|array',
            'berat_pcs.*'   => 'nullable',
            'jumlah_pcs'    => 'required|array',
            'jumlah_pcs.*'  => 'nullable',
            'aktual_berat'  => 'required|array',
            'aktual_berat.*' => 'nullable',
            'tanggal'       => 'required|date',
            'jam'           => 'required',
        ]);

        $tanggalData = $request->all();
        $tanggalData['tanggal'] = \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s');
        $tanggalData['jam']     = $request->jam;
        
        if (isset($tanggalData['aktual_berat']) && is_array($tanggalData['aktual_berat'])) {
            $tanggalData['aktual_berat'] = array_values(array_filter($tanggalData['aktual_berat'], function($val) {
                return $val !== null && trim((string)$val) !== '';
            }));
        }
        
        if (isset($tanggalData['jumlah_pcs']) && is_array($tanggalData['jumlah_pcs'])) {
            $tanggalData['jumlah_pcs'] = array_values(array_filter($tanggalData['jumlah_pcs'], function($val) {
                return $val !== null && trim((string)$val) !== '';
            }));
        }
        
        if (isset($tanggalData['berat_pcs']) && is_array($tanggalData['berat_pcs'])) {
            $tanggalData['berat_pcs'] = array_values(array_filter($tanggalData['berat_pcs'], function($val) {
                return $val !== null && trim((string)$val) !== '';
            }));
        }

        $produkYum = ProdukYum::create($tanggalData + [
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
        ]);
        
        return redirect()->route('produk-yum.index')
            ->with('success', 'Data Produk YUM berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $produkYum = ProdukYum::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access permission
        if ($user->role !== 'superadmin' && $produkYum->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('produk-yum.show', $produkYum)) {
            return $redirect;
        }
        
        $produkYum->load(['produk', 'plan', 'shift', 'user', 'dataBag']);
        return view('qc-sistem.produk_yum.show', compact('produkYum'));
    }

    public function edit($uuid)
    {
        $produkYum = ProdukYum::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($redirect = $this->redirectToCanonicalAuditUrl('produk-yum.edit', $produkYum)) {
            return $redirect;
        }

        // Check edit time limit using global config (skip for audit records)
        if (!$produkYum->is_audit) {
            $restrictedRoles = config('edit_limit.restricted_role_ids', []);
            if (in_array($user->id_role, $restrictedRoles)) {
                $timeLimitHours = config('edit_limit.time_limit_hours', 48);
                $createdAt = \Carbon\Carbon::parse($produkYum->created_at);
                if ($createdAt->addHours($timeLimitHours)->isPast()) {
                    return redirect()->route('produk-yum.index')
                        ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
                }
            }
        }

        // Check access permission
        if ($user->role !== 'superadmin' && $produkYum->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Gunakan id_plan dari record (bukan user) agar data audit yang plannya sama tetap bisa diakses
        $planId = $produkYum->id_plan;

        $produks = JenisProduk::when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $planId))->get();
        $shifts   = DataShift::when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $planId))->get();
        $dataBags = DataBag::when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $planId))->get();

        // Load relasi agar view dapat menampilkan nilai terpilih dengan benar
        $produkYum->load(['produk', 'shift', 'dataBag', 'plan']);

        return view('qc-sistem.produk_yum.edit', compact('produkYum', 'produks', 'shifts', 'dataBags'));
    }

    public function update(Request $request, $uuid)
    {
        $produkYum = ProdukYum::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check edit time limit using global config
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($produkYum->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('produk-yum.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        // Check access permission
        if ($user->role !== 'superadmin' && $produkYum->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate data ini.');
        }
        
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'shift_id' => 'required|exists:data_shift,id',
            'id_data_bag' => 'required|exists:data_bag,id',
            'kode_produksi' => 'required|string|max:255',
            'berat_pcs' => 'nullable|array',
            'berat_pcs.*' => 'nullable',
            'jumlah_pcs' => 'required|array',
            'jumlah_pcs.*' => 'nullable',
            'aktual_berat' => 'required|array',
            'aktual_berat.*' => 'nullable',
            'tanggal' => 'required|date',
        ]);

        $data = $request->except(['kode_form']);
        $data['tanggal'] = \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s');
        $data['id_plan'] = $user->id_plan;
        $data['user_id'] = $user->id;

        if (isset($data['aktual_berat']) && is_array($data['aktual_berat'])) {
            $data['aktual_berat'] = array_values(array_filter($data['aktual_berat'], function($val) {
                return $val !== null && trim((string)$val) !== '';
            }));
        }
        
        if (isset($data['jumlah_pcs']) && is_array($data['jumlah_pcs'])) {
            $data['jumlah_pcs'] = array_values(array_filter($data['jumlah_pcs'], function($val) {
                return $val !== null && trim((string)$val) !== '';
            }));
        }
        
        if (isset($data['berat_pcs']) && is_array($data['berat_pcs'])) {
            $data['berat_pcs'] = array_values(array_filter($data['berat_pcs'], function($val) {
                return $val !== null && trim((string)$val) !== '';
            }));
        }

        $produkYum->update($data);

        if ($user->id_role == 6 || $produkYum->is_audit) {
            return redirect()->route('produk-yum.audit')
                ->with('success', 'Data Produk YUM berhasil diperbarui.');
        }

        return redirect()->route('produk-yum.index')
            ->with('success', 'Data Produk YUM berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $produkYum = ProdukYum::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access permission
        if ($user->role !== 'superadmin' && $produkYum->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }
        
        $produkYum->delete();
        
        return redirect()->route('produk-yum.index')
            ->with('success', 'Data Produk YUM berhasil dihapus.');
    }

    // AJAX method to get data bags based on selected product
    public function getDataBags(Request $request)
    {
        $dataBags = DataBag::where('id_produk', $request->id_produk)->get();
        return response()->json($dataBags);
    }

    /**
     * Show logs for specific produk yum
     */
    public function showLogs($uuid)
    {
        $produkYum = ProdukYum::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access permission
        if ($user->role !== 'superadmin' && $produkYum->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }
        
        $logs = ProdukYumLog::where('produk_yum_uuid', $uuid)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('qc-sistem.produk_yum.logs', compact('produkYum', 'logs'));
    }

    /**
     * Get logs data for DataTables (JSON)
     */
    public function getLogsJson($uuid)
    {
        $produkYum = ProdukYum::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access permission
        if ($user->role !== 'superadmin' && $produkYum->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $logs = ProdukYumLog::where('produk_yum_uuid', $uuid)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'data' => $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user_name' => $log->user->name ?? 'Unknown',
                    'user_role' => $log->user->role ?? 'Unknown',
                    'field_yang_diubah' => $log->nama_field,
                    'deskripsi_perubahan' => $log->deskripsi_perubahan,
                    'ip_address' => $log->ip_address,
                    'created_at' => $log->created_at->format('d/m/Y H:i:s'),
                    'created_at_diff' => $log->created_at->diffForHumans(),
                ];
            })
        ]);
    }

    /**
     * Bulk export PDF with filters
     */
    public function bulkExportPdf(Request $request)
    {
        $request->validate([
            'kode_form' => 'required|string|max:50',
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'shift_id' => 'nullable|exists:data_shift,id',
            'id_produk' => 'nullable|exists:jenis_produk,id',
        ]);

        $user = Auth::user();
        
        // Build query with filters
        $query = ProdukYum::with(['produk', 'plan', 'shift', 'user', 'dataBag', 'qcApprover', 'produksiApprover', 'spvApprover']);
        
        // Filter berdasarkan plan user jika bukan superadmin
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($request->has('is_audit') && $request->is_audit == 1) {
            $query->where('is_audit', true);
        } else {
            $query->operasional();
        }
        
        // Apply filters
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $tanggalAwal = \Carbon\Carbon::parse($request->tanggal_awal)->startOfDay();
            $tanggalAkhir = \Carbon\Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        } elseif ($request->filled('tanggal_awal')) {
            $query->whereDate('tanggal', $request->tanggal_awal);
        }
        
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }
        
        if ($request->filled('id_produk')) {
            $query->where('id_produk', $request->id_produk);
        }
        
        $data = $query->orderBy('tanggal', 'desc')->get();
        
        if ($data->isEmpty()) {
            return response('<div class="alert alert-warning">Tidak ada data yang ditemukan berdasarkan filter yang dipilih.</div>', 404);
        }
        
        // Update kode_form for filtered data
        $data->each(function ($item) use ($request) {
            $item->update(['kode_form' => $request->kode_form]);
        });
        
        // Get filter information for PDF
        $tanggalFilter = 'Semua Tanggal';
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $tanggalFilter = \Carbon\Carbon::parse($request->tanggal_awal)->format('d/m/Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d/m/Y');
        } elseif ($request->filled('tanggal_awal')) {
            $tanggalFilter = \Carbon\Carbon::parse($request->tanggal_awal)->format('d/m/Y');
        }

        $filterInfo = [
            'tanggal' => $tanggalFilter,
            'shift' => $request->shift_id ? DataShift::find($request->shift_id)->shift : 'Semua Shift',
            'produk' => $request->id_produk ? JenisProduk::find($request->id_produk)->nama_produk : 'Semua Produk',
            'kode_form' => $request->kode_form,
        ];
        
        $pdf = Pdf::loadView('qc-sistem.produk_yum.export_pdf', compact('data', 'filterInfo'))
                  ->setPaper('a4', 'portrait');
        
        $filename = 'produk-yum-' . date('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export single data to PDF by UUID
     */
    public function exportPdf($uuid)
    {
        $item = ProdukYum::with(['produk', 'plan', 'shift', 'user', 'dataBag', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $user = Auth::user();
        
        // Check access permission
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }

        $data = collect([$item]);
        
        // Get filter information for PDF
        $filterInfo = [
            'tanggal' => $item->tanggal ? date('d/m/Y', strtotime($item->tanggal)) : 'Semua Tanggal',
            'shift' => $item->shift ? $item->shift->shift : 'Semua Shift',
            'produk' => $item->produk ? $item->produk->nama_produk : 'Semua Produk',
            'kode_form' => $item->kode_form ?? 'QF 16/00',
        ];
        
        $pdf = Pdf::loadView('qc-sistem.produk_yum.export_pdf', compact('data', 'filterInfo'))
                  ->setPaper('a4', 'portrait');
        
        $filename = 'produk-yum-' . ($item->tanggal ? date('Y-m-d', strtotime($item->tanggal)) : 'data') . '-' . substr($uuid, 0, 8) . '.pdf';
        
        return $pdf->stream($filename);
    }

    /**
     * Approve data with role-based validation
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $produkYum = ProdukYum::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control following ProdukNonForming pattern
        $allowedRoles = [
            'qc' => [1, 3, 5], // Role 1&5 (QC only), Role 3 (QC only)
            'produksi' => [1, 2, 5], // Role 1&5 (produksi only), Role 2 (produksi only)
            'spv' => [1, 4, 5] // Role 1&5 (SPV only), Role 4 (SPV only)
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan approval ini.'
            ], 403);
        }

        // Sequential approval validation (QC → Produksi → SPV)
        if ($type === 'produksi' && !$produkYum->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$produkYum->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($produkYum->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $produkYum->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        // Log approval activity
        ProdukYumLog::create([
            'produk_yum_id' => $produkYum->id,
            'produk_yum_uuid' => $produkYum->uuid,
            'user_id' => $user->id,
            'field_yang_diubah' => ['approval_' . $type],
            'nilai_lama' => [false],
            'nilai_baru' => [true],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }

    /**
     * Bulk Approve data produk yum per shift
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
        $allowedRoles = [
            'qc' => [1, 3, 5],
            'produksi' => [1, 2, 5],
            'spv' => [1, 4, 5],
        ];
        if (!in_array((int) $user->id_role, $allowedRoles[$type], true)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk approval ini.'], 403);
        }

        $query = ProdukYum::whereIn('uuid', $validated['uuids']);
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

        \DB::transaction(function () use ($pending, $updateData, $type, $approvalField, $user) {
            foreach ($pending as $item) {
                $item->update($updateData);
                \App\Models\ProdukYumLog::create([
                    'produk_yum_id' => $item->id,
                    'produk_yum_uuid' => $item->uuid,
                    'user_id' => $user->id,
                    'field_yang_diubah' => ['approval_' . $type],
                    'nilai_lama' => [false],
                    'nilai_baru' => [true],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'keterangan' => "Data disetujui oleh {$type}: {$user->name}",
                ]);
                $sourceUuid = $item->is_audit ? $item->source_uuid : $item->uuid;
                if ($sourceUuid) {
                    ProdukYum::where('is_audit', false)->where('uuid', $sourceUuid)->update($updateData);
                    ProdukYum::where('is_audit', true)->where('source_uuid', $sourceUuid)->update($updateData);
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Berhasil memverifikasi ' . $pending->count() . ' data ' . strtoupper($type) . '.']);
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv',
            'tanggal' => 'required|date',
            'shift_id' => 'required|exists:data_shift,id'
        ]);

        $user = Auth::user();
        $type = $request->type;
        $tanggal = $request->tanggal;
        $shift_id = $request->shift_id;

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

        // Ambil semua data pada tanggal dan shift tersebut
        $query = ProdukYum::whereDate('tanggal', $tanggal)
                          ->where('shift_id', $shift_id);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data ditemukan untuk shift ini.'
            ], 404);
        }

        // Sequential approval validation
        if ($type === 'produksi') {
            $unapprovedByQc = $items->where('approved_by_qc', false)->count();
            if ($unapprovedByQc > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seluruh data pada shift ini harus disetujui oleh QC terlebih dahulu.'
                ], 400);
            }
        }

        if ($type === 'spv') {
            $unapprovedByProduksi = $items->where('approved_by_produksi', false)->count();
            if ($unapprovedByProduksi > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seluruh data pada shift ini harus disetujui oleh Produksi terlebih dahulu.'
                ], 400);
            }
        }

        $approvalField = "approved_by_{$type}";
        
        // Update all records
        $query->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        // Log approval activity for each record
        foreach ($items as $item) {
            // Cek jika data ini sebelumnya belum di-approve tipe ini
            if (!$item->$approvalField) {
                ProdukYumLog::create([
                    'produk_yum_id' => $item->id,
                    'produk_yum_uuid' => $item->uuid,
                    'user_id' => $user->id,
                    'field_yang_diubah' => ['approval_' . $type],
                    'nilai_lama' => [false],
                    'nilai_baru' => [true],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Seluruh data pada shift ini berhasil disetujui.'
        ]);
    }
    
    // --- Implementasi Method Abstrak dari HasAuditController ---

    protected function auditModel(): string
    {
        return \App\Models\ProdukYum::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'produk-yum';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.produk_yum';
    }

    protected function auditPermissionPrefix(): string
    {
        return 'produk-yum'; // Ensure your permissions follow "create-produk-yum", "edit-produk-yum", etc.
    }

    protected function auditTimeColumn(): string
    {
        return 'jam';
    }

    protected function buildAuditQuery()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $query = \App\Models\ProdukYum::with(['produk', 'plan', 'shift', 'user', 'dataBag', 'qcApprover', 'produksiApprover', 'spvApprover']);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        return $query;
    }

    protected function auditSearchColumns(): array
    {
        return ['kode_produksi'];
    }

    protected function filterLogChanges(array $changes): array
    {
        unset($changes['updated_at']);
        unset($changes['created_at']);
        return $changes;
    }

    protected function customizeAuditCopy($newRecord, $originalRecord)
    {
        // No custom changes needed right now
    }

    public function bulkExportExcel(Request $request, ProdukYumBulkExportService $exportService)
    {
        return $exportService->exportExcel($request);
    }
}
