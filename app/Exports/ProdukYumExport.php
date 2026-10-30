<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProdukYumExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
            'STD BAG/PACK (GROSS)',
            'BERAT PCS',
            'JUMLAH PCS',
            'AKTUAL BERAT',
            'USER',
            'APPROVAL QC',
            'APPROVAL PRODUKSI',
            'APPROVAL SPV',
        ];
    }

    public function map($row): array
    {
        static $no = 1;

        $tanggal = $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') : '-';
        $jam = $row->jam ? \Carbon\Carbon::parse($row->jam)->format('H:i') : '-';
        $shift = $row->shift->shift ?? '-';
        $plant = $row->plan->nama_plan ?? '-';
        $namaProduk = $row->produk->nama_produk ?? '-';
        $kodeProduksi = $row->kode_produksi ?? '-';
        $stdBag = $row->dataBag->std_bag ?? '-';

        $beratPcsStr = (is_array($row->berat_pcs) && count($row->berat_pcs) > 0) ? implode(', ', array_filter($row->berat_pcs)) : '-';
        $jumlahPcsStr = (is_array($row->jumlah_pcs) && count($row->jumlah_pcs) > 0) ? implode(', ', array_filter($row->jumlah_pcs)) : '-';
        $aktualBeratStr = (is_array($row->aktual_berat) && count($row->aktual_berat) > 0) ? implode(', ', array_filter($row->aktual_berat)) : '-';

        $userName = $row->user->name ?? '-';

        $qcAppr = $row->approved_by_qc ? 'APPROVED' : 'PENDING';
        $prodAppr = $row->approved_by_produksi ? 'APPROVED' : 'PENDING';
        $spvAppr = $row->approved_by_spv ? 'APPROVED' : 'PENDING';

        return [
            $no++,
            $tanggal,
            $jam,
            $shift,
            $plant,
            $namaProduk,
            $kodeProduksi,
            $stdBag,
            $beratPcsStr,
            $jumlahPcsStr,
            $aktualBeratStr,
            $userName,
            $qcAppr,
            $prodAppr,
            $spvAppr,
        ];
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
