<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataDefect;
use App\Models\Plan;
use Illuminate\Support\Str;

class DataDefectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $query = DataDefect::with(['plan', 'user']);
        
        // Filter berdasarkan role
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();
        return view('super-admin.data_defect.index', compact('data'));
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
        
        return view('super-admin.data_defect.create', compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'id_plan'       => 'required|exists:plan,id',
            'nama_produsen' => 'required|array|min:1',
        ], [
            'id_plan.required'       => 'Plan harus dipilih',
            'nama_produsen.required' => 'Nama Produsen harus diisi',
        ]);

        // Cek akses berdasarkan role
        if ($user->role !== 'superadmin' && $request->id_plan != $user->id_plan) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk plan ini');
        }

        // Setiap form card = 1 record
        $successCount = 0;
        $namaProdusenList = $request->input('nama_produsen', []);

        foreach ($namaProdusenList as $formIndex => $namaProdusen) {
            if (empty(trim($namaProdusen))) continue;

            $categoriesInput = $request->input("kategori.{$formIndex}", []);
            $detailDefect = [];

            foreach ($categoriesInput as $catInput) {
                $kategoriNama = trim($catInput['nama'] ?? '');
                if (empty($kategoriNama)) continue;

                $items = [];
                $itemsInput = $catInput['items'] ?? [];
                foreach ($itemsInput as $itemInput) {
                    $jenis = trim($itemInput['jenis'] ?? '');
                    $subJenis = trim($itemInput['sub_jenis'] ?? '');
                    $spesifikasi = trim($itemInput['spesifikasi'] ?? '');

                    if (!empty($jenis) || !empty($subJenis) || !empty($spesifikasi)) {
                        $items[] = [
                            'jenis'       => $jenis,
                            'sub_jenis'   => $subJenis,
                            'spesifikasi' => $spesifikasi,
                        ];
                    }
                }

                $detailDefect[] = [
                    'kategori' => $kategoriNama,
                    'items'    => $items
                ];
            }

            DataDefect::create([
                'uuid'          => Str::uuid(),
                'id_plan'       => $request->id_plan,
                'user_id'       => $user->id,
                'nama_produsen' => trim($namaProdusen),
                'detail_defect' => $detailDefect,
            ]);
            $successCount++;
        }

        return redirect()->route('data-defect.index')
            ->with('success', $successCount . ' Data Defect berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        $data = DataDefect::where('uuid', $uuid)->with(['plan', 'user'])->firstOrFail();
        
        // Cek akses
        if (!$data->canAccess()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }
        
        return view('super-admin.data_defect.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        $user = auth()->user();
        $data = DataDefect::where('uuid', $uuid)->firstOrFail();
        
        // Cek akses
        if (!$data->canAccess()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }
        
        // Filter plans berdasarkan role
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        
        return view('super-admin.data_defect.edit', compact('data', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        $user = auth()->user();
        $data = DataDefect::where('uuid', $uuid)->firstOrFail();

        // Cek akses
        if (!$data->canAccess()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }

        $request->validate([
            'id_plan'       => 'required|exists:plan,id',
            'nama_produsen' => 'required|string|max:255',
            'kategori'      => 'required|array|min:1',
        ], [
            'id_plan.required'       => 'Plan harus dipilih',
            'nama_produsen.required' => 'Nama Produsen harus diisi',
            'kategori.required'      => 'Minimal harus ada 1 Kategori Ketidaksesuaian',
        ]);

        // Cek akses berdasarkan role
        if ($user->role !== 'superadmin' && $request->id_plan != $user->id_plan) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk plan ini');
        }

        $categoriesInput = $request->input('kategori', []);
        $detailDefect = [];

        foreach ($categoriesInput as $catInput) {
            $kategoriNama = trim($catInput['nama'] ?? '');
            if (empty($kategoriNama)) continue;

            $items = [];
            $itemsInput = $catInput['items'] ?? [];
            foreach ($itemsInput as $itemInput) {
                $jenis = trim($itemInput['jenis'] ?? '');
                $subJenis = trim($itemInput['sub_jenis'] ?? '');
                $spesifikasi = trim($itemInput['spesifikasi'] ?? '');

                if (!empty($jenis) || !empty($subJenis) || !empty($spesifikasi)) {
                    $items[] = [
                        'jenis'       => $jenis,
                        'sub_jenis'   => $subJenis,
                        'spesifikasi' => $spesifikasi,
                    ];
                }
            }

            $detailDefect[] = [
                'kategori' => $kategoriNama,
                'items'    => $items
            ];
        }

        $data->update([
            'id_plan'       => $request->id_plan,
            'nama_produsen' => trim($request->nama_produsen),
            'detail_defect' => $detailDefect,
        ]);

        return redirect()->route('data-defect.index')->with('success', 'Data Defect berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        $data = DataDefect::where('uuid', $uuid)->firstOrFail();
        
        // Cek akses
        if (!$data->canAccess()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }
        
        $data->delete();
        
        return redirect()->route('data-defect.index')->with('success', 'Data Defect berhasil dihapus');
    }
}