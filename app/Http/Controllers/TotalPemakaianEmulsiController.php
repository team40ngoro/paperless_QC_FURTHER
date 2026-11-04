<?php

namespace App\Http\Controllers;

use App\Models\TotalPemakaianEmulsi;
use App\Models\JenisEmulsi;
use App\Models\Plan;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TotalPemakaianEmulsiController extends Controller
{
    private function generateUniqueTotalPemakaian($totalPemakaian, $idProduk, $excludeUuid = null)
    {
        $baseValue = preg_replace('/\(\d+\)$/', '', $totalPemakaian);
        $baseValue = trim($baseValue);

        $query = TotalPemakaianEmulsi::where('id_produk', $idProduk)
            ->where('total_pemakaian', $baseValue);

        if ($excludeUuid) {
            $query->where('uuid', '!=', $excludeUuid);
        }

        if (!$query->exists()) {
            return $baseValue;
        }

        $counter = 1;
        while (true) {
            $candidate = $baseValue . '(' . $counter . ')';
            $exists = TotalPemakaianEmulsi::where('id_produk', $idProduk)
                ->where('total_pemakaian', $candidate);

            if ($excludeUuid) {
                $exists->where('uuid', '!=', $excludeUuid);
            }

            if (!$exists->exists()) {
                return $candidate;
            }
            $counter++;
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = TotalPemakaianEmulsi::with(['emulsi', 'user', 'produk', 'plan']);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        // Filter by produk
        $selectedProdukId = $request->get('id_produk');
        if ($selectedProdukId) {
            $query->where('id_produk', $selectedProdukId);
        }

        $data = $query->get();
        $groupedCollection = $data->groupBy('id_produk');

        // Paginate by product group (4 produk per page)
        $perPage = 4;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $currentItems = $groupedCollection->slice(($currentPage - 1) * $perPage, $perPage);

        $groupedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $groupedCollection->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        // Get list of produk for filter dropdown
        $produks = \App\Models\JenisProduk::when($user->role !== 'superadmin', function ($q) use ($user) {
            $q->where('id_plan', $user->id_plan);
        })->orderBy('nama_produk')->get();

        return view('super-admin.total_pemakaian_emulsi.index', compact('data', 'groupedData', 'produks', 'selectedProdukId'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
            $emulsis = JenisEmulsi::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $emulsis = JenisEmulsi::where('id_plan', $user->id_plan)->get();
        }
        return view('super-admin.total_pemakaian_emulsi.create', compact('emulsis', 'produks','plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'total_pemakaian' => 'required|string|max:255',
            'nama_emulsi_id' => 'required|exists:jenis_emulsi,id',
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
        ]);

        $user = Auth::user();
        $data = $request->all();

        if ($user->role !== 'superadmin') {
            $data['id_plan'] = $user->id_plan;
        }

        $data['total_pemakaian'] = $this->generateUniqueTotalPemakaian(
            $data['total_pemakaian'],
            $data['id_produk']
        );

        $data['user_id'] = $user->id;
        $data['uuid'] = Str::uuid();

        TotalPemakaianEmulsi::create($data);

        return redirect()->route('total-pemakaian-emulsi.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($uuid)
    {
        $item = TotalPemakaianEmulsi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'superadmin'], true) && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
            $emulsis = JenisEmulsi::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $emulsis = JenisEmulsi::where('id_plan', $user->id_plan)->where('id_produk', $item->id_produk)->get();
        }
        return view('super-admin.total_pemakaian_emulsi.edit', compact('item', 'emulsis', 'produks','plans'));
    }

    public function update(Request $request, $uuid)
    {
        $item = TotalPemakaianEmulsi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui data ini.');
        }

        $validated = $request->validate([
            'total_pemakaian' => 'required|string|max:255',
            'nama_emulsi_id' => 'required|exists:jenis_emulsi,id',
            'id_plan' => 'required|exists:plan,id',
            'id_produk' => 'required|exists:jenis_produk,id',
        ]);

        if ($user->role !== 'superadmin') {
            $validated['id_plan'] = $user->id_plan;
        }

        $validated['total_pemakaian'] = $this->generateUniqueTotalPemakaian(
            $validated['total_pemakaian'],
            $validated['id_produk'],
            $uuid
        );

        $item->update($validated);
        return redirect()->route('total-pemakaian-emulsi.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $item = TotalPemakaianEmulsi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }
        
        // Cek apakah ada data Nomor Emulsi yang terkait
        if ($item->nomorEmulsi()->exists()) {
            return redirect()->route('total-pemakaian-emulsi.index')
                ->with('error', 'Tidak bisa menghapus! Ada ' . $item->nomorEmulsi()->count() . ' data Nomor Emulsi yang terkait dengan Total Pemakaian Emulsi ini.');
        }
        
        $item->delete();
        return redirect()->route('total-pemakaian-emulsi.index')->with('success', 'Data berhasil dihapus');
    }
    
    public function getEmulsiByPlanProduk(Request $request)
    {
        $user = Auth::user();
        $id_plan = $request->id_plan;
        $id_produk = $request->id_produk;

        if ($user->role !== 'superadmin' && $id_plan != $user->id_plan) {
            return response()->json([], 403);
        }

        $emulsis = JenisEmulsi::where('id_plan', $id_plan)
            ->where('id_produk', $id_produk)
            ->get(['id', 'nama_emulsi']);

        return response()->json($emulsis);
    }
}