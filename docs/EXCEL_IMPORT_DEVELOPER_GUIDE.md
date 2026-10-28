# 📘 Panduan Pengembang: Sistem Import Excel Global

Dokumentasi ini berisi arsitektur lengkap, penjelasan 4 komponen engine utama, penjelasan 4 metode kontrak inti, serta kode lengkap utuh (*100% real code implementation*) agar tim developer dapat langsung menyalin (*copy-paste*) kode tanpa terpotong-potong.

---

## 🏗️ Struktur File Sistem Import Excel Global

```
app/
├── Services/Excel/
│   ├── Contracts/
│   │   └── ImportableModel.php          ← Interface Kontrak Model (Definisi 4 Metode Inti)
│   ├── ExcelTemplateService.php         ← Engine Pembuat Excel 3 Sheet & Dropdown Otomatis
│   └── ExcelImportService.php           ← Engine Pemproses, Mapping, & Validasi Upload Excel
│
├── Http/Controllers/
│   ├── Traits/
│   │   └── HasExcelImport.php           ← Trait Controller (Download Template & Import Handler)
│   ├── ChillroomController.php          ← Controller Menggunakan HasExcelImport
│   └── SeasoningController.php          ← Controller Menggunakan HasExcelImport
│
└── Models/
    ├── PenerimaanChillroom.php          ← Model Implementasi ImportableModel (Chillroom)
    └── Seasoning.php                    ← Model Implementasi ImportableModel (Seasoning)
```

---

## ⚙️ Penjelasan 4 Komponen Engine Utama

Sistem Import Excel Global didukung oleh 4 file core engine utama yang bekerja secara otomatis:

1. **`ImportableModel.php` (Contract Interface)**:
   * Menjadi *kontrak* standar yang wajib diimplementasikan oleh setiap Model Laravel yang mendukung import Excel.
   * Menjamin setiap Model memiliki 4 metode statis wajib: `getImportMapping()`, `getImportRules()`, `getMasterDataFields()`, dan `transformImportRow()`.

2. **`HasExcelImport.php` (Controller Trait)**:
   * Trait terintegrasi untuk Controller yang menyediakan handler otomatis untuk action `downloadTemplate()` dan `importExcel()`.
   * Menangani pengambilan data sesi user (`user_id`, `id_plan`) serta mereturn flash message sukses/gagal ke halaman UI Blade.

3. **`ExcelTemplateService.php` (Template Generator Service)**:
   * Service yang bertugas membangun workbook Excel dengan 3 Sheet terstruktur:
     * **Sheet 1 (Data Import)**: Tempat user mengisi data dengan header berwawasan visual & Data Validation Dropdown pada sel baris 2-1000.
     * **Sheet 2 (Master Data)**: Berisi daftar data acuan (Shift, Nama RM/Seasoning) dari DB sesuai `id_plan` user yang terkunci (*read-only protected sheet*).
     * **Sheet 3 (Instruksi)**: Petunjuk penggunaan & format input untuk pengguna akhir.

4. **`ExcelImportService.php` (Excel Import Processor Service)**:
   * Service yang bertugas memproses file Excel yang diunggah:
     * Membaca Sheet 1 (*Data Import*).
     * Melakukan stripping tanda required (`*`) dari header Excel.
     * Memetakan nama kolom Excel ke field DB berdasarkan `getImportMapping()`.
     * Menjalankan fungsi `transformImportRow()` untuk penyesuaian data (misal konversi nama shift ke `shift_id`).
     * Menjalankan `Validator::make()` Laravel berdasarkan `getImportRules()`.
     * Menggunakan Database Transaction (`DB::beginTransaction()`) untuk memastikan data disimpan secara aman.

---

## 📑 Penjelasan Mendalam 4 Metode Inti Model (`ImportableModel`)

 Setiap Model yang mengimplementasikan `ImportableModel` mendefinisikan 4 metode berikut:

1. **`getImportMapping(): array`**
   * **Fungsi**: Memetakan nama header kolom di file Excel ke nama kolom/atribut di database.
   * **Cara Kerja**: Service membaca array ini untuk menyusun posisi kolom di template Excel dan memasukkan nilai sel ke atribut database yang sesuai.

