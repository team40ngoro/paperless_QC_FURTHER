<?php

namespace App\Http\Controllers;

use App\Models\KetidaksesuaianPlastik;
use App\Models\DataShift;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\KetidaksesuaianPlastikLog;
use App\Http\Controllers\Traits\HasAuditController;
use App\Http\Controllers\Traits\HasExcelImport;
use App\Services\BulkExport\KetidaksesuaianPlastikBulkExportService;

class KetidaksesuaianPlastikController extends Controller
{
    use HasAuditController, HasExcelImport;
    public function index()
    {
        $user = auth()->user();

        if ($user->id_role == 6) {
            return redirect()->route('ketidaksesuaian-plastik.audit');
        }

        $perPage = request('per_page', 10);
        $search = request('search');
        
        $query = KetidaksesuaianPlastik::with(['plan', 'user', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover'])->operasional();
           
        if($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if($search) {
            $query->where('nama_plastik', 'LIKE', '%' . $search . '%');
        }
        
        // Use flat pagination instead of grouped
        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
        
        $shifts = DataShift::all();
       
        return view('qc-sistem.ketidaksesuaian_plastik.index', compact('records', 'shifts', 'search', 'perPage'));
    }

    public function auditIndex(Request $request)
    {
        $user = Auth::user();
        $perPage = request('per_page', 10);
        $search = request('search');
        
        // Get source_uuid that have been copied to audit (to exclude from operational view)
        $auditedSourceUuids = KetidaksesuaianPlastik::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $query = KetidaksesuaianPlastik::with(['plan', 'user', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover'])
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
            $query->where('nama_plastik', 'LIKE', '%' . $search . '%');
        }
        
        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
        
        $shifts = DataShift::all();
        
        return view('qc-sistem.ketidaksesuaian_plastik.index_audit', compact('records', 'search', 'perPage', 'shifts'));
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

        return view('qc-sistem.ketidaksesuaian_plastik.create', compact('shifts', 'plans'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal'  => 'required|date',
            'jam'      => 'required',
            'nama_plastik' => 'required|string|max:255',
            'jumlah_ketidaksesuaian' => 'nullable|string|max:255',
            'alasan_hold'  => 'required|string',
            'hold_data'    => 'required|string',
            'dokumentasi_tagging' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'dokumentasi_penyimpangan_plastik' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $tanggalData = \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s');

        $data = [
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'id_plan' => $user->id_plan,
            'id_shift' => $request->id_shift,
            'tanggal' => $tanggalData,
            'jam'    => $request->jam,
            'nama_plastik' => $request->nama_plastik,
            'jumlah_ketidaksesuaian' => $request->jumlah_ketidaksesuaian,
            'alasan_hold' => $request->alasan_hold,
            'hold_data' => $request->hold_data,
        ];

        // Handle file uploads
        if ($request->hasFile('dokumentasi_tagging')) {
            $file = $request->file('dokumentasi_tagging');
            $filename = time() . '_' . uniqid() . '_tagging.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/ketidaksesuaian_plastik/' . $filename, $image);
            $data['dokumentasi_tagging'] = 'uploads/ketidaksesuaian_plastik/' . $filename;
        }

        if ($request->hasFile('dokumentasi_penyimpangan_plastik')) {
            $file = $request->file('dokumentasi_penyimpangan_plastik');
            $filename = time() . '_' . uniqid() . '_penyimpangan.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/ketidaksesuaian_plastik/' . $filename, $image);
            $data['dokumentasi_penyimpangan_plastik'] = 'uploads/ketidaksesuaian_plastik/' . $filename;
        }

        KetidaksesuaianPlastik::create($data);

        return redirect()->route('ketidaksesuaian-plastik.index')
            ->with('success', 'Data ketidaksesuaian plastik berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $ketidaksesuaianPlastik = KetidaksesuaianPlastik::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        if ($user->role !== 'superadmin' && $ketidaksesuaianPlastik->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        return view('qc-sistem.ketidaksesuaian_plastik.show', compact('ketidaksesuaianPlastik'));
    }

    public function edit($uuid)
    {
        $ketidaksesuaianPlastik = KetidaksesuaianPlastik::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        if ($user->role !== 'superadmin' && $ketidaksesuaianPlastik->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.ketidaksesuaian_plastik.edit', compact('ketidaksesuaianPlastik', 'shifts', 'plans'));
    }

    public function update(Request $request, $uuid)
    {
        $ketidaksesuaianPlastik = KetidaksesuaianPlastik::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        if ($user->role !== 'superadmin' && $ketidaksesuaianPlastik->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal'  => 'required|date',
            'nama_plastik' => 'required|string|max:255',
            'jumlah_ketidaksesuaian' => 'nullable|string|max:255',
            'alasan_hold'  => 'required|string',
            'hold_data'    => 'required|string',
            'dokumentasi_tagging' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'dokumentasi_penyimpangan_plastik' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $data = [
            'id_shift' => $request->id_shift,
            'tanggal'  => \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s'),
            'nama_plastik' => $request->nama_plastik,
            'jumlah_ketidaksesuaian' => $request->jumlah_ketidaksesuaian,
            'alasan_hold' => $request->alasan_hold,
            'hold_data' => $request->hold_data,
        ];

        // Handle file uploads
        if ($request->hasFile('dokumentasi_tagging')) {
            // Delete old file if exists
            if ($ketidaksesuaianPlastik->dokumentasi_tagging) {
                $oldFile = public_path('uploads/ketidaksesuaian_plastik/' . $ketidaksesuaianPlastik->dokumentasi_tagging);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            
            $file = $request->file('dokumentasi_tagging');
            $filename = time() . '_' . uniqid() . '_tagging.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/ketidaksesuaian_plastik/' . $filename, $image);
            $data['dokumentasi_tagging'] = 'uploads/ketidaksesuaian_plastik/' . $filename;
        }

        if ($request->hasFile('dokumentasi_penyimpangan_plastik')) {
            // Delete old file if exists
            if ($ketidaksesuaianPlastik->dokumentasi_penyimpangan_plastik) {
                $oldFile = public_path('uploads/ketidaksesuaian_plastik/' . $ketidaksesuaianPlastik->dokumentasi_penyimpangan_plastik);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            
            $file = $request->file('dokumentasi_penyimpangan_plastik');
            $filename = time() . '_' . uniqid() . '_penyimpangan.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/ketidaksesuaian_plastik/' . $filename, $image);
            $data['dokumentasi_penyimpangan_plastik'] = 'uploads/ketidaksesuaian_plastik/' . $filename;
        }

        $ketidaksesuaianPlastik->update($data);

        if ($user->id_role == 6 || $ketidaksesuaianPlastik->is_audit) {
            return redirect()->route('ketidaksesuaian-plastik.audit')
                ->with('success', 'Data ketidaksesuaian plastik berhasil diperbarui.');
        }

        return redirect()->route('ketidaksesuaian-plastik.index')
            ->with('success', 'Data ketidaksesuaian plastik berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $ketidaksesuaianPlastik = KetidaksesuaianPlastik::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        if ($user->role !== 'superadmin' && $ketidaksesuaianPlastik->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        // Delete associated files
        if ($ketidaksesuaianPlastik->dokumentasi_tagging) {
            $file = public_path('uploads/ketidaksesuaian_plastik/' . $ketidaksesuaianPlastik->dokumentasi_tagging);
            if (file_exists($file)) {
                unlink($file);
            }
        }

        if ($ketidaksesuaianPlastik->dokumentasi_penyimpangan_plastik) {
            $file = public_path('uploads/ketidaksesuaian_plastik/' . $ketidaksesuaianPlastik->dokumentasi_penyimpangan_plastik);
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $ketidaksesuaianPlastik->delete();

        return redirect()->route('ketidaksesuaian-plastik.index')
            ->with('success', 'Data ketidaksesuaian plastik berhasil dihapus.');
    }
    /**
     * Show logs for the specified resource.
     */
    public function showLogs($uuid)
    {
        $item = KetidaksesuaianPlastik::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $logs = KetidaksesuaianPlastikLog::where('ketidaksesuaian_plastik_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.ketidaksesuaian_plastik.logs', compact('item', 'logs'));
    }

    /**
     * Get logs JSON for the specified resource.
     */
    public function getLogsJson($uuid)
    {
        $item = KetidaksesuaianPlastik::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = KetidaksesuaianPlastikLog::where('ketidaksesuaian_plastik_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    /**
     * Export single record PDF
     */
    public function exportPdf($uuid)
    {
        try {
            $data = KetidaksesuaianPlastik::with(['plan', 'user', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover'])
                ->where('uuid', $uuid)
                ->get(); // Keep as collection for view compatibility

            if ($data->isEmpty()) {
                abort(404, 'Data not found');
            }

            $kode_form = $data->first()->kode_form ?? 'QF 22/00';

            $pdf = Pdf::loadView('qc-sistem.ketidaksesuaian_plastik.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('ketidaksesuaian_plastik_' . $uuid . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal cetak PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export PDF with filters
     */
    public function bulkExportPdf(Request $request)
    {
        try {
            // Add debug logging
            \Log::info('Bulk PDF request received', [
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            $request->validate([
                'tanggal_awal' => 'nullable|date',
                'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
                'shift_id' => 'nullable|integer',
                'kode_form' => 'required|string|max:50'
            ]);

            $user = auth()->user();
            
            $query = KetidaksesuaianPlastik::with(['plan', 'user', 'shift'])
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($request->has('is_audit') && $request->is_audit == 1) {
                $query->where('is_audit', true);
            } else {
                $query->operasional();
            }

            // Apply filters
            if ($request->tanggal_awal && $request->tanggal_akhir) {
                $tanggalAwal = \Carbon\Carbon::parse($request->tanggal_awal)->startOfDay();
                $tanggalAkhir = \Carbon\Carbon::parse($request->tanggal_akhir)->endOfDay();
                $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            } elseif ($request->tanggal_awal) {
                $query->whereDate('tanggal', $request->tanggal_awal);
            }
            
            if ($request->filled('shift_id')) {
                $query->where('id_shift', $request->shift_id);
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            \Log::info('Data query result', [
                'data_count' => $data->count(),
                'query_sql' => $query->toSql()
            ]);

            if ($data->isEmpty()) {
                \Log::warning('No data found for PDF export');
                $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
                $filterInfo = [];
                
                if ($request->tanggal_awal && $request->tanggal_akhir) {
                    $filterInfo[] = 'Tanggal: ' . Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
                } elseif ($request->tanggal_awal) {
                    $filterInfo[] = 'Tanggal: ' . Carbon::parse($request->tanggal_awal)->format('d-m-Y');
                }
                if ($request->shift_id) {
                    $shift = DataShift::find($request->shift_id);
                    $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
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

            // Save kode_form to all filtered records
            if ($data->isNotEmpty()) {
                $query->update(['kode_form' => $request->kode_form]);
            }

            // Pass kode_form to view
            $kode_form = $request->kode_form ?? '';
            $filename = 'ketidaksesuaian_plastik_bulk_' . date('Y-m-d_H-i-s') . '.pdf';

            \Log::info('Generating PDF', [
                'filename' => $filename,
                'kode_form' => $kode_form,
                'data_count' => $data->count()
            ]);

            $pdf = Pdf::loadView('qc-sistem.ketidaksesuaian_plastik.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A4', 'landscape');
            
            \Log::info('PDF generated successfully, starting download');
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Bulk PDF generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json(['error' => 'Gagal membuat PDF: ' . $e->getMessage()], 500);
        }
    }

    public function approve(Request $request, $uuid)
    {
        try {
            $ketidaksesuaianPlastik = KetidaksesuaianPlastik::where('uuid', $uuid)->firstOrFail();
            $user = auth()->user();
            $type = $request->input('type');
            
            // Validasi role dan tipe approval
            $allowedRoles = [
                'qc' => [1, 3, 5], // Role 1, 3, 5 bisa approve QC
                'produksi' => [2], // Role 2 bisa approve Produksi
                'spv' => [4] // Role 4 bisa approve SPV
            ];
            
            if (!isset($allowedRoles[$type]) || !in_array($user->id_role, $allowedRoles[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melakukan approval ini.'
                ], 403);
            }
            
            // Validasi sequential approval
            if ($type === 'produksi' && !$ketidaksesuaianPlastik->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
                ], 400);
            }
            
            if ($type === 'spv' && !$ketidaksesuaianPlastik->approved_by_produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
                ], 400);
            }
            
            // Cek apakah sudah di-approve sebelumnya
            $approvalField = "approved_by_{$type}";
            if ($ketidaksesuaianPlastik->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui sebelumnya.'
                ], 400);
            }
            
            // Update approval
            $ketidaksesuaianPlastik->update([
                $approvalField => true,
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now()
            ]);
            
            // Log activity (jika ada model log)
            // KetidaksesuaianPlastikLog::create([
            //     'ketidaksesuaian_plastik_id' => $ketidaksesuaianPlastik->id,
            //     'user_id' => $user->id,
            //     'action' => 'approve',
            //     'description' => "Data disetujui oleh {$type}: {$user->name}",
            //     'old_values' => json_encode([$approvalField => false]),
            //     'new_values' => json_encode([$approvalField => true])
            // ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Approval failed', [
                'error' => $e->getMessage(),
                'uuid' => $uuid,
                'type' => $type,
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyetujui data.'
            ], 500);
        }
    }

    public function bulkApprove(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'id_shift' => 'required|exists:data_shift,id',
                'type' => 'required|in:qc,produksi,spv',
            ]);

            $user = Auth::user();
            $userRole = $user->id_role ?? null;
            $type = $request->type;

            // Validasi role
            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [2],
                'spv' => [4]
            ];

            if (!isset($allowedRoles[$type]) || !in_array($userRole, $allowedRoles[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melakukan bulk approval ini.'
                ], 403);
            }

            // Cari data yang belum di-approve untuk filter ini
            $query = KetidaksesuaianPlastik::whereDate('tanggal', $request->tanggal)
                ->where('id_shift', $request->id_shift);

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
                    $oldValue = $item->$approvalField;
                    
                    $item->update([
                        $approvalField => true,
                        $approvedAtField => now(),
                        $approvedByField => $user->id,
                    ]);

                    // Log activity
                    KetidaksesuaianPlastikLog::create([
                        'ketidaksesuaian_plastik_id' => $item->id,
                        'ketidaksesuaian_plastik_uuid' => $item->uuid,
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_role' => $user->role,
                        'aksi' => 'bulk_approve',
                        'field_yang_diubah' => [$approvalField],
                        'nilai_lama' => [$approvalField => $oldValue],
                        'nilai_baru' => [$approvalField => true],
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'keterangan' => "Bulk Approval {$type} oleh {$user->name}"
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
            $query = KetidaksesuaianPlastik::whereIn('uuid', $uuids)
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

            KetidaksesuaianPlastik::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            // Find all source_uuids from the updated records
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();

            // Update audit copies with same approval data
            KetidaksesuaianPlastik::where('is_audit', true)
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

    // --- AUDIT TRAIL METHODS ---

    protected function auditModel(): string
    {
        return KetidaksesuaianPlastik::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'ketidaksesuaian-plastik';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.ketidaksesuaian_plastik';
    }

    protected function auditRelations(): array
    {
        return [
            'plan', 'user', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover'
        ];
    }

    protected function auditSearchColumns(): array
    {
        return ['nama_plastik', 'alasan_hold'];
    }

    protected function auditTimeColumn(): string
    {
        return 'jam';
    }

    public function bulkExportExcel(Request $request, KetidaksesuaianPlastikBulkExportService $service)
    {
        return $service->handle($request, fn ($msg) => $this->buildNoDataHtmlResponse($msg));
    }

    /**
     * Get the model class for import
     */
    protected function getImportModelClass(): string
    {
        return KetidaksesuaianPlastik::class;
    }

    /**
     * Get the template filename
     */
    protected function getTemplateFilename(): string
    {
        return 'Ketidaksesuaian_Plastik_Template_' . date('Y-m-d_H-i-s') . '.xlsx';
    }
}
