@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><i class="fas fa-magic text-primary"></i> Detail Verifikasi Proses Frying Roasted Produk</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('wizard.proses2.index') }}">Frying Roasted Produk</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
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
                                            {{ $pembekuan->hasilProsesRoastingData->produk->nama_produk ?? 'N/A' }}
                                            <span class="h5">(
                                                {{ 
                                                    $pembekuan->berat_produk ?? 
                                                    ($pembekuan->penggorenganData->berat_produk ?? 
                                                    ($pembekuan->inputRoastingData->berat_produk ?? 
                                                    ($pembekuan->inputRoasting->berat_produk ?? '-'))) 
                                                }} gr)
                                            </span>
                                        </h3>
                                        <div class="d-flex flex-wrap" style="gap: 15px;">
                                            <span class="badge badge-light p-2"><i class="fas fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($pembekuan->tanggal)->format('d F Y') }}</span>
                                            <span class="badge badge-light p-2"><i class="fas fa-clock mr-1"></i> {{ $pembekuan->jam }}</span>
                                            <span class="badge badge-light p-2"><i class="fas fa-layer-group mr-1"></i>Shift {{ $pembekuan->shift_data->shift ?? ($pembekuan->shift->shift ?? 'N/A') }}</span>
                                            <span class="badge badge-light p-2"><i class="fas fa-user mr-1"></i>Dibuat Oleh : {{ $pembekuan->user->name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                                        <!-- @if(!$pembekuan->approved_by_qc && !$pembekuan->approved_by_produksi && !$pembekuan->approved_by_spv)
                                            <a href="{{ route('wizard.proses2.edit', $pembekuan->uuid) }}" class="btn btn-warning btn-sm mr-2">
                                                <i class="fas fa-edit mr-1"></i> Edit Data
                                            </a>
                                        @endif -->
                                        <a href="{{ route('wizard.proses2.index') }}" class="btn btn-light btn-sm mr-2">
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
                                    <div class="col-md-3"><strong>Kode Produksi:</strong><p>{{ $pembekuan->penggorenganData->kode_produksi }}</p></div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3"><strong>No. of Strokes:</strong><p>{{ $pembekuan->penggorenganData->no_of_strokes }}</p></div>
                                    <div class="col-md-3"><strong>Pukul:</strong><p>{{ $pembekuan->penggorenganData->waktu_pemasakan }}</p></div>
                                    <div class="col-md-3"><strong>Hasil Pencetakan:</strong><p><span class="badge badge-{{ $pembekuan->penggorenganData->hasil_pencetakan == '✔' ? 'warning' : 'danger' }} p-2">{{ $pembekuan->penggorenganData->hasil_pencetakan }}</span></p></div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="card card-outline card-secondary mb-4 shadow-sm collapsed-card">
                            <div class="card-header bg-light text-muted">
                                <h3 class="card-title"><i class="fas fa-fire mr-1"></i> Langkah 1: Pencetakan (Dilewati)</h3>
                                <div class="card-tools">
                                    <span class="badge badge-light border">Bukan Alur Penggorengan</span>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- STEP 2: PREDUST --}}
                        @if($pembekuan->predustData)
                        <div class="card card-outline card-primary mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-mortar-pestle text-primary mr-1"></i> Langkah 2: Predust</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- <div class="col-md-3"><strong>Waktu:</strong><p>{{ \Carbon\Carbon::parse($pembekuan->predustData->tanggal)->format('d-m-Y') }} {{ $pembekuan->predustData->jam }}</p></div> -->
                                    <div class="col-md-2"><strong>Jenis Predust:</strong><p>{{ $pembekuan->predustData->jenisPredust->jenis_predust ?? 'N/A' }}</p></div>
                                    <div class="col-md-2"><strong>Kode Produksi:</strong><p>{{ $pembekuan->predustData->kode_produksi }}</p></div>
                                    <div class="col-md-2"><strong>Hasil Predust:</strong><p><span class="badge badge-{{ $pembekuan->predustData->kondisi_predust == '✔' ? 'warning' : 'danger' }} p-2">{{ $pembekuan->predustData->kondisi_predust }}</span></p></div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="card card-outline card-secondary mb-4 shadow-sm collapsed-card">
                            <div class="card-header bg-light text-muted">
                                <h3 class="card-title"><i class="fas fa-mortar-pestle mr-1"></i> Langkah 2: Predust (Dilewati)</h3>
                                <div class="card-tools">
                                    <span class="badge badge-light border">Bukan Alur Penggorengan</span>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                                </div>
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
                                    <div class="col-md-6"><strong>jenis Better & Kode Produksi Better:</strong><p>{!! $pembekuan->batteringData->better_names_with_codes_html !!}</p></div>
                                    <div class="col-md-2"><strong>Hasil Better:</strong><p><span class="badge badge-{{ $pembekuan->batteringData->hasil_better == '✔' ? 'warning' : 'danger' }} p-2">{{ $pembekuan->batteringData->hasil_better }}</span></p></div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="card card-outline card-secondary mb-4 shadow-sm collapsed-card">
                            <div class="card-header bg-light text-muted">
                                <h3 class="card-title"><i class="fas fa-tint mr-1"></i> Langkah 3: Battering (Dilewati)</h3>
                                <div class="card-tools">
                                    <span class="badge badge-light border">Bukan Alur Penggorengan</span>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
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
                                    <div class="col-md-6"><strong>Jenis Breader & Kode Produksi Breader</strong><p>{!! $pembekuan->breaderData->breader_names_with_codes_html !!}</p></div>
                                    <div class="col-md-2"><strong>Hasil Breader:</strong><p><span class="badge badge-{{ $pembekuan->breaderData->hasil_breader == '✔' ? 'warning' : 'danger' }} p-2">{{ $pembekuan->breaderData->hasil_breader }}</span></p></div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="card card-outline card-secondary mb-4 shadow-sm collapsed-card">
                            <div class="card-header bg-light text-muted">
                                <h3 class="card-title"><i class="fas fa-layer-group mr-1"></i> Langkah 4: Breader (Dilewati)</h3>
                                <div class="card-tools">
                                    <span class="badge badge-light border">Bukan Alur Penggorengan</span>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- STEP 5: FRAYER (multiple possible) --}}
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
                                                <th>Waktu</th>
                                                <th>Suhu Std</th>
                                                <th>Waktu Std</th>
                                                <th>Aktual Suhu</th>
                                                <th>Aktual Waktu</th>
                                                <th>TPM</th>
                                                <th>Keterangan</th>
                                                <!-- <th>Produk</th> -->
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
                                                <tr class="text-center">
                                                    <td class="font-weight-bold">{{ $label }}</td>
                                                    <td>{{ $fData->jam }}</td>
                                                    <td>{{ $suhuVal }} °C</td>
                                                    <td>{{ $waktuVal }} dtk</td>
                                                    <td>{{ $fData->aktual_suhu_penggorengan ?? '-' }} °C</td>
                                                    <td>{{ $fData->aktual_penggorengan }} dtk</td>
                                                    <td>{{ $fData->tpm_minyak }} %</td>
                                                    <td>{{ $fData->keterangan ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- STEP 6: ROASTING FAN --}}
                        @if($pembekuan->prosesRoastingFanData)
                        <div class="card card-outline card-primary mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-fan text-primary mr-1"></i> Langkah 6: Roasting Fan</h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <!-- <div class="col-md-3"><strong>Waktu:</strong><p>{{ \Carbon\Carbon::parse($pembekuan->prosesRoastingFanData->tanggal)->format('d-m-Y') }} {{ $pembekuan->prosesRoastingFanData->jam }}</p></div> -->
                                    <!-- <div class="col-md-3"><strong>Produk:</strong><p>{{ $pembekuan->prosesRoastingFanData->produk->nama_produk ?? 'N/A' }}</p></div> -->
                                    <div class="col-md-3"><strong>Waktu Pemasakan (pukul):</strong><p>{{ $pembekuan->prosesRoastingFanData->aktual_waktu ?? '-' }} </p></div>
                                    <div class="col-md-3"><strong>Waktu Pengecekan (menit):</strong><p>{{ $pembekuan->prosesRoastingFanData->waktu_pemasakan ?? '-' }} </p></div>
                                </div>

                                @if($pembekuan->prosesRoastingFanData->is_grouped && $pembekuan->prosesRoastingFanData->blok_data)
                                    @php
                                        $blockData = $pembekuan->prosesRoastingFanData->blok_data;
                                        if (is_string($blockData)) {
                                            $blockData = json_decode($blockData, true) ?: [];
                                        }
                                        $sortedBlok = collect($blockData)->sortBy('block_number');
                                    @endphp
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm text-center">
                                            <thead class="bg-dark text-white">
                                                <tr>
                                                    <th style="width: 200px;" class="text-left">Parameter</th>
                                                    @foreach($sortedBlok as $blok)
                                                        <th>Blok {{ $blok['block_number'] ?? ($loop->index + 1) }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Standar Suhu Pemasakan --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Standar Suhu Pemasakan (°C)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        @php
                                                            $stdSuhu = \App\Models\SuhuBlok::find($blok['id_suhu_blok']);
                                                        @endphp
                                                        <td><span class="badge badge-secondary">{{ $stdSuhu->suhu_blok ?? '-' }} °C</span></td>
                                                    @endforeach
                                                </tr>
                                                {{-- Aktual Suhu Pemasakan --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Suhu Pemasakan (°C)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        <td><span class="badge badge-primary">{{ $blok['suhu_roasting'] ?? '-' }} °C</span></td>
                                                    @endforeach
                                                </tr>
                                                
                                                {{-- Standar Fan 1 --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Standar Fan 1 (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        @php
                                                            $stdFan = \App\Models\StdFan::find($blok['id_std_fan']);
                                                        @endphp
                                                        <td><span class="badge badge-secondary">{{ $stdFan->std_fan ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>
                                                {{-- Aktual Fan 1 --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Fan 1 (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        <td><span class="badge badge-success">{{ $blok['fan_1'] ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>

                                                {{-- Standar Fan 2 --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Standar Fan 2 (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        @php
                                                            $stdFan = \App\Models\StdFan::find($blok['id_std_fan']);
                                                        @endphp
                                                        <td><span class="badge badge-secondary">{{ $stdFan->std_fan_2 ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>
                                                {{-- Aktual Fan 2 --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Fan 2 (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        <td><span class="badge badge-success">{{ $blok['fan_2'] ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>

                                                {{-- Standar Fan 3 --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Standar Fan 3 (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        @php
                                                            $stdFan = \App\Models\StdFan::find($blok['id_std_fan']);
                                                        @endphp
                                                        <td><span class="badge badge-secondary">{{ $stdFan->fan_3 ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>
                                                {{-- Aktual Fan 3 --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Fan 3 (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        <td><span class="badge badge-success">{{ $blok['fan_3'] ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>

                                                {{-- Standar Fan 4 --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Standar Fan 4 (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        @php
                                                            $stdFan = \App\Models\StdFan::find($blok['id_std_fan']);
                                                        @endphp
                                                        <td><span class="badge badge-secondary">{{ $stdFan->fan_4 ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>
                                                {{-- Aktual Fan 4 --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Fan 4 (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        <td><span class="badge badge-success">{{ $blok['fan_4'] ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>

                                                {{-- Standar Humidity --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Standar Humidity (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        @php
                                                            $stdFan = \App\Models\StdFan::find($blok['id_std_fan']);
                                                        @endphp
                                                        <td><span class="badge badge-secondary">{{ $stdFan->std_humadity ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>
                                                {{-- Aktual Humidity - Merged Infeed & Outfeed --}}
                                                @php
                                                    $mergedHumidity = null;
                                                    $mergedInfraRed = null;
                                                    foreach($sortedBlok as $b) {
                                                        if(in_array($b['block_number'] ?? '', ['INFEED','OUTFEED'])) {
                                                            if(is_null($mergedHumidity)) $mergedHumidity = $b['aktual_humadity'] ?? null;
                                                            if(is_null($mergedInfraRed)) $mergedInfraRed = $b['infra_red'] ?? null;
                                                        }
                                                    }
                                                    $skipOutfeedH = false;
                                                    $skipOutfeedI = false;
                                                @endphp
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Humidity/Steam (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        @if(($blok['block_number'] ?? '') === 'INFEED')
                                                            <td colspan="2">{{ $mergedHumidity ?? '-' }} %</td>
                                                            @php $skipOutfeedH = true; @endphp
                                                        @elseif(($blok['block_number'] ?? '') === 'OUTFEED' && $skipOutfeedH)
                                                            {{-- merged --}}
                                                        @else
                                                            <td>{{ $blok['aktual_humadity'] ?? '-' }} %</td>
                                                        @endif
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Infra Red</td>
                                                    @foreach($sortedBlok as $blok)
                                                        @if(($blok['block_number'] ?? '') === 'INFEED')
                                                            <td colspan="2">{{ $mergedInfraRed ?? '-' }}</td>
                                                            @php $skipOutfeedI = true; @endphp
                                                        @elseif(($blok['block_number'] ?? '') === 'OUTFEED' && $skipOutfeedI)
                                                            {{-- merged --}}
                                                        @else
                                                            <td>{{ $blok['infra_red'] ?? '-' }}</td>
                                                        @endif
                                                    @endforeach
                                                </tr>
                                                {{-- Standar Lama Proses --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Standar Lama Proses (Menit)</td>
                                                    @php
                                                        $firstBlok = $sortedBlok->first();
                                                        $stdLama = $firstBlok ? \App\Models\StdFan::find($firstBlok['id_std_fan']) : null;
                                                    @endphp
                                                    <td colspan="{{ count($sortedBlok) }}">
                                                        <span class="badge badge-secondary">{{ $stdLama->std_lama_proses ?? '-' }} Menit</span>
                                                    </td>
                                                </tr>
                                                {{-- Aktual Lama Proses --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Lama Proses (Display Aktual; Menit)</td>
                                                    <td colspan="{{ count($sortedBlok) }}">
                                                        <span class="badge badge-primary">{{ $pembekuan->prosesRoastingFanData->aktual_lama_proses ?? '-' }} Menit</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    {{-- Format Lama (Single) --}}
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm text-center">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Parameter</th>
                                                    <th>Aktual</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-left font-weight-bold">Suhu Pemasakan (°C)</td>
                                                    <td><span class="badge badge-primary">{{ $pembekuan->prosesRoastingFanData->aktual_suhu_fan }} °C</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left font-weight-bold">Fan 1 (%)</td>
                                                    <td><span class="badge badge-success">{{ $pembekuan->prosesRoastingFanData->aktual_speed_fan }} %</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left font-weight-bold">Humidity/Steam Valve (%)</td>
                                                    <td>{{ $pembekuan->prosesRoastingFanData->aktual_humadity ?? '-' }} %</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left font-weight-bold">Infra Red</td>
                                                    <td>{{ $pembekuan->prosesRoastingFanData->infra_red ?? '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- STEP 7: HASIL ROASTING --}}
                        @if($pembekuan->hasilProsesRoastingData)
                        <div class="card card-outline card-primary mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-check-double text-primary mr-1"></i> Langkah 7: Hasil Roasting</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 text-center">
                                    <!-- <div class="col-md-3"><strong>Waktu:</sstrong><p>{{ \Carbon\Carbon::parse($pembekuan->hasilProsesRoastingData->tanggal)->format('d-m-Y') }} {{ $pembekuan->hasilProsesRoastingData->jam }}</p></div> -->
                                    <!-- <div class="col-md-3"><strong>Produk:</strong><p>{{ $pembekuan->hasilProsesRoastingData->produk->nama_produk ?? 'N/A' }}</p></div> -->
                                    <div class="col-md-3"><strong>Suhu Pusat Std:</strong><p>{{ $pembekuan->hasilProsesRoastingData->stdSuhuPusat->std_suhu_pusat_roasting ?? 'N/A' }} °C</p></div>
                                    <div class="col-md-3">
                                        <strong>Suhu Pusat Aktual:</strong>
                                        <p>
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
                                            @if(!empty($suhuPusatList))
                                                @foreach($suhuPusatList as $val)
                                                    <span class="badge badge-success p-1 mr-1">{{ $val }} °C</span>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-3"><strong>Keterangan:</strong><p>{{ $pembekuan->hasilProsesRoastingData->keterangan ?? '-' }}</p></div>
                                </div>
                                <hr>
                                <h6><strong>Uji Sensori:</strong></h6>
                                <div class="row text-center mt-2">
                                    @php
                                        $sensori = $pembekuan->hasilProsesRoastingData->sensori;
                                        $sensories = [
                                            'Kematangan' => $sensori['sensori_kematangan'] ?? ($pembekuan->hasilProsesRoastingData->sensori_kematangan ?? '-'),
                                            'Kenampakan' => $sensori['sensori_kenampakan'] ?? ($pembekuan->hasilProsesRoastingData->sensori_kenampakan ?? '-'),
                                            'Warna'      => $sensori['sensori_warna']      ?? ($pembekuan->hasilProsesRoastingData->sensori_warna      ?? '-'),
                                            'Rasa'       => $sensori['sensori_rasa']       ?? ($pembekuan->hasilProsesRoastingData->sensori_rasa       ?? '-'),
                                            'Bau'        => $sensori['sensori_bau']        ?? ($pembekuan->hasilProsesRoastingData->sensori_bau        ?? '-'),
                                            'Tekstur'    => $sensori['sensori_tekstur']    ?? ($pembekuan->hasilProsesRoastingData->sensori_tekstur    ?? '-'),
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

                        {{-- STEP 8: IQF ROASTING --}}
                        <div class="card card-outline card-success mb-5 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-snowflake text-success mr-1"></i> Langkah 8: IQF Roasting</h3>
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
