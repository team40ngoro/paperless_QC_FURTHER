<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;

class WizardProses1Export implements FromView, WithTitle, WithColumnWidths
{
    protected $processedData;
    protected $kodeForm;
    protected $filters;

    public function __construct($processedData, $kodeForm, $filters)
    {
        $this->processedData = $processedData;
        $this->kodeForm = $kodeForm;
        $this->filters = $filters;
    }

    public function view(): View
    {
        return view('qc-sistem.wizard_proses1.export_excel', [
            'processedData' => $this->processedData,
            'kode_form'     => $this->kodeForm,
            'filters'       => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Verifikasi Proses 1';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 16,
            'B' => 14,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 13,
            'G' => 11,
            'H' => 13,
            'I' => 24,
        ];
    }
}