2. **`getImportRules(): array`**
   * **Fungsi**: Mendefinisikan aturan validasi Laravel (`Validator`) untuk setiap baris data Excel.
   * **Cara Kerja**: Sebelum baris disimpan ke DB, `ExcelImportService` menguji data dengan rule ini. Jika melanggar, baris ditolak dan dicatat nomor baris beserta pesan errornya tanpa menggagalkan baris valid lainnya.

3. **`getMasterDataFields(): array`**
   * **Fungsi**: Mendefinisikan sumber data dropdown yang akan dimasukkan ke Sheet **Master Data** dan dipasang sebagai dropdown sel otomatis di Sheet **Data Import**.
   * **Konfigurasi**: Mendukung data dinamis dari database berdasarkan `id_plan` user (via `source`, `label_column`, `value_column`, `filter`) maupun pilihan statis (via `options`).

4. **`transformImportRow(array $row): array`**
   * **Fungsi**: Mengubah atau mengisi otomatis (*autofill*) data per baris sebelum divalidasi dan disimpan.
   * **Kegunaan**: Mengubah nama shift (misal `"1"`) menjadi ID integer (`shift_id`), mengisi otomatis tanggal/jam dengan waktu hari ini jika dikosongkan user, dan auto-generate `Str::uuid()`.

---

## 🛠️ Kode Utuh File Engine Core

### 1. Interface Contract (`app/Services/Excel/Contracts/ImportableModel.php`)

```php
<?php

namespace App\Services\Excel\Contracts;

interface ImportableModel
{
    /**
     * Get the column mapping for Excel import
     * 
     * @return array ['Excel Column Name' => 'database_field_name']
     */
    public static function getImportMapping(): array;
    
    /**
     * Get validation rules for import
     * 
     * @return array ['field_name' => 'validation_rule']
     */
    public static function getImportRules(): array;
    
    /**
     * Get master data fields for dropdown in Excel template
     * 
     * @return array [
     *   'field_name' => [
     *     'label' => 'Display Label',
     *     'source' => ModelClass::class,  // or 'options' => []
     *     'label_column' => 'name',
     *     'value_column' => 'id',
     *     'list_source' => "='Master Data'!A2:A100",
     *   ]
     * ]
     */
    public static function getMasterDataFields(): array;
    
    /**
     * Transform row data before import (optional)
     * 
     * @param array $row
     * @return array
     */
    public static function transformImportRow(array $row): array;
}
```

---

### 2. Controller Trait (`app/Http/Controllers/Traits/HasExcelImport.php`)

```php
<?php

namespace App\Http\Controllers\Traits;

use App\Services\Excel\ExcelImportService;
use App\Services\Excel\ExcelTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HasExcelImport
{
    /**
     * Download Excel template with master data dropdowns
     */
    public function downloadTemplate()
    {
        try {
            $modelClass = $this->getImportModelClass();
            $filename = $this->getTemplateFilename();
            
            $templateService = new ExcelTemplateService();
            
            return $templateService->generateWithDropdowns($modelClass, $filename);
            
        } catch (\Exception $e) {
            Log::error('Template download error: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Gagal mengunduh template: ' . $e->getMessage());
        }
    }
    
    /**
     * Import data from Excel file
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240', // Max 10MB
        ]);
        
        try {
            $file = $request->file('file');
            $modelClass = $this->getImportModelClass();
            
            // Additional data (user_id, id_plan, etc.)
            $additionalData = $this->getAdditionalImportData();
            
            $importService = new ExcelImportService();
            $results = $importService->import($file, $modelClass, $additionalData);
            
            if ($results['success'] > 0) {
                $message = "Import berhasil: {$results['success']} data diimport";
                
                if ($results['failed'] > 0) {
                    $message .= ", {$results['failed']} data gagal";
                }
                
                return redirect()->back()->with([
                    'success' => $message,
                    'import_errors' => $results['errors']
                ]);
            } else {
                return redirect()->back()->with([
                    'error' => 'Import gagal: Tidak ada data yang berhasil diimport',
                    'import_errors' => $results['errors']
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }
    
    /**
     * Get the model class for import (must be implemented by controller)
     */
    abstract protected function getImportModelClass(): string;
    
    /**
     * Get the template filename (must be implemented by controller)
     */
    abstract protected function getTemplateFilename(): string;
    
    /**
     * Get additional data to merge with import (optional override)
     */
    protected function getAdditionalImportData(): array
    {
        return [
            'user_id' => auth()->id(),
            'id_plan' => auth()->user()->id_plan ?? null,
        ];
    }
}
```

