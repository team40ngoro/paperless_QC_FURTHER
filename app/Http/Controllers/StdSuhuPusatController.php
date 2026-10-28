<?php

namespace App\Http\Controllers;

use App\Models\StdSuhuPusat;
use App\Models\JenisProduk;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StdSuhuPusatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $query = StdSuhuPusat::with(['produk', 'user', 'plan']);

            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }

            $stdSuhuPusat = $query->get();
            return view('super-admin.std_suhu_pusat.index', compact('stdSuhuPusat'));
        } catch (\Throwable $e) {
            dd($e);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') {
            $products = JenisProduk::all();
            $plans = Plan::all();
        } else {
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        return view('super-admin.std_suhu_pusat.create', compact('products', 'plans'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_plan' => 'required|exists:plan,id',
            'std_suhu_pusat' => 'required|array|min:1|max:10',
            'std_suhu_pusat.*' => 'required|string|max:50',
        ]);

        $user = Auth::user();
        $effectivePlanId = ($user->role === 'superadmin') ? $validated['id_plan'] : $user->id_plan;

        if ($user->role !== 'superadmin') {
            $validated['id_plan'] = $effectivePlanId;
        }

        $produk = JenisProduk::findOrFail($validated['id_produk']);
        if ((int) $produk->id_plan !== (int) $effectivePlanId) {
            return back()->withErrors(['id_produk' => 'Produk tidak sesuai dengan plan yang dipilih.'])->withInput();
        }

        $exists = StdSuhuPusat::where('id_plan', $effectivePlanId)
            ->where('id_produk', $validated['id_produk'])
            ->exists();
        if ($exists) {
            return back()->withErrors(['id_produk' => 'Data Standar Suhu Pusat untuk produk dan plan ini sudah ada.'])->withInput();
        }

        StdSuhuPusat::create([
            'id_produk' => $validated['id_produk'],
            'id_plan' => $validated['id_plan'],
            'user_id' => $user->id,
            'std_suhu_pusat' => $request->std_suhu_pusat, // Langsung array, otomatis di-encode
        ]);

        return redirect()->route('std-suhu-pusat.index')->with('success', 'Data Standar Suhu Pusat berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $stdSuhuPusat = StdSuhuPusat::where('uuid', $uuid)->with(['produk', 'user', 'plan'])->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $stdSuhuPusat->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        return view('super-admin.std_suhu_pusat.show', compact('stdSuhuPusat'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $stdSuhuPusat = StdSuhuPusat::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $stdSuhuPusat->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
            $products = JenisProduk::all();
            $plans = Plan::all();
        } else {
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }

        return view('super-admin.std_suhu_pusat.edit', compact('stdSuhuPusat', 'products', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        $stdSuhuPusat = StdSuhuPusat::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $stdSuhuPusat->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui data ini.');
        }

        $validated = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_plan' => 'required|exists:plan,id',
            'std_suhu_pusat' => 'required|array|min:1|max:10',
            'std_suhu_pusat.*' => 'required|string|max:50',
        ]);

        $effectivePlanId = ($user->role === 'superadmin') ? $validated['id_plan'] : $user->id_plan;
        if ($user->role !== 'superadmin') {
            $validated['id_plan'] = $effectivePlanId;
        }

        $produk = JenisProduk::findOrFail($validated['id_produk']);
        if ((int) $produk->id_plan !== (int) $effectivePlanId) {
            return back()->withErrors(['id_produk' => 'Produk tidak sesuai dengan plan yang dipilih.'])->withInput();
        }

        $exists = StdSuhuPusat::where('id_plan', $effectivePlanId)
            ->where('id_produk', $validated['id_produk'])
            ->where('id', '!=', $stdSuhuPusat->id)
            ->exists();
        if ($exists) {
            return back()->withErrors(['id_produk' => 'Data Standar Suhu Pusat untuk produk dan plan ini sudah ada.'])->withInput();
        }

        // Exclude user_id from update to preserve original creator
        $stdSuhuPusat->update([
            'id_produk' => $validated['id_produk'],
            'id_plan' => $validated['id_plan'],
            'std_suhu_pusat' => $request->std_suhu_pusat, // Langsung array, otomatis di-encode
        ]);

        return redirect()->route('std-suhu-pusat.index')->with('success', 'Data Standar Suhu Pusat berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $stdSuhuPusat = StdSuhuPusat::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $stdSuhuPusat->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $stdSuhuPusat->delete();

        return redirect()->route('std-suhu-pusat.index')->with('success', 'Data Standar Suhu Pusat berhasil dihapus!');
    }
    /**
     * Get Std Suhu Pusat by Product ID for AJAX
     */
    public function getByProduk($produkId)
    {
        $stdSuhu = StdSuhuPusat::where('id_produk', $produkId)->first();
        
        if (!$stdSuhu) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
        
        return response()->json([
            'id' => $stdSuhu->id,
            'std_suhu_pusat' => $stdSuhu->std_suhu_pusat, // Otomatis jadi array karena Cast
        ]);
    }
}
