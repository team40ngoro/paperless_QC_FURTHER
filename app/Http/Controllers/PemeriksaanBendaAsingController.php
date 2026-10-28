<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanBendaAsing;
use App\Models\JenisProduk;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\PemeriksaanBendaAsingLog;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Traits\HasAuditController;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Http\Controllers\Traits\HasExcelImport;
use App\Services\BulkExport\PemeriksaanBendaAsingBulkExportService;

class PemeriksaanBendaAsingController extends Controller
{
    use HasAuditController, HasNoDataHtmlResponse, HasExcelImport;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->id_role == 6) {
            return redirect()->route('pemeriksaan-benda-asing.audit');
        }

        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        $query = PemeriksaanBendaAsing::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->where('is_audit', false);

        // Role-based filtering
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        // Handle search
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('jenis_kontaminasi', 'like', '%' . $search . '%')
                  ->orWhere('kode_produksi', 'like', '%' . $search . '%')
                  ->orWhereHas('produk', function($produkQuery) use ($search) {
                      $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
                  });
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
                        ->orderBy('jam', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate($perPage);

        return view('qc-sistem.pemeriksaan_benda_asing.index', compact('records', 'search', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get data berdasarkan role
        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $shifts = DataShift::all();
            $plans = Plan::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('qc-sistem.pemeriksaan_benda_asing.create', compact('produks', 'shifts', 'plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $user = Auth::user();
         $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);
         if($isSpecialRole){
             $request->validate([
                 'shift_id' => 'required|exists:data_shift,id',
                 'id_produk' => 'nullable|exists:jenis_produk,id',
                 'rm' => 'nullable|string|max:255',
                 'jumlah_terdampak' => 'nullable|string|max:255',
                 'berat' => 'nullable|numeric',
                'tanggal' => 'required|date_format:d-m-Y',
                 'jam' => 'required',
                 'jenis_kontaminasi' => 'required|string|max:255',
                 'bukti' => 'nullable|image|mimes:jpeg,png,jpg',
                 'kode_produksi' => 'required|string|max:255',
                 'ukuran_kontaminasi' => 'required|string|max:255',
                 'ditemukan' => 'required|string|max:255',
                 'analisa_masalah' => 'nullable|string',
                 'koreksi' => 'nullable|string',
                 'tindak_korektif' => 'nullable|string',
                 'diketahui' => 'nullable|string|max:255',
             ]);

         }else{
            $request->validate([
                 'shift_id' => 'required|exists:data_shift,id',
                 'id_produk' => 'nullable|exists:jenis_produk,id',
                 'rm' => 'nullable|string|max:255',
                 'jumlah_terdampak' => 'nullable|string|max:255',
                 'berat' => 'nullable|numeric',
                  'tanggal' => 'required|date_format:d-m-Y H:i:s',
                  'jam' => 'required',
                 'jenis_kontaminasi' => 'required|string|max:255',
                 'bukti' => 'nullable|image|mimes:jpeg,png,jpg',
                 'kode_produksi' => 'required|string|max:255',
                 'ukuran_kontaminasi' => 'required|string|max:255',
                 'ditemukan' => 'required|string|max:255',
                 'analisa_masalah' => 'nullable|string',
                 'koreksi' => 'nullable|string',
                 'tindak_korektif' => 'nullable|string',
                 'diketahui' => 'nullable|string|max:255',
             ]);
         }

        $tanggalData = $request->tanggal;
    if ($isSpecialRole) {
        // Untuk user dengan role 2 atau 3, gunakan format tanggal dari request tapi waktu dari now()
        $dateOnly = \Carbon\Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
        $timeNow = now()->format('H:i:s');
        $tanggalData = $dateOnly . ' ' . $timeNow;
    } else {
        // Untuk user lain, gunakan format tanggal dan waktu dari request
        $tanggalData = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
    }
        
        // Get shift untuk mendapatkan id_plan
        $selectedShift = DataShift::findOrFail($request->shift_id);

        // Handle file upload bukti
        $bukti_path = null;
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = time() . '_' . uniqid() . '_bukti.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/pemeriksaan_benda_asing/' . $filename, $image);
            $bukti_path = 'uploads/pemeriksaan_benda_asing/' . $filename;
        }

        // Create data
        PemeriksaanBendaAsing::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'id_plan' => $selectedShift->id_plan,
            'shift_id' => $request->shift_id,
            'id_produk' => $request->id_produk,
            'rm' => $request->rm,
            'jumlah_terdampak' => $request->jumlah_terdampak,
            'berat' => $request->berat,
            'tanggal' => $tanggalData,
            'jam' => $request->jam,
            'jenis_kontaminasi' => $request->jenis_kontaminasi,
            'bukti' => $bukti_path,
            'kode_produksi' => $request->kode_produksi,
            'ukuran_kontaminasi' => $request->ukuran_kontaminasi,
            'ditemukan' => $request->ditemukan,
            'analisa_masalah' => $request->analisa_masalah,
            'koreksi' => $request->koreksi,
            'tindak_korektif' => $request->tindak_korektif,
            'diketahui' => $request->diketahui,
        ]);

        return redirect()->route('pemeriksaan-benda-asing.index')
            ->with('success', 'Data pemeriksaan benda asing berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $pemeriksaan = PemeriksaanBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access
        if (!$pemeriksaan->canAccess($user)) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('pemeriksaan-benda-asing.show', $pemeriksaan)) {
            return $redirect;
        }

        return view('qc-sistem.pemeriksaan_benda_asing.show', compact('pemeriksaan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $pemeriksaan = PemeriksaanBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access
        if (!$pemeriksaan->canAccess($user)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Determine if in audit mode
        $isAuditMode = (bool) $pemeriksaan->is_audit;
        $queryParameters = request()->query();
        $hasAuditFlag = array_key_exists('is_audit', $queryParameters);

        if (($isAuditMode && request()->query('is_audit') !== '1') || (!$isAuditMode && $hasAuditFlag)) {
            unset($queryParameters['is_audit'], $queryParameters['uuid']);
            if ($isAuditMode) {
                $queryParameters['is_audit'] = '1';
            }

            return redirect()->route(
                'pemeriksaan-benda-asing.edit',
                array_merge(['uuid' => $pemeriksaan->uuid], $queryParameters)
            );
        }

        // Get data berdasarkan role
        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $shifts = DataShift::all();
            $plans = Plan::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('qc-sistem.pemeriksaan_benda_asing.edit', compact('pemeriksaan', 'produks', 'shifts', 'plans', 'isAuditMode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'id_produk' => 'nullable|exists:jenis_produk,id',
            'rm' => 'nullable|string|max:255',
            'jumlah_terdampak' => 'nullable|string|max:255',
            'berat' => 'nullable|numeric',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'jenis_kontaminasi' => 'required|string|max:255',
            'bukti' => 'nullable|image|mimes:jpeg,png,jpg',
            'kode_produksi' => 'required|string|max:255',
            'ukuran_kontaminasi' => 'required|string|max:255',
            'ditemukan' => 'required|string|max:255',
            'analisa_masalah' => 'nullable|string',
            'koreksi' => 'nullable|string',
            'tindak_korektif' => 'nullable|string',
            'diketahui' => 'nullable|string|max:255',
        ]);

        $pemeriksaan = PemeriksaanBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access
        if (!$pemeriksaan->canAccess($user)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Handle file upload bukti
        $bukti_path = $pemeriksaan->bukti;
        if ($request->hasFile('bukti')) {
            // Delete old file if exists
            if ($bukti_path && Storage::disk('public')->exists($bukti_path)) {
                Storage::disk('public')->delete($bukti_path);
            }
            
            $file = $request->file('bukti');
            $filename = time() . '_' . uniqid() . '_bukti.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/pemeriksaan_benda_asing/' . $filename, $image);
            $bukti_path = 'uploads/pemeriksaan_benda_asing/' . $filename;
        }

        // Update data
        $pemeriksaan->update([
            'shift_id' => $request->shift_id,
            'id_produk' => $request->id_produk,
            'rm' => $request->rm,
            'jumlah_terdampak' => $request->jumlah_terdampak,
            'berat' => $request->berat,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'jenis_kontaminasi' => $request->jenis_kontaminasi,
            'bukti' => $bukti_path,
            'kode_produksi' => $request->kode_produksi,
            'ukuran_kontaminasi' => $request->ukuran_kontaminasi,
            'ditemukan' => $request->ditemukan,
            'analisa_masalah' => $request->analisa_masalah,
            'koreksi' => $request->koreksi,
            'tindak_korektif' => $request->tindak_korektif,
            'diketahui' => $request->diketahui,
        ]);

        if ($pemeriksaan->is_audit) {
            return redirect()->route('pemeriksaan-benda-asing.audit')
                ->with('success', 'Data pemeriksaan benda asing berhasil diperbarui.');
        }

        return redirect()->route('pemeriksaan-benda-asing.index')
            ->with('success', 'Data pemeriksaan benda asing berhasil diperbarui.');
    }

    // ===== EXCEL IMPORT TRAIT IMPLEMENTATION =====

    /**
     * Get the model class for import
     */
    protected function getImportModelClass(): string
    {
        return PemeriksaanBendaAsing::class;
    }

    /**
     * Get the template filename
     */
    protected function getTemplateFilename(): string
    {
        return 'Template_Import_PemeriksaanBendaAsing_' . date('Y-m-d_H-i-s') . '.xlsx';
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $pemeriksaan = PemeriksaanBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check access
        if (!$pemeriksaan->canAccess($user)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        // Delete file if exists
        if ($pemeriksaan->bukti && Storage::disk('public')->exists($pemeriksaan->bukti)) {
            Storage::disk('public')->delete($pemeriksaan->bukti);
        }

        $pemeriksaan->delete();

        return redirect()->route('pemeriksaan-benda-asing.index')
            ->with('success', 'Data pemeriksaan benda asing berhasil dihapus.');
    }
    /**
    * Show logs for the specified resource.
    */
    public function showLogs($uuid)
    {
        $item = PemeriksaanBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $logs = PemeriksaanBendaAsingLog::where('pemeriksaan_benda_asing_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.pemeriksaan_benda_asing.logs', compact('item', 'logs'));
    }

    /**
     * Get logs JSON for the specified resource.
     */
    public function getLogsJson($uuid)
    {
        $item = PemeriksaanBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = PemeriksaanBendaAsingLog::where('pemeriksaan_benda_asing_id', $item->id)
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
            $data = PemeriksaanBendaAsing::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover'])
                ->where('uuid', $uuid)
                ->get(); // Keep as collection for view compatibility

            if ($data->isEmpty()) {
                abort(404, 'Data not found');
            }

            $filters = [
                'tanggal' => $data->first()->tanggal ?? null,
                'shift_id' => $data->first()->shift_id ?? null,
                'id_produk' => $data->first()->id_produk ?? null,
                'kode_form' => $data->first()->kode_form ?? 'QF 23/00'
            ];

            $pdf = Pdf::loadView('qc-sistem.pemeriksaan_benda_asing.export_pdf', compact('data', 'filters'))
                ->setPaper('a4', 'portrait');

            return $pdf->stream('pemeriksaan_benda_asing_' . $uuid . '.pdf');
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
            $request->validate([
                'tanggal_awal' => 'nullable|date',
                'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
                'shift_id' => 'nullable|integer',
                'id_produk' => 'nullable|integer',
                'kode_form' => 'required|string|max:50'
            ]);

            $user = Auth::user();
            
            $query = PemeriksaanBendaAsing::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover'])
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
            
            if ($request->shift_id) {
                $query->where('shift_id', $request->shift_id);
            }

            if ($request->id_produk) {
                $query->where('id_produk', $request->id_produk);
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            // Save kode_form to all filtered records
            if ($data->isNotEmpty()) {
                $query->update(['kode_form' => $request->kode_form]);
            }
            if ($data->isEmpty()) {
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
            // Prepare filters for PDF view
            $tanggalFilter = 'Semua Tanggal';
            if ($request->tanggal_awal && $request->tanggal_akhir) {
                $tanggalFilter = Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
            } elseif ($request->tanggal_awal) {
                $tanggalFilter = Carbon::parse($request->tanggal_awal)->format('d-m-Y');
            }

            $filters = [
                'tanggal' => $tanggalFilter,
                'shift_id' => $request->shift_id,
                'id_produk' => $request->id_produk,
                'kode_form' => $request->kode_form
            ];

            // Generate PDF
            $pdf = Pdf::loadView('qc-sistem.pemeriksaan_benda_asing.export_pdf', compact('data', 'filters'))
                ->setPaper('a4', 'portrait');

            $filename = 'pemeriksaan_benda_asing_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve data with role-based validation
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $pemeriksaanBendaAsing = PemeriksaanBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control following Produk Forming pattern
        $allowedRoles = [
            'qc' => [1, 3, 5], // Role 1&5 (QC only), Role 3 (QC only)
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
        if ($type === 'produksi' && !$pemeriksaanBendaAsing->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$pemeriksaanBendaAsing->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($pemeriksaanBendaAsing->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $pemeriksaanBendaAsing->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        // Log the approval activity
        PemeriksaanBendaAsingLog::create([
            'uuid' => Str::uuid(),
            'pemeriksaan_benda_asing_id' => $pemeriksaanBendaAsing->id,
            'pemeriksaan_benda_asing_uuid' => $pemeriksaanBendaAsing->uuid,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'aksi' => 'approve',
            'field_yang_diubah' => [$approvalField],
            'nilai_lama' => ['false'],
            'nilai_baru' => ['true'],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'keterangan' => "Data disetujui oleh {$type}: {$user->name}"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }

    /**
     * Bulk Approve Selected - checkbox-based per-row approval
     * Replaces shift-based approval with flexible checkbox selection
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
            $query = PemeriksaanBendaAsing::whereIn('uuid', $uuids)
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

            PemeriksaanBendaAsing::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            // Find all source_uuids from the updated records
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();

            // Update audit copies with same approval data
            PemeriksaanBendaAsing::where('is_audit', true)
                ->whereIn('source_uuid', $updatedSourceUuids)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // Log activity
            foreach ($itemsToUpdate as $item) {
                PemeriksaanBendaAsingLog::create([
                    'uuid' => Str::uuid(),
                    'pemeriksaan_benda_asing_id' => $item->id,
                    'pemeriksaan_benda_asing_uuid' => $item->uuid,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role,
                    'aksi' => 'bulk_approval_selected',
                    'field_yang_diubah' => ["approved_by_{$type}"],
                    'nilai_lama' => ['false'],
                    'nilai_baru' => ['true'],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'keterangan' => 'Bulk Approved Selected by ' . strtoupper($type)
                ]);
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
            $query = PemeriksaanBendaAsing::whereDate('tanggal', $request->tanggal)
                ->where('shift_id', $request->shift_id);

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
                    PemeriksaanBendaAsingLog::create([
                        'uuid' => Str::uuid(),
                        'pemeriksaan_benda_asing_id' => $item->id,
                        'pemeriksaan_benda_asing_uuid' => $item->uuid,
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_role' => $user->role,
                        'aksi' => 'bulk_approve',
                        'field_yang_diubah' => [$approvalField],
                        'nilai_lama' => ['false'],
                        'nilai_baru' => ['true'],
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

    public function auditIndex(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        // Ambil daftar source_uuid yang sudah di-copy ke audit (bebas collation error)
        $auditedSourceUuids = PemeriksaanBendaAsing::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $query = PemeriksaanBendaAsing::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover', 'originalVersion'])
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
                $q->where('jenis_kontaminasi', 'LIKE', '%' . $search . '%')
                  ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('produk', function($produkQuery) use ($search) {
                      $produkQuery->where('nama_produk', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderBy('tanggal', 'desc')
                         ->orderBy('jam', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->orderBy('id', 'desc')
                         ->paginate($perPage);

        return view('qc-sistem.pemeriksaan_benda_asing.index_audit', compact('records', 'search', 'perPage'));
    }

    // --- AUDIT TRAIL METHODS ---

    protected function auditModel(): string
    {
        return PemeriksaanBendaAsing::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'pemeriksaan-benda-asing';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.pemeriksaan_benda_asing';
    }

    protected function auditRelations(): array
    {
        return [
            'plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover'
        ];
    }

    protected function auditSearchColumns(): array
    {
        return ['jenis_kontaminasi', 'kode_produksi'];
    }

    protected function auditTimeColumn(): string
    {
        return 'jam';
    }

    public function bulkExportExcel(Request $request, PemeriksaanBendaAsingBulkExportService $service)
    {
        return $service->handle($request, fn ($msg) => $this->buildNoDataHtmlResponse($msg));
    }
}
