<?php

namespace App\Http\Controllers;

use App\Models\PersiapanBahanBetter;
use App\Models\AktualBetter;
use App\Models\Plan;
use App\Models\JenisProduk;
use App\Models\JenisBetter;
use App\Models\StdSalinitasViskositas;
use App\Models\PersiapanBahanBetterLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Traits\HasAuditController;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Services\BulkExport\PersiapanBahanBetterBulkExportService;

class PersiapanBahanBetterController extends Controller
{
    use HasAuditController, HasNoDataHtmlResponse;

    protected function auditModel(): string
    {
        return PersiapanBahanBetter::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'persiapan-bahan-better';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.persiapan_bahan_better';
    }

    protected function auditRelations(): array
    {
        return ['plan', 'produk', 'better', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover', 'originalVersion'];
    }

    protected function auditSearchColumns(): array
    {
        return ['kode_produksi_produk', 'kode_produksi_better'];
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
            return redirect()->route('persiapan-bahan-better.audit');
        }
        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        $query = PersiapanBahanBetter::with(['user', 'plan', 'produk', 'better', 'aktuals.std', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->operasional()
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('produk', function ($produkQuery) use ($search) {
                    $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                })->orWhere('kode_produksi_produk', 'like', '%' . $search . '%');
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->paginate($perPage);

        return view('qc-sistem.persiapan_bahan_better.index', compact('records', 'search', 'perPage'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
            $betters = JenisBetter::all();
            $shifts = \App\Models\DataShift::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $betters = JenisBetter::where('id_plan', $user->id_plan)->get();
            $shifts = \App\Models\DataShift::where('id_plan', $user->id_plan)->get();
        }
        return view('qc-sistem.persiapan_bahan_better.create', compact('plans', 'produks', 'betters', 'shifts'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_better' => 'required|exists:jenis_better,id',
            'kode_produksi_produk' => 'required|string',
            'kode_form' => 'nullable|string',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'gramase' => 'required|numeric|min:0',
            'better_rows' => 'required|array|min:1',
            'better_rows.*.master_nama_formula_better' => 'nullable|string',
            'better_rows.*.master_berat' => 'required|numeric',
            'better_rows.*.kode_produksi_better' => 'nullable|string',
            'better_rows.*.suhu_air' => 'required|numeric',
            'better_rows.*.sensori' => 'nullable|string',
            'aktual_vis' => 'required|array',
            'aktual_sal' => 'required|array',
            'aktual_suhu_air' => 'required|array',
            'id_std_salinitas_viskositas' => 'required|array',
        ]);

        $rows = $data['better_rows'] ?? [];
        $totalBeratBetter = 0;
        $sumSuhuAir = 0;
        $countSuhuAir = 0;

        foreach ($rows as $r) {
            $b = $r['master_berat'] ?? null;
            if ($b !== null && $b !== '') {
                $n = is_numeric($b) ? (float) $b : (float) preg_replace('/[^0-9.\-]/', '', (string) $b);
                if (!is_nan($n)) {
                    $totalBeratBetter += $n;
                }
            }
            $suhu = $r['suhu_air'] ?? null;
            if ($suhu !== null && $suhu !== '' && is_numeric($suhu)) {
                $sumSuhuAir += (float) $suhu;
                $countSuhuAir++;
            }
        }

        // Pastikan field berat_air tidak tersimpan lagi di JSON rows (kompatibilitas data lama)
        foreach ($rows as $idx => $r) {
            if (is_array($r) && array_key_exists('berat_air', $r)) {
                unset($r['berat_air']);
                $rows[$idx] = $r;
            }
        }

        $firstRow = $rows[0] ?? [];
        $data['better_rows'] = $rows;
        $data['berat_better'] = $totalBeratBetter;
        // gramase langsung dari input form
        $data['gramase'] = $request->input('gramase');
        $data['suhu_air'] = $countSuhuAir > 0 ? ($sumSuhuAir / $countSuhuAir) : null;
        $data['sensori'] = $firstRow['sensori'] ?? null;
        $data['kode_produksi_better'] = (isset($firstRow['kode_produksi_better']) && $firstRow['kode_produksi_better'] !== '') ? $firstRow['kode_produksi_better'] : '-';

        $data['user_id'] = $user->id;
        $data['id_plan'] = $user->id_plan;
        // Normalisasi format tanggal ke Y-m-d H:i:s untuk database
        $data['tanggal'] = Carbon::parse($data['tanggal'])->setTimeFrom(now());
        // Simpan jam
        $data['jam'] = $data['jam'];
        // Simpan suhu_air ke tabel persiapan_bahan_better
        $persiapan = PersiapanBahanBetter::create($data);

        foreach ($data['id_std_salinitas_viskositas'] as $i => $id_std) {
            AktualBetter::create([
                'id_std_salinitas_viskositas' => $id_std,
                'id_persiapan_bahan_better' => $persiapan->id,
                'aktual_vis' => $data['aktual_vis'][$i],
                'aktual_sal' => $data['aktual_sal'][$i],
                'aktual_suhu_air' => $data['aktual_suhu_air'][$i],
            ]);
        }

        return redirect()->route('persiapan-bahan-better.index')->with('success', 'Data berhasil disimpan');
    }

    public function show($uuid)
    {
        $user = auth()->user();
        $item = PersiapanBahanBetter::where('uuid', $uuid)->with(['aktuals.std', 'shift', 'produk', 'better'])->firstOrFail();
        
        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan && !$item->is_audit) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('persiapan-bahan-better.show', $item)) {
            return $redirect;
        }

        return view('qc-sistem.persiapan_bahan_better.show', compact('item'));
    }

    public function edit($uuid)
    {
        $user = auth()->user();
        $item = PersiapanBahanBetter::where('uuid', $uuid)->with(['aktuals.std', 'shift'])->firstOrFail();

        if ($redirect = $this->redirectToCanonicalAuditUrl('persiapan-bahan-better.edit', $item)) {
            return $redirect;
        }

        // Cek batas waktu edit untuk role yang dibatasi (global config)
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($item->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('persiapan-bahan-better.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
            $betters = JenisBetter::all();
            $shifts = \App\Models\DataShift::all();
        } else {
            $targetPlan = $item->is_audit ? $item->id_plan : $user->id_plan;
            $plans = Plan::where('id', $targetPlan)->get();
            $produks = JenisProduk::where('id_plan', $targetPlan)->get();
            $betters = JenisBetter::where('id_plan', $targetPlan)->get();
            $shifts = \App\Models\DataShift::where('id_plan', $targetPlan)->get();
        }
        return view('qc-sistem.persiapan_bahan_better.edit', compact('item', 'plans', 'produks', 'betters', 'shifts'));
    }

    public function update(Request $request, $uuid)
    {
        $user = auth()->user();
        $itemCheck = PersiapanBahanBetter::where('uuid', $uuid)->firstOrFail();

        // Cek batas waktu edit untuk role yang dibatasi (global config)
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($itemCheck->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('persiapan-bahan-better.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        $data = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_better' => 'required|exists:jenis_better,id',
            'kode_produksi_produk' => 'required|string',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            //tambah field jam
            'jam' => 'required|date_format:H:i',
            'gramase' => 'required|numeric|min:0',
            'id_std_salinitas_viskositas' => 'nullable|array',
            'aktual_vis' => 'nullable|array',
            'aktual_sal' => 'nullable|array',
            'aktual_suhu_air' => 'nullable|array',
            'better_rows' => 'required|array|min:1',
            'better_rows.*.master_nama_formula_better' => 'nullable|string',
            'better_rows.*.master_berat' => 'required|numeric',
            'better_rows.*.kode_produksi_better' => 'nullable|string',
            'better_rows.*.suhu_air' => 'required|numeric',
            'better_rows.*.sensori' => 'nullable|string',
        ]);

        $rows = $data['better_rows'] ?? [];
        $totalBeratBetter = 0;
        $sumSuhuAir = 0;
        $countSuhuAir = 0;

        foreach ($rows as $r) {
            $b = $r['master_berat'] ?? null;
            if ($b !== null && $b !== '') {
                $n = is_numeric($b) ? (float) $b : (float) preg_replace('/[^0-9.\-]/', '', (string) $b);
                if (!is_nan($n)) {
                    $totalBeratBetter += $n;
                }
            }
            $suhu = $r['suhu_air'] ?? null;
            if ($suhu !== null && $suhu !== '' && is_numeric($suhu)) {
                $sumSuhuAir += (float) $suhu;
                $countSuhuAir++;
            }
        }

        // Pastikan field berat_air tidak tersimpan lagi di JSON rows (kompatibilitas data lama)
        foreach ($rows as $idx => $r) {
            if (is_array($r) && array_key_exists('berat_air', $r)) {
                unset($r['berat_air']);
                $rows[$idx] = $r;
            }
        }

        $firstRow = $rows[0] ?? [];
        $data['better_rows'] = $rows;
        $data['berat_better'] = $totalBeratBetter;
        // gramase langsung dari input form
        $data['gramase'] = $request->input('gramase');
        $data['suhu_air'] = $countSuhuAir > 0 ? ($sumSuhuAir / $countSuhuAir) : null;
        $data['sensori'] = $firstRow['sensori'] ?? null;
        $data['kode_produksi_better'] = $firstRow['kode_produksi_better'] ?? null;

        // user_id TIDAK diupdate — tetap menggunakan pembuat data asli
        $data['id_plan'] = $user->id_plan;
        // Normalisasi tanggal
        $data['tanggal'] = Carbon::parse($data['tanggal'])->setTimeFrom(now());

        // Update field jam 
        $data['jam'] = $request->jam;
        $persiapan = PersiapanBahanBetter::where('uuid', $uuid)->firstOrFail();
        $persiapan->update($data);

        // Hapus data aktual lama dan insert baru hanya jika ada input aktual
        if ($request->has('aktual_vis') && is_array($request->aktual_vis) && count($request->aktual_vis) > 0) {
            AktualBetter::where('id_persiapan_bahan_better', $persiapan->id)->delete();

            foreach ($data['id_std_salinitas_viskositas'] as $i => $id_std) {
                AktualBetter::create([
                    'id_std_salinitas_viskositas' => $id_std,
                    'id_persiapan_bahan_better' => $persiapan->id,
                    'aktual_vis' => $data['aktual_vis'][$i],
                    'aktual_sal' => $data['aktual_sal'][$i],
                    'aktual_suhu_air' => $data['aktual_suhu_air'][$i],
                ]);
            }
        }

        return redirect()->route('persiapan-bahan-better.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $persiapan = PersiapanBahanBetter::where('uuid', $uuid)->firstOrFail();
        AktualBetter::where('id_persiapan_bahan_better', $persiapan->id)->delete();
        $persiapan->delete();
        return redirect()->route('persiapan-bahan-better.index')->with('success', 'Data berhasil dihapus');
    }

    /**
     * Menampilkan riwayat log perubahan data
     */
    public function showLogs($uuid)
    {
        $item = PersiapanBahanBetter::where('uuid', $uuid)->firstOrFail();
        
        $logs = PersiapanBahanBetterLog::where('persiapan_bahan_better_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->paginate(5);
        
        return view('qc-sistem.persiapan_bahan_better.logs', compact('item', 'logs'));
    }

    /**
     * API untuk mendapatkan log dalam format JSON (untuk AJAX)
     */
    public function getLogsJson($uuid)
    {
        $item = PersiapanBahanBetter::where('uuid', $uuid)->firstOrFail();
        
        $logs = PersiapanBahanBetterLog::where('persiapan_bahan_better_id', $item->id)
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

    public function bulkExportExcel(Request $request, PersiapanBahanBetterBulkExportService $service)
    {
        return $service->export($request);
    }

    /**
     * Export data to PDF with filters
     */
    public function exportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal_mulai' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
                'shift_filter' => 'nullable|exists:data_shift,id',
                'produk_filter' => 'nullable|exists:jenis_produk,id',
                'kode_form' => 'required|string|max:50',
            ]);

            $user = auth()->user();

            $query = PersiapanBahanBetter::with(['user', 'plan', 'produk', 'better', 'aktuals.std', 'shift'])
                ->where('is_audit', $request->boolean('is_audit', false))
                ->when($user->role !== 'superadmin', function ($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            $query->whereDate('tanggal', '>=', $request->tanggal_mulai)
                  ->whereDate('tanggal', '<=', $request->tanggal_akhir);

            if ($request->shift_filter) {
                $query->where('shift_id', $request->shift_filter);
            }

            if ($request->produk_filter) {
                $query->where('id_produk', $request->produk_filter);
            }

            $data = $query->orderBy('tanggal', 'asc')->orderBy('jam', 'asc')->get();

            if ($data->isNotEmpty()) {
                $query->update(['kode_form' => $request->kode_form]);
            }

            if ($data->isEmpty()) {
                $filterInfo = [];
                $filterInfo[] = 'Periode: ' . Carbon::parse($request->tanggal_mulai)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
                if ($request->shift_filter) {
                    $shift = \App\Models\DataShift::find($request->shift_filter);
                    $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
                }
                if ($request->produk_filter) {
                    $produk = JenisProduk::find($request->produk_filter);
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
                'produk' => $request->produk_filter ? JenisProduk::find($request->produk_filter)->nama_produk : 'Semua Produk',
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir,
                'shift_id' => $request->shift_filter,
                'shift' => $request->shift_filter ? \App\Models\DataShift::find($request->shift_filter)->shift : 'Semua Shift'
            ];

            $kode_form = $request->kode_form;
            $pdfData = [
                'data' => $data,
                'kode_form' => $kode_form,
                'filters' => $filters,
                'exported_at' => Carbon::now()->format('d/m/Y H:i:s'),
                'exported_by' => $user->name
            ];

            $pdf = Pdf::loadView('qc-sistem.persiapan_bahan_better.export_pdf', $pdfData);
            $pdf->setPaper('A4', 'landscape');

            $safeKodeForm = str_replace(['/', '\\', ':'], '-', (string) $request->kode_form);
            $safeKodeForm = trim($safeKodeForm);
            $filename = 'Persiapan_Bahan_Better_' . $safeKodeForm . '_' . Carbon::now()->format('YmdHis') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle approval requests
     */
    public function approve(Request $request, $uuid)
    {
        try {
            $user = auth()->user();
            $type = $request->input('type'); // qc, produksi, spv

            // Validate approval type
            if (!in_array($type, ['qc', 'produksi', 'spv'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe persetujuan tidak valid.'
                ], 400);
            }

            // Find the record
            $persiapanBahanBetter = PersiapanBahanBetter::where('uuid', $uuid)->firstOrFail();

            // Role-based authorization
            $userRole = $user->id_role;
            $approvalField = "approved_by_{$type}";

            // Check if user has permission for this approval type
            if (!$this->canApprove($userRole, $type)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melakukan persetujuan ini.'
                ], 403);
            }

            // Check if already approved
            if ($persiapanBahanBetter->{$approvalField}) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui sebelumnya.'
                ], 400);
            }

            // Sequential approval validation
            if ($type === 'produksi' && !$persiapanBahanBetter->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
                ], 400);
            }

            if ($type === 'spv' && !$persiapanBahanBetter->approved_by_produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
                ], 400);
            }

            // Update approval
            $persiapanBahanBetter->update([
                $approvalField => true,
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now()
            ]);

            // Log the approval activity
            PersiapanBahanBetterLog::create([
                'persiapan_bahan_better_id' => $persiapanBahanBetter->id,
                'persiapan_bahan_better_uuid' => $persiapanBahanBetter->uuid,
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
                'data' => $persiapanBahanBetter->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkApprove(Request $request)
    {
        $user = auth()->user();
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'id_shift' => 'required|exists:data_shift,id',
            'type' => 'required|in:qc,produksi,spv',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $query = PersiapanBahanBetter::whereDate('tanggal', $request->tanggal)
                ->where('shift_id', $request->id_shift);

            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }

            $items = $query->get();

            if ($items->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data untuk disetujui.'], 404);
            }

            $userRole = $user->id_role ?? null;
            $type = $request->type;
            $approvedCount = 0;

            foreach ($items as $item) {
                $approvalField = "approved_by_{$type}";
                if ($item->{$approvalField}) continue;

                $canApprove = false;
                if ($type === 'qc') {
                    $canApprove = in_array($userRole, [1, 3, 5]);
                } elseif ($type === 'produksi') {
                    $canApprove = in_array($userRole, [1, 2, 5]) && $item->approved_by_qc;
                } elseif ($type === 'spv') {
                    $canApprove = in_array($userRole, [1, 4, 5]) && $item->approved_by_produksi;
                }

                if ($canApprove) {
                    $item->update([
                        $approvalField => true,
                        "{$type}_approved_by" => $user->id,
                        "{$type}_approved_at" => now()
                    ]);
                    $approvedCount++;

                    PersiapanBahanBetterLog::create([
                        'persiapan_bahan_better_id' => $item->id,
                        'persiapan_bahan_better_uuid' => $item->uuid,
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_role' => $user->id_role,
                        'aksi' => 'bulk_approve',
                        'field_yang_diubah' => [$approvalField],
                        'nilai_lama' => [$approvalField => false],
                        'nilai_baru' => [$approvalField => true],
                        'keterangan' => "Bulk approval {$type} by {$user->name}",
                        'ip_address' => $request->ip()
                    ]);
                }
            }

            return response()->json([
                'success' => true, 
                'message' => "Berhasil menyetujui $approvedCount data."
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function searchAjax(Request $request)
    {
        try {
            $user = auth()->user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 5);
            $page = (int) $request->get('page', 1);

            $query = PersiapanBahanBetter::with(['user', 'plan', 'produk', 'better', 'aktuals.std', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover'])
                ->operasional();

            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('produk', function ($produkQuery) use ($search) {
                        $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                    })->orWhere('kode_produksi_produk', 'like', '%' . $search . '%');
                });
            }

            $records = $query->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'html' => view('qc-sistem.persiapan_bahan_better._table', compact('records'))->render(),
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

    public function bulkApproveSelected(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'type' => 'required|in:qc,produksi,spv',
            'uuids' => 'required|array|min:1',
            'uuids.*' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
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

            $query = PersiapanBahanBetter::whereIn('uuid', $uuids);

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

                PersiapanBahanBetterLog::create([
                    'persiapan_bahan_better_id' => $record->id,
                    'persiapan_bahan_better_uuid' => $record->uuid,
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

    /**
     * Check if user can approve based on role and approval type
     */
    private function canApprove($userRole, $type)
    {
        $permissions = [
            'qc' => [1, 3, 5],
            'produksi' => [1, 2, 5],
            'spv' => [1, 4, 5]
        ];
        
        return isset($permissions[$type]) && in_array($userRole, $permissions[$type]);
    }

    public function auditIndex(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        $auditedSourceUuids = PersiapanBahanBetter::where('is_audit', true)->whereNotNull('source_uuid')->pluck('source_uuid')->toArray();

$query = PersiapanBahanBetter::with($this->auditRelations())
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
            $query->where(function ($q) use ($search) {
                $q->where('kode_produksi_produk', 'like', "%{$search}%")
                  ->orWhere('kode_produksi_better', 'like', "%{$search}%")
                  ->orWhereHas('produk', function ($produkQuery) use ($search) {
                      $produkQuery->where('nama_produk', 'like', "%{$search}%");
                  });
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->paginate($perPage);

        return view('qc-sistem.persiapan_bahan_better.index_audit', compact('records', 'search', 'perPage'));
    }

    public function auditSearchAjax(Request $request)
    {
        try {
            $user = auth()->user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 5);
            $page = (int) $request->get('page', 1);

            $query = PersiapanBahanBetter::with($this->auditRelations())
                ->where('is_audit', true)
                ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan));

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('kode_produksi_produk', 'like', "%{$search}%")
                      ->orWhere('kode_produksi_better', 'like', "%{$search}%")
                      ->orWhereHas('produk', fn($sq) => $sq->where('nama_produk', 'like', "%{$search}%"));
                });
            }

            $records = $query->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $isAuditPage = true;

            return response()->json([
                'html' => view('qc-sistem.persiapan_bahan_better._table', compact('records', 'isAuditPage'))->render(),
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