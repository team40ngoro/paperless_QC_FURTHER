<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\User;

class ChillroomExport implements FromCollection, WithHeadings, WithEvents
{
    protected Collection $rows;
    protected $qcApproverName = 'QC';
    protected $spvApproverName = 'SPV';
    protected $produksiApproverName = 'FM/FL PRODUKSI';
    protected $hasSamplingData = false;

    public function __construct(protected Collection $data, protected array $filterInfo)
    {
        $this->checkApprovers();
        $this->checkSamplingData();
        $this->rows = $this->flatten();
    }

    protected function checkApprovers()
    {
        // QC Approver (Dibuat Oleh: QC INSPEKTOR)
        foreach ($this->data as $item) {
            if ($item->approved_by_qc) {
                if (!empty($item->qcApprover?->name)) {
                    $this->qcApproverName = $item->qcApprover->name;
                    break;
                }
                if ($item->qc_approved_by) {
                    $qcApprover = User::find($item->qc_approved_by);
                    if ($qcApprover && !empty($qcApprover->name)) {
                        $this->qcApproverName = $qcApprover->name;
                        break;
                    }
                }
                if (!empty($item->user?->name)) {
                    $this->qcApproverName = $item->user->name;
                    break;
                }
            }
        }

        // Fallback jika belum di-approve QC: tampilkan nama user pembuat dokumen (QC Inspector)
        if ($this->qcApproverName === 'QC') {
            foreach ($this->data as $item) {
                if (!empty($item->user?->name)) {
                    $this->qcApproverName = $item->user->name;
                    break;
                }
            }
        }

        // SPV Approver (Disetujui Oleh: Supervisor QC)
        foreach ($this->data as $item) {
            if ($item->approved_by_spv) {
                if (!empty($item->spvApprover?->name)) {
                    $this->spvApproverName = $item->spvApprover->name;
                    break;
                }
                if ($item->spv_approved_by) {
                    $spvApprover = User::find($item->spv_approved_by);
                    if ($spvApprover && !empty($spvApprover->name)) {
                        $this->spvApproverName = $spvApprover->name;
                        break;
                    }
                }
            }
        }

        // Produksi Approver (Diketahui Oleh: Produksi)
        foreach ($this->data as $item) {
            if ($item->approved_by_produksi) {
                if (!empty($item->produksiApprover?->name)) {
                    $this->produksiApproverName = $item->produksiApprover->name;
                    break;
                }
                if ($item->produksi_approved_by) {
                    $produksiApprover = User::find($item->produksi_approved_by);
                    if ($produksiApprover && !empty($produksiApprover->name)) {
                        $this->produksiApproverName = $produksiApprover->name;
                        break;
                    }
                }
            }
        }
    }

    protected function checkSamplingData()
    {
        foreach($this->data as $item) {
            $nilaiJumlahRm = json_decode($item->nilai_jumlah_rm, true);
            $jumlahRmData = json_decode($item->jumlah_rm, true);
            
            $hasStandarBerat = !empty($item->standar_berat) && trim((string) $item->standar_berat) !== '' && $item->standar_berat !== '-';
            
            $hasBeratSamples = false;
            if ($nilaiJumlahRm) {
                foreach ($nilaiJumlahRm as $entry) {
                    if (is_array($entry) && !empty($entry)) {
                        foreach($entry as $sample) {
                            if (is_numeric($sample) && (float)$sample > 0) {
                                $hasBeratSamples = true;
                                break 2;
                            }
                        }
                    }
                }
            }
            
            $hasAgregasi = false;
            if ($jumlahRmData) {
                foreach ($jumlahRmData as $entry) {
                    if (is_array($entry)) {
                        $atas = $entry['berat_atas'] ?? 0;
                        $std = $entry['berat_std'] ?? 0;
                        $bawah = $entry['berat_bawah'] ?? 0;
                        if ($atas > 0 || $std > 0 || $bawah > 0) {
                            $hasAgregasi = true;
                            break;
                        }
                    }
                }
            }
            
            $hasStatusRm = !empty($item->status_rm) && in_array($item->status_rm, ['diterima', 'diretur']);
            $hasCatatanRm = !empty($item->catatan_rm) && trim($item->catatan_rm) !== '' && $item->catatan_rm !== '-';
            
            if ($hasStandarBerat || $hasBeratSamples || $hasAgregasi || $hasStatusRm || $hasCatatanRm) {
                $this->hasSamplingData = true;
                break;
            }
        }
    }

