<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ThermometerExport implements FromCollection, WithHeadings, WithEvents
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
            $jam = $item->jam ? Carbon::parse($item->jam)->format('H:i') : '-';

            if ($item->entries && is_array($item->entries)) {
                foreach ($item->entries as $entry) {
                    $hasil = ($entry['hasil_pengecekan'] ?? 'ok') === 'ok' ? 'OK' : 'Tidak OK';
                    $rows->push([
                        ++$no,
                        $jam,
                        $entry['jenis'] ?? '-',
                        $entry['area'] ?? '-',
                        $entry['kode_thermometer'] ?? '-',
                        $entry['hasil_verifikasi_0'] ?? '-',
                        $entry['hasil_verifikasi_100'] ?? '-',
                        $hasil,
                    ]);
                }
            } else {
                $hasil = $item->hasil_pengecekan === 'ok' ? 'OK' : 'Tidak OK';
                $rows->push([
                    ++$no,
                    $jam,
                    $item->jenis ?? '-',
                    $item->area ?? '-',
                    $item->kode_thermometer ?? '-',
                    $item->hasil_verifikasi_0 ?? '-',
                    $item->hasil_verifikasi_100 ?? '-',
                    $hasil,
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
        return ['No', 'Jam', 'Jenis', 'Area', 'Kode Thermometer', 'Hasil Verifikasi 0°C', 'Hasil Verifikasi 100°C', 'Hasil Pengecekan'];
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
                $sheet->mergeCells("A3:{$lastCol}3")->setCellValue('A3', 'PEMERIKSAAN THERMOMETER');
                
                $headerStyle = [
                    'font' => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ];
                $sheet->getStyle('A1:A3')->applyFromArray($headerStyle);

                $sheet->setCellValue('A4', 'Tanggal: ' . $this->filterInfo['tanggal'] . '   |   Shift: ' . $this->filterInfo['shift']);
                $sheet->getStyle('A4')->getFont()->setSize(9);

                $dataStartRow = 6;
                $lastDataRow = $sheet->getHighestRow();

                $tableBorder = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ];

                $headerFill = [
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'f0f0f0'],
                    ],
                ];

                $sheet->getStyle("A{$dataStartRow}:{$lastCol}{$dataStartRow}")->applyFromArray(array_merge($tableBorder, $headerFill));
                $sheet->getStyle("A{$dataStartRow}:{$lastCol}{$dataStartRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$dataStartRow}:{$lastCol}{$lastDataRow}")->applyFromArray($tableBorder);
                
                $sheet->getStyle("A{$dataStartRow}:{$lastCol}{$lastDataRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);

                $sheet->getStyle("A{$dataStartRow}:{$lastCol}{$lastDataRow}")->getFont()->setSize(8);

                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(8);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(12);
                $sheet->getColumnDimension('G')->setWidth(12);
                $sheet->getColumnDimension('H')->setWidth(12);

                $sheet->getRowDimension($dataStartRow)->setRowHeight(20);
                for ($row = $dataStartRow + 1; $row <= $lastDataRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(25);
                }

                $kodeFormRow = $lastDataRow + 2;
                $sheet->setCellValue("A{$kodeFormRow}", $this->filterInfo['kode_form']);
                $sheet->getStyle("A{$kodeFormRow}")->getFont()->setBold(true)->setSize(9);

                $sigRow = $lastDataRow + 4;
                $sheet->setCellValue("A{$sigRow}", 'Dibuat Oleh:');
                $sheet->setCellValue("C{$sigRow}", 'Diketahui Oleh:');
                $sheet->setCellValue("E{$sigRow}", 'Disetujui Oleh:');
                $sheet->getStyle("A{$sigRow}:E{$sigRow}")->getFont()->setBold(true)->setSize(8);

                $nameRow = $sigRow + 4;
                $sheet->setCellValue("A{$nameRow}", 'QC Inspector');
                $sheet->setCellValue("C{$nameRow}", 'SPV/Foreman/Forelady Produksi');
                $sheet->setCellValue("E{$nameRow}", 'Supervisor QC');
                $sheet->getStyle("A{$nameRow}:E{$nameRow}")->getFont()->setSize(8);
            },
        ];
    }
}