<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses Produksi - Wizard Proses 3 (Roasting)</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .company-logo { float: left; width: 60px; height: 60px; text-align: center; line-height: 60px; }
        .company-info { margin-left: 80px; text-align: left; }
        .company-name { font-weight: bold; font-size: 12px; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin: 20px 0; text-decoration: underline; }
        .form-info { margin-bottom: 15px; }
        .form-info table { width: 100%; border-collapse: collapse; }
        .form-info td { padding: 3px 5px; border: 1px solid #000; font-size: 9px; }
        .main-table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 4px; text-align: center; font-size: 8px; }
        .main-table th { background-color: #f0f0f0; font-weight: bold; }
        .section-title { font-weight: bold; font-size: 11px; margin-top: 15px; margin-bottom: 5px; color: #333; border-left: 3px solid #007bff; padding-left: 5px; }
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .signature-table td { width: 25%; text-align: center; vertical-align: bottom; padding: 5px; border: 1px solid #000; }
        .signature-box { height: 60px; }
        .footer { position: fixed; bottom: 10px; width: 100%; text-align: right; font-size: 8px; }
        .badge { padding: 2px 5px; border-radius: 3px; border: 1px solid #000; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    @php
        $items = isset($data) && $data instanceof \Illuminate\Support\Collection ? $data : (isset($pembekuan) ? collect([$pembekuan]) : collect());
        $isBulk = isset($data) && $data instanceof \Illuminate\Support\Collection && $data->count() > 1;
    @endphp

    @foreach($items as $index => $pembekuan)
        @if($index > 0)
            <div class="page-break"></div>
        @endif

        <div class="header">
            <div class="company-logo">
                 <img src="{{ public_path('dist/img/cpi-logo.png') }}" alt="CPI Logo" style="width: 60px; height: 60px; object-fit: contain;">
            </div>
            <div class="company-info">
                <div class="company-name">PT. CHAROEN POKPHAND INDONESIA</div>
                <div>FOOD DIVISION {{ strtoupper($pembekuan->plan->nama_plan ?? auth()->user()->plan->nama_plan) }}</div>
                <div>{{ strtoupper($pembekuan->plan->nama_plan ?? auth()->user()->plan->nama_plan) }} - INDONESIA</div>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="title">VERIFIKASI PROSES PENGGORENGAN -> ROASTING</div>

        <div class="form-info">
            <table>
                <tr>
                    <td width="20%">Hari / Tanggal</td>
                    <td width="30%">: {{ \Carbon\Carbon::parse($pembekuan->tanggal)->format('l, d-m-Y') }}</td>
                    <td width="20%">Produk</td>
                    <td width="30%">: {{ $pembekuan->penggorenganData->produk->nama_produk ?? ($pembekuan->prosesRoastingFanData->produk->nama_produk ?? ($pembekuan->hasilProsesRoastingData->produk->nama_produk ?? '-')) }}</td>
                </tr>
                <tr>
                    <td>Shift</td>
                    <td>: {{ $pembekuan->penggorenganData->shift->shift ?? ($pembekuan->hasilProsesRoastingData->shift->shift ?? '-') }}</td>
                    <td>Batch / Kode</td>
                    <td>:
                        @php
                            $batchCode = $pembekuan->hasilProsesRoastingData->inputRoasting->kode_produksi ??
                                         ($pembekuan->prosesRoastingFanData->inputRoasting->kode_produksi ??
                                         ($pembekuan->penggorenganData->kode_produksi ?? '-'));
                        @endphp
                        {{ $batchCode }}
                    </td>
                </tr>
                <tr>
                    <td>Pukul</td>
                    <td>: {{ $pembekuan->penggorenganData->waktu_pemasakan ?? '-' }}</td>
                    <td>Kode Produksi</td>
                    <td>: {{ $pembekuan->penggorenganData->kode_produksi ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- A. PENGGORENGAN --}}
        @if($pembekuan->penggorenganData)
        <div class="section-title">A. PENGGORENGAN</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th>No Of Strokes</th>
                    <th>Hasil Pencetakan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $pembekuan->penggorenganData->no_of_strokes }}</td>
                    <td>{{ $pembekuan->penggorenganData->hasil_pencetakan == '✔' ? 'OK' : ($pembekuan->penggorenganData->hasil_pencetakan == '✘' ? 'Tidak OK' : $pembekuan->penggorenganData->hasil_pencetakan) }}</td>
                </tr>
            </tbody>
        </table>
        @endif

        {{-- B. PREDUST --}}
        @if($pembekuan->predustData)
        <div class="section-title">B. PREDUSTING</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th>Kode Produksi</th>
                    <th>Jenis Predust</th>
                    <th>Hasil Predust</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $pembekuan->predustData->kode_produksi }}</td>
                    <td>{{ $pembekuan->predustData->jenisPredust->jenis_predust ?? '-' }}</td>
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
                    <td style="text-align: left;">{!! nl2br(e(implode("\n", array_map(function($k) { return "- $k"; }, explode(", ", $pembekuan->batteringData->jenis_better_names ?? ($pembekuan->batteringData->jenis_better->nama_better ?? '-')))))) !!}</td>
                    <td>{{ $pembekuan->batteringData->hasil_better == '✔' ? 'OK' : ($pembekuan->batteringData->hasil_better == '✘' ? 'Tidak OK' : $pembekuan->batteringData->hasil_better) }}</td>
                </tr>
            </tbody>
        </table>
        @endif

        {{-- D. BREADER --}}
        @if($pembekuan->breaderData)
        <div class="section-title">D. BREADERING</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th>Kode Produksi</th>
                    <th>Hasil Breader</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: left;">{!! $pembekuan->breaderData->breader_names_with_codes_html !!}</td>
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
                    <th>Suhu Std</th>
                    <th>Aktual Suhu</th>
                    <th>Waktu Std</th>
                    <th>Aktual Waktu</th>
                    <th>TPM Minyak</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $frayerData = $pembekuan->frayer_list_data;
                @endphp
                @foreach($frayerData as $fData)
                    @php
                        $className = class_basename($fData);
                        $label = 'Fryer';
                        $suhuVal = '-';
                        $waktuVal = '-';

                        if ($className == 'ProsesFrayer') {
                            $label = 'Fryer 1';
                            $suhuVal = $fData->suhuFrayer->suhu_frayer ?? ($fData->suhuFrayer->suhu ?? '-');
                            if (isset($fData->waktuPenggorengan)) {
                                $w = $fData->waktuPenggorengan;
                                $waktuVal = $w->waktu_penggorengan_1 ?? ($w->waktu_penggorengan ?? '-');
                            } elseif (isset($fData->suhuFrayer)) {
                                $waktuVal = $fData->suhuFrayer->waktu_penggorengan_1 ?? '-';
                            }
                        } elseif ($className == 'Frayer2') {
                            $label = 'Fryer 2';
                            $suhuVal = $fData->suhuFrayer->suhu_frayer_2 ?? ($fData->suhuFrayer2->suhu_frayer_2 ?? '-');
                            if (isset($fData->waktuPenggorengan)) {
                                $w = $fData->waktuPenggorengan;
                                $waktuVal = $w->waktu_penggorengan_2 ?? ($w->waktu_penggorengan ?? '-');
                            } elseif (isset($fData->waktuPenggorengan2)) {
                                $waktuVal = $fData->waktuPenggorengan2->waktu_penggorengan_2 ?? ($fData->waktuPenggorengan2->waktu_penggorengan ?? '-');
                            } elseif (isset($fData->suhuFrayer)) {
                                $waktuVal = $fData->suhuFrayer->waktu_penggorengan_2 ?? '-';
                            }
                        } elseif ($className == 'Frayer3') {
                            $label = 'Fryer 3';
                            $suhuVal = $fData->suhuFrayer->suhu_frayer_3 ?? '-';
                            if (isset($fData->waktuPenggorengan)) {
                                $w = $fData->waktuPenggorengan;
                                $waktuVal = $w->waktu_penggorengan_3 ?? ($w->waktu_penggorengan ?? '-');
                            } elseif (isset($fData->suhuFrayer)) {
                                $waktuVal = $fData->suhuFrayer->waktu_penggorengan_3 ?? '-';
                            }
                        } elseif ($className == 'Frayer4') {
                            $label = 'Fryer 4';
                            $suhuVal = $fData->suhuFrayer->suhu_frayer_4 ?? '-';
                            if (isset($fData->waktuPenggorengan)) {
                                $w = $fData->waktuPenggorengan;
                                $waktuVal = $w->waktu_penggorengan_4 ?? ($w->waktu_penggorengan ?? '-');
                            } elseif (isset($fData->suhuFrayer)) {
                                $waktuVal = $fData->suhuFrayer->waktu_penggorengan_4 ?? '-';
                            }
                        } elseif ($className == 'Frayer5') {
                            $label = 'Fryer 5';
                            $suhuVal = $fData->suhuFrayer->suhu_frayer_5 ?? '-';
                            if (isset($fData->waktuPenggorengan)) {
                                $w = $fData->waktuPenggorengan;
                                $waktuVal = $w->waktu_penggorengan_5 ?? ($w->waktu_penggorengan ?? '-');
                            } elseif (isset($fData->suhuFrayer)) {
                                $waktuVal = $fData->suhuFrayer->waktu_penggorengan_5 ?? '-';
                            }
                        }

                        if ($suhuVal === '' || is_null($suhuVal)) $suhuVal = '-';
                        if ($waktuVal === '' || is_null($waktuVal)) $waktuVal = '-';
                    @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ $suhuVal }} °C</td>
                        <td>{{ $fData->aktual_suhu_penggorengan ?? '-' }} °C</td>
                        <td>{{ $waktuVal }} dtk</td>
                        <td>{{ $fData->aktual_penggorengan }} dtk</td>
                        <td>{{ $fData->tpm_minyak }} %</td>
                        <td>{{ $fData->keterangan ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- F. ROASTING --}}
        <div class="section-title">F. ROASTING</div>
        {{-- LANGKAH 6: ROASTING FAN --}}
        @if($pembekuan->prosesRoastingFanData)
        <div class="section-title">Langkah 6: Roasting Fan</div>
        <table class="form-info" style="margin-bottom: 5px;">
            <tr>
                <td style="width: 45%;"><strong>Waktu Pemasakan (pukul):</strong> {{ $pembekuan->prosesRoastingFanData->aktual_waktu ?? '-' }} </td>
                <td style="width: 30%;"><strong>Waktu Pengecekan (menit):</strong> {{ $pembekuan->prosesRoastingFanData->waktu_pemasakan ?? '-' }} </td>
            </tr>
        </table>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 150px; text-align: left;">Parameter</th>
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
                        <td style="text-align: left;">Standar Suhu Pemasakan (°C)</td>
                        @foreach($sortedBlok as $blok)
                            @php $stdSuhu = \App\Models\SuhuBlok::find($blok['id_suhu_blok']); @endphp
                            <td>{{ $stdSuhu->suhu_blok ?? '-' }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="text-align: left;">Suhu Pemasakan (°C)</td>
                        @foreach($sortedBlok as $blok)
                            <td><strong>{{ $blok['suhu_roasting'] ?? '-' }}</strong></td>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="text-align: left;">Standar Fan 1 (%)</td>
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
                        <td style="text-align: left;">Standar Fan 2 (%)</td>
                        @foreach($sortedBlok as $blok)
                            @php $stdFan = \App\Models\StdFan::find($blok['id_std_fan']); @endphp
                            <td>{{ $stdFan->std_fan_2 ?? '-' }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="text-align: left;">Fan 2 (%)</td>
                        @foreach($sortedBlok as $blok)
                            <td><strong>{{ $blok['fan_2'] ?? '-' }}</strong></td>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="text-align: left;">Standar Humidity (%)</td>
                        @foreach($sortedBlok as $blok)
                            @php $stdFan = \App\Models\StdFan::find($blok['id_std_fan']); @endphp
                            <td>{{ $stdFan->std_humadity ?? '-' }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="text-align: left;">Humidity/Steam (%)</td>
                        @php
                            $mergedHumPdf = null;
                            $mergedIrPdf = null;
                            foreach($sortedBlok as $b) {
                                if(in_array($b['block_number'] ?? '', ['INFEED','OUTFEED'])) {
                                    if(is_null($mergedHumPdf)) $mergedHumPdf = $b['aktual_humadity'] ?? null;
                                    if(is_null($mergedIrPdf)) $mergedIrPdf = $b['infra_red'] ?? null;
                                }
                            }
                            $pdfSkipOutfeedH = false;
                            $pdfSkipOutfeedI = false;
                        @endphp
                        @foreach($sortedBlok as $blok)
                            @if(($blok['block_number'] ?? '') === 'INFEED')
                                <td colspan="2">{{ $mergedHumPdf ?? '-' }}</td>
                                @php $pdfSkipOutfeedH = true; @endphp
                            @elseif(($blok['block_number'] ?? '') === 'OUTFEED' && $pdfSkipOutfeedH)
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
                                <td colspan="2">{{ $mergedIrPdf ?? '-' }}</td>
                                @php $pdfSkipOutfeedI = true; @endphp
                            @elseif(($blok['block_number'] ?? '') === 'OUTFEED' && $pdfSkipOutfeedI)
                                {{-- merged --}}
                            @else
                                <td>{{ $blok['infra_red'] ?? '-' }}</td>
                            @endif
                        @endforeach
                    </tr>
                    {{-- Baris Standar Lama Proses --}}
                    <tr>
                        <td style="text-align: left;">Standar Lama Proses (Menit)</td>
                        @php
                            $firstBlok = $sortedBlok->first();
                            $stdLama = $firstBlok ? \App\Models\StdFan::find($firstBlok['id_std_fan']) : null;
                        @endphp
                        <td colspan="{{ count($sortedBlok) }}">{{ $stdLama->std_lama_proses ?? '-' }}</td>
                    </tr>
                    {{-- Baris Lama Proses Aktual --}}
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
                        <td style="text-align: left;">Fan 1 (%)</td>
                        <td>{{ $pembekuan->prosesRoastingFanData->aktual_speed_fan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">Humidity/Steam (%)</td>
                        <td>{{ $pembekuan->prosesRoastingFanData->aktual_humadity ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">Infra Red</td>
                        <td>{{ $pembekuan->prosesRoastingFanData->infra_red ?? '-' }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
        @endif

        {{-- LANGKAH 7: HASIL ROASTING --}}
        @if($pembekuan->hasilProsesRoastingData)
        <div class="section-title">Langkah 7: Hasil Roasting</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th>Standar Suhu Pusat</th>
                    <th>Aktual Suhu Pusat</th>
                    <th>Sensori Kematangan</th>
                    <th>Sensori Kenampakan</th>
                    <th>Sensori Warna</th>
                    <th>Sensori Rasa</th>
                    <th>Sensori Bau</th>
                    <th>Sensori Tekstur</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sensori = $pembekuan->hasilProsesRoastingData->sensori;
                    $kematangan = $sensori['sensori_kematangan'] ?? $pembekuan->hasilProsesRoastingData->sensori_kematangan ?? '-';
                    $kenampakan = $sensori['sensori_kenampakan'] ?? $pembekuan->hasilProsesRoastingData->sensori_kenampakan ?? '-';
                    $warna      = $sensori['sensori_warna']      ?? $pembekuan->hasilProsesRoastingData->sensori_warna      ?? '-';
                    $rasa       = $sensori['sensori_rasa']       ?? $pembekuan->hasilProsesRoastingData->sensori_rasa       ?? '-';
                    $bau        = $sensori['sensori_bau']        ?? $pembekuan->hasilProsesRoastingData->sensori_bau        ?? '-';
                    $tekstur    = $sensori['sensori_tekstur']    ?? $pembekuan->hasilProsesRoastingData->sensori_tekstur    ?? '-';
                @endphp
                <tr>
                    <td>{{ $pembekuan->hasilProsesRoastingData->stdSuhuPusat->std_suhu_pusat_roasting ?? '-' }} °C</td>
                    @php
                        $asp = $pembekuan->hasilProsesRoastingData->aktual_suhu_pusat;
                        $suhuPusatList = [];
                        
                        if (is_array($asp)) {
                            foreach ($asp as $val) {
                                if (is_string($val) && $val !== '') {
                                    preg_match_all('/\d+[.,]\d+|\d+/', $val, $matches);
                                    foreach ($matches[0] as $match) {
                                        $suhuPusatList[] = (float) str_replace(',', '.', $match);
                                    }
                                } elseif (is_numeric($val)) {
                                    $suhuPusatList[] = (float) str_replace(',', '.', $val);
                                }
                            }
                        } elseif (is_string($asp) && $asp !== '') {
                            preg_match_all('/\d+[.,]\d+|\d+/', $asp, $matches);
                            $suhuPusatList = array_map(fn($v) => (float) str_replace(',', '.', $v), $matches[0]);
                        }
                        $aspFormatted = count($suhuPusatList) > 0 ? implode(' °C, ', $suhuPusatList) . ' °C' : '-';
                    @endphp
                    <td><strong>{{ $aspFormatted }}</strong></td>
                    <td>{{ in_array(strtoupper($kematangan), ['OK', '✔']) ? 'OK' : 'Tidak OK' }}</td>
                    <td>{{ in_array(strtoupper($kenampakan), ['OK', '✔']) ? 'OK' : 'Tidak OK' }}</td>
                    <td>{{ in_array(strtoupper($warna), ['OK', '✔']) ? 'OK' : 'Tidak OK' }}</td>
                    <td>{{ in_array(strtoupper($rasa), ['OK', '✔']) ? 'OK' : 'Tidak OK' }}</td>
                    <td>{{ in_array(strtoupper($bau), ['OK', '✔']) ? 'OK' : 'Tidak OK' }}</td>
                    <td>{{ in_array(strtoupper($tekstur), ['OK', '✔']) ? 'OK' : 'Tidak OK' }}</td>
                    <td>{{ $pembekuan->hasilProsesRoastingData->keterangan ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
        @endif

        <div style="text-align: right; width: 100%; font-size: 8px; font-weight: bold; margin-bottom: 20px;">
            {{ $kode_form ?? 'QF 10/00' }}
        </div>

        {{-- SIGNATURES WITH QR CODE (4 PARTIES) --}}
        @php
            $creatorName = $pembekuan->user->name ?? 'PRODUKSI';
            $createdDate = $pembekuan->created_at ? \Carbon\Carbon::parse($pembekuan->created_at)->format('Y-m-d') : \Carbon\Carbon::parse($pembekuan->tanggal)->format('Y-m-d');
            $base64CreatorSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah dibuat secara sistem oleh {$creatorName} (Produksi) pada {$createdDate}"));

            $qcApprover = null;
            $qcApproverName = 'QC INSPEKTOR';
            if($pembekuan->approved_by_qc && $pembekuan->qc_approved_by) {
                $qcApprover = \App\Models\User::find($pembekuan->qc_approved_by);
                if($qcApprover) { $qcApproverName = $qcApprover->name; }
            }
            $spvQcApprover = null;
            $spvQcApproverName = 'SPV QC';
            if($pembekuan->approved_by_spv && $pembekuan->spv_approved_by) {
                $spvQcApprover = \App\Models\User::find($pembekuan->spv_approved_by);
                if($spvQcApprover) { $spvQcApproverName = $spvQcApprover->name; }
            }
            $spvProduksiApprover = null;
            $spvProduksiApproverName = 'FOREMAN / SPV PRODUKSI';
            if($pembekuan->approved_by_produksi && $pembekuan->produksi_approved_by) {
                $spvProduksiApprover = \App\Models\User::find($pembekuan->produksi_approved_by);
                if($spvProduksiApprover) { $spvProduksiApproverName = $spvProduksiApprover->name; }
            }

            $base64QcSvg = null;
            if($qcApprover && $pembekuan->qc_approved_at) {
                $qcApprovalDate = \Carbon\Carbon::parse($pembekuan->qc_approved_at)->format('Y-m-d');
                $base64QcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diverifikasi secara sistem oleh {$qcApprover->name} (QC Inspektor) pada {$qcApprovalDate}"));
            }
            $base64SpvQcSvg = null;
            if($spvQcApprover && $pembekuan->spv_approved_at) {
                $spvQcApprovalDate = \Carbon\Carbon::parse($pembekuan->spv_approved_at)->format('Y-m-d');
                $base64SpvQcSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah disetujui secara sistem oleh {$spvQcApprover->name} (SPV QC) pada {$spvQcApprovalDate}"));
            }
            $base64SpvProduksiSvg = null;
            if($spvProduksiApprover && $pembekuan->produksi_approved_at) {
                $spvProduksiApprovalDate = \Carbon\Carbon::parse($pembekuan->produksi_approved_at)->format('Y-m-d');
                $base64SpvProduksiSvg = "data:image/svg+xml;base64," . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(55)->generate("Dokumen ini telah diketahui secara sistem oleh {$spvProduksiApprover->name} (Foreman / SPV Produksi) pada {$spvProduksiApprovalDate}"));
            }
        @endphp

<table class="signature-table">
    <tr>
        <td class="signature-label">1. Dibuat Oleh:<br><strong>PRODUKSI</strong></td>
        <td class="signature-label">2. Diverifikasi Oleh:<br><strong>QC INSPEKTOR</strong></td>
        <td class="signature-label">3. Diketahui Oleh:<br><strong>FOREMAN / SPV PRODUKSI</strong></td>
        <td class="signature-label">4. Disetujui Oleh:<br><strong>SPV QC</strong></td>
    </tr>
    <tr>
        <td class="signature-qr-area">@if($base64CreatorSvg)<img src="{{ $base64CreatorSvg }}" class="qr-code-img" alt="QR Creator">@endif</td>
        <td class="signature-qr-area">@if($base64QcSvg)<img src="{{ $base64QcSvg }}" class="qr-code-img" alt="QR QC">@endif</td>
        <td class="signature-qr-area">@if($base64SpvProduksiSvg)<img src="{{ $base64SpvProduksiSvg }}" class="qr-code-img" alt="QR Produksi">@endif</td>
        <td class="signature-qr-area">@if($base64SpvQcSvg)<img src="{{ $base64SpvQcSvg }}" class="qr-code-img" alt="QR SPV">@endif</td>
    </tr>
    <tr>
        <td><div class="signature-line"></div></td>
        <td><div class="signature-line"></div></td>
        <td><div class="signature-line"></div></td>
        <td><div class="signature-line"></div></td>
    </tr>
    <tr>
        <td class="signature-name">{{ $creatorName }}</td>
        <td class="signature-name">{{ $qcApproverName }}</td>
        <td class="signature-name">{{ $spvProduksiApproverName }}</td>
        <td class="signature-name">{{ $spvQcApproverName }}</td>
    </tr>
</table>
    @endforeach
</body>
</html>