<?php

namespace App\Http\Controllers;

use App\Models\BahanFormulaFla;
use App\Models\NamaFormulaFla;
use App\Models\NomorStepFormulaFla;
use Illuminate\Http\Request;

class BahanFormulaFlaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $items = BahanFormulaFla::with(['plan', 'user', 'namaFormulaFla.produk', 'nomorStepFormulaFla'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $items = BahanFormulaFla::with(['plan', 'user', 'namaFormulaFla.produk', 'nomorStepFormulaFla'])
                ->where('id_plan', $user->id_plan)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('super-admin.bahan_formula_fla.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Get unique products from nama_formula_fla using relationship
        if ($user->role === 'superadmin') {
            $products = NamaFormulaFla::with('produk')
                ->get()
                ->pluck('produk')
                ->filter()
                ->unique('id')
                ->values();
        } else {
            $products = NamaFormulaFla::with('produk')
                ->where('id_plan', $user->id_plan)
                ->get()
                ->pluck('produk')
                ->filter()
                ->unique('id')
                ->values();
        }

        return view('super-admin.bahan_formula_fla.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'id_nama_formula_fla' => 'required|exists:nama-formula-fla,id',
            'id_nomor_step_formula_fla' => 'nullable|exists:nomor_step_formula_fla,id',
            'proses' => 'nullable|array|max:2',
            'proses.*' => 'in:sauted,mixing,stirring',
            'bahan_formula_fla' => 'required|array|min:1',
            'bahan_formula_fla.*' => 'required|string|max:255',
            'berat_formula_fla' => 'required|array|min:1',
            'berat_formula_fla.*' => 'required|string|max:255',
        ], [
            'id_nama_formula_fla.required' => 'Nama formula FLA harus dipilih.',
            'id_nama_formula_fla.exists' => 'Nama formula FLA tidak valid.',
            'id_nomor_step_formula_fla.exists' => 'Step formula FLA tidak valid.',
            'proses.array' => 'Proses harus berupa array.',
            'proses.max' => 'Maksimal pilih 2 proses.',
            'proses.*.in' => 'Proses harus salah satu dari: sauted, mixing, stirring.',
            'bahan_formula_fla.required' => 'Bahan formula FLA harus diisi.',
            'bahan_formula_fla.array' => 'Bahan formula FLA harus berupa array.',
            'bahan_formula_fla.min' => 'Minimal harus ada 1 bahan formula FLA.',
            'bahan_formula_fla.*.required' => 'Setiap bahan formula FLA harus diisi.',
            'berat_formula_fla.required' => 'Berat formula FLA harus diisi.',
            'berat_formula_fla.array' => 'Berat formula FLA harus berupa array.',
            'berat_formula_fla.min' => 'Minimal harus ada 1 berat formula FLA.',
            'berat_formula_fla.*.required' => 'Setiap berat formula FLA harus diisi.',
        ]);

        // Authorization check for non-superadmin
        if ($user->role !== 'superadmin') {
            $namaFormulaFla = NamaFormulaFla::findOrFail($request->id_nama_formula_fla);
            if ($namaFormulaFla->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses untuk menambah data ini.');
            }
        }

        // Filter out empty values
        $bahanFormula = array_filter($request->bahan_formula_fla, function($value) {
            return !empty(trim($value));
        });
        
        $beratFormula = array_filter($request->berat_formula_fla, function($value) {
            return !empty(trim($value));
        });

        // Convert proses array to comma-separated string if provided
        $prosesString = $request->has('proses') && is_array($request->proses) ? implode(',', $request->proses) : null;

        // 1. Check if we should create a new NomorStepFormulaFla
        // If id_nomor_step_formula_fla is null/empty but we have id_nama_formula_fla
        $idStep = $request->id_nomor_step_formula_fla;

        if (empty($idStep) && !empty($request->id_nama_formula_fla)) {
            // Get the next step number for this formula
            $lastStep = NomorStepFormulaFla::where('id_nama_formula_fla', $request->id_nama_formula_fla)
                ->orderBy('nomor_step', 'desc')
                ->first();
            
            $nextStepNumber = $lastStep ? ((int)$lastStep->nomor_step + 1) : 1;

            // Create new step
            $newStep = NomorStepFormulaFla::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'id_plan' => $user->id_plan,
                'user_id' => $user->id,
                'id_nama_formula_fla' => $request->id_nama_formula_fla,
                'nomor_step' => $nextStepNumber,
                'proses' => $prosesString, // Save to NomorStepFormulaFla table as requested
            ]);
            $idStep = $newStep->id;
        } elseif (!empty($idStep)) {
            // If step exists, update the process if needed (optional based on requirement)
            $existingStep = NomorStepFormulaFla::find($idStep);
            if ($existingStep) {
                $existingStep->update(['proses' => $prosesString]);
            }
        }

        BahanFormulaFla::create([
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'id_nama_formula_fla' => $request->id_nama_formula_fla,
            'id_nomor_step_formula_fla' => $idStep,
            'bahan_formula_fla' => array_values($bahanFormula),
            'berat_formula_fla' => array_values($beratFormula),
        ]);

        return redirect()->route('bahan-formula-fla.index')
            ->with('success', 'Data bahan formula FLA berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $user = auth()->user();
        
        $bahanFormulaFla = BahanFormulaFla::where('uuid', $uuid)->firstOrFail();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $bahanFormulaFla->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        $item = $bahanFormulaFla->load(['plan', 'user', 'namaFormulaFla.produk', 'nomorStepFormulaFla']);

        return view('super-admin.bahan_formula_fla.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $user = auth()->user();
        
        $bahanFormulaFla = BahanFormulaFla::where('uuid', $uuid)->firstOrFail();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $bahanFormulaFla->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Get unique products from nama_formula_fla using relationship
        if ($user->role === 'superadmin') {
            $products = NamaFormulaFla::with('produk')
                ->get()
                ->pluck('produk')
                ->filter()
                ->unique('id')
                ->values();
        } else {
            $products = NamaFormulaFla::with('produk')
                ->where('id_plan', $user->id_plan)
                ->get()
                ->pluck('produk')
                ->filter()
                ->unique('id')
                ->values();
        }

        $item = $bahanFormulaFla;

        return view('super-admin.bahan_formula_fla.edit', compact('item', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $user = auth()->user();
        
        $bahanFormulaFla = BahanFormulaFla::where('uuid', $uuid)->firstOrFail();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $bahanFormulaFla->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate data ini.');
        }

        $request->validate([
            'id_nama_formula_fla' => 'required|exists:nama-formula-fla,id',
            'id_nomor_step_formula_fla' => 'nullable|exists:nomor_step_formula_fla,id',
            'proses' => 'nullable|array|max:2',
            'proses.*' => 'in:sauted,mixing,stirring',
            'bahan_formula_fla' => 'required|array|min:1',
            'bahan_formula_fla.*' => 'required|string|max:255',
            'berat_formula_fla' => 'required|array|min:1',
            'berat_formula_fla.*' => 'required|string|max:255',
        ], [
            'id_nama_formula_fla.required' => 'Nama formula FLA harus dipilih.',
            'id_nama_formula_fla.exists' => 'Nama formula FLA tidak valid.',
            'id_nomor_step_formula_fla.exists' => 'Step formula FLA tidak valid.',
            'proses.array' => 'Proses harus berupa array.',
            'proses.max' => 'Maksimal pilih 2 proses.',
            'proses.*.in' => 'Proses harus salah satu dari: sauted, mixing, stirring.',
            'bahan_formula_fla.required' => 'Bahan formula FLA harus diisi.',
            'bahan_formula_fla.array' => 'Bahan formula FLA harus berupa array.',
            'bahan_formula_fla.min' => 'Minimal harus ada 1 bahan formula FLA.',
            'bahan_formula_fla.*.required' => 'Setiap bahan formula FLA harus diisi.',
            'berat_formula_fla.required' => 'Berat formula FLA harus diisi.',
            'berat_formula_fla.array' => 'Berat formula FLA harus berupa array.',
            'berat_formula_fla.min' => 'Minimal harus ada 1 berat formula FLA.',
            'berat_formula_fla.*.required' => 'Setiap berat formula FLA harus diisi.',
        ]);

        // Authorization check for non-superadmin
        if ($user->role !== 'superadmin') {
            $namaFormulaFla = NamaFormulaFla::findOrFail($request->id_nama_formula_fla);
            if ($namaFormulaFla->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses untuk mengupdate data ini.');
            }
        }

        // Filter out empty values
        $bahanFormula = array_filter($request->bahan_formula_fla, function($value) {
            return !empty(trim($value));
        });
        
        $beratFormula = array_filter($request->berat_formula_fla, function($value) {
            return !empty(trim($value));
        });

        // Convert proses array to comma-separated string if provided
        $prosesString = $request->has('proses') && is_array($request->proses) ? implode(',', $request->proses) : null;

        $bahanFormulaFla->update([
            'id_nama_formula_fla' => $request->id_nama_formula_fla,
            'id_nomor_step_formula_fla' => $request->id_nomor_step_formula_fla,
            'proses' => $prosesString,
            'bahan_formula_fla' => array_values($bahanFormula),
            'berat_formula_fla' => array_values($beratFormula),
        ]);

        return redirect()->route('bahan-formula-fla.index')
            ->with('success', 'Data bahan formula FLA berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $user = auth()->user();
        
        $bahanFormulaFla = BahanFormulaFla::where('uuid', $uuid)->firstOrFail();
        
        // Authorization check
        if ($user->role !== 'superadmin' && $bahanFormulaFla->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $bahanFormulaFla->delete();

        return redirect()->route('bahan-formula-fla.index')
            ->with('success', 'Data bahan formula FLA berhasil dihapus.');
    }

    /**
     * Get formula by product ID (AJAX)
     */
    public function getFormulaByProduct($productId)
    {
        $user = auth()->user();
        
        $query = NamaFormulaFla::where('id_produk', $productId);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        $formulas = $query->get(['id', 'nama_formula_fla']);
        
        return response()->json($formulas);
    }

    /**
     * Get steps by formula ID (AJAX)
     */
    public function getStepsByFormula($formulaId)
    {
        $user = auth()->user();
        
        $query = NomorStepFormulaFla::where('id_nama_formula_fla', $formulaId);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        $steps = $query->get(['id', 'nomor_step', 'proses']);
        
        return response()->json($steps);
    }

    /**
     * Get bahan by step ID (AJAX)
     */
    public function getBahanByStep($stepId)
    {
        $user = auth()->user();
        
        $query = BahanFormulaFla::with(['namaFormulaFla.produk', 'nomorStepFormulaFla'])
            ->where('id_nomor_step_formula_fla', $stepId);
        
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        $bahan = $query->first();
        
        if ($bahan) {
            return response()->json([
                'success' => true,
                'data' => $bahan
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Bahan not found'
        ]);
    }
}
