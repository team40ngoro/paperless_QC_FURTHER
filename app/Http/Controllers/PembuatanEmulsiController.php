<?php

namespace App\Http\Controllers;

use App\Models\PembuatanEmulsi;
use App\Models\PembuatanEmulsiLog;
use App\Models\NomorEmulsi;
use App\Models\TotalPemakaianEmulsi;
use App\Models\JenisProduk;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\SuhuEmulsi;
use App\Models\JenisEmulsi;
use App\Models\BahanEmulsi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PDF;
use App\Http\Controllers\Traits\HasAuditController;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Services\BulkExport\PersiapanBahanEmulsiBulkExportService;

class PembuatanEmulsiController extends Controller
{
    use HasAuditController, HasNoDataHtmlResponse;

    protected function auditModel(): string
    {
        return PembuatanEmulsi::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'persiapan-bahan-emulsi';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.persiapan_bahan_emulsi';
    }

    protected function auditRelations(): array
    {
        return ['nomor_emulsi', 'total_pemakaian', 'produk', 'plan', 'user', 'bahan_emulsi', 'suhuEmulsi', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'originalVersion'];
    }

    protected function auditSearchColumns(): array
    {
        return ['kode_produksi_emulsi'];
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

    protected function auditPlanColumn(): string
    {
        return 'id_plan';
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->id_role == 6) {
            return redirect()->route('persiapan-bahan-emulsi.audit');
        }
        $produks = JenisProduk::all();
        $plans = Plan::all();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        if ($user->role === 'superadmin') {
            $query = PembuatanEmulsi::with(['nomor_emulsi', 'total_pemakaian', 'produk', 'plan', 'user', 'bahan_emulsi', 'suhuEmulsi','shift', 'qcApprover', 'produksiApprover', 'spvApprover'])
                ->operasional()
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc');
        } else {
            $query = PembuatanEmulsi::with(['nomor_emulsi', 'total_pemakaian', 'produk', 'plan', 'user', 'bahan_emulsi', 'suhuEmulsi', 'qcApprover', 'produksiApprover', 'spvApprover'])
                ->operasional()
                ->where('id_plan', $user->id_plan)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc');
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_produksi_emulsi', 'like', "%{$search}%")
                  ->orWhereHas('produk', function($subq) use ($search) {
                      $subq->where('nama_produk', 'like', "%{$search}%");
                  });
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->paginate($perPage);

        return view('qc-sistem.persiapan_bahan_emulsi.index', compact('records', 'produks', 'plans', 'search', 'perPage'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->role == 'superadmin') {
            $jenis_produk = JenisProduk::all();
            $shifts = DataShift::all();
        } else {
            $jenis_produk = JenisProduk::where('id_plan', $user->id_plan)->get();
            // $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        return view('qc-sistem.persiapan_bahan_emulsi.create', compact('jenis_produk', 'shifts'));
    }

    public function store(Request $request)
    {
        // Debug: Log request data
        \Log::info('PembuatanEmulsi Store Request:', $request->all());
        // Validasi input untuk multiple proses
        $request->validate([
            'kode_produksi_emulsi' => 'required|string|max:255',
            'nomor_emulsi_id'      => 'required|integer',
            'nama_emulsi_id'       => 'required|integer',
            'hasil_emulsi'         => 'nullable|string',
            'total_pemakaian_id'   => 'required|integer',
            'id_produk'            => 'required|integer',
            'shift_id'             => 'required|exists:data_shift,id',
            'tanggal'              => 'required|date',
            'jam'                  => 'required|date_format:H:i',
            'kondisi'              => 'nullable|string',
            
            // Menjadi:
            'bahan_emulsi_id'      => 'nullable|array',
            'bahan_emulsi_id.*'    => 'nullable|array',
            'bahan_emulsi_id.*.*'  => 'nullable|integer',

            'suhu'                 => 'nullable|array',
            'suhu.*'               => 'nullable|array', 
            'suhu.*.*'             => 'nullable|string|in:✔,✘',

            'proses_ke'            => 'nullable|array',
            'proses_ke.*'          => 'nullable|array',
            'proses_ke.*.*'        => 'nullable|integer',
            
            'kode_produksi_bahan'  => 'nullable|array',
            'kode_produksi_bahan.*' => 'nullable|array',
            'kode_produksi_bahan.*.*' => 'nullable|string',

            'berat_rm'             => 'nullable|array',
            'berat_rm.*'           => 'nullable|array',
            'berat_rm.*.*'         => 'nullable|numeric',

            'kondisi_proses'          => 'nullable|array',
            'kondisi_proses.*'        => 'nullable|string',

            'hasil_emulsi_proses'     => 'nullable|array',
            'hasil_emulsi_proses.*'   => 'nullable|string|in:✔,✘',
            
        ], [
            // Custom error messages
            'bahan_emulsi_id.required' => 'Bahan emulsi harus dipilih',
            'suhu.required' => 'Kondisi harus dipilih untuk setiap bahan',
            'suhu.*.*.in' => 'Kondisi harus berupa ✔ atau ✘',
            'proses_ke.required' => 'Proses ke harus diisi',
            'kode_produksi_bahan.required' => 'Kode produksi bahan harus diisi',
        ]);

        try {
            \DB::beginTransaction();

            $id_plan = auth()->user()->id_plan;
            
            // Buat record utama
            $emulsi = PembuatanEmulsi::create([
                'uuid'                 => \Illuminate\Support\Str::uuid(),
                'kode_produksi_emulsi' => $request->kode_produksi_emulsi,
                'nomor_emulsi_id'      => $request->nomor_emulsi_id,
                'nama_emulsi_id'       => $request->nama_emulsi_id,
                'hasil_emulsi'         => $request->hasil_emulsi,
                'total_pemakaian_id'   => $request->total_pemakaian_id,
                'kondisi'              => $request->kondisi ?? '',
                'id_produk'            => $request->id_produk,
                'berat_sampling'       => $request->berat_sampling,
                'id_plan'              => $id_plan,
                'user_id'              => auth()->id(),
                'shift_id'             => $request->shift_id,
                'tanggal'              => Carbon::parse($request->tanggal)->setTimeFrom(now()),
                'jam'                  => $request->jam,
            ]);

            $createdCount = 0;

            // === DEBUG LOGGING START ===
            \Log::info('=== DEBUGGING SUHU EMULSI DATA ===');
            \Log::info('Full request data', $request->all());
            \Log::info('Debug flags', [
                'has_bahan_emulsi_id' => $request->has('bahan_emulsi_id'),
                'has_suhu' => $request->has('suhu'),
                'has_proses_ke' => $request->has('proses_ke'),
                'emulsi_id' => $emulsi->id,
                'bahan_emulsi_data' => $request->bahan_emulsi_id,
                'suhu_data' => $request->suhu,
                'proses_ke_data' => $request->proses_ke
            ]);
            // === DEBUG LOGGING END ===

            // Loop untuk setiap proses
            if ($request->has('bahan_emulsi_id') && is_array($request->bahan_emulsi_id)) {
                \Log::info('Bahan emulsi data found, starting loop');
                
                foreach ($request->bahan_emulsi_id as $prosesIndex => $bahanIds) {
                    \Log::info("Processing proses index: {$prosesIndex}");
                    \Log::info("Bahan IDs for this proses", $bahanIds);
                    
                    if (is_array($bahanIds)) {
                        foreach ($bahanIds as $bahanIndex => $bahanId) {
                            \Log::info("Processing bahan", [
                                'proses' => $prosesIndex,
                                'index' => $bahanIndex,
                                'id' => $bahanId
                            ]);
                            
                            // Check if suhu and proses_ke data exists
                            $suhuExists = isset($request->suhu[$prosesIndex][$bahanIndex]);
                            $prosesKeExists = isset($request->proses_ke[$prosesIndex][$bahanIndex]);
                            
                            \Log::info("Data check", [
                                'suhu_exists' => $suhuExists,
                                'proses_ke_exists' => $prosesKeExists,
                                'suhu_value' => $request->suhu[$prosesIndex][$bahanIndex] ?? 'NOT_FOUND',
                                'proses_ke_value' => $request->proses_ke[$prosesIndex][$bahanIndex] ?? 'NOT_FOUND'
                            ]);
                            
                            if ($request->has('suhu') && is_array($request->suhu) && 
                                $request->has('proses_ke') && is_array($request->proses_ke) &&
                                $suhuExists && $prosesKeExists) {
                                
                                $dataToInsert = [
                                    'bahan_emulsi_id'     => $bahanId,
                                    'pembuatan_emulsi_id' => $emulsi->id,
                                    'suhu'                => $request->suhu[$prosesIndex][$bahanIndex],
                                    'proses_ke'           => $request->proses_ke[$prosesIndex][$bahanIndex],
                                    'kode_produksi_bahan' => $request->kode_produksi_bahan[$prosesIndex][$bahanIndex],
                                    'berat_bahan'         => $request->berat_rm[$prosesIndex][$bahanIndex] ?? null,
                                ];
                                
                                \Log::info("Attempting to create SuhuEmulsi", $dataToInsert);
                                
                                try {
                                    $suhuEmulsi = SuhuEmulsi::create($dataToInsert);
                                    \Log::info("SuhuEmulsi created successfully", ['id' => $suhuEmulsi->id]);
                                    $createdCount++;
                                } catch (\Exception $e) {
                                    \Log::error("Failed to create SuhuEmulsi", [
                                        'error' => $e->getMessage(),
                                        'data' => $dataToInsert
                                    ]);
                                }
                            } else {
                                \Log::warning("Skipping bahan due to missing data", [
                                    'bahan_id' => $bahanId,
                                    'has_suhu' => $request->has('suhu'),
                                    'suhu_is_array' => is_array($request->suhu ?? null),
                                    'has_proses_ke' => $request->has('proses_ke'),
                                    'proses_ke_is_array' => is_array($request->proses_ke ?? null),
                                    'suhu_exists' => $suhuExists,
                                    'proses_ke_exists' => $prosesKeExists
                                ]);
                            }
                        }
                    } else {
                        \Log::warning("BahanIds is not array", ['proses' => $prosesIndex, 'data' => $bahanIds]);
                    }
                }
                
                \Log::info("Total SuhuEmulsi records created: {$createdCount}");
            } else {
                \Log::warning('No bahan_emulsi_id data found or not array');
                \Log::info('Debug info', [
                    'has_bahan_emulsi_id' => $request->has('bahan_emulsi_id'),
                    'is_array' => is_array($request->bahan_emulsi_id ?? null)
                ]);
            }
            // Simpan Suhu dan Hasil Emulsi per proses
            if ($request->has('kondisi_proses') && is_array($request->kondisi_proses)) {
                $kondisiProsesJson = [];
                $hasilProsesJson = [];
                
                foreach ($request->kondisi_proses as $prosesIndex => $suhuProses) {
                    $kondisiProsesJson[$prosesIndex] = $suhuProses;
                    $hasilProsesJson[$prosesIndex] = $request->hasil_emulsi_proses[$prosesIndex] ?? null;
                    
                    \Log::info("Proses ke-" . ($prosesIndex + 1), [
                        'suhu' => $suhuProses,
                        'hasil' => $request->hasil_emulsi_proses[$prosesIndex] ?? null
                    ]);
                }
                
                // Update record emulsi dengan data per proses
                $emulsi->update([
                    'kondisi' => json_encode($kondisiProsesJson),  // Simpan sebagai JSON
                    'hasil_emulsi' => json_encode($hasilProsesJson)  // Simpan sebagai JSON
                ]);
                
                \Log::info('Kondisi dan Hasil per proses berhasil disimpan', [
                    'kondisi' => $kondisiProsesJson,
                    'hasil' => $hasilProsesJson
                ]);
            }
            \Log::info('=== END DEBUGGING SUHU EMULSI ===');
            \DB::commit();

            $totalProses = $request->has('bahan_emulsi_id') && is_array($request->bahan_emulsi_id) 
            ? count($request->bahan_emulsi_id) 
            : 0;
            $message = $totalProses > 0 
            ? "Data berhasil disimpan! Total {$totalProses} proses dengan {$createdCount} detail bahan."
            : "Data berhasil disimpan! (Tanpa detail bahan emulsi)";
            return redirect()->route('persiapan-bahan-emulsi.index')->with('success', $message);

        } catch (\Exception $e) {
            \DB::rollback();
            
            // Debug: Log the actual error
            \Log::error('PembuatanEmulsi Store Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);     
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error: ' . $e->getMessage() . ' di file: ' . $e->getFile() . ' baris: ' . $e->getLine()]);        }
    }

    // AJAX: Get Total Pemakaian by Produk
    public function getTotalPemakaian(Request $request)
    {
        $data = TotalPemakaianEmulsi::where('id_produk', $request->id_produk)->get(['id', 'total_pemakaian']);
        return response()->json($data);
    }

    // AJAX: Get Nomor Emulsi by Produk, Total Pemakaian
    public function getNomorEmulsi(Request $request)
    {
        $data = NomorEmulsi::where('id_produk', $request->id_produk)
            ->where('total_pemakaian_id', $request->total_pemakaian_id)
            ->get(['id', 'nomor_emulsi']);
        return response()->json($data);
    }

    public function inputPersiapanBahan()
    {
        $user = auth()->user();
        if ($user->role === 'superadmin') {
            $jenis_produk = JenisProduk::all();
        } else {
            $jenis_produk = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        $shifts = DataShift::all();
        // Data lain jika perlu
        return view('qc-sistem.input_persiapan_bahan', compact('jenis_produk'));
    }

    public function getTotalPemakaianByProduk($id_produk)
    {
        $user = auth()->user();
        $query = TotalPemakaianEmulsi::where('id_produk', $id_produk);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $data = $query->get(['id', 'total_pemakaian']);
        return response()->json($data);
    }

    public function getEmulsiByProduk($id_produk)
    {
        $user = auth()->user();
        $query = JenisEmulsi::where('id_produk', $id_produk);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $data = $query->get(['id', 'nama_emulsi']);
        return response()->json($data);
    }

    public function getTotalPemakaianByEmulsiProduk($id_produk, $nama_emulsi_id)
    {
        $user = auth()->user();
        $query = TotalPemakaianEmulsi::where('id_produk', $id_produk)
            ->where('nama_emulsi_id', $nama_emulsi_id);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $data = $query->get(['id', 'total_pemakaian']);
        return response()->json($data);
    }

    public function getNomorEmulsiByTotalPemakaian($total_pemakaian_id)
    {
        $data = NomorEmulsi::where('total_pemakaian_id', $total_pemakaian_id)->get(['id', 'nomor_emulsi']);
        return response()->json($data);
    }

    public function getBahanEmulsiByNomorEmulsi($nomor_emulsi_id)
    {
        // Ambil data bahan emulsi berdasarkan nomor_emulsi_id
        $bahan = BahanEmulsi::where('nomor_emulsi_id', $nomor_emulsi_id)->get(['id', 'nama_rm', 'berat_rm']);
        return response()->json($bahan);
    }

    public function edit($uuid)
    {
        $item = PembuatanEmulsi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        if ($redirect = $this->redirectToCanonicalAuditUrl('persiapan-bahan-emulsi.edit', $item)) {
            return $redirect;
        }

        // Cek batas waktu edit untuk role yang dibatasi (global config)
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($item->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('persiapan-bahan-emulsi.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }
        
        $plan_id = $item->id_plan ?: $user->id_plan;

        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            $shifts = DataShift::where('id_plan', $plan_id)->orderBy('shift')->get();
            $nama_emulsis = JenisEmulsi::where('id_produk', $item->id_produk)->get();
            $total_pemakaians = TotalPemakaianEmulsi::where('id_produk', $item->id_produk)
                ->where('nama_emulsi_id', $item->nama_emulsi_id)
                ->get();
            $nomor_emulsis = NomorEmulsi::where('total_pemakaian_id', $item->total_pemakaian_id)->get();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->orderBy('shift')->get();
            $nama_emulsis = JenisEmulsi::where('id_produk', $item->id_produk)->where('id_plan', $user->id_plan)->get();
            $total_pemakaians = TotalPemakaianEmulsi::where('id_produk', $item->id_produk)
                ->where('nama_emulsi_id', $item->nama_emulsi_id)
                ->where('id_plan', $user->id_plan)
                ->get();
            $nomor_emulsis = NomorEmulsi::where('total_pemakaian_id', $item->total_pemakaian_id)->get();
        }

        return view('qc-sistem.persiapan_bahan_emulsi.edit', compact(
            'item', 'produks', 'shifts', 'nama_emulsis', 'total_pemakaians', 'nomor_emulsis'
        ));
    }
    public function update(Request $request, $uuid)
    {
        $item = PembuatanEmulsi::where('uuid', $uuid)->firstOrFail();

        // Cek batas waktu edit untuk role yang dibatasi (global config)
        $user = auth()->user();
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($item->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('persiapan-bahan-emulsi.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        $request->validate([
            'tanggal' => 'required|date',
            'kode_produksi_emulsi' => 'required|string|max:255',
            'shift_id' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'total_pemakaian_id' => 'required|exists:total_pemakaian_emulsi,id',
            'nomor_emulsi_id' => 'required|exists:nomor_emulsi,id',
            'nama_emulsi_id' => 'required|exists:jenis_emulsi,id',
        ]);

        try {
            \DB::beginTransaction();

            // Simpan Suhu dan Hasil Emulsi per proses sebagai JSON
            $kondisiJson = $item->kondisi;
            $hasilJson = $item->hasil_emulsi;
            
            if ($request->has('kondisi_proses') && is_array($request->kondisi_proses)) {
                $kondisiJson = json_encode($request->kondisi_proses);
                $hasilJson = json_encode($request->hasil_emulsi_proses ?? []);
            }

            $item->update([
                'kode_produksi_emulsi' => $request->kode_produksi_emulsi,
                'id_produk'            => $request->id_produk,
                'berat_sampling'       => $request->berat_sampling,
                'hasil_emulsi'         => $hasilJson, 
                'kondisi'              => $kondisiJson, 
                'shift_id'             => $request->shift_id,
                'jam'                  => $request->jam,
                'total_pemakaian_id'   => $request->total_pemakaian_id,
                'nomor_emulsi_id'      => $request->nomor_emulsi_id,
                'nama_emulsi_id'       => $request->nama_emulsi_id,
                'tanggal'              => Carbon::parse($request->tanggal)->setTimeFrom(now())
            ]);

            // CASE 1: Re-generated Table (from AJAX)
            if ($request->has('bahan_emulsi_id') && is_array($request->bahan_emulsi_id)) {
                // Hapus data lama
                SuhuEmulsi::where('pembuatan_emulsi_id', $item->id)->delete();
                
                // Buat data baru (sama seperti logic store)
                foreach ($request->bahan_emulsi_id as $prosesIndex => $bahanIds) {
                    if (is_array($bahanIds)) {
                        foreach ($bahanIds as $bahanIndex => $bahanId) {
                            SuhuEmulsi::create([
                                'bahan_emulsi_id'     => $bahanId,
                                'pembuatan_emulsi_id' => $item->id,
                                'suhu'                => $request->suhu[$prosesIndex][$bahanIndex] ?? '✔',
                                'proses_ke'           => $request->proses_ke[$prosesIndex][$bahanIndex] ?? ($prosesIndex + 1),
                                'kode_produksi_bahan' => $request->kode_produksi_bahan[$prosesIndex][$bahanIndex] ?? '',
                                'berat_bahan'         => $request->berat_rm[$prosesIndex][$bahanIndex] ?? null,
                            ]);
                        }
                    }
                }
            } 
            // CASE 2: Standard Edit (existing records)
            elseif ($request->has('suhu_emulsi_id') && $request->has('suhu')) {
                foreach ($request->suhu_emulsi_id as $idx => $suhuId) {
                    $updateData = ['suhu' => $request->suhu[$idx]];
                    
                    if ($request->has('kode_produksi_bahan') && isset($request->kode_produksi_bahan[$idx])) {
                        $updateData['kode_produksi_bahan'] = $request->kode_produksi_bahan[$idx];
                    }
                    
                    if ($request->has('berat_rm_edit') && isset($request->berat_rm_edit[$idx])) {
                        $updateData['berat_bahan'] = $request->berat_rm_edit[$idx];
                    }
                    
                    SuhuEmulsi::where('id', $suhuId)->update($updateData);
                }
            }

            \DB::commit();
            return redirect()->route('persiapan-bahan-emulsi.index')->with('success', 'Data berhasil diupdate');

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function show($uuid)
    {
        $data = PembuatanEmulsi::where('uuid', $uuid)
            ->with([
                'plan',
                'produk',
                'suhuEmulsi.bahanEmulsi',
                'shift',
                'nama_emulsi',
                'nomor_emulsi',
                'user'
            ])
            ->firstOrFail();

        $user = auth()->user();
        
        // Check access permission - skip check if it's audit data
        if ($user->role !== 'superadmin' && !$data->is_audit && $data->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('persiapan-bahan-emulsi.show', $data)) {
            return $redirect;
        }

        return view('qc-sistem.persiapan_bahan_emulsi.show', compact('data'));
    }

    public function destroy($uuid)
    {
        $item = PembuatanEmulsi::where('uuid', $uuid)->firstOrFail();
        $item->delete();

        return redirect()->route('persiapan-bahan-emulsi.index')->with('success', 'Data berhasil dihapus');
    }

    /**
     * Export PDF dengan filter
     */
    public function exportPdf(Request $request, $uuid = null)
    {
        // Debug logging
        \Log::info('PembuatanEmulsiController::exportPdf called', [
            'request_data' => $request->all(),
            'controller' => 'PembuatanEmulsiController'
        ]);
        
        $user = auth()->user();
        
        $query = PembuatanEmulsi::with([
            'plan',
            'produk', 
            'shift',
            'suhuEmulsi.bahanEmulsi',
            'nama_emulsi',
            'nomor_emulsi',
            'qcApprover',
            'produksiApprover',
            'spvApprover'
        ])
        ->when($user->role !== 'superadmin', function($q) use ($user) {
            $q->where('id_plan', $user->id_plan);
        });
        

        $uuid = $uuid ?: $request->uuid;
        // Handle individual record export with UUID
        if ($uuid) {
            $query->where('uuid', $uuid);
            $filename = 'persiapan_bahan_emulsi_' . $uuid . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            // Update kode_form dengan default value
            $kode_form = $request->kode_form ?: 'QF 05/00';
            $item = PembuatanEmulsi::where('uuid', $uuid)->first();
            if ($item) {
                $item->update(['kode_form' => $kode_form]);
            }
        } else {
            // Handle bulk export with filters
            if ($request->tanggal) {
                $query->whereDate('tanggal', $request->tanggal);
            }
            
            if ($request->shift_id) {
                $query->where('shift_id', $request->shift_id);
            }
            
            if ($request->id_produk) {
                $query->whereHas('produk', function($q) use ($request) {
                    $q->where('id', $request->id_produk);
                });
            }
            
            $filename = 'persiapan_bahan_emulsi_bulk_' . date('Y-m-d_H-i-s') . '.pdf';
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();

        \Log::info('Data retrieved for PDF export', [
            'count' => $data->count(),
            'first_item' => $data->first() ? $data->first()->toArray() : null
        ]);

        if ($data->isEmpty()) {
            \Log::warning('No data found for PDF export');
            $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
            $filterInfo = [];
            
            if ($request->tanggal) {
                $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y');
            }
            if ($request->shift_id) {
                $shift = DataShift::find($request->shift_id);
                $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
            }
            if ($request->id_produk) {
                $produk = JenisProduk::find($request->id_produk);
                $filterInfo[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Unknown');
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

        // Update semua data dengan kode form
        if ($data->isNotEmpty()) {
            $kode_form = $request->kode_form ?: 'QF 05/00';
            $data->each(function($item) use ($kode_form) {
                $item->update([
                    'kode_form' => $kode_form
                ]);
            });
        }

        // Pass kode_form to view
        $kode_form = $request->kode_form ?: 'QF 05/00';
        $pdf = PDF::loadView('qc-sistem.persiapan_bahan_emulsi.export_pdf', compact('data', 'kode_form'));
        $pdf->setPaper('A4', 'landscape');
        
        \Log::info('PDF generated successfully', ['filename' => $filename]);
        
        return $pdf->download($filename);
    }

    /**
     * Menampilkan riwayat log perubahan data
     */
    public function showLogs($uuid)
    {
        $item = PembuatanEmulsi::where('uuid', $uuid)->firstOrFail();
        
        $logs = PembuatanEmulsiLog::where('pembuatan_emulsi_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->paginate(5);
        
        return view('qc-sistem.persiapan_bahan_emulsi.logs', compact('item', 'logs'));
    }

    /**
     * API untuk mendapatkan log dalam format JSON (untuk AJAX)
     */
    public function getLogsJson($uuid)
    {
        $item = PembuatanEmulsi::where('uuid', $uuid)->firstOrFail();
        
        $logs = PembuatanEmulsiLog::where('pembuatan_emulsi_id', $item->id)
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

    public function bulkExportExcel(Request $request, PersiapanBahanEmulsiBulkExportService $service)
    {
        return $service->export($request);
    }

    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal_mulai' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
                'shift_id'      => 'nullable|exists:data_shift,id',
                'id_produk'     => 'nullable|exists:jenis_produk,id',
                'kode_form'     => 'required|string|max:50',
            ]);

            $user = auth()->user();
            
            $query = PembuatanEmulsi::with([
                'plan',
                'produk', 
                'shift',
                'suhuEmulsi.bahanEmulsi',
                'nama_emulsi',
                'nomor_emulsi',
                'qcApprover',
                'produksiApprover',
                'spvApprover'
            ])
            ->where('is_audit', $request->boolean('is_audit', false))
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

            $query->whereDate('tanggal', '>=', $request->tanggal_mulai)
                  ->whereDate('tanggal', '<=', $request->tanggal_akhir);

            if ($request->filled('shift_id')) {
                $query->where('shift_id', $request->shift_id);
            }

            if ($request->filled('id_produk')) {
                $query->where('id_produk', $request->id_produk);
            }

            $data = $query->orderBy('tanggal', 'asc')->orderBy('jam', 'asc')->get();

            if ($data->isNotEmpty()) {
                $query->update(['kode_form' => $request->kode_form]);
            }

            if ($data->isEmpty()) {
                $filterInfo = [];
                $filterInfo[] = 'Periode: ' . Carbon::parse($request->tanggal_mulai)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
                if ($request->shift_id) {
                    $shift = DataShift::find($request->shift_id);
                    $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
                }
                if ($request->id_produk) {
                    $produk = JenisProduk::find($request->id_produk);
                    $filterInfo[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Unknown');
                }
                
                $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Data Tidak Ditemukan</title>
                    <style>body{font-family:Arial,sans-serif;padding:40px;text-align:center;}.container{max-width:600px;margin:0 auto;background:white;padding:40px;}</style></head>
                    <body><div class="container"><h1>⚠️ Data Tidak Ditemukan</h1><p>Tidak ada data yang sesuai dengan filter yang dipilih.</p>
                    <ul style="text-align:left">' . implode('', array_map(fn($i) => "<li>$i</li>", $filterInfo)) . '</ul>
                    <a href="javascript:window.close()">Tutup</a></div></body></html>';
                
                return response($html)->header('Content-Type', 'text/html');
            }

            $filters = [
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir,
                'shift_id' => $request->shift_id,
                'id_produk' => $request->id_produk,
                'is_audit' => $request->boolean('is_audit', false),
            ];

            $kode_form = $request->kode_form;
            $filename = 'persiapan_bahan_emulsi_bulk_' . date('Y-m-d_H-i-s') . '.pdf';

            $pdf = PDF::loadView('qc-sistem.persiapan_bahan_emulsi.export_pdf', compact('data', 'kode_form', 'filters'));
            $pdf->setPaper('A4', 'landscape');
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Bulk PDF generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AJAX search method untuk pencarian data
     */
    public function searchAjax(Request $request)
    {
        try {
            $user = auth()->user();
            $search = $request->get('search', '');
            $perPage = $request->get('per_page', 5);
            $page = (int) $request->get('page', 1);

            // Start with basic query
            $query = PembuatanEmulsi::with([
                'plan',
                'produk',
                'suhuEmulsi.bahanEmulsi',
                'user',
                'shift',
                'nama_emulsi',
                'nomor_emulsi',
                'qcApprover',
                'produksiApprover',
                'spvApprover'
            ]);            

            if ($user && $user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('kode_produksi_emulsi', 'like', "%{$search}%")
                      ->orWhereHas('produk', function($subq) use ($search) {
                          $subq->where('nama_produk', 'like', "%{$search}%");
                      })
                      ->orWhereHas('nama_emulsi', function($subq) use ($search) {
                          $subq->where('nama_emulsi', 'like', "%{$search}%");
                      });
                });
            }
            
            $records = $query->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
            
            return response()->json([
                'html' => view('qc-sistem.persiapan_bahan_emulsi._table', compact('records'))->render(),
                'pagination' => [
                    'current_page' => $records->currentPage(),
                    'last_page' => $records->lastPage(),
                    'per_page' => $records->perPage(),
                    'total' => $records->total(),
                    'from' => $records->firstItem(),
                    'to' => $records->lastItem(),
                    'has_pages' => $records->hasPages()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request, $uuid)
    {
        try {
            $user = auth()->user();
            $type = $request->input('type'); // qc, produksi, or spv
            
            // Find the record
            $pembuatanEmulsi = PembuatanEmulsi::where('uuid', $uuid)->firstOrFail();
            
            // Role-based validation
            $userRole = $user->id_role;
            
            // Check if user has permission for this approval type
            $allowedRoles = [
                'qc' => [1, 3, 5], // Role 1, 3, 5 can approve QC
                'produksi' => [1, 2, 5], // Role 1, 2, 5 can approve Produksi
                'spv' => [1, 4, 5] // Role 1, 4, 5 can approve SPV
            ];
            
            if (!in_array($userRole, $allowedRoles[$type] ?? [])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melakukan persetujuan ini.'
                ], 403);
            }
            
            // Sequential approval validation
            if ($type === 'produksi' && !$pembuatanEmulsi->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'QC harus menyetujui terlebih dahulu sebelum Produksi.'
                ], 400);
            }
            
            // Check if already approved
            $approvalField = "approved_by_{$type}";
            if ($pembuatanEmulsi->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui sebelumnya.'
                ], 400);
            }
            
            // Update approval
            $pembuatanEmulsi->update([
                $approvalField => true,
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now()
            ]);
            
            // Log the approval activity
            PembuatanEmulsiLog::create([
                'pembuatan_emulsi_id' => $pembuatanEmulsi->id,
                'pembuatan_emulsi_uuid' => $pembuatanEmulsi->uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->id_role,
                'aksi' => 'approve',
                'field_yang_diubah' => [$approvalField],
                'nilai_lama' => [$approvalField => false],
                'nilai_baru' => [$approvalField => true],
                'keterangan' => "Disetujui oleh {$type}: {$user->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui.',
                'data' => $pembuatanEmulsi->fresh()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk Approve data by date and shift
     */
    public function bulkApprove(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'shift_id' => 'required',
                'type' => 'required|in:qc,produksi,spv'
            ]);

            $user = auth()->user();
            $userRole = $user->id_role;
            $type = $request->type;
            $tanggal = $request->tanggal;
            $shift_id = $request->shift_id;

            // Role validation
            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5]
            ];

            if (!in_array($userRole, $allowedRoles[$type])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized role'], 403);
            }

            // Get records to approve
            $query = PembuatanEmulsi::whereDate('tanggal', $tanggal)->where('shift_id', $shift_id);

            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }

            $records = $query->get();

            if ($records->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data untuk disetujui'], 404);
            }

            // Sequential validation check
            if ($type === 'produksi') {
                $anyMissingQc = $records->contains(fn($r) => !$r->approved_by_qc);
                if ($anyMissingQc) {
                    return response()->json(['success' => false, 'message' => 'Beberapa data belum disetujui oleh QC'], 400);
                }
            }

            if ($type === 'spv') {
                $anyMissingProduksi = $records->contains(fn($r) => !$r->approved_by_produksi);
                if ($anyMissingProduksi) {
                    return response()->json(['success' => false, 'message' => 'Beberapa data belum disetujui oleh Produksi'], 400);
                }
            }

            $approvalField = 'approved_by_' . $type;
            $approverField = $type . '_approved_by';
            $approvedAtField = $type . '_approved_at';

            \DB::beginTransaction();

            $count = 0;
            foreach ($records as $record) {
                if (!$record->$approvalField) {
                    $record->update([
                        $approvalField => true,
                        $approverField => $user->id,
                        $approvedAtField => now()
                    ]);
                    
                    // Log the approval activity
                    PembuatanEmulsiLog::create([
                        'pembuatan_emulsi_id' => $record->id,
                        'pembuatan_emulsi_uuid' => $record->uuid,
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_role' => $user->id_role,
                        'aksi' => 'approve',
                        'field_yang_diubah' => [$approvalField],
                        'nilai_lama' => [$approvalField => false],
                        'nilai_baru' => [$approvalField => true],
                        'keterangan' => "Bulk Approved by {$type}: {$user->name}",
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ]);
                    
                    $count++;
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menyetujui {$count} data sebagai " . strtoupper($type)
            ]);

        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk Approve Selected Rows
     */
    public function bulkApproveSelected(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv',
                'uuids' => 'required|array',
                'uuids.*' => 'string'
            ]);

            $user = auth()->user();
            $type = $request->input('type');
            $uuids = $request->input('uuids');

            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5]
            ];

            if (!in_array($user->id_role, $allowedRoles[$type] ?? [])) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk persetujuan ini.'], 403);
            }

            $query = PembuatanEmulsi::whereIn('uuid', $uuids);

            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }

            $records = $query->get();

            if ($records->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data valid yang dipilih.'], 404);
            }

            $approvalField = "approved_by_{$type}";
            $approverField = "{$type}_approved_by";
            $approvedAtField = "{$type}_approved_at";

            \DB::beginTransaction();

            $approvedCount = 0;
            $skippedCount = 0;

            foreach ($records as $record) {
                // Auto-skip logic
                if ($record->$approvalField) {
                    $skippedCount++;
                    continue;
                }

                if ($type === 'produksi' && !$record->approved_by_qc) {
                    $skippedCount++;
                    continue;
                }

                $record->update([
                    $approvalField => true,
                    $approverField => $user->id,
                    $approvedAtField => now()
                ]);

                PembuatanEmulsiLog::create([
                    'pembuatan_emulsi_id' => $record->id,
                    'pembuatan_emulsi_uuid' => $record->uuid,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->id_role,
                    'aksi' => 'approve',
                    'field_yang_diubah' => [$approvalField],
                    'nilai_lama' => [$approvalField => false],
                    'nilai_baru' => [$approvalField => true],
                    'keterangan' => "Bulk Approved Selected by {$type}: {$user->name}",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                $approvedCount++;
            }

            \DB::commit();

            $typeLabel = strtoupper($type);
            $msg = "Berhasil menyetujui {$approvedCount} data sebagai {$typeLabel}.";
            if ($skippedCount > 0) {
                $msg .= " ({$skippedCount} data dilewati karena sudah disetujui atau belum memenuhi syarat alur approval).";
            }

            return response()->json([
                'success' => true,
                'message' => $msg
            ]);

        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function auditIndex(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        $auditedSourceUuids = PembuatanEmulsi::where('is_audit', true)->whereNotNull('source_uuid')->pluck('source_uuid')->toArray();

$query = PembuatanEmulsi::with($this->auditRelations())
            ->where(function ($q) use ($auditedSourceUuids) {
            $q->where('is_audit', true)
              ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                  $subQ->where('is_audit', false);
                  if (!empty($auditedSourceUuids)) {
                      $subQ->whereNotIn('uuid', $auditedSourceUuids);
                  }
              });
        })
            ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        if ($search) {
            $query->where(fn($q) => $q->where('kode_produksi_emulsi', 'like', "%{$search}%")
                ->orWhereHas('produk', function($subq) use ($search) {
                    $subq->where('nama_produk', 'like', "%{$search}%");
                }));
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->paginate($perPage);

        return view('qc-sistem.persiapan_bahan_emulsi.index_audit', compact('records', 'search', 'perPage'));
    }

    public function auditSearchAjax(Request $request)
    {
        try {
            $user = auth()->user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 5);
            $page = (int) $request->get('page', 1);

            $query = PembuatanEmulsi::with($this->auditRelations())
                ->where('is_audit', true)
                ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan));

            if ($search) {
                $query->where(fn($q) => $q->where('kode_produksi_emulsi', 'like', "%{$search}%")
                    ->orWhereHas('produk', fn($sq) => $sq->where('nama_produk', 'like', "%{$search}%")));
            }

            $records = $query->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $isAuditPage = true;

            return response()->json([
                'html' => view('qc-sistem.persiapan_bahan_emulsi._table', compact('records', 'isAuditPage'))->render(),
                'pagination' => [
                    'current_page' => $records->currentPage(),
                    'last_page' => $records->lastPage(),
                    'per_page' => $records->perPage(),
                    'total' => $records->total(),
                    'from' => $records->firstItem(),
                    'to' => $records->lastItem(),
                    'has_pages' => $records->hasPages(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }
}
