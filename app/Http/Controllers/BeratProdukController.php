<?php

namespace App\Http\Controllers;

use App\Models\BeratProdukBag;
use App\Models\BeratProdukBox;
use App\Models\BeratProdukBagLog;
use App\Models\BeratProdukBoxLog;
use App\Models\PengemasanPlastik;
use App\Models\PengemasanProduk;
use App\Models\DataBag;
use App\Models\DataBox;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;


class BeratProdukController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $produk_id = $request->input('produk_id');
        $userRole = strtolower($user->role ?? '');
        $isSuperadmin = $userRole === 'superadmin';
        $produks = $isSuperadmin ? JenisProduk::all() : JenisProduk::where('id_plan', $user->id_plan)->get();

        $queryBag = BeratProdukBag::with(['plan', 'pengemasanProduk','pengemasanProduk.produk','pengemasanPlastik', 'shift', 'data_bag'])
            ->withCount('beratProdukBox')
            ->orderBy('created_at', 'desc');
        $queryBox = BeratProdukBox::with(['plan', 'beratProdukPack', 'shift', 'data_box', 'pengemasanProduk', 'pengemasanProduk.produk','pengemasanPlastik'])
            ->withCount('pengemasanKarton')
            ->orderBy('created_at', 'desc');

        if ($userRole !== 'superadmin') {
            $queryBag->where('id_plan', $user->id_plan);
            $queryBox->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if($search) {
            $queryBag->where(function($q) use ($search) {
                $q->whereHas('pengemasanProduk', function($q2) use ($search) {
                    $q2->where('kode_produksi', 'LIKE', '%' . $search . '%')
                       ->orWhereHas('produk', function($q3) use ($search) {
                           $q3->where('nama_produk', 'LIKE', '%' . $search . '%');
                       });
                });
            });

            $queryBox->where(function($q) use ($search) {
                $q->whereHas('pengemasanProduk', function($q2) use ($search) {
                    $q2->where('kode_produksi', 'LIKE', '%' . $search . '%')
                       ->orWhereHas('produk', function($q3) use ($search) {
                           $q3->where('nama_produk', 'LIKE', '%' . $search . '%');
                       });
                });
            });
        }

        if ($produk_id) {
            $queryBag->whereHas('pengemasanProduk', function($q) use ($produk_id) {
                $q->where('id_produk', $produk_id);
            });
            $queryBox->whereHas('pengemasanProduk', function($q) use ($produk_id) {
                $q->where('id_produk', $produk_id);
            });
        }

        $berat_produk_bag = $queryBag->paginate(10, ['*'], 'bag_page');
        $berat_produk_box = $queryBox->paginate(10, ['*'], 'box_page');
        
        return view('qc-sistem.berat_produk_bag_box.index', compact('berat_produk_bag', 'berat_produk_box', 'produks', 'produk_id'));
    }

    public function create()
    {
        $user = Auth::user();
        $data_bags = DataBag::all();
        $data_boxes = DataBox::all();
        $queryPlastik = PengemasanPlastik::with(['plan','shift', 'pengemasanProduk', 'pengemasanProduk.produk']);
        $queryBox = BeratProdukBag::with(['plan', 'shift', 'pengemasanProduk', 'pengemasanProduk.produk','pengemasanPlastik']);
        
       


        if ($user->role == 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
            $shifts = DataShift::all();
            $pengemasanPlastiks = $queryPlastik->whereDate('tanggal', now()->toDateString())
            ->whereNotIn('id', function($query) {
                $query->select('id_pengemasan_plastik')
                      ->from('berat_produk_bag')
                      ->whereDate('created_at', now()->toDateString());
            })->get();
            $beratProdukPacks = $queryBox->whereDate('tanggal', now()->toDateString())
            ->whereNotIn('id', function($query) {
                $query->select('id_berat_produk_bag')
                      ->from('berat_produk_box')
                      ->whereDate('created_at', now()->toDateString());
            })->get();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $pengemasanPlastiks=$queryPlastik->where('id_plan', $user->id_plan)->whereDate('created_at', now()->toDateString())
            ->whereNotIn('id', function($query) {
                $query->select('id_pengemasan_plastik')
                      ->from('berat_produk_bag')
                      ->whereDate('created_at', now()->toDateString());
            })->get();
            $beratProdukPacks = $queryBox->where('id_plan', $user->id_plan)->whereDate('created_at', now()->toDateString())
            ->whereNotIn('id', function($query) {
                $query->select('id_berat_produk_bag')
                      ->from('berat_produk_box')
                      ->whereDate('created_at', now()->toDateString());
            })->get();
        }
       
    //   dd($beratProdukPacks->toArray());
       return view('qc-sistem.berat_produk_bag_box.create', compact('plans', 'produks', 'shifts', 'data_bags', 'data_boxes','pengemasanPlastiks','beratProdukPacks'));
    }

    public function store_bag(Request $request)
    {
    $user = Auth::user();
    $userRole = strtolower($user->role ?? '');
    $isSpecialRole = in_array($userRole, ['qc inspector', 'produksi'], true);
    if ($isSpecialRole) {
        $validated = $request->validate([
            'id_pengemasan_plastik' => 'required|array',
            'id_pengemasan_plastik.*' => 'required|exists:pengemasan_plastik,id',
            'id_pengemasan_produk' => 'required|array',
            'id_pengemasan_produk.*' => 'nullable|exists:pengemasan_produk,id',
            'id_shift' => 'required|array',
            'id_shift.*' => 'required',
            'line' => 'required|array',
            'line.*' => 'required|integer|min:1|max:8',
            'id_data_bag' => 'required|array',
            'id_data_bag.*' => 'required',
            'tanggal' => 'required|date_format:d-m-Y', // Hanya validasi format tanggal saja
            'jam' => 'required',
            'berat_aktual_1' => 'required|array',
            'berat_aktual_1.*' => 'required|numeric',
            'berat_aktual_2' => 'required|array',
            'berat_aktual_2.*' => 'required|numeric',
            'berat_aktual_3' => 'required|array',
            'berat_aktual_3.*' => 'required|numeric',
            'rata_rata_berat' => 'required|array',
            'rata_rata_berat.*' => 'required|numeric',
        ]);
    } else {
        $validated = $request->validate([
            'id_pengemasan_plastik' => 'required|array',
            'id_pengemasan_plastik.*' => 'required|exists:pengemasan_plastik,id',
            'id_pengemasan_produk' => 'required|array',
            'id_pengemasan_produk.*' => 'nullable|exists:pengemasan_produk,id',
            'id_shift' => 'required|array',
            'id_shift.*' => 'required',
            'line' => 'required|array',
            'line.*' => 'required|integer|min:1|max:8',
            'id_data_bag' => 'required|array',
            'id_data_bag.*' => 'required',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required',
            'berat_aktual_1' => 'required|array',
            'berat_aktual_1.*' => 'required|numeric',
            'berat_aktual_2' => 'required|array',
            'berat_aktual_2.*' => 'required|numeric',
            'berat_aktual_3' => 'required|array',
            'berat_aktual_3.*' => 'required|numeric',
            'rata_rata_berat' => 'required|array',
            'rata_rata_berat.*' => 'required|numeric',
        ]);
    }

    // Set common data
    $userId = Auth::id();
    $idPlan = $user->id_plan;
    
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

    // Loop through each dynamic form entry
    $count = count($validated['berat_aktual_1']);
    for ($i = 0; $i < $count; $i++) {
        $pengemasanPlastik = PengemasanPlastik::find($validated['id_pengemasan_plastik'][$i]);
        $idPengemasanProduk = $validated['id_pengemasan_produk'][$i] ?? null;

        if (!$idPengemasanProduk) {
            $idPengemasanProduk = $pengemasanPlastik->id_pengemasan_produk ?? null;
        }

        if (!$idPengemasanProduk) {
            throw ValidationException::withMessages([
                'id_pengemasan_produk' => 'Produk tidak valid. Silakan pilih ulang Produk pada form C1.',
            ]);
        }
        
        BeratProdukBag::create([
            'id_pengemasan_plastik' => $validated['id_pengemasan_plastik'][$i],
            'id_pengemasan_produk' => $idPengemasanProduk,
            'id_shift' => $validated['id_shift'][$i],
            'id_data_bag' => $validated['id_data_bag'][$i],
            'line' => $validated['line'][$i],
            'tanggal' => $tanggal,
            'jam' => $request->jam, // Tambahkan field jam
            'berat_aktual_1' => $validated['berat_aktual_1'][$i],
            'berat_aktual_2' => $validated['berat_aktual_2'][$i],
            'berat_aktual_3' => $validated['berat_aktual_3'][$i],
            'rata_rata_berat' => $validated['rata_rata_berat'][$i],
            'user_id' => $userId,
            'id_plan' => $idPlan,
        ]);
    }

        return redirect()->route('berat-produk.index')->with('success', 'Data Berat Produk Bag berhasil ditambahkan (' . $count . ' data).');
    }

    public function store_box(Request $request)
    {
    $user = Auth::user();
    $userRole = strtolower($user->role ?? '');
    $isSpecialRole = in_array($userRole, ['qc inspector', 'produksi'], true);
    
    // Validasi berbeda berdasarkan role
    if ($isSpecialRole) {
        $validated = $request->validate([
            'id_pengemasan_plastik' => 'required',
            'id_pengemasan_produk' => 'required',
            'id_berat_produk_bag' => 'required',
            'id_shift' => 'required',
            'id_data_box' => 'required',
            'tanggal' => 'required|date_format:d-m-Y', // Hanya validasi format tanggal saja
            'jam' => 'required',
            'berat_aktual_1' => 'required|numeric',
            'berat_aktual_2' => 'required|numeric',
            'berat_aktual_3' => 'required|numeric',
            'rata_rata_berat' => 'required|numeric',
        ]);
    } else {
        $validated = $request->validate([
            'id_pengemasan_plastik' => 'required',
            'id_pengemasan_produk' => 'required',
            'id_berat_produk_bag' => 'required',
            'id_shift' => 'required',
            'id_data_box' => 'required',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required',
            'berat_aktual_1' => 'required|numeric',
            'berat_aktual_2' => 'required|numeric',
            'berat_aktual_3' => 'required|numeric',
            'rata_rata_berat' => 'required|numeric',
        ]);
    }

    // Set common data
    $userId = Auth::id();
    $idPlan = $user->id_plan;
    
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

    // Create single record
    BeratProdukBox::create([
        'id_pengemasan_plastik' => $validated['id_pengemasan_plastik'],
        'id_pengemasan_produk' => $validated['id_pengemasan_produk'],
        'id_berat_produk_bag' => $validated['id_berat_produk_bag'],
        'id_shift' => $validated['id_shift'],
        'id_data_box' => $validated['id_data_box'],
        'tanggal' => $tanggal,
        'jam' => $request->jam,
        'berat_aktual_1' => $validated['berat_aktual_1'],
        'berat_aktual_2' => $validated['berat_aktual_2'],
        'berat_aktual_3' => $validated['berat_aktual_3'],
        'rata_rata_berat' => $validated['rata_rata_berat'],
        'user_id' => $userId,
        'id_plan' => $idPlan,
    ]);

        return redirect()->away(route('berat-produk.index') . '#custom-tabs-four-box-tab')->with('success', 'Data Berat Produk Box berhasil ditambahkan.');
    }

    public function edit_bag($uuid)
    {
        $user = Auth::user();
        $data_bags = DataBag::all();
        $beratProdukBag = BeratProdukBag::where('uuid', $uuid)->firstOrFail();
      
        $query = PengemasanPlastik::with(['plan','shift', 'pengemasanProduk', 'pengemasanProduk.produk']);
        $pengemasanPlastik = $query->where('id', $beratProdukBag->id_pengemasan_plastik)->first();
        $produk = optional(optional($pengemasanPlastik->pengemasanProduk)->produk);
        $kode_produksi = optional(optional($pengemasanPlastik->pengemasanProduk));
        if ($user->role == 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
            $shifts = DataShift::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            
        }

        return view('qc-sistem.berat_produk_bag_box.edit_bag', compact('beratProdukBag', 'plans', 'produks', 'shifts', 'data_bags','produk','kode_produksi'));
    }

    public function edit_box($uuid)
    {
        $user = Auth::user();
        $data_boxes = DataBox::all();
        $beratProdukBox = BeratProdukBox::where('uuid', $uuid)->firstOrFail();
        
        $queryBag = BeratProdukBag::with(['plan', 'shift', 'pengemasanProduk', 'pengemasanProduk.produk','pengemasanPlastik']);
        $beratProdukBag = $queryBag->where('id', $beratProdukBox->id_berat_produk_bag)->first();
         $produk = optional(optional($beratProdukBag->pengemasanProduk)->produk);
        $kode_produksi = optional(optional($beratProdukBag->pengemasanProduk));   
       
         if ($user->role == 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
            $shifts = DataShift::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.berat_produk_bag_box.edit_box', compact('beratProdukBox', 'plans', 'produks', 'shifts', 'data_boxes','produk','kode_produksi'));
    }

    public function update_bag(Request $request, $uuid)
    {
        $beratProdukBag = BeratProdukBag::where('uuid', $uuid)->firstOrFail();

        $validatedData = $request->validate([
             'tanggal' => 'required|date_format:d-m-Y H:i:s',
            // 'id_plan' => 'required|exists:plan,id',
            // 'id_pengemasan_plastik' => 'required',
            // 'id_pengemasan_produk' => 'required',
            'id_shift' => 'required|exists:data_shift,id',
            'id_data_bag' => 'required|exists:data_bag,id',
            'line' => 'required|integer|min:1|max:8',
            'berat_aktual_1' => 'required|numeric',
            'berat_aktual_2' => 'required|numeric',
            'berat_aktual_3' => 'required|numeric',
            'rata_rata_berat' => 'required|numeric',
        ]);
        $validatedData['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
        $validated['id_plan'] = Auth::user()->id_plan;
        // dd($validatedData);
        $beratProdukBag->update($validatedData);

        return redirect()->route('berat-produk.index')->with('success', 'Data C1 (Bag) berhasil diperbarui.');
    }

    public function update_box(Request $request, $uuid)
    {
        $beratProdukBox = BeratProdukBox::where('uuid', $uuid)->firstOrFail();

        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            // 'id_plan' => 'required|exists:plan,id',
            // 'id_produk' => 'required|exists:jenis_produk,id',
            'id_shift' => 'required|exists:data_shift,id',
            'id_data_box' => 'required|exists:data_box,id',
            'berat_aktual_1' => 'required|numeric',
            'berat_aktual_2' => 'required|numeric',
            'berat_aktual_3' => 'required|numeric',
            'rata_rata_berat' => 'required|numeric',
        ]);
         $validatedData['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
        $validated['id_plan'] = Auth::user()->id_plan;
        $beratProdukBox->update($validatedData);

        return redirect()->route('berat-produk.index')->with('success', 'Data C2 (Box) berhasil diperbarui.');
    }

    public function destroy_bag($uuid)
    {
        $beratProdukBag = BeratProdukBag::where('uuid', $uuid)->firstOrFail();
        $beratProdukBag->delete();
        return redirect()->route('berat-produk.index')->with('success', 'Data Berat Produk Bag berhasil dihapus.');
    }

    public function destroy_box($uuid)
    {
        $beratProdukBox = BeratProdukBox::where('uuid', $uuid)->firstOrFail();
        $beratProdukBox->delete();
        return redirect()->route('berat-produk.index')->with('success', 'Data Berat Produk Box berhasil dihapus.');
    }

    public function getDataBagByProduk($id_produk)
{
    \Log::info('Fetching DataBag for product ID: ' . $id_produk);
    $data = DataBag::where('id_produk', $id_produk)->get();
    \Log::info('Data found: ' . $data->toJson());
    return response()->json($data);
}

    public function getDataBoxByProduk($id_produk)
    {
        \Log::info('Fetching DataBox for product ID: ' . $id_produk);
        $data = DataBox::where('id_produk', $id_produk)->get();
        \Log::info('Data found: ' . $data->toJson());
        return response()->json($data);
    }

    // Berat Produk Bag Logs Methods
    public function showBagLogs($uuid)
    {
        $beratProdukBag = BeratProdukBag::where('uuid', $uuid)->firstOrFail();
        
        // Check authorization - user can only view logs for their plan
        if (auth()->user()->role !== 'superadmin' && $beratProdukBag->id_plan !== auth()->user()->id_plan) {
            abort(403, 'Unauthorized access to logs.');
        }

        return view('qc-sistem.berat_produk_bag_box.bag_logs', compact('beratProdukBag'));
    }

    public function getBagLogsJson($uuid)
    {
        $beratProdukBag = BeratProdukBag::where('uuid', $uuid)->firstOrFail();
        
        // Check authorization
        if (auth()->user()->role !== 'superadmin' && $beratProdukBag->id_plan !== auth()->user()->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = BeratProdukBagLog::where('berat_produk_bag_uuid', $uuid)
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

    // Berat Produk Box Logs Methods
    public function showBoxLogs($uuid)
    {
        $beratProdukBox = BeratProdukBox::where('uuid', $uuid)->firstOrFail();
        
        // Check authorization - user can only view logs for their plan
        if (auth()->user()->role !== 'superadmin' && $beratProdukBox->id_plan !== auth()->user()->id_plan) {
            abort(403, 'Unauthorized access to logs.');
        }

        return view('qc-sistem.berat_produk_bag_box.box_logs', compact('beratProdukBox'));
    }

    public function getBoxLogsJson($uuid)
    {
        $beratProdukBox = BeratProdukBox::where('uuid', $uuid)->firstOrFail();
        
        // Check authorization
        if (auth()->user()->role !== 'superadmin' && $beratProdukBox->id_plan !== auth()->user()->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = BeratProdukBoxLog::where('berat_produk_box_uuid', $uuid)
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
