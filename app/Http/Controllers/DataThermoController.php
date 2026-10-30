<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataThermo;
use App\Models\Plan;
use Illuminate\Support\Str;

class DataThermoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $query = DataThermo::with(['plan', 'user']);
        
        // Filter berdasarkan role
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();
        return view('super-admin.data-thermo.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Filter plans berdasarkan role
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        
        return view('super-admin.data-thermo.create', compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'nama_thermo' => 'required|array|min:1',
            'nama_thermo.*' => 'required|string|max:255',
            'kode_thermo' => 'required|array|min:1',
            'kode_thermo.*' => 'required|string|max:255|distinct',
        ], [
            'id_plan.required' => 'Plan harus dipilih',
            'nama_thermo.required' => 'Minimal harus ada 1 Nama Thermometer',
            'nama_thermo.*.required' => 'Nama Thermometer tidak boleh kosong',
            'kode_thermo.required' => 'Minimal harus ada 1 Kode Thermometer',
            'kode_thermo.*.required' => 'Kode Thermometer tidak boleh kosong',
            'kode_thermo.*.distinct' => 'Kode Thermometer tidak boleh sama dalam satu input',
        ]);

        // Cek akses berdasarkan role
        if ($user->role !== 'superadmin' && $request->id_plan != $user->id_plan) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk plan ini');
        }

        // Validasi kode_thermo unique di database
        foreach ($request->kode_thermo as $kode) {
            if (!empty(trim($kode))) {
                $exists = DataThermo::where('kode_thermo', trim($kode))->exists();
                if ($exists) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Kode Thermometer "' . $kode . '" sudah digunakan di database');
                }
            }
        }

        // Insert multiple records
        $successCount = 0;
        foreach ($request->nama_thermo as $index => $nama) {
            if (!empty(trim($nama)) && !empty(trim($request->kode_thermo[$index]))) {
                DataThermo::create([
                    'uuid' => Str::uuid(),
                    'id_plan' => $request->id_plan,
                    'user_id' => $user->id,
                    'nama_thermo' => trim($nama),
                    'kode_thermo' => trim($request->kode_thermo[$index]),
                ]);
                $successCount++;
            }
        }

        return redirect()->route('data-thermo.index')
            ->with('success', $successCount . ' Data Thermometer berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $item = DataThermo::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Cek akses
        if (!$item->canAccess()) {
            abort(403, 'Unauthorized action.');
        }

        // Filter plans berdasarkan role
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('super-admin.data-thermo.edit', compact('item', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $item = DataThermo::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Cek akses
        if (!$item->canAccess()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'nama_thermo' => 'required|string|max:255',
            'kode_thermo' => 'required|string|max:255|unique:data_thermo,kode_thermo,' . $item->id,
        ], [
            'id_plan.required' => 'Plan harus dipilih',
            'nama_thermo.required' => 'Nama thermometer harus diisi',
            'kode_thermo.required' => 'Kode thermometer harus diisi',
            'kode_thermo.unique' => 'Kode thermometer sudah digunakan',
        ]);

        // Cek akses berdasarkan role
        if ($user->role !== 'superadmin' && $request->id_plan != $user->id_plan) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk plan ini');
        }

        $item->update([
            'id_plan' => $request->id_plan,
            'nama_thermo' => $request->nama_thermo,
            'kode_thermo' => $request->kode_thermo,
        ]);

        return redirect()->route('data-thermo.index')->with('success', 'Data thermometer berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $item = DataThermo::where('uuid', $uuid)->firstOrFail();

        // Cek akses
        if (!$item->canAccess()) {
            abort(403, 'Unauthorized action.');
        }

        $item->delete();
        return redirect()->route('data-thermo.index')->with('success', 'Data thermometer berhasil dihapus');
    }
}