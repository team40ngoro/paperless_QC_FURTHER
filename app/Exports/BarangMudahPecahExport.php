<?php

namespace App\Exports;

use App\Models\InputArea;
use App\Models\User;
use Illuminate\Support\Collection;
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
 * qc-sistem.barang_mudah_pecah.export_pdf: header perusahaan, judul, tabel
 * "Hari/Tanggal & Shift" (dari record pertama), tabel utama yang dikelompokkan
 * per Area (dengan baris "flatten" dari items_json ATAU baris manual/lama,
 * sama seperti logika PHP di blade), kode form, dan blok tanda tangan.
 *
 * Karena strukturnya pakai baris judul grup per Area, sheet ini dirakit
 * manual lewat AfterSheet (bukan lewat WithHeadings/WithMapping biasa).
 */
class BarangMudahPecahExport implements FromCollection, WithEvents, WithColumnWidths
{
    /** No, Nama Barang, Jumlah, Kondisi, Temuan Ketidaksesuaian, Tindakan Koreksi, Verifikasi Hasil */
    protected const TOTAL_COLUMNS = 7;

    protected Collection $records;
    protected string $kodeForm;

    public function __construct(Collection $records, ?string $kodeForm = null)
    {
        $this->records = $records;
        $this->kodeForm = $kodeForm ?? ($records->first()->kode_form ?? '-');
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
            'B' => 30,  // Nama Barang
            'C' => 10,  // Jumlah
            'D' => 12,  // Kondisi
            'E' => 25,  // Temuan Ketidaksesuaian
            'F' => 25,  // Tindakan Koreksi
            'G' => 16,  // Verifikasi Hasil
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = $this->columnLetter(self::TOTAL_COLUMNS); // 'F'

                $row = 1;
                $row = $this->writeCompanyHeader($sheet, $row, $lastColumn);
                $row = $this->writeFormInfo($sheet, $row, $lastColumn);
                $row = $this->writeMainTable($sheet, $row, $lastColumn);
                $row = $this->writeKodeForm($sheet, $row, $lastColumn);
                $row += 1;
                $this->writeSignatureBlock($sheet, $row, $lastColumn);
            },
        ];
    }

    protected function plantName(): string
    {
        $user = auth()->user();

        return strtoupper($user->plan->nama_plan ?? 'UNKNOWN');
    }

    protected function writeCompanyHeader($sheet, int $row, string $lastColumn): int
    {
        $plant = $this->plantName();

        $sheet->setCellValue("A{$row}", 'PT. CHAROEN POKPHAND INDONESIA');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(13);
        $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        $sheet->setCellValue("A{$row}", "FOOD DIVISION {$plant}");
        $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        $sheet->setCellValue("A{$row}", "{$plant} - INDONESIA");
        $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row += 2;

        $sheet->setCellValue("A{$row}", 'VERIFIKASI KONDISI BARANG MUDAH PECAH');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);
        $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row += 2;

        return $row;
    }

    /**
     * Sama seperti tabel "form-info" di export_pdf.blade.php: Hari/Tanggal &
     * Shift diambil dari record pertama, bukan dari filter request.
     */
    protected function writeFormInfo($sheet, int $row, string $lastColumn): int
    {
        $firstItem = $this->records->first();
        $borderStart = $row;

        $tanggalValue = '-';
        if ($firstItem && $firstItem->tanggal) {
            $tanggalValue = is_string($firstItem->tanggal)
                ? \Carbon\Carbon::parse($firstItem->tanggal)->translatedFormat('l, d-m-Y')
                : $firstItem->tanggal->translatedFormat('l, d-m-Y');
        }
        $shiftValue = $firstItem->shift->shift ?? '-';

        $sheet->setCellValue("A{$row}", 'Hari / Tanggal');
        $sheet->setCellValue("B{$row}", ': ' . $tanggalValue);
        $sheet->mergeCells("B{$row}:{$lastColumn}{$row}");
        $row++;

        $sheet->setCellValue("A{$row}", 'Shift');
        $sheet->setCellValue("B{$row}", ': ' . $shiftValue);
        $sheet->mergeCells("B{$row}:{$lastColumn}{$row}");
        $row++;

        $sheet->getStyle("A{$borderStart}:{$lastColumn}" . ($row - 1))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A{$borderStart}:A" . ($row - 1))->getFont()->setBold(true);

        $row++;

        return $row;
    }

    /**
     * Meratakan data persis seperti $allFlattenedItems di export_pdf.blade.php:
     * setiap record bisa berisi banyak baris lewat items_json, atau satu baris
     * "manual"/lama langsung dari kolom record itu sendiri.
     */
    protected function flattenItems(): Collection
    {
        $flattened = collect();

        foreach ($this->records as $record) {
            if ($record->items_json) {
                foreach ($record->items_json as $jsonRow) {
                    $area = InputArea::find($jsonRow['id_area']);
                    $flattened->push((object) [
                        'id_area' => $jsonRow['id_area'],
                        'area_name' => $area->area ?? 'Area Tidak Diketahui',
                        'nama_barang' => $jsonRow['nama_barang'] ?? '-',
                        'jumlah' => $jsonRow['jumlah'] ?? '-',
                        'kondisi' => $jsonRow['kondisi'] ?? '-',
                        'temuan' => $jsonRow['temuan_ketidaksesuaian'] ?? '',
                        'tindakan' => $jsonRow['tindakan_koreksi'] ?? '',
                        'verifikasi_hasil' => $jsonRow['verifikasi_hasil'] ?? '',
                        'nama_karyawan' => null,
                    ]);
                }
            } else {
                $flattened->push((object) [
                    'id_area' => $record->id_area,
                    'area_name' => $record->area->area ?? 'Area Tidak Diketahui',
                    'nama_barang' => $record->is_manual
                        ? ($record->nama_barang_manual ?? '-')
                        : ($record->namaBarang->nama_barang ?? '-'),
                    'jumlah' => $record->jumlah ?? '-',
                    'kondisi' => $record->kondisi ?? '-',
                    'temuan' => $record->temuan_ketidaksesuaian ?? '',
                    'tindakan' => $record->tindakan_koreksi ?? '',
                    'verifikasi_hasil' => $record->verifikasi_hasil ?? '',
                    'nama_karyawan' => $record->is_manual ? $record->nama_karyawan : null,
                ]);
            }
        }

        return $flattened;
    }

    /**
     * Tabel utama dikelompokkan per Area, dengan baris judul grup (colspan
     * penuh, background abu-abu) sebelum item-item area tersebut — persis
     * $groupedByArea di export_pdf.blade.php.
     */
    protected function writeMainTable($sheet, int $row, string $lastColumn): int
    {
        $headings = ['No', 'Nama Barang', 'Jumlah', 'Kondisi', 'Temuan Ketidaksesuaian', 'Tindakan Koreksi', 'Verifikasi Hasil'];
        $headingRow = $row;
        $sheet->fromArray($headings, null, "A{$headingRow}");
        $this->styleHeadingRow($sheet, $headingRow, $lastColumn);
        $row++;

        $groupedByArea = $this->flattenItems()->groupBy('id_area');
        $no = 1;

        foreach ($groupedByArea as $items) {
            $sheet->setCellValue("A{$row}", $items->first()->area_name);
            $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
            $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E9ECEF');
            $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $row++;

            foreach ($items as $item) {
                $namaBarang = $item->nama_barang;
                if ($item->nama_karyawan) {
                    $namaBarang .= "\n(Karyawan: {$item->nama_karyawan})";
                }

                $sheet->fromArray([
                    $no++,
                    $namaBarang,
                    $item->jumlah,
                    $item->kondisi,
                    $item->temuan !== '' ? $item->temuan : '-',
                    $item->tindakan !== '' ? $item->tindakan : '-',
                    $item->verifikasi_hasil !== '' && $item->verifikasi_hasil !== null ? $item->verifikasi_hasil : '-',
                ], null, "A{$row}");

                $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
                $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
                $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);

                if ($item->kondisi === 'OK') {
                    $sheet->getStyle("D{$row}")->getFont()->setBold(true)->getColor()->setRGB('28A745');
                } elseif ($item->kondisi === 'Tidak OK') {
                    $sheet->getStyle("D{$row}")->getFont()->setBold(true)->getColor()->setRGB('DC3545');
                }

                if ($item->verifikasi_hasil === 'OK') {
                    $sheet->getStyle("G{$row}")->getFont()->setBold(true)->getColor()->setRGB('28A745');
                } elseif ($item->verifikasi_hasil === 'Tidak OK') {
                    $sheet->getStyle("G{$row}")->getFont()->setBold(true)->getColor()->setRGB('DC3545');
                }

                $row++;
            }
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
     * approver pertama yang ditemukan lalu berhenti. Pakai relasi kalau sudah
     * di-eager-load, kalau tidak fallback ke User::find seperti di blade.
     */
    protected function resolveApproverNames(): array
    {
        $qcApproverName = 'QC Inspector';
        $produksiApproverName = 'SPV/Foreman/Forelady Produksi';
        $spvApproverName = 'Supervisor QC';

        $qcFound = $produksiFound = $spvFound = false;

        foreach ($this->records as $item) {
            if (!$qcFound && $item->approved_by_qc && $item->qc_approved_by) {
                $user = $item->relationLoaded('qcApprover') ? $item->qcApprover : User::find($item->qc_approved_by);
                if ($user) {
                    $qcApproverName = $user->name;
                    $qcFound = true;
                }
            }
            if (!$produksiFound && $item->approved_by_produksi && $item->produksi_approved_by) {
                $user = $item->relationLoaded('produksiApprover') ? $item->produksiApprover : User::find($item->produksi_approved_by);
                if ($user) {
                    $produksiApproverName = $user->name;
                    $produksiFound = true;
                }
            }
            if (!$spvFound && $item->approved_by_spv && $item->spv_approved_by) {
                $user = $item->relationLoaded('spvApprover') ? $item->spvApprover : User::find($item->spv_approved_by);
                if ($user) {
                    $spvApproverName = $user->name;
                    $spvFound = true;
                }
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
    }

    protected function columnLetter(int $index): string
    {
        return Coordinate::stringFromColumnIndex($index);
    }
}