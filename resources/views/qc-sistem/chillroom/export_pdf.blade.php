<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Penerimaan Chillroom</title>
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
        .bg-info {
            background-color: #17a2b8;
            color: white;
        }
        .bg-warning {
            background-color: #ffc107;
            color: #212529;
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
        .barcode-container {
            margin-bottom: 10px;
        }
        .barcode {
            font-family: 'Courier New', monospace;
            font-size: 8px;
            background-color: #f0f0f0;
            padding: 2px 4px;
            border: 1px solid #ccc;
            display: inline-block;
            margin-bottom: 5px;
        }
        .sample-badge {
            display: inline-block;
            padding: 2px 5px;
            margin: 1px;
            background-color: #007bff;
            color: white;
            border-radius: 3px;
            font-size: 7px;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7px;
            display: inline-block;
            margin: 1px;
        }
        .badge-primary {
            background-color: #007bff;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7px;
            display: inline-block;
            margin: 1px;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7px;
            display: inline-block;
            margin: 1px;
        }
        .progress-mini {
            width: 100%;
            height: 12px;
            background-color: #e9ecef;
            border: 1px solid #ddd;
            margin: 2px 0;
            position: relative;
        }
        .progress-bar-mini {
            height: 100%;
            text-align: center;
            line-height: 12px;
            color: white;
            font-weight: bold;
            font-size: 7px;
            float: left;
        }
        .progress-bar-success {
            background-color: #28a745;
        }
        .progress-bar-primary {
            background-color: #007bff;
        }
        .progress-bar-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .rekap-text {
            font-size: 7px;
            margin: 1px 0;
            text-align: left;
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

    <div class="title">PEMERIKSAAN KEDATANGAN BAHAN BAKU DAN PENUNJANG</div>

    @if($data->count() > 0)
        @php
            $firstItem = $data->first();

            $qcApprover = null;
            $qcApproverName = 'Operator';
            
            // Check if there's any sampling data - more strict checking
            $hasSamplingData = false;
            
            foreach($data as $item) {
                $nilaiJumlahRm = json_decode($item->nilai_jumlah_rm, true);
                $jumlahRmData = json_decode($item->jumlah_rm, true);
                
                // Cek apakah benar-benar ada data sampling yang terisi
                $hasStandarBerat = !empty($item->standar_berat) && trim((string) $item->standar_berat) !== '' && $item->standar_berat !== '-';
                
                $hasBeratSamples = false;
                if ($nilaiJumlahRm) {
                    foreach ($nilaiJumlahRm as $entry) {
                        if (is_array($entry) && !empty($entry)) {
                            // Cek apakah ada nilai yang benar-benar numerik dan > 0
                            foreach($entry as $sample) {
                                if (is_numeric($sample) && (float)$sample > 0) {
                                    $hasBeratSamples = true;
                                    break 2;
                                }
                            }
                        }
                    }
                }
                
                $hasAgregasi = false;
                if ($jumlahRmData) {
                    foreach ($jumlahRmData as $entry) {
                        if (is_array($entry)) {
                            $atas = $entry['berat_atas'] ?? 0;
                            $std = $entry['berat_std'] ?? 0;
                            $bawah = $entry['berat_bawah'] ?? 0;
                            if ($atas > 0 || $std > 0 || $bawah > 0) {
                                $hasAgregasi = true;
                                break;
                            }
                        }
                    }
                }
                
                $hasStatusRm = !empty($item->status_rm) && in_array($item->status_rm, ['diterima', 'diretur']);
                $hasCatatanRm = !empty($item->catatan_rm) && trim($item->catatan_rm) !== '' && $item->catatan_rm !== '-';
                
                // Hanya set true jika minimal ada salah satu data sampling yang benar-benar terisi dan valid
                if ($hasStandarBerat || $hasBeratSamples || $hasAgregasi || $hasStatusRm || $hasCatatanRm) {
                    $hasSamplingData = true;
                    break;
                }
            }
        @endphp

        <div class="form-info">
            <table>
                <tr>
                    <td>Hari / Tanggal</td>
                    <td colspan="3">: {{ \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($filters['tanggal_akhir'])->format('d-m-Y') }}</td>
                </tr>
                <!-- <tr>
                    <td>Shift</td>
                    <td colspan="3">: {{ $firstItem->datashift->shift ?? '-' }}</td>
                </tr> -->
                <!-- <tr>
                    <td>Jam Kedatangan</td>
                    <td colspan="3">: {{ $firstItem->jam_kedatangan ? \Carbon\Carbon::parse($firstItem->jam_kedatangan)->format('H:i') : '-' }}</td>
                </tr> -->
            </table>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th colspan="10" class="bg-warning">Pemeriksaan Kedatangan Bahan Baku</th>
                    @if($hasSamplingData)
                        <th colspan="6" class="bg-warning">Sampling Berat RM Daging SPO</th>
                    @endif
                </tr>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 7%;">Jam Kedatangan</th>
                    <th style="width: 9%;">Nama RM</th>
                    <th style="width: 7%;">Kode Produksi</th>
                    <th style="width: 5%;">Berat Perkemasan</th>
                    <th style="width: 5%;">Suhu (°C)</th>
                    <th style="width: 5%;">Sensori</th>
                    <th style="width: 5%;">Kemasan</th>
                    <th style="width: 7%;">Keterangan</th>
                    <th style="width: 7%;">Catatan</th>
                    @if($hasSamplingData)
                        <th style="width: 7%;">Standar Berat Per PCS</th>
                        <th style="width: 8%;">Hasil Aktual Berat Per PCS (gr)</th>
                        <th style="width: 8%;">Jumlah yang Disampling</th>
                        <th style="width: 10%;">Rekapitulasi (%)</th>
                        <th style="width: 5%;">Status</th>
                        <th style="width: 7%;">Catatan RM</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->jam_kedatangan ? \Carbon\Carbon::parse($item->jam_kedatangan)->format('H:i') : '-' }}</td>
                        <td>{{ $item->nama_rm ?? '-' }}</td>
                        <td>{{ $item->kode_produksi ?? '-' }}</td>
                        <td>
                            @if($item->berat)
                                {{ $item->berat }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($item->suhu == 0 && $item->berat == '-')
                                Frozen
                            @elseif($item->suhu !== null)
                                {{ $item->suhu }}°C
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($item->sensori == '✔' || $item->sensori == '&#10004;')
                                OK
                            @elseif($item->sensori == '✘' || $item->sensori == '&#10008;')
                                Tidak OK
                            @else
                                {{ $item->sensori ?? '-' }}
                            @endif
                        </td>
                        <td>
                            @if($item->kemasan == '✔' || $item->kemasan == '&#10004;')
                                OK
                            @elseif($item->kemasan == '✘' || $item->kemasan == '&#10008;')
                                Tidak OK
                            @else
                                {{ $item->kemasan ?? '-' }}
                            @endif
                        </td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td>{{ $item->catatan ?? '-' }}</td>
                        @if($hasSamplingData)
                            {{-- Standar Berat Per PCS --}}
                            <td>
                                {{ $item->standar_berat ?? '-' }}
                            </td>
                            
                            {{-- Hasil Aktual Berat Per PCS --}}
                            <td>
                                @php
                                    $nilaiJumlahRm = json_decode($item->nilai_jumlah_rm, true);
                                    $beratSamples = [];
                                    
                                    if ($nilaiJumlahRm) {
                                        foreach ($nilaiJumlahRm as $entry) {
                                            if (is_array($entry)) {
                                                $beratSamples = $entry;
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                
                                @if(!empty($beratSamples))
                                    @foreach($beratSamples as $idx => $sample)
                                        @if(is_numeric($sample))
                                            <span class="sample-badge">
                                                S{{ $idx + 1 }}: {{ number_format((float)$sample, 2) }}gr
                                            </span>
                                        @endif
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                            
                            {{-- Jumlah yang Disampling --}}
                            <td>
                                @php
                                    $jumlahRmData = json_decode($item->jumlah_rm, true);
                                    $agregasiData = [
                                        'berat_atas' => 0,
                                        'berat_std' => 0,
                                        'berat_bawah' => 0
                                    ];
                                    
                                    if ($jumlahRmData) {
                                        foreach ($jumlahRmData as $entry) {
                                            if (is_array($entry)) {
                                                $agregasiData['berat_atas'] = $entry['berat_atas'] ?? 0;
                                                $agregasiData['berat_std'] = $entry['berat_std'] ?? 0;
                                                $agregasiData['berat_bawah'] = $entry['berat_bawah'] ?? 0;
                                                break;
                                            }
                                        }
                                    }
                                    
                                    $hasData = $agregasiData['berat_atas'] > 0 || $agregasiData['berat_std'] > 0 || $agregasiData['berat_bawah'] > 0;
                                    $total = $agregasiData['berat_atas'] + $agregasiData['berat_std'] + $agregasiData['berat_bawah'];
                                    
                                    // Hitung persentase
                                    $persenAtas = $total > 0 ? ($agregasiData['berat_atas'] / $total) * 100 : 0;
                                    $persenStd = $total > 0 ? ($agregasiData['berat_std'] / $total) * 100 : 0;
                                    $persenBawah = $total > 0 ? ($agregasiData['berat_bawah'] / $total) * 100 : 0;
                                @endphp
                                
                                @if($hasData)
                                    @if($agregasiData['berat_atas'] > 0)
                                        <span class="badge-success">Atas: {{ $agregasiData['berat_atas'] }} pcs</span><br>
                                    @endif
                                    @if($agregasiData['berat_std'] > 0)
                                        <span class="badge-primary"> Std: {{ $agregasiData['berat_std'] }} pcs</span><br>
                                    @endif
                                    @if($agregasiData['berat_bawah'] > 0)
                                        <span class="badge-warning"> Bawah: {{ $agregasiData['berat_bawah'] }} pcs</span><br>
                                    @endif
                                    <small style="font-size: 7px;">Total: {{ $total }} pcs</small>
                                @else
                                    -
                                @endif
                            </td>
                            
                            {{-- KOLOM REKAPITULASI PERSENTASE --}}
                            <td style="padding: 3px;">
                                @if($hasData && $total > 0)
                                    <div style="width: 100%;">
                                        {{-- Di Atas Standar --}}
                                        @if($persenAtas > 0)
                                            <div class="rekap-text" style="color: #28a745; font-weight: bold;">
                                                 Atas: {{ number_format($persenAtas, 1) }}%
                                            </div>
                                            <div class="progress-mini">
                                                <div class="progress-bar-mini progress-bar-success" style="width: {{ $persenAtas }}%;">
                                                    {{ number_format($persenAtas, 0) }}%
                                                </div>
                                            </div>
                                        @endif
                                        
                                        {{-- Sesuai Standar --}}
                                        @if($persenStd > 0)
                                            <div class="rekap-text" style="color: #007bff; font-weight: bold; margin-top: 3px;">
                                                 Std: {{ number_format($persenStd, 1) }}%
                                            </div>
                                            <div class="progress-mini">
                                                <div class="progress-bar-mini progress-bar-primary" style="width: {{ $persenStd }}%;">
                                                    {{ number_format($persenStd, 0) }}%
                                                </div>
                                            </div>
                                        @endif
                                        
                                        {{-- Di Bawah Standar --}}
                                        @if($persenBawah > 0)
                                            <div class="rekap-text" style="color: #ffc107; font-weight: bold; margin-top: 3px;">
                                                 Bawah: {{ number_format($persenBawah, 1) }}%
                                            </div>
                                            <div class="progress-mini">
                                                <div class="progress-bar-mini progress-bar-warning" style="width: {{ $persenBawah }}%;">
                                                    {{ number_format($persenBawah, 0) }}%
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            
                            {{-- Status --}}
                            <td>
                                @if($item->status_rm === 'diterima')  
                                    Diterima
                                @elseif($item->status_rm === 'diretur')
                                    Diretur
                                @else
                                    -
                                @endif
                            </td>
                            
                            {{-- Catatan RM --}}
                            <td>{{ $item->catatan_rm ?? '-' }}</td>
                        @endif
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

            // Generate QR codes jika ada approver
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

        @endphp

        <table class="signature-table">
            {{-- Baris 1: Label --}}
            <tr>
                <td class="signature-label">Dibuat Oleh: QC INSPEKTOR</td>
                <td class="signature-label">Disetujui Oleh: Supervisor QC</td>
            </tr>
            {{-- Baris 2: Area QR / ruang tanda tangan --}}
            <tr>
                <td class="signature-qr-area">
                    @if($base64QcSvg)
                        <img src="{{ $base64QcSvg }}" class="qr-code-img" alt="QR Code QC">
                    @endif
                </td>
                <td class="signature-qr-area">
                    @if($base64SpvSvg)
                        <img src="{{ $base64SpvSvg }}" class="qr-code-img" alt="QR Code SPV">
                    @endif
                </td>
            </tr>
            {{-- Baris 3: Garis tanda tangan --}}
            <tr>
                <td><div class="signature-line"></div></td>
                <td><div class="signature-line"></div></td>
            </tr>
            {{-- Baris 4: Nama --}}
            <tr>
                <td class="signature-name">{{ $qcApproverName }}</td>
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