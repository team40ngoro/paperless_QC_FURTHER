<?php

namespace App\Exports;

use App\Models\DataShift;
use App\Models\DataSeasoning;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SeasoningTemplateExport implements WithMultipleSheets
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sheets(): array
    {
        $seasoningQuery = DataSeasoning::query();
        $shiftQuery = DataShift::query();

        if ($this->user && $this->user->role !== 'superadmin') {
            $seasoningQuery->where('id_plan', $this->user->id_plan);
            $shiftQuery->where('id_plan', $this->user->id_plan);
        }

        $seasoningList = $seasoningQuery->orderBy('nama_seasoning')->pluck('nama_seasoning')->toArray();
        $shiftList = $shiftQuery->orderBy('shift')->pluck('shift')->toArray();

        $sensoriList = ['✔', '✘'];
        $kemasanList = ['✔', '✘'];

        $lastSeasoningRow = max(2, count($seasoningList) + 1);
        $lastShiftRow = max(2, count($shiftList) + 1);
        $lastSensoriRow = max(2, count($sensoriList) + 1);
        $lastKemasanRow = max(2, count($kemasanList) + 1);

        return [
            new SeasoningTemplateSheet($lastSeasoningRow, $lastShiftRow, $lastSensoriRow, $lastKemasanRow),
            new SeasoningMasterSheet($seasoningList, $shiftList, $sensoriList, $kemasanList),
        ];
    }
}

class SeasoningTemplateSheet implements FromArray, WithTitle, WithEvents
{
    protected int $lastSeasoningRow;
    protected int $lastShiftRow;
    protected int $lastSensoriRow;
    protected int $lastKemasanRow;

    public function __construct(int $lastSeasoningRow, int $lastShiftRow, int $lastSensoriRow, int $lastKemasanRow)
    {
        $this->lastSeasoningRow = $lastSeasoningRow;
        $this->lastShiftRow = $lastShiftRow;
        $this->lastSensoriRow = $lastSensoriRow;
        $this->lastKemasanRow = $lastKemasanRow;
    }

    public function title(): string
    {
        return 'Template';
    }

    public function array(): array
    {
        return [
            ['Shift', 'Tanggal', 'Jam', 'Nama Seasoning', 'Kode Produksi', 'Berat Per Pack (kg)', 'Sensori', 'Kemasan', 'Keterangan'],
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

                    // Nama Seasoning Validation
                    $valSeasoning = $sheet->getCell('D' . $row)->getDataValidation();
                    $valSeasoning->setType(DataValidation::TYPE_LIST);
                    $valSeasoning->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valSeasoning->setAllowBlank(true);
                    $valSeasoning->setShowInputMessage(true);
                    $valSeasoning->setShowErrorMessage(true);
                    $valSeasoning->setShowDropDown(true);
                    $valSeasoning->setFormula1('=Master!$A$2:$A$' . $this->lastSeasoningRow);

                    // Sensori Validation
                    $valSensori = $sheet->getCell('G' . $row)->getDataValidation();
                    $valSensori->setType(DataValidation::TYPE_LIST);
                    $valSensori->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valSensori->setAllowBlank(true);
                    $valSensori->setShowInputMessage(true);
                    $valSensori->setShowErrorMessage(true);
                    $valSensori->setShowDropDown(true);
                    $valSensori->setFormula1('=Master!$C$2:$C$' . $this->lastSensoriRow);

                    // Kemasan Validation
                    $valKemasan = $sheet->getCell('H' . $row)->getDataValidation();
                    $valKemasan->setType(DataValidation::TYPE_LIST);
                    $valKemasan->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valKemasan->setAllowBlank(true);
                    $valKemasan->setShowInputMessage(true);
                    $valKemasan->setShowErrorMessage(true);
                    $valKemasan->setShowDropDown(true);
                    $valKemasan->setFormula1('=Master!$D$2:$D$' . $this->lastKemasanRow);
                }

                $sheet->getStyle('B2:B500')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $sheet->getStyle('C2:C500')->getNumberFormat()->setFormatCode('hh:mm');
            },
        ];
    }
}

class SeasoningMasterSheet implements FromArray, WithTitle, WithEvents
{
    protected array $seasoningList;
    protected array $shiftList;
    protected array $sensoriList;
    protected array $kemasanList;

    public function __construct(array $seasoningList, array $shiftList, array $sensoriList, array $kemasanList)
    {
        $this->seasoningList = $seasoningList;
        $this->shiftList = $shiftList;
        $this->sensoriList = $sensoriList;
        $this->kemasanList = $kemasanList;
    }

    public function title(): string
    {
        return 'Master';
    }

    public function array(): array
    {
        $maxRows = max(
            count($this->seasoningList),
            count($this->shiftList),
            count($this->sensoriList),
            count($this->kemasanList)
        );

        $rows = [];
        $rows[] = ['Seasoning', 'Shift', 'Sensori', 'Kemasan'];

        for ($i = 0; $i < $maxRows; $i++) {
            $rows[] = [
                $this->seasoningList[$i] ?? null,
                $this->shiftList[$i] ?? null,
                $this->sensoriList[$i] ?? null,
                $this->kemasanList[$i] ?? null,
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
