<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Pemeriksaan Produk Cooking Mixer FLA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 7px;
            margin: 0;
            padding: 8px;
            line-height: 1.1;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .company-logo {
            float: left;
            width: 40px;
            height: 40px;
            text-align: center;
            line-height: 40px;
            font-weight: bold;
            font-size: 10px;
        }
        .company-info {
            margin-left: 50px;
            text-align: left;
        }
        .company-name {
            font-weight: bold;
            font-size: 8px;
        }
        .title {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            margin: 8px 0;
        }
        .filter-info {
            margin-bottom: 5px;
            padding: 4px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .filter-info h4 {
            margin: 0 0 3px 0;
            font-size: 7px;
            font-weight: bold;
        }
        .filter-info p {
            margin: 1px 0;
            font-size: 6px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            page-break-inside: auto;
            border: 1px solid #000;
        }
        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 1px;
            text-align: center;
            font-size: 5px;
            vertical-align: middle;
            page-break-inside: avoid;
        }
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-left {
            text-align: left;
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
            <img src="{{ public_path('dist/img/cpi-logo.png') }}" alt="CPI Logo" style="width: 60px; height: 60px; object-fit: contain;">
        </div>
        <div class="company-info">
            <div class="company-name">PT. CHAROEN POKPHAND INDONESIA</div>
            <div>FOOD DIVISION {{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }}</div>
            <div>{{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }} - INDONESIA</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="title">PEMERIKSAAN PRODUK DI COOKING MIXER</div>

    <div class="filter-info">
        <h4>Informasi Filter</h4>
        <p><strong>Tanggal:</strong> {{ $filters['tanggal'] ?? 'Semua' }} | <strong>Shift:</strong> {{ $filters['shift_id'] ? \App\Models\DataShift::find($filters['shift_id'])->shift ?? 'Unknown' : 'Semua' }}</p>
    </div>

    @if(count($data) > 0)
        <table class="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Shift</th>
                    <th>Nama Produk</th>
                    <th>Kode Produksi</th>
                    <th>Detail Bahan & Kode Produksi</th>
                    <th>Berat</th>
                    <th>Waktu</th>
                    <th>Status Gas</th>
                    <th>Speed</th>
                    <th>Temperature</th>
                    <th>Organoleptic</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->jam)->format('H:i') }}</td>
                        <td>{{ $item->shift->shift ?? '-' }}</td>
                        <td class="text-left">{{ $item->namaFormulaFla->produk->nama_produk ?? '-' }}</td>
                        <td>{{ $item->kode_produksi ?? '-' }}</td>
                        <td class="text-left">
                            @php
                                $kodes = $item->getKodeProduksiBahanArray();
                                $bahans = ($item->bahanFormulaFla && $item->bahanFormulaFla->getBahanFormulaArray()) 
                                            ? $item->bahanFormulaFla->getBahanFormulaArray() 
                                            : [];
                                $berats = ($item->bahanFormulaFla && $item->bahanFormulaFla->getBeratFormulaArray()) 
                                            ? $item->bahanFormulaFla->getBeratFormulaArray() 
                                            : [];
                            @endphp
                            @if(!empty($bahans))
                                <small>
                                @foreach($bahans as $bIdx => $bName)
                                    @php 
                                        $beratVal = (isset($berats[$bIdx]) && $berats[$bIdx] !== '') ? $berats[$bIdx] . ' kg' : '-';
                                        $kodeVal  = (isset($kodes[$bIdx]) && $kodes[$bIdx] !== '') ? $kodes[$bIdx] : '-'; 
                                    @endphp
                                    <strong>{{ $bName }}</strong> ({{ $beratVal }}): {{ $kodeVal }}@if(!$loop->last)<br>@endif
                                @endforeach
                                </small>
                            @elseif(!empty($kodes))
                                <small>
                                @foreach($kodes as $kVal)
                                    {{ $kVal ?: '-' }}@if(!$loop->last)<br>@endif
                                @endforeach
                                </small>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->berat ?? '-' }} gram</td>
                        <td>
                            <small>
                                Start: {{ $item->waktu_start }}<br>
                                Stop: {{ $item->waktu_stop }}
                            </small>
                        </td>
                        <td>{{ $item->status_gas ? 'ON' : 'OFF' }}</td>
                        <td>{{ $item->speed ?? '-' }} RPM</td>
                        <td>
                            <small>
                                T1: {{ $item->temp_std_1 ?? '-' }}°C<br>
                                T2: {{ $item->temp_std_2 ?? '-' }}°C<br>
                                T3: {{ $item->temp_std_3 ?? '-' }}°C
                            </small>
                        </td>
                        <td>
                            <small>
                                W: {{ $item->organo_warna ?? '-' }}<br>
                                A: {{ $item->organo_aroma ?? '-' }}<br>
                                T: {{ $item->organo_tekstur ?? '-' }}<br>
                                R: {{ $item->organo_rasa ?? '-' }}
                            </small>
                        </td>
                        <td class="text-left">{{ $item->catatan ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
         <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
            <span>{{ $filters['kode_form'] ?? '-' }}</span>
        </div>
        {{-- ===== TANDA TANGAN ===== --}}
        @php
            $qcApprover = null; $qcApproverName = 'QC Inspector';
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
            $base64SpvSvg = null;
            if($spvApprover) {
                $spvApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_spv && $item->spv_approved_at) { $spvApprovalDate = $item->spv_approved_at->format('Y-m-d'); break; } }
                $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}"));
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
        <div style="text-align: center; padding: 40px; border: 2px dashed #ccc; margin: 20px 0; background-color: #f9f9f9;">
            <h3 style="color: #666; margin-bottom: 10px;">
                <i style="font-size: 24px;">⚠️</i>
            </h3>
            <h4 style="color: #333; margin-bottom: 15px;">Tidak Ada Data Ditemukan</h4>
            <p style="color: #666; margin-bottom: 10px; font-size: 12px;">
                Tidak ada data Pemeriksaan Produk Cooking Mixer FLA yang sesuai dengan filter yang dipilih:
            </p>
            <div style="background-color: #fff; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #ddd;">
                <p style="margin: 5px 0; font-size: 11px;"><strong>Tanggal:</strong> {{ $filters['tanggal'] ?? 'Semua' }}</p>
                <p style="margin: 5px 0; font-size: 11px;"><strong>Shift:</strong> {{ $filters['shift_id'] ? \App\Models\DataShift::find($filters['shift_id'])->shift ?? 'Unknown' : 'Semua' }}</p>
            </div>
            <p style="color: #888; font-size: 10px; margin-top: 15px;">
                Silakan periksa kembali filter yang digunakan atau pastikan data sudah tersedia dalam sistem.
            </p>
        </div>
    @endif
</body>
</html>