---

### 3. Excel Import Service (`app/Services/Excel/ExcelImportService.php`)

```php
<?php

namespace App\Services\Excel;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class ExcelImportService
{
    /**
     * Import data from Excel file
     * 
     * @param mixed $file Uploaded file
     * @param string $modelClass Model class that implements ImportableModel
     * @param array $additionalData Additional data to merge with each row
     * @return array ['success' => int, 'failed' => int, 'errors' => array]
     */
    public function import($file, string $modelClass, array $additionalData = []): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        DB::beginTransaction();
        
        try {
            // Read Excel file
            $sheets = Excel::toArray([], $file);
            $data = $sheets[0] ?? []; // Get first sheet (Data Import)
            
            if (empty($data)) {
                throw new Exception('File Excel kosong atau tidak valid.');
            }
            
            $headers = array_shift($data); // Remove header row
            $headers = array_map(function($header) {
                // Remove asterisk (*) for required indicator
                return trim(str_replace('*', '', $header));
            }, $headers);
            
            $mapping = $modelClass::getImportMapping();
            $rules = $modelClass::getImportRules();
            
            foreach ($data as $rowIndex => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                try {
                    // Map Excel columns to database fields
                    $rowData = $this->mapRowToFields($row, $headers, $mapping);
                    
                    // Merge additional data
                    $rowData = array_merge($rowData, $additionalData);
                    
                    // Transform if needed
                    if (method_exists($modelClass, 'transformImportRow')) {
                        $rowData = $modelClass::transformImportRow($rowData);
                    }
                    
                    // Validate
                    $validator = Validator::make($rowData, $rules);
                    
                    if ($validator->fails()) {
                        $results['failed']++;
                        $results['errors'][] = "Baris " . ($rowIndex + 2) . ": " . $validator->errors()->first();
                        continue;
                    }
                    
                    // Insert to database
                    $modelClass::create($rowData);
                    $results['success']++;
                    
                } catch (Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Baris " . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }
            
            if ($results['success'] > 0) {
                DB::commit();
            } else {
                DB::rollBack();
            }
            
            return $results;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Map Excel row to database fields
     */
    private function mapRowToFields(array $row, array $headers, array $mapping): array
    {
        $result = [];
        
        foreach ($mapping as $excelCol => $dbField) {
            // Find column index in headers
            $index = array_search($excelCol, $headers);
            
            if ($index !== false && isset($row[$index])) {
                $value = $row[$index];
                
                // Skip empty values
                if ($value !== null && $value !== '') {
                    $result[$dbField] = $value;
                }
            }
        }
        
        return $result;
    }
}
```

---

### 4. Excel Template Service (`app/Services/Excel/ExcelTemplateService.php`)

