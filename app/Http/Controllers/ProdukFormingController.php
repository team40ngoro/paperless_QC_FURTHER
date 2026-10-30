<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Models\ProdukForming;
use App\Models\ProdukFormingLog;
use App\Models\JenisProduk;
use App\Models\DataShift;
use App\Services\BulkExport\ProdukFormingBulkExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdukFormingController extends Controller
{
    use \App\Http\Controllers\Traits\HasAuditController, HasNoDataHtmlResponse;

    protected function auditModel(): string
    {
        return ProdukForming::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'produk-forming';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.produk_forming';
    }

    protected function auditDateColumn(): string
    {
        return 'tanggal';
    }

    protected function auditTimeColumn(): string
    {
        return 'tanggal';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Auditor langsung diarahkan ke halaman audit
        if ($user->id_role == 6) {
            return redirect()->route('produk-forming.audit');
        }
        $perPage = (int) request('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 10;
        }
        $search = request('search');
        
        $query = ProdukForming::with(['produk', 'plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->where('is_audit', false);
        
        // Filter berdasarkan plan user jika bukan superadmin
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if($search) {
            $query->whereHas('produk', function($q) use ($search) {
                $q->where('nama_produk', 'LIKE', '%' . $search . '%');
            });
        }
        
        // Pagination on records (not groups)
        $this->applyDateRangeFilter(request(), $query);
        $data = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
        
        return view('qc-sistem.produk_forming.index', compact('data', 'search', 'perPage'));
    }

    /**
     * Audit Index for Produk Forming
     */
    public function auditIndex(Request $request)
    {
        $user = Auth::user();
        $perPage = request('per_page', 10);
        $search = request('search');
        
        // Get source_uuid that have been copied to audit (to exclude from operational view)
        $auditedSourceUuids = ProdukForming::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();
        
        $query = ProdukForming::with(['produk', 'plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->where(function ($q) use ($auditedSourceUuids) {
                $q->where('is_audit', true)
                  ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                      $subQ->where('is_audit', false);
                      if (!empty($auditedSourceUuids)) {
                          $subQ->whereNotIn('uuid', $auditedSourceUuids);
                      }
                  });
            });
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if($search) {
            $query->whereHas('produk', function($q) use ($search) {
                $q->where('nama_produk', 'LIKE', '%' . $search . '%');
            });
        }
        
        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
        
        // Load shifts and products for export filter modal
        $cachedShifts = \App\Models\DataShift::all();
        $cachedProduk = \App\Models\ProdukForming::whereNotNull('id_produk')->distinct('id_produk')->with('produk')->get();
        
        return view('qc-sistem.produk_forming.index_audit', compact('records', 'search', 'perPage', 'cachedShifts', 'cachedProduk'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get only Forming products - filter by plan if not superadmin
        $query = JenisProduk::where('status_bahan', 'forming');
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $produks = $query->get();
        
        if ($user->role === 'superadmin') {      
            $shifts = DataShift::all();
          
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        // Load master mesin/peralatan filtered by Forming
        $mesinQuery = \App\Models\InputMesinPeralatan::with('area')->orderBy('id_area')->orderBy('nama_mesin');
        if ($user->role !== 'superadmin') {
            $mesinQuery->where('id_plan', $user->id_plan);
        }
        $masterMesin = $mesinQuery->whereJsonContains('jenis_produk', 'Forming')->get();

        // Load input areas filtered by plan
        $inputAreaQuery = \App\Models\InputArea::orderBy('area');
        if ($user->role !== 'superadmin') {
            $inputAreaQuery->where('id_plan', $user->id_plan);
        }
        $inputAreas = $inputAreaQuery->get();
        
        return view('qc-sistem.produk_forming.create', compact('produks', 'shifts', 'masterMesin', 'inputAreas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'id_produk'          => 'required|exists:jenis_produk,id',
            'id_formula'         => 'nullable|exists:nomor_formula,id',
            'id_shift'           => 'required|exists:data_shift,id',
            'tanggal'            => 'required|date',
            'jam'                => 'required',
            'bahan_baku'         => 'nullable|array',
            'bahan_penunjang'    => 'nullable|array',
            'kemasan_plastik'    => 'nullable|array',
            'kemasan_plastik.*'  => 'nullable|integer|min:1|max:8',
            'kemasan_karton'     => 'nullable|array',
            'kemasan_karton.*'   => 'nullable|integer|min:1|max:8',
            'labelisasi_plastik' => 'nullable|array',
            'labelisasi_plastik.*' => 'nullable|integer|min:1|max:8',
            'labelisasi_karton'  => 'nullable|array',
            'labelisasi_karton.*' => 'nullable|integer|min:1|max:8',
            'mesin_peralatan'    => 'nullable|array',
            'mesin_peralatan.*.nama_mesin'       => 'nullable|string',
            'mesin_peralatan.*.kondisi'          => 'nullable|array',
            'mesin_peralatan.*.kondisi.*'        => 'nullable|integer|min:3|max:8',
            'mesin_peralatan.*.tindakan_koreksi' => 'nullable|string',
            'mesin_peralatan.*.verifikasi'       => 'nullable|string',
            'mesin_peralatan.*.dihapus'          => 'nullable|in:0,1',
            'ruangan'                            => 'nullable|array',
            'ruangan.*.nama_ruangan'             => 'nullable|string',
            'ruangan.*.kondisi'                  => 'nullable|array',
            'ruangan.*.kondisi.*'                => 'nullable|integer|min:3|max:8',
            'ruangan.*.tindakan_koreksi'         => 'nullable|string',
            'ruangan.*.verifikasi'               => 'nullable|string',
            'ruangan.*.dihapus'                  => 'nullable|in:0,1',
            'penilaian'          => 'nullable|array',
            'tindakan_koreksi'   => 'nullable|string',
            'verifikasi'         => 'nullable|string',
            'keterangan'         => 'nullable|string',
        ]);

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['id_plan'] = $user->id_plan;
        $data['tanggal'] = \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now());
        $data['jam']     = $request->jam;
         
        // Ensure arrays are properly formatted
        if (isset($data['bahan_baku']) && is_array($data['bahan_baku'])) {
            $data['bahan_baku'] = array_values(array_filter($data['bahan_baku'], function($item) {
                return is_array($item) && isset($item['nama']) && isset($item['penilaian']) && 
                       trim($item['nama']) !== '' && (is_array($item['penilaian']) ? count($item['penilaian']) > 0 : trim($item['penilaian']) !== '');
            }));
        }
        
        if (isset($data['bahan_penunjang']) && is_array($data['bahan_penunjang'])) {
            $data['bahan_penunjang'] = array_values(array_filter($data['bahan_penunjang'], function($item) {
                return is_array($item) && isset($item['nama']) && isset($item['penilaian']) && 
                       trim($item['nama']) !== '' && trim($item['penilaian']) !== '';
            }));
        }
        
        if (isset($data['penilaian']) && is_array($data['penilaian'])) {
            $data['penilaian'] = array_values(array_filter($data['penilaian'], function($item) {
                return is_array($item) && isset($item['aspek']) && isset($item['nilai']) && 
                       trim($item['aspek']) !== '' && trim($item['nilai']) !== '';
            }));
        }

        // Buang baris Mesin/Peralatan & Ruangan yang ditandai "dihapus" (tidak dipakai) di form,
        // supaya baris tersebut tidak ikut tersimpan sama sekali ke database.
        if (isset($data['mesin_peralatan']) && is_array($data['mesin_peralatan'])) {
            $data['mesin_peralatan'] = array_values(array_map(function ($item) {
                unset($item['dihapus']);
                return $item;
            }, array_filter($data['mesin_peralatan'], function ($item) {
                return is_array($item) && empty($item['dihapus']);
            })));
        }

        if (isset($data['ruangan']) && is_array($data['ruangan'])) {
            $data['ruangan'] = array_values(array_map(function ($item) {
                unset($item['dihapus']);
                return $item;
            }, array_filter($data['ruangan'], function ($item) {
                return is_array($item) && empty($item['dihapus']);
            })));
        }

        ProdukForming::create($data);

        return redirect()->route('produk-forming.index')
                        ->with('success', 'Data produk forming berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $produkForming = ProdukForming::with(['produk', 'plan', 'shift', 'user'])->where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check authorization
        if ($user->role !== 'superadmin' && $produkForming->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('produk-forming.show', $produkForming)) {
            return $redirect;
        }
        
        return view('qc-sistem.produk_forming.show', compact('produkForming'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $produkForming = ProdukForming::with(['produk', 'plan', 'shift', 'user'])->where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check authorization
        if ($user->role !== 'superadmin' && $produkForming->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        // Determine if in audit mode
        $isAuditMode = (bool) $produkForming->is_audit;
        $queryParameters = request()->query();
        $hasAuditFlag = array_key_exists('is_audit', $queryParameters);

        if (($isAuditMode && request()->query('is_audit') !== '1') || (!$isAuditMode && $hasAuditFlag)) {
            unset($queryParameters['is_audit'], $queryParameters['uuid']);
            if ($isAuditMode) {
                $queryParameters['is_audit'] = '1';
            }

            return redirect()->route(
                'produk-forming.edit',
                array_merge(['uuid' => $produkForming->uuid], $queryParameters)
            );
        }
        
        // Get only Forming products - filter by plan if not superadmin
        $query = JenisProduk::where('status_bahan', 'forming');
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $produks = $query->get();
        
        // Filter shifts by plant in audit mode
        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        // Load input areas for Ruangan section
        $inputAreaQuery = \App\Models\InputArea::orderBy('area');
        if ($user->role !== 'superadmin') {
            $inputAreaQuery->where('id_plan', $user->id_plan);
        }
        $inputAreas = $inputAreaQuery->get();
        
        return view('qc-sistem.produk_forming.edit', compact('produkForming', 'produks', 'shifts', 'inputAreas', 'isAuditMode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $produkForming = ProdukForming::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check authorization
        if ($user->role !== 'superadmin' && $produkForming->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
        
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_formula' => 'nullable|exists:nomor_formula,id',
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'bahan_baku' => 'nullable|array',
            'bahan_penunjang' => 'nullable|array',
            'kemasan_plastik'    => 'nullable|array',
            'kemasan_plastik.*'  => 'nullable|integer|min:1|max:8',
            'kemasan_karton'     => 'nullable|array',
            'kemasan_karton.*'   => 'nullable|integer|min:1|max:8',
            'labelisasi_plastik' => 'nullable|array',
            'labelisasi_plastik.*' => 'nullable|integer|min:1|max:8',
            'labelisasi_karton'  => 'nullable|array',
            'labelisasi_karton.*' => 'nullable|integer|min:1|max:8',
            'mesin_peralatan'    => 'nullable|array',
            'mesin_peralatan.*.nama_mesin'       => 'nullable|string',
            'mesin_peralatan.*.kondisi'          => 'nullable|array',
            'mesin_peralatan.*.kondisi.*'        => 'nullable|integer|min:3|max:8',
            'mesin_peralatan.*.tindakan_koreksi' => 'nullable|string',
            'mesin_peralatan.*.verifikasi'       => 'nullable|string',
            'mesin_peralatan.*.dihapus'          => 'nullable|in:0,1',
            'ruangan'                            => 'nullable|array',
            'ruangan.*.nama_ruangan'             => 'nullable|string',
            'ruangan.*.kondisi'                  => 'nullable|array',
            'ruangan.*.kondisi.*'                => 'nullable|integer|min:3|max:8',
            'ruangan.*.tindakan_koreksi'         => 'nullable|string',
            'ruangan.*.verifikasi'               => 'nullable|string',
            'ruangan.*.dihapus'                  => 'nullable|in:0,1',
            'autogrind' => 'nullable|integer|min:3|max:8',
            'bowlcutter' => 'nullable|integer|min:3|max:8',
            'ayakan_seasoning' => 'nullable|integer|min:3|max:8',
            'unimix' => 'nullable|integer|min:3|max:8',
            'revoformer' => 'nullable|integer|min:3|max:8',
            'better_mixer' => 'nullable|integer|min:3|max:8',
            'wet_coater' => 'nullable|integer|min:3|max:8',
            'breader' => 'nullable|integer|min:3|max:8',
            'frayer_1' => 'nullable|integer|min:3|max:8',
            'frayer_2' => 'nullable|integer|min:3|max:8',
            'iqf_jbt' => 'nullable|integer|min:3|max:8',
            'keranjang' => 'nullable|integer|min:3|max:8',
            'timbangan' => 'nullable|integer|min:3|max:8',
            'mhw' => 'nullable|integer|min:3|max:8',
            'foot_sealer' => 'nullable|integer|min:3|max:8',
            'metal_detector' => 'nullable|integer|min:3|max:8',
            'rotary_table' => 'nullable|integer|min:3|max:8',
            'carton_sealer' => 'nullable|integer|min:3|max:8',
            'meatcar' => 'nullable|integer|min:3|max:8',
            'check_weigher_bag' => 'nullable|integer|min:3|max:8',
            'check_weigher_box' => 'nullable|integer|min:3|max:8',
            'penilaian' => 'nullable|array',
            'tindakan_koreksi' => 'nullable|string',
            'verifikasi' => 'nullable|string|in:✓,✗',
            'keterangan' => 'nullable|string'
        ]);

        $data = $request->all();
        
        // Ensure arrays are properly formatted
        if (isset($data['bahan_baku']) && is_array($data['bahan_baku'])) {
            $data['bahan_baku'] = array_values(array_filter($data['bahan_baku'], function($item) {
                return is_array($item) && isset($item['nama']) && isset($item['penilaian']) && 
                       trim($item['nama']) !== '' && (is_array($item['penilaian']) ? count($item['penilaian']) > 0 : trim($item['penilaian']) !== '');
            }));
        }
        
        if (isset($data['bahan_penunjang']) && is_array($data['bahan_penunjang'])) {
            $data['bahan_penunjang'] = array_values(array_filter($data['bahan_penunjang'], function($item) {
                return is_array($item) && isset($item['nama']) && isset($item['penilaian']) && 
                       trim($item['nama']) !== '' && trim($item['penilaian']) !== '';
            }));
        }
        
        if (isset($data['penilaian']) && is_array($data['penilaian'])) {
            $data['penilaian'] = array_values(array_filter($data['penilaian'], function($item) {
                return is_array($item) && isset($item['aspek']) && isset($item['nilai']) && 
                       trim($item['aspek']) !== '' && trim($item['nilai']) !== '';
            }));
        }

        // Buang baris Mesin/Peralatan & Ruangan yang ditandai "dihapus" (tidak dipakai) di form,
        // supaya baris tersebut tidak ikut tersimpan sama sekali ke database.
        if (isset($data['mesin_peralatan']) && is_array($data['mesin_peralatan'])) {
            $data['mesin_peralatan'] = array_values(array_map(function ($item) {
                unset($item['dihapus']);
                return $item;
            }, array_filter($data['mesin_peralatan'], function ($item) {
                return is_array($item) && empty($item['dihapus']);
            })));
        }

        if (isset($data['ruangan']) && is_array($data['ruangan'])) {
            $data['ruangan'] = array_values(array_map(function ($item) {
                unset($item['dihapus']);
                return $item;
            }, array_filter($data['ruangan'], function ($item) {
                return is_array($item) && empty($item['dihapus']);
            })));
        }
        
        $produkForming->update($data);

        $redirectRoute = $produkForming->is_audit ? 'produk-forming.audit' : 'produk-forming.index';
        return redirect()->route($redirectRoute)
                        ->with('success', 'Data produk forming berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $produkForming = ProdukForming::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check authorization
        if ($user->role !== 'superadmin' && $produkForming->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }
        
        $produkForming->delete();

        return redirect()->route('produk-forming.index')
                        ->with('success', 'Data produk forming berhasil dihapus.');
    }

    /**
     * Bulk Approve Selected - checkbox-based per-row approval
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
            $query = ProdukForming::whereIn('uuid', $uuids)
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

            ProdukForming::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();
            
            ProdukForming::where('is_audit', true)
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
     * Show logs for a specific produk forming record
     */
    public function showLogs($uuid)
    {
        $produkForming = ProdukForming::with(['produk', 'plan', 'shift', 'user'])->where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check authorization
        if ($user->role !== 'superadmin' && $produkForming->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('qc-sistem.produk_forming.logs', compact('produkForming'));
    }

    /**
     * Get logs data in JSON format for DataTables
     */
    public function getLogsJson($uuid)
    {
        $produkForming = ProdukForming::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check authorization
        if ($user->role !== 'superadmin' && $produkForming->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $logs = ProdukFormingLog::with('user')
            ->where('produk_forming_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [];
        foreach ($logs as $log) {
            $data[] = [
                'tanggal' => $log->created_at->format('d-m-Y H:i:s'),
                'user' => $log->user->name ?? 'Unknown',
                'field_yang_diubah' => $log->nama_field,
                'deskripsi_perubahan' => $log->deskripsi_perubahan,
                'ip_address' => $log->ip_address
            ];
        }

        return response()->json(['data' => $data]);
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
            'produk' => 'nullable|string',
            'kode_form' => 'required|string'
        ]);

        $user = Auth::user();
        $query = ProdukForming::with(['produk', 'plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover']);
        
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
        
        if ($request->produk) {
            $query->whereHas('produk', function($q) use ($request) {
                $q->where('nama_produk', $request->produk);
            });
        }
        
        // Note: kode_form is not used for filtering, only saved to database
        
        // Debug: Log the query and filters
        \Log::info('PDF Export Query Debug', [
            'tanggal' => $request->tanggal,
            'shift' => $request->shift,
            'produk' => $request->produk,
            'kode_form' => $request->kode_form,
            'user_plan' => $user->id_plan,
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);
        
        // First, update kode_form for matching records before fetching data for PDF
        if ($request->kode_form) {
            $query_for_update = ProdukForming::query();
            
            if ($request->has('is_audit') && $request->is_audit == 1) {
                $query_for_update->where('is_audit', true);
            } else {
                $query_for_update->operasional();
            }
            
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
            
            if ($request->produk) {
                $query_for_update->whereHas('produk', function($q) use ($request) {
                    $q->where('nama_produk', $request->produk);
                });
            }
            
            // Update kode_form for matching records BEFORE fetching data
            $updated_count = $query_for_update->update(['kode_form' => $request->kode_form]);
            \Log::info('Updated kode_form for records', ['count' => $updated_count, 'kode_form' => $request->kode_form]);
        }
        
        // Now fetch the updated data for PDF
        $data = $query->orderBy('tanggal', 'desc')->get();
        
        \Log::info('PDF Export Data Count', ['count' => $data->count()]);
        
        // Jika tidak ada data, tampilkan halaman error HTML (tidak download PDF)
        if ($data->isEmpty()) {
            $errorMessage = 'Tidak ada data Produk Forming yang sesuai dengan filter yang dipilih.';
            $filterInfo = [];
            
            if ($request->tanggal_awal && $request->tanggal_akhir) {
                $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
            } elseif ($request->tanggal_awal) {
                $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y');
            }
            if ($request->shift) {
                $filterInfo[] = 'Shift: ' . $request->shift;
            }
            if ($request->produk) {
                $filterInfo[] = 'Produk: ' . $request->produk;
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
                    <h1>Data Produk Forming Tidak Ditemukan</h1>
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
            'produk' => $request->produk ?: 'Semua Produk',
            'kode_form' => $request->kode_form
        ];
        
        $pdf = \PDF::loadView('qc-sistem.produk_forming.export_pdf', compact('data', 'filterInfo'))
                   ->setPaper('letter', 'portrait');
        
        $filename = 'produk-forming-' . date('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Approve data with role-based validation
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $produkForming = ProdukForming::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control following PembuatanSample pattern
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
        if ($type === 'produksi' && !$produkForming->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$produkForming->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($produkForming->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $produkForming->update([
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
     * Copy to audit with smart logic
     */
    public function copyToAudit($uuid)
    {
        $user = auth()->user();

        // Auditor (id_role 6) bisa akses audit, atau user dengan permission
        if ($user->id_role != 6 && !$user->hasPermissionTo('create-produk-forming')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk membuat data audit.');
        }

        $item = ProdukForming::where('uuid', $uuid)->firstOrFail();

        // Jika item yang diklik sudah merupakan record audit
        if ($item->is_audit) {
            return redirect()->route('produk-forming.edit', $item->uuid);
        }

        // Jika data operasional ini sudah memiliki versi audit sebelumnya
        if ($item->auditVersion) {
            return redirect()->route('produk-forming.edit', $item->auditVersion->uuid);
        }

        // Jika belum memiliki versi audit, buatkan salinan audit secara otomatis
        $clone = $item->copyToAudit();

        return redirect()->route('produk-forming.edit', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
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
            $query = ProdukForming::whereDate('tanggal', $request->tanggal)
                ->operasional()
                ->where('id_shift', $request->shift_id);

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

            // Validasi sekuensial (QC -> Produksi -> SPV)
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
                    ProdukFormingLog::create([
                        'uuid' => \Illuminate\Support\Str::uuid(),
                        'produk_forming_id' => $item->id,
                        'produk_forming_uuid' => $item->uuid,
                        'user_id' => $user->id,
                        'field_yang_diubah' => [$approvalField],
                        'nilai_lama' => ['false'],
                        'nilai_baru' => ['true'],
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
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

    public function bulkExportExcel(Request $request, ProdukFormingBulkExportService $service)
    {
        return $service->handle($request, fn ($msg) => $this->buildNoDataHtmlResponse($msg));
    }
}