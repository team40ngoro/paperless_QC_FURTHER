<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;

class WizardProses3Export implements FromView, WithTitle, WithColumnWidths
{
    protected $data;
    protected $kodeForm;
    protected $filters;

    public function __construct($data, $kodeForm, $filters)
    {
        $this->data = $data;
        $this->kodeForm = $kodeForm;
        $this->filters = $filters;
    }

    public function view(): View
    {
        return view('qc-sistem.wizard_proses3.export_excel', [
            'data'      => $this->data,
            'kode_form' => $this->kodeForm,
            'filters'   => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Verifikasi Proses 3';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 24,
            'B' => 15,
            'C' => 14,
            'D' => 15,
            'E' => 14,
            'F' => 12,
            'G' => 11,
            'H' => 13,
            'I' => 20,
        ];
    }
}
