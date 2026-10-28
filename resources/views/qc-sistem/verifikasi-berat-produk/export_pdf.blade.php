<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Berat Produk</title>
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
            table-layout: fixed;
        }
        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: left;
            font-size: 8px;
            vertical-align: middle;
            word-wrap: break-word;
        }
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .main-table tr {
            height: 20px;
        }
        .main-table thead tr {
            height: 20px;
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
    @if($data->count() > 0)
        <div class="header">
            <div class="company-logo">
                <img src="{{ public_path('dist/img/cpi-logo.png') }}" alt="CPI Logo" style="width: 60px; height: 60px; object-fit: contain;">
            </div>
            <div class="company-info">
                <div class="company-name">PT. CHAROEN POKPHAND INDONESIA</div>
                <div>FOOD DIVISION {{ strtoupper($filters['plan'] ?? '') }}</div>
                <div>{{ strtoupper($filters['plan'] ?? '') }} - INDONESIA</div>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="title">VERIFIKASI BERAT PRODUK PER TAHAPAN</div>

        <div class="form-info">
            <table>
                <tr>
                    <td>Hari / Tanggal</td>
                    <td colspan="3">: {{ $filters['tanggal_mulai'] ?? '-' }} s/d {{ $filters['tanggal_akhir'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Shift</td>
                    <td colspan="3">: {{ $filters['shift'] ?? '-' }}</td>
                </tr>
                
                <tr>
                    <td>Produk</td>
                    <td colspan="3">: {{ $filters['produk'] ?? '-' }}</td>
                </tr>
            </table>
        </div>

              <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Produk</th>
                    <th style="width: 15%;">Kode Produksi</th>
                    <th style="width: 8%;">Gramase</th>
                    <th style="width: 10%;">Jenis</th>
                    <th style="width: 15%;">Catatan</th>
                    @php
                        $hasBreader = false;
                        $hasAfterForming = false;
                        $hasDryKfc = false;
                        $hasWetKfc = false;
                        $hasPredusting = false;
                        $hasBattering = false;
                        $hasBreadering = false;
                        $hasFryer1 = false;
                        $hasFryer2 = false;
                        $hasRoasting = false;
                        $hasPickupRoasting = false;
                        $hasPickupTotalRoasting = false;
                        $hasHasilAdonan = false;
                        $hasSuhuAdonan = false;
                        
                        foreach($data as $item) {
                            if ($item->rata_rata_breader !== null) $hasBreader = true;
                            if ($item->rata_rata_after_forming !== null) $hasAfterForming = true;
                            if ($item->hasil_adonan !== null) $hasHasilAdonan = true;
                            if ($item->suhu_adonan !== null) $hasSuhuAdonan = true;
                            if ($item->rata_rata_dry_kfc !== null) $hasDryKfc = true;
                            if ($item->rata_rata_wet_kfc !== null) $hasWetKfc = true;
                            if ($item->rata_rata_predusting !== null) $hasPredusting = true;
                            if ($item->rata_rata_battering !== null) $hasBattering = true;
                            if ($item->rata_rata_breadering !== null) $hasBreadering = true;
                            if ($item->rata_rata_fryer_1 !== null) $hasFryer1 = true;
                            if ($item->rata_rata_fryer_2 !== null) $hasFryer2 = true;
                            if ($item->rata_rata_roasting !== null) $hasRoasting = true;
                            if ($item->pickup_after_breadering_roasting !== null) $hasPickupRoasting = true;
                            if ($item->pickup_total_roasting !== null) $hasPickupTotalRoasting = true;
                        }
                    @endphp
                    @if($hasBreader)
                        <th style="width: 10%;">Rincian Breader</th>
                    @endif
                    @if($hasAfterForming)
                        <th style="width: 10%;">Rincian After Forming</th>
                    @endif
                    @if($hasHasilAdonan)
                        <th style="width: 10%;">Hasil Pencetakan</th>
                    @endif
                    @if($hasSuhuAdonan)
                        <th style="width: 10%;">Suhu Adonan (°C)</th>
                    @endif
                    @if($hasDryKfc)
                        <th style="width: 10%;">Rincian Dry KFC</th>
                    @endif
                    @if($hasWetKfc)
                        <th style="width: 10%;">Rincian Wet KFC</th>
                    @endif
                    @if($hasPredusting)
                        <th style="width: 10%;">Rincian Predusting</th>
                    @endif
                    @if($hasBattering)
                        <th style="width: 10%;">Rincian Battering</th>
                    @endif
                    @if($hasBreadering)
                        <th style="width: 10%;">Rincian Breadering</th>
                    @endif
                    @if($hasFryer1)
                        <th style="width: 10%;">Rincian Fryer 1</th>
                    @endif
                    @if($hasFryer2)
                        <th style="width: 10%;">Rincian Fryer 2</th>
                    @endif
                    @if($hasRoasting)
                        <th style="width: 10%;">Rincian Roasting</th>
                    @endif
                    @if($hasPickupRoasting)
                        <th style="width: 10%;">Pickup Roasting</th>
                    @endif
                    @if($hasPickupTotalRoasting)
                        <th style="width: 10%;">Pickup Total Roasting</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                        <td>{{ $item->kode_produksi ?? '-' }}</td>
                        <td>{{ $item->gramase ?? '-' }}</td>
                        <td>{{ $item->jenis_produk_kfc ? ($item->jenis_produk_kfc == 'KFC' ? 'Produk Yum!' : 'Non-Yum!') : '-' }}</td>
                        <td>{{ $item->catatan ?? '-' }}</td>
                        @if($hasBreader)
                            <td>
                                @php $arr = is_string($item->berat_breader) ? json_decode($item->berat_breader, true) : $item->berat_breader; @endphp
                                @if(is_array($arr))
                                    @foreach($arr as $key => $val) {{ $key + 1 }}. {{ $val }}<br> @endforeach
                                @endif
                                <hr style="margin:2px 0;"><b>Rata-rata: {{ $item->rata_rata_breader ?? '-' }}</b>
                            </td>
                        @endif
                        @if($hasAfterForming)
                            <td>
                                @php $arr = is_string($item->after_forming) ? json_decode($item->after_forming, true) : $item->after_forming; @endphp
                                @if(is_array($arr))
                                    @foreach($arr as $key => $val) {{ $key + 1 }}. {{ $val }}<br> @endforeach
                                @endif
                                <hr style="margin:2px 0;"><b>Rata-rata: {{ $item->rata_rata_after_forming ?? '-' }}</b>
                            </td>
                        @endif
                        @if($hasHasilAdonan)
                            <td>{{ $item->hasil_adonan ?? '-' }}</td>
                        @endif
                        @if($hasSuhuAdonan)
                            <td>{{ $item->suhu_adonan ?? '-' }}</td>
                        @endif
                        @if($hasDryKfc)
                            <td>
                                @php $arr = is_string($item->berat_dry_kfc) ? json_decode($item->berat_dry_kfc, true) : $item->berat_dry_kfc; @endphp
                                @if(is_array($arr))
                                    @foreach($arr as $key => $val) {{ $key + 1 }}. {{ $val }}<br> @endforeach
                                @endif
                                <hr style="margin:2px 0;"><b>Rata-rata: {{ $item->rata_rata_dry_kfc ?? '-' }}</b>
                            </td>
                        @endif
                        @if($hasWetKfc)
                            <td>
                                @php $arr = is_string($item->berat_wet_kfc) ? json_decode($item->berat_wet_kfc, true) : $item->berat_wet_kfc; @endphp
                                @if(is_array($arr))
                                    @foreach($arr as $key => $val) {{ $key + 1 }}. {{ $val }}<br> @endforeach
                                @endif
                                <hr style="margin:2px 0;"><b>Rata-rata: {{ $item->rata_rata_wet_kfc ?? '-' }}</b>
                            </td>
                        @endif
                        @if($hasPredusting)
                            <td>
                                @php $arr = is_string($item->berat_predusting) ? json_decode($item->berat_predusting, true) : $item->berat_predusting; @endphp
                                @if(is_array($arr))
                                    @foreach($arr as $key => $val) {{ $key + 1 }}. {{ $val }}<br> @endforeach
                                @endif
                                <hr style="margin:2px 0;"><b>Rata-rata: {{ $item->rata_rata_predusting ?? '-' }}</b>
                            </td>
                        @endif
                        @if($hasBattering)
                            <td>
                                @php $arr = is_string($item->berat_battering) ? json_decode($item->berat_battering, true) : $item->berat_battering; @endphp
                                @if(is_array($arr))
                                    @foreach($arr as $key => $val) {{ $key + 1 }}. {{ $val }}<br> @endforeach
                                @endif
                                <hr style="margin:2px 0;"><b>Rata-rata: {{ $item->rata_rata_battering ?? '-' }}</b>
                            </td>
                        @endif
                        @if($hasBreadering)
                            <td>
                                @php $arr = is_string($item->berat_breadering) ? json_decode($item->berat_breadering, true) : $item->berat_breadering; @endphp
                                @if(is_array($arr))
                                    @foreach($arr as $key => $val) {{ $key + 1 }}. {{ $val }}<br> @endforeach
                                @endif
                                <hr style="margin:2px 0;"><b>Rata-rata: {{ $item->rata_rata_breadering ?? '-' }}</b>
                            </td>
                        @endif
                        @if($hasFryer1)
                            <td>
                                @php $arr = is_string($item->berat_fryer_1) ? json_decode($item->berat_fryer_1, true) : $item->berat_fryer_1; @endphp
                                @if(is_array($arr))
                                    @foreach($arr as $key => $val) {{ $key + 1 }}. {{ $val }}<br> @endforeach
                                @endif
                                <hr style="margin:2px 0;"><b>Rata-rata: {{ $item->rata_rata_fryer_1 ?? '-' }}</b>
                            </td>
                        @endif
                        @if($hasFryer2)
                            <td>
                                @php $arr = is_string($item->berat_fryer_2) ? json_decode($item->berat_fryer_2, true) : $item->berat_fryer_2; @endphp
                                @if(is_array($arr))
                                    @foreach($arr as $key => $val) {{ $key + 1 }}. {{ $val }}<br> @endforeach
                                @endif
                                <hr style="margin:2px 0;"><b>Rata-rata: {{ $item->rata_rata_fryer_2 ?? '-' }}</b>
                            </td>
                        @endif
                        @if($hasRoasting)
                            <td>
                                @php $arr = is_string($item->berat_roasting) ? json_decode($item->berat_roasting, true) : $item->berat_roasting; @endphp
                                @if(is_array($arr))
                                    @foreach($arr as $key => $val) {{ $key + 1 }}. {{ $val }}<br> @endforeach
                                @endif
                                <hr style="margin:2px 0;"><b>Rata-rata: {{ $item->rata_rata_roasting ?? '-' }}</b>
                            </td>
                        @endif
                        @if($hasPickupRoasting)
                            <td>{{ $item->pickup_after_breadering_roasting ?? '-' }}</td>
                        @endif
                        @if($hasPickupTotalRoasting)
                            <td>{{ $item->pickup_total_roasting ?? '-' }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
            <span>{{ $filters['kode_form'] ?? '-' }}</span>
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
    @else
        <div style="text-align: center; padding: 40px; font-style: italic; color: #666;">
            <p>Tidak ada data yang sesuai dengan filter yang dipilih.</p>
        </div>
    @endif
</body>
</html>