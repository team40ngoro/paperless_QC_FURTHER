@extends('layouts.app')

@section('container')
@php
    $isProduksiRole = ((int) auth()->user()->id_role === 2);
@endphp
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-magic text-primary"></i> Verifikasi Proses Penggorengan/Pemasakan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Frying</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- WIZARD STEPS INDICATOR -->
            <div class="row mb-4 mt-2">
                <div class="col-12">
                    <div class="wizard-steps-container d-flex justify-content-between position-relative">
                        <div class="wizard-line"></div>
                        @php
                            $steps = [
                                1 => ['icon' => 'fa-fire',          'label' => 'Pencetakan'],
                                2 => ['icon' => 'fa-mortar-pestle', 'label' => 'Predust'],
                                3 => ['icon' => 'fa-tint',          'label' => 'Battering'],
                                4 => ['icon' => 'fa-bread-slice',   'label' => 'Breadering'],
                            ];
                            if (!$isProduksiRole) {
                                $steps[5] = ['icon' => 'fa-burn',         'label' => 'Fryer'];
                                $steps[6] = ['icon' => 'fa-check-double', 'label' => 'Hasil Goreng'];
                            }
                        @endphp

                        @foreach($steps as $num => $s)
                        <div class="wizard-step-item text-center {{ $num == 1 ? 'active' : '' }}" id="step-indicator-{{ $num }}">
                            <div class="step-icon-circle mx-auto mb-2">
                                <i class="fas {{ $s['icon'] }}"></i>
                            </div>
                            <span class="step-label d-none d-md-block">{{ $s['label'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <style>
                .wizard-steps-container { padding: 10px 0; margin-bottom: 20px; }
                .wizard-line { position: absolute; top: 25px; left: 50px; right: 50px; height: 2px; background: #dee2e6; z-index: 1; }
                .wizard-step-item { position: relative; z-index: 2; flex: 1; }
                .step-icon-circle { width: 50px; height: 50px; border-radius: 50%; background: #fff; border: 2px solid #dee2e6; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #ced4da; transition: all 0.3s ease; }
                .wizard-step-item.active .step-icon-circle { border-color: #007bff; background: #007bff; color: #fff; transform: scale(1.1); box-shadow: 0 0 10px rgba(0,123,255,0.3); }
                .wizard-step-item.completed .step-icon-circle { border-color: #28a745; background: #28a745; color: #fff; }
                .step-label { font-size: 13px; font-weight: 600; color: #6c757d; }
                .wizard-step-item.active .step-label { color: #007bff; }
            </style>

            {{-- ===== ALERT CONTAINER ===== --}}
            <div id="wizardAlert" class="d-none mb-3"></div>

            <!-- STEP 1: PENGGORENGAN -->
            <div class="wizard-step" id="step-1">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-fire mr-1"></i> Form Pencetakan (Langkah 1)</h3>
                    </div>
                    <div class="card-body">
                        <form id="form-step-1" novalidate>
                            <div class="form-group">
                                <label>Shift <span class="text-danger">*</span></label>
                                <select name="shift_id" class="form-control" required>
                                    <option value="">Pilih Shift</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tanggal <span class="text-danger">*</span></label>
                                @php
                                    $now = \Carbon\Carbon::now('Asia/Jakarta');
                                    $displayValue = $now->format('Y-m-d');
                                @endphp
                                <input type="date" name="tanggal" class="form-control" value="{{ $displayValue }}" required>
                            </div>
                            <div class="form-group">
                                <label>Jam <span class="text-danger">*</span></label>
                                <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Nama Produk <span class="text-danger">*</span></label>
                                <select name="id_produk" id="id_produk_proses_penggorengan" class="form-control select2-produk" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($produks as $produk)
                                        <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Berat Produk (gr) <span class="text-danger">*</span></label>
                                <select id="nilai_select_berat" name="berat_produk" class="form-control" data-nilai-berat="true"></select>
                            </div>
                            <div class="form-group">
                                <label>Kode Produksi <span class="text-danger">*</span></label>
                                <input type="text" name="kode_produksi" class="form-control" placeholder="Contoh: BN12345" required>
                            </div>
                            <div class="form-group">
                                <label>No Of Strokes <span class="text-danger">*</span></label>
                                <input type="text" name="no_of_strokes" class="form-control" required placeholder="Contoh: 10">
                            </div>
                            <div class="form-group">
                                <label>Waktu Pemasakan <span class="text-danger">*</span></label>
                                <input type="text" name="waktu_pemasakan" class="form-control" required placeholder="Contoh: 30">
                            </div>
                            @if($isProduksiRole)
                                <input type="hidden" name="hasil_pencetakan" value="">
                                <div class="form-group">
                                    <label>Hasil Pencetakan <span class="badge badge-info ml-1">Bagian QC Inspector</span></label>
                                    <div class="alert alert-light border mb-0 py-2 text-muted small">
                                        <i class="fas fa-lock mr-1"></i> Hasil Pencetakan (OK / Tidak OK) akan diinput oleh <strong>QC Inspector</strong> melalui ikon mata (Detail).
                                    </div>
                                </div>
                            @else
                                <div class="form-group">
                                    <label>Hasil Pencetakan</label>
                                    <select name="hasil_pencetakan" class="form-control">
                                        <option value="">Pilih Hasil (Opsional)</option>
                                        <option value="✔">✔ OK</option>
                                        <option value="✘">✘ Tidak OK</option>
                                    </select>
                                </div>
                            @endif
                        </form>
                    </div>
                    <div class="card-footer text-right">
                        <button type="button" class="btn btn-primary next-step" data-step="1">Simpan & Lanjut <i class="fas fa-arrow-right ml-1"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 2: PREDUST -->
            <div class="wizard-step" id="step-2" style="display:none;">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-mortar-pestle mr-1"></i> Form Predust (Langkah 2)</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Produk:</strong> <span id="p2_info_produk">-</span>
                        </div>
                        <div id="context-step-2" class="mb-3"></div>
                        <form id="form-step-2-predust" novalidate>
                            <input type="hidden" name="penggorengan_uuid" id="p2_predust_penggorengan_uuid">
                            <div class="form-group">
                                <label>Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" id="predust_tanggal" class="form-control" value="{{ $displayValue }}" required>
                            </div>
                            <div class="form-group">
                                <label>Jam <span class="text-danger">*</span></label>
                                <input type="time" name="jam" id="predust_jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Nama Produk <span class="text-danger">*</span></label>
                                <select name="id_produk" id="id_produk_predust" class="form-control select2-produk" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($produks as $produk)
                                        <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jenis Predust <span class="text-danger">*</span></label>
                                <select name="id_jenis_predust" id="jenis_predust_select" class="form-control" required>
                                    <option value="">-- Pilih Jenis Predust --</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Kode Produksi <span class="text-danger">*</span></label>
                                <input type="text" name="kode_produksi" id="predust_kode_produksi" class="form-control" placeholder="Masukkan Kode Produksi" required>
                            </div>
                            @if($isProduksiRole)
                                <input type="hidden" name="hasil_predust" id="predust_hasil" value="">
                                <div class="form-group">
                                    <label>Hasil Predust <span class="badge badge-info ml-1">Bagian QC Inspector</span></label>
                                    <div class="alert alert-light border mb-0 py-2 text-muted small">
                                        <i class="fas fa-lock mr-1"></i> Hasil Predust (OK / Tidak OK) akan diinput oleh <strong>QC Inspector</strong> melalui ikon mata (Detail).
                                    </div>
                                </div>
                            @else
                                <div class="form-group">
                                    <label>Hasil Predust <span class="text-danger">*</span></label>
                                    <select name="hasil_predust" id="predust_hasil" class="form-control" required>
                                        <option value="">-- Pilih Hasil --</option>
                                        <option value="✔">✔ Baik</option>
                                        <option value="✘">✘ Tidak Baik</option>
                                    </select>
                                </div>
                            @endif
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" onclick="skipPredust()"><i class="fas fa-forward mr-1"></i> Lewati Langkah Ini</button>
                        <button type="button" class="btn btn-primary" id="btn-submit-predust">Simpan & Lanjut <i class="fas fa-arrow-right ml-1"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 3: BATTERING -->
            <div class="wizard-step" id="step-3" style="display:none;">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-tint mr-1"></i> Form Battering (Langkah 3)</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Produk:</strong> <span id="p3_info_produk">-</span>
                        </div>
                        <div id="context-step-3" class="mb-3"></div>
                        <form id="form-step-3" novalidate>
                            <input type="hidden" name="penggorengan_uuid" id="p3_penggorengan_uuid">
                            <div class="form-group">
                                <label>Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="{{ $displayValue }}" required>
                            </div>
                            <div class="form-group">
                                <label>Jam <span class="text-danger">*</span></label>
                                <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Nama Produk <span class="text-danger">*</span></label>
                                <select name="id_produk" id="id_produk_select_better" class="form-control select2-produk" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($produks as $produk)
                                        <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jenis Better <span class="text-danger">*</span></label>
                                <select name="id_jenis_better[]" id="id_better_select_better" multiple="multiple" class="form-control" required>
                                </select>
                            </div>
                            <div class="form-group" id="kode_produksi_better_wrapper" style="display:none;">
                                <label>Kode Produksi Better <span class="text-danger">*</span></label>
                                <div id="kode_produksi_better_container">
                                </div>
                            </div>
                            @if($isProduksiRole)
                                <input type="hidden" name="hasil_better" value="">
                                <div class="form-group">
                                    <label>Hasil Better <span class="badge badge-info ml-1">Bagian QC Inspector</span></label>
                                    <div class="alert alert-light border mb-0 py-2 text-muted small">
                                        <i class="fas fa-lock mr-1"></i> Hasil Battering (OK / Tidak OK) akan diinput oleh <strong>QC Inspector</strong> melalui ikon mata (Detail).
                                    </div>
                                </div>
                            @else
                                <div class="form-group">
                                    <label>Hasil Better <span class="text-danger">*</span></label>
                                    <select name="hasil_better" class="form-control" required>
                                        <option value="✔">✔ Baik</option>
                                        <option value="✘">✘ Tidak Baik</option>
                                    </select>
                                </div>
                            @endif
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-primary next-step" data-step="3">Simpan & Lanjut <i class="fas fa-arrow-right ml-1"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 4: BREADER -->
            <div class="wizard-step" id="step-4" style="display:none;">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-bread-slice mr-1"></i> Form Breader (Langkah 4)</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Produk:</strong> <span id="p4_info_produk">-</span>
                        </div>
                        <div id="context-step-4" class="mb-3"></div>
                        <form id="form-step-4" novalidate>
                            <input type="hidden" name="battering_uuid" id="p4_battering_uuid">
                            <div class="form-group">
                                <label>Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="{{ $displayValue }}" required>
                            </div>
                            <div class="form-group">
                                <label>Jam <span class="text-danger">*</span></label>
                                <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Nama Produk <span class="text-danger">*</span></label>
                                <select name="id_produk" id="id_produk_breader" class="form-control select2-produk" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($produks as $produk)
                                        <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jenis Breader <span class="text-danger">*</span></label>
                                <select name="id_jenis_breader[]" id="id_jenis_breader_breader" multiple="multiple" class="form-control" required>
                                </select>
                            </div>
                            <div class="form-group" id="kode_produksi_breader_wrapper" style="display:none;">
                                <label>Kode Produksi Breader <span class="text-danger">*</span></label>
                                <div id="kode_produksi_breader_container">
                                </div>
                            </div>
                            @if($isProduksiRole)
                                <input type="hidden" name="hasil_breader" value="">
                                <div class="form-group">
                                    <label>Hasil Breader <span class="badge badge-info ml-1">Bagian QC Inspector</span></label>
                                    <div class="alert alert-light border mb-0 py-2 text-muted small">
                                        <i class="fas fa-lock mr-1"></i> Hasil Breading (OK / Tidak OK), Frying, dan Hasil Penggorengan akan diinput oleh <strong>QC Inspector</strong> melalui ikon mata (Detail).
                                    </div>
                                </div>
                            @else
                                <div class="form-group">
                                    <label>Hasil Breader <span class="text-danger">*</span></label>
                                    <select name="hasil_breader" class="form-control" required>
                                        <option value="✔">✔ Baik</option>
                                        <option value="✘">✘ Tidak Baik</option>
                                    </select>
                                </div>
                            @endif
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        @if($isProduksiRole)
                            <button type="button" class="btn btn-success next-step" data-step="4"><i class="fas fa-check-circle mr-1"></i> Simpan Data Produksi & Selesai</button>
                        @else
                            <button type="button" class="btn btn-primary next-step" data-step="4">Simpan & Lanjut <i class="fas fa-arrow-right ml-1"></i></button>
                        @endif
                    </div>
                </div>
            </div>

            @if(!$isProduksiRole)
            <!-- STEP 5: FRAYER -->
            <div class="wizard-step" id="step-5" style="display:none;">
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-burn mr-1"></i> Form Fryer (Langkah 5)</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Produk:</strong> <span id="p5_info_produk">-</span>
                        </div>
                        <strong><i class="fas fa-info-circle text-primary"></i> Pilih Frayer yang digunakan:</strong>
                        <div class="row mt-2">
                            @foreach([1,2,3,4,5] as $fn)
                            <div class="col-auto">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input frayer-checkbox" id="chk_frayer_{{ $fn }}" value="{{ $fn }}">
                                    <label class="custom-control-label" for="chk_frayer_{{ $fn }}">Fryer {{ $fn }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Container untuk tiap frayer --}}
                        @foreach([1,2,3,4,5] as $fn)
                        <div class="frayer-form d-none" id="frayer-form-{{ $fn }}">
                            <div class="card card-outline card-danger mb-2">
                                <div class="card-header py-2">
                                    <h3 class="card-title text-sm"><i class="fas fa-fire text-danger"></i> Fryer {{ $fn }}</h3>
                                    <div class="card-tools">
                                        <span class="badge badge-success frayer-saved-badge d-none" id="frayer-{{ $fn }}-saved">
                                            <i class="fas fa-check"></i> Tersimpan
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Produk</label>
                                        <div class="col-sm-9">
                                            @if($fn == 1)
                                            <select class="form-control select2-produk p4-produk-select" id="id_produk_f2" name="id_produk" required>
                                            @elseif($fn == 2)
                                            <select class="form-control select2-produk p4-produk-select" id="id_produk_f3" name="id_produk" required>
                                            @elseif($fn == 3)
                                            <select class="form-control select2-produk p4-produk-select" id="id_produk_f4" name="id_produk" required>
                                            @elseif($fn == 4)
                                            <select class="form-control select2-produk p4-produk-select" id="id_produk_f4_tab" name="id_produk" required>
                                            @elseif($fn == 5)
                                            <select class="form-control select2-produk p4-produk-select" id="id_produk_f5_tab" name="id_produk" required>
                                            @endif
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produks as $product)
                                                    <option value="{{ $product->id }}">{{ $product->nama_produk }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Tanggal</label>
                                                <div class="col-sm-8">
                                                    <input type="date" id="p4_f{{ $fn }}_tanggal" name="tanggal" class="form-control" value="{{ $displayValue }}" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Jam <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="time" id="p4_f{{ $fn }}_jam" class="form-control" value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') }}">
                                                </div>
                                            </div>
                                            @if($fn == 1)
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Suhu Fryer 1 <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <select id="id_suhu_frayer_1_f2" class="form-control frayer-suhu-select f-suhu" data-frayer="{{ $fn }}">
                                                        <option value="">-- Pilih Suhu --</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Waktu Penggorengan <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <select id="id_waktu_penggorengan_f2" class="form-control f-waktu">
                                                        <option value="">-- Pilih Suhu Dulu --</option>
                                                    </select>
                                                </div>
                                            </div>
                                            @elseif($fn == 2)
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Suhu Fryer 2 <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <select id="id_suhu_frayer_2" class="form-control frayer-suhu-select f-suhu" data-frayer="{{ $fn }}">
                                                        <option value="">-- Pilih Suhu --</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Waktu Penggorengan <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <select id="id_waktu_penggorengan_2" class="form-control f-waktu">
                                                        <option value="">-- Pilih Suhu Dulu --</option>
                                                    </select>
                                                </div>
                                            </div>
                                            @elseif($fn == 3)
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Suhu Fryer 3 <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <select id="id_suhu_frayer" class="form-control frayer-suhu-select f-suhu" data-frayer="{{ $fn }}">
                                                        <option value="">-- Pilih Suhu --</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Waktu Penggorengan <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <select id="id_waktu_penggorengan" class="form-control f-waktu">
                                                        <option value="">-- Pilih Suhu Dulu --</option>
                                                    </select>
                                                </div>
                                            </div>
                                            @elseif($fn == 4)
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Suhu Fryer 4 <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <select id="id_suhu_frayer_f4_tab" class="form-control frayer-suhu-select f-suhu" data-frayer="{{ $fn }}">
                                                        <option value="">-- Pilih Suhu --</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Waktu Penggorengan <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <select id="id_waktu_penggorengan_f4_tab" class="form-control f-waktu">
                                                        <option value="">-- Pilih Suhu Dulu --</option>
                                                    </select>
                                                </div>
                                            </div>
                                            @else
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Suhu Fryer 5 <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <select id="id_suhu_frayer_f5_tab" class="form-control frayer-suhu-select f-suhu" data-frayer="{{ $fn }}">
                                                        <option value="">-- Pilih Suhu --</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Waktu Penggorengan <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <select id="id_waktu_penggorengan_f5_tab" class="form-control f-waktu">
                                                        <option value="">-- Pilih Suhu Dulu --</option>
                                                    </select>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Aktual Penggorengan <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" id="p4_f{{ $fn }}_aktual" class="form-control" placeholder="detik">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Aktual Suhu</label>
                                                <div class="col-sm-8">
                                                    <input type="text" id="p4_f{{ $fn }}_aktual_suhu" class="form-control" placeholder="°C">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">TPM Minyak <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" id="p4_f{{ $fn }}_tpm" class="form-control" placeholder="%">
                                                </div>
                                            </div>
                                            @if(in_array($fn, [1, 2]))
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">FFA</label>
                                                <div class="col-sm-8">
                                                    <input type="text" id="p4_f{{ $fn }}_ffa" class="form-control" placeholder="Nilai FFA">
                                                </div>
                                            </div>
                                            @endif
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Keterangan</label>
                                                <div class="col-sm-8">
                                                    <textarea id="p4_f{{ $fn }}_keterangan" class="form-control" rows="2" placeholder="Catatan tambahan"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button type="button" class="btn btn-sm btn-danger btn-save-frayer"
                                            data-frayer="{{ $fn }}" id="btn-save-frayer-{{ $fn }}">
                                            <i class="fas fa-save mr-1"></i> Simpan Fryer {{ $fn }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-primary" id="btn-frayer-lanjut">Simpan & Lanjut <i class="fas fa-arrow-right ml-1"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 6: HASIL PENGGORENGAN -->
            <div class="wizard-step" id="step-6" style="display:none;">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-poll-h mr-1"></i> Hasil Penggorengan (Langkah 6)</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Produk:</strong> <span id="p6_info_produk">-</span>
                        </div>
                        <div id="context-step-6" class="mb-3"></div>
                        <form id="form-step-6" novalidate>
                            <input type="hidden" name="frayer_uuid" id="p6_frayer_uuid">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal</label>
                                        <input type="date" name="tanggal" class="form-control" value="{{ $displayValue }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Jam <span class="text-danger">*</span></label>
                                        <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Produk</label>
                                        <select class="form-control select2-produk" id="id_produk_hasil" name="id_produk" required>
                                            <option value="">Pilih Produk</option>
                                            @foreach ($produks as $product)
                                                <option value="{{ $product->id }}">{{ $product->nama_produk }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Std Suhu Pusat</label>
                                        <input type="text" class="form-control" id="std_suhu_pusat_display" readonly placeholder="Loading...">
                                        <input type="hidden" name="id_std_suhu_pusat" id="id_std_suhu_pusat">
                                    </div>
                                    <div class="form-group">
                                        <label>Aktual Suhu Pusat (°C) <span class="text-danger">*</span></label>
                                        <div id="aktual_suhu_pusat_container">
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control input-aktual-suhu-pusat" name="aktual_suhu_pusat[]" placeholder="°C" required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-success btn-add-suhu-pusat" type="button"><i class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        @foreach(['kematangan', 'kenampakan', 'warna', 'rasa', 'bau', 'tekstur'] as $sen)
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <label class="text-capitalize">{{ $sen }}</label>
                                                <select class="form-control" name="sensori_{{ $sen }}">
                                                    <option value="✔">✔</option>
                                                    <option value="✘">✘</option>
                                                </select>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="form-group">
                                        <label>Keterangan</label>
                                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="button" class="btn btn-success next-step" data-step="5"><i class="fas fa-check-circle mr-1"></i> Simpan & Selesai</button>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </section>
</div>

{{-- Loading Overlay --}}
<div id="loadingOverlay" class="d-none" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.4);z-index:9999;align-items:center;justify-content:center;">
    <div class="text-center text-white">
        <div class="spinner-border mb-2" style="width:3rem;height:3rem;"></div>
        <div id="loadingText" style="font-size:1.1rem;">Menyimpan data...</div>
    </div>
</div>

<style>
.frayer-form .card { border-left: 4px solid #dc3545; }
#loadingOverlay { display: none !important; }
#loadingOverlay.show { display: flex !important; }
</style>

<script>
(function() {
    'use strict';

    const IS_PRODUKSI_ROLE = {{ $isProduksiRole ? 'true' : 'false' }};
    const MAX_STEP = IS_PRODUKSI_ROLE ? 4 : 6;

    // ========== STATE ==========
    const state = {
        currentStep: 1,
        penggorengan_uuid: null,
        predust_uuid: null,
        battering_uuid: null,
        breader_uuid: null,
        frayer_uuids: {},      // { 1: 'uuid...', 2: 'uuid...', ... }
        last_frayer_uuid: null,
        last_frayer_number: null,
        hasil_penggorengan_uuid: null,
        id_produk: null,
        id_produk_text: null,
        user_id: "{{ Auth::id() }}"
    };

    const STORAGE_KEY = 'wizard_proses1_state_v2';
    try {
        const saved = sessionStorage.getItem(STORAGE_KEY);
        if (saved) {
            const parsed = JSON.parse(saved);
            if (parsed && typeof parsed === 'object') {
                if (parsed.user_id && parsed.user_id !== state.user_id) {
                    sessionStorage.removeItem(STORAGE_KEY);
                } else {
                    Object.assign(state, parsed);
                    if (state.currentStep > MAX_STEP) {
                        state.currentStep = MAX_STEP;
                    }
                }
            }
        }
    } catch (_) {}

    function persistState() {
        try {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify({
                currentStep: state.currentStep,
                penggorengan_uuid: state.penggorengan_uuid,
                predust_uuid: state.predust_uuid,
                battering_uuid: state.battering_uuid,
                breader_uuid: state.breader_uuid,
                frayer_uuids: state.frayer_uuids,
                last_frayer_uuid: state.last_frayer_uuid,
                last_frayer_number: state.last_frayer_number,
                hasil_penggorengan_uuid: state.hasil_penggorengan_uuid,
                id_produk: state.id_produk,
                id_produk_text: state.id_produk_text,
                user_id: state.user_id,
            }));
        } catch (_) {}
    }

    // ========== CSRF ==========
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ========== UTILS ==========
    function showLoading(text) {
        document.getElementById('loadingText').textContent = text || 'Menyimpan data...';
        document.getElementById('loadingOverlay').classList.add('show');
    }
    function hideLoading() {
        document.getElementById('loadingOverlay').classList.remove('show');
    }
    function showAlert(type, msg) {
        const el = document.getElementById('wizardAlert');
        if (!el) return;
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        el.className = `alert alert-${type} alert-dismissible shadow-sm border-0 fade show`;
        el.innerHTML = `<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fas ${icon}"></i> ${msg}`;
        el.classList.remove('d-none');
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => { if (el) el.classList.add('d-none'); }, 5000);
    }
    function showErrors(errors) {
        let html = '<ul class="mb-0">';
        Object.values(errors).forEach(msgs => msgs.forEach(m => { html += `<li>${m}</li>`; }));
        html += '</ul>';
        showAlert('danger', html);
    }
    function goToStep(step) {
        document.querySelectorAll('.wizard-step').forEach(p => p.style.display = 'none');
        const panel = document.getElementById(`step-${step}`);
        if (panel) { panel.style.display = 'block'; panel.scrollIntoView({ behavior: 'smooth' }); }
        updateStepper(step);
        state.currentStep = step;
    }
    function updateStepper(active) {
        for (let i = 1; i <= MAX_STEP; i++) {
            const el = document.getElementById(`step-indicator-${i}`);
            if (!el) continue;
            el.classList.remove('active', 'completed', 'locked');
            if (i < active)  el.classList.add('completed');
            else if (i === active) el.classList.add('active');
            else el.classList.add('locked');
        }
    }
    function ajaxPost(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-Requested-With': 'XMLHttpRequest', 
                'X-CSRF-TOKEN': CSRF, 
                'Accept': 'application/json' 
            },
            body: JSON.stringify(data)
        }).then(r => r.json());
    }
    function copyProdukToStep(step, idProduk, namaProduk) {
        let sel;
        if (step === 2) sel = document.getElementById('id_produk_predust');
        else if (step === 3) sel = document.getElementById('id_produk_select_better');
        else if (step === 4) sel = document.getElementById('id_produk_breader');
        else if (step === 5) {
            if (window.jQuery) {
                $('.p4-produk-select').val(idProduk).trigger('change');
            }
        }
        else if (step === 6) sel = document.getElementById('id_produk_hasil');
        
        const badge = document.getElementById(`p${step}_info_produk`);
        if (sel) { 
            sel.value = idProduk; 
            sel.dispatchEvent(new Event('change')); 
        }
        if (badge) badge.textContent = namaProduk;
    }

    // ========== STEP 1 SUBMIT ==========
    document.querySelector('#step-1 .next-step')?.addEventListener('click', function() {
        const form1 = document.getElementById('form-step-1');
        const requiredFields = ['shift_id', 'jam', 'id_produk', 'berat_produk', 'kode_produksi', 'no_of_strokes', 'waktu_pemasakan'];
        let isValid = true;
        requiredFields.forEach(name => {
            const el = name === 'berat_produk' ? document.getElementById('nilai_select_berat') : 
                       (name === 'id_produk'   ? document.getElementById('id_produk_proses_penggorengan') : 
                        form1.querySelector(`[name="${name}"]`));
            if (!el || !el.value) {
                el?.classList.add('is-invalid');
                isValid = false;
            } else {
                el?.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            return showAlert('warning', 'Tolong Lengkapi Pengisian Datanya (bertanda bintang *).');
        }

        const data = {
            id_produk:        document.getElementById('id_produk_proses_penggorengan').value,
            shift_id:         form1.querySelector('[name="shift_id"]').value,
            kode_produksi:    form1.querySelector('[name="kode_produksi"]').value,
            berat_produk:     document.getElementById('nilai_select_berat').value,
            no_of_strokes:    form1.querySelector('[name="no_of_strokes"]').value,
            waktu_pemasakan:  form1.querySelector('[name="waktu_pemasakan"]').value,
            jam:              form1.querySelector('[name="jam"]').value,
            tanggal:          form1.querySelector('[name="tanggal"]').value,
            hasil_pencetakan: form1.querySelector('[name="hasil_pencetakan"]')?.value || null,
        };

        showLoading('Menyimpan data Penggorengan...');
        ajaxPost('{{ route("wizard.proses1.store-penggorengan") }}', data).then(res => {
            hideLoading();
            if (res.success) {
                state.penggorengan_uuid = res.uuid;
                state.id_produk = data.id_produk;
                state.id_produk_text = document.getElementById('id_produk_proses_penggorengan').options[document.getElementById('id_produk_proses_penggorengan').selectedIndex].text;
                persistState();
                copyProdukToStep(2, state.id_produk, state.id_produk_text);
                document.getElementById('p2_predust_penggorengan_uuid').value = state.penggorengan_uuid;
                goToStep(2);
                showAlert('success', '<i class="fas fa-check mr-1"></i> Data Penggorengan berhasil disimpan.');
            } else {
                if (res.errors) showErrors(res.errors);
                else showAlert('danger', res.message || 'Tolong Lengkapi Pengisian Datanya.');
            }
        }).catch(e => { hideLoading(); showAlert('danger', 'Tolong Lengkapi Pengisian Datanya.'); });
    });

    // ========== STEP 2 SUBMIT: PREDUST ==========
    document.getElementById('btn-submit-predust')?.addEventListener('click', function() {
        let isValid = true;
        const requiredFields = [
            { id: 'id_produk_predust', name: 'Nama Produk' },
            { id: 'jenis_predust_select', name: 'Jenis Predust' },
            { id: 'predust_kode_produksi', name: 'Kode Produksi' },
            { id: 'predust_tanggal', name: 'Tanggal' },
            { id: 'predust_jam', name: 'Jam' },
        ];
        if (!IS_PRODUKSI_ROLE) {
            requiredFields.push({ id: 'predust_hasil', name: 'Hasil Predust' });
        }
        requiredFields.forEach(f => {
            const el = document.getElementById(f.id);
            if (!el || !el.value) { el?.classList.add('is-invalid'); isValid = false; }
            else { el?.classList.remove('is-invalid'); }
        });
        if (!isValid) {
            return showAlert('warning', 'Tolong Lengkapi Pengisian Datanya (bertanda bintang *).');
        }

        const data = {
            id_produk:         document.getElementById('id_produk_predust').value,
            id_jenis_predust:  document.getElementById('jenis_predust_select').value,
            kode_produksi:     document.getElementById('predust_kode_produksi').value,
            hasil_predust:     document.getElementById('predust_hasil')?.value || null,
            tanggal:           document.getElementById('predust_tanggal').value,
            jam:               document.getElementById('predust_jam').value,
            penggorengan_uuid: state.penggorengan_uuid,
        };

        showLoading('Menyimpan data Predust...');
        ajaxPost('{{ route("wizard.proses1.store-predust") }}', data).then(res => {
            hideLoading();
            if (res.success) {
                state.predust_uuid = res.uuid;
                persistState();
                document.getElementById('p3_penggorengan_uuid').value = state.penggorengan_uuid;
                copyProdukToStep(3, state.id_produk, state.id_produk_text);
                goToStep(3);
                showAlert('success', '<i class="fas fa-check mr-1"></i> Data Predust berhasil disimpan.');
            } else {
                if (res.errors) showErrors(res.errors);
                else showAlert('danger', res.message || 'Tolong Lengkapi Pengisian Datanya.');
            }
        }).catch(e => { hideLoading(); showAlert('danger', 'Tolong Lengkapi Pengisian Datanya.'); });
    });

    function skipPredust() {
        state.predust_uuid = null;
        persistState();
        document.getElementById('p3_penggorengan_uuid').value = state.penggorengan_uuid;
        copyProdukToStep(3, state.id_produk, state.id_produk_text);
        goToStep(3);
        showAlert('info', 'Langkah Predust dilewati.');
    }
    window.skipPredust = skipPredust;

    // ========== STEP 3 SUBMIT: BATTERING ==========
    document.querySelector('#step-3 .next-step')?.addEventListener('click', function() {
        const formEl = document.getElementById('form-step-3');
        const betterSelect = document.getElementById('id_better_select_better');
        const selectedBetters = Array.from(betterSelect.selectedOptions).map(o => o.value);
        let isValid = true;

        const produkEl = document.getElementById('id_produk_select_better');
        if (!produkEl || !produkEl.value) { produkEl?.classList.add('is-invalid'); isValid = false; }
        else { produkEl?.classList.remove('is-invalid'); }

        const jamEl = formEl.querySelector('[name="jam"]');
        if (!jamEl || !jamEl.value) { jamEl?.classList.add('is-invalid'); isValid = false; }
        else { jamEl?.classList.remove('is-invalid'); }

        if (selectedBetters.length === 0) { betterSelect.classList.add('is-invalid'); isValid = false; }
        else { betterSelect.classList.remove('is-invalid'); }

        const kodeProduksiBetter = {};
        let kodeProduksiValid = true;
        formEl.querySelectorAll('input[name^="kode_produksi_better["]').forEach(inp => {
            const match = inp.name.match(/\[(.*?)\]/);
            if (match) {
                const id = match[1];
                if (!inp.value.trim()) {
                    inp.classList.add('is-invalid');
                    kodeProduksiValid = false;
                } else {
                    inp.classList.remove('is-invalid');
                }
                kodeProduksiBetter[id] = inp.value.trim();
            }
        });

        if (selectedBetters.length > 0 && Object.keys(kodeProduksiBetter).length === 0) {
            kodeProduksiValid = false;
        }

        if (!kodeProduksiValid) isValid = false;

        if (!isValid) {
            return showAlert('warning', 'Tolong Lengkapi Pengisian Datanya (bertanda bintang *).');
        }

        const data = {
            id_produk:            produkEl.value,
            kode_produksi_better: kodeProduksiBetter,
            id_jenis_better:      selectedBetters,
            hasil_better:         formEl.querySelector('[name="hasil_better"]')?.value || null,
            tanggal:              formEl.querySelector('[name="tanggal"]').value,
            jam:                  jamEl.value,
            penggorengan_uuid:    state.penggorengan_uuid,
            predust_uuid:         state.predust_uuid,
        };

        showLoading('Menyimpan data Battering...');
        ajaxPost('{{ route("wizard.proses1.store-battering") }}', data).then(res => {
            hideLoading();
            if (res.success) {
                state.battering_uuid = res.uuid;
                persistState();
                copyProdukToStep(4, state.id_produk, state.id_produk_text);
                goToStep(4);
                showAlert('success', '<i class="fas fa-check mr-1"></i> Data Battering berhasil disimpan.');
            } else {
                if (res.errors) showErrors(res.errors);
                else showAlert('danger', res.message || 'Tolong Lengkapi Pengisian Datanya.');
            }
        }).catch(e => { hideLoading(); showAlert('danger', 'Tolong Lengkapi Pengisian Datanya.'); });
    });

    // ========== STEP 4 SUBMIT: BREADER ==========
    document.querySelector('#step-4 .next-step')?.addEventListener('click', function() {
        const breaderSelect = document.getElementById('id_jenis_breader_breader');
        const selectedBreaders = Array.from(breaderSelect.selectedOptions).map(o => o.value);
        const form4 = document.getElementById('form-step-4');
        let isValid = true;

        const produkBreader = document.getElementById('id_produk_breader');
        if (!produkBreader || !produkBreader.value) { produkBreader?.classList.add('is-invalid'); isValid = false; }
        else { produkBreader?.classList.remove('is-invalid'); }

        const jamBreader = form4.querySelector('[name="jam"]');
        if (!jamBreader || !jamBreader.value) { jamBreader?.classList.add('is-invalid'); isValid = false; }
        else { jamBreader?.classList.remove('is-invalid'); }

        if (selectedBreaders.length === 0) { breaderSelect.classList.add('is-invalid'); isValid = false; }
        else { breaderSelect.classList.remove('is-invalid'); }

        const kodeProduksiBreader = {};
        let kodeProduksiValid = true;
        form4.querySelectorAll('input[name^="kode_produksi_breader["]').forEach(inp => {
            const match = inp.name.match(/\[(.*?)\]/);
            if (match) {
                const id = match[1];
                if (!inp.value.trim()) {
                    inp.classList.add('is-invalid');
                    kodeProduksiValid = false;
                } else {
                    inp.classList.remove('is-invalid');
                }
                kodeProduksiBreader[id] = inp.value.trim();
            }
        });

        if (selectedBreaders.length > 0 && Object.keys(kodeProduksiBreader).length === 0) {
            kodeProduksiValid = false;
        }

        if (!kodeProduksiValid) isValid = false;

        if (!isValid) {
            return showAlert('warning', 'Tolong Lengkapi Pengisian Datanya (bertanda bintang *).');
        }

        const data = {
            id_produk:             produkBreader.value,
            id_jenis_breader:      selectedBreaders,
            kode_produksi_breader: kodeProduksiBreader,
            hasil_breader:         form4.querySelector('[name="hasil_breader"]')?.value || null,
            tanggal:               form4.querySelector('[name="tanggal"]').value,
            jam:                   jamBreader.value,
            battering_uuid:        state.battering_uuid,
            finalize_produksi:     IS_PRODUKSI_ROLE ? 1 : 0,
        };

        showLoading('Menyimpan data Breader...');
        ajaxPost('{{ route("wizard.proses1.store-breader") }}', data).then(res => {
            hideLoading();
            if (res.success) {
                state.breader_uuid = res.uuid;
                if (IS_PRODUKSI_ROLE) {
                    try { sessionStorage.removeItem(STORAGE_KEY); } catch (_) {}
                    window.location.href = res.redirect_url || '{{ route("wizard.proses1.index") }}';
                    return;
                }
                persistState();
                copyProdukToStep(5, state.id_produk, state.id_produk_text);
                document.getElementById('p4_info_produk').textContent = state.id_produk_text;
                goToStep(5);
                showAlert('success', '<i class="fas fa-check mr-1"></i> Data Breader berhasil disimpan.');
            } else {
                if (res.errors) showErrors(res.errors);
                else showAlert('danger', res.message || 'Tolong Lengkapi Pengisian Datanya.');
            }
        }).catch(e => { hideLoading(); showAlert('danger', 'Tolong Lengkapi Pengisian Datanya.'); });
    });

    // ========== FRAYER CHECKBOX ==========
    document.querySelectorAll('.frayer-checkbox').forEach(chk => {
        chk.addEventListener('change', function() {
            const fn = parseInt(this.value);
            const formEl = document.getElementById(`frayer-form-${fn}`);
            if (this.checked) {
                formEl.classList.remove('d-none');
            } else {
                formEl.classList.add('d-none');
            }
        });
    });

    // ========== SIMPAN PER FRAYER ==========
    document.querySelectorAll('.btn-save-frayer').forEach(btn => {
        btn.addEventListener('click', function() {
            const fn = parseInt(this.dataset.frayer);

            const idMap = {
                1: { produk: 'id_produk_f2',     suhu: 'id_suhu_frayer_1_f2',    waktu: 'id_waktu_penggorengan_f2' },
                2: { produk: 'id_produk_f3',     suhu: 'id_suhu_frayer_2',       waktu: 'id_waktu_penggorengan_2' },
                3: { produk: 'id_produk_f4',     suhu: 'id_suhu_frayer',         waktu: 'id_waktu_penggorengan' },
                4: { produk: 'id_produk_f4_tab', suhu: 'id_suhu_frayer_f4_tab',  waktu: 'id_waktu_penggorengan_f4_tab' },
                5: { produk: 'id_produk_f5_tab', suhu: 'id_suhu_frayer_f5_tab',  waktu: 'id_waktu_penggorengan_f5_tab' },
            };
            const ids = idMap[fn];
            const produkEl = ids ? document.getElementById(ids.produk) : null;
            const suhuEl = ids ? document.getElementById(ids.suhu) : null;
            const waktuEl = ids ? document.getElementById(ids.waktu) : null;

            if (!produkEl || !suhuEl || !waktuEl) {
                return showAlert('danger', `Komponen input Frayer ${fn} tidak ditemukan. Mohon refresh halaman.`);
            }

            const id_produk = produkEl.value;
            const suhuId = suhuEl.value;
            const waktuId = waktuEl.value;

            const data = {
                frayer_number:            fn,
                id_produk:                id_produk,
                tanggal:                  document.getElementById(`p4_f${fn}_tanggal`).value,
                jam:                      document.getElementById(`p4_f${fn}_jam`).value,
                aktual_penggorengan:      document.getElementById(`p4_f${fn}_aktual`).value,
                aktual_suhu_penggorengan: document.getElementById(`p4_f${fn}_aktual_suhu`).value,
                tpm_minyak:               document.getElementById(`p4_f${fn}_tpm`).value,
                keterangan:               document.getElementById(`p4_f${fn}_keterangan`).value,
                breader_uuid:             state.breader_uuid,
                prev_frayer_uuid:         state.last_frayer_uuid,
            };

            if (fn === 1 || fn === 2) {
                const ffaEl = document.getElementById(`p4_f${fn}_ffa`);
                if (ffaEl) data.ffa = ffaEl.value;
            }

            if (!data.breader_uuid) {
                return showAlert('warning', 'Simpan data Breader terlebih dahulu sebelum menyimpan Frayer.');
            }

            if (fn === 2) {
                data.id_suhu_frayer_2        = suhuId;
                data.id_waktu_penggorengan_2 = waktuId;
            } else {
                data.id_suhu_frayer_1        = suhuId;
                data.id_waktu_penggorengan   = waktuId;
            }

            if (!suhuId || !waktuId || !data.aktual_penggorengan || !data.tpm_minyak || !data.jam) {
                if (!suhuId) suhuEl.classList.add('is-invalid');
                if (!waktuId) waktuEl.classList.add('is-invalid');
                if (!data.aktual_penggorengan) document.getElementById(`p4_f${fn}_aktual`).classList.add('is-invalid');
                if (!data.tpm_minyak) document.getElementById(`p4_f${fn}_tpm`).classList.add('is-invalid');
                if (!data.jam) document.getElementById(`p4_f${fn}_jam`).classList.add('is-invalid');
                return showAlert('warning', 'Tolong Lengkapi Pengisian Datanya.');
            }

            [suhuEl, waktuEl, document.getElementById(`p4_f${fn}_aktual`), document.getElementById(`p4_f${fn}_tpm`), document.getElementById(`p4_f${fn}_jam`)].forEach(el => el?.classList.remove('is-invalid'));
            showLoading(`Menyimpan Fryer ${fn}...`);
            ajaxPost('{{ route("wizard.proses1.store-frayer") }}', data).then(res => {
                hideLoading();
                if (res.success) {
                    state.frayer_uuids[fn]   = res.uuid;
                    state.last_frayer_uuid   = res.uuid;
                    state.last_frayer_number = fn;
                    persistState();
                    document.getElementById(`frayer-${fn}-saved`).classList.remove('d-none');
                    document.getElementById(`btn-save-frayer-${fn}`).disabled = true;
                    document.getElementById(`btn-save-frayer-${fn}`).innerHTML = '<i class="fas fa-check mr-1"></i> Tersimpan';
                    showAlert('success', `<i class="fas fa-check mr-1"></i> Fryer ${fn} berhasil disimpan.`);
                } else {
                    if (res.errors) showErrors(res.errors);
                    else showAlert('danger', res.message || 'Terjadi kesalahan.');
                }
            }).catch(e => { hideLoading(); showAlert('danger', 'Gagal: ' + e.message); });
        });
    });

    // ========== FRAYER LANJUT ==========
    document.getElementById('btn-frayer-lanjut')?.addEventListener('click', function() {
        const savedFrayers = Object.keys(state.frayer_uuids);
        if (savedFrayers.length === 0) {
            return showAlert('warning', 'Pilih dan simpan minimal 1 Fryer sebelum melanjutkan.');
        }
        const checkedFrayers = Array.from(document.querySelectorAll('.frayer-checkbox:checked')).map(c => parseInt(c.value));
        const unsaved = checkedFrayers.filter(fn => !state.frayer_uuids[fn]);
        if (unsaved.length > 0) {
            return showAlert('warning', `Fryer ${unsaved.join(', ')} belum disimpan. Harap simpan terlebih dahulu.`);
        }
        const lastFn = Math.max(...savedFrayers.map(Number));
        state.last_frayer_uuid = state.frayer_uuids[lastFn];
        state.last_frayer_number = lastFn;

        loadStdSuhuPusat(state.id_produk, state.last_frayer_number);
        document.getElementById('p5_info_produk').textContent = state.id_produk_text;
        copyProdukToStep(6, state.id_produk, state.id_produk_text);
        goToStep(6);
        showAlert('success', `<i class="fas fa-check mr-1"></i> ${savedFrayers.length} Fryer berhasil disimpan. Lanjut ke Hasil Penggorengan.`);
    });

    // ========== LOAD STD SUHU PUSAT ==========
    function loadStdSuhuPusat(idProduk, fryerNumber) {
        const displayEl = document.getElementById('std_suhu_pusat_display');
        const hiddenEl = document.getElementById('id_std_suhu_pusat');
        if (!displayEl || !hiddenEl) return;
        
        if (!fryerNumber || !idProduk) {
            displayEl.value = 'Fryer atau Produk tidak terdeteksi';
            return;
        }
        
        displayEl.value = 'Loading...';
        const url = "{{ route('get-std-suhu-pusat-by-produk', ['id_produk' => ':id_produk']) }}".replace(':id_produk', idProduk);
        
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (data && data.length > 0) {
                    const stdSuhu = data[0];
                    const suhuArray = stdSuhu.std_suhu_pusat;
                    
                    if (suhuArray && Array.isArray(suhuArray) && suhuArray.length >= fryerNumber) {
                        const suhuValue = suhuArray[fryerNumber - 1];
                        if (suhuValue) {
                            displayEl.value = suhuValue + '°C';
                            hiddenEl.value = stdSuhu.id;
                        } else {
                            displayEl.value = 'Data kosong untuk Fryer ' + fryerNumber;
                            hiddenEl.value = '';
                        }
                    } else {
                        displayEl.value = 'Data tidak tersedia untuk Fryer ' + fryerNumber;
                        hiddenEl.value = '';
                        showAlert('warning', 'Standar suhu untuk Fryer ' + fryerNumber + ' belum diatur untuk produk ini!');
                    }
                } else {
                    displayEl.value = 'Data tidak ditemukan';
                    hiddenEl.value = '';
                    showAlert('danger', 'Standar suhu pusat belum diatur untuk produk ini!');
                }
            })
            .catch(() => {
                displayEl.value = 'Error loading data';
                hiddenEl.value = '';
            });
    }

    // Dynamic Suhu Pusat Add/Remove
    document.addEventListener('click', function(e) {
        const addBtn = e.target.closest('.btn-add-suhu-pusat');
        if (addBtn) {
            e.preventDefault();
            const container = document.getElementById('aktual_suhu_pusat_container');
            if (container) {
                const div = document.createElement('div');
                div.className = 'input-group mb-2';
                div.innerHTML = `
                    <input type="text" class="form-control input-aktual-suhu-pusat" name="aktual_suhu_pusat[]" placeholder="°C" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-danger btn-remove-suhu-pusat" type="button"><i class="fas fa-minus"></i></button>
                    </div>
                `;
                container.appendChild(div);
            }
        }

        const removeBtn = e.target.closest('.btn-remove-suhu-pusat');
        if (removeBtn) {
            e.preventDefault();
            const group = removeBtn.closest('.input-group');
            if (group) {
                group.remove();
            }
        }
    });

    // ========== STEP 6 SUBMIT: HASIL PENGGORENGAN (FINAL STEP) ==========
    document.querySelector('#step-6 .next-step')?.addEventListener('click', function() {
        const form6 = document.getElementById('form-step-6');
        const requiredFields = ['id_produk', 'id_std_suhu_pusat', 'jam', 'sensori_kematangan', 'sensori_kenampakan', 'sensori_warna', 'sensori_rasa', 'sensori_bau', 'sensori_tekstur'];
        let isValid = true;
        requiredFields.forEach(name => {
            const el = name === 'id_produk' ? document.getElementById('id_produk_hasil') : 
                       (name === 'id_std_suhu_pusat' ? document.getElementById('std_suhu_pusat_display') : 
                        form6.querySelector(`[name="${name}"]`));
            if (!el || !el.value) {
                el?.classList.add('is-invalid');
                isValid = false;
            } else {
                el?.classList.remove('is-invalid');
            }
        });

        const suhuPusatInputs = form6.querySelectorAll('.input-aktual-suhu-pusat');
        const aktualSuhuPusatArr = [];
        let suhuPusatValid = true;

        suhuPusatInputs.forEach(input => {
            const val = input.value.trim();
            if (!val) {
                input.classList.add('is-invalid');
                suhuPusatValid = false;
            } else {
                input.classList.remove('is-invalid');
                aktualSuhuPusatArr.push(val);
            }
        });

        if (!suhuPusatValid || aktualSuhuPusatArr.length === 0) {
            isValid = false;
        }

        if (!isValid) {
            return showAlert('warning', 'Tolong Lengkapi Pengisian Datanya.');
        }

        const data = {
            id_produk:          document.getElementById('id_produk_hasil').value,
            id_std_suhu_pusat:  document.getElementById('id_std_suhu_pusat').value,
            aktual_suhu_pusat:  aktualSuhuPusatArr,
            keterangan:         form6.querySelector('[name="keterangan"]').value,
            tanggal:            form6.querySelector('[name="tanggal"]').value,
            jam:                form6.querySelector('[name="jam"]').value,
            sensori_kematangan: form6.querySelector('[name="sensori_kematangan"]').value || null,
            sensori_kenampakan: form6.querySelector('[name="sensori_kenampakan"]').value || null,
            sensori_warna:      form6.querySelector('[name="sensori_warna"]').value || null,
            sensori_rasa:       form6.querySelector('[name="sensori_rasa"]').value || null,
            sensori_bau:        form6.querySelector('[name="sensori_bau"]').value || null,
            sensori_tekstur:    form6.querySelector('[name="sensori_tekstur"]').value || null,
            frayer_uuid:        state.frayer_uuids[1] || null,
            frayer2_uuid:       state.frayer_uuids[2] || null,
            frayer3_uuid:       state.frayer_uuids[3] || null,
            frayer4_uuid:       state.frayer_uuids[4] || null,
            frayer5_uuid:       state.frayer_uuids[5] || null,
            breader_uuid:       state.breader_uuid,
            battering_uuid:     state.battering_uuid,
            predust_uuid:       state.predust_uuid,
            penggorengan_uuid:  state.penggorengan_uuid,
        };

        showLoading('Menyimpan data Hasil Penggorengan & menyelesaikan proses...');
        ajaxPost('{{ route("wizard.proses1.store-hasil-penggorengan") }}', data).then(res => {
            hideLoading();
            if (res.success) {
                try { sessionStorage.removeItem(STORAGE_KEY); } catch (_) {}
                window.location.href = res.redirect_url || '{{ route("wizard.proses1.index") }}';
            } else {
                if (res.errors) showErrors(res.errors);
                else showAlert('danger', res.message || 'Terjadi kesalahan.');
            }
        }).catch(e => { hideLoading(); showAlert('danger', 'Gagal: ' + e.message); });
    });

    // ========== PREV/NEXT BUTTONS ==========
    document.querySelectorAll('.prev-step, .btn-prev').forEach(btn => {
        btn.addEventListener('click', function() {
            const step = parseInt(this.dataset.step);
            goToStep(step - 1);
        });
    });

    // ========== LOAD JENIS BREADER SAAT PRODUK BERUBAH ==========
    document.getElementById('id_produk_breader')?.addEventListener('change', function() {
        const idProduk = this.value;
        const sel = document.getElementById('id_jenis_breader_breader');
        if (!idProduk || !sel) return;
        
        const $sel = $(sel);
        $sel.html('<option value="">Memuat...</option>').trigger('change');

        fetch(`{{ url('qc-sistem/ajax/jenis-breader-by-produk') }}/${idProduk}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(data => {
                $sel.empty();
                const seen = new Set();
                data.forEach(b => {
                    if (!seen.has(b.id)) {
                        $sel.append(new Option(b.jenis_breader, b.id, false, false));
                        seen.add(b.id);
                    }
                });
                $sel.trigger('change');
            }).catch(() => { $sel.html('<option value="">Pilih breader</option>').trigger('change'); });
    });

    // ========== LOAD JENIS PREDUST SAAT PRODUK BERUBAH ==========
    document.getElementById('id_produk_predust')?.addEventListener('change', function() {
        const idProduk = this.value;
        const sel = document.getElementById('jenis_predust_select');
        if (!idProduk || !sel) return;
        
        sel.innerHTML = '<option value="">Memuat...</option>';

        fetch(`{{ url('qc-sistem/ajax/get-jenis-predust-by-produk') }}?id_produk=${idProduk}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(data => {
                sel.innerHTML = '<option value="">-- Pilih Jenis Predust --</option>';
                const seen = new Set();
                data.forEach(p => {
                    if (!seen.has(p.id)) {
                        sel.innerHTML += `<option value="${p.id}">${p.jenis_predust}</option>`;
                        seen.add(p.id);
                    }
                });
            }).catch(() => { sel.innerHTML = '<option value="">-- Pilih Jenis Predust --</option>'; });
    });

    // ========== LOAD JENIS BETTER SAAT PRODUK BERUBAH ==========
    document.getElementById('id_produk_select_better')?.addEventListener('change', function() {
        const idProduk = this.value;
        const sel = document.getElementById('id_better_select_better');
        if (!idProduk || !sel) return;
        
        const $sel = $(sel);
        $sel.html('<option value="">Memuat...</option>').trigger('change');

        fetch(`{{ url('super-admin/std-salinitas-viskositas/better-by-produk') }}/${idProduk}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(data => {
                $sel.empty();
                const seen = new Set();
                data.forEach(b => {
                    if (!seen.has(b.id)) {
                        $sel.append(new Option(b.nama_formula_better || b.nama_better, b.id, false, false));
                        seen.add(b.id);
                    }
                });
                $sel.trigger('change');
            }).catch(() => { $sel.html('<option value="">Pilih better</option>').trigger('change'); });
    });

    // Init Hidden Inputs from State
    if (state.penggorengan_uuid) { const el = document.getElementById('p3_penggorengan_uuid'); if (el) el.value = state.penggorengan_uuid; }
    if (state.battering_uuid) { const el = document.getElementById('p4_battering_uuid'); if (el) el.value = state.battering_uuid; }
    if (state.last_frayer_uuid) { const el = document.getElementById('p6_frayer_uuid'); if (el) el.value = state.last_frayer_uuid; }

    // Init stepper
    if (state.currentStep > 1) {
        setTimeout(() => {
            goToStep(state.currentStep);
            if (state.id_produk) {
                const mainProd = document.getElementById('id_produk_proses_penggorengan');
                if (mainProd) {
                    mainProd.value = state.id_produk;
                    mainProd.dispatchEvent(new Event('change'));
                }
                for (let i = 2; i <= state.currentStep; i++) {
                    copyProdukToStep(i, state.id_produk, state.id_produk_text || '-');
                }
            }
            showAlert('info', '<i class="fas fa-history mr-1"></i> Sesi berhasil dipulihkan! Melanjutkan pengisian data yang belum selesai.');
        }, 150);
    } else {
        updateStepper(1);
        document.getElementById('step-1').style.display = 'block';
    }
})();
</script>
@endsection
