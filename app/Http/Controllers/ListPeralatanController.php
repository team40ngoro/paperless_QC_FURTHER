<?php

namespace App\Http\Controllers;

use App\Models\InputArea;
use App\Models\ListPeralatan;
use App\Models\Plan;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ListPeralatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $plans = Plan::orderBy('nama_plan')->get();

        $areasQuery = InputArea::query()->forUser($user)->orderBy('area');
        if ($request->filled('id_plan')) {
            $areasQuery->where('id_plan', $request->id_plan);
        }
        $areas = $areasQuery->get();

        $selectedPlanId = $request->get('id_plan');
        $selectedAreaId = $request->get('id_area');
        $search = $request->get('search');

        $query = ListPeralatan::with(['plan', 'area', 'user'])
            ->forUser($user)
            ->orderBy('id_plan')
            ->orderBy('id_area')
            ->orderBy('nama_alat');

        if (!empty($selectedPlanId)) {
            $query->where('id_plan', $selectedPlanId);
        }

        if (!empty($selectedAreaId)) {
            $query->where('id_area', $selectedAreaId);
        }

        if (!empty($search)) {
            $query->where('nama_alat', 'LIKE', '%' . $search . '%');
        }

        $paginatedItems = $query->paginate(15);
        $groupedData = $paginatedItems->groupBy('id_area');

        return view('super-admin.list_peralatan.index', compact(
            'paginatedItems',
            'groupedData',
            'plans',
            'areas',
            'selectedPlanId',
            'selectedAreaId',
            'search'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        $plans = Plan::orderBy('nama_plan')->get();

        $areas = InputArea::query()
            ->forUser($user)
            ->orderBy('area')
            ->get();

        return view('super-admin.list_peralatan.create', compact('plans', 'areas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'id_area' => 'required|exists:input_area,id',
            'list_alat' => 'required|array|min:1',
            'list_alat.*' => 'required|string|max:255',
            'jumlah_alat' => 'nullable|array',
            'jumlah_alat.*' => 'nullable|numeric|min:1',
        ], [
            'id_area.required' => 'Area wajib dipilih.',
            'id_area.exists' => 'Area yang dipilih tidak valid.',
            'list_alat.required' => 'Minimal 1 nama peralatan/alat wajib diisi.',
            'list_alat.*.required' => 'Nama peralatan/alat tidak boleh kosong.',
        ]);

        $area = InputArea::findOrFail($request->id_area);

        if ($user->role !== 'superadmin' && (int) $area->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $createdCount = 0;
        foreach ($request->list_alat as $index => $namaAlat) {
            $trimmedName = is_string($namaAlat) ? trim($namaAlat) : '';
            if ($trimmedName === '') continue;

            $jumlah = isset($request->jumlah_alat[$index]) && is_numeric($request->jumlah_alat[$index])
                ? max(1, (int) $request->jumlah_alat[$index])
                : 1;

            ListPeralatan::create([
                'id_plan' => $area->id_plan,
                'id_area' => $area->id,
                'user_id' => $user->id,
                'nama_alat' => $trimmedName,
                'jumlah_alat' => $jumlah,
            ]);
            $createdCount++;
        }

        if ($createdCount === 0) {
            return back()->withErrors(['list_alat' => 'Nama peralatan/alat wajib diisi minimal 1.'])->withInput();
        }

        return redirect()->route('list-peralatan.index')->with('success', 'Data list peralatan berhasil ditambahkan (' . $createdCount . ' item).');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $user = auth()->user();

        $item = ListPeralatan::with(['plan', 'area'])->where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $plans = Plan::orderBy('nama_plan')->get();

        $areas = InputArea::query()
            ->forUser($user)
            ->orderBy('area')
            ->get();

        return view('super-admin.list_peralatan.edit', compact('item', 'plans', 'areas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $user = auth()->user();

        $item = ListPeralatan::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $request->validate([
            'id_area' => 'required|exists:input_area,id',
            'list_alat' => 'required|string|max:255',
            'jumlah_alat' => 'required|numeric|min:1',
        ], [
            'id_area.required' => 'Area wajib dipilih.',
            'list_alat.required' => 'Nama peralatan/alat wajib diisi.',
            'jumlah_alat.required' => 'Jumlah alat wajib diisi.',
            'jumlah_alat.min' => 'Jumlah alat minimal 1.',
        ]);

        $area = InputArea::findOrFail($request->id_area);

        if ($user->role !== 'superadmin' && (int) $area->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $item->update([
            'id_plan' => $area->id_plan,
            'id_area' => $area->id,
            'nama_alat' => trim($request->list_alat),
            'jumlah_alat' => max(1, (int) $request->jumlah_alat),
        ]);

        return redirect()->route('list-peralatan.index')->with('success', 'Data list peralatan berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $user = auth()->user();

        $item = ListPeralatan::where('uuid', $uuid)->firstOrFail();

        if ($user->role !== 'superadmin' && (int) $item->id_plan !== (int) $user->id_plan) {
            abort(403);
        }

        $item->delete();

        return redirect()->route('list-peralatan.index')->with('success', 'Data list peralatan berhasil dihapus.');
    }

    /**
     * Get areas by plan ID for AJAX cascading dropdowns
     */
    public function getAreaByPlan($planId)
    {
        $user = auth()->user();

        $query = InputArea::query()->where('id_plan', $planId);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $areas = $query->orderBy('area')->get(['id', 'area']);

        return response()->json($areas);
    }

    /**
     * Download Excel template with pre-filled areas
     */
    public function downloadTemplate()
    {
        $user = auth()->user();

        $areas = InputArea::with('plan')
            ->forUser($user)
            ->orderBy('id_plan')
            ->orderBy('area')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template List Peralatan');

        // Header Styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D6EFD'] // Bootstrap primary blue
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
        $sheet->setCellValue('A1', 'Nama Plan');
        $sheet->setCellValue('B1', 'Nama Area');
        $sheet->setCellValue('C1', 'Nama Peralatan / Alat');
        $sheet->setCellValue('D1', 'Jumlah Alat');
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        $row = 2;
        foreach ($areas as $area) {
            $planName = $area->plan->nama_plan ?? '-';
            $areaName = $area->area;

            // Pre-fill 3 empty rows per area so the user can easily type equipment names
            for ($i = 0; $i < 3; $i++) {
                $sheet->setCellValue('A' . $row, $planName);
                $sheet->setCellValue('B' . $row, $areaName);
                $sheet->setCellValue('C' . $row, ''); // User types equipment name here
                $sheet->setCellValue('D' . $row, 1);  // Default jumlah_alat
                $row++;
            }
        }

        // Auto size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_list_peralatan.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Import Excel data for list peralatan
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes' => 'Format file harus berupa .xlsx atau .xls.'
        ]);

        $file = $request->file('file');
        $user = auth()->user();

        try {
            $reader = IOFactory::createReaderForFile($file->getPathname());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getPathname());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        $sheet = $spreadsheet->getSheet(0);
        $rows = $sheet->toArray();

        // Fetch all accessible areas for matching
        $allAreas = InputArea::with('plan')->forUser($user)->get();

        $importedCount = 0;
        $skippedCount = 0;

        foreach ($rows as $i => $row) {
            if ($i === 0) continue; // Skip header row

            $planName = trim($row[0] ?? '');
            $areaName = trim($row[1] ?? '');
            $namaAlat = trim($row[2] ?? '');
            $jumlahRaw = trim($row[3] ?? '');

            if (empty($areaName) || empty($namaAlat)) {
                $skippedCount++;
                continue;
            }

            $jumlahAlat = is_numeric($jumlahRaw) && (int) $jumlahRaw > 0 ? (int) $jumlahRaw : 1;

            // Find matching area by name (and plan name if available)
            $matchedArea = $allAreas->first(function ($areaObj) use ($areaName, $planName) {
                $areaMatch = strcasecmp(trim($areaObj->area), $areaName) === 0;
                if (!$areaMatch) return false;

                if (!empty($planName) && $areaObj->plan) {
                    return strcasecmp(trim($areaObj->plan->nama_plan), $planName) === 0;
                }

                return true;
            });

            if (!$matchedArea) {
                // If area not found directly by plan name, try matching area name alone
                $matchedArea = $allAreas->first(function ($areaObj) use ($areaName) {
                    return strcasecmp(trim($areaObj->area), $areaName) === 0;
                });
            }

            if ($matchedArea) {
                ListPeralatan::create([
                    'id_plan' => $matchedArea->id_plan,
                    'id_area' => $matchedArea->id,
                    'user_id' => $user->id,
                    'nama_alat' => $namaAlat,
                    'jumlah_alat' => $jumlahAlat,
                ]);
                $importedCount++;
            } else {
                $skippedCount++;
            }
        }

        if ($importedCount > 0) {
            return redirect()->route('list-peralatan.index')
                ->with('success', "Berhasil mengimpor {$importedCount} data list peralatan.");
        }

        return redirect()->route('list-peralatan.index')
            ->with('error', "Tidak ada data yang berhasil diimpor. Pastikan nama area pada file Excel sesuai dengan master area yang terdaftar.");
    }

    /**
     * Export existing list peralatan data to Excel
     */
    public function exportExcel(Request $request)
    {
        $user = auth()->user();

        $query = ListPeralatan::with(['plan', 'area', 'user'])
            ->forUser($user)
            ->orderBy('id_plan')
            ->orderBy('id_area')
            ->orderBy('nama_alat');

        if ($request->filled('id_plan')) {
            $query->where('id_plan', $request->id_plan);
        }

        if ($request->filled('id_area')) {
            $query->where('id_area', $request->id_area);
        }

        $items = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data List Peralatan');

        // Header Styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '198754'] // Bootstrap success green
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
        $sheet->setCellValue('A1', 'Nama Plan');
        $sheet->setCellValue('B1', 'Nama Area');
        $sheet->setCellValue('C1', 'Nama Peralatan / Alat');
        $sheet->setCellValue('D1', 'Jumlah Alat');
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        $row = 2;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $item->plan->nama_plan ?? '-');
            $sheet->setCellValue('B' . $row, $item->area->area ?? '-');
            $sheet->setCellValue('C' . $row, $item->nama_alat);
            $sheet->setCellValue('D' . $row, $item->jumlah_alat ?? 1);
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'export_list_peralatan_' . date('Ymd_His') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
