<?php

namespace App\Exports;

use App\Models\DataShift;
use App\Models\DataThermo;
use App\Models\Thermometer;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ThermometerTemplateExport implements WithMultipleSheets
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sheets(): array
    {
        $shiftQuery = DataShift::query();
        $thermoQuery = DataThermo::query();

        if ($this->user && $this->user->role !== 'superadmin') {
            $shiftQuery->where('id_plan', $this->user->id_plan);
            $thermoQuery->where('id_plan', $this->user->id_plan);
        }

        $shiftList = $shiftQuery->orderBy('shift')->pluck('shift')->toArray();
        $thermoList = $thermoQuery->orderBy('nama_thermo')->pluck('nama_thermo')->toArray();
        $hasilOptions = array_keys(Thermometer::getHasilPengecekanOptions());

        return [
            new ThermometerTemplateSheet(
                count($shiftList),
                count($thermoList),
                count($hasilOptions)
            ),
            new ThermometerMasterSheet($shiftList, $thermoList, $hasilOptions),
        ];
    }
}

class ThermometerTemplateSheet implements FromArray, WithTitle, WithEvents
{
    protected int $shiftCount;
    protected int $thermoCount;
    protected int $hasilCount;

    public function __construct($shiftCount, $thermoCount, $hasilCount)
    {
        $this->shiftCount = $shiftCount;
        $this->thermoCount = $thermoCount;
        $this->hasilCount = $hasilCount;
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
                'Nama Thermometer',
                'Hasil Pengecekan (ok/tidak_ok)',
                'Hasil Verifikasi 0°C',
                'Hasil Verifikasi 100°C'
            ],
            [
                null,
                date('d-m-Y'),
                date('H:i'),
                null,
                'ok',
                '0',
                '100'
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

                    // Thermometer Validation (Column D)
                    $valThermo = $sheet->getCell('D' . $row)->getDataValidation();
                    $valThermo->setType(DataValidation::TYPE_LIST);
                    $valThermo->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valThermo->setAllowBlank(true);
                    $valThermo->setShowInputMessage(true);
                    $valThermo->setShowErrorMessage(true);
                    $valThermo->setShowDropDown(true);
                    $valThermo->setFormula1('=Master!$B$2:$B$' . ($this->thermoCount + 1));

                    // Hasil Validation (Column E)
                    $valHasil = $sheet->getCell('E' . $row)->getDataValidation();
                    $valHasil->setType(DataValidation::TYPE_LIST);
                    $valHasil->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valHasil->setAllowBlank(true);
                    $valHasil->setShowInputMessage(true);
                    $valHasil->setShowErrorMessage(true);
                    $valHasil->setShowDropDown(true);
                    $valHasil->setFormula1('=Master!$C$2:$C$' . ($this->hasilCount + 1));
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

class ThermometerMasterSheet implements FromArray, WithTitle, WithEvents
{
    protected array $shiftList;
    protected array $thermoList;
    protected array $hasilOptions;

    public function __construct($shiftList, $thermoList, $hasilOptions)
    {
        $this->shiftList = $shiftList;
        $this->thermoList = $thermoList;
        $this->hasilOptions = $hasilOptions;
    }

    public function title(): string
    {
        return 'Master';
    }

    public function array(): array
    {
        $maxRows = max(
            count($this->shiftList),
            count($this->thermoList),
            count($this->hasilOptions)
        );

        $rows = [];
        $rows[] = ['Shift', 'Nama Thermometer', 'Hasil Pengecekan'];

        for ($i = 0; $i < $maxRows; $i++) {
            $rows[] = [
                $this->shiftList[$i] ?? null,
                $this->thermoList[$i] ?? null,
                $this->hasilOptions[$i] ?? null,
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
