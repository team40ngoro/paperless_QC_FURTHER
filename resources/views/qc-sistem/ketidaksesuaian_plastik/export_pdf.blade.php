<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Ketidaksesuaian Plastik</title>
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
        .doc-image {
            max-width: 80px;
            max-height: 60px;
            margin: 2px;
            border: 1px solid #ddd;
        }
        .image-container {
            text-align: center;
            padding: 2px;
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

    <div class="title">KETIDAKSESUAIAN PLASTIK</div>

    @if($data->count() > 0)
        @php
            $firstItem = $data->first();
        @endphp

        <div class="form-info">
            <table>
                <tr>
                    <td>Hari / Tanggal</td>
                    <td colspan="3">: {{ $firstItem->tanggal ? \Carbon\Carbon::parse($firstItem->tanggal)->format('l, d-m-Y') : '-' }}</td>
                </tr>
                <tr>
                    <td>Shift</td>
                    <td colspan="3">: {{ $firstItem->shift->shift ?? '-' }}</td>
                </tr>
                
            </table>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 10%;">Waktu</th>
                    <th style="width: 6%;">Shift</th>
                    <th style="width: 10%;">Nama Plastik</th>
                    <th style="width: 8%;">Jumlah (Kg)</th>
                    <th style="width: 20%;">Alasan Hold</th>
                    <th style="width: 15%;">Hold Data</th>
                    <th style="width: 33%;">Dokumentasi Tagging</th>
                    <th style="width: 33%;">Dokumentasi Penyimpangan Plastik</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                        <td>{{ $item->shift->shift ?? '-' }}</td>
                        <td>{{ $item->nama_plastik ?? '-' }}</td>
                        <td>{{ $item->jumlah_ketidaksesuaian ?? '-' }}</td>
                        <td>{{ $item->alasan_hold ?? '-' }}</td>
                        <td>{{ $item->hold_data ?? '-' }}</td>
                        <td class="image-container">
                            @if($item->dokumentasi_tagging)
                                <div style="margin-bottom: 5px;">
                                    <!-- <strong style="font-size: 7px;">Tagging:</strong><br> -->
                                    <img src="{{ public_path('storage/' . $item->dokumentasi_tagging) }}" 
                                         alt="Dokumentasi Tagging" class="doc-image">
                                </div>
                            @endif
                        </td>
                        <td class="image-container">
                            @if($item->dokumentasi_penyimpangan_plastik)
                                <div>
                                    <!-- <strong style="font-size: 7px;">Penyimpangan:</strong><br> -->
                                    <img src="{{ public_path('storage/' . $item->dokumentasi_penyimpangan_plastik) }}" 
                                         alt="Dokumentasi Penyimpangan" class="doc-image">
                                </div>
                            @endif
                            @if(!$item->dokumentasi_tagging && !$item->dokumentasi_penyimpangan_plastik)
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
 <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
            <span>{{ $kode_form ?? $firstItem->kode_form ?? '-' }}</span>
        </div>
        {{-- ===== TANDA TANGAN ===== --}}
        @php
            $allQcApproved = true;
            $allSpvApproved = true;
            $allProduksiApproved = true;

            foreach($data as $item) {
                if(!$item->approved_by_qc) $allQcApproved = false;
                if(!$item->approved_by_spv) $allSpvApproved = false;
                if(!$item->approved_by_produksi) $allProduksiApproved = false;
            }

            $qcApprover = null; $qcApproverName = 'QC Inspector';
            if($allQcApproved && count($data) > 0) {
                foreach($data as $item) {
                    if($item->approved_by_qc && $item->qc_approved_by) {
                        $qcApprover = \App\Models\User::find($item->qc_approved_by);
                        if($qcApprover) { $qcApproverName = $qcApprover->name; break; }
                    }
                }
            }

            $spvApprover = null; $spvApproverName = 'Supervisor QC';
            if($allSpvApproved && count($data) > 0) {
                foreach($data as $item) {
                    if($item->approved_by_spv && $item->spv_approved_by) {
                        $spvApprover = \App\Models\User::find($item->spv_approved_by);
                        if($spvApprover) { $spvApproverName = $spvApprover->name; break; }
                    }
                }
            }

            $produksiApprover = null; $produksiApproverName = 'SPV/Foreman/Forelady Produksi';
            if($allProduksiApproved && count($data) > 0) {
                foreach($data as $item) {
                    if($item->approved_by_produksi && $item->produksi_approved_by) {
                        $produksiApprover = \App\Models\User::find($item->produksi_approved_by);
                        if($produksiApprover) { $produksiApproverName = $produksiApprover->name; break; }
                    }
                }
            }

            $base64QcSvg = null;
            if($qcApprover && $allQcApproved) {
                $qcApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_qc && $item->qc_approved_at) { $qcApprovalDate = $item->qc_approved_at->format('Y-m-d'); break; } }
                $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}"));
            }

            $base64ProduksiSvg = null;
            if($produksiApprover && $allProduksiApproved) {
                $produksiApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_produksi && $item->produksi_approved_at) { $produksiApprovalDate = $item->produksi_approved_at->format('Y-m-d'); break; } }
                $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$produksiApprover->name} (FM/FL PRODUKSI) pada {$produksiApprovalDate}"));
            }
            $base64SpvSvg = null;
            if($spvApprover && $allSpvApproved) {
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
        <div style="text-align: center; padding: 20px;">
            <p>Tidak ada data yang sesuai dengan filter yang dipilih.</p>
        </div>
    @endif
</body>
</html>
