<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Produk Forming</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 7px;
            margin: 0;
            padding: 8px;
            line-height: 1.1;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .company-logo {
            float: left;
            width: 40px;
            height: 40px;
            text-align: center;
            line-height: 40px;
            font-weight: bold;
            font-size: 10px;
        }
        .company-info {
            margin-left: 50px;
            text-align: left;
        }
        .company-name {
            font-weight: bold;
            font-size: 8px;
        }
        .title {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            margin: 8px 0;
        }
        .filter-info {
            margin-bottom: 5px;
            padding: 4px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .filter-info h4 {
            margin: 0 0 3px 0;
            font-size: 7px;
            font-weight: bold;
        }
        .filter-info p {
            margin: 1px 0;
            font-size: 6px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            page-break-inside: auto;
            border: 1px solid #000;
        }
        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 1px;
            text-align: center;
            font-size: 5px;
            vertical-align: middle;
            page-break-inside: avoid;
        }
        .merged-cell {
            border: 1px solid #000 !important;
            border-collapse: separate !important;
        }
        .section-group {
            page-break-inside: avoid;
        }
        @media print {
            .main-table {
                border-collapse: collapse;
                border-spacing: 0;
            }
            .main-table td,
            .main-table th {
                border: 1px solid #000;
                position: relative;
            }
            .merged-cell {
                border: 1px solid #000 !important;
                background-color: #fff;
            }
        }
        .section-merged-cell {
            writing-mode: vertical-lr;
            text-orientation: mixed;
            width: 80px;
            min-width: 80px;
            max-width: 80px;
        }
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-left {
            text-align: left;
        }
        .bahan-list {
            text-align: left;
            font-size: 4px;
        }
        .bahan-list ul {
            margin: 0;
            padding-left: 10px;
        }
        .bahan-list li {
            margin-bottom: 1px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 3px 5px;
            border: none;
        }
        .signature-label {
            text-align: center;
            font-size: 8px;
            margin-bottom: 3px;
        }
        .signature-qr-area {
            height: 55px;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 3px;
            margin-bottom: 3px;
        }
        .signature-name {
            font-size: 8px;
            text-align: center;
        }
        .qr-code-img {
            max-height: 45px;
            max-width: 45px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-logo">
            <img src="{{ public_path('dist/img/cpi-logo.png') }}" alt="CPI Logo" style="width: 60px; height: 60px; object-fit: contain;">
        </div>
        <div class="company-info">
            <div class="company-name">PT. CHAROEN POKPHAND INDONESIA</div>
            <div>FOOD DIVISION {{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }}</div>
            <div>{{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }} - INDONESIA</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="title">VERIFIKASI PERGANTIAN PRODUK FORMING</div>

    <div class="filter-info">
        <h4>Informasi Filter</h4>
        <p><strong>Tanggal:</strong> {{ $filterInfo['tanggal'] }} | <strong>Shift:</strong> {{ $filterInfo['shift'] }} | <strong>Produk:</strong> {{ $filterInfo['produk'] }} | </p>
    </div>

    @if(count($data) > 0)
        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="2">PARAMETER PENGECEKAN</th>
                    <th colspan="4">PENILAIAN KONDISI BAHAN/PERALATAN</th>
                    <th rowspan="2">TINDAKAN KOREKSI</th>
                    <th rowspan="2">VERIFIKASI</th>
                    <th rowspan="2">KETERANGAN</th>
                </tr>
                <tr>
                    <th>1/2</th>
                    <th>3/4</th>
                    <th>5/6</th>
                    <th>7/8</th>
                </tr>
            </thead>
            <tbody>
                <!-- A. BAHAN BAKU DAN BAHAN PENUNJANG Section (digabung) -->
                <tr>
                    <td colspan="8" style="background-color: #f0f0f0; font-weight: bold; text-align: left; padding: 8px;">A. BAHAN BAKU DAN BAHAN PENUNJANG</td>
                </tr>
                                @php
                    $gabungan = [];
                    foreach($data as $item) {
                        if($item->bahan_baku && count($item->bahan_baku) > 0) {
                            foreach($item->bahan_baku as $b) { $gabungan[] = $b; }
                        }
                        if($item->bahan_penunjang && count($item->bahan_penunjang) > 0) {
                            foreach($item->bahan_penunjang as $b) { $gabungan[] = $b; }
                        }
                        break;
                    }
                    $gabunganCount = count($gabungan);
                    $totalRows = max($gabunganCount, 1);
                    $firstItem = $data->first();
                @endphp
                @if($gabunganCount == 0)
                    <tr>
                        <td style="text-align: left; padding-left: 20px;">-</td>
                        <td></td><td></td><td></td><td></td>
                        <td style="vertical-align: middle; border: 1px solid #000; text-align: center; font-size: 7px;">-</td>
                        <td style="vertical-align: middle; border: 1px solid #000; text-align: center; font-size: 7px;">-</td>
                        <td style="vertical-align: middle; border: 1px solid #000; text-align: center; font-size: 7px;">{{ $firstItem->keterangan ?? '-' }}</td>
                    </tr>
                @else
                    @foreach($gabungan as $gIdx => $bahan)
                        @php
                            $val = $bahan['penilaian'] ?? [];
                            if (!is_array($val)) $val = $val ? [$val] : [];
                        @endphp
                        <tr>
                            <td style="text-align: left; padding-left: 20px;">{{ $gIdx + 1 }}. {{ strtoupper($bahan['nama'] ?? '-') }}</td>
                            <td>{{ implode(', ', array_intersect($val, [1, 2, '1', '2'])) }}</td>
                            <td>{{ implode(', ', array_intersect($val, [3, 4, '3', '4'])) }}</td>
                            <td>{{ implode(', ', array_intersect($val, [5, 6, '5', '6'])) }}</td>
                            <td>{{ implode(', ', array_intersect($val, [7, 8, '7', '8'])) }}</td>
                            <td style="vertical-align: middle; border: 1px solid #000; text-align: center; font-size: 7px;">{{ $bahan['tindakan_koreksi'] ?? '-' }}</td>
                            <td style="vertical-align: middle; border: 1px solid #000; text-align: center; font-size: 7px;">{{ str_replace(['✓', '✗'], ['Ok', 'Tidak Ok'], $bahan['verifikasi'] ?? '-') }}</td>
                            <td style="vertical-align: middle; border: 1px solid #000; text-align: center; font-size: 7px;">{{ $firstItem->keterangan ?? '-' }}</td>
                        </tr>
                    @endforeach
                @endif

                <!-- KEMASAN Section -->
                <tr>
                    <td colspan="8" style="background-color: #f0f0f0; font-weight: bold; text-align: left; padding: 8px;">KEMASAN</td>
                </tr>
                @php 
                    $kemasan_items = [
                        ['name' => 'KEMASAN PLASTIK', 'value' => $data->first()->kemasan_plastik ?? null],
                        ['name' => 'KEMASAN KARTON', 'value' => $data->first()->kemasan_karton ?? null],
                        ['name' => 'LABELISASI PLASTIK', 'value' => $data->first()->labelisasi_plastik ?? null],
                        ['name' => 'LABELISASI KARTON', 'value' => $data->first()->labelisasi_karton ?? null]
                    ];
                @endphp
                @foreach($kemasan_items as $index => $kemasan)
                    @php
                        $val = $kemasan['value'];
                        if (!is_array($val)) $val = $val ? [$val] : [];
                    @endphp
                    <tr>
                        <td style="text-align: left; padding-left: 20px;">{{ $index + 1 }}. {{ $kemasan['name'] }}</td>
                        <td>{{ implode(', ', array_intersect($val, [1, 2, '1', '2'])) }}</td>
                        <td>{{ implode(', ', array_intersect($val, [3, 4, '3', '4'])) }}</td>
                        <td>{{ implode(', ', array_intersect($val, [5, 6, '5', '6'])) }}</td>
                        <td>{{ implode(', ', array_intersect($val, [7, 8, '7', '8'])) }}</td>
                        <td style="vertical-align: middle; border: 1px solid #000; text-align: center; font-size: 7px;">-</td>
                        <td style="vertical-align: middle; border: 1px solid #000; text-align: center; font-size: 7px;">-</td>
                        <td style="vertical-align: middle; border: 1px solid #000; text-align: center; font-size: 7px;">{{ $firstItem->keterangan ?? '-' }}</td>
                    </tr>
                @endforeach

                <!-- MESIN DAN PERALATAN Section -->
                <tr>
                    <td colspan="8" style="background-color: #f0f0f0; font-weight: bold; text-align: left; padding: 8px;">MESIN DAN PERALATAN</td>
                </tr>
                @php 
                    $mesinData = [];
                    $isNewFormat = false;
                    
                    if (!empty($firstItem->mesin_peralatan) && is_array($firstItem->mesin_peralatan)) {
                        $mesinData = $firstItem->mesin_peralatan;
                        $isNewFormat = true;
                    } elseif (!empty($firstItem->penilaian) && is_array($firstItem->penilaian)) {
                        $mesinData = $firstItem->penilaian;
                        $isNewFormat = false;
                    }
                @endphp

                @if(count($mesinData) > 0)
                    @foreach($mesinData as $index => $mesin)
                        @php
                            $namaMesin = $isNewFormat ? ($mesin['nama_mesin'] ?? '-') : ($mesin['aspek'] ?? '-');
                            $val = $isNewFormat ? ($mesin['kondisi'] ?? []) : ($mesin['nilai'] ?? []);
                            if (!is_array($val)) $val = $val ? [$val] : [];
                        @endphp
                        <tr>
                            <td style="text-align: left; padding-left: 20px;">{{ $index + 1 }}. {{ strtoupper($namaMesin) }}</td>
                            <td>{{ implode(', ', array_intersect($val, [1, 2, '1', '2'])) }}</td>
                            <td>{{ implode(', ', array_intersect($val, [3, 4, '3', '4'])) }}</td>
                            <td>{{ implode(', ', array_intersect($val, [5, 6, '5', '6'])) }}</td>
                            <td>{{ implode(', ', array_intersect($val, [7, 8, '7', '8'])) }}</td>
                            <td style="text-align: center; font-size: 7px; vertical-align: middle;">{{ $mesin['tindakan_koreksi'] ?? '-' }}</td>
                            <td style="text-align: center; font-size: 7px; vertical-align: middle;">{{ str_replace(['✓', '✗'], ['Ok', 'Tidak Ok'], $mesin['verifikasi'] ?? '-') }}</td>
                            <td style="vertical-align: middle; border: 1px solid #000; text-align: center; font-size: 7px;">{{ $firstItem->keterangan ?? '-' }}</td>
                        </tr>
                    @endforeach
                @else
                    @php 
                        $mesin_items = [
                            ['name' => 'AUTOGRIND', 'value' => $firstItem->autogrind ?? null],
                            ['name' => 'BOWLCUTTER', 'value' => $firstItem->bowlcutter ?? null],
                            ['name' => 'AYAKAN SEASONING', 'value' => $firstItem->ayakan_seasoning ?? null],
                            ['name' => 'UNIMIX', 'value' => $firstItem->unimix ?? null],
                            ['name' => 'REVOFORMER', 'value' => $firstItem->revoformer ?? null],
                            ['name' => 'BETTER MIXER', 'value' => $firstItem->better_mixer ?? null],
                            ['name' => 'WET COATER', 'value' => $firstItem->wet_coater ?? null],
                            ['name' => 'BREADER', 'value' => $firstItem->breader ?? null],
                            ['name' => 'FRAYER 1', 'value' => $firstItem->frayer_1 ?? null],
                            ['name' => 'FRAYER 2', 'value' => $firstItem->frayer_2 ?? null],
                            ['name' => 'IQF JBT', 'value' => $firstItem->iqf_jbt ?? null],
                            ['name' => 'KERANJANG', 'value' => $firstItem->keranjang ?? null],
                            ['name' => 'TIMBANGAN', 'value' => $firstItem->timbangan ?? null],
                            ['name' => 'MHW', 'value' => $firstItem->mhw ?? null],
                            ['name' => 'FOOT SEALER', 'value' => $firstItem->foot_sealer ?? null],
                            ['name' => 'METAL DETECTOR', 'value' => $firstItem->metal_detector ?? null],
                            ['name' => 'ROTARY TABLE', 'value' => $firstItem->rotary_table ?? null],
                            ['name' => 'CARTON SEALER', 'value' => $firstItem->carton_sealer ?? null],
                            ['name' => 'MEATCAR', 'value' => $firstItem->meatcar ?? null],
                            ['name' => 'CHECK WEIGHER BAG', 'value' => $firstItem->check_weigher_bag ?? null],
                            ['name' => 'CHECK WEIGHER BOX', 'value' => $firstItem->check_weigher_box ?? null]
                        ];
                    @endphp
                    @foreach($mesin_items as $index => $mesin)
                        @php
                            $val = $mesin['value'];
                            if (!is_array($val)) $val = $val ? [$val] : [];
                        @endphp
                        <tr>
                            <td style="text-align: left; padding-left: 20px;">{{ $index + 1 }}. {{ $mesin['name'] }}</td>
                            <td>{{ implode(', ', array_intersect($val, [1, 2, '1', '2'])) }}</td>
                            <td>{{ implode(', ', array_intersect($val, [3, 4, '3', '4'])) }}</td>
                            <td>{{ implode(', ', array_intersect($val, [5, 6, '5', '6'])) }}</td>
                            <td>{{ implode(', ', array_intersect($val, [7, 8, '7', '8'])) }}</td>
                            <td style="text-align: center; font-size: 7px; vertical-align: middle;">{{ $firstItem->tindakan_koreksi ?? '-' }}</td>
                            <td style="text-align: center; font-size: 7px; vertical-align: middle;">{{ str_replace(['✓', '✗'], ['Ok', 'Tidak Ok'], $firstItem->verifikasi ?? '-') }}</td>
                            <td style="vertical-align: middle; border: 1px solid #000; text-align: center; font-size: 7px;">{{ $firstItem->keterangan ?? '-' }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        {{-- D. RUANGAN --}}
        @php $ruanganData = $firstItem->ruangan ?? []; @endphp
        @if(count($ruanganData) > 0)
        <table style="width:100%; border-collapse:collapse; margin-bottom:8px; font-size:7px;">
            <thead>
                <tr>
                    <td colspan="8" style="background-color:#f0f0f0; font-weight:bold; text-align:left; padding:6px; border:1px solid #000;">D. RUANGAN</td>
                </tr>
                <tr style="background-color:#ddd; font-weight:bold; text-align:center; font-size:6px;">
                    <th style="border:1px solid #000; padding:3px; width:18%;">NAMA RUANGAN</th>
                    <th style="border:1px solid #000; padding:3px; width:8%;">1-2</th>
                    <th style="border:1px solid #000; padding:3px; width:8%;">3-4</th>
                    <th style="border:1px solid #000; padding:3px; width:8%;">5-6</th>
                    <th style="border:1px solid #000; padding:3px; width:8%;">7-8</th>
                    <th style="border:1px solid #000; padding:3px; width:25%;">TINDAKAN KOREKSI</th>
                    <th style="border:1px solid #000; padding:3px; width:10%;">VERIFIKASI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ruanganData as $rIdx => $ruangan)
                    @php
                        $rVal = $ruangan['kondisi'] ?? [];
                        if (!is_array($rVal)) $rVal = $rVal ? [$rVal] : [];
                    @endphp
                    <tr>
                        <td style="text-align:left; padding-left:10px; border:1px solid #000; font-size:7px;">{{ $rIdx + 1 }}. {{ strtoupper($ruangan['nama_ruangan'] ?? '-') }}</td>
                        <td style="text-align:center; border:1px solid #000; font-size:7px;">{{ implode(', ', array_intersect($rVal, [1, 2, '1', '2'])) }}</td>
                        <td style="text-align:center; border:1px solid #000; font-size:7px;">{{ implode(', ', array_intersect($rVal, [3, 4, '3', '4'])) }}</td>
                        <td style="text-align:center; border:1px solid #000; font-size:7px;">{{ implode(', ', array_intersect($rVal, [5, 6, '5', '6'])) }}</td>
                        <td style="text-align:center; border:1px solid #000; font-size:7px;">{{ implode(', ', array_intersect($rVal, [7, 8, '7', '8'])) }}</td>
                        <td style="text-align:center; border:1px solid #000; font-size:7px;">{{ $ruangan['tindakan_koreksi'] ?? '-' }}</td>
                        <td style="text-align:center; border:1px solid #000; font-size:7px;">{{ str_replace(['✓', '✗'], ['Ok', 'Tidak Ok'], $ruangan['verifikasi'] ?? '-') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif

 <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
            <span>{{ $filterInfo['kode_form'] }}</span>
        </div>
        <div class="keterangan-section" style="margin-top: 8px; margin-bottom: 8px; font-size: 5px; line-height: 1.2;">
            <div style="margin-bottom: 5px;">
                <strong>Keterangan Pengecekan :</strong><br>
                - Pengecekan Kondisi Bahan baku, bahan penunjang, dan kemasan (1 dan 2) : nomer 1-6<br>
                - Pengecekan Kemasan (3 dan 4) : nomer 1-2<br>
                - Pengecekan Kondisi Mesin dan Peralatan : nomer 3-8
            </div>
            
            <div>
                <strong>Kriterian Penilaian :</strong><br>
                <div style="display: table; width: 100%; margin-top: 2px;">
                    <div style="display: table-row;">
                        <div style="display: table-cell; width: 50%; padding-right: 10px;">
                            1. Sesuai spesifikasi<br>
                            2. Tidak sesuai spesifikasi<br>
                            3. Bebas dari kontaminan dan bahan sebelumnya<br>
                            4. Ada kontaminan atau sisa bahan sebelumnya
                        </div>
                        <div style="display: table-cell; width: 50%; padding-left: 10px;">
                            5. Bebas dari potensi kontaminasi allergen<br>
                            6. Ada potensi kontaminasi allergen<br>
                            7. Bersih, tidak ada kontaminan atau kotoran, tidak tercium bau menyimpang<br>
                            8. Tidak bersih, ada kontaminan atau kotoran, tercium bau menyimpang
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== TANDA TANGAN ===== --}}
        @php
            $qcApprover = null; $qcApproverName = 'QC Inspector';
            foreach($data as $item) {
                if($item->approved_by_qc && $item->qc_approved_by) {
                    $qcApprover = \App\Models\User::find($item->qc_approved_by);
                    if($qcApprover) { $qcApproverName = $qcApprover->name; break; }
                }
            }
            $spvApprover = null; $spvApproverName = 'Supervisor QC';
            foreach($data as $item) {
                if($item->approved_by_spv && $item->spv_approved_by) {
                    $spvApprover = \App\Models\User::find($item->spv_approved_by);
                    if($spvApprover) { $spvApproverName = $spvApprover->name; break; }
                }
            }
            $produksiApprover = null; $produksiApproverName = 'SPV/Foreman/Forelady Produksi';
            foreach($data as $item) {
                if($item->approved_by_produksi && $item->produksi_approved_by) {
                    $produksiApprover = \App\Models\User::find($item->produksi_approved_by);
                    if($produksiApprover) { $produksiApproverName = $produksiApprover->name; break; }
                }
            }
            $base64QcSvg = null;
            if($qcApprover) {
                $qcApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_qc && $item->qc_approved_at) { $qcApprovalDate = $item->qc_approved_at->format('Y-m-d'); break; } }
                $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}"));
            }
            $base64ProduksiSvg = null;
            if($produksiApprover) {
                $produksiApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_produksi && $item->produksi_approved_at) { $produksiApprovalDate = $item->produksi_approved_at->format('Y-m-d'); break; } }
                $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$produksiApprover->name} (FM/FL PRODUKSI) pada {$produksiApprovalDate}"));
            }
            $base64SpvSvg = null;
            if($spvApprover) {
                $spvApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_spv && $item->spv_approved_at) { $spvApprovalDate = $item->spv_approved_at->format('Y-m-d'); break; } }
                $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}"));
            }
        @endphp
        <table class="signature-table">
            <tr>
                <td class="signature-label">Dibuat Oleh:</td>
                <td class="signature-label">Diketahui Oleh:</td>
                <td class="signature-label">Disetujui Oleh:</td>
            </tr>
            <tr>
                <td class="signature-qr-area">@if($base64QcSvg)<img src="{{ $base64QcSvg }}" class="qr-code-img" alt="QR QC">@endif</td>
                <td class="signature-qr-area">@if($base64ProduksiSvg)<img src="{{ $base64ProduksiSvg }}" class="qr-code-img" alt="QR Produksi">@endif</td>
                <td class="signature-qr-area">@if($base64SpvSvg)<img src="{{ $base64SpvSvg }}" class="qr-code-img" alt="QR SPV">@endif</td>
            </tr>
            <tr>
                <td><div class="signature-line"></div></td>
                <td><div class="signature-line"></div></td>
                <td><div class="signature-line"></div></td>
            </tr>
            <tr>
                <td class="signature-name">{{ $qcApproverName }}</td>
                <td class="signature-name">{{ $produksiApproverName }}</td>
                <td class="signature-name">{{ $spvApproverName }}</td>
            </tr>
        </table>

    @else
        <div style="text-align: center; padding: 40px; border: 2px dashed #ccc; margin: 20px 0; background-color: #f9f9f9;">
            <h3 style="color: #666; margin-bottom: 10px;">
                <i style="font-size: 24px;">⚠️</i>
            </h3>
            <h4 style="color: #333; margin-bottom: 15px;">Tidak Ada Data Ditemukan</h4>
            <p style="color: #666; margin-bottom: 10px; font-size: 12px;">
                Tidak ada data Produk Forming yang sesuai dengan filter yang dipilih:
            </p>
            <div style="background-color: #fff; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #ddd;">
                <p style="margin: 5px 0; font-size: 11px;"><strong>Tanggal:</strong> {{ $filterInfo['tanggal'] }}</p>
                <p style="margin: 5px 0; font-size: 11px;"><strong>Shift:</strong> {{ $filterInfo['shift'] }}</p>
                <p style="margin: 5px 0; font-size: 11px;"><strong>Produk:</strong> {{ $filterInfo['produk'] }}</p>
            </div>
            <p style="color: #888; font-size: 10px; margin-top: 15px;">
                Silakan periksa kembali filter yang digunakan atau pastikan data sudah tersedia dalam sistem.
            </p>
        </div>
    @endif
</body>
</html>
