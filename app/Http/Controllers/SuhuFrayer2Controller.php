<?php

namespace App\Http\Controllers;

use App\Models\SuhuFrayer2;
use App\Models\JenisProduk;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SuhuFrayer2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        $query = SuhuFrayer2::with(['produk', 'plan', 'user']);
        
        // Role-based filtering
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();
        
        return view('super-admin.suhu_frayer_2.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get products based on role
        if ($user->role === 'superadmin') {
            $products = JenisProduk::all();
        } else {
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        
        return view('super-admin.suhu_frayer_2.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Get the product to access its id_plan (also used for unique validation)
        $produk = JenisProduk::findOrFail($request->id_produk);
        
        $request->validate([
            'id_produk' => [
                'required',
                'exists:jenis_produk,id',
                Rule::unique('suhu_frayer_2', 'id_produk')->where(function ($query) use ($produk) {
                    return $query->where('id_plan', $produk->id_plan);
                }),
            ],
            'suhu_frayer_2' => 'required|string|max:255',
             'waktu_penggorengan_2' => 'required|string|max:255'
        ], [
            'id_produk.unique' => 'Data Suhu Frayer 2 untuk produk dan plan ini sudah ada.',
        ]);

        // Check if user has access to this product
        if ($user->role !== 'superadmin' && $produk->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk produk ini.');
        }

        SuhuFrayer2::create([
            'id_produk' => $request->id_produk,
            'id_plan' => $produk->id_plan,
            'user_id' => $user->id,
            'suhu_frayer_2' => $request->suhu_frayer_2,
             'waktu_penggorengan_2' => $request->waktu_penggorengan_2,
        ]);

        return redirect()->route('suhu-frayer-2.index')
            ->with('success', 'Data Suhu Frayer 2 berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $user = Auth::user();
        $item = SuhuFrayer2::with(['produk', 'plan', 'user'])->where('uuid', $uuid)->firstOrFail();

        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        return view('super-admin.suhu_frayer_2.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $user = Auth::user();
        $item = SuhuFrayer2::where('uuid', $uuid)->firstOrFail();

        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Get products based on role
        if ($user->role === 'superadmin') {
            $products = JenisProduk::all();
        } else {
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('super-admin.suhu_frayer_2.edit', compact('item', 'products'));
    }

       /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $user = Auth::user();
        $item = SuhuFrayer2::where('uuid', $uuid)->firstOrFail();

        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Get the product to access its id_plan (also used for unique validation)
        $produk = JenisProduk::findOrFail($request->id_produk);

        $request->validate([
            'id_produk' => [
                'required',
                'exists:jenis_produk,id',
                Rule::unique('suhu_frayer_2', 'id_produk')->where(function ($query) use ($produk) {
                    return $query->where('id_plan', $produk->id_plan);
                })->ignore($item->id),
            ],
            'suhu_frayer_2' => 'required|string|max:255',
            'waktu_penggorengan_2' => 'required|string|max:255'
        ], [
            'id_produk.unique' => 'Data Suhu Frayer 2 untuk produk dan plan ini sudah ada.',
        ]);

        // Check if user has access to this product
        if ($user->role !== 'superadmin' && $produk->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk produk ini.');
        }

        $item->update([
            'id_produk' => $request->id_produk,
            'id_plan' => $produk->id_plan,
            'suhu_frayer_2' => $request->suhu_frayer_2,
             'waktu_penggorengan_2' => $request->waktu_penggorengan_2,
        ]);

        return redirect()->route('suhu-frayer-2.index')
            ->with('success', 'Data Suhu Frayer 2 berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $user = Auth::user();
        $item = SuhuFrayer2::where('uuid', $uuid)->firstOrFail();

        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $item->delete();

        return redirect()->route('suhu-frayer-2.index')
            ->with('success', 'Data Suhu Frayer 2 berhasil dihapus.');
    }
    
}
