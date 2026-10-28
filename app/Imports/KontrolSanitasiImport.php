<?php

namespace App\Imports;

use App\Models\DataShift;
use App\Models\KontrolSanitasi;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class KontrolSanitasiImport implements WithMultipleSheets
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
            'Template' => new KontrolSanitasiTemplateSheetImport($this),
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

class KontrolSanitasiTemplateSheetImport implements ToCollection, WithStartRow, WithCalculatedFormulas
{
    protected KontrolSanitasiImport $parent;

    public function __construct(KontrolSanitasiImport $parent)
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
                'd-m-Y', 'd/m/Y', 'd.m.Y', 
                'Y-m-d', 'Y/m/d', 'Y.m.d',
                'd-m-y', 'd/m/y'
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
            $suhuAir = trim((string) ($row[3] ?? ''));
            $klorinFood = trim((string) ($row[4] ?? ''));
            $klorinHand = trim((string) ($row[5] ?? ''));
            $hasilVerif = trim((string) ($row[6] ?? ''));

            if ($shiftVal === '' && $suhuAir === '' && $hasilVerif === '') {
                continue;
            }

            $excelRow = $i + 2;
            $reasons = [];

            // Shift Mapping
            $idShift = $shiftMap[$shiftVal] ?? null;
            if (!$idShift) {
                $reasons[] = "Shift tidak ditemukan: '{$shiftVal}'";
            }

            if ($suhuAir === '') $reasons[] = 'Suhu Air kosong';
            if ($hasilVerif === '') $reasons[] = 'Hasil Verifikasi kosong';

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

            if (!empty($reasons)) {
                $this->parent->addImportError('Row ' . $excelRow . ': ' . implode('; ', $reasons));
                continue;
            }

            $insertData[] = [
                'uuid' => (string) Str::uuid(),
                'id_plan' => $user->id_plan,
                'user_id' => $user->id,
                'shift_id' => $idShift,
                'tanggal' => $tanggal,
                'jam' => $jam,
                'suhu_air' => $suhuAir,
                'kadar_klorin_food_basin' => $klorinFood,
                'kadar_klorin_hand_basin' => $klorinHand,
                'hasil_verifikasi' => $hasilVerif,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            foreach (array_chunk($insertData, 100) as $chunk) {
                KontrolSanitasi::insert($chunk);
            }
            $this->parent->setInsertedCount(count($insertData));
        }
    }
}
