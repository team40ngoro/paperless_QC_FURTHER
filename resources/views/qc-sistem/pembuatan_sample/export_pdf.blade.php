<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Pembuatan Sample</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .company-logo {
            float: left;
            width: 60px;
            height: 60px;
            text-align: center;
            line-height: 60px;
            font-weight: bold;
            font-size: 14px;
        }
        .company-info {
            margin-left: 80px;
            text-align: left;
        }
        .company-name {
            font-weight: bold;
            font-size: 12px;
        }
        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0;
        }
        .filter-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .filter-info h4 {
            margin: 0 0 10px 0;
            font-size: 11px;
            font-weight: bold;
        }
        .filter-info p {
            margin: 2px 0;
            font-size: 9px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            font-size: 8px;
            vertical-align: middle;
        }
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .bg-primary {
            background-color: #007bff !important;
            color: white !important;
        }
        .bg-success {
            background-color: #28a745 !important;
            color: white !important;
        }
        .bg-secondary {
            background-color: #6c757d !important;
            color: white !important;
        }
        .bg-info {
            background-color: #17a2b8 !important;
            color: white !important;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 5px 10px;
            border: none;
        }
        .signature-label {
            text-align: center;
            font-size: 10px;
            margin-bottom: 5px;
        }
        .signature-qr-area {
            height: 65px;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        .signature-name {
            font-size: 10px;
            text-align: center;
        }
        .qr-code-img {
            max-height: 55px;
            max-width: 55px;
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

    <div class="title">PEMBUATAN SAMPLE</div>

    <div class="filter-info">
        <h4>Informasi Filter</h4>
        <p><strong>Tanggal:</strong> {{ $filterInfo['tanggal'] }} | <strong>Shift:</strong> {{ $filterInfo['shift'] }} | <strong>Produk:</strong> {{ $filterInfo['produk'] }} | </p>
    </div>

    @if(count($data) > 0)
        @php $globalIndex = 1; @endphp
        @foreach($groupedData as $jenisSample => $items)
            <!-- Jenis Sample Header -->
            <div style="margin-top: 20px; margin-bottom: 10px; padding: 8px; background-color: #e9ecef; border-left: 4px solid #007bff; font-weight: bold; font-size: 11px;">
                JENIS SAMPLE: {{ strtoupper($jenisSample) }}
            </div>
            
            <table class="main-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Kode Produksi</th>
                        <th>Tanggal Expired</th>
                        <th>Jumlah</th>
                        <th>Berat Sampel</th>
                        <th>Jenis Sample</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        @if($item->items_json && is_array($item->items_json) && count($item->items_json) > 0)
                            @foreach($item->items_json as $batch)
                                @php
                                    $produkBatch = \App\Models\JenisProduk::find($batch['id_produk'] ?? null);
                                    $namaProdukBatch = $produkBatch->nama_produk ?? ($item->produk->nama_produk ?? '-');
                                    $beratFormatted = isset($batch['berat']) && $batch['berat'] !== '' ? rtrim(rtrim(number_format((float)$batch['berat'], 2, '.', ''), '0'), '.') : '-';
                                    $expFormatted = isset($batch['tanggal_expired']) && $batch['tanggal_expired'] ? \Carbon\Carbon::parse($batch['tanggal_expired'])->format('d/m/Y') : '-';
                                @endphp
                                <tr>
                                    <td>{{ $globalIndex++ }}</td>
                                    <td>{{ $namaProdukBatch }}</td>
                                    <td>{{ $batch['kode_produksi'] ?? '-' }}</td>
                                    <td>{{ $expFormatted }}</td>
                                    <td>{{ $batch['jumlah'] ?? '-' }}</td>
                                    <td>{{ $beratFormatted != '-' ? $beratFormatted . ' gram' : '-' }}</td>
                                    <td>{{ strtoupper($batch['jenis_sample'] ?? $jenisSample) }}</td>
                                </tr>
                            @endforeach
                        @else
                            @php
                                $beratFormatted = $item->berat ? rtrim(rtrim(number_format((float)$item->berat, 2, '.', ''), '0'), '.') : '-';
                            @endphp
                            <tr>
                                <td>{{ $globalIndex++ }}</td>
                                <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                <td>{{ $item->kode_produksi }}</td>
                                <td>{{ $item->tanggal_expired ? \Carbon\Carbon::parse($item->tanggal_expired)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $item->jumlah }}</td>
                                <td>{{ $beratFormatted != '-' ? $beratFormatted . ' gram' : '-' }}</td>
                                <td>{{ strtoupper($item->jenis_sample ?? $jenisSample) }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @endforeach
        <div style="width: 100%; text-align: right; margin-top: 10px;">
            <span>{{ $filterInfo['kode_form'] ?? '-' }}</span>
        </div>

        {{-- ===== TANDA TANGAN ===== --}}
        @php
            $qcApprover = null; $qcApproverName = 'QC';
            foreach($data as $item) {
                if($item->approved_by_qc && $item->qc_approved_by) {
                    $qcApprover = \App\Models\User::find($item->qc_approved_by);
                    if($qcApprover) { $qcApproverName = $qcApprover->name; break; }
                }
            }
            $spvApprover = null; $spvApproverName = 'SPV';
            foreach($data as $item) {
                if($item->approved_by_spv && $item->spv_approved_by) {
                    $spvApprover = \App\Models\User::find($item->spv_approved_by);
                    if($spvApprover) { $spvApproverName = $spvApprover->name; break; }
                }
            }
            $produksiApprover = null; $produksiApproverName = 'FM/FL PRODUKSI';
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
                $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}"));
            }
            $base64SpvSvg = null;
            if($spvApprover) {
                $spvApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_spv && $item->spv_approved_at) { $spvApprovalDate = $item->spv_approved_at->format('Y-m-d'); break; } }
                $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}"));
            }
            $base64ProduksiSvg = null;
            if($produksiApprover) {
                $produksiApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_produksi && $item->produksi_approved_at) { $produksiApprovalDate = $item->produksi_approved_at->format('Y-m-d'); break; } }
                $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$produksiApprover->name} (FM/FL PRODUKSI) pada {$produksiApprovalDate}"));
            }
        @endphp
<table class="signature-table">
    <tr>
        <td class="signature-label">Dibuat Oleh: QC INSPEKTOR</td>
        <td class="signature-label">Diketahui Oleh: Produksi</td>
        <td class="signature-label">Disetujui Oleh: Supervisor QC</td>
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
                Tidak ada data Pembuatan Sample yang sesuai dengan filter yang dipilih:
            </p>
            <div style="background-color: #fff; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #ddd;">
                <p style="margin: 5px 0; font-size: 11px;"><strong>Tanggal:</strong> {{ $filterInfo['tanggal'] }}</p>
                <p style="margin: 5px 0; font-size: 11px;"><strong>Shift:</strong> {{ $filterInfo['shift'] }}</p>
                <p style="margin: 5px 0; font-size: 11px;"><strong>Produk:</strong> {{ $filterInfo['produk'] }}</p>
                <p style="margin: 5px 0; font-size: 11px;"><strong>Kode Form:</strong> {{ $filterInfo['kode_form'] }}</p>
            </div>
            <p style="color: #888; font-size: 10px; margin-top: 15px;">
                Silakan periksa kembali filter yang digunakan atau pastikan data sudah tersedia dalam sistem.
            </p>
        </div>
    @endif
</body>
</html>
