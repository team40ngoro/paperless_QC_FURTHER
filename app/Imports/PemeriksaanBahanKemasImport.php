<?php

namespace App\Imports;

use App\Models\DataShift;
use App\Models\PemeriksaanBahanKemas;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class PemeriksaanBahanKemasImport implements WithMultipleSheets
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
            'Template' => new PemeriksaanBahanKemasTemplateSheetImport($this),
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

class PemeriksaanBahanKemasTemplateSheetImport implements ToCollection, WithStartRow, WithCalculatedFormulas
{
    protected PemeriksaanBahanKemasImport $parent;

    public function __construct(PemeriksaanBahanKemasImport $parent)
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
            $namaKemasan = trim((string) ($row[3] ?? ''));
            $kodeProduksi = trim((string) ($row[4] ?? ''));
            $kondisiVal = trim((string) ($row[5] ?? ''));
            $keterangan = trim((string) ($row[6] ?? ''));

            if ($shiftVal === '' && $namaKemasan === '' && $kodeProduksi === '') {
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

            if ($namaKemasan === '') {
                $reasons[] = 'Nama Kemasan kosong (cek kolom D)';
            }
            if ($kodeProduksi === '') {
                $reasons[] = 'Kode Produksi kosong (cek kolom E)';
            }
            if (!in_array($kondisiVal, ['OK', 'Tidak OK'])) {
                $reasons[] = "Kondisi tidak valid: '{$kondisiVal}' (Harus OK atau Tidak OK)";
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
                'nama_kemasan' => $namaKemasan,
                'kode_produksi' => $kodeProduksi,
                'kondisi_bahan_kemasan' => $kondisiVal,
                'keterangan' => $keterangan ?: null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            foreach (array_chunk($insertData, 100) as $chunk) {
                PemeriksaanBahanKemas::insert($chunk);
            }
            $this->parent->setInsertedCount(count($insertData));
        }
    }
}
