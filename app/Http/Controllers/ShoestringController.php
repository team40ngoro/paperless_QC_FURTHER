<?php

namespace App\Http\Controllers;

use App\Models\Shoestring;
use App\Models\ShoestringLog;
use App\Models\DataShift;
use App\Models\Plan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ShoestringTemplateExport;
use App\Imports\ShoestringImport;
use App\Models\DataDefect;
use App\Http\Controllers\Traits\HasAuditController;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Http\Controllers\Traits\HasExcelImport;
use App\Services\BulkExport\ShoestringBulkExportService;

class ShoestringController extends Controller
{
    use HasAuditController, HasNoDataHtmlResponse, HasExcelImport;

    protected function auditModel(): string { return Shoestring::class; }
    protected function auditRoutePrefix(): string { return 'shoestring'; }
    protected function auditViewPrefix(): string { return 'qc-sistem.shoestring'; }
    protected function auditRelations(): array
    {
        return ['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'createdBy', 'originalVersion'];
    }
    protected function auditSearchColumns(): array { return ['nama_produsen', 'kode_produksi', 'catatan']; }
    protected function auditDateColumn(): string { return 'tanggal'; }
    protected function auditTimeColumn(): string { return 'jam'; }
    protected function auditShiftRelation(): string { return 'shift'; }
    protected function auditShiftColumn(): string { return 'shift_id'; }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Auditor langsung ke halaman audit
        if ($user->id_role == 6) {
            return redirect()->route('shoestring.audit');
        }

        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        $query = Shoestring::with(['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'createdBy', 'auditVersion'])
            ->operasional()
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_produsen', 'LIKE', '%' . $search . '%')
                  ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%')
                  ->orWhere('catatan', 'LIKE', '%' . $search . '%');
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

        return view('qc-sistem.shoestring.index', compact('records', 'search', 'perPage', 'shifts'));
    }

    public function searchAjax(Request $request)
    {
        try {
            $user = auth()->user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 5);
            $page = (int) $request->get('page', 1);

            $query = Shoestring::with(['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'createdBy', 'auditVersion'])
                ->operasional()
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_produsen', 'LIKE', '%' . $search . '%')
                      ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%')
                      ->orWhere('catatan', 'LIKE', '%' . $search . '%');
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
                'html' => view('qc-sistem.shoestring._table', compact('records'))->render(),
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
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        $produsens = DataDefect::filterByRole()->pluck('nama_produsen')->unique()->values();
        $dataDefect = DataDefect::filterByRole()->get();
        return view('qc-sistem.shoestring.create', compact('shifts', 'plans', 'produsens', 'dataDefect'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'shift_id' => 'required|integer',
            'entries' => 'required|array|min:1',
            'entries.*.nama_produsen' => 'required|string',
            'entries.*.kode_produksi' => 'required|string',
            'entries.*.best_before' => 'required|string',
            'entries.*.berat_sampling' => 'nullable|string',
            'entries.*.sampling_defect' => 'nullable|array',
            'entries.*.sampling_defect_qty' => 'nullable|array',
            'entries.*.catatan' => 'nullable|string',
            'entries.*.dokumentasi_base64' => 'nullable|array',
            'entries.*.dokumentasi_base64.*' => 'string',
        ]);

        $user = auth()->user();
        try {
            foreach ($data['entries'] as $entry) {
                // 1. Process sampling_defect_qty mapping
                $qtyMap = [];
                $totalDefect = 0;
                $filledDefects = [];
                
              if (!empty($entry['sampling_defect_qty'])) {
                    foreach ($entry['sampling_defect_qty'] as $defectKey => $qtyVal) {
                        if ($qtyVal !== null && $qtyVal !== '' && $qtyVal !== 'null') {
                            $qtyMap[$defectKey] = (string)$qtyVal;
                            $totalDefect += (float)$qtyVal;
                            
                            // Get name of defect for sampling_defect string log
                            if (is_numeric($defectKey)) {
                                $def = \App\Models\DataDefect::find($defectKey);
                                if ($def) {
                                    $filledDefects[] = $def->jenis_defect;
                                } else {
                                    $filledDefects[] = "Defect #" . $defectKey;
                                }
                            } else {
                                $filledDefects[] = $defectKey;
                            }
                        }
                    }
                }

                 $samplingDefectNamesStr = !empty($filledDefects) ? Str::limit(implode(', ', $filledDefects), 250, '...') : null;

                // 3. Process base64 documentation
                $dokumentasiPaths = [];
                if (!empty($entry['dokumentasi_base64'])) {
                    foreach ($entry['dokumentasi_base64'] as $base64Image) {
                        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                            $fileData = substr($base64Image, strpos($base64Image, ',') + 1);
                            $type = strtolower($type[1]);
                            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                                continue;
                            }
                            $fileData = base64_decode($fileData);
                            if ($fileData === false) {
                                continue;
                            }
                            $filename = 'shoestring_docs/' . Str::uuid() . '.' . $type;
                            \Storage::disk('public')->put($filename, $fileData);
                            $dokumentasiPaths[] = $filename;
                        }
                    }
                }

                Shoestring::create([
                    'uuid' => (string) Str::uuid(),
                    'id_plan' => $user->id_plan,
                    'created_by' => $user->id,
                    'tanggal' => Carbon::parse($data['tanggal'])->setTimeFrom(now())->format('Y-m-d H:i:s'),
                    'jam' => $data['jam'],
                    'shift_id' => $data['shift_id'],
                    'nama_produsen' => $entry['nama_produsen'],
                    'kode_produksi' => $entry['kode_produksi'],
                    'best_before' => $entry['best_before'],
                    'berat_sampling' => $entry['berat_sampling'] ?? null,
                    'sampling_defect' => $samplingDefectNamesStr,
                    'sampling_defect_qty' => !empty($qtyMap) ? $qtyMap : null,
                    'total_defect' => $totalDefect > 0 ? (string)$totalDefect : null,
                    'catatan' => $entry['catatan'] ?? null,
                    'dokumentasi' => !empty($dokumentasiPaths) ? $dokumentasiPaths : null,
                ]);
            }
            return redirect()->route('shoestring.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($uuid)
    {
        $shoestring = Shoestring::with(['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'createdBy'])->where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        if ($user->role !== 'superadmin' && (int) $shoestring->id_plan !== (int) $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('shoestring.show', $shoestring)) {
            return $redirect;
        }

        $qtyMap = !empty($shoestring->sampling_defect_qty) ? $shoestring->sampling_defect_qty : [];
        if (is_string($qtyMap)) {
            $qtyMap = json_decode($qtyMap, true) ?: [];
        }
        // Bangun peta label => detail (kategori, jenis, sub jenis, spesifikasi) dari master Data Defect produsen ini
        $sanitize = fn ($v) => preg_replace('/[\[\]"]/', '', (string) $v);
        $norm     = fn ($v) => strtolower(trim($sanitize($v)));
        $lookup   = []; // format baru: "jenis - sub jenis - spesifikasi"
        $legacy   = []; // format lama: "jenis - spesifikasi" atau "jenis"

        $records = \App\Models\DataDefect::where('nama_produsen', $shoestring->nama_produsen)->get();
        foreach ($records as $record) {
            $detail = $record->detail_defect ?? [];
            if (is_string($detail)) {
                $detail = json_decode($detail, true) ?: [];
            }
            foreach ($detail as $kat) {
                foreach (($kat['items'] ?? []) as $it) {
                    $jenis = $it['jenis'] ?? '';
                    $sub   = $it['sub_jenis'] ?? '';
                    $spec  = $it['spesifikasi'] ?? '';
                    $meta  = [
                        'kategori'    => $kat['kategori'] ?? '-',
                        'jenis'       => $jenis !== '' ? $jenis : '-',
                        'sub_jenis'   => $sub !== '' ? $sub : '-',
                        'spesifikasi' => $spec !== '' ? $spec : '-',
                    ];
                    $join = fn (array $parts) => $norm(implode(' - ', array_filter($parts, fn ($v) => trim((string) $v) !== '')));

                    $lookup[$join([$jenis, $sub, $spec])] = $meta;
                    $legacy[$join([$jenis, $spec])] = $legacy[$join([$jenis, $spec])] ?? $meta;
                    $legacy[$join([$jenis])]        = $legacy[$join([$jenis])] ?? $meta;
                }
            }
        }

        // Susun baris rincian defect untuk ditampilkan di view
        $defectRows = [];
        foreach ($qtyMap as $key => $qty) {
            if (is_numeric($key)) {
                // Data lama berbasis ID numerik
                $def = \App\Models\DataDefect::find((int) $key);
                $defectRows[] = [
                    'kategori'    => '-',
                    'jenis'       => $def->jenis_defect ?? ('Defect #' . $key),
                    'sub_jenis'   => '-',
                    'spesifikasi' => '-',
                    'qty'         => $qty,
                ];
                continue;
            }

            $k    = $norm($key);
            $meta = $lookup[$k] ?? $legacy[$k] ?? null;

            if (!$meta) {
                // Fallback: master sudah berubah/dihapus, tampilkan apa adanya dari key
                $parts = explode(' - ', (string) $key, 2);
                $meta = [
                    'kategori'    => '-',
                    'jenis'       => trim($parts[0]),
                    'sub_jenis'   => '-',
                    'spesifikasi' => isset($parts[1]) ? trim($parts[1]) : '-',
                ];
            }
            $defectRows[] = $meta + ['qty' => $qty];
        }

        return view('qc-sistem.shoestring.show', compact('shoestring', 'qtyMap', 'defectRows'));
    }

    public function edit($uuid)
    {
        $shoestring = Shoestring::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        if ($redirect = $this->redirectToCanonicalAuditUrl('shoestring.edit', $shoestring)) {
            return $redirect;
        }

        // Check edit time limit using global config
        $user = auth()->user();
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($shoestring->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('shoestring.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        $produsens = DataDefect::filterByRole()->pluck('nama_produsen')->unique()->values();
        $dataDefect = DataDefect::filterByRole()->get();
        return view('qc-sistem.shoestring.edit', compact('shoestring', 'shifts', 'plans', 'produsens', 'dataDefect'));
    }

    public function getDefectByProdusen(Request $request)
    {
        $request->validate([
            'nama_produsen' => 'required|string',
        ]);
        
        $defect = DataDefect::filterByRole()
            ->where('nama_produsen', $request->nama_produsen)
            ->first();

        if (!$defect) {
            return response()->json(['success' => false, 'message' => 'Produsen tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'detail_defect' => $defect->detail_defect
        ]);
    }

    public function update(Request $request, $uuid)
    {
        $shoestring = Shoestring::where('uuid', $uuid)->firstOrFail();

        // Check edit time limit using global config
        $user = auth()->user();
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($shoestring->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('shoestring.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'shift_id' => 'required',
            'nama_produsen' => 'required|string',
            'kode_produksi' => 'required|string',
            'tgl_exp' => 'required|string',
            'berat_sampling' => 'nullable|string',
            'sampling_defect_qty' => 'nullable|array',
            'catatan' => 'nullable|string',
            'dokumentasi_base64' => 'nullable|array',
            'dokumentasi_base64.*' => 'string',
        ]);

        $user = auth()->user();
        
         // 1. Process sampling_defect_qty mapping
        $qtyMap = [];
        $totalDefect = 0;
        $filledDefects = [];

        if (!empty($request->sampling_defect_qty)) {
            foreach ($request->sampling_defect_qty as $defectKey => $qtyVal) {
                if ($qtyVal !== null && $qtyVal !== '' && $qtyVal !== 'null') {
                    $qtyMap[$defectKey] = (string)$qtyVal;
                    $totalDefect += (float)$qtyVal;
                    
                    // Get name of defect for sampling_defect string log
                    if (is_numeric($defectKey)) {
                        $def = \App\Models\DataDefect::find($defectKey);
                        if ($def) {
                            $filledDefects[] = $def->jenis_defect;
                        } else {
                            $filledDefects[] = "Defect #" . $defectKey;
                        }
                    } else {
                        $filledDefects[] = $defectKey;
                    }
                }
            }
        }

        $samplingDefectNamesStr = !empty($filledDefects) ? Str::limit(implode(', ', $filledDefects), 250, '...') : null;


        // 3. Process base64 documentation
        $dokumentasiPaths = $shoestring->dokumentasi ?? [];
        if (!empty($request->dokumentasi_base64)) {
            foreach ($request->dokumentasi_base64 as $base64Image) {
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $fileData = substr($base64Image, strpos($base64Image, ',') + 1);
                    $type = strtolower($type[1]);
                    if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                        continue;
                    }
                    $fileData = base64_decode($fileData);
                    if ($fileData === false) {
                        continue;
                    }
                    $filename = 'shoestring_docs/' . Str::uuid() . '.' . $type;
                    \Storage::disk('public')->put($filename, $fileData);
                    $dokumentasiPaths[] = $filename;
                }
            }
        }

        $shoestring->update([
            'tanggal' => Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s'),
            'jam' => $request->jam,
            'shift_id' => $request->shift_id,
            'nama_produsen' => $request->nama_produsen,
            'kode_produksi' => $request->kode_produksi,
            'best_before' => $request->tgl_exp,
            'berat_sampling' => $request->berat_sampling,
            'sampling_defect' => $samplingDefectNamesStr,
            'sampling_defect_qty' => !empty($qtyMap) ? $qtyMap : null,
            'total_defect' => $totalDefect > 0 ? (string)$totalDefect : null,
            'catatan' => $request->catatan,
            'dokumentasi' => !empty($dokumentasiPaths) ? array_values($dokumentasiPaths) : null,
        ]);

        return redirect()->route('shoestring.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($uuid)
    {
        $shoestring = Shoestring::where('uuid', $uuid)->firstOrFail();
        $shoestring->delete();
        return redirect()->route('shoestring.index')->with('success', 'Data berhasil dihapus');
    }

    public function showLogs($uuid)
    {
        $item = Shoestring::where('uuid', $uuid)->firstOrFail();
        $logs = ShoestringLog::where('shoestring_id', $item->id)->with('user')->orderBy('created_at', 'desc')->paginate(10);
        return view('qc-sistem.shoestring.logs', compact('item', 'logs'));
    }

    public function getLogsJson($uuid)
    {
        $item = Shoestring::where('uuid', $uuid)->firstOrFail();
        $logs = ShoestringLog::where('shoestring_id', $item->id)->with('user')->orderBy('created_at', 'desc')->get();
        return response()->json($logs);
    }

    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal_mulai' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
                'shift_id'      => 'nullable|integer',
                'kode_form'     => 'required|string|max:50',
            ]);

            $user = auth()->user();

            $query = Shoestring::with(['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover'])
                ->where('is_audit', $request->boolean('is_audit', false))
                ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan));

            $query->whereDate('tanggal', '>=', $request->tanggal_mulai)
                  ->whereDate('tanggal', '<=', $request->tanggal_akhir);

            if ($request->shift_id) {
                $query->where('shift_id', $request->shift_id);
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
                'shift_id'      => $request->shift_id,
                'kode_form'     => $request->kode_form,
            ];

            $kode_form = $request->kode_form;
            $pdf = Pdf::loadView('qc-sistem.shoestring.export_pdf', compact('data', 'kode_form', 'filters'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download('shoestring_' . date('Y-m-d_H-i-s') . '.pdf');

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengekspor PDF: ' . $e->getMessage()], 500);
        }
    }

    public function saveKode(Request $request)
    {
        $item = Shoestring::where('uuid', $request->uuid)->firstOrFail();
        $item->update(['kode_form' => $request->kode_form]);
        return response()->json(['success' => true]);
    }

    public function approve(Request $request, $uuid)
    {
        $item = Shoestring::where('uuid', $uuid)->firstOrFail();
        $type = $request->type; // qc, produksi, spv
        $user = auth()->user();
        
        $item->update([
            'approved_by_' . $type => true,
            $type . '_approved_by' => $user->id,
            $type . '_approved_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function downloadTemplate()
    {
        return Excel::download(new ShoestringTemplateExport(auth()->user()), 'template_shoestring.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        Excel::import(new ShoestringImport(auth()->user()), $request->file('file'));
        return redirect()->route('shoestring.index')->with('success', 'Data berhasil diimport');
    }

    public function bulkApprove(Request $request)
    {
        try {
            $user = auth()->user();
            $type = $request->input('type');
            $tanggal = $request->input('tanggal');
            $shift_id = $request->input('shift_id');

            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5]
            ];

            if (!in_array($user->id_role, $allowedRoles[$type] ?? [])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $query = Shoestring::whereDate('tanggal', $tanggal)->where('shift_id', $shift_id);
            $items = $query->get();

            if ($type === 'produksi' && $items->contains(fn($i) => !$i->approved_by_qc)) {
                return response()->json(['success' => false, 'message' => 'QC approval required'], 422);
            }
            if ($type === 'spv' && $items->contains(fn($i) => !$i->approved_by_produksi)) {
                return response()->json(['success' => false, 'message' => 'Produksi approval required'], 422);
            }

            $query->update([
                'approved_by_' . $type => true,
                $type . '_approved_by' => $user->id,
                $type . '_approved_at' => now(),
            ]);

            foreach ($items as $item) {
                ShoestringLog::create([
                    'uuid' => Str::uuid(),
                    'shoestring_id' => $item->id,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role ?? 'Unknown',
                    'aksi' => 'bulk_approval',
                    'keterangan' => 'Bulk Approved by ' . strtoupper($type)
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Multi Approval Checkbox-Based (Per-Row Selection).
     *
     * Fitur:
     * - Menggunakan centang checkbox di setiap baris tabel.
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
            $query = Shoestring::whereIn('uuid', $uuids)
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

            Shoestring::whereIn('uuid', $uuids)
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
                ShoestringLog::create([
                    'uuid' => (string) Str::uuid(),
                    'shoestring_id' => $item->id,
                    'shoestring_uuid' => $item->uuid,
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
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk export Excel with filters
     */
    public function bulkExportExcel(Request $request)
    {
        return (new ShoestringBulkExportService())->handle($request, fn($msg) => $this->noDataHtmlResponse($msg));
    }

    /**
     * Get import model class (for HasExcelImport trait)
     */
    protected function getImportModelClass(): string
    {
        return Shoestring::class;
    }

    /**
     * Get template filename (for HasExcelImport trait)
     */
    protected function getTemplateFilename(): string
    {
        return 'Shoestring_Template_' . date('YmdHis') . '.xlsx';
    }
}
