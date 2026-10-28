<?php

namespace App\Http\Controllers;

use App\Models\BahanBakuRiceBites;
use App\Models\Plan;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BahanBakuRiceBitesController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);
        $filterPlan = $request->get('id_plan');

        $query = BahanBakuRiceBites::with(['produk', 'plan', 'user'])
            ->orderBy('id_plan')
            ->orderBy('id_produk')
            ->orderBy('nomor_formula')
            ->orderBy('nama_bahan_baku');

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        } elseif ($filterPlan) {
            $query->where('id_plan', $filterPlan);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_bahan_baku', 'like', "%$search%")
                  ->orWhere('nomor_formula', 'like', "%$search%")
                  ->orWhereHas('produk', function ($pq) use ($search) {
                      $pq->where('nama_produk', 'like', "%$search%");
                  });
            });
        }

        // Get unique combinations of plan, produk, and formula for pagination
        $allData = $query->get();
        
        $paginated_combinations = $allData->groupBy(function($item) {
            return $item->id_plan . '_' . $item->id_produk . '_' . $item->nomor_formula;
        });

        // Simple manual pagination for the combinations
        $currentPage = $request->get('page', 1);
        $total = $paginated_combinations->count();
        $items_for_page = $paginated_combinations->slice(($currentPage - 1) * $perPage, $perPage);
        
        $paginated_data = new \Illuminate\Pagination\LengthAwarePaginator(
            $items_for_page,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Group only the data for the current page
        $grouped_data = [];
        $rowspan_produk = [];
        $rowspan_formula = [];
        
        foreach ($items_for_page as $key => $items) {
            $firstItem = $items->first();
            $planId = $firstItem->id_plan;
            $produkId = $firstItem->id_produk;
            $formula = $firstItem->nomor_formula ?? 'no_formula';
            
            $produkKey = $planId . '_' . $produkId;
            $formulaKey = $produkKey . '_' . $formula;
            
            if (!isset($grouped_data[$planId])) $grouped_data[$planId] = [];
            if (!isset($grouped_data[$planId][$produkId])) $grouped_data[$planId][$produkId] = [];
            
            $grouped_data[$planId][$produkId][$formula] = $items;
            
            if (!isset($rowspan_produk[$produkKey])) $rowspan_produk[$produkKey] = 0;
            $rowspan_produk[$produkKey] += $items->count();
            
            $rowspan_formula[$formulaKey] = $items->count();
        }

        $plans = Plan::orderBy('nama_plan')->get();

        return view('super-admin.bahan_baku_rice_bites.index', compact(
            'grouped_data', 
            'rowspan_produk', 
            'rowspan_formula', 
            'paginated_data',
            'search', 
            'perPage', 
            'plans', 
            'filterPlan'
        ));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role === 'superadmin') {
            $plans  = Plan::orderBy('nama_plan')->get();
            $produks = JenisProduk::orderBy('nama_produk')->get();
        } else {
            $plans  = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->orderBy('nama_produk')->get();
        }

        return view('super-admin.bahan_baku_rice_bites.create', compact('plans', 'produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_plan'          => 'required|exists:plan,id',
            'id_produk'        => 'required|exists:jenis_produk,id',
            'nomor_formula'    => 'nullable|string|max:255',
            'nama_bahan_baku'  => 'required|array|min:1',
            'nama_bahan_baku.*'=> 'required|string|max:255',
            'berat'            => 'nullable|array',
            'berat.*'          => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $created = 0;

        foreach ($request->nama_bahan_baku as $idx => $nama) {
            $nama = trim($nama);
            if (empty($nama)) continue;

            BahanBakuRiceBites::create([
                'uuid'            => Str::uuid(),
                'id_plan'         => $request->id_plan,
                'id_produk'       => $request->id_produk,
                'nomor_formula'   => $request->nomor_formula,
                'user_id'         => $user->id,
                'nama_bahan_baku' => $nama,
                'berat'           => $request->berat[$idx] ?? null,
            ]);
            $created++;
        }

        return redirect()->route('bahan-baku-rice-bites.index')
            ->with('success', "Berhasil menambahkan {$created} data bahan baku rice bites.");
    }

    public function edit($uuid)
    {
        $bahan = BahanBakuRiceBites::where('uuid', $uuid)->firstOrFail();
        $user  = Auth::user();

        if ($user->role === 'superadmin') {
            $plans  = Plan::orderBy('nama_plan')->get();
            $produks = JenisProduk::orderBy('nama_produk')->get();
        } else {
            $plans  = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $bahan->id_plan)->orderBy('nama_produk')->get();
        }

        return view('super-admin.bahan_baku_rice_bites.edit', compact('bahan', 'plans', 'produks'));
    }

    public function update(Request $request, $uuid)
    {
        $bahan = BahanBakuRiceBites::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'id_plan'         => 'required|exists:plan,id',
            'id_produk'       => 'required|exists:jenis_produk,id',
            'nomor_formula'   => 'nullable|string|max:255',
            'nama_bahan_baku' => 'required|string|max:255',
            'berat'           => 'nullable|string|max:100',
        ]);

        $bahan->update([
            'id_plan'         => $request->id_plan,
            'id_produk'       => $request->id_produk,
            'nomor_formula'   => $request->nomor_formula,
            'nama_bahan_baku' => $request->nama_bahan_baku,
            'berat'           => $request->berat,
        ]);

        return redirect()->route('bahan-baku-rice-bites.index')
            ->with('success', 'Data bahan baku rice bites berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $bahan = BahanBakuRiceBites::where('uuid', $uuid)->firstOrFail();
        $bahan->delete();

        return redirect()->route('bahan-baku-rice-bites.index')
            ->with('success', 'Data bahan baku rice bites berhasil dihapus.');
    }
}
