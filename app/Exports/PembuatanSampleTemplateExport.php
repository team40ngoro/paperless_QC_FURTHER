<?php

namespace App\Exports;

use App\Models\DataShift;
use App\Models\JenisProduk;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PembuatanSampleTemplateExport implements WithMultipleSheets
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sheets(): array
    {
        $produkQuery = JenisProduk::query();
        $shiftQuery = DataShift::query();

        if ($this->user && $this->user->role !== 'superadmin') {
            $produkQuery->where('id_plan', $this->user->id_plan);
            $shiftQuery->where('id_plan', $this->user->id_plan);
        }

        $produkList = $produkQuery->orderBy('nama_produk')->pluck('nama_produk')->toArray();
        $shiftList = $shiftQuery->orderBy('shift')->pluck('shift')->toArray();

        $jenisSampleList = ['sample rnd', 'sample trial', 'sample retain'];
        $nilaiBeratList = ['-', 55, 100, 200, 225, 250, 300, 315, 400, 450, 500, 700, 900, 1000, 1100, 2000];

        $lastProdukRow = max(2, count($produkList) + 1);
        $lastShiftRow = max(2, count($shiftList) + 1);
        $lastJenisRow = max(2, count($jenisSampleList) + 1);
        $lastNilaiRow = max(2, count($nilaiBeratList) + 1);

        return [
            new PembuatanSampleTemplateSheet($lastProdukRow, $lastShiftRow, $lastJenisRow, $lastNilaiRow),
            new PembuatanSampleMasterSheet($produkList, $shiftList, $jenisSampleList, $nilaiBeratList),
        ];
    }
}

class PembuatanSampleTemplateSheet implements FromArray, WithTitle, WithEvents
{
    protected int $lastProdukRow;
    protected int $lastShiftRow;
    protected int $lastJenisRow;
    protected int $lastNilaiRow;

    public function __construct(int $lastProdukRow, int $lastShiftRow, int $lastJenisRow, int $lastNilaiRow)
    {
        $this->lastProdukRow = $lastProdukRow;
        $this->lastShiftRow = $lastShiftRow;
        $this->lastJenisRow = $lastJenisRow;
        $this->lastNilaiRow = $lastNilaiRow;
    }

    public function title(): string
    {
        return 'Template';
    }

    public function array(): array
    {
        return [
            ['Shift', 'Nama Produk', 'Berat Produk (gram)', 'Gramase (gram)', 'Kode Produksi', 'Tanggal Produksi', 'Jam', 'Tanggal Expired', 'Jumlah', 'Jenis Sample'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('F2', '=TODAY()');
                $sheet->setCellValue('G2', '=TIME(HOUR(NOW()),MINUTE(NOW()),0)');

                for ($row = 2; $row <= 500; $row++) {
                    if ($row > 2) {
                        $sheet->setCellValue('F' . $row, '=TODAY()');
                        $sheet->setCellValue('G' . $row, '=TIME(HOUR(NOW()),MINUTE(NOW()),0)');
                    }

                    $valShift = $sheet->getCell('A' . $row)->getDataValidation();
                    $valShift->setType(DataValidation::TYPE_LIST);
                    $valShift->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valShift->setAllowBlank(true);
                    $valShift->setShowInputMessage(true);
                    $valShift->setShowErrorMessage(true);
                    $valShift->setShowDropDown(true);
                    $valShift->setFormula1('=Master!$B$2:$B$' . $this->lastShiftRow);

                    $valProduk = $sheet->getCell('B' . $row)->getDataValidation();
                    $valProduk->setType(DataValidation::TYPE_LIST);
                    $valProduk->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valProduk->setAllowBlank(true);
                    $valProduk->setShowInputMessage(true);
                    $valProduk->setShowErrorMessage(true);
                    $valProduk->setShowDropDown(true);
                    $valProduk->setFormula1('=Master!$A$2:$A$' . $this->lastProdukRow);

                    $valBerat = $sheet->getCell('C' . $row)->getDataValidation();
                    $valBerat->setType(DataValidation::TYPE_LIST);
                    $valBerat->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valBerat->setAllowBlank(true);
                    $valBerat->setShowInputMessage(true);
                    $valBerat->setShowErrorMessage(true);
                    $valBerat->setShowDropDown(true);
                    $valBerat->setFormula1('=Master!$D$2:$D$' . $this->lastNilaiRow);

                    $valGramase = $sheet->getCell('D' . $row)->getDataValidation();
                    $valGramase->setType(DataValidation::TYPE_LIST);
                    $valGramase->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valGramase->setAllowBlank(true);
                    $valGramase->setShowInputMessage(true);
                    $valGramase->setShowErrorMessage(true);
                    $valGramase->setShowDropDown(true);
                    $valGramase->setFormula1('=Master!$D$2:$D$' . $this->lastNilaiRow);

                    $valJenis = $sheet->getCell('J' . $row)->getDataValidation();
                    $valJenis->setType(DataValidation::TYPE_LIST);
                    $valJenis->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valJenis->setAllowBlank(true);
                    $valJenis->setShowInputMessage(true);
                    $valJenis->setShowErrorMessage(true);
                    $valJenis->setShowDropDown(true);
                    $valJenis->setFormula1('=Master!$C$2:$C$' . $this->lastJenisRow);
                }

                $sheet->getStyle('F2:F500')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $sheet->getStyle('G2:G500')->getNumberFormat()->setFormatCode('hh:mm');
                $sheet->getStyle('F2')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                $sheet->getStyle('G2')->getNumberFormat()->setFormatCode('hh:mm');
                $sheet->getStyle('F2')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $sheet->getStyle('G2')->getNumberFormat()->setFormatCode('hh:mm');
            },
        ];
    }
}

class PembuatanSampleMasterSheet implements FromArray, WithTitle, WithEvents
{
    protected array $produkList;
    protected array $shiftList;
    protected array $jenisSampleList;
    protected array $nilaiBeratList;

    public function __construct(array $produkList, array $shiftList, array $jenisSampleList, array $nilaiBeratList)
    {
        $this->produkList = $produkList;
        $this->shiftList = $shiftList;
        $this->jenisSampleList = $jenisSampleList;
        $this->nilaiBeratList = $nilaiBeratList;
    }

    public function title(): string
    {
        return 'Master';
    }

    public function array(): array
    {
        $maxRows = max(
            count($this->produkList),
            count($this->shiftList),
            count($this->jenisSampleList),
            count($this->nilaiBeratList)
        );

        $rows = [];
        $rows[] = ['Produk', 'Shift', 'Jenis Sample', 'Nilai Berat / Gramase'];

        for ($i = 0; $i < $maxRows; $i++) {
            $rows[] = [
                $this->produkList[$i] ?? null,
                $this->shiftList[$i] ?? null,
                $this->jenisSampleList[$i] ?? null,
                $this->nilaiBeratList[$i] ?? null,
            ];
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
            },
        ];
    }
}
