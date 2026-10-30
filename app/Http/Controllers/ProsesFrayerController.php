<?php

namespace App\Http\Controllers;

use App\Models\ProsesFrayer;
use App\Models\Frayer2;
use App\Models\Frayer3;
use App\Models\Frayer4;
use App\Models\Frayer5;
use App\Models\Plan;
use App\Models\User;
use App\Models\JenisProduk;
use App\Models\DataShift;
use App\Models\SuhuFrayer1;
use App\Models\WaktuPenggorengan;
use App\Models\HasilPenggorengan;
use App\Models\PembekuanIqfPenggorengan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProsesFrayerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $queryProsesFrayer = ProsesFrayer::with(['plan', 'user', 'produk', 'penggorengan.shift', 'suhuFrayer', 'waktuPenggorengan']);
        $queryFrayer2 = Frayer2::with(['plan', 'user', 'produk', 'penggorengan.shift', 'penggorenganData.shift', 'frayerData.penggorengan.shift', 'suhuFrayer2', 'waktuPenggorengan2']);
        $queryFrayer3 = Frayer3::with(['plan', 'user', 'produk', 'penggorengan.shift', 'suhuFrayer', 'waktuPenggorengan']);
        $queryFrayer4 = Frayer4::with(['plan', 'user', 'produk', 'penggorengan.shift', 'suhuFrayer', 'waktuPenggorengan']);
        $queryFrayer5 = Frayer5::with(['plan', 'user', 'produk', 'penggorengan.shift', 'suhuFrayer', 'waktuPenggorengan']);

        if ($user->role !== 'superadmin') {
            $queryProsesFrayer->where('id_plan', $user->id_plan);
            $queryFrayer2->where('id_plan', $user->id_plan);
            $queryFrayer3->where('id_plan', $user->id_plan);
            $queryFrayer4->where('id_plan', $user->id_plan);
            $queryFrayer5->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if (!empty($search)) {
            $queryProsesFrayer->whereHas('produk', function ($produkQuery) use ($search) {
                $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
            });
            $queryFrayer2->whereHas('produk', function ($produkQuery) use ($search) {
                $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
            });
            $queryFrayer3->whereHas('produk', function ($produkQuery) use ($search) {
                $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
            });
            $queryFrayer4->whereHas('produk', function ($produkQuery) use ($search) {
                $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
            });
            $queryFrayer5->whereHas('produk', function ($produkQuery) use ($search) {
                $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
            });
        }

        $perPage = request()->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50, 100]) ? $perPage : 10;

        $activeTab = request()->get('tab', 'frayer1');

        $prosesFrayer = $queryProsesFrayer->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'frayer1_page');
        $frayer2 = $queryFrayer2->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'frayer2_page');
        $frayer3 = $queryFrayer3->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'frayer3_page');
        $frayer4 = $queryFrayer4->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'frayer4_page');
        $frayer5 = $queryFrayer5->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'frayer5_page');

        return view('qc-sistem.proses_frayer.index', compact('prosesFrayer', 'frayer2', 'frayer3', 'frayer4', 'frayer5', 'search', 'perPage', 'activeTab'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') {
            $products = JenisProduk::all();
        } else {
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        // Load data relasi berdasarkan UUID dari request
        $penggorenganData = null;
        $predustData = null;
        $batteringData = null;
        $breaderData = null;

        if (request('penggorengan_uuid')) {
            $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])->where('uuid', request('penggorengan_uuid'))->first();
        }
        
        if (request('predust_uuid')) {
            $predustData = \App\Models\PembuatanPredust::with(['produk', 'jenisPredust'])->where('uuid', request('predust_uuid'))->first();
        }
        
        if (request('battering_uuid')) {
            $batteringData = \App\Models\ProsesBattering::with(['produk', 'penggorengan.shift'])->where('uuid', request('battering_uuid'))->first();
        }
        
        if (request('breader_uuid')) {
            $breaderData = \App\Models\ProsesBreader::with(['produk', 'penggorengan.shift', 'jenisBreader'])->where('uuid', request('breader_uuid'))->first();
        }
        
        
        if (request('breader_uuid')) {
            // Jika datang dari breader, coba ambil UUID dari proses sebelumnya yang terkait
            if ($breaderData) {
                // Cek apakah breader memiliki relasi ke proses sebelumnya
                if ($breaderData->battering_uuid) {
                    $batteringData = \App\Models\ProsesBattering::with(['produk', 'penggorengan.shift'])->where('uuid', $breaderData->battering_uuid)->first();
                    
                    // Jika ada battering, cek predust
                    if ($batteringData && $batteringData->predust_uuid) {
                        $predustData = \App\Models\PembuatanPredust::with(['produk', 'jenisPredust'])->where('uuid', $batteringData->predust_uuid)->first();
                        
                        // Jika ada predust, cek penggorengan
                        if ($predustData && $predustData->penggorengan_uuid) {
                            $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])->where('uuid', $predustData->penggorengan_uuid)->first();
                        }
                    } else {
                        // Jika tidak ada predust, cek penggorengan langsung dari battering (flow langsung)
                        if ($batteringData && $batteringData->penggorengan_uuid) {
                            $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])->where('uuid', $batteringData->penggorengan_uuid)->first();
                        }
                    }
                }
            }
        }

        return view('qc-sistem.proses_frayer.create', compact(
            'products', 
            'penggorenganData', 
            'predustData', 
            'batteringData', 
            'breaderData',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produk' => 'required',
            'id_suhu_frayer_1' => 'required',
            'id_waktu_penggorengan' => 'required',
            'aktual_penggorengan' => 'required|string',
            'aktual_suhu_penggorengan' => 'nullable|string',
            'tpm_minyak' => 'required|string',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required|date_format:H:i',
        ]);

        // Siapkan data untuk create dengan UUID relasi
        $createData = [
            'id_plan' => Auth::user()->id_plan,
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
            'jam' => $request->jam,
            'user_id' => Auth::id(),
            'id_produk' => $request->id_produk,
            'id_suhu_frayer_1' => $request->id_suhu_frayer_1,
            'id_waktu_penggorengan' => $request->id_waktu_penggorengan,
            'aktual_penggorengan' => $request->aktual_penggorengan,
            'aktual_suhu_penggorengan' => $request->aktual_suhu_penggorengan,
            'tpm_minyak' => $request->tpm_minyak,
        ];

        // Tambahkan UUID relasi jika ada
        if ($request->breader_uuid) {
            $createData['breader_uuid'] = $request->breader_uuid;
            
            // Ambil data breader untuk mendapatkan UUID proses sebelumnya
            $breaderData = \App\Models\ProsesBreader::where('uuid', $request->breader_uuid)->first();
            if ($breaderData) {
                if ($breaderData->battering_uuid) {
                    $createData['battering_uuid'] = $breaderData->battering_uuid;
                    $batteringData = \App\Models\ProsesBattering::where('uuid', $breaderData->battering_uuid)->first();
                    
                    if ($batteringData && $batteringData->predust_uuid) {
                        $createData['predust_uuid'] = $batteringData->predust_uuid;
                        $predustData = \App\Models\PembuatanPredust::where('uuid', $batteringData->predust_uuid)->first();
                        
                        if ($predustData && $predustData->penggorengan_uuid) {
                            $createData['penggorengan_uuid'] = $predustData->penggorengan_uuid;
                        }
                    } else {
                        // Jika tidak ada predust, cek penggorengan langsung dari battering (flow langsung)
                        if ($batteringData && $batteringData->penggorengan_uuid) {
                            $createData['penggorengan_uuid'] = $batteringData->penggorengan_uuid;
                        }
                    }
                }
            }
        } else {
            // Jika tidak dari breader, ambil UUID langsung dari request
            $createData['penggorengan_uuid'] = $request->penggorengan_uuid;
            $createData['predust_uuid'] = $request->predust_uuid;
            $createData['battering_uuid'] = $request->battering_uuid;
        }

        $prosesFrayer = ProsesFrayer::create($createData);

        // Cek apakah ada aksi lanjut ke Frayer 2
        if ($request->action === 'save_and_continue') {
            return response()->json([
                'success' => true,
                'message' => 'Data Frayer 1 berhasil disimpan. Silakan lanjutkan mengisi Frayer 2.',
                'frayer1_uuid' => $prosesFrayer->uuid,
                'data' => $prosesFrayer
            ]);
        }

        return redirect()->route('proses-frayer.index', ['tab' => 'frayer1'])->with('success', 'Data Proses Frayer 1 berhasil ditambahkan.');
    }

    public function edit($uuid)
    {
        $prosesFrayer = ProsesFrayer::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Authorization Check
        if ($user->role !== 'superadmin' && $prosesFrayer->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($user->role === 'superadmin') {
            $products = JenisProduk::all();
        } else {
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        $suhuFrayers = SuhuFrayer1::where('id_produk', $prosesFrayer->id_produk)->get();
        $waktuPenggorengans = WaktuPenggorengan::where('id_suhu_frayer_1', $prosesFrayer->id_suhu_frayer_1)->get();

        return view('qc-sistem.proses_frayer.edit', compact('prosesFrayer', 'products', 'suhuFrayers', 'waktuPenggorengans'));
    }

    public function update(Request $request, $uuid)
    {
        $prosesFrayer = ProsesFrayer::where('uuid', $uuid)->firstOrFail();
        $request->validate([
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'id_produk' => 'required',
            'id_suhu_frayer_1' => 'required',
            'id_waktu_penggorengan' => 'required',
            'aktual_penggorengan' => 'required|string',
            'aktual_suhu_penggorengan' => 'nullable|string',
            'tpm_minyak' => 'required|string',
        ]);

        $prosesFrayer->update([
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
            'id_produk' => $request->id_produk,
            'id_suhu_frayer_1' => $request->id_suhu_frayer_1,
            'id_waktu_penggorengan' => $request->id_waktu_penggorengan,
            'aktual_penggorengan' => $request->aktual_penggorengan,
            'aktual_suhu_penggorengan' => $request->aktual_suhu_penggorengan,
            'tpm_minyak' => $request->tpm_minyak,
        ]);

        return redirect()->route('proses-frayer.index', ['tab' => 'frayer1'])->with('success', 'Data Proses Frayer 1 berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $prosesFrayer = ProsesFrayer::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Authorization Check
        if ($user->role !== 'superadmin' && $prosesFrayer->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $isReferenced = Frayer2::where('frayer_uuid', $uuid)->exists()
            || HasilPenggorengan::where('frayer_uuid', $uuid)->exists()
            || PembekuanIqfPenggorengan::where('frayer_uuid', $uuid)->exists();

        if ($isReferenced) {
            return redirect()->route('proses-frayer.index', ['tab' => 'frayer1'])
                ->with('error', 'Data tidak dapat dihapus karena sudah terhubung dengan proses selanjutnya.');
        }

        $prosesFrayer->delete();
        return redirect()->route('proses-frayer.index', ['tab' => 'frayer1'])->with('success', 'Data Proses Frayer 1 berhasil dihapus.');
    }

    // AJAX Methods
    // public function getSuhuFrayerByProduk($id_produk)
    // {
    //     $user = Auth::user();
    //     $produk = JenisProduk::find($id_produk);

    //     if ($user->role !== 'superadmin' && (!$produk || $produk->id_plan !== $user->id_plan)) {
    //         return response()->json([], 403);
    //     }

    //     $suhuFrayers = SuhuFrayer1::where('id_produk', $id_produk)->get();
    //     return response()->json($suhuFrayers);
    // }
    public function getSuhuFrayerByProduk($id_produk)
    {
        $user = Auth::user();
        $produk = JenisProduk::find($id_produk);

        if ($user->role !== 'superadmin' && (!$produk || $produk->id_plan !== $user->id_plan)) {
            return response()->json([], 403);
        }

        $suhuFrayers = SuhuFrayer1::where('id_produk', $id_produk)->get()->map(function($item) {
            // Format display untuk menampilkan semua suhu frayer
            $display = "F1: {$item->suhu_frayer}°C";
            
            if ($item->suhu_frayer_3) {
                $display .= " | F3: {$item->suhu_frayer_3}°C";
            }
            
            if ($item->suhu_frayer_4) { // ✅ PERBAIKAN: hapus double $item
                $display .= " | F4: {$item->suhu_frayer_4}°C";
            }
            
            if ($item->suhu_frayer_5) {
                $display .= " | F5: {$item->suhu_frayer_5}°C";
            }
            
            return [
                'id' => $item->id,
                'suhu_frayer' => $item->suhu_frayer,
                'suhu_frayer_3' => $item->suhu_frayer_3,
                'suhu_frayer_4' => $item->suhu_frayer_4,
                'suhu_frayer_5' => $item->suhu_frayer_5,
                'display' => $display
            ];
        });
        
        return response()->json($suhuFrayers);
    }
    // Method untuk Frayer 1 (hanya suhu_frayer)
    public function getSuhuFrayer1ByProduk($id_produk)
    {
        $user = Auth::user();
        $produk = JenisProduk::find($id_produk);

        if ($user->role !== 'superadmin' && (!$produk || $produk->id_plan !== $user->id_plan)) {
            return response()->json([], 403);
        }

        $suhuFrayers = SuhuFrayer1::where('id_produk', $id_produk)->get()->map(function($item) {
            return [
                'id' => $item->id,
                'suhu_frayer' => $item->suhu_frayer,
                'display' => "{$item->suhu_frayer}°C"
            ];
        });
        
        return response()->json($suhuFrayers);
    }

    // Method untuk Frayer 3 (hanya suhu_frayer_3)
    public function getSuhuFrayer3ByProduk($id_produk)
    {
        $user = Auth::user();
        $produk = JenisProduk::find($id_produk);

        if ($user->role !== 'superadmin' && (!$produk || $produk->id_plan !== $user->id_plan)) {
            return response()->json([], 403);
        }

        $suhuFrayers = SuhuFrayer1::where('id_produk', $id_produk)
            ->whereNotNull('suhu_frayer_3')
            ->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'suhu_frayer' => $item->suhu_frayer_3,
                    'display' => "{$item->suhu_frayer_3}°C"
                ];
            });
        
        return response()->json($suhuFrayers);
    }
    // Method untuk Frayer 4
    public function getSuhuFrayer4ByProduk($id_produk)
    {
        $user = Auth::user();
        $produk = JenisProduk::find($id_produk);

        if ($user->role !== 'superadmin' && (!$produk || $produk->id_plan !== $user->id_plan)) {
            return response()->json([], 403);
        }

        $suhuFrayers = SuhuFrayer1::where('id_produk', $id_produk)
            ->whereNotNull('suhu_frayer_4')
            ->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'suhu_frayer' => $item->suhu_frayer_4,
                    'display' => "{$item->suhu_frayer_4}°C"
                ];
            });
        
        return response()->json($suhuFrayers);
    }
    // Method untuk suhu frayer 5
    public function getSuhuFrayer5ByProduk($id_produk)
    {
        $user = Auth::user();
        $produk = JenisProduk::find($id_produk);

        if ($user->role !== 'superadmin' && (!$produk || $produk->id_plan !== $user->id_plan)) {
            return response()->json([], 403);
        }

        $suhuFrayers = SuhuFrayer1::where('id_produk', $id_produk)
            ->whereNotNull('suhu_frayer_5')
            ->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'suhu_frayer' => $item->suhu_frayer_5,
                    'display' => "{$item->suhu_frayer_5}°C"
                ];
            });
        
        return response()->json($suhuFrayers);
    }

    public function getWaktuPenggorenganBySuhu($id_suhu)
    {
        $user = Auth::user();
        $suhu = SuhuFrayer1::with('produk')->find($id_suhu);

        if ($user->role !== 'superadmin' && (!$suhu || $suhu->produk->id_plan !== $user->id_plan)) {
            return response()->json([], 403);
        }

        if (!$suhu) {
            return response()->json([]);
        }

        $waktu = trim((string) ($suhu->waktu_penggorengan_1 ?? ''));
        if ($waktu === '') {
            return response()->json([]);
        }

        $waktuPenggorengan = WaktuPenggorengan::firstOrCreate(
            [
                'id_suhu_frayer_1' => $suhu->id,
                'waktu_penggorengan' => $waktu,
            ],
            [
                'user_id' => $user ? $user->id : null,
                'id_produk' => $suhu->id_produk,
                'id_plan' => $suhu->id_plan,
            ]
        );

        return response()->json([$waktuPenggorengan]);
    }
}
