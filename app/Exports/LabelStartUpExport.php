<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LabelStartUpExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'NO',
            'TANGGAL',
            'JAM',
            'SHIFT',
            'PLANT',
            'NAMA PRODUK',
            'BERAT (G)',
            'LABEL PRODUK',
            'HASIL PENGECEKAN',
            'NAMA OPERATOR',
            'NAMA QC',
            'TINDAKAN KOREKSI',
            'APPROVAL QC',
            'APPROVAL PRODUKSI',
            'APPROVAL SPV',
        ];
    }

    public function map($row): array
    {
        static $no = 1;
        $rows = [];

        $tanggal = $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') : '-';
        $shift = $row->shift->shift ?? '-';
        $plant = $row->plan->nama_plan ?? '-';

        $qcAppr = $row->approved_by_qc ? 'APPROVED' : 'PENDING';
        $prodAppr = $row->approved_by_produksi ? 'APPROVED' : 'PENDING';
        $spvAppr = $row->approved_by_spv ? 'APPROVED' : 'PENDING';

        if ($row->details && $row->details->count() > 0) {
            foreach ($row->details as $detail) {
                $jam = $detail->jam ? \Carbon\Carbon::parse($detail->jam)->format('H:i') : '-';
                $namaProduk = $detail->produk->nama_produk ?? '-';
                $berat = $detail->berat ?? '-';
                $labelProduk = $detail->label_produk ?? '-';
                $hasilPengecekan = $detail->hasil_pengecekan ?? '-';
                $namaOperator = $detail->nama_operator ?? '-';
                $namaQc = $detail->nama_qc ?? '-';
                $tindakanKoreksi = $detail->tindakan_koreksi ?? '-';

                $rows[] = [
                    $no++,
                    $tanggal,
                    $jam,
                    $shift,
                    $plant,
                    $namaProduk,
                    $berat,
                    $labelProduk,
                    $hasilPengecekan,
                    $namaOperator,
                    $namaQc,
                    $tindakanKoreksi,
                    $qcAppr,
                    $prodAppr,
                    $spvAppr,
                ];
            }
        } else {
            $rows[] = [
                $no++,
                $tanggal,
                '-',
                $shift,
                $plant,
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                $qcAppr,
                $prodAppr,
                $spvAppr,
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '28A745'],
                ],
            ],
        ];
    }
}
