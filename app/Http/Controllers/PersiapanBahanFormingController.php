<?php

namespace App\Http\Controllers;

use App\Models\PersiapanBahanForming;
use App\Models\BahanForming;
use App\Models\SuhuForming;
use App\Models\JenisProduk;
use App\Models\DataShift;
use App\Models\AktualSuhuAdonan;
use App\Models\SuhuAdonan;
use App\Models\PersiapanBahanNonForming;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PersiapanBahanFormingExport;
use App\Models\PersiapanBahanFormingLog;
use App\Http\Controllers\Traits\HasAuditController;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Services\BulkExport\PersiapanBahanFormingBulkExportService;

class PersiapanBahanFormingController extends Controller
{
    use HasAuditController, HasNoDataHtmlResponse;

    protected function auditModel(): string
    {
        return PersiapanBahanForming::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'persiapan-bahan-forming';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.persiapan_bahan_forming';
    }

    protected function auditRelations(): array
    {
        return ['plan', 'formula.produk', 'suhuForming.bahanForming', 'shift', 'aktualSuhuAdonan', 'suhuAdonan', 'user', 'qcApprover', 'produksiApprover', 'spvApprover', 'originalVersion'];
    }

    protected function auditSearchColumns(): array
    {
        return ['kode_produksi_emulsi', 'kondisi', 'catatan'];
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

    protected function auditPlanColumn(): string
    {
        return 'plan_id';
    }

    public function auditIndex(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);
        $page = (int) $request->get('page', 1);
        $perPage = (int) $perPage;
        if ($perPage <= 0) $perPage = 5;

        $auditedSourceUuids = PersiapanBahanForming::where('is_audit', true)->whereNotNull('source_uuid')->pluck('source_uuid')->toArray();

$query = PersiapanBahanForming::with(['plan', 'formula.produk', 'suhuForming.bahanForming', 'shift', 'aktualSuhuAdonan', 'suhuAdonan', 'user', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->where(function ($q) use ($auditedSourceUuids) {
            $q->where('is_audit', true)
              ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                  $subQ->where('is_audit', false);
                  if (!empty($auditedSourceUuids)) {
                      $subQ->whereNotIn('uuid', $auditedSourceUuids);
                  }
              });
        })
            ->when($user->role !== 'superadmin', fn($q) => $q->where('plan_id', $user->id_plan));

        $queryNon = PersiapanBahanNonForming::with(['plan', 'formulaNonForming.produk', 'shift', 'suhuAdonan', 'user'])
            ->where('is_audit', true)
            ->when($user->role !== 'superadmin', fn($q) => $q->where('plan_id', $user->id_plan));

        if ($search) {
            $query->where(fn($q) => $q->where('kode_produksi_emulsi', 'like', "%{$search}%")
                ->orWhere('kondisi', 'like', "%{$search}%")->orWhere('catatan', 'like', "%{$search}%"));
            $queryNon->where(fn($q) => $q->where('kode_produksi', 'like', "%{$search}%")
                ->orWhere('kondisi', 'like', "%{$search}%")->orWhere('catatan', 'like', "%{$search}%"));
        }

        $this->applyDateRangeFilter($request, $query);
        $this->applyDateRangeFilter($request, $queryNon);

        $dataForming = $query->orderBy('created_at', 'desc')->orderBy('id', 'desc')->limit(500)->get();
        $dataNonForming = $queryNon->orderBy('created_at', 'desc')->orderBy('id', 'desc')->limit(500)->get();

        $formingRecords = $dataForming->map(fn($item) => (object)[
            'jenis' => 'Forming', 'uuid' => $item->uuid,
            'shift' => $item->shift->shift ?? '-', 'shift_id' => $item->shift_id,
            'tanggal' => $item->tanggal, 'jam' => $item->jam,
            'nama_produk' => $item->formula->produk->nama_produk ?? '-',
            'berat_sampling' => $item->berat_sampling,
            'kode_produksi' => $item->kode_produksi_emulsi ?? '-',
            'nomor_formula' => $item->formula->nomor_formula ?? '-',
            'kondisi' => $item->kondisi, 'catatan' => $item->catatan,
            'dibuat_oleh' => $item->user->name ?? '-',
            'created_at' => $item->created_at,
            'approved_by_qc' => $item->approved_by_qc,
            'approved_by_produksi' => $item->approved_by_produksi,
            'approved_by_spv' => $item->approved_by_spv,
            'is_audit' => true,
        ]);

        $nonFormingRecords = $dataNonForming->map(fn($item) => (object)[
            'jenis' => 'Non Forming', 'uuid' => $item->uuid,
            'shift' => $item->shift->shift ?? '-', 'shift_id' => $item->shift_id,
            'tanggal' => $item->tanggal, 'jam' => $item->jam,
            'nama_produk' => $item->formulaNonForming->produk->nama_produk ?? '-',
            'berat_sampling' => $item->berat_sampling,
            'kode_produksi' => $item->kode_produksi ?? '-',
            'nomor_formula' => $item->formulaNonForming->nomor_formula ?? '-',
            'kondisi' => $item->kondisi, 'catatan' => $item->catatan,
            'dibuat_oleh' => $item->user->name ?? '-',
            'created_at' => $item->created_at,
            'approved_by_qc' => $item->approved_by_qc,
            'approved_by_produksi' => $item->approved_by_produksi,
            'approved_by_spv' => $item->approved_by_spv,
            'is_audit' => true,
        ]);

        $merged = $formingRecords->concat($nonFormingRecords)->sortByDesc('created_at')->values();
        $total = $merged->count();
        $items = $merged->slice(($page - 1) * $perPage, $perPage)->values();
        $groupedItems = $items->groupBy(fn($item) => ($item->tanggal ? (is_string($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : $item->tanggal->format('Y-m-d')) : 'no_date') . '_' . ($item->shift_id ?? 'no_shift'));

        $records = new LengthAwarePaginator($groupedItems, $total, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

        return view('qc-sistem.persiapan_bahan_forming.index_audit', compact('records', 'search', 'perPage'));
    }

    public function auditSearchAjax(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
        $perPage = (int) $request->get('per_page', 5);
        $page = (int) $request->get('page', 1);
        if ($perPage <= 0) $perPage = 5;

        $query = PersiapanBahanForming::with(['plan', 'formula.produk', 'suhuForming.bahanForming', 'shift', 'aktualSuhuAdonan', 'suhuAdonan', 'user', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->where('is_audit', true)
            ->when($user->role !== 'superadmin', fn($q) => $q->where('plan_id', $user->id_plan));

        $queryNon = PersiapanBahanNonForming::with(['plan', 'formulaNonForming.produk', 'shift', 'suhuAdonan', 'user'])
            ->where('is_audit', true)
            ->when($user->role !== 'superadmin', fn($q) => $q->where('plan_id', $user->id_plan));

        if ($search) {
            $query->where(fn($q) => $q->where('kode_produksi_emulsi', 'like', "%{$search}%")
                ->orWhere('kondisi', 'like', "%{$search}%")->orWhere('catatan', 'like', "%{$search}%"));
            $queryNon->where(fn($q) => $q->where('kode_produksi', 'like', "%{$search}%")
                ->orWhere('kondisi', 'like', "%{$search}%")->orWhere('catatan', 'like', "%{$search}%"));
        }

        $this->applyDateRangeFilter($request, $query);
        $this->applyDateRangeFilter($request, $queryNon);

        $dataForming = $query->orderBy('created_at', 'desc')->orderBy('id', 'desc')->limit(500)->get();
        $dataNonForming = $queryNon->orderBy('created_at', 'desc')->orderBy('id', 'desc')->limit(500)->get();

        $formingRecords = $dataForming->map(fn($item) => (object)[
            'jenis' => 'Forming', 'uuid' => $item->uuid,
            'shift' => $item->shift->shift ?? '-', 'shift_id' => $item->shift_id,
            'tanggal' => $item->tanggal, 'jam' => $item->jam,
            'nama_produk' => $item->formula->produk->nama_produk ?? '-',
            'berat_sampling' => $item->berat_sampling,
            'kode_produksi' => $item->kode_produksi_emulsi ?? '-',
            'nomor_formula' => $item->formula->nomor_formula ?? '-',
            'kondisi' => $item->kondisi, 'catatan' => $item->catatan,
            'dibuat_oleh' => $item->user->name ?? '-',
            'created_at' => $item->created_at,
            'approved_by_qc' => $item->approved_by_qc,
            'approved_by_produksi' => $item->approved_by_produksi,
            'approved_by_spv' => $item->approved_by_spv,
            'is_audit' => true,
        ]);

        $nonFormingRecords = $dataNonForming->map(fn($item) => (object)[
            'jenis' => 'Non Forming', 'uuid' => $item->uuid,
            'shift' => $item->shift->shift ?? '-', 'shift_id' => $item->shift_id,
            'tanggal' => $item->tanggal, 'jam' => $item->jam,
            'nama_produk' => $item->formulaNonForming->produk->nama_produk ?? '-',
            'berat_sampling' => $item->berat_sampling,
            'kode_produksi' => $item->kode_produksi ?? '-',
            'nomor_formula' => $item->formulaNonForming->nomor_formula ?? '-',
            'kondisi' => $item->kondisi, 'catatan' => $item->catatan,
            'dibuat_oleh' => $item->user->name ?? '-',
            'created_at' => $item->created_at,
            'approved_by_qc' => $item->approved_by_qc,
            'approved_by_produksi' => $item->approved_by_produksi,
            'approved_by_spv' => $item->approved_by_spv,
            'is_audit' => true,
        ]);

        $merged = $formingRecords->concat($nonFormingRecords)->sortByDesc('created_at')->values();
        $total = $merged->count();
        $items = $merged->slice(($page - 1) * $perPage, $perPage)->values();
        $groupedItems = $items->groupBy(fn($item) => ($item->tanggal ? (is_string($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : $item->tanggal->format('Y-m-d')) : 'no_date') . '_' . ($item->shift_id ?? 'no_shift'));

        $records = new LengthAwarePaginator($groupedItems, $total, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

        return response()->json([
            'html' => view('qc-sistem.persiapan_bahan_forming._table', compact('records'))->render(),
            'pagination' => [
                'current_page' => $page,
                'last_page' => (int)ceil($total / $perPage),
                'per_page' => $perPage,
                'total' => $total,
                'from' => $total > 0 ? (($page - 1) * $perPage + 1) : null,
                'to' => $total > 0 ? min($page * $perPage, $total) : null,
                'has_pages' => (int)ceil($total / $perPage) > 1,
            ],
        ]);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        // Auditor langsung diarahkan ke halaman audit
        if ($user->id_role == 6) {
            return redirect()->route('persiapan-bahan-forming.audit');
        }
        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        $page = (int) $request->get('page', 1);
        $perPage = (int) $perPage;
        if ($perPage <= 0) {
            $perPage = 5;
        }

        $query = PersiapanBahanForming::with([
            'plan',
            'formula.produk',
            'suhuForming.bahanForming',
            'shift',
            'aktualSuhuAdonan',
            'suhuAdonan',
            'user',
            'qcApprover',
            'produksiApprover',
            'spvApprover',
            'auditVersion'
        ])
        ->operasional()
        ->when($user->role !== 'superadmin', function($q) use ($user) {
            $q->where('plan_id', $user->id_plan);
        });

        // Add search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_produksi_emulsi', 'like', "%{$search}%")
                  ->orWhere('kondisi', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhereHas('plan', function($q) use ($search) {
                      $q->where('nama_plan', 'like', "%{$search}%");
                  })
                  ->orWhereHas('formula.produk', function($q) use ($search) {
                      $q->where('nama_produk', 'like', "%{$search}%");
                  })
                  ->orWhereHas('formula', function($q) use ($search) {
                      $q->where('nomor_formula', 'like', "%{$search}%");
                  })
                  ->orWhereHas('suhuForming.bahanForming', function($q) use ($search) {
                      $q->where('nama_rm', 'like', "%{$search}%");
                  });
            });
        }

        $this->applyDateRangeFilter($request, $query);

        // Opsi A: tampil 1 baris per UUID (forming)
        $dataForming = $query->orderBy('created_at', 'desc')->orderBy('id', 'desc')->limit(500)->get();

        $queryNonForming = PersiapanBahanNonForming::with([
            'formulaNonForming.produk',
            'shift',
            'suhuAdonan',
            'user',
            'auditVersion',
        ])
        ->operasional()
        ->when($user->role !== 'superadmin', function ($q) use ($user) {
            $q->where('plan_id', $user->id_plan);
        });

        if ($search) {
            $queryNonForming->where(function ($q) use ($search) {
                $q->where('kode_produksi', 'like', "%{$search}%")
                  ->orWhere('kondisi', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhereHas('formulaNonForming', function ($q) use ($search) {
                      $q->where('nomor_formula', 'like', "%{$search}%");
                  })
                  ->orWhereHas('formulaNonForming.produk', function ($q) use ($search) {
                      $q->where('nama_produk', 'like', "%{$search}%");
                  });
            });
        }

        $this->applyDateRangeFilter($request, $queryNonForming);

        $dataNonForming = $queryNonForming->orderBy('created_at', 'desc')->orderBy('id', 'desc')->limit(500)->get();

        $formingRecords = $dataForming->map(function ($item) {
            return (object) [
                'jenis' => 'Forming',
                'uuid' => $item->uuid,
                'shift' => $item->shift->shift ?? '-',
                'shift_id' => $item->shift_id,
                'tanggal' => $item->tanggal,
                'jam' => $item->jam,
                'nama_produk' => $item->formula->produk->nama_produk ?? '-',
                'berat_sampling' => $item->berat_sampling,
                'kode_produksi' => $item->kode_produksi_emulsi ?? '-',
                'nomor_formula' => $item->formula->nomor_formula ?? '-',
                'kondisi' => $item->kondisi,
                'catatan' => $item->catatan,
                'dibuat_oleh' => $item->user->name ?? '-',
                'created_at' => $item->created_at,
                // Approval fields
                'approved_by_qc' => $item->approved_by_qc,
                'approved_by_produksi' => $item->approved_by_produksi,
                'approved_by_spv' => $item->approved_by_spv,
                'auditVersion' => $item->auditVersion,
                'has_audit_version' => $item->auditVersion !== null,
            ];
        });

        $nonFormingRecords = $dataNonForming->map(function ($item) {
            return (object) [
                'jenis' => 'Non Forming',
                'uuid' => $item->uuid,
                'shift' => $item->shift->shift ?? '-',
                'shift_id' => $item->shift_id,
                'tanggal' => $item->tanggal,
                'jam' => $item->jam,
                'nama_produk' => $item->formulaNonForming->produk->nama_produk ?? '-',
                'berat_sampling' => $item->berat_sampling,
                'kode_produksi' => $item->kode_produksi ?? '-',
                'nomor_formula' => $item->formulaNonForming->nomor_formula ?? '-',
                'kondisi' => $item->kondisi,
                'catatan' => $item->catatan,
                'dibuat_oleh' => $item->user->name ?? '-',
                'created_at' => $item->created_at,
                // Approval fields
                'approved_by_qc' => $item->approved_by_qc,
                'approved_by_produksi' => $item->approved_by_produksi,
                'approved_by_spv' => $item->approved_by_spv,
                'auditVersion' => $item->auditVersion,
                'has_audit_version' => $item->auditVersion !== null,
            ];
        });

        $merged = $formingRecords
            ->concat($nonFormingRecords)
            ->sortByDesc(function ($r) {
                return $r->created_at;
            })
            ->values();

        $total = $merged->count();
        $items = $merged->slice(($page - 1) * $perPage, $perPage)->values();

        // Group the items on the current page by date and shift
        $groupedItems = $items->groupBy(function ($item) {
            $dateStr = $item->tanggal ? (is_string($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : $item->tanggal->format('Y-m-d')) : 'no_date';
            return $dateStr . '_' . ($item->shift_id ?? 'no_shift');
        });

        $records = new LengthAwarePaginator(
            $groupedItems,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('qc-sistem.persiapan_bahan_forming.index', compact('records', 'search', 'perPage'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'superadmin') {
            $jenis_produk = JenisProduk::all();
            $list_formula = \App\Models\NomorFormula::all();
            $produks = JenisProduk::all();
            $shifts = DataShift::all();
            $suhu_adonan = SuhuAdonan::all();
        } else {
            $jenis_produk = JenisProduk::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $list_formula = \App\Models\NomorFormula::where('id_plan', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $suhu_adonan = SuhuAdonan::where('id_plan', $user->id_plan)->get();
        }

        $id_formula = $request->input('id_formula');
        $selectedProdukId = $request->query('id_produk');
        $bahan_forming = collect();
        if ($id_formula) {
            $bahan_forming = BahanForming::with('formula')
                ->where('id_formula', $id_formula)
                ->get();
        }

        return view('qc-sistem.persiapan_bahan_forming.create', [
            'bahan' => $bahan_forming,
            'id_formula' => $id_formula,
            'list_formula' => $list_formula,
            'jenis_produk' => $jenis_produk,
            'produks' => $produks,
            'id_plan' => $user->id_plan,
            'shifts' => $shifts,
            'suhu_adonan' => $suhu_adonan,
            'selectedProdukId' => $selectedProdukId,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'id_formula' => 'required|integer|exists:nomor_formula,id',
            'berat_sampling' => 'nullable|string',
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'kode_produksi_emulsi' => 'nullable|string',
            'kondisi' => 'nullable|string',
            'kondisi_hasil_pencampuran' => 'nullable|string',
            'kode_produksi_emulsi_oil' => 'nullable|array',
            'kode_produksi_emulsi_oil.*' => 'nullable|string',
            'rework' => 'nullable|string',
            'produk_rework_id' => 'nullable|integer|exists:jenis_produk,id',
            'catatan' => 'nullable|string',
            'id_bahan_forming' => 'required|array',
            'suhu' => 'required|array',
            'kode_produksi_bahan' => 'required|array',
            'sensori' => 'required|array',
            'keterangan_bahan' => 'required|array',
            'id_suhu_adonan' => 'required|exists:suhu_adonan,id',
            'waktu_mulai_mixing' => 'nullable|string',
            'waktu_selesai_mixing' => 'nullable|string',
            'aktual_suhu_1' => 'nullable|numeric',
            'aktual_suhu_2' => 'nullable|numeric',
            'aktual_suhu_3' => 'nullable|numeric',
            'aktual_suhu_4' => 'nullable|numeric',
            'aktual_suhu_5' => 'nullable|numeric',
        ]);
        $kodeEmulsiOil = [];
        if (isset($data['kode_produksi_emulsi_oil']) && is_array($data['kode_produksi_emulsi_oil'])) {
            $kodeEmulsiOil = array_values(array_filter($data['kode_produksi_emulsi_oil'], function($value) {
                return !empty(trim($value));
            }));
        }

        $persiapan = PersiapanBahanForming::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'plan_id' => $user->id_plan,
            'user_id' => $user->id,
            'id_formula' => $data['id_formula'],
            'berat_sampling' => $data['berat_sampling'] ?? null,
            'shift_id' => $data['shift_id'],
            'tanggal' => Carbon::parse($data['tanggal'])->setTimeFrom(now()),
            'jam' => $data['jam'],
            'kode_produksi_emulsi' => $data['kode_produksi_emulsi'] ?? null,
            'kondisi' => $data['kondisi'] ?? null,
            'kondisi_hasil_pencampuran' => $data['kondisi_hasil_pencampuran'] ?? null,
            'kode_produksi_emulsi_oil' => $kodeEmulsiOil,
            'rework' => $data['rework'] ?? null,
            'produk_rework_id' => $request->input('produk_rework_id') ? (int) $request->input('produk_rework_id') : null,
            'catatan' => $data['catatan'] ?? null,
            'id_suhu_adonan' => $data['id_suhu_adonan'],
            'waktu_mulai_mixing' => $data['waktu_mulai_mixing'] ?? null,
            'waktu_selesai_mixing' => $data['waktu_selesai_mixing'] ?? null,
        ]);

        foreach ($request->id_bahan_forming as $idx => $bahanId) {
            SuhuForming::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'id_persiapan_bahan_forming' => $persiapan->id,
                'id_bahan_forming' => $bahanId,
                'suhu' => ($request->suhu[$idx] !== null && $request->suhu[$idx] !== '') ? $request->suhu[$idx] : '-',
                'kode_produksi_bahan' => $request->kode_produksi_bahan[$idx],
                'sensori' => $request->sensori[$idx],
                'keterangan' => $request->keterangan_bahan[$idx],
            ]);
        }

        // Simpan Aktual Suhu Adonan
        $aktuals = [
            $request->aktual_suhu_1,
            $request->aktual_suhu_2,
            $request->aktual_suhu_3,
            $request->aktual_suhu_4,
            $request->aktual_suhu_5,
        ];
        $filled = collect($aktuals)->filter(fn($v) => $v !== null && $v !== '' && is_numeric($v));
        $sum = $filled->sum();
        $count = $filled->count();
        $avg = $count > 0 ? $sum / $count : null;
        AktualSuhuAdonan::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'owner_type' => 'App\\Models\\PersiapanBahanForming',
            'owner_id' => $persiapan->id,
            'id_persiapan_bahan_forming' => $persiapan->id,
            'id_suhu_adonan' => $data['id_suhu_adonan'],
            'aktual_suhu_1' => $request->aktual_suhu_1,
            'aktual_suhu_2' => $request->aktual_suhu_2,
            'aktual_suhu_3' => $request->aktual_suhu_3,
            'aktual_suhu_4' => $request->aktual_suhu_4,
            'aktual_suhu_5' => $request->aktual_suhu_5,
            'total_aktual_suhu' => $avg,
        ]);

        return redirect()->route('persiapan-bahan-forming.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($uuid)
    {
        $item = PersiapanBahanForming::where('uuid', $uuid)
            ->with(['formula', 'suhuForming.bahanForming.formula', 'shift', 'aktualSuhuAdonan'])
            ->firstOrFail();

        $user = auth()->user();
        // Authorization check
        if ($user->role !== 'superadmin' && $item->plan_id !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('persiapan-bahan-forming.edit', $item)) {
            return $redirect;
        }

        // Cek batas waktu edit untuk role yang dibatasi (global config)
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($item->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('persiapan-bahan-forming.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        $bahan_forming = BahanForming::where('id_formula', $item->id_formula)->get();
        
        if ($user->role === 'superadmin') {
            $shifts = DataShift::all();
            $suhu_adonan = SuhuAdonan::all();
            $jenis_produk = JenisProduk::all();
        } else {
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $suhu_adonan = SuhuAdonan::where('id_plan', $user->id_plan)->get();
            $jenis_produk = JenisProduk::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.persiapan_bahan_forming.edit', compact('item', 'bahan_forming', 'shifts', 'suhu_adonan', 'jenis_produk'));
    }

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'id_formula' => 'required|exists:nomor_formula,id',
            'shift_id' => 'required|exists:data_shift,id',
            'berat_sampling' => 'nullable|string',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'kode_produksi_emulsi' => 'nullable|string',
            'kondisi' => 'required|in:✔,✘',
            'kondisi_hasil_pencampuran' => 'nullable|in:✔,✘',
            'kode_produksi_emulsi_oil' => 'nullable|array',
            'kode_produksi_emulsi_oil.*' => 'nullable|string',
            'rework' => 'nullable|string',
            'produk_rework_id' => 'nullable|integer|exists:jenis_produk,id',
            'catatan' => 'nullable|string',
            'suhu' => 'required|array',
            'kode_produksi_bahan' => 'required|array',
            'sensori' => 'required|array',
            'keterangan_bahan' => 'required|array',
            'id_suhu_adonan' => 'required|exists:suhu_adonan,id',
            'waktu_mulai_mixing' => 'nullable|string',
            'waktu_selesai_mixing' => 'nullable|string',
            'aktual_suhu_1' => 'nullable|numeric',
            'aktual_suhu_2' => 'nullable|numeric',
            'aktual_suhu_3' => 'nullable|numeric',
            'aktual_suhu_4' => 'nullable|numeric',
            'aktual_suhu_5' => 'nullable|numeric',
        ]);
        $kodeEmulsiOil = [];
        if (isset($request->kode_produksi_emulsi_oil) && is_array($request->kode_produksi_emulsi_oil)) {
            $kodeEmulsiOil = array_values(array_filter($request->kode_produksi_emulsi_oil, function($value) {
                return !empty(trim($value));
            }));
        }
        $item = PersiapanBahanForming::where('uuid', $uuid)->firstOrFail();

        // Cek batas waktu edit untuk role yang dibatasi (global config)
        $user = auth()->user();
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($item->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('persiapan-bahan-forming.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        $formulaChanged = (int)$item->id_formula !== (int)$request->id_formula;

        // Update data - Observer akan otomatis mencatat log perubahan
        $item->update([
            'id_formula' => $request->id_formula,
            'shift_id' => $request->shift_id,
            'berat_sampling' => $request->berat_sampling,
            'tanggal' => Carbon::parse($request->tanggal)->setTimeFrom(now()),
            'jam' => $request->jam,
            'kode_produksi_emulsi' => $request->kode_produksi_emulsi,
            'kondisi' => $request->kondisi,
            'kondisi_hasil_pencampuran' => $request->kondisi_hasil_pencampuran,
            'kode_produksi_emulsi_oil' => $kodeEmulsiOil,
            'rework' => $request->rework,
            'produk_rework_id' => $request->input('produk_rework_id') ? (int) $request->input('produk_rework_id') : null,
            'catatan' => $request->catatan,
            'id_suhu_adonan' => $request->id_suhu_adonan,
            'waktu_mulai_mixing' => $request->waktu_mulai_mixing,
            'waktu_selesai_mixing' => $request->waktu_selesai_mixing,
        ]);

        if ($formulaChanged) {
            // Hapus detail suhu forming lama
            SuhuForming::where('id_persiapan_bahan_forming', $item->id)->delete();
            
            // Create new ones from the request (using id_bahan_forming from the new formula)
            if ($request->has('id_bahan_forming')) {
                foreach ($request->id_bahan_forming as $idx => $bahanId) {
                    SuhuForming::create([
                        'uuid' => \Illuminate\Support\Str::uuid(),
                        'id_persiapan_bahan_forming' => $item->id,
                        'id_bahan_forming' => $bahanId,
                        'suhu' => $request->suhu[$idx] ?? 0,
                        'kode_produksi_bahan' => $request->kode_produksi_bahan[$idx] ?? '-',
                        'sensori' => $request->sensori[$idx] ?? 'OK',
                        'keterangan' => $request->keterangan_bahan[$idx] ?? '',
                    ]);
                }
            }
        } else {
            // Update detail suhu forming yang ada
            if ($request->has('id_suhu_forming')) {
                // Delete SuhuForming records that were deleted in the UI
                SuhuForming::where('id_persiapan_bahan_forming', $item->id)
                    ->whereNotIn('id', $request->id_suhu_forming)
                    ->delete();

                foreach ($request->id_suhu_forming as $idx => $id) {
                    SuhuForming::where('id', $id)->update([
                        'suhu' => $request->suhu[$idx],
                        'kode_produksi_bahan' => $request->kode_produksi_bahan[$idx],
                        'sensori' => $request->sensori[$idx],
                        'keterangan' => $request->keterangan_bahan[$idx],
                    ]);
                }
            }
        }

        // Update/Create Aktual Suhu Adonan
        $aktuals = [
            $request->aktual_suhu_1,
            $request->aktual_suhu_2,
            $request->aktual_suhu_3,
            $request->aktual_suhu_4,
            $request->aktual_suhu_5,
        ];
        $filled = collect($aktuals)->filter(fn($v) => $v !== null && $v !== '' && is_numeric($v));
        $sum = $filled->sum();
        $count = $filled->count();
        $avg = $count > 0 ? $sum / $count : null;
        
        $aktual = AktualSuhuAdonan::where(function($q) use ($item) {
            $q->where('id_persiapan_bahan_forming', $item->id)
              ->orWhere(function($q) use ($item) {
                  $q->where('owner_type', 'App\\Models\\PersiapanBahanForming')
                    ->where('owner_id', $item->id);
              });
        })->first();

        if ($aktual) {
            $aktual->update([
                'owner_type' => 'App\\Models\\PersiapanBahanForming',
                'owner_id' => $item->id,
                'id_persiapan_bahan_forming' => $item->id,
                'id_suhu_adonan' => $request->id_suhu_adonan,
                'aktual_suhu_1' => $request->aktual_suhu_1,
                'aktual_suhu_2' => $request->aktual_suhu_2,
                'aktual_suhu_3' => $request->aktual_suhu_3,
                'aktual_suhu_4' => $request->aktual_suhu_4,
                'aktual_suhu_5' => $request->aktual_suhu_5,
                'total_aktual_suhu' => $avg,
            ]);
        } else {
            AktualSuhuAdonan::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'owner_type' => 'App\\Models\\PersiapanBahanForming',
                'owner_id' => $item->id,
                'id_persiapan_bahan_forming' => $item->id,
                'id_suhu_adonan' => $request->id_suhu_adonan,
                'aktual_suhu_1' => $request->aktual_suhu_1,
                'aktual_suhu_2' => $request->aktual_suhu_2,
                'aktual_suhu_3' => $request->aktual_suhu_3,
                'aktual_suhu_4' => $request->aktual_suhu_4,
                'aktual_suhu_5' => $request->aktual_suhu_5,
                'total_aktual_suhu' => $avg,
            ]);
        }

        return redirect()->route('persiapan-bahan-forming.index')
                        ->with('success', 'Data berhasil diperbarui dan perubahan telah dicatat secara otomatis');
    }

    /**
     * Method untuk membuat log perubahan data
     */
    private function buatLogPerubahan($persiapanBahanForming, $dataLama, $request)
    {
        $user = auth()->user();
        $dataBaru = $persiapanBahanForming->getAttributes();
        
        // Cari field yang berubah
        $fieldYangBerubah = [];
        $nilaiLama = [];
        $nilaiBaru = [];
        
        foreach ($dataBaru as $field => $nilai) {
            // Skip field yang tidak perlu di-log
            if (in_array($field, ['id', 'uuid', 'created_at', 'updated_at'])) {
                continue;
            }
            
            $nilaiLamaField = $dataLama[$field] ?? null;
            $nilaiBaruField = $nilai;
            
            // Jika nilai berubah
            if ($nilaiLamaField != $nilaiBaruField) {
                $fieldYangBerubah[] = $field;
                $nilaiLama[$field] = $nilaiLamaField;
                $nilaiBaru[$field] = $nilaiBaruField;
            }
        }
        
        // Jika ada perubahan, buat log
        if (!empty($fieldYangBerubah)) {
            \App\Models\PersiapanBahanFormingLog::create([
                'persiapan_bahan_forming_id' => $persiapanBahanForming->id,
                'persiapan_bahan_forming_uuid' => $persiapanBahanForming->uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'aksi' => 'update',
                'field_yang_diubah' => $fieldYangBerubah,
                'nilai_lama' => $nilaiLama,
                'nilai_baru' => $nilaiBaru,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'keterangan' => 'Data diperbarui melalui form edit'
            ]);
        }
    }

    public function show($uuid)
    {
        $data = PersiapanBahanForming::where('uuid', $uuid)
            ->with([
                'plan',
                'formula.produk',
                'suhuForming.bahanForming',
                'shift',
                'aktualSuhuAdonan',
                'suhuAdonan',
                'user'
            ])
            ->firstOrFail();

        $user = auth()->user();
        
        // Check access permission - skip check if it's audit data
        if ($user->role !== 'superadmin' && !$data->is_audit && $data->plan_id !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('persiapan-bahan-forming.show', $data)) {
            return $redirect;
        }

        return view('qc-sistem.persiapan_bahan_forming.show', compact('data'))
            ->with('produkReworkNama', (function() use ($data) {
                $id = $data->produk_rework_id ?? null;
                if (empty($id)) return '-';
                return \App\Models\JenisProduk::find((int)$id)?->nama_produk ?? '-';
            })());
    }

    public function destroy($uuid)
    {
        try {
            $item = PersiapanBahanForming::where('uuid', $uuid)->firstOrFail();
            SuhuForming::where('id_persiapan_bahan_forming', $item->id)->delete();
            $item->aktuals()->delete();
            $item->delete();
            
            return redirect()->route('persiapan-bahan-forming.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('persiapan-bahan-forming.index')->with('error', 'Gagal menghapus data');
        }
    }

    /**
     * Approve data
     */
    public function approve(Request $request, $uuid)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv'
            ]);

            $user = auth()->user();
            $userRole = $user->id_role;
            $type = $request->type;

            // Validasi role dan type yang diizinkan
            $allowedRoles = [
                'qc' => [1, 3, 5], // Role 1, 3, dan 5 bisa approve QC
                'produksi' => [1, 2, 5], // Role 1, 2, dan 5 bisa approve Produksi
                'spv' => [1, 4, 5] // Role 1, 4, dan 5 bisa approve SPV
            ];

            if (!in_array($userRole, $allowedRoles[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melakukan approval ini'
                ], 403);
            }

            $isForming = true;
            $persiapanBahan = PersiapanBahanForming::where('uuid', $uuid)->first();
            
            if (!$persiapanBahan) {
                $persiapanBahan = \App\Models\PersiapanBahanNonForming::where('uuid', $uuid)->first();
                $isForming = false;
            }

            if (!$persiapanBahan) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
            }

            // Check authorization - user hanya bisa approve data dari plan mereka sendiri
            if ($user->role !== 'superadmin' && $persiapanBahan->plan_id !== $user->id_plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Cek apakah sudah diapprove sebelumnya
            $approvalField = 'approved_by_' . $type;
            if ($persiapanBahan->$approvalField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah disetujui oleh ' . strtoupper($type)
                ], 400);
            }

            // Sequential approval validation
            if ($type === 'produksi' && !$persiapanBahan->approved_by_qc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh QC terlebih dahulu'
                ], 400);
            }

