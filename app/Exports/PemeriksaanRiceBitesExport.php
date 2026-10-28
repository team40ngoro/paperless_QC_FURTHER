<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * Layout export ini dibuat mengikuti persis
 * qc-sistem.pemeriksaan_rice_bites.export_pdf: header perusahaan, judul,
 * info filter, satu tabel utama dengan header 2 baris (grup "Bahan Baku" &
 * "Premix"), baris ganda per data ketika bahan_baku/premix lebih dari satu
 * (meniru rowspan di PDF), kode form, dan blok tanda tangan.
 *
 * Karena strukturnya pakai rowspan/colspan yang tidak bisa direpresentasikan
 * oleh WithHeadings/WithMapping biasa, sheet ini dirakit manual lewat
 * AfterSheet.
 */
class PemeriksaanRiceBitesExport implements FromCollection, WithEvents, WithColumnWidths
{
    /** Total kolom tabel utama: No, Jam, Batch, No Cooking Cycle, Bahan Baku (3), Premix (2),
     *  Parameter Nitrogen, Jumlah Inject Nitrogen, RPM Cooking Cattle, Cold Mixing,
     *  Suhu Aktual Adonan, Suhu Adonan Pencampuran, Rata-rata Suhu, Hasil Pencampuran, Catatan */
    protected const TOTAL_COLUMNS = 18;

    protected $records;
    protected array $filters;
    protected string $kodeForm;
    protected string $plantName;

    public function __construct($records, array $filters = [], ?string $kodeForm = null, ?string $plantName = null)
    {
        $this->records = $records;
        $this->filters = $filters;
        $this->kodeForm = $kodeForm ?? ($filters['kode_form'] ?? '-');
        $this->plantName = $plantName ?? 'UNKNOWN';
    }

