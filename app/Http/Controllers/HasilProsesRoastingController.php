<?php

namespace App\Http\Controllers;

use App\Models\HasilProsesRoasting;
use App\Models\HasilProsesRoastingLog;
use App\Models\Plan;
use App\Models\JenisProduk;
use App\Models\DataShift;
use App\Models\StdSuhuPusatRoasting;
use App\Models\PembekuanIqfRoasting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HasilProsesRoastingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        
        $query = HasilProsesRoasting::with(['plan', 'user', 'produk', 'shift', 'stdSuhuPusat']);

        if ($user->role != 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                // Search by product name
                $q->whereHas('produk', function($qp) use ($search) {
                    $qp->where('nama_produk', 'LIKE', '%' . $search . '%');
                })
                // Search by date
                ->orWhere('tanggal', 'LIKE', '%' . $search . '%');
            });
        }

        $hasilProsesRoasting = $query->orderBy('created_at', 'desc')->paginate(10);

        // Count related pembekuan IQF roasting records for each hasil proses roasting
        foreach ($hasilProsesRoasting as $item) {
            $item->pembekuanIqfRoastingCount = PembekuanIqfRoasting::where('hasil_proses_roasting_uuid', $item->uuid)->count();
        }

        return view('qc-sistem.hasil_proses_roasting.index', compact('hasilProsesRoasting', 'search'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Get related data from query parameters
        $prosesRoastingFanUuid = request('proses_roasting_fan_uuid');
        $inputRoastingUuid = request('input_roasting_uuid');
        $frayerUuid = request('frayer_uuid');
        $breaderUuid = request('breader_uuid');
        $batteringUuid = request('battering_uuid');
        $predustUuid = request('predust_uuid');
        $penggorenganUuid = request('penggorengan_uuid');
        
        // Load related models
        $prosesRoastingFanData = null;
        $inputRoastingData = null;
        $frayerData = null;
        $breaderData = null;
        $batteringData = null;
        $predustData = null;
        $penggorenganData = null;
        
        // Load input roasting data
        if ($inputRoastingUuid) {
            $inputRoastingData = \App\Models\InputRoasting::with(['produk', 'shift'])->where('uuid', $inputRoastingUuid)->first();
        }
        
        // Start with proses roasting fan data
        if ($prosesRoastingFanUuid) {
            $prosesRoastingFanData = \App\Models\ProsesRoastingFan::with(['produk'])->where('uuid', $prosesRoastingFanUuid)->first();
            
            // If proses roasting fan exists, get the complete chain
            if ($prosesRoastingFanData) {
                // Get frayer data
                if ($prosesRoastingFanData->frayer_uuid) {
                    $frayerData = \App\Models\ProsesFrayer::with(['produk'])->where('uuid', $prosesRoastingFanData->frayer_uuid)->first();
                }
                
                // Get breader data
                if ($prosesRoastingFanData->breader_uuid) {
                    $breaderData = \App\Models\ProsesBreader::with(['produk'])->where('uuid', $prosesRoastingFanData->breader_uuid)->first();
                }
                
                // Get battering data
                if ($prosesRoastingFanData->battering_uuid) {
                    $batteringData = \App\Models\ProsesBattering::with(['produk'])->where('uuid', $prosesRoastingFanData->battering_uuid)->first();
                }
                
                // Get predust data
                if ($prosesRoastingFanData->predust_uuid) {
                    $predustData = \App\Models\PembuatanPredust::with(['produk', 'jenisPredust'])->where('uuid', $prosesRoastingFanData->predust_uuid)->first();
                }
                
                // Get penggorengan data
                if ($prosesRoastingFanData->penggorengan_uuid) {
                    $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])->where('uuid', $prosesRoastingFanData->penggorengan_uuid)->first();
                }
            }
        }
        
        // Fallback: if no proses roasting fan data, try to load from individual UUIDs
        if (!$prosesRoastingFanData) {
            if ($frayerUuid) {
                $frayerData = \App\Models\ProsesFrayer::with(['produk'])->where('uuid', $frayerUuid)->first();
            }
            
            if ($breaderUuid) {
                $breaderData = \App\Models\ProsesBreader::with(['produk'])->where('uuid', $breaderUuid)->first();
            }
            
            if ($batteringUuid) {
                $batteringData = \App\Models\ProsesBattering::with(['produk'])->where('uuid', $batteringUuid)->first();
            }
            
            if ($predustUuid) {
                $predustData = \App\Models\PembuatanPredust::with(['produk', 'jenisPredust'])->where('uuid', $predustUuid)->first();
            }
            
            if ($penggorenganUuid) {
                $penggorenganData = \App\Models\Penggorengan::with(['produk', 'shift'])->where('uuid', $penggorenganUuid)->first();
            }
        }
        
        // Get dropdown data based on user role
        if ($user->role == 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
            // $shifts = DataShift::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            // $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        }
        
        // Get std suhu pusat data
        $stdSuhuPusats = StdSuhuPusatRoasting::all();

        // If we have related data, filter std suhu pusat by product and plan
        if ($prosesRoastingFanData && $prosesRoastingFanData->id_produk) {
            $stdSuhuPusats = StdSuhuPusatRoasting::where('id_produk', $prosesRoastingFanData->id_produk)
                ->where('id_plan', $prosesRoastingFanData->id_plan)
                ->get();
        } elseif ($penggorenganData && $penggorenganData->id_produk) {
            $stdSuhuPusats = StdSuhuPusatRoasting::where('id_produk', $penggorenganData->id_produk)
                ->where('id_plan', $penggorenganData->id_plan)
                ->get();
        }
        
        return view('qc-sistem.hasil_proses_roasting.create', compact(
            'plans', 
            'produks', 
            'stdSuhuPusats',
            'prosesRoastingFanUuid',
            'inputRoastingUuid',
            'frayerUuid',
            'breaderUuid',
            'batteringUuid',
            'predustUuid',
            'penggorenganUuid',
            'prosesRoastingFanData',
            'inputRoastingData',
            'frayerData',
            'breaderData',
            'batteringData',
            'predustData',
            'penggorenganData'
        ));
    }

    public function store(Request $request)
    {
         // Auto-detect shift berdasarkan alur proses
         $shift_id = null;

         if ($request->penggorengan_uuid) {
             // Kondisi 1: Alur Penggorengan - ambil shift dari penggorengan
             $penggorengan = \App\Models\Penggorengan::where('uuid', $request->penggorengan_uuid)->first();
             if ($penggorengan) {
                 $shift_id = $penggorengan->shift_id;
             }
         } elseif ($request->proses_roasting_fan_uuid) {
             // Kondisi 2: Alur Roasting - ambil shift dari proses_roasting_fan
             $prosesRoastingFan = \App\Models\ProsesRoastingFan::where('uuid', $request->proses_roasting_fan_uuid)->first();
             if ($prosesRoastingFan) {
                 $shift_id = $prosesRoastingFan->id_shift;
             }
         }
 
         if (!$shift_id) {
             return back()->withErrors(['shift' => 'Tidak dapat menentukan shift dari proses sebelumnya'])->withInput();
         }
        $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_std_suhu_pusat' => 'required|exists:std_suhu_pusat_roasting,id',
            'aktual_suhu_pusat' => 'required|array',
            'aktual_suhu_pusat.*' => 'required|string',
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required|date_format:H:i',
        ]);
       
        $data = $request->except(['aktual_suhu_pusat', 'sensori']);
        $data['user_id'] = Auth::id();
        $data['id_plan'] = Auth::user()->id_plan;
        $data['id_shift'] = $shift_id;
        $data['tanggal'] = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s');
        $data['jam'] = $request->jam;
        
        // Process aktual_suhu_pusat - convert array to JSON
        $suhuArray = $request->input('aktual_suhu_pusat', []);
        $data['aktual_suhu_pusat'] = json_encode($suhuArray);
        
        // Process sensori data - convert radio button array to JSON
        $sensoriData = [];
        
        // Get all sensori inputs dynamically
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'sensori_') === 0 && !strpos($key, 'sensori_param_')) {
                $sensoriData[$key] = $value;
            }
        }
        $data['sensori'] = $sensoriData;
        
        // Add UUID fields from request
        $data['proses_roasting_fan_uuid'] = $request->proses_roasting_fan_uuid;
        $data['input_roasting_uuid'] = $request->input_roasting_uuid;
        $data['frayer_uuid'] = $request->frayer_uuid;
        $data['breader_uuid'] = $request->breader_uuid;
        $data['battering_uuid'] = $request->battering_uuid;
        $data['predust_uuid'] = $request->predust_uuid;
        $data['penggorengan_uuid'] = $request->penggorengan_uuid;

        HasilProsesRoasting::create($data);

        return redirect()->route('hasil-proses-roasting.index')->with('success', 'Data hasil proses roasting berhasil ditambahkan!');
    }

    public function show($uuid)
    {
        $hasilProsesRoasting = HasilProsesRoasting::where('uuid', $uuid)->firstOrFail();
        $hasilProsesRoasting->load(['plan', 'user', 'produk', 'shift', 'stdSuhuPusat', 'inputRoasting', 'prosesRoastingFan']);
        
        return view('qc-sistem.hasil_proses_roasting.show', compact('hasilProsesRoasting'));
    }

    public function edit($uuid)
    {
        $hasilProsesRoasting = HasilProsesRoasting::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        if ($user->role == 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }
        
        // Get stdSuhuPusats - get all available
        $stdSuhuPusats = StdSuhuPusatRoasting::all();
        
        return view('qc-sistem.hasil_proses_roasting.edit', compact('hasilProsesRoasting', 'plans', 'produks', 'stdSuhuPusats'));
    }

    public function update(Request $request, $uuid)
    {
        try {
            // TAMBAHKAN: Auto-detect shift berdasarkan alur proses
            $shift_id = null;

            if ($request->penggorengan_uuid) {
                // Kondisi 1: Alur Penggorengan - ambil shift dari penggorengan
                $penggorengan = \App\Models\Penggorengan::where('uuid', $request->penggorengan_uuid)->first();
                if ($penggorengan) {
                    $shift_id = $penggorengan->shift_id;
                }
            } elseif ($request->proses_roasting_fan_uuid) {
                // Kondisi 2: Alur Roasting - ambil shift dari proses_roasting_fan
                $prosesRoastingFan = \App\Models\ProsesRoastingFan::where('uuid', $request->proses_roasting_fan_uuid)->first();
                if ($prosesRoastingFan) {
                    $shift_id = $prosesRoastingFan->id_shift;
                }
            }

            if (!$shift_id) {
                return back()->withErrors(['shift' => 'Tidak dapat menentukan shift dari proses sebelumnya'])->withInput();
            }

            // UBAH: Validasi untuk array aktual_suhu_pusat
            $request->validate([
                'id_produk' => 'required|exists:jenis_produk,id',
                'id_std_suhu_pusat' => 'required|exists:std_suhu_pusat_roasting,id',
                'aktual_suhu_pusat' => 'required|array',
                'aktual_suhu_pusat.*' => 'required|string',
                'tanggal' => 'required|date_format:d-m-Y H:i:s',
                'jam' => 'nullable',
            ]);

            $hasilProsesRoasting = HasilProsesRoasting::where('uuid', $uuid)->firstOrFail();
            
            // Get existing sensori data
            $existingSensoriData = is_array($hasilProsesRoasting->sensori) ? $hasilProsesRoasting->sensori : [];
            
            // Collect all sensori fields from request
            $newSensoriData = [];
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'sensori_') === 0) {
                    $newSensoriData[$key] = $value;
                }
            }
            
            // Merge: keep existing sensori data and update with new data from request
            $sensoriData = array_merge($existingSensoriData, $newSensoriData);
            
            // Build data array - only include fillable fields
            $data = [
                'id_produk' => $request->id_produk,
                'id_std_suhu_pusat' => $request->id_std_suhu_pusat,
                'id_shift' => $shift_id,
                'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
                'jam' => $request->jam,
            ];
            
            // Process aktual_suhu_pusat - convert array to JSON
            $suhuArray = $request->input('aktual_suhu_pusat', []);
            $data['aktual_suhu_pusat'] = json_encode($suhuArray);
            
            // Store sensori data as JSON object (merged with existing data)
            // Laravel will auto-convert array to JSON because of 'array' cast
            $data['sensori'] = $sensoriData;
            
            // Debug: Log the sensori data type and content
            \Log::info('Sensori data type: ' . gettype($sensoriData));
            \Log::info('Sensori data content:', $sensoriData);
            
            // Add UUID fields if they exist
            if ($request->proses_roasting_fan_uuid) {
                $data['proses_roasting_fan_uuid'] = $request->proses_roasting_fan_uuid;
            }
            if ($request->input_roasting_uuid) {
                $data['input_roasting_uuid'] = $request->input_roasting_uuid;
            }
            if ($request->frayer_uuid) {
                $data['frayer_uuid'] = $request->frayer_uuid;
            }
            if ($request->breader_uuid) {
                $data['breader_uuid'] = $request->breader_uuid;
            }
            if ($request->battering_uuid) {
                $data['battering_uuid'] = $request->battering_uuid;
            }
            if ($request->predust_uuid) {
                $data['predust_uuid'] = $request->predust_uuid;
            }
            if ($request->penggorengan_uuid) {
                $data['penggorengan_uuid'] = $request->penggorengan_uuid;
            }
            
            $result = $hasilProsesRoasting->update($data);
            \Log::info('Update result: ' . ($result ? 'Success' : 'Failed'));
            
            $updatedRecord = $hasilProsesRoasting->fresh();
            \Log::info('Updated record sensori:', ['sensori' => $updatedRecord->sensori]);
            \Log::info('Updated record aktual_suhu_pusat:', ['aktual_suhu_pusat' => $updatedRecord->aktual_suhu_pusat]);

            if ($result) {
                return redirect()->route('hasil-proses-roasting.index')->with('success', 'Data hasil proses roasting berhasil diperbarui!');
            } else {
                return back()->withErrors(['error' => 'Gagal menyimpan data'])->withInput();
            }
        } catch (\Exception $e) {
            \Log::error('Update error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($uuid)
    {
        $hasilProsesRoasting = HasilProsesRoasting::where('uuid', $uuid)->firstOrFail();
        $hasilProsesRoasting->delete();

        return redirect()->route('hasil-proses-roasting.index')->with('success', 'Data hasil proses roasting berhasil dihapus!');
    }

   // AJAX method for cascading dropdown - UPDATED
   public function getStdSuhuPusatByProduk($id_produk, $id_plan = null)
   {
       $query = StdSuhuPusatRoasting::where('id_produk', $id_produk);
       
       if ($id_plan) {
           $query->where('id_plan', $id_plan);
       }
       
       $stdSuhuPusats = $query->get();
       return response()->json($stdSuhuPusats);
   }

    /**
     * Show logs for a specific hasil proses roasting record
     */
    public function showLogs($uuid)
    {
        // Authorization: superadmin or can('view-logs')
        if (!auth()->check()) {
            abort(403, 'Unauthorized access to logs');
        }

        $hasilProsesRoasting = HasilProsesRoasting::where('uuid', $uuid)->firstOrFail();
        $hasilProsesRoasting->load(['plan', 'user', 'produk', 'shift', 'stdSuhuPusat']);

        $logs = HasilProsesRoastingLog::where('hasil_proses_roasting_uuid', $uuid)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.hasil_proses_roasting.logs', compact('hasilProsesRoasting', 'logs'));
    }

    /**
     * Get logs data in JSON format
     */
    public function getLogsJson($uuid)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = HasilProsesRoastingLog::where('hasil_proses_roasting_uuid', $uuid)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($logs);
    }
}
