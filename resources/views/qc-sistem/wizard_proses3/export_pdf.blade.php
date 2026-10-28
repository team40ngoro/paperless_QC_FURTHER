<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Wizard Proses 3 Roasted Product</title>
    <style>
        /* @page margin membuat area kosong ATAS/BAWAH yang benar-benar
           bagian dari kanvas kertas pada SETIAP halaman (bukan cuma
           halaman pertama). Header & footer fixed nanti "ditarik" masuk
           ke area margin ini, jadi tidak akan terpotong tepi kertas. */
        @page {
            margin: 110px 25px 45px 25px; /* atas kanan bawah kiri */
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        /* ===================== HEADER (REPEAT SETIAP HALAMAN) ===================== */
        .header {
            position: fixed;
            top: -110px;     /* sama persis dengan margin atas @page */
            left: -25px;     /* tarik sampai ke tepi kiri kertas */
            right: -25px;    /* tarik sampai ke tepi kanan kertas */
            padding: 10px 25px 10px 25px;
            text-align: center;
            border-bottom: 2px solid #000;
            background: #fff;
        }
        .company-logo { float: left; width: 60px; height: 60px; text-align: center; line-height: 60px; }
        .company-info { margin-left: 80px; text-align: left; }
        .company-name { font-weight: bold; font-size: 12px; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin: 6px 0; text-decoration: underline; }

        /* ===================== FORM INFO / TABEL UTAMA ===================== */
        .form-info { margin-bottom: 8px; }
        .form-info table { width: 100%; border-collapse: collapse; }
        .form-info td { padding: 2px 5px; border: 1px solid #000; font-size: 9px; }

        .main-table { width: 100%; border-collapse: collapse; margin-top: 6px; margin-bottom: 10px; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 3px; text-align: center; font-size: 8px; }
        .main-table th { background-color: #f0f0f0; font-weight: bold; }

        .section-title { font-weight: bold; font-size: 11px; margin-top: 8px; margin-bottom: 3px; color: #000; }

        /* ===================== SATU DATA = SATU HALAMAN ===================== */
        .record {
            page-break-after: always;   /* pindah halaman setelah tiap data */
            /* CATATAN: page-break-inside:avoid sengaja TIDAK dipakai.
               Jika dipakai, dompdf akan mendorong SELURUH blok record
               ke halaman berikutnya kalau tidak muat di sisa halaman
               saat ini -- akibatnya halaman sebelumnya jadi kosong. */
        }
        .record:last-child {
            page-break-after: auto;     /* data terakhir tidak perlu halaman kosong tambahan */
        }

        /* ===================== TABEL TANDA TANGAN (TANPA GARIS KOTAK) ===================== */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: avoid;   /* jangan sampai baris tanda tangan
                                            terpotong: label di 1 halaman,
                                            QR/garis/nama di halaman lain */
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 5px;
            border: none;           /* <-- garis kotak dihilangkan */
        }
        .signature-label {
            font-weight: bold;
            font-size: 9px;
        }
        .signature-qr-area {
            height: 60px;
            vertical-align: middle;
        }
        .qr-code-img {
            width: 55px;
            height: 55px;
        }
        .signature-line {
            border-bottom: 1px solid #000;   /* garis tanda tangan, bukan kotak penuh */
            width: 80%;
            margin: 25px auto 5px auto;
        }
        .signature-name {
            font-size: 9px;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: -45px;   /* sama persis dengan margin bawah @page */
            left: -25px;
            right: -25px;
            padding: 0 25px;
            text-align: right;
            font-size: 8px;
        }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    {{-- HEADER: posisi fixed, otomatis berulang di setiap halaman --}}
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
        <div class="title">VERIFIKASI PROSES ROASTING/STEAMING</div>
    </div>

    @if($data->count() > 0)
        <!-- @php
            $firstItem = $data->first();
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
        </div> -->

        @foreach($data as $pembekuan)
        {{-- Bungkus 1 data dalam div .record supaya 1 data = 1 halaman --}}
        <div class="record">

            <div class="section-title" style="margin-top: 8px; padding-top: 6px;">
                <strong>Data: {{ $pembekuan->hasilProsesRoastingData->produk->nama_produk ?? '-' }} (Kode: {{ $pembekuan->inputRoastingData->kode_produksi ?? '-' }})</strong>
            </div>
            <div class="form-info">
                <table>
                    <tr>
                        <td width="20%">Hari / Tanggal</td>
                        <td width="30%">: {{ \Carbon\Carbon::parse($pembekuan->tanggal)->format('l, d-m-Y') }}</td>
                        <td width="20%">Produk</td>
                        <td width="30%">: {{ $pembekuan->hasilProsesRoastingData->produk->nama_produk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Shift</td>
                        <td>: {{ $pembekuan->shift->shift ?? '-' }}</td>
                        <td>Berat Bahan Baku</td>
                        <td>: {{ $pembekuan->inputRoastingData->berat_produk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Kode Produksi</td>
                        <td>: {{ $pembekuan->inputRoastingData->kode_produksi ?? '-' }}</td>
                        <td>Waktu Pemasakan</td>
                        <td>: {{ $pembekuan->prosesRoastingFanData->waktu_pemasakan ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            {{-- A. INPUT ROASTING (BAHAN BAKU) --}}
            @if($pembekuan->inputRoastingData)
            <div class="section-title">A. INPUT ROASTING (BAHAN BAKU)</div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th>Kode Produksi</th>
                        <th>Berat Bahan Baku</th>
                        <th>Standar Suhu Sebelum Roasting</th>
                        <th>Aktual Suhu Sesudah Roasting</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $pembekuan->inputRoastingData->kode_produksi ?? '-' }}</td>
                        <td>{{ $pembekuan->inputRoastingData->berat_produk ?? '-' }}</td>
                        <td>{{ $pembekuan->inputRoastingData->std_suhu_sebelum ?? '-' }} °C</td>
                        <td>{{ $pembekuan->inputRoastingData->aktual_suhu_sesudah ?? '-' }} °C</td>
                    </tr>
                </tbody>
            </table>
            @endif

            {{-- B. PROSES ROASTING FAN --}}
            @if($pembekuan->prosesRoastingFanData)
            <div class="section-title">B. PROSES ROASTING FAN</div>
            <table class="form-info" style="margin-bottom: 5px;">
                <tr>
                    <td style="width: 45%;"><strong>Waktu Pemasakan (pukul):</strong> {{ $pembekuan->prosesRoastingFanData->aktual_waktu ?? '-' }} </td>
                    <td style="width: 30%;"><strong>Waktu Pengecekan (menit):</strong> {{ $pembekuan->prosesRoastingFanData->waktu_pemasakan ?? '-' }} </td>
                </tr>
            </table>

            <table class="main-table">
                <thead>
                    <tr>
                        <th style="width: 120px; text-align: left;">Parameter</th>
                        @if($pembekuan->prosesRoastingFanData->is_grouped && $pembekuan->prosesRoastingFanData->blok_data)
                            @php
                                $blokData = $pembekuan->prosesRoastingFanData->blok_data;
                                if (is_string($blokData)) {
                                    $blokData = json_decode($blokData, true) ?: [];
                                }
                                $sortedBlok = collect($blokData)->sortBy('block_number');
                            @endphp
                            @foreach($sortedBlok as $blok)
                                <th>Blok {{ $blok['block_number'] ?? ($loop->index + 1) }}</th>
                            @endforeach
                        @else
                            <th>Aktual</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if($pembekuan->prosesRoastingFanData->is_grouped && $pembekuan->prosesRoastingFanData->blok_data)
                        <tr>
                            <td style="text-align: left;">Std Suhu Pemasakan (°C)</td>
                            @foreach($sortedBlok as $blok)
                                @php $stdSuhu = \App\Models\SuhuBlok::find($blok['id_suhu_blok']); @endphp
                                <td>{{ $stdSuhu->suhu_blok ?? '-' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td style="text-align: left;">Suhu Pemasakan (°C)</td>
                            @foreach($sortedBlok as $blok)
                                <td>{{ $blok['suhu_roasting'] ?? '-' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td style="text-align: left;">Std Fan (%)</td>
                            @foreach($sortedBlok as $blok)
                                @php $stdFan = \App\Models\StdFan::find($blok['id_std_fan']); @endphp
                                <td>{{ $stdFan->std_fan ?? '-' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td style="text-align: left;">Fan 1 (%)</td>
                            @foreach($sortedBlok as $blok)
                                <td><strong>{{ $blok['fan_1'] ?? '-' }}</strong></td>
                            @endforeach
                        </tr>
                        <tr>
                            <td style="text-align: left;">Fan 2 (%)</td>
                            @foreach($sortedBlok as $blok)
                                <td><strong>{{ $blok['fan_2'] ?? '-' }}</strong></td>
                            @endforeach
                        </tr>
                        <tr>
                            <td style="text-align: left;">Standar Fan 3 (%)</td>
                            @foreach($sortedBlok as $blok)
                                @php $stdFan = \App\Models\StdFan::find($blok['id_std_fan']); @endphp
                                <td>{{ $stdFan->fan_3 ?? '-' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td style="text-align: left;">Fan 3 (%)</td>
                            @foreach($sortedBlok as $blok)
                                <td><strong>{{ $blok['fan_3'] ?? '-' }}</strong></td>
                            @endforeach
                        </tr>
                        <tr>
                            <td style="text-align: left;">Standar Fan 4 (%)</td>
                            @foreach($sortedBlok as $blok)
                                @php $stdFan = \App\Models\StdFan::find($blok['id_std_fan']); @endphp
                                <td>{{ $stdFan->fan_4 ?? '-' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td style="text-align: left;">Fan 4 (%)</td>
                            @foreach($sortedBlok as $blok)
                                <td><strong>{{ $blok['fan_4'] ?? '-' }}</strong></td>
                            @endforeach
                        </tr>
                        <tr>
                            <td style="text-align: left;">Humidity/Steam (%)</td>
                            @php
                                $pdf3Hum = null; $pdf3Ir = null;
                                foreach($sortedBlok as $b) {
                                    if(in_array($b['block_number'] ?? '', ['INFEED','OUTFEED'])) {
                                        if(is_null($pdf3Hum)) $pdf3Hum = $b['aktual_humadity'] ?? null;
                                        if(is_null($pdf3Ir)) $pdf3Ir = $b['infra_red'] ?? null;
                                    }
                                }
                                $pdf3SkH = false; $pdf3SkI = false;
                            @endphp
                            @foreach($sortedBlok as $blok)
                                @if(($blok['block_number'] ?? '') === 'INFEED')
                                    <td colspan="2">{{ $pdf3Hum ?? '-' }}</td>
                                    @php $pdf3SkH = true; @endphp
                                @elseif(($blok['block_number'] ?? '') === 'OUTFEED' && $pdf3SkH)
                                    {{-- merged --}}
                                @else
                                    <td>{{ $blok['aktual_humadity'] ?? '-' }}</td>
                                @endif
                            @endforeach
                        </tr>
                        <tr>
                            <td style="text-align: left;">Infra Red</td>
                            @foreach($sortedBlok as $blok)
                                @if(($blok['block_number'] ?? '') === 'INFEED')
                                    <td colspan="2">{{ $pdf3Ir ?? '-' }}</td>
                                    @php $pdf3SkI = true; @endphp
                                @elseif(($blok['block_number'] ?? '') === 'OUTFEED' && $pdf3SkI)
                                    {{-- merged --}}
                                @else
                                    <td>{{ $blok['infra_red'] ?? '-' }}</td>
                                @endif
                            @endforeach
                        </tr>
                        <tr>
                            <td style="text-align: left;">Standar Lama Proses (Menit)</td>
                            @php
                                $firstBlok = $sortedBlok->first();
                                $stdLama = $firstBlok ? \App\Models\StdFan::find($firstBlok['id_std_fan']) : null;
                            @endphp
                            <td colspan="{{ count($sortedBlok) }}">{{ $stdLama->std_lama_proses ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Lama Proses (Display Aktual; Menit)</td>
                            <td colspan="{{ count($sortedBlok) }}">{{ $pembekuan->prosesRoastingFanData->aktual_lama_proses ?? '-' }}</td>
                        </tr>
                    @else
                        <tr>
                            <td style="text-align: left;">Suhu Pemasakan (°C)</td>
                            <td>{{ $pembekuan->prosesRoastingFanData->aktual_suhu_fan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Fan (%)</td>
                            <td>{{ $pembekuan->prosesRoastingFanData->aktual_speed_fan ?? '-' }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            @endif

            {{-- C. HASIL PROSES ROASTING --}}
            @if($pembekuan->hasilProsesRoastingData)
            <div class="section-title">C. HASIL PROSES ROASTING</div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th>Standar Suhu Pusat</th>
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
                        <td>{{ $pembekuan->hasilProsesRoastingData->stdSuhuPusat->std_suhu_pusat_roasting ?? '-' }} °C</td>
                        <td>
                            @php
                                $suhuPusatRaw = $pembekuan->hasilProsesRoastingData->aktual_suhu_pusat ?? '';
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
                            {{ !empty($suhuPusatList) ? implode(', ', array_map(fn($v) => $v . ' °C', $suhuPusatList)) : '-' }}
                        </td>
                        <td>{{ ($pembekuan->hasilProsesRoastingData->sensori['sensori_kematangan'] ?? '') == '✔' ? 'OK' : 'Tidak OK' }}</td>
                        <td>{{ ($pembekuan->hasilProsesRoastingData->sensori['sensori_kenampakan'] ?? '') == '✔' ? 'OK' : 'Tidak OK' }}</td>
                        <td>{{ ($pembekuan->hasilProsesRoastingData->sensori['sensori_warna'] ?? '') == '✔' ? 'OK' : 'Tidak OK' }}</td>
                        <td>{{ ($pembekuan->hasilProsesRoastingData->sensori['sensori_rasa'] ?? '') == '✔' ? 'OK' : 'Tidak OK' }}</td>
                        <td>{{ ($pembekuan->hasilProsesRoastingData->sensori['sensori_bau'] ?? '') == '✔' ? 'OK' : 'Tidak OK' }}</td>
                        <td>{{ ($pembekuan->hasilProsesRoastingData->sensori['sensori_tekstur'] ?? '') == '✔' ? 'OK' : 'Tidak OK' }}</td>
                        <td>{{ $pembekuan->hasilProsesRoastingData->keterangan ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
            @endif

            {{-- D. PEMBEKUAN IQF --}}
            <div class="section-title">D. PEMBEKUAN IQF</div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th>Suhu Ruang IQF</th>
                        <th>Holding Time</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $pembekuan->suhu_ruang_iqf ?? '-' }} °C</td>
                        <td>{{ $pembekuan->holding_time ?? '-' }} menit</td>
                    </tr>
                </tbody>
            </table>
            <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
                {{ $kode_form }}
            </div>

            {{-- SIGNATURES WITH QR CODE --}}
            @php
                $qcApprover = null;
                $qcApproverName = 'QC';
                if($pembekuan->approved_by_qc && $pembekuan->qc_approved_by) {
                    $qcApprover = \App\Models\User::find($pembekuan->qc_approved_by);
                    if($qcApprover) { $qcApproverName = $qcApprover->name; }
                }
                $spvQcApprover = null;
                $spvQcApproverName = 'SUPERVISOR QC';
                if($pembekuan->approved_by_spv && $pembekuan->spv_approved_by) {
                    $spvQcApprover = \App\Models\User::find($pembekuan->spv_approved_by);
                    if($spvQcApprover) { $spvQcApproverName = $spvQcApprover->name; }
                }
                $spvProduksiApprover = null;
                $spvProduksiApproverName = 'SPV PRODUKSI';
                if($pembekuan->approved_by_produksi && $pembekuan->produksi_approved_by) {
                    $spvProduksiApprover = \App\Models\User::find($pembekuan->produksi_approved_by);
                    if($spvProduksiApprover) { $spvProduksiApproverName = $spvProduksiApprover->name; }
                }

                $base64QcSvg = null;
                if($qcApprover && $pembekuan->qc_approved_at) {
                    $qcApprovalDate = \Carbon\Carbon::parse($pembekuan->qc_approved_at)->format('Y-m-d');
                    $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (Tim QC) pada {$qcApprovalDate}"));
                }
                $base64SpvQcSvg = null;
                if($spvQcApprover && $pembekuan->spv_approved_at) {
                    $spvQcApprovalDate = \Carbon\Carbon::parse($pembekuan->spv_approved_at)->format('Y-m-d');
                    $base64SpvQcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvQcApprover->name} (SPV QC) pada {$spvQcApprovalDate}"));
                }
                $base64SpvProduksiSvg = null;
                if($spvProduksiApprover && $pembekuan->produksi_approved_at) {
                    $spvProduksiApprovalDate = \Carbon\Carbon::parse($pembekuan->produksi_approved_at)->format('Y-m-d');
                    $base64SpvProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$spvProduksiApprover->name} (SPV PRODUKSI) pada {$spvProduksiApprovalDate}"));
                }
            @endphp

            <table class="signature-table">
                <tr>
                    <td class="signature-label">Dilaporkan Oleh:</td>
                    <td class="signature-label">Diperiksa Oleh:</td>
                    <td class="signature-label">Diketahui Oleh:</td>
                </tr>
                <tr>
                    <td class="signature-qr-area">@if($base64QcSvg)<img src="{{ $base64QcSvg }}" class="qr-code-img" alt="QR QC">@endif</td>
                    <td class="signature-qr-area">@if($base64SpvQcSvg)<img src="{{ $base64SpvQcSvg }}" class="qr-code-img" alt="QR SPV QC">@endif</td>
                    <td class="signature-qr-area">@if($base64SpvProduksiSvg)<img src="{{ $base64SpvProduksiSvg }}" class="qr-code-img" alt="QR SPV Produksi">@endif</td>
                </tr>
                <tr>
                    <td><div class="signature-line"></div></td>
                    <td><div class="signature-line"></div></td>
                    <td><div class="signature-line"></div></td>
                </tr>
                <tr>
                    <td class="signature-name">{{ $qcApproverName }}</td>
                    <td class="signature-name">{{ $spvQcApproverName }}</td>
                    <td class="signature-name">{{ $spvProduksiApproverName }}</td>
                </tr>
            </table>

        </div>{{-- /.record --}}
        @endforeach
    @else
        <div style="text-align: center; padding: 20px;">
            <p>Tidak ada data yang sesuai dengan filter yang dipilih.</p>
        </div>
    @endif
</body>
</html>