```php
<?php

namespace App\Services\Excel;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExcelTemplateService
{
    /**
     * Generate Excel template with master data dropdowns
     */
    public function generateWithDropdowns(string $modelClass, string $filename)
    {
        $mapping = $modelClass::getImportMapping();
        $masterFields = $modelClass::getMasterDataFields();
        
        $export = new class($mapping, $masterFields) implements WithMultipleSheets {
            public function __construct(
                private array $mapping,
                private array $masterFields
            ) {}
            
            public function sheets(): array
            {
                return [
                    new DataImportSheet($this->mapping, $this->masterFields),
                    new MasterDataSheet($this->masterFields),
                    new InstructionSheet($this->mapping, $this->masterFields),
                ];
            }
        };
        
        return Excel::download($export, $filename);
    }
}

/**
 * Sheet 1: Data Import
 */
class DataImportSheet implements FromArray, WithHeadings, WithTitle, WithEvents
{
    public function __construct(
        private array $mapping,
        private array $masterFields
    ) {}
    
    public function title(): string
    {
        return 'Data Import';
    }
    
    public function headings(): array
    {
        $headers = [];
        foreach ($this->mapping as $excelCol => $dbField) {
            // Add asterisk for required fields (simplified)
            $headers[] = $excelCol . ' *';
        }
        return $headers;
    }
    
    public function array(): array
    {
        // Add sample row with auto-filled date/time dynamically matching field names
        $now = \Carbon\Carbon::now('Asia/Jakarta');
        $sampleRow = [];

        foreach ($this->mapping as $excelCol => $dbField) {
            if (in_array($dbField, ['tanggal', 'tanggal_terima', 'tanggal_masuk', 'tanggal_proses'])) {
                $sampleRow[] = $now->format('Y-m-d');
            } elseif (in_array($dbField, ['jam', 'jam_kedatangan', 'jam_masuk', 'jam_proses'])) {
                $sampleRow[] = $now->format('H:i');
            } elseif ($dbField === 'tanggal_expired') {
                $sampleRow[] = $now->copy()->addMonths(6)->format('Y-m-d');
            } else {
                $sampleRow[] = '';
            }
        }

        return [$sampleRow];
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Apply styling to header
                $lastColumn = Coordinate::stringFromColumnIndex(count($this->mapping));
                $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);
                
                // Set column widths
                foreach(range('A', $lastColumn) as $col) {
                    $sheet->getColumnDimension($col)->setWidth(20);
                }
                
                // Set row height for header
                $sheet->getRowDimension(1)->setRowHeight(25);
                
                // Apply dropdowns for master data fields
                $this->applyDropdowns($sheet);
            }
        ];
    }
    
    private function applyDropdowns($sheet)
    {
        $columnIndex = 1;
        
        foreach ($this->mapping as $excelCol => $dbField) {
            if (isset($this->masterFields[$dbField])) {
                $config = $this->masterFields[$dbField];
                $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
                
                // Get list source from config and ensure range starts from row 2 (skipping header)
                $listSource = $config['list_source'] ?? "='Master Data'!A2:A100";
                if (preg_match("/'([^']+)'!([A-Z]+):([A-Z]+)$/", $listSource, $matches)) {
                    $listSource = "='" . $matches[1] . "'!" . $matches[2] . "2:" . $matches[3] . "100";
                } elseif (preg_match("/'([^']+)'!([A-Z]+)1:([A-Z]+)(\d+)$/", $listSource, $matches)) {
                    $listSource = "='" . $matches[1] . "'!" . $matches[2] . "2:" . $matches[3] . $matches[4];
                }
                
                // Create new validation object for each cell range
                $validation = new DataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                $validation->setFormula1($listSource);
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Nilai Tidak Valid');
                $validation->setError('Pilih nilai dari dropdown yang tersedia');
                
                // Apply to rows 2-1000
                $cellRange = $columnLetter . '2:' . $columnLetter . '1000';
                $sheet->setDataValidation($cellRange, $validation);
                
                // Add light background color to dropdown cells to make them more visible
                $sheet->getStyle($cellRange)->applyFromArray([
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFF2CC']],
                ]);
            }
            
            $columnIndex++;
        }
    }
}

/**
 * Sheet 2: Master Data
 */
class MasterDataSheet implements FromArray, WithTitle, WithHeadings, WithEvents
{
    public function __construct(private array $masterFields) {}
    
    public function title(): string
    {
        return 'Master Data';
    }
    
    public function headings(): array
    {
        $headers = [];
        foreach ($this->masterFields as $fieldName => $config) {
            $headers[] = $config['label'] ?? $fieldName;
        }
        return $headers;
    }
    
    public function array(): array
    {
        $data = [];
        $maxRows = 0;
        $columns = [];
        
        // Fetch master data for each field
        foreach ($this->masterFields as $fieldName => $config) {
            $items = $this->fetchMasterData($config);
            $columns[] = $items;
            $maxRows = max($maxRows, count($items));
        }
        
        // Transpose columns to rows
        for ($i = 0; $i < $maxRows; $i++) {
            $row = [];
            foreach ($columns as $column) {
                $row[] = $column[$i] ?? '';
            }
            $data[] = $row;
        }
        
        return $data;
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Style header row
                $lastColumn = Coordinate::stringFromColumnIndex(count($this->masterFields));
                $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '70AD47']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);
                
                // Set column widths for better visibility
                foreach(range('A', $lastColumn) as $col) {
                    $sheet->getColumnDimension($col)->setWidth(25);
                }
                
                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(20);
                
                // Add alternating row colors for better readability
                for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray([
                            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E7F3E7']],
                        ]);
                    }
                }
                
                // Lock all cells to prevent editing
                $sheet->getStyle('A:' . $lastColumn)->getProtection()->setLocked(true);
                
                // Enable sheet protection without password
                $sheet->getProtection()->setSheet(true);
                $sheet->getProtection()->setObjects(true);
                $sheet->getProtection()->setScenarios(true);
            }
        ];
    }
    
    private function fetchMasterData(array $config): array
    {
        if (isset($config['source']) && is_string($config['source']) && class_exists($config['source'])) {
            // From model
            $model = $config['source'];
            $labelCol = $config['label_column'] ?? 'name';
            $valueCol = $config['value_column'] ?? 'id';
            
            // Apply filter if exists
            $query = $model::query();
            if (isset($config['filter']) && is_array($config['filter'])) {
                foreach ($config['filter'] as $filterKey => $filterValue) {
                    if ($filterValue !== null) {
                        $query = $query->where($filterKey, $filterValue);
                    }
                }
            }

            $items = $query->get();
            
            if ($items->isEmpty()) {
                return [];
            }

            return $items->map(function($item) use ($labelCol, $valueCol) {
                $label = $item->{$labelCol};
                $value = $item->{$valueCol};
                
                // If value and label are the same (like nama_rm), just use the label
                // Otherwise format as "value - label"
                if ($value === $label) {
                    return $label;
                } else {
                    return $value . ' - ' . $label;
                }
            })->toArray();
            
        } elseif (isset($config['options']) && is_array($config['options'])) {
            // From static array
            return $config['options'];
            
        } elseif (isset($config['callback']) && is_callable($config['callback'])) {
            // From callback
            return call_user_func($config['callback']);
        }
        
        return [];
    }
}

/**
 * Sheet 3: Instruction
 */
class InstructionSheet implements FromArray, WithTitle
{
    public function __construct(
        private array $mapping,
        private array $masterFields
    ) {}
    
    public function title(): string
    {
        return 'Instruksi';
    }
    
    public function array(): array
    {
        return [
            ['=== PANDUAN IMPORT DATA FORM ==='],
            [''],
            ['PETUNJUK UMUM:'],
            ['1. Field yang bertanda (*) wajib diisi'],
            ['2. Gunakan dropdown untuk field yang tersedia (jangan ketik manual)'],
            ['3. Jangan mengubah atau menghapus header (baris pertama)'],
            ['4. Jangan mengubah sheet "Master Data"'],
            ['5. Isi data mulai dari baris ke-2 di sheet "Data Import"'],
            [''],
            ['FORMAT DATA:'],
            ['- Tanggal: YYYY-MM-DD (contoh: 2026-10-06)'],
            ['- Jam: HH:MM (contoh: 14:30)'],
            ['- Angka desimal: gunakan titik (contoh: 4.5)'],
            ['- Shift: pilih dari dropdown'],
            [''],
            ['KOLOM YANG TERSEDIA:'],
        ];
        
        // Add field list
        $result = $this->array();
        foreach ($this->mapping as $excelCol => $dbField) {
            $hasDropdown = isset($this->masterFields[$dbField]) ? ' (Dropdown)' : '';
            $result[] = ['- ' . $excelCol . $hasDropdown];
        }
        
        return $result;
    }
}
```