            if ($type === 'spv' && !$persiapanBahan->approved_by_produksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data harus disetujui oleh Produksi terlebih dahulu'
                ], 400);
            }

            // Update approval status
            $updateData = [
                $approvalField => true,
                $type . '_approved_by' => $user->id,
                $type . '_approved_at' => now()
            ];

            $persiapanBahan->update($updateData);

            // Log activity (only for Forming as NonForming does not have its log model currently)
            if ($isForming) {
                PersiapanBahanFormingLog::create([
                    'persiapan_bahan_forming_id' => $persiapanBahan->id,
                    'persiapan_bahan_forming_uuid' => $uuid,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role,
                    'aksi' => 'approve',
                    'field_yang_diubah' => [$approvalField],
                    'nilai_lama' => [$approvalField => false],
                    'nilai_baru' => [$approvalField => true],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'keterangan' => 'Approved by ' . strtoupper($type)
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disetujui oleh ' . strtoupper($type)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk Approve data by date and shift
     */
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

            // Role validation
            $allowedRoles = [
                'qc' => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv' => [1, 4, 5]
            ];

            if (!in_array($userRole, $allowedRoles[$type])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized role'], 403);
            }

            // Get records to approve
            $formingQuery = PersiapanBahanForming::whereDate('tanggal', $tanggal)->where('shift_id', $shift_id);
            $nonFormingQuery = \App\Models\PersiapanBahanNonForming::whereDate('tanggal', $tanggal)->where('shift_id', $shift_id);

            if ($user->role !== 'superadmin') {
                $formingQuery->where('plan_id', $user->id_plan);
                $nonFormingQuery->where('plan_id', $user->id_plan);
            }

            $formingRecords = $formingQuery->get();
            $nonFormingRecords = $nonFormingQuery->get();
            $allRecords = $formingRecords->concat($nonFormingRecords);

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
     * Multi Approval Checkbox-Based (Per-Row Selection).
     *
     * Karena modul ini menggabungkan Forming + Non-Forming,
     * UUID dikirim dengan prefix: "forming::{uuid}" atau "non-forming::{uuid}"
     * untuk membedakan jenis record yang perlu di-approve.
     *
     * Fitur:
     * - Smart auto-skip: hanya update data yang belum approved_by_{type}.
     * - Approver original TIDAK tertimpa.
     * - Tidak ada sequential check (QC, Produksi, SPV independen).
     */
    public function bulkApproveSelected(Request $request)
    {
        try {
            $request->validate([
                'type'    => 'required|in:qc,produksi,spv',
                'uuids'   => 'required|array|min:1',
                'uuids.*' => 'required|string',
            ]);

            $user     = auth()->user();
            $type     = $request->input('type');
            $rawUuids = $request->input('uuids', []);
            $userRole = $user->id_role;

            // Role permission check
            $allowedRoles = [
                'qc'       => [1, 3, 5],
                'produksi' => [1, 2, 5],
                'spv'      => [1, 4, 5],
            ];

            if (!in_array($userRole, $allowedRoles[$type] ?? [])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki otoritas untuk verifikasi ini.',
                ], 403);
            }

            // Pisahkan UUID berdasarkan prefix jenis
            $formingUuids    = [];
            $nonFormingUuids = [];

            foreach ($rawUuids as $raw) {
                if (str_starts_with($raw, 'forming::')) {
                    $formingUuids[] = substr($raw, strlen('forming::'));
                } elseif (str_starts_with($raw, 'non-forming::')) {
                    $nonFormingUuids[] = substr($raw, strlen('non-forming::'));
                }
            }

            $approvalField  = 'approved_by_' . $type;
            $approverField  = $type . '_approved_by';
            $approvedAtField = $type . '_approved_at';
            $updateData     = [
                $approvalField   => true,
                $approverField   => $user->id,
                $approvedAtField => now(),
            ];

            $totalCount = 0;

            \DB::beginTransaction();

            // --- Proses Forming ---
            if (!empty($formingUuids)) {
                $formingItems = PersiapanBahanForming::whereIn('uuid', $formingUuids)
                    ->where($approvalField, false)
                    ->when($user->role !== 'superadmin', fn($q) => $q->where('plan_id', $user->id_plan))
                    ->get();

                if ($formingItems->isNotEmpty()) {
                    PersiapanBahanForming::whereIn('uuid', $formingItems->pluck('uuid'))
                        ->update($updateData);

                    // Sync audit versions & log
                    foreach ($formingItems as $item) {
                        if ($item->auditVersion) {
                            $item->auditVersion->update($updateData);
                        }

                        PersiapanBahanFormingLog::create([
                            'persiapan_bahan_forming_id'   => $item->id,
                            'persiapan_bahan_forming_uuid' => $item->uuid,
                            'user_id'                      => $user->id,
                            'user_name'                    => $user->name,
                            'user_role'                    => $user->role ?? 'Unknown',
                            'aksi'                         => 'bulk_approval_selected',
                            'field_yang_diubah'            => [$approvalField],
                            'nilai_lama'                   => [$approvalField => false],
                            'nilai_baru'                   => [$approvalField => true],
                            'ip_address'                   => $request->ip(),
                            'user_agent'                   => $request->userAgent(),
                            'keterangan'                   => 'Bulk Approved Selected (Forming) by ' . strtoupper($type),
                        ]);
                    }

                    $totalCount += $formingItems->count();
                }
            }

            // --- Proses Non-Forming ---
            if (!empty($nonFormingUuids)) {
                $nonFormingItems = PersiapanBahanNonForming::whereIn('uuid', $nonFormingUuids)
                    ->where($approvalField, false)
                    ->when($user->role !== 'superadmin', fn($q) => $q->where('plan_id', $user->id_plan))
                    ->get();

                if ($nonFormingItems->isNotEmpty()) {
                    PersiapanBahanNonForming::whereIn('uuid', $nonFormingItems->pluck('uuid'))
                        ->update($updateData);

                    // Sync audit versions (log jika dibutuhkan di masa depan)
                    foreach ($nonFormingItems as $item) {
                        if ($item->auditVersion) {
                            $item->auditVersion->update($updateData);
                        }
                    }

                    $totalCount += $nonFormingItems->count();
                }
            }

            \DB::commit();

            if ($totalCount === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Semua data yang dipilih sudah diverifikasi sebelumnya.',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil verifikasi ' . $totalCount . ' data ' . strtoupper($type),
            ]);

        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // AJAX
    public function getNomorFormulaOptions()
    {
        $user = auth()->user();
        $query = \App\Models\NomorFormula::select('id', 'nomor_formula', 'id_produk')->with('produk');

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $formula = $query->get();
        return response()->json($formula);
    }

    public function getBahanFormingByProduk($id_produk)
    {
        $bahan = BahanForming::where('id_produk', $id_produk)->get();
        return response()->json($bahan);
    }

    public function getBahanFormingByFormula($id_formula)
    {
        $user = auth()->user();
        $query = BahanForming::with('formula:id,nomor_formula')
            ->where('id_formula', $id_formula);

        if ($user->role !== 'superadmin') {
            $query->whereHas('formula', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });
        }

        $bahan = $query->get(['id', 'nama_rm', 'berat_rm', 'id_formula']);
        return response()->json($bahan);
    }

    /**
     * AJAX: kembalikan daftar produk rework berdasarkan id_produk_rework yang tersimpan di JenisProduk
     */
    public function getReworkProdukByProduk($id_produk)
    {
        $produk = JenisProduk::find($id_produk);
        if (!$produk || empty($produk->id_produk_rework)) {
            return response()->json([]);
        }
        $reworkIds = (array) $produk->id_produk_rework;
        $reworkProduk = JenisProduk::whereIn('id', $reworkIds)
                                   ->get(['id', 'nama_produk']);
        return response()->json($reworkProduk);
    }

    public function searchAjax(Request $request)
    {
        $perPage = $request->get('per_page', 5);

        $page = (int) $request->get('page', 1);
        $perPage = (int) $perPage;
        if ($perPage <= 0) {
            $perPage = 5;
        }

        $query = PersiapanBahanForming::with([
            'plan',
            'formula.produk',
            'suhuForming.bahanForming',
            'shift',
            'aktualSuhuAdonan',
            'suhuAdonan',
            'user',
            'qcApprover',
            'produksiApprover',
            'spvApprover'
        ])
        ->operasional()
        ->when($user->role !== 'superadmin', function($q) use ($user) {
            $q->where('plan_id', $user->id_plan);
        });

        // Add search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_produksi_emulsi', 'like', "%{$search}%")
                  ->orWhere('kondisi', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhereHas('plan', function($q) use ($search) {
                      $q->where('nama_plan', 'like', "%{$search}%");
                  })
                  ->orWhereHas('formula.produk', function($q) use ($search) {
                      $q->where('nama_produk', 'like', "%{$search}%");
                  })
                  ->orWhereHas('formula', function($q) use ($search) {
                      $q->where('nomor_formula', 'like', "%{$search}%");
                  })
                  ->orWhereHas('suhuForming.bahanForming', function($q) use ($search) {
                      $q->where('nama_rm', 'like', "%{$search}%");
                  });
            });
        }

        $dataForming = $query->orderBy('created_at', 'desc')->orderBy('id', 'desc')->limit(500)->get();

        $queryNonForming = PersiapanBahanNonForming::with([
            'formulaNonForming.produk',
            'shift',
            'suhuAdonan',
            'user',
        ])
        ->operasional()
        ->when($user->role !== 'superadmin', function ($q) use ($user) {
            $q->where('plan_id', $user->id_plan);
        });

        if ($search) {
            $queryNonForming->where(function ($q) use ($search) {
                $q->where('kode_produksi', 'like', "%{$search}%")
                  ->orWhere('kondisi', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhereHas('formulaNonForming', function ($q) use ($search) {
                      $q->where('nomor_formula', 'like', "%{$search}%");
                  })
                  ->orWhereHas('formulaNonForming.produk', function ($q) use ($search) {
                      $q->where('nama_produk', 'like', "%{$search}%");
                  });
            });
        }

        $dataNonForming = $queryNonForming->orderBy('created_at', 'desc')->orderBy('id', 'desc')->limit(500)->get();

        $formingRecords = $dataForming->map(function ($item) {
            return (object) [
                'jenis' => 'Forming',
                'uuid' => $item->uuid,
                'shift' => $item->shift->shift ?? '-',
                'shift_id' => $item->shift_id,
                'tanggal' => $item->tanggal,
                'jam' => $item->jam,
                'nama_produk' => $item->formula->produk->nama_produk ?? '-',
                'berat_sampling' => $item->berat_sampling,
                'kode_produksi' => $item->kode_produksi_emulsi ?? '-',
                'nomor_formula' => $item->formula->nomor_formula ?? '-',
                'kondisi' => $item->kondisi,
                'catatan' => $item->catatan,
                'dibuat_oleh' => $item->user->name ?? '-',
                'created_at' => $item->created_at,
                // Approval fields
                'approved_by_qc' => $item->approved_by_qc,
                'approved_by_produksi' => $item->approved_by_produksi,
                'approved_by_spv' => $item->approved_by_spv,
            ];
        });

        $nonFormingRecords = $dataNonForming->map(function ($item) {
            return (object) [
                'jenis' => 'Non Forming',
                'uuid' => $item->uuid,
                'shift' => $item->shift->shift ?? '-',
                'shift_id' => $item->shift_id,
                'tanggal' => $item->tanggal,
                'jam' => $item->jam,
                'nama_produk' => $item->formulaNonForming->produk->nama_produk ?? '-',
                'berat_sampling' => $item->berat_sampling,
                'kode_produksi' => $item->kode_produksi ?? '-',
                'nomor_formula' => $item->formulaNonForming->nomor_formula ?? '-',
                'kondisi' => $item->kondisi,
                'catatan' => $item->catatan,
                'dibuat_oleh' => $item->user->name ?? '-',
                'created_at' => $item->created_at,
                // Approval fields
                'approved_by_qc' => $item->approved_by_qc,
                'approved_by_produksi' => $item->approved_by_produksi,
                'approved_by_spv' => $item->approved_by_spv,
            ];
        });

        $merged = $formingRecords
            ->concat($nonFormingRecords)
            ->sortByDesc('created_at')
            ->values();

        $total = $merged->count();
        $items = $merged->slice(($page - 1) * $perPage, $perPage)->values();

        // Grouping
        $groupedItems = $items->groupBy(function ($item) {
            $dateStr = $item->tanggal ? (is_string($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : $item->tanggal->format('Y-m-d')) : 'no_date';
            return $dateStr . '_' . ($item->shift_id ?? 'no_shift');
        });

        $records = new LengthAwarePaginator(
            $groupedItems,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return response()->json([
            'html' => view('qc-sistem.persiapan_bahan_forming._table', compact('records'))->render(),
            'pagination' => [
                'current_page' => $page,
                'last_page' => (int) ceil($total / $perPage),
                'per_page' => $perPage,
                'total' => $total,
                'from' => $total > 0 ? (($page - 1) * $perPage + 1) : null,
                'to' => $total > 0 ? min($page * $perPage, $total) : null,
                'has_pages' => (int) ceil($total / $perPage) > 1,
            ],
        ]);
    }

    public function exportPdf($uuid = null)
    {
        $user = auth()->user();
        
        $query = PersiapanBahanForming::with([
            'plan',
            'formula.produk',
            'suhuForming.bahanForming',
            'shift',
            'aktualSuhuAdonan',
            'suhuAdonan',
            'qcApprover',
            'produksiApprover',
            'spvApprover'
        ])
        ->when($user->role !== 'superadmin', function($q) use ($user) {
            $q->where('plan_id', $user->id_plan);
        });

        // If UUID is provided, export single record
        if ($uuid) {
            $query->where('uuid', $uuid);
            $filename = 'persiapan_bahan_forming_' . $uuid . '_' . date('Y-m-d_H-i-s') . '.pdf';
        } else {
            // Export all records
            $filename = 'persiapan_bahan_forming_' . date('Y-m-d_H-i-s') . '.pdf';
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        if ($data->isEmpty()) {
            $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
            $filterInfo = [];
            
            if ($request->tanggal) {
                $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y');
            }
            if ($request->shift_id) {
                $shift = \App\Models\DataShift::find($request->shift_id);
                $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
            }
            if ($request->produk_id) {
                $produk = \App\Models\JenisProduk::find($request->produk_id);
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

        $pdf = PDF::loadView('qc-sistem.persiapan_bahan_forming.export_pdf', compact('data'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download($filename);
    }

    public function exportExcel($uuid = null)
    {
        if ($uuid) {
            $filename = 'persiapan_bahan_forming_' . $uuid . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        } else {
            $filename = 'persiapan_bahan_forming_' . date('Y-m-d_H-i-s') . '.xlsx';
        }
        
        return Excel::download(new PersiapanBahanFormingExport($uuid), $filename);
    }

    public function saveKode(Request $request)
    {
        try {
            $request->validate([
                'uuid' => 'required|string',
                'kode_form' => 'required|string|max:50'
            ]);

            $item = PersiapanBahanForming::where('uuid', $request->uuid)->firstOrFail();
            
            // Update kode form
            $item->update([
                'kode_form' => $request->kode_form
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kode form berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan kode form: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkExportExcel(Request $request, PersiapanBahanFormingBulkExportService $service)
    {
        return $service->export($request);
    }

    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal_mulai' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
                'shift_id' => 'nullable|exists:data_shift,id',
                'produk_id' => 'nullable|exists:jenis_produk,id',
                'kode_form' => 'required|string|max:50',
                'is_audit' => 'nullable'
            ]);

            $user = auth()->user();
            $isAudit = $request->boolean('is_audit', false);
            
            $query = PersiapanBahanForming::with([
                'plan',
                'formula.produk',
                'suhuForming.bahanForming', // Menambahkan relasi ini
                'shift',
                'aktualSuhuAdonan', // Menambahkan relasi ini
                'suhuAdonan',
                'qcApprover',
                'produksiApprover',
                'spvApprover'
            ])
            ->where('is_audit', $isAudit)
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('plan_id', $user->id_plan);
            });

            // Apply filters
                        $query->whereDate('tanggal', '>=', $request->tanggal_mulai)->whereDate('tanggal', '<=', $request->tanggal_akhir);
            
            if ($request->shift_id) {
                $query->where('shift_id', $request->shift_id);
            }
            
            if ($request->produk_id) {
                $query->whereHas('formula.produk', function($q) use ($request) {
                    $q->where('id', $request->produk_id);
                });
            }

            $dataForming = $query->orderBy('tanggal', 'asc')->orderBy('jam', 'asc')->get();

            $queryNonForming = PersiapanBahanNonForming::with([
                'formulaNonForming.produk',
                'details.bahanNonForming',
                'shift',
                'suhuAdonan',
                'user',
            ])
            ->where('is_audit', $isAudit)
            ->when($user->role !== 'superadmin', function ($q) use ($user) {
                $q->where('plan_id', $user->id_plan);
            });

                        $queryNonForming->whereDate('tanggal', '>=', $request->tanggal_mulai)->whereDate('tanggal', '<=', $request->tanggal_akhir);

            if ($request->shift_id) {
                $queryNonForming->where('shift_id', $request->shift_id);
            }

            if ($request->produk_id) {
                $queryNonForming->whereHas('formulaNonForming.produk', function ($q) use ($request) {
                    $q->where('id', $request->produk_id);
                });
            }

            $dataNonForming = $queryNonForming->orderBy('tanggal', 'asc')->orderBy('jam', 'asc')->get();

            if ($dataForming->isEmpty() && $dataNonForming->isEmpty()) {
                $errorMessage = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
                $filterInfoText = [
                    'Mulai Tanggal: ' . \Carbon\Carbon::parse($request->tanggal_mulai)->format('d-m-Y'),
                    'Sampai Tanggal: ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y'),
                ];
                if ($request->shift_id) {
                    $shift = \App\Models\DataShift::find($request->shift_id);
                    $filterInfoText[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
                }
                if ($request->produk_id) {
                    $produk = \App\Models\JenisProduk::find($request->produk_id);
                    $filterInfoText[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Unknown');
                }
                $filterInfoText[] = 'Mode: ' . ($isAudit ? 'Audit' : 'Operasional');
                
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
                        <p class="message">' . $errorMessage . '</p>
                        <div class="filter-info">
                            <h4>Filter yang digunakan:</h4>
                            <ul>';
                foreach ($filterInfoText as $info) {
                    $html .= '<li>' . $info . '</li>';
                }
                $html .= '
                            </ul>
                        </div>
                        <p style="color: #6c757d; font-size: 14px;">
                            Silakan coba dengan filter yang berbeda atau pastikan data sudah tersedia di sistem.
                        </p>
                        <a href="javascript:window.close()" class="btn">Tutup</a>
                    </div>
                </body>
                </html>';
                
                return response($html)->header('Content-Type', 'text/html');
            }

            // Update semua data dengan kode form
            $dataForming->each(function($item) use ($request) {
                $item->update([
                    'kode_form' => $request->kode_form
                ]);
            });

            $dataNonForming->each(function ($item) use ($request) {
                $item->update([
                    'kode_form' => $request->kode_form
                ]);
            });

            $filename = 'persiapan_bahan_forming&nonforming_' . date('Y-m-d_H-i-s') . '.pdf';
            
            $filterInfo = [
                'tanggal_mulai' => \Carbon\Carbon::parse($request->tanggal_mulai)->format('d-m-Y'),
                'tanggal_akhir' => \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y'),
                'shift' => $request->shift_id ? (\App\Models\DataShift::find($request->shift_id)->shift ?? 'Semua Shift') : 'Semua Shift',
                'produk' => $request->produk_id ? (\App\Models\JenisProduk::find($request->produk_id)->nama_produk ?? 'Semua Produk') : 'Semua Produk',
                'kode_form' => $request->kode_form,
                'is_audit' => $isAudit
            ];

            $pdf = PDF::loadView('qc-sistem.persiapan_bahan_forming.export_pdf_bulk_combined', [
                'dataForming' => $dataForming,
                'dataNonForming' => $dataNonForming,
                'kode_form' => $request->kode_form,
                'filterInfo' => $filterInfo
            ]);
            $pdf->setPaper('A4', 'landscape');
            
            return $pdf->download($filename);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->validator->errors()->all()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Bulk PDF forming/non-forming generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Exception occurred',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Menampilkan riwayat log perubahan data
     */
    public function showLogs($uuid)
    {
        $item = PersiapanBahanForming::where('uuid', $uuid)->firstOrFail();
        
        $logs = PersiapanBahanFormingLog::where('persiapan_bahan_forming_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(5);
        
        return view('qc-sistem.persiapan_bahan_forming.logs', compact('item', 'logs'));
    }

    /**
     * API untuk mendapatkan log dalam format JSON (untuk AJAX)
     */
    public function getLogsJson($uuid)
    {
        $item = PersiapanBahanForming::where('uuid', $uuid)->firstOrFail();
        
        $logs = PersiapanBahanFormingLog::where('persiapan_bahan_forming_id', $item->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($log) {
                        return [
                            'id' => $log->id,
                            'tanggal' => $log->created_at->format('d/m/Y H:i:s'),
                            'user' => $log->user_name,
                            'role' => $log->user_role,
                            'aksi' => ucfirst($log->aksi),
                            'field_diubah' => implode(', ', $log->field_yang_diubah ?? []),
                            'deskripsi' => $log->deskripsi_perubahan,
                            'ip_address' => $log->ip_address
                        ];
                    });
        
        return response()->json($logs);
    }

    /**
     * AJAX endpoint untuk mendapatkan nomor formula berdasarkan produk
     */
    public function getFormulaByProduk($id_produk)
    {
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $formulas = \App\Models\NomorFormula::where('id_produk', $id_produk)->get();
        } else {
            $formulas = \App\Models\NomorFormula::where('id_produk', $id_produk)
                        ->where('id_plan', $user->id_plan)
                        ->get();
        }
        
        return response()->json($formulas);
    }

    /**
     * AJAX endpoint untuk mendapatkan suhu adonan berdasarkan produk
     */
    public function getSuhuAdonanByProduk($id_produk)
    {
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $suhuAdonan = \App\Models\SuhuAdonan::where('id_produk', $id_produk)->get();
        } else {
            $suhuAdonan = \App\Models\SuhuAdonan::where('id_produk', $id_produk)
                          ->where('id_plan', $user->id_plan)
                          ->get();
        }
        
        return response()->json($suhuAdonan);
    }
}
