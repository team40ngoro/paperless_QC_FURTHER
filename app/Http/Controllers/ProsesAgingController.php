<?php

namespace App\Http\Controllers;

use App\Models\ProsesAging;
use App\Models\JenisProduk;
use App\Models\ProsesTumbling;
use App\Models\DataShift;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ProsesAgingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = ProsesAging::with(['plan', 'user', 'produk', 'prosesTumbling.shift']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if ($search) {
            $query->where('tanggal', 'LIKE', '%' . $search . '%');
        }

        $prosesAging = $query->orderBy('created_at', 'desc')
                             ->paginate(10);
        
        // Get shifts and produks for modal
        $shifts = DataShift::all();
        $produks = JenisProduk::all();
        
        return view('qc-sistem.proses_aging.index', compact('prosesAging', 'shifts', 'produks'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $produks = JenisProduk::all();
        $prosesTumbling = null;
        
        // Jika ada parameter proses_tumbling_id, ambil data proses tumbling
        if ($request->has('proses_tumbling_id')) {
            $prosesTumbling = ProsesTumbling::where('id', $request->proses_tumbling_id)->first();
            if (!$prosesTumbling) {
                return redirect()->route('proses-tumbling.index')
                    ->with('error', 'Data Proses Tumbling tidak ditemukan.');
            }
        }
        // Fallback: jika ada parameter proses_tumbling_uuid, ambil berdasarkan uuid
        elseif ($request->has('proses_tumbling_uuid')) {
            $prosesTumbling = ProsesTumbling::where('uuid', $request->proses_tumbling_uuid)->first();
            if (!$prosesTumbling) {
                return redirect()->route('proses-tumbling.index')
                    ->with('error', 'Data Proses Tumbling tidak ditemukan.');
            }
        }
        
        return view('qc-sistem.proses_aging.create', compact('produks', 'prosesTumbling'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'proses_tumbling_id' => 'nullable|exists:proses_tumbling,id',
            'proses_tumbling_uuid' => 'nullable|exists:proses_tumbling,uuid',
            'tanggal' => 'required|date_format:Y-m-d',
            'jam' => 'required|date_format:H:i',
            'waktu_mulai_aging' => 'required|string|max:255',
            'waktu_selesai_aging' => 'required|string|max:255',
            'suhu_produk' => 'required|string|max:255',
            'kondisi_produk' => 'required|string|max:255',
        ]);

        // Jika hanya ada proses_tumbling_uuid, cari id-nya
        $proses_tumbling_id = $request->proses_tumbling_id;
        $proses_tumbling_uuid = $request->proses_tumbling_uuid;
        
        if (!$proses_tumbling_id && $proses_tumbling_uuid) {
            $prosesTumbling = ProsesTumbling::where('uuid', $proses_tumbling_uuid)->first();
            if ($prosesTumbling) {
                $proses_tumbling_id = $prosesTumbling->id;
            }
        }

        ProsesAging::create([
            'uuid' => Str::uuid(),
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'id_produk' => $request->id_produk,
            'proses_tumbling_id' => $proses_tumbling_id,
            'proses_tumbling_uuid' => $proses_tumbling_uuid,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'waktu_mulai_aging' => $request->waktu_mulai_aging,
            'waktu_selesai_aging' => $request->waktu_selesai_aging,
            'suhu_produk' => $request->suhu_produk,
            'kondisi_produk' => $request->kondisi_produk,
        ]);

        return redirect()->route('proses-aging.index')
            ->with('success', 'Data proses aging berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $prosesAging = ProsesAging::where('uuid', $uuid)->firstOrFail();
        return view('qc-sistem.proses_aging.show', compact('prosesAging'));
    }

    public function edit($uuid)
    {
        $prosesAging = ProsesAging::where('uuid', $uuid)->firstOrFail();
        $produks = JenisProduk::all();
        
        return view('qc-sistem.proses_aging.edit', compact('prosesAging', 'produks'));
    }

    public function update(Request $request, $uuid)
    {
        $user = auth()->user();
        $prosesAging = ProsesAging::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'tanggal' => 'required|date_format:Y-m-d',
            'jam' => 'nullable',
            'waktu_mulai_aging' => 'required|string|max:255',
            'waktu_selesai_aging' => 'required|string|max:255',
            'suhu_produk' => 'required|string|max:255',
            'kondisi_produk' => 'required|string|max:255',
        ]);

        $prosesAging->update([
            'id_produk' => $request->id_produk,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'waktu_mulai_aging' => $request->waktu_mulai_aging,
            'waktu_selesai_aging' => $request->waktu_selesai_aging,
            'suhu_produk' => $request->suhu_produk,
            'kondisi_produk' => $request->kondisi_produk,
        ]);

        return redirect()->route('proses-aging.index')
            ->with('success', 'Data proses aging berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $prosesAging = ProsesAging::where('uuid', $uuid)->firstOrFail();
        $prosesAging->delete();

        return redirect()->route('proses-aging.index')
            ->with('success', 'Data proses aging berhasil dihapus.');
    }
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'tanggal' => 'nullable|date',
            'shift_id' => 'nullable|exists:data_shift,id',
            'id_produk' => 'nullable|exists:jenis_produk,id',
            'kode_form' => 'required|string|max:255',
        ]);
        
        $query = ProsesAging::with(['plan', 'user', 'produk', 'prosesTumbling.shift']);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        // Apply filters
        if ($request->tanggal) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        if ($request->shift_id) {
            $query->whereHas('prosesTumbling', function($q) use ($request) {
                $q->where('shift_id', $request->shift_id);
            });
        }
        
        if ($request->id_produk) {
            $query->where('id_produk', $request->id_produk);
        }
        
        $data = $query->orderBy('tanggal', 'desc')->get();

        // Update kode_form for all filtered data (only from modal)
        if (!$data->isEmpty()) {
            $dataIds = $data->pluck('id')->toArray();
            ProsesAging::whereIn('id', $dataIds)->update(['kode_form' => $request->kode_form]);

            // Refresh data to include updated kode_form
            $data = $data->map(function ($item) use ($request) {
                $item->kode_form = $request->kode_form;
                return $item;
            });
        }
        
        // Prepare filter info
        $shift = $request->shift_id ? DataShift::find($request->shift_id) : null;
        $produk = $request->id_produk ? JenisProduk::find($request->id_produk) : null;
        
        $filterInfo = [
            'tanggal' => $request->tanggal ? Carbon::parse($request->tanggal)->format('d-m-Y') : 'Semua Tanggal',
            'shift' => $shift ? $shift->shift : 'Semua Shift',
            'produk' => $produk ? $produk->nama_produk : 'Semua Produk',
            'kode_form' => $request->kode_form,
        ];
        
        // Check if no data found
        if ($data->isEmpty()) {
            $errorMessage = 'Tidak ada data Proses Aging yang sesuai dengan filter yang dipilih.';
            
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Data Tidak Ditemukan</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 40px; text-align: center; }
                    .container { max-width: 600px; margin: 0 auto; padding: 40px; border: 2px dashed #ccc; background-color: #f9f9f9; }
                    h1 { color: #d9534f; margin-bottom: 20px; }
                    .message { font-size: 16px; color: #666; margin-bottom: 30px; }
                    .filter-info { background-color: #fff; padding: 20px; border-radius: 5px; margin: 20px 0; }
                    .filter-info h4 { margin-top: 0; color: #333; }
                    .filter-info ul { text-align: left; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>Data Proses Aging Tidak Ditemukan</h1>
                    <p class="message">' . $errorMessage . '</p>
                    <div class="filter-info">
                        <h4>Filter yang digunakan:</h4>
                        <ul>
                            <li><strong>Tanggal:</strong> {{ $filterInfo["tanggal"] }}</li>
                            <li><strong>Shift:</strong> {{ $filterInfo["shift"] }}</li>
                            <li><strong>Produk:</strong> {{ $filterInfo["produk"] }}</li>
                        </ul>
                    </div>
                </div>
            </body>
            </html>';
            
            return response($html)->header('Content-Type', 'text/html');
        }
        
        $pdf = Pdf::loadView('qc-sistem.proses_aging.export_pdf', compact('data', 'filterInfo'))
                ->setPaper('letter', 'portrait');

        $safeKodeForm = (string) ($filterInfo['kode_form'] ?? '');
        $safeKodeForm = str_replace(['/', '\\', ':'], '-', $safeKodeForm);
        $safeKodeForm = trim($safeKodeForm);

        $filename = 'proses-aging-' . $safeKodeForm . '-' . date('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function approve(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'uuid' => 'required|exists:proses_aging,uuid',
            'type' => 'required|in:qc,produksi,spv',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $item = ProsesAging::where('uuid', $request->uuid)->firstOrFail();

            // Check role-based permissions
            $userRole = $user->id_role ?? null;
        
        if ($request->type === 'qc') {
            // Only role 1, 3, 5 can approve as QC
            if (!in_array($userRole, [1, 3, 5])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menyetujui sebagai QC'
                ], 403);
            }
            
            if ($item->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui oleh QC sebelumnya'
                ], 400);
            }
            
            $item->approved_by_qc = true;
            $item->qc_approved_by = $user->id;
            $item->qc_approved_at = now();
            
        } elseif ($request->type === 'produksi') {
            // Only role 2 can approve as Produksi
            if ($userRole !== 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menyetujui sebagai Produksi'
                ], 403);
            }
            
            // Check if QC approval exists
            if (!$item->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menunggu persetujuan QC terlebih dahulu'
                ], 400);
            }
            
            if ($item->approved_by_produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui oleh Produksi sebelumnya'
                ], 400);
            }
            
            $item->approved_by_produksi = true;
            $item->produksi_approved_by = $user->id;
            $item->produksi_approved_at = now();
            
        } elseif ($request->type === 'spv') {
            // Only role 4 can approve as SPV
            if ($userRole !== 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menyetujui sebagai SPV'
                ], 403);
            }
            
            // Check if Produksi approval exists
            if (!$item->approved_by_produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menunggu persetujuan Produksi terlebih dahulu'
                ], 400);
            }
            
            if ($item->approved_by_spv) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui oleh SPV sebelumnya'
                ], 400);
            }
            
            $item->approved_by_spv = true;
            $item->spv_approved_by = $user->id;
            $item->spv_approved_at = now();
        }

        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
    }
}