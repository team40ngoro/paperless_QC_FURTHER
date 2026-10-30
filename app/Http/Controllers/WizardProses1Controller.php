<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\JenisProduk;
use App\Models\DataShift;
use App\Models\JenisBetter;
use App\Models\JenisBreader;
use App\Models\SuhuFrayer1;
use App\Models\SuhuFrayer2;
use App\Models\WaktuPenggorengan;
use App\Models\WaktuPenggorengan2;
use App\Models\StdSuhuPusat;

use App\Models\Penggorengan;
use App\Models\ProsesBattering;
use App\Models\ProsesBreader;
use App\Models\ProsesFrayer;
use App\Models\Frayer2;
use App\Models\Frayer3;
use App\Models\Frayer4;
use App\Models\Frayer5;
use App\Models\HasilPenggorengan;
use App\Models\PembekuanIqfPenggorengan;
use App\Models\PembuatanPredust;
use App\Models\JenisPredust;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WizardProses1Export;
use App\Http\Controllers\Traits\HasAuditController;

class WizardProses1Controller extends Controller
{
    use HasAuditController;

    protected function auditModel(): string
    {
        return PembekuanIqfPenggorengan::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'wizard.proses1';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.wizard_proses1';
    }

    protected function auditRelations(): array
    {
        return [
            'hasilPenggorenganData',
            'hasilPenggorenganData.produk',
            'hasilPenggorenganData.stdSuhuPusat',
            'penggorenganData',
            'penggorenganData.produk',
            'penggorenganData.shift',
            'frayerData.suhuFrayer',
            'frayerData.waktuPenggorengan',
            'frayer2Data.suhuFrayer2',
            'frayer2Data.waktuPenggorengan2',
            'frayer3Data.suhuFrayer',
            'frayer3Data.waktuPenggorengan',
            'frayer4Data.suhuFrayer',
            'frayer4Data.waktuPenggorengan',
            'frayer5Data.suhuFrayer',
            'frayer5Data.waktuPenggorengan',
        ];
    }

    protected function auditSearchColumns(): array
    {
        return ['kode_form'];
    }

    protected function auditDateColumn(): string
    {
        return 'tanggal';
    }

    protected function auditShiftColumn(): string
    {
        return 'shift_id';
    }

    protected function auditPlanColumn(): string
    {
        return 'id_plan';
    }

    /**
     * Tampilkan halaman pemilihan jenis proses wizard
     */
    public function selection()
    {
        $user = Auth::user();
        $id_plan = $user->id_plan;

        // 1. Cek Proses 1 (Penggorengan -> IQF)
        // Cari Penggorengan yang belum ada di PembekuanIqfPenggorengan DAN belum ada di Roasting (alur proses 2)
        $draftProses1 = \App\Models\Penggorengan::where('user_id', $user->id)
            ->where('id_plan', $id_plan)
            ->where('created_at', '>=', now()->subDay()->startOfDay())
            ->whereDoesntHave('pembekuanIqfData')
            ->whereDoesntHave('prosesRoastingFanData')
            ->whereDoesntHave('pembekuanIqfRoastingData')
            ->orderBy('created_at', 'desc')
            ->first();

        // 2. Cek Proses 2 (Penggorengan -> Roasting -> IQF)
        $draftProses2 = \App\Models\Penggorengan::where('user_id', $user->id)
            ->where('id_plan', $id_plan)
            ->where('created_at', '>=', now()->subDay()->startOfDay())
            ->whereDoesntHave('pembekuanIqfRoastingData')
            // Tampilkan jika sudah ada Roasting Fan (Proses 2) 
            // ATAU baru sampai Penggorengan saja (Step 1 bisa lari ke IQF biasa atau Roasting)
            ->orderBy('created_at', 'desc')
            ->first();

        // 3. Cek Proses 3 (Roasting -> IQF)
        $draftProses3 = \App\Models\InputRoasting::where('user_id', $user->id)
            ->where('id_plan', $id_plan)
            ->where('created_at', '>=', now()->subDay()->startOfDay())
            ->whereDoesntHave('pembekuanIqfRoastingData')
            ->orderBy('created_at', 'desc')
            ->first();

        // 4. Cek Proses 4 (Tumbling -> Aging)
        $draftProses4 = \App\Models\BahanBakuTumbling::where('user_id', $user->id)
            ->where('id_plan', $id_plan)
            ->where('created_at', '>=', now()->subDay()->startOfDay())
            // Menggunakan relasi hasOneThrough yang sudah diperbaiki
            ->whereDoesntHave('prosesAging')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('qc-sistem.wizard.selection', compact(
            'draftProses1', 
            'draftProses2', 
            'draftProses3', 
            'draftProses4'
        ));
    }

