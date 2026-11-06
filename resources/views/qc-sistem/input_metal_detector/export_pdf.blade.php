<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Metal Detector</title>
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
        .bg-info {
            background-color: #d1ecf1 !important;
        }
        .bg-success {
            background-color: #d4edda !important;
        }
        .bg-warning {
            background-color: #fff3cd !important;
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
            <div>FOOD DIVISION {{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }}</div>
            <div>{{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }} - INDONESIA</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="title">PEMERIKSAAN METAL DETECTOR </div>

    <div class="filter-info">
        <h4>Informasi Filter</h4>
        <p><strong>Tanggal:</strong> {{ $filterInfo['tanggal'] }} | <strong>Shift:</strong> {{ $filterInfo['shift'] }} | <strong>Produk:</strong> {{ $filterInfo['produk'] }} | <strong>Line:</strong> {{ $filterInfo['line'] ?? 'Semua Line' }} | </p>
    </div>

    @if(count($data) > 0)
        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Jam</th>
                    <th rowspan="2">Nama Produk</th>
                    <th rowspan="2">Kode Produksi</th>
                    <th rowspan="2">Line</th>
                    <th rowspan="2">Merk</th>
                    <th rowspan="2">Waktu</th>
                    <th colspan="3" class="bg-info">Fe 1.5 mm</th>
                    <th colspan="3" class="bg-success">Non Fe 2 mm</th>
                    <th colspan="3" class="bg-warning">SUS 316 2.5 mm</th>
                    <th rowspan="2">Reject</th>
                    <th rowspan="2">Operator / SPV</th>
                    <th colspan="2" style="background-color: #e2e3e5 !important;">Verifikasi QC</th>
                    <th rowspan="2">Keterangan</th>
                </tr>
                <tr>
                    <th class="bg-info">Depan</th>
                    <th class="bg-info">Tengah</th>
                    <th class="bg-info">Belakang</th>
                    <th class="bg-success">Depan</th>
                    <th class="bg-success">Tengah</th>
                    <th class="bg-success">Belakang</th>
                    <th class="bg-warning">Depan</th>
                    <th class="bg-warning">Tengah</th>
                    <th class="bg-warning">Belakang</th>
                    <th style="background-color: #e2e3e5 !important;">Detail</th>
                    <th style="background-color: #e2e3e5 !important;">Proses</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                      <td>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                    <td>{{ $item->produk->nama_produk ?? '-' }} {{ $item->berat_produk ?? '-' }}g</td>
                    <td>{{ $item->kode_produksi }}</td>
                    <td>{{ $item->line ?? '-' }}</td>
                    <td>{{ $item->merk ?? '-' }}</td>
                    <td>{{ $item->waktu_mulai ? \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') : '-' }}-{{ $item->waktu_selesai ? \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') : '-' }}</td>
                    <td class="bg-info">
                        @if($item->fe_depan_aktual == '✔' || $item->fe_depan_aktual == '&#10004;')
                            OK
                        @elseif($item->fe_depan_aktual == '✘' || $item->fe_depan_aktual == '&#10008;')
                            Tidak OK
                        @else
                            {{ $item->fe_depan_aktual ?? '-' }}
                        @endif
                    </td>
                    <td class="bg-info">
                        @if($item->fe_tengah_aktual == '✔' || $item->fe_tengah_aktual == '&#10004;')
                            OK
                        @elseif($item->fe_tengah_aktual == '✘' || $item->fe_tengah_aktual == '&#10008;')
                            Tidak OK
                        @else
                            {{ $item->fe_tengah_aktual ?? '-' }}
                        @endif
                    </td>
                    <td class="bg-info">
                        @if($item->fe_belakang_aktual == '✔' || $item->fe_belakang_aktual == '&#10004;')
                            OK
                        @elseif($item->fe_belakang_aktual == '✘' || $item->fe_belakang_aktual == '&#10008;')
                            Tidak OK
                        @else
                            {{ $item->fe_belakang_aktual ?? '-' }}
                        @endif
                    </td>
                    <td class="bg-success">
                        @if($item->non_fe_depan_aktual == '✔' || $item->non_fe_depan_aktual == '&#10004;')
                            OK
                        @elseif($item->non_fe_depan_aktual == '✘' || $item->non_fe_depan_aktual == '&#10008;')
                            Tidak OK
                        @else
                            {{ $item->non_fe_depan_aktual ?? '-' }}
                        @endif
                    </td>
                    <td class="bg-success">
                        @if($item->non_fe_tengah_aktual == '✔' || $item->non_fe_tengah_aktual == '&#10004;')
                            OK
                        @elseif($item->non_fe_tengah_aktual == '✘' || $item->non_fe_tengah_aktual == '&#10008;')
                            Tidak OK
                        @else
                            {{ $item->non_fe_tengah_aktual ?? '-' }}
                        @endif
                    </td>
                    <td class="bg-success">
                        @if($item->non_fe_belakang_aktual == '✔' || $item->non_fe_belakang_aktual == '&#10004;')
                            OK
                        @elseif($item->non_fe_belakang_aktual == '✘' || $item->non_fe_belakang_aktual == '&#10008;')
                            Tidak OK
                        @else
                            {{ $item->non_fe_belakang_aktual ?? '-' }}
                        @endif
                    </td>
                    <td class="bg-warning">
                        @if($item->sus_depan_aktual == '✔' || $item->sus_depan_aktual == '&#10004;')
                            OK
                        @elseif($item->sus_depan_aktual == '✘' || $item->sus_depan_aktual == '&#10008;')
                            Tidak OK
                        @else
                            {{ $item->sus_depan_aktual ?? '-' }}
                        @endif
                    </td>
                    <td class="bg-warning">
                        @if($item->sus_tengah_aktual == '✔' || $item->sus_tengah_aktual == '&#10004;')
                            OK
                        @elseif($item->sus_tengah_aktual == '✘' || $item->sus_tengah_aktual == '&#10008;')
                            Tidak OK
                        @else
                            {{ $item->sus_tengah_aktual ?? '-' }}
                        @endif
                    </td>
                    <td class="bg-warning">
                        @if($item->sus_belakang_aktual == '✔' || $item->sus_belakang_aktual == '&#10004;')
                            OK
                        @elseif($item->sus_belakang_aktual == '✘' || $item->sus_belakang_aktual == '&#10008;')
                            Tidak OK
                        @else
                            {{ $item->sus_belakang_aktual ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $item->jumlah_reject ?? 0 }}</td>
                    <td>{{ $item->nama_operator ?? '-' }} / {{ $item->spv_foreman ?? '-' }}</td>
                    <td>{{ $item->verifikasi_detail == '✔' ? 'OK' : ($item->verifikasi_detail == '✘' ? 'Tidak OK' : ($item->verifikasi_detail ?? '-')) }}</td>
                    <td>{{ $item->verifikasi_hasil_proses == '✔' ? 'OK' : ($item->verifikasi_hasil_proses == '✘' ? 'Tidak OK' : ($item->verifikasi_hasil_proses ?? '-')) }}</td>
                    <td>{{ $item->keterangan ?: ($item->keterangan_b ?? '-') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
            <span>{{ $filterInfo['kode_form'] }}</span>
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
        <td class="signature-label">Dibuat Oleh: QC INSPEKTOR</td>
        <td class="signature-label">Diketahui Oleh: Produksi</td>
        <td class="signature-label">Disetujui Oleh: Supervisor QC</td>
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
        <div style="text-align: center; padding: 40px; border: 2px dashed #ccc; margin: 20px 0; background-color: #f9f9f9;">
            <h3 style="color: #666; margin-bottom: 10px;">
                <i style="font-size: 24px;">⚠️</i>
            </h3>
            <h4 style="color: #333; margin-bottom: 15px;">Tidak Ada Data Ditemukan</h4>
            <p style="color: #666; margin-bottom: 10px; font-size: 12px;">
                Tidak ada data Metal Detector yang sesuai dengan filter yang dipilih:
            </p>
            <div style="background-color: #fff; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #ddd;">
                <p style="margin: 5px 0; font-size: 11px;"><strong>Tanggal:</strong> {{ $filterInfo['tanggal'] }}</p>
                <p style="margin: 5px 0; font-size: 11px;"><strong>Shift:</strong> {{ $filterInfo['shift'] }}</p>
                <p style="margin: 5px 0; font-size: 11px;"><strong>Produk:</strong> {{ $filterInfo['produk'] }}</p>
                <p style="margin: 5px 0; font-size: 11px;"><strong>Line:</strong> {{ $filterInfo['line'] ?? 'Semua Line' }}</p>
                <p style="margin: 5px 0; font-size: 11px;"><strong>Kode Form:</strong> {{ $filterInfo['kode_form'] }}</p>
            </div>
            <p style="color: #888; font-size: 10px; margin-top: 15px;">
                Silakan periksa kembali filter yang digunakan atau pastikan data sudah tersedia dalam sistem.
            </p>
        </div>
    @endif
</body>
</html>
