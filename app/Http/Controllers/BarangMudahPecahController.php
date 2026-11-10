<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasAuditController;
use App\Models\BarangMudahPecah;
use App\Models\BarangMudahPecahLog;
use App\Models\DataBarang;
use App\Models\DataShift;
use App\Models\InputArea;
use App\Models\Plan;
use App\Models\SubArea;
use App\Services\BulkExport\BarangMudahPecahBulkExportService;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangMudahPecahController extends Controller
{
    use HasAuditController;

    protected function auditModel(): string
    {
        return BarangMudahPecah::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'barang-mudah-pecah';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.barang_mudah_pecah';
    }

    protected function auditDateColumn(): string
    {
        return 'tanggal';
    }

    protected function auditTimeColumn(): string
    {
        return 'tanggal';
    }

    protected function auditSearchColumns(): array
    {
        return [];
    }

    /**
     * Ambil daftar shift sesuai role user.
     * Superadmin melihat semua shift, user lain hanya shift di plan-nya.
     */
    protected function getShiftsForUser($user)
    {
        if ($user->role === 'superadmin') {
            return DataShift::orderBy('shift')->get();
        }

        return DataShift::where('id_plan', $user->id_plan)
            ->orderBy('shift')
            ->get();
    }

    /**
     * Terapkan filter pencarian (area, nama barang, items_json, tanggal) ke query.
     */
    protected function applySearchFilter($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->whereHas('area', function ($a) use ($search) {
                $a->where('area', 'like', "%{$search}%");
            })
                ->orWhereHas('namaBarang', function ($b) use ($search) {
                    $b->where('nama_barang', 'like', "%{$search}%");
                })
                ->orWhere('nama_barang_manual', 'like', "%{$search}%")
                ->orWhere('items_json', 'like', "%{$search}%")
                ->orWhere('tanggal', 'like', "%{$search}%");
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Auditor langsung diarahkan ke halaman audit
        if ($user->id_role == 6) {
            return redirect()->route('barang-mudah-pecah.audit');
        }

        $search = trim((string) $request->input('search', ''));
        $perPage = (int) ($request->input('per_page') ?? 5);
        if (! in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 5;
        }

        // Daftar shift untuk modal Export PDF di view
        $shifts = $this->getShiftsForUser($user);

        $query = BarangMudahPecah::with(['user', 'plan', 'shift', 'namaBarang', 'qcApprover', 'produksiApprover', 'spvApprover', 'area'])
            ->operasional();

        // Role-based data filtering
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search !== '') {
            $this->applySearchFilter($query, $search);
        }

        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        // Data datar: satu baris = satu record
        $this->applyDateRangeFilter(request(), $query);
        $barangMudahPecah = $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Alias agar view yang memakai $items maupun $barangMudahPecah sama-sama jalan
        $items = $barangMudahPecah;

        return view('qc-sistem.barang_mudah_pecah.index', compact('barangMudahPecah', 'items', 'shifts', 'search', 'perPage'));
    }

    /**
     * Display audit listing (data datar: satu baris = satu record).
     * Menimpa auditIndex dari trait HasAuditController.
     */
    public function auditIndex(Request $request)
    {
        $user = Auth::user();
        $search = trim((string) $request->input('search', ''));
        $perPage = (int) ($request->input('per_page') ?? 5);
        if (! in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 5;
        }

        // Daftar shift untuk modal Cetak PDF di view audit
        $shifts = $this->getShiftsForUser($user);

        $auditedSourceUuids = BarangMudahPecah::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $query = BarangMudahPecah::with(['user', 'plan', 'shift', 'namaBarang', 'qcApprover', 'produksiApprover', 'spvApprover', 'area'])
            ->where(function ($q) use ($auditedSourceUuids) {
                $q->where('is_audit', true)
                    ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                        $subQ->where('is_audit', false);
                        if (! empty($auditedSourceUuids)) {
                            $subQ->whereNotIn('uuid', $auditedSourceUuids);
                        }
                    });
            });

        // Role-based data filtering
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search !== '') {
            $this->applySearchFilter($query, $search);
        }

        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        $this->applyDateRangeFilter(request(), $query);
        $items = $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Alias agar view yang memakai $records maupun $items sama-sama jalan
        $records = $items;

        return view('qc-sistem.barang_mudah_pecah.index_audit', compact('items', 'records', 'shifts', 'search', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $areas = collect();
        $barangByArea = collect();

        // Get shifts based on user role
        if ($user->role === 'superadmin') {
            $shifts = DataShift::with('plan')->get();
            $plans = Plan::all();
            $dataBarang = DataBarang::all();
        } else {
            $shifts = DataShift::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $dataBarang = DataBarang::where('id_plan', $user->id_plan)->get();

            if ($user->isPlanNgoro()) {
                $areas = InputArea::where('id_plan', $user->id_plan)
                    ->whereIn('area', ['Meat preparation', 'Packing Primary', 'Packing Secondary'])
                    ->orderBy('area')
                    ->get(['id', 'area']);
            } else {
                $areas = InputArea::where('id_plan', $user->id_plan)
                    ->orderBy('area')
                    ->get(['id', 'area']);
            }

            $barangByArea = DataBarang::where('id_plan', $user->id_plan)
                ->orderBy('nama_barang')
                ->get(['id', 'id_area', 'nama_barang', 'jumlah'])
                ->groupBy('id_area');
        }

        return view('qc-sistem.barang_mudah_pecah.create', compact('shifts', 'plans', 'dataBarang', 'areas', 'barangByArea'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($request->input('id_sub_area_manual') === '') {
            $request->merge(['id_sub_area_manual' => null]);
        }

        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'items' => 'required|array|min:1',
            'items.*.jumlah' => 'required|integer',
            'items.*.kondisi' => 'nullable|in:OK,Tidak OK',
            'items.*.verifikasi_hasil' => 'nullable|in:OK,Tidak OK',
            'items_mapping' => 'required|json',
            'is_manual' => 'nullable|boolean',
            'id_area_manual' => 'required_if:is_manual,1|exists:input_area,id',
            'id_sub_area_manual' => 'nullable|exists:sub_area,id',
            'nama_barang_manual' => 'required_if:is_manual,1|string|max:255',
            'jumlah_manual' => 'required_if:is_manual,1|integer',
            'nama_karyawan' => 'nullable|string|max:255',
            'kondisi_manual' => 'required_if:is_manual,1|in:OK,Tidak OK',
            'temuan_ketidaksesuaian_manual' => 'nullable|string|max:1000',
            'tindakan_koreksi_manual' => 'nullable|string|max:1000',
            'verifikasi_hasil_manual' => 'nullable|in:OK,Tidak OK',
        ]);

        // Get the selected shift to validate plan access and get plan_id
        $selectedShift = DataShift::findOrFail($request->shift_id);

        // Additional validation for non-superadmin users
        if ($user->role !== 'superadmin') {
            if ($selectedShift->id_plan !== $user->id_plan) {
                return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.']);
            }
        }

        $effectivePlanId = ($user->role === 'superadmin') ? $selectedShift->id_plan : $user->id_plan;

        $canFillTemuan = $user->isQc() || $user->role === 'superadmin' || (int) ($user->id_role ?? 0) === 5;

        $itemsData = [];
        $mapping = json_decode($request->items_mapping, true) ?? [];
        foreach ((array) $request->items as $idx => $row) {
            $kondisi = isset($row['kondisi']) ? trim($row['kondisi']) : '';
            if ($kondisi === '' || $kondisi === 'Pilih Kondisi') {
                continue;
            }

            $map = $mapping[$idx] ?? [];
            $idArea = $map['id_area'] ?? null;
            $idNamaBarang = $map['id_nama_barang'] ?? null;

            if (! $idArea || ! $idNamaBarang) {
                continue;
            }

            $barang = DataBarang::findOrFail($idNamaBarang);

            $itemsData[] = [
                'id_area' => $idArea,
                'id_nama_barang' => $idNamaBarang,
                'nama_barang' => $barang->nama_barang,
                'jumlah' => $row['jumlah'],
                'kondisi' => $kondisi,
                'temuan_ketidaksesuaian' => $canFillTemuan ? ($row['temuan_ketidaksesuaian'] ?? null) : null,
                'tindakan_koreksi' => $row['tindakan_koreksi'] ?? null,
                'verifikasi_hasil' => $canFillTemuan ? ($row['verifikasi_hasil'] ?? null) : null,
            ];
        }

        if (! empty($itemsData)) {
            BarangMudahPecah::create([
                'user_id' => $user->id,
                'id_plan' => $effectivePlanId,
                'shift_id' => $request->shift_id,
                'tanggal' => $request->tanggal,
                'jam' => $request->jam,
                'items_json' => $itemsData,
                'is_manual' => false,
            ]);
        }

        $isManual = $request->has('is_manual');
        if ($isManual) {
            $manualArea = InputArea::findOrFail($request->id_area_manual);
            if ((int) $manualArea->id_plan !== (int) $effectivePlanId) {
                return back()->withErrors(['id_area_manual' => 'Area manual tidak sesuai dengan plan yang dipilih.'])->withInput();
            }

            if ($request->filled('id_sub_area_manual')) {
                $subArea = SubArea::findOrFail($request->id_sub_area_manual);
                if ((int) $subArea->id_input_area !== (int) $request->id_area_manual) {
                    return back()->withErrors(['id_sub_area_manual' => 'Sub Area tidak sesuai dengan Area yang dipilih.'])->withInput();
                }
            }

            BarangMudahPecah::create([
                'user_id' => $user->id,
                'id_plan' => $effectivePlanId,
                'shift_id' => $request->shift_id,
                'tanggal' => $request->tanggal,
                'jam' => $request->jam,
                'id_area' => $request->id_area_manual,
                'id_sub_area' => $request->id_sub_area_manual,
                'is_manual' => true,
                'id_nama_barang' => null,
                'nama_barang_manual' => $request->nama_barang_manual,
                'jumlah' => $request->jumlah_manual,
                'nama_karyawan' => $request->nama_karyawan,
                'kondisi' => $request->kondisi_manual,
                'temuan_ketidaksesuaian' => $canFillTemuan ? $request->temuan_ketidaksesuaian_manual : null,
                'tindakan_koreksi' => $request->tindakan_koreksi_manual,
                'verifikasi_hasil' => $canFillTemuan ? ($request->verifikasi_hasil_manual ?? null) : null,
            ]);
        }

        return redirect()->route('barang-mudah-pecah.index')
            ->with('success', 'Data barang mudah pecah berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $barangMudahPecah = BarangMudahPecah::with(['user', 'plan', 'shift', 'namaBarang', 'area', 'subArea'])
            ->where('uuid', $uuid)
            ->firstOrFail();
        $user = Auth::user();

        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $barangMudahPecah->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $groupItems = BarangMudahPecah::with(['user', 'plan', 'shift', 'namaBarang', 'area', 'subArea'])
            ->where('is_audit', $barangMudahPecah->is_audit)
            ->where('id_plan', $barangMudahPecah->id_plan)
            ->where('shift_id', $barangMudahPecah->shift_id)
            ->where('jam', $barangMudahPecah->jam)
            ->whereDate('tanggal', Carbon::parse($barangMudahPecah->tanggal)->format('Y-m-d'))
            ->orderBy('created_at', 'asc')
            ->get();

        return view('qc-sistem.barang_mudah_pecah.show', compact('barangMudahPecah', 'groupItems'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $barangMudahPecah = BarangMudahPecah::with(['shift', 'area'])->where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $barangMudahPecah->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        $groupItems = BarangMudahPecah::with(['namaBarang', 'area', 'subArea'])
            ->where('is_audit', $barangMudahPecah->is_audit)
            ->where('id_plan', $barangMudahPecah->id_plan)
            ->where('shift_id', $barangMudahPecah->shift_id)
            ->where('jam', $barangMudahPecah->jam)
            ->whereDate('tanggal', Carbon::parse($barangMudahPecah->tanggal)->format('Y-m-d'))
            ->orderBy('created_at', 'asc')
            ->get();

        // Get shifts based on user role
        if ($user->role === 'superadmin') {
            $shifts = DataShift::with('plan')->get();
            $plans = Plan::all();
            $dataBarang = DataBarang::all();
        } else {
            $shifts = DataShift::with('plan')
                ->where('id_plan', $user->id_plan)
                ->get();
            $plans = Plan::where('id', $user->id_plan)->get();
            $dataBarang = DataBarang::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.barang_mudah_pecah.edit', compact('barangMudahPecah', 'groupItems', 'shifts', 'plans', 'dataBarang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $barangMudahPecah = BarangMudahPecah::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $barangMudahPecah->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        $isSuperadmin = $user->role === 'superadmin' || (int) ($user->id_role ?? 0) === 5;
        $isQcInspector = ! $isSuperadmin && ($user->isQcInspector() || (int) ($user->id_role ?? 0) === 3);
        $canEditTemuan = $isSuperadmin || $user->isQc();
        $canEditOthers = $isSuperadmin || ! $isQcInspector;

        // Jika edit via tabel (seperti create): update massal kondisi per item
        if ($request->has('items')) {
            $rules = [
                'items' => 'required|array|min:1',
                'items.*.record_uuid' => 'required|exists:barang_mudah_pecah,uuid',
                'items.*.kondisi' => $canEditOthers ? 'required|in:OK,Tidak OK' : 'nullable|in:OK,Tidak OK',
                'items.*.temuan_ketidaksesuaian' => 'nullable|string|max:1000',
                'items.*.tindakan_koreksi' => 'nullable|string|max:1000',
                'items.*.nama_karyawan' => 'nullable|string|max:255',
                'items.*.verifikasi_hasil' => 'nullable|in:OK,Tidak OK',
            ];
            $request->validate($rules);

            foreach ((array) $request->items as $idx => $row) {
                $record = BarangMudahPecah::where('uuid', $row['record_uuid'])->firstOrFail();

                if ($user->role !== 'superadmin' && (int) $record->id_plan !== (int) $user->id_plan) {
                    abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
                }

                // Hanya role selain QC Inspector yang boleh update tanggal dan jam
                if ($canEditOthers && $request->filled('tanggal') && $request->filled('jam')) {
                    $record->tanggal = Carbon::parse(str_replace('T', ' ', $request->tanggal));
                    $record->jam = Carbon::parse($request->jam)->format('H:i');
                }

                if (isset($row['json_index'])) {
                    $itemsJson = $record->items_json;
                    if (isset($itemsJson[$row['json_index']])) {
                        // Jika berhak edit field umum (Produksi / SPV QC / Superadmin)
                        if ($canEditOthers && isset($row['kondisi'])) {
                            $itemsJson[$row['json_index']]['kondisi'] = $row['kondisi'];
                            $itemsJson[$row['json_index']]['tindakan_koreksi'] = $row['tindakan_koreksi'] ?? null;
                        }

                        // Jika berhak edit temuan & verifikasi hasil (QC Inspector / SPV QC / Superadmin)
                        if ($canEditTemuan) {
                            $itemsJson[$row['json_index']]['temuan_ketidaksesuaian'] = $row['temuan_ketidaksesuaian'] ?? null;
                            $itemsJson[$row['json_index']]['verifikasi_hasil'] = $row['verifikasi_hasil'] ?? null;
                        }

                        $record->items_json = $itemsJson;
                    }
                } else {
                    // Non-json row (misal manual item)
                    if ($canEditOthers) {
                        if (isset($row['kondisi'])) {
                            $record->kondisi = $row['kondisi'];
                        }
                        $record->tindakan_koreksi = $row['tindakan_koreksi'] ?? null;
                        if (array_key_exists('nama_karyawan', $row)) {
                            $record->nama_karyawan = $row['nama_karyawan'];
                        }
                    }

                    if ($canEditTemuan) {
                        $record->temuan_ketidaksesuaian = $row['temuan_ketidaksesuaian'] ?? null;
                        $record->verifikasi_hasil = $row['verifikasi_hasil'] ?? null;
                    }
                }

                $record->save();
            }

            return redirect()->route($barangMudahPecah->is_audit ? 'barang-mudah-pecah.audit' : 'barang-mudah-pecah.index')
                ->with('success', 'Data barang mudah pecah berhasil diperbarui.');
        }

        // Fallback: update lama (single row)
        $request->validate([
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'id_area' => 'required|exists:input_area,id',
            'id_sub_area' => 'nullable|exists:sub_area,id',
            'is_manual' => 'nullable|boolean',
            'id_nama_barang' => 'required_if:is_manual,null|exists:data_barang,id',
            'nama_barang_manual' => 'required_if:is_manual,1|string|max:255',
            'jumlah' => 'required|integer',
            'nama_karyawan' => 'nullable|string|max:255',
            'kondisi' => 'required|in:OK,Tidak OK',
            'temuan_ketidaksesuaian' => 'nullable|string|max:1000',
            'tindakan_koreksi' => 'nullable|string|max:1000',
        ]);

        // Get the selected shift to validate plan access
        $selectedShift = DataShift::findOrFail($request->shift_id);

        // Additional validation for non-superadmin users
        if ($user->role !== 'superadmin') {
            if ($selectedShift->id_plan !== $user->id_plan) {
                return back()->withErrors(['shift_id' => 'Anda tidak memiliki akses ke shift ini.']);
            }
        }

        $effectivePlanId = ($user->role === 'superadmin') ? $selectedShift->id_plan : $user->id_plan;

        $selectedArea = InputArea::findOrFail($request->id_area);
        if ((int) $selectedArea->id_plan !== (int) $effectivePlanId) {
            $areaMessage = ($user->role === 'superadmin')
                ? 'Area tidak sesuai dengan plan shift yang dipilih.'
                : 'Area tidak sesuai dengan plan yang sedang login.';

            return back()->withErrors(['id_area' => $areaMessage])->withInput();
        }

        if ($request->filled('id_sub_area')) {
            $subArea = SubArea::findOrFail($request->id_sub_area);
            if ((int) $subArea->id_input_area !== (int) $request->id_area) {
                return back()->withErrors(['id_sub_area' => 'Sub Area tidak sesuai dengan Area yang dipilih.'])->withInput();
            }
        }

        $isManual = $request->boolean('is_manual');

        if (! $isManual) {
            $barang = DataBarang::findOrFail($request->id_nama_barang);
            if ((int) $barang->id_plan !== (int) $effectivePlanId || (int) $barang->id_area !== (int) $request->id_area) {
                return back()->withErrors(['id_nama_barang' => 'Nama barang tidak sesuai dengan Area yang dipilih.'])->withInput();
            }
        }

        $updateData = [];
        if ($canEditOthers) {
            $updateData['id_plan'] = $effectivePlanId;
            $updateData['shift_id'] = $request->shift_id;
            $updateData['tanggal'] = $request->tanggal;
            $updateData['id_area'] = $request->id_area;
            $updateData['id_sub_area'] = $request->id_sub_area;
            $updateData['is_manual'] = $isManual;
            $updateData['id_nama_barang'] = ! $isManual ? $request->id_nama_barang : null;
            $updateData['nama_barang_manual'] = $isManual ? $request->nama_barang_manual : null;
            $updateData['jumlah'] = $request->jumlah;
            $updateData['nama_karyawan'] = $isManual ? $request->nama_karyawan : null;
            $updateData['kondisi'] = $request->kondisi;
            $updateData['tindakan_koreksi'] = $request->tindakan_koreksi;
        }
        if ($canEditTemuan) {
            $updateData['temuan_ketidaksesuaian'] = $request->temuan_ketidaksesuaian;
            $updateData['verifikasi_hasil'] = $request->verifikasi_hasil;
        }

        $barangMudahPecah->update($updateData);

        return redirect()->route($barangMudahPecah->is_audit ? 'barang-mudah-pecah.audit' : 'barang-mudah-pecah.index')
            ->with('success', 'Data barang mudah pecah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $barangMudahPecah = BarangMudahPecah::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Check access for non-superadmin users
        if ($user->role !== 'superadmin' && $barangMudahPecah->id_plan !== $user->id_plan) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $barangMudahPecah->delete();

        return redirect()->route('barang-mudah-pecah.index')
            ->with('success', 'Data barang mudah pecah berhasil dihapus.');
    }

    /**
     * Show logs for the specified resource.
     */
    public function showLogs($uuid)
    {
        $item = BarangMudahPecah::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        $logs = BarangMudahPecahLog::where('barang_mudah_pecah_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.barang_mudah_pecah.logs', compact('item', 'logs'));
    }

    /**
     * Get logs JSON for the specified resource.
     */
    public function getLogsJson($uuid)
    {
        $item = BarangMudahPecah::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Authorization check
        if ($user->role !== 'superadmin' && $item->id_plan !== $user->id_plan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = BarangMudahPecahLog::where('barang_mudah_pecah_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    /**
     * Export single group PDF by UUID
     */
    public function exportPdf($uuid)
    {
        try {
            $user = auth()->user();

            $barangMudahPecah = BarangMudahPecah::with(['user', 'plan', 'shift', 'namaBarang', 'area', 'subArea'])
                ->where('uuid', $uuid)
                ->firstOrFail();

            // Check access for non-superadmin users
            if ($user->role !== 'superadmin' && $barangMudahPecah->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }

            // Fetch the whole group (same tanggal, shift, area, jam)
            $data = BarangMudahPecah::with(['user', 'plan', 'shift', 'namaBarang', 'area', 'subArea',
                'qcApprover', 'produksiApprover', 'spvApprover'])
                ->operasional()
                ->where('id_plan', $barangMudahPecah->id_plan)
                ->where('shift_id', $barangMudahPecah->shift_id)
                ->where('jam', $barangMudahPecah->jam)
                ->whereDate('tanggal', Carbon::parse($barangMudahPecah->tanggal)->format('Y-m-d'))
                ->orderBy('created_at', 'asc')
                ->get();

            $kode_form = 'QF 30/00';

            $pdf = PDF::loadView('qc-sistem.barang_mudah_pecah.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A4', 'landscape');

            $filename = 'barang_mudah_pecah_'.($barangMudahPecah->tanggal ? Carbon::parse($barangMudahPecah->tanggal)->format('Y-m-d') : date('Y-m-d')).'.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: '.$e->getMessage());
        }
    }

    /**
     * Export PDF with filters
     */
    public function bulkExportPdf(Request $request)
    {
        try {
            $request->validate([
                'tanggal_awal' => 'nullable|date',
                'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
                'shift_id' => 'nullable|integer',
                'id_area' => 'nullable|integer|exists:input_area,id',
                'kode_form' => 'required|string|max:50',
            ]);

            $user = auth()->user();

            $query = BarangMudahPecah::with(['plan', 'user', 'shift', 'namaBarang']);

            if ($request->has('is_audit') && $request->is_audit == 1) {
                $query->where('is_audit', true);
            } else {
                $query->operasional();
            }

            $query->when($user->role !== 'superadmin', function ($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

            // Apply filters
            if ($request->tanggal_awal && $request->tanggal_akhir) {
                $tanggalAwal = Carbon::parse($request->tanggal_awal)->startOfDay();
                $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
                $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            } elseif ($request->tanggal_awal) {
                $query->whereDate('tanggal', $request->tanggal_awal);
            }

            if ($request->shift_id) {
                $query->where('shift_id', $request->shift_id);
            }

            if ($request->id_area) {
                $query->where('id_area', $request->id_area);
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            // Save kode_form to all filtered records
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
                if ($request->shift_id) {
                    $shift = DataShift::find($request->shift_id);
                    $filterInfo[] = 'Shift: '.($shift ? $shift->shift : 'Unknown');
                }
                if ($request->id_area) {
                    $area = InputArea::find($request->id_area);
                    $filterInfo[] = 'Area: '.($area ? $area->area : 'Unknown');
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
            $pdf = \PDF::loadView('qc-sistem.barang_mudah_pecah.export_pdf', compact('data', 'kode_form'));
            $pdf->setPaper('A4', 'landscape');

            $filename = 'barang_mudah_pecah_'.date('Y-m-d_H-i-s').'.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export Excel with filters, layout-nya disamakan dengan bulkExportPdf().
     */
    public function bulkExportExcel(Request $request, BarangMudahPecahBulkExportService $service)
    {
        try {
            return $service->exportExcel($request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor Excel: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ambil sub area berdasarkan area
     */
    public function getSubAreasByArea($areaId)
    {
        $subAreas = SubArea::where('id_input_area', $areaId)->get();

        return response()->json($subAreas);
    }

    /**
     * Bulk Approve data barang mudah pecah per shift
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv',
            'tanggal' => 'required|date',
            'shift_id' => 'required|exists:data_shift,id',
        ]);

        $user = Auth::user();
        $type = $request->type;
        $tanggal = $request->tanggal;
        $shift_id = $request->shift_id;

        // Role-based access control
        $allowedRoles = [
            'qc' => [1, 3, 5],
            'produksi' => [1, 2, 5],
            'spv' => [1, 4, 5],
        ];

        if (! in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan approval ini.',
            ], 403);
        }

        // Ambil semua data pada tanggal dan shift tersebut
        $query = BarangMudahPecah::whereDate('tanggal', $tanggal)
            ->where('shift_id', $shift_id)
            ->operasional();

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data ditemukan untuk shift ini.',
            ], 404);
        }

        // Sequential approval validation
        if ($type === 'produksi') {
            $unapprovedByQc = $items->where('approved_by_qc', false)->count();
            if ($unapprovedByQc > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seluruh data pada shift ini harus disetujui oleh QC terlebih dahulu.',
                ], 400);
            }
        }

        if ($type === 'spv') {
            $unapprovedByProduksi = $items->where('approved_by_produksi', false)->count();
            if ($unapprovedByProduksi > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seluruh data pada shift ini harus disetujui oleh Produksi terlebih dahulu.',
                ], 400);
            }
        }

        $approvalField = "approved_by_{$type}";

        // Update all records
        $query->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now(),
        ]);

        // Log approval activity for each record
        foreach ($items as $item) {
            // Cek jika data ini sebelumnya belum di-approve tipe ini
            if (! $item->$approvalField) {
                BarangMudahPecahLog::create([
                    'barang_mudah_pecah_id' => $item->id,
                    'barang_mudah_pecah_uuid' => $item->uuid,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->id_role,
                    'aksi' => 'Bulk Approval '.ucfirst($type),
                    'field_yang_diubah' => ['approval_'.$type],
                    'nilai_lama' => [false],
                    'nilai_baru' => [true],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'keterangan' => 'Data disetujui secara massal (per shift) oleh '.ucfirst($type),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Seluruh data pada shift ini berhasil disetujui.',
        ]);
    }

    /**
     * Approve data with role-based validation
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv',
        ]);

        $barangMudahPecah = BarangMudahPecah::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based authorization
        $allowedRoles = [
            'qc' => [1, 3, 5], // Role IDs yang bisa approve QC
            'produksi' => [2, 5], // Role IDs yang bisa approve Produksi
            'spv' => [4, 5], // Role IDs yang bisa approve SPV
        ];

        if (! in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki wewenang untuk melakukan persetujuan ini.',
            ], 403);
        }

        // Sequential approval validation (QC → Produksi → SPV)
        if ($type === 'produksi' && ! $barangMudahPecah->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.',
            ], 400);
        }

        if ($type === 'spv' && ! $barangMudahPecah->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.',
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($barangMudahPecah->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.',
            ], 400);
        }

        // Update approval
        $barangMudahPecah->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.',
        ]);
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
            $query = BarangMudahPecah::whereIn('uuid', $uuids)
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
                "{$type}_approved_by" => $user->id,
                "{$type}_approved_at" => now(),
            ];

            BarangMudahPecah::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function ($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();

            // Update audit copies with same approval data
            BarangMudahPecah::where('is_audit', true)
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
}
