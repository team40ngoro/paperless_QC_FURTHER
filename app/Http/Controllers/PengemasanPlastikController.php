<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengemasanPlastik;
use App\Models\PengemasanPlastikLog;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\PengemasanProduk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class PengemasanPlastikController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = PengemasanPlastik::with(['plan', 'user', 'shift','pengemasanProduk', 'pengemasanProduk.produk'])
            ->withCount('beratProdukBag');

        if ($user->role != 'admin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if (!empty($search)) {
            $query->whereHas('pengemasanProduk.produk', function ($produkQuery) use ($search) {
                $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
            });
        }

        $perPage = request()->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50, 100]) ? $perPage : 10;

        $data = $query->orderBy('created_at', 'desc')->paginate($perPage);
        // dd($data->toArray());
       
        return view('qc-sistem.pengemasan_plastik.index', compact('data', 'search', 'perPage'));
    }
     public function create()
    {
        $user = Auth::user();
      
           $query = PengemasanProduk::with(['plan','shift', 'produk']);
        if ($user->role === 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
            $shifts = DataShift::all();
           $pengemasanProduks = PengemasanProduk::whereDate('tanggal', now()->toDateString())
           ->whereNotIn('id', function($query) {
               $query->select('id_pengemasan_produk')
                     ->from('pengemasan_plastik')
                     ->whereDate('created_at', now()->toDateString());
           })->get();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
           $pengemasanProduks=$query->where('id_plan', $user->id_plan)->whereDate('created_at', now()->toDateString())
           ->whereNotIn('id', function($query) {
               $query->select('id_pengemasan_produk')
                     ->from('pengemasan_plastik')
                     ->whereDate('created_at', now()->toDateString());
           })->get();
          
        }
    
        return view('qc-sistem.pengemasan_plastik.create', compact('produks', 'plans','shifts', 'pengemasanProduks'));
    }
       public function store(Request $request)
    {
        $user = auth()->user();
          $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);
              // Validasi berbeda berdasarkan role
    if ($isSpecialRole) {
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'id_pengemasan_produk' => 'required|exists:pengemasan_produk,id',
            'proses_penimbangan' => 'required|string|max:255',
            'proses_sealing' => 'required|string|max:255',
            'identitas_produk' => 'required|string|max:255',
            'nomor_md' => 'required|image|mimes:jpeg,png,jpg',
            'kode_kemasan_plastik' => 'required|string|max:255',
            'kekuatan_seal' => 'required|string|max:255',
            'tanggal' => 'required|date_format:d-m-Y', // Hanya validasi format tanggal saja
            'jam' => 'required',
        ]);
    } else {
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'id_pengemasan_produk' => 'required|exists:pengemasan_produk,id',
            'proses_penimbangan' => 'required|string|max:255',
            'proses_sealing' => 'required|string|max:255',
            'identitas_produk' => 'required|string|max:255',
            'nomor_md' => 'required|image|mimes:jpeg,png,jpg',
            'kode_kemasan_plastik' => 'required|string|max:255',
            'kekuatan_seal' => 'required|string|max:255',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required',
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
        $tanggal = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
    }

    $nomor_md = null;
    
     
        // Foto Kode Produksi
        if (!$request->hasFile('nomor_md') && $request->has('nomor_md')) {
            Log::warning('PengemasanPlastik nomor_md input present but no file uploaded', [
                'user_id' => $user->id ?? null,
                'id_plan' => $user->id_plan ?? null,
                'shift_id' => $request->shift_id ?? null,
                'id_pengemasan_produk' => $request->id_pengemasan_produk ?? null,
                'content_length' => $request->server('CONTENT_LENGTH'),
                'content_type' => $request->server('CONTENT_TYPE'),
            ]);
        }

        if ($request->hasFile('nomor_md')) {
            $file = $request->file('nomor_md');

            if (is_array($file)) {
                $file = collect($file)->first(function ($f) {
                    return $f instanceof UploadedFile;
                });
            }

            if (!$file instanceof UploadedFile) {
                Log::warning('PengemasanPlastik nomor_md is not an UploadedFile instance', [
                    'user_id' => $user->id ?? null,
                    'id_plan' => $user->id_plan ?? null,
                    'shift_id' => $request->shift_id ?? null,
                    'id_pengemasan_produk' => $request->id_pengemasan_produk ?? null,
                    'type' => is_object($file) ? get_class($file) : gettype($file),
                    'content_length' => $request->server('CONTENT_LENGTH'),
                    'content_type' => $request->server('CONTENT_TYPE'),
                ]);

                throw ValidationException::withMessages([
                    'nomor_md' => 'File Nomor MD tidak valid. Silakan upload ulang gambar.',
                ]);
            }

            if (!$file->isValid()) {
                Log::warning('PengemasanPlastik nomor_md UploadedFile invalid', [
                    'user_id' => $user->id ?? null,
                    'id_plan' => $user->id_plan ?? null,
                    'shift_id' => $request->shift_id ?? null,
                    'id_pengemasan_produk' => $request->id_pengemasan_produk ?? null,
                    'error' => $file->getError(),
                    'mime' => $file->getClientMimeType(),
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'content_length' => $request->server('CONTENT_LENGTH'),
                    'content_type' => $request->server('CONTENT_TYPE'),
                ]);

                throw ValidationException::withMessages([
                    'nomor_md' => 'File Nomor MD tidak valid. Silakan upload ulang gambar.',
                ]);
            }

            $filename = time() . '_' . uniqid() . '_nomormd.jpg';

            try {
                $image = Image::make($file)->encode('jpg', 70);
                Storage::disk('public')->put('uploads/nomor_md_dokumentasi/' . $filename, $image);
                $nomor_md_dokumentasi_path = 'uploads/nomor_md_dokumentasi/' . $filename;
            } catch (\Throwable $e) {
                Log::warning('PengemasanPlastik upload nomor_md failed', [
                    'message' => $e->getMessage(),
                    'class' => get_class($e),
                    'mime' => $file->getClientMimeType(),
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ]);

                throw ValidationException::withMessages([
                    'nomor_md' => 'Upload Nomor MD gagal. Silakan pilih ulang file (PNG/JPG) dari galeri lalu coba lagi.',
                ]);
            }
        }
    // Lanjutkan dengan create data
    $pengemasanProduk = PengemasanProduk::findOrFail($request->id_pengemasan_produk);

    PengemasanPlastik::create([
        'id_shift' => $request->shift_id,
        'id_pengemasan_produk' => $request->id_pengemasan_produk,
        'proses_penimbangan' => $request->proses_penimbangan,
        'proses_sealing' => $request->proses_sealing,
        'berat' => $pengemasanProduk->berat,
        'identitas_produk' => $request->identitas_produk,
        'nomor_md' => $nomor_md_dokumentasi_path ?? null,
        'kode_kemasan_plastik' => $request->kode_kemasan_plastik,
        'kekuatan_seal' => $request->kekuatan_seal,
        'tanggal' => $tanggal,
        'jam' => $request->jam,
        'user_id' => $user->id,
        'id_plan' => $user->id_plan,
        // field lainnya sesuai kebutuhan
    ]);

        // $request->validate([
        //     'shift_id' => 'required|exists:data_shift,id',
        //     // 'id_produk' => 'nullable|exists:jenis_produk,id',
        //     'id_pengemasan_produk' => 'required|exists:pengemasan_produk,id',
        //     'proses_penimbangan' => 'required|string|max:255',
        //     'proses_sealing' => 'required|string|max:255',
        //     'berat_pengemasan_produk' => 'required|string|max:255',
        //     'identitas_produk' => 'required|string|max:255',
        //     'nomor_md' => 'required|image|mimes:jpeg,png,jpg',
        //     'kode_kemasan_plastik' => 'required|string|max:255',
        //     'kekuatan_seal' => 'required|string|max:255',
        //    'tanggal' => 'required|date_format:d-m-Y H:i:s',
        // ]);

        //       $nomor_md = null;
        
        
        // // Foto Kode Produksi
        // if ($request->hasFile('nomor_md')) {
        //     $file = $request->file('nomor_md');
        //     $filename = time() . '_' . uniqid() . '_nomormd.jpg';
        //     $image = Image::make($file)->encode('jpg', 70);
        //     Storage::disk('public')->put('uploads/nomor_md_dokumentasi/' . $filename, $image);
        //     $nomor_md_dokumentasi_path = 'uploads/nomor_md_dokumentasi/' . $filename;
        // }
        
        // $data=[
        //     'uuid' => Str::uuid(),
        //     'user_id' => $user->id,
        //     'id_plan' => $user->id_plan,
        //     // 'id_produk'=> $request->id_pengemasan_produk,
        //     'id_shift' => $request->shift_id,
        
        //     'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
        //     'id_pengemasan_produk'=>$request->id_pengemasan_produk,
        //     'berat'=>$request->berat_pengemasan_produk,
        //     'proses_penimbangan'=>$request->proses_penimbangan,
        //     'proses_sealing'=>$request->proses_sealing,
        //     'identitas_produk'=>$request->identitas_produk,
        //     'nomor_md'=>$nomor_md_dokumentasi_path,
        //     'kode_kemasan_plastik'=>$request->kode_kemasan_plastik,
        //     'kekuatan_seal'=>$request->kekuatan_seal,
            
        // ];
    //      if ($request->filled('id_produk')) {
    //     $data['id_produk'] = $request->id_produk;
    // } else {
    //     $data['id_produk'] = $request->id_pengemasan_produk;
    // }
        // dd($data); 
            // $pengemasanPlastik = PengemasanPlastik::create($data);
        return redirect()->route('pengemasan-plastik.index')
            ->with('success', 'Data Pengemasan Plastik berhasil ditambahkan.');
    }

