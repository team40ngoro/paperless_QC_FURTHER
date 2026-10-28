<?php

namespace App\Http\Controllers;

use App\Models\SuhuFrayer1;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Plan;
use App\Models\JenisProduk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SuhuFrayer1Controller extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = SuhuFrayer1::with(['user', 'produk', 'plan']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
          $data = $query->orderBy('created_at', 'desc')->get();

     //   $data = $query->get();
        return view('super-admin.suhu_frayer_1.index', compact('data'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('super-admin.suhu_frayer_1.create', compact('produks', 'plans'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $idPlanForValidation = $user->role !== 'superadmin'
            ? $user->id_plan
            : $request->input('id_plan');

        $request->validate([
        'id_plan' => 'required|exists:plan,id',
        'id_produk' => [
            'required',
            'exists:jenis_produk,id',
            Rule::unique('suhu_frayer_1', 'id_produk')->where(function ($query) use ($idPlanForValidation) {
                return $query->where('id_plan', $idPlanForValidation);
            }),
        ],
        'suhu_frayer'          => 'nullable|string|max:255',
        'waktu_penggorengan_1' => 'nullable|string|max:255',
        'suhu_frayer_2'        => 'nullable|string|max:255',
        'waktu_penggorengan_2' => 'nullable|string|max:255',
        'suhu_frayer_3'        => 'nullable|string|max:255',
        'waktu_penggorengan_3' => 'nullable|string|max:255',
        'suhu_frayer_4'        => 'nullable|string|max:255',
        'waktu_penggorengan_4' => 'nullable|string|max:255',
        'suhu_frayer_5'        => 'nullable|string|max:255',
        'waktu_penggorengan_5' => 'nullable|string|max:255',
        ], [
            'id_produk.unique' => 'Data Suhu Frayer untuk produk dan plan ini sudah ada.',
        ]);

        $data = $request->all();

        if ($user->role !== 'superadmin') {
            $data['id_plan'] = $user->id_plan;
        }

        $data['user_id'] = $user->id;
        $data['uuid'] = Str::uuid();

        SuhuFrayer1::create($data);

        return redirect()->route('suhu-frayer-1.index')->with('success', 'Data berhasil disimpan');
    }
    
    public function edit($uuid)
    {
        $data = SuhuFrayer1::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $data->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('super-admin.suhu_frayer_1.edit', compact('data', 'produks', 'plans'));
    }

    public function update(Request $request, $uuid)
    {
        $data = SuhuFrayer1::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $data->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui data ini.');
        }

        $idPlanForValidation = $user->role !== 'superadmin'
            ? $user->id_plan
            : $request->input('id_plan');

        $validated = $request->validate([
        'id_plan' => 'required|exists:plan,id',
        'id_produk' => [
            'required',
            'exists:jenis_produk,id',
            Rule::unique('suhu_frayer_1', 'id_produk')->where(function ($query) use ($idPlanForValidation) {
                return $query->where('id_plan', $idPlanForValidation);
            })->ignore($data->id),
        ],
        'suhu_frayer'          => 'required|string|max:255',
        'waktu_penggorengan_1' => 'nullable|string|max:255',
        'suhu_frayer_2'        => 'nullable|string|max:255',
        'waktu_penggorengan_2' => 'nullable|string|max:255',
        'suhu_frayer_3'        => 'nullable|string|max:255',
        'waktu_penggorengan_3' => 'nullable|string|max:255',
        'suhu_frayer_4'        => 'nullable|string|max:255',
        'waktu_penggorengan_4' => 'nullable|string|max:255',
        'suhu_frayer_5'        => 'nullable|string|max:255',
        'waktu_penggorengan_5' => 'nullable|string|max:255',
        ], [
            'id_produk.unique' => 'Data Suhu Frayer untuk produk dan plan ini sudah ada.',
        ]);

        if ($user->role !== 'superadmin') {
            $validated['id_plan'] = $user->id_plan;
        }

        $data->update($validated);
        return redirect()->route('suhu-frayer-1.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $data = SuhuFrayer1::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $data->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $data->delete();
        return redirect()->route('suhu-frayer-1.index')->with('success', 'Data berhasil dihapus');
    }
    public function getByProduk($produk_id)
    {
        $user = Auth::user();
        $query = SuhuFrayer1::where('id_produk', $produk_id);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $data = $query->get(['id', 'suhu_frayer']);
        return response()->json($data);
    }
}
