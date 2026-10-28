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

class PemeriksaanBahanKemasExport implements FromCollection, WithHeadings, WithEvents
{
    protected Collection $rows;
    protected $qcApproverName = 'QC';
    protected $spvApproverName = 'SPV';
    protected $produksiApproverName = 'FM/FL PRODUKSI';

    public function __construct(protected Collection $data, protected array $filterInfo)
    {
        $this->checkApprovers();
        $this->rows = $this->flatten();
    }

    protected function checkApprovers()
    {
        // QC Approver (Dibuat Oleh:)
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
                if (!empty($item->createdBy?->name)) {
                    $this->qcApproverName = $item->createdBy->name;
                    break;
                }
            }
        }

        // Fallback jika belum di-approve QC: tampilkan nama user pembuat dokumen
        if ($this->qcApproverName === 'QC') {
            foreach ($this->data as $item) {
                if (!empty($item->user?->name)) {
                    $this->qcApproverName = $item->user->name;
                    break;
                }
                if (!empty($item->createdBy?->name)) {
                    $this->qcApproverName = $item->createdBy->name;
                    break;
                }
            }
        }

        // SPV Approver (Diperiksa Oleh:)
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

        // Produksi Approver (Diketahui Oleh:)
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

    protected function flatten(): Collection
    {
        $rows = collect();
        $no = 0;

        foreach ($this->data as $item) {
            $jam = $item->jam ? Carbon::parse($item->jam)->format('H:i') : '-';

            $rows->push([
                ++$no,
                $jam,
                $item->nama_kemasan ?? '-',
                $item->kode_produksi ?? '-',
                $item->kondisi_bahan_kemasan ?? '-',
                $item->keterangan ?? '-',
            ]);
        }

        return $rows;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Waktu',
            'Nama Kemasan',
            'Kode Produksi',
            'Kondisi',
            'Keterangan',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'F';

                $sheet->insertNewRowBefore(1, 7);

                $planTitle = 'FOOD DIVISION ' . strtoupper($this->filterInfo['plan_nama'] ?? '');
                $planLocation = strtoupper($this->filterInfo['plan_nama'] ?? '') . ' - INDONESIA';

                $sheet->mergeCells("A1:{$lastCol}1")->setCellValue('A1', 'PT. CHAROEN POKPHAND INDONESIA');
                $sheet->mergeCells("A2:{$lastCol}2")->setCellValue('A2', $planTitle);
                $sheet->mergeCells("A3:{$lastCol}3")->setCellValue('A3', $planLocation);
                
                $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells("A4:{$lastCol}4")->setCellValue('A4', 'PEMERIKSAAN KEDATANGAN KEMASAN DI PRODUKSI');
                $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $tanggalDisplay = '-';
                if (!empty($this->filterInfo['tanggal'])) {
                    $tanggalDisplay = $this->filterInfo['tanggal'];
                } else {
                    $tglMulai = $this->filterInfo['tanggal_mulai'] ?? null;
                    $tglAkhir = $this->filterInfo['tanggal_akhir'] ?? null;

                    $formatDate = function ($dateStr) {
                        if (empty($dateStr) || $dateStr === '-') return null;
                        try {
                            return Carbon::parse($dateStr)->format('d-m-Y');
                        } catch (\Throwable $e) {
                            return $dateStr;
                        }
                    };

                    $fMulai = $formatDate($tglMulai);
                    $fAkhir = $formatDate($tglAkhir);

                    if ($fMulai && $fAkhir) {
                        $tanggalDisplay = $fMulai . ' s/d ' . $fAkhir;
                    } elseif ($fMulai) {
                        $tanggalDisplay = $fMulai;
                    } elseif ($fAkhir) {
                        $tanggalDisplay = $fAkhir;
                    }
                }

                $sheet->mergeCells("A5:B5")->setCellValue('A5', 'Hari / Tanggal: ' . $tanggalDisplay);
                $sheet->getStyle('A5')->getFont()->setSize(9);
                $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $shiftDisplay = $this->filterInfo['shift'] ?? 'Semua Shift';
                $sheet->mergeCells("A6:B6")->setCellValue('A6', 'Shift: ' . $shiftDisplay);
                $sheet->getStyle('A6')->getFont()->setSize(9);
                $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $headerRow = 8;
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF0F0F0');
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getFont()->setBold(true)->setSize(8);
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $lastDataRow = $sheet->getHighestRow();
                $sheet->getStyle("A" . ($headerRow + 1) . ":{$lastCol}{$lastDataRow}")->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("A" . ($headerRow + 1) . ":{$lastCol}{$lastDataRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);
                $sheet->getStyle("A" . ($headerRow + 1) . ":{$lastCol}{$lastDataRow}")->getFont()->setSize(8);

                foreach (range(9, $lastDataRow) as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }

                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(25);

                $kodeFormRow = $lastDataRow + 2;
                $sheet->setCellValue("{$lastCol}{$kodeFormRow}", $this->filterInfo['kode_form'] ?? 'QF 03/00');
                $sheet->getStyle("{$lastCol}{$kodeFormRow}")->getFont()->setBold(true)->setSize(8);
                $sheet->getStyle("{$lastCol}{$kodeFormRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sigRow = $lastDataRow + 4;
                
                $col1Start = 'A'; $col1End = 'B';
                $col2Start = 'C'; $col2End = 'D';
                $col3Start = 'E'; $col3End = 'F';
                
                $sheet->mergeCells("{$col1Start}{$sigRow}:{$col1End}{$sigRow}")
                    ->setCellValue("{$col1Start}{$sigRow}", 'Dibuat Oleh:');
                $sheet->mergeCells("{$col2Start}{$sigRow}:{$col2End}{$sigRow}")
                    ->setCellValue("{$col2Start}{$sigRow}", 'Diperiksa Oleh:');
                $sheet->mergeCells("{$col3Start}{$sigRow}:{$col3End}{$sigRow}")
                    ->setCellValue("{$col3Start}{$sigRow}", 'Diketahui Oleh:');
                
                $sheet->getStyle("{$col1Start}{$sigRow}:{$col3End}{$sigRow}")->getFont()->setSize(10);
                $sheet->getStyle("{$col1Start}{$sigRow}:{$col3End}{$sigRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $nameRow = $sigRow + 5;
                $sheet->mergeCells("{$col1Start}{$nameRow}:{$col1End}{$nameRow}")
                    ->setCellValue("{$col1Start}{$nameRow}", $this->qcApproverName);
                $sheet->mergeCells("{$col2Start}{$nameRow}:{$col2End}{$nameRow}")
                    ->setCellValue("{$col2Start}{$nameRow}", $this->spvApproverName);
                $sheet->mergeCells("{$col3Start}{$nameRow}:{$col3End}{$nameRow}")
                    ->setCellValue("{$col3Start}{$nameRow}", $this->produksiApproverName);
                
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
