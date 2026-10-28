<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Area Proses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 10px;
            line-height: 1.2;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .company-logo {
            float: left;
            width: 50px;
            height: 50px;
            text-align: center;
            line-height: 50px;
            font-weight: bold;
            font-size: 12px;
        }
        .company-info {
            margin-left: 60px;
            text-align: left;
        }
        .company-name {
            font-weight: bold;
            font-size: 10px;
        }
        .title {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin: 10px 0;
            text-transform: uppercase;
        }
        .filter-info {
            margin-bottom: 10px;
            padding: 8px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .filter-info h4 {
            margin: 0 0 5px 0;
            font-size: 9px;
            font-weight: bold;
        }
        .filter-info p {
            margin: 2px 0;
            font-size: 8px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border: 2px solid #000;
        }
        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 7px;
            vertical-align: middle;
        }
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 8px;
        }
        .text-left {
            text-align: left;
        }
        .status-ok {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
        }
        .status-kotor {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
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
    </style>
</head>
<body>
    <div class="header">
        <div class="company-logo">
            <img src="{{ public_path('dist/img/cpi-logo.png') }}" alt="CPI Logo" style="width: 70px; height: 70px; object-fit: contain;">
        </div>
        <div class="company-info">
            <div class="company-name">PT. CHAROEN POKPHAND INDONESIA</div>
            <div>FOOD DIVISION {{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }}</div>
            <div>{{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }} - INDONESIA</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="title">KONDISI AREA KERJA SELAMA PRODUKSI</div>

    <div class="filter-info">
        <h4>Informasi Filter</h4>
        <p><strong>Tanggal:</strong> {{ $filterInfo['tanggal'] }} | <strong>Shift:</strong> {{ $filterInfo['shift'] }} | <strong>Area:</strong> {{ $filterInfo['area'] }} | </p>
    </div>

    @php
        $showKondisiBarang = false;
        if(!empty($data) && isset($data[0]->area)) {
            $areaName = strtolower($data[0]->area->area ?? '');
            $showKondisiBarang = in_array($areaName, ['chillroom', 'seasoning']);
        }
    @endphp

    @if(count($data) > 0)
        @php
            // Urutkan data berdasarkan jam secara ascending
            $dataSorted = collect($data)->sortBy(function($item) {
                return \Carbon\Carbon::parse($item->jam)->timestamp;
            })->values();
        @endphp
        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    @if(($filterInfo['area'] ?? '') === 'Semua Area')
                        <th rowspan="2">Area</th>
                    @endif
                    <th rowspan="2">Jam</th>
                    <th colspan="2">Kebersihan</th>
                    <th rowspan="2">Suhu Ruang (°C)</th>
                    <th rowspan="2">Suhu RM</th>
                    @if($showKondisiBarang)
                        <th rowspan="2">Kondisi Barang</th>
                    @endif
                    <th rowspan="2">Ketidaksesuaian</th>
                    <th rowspan="2">Tindakan Koreksi</th>
                </tr>
                <tr>
                    <th>Ruangan</th>
                    <th>Karyawan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataSorted as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        @if(($filterInfo['area'] ?? '') === 'Semua Area')
                            <td>{{ $item->area->area ?? '-' }}</td>
                        @endif
                        <td>{{ $item->jam }}</td>
                        <td class="{{ $item->kebersihan_ruangan === 'OK' ? 'status-ok' : 'status-kotor' }}">
                            {{ $item->kebersihan_ruangan }}
                        </td>
                        <td class="{{ $item->kebersihan_karyawan === 'OK' ? 'status-ok' : 'status-kotor' }}">
                            {{ $item->kebersihan_karyawan }}
                        </td>
                        <td>{{ $item->pemeriksaan_suhu_ruang }}</td>
                        <td>
                            @if(!empty($item->suhu_rm))
                                {!! implode('<br>', array_map('e', array_map('trim', explode(',', $item->suhu_rm)))) !!}
                            @else
                                -
                            @endif
                        </td>
                        @if($showKondisiBarang)
                            <td class="{{ $item->kondisi_barang === 'OK' ? 'status-ok' : 'status-kotor' }}">
                                {{ $item->kondisi_barang ?? '-' }}
                            </td>
                        @endif
                        <td class="text-left">{{ $item->ketidaksesuaian ?: '-' }}</td>
                        <td class="text-left">{{ $item->tindakan_koreksi ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
     <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
            <span>{{ $filterInfo['kode_form'] }}</span>
        </div>
        {{-- ===== TANDA TANGAN ===== --}}
        @php
            $qcApprover = null; $qcApproverName = 'QC Inspector' ;
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

    @else
        <div style="text-align: center; padding: 50px; font-size: 14px; color: #666;">
            <strong>Tidak ada data Area Proses yang sesuai dengan filter yang dipilih.</strong>
        </div>
    @endif
</body>
</html>
