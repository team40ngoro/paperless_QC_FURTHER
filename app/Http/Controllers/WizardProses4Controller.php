<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\BahanBakuTumbling;
use App\Models\ProsesTumbling;
use App\Models\ProsesAging;
use App\Models\FormulaNonForming;
use App\Models\Tumbling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Services\BulkExport\WizardProses4BulkExportService;

class WizardProses4Controller extends Controller
{
    use HasNoDataHtmlResponse;

    public function bulkExportExcel(Request $request, WizardProses4BulkExportService $service)
    {
        return $service->export($request);
    }
    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal_mulai' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
                'shift_id' => 'nullable|integer',
                'produk_id' => 'nullable|integer',
                'kode_form' => 'required|string|max:50',
                'is_audit' => 'nullable|boolean',
            ]);

            $user = Auth::user();
            $isAudit = $request->boolean('is_audit', false);

            // For wizard 4, the main model is ProsesAging
            $query = ProsesAging::with([
                'user', 'plan', 'produk', 'bahanBaku.shift',
                'prosesTumbling.dataTumbling',
                'prosesTumbling.shift',
                'prosesTumbling.user'
            ])
            ->where('is_audit', $isAudit)
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

            // Apply filters
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai)
                  ->whereDate('tanggal', '<=', $request->tanggal_akhir);

            if ($request->shift_id) {
                // Shift is on bahanBaku relation
                $query->whereHas('bahanBaku', function ($q) use ($request) {
                    $q->where('shift_id', $request->shift_id);
                });
            }

            if ($request->produk_id) {
                $query->where('id_produk', $request->produk_id);
            }

            $data = $query->orderBy('tanggal', 'desc')->get();

            if ($data->isEmpty()) {
                return back()->with('error', 'Tidak ada data yang sesuai dengan filter yang dipilih.');
            }

            $filters = $request->only(['tanggal_mulai', 'tanggal_akhir', 'shift_id', 'produk_id', 'kode_form']);
            $kode_form = $request->kode_form;

            $pdf = Pdf::loadView('qc-sistem.wizard_proses4.export_pdf', compact('data', 'kode_form', 'filters'));
            $pdf->setPaper('a4', 'portrait');

            $filename = 'Wizard-Proses4-Bulk-'. date('Y-m-d_H-i-s') .'.pdf';
            return $pdf->stream($filename);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->id_role == 6) {
            return redirect()->route('wizard.proses4.audit');
        }
        $search = $request->input('search');

        // Query proses tumbling dengan relasi aging (left join concept via eager loading)
        $query = ProsesTumbling::with([
            'produk',
            'shift',
            'prosesAging'  // Relasi ke aging (bisa null jika belum ada)
        ]);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('produk', function($qp) use ($search) {
                    $qp->where('nama_produk', 'LIKE', "%{$search}%");
                })
                ->orWhere('kode_produksi', 'LIKE', "%{$search}%");
            });
        }

        // Group by bahan_baku_tumbling_uuid untuk menghindari duplikat (karena bisa ada multiple mesin)
        $this->applyDateRangeFilter(request(), $query);
        $tumblingData = $query->orderByRaw('DATE(tanggal) DESC')
            ->orderByRaw('TIME(jam) DESC')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        
        // Group by bahan_baku_tumbling_uuid dan ambil representative
        $grouped = $tumblingData->groupBy('bahan_baku_tumbling_uuid');
        
        // Flatten untuk pagination manual
        $flatData = collect();
        foreach ($grouped as $uuid => $group) {
            // Ambil satu record representatif per grup
            $representative = $group->first();
            $flatData->push($representative);
        }

        // Manual pagination
        $perPage = $request->get('per_page', 5);
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedItems = $flatData->slice($offset, $perPage)->values();
        
        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $flatData->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        // Group by tanggal dan shift untuk tampilan
        $groupedForDisplay = $paginatedItems->groupBy(function($item) {
            $date = $item->tanggal ? $item->tanggal->format('Y-m-d') : 'no_date';
            $shiftId = $item->shift_id ?? 'no_shift';
            return $date . '_' . $shiftId;
        });
        
        // Set grouped data ke paginator untuk view
        $paginatedData->setCollection($groupedForDisplay);

        return view('qc-sistem.wizard_proses4.index', [
            'agingData' => $paginatedData  // Tetap gunakan variable name yang sama agar view tidak perlu diubah banyak
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $continueUuid = $request->query('continue_uuid');
        $draftData = null;

        if ($continueUuid) {
            $draftData = BahanBakuTumbling::where('uuid', $continueUuid)->first();
        }

        if ($user->role === 'superadmin') {
            $produks = JenisProduk::orderBy('nama_produk', 'asc')->get();
            $shifts  = DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->orderBy('nama_produk', 'asc')->get();
            $shifts  = DataShift::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.wizard_proses4.create', compact('produks', 'shifts', 'user', 'draftData'));
    }

    // STEP 1: BAHAN BAKU TUMBLING
    public function storeBahanBaku(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'id_produk'                 => 'required',
                'shift_id'                  => 'required',
                'kode_produksi'             => 'required|string',
                'nomor_formula_non_forming' => 'required',
                'salinity'                  => 'nullable|string',
                'hasil_pencampuran'         => 'required|string',
                'jam'                       => 'required|string',
                'tanggal'                   => 'required|date',
            ]);

            $manualBahanData = $request->manual_bahan_data;
            if (!$manualBahanData && $request->filled('manual_bahan_json')) {
                $decoded = json_decode($request->manual_bahan_json, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $manualBahanData = $decoded;
                }
            }

            $record = BahanBakuTumbling::create([
                'uuid'                      => (string) Str::uuid(),
                'id_plan'                   => $user->id_plan,
                'user_id'                   => $user->id,
                'shift_id'                  => $request->shift_id,
                'id_produk'                 => $request->id_produk,
                'kode_produksi'             => $request->kode_produksi,
                'id_bahan_nonforming'       => $request->nomor_formula_non_forming,
                'salinity'                  => $request->salinity,
                'viskositas'                => $request->viskositas,
                'gramase'                   => $request->gramase ?? $request->berat_sampling,
                'hasil_pencampuran'         => $request->hasil_pencampuran,
                'keterangan'                => $request->keterangan,
                'jam'                       => $request->jam,
                'tanggal'                   => Carbon::parse($request->tanggal)->setTimeFrom(now()),
                'manual_bahan_data'         => $manualBahanData,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Bahan Baku Tumbling berhasil disimpan.',
                'uuid'    => $record->uuid,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // STEP 2: PROSES TUMBLING
    public function storeProsesTumbling(Request $request)
    {
        try {
            $user = Auth::user();
            $validated = $request->validate([
                'id_produk'                  => 'required|exists:jenis_produk,id',
                'shift_id'                   => 'required|exists:data_shift,id',
                'kode_produksi'              => 'required|string',
                'bahan_baku_uuid'            => 'required|string|exists:bahan_baku_tumbling,uuid',
                'jam'                        => 'required|string',
                'tanggal'                    => 'required|date',
                'tumbling_data'              => 'required|array',
                'tumbling_data.*.id_tumbling' => 'required|exists:data_tumbling,id',
                'tumbling_data.*.aktual_drum_on' => 'nullable|string',
                'tumbling_data.*.aktual_drum_off' => 'nullable|string',
                'tumbling_data.*.aktual_speed' => 'nullable|string',
                'tumbling_data.*.aktual_total_waktu' => 'nullable|string',
                'tumbling_data.*.aktual_vakum' => 'nullable|string',
                'tumbling_data.*.aktual_drum_on_non_vakum' => 'nullable|string',
                'tumbling_data.*.aktual_drum_off_non_vakum' => 'nullable|string',
                'tumbling_data.*.aktual_speed_non_vakum' => 'nullable|string',
                'tumbling_data.*.aktual_total_waktu_non_vakum' => 'nullable|string',
                'tumbling_data.*.aktual_tekanan_non_vakum' => 'nullable|string',
                'tumbling_data.*.waktu_mulai_tumbling' => 'nullable|string',
                'tumbling_data.*.waktu_selesai_tumbling' => 'nullable|string',
                'tumbling_data.*.waktu_mulai_tumbling_non_vakum' => 'nullable|string',
                'tumbling_data.*.waktu_selesai_tumbling_non_vakum' => 'nullable|string',
                'tumbling_data.*.suhu_hasil_tumbling' => 'nullable|string',
                'tumbling_data.*.sensori_hasil_tumbling' => 'nullable|string',
            ]);

            $formattedDate = Carbon::parse($validated['tanggal'])->setTimeFrom(now());

            $bahanBaku = BahanBakuTumbling::where('uuid', $validated['bahan_baku_uuid'])->first();

            $firstUuid = null;
            $savedCount = 0;

            foreach ($validated['tumbling_data'] as $tumblingData) {
                $record = ProsesTumbling::create([
                    'uuid'                          => (string) Str::uuid(),
                    'id_plan'                       => $user->id_plan,
                    'user_id'                       => $user->id,
                    'shift_id'                      => $validated['shift_id'],
                    'id_produk'                     => $validated['id_produk'],
                    'id_tumbling'                   => $tumblingData['id_tumbling'],
                    'kode_produksi'                 => $validated['kode_produksi'],
                    'bahan_baku_tumbling_uuid'      => $validated['bahan_baku_uuid'],
                    'bahan_baku_tumbling_id'        => $bahanBaku?->id,
                    'jam'                           => $validated['jam'],
                    'tanggal'                       => $formattedDate,

                    'aktual_drum_on'                => $tumblingData['aktual_drum_on'] ?? null,
                    'aktual_drum_off'               => $tumblingData['aktual_drum_off'] ?? null,
                    'aktual_speed'                  => $tumblingData['aktual_speed'] ?? null,
                    'aktual_total_waktu'            => $tumblingData['aktual_total_waktu'] ?? null,
                    'aktual_vakum'                  => $tumblingData['aktual_vakum'] ?? null,

                    'aktual_drum_on_non_vakum'       => $tumblingData['aktual_drum_on_non_vakum'] ?? null,
                    'aktual_drum_off_non_vakum'      => $tumblingData['aktual_drum_off_non_vakum'] ?? null,
                    'aktual_speed_non_vakum'         => $tumblingData['aktual_speed_non_vakum'] ?? null,
                    'aktual_total_waktu_non_vakum'   => $tumblingData['aktual_total_waktu_non_vakum'] ?? null,
                    'aktual_tekanan_non_vakum'       => $tumblingData['aktual_tekanan_non_vakum'] ?? null,

                    'waktu_mulai_tumbling'           => $tumblingData['waktu_mulai_tumbling'] ?? null,
                    'waktu_selesai_tumbling'         => $tumblingData['waktu_selesai_tumbling'] ?? null,
                    'waktu_mulai_tumbling_non_vakum' => $tumblingData['waktu_mulai_tumbling_non_vakum'] ?? null,
                    'waktu_selesai_tumbling_non_vakum' => $tumblingData['waktu_selesai_tumbling_non_vakum'] ?? null,

                    'suhu_hasil_tumbling'            => $tumblingData['suhu_hasil_tumbling'] ?? null,
                    'sensori_hasil_tumbling'         => $tumblingData['sensori_hasil_tumbling'] ?? null,
                    'kode_form'                      => 'FRM-QC-TUM-' . date('YmdHis'), // Isi default jika mandatory
                ]);

                $savedCount++;
                if (!$firstUuid) {
                    $firstUuid = $record->uuid;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Data Proses Tumbling berhasil disimpan ({$savedCount} item).",
                'uuid'    => $firstUuid,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    // STEP 3: PROSES AGING
    public function storeProsesAging(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'id_produk'             => 'required',
                'proses_tumbling_uuid'  => 'required|string|exists:proses_tumbling,uuid',
                'waktu_mulai_aging'     => 'nullable|array',
                'waktu_mulai_aging.*'   => 'nullable|string',
                'waktu_selesai_aging'   => 'nullable|string',
                'suhu_produk'           => 'nullable|array',
                'suhu_produk.*'         => 'nullable|string',
                'kondisi_produk'        => 'nullable|array',
                'kondisi_produk.*'      => 'nullable|string',
                'jam'                   => 'required|string',
                'tanggal'               => 'required|date',
            ]);

            $formattedDate = Carbon::parse($request->tanggal)->setTimeFrom(now());

            $prosesTumbling = ProsesTumbling::where('uuid', $request->proses_tumbling_uuid)->first();

            // Gabungkan array waktu_mulai_aging menjadi string
            $waktuArrayProcess = is_array($request->waktu_mulai_aging) 
                ? array_filter($request->waktu_mulai_aging, fn($v) => $v !== null && $v !== '') 
                : [];
            $waktuMulaiAgingStr = is_array($request->waktu_mulai_aging) 
                ? implode(', ', $waktuArrayProcess) 
                : $request->waktu_mulai_aging;

            // Untuk suhu dan kondisi
            $primarySuhu = $request->suhu_produk[0] ?? null;
            $primaryKondisi = $request->kondisi_produk[0] ?? '✓';
            
            $suhuKondisiArray = [];
            if (is_array($request->suhu_produk) && is_array($request->kondisi_produk)) {
                foreach ($request->suhu_produk as $index => $suhu) {
                    $kondisi = $request->kondisi_produk[$index] ?? '✓';
                    if ($suhu !== null && $suhu !== '') {
                        $suhuKondisiArray[] = "{$suhu}°C ({$kondisi})";
                    }
                }
            }
            $suhuKondisiStr = implode(', ', $suhuKondisiArray);

            $record = ProsesAging::create([
                'uuid'                    => (string) Str::uuid(),
                'id_plan'                 => $user->id_plan,
                'user_id'                 => $user->id,
                'id_produk'               => $request->id_produk,
                'proses_tumbling_uuid'    => $request->proses_tumbling_uuid,
                'bahan_baku_tumbling_uuid'=> $prosesTumbling ? $prosesTumbling->bahan_baku_tumbling_uuid : null,
                'waktu_mulai_aging'       => $waktuMulaiAgingStr, // Multiple waktu digabung: "08:00, 10:00, 12:00"
                'waktu_selesai_aging'     => $request->waktu_selesai_aging,
                'suhu_produk'             => $suhuKondisiStr, // Multiple suhu-kondisi: "5.5°C (✓), 6.0°C (✓)"
                'kondisi_produk'          => $primaryKondisi, // Primary kondisi untuk backward compatibility
                'jam'                     => $request->jam,
                'tanggal'                 => $formattedDate,
            ]);

            session()->flash('success', 'Wizard Proses 4 Selesai! Semua data berhasil disimpan.');

            return response()->json([
                'success'      => true,
                'message'      => 'Wizard Proses 4 Selesai! Semua data berhasil disimpan.',
                'uuid'         => $record->uuid,
                'redirect_url' => route('wizard.proses4.index'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function edit($uuid)
    {
        $user = Auth::user();
        // Coba cari sebagai ProsesAging dulu
        $aging = ProsesAging::with([
            'user', 'plan', 'produk', 'bahanBaku.shift',
            'prosesTumbling.dataTumbling',
            'prosesTumbling.shift',
            'prosesTumbling.user'
        ])->where('uuid', $uuid)
          ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
          ->first();

        if (!$aging) {
            $tumbling = ProsesTumbling::with(['bahanBakuTumbling', 'dataTumbling'])->where('uuid', $uuid)
                ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
                ->firstOrFail();
            $aging = (object)[
                'uuid' => $tumbling->uuid,
                'bahanBaku' => $tumbling->bahanBakuTumbling,
                'prosesTumbling' => $tumbling,
                'waktu_mulai_aging' => null,
                'waktu_selesai_aging' => null,
                'suhu_produk' => null,
                'kondisi_produk' => null,
                'created_at' => $tumbling->created_at,
            ];
        }

        $isAuditMode = $aging instanceof ProsesAging && (bool) $aging->is_audit;
        $queryParameters = request()->query();
        $hasAuditFlag = array_key_exists('is_audit', $queryParameters);

        if (($isAuditMode && request()->query('is_audit') !== '1') || (!$isAuditMode && $hasAuditFlag)) {
            unset($queryParameters['is_audit'], $queryParameters['uuid']);
            if ($isAuditMode) {
                $queryParameters['is_audit'] = '1';
            }

            return redirect()->route(
                'wizard.proses4.edit',
                array_merge(['uuid' => $aging->uuid], $queryParameters)
            );
        }

        // Cek batas waktu edit untuk role yang dibatasi (global config)
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 8);
            $createdAt = Carbon::parse($aging->created_at);
            $hoursElapsed = $createdAt->diffInHours(now(), true);

            if ($hoursElapsed > $timeLimitHours) {
                return redirect()
                    ->route($isAuditMode ? 'wizard.proses4.audit' : 'wizard.proses4.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        // Removed restriction that prevented editing approved data as requested by user

        $shifts = $user->role === 'superadmin'
            ? DataShift::all()
            : DataShift::where('id_plan', $user->id_plan)->get();

        return view('qc-sistem.wizard_proses4.edit', compact('aging', 'shifts'));
    }

    public function update(Request $request, $uuid)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            $user = Auth::user();

            $aging = ProsesAging::with(['bahanBaku', 'prosesTumbling'])->where('uuid', $uuid)
                ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
                ->first();
            $isDummyAging = false;
            $isAuditMode = (bool) ($aging->is_audit ?? false);

            if (!$aging) {
                $tumbling = ProsesTumbling::with(['bahanBakuTumbling'])->where('uuid', $uuid)
                    ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
                    ->firstOrFail();
                $aging = (object)[
                    'bahanBaku' => $tumbling->bahanBakuTumbling,
                    'prosesTumbling' => $tumbling,
                    'created_at' => $tumbling->created_at,
                    'bahan_baku_tumbling_uuid' => $tumbling->bahan_baku_tumbling_uuid,
                ];
                $isDummyAging = true;
            }

            // Cek batas waktu edit untuk role yang dibatasi (global config)
            $restrictedRoles = config('edit_limit.restricted_role_ids', []);
            if (in_array($user->id_role, $restrictedRoles)) {
                $timeLimitHours = config('edit_limit.time_limit_hours', 8);
                $createdAt = Carbon::parse($aging->created_at);
                $hoursElapsed = $createdAt->diffInHours(now(), true);

                if ($hoursElapsed > $timeLimitHours) {
                    \Illuminate\Support\Facades\DB::rollBack();

                    return redirect()
                        ->route($isAuditMode ? 'wizard.proses4.audit' : 'wizard.proses4.index')
                        ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
                }
            }

            // 1. Update Bahan Baku Tumbling (Jika ada)
            if ($aging->bahanBaku) {
                $bahanBaku = $aging->bahanBaku;
                $manualBahanData = $request->manual_bahan_data ?? $bahanBaku->manual_bahan_data;
                if ($request->filled('manual_bahan_json')) {
                    $decoded = json_decode($request->manual_bahan_json, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $manualBahanData = $decoded;
                    }
                }
                
                $bahanBaku->update([
                    'shift_id'          => $request->input('shift_id', $bahanBaku->shift_id),
                    'kode_produksi'     => $request->input('kode_produksi', $bahanBaku->kode_produksi),
                    'tanggal'           => $request->filled('tanggal') ? Carbon::parse($request->tanggal)->format('Y-m-d H:i:s') : $bahanBaku->tanggal,
                    'jam'               => $request->input('jam', $bahanBaku->jam),
                    'salinity'          => $request->input('salinity', $bahanBaku->salinity),
                    'viskositas'        => $request->input('viskositas', $bahanBaku->viskositas),
                    'gramase'           => $request->input('gramase', $request->input('berat_sampling', $bahanBaku->gramase)),
                    'hasil_pencampuran' => $request->input('hasil_pencampuran', $bahanBaku->hasil_pencampuran),
                    'keterangan'        => $request->input('keterangan', $bahanBaku->keterangan),
                    'manual_bahan_data' => $manualBahanData,
                ]);
            }

            // 2. Update Proses Tumbling (Multiples)
            if ($request->has('tumbling_data') && is_array($request->tumbling_data)) {
                $bahanUuid = $aging->bahan_baku_tumbling_uuid ?? ($aging->prosesTumbling->bahan_baku_tumbling_uuid ?? null);
                
                if ($bahanUuid) {
                    $allTumbling = ProsesTumbling::where('bahan_baku_tumbling_uuid', $bahanUuid)->get();
                    
                    foreach ($request->tumbling_data as $tumId => $tumData) {
                        $tumblingRecord = $allTumbling->where('id', $tumId)->first();
                        if ($tumblingRecord) {
                            $tumblingRecord->update([
                                'shift_id'                      => $request->input('shift_id', $tumblingRecord->shift_id),
                                'kode_produksi'                 => $request->input('kode_produksi', $tumblingRecord->kode_produksi),
                                'tanggal'                       => $request->filled('tanggal') ? Carbon::parse($request->tanggal)->format('Y-m-d H:i:s') : $tumblingRecord->tanggal,
                                'jam'                           => $request->input('jam', $tumblingRecord->jam),
                                'aktual_drum_on'                => $tumData['aktual_drum_on'] ?? $tumblingRecord->aktual_drum_on,
                                'aktual_drum_off'               => $tumData['aktual_drum_off'] ?? $tumblingRecord->aktual_drum_off,
                                'aktual_speed'                  => $tumData['aktual_speed'] ?? $tumblingRecord->aktual_speed,
                                'aktual_total_waktu'            => $tumData['aktual_total_waktu'] ?? $tumblingRecord->aktual_total_waktu,
                                'aktual_vakum'                  => $tumData['aktual_vakum'] ?? $tumblingRecord->aktual_vakum,

                                'aktual_drum_on_non_vakum'       => $tumData['aktual_drum_on_non_vakum'] ?? $tumblingRecord->aktual_drum_on_non_vakum,
                                'aktual_drum_off_non_vakum'      => $tumData['aktual_drum_off_non_vakum'] ?? $tumblingRecord->aktual_drum_off_non_vakum,
                                'aktual_speed_non_vakum'         => $tumData['aktual_speed_non_vakum'] ?? $tumblingRecord->aktual_speed_non_vakum,
                                'aktual_total_waktu_non_vakum'   => $tumData['aktual_total_waktu_non_vakum'] ?? $tumblingRecord->aktual_total_waktu_non_vakum,
                                'aktual_tekanan_non_vakum'       => $tumData['aktual_tekanan_non_vakum'] ?? $tumblingRecord->aktual_tekanan_non_vakum,

                                'waktu_mulai_tumbling'           => $tumData['waktu_mulai_tumbling'] ?? $tumblingRecord->waktu_mulai_tumbling,
                                'waktu_selesai_tumbling'         => $tumData['waktu_selesai_tumbling'] ?? $tumblingRecord->waktu_selesai_tumbling,
                                'waktu_mulai_tumbling_non_vakum' => $tumData['waktu_mulai_tumbling_non_vakum'] ?? $tumblingRecord->waktu_mulai_tumbling_non_vakum,
                                'waktu_selesai_tumbling_non_vakum' => $tumData['waktu_selesai_tumbling_non_vakum'] ?? $tumblingRecord->waktu_selesai_tumbling_non_vakum,

                                'suhu_hasil_tumbling'            => $tumData['suhu_hasil_tumbling'] ?? $tumblingRecord->suhu_hasil_tumbling,
                                'sensori_hasil_tumbling'         => $tumData['sensori_hasil_tumbling'] ?? $tumblingRecord->sensori_hasil_tumbling,
                            ]);
                        }
                    }
                }
            }

            if (!$isDummyAging) {
                // 3. Update Proses Aging
                $waktuMulaiAgingStr = $aging->waktu_mulai_aging;
                if ($request->has('waktu_mulai_aging')) {
                    $waktuArrayFiltered = is_array($request->waktu_mulai_aging)
                        ? array_filter($request->waktu_mulai_aging, fn($val) => $val !== null && $val !== '')
                        : [];
                    $waktuMulaiAgingStr = is_array($request->waktu_mulai_aging)
                        ? implode(', ', $waktuArrayFiltered)
                        : $request->waktu_mulai_aging;
                }

                $suhuProdukStr = $aging->suhu_produk;
                $primaryKondisi = $aging->kondisi_produk;

                if ($request->has('suhu_produk') && $request->has('kondisi_produk')) {
                    $suhuArray = $request->suhu_produk;
                    $kondisiArray = $request->kondisi_produk;

                    $primaryKondisi = $kondisiArray[0] ?? '✓';

                    $suhuKondisiArray = [];
                    if (is_array($suhuArray) && is_array($kondisiArray)) {
                        foreach ($suhuArray as $index => $suhu) {
                            $kondisi = $kondisiArray[$index] ?? '✓';
                            if ($suhu !== null && $suhu !== '') {
                                $suhuKondisiArray[] = "{$suhu}°C ({$kondisi})";
                            }
                        }
                    }
                    $suhuProdukStr = implode(', ', $suhuKondisiArray);
                }

                $aging->update([
                    'tanggal'               => $request->filled('tanggal') ? Carbon::parse($request->tanggal)->format('Y-m-d H:i:s') : $aging->tanggal,
                    'jam'                   => $request->input('jam', $aging->jam),
                    'waktu_mulai_aging'     => $waktuMulaiAgingStr,
                    'waktu_selesai_aging'   => $request->input('waktu_selesai_aging', $aging->waktu_selesai_aging),
                    'suhu_produk'           => $suhuProdukStr,
                    'kondisi_produk'        => $primaryKondisi,
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()
                ->route($isAuditMode ? 'wizard.proses4.audit' : 'wizard.proses4.index')
                ->with('success', $isAuditMode ? 'Data audit Wizard Proses 4 berhasil diperbarui.' : 'Data Wizard Proses 4 berhasil diperbarui.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function show($uuid)
    {
        $user = Auth::user();
        // Coba cari sebagai ProsesAging dulu
        $aging = ProsesAging::with([
            'user', 'plan', 'produk', 'bahanBaku.shift',
            'prosesTumbling.dataTumbling',
            'prosesTumbling.shift',
            'prosesTumbling.user'
        ])->where('uuid', $uuid)
          ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
          ->first();

        if ($aging) {
            if ($redirect = $this->redirectToCanonicalAuditUrl('wizard.proses4.show', $aging)) {
                return $redirect;
            }

            return view('qc-sistem.wizard_proses4.show', compact('aging'));
        }

        // Jika tidak ditemukan sebagai ProsesAging, coba sebagai ProsesTumbling
        $tumbling = ProsesTumbling::with([
            'user', 'plan', 'produk', 'shift', 'dataTumbling',
            'bahanBakuTumbling.shift'
        ])->where('uuid', $uuid)
          ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
          ->firstOrFail();

        // Buat object aging dummy agar view tetap bisa menampilkan data
        $aging = (object) [
            'uuid' => $tumbling->uuid, // Gunakan UUID tumbling sebagai fallback
            'id_plan' => $tumbling->id_plan,
            'user_id' => $tumbling->user_id,
            'user' => $tumbling->user,
            'plan' => $tumbling->plan,
            'id_produk' => $tumbling->id_produk,
            'produk' => $tumbling->produk,
            'proses_tumbling_uuid' => $tumbling->uuid,
            'prosesTumbling' => $tumbling,
            'bahan_baku_tumbling_uuid' => $tumbling->bahan_baku_tumbling_uuid,
            'bahanBaku' => $tumbling->bahanBakuTumbling,
            'waktu_mulai_aging' => null,
            'waktu_selesai_aging' => null,
            'suhu_produk' => null,
            'kondisi_produk' => null,
            'jam' => $tumbling->jam,
            'tanggal' => $tumbling->tanggal,
            'created_at' => $tumbling->created_at,
            'approved_by_qc' => null,
            'approved_by_produksi' => null,
            'approved_by_spv' => null,
            'is_dummy_aging' => true, // Flag untuk identifikasi
        ];

        if ($redirect = $this->redirectToCanonicalAuditUrl('wizard.proses4.show', $aging)) {
            return $redirect;
        }

        return view('qc-sistem.wizard_proses4.show', compact('aging'));
    }

    public function exportPdf($uuid)
    {
        $user = Auth::user();
        $aging = ProsesAging::with([
            'user', 'plan', 'produk', 'bahanBaku.shift',
            'prosesTumbling.dataTumbling',
            'prosesTumbling.shift',
            'prosesTumbling.user',
            'qcApprover', 'produksiApprover', 'spvApprover'
        ])->where('uuid', $uuid)
          ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
          ->first();

        if (!$aging) {
            $tumbling = ProsesTumbling::with([
                'user', 'plan', 'produk', 'shift', 'dataTumbling',
                'bahanBakuTumbling.shift'
            ])->where('uuid', $uuid)
              ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
              ->firstOrFail();

            $aging = (object) [
                'uuid' => $tumbling->uuid,
                'id_plan' => $tumbling->id_plan,
                'user_id' => $tumbling->user_id,
                'user' => $tumbling->user,
                'plan' => $tumbling->plan,
                'id_produk' => $tumbling->id_produk,
                'produk' => $tumbling->produk,
                'proses_tumbling_uuid' => $tumbling->uuid,
                'prosesTumbling' => $tumbling,
                'bahan_baku_tumbling_uuid' => $tumbling->bahan_baku_tumbling_uuid,
                'bahanBaku' => $tumbling->bahanBakuTumbling,
                'waktu_mulai_aging' => null,
                'waktu_selesai_aging' => null,
                'suhu_produk' => null,
                'kondisi_produk' => null,
                'jam' => $tumbling->jam,
                'tanggal' => $tumbling->tanggal,
                'created_at' => $tumbling->created_at,
                'approved_by_qc' => $tumbling->approved_by_qc ?? null,
                'approved_by_produksi' => $tumbling->approved_by_produksi ?? null,
                'approved_by_spv' => $tumbling->approved_by_spv ?? null,
                'qc_approved_by' => $tumbling->qc_approved_by ?? null,
                'produksi_approved_by' => $tumbling->produksi_approved_by ?? null,
                'spv_approved_by' => $tumbling->spv_approved_by ?? null,
                'qc_approved_at' => $tumbling->qc_approved_at ?? null,
                'produksi_approved_at' => $tumbling->produksi_approved_at ?? null,
                'spv_approved_at' => $tumbling->spv_approved_at ?? null,
                'is_dummy_aging' => true,
            ];
        }

        $pdf = Pdf::loadView('qc-sistem.wizard_proses4.export_pdf', compact('aging'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Wizard-Proses4-' . ($aging->tanggal ? \Carbon\Carbon::parse($aging->tanggal)->format('d-m-Y') : date('d-m-Y')) . '.pdf');
    }

    public function destroy($uuid)
    {
        $user = Auth::user();
        try {
            $aging = ProsesAging::where('uuid', $uuid)
                ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
                ->firstOrFail();
            
            // Delete related tumbling & bahan baku records if they exist
            if($aging->prosesTumbling) {
                if($aging->prosesTumbling->bahanBakuTumbling) {
                    $aging->prosesTumbling->bahanBakuTumbling->delete();
                }
                $aging->prosesTumbling->delete();
            }
            
            $aging->delete();

            return redirect()->route('wizard.proses4.index')->with('success', 'Data Riwayat Wizard Proses 4 berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('wizard.proses4.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function approve(Request $request, $uuid)
    {
        try {
            $user = Auth::user();
            $type = $request->input('type');
            
            // Validate approval type
            if (!in_array($type, ['qc', 'produksi', 'spv'])) {
                return response()->json(['success' => false, 'message' => 'Tipe approval tidak valid'], 400);
            }
            
            // Find the record (bisa berupa UUID dari ProsesAging atau ProsesTumbling)
            $aging = ProsesAging::where('uuid', $uuid)->first();
            $tumbling = null;

            if ($aging) {
                // If it's aging UUID, find tumbling
                $tumbling = $aging->prosesTumbling;
            } else {
                // If it's tumbling UUID
                $tumbling = ProsesTumbling::where('uuid', $uuid)->firstOrFail();
                $aging = $tumbling->prosesAging; // might be null
            }
            
            // Evaluator object (mengutamakan aging jika ada, jika tidak, pakai tumbling)
            $evalObj = $aging ? $aging : $tumbling;

            // Check role permissions based on type
            $userRole = $user->id_role;
            
            if ($type === 'qc' && !in_array($userRole, [1, 3, 5])) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk approval QC'], 403);
            }
            
            if ($type === 'produksi' && !in_array($userRole, [1, 2, 5])) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk approval Produksi'], 403);
            }
            
            if ($type === 'spv' && !in_array($userRole, [1, 4, 5])) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk approval SPV'], 403);
            }
            
            // Check sequential approval requirements
            if ($type === 'produksi' && !$evalObj->approved_by_qc) {
                return response()->json(['success' => false, 'message' => 'Menunggu approval QC terlebih dahulu'], 403);
            }
            
            if ($type === 'spv' && !$evalObj->approved_by_produksi) {
                return response()->json(['success' => false, 'message' => 'Menunggu approval Produksi terlebih dahulu'], 403);
            }
            
            // Function to update approval fields
            $updateApproval = function($model, $type, $user) {
                if (!$model) return false;
                switch ($type) {
                    case 'qc':
                        if ($model->approved_by_qc) return false;
                        $model->approved_by_qc = true;
                        $model->qc_approved_by = $user->id;
                        $model->qc_approved_at = now();
                        break;
                    case 'produksi':
                        if ($model->approved_by_produksi) return false;
                        $model->approved_by_produksi = true;
                        $model->produksi_approved_by = $user->id;
                        $model->produksi_approved_at = now();
                        break;
                    case 'spv':
                        if ($model->approved_by_spv) return false;
                        $model->approved_by_spv = true;
                        $model->spv_approved_by = $user->id;
                        $model->spv_approved_at = now();
                        break;
                }
                $model->save();
                return true;
            };

            $updatedAging = $updateApproval($aging, $type, $user);
            $updatedTumbling = $updateApproval($tumbling, $type, $user);

            if (!$updatedAging && !$updatedTumbling) {
                return response()->json(['success' => false, 'message' => 'Data sudah di-approve oleh ' . ucfirst($type)], 400);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil di-approve oleh ' . ucfirst($type)
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function bulkApprove(Request $request)
    {
        try {
            $user = Auth::user();
            $type = $request->input('type');
            $tanggal = $request->input('tanggal');
            $shift_id = $request->input('shift_id');

            if (!in_array($type, ['qc', 'produksi', 'spv'])) {
                return response()->json(['success' => false, 'message' => 'Tipe approval tidak valid'], 400);
            }

            $query = ProsesTumbling::with('prosesAging')->whereDate('tanggal', $tanggal)->where('shift_id', $shift_id);

            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }

            $items = $query->get();

            if ($items->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data untuk disetujui'], 404);
            }

            // Get evaluator logic for sequential check
            $getEvalStatus = function($item, $field) {
                return $item->prosesAging ? $item->prosesAging->{$field} : $item->{$field};
            };

            // Sequential check - cek dari sumber yang tepat (aging jika ada, tumbling jika tidak)
            $getApprovalStatus = function($item, $field) {
                if ($item->prosesAging) {
                    return $item->prosesAging->{$field};
                }
                return $item->{$field};
            };

            if ($type === 'produksi' && $items->contains(fn($v) => !$getApprovalStatus($v, 'approved_by_qc'))) {
                return response()->json(['success' => false, 'message' => 'Beberapa data belum disetujui oleh QC'], 403);
            }
            if ($type === 'spv' && $items->contains(fn($v) => !$getApprovalStatus($v, 'approved_by_produksi'))) {
                return response()->json(['success' => false, 'message' => 'Beberapa data belum disetujui oleh Produksi'], 403);
            }

            $updateApproval = function($model, $type, $user) {
                if (!$model) return;
                if ($type === 'qc' && !$model->approved_by_qc) {
                    $model->approved_by_qc = true;
                    $model->qc_approved_by = $user->id;
                    $model->qc_approved_at = now();
                } elseif ($type === 'produksi' && !$model->approved_by_produksi) {
                    $model->approved_by_produksi = true;
                    $model->produksi_approved_by = $user->id;
                    $model->produksi_approved_at = now();
                } elseif ($type === 'spv' && !$model->approved_by_spv) {
                    $model->approved_by_spv = true;
                    $model->spv_approved_by = $user->id;
                    $model->spv_approved_at = now();
                }
                $model->save();
            };

            foreach ($items as $item) {
                $updateApproval($item, $type, $user);
                $updateApproval($item->prosesAging, $type, $user);
            }

            return response()->json(['success' => true, 'message' => 'Bulk approval berhasil (' . strtoupper($type) . ')']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
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

            // Find ProsesTumbling records - ONLY unapproved ones
            $query = ProsesTumbling::with('prosesAging')
                ->whereIn('uuid', $uuids)
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

            // Update ProsesTumbling records
            ProsesTumbling::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // Also update ProsesAging records if they exist
            $tumblingUuids = $itemsToUpdate->pluck('uuid')->toArray();
            ProsesAging::whereIn('proses_tumbling_uuid', $tumblingUuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();
            
            // Update ProsesTumbling audit copies
            ProsesTumbling::where('is_audit', true)
                ->whereIn('source_uuid', $updatedSourceUuids)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // Update ProsesAging audit copies (where proses_tumbling_uuid links to audit tumbling)
            $auditTumblingRecords = ProsesTumbling::where('is_audit', true)
                ->whereIn('source_uuid', $updatedSourceUuids)
                ->pluck('uuid')
                ->toArray();

            if (!empty($auditTumblingRecords)) {
                ProsesAging::where('is_audit', true)
                    ->whereIn('proses_tumbling_uuid', $auditTumblingRecords)
                    ->when($user->role !== 'superadmin', function($q) use ($user) {
                        $q->where('id_plan', $user->id_plan);
                    })
                    ->update($updateData);
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

    // ===================== AUDIT METHODS =====================

    protected function auditRelations(): array
    {
        return [
            'prosesTumbling',
            'prosesTumbling.produk',
            'bahanBaku',
            'bahanBaku.shift',
        ];
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.wizard_proses4';
    }

    public function copyToAudit($uuid)
    {
        $user = Auth::user();
        
        // Auditor (id_role 6) bisa akses audit, atau user dengan permission
        if ($user->id_role != 6 && !$user->hasPermissionTo('create-wizard-proses4')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk membuat data audit.');
        }

        // Coba cari sebagai ProsesAging dulu
        $item = ProsesAging::where('uuid', $uuid)->first();

        // Jika item yang diklik sudah merupakan record audit
        if ($item && $item->is_audit) {
            return redirect()->route('wizard.proses4.edit', $item->uuid);
        }

        // Jika data operasional ini sudah memiliki versi audit sebelumnya
        if ($item && $item->auditVersion) {
            return redirect()->route('wizard.proses4.edit', $item->auditVersion->uuid);
        }

        // Jika tidak ditemukan sebagai Aging, coba cari sebagai ProsesTumbling
        if (!$item) {
            $tumbling = ProsesTumbling::where('uuid', $uuid)->firstOrFail();
            $item = $tumbling->prosesAging;
            
            if (!$item) {
                // Tidak ada aging, copy tumbling + bahan baku saja, buat aging kosong
                $bahanBaku = BahanBakuTumbling::where('uuid', $tumbling->bahan_baku_tumbling_uuid)->first();

                // Clone BahanBakuTumbling
                $newBahanUuid = null;
                if ($bahanBaku) {
                    $c = $bahanBaku->replicate();
                    $c->uuid = (string) Str::uuid();
                    $c->save();
                    $newBahanUuid = $c->uuid;
                }

                // Clone ProsesTumbling records
                $tumblingRecords = ProsesTumbling::where('bahan_baku_tumbling_uuid', $tumbling->bahan_baku_tumbling_uuid)->get();
                $newTumblingUuid = null;
                foreach ($tumblingRecords as $tr) {
                    $tc = $tr->replicate();
                    $tc->uuid = (string) Str::uuid();
                    if ($newBahanUuid) {
                        $tc->bahan_baku_tumbling_uuid = $newBahanUuid;
                    }
                    $tc->save();
                    if (!$newTumblingUuid) {
                        $newTumblingUuid = $tc->uuid;
                    }
                }

                // Buat minimal ProsesAging audit record
                $newAgingUuid = (string) Str::uuid();
                $newAging = ProsesAging::create([
                    'uuid'                      => $newAgingUuid,
                    'is_audit'                  => true,
                    'source_uuid'               => $uuid,
                    'id_plan'                   => $tumbling->id_plan,
                    'user_id'                   => Auth::user()->id,
                    'id_produk'                 => $tumbling->id_produk,
                    'proses_tumbling_uuid'      => $newTumblingUuid,
                    'bahan_baku_tumbling_uuid'  => $newBahanUuid,
                    'tanggal'                   => $tumbling->tanggal,
                    'jam'                       => $tumbling->jam,
                    'waktu_mulai_aging'         => '-',
                    'waktu_selesai_aging'       => '-',
                    'suhu_produk'               => '-',
                    'kondisi_produk'            => '✓',
                ]);

                return redirect()->route('wizard.proses4.edit', $newAgingUuid)
                    ->with('info', 'Salinan data audit berhasil dibuat (Aging kosong). Silakan lakukan penyesuaian.');
            }
        }

        // Jika sudah ada audit version sebelumnya
        if ($item->auditVersion) {
            return redirect()->route('wizard.proses4.edit', $item->auditVersion->uuid);
        }

        // Jika belum ada, buat copy baru
        $clone = $item->copyToAudit();

        return redirect()->route('wizard.proses4.edit', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    public function auditIndex(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $auditedSourceUuids = ProsesAging::where('is_audit', true)->whereNotNull('source_uuid')->pluck('source_uuid')->toArray();

$query = ProsesAging::with($this->auditRelations())
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
                $q->whereHas('prosesTumbling.produk', function ($qp) use ($search) {
                    $qp->where('nama_produk', 'LIKE', "%{$search}%");
                })->orWhereHas('bahanBaku', function ($qb) use ($search) {
                    $qb->where('kode_produksi', 'LIKE', "%{$search}%");
                });
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        $groupedData = $records->getCollection()->groupBy(function ($item) {
            $dateStr = $item->tanggal
                ? (is_string($item->tanggal)
                    ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d')
                    : $item->tanggal->format('Y-m-d'))
                : 'no_date';
            $shiftId = $item->bahanBaku->shift_id ?? 'no_shift';
            return $dateStr . '_' . $shiftId;
        });

        $records->setCollection($groupedData);

        return view("{$this->auditViewPrefix()}.index_audit", compact('records', 'search', 'perPage'));
    }

    public function auditSearchAjax(Request $request)
    {
        try {
            $user = Auth::user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 10);
            $page = (int) $request->get('page', 1);

            $query = ProsesAging::with($this->auditRelations())
                ->where('is_audit', true)
                ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan));

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('prosesTumbling.produk', function ($qp) use ($search) {
                        $qp->where('nama_produk', 'like', "%{$search}%");
                    })->orWhereHas('bahanBaku', function ($qb) use ($search) {
                        $qb->where('kode_produksi', 'like', "%{$search}%");
                    });
                });
            }

            $records = $query->orderBy('tanggal', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $total = $records->total();
            $groupedData = $records->getCollection()->groupBy(fn($item) =>
                ($item->tanggal
                    ? (is_string($item->tanggal)
                        ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d')
                        : $item->tanggal->format('Y-m-d'))
                    : 'no_date') . '_' . ($item->bahanBaku->shift_id ?? 'no_shift')
            );
            $records->setCollection($groupedData);

            return response()->json([
                'html' => view("{$this->auditViewPrefix()}._table", compact('records'))->render(),
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => (int) ceil($total / $perPage),
                    'per_page' => $perPage,
                    'total' => $total,
                    'from' => $records->firstItem(),
                    'to' => $records->lastItem(),
                    'has_pages' => $total > $perPage,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }
}
