<?php

namespace App\Http\Controllers;

use App\Models\NamaFormulaFla;
use App\Models\Plan;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NamaFormulaFlaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = NamaFormulaFla::with(['plan', 'user', 'produk']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $formulaFla = $query->get();
        
        return view('super-admin.nama-formula-fla.index', compact('formulaFla'));
    }

    public function create()
    {
        $user = auth()->user();
        
        // Get produk based on user's plan
        if ($user->role == 'superadmin') {
            $produk = JenisProduk::with('plan')->get();
        } else {
            $produk = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        
        return view('super-admin.nama-formula-fla.create', compact('produk'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $data = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'nama_formula_fla' => 'required|string|max:255',
        ]);

        // Auto-populate id_plan and user_id from logged-in user
        $data['id_plan'] = $user->id_plan;
        $data['user_id'] = $user->id;
        
        NamaFormulaFla::create($data);

        return redirect()->route('nama-formula-fla.index')->with('success', 'Formula FLA berhasil ditambahkan');
    }

  

    public function edit($uuid)
    {
        $formulaFla = NamaFormulaFla::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Check authorization
        if ($user->role !== 'superadmin' && $formulaFla->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access');
        }
        
        // Get produk based on user's plan
        if ($user->role == 'superadmin') {
            $produk = JenisProduk::with('plan')->get();
        } else {
            $produk = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        
        return view('super-admin.nama-formula-fla.edit', compact('formulaFla', 'produk'));
    }

    public function update(Request $request, $uuid)
    {
        $formulaFla = NamaFormulaFla::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Check authorization
        if ($user->role !== 'superadmin' && $formulaFla->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access');
        }
        
        $data = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'nama_formula_fla' => 'required|string|max:255',
        ]);
        
        $formulaFla->update($data);
        
        return redirect()->route('nama-formula-fla.index')->with('success', 'Formula FLA berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $formulaFla = NamaFormulaFla::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Check authorization
        if ($user->role !== 'superadmin' && $formulaFla->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access');
        }
        
        $formulaFla->delete();
        
        return redirect()->route('nama-formula-fla.index')->with('success', 'Formula FLA berhasil dihapus');
    }

    // AJAX endpoint to get produk by plan
    public function getProdukByPlan($planId)
    {
        $produk = JenisProduk::where('id_plan', $planId)->get();
        return response()->json($produk);
    }
}
