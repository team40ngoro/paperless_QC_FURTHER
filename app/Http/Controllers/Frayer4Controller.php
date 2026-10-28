<?php

namespace App\Http\Controllers;

use App\Models\Frayer4;
use App\Models\JenisProduk;
use App\Models\DataShift;
use App\Models\SuhuFrayer1;
use App\Models\WaktuPenggorengan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Frayer4Controller extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Authorization check
        if (!in_array($user->role, ['superadmin', 'admin', 'user'])) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
        
        return redirect()->route('proses-frayer.index', ['tab' => 'frayer4']);
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
            $batteringData = \App\Models\ProsesBattering::with(['produk'])->where('uuid', request('battering_uuid'))->first();
        }
        
        if (request('breader_uuid')) {
            $breaderData = \App\Models\ProsesBreader::with(['produk', 'jenisBreader'])->where('uuid', request('breader_uuid'))->first();
            
            // Jika datang dari breader, coba ambil UUID dari proses sebelumnya yang terkait
            if ($breaderData) {
                // Cek apakah breader memiliki relasi ke proses sebelumnya
                if ($breaderData->battering_uuid) {
                    $batteringData = \App\Models\ProsesBattering::with(['produk'])->where('uuid', $breaderData->battering_uuid)->first();
                    
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
            'breaderData'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_suhu_frayer' => 'required|exists:suhu_frayer_1,id',
            'id_waktu_penggorengan' => 'required|exists:waktu_penggorengan,id',
            'aktual_penggorengan' => 'required|string',
            'aktual_suhu_penggorengan' => 'required|string',
            'tpm_minyak' => 'required|string',
            'tanggal' => 'required',
            'jam' => 'required|date_format:H:i',
        ]);

        $user = Auth::user();
        
        // Siapkan data untuk create dengan UUID relasi
        $createData = [
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'id_produk' => $request->id_produk,
            'id_suhu_frayer' => $request->id_suhu_frayer,
            'id_waktu_penggorengan' => $request->id_waktu_penggorengan,
            'aktual_suhu_penggorengan' => $request->aktual_suhu_penggorengan,
            'aktual_penggorengan' => $request->aktual_penggorengan,
            'tpm_minyak' => $request->tpm_minyak,
            'jam' => Carbon::createFromFormat('H:i', $request->jam)->format('H:i'),
        ];
        // Convert tanggal format
        if ($request->tanggal) {
            $createData['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
        }

        // Tambahkan UUID relasi jika ada
        if ($request->breader_uuid) {
            $createData['breader_uuid'] = $request->breader_uuid;
            
            // Ambil data breader untuk mendapatkan UUID proses sebelumnya
            $breaderData = \App\Models\ProsesBreader::with(['produk', 'jenisBreader'])->where('uuid', $request->breader_uuid)->first();
            if ($breaderData) {
                if ($breaderData->battering_uuid) {
                    $createData['battering_uuid'] = $breaderData->battering_uuid;
                    $batteringData = \App\Models\ProsesBattering::with(['produk'])->where('uuid', $breaderData->battering_uuid)->first();
                    
                    if ($batteringData && $batteringData->predust_uuid) {
                        $createData['predust_uuid'] = $batteringData->predust_uuid;
                        $predustData = \App\Models\PembuatanPredust::with(['produk', 'jenisPredust'])->where('uuid', $batteringData->predust_uuid)->first();
                        
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

        Frayer4::create($createData);

        return redirect()->route('proses-frayer.index', ['tab' => 'frayer4'])->with('success', 'Data Frayer 4 berhasil ditambahkan');
    }
    public function show($uuid)
    {
        $frayer4 = Frayer4::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        if ($user->role !== 'superadmin' && $frayer4->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        return view('qc-sistem.frayer_4.show', compact('frayer4'));
    }

    public function edit($uuid)
    {
        $frayer4 = Frayer4::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        if ($user->role !== 'superadmin' && $frayer4->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->role === 'superadmin') {
            $products = JenisProduk::all();
            $suhuFrayers = SuhuFrayer1::where('id_produk', $frayer4->id_produk)->get();
            $waktuPenggorengans = WaktuPenggorengan::where('id_suhu_frayer_1', $frayer4->id_suhu_frayer)->get();
        } else {
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
            $suhuFrayers = SuhuFrayer1::where('id_produk', $frayer4->id_produk)->where('id_plan', $user->id_plan)->get();
            $waktuPenggorengans = WaktuPenggorengan::where('id_suhu_frayer_1', $frayer4->id_suhu_frayer)->where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.frayer_4.edit', compact('frayer4', 'products', 'suhuFrayers', 'waktuPenggorengans'));
    }

    public function update(Request $request, $uuid)
    {
        $frayer4 = Frayer4::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        if ($user->role !== 'superadmin' && $frayer4->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_suhu_frayer' => 'required|exists:suhu_frayer_1,id',
            'id_waktu_penggorengan' => 'required|exists:waktu_penggorengan,id',
            'aktual_penggorengan' => 'required|string',
            'aktual_suhu_penggorengan' => 'nullable|string',
            'tpm_minyak' => 'required|string',
            'tanggal' => 'required'
        ]);

        $data = $request->all();
        
        // Convert tanggal format
        if ($request->tanggal) {
            $data['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
        }

        $frayer4->update($data);

        return redirect()->route('proses-frayer.index', ['tab' => 'frayer4'])->with('success', 'Data Frayer 4 berhasil diperbarui');
    }

    public function destroy($uuid)
    {
        $frayer4 = Frayer4::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        if ($user->role !== 'superadmin' && $frayer4->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $frayer4->delete();
        return redirect()->route('proses-frayer.index', ['tab' => 'frayer4'])->with('success', 'Data Frayer 4 berhasil dihapus');
    }

    public function getSuhuFrayerByProduk($id_produk)
    {
        $user = Auth::user();
        $query = SuhuFrayer1::where('id_produk', $id_produk);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $suhuFrayers = $query->get();
        return response()->json($suhuFrayers);
    }

    public function getWaktuPenggorenganBySuhu($id_suhu)
    {
        $user = Auth::user();
        $suhu = SuhuFrayer1::find($id_suhu);

        if (!$suhu) {
            return response()->json([]);
        }

        if ($user->role !== 'superadmin' && $suhu->id_plan !== $user->id_plan) {
            return response()->json([], 403);
        }

        $waktu = trim((string) ($suhu->waktu_penggorengan_4 ?? ''));
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
