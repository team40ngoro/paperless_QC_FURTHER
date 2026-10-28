<?php

namespace App\Http\Controllers;

use App\Models\JenisProduk;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class JenisProdukController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = \App\Models\JenisProduk::query();

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        // $jenis_produk = $query->get();
        $jenis_produk = $query->get()->map(function ($item) {
            if (strtolower($item->status_bahan) === 'forming') {
                $item->status_bahan = 'Forming';
            } elseif (strtolower($item->status_bahan) === 'non-forming' || strtolower($item->status_bahan) === 'non forming') {
                $item->status_bahan = 'Non Forming';
            }
            return $item;
        });
       
        return view('super-admin.produk.index', compact('jenis_produk'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->role == 'superadmin') {
            $plans = Plan::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        $allProduk = JenisProduk::with('plan')->orderBy('nama_produk')->get();
        return view('super-admin.produk.create', compact('plans', 'allProduk'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_plan'          => 'required|exists:plan,id',
            'nama_produk'      => 'required|string|max:255',
            'status_bahan'     => 'required|string|max:255',
            'id_produk_rework' => 'nullable|array',
            'id_produk_rework.*' => 'exists:jenis_produk,id',
        ]);

        $data['user_id'] = auth()->id();
        $data['id_produk_rework'] = $request->input('id_produk_rework') ? array_map('intval', $request->input('id_produk_rework')) : null;

        JenisProduk::create($data);

        return redirect()->route('produk.index')->with('success', 'Jenis produk berhasil ditambahkan');
    }

    public function edit($uuid)
    {
        $produk = JenisProduk::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        if ($user->role == 'superadmin') {
            $plans = Plan::all();
        } else {
            $plans = Plan::where('id', $user->id_plan)->get();
        }
        $allProduk = JenisProduk::with('plan')->orderBy('nama_produk')->get();
        return view('super-admin.produk.edit', compact('produk', 'plans', 'allProduk'));
    }

    public function update(Request $request, $uuid)
    {
        $produk = JenisProduk::where('uuid', $uuid)->firstOrFail();
        $data = $request->validate([
            'id_plan'          => 'required|exists:plan,id',
            'nama_produk'      => 'required|string|max:255',
            'status_bahan'     => 'required|string|max:255',
            'id_produk_rework' => 'nullable|array',
            'id_produk_rework.*' => 'exists:jenis_produk,id',
        ]);
        $data['id_produk_rework'] = $request->input('id_produk_rework') ? array_map('intval', $request->input('id_produk_rework')) : null;
        $produk->update($data);
        return redirect()->route('produk.index')->with('success', 'Produk berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $produk = JenisProduk::where('uuid', $uuid)->firstOrFail();
        $produk->delete();
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }
    public function getProdukByPlan($plan_id)
    {
        $produk = JenisProduk::where('id_plan', $plan_id)->get();
        return response()->json($produk);
    }

    public function getFrayerByProduk($produk_id)
    {
        // Misal model JenisProduk punya relasi suhu_frayer
        $produk = JenisProduk::find($produk_id);
        return response()->json([
            'suhu_frayer' => $produk->suhu_frayer ?? null
        ]);
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

        // Ambil semua plan
        $plans = Plan::pluck('id', 'nama_plan')->toArray();
        $user_id = auth()->id();
        $insertData = [];
        foreach ($rows as $i => $row) {
            if ($i == 0) continue; // Skip header
            $plan_name = trim($row[0] ?? '');
            $nama_produk = trim($row[1] ?? '');
            $status_bahan = trim($row[2] ?? '');

            if ($nama_produk && isset($plans[$plan_name])) {
                $insertData[] = [
                    'uuid' => \Str::uuid(), 
                    'nama_produk' => $nama_produk,
                    'id_plan' => $plans[$plan_name],
                    'status_bahan' => $status_bahan,
                    'user_id' => $user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        // dd($insertData);
        if (!empty($insertData)) {
            JenisProduk::insert($insertData);
        }

    return redirect()->route('produk.index')->with('success', 'Import data produk berhasil');
    }
    public function downloadTemplate()
    {
        $user = auth()->user();
            if ($user->role == 'superadmin') {
                $plans = Plan::all();
            } else {
                $plans = Plan::where('id', $user->id_plan)->get();
            }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Plan');
        $sheet->setCellValue('B1', 'Nama Produk');
        $sheet->setCellValue('C1', 'Jenis Produk');
        // Dropdown Plan (Excel Data Validation)
        $planNames = $plans->pluck('nama_plan')->toArray();
        $planList = '"' . implode(',', $planNames) . '"';
        
        // Dropdown Jenis Produk (Excel Data Validation)
        $jenisProdukList = '"forming,non-forming"';
        
        for ($row = 2; $row <= 500; $row++) {
            // Apply Plan dropdown validation to column A
            $validation = $sheet->getCell('A' . $row)->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(true);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1($planList);
            
            // Apply Jenis Produk dropdown validation to column C
            $validationJenis = $sheet->getCell('C' . $row)->getDataValidation();
            $validationJenis->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validationJenis->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $validationJenis->setAllowBlank(true);
            $validationJenis->setShowInputMessage(true);
            $validationJenis->setShowErrorMessage(true);
            $validationJenis->setShowDropDown(true);
            $validationJenis->setFormula1($jenisProdukList);
        }
        // Download
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_jenis_produk.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return Response::download($temp_file, $filename)->deleteFileAfterSend(true);
    }
    
}
