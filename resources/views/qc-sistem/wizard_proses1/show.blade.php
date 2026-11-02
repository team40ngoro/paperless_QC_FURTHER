@extends('layouts.app')

@section('container')
@php
    $namaProduk = $pembekuan->penggorenganData->produk->nama_produk
        ?? $pembekuan->hasilPenggorenganData->produk->nama_produk
        ?? $pembekuan->batteringData->produk->nama_produk
        ?? 'N/A';

    $isQcCompleted = (bool) (
        $pembekuan->hasil_penggorengan_uuid &&
        ($pembekuan->penggorenganData?->hasil_pencetakan) &&
        ($pembekuan->batteringData?->hasil_better) &&
        ($pembekuan->breaderData?->hasil_breader)
    );

    $existingFrayers = [
        1 => $frayer1,
        2 => $frayer2,
        3 => $frayer3,
        4 => $frayer4,
        5 => $frayer5,
    ];

    $checkedFrayerNums = collect($existingFrayers)
        ->filter(fn($f) => !is_null($f))
        ->keys()
        ->toArray();

    if (empty($checkedFrayerNums)) {
        $checkedFrayerNums = old('selected_frayers', [1]);
    } else {
        $checkedFrayerNums = old('selected_frayers', $checkedFrayerNums);
    }
    $checkedFrayerNums = array_map('intval', (array) $checkedFrayerNums);

    $defaultJam = $pembekuan->jam
        ? \Carbon\Carbon::parse($pembekuan->jam)->format('H:i')
        : \Carbon\Carbon::now('Asia/Jakarta')->format('H:i');
