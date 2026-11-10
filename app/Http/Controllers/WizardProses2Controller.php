<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WizardProses2Export;

use App\Models\JenisProduk;
use App\Models\DataShift;
use App\Models\JenisBetter;
use App\Models\JenisBreader;
use App\Models\PembuatanPredust;
use App\Models\JenisPredust;
use App\Models\SuhuFrayer1;
use App\Models\SuhuFrayer2;
use App\Models\WaktuPenggorengan;
use App\Models\WaktuPenggorengan2;
use App\Models\StdSuhuPusat;
use App\Models\StdSuhuPusatRoasting;
use App\Models\SuhuBlok;
use App\Models\StdFan;

use App\Models\Penggorengan;
use App\Models\ProsesBattering;
use App\Models\ProsesBreader;
use App\Models\ProsesFrayer;
use App\Models\Frayer2;
use App\Models\Frayer3;
use App\Models\Frayer4;
use App\Models\Frayer5;
use App\Models\ProsesRoastingFan;
use App\Models\HasilProsesRoasting;
use App\Models\PembekuanIqfRoasting;
use App\Http\Controllers\Traits\HasAuditController;

class WizardProses2Controller extends Controller
{
    use HasAuditController;

    protected function auditModel(): string
    {
        return PembekuanIqfRoasting::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'wizard.proses2';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.wizard_proses2';
    }

    protected function auditRelations(): array
    {
        return [
            'hasilProsesRoastingData',
            'hasilProsesRoastingData.produk',
            'penggorenganData',
            'shift'
        ];
    }

    protected function auditSearchColumns(): array
    {
        return ['kode_produksi'];
    }

    protected function auditDateColumn(): string
    {
        return 'tanggal';
    }

    protected function auditTimeColumn(): string
    {
        return 'jam';
    }

    protected function auditShiftColumn(): string
    {
        return 'shift_id';
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->id_role == 6) {
            return redirect()->route('wizard.proses2.audit');
        }
        $search = $request->input('search');

        $query = PembekuanIqfRoasting::operasional()->with([
            'hasilProsesRoastingData',
            'hasilProsesRoastingData.produk',
            'hasilProsesRoastingData.stdSuhuPusat',
            'penggorenganData',
            'shift',
            'predustData',
            'predustData.jenisPredust',
            'batteringData',
            'breaderData',
            'prosesRoastingFanData',
            'frayer2',
            'frayer2.suhuFrayer2',
            'frayer2.waktuPenggorengan2',
            'frayer3',
            'frayer3.suhuFrayer',
            'frayer4',
            'frayer4.suhuFrayer',
            'frayer5',
            'frayer5.suhuFrayer',
        ])->whereNotNull('penggorengan_uuid'); // Filter khusus alur Proses 2 (dari Penggorengan)

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('hasilProsesRoastingData.produk', function($qp) use ($search) {
                    $qp->where('nama_produk', 'LIKE', "%{$search}%");
                })->orWhereHas('penggorenganData', function($qg) use ($search) {
                    $qg->where('kode_produksi', 'LIKE', "%{$search}%");
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
        });

        $pembekuanData->setCollection($groupedData);

