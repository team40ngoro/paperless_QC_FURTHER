<?php

namespace App\Http\Controllers;

use App\Models\MasterProdukNonForming;
use App\Models\BahanNonForming;
use App\Models\JenisProduk;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BahanNonFormingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $query = \App\Models\BahanNonForming::with(['plan', 'produkNonForming.produk'])
            ->orderBy('id_plan')
            ->orderBy('id_no_formula_non_forming')
            ->orderBy('id');

        if ($search) {
            $query->whereHas('produkNonForming.produk', function($q) use ($search) {
                $q->where('nama_produk', 'like', "%$search%");
            });
        }

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $bahan_non_forming = $query->get();

        $grouped_data = [];
        foreach ($bahan_non_forming as $item) {
            $produkId = $item->produkNonForming->id_produk ?? 0;
            $formulaId = $item->id_no_formula_non_forming;

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

        return view('super-admin.bahan_non_forming.index', [
            'grouped_data' => $page_grouped_data,
            'rowspan_produk' => $rowspan_produk,
            'rowspan_formula' => $rowspan_formula,
            'paginated_formulas' => $paginated_formulas,
            'search' => $search,
            'perPage' => $perPage
        ]);
    }

    public function create()
    {
         $user = auth()->user();
    
    // Filter plans berdasarkan role
    if ($user->role === 'superadmin') {
        $plans = Plan::all();
               $produks = collect(); // Kosongkan, akan diisi via AJAX

    } else {
        $plans = Plan::where('id', $user->id_plan)->get();
    $produks = collect(); // Kosongkan, akan diisi via AJAX
    }
    
    return view('super-admin.bahan_non_forming.create', compact('produks', 'plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'id_produk' => 'required|exists:jenis_produk,id',
        'nomor_formula' => 'required|string|max:255',
        'id_plan' => 'required|exists:plan,id',
        'nama_rm' => 'required|array|min:1',
        'nama_rm.*' => 'required|string|max:255',
        'berat_rm' => 'required|array|min:1',
        'berat_rm.*' => 'required|string|max:50',
    ], [
        'nama_rm.required' => 'Minimal harus ada 1 Nama RM',
        'nama_rm.*.required' => 'Nama RM tidak boleh kosong',
        'berat_rm.required' => 'Minimal harus ada 1 Berat RM',
        'berat_rm.*.required' => 'Berat RM tidak boleh kosong',
    ]);

    try {
        // Simpan ke tabel no_formula_non_forming (tabel A)
        $produkNonForming = MasterProdukNonForming::create([
            'id_produk' => $validated['id_produk'],
            'nomor_formula' => $validated['nomor_formula'],
            'user_id' => Auth::id(),
            'id_plan' => $validated['id_plan'],
        ]);
        
        
        // Tampung semua data RM ke dalam array
        $dataRM = [];
        foreach ($validated['nama_rm'] as $index => $nama_rm) {
            $dataRM[] = [
                  'id_no_formula_non_forming' => $produkNonForming->id, // ID yang SAMA untuk semua RM
                'nama_rm' => $nama_rm,
                'berat_rm' => $validated['berat_rm'][$index],
                'user_id' => Auth::id(),
                'id_plan' => $validated['id_plan'],
                'uuid' => (string) Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
      
        // Simpan semua data RM sekaligus dengan insert batch
        BahanNonForming::insert($dataRM);

   

        return redirect()->route('bahan-non-forming.index')
            ->with('success', count($validated['nama_rm']) . ' Data bahan non forming berhasil disimpan!');

    } catch (\Exception $e) {
    
        
        return redirect()->route('bahan-non-forming.create')
            ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
            ->withInput();
    }

    }

  
   public function edit($uuid)
{
    try {
        $user = auth()->user();
        
        // Cari bahan non forming berdasarkan UUID
        $bahanNonForming = BahanNonForming::where('uuid', $uuid)->firstOrFail();
        
        // Load relasi yang dibutuhkan
        $bahanNonForming->load(['produkNonForming.produk', 'produkNonForming.plan']);
        
        // Cek akses
        if ($user->role !== 'superadmin' && $bahanNonForming->id_plan != $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }
        
        // Ambil data untuk dropdown
        $plans = Plan::all();
        $produks = JenisProduk::where('id_plan', $bahanNonForming->id_plan)->get();
        
        return view('super-admin.bahan_non_forming.edit', compact('bahanNonForming', 'plans', 'produks'));
        
    } catch (\Exception $e) {
        \Log::error('Error loading edit form: ' . $e->getMessage());
        
        return redirect()->route('bahan-non-forming.index')
            ->with('error', 'Data tidak ditemukan atau terjadi kesalahan.');
    }
}

public function update(Request $request, $uuid)
{
    try {
        $bahanNonForming = BahanNonForming::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        $masterProduk = $bahanNonForming->produkNonForming;
        if (!$masterProduk) {
            return redirect()->route('bahan-non-forming.edit', $uuid)
                ->with('error', 'Data master nomor formula tidak ditemukan.');
        }

        $validated = $request->validate([
            'id_produk' => 'required|exists:jenis_produk,id',
            'nomor_formula' => [
                'required',
                'string',
                'max:255',
                Rule::unique('no_formula_non_forming', 'nomor_formula')
                    ->ignore($masterProduk->id)
                    ->where(function ($q) use ($request) {
                        return $q
                            ->where('id_plan', $request->input('id_plan'))
                            ->where('id_produk', $request->input('id_produk'));
                    }),
            ],
            'id_plan' => 'required|exists:plan,id',
            'nama_rm' => 'required|string|max:255',
            'berat_rm' => 'required|string|max:50',
        ]);

        // Cek akses
        if ($user->role !== 'superadmin' && $bahanNonForming->id_plan != $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }

        // Update nomor formula di master (no_formula_non_forming)
        $masterProduk->update([
            'nomor_formula' => $validated['nomor_formula'],
            'id_plan' => $validated['id_plan'],
        ]);

        // Update data RM ini
        $bahanNonForming->update([
            'nama_rm' => $validated['nama_rm'],
            'berat_rm' => $validated['berat_rm'],
            'id_plan' => $validated['id_plan'],
        ]);

        return redirect()->route('bahan-non-forming.index')
            ->with('success', 'Data bahan non forming berhasil diperbarui!');

    } catch (\Exception $e) {
        \Log::error('Error updating bahan non forming: ' . $e->getMessage());
        
        return redirect()->route('bahan-non-forming.edit', $uuid)
            ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())
            ->withInput();
    }
}

    public function destroy($id)
    {
     try {
        $user = auth()->user();
        
        // Cari bahan non forming berdasarkan UUID (gunakan $id)
        $bahanNonForming = BahanNonForming::where('uuid', $id)->firstOrFail();
        $masterProdukId = $bahanNonForming->id_no_formula_non_forming;
        
        // Cek akses
        if ($user->role !== 'superadmin' && $bahanNonForming->id_plan != $user->id_plan) {
            abort(403, 'Unauthorized action.');
        }
        
        // Hapus hanya satu data bahan ini
        $bahanNonForming->delete();
        
        // Cek apakah masih ada bahan lain untuk master yang sama
        $remainingBahan = BahanNonForming::where('id_no_formula_non_forming', $masterProdukId)->count();
        
        if ($remainingBahan == 0) {
            // Jika tidak ada bahan lagi, hapus juga master produknya
            $masterProduk = MasterProdukNonForming::find($masterProdukId);
            if ($masterProduk) {
                $masterProduk->delete();
            }
        }
        
        return redirect()->route('bahan-non-forming.index')
            ->with('success', 'Data bahan non forming berhasil dihapus!');
            
    } catch (\Exception $e) {
        \Log::error('Error deleting bahan non forming: ' . $e->getMessage());
        
        return redirect()->route('bahan-non-forming.index')
            ->with('error', 'Gagal menghapus data. ' . $e->getMessage());
    }
    }

    public function downloadTemplate()
    {
        // Ambil semua produk non forming beserta relasi plan
        $user = auth()->user();
        $produksQuery = \App\Models\JenisProduk::with(['plan'])
            ->where('status_bahan', 'non-forming');

        if ($user && $user->role !== 'superadmin') {
            $produksQuery->where('id_plan', $user->id_plan);
        }

        $produks = $produksQuery->get();

        // Siapkan list dropdown untuk Excel (format: plan#produk)
        $dropdownList = [];
        foreach ($produks as $produk) {
            if ($produk->plan) {
                $dropdownList[] = $produk->plan->nama_plan . '#' . $produk->nama_produk;
            }
        }

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Master Produk');
        $sheet->setCellValue('B1', 'Nomor Formula');
        $sheet->setCellValue('C1', 'Nama RM');
        $sheet->setCellValue('D1', 'Berat RM');

        // Sheet referensi untuk dropdown
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('DropdownProduk');
        foreach ($dropdownList as $i => $val) {
            $refSheet->setCellValue('A' . ($i + 1), $val);
        }
        // Hide sheet referensi
        $spreadsheet->getSheetByName('DropdownProduk')->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        // Kembali ke sheet utama
        $spreadsheet->setActiveSheetIndex(0);

        // Data validation dropdown untuk kolom A (Master Nomor Formula)
        for ($row = 2; $row <= 100; $row++) {
            $validation = $sheet->getCell('A' . $row)->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1("=DropdownProduk!A:A");
        }

        // Download response
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_bahan_non_forming.xlsx';
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
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Map master produk: plan#produk => ids
        $plans = \App\Models\Plan::pluck('id', 'nama_plan')->toArray();
        $produkMap = [];
        $allProduks = \App\Models\JenisProduk::with(['plan'])
            ->where('status_bahan', 'non-forming')
            ->get();
        foreach ($allProduks as $produk) {
            if ($produk->plan) {
                $key = $produk->plan->nama_plan . '#' . $produk->nama_produk;
                $produkMap[$key] = [
                    'id_plan' => $produk->plan->id,
                    'id_produk' => $produk->id,
                ];
            }
        }

        $user_id = auth()->id();
        $insertData = [];

        foreach ($rows as $i => $row) {
            if ($i == 0) continue; // Skip header

            $master_produk = trim($row[0] ?? '');
            $nomor_formula = trim($row[1] ?? '');
            $nama_rm = trim($row[2] ?? '');
            $berat_rm = trim($row[3] ?? '');

            $produkInfo = $produkMap[$master_produk] ?? null;
            if (!$produkInfo || !$nomor_formula || !$nama_rm || $berat_rm === '') {
                continue;
            }

            $master = \App\Models\MasterProdukNonForming::firstOrCreate(
                [
                    'id_plan' => $produkInfo['id_plan'],
                    'id_produk' => $produkInfo['id_produk'],
                    'nomor_formula' => $nomor_formula,
                ],
                [
                    'user_id' => $user_id,
                ]
            );

            $insertData[] = [
                'uuid' => (string) \Str::uuid(),
                'id_no_formula_non_forming' => $master->id,
                'nama_rm' => $nama_rm,
                'berat_rm' => $berat_rm,
                'user_id' => $user_id,
                'id_plan' => $master->id_plan,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            \App\Models\BahanNonForming::insert($insertData);
        }

        return redirect()->route('bahan-non-forming.index')->with('success', 'Import data bahan non forming berhasil');
    }
}