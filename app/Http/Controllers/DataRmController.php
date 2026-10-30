<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataRm;
use App\Models\Plan;
use Illuminate\Support\Str;

class DataRmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $query = DataRm::with(['plan', 'user']);
        
        // Filter berdasarkan role
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();
        return view('super-admin.data_rm.index', compact('data'));
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
        
        return view('super-admin.data_rm.create', compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'nama_rm' => 'required|array|min:1',
            'nama_rm.*' => 'required|string|max:255',
        ], [
            'id_plan.required' => 'Plan harus dipilih',
            'nama_rm.required' => 'Minimal harus ada 1 Nama RM',
            'nama_rm.*.required' => 'Nama RM tidak boleh kosong',
        ]);

        // Cek akses berdasarkan role
        if ($user->role !== 'superadmin' && $request->id_plan != $user->id_plan) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk plan ini');
        }

        // Insert multiple records
        $successCount = 0;
        foreach ($request->nama_rm as $nama) {
            if (!empty(trim($nama))) {
                DataRm::create([
                    'uuid' => Str::uuid(),
                    'id_plan' => $request->id_plan,
                    'user_id' => $user->id,
                    'nama_rm' => trim($nama),
                ]);
                $successCount++;
            }
        }

        return redirect()->route('data-rm.index')
            ->with('success', $successCount . ' Data RM berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $item = DataRm::where('uuid', $uuid)->firstOrFail();
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

        return view('super-admin.data_rm.edit', compact('item', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $item = DataRm::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Cek akses
        if (!$item->canAccess()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'nama_rm' => 'required|string|max:255',
        ], [
            'id_plan.required' => 'Plan harus dipilih',
            'nama_rm.required' => 'Nama RM harus diisi',
        ]);

        // Cek akses berdasarkan role
        if ($user->role !== 'superadmin' && $request->id_plan != $user->id_plan) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk plan ini');
        }

        $item->update([
            'id_plan' => $request->id_plan,
            'nama_rm' => $request->nama_rm,
        ]);

        return redirect()->route('data-rm.index')->with('success', 'Data RM berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $item = DataRm::where('uuid', $uuid)->firstOrFail();

        // Cek akses
        if (!$item->canAccess()) {
            abort(403, 'Unauthorized action.');
        }

        $item->delete();
        return redirect()->route('data-rm.index')->with('success', 'Data RM berhasil dihapus');
    }
}