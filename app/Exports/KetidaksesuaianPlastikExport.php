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

class KetidaksesuaianPlastikExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    public function __construct(protected Collection $data, protected array $filterInfo) {}

    public function collection() { return $this->data; }

    public function headings(): array
    {
        return ['No', 'Waktu', 'Shift', 'Nama Plastik', 'Jumlah (Kg)', 'Alasan Hold', 'Hold Data'];
    }

    public function map($item): array
    {
        static $no = 0;
        return [
            ++$no,
            $item->jam ? Carbon::parse($item->jam)->format('H:i') : '-',
            $item->shift->shift ?? '-',
            $item->nama_plastik ?? '-',
            $item->jumlah_ketidaksesuaian ?? '-',
            $item->alasan_hold ?? '-',
            $item->hold_data ?? '-',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'G';

                // Push data down 5 rows to make room for the header block
                $sheet->insertNewRowBefore(1, 5);

                $sheet->mergeCells("A1:{$lastCol}1")->setCellValue('A1', 'PT. CHAROEN POKPHAND INDONESIA');
                $sheet->mergeCells("A2:{$lastCol}2")->setCellValue('A2', 'FOOD DIVISION ' . strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN'));
                $sheet->mergeCells("A3:{$lastCol}3")->setCellValue('A3', 'KETIDAKSESUAIAN PLASTIK');
                $sheet->getStyle('A1:A3')->getFont()->setBold(true);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A4', 'Tanggal: ' . $this->filterInfo['tanggal']);
                $sheet->setCellValue('A5', 'Shift: ' . $this->filterInfo['shift']);

                // Footer below the data: kode_form + signature block
                $lastDataRow = $sheet->getHighestRow();
                $sheet->setCellValue("A" . ($lastDataRow + 2), $this->filterInfo['kode_form']);

                $sigRow = $lastDataRow + 4;
                $sheet->setCellValue("A{$sigRow}", 'Dibuat Oleh:');
                $sheet->setCellValue("C{$sigRow}", 'Diketahui Oleh:');
                $sheet->setCellValue("E{$sigRow}", 'Disetujui Oleh:');

                $nameRow = $sigRow + 4;
                $sheet->setCellValue("A{$nameRow}", 'QC Inspector');
                $sheet->setCellValue("C{$nameRow}", 'SPV/Foreman/Forelady Produksi');
                $sheet->setCellValue("E{$nameRow}", 'Supervisor QC');
            },
        ];
    }
}