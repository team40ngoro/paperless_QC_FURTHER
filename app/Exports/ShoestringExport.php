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

class ShoestringExport implements FromCollection, WithHeadings, WithEvents
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
            $shift = $item->shift ? $item->shift->shift : '-';
            $bestBefore = $item->best_before ? Carbon::parse($item->best_before)->format('d/m/Y') : '-';

            // Flatten defect qty map into readable string
            $qtyMap = is_array($item->sampling_defect_qty) ? $item->sampling_defect_qty : [];
            $defectParts = [];
            foreach ($qtyMap as $defectKey => $qty) {
                if (is_numeric($defectKey)) {
                    $defect = \App\Models\DataDefect::find($defectKey);
                    $name = $defect ? $defect->jenis_defect : 'Defect #' . $defectKey;
                } else {
                    $name = $defectKey;
                }
                $defectParts[] = $name . ': ' . $qty;
            }
            $defectStr = !empty($defectParts) ? implode('; ', $defectParts) : '-';

            $rows->push([
                ++$no,
                $jam,
                $item->nama_produsen ?? '-',
                $item->kode_produksi ?? '-',
                $bestBefore,
                $shift,
                $defectStr,
                $item->total_defect ?? '-',
                $item->catatan ?? '-',
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
            'Nama Produsen',
            'Kode Produksi',
            'Tanggal Expired',
            'Shift',
            'Defect & Jumlah',
            'Total Defect',
            'Catatan',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'I';

                $sheet->insertNewRowBefore(1, 7);

                $planTitle = 'FOOD DIVISION ' . strtoupper($this->filterInfo['plan_nama'] ?? 'SALATIGA');
                $sheet->mergeCells("A1:{$lastCol}1")->setCellValue('A1', 'PT. CHAROEN POKPHAND INDONESIA');
                $sheet->mergeCells("A2:{$lastCol}2")->setCellValue('A2', $planTitle);
                $sheet->mergeCells("A3:{$lastCol}3")->setCellValue('A3', 'PEMERIKSAAN KEDATANGAN BAHAN BAKU KENTANG');
                $sheet->getStyle('A1:A3')->getFont()->setBold(true);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:A3')->getFont()->setSize(12);

                $tanggalDisplay = '-';
                // Prioritas: jika 'tanggal' sudah berisi range (mengandung s/d), gunakan langsung
                if (!empty($this->filterInfo['tanggal']) && str_contains($this->filterInfo['tanggal'], 's/d')) {
                    $tanggalDisplay = $this->filterInfo['tanggal'];
                } elseif (!empty($this->filterInfo['tanggal'])) {
                    // 'tanggal' hanya single date, coba parse
                    try {
                        $tanggalDisplay = Carbon::parse($this->filterInfo['tanggal'])->format('d-m-Y');
                    } catch (\Throwable $e) {
                        $tanggalDisplay = $this->filterInfo['tanggal'];
                    }
                } else {
                    $tanggalMulai = $this->filterInfo['tanggal_mulai'] ?? null;
                    $tanggalAkhir = $this->filterInfo['tanggal_akhir'] ?? null;

                    $formatDateSafe = function ($dateStr) {
                        if (empty($dateStr) || $dateStr === '-') return null;
                        if (str_contains($dateStr, 's/d')) return $dateStr;
                        try {
                            return Carbon::parse($dateStr)->format('d-m-Y');
                        } catch (\Throwable $e) {
                            return $dateStr;
                        }
                    };

                    $fMulai = $formatDateSafe($tanggalMulai);
                    $fAkhir = $formatDateSafe($tanggalAkhir);

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

                // Left align for text columns: Nama Produsen (C), Defect & Jumlah (G), Catatan (I)
                $sheet->getStyle('C' . ($headerRow + 1) . ':C' . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('G' . ($headerRow + 1) . ':G' . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getStyle('I' . ($headerRow + 1) . ':I' . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_TOP);

                $kodeFormRow = $lastDataRow + 2;
                $sheet->setCellValue($lastCol . $kodeFormRow, $this->filterInfo['kode_form'] ?? '-');
                $sheet->getStyle($lastCol . $kodeFormRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle($lastCol . $kodeFormRow)->getFont()->setBold(true)->setSize(8);

                $sigRow = $lastDataRow + 5;
                $sheet->setCellValue("A{$sigRow}", 'Dibuat Oleh: QC INSPEKTOR');
                $sheet->setCellValue("D{$sigRow}", 'Diketahui Oleh: Produksi');
                $sheet->setCellValue("G{$sigRow}", 'Disetujui Oleh: Supervisor QC');
                $sheet->getStyle("A{$sigRow}:{$lastCol}{$sigRow}")->getFont()->setBold(true)->setSize(10);
                $sheet->getStyle("A{$sigRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D{$sigRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("G{$sigRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $lineRow = $sigRow + 2;
                for ($col = 'A'; $col <= $lastCol; $col++) {
                    if (in_array($col, ['A', 'D', 'G'])) {
                        $sheet->getStyle($col . $lineRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                    }
                }

                $nameRow = $lineRow + 1;
                $sheet->setCellValue("A{$nameRow}", 'QC Inspector');
                $sheet->setCellValue("D{$nameRow}", 'FM/FL Produksi');
                $sheet->setCellValue("G{$nameRow}", 'Supervisor QC');
                $sheet->getStyle("A{$nameRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D{$nameRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("G{$nameRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A{$nameRow}:{$lastCol}{$nameRow}")->getFont()->setSize(10);

                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(18);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(10);
                $sheet->getColumnDimension('G')->setWidth(30);
                $sheet->getColumnDimension('H')->setWidth(12);
                $sheet->getColumnDimension('I')->setWidth(25);
            },
        ];
    }
}
