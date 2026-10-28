<?php

namespace App\Http\Controllers;

use App\Models\HasilPenggorengan;
use App\Models\HasilPenggorenganLog;
use App\Models\Plan;
use App\Models\User;
use App\Models\JenisProduk;
use App\Models\DataShift;
use App\Models\StdSuhuPusat;
use App\Models\PembekuanIqfPenggorengan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HasilPenggorenganController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = HasilPenggorengan::with(['plan', 'user', 'produk', 'stdSuhuPusat']);

        if ($user->role != 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if($search) {
            $query->whereHas('produk', function($q) use ($search) {
                $q->where('nama_produk', 'LIKE', '%' . $search . '%');
            });
        }

        $hasilPenggorengan = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('qc-sistem.hasil_penggorengan.index', compact('hasilPenggorengan'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Get related data from query parameters
        $frayerUuid = request('frayer_uuid');
        $frayer2Uuid = request('frayer2_uuid');
        $breaderUuid = request('breader_uuid');
        $batteringUuid = request('battering_uuid');
        $predustUuid = request('predust_uuid');
        $penggorenganUuid = request('penggorengan_uuid');
        
        // Load related models
        $frayerData = null;
        $frayer2Data = null;
        $breaderData = null;
        $batteringData = null;
        $predustData = null;
        $penggorenganData = null;
        
        // Handle frayer2_uuid parameter (khusus untuk tombol frayer-2)
        if ($frayer2Uuid) {
            $frayer2Data = \App\Models\Frayer2::with(['produk', 'penggorengan.shift', 'suhuFrayer2', 'waktuPenggorengan2'])->where('uuid', $frayer2Uuid)->first();
            
            // Jika ada frayer2, ambil juga data frayer yang terkait
            if ($frayer2Data && $frayer2Data->frayer_uuid) {
                $frayerData = \App\Models\ProsesFrayer::with(['produk', 'penggorengan.shift'])->where('uuid', $frayer2Data->frayer_uuid)->first();
            }
        }

        if ($frayerUuid && !$frayerData) {
            $frayerData = \App\Models\Frayer5::where('uuid', $frayerUuid)->first();
            if (!$frayerData) {
                $frayerData = \App\Models\Frayer4::where('uuid', $frayerUuid)->first();
                if (!$frayerData) {
                    $frayerData = \App\Models\Frayer3::where('uuid', $frayerUuid)->first();
                    if (!$frayerData) {
                        $frayerData = \App\Models\Frayer2::where('uuid', $frayerUuid)->first();
                        if (!$frayerData) {
                            $frayerData = \App\Models\ProsesFrayer::where('uuid', $frayerUuid)->first();
                        }
                    }
                }
            }
        }
        
        // Dual-check logic untuk data terkait dari frayer
        if ($frayerData) {
            // Cek apakah frayer memiliki relasi ke breader
            if ($frayerData->breader_uuid) {
                $breaderData = \App\Models\ProsesBreader::where('uuid', $frayerData->breader_uuid)->first();
                
                if ($breaderData && $breaderData->battering_uuid) {
                    $batteringData = \App\Models\ProsesBattering::with(['produk', 'penggorengan.shift'])->where('uuid', $breaderData->battering_uuid)->first();
                    
                    // Jika ada battering, cek predust dan penggorengan
                    if ($batteringData && $batteringData->predust_uuid) {
                        $predustData = \App\Models\PembuatanPredust::with(['produk', 'jenisPredust'])->where('uuid', $batteringData->predust_uuid)->first();
                        
                        // Jika ada predust, cek penggorengan
                        if ($predustData && $predustData->penggorengan_uuid) {
                            $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])->where('uuid', $predustData->penggorengan_uuid)->first();
                        }
                    } else {
                        // Flow langsung: ambil penggorengan langsung dari battering
                        if ($batteringData && $batteringData->penggorengan_uuid) {
                            $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])->where('uuid', $batteringData->penggorengan_uuid)->first();
                        }
                    }
                }
            }
            
            // Cek juga apakah frayer memiliki relasi langsung ke battering
            if ($frayerData->battering_uuid) {
                $batteringData = \App\Models\ProsesBattering::with(['produk', 'penggorengan.shift'])->where('uuid', $frayerData->battering_uuid)->first();
                
                // Dual-check logic untuk penggorengan dari battering
                if ($batteringData && $batteringData->predust_uuid) {
                    $predustData = \App\Models\PembuatanPredust::with(['produk', 'jenisPredust'])->where('uuid', $batteringData->predust_uuid)->first();
                    
                    if ($predustData && $predustData->penggorengan_uuid) {
                        $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])->where('uuid', $predustData->penggorengan_uuid)->first();
                    }
                } else {
                    if ($batteringData && $batteringData->penggorengan_uuid) {
                        $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])->where('uuid', $batteringData->penggorengan_uuid)->first();
                    }
                }
            }
        }
        
        if ($breaderUuid) {
            $breaderData = \App\Models\ProsesBreader::where('uuid', $breaderUuid)->first();
            
            // Dual-check logic untuk data terkait dari breader
            if ($breaderData) {
                // Cek apakah breader memiliki relasi ke battering
                if ($breaderData->battering_uuid) {
                    $batteringData = \App\Models\ProsesBattering::with(['produk', 'penggorengan.shift'])->where('uuid', $breaderData->battering_uuid)->first();
                    
                    // Jika ada battering, cek predust dan penggorengan
                    if ($batteringData && $batteringData->predust_uuid) {
                        $predustData = \App\Models\PembuatanPredust::with(['produk', 'jenisPredust'])->where('uuid', $batteringData->predust_uuid)->first();
                        
                        // Jika ada predust, cek penggorengan
                        if ($predustData && $predustData->penggorengan_uuid) {
                            $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])->where('uuid', $predustData->penggorengan_uuid)->first();
                        }
                    } else {
                        // Flow langsung: ambil penggorengan langsung dari battering
                        if ($batteringData && $batteringData->penggorengan_uuid) {
                            $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])->where('uuid', $batteringData->penggorengan_uuid)->first();
                        }
                    }
                }
            }
        }
        
        if ($batteringUuid) {
            $batteringData = \App\Models\ProsesBattering::where('uuid', $batteringUuid)->first();
            
            // Dual-check logic untuk penggorengan dari battering
            if ($batteringData && $batteringData->predust_uuid) {
                // Flow lengkap: ambil dari predust
                $predustData = \App\Models\PembuatanPredust::with(['produk', 'jenisPredust'])->where('uuid', $batteringData->predust_uuid)->first();
                
                // Jika ada predust, cek penggorengan
                if ($predustData && $predustData->penggorengan_uuid) {
                    $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])->where('uuid', $predustData->penggorengan_uuid)->first();
                }
            } else {
                // Flow langsung: ambil penggorengan langsung dari battering
                if ($batteringData && $batteringData->penggorengan_uuid) {
                    $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])->where('uuid', $batteringData->penggorengan_uuid)->first();
                }
            }
        }
        
        if ($predustUuid) {
            $predustData = \App\Models\PembuatanPredust::where('uuid', $predustUuid)->first();
            
            // Dual-check logic untuk penggorengan dari predust
            if ($predustData && $predustData->penggorengan_uuid) {
                $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])->where('uuid', $predustData->penggorengan_uuid)->first();
            }
        }
        
        if ($penggorenganUuid) {
            $penggorenganData = \App\Models\Penggorengan::where('uuid', $penggorenganUuid)->first();
        }
        
        // Get products and shifts based on user role
        if ($user->role == 'superadmin') {
            $products = JenisProduk::all();
        } else {
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        
        return view('qc-sistem.hasil_penggorengan.create', compact(
            'products',
            'frayerData',
            'frayer2Data',
            'breaderData',
            'batteringData',
            'predustData',
            'penggorenganData'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required|date_format:H:i',
            'id_produk' => 'required',
            'id_std_suhu_pusat' => 'required',
            'aktual_suhu_pusat' => 'required',
            'sensori_kematangan' => 'nullable|in:✔,✘',
            'sensori_kenampakan' => 'nullable|in:✔,✘',
            'sensori_warna' => 'nullable|in:✔,✘',
            'sensori_rasa' => 'nullable|in:✔,✘',
            'sensori_bau' => 'nullable|in:✔,✘',
            'sensori_tekstur' => 'nullable|in:✔,✘',
            'frayer2_uuid' => 'nullable|string|exists:frayer_2,uuid',
            'breader_uuid' => 'nullable|string|exists:proses_breader,uuid',
            'battering_uuid' => 'nullable|string|exists:proses_battering,uuid',
            'predust_uuid' => 'nullable|string|exists:pembuatan_predust,uuid',
            'penggorengan_uuid' => 'nullable|string|exists:penggorengan,uuid',
        ]);

        $aktualSuhuPusat = $request->aktual_suhu_pusat;
        if (is_array($aktualSuhuPusat)) {
            $filtered = array_filter(array_map('trim', $aktualSuhuPusat), fn($v) => $v !== '' && !is_null($v));
            $aktualSuhuPusat = !empty($filtered) ? implode(', ', $filtered) : null;
        }

        // Custom validation untuk frayer_uuid yang bisa ada di multiple tabel
        if ($request->frayer_uuid) {
            $frayerExists = false;
            $frayerTables = ['proses_frayer', 'frayer_2', 'frayer_3', 'frayer_4', 'frayer_5'];
            
            foreach ($frayerTables as $table) {
                if (\DB::table($table)->where('uuid', $request->frayer_uuid)->exists()) {
                    $frayerExists = true;
                    break;
                }
            }
            
            if (!$frayerExists) {
                return back()->withErrors(['frayer_uuid' => 'The selected frayer uuid is invalid.'])->withInput();
            }
        }

        $validated = $request->all();

        // Prepare the data for creation
        $data = [
            'id_plan' => Auth::user()->id_plan,
            'user_id' => Auth::id(),
            'jam' => Carbon::createFromFormat('H:i', $request->jam)->format('H:i'),
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $validated['tanggal'])->format('Y-m-d H:i:s'),
            'id_produk' => $validated['id_produk'],
            'id_std_suhu_pusat' => $validated['id_std_suhu_pusat'],
            'aktual_suhu_pusat' => $aktualSuhuPusat,
            'sensori_kematangan' => $validated['sensori_kematangan'] ?? null,
            'sensori_kenampakan' => $validated['sensori_kenampakan'] ?? null,
            'sensori_warna' => $validated['sensori_warna'] ?? null,
            'sensori_rasa' => $validated['sensori_rasa'] ?? null,
            'sensori_bau' => $validated['sensori_bau'] ?? null,
            'sensori_tekstur' => $validated['sensori_tekstur'] ?? null,
        ];

        // Add UUID relationships if they exist
        $uuidFields = [
            'frayer_uuid',
            'frayer2_uuid',
            'breader_uuid',
            'battering_uuid',
            'predust_uuid',
            'penggorengan_uuid'
        ];

        foreach ($uuidFields as $field) {
            if (!empty($validated[$field])) {
                $data[$field] = $validated[$field];
            }
        }

        // Jika datang dari Frayer2 dan relasi proses sebelumnya kosong (kasus skip Frayer1),
        // turunkan relasi dari tabel frayer_2 agar tidak null.
        if (!empty($validated['frayer2_uuid'])) {
            $frayer2Data = \App\Models\Frayer2::where('uuid', $validated['frayer2_uuid'])->first();
            if ($frayer2Data) {
                if (empty($data['penggorengan_uuid']) && !empty($frayer2Data->penggorengan_uuid)) {
                    $data['penggorengan_uuid'] = $frayer2Data->penggorengan_uuid;
                }
                if (empty($data['predust_uuid']) && !empty($frayer2Data->predust_uuid)) {
                    $data['predust_uuid'] = $frayer2Data->predust_uuid;
                }
                if (empty($data['battering_uuid']) && !empty($frayer2Data->battering_uuid)) {
                    $data['battering_uuid'] = $frayer2Data->battering_uuid;
                }
                if (empty($data['breader_uuid']) && !empty($frayer2Data->breader_uuid)) {
                    $data['breader_uuid'] = $frayer2Data->breader_uuid;
                }
            }
        }

        // Create the record
        HasilPenggorengan::create($data);

        return redirect()->route('hasil-penggorengan.index')
            ->with('success', 'Data Hasil Penggorengan berhasil ditambahkan.');
    }

    public function edit($uuid)
    {
        $hasilPenggorengan = HasilPenggorengan::with([
            'frayer', 'breader', 'battering', 'predust', 'penggorengan',
            'frayer.produk', 'breader.produk', 'battering.produk', 
            'predust.produk', 'penggorengan.produk'
        ])->where('uuid', $uuid)->firstOrFail();
        
        $user = Auth::user();
        
        // Get products and shifts based on user role
        if ($user->role == 'superadmin') {
            $products = JenisProduk::all();
        } else {
            $products = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        $stdSuhuPusats = StdSuhuPusat::where('id_produk', $hasilPenggorengan->id_produk)->get();

        return view('qc-sistem.hasil_penggorengan.edit', compact(
            'hasilPenggorengan', 
            'products', 
            'stdSuhuPusats'
        ));
    }

    public function update(Request $request, $uuid)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'id_produk' => 'required',
            'id_std_suhu_pusat' => 'required',
            'aktual_suhu_pusat' => 'required',
            'sensori_kematangan' => 'nullable|in:✔,✘',
            'sensori_kenampakan' => 'nullable|in:✔,✘',
            'sensori_warna' => 'nullable|in:✔,✘',
            'sensori_rasa' => 'nullable|in:✔,✘',
            'sensori_bau' => 'nullable|in:✔,✘',
            'sensori_tekstur' => 'nullable|in:✔,✘',
        ]);

        $aktualSuhuPusat = $request->aktual_suhu_pusat;
        if (is_array($aktualSuhuPusat)) {
            $filtered = array_filter(array_map('trim', $aktualSuhuPusat), fn($v) => $v !== '' && !is_null($v));
            $aktualSuhuPusat = !empty($filtered) ? implode(', ', $filtered) : null;
        }

        $hasilPenggorengan = HasilPenggorengan::where('uuid', $uuid)->firstOrFail();
        
        // Prepare the data for update
        $data = [
            'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $validated['tanggal'])->format('Y-m-d H:i:s'),
            'id_produk' => $validated['id_produk'],
            'id_std_suhu_pusat' => $validated['id_std_suhu_pusat'],
            'aktual_suhu_pusat' => $aktualSuhuPusat,
            'sensori_kematangan' => $validated['sensori_kematangan'] ?? null,
            'sensori_kenampakan' => $validated['sensori_kenampakan'] ?? null,
            'sensori_warna' => $validated['sensori_warna'] ?? null,
            'sensori_rasa' => $validated['sensori_rasa'] ?? null,
            'sensori_bau' => $validated['sensori_bau'] ?? null,
            'sensori_tekstur' => $validated['sensori_tekstur'] ?? null,
        ];

        // Handle UUID relationships
        $uuidFields = [
            'frayer_uuid',
            'breader_uuid',
            'battering_uuid',
            'predust_uuid',
            'penggorengan_uuid'
        ];

        foreach ($uuidFields as $field) {
            if ($request->has($field)) {
                $data[$field] = $request->input($field);
            }
        }

        $hasilPenggorengan->update($data);

        return redirect()->route('hasil-penggorengan.index')
            ->with('success', 'Data Hasil Penggorengan berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $hasilPenggorengan = HasilPenggorengan::where('uuid', $uuid)->firstOrFail();

        $isReferenced = PembekuanIqfPenggorengan::where('hasil_penggorengan_uuid', $uuid)->exists();
        if ($isReferenced) {
            return redirect()->route('hasil-penggorengan.index')
                ->with('error', 'Data tidak dapat dihapus karena sudah terhubung dengan proses selanjutnya.');
        }

        $hasilPenggorengan->delete();
        return redirect()->route('hasil-penggorengan.index')->with('success', 'Data Hasil Penggorengan berhasil dihapus.');
    }

    // AJAX Methods
    public function getStdSuhuPusatByProduk($id_produk)
    {
        $stdSuhuPusats = StdSuhuPusat::where('id_produk', $id_produk)->get();
        return response()->json($stdSuhuPusats);
    }

    // Logs Methods
    public function showLogs($uuid)
    {
        $user = Auth::user();
        $hasilPenggorengan = HasilPenggorengan::with(['plan', 'user', 'produk', 'stdSuhuPusat'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Check authorization
        if ($user->role != 'superadmin' && $hasilPenggorengan->id_plan != $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        return view('qc-sistem.hasil_penggorengan.logs', compact('hasilPenggorengan'));
    }

    public function getLogsJson($uuid)
    {
        $user = Auth::user();
        $hasilPenggorengan = HasilPenggorengan::where('uuid', $uuid)->firstOrFail();

        // Check authorization
        if ($user->role != 'superadmin' && $hasilPenggorengan->id_plan != $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = HasilPenggorenganLog::with('user')
            ->where('hasil_penggorengan_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'data' => $logs->items(),
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ]
        ]);
    }
}
