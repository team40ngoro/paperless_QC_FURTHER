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

class PersiapanBahanBetterExport implements FromArray, WithEvents
{
    protected $data;
    protected $filterInfo;

    protected array $mergeCells = [];
    protected array $titleRows = [];
    protected array $formInfoRanges = [];
    protected array $headerRowPairs = [];
    protected array $tableRanges = [];
    protected array $leftAlignCells = [];
    protected array $tallRows = [];
    protected int $kodeFormRow = 0;
    protected int $signatureLabelRow = 0;
    protected int $signatureNameRow = 0;

    public function __construct($data = null, array $filterInfo = [])
    {
        $this->data = $data ?? collect();
        $this->filterInfo = $filterInfo;
    }

    public function array(): array
    {
        $rows = [];
        $planName = strtoupper($this->filterInfo['plan_name'] ?? auth()->user()->plan->nama_plan ?? 'PLANT');
        $kodeForm = $this->filterInfo['kode_form'] ?? 'QF 05/00';
        $lastCol = 'P';

        $rows[] = $this->pad(['PT. CHAROEN POKPHAND INDONESIA']);
        $rows[] = $this->pad(['FOOD DIVISION ' . $planName]);
        $rows[] = $this->pad([$planName . ' - INDONESIA']);
        $this->mergeCells[] = "A1:{$lastCol}1";
        $this->mergeCells[] = "A2:{$lastCol}2";
        $this->mergeCells[] = "A3:{$lastCol}3";
        $this->titleRows[] = ['start' => 1, 'end' => 3, 'size' => 12];

        $rows[] = $this->pad([]);
        $titleRow = count($rows) + 1;
        $title = 'VERIFIKASI PEMBUATAN BATTER' . (!empty($this->filterInfo['is_audit']) ? ' (DATA AUDIT)' : '');
        $rows[] = $this->pad([$title]);
        $this->mergeCells[] = "A{$titleRow}:{$lastCol}{$titleRow}";
        $this->titleRows[] = ['start' => $titleRow, 'end' => $titleRow, 'size' => 14];

        $firstItem = $this->data->first();
        $tanggalMulai = $this->filterInfo['tanggal_mulai'] ?? ($firstItem && $firstItem->tanggal ? Carbon::parse($firstItem->tanggal)->format('d-m-Y') : '-');
        $tanggalAkhir = $this->filterInfo['tanggal_akhir'] ?? $tanggalMulai;
        $produk = $this->filterInfo['produk'] ?? ($firstItem->produk->nama_produk ?? '-');
        $shift = $this->filterInfo['shift'] ?? 'Semua Shift';

        $rows[] = $this->pad([]);
        $formStart = count($rows) + 1;
        $rows[] = $this->pad(['Hari / Tanggal', ': ' . $tanggalMulai . ' s/d ' . $tanggalAkhir]);
        $rows[] = $this->pad(['NAMA PRODUK', ': ' . $produk]);
        $rows[] = $this->pad(['Shift', ': ' . $shift]);
        $formEnd = count($rows);
        $this->formInfoRanges[] = [$formStart, $formEnd];
        for ($r = $formStart; $r <= $formEnd; $r++) {
            $this->mergeCells[] = "B{$r}:{$lastCol}{$r}";
        }

        $rows[] = $this->pad([]);
        $header1 = count($rows) + 1;
        $rows[] = $this->pad([
            'No', 'Waktu', 'Jenis Better', 'Kode Produksi Produk', 'Gram (g)',
            'Data Produksi', '', '',
            'Suhu Air (°C)', 'Sensori',
            'Standar', '', '',
            'Aktual', '', '',
        ]);
        $header2 = count($rows) + 1;
        $rows[] = $this->pad([
            '', '', '', '', '',
            'Nama Bahan', 'Berat (kg)', 'Kode Produksi',
            '', '',
            'Viskositas (Detik)', 'Salinitas (%)', 'Suhu Air (°C)',
            'Viskositas (Detik)', 'Salinitas (%)', 'Suhu Air (°C)',
        ]);

        $this->headerRowPairs[] = [$header1, $header2];
        foreach (['A', 'B', 'C', 'D', 'E', 'I', 'J'] as $col) {
            $this->mergeCells[] = "{$col}{$header1}:{$col}{$header2}";
        }
        $this->mergeCells[] = "F{$header1}:H{$header1}";
        $this->mergeCells[] = "K{$header1}:M{$header1}";
        $this->mergeCells[] = "N{$header1}:P{$header1}";

        $tableStart = $header1;
        if ($this->data->isNotEmpty()) {
            $no = 1;
            foreach ($this->data as $item) {
                $betterRows = $item->better_rows ?? [];
                if (!is_array($betterRows)) {
                    $betterRows = [];
                }
                if (count($betterRows) === 0) {
                    $betterRows = [[
                        'master_nama_formula_better' => $item->better->nama_formula_better ?? null,
                        'master_berat' => $item->berat_better ?? null,
                        'kode_produksi_better' => $item->kode_produksi_better ?? null,
                    ]];
                }

                $cellFormula = implode("\n", array_map(fn ($r) => $r['master_nama_formula_better'] ?? '-', $betterRows));
                $cellBerat = implode("\n", array_map(fn ($r) => $r['master_berat'] ?? '-', $betterRows));
                $cellKode = implode("\n", array_map(fn ($r) => $r['kode_produksi_better'] ?? '-', $betterRows));
                $bahanLineCount = max(count($betterRows), 1);

                $suhuValue = $item->suhu_air ?? ($item->better_rows[0]['suhu_air'] ?? '-');
                $sensValue = $item->sensori ?? ($item->better_rows[0]['sensori'] ?? '-');
                $cellSensori = $this->formatStatus($sensValue);

                $jam = $item->jam ? Carbon::parse($item->jam)->format('H:i') : '-';
                $shared = [
                    $no++,
                    $jam,
                    $item->better->nama_better ?? '-',
                    $item->kode_produksi_produk ?? '-',
                    $item->gramase ?? '-',
                    $cellFormula,
                    $cellBerat,
                    $cellKode,
                    $suhuValue ?: '-',
                    $cellSensori,
                ];

                $firstRow = count($rows) + 1;
                $aktuals = $item->aktuals;
                if ($aktuals && $aktuals->isNotEmpty()) {
                    foreach ($aktuals as $idx => $aktual) {
                        $stdAkt = [
                            $aktual->std->std_viskositas ?? '-',
                            $aktual->std->std_salinitas ?? '-',
                            $aktual->std->std_suhu_akhir ?? '-',
                            $aktual->aktual_vis ?? '-',
                            $aktual->aktual_sal ?? '-',
                            $aktual->aktual_suhu_air ?? '-',
                        ];
                        if ($idx === 0) {
                            $rows[] = $this->pad(array_merge($shared, $stdAkt));
                            $this->leftAlignCells[] = 'F' . count($rows);
                            $this->tallRows[count($rows)] = max(18, $bahanLineCount * 16);
                        } else {
                            $rows[] = $this->pad(array_merge(array_fill(0, 10, ''), $stdAkt));
                        }
                    }
                    $lastRow = count($rows);
                    if ($lastRow > $firstRow) {
                        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'] as $col) {
                            $this->mergeCells[] = "{$col}{$firstRow}:{$col}{$lastRow}";
                        }
                    }
                } else {
                    $rows[] = $this->pad(array_merge($shared, ['Tidak ada data aktual']));
                    $this->mergeCells[] = "K{$firstRow}:P{$firstRow}";
                    $this->leftAlignCells[] = "F{$firstRow}";
                    $this->tallRows[$firstRow] = max(18, $bahanLineCount * 16);
                }
            }
        } else {
            $emptyRow = count($rows) + 1;
            $rows[] = $this->pad(['Tidak ada data yang sesuai dengan filter yang dipilih.']);
            $this->mergeCells[] = "A{$emptyRow}:{$lastCol}{$emptyRow}";
        }

