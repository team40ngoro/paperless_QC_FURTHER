<?php

namespace App\Exports;

use App\Models\DataShift;
use App\Models\InputArea;
use App\Models\PemeriksaanProsesProduksi;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PemeriksaanProsesProduksiTemplateExport implements WithMultipleSheets
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sheets(): array
    {
        $shiftQuery = DataShift::query();
        $areaQuery = InputArea::query();

        if ($this->user && $this->user->role !== 'superadmin') {
            $shiftQuery->where('id_plan', $this->user->id_plan);
            $areaQuery->where('id_plan', $this->user->id_plan);
        }

        $shiftList = $shiftQuery->orderBy('shift')->pluck('shift')->toArray();
        $areaList = $areaQuery->orderBy('area')->pluck('area')->toArray();
        $ketidaksesuaianOptions = PemeriksaanProsesProduksi::getKetidaksesuaianOptions();
        $disposisiOptions = PemeriksaanProsesProduksi::getDisposisiOptions();

        return [
            new PemeriksaanProsesProduksiTemplateSheet(
                count($shiftList),
                count($areaList),
                count($ketidaksesuaianOptions),
                count($disposisiOptions)
            ),
            new PemeriksaanProsesProduksiMasterSheet($shiftList, $areaList, $ketidaksesuaianOptions, $disposisiOptions),
        ];
    }
}

class PemeriksaanProsesProduksiTemplateSheet implements FromArray, WithTitle, WithEvents
{
    protected int $shiftCount;
    protected int $areaCount;
    protected int $ketidaksesuaianCount;
    protected int $disposisiCount;

    public function __construct($shiftCount, $areaCount, $ketidaksesuaianCount, $disposisiCount)
    {
        $this->shiftCount = $shiftCount;
        $this->areaCount = $areaCount;
        $this->ketidaksesuaianCount = $ketidaksesuaianCount;
        $this->disposisiCount = $disposisiCount;
    }

    public function title(): string
    {
        return 'Template';
    }

    public function array(): array
    {
        return [
            [
                'Shift', 
                'Area', 
                'Tanggal', 
                'Jam', 
                'Ketidaksesuaian', 
                'Uraian Permasalahan', 
                'Analisa Penyebab', 
                'Disposisi', 
                'Tindakan Koreksi'
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('C2', '=TODAY()');
                $sheet->setCellValue('D2', '=TIME(HOUR(NOW()),MINUTE(NOW()),0)');

                for ($row = 2; $row <= 500; $row++) {
                    if ($row > 2) {
                        $sheet->setCellValue('C' . $row, '=TODAY()');
                        $sheet->setCellValue('D' . $row, '=TIME(HOUR(NOW()),MINUTE(NOW()),0)');
                    }

                    // Shift Validation (Column A)
                    $valShift = $sheet->getCell('A' . $row)->getDataValidation();
                    $valShift->setType(DataValidation::TYPE_LIST);
                    $valShift->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valShift->setAllowBlank(true);
                    $valShift->setShowInputMessage(true);
                    $valShift->setShowErrorMessage(true);
                    $valShift->setShowDropDown(true);
                    $valShift->setFormula1('=Master!$A$2:$A$' . ($this->shiftCount + 1));

                    // Area Validation (Column B)
                    $valArea = $sheet->getCell('B' . $row)->getDataValidation();
                    $valArea->setType(DataValidation::TYPE_LIST);
                    $valArea->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valArea->setAllowBlank(true);
                    $valArea->setShowInputMessage(true);
                    $valArea->setShowErrorMessage(true);
                    $valArea->setShowDropDown(true);
                    $valArea->setFormula1('=Master!$B$2:$B$' . ($this->areaCount + 1));

                    // Ketidaksesuaian Validation (Column E)
                    $valKet = $sheet->getCell('E' . $row)->getDataValidation();
                    $valKet->setType(DataValidation::TYPE_LIST);
                    $valKet->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valKet->setAllowBlank(true);
                    $valKet->setShowInputMessage(true);
                    $valKet->setShowErrorMessage(true);
                    $valKet->setShowDropDown(true);
                    $valKet->setFormula1('=Master!$C$2:$C$' . ($this->ketidaksesuaianCount + 1));

                    // Disposisi Validation (Column H)
                    $valDisp = $sheet->getCell('H' . $row)->getDataValidation();
                    $valDisp->setType(DataValidation::TYPE_LIST);
                    $valDisp->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valDisp->setAllowBlank(true);
                    $valDisp->setShowInputMessage(true);
                    $valDisp->setShowErrorMessage(true);
                    $valDisp->setShowDropDown(true);
                    $valDisp->setFormula1('=Master!$D$2:$D$' . ($this->disposisiCount + 1));
                }

                $sheet->getStyle('C2:C500')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $sheet->getStyle('D2:D500')->getNumberFormat()->setFormatCode('hh:mm');
                
                // Set column widths
                foreach(range('A','I') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}

class PemeriksaanProsesProduksiMasterSheet implements FromArray, WithTitle, WithEvents
{
    protected array $shiftList;
    protected array $areaList;
    protected array $ketidaksesuaianOptions;
    protected array $disposisiOptions;

    public function __construct($shiftList, $areaList, $ketidaksesuaianOptions, $disposisiOptions)
    {
        $this->shiftList = $shiftList;
        $this->areaList = $areaList;
        $this->ketidaksesuaianOptions = $ketidaksesuaianOptions;
        $this->disposisiOptions = $disposisiOptions;
    }

    public function title(): string
    {
        return 'Master';
    }

    public function array(): array
    {
        $maxRows = max(
            count($this->shiftList),
            count($this->areaList),
            count($this->ketidaksesuaianOptions),
            count($this->disposisiOptions)
        );

        $rows = [];
        $rows[] = ['Shift', 'Area', 'Ketidaksesuaian', 'Disposisi'];

        $ketValues = array_values($this->ketidaksesuaianOptions);
        $dispValues = array_values($this->disposisiOptions);

        for ($i = 0; $i < $maxRows; $i++) {
            $rows[] = [
                $this->shiftList[$i] ?? null,
                $this->areaList[$i] ?? null,
                $ketValues[$i] ?? null,
                $dispValues[$i] ?? null,
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
