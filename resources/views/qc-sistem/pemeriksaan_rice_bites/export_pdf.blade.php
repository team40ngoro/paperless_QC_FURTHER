<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pemeriksaan Rice Bites</title>
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
            font-size: 5px;
            vertical-align: middle;
            page-break-inside: avoid;
        }
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-left {
            text-align: left;
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

    <div class="title">PEMERIKSAAN PRODUK RICE BITES DI COOKING MIXER </div>

    <div class="filter-info">
        <h4>Informasi Filter</h4>
        <p><strong>Tanggal:</strong> {{ $filterInfo['tanggal'] }} | <strong>Shift:</strong> {{ $filterInfo['shift'] }} | <strong>Produk:</strong> {{ $filterInfo['produk'] }} | </p>
    </div>

    @if(count($data) > 0)
        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Jam</th>
                    <th rowspan="2">Batch</th>
                    <th rowspan="2">No Cooking Cycle</th>
                    <th colspan="3">Bahan Baku</th>
                    <th colspan="2">Premix</th>
                    <th rowspan="2">Parameter Nitrogen</th>
                    <th rowspan="2">Jumlah Inject Nitrogen</th>
                    <th rowspan="2">RPM Cooking Cattle</th>
                    <th rowspan="2">Cold Mixing</th>
                    <th rowspan="2">Suhu Aktual Adonan</th>
                    <th rowspan="2">Suhu Adonan Pencampuran</th>
                    <th rowspan="2">Rata-rata Suhu (°C)</th>
                    <th rowspan="2">Hasil Pencampuran</th>
                    <th rowspan="2">Catatan</th>
                </tr>
                <tr>
                    <th>Nama</th>
                    <th>Kode Produksi</th>
                    <th>Detail</th>
                    <th>Nama</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    @php
                        $bahanBakuCount = count($item->bahan_baku ?? []);
                        $premixCount = count($item->premix ?? []);
                        $maxRows = max($bahanBakuCount, $premixCount, 1);
                    @endphp
                    
                    @for($i = 0; $i < $maxRows; $i++)
                        <tr>
                            @if($i == 0)
                                <td rowspan="{{ $maxRows }}">{{ $index + 1 }}</td>
                                <td rowspan="{{ $maxRows }}">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                                <td rowspan="{{ $maxRows }}">{{ $item->batch }}</td>
                                <td rowspan="{{ $maxRows }}">{{ $item->no_cooking_cycle }}</td>
                            @endif
                            
                            <!-- Bahan Baku -->
                            @if(isset($item->bahan_baku[$i]))
                                <td class="text-left">{{ $item->bahan_baku[$i]['nama'] ?? '-' }}</td>
                                <td class="text-left">{{ $item->bahan_baku[$i]['kode_produksi'] ?? '-' }}</td>
                                <td class="text-left">
                                    @if(isset($item->bahan_baku[$i]))
                                        Berat: {{ $item->bahan_baku[$i]['berat'] ?? '-' }}<br>
                                        Suhu: {{ $item->bahan_baku[$i]['suhu'] ?? '-' }}<br>
                                        Kondisi: {{ $item->bahan_baku[$i]['kondisi'] ?? '-' }}
                                    @endif
                                </td>
                            @else
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            @endif
                            
                            <!-- Premix -->
                            @if(isset($item->premix[$i]))
                                <td class="text-left">{{ $item->premix[$i]['nama'] ?? '-' }}</td>
                                <td class="text-left">
                                    @if(isset($item->premix[$i]))
                                        Berat: {{ $item->premix[$i]['berat'] ?? '-' }}<br>
                                        Kondisi: {{ $item->premix[$i]['kondisi'] ?? '-' }}
                                    @endif
                                </td>
                            @else
                                <td>-</td>
                                <td>-</td>
                            @endif
                            
                            @if($i == 0)
                                <td rowspan="{{ $maxRows }}">{{ $item->parameter_nitrogen ?? '-' }}</td>
                                <td rowspan="{{ $maxRows }}">{{ $item->jumlah_inject_nitrogen ?? '-' }}</td>
                                <td rowspan="{{ $maxRows }}">{{ $item->rpm_cooking_cattle ?? '-' }}</td>
                                <td rowspan="{{ $maxRows }}">{{ $item->cold_mixing ?? '-' }}</td>
                                <td rowspan="{{ $maxRows }}">
                                    @if($item->suhu_aktual_adonan && is_array($item->suhu_aktual_adonan))
                                        {{ implode(', ', array_filter($item->suhu_aktual_adonan)) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td rowspan="{{ $maxRows }}">
                                    @if($item->suhu_adonan_pencampuran && is_array($item->suhu_adonan_pencampuran))
                                        {{ implode(', ', array_filter($item->suhu_adonan_pencampuran)) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td rowspan="{{ $maxRows }}">{{ number_format($item->rata_rata_suhu, 2) }}</td>
                                <td rowspan="{{ $maxRows }}">
                                    @if($item->hasil_pencampuran === 'OK')
                                        OK
                                    @elseif($item->hasil_pencampuran === 'Tidak OK')
                                        Tidak OK
                                    @else
                                        -
                                    @endif
                                </td>
                                <td rowspan="{{ $maxRows }}" class="text-left">{{ $item->catatan ?? '-' }}</td>
                            @endif
                        </tr>
                    @endfor
                @endforeach
            </tbody>
        </table>
 <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
            <span>{{ $filterInfo['kode_form'] }}</span>
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
                Tidak ada data Pemeriksaan Rice Bites yang sesuai dengan filter yang dipilih:
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