    protected function flatten(): Collection
    {
        $rows = collect();
        $no = 0;

        foreach ($this->data as $item) {
            $jam = $item->jam_kedatangan ? Carbon::parse($item->jam_kedatangan)->format('H:i') : '-';

            $sensori = $item->sensori;
            if ($sensori == '✔' || $sensori == '&#10004;') {
                $sensori = 'OK';
            } elseif ($sensori == '✘' || $sensori == '&#10008;') {
                $sensori = 'Tidak OK';
            } else {
                $sensori = $sensori ?? '-';
            }

            $kemasan = $item->kemasan;
            if ($kemasan == '✔' || $kemasan == '&#10004;') {
                $kemasan = 'OK';
            } elseif ($kemasan == '✘' || $kemasan == '&#10008;') {
                $kemasan = 'Tidak OK';
            } else {
                $kemasan = $kemasan ?? '-';
            }

            $suhuVal = '-';
            if ($item->suhu == 0 && $item->berat == '-') {
                $suhuVal = 'Frozen';
            } elseif ($item->suhu !== null) {
                $suhuVal = $item->suhu . '°C';
            }

            $rowData = [
                ++$no,
                $jam,
                $item->nama_rm ?? '-',
                $item->kode_produksi ?? '-',
                $item->berat ? $item->berat : '-',
                $suhuVal,
                $sensori,
                $kemasan,
                $item->keterangan ?? '-',
                $item->catatan ?? '-',
            ];

            if ($this->hasSamplingData) {
                $nilaiJumlahRm = json_decode($item->nilai_jumlah_rm, true);
                $beratSamplesArr = [];
                if ($nilaiJumlahRm) {
                    foreach ($nilaiJumlahRm as $entry) {
                        if (is_array($entry)) {
                            foreach ($entry as $idx => $sample) {
                                if (is_numeric($sample)) {
                                    $beratSamplesArr[] = 'S' . ($idx + 1) . ': ' . number_format((float)$sample, 2) . 'gr';
                                }
                            }
                            break;
                        }
                    }
                }
                $aktualBeratStr = !empty($beratSamplesArr) ? implode(', ', $beratSamplesArr) : '-';

                $jumlahRmData = json_decode($item->jumlah_rm, true);
                $agregasiData = ['berat_atas' => 0, 'berat_std' => 0, 'berat_bawah' => 0];
                if ($jumlahRmData) {
                    foreach ($jumlahRmData as $entry) {
                        if (is_array($entry)) {
                            $agregasiData['berat_atas'] = $entry['berat_atas'] ?? 0;
                            $agregasiData['berat_std'] = $entry['berat_std'] ?? 0;
                            $agregasiData['berat_bawah'] = $entry['berat_bawah'] ?? 0;
                            break;
                        }
                    }
                }
                $totalSampling = $agregasiData['berat_atas'] + $agregasiData['berat_std'] + $agregasiData['berat_bawah'];
                
                $disamplingStrArr = [];
                $rekapStrArr = [];
                if ($totalSampling > 0) {
                    if ($agregasiData['berat_atas'] > 0) {
                        $disamplingStrArr[] = 'Atas: ' . $agregasiData['berat_atas'] . ' pcs';
                        $rekapStrArr[] = 'Atas: ' . number_format(($agregasiData['berat_atas'] / $totalSampling) * 100, 1) . '%';
                    }
                    if ($agregasiData['berat_std'] > 0) {
                        $disamplingStrArr[] = 'Std: ' . $agregasiData['berat_std'] . ' pcs';
                        $rekapStrArr[] = 'Std: ' . number_format(($agregasiData['berat_std'] / $totalSampling) * 100, 1) . '%';
                    }
                    if ($agregasiData['berat_bawah'] > 0) {
                        $disamplingStrArr[] = 'Bawah: ' . $agregasiData['berat_bawah'] . ' pcs';
                        $rekapStrArr[] = 'Bawah: ' . number_format(($agregasiData['berat_bawah'] / $totalSampling) * 100, 1) . '%';
                    }
                    $disamplingStrArr[] = 'Total: ' . $totalSampling . ' pcs';
                }
                $disamplingStr = !empty($disamplingStrArr) ? implode("\n", $disamplingStrArr) : '-';
                $rekapStr = !empty($rekapStrArr) ? implode("\n", $rekapStrArr) : '-';

                $statusRm = $item->status_rm === 'diterima' ? 'Diterima' : ($item->status_rm === 'diretur' ? 'Diretur' : '-');

                $rowData = array_merge($rowData, [
                    $item->standar_berat ?? '-',
                    $aktualBeratStr,
                    $disamplingStr,
                    $rekapStr,
                    $statusRm,
                    $item->catatan_rm ?? '-',
                ]);
            }

            $rows->push($rowData);
        }

        return $rows;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        $baseHeadings = [
            'No',
            'Jam Kedatangan',
            'Nama RM',
            'Kode Produksi',
            'Berat Perkemasan',
            'Suhu (°C)',
            'Sensori',
            'Kemasan',
            'Keterangan',
            'Catatan',
        ];

        if ($this->hasSamplingData) {
            $baseHeadings = array_merge($baseHeadings, [
                'Standar Berat Per PCS',
                'Hasil Aktual Berat Per PCS (gr)',
                'Jumlah yang Disampling',
                'Rekapitulasi (%)',
                'Status',
                'Catatan RM',
            ]);
        }

        return $baseHeadings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $lastCol = $this->hasSamplingData ? 'P' : 'J';
                $lastColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastCol);

