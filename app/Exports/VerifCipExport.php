<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class VerifCipExport implements FromCollection, WithHeadings, WithEvents
{
    protected array $stepsMeta = [
        ['key' => 'ro1', 'label' => 'Rinse Outside 1'],
        ['key' => 'ri1', 'label' => 'Rinse Inside 1'],
        ['key' => 'ro2', 'label' => 'Rinse Outside 2'],
        ['key' => 'ri2', 'label' => 'Rinse Inside 2'],
        ['key' => 'hc', 'label' => 'Hot Clean Inside'],
        ['key' => 'hci', 'label' => 'Hot Clean Outside'],
        ['key' => 'ro3', 'label' => 'Rinse Outside 3'],
        ['key' => 'ri3', 'label' => 'Rinse Inside 3'],
        ['key' => 'dis', 'label' => 'Disinfection'],
        ['key' => 'diso', 'label' => 'Disinfection Out'],
        ['key' => 'ro4', 'label' => 'Rinse Outside 4'],
        ['key' => 'ri4', 'label' => 'Rinse Inside 4'],
    ];

    protected Collection $rows;

    public function __construct(protected Collection $data, protected array $filterInfo)
    {
        $this->rows = $this->flatten();
    }

    protected function flatten(): Collection
    {
        $rows = collect();

        foreach ($this->data as $item) {
            $forms = $item->payload['forms'] ?? [];
            if (!is_array($forms) || count($forms) === 0) {
                $forms = [['tanggal' => $item->tanggal, 'details' => []]];
            }

            foreach ($forms as $form) {
                $formTanggal = data_get($form, 'tanggal') ?: $item->tanggal;
                $details = data_get($form, 'details', []);
                if (!is_array($details) || count($details) === 0) {
                    $details = [null];
                }

                foreach ($details as $detailIndex => $detail) {
                    $rows->push([
                        'formTanggal' => $formTanggal,
                        'detailIndex' => is_int($detailIndex) ? $detailIndex : 0,
                        'detail' => $detail,
                    ]);
                }
            }
        }

        return $rows->sortBy([
            fn ($r) => $r['formTanggal'] ? Carbon::parse($r['formTanggal'])->timestamp : 0,
            fn ($r) => $r['detailIndex'],
        ])->values();
    }

    public function collection()
    {
        return $this->rows->map(function ($r, $i) {
            $detail = $r['detail'];
            $steps = is_array($detail) ? (data_get($detail, 'steps') ?: []) : [];

            $row = [
                $i + 1,
                $r['formTanggal'] ? Carbon::parse($r['formTanggal'])->format('d-m-Y') : '-',
                is_array($detail) ? (data_get($detail, 'jenis_mouldrum') ?: '-') : '-',
            ];

            foreach ($this->stepsMeta as $s) {
                $stepVal = is_array($steps) ? (data_get($steps, $s['key']) ?: []) : [];
                $row[] = is_array($stepVal) ? (data_get($stepVal, 'suhu') ?: '-') : '-';
                $row[] = is_array($stepVal) ? (data_get($stepVal, 'suhu_display') ?: '-') : '-';
                $row[] = is_array($stepVal) ? (data_get($stepVal, 'waktu') ?: '-') : '-';
            }

            $row[] = is_array($detail) ? (data_get($detail, 'kondisi_sebelum') ?: '-') : '-';
            $row[] = is_array($detail) ? (data_get($detail, 'kondisi_sesudah') ?: '-') : '-';
            $row[] = is_array($detail) ? (data_get($detail, 'ph_air') ?: '-') : '-';
            $row[] = is_array($detail) ? (data_get($detail, 'pressure') ?: '-') : '-';
            $row[] = is_array($detail) ? (data_get($detail, 'keterangan') ?: '-') : '-';
            $row[] = is_array($detail) ? (data_get($detail, 'tindakan_koreksi') ?: '-') : '-';

            return $row;
        });
    }

    public function headings(): array
    {
        $headings = ['No', 'Tanggal', 'Jenis Mouldrum'];

        foreach ($this->stepsMeta as $s) {
            $headings[] = $s['label'] . ' - Aktual';
            $headings[] = $s['label'] . ' - Display';
            $headings[] = $s['label'] . ' - Waktu';
        }

        return array_merge($headings, ['Kondisi Sebelum', 'Kondisi Sesudah', 'PH Air', 'Pressure', 'Keterangan', 'Tindakan Koreksi']);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = $sheet->getHighestColumn();

                $sheet->insertNewRowBefore(1, 5);

                $sheet->mergeCells("A1:{$lastCol}1")->setCellValue('A1', 'PT. CHAROEN POKPHAND INDONESIA');
                $sheet->mergeCells("A2:{$lastCol}2")->setCellValue('A2', 'FOOD DIVISION ' . strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN'));
                $sheet->mergeCells("A3:{$lastCol}3")->setCellValue('A3', 'PEMERIKSAAN CIP');
                $sheet->getStyle('A1:A3')->getFont()->setBold(true);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A4', 'Tanggal: ' . $this->filterInfo['tanggal']);

                $lastDataRow = $sheet->getHighestRow();
                $sheet->setCellValue("A" . ($lastDataRow + 2), $this->filterInfo['kode_form']);

                $sigRow = $lastDataRow + 4;
                $sheet->setCellValue("A{$sigRow}", 'Dibuat Oleh:');
                $sheet->setCellValue("E{$sigRow}", 'Diketahui Oleh:');
                $sheet->setCellValue("H{$sigRow}", 'Disetujui Oleh:');

                $nameRow = $sigRow + 4;
                $sheet->setCellValue("A{$nameRow}", 'QC Inspector');
                $sheet->setCellValue("E{$nameRow}", 'SPV/Foreman/Forelady Produksi');
                $sheet->setCellValue("H{$nameRow}", 'Supervisor QC');
            },
        ];
    }
}