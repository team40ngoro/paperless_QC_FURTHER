<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Barang Mudah Pecah</title>
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
        .status-ok {
            color: #28a745;
            font-weight: bold;
        }
        .status-not-ok {
            color: #dc3545;
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
            <img src="{{ public_path('dist/img/cpi-logo.png') }}" alt="CPI Logo" style="width: 60px; height: 60px; object-fit: contain;">
        </div>
        <div class="company-info">
            <div class="company-name">PT. CHAROEN POKPHAND INDONESIA</div>
            <div>FOOD DIVISION {{ strtoupper(auth()->user()->plan->nama_plan) }}</div>
            <div>{{ strtoupper(auth()->user()->plan->nama_plan) }} - INDONESIA</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="title">VERIFIKASI KONDISI BARANG MUDAH PECAH</div>

    @if($data->count() > 0)
        @php
            $firstItem = $data->first();
            
            // Flatten all items from both old row format and new JSON format
            $allFlattenedItems = collect();
            foreach($data as $record) {
                if ($record->items_json) {
                    foreach($record->items_json as $jsonRow) {
                        $area = \App\Models\InputArea::find($jsonRow['id_area']);
                        $allFlattenedItems->push((object)[
                            'id_area' => $jsonRow['id_area'],
                            'area_name' => $area->area ?? 'Area Tidak Diketahui',
                            'nama_barang' => $jsonRow['nama_barang'] ?? '-',
                            'jumlah' => $jsonRow['jumlah'] ?? '-',
                            'kondisi' => $jsonRow['kondisi'] ?? '-',
                            'temuan' => $jsonRow['temuan_ketidaksesuaian'] ?? '',
                            'tindakan' => $jsonRow['tindakan_koreksi'] ?? '',
                            'verifikasi_hasil' => $jsonRow['verifikasi_hasil'] ?? null,
                            'is_manual' => false,
                            'nama_karyawan' => null
                        ]);
                    }
                } else {
                    $allFlattenedItems->push((object)[
                        'id_area' => $record->id_area,
                        'area_name' => $record->area->area ?? 'Area Tidak Diketahui',
                        'nama_barang' => $record->is_manual ? ($record->nama_barang_manual ?? '-') : ($record->namaBarang->nama_barang ?? '-'),
                        'jumlah' => $record->jumlah ?? '-',
                        'kondisi' => $record->kondisi ?? '-',
                        'temuan' => $record->temuan_ketidaksesuaian ?? '',
                        'tindakan' => $record->tindakan_koreksi ?? '',
                        'verifikasi_hasil' => $record->verifikasi_hasil ?? null,
                        'is_manual' => $record->is_manual,
                        'nama_karyawan' => $record->is_manual ? $record->nama_karyawan : null
                    ]);
                }
            }

            $groupedByArea = $allFlattenedItems->groupBy('id_area');
            $no = 1;
        @endphp

        <div class="form-info">
            <table>
                <tr>
                    <td>Hari / Tanggal</td>
                    <td colspan="3">: {{ $firstItem->tanggal ? $firstItem->tanggal->format('l, d-m-Y') : '-' }}</td>
                </tr>
                <tr>
                    <td>Shift</td>
                    <td colspan="3">: {{ $firstItem->shift->shift ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Nama Barang</th>
                    <th style="width: 8%;">Jumlah</th>
                    <th style="width: 8%;">Kondisi</th>
                    <th style="width: 18%;">Temuan Ketidaksesuaian</th>
                    <th style="width: 18%;">Tindakan Koreksi</th>
                    <th style="width: 18%;">Verifikasi Hasil</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedByArea as $areaId => $items)
                    <tr>
                        <td colspan="7" style="text-align: left; font-weight: bold; background-color: #e9ecef;">
                            {{ $items->first()->area_name }}
                        </td>
                    </tr>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td style="text-align: left;">
                                {{ $item->nama_barang }}
                                @if($item->nama_karyawan)
                                    <div style="font-size: 7px; color: #666;">(Karyawan: {{ $item->nama_karyawan }})</div>
                                @endif
                            </td>
                            <td>{{ $item->jumlah }}</td>
                            <td>
                                @if($item->kondisi == 'OK')
                                    <span class="status-ok">OK</span>
                                @elseif($item->kondisi == 'Tidak OK')
                                    <span class="status-not-ok">Tidak OK</span>
                                @else
                                    {{ $item->kondisi }}
                                @endif
                            </td>
                            <td style="text-align: left;">
                                {{ $item->temuan ?: '-' }}
                            </td>
                            <td style="text-align: left;">
                                {{ $item->tindakan ?: '-' }}
                            </td>
                            <td>
                                @if($item->verifikasi_hasil == 'OK')
                                    <span class="status-ok">OK</span>
                                @elseif($item->verifikasi_hasil == 'Tidak OK')
                                    <span class="status-not-ok">Tidak OK</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
         <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
            <span>{{ $kode_form ?? $firstItem->kode_form ?? '-' }}</span>
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
                $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}"));
            }
            $base64SpvSvg = null;
            if($spvApprover) {
                $spvApprovalDate = '';
                foreach($data as $item) { if($item->approved_by_spv && $item->spv_approved_at) { $spvApprovalDate = $item->spv_approved_at->format('Y-m-d'); break; } }
                $base64SpvSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvApprover->name} (SPV) pada {$spvApprovalDate}"));
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
        <div style="text-align: center; padding: 20px;">
            <p>Tidak ada data yang sesuai dengan filter yang dipilih.</p>
        </div>
    @endif
</body>
</html>
