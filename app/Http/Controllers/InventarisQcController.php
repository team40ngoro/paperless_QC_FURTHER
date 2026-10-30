<?php

namespace App\Http\Controllers;

use App\Models\DataShift;
use App\Models\InputArea;
use App\Models\InventarisQc;
use App\Models\InventarisQcDetail;
use App\Models\ListPeralatan;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InventarisQcController extends Controller
{
    use \App\Http\Controllers\Traits\HasExcelImport;

    protected function getImportModelClass(): string
    {
        return InventarisQc::class;
    }

    protected function getTemplateFilename(): string
    {
        return 'Template_InventarisQc_' . date('YmdHis') . '.xlsx';
    }

    /**
     * Standard Kondisi Options as specified in design requirements
     */
    public const KONDISI_OPTIONS = [
        '(-) Tidak Tersedia',
        '(1) Baik',
        '(2) Rusak',
        '(3) Hilang',
        '(4) Bersih',
        '(5) Kotor',
        '(6) Masih',
        '(7) Habis',
        '(8) Di dalam meja',
        '(9) Di luar meja',
        '(10) Baik, Bersih, Masih, Di dalam meja',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $plans = Plan::orderBy('nama_plan')->get();

        $selectedPlanId = $request->get('id_plan', $user->role !== 'superadmin' ? $user->id_plan : null);
        $selectedShiftId = $request->get('id_shift');
        $waktuPemeriksaan = $request->get('waktu_pemeriksaan');
        $tanggal = $request->get('tanggal');
        $search = $request->get('search');

        // Filter shifts berdasarkan plan yang aktif
        $shiftsQuery = DataShift::orderBy('shift');
        if (!empty($selectedPlanId)) {
            $shiftsQuery->where('id_plan', $selectedPlanId);
        } elseif ($user->role !== 'superadmin') {
            $shiftsQuery->where('id_plan', $user->id_plan);
        }
        $shifts = $shiftsQuery->get();

        $query = InventarisQc::with(['plan', 'shift', 'user', 'details.area'])
            ->forUser($user)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        if (!empty($selectedPlanId)) {
            $query->where('id_plan', $selectedPlanId);
        }

        if (!empty($selectedShiftId)) {
            $query->where('id_shift', $selectedShiftId);
        }

        if (!empty($waktuPemeriksaan)) {
            $query->where('waktu_pemeriksaan', $waktuPemeriksaan);
        }

        if (!empty($tanggal)) {
            $query->whereDate('tanggal', $tanggal);
        }

        $this->applyDateRangeFilter($request, $query);

        if (!empty($search)) {
            $query->whereHas('details', function ($q) use ($search) {
                $q->where('nama_alat', 'LIKE', '%' . $search . '%')
                  ->orWhere('keterangan', 'LIKE', '%' . $search . '%');
            });
        }

        $items = $query->paginate(15)->appends($request->query());

        return view('qc-sistem.inventaris_qc.index', compact(
            'items',
            'plans',
            'shifts',
            'selectedPlanId',
            'selectedShiftId',
            'waktuPemeriksaan',
            'tanggal',
            'search'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = auth()->user();

        $plans = Plan::orderBy('nama_plan')->get();
        $selectedPlanId = $request->get('plan_id', $user->role === 'superadmin' ? ($plans->first()->id ?? null) : $user->id_plan);

        // Filter shifts berdasarkan plan yang dipilih
        $shifts = DataShift::where('id_plan', $selectedPlanId)->orderBy('shift')->get();

        // Get areas for the plan
        $areasQuery = InputArea::query()->forUser($user)->orderBy('area');
        if (!empty($selectedPlanId)) {
            $areasQuery->where('id_plan', $selectedPlanId);
        }
        $areas = $areasQuery->get();

        // Get all tools for these areas from ListPeralatan master data
        $peralatanQuery = ListPeralatan::query()->forUser($user)->orderBy('nama_alat');
        if (!empty($selectedPlanId)) {
            $peralatanQuery->where('id_plan', $selectedPlanId);
        }
        $peralatanList = $peralatanQuery->get();

        $kondisiOptions = self::KONDISI_OPTIONS;

        return view('qc-sistem.inventaris_qc.create', compact(
            'plans',
            'shifts',
            'areas',
            'peralatanList',
            'selectedPlanId',
            'kondisiOptions'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'waktu_pemeriksaan' => 'required|in:AWAL SHIFT,AKHIR SHIFT',
            'details' => 'required|array|min:1',
        ], [
            'id_shift.required' => 'Shift wajib dipilih.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'waktu_pemeriksaan.required' => 'Waktu pemeriksaan (AWAL/AKHIR SHIFT) wajib dipilih.',
            'details.required' => 'Minimal 1 area pemeriksaan wajib diisi.',
        ]);

        $planId = $user->role === 'superadmin' ? ($request->id_plan ?? $user->id_plan) : $user->id_plan;

        DB::beginTransaction();
        try {
            $header = InventarisQc::create([
                'id_plan' => $planId,
                'id_shift' => $request->id_shift,
                'user_id' => $user->id,
                'tanggal' => $request->tanggal,
                'waktu_pemeriksaan' => $request->waktu_pemeriksaan,
                'catatan_umum' => $request->catatan_umum,
            ]);

            $insertedDetailsCount = 0;

            foreach ($request->details as $areaId => $tools) {
                if (!is_array($tools)) continue;

                foreach ($tools as $toolId => $data) {
                    $namaAlat = trim($data['nama_alat'] ?? '');
                    if (empty($namaAlat)) continue;

                    InventarisQcDetail::create([
                        'id_inventaris_qc' => $header->id,
                        'id_area' => $areaId,
                        'id_list_peralatan' => is_numeric($toolId) ? $toolId : null,
                        'nama_alat' => $namaAlat,
                        'jumlah' => is_numeric($data['jumlah'] ?? 1) ? max(1, (int)$data['jumlah']) : 1,
                        'kondisi' => $data['kondisi'] ?? null,
                        'keterangan' => $data['keterangan'] ?? null,
                    ]);
                    $insertedDetailsCount++;
                }
            }

            if ($insertedDetailsCount === 0) {
                DB::rollBack();
                return back()->withErrors(['details' => 'Minimal 1 peralatan diisi pada area yang dipilih.'])->withInput();
            }

            DB::commit();

            return redirect()->route('inventaris-qc.index')->with('success', 'Data Checklist Inventaris Peralatan QC berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $user = auth()->user();

        $header = InventarisQc::with(['plan', 'shift', 'user', 'details.area', 'details.peralatan'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $header->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $groupedDetails = $header->details->groupBy('id_area');

        return view('qc-sistem.inventaris_qc.show', compact('header', 'groupedDetails'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $uuid)
    {
        $user = auth()->user();

        $header = InventarisQc::with(['details'])->where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $header->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $plans = Plan::orderBy('nama_plan')->get();
        $selectedPlanId = $header->id_plan;

        // Filter shifts berdasarkan plan yang sesuai dengan data
        $shifts = DataShift::where('id_plan', $selectedPlanId)->orderBy('shift')->get();

        $areas = InputArea::query()
            ->forUser($user)
            ->where('id_plan', $selectedPlanId)
            ->orderBy('area')
            ->get();

        $peralatanList = ListPeralatan::query()
            ->forUser($user)
            ->where('id_plan', $selectedPlanId)
            ->orderBy('nama_alat')
            ->get();

        $kondisiOptions = self::KONDISI_OPTIONS;

        // Key details by area and tool ID for quick lookup in Blade
        $existingDetailsMap = [];
        foreach ($header->details as $d) {
            $key = $d->id_list_peralatan ?: $d->nama_alat;
            $existingDetailsMap[$d->id_area][$key] = $d;
        }
        $activeAreaIds = array_keys($existingDetailsMap);

        return view('qc-sistem.inventaris_qc.edit', compact(
            'header',
            'plans',
            'shifts',
            'areas',
            'peralatanList',
            'existingDetailsMap',
            'activeAreaIds',
            'kondisiOptions'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $user = auth()->user();

        $header = InventarisQc::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $header->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $request->validate([
            'id_shift' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date',
            'waktu_pemeriksaan' => 'required|in:AWAL SHIFT,AKHIR SHIFT',
            'details' => 'required|array|min:1',
        ], [
            'id_shift.required' => 'Shift wajib dipilih.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'waktu_pemeriksaan.required' => 'Waktu pemeriksaan (AWAL/AKHIR SHIFT) wajib dipilih.',
            'details.required' => 'Minimal 1 area pemeriksaan wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            $header->update([
                'id_shift' => $request->id_shift,
                'tanggal' => $request->tanggal,
                'waktu_pemeriksaan' => $request->waktu_pemeriksaan,
                'catatan_umum' => $request->catatan_umum,
            ]);

            // Delete old details and recreate
            InventarisQcDetail::where('id_inventaris_qc', $header->id)->delete();

            $insertedDetailsCount = 0;

            foreach ($request->details as $areaId => $tools) {
                if (!is_array($tools)) continue;

                foreach ($tools as $toolId => $data) {
                    $namaAlat = trim($data['nama_alat'] ?? '');
                    if (empty($namaAlat)) continue;

                    InventarisQcDetail::create([
                        'id_inventaris_qc' => $header->id,
                        'id_area' => $areaId,
                        'id_list_peralatan' => is_numeric($toolId) ? $toolId : null,
                        'nama_alat' => $namaAlat,
                        'jumlah' => is_numeric($data['jumlah'] ?? 1) ? max(1, (int)$data['jumlah']) : 1,
                        'kondisi' => $data['kondisi'] ?? null,
                        'keterangan' => $data['keterangan'] ?? null,
                    ]);
                    $insertedDetailsCount++;
                }
            }

            if ($insertedDetailsCount === 0) {
                DB::rollBack();
                return back()->withErrors(['details' => 'Minimal 1 peralatan diisi pada area yang dipilih.'])->withInput();
            }

            DB::commit();

            return redirect()->route('inventaris-qc.index')->with('success', 'Data Checklist Inventaris Peralatan QC berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $user = auth()->user();

        $header = InventarisQc::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $header->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $header->delete();

        return redirect()->route('inventaris-qc.index')->with('success', 'Data Checklist Inventaris Peralatan QC berhasil dihapus.');
    }

    /**
     * Export inspection checklist data to Excel
     */
    public function exportExcel(Request $request)
    {
        $user = auth()->user();

        $query = InventarisQc::with(['plan', 'shift', 'user', 'details.area'])
            ->forUser($user)
            ->orderBy('tanggal', 'desc');

        if ($request->filled('id_plan')) {
            $query->where('id_plan', $request->id_plan);
        }

        if ($request->filled('id_shift')) {
            $query->where('id_shift', $request->id_shift);
        }

        if ($request->filled('waktu_pemeriksaan')) {
            $query->where('waktu_pemeriksaan', $request->waktu_pemeriksaan);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $headers = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventaris QC');

        // Styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D6EFD']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];

        $sheet->setCellValue('A1', 'Tanggal');
        $sheet->setCellValue('B1', 'Shift');
        $sheet->setCellValue('C1', 'Waktu');
        $sheet->setCellValue('D1', 'Area');
        $sheet->setCellValue('E1', 'Nama Alat');
        $sheet->setCellValue('F1', 'Jumlah');
        $sheet->setCellValue('G1', 'Kondisi');
        $sheet->setCellValue('H1', 'Keterangan');
        $sheet->setCellValue('I1', 'Petugas QC');
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        $row = 2;
        foreach ($headers as $h) {
            foreach ($h->details as $d) {
                $sheet->setCellValue('A' . $row, $h->tanggal);
                $sheet->setCellValue('B' . $row, $h->shift->shift ?? '-');
                $sheet->setCellValue('C' . $row, $h->waktu_pemeriksaan);
                $sheet->setCellValue('D' . $row, $d->area->area ?? '-');
                $sheet->setCellValue('E' . $row, $d->nama_alat);
                $sheet->setCellValue('F' . $row, $d->jumlah);
                $sheet->setCellValue('G' . $row, $d->kondisi ?? '-');
                $sheet->setCellValue('H' . $row, $d->keterangan ?? '-');
                $sheet->setCellValue('I' . $row, $h->user->name ?? '-');
                $row++;
            }
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'export_inventaris_qc_' . date('Ymd_His') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    public function downloadTemplate()
    {
        $user = auth()->user();
        $planId = $user->role === 'superadmin' ? (Plan::first()->id ?? null) : $user->id_plan;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Inventaris QC');

        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D6EFD']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];

        // Headers
        $headers = ['Shift', 'Tanggal', 'Waktu Pemeriksaan', 'Catatan Umum'];
        foreach ($headers as $col => $header) {
            $sheet->setCellValue(chr(65 + $col) . '1', $header);
        }
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // Add sample row
        $sheet->setCellValue('A2', 'Shift 1');
        $sheet->setCellValue('B2', date('Y-m-d'));
        $sheet->setCellValue('C2', '06:00');
        $sheet->setCellValue('D2', '-');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(30);

        $writer = new Xlsx($spreadsheet);
        $filename = $this->getTemplateFilename();
        $temp_file = tempnam(sys_get_temp_dir(), 'template');
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    public function importExcel(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $file = $request->file('file');
            $user = auth()->user();

            // Import menggunakan ExcelImportService
            $importService = app(\App\Services\Excel\ExcelImportService::class);
            $result = $importService->import($file, $this->getImportModelClass());

            if ($result['success']) {
                $message = "Berhasil import {$result['imported_count']} data.";
                if (!empty($result['errors'])) {
                    $message .= " Ada " . count($result['errors']) . " baris gagal.";
                    return redirect()->route('inventaris-qc.index')
                        ->with('success', $message)
                        ->with('import_errors', array_slice($result['errors'], 0, 20));
                }
                return redirect()->route('inventaris-qc.index')->with('success', $message);
            }

            return redirect()->route('inventaris-qc.index')
                ->with('error', $result['error_message'] ?? 'Import gagal.');

        } catch (\Exception $e) {
            return redirect()->route('inventaris-qc.index')
                ->with('error', 'Error saat import: ' . $e->getMessage());
        }
    }
}
