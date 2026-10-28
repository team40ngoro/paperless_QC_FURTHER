<?php

namespace App\Http\Controllers;

use App\Models\Rebox;
use App\Models\DataShift;
use App\Models\Plan;
use App\Models\ReboxLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReboxTemplateExport;
use App\Imports\ReboxImport;
use App\Http\Controllers\Traits\HasAuditController;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Http\Controllers\Traits\HasExcelImport;
use App\Services\BulkExport\ReboxBulkExportService;

class ReboxController extends Controller
{
    use HasAuditController, HasNoDataHtmlResponse, HasExcelImport;

    protected function auditModel(): string
    {
        return Rebox::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'rebox';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.rebox';
    }

    protected function auditRelations(): array
    {
        return ['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'createdBy', 'originalVersion'];
    }

    protected function auditSearchColumns(): array
    {
        return ['nama_produk', 'kode_produksi'];
    }

    protected function auditDateColumn(): string
    {
        return 'tanggal';
    }

    protected function auditTimeColumn(): string
    {
        return 'jam';
    }

    protected function auditShiftRelation(): string
    {
        return 'shift';
    }

    protected function auditShiftColumn(): string
    {
        return 'shift_id';
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        // Auditor langsung diarahkan ke halaman audit
        if ($user->id_role == 6) {
            return redirect()->route('rebox.audit');
        }
        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        $query = Rebox::with(['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'createdBy' , 'auditVersion'])
            ->operasional() 
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_produk', 'LIKE', '%' . $search . '%')
                  ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%');
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

        return view('qc-sistem.rebox.index', compact('records', 'search', 'perPage'));
    }

    public function searchAjax(Request $request)
    {
        try {
            $user = auth()->user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 5);
            $page = (int) $request->get('page', 1);

            $query = Rebox::with(['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'createdBy' ,'auditVersion'])
                ->operasional() 
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_produk', 'LIKE', '%' . $search . '%')
                      ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%');
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
                'html' => view('qc-sistem.rebox._table', compact('records'))->render(),
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
        
        if ($user->role === 'superadmin' || $user->id_role == 1) {
            $plans = Plan::all();
            $shifts = DataShift::all();
            $jenis_produk = \App\Models\JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $jenis_produk = \App\Models\JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        
        return view('qc-sistem.rebox.create', compact('shifts', 'plans', 'jenis_produk'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_produk' => 'required|array',
            'nama_produk.*' => 'required|string',
            'kode_produksi' => 'required|array',
            'kode_produksi.*' => 'required|string',
            'best_before' => 'required|array',
            'best_before.*' => 'required|date',
            'isi_jumlah' => 'required|array',
            'isi_jumlah.*' => 'required|string',
            'tanggal_rebox' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'shift_id' => 'required|integer',
            'labelisasi' => 'required|array',
            'labelisasi.*' => 'required|string',
        ]);

        $count = count($data['nama_produk']);
        $user = auth()->user();
        for ($i = 0; $i < $count; $i++) {
            Rebox::create([
                'id_plan' => $user->id_plan,
                'created_by' => $user->id,
                'nama_produk' => $data['nama_produk'][$i],
                'kode_produksi' => $data['kode_produksi'][$i],
                'best_before' => $data['best_before'][$i],
                'tanggal' => Carbon::parse($data['tanggal_rebox'])->setTimeFrom(now())->format('Y-m-d H:i:s'),
                'jam' => $data['jam'],
                'shift_id' => $data['shift_id'],
                'isi_jumlah' => $data['isi_jumlah'][$i],
                'labelisasi' => $data['labelisasi'][$i],
            ]);
        }

        return redirect()->route('rebox.index')->with('success', 'Data berhasil disimpan');
    }

    /**
     * Menampilkan detail data rebox
     */
    public function show($uuid)
    {
        $rebox = Rebox::with(['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'createdBy'])
            ->where('uuid', $uuid)->firstOrFail();
        
        $user = auth()->user();
        if ($user->role !== 'superadmin' && $rebox->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('rebox.show', $rebox)) {
            return $redirect;
        }

        return view('qc-sistem.rebox.show', compact('rebox'));
    }

    public function edit($uuid)
    {
        $rebox = Rebox::where('uuid', $uuid)->firstOrFail();
       
        // Check edit time limit using global config
        $user = auth()->user();
        if ($redirect = $this->redirectToCanonicalAuditUrl('rebox.edit', $rebox)) {
            return $redirect;
        }

        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($rebox->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('rebox.index')
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
        return view('qc-sistem.rebox.edit', compact('rebox', 'shifts', 'plans'));
    }

    public function update(Request $request, $uuid)
    {
        $rebox = Rebox::where('uuid', $uuid)->firstOrFail();

        // Check edit time limit using global config
        $user = auth()->user();
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($rebox->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('rebox.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        $data = $request->validate([
            'nama_produk' => 'required|string',
            'kode_produksi' => 'required|string',
            'best_before' => 'required|date',
            'isi_jumlah' => 'required|string',
            'tanggal_rebox' => 'required|date',
            //update dev bagus nambah field validasi jam
            'jam' => 'required|date_format:H:i',
            'shift_id' => 'required|integer',
            'labelisasi' => 'required|string',
        ]);

        $rebox->update([
            'nama_produk' => $request->nama_produk,
            'kode_produksi' => $request->kode_produksi,
            'best_before' => $request->best_before,
            'isi_jumlah' => $request->isi_jumlah,
            //update dev bagus save update jam
            'jam' => $request->jam,
            'tanggal' => Carbon::parse($data['tanggal_rebox'])->setTimeFrom(now())->format('Y-m-d H:i:s'),
            'shift_id' => $request->shift_id,
            'labelisasi' => $request->labelisasi,
        ]);

        return redirect()->route('rebox.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $rebox = Rebox::where('uuid', $uuid)->firstOrFail();
        $rebox->delete();
        return redirect()->route('rebox.index')->with('success', 'Data berhasil dihapus');
    }

    /**
     * Approve data
     */
    public function approve(Request $request, $uuid)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv'
            ]);

            $user = auth()->user();
            $userRole = $user->id_role;
            $type = $request->type;

            // Validasi role dan type yang diizinkan
            $allowedRoles = [
                'qc' => [1, 3, 5], // Role 1, 3, dan 5 bisa approve QC
                'produksi' => [1, 2, 5], // Role 1, 2, dan 5 bisa approve Produksi
                'spv' => [1, 4, 5] // Role 1, 4, dan 5 bisa approve SPV
            ];

            if (!in_array($userRole, $allowedRoles[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melakukan approval ini'
                ], 403);
            }

            $rebox = Rebox::where('uuid', $uuid)->firstOrFail();

            // Check authorization - user hanya bisa approve data dari plan mereka sendiri
            if ($user->role !== 'superadmin' && $rebox->id_plan !== $user->id_plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Cek apakah sudah diapprove sebelumnya
            $approvalField = 'approved_by_' . $type;
            if ($rebox->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui oleh ' . strtoupper($type)
                ], 400);
            }

            // Sequential approval validation
            if ($type === 'produksi' && !$rebox->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh QC terlebih dahulu'
                ], 400);
            }

            if ($type === 'spv' && !$rebox->approved_by_produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh Produksi terlebih dahulu'
                ], 400);
            }

            // Update approval status
            $updateData = [
                $approvalField => true,
                $type . '_approved_by' => $user->id,
                $type . '_approved_at' => now()
            ];

            $rebox->update($updateData);

            // Log activity
            ReboxLog::create([
                'rebox_id' => $rebox->id,
                'rebox_uuid' => $uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'aksi' => 'approve',
                'field_yang_diubah' => [$approvalField],
                'nilai_lama' => [$approvalField => false],
                'nilai_baru' => [$approvalField => true],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'keterangan' => 'Approved by ' . strtoupper($type)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui oleh ' . strtoupper($type)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan halaman log perubahan data rebox
     */
    public function showLogs($uuid)
    {
        $user = auth()->user();
        
        // Cari data rebox berdasarkan UUID
        $item = Rebox::where('uuid', $uuid)->with(['shift', 'plan'])->firstOrFail();
        
        // Cek authorization - user hanya bisa melihat log dari plan mereka sendiri
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access to logs');
        }
        
        // Ambil log dengan pagination
        $logs = ReboxLog::where('rebox_uuid', $uuid)
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
        
        return view('qc-sistem.rebox.logs', compact('item', 'logs'));
    }

    /**
     * API endpoint untuk mendapatkan data log dalam format JSON
     */
    public function getLogsJson($uuid)
    {
        $user = auth()->user();
        
        // Cari data rebox berdasarkan UUID
        $item = Rebox::where('uuid', $uuid)->firstOrFail();
        
        // Cek authorization
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Ambil log
        $logs = ReboxLog::where('rebox_uuid', $uuid)
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        $formattedLogs = $logs->map(function($log) {
            return [
                'id' => $log->id,
                'tanggal' => $log->created_at->format('d/m/Y H:i:s'),
                'user_name' => $log->user_name,
                'user_role' => $log->user_role,
                'aksi' => $log->aksi,
                'field_yang_diubah' => $log->field_yang_diubah,
                'deskripsi_perubahan' => $log->getDeskripsiPerubahanAttribute(),
                'keterangan' => $log->keterangan
            ];
        });
        
        return response()->json($formattedLogs);
    }

    /**
     * Export PDF with filters
     */
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

            $query = Rebox::with(['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover'])
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
            $pdf = PDF::loadView('qc-sistem.rebox.export_pdf', compact('data', 'kode_form', 'filters'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->download('rebox_' . date('Y-m-d_H-i-s') . '.pdf');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Save kode form for specific record
     */
    public function saveKode(Request $request)
    {
        try {
            $request->validate([
                'uuid' => 'required|string',
                'kode_form' => 'required|string|max:50'
            ]);

            $item = Rebox::where('uuid', $request->uuid)->firstOrFail();
            
            // Update kode form
            $item->update([
                'kode_form' => $request->kode_form
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kode form berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan kode form: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        $user = auth()->user();
        $filename = 'template_rebox.xlsx';

        return Excel::download(new ReboxTemplateExport($user), $filename);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $user = auth()->user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('rebox.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new ReboxImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('rebox.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift/Produk sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data rebox berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('rebox.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('rebox.index')->with('success', $successMessage);
    }

    /**
     * Bulk approve per shift sekuensial
     */
    public function bulkApprove(Request $request)
    {
        try {
            $user = auth()->user();
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
            $query = Rebox::whereDate('tanggal', $tanggal)
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
                ReboxLog::create([
                    'rebox_id' => $item->id,
                    'rebox_uuid' => $item->uuid,
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
            $query = Rebox::whereIn('uuid', $uuids)
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

            Rebox::whereIn('uuid', $uuids)
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
                ReboxLog::create([
                    'rebox_id' => $item->id,
                    'rebox_uuid' => $item->uuid,
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
        return (new ReboxBulkExportService())->handle($request, fn($msg) => $this->noDataHtmlResponse($msg));
    }

    /**
     * Get import model class (for HasExcelImport trait)
     */
    protected function getImportModelClass(): string
    {
        return Rebox::class;
    }

    /**
     * Get template filename (for HasExcelImport trait)
     */
    protected function getTemplateFilename(): string
    {
        return 'Rebox_Template_' . date('YmdHis') . '.xlsx';
    }
}
