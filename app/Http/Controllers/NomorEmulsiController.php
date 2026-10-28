<?php

namespace App\Http\Controllers;

use App\Models\NomorEmulsi;
use App\Models\Plan;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NomorEmulsiController extends Controller
{
    public function index(Request $request)
    {
        $query = NomorEmulsi::with(['plan', 'produk', 'total_pemakaian', 'emulsi']);

        if (auth()->user()->role !== 'superadmin') {
            $query->where('id_plan', auth()->user()->id_plan);
        }

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

        $produks = \App\Models\JenisProduk::when(auth()->user()->role !== 'superadmin', function ($q) {
            $q->where('id_plan', auth()->user()->id_plan);
        })->orderBy('nama_produk')->get();

        return view('super-admin.nomor_emulsi.index', compact('data', 'groupedData', 'produks', 'selectedProdukId'));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->role == 'superadmin') {
            $plans = \App\Models\Plan::all();
            $produks = \App\Models\JenisProduk::all();
            $emulsis = \App\Models\JenisEmulsi::all();
        } else {
            $plans = \App\Models\Plan::where('id', $user->id_plan)->get();
            $produks = \App\Models\JenisProduk::where('id_plan', $user->id_plan)->get();
            $emulsis = \App\Models\JenisEmulsi::where('id_plan', $user->id_plan)->get();
        }

        $totalPemakaians = \App\Models\TotalPemakaianEmulsi::all();

        return view('super-admin.nomor_emulsi.create', compact('plans', 'produks', 'emulsis', 'totalPemakaians'));
    }
    public function store(Request $request)
    {
        \Log::info('NomorEmulsi Store Request:', $request->all());

        try {
            // Validasi input array
            $request->validate([
                'id_plan' => 'required|integer|exists:plan,id',
                'id_produk' => 'required|integer|exists:jenis_produk,id',
                'nama_emulsi_id' => 'required|integer|exists:jenis_emulsi,id',
                'total_pemakaian_id' => 'required|integer|exists:total_pemakaian_emulsi,id',
                'nomor_emulsi' => 'required|array|min:1',
                'nomor_emulsi.*' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) use ($request) {
                        $idProduk = $request->id_produk;
                        $totalPemakaianId = $request->total_pemakaian_id;
                        // Nomor boleh sama selama kombinasi produk + total_pemakaian + nomor belum ada
                        $exists = \App\Models\NomorEmulsi::where('id_produk', $idProduk)
                            ->where('total_pemakaian_id', $totalPemakaianId)
                            ->where('nomor_emulsi', $value)
                            ->exists();
                        if ($exists) {
                            $fail("Nomor '$value' sudah terdaftar untuk produk dan total pemakaian ini.");
                        }
                    },
                ],
            ]);

            $user_id = auth()->id();
            $created_count = 0;

            // Loop untuk setiap nomor emulsi
            foreach ($request->nomor_emulsi as $emulsi) {
                $trimmed_emulsi = trim($emulsi);
                
                if (!empty($trimmed_emulsi)) {
                    $data = [
                        'uuid' => Str::uuid(),
                        'id_plan' => $request->id_plan,
                        'id_produk' => $request->id_produk,
                        'nama_emulsi_id' => $request->nama_emulsi_id,
                        'total_pemakaian_id' => $request->total_pemakaian_id,
                        'nomor_emulsi' => $trimmed_emulsi,
                        'user_id' => $user_id,
                    ];
                    
                    NomorEmulsi::create($data);
                    $created_count++;
                }
            }

            $message = "Berhasil menambahkan {$created_count} Nomor Emulsi";

            return redirect()->route('nomor-emulsi.index')
                ->with('success', $message);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            \Log::error('NomorEmulsi Store Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($uuid)
    {
        $item = \App\Models\NomorEmulsi::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        if ($user->role == 'superadmin') {
            $plans = \App\Models\Plan::all();
            $produks = \App\Models\JenisProduk::all();
            $emulsis = \App\Models\JenisEmulsi::all();
            $totalPemakaians = \App\Models\TotalPemakaianEmulsi::all();
        } else {
            $plans = \App\Models\Plan::where('id', $user->id_plan)->get();
            $produks = \App\Models\JenisProduk::where('id_plan', $user->id_plan)->get();
            $emulsis = \App\Models\JenisEmulsi::where('id_plan', $user->id_plan)->get();
            $totalPemakaians = \App\Models\TotalPemakaianEmulsi::where('id_plan', $user->id_plan)->get();
        }

     //   $totalPemakaians = \App\Models\TotalPemakaianEmulsi::all();
        return view('super-admin.nomor_emulsi.edit', compact('item', 'plans', 'produks', 'emulsis', 'totalPemakaians'));
    }

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'nomor_emulsi' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $uuid) {
                    $idProduk = $request->id_produk;
                    $totalPemakaianId = $request->total_pemakaian_id;
                    // Nomor boleh sama selama kombinasi produk + total_pemakaian + nomor belum ada (kecuali record sendiri)
                    $exists = \App\Models\NomorEmulsi::where('id_produk', $idProduk)
                        ->where('total_pemakaian_id', $totalPemakaianId)
                        ->where('nomor_emulsi', $value)
                        ->where('uuid', '!=', $uuid)
                        ->exists();
                    if ($exists) {
                        $fail("Nomor '$value' sudah terdaftar untuk produk dan total pemakaian ini.");
                    }
                },
            ],
            'total_pemakaian_id' => 'required|integer',
            'id_produk' => 'required|integer',
            'nama_emulsi_id' => 'required|integer',
            'id_plan' => 'required|integer',
        ]);

        $item = \App\Models\NomorEmulsi::where('uuid', $uuid)->firstOrFail();
        $item->update([
            'nomor_emulsi' => $request->nomor_emulsi,
            'total_pemakaian_id' => $request->total_pemakaian_id,
            'id_produk' => $request->id_produk,
            'nama_emulsi_id' => $request->nama_emulsi_id,
            'id_plan' => $request->id_plan,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('nomor-emulsi.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $item = NomorEmulsi::where('uuid', $uuid)->firstOrFail();

        if ($item->bahanEmulsi()->exists()) {
            return redirect()->route('nomor-emulsi.index')
                ->with('error', 'Tidak bisa menghapus! Ada ' . $item->bahanEmulsi()->count() . ' data Bahan Emulsi yang terkait dengan Nomor Emulsi ini.');
        }

        $item->delete();
        return redirect()->route('nomor-emulsi.index')->with('success', 'Data berhasil dihapus');
    }

    public function getTotalPemakaianByEmulsi($nama_emulsi_id)
    {
        // Ambil semua total pemakaian yang punya nama_emulsi_id tersebut
        $totalPemakaians = \App\Models\TotalPemakaianEmulsi::where('nama_emulsi_id', $nama_emulsi_id)->get(['id', 'total_pemakaian']);
        return response()->json($totalPemakaians);
    }
}
