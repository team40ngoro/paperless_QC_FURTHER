<?php

namespace App\Http\Controllers;

use App\Models\Frayer3;
use App\Models\Plan;
use App\Models\User;
use App\Models\JenisProduk;
use App\Models\DataShift;
use App\Models\SuhuFrayer1;
use App\Models\WaktuPenggorengan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Frayer3Controller extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Authorization check
        if (!in_array($user->role, ['superadmin', 'admin', 'user'])) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
        
        return redirect()->route('proses-frayer.index', ['tab' => 'frayer3']);
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role == 'superadmin') {
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
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'id_produk' => 'required',
            'id_suhu_frayer' => 'required',
            'jam' => 'required|date_format:H:i',
            'id_waktu_penggorengan' => 'required',
            'aktual_penggorengan' => 'required|string',
            'aktual_suhu_penggorengan' => 'required|string',
            'tpm_minyak' => 'required|string',
        ]);

        // $data = $request->all();
        // $data['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
        // $data['id_plan'] = Auth::user()->id_plan;
        // $data['user_id'] = Auth::id();

        // Siapkan data untuk create dengan UUID relasi
        $createData = [
            'id_plan' => Auth::user()->id_plan,
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
            'jam' => Carbon::createFromFormat('H:i', $request->jam)->format('H:i'),
            'user_id' => Auth::id(),
            'id_produk' => $request->id_produk,
            'id_suhu_frayer' => $request->id_suhu_frayer,
            'id_waktu_penggorengan' => $request->id_waktu_penggorengan,
            'aktual_suhu_penggorengan' => $request->aktual_suhu_penggorengan,
            'aktual_penggorengan' => $request->aktual_penggorengan,
            'tpm_minyak' => $request->tpm_minyak,
        ];

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
                        // Jika tidak ada predust, cek penggorengan langsung dari battering
                        if ($batteringData && $batteringData->penggorengan_uuid) {
                            $createData['penggorengan_uuid'] = $batteringData->penggorengan_uuid;
                        }
                    }
                }
            }
        } else {
            // Jika tidak dari breader, ambil UUID langsung dari request
            if ($request->penggorengan_uuid) {
                $createData['penggorengan_uuid'] = $request->penggorengan_uuid;
            }
            if ($request->predust_uuid) {
                $createData['predust_uuid'] = $request->predust_uuid;
            }
            if ($request->battering_uuid) {
                $createData['battering_uuid'] = $request->battering_uuid;
            }
        }

        Frayer3::create($createData);

        return redirect()->route('proses-frayer.index', ['tab' => 'frayer3'])->with('success', 'Data Frayer 3 berhasil ditambahkan.');
    }

    public function edit($uuid)
    {
        $frayer3 = Frayer3::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        if ($user->role == 'superadmin') {
            $products = JenisProduk::all();
        } else {
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        
        $suhuFrayers = SuhuFrayer1::where('id_produk', $frayer3->id_produk)->get();
        $waktuPenggorengans = WaktuPenggorengan::where('id_suhu_frayer_1', $frayer3->id_suhu_frayer)->get();

        return view('qc-sistem.frayer_3.edit', compact('frayer3', 'products', 'suhuFrayers', 'waktuPenggorengans'));
    }

    public function update(Request $request, $uuid)
    {
        $frayer3 = Frayer3::where('uuid', $uuid)->firstOrFail();
        $request->validate([
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'id_produk' => 'required',
            'id_suhu_frayer' => 'required',
            'id_waktu_penggorengan' => 'required',
            'aktual_penggorengan' => 'required|string',
            'aktual_suhu_penggorengan' => 'nullable|string',
            'tpm_minyak' => 'required|string',
        ]);

        $data = $request->all();
        $data['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');

        $frayer3->update($data);

        return redirect()->route('proses-frayer.index', ['tab' => 'frayer3'])->with('success', 'Data Frayer 3 berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $frayer3 = Frayer3::where('uuid', $uuid)->firstOrFail();
        $frayer3->delete();
        return redirect()->route('proses-frayer.index', ['tab' => 'frayer3'])->with('success', 'Data Frayer 3 berhasil dihapus.');
    }

    // AJAX Methods
    public function getSuhuFrayerByProduk($id_produk)
    {
        $suhuFrayers = SuhuFrayer1::where('id_produk', $id_produk)->get();
        return response()->json($suhuFrayers);
    }

    public function getWaktuPenggorenganBySuhu($id_suhu)
    {
        $user = Auth::user();
        $suhu = SuhuFrayer1::find($id_suhu);

        if (!$suhu) {
            return response()->json([]);
        }

        $waktu = trim((string) ($suhu->waktu_penggorengan_3 ?? ''));
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