    /**
     * Tampilkan halaman index Wizard Proses 1
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->id_role == 6) {
            return redirect()->route('wizard.proses1.audit');
        }
        $search = $request->input('search');
        $perPage = $request->get('per_page', 5);

        // Ambil semua data pembekuan IQF penggorengan
        $query = PembekuanIqfPenggorengan::operasional()->with([
            'hasilPenggorenganData',
            'hasilPenggorenganData.produk',
            'hasilPenggorenganData.stdSuhuPusat',
            'penggorenganData',
            'penggorenganData.produk',
            'penggorenganData.shift',
            'frayerData',
            'frayerData.suhuFrayer',
            'frayerData.waktuPenggorengan',
            'frayer2Data',
            'frayer2Data.suhuFrayer2',
            'frayer2Data.waktuPenggorengan2',
            'frayer3Data',
            'frayer3Data.suhuFrayer',
            'frayer3Data.waktuPenggorengan',
            'frayer4Data',
            'frayer4Data.suhuFrayer',
            'frayer4Data.waktuPenggorengan',
            'frayer5Data',
            'frayer5Data.suhuFrayer',
            'frayer5Data.waktuPenggorengan',
            'predustData',
            'predustData.jenisPredust',
            'batteringData',
            'breaderData',
        ]);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('hasilPenggorenganData.produk', function($qp) use ($search) {
                    $qp->where('nama_produk', 'LIKE', "%{$search}%");
                })->orWhereHas('penggorenganData.produk', function($qp) use ($search) {
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
            ->paginate($perPage);
        
        $groupedData = $pembekuanData->getCollection()->groupBy(function($item) {
            $date = $item->tanggal ? $item->tanggal->format('Y-m-d') : 'no_date';
            $shiftId = $item->penggorenganData->shift_id ?? 'no_shift';
            return $date . '_' . $shiftId;
        });
        
        $pembekuanData->setCollection($groupedData);

        return view('qc-sistem.wizard_proses1.index', compact('pembekuanData', 'perPage', 'search'));
    }

    /**
     * Tampilkan detail Proses 1 berdasarkan UUID pembekuan IQF
     */
    public function show($uuid)
    {
        $user = Auth::user();

        $query = PembekuanIqfPenggorengan::with([
            'hasilPenggorenganData',
            'hasilPenggorenganData.produk',
            'hasilPenggorenganData.stdSuhuPusat',
            'penggorenganData',
            'penggorenganData.produk',
            'penggorenganData.shift',
            'batteringData',
            'batteringData.produk',
            'batteringData.jenis_better',
            'breaderData',
            'breaderData.produk',
            'breaderData.jenisBreader',
            'predustData',
            'predustData.jenisPredust',
        ])->where('uuid', $uuid);

        $pembekuan = $query->firstOrFail();

        if ($user->role !== 'superadmin' && !$pembekuan->is_audit) {
            if ($pembekuan->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('wizard.proses1.show', $pembekuan)) {
            return $redirect;
        }

        // Ambil data frayer yang terkait
        $frayer1 = ProsesFrayer::where('uuid', $pembekuan->frayer_uuid)->with(['produk', 'suhuFrayer', 'waktuPenggorengan'])->first();
        $frayer2 = Frayer2::where('uuid', $pembekuan->frayer2_uuid)->with(['produk', 'suhuFrayer2', 'waktuPenggorengan2'])->first();
        $frayer3 = Frayer3::where('uuid', $pembekuan->frayer3_uuid)->with(['produk', 'suhuFrayer', 'waktuPenggorengan'])->first();
        $frayer4 = Frayer4::where('uuid', $pembekuan->frayer4_uuid)->with(['produk', 'suhuFrayer', 'waktuPenggorengan'])->first();
        $frayer5 = Frayer5::where('uuid', $pembekuan->frayer5_uuid)->with(['produk', 'suhuFrayer', 'waktuPenggorengan'])->first();

        $predustData = $pembekuan->predustData;

        return view('qc-sistem.wizard_proses1.show', compact(
            'pembekuan',
            'frayer1',
            'frayer2',
            'frayer3',
            'frayer4',
            'frayer5',
            'predustData'
        ));
    }

    /**
     * Tampilkan halaman wizard create Proses 1
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $continueUuid = $request->query('continue_uuid');
        $draftData = null;

        if ($continueUuid) {
            // Sederhanakan loading draf untuk menghindari error relasi
            $draftData = Penggorengan::where('uuid', $continueUuid)->first();
        }

        if ($user->role === 'superadmin') {
            $shifts  = DataShift::all();
            $produks = JenisProduk::all();
            $jenisBreaders = JenisBreader::all();
        } else {
            $shifts  = DataShift::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $jenisBreaders = JenisBreader::where('id_plan', $user->id_plan)->get();
        }

        // List frayer yang tersedia (untuk checkbox pilihan)
        $frayers = [
            1 => 'Frayer 1',
            2 => 'Frayer 2',
            3 => 'Frayer 3',
            4 => 'Frayer 4',
            5 => 'Frayer 5',
        ];

        return view('qc-sistem.wizard_proses1.create', compact(
            'shifts', 'produks', 'jenisBreaders', 'frayers', 'draftData'
        ));
    }

    // =====================================================================
    // AJAX STEP HANDLERS
    // =====================================================================

    /**
     * STEP 1: Simpan Penggorengan via AJAX
     */
    public function storePenggorengan(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'id_produk'        => 'required|exists:jenis_produk,id',
                'shift_id'         => 'required|exists:data_shift,id',
                'kode_produksi'    => 'required|string',
                'berat_produk'     => 'required|string|max:255',
                'no_of_strokes'    => 'required|string',
                'waktu_pemasakan'  => 'required|string',
                'jam'              => 'required|string',
                'tanggal'          => 'required',
                'hasil_pencetakan' => 'nullable|string',
            ]);

