<?php

namespace App\Http\Controllers;

use App\Models\DataTumbling;
use App\Models\Plan;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DataTumblingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = DataTumbling::with(['plan', 'user', 'produk']);

        if ($user->role != 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $dataTumbling = $query->get();
        return view('super-admin.data_tumbling.index', compact('dataTumbling'));
    }

    public function show($uuid)
    {
        $user = Auth::user();

        $query = DataTumbling::with(['plan', 'user', 'produk'])->where('uuid', $uuid);
        if ($user->role != 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $dataTumbling = $query->firstOrFail();

        return view('super-admin.data_tumbling.show', compact('dataTumbling'));
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
        return view('super-admin.data_tumbling.create', compact('produks', 'plans'));
    }

    public function store(Request $request)
    {
        $duplicate = DataTumbling::where('id_plan', $request->id_plan)
            ->where('id_produk', $request->id_produk)
            ->exists();
        if ($duplicate) {
            return redirect()->back()
                ->withInput()
                ->with('info', 'Produk ini sudah ada pada plan ini. Silakan pilih produk lain atau edit data yang sudah ada.');
        }

        $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'drum_on' => 'required|string|max:255',
            'drum_off' => 'required|string|max:255',
            'drum_speed' => 'required|string|max:255',
            'total_waktu' => 'required|string|max:255',
            'tekanan_vakum' => 'required|string|max:255',
            'drum_on_non_vakum' => 'nullable|string|max:255',
            'drum_off_non_vakum' => 'nullable|string|max:255',
            'drum_speed_non_vakum' => 'nullable|string|max:255',
            'total_waktu_non_vakum' => 'nullable|string|max:255',
            'tekanan_non_vakum' => 'nullable|string|max:255',
        ]);

        DataTumbling::create([
            'uuid' => Str::uuid(),
            'id_plan' => $request->id_plan,
            'id_produk' => $request->id_produk,
            'user_id' => Auth::id(),
            'drum_on' => $request->drum_on,
            'drum_off' => $request->drum_off,
            'drum_speed' => $request->drum_speed,
            'total_waktu' => $request->total_waktu,
            'tekanan_vakum' => $request->tekanan_vakum,
            'drum_on_non_vakum' => $request->drum_on_non_vakum,
            'drum_off_non_vakum' => $request->drum_off_non_vakum,
            'drum_speed_non_vakum' => $request->drum_speed_non_vakum,
            'total_waktu_non_vakum' => $request->total_waktu_non_vakum,
            'tekanan_non_vakum' => $request->tekanan_non_vakum,
        ]);

        return redirect()->route('data-tumbling.index')->with('success', 'Data tumbling berhasil ditambahkan.');
    }

    public function edit($uuid)
    {
        $dataTumbling = DataTumbling::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        
        return view('super-admin.data_tumbling.edit', compact('dataTumbling', 'plans', 'produks'));
    }

    public function update(Request $request, $uuid)
    {
        $dataTumbling = DataTumbling::where('uuid', $uuid)->firstOrFail();

        $duplicate = DataTumbling::where('id_plan', $dataTumbling->id_plan)
            ->where('id_produk', $request->id_produk)
            ->where('id', '!=', $dataTumbling->id)
            ->exists();
        if ($duplicate) {
            return redirect()->back()
                ->withInput()
                ->with('info', 'Produk ini sudah ada pada plan ini. Silakan pilih produk lain atau edit data yang sudah ada.');
        }

        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'drum_on' => 'required|string|max:255',
            'drum_off' => 'required|string|max:255',
            'drum_speed' => 'required|string|max:255',
            'total_waktu' => 'required|string|max:255',
            'tekanan_vakum' => 'required|string|max:255',
            'drum_on_non_vakum' => 'nullable|string|max:255',
            'drum_off_non_vakum' => 'nullable|string|max:255',
            'drum_speed_non_vakum' => 'nullable|string|max:255',
            'total_waktu_non_vakum' => 'nullable|string|max:255',
            'tekanan_non_vakum' => 'nullable|string|max:255',
        ]);
        $data = $request->except(['user_id', 'id_plan']);
        
        $dataTumbling->update($data);

        return redirect()->route('data-tumbling.index')->with('success', 'Data tumbling berhasil diperbarui!');
    }

    public function destroy($uuid)
    {
        $dataTumbling = DataTumbling::where('uuid', $uuid)->firstOrFail();
        $dataTumbling->delete();

        return redirect()->route('data-tumbling.index')->with('success', 'Data tumbling berhasil dihapus!');
    }
}
