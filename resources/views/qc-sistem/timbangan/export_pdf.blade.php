<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Timbangan - {{ $filters['kode_form'] }}</title>
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
            padding: 2px;
            text-align: center;
            font-size: 6px;
            vertical-align: middle;
            page-break-inside: avoid;
        }
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
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

    <div class="title">PEMERIKSAAN TIMBANGAN</div>

    <div class="filter-info">
        <h4>Informasi Filter</h4>
        <p><strong>Tanggal:</strong> {{ $filters['tanggal'] }} | <strong>Shift:</strong> {{ $filters['shift'] }} |</p>
    </div>
    
    @if($data->count() > 0)
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 5%;">Jam</th>
                    <th style="width: 30%;">Jenis</th>
                    <th style="width: 30%;">Kode Timbangan</th>
                    <th style="width: 15%;">Area</th>
                    <th style="width: 15%;">Hasil Pengecekan</th>
                    <th style="width: 15%;">Hasil Verifikasi 500 Gr</th>
                    <th style="width: 15%;">Hasil Verifikasi 1000 Gr</th>
                    <th style="width: 15%;">Hasil Verifikasi 10000 Gr</th>
                </tr>
            </thead>
            <tbody>
                @php $globalIndex = 1; @endphp
                @foreach($data as $item)
                    @if($item->entries && is_array($item->entries))
                        @foreach($item->entries as $key => $entry)
                            @if(!is_numeric($key)) @continue @endif
                            <tr>
                                <td>{{ $globalIndex++ }}</td>
                                <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                                <td>{{ $entry['jenis'] ?? '-' }}</td>
                                <td>{{ $entry['kode_timbangan'] ?? '-' }}</td>
                                <td>{{ $entry['area'] ?? '-' }}</td>
                                <td>
                                    @php
                                        $hasilText = ($entry['hasil_pengecekan'] ?? 'ok') === 'ok' ? 'OK' : 'TIDAK OK';
                                    @endphp
                                    {{ $hasilText }}
                                </td>
                                <td>{{ $entry['hasil_verifikasi_500'] ?? '-' }}</td>
                                <td>{{ $entry['hasil_verifikasi_1000'] ?? '-' }}</td>
                                <td>{{ $entry['hasil_verifikasi_10000'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>{{ $globalIndex++ }}</td>
                            <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                            <td>{{ $item->jenis }}</td>
                            <td>{{ $item->kode_timbangan }}</td>
                            <td>{{ $item->area }}</td>
                            <td>
                                @php
                                    $hasilText = $item->hasil_pengecekan === 'ok' ? 'OK' : 'TIDAK OK';
                                @endphp
                                {{ $hasilText }}
                            </td>
                            <td>{{ $item->hasil_verifikasi_500 ?? '-' }}</td>
                            <td>{{ $item->hasil_verifikasi_1000 ?? '-' }}</td>
                            <td>{{ $item->hasil_verifikasi_10000 ?? '-' }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        {{-- Check Weigher Bag Section in PDF --}}
        @php
            $allBags = [];
            $allBoxes = [];
            foreach($data as $item) {
                if(!empty($item->entries['checkweigher_bag']) && is_array($item->entries['checkweigher_bag'])) {
                    foreach($item->entries['checkweigher_bag'] as $b) {
                        $allBags[] = array_merge($b, ['jam' => $item->jam]);
                    }
                }
                if(!empty($item->entries['checkweigher_box']) && is_array($item->entries['checkweigher_box'])) {
                    foreach($item->entries['checkweigher_box'] as $bx) {
                        $allBoxes[] = array_merge($bx, ['jam' => $item->jam]);
                    }
                }
            }
        @endphp

        @if(count($allBags) > 0)
            <div style="font-weight: bold; font-size: 8px; margin-top: 10px; margin-bottom: 3px;">VERIFIKASI PENGGUNAAN CHECK WEIGHER BAG</div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 10%;">Line</th>
                        <th style="width: 20%;">Nama Produk</th>
                        <th style="width: 10%;">Gramase</th>
                        <th style="width: 10%;">Waktu Check</th>
                        <th style="width: 15%;">Sample 1 (g)</th>
                        <th style="width: 15%;">Sample 2 (g)</th>
                        <th style="width: 7.5%;">Under</th>
                        <th style="width: 7.5%;">Over</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allBags as $bIdx => $bag)
                        <tr>
                            <td>{{ $bIdx + 1 }}</td>
                            <td>{{ $bag['line'] ?? '-' }}</td>
                            <td>{{ $bag['nama_produk'] ?? '-' }}</td>
                            <td>{{ $bag['gramase'] ?? '-' }}</td>
                            <td>{{ $bag['waktu_pemeriksaan'] ?? '-' }}</td>
                            <td>{{ $bag['perbandingan_1'] ?? '-' }}</td>
                            <td>{{ $bag['perbandingan_2'] ?? '-' }}</td>
                            <td>{{ $bag['under'] ?? '-' }}</td>
                            <td>{{ $bag['over'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if(count($allBoxes) > 0)
            <div style="font-weight: bold; font-size: 8px; margin-top: 10px; margin-bottom: 3px;">VERIFIKASI PENGGUNAAN CHECK WEIGHER BOX</div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 10%;">Line</th>
                        <th style="width: 25%;">Nama Produk</th>
                        <th style="width: 12%;">Gramase</th>
                        <th style="width: 12%;">Waktu Check</th>
                        <th style="width: 12%;">Kurang 1 Pack</th>
                        <th style="width: 12%;">Lebih 1 Pack</th>
                        <th style="width: 12%;">Double Item</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allBoxes as $bxIdx => $box)
                        <tr>
                            <td>{{ $bxIdx + 1 }}</td>
                            <td>{{ $box['line'] ?? '-' }}</td>
                            <td>{{ $box['nama_produk'] ?? '-' }}</td>
                            <td>{{ $box['gramase'] ?? '-' }}</td>
                            <td>{{ $box['waktu_pemeriksaan'] ?? '-' }}</td>
                            <td>{{ $box['kurang_1_pack'] ?? '-' }}</td>
                            <td>{{ $box['lebih_1_pack'] ?? '-' }}</td>
                            <td>{{ $box['double_item'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
         <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
            <span>{{ $filters['kode_form'] ?? $firstItem->kode_form ?? '-' }}</span>
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
                Tidak ada data timbangan yang sesuai dengan filter yang dipilih:
            </p>
            <div style="background-color: #fff; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #ddd;">
                <p style="margin: 5px 0; font-size: 11px;"><strong>Tanggal:</strong> {{ $filters['tanggal'] }}</p>
                <p style="margin: 5px 0; font-size: 11px;"><strong>Shift:</strong> {{ $filters['shift'] }}</p>
                <p style="margin: 5px 0; font-size: 11px;"><strong>Kode Form:</strong> {{ $filters['kode_form'] }}</p>
            </div>
            <p style="color: #888; font-size: 10px; margin-top: 15px;">
                Silakan periksa kembali filter yang digunakan atau pastikan data sudah tersedia dalam sistem.
            </p>
        </div>
    @endif
</body>
</html>