@endphp
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><i class="fas fa-magic text-primary"></i> Detail Verifikasi Proses Penggorengan/Pemasakan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('wizard.proses1.index') }}">Frying</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible shadow-sm border-0">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible shadow-sm border-0">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fas fa-exclamation-triangle"></i> {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible shadow-sm border-0">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <strong><i class="icon fas fa-exclamation-triangle"></i> Mohon periksa kembali inputan Anda:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">

                    {{-- ===== HEADER INFO ===== --}}
                    <div class="col-12 mb-3">
                        <div class="card bg-gradient-primary shadow-sm border-0">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h3 class="mb-1 font-weight-bold">
                                            {{ $namaProduk }}
                                            <span class="h5">({{ $pembekuan->penggorenganData->berat_produk ?? '-' }} gr)</span>
                                        </h3>
                                        <div class="d-flex flex-wrap align-items-center mt-2" style="gap: 10px;">
                                            <span class="badge badge-light p-2"><i class="fas fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($pembekuan->tanggal)->format('d F Y') }}</span>
                                            <span class="badge badge-light p-2"><i class="fas fa-clock mr-1"></i> {{ $pembekuan->jam }}</span>
                                            <span class="badge badge-light p-2"><i class="fas fa-layer-group mr-1"></i> Shift {{ $pembekuan->shift_data->shift ?? 'N/A' }}</span>
                                            <span class="badge badge-light p-2"><i class="fas fa-user mr-1"></i> Dibuat Oleh: {{ $pembekuan->user->name ?? 'N/A' }}</span>
                                            @if($isQcCompleted)
                                                <span class="badge badge-success p-2"><i class="fas fa-check-circle mr-1"></i> Verifikasi QC Lengkap</span>
                                            @else
                                                <span class="badge badge-warning p-2 text-dark"><i class="fas fa-hourglass-half mr-1"></i> Menunggu Input Verifikasi QC</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                                        @if($canEditQcVerification ?? false)
                                            <a href="#qc-verification-section" class="btn btn-warning btn-sm font-weight-bold mr-2">
                                                <i class="fas fa-clipboard-check mr-1"></i> {{ $isQcCompleted ? 'Update Verifikasi QC' : 'Input Verifikasi QC' }}
                                            </a>
                                        @endif
                                        <a href="{{ route('wizard.proses1.index') }}" class="btn btn-light btn-sm">
                                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== DATA INPUTAN PRODUKSI (DIBACA TERLEBIH DAHULU OLEH QC) ===== --}}
                    <div class="col-12">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="mb-0 font-weight-bold text-secondary">
                                <i class="fas fa-industry mr-1 text-primary"></i> Bagian A: Data Inputan Produksi (Langkah 1 – 4)
                            </h5>
                        </div>

                        {{-- STEP 1: PENGGORENGAN / PENCETAKAN --}}
                        @if($pembekuan->penggorenganData)
                        <div class="card card-outline card-primary mb-3 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-fire text-primary mr-1"></i> Langkah 1: Pencetakan</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3"><strong>Kode Produksi:</strong><p class="mb-1">{{ $pembekuan->penggorenganData->kode_produksi }}</p></div>
                                    <div class="col-md-3"><strong>No. of Strokes:</strong><p class="mb-1">{{ $pembekuan->penggorenganData->no_of_strokes }}</p></div>
                                    <div class="col-md-3"><strong>Waktu Pemasakan:</strong><p class="mb-1">{{ $pembekuan->penggorenganData->waktu_pemasakan }}</p></div>
                                    <div class="col-md-3">
                                        <strong>Hasil Pencetakan (QC):</strong>
                                        <p class="mb-1">
                                            @if($pembekuan->penggorenganData->hasil_pencetakan)
                                                <span class="badge badge-{{ $pembekuan->penggorenganData->hasil_pencetakan == '✔' ? 'success' : 'danger' }} p-2">
                                                    {{ $pembekuan->penggorenganData->hasil_pencetakan == '✔' ? '✔ OK' : '✘ Tidak OK' }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary p-2">Belum Diisi QC</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- STEP 2: PREDUST --}}
                        @if($pembekuan->predust_uuid && $predustData)
                        <div class="card card-outline card-info mb-3 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-mortar-pestle text-info mr-1"></i> Langkah 2: Predust</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4"><strong>Jenis Predust:</strong><p class="mb-1">{{ $predustData->jenisPredust->jenis_predust ?? '-' }}</p></div>
                                    <div class="col-md-4"><strong>Kode Produksi Predust:</strong><p class="mb-1">{{ $predustData->kode_produksi ?? '-' }}</p></div>
                                    <div class="col-md-4">
                                        <strong>Hasil Predust (QC):</strong>
                                        <p class="mb-1">
                                            @if($predustData->kondisi_predust)
                                                <span class="badge badge-{{ $predustData->kondisi_predust == '✔' ? 'success' : 'danger' }} p-2">
                                                    {{ $predustData->kondisi_predust == '✔' ? '✔ OK' : '✘ Tidak OK' }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary p-2">Belum Diisi QC</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="card card-outline card-secondary mb-3 shadow-sm">
                            <div class="card-header bg-light py-2">
                                <h3 class="card-title text-muted"><i class="fas fa-mortar-pestle mr-1"></i> Langkah 2: Predust <span class="badge badge-secondary ml-2">Tidak Menggunakan Line Predust (Dilewati)</span></h3>
                            </div>
                        </div>
                        @endif

                        {{-- STEP 3: BATTERING --}}
                        @if($pembekuan->batteringData)
                        <div class="card card-outline card-primary mb-3 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-tint text-primary mr-1"></i> Langkah 3: Battering</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6"><strong>Jenis Better & Kode Produksi Better:</strong><p class="mb-1">{!! $pembekuan->batteringData->better_names_with_codes_html !!}</p></div>
                                    <div class="col-md-6">
                                        <strong>Hasil Battering (QC):</strong>
                                        <p class="mb-1">
                                            @if($pembekuan->batteringData->hasil_better)
                                                <span class="badge badge-{{ $pembekuan->batteringData->hasil_better == '✔' ? 'success' : 'danger' }} p-2">
                                                    {{ $pembekuan->batteringData->hasil_better == '✔' ? '✔ OK' : '✘ Tidak OK' }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary p-2">Belum Diisi QC</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- STEP 4: BREADER --}}
                        @if($pembekuan->breaderData)
                        <div class="card card-outline card-primary mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-layer-group text-primary mr-1"></i> Langkah 4: Breading</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6"><strong>Jenis Breader & Kode Produksi Breader:</strong><p class="mb-1">{!! $pembekuan->breaderData->breader_names_with_codes_html !!}</p></div>
                                    <div class="col-md-6">
                                        <strong>Hasil Breading (QC):</strong>
                                        <p class="mb-1">
                                            @if($pembekuan->breaderData->hasil_breader)
                                                <span class="badge badge-{{ $pembekuan->breaderData->hasil_breader == '✔' ? 'success' : 'danger' }} p-2">
                                                    {{ $pembekuan->breaderData->hasil_breader == '✔' ? '✔ OK' : '✘ Tidak OK' }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary p-2">Belum Diisi QC</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- ===== RINGKASAN DATA FRYING & HASIL PENGGORENGAN (BAGIAN QC) ===== --}}
                        <div class="d-flex align-items-center mb-2 mt-4">
                            <h5 class="mb-0 font-weight-bold text-secondary">
                                <i class="fas fa-clipboard-check mr-1 text-danger"></i> Bagian B: Ringkasan Data Frying & Hasil Penggorengan (QC)
                            </h5>
                        </div>

                        {{-- STEP 5: FRYER --}}
                        @if($frayer1 || $frayer2 || $frayer3 || $frayer4 || $frayer5)
                        <div class="card card-outline card-danger mb-3 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-burn text-danger mr-1"></i> Langkah 5: Frying</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="bg-gray-100">
                                            <tr class="text-center font-weight-bold">
                                                <th>Mesin</th>
                                                <th>Suhu Std</th>
                                                <th>Waktu Std</th>
                                                <th>Aktual Suhu</th>
                                                <th>Aktual Waktu</th>
                                                <th>TPM</th>
                                                <th>FFA</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($frayer1)
                                            <tr class="text-center">
                                                <td class="font-weight-bold">Fryer 1</td>
                                                <td>
                                                    @php
                                                        $suhu1 = $frayer1->suhuFrayer->suhu_frayer ?? $frayer1->suhuFrayer->suhu_frayer_1 ?? null;
                                                        if (!$suhu1 && $frayer1->id_suhu_frayer_1) {
                                                            $master1 = \App\Models\SuhuFrayer1::find($frayer1->id_suhu_frayer_1);
                                                            $suhu1 = $master1->suhu_frayer ?? $master1->suhu_frayer_1 ?? null;
                                                        }
                                                    @endphp
                                                    {{ $suhu1 ?? '-' }} °C
                                                </td>
                                                <td>{{ $frayer1->waktuPenggorengan->waktu_penggorengan ?? '-' }} dtk</td>
                                                <td>{{ $frayer1->aktual_suhu_penggorengan ?? '-' }} °C</td>
                                                <td>{{ $frayer1->aktual_penggorengan }} dtk</td>
                                                <td>{{ $frayer1->tpm_minyak }} %</td>
                                                <td>{{ $frayer1->ffa ?? '-' }}</td>
                                                <td>{{ $frayer1->keterangan ?? '-' }}</td>
                                            </tr>
                                            @endif

                                            @if($frayer2)
                                            <tr class="text-center">
                                                <td class="font-weight-bold">Fryer 2</td>
                                                <td>
                                                    @php
                                                        $suhu2 = null;
                                                        if ($frayer2->suhuFrayer2) {
                                                            $suhu2 = $frayer2->suhuFrayer2->suhu_frayer_2;
                                                        } elseif ($frayer2->suhuFrayer) {
                                                            $suhu2 = $frayer2->suhuFrayer->suhu_frayer_2;
                                                        }
                                                        if (!$suhu2 && $frayer2->id_suhu_frayer_2) {
                                                            $master2 = \App\Models\SuhuFrayer1::find($frayer2->id_suhu_frayer_2) ?? \App\Models\SuhuFrayer2::find($frayer2->id_suhu_frayer_2);
                                                            $suhu2 = $master2->suhu_frayer_2 ?? null;
                                                        }
                                                    @endphp
                                                    {{ $suhu2 ?? '-' }} °C
                                                </td>
                                                <td>{{ $frayer2->waktuPenggorengan2->waktu_penggorengan_2 ?? $frayer2->waktuPenggorengan->waktu_penggorengan_2 ?? '-' }} dtk</td>
                                                <td>{{ $frayer2->aktual_suhu_penggorengan ?? '-' }} °C</td>
                                                <td>{{ $frayer2->aktual_penggorengan }} dtk</td>
                                                <td>{{ $frayer2->tpm_minyak }} %</td>
                                                <td>{{ $frayer2->ffa ?? '-' }}</td>
                                                <td>{{ $frayer2->keterangan ?? '-' }}</td>
                                            </tr>
                                            @endif

                                            @if($frayer3)
                                            <tr class="text-center">
                                                <td class="font-weight-bold">Fryer 3</td>
                                                <td>{{ $frayer3->suhuFrayer->suhu_frayer_3 ?? $frayer3->suhuFrayer->suhu_frayer ?? $frayer3->suhuFrayer3->suhu_frayer_3 ?? '-' }} °C</td>
                                                <td>{{ $frayer3->waktuPenggorengan->waktu_penggorengan_3 ?? $frayer3->waktuPenggorengan->waktu_penggorengan ?? '-' }} dtk</td>
                                                <td>{{ $frayer3->aktual_suhu_penggorengan ?? '-' }} °C</td>
                                                <td>{{ $frayer3->aktual_penggorengan }} dtk</td>
                                                <td>{{ $frayer3->tpm_minyak }} %</td>
                                                <td>-</td>
                                                <td>{{ $frayer3->keterangan ?? '-' }}</td>
                                            </tr>
                                            @endif

                                            @if($frayer4)
                                            <tr class="text-center">
                                                <td class="font-weight-bold">Fryer 4</td>
                                                <td>{{ $frayer4->suhuFrayer->suhu_frayer_4 ?? $frayer4->suhuFrayer->suhu_frayer ?? $frayer4->suhuFrayer4->suhu_frayer_4 ?? '-' }} °C</td>
                                                <td>{{ $frayer4->waktuPenggorengan->waktu_penggorengan_4 ?? $frayer4->waktuPenggorengan->waktu_penggorengan ?? '-' }} dtk</td>
                                                <td>{{ $frayer4->aktual_suhu_penggorengan ?? '-' }} °C</td>
                                                <td>{{ $frayer4->aktual_penggorengan }} dtk</td>
                                                <td>{{ $frayer4->tpm_minyak }} %</td>
                                                <td>-</td>
                                                <td>{{ $frayer4->keterangan ?? '-' }}</td>
                                            </tr>
                                            @endif

                                            @if($frayer5)
                                            <tr class="text-center">
                                                <td class="font-weight-bold">Fryer 5</td>
                                                <td>{{ $frayer5->suhuFrayer->suhu_frayer_5 ?? $frayer5->suhuFrayer->suhu_frayer ?? $frayer5->suhuFrayer5->suhu_frayer_5 ?? '-' }} °C</td>
                                                <td>{{ $frayer5->waktuPenggorengan->waktu_penggorengan_5 ?? $frayer5->waktuPenggorengan->waktu_penggorengan ?? '-' }} dtk</td>
                                                <td>{{ $frayer5->aktual_suhu_penggorengan ?? '-' }} °C</td>
                                                <td>{{ $frayer5->aktual_penggorengan }} dtk</td>
                                                <td>{{ $frayer5->tpm_minyak }} %</td>
                                                <td>-</td>
                                                <td>{{ $frayer5->keterangan ?? '-' }}</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-light border mb-3 text-muted">
                            <i class="fas fa-info-circle mr-1"></i> Data <strong>Langkah 5: Frying</strong> belum diinput oleh QC Inspector.
                        </div>
                        @endif

                        {{-- STEP 6: HASIL PENGGORENGAN --}}
                        @if($pembekuan->hasilPenggorenganData)
                        <div class="card card-outline card-primary mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-check-double text-primary mr-1"></i> Langkah 6: Hasil Penggorengan</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 text-center">
                                    <div class="col-md-4"><strong>Suhu Pusat Std:</strong><p>
                                        @php
                                            $stdSuhuArr = $pembekuan->hasilPenggorenganData->stdSuhuPusat->std_suhu_pusat ?? 'N/A';
                                            $finalStd = 'N/A';
                                            if (is_array($stdSuhuArr)) {
                                                if ($frayer5) $finalStd = $stdSuhuArr[4] ?? ($stdSuhuArr[0] ?? 'N/A');
                                                elseif ($frayer4) $finalStd = $stdSuhuArr[3] ?? ($stdSuhuArr[0] ?? 'N/A');
                                                elseif ($frayer3) $finalStd = $stdSuhuArr[2] ?? ($stdSuhuArr[0] ?? 'N/A');
                                                elseif ($frayer2) $finalStd = $stdSuhuArr[1] ?? ($stdSuhuArr[0] ?? 'N/A');
                                                elseif ($frayer1) $finalStd = $stdSuhuArr[0] ?? 'N/A';
                                                else $finalStd = implode(' / ', array_filter($stdSuhuArr));
                                            } else {
                                                $finalStd = $stdSuhuArr;
                                            }
                                        @endphp
                                        {{ $finalStd }} °C
                                    </p></div>
                                    <div class="col-md-4"><strong>Suhu Pusat Aktual:</strong>
                                        <p>
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
                                                @foreach($suhuPusatList as $suhuVal)
                                                    <span class="badge badge-info mr-1 mb-1" style="font-size: 0.95rem;">
                                                        <i class="fas fa-thermometer-half mr-1"></i>{{ $suhuVal }} °C
                                                    </span>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-4"><strong>Keterangan:</strong><p>{{ $pembekuan->hasilPenggorenganData->keterangan ?? '-' }}</p></div>
                                </div>
                                <hr>
                                <h6><strong>Uji Sensori:</strong></h6>
                                <div class="row text-center mt-2">
                                    @php
                                        $sensories = [
                                            'Kematangan' => $pembekuan->hasilPenggorenganData->sensori_kematangan,
                                            'Kenampakan' => $pembekuan->hasilPenggorenganData->sensori_kenampakan,
                                            'Warna'      => $pembekuan->hasilPenggorenganData->sensori_warna,
                                            'Rasa'       => $pembekuan->hasilPenggorenganData->sensori_rasa,
                                            'Bau'        => $pembekuan->hasilPenggorenganData->sensori_bau,
                                            'Tekstur'    => $pembekuan->hasilPenggorenganData->sensori_tekstur,
                                        ];
                                    @endphp
                                    @foreach($sensories as $label => $val)
                                    <div class="col-4 col-md-2 mb-3">
                                        <div class="small text-muted">{{ $label }}</div>
                                        <div class="font-weight-bold h5">
                                            <span class="badge badge-{{ $val == '✔' ? 'success' : 'danger' }}">{{ $val ?? '-' }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-light border mb-4 text-muted">
                            <i class="fas fa-info-circle mr-1"></i> Data <strong>Langkah 6: Hasil Penggorengan</strong> belum diinput oleh QC Inspector.
                        </div>
                        @endif

                        {{-- ===== FORM INPUT / UPDATE VERIFIKASI QC INSPECTOR (KHUSUS ROLE QC / ADMIN / SUPERADMIN) ===== --}}
                        @if($canEditQcVerification ?? false)
                        <div id="qc-verification-section" class="card card-warning card-outline mb-5 shadow">
                            <div class="card-header bg-warning">
                                <h3 class="card-title font-weight-bold text-dark">
                                    <i class="fas fa-user-check mr-1"></i> Form Input / Update Verifikasi QC Inspector
                                </h3>
                            </div>
                            <form action="{{ route('wizard.proses1.qc-verification', $pembekuan->uuid) }}" method="POST" id="qcVerificationForm">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="alert alert-info py-2">
                                        <i class="fas fa-eye mr-1"></i> Pastikan Anda telah memeriksa data inputan <strong>Produksi</strong> pada Bagian A di atas sebelum mengisi hasil verifikasi QC di bawah ini.
                                    </div>

                                    {{-- 1 s/d 4: HASIL PENCETAKAN, PREDUST, BATTERING, BREADING --}}
                                    <h5 class="font-weight-bold text-primary border-bottom pb-2 mb-3">
                                        1 – 4. Verifikasi Hasil Proses (Pencetakan, Predust, Battering, Breading)
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>1. Hasil Pencetakan <span class="text-danger">*</span></label>
                                                @php
                                                    $valPencetakan = old('hasil_pencetakan', $pembekuan->penggorenganData->hasil_pencetakan ?? '✔');
                                                @endphp
                                                <select name="hasil_pencetakan" class="form-control" required>
                                                    <option value="✔" {{ $valPencetakan == '✔' ? 'selected' : '' }}>✔ OK</option>
                                                    <option value="✘" {{ $valPencetakan == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>2. Hasil Predust {!! $pembekuan->predust_uuid ? '<span class="text-danger">*</span>' : '<small class="text-muted">(Line Dilewati)</small>' !!}</label>
                                                @if($pembekuan->predust_uuid && $predustData)
                                                    @php
                                                        $valPredust = old('kondisi_predust', $predustData->kondisi_predust ?? '✔');
                                                    @endphp
                                                    <select name="kondisi_predust" class="form-control" required>
                                                        <option value="✔" {{ $valPredust == '✔' ? 'selected' : '' }}>✔ OK</option>
                                                        <option value="✘" {{ $valPredust == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                                    </select>
                                                @else
                                                    <input type="text" class="form-control" value="Tidak menggunakan Predust" disabled>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>3. Hasil Battering <span class="text-danger">*</span></label>
                                                @php
                                                    $valBatter = old('hasil_better', $pembekuan->batteringData->hasil_better ?? '✔');
                                                @endphp
                                                <select name="hasil_better" class="form-control" required>
                                                    <option value="✔" {{ $valBatter == '✔' ? 'selected' : '' }}>✔ OK</option>
                                                    <option value="✘" {{ $valBatter == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>4. Hasil Breading <span class="text-danger">*</span></label>
                                                @php
                                                    $valBreader = old('hasil_breader', $pembekuan->breaderData->hasil_breader ?? '✔');
                                                @endphp
                                                <select name="hasil_breader" class="form-control" required>
                                                    <option value="✔" {{ $valBreader == '✔' ? 'selected' : '' }}>✔ OK</option>
                                                    <option value="✘" {{ $valBreader == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 5. FRYING (INPUT ALL) --}}
                                    <h5 class="font-weight-bold text-danger border-bottom pb-2 mt-4 mb-3">
                                        5. Frying (Input All)
                                    </h5>
                                    <div class="form-group">
                                        <label class="d-block">Pilih Mesin Fryer yang Digunakan: <span class="text-danger">*</span></label>
                                        <div class="d-flex flex-wrap" style="gap: 20px;">
                                            @foreach([1, 2, 3, 4, 5] as $fn)
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox"
                                                           class="custom-control-input qc-frayer-toggle"
                                                           id="qc_chk_frayer_{{ $fn }}"
                                                           name="selected_frayers[]"
                                                           value="{{ $fn }}"
                                                           {{ in_array($fn, $checkedFrayerNums, true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold" for="qc_chk_frayer_{{ $fn }}">
                                                        Fryer {{ $fn }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    @foreach([1, 2, 3, 4, 5] as $fn)
                                        @php
                                            $fObj = $existingFrayers[$fn];
                                            $isChecked = in_array($fn, $checkedFrayerNums, true);
                                            $selectedSuhuId = old(
                                                "frayer_{$fn}_suhu",
                                                $fn == 1 ? ($fObj->id_suhu_frayer_1 ?? '') : ($fn == 2 ? ($fObj->id_suhu_frayer_2 ?? '') : ($fObj->id_suhu_frayer ?? ''))
                                            );
                                            $selectedWaktuId = old(
                                                "frayer_{$fn}_waktu",
                                                $fn == 2 ? ($fObj->id_waktu_penggorengan_2 ?? '') : ($fObj->id_waktu_penggorengan ?? '')
                                            );
                                            $fJam = old("frayer_{$fn}_jam", $fObj && $fObj->jam ? \Carbon\Carbon::parse($fObj->jam)->format('H:i') : $defaultJam);
                                        @endphp
                                        <div class="card card-outline card-danger mb-3 qc-frayer-card {{ $isChecked ? '' : 'd-none' }}" id="qc_frayer_card_{{ $fn }}" data-frayer="{{ $fn }}">
                                            <div class="card-header py-2 bg-light">
                                                <h6 class="card-title mb-0 font-weight-bold text-danger">
                                                    <i class="fas fa-fire mr-1"></i> Parameter Fryer {{ $fn }}
                                                </h6>
                                            </div>
                                            <div class="card-body py-3">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group mb-2">
                                                            <label>Jam Fryer {{ $fn }} <span class="text-danger">*</span></label>
                                                            <input type="time" name="frayer_{{ $fn }}_jam" class="form-control qc-frayer-input" value="{{ $fJam }}" {{ $isChecked ? 'required' : 'disabled' }}>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group mb-2">
                                                            <label>Standar Suhu <span class="text-danger">*</span></label>
                                                            <select name="frayer_{{ $fn }}_suhu"
                                                                    class="form-control qc-frayer-input qc-suhu-select"
                                                                    data-frayer="{{ $fn }}"
                                                                    {{ $isChecked ? 'required' : 'disabled' }}>
                                                                <option value="">-- Pilih Standar Suhu --</option>
                                                                @if($fn == 2)
                                                                    @foreach($suhuFrayer2Options as $s2)
                                                                        <option value="{{ $s2->id }}" {{ (string)$selectedSuhuId === (string)$s2->id ? 'selected' : '' }}>
                                                                            {{ $s2->suhu_frayer_2 }} °C
                                                                        </option>
                                                                    @endforeach
                                                                @else
                                                                    @foreach($suhuFrayer1Options as $s1)
                                                                        @php
                                                                            $suhuLabel = match($fn) {
                                                                                1 => $s1->suhu_frayer ?? $s1->suhu_frayer_1,
                                                                                3 => $s1->suhu_frayer_3 ?? $s1->suhu_frayer,
                                                                                4 => $s1->suhu_frayer_4 ?? $s1->suhu_frayer,
                                                                                5 => $s1->suhu_frayer_5 ?? $s1->suhu_frayer,
                                                                            };
                                                                        @endphp
                                                                        @if(!empty($suhuLabel))
                                                                            <option value="{{ $s1->id }}" {{ (string)$selectedSuhuId === (string)$s1->id ? 'selected' : '' }}>
                                                                                {{ $suhuLabel }} °C
                                                                            </option>
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group mb-2">
                                                            <label>Standar Waktu <span class="text-danger">*</span></label>
                                                            <select name="frayer_{{ $fn }}_waktu"
                                                                    id="qc_frayer_{{ $fn }}_waktu"
                                                                    class="form-control qc-frayer-input"
                                                                    {{ $isChecked ? 'required' : 'disabled' }}>
                                                                <option value="">-- Pilih Standar Waktu --</option>
                                                                @if($fn == 2)
                                                                    @foreach($waktuPenggorengan2Options as $w2)
                                                                        <option value="{{ $w2->id }}"
                                                                                data-suhu-id="{{ $w2->id_suhu_frayer_2 }}"
                                                                                {{ (string)$selectedWaktuId === (string)$w2->id ? 'selected' : '' }}>
                                                                            {{ $w2->waktu_penggorengan_2 }} dtk
                                                                        </option>
                                                                    @endforeach
                                                                @else
                                                                    @foreach($waktuPenggorengan1Options as $w1)
                                                                        @php
                                                                            $waktuLabel = match($fn) {
                                                                                1 => $w1->waktu_penggorengan,
                                                                                3 => $w1->waktu_penggorengan_3 ?? $w1->waktu_penggorengan,
                                                                                4 => $w1->waktu_penggorengan_4 ?? $w1->waktu_penggorengan,
                                                                                5 => $w1->waktu_penggorengan_5 ?? $w1->waktu_penggorengan,
                                                                            };
                                                                        @endphp
                                                                        @if(!empty($waktuLabel))
                                                                            <option value="{{ $w1->id }}"
                                                                                    data-suhu-id="{{ $w1->id_suhu_frayer_1 }}"
                                                                                    {{ (string)$selectedWaktuId === (string)$w1->id ? 'selected' : '' }}>
                                                                                {{ $waktuLabel }} dtk
                                                                            </option>
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group mb-2">
                                                            <label>Aktual Suhu (°C)</label>
                                                            <input type="text"
                                                                   name="frayer_{{ $fn }}_aktual_suhu"
                                                                   class="form-control qc-frayer-input"
                                                                   placeholder="Contoh: 185"
                                                                   value="{{ old("frayer_{$fn}_aktual_suhu", $fObj->aktual_suhu_penggorengan ?? '') }}"
                                                                   {{ $isChecked ? '' : 'disabled' }}>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group mb-2">
                                                            <label>Aktual Waktu (Detik) <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                   name="frayer_{{ $fn }}_aktual_waktu"
                                                                   class="form-control qc-frayer-input"
                                                                   placeholder="Contoh: 45"
                                                                   value="{{ old("frayer_{$fn}_aktual_waktu", $fObj->aktual_penggorengan ?? '') }}"
                                                                   {{ $isChecked ? 'required' : 'disabled' }}>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group mb-2">
                                                            <label>TPM Minyak (%) <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                   name="frayer_{{ $fn }}_tpm"
                                                                   class="form-control qc-frayer-input"
                                                                   placeholder="Contoh: 12"
                                                                   value="{{ old("frayer_{$fn}_tpm", $fObj->tpm_minyak ?? '') }}"
                                                                   {{ $isChecked ? 'required' : 'disabled' }}>
                                                        </div>
                                                    </div>

                                                    @if(in_array($fn, [1, 2], true))
                                                    <div class="col-md-3">
                                                        <div class="form-group mb-2">
                                                            <label>FFA</label>
                                                            <input type="text"
                                                                   name="frayer_{{ $fn }}_ffa"
                                                                   class="form-control qc-frayer-input"
                                                                   placeholder="Nilai FFA"
                                                                   value="{{ old("frayer_{$fn}_ffa", $fObj->ffa ?? '') }}"
                                                                   {{ $isChecked ? '' : 'disabled' }}>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    <div class="col-md-{{ in_array($fn, [1, 2], true) ? '3' : '6' }}">
                                                        <div class="form-group mb-2">
                                                            <label>Keterangan</label>
                                                            <input type="text"
                                                                   name="frayer_{{ $fn }}_keterangan"
                                                                   class="form-control qc-frayer-input"
                                                                   placeholder="Catatan tambahan"
                                                                   value="{{ old("frayer_{$fn}_keterangan", $fObj->keterangan ?? '') }}"
                                                                   {{ $isChecked ? '' : 'disabled' }}>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- 6. HASIL PENGGORENGAN (INPUT ALL) --}}
                                    <h5 class="font-weight-bold text-primary border-bottom pb-2 mt-4 mb-3">
                                        6. Hasil Penggorengan (Input All)
                                    </h5>
                                    @php
                                        $hasilObj = $pembekuan->hasilPenggorenganData;
                                        $selectedStdSuhuId = old('id_std_suhu_pusat', $hasilObj->id_std_suhu_pusat ?? ($stdSuhuPusatOptions->first()->id ?? ''));
                                        $jamHasilVal = old('jam_hasil', $hasilObj && $hasilObj->jam ? \Carbon\Carbon::parse($hasilObj->jam)->format('H:i') : $defaultJam);

                                        $existingSuhuPusatArr = [];
                                        if (old('aktual_suhu_pusat')) {
                                            $existingSuhuPusatArr = (array) old('aktual_suhu_pusat');
                                        } elseif ($hasilObj && !empty($hasilObj->aktual_suhu_pusat)) {
                                            $rawVal = $hasilObj->aktual_suhu_pusat;
                                            $existingSuhuPusatArr = is_array($rawVal)
                                                ? $rawVal
                                                : array_values(array_filter(array_map('trim', explode(',', (string) $rawVal)), fn($v) => $v !== ''));
                                        }
                                        if (empty($existingSuhuPusatArr)) {
                                            $existingSuhuPusatArr = [''];
                                        }
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Jam Hasil Penggorengan <span class="text-danger">*</span></label>
                                                <input type="time" name="jam_hasil" class="form-control" value="{{ $jamHasilVal }}" required>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Standar Suhu Pusat <span class="text-danger">*</span></label>
                                                <select name="id_std_suhu_pusat" class="form-control" required>
                                                    <option value="">-- Pilih Standar Suhu Pusat --</option>
                                                    @foreach($stdSuhuPusatOptions as $std)
                                                        @php
                                                            $stdText = is_array($std->std_suhu_pusat)
                                                                ? implode('°C / ', array_filter($std->std_suhu_pusat)) . '°C'
                                                                : $std->std_suhu_pusat . '°C';
                                                        @endphp
                                                        <option value="{{ $std->id }}" {{ (string)$selectedStdSuhuId === (string)$std->id ? 'selected' : '' }}>
                                                            {{ $stdText }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Aktual Suhu Pusat (°C) <span class="text-danger">*</span></label>
                                                <div id="qc_aktual_suhu_pusat_container">
                                                    @foreach($existingSuhuPusatArr as $idx => $spVal)
                                                        <div class="input-group mb-2">
                                                            <input type="text"
                                                                   name="aktual_suhu_pusat[]"
                                                                   class="form-control"
                                                                   placeholder="Contoh: 78.5"
                                                                   value="{{ $spVal }}"
                                                                   required>
                                                            <div class="input-group-append">
                                                                @if($idx === 0)
                                                                    <button class="btn btn-outline-success btn-qc-add-suhu-pusat" type="button" title="Tambah titik suhu">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                @else
                                                                    <button class="btn btn-outline-danger btn-qc-remove-suhu-pusat" type="button" title="Hapus">
                                                                        <i class="fas fa-minus"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        @foreach(['kematangan', 'kenampakan', 'warna', 'rasa', 'bau', 'tekstur'] as $sen)
                                            @php
                                                $senField = "sensori_{$sen}";
                                                $senVal = old($senField, $hasilObj->{$senField} ?? '✔');
                                            @endphp
                                            <div class="col-md-2 col-6">
                                                <div class="form-group">
                                                    <label class="text-capitalize">Sensori {{ $sen }} <span class="text-danger">*</span></label>
                                                    <select name="{{ $senField }}" class="form-control" required>
                                                        <option value="✔" {{ $senVal == '✔' ? 'selected' : '' }}>✔ OK</option>
                                                        <option value="✘" {{ $senVal == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="form-group mt-2">
                                        <label>Keterangan Hasil Penggorengan</label>
                                        <textarea name="keterangan_hasil" class="form-control" rows="2" placeholder="Catatan tambahan hasil penggorengan">{{ old('keterangan_hasil', $hasilObj->keterangan ?? '') }}</textarea>
                                    </div>
                                </div>
                                <div class="card-footer text-right bg-light">
                                    <button type="submit" class="btn btn-success font-weight-bold px-4">
                                        <i class="fas fa-save mr-1"></i> Simpan Verifikasi QC
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Toggle Fryer cards & enable/disable inputs when checkbox changes
    document.querySelectorAll('.qc-frayer-toggle').forEach(function (chk) {
        chk.addEventListener('change', function () {
            const fn = this.value;
            const card = document.getElementById('qc_frayer_card_' + fn);
            if (!card) return;
            const inputs = card.querySelectorAll('.qc-frayer-input');
            if (this.checked) {
                card.classList.remove('d-none');
                inputs.forEach(function (inp) {
                    inp.disabled = false;
                });
            } else {
                card.classList.add('d-none');
                inputs.forEach(function (inp) {
                    inp.disabled = true;
                });
            }
        });
    });

    // Auto-select matching Standar Waktu when Standar Suhu changes
    document.querySelectorAll('.qc-suhu-select').forEach(function (sel) {
        sel.addEventListener('change', function () {
            const fn = this.dataset.frayer;
            const suhuId = this.value;
            const waktuSelect = document.getElementById('qc_frayer_' + fn + '_waktu');
            if (!waktuSelect) return;

            let firstMatch = null;
            Array.from(waktuSelect.options).forEach(function (opt) {
                if (!opt.value) return;
                const optSuhuId = opt.getAttribute('data-suhu-id');
                if (!suhuId || optSuhuId === suhuId) {
                    opt.hidden = false;
                    if (!firstMatch) firstMatch = opt.value;
                } else {
                    opt.hidden = true;
                }
            });

            if (firstMatch) {
                waktuSelect.value = firstMatch;
            } else {
                waktuSelect.value = '';
            }
        });
    });

    // Dynamic Aktual Suhu Pusat add/remove
    document.addEventListener('click', function (e) {
        const addBtn = e.target.closest('.btn-qc-add-suhu-pusat');
        if (addBtn) {
            e.preventDefault();
            const container = document.getElementById('qc_aktual_suhu_pusat_container');
            if (!container) return;
            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `
                <input type="text" name="aktual_suhu_pusat[]" class="form-control" placeholder="Contoh: 78.5" required>
                <div class="input-group-append">
                    <button class="btn btn-outline-danger btn-qc-remove-suhu-pusat" type="button" title="Hapus">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            `;
            container.appendChild(div);
        }

        const removeBtn = e.target.closest('.btn-qc-remove-suhu-pusat');
        if (removeBtn) {
            e.preventDefault();
            const group = removeBtn.closest('.input-group');
            if (group) group.remove();
        }
    });
});
</script>

<style>
    @media print {
        .main-sidebar, .main-header, .card-tools, .btn, .breadcrumb, #qc-verification-section { display: none !important; }
        .content-wrapper { margin-left: 0 !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
        .bg-gradient-primary { background: #fff !important; color: #000 !important; border: 1px solid #000 !important; }
        .badge { border: 1px solid #000 !important; color: #000 !important; background: transparent !important; }
    }
</style>
@endsection
