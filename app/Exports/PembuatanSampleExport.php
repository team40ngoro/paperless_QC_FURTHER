<?php

namespace App\Exports;

use App\Models\JenisProduk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PembuatanSampleExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
            'KODE PRODUKSI',
            'TANGGAL EXPIRED',
            'JUMLAH',
            'BERAT (G)',
            'BERAT SAMPLING',
            'JENIS SAMPLE',
     
        ];
    }

    public function map($row): array
    {
        static $no = 1;
        $rows = [];

        $tanggal = $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') : '-';
        $jam = $row->jam ? \Carbon\Carbon::parse($row->jam)->format('H:i') : '-';
        $shift = $row->shift->shift ?? '-';
        $plant = $row->plan->nama_plan ?? '-';
        $dibuatOleh = $row->createdBy->name ?? '-';
        $qcAppr = $row->approved_by_qc ? 'APPROVED' : 'PENDING';
        $prodAppr = $row->approved_by_produksi ? 'APPROVED' : 'PENDING';
        $spvAppr = $row->approved_by_spv ? 'APPROVED' : 'PENDING';

        if (!empty($row->items_json) && is_array($row->items_json)) {
            foreach ($row->items_json as $item) {
                $produkModel = isset($item['id_produk']) ? JenisProduk::find($item['id_produk']) : null;
                $namaProduk = $produkModel->nama_produk ?? ($row->produk->nama_produk ?? '-');
                $kodeProduksi = $item['kode_produksi'] ?? $row->kode_produksi ?? '-';
                $tglExp = isset($item['tanggal_expired']) && $item['tanggal_expired'] ? \Carbon\Carbon::parse($item['tanggal_expired'])->format('d-m-Y') : '-';
                $jumlah = $item['jumlah'] ?? $row->jumlah ?? '-';
                $berat = $item['berat'] ?? $row->berat ?? '-';
                $beratSampling = $item['berat_sampling'] ?? $row->berat_sampling ?? '-';
                $jenisSample = $item['jenis_sample'] ?? $row->jenis_sample ?? '-';

                $rows[] = [
                    $no++,
                    $tanggal,
                    $jam,
                    $shift,
                    $plant,
                    $namaProduk,
                    $kodeProduksi,
                    $tglExp,
                    $jumlah,
                    $berat,
                    $beratSampling,
                    $jenisSample,
               
                ];
            }
        } else {
            $rows[] = [
                $no++,
                $tanggal,
                $jam,
                $shift,
                $plant,
                $row->produk->nama_produk ?? '-',
                $row->kode_produksi ?? '-',
                $row->tanggal_expired ? \Carbon\Carbon::parse($row->tanggal_expired)->format('d-m-Y') : '-',
                $row->jumlah ?? '-',
                $row->berat ?? '-',
                $row->berat_sampling ?? '-',
                $row->jenis_sample ?? '-',
           
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