public function edit($uuid)
{
    $user = Auth::user();
    $item = PengemasanPlastik::where('uuid', $uuid)
        ->with(['pengemasanProduk', 'pengemasanProduk.produk', 'shift'])
        ->firstOrFail();
      

    $pengemasanProduks=$item->where('id_pengemasan_produk',$item->pengemasanProduk->id)->first();
    $produk = optional(optional($pengemasanProduks->pengemasanProduk)->produk);
   $kode_produksi = $pengemasanProduks->pengemasanProduk->kode_produksi;
    $berat = $pengemasanProduks->berat;
  

    if ($user->role === 'superadmin') {
        $produks = JenisProduk::all();
        $plans = Plan::all();
        $shifts = DataShift::all();
       
    } else {
        $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        $plans = Plan::where('id', $user->id_plan)->get();
        $shifts = DataShift::where('id_plan', $user->id_plan)->get();
       
    }
   
    return view('qc-sistem.pengemasan_plastik.edit', compact('item', 'produks', 'plans', 'shifts', 'pengemasanProduks','produk','kode_produksi','berat'));
}

public function update(Request $request, $uuid)
{
    $user = auth()->user();
    $item = PengemasanPlastik::where('uuid', $uuid)->firstOrFail();

    $request->validate([
        'shift_id' => 'required|exists:data_shift,id',
      
       
        'proses_penimbangan' => 'required|string|max:255',
        'proses_sealing' => 'required|string|max:255',
        'identitas_produk' => 'required|string|max:255',
        'kode_kemasan_plastik' => 'required|string|max:255',
        'kekuatan_seal' => 'required|string|max:255',
    'tanggal' => 'required|date_format:d-m-Y H:i:s',
        'nomor_md' => 'nullable|image|mimes:jpeg,png,jpg',
    ]);

    // Update file nomor_md jika ada upload baru
    if ($request->hasFile('nomor_md')) {
        // Hapus file lama jika ada
        if ($item->nomor_md && Storage::disk('public')->exists($item->nomor_md)) {
            Storage::disk('public')->delete($item->nomor_md);
        }
        $file = $request->file('nomor_md');
        $filename = time() . '_' . uniqid() . '_nomormd.jpg';
        $image = Image::make($file)->encode('jpg', 70);
        Storage::disk('public')->put('uploads/nomor_md_dokumentasi/' . $filename, $image);
        $item->nomor_md = 'uploads/nomor_md_dokumentasi/' . $filename;
    }

        $item->user_id = $user->id;
        $item->id_plan = $user->id_plan;
        $item->id_shift = $request->shift_id;
    
       
        $item->proses_penimbangan = $request->proses_penimbangan;
        $item->proses_sealing = $request->proses_sealing;
        $item->identitas_produk = $request->identitas_produk;
        $item->kode_kemasan_plastik = $request->kode_kemasan_plastik;
        $item->kekuatan_seal = $request->kekuatan_seal;
        $item->tanggal = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
        $item->save();

        return redirect()->route('pengemasan-plastik.index')
            ->with('success', 'Data Pengemasan Plastik berhasil diupdate.');
    }

    public function destroy($uuid)
    {
        $item = PengemasanPlastik::where('uuid', $uuid)->firstOrFail();

        // Hapus file nomor_md jika ada
        if ($item->nomor_md && Storage::disk('public')->exists($item->nomor_md)) {
            Storage::disk('public')->delete($item->nomor_md);
        }

        $item->delete();

        return redirect()->route('pengemasan-plastik.index')
            ->with('success', 'Data Pengemasan Plastik berhasil dihapus.');
    }

    /**
    * Show logs for a specific pengemasan plastik record
    */
    public function showLogs($uuid)
    {
        $user = auth()->user();
        
        // Check if user has access - allow superadmin, admin, spv, qc, fm/fl
        $allowedRoles = ['superadmin', 'admin', 'spv', 'qc', 'fm/fl'];
        $userRole = strtolower($user->role ?? '');
        
        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Unauthorized access');
        }
        
        $pengemasanPlastik = PengemasanPlastik::where('uuid', $uuid)->firstOrFail();
        
        $logs = PengemasanPlastikLog::where('pengemasan_plastik_uuid', $uuid)
            ->with(['user', 'pengemasanPlastik'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('qc-sistem.pengemasan_plastik.logs', compact('logs', 'pengemasanPlastik'));
    }
/**
 * Get logs data in JSON format for AJAX requests
 */
    public function getLogsJson($uuid)
    {
        $user = auth()->user();
        
        // Check if user has access
        if ($user->role !== 'superadmin' && $user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $pengemasanPlastik = PengemasanPlastik::where('uuid', $uuid)->firstOrFail();
        
        // Check if user can access this record
        if ($user->role !== 'superadmin' && $pengemasanPlastik->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $logs = PengemasanPlastikLog::where('pengemasan_plastik_uuid', $uuid)
            ->with(['user', 'pengemasanPlastik'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'data' => $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'uuid' => $log->uuid,
                    'user_name' => $log->user_name,
                    'user_role' => $log->user_role,
                    'aksi' => $log->aksi,
                    'nama_field' => $log->nama_field,
                    'deskripsi_perubahan' => $log->deskripsi_perubahan,
                    'ip_address' => $log->ip_address,
                    'created_at' => $log->created_at->format('d-m-Y H:i:s'),
                    'keterangan' => $log->keterangan
                ];
            })
        ]);
    }
}
