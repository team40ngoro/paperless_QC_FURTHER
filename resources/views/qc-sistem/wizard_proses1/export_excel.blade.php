<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Proses 1</title>
</head>
<body>
@php
    // Style inline (parser HTML PhpSpreadsheet hanya membaca style attribute)
    $td   = 'border:1px solid #000000; text-align:center; vertical-align:middle; font-size:10px;';
    $tdL  = 'border:1px solid #000000; text-align:left; vertical-align:middle; font-size:10px;';
    $lbl  = 'border:1px solid #000000; text-align:left; vertical-align:middle; font-size:10px; font-weight:bold; background-color:#F2F2F2;';
    $th   = 'border:1px solid #000000; text-align:center; vertical-align:middle; font-size:10px; font-weight:bold; background-color:#D9D9D9;';
    $sec  = 'border:1px solid #000000; text-align:left; vertical-align:middle; font-size:11px; font-weight:bold; background-color:#EDEDED;';

    // Tentukan shift display
    $shiftDisplay = $filters['shift_id']
        ? (\App\Models\DataShift::find($filters['shift_id'])->shift ?? 'Unknown')
        : 'Semua Shift';
    $periodeDisplay = \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($filters['tanggal_akhir'])->format('d-m-Y');
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
        <td colspan="9" style="text-align:center; font-size:13px; font-weight:bold;">VERIFIKASI PROSES PENGGORENGAN/PEMASAKAN</td>
    </tr>
    <tr><td colspan="9">&nbsp;</td></tr>

    {{-- INFO PERIODE --}}
    <tr>
        <td colspan="2" style="{{ $lbl }}">Periode Tanggal</td>
        <td colspan="3" style="{{ $tdL }}">: {{ $periodeDisplay }}</td>
        <td style="{{ $lbl }}">Shift</td>
        <td colspan="3" style="{{ $tdL }}">: {{ $shiftDisplay }}</td>
    </tr>
    <tr><td colspan="9">&nbsp;</td></tr>

