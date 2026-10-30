<?php

namespace App\Http\Controllers;

use App\Models\Thermometer;
use App\Models\Plan;
use App\Models\DataShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\ThermometerLog;
use App\Models\DataThermo;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Traits\HasNoDataHtmlResponse;
use App\Imports\ThermometerImport;
use App\Services\BulkExport\ThermometerBulkExportService;

class ThermometerController extends Controller
{
    use \App\Http\Controllers\Traits\HasAuditController, HasNoDataHtmlResponse, \App\Http\Controllers\Traits\HasExcelImport;

    protected function auditModel(): string
    {
        return Thermometer::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'thermometer';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.thermometer';
    }

    protected function auditDateColumn(): string
    {
        return 'tanggal';
    }

    protected function auditTimeColumn(): string
    {
        return 'tanggal';
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
     * Terapkan filter pencarian ke query.
     */
    protected function applySearchFilter($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('jenis', 'LIKE', '%' . $search . '%')
              ->orWhere('kode_thermometer', 'LIKE', '%' . $search . '%')
              ->orWhere('hasil_pengecekan', 'LIKE', '%' . $search . '%')
              ->orWhere('tanggal', 'LIKE', '%' . $search . '%')
              ->orWhere('entries', 'LIKE', '%' . $search . '%');
        });
    }

    /**
     * Menampilkan daftar data thermometer
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Auditor langsung diarahkan ke halaman audit
        if ($user->id_role == 6) {
            return redirect()->route('thermometer.audit');
        }

        $search = trim((string) $request->input('search', ''));
        $perPage = (int) ($request->input('per_page') ?? 5);
        if (!in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 5;
        }

        // Daftar shift untuk modal Export PDF di view
        $shifts = $this->getShiftsForUser($user);

        $query = Thermometer::with(['plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->operasional()
            ->filterByRole();

        if ($search !== '') {
            $this->applySearchFilter($query, $search);
        }

        // Data datar: satu baris = satu record
        $this->applyDateRangeFilter(request(), $query);
        $thermometer = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->orderBy('shift_id', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Alias agar view yang memakai $items maupun $thermometer sama-sama jalan
        $items = $thermometer;

        return view('qc-sistem.thermometer.index', compact('thermometer', 'items', 'shifts', 'search', 'perPage'));
    }

    /**
     * Audit Index for Thermometer
     */
    public function auditIndex(Request $request)
    {
        $user = Auth::user();

        $search = trim((string) $request->input('search', ''));
        $perPage = (int) ($request->input('per_page') ?? 5);
        if (!in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 5;
        }

        // Daftar shift untuk modal Cetak PDF di view audit
        $shifts = $this->getShiftsForUser($user);

        $auditedSourceUuids = Thermometer::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $query = Thermometer::with(['plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->where(function ($q) use ($auditedSourceUuids) {
                $q->where('is_audit', true)
                  ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                      $subQ->where('is_audit', false);
                      if (!empty($auditedSourceUuids)) {
                          $subQ->whereNotIn('uuid', $auditedSourceUuids);
                      }
                  });
            })
            ->filterByRole();

        if ($search !== '') {
            $this->applySearchFilter($query, $search);
        }

        $this->applyDateRangeFilter(request(), $query);
        $items = $query->orderByRaw('DATE(tanggal) DESC')
            ->orderByRaw('TIME(jam) DESC')
            ->orderBy('shift_id', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Alias agar view yang memakai $records maupun $items sama-sama jalan
        $records = $items;

        return view('qc-sistem.thermometer.index_audit', compact('items', 'records', 'shifts', 'search', 'perPage'));
    }

    /**
     * Menampilkan form untuk membuat data thermometer baru
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();

        // Filter data berdasarkan role - id_plan dan user_id akan diisi otomatis dari controller
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
            $dataThermo = DataThermo::all();
            $inputAreas = \App\Models\InputArea::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $dataThermo = DataThermo::where('id_plan', $user->id_plan)->get();
            $inputAreas = \App\Models\InputArea::where('id_plan', $user->id_plan)->get();
        }

        $hasilPengecekanOptions = Thermometer::getHasilPengecekanOptions();

        return view('qc-sistem.thermometer.create', compact('plans', 'shifts', 'dataThermo', 'inputAreas', 'hasilPengecekanOptions'));
    }

    /**
     * Menyimpan data thermometer baru ke database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal'  => 'required|date',
            'jam'      => 'required',
            'entries'  => 'required|array|min:1',
            'entries.*.jenis' => 'required|string|max:255',
            'entries.*.kode_thermometer' => 'required|string|max:255',
            'entries.*.area' => 'nullable|string|max:255',
            'entries.*.hasil_pengecekan' => 'required|in:ok,tidak_ok',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $tanggalData = \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s');

        Thermometer::create([
            'uuid'     => (string) Str::uuid(),
            'user_id'  => $user->id,
            'id_plan'  => $user->id_plan,
            'shift_id' => $request->shift_id,
            'tanggal'  => $tanggalData,
            'jam'      => $request->jam,
            'entries'  => $request->entries,
        ]);

        return redirect()->route('thermometer.index')
            ->with('success', count($request->entries) . ' data thermometer berhasil disimpan.');
    }

    /**
     * Menampilkan detail data thermometer
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $data = Thermometer::with(['plan', 'shift', 'user'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Cek akses data berdasarkan plan user
        if (!$data->canAccess()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }

        if ($redirect = $this->redirectToCanonicalAuditUrl('thermometer.show', $data)) {
            return $redirect;
        }

        return view('qc-sistem.thermometer.show', compact('data'));
    }

    /**
     * Menampilkan form untuk edit data thermometer
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $data = Thermometer::where('uuid', $uuid)->firstOrFail();

        $user = Auth::user();
        $isAuditMode = (bool) $data->is_audit;
        $queryParameters = request()->query();
        $hasAuditFlag = array_key_exists('is_audit', $queryParameters);

        if (($isAuditMode && request()->query('is_audit') !== '1') || (!$isAuditMode && $hasAuditFlag)) {
            unset($queryParameters['is_audit'], $queryParameters['uuid']);
            if ($isAuditMode) {
                $queryParameters['is_audit'] = '1';
            }

            return redirect()->route(
                'thermometer.edit',
                array_merge(['uuid' => $data->uuid], $queryParameters)
            );
        }

        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $shifts = DataShift::all();
            $dataThermo = DataThermo::all();
            $inputAreas = \App\Models\InputArea::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $shifts = DataShift::where('id_plan', $user->id_plan)->get();
            $dataThermo = DataThermo::where('id_plan', $user->id_plan)->get();
            $inputAreas = \App\Models\InputArea::where('id_plan', $user->id_plan)->get();
        }

        $hasilPengecekanOptions = Thermometer::getHasilPengecekanOptions();

        return view('qc-sistem.thermometer.edit', compact('data', 'plans', 'shifts', 'dataThermo', 'inputAreas', 'hasilPengecekanOptions'));
    }

    /**
     * Update data thermometer di database
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        $data = Thermometer::where('uuid', $uuid)->firstOrFail();

        // Cek akses data berdasarkan plan user
        if (!$data->canAccess()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }

        $validator = Validator::make($request->all(), [
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'entries' => 'required|array|min:1',
            'entries.*.jenis' => 'required|string',
            'entries.*.kode_thermometer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data->update([
            'shift_id' => $request->shift_id,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'entries' => $request->entries,
        ]);

        return redirect()->route($data->is_audit ? 'thermometer.audit' : 'thermometer.index')
            ->with('success', 'Data Thermometer berhasil diupdate.');
    }

    /**
     * Hapus data thermometer dari database
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $data = Thermometer::where('uuid', $uuid)->firstOrFail();

        // Cek akses data berdasarkan plan user
        if (!$data->canAccess()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }

        $data->delete();

        return redirect()->route('thermometer.index')
            ->with('success', 'Data Thermometer berhasil dihapus.');
    }

    /**
     * Show logs for the specified resource.
     */
    public function showLogs($uuid)
    {
        $item = Thermometer::where('uuid', $uuid)->firstOrFail();

        // Authorization check
        if (!$item->canAccess()) {
            abort(403, 'Unauthorized action.');
        }

        $logs = ThermometerLog::where('thermometer_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('qc-sistem.thermometer.logs', compact('item', 'logs'));
    }

    /**
     * Get logs JSON for the specified resource.
     */
    public function getLogsJson($uuid)
    {
        $item = Thermometer::where('uuid', $uuid)->firstOrFail();

        // Authorization check
        if (!$item->canAccess()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = ThermometerLog::where('thermometer_id', $item->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    /**
     * Bulk export PDF with filters
     */
    public function bulkExportPdf(Request $request)
    {
        // Kompatibilitas nama field dari modal (tanggal_mulai -> tanggal_awal)
        // dan kode_form default (modal audit tidak punya input kode_form)
        $request->merge([
            'tanggal_awal' => $request->input('tanggal_awal') ?: $request->input('tanggal_mulai'),
            'kode_form'    => $request->input('kode_form') ?: 'QF 32/00',
        ]);

        $validator = Validator::make($request->all(), [
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'shift_id' => 'nullable|exists:data_shift,id',
            'kode_form' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();

        // Build query with filters
        $query = Thermometer::with(['plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover']);

        if ($request->has('is_audit') && $request->is_audit == 1) {
            $query->where('is_audit', true);
        } else {
            $query->operasional();
        }

        // Apply role-based filtering
        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        // Apply date filters
        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $tanggalAwal = \Carbon\Carbon::parse($request->tanggal_awal)->startOfDay();
            $tanggalAkhir = \Carbon\Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        } elseif ($request->tanggal_awal) {
            $tanggal = \Carbon\Carbon::parse($request->tanggal_awal);
            $query->whereDate('tanggal', $tanggal);
        }

        // Apply shift filter
        if ($request->shift_id) {
            $query->where('shift_id', $request->shift_id);
        }

        $data = $query->orderBy('tanggal', 'asc')->get();

        if ($data->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada data yang ditemukan dengan filter yang dipilih.');
        }

        // Update kode_form for matching records
        $query->update(['kode_form' => $request->kode_form]);

        // Get shift and plan info for PDF
        $shift = $request->shift_id ? DataShift::find($request->shift_id) : null;
        $plan = $user->role === 'superadmin' ? $data->first()->plan : $user->plan;

        $filters = [
            'tanggal' => $request->tanggal_awal && $request->tanggal_akhir ? \Carbon\Carbon::parse($request->tanggal_awal)->format('d/m/Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d/m/Y') : ($request->tanggal_awal ? \Carbon\Carbon::parse($request->tanggal_awal)->format('d/m/Y') : 'Semua Tanggal'),
            'shift' => $shift ? 'Shift ' . $shift->shift : 'Semua Shift',
            'kode_form' => $request->kode_form,
            'plan' => $plan ? $plan->nama_plan : '-'
        ];

        $pdf = \PDF::loadView('qc-sistem.thermometer.export_pdf', compact('data', 'filters'))
            ->setPaper('a4', 'portrait');

        $safeKodeForm = str_replace(['/', '\\', ':'], '-', $request->kode_form);
        $filename = 'Thermometer_' . $safeKodeForm . '_' . date('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export single data to PDF by UUID
     */
    public function exportPdf($uuid)
    {
        $item = Thermometer::with(['plan', 'shift', 'user', 'qcApprover', 'produksiApprover', 'spvApprover'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Cek akses data
        if (!$item->canAccess()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }

        $data = collect([$item]);
        $firstItem = $item;

        $filters = [
            'tanggal' => $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-',
            'shift' => $item->shift ? 'Shift ' . $item->shift->shift : '-',
            'kode_form' => $item->kode_form ?? 'QF 32/00',
            'plan' => $item->plan ? $item->plan->nama_plan : '-'
        ];

        $pdf = \PDF::loadView('qc-sistem.thermometer.export_pdf', compact('data', 'filters', 'firstItem'))
            ->setPaper('a4', 'portrait');

        $filename = 'thermometer-' . ($item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : 'data') . '-' . substr($uuid, 0, 8) . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Bulk Approve data thermometer per shift
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
        $areaName = $request->area_name;

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
        $query = Thermometer::whereDate('tanggal', $tanggal)
                            ->operasional()
                            ->where('shift_id', $shift_id);

        if ($areaName && $areaName !== '-') {
            $query->whereJsonContains('entries', ['area' => $areaName]);
        }

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

        // Update all records
        $query->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        // Log approval activity for each record
        foreach ($items as $item) {
            // Cek jika data ini sebelumnya belum di-approve tipe ini
            if (!$item->$approvalField) {
                ThermometerLog::create([
                    'thermometer_id' => $item->id,
                    'thermometer_uuid' => $item->uuid,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->id_role,
                    'aksi' => 'Bulk Approval ' . ucfirst($type),
                    'field_yang_diubah' => ['approval_' . $type],
                    'nilai_lama' => [false],
                    'nilai_baru' => [true],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'keterangan' => 'Data disetujui secara massal (per shift) oleh ' . ucfirst($type)
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Seluruh data pada shift ini berhasil disetujui.'
        ]);
    }

    /**
     * Approve data thermometer
     */
    public function approve(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv'
        ]);

        $thermometer = Thermometer::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        $type = $request->type;

        // Role-based access control following Timbangan pattern
        $allowedRoles = [
            'qc' => [1, 3, 5], // Role 1&5 (QC only), Role 3 (QC only)
            'produksi' => [1, 2, 5], // Role 1&5 (produksi only), Role 2 (produksi only)
            'spv' => [1, 4, 5] // Role 1&5 (SPV only), Role 4 (SPV only)
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan approval ini.'
            ], 403);
        }

        // Sequential approval validation (QC → Produksi → SPV)
        if ($type === 'produksi' && !$thermometer->approved_by_qc) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh QC terlebih dahulu.'
            ], 400);
        }

        if ($type === 'spv' && !$thermometer->approved_by_produksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'
            ], 400);
        }

        // Check if already approved
        $approvalField = "approved_by_{$type}";
        if ($thermometer->$approvalField) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah disetujui sebelumnya.'
            ], 400);
        }

        // Update approval
        $thermometer->update([
            $approvalField => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now()
        ]);

        // Log approval activity
        ThermometerLog::create([
            'thermometer_id' => $thermometer->id,
            'thermometer_uuid' => $thermometer->uuid,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->id_role,
            'aksi' => 'Approval ' . ucfirst($type),
            'field_yang_diubah' => ['approval_' . $type],
            'nilai_lama' => [false],
            'nilai_baru' => [true],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'keterangan' => 'Data disetujui oleh ' . ucfirst($type)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui.'
        ]);
    }

    protected function getImportModelClass(): string
    {
        return Thermometer::class;
    }

    protected function getTemplateFilename(): string
    {
        return 'Template_Thermometer_' . date('YmdHis') . '.xlsx';
    }

    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return redirect()->route('thermometer.index')->with('error', 'File yang diupload harus berupa Excel.');
        }

        $user = auth()->user();

        if (!$user || !$user->id_plan) {
            return redirect()->route('thermometer.index')->with('error', 'Plan user tidak ditemukan. Tidak dapat melakukan import.');
        }

        $import = new ThermometerImport($user);
        Excel::import($import, $request->file('file'));

        $insertedCount = $import->getInsertedCount();
        $importErrors = $import->getImportErrors();

        if ($insertedCount <= 0) {
            return redirect()->route('thermometer.index')
                ->with('warning', 'Tidak ada data valid untuk di-import. Pastikan format kolom dan master Shift/Thermometer sesuai.')
                ->with('import_errors', array_slice($importErrors, 0, 20));
        }

        $successMessage = 'Import data Thermometer berhasil. Total: ' . $insertedCount . ' baris.';
        if (!empty($importErrors)) {
            return redirect()->route('thermometer.index')
                ->with('success', $successMessage)
                ->with('import_errors', array_slice($importErrors, 0, 20))
                ->with('info', 'Ada beberapa baris yang gagal di-import. Silakan cek detail di bawah.');
        }

        return redirect()->route('thermometer.index')->with('success', $successMessage);
    }

    public function bulkExportExcel(Request $request, ThermometerBulkExportService $service)
    {
        return $service->handle($request, fn ($msg) => $this->buildNoDataHtmlResponse($msg));
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
            $query = Thermometer::whereIn('uuid', $uuids)
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

            Thermometer::whereIn('uuid', $uuids)
                ->where("approved_by_{$type}", false)
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                })
                ->update($updateData);

            // ALSO update corresponding audit records (is_audit=true, source_uuid matches)
            $updatedSourceUuids = $itemsToUpdate->pluck('uuid')->toArray();

            // Update audit copies with same approval data
            Thermometer::where('is_audit', true)
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
