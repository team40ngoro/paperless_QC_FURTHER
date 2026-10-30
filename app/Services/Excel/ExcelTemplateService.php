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
            } elseif ($dbField === 'hasil_pengecekan') {
                $sampleRow[] = 'ok';
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
                
                // Master Data columns follow the same order as $masterFields.
                // Point each dropdown at its own field column (starting at row 2).
                $masterColumnIndex = array_search($dbField, array_keys($this->masterFields), true) + 1;
                $masterColumn = Coordinate::stringFromColumnIndex($masterColumnIndex);
                $listSource = $config['list_source'] ?? "='Master Data'!\$" . $masterColumn . "\$2:\$" . $masterColumn . "\$1000";
                
                // If list_source only has column letter (e.g., "='Master Data'!A:A"), fix it
                if (preg_match("/='[^']+!'[A-Z]+:[A-Z]+$/", $listSource)) {
                    // Replace format like A:A with A$2:A$100
                    $listSource = preg_replace("/='([^']+)'!([A-Z]+):([A-Z]+)$/", "='$1'!\$$2\$2:\$$3\$100", $listSource);
                }
                
                // Clean '=' and '$' first to prevent regex character escaping issues
                $cleanSource = str_replace(['=', '$'], '', $listSource);
                
                if (preg_match("/'([^']+)'!([A-Z]+)(\d+):([A-Z]+)(\d+)/", $cleanSource, $matches)) {
                    $sheetName = $matches[1];
                    $startCol  = strtoupper($matches[2]);
                    $startRow  = ((int)$matches[3] > 1) ? (int)$matches[3] : 2; // Enforce row 2 minimum
                    $endCol    = strtoupper($matches[4]);
                    $endRow    = (int)$matches[5];

                    $listSource = "='" . $sheetName . "'!\$" . $startCol . "\$" . $startRow . ":\$" . $endCol . "\$" . $endRow;
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
                $validation->setError('Pilih nilai dari dropdown yang tersedia. Header tidak boleh dipilih.');
                
                // Apply to rows 2-1000 (SKIP row 1 which is header)
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
            return array_values($config['options']);
            
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
class InstructionSheet implements FromArray, WithTitle, WithEvents
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
        $result = [
            ['=== PANDUAN IMPORT DATA ==='],
            [''],
            ['PETUNJUK UMUM:'],
            ['1. Field yang bertanda (*) wajib diisi'],
            ['2. Gunakan dropdown untuk field yang tersedia (jangan ketik manual)'],
            ['3. Jangan mengubah atau menghapus header (baris pertama)'],
            ['4. Jangan mengubah sheet "Master Data"'],
            ['5. Isi data mulai dari baris ke-2 di sheet "Data Import"'],
            [''],
            ['FORMAT DATA:'],
            ['- Tanggal: YYYY-MM-DD (contoh: ' . date('Y-m-d') . ')'],
            ['- Jam: HH:MM (contoh: 14:30)'],
            ['- Angka desimal: gunakan titik (contoh: 4.5)'],
            ['- Dropdown: pilih dari pilihan yang tersedia'],
            [''],
            ['KOLOM KHUSUS - SUHU RM:'],
            ['Kolom "Suhu RM" mendukung MULTIPLE VALUES (lebih dari satu nilai).'],
            ['Cara mengisi:'],
            ['  • Untuk satu nilai: 22.5'],
            ['  • Untuk multiple nilai: 22.5, 23.0, 24.1 (pisahkan dengan koma dan spasi)'],
            ['Contoh lengkap:'],
            ['  - BENAR: 22.5, 23.0, 24.1'],
            ['  - BENAR: 22.5, 23.0'],
            ['  - BENAR: 22.5'],
            ['  - SALAH: 22.5,23.0 (tanpa spasi)'],
            ['  - SALAH: 22.5; 23.0 (gunakan koma, bukan semicolon)'],
            [''],
            ['KOLOM YANG TERSEDIA:'],
        ];

        foreach ($this->mapping as $excelCol => $dbField) {
            $hasDropdown = isset($this->masterFields[$dbField]) ? ' (Dropdown)' : '';
            $result[] = ['- ' . $excelCol . $hasDropdown];
        }

        return $result;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                $highestRow = max(1, $sheet->getHighestRow());

                // Seluruh isi instruksi dikunci dan sheet diproteksi dari perubahan.
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)
                    ->getProtection()
                    ->setLocked(true);
                $sheet->getProtection()->setSheet(true);
                $sheet->getProtection()->setObjects(true);
                $sheet->getProtection()->setScenarios(true);
            },
        ];
    }
}
