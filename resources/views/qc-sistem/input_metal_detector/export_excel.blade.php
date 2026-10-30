<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pemeriksaan Metal Detector</title>
</head>
<body>
@php
    // Style inline (parser HTML PhpSpreadsheet hanya membaca style attribute)
    $td   = 'border:1px solid #000000; text-align:center; vertical-align:middle; font-size:10px;';
    $tdL  = 'border:1px solid #000000; text-align:left; vertical-align:middle; font-size:10px;';
    $lbl  = 'border:1px solid #000000; text-align:left; vertical-align:middle; font-size:10px; font-weight:bold; background-color:#F2F2F2;';
    $th   = 'border:1px solid #000000; text-align:center; vertical-align:middle; font-size:10px; font-weight:bold; background-color:#D9D9D9;';
    // Warna kelompok uji (sama dengan PDF)
    $feBg   = $th . 'background-color:#D1ECF1;';
    $nfBg   = $th . 'background-color:#D4EDDA;';
    $susBg  = $th . 'background-color:#FFF3CD;';
    $feCell   = $td . 'background-color:#D1ECF1;';
    $nfCell   = $td . 'background-color:#D4EDDA;';
    $susCell  = $td . 'background-color:#FFF3CD;';

    // Konversi nilai cek ✔/✘ (termasuk varian html entity) sama seperti PDF
    $mdCek = function ($val) {
        if ($val == '✔' || $val == '&#10004;') return 'OK';
        if ($val == '✘' || $val == '&#10008;') return 'Tidak OK';
        return $val ?? '-';
    };
@endphp

<table>
    {{-- HEADER PERUSAHAAN --}}
    <tr>
        <td colspan="15" style="text-align:center; font-size:14px; font-weight:bold;">PT. CHAROEN POKPHAND INDONESIA</td>
    </tr>
    <tr>
        <td colspan="15" style="text-align:center; font-size:11px;">FOOD DIVISION {{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }}</td>
    </tr>
    <tr>
        <td colspan="15" style="text-align:center; font-size:11px;">{{ strtoupper(auth()->user()->plan->nama_plan ?? 'UNKNOWN') }} - INDONESIA</td>
    </tr>
    <tr><td colspan="15">&nbsp;</td></tr>

    {{-- JUDUL --}}
    <tr>
        <td colspan="15" style="text-align:center; font-size:13px; font-weight:bold;">PEMERIKSAAN METAL DETECTOR</td>
    </tr>
    <tr><td colspan="15">&nbsp;</td></tr>

    {{-- INFO FILTER --}}
    <tr>
        <td colspan="2" style="{{ $lbl }}">Tanggal</td>
        <td colspan="3" style="{{ $tdL }}">: {{ $filterInfo['tanggal'] }}</td>
        <td colspan="2" style="{{ $lbl }}">Shift</td>
        <td colspan="3" style="{{ $tdL }}">: {{ $filterInfo['shift'] }}</td>
        <td colspan="2" style="{{ $lbl }}">Produk</td>
        <td colspan="3" style="{{ $tdL }}">: {{ $filterInfo['produk'] }}</td>
    </tr>
    <tr>
        <td colspan="2" style="{{ $lbl }}">Line</td>
        <td colspan="3" style="{{ $tdL }}">: {{ $filterInfo['line'] ?? 'Semua Line' }}</td>
        <td colspan="2" style="{{ $lbl }}">Kode Form</td>
        <td colspan="8" style="{{ $tdL }}">: {{ $filterInfo['kode_form'] }}</td>
    </tr>
    <tr><td colspan="15">&nbsp;</td></tr>

@if(count($data) > 0)
    {{-- HEADER TABEL UTAMA --}}
    <tr>
        <td rowspan="2" style="{{ $th }}">No</td>
        <td rowspan="2" style="{{ $th }}">Jam</td>
        <td rowspan="2" style="{{ $th }}">Nama Produk</td>
        <td rowspan="2" style="{{ $th }}">Kode Produksi</td>
        <td rowspan="2" style="{{ $th }}">Line</td>
        <td colspan="3" style="{{ $feBg }}">Fe 1.5 mm</td>
        <td colspan="3" style="{{ $nfBg }}">Non Fe 2 mm</td>
        <td colspan="3" style="{{ $susBg }}">SUS 316 2.5 mm</td>
        <td rowspan="2" style="{{ $th }}">Keterangan</td>
    </tr>
    <tr>
        <td style="{{ $feBg }}">Depan</td>
        <td style="{{ $feBg }}">Tengah</td>
        <td style="{{ $feBg }}">Belakang</td>
        <td style="{{ $nfBg }}">Depan</td>
        <td style="{{ $nfBg }}">Tengah</td>
        <td style="{{ $nfBg }}">Belakang</td>
        <td style="{{ $susBg }}">Depan</td>
        <td style="{{ $susBg }}">Tengah</td>
        <td style="{{ $susBg }}">Belakang</td>
    </tr>
    @foreach($data as $index => $item)
    <tr>
        <td style="{{ $td }}">{{ $index + 1 }}</td>
        <td style="{{ $td }}">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
        <td style="{{ $tdL }}">{{ $item->produk->nama_produk ?? '-' }} {{ $item->berat_produk ?? '-' }}g</td>
        <td style="{{ $td }}">{{ $item->kode_produksi }}</td>
        <td style="{{ $td }}">{{ $item->line ?? '-' }}</td>
        <td style="{{ $feCell }}">{{ $mdCek($item->fe_depan_aktual) }}</td>
        <td style="{{ $feCell }}">{{ $mdCek($item->fe_tengah_aktual) }}</td>
        <td style="{{ $feCell }}">{{ $mdCek($item->fe_belakang_aktual) }}</td>
        <td style="{{ $nfCell }}">{{ $mdCek($item->non_fe_depan_aktual) }}</td>
        <td style="{{ $nfCell }}">{{ $mdCek($item->non_fe_tengah_aktual) }}</td>
        <td style="{{ $nfCell }}">{{ $mdCek($item->non_fe_belakang_aktual) }}</td>
        <td style="{{ $susCell }}">{{ $mdCek($item->sus_depan_aktual) }}</td>
        <td style="{{ $susCell }}">{{ $mdCek($item->sus_tengah_aktual) }}</td>
        <td style="{{ $susCell }}">{{ $mdCek($item->sus_belakang_aktual) }}</td>
        <td style="{{ $tdL }}">{{ $item->keterangan ?? '-' }}</td>
    </tr>
    @endforeach

    {{-- KODE FORM --}}
    <tr><td colspan="15">&nbsp;</td></tr>
    <tr>
        <td colspan="15" style="text-align:right; font-size:10px; font-weight:bold;">{{ $filterInfo['kode_form'] }}</td>
    </tr>
@else
    <tr>
        <td colspan="15" style="text-align:center; padding:20px; font-size:11px;">Tidak ada data Metal Detector yang sesuai dengan filter yang dipilih.</td>
    </tr>
@endif
</table>
</body>
</html>
