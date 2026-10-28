<?php

namespace App\Http\Controllers;

use App\Models\JenisBreader;
use App\Models\JenisProduk;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JenisBreaderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $query = JenisBreader::with(['plan', 'user', 'produk'])
            ->orderBy('id_produk')
            ->orderBy('id');

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->whereHas('produk', function($q) use ($search) {
                $q->where('nama_produk', 'like', "%$search%");
            });
        }

        $data = $query->get();

        $grouped_data = [];
        foreach ($data as $item) {
            $produkId = $item->id_produk ?? 0;
            if (!isset($grouped_data[$produkId])) {
                $grouped_data[$produkId] = [];
            }
            $grouped_data[$produkId][] = $item;
        }

        // Pagination for products
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $currentItems = array_slice($grouped_data, ($currentPage - 1) * $perPage, $perPage, true);
        $paginated_data = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            count($grouped_data),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $rowspan_produk = [];
        foreach ($currentItems as $produkId => $items) {
            $rowspan_produk[$produkId] = count($items);
        }

        return view('super-admin.jenis_breader.index', [
            'grouped_data' => $currentItems,
            'rowspan_produk' => $rowspan_produk,
            'paginated_data' => $paginated_data,
            'search' => $search,
            'perPage' => $perPage
        ]);
    }


    public function create()
    {
        $user = auth()->user();
        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        return view('super-admin.jenis_breader.create', compact('produks', 'plans'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'id_plan' => 'required|exists:plan,id', // Tambahkan validasi plan
            'id_produk' => 'required|exists:jenis_produk,id',
            'jenis_breader' => 'required|string|max:255',
        ]);
        JenisBreader::create([
            'uuid' => Str::uuid(),
            'id_plan' => $request->id_plan, // Ambil dari request
            'user_id' => $user->id,
            'id_produk' => $request->id_produk,
            'jenis_breader' => $request->jenis_breader,
        ]);
        return redirect()->route('jenis-breader.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit($uuid)
    {
        $user = auth()->user();
        $item = JenisBreader::where('uuid', $uuid)->firstOrFail();
        if ($user->role == 'superadmin') {
            $produks = JenisProduk::all();
            $plans = Plan::all();
        } else {
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        return view('super-admin.jenis_breader.edit', compact('item', 'produks', 'plans'));
    }

    public function update(Request $request, $uuid)
    {
        $user = auth()->user();
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'jenis_breader' => 'required|string|max:255',
        ]);
        $item = JenisBreader::where('uuid', $uuid)->firstOrFail();
        $item->update([
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'id_produk' => $request->id_produk,
            'jenis_breader' => $request->jenis_breader,
        ]);
        return redirect()->route('jenis-breader.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $item = JenisBreader::where('uuid', $uuid)->firstOrFail();
        $item->delete();
        return redirect()->route('jenis-breader.index')->with('success', 'Data berhasil dihapus');
    }
}