---

## 💻 Kode Lengkap 100% Modul Eksisting (`Chillroom`)

### 1. Model (`app/Models/PenerimaanChillroom.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Traits\HasAudit;
use App\Services\Excel\Contracts\ImportableModel;

class PenerimaanChillroom extends Model implements ImportableModel
{
    use HasFactory, HasAudit;

    protected $table = 'penerimaan_chillroom';

    protected $fillable = [
        'uuid',
        'nama_rm',
        'kode_produksi',
        'berat',
        'tanggal',
        'suhu',
        'shift_id',
        'jam_kedatangan',
        'sensori',
        'kemasan',
        'keterangan',
        'standar_berat',
        'jumlah_rm',
        'status_rm',
        'nilai_jumlah_rm',
        'catatan_rm',
        'id_plan',
        'user_id',
        'kode_form',
        'is_audit',
        'source_uuid',
    ];

    /**
     * Column mapping for Excel import
     */
    public static function getImportMapping(): array
    {
        return [
            'Nama RM'           => 'nama_rm',
            'Kode Produksi'     => 'kode_produksi',
            'Berat (kg)'        => 'berat',
            'Tanggal'           => 'tanggal',
            'Jam Kedatangan'    => 'jam_kedatangan',
            'Suhu (°C)'         => 'suhu',
            'Shift'             => 'shift_id',
            'Sensori'           => 'sensori',
            'Kemasan'           => 'kemasan',
            'Standar Berat'     => 'standar_berat',
            'Jumlah RM'         => 'jumlah_rm',
            'Status RM'         => 'status_rm',
            'Keterangan'        => 'keterangan',
            'Catatan RM'        => 'catatan_rm',
        ];
    }

