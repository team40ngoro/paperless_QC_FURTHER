<?php

namespace App\Http\Controllers;

use App\Models\JenisProduk;
use App\Models\Plan;
use App\Models\StdBeratRheon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StdBeratRheonController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = StdBeratRheon::with(['plan', 'produk', 'user']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return view('super-admin.std_berat_rheon.index', compact('data'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $products = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('super-admin.std_berat_rheon.create', compact('plans', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'std_adonan' => 'required|string|max:255',
            'std_filler' => 'nullable|string|max:255',
            'std_after_forming' => 'nullable|string|max:255',
            'std_after_frying' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $effectivePlanId = ($user->role === 'superadmin') ? (int) $validated['id_plan'] : (int) $user->id_plan;

        if ($user->role !== 'superadmin') {
            $validated['id_plan'] = $effectivePlanId;
        }

        $produk = JenisProduk::findOrFail($validated['id_produk']);
        if ((int) $produk->id_plan !== (int) $effectivePlanId) {
            return back()->withErrors(['id_produk' => 'Produk tidak sesuai dengan plan yang dipilih.'])->withInput();
        }

        $exists = StdBeratRheon::where('id_plan', $effectivePlanId)
            ->where('id_produk', $validated['id_produk'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['id_produk' => 'Data Standar Berat Rheon untuk produk dan plan ini sudah ada.'])->withInput();
        }

        StdBeratRheon::create([
            'id_plan' => $validated['id_plan'],
            'id_produk' => $validated['id_produk'],
            'user_id' => $user->id,
            'std_adonan' => $validated['std_adonan'],
            'std_filler' => $validated['std_filler'] ?? null,
            'std_after_forming' => $validated['std_after_forming'] ?? null,
            'std_after_frying' => $validated['std_after_frying'] ?? null,
        ]);

        return redirect()->route('std-berat-rheon.index')->with('success', 'Data Standar Berat Rheon berhasil ditambahkan.');
    }

    public function edit($uuid)
    {
        $item = StdBeratRheon::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $products = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('super-admin.std_berat_rheon.edit', compact('item', 'plans', 'products'));
    }

    public function update(Request $request, $uuid)
    {
        $item = StdBeratRheon::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui data ini.');
        }

        $validated = $request->validate([
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'std_adonan' => 'required|string|max:255',
            'std_filler' => 'nullable|string|max:255',
            'std_after_forming' => 'nullable|string|max:255',
            'std_after_frying' => 'nullable|string|max:255',
        ]);

        $effectivePlanId = ($user->role === 'superadmin') ? (int) $validated['id_plan'] : (int) $user->id_plan;

        if ($user->role !== 'superadmin') {
            $validated['id_plan'] = $effectivePlanId;
        }

        $produk = JenisProduk::findOrFail($validated['id_produk']);
        if ((int) $produk->id_plan !== (int) $effectivePlanId) {
            return back()->withErrors(['id_produk' => 'Produk tidak sesuai dengan plan yang dipilih.'])->withInput();
        }

        $exists = StdBeratRheon::where('id_plan', $effectivePlanId)
            ->where('id_produk', $validated['id_produk'])
            ->where('id', '!=', $item->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['id_produk' => 'Data Standar Berat Rheon untuk produk dan plan ini sudah ada.'])->withInput();
        }

        $item->update([
            'id_plan' => $validated['id_plan'],
            'id_produk' => $validated['id_produk'],
            'std_adonan' => $validated['std_adonan'],
            'std_filler' => $validated['std_filler'] ?? null,
            'std_after_forming' => $validated['std_after_forming'] ?? null,
            'std_after_frying' => $validated['std_after_frying'] ?? null,
        ]);

        return redirect()->route('std-berat-rheon.index')->with('success', 'Data Standar Berat Rheon berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $item = StdBeratRheon::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $item->delete();

        return redirect()->route('std-berat-rheon.index')->with('success', 'Data Standar Berat Rheon berhasil dihapus.');
    }
}
