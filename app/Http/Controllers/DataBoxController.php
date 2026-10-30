<?php

namespace App\Http\Controllers;

use App\Models\DataBox;
use App\Models\JenisProduk;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataBoxController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = DataBox::with(['produk', 'plan', 'user']);

        if ($user->role != 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $data = $query->orderBy('created_at', 'desc')->get();
        return view('super-admin.data_box.index', compact('data'));
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
        return view('super-admin.data_box.create', compact('produks', 'plans'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_plan' => 'required|exists:plan,id',
            'std_box' => 'required|string|max:255',
            'berat_produk' => 'required|',
            'std_isi_pack_karton' => 'nullable|integer',
        ]);

        $validatedData['user_id'] = Auth::id();
        $validatedData['berat'] = $request->input('berat_produk');
        $validatedData['std_isi_pack_karton'] = $request->input('std_isi_pack_karton');
        DataBox::create($validatedData);

        return redirect()->route('data-box.index')->with('success', 'Data Standart BOX berhasil ditambahkan.');
    }

    public function edit($uuid)
    {
        $dataBox = DataBox::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        return view('super-admin.data_box.edit', compact('dataBox', 'produks', 'plans'));
    }

    public function update(Request $request, $uuid)
    {
        $dataBox = DataBox::where('uuid', $uuid)->firstOrFail();
        $validatedData = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_plan' => 'required|exists:plan,id',
            'std_box' => 'required|string|max:255',
            'berat_produk' => 'required|',
            'std_isi_pack_karton' => 'nullable|integer',
        ]);
        $validatedData['berat'] = $request->input('berat_produk');
        $validatedData['std_isi_pack_karton'] = $request->input('std_isi_pack_karton');
        $dataBox->update($validatedData);

        return redirect()->route('data-box.index')->with('success', 'Data Standart BOX berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $dataBox = DataBox::where('uuid', $uuid)->firstOrFail();
        $dataBox->delete();
        return redirect()->route('data-box.index')->with('success', 'Data Standart BOX berhasil dihapus.');
    }
}
