<?php

namespace App\Http\Controllers;

use App\Models\KetidaksesuaianBendaAsing;
use App\Models\KetidaksesuaianBendaAsingLog;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use PDF;

class KetidaksesuaianBendaAsingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = KetidaksesuaianBendaAsing::with(['user', 'plan', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if ($search) {
            $query->where('kode_produksi', 'LIKE', '%' . $search . '%');
        }

        $ketidaksesuaianBendaAsing = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('qc-sistem.ketidaksesuaian_benda_asing.index', compact('ketidaksesuaianBendaAsing'));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
            $produks = JenisProduk::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.ketidaksesuaian_benda_asing.create', compact('shifts', 'produks'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'shift_id'  => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'tanggal'   => 'required|date',
            'jam'       => 'required',
            'kode_produksi' => 'required|string|max:255',
            'jenis_kontaminan' => 'required|string|max:255',
            'jumlah_produk_terdampak' => 'required|integer|min:1',
            'tahapan'   => 'required|string|max:255',
            'dokumentasi' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $tanggalData = \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s');

        $data = [
            'uuid' => Str::uuid(),
            'id_plan' => $user->id_plan, // Automatically use logged-in user's plan
            'user_id' => $user->id,
            'shift_id' => $request->shift_id,
            'id_produk' => $request->id_produk,
          'tanggal' => $tanggalData,
            'jam' => $request->jam,
            'kode_produksi' => $request->kode_produksi,
            'jenis_kontaminan' => $request->jenis_kontaminan,
            'jumlah_produk_terdampak' => $request->jumlah_produk_terdampak,
            'tahapan' => $request->tahapan,
        ];

        // Handle file upload
        if ($request->hasFile('dokumentasi')) {
            $file = $request->file('dokumentasi');
            $filename = time() . '_' . uniqid() . '_dokumentasi.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/ketidaksesuaian_benda_asing/' . $filename, $image);
            $data['dokumentasi'] = 'uploads/ketidaksesuaian_benda_asing/' . $filename;
        }

        KetidaksesuaianBendaAsing::create($data);

        return redirect()->route('ketidaksesuaian-benda-asing.index')
            ->with('success', 'Data ketidaksesuaian benda asing berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $ketidaksesuaianBendaAsing = KetidaksesuaianBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        if ($user->role !== 'superadmin' && $ketidaksesuaianBendaAsing->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        return view('qc-sistem.ketidaksesuaian_benda_asing.show', compact('ketidaksesuaianBendaAsing'));
    }

    public function edit($uuid)
    {
        $ketidaksesuaianBendaAsing = KetidaksesuaianBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        if ($user->role !== 'superadmin' && $ketidaksesuaianBendaAsing->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
            $produks = JenisProduk::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.ketidaksesuaian_benda_asing.edit', compact('ketidaksesuaianBendaAsing', 'shifts', 'produks'));
    }

    public function update(Request $request, $uuid)
    {
        $user = auth()->user();
        $ketidaksesuaianBendaAsing = KetidaksesuaianBendaAsing::where('uuid', $uuid)->firstOrFail();

        // Authorization check
        if ($user->role !== 'superadmin' && $ketidaksesuaianBendaAsing->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'tanggal' => 'required|date',
            'jam' => 'required|string',
            'kode_produksi' => 'required|string|max:255',
            'jenis_kontaminan' => 'required|string|max:255',
            'jumlah_produk_terdampak' => 'required|integer|min:1',
            'tahapan' => 'required|string|max:255',
            'dokumentasi' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $data = [
            'id_plan' => $user->id_plan, // Keep using logged-in user's plan
            'shift_id' => $request->shift_id,
            'id_produk' => $request->id_produk,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'kode_produksi' => $request->kode_produksi,
            'jenis_kontaminan' => $request->jenis_kontaminan,
            'jumlah_produk_terdampak' => $request->jumlah_produk_terdampak,
            'tahapan' => $request->tahapan,
        ];

        // Handle file upload
        if ($request->hasFile('dokumentasi')) {
            // Delete old file if exists
            if ($ketidaksesuaianBendaAsing->dokumentasi && Storage::disk('public')->exists($ketidaksesuaianBendaAsing->dokumentasi)) {
                Storage::disk('public')->delete($ketidaksesuaianBendaAsing->dokumentasi);
            }
            
            $file = $request->file('dokumentasi');
            $filename = time() . '_' . uniqid() . '_dokumentasi.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/ketidaksesuaian_benda_asing/' . $filename, $image);
            $data['dokumentasi'] = 'uploads/ketidaksesuaian_benda_asing/' . $filename;
        }

        $ketidaksesuaianBendaAsing->update($data);

        return redirect()->route('ketidaksesuaian-benda-asing.index')
            ->with('success', 'Data ketidaksesuaian benda asing berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $ketidaksesuaianBendaAsing = KetidaksesuaianBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        if ($user->role !== 'superadmin' && $ketidaksesuaianBendaAsing->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        // Delete associated file
        if ($ketidaksesuaianBendaAsing->dokumentasi && Storage::disk('public')->exists($ketidaksesuaianBendaAsing->dokumentasi)) {
            Storage::disk('public')->delete($ketidaksesuaianBendaAsing->dokumentasi);
        }

        $ketidaksesuaianBendaAsing->delete();

        return redirect()->route('ketidaksesuaian-benda-asing.index')
            ->with('success', 'Data ketidaksesuaian benda asing berhasil dihapus.');
    }
    /**
 * Show logs for the specified resource.
 */
    public function showLogs($uuid)
    {
        $item = KetidaksesuaianBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $logs = \App\Models\KetidaksesuaianBendaAsingLog::where('ketidaksesuaian_benda_asing_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.ketidaksesuaian_benda_asing.logs', compact('item', 'logs'));
    }

    /**
     * Get logs JSON for the specified resource.
     */
    public function getLogsJson($uuid)
    {
        $item = KetidaksesuaianBendaAsing::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = \App\Models\KetidaksesuaianBendaAsingLog::where('ketidaksesuaian_benda_asing_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    public function bulkExportPdf(Request $request)
    {
        try {
            // Add debug logging
            \Log::info('Bulk PDF request received', [
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            $request->validate([
                'tanggal' => 'nullable|date',
                'shift_id' => 'nullable|integer',
                'kode_form' => 'required|string|max:50'
            ]);

            $user = auth()->user();
            
            $query = KetidaksesuaianBendaAsing::with(['plan', 'user', 'shift', 'produk'])
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            // Apply filters
            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal', $request->tanggal);
            }
            
            if ($request->filled('shift_id')) {
                $query->where('shift_id', $request->shift_id);
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
                
                if ($request->tanggal) {
                    $filterInfo[] = 'Tanggal: ' . Carbon::parse($request->tanggal)->format('d-m-Y');
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
            $filename = 'ketidaksesuaian_benda_asing_bulk_' . date('Y-m-d_H-i-s') . '.pdf';

            \Log::info('Generating PDF', [
                'filename' => $filename,
                'kode_form' => $kode_form,
                'data_count' => $data->count()
            ]);

            $pdf = PDF::loadView('qc-sistem.ketidaksesuaian_benda_asing.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('F4', 'landscape');
            
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
            $ketidaksesuaianBendaAsing = KetidaksesuaianBendaAsing::where('uuid', $uuid)->firstOrFail();
            $user = auth()->user();
            $type = $request->input('type');
            
            // Validasi role dan tipe approval
            $allowedRoles = [
                'qc' => [1, 3, 5], // Role 1, 3, 5 bisa approve QC
                'produksi' => [2, 5], // Role 2 bisa approve Produksi
                'spv' => [4, 5] // Role 4 bisa approve SPV
            ];
            
            if (!isset($allowedRoles[$type]) || !in_array($user->id_role, $allowedRoles[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melakukan approval ini.'
                ], 403);
            }
            
            // Validasi sequential approval
            if ($type === 'produksi' && !$ketidaksesuaianBendaAsing->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
                ], 400);
            }
            
            if ($type === 'spv' && !$ketidaksesuaianBendaAsing->approved_by_produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
                ], 400);
            }
            
            // Cek apakah sudah di-approve sebelumnya
            $approvalField = "approved_by_{$type}";
            if ($ketidaksesuaianBendaAsing->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui sebelumnya.'
                ], 400);
            }
            
            // Update approval
            $ketidaksesuaianBendaAsing->update([
                $approvalField => true,
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now()
            ]);
            
            // Log activity (jika ada model log)
            // KetidaksesuaianBendaAsingLog::create([
            //     'ketidaksesuaian_benda_asing_id' => $ketidaksesuaianBendaAsing->id,
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
}
