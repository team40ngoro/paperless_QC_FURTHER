<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Carbon\Carbon;

class WizardPackingExport implements WithMultipleSheets
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->records as $index => $record) {
            $sheets[] = new WizardPackingSheet($record, $index + 1);
        }
        return $sheets;
    }
}

class WizardPackingSheet implements FromArray, WithTitle, WithEvents
{
    protected $record;
    protected $sheetIndex;

    public function __construct($record, $sheetIndex)
    {
        $this->record = $record;
        $this->sheetIndex = $sheetIndex;
    }

    public function title(): string
    {
        $pembekuan = $this->record['pembekuan'];
        $tanggal = $pembekuan->tanggal ? Carbon::parse($pembekuan->tanggal)->format('d-m-Y') : 'NoDate';
        $shift = $pembekuan->shift->shift ?? 'NoShift';
        return "Record {$this->sheetIndex} - {$tanggal} - {$shift}";
    }

    public function array(): array
    {
        $pembekuan = $this->record['pembekuan'];
        $semuaBeratProdukBag = $this->record['semuaBeratProdukBag'];

        $rows = [];
        $row = 0;

        $companyName = 'PT. CHAROEN POKPHAND INDONESIA';
        $plantName = $pembekuan->plan->nama_plan ?? auth()->user()->plan->nama_plan ?? '';
        $fullPlant = strtoupper($plantName) . ' - INDONESIA';

        $rows[] = [$companyName, '', '', '', '', '', '', '', '', ''];
        $rows[] = [$fullPlant, '', '', '', '', '', '', '', '', ''];
        $rows[] = [];

        $rows[] = ['VERIFIKASI PROSES PENGEMASAN', '', '', '', '', '', '', '', '', ''];
        $rows[] = [];

        $tanggalStr = $pembekuan->tanggal ? Carbon::parse($pembekuan->tanggal)->format('l, d-m-Y') : '-';
        $shiftStr = $pembekuan->shift->shift ?? '-';
        $waktuAwal = $pembekuan->pengemasanProduk->waktu_awal_packing ?? '-';
        $produk1 = $pembekuan->pengemasanProduk->produk->nama_produk ?? '-';
        $kodeProduksi1 = $pembekuan->pengemasanProduk->kode_produksi ?? '-';
        $beratProduk1 = $pembekuan->pengemasanProduk->berat ?? '-';
        $tglExpired1 = $pembekuan->pengemasanProduk->tanggal_expired ? Carbon::parse($pembekuan->pengemasanProduk->tanggal_expired)->format('d-m-Y') : '-';
        $isKombinasi = ($pembekuan->pengemasanProduk && $pembekuan->pengemasanProduk->is_kombinasi) ? 'Ya (2 Produk dalam 1 Box)' : 'Tidak';

        $rows[] = ['Hari / Tanggal', $tanggalStr, 'Produk 1', $produk1];
        $rows[] = ['Shift', $shiftStr, 'Kode Produksi 1', $kodeProduksi1];
        $rows[] = ['Waktu Pengecekan After IQF', $waktuAwal, 'Berat Produk 1 (gr)', $beratProduk1];
        $rows[] = ['Tgl Expired 1', $tglExpired1, 'Box Kombinasi', $isKombinasi];

        if ($pembekuan->pengemasanProduk && $pembekuan->pengemasanProduk->is_kombinasi) {
            $rows[] = ['', '', '', ''];
            $rows[] = ['Produk 2', $pembekuan->pengemasanProduk->produk2->nama_produk ?? '-', 'Berat Produk 2 (gr)', $pembekuan->pengemasanProduk->berat_produk_2 ?? '-'];
            $rows[] = ['Kode Produksi 2', $pembekuan->pengemasanProduk->kode_produksi_2 ?? '-', 'Tgl Expired 2', $pembekuan->pengemasanProduk->tanggal_expired_2 ? Carbon::parse($pembekuan->pengemasanProduk->tanggal_expired_2)->format('d-m-Y') : '-'];
        }

        $rows[] = [];

        if ($pembekuan->pengemasanProduk) {
            $rows[] = ['A. PEMBEKUAN PRODUK (LANGKAH 2)'];
            $rows[] = ['Std Suhu IQF (°C)', 'Aktual Suhu Produk'];
            $suhus = explode(',', $pembekuan->pengemasanProduk->aktual_suhu_produk ?? '');
            $suhus = array_filter(array_map('trim', $suhus));
            $aktualSuhu = !empty($suhus) ? implode(' °C, ', $suhus) . ' °C' : '-';
            $rows[] = [$pembekuan->pengemasanProduk->std_suhu_produk_iqf ?? '-', $aktualSuhu];
            $rows[] = [];
        }

        if ($pembekuan->pengemasanPlastik) {
            $rows[] = ['B. PENGEMASAN PLASTIK (LANGKAH 3)'];
            $rows[] = ['Line', 'Waktu Pengemasan', 'Identitas Produk', 'Kesesuaian Label Plastik', 'Kode Kemasan', 'Kekuatan Seal', 'Proses Penimbangan', 'Proses Sealing', 'Nomor MD'];
            $rows[] = [
                $pembekuan->pengemasanPlastik->line ?? '-',
                $pembekuan->pengemasanPlastik->waktu_pengemasan ?? '-',
                $pembekuan->pengemasanPlastik->identitas_produk == '✔' ? 'OK' : 'Tidak Sesuai',
                $pembekuan->pengemasanPlastik->kesesuaian_label_plastik ?? '-',
                $pembekuan->pengemasanPlastik->kode_kemasan_plastik ?? '-',
                $pembekuan->pengemasanPlastik->kekuatan_seal == '✔' ? 'OK' : 'Tidak Sesuai',
                strtoupper($pembekuan->pengemasanPlastik->proses_penimbangan ?? '-'),
                strtoupper($pembekuan->pengemasanPlastik->proses_sealing ?? '-'),
                $pembekuan->pengemasanPlastik->nomor_md ?? '-'
            ];
            $rows[] = [];
        }

        if ($semuaBeratProdukBag->count() > 0) {
            $rows[] = ['C. BERAT PRODUK BAG (LANGKAH 4)'];
            $rows[] = ['Line', 'Nilai Standar Pack', 'Aktual (gr)', 'Rata-rata (gr)'];
            foreach ($semuaBeratProdukBag as $bag) {
                $berats = is_string($bag->berat_aktual) ? json_decode($bag->berat_aktual, true) : $bag->berat_aktual;
                $aktualStr = (is_array($berats) && count($berats) > 0) ? implode(', ', $berats) : '-';
                $rows[] = [
                    $bag->line ?? '-',
                    $bag->data_bag ? $bag->data_bag->std_bag : '-',
                    $aktualStr,
                    $bag->rata_rata_berat ?? '-'
                ];
            }
            $rows[] = [];
        }

        if ($pembekuan->beratProdukBox) {
            $rows[] = ['D. BERAT PRODUK BOX (LANGKAH 5)'];
            $rows[] = ['Nilai Standar Box', 'Aktual (gr)', 'Rata-rata (gr)'];
            $beratsBox = is_string($pembekuan->beratProdukBox->berat_aktual) ? json_decode($pembekuan->beratProdukBox->berat_aktual, true) : $pembekuan->beratProdukBox->berat_aktual;
            $aktualBoxStr = (is_array($beratsBox) && count($beratsBox) > 0) ? implode(', ', $beratsBox) : '-';
            $rows[] = [
                $pembekuan->beratProdukBox->data_box ? $pembekuan->beratProdukBox->data_box->std_box : '-',
                $aktualBoxStr,
                $pembekuan->beratProdukBox->rata_rata_berat ?? '-'
            ];
            $rows[] = [];
        }

        if ($pembekuan->pengemasanKarton) {
            $rows[] = ['E. PENGEMASAN KARTON (LANGKAH 6)'];
            $rows[] = ['Kesesuaian Label Karton', 'Standar isi pack dalam karton', 'Aktual isi pack dalam karton', 'Kesesuaian Standar Isi Pack'];
            $isiPack = $pembekuan->pengemasanKarton->kesesuaian_isi_pack;
            if (is_string($isiPack)) $isiPack = json_decode($isiPack, true);
            if (is_array($isiPack)) {
                $isiPack = array_map(fn($v) => $v === '✔' ? 'OK' : ($v === '✘' ? 'Tidak OK' : $v), $isiPack);
            }
            $isiPackStr = (is_array($isiPack) && count($isiPack) > 0) ? implode(', ', $isiPack) : '-';
            $rows[] = [
                $pembekuan->pengemasanKarton->identitas_produk_pada_karton == '✔' ? 'OK' : 'Tidak Sesuai',
                $pembekuan->pengemasanKarton->standar_jumlah_karton ?? '-',
                $pembekuan->pengemasanKarton->aktual_jumlah_karton ?? '-',
                $isiPackStr
            ];
            $rows[] = [];
        }

        $rows[] = ['F. DOKUMENTASI (LANGKAH 7)'];
        $rows[] = ['Foto Kode Produksi Primer & Best Before', 'Foto Kode Produksi Sekunder & Best Before', 'QR Code', 'Foto Label Polyroll', 'Label Supplier'];
        $rows[] = [
            $pembekuan->foto_kode_produksi ? '[GAMBAR]' : 'Tidak ada foto',
            $this->getSekunderDisplay($pembekuan),
            $pembekuan->qr_code ? '[GAMBAR]' : 'Tidak ada foto',
            $pembekuan->label_polyroll ? '[GAMBAR]' : 'Tidak ada foto',
            $pembekuan->label_supplier ? '[GAMBAR]' : 'Tidak ada foto'
        ];
        $rows[] = ['Keterangan:', $pembekuan->pengemasanKarton->keterangan ?? '-'];
        
        $statusRelease = '';
        if (($pembekuan->pengemasanProduk->status_release_produk ?? '') == 'ya') $statusRelease = 'Ya';
        elseif (($pembekuan->pengemasanProduk->status_release_produk ?? '') == 'tidak') $statusRelease = 'Tidak';
        else $statusRelease = '-';
        
        $rows[] = ['Status Release Produk:', $statusRelease, 'Tindakan Koreksi:', $pembekuan->pengemasanProduk->tindakan_koreksi ?? '-'];
        $rows[] = [];
        $rows[] = ['QF 15/00'];
        $rows[] = [];
        $rows[] = ['Dibuat Oleh:', 'Diperiksa Oleh:', 'Diketahui Oleh:'];
        $rows[] = ['', '', ''];
        $rows[] = ['', '', ''];
        $rows[] = ['QC', 'SUPERVISOR QC', 'FM/FL PRODUKSI'];

        return $rows;
    }

