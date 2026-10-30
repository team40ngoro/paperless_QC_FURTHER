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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReboxTemplateExport implements WithMultipleSheets
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sheets(): array
    {
        $shiftQuery = DataShift::query();
        $produkQuery = JenisProduk::query();

        if ($this->user && $this->user->role !== 'superadmin') {
            $shiftQuery->where('id_plan', $this->user->id_plan);
            $produkQuery->where('id_plan', $this->user->id_plan);
        }

        $shiftList = $shiftQuery->orderBy('shift')->pluck('shift')->toArray();
        $produkList = $produkQuery->orderBy('nama_produk')->pluck('nama_produk')->toArray();
        $labelisasiList = ['✔', '✘'];

        $lastShiftRow = max(2, count($shiftList) + 1);
        $lastProdukRow = max(2, count($produkList) + 1);
        $lastLabelRow = max(2, count($labelisasiList) + 1);

        return [
            new ReboxTemplateSheet($lastShiftRow, $lastProdukRow, $lastLabelRow),
            new ReboxMasterSheet($shiftList, $produkList, $labelisasiList),
        ];
    }
}

class ReboxTemplateSheet implements FromArray, WithTitle, WithEvents
{
    protected int $lastShiftRow;
    protected int $lastProdukRow;
    protected int $lastLabelRow;

    public function __construct(int $lastShiftRow, int $lastProdukRow, int $lastLabelRow)
    {
        $this->lastShiftRow = $lastShiftRow;
        $this->lastProdukRow = $lastProdukRow;
        $this->lastLabelRow = $lastLabelRow;
    }

    public function title(): string
    {
        return 'Template';
    }

    public function array(): array
    {
        return [
            ['Shift', 'Tanggal', 'Jam', 'Nama Produk', 'Kode Produksi', 'Best Before', 'Isi/Jumlah', 'Labelisasi (✔/✘)'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('B2', '=TODAY()');
                $sheet->setCellValue('C2', '=TIME(HOUR(NOW()),MINUTE(NOW()),0)');
                $sheet->setCellValue('F2', '=TODAY()+365');

                for ($row = 2; $row <= 500; $row++) {
                    if ($row > 2) {
                        $sheet->setCellValue('B' . $row, '=TODAY()');
                        $sheet->setCellValue('C' . $row, '=TIME(HOUR(NOW()),MINUTE(NOW()),0)');
                        $sheet->setCellValue('F' . $row, '=F' . ($row-1));
                    }

                    // Shift Validation
                    $valShift = $sheet->getCell('A' . $row)->getDataValidation();
                    $valShift->setType(DataValidation::TYPE_LIST);
                    $valShift->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valShift->setAllowBlank(true);
                    $valShift->setShowInputMessage(true);
                    $valShift->setShowErrorMessage(true);
                    $valShift->setShowDropDown(true);
                    $valShift->setFormula1('=Master!$B$2:$B$' . $this->lastShiftRow);

                    // Produk Validation
                    $valProduk = $sheet->getCell('D' . $row)->getDataValidation();
                    $valProduk->setType(DataValidation::TYPE_LIST);
                    $valProduk->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valProduk->setAllowBlank(true);
                    $valProduk->setShowInputMessage(true);
                    $valProduk->setShowErrorMessage(true);
                    $valProduk->setShowDropDown(true);
                    $valProduk->setFormula1('=Master!$A$2:$A$' . $this->lastProdukRow);

                    // Isi & Jumlah Validation
                    $valIsi = $sheet->getCell('G' . $row)->getDataValidation();
                    $valIsi->setType(DataValidation::TYPE_LIST);
                    $valIsi->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valIsi->setAllowBlank(true);
                    $valIsi->setShowInputMessage(true);
                    $valIsi->setShowErrorMessage(true);
                    $valIsi->setShowDropDown(true);
                    $valIsi->setFormula1('=Master!$C$2:$C$' . $this->lastLabelRow);

                    // Labelisasi Validation
                    $valLabel = $sheet->getCell('H' . $row)->getDataValidation();
                    $valLabel->setType(DataValidation::TYPE_LIST);
                    $valLabel->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valLabel->setAllowBlank(true);
                    $valLabel->setShowInputMessage(true);
                    $valLabel->setShowErrorMessage(true);
                    $valLabel->setShowDropDown(true);
                    $valLabel->setFormula1('=Master!$C$2:$C$' . $this->lastLabelRow);
                }

                $sheet->getStyle('B2:B500')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $sheet->getStyle('C2:C500')->getNumberFormat()->setFormatCode('hh:mm');
                $sheet->getStyle('F2:F500')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            },
        ];
    }
}

class ReboxMasterSheet implements FromArray, WithTitle, WithEvents
{
    protected array $shiftList;
    protected array $produkList;
    protected array $labelList;

    public function __construct(array $shiftList, array $produkList, array $labelList)
    {
        $this->shiftList = $shiftList;
        $this->produkList = $produkList;
        $this->labelList = $labelList;
    }

    public function title(): string
    {
        return 'Master';
    }

    public function array(): array
    {
        $maxRows = max(
            count($this->shiftList),
            count($this->produkList),
            count($this->labelList)
        );

        $rows = [];
        $rows[] = ['Produk', 'Shift', 'Labelisasi'];

        for ($i = 0; $i < $maxRows; $i++) {
            $rows[] = [
                $this->produkList[$i] ?? null,
                $this->shiftList[$i] ?? null,
                $this->labelList[$i] ?? null,
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
