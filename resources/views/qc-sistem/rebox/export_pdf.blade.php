<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Penyimpanan Bahan Rebox</title>
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

    <div class="title">VERIFIKASI PROSES REBOX</div>

    @if($data->count() > 0)
        @php
            $firstItem = $data->first();
        @endphp

        <div class="form-info">
            <table>
                <tr>
                    <td>Hari / Tanggal</td>
                    <td colspan="3">: {{ isset($filters['tanggal_mulai']) ? \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d-m-Y') : ($firstItem->tanggal ? $firstItem->tanggal->format('d-m-Y') : '-') }} s/d {{ isset($filters['tanggal_akhir']) ? \Carbon\Carbon::parse($filters['tanggal_akhir'])->format('d-m-Y') : ($firstItem->tanggal ? $firstItem->tanggal->format('d-m-Y') : '-') }}</td>
                </tr>
                <tr>
                    <td>Shift</td>
                    <td colspan="3">: {{ isset($filters['shift_id']) && $filters['shift_id'] ? ($firstItem->shift->shift ?? '-') : 'Semua Shift' }}</td>
                </tr>
            </table>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <th>Nama Produk</th>
                    <th>Kode Produksi</th>
                    <th>Best Before</th>
                    <th>Kesesuaian dan Jumlah Produk</th>
                    <th>Labelisasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->jam ? $item->jam : '-' }}</td>
                        <td>{{ $item->nama_produk ?? '-' }}</td>
                        <td>{{ $item->kode_produksi ?? '-' }}</td>
                        <td>{{ $item->best_before ? \Carbon\Carbon::parse($item->best_before)->format('d-m-Y') : '-' }}</td>
                        <td>
                            @if($item->isi_jumlah == '✔' || $item->isi_jumlah == '&#10004;')
                                OK
                            @elseif($item->isi_jumlah == '✘' || $item->isi_jumlah == '&#10008;')
                                Tidak OK
                            @else
                                {{ $item->isi_jumlah ?? '-' }}
                            @endif
                        </td>
                        <td>
                            @if($item->labelisasi == '✔' || $item->labelisasi == '&#10004;')
                                OK
                            @elseif($item->labelisasi == '✘' || $item->labelisasi == '&#10008;')
                                Tidak OK
                            @else
                                {{ $item->labelisasi ?? '-' }}
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
            $qcApprover = null;
            $qcApproverName = 'QC';
            foreach($data as $item) {
                if($item->approved_by_qc && $item->qc_approved_by) {
                    $qcApprover = \App\Models\User::find($item->qc_approved_by);
                    if($qcApprover) { $qcApproverName = $qcApprover->name; break; }
                }
            }

            $spvApprover = null;
            $spvApproverName = 'SPV';
            foreach($data as $item) {
                if($item->approved_by_spv && $item->spv_approved_by) {
                    $spvApprover = \App\Models\User::find($item->spv_approved_by);
                    if($spvApprover) { $spvApproverName = $spvApprover->name; break; }
                }
            }

            $produksiApprover = null;
            $produksiApproverName = 'FM/FL PRODUKSI';
            foreach($data as $item) {
                if($item->approved_by_produksi && $item->produksi_approved_by) {
                    $produksiApprover = \App\Models\User::find($item->produksi_approved_by);
                    if($produksiApprover) { $produksiApproverName = $produksiApprover->name; break; }
                }
            }

            $base64QcSvg = null;
            if($qcApprover) {
                $qcApprovalDate = '';
                foreach($data as $item) {
                    if($item->approved_by_qc && $item->qc_approved_at) {
                        $qcApprovalDate = $item->qc_approved_at->format('Y-m-d'); break;
                    }
                }
                $qcQrData = "Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}";
                $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate($qcQrData));
            }

            $base64SpvSvg = null;
            if($spvApprover) {
                $spvApprovalDate = '';
                foreach($data as $item) {
                    if($item->approved_by_spv && $item->spv_approved_at) {
                        $spvApprovalDate = $item->spv_approved_at->format('Y-m-d'); break;
                    }
                }
                $spvQrData = "Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}";
                $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate($spvQrData));
            }

            $base64ProduksiSvg = null;
            if($produksiApprover) {
                $produksiApprovalDate = '';
                foreach($data as $item) {
                    if($item->approved_by_produksi && $item->produksi_approved_at) {
                        $produksiApprovalDate = $item->produksi_approved_at->format('Y-m-d'); break;
                    }
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
                    @if($base64QcSvg)
                        <img src="{{ $base64QcSvg }}" class="qr-code-img" alt="QR Code QC">
                    @endif
                </td>
                <td class="signature-qr-area">
                    @if($base64ProduksiSvg)
                        <img src="{{ $base64ProduksiSvg }}" class="qr-code-img" alt="QR Code Produksi">
                    @endif
                </td>
                <td class="signature-qr-area">
                    @if($base64SpvSvg)
                        <img src="{{ $base64SpvSvg }}" class="qr-code-img" alt="QR Code SPV">
                    @endif
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

    @else
        <div style="text-align: center; padding: 20px;">
            <p>Tidak ada data yang sesuai dengan filter yang dipilih.</p>
        </div>
    @endif
</body>
</html>
