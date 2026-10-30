<?php

namespace App\Imports;

use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\PembuatanSample;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class PembuatanSampleImport implements WithMultipleSheets
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
            'Template' => new PembuatanSampleTemplateSheetImport($this),
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

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        if (!$this->user || !$this->user->id_plan) {
            $this->importErrors[] = 'Plan user tidak ditemukan. Tidak dapat melakukan import.';
            return;
        }

        $produkMap = JenisProduk::where('id_plan', $this->user->id_plan)
            ->pluck('id', 'nama_produk')
            ->toArray();

        $shiftMap = DataShift::where('id_plan', $this->user->id_plan)
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

            if (is_numeric($value)) {
                try {
                    return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            }

            $valueStr = trim((string) $value);
            $valueStr = preg_replace('/\s+/', ' ', $valueStr);
            $valueStr = str_replace('.', '/', $valueStr);

            if (preg_match_all('/(\d{4})年(\d{1,2})月(\d{1,2})日/u', $valueStr, $matches, PREG_SET_ORDER)) {
                $last = end($matches);
                $y = (int) ($last[1] ?? 0);
                $mo = (int) ($last[2] ?? 0);
                $d = (int) ($last[3] ?? 0);
                if ($y >= 1900 && $mo >= 1 && $mo <= 12 && $d >= 1 && $d <= 31) {
                    try {
                        return Carbon::create($y, $mo, $d)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // continue
                    }
                }
            }

            $valueStr = preg_replace('/[^0-9\-\/\:\s]/u', '', $valueStr);
            if ($valueStr === '') {
                return null;
            }

            $digitsOnly = preg_replace('/\D/', '', $valueStr);
            if (strlen($digitsOnly) >= 8) {
                $candidates = [];
                $candidates[] = substr($digitsOnly, -8);
                if (strlen($digitsOnly) === 8) {
                    $candidates[] = $digitsOnly;
                }

                foreach (array_unique($candidates) as $cand) {
                    try {
                        return Carbon::createFromFormat('Ymd', $cand)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // continue
                    }
                }
            }

            $valueStrFirstToken = explode(' ', $valueStr)[0] ?? $valueStr;

            $formats = [
                'd/m/Y',
                'd-m-Y',
                'd.m.Y',
                'Y-m-d',
                'Y.m.d',
                'd/m/y',
                'd-m-y',
                'd.m.y',
                'd/m/Y H:i',
                'd/m/Y H:i:s',
                'd-m-Y H:i',
                'd-m-Y H:i:s',
                'Y-m-d H:i',
                'Y-m-d H:i:s',
            ];

            foreach ($formats as $fmt) {
                try {
                    return Carbon::createFromFormat($fmt, $valueStr)->format('Y-m-d');
                } catch (\Exception $e) {
                    // continue
                }

                if ($valueStrFirstToken !== $valueStr) {
                    try {
                        return Carbon::createFromFormat($fmt, $valueStrFirstToken)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // continue
                    }
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
            $namaProduk = trim((string) ($row[1] ?? ''));
            $berat = trim((string) ($row[2] ?? ''));
            $beratSampling = trim((string) ($row[3] ?? ''));
            $kodeProduksi = trim((string) ($row[4] ?? ''));
            $tanggalVal = $row[5] ?? null;
            $jamVal = $row[6] ?? null;
            $tanggalExpiredVal = $row[7] ?? null;
            $jumlahVal = $row[8] ?? null;
            $jenisSample = trim((string) ($row[9] ?? ''));

            if ($shiftVal === '' && $namaProduk === '' && $kodeProduksi === '') {
                continue;
            }

            $excelRow = $i + 2;
            $reasons = [];

            $shiftKey = is_numeric($shiftVal) ? (int) $shiftVal : $shiftVal;
            $idShift = $shiftMap[$shiftKey] ?? null;
            if (!$idShift) {
                $reasons[] = "Shift tidak ditemukan: '{$shiftVal}'";
            }

            $idProduk = $produkMap[$namaProduk] ?? null;
            if (!$idProduk) {
                $reasons[] = "Produk tidak ditemukan: '{$namaProduk}'";
            }

            if ($kodeProduksi === '') {
                $reasons[] = 'Kode Produksi kosong (cek kolom E)';
            }

            $tanggal = $parseDateCell($tanggalVal);
            if (!$tanggal) {
                $tanggal = now()->format('Y-m-d');
            }

            $jam = null;
            if ($jamVal !== null && $jamVal !== '') {
                if (is_numeric($jamVal)) {
                    try {
                        $jam = ExcelDate::excelToDateTimeObject($jamVal)->format('H:i');
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

            $tanggalExpired = $parseDateCell($tanggalExpiredVal);
            if (!$tanggalExpired) {
                $rawExpired = null;
                if ($tanggalExpiredVal instanceof RichText) {
                    $rawExpired = $tanggalExpiredVal->getPlainText();
                } elseif ($tanggalExpiredVal instanceof \DateTimeInterface) {
                    $rawExpired = Carbon::instance($tanggalExpiredVal)->toDateTimeString();
                } elseif (is_scalar($tanggalExpiredVal)) {
                    $rawExpired = (string) $tanggalExpiredVal;
                } else {
                    $rawExpired = gettype($tanggalExpiredVal);
                }
                $reasons[] = 'Tanggal Expired tidak valid/kosong (cek kolom H). Nilai: ' . $rawExpired;
            }

            $beratNormalized = ($berat === '' || $berat === '-') ? null : (float) str_replace(',', '.', $berat);
            $beratSamplingNormalized = ($beratSampling === '' || $beratSampling === '-') ? null : (float) str_replace(',', '.', $beratSampling);
            $jumlahNormalized = ($jumlahVal === '' || $jumlahVal === null) ? null : (int) $jumlahVal;

            if ($beratNormalized === null) {
                $reasons[] = 'Berat Produk kosong/tidak valid (cek kolom C)';
            }

            if (!$jumlahNormalized) {
                $reasons[] = 'Jumlah kosong/tidak valid (cek kolom I)';
            }

            if ($jenisSample === '') {
                $reasons[] = 'Jenis Sample kosong (cek kolom J)';
            }

            if ($kodeProduksi === '' && is_string($tanggalExpiredVal) && $tanggalExpiredVal !== '' && $tanggalExpired === null) {
                $reasons[] = 'Kemungkinan kolom bergeser (Kode Produksi terisi di kolom H/Tanggal Expired)';
            }

            if (!empty($reasons)) {
                $this->importErrors[] = 'Row ' . $excelRow . ': ' . implode('; ', $reasons);
                continue;
            }

            $insertData[] = [
                'uuid' => (string) Str::uuid(),
                'id_produk' => $idProduk,
                'id_plan' => $this->user->id_plan,
                'id_shift' => $idShift,
                'kode_produksi' => $kodeProduksi,
                'tanggal' => $tanggal . ' ' . Carbon::parse($jam)->format('H:i:s'),
                'jam' => Carbon::parse($jam)->format('H:i:s'),
                'tanggal_expired' => $tanggalExpired,
                'jumlah' => $jumlahNormalized,
                'berat' => $beratNormalized,
                'berat_sampling' => $beratSamplingNormalized,
                'jenis_sample' => $jenisSample,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            PembuatanSample::insert($insertData);
            $this->insertedCount = count($insertData);
        }
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

class PembuatanSampleTemplateSheetImport implements ToCollection, WithStartRow
{
    protected PembuatanSampleImport $parent;

    public function __construct(PembuatanSampleImport $parent)
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

        $produkMap = JenisProduk::where('id_plan', $user->id_plan)
            ->pluck('id', 'nama_produk')
            ->toArray();

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

            if (is_numeric($value)) {
                try {
                    return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            }

            $valueStr = trim((string) $value);
            $valueStr = preg_replace('/\s+/', ' ', $valueStr);
            $valueStr = str_replace('.', '/', $valueStr);

            if (preg_match_all('/(\d{4})年(\d{1,2})月(\d{1,2})日/u', $valueStr, $matches, PREG_SET_ORDER)) {
                $last = end($matches);
                $y = (int) ($last[1] ?? 0);
                $mo = (int) ($last[2] ?? 0);
                $d = (int) ($last[3] ?? 0);
                if ($y >= 1900 && $mo >= 1 && $mo <= 12 && $d >= 1 && $d <= 31) {
                    try {
                        return Carbon::create($y, $mo, $d)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // continue
                    }
                }
            }

            $valueStr = preg_replace('/[^0-9\-\/\:\s]/u', '', $valueStr);
            if ($valueStr === '') {
                return null;
            }

            $digitsOnly = preg_replace('/\D/', '', $valueStr);
            if (strlen($digitsOnly) >= 8) {
                $candidates = [];
                $candidates[] = substr($digitsOnly, -8);
                if (strlen($digitsOnly) === 8) {
                    $candidates[] = $digitsOnly;
                }

                foreach (array_unique($candidates) as $cand) {
                    try {
                        return Carbon::createFromFormat('Ymd', $cand)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // continue
                    }
                }
            }

            $valueStrFirstToken = explode(' ', $valueStr)[0] ?? $valueStr;

            $formats = [
                'd/m/Y',
                'd-m-Y',
                'd.m.Y',
                'Y-m-d',
                'Y.m.d',
                'd/m/y',
                'd-m-y',
                'd.m.y',
                'd/m/Y H:i',
                'd/m/Y H:i:s',
                'd-m-Y H:i',
                'd-m-Y H:i:s',
                'Y-m-d H:i',
                'Y-m-d H:i:s',
            ];

            foreach ($formats as $fmt) {
                try {
                    return Carbon::createFromFormat($fmt, $valueStr)->format('Y-m-d');
                } catch (\Exception $e) {
                    // continue
                }

                if ($valueStrFirstToken !== $valueStr) {
                    try {
                        return Carbon::createFromFormat($fmt, $valueStrFirstToken)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // continue
                    }
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
            $namaProduk = trim((string) ($row[1] ?? ''));
            $berat = trim((string) ($row[2] ?? ''));
            $beratSampling = trim((string) ($row[3] ?? ''));
            $kodeProduksi = trim((string) ($row[4] ?? ''));
            $tanggalVal = $row[5] ?? null;
            $jamVal = $row[6] ?? null;
            $tanggalExpiredVal = $row[7] ?? null;
            $jumlahVal = $row[8] ?? null;
            $jenisSample = trim((string) ($row[9] ?? ''));

            if ($shiftVal === '' && $namaProduk === '' && $kodeProduksi === '') {
                continue;
            }

            $excelRow = $i + 2;
            $reasons = [];

            $shiftKey = is_numeric($shiftVal) ? (int) $shiftVal : $shiftVal;
            $idShift = $shiftMap[$shiftKey] ?? null;
            if (!$idShift) {
                $reasons[] = "Shift tidak ditemukan: '{$shiftVal}'";
            }

            $idProduk = $produkMap[$namaProduk] ?? null;
            if (!$idProduk) {
                $reasons[] = "Produk tidak ditemukan: '{$namaProduk}'";
            }

            if ($kodeProduksi === '') {
                $reasons[] = 'Kode Produksi kosong (cek kolom E)';
            }

            $tanggal = $parseDateCell($tanggalVal);
            if (!$tanggal) {
                $tanggal = now()->format('Y-m-d');
            }

            $jam = null;
            if ($jamVal !== null && $jamVal !== '') {
                if (is_numeric($jamVal)) {
                    try {
                        $jam = ExcelDate::excelToDateTimeObject($jamVal)->format('H:i');
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

            $tanggalExpired = $parseDateCell($tanggalExpiredVal);
            if (!$tanggalExpired) {
                $rawExpired = null;
                if ($tanggalExpiredVal instanceof RichText) {
                    $rawExpired = $tanggalExpiredVal->getPlainText();
                } elseif ($tanggalExpiredVal instanceof \DateTimeInterface) {
                    $rawExpired = Carbon::instance($tanggalExpiredVal)->toDateTimeString();
                } elseif (is_scalar($tanggalExpiredVal)) {
                    $rawExpired = (string) $tanggalExpiredVal;
                } else {
                    $rawExpired = gettype($tanggalExpiredVal);
                }
                $reasons[] = 'Tanggal Expired tidak valid/kosong (cek kolom H). Nilai: ' . $rawExpired;
            }

            $beratNormalized = ($berat === '' || $berat === '-') ? null : (float) str_replace(',', '.', $berat);
            $beratSamplingNormalized = ($beratSampling === '' || $beratSampling === '-') ? null : (float) str_replace(',', '.', $beratSampling);
            $jumlahNormalized = ($jumlahVal === '' || $jumlahVal === null) ? null : (int) $jumlahVal;

            if ($beratNormalized === null) {
                $reasons[] = 'Berat Produk kosong/tidak valid (cek kolom C)';
            }

            if (!$jumlahNormalized) {
                $reasons[] = 'Jumlah kosong/tidak valid (cek kolom I)';
            }

            if ($jenisSample === '') {
                $reasons[] = 'Jenis Sample kosong (cek kolom J)';
            }

            if ($kodeProduksi === '' && is_string($tanggalExpiredVal) && $tanggalExpiredVal !== '' && $tanggalExpired === null) {
                $reasons[] = 'Kemungkinan kolom bergeser (Kode Produksi terisi di kolom H/Tanggal Expired)';
            }

            if (!empty($reasons)) {
                $this->parent->addImportError('Row ' . $excelRow . ': ' . implode('; ', $reasons));
                continue;
            }

            $insertData[] = [
                'uuid' => (string) Str::uuid(),
                'id_produk' => $idProduk,
                'id_plan' => $user->id_plan,
                'created_by' => $user->id,
                'id_shift' => $idShift,
                'kode_produksi' => $kodeProduksi,
                'tanggal' => $tanggal . ' ' . Carbon::parse($jam)->format('H:i:s'),
                'jam' => Carbon::parse($jam)->format('H:i:s'),
                'tanggal_expired' => $tanggalExpired,
                'jumlah' => $jumlahNormalized,
                'berat' => $beratNormalized,
                'berat_sampling' => $beratSamplingNormalized,
                'jenis_sample' => $jenisSample,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            PembuatanSample::insert($insertData);
            $this->parent->setInsertedCount(count($insertData));
        }
    }
}
