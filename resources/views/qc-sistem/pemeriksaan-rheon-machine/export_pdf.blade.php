<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Pemeriksaan Rheon Machine</title>
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
            width: 60px;
            height: 60px;
            text-align: center;
            line-height: 60px;
            font-weight: bold;
            font-size: 10px;
        }
        .company-info {
            margin-left: 70px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            font-size: 6px;
        }
        th {
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
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
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

    <div class="title">PEMERIKSAAN RHEON MACHINE</div>

    @if($pemeriksaan->count() > 0)
        @php
            $firstItem = $pemeriksaan->first();
        @endphp

        <div class="filter-info">
            <h4>Filter yang Diterapkan:</h4>
            <p>
                <strong>Tanggal:</strong> {{ $filters['tanggal'] ?? 'Semua Tanggal' }} | 
                <strong>Shift:</strong> {{ $filters['shift'] ?? 'Semua Shift' }} | 
                <strong>Produk:</strong> {{ $filters['produk'] ?? 'Semua Produk' }}
            </p>
        </div>
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>     
                    <th width="15%">Produk</th>
                    <th width="8%">Batch</th>
                    <th width="8%">Waktu</th>
                    <th width="11%">Inner</th>
                    <th width="11%">Outer</th>
                    <th width="11%">Belt</th>
                    <th width="11%">Encrushed Speed</th>
                    <th width="12%">Jenis Cetakan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pemeriksaan as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $item->produk->nama_produk ?? '-' }}</td>
                    <td>{{ $item->batch }}</td>
                    <td>{{ $item->pukul }}</td>
                    <td>{{ $item->inner }}</td>
                    <td>{{ $item->outer }}</td>
                    <td>{{ $item->belt }}</td>
                    <td>{{ $item->extrusion_speed }}</td>
                    <td>{{ $item->jenis_cetakan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Weight Details Table -->
        <table style="margin-top: 15px;">
            <thead>
                <tr>
                    <th colspan="12" style="background-color: #e0e0e0; font-size: 8px;">DETAIL BERAT PEMERIKSAAN</th>
                </tr>
                <tr>
                    <th width="5%">No</th>
                    <th width="12%">Produk</th>
                    <th width="8%">Batch</th>
                    <th width="10%">Rata-rata Dough (gram)</th>
                    <th width="10%">Rata-rata Filler (gram)</th>
                    <th width="12%">Rata-rata After Forming (gram)</th>
                    <th width="12%">Rata-rata After Frying (gram)</th>
                    <th width="31%">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pemeriksaan as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $item->produk->nama_produk ?? '-' }}</td>
                    <td>{{ $item->batch }}</td>
                    <td>{{ number_format($item->rata_rata_dough, 2) }}</td>
                    <td>{{ number_format($item->rata_rata_filler, 2) }}</td>
                    <td>{{ number_format($item->rata_rata_after_forming, 2) }}</td>
                    <td>{{ number_format($item->rata_rata_after_frying, 2) }}</td>
                    <td class="text-left">{{ $item->catatan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
         <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
            <span>{{ $filters['kode_form'] ?? '-' }}</span>
        </div>
    @else
        <div class="filter-info">
            <h4>Filter yang Diterapkan:</h4>
            <p>
                <strong>Tanggal:</strong> {{ $filters['tanggal'] ?? 'Semua Tanggal' }} | 
                <strong>Shift:</strong> {{ $filters['shift'] ?? 'Semua Shift' }} | 
                <strong>Produk:</strong> {{ $filters['produk'] ?? 'Semua Produk' }}
            </p>
        </div>
        
        <div style="text-align: center; padding: 20px;">
            <p>Tidak ada data yang sesuai dengan filter yang dipilih.</p>
        </div>
    @endif

    {{-- ===== TANDA TANGAN ===== --}}
    @php
        $qcApprover = null; $qcApproverName = 'QC Inspector';
        foreach($pemeriksaan as $item) {
            if($item->approved_by_qc && $item->qc_approved_by) {
                $qcApprover = \App\Models\User::find($item->qc_approved_by);
                if($qcApprover) { $qcApproverName = $qcApprover->name; break; }
            }
        }
        $spvApprover = null; $spvApproverName = 'Supervisor QC';
        foreach($pemeriksaan as $item) {
            if($item->approved_by_spv && $item->spv_approved_by) {
                $spvApprover = \App\Models\User::find($item->spv_approved_by);
                if($spvApprover) { $spvApproverName = $spvApprover->name; break; }
            }
        }
        $produksiApprover = null; $produksiApproverName = 'SPV/Foreman/Forelady Produksi';
        foreach($pemeriksaan as $item) {
            if($item->approved_by_produksi && $item->produksi_approved_by) {
                $produksiApprover = \App\Models\User::find($item->produksi_approved_by);
                if($produksiApprover) { $produksiApproverName = $produksiApprover->name; break; }
            }
        }
        $base64QcSvg = null;
        if($qcApprover) {
            $qcApprovalDate = '';
            foreach($pemeriksaan as $item) { if($item->approved_by_qc && $item->qc_approved_at) { $qcApprovalDate = $item->qc_approved_at->format('Y-m-d'); break; } }
            $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}"));
        }
        $base64ProduksiSvg = null;
        if($produksiApprover) {
            $produksiApprovalDate = '';
            foreach($pemeriksaan as $item) { if($item->approved_by_produksi && $item->produksi_approved_at) { $produksiApprovalDate = $item->produksi_approved_at->format('Y-m-d'); break; } }
            $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$produksiApprover->name} (FM/FL PRODUKSI) pada {$produksiApprovalDate}"));
        }
        $base64SpvSvg = null;
        if($spvApprover) {
            $spvApprovalDate = '';
            foreach($pemeriksaan as $item) { if($item->approved_by_spv && $item->spv_approved_at) { $spvApprovalDate = $item->spv_approved_at->format('Y-m-d'); break; } }
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
</body>
</html>
