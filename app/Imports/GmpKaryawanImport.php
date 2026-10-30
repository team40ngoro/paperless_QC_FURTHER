<?php

namespace App\Imports;

use App\Models\DataShift;
use App\Models\InputArea;
use App\Models\GmpKaryawan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class GmpKaryawanImport implements WithMultipleSheets
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
            'Template' => new GmpKaryawanTemplateSheetImport($this),
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

class GmpKaryawanTemplateSheetImport implements ToCollection, WithStartRow, WithCalculatedFormulas
{
    protected GmpKaryawanImport $parent;

    public function __construct(GmpKaryawanImport $parent)
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

        $temuanOptions = GmpKaryawan::getTemuanOptions();
        $temuanMap = array_flip($temuanOptions);

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
            $areaVal = trim((string) ($row[1] ?? ''));
            $tanggalVal = $row[2] ?? null;
            $jamVal = $row[3] ?? null;
            $namaKaryawan = trim((string) ($row[4] ?? ''));
            $temuanLabel = trim((string) ($row[5] ?? ''));
            $keterangan = trim((string) ($row[6] ?? ''));
            $tindakan = trim((string) ($row[7] ?? ''));
            $verifVal = strtolower(trim((string) ($row[8] ?? '')));

            if ($shiftVal === '' && $areaVal === '' && $namaKaryawan === '') {
                continue;
            }

            $excelRow = $i + 2;
            $reasons = [];

            // Shift Mapping
            $idShift = $shiftMap[$shiftVal] ?? null;
            if (!$idShift) {
                $reasons[] = "Shift tidak ditemukan: '{$shiftVal}'";
            }

            // Area Mapping
            $idArea = $areaMap[$areaVal] ?? null;
            if (!$idArea) {
                $reasons[] = "Area tidak ditemukan: '{$areaVal}'";
            }

            // Temuan Mapping
            $temuanKey = $temuanMap[$temuanLabel] ?? null;
            if (!$temuanKey) {
                $reasons[] = "Temuan tidak valid: '{$temuanLabel}'";
            }

            if ($namaKaryawan === '') $reasons[] = 'Nama Karyawan kosong (Baris kolom E)';
            if ($verifVal === '') $reasons[] = 'Verifikasi kosong (Baris kolom I)';
            if ($verifVal !== 'ok' && $verifVal !== 'tidak_ok') {
                $reasons[] = "Verifikasi harus 'ok' atau 'tidak_ok': '{$verifVal}'";
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
                'nama_karyawan' => $namaKaryawan,
                'temuan_ketidaksesuaian' => $temuanKey,
                'keterangan' => $keterangan,
                'tindakan_koreksi' => $tindakan,
                'verifikasi' => $verifVal,
                'koreksi_lanjutan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            foreach (array_chunk($insertData, 100) as $chunk) {
                GmpKaryawan::insert($chunk);
            }
            $this->parent->setInsertedCount(count($insertData));
        }
    }
}
