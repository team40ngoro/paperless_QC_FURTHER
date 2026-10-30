<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses 2</title>
</head>
<body>
@php
    // Style inline (parser HTML PhpSpreadsheet hanya membaca style attribute)
    $td   = 'border:1px solid #000000; text-align:center; vertical-align:middle; font-size:10px;';
    $tdL  = 'border:1px solid #000000; text-align:left; vertical-align:middle; font-size:10px;';
    $lbl  = 'border:1px solid #000000; text-align:left; vertical-align:middle; font-size:10px; font-weight:bold; background-color:#F2F2F2;';
    $th   = 'border:1px solid #000000; text-align:center; vertical-align:middle; font-size:10px; font-weight:bold; background-color:#D9D9D9;';
    $sec  = 'border:1px solid #000000; text-align:left; vertical-align:middle; font-size:11px; font-weight:bold; background-color:#EDEDED;';

    $items = isset($data) && $data instanceof \Illuminate\Support\Collection ? $data : collect();
@endphp

<table>
    {{-- HEADER PERUSAHAAN --}}
    <tr>
        <td colspan="9" style="text-align:center; font-size:14px; font-weight:bold;">PT. CHAROEN POKPHAND INDONESIA</td>
    </tr>
    <tr>
        <td colspan="9" style="text-align:center; font-size:11px;">FOOD DIVISION {{ strtoupper(auth()->user()->plan->nama_plan) }}</td>
    </tr>
    <tr>
        <td colspan="9" style="text-align:center; font-size:11px;">{{ strtoupper(auth()->user()->plan->nama_plan) }} - INDONESIA</td>
    </tr>
    <tr><td colspan="9">&nbsp;</td></tr>

    {{-- JUDUL --}}
    <tr>
        <td colspan="9" style="text-align:center; font-size:13px; font-weight:bold;">VERIFIKASI PROSES PENGGORENGAN -&gt; ROASTING</td>
    </tr>
    <tr><td colspan="9">&nbsp;</td></tr>

