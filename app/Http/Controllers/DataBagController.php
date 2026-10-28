<?php

namespace App\Http\Controllers;

use App\Models\DataBag;
use App\Models\JenisProduk;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataBagController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = DataBag::with(['produk', 'plan', 'user']);

        if ($user->role != 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $data = $query->orderBy('created_at', 'desc')->get();
        return view('super-admin.data_bag.index', compact('data'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        return view('super-admin.data_bag.create', compact('produks', 'plans'));
    }

    public function store(Request $request)
    {
        // 1. Validasi input dan simpan hasilnya
        $validatedData = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_plan' => 'required|exists:plan,id',
            'std_bag' => 'required|string|max:255',
            'berat_produk' => 'required|',

        ]);

        // 2. Tambahkan user_id dari user yang login
        $validatedData['user_id'] = Auth::id();
        $validatedData['berat'] = $request->input('berat_produk');
        // 3. Buat record baru HANYA dengan data yang sudah divalidasi
        // Trait HasUuids di model akan mengisi 'uuid' secara otomatis.
        DataBag::create($validatedData);

        return redirect()->route('data-bag.index')->with('success', 'Data Standart BAG berhasil ditambahkan.');
    }

    public function edit($uuid)
    {
        $dataBag = DataBag::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        return view('super-admin.data_bag.edit', compact('dataBag', 'produks', 'plans'));
    }

    public function update(Request $request, $uuid)
    {
        $dataBag = DataBag::where('uuid', $uuid)->firstOrFail();
        // 1. Validasi input dan simpan hasilnya
        $validatedData = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_plan' => 'required|exists:plan,id',
            'std_bag' => 'required|string|max:255',
            'berat_produk' => 'required|',
        ]);
        $validatedData['berat'] = $request->input('berat_produk');
        // 2. Update record HANYA dengan data yang sudah divalidasi
        $dataBag->update($validatedData);

        return redirect()->route('data-bag.index')->with('success', 'Data Standart BAG berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $dataBag = DataBag::where('uuid', $uuid)->firstOrFail();
        $dataBag->delete();
        return redirect()->route('data-bag.index')->with('success', 'Data Standart BAG berhasil dihapus.');
    }
}