        return view('qc-sistem.wizard_proses2.index', compact('pembekuanData'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();
        $continueUuid = $request->query('continue_uuid');
        $draftData = null;

        if ($continueUuid) {
            $draftData = Penggorengan::where('uuid', $continueUuid)->first();
        }

        if ($user->role === 'superadmin') {
            $produk        = JenisProduk::all();
            $shift         = DataShift::all();
            $predustMaster = JenisPredust::all();
            $better        = JenisBetter::all();
            $breader       = JenisBreader::all();
            $stdSuhuPusat  = StdSuhuPusat::all();
        } else {
            $produk        = JenisProduk::where('id_plan', $user->id_plan)->get();
            $shift         = DataShift::where('id_plan', $user->id_plan)->get();
            $predustMaster = JenisPredust::where('id_plan', $user->id_plan)->get();
            $better        = JenisBetter::where('id_plan', $user->id_plan)->get();
            $breader       = JenisBreader::where('id_plan', $user->id_plan)->get();
            $stdSuhuPusat  = StdSuhuPusat::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.wizard_proses2.create', compact('user', 'now', 'produk', 'shift', 'predustMaster', 'better', 'breader', 'stdSuhuPusat', 'draftData'));
    }

    // STEP 1: PENGGORENGAN
    public function storePenggorengan(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'id_produk'        => 'required|exists:jenis_produk,id',
                'id_shift'         => 'required|exists:data_shift,id',
                'kode_produksi'    => 'required|string',
                'berat_produk'     => 'required|string',
                'no_of_strokes'    => 'required|string',
                'waktu_pemasakan'  => 'required|string',
                'jam'              => 'required|string',
                'tanggal'          => 'required|date',
                'hasil_pencetakan' => 'nullable|string',
            ]);

            $record = Penggorengan::create([
                'uuid'             => Str::uuid(),
                'id_produk'        => $request->id_produk,
                'shift_id'         => $request->id_shift,
                'id_plan'          => $this->resolvePlanId($user, $request->id_produk),
                'user_id'          => $user->id,
                'kode_produksi'    => $request->kode_produksi,
                'berat_produk'     => $request->berat_produk,
                'no_of_strokes'    => $request->no_of_strokes,
                'waktu_pemasakan'  => $request->waktu_pemasakan,
                'jam'              => $request->jam,
                'tanggal'          => Carbon::parse($request->tanggal)->setTimeFrom(now()),
                'hasil_pencetakan' => $request->hasil_pencetakan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Penggorengan berhasil disimpan.',
                'uuid'    => $record->uuid,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // STEP 2: PREDUST
    public function storePredust(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'id_produk'         => 'required',
                'id_jenis_predust'  => 'required',
                'kode_produksi'     => 'required|string',
                'hasil_predust'     => 'required|string',
                'tanggal'           => 'required|date',
                'jam'               => 'required|string',
                'penggorengan_uuid' => 'required|string|exists:penggorengan,uuid',
            ]);

            // Ambil hasil_pencetakan dari tabel penggorengan karena diperlukan oleh tabel predust
            $penggorengan = \App\Models\Penggorengan::where('uuid', $request->penggorengan_uuid)->first();

            $record = PembuatanPredust::create([
                'uuid'              => Str::uuid(),
                'id_plan'           => $this->resolvePlanId($user, $request->id_produk, $penggorengan?->id_plan),
                'user_id'           => $user->id,
                'id_produk'         => $request->id_produk,
                'id_jenis_predust'  => $request->id_jenis_predust,
                'kode_produksi'     => $request->kode_produksi,
                'kondisi_predust'   => $request->hasil_predust, // Kolom database menggunakan kondisi_predust
                'hasil_pencetakan'  => $penggorengan?->hasil_pencetakan ?: '✔',
                'tanggal'           => Carbon::parse($request->tanggal)->setTimeFrom(now()),
                'jam'               => $request->jam,
                'penggorengan_uuid' => $request->penggorengan_uuid,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Predust berhasil disimpan.',
                'uuid'    => $record->uuid,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // STEP 3: BATTERING
    public function storeBattering(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'id_produk'            => 'required',
                'id_jenis_better'      => 'required|array',
                'id_jenis_better.*'    => 'exists:jenis_better,id',
                'kode_produksi_better' => 'required|array',
                'hasil_better'         => 'required|string',
                'tanggal'              => 'required|date',
                'jam'                  => 'required|string',
                'predust_uuid'         => 'nullable|string|exists:pembuatan_predust,uuid',
                'penggorengan_uuid'    => 'nullable|string|exists:penggorengan,uuid',
            ]);

            $predust = $request->predust_uuid ? PembuatanPredust::where('uuid', $request->predust_uuid)->first() : null;

            $jenisBetter = is_array($request->id_jenis_better)
                ? implode(',', $request->id_jenis_better)
                : $request->id_jenis_better;

            $record = ProsesBattering::create([
                'uuid'                 => Str::uuid(),
                'id_plan'              => $this->resolvePlanId($user, $request->id_produk, $predust?->id_plan),
                'user_id'              => $user->id,
                'id_produk'            => $request->id_produk,
                'id_jenis_better'      => $jenisBetter,
                'kode_produksi_better' => $request->kode_produksi_better,
                'hasil_better'         => $request->hasil_better,
                'tanggal'              => Carbon::parse($request->tanggal)->setTimeFrom(now()),
                'jam'                  => $request->jam,
                'predust_uuid'         => $request->predust_uuid,
                'penggorengan_uuid'    => $predust?->penggorengan_uuid ?? $request->penggorengan_uuid,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Battering berhasil disimpan.',
                'uuid'    => $record->uuid,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // STEP 4: BREADER
    public function storeBreader(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'id_produk'             => 'required',
                'id_jenis_breader'      => 'required|array',
                'id_jenis_breader.*'    => 'exists:jenis_breader,id',
                'kode_produksi_breader' => 'required|array',
                'hasil_breader'         => 'required|string',
                'tanggal'               => 'required|date',
                'jam'                   => 'required|string',
                'battering_uuid'        => 'required|string|exists:proses_battering,uuid',
            ]);

            $battering = ProsesBattering::where('uuid', $request->battering_uuid)->first();

            $jenisBreader = is_array($request->id_jenis_breader)
                ? implode(',', $request->id_jenis_breader)
                : $request->id_jenis_breader;

            $record = ProsesBreader::create([
                'uuid'              => Str::uuid(),
                'id_plan'           => $this->resolvePlanId($user, $request->id_produk, $battering?->id_plan),
                'user_id'           => $user->id,
                'id_produk'         => $request->id_produk,
                'id_jenis_breader'  => $jenisBreader,
                'kode_produksi'     => $request->kode_produksi_breader,
                'hasil_breader'     => $request->hasil_breader,
                'tanggal'           => Carbon::parse($request->tanggal)->setTimeFrom(now()),
                'jam'               => $request->jam,
                'battering_uuid'    => $request->battering_uuid,
                'predust_uuid'      => $battering?->predust_uuid,
                'penggorengan_uuid' => $battering?->penggorengan_uuid,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Breader berhasil disimpan.',
                'uuid'    => $record->uuid,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // STEP 5: FRAYER
    public function storeFrayer(Request $request)
    {
        try {
            $user         = Auth::user();
            $frayerNumber = (int) $request->frayer_number;

            if (!in_array($frayerNumber, [1, 2, 3, 4, 5])) {
                return response()->json(['success' => false, 'message' => 'Nomor frayer tidak valid.'], 422);
            }

            $request->validate([
                'frayer_number'            => 'required|integer|between:1,5',
                'id_produk'                => 'required',
                'tanggal'                  => 'required|date',
                'jam'                      => 'required|string',
                'aktual_penggorengan'      => 'required|string',
                'aktual_suhu_penggorengan' => 'nullable|string',
                'tpm_minyak'               => 'required|string',
                'breader_uuid'             => 'required|string|exists:proses_breader,uuid',
                'keterangan'               => 'nullable|string',
            ]);

            $breader = ProsesBreader::where('uuid', $request->breader_uuid)->first();

            $baseData = [
                'id_plan'                  => $this->resolvePlanId($user, $request->id_produk, $breader?->id_plan),
                'user_id'                  => $user->id,
                'id_produk'                => $request->id_produk,
                'tanggal'                  => Carbon::parse($request->tanggal)->setTimeFrom(now()),
                'jam'                      => $request->jam,
                'aktual_penggorengan'      => $request->aktual_penggorengan,
                'aktual_suhu_penggorengan' => $request->aktual_suhu_penggorengan,
                'tpm_minyak'               => $request->tpm_minyak,
                'keterangan'               => $request->keterangan,
                'breader_uuid'             => $request->breader_uuid,
                'battering_uuid'           => $breader?->battering_uuid,
                'predust_uuid'             => $breader?->predust_uuid,
                'penggorengan_uuid'        => $breader?->penggorengan_uuid,
            ];

            if ($request->prev_frayer_uuid) {
                $baseData['frayer_uuid'] = $request->prev_frayer_uuid;
            }

            switch ($frayerNumber) {
                case 1:
                    $request->validate(['id_suhu_frayer_1' => 'required', 'id_waktu_penggorengan' => 'required']);
                    $baseData['id_suhu_frayer_1']       = $request->id_suhu_frayer_1;
                    $baseData['id_waktu_penggorengan']  = $request->id_waktu_penggorengan;
                    $record = ProsesFrayer::create($baseData);
                    break;
                case 2:
                    $request->validate(['id_suhu_frayer_2' => 'required', 'id_waktu_penggorengan_2' => 'required']);
                    $baseData['id_suhu_frayer_2']         = $request->id_suhu_frayer_2;
                    $baseData['id_waktu_penggorengan_2']  = $request->id_waktu_penggorengan_2;
                    $record = Frayer2::create($baseData);
                    break;
                case 3:
                    $request->validate(['id_suhu_frayer_3' => 'required', 'id_waktu_penggorengan_3' => 'required']);
                    $baseData['id_suhu_frayer']         = $request->id_suhu_frayer_3;
                    $baseData['id_waktu_penggorengan']  = $request->id_waktu_penggorengan_3;
                    $record = Frayer3::create($baseData);
                    break;
                case 4:
                    $request->validate(['id_suhu_frayer_4' => 'required', 'id_waktu_penggorengan_4' => 'required']);
                    $baseData['id_suhu_frayer']         = $request->id_suhu_frayer_4;
                    $baseData['id_waktu_penggorengan']  = $request->id_waktu_penggorengan_4;
                    $record = Frayer4::create($baseData);
                    break;
                case 5:
                    $request->validate(['id_suhu_frayer_5' => 'required', 'id_waktu_penggorengan_5' => 'required']);
                    $baseData['id_suhu_frayer']         = $request->id_suhu_frayer_5;
                    $baseData['id_waktu_penggorengan']  = $request->id_waktu_penggorengan_5;
                    $record = Frayer5::create($baseData);
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => "Data Frayer {$frayerNumber} berhasil disimpan.",
                'uuid'    => $record->uuid,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // STEP 6: ROASTING FAN
    public function storeRoastingFan(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'tanggal'           => 'required|date',
                'jam'               => 'required|string',
                'waktu_pemasakan'   => 'nullable|string',
                'id_produk'         => 'required',
                'penggorengan_uuid' => 'required|string|exists:penggorengan,uuid',

                // Data blok (samakan seperti form asli)
                'id_suhu_blok'      => 'required|array',
                'id_suhu_blok.*'    => 'required',
                'id_std_fan'        => 'required|array',
                'id_std_fan.*'      => 'required',
                'block_number'      => 'required|array',
                'block_number.*'    => 'required',
                'suhu_roasting'     => 'required|array',
                'fan_1'             => 'required|array',
                'fan_2'             => 'required|array',
                'fan_3'             => 'nullable|array',
                'fan_4'             => 'nullable|array',
                'aktual_humadity'   => 'nullable|array',
                'infra_red'         => 'nullable|array',

                // UUID relasi proses sebelumnya
                'frayer_uuid'       => 'nullable|string',
                'breader_uuid'      => 'nullable|string',
                'battering_uuid'    => 'nullable|string',
                'predust_uuid'      => 'nullable|string',
            ]);

            $penggorengan = Penggorengan::where('uuid', $validated['penggorengan_uuid'])->first();

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

            // Build blok_data JSON (skip blok yang benar-benar kosong)
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
                    'block_number'     => $validated['block_number'][$i] ?? null,
                    'id_suhu_blok'      => $idSuhuBlok,
                    'id_std_fan'        => $validated['id_std_fan'][$i] ?? null,
                    'suhu_roasting'     => $suhuRoasting,
                    'fan_1'             => $fan1,
                    'fan_2'             => $fan2,
                    'fan_3'             => $fan3,
                    'fan_4'             => $fan4,
                    'aktual_humadity'   => $aktualHumadity,
                    'infra_red'         => $infraRed,
                ];
            }

            if (empty($blokData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data blok Roasting Fan kosong. Harap isi minimal 1 blok.',
                ], 422);
            }

            // aktual_waktu = nilai dari field "Waktu Pemasakan (pukul)" di form
            // aktual_lama_proses = nilai dari field "Lama Proses (Display Aktual; Menit)" di tabel
            $aktualWaktu      = $request->aktual_waktu ?: null;
            $aktualLamaProses = $request->aktual_lama_proses ?: null;

            // ambil value ringkas untuk backward compatibility
            $firstBlok = $blokData[0] ?? null;

            $record = ProsesRoastingFan::create([
                'uuid'               => Str::uuid(),
                'id_plan'            => $this->resolvePlanId($user, $validated['id_produk'], $penggorengan?->id_plan),
                'user_id'            => $user->id,
                'id_produk'          => $validated['id_produk'],
                'id_shift'           => $penggorengan ? $penggorengan->shift_id : 1,
                'tanggal'            => Carbon::parse($validated['tanggal'])->setTimeFrom(now()),
                'jam'                => $validated['jam'],
                'waktu_pemasakan'    => $validated['waktu_pemasakan'] ?? null,

                'blok_data'          => $blokData,
                'is_grouped'         => true,
                'aktual_lama_proses' => $aktualLamaProses,

                // keep old fields filled (optional)
                'aktual_waktu'       => $aktualWaktu,
                'aktual_suhu_fan'    => $request->aktual_suhu_fan ?: ($firstBlok['suhu_roasting'] ?? null),
                'aktual_speed_fan'   => $request->aktual_speed_fan ?: ($firstBlok['fan_1'] ?? null),

                // UUID relasi
                'frayer_uuid'        => $validated['frayer_uuid'] ?? null,
                'frayer2_uuid'       => $request->frayer2_uuid ?? null,
                'frayer3_uuid'       => $request->frayer3_uuid ?? null,
                'frayer4_uuid'       => $request->frayer4_uuid ?? null,
                'frayer5_uuid'       => $request->frayer5_uuid ?? null,
                'breader_uuid'       => $validated['breader_uuid'] ?? null,
                'battering_uuid'     => $validated['battering_uuid'] ?? null,
                'predust_uuid'       => $validated['predust_uuid'] ?? null,
                'penggorengan_uuid'  => $validated['penggorengan_uuid'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Roasting Fan berhasil disimpan.',
                'uuid'    => $record->uuid,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // STEP 7: HASIL ROASTING
    public function storeHasilRoasting(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'id_produk'           => 'required',
                'id_std_suhu_pusat'   => 'required',
                'aktual_suhu_pusat'   => 'required',
                'tanggal'             => 'required|date',
                'jam'                 => 'required|string',
                'sensori_kematangan'  => 'nullable|in:✔,✘',
                'sensori_kenampakan'  => 'nullable|in:✔,✘',
                'sensori_warna'       => 'nullable|in:✔,✘',
                'sensori_rasa'        => 'nullable|in:✔,✘',
                'sensori_bau'         => 'nullable|in:✔,✘',
                'sensori_tekstur'     => 'nullable|in:✔,✘',
                'keterangan'         => 'nullable|string',
                'roasting_fan_uuid'   => 'required|string|exists:proses_roasting_fan,uuid',

                // UUID relasi (optional, tapi akan diisi dari chain bila tidak dikirim)
                'frayer_uuid'         => 'nullable|string',
                'breader_uuid'        => 'nullable|string',
                'battering_uuid'      => 'nullable|string',
                'predust_uuid'        => 'nullable|string',
            ]);

            $roastingFan = ProsesRoastingFan::where('uuid', $request->roasting_fan_uuid)->first();

            // Kumpulkan data sensori ke dalam satu array (seperti form asli)
            $sensoriData = [
                'sensori_kematangan' => $request->sensori_kematangan,
                'sensori_kenampakan' => $request->sensori_kenampakan,
                'sensori_warna'      => $request->sensori_warna,
                'sensori_rasa'       => $request->sensori_rasa,
                'sensori_bau'        => $request->sensori_bau,
                'sensori_tekstur'    => $request->sensori_tekstur,
            ];

            // Karena database kolom 'aktual_suhu_pusat' adalah JSON, simpan sebagai array
            $aktualSuhuPusat = is_array($request->aktual_suhu_pusat)
                ? array_values(array_filter(array_map('trim', $request->aktual_suhu_pusat), fn($v) => $v !== '' && !is_null($v)))
                : (is_string($request->aktual_suhu_pusat) ? array_values(array_filter(array_map('trim', explode(',', $request->aktual_suhu_pusat)), fn($v) => $v !== '' && !is_null($v))) : [$request->aktual_suhu_pusat]);

            $record = HasilProsesRoasting::create([
                'uuid'               => Str::uuid(),
                'id_plan'            => $this->resolvePlanId($user, $request->id_produk, $roastingFan?->id_plan),
                'id_shift'           => $roastingFan?->id_shift,
                'user_id'            => $user->id,
                'id_produk'          => $request->id_produk,
                'id_std_suhu_pusat'  => $request->id_std_suhu_pusat,
                'aktual_suhu_pusat'  => $aktualSuhuPusat,
                'sensori'            => $sensoriData, // Simpan sebagai JSON
                'tanggal'            => Carbon::parse($request->tanggal)->setTimeFrom(now()),
                'jam'                => $request->jam,
                'keterangan' => $request->keterangan,
'proses_roasting_fan_uuid' => $request->roasting_fan_uuid,
                'penggorengan_uuid'  => $roastingFan?->penggorengan_uuid,

                // UUID relasi proses sebelumnya (samakan seperti form asli)
                'frayer_uuid'         => $request->frayer_uuid ?: ($roastingFan?->frayer_uuid),
                'frayer2_uuid'        => $request->frayer2_uuid ?: ($roastingFan?->frayer2_uuid),
                'frayer3_uuid'        => $request->frayer3_uuid ?: ($roastingFan?->frayer3_uuid),
                'frayer4_uuid'        => $request->frayer4_uuid ?: ($roastingFan?->frayer4_uuid),
                'frayer5_uuid'        => $request->frayer5_uuid ?: ($roastingFan?->frayer5_uuid),
                'breader_uuid'        => $request->breader_uuid ?: ($roastingFan?->breader_uuid),
                'battering_uuid'      => $request->battering_uuid ?: ($roastingFan?->battering_uuid),
                'predust_uuid'        => $request->predust_uuid ?: ($roastingFan?->predust_uuid),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Hasil Roasting berhasil disimpan.',
                'uuid'    => $record->uuid,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // STEP 8: IQF ROASTING
    public function storeIqfRoasting(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'tanggal'              => 'required|date',
                'jam'                  => 'required|string',
                'suhu_ruang_iqf'       => 'required|string',
                'holding_time'         => 'required|string',
                'hasil_roasting_uuid'  => 'required|string|exists:hasil_proses_roasting,uuid',
            ]);

            $hasilRoasting = HasilProsesRoasting::where('uuid', $request->hasil_roasting_uuid)->first();

            // Resolve battering/breader/predust UUIDs with full fallback chain:
            // 1. What the client sent in the request
            // 2. What's stored on HasilProsesRoasting (saved at step 7)
            // 3. What's stored on ProsesRoastingFan (saved at step 6)
            // This makes step 8 resilient to sessionStorage loss on the client
            $roastingFan = null;
            if ($hasilRoasting?->proses_roasting_fan_uuid) {
                $roastingFan = \App\Models\ProsesRoastingFan::where('uuid', $hasilRoasting->proses_roasting_fan_uuid)->first();
            }

            $batteringUuid = $request->battering_uuid
                ?: $hasilRoasting?->battering_uuid
                ?: $roastingFan?->battering_uuid;

            $breaderUuid = $request->breader_uuid
                ?: $hasilRoasting?->breader_uuid
                ?: $roastingFan?->breader_uuid;

            $predustUuid = $request->predust_uuid
                ?: $hasilRoasting?->predust_uuid
                ?: $roastingFan?->predust_uuid;

            // Ambil semua UUID frayer yang mungkin diisi
            $frayerUuids = [];
            if ($request->frayer_uuid) $frayerUuids[] = $request->frayer_uuid;
            if ($request->frayer2_uuid) $frayerUuids[] = $request->frayer2_uuid;
            if ($request->frayer3_uuid) $frayerUuids[] = $request->frayer3_uuid;
            if ($request->frayer4_uuid) $frayerUuids[] = $request->frayer4_uuid;
            if ($request->frayer5_uuid) $frayerUuids[] = $request->frayer5_uuid;

            // Jika tidak ada di request, ambil dari hasil roasting (flow fallback)
            if (empty($frayerUuids) && $hasilRoasting && $hasilRoasting->frayer_uuid) {
               $frayerUuids[] = $hasilRoasting->frayer_uuid;
            }

            $record = PembekuanIqfRoasting::create([
                'uuid'                       => Str::uuid(),
                'id_plan'                    => $this->resolvePlanId($user, $hasilRoasting?->id_produk, $hasilRoasting?->id_plan),
                'shift_id'                   => $hasilRoasting?->id_shift, // Sourced from HasilRoasting's id_shift
                'user_id'                    => $user->id,
                'id_produk'                  => $hasilRoasting?->id_produk,
                'tanggal'                    => Carbon::parse($request->tanggal)->setTimeFrom(now()),
                'jam'                        => $request->jam,
                'suhu_ruang_iqf'             => $request->suhu_ruang_iqf,
                'holding_time'               => $request->holding_time,
                'hasil_proses_roasting_uuid' => $request->hasil_roasting_uuid,
                'proses_roasting_fan_uuid'   => $hasilRoasting?->proses_roasting_fan_uuid ?? $request->roasting_fan_uuid,
                'penggorengan_uuid'          => $hasilRoasting?->penggorengan_uuid ?? $request->penggorengan_uuid,
                'frayer_uuid'                => !empty($frayerUuids) ? $frayerUuids : null,
                'frayer2_uuid'               => $request->frayer2_uuid ?: ($hasilRoasting?->frayer2_uuid),
                'frayer3_uuid'               => $request->frayer3_uuid ?: ($hasilRoasting?->frayer3_uuid),
                'frayer4_uuid'               => $request->frayer4_uuid ?: ($hasilRoasting?->frayer4_uuid),
                'frayer5_uuid'               => $request->frayer5_uuid ?: ($hasilRoasting?->frayer5_uuid),
                'breader_uuid'               => $breaderUuid,
                'battering_uuid'             => $batteringUuid,
                'predust_uuid'               => $predustUuid,
            ]);

            session()->flash('success', 'Proses 2 selesai! Semua data berhasil disimpan.');

            return response()->json([
                'success'      => true,
                'message'      => 'Proses 2 selesai! Semua data berhasil disimpan.',
                'uuid'         => $record->uuid,
                'redirect_url' => route('wizard.proses2.index'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($uuid)
    {
        $user = Auth::user();
        $query = PembekuanIqfRoasting::with([
            'user', 'plan', 'shift',
            'hasilProsesRoastingData.produk',
            'hasilProsesRoastingData.stdSuhuPusat',
            'prosesRoastingFanData.penggorengan',
            'prosesRoastingFanData.produk',
            'prosesRoastingFanData.suhuBlok',
            'prosesRoastingFanData.stdFan',
            'breaderData',
            'batteringData.jenis_better',
            'predustData.jenisPredust',
            'penggorenganData.shift',
            'frayer2', 'frayer3', 'frayer4', 'frayer5'
        ])->where('uuid', $uuid);

        $pembekuan = $query->firstOrFail();

        if ($user->role !== 'superadmin' && !$pembekuan->is_audit) {
            if ($pembekuan->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('wizard.proses2.show', $pembekuan)) {
            return $redirect;
        }

        // ── Fallback recovery for records affected by the sessionStorage bug ──
        // If battering_uuid or breader_uuid are null but the data exists in the
        // chain (HasilProsesRoasting or ProsesRoastingFan), patch the missing FKs
        // permanently so future loads are also correct.
        $needsPatch = false;

        if (!$pembekuan->battering_uuid || !$pembekuan->breader_uuid || !$pembekuan->predust_uuid) {
            $hasilRoasting = $pembekuan->hasilProsesRoastingData;
            $roastingFan   = $pembekuan->prosesRoastingFanData;

            $patch = [];

            if (!$pembekuan->battering_uuid) {
                $recovered = $hasilRoasting?->battering_uuid ?? $roastingFan?->battering_uuid;
                if ($recovered) {
                    $patch['battering_uuid'] = $recovered;
                    $pembekuan->battering_uuid = $recovered;
                }
            }

            if (!$pembekuan->breader_uuid) {
                $recovered = $hasilRoasting?->breader_uuid ?? $roastingFan?->breader_uuid;
                if ($recovered) {
                    $patch['breader_uuid'] = $recovered;
                    $pembekuan->breader_uuid = $recovered;
                }
            }

            if (!$pembekuan->predust_uuid) {
                $recovered = $hasilRoasting?->predust_uuid ?? $roastingFan?->predust_uuid;
                if ($recovered) {
                    $patch['predust_uuid'] = $recovered;
                    $pembekuan->predust_uuid = $recovered;
                }
            }

            if (!empty($patch)) {
                // Persist the recovered UUIDs so future loads don't need to fallback
                $pembekuan->updateQuietly($patch);
                // Reload the now-resolved relationships
                $pembekuan->load(['batteringData.jenis_better', 'breaderData', 'predustData.jenisPredust']);
            }
        }

        return view('qc-sistem.wizard_proses2.show', compact('pembekuan'));
    }

    /**
     * Bulk export PDF with filters
     */
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

            $query = PembekuanIqfRoasting::with([
                'user', 'plan', 'shift',
                'hasilProsesRoastingData.produk',
                'hasilProsesRoastingData.stdSuhuPusat',
                'prosesRoastingFanData.penggorengan',
                'prosesRoastingFanData.produk',
                'prosesRoastingFanData.suhuBlok',
                'prosesRoastingFanData.stdFan',
                'breaderData',
                'batteringData.jenis_better',
                'predustData.jenisPredust',
                'penggorenganData.shift',
            ])
            ->where('is_audit', $request->boolean('is_audit', false))
            ->whereNotNull('penggorengan_uuid')
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
                $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
                $filterInfo = [];

                if ($request->tanggal_mulai && $request->tanggal_akhir) {
                    $filterInfo[] = 'Periode: ' . Carbon::parse($request->tanggal_mulai)->format('d-m-Y') . ' sampai ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
                }
                if ($request->shift_id) {
                    $shift = DataShift::find($request->shift_id);
                    $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
                }
                if ($request->produk_id) {
                    $produk = JenisProduk::find($request->produk_id);
                    $filterInfo[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Unknown');
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

            $filters = [
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir,
                'shift_id' => $request->shift_id,
                'produk_id' => $request->produk_id,
                'kode_form' => $request->kode_form
            ];

            $kode_form = $request->kode_form;
            $pdf = Pdf::loadView('qc-sistem.wizard_proses2.export_pdf', compact('data', 'kode_form', 'filters'));
            $pdf->setPaper('A4', 'portrait');

            $filename = 'Wizard-Proses2-Bulk-'. date('Y-m-d_H-i-s') .'.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Excel massal berdasarkan filter (meniru bulkExportPdf)
     */
    public function bulkExportExcel(Request $request, \App\Services\BulkExport\WizardProses2BulkExportService $service)
    {
        return $service->export($request);
    }

    /**
     * Halaman HTML "Data Tidak Ditemukan" untuk export PDF/Excel
     */
    private function buildNoDataHtmlResponse(Request $request)
    {
        $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
        $filterInfo = [];

        if ($request->tanggal_mulai && $request->tanggal_akhir) {
            $filterInfo[] = 'Periode: ' . Carbon::parse($request->tanggal_mulai)->format('d-m-Y') . ' sampai ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
        }
        if ($request->shift_id) {
            $shift = DataShift::find($request->shift_id);
            $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
        }
        if ($request->produk_id) {
            $produk = JenisProduk::find($request->produk_id);
            $filterInfo[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Unknown');
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



    public function destroy($uuid)
    {
        $user = Auth::user();
        $pembekuan = PembekuanIqfRoasting::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && !$pembekuan->is_audit) {
            if ($pembekuan->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }
        }

        $pembekuan->delete();

        return redirect()->route('wizard.proses2.index')->with('success', 'Data berhasil dihapus.');
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

            $query = PembekuanIqfRoasting::whereDate('tanggal', $tanggal)->where('shift_id', $shift_id);

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

    public function edit($uuid)
    {
        $user = Auth::user();
        $query = PembekuanIqfRoasting::with([
            'user', 'plan', 'shift',
            'hasilProsesRoastingData.produk',
            'hasilProsesRoastingData.stdSuhuPusat',
            'prosesRoastingFanData.penggorengan',
            'prosesRoastingFanData.produk',
            'breaderData',
            'batteringData.jenis_better',
            'predustData.jenisPredust',
            'penggorenganData.shift',
            'frayer2', 'frayer3', 'frayer4', 'frayer5'
        ])->where('uuid', $uuid);

        $pembekuan = $query->firstOrFail();

        if ($user->role !== 'superadmin' && !$pembekuan->is_audit) {
            if ($pembekuan->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }
        }

        $isAuditMode = (bool) $pembekuan->is_audit;
        $queryParameters = request()->query();
        $hasAuditFlag = array_key_exists('is_audit', $queryParameters);

        if (($isAuditMode && request()->query('is_audit') !== '1') || (!$isAuditMode && $hasAuditFlag)) {
            unset($queryParameters['is_audit'], $queryParameters['uuid']);
            if ($isAuditMode) {
                $queryParameters['is_audit'] = '1';
            }

            return redirect()->route(
                'wizard.proses2.edit',
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
                    ->route('wizard.proses2.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        // Get Master Data
        // Jika audit: filter by audit record's plant, bukan user's plant
        $targetPlan = $pembekuan->is_audit ? $pembekuan->id_plan : ($user->role === 'superadmin' ? null : $user->id_plan);

        if ($targetPlan) {
            $produks       = JenisProduk::where('id_plan', $targetPlan)->get();
            $shifts        = DataShift::where('id_plan', $targetPlan)->get();
            $predustMaster = JenisPredust::where('id_plan', $targetPlan)->get();
            $jenisBetters  = JenisBetter::where('id_plan', $targetPlan)->get();
            $jenisBreaders = JenisBreader::where('id_plan', $targetPlan)->get();
            $stdSuhuPusat  = StdSuhuPusatRoasting::where('id_plan', $targetPlan)->get();
            $suhuFrayer1   = SuhuFrayer1::where('id_plan', $targetPlan)->get();
            $suhuFrayer2   = SuhuFrayer2::where('id_plan', $targetPlan)->get();
            $waktuPenggorengan = WaktuPenggorengan::where('id_plan', $targetPlan)->get();
            $waktuPenggorengan2 = WaktuPenggorengan2::where('id_plan', $targetPlan)->get();
            $suhuBloks     = SuhuBlok::where('id_plan', $targetPlan)->get();
            $stdFans       = StdFan::where('id_plan', $targetPlan)->get();
        } else {
            $produks       = JenisProduk::all();
            $shifts        = DataShift::all();
            $predustMaster = JenisPredust::all();
            $jenisBetters  = JenisBetter::all();
            $jenisBreaders = JenisBreader::all();
            $stdSuhuPusat  = StdSuhuPusatRoasting::all();
            $suhuFrayer1   = SuhuFrayer1::all();
            $suhuFrayer2   = SuhuFrayer2::all();
            $waktuPenggorengan = WaktuPenggorengan::all();
            $waktuPenggorengan2 = WaktuPenggorengan2::all();
            $suhuBloks     = SuhuBlok::all();
            $stdFans       = StdFan::all();
        }

        $f1_uuid = is_array($pembekuan->frayer_uuid) ? ($pembekuan->frayer_uuid[0] ?? null) : $pembekuan->frayer_uuid;
        $frayer1 = ProsesFrayer::where('uuid', $f1_uuid)->first();
        $frayer2 = Frayer2::where('uuid', $pembekuan->frayer2_uuid)->first();
        $frayer3 = Frayer3::where('uuid', $pembekuan->frayer3_uuid)->first();
        $frayer4 = Frayer4::where('uuid', $pembekuan->frayer4_uuid)->first();
        $frayer5 = Frayer5::where('uuid', $pembekuan->frayer5_uuid)->first();

        $frayerList = [
            1 => ['label' => 'Fryer 1', 'data' => $frayer1, 'suhu' => $suhuFrayer1, 'waktu' => $waktuPenggorengan, 'suhu_key' => 'id_suhu_frayer_1', 'waktu_key' => 'id_waktu_penggorengan'],
            2 => ['label' => 'Fryer 2', 'data' => $frayer2, 'suhu' => $suhuFrayer2, 'waktu' => $waktuPenggorengan2, 'suhu_key' => 'id_suhu_frayer_2', 'waktu_key' => 'id_waktu_penggorengan_2'],
            3 => ['label' => 'Fryer 3', 'data' => $frayer3, 'suhu' => $suhuFrayer1, 'waktu' => $waktuPenggorengan, 'suhu_key' => 'id_suhu_frayer', 'waktu_key' => 'id_waktu_penggorengan'],
            4 => ['label' => 'Fryer 4', 'data' => $frayer4, 'suhu' => $suhuFrayer1, 'waktu' => $waktuPenggorengan, 'suhu_key' => 'id_suhu_frayer', 'waktu_key' => 'id_waktu_penggorengan'],
            5 => ['label' => 'Fryer 5', 'data' => $frayer5, 'suhu' => $suhuFrayer1, 'waktu' => $waktuPenggorengan, 'suhu_key' => 'id_suhu_frayer', 'waktu_key' => 'id_waktu_penggorengan'],
        ];

        return view('qc-sistem.wizard_proses2.edit', compact(
            'pembekuan', 'produks', 'shifts', 'predustMaster', 'jenisBetters', 'jenisBreaders', 'stdSuhuPusat',
            'frayerList', 'frayer1', 'frayer2', 'frayer3', 'frayer4', 'frayer5', 'suhuBloks', 'stdFans',
            'suhuFrayer1', 'suhuFrayer2', 'waktuPenggorengan', 'waktuPenggorengan2'
        ));
    }

    public function update(Request $request, $uuid)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $pembekuan = PembekuanIqfRoasting::where('uuid', $uuid)->firstOrFail();

            // Cek batas waktu edit untuk role yang dibatasi (global config)
            $restrictedRoles = config('edit_limit.restricted_role_ids', []);
            if (in_array($user->id_role, $restrictedRoles)) {
                $timeLimitHours = config('edit_limit.time_limit_hours', 8);
                $createdAt = Carbon::parse($pembekuan->created_at);
                $hoursElapsed = $createdAt->diffInHours(now(), true);

                if ($hoursElapsed > $timeLimitHours) {
                    return redirect()
                        ->route('wizard.proses2.index')
                        ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
                }
            }

            // 1. Update Pembekuan IQF
            $pembekuan->update([
                'tanggal' => Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal)->format('Y-m-d H:i:s'),
                'jam' => $request->jam,
                'suhu_ruang_iqf' => $request->suhu_ruang_iqf,
                'holding_time' => $request->holding_time,
            ]);

            // 2. Update Penggorengan
            if ($pembekuan->penggorengan_uuid) {
                $penggorengan = Penggorengan::where('uuid', $pembekuan->penggorengan_uuid)->first();
                if ($penggorengan) {
                    $penggorengan->update([
                        'shift_id' => $request->shift_id,
                        'id_produk' => $request->id_produk,
                        'kode_produksi' => $request->kode_produksi,
                        'berat_produk' => $request->berat_produk,
                        'no_of_strokes' => $request->no_of_strokes,
                        'waktu_pemasakan' => $request->waktu_pemasakan,
                        'hasil_pencetakan' => $request->hasil_pencetakan,
                    ]);
                }
            }

            // 3. Update Predust
            if ($pembekuan->predust_uuid) {
                $predust = PembuatanPredust::where('uuid', $pembekuan->predust_uuid)->first();
                if ($predust) {
                    $predust->update([
                        'id_produk' => $request->id_produk,
                        'id_jenis_predust' => $request->id_jenis_predust,
                        'kode_produksi' => $request->kode_produksi_predust,
                        'kondisi_predust' => $request->kondisi_predust,
                    ]);
                }
            }

            // 4. Update Battering
            if ($pembekuan->battering_uuid) {
                $battering = ProsesBattering::where('uuid', $pembekuan->battering_uuid)->first();
                if ($battering) {
                    $battering->update([
                        'id_produk' => $request->id_produk,
                        'id_jenis_better' => $request->id_jenis_better,
                        'kode_produksi_better' => $request->kode_produksi_better,
                        'hasil_better' => $request->hasil_better,
                    ]);
                }
            }

            // 5. Update Breader
            if ($pembekuan->breader_uuid) {
                $breader = ProsesBreader::where('uuid', $pembekuan->breader_uuid)->first();
                if ($breader) {
                    $jenisBreader = is_array($request->id_jenis_breader)
                        ? implode(',', $request->id_jenis_breader)
                        : $request->id_jenis_breader;
                    $breader->update([
                        'id_produk' => $request->id_produk,
                        'id_jenis_breader' => $jenisBreader,
                        'kode_produksi' => $request->kode_produksi_breader,
                        'hasil_breader' => $request->hasil_breader,
                    ]);
                }
            }

            // 6. Update Frayers
            $frayers = [
                1 => ['model' => ProsesFrayer::class, 'uuid' => $pembekuan->frayer_uuid, 'suhu_key' => 'id_suhu_frayer_1', 'waktu_key' => 'id_waktu_penggorengan'],
                2 => ['model' => Frayer2::class, 'uuid' => $pembekuan->frayer2_uuid, 'suhu_key' => 'id_suhu_frayer_2', 'waktu_key' => 'id_waktu_penggorengan_2'],
                3 => ['model' => Frayer3::class, 'uuid' => $pembekuan->frayer3_uuid, 'suhu_key' => 'id_suhu_frayer', 'waktu_key' => 'id_waktu_penggorengan'],
                4 => ['model' => Frayer4::class, 'uuid' => $pembekuan->frayer4_uuid, 'suhu_key' => 'id_suhu_frayer', 'waktu_key' => 'id_waktu_penggorengan'],
                5 => ['model' => Frayer5::class, 'uuid' => $pembekuan->frayer5_uuid, 'suhu_key' => 'id_suhu_frayer', 'waktu_key' => 'id_waktu_penggorengan'],
            ];

            foreach ($frayers as $num => $fInfo) {
                $fUuid = is_array($fInfo['uuid']) ? ($fInfo['uuid'][0] ?? null) : $fInfo['uuid'];
                if ($fUuid) {
                    $fRecord = $fInfo['model']::where('uuid', $fUuid)->first();
                    if ($fRecord) {
                        $fRecord->update([
                            'id_produk' => $request->id_produk,
                            'jam' => $request->input("frayer_{$num}_jam"),
                            $fInfo['suhu_key'] => $request->input("frayer_{$num}_suhu"),
                            $fInfo['waktu_key'] => $request->input("frayer_{$num}_waktu"),
                            'aktual_penggorengan' => $request->input("frayer_{$num}_aktual_waktu"),
                            'aktual_suhu_penggorengan' => $request->input("frayer_{$num}_aktual_suhu"),
                            'tpm_minyak' => $request->input("frayer_{$num}_tpm"),
                            'keterangan' => $request->input("frayer_{$num}_keterangan"),
                        ]);
                    }
                }
            }

            // 7. Update Roasting Fan
            if ($pembekuan->proses_roasting_fan_uuid) {
                $fan = ProsesRoastingFan::where('uuid', $pembekuan->proses_roasting_fan_uuid)->first();
                if ($fan) {
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
                                'block_number' => $blockNum,
                                'id_suhu_blok' => $request->id_suhu_blok[$i] ?? null,
                                'id_std_fan' => $request->id_std_fan[$i] ?? null,
                                'suhu_roasting' => $request->suhu_roasting[$i] ?? null,
                                'fan_1' => $request->fan_1[$i] ?? null,
                                'fan_2' => $request->fan_2[$i] ?? null,
                                'fan_3' => $request->fan_3[$i] ?? null,
                                'fan_4' => $request->fan_4[$i] ?? null,
                                'aktual_humadity' => $aktualHum[$i] ?? null,
                                'infra_red' => $infraRedArr[$i] ?? null,
                            ];
                        }
                    }

                    $fan->update([
                        'id_produk' => $request->id_produk,
                        'jam' => $request->jam_roasting,
                        'waktu_pemasakan' => $request->waktu_pemasakan_roasting,
                        'blok_data' => $blokData,
                        'aktual_lama_proses' => $request->aktual_lama_proses_roasting,
                    ]);
                }
            }

            // 8. Update Hasil Roasting
            if ($pembekuan->hasil_proses_roasting_uuid) {
                $hasil = HasilProsesRoasting::where('uuid', $pembekuan->hasil_proses_roasting_uuid)->first();
                if ($hasil) {
                    $sensoriData = [
                        'sensori_kematangan' => $request->sensori_kematangan,
                        'sensori_kenampakan' => $request->sensori_kenampakan,
                        'sensori_warna'      => $request->sensori_warna,
                        'sensori_rasa'       => $request->sensori_rasa,
                        'sensori_bau'        => $request->sensori_bau,
                        'sensori_tekstur'    => $request->sensori_tekstur,
                    ];

                    $aktualSuhuPusat = is_array($request->aktual_suhu_pusat)
                        ? array_values(array_filter(array_map('trim', $request->aktual_suhu_pusat), fn($v) => $v !== '' && !is_null($v)))
                        : (is_string($request->aktual_suhu_pusat) ? array_values(array_filter(array_map('trim', explode(',', $request->aktual_suhu_pusat)), fn($v) => $v !== '' && !is_null($v))) : [$request->aktual_suhu_pusat]);

                    $hasil->update([
                        'id_produk' => $request->id_produk,
                        'id_std_suhu_pusat' => $request->id_std_suhu_pusat,
                        'aktual_suhu_pusat' => $aktualSuhuPusat,
                        'sensori' => $sensoriData,
                        'keterangan' => $request->keterangan,
                        'jam' => $request->jam_hasil,
                    ]);
                }
            }

            DB::commit();

            // Redirect to audit index if this is an audit record, otherwise to regular index
            if ($pembekuan->is_audit) {
                return redirect()->route('wizard.proses2.audit')->with('success', 'Data audit berhasil diperbarui.');
            }

            return redirect()->route('wizard.proses2.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
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
            ->whereNotNull('penggorengan_uuid')
            ->when($user->role !== 'superadmin', function ($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('hasilProsesRoastingData.produk', function($qp) use ($search) {
                    $qp->where('nama_produk', 'LIKE', "%{$search}%");
                })->orWhereHas('penggorenganData', function($qg) use ($search) {
                    $qg->where('kode_produksi', 'LIKE', "%{$search}%");
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
                ->whereNotNull('penggorengan_uuid')
                ->when($user->role !== 'superadmin', fn($q) => $q->where('id_plan', $user->id_plan));

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('hasilProsesRoastingData.produk', function($qp) use ($search) {
                        $qp->where('nama_produk', 'like', "%{$search}%");
                    })->orWhereHas('penggorenganData', function($qg) use ($search) {
                        $qg->where('kode_produksi', 'like', "%{$search}%");
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
                'html' => view('qc-sistem.wizard_proses2._table', compact('records'))->render(),
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
            return redirect()->route('wizard.proses2.edit', $item->uuid);
        }

        // Jika data operasional ini sudah memiliki versi audit sebelumnya
        if ($item->auditVersion) {
            return redirect()->route('wizard.proses2.edit', $item->auditVersion->uuid);
        }

        // Jika belum memiliki versi audit, buatkan salinan audit secara otomatis
        $clone = $item->copyToAudit();

        return redirect()->route('wizard.proses2.edit', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
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
                ->where('is_audit', false)
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
                ->where('is_audit', false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
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

}

