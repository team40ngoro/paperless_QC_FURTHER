<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class VerifikasiBeratProdukExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles
{
    protected $data;
    protected $filters;

    public function __construct($data, array $filters = [])
    {
        $this->data    = $data;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'Verifikasi Berat Produk';
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Shift',
            'Jam',
            'Produk',
            'Kode Produksi',
            'Gramase (g)',
            'Jenis Produk',
            // After Forming
            'Rata-rata After Forming',
            // Predusting
            'Rata-rata Predusting',
            'Pickup After Forming (Predusting)',
            // Battering
            'Rata-rata Battering',
            'Pickup Predusting → Battering',
            // Breadering
            'Rata-rata Breadering',
            'Pickup Battering → Breadering',
            // Breader
            'Rata-rata Breader',
            'Pickup Breader',
            'Pickup Total Breader',
            // Fryer 1
            'Rata-rata Fryer 1',
            'Pickup Breadering → Fryer 1',
            // Fryer 2
            'Rata-rata Fryer 2',
            'Pickup Fryer 1 → Fryer 2',
            'Pickup Total',
            'Pickup Total Fryer 2',
            // Roasting
            'Rata-rata Roasting',
            'Pickup After Breadering (Roasting)',
            'Pickup Total Roasting',
            // KFC Dry
            'Rata-rata Dry KFC',
            // KFC Wet
            'Rata-rata Wet KFC',
            'Pickup After Forming (KFC)',
            // Hasil & Suhu Adonan
            'Hasil Adonan',
            'Suhu Adonan (°C)',
            'Catatan',
            // Approval
            'Disetujui QC',
            'Disetujui Produksi',
            'Disetujui SPV',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->tanggal ? Carbon::parse($row->tanggal)->format('d-m-Y') : '-',
            $row->shift->shift ?? '-',
            $row->jam ? Carbon::parse($row->jam)->format('H:i') : '-',
            $row->produk->nama_produk ?? '-',
            $row->kode_produksi ?? '-',
            $row->gramase ?? '-',
            $row->jenis_produk_kfc ?? '-',
            // After Forming
            $row->rata_rata_after_forming ?? '-',
            // Predusting
            $row->rata_rata_predusting ?? '-',
            $row->pickup_after_forming_predusting ?? '-',
            // Battering
            $row->rata_rata_battering ?? '-',
            $row->pickup_after_predusting_battering ?? '-',
            // Breadering
            $row->rata_rata_breadering ?? '-',
            $row->pickup_after_battering_breadering ?? '-',
            // Breader
            $row->rata_rata_breader ?? '-',
            $row->pickup_breader ?? '-',
            $row->pickup_total_breader ?? '-',
            // Fryer 1
            $row->rata_rata_fryer_1 ?? '-',
            $row->pickup_breadering_fryer_1 ?? '-',
            // Fryer 2
            $row->rata_rata_fryer_2 ?? '-',
            $row->pickup_fryer_1_fryer_2 ?? '-',
            $row->pickup_total ?? '-',
            $row->pickup_total_fryer_2 ?? '-',
            // Roasting
            $row->rata_rata_roasting ?? '-',
            $row->pickup_after_breadering_roasting ?? '-',
            $row->pickup_total_roasting ?? '-',
            // KFC
            $row->rata_rata_dry_kfc ?? '-',
            $row->rata_rata_wet_kfc ?? '-',
            $row->pickup_after_forming_kfc ?? '-',
            // Hasil Adonan
            $row->hasil_adonan ?? '-',
            $row->suhu_adonan ?? '-',
            $row->catatan ?? '-',
            // Approval
            $row->approved_by_qc ? 'Ya' : 'Belum',
            $row->approved_by_produksi ? 'Ya' : 'Belum',
            $row->approved_by_spv ? 'Ya' : 'Belum',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1E3A5F']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            ],
        ];
    }
}
