<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Persiapan Bahan Non Forming</title>
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

    <div class="title">VERIFIKASI PERSIAPAN BAHAN NON FORMING</div>

    @php
        $details = $data->details ?? collect();
        $rowspan = $details->count() > 0 ? $details->count() : 1;
    @endphp

    <div class="form-info">
        <table>
            <tr>
                <td>Hari / Tanggal</td>
                <td colspan="3">: {{ $data->tanggal ? \Carbon\Carbon::parse($data->tanggal)->format('l, d-m-Y') : '-' }}</td>
            </tr>
            <tr>
                <td>NAMA PRODUK</td>
                <td colspan="3">: {{ $data->formulaNonForming->produk->nama_produk ?? '-' }}</td>
            </tr>
            <tr>
                <td>Shift</td>
                <td colspan="3">: {{ $data->shift->shift ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 4%;">No</th>
                <th rowspan="2" style="width: 6%;">Waktu</th>
                <th rowspan="2" style="width: 7%;">Kode Produksi</th>
                <th rowspan="2" style="width: 7%;">Kode Produksi Emulsi OIL</th>
                <th rowspan="2" style="width: 7%;">Nomor Formula</th>
                <th rowspan="2" style="width: 4%;">Gramase (g)</th>
                <th colspan="5" class="section-header">LARUTAN BUMBU</th>
                <th rowspan="2" style="width: 7%;">Suhu Standard Adonan</th>
                <th rowspan="2" style="width: 6%;">REWORK (kg)</th>
                <th colspan="6" class="section-header">SUHU AKTUAL (°C)</th>
                <th rowspan="2" style="width: 7%;">Waktu Mixing</th>
                <th rowspan="2" style="width: 6%;">Kondisi</th>
                <th rowspan="2" style="width: 10%;">KETERANGAN</th>
            </tr>
            <tr>
                <th style="width: 10%;">Bahan RM</th>
                <th style="width: 10%;">Kode Produksi Bahan</th>
                <th style="width: 5%;">Suhu (°C)</th>
                <th style="width: 5%;">Sensori</th>
                <th style="width: 5%;">Ket.</th>
                <th style="width: 5%;">1</th>
                <th style="width: 5%;">2</th>
                <th style="width: 5%;">3</th>
                <th style="width: 5%;">4</th>
                <th style="width: 5%;">5</th>
                <th style="width: 5%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @if($details->count() > 0)
                @foreach($details as $index => $d)
                    <tr>
                        @if($index == 0)
                            <td rowspan="{{ $rowspan }}">1</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->jam ? \Carbon\Carbon::parse($data->jam)->format('H:i') : '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->kode_produksi ?? '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ is_array($data->kode_produksi_emulsi_oil) ? implode(', ', $data->kode_produksi_emulsi_oil) : ($data->kode_produksi_emulsi_oil ?? '-') }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->formulaNonForming->nomor_formula ?? '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->berat_sampling ?? '-' }}</td>
                        @endif
                        <td>{{ $d->bahanNonForming->nama_rm ?? '-' }}</td>
                        <td>{{ $d->kode_produksi_bahan ?? '-' }}</td>
                        <td>{{ $d->suhu ?? '-' }}</td>
                        <td>{{ $d->sensori ?? '-' }}</td>
                        <td>{{ $d->keterangan ?? '-' }}</td>
                        @if($index == 0)
                            <td rowspan="{{ $rowspan }}">{{ $data->suhuAdonan->std_suhu ?? '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->rework ?? '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_1 : '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_2 : '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_3 : '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_4 : '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_5 : '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->aktualSuhuAdonan ? number_format($data->aktualSuhuAdonan->total_aktual_suhu, 1) : '-' }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $data->waktu_mulai_mixing ?? '-' }} - {{ $data->waktu_selesai_mixing ?? '-' }}</td>
                            <td rowspan="{{ $rowspan }}">
                                @if($data->kondisi == '✔' || $data->kondisi == '&#10004;')
                                    OK
                                @elseif($data->kondisi == '✘' || $data->kondisi == '&#10008;')
                                    Not OK
                                @else
                                    {{ $data->kondisi ?? '-' }}
                                @endif
                            </td>
                            <td rowspan="{{ $rowspan }}">{{ $data->catatan ?? '-' }}</td>
                        @endif
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>1</td>
                    <td>{{ $data->jam ? \Carbon\Carbon::parse($data->jam)->format('H:i') : '-' }}</td>
                    <td>{{ $data->kode_produksi ?? '-' }}</td>
                    <td>{{ is_array($data->kode_produksi_emulsi_oil) ? implode(', ', $data->kode_produksi_emulsi_oil) : ($data->kode_produksi_emulsi_oil ?? '-') }}</td>
                    <td>{{ $data->formulaNonForming->nomor_formula ?? '-' }}</td>
                    <td>{{ $data->berat_sampling ?? '-' }}</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>{{ $data->suhuAdonan->std_suhu ?? '-' }}</td>
                    <td>{{ $data->rework ?? '-' }}</td>
                    <td>{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_1 : '-' }}</td>
                    <td>{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_2 : '-' }}</td>
                    <td>{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_3 : '-' }}</td>
                    <td>{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_4 : '-' }}</td>
                    <td>{{ $data->aktualSuhuAdonan ? $data->aktualSuhuAdonan->aktual_suhu_5 : '-' }}</td>
                    <td>{{ $data->aktualSuhuAdonan ? number_format($data->aktualSuhuAdonan->total_aktual_suhu, 1) : '-' }}</td>
                    <td>{{ $data->waktu_mulai_mixing ?? '-' }} - {{ $data->waktu_selesai_mixing ?? '-' }}</td>
                    <td>
                        @if($data->kondisi == '✔' || $data->kondisi == '&#10004;')
                            OK
                        @elseif($data->kondisi == '✘' || $data->kondisi == '&#10008;')
                            Not OK
                        @else
                            {{ $data->kondisi ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $data->catatan ?? '-' }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div style="width: 100%; text-align: right; margin-top: 10px; font-style: italic;">
        <span>QF 04/00</span>
    </div>

    {{-- ===== TANDA TANGAN ===== --}}
    @php
        $qcApprover = null; $qcApproverName = 'QC';
        $spvApprover = null; $spvApproverName = 'SPV';
        $produksiApprover = null; $produksiApproverName = 'FM/FL PRODUKSI';
        
        if($data->approved_by_qc && $data->qc_approved_by) {
            $qcApprover = \App\Models\User::find($data->qc_approved_by);
            if($qcApprover) $qcApproverName = $qcApprover->name;
        }
        if($data->approved_by_spv && $data->spv_approved_by) {
            $spvApprover = \App\Models\User::find($data->spv_approved_by);
            if($spvApprover) $spvApproverName = $spvApprover->name;
        }
        if($data->approved_by_produksi && $data->produksi_approved_by) {
            $produksiApprover = \App\Models\User::find($data->produksi_approved_by);
            if($produksiApprover) $produksiApproverName = $produksiApprover->name;
        }

        $base64QcSvg = null;
        if($qcApprover) {
            $qcDate = $data->qc_approved_at ? $data->qc_approved_at->format('Y-m-d') : '';
            $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcDate}"));
        }
        $base64SpvSvg = null;
        if($spvApprover) {
            $spvDate = $data->spv_approved_at ? $data->spv_approved_at->format('Y-m-d') : '';
            $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvDate}"));
        }
        $base64ProduksiSvg = null;
        if($produksiApprover) {
            $prodDate = $data->produksi_approved_at ? $data->produksi_approved_at->format('Y-m-d') : '';
            $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$produksiApprover->name} (FM/FL PRODUKSI) pada {$prodDate}"));
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
</body>
</html>
