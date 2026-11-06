<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\InputRoasting;
use App\Models\ProsesRoastingFan;
use App\Models\HasilProsesRoasting;
use App\Models\PembekuanIqfRoasting;
use App\Models\SuhuBlok;
use App\Models\StdFan;
use App\Models\StdSuhuPusatRoasting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WizardProses3Export;
use App\Http\Controllers\Traits\HasAuditController;

class WizardProses3Controller extends Controller
{
    protected function auditModel(): string
    {
        return PembekuanIqfRoasting::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'wizard.proses3';
    }


    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal_mulai' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
                'shift_id' => 'nullable|integer',
                'produk_id' => 'nullable|integer',
                'kode_form' => 'required|string|max:50',
                'is_audit' => 'nullable|boolean',
            ]);

            $user = Auth::user();

            $query = PembekuanIqfRoasting::with($this->auditRelations())
            ->where('is_audit', $request->boolean('is_audit', false))
            ->whereNotNull('input_roasting_uuid') // Filter khusus alur Proses 3
            ->whereNull('penggorengan_uuid')    // Pastikan bukan berasal dari alur Proses 2
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

            // Apply filters
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai)
                  ->whereDate('tanggal', '<=', $request->tanggal_akhir);

            if ($request->shift_id) {
                $query->where('shift_id', $request->shift_id);
            }

            if ($request->produk_id) {
                $query->whereHas('hasilProsesRoastingData', function ($q) use ($request) {
                    $q->where('id_produk', $request->produk_id);
                });
            }

            $data = $query->orderBy('tanggal', 'desc')->get();

            if ($data->isEmpty()) {
                // Return a user-friendly message if no data is found
                return back()->with('error', 'Tidak ada data yang sesuai dengan filter yang dipilih.');
            }

            $filters = [
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir,
                'shift_id' => $request->shift_id,
                'produk_id' => $request->produk_id,
                'kode_form' => $request->kode_form
            ];

            $kode_form = $request->kode_form;
            $pdf = PDF::loadView('qc-sistem.wizard_proses3.export_pdf', compact('data', 'kode_form', 'filters'));
            $pdf->setPaper('A4', 'portrait');

            $filename = 'Wizard-Proses3-Bulk-'. date('Y-m-d_H-i-s') .'.pdf';
            return $pdf->stream($filename);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export Excel massal berdasarkan filter (meniru bulkExportPdf)
     */
    public function bulkExportExcel(Request $request, \App\Services\BulkExport\WizardProses3BulkExportService $service)
    {
        return $service->export($request);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->id_role == 6) {
            return redirect()->route('wizard.proses3.audit');
        }
        $search = $request->input('search');

        // Ambil data pembekuan iqf yang berasal dari alur Proses 3 (Input Roasting Langsung)
        $query = PembekuanIqfRoasting::operasional()->with([
            'hasilProsesRoastingData',
            'hasilProsesRoastingData.produk',
            'hasilProsesRoastingData.stdSuhuPusat',
            'inputRoastingData',
            'prosesRoastingFanData',
            'shift',
        ])
        ->whereNotNull('input_roasting_uuid') // Filter khusus alur Proses 3
        ->whereNull('penggorengan_uuid');    // Pastikan bukan berasal dari alur Proses 2

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('hasilProsesRoastingData.produk', function($qp) use ($search) {
                    $qp->where('nama_produk', 'LIKE', "%{$search}%");
                })->orWhereHas('inputRoastingData', function($qi) use ($search) {
                    $qi->where('kode_produksi', 'LIKE', "%{$search}%");
                });
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $pembekuanData = $query->orderByRaw('DATE(tanggal) DESC')
            ->orderByRaw('TIME(jam) DESC')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);
        
        $groupedData = $pembekuanData->getCollection()->groupBy(function($item) {
            $date = $item->tanggal ? $item->tanggal->format('Y-m-d') : 'no_date';
            $shiftId = $item->shift_id ?? 'no_shift';
            return $date . '_' . $shiftId;
        })->map(function($group) {
            return $group->values();
        })->sortByDesc(function($group) {
            $first = $group->first();
            $date = $first->tanggal ? $first->tanggal->format('Y-m-d') : '0000-00-00';
            $shiftVal = $first->shift->shift ?? 0;
            return $date . sprintf('%03d', $shiftVal);
        });
        
        $pembekuanData->setCollection($groupedData);

        return view('qc-sistem.wizard_proses3.index', compact('pembekuanData'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $continueUuid = $request->query('continue_uuid');
        $draftData = null;

        if ($continueUuid) {
            // Hilangkan with() karena relasi tidak didefinisikan di model
            $draftData = InputRoasting::where('uuid', $continueUuid)->first();
        }

        if ($user->role === 'superadmin') {
            $produk = JenisProduk::orderBy('nama_produk', 'asc')->get();
            $shift  = DataShift::all();
        } else {
            $produk = JenisProduk::where('id_plan', $user->id_plan)->orderBy('nama_produk', 'asc')->get();
            $shift  = DataShift::where('id_plan', $user->id_plan)->get();
        }
        $suhuBlok = SuhuBlok::all();
        $stdFan = StdFan::all();
        $stdSuhuPusat = StdSuhuPusatRoasting::all();

        return view('qc-sistem.wizard_proses3.create', compact('produk', 'shift', 'suhuBlok', 'stdFan', 'stdSuhuPusat', 'draftData'));
    }

    // STEP 1: INPUT ROASTING (Bahan Baku)
    public function storeInputRoasting(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'id_produk'           => 'required',
                'id_shift'            => 'required',
                'kode_produksi'       => 'required|string',
                'berat_bahan'         => 'required|string',
                'std_suhu_sebelum'    => 'required|string',
                'aktual_suhu_sesudah' => 'required|string',
                'jam'                 => 'required|string',
                'tanggal'             => 'required|date',
            ]);

            $record = InputRoasting::create([
                'uuid'                => (string) Str::uuid(),
                'id_plan'             => $this->resolvePlanId($user, $request->id_produk),
                'shift_id'            => $request->id_shift, // Sesuai Model
                'user_id'             => $user->id,
                'id_produk'           => $request->id_produk,
                'kode_produksi'       => $request->kode_produksi,
                'berat_produk'        => $request->berat_bahan, // Sesuai Model
                'std_suhu_sebelum'    => $request->std_suhu_sebelum,
                'aktual_suhu_sesudah' => $request->aktual_suhu_sesudah,
                'jam'                 => $request->jam,
                'tanggal'             => Carbon::parse($request->tanggal)->setTimeFrom(now()),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Input Roasting berhasil disimpan.',
                'uuid'    => $record->uuid,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // STEP 2: ROASTING FAN (Grouped per Blok)
    public function storeRoastingFan(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'tanggal'             => 'required|date',
                'jam'                 => 'required|string',
                'waktu_pemasakan'     => 'nullable|string',
                'id_produk'           => 'required',
                'input_roasting_uuid' => 'required|string|exists:input_roasting,uuid',

                // Data blok - allow nullable elements as they are filtered in the loop below
                'id_suhu_blok'   => 'required|array',
                'id_suhu_blok.*' => 'nullable',
                'id_std_fan'     => 'required|array',
                'id_std_fan.*'   => 'nullable',
                'block_number'   => 'required|array',
                'block_number.*' => 'nullable',
                'suhu_roasting'  => 'required|array',
                'suhu_roasting.*' => 'nullable',
                'fan_1'          => 'required|array',
                'fan_1.*'        => 'nullable',
                'fan_2'          => 'required|array',
                'fan_2.*'        => 'nullable',
                'fan_3'          => 'nullable|array',
                'fan_4'          => 'nullable|array',
                'aktual_humadity' => 'nullable|array',
                'infra_red'      => 'nullable|array',
            ]);

            $inputRoasting = InputRoasting::where('uuid', $validated['input_roasting_uuid'])->first();

            // Handle merged Infeed & Outfeed inputs
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

            // Build blok_data JSON
            $blokData = [];
            foreach ($validated['id_suhu_blok'] as $i => $idSuhuBlok) {
                $suhuRoasting = $validated['suhu_roasting'][$i] ?? null;
                $fan1 = $validated['fan_1'][$i] ?? null;
                $fan2 = $validated['fan_2'][$i] ?? null;
                $fan3 = $validated['fan_3'][$i] ?? null;
                $fan4 = $validated['fan_4'][$i] ?? null;
                $aktualHumadity = $validated['aktual_humadity'][$i] ?? null;
                $infraRed = $validated['infra_red'][$i] ?? null;

                if (
                    ($suhuRoasting === null || $suhuRoasting === '')
                    && ($fan1 === null || $fan1 === '')
                    && ($fan2 === null || $fan2 === '')
                    && ($fan3 === null || $fan3 === '')
                    && ($fan4 === null || $fan4 === '')
                    && ($aktualHumadity === null || $aktualHumadity === '')
                    && ($infraRed === null || $infraRed === '')
                ) {
                    continue;
                }

                $blokData[] = [
                    'block_number'    => $validated['block_number'][$i] ?? null,
                    'id_suhu_blok'    => $idSuhuBlok,
                    'id_std_fan'      => $validated['id_std_fan'][$i] ?? null,
                    'suhu_roasting'   => $suhuRoasting,
                    'fan_1'           => $fan1,
                    'fan_2'           => $fan2,
                    'fan_3'           => $fan3,
                    'fan_4'           => $fan4,
                    'aktual_humadity' => $aktualHumadity,
                    'infra_red'       => $infraRed,
                ];
            }

            if (empty($blokData)) {
                return response()->json(['success' => false, 'message' => 'Data blok Roasting Fan kosong. Harap isi minimal 1 blok.'], 422);
            }

            // aktual_waktu = nilai dari field "Waktu Pemasakan (pukul)" di form
            // aktual_lama_proses = nilai dari field "Lama Proses (Display Aktual; Menit)" di tabel
            $aktualWaktu     = $request->aktual_waktu ?: null;
            $aktualLamaProses = $request->aktual_lama_proses ?: null;
            $firstBlok = $blokData[0] ?? null;

            $record = ProsesRoastingFan::create([
                'uuid'                => Str::uuid(),
                'id_plan'             => $this->resolvePlanId($user, $validated['id_produk'], $inputRoasting?->id_plan),
                'user_id'             => $user->id,
                'id_produk'           => $validated['id_produk'],
                'id_shift'            => $inputRoasting ? $inputRoasting->shift_id : null,
                'tanggal'             => Carbon::parse($validated['tanggal'])->setTimeFrom(now()),
                'jam'                 => $validated['jam'],
                'waktu_pemasakan'     => $validated['waktu_pemasakan'] ?? null,
                'blok_data'           => $blokData,
                'is_grouped'          => true,
                'aktual_lama_proses'  => $aktualLamaProses,
                'aktual_waktu'        => $aktualWaktu,
                'aktual_suhu_fan'     => $request->aktual_suhu_fan ?: ($firstBlok['suhu_roasting'] ?? null),
                'aktual_speed_fan'    => $request->aktual_speed_fan ?: ($firstBlok['fan_1'] ?? null),
                'input_roasting_uuid' => $validated['input_roasting_uuid'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Roasting Fan berhasil disimpan.',
                'uuid'    => $record->uuid,
                'id_produk' => $record->id_produk
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // STEP 3: HASIL ROASTING (Sensori & Suhu Pusat)
    public function storeHasilRoasting(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'id_produk'         => 'required',
                'id_std_suhu_pusat' => 'required',
                'aktual_suhu_pusat' => 'required',
                'roasting_fan_uuid' => 'required|string|exists:proses_roasting_fan,uuid',
                'tanggal'           => 'required|date',
                'jam'               => 'required|string',
                'keterangan'        => 'nullable|string',
            ]);

            $roastingFan = ProsesRoastingFan::where('uuid', $request->roasting_fan_uuid)->first();

            // Kumpulkan data sensori JSON
            $sensoriData = [
                'sensori_kematangan' => $request->sensori_kematangan,
                'sensori_kenampakan' => $request->sensori_kenampakan,
                'sensori_warna'      => $request->sensori_warna,
                'sensori_rasa'       => $request->sensori_rasa,
                'sensori_bau'        => $request->sensori_bau,
                'sensori_tekstur'    => $request->sensori_tekstur,
            ];

            $aktualSuhuPusat = $request->aktual_suhu_pusat;
            if (is_array($aktualSuhuPusat)) {
                $aktualSuhuPusat = array_values(array_filter($aktualSuhuPusat, function ($val) {
                    return !is_null($val) && trim((string)$val) !== '';
                }));
            } elseif (is_string($aktualSuhuPusat) && str_contains($aktualSuhuPusat, ',')) {
                $aktualSuhuPusat = array_values(array_filter(array_map('trim', explode(',', $aktualSuhuPusat)), function ($val) {
                    return $val !== '';
                }));
            }

            $record = HasilProsesRoasting::create([
                'uuid'                     => Str::uuid(),
                'id_plan'                  => $this->resolvePlanId($user, $request->id_produk, $roastingFan?->id_plan),
                'id_shift'                 => $roastingFan?->id_shift,
                'user_id'                  => $user->id,
                'id_produk'                => $request->id_produk,
                'id_std_suhu_pusat'        => $request->id_std_suhu_pusat,
                'aktual_suhu_pusat'        => $aktualSuhuPusat,
                'sensori'                  => $sensoriData,
                'keterangan'               => $request->keterangan,
                'proses_roasting_fan_uuid' => $request->roasting_fan_uuid,
                'input_roasting_uuid'      => $roastingFan?->input_roasting_uuid,
                'tanggal'                  => Carbon::parse($request->tanggal)->setTimeFrom(now()),
                'jam'                      => $request->jam,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Hasil Roasting berhasil disimpan.',
                'uuid'    => $record->uuid,
                'id_produk' => $record->id_produk // Tambahkan id_produk untuk AJAX step selanjutnya
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // STEP 4: IQF ROASTING (Final)
    public function storeIqfRoasting(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'hasil_roasting_uuid' => 'required|string|exists:hasil_proses_roasting,uuid',
                'suhu_ruang_iqf'      => 'required|string',
                'holding_time'        => 'required|string',
                'tanggal'             => 'required',
                'jam'                 => 'required|string',
            ]);

            $hasilRoasting = HasilProsesRoasting::where('uuid', $request->hasil_roasting_uuid)->first();
            
            // Ambil jam dari input roasting untuk konsistensi
            $inputRoasting = $hasilRoasting ? InputRoasting::where('uuid', $hasilRoasting->input_roasting_uuid)->first() : null;
            $jamToUse = $inputRoasting ? $inputRoasting->jam : $request->jam;

            $record = PembekuanIqfRoasting::create([
                'uuid'                       => Str::uuid(),
                'id_plan'                    => $this->resolvePlanId($user, $hasilRoasting?->id_produk, $hasilRoasting?->id_plan),
                'shift_id'                   => $hasilRoasting?->id_shift,
                'user_id'                    => $user->id,
                'id_produk'                  => $hasilRoasting?->id_produk,
                'input_roasting_uuid'        => $hasilRoasting?->input_roasting_uuid,
                'proses_roasting_fan_uuid'   => $hasilRoasting?->proses_roasting_fan_uuid,
                'hasil_proses_roasting_uuid' => $request->hasil_roasting_uuid,
                'suhu_ruang_iqf'             => $request->suhu_ruang_iqf,
                'holding_time'               => $request->holding_time,
                'tanggal'                    => Carbon::parse($request->tanggal)->format('Y-m-d H:i:s'),
                'jam'                        => $jamToUse,
            ]);

            session()->flash('success', 'Proses 3 selesai! Semua data berhasil disimpan.');

            return response()->json([
                'success'      => true,
                'message'      => 'Proses 3 selesai! Semua data berhasil disimpan.',
                'uuid'         => $record->uuid,
                'redirect_url' => route('wizard.proses3.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($uuid)
    {
        $user = Auth::user();
        $query = PembekuanIqfRoasting::with([
            'user', 'plan', 'shift',
            'inputRoastingData',
            'prosesRoastingFanData.produk',
            'prosesRoastingFanData.suhuBlok',
            'prosesRoastingFanData.stdFan',
            'hasilProsesRoastingData.produk',
            'hasilProsesRoastingData.stdSuhuPusat'
        ])->where('uuid', $uuid);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $pembekuan = $query->firstOrFail();

        if ($redirect = $this->redirectToCanonicalAuditUrl('wizard.proses3.show', $pembekuan)) {
            return $redirect;
        }

        return view('qc-sistem.wizard_proses3.show', compact('pembekuan'));
    }

    public function exportPdf($uuid)
    {
        $user = Auth::user();
        $query = PembekuanIqfRoasting::with([
            'user', 'plan', 'shift',
            'inputRoastingData',
            'prosesRoastingFanData.produk',
            'prosesRoastingFanData.suhuBlok',
            'prosesRoastingFanData.stdFan',
            'hasilProsesRoastingData.produk',
            'hasilProsesRoastingData.stdSuhuPusat'
        ])->where('uuid', $uuid);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $pembekuan = $query->firstOrFail();

        $pdf = Pdf::loadView('qc-sistem.wizard_proses3.export_pdf', compact('pembekuan'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Wizard-Proses3-' . $pembekuan->tanggal . '.pdf');
    }

    public function destroy($uuid)
    {
        $user = Auth::user();
        $query = PembekuanIqfRoasting::where('uuid', $uuid);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $record = $query->firstOrFail();
        $record->delete();

        return redirect()->route('wizard.proses3.index')->with('success', 'Data berhasil dihapus.');
    }

    public function approve(Request $request, $uuid)
    {
        try {
            $user = Auth::user();
            $type = $request->input('type');
            
            // Validate approval type
            if (!in_array($type, ['qc', 'produksi', 'spv'])) {
                return response()->json(['success' => false, 'message' => 'Tipe approval tidak valid'], 400);
            }
            
            // Find the record
            $pembekuan = PembekuanIqfRoasting::where('uuid', $uuid)->firstOrFail();
            
            // Check role permissions based on type
            $userRole = $user->id_role;
            
            if ($type === 'qc' && !in_array($userRole, [1, 3, 5])) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk approval QC'], 403);
            }
            
            if ($type === 'produksi' && !in_array($userRole, [1, 2, 5])) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk approval Produksi'], 403);
            }
            
            if ($type === 'spv' && !in_array($userRole, [1, 4, 5])) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk approval SPV'], 403);
            }
            
            // Check sequential approval requirements
            if ($type === 'produksi' && !$pembekuan->approved_by_qc) {
                return response()->json(['success' => false, 'message' => 'Menunggu approval QC terlebih dahulu'], 403);
            }
            
            if ($type === 'spv' && !$pembekuan->approved_by_produksi) {
                return response()->json(['success' => false, 'message' => 'Menunggu approval Produksi terlebih dahulu'], 403);
            }
            
            // Update approval fields based on type
            switch ($type) {
                case 'qc':
                    if ($pembekuan->approved_by_qc) {
                        return response()->json(['success' => false, 'message' => 'Data sudah di-approve oleh QC'], 400);
                    }
                    $pembekuan->approved_by_qc = true;
                    $pembekuan->qc_approved_by = $user->id;
                    $pembekuan->qc_approved_at = now();
                    break;
                    
                case 'produksi':
                    if ($pembekuan->approved_by_produksi) {
                        return response()->json(['success' => false, 'message' => 'Data sudah di-approve oleh Produksi'], 400);
                    }
                    $pembekuan->approved_by_produksi = true;
                    $pembekuan->produksi_approved_by = $user->id;
                    $pembekuan->produksi_approved_at = now();
                    break;
                    
                case 'spv':
                    if ($pembekuan->approved_by_spv) {
                        return response()->json(['success' => false, 'message' => 'Data sudah di-approve oleh SPV'], 400);
                    }
                    $pembekuan->approved_by_spv = true;
                    $pembekuan->spv_approved_by = $user->id;
                    $pembekuan->spv_approved_at = now();
                    break;
            }
            
            $pembekuan->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil di-approve oleh ' . ucfirst($type)
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function bulkApprove(Request $request)
    {
        try {
            $user = Auth::user();
            $type = $request->input('type');
            $tanggal = $request->input('tanggal');
            $shift_id = $request->input('shift_id');

            if (!in_array($type, ['qc', 'produksi', 'spv'])) {
                return response()->json(['success' => false, 'message' => 'Tipe approval tidak valid'], 400);
            }

            $query = PembekuanIqfRoasting::whereDate('tanggal', $tanggal)
                ->where('shift_id', $shift_id)
                ->whereNotNull('input_roasting_uuid');

            if ($user->role !== 'superadmin') {
                $query->where('id_plan', $user->id_plan);
            }

            $items = $query->get();

            if ($items->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data untuk disetujui'], 404);
            }

            // Sequential check
            if ($type === 'produksi' && $items->contains('approved_by_qc', false)) {
                return response()->json(['success' => false, 'message' => 'Beberapa data belum disetujui oleh QC'], 403);
            }
            if ($type === 'spv' && $items->contains('approved_by_produksi', false)) {
                return response()->json(['success' => false, 'message' => 'Beberapa data belum disetujui oleh Produksi'], 403);
            }

            foreach ($items as $item) {
                if ($type === 'qc' && !$item->approved_by_qc) {
                    $item->approved_by_qc = true;
                    $item->qc_approved_by = $user->id;
                    $item->qc_approved_at = now();
                } elseif ($type === 'produksi' && !$item->approved_by_produksi) {
                    $item->approved_by_produksi = true;
                    $item->produksi_approved_by = $user->id;
                    $item->produksi_approved_at = now();
                } elseif ($type === 'spv' && !$item->approved_by_spv) {
                    $item->approved_by_spv = true;
                    $item->spv_approved_by = $user->id;
                    $item->spv_approved_at = now();
                }
                $item->save();
            }

            return response()->json(['success' => true, 'message' => 'Bulk approval berhasil (' . strtoupper($type) . ')']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function bulkApproveSelected(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv',
                'uuids' => 'required|array|min:1',
                'uuids.*' => 'required|uuid'
            ]);

            $user = Auth::user();
            $type = $request->input('type');
            $uuids = $request->input('uuids', []);

            // Role-based permission check
            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5]
            ];

            if (!in_array($user->id_role, $allowedRoles[$type] ?? [])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki otoritas untuk verifikasi ini.'
                ], 403);
            }

            // Find records - ONLY unapproved ones (smart auto-skip)
            $query = PembekuanIqfRoasting::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            $itemsToUpdate = $query->get();
            $count = $itemsToUpdate->count();

            if ($count === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Semua data sudah diverifikasi sebelumnya.'
                ]);
            }

            // Update operasional records
            $updateData = [
                "approved_by_{$type}" => true,
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now(),
            ];

            PembekuanIqfRoasting::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            // Find all source_uuids from the updated records
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();
            
            // Update audit copies with same approval data
            PembekuanIqfRoasting::where('is_audit', true)
                ->whereIn('source_uuid', $updatedSourceUuids)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => "Berhasil verifikasi {$count} data " . strtoupper($type)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function edit($uuid)
    {
        $user = Auth::user();
        $query = PembekuanIqfRoasting::with([
            'inputRoastingData',
            'prosesRoastingFanData',
            'hasilProsesRoastingData'
        ])->where('uuid', $uuid);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $pembekuan = $query->firstOrFail();

        $isAuditMode = (bool) $pembekuan->is_audit;
        $queryParameters = request()->query();
        $hasAuditFlag = array_key_exists('is_audit', $queryParameters);

        if (($isAuditMode && request()->query('is_audit') !== '1') || (!$isAuditMode && $hasAuditFlag)) {
            unset($queryParameters['is_audit'], $queryParameters['uuid']);
            if ($isAuditMode) {
                $queryParameters['is_audit'] = '1';
            }

            return redirect()->route(
                'wizard.proses3.edit',
                array_merge(['uuid' => $pembekuan->uuid], $queryParameters)
            );
        }

        // Cek batas waktu edit untuk role yang dibatasi (global config)
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 8);
            $createdAt = Carbon::parse($pembekuan->created_at);
            $hoursElapsed = $createdAt->diffInHours(now(), true);

            if ($hoursElapsed > $timeLimitHours) {
                return redirect()
                    ->route($isAuditMode ? 'wizard.proses3.audit' : 'wizard.proses3.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        $produks = JenisProduk::where('id_plan', $pembekuan->id_plan)->orderBy('nama_produk', 'asc')->get();
        $shifts = DataShift::where('id_plan', $pembekuan->id_plan)->get();
        $suhuBloks = SuhuBlok::all();
        $stdFans = StdFan::all();
        $stdSuhuPusat = StdSuhuPusatRoasting::all();

        return view('qc-sistem.wizard_proses3.edit', compact('pembekuan', 'produks', 'shifts', 'suhuBloks', 'stdFans', 'stdSuhuPusat'));
    }

    public function update(Request $request, $uuid)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'tanggal' => 'required',
            ]);

            $pembekuan = PembekuanIqfRoasting::where('uuid', $uuid)->firstOrFail();
            $isAuditMode = (bool) $pembekuan->is_audit;

            // Cek batas waktu edit untuk role yang dibatasi (global config)
            $user = Auth::user();
            $restrictedRoles = config('edit_limit.restricted_role_ids', []);
            if (in_array($user->id_role, $restrictedRoles)) {
                $timeLimitHours = config('edit_limit.time_limit_hours', 8);
                $createdAt = Carbon::parse($pembekuan->created_at);
                $hoursElapsed = $createdAt->diffInHours(now(), true);

                if ($hoursElapsed > $timeLimitHours) {
                    DB::rollBack();

                    return redirect()
                        ->route($isAuditMode ? 'wizard.proses3.audit' : 'wizard.proses3.index')
                        ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
                }
            }

            // 1. Update Info Utama & IQF
            $pembekuan->update([
                'tanggal'        => Carbon::parse($request->tanggal)->format('Y-m-d H:i:s'),
                'jam'            => $request->jam ?? $pembekuan->jam,
                'suhu_ruang_iqf' => $request->suhu_ruang_iqf,
                'holding_time'   => $request->holding_time,
            ]);

            // 2. Update Input Roasting (Bahan Baku) - JANGAN UPDATE JAM karena bisa di-share
            if ($pembekuan->inputRoastingData) {
                $pembekuan->inputRoastingData->update([
                    'shift_id'            => $request->id_shift,
                    'kode_produksi'       => $request->kode_produksi,
                    'berat_produk'        => $request->berat_bahan,
                    'std_suhu_sebelum'    => $request->std_suhu_sebelum,
                    'aktual_suhu_sesudah' => $request->aktual_suhu_sesudah,
                    // Jangan update jam karena InputRoasting bisa di-share oleh banyak PembekuanIqfRoasting
                    // jam field TIDAK di-update untuk menjaga integritas data yang sudah ada
                ]);
            }

            // 3. Update Roasting Fan (Grouped per Blok)
            if ($pembekuan->prosesRoastingFanData) {
                $blockNumbers = $request->input('block_number', []);
                $infeedIdx = array_search('INFEED', $blockNumbers);
                $outfeedIdx = array_search('OUTFEED', $blockNumbers);

                $aktualHum = $request->input('aktual_humadity', []);
                $infraRedArr = $request->input('infra_red', []);

                if ($request->filled('aktual_humadity_infeed_outfeed')) {
                    $valH = $request->input('aktual_humadity_infeed_outfeed');
                    if ($infeedIdx !== false) $aktualHum[$infeedIdx] = $valH;
                    if ($outfeedIdx !== false) $aktualHum[$outfeedIdx] = $valH;
                }

                if ($request->filled('infra_red_infeed_outfeed')) {
                    $valI = $request->input('infra_red_infeed_outfeed');
                    if ($infeedIdx !== false) $infraRedArr[$infeedIdx] = $valI;
                    if ($outfeedIdx !== false) $infraRedArr[$outfeedIdx] = $valI;
                }

                $blokData = [];
                if ($request->has('block_number')) {
                    foreach ($request->block_number as $i => $blockNum) {
                        $blokData[] = [
                            'block_number'    => $blockNum,
                            'id_suhu_blok'    => $request->id_suhu_blok[$i] ?? null,
                            'id_std_fan'      => $request->id_std_fan[$i] ?? null,
                            'suhu_roasting'   => $request->suhu_roasting[$i] ?? null,
                            'fan_1'           => $request->fan_1[$i] ?? null,
                            'fan_2'           => $request->fan_2[$i] ?? null,
                            'fan_3'           => $request->fan_3[$i] ?? null,
                            'fan_4'           => $request->fan_4[$i] ?? null,
                            'aktual_humadity' => $aktualHum[$i] ?? null,
                            'infra_red'       => $infraRedArr[$i] ?? null,
                        ];
                    }
                }

                $pembekuan->prosesRoastingFanData->update([
                    'jam'                => $request->jam_roasting ?? $pembekuan->prosesRoastingFanData->jam,
                    'waktu_pemasakan'    => $request->waktu_pemasakan_roasting,
                    'aktual_waktu'       => $request->aktual_waktu,
                    'aktual_lama_proses' => $request->aktual_lama_proses_roasting,
                    'blok_data'          => $blokData,
                ]);
            }

            // 4. Update Hasil Roasting
            if ($pembekuan->hasilProsesRoastingData) {
                $sensoriData = [
                    'sensori_kematangan' => $request->sensori_kematangan,
                    'sensori_kenampakan' => $request->sensori_kenampakan,
                    'sensori_warna'      => $request->sensori_warna,
                    'sensori_rasa'       => $request->sensori_rasa,
                    'sensori_bau'        => $request->sensori_bau,
                    'sensori_tekstur'    => $request->sensori_tekstur,
                ];

                $asp = $request->aktual_suhu_pusat;
                if (is_array($asp)) {
                    $asp = array_values(array_filter($asp, function ($val) {
                        return !is_null($val) && trim((string)$val) !== '';
                    }));
                } elseif (is_string($asp) && str_contains($asp, ',')) {
                    $asp = array_values(array_filter(array_map('trim', explode(',', $asp)), function ($val) {
                        return $val !== '';
                    }));
                }

                $pembekuan->hasilProsesRoastingData->update([
                    'jam'               => $request->jam_hasil ?? $pembekuan->hasilProsesRoastingData->jam,
                    'id_std_suhu_pusat' => $request->id_std_suhu_pusat,
                    'aktual_suhu_pusat' => $asp,
                    'sensori'           => $sensoriData,
                    'keterangan'        => $request->keterangan,
                ]);
            }

            DB::commit();
            return redirect()
                ->route($isAuditMode ? 'wizard.proses3.audit' : 'wizard.proses3.index')
                ->with('success', $isAuditMode ? 'Data audit berhasil diperbarui.' : 'Data berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    protected function auditRelations(): array
    {
        return [
            'hasilProsesRoastingData',
            'hasilProsesRoastingData.produk',
            'inputRoastingData',
            'shift'
        ];
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.wizard_proses3';
    }

    public function auditIndex(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $auditedSourceUuids = PembekuanIqfRoasting::where('is_audit', true)->whereNotNull('source_uuid')->pluck('source_uuid')->toArray();

$query = PembekuanIqfRoasting::with($this->auditRelations())
            ->where(function ($q) use ($auditedSourceUuids) {
            $q->where('is_audit', true)
              ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                  $subQ->where('is_audit', false);
                  if (!empty($auditedSourceUuids)) {
                      $subQ->whereNotIn('uuid', $auditedSourceUuids);
                  }
              });
        })
            ->whereNotNull('input_roasting_uuid')
            ->whereNull('penggorengan_uuid')
            ->when($user->role !== 'superadmin', function ($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('hasilProsesRoastingData.produk', function($qp) use ($search) {
                    $qp->where('nama_produk', 'LIKE', "%{$search}%");
                })->orWhereHas('inputRoastingData', function($qi) use ($search) {
                    $qi->where('kode_produksi', 'LIKE', "%{$search}%");
                });
            });
        }

        $this->applyDateRangeFilter(request(), $query);
        $records = $query->orderByRaw('DATE(tanggal) DESC')
            ->orderByRaw('TIME(jam) DESC')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        $groupedData = $records->getCollection()->groupBy(function ($item) {
            $dateStr = $item->tanggal
                ? (is_string($item->tanggal)
                    ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d')
                    : $item->tanggal->format('Y-m-d'))
                : 'no_date';
            $shiftId = $item->shift_id ?? $item->getShiftDataAttribute()->id ?? 'no_shift';
            return $dateStr . '_' . $shiftId;
        });

        $records->setCollection($groupedData);

        return view("{$this->auditViewPrefix()}.index_audit", compact('records', 'search', 'perPage'));
    }

    public function auditSearchAjax(Request $request)
    {
        try {
            $user = auth()->user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 5);
            $page = (int) $request->get('page', 1);

            $query = PembekuanIqfRoasting::with($this->auditRelations())
                ->where('is_audit', true)
                ->whereNotNull('input_roasting_uuid')
                ->whereNull('penggorengan_uuid')
                ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan));

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('hasilProsesRoastingData.produk', function($qp) use ($search) {
                        $qp->where('nama_produk', 'like', "%{$search}%");
                    })->orWhereHas('inputRoastingData', function($qi) use ($search) {
                        $qi->where('kode_produksi', 'like', "%{$search}%");
                    });
                });
            }

            $records = $query->orderByRaw('DATE(tanggal) DESC')
                ->orderByRaw('TIME(jam) DESC')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $total = $records->total();
            $groupedData = $records->getCollection()->groupBy(fn($item) =>
                ($item->tanggal ? (is_string($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : $item->tanggal->format('Y-m-d')) : 'no_date') . '_' . ($item->shift_id ?? 'no_shift')
            );
            $records->setCollection($groupedData);

            return response()->json([
                'html' => view('qc-sistem.wizard_proses3._table', compact('records'))->render(),
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => (int) ceil($total / $perPage),
                    'per_page' => $perPage,
                    'total' => $total,
                    'from' => $records->firstItem(),
                    'to' => $records->lastItem(),
                    'has_pages' => $total > $perPage,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function copyToAudit($uuid)
    {
        $item = PembekuanIqfRoasting::where('uuid', $uuid)->firstOrFail();

        // Jika item yang diklik sudah merupakan record audit
        if ($item->is_audit) {
            return redirect()->route('wizard.proses3.edit', $item->uuid);
        }

        // Jika data operasional ini sudah memiliki versi audit sebelumnya
        if ($item->auditVersion) {
            return redirect()->route('wizard.proses3.edit', $item->auditVersion->uuid);
        }

        // Jika belum memiliki versi audit, buatkan salinan audit secara otomatis
        $clone = $item->copyToAudit();

        return redirect()->route('wizard.proses3.edit', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }
}
