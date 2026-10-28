<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class VerifPeralatanExport implements FromCollection, WithHeadings, WithEvents
{
    protected Collection $rows;

    public function __construct(protected Collection $data, protected array $filterInfo)
    {
        $this->rows = $this->flatten();
    }

    protected function flatten(): Collection
    {
        $rows = collect();
        $no = 0;

        foreach ($this->data as $item) {
            $details = ($item->details ?? collect())->sortBy(function ($d) {
                return ($d->mesin->area->area ?? '') . '|' . ($d->mesin->nama_mesin ?? '');
            });

            foreach ($details as $d) {
                $hasBagian = $d->mesin && $d->mesin->bagian && $d->mesin->bagian->count() > 0;
                $savedBagianCount = $d->bagianDetails->count();
                $rowIsEmpty = ($hasBagian && $savedBagianCount === 0) || (!$hasBagian && $d->verifikasi === null);
                if ($rowIsEmpty) continue;

                $jamFormatted = $d->jam ? Carbon::parse($d->jam)->format('H:i') : ($item->jam ? Carbon::parse($item->jam)->format('H:i') : '-');

                if ($hasBagian && $savedBagianCount > 0) {
                    $bagianVerifMap = $d->bagianDetails->keyBy('id_bagian');
                    foreach ($d->mesin->bagian as $bagian) {
                        $bv = $bagianVerifMap->get($bagian->id);
                        if (!$bv) continue;

                        $rows->push([
                            ++$no,
                            $item->tanggal ? Carbon::parse($item->tanggal)->format('d-m-Y') : '-',
                            $jamFormatted,
                            $d->mesin->area->area ?? '-',
                            $d->mesin->nama_mesin ?? '-',
                            $bagian->nama_bagian,
                            $bv->verifikasi ? 'Bersih' : 'Kotor',
                            $bv->verifikasi ? '-' : ($bv->keterangan ?? '-'),
                            $bv->verifikasi ? '-' : ($bv->tindakan_koreksi ?? '-'),
                            $bv->verifikasi_hasil === null ? '-' : ($bv->verifikasi_hasil ? 'OK' : 'Tidak OK'),
                        ]);
                    }
                } else {
                    $rows->push([
                        ++$no,
                        $item->tanggal ? Carbon::parse($item->tanggal)->format('d-m-Y') : '-',
                        $jamFormatted,
                        $d->mesin->area->area ?? '-',
                        $d->mesin->nama_mesin ?? '-',
                        '-',
                        $d->verifikasi === null ? '-' : ($d->verifikasi ? 'Bersih' : 'Kotor'),
                        $d->verifikasi ? '-' : ($d->keterangan ?? '-'),
                        $d->verifikasi ? '-' : ($d->tindakan_koreksi ?? '-'),
                        $d->verifikasi_hasil === null ? '-' : ($d->verifikasi_hasil ? 'OK' : 'Tidak OK'),
                    ]);
                }
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
        return ['No', 'Tanggal', 'Jam', 'Area', 'Mesin', 'Bagian', 'Kondisi', 'Keterangan', 'Tindakan Koreksi', 'Verifikasi Hasil'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'J';

                $sheet->insertNewRowBefore(1, 5);

                $sheet->mergeCells("A1:{$lastCol}1")->setCellValue('A1', 'PT. CHAROEN POKPHAND INDONESIA');
                $sheet->mergeCells("A2:{$lastCol}2")->setCellValue('A2', 'FOOD DIVISION ' . strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN'));
                $sheet->mergeCells("A3:{$lastCol}3")->setCellValue('A3', 'VERIFIKASI KEBERSIHAN RUANGAN, MESIN, DAN PERALATAN');
                $sheet->getStyle('A1:A3')->getFont()->setBold(true);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A4', 'Tanggal: ' . $this->filterInfo['tanggal'] . '   |   Shift: ' . $this->filterInfo['shift']);

                $lastDataRow = $sheet->getHighestRow();
                $sheet->setCellValue("A" . ($lastDataRow + 2), $this->filterInfo['kode_form']);

                $sigRow = $lastDataRow + 4;
                $sheet->setCellValue("A{$sigRow}", 'Dibuat Oleh: QC INSPEKTOR');
                $sheet->setCellValue("E{$sigRow}", 'Diketahui Oleh: Produksi');
                $sheet->setCellValue("H{$sigRow}", 'Disetujui Oleh: Supervisor QC');
            },
        ];
    }
}