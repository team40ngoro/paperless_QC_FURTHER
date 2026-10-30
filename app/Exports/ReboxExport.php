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

class ReboxExport implements FromCollection, WithHeadings, WithEvents
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
            $jam = $item->jam ?? '-';
            $bestBefore = $item->best_before ? Carbon::parse($item->best_before)->format('d/m/Y') : '-';

            $isiJumlah = $item->isi_jumlah;
            if ($isiJumlah == '✔' || $isiJumlah == '&#10004;') {
                $isiJumlah = 'OK';
            } elseif ($isiJumlah == '✘' || $isiJumlah == '&#10008;') {
                $isiJumlah = 'Tidak OK';
            } else {
                $isiJumlah = $isiJumlah ?? '-';
            }

            $labelisasi = $item->labelisasi;
            if ($labelisasi == '✔' || $labelisasi == '&#10004;') {
                $labelisasi = 'OK';
            } elseif ($labelisasi == '✘' || $labelisasi == '&#10008;') {
                $labelisasi = 'Tidak OK';
            } else {
                $labelisasi = $labelisasi ?? '-';
            }

            $rows->push([
                ++$no,
                $jam,
                $item->nama_produk ?? '-',
                $item->kode_produksi ?? '-',
                $bestBefore,
                $isiJumlah,
                $labelisasi,
            ]);
        }

        return $rows;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Waktu',
            'Nama Produk',
            'Kode Produksi',
            'Best Before',
            'Kesesuaian dan Jumlah Produk',
            'Labelisasi',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'G';

                $sheet->insertNewRowBefore(1, 7);

                $planTitle = 'FOOD DIVISION ' . strtoupper($this->filterInfo['plan_nama'] ?? 'SALATIGA');
                $sheet->mergeCells("A1:{$lastCol}1")->setCellValue('A1', 'PT. CHAROEN POKPHAND INDONESIA');
                $sheet->mergeCells("A2:{$lastCol}2")->setCellValue('A2', $planTitle);
                $sheet->mergeCells("A3:{$lastCol}3")->setCellValue('A3', 'VERIFIKASI PROSES REBOX');
                $sheet->getStyle('A1:A3')->getFont()->setBold(true);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:A3')->getFont()->setSize(12);

                $tanggalDisplay = '-';
                if (!empty($this->filterInfo['tanggal']) && str_contains($this->filterInfo['tanggal'], 's/d')) {
                    $tanggalDisplay = $this->filterInfo['tanggal'];
                } elseif (!empty($this->filterInfo['tanggal'])) {
                    try {
                        $tanggalDisplay = Carbon::parse($this->filterInfo['tanggal'])->format('d-m-Y');
                    } catch (\Throwable $e) {
                        $tanggalDisplay = $this->filterInfo['tanggal'];
                    }
                } else {
                    $tglMulai = $this->filterInfo['tanggal_mulai'] ?? null;
                    $tglAkhir = $this->filterInfo['tanggal_akhir'] ?? null;

                    $formatDate = function ($dateStr) {
                        if (empty($dateStr) || $dateStr === '-') return null;
                        if (str_contains($dateStr, 's/d')) return $dateStr;
                        try {
                            return Carbon::parse($dateStr)->format('d-m-Y');
                        } catch (\Throwable $e) {
                            return $dateStr;
                        }
                    };

                    $fMulai = $formatDate($tglMulai);
                    $fAkhir = $formatDate($tglAkhir);

                    if ($fMulai && $fAkhir) {
                        $tanggalDisplay = $fMulai . ' s/d ' . $fAkhir;
                    } elseif ($fMulai) {
                        $tanggalDisplay = $fMulai;
                    } elseif ($fAkhir) {
                        $tanggalDisplay = $fAkhir;
                    }
                }

                $sheet->setCellValue('A5', 'Hari / Tanggal');
                $sheet->setCellValue('B5', ': ' . $tanggalDisplay);
                $sheet->mergeCells('B5:' . $lastCol . '5');

                $shiftVal = $this->filterInfo['shift'] ?? 'Semua Shift';
                $sheet->setCellValue('A6', 'Shift');
                $sheet->setCellValue('B6', ': ' . $shiftVal);
                $sheet->mergeCells('B6:' . $lastCol . '6');

                $headerRow = 8;
                $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
                $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $lastDataRow = $sheet->getHighestRow();
                $sheet->getStyle('A' . ($headerRow + 1) . ':' . $lastCol . $lastDataRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A' . ($headerRow + 1) . ':' . $lastCol . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle('C' . ($headerRow + 1) . ':C' . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $kodeFormRow = $lastDataRow + 2;
                $sheet->setCellValue($lastCol . $kodeFormRow, $this->filterInfo['kode_form'] ?? '-');
                $sheet->getStyle($lastCol . $kodeFormRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle($lastCol . $kodeFormRow)->getFont()->setBold(true)->setSize(8);

                $sigRow = $lastDataRow + 5;
                $sheet->setCellValue("A{$sigRow}", 'Dibuat Oleh: QC INSPEKTOR');
                $sheet->setCellValue("C{$sigRow}", 'Diketahui Oleh: Produksi');
                $sheet->setCellValue("E{$sigRow}", 'Disetujui Oleh: Supervisor QC');
                $sheet->getStyle("A{$sigRow}:{$lastCol}{$sigRow}")->getFont()->setBold(true)->setSize(10);
                $sheet->getStyle("A{$sigRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$sigRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$sigRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $lineRow = $sigRow + 2;
                for ($col = 'A'; $col <= $lastCol; $col++) {
                    if (in_array($col, ['A', 'C', 'E'])) {
                        $sheet->getStyle($col . $lineRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                    }
                }

                $nameRow = $lineRow + 1;
                $sheet->setCellValue("A{$nameRow}", 'QC Inspector');
                $sheet->setCellValue("C{$nameRow}", 'FM/FL Produksi');
                $sheet->setCellValue("E{$nameRow}", 'Supervisor QC');
                $sheet->getStyle("A{$nameRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$nameRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$nameRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A{$nameRow}:{$lastCol}{$nameRow}")->getFont()->setSize(10);

                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(18);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(25);
                $sheet->getColumnDimension('G')->setWidth(15);
            },
        ];
    }
}
