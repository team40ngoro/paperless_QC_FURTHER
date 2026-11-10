<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanRheonMachine;
use App\Models\Plan;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\StdBeratRheon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\PemeriksaanRheonMachineLog;
use App\Services\BulkExport\PemeriksaanRheonMachineBulkExportService;
use App\Http\Controllers\Traits\HasAuditController;

class PemeriksaanRheonMachineController extends Controller
{
    use HasAuditController;

    public function bulkExportExcel(Request $request, PemeriksaanRheonMachineBulkExportService $exportService)
    {
        return $exportService->exportExcel($request);
    }
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Auditor langsung diarahkan ke halaman audit
        if ($user->id_role == 6) {
            return redirect()->route('pemeriksaan-rheon-machine.audit');
        }

        $query = PemeriksaanRheonMachine::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->where('is_audit', false);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $search = request('search');
        if (!empty($search)) {
            $query->whereHas('produk', function ($produkQuery) use ($search) {
                $produkQuery->where('nama_produk', 'like', '%' . $search . '%');
            });
        }

        $perPage = request()->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50, 100]) ? $perPage : 10;

        $this->applyDateRangeFilter(request(), $query);
        $pemeriksaan = $query->orderBy('tanggal', 'desc')
            ->orderBy('pukul', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return view('qc-sistem.pemeriksaan-rheon-machine.index', compact('pemeriksaan', 'search', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
            $produk = JenisProduk::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $produk = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.pemeriksaan-rheon-machine.create', compact('shifts', 'produk'));
    }

    public function getStdBeratByProduk(Request $request, $id_produk)
    {
        $user = Auth::user();

        $planId = null;
        if ($user->role === 'superadmin') {
            $planId = $request->query('id_plan');
        } else {
            $planId = $user->id_plan;
        }

        $query = StdBeratRheon::query()->where('id_produk', $id_produk);

        if (!empty($planId)) {
            $query->where('id_plan', $planId);
        }

        $std = $query->first();

        return response()->json([
            'found' => (bool) $std,
            'std_adonan' => $std->std_adonan ?? null,
            'std_filler' => $std->std_filler ?? null,
            'std_after_forming' => $std->std_after_forming ?? null,
            'std_after_frying' => $std->std_after_frying ?? null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'shift_id'               => 'required|exists:data_shift,id',
            'id_produk'              => 'required|exists:jenis_produk,id',
            'tanggal'                => 'required|date',
            'batch'                  => 'required|string|max:255',
            'pukul'                  => 'required',
            'inner'                  => 'nullable|string|max:255',
            'outer'                  => 'nullable|string|max:255',
            'belt'                   => 'nullable|string|max:255',
            'extrusion_speed'        => 'nullable|string|max:255',
            'jenis_cetakan'          => 'nullable|string|max:255',
            'catatan'                => 'nullable|string',
            'new_jumlah_dough'       => 'nullable|numeric',
            'new_jumlah_filler'      => 'nullable|numeric',
            'new_jumlah_after_forming' => 'nullable|numeric',
            'new_jumlah_after_frying'  => 'nullable|numeric',
            'rata_rata_dough'        => 'nullable|numeric',
            'rata_rata_filler'       => 'nullable|numeric',
            'rata_rata_after_forming' => 'nullable|numeric',
            'rata_rata_after_frying'  => 'nullable|numeric',
        ]);

        $tanggalData = \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now());

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        

        $user = Auth::user();

        // Process dynamic form data - handle nested arrays from JavaScript
        // $doughData = $request->input('berat_dough_adonan_items', []);
        $doughData = $request->input('input_dough_berat_with_value', []);
        $fillerData = $request->input('berat_filler_items_with_value', []);
        $afterFormingData = $request->input('berat_after_forming_items_with_value', []);
        $afterFryingData = $request->input('berat_after_frying_items_with_value', []);
        
        // Prepare data array following PengemasanPlastikController pattern
        // Exclude kode_form from regular CRUD operations for security
        $data = $request->except(['kode_form']);
        $data = array_merge($data, [
            'uuid' => Str::uuid(),
            'id_plan' => $user->id_plan,
            'user_id' => $user->id,
            'tanggal'=> $tanggalData,
            // Store calculated averages from readonly inputs
            'rata_rata_dough' => $request->rata_rata_dough ?? 0,
            'rata_rata_filler' => $request->rata_rata_filler ?? 0,
            'rata_rata_after_forming' => $request->rata_rata_after_forming ?? 0,
            'rata_rata_after_frying' => $request->rata_rata_after_frying ?? 0,
            'jumlah_dough' =>  $request->new_jumlah_dough ?? 0,
            'jumlah_filler' => $request->new_jumlah_filler ?? 0,
            'jumlah_after_forming' => $request->new_jumlah_after_forming ?? 0,
            'jumlah_after_frying' =>   $request->new_jumlah_after_frying ?? 0,
            // Store dynamic form data
            'berat_dough_adonan' => $doughData,
            'berat_filler' => $fillerData,
            'berat_after_forming' => $afterFormingData,
            'berat_after_frying' => $afterFryingData,
        ]);
        // dd($data);

        // Create a new instance with basic data
        $pemeriksaan = new PemeriksaanRheonMachine($data);
        
        // Ensure values are calculated before saving
        $this->recalculateValues($pemeriksaan);
        
        // Save the record
        $pemeriksaan->save();
        

        return redirect()->route('pemeriksaan-rheon-machine.index')
            ->with('success', 'Data pemeriksaan rheon machine berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $pemeriksaanRheonMachine = PemeriksaanRheonMachine::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check authorization
        if ($user->role !== 'superadmin' && $pemeriksaanRheonMachine->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('pemeriksaan-rheon-machine.show', $pemeriksaanRheonMachine)) {
            return $redirect;
        }

        $pemeriksaanRheonMachine->load(['plan', 'user', 'shift', 'produk']);

        return view('qc-sistem.pemeriksaan-rheon-machine.show', compact('pemeriksaanRheonMachine'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $pemeriksaanRheonMachine = PemeriksaanRheonMachine::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        if ($redirect = $this->redirectToCanonicalAuditUrl('pemeriksaan-rheon-machine.edit', $pemeriksaanRheonMachine)) {
            return $redirect;
        }

        // Check edit time limit using global config
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($pemeriksaanRheonMachine->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('pemeriksaan-rheon-machine.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        // Check authorization
        if ($user->role !== 'superadmin' && $pemeriksaanRheonMachine->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        // Get shifts filtered by user's plan
        $shifts = DataShift::where('id_plan', $user->id_plan)->get();
        
        // Get products filtered by user's plan
        $produk = JenisProduk::where('id_plan', $user->id_plan)->get();

        return view('qc-sistem.pemeriksaan-rheon-machine.edit', compact('pemeriksaanRheonMachine', 'shifts', 'produk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $pemeriksaanRheonMachine = PemeriksaanRheonMachine::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        // Check edit time limit using global config
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($pemeriksaanRheonMachine->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('pemeriksaan-rheon-machine.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        // Check authorization
        if ($user->role !== 'superadmin' && $pemeriksaanRheonMachine->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized access.');
        }

        $validator = Validator::make($request->all(), [
            'shift_id' => 'required|exists:data_shift,id',
            'id_produk' => 'required|exists:jenis_produk,id',
            'tanggal' => 'required|date',
            'batch' => 'required|string|max:255',
            'pukul' => 'required',
            'inner' => 'nullable|string|max:255',
            'outer' => 'nullable|string|max:255',
            'belt' => 'nullable|string|max:255',
            'extrusion_speed' => 'nullable|string|max:255',
            'jenis_cetakan' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'new_jumlah_dough' => 'nullable|numeric',
            'new_jumlah_filler' => 'nullable|numeric',
            'new_jumlah_after_forming' => 'nullable|numeric',
            'new_jumlah_after_frying' => 'nullable|numeric',
            'rata_rata_dough' => 'nullable|numeric',
            'rata_rata_filler' => 'nullable|numeric',
            'rata_rata_after_forming' => 'nullable|numeric',
            'rata_rata_after_frying' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update the record
        $pemeriksaanRheonMachine->shift_id = $request->shift_id;
        $pemeriksaanRheonMachine->id_produk = $request->id_produk;
        $pemeriksaanRheonMachine->tanggal = \Carbon\Carbon::parse($request->tanggal)
            ->setTimeFrom($pemeriksaanRheonMachine->tanggal ?? now());
        $pemeriksaanRheonMachine->batch = $request->batch;
        $pemeriksaanRheonMachine->inner = $request->inner;
        $pemeriksaanRheonMachine->outer = $request->outer;
        $pemeriksaanRheonMachine->belt = $request->belt;
        $pemeriksaanRheonMachine->extrusion_speed = $request->extrusion_speed;
        $pemeriksaanRheonMachine->jenis_cetakan = $request->jenis_cetakan;
        $pemeriksaanRheonMachine->catatan = $request->catatan;
        
        // Note: Calculated values (jumlah and rata_rata) will be set by recalculateValues() method

        // Process dynamic form data - get actual weight values from input fields
        $doughInputValues = $request->input('input_dough_berat', []);
        $fillerInputValues = $request->input('input_filler_berat', []);
        $afterFormingInputValues = $request->input('input_after_forming_berat', []);
        $afterFryingInputValues = $request->input('input_after_frying_berat', []);
        
        // Transform input values to match the expected array structure
        $doughData = [];
        $fillerData = [];
        $afterFormingData = [];
        $afterFryingData = [];
        
        // Process dough input values
        foreach ($doughInputValues as $sectionIndex => $values) {
            if (is_array($values)) {
                $doughData[$sectionIndex] = array_filter($values, function($value) {
                    return !empty($value) && is_numeric($value);
                });
            }
        }
        
        // Process filler input values
        foreach ($fillerInputValues as $sectionIndex => $values) {
            if (is_array($values)) {
                $fillerData[$sectionIndex] = array_filter($values, function($value) {
                    return !empty($value) && is_numeric($value);
                });
            }
        }
        
        // Process after forming input values
        foreach ($afterFormingInputValues as $sectionIndex => $values) {
            if (is_array($values)) {
                $afterFormingData[$sectionIndex] = array_filter($values, function($value) {
                    return !empty($value) && is_numeric($value);
                });
            }
        }
        
        // Process after frying input values
        foreach ($afterFryingInputValues as $sectionIndex => $values) {
            if (is_array($values)) {
                $afterFryingData[$sectionIndex] = array_filter($values, function($value) {
                    return !empty($value) && is_numeric($value);
                });
            }
        }

        // Store dynamic form data
        $pemeriksaanRheonMachine->berat_dough_adonan = $doughData;
        $pemeriksaanRheonMachine->berat_filler = $fillerData;
        $pemeriksaanRheonMachine->berat_after_forming = $afterFormingData;
        $pemeriksaanRheonMachine->berat_after_frying = $afterFryingData;

        // Recalculate totals and averages based on the input data
        $this->recalculateValues($pemeriksaanRheonMachine);

        // Collect all final calculation results into array for inspection
        $calculationResults = [
            'basic_data' => [
                'shift_id' => $pemeriksaanRheonMachine->shift_id,
                'id_produk' => $pemeriksaanRheonMachine->id_produk,
                'tanggal' => $pemeriksaanRheonMachine->tanggal,
                'batch' => $pemeriksaanRheonMachine->batch,
                'pukul' => $pemeriksaanRheonMachine->pukul,
            ],
            'rheon_machine_settings' => [
                'inner' => $pemeriksaanRheonMachine->inner,
                'outer' => $pemeriksaanRheonMachine->outer,
                'belt' => $pemeriksaanRheonMachine->belt,
                'extrusion_speed' => $pemeriksaanRheonMachine->extrusion_speed,
                'jenis_cetakan' => $pemeriksaanRheonMachine->jenis_cetakan,
            ],
            'input_data_arrays' => [
                'berat_dough_adonan' => $doughData,
                'berat_filler' => $fillerData,
                'berat_after_forming' => $afterFormingData,
                'berat_after_frying' => $afterFryingData,
            ],
            'calculated_counts' => [
                'jumlah_dough' => $pemeriksaanRheonMachine->jumlah_dough,
                'jumlah_filler' => $pemeriksaanRheonMachine->jumlah_filler,
                'jumlah_after_forming' => $pemeriksaanRheonMachine->jumlah_after_forming,
                'jumlah_after_frying' => $pemeriksaanRheonMachine->jumlah_after_frying,
            ],
            'calculated_averages' => [
                'rata_rata_dough' => $pemeriksaanRheonMachine->rata_rata_dough,
                'rata_rata_filler' => $pemeriksaanRheonMachine->rata_rata_filler,
                'rata_rata_after_forming' => $pemeriksaanRheonMachine->rata_rata_after_forming,
                'rata_rata_after_frying' => $pemeriksaanRheonMachine->rata_rata_after_frying,
            ],
            'additional_info' => [
                'catatan' => $pemeriksaanRheonMachine->catatan,
                'uuid' => $pemeriksaanRheonMachine->uuid,
                'id_plan' => $pemeriksaanRheonMachine->id_plan,
                'user_id' => $pemeriksaanRheonMachine->user_id,
            ]
        ];

        // Display calculation results for inspection
      
        
        $pemeriksaanRheonMachine->save();

        // Smart redirect: jika audit -> ke audit page, jika operasional -> ke index page
        $redirectRoute = $pemeriksaanRheonMachine->is_audit ? 'pemeriksaan-rheon-machine.audit' : 'pemeriksaan-rheon-machine.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Data pemeriksaan rheon machine berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        try {
            $pemeriksaanRheonMachine = PemeriksaanRheonMachine::where('uuid', $uuid)->firstOrFail();
            $user = Auth::user();
            
            // Check authorization
            if ($user->role !== 'superadmin' && $pemeriksaanRheonMachine->id_plan !== $user->id_plan) {
                abort(403, 'Unauthorized access.');
            }

            // Store batch info for success message
            $batchInfo = $pemeriksaanRheonMachine->batch;

            $pemeriksaanRheonMachine->delete();

            return redirect()->route('pemeriksaan-rheon-machine.index')
                ->with('success', "Data pemeriksaan rheon machine batch {$batchInfo} berhasil dihapus.");
                
        } catch (\Exception $e) {
            return redirect()->route('pemeriksaan-rheon-machine.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
    /**
     * Tampilkan halaman logs untuk pemeriksaan rheon machine
     */
    public function showLogs($uuid)
    {
        $item = PemeriksaanRheonMachine::where('uuid', $uuid)->firstOrFail();
        
        $logs = PemeriksaanRheonMachineLog::where('pemeriksaan_rheon_machine_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(5);
        
        return view('qc-sistem.pemeriksaan-rheon-machine.logs', compact('item', 'logs'));
    }

    /**
     * Get logs data untuk DataTables (jika diperlukan)
     */
    public function getLogsJson($uuid)
    {
        $pemeriksaanRheonMachine = PemeriksaanRheonMachine::where('uuid', $uuid)->firstOrFail();
        
        $logs = PemeriksaanRheonMachineLog::where('pemeriksaan_rheon_machine_uuid', $uuid)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'tanggal' => $log->created_at->format('d/m/Y H:i:s'),
                    'user' => $log->user_name,
                    'field' => $log->nama_field,
                    'perubahan' => $log->deskripsi_perubahan,
                    'ip_address' => $log->ip_address
                ];
            });

        return response()->json(['data' => $logs]);
    }

    /**
     * Recalculate all values based on input data following create method logic
     */
    private function recalculateValues($pemeriksaan)
    {
        // Get arrays from the stored data
        $doughArray = $pemeriksaan->getBeratDoughAdonanArrayAttribute();
        $fillerArray = $pemeriksaan->getBeratFillerArrayAttribute();
        $formingArray = $pemeriksaan->getBeratAfterFormingArrayAttribute();
        $fryingArray = $pemeriksaan->getBeratAfterFryingArrayAttribute();

        // Initialize totals and counts
        $doughTotal = 0;
        $fillerTotal = 0;
        $formingTotal = 0;
        $fryingTotal = 0;
        
        $doughCount = 0;
        $fillerCount = 0;
        $formingCount = 0;
        $fryingCount = 0;

        // Calculate dough totals and counts
        if (!empty($doughArray)) {
            foreach ($doughArray as $sectionData) {
                if (is_array($sectionData)) {
                    foreach ($sectionData as $value) {
                        if (is_numeric($value) && $value > 0) {
                            $doughTotal += (float)$value;
                            $doughCount++;
                        }
                    }
                }
            }
        }

        // Calculate filler totals and counts
        if (!empty($fillerArray)) {
            foreach ($fillerArray as $sectionData) {
                if (is_array($sectionData)) {
                    foreach ($sectionData as $value) {
                        if (is_numeric($value) && $value > 0) {
                            $fillerTotal += (float)$value;
                            $fillerCount++;
                        }
                    }
                }
            }
        }

        // Calculate after forming totals and counts
        if (!empty($formingArray)) {
            foreach ($formingArray as $sectionData) {
                if (is_array($sectionData)) {
                    foreach ($sectionData as $value) {
                        if (is_numeric($value) && $value > 0) {
                            $formingTotal += (float)$value;
                            $formingCount++;
                        }
                    }
                }
            }
        }

        // Calculate after frying totals and counts
        if (!empty($fryingArray)) {
            foreach ($fryingArray as $sectionData) {
                if (is_array($sectionData)) {
                    foreach ($sectionData as $value) {
                        if (is_numeric($value) && $value > 0) {
                            $fryingTotal += (float)$value;
                            $fryingCount++;
                        }
                    }
                }
            }
        }

        // Advanced Calculation Logic for Dough/Filler (Cross-reference calculation)
        $doughAverage = 0;
        $fillerAverage = 0;

        if ($doughCount > 0 && $fillerCount === 0) {
            // Calculate dough average only
            $doughAverage = $doughTotal / $doughCount;
            $fillerAverage = 0;
        } elseif ($fillerCount > 0 && $doughCount === 0) {
            // Calculate filler average only
            $fillerAverage = $fillerTotal / $fillerCount;
            $doughAverage = 0;
        } elseif ($doughCount > 0 && $fillerCount > 0) {
            // Both exist - calculate separately
            $doughAverage = $doughTotal / $doughCount;
            $fillerAverage = $fillerTotal / $fillerCount;
        } else {
            // Both zero
            $doughAverage = 0;
            $fillerAverage = 0;
        }

        // Advanced Calculation Logic for After Forming/Frying (Cross-reference calculation)
        $afterFormingAverage = 0;
        $afterFryingAverage = 0;

        if ($formingCount > 0 && $fryingCount === 0) {
            // Calculate after forming average only
            $afterFormingAverage = $formingTotal / $formingCount;
            $afterFryingAverage = 0;
        } elseif ($fryingCount > 0 && $formingCount === 0) {
            // Calculate after frying average only
            $afterFryingAverage = $fryingTotal / $fryingCount;
            $afterFormingAverage = 0;
        } elseif ($formingCount > 0 && $fryingCount > 0) {
            // Both exist - calculate separately
            $afterFormingAverage = $formingTotal / $formingCount;
            $afterFryingAverage = $fryingTotal / $fryingCount;
        } else {
            // Both zero
            $afterFormingAverage = 0;
            $afterFryingAverage = 0;
        }

        // For edit form: Store COUNTS instead of TOTALS for jumlah fields
        $pemeriksaan->jumlah_dough = $doughCount;
        $pemeriksaan->jumlah_filler = $fillerCount;
        $pemeriksaan->jumlah_after_forming = $formingCount;
        $pemeriksaan->jumlah_after_frying = $fryingCount;
        
        $pemeriksaan->rata_rata_dough = round($doughAverage, 2);
        $pemeriksaan->rata_rata_filler = round($fillerAverage, 2);
        $pemeriksaan->rata_rata_after_forming = round($afterFormingAverage, 2);
        $pemeriksaan->rata_rata_after_frying = round($afterFryingAverage, 2);
    }

    /**
     * Calculate only jumlah totals without affecting rata_rata values
     */
    private function calculateJumlahTotalsOnly($pemeriksaan)
    {
        // Calculate dough and filler totals
        $doughArray = $pemeriksaan->getBeratDoughAdonanArrayAttribute();
        $fillerArray = $pemeriksaan->getBeratFillerArrayAttribute();

        $doughTotal = 0;
        $fillerTotal = 0;

        // Calculate dough totals - sum all values from all sections
        if (!empty($doughArray)) {
            foreach ($doughArray as $sectionData) {
                if (is_array($sectionData)) {
                    foreach ($sectionData as $value) {
                        if (is_numeric($value) && $value > 0) {
                            $doughTotal += (float)$value;
                        }
                    }
                }
            }
        }

        // Calculate filler totals - sum all values from all sections
        if (!empty($fillerArray)) {
            foreach ($fillerArray as $sectionData) {
                if (is_array($sectionData)) {
                    foreach ($sectionData as $value) {
                        if (is_numeric($value) && $value > 0) {
                            $fillerTotal += (float)$value;
                        }
                    }
                }
            }
        }

        // Set only jumlah totals, preserve rata_rata values from form
        $pemeriksaan->jumlah_dough = round($doughTotal);
        $pemeriksaan->jumlah_filler = round($fillerTotal);

        // Calculate after forming and after frying totals
        $formingArray = $pemeriksaan->getBeratAfterFormingArrayAttribute();
        $fryingArray = $pemeriksaan->getBeratAfterFryingArrayAttribute();

        $formingTotal = 0;
        $fryingTotal = 0;

        // Calculate after forming totals - sum all values from all sections
        if (!empty($formingArray)) {
            foreach ($formingArray as $sectionData) {
                if (is_array($sectionData)) {
                    foreach ($sectionData as $value) {
                        if (is_numeric($value) && $value > 0) {
                            $formingTotal += (float)$value;
                        }
                    }
                }
            }
        }

        // Calculate after frying totals - sum all values from all sections
        if (!empty($fryingArray)) {
            foreach ($fryingArray as $sectionData) {
                if (is_array($sectionData)) {
                    foreach ($sectionData as $value) {
                        if (is_numeric($value) && $value > 0) {
                            $fryingTotal += (float)$value;
                        }
                    }
                }
            }
        }

        // Set only jumlah totals, preserve rata_rata values from form
        $pemeriksaan->jumlah_after_forming = round($formingTotal);
        $pemeriksaan->jumlah_after_frying = round($fryingTotal);
    }

    /**
     * Approve data with role-based validation
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $pemeriksaan = PemeriksaanRheonMachine::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control following Produk Forming pattern
        $allowedRoles = [
            'qc' => [1, 3, 5], // Role 1&5 (QC only), Role 3 (QC only)
            'produksi' => [2, 5], // Role 2 (produksi only)
            'spv' => [4, 5] // Role 4 (SPV only)
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan approval ini.'
            ], 403);
        }

        // Sequential approval validation (QC → Produksi → SPV)
        if ($type === 'produksi' && !$pemeriksaan->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$pemeriksaan->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($pemeriksaan->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $pemeriksaan->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }

    /**
     * Bulk Approve data by date and shift
     */
    public function bulkApproveSelected(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:qc,produksi,spv',
            'uuids' => 'required|array|min:1',
            'uuids.*' => 'required|uuid',
        ]);

        $user = Auth::user();
        $type = $validated['type'];
        $allowedRoles = ['qc' => [1, 3, 5], 'produksi' => [2, 5], 'spv' => [4, 5]];
        if (!in_array((int) $user->id_role, $allowedRoles[$type], true)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk approval ini.'], 403);
        }

        $query = PemeriksaanRheonMachine::whereIn('uuid', $validated['uuids']);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $items = $query->get();
        if ($items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Data terpilih tidak ditemukan.'], 404);
        }

        $approvalField = "approved_by_{$type}";
        $pending = $items->where($approvalField, false);
        if ($pending->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Semua data terpilih sudah diverifikasi sebelumnya.']);
        }
        $updateData = [
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now(),
        ];
        \DB::transaction(function () use ($pending, $updateData) {
            foreach ($pending as $item) {
                $item->update($updateData);
                $sourceUuid = $item->is_audit ? $item->source_uuid : $item->uuid;
                if ($sourceUuid) {
                    PemeriksaanRheonMachine::where('is_audit', false)->where('uuid', $sourceUuid)->update($updateData);
                    PemeriksaanRheonMachine::where('is_audit', true)->where('source_uuid', $sourceUuid)->update($updateData);
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Berhasil memverifikasi ' . $pending->count() . ' data ' . strtoupper($type) . '.']);
    }

    public function bulkApprove(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'shift_id' => 'required',
                'type' => 'required|in:qc,produksi,spv'
            ]);

            $user = auth()->user();
            $userRole = $user->id_role;
            $type = $request->type;
            $tanggal = $request->tanggal;
            $shift_id = $request->shift_id;

            // Role-based access control following Produk Forming pattern
            $allowedRoles = [
                'qc' => [1, 3, 5], // Role 1&5 (QC only), Role 3 (QC only)
                'produksi' => [2, 5], // Role 2 (produksi only)
                'spv' => [4, 5] // Role 4 (SPV only)
            ];

            if (!in_array($userRole, $allowedRoles[$type])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized role'], 403);
            }

            // Get records to approve
            $query = PemeriksaanRheonMachine::whereDate('tanggal', $tanggal)->where('shift_id', $shift_id);

            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }

            $allRecords = $query->get();

            if ($allRecords->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data untuk disetujui'], 404);
            }

            // Sequential validation check for the WHOLE group
            if ($type === 'produksi') {
                $anyMissingQc = $allRecords->contains(fn($r) => !$r->approved_by_qc);
                if ($anyMissingQc) {
                    return response()->json(['success' => false, 'message' => 'Beberapa data belum disetujui oleh QC'], 400);
                }
            }

            if ($type === 'spv') {
                $anyMissingProduksi = $allRecords->contains(fn($r) => !$r->approved_by_produksi);
                if ($anyMissingProduksi) {
                    return response()->json(['success' => false, 'message' => 'Beberapa data belum disetujui oleh Produksi'], 400);
                }
            }

            $approvalField = 'approved_by_' . $type;
            $approverField = $type . '_approved_by';
            $approvedAtField = $type . '_approved_at';

            \DB::beginTransaction();

            $count = 0;
            foreach ($allRecords as $record) {
                if (!$record->$approvalField) {
                    $record->update([
                        $approvalField => true,
                        $approverField => $user->id,
                        $approvedAtField => now()
                    ]);
                    $count++;
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menyetujui {$count} data sebagai " . strtoupper($type)
            ]);

        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export PDF with filters
     */
    public function bulkExportPdf(Request $request)
    {
        $request->validate([
            'kode_form' => 'required|string|max:50',
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'shift' => 'nullable|string',
            'produk' => 'nullable|string'
        ]);

        $user = Auth::user();
        $query = PemeriksaanRheonMachine::with(['plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover']);

        // Apply user plan filter for non-superadmin
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($request->has('is_audit') && $request->is_audit == 1) {
            $query->where('is_audit', true);
        } else {
            $query->operasional();
        }

        // Apply filters
        $tanggalFilter = 'Semua Tanggal';
        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $tanggalAwal = \Carbon\Carbon::parse($request->tanggal_awal)->startOfDay();
            $tanggalAkhir = \Carbon\Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            $tanggalFilter = \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
        } elseif ($request->tanggal_awal) {
            $query->whereDate('tanggal', $request->tanggal_awal);
            $tanggalFilter = \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y');
        }
        if ($request->shift) {
            $query->whereHas('shift', function($q) use ($request) {
                $q->where('shift', $request->shift);
            });
        }
        if ($request->produk) {
            $query->whereHas('produk', function($q) use ($request) {
                $q->where('nama_produk', $request->produk);
            });
        }

        $filters = [
            'tanggal' => $tanggalFilter,
            'shift' => $request->shift ?: 'Semua Shift',
            'produk' => $request->produk ?: 'Semua Produk',
            'kode_form' => $request->kode_form
        ];

        $pemeriksaan = $query->orderBy('tanggal', 'desc')->get();

        // Save kode_form to all filtered records
        if ($pemeriksaan->isNotEmpty()) {
            PemeriksaanRheonMachine::whereIn('id', $pemeriksaan->pluck('id'))->update(['kode_form' => $request->kode_form]);
        }

        if ($pemeriksaan->isEmpty()) {
            $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
            $filterInfo = [];
            
            if ($request->tanggal_awal && $request->tanggal_akhir) {
                $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
            } elseif ($request->tanggal_awal) {
                $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y');
            }
            if ($request->shift) {
                $filterInfo[] = 'Shift: ' . $request->shift;
            }
            if ($request->produk) {
                $filterInfo[] = 'Produk: ' . $request->produk;
            }
            if ($request->kode_form) {
                $filterInfo[] = 'Kode Form: ' . $request->kode_form;
            }
            
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Data Tidak Ditemukan</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 40px; text-align: center; background-color: #f8f9fa; }
                    .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                    .icon { font-size: 64px; color: #ffc107; margin-bottom: 20px; }
                    h1 { color: #495057; margin-bottom: 20px; }
                    .message { color: #6c757d; margin-bottom: 30px; font-size: 16px; }
                    .filter-info { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0; text-align: left; }
                    .filter-info h4 { margin: 0 0 10px 0; color: #495057; }
                    .filter-info ul { margin: 0; padding-left: 20px; }
                    .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
                    .btn:hover { background: #0056b3; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="icon">⚠️</div>
                    <h1>Data Tidak Ditemukan</h1>
                    <p class="message">' . $errorMessage . '</p>';
                    
            if (!empty($filterInfo)) {
                $html .= '
                    <div class="filter-info">
                        <h4>Filter yang digunakan:</h4>
                        <ul>';
                foreach ($filterInfo as $info) {
                    $html .= '<li>' . $info . '</li>';
                }
                $html .= '
                        </ul>
                    </div>';
            }
            
            $html .= '
                    <p style="color: #6c757d; font-size: 14px;">
                        Silakan coba dengan filter yang berbeda atau pastikan data sudah tersedia di sistem.
                    </p>
                    <a href="javascript:window.close()" class="btn">Tutup</a>
                </div>
            </body>
            </html>';
            
            return response($html)->header('Content-Type', 'text/html');
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('qc-sistem.pemeriksaan-rheon-machine.export_pdf', compact('pemeriksaan', 'filters'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'pemeriksaan-rheon-machine-' . date('Y-m-d-H-i-s') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Copy to audit with smart logic
     */
    public function copyToAudit($uuid)
    {
        $user = auth()->user();

        // Auditor (id_role 6) bisa akses audit, atau user dengan permission
        if ($user->id_role != 6 && !$user->hasPermissionTo('create-pemeriksaan-rheon-machine')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk membuat data audit.');
        }

        $item = PemeriksaanRheonMachine::where('uuid', $uuid)->firstOrFail();

        // Jika item yang diklik sudah merupakan record audit
        if ($item->is_audit) {
            return redirect()->route('pemeriksaan-rheon-machine.edit', $item->uuid);
        }

        // Jika data operasional ini sudah memiliki versi audit sebelumnya
        if ($item->auditVersion) {
            return redirect()->route('pemeriksaan-rheon-machine.edit', $item->auditVersion->uuid);
        }

        // Jika belum memiliki versi audit, buatkan salinan audit secara otomatis
        $clone = $item->copyToAudit();

        return redirect()->route('pemeriksaan-rheon-machine.edit', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    // --- AUDIT TRAIL METHODS ---

    protected function auditModel(): string
    {
        return PemeriksaanRheonMachine::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'pemeriksaan-rheon-machine';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.pemeriksaan-rheon-machine';
    }

    protected function auditRelations(): array
    {
        return [
            'plan', 'user', 'shift', 'produk', 'qcApprover', 'produksiApprover', 'spvApprover'
        ];
    }

    protected function auditSearchColumns(): array
    {
        return ['batch'];
    }

    protected function auditTimeColumn(): string
    {
        return 'pukul';
    }
}
