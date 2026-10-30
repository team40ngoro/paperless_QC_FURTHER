<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pemeriksaan Benda Asing</title>
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
        .form-info {
            margin-bottom: 15px;
        }
        .form-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .form-info td {
            padding: 3px 5px;
            border: 1px solid #000;
            font-size: 9px;
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
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
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
        .detail-section {
            margin-top: 20px;
        }
        .detail-section h4 {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .detail-item {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 9px;
        }
        .detail-item strong {
            font-size: 9px;
        }
        .detail-item p {
            margin: 3px 0;
            font-size: 9px;
        }
        .barcode-container {
            margin-top: 10px;
            text-align: center;
        }
        .barcode {
            font-family: 'Courier New', monospace;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 1px;
            background-color: #f0f0f0;
            padding: 2px 5px;
            border: 1px solid #333;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-logo">
            <img src="{{ public_path('dist/img/cpi-logo.png') }}" alt="Logo" style="width: 60px; height: 60px; object-fit: contain;">
        </div>
        <div class="company-info">
            <div class="company-name">PT. CHAROEN POKPHAND INDONESIA</div>
            <div>FOOD DIVISION {{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }}</div>
            <div>{{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }} - INDONESIA</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="title">PEMERIKSAAN KONTAMINASI BENDA ASING</div>

    <div class="filter-info">
        <h4>Informasi Filter</h4>
        <p><strong>Tanggal:</strong> {{ $filters['tanggal'] ?? 'Semua Tanggal' }} | <strong>Shift:</strong> {{ $filters['shift_id'] ? (\App\Models\DataShift::find($filters['shift_id'])->shift ?? 'Unknown') : 'Semua Shift' }} | <strong>Produk:</strong> {{ $filters['id_produk'] ? (\App\Models\JenisProduk::find($filters['id_produk'])->nama_produk ?? 'Unknown') : 'Semua Produk' }} | </p>
    </div>


    @if(count($data) > 0)
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 6%;">Jam</th>
                    <th style="width: 12%;">Produk</th>
                    <th style="width: 8%;">RM</th>
                    <th style="width: 6%;">Berat</th>
                    <th style="width: 10%;">Kode Produksi</th>
                    <th style="width: 10%;">Jenis Kontaminasi</th>
                    <th style="width: 7%;">Ukuran</th>
                    <th style="width: 8%;">Ditemukan</th>
                    <th style="width: 7%;">Jml Terdampak</th>
                    <th style="width: 7%;">Diketahui Oleh</th>
                    <th style="width: 10%;">Analisa Masalah</th>
                    <th style="width: 8%;">Koreksi</th>
                    <th style="width: 10%;">Tindakan Korektif</th>
                    <th style="width: 6%;">Bukti</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->jam)->format('H:i') }}</td>
                    <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                    <td>{{ $item->rm ?? '-' }}</td>
                    <td>{{ $item->berat !== null && $item->berat !== '' ? $item->berat . ' gram' : '-' }}</td>
                    <td>{{ $item->kode_produksi }}</td>
                    <td>{{ $item->jenis_kontaminasi }}</td>
                    <td>{{ $item->ukuran_kontaminasi }}</td>
                    <td>{{ $item->ditemukan }}</td>
                    <td>{{ $item->jumlah_terdampak ?? '-' }}</td>
                    <td>{{ $item->diketahui }}</td>
                    <td>{{ $item->analisa_masalah }}</td>
                    <td>{{ $item->koreksi }}</td>
                    <td>{{ $item->tindak_korektif }}</td>
                    <td>
                        @if($item->bukti)
                            <img src="{{ public_path('storage/' . $item->bukti) }}" alt="Bukti" style="width: 30px; height: 30px; object-fit: cover; border: 1px solid #000;">
                        @else
                            -
                        @endif
                    </td>                
                </tr>
                @endforeach
            </tbody>
        </table>

    @else
        <div style="text-align: center; padding: 40px; border: 2px dashed #ccc; margin: 20px 0; background-color: #f9f9f9;">
            <h3 style="color: #666; margin-bottom: 10px;">
                <i style="font-size: 24px;">⚠️</i>
            </h3>
            <h4 style="color: #333; margin-bottom: 15px;">Tidak Ada Data Ditemukan</h4>
            <p style="color: #666; margin-bottom: 10px; font-size: 12px;">
                Tidak ada data Pemeriksaan Benda Asing yang sesuai dengan filter yang dipilih:
            </p>
            <div style="background-color: #fff; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #ddd;">
                <p style="margin: 5px 0; font-size: 11px;"><strong>Tanggal:</strong> {{ $filters['tanggal'] ? \Carbon\Carbon::parse($filters['tanggal'])->format('d-m-Y') : 'Semua Tanggal' }}</p>
                <p style="margin: 5px 0; font-size: 11px;"><strong>Shift:</strong> {{ $filters['shift_id'] ? (\App\Models\DataShift::find($filters['shift_id'])->shift ?? 'Unknown') : 'Semua Shift' }}</p>
                <p style="margin: 5px 0; font-size: 11px;"><strong>Produk:</strong> {{ $filters['id_produk'] ? (\App\Models\JenisProduk::find($filters['id_produk'])->nama_produk ?? 'Unknown') : 'Semua Produk' }}</p>
            </div>
            <p style="color: #888; font-size: 10px; margin-top: 15px;">
                Silakan periksa kembali filter yang digunakan atau pastikan data sudah tersedia dalam sistem.
            </p>
        </div>
    @endif
 <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
            <span>{{ $filters['kode_form'] ?? '-' }}</span>
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

    <!-- <div style="margin-top: 20px; text-align: center; font-size: 8px; color: #666;">
        Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}
    </div> -->
</body>
</html>