<?php

namespace App\Http\Controllers;

use App\Models\InputMetalDetector;
use App\Models\InputMetalDetectorLog;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MetalDetectorExport;
use App\Http\Controllers\Traits\HasAuditController;

class MetalDetectorController extends Controller
{
    use HasAuditController;

    protected function auditModel(): string
    {
        return InputMetalDetector::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'input-metal-detector';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.input_metal_detector';
    }

    protected function auditRelations(): array
    {
        return ['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover'];
    }

    protected function auditSearchColumns(): array
    {
        return ['kode_produksi', 'keterangan'];
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
        return 'id_shift';
    }

    protected function auditPlanColumn(): string
    {
        return 'id_plan';
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Auditor langsung diarahkan ke halaman audit
        if ($user->id_role == 6) {
            return redirect()->route('input-metal-detector.audit');
        }

        $search = $request->get('search');
        $perPage = (int) $request->get('per_page', 10);
        if ($perPage <= 0) { $perPage = 10; }
        $filters = $request->validate([
            'line' => 'nullable|in:1,2,3,4,5,6,7,8',
        ]);
        $line = $filters['line'] ?? null;

        $query = InputMetalDetector::operasional()->with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover']);

        if ($line !== null) {
            $query->where('line', $line);
        }

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_produksi', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('produk', function ($q) use ($search) {
                        $q->where('nama_produk', 'like', "%{$search}%");
                    });
            });
        }

        // Paginate records first (not groups)
        $this->applyDateRangeFilter(request(), $query);
        $items = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        // Pass items without grouping to view
        $metalDetector = $items;

        $cachedShifts = DataShift::when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))->get();
        $cachedProduk = JenisProduk::when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan))->get();

        return view('qc-sistem.input_metal_detector.index', compact('metalDetector', 'search', 'perPage', 'cachedShifts', 'cachedProduk'));
    }

    protected function applyAuditFilters(Request $request, $query): void
    {
        $filters = $request->validate([
            'line' => 'nullable|in:1,2,3,4,5,6,7,8',
        ]);

        if (isset($filters['line'])) {
            $query->where('line', $filters['line']);
        }
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
            $shifts = DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        return view('qc-sistem.input_metal_detector.create', compact('produks', 'plans','shifts'));
    }
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validasi unified untuk semua role
        $request->validate([
            'id_shift.*'              => 'required|exists:data_shift,id',
            'line.*'                  => 'required|in:1,2,3,4,5,6,7,8',
            'tanggal.*'               => 'required|date',
            'jam.*'                   => 'required',
            'id_produk.*'             => 'required|exists:jenis_produk,id',
            'kode_produksi.*'         => 'required|string|max:255',
            'berat_produk.*'          => 'required|string|max:255',
            'fe_depan_aktual.*'       => 'required|in:✔,✘',
            'fe_tengah_aktual.*'      => 'required|in:✔,✘',
            'fe_belakang_aktual.*'    => 'required|in:✔,✘',
            'non_fe_depan_aktual.*'   => 'required|in:✔,✘',
            'non_fe_tengah_aktual.*'  => 'required|in:✔,✘',
            'non_fe_belakang_aktual.*' => 'required|in:✔,✘',
            'sus_depan_aktual.*'      => 'required|in:✔,✘',
            'sus_tengah_aktual.*'     => 'required|in:✔,✘',
            'sus_belakang_aktual.*'   => 'required|in:✔,✘',
            'keterangan.*'            => 'nullable|string|max:255',
        ]);

    $shiftIds = Arr::wrap($request->input('id_shift'));
    $lines = Arr::wrap($request->input('line'));
    $jams = Arr::wrap($request->input('jam'));
    $tanggalArr = Arr::wrap($request->input('tanggal'));
    $produkIds = Arr::wrap($request->input('id_produk'));
    $kodeProduksiArr = Arr::wrap($request->input('kode_produksi'));
    $beratProdukArr = Arr::wrap($request->input('berat_produk'));
    $feDepanArr = Arr::wrap($request->input('fe_depan_aktual'));
    $feTengahArr = Arr::wrap($request->input('fe_tengah_aktual'));
    $feBelakangArr = Arr::wrap($request->input('fe_belakang_aktual'));
    $nonFeDepanArr = Arr::wrap($request->input('non_fe_depan_aktual'));
    $nonFeTengahArr = Arr::wrap($request->input('non_fe_tengah_aktual'));
    $nonFeBelakangArr = Arr::wrap($request->input('non_fe_belakang_aktual'));
    $susDepanArr = Arr::wrap($request->input('sus_depan_aktual'));
    $susTengahArr = Arr::wrap($request->input('sus_tengah_aktual'));
    $susBelakangArr = Arr::wrap($request->input('sus_belakang_aktual'));
    $keteranganArr = Arr::wrap($request->input('keterangan'));

    $count = count($shiftIds);
    $savedCount = 0;

    for ($i = 0; $i < $count; $i++) {
        $tanggal = Carbon::parse($tanggalArr[$i])->setTimeFrom(now());

        $data = [
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'uuid' => Str::uuid(),
            'id_shift' => $shiftIds[$i] ?? null,
            'line' => $lines[$i] ?? null,
            'tanggal' => $tanggal,
            'jam' => $jams[$i] ?? null,
            'id_produk' => $produkIds[$i] ?? null,
            'kode_produksi' => $kodeProduksiArr[$i] ?? null,
            'berat_produk' => $beratProdukArr[$i] ?? null,
            'keterangan' => $keteranganArr[$i] ?? null,
            'aktual_json_metal' => [
                'fe_depan_aktual' => $feDepanArr[$i] ?? null,
                'fe_tengah_aktual' => $feTengahArr[$i] ?? null,
                'fe_belakang_aktual' => $feBelakangArr[$i] ?? null,
                'non_fe_depan_aktual' => $nonFeDepanArr[$i] ?? null,
                'non_fe_tengah_aktual' => $nonFeTengahArr[$i] ?? null,
                'non_fe_belakang_aktual' => $nonFeBelakangArr[$i] ?? null,
                'sus_depan_aktual' => $susDepanArr[$i] ?? null,
                'sus_tengah_aktual' => $susTengahArr[$i] ?? null,
                'sus_belakang_aktual' => $susBelakangArr[$i] ?? null,
            ],
            // old columns left null for new data
            'fe_depan_aktual' => null,
            'fe_tengah_aktual' => null,
            'fe_belakang_aktual' => null,
            'non_fe_depan_aktual' => null,
            'non_fe_tengah_aktual' => null,
            'non_fe_belakang_aktual' => null,
            'sus_depan_aktual' => null,
            'sus_tengah_aktual' => null,
            'sus_belakang_aktual' => null,
        ];

        InputMetalDetector::create($data);
        $savedCount++;
    }


        return redirect()->route('input-metal-detector.index')->with('success', "$savedCount Data Metal Detector berhasil disimpan.");
    }

    public function edit($uuid)
    {
        $metalDetector = InputMetalDetector::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $metalDetector->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        $isAuditMode = (bool) $metalDetector->is_audit;
        $queryParameters = request()->query();
        $hasAuditFlag = array_key_exists('is_audit', $queryParameters);

        if (($isAuditMode && request()->query('is_audit') !== '1') || (!$isAuditMode && $hasAuditFlag)) {
            unset($queryParameters['is_audit'], $queryParameters['uuid']);
            if ($isAuditMode) {
                $queryParameters['is_audit'] = '1';
            }

            return redirect()->route(
                'input-metal-detector.edit',
                array_merge(['uuid' => $metalDetector->uuid], $queryParameters)
            );
        }

        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
            $shifts = DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        // Cek batas waktu edit untuk role yang dibatasi (global config)
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($metalDetector->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('input-metal-detector.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }
   
        return view('qc-sistem.input_metal_detector.edit', compact('metalDetector', 'produks', 'plans', 'shifts'));
    }

    public function update(Request $request, $uuid)
    {
        $metalDetector = InputMetalDetector::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $metalDetector->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui data ini.');
        }

        // Cek batas waktu edit untuk role yang dibatasi (global config)
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($metalDetector->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('input-metal-detector.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);
        
        $rules = [
            'id_shift' => 'required|exists:data_shift,id',
            'line' => 'required|in:1,2,3,4,5,6,7,8',
            'tanggal' => 'required|date_format:Y-m-d',
            'jam' => 'required',
            'id_produk' => 'required|exists:jenis_produk,id',
            'kode_produksi' => 'required|string|max:255',
            'berat_produk' => 'required|string|max:255',
            'fe_depan_aktual' => 'required|in:✔,✘',
            'fe_tengah_aktual' => 'required|in:✔,✘',
            'fe_belakang_aktual' => 'required|in:✔,✘',
            'non_fe_depan_aktual' => 'required|in:✔,✘',
            'non_fe_tengah_aktual' => 'required|in:✔,✘',
            'non_fe_belakang_aktual' => 'required|in:✔,✘',
            'sus_depan_aktual' => 'required|in:✔,✘',
            'sus_tengah_aktual' => 'required|in:✔,✘',
            'sus_belakang_aktual' => 'required|in:✔,✘',
            'keterangan' => 'nullable|string|max:255',
        ];

        $validatedData = $request->validate($rules);

        $validatedData['id_plan'] = $user->id_plan;
        
        $dateOnly = $validatedData['tanggal'];
        $validatedData['tanggal'] = Carbon::parse($dateOnly)->setTimeFrom(
            $metalDetector->tanggal ? Carbon::parse($metalDetector->tanggal) : now()
        );

        $validatedData['aktual_json_metal'] = [
            'fe_depan_aktual' => $validatedData['fe_depan_aktual'] ?? null,
            'fe_tengah_aktual' => $validatedData['fe_tengah_aktual'] ?? null,
            'fe_belakang_aktual' => $validatedData['fe_belakang_aktual'] ?? null,
            'non_fe_depan_aktual' => $validatedData['non_fe_depan_aktual'] ?? null,
            'non_fe_tengah_aktual' => $validatedData['non_fe_tengah_aktual'] ?? null,
            'non_fe_belakang_aktual' => $validatedData['non_fe_belakang_aktual'] ?? null,
            'sus_depan_aktual' => $validatedData['sus_depan_aktual'] ?? null,
            'sus_tengah_aktual' => $validatedData['sus_tengah_aktual'] ?? null,
            'sus_belakang_aktual' => $validatedData['sus_belakang_aktual'] ?? null,
        ];

        // Unset old columns so they remain completely untouched
        unset($validatedData['fe_depan_aktual']);
        unset($validatedData['fe_tengah_aktual']);
        unset($validatedData['fe_belakang_aktual']);
        unset($validatedData['non_fe_depan_aktual']);
        unset($validatedData['non_fe_tengah_aktual']);
        unset($validatedData['non_fe_belakang_aktual']);
        unset($validatedData['sus_depan_aktual']);
        unset($validatedData['sus_tengah_aktual']);
        unset($validatedData['sus_belakang_aktual']);

        $metalDetector->update($validatedData);

        return redirect()->route('input-metal-detector.index')->with('success', 'Data Metal Detector berhasil diperbarui.');
    }

    public function show($uuid)
    {
        $item = InputMetalDetector::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('input-metal-detector.show', $item)) {
            return $redirect;
        }

        return view('qc-sistem.input_metal_detector.show', compact('item'));
    }

    public function destroy($uuid)
    {
        $item = InputMetalDetector::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $item->delete();
        return redirect()->route('input-metal-detector.index')->with('success', 'Data berhasil dihapus');
    }

    /**
     * Show logs for specific metal detector record
     */
    public function showLogs($uuid)
    {
        $item = InputMetalDetector::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat log data ini.');
        }

        $logs = InputMetalDetectorLog::where('input_metal_detector_uuid', $uuid)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.input_metal_detector.logs', compact('item', 'logs'));
    }

    /**
     * Get logs data in JSON format for DataTables
     */
    public function getLogsJson($uuid)
    {
        $item = InputMetalDetector::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = InputMetalDetectorLog::where('input_metal_detector_uuid', $uuid)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $logs->map(function ($log) {
                return [
                    'tanggal' => $log->created_at->format('d/m/Y H:i:s'),
                    'user_name' => $log->user_name,
                    'user_role' => $log->user_role,
                    'aksi' => ucfirst($log->aksi),
                    'field_yang_diubah' => $log->field_yang_diubah ? implode(', ', array_map(function($field) use ($log) {
                        return $log->getNamaFieldSingle($field);
                    }, $log->field_yang_diubah)) : '-',
                    'deskripsi_perubahan' => $log->deskripsi_perubahan ?: '-'
                ];
            })
        ]);
    }

    public function bulkExportPdf(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'shift_id' => 'nullable|exists:data_shift,id',
            'id_produk' => 'nullable|exists:jenis_produk,id',
            'line' => 'nullable|in:1,2,3,4,5,6,7,8',
            'kode_form' => 'required|string|max:255'
        ]);

        $user = Auth::user();
        $query = InputMetalDetector::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover']);

        // Filter berdasarkan role user
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
            $query->where('id_shift', $request->shift_id);
        }

        if ($request->filled('id_produk')) {
            $query->where('id_produk', $request->id_produk);
        }

        if ($request->filled('line')) {
            $query->where('line', $request->line);
        }

        $data = $query->orderBy('tanggal', 'asc')->get();

        // Update kode_form untuk semua data yang difilter (hanya jika ada data)
        if ($data->count() > 0) {
            $dataIds = $data->pluck('id')->toArray();
            InputMetalDetector::whereIn('id', $dataIds)->update(['kode_form' => $request->kode_form]);
            
            // Refresh data untuk mendapatkan kode_form yang baru disimpan
            $data = $query->orderBy('tanggal', 'asc')->get();
        }

        // Jika tidak ada data, tampilkan halaman error HTML (tidak download PDF)
        if ($data->isEmpty()) {
            $errorMessage = 'Tidak ada data Metal Detector yang sesuai dengan filter yang dipilih.';
            $filterInfo = [];
            
            if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
                $filterInfo[] = 'Tanggal: ' . Carbon::parse($request->tanggal_awal)->format('d/m/Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d/m/Y');
            } elseif ($request->filled('tanggal_awal')) {
                $filterInfo[] = 'Tanggal: ' . Carbon::parse($request->tanggal_awal)->format('d/m/Y');
            }
            if ($request->shift_id) {
                $shift = DataShift::find($request->shift_id);
                $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Tidak Diketahui');
            }
            if ($request->id_produk) {
                $produk = JenisProduk::find($request->id_produk);
                $filterInfo[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Tidak Diketahui');
            }
            if ($request->line) {
                $filterInfo[] = 'Line: Line ' . $request->line;
            }
            $filterInfo[] = 'Kode Form: ' . $request->kode_form;
            
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Data Tidak Ditemukan - Metal Detector</title>
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
                    <div class="icon">🔍</div>
                    <h1>Data Metal Detector Tidak Ditemukan</h1>
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
                        Silakan coba dengan filter yang berbeda atau pastikan data Metal Detector sudah tersedia di sistem.
                    </p>
                    <a href="javascript:window.close()" class="btn">Tutup</a>
                </div>
            </body>
            </html>';
            
            return response($html)->header('Content-Type', 'text/html');
        }

        // Prepare filter info for PDF
        $filterDateStr = 'Semua Tanggal';
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $filterDateStr = Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
        } elseif ($request->filled('tanggal_awal')) {
            $filterDateStr = Carbon::parse($request->tanggal_awal)->format('d-m-Y');
        }

        $filterInfo = [
            'tanggal' => $filterDateStr,
            'shift' => $request->shift_id ? DataShift::find($request->shift_id)->shift ?? 'Tidak Diketahui' : 'Semua Shift',
            'produk' => $request->id_produk ? JenisProduk::find($request->id_produk)->nama_produk ?? 'Tidak Diketahui' : 'Semua Produk',
            'line' => $request->line ? 'Line ' . $request->line : 'Semua Line',
            'kode_form' => $request->kode_form
        ];

        $pdf = \PDF::loadView('qc-sistem.input_metal_detector.export_pdf', compact('data', 'filterInfo'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'Metal_Detector_' . ($request->tanggal_awal ? Carbon::parse($request->tanggal_awal)->format('Y-m-d') : 'All') . '_' . time() . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export Excel massal berdasarkan filter (meniru bulkExportPdf)
     */
    public function bulkExportExcel(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'shift_id' => 'nullable|exists:data_shift,id',
            'id_produk' => 'nullable|exists:jenis_produk,id',
            'line' => 'nullable|in:1,2,3,4,5,6,7,8',
            'kode_form' => 'required|string|max:255'
        ]);

        $user = Auth::user();
        $query = InputMetalDetector::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover']);

        // Filter berdasarkan role user
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
            $query->where('id_shift', $request->shift_id);
        }

        if ($request->filled('id_produk')) {
            $query->where('id_produk', $request->id_produk);
        }

        if ($request->filled('line')) {
            $query->where('line', $request->line);
        }

        $data = $query->orderBy('tanggal', 'asc')->get();

        // Update kode_form untuk semua data yang difilter (hanya jika ada data)
        if ($data->count() > 0) {
            $dataIds = $data->pluck('id')->toArray();
            InputMetalDetector::whereIn('id', $dataIds)->update(['kode_form' => $request->kode_form]);

            // Refresh data untuk mendapatkan kode_form yang baru disimpan
            $data = $query->orderBy('tanggal', 'asc')->get();
        }

        // Jika tidak ada data, tampilkan halaman error HTML (tidak download Excel)
        if ($data->isEmpty()) {
            return $this->buildNoDataHtmlResponse(
                $request,
                'Tidak ada data Metal Detector yang sesuai dengan filter yang dipilih.'
            );
        }

        // Prepare filter info for Excel
        $filterDateStr = 'Semua Tanggal';
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $filterDateStr = Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
        } elseif ($request->filled('tanggal_awal')) {
            $filterDateStr = Carbon::parse($request->tanggal_awal)->format('d-m-Y');
        }

        $filterInfo = [
            'tanggal' => $filterDateStr,
            'shift' => $request->shift_id ? DataShift::find($request->shift_id)->shift ?? 'Tidak Diketahui' : 'Semua Shift',
            'produk' => $request->id_produk ? JenisProduk::find($request->id_produk)->nama_produk ?? 'Tidak Diketahui' : 'Semua Produk',
            'line' => $request->line ? 'Line ' . $request->line : 'Semua Line',
            'kode_form' => $request->kode_form
        ];

        $filename = 'Metal_Detector_' . ($request->tanggal_awal ? Carbon::parse($request->tanggal_awal)->format('Y-m-d') : 'All') . '_' . time() . '.xlsx';

        return Excel::download(new MetalDetectorExport($data, $filterInfo), $filename);
    }

    /**
     * Halaman HTML "Data Tidak Ditemukan" untuk export
     */
    private function buildNoDataHtmlResponse(Request $request, string $errorMessage)
    {
        $filterInfo = [];

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $filterInfo[] = 'Tanggal: ' . Carbon::parse($request->tanggal_awal)->format('d/m/Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d/m/Y');
        } elseif ($request->filled('tanggal_awal')) {
            $filterInfo[] = 'Tanggal: ' . Carbon::parse($request->tanggal_awal)->format('d/m/Y');
        }
        if ($request->shift_id) {
            $shift = DataShift::find($request->shift_id);
            $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Tidak Diketahui');
        }
        if ($request->id_produk) {
            $produk = JenisProduk::find($request->id_produk);
            $filterInfo[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Tidak Diketahui');
        }
        if ($request->line) {
            $filterInfo[] = 'Line: Line ' . $request->line;
        }
        $filterInfo[] = 'Kode Form: ' . $request->kode_form;

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Data Tidak Ditemukan - Metal Detector</title>
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
                <div class="icon">🔍</div>
                <h1>Data Metal Detector Tidak Ditemukan</h1>
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
                    Silakan coba dengan filter yang berbeda atau pastikan data Metal Detector sudah tersedia di sistem.
                </p>
                <a href="javascript:window.close()" class="btn">Tutup</a>
            </div>
        </body>
        </html>';

        return response($html)->header('Content-Type', 'text/html');
    }

    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $metalDetector = InputMetalDetector::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control
        $allowedRoles = [
            'qc' => [1, 3, 5], // QC roles
            'produksi' => [1, 2, 5], // Produksi role
            'spv' => [1, 4, 5] // SPV role
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan persetujuan ini.'
            ], 403);
        }

        // Sequential approval validation
        if ($type === 'produksi' && !$metalDetector->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$metalDetector->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($metalDetector->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $metalDetector->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        // Log the approval activity
        InputMetalDetectorLog::create([
            'uuid' => Str::uuid(),
            'input_metal_detector_id' => $metalDetector->id,
            'input_metal_detector_uuid' => $metalDetector->uuid,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'aksi' => 'approve',
            'field_yang_diubah' => [$approvalField],
            'deskripsi_perubahan' => "Data disetujui oleh {$type}: {$user->name}"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }

    /**
     * Bulk Approve data metal detector per shift
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv',
            'tanggal' => 'required|date',
            'shift_id' => 'required|exists:data_shift,id',
            'line' => 'required|in:1,2,3,4,5,6,7,8'
        ]);

        $user = Auth::user();
        $type = $request->type;
        $tanggal = $request->tanggal;
        $shift_id = $request->shift_id;
        $line = $request->line;

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

        // Ambil semua data pada tanggal, shift, dan line tersebut
        $query = InputMetalDetector::whereDate('tanggal', $tanggal)
                                  ->where('id_shift', $shift_id)
                                  ->where('line', $line);

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
        $approvedByField = "{$type}_approved_by";
        $approvedAtField = "{$type}_approved_at";
        
        // Update all records
        $query->update([
            $approvalField => true,
            $approvedByField => $user->id,
            $approvedAtField => now()
        ]);

        // Log approval activity
        foreach ($items as $item) {
            InputMetalDetectorLog::create([
                'uuid' => Str::uuid(),
                'input_metal_detector_id' => $item->id,
                'input_metal_detector_uuid' => $item->uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'aksi' => 'approve',
                'field_yang_diubah' => [$approvalField],
                'deskripsi_perubahan' => "Data disetujui secara massal oleh {$type}: {$user->name}"
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Seluruh data pada shift ini berhasil disetujui.'
        ]);
    }

    /**
     * Bulk Approve Selected Items by Checkbox
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

            // Build column names dynamically
            $approvalColumn = "approved_by_{$type}";
            $approverColumn = "{$type}_approved_by";
            $timestampColumn = "{$type}_approved_at";

            // Find records - ONLY unapproved ones (smart auto-skip)
            $itemsToUpdate = InputMetalDetector::whereIn('uuid', $uuids)
                ->where($approvalColumn, false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->get();

            $count = $itemsToUpdate->count();

            if ($count === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Semua data sudah diverifikasi sebelumnya.'
                ]);
            }

            // Update operasional records
            $updateData = [
                $approvalColumn => true,
                $approverColumn => $user->id,
                $timestampColumn => now(),
            ];

            InputMetalDetector::whereIn('uuid', $uuids)
                ->where($approvalColumn, false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();
            
            InputMetalDetector::where('is_audit', true)
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
