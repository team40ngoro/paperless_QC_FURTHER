<?php

namespace App\Http\Controllers;

use App\Models\InputRoasting;
use App\Models\InputRoastingLog;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InputRoastingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        
        $query = InputRoasting::with(['plan', 'user', 'shift', 'produk']);

        if ($user->role != 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('produk', function($qp) use ($search) {
                    $qp->where('nama_produk', 'LIKE', '%' . $search . '%');
                })
                ->orWhere('kode_produksi', 'LIKE', '%' . $search . '%')
                ->orWhere('tanggal', 'LIKE', '%' . $search . '%');
            });
        }

        $inputRoasting = $query->orderBy('created_at', 'desc')->paginate(10);

        // Count related proses roasting fan records for each input roasting
        foreach ($inputRoasting as $item) {
            $item->prosesRoastingFanCount = \App\Models\ProsesRoastingFan::where('input_roasting_uuid', $item->uuid)->count();
        }

        return view('qc-sistem.input_roasting.index', compact('inputRoasting', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->role == 'superadmin') {
            $shifts = DataShift::all();
            $produks = JenisProduk::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        
        return view('qc-sistem.input_roasting.create', compact('shifts', 'produks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'kode_produksi' => 'required|string|max:255',
            'berat_produk' => 'required|string|max:255',
            'std_suhu_sebelum' => 'required|string|max:255',
            'aktual_suhu_sesudah' => 'required|string|max:255',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required|date_format:H:i',
        ]);

        $validated = $request->all();
        // Automatically set user_id and id_plan from authenticated user
        $data = [
            'user_id' => Auth::id(),
            'id_plan' => Auth::user()->id_plan,
            'shift_id' => $validated['shift_id'],
            'id_produk' => $validated['id_produk'],
            'kode_produksi' => $validated['kode_produksi'],
            'berat_produk' => $validated['berat_produk'],
            'std_suhu_sebelum' => $validated['std_suhu_sebelum'],
            'aktual_suhu_sesudah' => $validated['aktual_suhu_sesudah'],
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $validated['tanggal'])->format('Y-m-d H:i:s'),
            'jam' => $validated['jam'],
        ];

        InputRoasting::create($data);

        return redirect()->route('input-roasting.index')
            ->with('success', 'Data input roasting berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(InputRoasting $inputRoasting)
    {
        $inputRoasting->load(['plan', 'user', 'shift', 'produk']);
        return view('qc-sistem.input_roasting.show', compact('inputRoasting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $inputRoasting = InputRoasting::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        if ($user->role == 'superadmin') {
            $shifts = DataShift::all();
            $produks = JenisProduk::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        
        return view('qc-sistem.input_roasting.edit', compact('inputRoasting', 'shifts', 'produks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $inputRoasting = InputRoasting::where('uuid', $uuid)->firstOrFail();
        
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'berat_produk' => 'required|string|max:255',
            'kode_produksi' => 'required|string|max:255',
            'std_suhu_sebelum' => 'required|string|max:255',
            'aktual_suhu_sesudah' => 'required|string|max:255',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'nullable|date_format:H:i',
        ]);

        $validated = $request->all();
        $data = [
            'shift_id' => $validated['shift_id'],
            'id_produk' => $validated['id_produk'],
            'berat_produk' => $validated['berat_produk'],
            'kode_produksi' => $validated['kode_produksi'],
            'std_suhu_sebelum' => $validated['std_suhu_sebelum'],
            'aktual_suhu_sesudah' => $validated['aktual_suhu_sesudah'],
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $validated['tanggal'])->format('Y-m-d H:i:s'),
            'jam' => $validated['jam'],
        ];
        $inputRoasting->update($data);

        return redirect()->route('input-roasting.index')
            ->with('success', 'Data input roasting berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $inputRoasting = InputRoasting::where('uuid', $uuid)->firstOrFail();
        $inputRoasting->delete();

        return redirect()->route('input-roasting.index')
            ->with('success', 'Data input roasting berhasil dihapus.');
    }

    // Logs Methods
    public function showLogs($uuid)
    {
        $user = Auth::user();
        $inputRoasting = InputRoasting::with(['plan', 'shift', 'user', 'produk'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Check authorization
        if ($user->role != 'superadmin' && $inputRoasting->id_plan != $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        return view('qc-sistem.input_roasting.logs', compact('inputRoasting'));
    }

    public function getLogsJson($uuid)
    {
        $user = Auth::user();
        $inputRoasting = InputRoasting::where('uuid', $uuid)->firstOrFail();

        // Check authorization
        if ($user->role != 'superadmin' && $inputRoasting->id_plan != $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = InputRoastingLog::with('user')
            ->where('input_roasting_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'data' => $logs->items(),
            'pagination' => [
            'current_page' => $logs->currentPage(),
            'last_page' => $logs->lastPage(),
            'per_page' => $logs->perPage(),
            'total' => $logs->total(),
            ]
        ]);
    }
}
