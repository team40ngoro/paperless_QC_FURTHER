<?php

namespace App\Http\Controllers;

use App\Models\ProsesRoastingFan;
use App\Models\ProsesRoastingFanLog;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\SuhuBlok;
use App\Models\StdFan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ProsesRoastingFanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query1 = ProsesRoastingFan::with(['plan', 'user', 'shift', 'produk']);

        if ($user->role !== 'superadmin') {
            $query1->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if (!empty($search)) {
            $query1->whereHas('produk', function ($produkQuery) use ($search) {
                $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
            });
        }

        $perPage = request()->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50, 100]) ? $perPage : 10;
        $page = (int) request()->get('page', 1);

        // Prioritize grouped records (new format)
        $rawData = $query1->orderBy('is_grouped', 'desc')->latest()->get();
        
        // Group data by session (tanggal, shift, produk, aktual_lama_proses)
        $groupedData = $rawData->groupBy(function($item) {
            return $item->tanggal->format('Y-m-d H:i:s') . '_' . $item->id_shift . '_' . $item->id_produk . '_' . ($item->aktual_lama_proses ?? 'null');
        });
        
        $data = $groupedData->map(function($group) {
            $firstItem = $group->first();
            
            // Handle new grouped format vs old individual records
            if ($firstItem->is_grouped && $firstItem->blok_data) {
                // New format: data stored as JSON
                $blockNumbers = collect($firstItem->blok_data)->pluck('block_number')->toArray();
                $blocks = [];
                
                foreach ($firstItem->blok_data as $blokItem) {
                    $suhuBlok = \App\Models\SuhuBlok::find($blokItem['id_suhu_blok']);
                    $blocks[] = $suhuBlok ? $suhuBlok->suhu_blok : '-';
                }
                
                return (object)[
                    'id' => $firstItem->id,
                    'uuid' => $firstItem->uuid,
                    'tanggal' => $firstItem->tanggal,
                    'jam' => $firstItem->jam,
                    'shift' => $firstItem->shift,
                    'shift_data' => $firstItem->shift_data, // TAMBAHKAN INI
                    'produk' => $firstItem->produk,
                    'aktual_lama_proses' => $firstItem->aktual_lama_proses,
                    'filled_blocks' => $blocks,
                    'block_numbers' => $blockNumbers,
                    'block_count' => count($blocks),
                    'records' => $group,
                    'is_grouped' => true,
                    'blok_data' => $firstItem->blok_data
                    
                ];
            } else {
                // Old format: individual records (backward compatibility)
                $blockNumbers = [];
                $blocks = [];
                
                foreach($group as $record) {
                    $blockNumbers[] = $record->block_number ?? 1;
                    $blocks[] = $record->suhuBlok ? $record->suhuBlok->suhu_blok : '-';
                }
                
                // Sort arrays by block number
                array_multisort($blockNumbers, $blocks);
                
                return (object)[
                    'id' => $firstItem->id,
                    'uuid' => $firstItem->uuid,
                    'tanggal' => $firstItem->tanggal,
                    'jam' => $firstItem->jam,
                    'shift' => $firstItem->shift,
                    'shift_data' => $firstItem->shift_data, // TAMBAHKAN INI
                    'produk' => $firstItem->produk,
                    'aktual_lama_proses' => $firstItem->aktual_lama_proses,
                    'filled_blocks' => $blocks,
                    'block_numbers' => $blockNumbers,
                    'block_count' => count($blocks),
                    'records' => $group,
                    'is_grouped' => false
                ];
            }
        })->values();

        $total = $data->count();
        $pagedData = $data->slice(($page - 1) * $perPage, $perPage)->values();

        $data = new LengthAwarePaginator(
            $pagedData,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
        
        return view('qc-sistem.proses_roasting_fan.index', compact('data', 'search', 'perPage'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') {
            // $shifts = DataShift::all();
            $produks = JenisProduk::all();
        } else {
            // $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        // Get data from previous processes
        $inputRoastingData = null;
        $inputRoastingUuid = null;
        
        // Check if coming from Input Roasting
        if ($request->has('input_roasting_uuid')) {
            $inputRoastingUuid = $request->input_roasting_uuid;
            $inputRoastingData = \App\Models\InputRoasting::with(['produk', 'shift', 'user'])
                ->where('uuid', $inputRoastingUuid)
                ->first();
        }

        // Ambil data dari proses sebelumnya berdasarkan UUID yang dikirim (backward compatibility)
        $frayerData = null;
        $breaderData = null;
        $batteringData = null;
        $predustData = null;
        $penggorenganData = null;

        if (request('frayer_uuid')) {
            $frayerData = \App\Models\ProsesFrayer::where('uuid', request('frayer_uuid'))->first();
        }
        if (request('breader_uuid')) {
            $breaderData = \App\Models\ProsesBreader::where('uuid', request('breader_uuid'))->first();
        }
        if (request('battering_uuid')) {
            $batteringData = \App\Models\ProsesBattering::where('uuid', request('battering_uuid'))->first();
        }
        if (request('predust_uuid')) {
            $predustData = \App\Models\PembuatanPredust::where('uuid', request('predust_uuid'))->first();
        }
        if (request('penggorengan_uuid')) {
            $penggorenganData = \App\Models\Penggorengan::where('uuid', request('penggorengan_uuid'))->first();
        }

        return view('qc-sistem.proses_roasting_fan.create', compact(
            'produks', 
            'frayerData', 
            'breaderData', 
            'batteringData', 
            'predustData', 
            'penggorenganData',
            'inputRoastingData',
            'inputRoastingUuid'
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
        } elseif ($request->input_roasting_uuid) {
            // Kondisi 2: Alur Roasting - ambil shift dari input_roasting
            $inputRoasting = \App\Models\InputRoasting::where('uuid', $request->input_roasting_uuid)->first();
            if ($inputRoasting) {
                $shift_id = $inputRoasting->shift_id;
            }
        }

        if (!$shift_id) {
            return back()->withErrors(['shift' => 'Tidak dapat menentukan shift dari proses sebelumnya'])->withInput();
        }
        $validated = $request->validate([
            'tanggal' => 'required|date_format:d-m-Y H:i:s',
            'jam' => 'required|date_format:H:i',
            'waktu_pemasakan' => 'nullable|string|max:255',
            'id_produk' => 'required|exists:jenis_produk,id',
            'id_suhu_blok' => 'required|array',
            'id_suhu_blok.*' => 'required|exists:suhu_blok,id',
            'id_std_fan' => 'required|array',
            'id_std_fan.*' => 'required|exists:std_fan,id',
            'suhu_roasting' => 'required|array',
            'suhu_roasting.*' => 'nullable|numeric',
            'fan_1' => 'required|array',
            'fan_1.*' => 'nullable|numeric',
            'fan_2' => 'required|array',
            'fan_2.*' => 'nullable|numeric',
            'fan_3' => 'nullable|array',
            'fan_3.*' => 'nullable|numeric',
            'fan_4' => 'nullable|array',
            'fan_4.*' => 'nullable|numeric',
            'aktual_humadity' => 'nullable|array',
            'aktual_humadity.*' => 'nullable|numeric',
            'infra_red' => 'nullable|array',
            'infra_red.*' => 'nullable|string|max:255',
            'conveyor_bandung' => 'nullable|array',
            'conveyor_bandung.*' => 'nullable|string|max:255',
            'conveyor_infeed' => 'nullable|array',
            'conveyor_infeed.*' => 'nullable|string|max:255',
            'conveyor_outfeed' => 'nullable|array',
            'conveyor_outfeed.*' => 'nullable|string|max:255',
            'conveyor_blok1' => 'nullable|array',
            'conveyor_blok1.*' => 'nullable|string|max:255',
            'aktual_lama_proses' => 'nullable|string|max:255',
            'block_number' => 'required|array',
            'block_number.*' => 'required|string|max:255',
            // UUID fields untuk relasi ke proses sebelumnya
            'input_roasting_uuid' => 'nullable|string',
            'frayer_uuid' => 'nullable|string',
            'breader_uuid' => 'nullable|string',
            'battering_uuid' => 'nullable|string',
            'predust_uuid' => 'nullable|string',
            'penggorengan_uuid' => 'nullable|string',
        ]);

        $user = Auth::user();
        $tanggal = Carbon::createFromFormat('d-m-Y H:i:s', $validated['tanggal'])->format('Y-m-d H:i:s');
        $jam = Carbon::createFromFormat('H:i', $validated['jam'])->format('H:i');
        
        DB::beginTransaction();
        try {
            // Handle merged Infeed & Outfeed inputs for Aktual Humidity and Infra Red if provided
            $blockNumbers = $validated['block_number'] ?? [];
            $infeedIndex = array_search('INFEED', $blockNumbers);
            $outfeedIndex = array_search('OUTFEED', $blockNumbers);

            if ($request->filled('aktual_humadity_infeed_outfeed')) {
                $val = $request->input('aktual_humadity_infeed_outfeed');
                if ($infeedIndex !== false) $validated['aktual_humadity'][$infeedIndex] = $val;
                if ($outfeedIndex !== false) $validated['aktual_humadity'][$outfeedIndex] = $val;
            }
            if ($request->filled('infra_red_infeed_outfeed')) {
                $val = $request->input('infra_red_infeed_outfeed');
                if ($infeedIndex !== false) $validated['infra_red'][$infeedIndex] = $val;
                if ($outfeedIndex !== false) $validated['infra_red'][$outfeedIndex] = $val;
            }

            // Prepare block data array
            $blokData = [];
            
            foreach ($validated['id_suhu_blok'] as $key => $idSuhuBlok) {
                // Only save if the block has actual data filled
                $suhuRoasting = $validated['suhu_roasting'][$key] ?? null;
                $fan1 = $validated['fan_1'][$key] ?? null;
                $fan2 = $validated['fan_2'][$key] ?? null;
                $fan3 = $validated['fan_3'][$key] ?? null;
                $fan4 = $validated['fan_4'][$key] ?? null;
                $aktualHumadity = $validated['aktual_humadity'][$key] ?? null;
                $infraRed = $validated['infra_red'][$key] ?? null;
                
                // Skip this block if no actual data is provided
                if (empty($suhuRoasting) && empty($fan1) && empty($fan2) && empty($fan3) && empty($fan4) && empty($aktualHumadity) && empty($infraRed)) {
                    continue;
                }
                
                $blokData[] = [
                    'block_number' => $validated['block_number'][$key],
                    'id_suhu_blok' => $idSuhuBlok,
                    'id_std_fan' => $validated['id_std_fan'][$key],
                    'suhu_roasting' => $suhuRoasting,
                    'fan_1' => $fan1,
                    'fan_2' => $fan2,
                    'fan_3' => $fan3,
                    'fan_4' => $fan4,
                    'aktual_humadity' => $aktualHumadity,
                    'infra_red' => $infraRed,
                ];
            }
            
            // Create single record with JSON data
            if (!empty($blokData)) {
                $savedRecord = ProsesRoastingFan::create([
                    'id_plan' => $user->id_plan,
                    'user_id' => $user->id,
                    'id_shift' => $shift_id,
                    'tanggal' => $tanggal,
                    'jam' => $jam,
                    'waktu_pemasakan' => $validated['waktu_pemasakan'] ?? null,
                    'id_produk' => $validated['id_produk'],
                    'blok_data' => $blokData,
                    'is_grouped' => true,
                    'aktual_lama_proses' => $validated['aktual_lama_proses'],
                    // UUID fields untuk relasi ke proses sebelumnya
                    'input_roasting_uuid' => $validated['input_roasting_uuid'] ?? null,
                    'frayer_uuid' => $validated['frayer_uuid'] ?? null,
                    'breader_uuid' => $validated['breader_uuid'] ?? null,
                    'battering_uuid' => $validated['battering_uuid'] ?? null,
                    'predust_uuid' => $validated['predust_uuid'] ?? null,
                    'penggorengan_uuid' => $validated['penggorengan_uuid'] ?? null,
                ]);
            }
            
            // Block numbers are now set directly from form, no need for additional processing
            
            DB::commit();
            return redirect()->route('proses-roasting-fan.index')
                ->with('success', 'Data berhasil disimpan');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($uuid)
    {
        $firstRecord = ProsesRoastingFan::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $firstRecord->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        $sessionRecords = collect();

        // Check if this is a grouped record (new format)
        if ($firstRecord->is_grouped && $firstRecord->blok_data) {
            // Handle new JSON format
            foreach ($firstRecord->blok_data as $index => $blokItem) {
                $suhuBlok = \App\Models\SuhuBlok::find($blokItem['id_suhu_blok']);
                $stdFan = \App\Models\StdFan::find($blokItem['id_std_fan']);
                
                // Create a virtual record object for the view
                $virtualRecord = (object) [
                    'id' => $firstRecord->id,
                    'uuid' => $firstRecord->uuid,
                    'block_number' => $blokItem['block_number'],
                    'id_suhu_blok' => $blokItem['id_suhu_blok'],
                    'id_std_fan' => $blokItem['id_std_fan'],
                    'suhu_roasting' => $blokItem['suhu_roasting'] ?? null,
                    'fan_1' => $blokItem['fan_1'] ?? null,
                    'fan_2' => $blokItem['fan_2'] ?? null,
                    'fan_3' => $blokItem['fan_3'] ?? null,
                    'fan_4' => $blokItem['fan_4'] ?? null,
                    'aktual_humadity' => $blokItem['aktual_humadity'] ?? null,
                    'infra_red' => $blokItem['infra_red'] ?? null,
                    'suhuBlok' => $suhuBlok,
                    'stdFan' => $stdFan,
                ];
                
                $sessionRecords->push($virtualRecord);
            }
            
            $sessionRecords = $sessionRecords->sortBy('block_number');
        } else {
            // Handle old format - get all records from the same session
            $sessionRecords = ProsesRoastingFan::where('tanggal', $firstRecord->tanggal)
                ->where('id_shift', $firstRecord->id_shift)
                ->where('id_produk', $firstRecord->id_produk)
                ->where('aktual_lama_proses', $firstRecord->aktual_lama_proses)
                ->with(['suhuBlok', 'stdFan'])
                ->get();

            // Get all suhu blok for this product ordered by ID to maintain original block order
            $allSuhuBlok = SuhuBlok::where('id_produk', $firstRecord->id_produk)
                ->orderBy('id')
                ->get();
            
            // Create mapping based on original order: first record = block 1, second = block 2, etc.
            $idToBlockNumber = [];
            $blockNumber = 1;
            foreach($allSuhuBlok as $suhuBlokRecord) {
                $idToBlockNumber[$suhuBlokRecord->id] = $blockNumber++;
            }
            
            // Use stored block_number from database, fallback to calculated if not available
            $sessionRecords = $sessionRecords->map(function($record) use ($idToBlockNumber) {
                // Use stored block_number if available, otherwise calculate
                if ($record->block_number) {
                    $record->block_number = $record->block_number;
                } else {
                    $record->block_number = $idToBlockNumber[$record->id_suhu_blok] ?? 0;
                }
                return $record;
            })->sortBy('block_number');
        }

        if ($user->role === 'superadmin') {
            // $shifts = DataShift::all();
            $produks = JenisProduk::all();
        } else {
            // $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        // Get all suhu blok for the product to show available blocks
        $allSuhuBlok = SuhuBlok::where('id_produk', $firstRecord->id_produk)
            ->orderBy('suhu_blok')
            ->get();

        return view('qc-sistem.proses_roasting_fan.edit', compact('firstRecord', 'sessionRecords', 'produks', 'allSuhuBlok'));
    }

    public function update(Request $request, $uuid)
    {
        $firstRecord = ProsesRoastingFan::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $firstRecord->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate data ini.');
        }

        $validated = $request->validate([
            'aktual_lama_proses' => 'nullable|string|max:255',
            'records' => 'required|array',
            'records.*.suhu_roasting' => 'nullable|numeric',
            'records.*.fan_1' => 'nullable|numeric',
            'records.*.fan_2' => 'nullable|numeric',
            'records.*.fan_3' => 'nullable|numeric',
            'records.*.fan_4' => 'nullable|numeric',
            'records.*.aktual_humadity' => 'nullable|numeric',
            'records.*.infra_red' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Check if this is a grouped record (new format)
            if ($firstRecord->is_grouped && $firstRecord->blok_data) {
                // Handle new JSON format - update blok_data array
                $updatedBlokData = $firstRecord->blok_data;
                
                // Parse the form data which now has keys like "recordId_blockNumber"
                foreach ($validated['records'] as $recordKey => $recordData) {
                    // Extract record ID and block number from the key
                    if (strpos($recordKey, '_') !== false) {
                        list($recordId, $blockNumber) = explode('_', $recordKey);
                        
                        // Find the corresponding block in blok_data array
                        foreach ($updatedBlokData as $index => $blokItem) {
                            if ($blokItem['block_number'] == $blockNumber) {
                                $updatedBlokData[$index]['suhu_roasting'] = $recordData['suhu_roasting'];
                                $updatedBlokData[$index]['fan_1'] = $recordData['fan_1'];
                                $updatedBlokData[$index]['fan_2'] = $recordData['fan_2'];
                                $updatedBlokData[$index]['fan_3'] = $recordData['fan_3'] ?? null;
                                $updatedBlokData[$index]['fan_4'] = $recordData['fan_4'] ?? null;
                                $updatedBlokData[$index]['aktual_humadity'] = $recordData['aktual_humadity'] ?? null;
                                $updatedBlokData[$index]['infra_red'] = $recordData['infra_red'] ?? null;
                                break;
                            }
                        }
                    }
                }
                
                // Update the main record with new blok_data and aktual_lama_proses
                $firstRecord->update([
                    'blok_data' => $updatedBlokData,
                    'aktual_lama_proses' => $validated['aktual_lama_proses'],
                ]);
                
            } else {
                // Handle old format - update each record individually
                foreach ($validated['records'] as $recordId => $recordData) {
                    $record = ProsesRoastingFan::find($recordId);
                    if ($record && $record->tanggal == $firstRecord->tanggal && 
                        $record->id_shift == $firstRecord->id_shift && 
                        $record->id_produk == $firstRecord->id_produk) {
                        
                        $record->update([
                            'suhu_roasting' => $recordData['suhu_roasting'],
                            'fan_1' => $recordData['fan_1'],
                            'fan_2' => $recordData['fan_2'],
                            'fan_3' => $recordData['fan_3'] ?? null,
                            'fan_4' => $recordData['fan_4'] ?? null,
                            'aktual_humadity' => $recordData['aktual_humadity'] ?? null,
                            'infra_red' => $recordData['infra_red'] ?? null,
                            'aktual_lama_proses' => $validated['aktual_lama_proses'],
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('proses-roasting-fan.index')
                ->with('success', 'Data Proses Roasting Fan berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    public function show($uuid)
    {
        $user = Auth::user();
        
        // Get the first record to identify the session
        $firstRecord = ProsesRoastingFan::where('uuid', $uuid)->firstOrFail();
        
        if ($user->role !== 'superadmin' && $firstRecord->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }
        
        $sessionRecords = collect();

        // Check if this is a grouped record (new format)
        if ($firstRecord->is_grouped && $firstRecord->blok_data) {
            // Handle new JSON format
            foreach ($firstRecord->blok_data as $index => $blokItem) {
                $suhuBlok = \App\Models\SuhuBlok::find($blokItem['id_suhu_blok']);
                $stdFan = \App\Models\StdFan::find($blokItem['id_std_fan']);
                
                // Create a virtual record object for the view
                $virtualRecord = (object) [
                    'id' => $firstRecord->id,
                    'uuid' => $firstRecord->uuid,
                    'block_number' => $blokItem['block_number'],
                    'id_suhu_blok' => $blokItem['id_suhu_blok'],
                    'id_std_fan' => $blokItem['id_std_fan'],
                    'suhu_roasting' => $blokItem['suhu_roasting'] ?? null,
                    'fan_1' => $blokItem['fan_1'] ?? null,
                    'fan_2' => $blokItem['fan_2'] ?? null,
                    'fan_3' => $blokItem['fan_3'] ?? null,
                    'fan_4' => $blokItem['fan_4'] ?? null,
                    'aktual_humadity' => $blokItem['aktual_humadity'] ?? null,
                    'infra_red' => $blokItem['infra_red'] ?? null,
                    'suhuBlok' => $suhuBlok,
                    'stdFan' => $stdFan,
                    'produk' => $firstRecord->produk,
                    'shift' => $firstRecord->shift,
                    'user' => $firstRecord->user,
                    'tanggal' => $firstRecord->tanggal,
                    'aktual_lama_proses' => $firstRecord->aktual_lama_proses,
                ];
                
                $sessionRecords->push($virtualRecord);
            }
            
            $sessionRecords = $sessionRecords->sortBy('block_number');
        } else {
            // Handle old format - get all records from the same session
            $sessionRecords = ProsesRoastingFan::where('tanggal', $firstRecord->tanggal)
                ->where('id_shift', $firstRecord->id_shift)
                ->where('id_produk', $firstRecord->id_produk)
                ->where('aktual_lama_proses', $firstRecord->aktual_lama_proses)
                ->with(['suhuBlok', 'stdFan', 'produk', 'shift', 'user'])
                ->get();

            // Get all suhu blok for this product ordered by ID to maintain original block order
            $allSuhuBlok = SuhuBlok::where('id_produk', $firstRecord->id_produk)
                ->orderBy('id')
                ->get();
            
            // Create mapping based on original order: first record = block 1, second = block 2, etc.
            $idToBlockNumber = [];
            $blockNumber = 1;
            foreach($allSuhuBlok as $suhuBlokRecord) {
                $idToBlockNumber[$suhuBlokRecord->id] = $blockNumber++;
            }
            
            // Use stored block_number from database, fallback to calculated if not available
            $sessionRecords = $sessionRecords->map(function($record) use ($idToBlockNumber) {
                // Use stored block_number if available, otherwise calculate
                if (isset($record->block_number) && $record->block_number) {
                    $record->block_number = $record->block_number;
                } else {
                    $record->block_number = $idToBlockNumber[$record->id_suhu_blok] ?? 1;
                }
                return $record;
            })->sortBy('block_number');
        }
        
        return view('qc-sistem.proses_roasting_fan.show', compact('sessionRecords', 'firstRecord'));
    }

    public function destroy($uuid)
    {
        $data = ProsesRoastingFan::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $data->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $data->delete();
        return redirect()->route('proses-roasting-fan.index')->with('success', 'Data berhasil dihapus.');
    }

    // AJAX Endpoint
    public function getProductDetails(Request $request)
    {
        $user = Auth::user();
        $productId = $request->input('id_produk');
        $product = JenisProduk::find($productId);

        if ($user->role !== 'superadmin' && (!$product || $product->id_plan !== $user->id_plan)) {
            return response()->json([], 403);
        }

        $stdFan = StdFan::with('suhuBlok')->where('id_produk', $productId)->get();
        $suhuBlok = SuhuBlok::where('id_produk', $productId)->get();

        return response()->json([
            'std_fan' => $stdFan,
            'suhu_blok' => $suhuBlok
        ]);
    }

    public function getSuhuBlokByProduct($id)
    {
        // Get suhu blok for the product
        $suhuBlok = SuhuBlok::where('id_produk', $id)
            ->select('id', 'suhu_blok')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'suhu_blok' => $item->suhu_blok
                ];
            });
            
        return response()->json($suhuBlok);
    }

    public function getFanBySuhu($id)
    {
        // Get fan data for the suhu blok
        $stdFan = StdFan::where('id_suhu_blok', $id)
            ->select('id', 'std_fan', 'std_fan_2', 'std_lama_proses', 'fan_3', 'fan_4', 'std_humadity')
            ->first();
            
        if ($stdFan) {
            return response()->json([
                'success' => true,
                'id' => $stdFan->id,
                'std_fan' => $stdFan->std_fan,
                'std_fan_2' => $stdFan->std_fan_2,
                'std_lama_proses' => $stdFan->std_lama_proses,
                'fan_3' => $stdFan->fan_3,
                'fan_4' => $stdFan->fan_4,
                'std_humadity' => $stdFan->std_humadity
            ]);
        }
        
        return response()->json(['success' => false]);
    }

    /**
     * Show logs for a specific proses roasting fan record
     */
    public function showLogs($uuid)
    {
        // Check authorization
        if (!auth()->check()) {
            abort(403, 'Unauthorized access to logs');
        }

        $prosesRoastingFan = ProsesRoastingFan::where('uuid', $uuid)->firstOrFail();
        $prosesRoastingFan->load(['plan', 'user', 'shift', 'produk']);
        
        $logs = ProsesRoastingFanLog::where('proses_roasting_fan_uuid', $uuid)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.proses_roasting_fan.logs', compact('prosesRoastingFan', 'logs'));
    }

    /**
     * Get logs data in JSON format for a specific proses roasting fan record
     */
    public function getLogsJson($uuid)
    {
        // Check authorization
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = ProsesRoastingFanLog::where('proses_roasting_fan_uuid', $uuid)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($logs);
    }
}