    /**
     * Validation rules for import
     */
    public static function getImportRules(): array
    {
        return [
            'nama_rm'           => 'required|string|max:255',
            'kode_produksi'     => 'required|string|max:100',
            'berat'             => 'required|numeric|min:0',
            'tanggal'           => 'nullable|date_format:Y-m-d',
            'jam_kedatangan'    => 'nullable|date_format:H:i',
            'suhu'              => 'required|numeric',
            'shift_id'          => 'required|integer|exists:data_shift,id',
            'sensori'           => 'required|in:✓ Oke,✘ Tidak Oke',
            'kemasan'           => 'required|in:✓ Oke,✘ Tidak Oke',
            'standar_berat'     => 'nullable|numeric|min:0',
            'jumlah_rm'         => 'nullable|numeric|min:0',
            'status_rm'         => 'nullable|in:diterima,diretur',
            'keterangan'        => 'nullable|string',
            'catatan_rm'        => 'nullable|string',
        ];
    }

    /**
     * Master data fields for dropdown in Excel template
     */
    public static function getMasterDataFields(): array
    {
        $idPlan = auth()->user()->id_plan ?? null;

        return [
            'nama_rm' => [
                'label'        => 'Nama RM',
                'source'       => DataRm::class,
                'label_column' => 'nama_rm',
                'value_column' => 'nama_rm',
                'filter'       => ['id_plan' => $idPlan],
                'list_source'  => "='Master Data'!A:A",
            ],
            'shift_id' => [
                'label'        => 'Shift',
                'source'       => DataShift::class,
                'label_column' => 'shift',
                'value_column' => 'shift',
                'filter'       => ['id_plan' => $idPlan],
                'list_source'  => "='Master Data'!B:B",
            ],
            'sensori' => [
                'label'       => 'Sensori',
                'options'     => ['✓ Oke', '✘ Tidak Oke'],
                'list_source' => "='Master Data'!E:E",
            ],
            'kemasan' => [
                'label'       => 'Kemasan',
                'options'     => ['✓ Oke', '✘ Tidak Oke'],
                'list_source' => "='Master Data'!F:F",
            ],
            'status_rm' => [
                'label'       => 'Status RM',
                'options'     => ['diterima', 'diretur'],
                'list_source' => "='Master Data'!G:G",
            ],
        ];
    }

    /**
     * Transform row data before import
     */
    public static function transformImportRow(array $row): array
    {
        if (isset($row['kode_produksi']) && is_numeric($row['kode_produksi'])) {
            $row['kode_produksi'] = (string) $row['kode_produksi'];
        }

        if (empty($row['tanggal']) || $row['tanggal'] === '' || $row['tanggal'] === null) {
            $row['tanggal'] = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        } else {
            try {
                $row['tanggal'] = Carbon::parse($row['tanggal'])->format('Y-m-d');
            } catch (\Exception $e) {
                $row['tanggal'] = Carbon::now('Asia/Jakarta')->format('Y-m-d');
            }
        }

        if (empty($row['jam_kedatangan']) || $row['jam_kedatangan'] === '' || $row['jam_kedatangan'] === null) {
            $row['jam_kedatangan'] = Carbon::now('Asia/Jakarta')->format('H:i');
        } else {
            try {
                $row['jam_kedatangan'] = Carbon::parse($row['jam_kedatangan'])->format('H:i');
            } catch (\Exception $e) {
                $row['jam_kedatangan'] = Carbon::now('Asia/Jakarta')->format('H:i');
            }
        }

        if (isset($row['shift_id'])) {
            $shiftValue = trim((string) $row['shift_id']);
            $shift = DataShift::where('shift', $shiftValue)->first();
            if ($shift) {
                $row['shift_id'] = (int) $shift->id;
            } else {
                $row['shift_id'] = (int) $shiftValue;
            }
        }

        if (!isset($row['uuid'])) {
            $row['uuid'] = \Str::uuid();
        }

        return $row;
    }
}
```

### 2. Controller (`app/Http/Controllers/ChillroomController.php`)

```php
<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanChillroom;
use App\Http\Controllers\Traits\HasExcelImport;

