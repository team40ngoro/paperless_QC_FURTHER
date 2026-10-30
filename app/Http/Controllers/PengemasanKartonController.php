<?php

namespace App\Http\Controllers;

use App\Models\PengemasanKarton;
use App\Models\PengemasanKartonLog;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\BeratProdukBox;
use App\Models\JenisProduk;
use App\Models\PengemasanProduk;
use App\Models\BeratProdukBag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PengemasanKartonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $query = PengemasanKarton::with(['plan', 'user','shift', 'beratProdukBag','beratProdukBox','pengemasanProduk.produk','pengemasanPlastik'])
            ->withCount('dokumentasi');

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->whereHas('pengemasanProduk.produk', function($q) use ($search) {
                $q->where('nama_produk', 'LIKE', '%' . $search . '%');
            });
        }

        $pengemasanKarton = $query->orderBy('created_at', 'desc')
            ->paginate(10);
      
        return view('qc-sistem.pengemasan_karton.index', compact('pengemasanKarton', 'search'));
    }
      public function create()
    {
        

          $user = auth()->user();
        $queryKarton = BeratProdukBox::with(['plan', 'shift', 'pengemasanProduk', 'pengemasanProduk.produk','pengemasanPlastik','beratProdukPack']);

        $prefill = [];
        if (request()->filled('id_berat_produk_box')) {
            $selectedBox = BeratProdukBox::with(['beratProdukPack', 'pengemasanPlastik', 'pengemasanProduk'])
                ->find(request('id_berat_produk_box'));

            if ($selectedBox) {
                $prefill = [
                    'shift_id' => $selectedBox->id_shift,
                    'id_berat_produk_box' => $selectedBox->id,
                    'id_berat_produk_bag' => $selectedBox->id_berat_produk_bag,
                    'id_pengemasan_plastik' => $selectedBox->id_pengemasan_plastik,
                    'id_pengemasan_produk' => $selectedBox->id_pengemasan_produk,
                ];
            }
        }
       
        if ($user->role === 'superadmin') {
             $produks = JenisProduk::all();
              $plans = Plan::all();
        $shifts = DataShift::all();
         $pengemasanKartons = $queryKarton->whereDate('tanggal', now()->toDateString())
         ->whereNotIn('id', function($query) {
             $query->select('id_berat_produk_box')
                   ->from('pengemasan_karton')
                   ->whereDate('created_at', now()->toDateString());
         })->get();
        
        } else {
          
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $pengemasanKartons = $queryKarton->where('id_plan', $user->id_plan)->whereDate('created_at', now()->toDateString())
            ->whereNotIn('id', function($query) {
                $query->select('id_berat_produk_box')
                      ->from('pengemasan_karton')
                      ->whereDate('created_at', now()->toDateString());
            })->get();
           
        }

      
        return view('qc-sistem.pengemasan_karton.create', compact('plans', 'shifts', 'produks','pengemasanKartons', 'prefill'));
    }

     public function store(Request $request)
    {
       $user = Auth::user();
        $isSpecialRole = ($user->id_role == 2 || $user->id_role == 3);
        
        // Validasi berbeda berdasarkan role
        if ($isSpecialRole) {
            $request->validate([
                'shift_id' => 'required|exists:data_shift,id',
                'id_berat_produk_box' => 'required',
                'id_berat_produk_bag' => 'required',
                'id_pengemasan_produk' => 'required',
                'id_pengemasan_plastik' => 'required',
                'identitas_produk_pada_karton' => 'required|string|max:255',
                'standar_jumlah_karton' => 'required|string|max:255',
                'aktual_jumlah_karton' => 'required|string|max:255',
                'tanggal' => 'required|date_format:d-m-Y', // Hanya validasi format tanggal saja
                'jam' => 'required',
            ]);
        } else {
            $request->validate([
                'shift_id' => 'required|exists:data_shift,id',
                'id_berat_produk_box' => 'required',
                'id_berat_produk_bag' => 'required',
                'id_pengemasan_produk' => 'required',
                'id_pengemasan_plastik' => 'required',
                'identitas_produk_pada_karton' => 'required|string|max:255',
                'standar_jumlah_karton' => 'required|string|max:255',
                'aktual_jumlah_karton' => 'required|string|max:255',
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
            $tanggal = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
        }

        $data = $request->all();
        // Automatically set user_id from authenticated user
        $data['user_id'] = Auth::id();
        $data['id_plan'] = Auth::user()->id_plan;
        // Set tanggal yang sudah ditransformasi
        $data['tanggal'] = $tanggal;
        $data['jam'] = $request->jam;
    
        PengemasanKarton::create($data);
        return redirect()->route('pengemasan-karton.index')
            ->with('success', 'Data Pengemasan Karton berhasil ditambahkan.');
    }

      public function edit($uuid)
    {
        $pengemasanKarton = PengemasanKarton::where('uuid', $uuid)->firstOrFail();
         // Ambil semua data pengemasan karton beserta relasi yang diperlukan
    $pengemasanKartons = PengemasanKarton::with([
        'beratProdukBag',
        'pengemasanPlastik',
        'pengemasanProduk.produk'
    ])->get();
   
        $user = auth()->user();

        if ($user->role === 'superadmin') {
             $produks = JenisProduk::all();
              $plans = Plan::all();
        $shifts = DataShift::all();
        } else {
          
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
           
        }

        return view('qc-sistem.pengemasan_karton.edit', compact('pengemasanKarton', 'pengemasanKartons','plans', 'shifts', 'produks'));
    }


     public function update(Request $request, $uuid)
    {
        $pengemasanKarton = PengemasanKarton::where('uuid', $uuid)->firstOrFail();
        
        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'shift_id' => 'required|exists:data_shift,id',
          
            'identitas_produk_pada_karton' => 'required|string|max:255',
            'standar_jumlah_karton' => 'required|string|max:255',
            'aktual_jumlah_karton' => 'required|string|max:255',
            'tanggal' => 'required|date',
        ]);

        $data = $request->except(['user_id']); // Exclude user_id from updates
        // Format the date correctly for MySQL
        $data['tanggal'] = Carbon::parse($request->tanggal)->format('Y-m-d H:i:s');
        $pengemasanKarton->update($data);

        return redirect()->route('pengemasan-karton.index')
            ->with('success', 'Data Pengemasan Karton berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $pengemasanKarton = PengemasanKarton::where('uuid', $uuid)->firstOrFail();
        $pengemasanKarton->delete();

        return redirect()->route('pengemasan-karton.index')
            ->with('success', 'Data Pengemasan Karton berhasil dihapus.');
    }

    // Logging Methods
    public function showLogs($uuid)
    {
        $pengemasanKarton = PengemasanKarton::where('uuid', $uuid)->firstOrFail();
        
        // Check authorization - user can only view logs for their plan
        if (auth()->user()->role !== 'superadmin' && $pengemasanKarton->id_plan !== auth()->user()->id_plan) {
            abort(403, 'Unauthorized access to logs.');
        }

        return view('qc-sistem.pengemasan_karton.logs', compact('pengemasanKarton'));
    }

    public function getLogsJson($uuid)
    {
        $pengemasanKarton = PengemasanKarton::where('uuid', $uuid)->firstOrFail();
        
        // Check authorization
        if (auth()->user()->role !== 'superadmin' && $pengemasanKarton->id_plan !== auth()->user()->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = PengemasanKartonLog::where('pengemasan_karton_uuid', $uuid)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedLogs = $logs->map(function ($log) {
            return [
                'tanggal' => $log->created_at->format('d/m/Y H:i:s'),
                'user' => $log->user->name ?? 'Unknown',
                'role' => $log->user->role ?? 'Unknown',
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'deskripsi_perubahan' => $log->deskripsi_perubahan,
            ];
        });

        return response()->json($formattedLogs);
    }
}
