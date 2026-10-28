<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Persiapan Bahan Emulsi</title>
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
        .section-header {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        .proses-header {
            background-color: #d0d0d0;
            font-weight: bold;
            text-align: left;
            padding-left: 8px !important;
            font-size: 9px;
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
        .page-break {
            page-break-before: always;
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
            <div>FOOD DIVISION {{ strtoupper(auth()->user()->plan->nama_plan) }}</div>
            <div>{{ strtoupper(auth()->user()->plan->nama_plan) }} - INDONESIA</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="title">VERIFIKASI PEMBUATAN EMULSI{{ isset($filters['is_audit']) && $filters['is_audit'] ? ' (DATA AUDIT)' : '' }}</div>

    @foreach($data->groupBy(function($item) { return $item->tanggal->format('Y-m-d') . '_' . $item->produk->nama_produk; }) as $groupKey => $groupItems)
    @php
        $firstItem = $groupItems->first();
        [$date, $productName] = explode('_', $groupKey, 2);
    @endphp
    
    @if(!$loop->first)
        <div class="page-break"></div>
    @endif

    <div class="form-info">
        <table>
            <tr>
                <td>Hari / Tanggal</td>
                <td colspan="3">: {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d-m-Y') }}</td>
            </tr>
            <tr>
                <td>NAMA PRODUK</td>
                <td colspan="3">: {{ $productName }}</td>
            </tr>
            <tr>
                <td>GRAMASE</td>
                <td colspan="3">: {{ $firstItem->berat_sampling ?? '-' }} gram</td>
            </tr>
            <tr>
                <td>Shift</td>
                <td colspan="3">: {{ $firstItem->shift->shift ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Tabel Summary -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 8%;">Jam</th>
                <th style="width: 26%;">Kode Produksi Emulsi</th>
                <th style="width: 26%;">Nama Emulsi</th>
                <th style="width: 17%;">Jumlah Proses</th>
                <th style="width: 17%;">Total Pemakaian</th>
            </tr>
        </thead>
        <tbody>
            @php $rowNumber = 1; @endphp
            @foreach($groupItems as $item)
            <tr>
                <td>{{ $rowNumber }}</td>
                <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                <td>{{ $item->kode_produksi_emulsi ?? '-' }}</td>
                <td>{{ $item->nama_emulsi->nama_emulsi ?? '-' }}</td>
                <td>{{ $item->nomor_emulsi->nomor_emulsi ?? '-' }}</td>
                <td>{{ $item->total_pemakaian->total_pemakaian ?? '-' }}</td>
            </tr>
            @php $rowNumber++; @endphp
            @endforeach
        </tbody>
    </table>

    <!-- Detail Bahan Emulsi per Proses -->
    @foreach($groupItems as $itemIndex => $item)
        @if($item->suhuEmulsi && $item->suhuEmulsi->count() > 0)
            @php
                // Group data by proses_ke
                $groupedData = $item->suhuEmulsi->groupBy('proses_ke');
                $kondisiArray = json_decode($item->kondisi, true) ?? [];
                $hasilArray = json_decode($item->hasil_emulsi, true) ?? [];
            @endphp
            
            <div style="margin-top: 20px; page-break-inside: avoid;">
                <h4 style="font-size: 11px; margin-bottom: 8px; font-weight: bold;">
                    Detail Bahan Emulsi - {{ $item->kode_produksi_emulsi }}
                </h4>
                
                <table class="main-table">
                    <thead>
                        <tr>
                            <th style="width: 6%;">No</th>
                            <th style="width: 8%;">Waktu</th>
                            <th style="width: 40%;">Nama RM</th>
                            <th style="width: 15%;">Berat (gram)</th>
                            <th style="width: 16%;">Kode Produksi Bahan</th>
                            <th style="width: 15%;">Kondisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowNum = 1; @endphp
                        @foreach($groupedData as $prosesKe => $items)
                            <!-- Baris Header Proses -->
                            <tr>
                                <td colspan="6" class="proses-header">
                                    Proses Emulsi ke-{{ $prosesKe }}
                                </td>
                            </tr>
                            <!-- Data Bahan -->
                            @foreach($items as $suhu)
                            <tr>
                                <td>{{ $rowNum }}</td>
                                <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                                <td style="text-align: left; padding-left: 4px;">{{ $suhu->bahanEmulsi->nama_rm ?? '-' }}</td>
                                <td>{{ $suhu->berat_bahan ?? $suhu->bahanEmulsi->berat_rm ?? '-' }}</td>
                                <td>{{ $suhu->kode_produksi_bahan ?? '-' }}</td>
                                <td>
                                    @if($suhu->suhu == '✔' || $suhu->suhu == '&#10004;')
                                        OK
                                    @elseif($suhu->suhu == '✘' || $suhu->suhu == '&#10008;')
                                        Tidak OK
                                    @else
                                        {{ $suhu->suhu ?? '-' }}
                                    @endif
                                </td>
                            </tr>
                            @php $rowNum++; @endphp
                            @endforeach
                            
                            <!-- TAMBAHAN: Row untuk Suhu per Proses -->
                            <tr class="section-header">
                                <td colspan="3" style="text-align: left; padding-left: 8px;"><strong>Suhu</strong></td>
                                <td colspan="4">
                                    {{ $kondisiArray[$prosesKe - 1] ?? '-' }}
                                </td>
                            </tr>
                            
                            <!-- TAMBAHAN: Row untuk Hasil Emulsi per Proses -->
                            <tr class="section-header">
                                <td colspan="3" style="text-align: left; padding-left: 8px;"><strong>Hasil Emulsi</strong></td>
                                <td colspan="4">
                                    @php
                                        $hasil = $hasilArray[$prosesKe - 1] ?? '-';
                                    @endphp
                                    @if($hasil == '✔' || $hasil == '&#10004;')
                                        OK
                                    @elseif($hasil == '✘' || $hasil == '&#10008;')
                                        Tidak OK
                                    @else
                                        {{ $hasil }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach
    <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
            <span>{{ $kode_form ?? $firstItem->kode_form ?? '-' }}</span>
        </div>
    {{-- ===== TANDA TANGAN ===== --}}
    @php
        $qcApprover = null;
        $qcApproverName = 'QC';
        foreach($groupItems as $item) {
            if($item->approved_by_qc && $item->qc_approved_by) {
                $qcApprover = \App\Models\User::find($item->qc_approved_by);
                if($qcApprover) { $qcApproverName = $qcApprover->name; break; }
            }
        }
        $spvApprover = null;
        $spvApproverName = 'SPV';
        foreach($groupItems as $item) {
            if($item->approved_by_spv && $item->spv_approved_by) {
                $spvApprover = \App\Models\User::find($item->spv_approved_by);
                if($spvApprover) { $spvApproverName = $spvApprover->name; break; }
            }
        }
        $produksiApprover = null;
        $produksiApproverName = 'FM/FL PRODUKSI';
        foreach($groupItems as $item) {
            if($item->approved_by_produksi && $item->produksi_approved_by) {
                $produksiApprover = \App\Models\User::find($item->produksi_approved_by);
                if($produksiApprover) { $produksiApproverName = $produksiApprover->name; break; }
            }
        }
        $base64QcSvg = null;
        if($qcApprover) {
            $qcApprovalDate = '';
            foreach($groupItems as $item) {
                if($item->approved_by_qc && $item->qc_approved_at) { $qcApprovalDate = $item->qc_approved_at->format('Y-m-d'); break; }
            }
            $qcQrData = "Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}";
            $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate($qcQrData));
        }
        $base64SpvSvg = null;
        if($spvApprover) {
            $spvApprovalDate = '';
            foreach($groupItems as $item) {
                if($item->approved_by_spv && $item->spv_approved_at) { $spvApprovalDate = $item->spv_approved_at->format('Y-m-d'); break; }
            }
            $spvQrData = "Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}";
            $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate($spvQrData));
        }
        $base64ProduksiSvg = null;
        if($produksiApprover) {
            $produksiApprovalDate = '';
            foreach($groupItems as $item) {
                if($item->approved_by_produksi && $item->produksi_approved_at) { $produksiApprovalDate = $item->produksi_approved_at->format('Y-m-d'); break; }
            }
            $produksiQrData = "Dokumen ini telah diverifikasi secara sistem oleh {$produksiApprover->name} (FM/FL PRODUKSI) pada {$produksiApprovalDate}";
            $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate($produksiQrData));
        }
    @endphp
    <table class="signature-table">
        <tr>
            <td class="signature-label">Dibuat Oleh: QC INSPEKTOR</td>
            <td class="signature-label">Diketahui Oleh: Produksi</td>
            <td class="signature-label">Disetujui Oleh: Supervisor QC</td>
        </tr>
        <tr>
            <td class="signature-qr-area">
                @if($base64QcSvg)<img src="{{ $base64QcSvg }}" class="qr-code-img" alt="QR QC">@endif
            </td>
            <td class="signature-qr-area">
                @if($base64ProduksiSvg)<img src="{{ $base64ProduksiSvg }}" class="qr-code-img" alt="QR Produksi">@endif
            </td>
            <td class="signature-qr-area">
                @if($base64SpvSvg)<img src="{{ $base64SpvSvg }}" class="qr-code-img" alt="QR SPV">@endif
            </td>
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
    @endforeach
</body>
</html>