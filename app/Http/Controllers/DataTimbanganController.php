<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataTimbangan;
use App\Models\Plan;
use Illuminate\Support\Str;

class DataTimbanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $query = DataTimbangan::with(['plan', 'user']);
        
        // Filter berdasarkan role
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();
        return view('super-admin.data-timbangan.index', compact('data'));
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
        
        return view('super-admin.data-timbangan.create', compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'nama_timbangan' => 'required|array|min:1',
            'nama_timbangan.*' => 'required|string|max:255',
            'kode_timbangan' => 'required|array|min:1',
            'kode_timbangan.*' => 'required|string|max:255|distinct',
        ], [
            'id_plan.required' => 'Plan harus dipilih',
            'nama_timbangan.required' => 'Minimal harus ada 1 Nama Timbangan',
            'nama_timbangan.*.required' => 'Nama Timbangan tidak boleh kosong',
            'kode_timbangan.required' => 'Minimal harus ada 1 Kode Timbangan',
            'kode_timbangan.*.required' => 'Kode Timbangan tidak boleh kosong',
            'kode_timbangan.*.distinct' => 'Kode Timbangan tidak boleh sama dalam satu input',
        ]);

        // Cek akses berdasarkan role
        if ($user->role !== 'superadmin' && $request->id_plan != $user->id_plan) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk plan ini');
        }

        // Validasi kode_timbangan unique di database
        foreach ($request->kode_timbangan as $kode) {
            if (!empty(trim($kode))) {
                $exists = DataTimbangan::where('kode_timbangan', trim($kode))->exists();
                if ($exists) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Kode Timbangan "' . $kode . '" sudah digunakan di database');
                }
            }
        }

        // Insert multiple records
        $successCount = 0;
        foreach ($request->nama_timbangan as $index => $nama) {
            if (!empty(trim($nama)) && !empty(trim($request->kode_timbangan[$index]))) {
                DataTimbangan::create([
                    'uuid' => Str::uuid(),
                    'id_plan' => $request->id_plan,
                    'user_id' => $user->id,
                    'nama_timbangan' => trim($nama),
                    'kode_timbangan' => trim($request->kode_timbangan[$index]),
                ]);
                $successCount++;
            }
        }

        return redirect()->route('data-timbangan.index')
            ->with('success', $successCount . ' Data Timbangan berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $item = DataTimbangan::where('uuid', $uuid)->firstOrFail();
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

        return view('super-admin.data-timbangan.edit', compact('item', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $item = DataTimbangan::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Cek akses
        if (!$item->canAccess()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'nama_timbangan' => 'required|string|max:255',
            'kode_timbangan' => 'required|string|max:255|unique:data_timbangan,kode_timbangan,' . $item->id,
        ], [
            'id_plan.required' => 'Plan harus dipilih',
            'nama_timbangan.required' => 'Nama timbangan harus diisi',
            'kode_timbangan.required' => 'Kode timbangan harus diisi',
            'kode_timbangan.unique' => 'Kode timbangan sudah digunakan',
        ]);

        // Cek akses berdasarkan role
        if ($user->role !== 'superadmin' && $request->id_plan != $user->id_plan) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk plan ini');
        }

        $item->update([
            'id_plan' => $request->id_plan,
            'nama_timbangan' => $request->nama_timbangan,
            'kode_timbangan' => $request->kode_timbangan,
        ]);

        return redirect()->route('data-timbangan.index')->with('success', 'Data timbangan berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $item = DataTimbangan::where('uuid', $uuid)->firstOrFail();

        // Cek akses
        if (!$item->canAccess()) {
            abort(403, 'Unauthorized action.');
        }

        $item->delete();
        return redirect()->route('data-timbangan.index')->with('success', 'Data timbangan berhasil dihapus');
    }
}