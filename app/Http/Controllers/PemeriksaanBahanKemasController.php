<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanBahanKemas;
use App\Models\PemeriksaanBahanKemasLog;
use App\Models\DataShift;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PemeriksaanBahanKemasTemplateExport;
use App\Imports\PemeriksaanBahanKemasImport;
use App\Http\Controllers\Traits\HasAuditController;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Http\Controllers\Traits\HasExcelImport;
use App\Services\BulkExport\PemeriksaanBahanKemasBulkExportService;

class PemeriksaanBahanKemasController extends Controller
{
    use HasAuditController, HasNoDataHtmlResponse, HasExcelImport;

    protected function auditModel(): string { return PemeriksaanBahanKemas::class; }
    protected function auditRoutePrefix(): string { return 'pemeriksaan-bahan-kemas'; }
    protected function auditViewPrefix(): string { return 'qc-sistem.pemeriksaan_bahan_kemas'; }
    protected function auditRelations(): array
    {
        return ['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'createdBy', 'originalVersion'];
    }
    protected function auditSearchColumns(): array { return ['nama_kemasan', 'kode_produksi', 'catatan']; }
    protected function auditDateColumn(): string { return 'tanggal'; }
    protected function auditTimeColumn(): string { return 'jam'; }
    protected function auditShiftRelation(): string { return 'shift'; }
    protected function auditShiftColumn(): string { return 'shift_id'; }
    public function index(Request $request)
    {
        $user = Auth::user();
        // Auditor langsung diarahkan ke halaman audit
        if ($user->id_role == 6) {
            return redirect()->route('pemeriksaan-bahan-kemas.audit');
        }
        $search = trim((string) $request->get('search', ''));
        $perPage = (int) $request->get('per_page', 5);

        $query = PemeriksaanBahanKemas::with(['user', 'plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover','auditVersion'])
            ->operasional()
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('nama_kemasan', 'LIKE', '%' . $search . '%')
                  ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%')
                  ->orWhere('keterangan', 'LIKE', '%' . $search . '%');
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

        return view('qc-sistem.pemeriksaan_bahan_kemas.index', compact('records', 'search', 'perPage'));
    }

    public function searchAjax(Request $request)
    {
        try {
            $user = Auth::user();
            $search = trim((string) $request->get('search', ''));
            $perPage = (int) $request->get('per_page', 5);
            $page = (int) $request->get('page', 1);

            $query = PemeriksaanBahanKemas::with(['user', 'plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover']);

            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }

            if ($search !== '') {
                $query->where(function($q) use ($search) {
                    $q->where('nama_kemasan', 'LIKE', '%' . $search . '%')
                      ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%')
                      ->orWhere('keterangan', 'LIKE', '%' . $search . '%');
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
                'html' => view('qc-sistem.pemeriksaan_bahan_kemas._table', compact('records'))->render(),
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
        $user = Auth::user();

        if ($user->role === 'superadmin') {
            $shifts = DataShift::with('plan')->get();
            $plans = Plan::all();
        } else {
            $shifts = DataShift::with('plan')->where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('qc-sistem.pemeriksaan_bahan_kemas.create', compact('shifts', 'plans'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.jam' => 'required|date_format:H:i',
            'items.*.nama_kemasan' => 'required|string|max:255',
            'items.*.kode_produksi' => 'required|string|max:255',
            'items.*.kondisi_bahan_kemasan' => 'required|in:OK,Tidak OK',
            'items.*.keterangan' => 'nullable|string|max:1000',
        ]);

        $selectedShift = DataShift::findOrFail($request->shift_id);

        if ($user->role !== 'superadmin' && (int) $selectedShift->id_plan !== (int) $user->id_plan) {
            return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.'])->withInput();
        }

        $effectivePlanId = ($user->role === 'superadmin') ? $selectedShift->id_plan : $user->id_plan;

        foreach ((array) $request->items as $row) {
            PemeriksaanBahanKemas::create([
                'user_id' => $user->id,
                'id_plan' => $effectivePlanId,
                'shift_id' => $request->shift_id,
                'tanggal' => Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s'),
                'jam' => $row['jam'] ?? null,
                'nama_kemasan' => $row['nama_kemasan'] ?? null,
                'kode_produksi' => $row['kode_produksi'] ?? null,
                'kondisi_bahan_kemasan' => $row['kondisi_bahan_kemasan'] ?? null,
                'keterangan' => $row['keterangan'] ?? null,
            ]);
        }

        return redirect()->route('pemeriksaan-bahan-kemas.index')
            ->with('success', 'Data pemeriksaan bahan kemas berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $item = PemeriksaanBahanKemas::with(['user', 'plan', 'shift', 'verifikasiKedatanganBy'])->where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('pemeriksaan-bahan-kemas.show', $item)) {
            return $redirect;
        }

        return view('qc-sistem.pemeriksaan_bahan_kemas.show', compact('item'));
    }

    public function storeVerifikasiKedatangan(Request $request, $uuid)
    {
        $user = Auth::user();
        $item = PemeriksaanBahanKemas::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $allowedRoles = [1, 3, 5];

        if ($user->role !== 'superadmin' && !in_array((int) $user->id_role, $allowedRoles, true)) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan verifikasi ini.');
        }

        $validated = $request->validate([
            'verifikasi_kedatangan' => 'required|in:OK,NOT_OK',
        ]);

        $previousStatus = $item->verifikasi_kedatangan;

        $item->update([
            'verifikasi_kedatangan' => $validated['verifikasi_kedatangan'],
            'verifikasi_kedatangan_by' => $user->id,
            'verifikasi_kedatangan_at' => now(),
        ]);

        PemeriksaanBahanKemasLog::create([
            'pemeriksaan_bahan_kemas_id' => $item->id,
            'pemeriksaan_bahan_kemas_uuid' => $item->uuid,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'aksi' => 'verifikasi_kedatangan',
            'field_yang_diubah' => ['verifikasi_kedatangan'],
            'nilai_lama' => ['verifikasi_kedatangan' => $previousStatus],
            'nilai_baru' => ['verifikasi_kedatangan' => $validated['verifikasi_kedatangan']],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'keterangan' => 'Verifikasi kedatangan bahan kemas oleh QC',
        ]);

        return redirect()->route('pemeriksaan-bahan-kemas.show', $item->uuid)
            ->with('success', 'Verifikasi kedatangan bahan kemas berhasil disimpan.');
    }

    public function showLogs($uuid)
    {
        $user = Auth::user();

        $item = PemeriksaanBahanKemas::where('uuid', $uuid)
            ->with(['shift', 'plan'])
            ->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403, 'Unauthorized access to logs');
        }

        $logs = PemeriksaanBahanKemasLog::where('pemeriksaan_bahan_kemas_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('qc-sistem.pemeriksaan_bahan_kemas.logs', compact('item', 'logs'));
    }

    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal_mulai' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
                'shift_id' => 'nullable|integer',
                'kode_form' => 'required|string|max:50',
            ]);

            $user = auth()->user();

            $query = PemeriksaanBahanKemas::with(['plan', 'shift', 'user'])
                ->where('is_audit', $request->boolean('is_audit', false))
                ->when($user->role !== 'superadmin', function ($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

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
                'shift_id' => $request->shift_id,
                'kode_form' => $request->kode_form,
            ];

            $kode_form = $request->kode_form;
            $pdf = Pdf::loadView('qc-sistem.pemeriksaan_bahan_kemas.export_pdf', compact('data', 'kode_form', 'filters'));
            $pdf->setPaper('A4', 'landscape');

            $filename = 'pemeriksaan_bahan_kemas_' . date('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($uuid)
    {
        $item = PemeriksaanBahanKemas::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('pemeriksaan-bahan-kemas.edit', $item)) {
            return $redirect;
        }

        // Cek batas waktu edit untuk role yang dibatasi (global config)
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($item->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('pemeriksaan-bahan-kemas.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        if ($user->role === 'superadmin') {
            $shifts = DataShift::with('plan')->get();
            $plans = Plan::all();
        } else {
            $shifts = DataShift::with('plan')->where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('qc-sistem.pemeriksaan_bahan_kemas.edit', compact('item', 'shifts', 'plans'));
    }

    public function update(Request $request, $uuid)
    {
        $item = PemeriksaanBahanKemas::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Cek batas waktu edit untuk role yang dibatasi (global config)
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($item->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('pemeriksaan-bahan-kemas.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'nama_kemasan' => 'required|string|max:255',
            'kode_produksi' => 'required|string|max:255',
            'kondisi_bahan_kemasan' => 'required|in:OK,Tidak OK',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $selectedShift = DataShift::findOrFail($request->shift_id);

        if ($user->role !== 'superadmin' && (int) $selectedShift->id_plan !== (int) $user->id_plan) {
            return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.'])->withInput();
        }

        $effectivePlanId = ($user->role === 'superadmin') ? $selectedShift->id_plan : $user->id_plan;

        $item->update([
            'id_plan' => $effectivePlanId,
            'shift_id' => $request->shift_id,
            'tanggal' => Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s'),
            'jam' => $request->jam,
            'nama_kemasan' => $request->nama_kemasan,
            'kode_produksi' => $request->kode_produksi,
            'kondisi_bahan_kemasan' => $request->kondisi_bahan_kemasan,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('pemeriksaan-bahan-kemas.index')
            ->with('success', 'Data pemeriksaan bahan kemas berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $item = PemeriksaanBahanKemas::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $item->delete();

        return redirect()->route('pemeriksaan-bahan-kemas.index')
            ->with('success', 'Data pemeriksaan bahan kemas berhasil dihapus.');
    }

    public function approve(Request $request, $uuid)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv',
            ]);

            $user = Auth::user();
            $userRole = $user->id_role ?? null;
            $type = $request->type;

            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5],
            ];

            if (!isset($allowedRoles[$type]) || !in_array($userRole, $allowedRoles[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melakukan approval ini',
                ], 403);
            }

            $item = PemeriksaanBahanKemas::where('uuid', $uuid)->firstOrFail();

            if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $approvalField = 'approved_by_' . $type;
            if ($item->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui oleh ' . strtoupper($type),
                ], 400);
            }

            if ($type === 'produksi' && !$item->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh QC terlebih dahulu',
                ], 400);
            }

            if ($type === 'spv' && !$item->approved_by_produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh Produksi terlebih dahulu',
                ], 400);
            }

            $updateData = [
                $approvalField => true,
                $type . '_approved_by' => $user->id,
                $type . '_approved_at' => now(),
            ];

            $item->update($updateData);

            PemeriksaanBahanKemasLog::create([
                'pemeriksaan_bahan_kemas_id' => $item->id,
                'pemeriksaan_bahan_kemas_uuid' => $uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'aksi' => 'approve',
                'field_yang_diubah' => [$approvalField],
                'nilai_lama' => [$approvalField => false],
                'nilai_baru' => [$approvalField => true],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'keterangan' => 'Approved by ' . strtoupper($type),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui oleh ' . strtoupper($type),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        $user = auth()->user();
        $filename = 'template_pemeriksaan_bahan_kemas.xlsx';

        return Excel::download(new PemeriksaanBahanKemasTemplateExport($user), $filename);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $user = auth()->user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('pemeriksaan-bahan-kemas.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new PemeriksaanBahanKemasImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('pemeriksaan-bahan-kemas.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data pemeriksaan bahan kemas berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('pemeriksaan-bahan-kemas.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('pemeriksaan-bahan-kemas.index')->with('success', $successMessage);
    }

    /**
     * Bulk approve per shift sekuensial
     */
    public function bulkApprove(Request $request)
    {
        try {
            $user = Auth::user();
            $type = $request->input('type'); // qc, produksi, spv
            $tanggal = $request->input('tanggal');
            $shift_id = $request->input('shift_id');

            // Role check
            $userRole = $user->id_role;
            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5]
            ];

            if (!in_array($userRole, $allowedRoles[$type] ?? [])) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki otoritas untuk verifikasi ini.'], 403);
            }

            // Find records in this shift
            $query = PemeriksaanBahanKemas::whereDate('tanggal', $tanggal)
                ->where('shift_id', $shift_id)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            $items = $query->get();
            if ($items->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan untuk shift ini.'], 404);
            }

            // Check Sequential
            if ($type === 'produksi') {
                if ($items->contains(fn($i) => !$i->approved_by_qc)) {
                    return response()->json(['success' => false, 'message' => 'Verifikasi QC belum lengkap untuk shift ini.'], 422);
                }
            } elseif ($type === 'spv') {
                if ($items->contains(fn($i) => !$i->approved_by_produksi)) {
                    return response()->json(['success' => false, 'message' => 'Verifikasi Produksi belum lengkap untuk shift ini.'], 422);
                }
            }

            // Update
            $updateData = [
                'approved_by_' . $type => true,
                $type . '_approved_by' => $user->id,
                $type . '_approved_at' => now(),
            ];

            $query->update($updateData);

            // Log
            foreach ($items as $item) {
                PemeriksaanBahanKemasLog::create([
                    'pemeriksaan_bahan_kemas_id' => $item->id,
                    'pemeriksaan_bahan_kemas_uuid' => $item->uuid,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role ?? 'Unknown',
                    'aksi' => 'bulk_approval',
                    'field_yang_diubah' => ['approved_by_' . $type],
                    'nilai_lama' => ['approved_by_' . $type => false],
                    'nilai_baru' => ['approved_by_' . $type => true],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'keterangan' => 'Bulk Approved by ' . strtoupper($type)
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Berhasil verifikasi masal ' . strtoupper($type)]);
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
            $query = PemeriksaanBahanKemas::whereIn('uuid', $uuids)
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

            // Update hanya data yang belum approved
            $updateData = [
                $approvalField => true,
                $type . '_approved_by' => $user->id,
                $type . '_approved_at' => now(),
            ];

            PemeriksaanBahanKemas::whereIn('uuid', $uuids)
                ->where($approvalField, false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // SYNC to audit versions
            foreach ($itemsToUpdate as $item) {
                if ($item->auditVersion) {
                    $item->auditVersion->update($updateData);
                }
            }

            // Log setiap approval
            foreach ($itemsToUpdate as $item) {
                PemeriksaanBahanKemasLog::create([
                    'pemeriksaan_bahan_kemas_id' => $item->id,
                    'pemeriksaan_bahan_kemas_uuid' => $item->uuid,
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
        return (new PemeriksaanBahanKemasBulkExportService())->handle($request, fn($msg) => $this->noDataHtmlResponse($msg));
    }

    /**
     * Get import model class (for HasExcelImport trait)
     */
    protected function getImportModelClass(): string
    {
        return PemeriksaanBahanKemas::class;
    }

    /**
     * Get template filename (for HasExcelImport trait)
     */
    protected function getTemplateFilename(): string
    {
        return 'PemeriksaanBahanKemas_Template_' . date('YmdHis') . '.xlsx';
    }
}
