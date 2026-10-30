<?php

namespace App\Imports;

use App\Models\DataShift;
use App\Models\Shoestring;
use App\Models\DataDefect;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class ShoestringImport implements WithMultipleSheets
{
    protected $user;
    protected array $importErrors = [];
    protected int $insertedCount = 0;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sheets(): array
    {
        return [
            'Template' => new ShoestringTemplateSheetImport($this),
        ];
    }

    public function getUser()
    {
        return $this->user;
    }

    public function addImportError(string $message): void
    {
        $this->importErrors[] = $message;
    }

    public function setInsertedCount(int $count): void
    {
        $this->insertedCount = $count;
    }

    public function getInsertedCount(): int
    {
        return $this->insertedCount;
    }

    public function getImportErrors(): array
    {
        return $this->importErrors;
    }
}

class ShoestringTemplateSheetImport implements ToCollection, WithStartRow, WithCalculatedFormulas
{
    protected ShoestringImport $parent;

    public function __construct(ShoestringImport $parent)
    {
        $this->parent = $parent;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        $user = $this->parent->getUser();

        if (!$user || !$user->id_plan) {
            $this->parent->addImportError('Plan user tidak ditemukan. Tidak dapat melakukan import.');
            return;
        }

        $shiftMap = DataShift::where('id_plan', $user->id_plan)
            ->pluck('id', 'shift')
            ->toArray();
        
        $defectMap = DataDefect::where('id_plan', $user->id_plan)
            ->pluck('id', 'jenis_defect')
            ->toArray();

        $insertData = [];

        $parseDateCell = function ($value) {
            if ($value === null || $value === '') {
                return null;
            }

            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->format('Y-m-d');
            }

            if ($value instanceof RichText) {
                $value = $value->getPlainText();
            }

            // Handle Excel formula results or generic string
            $valueStr = trim((string) $value);
            if (empty($valueStr)) return null;

            // If it's a numeric value from Excel (date as number)
            if (is_numeric($valueStr)) {
                try {
                    return ExcelDate::excelToDateTimeObject((float)$valueStr)->format('Y-m-d');
                } catch (\Exception $e) {
                    // fall through
                }
            }

            $formats = [
                'd/m/Y', 'd-m-Y', 'd.m.Y', 
                'Y-m-d', 'Y/m/d', 'Y.m.d',
                'm/d/Y', 'm-d-Y',
                'd/m/y', 'd-m-y'
            ];
            
            foreach ($formats as $fmt) {
                try {
                    $d = Carbon::createFromFormat($fmt, $valueStr);
                    if ($d) return $d->format('Y-m-d');
                } catch (\Exception $e) {
                    continue;
                }
            }

            try {
                return Carbon::parse($valueStr)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        };

        foreach ($rows as $i => $row) {
            $row = is_array($row) ? $row : $row->toArray();

            $shiftVal = trim((string) ($row[0] ?? ''));
            $tanggalVal = $row[1] ?? null;
            $jamVal = $row[2] ?? null;
            $namaProdusen = trim((string) ($row[3] ?? ''));
            $kodeProduksi = trim((string) ($row[4] ?? ''));
            $bestBeforeVal = $row[5] ?? null;
            $samplingDefectNames = trim((string) ($row[6] ?? ''));
            $samplingDefectQtyRaw = trim((string) ($row[7] ?? ''));
            $catatan = trim((string) ($row[8] ?? ''));

            if ($shiftVal === '' && $namaProdusen === '' && $kodeProduksi === '') {
                continue;
            }

            $excelRow = $i + 2;
            $reasons = [];

            $shiftKey = is_numeric($shiftVal) ? (int) $shiftVal : $shiftVal;
            $idShift = $shiftMap[$shiftKey] ?? null;
            if (!$idShift) {
                $reasons[] = "Shift tidak ditemukan: '{$shiftVal}'";
            }

            $tanggal = $parseDateCell($tanggalVal);
            if (!$tanggal) {
                $tanggal = now()->format('Y-m-d');
            }

            $jam = null;
            if ($jamVal !== null && $jamVal !== '') {
                if (is_numeric($jamVal)) {
                    try {
                        $jam = ExcelDate::excelToDateTimeObject((float)$jamVal)->format('H:i');
                    } catch (\Exception $e) {
                        $jam = null;
                    }
                } else {
                    try {
                        $jam = Carbon::parse($jamVal)->format('H:i');
                    } catch (\Exception $e) {
                        $jam = null;
                    }
                }
            }

            if (!$jam) {
                $jam = now()->format('H:i');
            }

            $bestBefore = $parseDateCell($bestBeforeVal);

            if ($namaProdusen === '') {
                $reasons[] = 'Nama Produsen kosong (cek kolom D)';
            }
            if ($kodeProduksi === '') {
                $reasons[] = 'Kode Produksi kosong (cek kolom E)';
            }
            if (!$bestBefore) {
                $reasons[] = 'Best Before tidak valid atau kosong (cek kolom F)';
            }

            // Parse defect qty
            $qtyMap = [];
            $totalDefect = 0;
            if (!empty($samplingDefectQtyRaw)) {
                $pairs = explode(',', $samplingDefectQtyRaw);
                foreach ($pairs as $pair) {
                    $parts = explode(':', $pair);
                    if (count($parts) === 2) {
                        $defectName = trim($parts[0]);
                        $qty = trim($parts[1]);
                        if (isset($defectMap[$defectName])) {
                            $defectId = $defectMap[$defectName];
                            $qtyValue = (float)str_replace(',', '.', $qty);
                            $qtyMap[(string)$defectId] = (string)$qtyValue;
                            $totalDefect += $qtyValue;
                        } else {
                            // $reasons[] = "Warning: Defect '{$defectName}' tidak ditemukan di plan ini.";
                        }
                    }
                }
            }

            if (!empty($reasons)) {
                $this->parent->addImportError('Row ' . $excelRow . ': ' . implode('; ', $reasons));
                continue;
            }

            $insertData[] = [
                'uuid' => (string) Str::uuid(),
                'id_plan' => $user->id_plan,
                'created_by' => $user->id,
                'shift_id' => $idShift,
                'tanggal' => $tanggal . ' ' . $jam . ':00',
                'jam' => $jam,
                'nama_produsen' => $namaProdusen,
                'kode_produksi' => $kodeProduksi,
                'best_before' => $bestBefore,
                'sampling_defect' => $samplingDefectNames ?: null,
                'sampling_defect_qty' => !empty($qtyMap) ? json_encode($qtyMap) : null,
                'total_defect' => $totalDefect > 0 ? (string)$totalDefect : null,
                'catatan' => $catatan ?: null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            // Note: Shoestring::insert doesn't handle JSON cast automatically on insert array
            // But usually string is fine for json column in raw insert
            Shoestring::insert($insertData);
            $this->parent->setInsertedCount(count($insertData));
        }
    }
}
