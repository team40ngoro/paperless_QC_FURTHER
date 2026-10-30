<?php

namespace App\Exports;

use App\Models\ProsesTwahing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProsesThawingExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
            'KODE PRODUKSI PRODUK',
            'TANGGAL EXPIRED PRODUK',
            'NAMA RAW MATERIAL',
            'KODE PRODUKSI RM',
            'BERAT RM (KG)',
            'SUHU RM (°C)',
            'KONDISI RM',
            'WAKTU THAWING AWAL',
            'WAKTU THAWING AKHIR',
            'TOTAL WAKTU THAWING (JAM)',
            'SUHU AIR THAWING (°C)',
            'KETERANGAN',
            'DIBUAT OLEH',
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
        $jam = $row->jam ? \Carbon\Carbon::parse($row->jam)->format('H:i') : '-';
        $shift = $row->shift->shift ?? '-';
        $plant = $row->plan->nama_plan ?? '-';
        $namaProduk = $row->produk->nama_produk ?? '-';
        $kodeProduksiProduk = $row->kode_produksi_produk ?? '-';
        $tanggalExpiredProduk = $row->tanggal_expired_produk ? \Carbon\Carbon::parse($row->tanggal_expired_produk)->format('d-m-Y') : '-';
        $waktuAwal = $row->waktu_thawing_awal ? \Carbon\Carbon::parse($row->waktu_thawing_awal)->format('H:i') : '-';
        $waktuAkhir = $row->waktu_thawing_akhir ? \Carbon\Carbon::parse($row->waktu_thawing_akhir)->format('H:i') : '-';
        $totalWaktu = $row->total_waktu_thawing_jam !== null ? $row->total_waktu_thawing_jam : '-';
        $suhuAir = $row->suhu_air_thawing !== null ? $row->suhu_air_thawing : '-';
        $keterangan = $row->keterangan ?? '-';
        $dibuatOleh = $row->user->name ?? '-';

        $qcAppr = $row->approved_by_qc ? 'APPROVED' : 'PENDING';
        $prodAppr = $row->approved_by_produksi ? 'APPROVED' : 'PENDING';
        $spvAppr = $row->approved_by_spv ? 'APPROVED' : 'PENDING';

        if ($row->details && $row->details->count() > 0) {
            foreach ($row->details as $d) {
                $rows[] = [
                    $no++,
                    $tanggal,
                    $jam,
                    $shift,
                    $plant,
                    $namaProduk,
                    $kodeProduksiProduk,
                    $tanggalExpiredProduk,
                    $d->rm->nama_rm ?? '-',
                    $d->kode_produksi ?? '-',
                    $d->berat ?? '-',
                    $d->suhu ?? '-',
                    $d->kondisi ?? '-',
                    $waktuAwal,
                    $waktuAkhir,
                    $totalWaktu,
                    $suhuAir,
                    $keterangan,
                    $dibuatOleh,
                    $qcAppr,
                    $prodAppr,
                    $spvAppr,
                ];
            }
        } else {
            $rows[] = [
                $no++,
                $tanggal,
                $jam,
                $shift,
                $plant,
                $namaProduk,
                $kodeProduksiProduk,
                $tanggalExpiredProduk,
                '-',
                '-',
                '-',
                '-',
                '-',
                $waktuAwal,
                $waktuAkhir,
                $totalWaktu,
                $suhuAir,
                $keterangan,
                $dibuatOleh,
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