                $sheet->insertNewRowBefore(1, 6);

                $planTitle = 'FOOD DIVISION ' . strtoupper($this->filterInfo['plan_nama'] ?? '');
                $planLocation = strtoupper($this->filterInfo['plan_nama'] ?? '') . ' - INDONESIA';
                
                $sheet->mergeCells("A1:{$lastCol}1")->setCellValue('A1', 'PT. CHAROEN POKPHAND INDONESIA');
                $sheet->mergeCells("A2:{$lastCol}2")->setCellValue('A2', $planTitle);
                $sheet->mergeCells("A3:{$lastCol}3")->setCellValue('A3', $planLocation);
                
                $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $sheet->mergeCells("A4:{$lastCol}4")->setCellValue('A4', 'PEMERIKSAAN KEDATANGAN BAHAN BAKU DAN PENUNJANG');
                $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $tanggalRange = 'Hari / Tanggal: ' . ($this->filterInfo['tanggal'] ?? '-');
                $sheet->mergeCells("A5:D5")->setCellValue("A5", $tanggalRange);
                $sheet->getStyle("A5")->getFont()->setSize(9);
                $sheet->getStyle("A5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $headerRow1 = 6;
                $headerRow2 = 7;
                
                if ($this->hasSamplingData) {
                    $sheet->mergeCells("A{$headerRow1}:J{$headerRow1}")->setCellValue("A{$headerRow1}", 'Pemeriksaan Kedatangan Bahan Baku');
                    $sheet->mergeCells("K{$headerRow1}:P{$headerRow1}")->setCellValue("K{$headerRow1}", 'Sampling Berat RM Daging SPO');
                } else {
                    $sheet->mergeCells("A{$headerRow1}:J{$headerRow1}")->setCellValue("A{$headerRow1}", 'Pemeriksaan Kedatangan Bahan Baku');
                }
                
                $sheet->getStyle("A{$headerRow1}:{$lastCol}{$headerRow1}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFC107');
                $sheet->getStyle("A{$headerRow1}:{$lastCol}{$headerRow1}")->getFont()->setBold(true);
                $sheet->getStyle("A{$headerRow1}:{$lastCol}{$headerRow1}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A{$headerRow1}:{$lastCol}{$headerRow1}")->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $sheet->getStyle("A{$headerRow2}:{$lastCol}{$headerRow2}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF0F0F0');
                $sheet->getStyle("A{$headerRow2}:{$lastCol}{$headerRow2}")->getFont()->setBold(true)->setSize(8);
                $sheet->getStyle("A{$headerRow2}:{$lastCol}{$headerRow2}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);

                $lastDataRow = $sheet->getHighestRow();
                
                $sheet->getStyle("A{$headerRow2}:{$lastCol}{$lastDataRow}")->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("A{$headerRow2}:{$lastCol}{$lastDataRow}")->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);
                $sheet->getStyle("A{$headerRow2}:{$lastCol}{$lastDataRow}")->getFont()->setSize(8);

                foreach (range(8, $lastDataRow) as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }

                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(12);
                $sheet->getColumnDimension('F')->setWidth(10);
                $sheet->getColumnDimension('G')->setWidth(10);
                $sheet->getColumnDimension('H')->setWidth(10);
                $sheet->getColumnDimension('I')->setWidth(15);
                $sheet->getColumnDimension('J')->setWidth(15);
                
                if ($this->hasSamplingData) {
                    $sheet->getColumnDimension('K')->setWidth(15);
                    $sheet->getColumnDimension('L')->setWidth(22);
                    $sheet->getColumnDimension('M')->setWidth(18);
                    $sheet->getColumnDimension('N')->setWidth(18);
                    $sheet->getColumnDimension('O')->setWidth(12);
                    $sheet->getColumnDimension('P')->setWidth(15);
                }

                $kodeFormRow = $lastDataRow + 2;
                $sheet->setCellValue("{$lastCol}{$kodeFormRow}", $this->filterInfo['kode_form'] ?? '');
                $sheet->getStyle("{$lastCol}{$kodeFormRow}")->getFont()->setBold(true)->setSize(8);
                $sheet->getStyle("{$lastCol}{$kodeFormRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sigRow = $lastDataRow + 4;
                
                $col1Start = 'A';
                $col1End = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex((int)($lastColIndex / 3));
                
                $col2Start = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex((int)($lastColIndex / 3) + 1);
                $col2End = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex((int)(2 * $lastColIndex / 3));
                
                $col3Start = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex((int)(2 * $lastColIndex / 3) + 1);
                $col3End = $lastCol;
                
                $sheet->mergeCells("{$col1Start}{$sigRow}:{$col1End}{$sigRow}")
                    ->setCellValue("{$col1Start}{$sigRow}", 'Dibuat Oleh: QC INSPEKTOR');
                $sheet->mergeCells("{$col2Start}{$sigRow}:{$col2End}{$sigRow}")
                    ->setCellValue("{$col2Start}{$sigRow}", 'Diketahui Oleh: Produksi');
                $sheet->mergeCells("{$col3Start}{$sigRow}:{$col3End}{$sigRow}")
                    ->setCellValue("{$col3Start}{$sigRow}", 'Disetujui Oleh: Supervisor QC');
                
                $sheet->getStyle("{$col1Start}{$sigRow}:{$col3End}{$sigRow}")->getFont()->setSize(10);
                $sheet->getStyle("{$col1Start}{$sigRow}:{$col3End}{$sigRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $nameRow = $sigRow + 5;
                $sheet->mergeCells("{$col1Start}{$nameRow}:{$col1End}{$nameRow}")
                    ->setCellValue("{$col1Start}{$nameRow}", $this->qcApproverName);
                $sheet->mergeCells("{$col2Start}{$nameRow}:{$col2End}{$nameRow}")
                    ->setCellValue("{$col2Start}{$nameRow}", $this->produksiApproverName);
                $sheet->mergeCells("{$col3Start}{$nameRow}:{$col3End}{$nameRow}")
                    ->setCellValue("{$col3Start}{$nameRow}", $this->spvApproverName);
                
                $sheet->getStyle("{$col1Start}{$nameRow}:{$col3End}{$nameRow}")->getFont()->setSize(10);
                $sheet->getStyle("{$col1Start}{$nameRow}:{$col3End}{$nameRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $lineRow = $nameRow - 1;
                $sheet->getStyle("{$col1Start}{$lineRow}:{$col1End}{$lineRow}")->getBorders()->getTop()
                    ->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("{$col2Start}{$lineRow}:{$col2End}{$lineRow}")->getBorders()->getTop()
                    ->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("{$col3Start}{$lineRow}:{$col3End}{$lineRow}")->getBorders()->getTop()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
