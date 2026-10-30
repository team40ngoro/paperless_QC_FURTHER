<?php

namespace App\Http\Controllers;

use App\Models\PemasakanNasi;
use App\Models\PemasakanNasiLog;
use App\Models\JenisProduk;
use App\Models\DataShift;
use Illuminate\Http\Request;
use App\Services\BulkExport\PemasakanNasiBulkExportService;
use App\Http\Controllers\Traits\HasAuditController;

class PemasakanNasiController extends Controller
{
    use HasAuditController;

    public function bulkExportExcel(Request $request, PemasakanNasiBulkExportService $exportService)
    {
        return $exportService->exportExcel($request);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->id_role == 6) {
            return redirect()->route('pemasakan-nasi.audit');
        }

        $search = $request->get('search');
        $perPage = in_array($request->get('per_page', 10), [5, 10, 25, 50, 100]) ? $request->get('per_page', 10) : 10;
        
        $query = PemasakanNasi::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->where('is_audit', false);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_produksi', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('produk', function($qp) use ($search) {
                      $qp->where('nama_produk', 'LIKE', '%' . $search . '%');
                  })
                  ->orWhere('tanggal', 'LIKE', '%' . $search . '%');
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $allData = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Group by Date + Shift
        $groupedData = $allData->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') . '_' . ($item->shift_id ?? 'no_shift');
        });

        // Paginate the groups
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $currentItems = $groupedData->slice(($currentPage - 1) * $perPage, $perPage);
        $items = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $groupedData->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        // For PDF modal dropdowns
        $cachedShifts = DataShift::where('id_plan', $user->id_plan)->orderBy('shift')->get();
        $cachedProduk = JenisProduk::where('id_plan', $user->id_plan)->orderBy('nama_produk')->get();

        return view('qc-sistem.pemasakan_nasi.index', [
            'items'        => $items,
            'groupedData'  => $currentItems,
            'search'       => $search,
            'perPage'      => $perPage,
            'cachedShifts' => $cachedShifts,
            'cachedProduk' => $cachedProduk,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $products = JenisProduk::orderBy('nama_produk')->get();
            $shifts = DataShift::orderBy('shift')->get();
        } else {
            $products = JenisProduk::where('id_plan', $user->id_plan)
                ->orderBy('nama_produk')
                ->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)
                ->orderBy('shift')
                ->get();
        }

        return view('qc-sistem.pemasakan_nasi.create', compact('products', 'shifts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'id_produk'      => 'required|exists:jenis_produk,id',
            'shift_id'       => 'required|exists:data_shift,id',
            'tanggal'        => 'required|date',
            'jam'            => 'required',
            'kode_produksi'  => 'required|string|max:255',
            'waktu_start'    => 'required|string',
            'waktu_stop'     => 'required|string',
            'proses'         => 'required|string|max:255',
            'waktu'          => 'required|string|max:255',
            'jenis_bahan'    => 'required|array|min:1',
            'jenis_bahan.*'  => 'required|string|max:255',
            'jumlah'         => 'required|array|min:1',
            'jumlah.*'       => 'required|string|max:255',
            'sensori_kondisi' => 'required|in:OK,Tidak OK',
            'status_cooking' => 'required|boolean',
            'lama_proses'    => 'required|string|max:255',
            'temp_std_1'     => 'required|string|max:255',
            'temp_std_2'     => 'required|string|max:255',
            'temp_std_3'     => 'required|string|max:255',
            'organo_warna'   => 'required|in:OK,Tidak OK',
            'organo_aroma'   => 'required|in:OK,Tidak OK',
            'organo_rasa'    => 'required|in:OK,Tidak OK',
            'organo_tekstur' => 'required|in:OK,Tidak OK',
            'catatan'        => 'nullable|string'
        ]);

        $data = $request->all();
        $tanggalData = \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now());
        $data['id_plan']  = $user->id_plan;
        $data['user_id']  = $user->id;
        $data['tanggal']  = $tanggalData;
        $data['jam']      = $request->jam;
        PemasakanNasi::create($data);

        return redirect()->route('pemasakan-nasi.index')
            ->with('success', 'Data pemasakan nasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $pemasakanNasi = PemasakanNasi::where('uuid', $uuid)->firstOrFail();

        if ($redirect = $this->redirectToCanonicalAuditUrl('pemasakan-nasi.show', $pemasakanNasi)) {
            return $redirect;
        }

        $pemasakanNasi->load(['plan', 'user', 'shift', 'produk']);
        return view('qc-sistem.pemasakan_nasi.show', compact('pemasakanNasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $pemasakanNasi = PemasakanNasi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        if ($redirect = $this->redirectToCanonicalAuditUrl('pemasakan-nasi.edit', $pemasakanNasi)) {
            return $redirect;
        }

        // Check edit time limit using global config
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($pemasakanNasi->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('pemasakan-nasi.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        // Authorization check
        if ($user->role !== 'superadmin' && $pemasakanNasi->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->role === 'superadmin') {
            $products = JenisProduk::orderBy('nama_produk')->get();
            $shifts = DataShift::orderBy('shift')->get();
        } else {
            $products = JenisProduk::where('id_plan', $user->id_plan)
                ->orderBy('nama_produk')
                ->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)
                ->orderBy('shift')
                ->get();
        }

        return view('qc-sistem.pemasakan_nasi.edit', compact('pemasakanNasi', 'products', 'shifts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $pemasakanNasi = PemasakanNasi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Check edit time limit using global config
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($pemasakanNasi->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('pemasakan-nasi.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        // Authorization check
        if ($user->role !== 'superadmin' && $pemasakanNasi->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'nullable|string',
            'kode_produksi' => 'required|string|max:255',
            'waktu_start' => 'required|string',
            'waktu_stop' => 'required|string',
            'proses' => 'required|string|max:255',
            'waktu' => 'required|string|max:255',
            'jenis_bahan' => 'required|array|min:1',
            'jenis_bahan.*' => 'required|string|max:255',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|string|max:255',
            'sensori_kondisi' => 'required|in:OK,Tidak OK',
            'status_cooking' => 'required|boolean',
            'lama_proses' => 'required|string|max:255',
            'temp_std_1' => 'required|string|max:255',
            'temp_std_2' => 'required|string|max:255',
            'temp_std_3' => 'required|string|max:255',
            'organo_warna' => 'required|in:OK,Tidak OK',
            'organo_aroma' => 'required|in:OK,Tidak OK',
            'organo_rasa' => 'required|in:OK,Tidak OK',
            'organo_tekstur' => 'required|in:OK,Tidak OK',
            'catatan' => 'nullable|string'
        ]);

        $data = $request->all();
        $data['id_plan'] = $user->id_plan;
        $data['user_id'] = $user->id;

        $pemasakanNasi->update($data);

        // Smart redirect: jika audit -> ke audit page, jika operasional -> ke index page
        $redirectRoute = $pemasakanNasi->is_audit ? 'pemasakan-nasi.audit' : 'pemasakan-nasi.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Data pemasakan nasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $pemasakanNasi = PemasakanNasi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $pemasakanNasi->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $pemasakanNasi->delete();

        return redirect()->route('pemasakan-nasi.index')
            ->with('success', 'Data pemasakan nasi berhasil dihapus.');
    }
    /**
    * Show logs for the specified resource.
    */
    public function showLogs($uuid)
    {
        $item = PemasakanNasi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $logs = PemasakanNasiLog::where('pemasakan_nasi_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.pemasakan_nasi.logs', compact('item', 'logs'));
    }

    /**
    * Get logs JSON for the specified resource.
    */
    public function getLogsJson($uuid)
    {
        $item = PemasakanNasi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = PemasakanNasiLog::where('pemasakan_nasi_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    /**
     * Approve data with role-based validation
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $pemasakanNasi = PemasakanNasi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
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
        if ($type === 'produksi' && !$pemasakanNasi->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$pemasakanNasi->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($pemasakanNasi->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $pemasakanNasi->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        // Log the approval activity
        PemasakanNasiLog::create([
            'pemasakan_nasi_id' => $pemasakanNasi->id,
            'pemasakan_nasi_uuid' => $pemasakanNasi->uuid,
            'user_name' => $user->name,
            'user_role' => $user->role ?? 'Unknown',
            'aksi' => 'approved',
            'field_yang_diubah' => [$approvalField, "{$type}_approved_by", "{$type}_approved_at"],
            'nilai_lama' => [
                $approvalField => false,
                "{$type}_approved_by" => null,
                "{$type}_approved_at" => null
            ],
            'nilai_baru' => [
                $approvalField => true,
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now()->toDateTimeString()
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'keterangan' => "Data disetujui oleh {$type}: {$user->name}"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }

    /**
     * Bulk export PDF with filters
     */
    public function bulkExportPdf(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'shift' => 'nullable|string',
            'produk' => 'nullable|string',
            'kode_form' => 'required|string|max:50'
        ]);

        $user = auth()->user();
        
        // Build query with approval relationships
        $query = PemasakanNasi::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover']);
        
        if ($request->has('is_audit') && $request->is_audit == 1) {
            $query->where('is_audit', true);
        } else {
            $query->operasional();
        }
        
        // Filter berdasarkan plan user jika bukan superadmin
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        // Apply filters
        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $tanggalAwal = \Carbon\Carbon::parse($request->tanggal_awal)->startOfDay();
            $tanggalAkhir = \Carbon\Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        } elseif ($request->tanggal_awal) {
            $query->whereDate('tanggal', $request->tanggal_awal);
        }
        
        if ($request->filled('shift')) {
            $query->whereHas('shift', function($q) use ($request) {
                $q->where('shift', $request->shift);
            });
        }
        
        if ($request->filled('produk')) {
            $query->whereHas('produk', function($q) use ($request) {
                $q->where('nama_produk', $request->produk);
            });
        }

        $data = $query->orderBy('tanggal', 'desc')->get();

        if ($data->isEmpty()) {
            return response('<div style="text-align: center; margin-top: 50px;"><h3>Tidak ada data yang ditemukan dengan filter yang diterapkan.</h3></div>', 404);
        }

        // Update kode_form for matching records
        $kodeForm = $request->kode_form;
        $dataIds = $data->pluck('id')->toArray();
        
        PemasakanNasi::whereIn('id', $dataIds)->update([
            'kode_form' => $kodeForm
        ]);

        // Get plan information
        $plan = $user->role === 'superadmin' ? 
            (isset($data[0]) ? $data[0]->plan : null) : 
            $user->plan;

        // Prepare filters for display
        $tanggalFilter = 'Semua Tanggal';
        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $tanggalFilter = \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
        } elseif ($request->tanggal_awal) {
            $tanggalFilter = \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y');
        }

        $filters = [
            'tanggal' => $tanggalFilter,
            'shift' => $request->shift ?: 'Semua Shift',
            'produk' => $request->produk ?: 'Semua Produk',
            'kode_form' => $kodeForm
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('qc-sistem.pemasakan_nasi.export_pdf', compact('data', 'plan', 'filters'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'pemasakan-nasi-' . date('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Bulk approval per shift
     */
    public function bulkApproveSelected(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:qc,produksi,spv',
            'uuids' => 'required|array|min:1',
            'uuids.*' => 'required|uuid',
        ]);

        $user = auth()->user();
        $type = $validated['type'];
        $allowedRoles = [
            'qc' => [1, 3, 5],
            'produksi' => [2],
            'spv' => [4],
        ];
        if (!in_array((int) $user->id_role, $allowedRoles[$type], true)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk approval ini.'], 403);
        }

        $query = PemasakanNasi::whereIn('uuid', $validated['uuids']);
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
                \App\Models\PemasakanNasiLog::create([
                    'pemasakan_nasi_id' => $item->id,
                    'pemasakan_nasi_uuid' => $item->uuid,
                    'user_name' => $user->name,
                    'user_role' => $user->role ?? 'Unknown',
                    'aksi' => 'approved',
                    'field_yang_diubah' => [$approvalField, "{$type}_approved_by", "{$type}_approved_at"],
                    'nilai_lama' => [
                        $approvalField => false,
                        "{$type}_approved_by" => null,
                        "{$type}_approved_at" => null,
                    ],
                    'nilai_baru' => [
                        $approvalField => true,
                        "{$type}_approved_by" => $user->id,
                        "{$type}_approved_at" => now()->toDateTimeString(),
                    ],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'keterangan' => "Data disetujui oleh {$type}: {$user->name}",
                ]);
                $sourceUuid = $item->is_audit ? $item->source_uuid : $item->uuid;
                if ($sourceUuid) {
                    PemasakanNasi::where('is_audit', false)->where('uuid', $sourceUuid)->update($updateData);
                    PemasakanNasi::where('is_audit', true)->where('source_uuid', $sourceUuid)->update($updateData);
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Berhasil memverifikasi ' . $pending->count() . ' data ' . strtoupper($type) . '.']);
    }

    public function bulkApprove(Request $request)
    {
        try {
            $request->validate([
                'tanggal'  => 'required|date',
                'shift_id' => 'required|exists:data_shift,id',
                'type'     => 'required|in:qc,produksi,spv'
            ]);

            $user     = auth()->user();
            $type     = $request->type;
            $tanggal  = $request->tanggal;
            $shift_id = $request->shift_id;

            $allowedRoles = [
                'qc'       => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv'      => [1, 4, 5]
            ];

            if (!in_array($user->id_role, $allowedRoles[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melakukan approval ini.'
                ], 403);
            }

            $items = PemasakanNasi::whereDate('tanggal', $tanggal)
                ->where('shift_id', $shift_id)
                ->where('id_plan', $user->id_plan)
                ->get();

            if ($items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data untuk disetujui pada tanggal dan shift tersebut.'
                ], 404);
            }

            $approvalField = "approved_by_{$type}";
            $approverField = "{$type}_approved_by";
            $atField       = "{$type}_approved_at";

            foreach ($items as $item) {
                if ($type === 'produksi' && !$item->approved_by_qc) continue;
                if ($type === 'spv' && !$item->approved_by_produksi) continue;
                if ($item->$approvalField) continue;

                $item->update([
                    $approvalField => true,
                    $approverField => $user->id,
                    $atField       => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui secara massal.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Copy to audit with smart logic
     */
    public function copyToAudit($uuid)
    {
        $user = auth()->user();

        // Auditor (id_role 6) bisa akses audit, atau user dengan permission
        if ($user->id_role != 6 && !$user->hasPermissionTo('create-pemasakan-nasi')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk membuat data audit.');
        }

        $item = PemasakanNasi::where('uuid', $uuid)->firstOrFail();

        // Jika item yang diklik sudah merupakan record audit
        if ($item->is_audit) {
            return redirect()->route('pemasakan-nasi.edit', $item->uuid);
        }

        // Jika data operasional ini sudah memiliki versi audit sebelumnya
        if ($item->auditVersion) {
            return redirect()->route('pemasakan-nasi.edit', $item->auditVersion->uuid);
        }

        // Jika belum memiliki versi audit, buatkan salinan audit secara otomatis
        $clone = $item->copyToAudit();

        return redirect()->route('pemasakan-nasi.edit', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    // --- AUDIT TRAIL METHODS ---

    protected function auditModel(): string
    {
        return PemasakanNasi::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'pemasakan-nasi';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.pemasakan_nasi';
    }

    protected function auditRelations(): array
    {
        return [
            'plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover'
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
