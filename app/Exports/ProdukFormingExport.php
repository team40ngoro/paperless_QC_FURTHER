<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProdukFormingExport implements FromArray, WithEvents
{
    protected array $mesinLegacyKeys = [
        'AUTOGRIND' => 'autogrind', 'BOWLCUTTER' => 'bowlcutter', 'AYAKAN SEASONING' => 'ayakan_seasoning',
        'UNIMIX' => 'unimix', 'REVOFORMER' => 'revoformer', 'BETTER MIXER' => 'better_mixer',
        'WET COATER' => 'wet_coater', 'BREADER' => 'breader', 'FRAYER 1' => 'frayer_1', 'FRAYER 2' => 'frayer_2',
        'IQF JBT' => 'iqf_jbt', 'KERANJANG' => 'keranjang', 'TIMBANGAN' => 'timbangan', 'MHW' => 'mhw',
        'FOOT SEALER' => 'foot_sealer', 'METAL DETECTOR' => 'metal_detector', 'ROTARY TABLE' => 'rotary_table',
        'CARTON SEALER' => 'carton_sealer', 'MEATCAR' => 'meatcar', 'CHECK WEIGHER BAG' => 'check_weigher_bag',
        'CHECK WEIGHER BOX' => 'check_weigher_box',
    ];

    public function __construct(protected Collection $data, protected array $filterInfo) {}

    public function array(): array
    {
        return []; // all content written directly in AfterSheet
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
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}")->setCellValue("A{$row}", 'VERIFIKASI PERGANTIAN PRODUK FORMING');
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

                    // Kemasan
                    $sheet->setCellValue("A{$row}", 'KEMASAN');
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
                        $val = $this->normalizeArr($value);
                        $sheet->fromArray([
                            $i . '. ' . $name,
                            $this->bracket($val, 1, 2), $this->bracket($val, 3, 4),
                            $this->bracket($val, 5, 6), $this->bracket($val, 7, 8),
                            '-', '-', $item->keterangan ?? '-',
                        ], null, "A{$row}");
                        $row++;
                        $i++;
                    }

                    // Mesin & Peralatan
                    $sheet->setCellValue("A{$row}", 'MESIN DAN PERALATAN');
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
                    } elseif (!empty($item->penilaian) && is_array($item->penilaian)) {
                        foreach ($item->penilaian as $i => $mesin) {
                            $val = $this->normalizeArr($mesin['nilai'] ?? []);
                            $sheet->fromArray([
                                ($i + 1) . '. ' . strtoupper($mesin['aspek'] ?? '-'),
                                $this->bracket($val, 1, 2), $this->bracket($val, 3, 4),
                                $this->bracket($val, 5, 6), $this->bracket($val, 7, 8),
                                $item->tindakan_koreksi ?? '-',
                                str_replace(['✓', '✗'], ['Ok', 'Tidak Ok'], $item->verifikasi ?? '-'),
                                $item->keterangan ?? '-',
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
                                str_replace(['✓', '✗'], ['Ok', 'Tidak Ok'], $item->verifikasi ?? '-'),
                                $item->keterangan ?? '-',
                            ], null, "A{$row}");
                            $row++;
                            $i++;
                        }
                    }

                    // Ruangan
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