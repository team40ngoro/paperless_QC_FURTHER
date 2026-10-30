<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PersiapanBahanEmulsiExport implements FromArray, WithEvents
{
    protected $data;
    protected $filterInfo;

    protected array $mergeCells = [];
    protected array $headerRows = [];
    protected array $prosesHeaderRows = [];
    protected array $sectionHeaderRows = [];
    protected array $tableRanges = [];
    protected array $formInfoRanges = [];
    protected array $leftAlignRows = [];
    protected array $titleRows = [];
    protected array $kodeFormRows = [];
    protected array $signatureLabelRows = [];
    protected array $signatureNameRows = [];
    protected array $namaRmCells = [];

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
        $isAudit = !empty($this->filterInfo['is_audit']);
        $title = 'VERIFIKASI PEMBUATAN EMULSI' . ($isAudit ? ' (DATA AUDIT)' : '');

        $groups = $this->data->groupBy(function ($item) {
            $date = $item->tanggal ? Carbon::parse($item->tanggal)->format('Y-m-d') : '-';
            $product = $item->produk->nama_produk ?? '-';

            return $date . '_' . $product;
        });

        $isFirstGroup = true;
        foreach ($groups as $groupKey => $groupItems) {
            if (!$isFirstGroup) {
                $rows[] = $this->pad([]);
                $rows[] = $this->pad([]);
            }
            $isFirstGroup = false;

            $firstItem = $groupItems->first();
            [$date] = explode('_', $groupKey, 2);
            $productName = $firstItem->produk->nama_produk ?? '-';

            $hariTanggal = '-';
            if ($date && $date !== '-') {
                try {
                    $hariTanggal = Carbon::parse($date)->translatedFormat('l, d-m-Y');
                } catch (\Throwable $e) {
                    $hariTanggal = $date;
                }
            }

            $companyStart = count($rows) + 1;
            $rows[] = $this->pad(['PT. CHAROEN POKPHAND INDONESIA']);
            $rows[] = $this->pad(['FOOD DIVISION ' . $planName]);
            $rows[] = $this->pad([$planName . ' - INDONESIA']);
            $this->mergeRowRange($companyStart, $companyStart + 2);
            $this->titleRows[] = ['start' => $companyStart, 'end' => $companyStart + 2];

            $rows[] = $this->pad([]);

            $titleRow = count($rows) + 1;
            $rows[] = $this->pad([$title]);
            $this->mergeCells[] = "A{$titleRow}:F{$titleRow}";
            $this->titleRows[] = ['start' => $titleRow, 'end' => $titleRow, 'size' => 14];

            $rows[] = $this->pad([]);

            $formStart = count($rows) + 1;
            $rows[] = $this->pad(['Hari / Tanggal', ': ' . $hariTanggal]);
            $rows[] = $this->pad(['NAMA PRODUK', ': ' . $productName]);
            $rows[] = $this->pad(['GRAMASE', ': ' . ($firstItem->berat_sampling ?? '-') . ' gram']);
            $rows[] = $this->pad(['Shift', ': ' . ($firstItem->shift->shift ?? '-')]);
            $formEnd = count($rows);
            $this->formInfoRanges[] = [$formStart, $formEnd];
            for ($r = $formStart; $r <= $formEnd; $r++) {
                $this->mergeCells[] = "B{$r}:F{$r}";
            }

            $rows[] = $this->pad([]);

            $summaryHeaderRow = count($rows) + 1;
            $rows[] = $this->pad([
                'No',
                'Jam',
                'Kode Produksi Emulsi',
                'Nama Emulsi',
                'Jumlah Proses',
                'Total Pemakaian',
            ]);
            $this->headerRows[] = $summaryHeaderRow;

            $summaryDataStart = count($rows) + 1;
            $rowNumber = 1;
            foreach ($groupItems as $item) {
                $rows[] = $this->pad([
                    $rowNumber++,
                    $item->jam ? Carbon::parse($item->jam)->format('H:i') : '-',
                    $item->kode_produksi_emulsi ?? '-',
                    $item->nama_emulsi->nama_emulsi ?? '-',
                    $item->nomor_emulsi->nomor_emulsi ?? '-',
                    $item->total_pemakaian->total_pemakaian ?? '-',
                ]);
            }
            $summaryDataEnd = count($rows);
            $this->tableRanges[] = [$summaryHeaderRow, $summaryDataEnd];

            foreach ($groupItems as $item) {
                if (!$item->suhuEmulsi || $item->suhuEmulsi->count() === 0) {
                    continue;
                }

                $rows[] = $this->pad([]);

                $detailTitleRow = count($rows) + 1;
                $rows[] = $this->pad(['Detail Bahan Emulsi - ' . ($item->kode_produksi_emulsi ?? '-')]);
                $this->mergeCells[] = "A{$detailTitleRow}:F{$detailTitleRow}";
                $this->titleRows[] = ['start' => $detailTitleRow, 'end' => $detailTitleRow, 'size' => 11, 'align' => 'left'];

                $detailHeaderRow = count($rows) + 1;
                $rows[] = $this->pad([
                    'No',
                    'Waktu',
                    'Nama RM',
                    'Berat (gram)',
                    'Kode Produksi Bahan',
                    'Kondisi',
                ]);
                $this->headerRows[] = $detailHeaderRow;

                $groupedData = $item->suhuEmulsi->groupBy('proses_ke');
                $kondisiArray = json_decode($item->kondisi, true) ?? [];
                $hasilArray = json_decode($item->hasil_emulsi, true) ?? [];

                $detailStart = $detailHeaderRow;
                $rowNum = 1;
                foreach ($groupedData as $prosesKe => $items) {
                    $prosesRow = count($rows) + 1;
                    $rows[] = $this->pad(['Proses Emulsi ke-' . $prosesKe]);
                    $this->mergeCells[] = "A{$prosesRow}:F{$prosesRow}";
                    $this->prosesHeaderRows[] = $prosesRow;

                    foreach ($items as $suhu) {
                        $dataRow = count($rows) + 1;
                        $rows[] = $this->pad([
                            $rowNum++,
                            $item->jam ? Carbon::parse($item->jam)->format('H:i') : '-',
                            $suhu->bahanEmulsi->nama_rm ?? '-',
                            $suhu->berat_bahan ?? $suhu->bahanEmulsi->berat_rm ?? '-',
                            $suhu->kode_produksi_bahan ?? '-',
                            $this->formatStatus($suhu->suhu ?? '-'),
                        ]);
                        $this->namaRmCells[] = "C{$dataRow}";
                    }

                    $suhuRow = count($rows) + 1;
                    $rows[] = $this->pad([
                        'Suhu',
                        '',
                        '',
                        $kondisiArray[$prosesKe - 1] ?? '-',
                    ]);
                    $this->mergeCells[] = "A{$suhuRow}:C{$suhuRow}";
                    $this->mergeCells[] = "D{$suhuRow}:F{$suhuRow}";
                    $this->sectionHeaderRows[] = $suhuRow;
                    $this->leftAlignRows[] = $suhuRow;

                    $hasil = $hasilArray[$prosesKe - 1] ?? '-';
                    $hasilRow = count($rows) + 1;
                    $rows[] = $this->pad([
                        'Hasil Emulsi',
                        '',
                        '',
                        $this->formatStatus($hasil),
                    ]);
                    $this->mergeCells[] = "A{$hasilRow}:C{$hasilRow}";
                    $this->mergeCells[] = "D{$hasilRow}:F{$hasilRow}";
                    $this->sectionHeaderRows[] = $hasilRow;
                    $this->leftAlignRows[] = $hasilRow;
                }

                $this->tableRanges[] = [$detailStart, count($rows)];
            }

            $rows[] = $this->pad([]);
            $kodeFormRow = count($rows) + 1;
            $rows[] = $this->pad(['', '', '', '', '', $firstItem->kode_form ?? $kodeForm]);
            $this->kodeFormRows[] = $kodeFormRow;

            [$qcName, $produksiName, $spvName] = $this->resolveApproverNames($groupItems);

            $rows[] = $this->pad([]);
            $rows[] = $this->pad([]);
            $sigLabelRow = count($rows) + 1;
            $rows[] = $this->pad([
                'Dibuat Oleh: QC INSPEKTOR',
                '',
                'Diketahui Oleh: Produksi',
                '',
                'Disetujui Oleh: Supervisor QC',
            ]);
            $this->mergeCells[] = "A{$sigLabelRow}:B{$sigLabelRow}";
            $this->mergeCells[] = "C{$sigLabelRow}:D{$sigLabelRow}";
            $this->mergeCells[] = "E{$sigLabelRow}:F{$sigLabelRow}";
            $this->signatureLabelRows[] = $sigLabelRow;

            $rows[] = $this->pad([]);
            $rows[] = $this->pad([]);

            $sigNameRow = count($rows) + 1;
            $rows[] = $this->pad([
                $qcName,
                '',
                $produksiName,
                '',
                $spvName,
            ]);
            $this->mergeCells[] = "A{$sigNameRow}:B{$sigNameRow}";
            $this->mergeCells[] = "C{$sigNameRow}:D{$sigNameRow}";
            $this->mergeCells[] = "E{$sigNameRow}:F{$sigNameRow}";
            $this->signatureNameRows[] = $sigNameRow;
        }

        if ($groups->isEmpty()) {
            $rows[] = $this->pad(['Tidak ada data pembuatan emulsi untuk filter ini']);
            $this->mergeCells[] = 'A1:F1';
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $thin = Border::BORDER_THIN;

                foreach ($this->mergeCells as $range) {
                    $sheet->mergeCells($range);
                }

                foreach ($this->titleRows as $meta) {
                    $range = 'A' . $meta['start'] . ':F' . $meta['end'];
                    $align = ($meta['align'] ?? 'center') === 'left'
                        ? Alignment::HORIZONTAL_LEFT
                        : Alignment::HORIZONTAL_CENTER;
                    $sheet->getStyle($range)->getFont()->setBold(true)->setSize($meta['size'] ?? 12);
                    $sheet->getStyle($range)->getAlignment()->setHorizontal($align)->setVertical(Alignment::VERTICAL_CENTER);
                }

                foreach ($this->formInfoRanges as [$start, $end]) {
                    $range = "A{$start}:F{$end}";
                    $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle($thin);
                    $sheet->getStyle($range)->getFont()->setSize(9);
                    $sheet->getStyle("A{$start}:A{$end}")->getFont()->setBold(true);
                    $sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }

                foreach ($this->tableRanges as [$start, $end]) {
                    $range = "A{$start}:F{$end}";
                    $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle($thin);
                    $sheet->getStyle($range)->getFont()->setSize(8);
                    $sheet->getStyle($range)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setWrapText(true);
                }

                foreach ($this->headerRows as $row) {
                    $range = "A{$row}:F{$row}";
                    $sheet->getStyle($range)->getFont()->setBold(true)->setSize(8);
                    $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F0F0F0');
                    $sheet->getStyle($range)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setWrapText(true);
                }

                foreach ($this->prosesHeaderRows as $row) {
                    $range = "A{$row}:F{$row}";
                    $sheet->getStyle($range)->getFont()->setBold(true)->setSize(9);
                    $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D0D0D0');
                    $sheet->getStyle($range)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }

                foreach ($this->sectionHeaderRows as $row) {
                    $range = "A{$row}:F{$row}";
                    $sheet->getStyle($range)->getFont()->setBold(true)->setSize(8);
                    $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E0E0E0');
                    $sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }

                foreach ($this->leftAlignRows as $row) {
                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                foreach ($this->namaRmCells as $cell) {
                    $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }

                foreach ($this->kodeFormRows as $row) {
                    $sheet->getStyle("F{$row}")->getFont()->setBold(true)->setSize(8);
                    $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }

                foreach ($this->signatureLabelRows as $row) {
                    $sheet->getStyle("A{$row}:F{$row}")->getFont()->setBold(true)->setSize(10);
                    $sheet->getStyle("A{$row}:F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                foreach ($this->signatureNameRows as $row) {
                    $sheet->getStyle("A{$row}:F{$row}")->getFont()->setSize(10);
                    $sheet->getStyle("A{$row}:F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $lineRow = $row - 1;
                    foreach (['A', 'C', 'E'] as $col) {
                        $sheet->getStyle("{$col}{$lineRow}")->getBorders()->getBottom()->setBorderStyle($thin);
                    }
                }

                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(32);
                $sheet->getColumnDimension('D')->setWidth(22);
                $sheet->getColumnDimension('E')->setWidth(22);
                $sheet->getColumnDimension('F')->setWidth(18);

                $sheet->getDefaultRowDimension()->setRowHeight(18);
            },
        ];
    }

    protected function pad(array $row): array
    {
        return array_pad($row, 6, '');
    }

    protected function mergeRowRange(int $start, int $end): void
    {
        for ($r = $start; $r <= $end; $r++) {
            $this->mergeCells[] = "A{$r}:F{$r}";
        }
    }

    protected function formatStatus($value): string
    {
        if ($value === '✔' || $value === '&#10004;') {
            return 'OK';
        }
        if ($value === '✘' || $value === '&#10008;') {
            return 'Tidak OK';
        }

        return $value === null || $value === '' ? '-' : (string) $value;
    }

    protected function resolveApproverNames($groupItems): array
    {
        $qcName = 'QC';
        $spvName = 'SPV';
        $produksiName = 'FM/FL PRODUKSI';

        foreach ($groupItems as $item) {
            if ($item->approved_by_qc && $item->qcApprover) {
                $qcName = $item->qcApprover->name;
                break;
            }
        }
        foreach ($groupItems as $item) {
            if ($item->approved_by_spv && $item->spvApprover) {
                $spvName = $item->spvApprover->name;
                break;
            }
        }
        foreach ($groupItems as $item) {
            if ($item->approved_by_produksi && $item->produksiApprover) {
                $produksiName = $item->produksiApprover->name;
                break;
            }
        }

        return [$qcName, $produksiName, $spvName];
    }
}