    /**
     * Sheet dibangun manual lewat AfterSheet, jadi di sini cukup kembalikan
     * collection kosong supaya package tidak menulis apa pun secara otomatis.
     */
    public function collection()
    {
        return collect();
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 8,   // Jam
            'C' => 10,  // Batch
            'D' => 14,  // No Cooking Cycle
            'E' => 20,  // Bahan Baku - Nama
            'F' => 14,  // Bahan Baku - Kode Produksi
            'G' => 26,  // Bahan Baku - Detail
            'H' => 20,  // Premix - Nama
            'I' => 22,  // Premix - Detail
            'J' => 14,  // Parameter Nitrogen
            'K' => 16,  // Jumlah Inject Nitrogen
            'L' => 14,  // RPM Cooking Cattle
            'M' => 12,  // Cold Mixing
            'N' => 18,  // Suhu Aktual Adonan
            'O' => 20,  // Suhu Adonan Pencampuran
            'P' => 14,  // Rata-rata Suhu (°C)
            'Q' => 14,  // Hasil Pencampuran
            'R' => 22,  // Catatan
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = $this->columnLetter(self::TOTAL_COLUMNS); // 'R'

                $row = 1;
                $row = $this->writeCompanyHeader($sheet, $row, $lastColumn);
                $row = $this->writeFilterInfo($sheet, $row, $lastColumn);
                $row = $this->writeMainTable($sheet, $row, $lastColumn);
                $row = $this->writeKodeForm($sheet, $row, $lastColumn);
                $row += 1;
                $this->writeSignatureBlock($sheet, $row, $lastColumn);
            },
        ];
    }

    protected function writeCompanyHeader($sheet, int $row, string $lastColumn): int
    {
        $sheet->setCellValue("A{$row}", 'PT. CHAROEN POKPHAND INDONESIA');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(13);
        $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        $sheet->setCellValue("A{$row}", 'FOOD DIVISION ' . strtoupper($this->plantName));
        $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        $sheet->setCellValue("A{$row}", strtoupper($this->plantName) . ' - INDONESIA');
        $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row += 2;

        $sheet->setCellValue("A{$row}", 'PEMERIKSAAN PRODUK RICE BITES DI COOKING MIXER');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);
        $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row += 2;

        return $row;
    }

    protected function writeFilterInfo($sheet, int $row, string $lastColumn): int
    {
        $tanggal = $this->filters['tanggal'] ?? 'Semua Tanggal';
        $shift = $this->filters['shift'] ?? 'Semua Shift';
        $produk = $this->filters['produk'] ?? 'Semua Produk';

        $sheet->setCellValue(
            "A{$row}",
            "Informasi Filter  -  Tanggal: {$tanggal}   |   Shift: {$shift}   |   Produk: {$produk}"
        );
        $sheet->getStyle("A{$row}")->getFont()->setSize(9)->setItalic(true);
        $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
        $row += 2;

        return $row;
    }

    /**
     * Tabel utama dengan header 2 baris (grup "Bahan Baku" & "Premix"), dan
     * baris berulang per record mengikuti jumlah bahan_baku/premix terbanyak,
     * persis logika rowspan di export_pdf.blade.php.
     */
    protected function writeMainTable($sheet, int $row, string $lastColumn): int
    {
        $headingRow1 = $row;
        $headingRow2 = $row + 1;

        // Baris header pertama (kolom yang rowspan ke bawah 2 baris)
        $sheet->setCellValue("A{$headingRow1}", 'No');
        $sheet->setCellValue("B{$headingRow1}", 'Jam');
        $sheet->setCellValue("C{$headingRow1}", 'Batch');
        $sheet->setCellValue("D{$headingRow1}", 'No Cooking Cycle');
        $sheet->setCellValue("E{$headingRow1}", 'Bahan Baku');
        $sheet->setCellValue("H{$headingRow1}", 'Premix');
        $sheet->setCellValue("J{$headingRow1}", 'Parameter Nitrogen');
        $sheet->setCellValue("K{$headingRow1}", 'Jumlah Inject Nitrogen');
        $sheet->setCellValue("L{$headingRow1}", 'RPM Cooking Cattle');
        $sheet->setCellValue("M{$headingRow1}", 'Cold Mixing');
        $sheet->setCellValue("N{$headingRow1}", 'Suhu Aktual Adonan');
        $sheet->setCellValue("O{$headingRow1}", 'Suhu Adonan Pencampuran');
        $sheet->setCellValue("P{$headingRow1}", 'Rata-rata Suhu (°C)');
        $sheet->setCellValue("Q{$headingRow1}", 'Hasil Pencampuran');
        $sheet->setCellValue("R{$headingRow1}", 'Catatan');

        // Baris header kedua (sub-kolom Bahan Baku & Premix)
        $sheet->setCellValue("E{$headingRow2}", 'Nama');
        $sheet->setCellValue("F{$headingRow2}", 'Kode Produksi');
        $sheet->setCellValue("G{$headingRow2}", 'Detail');
        $sheet->setCellValue("H{$headingRow2}", 'Nama');
        $sheet->setCellValue("I{$headingRow2}", 'Detail');

        foreach (['A', 'B', 'C', 'D', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R'] as $col) {
            $sheet->mergeCells("{$col}{$headingRow1}:{$col}{$headingRow2}");
        }
        $sheet->mergeCells("E{$headingRow1}:G{$headingRow1}");
        $sheet->mergeCells("H{$headingRow1}:I{$headingRow1}");

        $this->styleHeadingRow($sheet, $headingRow1, $headingRow2, $lastColumn);

        $dataStartRow = $headingRow2 + 1;
        $row = $dataStartRow;

        foreach ($this->records as $index => $item) {
            $bahanBaku = is_array($item->bahan_baku) ? $item->bahan_baku : [];
            $premix = is_array($item->premix) ? $item->premix : [];
            $maxRows = max(count($bahanBaku), count($premix), 1);

            $groupStartRow = $row;
            $groupEndRow = $row + $maxRows - 1;

            for ($i = 0; $i < $maxRows; $i++) {
                if ($i === 0) {
                    $sheet->setCellValue("A{$row}", $index + 1);
                    $sheet->setCellValue("B{$row}", $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-');
                    $sheet->setCellValue("C{$row}", $item->batch ?? '-');
                    $sheet->setCellValue("D{$row}", $item->no_cooking_cycle ?? '-');
                    $sheet->setCellValue("J{$row}", $item->parameter_nitrogen ?? '-');
                    $sheet->setCellValue("K{$row}", $item->jumlah_inject_nitrogen ?? '-');
                    $sheet->setCellValue("L{$row}", $item->rpm_cooking_cattle ?? '-');
                    $sheet->setCellValue("M{$row}", $item->cold_mixing ?? '-');
                    $sheet->setCellValue("N{$row}", $this->formatArrayOrString($item->suhu_aktual_adonan));
                    $sheet->setCellValue("O{$row}", $this->formatArrayOrString($item->suhu_adonan_pencampuran));
                    $sheet->setCellValue("P{$row}", $item->rata_rata_suhu !== null ? number_format((float) $item->rata_rata_suhu, 2) : '-');
                    $sheet->setCellValue("Q{$row}", in_array($item->hasil_pencampuran, ['OK', 'Tidak OK'], true) ? $item->hasil_pencampuran : '-');
                    $sheet->setCellValue("R{$row}", $item->catatan ?? '-');
                }

                if (isset($bahanBaku[$i])) {
                    $sheet->setCellValue("E{$row}", $bahanBaku[$i]['nama'] ?? '-');
                    $sheet->setCellValue("F{$row}", $bahanBaku[$i]['kode_produksi'] ?? '-');
                    $sheet->setCellValue("G{$row}", 'Berat: ' . ($bahanBaku[$i]['berat'] ?? '-')
                        . "\nSuhu: " . ($bahanBaku[$i]['suhu'] ?? '-')
                        . "\nKondisi: " . ($bahanBaku[$i]['kondisi'] ?? '-'));
                } else {
                    $sheet->setCellValue("E{$row}", '-');
                    $sheet->setCellValue("F{$row}", '-');
                    $sheet->setCellValue("G{$row}", '-');
                }

                if (isset($premix[$i])) {
                    $sheet->setCellValue("H{$row}", $premix[$i]['nama'] ?? '-');
                    $sheet->setCellValue("I{$row}", 'Berat: ' . ($premix[$i]['berat'] ?? '-')
                        . "\nKondisi: " . ($premix[$i]['kondisi'] ?? '-'));
                } else {
                    $sheet->setCellValue("H{$row}", '-');
                    $sheet->setCellValue("I{$row}", '-');
                }

                $row++;
            }

            if ($maxRows > 1) {
                foreach (['A', 'B', 'C', 'D', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R'] as $col) {
                    $sheet->mergeCells("{$col}{$groupStartRow}:{$col}{$groupEndRow}");
                }
            }

            foreach (['A', 'B', 'C', 'D', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R'] as $col) {
                $sheet->getStyle("{$col}{$groupStartRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            }
        }

        $lastDataRow = $row - 1;
        $this->applyTableBorders($sheet, $headingRow1, $lastDataRow, $lastColumn);

        return $row;
    }

    protected function formatArrayOrString($value): string
    {
        if (is_array($value)) {
            $filtered = implode(', ', array_filter($value));

            return $filtered !== '' ? $filtered : '-';
        }

        return $value ?: '-';
    }

    protected function writeKodeForm($sheet, int $row, string $lastColumn): int
    {
        $row += 1;
        $sheet->setCellValue("{$lastColumn}{$row}", $this->kodeForm);
        $sheet->getStyle("{$lastColumn}{$row}")->getFont()->setBold(true);
        $sheet->getStyle("{$lastColumn}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return $row + 1;
    }

    /**
     * Blok "Dibuat Oleh / Diketahui Oleh / Disetujui Oleh" seperti signature-table
     * di PDF (tanpa QR code, karena Excel bukan dokumen final bertanda tangan).
     */
    protected function writeSignatureBlock($sheet, int $startRow, string $lastColumn): void
    {
        [$qcName, $produksiName, $spvName] = $this->resolveApproverNames();

        $totalColumns = Coordinate::columnIndexFromString($lastColumn);
        $third = (int) ceil($totalColumns / 3);
        $col1Start = 'A';
        $col1End = $this->columnLetter($third);
        $col2Start = $this->columnLetter($third + 1);
        $col2End = $this->columnLetter($third * 2);
        $col3Start = $this->columnLetter($third * 2 + 1);
        $col3End = $lastColumn;

        $labelRow = $startRow;
        $nameRow = $startRow + 3;

        $sheet->setCellValue("{$col1Start}{$labelRow}", 'Dibuat Oleh:');
        $sheet->setCellValue("{$col2Start}{$labelRow}", 'Diketahui Oleh:');
        $sheet->setCellValue("{$col3Start}{$labelRow}", 'Disetujui Oleh:');

        $sheet->setCellValue("{$col1Start}{$nameRow}", $qcName);
        $sheet->setCellValue("{$col2Start}{$nameRow}", $produksiName);
        $sheet->setCellValue("{$col3Start}{$nameRow}", $spvName);

        foreach ([
            [$col1Start, $col1End],
            [$col2Start, $col2End],
            [$col3Start, $col3End],
        ] as [$start, $end]) {
            $sheet->mergeCells("{$start}{$labelRow}:{$end}{$labelRow}");
            $sheet->mergeCells("{$start}{$nameRow}:{$end}{$nameRow}");
            $sheet->getStyle("{$start}{$labelRow}:{$end}{$labelRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("{$start}{$nameRow}:{$end}{$nameRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("{$start}{$nameRow}")->getFont()->setBold(true);
        }
    }

    /**
     * Sama seperti loop pencarian approver di export_pdf.blade.php: ambil
     * approver pertama yang ditemukan lalu berhenti.
     */
    protected function resolveApproverNames(): array
    {
        $qcApproverName = 'QC Inspector';
        $produksiApproverName = 'SPV/Foreman/Forelady Produksi';
        $spvApproverName = 'Supervisor QC';

        $qcFound = $produksiFound = $spvFound = false;

        foreach ($this->records as $item) {
            if (!$qcFound && $item->approved_by_qc && $item->qcApprover) {
                $qcApproverName = $item->qcApprover->name;
                $qcFound = true;
            }
            if (!$produksiFound && $item->approved_by_produksi && $item->produksiApprover) {
                $produksiApproverName = $item->produksiApprover->name;
                $produksiFound = true;
            }
            if (!$spvFound && $item->approved_by_spv && $item->spvApprover) {
                $spvApproverName = $item->spvApprover->name;
                $spvFound = true;
            }
            if ($qcFound && $produksiFound && $spvFound) {
                break;
            }
        }

        return [$qcApproverName, $produksiApproverName, $spvApproverName];
    }

    protected function styleHeadingRow($sheet, int $row1, int $row2, string $lastColumn): void
    {
        $range = "A{$row1}:{$lastColumn}{$row2}";
        $sheet->getStyle($range)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('28A745');
        $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
    }

    protected function applyTableBorders($sheet, int $headingRow, int $lastDataRow, string $lastColumn): void
    {
        $range = "A{$headingRow}:{$lastColumn}{$lastDataRow}";
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A' . ($headingRow + 2) . ":{$lastColumn}{$lastDataRow}")
            ->getAlignment()
            ->setWrapText(true);
    }

    protected function columnLetter(int $index): string
    {
        return Coordinate::stringFromColumnIndex($index);
    }
}