<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Wizard Proses 1 Penggorengan</title>
    <style>
        @page {
            margin: 110px 25px 45px 25px; /* top right bottom left */
        }
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
        
        .header {
            position: fixed;
            top: -110px;
            left: -25px;
            right: -25px;
            padding: 10px 25px 10px 25px;
            text-align: center;
            border-bottom: 2px solid #000;
            background: #fff;
        }
        .company-logo { float: left; width: 60px; height: 60px; text-align: center; line-height: 60px; }
        .company-info { margin-left: 80px; text-align: left; }
        .company-name { font-weight: bold; font-size: 12px; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin: 6px 0 0 0; text-decoration: underline; }

        .form-info { margin-bottom: 15px; }
        .form-info table { width: 100%; border-collapse: collapse; }
        .form-info td { padding: 3px 5px; border: 1px solid #000; font-size: 9px; }
        .main-table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 4px; text-align: center; font-size: 8px; }
        .main-table th { background-color: #f0f0f0; font-weight: bold; }
        .section-title { font-weight: bold; font-size: 11px; margin-top: 15px; margin-bottom: 5px; color: #000; }
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .signature-table td { width: 33.33%; text-align: center; vertical-align: bottom; padding: 5px; border: 1px solid #000; }
        .signature-box { height: 60px; }
        .footer { position: fixed; bottom: -45px; left: -25px; right: -25px; padding: 0 25px; text-align: right; font-size: 8px; }
    </style>
</head>
<body>
    {{-- HEADER FIXED: Otomatis berulang di setiap halaman baru PDF --}}
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
        <div class="title">VERIFIKASI PROSES PENGGORENGAN/PEMASAKAN</div>
    </div>

    @if($processedData->count() > 0)
        @php
            $firstItem = $processedData->first();
            // Tentukan shift display
            $shiftDisplay = $filters['shift_id'] 
                ? (\App\Models\DataShift::find($filters['shift_id'])->shift ?? 'Unknown')
                : 'Semua Shift';
            $periodeDisplay = \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($filters['tanggal_akhir'])->format('d-m-Y');
        @endphp

        <div class="form-info">
            <table>
                <tr>
                    <td width="20%">Periode Tanggal</td>
                    <td width="30%">: {{ $periodeDisplay }}</td>
                    <td width="20%">Shift</td>
                    <td width="30%">: {{ $shiftDisplay }}</td>
                </tr>
            </table>
        </div>

        @foreach($processedData as $pembekuan)
            {{-- Inject frayer data if not already loaded --}}
            @php
                $frayer1 = $pembekuan->frayer1 ?? \App\Models\ProsesFrayer::where('uuid', $pembekuan->frayer_uuid)->with(['produk', 'suhuFrayer', 'waktuPenggorengan'])->first();
                $frayer2 = $pembekuan->frayer2 ?? \App\Models\Frayer2::where('uuid', $pembekuan->frayer2_uuid)->with(['produk', 'suhuFrayer2', 'waktuPenggorengan2'])->first();
                $frayer3 = $pembekuan->frayer3 ?? \App\Models\Frayer3::where('uuid', $pembekuan->frayer3_uuid)->with(['produk', 'suhuFrayer', 'waktuPenggorengan'])->first();
                $frayer4 = $pembekuan->frayer4 ?? \App\Models\Frayer4::where('uuid', $pembekuan->frayer4_uuid)->with(['produk', 'suhuFrayer', 'waktuPenggorengan'])->first();
                $frayer5 = $pembekuan->frayer5 ?? \App\Models\Frayer5::where('uuid', $pembekuan->frayer5_uuid)->with(['produk', 'suhuFrayer', 'waktuPenggorengan'])->first();
            @endphp

            <div class="section-title" style="margin-top: 10px; border-top: 1px solid #ccc; padding-top: 10px;">
                <strong>Data: {{ $pembekuan->hasilPenggorenganData->produk->nama_produk ?? '-' }} {{ $pembekuan->penggorenganData->berat_produk ? $pembekuan->penggorenganData->berat_produk . ' gram' : '' }} (Kode: {{ $pembekuan->penggorenganData->kode_produksi ?? '-' }})</strong>
            </div>
            <div class="form-info">
                <table>
                    <tr>
                        <td width="20%">Hari / Tanggal</td>
                        <td width="80%" colspan="3">: {{ \Carbon\Carbon::parse($pembekuan->tanggal)->format('l, d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td>Shift</td>
                        <td colspan="3">: {{ $pembekuan->penggorenganData->shift->shift ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            {{-- A. PENGGORENGAN --}}
            @if($pembekuan->penggorenganData)
            <div class="section-title">A. PENCETAKAN</div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th>Waktu Pemasakan</th>
                        <th>No Of Strokes</th>
                        <th>Hasil Pencetakan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $pembekuan->penggorenganData->waktu_pemasakan ?? '-' }}</td>
                        <td>{{ $pembekuan->penggorenganData->no_of_strokes }}</td>
                        <td>{{ $pembekuan->penggorenganData->hasil_pencetakan == '✔' ? 'OK' : ($pembekuan->penggorenganData->hasil_pencetakan == '✘' ? 'Tidak OK' : $pembekuan->penggorenganData->hasil_pencetakan) }}</td>
                    </tr>
                </tbody>
            </table>
            @endif

            {{-- B. PREDUST --}}
            @if($pembekuan->predust_uuid && $pembekuan->predustData)
            <div class="section-title">B. PREDUST</div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th>Jenis Predust</th>
                        <th>Kode Produksi</th>
                        <th>Hasil Predust</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $pembekuan->predustData->jenisPredust->jenis_predust ?? '-' }}</td>
                        <td>{{ $pembekuan->predustData->kode_produksi ?? '-' }}</td>
                        <td>{{ $pembekuan->predustData->kondisi_predust == '✔' ? 'OK' : ($pembekuan->predustData->kondisi_predust == '✘' ? 'Tidak OK' : $pembekuan->predustData->kondisi_predust) }}</td>
                    </tr>
                </tbody>
            </table>
            @endif

            {{-- C. BATTERING --}}
            @if($pembekuan->batteringData)
            <div class="section-title">C. BATTERING</div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th>Kode Produksi</th>
                        <th>Jenis Better</th>
                        <th>Hasil Better</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: left;">{!! $pembekuan->batteringData->better_names_with_codes_html !!}</td>
                        <td>{{ $pembekuan->batteringData->jenis_better_names ?? '-' }}</td>
                        <td>{{ $pembekuan->batteringData->hasil_better == '✔' ? 'OK' : ($pembekuan->batteringData->hasil_better == '✘' ? 'Tidak OK' : $pembekuan->batteringData->hasil_better) }}</td>
                    </tr>
                </tbody>
            </table>
            @endif

            {{-- D. BREADER --}}
            @if($pembekuan->breaderData)
            <div class="section-title">D. BREADER</div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th>Kode Produksi</th>
                        <th>Jenis Breader</th>
                        <th>Hasil Breader</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: left;">{!! $pembekuan->breaderData->breader_names_with_codes_html !!}</td>
                        <td>
                            @if($pembekuan->breaderData->id_jenis_breader)
                                {{ $pembekuan->breaderData->jenis_breader_names }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $pembekuan->breaderData->hasil_breader == '✔' ? 'OK' : ($pembekuan->breaderData->hasil_breader == '✘' ? 'Tidak OK' : $pembekuan->breaderData->hasil_breader) }}</td>
                    </tr>
                </tbody>
            </table>
            @endif

            {{-- E. FRYER 1 - 5 --}}
            <div class="section-title">E. FRYER</div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th>Fryer</th>
                        <th>Standart Suhu</th>
                        <th>Aktual Suhu</th>
                        <th>Standart Waktu</th>
                        <th>Aktual Waktu</th>
                        <th>TPM Minyak</th>
                        <th>FFA</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['frayer1', 'frayer2', 'frayer3', 'frayer4', 'frayer5'] as $fKey)
                        @php $fData = ${$fKey}; @endphp
                        @if($fData)
                        <tr>
                            <td>Fryer {{ substr($fKey, -1) }}</td>
                            <td>
                                @if($fKey == 'frayer1') {{ $fData->suhuFrayer->suhu_frayer ?? $fData->suhuFrayer->suhu_frayer_1 ?? '-' }}
                                @elseif($fKey == 'frayer2') {{ $fData->suhuFrayer2->suhu_frayer_2 ?? $fData->suhuFrayer->suhu_frayer_2 ?? '-' }}
                                @elseif($fKey == 'frayer3') {{ $fData->suhuFrayer->suhu_frayer_3 ?? '-' }}
                                @elseif($fKey == 'frayer4') {{ $fData->suhuFrayer->suhu_frayer_4 ?? '-' }}
                                @elseif($fKey == 'frayer5') {{ $fData->suhuFrayer->suhu_frayer_5 ?? '-' }}
                                @endif °C
                            </td>
                            <td>{{ $fData->aktual_suhu_penggorengan }} °C</td>
                            <td>
                                @if($fKey == 'frayer1') {{ $fData->waktuPenggorengan->waktu_penggorengan ?? '-' }}
                                @elseif($fKey == 'frayer2') {{ $fData->waktuPenggorengan2->waktu_penggorengan_2 ?? $fData->waktuPenggorengan->waktu_penggorengan_2 ?? '-' }}
                                @elseif($fKey == 'frayer3') {{ $fData->waktuPenggorengan->waktu_penggorengan ?? $fData->waktuPenggorengan3->waktu_penggorengan_3 ?? '-' }}
                                @elseif($fKey == 'frayer4') {{ $fData->waktuPenggorengan->waktu_penggorengan ?? $fData->waktuPenggorengan4->waktu_penggorengan_4 ?? '-' }}
                                @elseif($fKey == 'frayer5') {{ $fData->waktuPenggorengan->waktu_penggorengan ?? $fData->waktuPenggorengan5->waktu_penggorengan_5 ?? '-' }}
                                @endif detik
                            </td>
                            <td>{{ $fData->aktual_penggorengan }} detik</td>
                            <td>{{ $fData->tpm_minyak }}</td>
                            <td>{{ in_array($fKey, ['frayer1', 'frayer2']) ? ($fData->ffa ?? '-') : '-' }}</td>
                            <td>{{ $fData->keterangan ?? '-' }}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            {{-- F. HASIL PENGGORENGAN --}}
            @if($pembekuan->hasilPenggorenganData)
            <div class="section-title">F. HASIL PENGGORENGAN</div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th>Std Suhu Pusat</th>
                        <th>Aktual Suhu Pusat</th>
                        <th>Kematangan</th>
                        <th>Kenampakan</th>
                        <th>Warna</th>
                        <th>Rasa</th>
                        <th>Bau</th>
                        <th>Tekstur</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            @php
                                $stdSuhuArr = $pembekuan->hasilPenggorenganData->stdSuhuPusat->std_suhu_pusat ?? 'N/A';
                                $finalStd = 'N/A';
                                if (is_array($stdSuhuArr)) {
                                    if ($frayer5) $finalStd = $stdSuhuArr[4] ?? 'N/A';
                                    elseif ($frayer4) $finalStd = $stdSuhuArr[3] ?? 'N/A';
                                    elseif ($frayer3) $finalStd = $stdSuhuArr[2] ?? 'N/A';
                                    elseif ($frayer2) $finalStd = $stdSuhuArr[1] ?? 'N/A';
                                    elseif ($frayer1) $finalStd = $stdSuhuArr[0] ?? 'N/A';
                                } else {
                                    $finalStd = $stdSuhuArr;
                                }
                            @endphp
                            {{ $finalStd }} °C
                        </td>
                        <td>
                            @php
                                $suhuPusatRaw = $pembekuan->hasilPenggorenganData->aktual_suhu_pusat ?? '';
                                $suhuPusatList = [];
                                
                                if (is_array($suhuPusatRaw)) {
                                    foreach ($suhuPusatRaw as $val) {
                                        if (is_string($val) && $val !== '') {
                                            preg_match_all('/\d+[.,]\d+|\d+/', $val, $matches);
                                            foreach ($matches[0] as $match) {
                                                $suhuPusatList[] = (float) str_replace(',', '.', $match);
                                            }
                                        } elseif (is_numeric($val)) {
                                            $suhuPusatList[] = (float) str_replace(',', '.', $val);
                                        }
                                    }
                                } elseif (is_string($suhuPusatRaw) && $suhuPusatRaw !== '') {
                                    preg_match_all('/\d+[.,]\d+|\d+/', $suhuPusatRaw, $matches);
                                    $suhuPusatList = array_map(fn($v) => (float) str_replace(',', '.', $v), $matches[0]);
                                }
                            @endphp
                            @if(!empty($suhuPusatList))
                                {!! implode(' °C<br>', $suhuPusatList) !!} °C
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $pembekuan->hasilPenggorenganData->sensori_kematangan == '✔' ? 'OK' : 'Tidak OK' }}</td>
                        <td>{{ $pembekuan->hasilPenggorenganData->sensori_kenampakan == '✔' ? 'OK' : 'Tidak OK' }}</td>
                        <td>{{ $pembekuan->hasilPenggorenganData->sensori_warna == '✔' ? 'OK' : 'Tidak OK' }}</td>
                        <td>{{ $pembekuan->hasilPenggorenganData->sensori_rasa == '✔' ? 'OK' : 'Tidak OK' }}</td>
                        <td>{{ $pembekuan->hasilPenggorenganData->sensori_bau == '✔' ? 'OK' : 'Tidak OK' }}</td>
                        <td>{{ $pembekuan->hasilPenggorenganData->sensori_tekstur == '✔' ? 'OK' : 'Tidak OK' }}</td>
                        <td>{{ $pembekuan->hasilPenggorenganData->keterangan ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
            @endif

            {{-- G. PEMBEKUAN IQF --}}
            <div class="section-title">G. PEMBEKUAN IQF</div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th>Suhu Ruang IQF</th>
                        <th>Holding Time</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $pembekuan->suhu_ruang_iqf }} °C</td>
                        <td>{{ $pembekuan->holding_time }} menit</td>
                    </tr>
                </tbody>
            </table>
            <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
                {{ $kode_form }}
            </div>
            {{-- SIGNATURES WITH QR CODE --}}
            @php
                $approvalData = $pembekuan->_approvalData ?? [];
                $qcApproverName      = $approvalData['qc_name']      ?? 'QC';
                $spvQcApproverName   = $approvalData['spv_name']     ?? 'SUPERVISOR QC';
                $spvProduksiApproverName = $approvalData['produksi_name'] ?? 'SPV PRODUKSI';
                $base64QcSvg         = $approvalData['qc_qr']        ?? null;
                $base64SpvQcSvg      = $approvalData['spv_qr']       ?? null;
                $base64SpvProduksiSvg = $approvalData['produksi_qr'] ?? null;
            @endphp

            <table class="signature-table">
                <tr>
                    <td class="signature-label">Dibuat Oleh: QC INSPEKTOR</td>
                    <td class="signature-label">Diketahui Oleh: Produksi</td>
                    <td class="signature-label">Disetujui Oleh: Supervisor QC</td>
                </tr>
                <tr>
                    <td class="signature-qr-area">@if($base64QcSvg)<img src="{{ $base64QcSvg }}" class="qr-code-img" alt="QR QC">@endif</td>
                    <td class="signature-qr-area">@if($base64SpvProduksiSvg)<img src="{{ $base64SpvProduksiSvg }}" class="qr-code-img" alt="QR Produksi">@endif</td>
                    <td class="signature-qr-area">@if($base64SpvQcSvg)<img src="{{ $base64SpvQcSvg }}" class="qr-code-img" alt="QR SPV">@endif</td>
                </tr>
                <tr>
                    <td><div class="signature-line"></div></td>
                    <td><div class="signature-line"></div></td>
                    <td><div class="signature-line"></div></td>
                </tr>
                <tr>
                    <td class="signature-name">{{ $qcApproverName }}</td>
                    <td class="signature-name">{{ $spvProduksiApproverName }}</td>
                    <td class="signature-name">{{ $spvQcApproverName }}</td>
                </tr>
            </table>
            {{-- PAGE BREAK PER RECORD --}}
            @if(!$loop->last)
            <div style="page-break-after: always;"></div>
            @endif
        @endforeach
        @else
            <div style="text-align: center; padding: 20px;">
                <p>Tidak ada data yang sesuai dengan filter yang dipilih.</p>
            </div>
        @endif
</body>
</html>