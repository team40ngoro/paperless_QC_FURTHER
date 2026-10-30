<?php

namespace App\Http\Controllers;

use App\Models\Frayer2;
use App\Models\ProsesFrayer;
use App\Models\Plan;
use App\Models\User;
use App\Models\JenisProduk;
use App\Models\DataShift;
use App\Models\SuhuFrayer1;
use App\Models\SuhuFrayer2;
use App\Models\WaktuPenggorengan;
use App\Models\WaktuPenggorengan2;
use App\Models\ProsesBreader;
use App\Models\ProsesBattering;
use App\Models\PembuatanPredust;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Frayer2Controller extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $queryFrayer2 = Frayer2::with(['plan', 'user', 'produk', 'suhuFrayer2', 'waktuPenggorengan2']);
        $queryProsesFrayer = ProsesFrayer::with(['plan', 'user', 'produk', 'suhuFrayer', 'waktuPenggorengan']);

        if ($user->role != 'superadmin') {
            $queryFrayer2->where('id_plan', $user->id_plan);
            $queryProsesFrayer->where('id_plan', $user->id_plan);
        }

        $frayer2 = $queryFrayer2->get();
        $prosesFrayer = $queryProsesFrayer->get();
        return view('qc-sistem.proses_frayer.index', compact('frayer2', 'prosesFrayer'));
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
        $frayerData = null;

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
        }
        if (request('frayer_uuid')) {
            $frayerData = \App\Models\ProsesFrayer::where('uuid', request('frayer_uuid'))->first();
        }

        return view('qc-sistem.proses_frayer.create', compact('products', 'frayerData', 'penggorenganData', 'predustData', 'batteringData', 'breaderData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'id_produk' => 'required',
            'id_suhu_frayer_2' => 'required',
            'id_waktu_penggorengan_2' => 'required',
            'aktual_penggorengan' => 'required|string',
            'aktual_suhu_penggorengan' => 'nullable|string',
            'tpm_minyak' => 'required|string',
            'jam' => 'required|string',
            'penggorengan_uuid' => 'nullable|string',
            'predust_uuid' => 'nullable|string',
            'battering_uuid' => 'nullable|string',
            'breader_uuid' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
        $data['id_plan'] = Auth::user()->id_plan;
        $data['user_id'] = Auth::id();
        $data['jam'] = Carbon::createFromFormat('H:i', $request->jam)->format('H:i');

        // Tambahkan UUID relasi jika ada
        if ($request->frayer_uuid) {
            $data['frayer_uuid'] = $request->frayer_uuid;
        }

        // Jika datang dari proses sebelumnya (breader/battering/predust), turunkan UUID relasi lain
        // agar penggorengan_uuid tidak kosong (kasus umum: skip Frayer1 lalu input Frayer2).
        if ($request->breader_uuid && !$request->battering_uuid) {
            $breaderData = ProsesBreader::where('uuid', $request->breader_uuid)->first();
            if ($breaderData && $breaderData->battering_uuid) {
                $data['battering_uuid'] = $breaderData->battering_uuid;
            }

            if ($breaderData && empty($request->predust_uuid) && !empty($breaderData->predust_uuid)) {
                $data['predust_uuid'] = $breaderData->predust_uuid;
            }

            if ($breaderData && empty($request->penggorengan_uuid) && !empty($breaderData->penggorengan_uuid)) {
                $data['penggorengan_uuid'] = $breaderData->penggorengan_uuid;
            }
        }

        if (($request->battering_uuid || !empty($data['battering_uuid'])) && !$request->predust_uuid) {
            $batteringUuid = $request->battering_uuid ?: ($data['battering_uuid'] ?? null);
            if ($batteringUuid) {
                $batteringData = ProsesBattering::where('uuid', $batteringUuid)->first();
                if ($batteringData) {
                    if (!empty($batteringData->predust_uuid)) {
                        $data['predust_uuid'] = $batteringData->predust_uuid;
                    }
                    // Flow langsung: battering bisa punya penggorengan_uuid
                    if (empty($request->penggorengan_uuid) && !empty($batteringData->penggorengan_uuid)) {
                        $data['penggorengan_uuid'] = $batteringData->penggorengan_uuid;
                    }
                }
            }
        }

        if (($request->predust_uuid || !empty($data['predust_uuid'])) && empty($request->penggorengan_uuid)) {
            $predustUuid = $request->predust_uuid ?: ($data['predust_uuid'] ?? null);
            if ($predustUuid) {
                $predustData = PembuatanPredust::where('uuid', $predustUuid)->first();
                if ($predustData && !empty($predustData->penggorengan_uuid)) {
                    $data['penggorengan_uuid'] = $predustData->penggorengan_uuid;
                }
            }
        }

        if ($request->penggorengan_uuid) {
            $data['penggorengan_uuid'] = $request->penggorengan_uuid;
        } elseif ($request->frayer_uuid) {
            $frayerData = ProsesFrayer::where('uuid', $request->frayer_uuid)->first();
            if ($frayerData && $frayerData->penggorengan_uuid) {
                $data['penggorengan_uuid'] = $frayerData->penggorengan_uuid;
            }
        }

        if ($request->predust_uuid) {
            $data['predust_uuid'] = $request->predust_uuid;
        }

        if ($request->battering_uuid) {
            $data['battering_uuid'] = $request->battering_uuid;
        }

        if ($request->breader_uuid) {
            $data['breader_uuid'] = $request->breader_uuid;
        }

        Frayer2::create($data);

        return redirect()->route('proses-frayer.index', ['tab' => 'frayer2'])->with('success', 'Data Frayer 2 berhasil ditambahkan.');
    }

    public function edit($uuid)
    {
        $frayer2 = Frayer2::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        if ($user->role == 'superadmin') {
            $products = JenisProduk::all();
        } else {
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        $suhuFrayers = SuhuFrayer1::where('id_produk', $frayer2->id_produk)->whereNotNull('suhu_frayer_2')->get();
        $waktuPenggorengans = WaktuPenggorengan2::where('id_suhu_frayer_2', $frayer2->id_suhu_frayer_2)->get();

        return view('qc-sistem.frayer_2.edit', compact('frayer2', 'products', 'suhuFrayers', 'waktuPenggorengans'));
    }

    public function update(Request $request, $uuid)
    {
        $frayer2 = Frayer2::where('uuid', $uuid)->firstOrFail();
        $request->validate([
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'id_produk' => 'required',
            'id_suhu_frayer_2' => 'required',
            'id_waktu_penggorengan_2' => 'required',
            'aktual_penggorengan' => 'required|string',
            'aktual_suhu_penggorengan' => 'nullable|string',
            'tpm_minyak' => 'required|string',
        ]);

        $data = $request->all();
        $data['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');

        $frayer2->update($data);

        return redirect()->route('proses-frayer.index', ['tab' => 'frayer2'])->with('success', 'Data Frayer 2 berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $frayer2 = Frayer2::where('uuid', $uuid)->firstOrFail();
        $frayer2->delete();
        return redirect()->route('proses-frayer.index', ['tab' => 'frayer2'])->with('success', 'Data Frayer 2 berhasil dihapus.');
    }

    // AJAX Methods
    public function getSuhuFrayer2ByProduk($id_produk)
    {
        $suhuFrayers = SuhuFrayer1::where('id_produk', $id_produk)->whereNotNull('suhu_frayer_2')->get();
        return response()->json($suhuFrayers);
    }

    public function getWaktuPenggorengan2BySuhu($id_suhu)
    {
        $waktuPenggorengans = WaktuPenggorengan2::where('id_suhu_frayer_2', $id_suhu)->get();
        return response()->json($waktuPenggorengans);
    }
}