class ChillroomController extends Controller
{
    use HasExcelImport;

    protected function getImportModelClass(): string
    {
        return PenerimaanChillroom::class;
    }

    protected function getTemplateFilename(): string
    {
        return 'Template_Import_Chillroom_' . date('Y-m-d_H-i-s') . '.xlsx';
    }
}
```

---

## 💻 Kode Lengkap 100% Modul Penerapan Baru (`Seasoning`)

### 1. Model (`app/Models/Seasoning.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasAudit;
use App\Services\Excel\Contracts\ImportableModel;
use Carbon\Carbon;

class Seasoning extends Model implements ImportableModel
{
    use HasAudit;

    protected $fillable = [
        'uuid',
        'id_plan',
        'created_by',
        'nama_rm',
        'kode_produksi',
        'berat',
        'sensori',
        'kemasan',
        'keterangan',
        'shift_id',
        'tanggal',
        'jam',
        'is_audit',
        'source_uuid',
    ];

    /**
     * Column mapping for Excel import
     */
    public static function getImportMapping(): array
    {
        return [
            'Nama Seasoning'    => 'nama_rm',
            'Kode Produksi'     => 'kode_produksi',
            'Berat (kg)'        => 'berat',
            'Tanggal'           => 'tanggal',
            'Jam'               => 'jam',
            'Shift'             => 'shift_id',
            'Sensori'           => 'sensori',
            'Kemasan'           => 'kemasan',
            'Keterangan'        => 'keterangan',
        ];
    }

    /**
     * Validation rules for import
     */
    public static function getImportRules(): array
    {
        return [
            'nama_rm'           => 'required|string|max:255',
            'kode_produksi'     => 'required|string|max:100',
            'berat'             => 'required|numeric|min:0',
            'tanggal'           => 'nullable|date_format:Y-m-d',
            'jam'               => 'nullable|date_format:H:i',
            'shift_id'          => 'required|integer|exists:data_shift,id',
            'sensori'           => 'required|in:✔ Baik,✘ Tidak Baik',
            'kemasan'           => 'required|in:✔ Baik,✘ Tidak Baik',
            'keterangan'        => 'nullable|string',
        ];
    }

    /**
     * Master data fields for dropdown in Excel template
     */
    public static function getMasterDataFields(): array
    {
        $idPlan = auth()->user()->id_plan ?? null;

        return [
            'nama_rm' => [
                'label'        => 'Nama Seasoning',
                'source'       => DataSeasoning::class,
                'label_column' => 'nama_seasoning',
                'value_column' => 'nama_seasoning',
                'filter'       => ['id_plan' => $idPlan],
                'list_source'  => "='Master Data'!A:A",
            ],
            'shift_id' => [
                'label'        => 'Shift',
                'source'       => DataShift::class,
                'label_column' => 'shift',
                'value_column' => 'shift',
                'filter'       => ['id_plan' => $idPlan],
                'list_source'  => "='Master Data'!B:B",
            ],
            'sensori' => [
                'label'       => 'Sensori',
                'options'     => ['✔ Baik', '✘ Tidak Baik'],
                'list_source' => "='Master Data'!E:E",
            ],
            'kemasan' => [
                'label'       => 'Kemasan',
                'options'     => ['✔ Baik', '✘ Tidak Baik'],
                'list_source' => "='Master Data'!F:F",
            ],
        ];
    }

