<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class PersiapanBahanFormingExport implements FromArray, WithEvents
{
    protected $dataForming;
    protected $dataNonForming;
    protected $filterInfo;

    protected array $mergeCells = [];
    protected array $titleRows = [];
    protected array $sectionTitleRows = [];
    protected array $headerRowPairs = [];
    protected array $tableRanges = [];
    protected array $leftAlignCells = [];
    protected array $rightAlignCells = [];
    protected array $boldCells = [];
    protected int $filterInfoRow = 0;
    protected int $kodeFormRow = 0;
    protected int $signatureLabelRow = 0;
    protected int $signatureNameRow = 0;
    public function __construct($dataForming = null, $dataNonForming = null, array $filterInfo = [])
    {
        $this->dataForming = $dataForming ?? collect();
        $this->dataNonForming = $dataNonForming ?? collect();
        $this->filterInfo = $filterInfo;
    }

    public function array(): array
    {
        $rows = [];
        $planName = strtoupper($this->filterInfo['plan_name'] ?? auth()->user()->plan->nama_plan ?? 'PLANT');
        $kodeForm = $this->filterInfo['kode_form'] ?? 'QF 04/00';
        $lastCol = $this->col(25);

        $rows[] = $this->pad(['PT. CHAROEN POKPHAND INDONESIA'], 25);
        $rows[] = $this->pad(['FOOD DIVISION ' . $planName], 25);
        $rows[] = $this->pad([$planName . ' - INDONESIA'], 25);
        $this->mergeCells[] = "A1:{$lastCol}1";
        $this->mergeCells[] = "A2:{$lastCol}2";
        $this->mergeCells[] = "A3:{$lastCol}3";
        $this->titleRows[] = ['start' => 1, 'end' => 3, 'size' => 12];

        $titleRow = 5;
        $rows[] = $this->pad([]);
        $rows[] = $this->pad(['VERIFIKASI PERSIAPAN COLD MIXING'], 25);
        $this->mergeCells[] = "A{$titleRow}:{$lastCol}{$titleRow}";
        $this->titleRows[] = ['start' => $titleRow, 'end' => $titleRow, 'size' => 14];

        $tanggalMulai = $this->filterInfo['tanggal_mulai'] ?? '-';
        $tanggalAkhir = $this->filterInfo['tanggal_akhir'] ?? '-';
        $shift = $this->filterInfo['shift'] ?? 'Semua Shift';
        $produk = $this->filterInfo['produk'] ?? 'Semua Produk';

        $rows[] = $this->pad([]);
        $this->filterInfoRow = count($rows) + 1;
        $rows[] = $this->pad([
            'Tanggal: ' . $tanggalMulai . ' s/d ' . $tanggalAkhir . ' | Shift: ' . $shift . ' | Produk: ' . $produk,
        ], 25);
        $this->mergeCells[] = "A{$this->filterInfoRow}:{$lastCol}{$this->filterInfoRow}";

        $rows[] = $this->pad([]);
        $sectionATitleRow = count($rows) + 1;
        $rows[] = $this->pad(['A. Persiapan Bahan Forming'], 25);
        $this->mergeCells[] = "A{$sectionATitleRow}:{$lastCol}{$sectionATitleRow}";
        $this->sectionTitleRows[] = $sectionATitleRow;

        $header1 = count($rows) + 1;
        $rows[] = $this->pad([
            'No', 'Waktu', 'Kode Produksi', 'Kode Produksi Emulsi OIL', 'Nomor Formula', 'Gramase (g)',
            'LARUTAN BUMBU', '', '', '', '', '',
            'Suhu Std Adonan', 'PRODUK REWORK', 'REWORK (kg)',
            'SUHU AKTUAL (°C)', '', '', '', '', '',
            'Waktu Mixing', 'Hasil Penggilingan', 'Hasil Pencampuran', 'KETERANGAN',
        ], 25);

        $header2 = count($rows) + 1;
        $rows[] = $this->pad([
            '', '', '', '', '', '',
            'Bahan RM', 'Berat RM', 'Kode Produksi', 'Suhu', 'Sensori', 'Ket.',
            '', '', '',
            '1', '2', '3', '4', '5', 'Total',
            '', '', '', '',
        ], 25);

        $this->headerRowPairs[] = ['start' => $header1, 'end' => $header2, 'last' => $lastCol];
        $this->mergeHeaderRowspan($header1, $header2, ['A', 'B', 'C', 'D', 'E', 'F', 'M', 'N', 'O', 'V', 'W', 'X', 'Y']);
        $this->mergeCells[] = "G{$header1}:L{$header1}";
        $this->mergeCells[] = "P{$header1}:U{$header1}";

        $allReworkIds = $this->dataForming->flatMap(fn ($r) => (array) ($r->produk_rework_id ?? []))->filter()->unique()->values()->toArray();
        $reworkProdukMap = !empty($allReworkIds)
            ? \App\Models\JenisProduk::whereIn('id', $allReworkIds)->get()->keyBy('id')
            : collect();

        $sectionAStart = $header1;
        if ($this->dataForming->isNotEmpty()) {
            $noForming = 1;
            foreach ($this->dataForming as $item) {
                $reworkNama = '-';
                if (!empty($item->produk_rework_id)) {
                    $names = collect((array) $item->produk_rework_id)
                        ->map(fn ($id) => $reworkProdukMap->get($id)?->nama_produk)
                        ->filter()
                        ->implode(', ');
                    $reworkNama = $names ?: '-';
                }

                $waktuMixing = ($item->waktu_mulai_mixing ?? '-') . ' - ' . ($item->waktu_selesai_mixing ?? '-');
                $shared = [
                    $noForming++,
                    $item->jam ? Carbon::parse($item->jam)->format('H:i') : '-',
                    $item->kode_produksi_emulsi ?? '-',
                    is_array($item->kode_produksi_emulsi_oil)
                        ? implode(', ', $item->kode_produksi_emulsi_oil)
                        : ($item->kode_produksi_emulsi_oil ?? '-'),
                    $item->formula->nomor_formula ?? '-',
                    $item->berat_sampling ?? '-',
                ];
                $trailing = [
                    $item->suhuAdonan->std_suhu ?? '-',
                    $reworkNama,
                    $item->rework ?? '-',
                    $item->aktualSuhuAdonan->aktual_suhu_1 ?? '-',
                    $item->aktualSuhuAdonan->aktual_suhu_2 ?? '-',
                    $item->aktualSuhuAdonan->aktual_suhu_3 ?? '-',
                    $item->aktualSuhuAdonan->aktual_suhu_4 ?? '-',
                    $item->aktualSuhuAdonan->aktual_suhu_5 ?? '-',
                    $item->aktualSuhuAdonan ? number_format($item->aktualSuhuAdonan->total_aktual_suhu, 1) : '-',
                    $waktuMixing,
                    $this->formatStatus($item->kondisi ?? '-'),
                    $this->formatStatus($item->kondisi_hasil_pencampuran ?? '-'),
                    $item->catatan ?? '-',
                ];

                $bahanList = $item->suhuForming;
                $firstRow = count($rows) + 1;

                if ($bahanList && $bahanList->isNotEmpty()) {
                    foreach ($bahanList as $index => $suhu) {
                        $larutan = [
                            $suhu->bahanForming->nama_rm ?? '-',
                            $suhu->bahanForming->berat_rm ?? '-',
                            $suhu->kode_produksi_bahan ?? '-',
                            isset($suhu->suhu) ? $suhu->suhu . '°C' : '-',
                            $suhu->sensori ?? '-',
                            $suhu->keterangan ?? '-',
                        ];

                        if ($index === 0) {
                            $rows[] = $this->pad(array_merge($shared, $larutan, $trailing), 25);
                        } else {
                            $rows[] = $this->pad(array_merge(array_fill(0, 6, ''), $larutan, array_fill(0, 13, '')), 25);
                        }

                        $this->leftAlignCells[] = 'G' . count($rows);
                    }

                    $beratList = $bahanList->map(fn ($s) => $this->toFloat($s->bahanForming->berat_rm ?? 0));
                    $totalBerat = $beratList->sum();
                    $avgBerat = $beratList->count() > 0 ? $beratList->avg() : 0;

                    $totRow = count($rows) + 1;
                    $rows[] = $this->pad(array_merge(array_fill(0, 6, ''), ['Tot:', number_format($totalBerat, 3), '', '', '', ''], array_fill(0, 13, '')), 25);
                    $avgRow = count($rows) + 1;
                    $rows[] = $this->pad(array_merge(array_fill(0, 6, ''), ['Avg:', number_format($avgBerat, 3), '', '', '', ''], array_fill(0, 13, '')), 25);
                    $this->mergeCells[] = "I{$totRow}:L{$totRow}";
                    $this->mergeCells[] = "I{$avgRow}:L{$avgRow}";
                    $this->boldCells[] = "G{$totRow}:H{$totRow}";
                    $this->boldCells[] = "G{$avgRow}:H{$avgRow}";
                    $this->rightAlignCells[] = "G{$totRow}";
                    $this->rightAlignCells[] = "G{$avgRow}";

                    $lastRow = count($rows);
                    if ($lastRow > $firstRow) {
                        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y'] as $col) {
                            $this->mergeCells[] = "{$col}{$firstRow}:{$col}{$lastRow}";
                        }
                    }
                } else {
                    $rows[] = $this->pad(array_merge($shared, array_fill(0, 6, '-'), $trailing), 25);
                }
            }
        } else {
            $emptyRow = count($rows) + 1;
            $rows[] = $this->pad(['Tidak ada data forming untuk filter ini'], 25);
            $this->mergeCells[] = "A{$emptyRow}:{$lastCol}{$emptyRow}";
        }

        $this->tableRanges[] = [$sectionAStart, count($rows), $lastCol];

        $rows[] = $this->pad([]);
        $sectionBTitleRow = count($rows) + 1;
        $rows[] = $this->pad(['B. Persiapan Bahan Non Forming'], 25);
        $this->mergeCells[] = "A{$sectionBTitleRow}:{$lastCol}{$sectionBTitleRow}";
        $this->sectionTitleRows[] = $sectionBTitleRow;

        $sectionBHeader = count($rows) + 1;
        $rows[] = $this->pad([
            'No', 'Tanggal', 'Jam', 'Shift', 'Produk', 'Kode Produksi', 'Nomor Formula', 'Kondisi', 'Dibuat Oleh', 'Catatan',
        ], 10);
        $this->headerRowPairs[] = ['start' => $sectionBHeader, 'end' => $sectionBHeader, 'last' => 'J'];

        if ($this->dataNonForming->isNotEmpty()) {
            $noNonForming = 1;
            foreach ($this->dataNonForming as $item) {
                $rowIndex = count($rows) + 1;
                $rows[] = $this->pad([
                    $noNonForming++,
                    $item->tanggal ? Carbon::parse($item->tanggal)->format('d-m-Y') : '-',
                    $item->jam ? Carbon::parse($item->jam)->format('H:i') : '-',
                    $item->shift->shift ?? '-',
                    $item->formulaNonForming->produk->nama_produk ?? '-',
                    $item->kode_produksi ?? '-',
                    $item->formulaNonForming->nomor_formula ?? '-',
                    $this->formatStatus($item->kondisi ?? '-'),
                    $item->user->name ?? '-',
                    $item->catatan ?? '-',
                ], 10);
                $this->leftAlignCells[] = "E{$rowIndex}";
                $this->leftAlignCells[] = "J{$rowIndex}";
            }
        } else {
            $emptyRow = count($rows) + 1;
            $rows[] = $this->pad(['Tidak ada data non-forming untuk filter ini'], 10);
            $this->mergeCells[] = "A{$emptyRow}:J{$emptyRow}";
        }

        $this->tableRanges[] = [$sectionBHeader, count($rows), 'J'];

        $rows[] = $this->pad([]);
        $this->kodeFormRow = count($rows) + 1;
        $kodeRow = $this->pad([], 25);
        $kodeRow[24] = $kodeForm;
        $rows[] = $kodeRow;

        [$qcName, $produksiName, $spvName] = $this->resolveApproverNames();

        $rows[] = $this->pad([]);
        $rows[] = $this->pad([]);
        $this->signatureLabelRow = count($rows) + 1;
        $rows[] = $this->pad([
            'Dibuat Oleh: QC INSPEKTOR', '', '', '', '', '', '', '',
            'Diketahui Oleh: Produksi', '', '', '', '', '', '', '',
            'Disetujui Oleh: Supervisor QC',
        ], 25);
        $this->mergeCells[] = "A{$this->signatureLabelRow}:H{$this->signatureLabelRow}";
        $this->mergeCells[] = "I{$this->signatureLabelRow}:P{$this->signatureLabelRow}";
        $this->mergeCells[] = "Q{$this->signatureLabelRow}:Y{$this->signatureLabelRow}";

        $rows[] = $this->pad([]);
        $rows[] = $this->pad([]);

        $this->signatureNameRow = count($rows) + 1;
        $rows[] = $this->pad([
            $qcName, '', '', '', '', '', '', '',
            $produksiName, '', '', '', '', '', '', '',
            $spvName,
        ], 25);
        $this->mergeCells[] = "A{$this->signatureNameRow}:H{$this->signatureNameRow}";
        $this->mergeCells[] = "I{$this->signatureNameRow}:P{$this->signatureNameRow}";
        $this->mergeCells[] = "Q{$this->signatureNameRow}:Y{$this->signatureNameRow}";

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $thin = Border::BORDER_THIN;
                $lastCol = $this->col(25);

                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToPage(true);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);

                foreach (array_unique($this->mergeCells) as $range) {
                    $sheet->mergeCells($range);
                }

                foreach ($this->titleRows as $meta) {
                    $range = 'A' . $meta['start'] . ':' . $lastCol . $meta['end'];
                    $sheet->getStyle($range)->getFont()->setBold(true)->setSize($meta['size'] ?? 12);
                    $sheet->getStyle($range)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }

                if ($this->filterInfoRow > 0) {
                    $range = "A{$this->filterInfoRow}:{$lastCol}{$this->filterInfoRow}";
                    $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F9F9F9');
                    $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle($thin);
                    $sheet->getStyle($range)->getFont()->setSize(9);
                }

                foreach ($this->sectionTitleRows as $row) {
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(11);
                }

                foreach ($this->tableRanges as [$start, $end, $col]) {
                    $range = "A{$start}:{$col}{$end}";
                    $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle($thin);
                    $sheet->getStyle($range)->getFont()->setSize(8);
                    $sheet->getStyle($range)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setWrapText(true);
                }

                foreach ($this->headerRowPairs as $meta) {
                    $range = "A{$meta['start']}:{$meta['last']}{$meta['end']}";
                    $sheet->getStyle($range)->getFont()->setBold(true)->setSize(8);
                    $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F0F0F0');
                    $sheet->getStyle($range)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setWrapText(true);
                }

                foreach ($this->leftAlignCells as $cell) {
                    $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }

                foreach ($this->rightAlignCells as $cell) {
                    $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }

                foreach ($this->boldCells as $range) {
                    $sheet->getStyle($range)->getFont()->setBold(true);
                }

                if ($this->kodeFormRow > 0) {
                    $sheet->getStyle("{$lastCol}{$this->kodeFormRow}")->getFont()->setBold(true)->setSize(8);
                    $sheet->getStyle("{$lastCol}{$this->kodeFormRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }

                if ($this->signatureLabelRow > 0) {
                    $sheet->getStyle("A{$this->signatureLabelRow}:{$lastCol}{$this->signatureLabelRow}")
                        ->getFont()->setBold(true)->setSize(10);
                    $sheet->getStyle("A{$this->signatureLabelRow}:{$lastCol}{$this->signatureLabelRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                if ($this->signatureNameRow > 0) {
                    $sheet->getStyle("A{$this->signatureNameRow}:{$lastCol}{$this->signatureNameRow}")
                        ->getFont()->setBold(true)->setSize(10);
                    $sheet->getStyle("A{$this->signatureNameRow}:{$lastCol}{$this->signatureNameRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $lineRow = $this->signatureNameRow - 1;
                    foreach (['A', 'I', 'Q'] as $col) {
                        $sheet->getStyle("{$col}{$lineRow}")->getBorders()->getBottom()->setBorderStyle($thin);
                    }
                }

                $widths = [
                    'A' => 6, 'B' => 10, 'C' => 16, 'D' => 18, 'E' => 14, 'F' => 12,
                    'G' => 22, 'H' => 12, 'I' => 16, 'J' => 10, 'K' => 12, 'L' => 12,
                    'M' => 14, 'N' => 16, 'O' => 12,
                    'P' => 8, 'Q' => 8, 'R' => 8, 'S' => 8, 'T' => 8, 'U' => 10,
                    'V' => 16, 'W' => 14, 'X' => 14, 'Y' => 18,
                ];
                foreach ($widths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }
            },
        ];
    }

    protected function mergeHeaderRowspan(int $start, int $end, array $cols): void
    {
        foreach ($cols as $col) {
            $this->mergeCells[] = "{$col}{$start}:{$col}{$end}";
        }
    }

    protected function pad(array $row, int $size = 25): array
    {
        return array_pad($row, $size, '');
    }

    protected function col(int $index): string
    {
        $name = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $name = chr(65 + $mod) . $name;
            $index = intdiv($index - 1, 26);
        }

        return $name;
    }

    protected function toFloat($val): float
    {
        if (is_null($val) || $val === '' || is_array($val)) {
            return 0.0;
        }
        $clean = preg_replace('/[^\d.\-]/', '', str_replace(',', '.', (string) $val));

        return is_numeric($clean) ? (float) $clean : 0.0;
    }

    protected function formatStatus($value): string
    {
        $value = (string) $value;
        if ($value === '✔' || $value === '&#10004;' || str_contains($value, '✔')) {
            return 'OK';
        }
        if ($value === '✘' || $value === '&#10008;' || str_contains($value, '✘')) {
            return 'Not OK';
        }

        return $value === '' ? '-' : $value;
    }

    protected function resolveApproverNames(): array
    {
        $combined = $this->dataForming->concat($this->dataNonForming);
        $qcName = 'QC';
        $spvName = 'SPV';
        $produksiName = 'FM/FL PRODUKSI';

        foreach ($combined as $item) {
            if ($item->approved_by_qc && $item->qcApprover) {
                $qcName = $item->qcApprover->name;
                break;
            }
        }
        foreach ($combined as $item) {
            if ($item->approved_by_produksi && $item->produksiApprover) {
                $produksiName = $item->produksiApprover->name;
                break;
            }
        }
        foreach ($combined as $item) {
            if ($item->approved_by_spv && $item->spvApprover) {
                $spvName = $item->spvApprover->name;
                break;
            }
        }

        return [$qcName, $produksiName, $spvName];
    }
}
