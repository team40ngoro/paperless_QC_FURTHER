<?php

namespace App\Http\Controllers;

use App\Models\DataRm;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\Plan;
use App\Models\ProsesTwahing;
use App\Models\ProsesTwahingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\Traits\HasAuditController;

class ProsesTwahingController extends Controller
{
    use HasAuditController;

    protected function auditModel(): string
    {
        return ProsesTwahing::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'proses-twahing';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.proses_twahing';
    }

    protected function auditRelations(): array
    {
        return ['plan', 'user', 'shift', 'produk', 'details.rm'];
    }

    protected function auditSearchColumns(): array
    {
        return ['catatan'];
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

    protected function auditPlanColumn(): string
    {
        return 'id_plan';
    }

    protected function applyAuditSearchQuery($query, $search)
    {
        $query->where(function ($q) use ($search) {
            $q->where('tanggal', 'LIKE', '%' . $search . '%')
              ->orWhere('catatan', 'LIKE', '%' . $search . '%')
              ->orWhereHas('details.rm', function ($subQuery) use ($search) {
                  $subQuery->where('nama_rm', 'LIKE', '%' . $search . '%');
              })
              ->orWhereHas('produk', function ($subQuery) use ($search) {
                  $subQuery->where('nama_produk', 'LIKE', '%' . $search . '%');
              })
              ->orWhereHas('details', function ($subQuery) use ($search) {
                  $subQuery->where('kode_produksi', 'LIKE', '%' . $search . '%');
              });
        });
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->id_role == 6) {
            return redirect()->route('proses-twahing.audit');
        }

        $search = $request->get('search');

        $query = ProsesTwahing::with(['plan', 'user', 'shift', 'produk', 'details.rm', 'groupRecords', 'latestGroupRecord'])
            ->operasional();

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        // Hanya tampilkan parent (record pertama per grup)
        $query->where(function ($q) {
            $q->whereColumn('uuid', 'group_uuid')
              ->orWhereNull('group_uuid');
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tanggal', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('details.rm', function ($subQuery) use ($search) {
                      $subQuery->where('nama_rm', 'LIKE', '%' . $search . '%');
                  })
                  ->orWhereHas('produk', function ($subQuery) use ($search) {
                      $subQuery->where('nama_produk', 'LIKE', '%' . $search . '%');
                  })
                  ->orWhereHas('details', function ($subQuery) use ($search) {
                      $subQuery->where('kode_produksi', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        // Ambil data yang diurutkan untuk grouping
        $this->applyDateRangeFilter(request(), $query);
        $itemsAll = $query->orderByDesc('tanggal')
            ->orderByDesc('jam')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        // Grouping logic: Date + Shift
        $grouped = $itemsAll->groupBy(function($item) {
            $date = $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : 'no_date';
            $shift = $item->id_shift ?? 'no_shift';
            return $date . '_' . $shift;
        });

        // Manual Pagination for Grouped Collection
        $page = request()->get('page', 1);
        $perPage = 10;
        $items = new \Illuminate\Pagination\LengthAwarePaginator(
            $grouped->forPage($page, $perPage),
            $grouped->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('qc-sistem.proses_twahing.index', compact('items', 'search'));
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

        if (!$selectedPlanId) {
            abort(404);
        }

        $shifts = DataShift::where('id_plan', $selectedPlanId)->orderBy('shift')->get();
        $rms = DataRm::where('id_plan', $selectedPlanId)->orderBy('nama_rm')->get();
        $produks = JenisProduk::where('id_plan', $selectedPlanId)->orderBy('nama_produk')->get();

        return view('qc-sistem.proses_twahing.create', [
            'plans' => $plans,
            'selectedPlanId' => $selectedPlanId,
            'shifts' => $shifts,
            'rms' => $rms,
            'produks' => $produks,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'id_shift' => 'required|exists:data_shift,id',
            'id_produk' => 'nullable|exists:jenis_produk,id',
            'tanggal' => 'required|date',
            'jam' => 'nullable|date_format:H:i',
            'waktu_thawing_awal' => 'nullable|date_format:H:i',
            'waktu_thawing_akhir' => 'nullable|date_format:H:i',
            'total_waktu_thawing_jam' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.id_rm' => 'nullable|exists:data_rm,id',
            'details.*.kode_produksi' => 'nullable|string|max:255',
            'details.*.kondisi_ruang' => 'nullable|string|max:255',
            'details.*.waktu_pemeriksaan' => 'nullable|date_format:H:i',
            'details.*.suhu_rm' => 'nullable|numeric',
            'details.*.suhu_ruang' => 'nullable|numeric',
            'details.*.suhu_air_thawing' => 'nullable|numeric',
            'details.*.suhu_produk' => 'nullable|numeric',
            'details.*.kondisi_produk' => 'nullable|string|max:255',
        ];

        if ($user->role === 'superadmin') {
            $rules['id_plan'] = 'required|exists:plan,id';
        }

        $data = $request->validate($rules);

        $planId = $user->role === 'superadmin' ? (int) $data['id_plan'] : (int) $user->id_plan;

        $shift = DataShift::where('id', $data['id_shift'])->where('id_plan', $planId)->first();
        if (!$shift) {
            return back()->withErrors(['id_shift' => 'Shift tidak valid untuk plan Anda.'])->withInput();
        }

        $header = ProsesTwahing::create([
            'id_plan' => $planId,
            'user_id' => $user->id,
            'id_shift' => (int) $data['id_shift'],
            'id_produk' => !empty($data['id_produk']) ? (int) $data['id_produk'] : null,
            'tanggal' => $data['tanggal'],
            'jam' => $data['jam'] ?? null,
            'waktu_thawing_awal' => $data['waktu_thawing_awal'] ?? null,
            'waktu_thawing_akhir' => $data['waktu_thawing_akhir'] ?? null,
            'total_waktu_thawing_jam' => $data['total_waktu_thawing_jam'] ?? null,
            'catatan' => $data['catatan'] ?? null,
        ]);

        $rows = [];
        foreach (($data['details'] ?? []) as $row) {
            if (empty($row['id_rm'])) {
                continue;
            }

            $rm = DataRm::where('id', $row['id_rm'])->where('id_plan', $planId)->first();
            if (!$rm) {
                continue;
            }

            $rows[] = [
                'uuid' => (string) Str::uuid(),
                'proses_twahing_id' => $header->id,
                'id_rm' => (int) $row['id_rm'],
                'kode_produksi' => $row['kode_produksi'] ?? null,
                'kondisi_ruang' => $row['kondisi_ruang'] ?? null,
                'waktu_pemeriksaan' => $row['waktu_pemeriksaan'] ?? null,
                'suhu_rm' => $row['suhu_rm'] ?? null,
                'suhu_ruang' => $row['suhu_ruang'] ?? null,
                'suhu_air_thawing' => $row['suhu_air_thawing'] ?? null,
                'suhu_produk' => $row['suhu_produk'] ?? null,
                'kondisi_produk' => $row['kondisi_produk'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (count($rows) === 0) {
            $header->delete();
            return back()->withErrors(['details' => 'Minimal 1 baris detail wajib memilih Nama RM.'])->withInput();
        }

        if (count($rows) > 0) {
            ProsesTwahingDetail::insert($rows);
        }

        return redirect()->route('proses-twahing.show', $header->uuid)
            ->with('success', 'Data pemeriksaan proses thawing berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $user = Auth::user();

        $item = ProsesTwahing::with(['plan', 'user', 'shift', 'produk', 'details.rm'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('proses-twahing.show', $item)) {
            return $redirect;
        }

        return view('qc-sistem.proses_twahing.show', compact('item'));
    }

    public function edit(Request $request, $uuid)
    {
        $user = Auth::user();

        $original = ProsesTwahing::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $original->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('proses-twahing.edit', $original)) {
            return $redirect;
        }

        $groupUuid = $original->group_uuid ?: $original->uuid;
        $item = ProsesTwahing::with(['details.rm', 'shift', 'produk'])
            ->where(function ($q) use ($groupUuid) {
                $q->where('group_uuid', $groupUuid)
                  ->orWhere('uuid', $groupUuid);
            })
            ->orderByDesc('id')
            ->first() ?? $original;
        // Check edit time limit using global config
        $user = auth()->user();
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($item->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('proses-twahing.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        $plans = null;
        $selectedPlanId = (int) $item->id_plan;

        if ($user->role === 'superadmin') {
            $plans = Plan::orderBy('nama_plan')->get();
            if ($request->filled('plan_id')) {
                $selectedPlanId = (int) $request->get('plan_id');
            }
        }

        $shifts = DataShift::where('id_plan', $selectedPlanId)->orderBy('shift')->get();
        $rms = DataRm::where('id_plan', $selectedPlanId)->orderBy('nama_rm')->get();
        $produks = JenisProduk::where('id_plan', $selectedPlanId)->orderBy('nama_produk')->get();

        return view('qc-sistem.proses_twahing.edit', [
            'item' => $item,
            'plans' => $plans,
            'selectedPlanId' => $selectedPlanId,
            'shifts' => $shifts,
            'rms' => $rms,
            'produks' => $produks,
        ]);
    }

    public function update(Request $request, $uuid)
    {
        $user = Auth::user();

        // Find the record to update
        $item = ProsesTwahing::with(['details'])->where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }
        // Check edit time limit using global config
        $user = auth()->user();
        $restrictedRoles = config('edit_limit.restricted_role_ids', []);
        if (in_array($user->id_role, $restrictedRoles)) {
            $timeLimitHours = config('edit_limit.time_limit_hours', 48);
            $createdAt = \Carbon\Carbon::parse($item->created_at);
            if ($createdAt->addHours($timeLimitHours)->isPast()) {
                return redirect()->route('proses-twahing.index')
                    ->with('error', config('edit_limit.error_message', 'Waktu edit sudah habis.'));
            }
        }

        $planId = (int) $item->id_plan;

        $data = $request->validate([
            'id_shift' => 'required|exists:data_shift,id',
            'id_produk' => 'nullable|exists:jenis_produk,id',
            'tanggal' => 'required|date',
            'jam' => 'nullable|date_format:H:i',
            'waktu_thawing_awal' => 'nullable|date_format:H:i',
            'waktu_thawing_akhir' => 'nullable|date_format:H:i',
            'total_waktu_thawing_jam' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.id_rm' => 'nullable|exists:data_rm,id',
            'details.*.kode_produksi' => 'nullable|string|max:255',
            'details.*.kondisi_ruang' => 'nullable|string|max:255',
            'details.*.waktu_pemeriksaan' => 'nullable|date_format:H:i',
            'details.*.suhu_rm' => 'nullable|numeric',
            'details.*.suhu_ruang' => 'nullable|numeric',
            'details.*.suhu_air_thawing' => 'nullable|numeric',
            'details.*.suhu_produk' => 'nullable|numeric',
            'details.*.kondisi_produk' => 'nullable|string|max:255',
        ]);

        $shift = DataShift::where('id', $data['id_shift'])->where('id_plan', $planId)->first();
        if (!$shift) {
            return back()->withErrors(['id_shift' => 'Shift tidak valid untuk plan data ini.'])->withInput();
        }

        // UPDATE existing record (not create new)
        $item->update([
            'id_shift'               => (int) $data['id_shift'],
            'id_produk'              => !empty($data['id_produk']) ? (int) $data['id_produk'] : null,
            'tanggal'                => $data['tanggal'],
            'jam'                    => $data['jam'] ?? null,
            'waktu_thawing_awal'     => $data['waktu_thawing_awal'] ?? null,
            'waktu_thawing_akhir'    => $data['waktu_thawing_akhir'] ?? null,
            'total_waktu_thawing_jam'=> $data['total_waktu_thawing_jam'] ?? null,
            'catatan'                => $data['catatan'] ?? null,
        ]);

        $rows = [];
        foreach (($data['details'] ?? []) as $row) {
            if (empty($row['id_rm'])) {
                continue;
            }

            $rm = DataRm::where('id', $row['id_rm'])->where('id_plan', $planId)->first();
            if (!$rm) {
                continue;
            }

            $rows[] = [
                'uuid'             => (string) Str::uuid(),
                'proses_twahing_id'=> $item->id,
                'id_rm'            => (int) $row['id_rm'],
                'kode_produksi'    => $row['kode_produksi'] ?? null,
                'kondisi_ruang'    => $row['kondisi_ruang'] ?? null,
                'waktu_pemeriksaan'=> $row['waktu_pemeriksaan'] ?? null,
                'suhu_rm'          => $row['suhu_rm'] ?? null,
                'suhu_ruang'       => $row['suhu_ruang'] ?? null,
                'suhu_air_thawing' => $row['suhu_air_thawing'] ?? null,
                'suhu_produk'      => $row['suhu_produk'] ?? null,
                'kondisi_produk'   => $row['kondisi_produk'] ?? null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        }

        if (count($rows) === 0) {
            return back()->withErrors(['details' => 'Minimal 1 baris detail wajib memilih Nama RM.'])->withInput();
        }

        // DELETE existing details first to prevent duplication on audit edits
        ProsesTwahingDetail::where('proses_twahing_id', $item->id)->delete();
        
        ProsesTwahingDetail::insert($rows);

        // Smart redirect: jika audit -> ke audit page, jika operasional -> ke index page
        $redirectRoute = $item->is_audit ? 'proses-twahing.audit' : 'proses-twahing.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Data pemeriksaan proses thawing berhasil diperbarui.');
    }

    /**
     * Tampilkan form Edit Per 2 Jam (untuk membuat record pengecekan jam baru dalam grup)
     */
    public function twoHourEdit(Request $request, $uuid)
    {
        $user = Auth::user();

        $original = ProsesTwahing::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $original->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        // Ambil record terbaru dalam grup beserta relasi details.rm
        $groupUuid = $original->group_uuid ?: $original->uuid;
        $item = ProsesTwahing::with(['details.rm', 'shift', 'produk'])
            ->where(function ($q) use ($groupUuid) {
                $q->where('group_uuid', $groupUuid)
                  ->orWhere('uuid', $groupUuid);
            })
            ->orderByDesc('id')
            ->first() ?? $original;
        
        $selectedPlanId = (int) $original->id_plan;
        $plans = null;

        if ($user->role === 'superadmin') {
            $plans = Plan::orderBy('nama_plan')->get();
            if ($request->filled('plan_id')) {
                $selectedPlanId = (int) $request->get('plan_id');
            }
        }

        $shifts = DataShift::where('id_plan', $selectedPlanId)->orderBy('shift')->get();
        $rms = DataRm::where('id_plan', $selectedPlanId)->orderBy('nama_rm')->get();
        $produks = JenisProduk::where('id_plan', $selectedPlanId)->orderBy('nama_produk')->get();

        return view('qc-sistem.proses_twahing.edit', [
            'item' => $item,
            'original' => $original,
            'plans' => $plans,
            'selectedPlanId' => $selectedPlanId,
            'shifts' => $shifts,
            'rms' => $rms,
            'produks' => $produks,
            'twoHour' => true,
        ]);
    }

    /**
     * Simpan record per 2 jam baru ke dalam grup
     */
    public function twoHourStore(Request $request, $uuid)
    {
        $user = Auth::user();
        $parent = ProsesTwahing::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $parent->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $planId = (int) $parent->id_plan;

        $data = $request->validate([
            'id_shift'               => 'required|exists:data_shift,id',
            'id_produk'              => 'nullable|exists:jenis_produk,id',
            'tanggal'                => 'required|date',
            'jam'                    => 'nullable|date_format:H:i',
            'waktu_thawing_awal'     => 'nullable|date_format:H:i',
            'waktu_thawing_akhir'    => 'nullable|date_format:H:i',
            'total_waktu_thawing_jam'=> 'nullable|numeric',
            'catatan'                => 'nullable|string|max:1000',
            'details'                => 'required|array|min:1',
            'details.*.id_rm'        => 'nullable|exists:data_rm,id',
            'details.*.kode_produksi'=> 'nullable|string|max:255',
            'details.*.kondisi_ruang'=> 'nullable|string|max:255',
            'details.*.waktu_pemeriksaan' => 'nullable|date_format:H:i',
            'details.*.suhu_rm'      => 'nullable|numeric',
            'details.*.suhu_ruang'   => 'nullable|numeric',
            'details.*.suhu_air_thawing' => 'nullable|numeric',
            'details.*.suhu_produk'  => 'nullable|numeric',
            'details.*.kondisi_produk' => 'nullable|string|max:255',
        ]);

        $groupUuid = $parent->group_uuid ?: $parent->uuid;

        $newRecord = ProsesTwahing::create([
            'group_uuid'             => $groupUuid,
            'id_plan'                => $planId,
            'user_id'                => $user->id,
            'id_shift'               => (int) $data['id_shift'],
            'id_produk'              => !empty($data['id_produk']) ? (int) $data['id_produk'] : null,
            'tanggal'                => $data['tanggal'],
            'jam'                    => $data['jam'] ?? null,
            'waktu_thawing_awal'     => $data['waktu_thawing_awal'] ?? null,
            'waktu_thawing_akhir'    => $data['waktu_thawing_akhir'] ?? null,
            'total_waktu_thawing_jam'=> $data['total_waktu_thawing_jam'] ?? null,
            'catatan'                => $data['catatan'] ?? null,
        ]);

        $rows = [];
        foreach (($data['details'] ?? []) as $row) {
            if (empty($row['id_rm'])) {
                continue;
            }

            $rm = DataRm::where('id', $row['id_rm'])->where('id_plan', $planId)->first();
            if (!$rm) {
                continue;
            }

            $rows[] = [
                'uuid'             => (string) Str::uuid(),
                'proses_twahing_id'=> $newRecord->id,
                'id_rm'            => (int) $row['id_rm'],
                'kode_produksi'    => $row['kode_produksi'] ?? null,
                'kondisi_ruang'    => $row['kondisi_ruang'] ?? null,
                'waktu_pemeriksaan'=> $row['waktu_pemeriksaan'] ?? null,
                'suhu_rm'          => $row['suhu_rm'] ?? null,
                'suhu_ruang'       => $row['suhu_ruang'] ?? null,
                'suhu_air_thawing' => $row['suhu_air_thawing'] ?? null,
                'suhu_produk'      => $row['suhu_produk'] ?? null,
                'kondisi_produk'   => $row['kondisi_produk'] ?? null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        }

        if (count($rows) === 0) {
            $newRecord->delete();
            return back()->withErrors(['details' => 'Minimal 1 baris detail wajib memilih Nama RM.'])->withInput();
        }

        ProsesTwahingDetail::insert($rows);

        return redirect()->route('proses-twahing.index')
            ->with('success', 'Data pemeriksaan per 2 jam berhasil ditambahkan.');
    }

    public function destroy($uuid)
    {
        $user = Auth::user();

        $item = ProsesTwahing::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $item->delete();

        return redirect()->route('proses-twahing.index')
            ->with('success', 'Data pemeriksaan proses thawing berhasil dihapus.');
    }

    public function approve(Request $request, $uuid)
    {
        try {
            $request->validate([
                'type' => 'required|in:qc,produksi,spv',
            ]);

            $item = ProsesTwahing::where('uuid', $uuid)->firstOrFail();
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
                    if (!in_array($userRole, [1, 3, 5])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai QC',
                        ], 403);
                    }
                    break;
                case 'produksi':
                    if (!in_array($userRole, [1, 2, 5])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai Produksi',
                        ], 403);
                    }
                    if (!$item->approved_by_qc) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data harus disetujui oleh QC terlebih dahulu',
                        ], 400);
                    }
                    break;
                case 'spv':
                    if (!in_array($userRole, [1, 4, 5])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki wewenang untuk menyetujui sebagai SPV',
                        ], 403);
                    }
                    if (!$item->approved_by_produksi) {
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
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkApproveSelected(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:qc,produksi,spv',
            'uuids' => 'required|array|min:1',
            'uuids.*' => 'required|uuid',
        ]);

        $user = Auth::user();
        $type = $validated['type'];
        $allowedRoles = [
            'qc' => [1, 3, 5],
            'produksi' => [1, 2, 5],
            'spv' => [1, 4, 5],
        ];

        if (!in_array((int) $user->id_role, $allowedRoles[$type], true)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki wewenang untuk approval ini.'], 403);
        }

        $query = ProsesTwahing::whereIn('uuid', $validated['uuids']);
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }
        $items = $query->get();

        if ($items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Data terpilih tidak ditemukan.'], 404);
        }

        $approvalField = "approved_by_{$type}";
        $userField = "approved_by_{$type}_user_id";
        $atField = "approved_by_{$type}_at";
        $pending = $items->where($approvalField, false);
        if ($pending->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Semua data terpilih sudah diverifikasi sebelumnya.']);
        }

        $updateData = [$approvalField => true, $userField => $user->id, $atField => now()];
        \DB::transaction(function () use ($pending, $updateData) {
            foreach ($pending as $item) {
                $groupItems = ProsesTwahing::where('group_uuid', $item->group_uuid)->get();
                foreach ($groupItems as $groupItem) {
                    $groupItem->update($updateData);
                }
                $sourceUuid = $item->is_audit ? $item->source_uuid : $item->uuid;
                if ($sourceUuid) {
                    ProsesTwahing::where('is_audit', false)->where('uuid', $sourceUuid)->update($updateData);
                    ProsesTwahing::where('is_audit', true)->where('source_uuid', $sourceUuid)->update($updateData);
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Berhasil memverifikasi ' . $pending->count() . ' data.']);
    }

    /**
     *  Bulk Approve data proses thawing per shift
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv',
            'tanggal' => 'required|date',
            'shift_id' => 'required|exists:data_shift,id'
        ]);

        $user = Auth::user();
        $type = $request->type;
        $tanggal = $request->tanggal;
        $shift_id = $request->shift_id;

        // Role-based access control
        $allowedRoles = [
            'qc' => [1, 3, 5],
            'produksi' => [1, 2, 5],
            'spv' => [1, 4, 5]
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan approval ini.'
            ], 403);
        }

        // Ambil semua data pada tanggal dan shift tersebut
        $query = ProsesTwahing::whereDate('tanggal', $tanggal)
                              ->where('id_shift', $shift_id);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data ditemukan untuk shift ini.'
            ], 404);
        }

        // Sequential approval validation
        if ($type === 'produksi') {
            $unapprovedByQc = $items->where('approved_by_qc', false)->count();
            if ($unapprovedByQc > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seluruh data pada shift ini harus disetujui oleh QC terlebih dahulu.'
                ], 400);
            }
        }

        if ($type === 'spv') {
            $unapprovedByProduksi = $items->where('approved_by_produksi', false)->count();
            if ($unapprovedByProduksi > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seluruh data pada shift ini harus disetujui oleh Produksi terlebih dahulu.'
                ], 400);
            }
        }

        $approvalField = "approved_by_{$type}";
        $approvedAtField = "approved_by_{$type}_at";
        $approvedByField = "approved_by_{$type}_user_id";
        
        // Update all records one by one to trigger Observers
        \DB::beginTransaction();
        try {
            foreach ($items as $item) {
                if (!$item->$approvalField) {
                    $item->update([
                        $approvalField => true,
                        $approvedByField => $user->id,
                        $approvedAtField => now()
                    ]);
                }
            }
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui data: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Seluruh data pada shift ini berhasil disetujui.'
        ]);
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

            $query = ProsesTwahing::with(['plan', 'user', 'shift', 'produk', 'details.rm'])
                ->when($user->role !== 'superadmin', function ($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($request->has('is_audit') && $request->is_audit == 1) {
                $query->where('is_audit', true);
            } else {
                $query->operasional();
            }

            if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
                $tanggalAwal = \Carbon\Carbon::parse($request->tanggal_awal)->startOfDay();
                $tanggalAkhir = \Carbon\Carbon::parse($request->tanggal_akhir)->endOfDay();
                $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            } elseif ($request->filled('tanggal_awal')) {
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

                if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
                    $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal_awal)->format('d/m/Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d/m/Y');
                } elseif ($request->filled('tanggal_awal')) {
                    $filterInfo[] = 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal_awal)->format('d/m/Y');
                }
                
                if ($request->id_shift) {
                    $shift = DataShift::find($request->id_shift);
                    $filterInfo[] = 'Shift: ' . ($shift ? $shift->shift : 'Unknown');
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

            $kode_form = $request->kode_form;
            $pdf = \PDF::loadView('qc-sistem.proses_twahing.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A4', 'landscape');

            $filename = 'proses_twahing_' . date('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function showLogs($uuid)
    {
        // Authorization: superadmin or can('view-logs')
        if (!auth()->check()) {
            abort(403, 'Unauthorized access to logs');
        }

        $item = ProsesTwahing::where('uuid', $uuid)->firstOrFail();
        $item->load(['plan', 'user', 'shift']);

        $logs = \App\Models\ProsesTwahingLog::where('proses_twahing_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.proses_twahing.logs', compact('item', 'logs'));
    }

    public function getLogsJson($uuid)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $item = ProsesTwahing::where('uuid', $uuid)->firstOrFail();

        $logs = \App\Models\ProsesTwahingLog::where('proses_twahing_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($logs);
    }

    public function bulkExportExcel(Request $request, \App\Services\BulkExport\ProsesTwahingBulkExportService $exportService)
    {
        return $exportService->exportExcel($request);
    }
    public function copyToAudit($uuid)
    {
        $item = ProsesTwahing::where('uuid', $uuid)->firstOrFail();

        // Jika item yang diklik sudah merupakan record audit
        if ($item->is_audit) {
            return redirect()->route('proses-twahing.edit', $item->uuid);
        }

        // Jika data operasional ini sudah memiliki versi audit sebelumnya
        if ($item->auditVersion) {
            return redirect()->route('proses-twahing.edit', $item->auditVersion->uuid);
        }

        // Jika belum memiliki versi audit, buatkan salinan audit secara otomatis
        $clone = $item->copyToAudit();

        return redirect()->route('proses-twahing.edit', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }
}