        $this->tableRanges[] = [$tableStart, count($rows)];

        $rows[] = $this->pad([]);
        $this->kodeFormRow = count($rows) + 1;
        $kodeRow = $this->pad([]);
        $kodeRow[15] = optional($firstItem)->kode_form ?? $kodeForm;
        $rows[] = $kodeRow;

        [$qcName, $produksiName, $spvName] = $this->resolveApproverNames();

        $rows[] = $this->pad([]);
        $rows[] = $this->pad([]);
        $this->signatureLabelRow = count($rows) + 1;
        $rows[] = $this->pad([
            'Dibuat Oleh: QC INSPEKTOR', '', '', '', '',
            'Diketahui Oleh: Produksi', '', '', '', '',
            'Disetujui Oleh: Supervisor QC',
        ]);
        $this->mergeCells[] = "A{$this->signatureLabelRow}:E{$this->signatureLabelRow}";
        $this->mergeCells[] = "F{$this->signatureLabelRow}:J{$this->signatureLabelRow}";
        $this->mergeCells[] = "K{$this->signatureLabelRow}:P{$this->signatureLabelRow}";

        $rows[] = $this->pad([]);
        $rows[] = $this->pad([]);
        $this->signatureNameRow = count($rows) + 1;
        $rows[] = $this->pad([
            $qcName, '', '', '', '',
            $produksiName, '', '', '', '',
            $spvName,
        ]);
        $this->mergeCells[] = "A{$this->signatureNameRow}:E{$this->signatureNameRow}";
        $this->mergeCells[] = "F{$this->signatureNameRow}:J{$this->signatureNameRow}";
        $this->mergeCells[] = "K{$this->signatureNameRow}:P{$this->signatureNameRow}";

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $thin = Border::BORDER_THIN;
                $lastCol = 'P';

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

