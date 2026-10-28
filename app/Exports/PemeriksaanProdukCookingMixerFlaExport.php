<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * Layout & kolom export ini sengaja dibuat identik dengan
 * qc-sistem.pemeriksaan-produk-cooking-mixer-fla.export_pdf
 * supaya versi Excel dan versi PDF menampilkan informasi yang sama persis.
 */
class PemeriksaanProdukCookingMixerFlaExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithEvents,
    WithColumnWidths
{
    /** Jumlah baris custom (header perusahaan, judul, info filter) di atas heading tabel */
    protected const HEADER_ROWS = 8;

    /** Total kolom pada tabel utama (harus sama dengan jumlah heading & mapping) */
    protected const TOTAL_COLUMNS = 14;

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

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'NO',
            'TANGGAL',
            'JAM',
            'SHIFT',
            'NAMA PRODUK',
            'KODE PRODUKSI',
            'DETAIL BAHAN & KODE PRODUKSI',
            'BERAT (GRAM)',
            'WAKTU (START/STOP)',
            'STATUS GAS',
            'SPEED (RPM)',
            'TEMPERATURE (°C)',
            'ORGANOLEPTIC',
            'CATATAN',
        ];
    }

    public function map($row): array
    {
        static $no = 1;

        $tanggal = $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') : '-';
        $jam = $row->jam ? \Carbon\Carbon::parse($row->jam)->format('H:i') : '-';
        $shift = $row->shift->shift ?? '-';
        $namaProduk = $row->namaFormulaFla->produk->nama_produk ?? '-';
        $kodeProduksi = $row->kode_produksi ?? '-';

        $detailBahan = $this->formatDetailBahan($row);

        $berat = ($row->berat ?? '-') . ' gram';

        $waktu = 'Start: ' . ($row->waktu_start ?? '-') . "\n" . 'Stop: ' . ($row->waktu_stop ?? '-');

        $statusGas = $row->status_gas ? 'ON' : 'OFF';

        $speed = ($row->speed ?? '-') . ' RPM';

        $temperature = 'T1: ' . ($row->temp_std_1 ?? '-') . "°C\n"
            . 'T2: ' . ($row->temp_std_2 ?? '-') . "°C\n"
            . 'T3: ' . ($row->temp_std_3 ?? '-') . '°C';

        $organoleptic = 'W: ' . ($row->organo_warna ?? '-') . "\n"
            . 'A: ' . ($row->organo_aroma ?? '-') . "\n"
            . 'T: ' . ($row->organo_tekstur ?? '-') . "\n"
            . 'R: ' . ($row->organo_rasa ?? '-');

        $catatan = $row->catatan ?? '-';

        return [
            $no++,
            $tanggal,
            $jam,
            $shift,
            $namaProduk,
            $kodeProduksi,
            $detailBahan,
            $berat,
            $waktu,
            $statusGas,
            $speed,
            $temperature,
            $organoleptic,
            $catatan,
        ];
    }

    /**
     * Sama persis dengan logika "Detail Bahan & Kode Produksi" pada export_pdf.blade.php.
     */
    protected function formatDetailBahan($row): string
    {
        $kodes = $row->getKodeProduksiBahanArray() ?: [];
        $bahans = $row->bahanFormulaFla?->getBahanFormulaArray() ?: [];
        $berats = $row->bahanFormulaFla?->getBeratFormulaArray() ?: [];

        if (!empty($bahans)) {
            $lines = [];
            foreach ($bahans as $idx => $bName) {
                $beratVal = (isset($berats[$idx]) && $berats[$idx] !== '') ? $berats[$idx] . ' kg' : '-';
                $kodeVal = (isset($kodes[$idx]) && $kodes[$idx] !== '') ? $kodes[$idx] : '-';
                $lines[] = "{$bName} ({$beratVal}): {$kodeVal}";
            }

            return implode("\n", $lines);
        }

        if (!empty($kodes)) {
            return implode("\n", array_map(fn ($k) => $k !== '' ? $k : '-', $kodes));
        }

        return '-';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 12,  // Tanggal
            'C' => 8,   // Jam
            'D' => 10,  // Shift
            'E' => 22,  // Nama Produk
            'F' => 16,  // Kode Produksi
            'G' => 38,  // Detail Bahan & Kode Produksi
            'H' => 12,  // Berat
            'I' => 18,  // Waktu
            'J' => 10,  // Status Gas
            'K' => 10,  // Speed
            'L' => 16,  // Temperature
            'M' => 16,  // Organoleptic
            'N' => 25,  // Catatan
        ];
    }

    /**
     * NB: pada tahap ini sheet BELUM disisipi baris header custom (itu terjadi di AfterSheet),
     * jadi baris "1" di sini merujuk pada baris heading tabel yang asli (sebelum disisipi).
     * PhpSpreadsheet otomatis menggeser style ini mengikuti barisnya saat insertNewRowBefore() dipanggil.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '28A745'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = $this->columnLetter(self::TOTAL_COLUMNS); // 'N'

                // Sisipkan baris kosong di atas untuk header perusahaan, judul & info filter,
                // meniru bagian atas export_pdf.blade.php
                $sheet->insertNewRowBefore(1, self::HEADER_ROWS);

                $sheet->setCellValue('A1', 'PT. CHAROEN POKPHAND INDONESIA');
                $sheet->setCellValue('A2', 'FOOD DIVISION ' . strtoupper($this->plantName));
                $sheet->setCellValue('A3', strtoupper($this->plantName) . ' - INDONESIA');
                $sheet->setCellValue('A5', 'PEMERIKSAAN PRODUK DI COOKING MIXER');

                $tanggalFilter = $this->filters['tanggal'] ?? 'Semua Tanggal';
                $shiftFilter = $this->filters['shift_nama'] ?? 'Semua Shift';
                $sheet->setCellValue(
                    'A7',
                    "Informasi Filter  -  Tanggal: {$tanggalFilter}   |   Shift: {$shiftFilter}   |   Kode Form: {$this->kodeForm}"
                );

                foreach ([1, 2, 3, 5, 7] as $row) {
                    $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
                $sheet->getStyle('A2:A3')->getFont()->setSize(10);
                $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A7')->getFont()->setSize(9)->setItalic(true);

                // Heading tabel sekarang ada di baris HEADER_ROWS+1 (setelah disisipi 8 baris di atas)
                $headingRow = self::HEADER_ROWS + 1;
                $lastDataRow = $sheet->getHighestRow();
                $tableRange = "A{$headingRow}:{$lastColumn}{$lastDataRow}";

                $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A' . ($headingRow + 1) . ":{$lastColumn}{$lastDataRow}")
                    ->getAlignment()
                    ->setWrapText(true)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Kode form di kanan bawah tabel, seperti pada PDF
                $footerRow = $lastDataRow + 2;
                $sheet->setCellValue("{$lastColumn}{$footerRow}", $this->kodeForm);
                $sheet->getStyle("{$lastColumn}{$footerRow}")->getFont()->setBold(true);
                $sheet->getStyle("{$lastColumn}{$footerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $this->writeSignatureBlock($sheet, $footerRow + 2, $lastColumn);
            },
        ];
    }

    /**
     * Blok "Dibuat Oleh / Diketahui Oleh / Disetujui Oleh" seperti signature-table di PDF
     * (tanpa QR code, karena Excel bukan dokumen final bertanda tangan).
     */
    protected function writeSignatureBlock($sheet, int $startRow, string $lastColumn): void
    {
        [$qcName, $produksiName, $spvName] = $this->resolveApproverNames();

        $third = (int) ceil(self::TOTAL_COLUMNS / 3);
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
     * Sama seperti loop pencarian approver di export_pdf.blade.php: ambil approver
     * pertama yang ditemukan lalu berhenti.
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

    protected function columnLetter(int $index): string
    {
        return Coordinate::stringFromColumnIndex($index);
    }
}