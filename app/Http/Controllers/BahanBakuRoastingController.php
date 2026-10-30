<?php

namespace App\Http\Controllers;

use App\Models\BahanBakuRoasting;
use App\Models\BahanBakuRoastingLog;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\ProsesRoastingFan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BahanBakuRoastingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');

        $query = BahanBakuRoasting::with(['plan', 'user', 'shift', 'produk'])
            ->orderBy('created_at', 'desc');
           
        if($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_produksi_rm', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('produk', function($sq) use ($search) {
                      $sq->where('nama_produk', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        $bahanBakuRoasting = $query->paginate(10);

        // Count related proses roasting fan records for each bahan baku roasting
        foreach ($bahanBakuRoasting as $item) {
            $item->prosesRoastingFanCount = ProsesRoastingFan::where('bahan_baku_roasting_uuid', $item->uuid)->count();
        }

        return view('qc-sistem.bahan_baku_roasting.index', compact('bahanBakuRoasting', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Get input roasting data if coming from Input Roasting
        $inputRoastingData = null;
        $inputRoastingUuid = null;
        
        if ($request->has('input_roasting_uuid')) {
            $inputRoastingUuid = $request->input_roasting_uuid;
            $inputRoastingData = \App\Models\InputRoasting::with(['produk', 'shift', 'user'])
                ->where('uuid', $inputRoastingUuid)
                ->first();
        }

        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            // $shifts = DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            // $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        $plan = Plan::find($user->id_plan);

        return view('qc-sistem.bahan_baku_roasting.create', compact('plan', 'produks', 'inputRoastingData', 'inputRoastingUuid'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
    
        // Auto-detect shift berdasarkan input_roasting_uuid
        $shift_id = null;
        
        if ($request->input_roasting_uuid) {
            $inputRoasting = \App\Models\InputRoasting::where('uuid', $request->input_roasting_uuid)->first();
            if ($inputRoasting) {
                $shift_id = $inputRoasting->shift_id;
            }
        }
        
        if (!$shift_id) {
            return back()->withErrors(['shift' => 'Tidak dapat menentukan shift dari Input Roasting'])->withInput();
        }
        
        // UBAH: Validasi tanpa shift_id
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'kode_produksi_rm' => 'required|string|max:255',
            'standart_suhu_rm' => 'required|string|max:255',
            'aktual_suhu_rm' => 'required|string|max:255',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'input_roasting_uuid' => 'required|string', // Pastikan UUID ada
    ]);

        try {
            $bahanBakuRoasting = new BahanBakuRoasting();
            $bahanBakuRoasting->uuid = (string) \Illuminate\Support\Str::uuid();
            $bahanBakuRoasting->id_plan = $user->id_plan; // Set plan from logged-in user
            $bahanBakuRoasting->user_id = $user->id;
            $bahanBakuRoasting->shift_id = $shift_id;
            $bahanBakuRoasting->id_produk = $request->id_produk;
            $bahanBakuRoasting->kode_produksi_rm = $request->kode_produksi_rm;
            $bahanBakuRoasting->standart_suhu_rm = $request->standart_suhu_rm;
            $bahanBakuRoasting->aktual_suhu_rm = $request->aktual_suhu_rm;
            $bahanBakuRoasting->tanggal = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
            
            // Add input_roasting_uuid if provided
            if ($request->has('input_roasting_uuid')) {
                $bahanBakuRoasting->input_roasting_uuid = $request->input_roasting_uuid;
            }
            
            $bahanBakuRoasting->save();

            return redirect()->route('bahan-baku-roasting.index')
                ->with('success', 'Data Bahan Baku Roasting berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $bahanBakuRoasting = BahanBakuRoasting::where('uuid', $uuid)->firstOrFail();
        $bahanBakuRoasting->load(['plan', 'user', 'shift', 'produk']);
        return view('qc-sistem.bahan_baku_roasting.show', compact('bahanBakuRoasting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $user = Auth::user();
        $bahanBakuRoasting = BahanBakuRoasting::where('uuid', $uuid)->firstOrFail();
        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            // $shifts = DataShift::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            // $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.bahan_baku_roasting.edit', compact('bahanBakuRoasting', 'produks'));
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, $uuid)
    {
        $user = Auth::user();
        $bahanBakuRoasting = BahanBakuRoasting::where('uuid', $uuid)->firstOrFail();
        
        // TAMBAHKAN: Auto-detect shift berdasarkan input_roasting_uuid
        $shift_id = null;
        
        if ($request->input_roasting_uuid) {
            $inputRoasting = \App\Models\InputRoasting::where('uuid', $request->input_roasting_uuid)->first();
            if ($inputRoasting) {
                $shift_id = $inputRoasting->shift_id;
            }
        }
        
        if (!$shift_id) {
            return back()->withErrors(['shift' => 'Tidak dapat menentukan shift dari Input Roasting'])->withInput();
        }
        
        // UBAH: Validasi tanpa shift_id
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'kode_produksi_rm' => 'required|string|max:255',
            'standart_suhu_rm' => 'required|string|max:255',
            'aktual_suhu_rm' => 'required|string|max:255',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'input_roasting_uuid' => 'required|string', // Pastikan UUID ada
        ]);

        // UBAH: Gunakan shift_id yang sudah didapat
        $data = $request->except(['user_id', 'id_plan', 'tanggal']);
        $data['id_plan'] = $user->id_plan;
        $data['shift_id'] = $shift_id;  // TAMBAHKAN INI
        $data['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');

        $bahanBakuRoasting->update($data);

        return redirect()->route('bahan-baku-roasting.index')
            ->with('success', 'Data bahan baku roasting berhasil diperbarui.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $bahanBakuRoasting = BahanBakuRoasting::where('uuid', $uuid)->firstOrFail();
        $bahanBakuRoasting->delete();

        return redirect()->route('bahan-baku-roasting.index')
            ->with('success', 'Data bahan baku roasting berhasil dihapus.');
    }

    /**
     * Show logs for a specific bahan baku roasting record
     */
    public function showLogs($uuid)
    {
        // Check authorization
        if (!auth()->check()) {
            abort(403, 'Unauthorized access to logs');
        }

        $bahanBakuRoasting = BahanBakuRoasting::where('uuid', $uuid)->firstOrFail();
        $bahanBakuRoasting->load(['plan', 'user', 'shift', 'produk']);
        
        $logs = BahanBakuRoastingLog::where('bahan_baku_roasting_uuid', $uuid)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.bahan_baku_roasting.logs', compact('bahanBakuRoasting', 'logs'));
    }

    /**
     * Get logs data in JSON format for a specific bahan baku roasting record
     */
    public function getLogsJson($uuid)
    {
        // Check authorization
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = BahanBakuRoastingLog::where('bahan_baku_roasting_uuid', $uuid)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($logs);
    }
}