                foreach ($this->formInfoRanges as [$start, $end]) {
                    $range = "A{$start}:{$lastCol}{$end}";
                    $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle($thin);
                    $sheet->getStyle($range)->getFont()->setSize(9);
                    $sheet->getStyle("A{$start}:A{$end}")->getFont()->setBold(true);
                }

                foreach ($this->tableRanges as [$start, $end]) {
                    $range = "A{$start}:{$lastCol}{$end}";
                    $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle($thin);
                    $sheet->getStyle($range)->getFont()->setSize(8);
                    $sheet->getStyle($range)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setWrapText(true);
                }

                foreach ($this->headerRowPairs as [$start, $end]) {
                    $range = "A{$start}:{$lastCol}{$end}";
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

                foreach ($this->tallRows as $row => $height) {
                    $sheet->getRowDimension($row)->setRowHeight($height);
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
                        ->getFont()->setSize(10);
                    $sheet->getStyle("A{$this->signatureNameRow}:{$lastCol}{$this->signatureNameRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $lineRow = $this->signatureNameRow - 1;
                    foreach (['A', 'F', 'K'] as $col) {
                        $sheet->getStyle("{$col}{$lineRow}")->getBorders()->getBottom()->setBorderStyle($thin);
                    }
                }

                $widths = [
                    'A' => 6, 'B' => 10, 'C' => 16, 'D' => 18, 'E' => 10,
                    'F' => 22, 'G' => 12, 'H' => 16, 'I' => 12, 'J' => 12,
                    'K' => 16, 'L' => 14, 'M' => 14, 'N' => 16, 'O' => 14, 'P' => 14,
                ];
                foreach ($widths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }
            },
        ];
    }

    protected function pad(array $row): array
    {
        return array_pad($row, 16, '');
    }

    protected function formatStatus($value): string
    {
        $value = (string) $value;
        if ($value === '✔' || $value === '&#10004;' || str_contains($value, '✔')) {
            return 'OK';
        }
        if ($value === '✘' || $value === '&#10008;' || str_contains($value, '✘')) {
            return 'Tidak OK';
        }

        return $value === '' ? '-' : $value;
    }

    protected function resolveApproverNames(): array
    {
        $qcName = 'QC';
        $spvName = 'SPV';
        $produksiName = 'FM/FL PRODUKSI';

        foreach ($this->data as $item) {
            if ($item->approved_by_qc && $item->qcApprover) {
                $qcName = $item->qcApprover->name;
                break;
            }
        }
        foreach ($this->data as $item) {
            if ($item->approved_by_produksi && $item->produksiApprover) {
                $produksiName = $item->produksiApprover->name;
                break;
            }
        }
        foreach ($this->data as $item) {
            if ($item->approved_by_spv && $item->spvApprover) {
                $spvName = $item->spvApprover->name;
                break;
            }
        }

        return [$qcName, $produksiName, $spvName];
    }
}
