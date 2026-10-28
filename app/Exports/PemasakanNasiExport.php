<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PemasakanNasiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
            'PRODUK',
            'KODE PRODUKSI',
            'WAKTU START',
            'WAKTU STOP',
            'PROSES',
            'JENIS BAHAN',
            'JUMLAH',
            'STATUS COOKING',
            'LAMA PROSES (MENIT)',
            'TEMP STD 1 (°C)',
            'TEMP STD 2 (°C)',
            'TEMP STD 3 (°C)',
            'SENSORI KONDISI',
            'ORGANO WARNA',
            'ORGANO AROMA',
            'ORGANO RASA',
            'ORGANO TEKSTUR',
            'CATATAN',
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
        $produk = $row->produk->nama_produk ?? '-';

        $jenisBahanStr = is_array($row->jenis_bahan) ? implode(', ', array_filter($row->jenis_bahan)) : ($row->jenis_bahan ?? '-');
        $jumlahStr = is_array($row->jumlah) ? implode(', ', array_filter($row->jumlah)) : ($row->jumlah ?? '-');

        $statusCooking = $row->status_cooking ? 'Aktif' : 'Tidak Aktif';
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
            $produk,
            $row->kode_produksi ?? '-',
            $row->waktu_start ?? '-',
            $row->waktu_stop ?? '-',
            $row->proses ?? '-',
            $jenisBahanStr,
            $jumlahStr,
            $statusCooking,
            $row->lama_proses ?? '-',
            $row->temp_std_1 ?? '-',
            $row->temp_std_2 ?? '-',
            $row->temp_std_3 ?? '-',
            $row->sensori_kondisi ?? '-',
            $row->organo_warna ?? '-',
            $row->organo_aroma ?? '-',
            $row->organo_rasa ?? '-',
            $row->organo_tekstur ?? '-',
            $row->catatan ?? '-',
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
