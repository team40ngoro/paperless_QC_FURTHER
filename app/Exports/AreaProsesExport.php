<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AreaProsesExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    public function __construct(protected Collection $data, protected array $filterInfo) {}

    public function collection() { return $this->data; }

    public function headings(): array
    {
        return ['No', 'Area', 'Jam', 'Kebersihan Ruangan', 'Kebersihan Karyawan', 'Suhu Ruang (°C)', 'Suhu RM', 'Kondisi Barang', 'Ketidaksesuaian', 'Tindakan Koreksi'];
    }

    public function map($item): array
    {
        static $no = 0;
        return [
            ++$no,
            $item->area->area ?? '-',
            $item->jam ?? '-',
            $item->kebersihan_ruangan ?? '-',
            $item->kebersihan_karyawan ?? '-',
            $item->pemeriksaan_suhu_ruang ?? '-',
            $item->suhu_rm ?: '-',
            $item->kondisi_barang ?: '-',
            $item->ketidaksesuaian ?: '-',
            $item->tindakan_koreksi ?: '-',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'J';

                $sheet->insertNewRowBefore(1, 5);

                $sheet->mergeCells("A1:{$lastCol}1")->setCellValue('A1', 'PT. CHAROEN POKPHAND INDONESIA');
                $sheet->mergeCells("A2:{$lastCol}2")->setCellValue('A2', 'FOOD DIVISION ' . strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN'));
                $sheet->mergeCells("A3:{$lastCol}3")->setCellValue('A3', 'KONDISI AREA KERJA SELAMA PRODUKSI');
                $sheet->getStyle('A1:A3')->getFont()->setBold(true);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A4', 'Tanggal: ' . $this->filterInfo['tanggal'] . '   |   Shift: ' . $this->filterInfo['shift']);
                $sheet->setCellValue('A5', 'Area: ' . $this->filterInfo['area']);

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