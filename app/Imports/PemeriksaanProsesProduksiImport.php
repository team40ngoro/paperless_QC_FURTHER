<?php

namespace App\Imports;

use App\Models\DataShift;
use App\Models\InputArea;
use App\Models\PemeriksaanProsesProduksi;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class PemeriksaanProsesProduksiImport implements WithMultipleSheets
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
            'Template' => new PemeriksaanProsesProduksiTemplateSheetImport($this),
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

class PemeriksaanProsesProduksiTemplateSheetImport implements ToCollection, WithStartRow, WithCalculatedFormulas
{
    protected PemeriksaanProsesProduksiImport $parent;

    public function __construct(PemeriksaanProsesProduksiImport $parent)
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
            
        $areaMap = InputArea::where('id_plan', $user->id_plan)
            ->pluck('id', 'area')
            ->toArray();

        $ketOptions = PemeriksaanProsesProduksi::getKetidaksesuaianOptions();
        $dispOptions = PemeriksaanProsesProduksi::getDisposisiOptions();
        
        // reverse map for lookup
        $ketMap = array_flip($ketOptions);
        $dispMap = array_flip($dispOptions);

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

            $valueStr = trim((string) $value);
            if (empty($valueStr)) return null;

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
            $areaVal = trim((string) ($row[1] ?? ''));
            $tanggalVal = $row[2] ?? null;
            $jamVal = $row[3] ?? null;
            $ketLabel = trim((string) ($row[4] ?? ''));
            $uraian = trim((string) ($row[5] ?? ''));
            $analisa = trim((string) ($row[6] ?? ''));
            $dispLabel = trim((string) ($row[7] ?? ''));
            $tindakan = trim((string) ($row[8] ?? ''));

            if ($shiftVal === '' && $areaVal === '' && $uraian === '') {
                continue;
            }

            $excelRow = $i + 2;
            $reasons = [];

            // Mapping Shift
            $shiftKey = is_numeric($shiftVal) ? (int) $shiftVal : $shiftVal;
            $idShift = $shiftMap[$shiftKey] ?? null;
            if (!$idShift) {
                $reasons[] = "Shift tidak ditemukan: '{$shiftVal}'";
            }

            // Mapping Area
            $idArea = $areaMap[$areaVal] ?? null;
            if (!$idArea) {
                $reasons[] = "Area tidak ditemukan: '{$areaVal}'";
            }

            // Mapping Ketidaksesuaian
            $ketValue = $ketMap[$ketLabel] ?? null;
            if (!$ketValue) {
                $reasons[] = "Ketidaksesuaian tidak valid: '{$ketLabel}'";
            }

            // Mapping Disposisi
            $dispValue = $dispMap[$dispLabel] ?? null;
            if (!$dispValue) {
                $reasons[] = "Disposisi tidak valid: '{$dispLabel}'";
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

            if ($uraian === '') $reasons[] = 'Uraian Permasalahan kosong (Baris kolom F)';
            if ($analisa === '') $reasons[] = 'Analisa Penyebab kosong (Baris kolom G)';
            if ($tindakan === '') $reasons[] = 'Tindakan Koreksi kosong (Baris kolom I)';

            if (!empty($reasons)) {
                $this->parent->addImportError('Row ' . $excelRow . ': ' . implode('; ', $reasons));
                continue;
            }

            $insertData[] = [
                'uuid' => (string) Str::uuid(),
                'id_plan' => $user->id_plan,
                'user_id' => $user->id,
                'shift_id' => $idShift,
                'id_area' => $idArea,
                'tanggal' => $tanggal . ' ' . $jam . ':00',
                'jam' => $jam,
                'ketidaksesuaian' => $ketValue,
                'uraian_permasalahan' => $uraian,
                'analisa_penyebab' => $analisa,
                'disposisi' => $dispValue,
                'tindakan_koreksi' => $tindakan,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            foreach (array_chunk($insertData, 100) as $chunk) {
                PemeriksaanProsesProduksi::insert($chunk);
            }
            $this->parent->setInsertedCount(count($insertData));
        }
    }
}