    /**
     * Transform row data before import
     */
    public static function transformImportRow(array $row): array
    {
        if (isset($row['kode_produksi']) && is_numeric($row['kode_produksi'])) {
            $row['kode_produksi'] = (string) $row['kode_produksi'];
        }

        if (empty($row['tanggal']) || $row['tanggal'] === '' || $row['tanggal'] === null) {
            $row['tanggal'] = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        } else {
            try {
                $row['tanggal'] = Carbon::parse($row['tanggal'])->format('Y-m-d');
            } catch (\Exception $e) {
                $row['tanggal'] = Carbon::now('Asia/Jakarta')->format('Y-m-d');
            }
        }

        if (empty($row['jam']) || $row['jam'] === '' || $row['jam'] === null) {
            $row['jam'] = Carbon::now('Asia/Jakarta')->format('H:i');
        } else {
            try {
                $row['jam'] = Carbon::parse($row['jam'])->format('H:i');
            } catch (\Exception $e) {
                $row['jam'] = Carbon::now('Asia/Jakarta')->format('H:i');
            }
        }

        if (isset($row['shift_id'])) {
            $shiftValue = trim((string) $row['shift_id']);
            $shift = DataShift::where('shift', $shiftValue)->first();
            if ($shift) {
                $row['shift_id'] = (int) $shift->id;
            } else {
                $row['shift_id'] = (int) $shiftValue;
            }
        }

        if (isset($row['nama_rm'])) {
            $row['nama_rm'] = trim((string) $row['nama_rm']);
        }

        if (isset($row['berat']) && !is_null($row['berat'])) {
            $row['berat'] = (float) str_replace(',', '.', (string) $row['berat']);
        }

        if (!isset($row['uuid'])) {
            $row['uuid'] = (string) Str::uuid();
        }

        return $row;
    }
}
```

### 2. Controller (`app/Http/Controllers/SeasoningController.php`)

```php
<?php

namespace App\Http\Controllers;

use App\Models\Seasoning;
use App\Http\Controllers\Traits\HasExcelImport;

class SeasoningController extends Controller
{
    use HasExcelImport;

    protected function getImportModelClass(): string
    {
        return Seasoning::class;
    }

    protected function getTemplateFilename(): string
    {
        return 'Seasoning_Template.xlsx';
    }
}
```

---

## 🌐 Route & View Components

### 1. Route (`routes/web.php`)

```php
Route::group(['middleware' => ['auth']], function () {
    // Route Modul Chillroom
    Route::get('chillroom/download-template', [ChillroomController::class, 'downloadTemplate'])->name('chillroom.download-template');
    Route::post('chillroom/import-excel', [ChillroomController::class, 'importExcel'])->name('chillroom.import-excel');

    // Route Modul Seasoning
    Route::get('qc-sistem/seasoning/download-template', [SeasoningController::class, 'downloadTemplate'])->name('seasoning.download-template');
    Route::post('qc-sistem/seasoning/import-excel', [SeasoningController::class, 'importExcel'])->name('seasoning.import-excel');
});
```

### 2. Blade Toolbar & Modal Upload (`resources/views/qc-sistem/seasoning/index.blade.php`)

```blade
<!-- Tombol Import Excel -->
<div class="btn-group">
    <button type="button" class="btn btn-success btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
        <i class="fas fa-file-import mr-1"></i> Import Excel
    </button>
    <div class="dropdown-menu dropdown-menu-right">
        <a href="{{ route('seasoning.download-template') }}" class="dropdown-item">
            <i class="fas fa-download text-success mr-2"></i> Download Template
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item" data-toggle="modal" data-target="#importExcelModal">
            <i class="fas fa-upload text-primary mr-2"></i> Upload File
        </a>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('seasoning.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="importExcelModalLabel"><i class="fas fa-file-import mr-2"></i> Import Data Seasoning</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0">
                        <i class="fas fa-info-circle mr-2"></i> Gunakan template resmi untuk format data yang valid.
                        <a href="{{ route('seasoning.download-template') }}" class="alert-link" target="_blank">Download template di sini</a>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted mb-2">Pilih File Excel</label>
                        <div class="custom-file shadow-sm">
                            <input type="file" class="custom-file-input" id="excelFile" name="file" accept=".xlsx,.xls" required>
                            <label class="custom-file-label" for="excelFile">Pilih file...</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold"><i class="fas fa-upload mr-2"></i> Import Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
```

---

## 💡 Ringkasan 4 Langkah Penerapan Global

Untuk menerapkan fitur ini di modul baru (misal: `Rebox` / `Thawing`):

1. **Model**: Tambahkan `implements ImportableModel` dan tuliskan 4 metode inti (`getImportMapping`, `getImportRules`, `getMasterDataFields`, `transformImportRow`) dalam **1 file Model utuh**.
2. **Controller**: Gunakan `use HasExcelImport;` dan tentukan `getImportModelClass()` & `getTemplateFilename()`.
3. **Route**: Daftarkan 2 Route (`download-template` dan `import-excel`).
4. **View**: Pasang tombol Toolbar & Modal Upload di Blade.

---
*Terakhir Diperbarui: Oktober 2026*