@if($items->count() > 0)
    @foreach($items as $index => $pembekuan)
        {{-- IDENTITAS DATA --}}
        <tr>
            <td colspan="2" style="{{ $lbl }}">Hari / Tanggal</td>
            <td colspan="3" style="{{ $tdL }}">: {{ \Carbon\Carbon::parse($pembekuan->tanggal)->format('l, d-m-Y') }}</td>
            <td style="{{ $lbl }}">Produk</td>
            <td colspan="3" style="{{ $tdL }}">: {{ $pembekuan->hasilProsesRoastingData->produk->nama_produk ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="2" style="{{ $lbl }}">Shift</td>
            <td colspan="3" style="{{ $tdL }}">: {{ $pembekuan->penggorenganData->shift->shift ?? ($pembekuan->hasilProsesRoastingData->shift->shift ?? '-') }}</td>
            <td style="{{ $lbl }}">Batch / Kode</td>
            <td colspan="3" style="{{ $tdL }}">
                @php
                    $batchCode = $pembekuan->hasilProsesRoastingData->inputRoasting->kode_produksi ??
                                 ($pembekuan->prosesRoastingFanData->inputRoasting->kode_produksi ??
                                 ($pembekuan->penggorenganData->kode_produksi ?? '-'));
                @endphp
                : {{ $batchCode }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="{{ $lbl }}">Pukul</td>
            <td colspan="3" style="{{ $tdL }}">: {{ $pembekuan->penggorenganData->waktu_pemasakan ?? '-' }}</td>
            <td style="{{ $lbl }}">Kode Produksi</td>
            <td colspan="3" style="{{ $tdL }}">: {{ $pembekuan->penggorenganData->kode_produksi ?? '-' }}</td>
        </tr>

        {{-- A. PENGGORENGAN --}}
        @if($pembekuan->penggorenganData)
        <tr><td colspan="9" style="{{ $sec }}">A. PENGGORENGAN</td></tr>
        <tr>
            <td colspan="4" style="{{ $th }}">No Of Strokes</td>
            <td colspan="5" style="{{ $th }}">Hasil Pencetakan</td>
        </tr>
        <tr>
            <td colspan="4" style="{{ $td }}">{{ $pembekuan->penggorenganData->no_of_strokes }}</td>
            <td colspan="5" style="{{ $td }}">{{ $pembekuan->penggorenganData->hasil_pencetakan == '✔' ? 'OK' : ($pembekuan->penggorenganData->hasil_pencetakan == '✘' ? 'Tidak OK' : $pembekuan->penggorenganData->hasil_pencetakan) }}</td>
        </tr>
        @endif

        {{-- B. PREDUSTING --}}
        @if($pembekuan->predustData)
        <tr><td colspan="9" style="{{ $sec }}">B. PREDUSTING</td></tr>
        <tr>
            <td colspan="3" style="{{ $th }}">Kode Produksi</td>
            <td colspan="3" style="{{ $th }}">Jenis Predust</td>
            <td colspan="3" style="{{ $th }}">Hasil Predust</td>
        </tr>
        <tr>
            <td colspan="3" style="{{ $td }}">{{ $pembekuan->predustData->kode_produksi }}</td>
            <td colspan="3" style="{{ $td }}">{{ $pembekuan->predustData->jenisPredust->jenis_predust ?? '-' }}</td>
            <td colspan="3" style="{{ $td }}">{{ $pembekuan->predustData->kondisi_predust == '✔' ? 'OK' : ($pembekuan->predustData->kondisi_predust == '✘' ? 'Tidak OK' : $pembekuan->predustData->kondisi_predust) }}</td>
        </tr>
        @endif

        {{-- C. BATTERING --}}
        @if($pembekuan->batteringData)
        <tr><td colspan="9" style="{{ $sec }}">C. BATTERING</td></tr>
        <tr>
            <td colspan="3" style="{{ $th }}">Kode Produksi</td>
            <td colspan="3" style="{{ $th }}">Jenis Better</td>
            <td colspan="3" style="{{ $th }}">Hasil Better</td>
        </tr>
        <tr>
            <td colspan="3" style="{{ $tdL }}">{!! $pembekuan->batteringData->better_names_with_codes_html !!}</td>
            <td colspan="3" style="{{ $tdL }}">{!! nl2br(e(implode("\n", array_map(function($k) { return "- $k"; }, explode(", ", $pembekuan->batteringData->jenis_better_names ?? ($pembekuan->batteringData->jenis_better->nama_better ?? '-')))))) !!}</td>
            <td colspan="3" style="{{ $td }}">{{ $pembekuan->batteringData->hasil_better == '✔' ? 'OK' : ($pembekuan->batteringData->hasil_better == '✘' ? 'Tidak OK' : $pembekuan->batteringData->hasil_better) }}</td>
        </tr>
        @endif

        {{-- D. BREADERING --}}
        @if($pembekuan->breaderData)
        <tr><td colspan="9" style="{{ $sec }}">D. BREADERING</td></tr>
        <tr>
            <td colspan="4" style="{{ $th }}">Kode Produksi</td>
            <td colspan="5" style="{{ $th }}">Hasil Breader</td>
        </tr>
        <tr>
            <td colspan="4" style="{{ $tdL }}">{!! $pembekuan->breaderData->breader_names_with_codes_html !!}</td>
            <td colspan="5" style="{{ $td }}">{{ $pembekuan->breaderData->hasil_breader == '✔' ? 'OK' : ($pembekuan->breaderData->hasil_breader == '✘' ? 'Tidak OK' : $pembekuan->breaderData->hasil_breader) }}</td>
        </tr>
        @endif

        {{-- E. FRYER 1 - 5 --}}
        <tr><td colspan="9" style="{{ $sec }}">E. FRYER</td></tr>
        <tr>
            <td style="{{ $th }}">Fryer</td>
            <td style="{{ $th }}">Suhu Std</td>
            <td style="{{ $th }}">Aktual Suhu</td>
            <td style="{{ $th }}">Waktu Std</td>
            <td style="{{ $th }}">Aktual Waktu</td>
            <td style="{{ $th }}">TPM Minyak</td>
            <td colspan="3" style="{{ $th }}">Keterangan</td>
        </tr>
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
                <td style="{{ $td }}">{{ $label }}</td>
                <td style="{{ $td }}">{{ $suhuVal }} °C</td>
                <td style="{{ $td }}">{{ $fData->aktual_suhu_penggorengan ?? '-' }} °C</td>
                <td style="{{ $td }}">{{ $waktuVal }} dtk</td>
                <td style="{{ $td }}">{{ $fData->aktual_penggorengan }} dtk</td>
                <td style="{{ $td }}">{{ $fData->tpm_minyak }} %</td>
                <td colspan="3" style="{{ $td }}">{{ $fData->keterangan ?? '-' }}</td>
            </tr>
        @endforeach

        {{-- F. ROASTING --}}
        <tr><td colspan="9" style="{{ $sec }}">F. ROASTING</td></tr>

        {{-- LANGKAH 6: ROASTING FAN --}}
        @if($pembekuan->prosesRoastingFanData)
        <tr><td colspan="9" style="{{ $sec }}">Langkah 6: Roasting Fan</td></tr>
        <tr>
            <td colspan="5" style="{{ $tdL }}"><strong>Waktu Pemasakan (pukul):</strong> {{ $pembekuan->prosesRoastingFanData->aktual_waktu ?? '-' }}</td>
            <td colspan="4" style="{{ $tdL }}"><strong>Waktu Pengecekan (menit):</strong> {{ $pembekuan->prosesRoastingFanData->waktu_pemasakan ?? '-' }}</td>
        </tr>

        @if($pembekuan->prosesRoastingFanData->is_grouped && $pembekuan->prosesRoastingFanData->blok_data)
            @php
                $blokData = $pembekuan->prosesRoastingFanData->blok_data;
                if (is_string($blokData)) {
                    $blokData = json_decode($blokData, true) ?: [];
                }
                $sortedBlok = collect($blokData)->sortBy('block_number');
                $padCols = max(0, 7 - $sortedBlok->count());
            @endphp
            <tr>
                <td colspan="2" style="{{ $th }}">Parameter</td>
                @foreach($sortedBlok as $blok)
                    <td style="{{ $th }}">Blok {{ $blok['block_number'] ?? ($loop->index + 1) }}</td>
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $th }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Standar Suhu Pemasakan (°C)</td>
                @foreach($sortedBlok as $blok)
                    @php $stdSuhu = \App\Models\SuhuBlok::find($blok['id_suhu_blok']); @endphp
                    <td style="{{ $td }}">{{ $stdSuhu->suhu_blok ?? '-' }}</td>
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $td }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Suhu Pemasakan (°C)</td>
                @foreach($sortedBlok as $blok)
                    <td style="{{ $td }}"><strong>{{ $blok['suhu_roasting'] ?? '-' }}</strong></td>
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $td }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Standar Fan 1 (%)</td>
                @foreach($sortedBlok as $blok)
                    @php $stdFan = \App\Models\StdFan::find($blok['id_std_fan']); @endphp
                    <td style="{{ $td }}">{{ $stdFan->std_fan ?? '-' }}</td>
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $td }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Fan 1 (%)</td>
                @foreach($sortedBlok as $blok)
                    <td style="{{ $td }}"><strong>{{ $blok['fan_1'] ?? '-' }}</strong></td>
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $td }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Standar Fan 2 (%)</td>
                @foreach($sortedBlok as $blok)
                    @php $stdFan = \App\Models\StdFan::find($blok['id_std_fan']); @endphp
                    <td style="{{ $td }}">{{ $stdFan->std_fan_2 ?? '-' }}</td>
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $td }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Fan 2 (%)</td>
                @foreach($sortedBlok as $blok)
                    <td style="{{ $td }}"><strong>{{ $blok['fan_2'] ?? '-' }}</strong></td>
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $td }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Standar Humidity (%)</td>
                @foreach($sortedBlok as $blok)
                    @php $stdFan = \App\Models\StdFan::find($blok['id_std_fan']); @endphp
                    <td style="{{ $td }}">{{ $stdFan->std_humadity ?? '-' }}</td>
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $td }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Humidity/Steam (%)</td>
                @php
                    $xlsHum = null; $xlsIr = null;
                    foreach($sortedBlok as $b) {
                        if(in_array($b['block_number'] ?? '', ['INFEED','OUTFEED'])) {
                            if(is_null($xlsHum)) $xlsHum = $b['aktual_humadity'] ?? null;
                            if(is_null($xlsIr)) $xlsIr = $b['infra_red'] ?? null;
                        }
                    }
                    $xlsSkipH = false; $xlsSkipI = false;
                @endphp
                @foreach($sortedBlok as $blok)
                    @if(($blok['block_number'] ?? '') === 'INFEED')
                        <td colspan="2" style="{{ $td }}">{{ $xlsHum ?? '-' }}</td>
                        @php $xlsSkipH = true; @endphp
                    @elseif(($blok['block_number'] ?? '') === 'OUTFEED' && $xlsSkipH)
                        {{-- merged --}}
                    @else
                        <td style="{{ $td }}">{{ $blok['aktual_humadity'] ?? '-' }}</td>
                    @endif
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $td }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Infra Red</td>
                @foreach($sortedBlok as $blok)
                    @if(($blok['block_number'] ?? '') === 'INFEED')
                        <td colspan="2" style="{{ $td }}">{{ $xlsIr ?? '-' }}</td>
                        @php $xlsSkipI = true; @endphp
                    @elseif(($blok['block_number'] ?? '') === 'OUTFEED' && $xlsSkipI)
                        {{-- merged --}}
                    @else
                        <td style="{{ $td }}">{{ $blok['infra_red'] ?? '-' }}</td>
                    @endif
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $td }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Standar Lama Proses (Menit)</td>
                @php
                    $firstBlok = $sortedBlok->first();
                    $stdLama = $firstBlok ? \App\Models\StdFan::find($firstBlok['id_std_fan']) : null;
                @endphp
                <td colspan="7">{{ $stdLama->std_lama_proses ?? '-' }}</td>
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Lama Proses (Display Aktual; Menit)</td>
                <td colspan="7">{{ $pembekuan->prosesRoastingFanData->aktual_lama_proses ?? '-' }}</td>
            </tr>
        @else
            <tr>
                <td colspan="2" style="{{ $tdL }}">Suhu Pemasakan (°C)</td>
                <td colspan="7">{{ $pembekuan->prosesRoastingFanData->aktual_suhu_fan ?? '-' }}</td>
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Fan 1 (%)</td>
                <td colspan="7">{{ $pembekuan->prosesRoastingFanData->aktual_speed_fan ?? '-' }}</td>
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Humidity/Steam (%)</td>
                <td colspan="7">{{ $pembekuan->prosesRoastingFanData->aktual_humadity ?? '-' }}</td>
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Infra Red</td>
                <td colspan="7">{{ $pembekuan->prosesRoastingFanData->infra_red ?? '-' }}</td>
            </tr>
        @endif
        @endif

        {{-- LANGKAH 7: HASIL ROASTING --}}
        @if($pembekuan->hasilProsesRoastingData)
        <tr><td colspan="9" style="{{ $sec }}">Langkah 7: Hasil Roasting</td></tr>
        <tr>
            <td style="{{ $th }}">Standar Suhu Pusat</td>
            <td style="{{ $th }}">Aktual Suhu Pusat</td>
            <td style="{{ $th }}">Sensori Kematangan</td>
            <td style="{{ $th }}">Sensori Kenampakan</td>
            <td style="{{ $th }}">Sensori Warna</td>
            <td style="{{ $th }}">Sensori Rasa</td>
            <td style="{{ $th }}">Sensori Bau</td>
            <td style="{{ $th }}">Sensori Tekstur</td>
            <td style="{{ $th }}">Keterangan</td>
        </tr>
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
            <td style="{{ $td }}">{{ $pembekuan->hasilProsesRoastingData->stdSuhuPusat->std_suhu_pusat_roasting ?? '-' }} °C</td>
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
             <td style="{{ $td }}"><strong>{{ $aspFormatted }}</strong></td>
            <td style="{{ $td }}">{{ in_array(strtoupper($kematangan), ['OK', '✔']) ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ in_array(strtoupper($kenampakan), ['OK', '✔']) ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ in_array(strtoupper($warna), ['OK', '✔']) ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ in_array(strtoupper($rasa), ['OK', '✔']) ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ in_array(strtoupper($bau), ['OK', '✔']) ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ in_array(strtoupper($tekstur), ['OK', '✔']) ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ $pembekuan->hasilProsesRoastingData->keterangan ?? '-' }}</td>
        </tr>
        @endif

        {{-- G. PEMBEKUAN IQF --}}
        <tr><td colspan="9" style="{{ $sec }}">G. PEMBEKUAN IQF</td></tr>
        <tr>
            <td colspan="4" style="{{ $th }}">Suhu Ruang IQF</td>
            <td colspan="5" style="{{ $th }}">Holding Time</td>
        </tr>
        <tr>
            <td colspan="4" style="{{ $td }}">{{ $pembekuan->suhu_ruang_iqf }} °C</td>
            <td colspan="5" style="{{ $td }}">{{ $pembekuan->holding_time }} menit</td>
        </tr>

        {{-- KODE FORM --}}
        <tr>
            <td colspan="9" style="text-align:right; font-size:10px; font-weight:bold;">{{ $kode_form ?? 'QF 10/00' }}</td>
        </tr>

        {{-- PEMISAH ANTAR RECORD --}}
        @if(!$loop->last)
        <tr><td colspan="9">&nbsp;</td></tr>
        <tr><td colspan="9">&nbsp;</td></tr>
        @endif
    @endforeach
@else
    <tr>
        <td colspan="9" style="text-align:center; padding:20px; font-size:11px;">Tidak ada data yang sesuai dengan filter yang dipilih.</td>
    </tr>
@endif
</table>
</body>
</html>
