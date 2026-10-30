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
 * Layout export ini sengaja dibuat identik dengan
 * qc-sistem.pemeriksaan-rheon-machine.export_pdf: header perusahaan, judul,
 * info filter, DUA tabel terpisah (tabel proses & tabel detail berat),
 * kode form, dan blok tanda tangan.
 *
 * Karena strukturnya "dua tabel berbeda header dalam satu halaman", sheet ini
 * dirakit manual lewat AfterSheet (bukan lewat WithHeadings/WithMapping tunggal),
 * supaya bisa meniru persis dua tabel pada PDF.
 */
class PemeriksaanRheonMachineExport implements FromCollection, WithEvents, WithColumnWidths
{
    /** Jumlah kolom tabel proses (tabel pertama, yang paling lebar) */
    protected const TOTAL_COLUMNS = 9;

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
            'B' => 24,  // Produk
            'C' => 12,  // Batch
            'D' => 16,  // Waktu / Rata-rata Dough
            'E' => 16,  // Inner / Rata-rata Filler
            'F' => 16,  // Outer / Rata-rata After Forming
            'G' => 18,  // Belt / Rata-rata After Frying
            'H' => 18,  // Extrusion Speed / Catatan
            'I' => 16,  // Jenis Cetakan
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = $this->columnLetter(self::TOTAL_COLUMNS); // 'I'

                $row = 1;
                $row = $this->writeCompanyHeader($sheet, $row, $lastColumn);
                $row = $this->writeFilterInfo($sheet, $row, $lastColumn);
                $row = $this->writeProsesTable($sheet, $row, $lastColumn);
                $row += 1;
                $row = $this->writeDetailBeratTable($sheet, $row);
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

        $sheet->setCellValue("A{$row}", 'PEMERIKSAAN RHEON MACHINE');
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
            "Filter yang Diterapkan  -  Tanggal: {$tanggal}   |   Shift: {$shift}   |   Produk: {$produk}"
        );
        $sheet->getStyle("A{$row}")->getFont()->setSize(9)->setItalic(true);
        $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
        $row += 2;

        return $row;
    }

    /**
     * Tabel pertama: No, Produk, Batch, Waktu, Inner, Outer, Belt, Extrusion
     * Speed, Jenis Cetakan — sama seperti tabel proses di export_pdf.blade.php.
     */
    protected function writeProsesTable($sheet, int $row, string $lastColumn): int
    {
        $headings = ['No', 'Produk', 'Batch', 'Waktu', 'Inner', 'Outer', 'Belt', 'Extrusion Speed', 'Jenis Cetakan'];
        $headingRow = $row;
        $sheet->fromArray($headings, null, "A{$headingRow}");
        $this->styleHeadingRow($sheet, $headingRow, $lastColumn);
        $row++;

        foreach ($this->records as $index => $item) {
            $sheet->fromArray([
                $index + 1,
                $item->produk->nama_produk ?? '-',
                $item->batch ?? '-',
                $item->pukul ?? '-',
                $item->inner ?? '-',
                $item->outer ?? '-',
                $item->belt ?? '-',
                $item->extrusion_speed ?? '-',
                $item->jenis_cetakan ?? '-',
            ], null, "A{$row}");
            $row++;
        }

        $this->applyTableBorders($sheet, $headingRow, $row - 1, $lastColumn);

        return $row;
    }

    /**
     * Tabel kedua: "DETAIL BERAT PEMERIKSAAN" — No, Produk, Batch, Rata-rata
     * Dough, Rata-rata Filler, Rata-rata After Forming, Rata-rata After
     * Frying, Catatan — sama seperti tabel kedua di export_pdf.blade.php.
     */
    protected function writeDetailBeratTable($sheet, int $row): int
    {
        $headings = [
            'No', 'Produk', 'Batch',
            'Rata-rata Dough (gram)', 'Rata-rata Filler (gram)',
            'Rata-rata After Forming (gram)', 'Rata-rata After Frying (gram)',
            'Catatan',
        ];
        $lastColumn = $this->columnLetter(count($headings)); // 'H'

        $sheet->setCellValue("A{$row}", 'DETAIL BERAT PEMERIKSAAN');
        $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E0E0E0');
        $row++;

        $headingRow = $row;
        $sheet->fromArray($headings, null, "A{$headingRow}");
        $this->styleHeadingRow($sheet, $headingRow, $lastColumn);
        $row++;

        foreach ($this->records as $index => $item) {
            $sheet->fromArray([
                $index + 1,
                $item->produk->nama_produk ?? '-',
                $item->batch ?? '-',
                number_format((float) ($item->rata_rata_dough ?? 0), 2),
                number_format((float) ($item->rata_rata_filler ?? 0), 2),
                number_format((float) ($item->rata_rata_after_forming ?? 0), 2),
                number_format((float) ($item->rata_rata_after_frying ?? 0), 2),
                $item->catatan ?? '-',
            ], null, "A{$row}");
            $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
            $row++;
        }

        $this->applyTableBorders($sheet, $headingRow, $row - 1, $lastColumn);

        return $row;
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

    protected function styleHeadingRow($sheet, int $row, string $lastColumn): void
    {
        $range = "A{$row}:{$lastColumn}{$row}";
        $sheet->getStyle($range)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('28A745');
        $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
    }

    protected function applyTableBorders($sheet, int $headingRow, int $lastDataRow, string $lastColumn): void
    {
        $range = "A{$headingRow}:{$lastColumn}{$lastDataRow}";
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A' . ($headingRow + 1) . ":{$lastColumn}{$lastDataRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);
    }

    protected function columnLetter(int $index): string
    {
        return Coordinate::stringFromColumnIndex($index);
    }
}