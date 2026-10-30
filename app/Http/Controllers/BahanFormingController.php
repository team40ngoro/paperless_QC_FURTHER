<?php

namespace App\Http\Controllers;

use App\Models\BahanForming;
use App\Models\Plan;
use App\Models\JenisProduk;
use App\Models\NomorFormula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
use App\Models\JenisEmulsi;

class BahanFormingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $query = \App\Models\BahanForming::with(['plan', 'produk', 'formula'])
            ->whereHas('produk', function($q) {
                $q->where('status_bahan', 'forming');
            })
            ->orderBy('id_produk')
            ->orderBy('id_formula')
            ->orderBy('id');

        if ($search) {
            $query->whereHas('produk', function($q) use ($search) {
                $q->where('nama_produk', 'like', "%$search%");
            });
        }

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $bahan_forming = $query->get();

        $grouped_data = [];
        foreach ($bahan_forming as $item) {
            $produkId = $item->id_produk;
            $formulaId = $item->id_formula;

            if (!isset($grouped_data[$produkId])) {
                $grouped_data[$produkId] = [];
            }
            if (!isset($grouped_data[$produkId][$formulaId])) {
                $grouped_data[$produkId][$formulaId] = [];
            }

            $grouped_data[$produkId][$formulaId][] = $item;
        }

        // Flatten to formula level for pagination
        $flat_formulas = [];
        foreach ($grouped_data as $produkId => $formulaGroups) {
            foreach ($formulaGroups as $formulaId => $items) {
                $flat_formulas[] = [
                    'produk_id' => $produkId,
                    'formula_id' => $formulaId,
                    'items' => $items
                ];
            }
        }

        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $currentItems = array_slice($flat_formulas, ($currentPage - 1) * $perPage, $perPage);
        $paginated_formulas = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            count($flat_formulas),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Rebuild grouped data for current page and compute rowspans
        $page_grouped_data = [];
        $rowspan_produk = [];
        $rowspan_formula = [];

        foreach ($currentItems as $formulaGroup) {
            $pId = $formulaGroup['produk_id'];
            $fId = $formulaGroup['formula_id'];
            $items = $formulaGroup['items'];

            if (!isset($page_grouped_data[$pId])) {
                $page_grouped_data[$pId] = [];
                $rowspan_produk[$pId] = 0;
            }
            $page_grouped_data[$pId][$fId] = $items;
            $rowspan_formula[$pId . '_' . $fId] = count($items);
            $rowspan_produk[$pId] += count($items);
        }

        if ($user->role === 'superadmin') {
            $produks = \App\Models\JenisProduk::where('status_bahan', 'forming')->orderBy('nama_produk')->get();
        } else {
            $produks = \App\Models\JenisProduk::where('id_plan', $user->id_plan)->where('status_bahan', 'forming')->orderBy('nama_produk')->get();
        }

        return view('super-admin.bahan_forming.index', [
            'grouped_data' => $page_grouped_data,
            'rowspan_produk' => $rowspan_produk,
            'rowspan_formula' => $rowspan_formula,
            'paginated_formulas' => $paginated_formulas,
            'search' => $search,
            'perPage' => $perPage,
            'produks' => $produks
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->role === 'superadmin') {
            $plans = \App\Models\Plan::all();
            $produks = \App\Models\JenisProduk::where('status_bahan', 'forming')->get();
            $formulas = \App\Models\NomorFormula::all();
            $emulsis = \App\Models\JenisEmulsi::all(); // TAMBAHKAN INI

        } else {
            $plans = \App\Models\Plan::where('id', $user->id_plan)->get();
            $produks = \App\Models\JenisProduk::where('id_plan', $user->id_plan)->where('status_bahan', 'forming')->get();
            $formulas = \App\Models\NomorFormula::where('id_plan', $user->id_plan)->get();
            $emulsis = \App\Models\JenisEmulsi::where('id_plan', $user->id_plan)->get(); // TAMBAHKAN INI

        }
        return view('super-admin.bahan_forming.create', compact('plans', 'produks', 'formulas','emulsis'));
    }

    public function store(Request $request)
    {
        try {
            // Validasi input array
            $request->validate([
                'id_plan' => 'required|exists:plan,id',
                'id_produk' => 'required|exists:jenis_produk,id',
                'id_formula' => 'required|exists:nomor_formula,id',
                'nama_rm' => 'required|array|min:1',
                'nama_rm.*' => 'required|string|max:255',
                'berat_rm' => 'required|array|min:1',
                'berat_rm.*' => 'required|string|max:255',
            ], [
                'nama_rm.required' => 'Minimal harus ada 1 nama RM.',
                'nama_rm.array' => 'Format nama RM tidak valid.',
                'nama_rm.min' => 'Minimal harus ada 1 nama RM.',
                'nama_rm.*.required' => 'Nama RM tidak boleh kosong.',
                'nama_rm.*.string' => 'Nama RM harus berupa teks.',
                'nama_rm.*.max' => 'Nama RM maksimal 255 karakter.',
                'berat_rm.required' => 'Minimal harus ada 1 berat RM.',
                'berat_rm.array' => 'Format berat RM tidak valid.',
                'berat_rm.min' => 'Minimal harus ada 1 berat RM.',
                'berat_rm.*.required' => 'Berat RM tidak boleh kosong.',
                'berat_rm.*.string' => 'Berat RM harus berupa teks.',
                'berat_rm.*.max' => 'Berat RM maksimal 255 karakter.',
            ]);

            $user_id = auth()->id();
            $created_count = 0;
            $failed_count = 0;

            // Pastikan jumlah nama_rm dan berat_rm sama
            if (count($request->nama_rm) !== count($request->berat_rm)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Jumlah nama RM dan berat RM harus sama.');
            }

            // Loop untuk setiap RM
            foreach ($request->nama_rm as $index => $nama) {
                $trimmed_nama = trim($nama);
                $berat = $request->berat_rm[$index] ?? 0;
                
                if (!empty($trimmed_nama) && $berat > 0) {
                    $data = [
                        'id_plan' => $request->id_plan,
                        'id_produk' => $request->id_produk,
                        'id_formula' => $request->id_formula,
                        'nama_rm' => $trimmed_nama,
                        'berat_rm' => $berat,
                        'user_id' => $user_id,
                    ];
                    
                    BahanForming::create($data);
                    $created_count++;
                } else {
                    $failed_count++;
                }
            }

            // Prepare success message
            $message = "Berhasil menambahkan {$created_count} data Bahan Forming";
            
            if ($failed_count > 0) {
                $message .= ", {$failed_count} data gagal (nama kosong atau berat 0)";
            }

            return redirect()->route('bahan-forming.index')
                ->with('success', $message);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            \Log::error('BahanForming Store Error:', [
                'message' => $e->getMessage(),
                'input' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($uuid)
    {
        $bahan = BahanForming::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        if ($user->role === 'superadmin') {
            $plans = Plan::all();
            $produks = JenisProduk::all();
            $formulas = NomorFormula::all();
            // $emulsis = JenisEmulsi::all(); // TAMBAHKAN INI
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $formulas = NomorFormula::where('id_plan', $user->id_plan)->get();
            $emulsis = JenisEmulsi::where('id_plan', $user->id_plan)->get(); // TAMBAHKAN INI
        }
        return view('super-admin.bahan_forming.edit', compact('bahan', 'plans', 'produks', 'formulas'));
    }

    public function update(Request $request, $uuid)
    {
        $bahan = BahanForming::where('uuid', $uuid)->firstOrFail();

        $data = $request->validate([
            'id_plan'    => 'required|exists:plan,id',
            'id_produk'  => 'required|exists:jenis_produk,id',
            'id_formula' => 'required|exists:nomor_formula,id',
            'nama_rm'    => 'required|string|max:255',
            'berat_rm'   => 'required|string|max:255',
        ]);

        // Gunakan fill + save agar Eloquent selalu menjalankan query UPDATE
        $bahan->fill($data);
        $bahan->save();

        return redirect()->route('bahan-forming.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $bahan = BahanForming::where('uuid', $uuid)->firstOrFail();
        $bahan->delete();
        return redirect()->route('bahan-forming.index')->with('success', 'Data berhasil dihapus');
    }
    public function ajaxByFormula($id)
    {
        $bahan = BahanForming::where('id_formula', $id)->get();
        return response()->json($bahan);
    }
    
    public function downloadTemplate()
    {
        // Ambil semua master nomor formula beserta relasi plan dan produk
        $formulas = \App\Models\NomorFormula::with(['plan', 'produk'])->get();

        // Siapkan list dropdown untuk Excel (format: plan#namaproduk#nomorformula)
        $dropdownList = [];
        foreach ($formulas as $formula) {
            if ($formula->plan && $formula->produk) {
                $dropdownList[] = $formula->plan->nama_plan . '#' . $formula->produk->nama_produk . '#' . $formula->nomor_formula;
            }
        }

        // Buat spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Master Nomor Formula');
        $sheet->setCellValue('B1', 'Nama RM');
        $sheet->setCellValue('C1', 'Berat RM');

        // Sheet referensi untuk dropdown
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('DropdownFormula');
        foreach ($dropdownList as $i => $val) {
            $refSheet->setCellValue('A' . ($i + 1), $val);
        }
        // Hide sheet referensi
        $spreadsheet->getSheetByName('DropdownFormula')->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        // Kembali ke sheet utama
        $spreadsheet->setActiveSheetIndex(0);

        // Buat named range untuk dropdown (A1:A{jumlah data})
        $lastRow = count($dropdownList);
        if ($lastRow > 0) {
            $spreadsheet->addNamedRange(
                new \PhpOffice\PhpSpreadsheet\NamedRange(
                    'listFormula',
                    $refSheet,
                    'A1:A' . $lastRow
                )
            );
        
        }

        // Data validation dropdown untuk kolom A (Master Nomor Formula)
        for ($row = 2; $row <= 100; $row++) {
            $validation = $sheet->getCell('A' . $row)->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            // Sumber dropdown langsung ke sheet 'DropdownFormula' kolom A seluruh data
            $validation->setFormula1("=DropdownFormula!A:A");

        }

        // Download response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template_bahan_forming.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

     public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');

        try {
            // Gunakan ReadDataOnly: kita hanya butuh nilai sel, bukan style/
            // data-validation/merge/comment. Ini juga menghindari error
            // "Invalid cell coordinate ..." yang muncul kalau file sudah
            // diedit ulang di Excel dan baris ditambah/dihapus/diurutkan,
            // karena Excel kadang menulis ulang sqref data validation
            // menjadi daftar sel terpisah koma/titik-koma yang tidak
            // dikenali parser data-validation PhpSpreadsheet.
            $reader = IOFactory::createReaderForFile($file->getPathname());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getPathname());
        } catch (\Exception $e) {
            \Log::error('BahanForming Import Error (gagal membaca file):', [
                'message' => $e->getMessage(),
            ]);
            return redirect()->route('bahan-forming.index')
                ->with('error', 'File Excel tidak dapat dibaca. Pastikan file berasal dari Export/Template dan tidak rusak. (' . $e->getMessage() . ')');
        }

        // Ambil sheet data secara eksplisit (bukan getActiveSheet()), karena
        // saat file diedit/disimpan ulang di Excel, sheet yang "aktif" bisa
        // berubah menjadi sheet lain (misalnya sheet tersembunyi DropdownFormula).
        // Dengan mengambil sheet berdasarkan nama/index tetap, proses import
        // tetap aman meskipun file berasal dari hasil Export Excel.
        $sheet = $spreadsheet->getSheetByName('Data Bahan Forming')
            ?? $spreadsheet->getSheetByName('Template Bahan Forming')
            ?? $spreadsheet->getSheet(0);

        // Jaga-jaga: jika sheet index 0 justru sheet referensi/tersembunyi
        // (misalnya karena file disusun ulang manual), cari sheet pertama
        // yang tidak berjudul DropdownFormula.
        if (strcasecmp($sheet->getTitle(), 'DropdownFormula') === 0) {
            foreach ($spreadsheet->getAllSheets() as $candidate) {
                if (strcasecmp($candidate->getTitle(), 'DropdownFormula') !== 0) {
                    $sheet = $candidate;
                    break;
                }
            }
        }

        $rows = $sheet->toArray();

        // Ambil referensi plan, produk, formula
        $plans = \App\Models\Plan::pluck('id', 'nama_plan')->toArray();
        $produks = \App\Models\JenisProduk::pluck('id', 'nama_produk')->toArray();
        // Ambil formula dengan relasi plan dan produk
        $formulaMap = [];
        $allFormulas = \App\Models\NomorFormula::with(['plan', 'produk'])->get();
        foreach ($allFormulas as $formula) {
            if ($formula->plan && $formula->produk) {
                $key = $formula->plan->nama_plan . '#' . $formula->produk->nama_produk . '#' . $formula->nomor_formula;
                $formulaMap[$key] = $formula->id;
            }
        }

        $user_id = auth()->id();
        $insertData = [];

        foreach ($rows as $i => $row) {
            if ($i == 0) continue; // Skip header

            $master_formula = trim($row[0] ?? '');
            $nama_rm = trim($row[1] ?? '');
            $berat_rm = trim($row[2] ?? '');

            // Parsing master nomor formula: plan#namaproduk#nomorformula
            $parts = explode('#', $master_formula);
            if (count($parts) !== 3) continue;

            $plan_name = trim($parts[0]);
            $produk_name = trim($parts[1]);
            $nomor_formula = trim($parts[2]);

            $id_plan = $plans[$plan_name] ?? null;
            $id_produk = $produks[$produk_name] ?? null;
            $formulaKey = $plan_name . '#' . $produk_name . '#' . $nomor_formula;
            $id_formula = $formulaMap[$formulaKey] ?? null;

            if ($id_plan && $id_produk && $id_formula && $nama_rm && $berat_rm) {
                $insertData[] = [
                    'uuid' => \Str::uuid(),
                    'id_plan' => $id_plan,
                    'id_produk' => $id_produk,
                    'id_formula' => $id_formula,
                    'nama_rm' => $nama_rm,
                    'berat_rm' => $berat_rm,
                    'user_id' => $user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($insertData)) {
            \App\Models\BahanForming::insert($insertData);
        }

        return redirect()->route('bahan-forming.index')->with('success', 'Import data bahan forming berhasil');
    }

   public function exportExcel(Request $request)
    {
        $user = auth()->user();
        $id_produk = $request->get('id_produk');

        // Tarik SEMUA master Nomor Formula milik produk yang dipilih di modal
        // "Tarik Data" (bukan hanya formula yang kebetulan sudah punya data RM
        // di tabel bahan_forming). Dengan begini, formula yang belum diisi RM
        // sama sekali pun tetap ikut tertarik ke file export.
        $formulasQuery = \App\Models\NomorFormula::with(['plan', 'produk'])
            ->whereHas('produk', function ($q) {
                $q->where('status_bahan', 'forming');
            });

        if ($id_produk && $id_produk !== 'all') {
            $formulasQuery->where('id_produk', $id_produk);
        }

        if ($user->role !== 'superadmin') {
            $formulasQuery->where('id_plan', $user->id_plan);
        }

        $formulasForExport = $formulasQuery->orderBy('id_produk')
            ->orderBy('id')
            ->get();

        // Ambil semua data Bahan Forming yang sudah ada untuk formula-formula tsb,
        // lalu kelompokkan per id_formula agar mudah dipasangkan.
        $formulaIds = $formulasForExport->pluck('id');
        $bahanQuery = \App\Models\BahanForming::with(['plan', 'produk', 'formula'])
            ->whereIn('id_formula', $formulaIds);

        if ($user->role !== 'superadmin') {
            $bahanQuery->where('id_plan', $user->id_plan);
        }

        $itemsByFormula = $bahanQuery->orderBy('id')->get()->groupBy('id_formula');

        // Ambil seluruh master formula (lintas produk, sesuai scope plan) untuk
        // sumber dropdown, supaya user tetap bisa menambah baris formula lain.
        $dropdownFormulasQuery = \App\Models\NomorFormula::with(['plan', 'produk']);
        if ($user->role !== 'superadmin') {
            $dropdownFormulasQuery->where('id_plan', $user->id_plan);
        }
        $dropdownFormulas = $dropdownFormulasQuery->get();

        // Siapkan list dropdown untuk Excel (format: plan#namaproduk#nomorformula)
        $dropdownList = [];
        foreach ($dropdownFormulas as $formula) {
            if ($formula->plan && $formula->produk) {
                $dropdownList[] = $formula->plan->nama_plan . '#' . $formula->produk->nama_produk . '#' . $formula->nomor_formula;
            }
        }

        // Buat spreadsheet (strukturnya dibuat persis seperti downloadTemplate())
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Bahan Forming');

        // Header
        $sheet->setCellValue('A1', 'Master Nomor Formula');
        $sheet->setCellValue('B1', 'Nama RM');
        $sheet->setCellValue('C1', 'Berat RM');

        // Isi Data: loop per master formula produk terpilih, bukan per baris bahan_forming,
        // supaya semua formula produk tersebut ikut tertarik (termasuk yang belum ada RM-nya).
        $row = 2;
        foreach ($formulasForExport as $formula) {
            if (!$formula->plan || !$formula->produk) continue;

            $masterFormula = $formula->plan->nama_plan . '#' . $formula->produk->nama_produk . '#' . $formula->nomor_formula;
            $existingItems = $itemsByFormula->get($formula->id);

            if ($existingItems && $existingItems->count() > 0) {
                foreach ($existingItems as $item) {
                    $sheet->setCellValue('A' . $row, $masterFormula);
                    $sheet->setCellValue('B' . $row, $item->nama_rm);
                    $sheet->setCellValue('C' . $row, $item->berat_rm);
                    $row++;
                }
            } else {
                // Formula ini belum punya data RM sama sekali -> tetap ditarik
                // sebagai satu baris kosong, siap diisi lalu di-import ulang.
                $sheet->setCellValue('A' . $row, $masterFormula);
                $sheet->setCellValue('B' . $row, '');
                $sheet->setCellValue('C' . $row, '');
                $row++;
            }
        }
        $lastDataRow = $row - 1;

        // Sheet referensi untuk dropdown (sama seperti di downloadTemplate)
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('DropdownFormula');
        foreach ($dropdownList as $i => $val) {
            $refSheet->setCellValue('A' . ($i + 1), $val);
        }

        // Buat named range untuk dropdown (A1:A{jumlah data}) — sama seperti downloadTemplate
        $lastRefRow = count($dropdownList);
        if ($lastRefRow > 0) {
            $spreadsheet->addNamedRange(
                new \PhpOffice\PhpSpreadsheet\NamedRange(
                    'listFormula',
                    $refSheet,
                    'A1:A' . $lastRefRow
                )
            );
        }

        // Kunci sheet referensi agar tidak sengaja diedit/dihapus isinya
        $refSheet->getProtection()->setSheet(true);

        // Hide sheet referensi
        $refSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        // Kunci struktur workbook (tidak bisa unhide/hapus/pindah sheet dari Excel)
        // agar sheet DropdownFormula tetap ada & tersembunyi ketika file ini
        // nantinya di-import ulang lewat fitur Import Excel.
        $spreadsheet->getSecurity()->setLockStructure(true);

        // Kembali ke sheet utama
        $spreadsheet->setActiveSheetIndex(0);

        // Data validation dropdown untuk kolom A (Master Nomor Formula)
        // Minimal 100 baris (sama seperti template import) ditambah baris data aktual,
        // supaya user masih bisa menambah baris baru pada hasil export ini.
        $maxRow = max($lastDataRow, 100);
        for ($r = 2; $r <= $maxRow; $r++) {
            $validation = $sheet->getCell('A' . $r)->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            // Sumber dropdown langsung ke sheet 'DropdownFormula' kolom A seluruh data
            $validation->setFormula1("=DropdownFormula!A:A");
        }

        // Download response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'export_bahan_forming_' . date('Ymd_His') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
