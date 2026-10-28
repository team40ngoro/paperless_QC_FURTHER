<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verif CIP</title>
    <style>
        @page {
            size: A3 landscape;
            margin: 8mm;
        }
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
            padding: 2px 2px;
            text-align: center;
            font-size: 6px;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
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
            <div>FOOD DIVISION {{ strtoupper(auth()->user()->plan->nama_plan ?? '') }}</div>
            <div>{{ strtoupper(auth()->user()->plan->nama_plan ?? '') }} - INDONESIA</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="title">PEMERIKSAAN CIP</div>

    @php
        $stepsMeta = [
            ['key' => 'ro1', 'label' => 'Rinse Outside 1'],
            ['key' => 'ri1', 'label' => 'Rinse Inside 1'],
            ['key' => 'ro2', 'label' => 'Rinse Outside 2'],
            ['key' => 'ri2', 'label' => 'Rinse Inside 2'],
            ['key' => 'hc', 'label' => 'Hot Clean Inside'],
            ['key' => 'hci', 'label' => 'Hot Clean Outside'],
            ['key' => 'ro3', 'label' => 'Rinse Outside 3'],
            ['key' => 'ri3', 'label' => 'Rinse Inside 3'],
            ['key' => 'dis', 'label' => 'Disinfection'],
            ['key' => 'diso', 'label' => 'Disinfection Out'],
            ['key' => 'ro4', 'label' => 'Rinse Outside 4'],
            ['key' => 'ri4', 'label' => 'Rinse Inside 4'],
        ];

        $rows = [];
        foreach ($data as $item) {
            $forms = $item->payload['forms'] ?? [];
            if (!is_array($forms) || count($forms) === 0) {
                $forms = [
                    [
                        'tanggal' => $item->tanggal,
                        'details' => [],
                    ],
                ];
            }

            foreach ($forms as $formIndex => $form) {
                $formTanggal = data_get($form, 'tanggal') ?: $item->tanggal;
                $details = data_get($form, 'details', []);
                if (!is_array($details) || count($details) === 0) {
                    $details = [null];
                }

                foreach ($details as $detailIndex => $detail) {
                    $rows[] = [
                        'item' => $item,
                        'formTanggal' => $formTanggal,
                        'detailIndex' => is_int($detailIndex) ? $detailIndex : 0,
                        'detail' => $detail,
                    ];
                }
            }
        }

        usort($rows, function ($a, $b) {
            $da = $a['formTanggal'] ? strtotime($a['formTanggal']) : 0;
            $db = $b['formTanggal'] ? strtotime($b['formTanggal']) : 0;
            if ($da !== $db) {
                return $da <=> $db;
            }
            return ($a['detailIndex'] ?? 0) <=> ($b['detailIndex'] ?? 0);
        });

        $firstTanggal = $rows[0]['formTanggal'] ?? null;
        $firstPlan = $rows[0]['item']->plan->nama_plan ?? null;
    @endphp

    <div class="form-info">
        <table>
            <tr>
                <td>Hari / Tanggal</td>
                <td colspan="3">: {{ $firstTanggal ? \Carbon\Carbon::parse($firstTanggal)->format('l, d-m-Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Plan</td>
                <td colspan="3">: {{ $firstPlan ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 2%;">No</th>
                <th rowspan="2" style="width: 4%;">Tanggal</th>
                <th rowspan="2" style="width: 6%;">Jenis Mouldrum</th>
                @foreach($stepsMeta as $s)
                    <th colspan="3">{{ $s['label'] }}</th>
                @endforeach
                <th rowspan="2" style="width: 5%;">Kondisi Sebelum</th>
                <th rowspan="2" style="width: 5%;">Kondisi Sesudah</th>
                <th rowspan="2" style="width: 3%;">PH Air</th>
                <th rowspan="2" style="width: 3%;">Pressure</th>
                <th rowspan="2" style="width: 7%;">Keterangan</th>
                <th rowspan="2" style="width: 7%;">Tindakan Koreksi</th>
            </tr>
            <tr>
                @foreach($stepsMeta as $s)
                    <th>Aktual</th>
                    <th>Display</th>
                    <th>Waktu</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $r)
                @php
                    $detail = $r['detail'];
                    $steps = is_array($detail) ? (data_get($detail, 'steps') ?: []) : [];
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $r['formTanggal'] ? \Carbon\Carbon::parse($r['formTanggal'])->format('d-m-Y') : '-' }}</td>
                    <td class="text-left">{{ is_array($detail) ? (data_get($detail, 'jenis_mouldrum') ?: '-') : '-' }}</td>
                    @foreach($stepsMeta as $s)
                        @php
                            $key = $s['key'];
                            $stepVal = is_array($steps) ? (data_get($steps, $key) ?: []) : [];
                        @endphp
                        <td>{{ is_array($stepVal) ? (data_get($stepVal, 'suhu') ?: '-') : '-' }}</td>
                        <td>{{ is_array($stepVal) ? (data_get($stepVal, 'suhu_display') ?: '-') : '-' }}</td>
                        <td>{{ is_array($stepVal) ? (data_get($stepVal, 'waktu') ?: '-') : '-' }}</td>
                    @endforeach
                    <td class="text-left">{{ is_array($detail) ? (data_get($detail, 'kondisi_sebelum') ?: '-') : '-' }}</td>
                    <td class="text-left">{{ is_array($detail) ? (data_get($detail, 'kondisi_sesudah') ?: '-') : '-' }}</td>
                    <td>{{ is_array($detail) ? (data_get($detail, 'ph_air') ?: '-') : '-' }}</td>
                    <td>{{ is_array($detail) ? (data_get($detail, 'pressure') ?: '-') : '-' }}</td>
                    <td class="text-left">{{ is_array($detail) ? (data_get($detail, 'keterangan') ?: '-') : '-' }}</td>
                    <td class="text-left">{{ is_array($detail) ? (data_get($detail, 'tindakan_koreksi') ?: '-') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
        <span>{{ $kode_form ?? '-' }}</span>
    </div>

    {{-- ===== TANDA TANGAN ===== --}}
    @php
        $firstItem = $data->first();
        $qcApprover = null; $qcApproverName = 'QC Inspector';
        if (!empty($firstItem?->approved_by_qc_user_id)) {
            $qcApprover = \App\Models\User::find($firstItem->approved_by_qc_user_id);
            if($qcApprover) $qcApproverName = $qcApprover->name;
        }
        $produksiApprover = null; $produksiApproverName = 'SPV/Foreman/Forelady Produksi';
        if (!empty($firstItem?->approved_by_produksi_user_id)) {
            $produksiApprover = \App\Models\User::find($firstItem->approved_by_produksi_user_id);
            if($produksiApprover) $produksiApproverName = $produksiApprover->name;
        }
        $spvApprover = null; $spvApproverName = 'Supervisor QC';
        if (!empty($firstItem?->approved_by_spv_user_id)) {
            $spvApprover = \App\Models\User::find($firstItem->approved_by_spv_user_id);
            if($spvApprover) $spvApproverName = $spvApprover->name;
        }
        $base64QcSvg = null;
        if($qcApprover && $firstItem?->approved_by_qc) {
            $qcApprovalDate = $firstItem->approved_by_qc_at ? $firstItem->approved_by_qc_at->format('Y-m-d') : '';
            $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}"));
        }
        $base64ProduksiSvg = null;
        if($produksiApprover && $firstItem?->approved_by_produksi) {
            $produksiApprovalDate = $firstItem->approved_by_produksi_at ? $firstItem->approved_by_produksi_at->format('Y-m-d') : '';
            $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$produksiApprover->name} (FM/FL PRODUKSI) pada {$produksiApprovalDate}"));
        }
        $base64SpvSvg = null;
        if($spvApprover && $firstItem?->approved_by_spv) {
            $spvApprovalDate = $firstItem->approved_by_spv_at ? $firstItem->approved_by_spv_at->format('Y-m-d') : '';
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
</body>
</html>
