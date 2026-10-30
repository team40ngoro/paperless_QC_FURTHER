<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;

class MetalDetectorExport implements FromView, WithTitle, WithColumnWidths
{
    protected $data;
    protected $filterInfo;

    public function __construct($data, $filterInfo)
    {
        $this->data = $data;
        $this->filterInfo = $filterInfo;
    }

    public function view(): View
    {
        return view('qc-sistem.input_metal_detector.export_excel', [
            'data'       => $this->data,
            'filterInfo' => $this->filterInfo,
        ]);
    }

    public function title(): string
    {
        return 'Pemeriksaan Metal Detector';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 8,
            'C' => 30,
            'D' => 16,
            'E' => 8,
            'F' => 10,
            'G' => 10,
            'H' => 10,
            'I' => 10,
            'J' => 10,
            'K' => 10,
            'L' => 10,
            'M' => 10,
            'N' => 10,
            'O' => 22,
        ];
    }
}
