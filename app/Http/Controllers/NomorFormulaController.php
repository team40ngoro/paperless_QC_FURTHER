<?php

namespace App\Http\Controllers;

use App\Models\NomorFormula;
use App\Models\Plan;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class NomorFormulaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = NomorFormula::with(['plan', 'produk', 'user']);

        // Filter berdasarkan plan jika bukan superadmin
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $nomor_formula = $query->orderBy('created_at', 'desc')->get();

        // Untuk dropdown plan (opsional)
        $list_plan = $user->role === 'superadmin'
            ? Plan::all()
            : Plan::where('id', $user->id_plan)->get();

        return view('super-admin.nomor_formula.index', compact('nomor_formula', 'list_plan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Role-based data filtering
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        
        return view('super-admin.nomor_formula.create', compact('plans', 'produks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validasi input array
            $request->validate([
                'id_plan' => 'required|exists:plan,id',
                'id_produk' => 'required|exists:jenis_produk,id',
                'nomor_formula' => 'required|array|min:1',
                'nomor_formula.*' => 'required|string|max:255',
            ], [
                'nomor_formula.required' => 'Minimal harus ada 1 nomor formula.',
                'nomor_formula.array' => 'Format nomor formula tidak valid.',
                'nomor_formula.min' => 'Minimal harus ada 1 nomor formula.',
                'nomor_formula.*.required' => 'Nomor formula tidak boleh kosong.',
                'nomor_formula.*.string' => 'Nomor formula harus berupa teks.',
                'nomor_formula.*.max' => 'Nomor formula maksimal 255 karakter.',
            ]);

            $user_id = auth()->id();
            $created_count = 0;
            $failed_count = 0;
            $duplicate_formulas = [];

            // Debug: Log input data
            Log::info('NomorFormula Store Input:', [
                'id_plan' => $request->id_plan,
                'id_produk' => $request->id_produk,
                'nomor_formula' => $request->nomor_formula,
                'user_id' => $user_id
            ]);

            // Loop untuk setiap nomor formula
            foreach ($request->nomor_formula as $index => $formula) {
                $trimmed_formula = trim($formula);
                
                if (!empty($trimmed_formula)) {
                    // Cek duplikasi
                    $existing = NomorFormula::where('id_plan', $request->id_plan)
                        ->where('id_produk', $request->id_produk)
                        ->where('nomor_formula', $trimmed_formula)
                        ->first();
                    
                    if ($existing) {
                        $duplicate_formulas[] = $trimmed_formula;
                        $failed_count++;
                        continue;
                    }

                    $data = [
                        'id_plan' => $request->id_plan,
                        'id_produk' => $request->id_produk,
                        'nomor_formula' => $trimmed_formula,
                        'user_id' => $user_id,
                    ];
                    
                    Log::info("Creating NomorFormula #{$index}:", $data);
                    
                    $created = NomorFormula::create($data);
                    
                    Log::info("Created NomorFormula:", [
                        'id' => $created->id, 
                        'uuid' => $created->uuid,
                        'nomor_formula' => $created->nomor_formula
                    ]);
                    
                    $created_count++;
                } else {
                    $failed_count++;
                }
            }

            Log::info("Summary - Created: {$created_count}, Failed: {$failed_count}");

            // Prepare success message
            $message = "Berhasil menambahkan {$created_count} Nomor Formula";
            
            if ($failed_count > 0) {
                $message .= ", {$failed_count} gagal";
                if (!empty($duplicate_formulas)) {
                    $message .= " (duplikat: " . implode(', ', $duplicate_formulas) . ")";
                }
            }

            return redirect()->route('nomor-formula.index')
                ->with('success', $message);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('NomorFormula Validation Error:', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            Log::error('NomorFormula Store Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $formula = NomorFormula::with(['plan', 'produk', 'user'])
            ->where('uuid', $uuid)
            ->firstOrFail();
        
        $user = auth()->user();
        
        // Check access permission
        if ($user->role !== 'superadmin' && $formula->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        return view('super-admin.nomor_formula.show', compact('formula'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $formula = NomorFormula::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Check access permission
        if ($user->role !== 'superadmin' && $formula->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }
        
        // Role-based data filtering
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        
        return view('super-admin.nomor_formula.edit', compact('formula', 'plans', 'produks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        try {
            $formula = NomorFormula::where('uuid', $uuid)->firstOrFail();
            $user = auth()->user();
            
            // Check access permission
            if ($user->role !== 'superadmin' && $formula->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
            }
            
            // Validasi input
            $data = $request->validate([
                'id_plan' => 'required|exists:plan,id',
                'id_produk' => 'required|exists:jenis_produk,id',
                'nomor_formula' => 'required|string|max:255',
            ]);
            
            // Cek duplikasi (kecuali data yang sedang diedit)
            $existing = NomorFormula::where('id_plan', $data['id_plan'])
                ->where('id_produk', $data['id_produk'])
                ->where('nomor_formula', $data['nomor_formula'])
                ->where('uuid', '!=', $uuid)
                ->first();
            
            if ($existing) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Nomor formula sudah ada untuk plan dan produk yang sama.');
            }
            
            Log::info('NomorFormula Update:', [
                'uuid' => $uuid,
                'old_data' => $formula->toArray(),
                'new_data' => $data
            ]);
            
            $formula->update($data);
            
            return redirect()->route('nomor-formula.index')
                ->with('success', 'Nomor Formula berhasil diperbarui');
                
        } catch (\Exception $e) {
            Log::error('NomorFormula Update Error:', [
                'uuid' => $uuid,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        try {
            $formula = NomorFormula::where('uuid', $uuid)->firstOrFail();
            $user = auth()->user();
            
            // Check access permission
            if ($user->role !== 'superadmin' && $formula->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
            }
            
            // Cek apakah ada data Bahan Forming yang terkait
            if ($formula->bahanForming()->exists()) {
                return redirect()->route('nomor-formula.index')
                    ->with('error', 'Tidak bisa menghapus! Ada ' . $formula->bahanForming()->count() . ' data Bahan Forming yang terkait dengan Nomor Formula ini.');
            }
            
            Log::info('NomorFormula Delete:', [
                'uuid' => $uuid,
                'data' => $formula->toArray()
            ]);
            
            $nomor_formula = $formula->nomor_formula;
            $formula->delete();
            
            return redirect()->route('nomor-formula.index')
                ->with('success', "Nomor Formula '{$nomor_formula}' berhasil dihapus");
                
        } catch (\Exception $e) {
            Log::error('NomorFormula Delete Error:', [
                'uuid' => $uuid,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get products by plan (AJAX)
     */
    public function getProductsByPlan(Request $request)
    {
        try {
            $id_plan = $request->get('id_plan');
            
            if (!$id_plan) {
                return response()->json(['error' => 'Plan ID required'], 400);
            }
            
            $produks = JenisProduk::where('id_plan', $id_plan)
                ->select('id', 'nama_produk')
                ->orderBy('nama_produk')
                ->get();
            
            return response()->json($produks);
            
        } catch (\Exception $e) {
            Log::error('Get Products By Plan Error:', [
                'message' => $e->getMessage(),
                'id_plan' => $request->get('id_plan')
            ]);
            
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}