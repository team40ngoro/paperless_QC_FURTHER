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

class PemeriksaanProsesProduksiExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    public function __construct(protected Collection $data, protected array $filterInfo) {}

    public function collection() { return $this->data; }

    public function headings(): array
    {
        return ['No', 'Waktu', 'Area', 'Ketidaksesuaian', 'Uraian Permasalahan', 'Analisa Penyebab', 'Disposisi', 'Tindakan Koreksi'];
    }

    public function map($item): array
    {
        static $no = 0;
        return [
            ++$no,
            $item->jam ? Carbon::parse($item->jam)->format('H:i') : '-',
            $item->area->area ?? '-',
            $item->ketidaksesuaian_label ?? $item->ketidaksesuaian ?? '-',
            $item->uraian_permasalahan ?? '-',
            $item->analisa_penyebab ?? '-',
            $item->disposisi_label ?? $item->disposisi ?? '-',
            $item->tindakan_koreksi ?? '-',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'H';

                $sheet->insertNewRowBefore(1, 5);

                $sheet->mergeCells("A1:{$lastCol}1")->setCellValue('A1', 'PT. CHAROEN POKPHAND INDONESIA');
                $sheet->mergeCells("A2:{$lastCol}2")->setCellValue('A2', 'FOOD DIVISION ' . strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN'));
                $sheet->mergeCells("A3:{$lastCol}3")->setCellValue('A3', 'LAPORAN KETIDAKSESUAIAN');
                $sheet->getStyle('A1:A3')->getFont()->setBold(true);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A4', 'Tanggal: ' . $this->filterInfo['tanggal'] . '   |   Shift: ' . $this->filterInfo['shift']);
                $sheet->setCellValue('A5', 'Area: ' . $this->filterInfo['area']);

                $lastDataRow = $sheet->getHighestRow();
                $sheet->setCellValue("A" . ($lastDataRow + 2), $this->filterInfo['kode_form']);

                $sigRow = $lastDataRow + 4;
                $sheet->setCellValue("A{$sigRow}", 'Dibuat Oleh:');
                $sheet->setCellValue("D{$sigRow}", 'Diketahui Oleh:');
                $sheet->setCellValue("G{$sigRow}", 'Disetujui Oleh:');

                $nameRow = $sigRow + 4;
                $sheet->setCellValue("A{$nameRow}", 'QC Inspector');
                $sheet->setCellValue("D{$nameRow}", 'SPV/Foreman/Forelady Produksi');
                $sheet->setCellValue("G{$nameRow}", 'Supervisor QC');
            },
        ];
    }
}