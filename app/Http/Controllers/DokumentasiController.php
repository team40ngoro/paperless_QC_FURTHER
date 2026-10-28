<?php

namespace App\Http\Controllers;

use App\Models\Dokumentasi;
use App\Models\JenisProduk;
use App\Models\Plan;
use App\Models\BeratProdukBox;
use App\Models\PengemasanProduk;
use App\Models\BeratProdukBag;
use App\Models\PengemasanPlastik;
use App\Models\PengemasanKarton;
use App\Models\DataShift;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use App\Models\DokumentasiLog;

class DokumentasiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        
        $query = Dokumentasi::with(['plan', 'user','shift', 'beratProdukBag','beratProdukBox','pengemasanProduk.produk','pengemasanPlastik','pengemasanKarton', 'qcApprover', 'produksiApprover', 'spvApprover']);

        if ($user->role != 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                // Search by related product name
                $q->whereHas('pengemasanProduk.produk', function($qp) use ($search) {
                    $qp->where('nama_produk', 'LIKE', '%' . $search . '%');
                })
                // Search by date
                ->orWhere('tanggal', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $query->orderBy('created_at', 'desc')->paginate(10);
       
        return view('qc-sistem.dokumentasi.index', compact('data', 'search'));
    }
    public function create()
    {
            $queryDokumentasi = PengemasanKarton::with(['plan', 'shift', 'pengemasanProduk', 'pengemasanProduk.produk','pengemasanPlastik','beratProdukBox','beratProdukBag']);

        $prefill = [];
        if (request()->filled('id_pengemasan_karton')) {
            $selectedKarton = PengemasanKarton::with(['beratProdukBox', 'beratProdukBag', 'pengemasanPlastik', 'pengemasanProduk'])
                ->find(request('id_pengemasan_karton'));

            if ($selectedKarton) {
                $prefill = [
                    'shift_id' => $selectedKarton->shift_id,
                    'id_pengemasan_karton' => $selectedKarton->id,
                    'id_berat_produk_box' => $selectedKarton->id_berat_produk_box,
                    'id_berat_produk_bag' => $selectedKarton->id_berat_produk_bag,
                    'id_pengemasan_plastik' => $selectedKarton->id_pengemasan_plastik,
                    'id_pengemasan_produk' => $selectedKarton->id_pengemasan_produk,
                ];
            }
        }

        $user = auth()->user();
        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $shifts = \App\Models\DataShift::all();
             $newDokumentasi = $queryDokumentasi->whereDate('tanggal', now()->toDateString())
             ->whereNotIn('id', function($query) {
                 $query->select('id_pengemasan_karton')
                       ->from('dokumentasi')
                       ->whereDate('created_at', now()->toDateString());
             })->get();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = \App\Models\DataShift::where('id_plan', $user->id_plan)->get();
               $newDokumentasi= $queryDokumentasi->where('id_plan', $user->id_plan)->whereDate('created_at', now()->toDateString())
               ->whereNotIn('id', function($query) {
                   $query->select('id_pengemasan_karton')
                         ->from('dokumentasi')
                         ->whereDate('created_at', now()->toDateString());
               })->get();
        }
       
        return view('qc-sistem.dokumentasi.create', compact('produks', 'shifts','newDokumentasi', 'prefill'));
    }
     public function store(Request $request)
    {
       $user = auth()->user();
    $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);
    
    // Validasi berbeda berdasarkan role
    if ($isSpecialRole) {
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date_format:d-m-Y', // Hanya validasi format tanggal saja
            'jam' => 'required',
            'id_berat_produk_box' => 'required',
            'id_berat_produk_bag' => 'required',
            'id_pengemasan_produk' => 'required',
            'id_pengemasan_plastik' => 'required',
            'id_pengemasan_karton' => 'required',
            'foto_kode_produksi' => 'required|image|mimes:jpeg,png,jpg',
            'qr_code' => 'required|image|mimes:jpeg,png,jpg',  
            'label_polyroll' => 'required|image|mimes:jpeg,png,jpg',
        ]);
    } else {
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required',
            'id_berat_produk_box' => 'required',
            'id_berat_produk_bag' => 'required',
            'id_pengemasan_produk' => 'required',
            'id_pengemasan_plastik' => 'required',
            'id_pengemasan_karton' => 'required',
            'foto_kode_produksi' => 'required|image|mimes:jpeg,png,jpg',
            'qr_code' => 'required|image|mimes:jpeg,png,jpg',  
            'label_polyroll' => 'required|image|mimes:jpeg,png,jpg',
        ]);
    }

    // Transform the date format
    if ($isSpecialRole) {
        // Untuk user dengan role 2 atau 3, gunakan format tanggal dari request tapi waktu dari now()
        $dateOnly = \Carbon\Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
        $timeNow = now()->format('H:i:s');
        $tanggal = $dateOnly . ' ' . $timeNow;
    } else {
        // Untuk user lain, gunakan format tanggal dan waktu dari request
        $tanggal = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
    }

    $foto_kode_produksi_path = null;
    $qr_code_path = null;
    $label_polyroll_path = null;

    // Foto Kode Produksi
    if ($request->hasFile('foto_kode_produksi')) {
        $file = $request->file('foto_kode_produksi');
        $filename = time() . '_' . uniqid() . '_kode.jpg';
        $image = Image::make($file)->encode('jpg', 70);
        Storage::disk('public')->put('uploads/foto_kode_produksi/' . $filename, $image);
        $foto_kode_produksi_path = 'uploads/foto_kode_produksi/' . $filename;
    }

    // QR Code
    if ($request->hasFile('qr_code')) {
        $file = $request->file('qr_code');
        $filename = time() . '_' . uniqid() . '_qr.jpg';
        $image = Image::make($file)->encode('jpg', 70);
        Storage::disk('public')->put('uploads/qr_code/' . $filename, $image);
        $qr_code_path = 'uploads/qr_code/' . $filename;
    }

    // Label Polyroll
    if ($request->hasFile('label_polyroll')) {
        $file = $request->file('label_polyroll');
        $filename = time() . '_' . uniqid() . '_polyroll.jpg';
        $image = Image::make($file)->encode('jpg', 70);
        Storage::disk('public')->put('uploads/label_polyroll/' . $filename, $image);
        $label_polyroll_path = 'uploads/label_polyroll/' . $filename;
    }

    $data = [
        'uuid' => Str::uuid(),
        'user_id' => $user->id,
        'id_plan' => $user->id_plan,
        'id_shift' => $request->shift_id,
        'tanggal' => $tanggal, // Gunakan tanggal yang sudah ditransformasi
        'jam' => $request->jam,
        'id_pengemasan_karton' => $request->id_pengemasan_karton,
        'id_berat_produk_box' => $request->id_berat_produk_box,
        'id_berat_produk_bag' => $request->id_berat_produk_bag,
        'id_pengemasan_plastik' => $request->id_pengemasan_plastik,
        'id_pengemasan_produk' => $request->id_pengemasan_produk,
        'foto_kode_produksi' => $foto_kode_produksi_path,
        'qr_code' => $qr_code_path,
        'label_polyroll' => $label_polyroll_path,
    ];

    $dokumentasi = Dokumentasi::create($data);

      
        return redirect()->route('dokumentasi.index')->with('success', 'Data berhasil disimpan');
    }
     public function edit($uuid)
    {
        $item = Dokumentasi::where('uuid', $uuid)->firstOrFail();
           $dokumentasi = Dokumentasi::with([
        'beratProdukBag',
        'beratProdukBox',
        'pengemasanPlastik',
        'pengemasanProduk.produk'
    ])->get();
   
        $user = auth()->user();

        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
           
            $shifts = \App\Models\DataShift::all();
        } else {
           
            $shifts = \App\Models\DataShift::where('id_plan', $user->id_plan)->get();
        }

  
        return view('qc-sistem.dokumentasi.edit', compact('item', 'shifts','dokumentasi'));
    }
    public function update(Request $request, $uuid)
    {
        $user = auth()->user();
        $dokumentasi = Dokumentasi::where('uuid', $uuid)->firstOrFail();

        // Authorization check
        if ($user->role !== 'superadmin' && $dokumentasi->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
          
            'foto_kode_produksi' => 'nullable|image|mimes:jpeg,png,jpg',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg',
            'label_polyroll' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        // Foto Kode Produksi
        if ($request->hasFile('foto_kode_produksi')) {
            if ($dokumentasi->foto_kode_produksi && Storage::disk('public')->exists($dokumentasi->foto_kode_produksi)) {
                Storage::disk('public')->delete($dokumentasi->foto_kode_produksi);
            }
            $file = $request->file('foto_kode_produksi');
            $filename = time() . '_' . uniqid() . '_kode.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/foto_kode_produksi/' . $filename, $image);
            $dokumentasi->foto_kode_produksi = 'uploads/foto_kode_produksi/' . $filename;
        }

        // QR Code
        if ($request->hasFile('qr_code')) {
            if ($dokumentasi->qr_code && Storage::disk('public')->exists($dokumentasi->qr_code)) {
                Storage::disk('public')->delete($dokumentasi->qr_code);
            }
            $file = $request->file('qr_code');
            $filename = time() . '_' . uniqid() . '_qr.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/qr_code/' . $filename, $image);
            $dokumentasi->qr_code = 'uploads/qr_code/' . $filename;
        }

        // Label Polyroll
        if ($request->hasFile('label_polyroll')) {
            if ($dokumentasi->label_polyroll && Storage::disk('public')->exists($dokumentasi->label_polyroll)) {
                Storage::disk('public')->delete($dokumentasi->label_polyroll);
            }
            $file = $request->file('label_polyroll');
            $filename = time() . '_' . uniqid() . '_polyroll.jpg';
            $image = Image::make($file)->encode('jpg', 70);
            Storage::disk('public')->put('uploads/label_polyroll/' . $filename, $image);
            $dokumentasi->label_polyroll = 'uploads/label_polyroll/' . $filename;
        }

        // Update data lain
        $dokumentasi->user_id = $user->id;
        $dokumentasi->id_plan = $user->id_plan;
        $dokumentasi->id_shift = $request->shift_id;
        $dokumentasi->tanggal = Carbon::parse($request->tanggal)->format('Y-m-d H:i:s');
     
        $dokumentasi->save();

        return redirect()->route('dokumentasi.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $user = auth()->user();
        $dokumentasi = Dokumentasi::where('uuid', $uuid)->firstOrFail();

        // Authorization check
        if ($user->role !== 'superadmin' && $dokumentasi->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        // Hapus file gambar jika ada
        if ($dokumentasi->foto_kode_produksi && Storage::disk('public')->exists($dokumentasi->foto_kode_produksi)) {
            Storage::disk('public')->delete($dokumentasi->foto_kode_produksi);
        }
        if ($dokumentasi->qr_code && Storage::disk('public')->exists($dokumentasi->qr_code)) {
            Storage::disk('public')->delete($dokumentasi->qr_code);
        }
        if ($dokumentasi->label_polyroll && Storage::disk('public')->exists($dokumentasi->label_polyroll)) {
            Storage::disk('public')->delete($dokumentasi->label_polyroll);
        }

        $dokumentasi->delete();

        return redirect()->route('dokumentasi.index')->with('success', 'Data berhasil dihapus');
    }
        /**
     * Tampilkan halaman logs untuk dokumentasi
     */
    public function showLogs($uuid)
    {
        $item = Dokumentasi::where('uuid', $uuid)->firstOrFail();
        
        $logs = DokumentasiLog::where('dokumentasi_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(5);
        
        return view('qc-sistem.dokumentasi.logs', compact('item', 'logs'));
    }

    /**
     * Get logs data untuk DataTables
     */
    public function getLogsJson($uuid)
    {
        $dokumentasi = Dokumentasi::where('uuid', $uuid)->firstOrFail();
        
        // Cek authorization
        $user = Auth::user();
        if ($user->role->nama_role !== 'superadmin' && $dokumentasi->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = DokumentasiLog::where('dokumentasi_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'tanggal' => $log->created_at->format('d/m/Y H:i:s'),
                    'user' => $log->user_name . ' (' . $log->user_role . ')',
                    'field' => $log->nama_field,
                    'perubahan' => $log->deskripsi_perubahan,
                    'ip_address' => $log->ip_address
                ];
            });

        return response()->json(['data' => $logs]);
    }


     public function bulkExportPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'nullable|date',
            'shift_id' => 'nullable|exists:data_shift,id',
            'id_produk' => 'nullable|exists:jenis_produk,id',
            'kode_form' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $tanggal = $request->tanggal;
        $shiftId = $request->shift_id;
        $idProduk = $request->id_produk;
        $kodeForm = $request->kode_form;

	

        $pengemasanProdukQuery = PengemasanProduk::with(['plan','user', 'produk', 'shift']);
        $pengemasanPlastikQuery = PengemasanPlastik::with(['plan', 'user', 'shift','pengemasanProduk', 'pengemasanProduk.produk']);
        $beratProdukPackQuery = BeratProdukBag::with(['plan', 'user','pengemasanProduk','pengemasanProduk.produk','pengemasanPlastik', 'shift', 'data_bag']);
        $beratProdukBoxQuery= BeratProdukBox::with(['plan', 'user', 'beratProdukPack', 'shift', 'data_box', 'pengemasanProduk', 'pengemasanProduk.produk','pengemasanPlastik']);
        $pengemasanKartonQuery = PengemasanKarton::with(['plan', 'user','shift', 'beratProdukBag','beratProdukBox','pengemasanProduk.produk','pengemasanPlastik']);
        $DokumentasiQuery = Dokumentasi::with(['plan', 'user','shift', 'beratProdukBag','beratProdukBox','pengemasanProduk.produk','pengemasanPlastik','pengemasanKarton']);

       
        // Apply role-based filtering
        if ($user->role !== 'superadmin') {
            // $bahanBakuQuery->where('id_plan', $user->id_plan);
            // $prosesMarinadeQuery->where('id_plan', $user->id_plan);
            // $prosesTumblingQuery->where('id_plan', $user->id_plan);

            $pengemasanProdukQuery->where('id_plan', $user->id_plan);
            $pengemasanPlastikQuery->where('id_plan', $user->id_plan);
            $beratProdukPackQuery->where('id_plan', $user->id_plan);
            $beratProdukBoxQuery->where('id_plan', $user->id_plan);
            $pengemasanKartonQuery->where('id_plan', $user->id_plan);
            $DokumentasiQuery->where('id_plan', $user->id_plan);
        
        }

        // Apply filters
        if ($tanggal) {

            // $bahanBakuQuery->whereDate('tanggal', $tanggal);
            // $prosesMarinadeQuery->whereDate('tanggal', $tanggal);
            // $prosesTumblingQuery->whereDate('tanggal', $tanggal);

           $pengemasanProdukQuery->whereDate('tanggal', Carbon::parse($tanggal)->format('Y-m-d'));
            $pengemasanPlastikQuery->whereDate('tanggal', Carbon::parse($tanggal)->format('Y-m-d'));
            $beratProdukPackQuery->whereDate('tanggal', Carbon::parse($tanggal)->format('Y-m-d'));
            $beratProdukBoxQuery->whereDate('tanggal', Carbon::parse($tanggal)->format('Y-m-d'));
            $pengemasanKartonQuery->whereDate('tanggal', Carbon::parse($tanggal)->format('Y-m-d'));
            $DokumentasiQuery->whereDate('tanggal', Carbon::parse($tanggal)->format('Y-m-d'));
        }

        if ($shiftId) {

            $pengemasanProdukQuery->where('id_shift', $shiftId);
            $pengemasanPlastikQuery->where('id_shift', $shiftId);
            $beratProdukPackQuery->where('id_shift', $shiftId);
            $beratProdukBoxQuery->where('id_shift', $shiftId);
            $pengemasanKartonQuery->where('shift_id', $shiftId);
            $DokumentasiQuery->where('id_shift', $shiftId);

            // $bahanBakuQuery->where('shift_id', $shiftId);
            // $prosesMarinadeQuery->where('id_shift', $shiftId);
            // $prosesTumblingQuery->where('shift_id', $shiftId);


        }

        if ($idProduk) {
               $pengemasanProdukQuery->where('id_produk', $idProduk);
            // $bahanBakuQuery->where('id_produk', $idProduk);
            // Note: proses_marinade doesn't have id_produk, it uses id_jenis_marinade
            // Skip filtering proses_marinade by product for now
            // $prosesTumblingQuery->where('id_produk', $idProduk);
        }

        // Get the data
        // $bahanBakuData = $bahanBakuQuery->get();
        // $prosesMarinadeData = $prosesMarinadeQuery->get();
        // $prosesTumblingData = $prosesTumblingQuery->get();
        $pengemasanProdukData = $pengemasanProdukQuery->get();
        $pengemasanPlastikData = $pengemasanPlastikQuery->get();
        $beratProdukPackData = $beratProdukPackQuery->get();
        $beratProdukBoxData = $beratProdukBoxQuery->get();
        $pengemasanKartonData = $pengemasanKartonQuery->get();
        $DokumentasiData = $DokumentasiQuery->get();
     
        // Update kode_form for all filtered records
        // if ($bahanBakuData->isNotEmpty()) {
            //     $bahanBakuQuery->update(['kode_form' => $kodeForm]);
            // }
            // if ($prosesMarinadeData->isNotEmpty()) {
                //     $prosesMarinadeQuery->update(['kode_form' => $kodeForm]);
                // }
                // if ($prosesTumblingData->isNotEmpty()) {
                    //     $prosesTumblingQuery->update(['kode_form' => $kodeForm]);
                    // }
                    
                    // Get shift and product information for filter display
                    $shift = $shiftId ? DataShift::find($shiftId) : null;
                    $produk = $idProduk ? JenisProduk::find($idProduk) : null;
                    
                    $filterInfo = [
                        'tanggal' => $tanggal ? Carbon::parse($tanggal)->format('d-m-Y') : 'Semua Tanggal',
                        'shift' => $shift ? $shift->shift : 'Semua Shift',
                        'produk' => $produk ? $produk->nama_produk : 'Semua Produk',
                        'kode_form' => $kodeForm
                    ];
                    
                    // Check if no data found - if ANY of the collections is empty, show no data message
                    if ($pengemasanProdukData->isEmpty() || $pengemasanPlastikData->isEmpty() || $beratProdukPackData->isEmpty() || $beratProdukBoxData->isEmpty() || $pengemasanKartonData->isEmpty() || $DokumentasiData->isEmpty()) {
                        $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
                        $filterInfo = [];
            
            if ($tanggal) {
                $filterInfo[] = 'Tanggal: ' . Carbon::parse($tanggal)->format('d-m-Y');
            }
            if ($shift) {
                $filterInfo[] = 'Shift: ' . $shift->shift;
            }
            if ($produk) {
                $filterInfo[] = 'Produk: ' . $produk->nama_produk;
            }
            if ($kodeForm) {
                $filterInfo[] = 'Kode Form: ' . $kodeForm;
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
        
        // Update kode_form only if all data collections have data
        if($pengemasanProdukData->isNotEmpty()){
            $pengemasanProdukQuery->update(['kode_form' => $kodeForm]);
        }
        
    // Generate PDF
        $pdf = Pdf::loadView('qc-sistem.dokumentasi.unified_export_pdf', compact(
            'pengemasanProdukData',
            'pengemasanPlastikData',
            'beratProdukPackData',
            'beratProdukBoxData',
            'DokumentasiData',
            'pengemasanKartonData',
          
            'filterInfo'
        ));

        $filename = 'Unified_Production_Export_' . ($tanggal ? Carbon::parse($tanggal)->format('Y-m-d') : date('Y-m-d')) . '_' . ($shift ? $shift->shift : 'All') . '.pdf';

        return $pdf->download($filename);
    }

    public function approve(Request $request, $uuid)
    {
        try {
            $dokumentasi = Dokumentasi::where('uuid', $uuid)->firstOrFail();
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
            if ($type === 'produksi' && !$dokumentasi->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
                ], 400);
            }
            
            if ($type === 'spv' && !$dokumentasi->approved_by_produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
                ], 400);
            }
            
            // Cek apakah sudah di-approve sebelumnya
            $approvalField = "approved_by_{$type}";
            if ($dokumentasi->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui sebelumnya.'
                ], 400);
            }
            
            // Update approval
            $dokumentasi->update([
                $approvalField => true,
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now()
            ]);
            
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
