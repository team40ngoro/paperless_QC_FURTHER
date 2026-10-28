<?php

namespace App\Exports;

use App\Models\ProsesTumbling;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class WizardProses4Export implements FromArray, WithEvents
{
    protected $data;
    protected $filterInfo;

    protected array $mergeCells = [];
    protected array $titleRows = [];
    protected array $formInfoRanges = [];
    protected array $sectionTitleRows = [];
    protected array $headerRows = [];
    protected array $tableRanges = [];
    protected array $leftAlignCells = [];
    protected array $highlightRows = [];
    protected array $boldCells = [];
    protected array $kodeFormRows = [];
    protected array $signatureLabelRows = [];
    protected array $signatureNameRows = [];
    protected array $machineTitleRows = [];

    public function __construct($data = null, array $filterInfo = [])
    {
        $this->data = $data ?? collect();
        $this->filterInfo = $filterInfo;
    }

    public function array(): array
    {
        $rows = [];
        $kodeForm = $this->filterInfo['kode_form'] ?? 'QF 12/00';
        $lastCol = 'F';

        if ($this->data->isEmpty()) {
            $rows[] = $this->pad(['Tidak ada data yang sesuai dengan filter yang dipilih.']);
            $this->mergeCells[] = 'A1:F1';
            return $rows;
        }

        $isFirst = true;
        foreach ($this->data as $aging) {
            if (!$isFirst) {
                $rows[] = $this->pad([]);
                $rows[] = $this->pad([]);
            }
            $isFirst = false;

            $planName = strtoupper($aging->plan->nama_plan ?? $this->filterInfo['plan_name'] ?? auth()->user()->plan->nama_plan ?? 'PLANT');

            $companyStart = count($rows) + 1;
            $rows[] = $this->pad(['PT. CHAROEN POKPHAND INDONESIA']);
            $rows[] = $this->pad(['FOOD DIVISION ' . $planName]);
            $rows[] = $this->pad([$planName . ' - INDONESIA']);
            $this->mergeRow($companyStart, $companyStart + 2, $lastCol);
            $this->titleRows[] = ['start' => $companyStart, 'end' => $companyStart + 2, 'size' => 12];

            $rows[] = $this->pad([]);
            $titleRow = count($rows) + 1;
            $rows[] = $this->pad(['VERIFIKASI PROSES TUMBLING']);
            $this->mergeCells[] = "A{$titleRow}:{$lastCol}{$titleRow}";
            $this->titleRows[] = ['start' => $titleRow, 'end' => $titleRow, 'size' => 14];

            $hariTanggal = $aging->tanggal ? Carbon::parse($aging->tanggal)->format('l, d-m-Y') : '-';
            $shiftName = $aging->prosesTumbling->shift->shift ?? '-';
            $kodeProduksi = $aging->kode_produksi ?? ($aging->prosesTumbling->kode_produksi ?? '-');

            $rows[] = $this->pad([]);
            $formStart = count($rows) + 1;
            $rows[] = $this->pad(['Hari / Tanggal', ': ' . $hariTanggal, '', 'Produk', ': ' . ($aging->produk->nama_produk ?? '-')]);
            $rows[] = $this->pad(['Shift', ': ' . $shiftName, '', 'Kode Produksi', ': ' . $kodeProduksi]);
            $formEnd = count($rows);
            $this->formInfoRanges[] = [$formStart, $formEnd];
            $this->mergeCells[] = "B{$formStart}:C{$formStart}";
            $this->mergeCells[] = "E{$formStart}:F{$formStart}";
            $this->mergeCells[] = "B{$formEnd}:C{$formEnd}";
            $this->mergeCells[] = "E{$formEnd}:F{$formEnd}";

            $bahan = $aging->bahanBaku;
            if (!$bahan && $aging->prosesTumbling) {
                $bahan = $aging->prosesTumbling->bahanBakuTumbling;
            }

            if ($bahan) {
                $rows[] = $this->pad([]);
                $secRow = count($rows) + 1;
                $rows[] = $this->pad(['1. DATA BAHAN BAKU & PENCAMPURAN']);
                $this->mergeCells[] = "A{$secRow}:{$lastCol}{$secRow}";
                $this->sectionTitleRows[] = $secRow;

                $bbHeader = count($rows) + 1;
                $rows[] = $this->pad(['No', 'Nama Bahan Baku', 'Kode Produksi', 'Jumlah (Kg)', 'Suhu (°C)', 'Kondisi']);
                $this->headerRows[] = [$bbHeader, $lastCol];
                $bbStart = $bbHeader;

                $manual = $bahan->manual_bahan_data ?? [];
                if (!empty($manual)) {
                    foreach ($manual as $i => $b) {
                        $dataRow = count($rows) + 1;
                        $rows[] = $this->pad([
                            $i + 1,
                            $b['nama_bahan_baku'] ?? '-',
                            $b['kode_produksi_bahan_baku'] ?? '-',
                            $b['jumlah'] ?? '-',
                            $b['suhu'] ?? '-',
                            $this->formatKondisi($b['kondisi_daging'] ?? '-'),
                        ]);
                        $this->leftAlignCells[] = "B{$dataRow}";
                    }
                } else {
                    $emptyRow = count($rows) + 1;
                    $rows[] = $this->pad(['Data bahan baku tidak ditemukan.']);
                    $this->mergeCells[] = "A{$emptyRow}:{$lastCol}{$emptyRow}";
                }
                $this->tableRanges[] = [$bbStart, count($rows), $lastCol];

                $metaParts = [];
                if (!empty($bahan->gramase) && $bahan->gramase !== '-') {
                    $metaParts[] = 'Gramase: ' . $bahan->gramase . ' g';
                }
                if (!empty($bahan->salinity) && $bahan->salinity !== '-') {
                    $metaParts[] = 'Salinity: ' . $bahan->salinity;
                }
                if (!empty($bahan->viskositas) && $bahan->viskositas !== '-') {
                    $metaParts[] = 'Brix: ' . $bahan->viskositas;
                }
                $metaParts[] = 'Hasil Pencampuran: ' . $this->formatKondisi($bahan->hasil_pencampuran ?? '-');
                $metaRow = count($rows) + 1;
                $rows[] = $this->pad($metaParts);
                $this->tableRanges[] = [$metaRow, $metaRow, $lastCol];
                if (count($metaParts) === 1) {
                    $this->mergeCells[] = "A{$metaRow}:{$lastCol}{$metaRow}";
                } elseif (count($metaParts) === 2) {
                    $this->mergeCells[] = "A{$metaRow}:C{$metaRow}";
                    $this->mergeCells[] = "D{$metaRow}:F{$metaRow}";
                } elseif (count($metaParts) === 3) {
                    $this->mergeCells[] = "A{$metaRow}:B{$metaRow}";
                    $this->mergeCells[] = "C{$metaRow}:D{$metaRow}";
                    $this->mergeCells[] = "E{$metaRow}:F{$metaRow}";
                } else {
                    $this->mergeCells[] = "A{$metaRow}:B{$metaRow}";
                    $this->mergeCells[] = "C{$metaRow}:C{$metaRow}";
                    $this->mergeCells[] = "D{$metaRow}:E{$metaRow}";
                    $this->mergeCells[] = "F{$metaRow}:F{$metaRow}";
                }
            }

            $rows[] = $this->pad([]);
            $tumTitle = count($rows) + 1;
            $rows[] = $this->pad(['2. PROSES TUMBLING']);
            $this->mergeCells[] = "A{$tumTitle}:{$lastCol}{$tumTitle}";
            $this->sectionTitleRows[] = $tumTitle;

            $bahanUuid = $aging->bahan_baku_tumbling_uuid ?? ($aging->prosesTumbling->bahan_baku_tumbling_uuid ?? null);
            $allTumbling = ProsesTumbling::with(['dataTumbling'])
                ->where('bahan_baku_tumbling_uuid', $bahanUuid)
                ->get();

            foreach ($allTumbling as $tum) {
                [$hasVakum, $hasNonVakum] = $this->detectTumblingType($tum);

                $machineRow = count($rows) + 1;
                $rows[] = $this->pad([$tum->dataTumbling->nama_tumbling ?? 'MESIN TUMBLING']);
                $this->mergeCells[] = "A{$machineRow}:{$lastCol}{$machineRow}";
                $this->machineTitleRows[] = $machineRow;

                $h1 = count($rows) + 1;
                $header1 = ['PARAMETER'];
                if ($hasVakum) {
                    $header1[] = 'TUMBLING VAKUM';
                    $header1[] = '';
                }
                if ($hasNonVakum) {
                    $header1[] = 'TUMBLING NON-VAKUM';
                    $header1[] = '';
                }
                $rows[] = $this->pad($header1);

                $h2 = count($rows) + 1;
                $header2 = [''];
                if ($hasVakum) {
                    $header2[] = 'STANDART';
                    $header2[] = 'AKTUAL';
                }
                if ($hasNonVakum) {
                    $header2[] = 'STANDART';
                    $header2[] = 'AKTUAL';
                }
                $rows[] = $this->pad($header2);

                $this->mergeCells[] = "A{$h1}:A{$h2}";
                if ($hasVakum && $hasNonVakum) {
                    $this->mergeCells[] = "B{$h1}:C{$h1}";
                    $this->mergeCells[] = "D{$h1}:E{$h1}";
                    $tumLast = 'E';
                } elseif ($hasVakum || $hasNonVakum) {
                    $this->mergeCells[] = "B{$h1}:C{$h1}";
                    $tumLast = 'C';
                } else {
                    $tumLast = 'C';
                }
                $this->headerRows[] = [$h1, $tumLast];
                $this->headerRows[] = [$h2, $tumLast];

                $paramRows = [
                    ['Drum On', $tum->dataTumbling->drum_on ?? '-', $tum->aktual_drum_on ?? '-', $tum->dataTumbling->drum_on_non_vakum ?? '-', $tum->aktual_drum_on_non_vakum ?? '-'],
                    ['Drum Off', $tum->dataTumbling->drum_off ?? '-', $tum->aktual_drum_off ?? '-', $tum->dataTumbling->drum_off_non_vakum ?? '-', $tum->aktual_drum_off_non_vakum ?? '-'],
                    ['Speed (Rpm)', $tum->dataTumbling->drum_speed ?? '-', $tum->aktual_speed ?? '-', $tum->dataTumbling->drum_speed_non_vakum ?? '-', $tum->aktual_speed_non_vakum ?? '-'],
                    ['Total Waktu', $tum->dataTumbling->total_waktu ?? '-', $tum->aktual_total_waktu ?? '-', $tum->dataTumbling->total_waktu_non_vakum ?? '-', $tum->aktual_total_waktu_non_vakum ?? '-'],
                    ['Tekanan / Vakum', $tum->dataTumbling->tekanan_vakum ?? '-', $tum->aktual_vakum ?? '-', $tum->dataTumbling->tekanan_non_vakum ?? '-', $tum->aktual_tekanan_non_vakum ?? '-'],
                ];

                foreach ($paramRows as $param) {
                    $dataRow = count($rows) + 1;
                    $line = [$param[0]];
                    if ($hasVakum) {
                        $line[] = $param[1];
                        $line[] = $param[2];
                    }
                    if ($hasNonVakum) {
                        $line[] = $param[3];
                        $line[] = $param[4];
                    }
                    $rows[] = $this->pad($line);
                    $this->leftAlignCells[] = "A{$dataRow}";
                }

                $waktuRow = count($rows) + 1;
                $waktuLine = ['Waktu (Start - End)'];
                if ($hasVakum) {
                    $waktuLine[] = ($tum->waktu_mulai_tumbling ?? '-') . ' - ' . ($tum->waktu_selesai_tumbling ?? '-');
                    $waktuLine[] = '';
                }
                if ($hasNonVakum) {
                    $waktuLine[] = ($tum->waktu_mulai_tumbling_non_vakum ?? '-') . ' - ' . ($tum->waktu_selesai_tumbling_non_vakum ?? '-');
                    $waktuLine[] = '';
                }
                $rows[] = $this->pad($waktuLine);
                $this->leftAlignCells[] = "A{$waktuRow}";
                if ($hasVakum && $hasNonVakum) {
                    $this->mergeCells[] = "B{$waktuRow}:C{$waktuRow}";
                    $this->mergeCells[] = "D{$waktuRow}:E{$waktuRow}";
                } else {
                    $this->mergeCells[] = "B{$waktuRow}:C{$waktuRow}";
                }

                $colspanLast = ($hasVakum && $hasNonVakum) ? 'E' : 'C';
                $suhuRow = count($rows) + 1;
                $rows[] = $this->pad(['Suhu Hasil Tumbling', ($tum->suhu_hasil_tumbling ?? '-') . ' °C']);
                $this->mergeCells[] = "B{$suhuRow}:{$colspanLast}{$suhuRow}";
                $this->leftAlignCells[] = "A{$suhuRow}";

                $sensRow = count($rows) + 1;
                $rows[] = $this->pad(['Sensori', strtoupper($tum->sensori_hasil_tumbling ?? '-')]);
                $this->mergeCells[] = "B{$sensRow}:{$colspanLast}{$sensRow}";
                $this->leftAlignCells[] = "A{$sensRow}";

                $this->tableRanges[] = [$h1, $sensRow, $tumLast];
            }

            $isAgingUsed = !($aging->is_dummy_aging ?? false);
            if ($isAgingUsed) {
                $waktuMulai = trim((string) ($aging->waktu_mulai_aging ?? ''));
                $waktuSelesai = trim((string) ($aging->waktu_selesai_aging ?? ''));
                $suhuProduk = trim((string) ($aging->suhu_produk ?? ''));
                if ($waktuMulai === '' && $waktuSelesai === '' && $suhuProduk === '') {
                    $isAgingUsed = false;
                }
            }

            if ($isAgingUsed) {
                $rows[] = $this->pad([]);
                $agingTitle = count($rows) + 1;
                $rows[] = $this->pad(['3. HASIL PROSES AGING']);
                $this->mergeCells[] = "A{$agingTitle}:{$lastCol}{$agingTitle}";
                $this->sectionTitleRows[] = $agingTitle;

                $agHeader = count($rows) + 1;
                $rows[] = $this->pad(['No', 'Waktu Mulai Aging', 'Suhu Produk', 'Kondisi Produk']);
                $this->headerRows[] = [$agHeader, 'D'];
                $agStart = $agHeader;

                $waktuMulaiArray = array_map('trim', explode(',', $aging->waktu_mulai_aging ?? ''));
                $suhuKondisiArray = array_map('trim', explode(',', $aging->suhu_produk ?? ''));
                $maxCount = max(count($waktuMulaiArray), count($suhuKondisiArray));

                for ($index = 0; $index < $maxCount; $index++) {
                    $waktu = $waktuMulaiArray[$index] ?? '';
                    $suhuKondisi = $suhuKondisiArray[$index] ?? '';
                    if (preg_match('/^(.+?)\s*\((.+?)\)$/', $suhuKondisi, $matches)) {
                        $suhu = trim($matches[1]);
                        $kondisi = trim($matches[2]);
                    } else {
                        $suhu = $suhuKondisi;
                        $kondisi = $aging->kondisi_produk ?? '✓';
                    }
                    $rows[] = $this->pad([
                        $index + 1,
                        $waktu,
                        $suhu,
                        ($kondisi === '✓' || $kondisi === 'OK') ? 'OK' : 'Tidak OK',
                    ]);
                    $this->boldCells[] = 'B' . count($rows);
                    $this->boldCells[] = 'C' . count($rows);
                }
                $this->tableRanges[] = [$agStart, count($rows), 'D'];

                $selesaiRow = count($rows) + 1;
                $rows[] = $this->pad(['Waktu Selesai Aging: ' . ($aging->waktu_selesai_aging ?? '-')]);
                $this->mergeCells[] = "A{$selesaiRow}:{$lastCol}{$selesaiRow}";
                $this->highlightRows[] = $selesaiRow;
                $this->tableRanges[] = [$selesaiRow, $selesaiRow, $lastCol];
            }

            $rows[] = $this->pad([]);
            $kodeRow = count($rows) + 1;
            $kodeLine = $this->pad([]);
            $kodeLine[5] = $kodeForm;
            $rows[] = $kodeLine;
            $this->kodeFormRows[] = $kodeRow;

            [$qcName, $produksiName, $spvName] = $this->resolveApproverNames($aging);

            $rows[] = $this->pad([]);
            $rows[] = $this->pad([]);
            $sigLabel = count($rows) + 1;
            $rows[] = $this->pad([
                'Dibuat Oleh: QC INSPEKTOR', '',
                'Diketahui Oleh: Produksi', '',
                'Disetujui Oleh: Supervisor QC',
            ]);
            $this->mergeCells[] = "A{$sigLabel}:B{$sigLabel}";
            $this->mergeCells[] = "C{$sigLabel}:D{$sigLabel}";
            $this->mergeCells[] = "E{$sigLabel}:F{$sigLabel}";
            $this->signatureLabelRows[] = $sigLabel;

            $rows[] = $this->pad([]);
            $rows[] = $this->pad([]);
            $sigName = count($rows) + 1;
            $rows[] = $this->pad([$qcName, '', $produksiName, '', $spvName]);
            $this->mergeCells[] = "A{$sigName}:B{$sigName}";
            $this->mergeCells[] = "C{$sigName}:D{$sigName}";
            $this->mergeCells[] = "E{$sigName}:F{$sigName}";
            $this->signatureNameRows[] = $sigName;
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $thin = Border::BORDER_THIN;
                $lastCol = 'F';

                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
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
                    $sheet->getStyle("D{$start}:D{$end}")->getFont()->setBold(true);
                }

                foreach ($this->sectionTitleRows as $row) {
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(11);
                }

                foreach ($this->machineTitleRows as $row) {
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFont()->setBold(true)->setSize(10);
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

                foreach ($this->headerRows as [$row, $col]) {
                    $end = is_numeric($col) ? $lastCol : $col;
                    $range = str_contains((string) $row, ':') ? $row : "A{$row}:{$end}{$row}";
                    if (is_int($row) || ctype_digit((string) $row)) {
                        $range = "A{$row}:{$end}{$row}";
                    }
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

                foreach ($this->highlightRows as $row) {
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8F5E9');
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                }

                foreach ($this->boldCells as $cell) {
                    $sheet->getStyle($cell)->getFont()->setBold(true);
                }

                foreach ($this->kodeFormRows as $row) {
                    $sheet->getStyle("F{$row}")->getFont()->setBold(true)->setSize(8);
                    $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }

                foreach ($this->signatureLabelRows as $row) {
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFont()->setBold(true)->setSize(10);
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                foreach ($this->signatureNameRows as $row) {
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFont()->setSize(10);
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $lineRow = $row - 1;
                    foreach (['A', 'C', 'E'] as $col) {
                        $sheet->getStyle("{$col}{$lineRow}")->getBorders()->getBottom()->setBorderStyle($thin);
                    }
                }

                $sheet->getColumnDimension('A')->setWidth(22);
                $sheet->getColumnDimension('B')->setWidth(22);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(18);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(16);
            },
        ];
    }

    protected function pad(array $row): array
    {
        return array_pad($row, 6, '');
    }

    protected function mergeRow(int $start, int $end, string $lastCol): void
    {
        for ($r = $start; $r <= $end; $r++) {
            $this->mergeCells[] = "A{$r}:{$lastCol}{$r}";
        }
    }

    protected function detectTumblingType($tum): array
    {
        $hasValue = function ($val) {
            return !empty($val) && $val !== '-' && $val !== '---' && $val !== 'null';
        };

        $hasVakum = false;
        foreach ([$tum->aktual_drum_on, $tum->aktual_drum_off, $tum->aktual_speed, $tum->aktual_total_waktu, $tum->aktual_vakum, $tum->waktu_mulai_tumbling, $tum->waktu_selesai_tumbling] as $val) {
            if ($hasValue($val)) {
                $hasVakum = true;
                break;
            }
        }

        $hasNonVakum = false;
        foreach ([$tum->aktual_drum_on_non_vakum, $tum->aktual_drum_off_non_vakum, $tum->aktual_speed_non_vakum, $tum->aktual_total_waktu_non_vakum, $tum->aktual_tekanan_non_vakum, $tum->waktu_mulai_tumbling_non_vakum, $tum->waktu_selesai_tumbling_non_vakum] as $val) {
            if ($hasValue($val)) {
                $hasNonVakum = true;
                break;
            }
        }

        if (!$hasVakum && !$hasNonVakum) {
            return [true, true];
        }

        return [$hasVakum, $hasNonVakum];
    }

    protected function formatKondisi($value): string
    {
        $value = (string) $value;
        if (in_array($value, ['✓', 'OK', '✔'], true) || str_contains($value, '✔')) {
            return 'OK';
        }
        if (in_array($value, ['×', '✗', '✘', 'Tidak OK'], true) || str_contains($value, '✘')) {
            return 'Tidak OK';
        }

        return $value === '' ? '-' : $value;
    }

    protected function resolveApproverNames($aging): array
    {
        $source = (($aging->is_dummy_aging ?? false) && $aging->prosesTumbling)
            ? $aging->prosesTumbling
            : $aging;
        $qcName = 'QC';
        $produksiName = 'FM/FL PRODUKSI';
        $spvName = 'SPV';

        if ($source->approved_by_qc && ($source->qcApprover || $source->qc_approved_by)) {
            $qcName = $source->qcApprover->name ?? $qcName;
        }
        if ($source->approved_by_produksi && ($source->produksiApprover || $source->produksi_approved_by)) {
            $produksiName = $source->produksiApprover->name ?? $produksiName;
        }
        if ($source->approved_by_spv && ($source->spvApprover || $source->spv_approved_by)) {
            $spvName = $source->spvApprover->name ?? $spvName;
        }

        return [$qcName, $produksiName, $spvName];
    }
}
