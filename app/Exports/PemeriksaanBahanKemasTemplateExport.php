<?php

namespace App\Exports;

use App\Models\DataShift;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PemeriksaanBahanKemasTemplateExport implements WithMultipleSheets
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sheets(): array
    {
        $shiftQuery = DataShift::query();

        if ($this->user && $this->user->role !== 'superadmin') {
            $shiftQuery->where('id_plan', $this->user->id_plan);
        }

        $shiftList = $shiftQuery->orderBy('shift')->pluck('shift')->toArray();
        $kondisiList = ['OK', 'Tidak OK'];

        $lastShiftRow = max(2, count($shiftList) + 1);
        $lastKondisiRow = max(2, count($kondisiList) + 1);

        return [
            new PemeriksaanBahanKemasTemplateSheet($lastShiftRow, $lastKondisiRow),
            new PemeriksaanBahanKemasMasterSheet($shiftList, $kondisiList),
        ];
    }
}

class PemeriksaanBahanKemasTemplateSheet implements FromArray, WithTitle, WithEvents
{
    protected int $lastShiftRow;
    protected int $lastKondisiRow;

    public function __construct(int $lastShiftRow, int $lastKondisiRow)
    {
        $this->lastShiftRow = $lastShiftRow;
        $this->lastKondisiRow = $lastKondisiRow;
    }

    public function title(): string
    {
        return 'Template';
    }

    public function array(): array
    {
        return [
            ['Shift', 'Tanggal', 'Jam', 'Nama Kemasan', 'Kode Produksi', 'Kondisi Bahan Kemasan (OK/Tidak OK)', 'Keterangan'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('B2', '=TODAY()');
                $sheet->setCellValue('C2', '=TIME(HOUR(NOW()),MINUTE(NOW()),0)');

                for ($row = 2; $row <= 500; $row++) {
                    if ($row > 2) {
                        $sheet->setCellValue('B' . $row, '=TODAY()');
                        $sheet->setCellValue('C' . $row, '=TIME(HOUR(NOW()),MINUTE(NOW()),0)');
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

                    // Kondisi Validation
                    $valKondisi = $sheet->getCell('F' . $row)->getDataValidation();
                    $valKondisi->setType(DataValidation::TYPE_LIST);
                    $valKondisi->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valKondisi->setAllowBlank(true);
                    $valKondisi->setShowInputMessage(true);
                    $valKondisi->setShowErrorMessage(true);
                    $valKondisi->setShowDropDown(true);
                    $valKondisi->setFormula1('=Master!$A$2:$A$' . $this->lastKondisiRow);
                }

                $sheet->getStyle('B2:B500')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $sheet->getStyle('C2:C500')->getNumberFormat()->setFormatCode('hh:mm');
            },
        ];
    }
}

class PemeriksaanBahanKemasMasterSheet implements FromArray, WithTitle, WithEvents
{
    protected array $shiftList;
    protected array $kondisiList;

    public function __construct(array $shiftList, array $kondisiList)
    {
        $this->shiftList = $shiftList;
        $this->kondisiList = $kondisiList;
    }

    public function title(): string
    {
        return 'Master';
    }

    public function array(): array
    {
        $maxRows = max(
            count($this->shiftList),
            count($this->kondisiList)
        );

        $rows = [];
        $rows[] = ['Kondisi', 'Shift'];

        for ($i = 0; $i < $maxRows; $i++) {
            $rows[] = [
                $this->kondisiList[$i] ?? null,
                $this->shiftList[$i] ?? null,
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
