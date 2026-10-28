<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PemeriksaanBendaAsingExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithColumnWidths
{
    // Header rows sebelum data (judul perusahaan + filter info)
    private int $headerOffset = 5;
    // Nomor baris heading kolom
    private int $headingRow;

    public function __construct(
        protected Collection $data,
        protected array $filterInfo
    ) {
        $this->headingRow = $this->headerOffset + 1; // baris 6
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Jam',
            'Produk',
            'RM',
            'Berat (gram)',
            'Kode Produksi',
            'Jenis Kontaminasi',
            'Ukuran',
            'Ditemukan',
            'Jml Terdampak',
            'Diketahui Oleh',
            'Analisa Masalah',
            'Koreksi',
            'Tindakan Korektif',
            'Bukti',
        ];
    }

    public function map($item): array
    {
        static $no = 0;
        return [
            ++$no,
            $item->jam ? Carbon::parse($item->jam)->format('H:i') : '-',
            $item->produk->nama_produk ?? '-',
            $item->rm ?? '-',
            $item->berat !== null && $item->berat !== '' ? $item->berat : '-',
            $item->kode_produksi ?? '-',
            $item->jenis_kontaminasi ?? '-',
            $item->ukuran_kontaminasi ?? '-',
            $item->ditemukan ?? '-',
            $item->jumlah_terdampak ?? '-',
            $item->diketahui ?? '-',
            $item->analisa_masalah ?? '-',
            $item->koreksi ?? '-',
            $item->tindak_korektif ?? '-',
            $item->bukti ? '(lihat gambar)' : '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 8,
            'C' => 20,
            'D' => 15,
            'E' => 12,
            'F' => 18,
            'G' => 20,
            'H' => 12,
            'I' => 18,
            'J' => 14,
            'K' => 18,
            'L' => 25,
            'M' => 18,
            'N' => 25,
            'O' => 18,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                /** @var Worksheet $sheet */
                $sheet = $event->sheet->getDelegate();
                $lastCol      = 'O';
                $lastColIndex = 15; // O = kolom ke-15
                $headerOffset = $this->headerOffset;
                $headingRow   = $this->headingRow;

                // ── 1. Sisipkan baris header di atas ──────────────────────────────
                $sheet->insertNewRowBefore(1, $headerOffset);

                // ── 2. Judul perusahaan ───────────────────────────────────────────
                $planName = strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN');

                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->setCellValue('A1', 'PT. CHAROEN POKPHAND INDONESIA');
                $this->applyTitleStyle($sheet, 'A1', '000000', 'FFFFFF', 14);

                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->setCellValue('A2', "FOOD DIVISION {$planName}");
                $this->applyTitleStyle($sheet, 'A2', '1F3864', 'FFFFFF', 12);

                $sheet->mergeCells("A3:{$lastCol}3");
                $sheet->setCellValue('A3', 'PEMERIKSAAN KONTAMINASI BENDA ASING');
                $this->applyTitleStyle($sheet, 'A3', '2E75B6', 'FFFFFF', 11);

                // ── 3. Info filter ────────────────────────────────────────────────
                $sheet->mergeCells("A4:{$lastCol}4");
                $sheet->setCellValue('A4',
                    'Tanggal: ' . ($this->filterInfo['tanggal'] ?? '-') .
                    '   |   Shift: ' . ($this->filterInfo['shift'] ?? 'Semua Shift') .
                    '   |   Produk: ' . ($this->filterInfo['produk'] ?? 'Semua Produk')
                );
                $sheet->getStyle('A4')->applyFromArray([
                    'font'      => ['bold' => false, 'size' => 9, 'color' => ['argb' => 'FF333333']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9E1F2']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(4)->setRowHeight(18);

                $sheet->mergeCells("A5:{$lastCol}5");
                $sheet->setCellValue('A5', 'Kode Form: ' . ($this->filterInfo['kode_form'] ?? '-'));
                $sheet->getStyle('A5')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'italic' => true, 'color' => ['argb' => 'FF555555']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEAF0FB']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(5)->setRowHeight(16);

                // ── 4. Style heading kolom (baris 6) ─────────────────────────────
                $headingRange = "A{$headingRow}:{$lastCol}{$headingRow}";
                $sheet->getStyle($headingRange)->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2E75B6']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders'   => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFFFFFFF'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension($headingRow)->setRowHeight(28);

                // ── 5. Style + border seluruh data ────────────────────────────────
                $lastDataRow = $sheet->getHighestRow();
                if ($lastDataRow > $headingRow) {
                    $dataRange = "A" . ($headingRow + 1) . ":{$lastCol}{$lastDataRow}";
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'font'      => ['size' => 8],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                            'wrapText'   => true,
                        ],
                        'borders'   => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['argb' => 'FFAAAAAA'],
                            ],
                        ],
                    ]);

                    // Zebra striping (baris genap lebih terang)
                    for ($r = $headingRow + 1; $r <= $lastDataRow; $r++) {
                        if ($r % 2 === 0) {
                            $sheet->getStyle("A{$r}:{$lastCol}{$r}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFF2F7FD');
                        }
                    }

                    // Kolom No rata tengah
                    $sheet->getStyle("A" . ($headingRow + 1) . ":A{$lastDataRow}")
                          ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // ── 6. Embed gambar bukti ─────────────────────────────────────────
                $rowIndex = $headingRow + 1;
                foreach ($this->data as $item) {
                    if ($item->bukti) {
                        $imagePath = storage_path('app/public/' . $item->bukti);
                        if (file_exists($imagePath)) {
                            // Tinggi baris disesuaikan dengan gambar
                            $sheet->getRowDimension($rowIndex)->setRowHeight(55);

                            $drawing = new Drawing();
                            $drawing->setName('Bukti');
                            $drawing->setDescription('Bukti Kontaminasi');
                            $drawing->setPath($imagePath);
                            $drawing->setHeight(50);
                            $drawing->setCoordinates('O' . $rowIndex);
                            $drawing->setOffsetX(3);
                            $drawing->setOffsetY(3);
                            $drawing->setWorksheet($sheet);

                            // Hapus teks placeholder di sel O
                            $sheet->setCellValue('O' . $rowIndex, '');
                        }
                    }
                    $rowIndex++;
                }

                // ── 7. Baris tanda tangan ─────────────────────────────────────────
                $sigRow  = $lastDataRow + 3;
                $nameRow = $sigRow + 5;

                foreach (['A', 'F', 'K'] as $col) {
                    $sheet->getStyle("{$col}{$sigRow}")->getFont()->setBold(true);
                }

                $sheet->setCellValue("A{$sigRow}", 'Dibuat Oleh:');
                $sheet->setCellValue("F{$sigRow}", 'Diketahui Oleh:');
                $sheet->setCellValue("K{$sigRow}", 'Disetujui Oleh:');

                // Garis tanda tangan
                $lineRow = $sigRow + 4;
                foreach (['A', 'F', 'K'] as $col) {
                    $endCol = ($col === 'A') ? 'E' : (($col === 'F') ? 'J' : $lastCol);
                    $sheet->mergeCells("{$col}{$lineRow}:{$endCol}{$lineRow}");
                    $sheet->getStyle("{$col}{$lineRow}")->getBorders()
                          ->getBottom()->setBorderStyle(Border::BORDER_THIN);
                }

                $sheet->setCellValue("A{$nameRow}", 'QC Inspector');
                $sheet->setCellValue("F{$nameRow}", 'SPV/Foreman/Forelady Produksi');
                $sheet->setCellValue("K{$nameRow}", 'Supervisor QC');

                foreach ([$sigRow, $lineRow, $nameRow] as $r) {
                    $sheet->getStyle("A{$r}:{$lastCol}{$r}")
                          ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // ── 8. Freeze pane di bawah heading ───────────────────────────────
                $sheet->freezePane('A' . ($headingRow + 1));

                // ── 9. Print setup ────────────────────────────────────────────────
                $sheet->getPageSetup()
                      ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                      ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                      ->setFitToPage(true)
                      ->setFitToWidth(1)
                      ->setFitToHeight(0);
            },
        ];
    }

    // ── Helper: apply title style ─────────────────────────────────────────────
    private function applyTitleStyle(
        Worksheet $sheet,
        string $cell,
        string $bgArgb,
        string $fontArgb,
        int $fontSize = 12
    ): void {
        $sheet->getStyle($cell)->applyFromArray([
            'font'      => [
                'bold'  => true,
                'size'  => $fontSize,
                'color' => ['argb' => 'FF' . ltrim($fontArgb, 'F')],
            ],
            'fill'      => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF' . ltrim($bgArgb, 'F')],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $rowNum = (int) preg_replace('/[^0-9]/', '', $cell);
        $sheet->getRowDimension($rowNum)->setRowHeight(22);
    }
}
