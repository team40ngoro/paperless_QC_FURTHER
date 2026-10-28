<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pemeriksaan Release Produk Finish Good</title>
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

    <div class="title">Pemeriksaan Release Produk Finish Good</div>

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

    @if($data->count() > 0)
        @php
            $firstItem = $data->first();
        @endphp

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <!-- <th style="width: 7%;">Tanggal</th> -->
                    <th style="width: 5%;">Shift</th>
                    <th style="width: 5%;">Jam</th>
                    <th style="width: 12%;">Nama Produk</th>
                    <th style="width: 6%;">Suhu IQF (°C)</th>
                    <th style="width: 6%;">Suhu Produk (°C)</th>
                    <th style="width: 6%;">Lama Pembekuan (Mnt)</th>
                    <th style="width: 5%;">Jumlah Box</th>
                    <th style="width: 8%;">Berat Packing Sekunder</th>
                    <th style="width: 8%;">Kondisi Kemasan Primer</th>
                    <th style="width: 4%;">Status</th>
                    <th style="width: 10%;">Dok. Primer</th>
                    <th style="width: 10%;">Dok. Sekunder</th>
                    <th style="width: 10%;">Keterangan</th>
                    <th style="width: 10%;">Tindakan Koreksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <!-- <td>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') : '-' }}</td> -->
                        <td>{{ $item->shift->shift ?? '-' }}</td>
                        <td>{{ $item->jam ?? '-' }}</td>
                        <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                        <td>{{ $item->suhu_iqf }}</td>
                        <td>{{ $item->suhu_produk }}</td>
                        <td>{{ $item->lama_pembekuan }}</td>
                        <td>{{ $item->jumlah_box }}</td>
                        <td>{{ is_array($item->berat_packing_sekunder) ? implode(', ', $item->berat_packing_sekunder) : ($item->berat_packing_sekunder ?? '-') }}</td>
                        <td>{{ $item->kondisi_kemasan_primer ?? '-' }}</td>
                        <td>{{ $item->status }}</td>
                        <td>
                            @if($item->foto_kemasan_primer)
                                <img src="{{ public_path('storage/' . $item->foto_kemasan_primer) }}" style="max-height: 40px; max-width: 40px; object-fit: contain;" alt="Primer">
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($item->foto_kemasan_sekunder)
                                <img src="{{ public_path('storage/' . $item->foto_kemasan_sekunder) }}" style="max-height: 40px; max-width: 40px; object-fit: contain;" alt="Sekunder">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td>{{ $item->tindakan_koreksi ?? '-' }}</td>
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

            $produksiApprover = null;
            $produksiApproverName = 'PRODUKSI';
            foreach($data as $item) {
                if($item->approved_by_produksi && $item->produksi_approved_by) {
                    $produksiApprover = \App\Models\User::find($item->produksi_approved_by);
                    if($produksiApprover) { $produksiApproverName = $produksiApprover->name; break; }
                }
            }

            $base64QcSvg = null;
            if($qcApprover) {
                $qcApprovalDate = '';
                foreach($data as $item) {
                    if($item->approved_by_qc && $item->qc_approved_at) {
                        $qcApprovalDate = is_string($item->qc_approved_at) ? \Carbon\Carbon::parse($item->qc_approved_at)->format('Y-m-d') : $item->qc_approved_at->format('Y-m-d'); break;
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
                        $spvApprovalDate = is_string($item->spv_approved_at) ? \Carbon\Carbon::parse($item->spv_approved_at)->format('Y-m-d') : $item->spv_approved_at->format('Y-m-d'); break;
                    }
                }
                $spvQrData = "Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}";
                $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate($spvQrData));
            }

            $base64ProduksiSvg = null;
            if($produksiApprover) {
                $produksiApprovalDate = '';
                foreach($data as $item) {
                    if($item->approved_by_produksi && $item->produksi_approved_at) {
                        $produksiApprovalDate = is_string($item->produksi_approved_at) ? \Carbon\Carbon::parse($item->produksi_approved_at)->format('Y-m-d') : $item->produksi_approved_at->format('Y-m-d'); break;
                    }
                }
                $produksiQrData = "Dokumen ini telah diverifikasi secara sistem oleh {$produksiApprover->name} (PRODUKSI) pada {$produksiApprovalDate}";
                $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate($produksiQrData));
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
                <td class="signature-name">{{ $qcApproverName }}</td>
                <td class="signature-name">{{ $produksiApproverName }}</td>
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
