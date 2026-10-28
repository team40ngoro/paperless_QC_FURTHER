<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KontrolSanitasiExport implements FromCollection, WithHeadings, WithEvents
{
    protected Collection $rows;

    public function __construct(protected Collection $data, protected array $filterInfo)
    {
        $this->rows = $this->flatten();
    }

    protected function flatten(): Collection
    {
        $rows = collect();
        $no = 0;

        foreach ($this->data as $item) {
            if ($item->entries && is_array($item->entries)) {
                foreach ($item->entries as $entry) {
                    $rows->push([
                        ++$no,
                        $entry['jam'] ?? '-',
                        ($entry['suhu_air'] ?? '-') . '°C',
                        $entry['kadar_klorin_food_basin'] ?? '-',
                        $entry['kadar_klorin_hand_basin'] ?? '-',
                        $entry['hasil_verifikasi'] ?? '-',
                        $entry['keterangan'] ?? '-',
                    ]);
                }
            } else {
                $rows->push([
                    ++$no,
                    $item->jam ? (is_string($item->jam) ? Carbon::parse($item->jam)->format('H:i') : $item->jam->format('H:i')) : '-',
                    ($item->suhu_air ?? '-') . '°C',
                    $item->kadar_klorin_food_basin ?? '-',
                    $item->kadar_klorin_hand_basin ?? '-',
                    $item->hasil_verifikasi ?? '-',
                    $item->keterangan ?? '-',
                ]);
            }
        }

        return $rows;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['No', 'Waktu', 'Suhu Air', 'Kadar Klorin Food Basin', 'Kadar Klorin Hand Basin', 'Hasil Verifikasi', 'Keterangan'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'G';

                $sheet->insertNewRowBefore(1, 5);

                $sheet->mergeCells("A1:{$lastCol}1")->setCellValue('A1', 'PT. CHAROEN POKPHAND INDONESIA');
                $sheet->mergeCells("A2:{$lastCol}2")->setCellValue('A2', 'FOOD DIVISION ' . strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN'));
                $sheet->mergeCells("A3:{$lastCol}3")->setCellValue('A3', 'VERIFIKASI KONTROL SANITASI');
                $sheet->getStyle('A1:A3')->getFont()->setBold(true);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A4', 'Tanggal: ' . $this->filterInfo['tanggal'] . '   |   Shift: ' . $this->filterInfo['shift']);

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