<?php

namespace App\Exports;

use App\Models\DataShift;
use App\Models\InputArea;
use App\Models\GmpKaryawan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GmpKaryawanTemplateExport implements WithMultipleSheets
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
        $temuanOptions = GmpKaryawan::getTemuanOptions();
        $verifikasiOptions = ['ok', 'tidak_ok'];

        return [
            new GmpKaryawanTemplateSheet(
                count($shiftList),
                count($areaList),
                count($temuanOptions),
                count($verifikasiOptions)
            ),
            new GmpKaryawanMasterSheet($shiftList, $areaList, $temuanOptions, $verifikasiOptions),
        ];
    }
}

class GmpKaryawanTemplateSheet implements FromArray, WithTitle, WithEvents
{
    protected int $shiftCount;
    protected int $areaCount;
    protected int $temuanCount;
    protected int $verifikasiCount;

    public function __construct($shiftCount, $areaCount, $temuanCount, $verifikasiCount)
    {
        $this->shiftCount = $shiftCount;
        $this->areaCount = $areaCount;
        $this->temuanCount = $temuanCount;
        $this->verifikasiCount = $verifikasiCount;
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
                'Nama Karyawan',
                'Temuan Ketidaksesuaian',
                'Keterangan',
                'Tindakan Koreksi',
                'Verifikasi (ok/tidak_ok)'
            ],
            [
                null,
                null,
                date('d-m-Y'),
                date('H:i'),
                'Contoh Nama',
                null,
                'Contoh Keterangan',
                'Contoh Tindakan',
                'ok'
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

                    // Area Validation (Column B)
                    $valArea = $sheet->getCell('B' . $row)->getDataValidation();
                    $valArea->setType(DataValidation::TYPE_LIST);
                    $valArea->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valArea->setAllowBlank(true);
                    $valArea->setShowInputMessage(true);
                    $valArea->setShowErrorMessage(true);
                    $valArea->setShowDropDown(true);
                    $valArea->setFormula1('=Master!$B$2:$B$' . ($this->areaCount + 1));

                    // Temuan Validation (Column F)
                    $valTemuan = $sheet->getCell('F' . $row)->getDataValidation();
                    $valTemuan->setType(DataValidation::TYPE_LIST);
                    $valTemuan->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valTemuan->setAllowBlank(true);
                    $valTemuan->setShowInputMessage(true);
                    $valTemuan->setShowErrorMessage(true);
                    $valTemuan->setShowDropDown(true);
                    $valTemuan->setFormula1('=Master!$C$2:$C$' . ($this->temuanCount + 1));

                    // Verifikasi Validation (Column I)
                    $valVerif = $sheet->getCell('I' . $row)->getDataValidation();
                    $valVerif->setType(DataValidation::TYPE_LIST);
                    $valVerif->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $valVerif->setAllowBlank(true);
                    $valVerif->setShowInputMessage(true);
                    $valVerif->setShowErrorMessage(true);
                    $valVerif->setShowDropDown(true);
                    $valVerif->setFormula1('=Master!$D$2:$D$' . ($this->verifikasiCount + 1));
                }

                // Set date and time format
                $sheet->getStyle('C2:C1000')->getNumberFormat()->setFormatCode('dd-mm-yyyy');
                $sheet->getStyle('D2:D1000')->getNumberFormat()->setFormatCode('hh:mm');

                foreach (range('A', 'I') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}

class GmpKaryawanMasterSheet implements FromArray, WithTitle, WithEvents
{
    protected array $shiftList;
    protected array $areaList;
    protected array $temuanOptions;
    protected array $verifikasiOptions;

    public function __construct($shiftList, $areaList, $temuanOptions, $verifikasiOptions)
    {
        $this->shiftList = $shiftList;
        $this->areaList = $areaList;
        $this->temuanOptions = $temuanOptions;
        $this->verifikasiOptions = $verifikasiOptions;
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
            count($this->temuanOptions),
            count($this->verifikasiOptions)
        );

        $rows = [];
        $rows[] = ['Shift', 'Area', 'Temuan', 'Verifikasi'];

        $temuanValues = array_values($this->temuanOptions);

        for ($i = 0; $i < $maxRows; $i++) {
            $rows[] = [
                $this->shiftList[$i] ?? null,
                $this->areaList[$i] ?? null,
                $temuanValues[$i] ?? null,
                $this->verifikasiOptions[$i] ?? null,
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
