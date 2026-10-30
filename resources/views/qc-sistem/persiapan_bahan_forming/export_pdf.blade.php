<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Persiapan Bahan Forming</title>
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
        tr { page-break-inside: avoid; }
        tbody { page-break-inside: avoid; }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            page-break-inside: avoid;
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

    <div class="title">VERIFIKASI PERSIAPAN COLD MIXING</div>

    @foreach($data->groupBy(function($item) { return $item->tanggal->format('Y-m-d') . '_' . $item->formula->produk->nama_produk; }) as $groupKey => $groupItems)
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
                <td colspan="3">: {{ \Carbon\Carbon::parse($date)->format('l, d-m-Y') }}</td>
                
            </tr>
            <tr>
                <td>NAMA PRODUK</td>
                <td colspan="3">: {{ $productName }}</td>
            </tr>
            <tr>
                <td>Shift</td>
                <td colspan="3">: {{ $firstItem->shift->shift ?? '-' }}</td>
                <!-- <td></td> -->
                <!-- <td></td> -->
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
                <th rowspan="2" style="width: 8%;">Suhu Standard Adonan</th>
                <th rowspan="2" style="width: 5%;">PRODUK REWORK</th>
                <th rowspan="2" style="width: 6%;">REWORK (kg)</th>
                <th colspan="6" class="section-header">SUHU AKTUAL (°C)</th>
                <th rowspan="2" style="width: 8%;">Waktu Mixing</th>
                <th rowspan="2" style="width: 6%;">Kondisi Hasil Penggilingan</th>
                <th rowspan="2" style="width: 6%;">Kondisi Hasil Pencampuran</th>
                <th rowspan="2" style="width: 10%;">KETERANGAN</th>
            </tr>
            <tr>
                <th style="width: 8%;">Bahan RM</th>
                <th style="width: 8%;">Kode Produksi Bahan</th>
                <th style="width: 4%;">Suhu (°C)</th>
                <th style="width: 5%;">Sensori</th>
                <th style="width: 5%;">Keterangan</th>
                <th style="width: 5%;">1</th>
                <th style="width: 5%;">2</th>
                <th style="width: 5%;">3</th>
                <th style="width: 5%;">4</th>
                <th style="width: 5%;">5</th>
                <th style="width: 5%;">Total</th>
            </tr>
        </thead>
        @php $rowNumber = 1; @endphp
        @foreach($groupItems as $item)
            @php
                $suhuCount = max(1, $item->suhuForming->count());
            @endphp
            <tbody style="page-break-inside: avoid;">
            @if($item->suhuForming->count() > 0)
                @foreach($item->suhuForming as $index => $suhu)
                <tr>
                    @if($index == 0)
                        <td rowspan="{{ $suhuCount }}">{{ $rowNumber }}</td>
                        <td rowspan="{{ $suhuCount }}">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                        <td rowspan="{{ $suhuCount }}">{{ $item->kode_produksi_emulsi ?? '-' }}</td>
                        <td rowspan="{{ $suhuCount }}">{{ is_array($item->kode_produksi_emulsi_oil) ? implode(', ', $item->kode_produksi_emulsi_oil) : ($item->kode_produksi_emulsi_oil ?? '-') }}</td>                        
                        <td rowspan="{{ $suhuCount }}">{{ $item->formula->nomor_formula ?? '-' }}</td>
                        <td rowspan="{{ $suhuCount }}">{{ $item->berat_sampling ?? '-' }}</td>
                    @endif
                    <td>{{ $suhu->bahanForming->nama_rm ?? '-' }}</td>
                    <td>{{ $suhu->kode_produksi_bahan ?? '-' }}</td>
                    <td>{{ $suhu->suhu }}°C</td>
                    <td>{{ $suhu->sensori ?? '-' }}</td>
                    <td>{{ $suhu->keterangan ?? '-' }}</td>
                    @if($index == 0)
                        <td rowspan="{{ $suhuCount }}">{{ $item->suhuAdonan->std_suhu ?? '-' }}</td>
                        @php
                            $reworkNamaPdf = '-';
                            if (!empty($item->produk_rework_id)) {
                                $reworkNamaPdf = \App\Models\JenisProduk::whereIn('id', (array) $item->produk_rework_id)->pluck('nama_produk')->implode(', ');
                                $reworkNamaPdf = $reworkNamaPdf ?: '-';
                            }
                        @endphp
                        <td rowspan="{{ $suhuCount }}">{{ $reworkNamaPdf }}</td>
                        <td rowspan="{{ $suhuCount }}">{{ $item->rework ?? '-' }}</td>
                        <td rowspan="{{ $suhuCount }}">
                            {{ $item->aktualSuhuAdonan ? $item->aktualSuhuAdonan->aktual_suhu_1 : '-' }}
                        </td>
                        <td rowspan="{{ $suhuCount }}">
                            {{ $item->aktualSuhuAdonan ? $item->aktualSuhuAdonan->aktual_suhu_2 : '-' }}
                        </td>
                        <td rowspan="{{ $suhuCount }}">
                            {{ $item->aktualSuhuAdonan ? $item->aktualSuhuAdonan->aktual_suhu_3 : '-' }}
                        </td>
                        <td rowspan="{{ $suhuCount }}">
                            {{ $item->aktualSuhuAdonan ? $item->aktualSuhuAdonan->aktual_suhu_4 : '-' }}
                        </td>
                        <td rowspan="{{ $suhuCount }}">
                            {{ $item->aktualSuhuAdonan ? $item->aktualSuhuAdonan->aktual_suhu_5 : '-' }}
                        </td>
                        <td rowspan="{{ $suhuCount }}">
                            {{ $item->aktualSuhuAdonan ? number_format($item->aktualSuhuAdonan->total_aktual_suhu, 1) : '-' }}
                        </td>
                        <td rowspan="{{ $suhuCount }}">
                            {{ $item->waktu_mulai_mixing ?? '-' }} - {{ $item->waktu_selesai_mixing ?? '-' }}
                        </td>
                        <td rowspan="{{ $suhuCount }}">
                            @if(strpos($item->kondisi, '✔') !== false || $item->kondisi == '&#10004;')
                                OK
                            @elseif(strpos($item->kondisi, '✘') !== false || $item->kondisi == '&#10008;')
                                Not OK
                            @else
                                {{ $item->kondisi ?? '-' }}
                            @endif
                        </td>
                        <td rowspan="{{ $suhuCount }}">
                            @if(strpos($item->kondisi_hasil_pencampuran, '✔') !== false || $item->kondisi_hasil_pencampuran == '&#10004;')
                                OK
                            @elseif(strpos($item->kondisi_hasil_pencampuran, '✘') !== false || $item->kondisi_hasil_pencampuran == '&#10008;')
                                Not OK
                            @else
                                {{ $item->kondisi_hasil_pencampuran ?? '-' }}
                            @endif
                        </td>
                        <td rowspan="{{ $suhuCount }}">{{ $item->catatan ?? '-' }}</td>
                    @endif
                </tr>
                @endforeach
            @else
                <tr>
                    <td>{{ $rowNumber }}</td>
                    <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                    <td>{{ $item->kode_produksi_emulsi ?? '-' }}</td>
                    <td>{{ is_array($item->kode_produksi_emulsi_oil) ? implode(', ', $item->kode_produksi_emulsi_oil) : ($item->kode_produksi_emulsi_oil ?? '-') }}</td>                        
                    <td>{{ $item->formula->nomor_formula ?? '-' }}</td>
                    <td>{{ $item->berat_sampling ?? '-' }}</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>{{ $item->suhuAdonan->std_suhu ?? '-' }}</td>
                    @php
                        $reworkNamaPdfElse = '-';
                        if (!empty($item->produk_rework_id)) {
                            $reworkNamaPdfElse = \App\Models\JenisProduk::whereIn('id', (array) $item->produk_rework_id)->pluck('nama_produk')->implode(', ');
                            $reworkNamaPdfElse = $reworkNamaPdfElse ?: '-';
                        }
                    @endphp
                    <td>{{ $reworkNamaPdfElse }}</td>
                    <td>{{ $item->rework ?? '-' }}</td>
                    <td>{{ $item->aktualSuhuAdonan ? $item->aktualSuhuAdonan->aktual_suhu_1 : '-' }}</td>
                    <td>{{ $item->aktualSuhuAdonan ? $item->aktualSuhuAdonan->aktual_suhu_2 : '-' }}</td>
                    <td>{{ $item->aktualSuhuAdonan ? $item->aktualSuhuAdonan->aktual_suhu_3 : '-' }}</td>
                    <td>{{ $item->aktualSuhuAdonan ? $item->aktualSuhuAdonan->aktual_suhu_4 : '-' }}</td>
                    <td>{{ $item->aktualSuhuAdonan ? $item->aktualSuhuAdonan->aktual_suhu_5 : '-' }}</td>
                    <td>{{ $item->aktualSuhuAdonan ? number_format($item->aktualSuhuAdonan->total_aktual_suhu, 1) : '-' }}</td>
                    <td>{{ $item->waktu_mulai_mixing ?? '-' }} - {{ $item->waktu_selesai_mixing ?? '-' }}</td>
                    <td>
                        @if(strpos($item->kondisi, '✔') !== false || $item->kondisi == '&#10004;')
                            OK
                        @elseif(strpos($item->kondisi, '✘') !== false || $item->kondisi == '&#10008;')
                            Not OK
                        @else
                            {{ $item->kondisi ?? '-' }}
                        @endif
                    </td>
                    <td>
                        @if(strpos($item->kondisi_hasil_pencampuran, '✔') !== false || $item->kondisi_hasil_pencampuran == '&#10004;')
                            OK
                        @elseif(strpos($item->kondisi_hasil_pencampuran, '✘') !== false || $item->kondisi_hasil_pencampuran == '&#10008;')
                            Not OK
                        @else
                            {{ $item->kondisi_hasil_pencampuran ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $item->catatan ?? '-' }}</td>
                </tr>
            @endif
            </tbody>
            @php $rowNumber++; @endphp
        @endforeach
    </table>
         <div style="width: 100%; text-align: right; margin-top: 10px; font-style: italic;">
            <span>QF 04/00</span>
        </div>
    {{-- ===== TANDA TANGAN ===== --}}
    @php
        $qcApprover = null; $qcApproverName = 'QC';
        $spvApprover = null; $spvApproverName = 'SPV';
        $produksiApprover = null; $produksiApproverName = 'FM/FL PRODUKSI';
        $qcApprovalDate = ''; $spvApprovalDate = ''; $produksiApprovalDate = '';

        foreach($groupItems as $item) {
            if($item->approved_by_qc && $item->qc_approved_by) {
                $qcApprover = \App\Models\User::find($item->qc_approved_by);
                if($qcApprover) { $qcApproverName = $qcApprover->name; $qcApprovalDate = $item->qc_approved_at->format('Y-m-d'); break; }
            }
        }
        foreach($groupItems as $item) {
            if($item->approved_by_spv && $item->spv_approved_by) {
                $spvApprover = \App\Models\User::find($item->spv_approved_by);
                if($spvApprover) { $spvApproverName = $spvApprover->name; $spvApprovalDate = $item->spv_approved_at->format('Y-m-d'); break; }
            }
        }
        foreach($groupItems as $item) {
            if($item->approved_by_produksi && $item->produksi_approved_by) {
                $produksiApprover = \App\Models\User::find($item->produksi_approved_by);
                if($produksiApprover) { $produksiApproverName = $produksiApprover->name; $produksiApprovalDate = $item->produksi_approved_at->format('Y-m-d'); break; }
            }
        }

        $base64QcSvg = null;
        if($qcApprover) {
            $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}"));
        }
        $base64SpvSvg = null;
        if($spvApprover) {
            $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}"));
        }
        $base64ProduksiSvg = null;
        if($produksiApprover) {
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
    @endforeach
</body>
</html>
