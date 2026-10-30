<?php

namespace App\Exports;

use App\Models\DataShift;
use App\Models\DataTimbangan;
use App\Models\Timbangan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TimbanganTemplateExport implements WithMultipleSheets
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sheets(): array
    {
        $areaQuery = \App\Models\InputArea::query();
        $shiftQuery = DataShift::query();
        $timbanganQuery = DataTimbangan::query();

        if ($this->user && $this->user->role !== 'superadmin') {
            $areaQuery->where('id_plan', $this->user->id_plan);
            $shiftQuery->where('id_plan', $this->user->id_plan);
            $timbanganQuery->where('id_plan', $this->user->id_plan);
        }

        $areaList = $areaQuery->orderBy('area')->pluck('area')->toArray();
        $shiftList = $shiftQuery->orderBy('shift')->pluck('shift')->toArray();
        $timbanganList = $timbanganQuery->orderBy('nama_timbangan')->pluck('nama_timbangan')->toArray();

        return [
            new TimbanganTemplateSheet(
                count($areaList),
                count($shiftList),
                count($timbanganList)
            ),
            new TimbanganMasterSheet($areaList, $shiftList, $timbanganList),
        ];
    }
}

class TimbanganTemplateSheet implements FromArray, WithTitle, WithEvents
{
    protected int $areaCount;
    protected int $shiftCount;
    protected int $timbanganCount;

    public function __construct($areaCount, $shiftCount, $timbanganCount)
    {
        $this->areaCount = $areaCount;
        $this->shiftCount = $shiftCount;
        $this->timbanganCount = $timbanganCount;
    }

    public function title(): string
    {
        return 'Template';
    }

    public function array(): array
    {
        return [
            [
                'Area',
                'Shift',
                'Tanggal',
                'Jam',
                'Nama Timbangan',
                'Hasil Verifikasi 500',
                'Hasil Verifikasi 1000',
                'Hasil Verifikasi 10000'
            ],
            [
                null,
                null,
                date('d-m-Y'),
                date('H:i'),
                null,
                null,
                null,
                null
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                for ($row = 2; $row <= 1000; $row++) {
                    // Area Validation (Column A)
                    $valArea = $sheet->getCell('A' . $row)->getDataValidation();
                    $valArea->setType(DataValidation::TYPE_LIST);
                    $valArea->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valArea->setAllowBlank(true);
                    $valArea->setShowInputMessage(true);
                    $valArea->setShowErrorMessage(true);
                    $valArea->setShowDropDown(true);
                    $valArea->setFormula1('=\'Master Data\'!$A$2:$A$' . ($this->areaCount + 1));

                    // Shift Validation (Column B)
                    $valShift = $sheet->getCell('B' . $row)->getDataValidation();
                    $valShift->setType(DataValidation::TYPE_LIST);
                    $valShift->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valShift->setAllowBlank(true);
                    $valShift->setShowInputMessage(true);
                    $valShift->setShowErrorMessage(true);
                    $valShift->setShowDropDown(true);
                    $valShift->setFormula1('=\'Master Data\'!$B$2:$B$' . ($this->shiftCount + 1));

                    // Timbangan Validation (Column E)
                    $valTimbangan = $sheet->getCell('E' . $row)->getDataValidation();
                    $valTimbangan->setType(DataValidation::TYPE_LIST);
                    $valTimbangan->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valTimbangan->setAllowBlank(true);
                    $valTimbangan->setShowInputMessage(true);
                    $valTimbangan->setShowErrorMessage(true);
                    $valTimbangan->setShowDropDown(true);
                    $valTimbangan->setFormula1('=\'Master Data\'!$C$2:$C$' . ($this->timbanganCount + 1));

                    // Gram Column (Column F) - NO DROPDOWN, TEXT ONLY
                    // Hasil Verifikasi 1000 Validation (Column G) - NO DROPDOWN
                    // Hasil Verifikasi 10000 Validation (Column H) - NO DROPDOWN
                }

                // Set date and time format
                $sheet->getStyle('C2:C1000')->getNumberFormat()->setFormatCode('dd-mm-yyyy');
                $sheet->getStyle('D2:D1000')->getNumberFormat()->setFormatCode('hh:mm');

                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}

class TimbanganMasterSheet implements FromArray, WithTitle, WithEvents
{
    protected array $areaList;
    protected array $shiftList;
    protected array $timbanganList;

    public function __construct($areaList, $shiftList, $timbanganList)
    {
        $this->areaList = $areaList;
        $this->shiftList = $shiftList;
        $this->timbanganList = $timbanganList;
    }

    public function title(): string
    {
        return 'Master Data';
    }

    public function array(): array
    {
        $maxRows = max(
            count($this->areaList),
            count($this->shiftList),
            count($this->timbanganList)
        );

        $rows = [];
        $rows[] = ['Area', 'Shift', 'Nama Timbangan'];

        for ($i = 0; $i < $maxRows; $i++) {
            $rows[] = [
                $this->areaList[$i] ?? null,
                $this->shiftList[$i] ?? null,
                $this->timbanganList[$i] ?? null,
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
