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
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="fas fa-exclamation-triangle mr-1"></i> Validasi Gagal:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @php
                    $namaProdukHeader = $pembekuan->penggorenganData->produk->nama_produk
                        ?? ($pembekuan->prosesRoastingFanData->produk->nama_produk
                        ?? ($pembekuan->hasilProsesRoastingData->produk->nama_produk ?? 'N/A'));
                @endphp
                <div class="row">
                    {{-- ===== HEADER INFO ===== --}}
                    <div class="col-12 mb-3">
                        <div class="card bg-gradient-primary shadow-sm border-0">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h3 class="mb-1 font-weight-bold">
                                            {{ $namaProdukHeader }}
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
                                            <span class="badge badge-light p-2"><i class="fas fa-user mr-1"></i>Dibuat Oleh (Produksi) : {{ $pembekuan->user->name ?? 'N/A' }}</span>
                                            @if($pembekuan->hasilProsesRoastingData)
                                                <span class="badge badge-success p-2"><i class="fas fa-check-circle mr-1"></i> Verifikasi QC Lengkap</span>
                                            @else
                                                <span class="badge badge-warning p-2"><i class="fas fa-hourglass-half mr-1"></i> Menunggu Input Verifikasi QC</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                                        @if(!empty($canEditQcVerification))
                                            <a href="#qc-verification-section" class="btn btn-warning btn-sm mr-2 font-weight-bold">
                                                <i class="fas fa-clipboard-check mr-1"></i> {{ $pembekuan->hasilProsesRoastingData ? 'Edit Verifikasi QC' : 'Input Verifikasi QC' }}
                                            </a>
                                        @endif
                                        <a href="{{ route('wizard.proses2.index') }}" class="btn btn-light btn-sm mr-2">
                                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== STEP-BY-STEP DETAILS ===== --}}
                    <div class="col-12">
                        <div class="mb-2">
                            <h5 class="font-weight-bold text-secondary"><i class="fas fa-industry mr-1"></i> A. Detail Inputan Produksi (Langkah 1 – 6)</h5>
                        </div>
                        
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

                        <div class="mt-4 mb-2" id="qc-verification-section">
                            <h5 class="font-weight-bold text-primary"><i class="fas fa-user-check mr-1"></i> B. Verifikasi Hasil Akhir Penggorengan-Roasted (Inputan QC Inspector)</h5>
                        </div>

                        {{-- STEP 7: RINGKASAN HASIL ROASTING --}}
                        @php
                            $hasilRoasting = $pembekuan->hasilProsesRoastingData;
                            $suhuPusatRaw = $hasilRoasting->aktual_suhu_pusat ?? [];
                            $suhuPusatList = [];
                            if (is_array($suhuPusatRaw)) {
                                foreach ($suhuPusatRaw as $val) {
                                    if (is_string($val) && $val !== '') {
                                        preg_match_all('/\d+[.,]\d+|\d+/', $val, $matches);
                                        foreach ($matches[0] as $match) {
                                            $suhuPusatList[] = str_replace(',', '.', $match);
                                        }
                                    } elseif (is_numeric($val)) {
                                        $suhuPusatList[] = str_replace(',', '.', (string) $val);
                                    }
                                }
                            } elseif (is_string($suhuPusatRaw) && $suhuPusatRaw !== '') {
                                preg_match_all('/\d+[.,]\d+|\d+/', $suhuPusatRaw, $matches);
                                $suhuPusatList = array_map(fn($v) => str_replace(',', '.', $v), $matches[0]);
                            }
                            $sensori = is_array($hasilRoasting?->sensori) ? $hasilRoasting->sensori : [];
                        @endphp

                        @if($hasilRoasting)
                        <div class="card card-outline card-primary mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-check-double text-primary mr-1"></i> Ringkasan Verifikasi Hasil Akhir Penggorengan-Roasted (QC)</h3>
                                <div class="card-tools">
                                    <span class="badge badge-success mr-2"><i class="fas fa-check mr-1"></i> Sudah Diinput QC</span>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 text-center">
                                    <div class="col-md-4"><strong>Suhu Pusat Std:</strong><p>{{ $hasilRoasting->stdSuhuPusat->std_suhu_pusat_roasting ?? 'N/A' }} °C</p></div>
                                    <div class="col-md-4">
                                        <strong>Suhu Produk Aktual:</strong>
                                        <p>
                                            @if(!empty($suhuPusatList))
                                                @foreach($suhuPusatList as $val)
                                                    <span class="badge badge-success p-1 mr-1">{{ $val }} °C</span>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-4"><strong>Keterangan:</strong><p>{{ $hasilRoasting->keterangan ?? '-' }}</p></div>
                                </div>
                                <hr>
                                <h6><strong>Uji Sensori Produk:</strong></h6>
                                <div class="row text-center mt-2">
                                    @php
                                        $sensories = [
                                            'Kematangan'  => $sensori['sensori_kematangan'] ?? ($hasilRoasting->sensori_kematangan ?? '-'),
                                            'Rasa'        => $sensori['sensori_rasa']       ?? ($hasilRoasting->sensori_rasa       ?? '-'),
                                            'Aroma (Bau)' => $sensori['sensori_bau']        ?? ($hasilRoasting->sensori_bau        ?? '-'),
                                            'Tekstur'     => $sensori['sensori_tekstur']    ?? ($hasilRoasting->sensori_tekstur    ?? '-'),
                                            'Kenampakan'  => $sensori['sensori_kenampakan'] ?? ($hasilRoasting->sensori_kenampakan ?? '-'),
                                            'Warna'       => $sensori['sensori_warna']      ?? ($hasilRoasting->sensori_warna      ?? '-'),
                                        ];
                                    @endphp
                                    @foreach($sensories as $label => $val)
                                    <div class="col-4 col-md-2 mb-3">
                                        <div class="small text-muted">{{ $label }}</div>
                                        <div class="font-weight-bold h5">
                                            <span class="badge badge-{{ $val == '✔' ? 'success' : ($val == '✘' ? 'danger' : 'secondary') }}">
                                                {{ $val == '✔' ? '✔ OK' : ($val == '✘' ? '✘ Tidak OK' : ($val ?? '-')) }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-warning shadow-sm mb-4">
                            <i class="fas fa-Exclamation-circle mr-1"></i>
                            <strong>Belum Ada Verifikasi Hasil Akhir Penggorengan-Roasted dari QC Inspector.</strong>
                            @if(empty($canEditQcVerification))
                                Silakan menunggu QC Inspector menginput hasil verifikasi suhu produk dan uji sensori pada halaman ini.
                            @endif
                        </div>
                        @endif

                        {{-- FORM INPUT / EDIT VERIFIKASI HASIL AKHIR PENGGORENGAN-ROASTED (KHUSUS QC INSPECTOR / ADMIN / SUPERADMIN) --}}
                        @if(!empty($canEditQcVerification))
                        <div class="card card-primary shadow-sm mb-5">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-clipboard-check mr-1"></i>
                                    {{ $hasilRoasting ? 'Perbarui Verifikasi Hasil Akhir Penggorengan-Roasted (QC Inspector)' : 'Form Input Verifikasi Hasil Akhir Penggorengan-Roasted (QC Inspector)' }}
                                </h3>
                            </div>
                            <form action="{{ route('wizard.proses2.qc-verification', $pembekuan->uuid) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label>Jam Verifikasi QC</label>
                                            <input type="time" name="jam_hasil" class="form-control" value="{{ old('jam_hasil', $hasilRoasting->jam ?? \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}">
                                        </div>
                                        <div class="col-md-8 form-group">
                                            <label>Standar Suhu Pusat Produk <span class="text-danger">*</span></label>
                                            <select name="id_std_suhu_pusat" class="form-control" required>
                                                <option value="">-- Pilih Standar Suhu Pusat --</option>
                                                @foreach($stdSuhuPusatOptions ?? [] as $std)
                                                    <option value="{{ $std->id }}" {{ old('id_std_suhu_pusat', $hasilRoasting->id_std_suhu_pusat ?? '') == $std->id ? 'selected' : '' }}>
                                                        {{ $std->std_suhu_pusat_roasting }} °C
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Input Suhu Produk (°C) <span class="text-danger">*</span></label>
                                        <div id="qc_suhu_pusat_container">
                                            @php
                                                $existingSuhuInputs = old('aktual_suhu_pusat', !empty($suhuPusatList) ? $suhuPusatList : ['']);
                                            @endphp
                                            @foreach($existingSuhuInputs as $idx => $sVal)
                                                <div class="input-group mb-2">
                                                    <input type="number" step="0.1" name="aktual_suhu_pusat[]" class="form-control" value="{{ $sVal }}" placeholder="Contoh: 75.5" required>
                                                    <div class="input-group-append">
                                                        @if($idx === 0)
                                                            <button type="button" class="btn btn-outline-success btn-add-qc-suhu"><i class="fas fa-plus"></i> Tambah Pengukuran</button>
                                                        @else
                                                            <button type="button" class="btn btn-outline-danger btn-remove-qc-suhu"><i class="fas fa-minus"></i></button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <hr>
                                    <h6 class="font-weight-bold mb-3"><i class="fas fa-check-circle text-primary mr-1"></i> Input Sensori Produk (Dropdown OK / Tidak OK)</h6>
                                    @php
                                        $sensoriFields = [
                                            'sensori_kematangan' => 'Kematangan',
                                            'sensori_rasa'       => 'Rasa',
                                            'sensori_bau'        => 'Aroma (Bau)',
                                            'sensori_tekstur'    => 'Tekstur',
                                            'sensori_kenampakan' => 'Kenampakan',
                                            'sensori_warna'      => 'Warna',
                                        ];
                                    @endphp
                                    <div class="row">
                                        @foreach($sensoriFields as $fieldKey => $fieldLabel)
                                            @php
                                                $currentVal = old($fieldKey, $sensori[$fieldKey] ?? ($hasilRoasting->{$fieldKey} ?? '✔'));
                                            @endphp
                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label>{{ $fieldLabel }} <span class="text-danger">*</span></label>
                                                <select name="{{ $fieldKey }}" class="form-control" required>
                                                    <option value="✔" {{ $currentVal == '✔' ? 'selected' : '' }}>✔ OK</option>
                                                    <option value="✘" {{ $currentVal == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="form-group mt-2">
                                        <label>Keterangan / Catatan QC</label>
                                        <textarea name="keterangan_hasil" class="form-control" rows="2" placeholder="Catatan hasil verifikasi penggorengan-roasted (opsional)">{{ old('keterangan_hasil', $hasilRoasting->keterangan ?? '') }}</textarea>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary">
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
document.addEventListener('click', function(e) {
    const addBtn = e.target.closest('.btn-add-qc-suhu');
    if (addBtn) {
        e.preventDefault();
        const container = document.getElementById('qc_suhu_pusat_container');
        if (container) {
            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `
                <input type="number" step="0.1" name="aktual_suhu_pusat[]" class="form-control" placeholder="Contoh: 75.5" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger btn-remove-qc-suhu"><i class="fas fa-minus"></i></button>
                </div>
            `;
            container.appendChild(div);
        }
    }

    const removeBtn = e.target.closest('.btn-remove-qc-suhu');
    if (removeBtn) {
        e.preventDefault();
        const group = removeBtn.closest('.input-group');
        if (group) group.remove();
    }
});
</script>

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
