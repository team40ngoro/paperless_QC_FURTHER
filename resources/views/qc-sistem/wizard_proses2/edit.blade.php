@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-edit text-primary"></i> Edit Verifikasi Proses Frying Roasted Produk</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('wizard.proses2.index') }}">Wizard Proses 2</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <form action="{{ route('wizard.proses2.update', $pembekuan->uuid) }}" method="POST" id="main-edit-form">
                @csrf
                @method('PUT')

                {{-- SECTION: INFO UTAMA & IQF --}}
                <div class="card card-dark card-outline shadow-sm mb-4">
                    <div class="card-header bg-dark">
                        <h3 class="card-title font-weight-bold text-white"><i class="fas fa-snowflake mr-1"></i> Info Utama & IQF</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nama Produk <span class="text-danger">*</span></label>
                                    @php
                                        // Cari id_produk dari berbagai relasi (fallback)
                                        $fallbackIdProduk = $pembekuan->penggorenganData->id_produk 
                                            ?? ($pembekuan->prosesRoastingFanData->id_produk 
                                            ?? ($pembekuan->hasilProsesRoastingData->id_produk ?? null));
                                    @endphp
                                    <select name="id_produk" class="form-control select2-field" required disabled>
                                        @foreach($produks as $p)
                                            <option value="{{ $p->id }}" {{ $fallbackIdProduk == $p->id ? 'selected' : '' }}>{{ $p->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="id_produk" value="{{ $fallbackIdProduk }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tanggal <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-light" value="{{ \Carbon\Carbon::parse($pembekuan->tanggal)->format('d-m-Y H:i:s') }}" readonly>
                                    <input type="hidden" name="tanggal" value="{{ \Carbon\Carbon::parse($pembekuan->tanggal)->format('d-m-Y H:i:s') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jam <span class="text-danger">*</span></label>
                                    <input type="time" name="jam" class="form-control" value="{{ $pembekuan->jam }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Suhu Ruang IQF (°C) <span class="text-danger">*</span></label>
                                    <input type="text" name="suhu_ruang_iqf" class="form-control" value="{{ $pembekuan->suhu_ruang_iqf }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Holding Time (Menit) <span class="text-danger">*</span></label>
                                    <input type="text" name="holding_time" class="form-control" value="{{ $pembekuan->holding_time }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 1: PENGGORENGAN --}}
                <div class="card {{ $pembekuan->penggorenganData ? 'card-primary' : 'card-secondary collapsed-card' }} card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold {{ $pembekuan->penggorenganData ? 'text-primary' : 'text-muted' }}">
                            <i class="fas fa-compact-disc mr-1"></i> 1. Pencetakan {{ $pembekuan->penggorenganData ? '' : '(Dilewati)' }}
                        </h3>
                        <div class="card-tools">
                            @if(!$pembekuan->penggorenganData) <span class="badge badge-secondary mr-2">Tidak Ada Data</span> @endif
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-{{ $pembekuan->penggorenganData ? 'minus' : 'plus' }}"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($pembekuan->penggorenganData)
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Shift <span class="text-danger">*</span></label>
                                        <select name="shift_id" class="form-control" required>
                                            @foreach($shifts as $s)
                                                <option value="{{ $s->id }}" {{ $pembekuan->penggorenganData->shift_id == $s->id ? 'selected' : '' }}>{{ $s->shift }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Kode Produksi <span class="text-danger">*</span></label>
                                        <input type="text" name="kode_produksi" class="form-control" value="{{ $pembekuan->penggorenganData->kode_produksi }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Berat Produk (gr) <span class="text-danger">*</span></label>
                                        <select name="berat_produk" id="nilai_select_berat_penggorengan" class="form-control select2" data-nilai-berat="true" data-selected="{{ $pembekuan->penggorenganData->berat_produk }}"></select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>No Of Strokes <span class="text-danger">*</span></label>
                                        <input type="text" name="no_of_strokes" class="form-control" value="{{ $pembekuan->penggorenganData->no_of_strokes }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Pukul <span class="text-danger">*</span></label>
                                        <input type="text" name="waktu_pemasakan" class="form-control" value="{{ $pembekuan->penggorenganData->waktu_pemasakan }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Hasil Pencetakan <span class="text-danger">*</span></label>
                                        <select name="hasil_pencetakan" class="form-control" required>
                                            <option value="✔" {{ $pembekuan->penggorenganData->hasil_pencetakan == '✔' ? 'selected' : '' }}>✔ OK</option>
                                            <option value="✘" {{ $pembekuan->penggorenganData->hasil_pencetakan == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted italic mb-0">Langkah ini dilewati saat pembuatan data.</p>
                        @endif
                    </div>
                </div>

                {{-- SECTION 2: PREDUST --}}
                <div class="card {{ $pembekuan->predustData ? 'card-info' : 'card-secondary collapsed-card' }} card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold {{ $pembekuan->predustData ? 'text-info' : 'text-muted' }}">
                            <i class="fas fa-mortar-pestle mr-1"></i> 2. Predust {{ $pembekuan->predustData ? '' : '(Dilewati)' }}
                        </h3>
                        <div class="card-tools">
                            @if(!$pembekuan->predustData) <span class="badge badge-secondary mr-2">Tidak Ada Data</span> @endif
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-{{ $pembekuan->predustData ? 'minus' : 'plus' }}"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($pembekuan->predustData)
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Jenis Predust <span class="text-danger">*</span></label>
                                        <select name="id_jenis_predust_disabled" class="form-control select2-field" disabled>
                                            <option value="">Pilih Jenis Predust</option>
                                            @foreach($predustMaster as $pm)
                                                <option value="{{ $pm->id }}" {{ $pembekuan->predustData->id_jenis_predust == $pm->id ? 'selected' : '' }}>{{ $pm->jenis_predust }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="id_jenis_predust" value="{{ $pembekuan->predustData->id_jenis_predust }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Kode Produksi <span class="text-danger">*</span></label>
                                        <input type="text" name="kode_produksi_predust" class="form-control" value="{{ $pembekuan->predustData->kode_produksi }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Kondisi Predust <span class="text-danger">*</span></label>
                                        <select name="kondisi_predust" class="form-control">
                                            <option value="✔" {{ $pembekuan->predustData->kondisi_predust == '✔' ? 'selected' : '' }}>✔ Baik</option>
                                            <option value="✘" {{ $pembekuan->predustData->kondisi_predust == '✘' ? 'selected' : '' }}>✘ Tidak Baik</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted italic mb-0">Langkah ini dilewati saat pembuatan data.</p>
                        @endif
                    </div>
                </div>

                {{-- SECTION 3: BATTERING --}}
                <div class="card {{ $pembekuan->batteringData ? 'card-warning' : 'card-secondary collapsed-card' }} card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold {{ $pembekuan->batteringData ? 'text-warning' : 'text-muted' }}">
                            <i class="fas fa-tint mr-1"></i> 3. Battering {{ $pembekuan->batteringData ? '' : '(Dilewati)' }}
                        </h3>
                        <div class="card-tools">
                            @if(!$pembekuan->batteringData) <span class="badge badge-secondary mr-2">Tidak Ada Data</span> @endif
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-{{ $pembekuan->batteringData ? 'minus' : 'plus' }}"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($pembekuan->batteringData)
                            <div class="row">
                                <!-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jenis Better <span class="text-danger">*</span></label>
                                        @php
                                            $selectedBetter = $pembekuan->batteringData->id_jenis_better 
                                                ? array_map('trim', explode(',', $pembekuan->batteringData->id_jenis_better)) 
                                                : [];
                                        @endphp
                                        <select name="id_jenis_better[]" class="form-control select2" multiple="multiple" disabled>
                                            @foreach($jenisBetters as $jb)
                                                <option value="{{ $jb->id }}" {{ in_array($jb->id, $selectedBetter) ? 'selected' : '' }}>{{ $jb->nama_formula_better ?? $jb->nama_better ?? $jb->jenis_better }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="id_jenis_better" value="{{ $pembekuan->batteringData->id_jenis_better }}">
                                    </div>
                                </div> -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kode Produksi Better <span class="text-danger">*</span></label>
                                        <div id="kode_produksi_better_container">
                                            @php
                                                $codes = $pembekuan->batteringData->kode_produksi_better;
                                                $selectedBetterIds = array_map('trim', explode(',', $pembekuan->batteringData->id_jenis_better));
                                            @endphp
                                            @if(is_array($codes))
                                                @foreach($jenisBetters as $jb)
                                                    @if(in_array($jb->id, $selectedBetterIds))
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" style="min-width: 150px;">{{ $jb->nama_formula_better ?? $jb->nama_better ?? $jb->jenis_better }}</span>
                                                            </div>
                                                            <input type="text" name="kode_produksi_better[{{ $jb->id }}]" class="form-control" value="{{ $codes[$jb->id] ?? '' }}" required>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="text" name="kode_produksi_better" class="form-control" value="{{ $codes }}" required>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Hasil Better <span class="text-danger">*</span></label>
                                        <select name="hasil_better" class="form-control" required>
                                            <option value="✔" {{ $pembekuan->batteringData->hasil_better == '✔' ? 'selected' : '' }}>✔ Baik</option>
                                            <option value="✘" {{ $pembekuan->batteringData->hasil_better == '✘' ? 'selected' : '' }}>✘ Tidak Baik</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted italic mb-0">Langkah ini dilewati saat pembuatan data.</p>
                        @endif
                    </div>
                </div>

                {{-- SECTION 4: BREADER --}}
                <div class="card {{ $pembekuan->breaderData ? 'card-success' : 'card-secondary collapsed-card' }} card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold {{ $pembekuan->breaderData ? 'text-success' : 'text-muted' }}">
                            <i class="fas fa-layer-group mr-1"></i> 4. Breader {{ $pembekuan->breaderData ? '' : '(Dilewati)' }}
                        </h3>
                        <div class="card-tools">
                            @if(!$pembekuan->breaderData) <span class="badge badge-secondary mr-2">Tidak Ada Data</span> @endif
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-{{ $pembekuan->breaderData ? 'minus' : 'plus' }}"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($pembekuan->breaderData)
                            <div class="row">
                                <!-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jenis Breader <span class="text-danger">*</span></label>
                                        @php
                                            $selectedBreader = $pembekuan->breaderData->id_jenis_breader 
                                                ? array_map('trim', explode(',', $pembekuan->breaderData->id_jenis_breader)) 
                                                : [];
                                        @endphp
                                        <select name="id_jenis_breader[]" class="form-control select2" multiple="multiple" disabled>
                                            @foreach($jenisBreaders as $jb)
                                                <option value="{{ $jb->id }}" {{ in_array($jb->id, $selectedBreader) ? 'selected' : '' }}>{{ $jb->jenis_breader }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="id_jenis_breader" value="{{ $pembekuan->breaderData->id_jenis_breader }}">
                                    </div>
                                </div> -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kode Produksi Breader <span class="text-danger">*</span></label>
                                        <div id="kode_produksi_breader_container">
                                            @php
                                                $codesB = $pembekuan->breaderData->kode_produksi;
                                                $selectedBreaderIds = array_map('trim', explode(',', $pembekuan->breaderData->id_jenis_breader));
                                            @endphp
                                            @if(is_array($codesB))
                                                @foreach($jenisBreaders as $jbr)
                                                    @if(in_array($jbr->id, $selectedBreaderIds))
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" style="min-width: 150px;">{{ $jbr->jenis_breader }}</span>
                                                            </div>
                                                            <input type="text" name="kode_produksi_breader[{{ $jbr->id }}]" class="form-control" value="{{ $codesB[$jbr->id] ?? '' }}" required>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="text" name="kode_produksi_breader" class="form-control" value="{{ $codesB }}" required>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Hasil Breader <span class="text-danger">*</span></label>
                                        <select name="hasil_breader" class="form-control" required>
                                            <option value="✔" {{ $pembekuan->breaderData->hasil_breader == '✔' ? 'selected' : '' }}>✔ Baik</option>
                                            <option value="✘" {{ $pembekuan->breaderData->hasil_breader == '✘' ? 'selected' : '' }}>✘ Tidak Baik</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted italic mb-0">Langkah ini dilewati saat pembuatan data.</p>
                        @endif
                    </div>
                </div>

                {{-- SECTION 5: FRYERS (1-5) --}}
                <div class="card card-danger card-outline shadow-sm mb-4">
                    <div class="card-header bg-danger">
                        <h3 class="card-title font-weight-bold text-white"><i class="fas fa-burn mr-1"></i> 5. Data Fryers</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0 text-sm">
                                <thead class="bg-light">
                                    <tr class="text-center">
                                        <th style="width: 100px;">Fryer</th>
                                        <th>Jam</th>
                                        <th>Suhu Std (°C)</th>
                                        <th>Waktu Std (detik)</th>
                                        <th>Aktual Suhu (°C)</th>
                                        <th>Aktual Waktu (detik)</th>
                                        <th>TPM Minyak (%)</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($frayerList as $num => $fInfo)
                                    @php 
                                        $f = $fInfo['data']; 
                                        $suhuOptions = $fInfo['suhu'];
                                        $waktuOptions = $fInfo['waktu'];
                                        $suhuKey = $fInfo['suhu_key'];
                                        $waktuKey = $fInfo['waktu_key'];
                                    @endphp
                                    @if($f)
                                    <tr>
                                        <td class="text-center font-weight-bold align-middle">Fryer {{ $num }}</td>
                                        <td><input type="time" name="frayer_{{ $num }}_jam" class="form-control form-control-sm" value="{{ $f->jam }}"></td>
                                        <td>
                                            <select name="frayer_{{ $num }}_suhu_disabled" class="form-control form-control-sm" disabled>
                                                <option value="">Pilih Suhu</option>
                                                @foreach($suhuOptions as $s)
                                                    @php 
                                                        $valSuhu = $s->suhu_frayer ?? ($s->suhu_frayer_2 ?? ($s->suhu_frayer_3 ?? ($s->suhu_frayer_4 ?? ($s->suhu_frayer_5 ?? ''))));
                                                    @endphp
                                                    <option value="{{ $s->id }}" {{ ($f->$suhuKey ?? '') == $s->id ? 'selected' : '' }}>{{ $valSuhu }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="frayer_{{ $num }}_suhu" value="{{ $f->$suhuKey ?? '' }}">
                                        </td>
                                        <td>
                                            <select name="frayer_{{ $num }}_waktu_disabled" class="form-control form-control-sm" disabled>
                                                <option value="">Pilih Waktu</option>
                                                @foreach($waktuOptions as $w)
                                                    <option value="{{ $w->id }}" {{ ($f->$waktuKey ?? '') == $w->id ? 'selected' : '' }}>{{ $w->waktu_penggorengan ?? ($w->waktu_penggorengan_2 ?? '') }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="frayer_{{ $num }}_waktu" value="{{ $f->$waktuKey ?? '' }}">
                                        </td>
                                        <td><input type="text" name="frayer_{{ $num }}_aktual_suhu" class="form-control form-control-sm" value="{{ $f->aktual_suhu_penggorengan }}"></td>
                                        <td><input type="text" name="frayer_{{ $num }}_aktual_waktu" class="form-control form-control-sm" value="{{ $f->aktual_penggorengan }}"></td>
                                        <td><input type="text" name="frayer_{{ $num }}_tpm" class="form-control form-control-sm" value="{{ $f->tpm_minyak }}"></td>
                                        <td><input type="text" name="frayer_{{ $num }}_keterangan" class="form-control form-control-sm" value="{{ $f->keterangan ?? '' }}"></td>
                                    </tr>
                                    @else
                                    <tr class="text-muted bg-light">
                                        <td class="text-center">Fryer {{ $num }}</td>
                                        <td colspan="6" class="text-center font-italic small">Data tidak tersedia / dilewati</td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- SECTION 6: ROASTING FAN --}}
                <div class="card {{ $pembekuan->prosesRoastingFanData ? 'card-primary' : 'card-secondary collapsed-card' }} card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold {{ $pembekuan->prosesRoastingFanData ? 'text-primary' : 'text-muted' }}">
                            <i class="fas fa-fan mr-1"></i> 6. Roasting Fan {{ $pembekuan->prosesRoastingFanData ? '' : '(Dilewati)' }}
                        </h3>
                        <div class="card-tools">
                            @if(!$pembekuan->prosesRoastingFanData) <span class="badge badge-secondary mr-2">Tidak Ada Data</span> @endif
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-{{ $pembekuan->prosesRoastingFanData ? 'minus' : 'plus' }}"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($pembekuan->prosesRoastingFanData)
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Jam</label>
                                        <input type="time" name="jam_roasting" class="form-control" value="{{ $pembekuan->prosesRoastingFanData->jam }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Waktu Pengecekan (menit)</label>
                                        <input type="text" name="waktu_pemasakan_roasting" class="form-control" value="{{ $pembekuan->prosesRoastingFanData->waktu_pemasakan }}">
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm text-center">
                                    <thead class="bg-dark text-white">
                                        <tr>
                                            <th style="width: 250px;" class="text-left">Parameter</th>
                                            @php
                                                $blockData = $pembekuan->prosesRoastingFanData->blok_data;
                                                if (is_string($blockData)) {
                                                    $blockData = json_decode($blockData, true) ?: [];
                                                }
                                                $sortedBlok = collect($blockData)->sortBy('block_number');
                                            @endphp
                                            @foreach($sortedBlok as $blok)
                                                <th>
                                                    Blok {{ $blok['block_number'] ?? ($loop->index + 1) }}
                                                    <input type="hidden" name="block_number[]" value="{{ $blok['block_number'] ?? ($loop->index + 1) }}">
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-left font-weight-bold bg-light">Standar Suhu Pemasakan (°C)</td>
                                            @foreach($sortedBlok as $blok)
                                            <td>
                                                <select class="form-control form-control-sm text-center" disabled>
                                                    <option value="">Pilih</option>
                                                    @foreach($suhuBloks as $sb)
                                                        <option value="{{ $sb->id }}" {{ ($blok['id_suhu_blok'] ?? '') == $sb->id ? 'selected' : '' }}>{{ $sb->suhu_blok }} °C</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="id_suhu_blok[]" value="{{ $blok['id_suhu_blok'] ?? '' }}">
                                                <input type="hidden" name="id_std_fan[]" value="{{ $blok['id_std_fan'] ?? '' }}">
                                            </td>
                                            @endforeach
                                        </tr>
                                        {{-- AKTUAL SUHU --}}
                                        <tr>
                                            <td class="text-left font-weight-bold bg-light">Suhu Pemasakan (Setting/Aktual) (°C)</td>
                                            @foreach($sortedBlok as $blok)
                                            <td>
                                                <input type="text" name="suhu_roasting[]" class="form-control form-control-sm text-center" value="{{ $blok['suhu_roasting'] ?? '' }}">
                                            </td>
                                            @endforeach
                                        </tr>

                                        {{-- INDIVIDUAL FAN ROWS --}}
                                        @php
                                            $fanParams = [
                                                ['label' => 'Fan 1', 'field' => 'fan_1', 'std' => 'std_fan'],
                                                ['label' => 'Fan 2', 'field' => 'fan_2', 'std' => 'std_fan_2'],
                                                ['label' => 'Fan 3', 'field' => 'fan_3', 'std' => 'fan_3'],
                                                ['label' => 'Fan 4', 'field' => 'fan_4', 'std' => 'fan_4'],
                                            ];
                                        @endphp

                                        @foreach($fanParams as $param)
                                        <tr class="table-info-light">
                                            <td class="text-left font-weight-bold">Standar {{ $param['label'] }} (%)</td>
                                            @foreach($sortedBlok as $blok)
                                                @php
                                                    $stdVal = '-';
                                                    if(!empty($blok['id_std_fan'])) {
                                                        $sfRecord = $stdFans->firstWhere('id', $blok['id_std_fan']);
                                                        $stdVal = $sfRecord ? ($sfRecord->{$param['std']} . ' %') : '-';
                                                    }
                                                @endphp
                                                <td class="bg-light text-center"><code>{{ $stdVal }}</code></td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td class="text-left font-weight-bold">{{ $param['label'] }} (Aktual) (%)</td>
                                            @foreach($sortedBlok as $blok)
                                            <td>
                                                <input type="text" name="{{ $param['field'] }}[]" class="form-control form-control-sm text-center" value="{{ $blok[$param['field']] ?? '' }}">
                                            </td>
                                            @endforeach
                                        </tr>
                                        @endforeach

                                        {{-- HUMIDITY --}}
                                        <tr class="table-info-light">
                                            <td class="text-left font-weight-bold">Standar Humidity (%)</td>
                                            @foreach($sortedBlok as $blok)
                                                @php
                                                    $stdVal = '-';
                                                    if(!empty($blok['id_std_fan'])) {
                                                        $sfRecord = $stdFans->firstWhere('id', $blok['id_std_fan']);
                                                        $stdVal = $sfRecord ? ($sfRecord->std_humadity . ' %') : '-';
                                                    }
                                                @endphp
                                                <td class="bg-light text-center"><code>{{ $stdVal }}</code></td>
                                            @endforeach
                                        </tr>
                                        @php
                                            $mergedHumEdit2 = null;
                                            $mergedIrEdit2 = null;
                                            foreach($sortedBlok as $b) {
                                                if(in_array($b['block_number'] ?? '', ['INFEED','OUTFEED'])) {
                                                    if(is_null($mergedHumEdit2)) $mergedHumEdit2 = $b['aktual_humadity'] ?? '';
                                                    if(is_null($mergedIrEdit2)) $mergedIrEdit2 = $b['infra_red'] ?? '';
                                                }
                                            }
                                            $edit2SkipH = false;
                                            $edit2SkipI = false;
                                        @endphp
                                        <tr>
                                            <td class="text-left font-weight-bold">Aktual Humidity/Steam Valve (%)</td>
                                            @foreach($sortedBlok as $blok)
                                                @if(($blok['block_number'] ?? '') === 'INFEED')
                                                    <td colspan="2">
                                                        <input type="text" name="aktual_humadity_infeed_outfeed" class="form-control form-control-sm text-center" value="{{ $mergedHumEdit2 }}">
                                                    </td>
                                                    @php $edit2SkipH = true; @endphp
                                                @elseif(($blok['block_number'] ?? '') === 'OUTFEED' && $edit2SkipH)
                                                    {{-- merged into INFEED --}}
                                                @else
                                                    <td>
                                                        <input type="text" name="aktual_humadity[]" class="form-control form-control-sm text-center" value="{{ $blok['aktual_humadity'] ?? '' }}">
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>

                                        {{-- INFRA RED --}}
                                        <tr>
                                            <td class="text-left font-weight-bold bg-light">Infra Red</td>
                                            @foreach($sortedBlok as $blok)
                                                @if(($blok['block_number'] ?? '') === 'INFEED')
                                                    <td colspan="2">
                                                        <select name="infra_red_infeed_outfeed" class="form-control form-control-sm text-center">
                                                            <option value="">Pilih</option>
                                                            @foreach([1, 2, 3] as $irVal)
                                                                <option value="{{ $irVal }}" {{ (string)($mergedIrEdit2) === (string)$irVal ? 'selected' : '' }}>{{ $irVal }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    @php $edit2SkipI = true; @endphp
                                                @elseif(($blok['block_number'] ?? '') === 'OUTFEED' && $edit2SkipI)
                                                    {{-- merged into INFEED --}}
                                                @else
                                                    <td>
                                                        <select name="infra_red[]" class="form-control form-control-sm text-center">
                                                            <option value="">Pilih</option>
                                                            @foreach([1, 2, 3] as $irVal)
                                                                <option value="{{ $irVal }}" {{ (string)($blok['infra_red'] ?? '') === (string)$irVal ? 'selected' : '' }}>{{ $irVal }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>

                                        {{-- LAMA PROSES --}}
                                        <tr>
                                            <td class="text-left font-weight-bold bg-light">Standar Lama Proses (Menit)</td>
                                            @php
                                                $firstBlok = $sortedBlok->first();
                                                $stdLama = $firstBlok ? $stdFans->firstWhere('id', $firstBlok['id_std_fan']) : null;
                                            @endphp
                                            <td colspan="{{ count($sortedBlok) }}">
                                                <input type="text" class="form-control form-control-sm text-center" value="{{ $stdLama->std_lama_proses ?? '-' }}" readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left font-weight-bold bg-light">Lama Proses (Display Aktual; Menit)</td>
                                            <td colspan="{{ count($sortedBlok) }}">
                                                <input type="text" name="aktual_lama_proses_roasting" class="form-control form-control-sm text-center" value="{{ $pembekuan->prosesRoastingFanData->aktual_lama_proses ?? '' }}">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted italic mb-0">Langkah ini dilewati saat pembuatan data.</p>
                        @endif
                    </div>
                </div>

                {{-- SECTION 7: HASIL ROASTING --}}
                <div class="card {{ $pembekuan->hasilProsesRoastingData ? 'card-success' : 'card-secondary collapsed-card' }} card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold {{ $pembekuan->hasilProsesRoastingData ? 'text-success' : 'text-muted' }}">
                            <i class="fas fa-check-double mr-1"></i> 7. Hasil Roasting {{ $pembekuan->hasilProsesRoastingData ? '' : '(Dilewati)' }}
                        </h3>
                        <div class="card-tools">
                            @if(!$pembekuan->hasilProsesRoastingData) <span class="badge badge-secondary mr-2">Tidak Ada Data</span> @endif
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-{{ $pembekuan->hasilProsesRoastingData ? 'minus' : 'plus' }}"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($pembekuan->hasilProsesRoastingData)
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Jam <span class="text-danger">*</span></label>
                                        <input type="time" name="jam_hasil" class="form-control" value="{{ $pembekuan->hasilProsesRoastingData->jam }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Std Suhu Pusat <span class="text-danger">*</span></label>
                                        <select name="id_std_suhu_pusat_disabled" class="form-control select2-field" disabled>
                                            <option value="">Pilih Std</option>
                                            @foreach($stdSuhuPusat as $s)
                                                <option value="{{ $s->id }}" {{ $pembekuan->hasilProsesRoastingData->id_std_suhu_pusat == $s->id ? 'selected' : '' }}>{{ $s->std_suhu_pusat_roasting }} °C</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="id_std_suhu_pusat" value="{{ $pembekuan->hasilProsesRoastingData->id_std_suhu_pusat }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Aktual Suhu Pusat (°C) <span class="text-danger">*</span></label>
                                        @php
                                            $asp = $pembekuan->hasilProsesRoastingData->aktual_suhu_pusat;
                                            $aspList = is_array($asp) ? $asp : (is_string($asp) ? array_map('trim', preg_split('/[\/,;]+/', $asp)) : []);
                                            $aspList = array_values(array_filter($aspList, fn($v) => $v !== '' && !is_null($v)));
                                            if(empty($aspList)) { $aspList = ['']; }
                                        @endphp
                                        <div id="aktual_suhu_pusat_container">
                                            @foreach($aspList as $index => $suhuVal)
                                                <div class="input-group mb-2">
                                                    <input type="text" class="form-control input-aktual-suhu-pusat" name="aktual_suhu_pusat[]" value="{{ $suhuVal }}" placeholder="°C" required>
                                                    <div class="input-group-append">
                                                        @if($loop->first)
                                                            <button class="btn btn-success btn-add-suhu-pusat" type="button"><i class="fas fa-plus"></i></button>
                                                        @else
                                                            <button class="btn btn-danger btn-remove-suhu-pusat" type="button"><i class="fas fa-minus"></i></button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                @php
                                    $sensoriData = $pembekuan->hasilProsesRoastingData->sensori;
                                @endphp
                                @foreach(['kematangan','kenampakan','warna','rasa','bau','tekstur'] as $sen)
                                @php 
                                    $field = 'sensori_'.$sen; 
                                    $val = $sensoriData[$field] ?? ($pembekuan->hasilProsesRoastingData->$field ?? '');
                                @endphp
                                <div class="col-md-2 col-6 mb-2 text-center">
                                    <label class="text-capitalize small font-weight-bold">{{ $sen }}</label>
                                    <select name="sensori_{{ $sen }}" class="form-control form-control-sm">
                                        <option value="✔" {{ $val == '✔' ? 'selected' : '' }}>✔ OK</option>
                                        <option value="✘" {{ $val == '✘' ? 'selected' : '' }}>✘ NO</option>
                                    </select>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted italic mb-0">Langkah ini dilewati saat pembuatan data.</p>
                        @endif
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="card-footer text-right mb-5 shadow-sm">
                    <a href="{{ route('wizard.proses2.show', $pembekuan->uuid) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary ml-2">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>



@push('scripts')
<script>
// Dynamic Suhu Pusat Add/Remove (Global Event Handler)
$(document).on('click', '.btn-add-suhu-pusat', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var container = $('#aktual_suhu_pusat_container');
    if (container.length) {
        var newRow = `
            <div class="input-group mb-2">
                <input type="text" class="form-control input-aktual-suhu-pusat" name="aktual_suhu_pusat[]" placeholder="°C" required>
                <div class="input-group-append">
                    <button class="btn btn-danger btn-remove-suhu-pusat" type="button"><i class="fas fa-minus"></i></button>
                </div>
            </div>
        `;
        container.append(newRow);
    }
});

$(document).on('click', '.btn-remove-suhu-pusat', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).closest('.input-group').remove();
});

$(document).ready(function() {
    // Inisialisasi Select2
    $('.select2-field').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Pilih Opsi',
        allowClear: true
    });

    // Inisialisasi Berat Produk (logic dari app.blade.php jika diperlukan)
    if (typeof initSelectBeratEdit === 'function') {
        const beratVal = $('#edit_nilai_select_berat_penggorengan').data('selected');
        initSelectBeratEdit(beratVal, '#edit_nilai_select_berat_penggorengan');
    }

    // Confirmation before submit
    $('#main-edit-form').on('submit', function(e) {
        if (!confirm('Apakah Anda yakin ingin menyimpan semua perubahan data ini?')) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
@endsection