            $record = Penggorengan::create([
                'uuid'             => Str::uuid(),
                'id_produk'        => $request->id_produk,
                'shift_id'         => $request->shift_id,
                'id_plan'          => $user->id_plan,
                'user_id'          => $user->id,
                'kode_produksi'    => $request->kode_produksi,
                'berat_produk'     => $request->berat_produk,
                'no_of_strokes'    => $request->no_of_strokes,
                'waktu_pemasakan'  => $request->waktu_pemasakan,
                'jam'              => $request->jam,
                'tanggal'          => Carbon::parse($request->tanggal)->format('Y-m-d H:i:s'),
                'hasil_pencetakan' => $request->hasil_pencetakan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Penggorengan berhasil disimpan.',
                'uuid'    => $record->uuid,
                'data'    => [
                    'id_produk'     => $record->id_produk,
                    'kode_produksi' => $record->kode_produksi,
                    'nama_produk'   => $record->produk->nama_produk ?? '-',
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * STEP 2: Simpan Predust via AJAX
     */
    public function storePredust(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'id_produk'        => 'required|exists:jenis_produk,id',
                'id_jenis_predust' => 'required|exists:jenis_predust,id',
                'kode_produksi'    => 'required|string|max:255',
                'hasil_predust'    => 'required|in:✔,✘',
                'tanggal'          => 'required|date',
                'jam'              => 'required|date_format:H:i',
                'penggorengan_uuid' => 'required|string|exists:penggorengan,uuid',
            ]);

            $penggorengan = Penggorengan::where('uuid', $request->penggorengan_uuid)->first();

            $record = PembuatanPredust::create([
                'uuid'              => Str::uuid(),
                'id_plan'           => $user->id_plan,
                'user_id'           => $user->id,
                'penggorengan_uuid' => $request->penggorengan_uuid,
                'id_produk'         => $request->id_produk,
                'id_jenis_predust'  => $request->id_jenis_predust,
                'tanggal'           => Carbon::parse($request->tanggal)->format('Y-m-d H:i:s'),
                'jam'               => $request->jam,
                'kondisi_predust'   => $request->hasil_predust,
                'hasil_pencetakan'  => $penggorengan?->hasil_pencetakan ?: '✔',
                'kode_produksi'     => $request->kode_produksi,
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

    /**
     * STEP 3: Simpan Battering via AJAX
     */
    public function storeBattering(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'id_produk'            => 'required|exists:jenis_produk,id',
                'kode_produksi_better' => 'required|array',
                'id_jenis_better'      => 'required|array',
                'id_jenis_better.*'    => 'exists:jenis_better,id',
                'hasil_better'         => 'required|string|max:255',
                'tanggal'              => 'required',
                'jam'                  => 'required|date_format:H:i',
                'penggorengan_uuid'    => 'required|string|exists:penggorengan,uuid',
                'predust_uuid'         => 'nullable|string|exists:pembuatan_predust,uuid',
            ]);

            $jenisBetter = is_array($request->id_jenis_better)
                ? implode(',', $request->id_jenis_better)
                : $request->id_jenis_better;

            $record = ProsesBattering::create([
                'uuid'                 => Str::uuid(),
                'id_plan'              => $user->id_plan,
                'user_id'              => $user->id,
                'id_produk'            => $request->id_produk,
                'kode_produksi_better' => $request->kode_produksi_better,
                'id_jenis_better'      => $jenisBetter,
                'hasil_better'         => $request->hasil_better,
                'tanggal'              => Carbon::parse($request->tanggal)->format('Y-m-d H:i:s'),
                'jam'                  => $request->jam,
                'penggorengan_uuid'    => $request->penggorengan_uuid,
                'predust_uuid'         => $request->predust_uuid,
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

    /**
     * STEP 4: Simpan Breader via AJAX
     */
    public function storeBreader(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'id_produk'        => 'required|exists:jenis_produk,id',
                'id_jenis_breader' => 'required|array',
                'kode_produksi_breader' => 'required|array',
                'hasil_breader'    => 'required|string',
                'tanggal'          => 'required',
                'jam'              => 'required|date_format:H:i',
                'battering_uuid'   => 'required|string|exists:proses_battering,uuid',
            ]);

            $jenisBreader = is_array($request->id_jenis_breader)
                ? implode(',', $request->id_jenis_breader)
                : $request->id_jenis_breader;

            // Ambil penggorengan_uuid & predust_uuid dari battering
            $battering = ProsesBattering::where('uuid', $request->battering_uuid)->first();

            $record = ProsesBreader::create([
                'uuid'              => Str::uuid(),
                'id_produk'         => $request->id_produk,
                'user_id'           => $user->id,
                'id_plan'           => $user->id_plan,
                'id_jenis_breader'  => $jenisBreader,
                'kode_produksi'     => $request->kode_produksi_breader,
                'hasil_breader'     => $request->hasil_breader,
                'tanggal'           => Carbon::parse($request->tanggal)->format('Y-m-d H:i:s'),
                'jam'               => $request->jam,
                'battering_uuid'    => $request->battering_uuid,
                'penggorengan_uuid' => $battering?->penggorengan_uuid,
                'predust_uuid'      => $battering?->predust_uuid ?? $request->predust_uuid,
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

    /**
     * STEP 4: Simpan satu Frayer via AJAX (bisa dipanggil beberapa kali untuk frayer berbeda)
     * frayer_number: 1, 2, 3, 4, atau 5
     */
    public function storeFrayer(Request $request)
    {
        try {
            $user         = Auth::user();
            $frayerNumber = (int) $request->frayer_number;

            if (!in_array($frayerNumber, [1, 2, 3, 4, 5])) {
                return response()->json(['success' => false, 'message' => 'Nomor frayer tidak valid.'], 422);
            }

            $request->validate([
                'frayer_number'    => 'required|integer|between:1,5',
                'id_produk'        => 'required',
                'tanggal'          => 'required',
                'jam'              => 'required|date_format:H:i',
                'aktual_penggorengan'      => 'required|string',
                'aktual_suhu_penggorengan' => 'nullable|string',
                'tpm_minyak'       => 'required|string',
                'breader_uuid'     => 'required|string|exists:proses_breader,uuid',
                'keterangan'       => 'nullable|string',
            ]);

            $breader    = ProsesBreader::where('uuid', $request->breader_uuid)->first();
            $battering  = $breader?->battering_uuid
                ? ProsesBattering::where('uuid', $breader->battering_uuid)->first()
                : null;

            $baseData = [
                'id_plan'                  => $user->id_plan,
                'user_id'                  => $user->id,
                'id_produk'                => $request->id_produk,
                'tanggal'                  => Carbon::parse($request->tanggal)->format('Y-m-d H:i:s'),
                'jam'                      => $request->jam,
                'aktual_penggorengan'      => $request->aktual_penggorengan,
                'aktual_suhu_penggorengan' => $request->aktual_suhu_penggorengan,
                'tpm_minyak'               => $request->tpm_minyak,
                'ffa'                      => $request->ffa,
                'keterangan'               => $request->keterangan,
                'breader_uuid'             => $request->breader_uuid,
                'battering_uuid'           => $breader?->battering_uuid,
                'penggorengan_uuid'        => $battering?->penggorengan_uuid ?? $breader?->penggorengan_uuid,
                'predust_uuid'             => $breader?->predust_uuid ?? $request->predust_uuid,
            ];

            // Frayer sebelumnya (jika frayer 2-5, butuh uuid frayer sebelumnya)
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
                    $request->validate(['id_suhu_frayer_1' => 'required', 'id_waktu_penggorengan' => 'required']);
                    $baseData['id_suhu_frayer']        = $request->id_suhu_frayer_1;
                    $baseData['id_waktu_penggorengan'] = $request->id_waktu_penggorengan;
                    $record = Frayer3::create($baseData);
                    break;

                case 4:
                    $request->validate(['id_suhu_frayer_1' => 'required', 'id_waktu_penggorengan' => 'required']);
                    $baseData['id_suhu_frayer']        = $request->id_suhu_frayer_1;
                    $baseData['id_waktu_penggorengan'] = $request->id_waktu_penggorengan;
                    $record = Frayer4::create($baseData);
                    break;

                case 5:
                    $request->validate(['id_suhu_frayer_1' => 'required', 'id_waktu_penggorengan' => 'required']);
                    $baseData['id_suhu_frayer']        = $request->id_suhu_frayer_1;
                    $baseData['id_waktu_penggorengan'] = $request->id_waktu_penggorengan;
                    $record = Frayer5::create($baseData);
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => "Data Frayer {$frayerNumber} berhasil disimpan.",
                'uuid'    => $record->uuid,
                'frayer_number' => $frayerNumber,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * STEP 6: Simpan Hasil Penggorengan via AJAX
     */
    public function storeHasilPenggorengan(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'id_produk'           => 'required',
                'id_std_suhu_pusat'   => 'required',
                'aktual_suhu_pusat'   => 'required',
                'tanggal'             => 'required',
                'jam'                 => 'required|date_format:H:i',
                'sensori_kematangan'  => 'nullable|in:✔,✘',
                'sensori_kenampakan'  => 'nullable|in:✔,✘',
                'sensori_warna'       => 'nullable|in:✔,✘',
                'sensori_rasa'        => 'nullable|in:✔,✘',
                'sensori_bau'         => 'nullable|in:✔,✘',
                'sensori_tekstur'     => 'nullable|in:✔,✘',
                'keterangan'          => 'nullable|string',
            ]);

            $aktualSuhuPusat = $request->aktual_suhu_pusat;
            if (is_array($aktualSuhuPusat)) {
                $filtered = array_filter(array_map('trim', $aktualSuhuPusat), fn($v) => $v !== '' && !is_null($v));
                $aktualSuhuPusat = !empty($filtered) ? implode(', ', $filtered) : null;
            }

            $data = [
                'id_plan'             => $user->id_plan,
                'user_id'             => $user->id,
                'tanggal'             => Carbon::parse($request->tanggal)->format('Y-m-d H:i:s'),
                'jam'                 => $request->jam,
                'id_produk'           => $request->id_produk,
                'id_std_suhu_pusat'   => $request->id_std_suhu_pusat,
                'aktual_suhu_pusat'   => $aktualSuhuPusat,
                'sensori_kematangan'  => $request->sensori_kematangan,
                'sensori_kenampakan'  => $request->sensori_kenampakan,
                'sensori_warna'       => $request->sensori_warna,
                'sensori_rasa'        => $request->sensori_rasa,
                'sensori_bau'         => $request->sensori_bau,
                'sensori_tekstur'     => $request->sensori_tekstur,
                'keterangan'          => $request->keterangan,
                'frayer_uuid'         => $request->frayer_uuid ?: null,
                'frayer2_uuid'        => $request->frayer2_uuid ?: null,
                'frayer3_uuid'        => $request->frayer3_uuid ?: null,
                'frayer4_uuid'        => $request->frayer4_uuid ?: null,
                'frayer5_uuid'        => $request->frayer5_uuid ?: null,
                'breader_uuid'        => $request->breader_uuid ?: null,
                'battering_uuid'      => $request->battering_uuid ?: null,
                'predust_uuid'        => $request->predust_uuid ?: null,
                'penggorengan_uuid'   => $request->penggorengan_uuid ?: null,
            ];

            $record = HasilPenggorengan::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Data Hasil Penggorengan berhasil disimpan.',
                'uuid'    => $record->uuid,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * STEP 7: Simpan Pembekuan IQF Penggorengan via AJAX
     */
    public function storePembekuanIqf(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'tanggal'                  => 'required',
                'jam'                      => 'required|date_format:H:i',
                'suhu_ruang_iqf'           => 'required|string',
                'holding_time'             => 'required|string',
                'hasil_penggorengan_uuid'  => 'required|string|exists:hasil_penggorengan,uuid',
            ]);

            $data = [
                'id_plan'                 => $user->id_plan,
                'user_id'                 => $user->id,
                'tanggal'                 => Carbon::parse($request->tanggal)->format('Y-m-d H:i:s'),
                'jam'                     => $request->jam,
                'suhu_ruang_iqf'          => $request->suhu_ruang_iqf,
                'holding_time'            => $request->holding_time,
                'hasil_penggorengan_uuid' => $request->hasil_penggorengan_uuid,
                'frayer_uuid'             => $request->frayer_uuid ?: null,
                'frayer2_uuid'            => $request->frayer2_uuid ?: null,
                'frayer3_uuid'            => $request->frayer3_uuid ?: null,
                'frayer4_uuid'            => $request->frayer4_uuid ?: null,
                'frayer5_uuid'            => $request->frayer5_uuid ?: null,
                'breader_uuid'            => $request->breader_uuid ?: null,
                'battering_uuid'          => $request->battering_uuid ?: null,
                'predust_uuid'            => $request->predust_uuid ?: null,
                'penggorengan_uuid'       => $request->penggorengan_uuid ?: null,
            ];

            $record = PembekuanIqfPenggorengan::create($data);

            session()->flash('success', 'Proses 1 selesai! Semua data berhasil disimpan.');

            return response()->json([
                'success'      => true,
                'message'      => 'Proses 1 selesai! Semua data berhasil disimpan.',
                'uuid'         => $record->uuid,
                'redirect_url' => route('wizard.proses1.index'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function bulkExportPdf(Request $request)
    {
        // Naikkan time limit & memory untuk proses render PDF banyak halaman
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        try {
            $request->validate([
                'tanggal_mulai' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
                'shift_id' => 'nullable|integer',
                'produk_id' => 'nullable|integer', // Tambahkan validasi produk_id
                'kode_form' => 'required|string|max:50',
                'is_audit' => 'nullable|boolean',
            ]);

            $user = Auth::user();
            
            $query = PembekuanIqfPenggorengan::with([
                'hasilPenggorenganData',
                'hasilPenggorenganData.produk',
                'hasilPenggorenganData.stdSuhuPusat',
                'penggorenganData',
                'penggorenganData.produk',
                'penggorenganData.shift',
                'batteringData',
                'batteringData.produk',
                'batteringData.jenis_better',
                'breaderData',
                'breaderData.produk',
                'breaderData.jenisBreader',
                'predustData',
                'predustData.jenisPredust',
            ])
            ->where('is_audit', $request->boolean('is_audit', false))
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

            // Apply filters
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai)
                  ->whereDate('tanggal', '<=', $request->tanggal_akhir);
            
            if ($request->shift_id) {
                $query->whereHas('penggorenganData', function($q) use ($request) {
                    $q->where('shift_id', $request->shift_id);
                });
            }

            if ($request->produk_id) {
                $query->whereHas('hasilPenggorenganData.produk', function($q) use ($request) {
                    $q->where('id', $request->produk_id);
                });
            }

            $data = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->get();

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

            $processedData = $this->loadFrayerDataBatched($data);

            $filters = [
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir,
                'shift_id' => $request->shift_id,
                'produk_id' => $request->produk_id,
                'kode_form' => $request->kode_form
            ];

            $kode_form = $request->kode_form;
            $pdf = Pdf::loadView('qc-sistem.wizard_proses1.export_pdf', compact('processedData', 'kode_form', 'filters'));
            $pdf->setPaper('A4', 'portrait');
            // Optimalkan DomPDF: matikan fitur yang memperlambat rendering
            $pdf->getDomPDF()->set_option('enable_javascript', false);
            $pdf->getDomPDF()->set_option('enable_remote', false);
            $pdf->getDomPDF()->set_option('enable_html5_parser', true);
            
            $filename = 'Wizard-Proses1-Bulk-'. date('Y-m-d_H-i-s') .'.pdf';
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
    public function bulkExportExcel(Request $request)
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

            $query = PembekuanIqfPenggorengan::with([
                'hasilPenggorenganData',
                'hasilPenggorenganData.produk',
                'hasilPenggorenganData.stdSuhuPusat',
                'penggorenganData',
                'penggorenganData.produk',
                'penggorenganData.shift',
                'batteringData',
                'batteringData.produk',
                'batteringData.jenis_better',
                'breaderData',
                'breaderData.produk',
                'breaderData.jenisBreader',
                'predustData',
                'predustData.jenisPredust',
            ])
            ->where('is_audit', $request->boolean('is_audit', false))
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

            // Apply filters
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai)
                  ->whereDate('tanggal', '<=', $request->tanggal_akhir);

            if ($request->shift_id) {
                $query->whereHas('penggorenganData', function($q) use ($request) {
                    $q->where('shift_id', $request->shift_id);
                });
            }

            if ($request->produk_id) {
                $query->whereHas('hasilPenggorenganData.produk', function($q) use ($request) {
                    $q->where('id', $request->produk_id);
                });
            }

            $data = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->get();

            if ($data->isEmpty()) {
                return $this->buildNoDataHtmlResponse($request);
            }

            $processedData = $this->loadFrayerDataBatched($data);

            $filters = [
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir,
                'shift_id' => $request->shift_id,
                'produk_id' => $request->produk_id,
                'kode_form' => $request->kode_form
            ];

            $kode_form = $request->kode_form;

            $filename = 'Wizard-Proses1-Bulk-'. date('Y-m-d_H-i-s') .'.xlsx';
            return Excel::download(new WizardProses1Export($processedData, $kode_form, $filters), $filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor Excel: ' . $e-okee>getMessage()
            ], 500);
        }
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

    /**
     * Hapus data Pembekuan IQF Penggorengan
     */
    public function destroy($uuid)
    {
        $user = Auth::user();
        $record = PembekuanIqfPenggorengan::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && !$record->is_audit) {
            if ($record->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }
        }

        $record->delete();

        return redirect()->route('wizard.proses1.index')->with('success', 'Data berhasil dihapus.');
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
            $pembekuan = PembekuanIqfPenggorengan::where('uuid', $uuid)->firstOrFail();
            
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

            $query = PembekuanIqfPenggorengan::whereDate('tanggal', $tanggal)
                ->whereHas('penggorenganData', function($q) use ($shift_id) {
                    $q->where('shift_id', $shift_id);
                });

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
    /**
     * Tampilkan halaman edit Proses 1 berdasarkan UUID
     */
    public function edit($uuid)
    {
        $user = Auth::user();

        $query = PembekuanIqfPenggorengan::with([
            'hasilPenggorenganData',
            'penggorenganData',
            'batteringData',
            'breaderData',
            'predustData',
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
                'wizard.proses1.edit',
                array_merge(['uuid' => $pembekuan->uuid], $queryParameters)
            );
        }

        // Ambil data frayer yang terkait
        $frayer1 = ProsesFrayer::where('uuid', $pembekuan->frayer_uuid)->first();
        $frayer2 = Frayer2::where('uuid', $pembekuan->frayer2_uuid)->first();
        $frayer3 = Frayer3::where('uuid', $pembekuan->frayer3_uuid)->first();
        $frayer4 = Frayer4::where('uuid', $pembekuan->frayer4_uuid)->first();
        $frayer5 = Frayer5::where('uuid', $pembekuan->frayer5_uuid)->first();

        $predustData = $pembekuan->predustData;

        // Master Data
        // Jika audit: filter by audit record's plant, bukan user's plant
        $targetPlan = $pembekuan->is_audit ? $pembekuan->id_plan : ($user->role === 'superadmin' ? null : $user->id_plan);
        
        if ($targetPlan) {
            $shifts  = DataShift::where('id_plan', $targetPlan)->get();
            $produks = JenisProduk::where('id_plan', $targetPlan)->get();
            $jenisBreaders = JenisBreader::where('id_plan', $targetPlan)->get();
            $jenisBetters = JenisBetter::where('id_plan', $targetPlan)->get();
            $jenisPredust = JenisPredust::where('id_plan', $targetPlan)->get();
        } else {
            $shifts  = DataShift::all();
            $produks = JenisProduk::all();
            $jenisBreaders = JenisBreader::all();
            $jenisBetters = JenisBetter::all();
            $jenisPredust = JenisPredust::all();
        }

        $suhuFrayer1 = SuhuFrayer1::all();
        $suhuFrayer2 = SuhuFrayer2::all();
        $waktuPenggorengan = WaktuPenggorengan::all();
        $waktuPenggorengan2 = WaktuPenggorengan2::all();
        $stdSuhuPusat = StdSuhuPusat::all();

        return view('qc-sistem.wizard_proses1.edit', compact(
            'pembekuan',
            'frayer1', 'frayer2', 'frayer3', 'frayer4', 'frayer5',
            'predustData',
            'shifts', 'produks', 'jenisBreaders', 'jenisBetters', 'jenisPredust',
            'suhuFrayer1', 'suhuFrayer2', 'waktuPenggorengan', 'waktuPenggorengan2', 'stdSuhuPusat'
        ));
    }

    /**
     * Update data Proses 1
     */
    public function update(Request $request, $uuid)
    {
        try {
            $user = Auth::user();
            $pembekuan = PembekuanIqfPenggorengan::where('uuid', $uuid)->firstOrFail();

            // Validate common fields
            $request->validate([
                'tanggal' => 'required',
                'jam' => 'required',
                'suhu_ruang_iqf' => 'required',
                'holding_time' => 'required',
            ]);

            // 1. Update Pembekuan IQF
            $pembekuan->update([
                'tanggal' => Carbon::parse($request->tanggal)->format('Y-m-d H:i:s'),
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

            // 3. Update Battering
            if ($pembekuan->battering_uuid) {
                $battering = ProsesBattering::where('uuid', $pembekuan->battering_uuid)->first();
                if ($battering) {
                    $jenisBetter = is_array($request->id_jenis_better)
                        ? implode(',', $request->id_jenis_better)
                        : $request->id_jenis_better;
                    $battering->update([
                        'id_produk' => $request->id_produk,
                        'id_jenis_better' => $jenisBetter,
                        'kode_produksi_better' => $request->kode_produksi_better,
                        'hasil_better' => $request->hasil_better,
                    ]);
                }
            }

            // 4. Update Breader
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

            // 4.5. Update Predust
            if ($pembekuan->predust_uuid) {
                $predust = PembuatanPredust::where('uuid', $pembekuan->predust_uuid)->first();
                if ($predust) {
                    $predust->update([
                        'id_jenis_predust' => $request->id_jenis_predust,
                        'kode_produksi' => $request->kode_produksi_predust,
                        'kondisi_predust' => $request->kondisi_predust,
                    ]);
                }
            }

            // 5. Update Frayers
            $frayers = [
                1 => ['model' => ProsesFrayer::class, 'uuid' => $pembekuan->frayer_uuid, 'suhu_key' => 'id_suhu_frayer_1', 'waktu_key' => 'id_waktu_penggorengan'],
                2 => ['model' => Frayer2::class, 'uuid' => $pembekuan->frayer2_uuid, 'suhu_key' => 'id_suhu_frayer_2', 'waktu_key' => 'id_waktu_penggorengan_2'],
                3 => ['model' => Frayer3::class, 'uuid' => $pembekuan->frayer3_uuid, 'suhu_key' => 'id_suhu_frayer', 'waktu_key' => 'id_waktu_penggorengan'],
                4 => ['model' => Frayer4::class, 'uuid' => $pembekuan->frayer4_uuid, 'suhu_key' => 'id_suhu_frayer', 'waktu_key' => 'id_waktu_penggorengan'],
                5 => ['model' => Frayer5::class, 'uuid' => $pembekuan->frayer5_uuid, 'suhu_key' => 'id_suhu_frayer', 'waktu_key' => 'id_waktu_penggorengan'],
            ];

            foreach ($frayers as $num => $fInfo) {
                if ($fInfo['uuid']) {
                    $fRecord = $fInfo['model']::where('uuid', $fInfo['uuid'])->first();
                    if ($fRecord) {
                        $updateData = [
                            'id_produk' => $request->id_produk,
                            'jam' => $request->input("frayer_{$num}_jam"),
                            $fInfo['suhu_key'] => $request->input("frayer_{$num}_suhu"),
                            $fInfo['waktu_key'] => $request->input("frayer_{$num}_waktu"),
                            'aktual_penggorengan' => $request->input("frayer_{$num}_aktual_waktu"),
                            'aktual_suhu_penggorengan' => $request->input("frayer_{$num}_aktual_suhu"),
                            'tpm_minyak' => $request->input("frayer_{$num}_tpm"),
                            'keterangan' => $request->input("frayer_{$num}_keterangan"),
                        ];
                        if (in_array($num, [1, 2])) {
                            $updateData['ffa'] = $request->input("frayer_{$num}_ffa");
                        }
                        $fRecord->update($updateData);
                    }
                }
            }

            // 6. Update Hasil Penggorengan
            if ($pembekuan->hasil_penggorengan_uuid) {
                $hasil = HasilPenggorengan::where('uuid', $pembekuan->hasil_penggorengan_uuid)->first();
                if ($hasil) {
                    $aktualSuhuPusat = $request->aktual_suhu_pusat;
                    if (is_array($aktualSuhuPusat)) {
                        $filtered = array_filter(array_map('trim', $aktualSuhuPusat), fn($v) => $v !== '' && !is_null($v));
                        $aktualSuhuPusat = !empty($filtered) ? implode(', ', $filtered) : null;
                    }

                    $hasil->update([
                        'id_produk' => $request->id_produk,
                        'id_std_suhu_pusat' => $request->id_std_suhu_pusat,
                        'aktual_suhu_pusat' => $aktualSuhuPusat,
                        'sensori_kematangan' => $request->sensori_kematangan,
                        'sensori_kenampakan' => $request->sensori_kenampakan,
                        'sensori_warna' => $request->sensori_warna,
                        'sensori_rasa' => $request->sensori_rasa,
                        'sensori_bau' => $request->sensori_bau,
                        'sensori_tekstur' => $request->sensori_tekstur,
                        'keterangan' => $request->keterangan,
                    ]);
                }
            }
            
            // Redirect to audit index if this is an audit record, otherwise to regular index
            if ($pembekuan->is_audit) {
                return redirect()->route('wizard.proses1.audit')->with('success', 'Data audit berhasil diperbarui.');
            }
            
            return redirect()->route('wizard.proses1.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Override auditIndex to match search behavior of regular index
     */
    public function auditIndex(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $auditedSourceUuids = PembekuanIqfPenggorengan::where('is_audit', true)->whereNotNull('source_uuid')->pluck('source_uuid')->toArray();

$query = PembekuanIqfPenggorengan::with($this->auditRelations())
            ->where(function ($q) use ($auditedSourceUuids) {
            $q->where('is_audit', true)
              ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                  $subQ->where('is_audit', false);
                  if (!empty($auditedSourceUuids)) {
                      $subQ->whereNotIn('uuid', $auditedSourceUuids);
                  }
              });
        })
            ->when($user->role !== 'superadmin', function ($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('hasilPenggorenganData.produk', function($qp) use ($search) {
                    $qp->where('nama_produk', 'LIKE', "%{$search}%");
                })->orWhereHas('penggorenganData.produk', function($qp) use ($search) {
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
            $shiftId = $item->penggorenganData->shift_id ?? $item->getShiftDataAttribute()->shift_id ?? 'no_shift';
            return $dateStr . '_' . $shiftId;
        });

        $records->setCollection($groupedData);

        return view("{$this->auditViewPrefix()}.index_audit", compact('records', 'search', 'perPage'));
    }

    /**
     * Load data frayer 1–5 secara batch untuk menghindari N+1 query.
     * Juga pre-load approver users dan generate QR code per record di sini
     * agar tidak dilakukan berulang kali di dalam view (penyebab timeout DomPDF).
     *
     * @param \Illuminate\Support\Collection $data
     * @return \Illuminate\Support\Collection
     */
    private function loadFrayerDataBatched(\Illuminate\Support\Collection $data): \Illuminate\Support\Collection
    {
        // --- 1. Batch load frayer 1-5 ---
        $frayer1Uuids = $data->pluck('frayer_uuid')->filter()->unique()->values();
        $frayer2Uuids = $data->pluck('frayer2_uuid')->filter()->unique()->values();
        $frayer3Uuids = $data->pluck('frayer3_uuid')->filter()->unique()->values();
        $frayer4Uuids = $data->pluck('frayer4_uuid')->filter()->unique()->values();
        $frayer5Uuids = $data->pluck('frayer5_uuid')->filter()->unique()->values();

        $frayer1Map = ProsesFrayer::whereIn('uuid', $frayer1Uuids)
            ->with(['produk', 'suhuFrayer', 'waktuPenggorengan'])
            ->get()->keyBy('uuid');

        $frayer2Map = Frayer2::whereIn('uuid', $frayer2Uuids)
            ->with(['produk', 'suhuFrayer2', 'waktuPenggorengan2'])
            ->get()->keyBy('uuid');

        $frayer3Map = Frayer3::whereIn('uuid', $frayer3Uuids)
            ->with(['produk', 'suhuFrayer', 'waktuPenggorengan'])
            ->get()->keyBy('uuid');

        $frayer4Map = Frayer4::whereIn('uuid', $frayer4Uuids)
            ->with(['produk', 'suhuFrayer', 'waktuPenggorengan'])
            ->get()->keyBy('uuid');

        $frayer5Map = Frayer5::whereIn('uuid', $frayer5Uuids)
            ->with(['produk', 'suhuFrayer', 'waktuPenggorengan'])
            ->get()->keyBy('uuid');

        // --- 2. Batch load approver users (1 query) ---
        $approverIds = collect()
            ->merge($data->pluck('qc_approved_by'))
            ->merge($data->pluck('produksi_approved_by'))
            ->merge($data->pluck('spv_approved_by'))
            ->filter()
            ->unique()
            ->values();

        $approverMap = \App\Models\User::whereIn('id', $approverIds)
            ->get()->keyBy('id');

        // --- 3. Map semua data ke setiap record ---
        return $data->map(function ($item) use ($frayer1Map, $frayer2Map, $frayer3Map, $frayer4Map, $frayer5Map, $approverMap) {
            // Map frayer
            $item->frayer1 = $item->frayer_uuid  ? ($frayer1Map[$item->frayer_uuid]  ?? null) : null;
            $item->frayer2 = $item->frayer2_uuid ? ($frayer2Map[$item->frayer2_uuid] ?? null) : null;
            $item->frayer3 = $item->frayer3_uuid ? ($frayer3Map[$item->frayer3_uuid] ?? null) : null;
            $item->frayer4 = $item->frayer4_uuid ? ($frayer4Map[$item->frayer4_uuid] ?? null) : null;
            $item->frayer5 = $item->frayer5_uuid ? ($frayer5Map[$item->frayer5_uuid] ?? null) : null;

            // Pre-compute approval data & QR codes (agar tidak dilakukan di view)
            $approvalData = [
                'qc_name'       => 'QC',
                'spv_name'      => 'SUPERVISOR QC',
                'produksi_name' => 'SPV PRODUKSI',
                'qc_qr'         => null,
                'spv_qr'        => null,
                'produksi_qr'   => null,
            ];

            if ($item->approved_by_qc && $item->qc_approved_by) {
                $user = $approverMap[$item->qc_approved_by] ?? null;
                if ($user) {
                    $approvalData['qc_name'] = $user->name;
                    if ($item->qc_approved_at) {
                        $date = \Carbon\Carbon::parse($item->qc_approved_at)->format('Y-m-d');
                        $approvalData['qc_qr'] = 'data:image/svg+xml;base64,' . base64_encode(
                            \SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate(
                                "Dokumen ini telah diverifikasi secara sistem oleh {$user->name} (Tim QC) pada {$date}"
                            )
                        );
                    }
                }
            }

            if ($item->approved_by_produksi && $item->produksi_approved_by) {
                $user = $approverMap[$item->produksi_approved_by] ?? null;
                if ($user) {
                    $approvalData['produksi_name'] = $user->name;
                    if ($item->produksi_approved_at) {
                        $date = \Carbon\Carbon::parse($item->produksi_approved_at)->format('Y-m-d');
                        $approvalData['produksi_qr'] = 'data:image/svg+xml;base64,' . base64_encode(
                            \SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate(
                                "Dokumen ini telah diverifikasi secara sistem oleh {$user->name} (SPV PRODUKSI) pada {$date}"
                            )
                        );
                    }
                }
            }

            if ($item->approved_by_spv && $item->spv_approved_by) {
                $user = $approverMap[$item->spv_approved_by] ?? null;
                if ($user) {
                    $approvalData['spv_name'] = $user->name;
                    if ($item->spv_approved_at) {
                        $date = \Carbon\Carbon::parse($item->spv_approved_at)->format('Y-m-d');
                        $approvalData['spv_qr'] = 'data:image/svg+xml;base64,' . base64_encode(
                            \SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate(
                                "Dokumen ini telah diverifikasi secara sistem oleh {$user->name} (SPV QC) pada {$date}"
                            )
                        );
                    }
                }
            }

            $item->_approvalData = $approvalData;

            return $item;
        });
    }


    public function copyToAudit($uuid)
    {
        $item = PembekuanIqfPenggorengan::where('uuid', $uuid)->firstOrFail();

        // Jika item yang diklik sudah merupakan record audit
        if ($item->is_audit) {
            return redirect()->route('wizard.proses1.edit', $item->uuid);
        }

        // Jika data operasional ini sudah memiliki versi audit sebelumnya
        if ($item->auditVersion) {
            return redirect()->route('wizard.proses1.edit', $item->auditVersion->uuid);
        }

        // Jika belum memiliki versi audit, buatkan salinan audit secara otomatis
        $clone = $item->copyToAudit();

        return redirect()->route('wizard.proses1.edit', $clone->uuid)
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
            $query = PembekuanIqfPenggorengan::whereIn('uuid', $uuids)
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

            PembekuanIqfPenggorengan::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->where('is_audit', false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();

            // Update audit copies with same approval data
            PembekuanIqfPenggorengan::where('is_audit', true)
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
