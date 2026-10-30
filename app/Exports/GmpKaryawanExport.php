<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class GmpKaryawanExport implements FromCollection, WithHeadings, WithEvents
{
    protected Collection $rows;

    public function __construct(
        protected Collection $data,
        protected array $filterInfo,
        protected Collection $areas,
        protected array $temuanOptions
    ) {
        $this->rows = $this->flatten();
    }

    protected function flatten(): Collection
    {
        $rows = collect();
        $no = 0;

        foreach ($this->data as $item) {
            if ($item->entries && is_array($item->entries)) {
                foreach ($item->entries as $entry) {
                    $verifikasi = $entry['verifikasi'] ?? null;
                    $rows->push([
                        ++$no,
                        $entry['jam'] ?? '-',
                        $this->areas[$entry['id_area'] ?? null] ?? '-',
                        $entry['nama_karyawan'] ?? '-',
                        $this->temuanOptions[$entry['temuan_ketidaksesuaian'] ?? null] ?? ($entry['temuan_ketidaksesuaian'] ?? '-'),
                        $entry['keterangan'] ?? '-',
                        $entry['tindakan_koreksi'] ?? '-',
                        $verifikasi === 'ok' ? 'OK' : ($verifikasi === 'tidak_ok' ? 'Tidak OK' : '-'),
                    ]);
                }
            } else {
                $verifikasi = $item->verifikasi ?? null;
                $rows->push([
                    ++$no,
                    $item->jam ? Carbon::parse($item->jam)->format('H:i') : '-',
                    $item->area->area ?? '-',
                    $item->nama_karyawan ?? '-',
                    $item->temuan_ketidaksesuaian_label ?? $item->temuan_ketidaksesuaian ?? '-',
                    $item->keterangan ?? '-',
                    $item->tindakan_koreksi ?? '-',
                    $verifikasi === 'ok' ? 'OK' : ($verifikasi === 'tidak_ok' ? 'Tidak OK' : '-'),
                ]);
            }
        }

        return $rows;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['No', 'Waktu', 'Area', 'Nama Karyawan', 'Temuan Ketidaksesuaian', 'Keterangan', 'Tindakan Koreksi', 'Verifikasi'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'H';

                $sheet->insertNewRowBefore(1, 5);

                $sheet->mergeCells("A1:{$lastCol}1")->setCellValue('A1', 'PT. CHAROEN POKPHAND INDONESIA');
                $sheet->mergeCells("A2:{$lastCol}2")->setCellValue('A2', 'FOOD DIVISION ' . strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN'));
                $sheet->mergeCells("A3:{$lastCol}3")->setCellValue('A3', 'KEBERSIHAN KARYAWAN');
                $sheet->getStyle('A1:A3')->getFont()->setBold(true);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A4', 'Tanggal: ' . $this->filterInfo['tanggal'] . '   |   Shift: ' . $this->filterInfo['shift']);

                $lastDataRow = $sheet->getHighestRow();
                $sheet->setCellValue("A" . ($lastDataRow + 2), $this->filterInfo['kode_form']);

                $sigRow = $lastDataRow + 4;
                $sheet->setCellValue("A{$sigRow}", 'Dibuat Oleh:');
                $sheet->setCellValue("D{$sigRow}", 'Diketahui Oleh:');
                $sheet->setCellValue("G{$sigRow}", 'Disetujui Oleh:');

                $nameRow = $sigRow + 4;
                $sheet->setCellValue("A{$nameRow}", 'QC Inspector');
                $sheet->setCellValue("D{$nameRow}", 'SPV/Foreman/Forelady Produksi');
                $sheet->setCellValue("G{$nameRow}", 'Supervisor QC');
            },
        ];
    }
}