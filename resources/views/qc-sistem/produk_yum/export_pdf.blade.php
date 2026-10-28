<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Produk YUM</title>
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
            padding: 2px 1px;
            text-align: center;
            font-size: 7px;
            vertical-align: middle;
            word-wrap: break-word;
        }
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .main-table td:nth-child(1),
        .main-table td:nth-child(2) {
            font-size: 6px;
            text-align: left;
            padding-left: 3px;
        }
        .main-table tr {
            height: 25px;
        }
        .main-table thead tr {
            height: 20px;
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
        .data-arrays {
            font-size: 7px;
        }
        .array-item {
            display: inline-block;
            margin-right: 2px;
            padding: 1px 2px;
            background-color: #e9ecef;
            border-radius: 2px;
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

    <div class="title">VERIFIKASI BERAT DAN ISI PRODUK YUM!</div>

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
            <td>Jam</td>
            <td colspan="3">: {{ $firstItem->jam ? \Carbon\Carbon::parse($firstItem->jam)->format('H:i') : '-' }}</td>
        </tr>
                <tr>
                    <td>Shift</td>
                    <td colspan="3">: {{ $firstItem->shift->shift ?? '-' }}</td>
                </tr>
              
            </table>
        </div>

        {{-- Informasi Produk --}}
        <div style="margin-bottom: 15px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; width: 15%; font-weight: bold;">Nama Produk</td>
                    <td style="border: 1px solid #000; padding: 5px; width: 85%;">
                        @if($data->count() > 0)
                            {{ $data->first()->produk->nama_produk ?? '-' }}
                        @else
                            &nbsp;
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; width: 15%; font-weight: bold;">Kode Produksi</td>
                    <td style="border: 1px solid #000; padding: 5px; width: 85%;">
                        @if($data->count() > 0)
                            {{ $data->first()->kode_produksi ?? '-' }}
                        @else
                            &nbsp;
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; width: 15%; font-weight: bold;">Best Before</td>
                    <td style="border: 1px solid #000; padding: 5px; width: 85%;">
                        @if($data->count() > 0)
                            {{ $data->first()->kode_exp ?? '-' }}
                        @else
                            &nbsp;
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 15%;">Pack</th>
                    <th colspan="20" style="width: 85%;">1-20</th>
                </tr>
                <tr>
                    @for($i = 1; $i <= 20; $i++)
                        <th style="width: 4.25%;">{{ $i }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    {{-- Baris kosong untuk Pack --}}
                    @for($emptyRow = 0; $emptyRow < 8; $emptyRow++)
                        <!-- <tr>
                            @if($emptyRow == 0)
                                <td rowspan="8">{{ $item->dataBag->std_bag ?? '-' }}</td>
                            @endif
                            @for($i = 0; $i < 20; $i++)
                                <td>&nbsp;</td>
                            @endfor
                        </tr> -->
                    @endfor
                    
                    {{-- Baris Berat Per PCS - hanya tampilkan jika ada data --}}
                    @if($item->berat_pcs && is_array($item->berat_pcs) && count(array_filter($item->berat_pcs)) > 0)
                        @for($beratRow = 0; $beratRow < 8; $beratRow++)
                            <tr>
                                @if($beratRow == 0)
                                    <td rowspan="8" style="background-color: #f0f0f0;">Berat Per Potong (gram)</td>
                                @endif
                                @php
                                    // Data berat_pcs untuk baris ini
                                    $beratPcsData = [];
                                    if($item->berat_pcs && is_array($item->berat_pcs)) {
                                        $startIndex = $beratRow * 20;
                                        $beratPcsData = array_slice($item->berat_pcs, $startIndex, 20);
                                    }
                                    $beratPcsData = array_pad($beratPcsData, 20, '');
                                @endphp
                                @for($i = 0; $i < 20; $i++)
                                    <td>{{ $beratPcsData[$i] ?? '' }}</td>
                                @endfor
                            </tr>
                        @endfor
                    @endif
                    
                    {{-- Baris Jumlah (pcs) --}}
                    <tr>
                        <td>Jumlah Isi per Pack (pcs)</td>
                        @php
                            // Data jumlah_pcs
                            $jumlahPcsData = [];
                            if($item->jumlah_pcs && is_array($item->jumlah_pcs)) {
                                $jumlahPcsData = $item->jumlah_pcs;
                            }
                            $jumlahPcsData = array_pad($jumlahPcsData, 20, '');
                        @endphp
                        @for($i = 0; $i < 20; $i++)
                            <td>{{ $jumlahPcsData[$i] ?? '' }}</td>
                        @endfor
                    </tr>
                    
                    {{-- Baris Standar Berat/Pack (Gross) --}}
                    <tr>
                        <td>Standar Berat/Pack (Gross) (gr)</td>
                        <td colspan="20" style="text-align: center;">{{ $item->dataBag->std_bag ?? '' }}</td>
                    </tr>
                    
                    {{-- Baris Aktual Berat/Pack (Gross) --}}
                    <tr>
                        <td>Aktual Berat Per Pack (gram)</td>
                        @php
                            // Data aktual_berat
                            $aktualBeratData = [];
                            if($item->aktual_berat && is_array($item->aktual_berat)) {
                                $aktualBeratData = $item->aktual_berat;
                            }
                            $aktualBeratData = array_pad($aktualBeratData, 20, '');
                        @endphp
                        @for($i = 0; $i < 20; $i++)
                            <td>{{ $aktualBeratData[$i] ?? '' }}</td>
                        @endfor
                    </tr>
                @endforeach
                
                {{-- Jika tidak ada data atau perlu baris kosong tambahan --}}
                @if($data->count() == 0)
                    {{-- Baris kosong untuk Pack --}}
                    @for($emptyRow = 0; $emptyRow < 8; $emptyRow++)
                        <tr>
                            @if($emptyRow == 0)
                                <td rowspan="8">&nbsp;</td>
                            @endif
                            @for($i = 0; $i < 20; $i++)
                                <td>&nbsp;</td>
                            @endfor
                        </tr>
                    @endfor
                    
                    {{-- Baris Berat Per PCS --}}
                    @for($beratRow = 0; $beratRow < 8; $beratRow++)
                        <tr>
                            @if($beratRow == 0)
                                <td rowspan="8" style="background-color: #f0f0f0;">Berat Per Potong (gram)</td>
                            @endif
                            @for($i = 0; $i < 20; $i++)
                                <td>&nbsp;</td>
                            @endfor
                        </tr>
                    @endfor
                    
                    {{-- Baris Jumlah (pcs) --}}
                    <tr>
                        <td>Jumlah (pcs)</td>
                        @for($i = 0; $i < 20; $i++)
                            <td>&nbsp;</td>
                        @endfor
                    </tr>
                    
                    {{-- Baris Standar Berat/Pack (Gross) --}}
                    <tr>
                        <td>Standar Berat/Pack (Gross) (gr)</td>
                        <td colspan="20" style="text-align: center;">&nbsp;</td>
                    </tr>
                    
                    {{-- Baris Aktual Berat/Pack (Gross) --}}
                    <tr>
                        <td>Aktual Berat Per Pack (gram)</td>
                        @for($i = 0; $i < 20; $i++)
                            <td>&nbsp;</td>
                        @endfor
                    </tr>
                @endif
            </tbody>
        </table>
 <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
            <span>{{ $filterInfo['kode_form'] ?? $firstItem->kode_form ?? '-' }}</span>
        </div>
        {{-- Keterangan --}}
        <div style="margin-top: 15px; margin-bottom: 20px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; width: 15%; font-weight: bold;">Keterangan</td>
                    <td style="border: 1px solid #000; padding: 5px; width: 85%;">:Berat diluar standar langsung di-reject pada saat proses sortir</td>
                </tr>
            </table>
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
            $base64QcSvg = null;
            if($qcApprover) {
                $qcApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_qc && $item->qc_approved_at) { $qcApprovalDate = $item->qc_approved_at->format('Y-m-d'); break; } }
                $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}"));
            }
            $base64ProduksiSvg = null;
            if($produksiApprover) {
                $produksiApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_produksi && $item->produksi_approved_at) { $produksiApprovalDate = $item->produksi_approved_at->format('Y-m-d'); break; } }
                $base64ProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$produksiApprover->name} (FM/FL PRODUKSI) pada {$produksiApprovalDate}"));
            }
            $base64SpvSvg = null;
            if($spvApprover) {
                $spvApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_spv && $item->spv_approved_at) { $spvApprovalDate = $item->spv_approved_at->format('Y-m-d'); break; } }
                $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}"));
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
        <div style="text-align: center; padding: 20px;">
            <p>Tidak ada data yang sesuai dengan filter yang dipilih.</p>
        </div>
    @endif
</body>
</html>
