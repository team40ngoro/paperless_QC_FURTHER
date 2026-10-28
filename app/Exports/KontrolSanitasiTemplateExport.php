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

class KontrolSanitasiTemplateExport implements WithMultipleSheets
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

        return [
            new KontrolSanitasiTemplateSheet(count($shiftList)),
            new KontrolSanitasiMasterSheet($shiftList),
        ];
    }
}

class KontrolSanitasiTemplateSheet implements FromArray, WithTitle, WithEvents
{
    protected int $shiftCount;

    public function __construct($shiftCount)
    {
        $this->shiftCount = $shiftCount;
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
                'Tanggal',
                'Jam',
                'Suhu Air (°C)',
                'Kadar Klorin Foot Basin (ppm)',
                'Kadar Klorin Hand Basin (ppm)',
                'Hasil Verifikasi'
            ],
            [
                null,
                date('d-m-Y'),
                date('H:i'),
                '40',
                '50',
                '10',
                'Sesuai'
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                for ($row = 2; $row <= 1000; $row++) {
                    // Shift Validation (Column A)
                    $valShift = $sheet->getCell('A' . $row)->getDataValidation();
                    $valShift->setType(DataValidation::TYPE_LIST);
                    $valShift->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valShift->setAllowBlank(true);
                    $valShift->setShowInputMessage(true);
                    $valShift->setShowErrorMessage(true);
                    $valShift->setShowDropDown(true);
                    $valShift->setFormula1('=Master!$A$2:$A$' . ($this->shiftCount + 1));
                }

                // Set date and time format
                $sheet->getStyle('B2:B1000')->getNumberFormat()->setFormatCode('dd-mm-yyyy');
                $sheet->getStyle('C2:C1000')->getNumberFormat()->setFormatCode('hh:mm');

                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}

class KontrolSanitasiMasterSheet implements FromArray, WithTitle, WithEvents
{
    protected array $shiftList;

    public function __construct($shiftList)
    {
        $this->shiftList = $shiftList;
    }

    public function title(): string
    {
        return 'Master';
    }

    public function array(): array
    {
        $rows = [];
        $rows[] = ['Shift'];

        foreach ($this->shiftList as $shift) {
            $rows[] = [$shift];
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
