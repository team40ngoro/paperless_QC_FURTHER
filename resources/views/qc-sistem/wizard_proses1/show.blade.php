@extends('layouts.app')

@section('container')
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

                <div class="row">

                    {{-- ===== HEADER INFO ===== --}}
                    <div class="col-12 mb-3">
                        <div class="card bg-gradient-primary shadow-sm border-0">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h3 class="mb-1 font-weight-bold">
                                            {{ $pembekuan->hasilPenggorenganData->produk->nama_produk ?? 'N/A' }} 
                                            <span class="h5">({{ $pembekuan->penggorenganData->berat_produk ?? '-' }} gr)</span>
                                        </h3>
                                        <div class="d-flex flex-wrap" style="gap: 15px;">
                                            <span class="badge badge-light p-2"><i class="fas fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($pembekuan->tanggal)->format('d F Y') }}</span>
                                            <span class="badge badge-light p-2"><i class="fas fa-clock mr-1"></i> {{ $pembekuan->jam }}</span>
                                            <span class="badge badge-light p-2"><i class="fas fa-layer-group mr-1"></i>Shift {{ $pembekuan->shift_data->shift ?? 'N/A' }}</span>
                                            <span class="badge badge-light p-2"><i class="fas fa-user mr-1"></i>Dibuat Oleh : {{ $pembekuan->user->name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                                        <a href="{{ route('wizard.proses1.index') }}" class="btn btn-light btn-sm mr-2">
                                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                                        </a>
                                        <!-- <button onclick="window.print()" class="btn btn-light btn-sm">
                                            <i class="fas fa-print mr-1"></i> Print
                                        </button> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== STEP-BY-STEP DETAILS ===== --}}
                    <div class="col-12">

                        {{-- STEP 1: PENGGORENGAN --}}
                        @if($pembekuan->penggorenganData)
                        <div class="card card-outline card-primary mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-fire text-primary mr-1"></i> Langkah 1: Pencetakan</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- <div class="col-md-3"><strong>Shift/Tanggal:</strong><p>{{ $pembekuan->penggorenganData->shift->nama_shift ?? 'N/A' }} / {{ \Carbon\Carbon::parse($pembekuan->penggorenganData->tanggal)->format('d-m-Y') }} {{ $pembekuan->penggorenganData->jam }}</p></div> -->
                                    <!-- <div class="col-md-3"><strong>Produk:</strong><p>{{ $pembekuan->penggorenganData->produk->nama_produk ?? 'N/A' }}</p></div> -->
                                    <div class="col-md-3"><strong>Kode Produksi:</strong><p>{{ $pembekuan->penggorenganData->kode_produksi }}</p></div>
                                    <!-- <div class="col-md-3"><strong>Berat Produk:</strong><p>{{ $pembekuan->penggorenganData->berat_produk }} gr</p></div> -->
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3"><strong>No. of Strokes:</strong><p>{{ $pembekuan->penggorenganData->no_of_strokes }}</p></div>
                                    <div class="col-md-3"><strong>Waktu Pemasakan:</strong><p>{{ $pembekuan->penggorenganData->waktu_pemasakan }}</p></div>
                                    <div class="col-md-3"><strong>Hasil Cetak:</strong><p><span class="badge badge-{{ $pembekuan->penggorenganData->hasil_pencetakan == '✔' ? 'warning' : 'danger' }} p-2">{{ $pembekuan->penggorenganData->hasil_pencetakan }}</span></p></div>

                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- STEP 2: PREDUST --}}
                        @if($pembekuan->predust_uuid && $predustData)
                        <div class="card card-outline card-info mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-mortar-pestle text-info mr-1"></i> Langkah 2: Predust</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4"><strong>Jenis Predust:</strong><p>{{ $predustData->jenisPredust->jenis_predust ?? '-' }}</p></div>
                                    <div class="col-md-4"><strong>Kode Produksi:</strong><p>{{ $predustData->kode_produksi ?? '-' }}</p></div>
                                    <div class="col-md-4"><strong>Hasil Predust:</strong><p><span class="badge badge-{{ $predustData->kondisi_predust == '✔' ? 'warning' : 'danger' }} p-2">{{ $predustData->kondisi_predust }}</span></p></div>
                                </div>
                            </div>
                        </div>
                        @elseif($pembekuan->predust_uuid)
                        <div class="card card-outline card-secondary mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-mortar-pestle text-secondary mr-1"></i> Langkah 2: Predust <span class="badge badge-secondary ml-2">(Data tidak ditemukan)</span></h3>
                            </div>
                        </div>
                        @endif

                        {{-- STEP 3: BATTERING --}}
                        @if($pembekuan->batteringData)
                        <div class="card card-outline card-primary mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-tint text-primary mr-1"></i> Langkah 3: Battering</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- <div class="col-md-3"><strong>Waktu:</strong><p>{{ \Carbon\Carbon::parse($pembekuan->batteringData->tanggal)->format('d-m-Y') }} {{ $pembekuan->batteringData->jam }}</p></div> -->
                                    <!-- <div class="col-md-3"><strong>Produk:</strong><p>{{ $pembekuan->batteringData->produk->nama_produk ?? 'N/A' }}</p></div> -->
                                    <!-- <div class="col-md-3"><strong>Jenis Better:</strong><p>
                                        @if($pembekuan->batteringData->id_jenis_better)
                                            {{ $pembekuan->batteringData->jenis_better_names }}
                                        @else
                                            N/A
                                        @endif
                                    </p></div> -->
                                    <div class="col-md-6"><strong>Jenis Better & Kode Produksi Better: </strong><p>{!! $pembekuan->batteringData->better_names_with_codes_html !!}</p></div>
                                    <div class="col-md-3"><strong>Hasil Better:</strong><p><span class="badge badge-{{ $pembekuan->batteringData->hasil_better == '✔' ? 'warning' : 'danger' }} p-2">{{ $pembekuan->batteringData->hasil_better }}</span></p></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- STEP 4: BREADER --}}
                        @if($pembekuan->breaderData)
                        <div class="card card-outline card-primary mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-layer-group text-primary mr-1"></i> Langkah 4: Breader</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- <div class="col-md-3"><strong>Waktu:</strong><p>{{ \Carbon\Carbon::parse($pembekuan->breaderData->tanggal)->format('d-m-Y') }} {{ $pembekuan->breaderData->jam }}</p></div> -->
                                    <!-- <div class="col-md-3"><strong>Produk:</strong><p>{{ $pembekuan->breaderData->produk->nama_produk ?? 'N/A' }}</p></div> -->
                                    <!-- <div class="col-md-3"><strong>Jenis Breader:</strong><p>
                                        @if($pembekuan->breaderData->id_jenis_breader)
                                            {{ $pembekuan->breaderData->jenis_breader_names }}
                                        @else
                                            N/A
                                        @endif
                                    </p></div> -->
                                    <div class="col-md-6"><strong>Jenis Breader & Kode Produksi Breader</strong><p>{!! $pembekuan->breaderData->breader_names_with_codes_html !!}</p></div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3"><strong>Hasil Breader:</strong><p><span class="badge badge-{{ $pembekuan->breaderData->hasil_breader == '✔' ? 'warning' : 'danger' }} p-2">{{ $pembekuan->breaderData->hasil_breader }}</span></p></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- STEP 5: FRYER --}}
                        @if($frayer1 || $frayer2 || $frayer3 || $frayer4 || $frayer5)
                        <div class="card card-outline card-danger mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-burn text-danger mr-1"></i> Langkah 5: Fryer</h3>
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
                                                <!-- <th>Waktu</th> -->
                                                <th>Suhu Std</th>
                                                <th>Waktu Std</th>
                                                <th>Aktual Suhu</th>
                                                <th>Aktual Waktu</th>
                                                <th>TPM</th>
                                                <th>FFA</th>
                                                <th>Keterangan</th>
                                                <!-- <th>Produk</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($frayer1)
                                            <tr class="text-center">
                                                <td class="font-weight-bold">Fryer 1</td>
                                                <!-- <td>{{ $frayer1->jam }}</td> -->
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
                                                <!-- <td><small>{{ $frayer1->produk->nama_produk ?? '-' }}</small></td> -->
                                            </tr>
                                            @endif

                                            @if($frayer2)
                                            <tr class="text-center">
                                                <td class="font-weight-bold">Fryer 2</td>
                                                <!-- <td>{{ $frayer2->jam }}</td> -->
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
                                                <!-- <td><small>{{ $frayer2->produk->nama_produk ?? '-' }}</small></td> -->
                                            </tr>
                                            @endif

                                            @if($frayer3)
                                            <tr class="text-center">
                                                <td class="font-weight-bold">Fryer 3</td>
                                                <!-- <td>{{ $frayer3->jam }}</td> -->
                                                <td>{{ $frayer3->suhuFrayer->suhu_frayer_3 ?? $frayer3->suhuFrayer3->suhu_frayer_3 ?? '-' }} °C</td>
                                                <td>{{ $frayer3->waktuPenggorengan->waktu_penggorengan ?? $frayer3->waktuPenggorengan3->waktu_penggorengan_3 ?? '-' }} dtk</td>
                                                <td>{{ $frayer3->aktual_suhu_penggorengan ?? '-' }} °C</td>
                                                <td>{{ $frayer3->aktual_penggorengan }} dtk</td>
                                                <td>{{ $frayer3->tpm_minyak }} %</td>
                                                <td>-</td>
                                                <td>{{ $frayer3->keterangan ?? '-' }}</td>
                                                <!-- <td><small>{{ $frayer3->produk->nama_produk ?? '-' }}</small></td> -->
                                            </tr>
                                            @endif

                                            @if($frayer4)
                                            <tr class="text-center">
                                                <td class="font-weight-bold">Fryer 4</td>
                                                <!-- <td>{{ $frayer4->jam }}</td> -->
                                                <td>{{ $frayer4->suhuFrayer->suhu_frayer_4 ?? $frayer4->suhuFrayer4->suhu_frayer_4 ?? '-' }} °C</td>
                                                <td>{{ $frayer4->waktuPenggorengan->waktu_penggorengan ?? $frayer4->waktuPenggorengan4->waktu_penggorengan_4 ?? '-' }} dtk</td>
                                                <td>{{ $frayer4->aktual_suhu_penggorengan ?? '-' }} °C</td>
                                                <td>{{ $frayer4->aktual_penggorengan }} dtk</td>
                                                <td>{{ $frayer4->tpm_minyak }} %</td>
                                                <td>-</td>
                                                <td>{{ $frayer4->keterangan ?? '-' }}</td>
                                                <!-- <td><small>{{ $frayer4->produk->nama_produk ?? '-' }}</small></td> -->
                                            </tr>
                                            @endif

                                            @if($frayer5)
                                            <tr class="text-center">
                                                <td class="font-weight-bold">Fryer 5</td>
                                                <!-- <td>{{ $frayer5->jam }}</td> -->
                                                <td>{{ $frayer5->suhuFrayer->suhu_frayer_5 ?? $frayer5->suhuFrayer5->suhu_frayer_5 ?? '-' }} °C</td>
                                                <td>{{ $frayer5->waktuPenggorengan->waktu_penggorengan ?? $frayer5->waktuPenggorengan5->waktu_penggorengan_5 ?? '-' }} dtk</td>
                                                <td>{{ $frayer5->aktual_suhu_penggorengan ?? '-' }} °C</td>
                                                <td>{{ $frayer5->aktual_penggorengan }} dtk</td>
                                                <td>{{ $frayer5->tpm_minyak }} %</td>
                                                <td>-</td>
                                                <td>{{ $frayer5->keterangan ?? '-' }}</td>
                                                <!-- <td><small>{{ $frayer5->produk->nama_produk ?? '-' }}</small></td> -->
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
                                    <!-- <div class="col-md-3"><strong>Waktu:</strong><p>{{ \Carbon\Carbon::parse($pembekuan->hasilPenggorenganData->tanggal)->format('d-m-Y') }} {{ $pembekuan->hasilPenggorenganData->jam }}</p></div> -->
                                    <!-- <div class="col-md-3"><strong>Produk:</strong><p>{{ $pembekuan->hasilPenggorenganData->produk->nama_produk ?? 'N/A' }}</p></div> -->
                                    <div class="col-md-3"><strong>Suhu Pusat Std:</strong><p>
                                        @php
                                            $stdSuhuArr = $pembekuan->hasilPenggorenganData->stdSuhuPusat->std_suhu_pusat ?? 'N/A';
                                            $finalStd = 'N/A';
                                            
                                            if (is_array($stdSuhuArr)) {
                                                // Gunakan variabel yang di-load oleh controller untuk konsistensi
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
                                    </p></div>
                                     <div class="col-md-3"><strong>Suhu Pusat Aktual:</strong>
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
                                    <div class="col-md-3"><strong>Keterangan:</strong><p>{{ $pembekuan->hasilPenggorenganData->keterangan ?? '-' }}</p></div>
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
                                            <span class="badge badge-{{ $val == '✔' ? 'warning' : 'danger' }}">{{ $val ?? '-' }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- STEP 7: PEMBEKUAN IQF --}}
                        <div class="card card-outline card-success mb-5 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-snowflake text-success mr-1"></i> Langkah 7: Pembekuan IQF</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-4 border-right">
                                        <div class="small text-muted mb-1 font-weight-bold">SUHU RUANG IQF</div>
                                        <div class="h3 font-weight-bold text-primary">{{ $pembekuan->suhu_ruang_iqf }} <small>°C</small></div>
                                    </div>
                                    <div class="col-md-4 border-right">
                                        <div class="small text-muted mb-1 font-weight-bold">HOLDING TIME</div>
                                        <div class="h3 font-weight-bold text-primary">{{ $pembekuan->holding_time }} <small>menit</small></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="small text-muted mb-1 font-weight-bold">STATUS</div>
                                        <div class="h3"><span class="badge badge-success px-4 py-2">FINISH</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<style>
    @media print {
        .main-sidebar, .main-header, .card-tools, .btn, .breadcrumb { display: none !important; }
        .content-wrapper { margin-left: 0 !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
        .bg-gradient-primary { background: #fff !important; color: #000 !important; border: 1px solid #000 !important; }
        .badge { border: 1px solid #000 !important; color: #000 !important; background: transparent !important; }
    }
</style>
@endsection