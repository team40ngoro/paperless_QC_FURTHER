<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasAuditController;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Models\BagianMesin;
use App\Models\DataShift;
use App\Models\InputArea;
use App\Models\InputMesinPeralatan;
use App\Models\Plan;
use App\Models\VerifPeralatan;
use App\Models\VerifPeralatanBagian;
use App\Models\VerifPeralatanDetail;
use App\Services\BulkExport\VerifPeralatanBulkExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VerifPeralatanController extends Controller
{
    use HasAuditController, HasNoDataHtmlResponse;

    protected function auditModel(): string
    {
        return VerifPeralatan::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'verif-peralatan';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.verif_peralatan';
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
        return 'id_shift';
    }

    protected function getAuditEagerLoads(): array
    {
        return ['plan', 'user', 'shift', 'details.mesin.area', 'details.mesin.bagian'];
    }

    private function normalizeVerifikasi($value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        $str = is_string($value) ? trim($value) : $value;

        if ($str === 1 || $str === '1' || $str === 'OK' || $str === 'ok') {
            return true;
        }

        if ($str === 0 || $str === '0' || $str === 'tidak ok' || $str === 'tidak_ok' || $str === 'kotor') {
            return false;
        }

        return null;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Auditor langsung diarahkan ke halaman audit
        if ($user->id_role == 6) {
            return redirect()->route('verif-peralatan.audit');
        }

        $perPage = (int) $request->input('per_page', 5);
        if (! in_array($perPage, [5, 10, 25, 50])) {
            $perPage = 5;
        }

        $query = VerifPeralatan::with(['plan', 'user', 'shift', 'details.mesin.area', 'details.mesin.bagian'])
            ->operasional();

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search = $request->input('search')) {
            $query->where('tanggal', 'LIKE', '%'.$search.'%');
        }

        $this->applyDateRangeFilter(request(), $query);
        $items = $query->orderByDesc('tanggal')
            ->orderByDesc('jam')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $shifts = DataShift::when($user->role !== 'superadmin', fn ($q) => $q->where('id_plan', $user->id_plan))
            ->orderBy('shift')
            ->get();

        return view('qc-sistem.verif_peralatan.index', compact('items', 'perPage', 'shifts'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        $plans = null;
        $selectedPlanId = $user->id_plan;

        if ($user->role === 'superadmin') {
            $plans = Plan::orderBy('nama_plan')->get();
            $selectedPlanId = $request->get('plan_id') ?: old('id_plan') ?: ($plans->first()->id ?? null);
        }

        if (! $selectedPlanId) {
            abort(404);
        }

        $shifts = DataShift::where('id_plan', $selectedPlanId)->orderBy('shift')->get();

        $mesinsQuery = InputMesinPeralatan::with(['area', 'bagian'])
            ->where('id_plan', $selectedPlanId);

        $areasQuery = InputArea::with(['subarea'])
            ->where('id_plan', $selectedPlanId);

        // if ($user->isProduksi()) {
        //     $areasQuery->whereRaw('LOWER(area) NOT LIKE ?', ['%seasoning%']);
        //     $mesinsQuery->whereHas('area', function ($q) {
        //         $q->whereRaw('LOWER(area) NOT LIKE ?', ['%seasoning%']);
        //     });
        // }

        $mesins = $mesinsQuery->orderBy('nama_mesin')->get();
        $areas = $areasQuery->orderBy('area')->get();

        return view('qc-sistem.verif_peralatan.create', [
            'plans' => $plans,
            'selectedPlanId' => $selectedPlanId,
            'shifts' => $shifts,
            'mesins' => $mesins,
            'areas' => $areas,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'nullable|date_format:H:i',
        ];

        if ($user->role === 'superadmin') {
            $rules['id_plan'] = 'required|exists:plan,id';
        }

        $data = $request->validate($rules);

        $planId = $user->role === 'superadmin' ? (int) $data['id_plan'] : (int) $user->id_plan;

        $shift = DataShift::where('id', $data['id_shift'])->where('id_plan', $planId)->first();
        if (! $shift) {
            return back()->withErrors(['id_shift' => 'Shift tidak valid untuk plan Anda.'])->withInput();
        }

        $detailsInput = $request->input('details', []);
        if (! is_array($detailsInput) || empty($detailsInput)) {
            return back()->withErrors(['error' => 'Silakan pilih minimal 1 Area terlebih dahulu sebelum menyimpan data.'])->withInput();
        }

        // ─── Validasi backend: minimal 1 kondisi harus diisi ─────────────────
        $hasAnyVerifikasi = false;
        foreach ($detailsInput as $detail) {
            $v = $detail['verifikasi'] ?? '';
            if ($v !== '' && $v !== null) {
                $hasAnyVerifikasi = true;
                break;
            }
            foreach ($detail['bagian'] ?? [] as $bagianData) {
                $bv = $bagianData['verifikasi'] ?? '';
                if ($bv !== '' && $bv !== null) {
                    $hasAnyVerifikasi = true;
                    break 2;
                }
            }
        }

        if (! $hasAnyVerifikasi) {
            return back()
                ->withErrors(['error' => 'Silakan isi minimal 1 kondisi (Bersih/Kotor) pada area yang dipilih sebelum menyimpan data.'])
                ->withInput();
        }

        $areaJams = $request->input('area_jam', []);
        $firstJam = collect($areaJams)->first() ?? now()->format('H:i');

        // Seluruh proses simpan (header + detail + bagian) dibungkus dalam 1 transaksi
        // agar data konsisten: jika ada kegagalan di tengah proses, semua di-rollback.
        $header = DB::transaction(function () use ($data, $user, $planId, $detailsInput, $areaJams, $firstJam) {

            // ─── Buat header verifikasi ──────────────────────────────────────────
            $header = VerifPeralatan::create([
                'id_plan' => $planId,
                'user_id' => $user->id,
                'id_shift' => (int) $data['id_shift'],
                'tanggal' => $data['tanggal'],
                'jam' => $firstJam,
            ]);

            if (! is_array($detailsInput) || empty($detailsInput)) {
                return $header;
            }

            $now = now();
            $mesinIds = array_keys($detailsInput);

            // ─── 1. Pre-fetch semua mesin valid dalam 1 query (hindari N+1) ─────
            $mesins = InputMesinPeralatan::with('area')->whereIn('id', $mesinIds)
                ->where('id_plan', $planId)
                ->get()
                ->keyBy('id');

            // ─── 2. Siapkan array detail rows, di-index oleh mesinId ─────────────
            $detailRows = [];

            foreach ($detailsInput as $mesinId => $detail) {
                $mesin = $mesins->get($mesinId);
                if (! $mesin) {
                    continue;
                }

                // Proteksi role produksi: tolak/skip mesin dari area seasoning
                // if ($user->isProduksi() && $mesin->area && str_contains(strtolower($mesin->area->area), 'seasoning')) {
                //     continue;
                // }

                $keterangan = $detail['keterangan'] ?? null;
                if ($keterangan === '__lainnya__') {
                    $keterangan = $detail['keterangan_manual'] ?? null;
                }

                $verifikasi = $this->normalizeVerifikasi($detail['verifikasi'] ?? null);

                // Role produksi TIDAK BOLEH mengisi verifikasi_hasil (otomatis null)
                if ($user->isProduksi()) {
                    $verifHasil = null;
                } else {
                    $verifHasil = $this->normalizeVerifikasi($detail['verifikasi_hasil'] ?? null);
                }

                if ($verifikasi === null) {
                    if (isset($detail['bagian']) && is_array($detail['bagian']) && count($detail['bagian']) > 0) {
                        $hasKotor = false;
                        foreach ($detail['bagian'] as $bData) {
                            if ($this->normalizeVerifikasi($bData['verifikasi'] ?? null) === false) {
                                $hasKotor = true;
                                break;
                            }

                        }
                        $verifikasi = ! $hasKotor;
                    } else {
                        $verifikasi = true;
                    }
                }

                if (! $user->isProduksi() && $verifHasil === null) {
                    if (isset($detail['bagian']) && is_array($detail['bagian']) && count($detail['bagian']) > 0) {
                        $hasNotOk = false;
                        foreach ($detail['bagian'] as $bData) {
                            if ($this->normalizeVerifikasi($bData['verifikasi_hasil'] ?? null) === false) {
                                $hasNotOk = true;
                                break;
                            }

                        }
                        $verifHasil = ! $hasNotOk;
                    } else {
                        $verifHasil = true;
                    }
                }

                $detailJam = $areaJams[$mesin->id_area] ?? $firstJam;

                $detailRows[$mesinId] = [
                    'verif_peralatan_id' => $header->id,
                    'id_mesin' => (int) $mesin->id,
                    'id_area' => (int) $mesin->id_area,
                    'jam' => $detailJam,
                    'verifikasi' => $verifikasi,
                    'keterangan' => $keterangan,
                    'tindakan_koreksi' => $detail['tindakan_koreksi'] ?? null,
                    'verifikasi_hasil' => $verifHasil,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // ─── 3. Bulk insert detail dalam chunks @50 baris ───────────────────
            $detailTable = (new VerifPeralatanDetail)->getTable();
            foreach (array_chunk(array_values($detailRows), 50) as $chunk) {
                DB::table($detailTable)->insert($chunk);
            }

            // ─── 4. Query balik untuk mendapatkan ID detail yang baru di-insert ─
            //        (diperlukan sebagai FK untuk bagian)
            $savedDetails = VerifPeralatanDetail::where('verif_peralatan_id', $header->id)
                ->get()
                ->keyBy('id_mesin');

            // ─── 5. Kumpulkan semua bagian ID, pre-fetch dalam 1 query ───────────
            $allBagianIds = [];
            foreach ($detailsInput as $detail) {
                foreach (array_keys($detail['bagian'] ?? []) as $bid) {
                    $allBagianIds[] = (int) $bid;
                }
            }

            $bagianMesins = collect();
            if (! empty($allBagianIds)) {
                $bagianMesins = BagianMesin::whereIn('id', array_unique($allBagianIds))
                    ->get()
                    ->keyBy('id');
            }

            // ─── 6. Siapkan array bagian rows ────────────────────────────────────
            $bagianRows = [];

            foreach ($detailsInput as $mesinId => $detail) {
                $savedDetail = $savedDetails->get($mesinId);
                if (! $savedDetail) {
                    continue;
                }

                $bagianInput = $detail['bagian'] ?? [];
                if (! is_array($bagianInput)) {
                    continue;
                }

                foreach ($bagianInput as $bagianId => $bagianData) {
                    $bagianMesin = $bagianMesins->get($bagianId);
                    // Validasi: bagian harus milik mesin yang benar
                    if (! $bagianMesin || (int) $bagianMesin->id_mesin !== (int) $mesinId) {
                        continue;
                    }

                    $bagianKet = $bagianData['keterangan'] ?? null;
                    if ($bagianKet === '__lainnya__') {
                        $bagianKet = $bagianData['keterangan_manual'] ?? null;
                    }

                    $bagianVerifikasi = $this->normalizeVerifikasi($bagianData['verifikasi'] ?? null);

                    // Skip bagian yang kondisinya tidak diisi (-- / kosong)
                    if ($bagianVerifikasi === null) {
                        continue;
                    }

                    $bagianHasil = $user->isProduksi() ? null : ($this->normalizeVerifikasi($bagianData['verifikasi_hasil'] ?? null) ?? true);

                    $bagianRows[] = [
                        'verif_peralatan_detail_id' => $savedDetail->id,
                        'id_bagian' => (int) $bagianMesin->id,
                        'verifikasi' => $bagianVerifikasi,
                        'keterangan' => $bagianKet,
                        'tindakan_koreksi' => $bagianData['tindakan_koreksi'] ?? null,
                        'verifikasi_hasil' => $bagianHasil,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            // ─── 7. Bulk insert bagian dalam chunks @100 baris ──────────────────
            if (! empty($bagianRows)) {
                $bagianTable = (new VerifPeralatanBagian)->getTable();
                foreach (array_chunk($bagianRows, 100) as $chunk) {
                    DB::table($bagianTable)->insert($chunk);
                }
            }

            return $header;
        });

        return redirect()->route('verif-peralatan.show', $header->uuid)
            ->with('success', 'Data verifikasi peralatan berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $user = Auth::user();

        $item = VerifPeralatan::with([
            'plan',
            'user',
            'shift',
            'details.mesin.area', 'details.mesin.bagian',
            'details.bagianDetails.bagian',
        ])->where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('verif-peralatan.show', $item)) {
            return $redirect;
        }

        $detailsByArea = $item->details
            ->sortBy(function ($d) {
                $area = $d->mesin->area->area ?? '';
                $mesin = $d->mesin->nama_mesin ?? '';

                return $area.'|'.$mesin;
            })
            ->groupBy('id_area');

        return view('qc-sistem.verif_peralatan.show', compact('item', 'detailsByArea'));
    }

    public function edit($uuid)
    {
        $user = Auth::user();

        $item = VerifPeralatan::with([
            'details.bagianDetails',
            'details.mesin.area',
            'details.mesin.bagian',
        ])->where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $isAuditMode = (bool) $item->is_audit;
        $queryParameters = request()->query();
        $hasAuditFlag = array_key_exists('is_audit', $queryParameters);

        if (($isAuditMode && request()->query('is_audit') !== '1') || (! $isAuditMode && $hasAuditFlag)) {
            unset($queryParameters['is_audit'], $queryParameters['uuid']);
            if ($isAuditMode) {
                $queryParameters['is_audit'] = '1';
            }

            return redirect()->route(
                'verif-peralatan.edit',
                array_merge(['uuid' => $item->uuid], $queryParameters)
            );
        }

        $plans = null;
        $selectedPlanId = (int) $item->id_plan;

        if ($user->role === 'superadmin') {
            $plans = Plan::orderBy('nama_plan')->get();
        }

        $shifts = DataShift::where('id_plan', $selectedPlanId)->orderBy('shift')->get();

        $mesinsQuery = InputMesinPeralatan::with(['area', 'bagian'])
            ->where('id_plan', $selectedPlanId);

        $areasQuery = InputArea::where('id_plan', $selectedPlanId);

        // if ($user->isProduksi()) {
        //     $areasQuery->whereRaw('LOWER(area) NOT LIKE ?', ['%seasoning%']);
        //     $mesinsQuery->whereHas('area', function ($q) {
        //         $q->whereRaw('LOWER(area) NOT LIKE ?', ['%seasoning%']);
        //     });
        // }

        $mesins = $mesinsQuery->orderBy('nama_mesin')->get();
        $areas = $areasQuery->orderBy('area')->get();

        $existing = $item->details->keyBy(function ($d) {
            return $d->id_mesin;
        });

        $savedAreaIds = $item->details
            ->map(function ($d) {
                return (string) ($d->id_area ?: ($d->mesin->id_area ?? null));
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        return view('qc-sistem.verif_peralatan.edit', [
            'item' => $item,
            'plans' => $plans,
            'selectedPlanId' => $selectedPlanId,
            'shifts' => $shifts,
            'mesins' => $mesins,
            'areas' => $areas,
            'existing' => $existing,
            'savedAreaIds' => $savedAreaIds,
        ]);
    }

    public function update(Request $request, $uuid)
    {
        $user = Auth::user();

        $item = VerifPeralatan::with(['details.bagianDetails', 'details.mesin.area'])->where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        // Khusus role QC Inspector: hanya boleh mengedit verifikasi_hasil, selain itu read-only
        if ($user->isQcInspector()) {
            $detailsInput = $request->input('details', []);
            if (is_array($detailsInput)) {
                foreach ($item->details as $detailRow) {
                    $mesinId = $detailRow->id_mesin;
                    if (isset($detailsInput[$mesinId])) {
                        $detailData = $detailsInput[$mesinId];
                        if (array_key_exists('verifikasi_hasil', $detailData)) {
                            $detailRow->verifikasi_hasil = $this->normalizeVerifikasi($detailData['verifikasi_hasil']);
                            $detailRow->save();
                        }

                        if (isset($detailData['bagian']) && is_array($detailData['bagian'])) {
                            foreach ($detailRow->bagianDetails as $bagianRow) {
                                $bid = $bagianRow->id_bagian;
                                if (isset($detailData['bagian'][$bid]) && array_key_exists('verifikasi_hasil', $detailData['bagian'][$bid])) {
                                    $bagianRow->verifikasi_hasil = $this->normalizeVerifikasi($detailData['bagian'][$bid]['verifikasi_hasil']);
                                    $bagianRow->save();
                                }
                            }
                        }

                        // Agregasi verifikasi_hasil mesin induk dari sub-bagian (jika ada).
                        // Refresh koleksi dari DB agar nilai yang baru tersimpan terbaca.
                        if ($detailRow->bagianDetails->isNotEmpty()) {
                            $freshBagian = $detailRow->bagianDetails()->get();
                            $hasNull = false;
                            $aggregated = true;
                            foreach ($freshBagian as $bag) {
                                if (is_null($bag->verifikasi_hasil)) {
                                    $hasNull = true;
                                    break;
                                }
                                if ($bag->verifikasi_hasil === false) {
                                    $aggregated = false;
                                    break;
                                }
                            }
                            // Hanya update parent jika semua sub-bagian sudah di-set (tidak ada null).
                            if (! $hasNull) {
                                $detailRow->verifikasi_hasil = $aggregated;
                                $detailRow->save();
                            }
                        }
                    }
                }
            }

            if ($user->id_role == 6 || $item->is_audit) {
                return redirect()->route('verif-peralatan.audit')
                    ->with('success', 'Verifikasi hasil berhasil diperbarui oleh QC Inspector.');
            }

            return redirect()->route('verif-peralatan.show', $item->uuid)
                ->with('success', 'Verifikasi hasil berhasil diperbarui oleh QC Inspector.');
        }

        $rules = [
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'nullable|date_format:H:i',
        ];

        $data = $request->validate($rules);

        $planId = (int) $item->id_plan;

        $shift = DataShift::where('id', $data['id_shift'])->where('id_plan', $planId)->first();
        if (! $shift) {
            return back()->withErrors(['id_shift' => 'Shift tidak valid untuk plan data ini.'])->withInput();
        }

        $areaJams = $request->input('area_jam', []);
        $firstJam = collect($areaJams)->first() ?? $item->jam ?? now()->format('H:i');

        $updateData = [
            'id_shift' => (int) $data['id_shift'],
            'tanggal' => $data['tanggal'],
            'jam' => $firstJam,
        ];

        $item->update($updateData);

        $isProduksi = $user->isProduksi();

        // Ambil data detail & bagian yang sudah ada sebelum dihapus
        $existingDetails = $item->details->keyBy('id_mesin');
        $existingBagians = collect();
        foreach ($item->details as $d) {
            foreach ($d->bagianDetails as $bd) {
                $existingBagians->put($bd->id_bagian, $bd);
            }
        }

        if ($isProduksi) {
            // Untuk user produksi, simpan detail yang berada di area seasoning agar tidak ikut terhapus
            $preservedSeasoningDetailIds = $item->details->filter(function ($d) {
                return $d->mesin && $d->mesin->area && str_contains(strtolower($d->mesin->area->area), 'seasoning');
            })->pluck('id')->all();

            VerifPeralatanDetail::where('verif_peralatan_id', $item->id)
                ->whereNotIn('id', $preservedSeasoningDetailIds)
                ->delete();
        } else {
            VerifPeralatanDetail::where('verif_peralatan_id', $item->id)->delete();
        }

        $detailsInput = $request->input('details', []);

        if (is_array($detailsInput)) {
            foreach ($detailsInput as $mesinId => $detail) {
                $mesin = InputMesinPeralatan::with('area')->where('id', $mesinId)->where('id_plan', $planId)->first();

                if (! $mesin) {
                    continue;
                }

                // Proteksi untuk role produksi: jangan proses mesin dari area seasoning
                if ($isProduksi && $mesin->area && str_contains(strtolower($mesin->area->area), 'seasoning')) {
                    continue;
                }

                $verifikasiRaw = $detail['verifikasi'] ?? null;
                $verifikasi = $this->normalizeVerifikasi($verifikasiRaw);
                $keterangan = $detail['keterangan'] ?? null;
                if ($keterangan === '__lainnya__') {
                    $keterangan = $detail['keterangan_manual'] ?? null;
                }
                $tindakan = $detail['tindakan_koreksi'] ?? null;

                // Jika user adalah Produksi: pertahankan verifikasi_hasil lama (gaboleh isi / edit)
                if ($isProduksi) {
                    $verifHasil = $existingDetails->get($mesinId)?->verifikasi_hasil ?? null;
                } else {
                    $verifHasilRaw = $detail['verifikasi_hasil'] ?? null;
                    $verifHasil = $this->normalizeVerifikasi($verifHasilRaw);
                }

                if ($verifikasi === null) {
                    if (isset($detail['bagian']) && is_array($detail['bagian']) && count($detail['bagian']) > 0) {
                        $hasKotor = false;
                        foreach ($detail['bagian'] as $bData) {
                            if ($this->normalizeVerifikasi($bData['verifikasi'] ?? null) === false) {
                                $hasKotor = true;
                                break;
                            }

                        }
                        $verifikasi = ! $hasKotor;
                    } else {
                        $verifikasi = true;
                    }
                }

                if (! $isProduksi && $verifHasil === null) {
                    if (isset($detail['bagian']) && is_array($detail['bagian']) && count($detail['bagian']) > 0) {
                        $hasNotOk = false;
                        foreach ($detail['bagian'] as $bData) {
                            if ($this->normalizeVerifikasi($bData['verifikasi_hasil'] ?? null) === false) {
                                $hasNotOk = true;
                                break;
                            }

                        }
                        $verifHasil = ! $hasNotOk;
                    } else {
                        $verifHasil = true;
                    }
                }

                $detailJam = $areaJams[$mesin->id_area] ?? $firstJam;

                $detailRow = VerifPeralatanDetail::create([
                    'verif_peralatan_id' => $item->id,
                    'id_mesin' => (int) $mesin->id,
                    'id_area' => (int) $mesin->id_area,
                    'jam' => $detailJam,
                    'verifikasi' => $verifikasi,
                    'keterangan' => $keterangan,
                    'tindakan_koreksi' => $tindakan,
                    'verifikasi_hasil' => $verifHasil,
                ]);

                $bagianInput = $detail['bagian'] ?? [];
                if (is_array($bagianInput)) {
                    foreach ($bagianInput as $bagianId => $bagianDetail) {
                        $bagianMesin = BagianMesin::where('id', $bagianId)->where('id_mesin', $mesinId)->first();
                        if (! $bagianMesin) {
                            continue;
                        }

                        $bagianVerifRaw = $bagianDetail['verifikasi'] ?? null;
                        $bagianVerif = $this->normalizeVerifikasi($bagianVerifRaw) ?? true;
                        $bagianKet = $bagianDetail['keterangan'] ?? null;
                        if ($bagianKet === '__lainnya__') {
                            $bagianKet = $bagianDetail['keterangan_manual'] ?? null;
                        }
                        $bagianTindakan = $bagianDetail['tindakan_koreksi'] ?? null;

                        // Jika user adalah Produksi: pertahankan verifikasi_hasil lama pada bagian
                        if ($isProduksi) {
                            $bagianHasil = $existingBagians->get($bagianId)?->verifikasi_hasil ?? null;
                        } else {
                            $bagianHasilRaw = $bagianDetail['verifikasi_hasil'] ?? null;
                            $bagianHasil = $this->normalizeVerifikasi($bagianHasilRaw) ?? true;
                        }

                        VerifPeralatanBagian::create([
                            'verif_peralatan_detail_id' => $detailRow->id,
                            'id_bagian' => (int) $bagianMesin->id,
                            'verifikasi' => $bagianVerif,
                            'keterangan' => $bagianKet,
                            'tindakan_koreksi' => $bagianTindakan,
                            'verifikasi_hasil' => $bagianHasil,
                        ]);
                    }
                }
            }
        }

        if ($user->id_role == 6 || $item->is_audit) {
            return redirect()->route('verif-peralatan.audit')
                ->with('success', 'Data verifikasi peralatan berhasil diperbarui.');
        }

        return redirect()->route('verif-peralatan.show', $item->uuid)
            ->with('success', 'Data verifikasi peralatan berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $user = Auth::user();

        $item = VerifPeralatan::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $item->delete();

        return redirect()->route('verif-peralatan.index')
            ->with('success', 'Data verifikasi peralatan berhasil dihapus.');
    }

    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal_awal' => 'nullable|date',
                'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
                'id_shift' => 'nullable|exists:data_shift,id',
                'kode_form' => 'required|string|max:50',
            ]);

            $user = Auth::user();

            $query = VerifPeralatan::with(['plan', 'user', 'shift', 'details.mesin.area', 'details.mesin.bagian', 'details.bagianDetails.bagian']);

            if ($request->has('is_audit') && $request->is_audit == 1) {
                $query->where('is_audit', true);
            } else {
                $query->operasional();
            }

            $query->when($user->role !== 'superadmin', function ($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

            if ($request->tanggal_awal && $request->tanggal_akhir) {
                $tanggalAwal = Carbon::parse($request->tanggal_awal)->startOfDay();
                $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
                $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            } elseif ($request->tanggal_awal) {
                $query->whereDate('tanggal', $request->tanggal_awal);
            }

            if ($request->id_shift) {
                $query->where('id_shift', $request->id_shift);
            }

            $data = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();

            if ($data->isNotEmpty()) {
                $query->update(['kode_form' => $request->kode_form]);
            }

            if ($data->isEmpty()) {
                $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
                $filterInfo = [];

                if ($request->tanggal_awal && $request->tanggal_akhir) {
                    $filterInfo[] = 'Tanggal: '.Carbon::parse($request->tanggal_awal)->format('d-m-Y').' s/d '.Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
                } elseif ($request->tanggal_awal) {
                    $filterInfo[] = 'Tanggal: '.Carbon::parse($request->tanggal_awal)->format('d-m-Y');
                }
                if ($request->id_shift) {
                    $shift = DataShift::find($request->id_shift);
                    $filterInfo[] = 'Shift: '.($shift ? $shift->shift : 'Unknown');
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
                        <p class="message">'.$errorMessage.'</p>';

                if (! empty($filterInfo)) {
                    $html .= '
                        <div class="filter-info">
                            <h4>Filter yang digunakan:</h4>
                            <ul>';
                    foreach ($filterInfo as $info) {
                        $html .= '<li>'.$info.'</li>';
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

            $kode_form = $request->kode_form;

            // Prepare filters for PDF view
            $tanggalFilter = 'Semua Tanggal';
            if ($request->tanggal_awal && $request->tanggal_akhir) {
                $tanggalFilter = Carbon::parse($request->tanggal_awal)->format('d-m-Y').' s/d '.Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
            } elseif ($request->tanggal_awal) {
                $tanggalFilter = Carbon::parse($request->tanggal_awal)->format('d-m-Y');
            }

            $filters = [
                'tanggal' => $tanggalFilter,
                'shift_id' => $request->id_shift,
                'kode_form' => $request->kode_form,
            ];

            $pdf = \PDF::loadView('qc-sistem.verif_peralatan.export_pdf', compact('data', 'kode_form', 'filters'));
            $pdf->setPaper('A4', 'landscape');

            $filename = 'verif_peralatan_'.date('Y-m-d_H-i-s').'.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: '.$e->getMessage(),
            ], 500);
        }
    }

    public function exportPdf($uuid)
    {
        $user = Auth::user();
        $item = VerifPeralatan::with(['plan', 'user', 'shift', 'details.mesin.area', 'details.mesin.bagian'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $data = collect([$item]);
        $kode_form = $item->kode_form ?? 'QF 26/00';

        $pdf = \PDF::loadView('qc-sistem.verif_peralatan.export_pdf', compact('data', 'kode_form'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'verif_peralatan_'.($item->tanggal ? Carbon::parse($item->tanggal)->format('Y-m-d') : 'data').'_'.substr($uuid, 0, 8).'.pdf';

        return $pdf->stream($filename);
    }

    public function approve(Request $request, $uuid)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv',
            ]);

            $item = VerifPeralatan::where('uuid', $uuid)->firstOrFail();
            $user = Auth::user();
            $userRole = $user->id_role ?? null;

            if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk data ini',
                ], 403);
            }

            $type = $request->type;
            switch ($type) {
                case 'qc':
                    if (! in_array($userRole, [1, 3, 5])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai QC',
                        ], 403);
                    }
                    break;
                case 'produksi':
                    if ($userRole != 2) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai Produksi',
                        ], 403);
                    }
                    if (! $item->approved_by_qc) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data harus disetujui oleh QC terlebih dahulu',
                        ], 400);
                    }
                    break;
                case 'spv':
                    if ($userRole != 4) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai SPV',
                        ], 403);
                    }
                    if (! $item->approved_by_produksi) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data harus disetujui oleh Produksi terlebih dahulu',
                        ], 400);
                    }
                    break;
            }

            $approvalField = "approved_by_{$type}";
            if ($item->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui sebelumnya',
                ], 400);
            }

            $updateData = [
                $approvalField => true,
                "approved_by_{$type}_at" => now(),
                "approved_by_{$type}_user_id" => $user->id,
            ];

            $item->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui',
                'data' => $item->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    public function bulkApprove(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'shift_id' => 'required|exists:data_shift,id',
                'type' => 'required|in:qc,produksi,spv',
            ]);

            $user = Auth::user();
            $type = $request->type;
            $tanggal = $request->tanggal;
            $id_shift = $request->shift_id;
            $id_area = $request->id_area;

            $items = VerifPeralatan::whereDate('tanggal', $tanggal)
                ->where('id_shift', $id_shift)
                ->where('id_plan', $user->id_plan)
                ->when($id_area && $id_area !== 'no_area', function ($q) use ($id_area) {
                    $q->whereHas('details.mesin', function ($query) use ($id_area) {
                        $query->where('id_area', $id_area);
                    });
                })
                ->get();

            if ($items->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data untuk disetujui.']);
            }

            foreach ($items as $item) {
                // Sequential validation
                if ($type === 'produksi' && ! $item->approved_by_qc) {
                    continue;
                }
                if ($type === 'spv' && ! $item->approved_by_produksi) {
                    continue;
                }

                $approvalField = "approved_by_{$type}";
                if (! $item->$approvalField) {
                    $item->update([
                        $approvalField => true,
                        "approved_by_{$type}_at" => now(),
                        "approved_by_{$type}_user_id" => $user->id,
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Berhasil menyetujui data secara massal.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function bulkApproveSelected(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv',
                'uuids' => 'required|array|min:1',
                'uuids.*' => 'required|uuid',
            ]);

            $user = Auth::user();
            $type = $request->input('type');
            $uuids = $request->input('uuids', []);

            // Role-based permission check
            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5],
            ];

            if (! in_array($user->id_role, $allowedRoles[$type] ?? [])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki otoritas untuk verifikasi ini.',
                ], 403);
            }

            // Find records - ONLY unapproved ones (smart auto-skip)
            $query = VerifPeralatan::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function ($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            $itemsToUpdate = $query->get();
            $count = $itemsToUpdate->count();

            if ($count === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Semua data sudah diverifikasi sebelumnya.',
                ]);
            }

            // Update operasional records
            $updateData = [
                "approved_by_{$type}" => true,
                "approved_by_{$type}_user_id" => $user->id,
                "approved_by_{$type}_at" => now(),
            ];

            VerifPeralatan::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function ($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();

            // Update audit copies with same approval data
            VerifPeralatan::where('is_audit', true)
                ->whereIn('source_uuid', $updatedSourceUuids)
                ->when($user->role !== 'superadmin', function ($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => "Berhasil verifikasi {$count} data ".strtoupper($type),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function bulkExportExcel(Request $request, VerifPeralatanBulkExportService $service)
    {
        return $service->handle($request, fn ($msg) => $this->buildNoDataHtmlResponse($msg));
    }
}
