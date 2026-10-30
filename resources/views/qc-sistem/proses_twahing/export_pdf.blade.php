<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pemeriksaan Proses Thawing</title>
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
        .detail-cell {
            text-align: left;
            vertical-align: top;
            font-size: 7.5px;
            padding: 4px;
        }
        .detail-cell div {
            margin-bottom: 2px;
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

    <div class="title">Pemeriksaan Proses Thawing</div>

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

        @foreach($data as $headerIndex => $item)
            <div class="form-info" style="margin-top: 10px;">
                <table>
                    <tr>
                        <td>Nama Produk</td>
                        <td colspan="3">: {{ $item->produk->nama_produk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Jam</td>
                        <td>: {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                        <td>Waktu Thawing</td>
                        <td>: {{ $item->waktu_thawing_awal ? \Carbon\Carbon::parse($item->waktu_thawing_awal)->format('H:i') : '-' }} - {{ $item->waktu_thawing_akhir ? \Carbon\Carbon::parse($item->waktu_thawing_akhir)->format('H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Total (Jam)</td>
                        <td>: {{ $item->total_waktu_thawing_jam !== null ? (int)$item->total_waktu_thawing_jam : '-' }}</td>
                        <td>Dibuat Oleh</td>
                        <td>: {{ $item->user->name ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <table class="main-table">
                <thead>
                    <tr>
                        <th style="width: 18%;">Nama RM</th>
                        <th style="width: 12%;">Kode Produksi</th>
                        <th style="width: 10%;">Kondisi Ruang</th>
                        <th style="width: 8%;">Waktu Pemeriksaan</th>
                        <th style="width: 8%;">Suhu RM (°C)</th>
                        <th style="width: 8%;">Suhu Ruang (°C)</th>
                        <th style="width: 8%;">Suhu Air Thawing (°C)</th>
                        <th style="width: 8%;">Suhu Produk (°C)</th>
                        <th style="width: 10%;">Kondisi Produk</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $details = $item->details ?? collect();
                        $rowCount = 0;
                    @endphp

                    @foreach($details as $d)
                        @php $rowCount++; @endphp
                        <tr>
                            <td>{{ $d->rm->nama_rm ?? '-' }}</td>
                            <td>{{ $d->kode_produksi ?? '-' }}</td>
                            <td>{{ $d->kondisi_ruang ?? '-' }}</td>
                            <td>{{ $d->waktu_pemeriksaan ? \Carbon\Carbon::parse($d->waktu_pemeriksaan)->format('H:i') : '-' }}</td>
                            <td>{{ $d->suhu_rm ?? '-' }}</td>
                            <td>{{ $d->suhu_ruang ?? '-' }}</td>
                            <td>{{ $d->suhu_air_thawing ?? '-' }}</td>
                            <td>{{ $d->suhu_produk ?? '-' }}</td>
                            <td>{{ $d->kondisi_produk ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach

        <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
            <span>{{ $kode_form ?? $firstItem->kode_form ?? '-' }}</span>
        </div>

        {{-- ===== TANDA TANGAN ===== --}}
        @php
            $qcApprover = null; $qcApproverName = 'QC';
            foreach($data as $row) {
                if(($row->approved_by_qc ?? false) && ($row->approved_by_qc_user_id ?? null)) {
                    $qcApprover = \App\Models\User::find($row->approved_by_qc_user_id);
                    if($qcApprover) { $qcApproverName = $qcApprover->name; break; }
                }
            }
            $spvApprover = null; $spvApproverName = 'SPV';
            foreach($data as $row) {
                if(($row->approved_by_spv ?? false) && ($row->approved_by_spv_user_id ?? null)) {
                    $spvApprover = \App\Models\User::find($row->approved_by_spv_user_id);
                    if($spvApprover) { $spvApproverName = $spvApprover->name; break; }
                }
            }
            $produksiApprover = null; $produksiApproverName = 'FM/FL PRODUKSI';
            foreach($data as $row) {
                if(($row->approved_by_produksi ?? false) && ($row->approved_by_produksi_user_id ?? null)) {
                    $produksiApprover = \App\Models\User::find($row->approved_by_produksi_user_id);
                    if($produksiApprover) { $produksiApproverName = $produksiApprover->name; break; }
                }
            }
            $base64QcSvg = null;
            if($qcApprover) {
                $qcApprovalDate = '';
                foreach($data as $row) { if(($row->approved_by_qc ?? false) && ($row->approved_by_qc_at ?? null)) { $qcApprovalDate = \Carbon\Carbon::parse($row->approved_by_qc_at)->format('Y-m-d'); break; } }
                $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}"));
            }
            $base64SpvSvg = null;
            if($spvApprover) {
                $spvApprovalDate = '';
                foreach($data as $row) { if(($row->approved_by_spv ?? false) && ($row->approved_by_spv_at ?? null)) { $spvApprovalDate = \Carbon\Carbon::parse($row->approved_by_spv_at)->format('Y-m-d'); break; } }
                $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}"));
            }
            $base64ProduksiSvg = null;
            if($produksiApprover) {
                $produksiApprovalDate = '';
                foreach($data as $row) { if(($row->approved_by_produksi ?? false) && ($row->approved_by_produksi_at ?? null)) { $produksiApprovalDate = \Carbon\Carbon::parse($row->approved_by_produksi_at)->format('Y-m-d'); break; } }
                $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$produksiApprover->name} (FM/FL PRODUKSI) pada {$produksiApprovalDate}"));
            }
        @endphp
        <table class="signature-table">
            <tr>
                <td class="signature-label">Dibuat Oleh:</td>
                <td class="signature-label">Diperiksa Oleh:</td>
                <td class="signature-label">Diketahui Oleh:</td>
            </tr>
            <tr>
                <td class="signature-qr-area">@if($base64QcSvg)<img src="{{ $base64QcSvg }}" class="qr-code-img" alt="QR QC">@endif</td>
                <td class="signature-qr-area">@if($base64SpvSvg)<img src="{{ $base64SpvSvg }}" class="qr-code-img" alt="QR SPV">@endif</td>
                <td class="signature-qr-area">@if($base64ProduksiSvg)<img src="{{ $base64ProduksiSvg }}" class="qr-code-img" alt="QR Produksi">@endif</td>
            </tr>
            <tr>
                <td><div class="signature-line"></div></td>
                <td><div class="signature-line"></div></td>
                <td><div class="signature-line"></div></td>
            </tr>
            <tr>
                <td class="signature-name">{{ $qcApproverName }}</td>
                <td class="signature-name">{{ $spvApproverName }}</td>
                <td class="signature-name">{{ $produksiApproverName }}</td>
            </tr>
        </table>
    @endif
</body>
</html>
