<?php

namespace App\Exports;

use App\Models\DataShift;
use App\Models\DataDefect;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShoestringTemplateExport implements WithMultipleSheets
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sheets(): array
    {
        $shiftQuery = DataShift::query();
        $defectQuery = DataDefect::query();

        if ($this->user && $this->user->role !== 'superadmin') {
            $shiftQuery->where('id_plan', $this->user->id_plan);
            $defectQuery->where('id_plan', $this->user->id_plan);
        }

        $shiftList = $shiftQuery->orderBy('shift')->pluck('shift')->toArray();
        $defectList = $defectQuery->orderBy('nama_produsen')->pluck('nama_produsen')->toArray();

        $lastShiftRow = max(2, count($shiftList) + 1);
        $lastDefectRow = max(2, count($defectList) + 1);

        return [
            new ShoestringTemplateSheet($lastShiftRow, $lastDefectRow),
            new ShoestringMasterSheet($shiftList, $defectList),
        ];
    }
}

class ShoestringTemplateSheet implements FromArray, WithTitle, WithEvents
{
    protected int $lastShiftRow;
    protected int $lastDefectRow;

    public function __construct(int $lastShiftRow, int $lastDefectRow)
    {
        $this->lastShiftRow = $lastShiftRow;
        $this->lastDefectRow = $lastDefectRow;
    }

    public function title(): string
    {
        return 'Template';
    }

    public function array(): array
    {
        return [
            ['Shift', 'Tanggal', 'Jam', 'Nama Produsen', 'Kode Produksi', 'Best Before', 'Sampling Defect (Nama Defect)', 'Sampling Defect Qty (Format: Defect1:10, Defect2:5)', 'Catatan'],
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
                }

                $sheet->getStyle('B2:B500')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $sheet->getStyle('C2:C500')->getNumberFormat()->setFormatCode('hh:mm');
                $sheet->getStyle('F2:F500')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            },
        ];
    }
}

class ShoestringMasterSheet implements FromArray, WithTitle, WithEvents
{
    protected array $shiftList;
    protected array $defectList;

    public function __construct(array $shiftList, array $defectList)
    {
        $this->shiftList = $shiftList;
        $this->defectList = $defectList;
    }

    public function title(): string
    {
        return 'Master';
    }

    public function array(): array
    {
        $maxRows = max(
            count($this->shiftList),
            count($this->defectList)
        );

        $rows = [];
        $rows[] = ['Defect List', 'Shift'];

        for ($i = 0; $i < $maxRows; $i++) {
            $rows[] = [
                $this->defectList[$i] ?? null,
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
