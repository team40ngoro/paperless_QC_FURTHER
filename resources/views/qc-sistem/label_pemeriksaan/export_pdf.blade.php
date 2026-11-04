<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pemeriksaan Labelisasi Start Up</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .company-logo { float: left; width: 60px; height: 60px; text-align: center; line-height: 60px; }
        .company-info { margin-left: 80px; text-align: left; }
        .company-name { font-weight: bold; font-size: 12px; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin: 20px 0; text-decoration: underline; }
        .form-info { margin-bottom: 15px; }
        .form-info table { width: 100%; border-collapse: collapse; }
        .form-info td { padding: 3px 5px; border: 1px solid #000; font-size: 10px; }
        .main-table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 4px; text-align: center; font-size: 9px; vertical-align: middle; }
        .main-table th { background-color: #f0f0f0; font-weight: bold; }
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 30px; page-break-inside: avoid; }
        .signature-table td { width: 33.33%; text-align: center; vertical-align: bottom; padding: 5px 10px; border: none; }
        .signature-label { text-align: center; font-size: 10px; margin-bottom: 5px; }
        .signature-qr-area { height: 65px; text-align: center; vertical-align: bottom; }
        .signature-line { border-top: 1px solid #000; margin-top: 5px; margin-bottom: 5px; }
        .signature-name { font-size: 10px; text-align: center; }
        .qr-code-img { max-height: 55px; max-width: 55px; }
        .footer { position: fixed; bottom: 10px; width: 100%; text-align: right; font-size: 8px; }
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

    <div class="title">PEMERIKSAAN LABELISASI START UP</div>

    @if(isset($filterInfo))
    <div class="form-info" style="margin-bottom: 10px; border: 1px solid #ddd; padding: 5px; background-color: #f9f9f9;">
        <table style="border: none;">
            <tr style="border: none;"><td style="border: none; font-size: 10px;" colspan="4"><strong>Filter yang digunakan:</strong></td></tr>
            <tr style="border: none;">
                <td style="border: none; font-size: 10px;" width="15%"><strong>Tanggal</strong></td>
                <td style="border: none; font-size: 10px;" width="35%">: {{ $filterInfo['tanggal_mulai'] ? \Carbon\Carbon::parse($filterInfo['tanggal_mulai'])->format('d-m-Y') : 'Semua' }} s/d {{ $filterInfo['tanggal_akhir'] ? \Carbon\Carbon::parse($filterInfo['tanggal_akhir'])->format('d-m-Y') : 'Semua' }}</td>
                <td style="border: none; font-size: 10px;" width="15%"><strong>Shift</strong></td>
                <td style="border: none; font-size: 10px;" width="35%">: {{ $filterInfo['shift'] ?? 'Semua' }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; font-size: 10px;"><strong>Produk</strong></td>
                <td style="border: none; font-size: 10px;">: {{ $filterInfo['produk'] ?? 'Semua' }}</td>
            </tr>
        </table>
    </div>
    @endif

    @php $firstItem = $data->first(); @endphp

    <table class="main-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Shift</th>
                <th>Nama Produk</th>
                <th>Berat</th>
                <th>Label</th>
                <th>Pukul</th>
                <th>Foto Kode Produksi</th>
                <th>Hasil</th>
                <th>Tindakan Koreksi</th>
                <th>PIC Operator</th>
                <th>PIC QC</th>
            </tr>
        </thead>
        <tbody>
            @php $rowNumber = 1; @endphp
            @foreach($data as $item)
                @foreach($item->details as $detail)
                <tr>
                    <td>{{ $rowNumber++ }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $item->shift->shift ?? '-' }}</td>
                    <td>{{ $detail->produk->nama_produk ?? '-' }}</td>
                    <td>{{ $detail->berat ? rtrim(rtrim(number_format($detail->berat, 2), '0'), '.') . ' g' : '-' }}</td>
                    <td>{{ $detail->label_produk ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($detail->jam)->format('H:i') }}</td>
                    <td>
                        @if($detail->foto_kode_produksi)
                            @php
                                $path = storage_path('app/public/' . $detail->foto_kode_produksi);
                                $base64 = is_file($path) ? base64_encode(file_get_contents($path)) : null;
                            @endphp
                            @if($base64)
                                <img src="data:image/jpeg;base64,{{ $base64 }}" style="max-height: 60px; max-width: 100px;">
                            @else
                                -
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $detail->hasil_pengecekan ?? '-' }}</td>
                    <td>{{ $detail->tindakan_koreksi ?? '-' }}</td>
                    <td>
                        {{ $detail->nama_operator ?? '-' }}<br>
                        @if($detail->ttd_operator)
                            <img src="{{ $detail->ttd_operator }}" style="max-height: 40px; display: block; margin: 2px auto;">
                        @endif
                    </td>
                    <td>
                        {{ $detail->nama_qc ?? '-' }}<br>
                        @if($detail->ttd_qc)
                            <img src="{{ $detail->ttd_qc }}" style="max-height: 40px; display: block; margin: 2px auto;">
                        @endif
                    </td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
        {{ $kode_form ?? 'QF 36/00' }}
    </div>

    {{-- TANDA TANGAN --}}
    @php
        $qcApprover = null; $qcApproverName = 'QC';
        if($firstItem && $firstItem->approved_by_qc && $firstItem->qc_approved_by) {
            $qcApprover = \App\Models\User::find($firstItem->qc_approved_by);
            if($qcApprover) { $qcApproverName = $qcApprover->name; }
        }
        $produksiApprover = null; $produksiApproverName = 'FM/FL PRODUKSI';
        if($firstItem && $firstItem->approved_by_produksi && $firstItem->produksi_approved_by) {
            $produksiApprover = \App\Models\User::find($firstItem->produksi_approved_by);
            if($produksiApprover) { $produksiApproverName = $produksiApprover->name; }
        }
        $spvApprover = null; $spvApproverName = 'SUPERVISOR QC';
        if($firstItem && $firstItem->approved_by_spv && $firstItem->spv_approved_by) {
            $spvApprover = \App\Models\User::find($firstItem->spv_approved_by);
            if($spvApprover) { $spvApproverName = $spvApprover->name; }
        }
        $base64QcSvg = null;
        if($qcApprover && $firstItem && $firstItem->qc_approved_at) {
            $qcApprovalDate = \Carbon\Carbon::parse($firstItem->qc_approved_at)->format('Y-m-d');
            $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApproverName} (Tim QC) pada {$qcApprovalDate}"));
        }
        $base64ProduksiSvg = null;
        if($produksiApprover && $firstItem && $firstItem->produksi_approved_at) {
            $produksiApprovalDate = \Carbon\Carbon::parse($firstItem->produksi_approved_at)->format('Y-m-d');
            $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$produksiApproverName} (FM/FL PRODUKSI) pada {$produksiApprovalDate}"));
        }
        $base64SpvSvg = null;
        if($spvApprover && $firstItem && $firstItem->spv_approved_at) {
            $spvApprovalDate = \Carbon\Carbon::parse($firstItem->spv_approved_at)->format('Y-m-d');
            $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvApproverName} (SPV) pada {$spvApprovalDate}"));
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
            <td class="signature-name">{{ strtoupper($qcApproverName) }}</td>
            <td class="signature-name">{{ strtoupper($produksiApproverName) }}</td>
            <td class="signature-name">{{ strtoupper($spvApproverName) }}</td>
        </tr>
    </table>
</body>
</html>