    private function getSekunderDisplay($pembekuan)
    {
        $sekunderVal = $pembekuan->foto_kode_produksi_sekunder;
        if (in_array($sekunderVal, ['OK', 'Not OK'])) {
            return $sekunderVal;
        } elseif ($sekunderVal && $sekunderVal !== '-') {
            return '[GAMBAR]';
        }
        return 'Tidak ada foto';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->styleSheet($sheet);
            },
        ];
    }

    private function styleSheet(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestCol = $sheet->getHighestColumn();
        $highestColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);

        $headerStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '28A745']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];

        $sectionTitleStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E0E0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];

        $tableHeaderStyle = [
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];

        $cellStyle = [
            'font' => ['size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];

        $labelCellStyle = [
            'font' => ['size' => 9, 'bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];

        $valueCellStyle = [
            'font' => ['size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];

        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14, 'underline' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];

        $companyStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ];

        $formInfoStyle = [
            'font' => ['size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];

        $sigHeaderStyle = [
            'font' => ['bold' => true, 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];

        $sigNameStyle = [
            'font' => ['size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];

        $row = 1;
        foreach ($sheet->getRowIterator() as $rowIndex => $worksheetRow) {
            $cellIterator = $worksheetRow->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }

            if (empty(array_filter($cells, fn($v) => $v !== null && $v !== ''))) {
                $row++;
                continue;
            }

            $firstCell = trim((string)($cells[0] ?? ''));

            if ($row <= 2) {
                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray($companyStyle);
                if ($row === 1) {
                    $sheet->mergeCells("A{$row}:B{$row}");
                }
            } elseif ($firstCell === 'VERIFIKASI PROSES PENGEMASAN') {
                $sheet->mergeCells("A{$row}:J{$row}");
                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray($titleStyle);
            } elseif (in_array($firstCell, ['Hari / Tanggal', 'Shift', 'Waktu Pengecekan After IQF', 'Tgl Expired 1', 'Produk 2', 'Kode Produksi 2'])) {
                $sheet->getStyle("A{$row}:D{$row}")->applyFromArray($formInfoStyle);
                $sheet->getStyle("A{$row}")->applyFromArray($labelCellStyle);
                $sheet->getStyle("C{$row}")->applyFromArray($labelCellStyle);
            } elseif (str_starts_with($firstCell, 'A.') || str_starts_with($firstCell, 'B.') || str_starts_with($firstCell, 'C.') || str_starts_with($firstCell, 'D.') || str_starts_with($firstCell, 'E.') || str_starts_with($firstCell, 'F.')) {
                $sheet->mergeCells("A{$row}:J{$row}");
                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray($sectionTitleStyle);
            } elseif (in_array($firstCell, ['Std Suhu IQF (°C)', 'Line', 'Nilai Standar Pack', 'Nilai Standar Box', 'Kesesuaian Label Karton', 'Foto Kode Produksi Primer & Best Before', 'Keterangan:', 'Status Release Produk:', 'Dibuat Oleh:', 'QC'])) {
                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray($tableHeaderStyle);
            } elseif ($firstCell === 'QF 15/00') {
                $sheet->mergeCells("A{$row}:J{$row}");
                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                    'font' => ['size' => 8],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
            } else {
                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray($cellStyle);
            }

            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);

        $sheet->getDefaultRowDimension()->setRowHeight(18);
    }
}