<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\DataShift;
use App\Models\Plan;
use App\Models\JenisProduk;

class LabelStartUpTemplateExport implements WithMultipleSheets
{
    protected $plantId;

    public function __construct($plantId = null)
    {
        $this->plantId = $plantId;
    }

    public function sheets(): array
    {
        return [
            0 => new LabelStartUpDataSheet($this->plantId),
            1 => new LabelStartUpMasterSheet(),
        ];
    }
}

class LabelStartUpDataSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $plantId;

    public function __construct($plantId = null)
    {
        $this->plantId = $plantId;
    }

    public function title(): string
    {
        return 'Data Import';
    }

    public function array(): array
    {
        $tanggal = now()->format('d-m-Y');
        $jam = now()->format('H:i');

        return [
            [$tanggal, $jam, 'Select Shift', 'Select Plant', 'Select Produk', '', '', '', '', '', ''],
            [$tanggal, $jam, 'Select Shift', 'Select Plant', 'Select Produk', '', '', '', '', '', ''],
        ];
    }

    public function headings(): array
    {
        return [
            'TANGGAL*',
            'JAM*',
            'SHIFT*',
            'PLANT*',
            'NAMA PRODUK*',
            'BERAT (G)*',
            'LABEL PRODUK*',
            'HASIL PENGECEKAN*',
            'NAMA OPERATOR*',
            'NAMA QC*',
            'TINDAKAN KOREKSI',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 12,
            'C' => 15,
            'D' => 15,
            'E' => 20,
            'F' => 12,
            'G' => 15,
            'H' => 20,
            'I' => 18,
            'J' => 15,
            'K' => 20,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Apply dropdowns with data validation
        // SHIFT column (C) - link to Master Data sheet
        $sheet->getDataValidation('C2:C100')
            ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
            ->setFormula1("'Master Data'!A:A")
            ->setAllowBlank(false)
            ->setShowDropDown(true)
            ->setShowErrorMessage(true)
            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
            ->setError('Pilih Shift dari daftar');

        // PLANT column (D) - link to Master Data sheet
        $sheet->getDataValidation('D2:D100')
            ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
            ->setFormula1("'Master Data'!B:B")
            ->setAllowBlank(false)
            ->setShowDropDown(true)
            ->setShowErrorMessage(true)
            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
            ->setError('Pilih Plant dari daftar');

        // NAMA PRODUK column (E) - link to Master Data sheet
        $sheet->getDataValidation('E2:E100')
            ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
            ->setFormula1("'Master Data'!C:C")
            ->setAllowBlank(false)
            ->setShowDropDown(true)
            ->setShowErrorMessage(true)
            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
            ->setError('Pilih Produk dari daftar');

        // Header styling
        $sheet->getStyle('1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0070C0'],
            ],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Lock all cells
        $sheet->getStyle('A:K')->getProtection()->setLocked(true);

        // Unlock data input cells only
        $sheet->getStyle('A2:K100')->getProtection()->setLocked(false);

        return [];
    }
}

class LabelStartUpMasterSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Master Data';
    }

    public function array(): array
    {
        $shifts = DataShift::orderBy('shift')->pluck('shift')->values()->toArray();
        $plants = Plan::orderBy('nama_plan')->pluck('nama_plan')->values()->toArray();
        $produk = JenisProduk::orderBy('nama_produk')->pluck('nama_produk')->values()->toArray();

        $maxRows = max(count($shifts), count($plants), count($produk));

        $data = [];
        for ($i = 0; $i < $maxRows; $i++) {
            $data[] = [
                $shifts[$i] ?? '',
                $plants[$i] ?? '',
                $produk[$i] ?? '',
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['Shift', 'Plant', 'Produk'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 25,
            'C' => 30,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header styling
        $sheet->getStyle('1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '28A745'],
            ],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Lock all cells
        $sheet->getStyle('A:C')->getProtection()->setLocked(true);

        return [];
    }
}
