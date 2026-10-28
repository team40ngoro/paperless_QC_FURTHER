<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProdukNonFormingExport implements FromArray, WithEvents
{
    protected array $mesinLegacyKeys = [
        'TUMBLER' => 'tumbler', 'FRAYER' => 'frayer', 'HICOOK' => 'hicook',
        'IQF ADVANCE 1' => 'iqf_advance_1', 'IQF ADVANCE 2' => 'iqf_advance_2',
        'KERANJANG' => 'keranjang', 'PALET' => 'palet', 'MEATCAR' => 'meatcar',
        'TIMBANGAN' => 'timbangan', 'MHW' => 'mhw', 'FOOT SEALER' => 'foot_sealer',
        'METAL DETECTOR' => 'metal_detector', 'CHECK WEIGHER BAG' => 'check_weigher_bag',
        'CHECK WEIGHER BOX' => 'check_weigher_box', 'KARTON SEALER' => 'karton_sealer',
    ];

    public function __construct(protected Collection $data, protected array $filterInfo) {}

    public function array(): array
    {
        return [];
    }

    protected function formatVerifikasi($verifikasi): string
    {
        if ($verifikasi === '✓') return 'OK';
        if ($verifikasi === '✗') return 'NOT OK';
        return $verifikasi ?? '-';
    }

    protected function bracket(array $val, int $lo, int $hi): string
    {
        return implode(', ', array_intersect($val, [$lo, $hi, (string) $lo, (string) $hi]));
    }

    protected function normalizeArr($val): array
    {
        if (!is_array($val)) {
            return $val ? [$val] : [];
        }
        return $val;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'H';
                $row = 1;

                $sheet->mergeCells("A{$row}:{$lastCol}{$row}")->setCellValue("A{$row}", 'PT. CHAROEN POKPHAND INDONESIA');
                $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                $row++;
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}")->setCellValue("A{$row}", 'FOOD DIVISION ' . strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN'));
                $row++;
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}")->setCellValue("A{$row}", 'VERIFIKASI PERGANTIAN PRODUK NON FORMING');
                $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                $row++;
                $sheet->setCellValue("A{$row}", 'Tanggal: ' . $this->filterInfo['tanggal'] . '   |   Shift: ' . $this->filterInfo['shift'] . '   |   Produk: ' . $this->filterInfo['produk']);
                $row += 2;

                foreach ($this->data as $item) {
                    $sheet->mergeCells("A{$row}:{$lastCol}{$row}")
                        ->setCellValue("A{$row}", ($item->produk->nama_produk ?? '-') . ' — ' . ($item->tanggal ? Carbon::parse($item->tanggal)->format('d-m-Y') : '-') . ' — Shift ' . ($item->shift->shift ?? '-'));
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);
                    $row += 2;

                    $sheet->fromArray(['Parameter', '1/2', '3/4', '5/6', '7/8', 'Tindakan Koreksi', 'Verifikasi', 'Keterangan'], null, "A{$row}");
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFont()->setBold(true);
                    $row++;

                    // A. Bahan Baku & Penunjang
                    $sheet->setCellValue("A{$row}", 'A. BAHAN BAKU DAN BAHAN PENUNJANG');
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                    $row++;

                    $gabungan = array_merge($item->bahan_baku ?? [], $item->bahan_penunjang ?? []);
                    if (empty($gabungan)) {
                        $sheet->fromArray(['-', '', '', '', '', '-', '-', $item->keterangan ?? '-'], null, "A{$row}");
                        $row++;
                    } else {
                        foreach ($gabungan as $i => $bahan) {
                            $val = $this->normalizeArr($bahan['penilaian'] ?? []);
                            $verif = str_replace(['✓', '✗'], ['Ok', 'Tidak Ok'], $bahan['verifikasi'] ?? '-');
                            $sheet->fromArray([
                                ($i + 1) . '. ' . strtoupper($bahan['nama'] ?? '-'),
                                $this->bracket($val, 1, 2), $this->bracket($val, 3, 4),
                                $this->bracket($val, 5, 6), $this->bracket($val, 7, 8),
                                $bahan['tindakan_koreksi'] ?? '-', $verif, $item->keterangan ?? '-',
                            ], null, "A{$row}");
                            $row++;
                        }
                    }

                    // B. Kemasan — single-integer values here, not arrays
                    $sheet->setCellValue("A{$row}", 'B. SISA KEMASAN DAN LABELISASI');
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                    $row++;

                    $kemasanItems = [
                        'KEMASAN PLASTIK' => $item->kemasan_plastik,
                        'KEMASAN KARTON' => $item->kemasan_karton,
                        'LABELISASI PLASTIK' => $item->labelisasi_plastik,
                        'LABELISASI KARTON' => $item->labelisasi_karton,
                    ];
                    $i = 1;
                    foreach ($kemasanItems as $name => $value) {
                        $sheet->fromArray([
                            $i . '. ' . $name,
                            in_array($value, [1, 2]) ? $value : '',
                            in_array($value, [3, 4]) ? $value : '',
                            in_array($value, [5, 6]) ? $value : '',
                            in_array($value, [7, 8]) ? $value : '',
                            $item->tindakan_koreksi ?? '-',
                            $this->formatVerifikasi($item->verifikasi),
                            $item->keterangan ?? '-',
                        ], null, "A{$row}");
                        $row++;
                        $i++;
                    }

                    // C. Mesin & Peralatan
                    $sheet->setCellValue("A{$row}", 'C. MESIN DAN PERALATAN');
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                    $row++;

                    if (!empty($item->mesin_peralatan) && is_array($item->mesin_peralatan)) {
                        foreach ($item->mesin_peralatan as $i => $mesin) {
                            $val = $this->normalizeArr($mesin['kondisi'] ?? []);
                            $verif = str_replace(['✓', '✗'], ['Ok', 'Tidak Ok'], $mesin['verifikasi'] ?? '-');
                            $sheet->fromArray([
                                ($i + 1) . '. ' . strtoupper($mesin['nama_mesin'] ?? '-'),
                                $this->bracket($val, 1, 2), $this->bracket($val, 3, 4),
                                $this->bracket($val, 5, 6), $this->bracket($val, 7, 8),
                                $mesin['tindakan_koreksi'] ?? '-', $verif, $item->keterangan ?? '-',
                            ], null, "A{$row}");
                            $row++;
                        }
                    } else {
                        $i = 1;
                        foreach ($this->mesinLegacyKeys as $name => $col) {
                            $val = $this->normalizeArr($item->$col ?? null);
                            $sheet->fromArray([
                                $i . '. ' . $name,
                                $this->bracket($val, 1, 2), $this->bracket($val, 3, 4),
                                $this->bracket($val, 5, 6), $this->bracket($val, 7, 8),
                                $item->tindakan_koreksi ?? '-',
                                $this->formatVerifikasi($item->verifikasi),
                                $item->keterangan ?? '-',
                            ], null, "A{$row}");
                            $row++;
                            $i++;
                        }
                    }

                    // D. Ruangan
                    if (!empty($item->ruangan) && is_array($item->ruangan)) {
                        $sheet->setCellValue("A{$row}", 'D. RUANGAN');
                        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                        $row++;
                        foreach ($item->ruangan as $i => $ruangan) {
                            $val = $this->normalizeArr($ruangan['kondisi'] ?? []);
                            $verif = str_replace(['✓', '✗'], ['Ok', 'Tidak Ok'], $ruangan['verifikasi'] ?? '-');
                            $sheet->fromArray([
                                ($i + 1) . '. ' . strtoupper($ruangan['nama_ruangan'] ?? '-'),
                                $this->bracket($val, 1, 2), $this->bracket($val, 3, 4),
                                $this->bracket($val, 5, 6), $this->bracket($val, 7, 8),
                                $ruangan['tindakan_koreksi'] ?? '-', $verif, '-',
                            ], null, "A{$row}");
                            $row++;
                        }
                    }

                    $sheet->setCellValue("A{$row}", 'Kode Form: ' . $this->filterInfo['kode_form']);
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                    $row += 3;
                }

                $sheet->getStyle("A1:{$lastCol}{$row}")->getAlignment()->setWrapText(true);
            },
        ];
    }
}