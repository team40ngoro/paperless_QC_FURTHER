<?php

namespace App\Http\Controllers;

use App\Models\AreaProses;
use App\Models\DataShift;
use App\Models\InputArea;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Http\Controllers\Traits\HasAuditController;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Http\Controllers\Traits\HasExcelImport;
use App\Services\BulkExport\AreaProsesBulkExportService;

class AreaProsesController extends Controller
{
    use HasAuditController, HasNoDataHtmlResponse, HasExcelImport;

    protected function auditModel(): string
    {
        return \App\Models\AreaProses::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'area-proses';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.area_proses';
    }

    protected function auditRelations(): array
    {
        return ['user', 'plan', 'area', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'groupRecords'];
    }

    protected function auditSearchColumns(): array
    {
        return ['tanggal'];
    }

    protected function auditTimeColumn(): string
    {
        return 'jam';
    }

    /**
     * Override auditIndex (data datar, satu baris = satu record)
     */
    public function auditIndex(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        // Ambil daftar source_uuid yang sudah di-copy ke audit
        $auditedSourceUuids = AreaProses::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $query = AreaProses::with($this->auditRelations())
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
                $q->where('tanggal', 'LIKE', '%' . $search . '%')
                  ->orWhere('area_id', $search)
                  ->orWhereHas('area', function($sq) use ($search) {
                      $sq->where('area', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        // Get areas based on user role
        if ($user->role === 'superadmin') {
            $areas = InputArea::with('plan')->get();
        } else {
            $areas = InputArea::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
        }

        return view('qc-sistem.area_proses.index_audit', compact('records', 'search', 'perPage', 'areas'));
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Auditor langsung diarahkan ke halaman audit
        if ($user->id_role == 6) {
            return redirect()->route('area-proses.audit');
        }

        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);
        
        $query = AreaProses::with(['user', 'plan', 'area', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'groupRecords', 'auditVersion'])
            ->operasional() // filter is_audit = false via HasAudit scope
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });
        
        // Optional filter to show history per group_uuid
        if ($request->filled('group_uuid')) {
            $query->where('group_uuid', $request->get('group_uuid'));
        } else {
            // Hanya tampilkan data utama (parent) di halaman index utama
            $query->where(function($q) {
                $q->whereColumn('uuid', 'group_uuid')
                  ->orWhereNull('group_uuid');
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('tanggal', 'LIKE', '%' . $search . '%')
                  ->orWhere('area_id', $search)
                  ->orWhereHas('area', function($sq) use ($search) {
                      $sq->where('area', 'LIKE', '%' . $search . '%');
                  });
            });
        }
        
        $this->applyDateRangeFilter(request(), $query);
        $areaProses = $query->orderBy('tanggal', 'desc')
                            ->orderBy('jam', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->orderBy('id', 'desc')
                            ->paginate($perPage);

        // Get areas and shifts based on user role
        if ($user->role === 'superadmin') {
            $areas = InputArea::with('plan')->get();
            $shifts = DataShift::with('plan')->get();
        } else {
            $areas = InputArea::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
            $shifts = DataShift::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
        }

        return view('qc-sistem.area_proses.index', [
            'areaProses' => $areaProses,
            'search' => $search,
            'areas' => $areas,
            'shifts' => $shifts,
            'perPage' => $perPage
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get areas based on user role
        if ($user->role === 'superadmin') {
            $areas = InputArea::with('plan')->get();
            $shifts = DataShift::with('plan')->get();
        } else {
            $areas = InputArea::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
            $shifts = DataShift::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
        }

        return view('qc-sistem.area_proses.create', compact('areas', 'shifts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'area_id' => 'required|array',
            'area_id.*' => 'required|exists:input_area,id',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'required|array',
            'jam.*' => 'required|string|max:10',
            'kebersihan_ruangan' => 'required|array',
            'kebersihan_ruangan.*' => 'required|in:OK,Kotor',
            'kebersihan_karyawan' => 'required|array',
            'kebersihan_karyawan.*' => 'required|in:OK,Kotor',
            'pemeriksaan_suhu_ruang' => 'required|array',
            'pemeriksaan_suhu_ruang.*' => 'required|string|max:50',
            'ketidaksesuaian' => 'array',
            'tindakan_koreksi' => 'array',
            'kondisi_barang' => 'array',
            'suhu_rm' => 'nullable|array',
            'suhu_rm.*' => 'nullable',
        ]);

        $selectedShift = DataShift::findOrFail($request->shift_id);
        
        if ($user->role !== 'superadmin') {
            if ($selectedShift->id_plan !== $user->id_plan) {
                return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.']);
            }
        }

        $count = count($request->area_id);
        for ($i = 0; $i < $count; $i++) {
            $selectedArea = InputArea::findOrFail($request->area_id[$i]);
            
            if ($user->role !== 'superadmin' && $selectedArea->id_plan !== $user->id_plan) {
                continue; // Skip areas user doesn't have access to
            }

            $suhuRmVal = $request->suhu_rm[$i] ?? null;
            if (is_array($suhuRmVal)) {
                $suhuRmFiltered = array_filter(array_map('trim', $suhuRmVal), fn($val) => $val !== '' && !is_null($val));
                $suhuRmVal = !empty($suhuRmFiltered) ? implode(', ', $suhuRmFiltered) : null;
            }

            AreaProses::create([
                'uuid' => Str::uuid(),
                'user_id' => $user->id,
                'id_plan' => $selectedArea->id_plan,
                'area_id' => $request->area_id[$i],
                'shift_id' => $request->shift_id,
                'tanggal' => $request->tanggal,
                'jam' => $request->jam[$i],
                'kebersihan_ruangan' => $request->kebersihan_ruangan[$i],
                'kebersihan_karyawan' => $request->kebersihan_karyawan[$i],
                'pemeriksaan_suhu_ruang' => $request->pemeriksaan_suhu_ruang[$i],
                'suhu_rm' => $suhuRmVal,
                'ketidaksesuaian' => $request->ketidaksesuaian[$i] ?? null,
                'tindakan_koreksi' => $request->tindakan_koreksi[$i] ?? null,
                'kondisi_barang' => $request->kondisi_barang[$i] ?? null,
            ]);
        }

        return redirect()->route('area-proses.index')
            ->with('success', 'Data area proses berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $user = Auth::user();
        $areaProses = AreaProses::with(['user', 'plan', 'area', 'shift', 'groupRecords.user', 'qcApprover', 'produksiApprover', 'spvApprover'])->where('uuid', $uuid)->firstOrFail();

        // Check access based on user role
        if ($user->role !== 'superadmin' && $areaProses->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('area-proses.show', $areaProses)) {
            return $redirect;
        }

        return view('qc-sistem.area_proses.show', compact('areaProses'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $user = Auth::user();
        $areaProses = AreaProses::where('uuid', $uuid)->firstOrFail();

        // Check access based on user role
        if ($user->role !== 'superadmin' && $areaProses->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Get areas based on user role
        if ($user->role === 'superadmin') {
            $areas = InputArea::with('plan')->get();
            $shifts = DataShift::with('plan')->get();
        } else {
            $areas = InputArea::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
            $shifts = DataShift::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
        }

        $isAuditMode = (bool) $areaProses->is_audit;
        $queryParameters = request()->query();
        $hasAuditFlag = array_key_exists('is_audit', $queryParameters);

        if (($isAuditMode && request()->query('is_audit') !== '1') || (!$isAuditMode && $hasAuditFlag)) {
            unset($queryParameters['is_audit'], $queryParameters['uuid']);
            if ($isAuditMode) {
                $queryParameters['is_audit'] = '1';
            }

            return redirect()->route(
                'area-proses.edit',
                array_merge(['uuid' => $areaProses->uuid], $queryParameters)
            );
        }

        $is_audit = $isAuditMode;

        return view('qc-sistem.area_proses.edit', compact('areaProses', 'areas', 'shifts', 'is_audit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $user = Auth::user();
        $areaProses = AreaProses::where('uuid', $uuid)->firstOrFail();

        // Check access based on user role
        if ($user->role !== 'superadmin' && $areaProses->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        $request->validate([
            'area_id' => 'required|exists:input_area,id',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'required|string|max:10',
            'kebersihan_ruangan' => 'required|in:OK,Tidak OK',
            'kebersihan_karyawan' => 'required|in:OK,Tidak OK',
            'pemeriksaan_suhu_ruang' => 'required|string|max:50',
            'ketidaksesuaian' => 'nullable|string|max:1000',
            'tindakan_koreksi' => 'nullable|string|max:1000',
            'kondisi_barang' => 'nullable|string',
            'suhu_rm' => 'nullable',
        ]);

        // Get the selected area to validate plan access
        $selectedArea = InputArea::findOrFail($request->area_id);
        $selectedShift = DataShift::findOrFail($request->shift_id);
        
        // Additional validation for non-superadmin users
        if ($user->role !== 'superadmin') {
            if ($selectedArea->id_plan !== $user->id_plan) {
                return back()->withErrors(['area_id' => 'Anda tidak memiliki akses ke area ini.']);
            }
            if ($selectedShift->id_plan !== $user->id_plan) {
                return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.']);
            }
        }

        $suhuRmVal = $request->suhu_rm;
        if (is_array($suhuRmVal)) {
            $suhuRmFiltered = array_filter(array_map('trim', $suhuRmVal), fn($val) => $val !== '' && !is_null($val));
            $suhuRmVal = !empty($suhuRmFiltered) ? implode(', ', $suhuRmFiltered) : null;
        }

        $areaProses->update([
            'id_plan' => $selectedArea->id_plan,
            'area_id' => $request->area_id,
            'shift_id' => $request->shift_id,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'kebersihan_ruangan' => $request->kebersihan_ruangan,
            'kebersihan_karyawan' => $request->kebersihan_karyawan,
            'pemeriksaan_suhu_ruang' => $request->pemeriksaan_suhu_ruang,
            'suhu_rm' => $suhuRmVal,
            'ketidaksesuaian' => $request->ketidaksesuaian,
            'tindakan_koreksi' => $request->tindakan_koreksi,
            'kondisi_barang' => $request->kondisi_barang,
        ]);

        if ($user->id_role == 6 || $areaProses->is_audit) {
            return redirect()->route('area-proses.audit')
                ->with('success', 'Data area proses berhasil diperbarui.');
        }

        return redirect()->route('area-proses.index')
            ->with('success', 'Data area proses berhasil diperbarui.');
    }

    /**
     * Show the form for editing per 2 hours (create new record).
     */
    public function twoHourEdit($uuid)
    {
        $areaProses = AreaProses::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $areaProses->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }
        
        if ($user->role === 'superadmin') {
            $areas = InputArea::with('plan')->get();
            $shifts = DataShift::with('plan')->get();
        } else {
            $areas = InputArea::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
            $shifts = DataShift::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
        }
        
        $twoHour = true; // flag untuk view
        return view('qc-sistem.area_proses.edit', compact('areaProses', 'areas', 'shifts', 'twoHour'));
    }

    /**
     * Store new record for 2 hour edit.
     */
    public function twoHourStore(Request $request, $uuid)
    {
        $user = Auth::user();
        $request->validate([
            'area_id' => 'required|exists:input_area,id',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'required|string|max:10',
            'kebersihan_ruangan' => 'required|in:OK,Kotor',
            'kebersihan_karyawan' => 'required|in:OK,Kotor',
            'pemeriksaan_suhu_ruang' => 'required|string|max:50',
            'ketidaksesuaian' => 'nullable|string|max:1000',
            'tindakan_koreksi' => 'nullable|string|max:1000',
            'kondisi_barang' => 'nullable|string',
            'suhu_rm' => 'nullable',
            
        ]);

        $original = AreaProses::where('uuid', $uuid)->firstOrFail();
        
        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $original->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }
        
        $groupUuid = $original->group_uuid ?: $original->uuid; // gunakan uuid asli sebagai group jika belum ada

        // Get the selected area to validate plan access
        $selectedArea = InputArea::findOrFail($request->area_id);
        $selectedShift = DataShift::findOrFail($request->shift_id);
        
        // Additional validation for non-superadmin users
        if ($user->role !== 'superadmin') {
            if ($selectedArea->id_plan !== $user->id_plan) {
                return back()->withErrors(['area_id' => 'Anda tidak memiliki akses ke area ini.']);
            }
            if ($selectedShift->id_plan !== $user->id_plan) {
                return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.']);
            }
        }

        $twoHourSuhuRmVal = $request->suhu_rm;
        if (is_array($twoHourSuhuRmVal)) {
            $suhuRmFiltered = array_filter(array_map('trim', $twoHourSuhuRmVal), fn($val) => $val !== '' && !is_null($val));
            $twoHourSuhuRmVal = !empty($suhuRmFiltered) ? implode(', ', $suhuRmFiltered) : null;
        }

        AreaProses::create([
            'uuid' => Str::uuid(),
            'group_uuid' => $groupUuid,
            'user_id' => $user->id,
            'id_plan' => $selectedArea->id_plan,
            'area_id' => $request->area_id,
            'shift_id' => $request->shift_id,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'kebersihan_ruangan' => $request->kebersihan_ruangan,
            'kebersihan_karyawan' => $request->kebersihan_karyawan,
            'pemeriksaan_suhu_ruang' => $request->pemeriksaan_suhu_ruang,
            'suhu_rm' => $twoHourSuhuRmVal,
            'ketidaksesuaian' => $request->ketidaksesuaian,
            'tindakan_koreksi' => $request->tindakan_koreksi,
            'kondisi_barang'  => $request->kondisi_barang,
        ]);

        // Pastikan original menyimpan group_uuid agar terhubung dalam riwayat
        if (!$original->group_uuid) {
            $original->update(['group_uuid' => $groupUuid]);
        }

        return redirect()->route('area-proses.index', ['group_uuid' => $groupUuid])
            ->with('success', 'Data per 2 jam berhasil disimpan sebagai record baru');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $user = Auth::user();
        $areaProses = AreaProses::where('uuid', $uuid)->firstOrFail();

        // Check access based on user role
        if ($user->role !== 'superadmin' && $areaProses->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $areaProses->delete();

        return redirect()->route('area-proses.index')
            ->with('success', 'Data area proses berhasil dihapus.');
    }

    /**
     * Approve data with role-based validation
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $data = AreaProses::where('uuid', $uuid)->firstOrFail();
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
        if ($type === 'produksi' && !$data->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$data->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($data->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $data->update([
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
     * Export filtered data to PDF
     */
    public function bulkExportPdf(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'shift' => 'nullable|string',
            'area' => 'nullable|string',
            'kode_form' => 'required|string'
        ]);

        $user = Auth::user();
        $query = AreaProses::with(['area', 'plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover']);
        
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
        
        if ($request->shift) {
            $query->whereHas('shift', function($q) use ($request) {
                $q->where('shift', $request->shift);
            });
        }
        
        if ($request->area) {
            $query->whereHas('area', function($q) use ($request) {
                $q->where('area', $request->area);
            });
        }
        
        // Debug: Log the query and filters
        \Log::info('Area Proses PDF Export Query Debug', [
            'tanggal' => $request->tanggal,
            'shift' => $request->shift,
            'area' => $request->area,
            'kode_form' => $request->kode_form,
            'user_plan' => $user->id_plan,
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);
        
        // First, update kode_form for matching records before fetching data for PDF
        if ($request->kode_form) {
            $query_for_update = AreaProses::query();
            
            // Apply same filters for update
            if ($user->role !== 'superadmin') {
                $query_for_update->where('id_plan', $user->id_plan);
            }
            
            if ($request->tanggal_awal && $request->tanggal_akhir) {
                $tanggalAwal = \Carbon\Carbon::parse($request->tanggal_awal)->startOfDay();
                $tanggalAkhir = \Carbon\Carbon::parse($request->tanggal_akhir)->endOfDay();
                $query_for_update->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            } elseif ($request->tanggal_awal) {
                $query_for_update->whereDate('tanggal', $request->tanggal_awal);
            }
            
            if ($request->shift) {
                $query_for_update->whereHas('shift', function($q) use ($request) {
                    $q->where('shift', $request->shift);
                });
            }
            
            if ($request->area) {
                $query_for_update->whereHas('area', function($q) use ($request) {
                    $q->where('area', $request->area);
                });
            }
            
            // Update kode_form for matching records BEFORE fetching data
            $updated_count = $query_for_update->update(['kode_form' => $request->kode_form]);
            \Log::info('Updated kode_form for Area Proses records', ['count' => $updated_count, 'kode_form' => $request->kode_form]);
        }
        
        // Now fetch the updated data for PDF
        $data = $query->orderBy('tanggal', 'desc')->get();
        
        \Log::info('Area Proses PDF Export Data Count', ['count' => $data->count()]);
        
        // Jika tidak ada data, tampilkan halaman error HTML (tidak download PDF)
        if ($data->isEmpty()) {
            $errorMessage = 'Tidak ada data Area Proses yang sesuai dengan filter yang dipilih.';
            $filterInfo = [];
            
            if ($request->tanggal_awal && $request->tanggal_akhir) {
                $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
            } elseif ($request->tanggal_awal) {
                $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y');
            }
            if ($request->shift) {
                $filterInfo[] = 'Shift: ' . $request->shift;
            }
            if ($request->area) {
                $filterInfo[] = 'Area: ' . $request->area;
            }
            if ($request->kode_form) {
                $filterInfo[] = 'Kode Form: ' . $request->kode_form;
            }
            
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Data Tidak Ditemukan</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 40px; text-align: center; }
                    .container { max-width: 600px; margin: 0 auto; padding: 40px; border: 2px dashed #ccc; background-color: #f9f9f9; }
                    h1 { color: #d9534f; margin-bottom: 20px; }
                    .message { font-size: 16px; color: #666; margin-bottom: 30px; }
                    .filter-info { background-color: #fff; padding: 20px; border-radius: 5px; margin: 20px 0; }
                    .filter-info h4 { margin-top: 0; color: #333; }
                    .filter-info ul { text-align: left; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>Data Area Proses Tidak Ditemukan</h1>
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
                </div>
            </body>
            </html>';
            
            return response($html)->header('Content-Type', 'text/html');
        }

        // Prepare filter info for PDF (include kode_form for identification)
        $tanggalFilter = 'Semua Tanggal';
        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $tanggalFilter = \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
        } elseif ($request->tanggal_awal) {
            $tanggalFilter = \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y');
        }

        $filterInfo = [
            'tanggal' => $tanggalFilter,
            'shift' => $request->shift ?: 'Semua Shift',
            'area' => $request->area ?: 'Semua Area',
            'kode_form' => $request->kode_form
        ];
        
        $pdf = \PDF::loadView('qc-sistem.area_proses.export_pdf', compact('data', 'filterInfo'))
                   ->setPaper('letter', 'landscape');
        
        $filename = 'area-proses-' . date('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export single data group to PDF by UUID
     */
    public function exportPdf($uuid)
    {
        $item = AreaProses::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Check access
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        // Fetch all related records in the same group
        $groupUuid = $item->group_uuid ?: $item->uuid;
        $data = AreaProses::with(['area', 'plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->operasional()
            ->where(function($q) use ($groupUuid) {
                $q->where('group_uuid', $groupUuid)
                  ->orWhere('uuid', $groupUuid);
            })
            ->orderBy('tanggal', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $filterInfo = [
            'tanggal' => $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') : '-',
            'shift' => $item->shift ? 'Shift ' . $item->shift->shift : 'Semua Shift',
            'area' => $item->area ? $item->area->area : 'Semua Area',
            'kode_form' => $item->kode_form ?? 'QF 25.00'
        ];

        $pdf = \PDF::loadView('qc-sistem.area_proses.export_pdf', compact('data', 'filterInfo'))
                   ->setPaper('letter', 'landscape');

        $filename = 'area-proses-' . ($item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : 'data') . '-' . substr($uuid, 0, 8) . '.pdf';
        
        return $pdf->stream($filename);
    }
    public function bulkApprove(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'shift_id' => 'required|exists:data_shift,id',
                'area_id' => 'required|exists:input_area,id',
                'type' => 'required|in:qc,produksi,spv'
            ]);

            $user = Auth::user();
            $type = $request->type;
            $tanggal = $request->tanggal;
            $shiftId = $request->shift_id;
            $areaId = $request->area_id;

            $items = AreaProses::whereDate('tanggal', $tanggal)
                ->where('shift_id', $shiftId)
                ->where('area_id', $areaId)
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
                        "{$type}_approved_by" => $user->id,
                        "{$type}_approved_at" => now(),
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Berhasil menyetujui data secara massal.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
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
            $query = AreaProses::whereIn('uuid', $uuids)
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

            AreaProses::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();

            // Update audit copies with same approval data
            AreaProses::where('is_audit', true)
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

    public function bulkExportExcel(Request $request, AreaProsesBulkExportService $service)
    {
        return $service->handle($request, fn ($msg) => $this->buildNoDataHtmlResponse($msg));
    }

    /**
     * Get the model class for Excel import (required by HasExcelImport trait)
     */
    protected function getImportModelClass(): string
    {
        return AreaProses::class;
    }

    /**
     * Get template filename with timestamp (required by HasExcelImport trait)
     */
    protected function getTemplateFilename(): string
    {
        return 'Template_Area_Proses_' . date('YmdHis') . '.xlsx';
    }
}