@if($processedData->count() > 0)
    @foreach($processedData as $pembekuan)
        {{-- Inject frayer data if not already loaded --}}
        @php
            $frayer1 = $pembekuan->frayer1 ?? \App\Models\ProsesFrayer::where('uuid', $pembekuan->frayer_uuid)->with(['produk', 'suhuFrayer', 'waktuPenggorengan'])->first();
            $frayer2 = $pembekuan->frayer2 ?? \App\Models\Frayer2::where('uuid', $pembekuan->frayer2_uuid)->with(['produk', 'suhuFrayer2', 'waktuPenggorengan2'])->first();
            $frayer3 = $pembekuan->frayer3 ?? \App\Models\Frayer3::where('uuid', $pembekuan->frayer3_uuid)->with(['produk', 'suhuFrayer', 'waktuPenggorengan'])->first();
            $frayer4 = $pembekuan->frayer4 ?? \App\Models\Frayer4::where('uuid', $pembekuan->frayer4_uuid)->with(['produk', 'suhuFrayer', 'waktuPenggorengan'])->first();
            $frayer5 = $pembekuan->frayer5 ?? \App\Models\Frayer5::where('uuid', $pembekuan->frayer5_uuid)->with(['produk', 'suhuFrayer', 'waktuPenggorengan'])->first();
        @endphp

        {{-- IDENTITAS DATA --}}
        <tr>
            <td colspan="9" style="{{ $sec }}">Data: {{ $pembekuan->hasilPenggorenganData->produk->nama_produk ?? '-' }} {{ $pembekuan->penggorenganData->berat_produk ? $pembekuan->penggorenganData->berat_produk . ' gram' : '' }} (Kode: {{ $pembekuan->penggorenganData->kode_produksi ?? '-' }})</td>
        </tr>
        <tr>
            <td colspan="2" style="{{ $lbl }}">Hari / Tanggal</td>
            <td colspan="7" style="{{ $tdL }}">: {{ \Carbon\Carbon::parse($pembekuan->tanggal)->format('l, d-m-Y') }}</td>
        </tr>
        <tr>
            <td colspan="2" style="{{ $lbl }}">Shift</td>
            <td colspan="7" style="{{ $tdL }}">: {{ $pembekuan->penggorenganData->shift->shift ?? '-' }}</td>
        </tr>

        {{-- A. PENGGORENGAN --}}
        @if($pembekuan->penggorenganData)
        <tr><td colspan="9" style="{{ $sec }}">A. PENCETAKAN</td></tr>
        <tr>
            <td colspan="3" style="{{ $th }}">Waktu Pemasakan</td>
            <td colspan="3" style="{{ $th }}">No Of Strokes</td>
            <td colspan="3" style="{{ $th }}">Hasil Pencetakan</td>
        </tr>
        <tr>
            <td colspan="3" style="{{ $td }}">{{ $pembekuan->penggorenganData->waktu_pemasakan ?? '-' }}</td>
            <td colspan="3" style="{{ $td }}">{{ $pembekuan->penggorenganData->no_of_strokes }}</td>
            <td colspan="3" style="{{ $td }}">{{ $pembekuan->penggorenganData->hasil_pencetakan == '✔' ? 'OK' : ($pembekuan->penggorenganData->hasil_pencetakan == '✘' ? 'Tidak OK' : $pembekuan->penggorenganData->hasil_pencetakan) }}</td>
        </tr>
        @endif

        {{-- B. PREDUST --}}
        @if($pembekuan->predust_uuid && $pembekuan->predustData)
        <tr><td colspan="9" style="{{ $sec }}">B. PREDUST</td></tr>
        <tr>
            <td colspan="3" style="{{ $th }}">Jenis Predust</td>
            <td colspan="3" style="{{ $th }}">Kode Produksi</td>
            <td colspan="3" style="{{ $th }}">Hasil Predust</td>
        </tr>
        <tr>
            <td colspan="3" style="{{ $td }}">{{ $pembekuan->predustData->jenisPredust->jenis_predust ?? '-' }}</td>
            <td colspan="3" style="{{ $td }}">{{ $pembekuan->predustData->kode_produksi ?? '-' }}</td>
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
            <td colspan="3" style="{{ $td }}">{{ $pembekuan->batteringData->jenis_better_names ?? '-' }}</td>
            <td colspan="3" style="{{ $td }}">{{ $pembekuan->batteringData->hasil_better == '✔' ? 'OK' : ($pembekuan->batteringData->hasil_better == '✘' ? 'Tidak OK' : $pembekuan->batteringData->hasil_better) }}</td>
        </tr>
        @endif

        {{-- D. BREADER --}}
        @if($pembekuan->breaderData)
        <tr><td colspan="9" style="{{ $sec }}">D. BREADER</td></tr>
        <tr>
            <td colspan="3" style="{{ $th }}">Kode Produksi</td>
            <td colspan="3" style="{{ $th }}">Jenis Breader</td>
            <td colspan="3" style="{{ $th }}">Hasil Breader</td>
        </tr>
        <tr>
            <td colspan="3" style="{{ $tdL }}">{!! $pembekuan->breaderData->breader_names_with_codes_html !!}</td>
            <td colspan="3" style="{{ $td }}">
                @if($pembekuan->breaderData->id_jenis_breader)
                    {{ $pembekuan->breaderData->jenis_breader_names }}
                @else
                    N/A
                @endif
            </td>
            <td colspan="3" style="{{ $td }}">{{ $pembekuan->breaderData->hasil_breader == '✔' ? 'OK' : ($pembekuan->breaderData->hasil_breader == '✘' ? 'Tidak OK' : $pembekuan->breaderData->hasil_breader) }}</td>
        </tr>
        @endif

        {{-- E. FRYER 1 - 5 --}}
        <tr><td colspan="9" style="{{ $sec }}">E. FRYER</td></tr>
        <tr>
            <td style="{{ $th }}">Fryer</td>
            <td style="{{ $th }}">Standart Suhu</td>
            <td style="{{ $th }}">Aktual Suhu</td>
            <td style="{{ $th }}">Standart Waktu</td>
            <td style="{{ $th }}">Aktual Waktu</td>
            <td style="{{ $th }}">TPM Minyak</td>
            <td style="{{ $th }}">FFA</td>
            <td colspan="2" style="{{ $th }}">Keterangan</td>
        </tr>
        @foreach(['frayer1', 'frayer2', 'frayer3', 'frayer4', 'frayer5'] as $fKey)
            @php $fData = ${$fKey}; @endphp
            @if($fData)
            <tr>
                <td style="{{ $td }}">Fryer {{ substr($fKey, -1) }}</td>
                <td style="{{ $td }}">
                    @if($fKey == 'frayer1') {{ $fData->suhuFrayer->suhu_frayer ?? $fData->suhuFrayer->suhu_frayer_1 ?? '-' }}
                    @elseif($fKey == 'frayer2') {{ $fData->suhuFrayer2->suhu_frayer_2 ?? $fData->suhuFrayer->suhu_frayer_2 ?? '-' }}
                    @elseif($fKey == 'frayer3') {{ $fData->suhuFrayer->suhu_frayer_3 ?? '-' }}
                    @elseif($fKey == 'frayer4') {{ $fData->suhuFrayer->suhu_frayer_4 ?? '-' }}
                    @elseif($fKey == 'frayer5') {{ $fData->suhuFrayer->suhu_frayer_5 ?? '-' }}
                    @endif °C
                </td>
                <td style="{{ $td }}">{{ $fData->aktual_suhu_penggorengan }} °C</td>
                <td style="{{ $td }}">
                    @if($fKey == 'frayer1') {{ $fData->waktuPenggorengan->waktu_penggorengan ?? '-' }}
                    @elseif($fKey == 'frayer2') {{ $fData->waktuPenggorengan2->waktu_penggorengan_2 ?? $fData->waktuPenggorengan->waktu_penggorengan_2 ?? '-' }}
                    @elseif($fKey == 'frayer3') {{ $fData->waktuPenggorengan->waktu_penggorengan ?? $fData->waktuPenggorengan3->waktu_penggorengan_3 ?? '-' }}
                    @elseif($fKey == 'frayer4') {{ $fData->waktuPenggorengan->waktu_penggorengan ?? $fData->waktuPenggorengan4->waktu_penggorengan_4 ?? '-' }}
                    @elseif($fKey == 'frayer5') {{ $fData->waktuPenggorengan->waktu_penggorengan ?? $fData->waktuPenggorengan5->waktu_penggorengan_5 ?? '-' }}
                    @endif detik
                </td>
                <td style="{{ $td }}">{{ $fData->aktual_penggorengan }} detik</td>
                <td style="{{ $td }}">{{ $fData->tpm_minyak }}</td>
                <td style="{{ $td }}">{{ in_array($fKey, ['frayer1', 'frayer2']) ? ($fData->ffa ?? '-') : '-' }}</td>
                <td colspan="2" style="{{ $td }}">{{ $fData->keterangan ?? '-' }}</td>
            </tr>
            @endif
        @endforeach

        {{-- F. HASIL PENGGORENGAN --}}
        @if($pembekuan->hasilPenggorenganData)
        <tr><td colspan="9" style="{{ $sec }}">F. HASIL PENGGORENGAN</td></tr>
        <tr>
            <td style="{{ $th }}">Std Suhu Pusat</td>
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
            <td style="{{ $td }}">
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
            <td style="{{ $td }}">
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
            <td style="{{ $td }}">{{ $pembekuan->hasilPenggorenganData->sensori_kematangan == '✔' ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ $pembekuan->hasilPenggorenganData->sensori_kenampakan == '✔' ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ $pembekuan->hasilPenggorenganData->sensori_warna == '✔' ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ $pembekuan->hasilPenggorenganData->sensori_rasa == '✔' ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ $pembekuan->hasilPenggorenganData->sensori_bau == '✔' ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ $pembekuan->hasilPenggorenganData->sensori_tekstur == '✔' ? 'OK' : 'Tidak OK' }}</td>
            <td style="{{ $td }}">{{ $pembekuan->hasilPenggorenganData->keterangan ?? '-' }}</td>
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
