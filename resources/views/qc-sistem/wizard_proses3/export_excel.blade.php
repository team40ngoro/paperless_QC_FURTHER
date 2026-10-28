<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses 3</title>
</head>
<body>
@php
    // Style inline (parser HTML PhpSpreadsheet hanya membaca style attribute)
    $td   = 'border:1px solid #000000; text-align:center; vertical-align:middle; font-size:10px;';
    $tdL  = 'border:1px solid #000000; text-align:left; vertical-align:middle; font-size:10px;';
    $lbl  = 'border:1px solid #000000; text-align:left; vertical-align:middle; font-size:10px; font-weight:bold; background-color:#F2F2F2;';
    $th   = 'border:1px solid #000000; text-align:center; vertical-align:middle; font-size:10px; font-weight:bold; background-color:#D9D9D9;';
    $sec  = 'border:1px solid #000000; text-align:left; vertical-align:middle; font-size:11px; font-weight:bold; background-color:#EDEDED;';
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
        <td colspan="9" style="text-align:center; font-size:13px; font-weight:bold;">VERIFIKASI PROSES ROASTING/STEAMING</td>
    </tr>
    <tr><td colspan="9">&nbsp;</td></tr>

@if($data->count() > 0)
    @foreach($data as $pembekuan)
        {{-- IDENTITAS DATA --}}
        <tr>
            <td colspan="9" style="{{ $sec }}">Data: {{ $pembekuan->hasilProsesRoastingData->produk->nama_produk ?? '-' }} (Kode: {{ $pembekuan->inputRoastingData->kode_produksi ?? '-' }})</td>
        </tr>
        <tr>
            <td colspan="2" style="{{ $lbl }}">Hari / Tanggal</td>
            <td colspan="3" style="{{ $tdL }}">: {{ \Carbon\Carbon::parse($pembekuan->tanggal)->format('l, d-m-Y') }}</td>
            <td style="{{ $lbl }}">Produk</td>
            <td colspan="3" style="{{ $tdL }}">: {{ $pembekuan->hasilProsesRoastingData->produk->nama_produk ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="2" style="{{ $lbl }}">Shift</td>
            <td colspan="3" style="{{ $tdL }}">: {{ $pembekuan->shift->shift ?? '-' }}</td>
            <td style="{{ $lbl }}">Berat Bahan Baku</td>
            <td colspan="3" style="{{ $tdL }}">: {{ $pembekuan->inputRoastingData->berat_produk ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="2" style="{{ $lbl }}">Kode Produksi</td>
            <td colspan="3" style="{{ $tdL }}">: {{ $pembekuan->inputRoastingData->kode_produksi ?? '-' }}</td>
            <td style="{{ $lbl }}">Waktu Pemasakan</td>
            <td colspan="3" style="{{ $tdL }}">: {{ $pembekuan->prosesRoastingFanData->waktu_pemasakan ?? '-' }}</td>
        </tr>

        {{-- A. INPUT ROASTING (BAHAN BAKU) --}}
        @if($pembekuan->inputRoastingData)
        <tr><td colspan="9" style="{{ $sec }}">A. INPUT ROASTING (BAHAN BAKU)</td></tr>
        <tr>
            <td colspan="2" style="{{ $th }}">Kode Produksi</td>
            <td colspan="2" style="{{ $th }}">Berat Bahan Baku</td>
            <td colspan="3" style="{{ $th }}">Standar Suhu Sebelum Roasting</td>
            <td colspan="2" style="{{ $th }}">Aktual Suhu Sesudah Roasting</td>
        </tr>
        <tr>
            <td colspan="2" style="{{ $td }}">{{ $pembekuan->inputRoastingData->kode_produksi ?? '-' }}</td>
            <td colspan="2" style="{{ $td }}">{{ $pembekuan->inputRoastingData->berat_produk ?? '-' }}</td>
            <td colspan="3" style="{{ $td }}">{{ $pembekuan->inputRoastingData->std_suhu_sebelum ?? '-' }} °C</td>
            <td colspan="2" style="{{ $td }}">{{ $pembekuan->inputRoastingData->aktual_suhu_sesudah ?? '-' }} °C</td>
        </tr>
        @endif

        {{-- B. PROSES ROASTING FAN --}}
        @if($pembekuan->prosesRoastingFanData)
        <tr><td colspan="9" style="{{ $sec }}">B. PROSES ROASTING FAN</td></tr>
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
                <td colspan="2" style="{{ $tdL }}">Std Suhu Pemasakan (°C)</td>
                @foreach($sortedBlok as $blok)
                    @php $stdSuhu = \App\Models\SuhuBlok::find($blok['id_suhu_blok']); @endphp
                    <td style="{{ $td }}">{{ $stdSuhu->suhu_blok ?? '-' }}</td>
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $td }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Suhu Pemasakan (°C)</td>
                @foreach($sortedBlok as $blok)
                    <td style="{{ $td }}">{{ $blok['suhu_roasting'] ?? '-' }}</td>
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $td }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Std Fan (%)</td>
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
                <td colspan="2" style="{{ $tdL }}">Fan 2 (%)</td>
                @foreach($sortedBlok as $blok)
                    <td style="{{ $td }}"><strong>{{ $blok['fan_2'] ?? '-' }}</strong></td>
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $td }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Standar Fan 3 (%)</td>
                @foreach($sortedBlok as $blok)
                    @php $stdFan = \App\Models\StdFan::find($blok['id_std_fan']); @endphp
                    <td style="{{ $td }}">{{ $stdFan->fan_3 ?? '-' }}</td>
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $td }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Fan 3 (%)</td>
                @foreach($sortedBlok as $blok)
                    <td style="{{ $td }}"><strong>{{ $blok['fan_3'] ?? '-' }}</strong></td>
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $td }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Standar Fan 4 (%)</td>
                @foreach($sortedBlok as $blok)
                    @php $stdFan = \App\Models\StdFan::find($blok['id_std_fan']); @endphp
                    <td style="{{ $td }}">{{ $stdFan->fan_4 ?? '-' }}</td>
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $td }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Fan 4 (%)</td>
                @foreach($sortedBlok as $blok)
                    <td style="{{ $td }}"><strong>{{ $blok['fan_4'] ?? '-' }}</strong></td>
                @endforeach
                @if($padCols > 0)<td colspan="{{ $padCols }}" style="{{ $td }}">&nbsp;</td>@endif
            </tr>
            <tr>
                <td colspan="2" style="{{ $tdL }}">Humidity/Steam (%)</td>
                @php
                    $xls3Hum = null; $xls3Ir = null;
                    foreach($sortedBlok as $b) {
                        if(in_array($b['block_number'] ?? '', ['INFEED','OUTFEED'])) {
                            if(is_null($xls3Hum)) $xls3Hum = $b['aktual_humadity'] ?? null;
                            if(is_null($xls3Ir)) $xls3Ir = $b['infra_red'] ?? null;
                        }
                    }
                    $xls3SkH = false; $xls3SkI = false;
                @endphp
                @foreach($sortedBlok as $blok)
                    @if(($blok['block_number'] ?? '') === 'INFEED')
                        <td colspan="2" style="{{ $td }}">{{ $xls3Hum ?? '-' }}</td>
                        @php $xls3SkH = true; @endphp
                    @elseif(($blok['block_number'] ?? '') === 'OUTFEED' && $xls3SkH)
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
                        <td colspan="2" style="{{ $td }}">{{ $xls3Ir ?? '-' }}</td>
                        @php $xls3SkI = true; @endphp
                    @elseif(($blok['block_number'] ?? '') === 'OUTFEED' && $xls3SkI)
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
                <td colspan="2" style="{{ $tdL }}">Fan (%)</td>
                <td colspan="7">{{ $pembekuan->prosesRoastingFanData->aktual_speed_fan ?? '-' }}</td>
            </tr>
        @endif
        @endif

        {{-- C. HASIL PROSES ROASTING --}}
        @if($pembekuan->hasilProsesRoastingData)
        <tr><td colspan="9" style="{{ $sec }}">C. HASIL PROSES ROASTING</td></tr>
        <tr>
            <td style="{{ $th }}">Standar Suhu Pusat</td>
            <td style="{{ $th }}">Aktual Suhu Pusat</td>
            <td style="{{ $th }}">Kematangan</td>
            <td style="{{ $th }}">Kenampakan</td>
            <td style="{{ $th }}">Warna</td>
            <td style="{{ $th }}">Rasa</td>
            <td style="{{ $th }}">Bau</td>
            <td style="{{ $th }}">Tekstur</td>
            <td style="{{ $th }}">Keterangan</td>
        </tr>
        <tr>
            <td style="{{ $td }}">{{ $pembekuan->hasilProsesRoastingData->stdSuhuPusat->std_suhu_pusat_roasting ?? '-' }} °C</td>
            <td style="{{ $td }}">
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
            <td style="{{ $td }}">{{ ($pembekuan->hasilProsesRoastingData->sensori['sensori_kematangan'] ?? '') == '✔' ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ ($pembekuan->hasilProsesRoastingData->sensori['sensori_kenampakan'] ?? '') == '✔' ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ ($pembekuan->hasilProsesRoastingData->sensori['sensori_warna'] ?? '') == '✔' ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ ($pembekuan->hasilProsesRoastingData->sensori['sensori_rasa'] ?? '') == '✔' ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ ($pembekuan->hasilProsesRoastingData->sensori['sensori_bau'] ?? '') == '✔' ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ ($pembekuan->hasilProsesRoastingData->sensori['sensori_tekstur'] ?? '') == '✔' ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ $pembekuan->hasilProsesRoastingData->keterangan ?? '-' }}</td>
        </tr>
        @endif

        {{-- D. PEMBEKUAN IQF --}}
        <tr><td colspan="9" style="{{ $sec }}">D. PEMBEKUAN IQF</td></tr>
        <tr>
            <td colspan="4" style="{{ $th }}">Suhu Ruang IQF</td>
            <td colspan="5" style="{{ $th }}">Holding Time</td>
        </tr>
        <tr>
            <td colspan="4" style="{{ $td }}">{{ $pembekuan->suhu_ruang_iqf ?? '-' }} °C</td>
            <td colspan="5" style="{{ $td }}">{{ $pembekuan->holding_time ?? '-' }} menit</td>
        </tr>

        {{-- KODE FORM --}}
        <tr>
            <td colspan="9" style="text-align:right; font-size:10px; font-weight:bold;">{{ $kode_form }}</td>
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
