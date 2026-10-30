@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-magic text-primary"></i> Verifikasi Proses Frying Roasted Produk</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Proses Frying Roasted Produk</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- WIZARD STEPS INDICATOR (Consistent with Wizard 3) -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="wizard-steps-container d-flex justify-content-between position-relative">
                        <div class="wizard-line"></div>
                        @php
                            $steps = [
                                1 => ['icon' => 'fa-fire',          'label' => 'Pencetakan'],
                                2 => ['icon' => 'fa-mortar-pestle', 'label' => 'Predust'],
                                3 => ['icon' => 'fa-tint',          'label' => 'Battering'],
                                4 => ['icon' => 'fa-layer-group',   'label' => 'Breader'],
                                5 => ['icon' => 'fa-burn',          'label' => 'Fryer'],
                                6 => ['icon' => 'fa-fan',           'label' => 'Roasting Fan'],
                                7 => ['icon' => 'fa-check-double',  'label' => 'Hasil Roasting'],
                                8 => ['icon' => 'fa-snowflake',     'label' => 'IQF Roasting'],
                            ];
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

            @php
                $now = \Carbon\Carbon::now('Asia/Jakarta');
                $submitValue = $now->format('d-m-Y H:i:s');
                $displayValue = $now->format('d-m-Y H:i:s');
            @endphp

            <!-- STEP 1: PENGGORENGAN -->
            <div class="wizard-step" id="step-1">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-fire mr-1"></i> Form Pencetakan (Langkah 1)</h3></div>
                    <div class="card-body">
                        <form id="form-step-1">
                            <div class="form-group"><label>Shift *</label>
                                <select name="id_shift" class="form-control" required>
                                    <option value="">Pilih Shift</option>
                                    @foreach($shift as $s) <option value="{{ $s->id }}">{{ $s->shift }}</option> @endforeach
                                </select>
                            </div>
                            <div class="form-group"><label>Tanggal *</label>
                                @php
                                    $now = \Carbon\Carbon::now('Asia/Jakarta');
                                    $displayValue = $now->format('Y-m-d');
                                @endphp
                                <input type="date" name="tanggal" class="form-control" value="{{ $displayValue }}" required>
                            </div>
                            <div class="form-group"><label>Jam *</label>
                                <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                            </div>
                            <div class="form-group"><label>Nama Produk *</label>
                                <select name="id_produk" id="id_produk_step1" class="form-control select2-field" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($produk as $p) <option value="{{ $p->id }}">{{ $p->nama_produk }}</option> @endforeach
                                </select>
                            </div>
                            <div class="form-group"><label>Berat Produk (gr) *</label>
                                <select id="nilai_select_berat" name="berat_produk" class="form-control" data-nilai-berat="true"></select>
                            </div>
                            <div class="form-group"><label>Kode Produksi *</label>
                                <input type="text" name="kode_produksi" class="form-control" placeholder="Contoh: BN12345" required>
                            </div>
                            <div class="form-group"><label>No Of Strokes *</label>
                                <input type="text" name="no_of_strokes" class="form-control" required>
                            </div>
                            <div class="form-group"><label>Pukul *</label>
                                <input type="text" name="waktu_pemasakan" class="form-control" required>
                            </div>
                            <div class="form-group"><label>Hasil Pencetakan</label>
                                <select name="hasil_pencetakan" class="form-control">
                                    <option value="">Pilih Hasil (Opsional)</option>
                                    <option value="✔">✔ OK</option>
                                    <option value="✘">✘ Tidak OK</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-right">
                        <button type="button" class="btn btn-primary next-step" data-step="1">Simpan &amp; Lanjut <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 2: PREDUST -->
            <div class="wizard-step" id="step-2" style="display:none;">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-mortar-pestle mr-1"></i> Form Predust (Langkah 2)</h3></div>
                    <div class="card-body">
                        <div class="alert alert-info py-2"><strong>Produk:</strong> <span class="info_produk">-</span></div>
                        <form id="form-step-2">
                            <input type="hidden" name="penggorengan_uuid" id="p2_penggorengan_uuid">
                            <div class="form-group"><label>Tanggal *</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ $displayValue }}" required>
                            </div>
                            <div class="form-group"><label>Jam *</label>
                                <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                            </div>
                            <div class="form-group"><label>Nama Produk *</label>
                                <select name="id_produk" id="id_produk_step2" class="form-control select2-field" required>
                                    @foreach($produk as $p) <option value="{{ $p->id }}">{{ $p->nama_produk }}</option> @endforeach
                                </select>
                            </div>
                            <div class="form-group"><label>Jenis Predust *</label>
                                <select name="id_jenis_predust" id="id_jenis_predust_step2" class="form-control" required>
                                    <option value="">Pilih Jenis Predust</option>
                                </select>
                            </div>
                            <div class="form-group"><label>Kode Produksi *</label>
                                <input type="text" name="kode_produksi" class="form-control" required>
                            </div>
                            <div class="form-group"><label>Hasil Predust *</label>
                                <select name="hasil_predust" class="form-control" required>
                                    <option value="✔">✔ Baik</option>
                                    <option value="✘">✘ Tidak Baik</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-warning skip-step" data-step="2">Lewati Langkah Ini <i class="fas fa-forward"></i></button>
                        <button type="button" class="btn btn-primary next-step" data-step="2">Simpan &amp; Lanjut <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 3: BATTERING -->
            <div class="wizard-step" id="step-3" style="display:none;">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-tint mr-1"></i> Form Battering (Langkah 3)</h3></div>
                    <div class="card-body">
                        <div class="alert alert-info py-2"><strong>Produk:</strong> <span class="info_produk">-</span></div>
                        <form id="form-step-3">
                            <input type="hidden" name="predust_uuid" id="p3_predust_uuid">
                            <div class="form-group"><label>Tanggal *</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ $displayValue }}" required>
                            </div>
                            <div class="form-group"><label>Jam *</label>
                                <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                            </div>
                            <div class="form-group"><label>Nama Produk *</label>
                                <select name="id_produk" id="id_produk_step3" class="form-control select2-field" required>
                                    @foreach($produk as $p) <option value="{{ $p->id }}">{{ $p->nama_produk }}</option> @endforeach
                                </select>
                            </div>
                            <div class="form-group"><label>Jenis Better <span class="text-danger">*</span></label>
                                <select name="id_jenis_better[]" id="id_jenis_better_step3" multiple="multiple" class="form-control" required>
                                </select>
                            </div>
                            <div class="form-group" id="kode_produksi_better_wrapper" style="display:none;">
                                <label>Kode Produksi Better <span class="text-danger">*</span></label>
                                <div id="kode_produksi_better_container"></div>
                            </div>
                            <div class="form-group"><label>Hasil Better *</label>
                                <select name="hasil_better" class="form-control" required>
                                    <option value="✔">✔ Baik</option>
                                    <option value="✘">✘ Tidak Baik</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <!-- <button type="button" class="btn btn-secondary prev-step" data-step="3"><i class="fas fa-arrow-left"></i> Kembali</button> -->
                        <button type="button" class="btn btn-primary next-step" data-step="3">Simpan &amp; Lanjut <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 4: BREADER -->
            <div class="wizard-step" id="step-4" style="display:none;">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-layer-group mr-1"></i> Form Breader (Langkah 4)</h3></div>
                    <div class="card-body">
                        <div class="alert alert-info py-2"><strong>Produk:</strong> <span class="info_produk">-</span></div>
                        <form id="form-step-4">
                            <input type="hidden" name="battering_uuid" id="p4_battering_uuid">
                            <div class="form-group"><label>Tanggal *</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ $displayValue }}" required>
                            </div>
                            <div class="form-group"><label>Jam *</label>
                                <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                            </div>
                            <div class="form-group"><label>Nama Produk *</label>
                                <select name="id_produk" id="id_produk_step4" class="form-control select2-field" required>
                                    @foreach($produk as $p) <option value="{{ $p->id }}">{{ $p->nama_produk }}</option> @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jenis Breader <span class="text-danger">*</span></label>
                                <select name="id_jenis_breader[]" id="id_jenis_breader_breader" multiple="multiple" class="form-control select2-field" required>
                                </select>
                            </div>
                            <div class="form-group" id="kode_produksi_breader_wrapper" style="display:none;">
                                <label>Kode Produksi Breader <span class="text-danger">*</span></label>
                                <div id="kode_produksi_breader_container"></div>
                            </div>

                            <div class="form-group"><label>Hasil Breader *</label>
                                <select name="hasil_breader" class="form-control" required>
                                    <option value="✔">✔ Baik</option>
                                    <option value="✘">✘ Tidak Baik</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <!-- <button type="button" class="btn btn-secondary prev-step" data-step="4"><i class="fas fa-arrow-left"></i> Kembali</button> -->
                        <button type="button" class="btn btn-primary next-step" data-step="4">Simpan &amp; Lanjut <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 5: FRYER -->
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
                                            <select class="form-control select2-produk p5-produk-select" id="id_produk_f2" name="id_produk" required>
                                            @elseif($fn == 2)
                                            <select class="form-control select2-produk p5-produk-select" id="id_produk_f3" name="id_produk" required>
                                            @elseif($fn == 3)
                                            <select class="form-control select2-produk p5-produk-select" id="id_produk_f4" name="id_produk" required>
                                            @elseif($fn == 4)
                                            <select class="form-control select2-produk p5-produk-select" id="id_produk_f4_tab" name="id_produk" required>
                                            @elseif($fn == 5)
                                            <select class="form-control select2-produk p5-produk-select" id="id_produk_f5_tab" name="id_produk" required>
                                            @endif
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produk as $product)
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
                                                    <input type="date" id="p5_f{{ $fn }}_tanggal" name="tanggal" class="form-control" value="{{ $displayValue }}" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Jam <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="time" id="p5_f{{ $fn }}_jam" class="form-control" value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') }}">
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
                                            @elseif($fn == 5)
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
                                                <label class="col-sm-4 col-form-label">Aktual Suhu (°C)</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="aktual_suhu[]" class="form-control f-aktual-suhu" placeholder="0.0">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Aktual Penggorengan (detik)</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="aktual_penggorengan[]" class="form-control f-aktual-waktu" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">TPM Minyak (%)</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="tpm_minyak[]" class="form-control f-tpm" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Keterangan</label>
                                                <div class="col-sm-8">
                                                    <textarea name="keterangan[]" class="form-control f-keterangan" rows="2" placeholder="Catatan tambahan"></textarea>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <button type="button" class="btn btn-danger btn-sm btn-save-f" data-fn="{{ $fn }}"><i class="fas fa-save"></i> Simpan Fryer {{ $fn }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <!-- <button type="button" class="btn btn-secondary prev-step" data-step="5"><i class="fas fa-arrow-left"></i> Kembali</button> -->
                        <button type="button" class="btn btn-primary next-step" data-step="5">Simpan &amp; Lanjut <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 6: ROASTING FAN -->
            <div class="wizard-step" id="step-6" style="display:none;">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-fan mr-1"></i> Form Roasting Fan (Langkah 6)</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info py-2"><strong>Produk:</strong> <span class="info_produk">-</span></div>
                        
                        <form id="form-step-6">
                            @csrf
                             <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Tanggal <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="date" name="tanggal" class="form-control" value="{{ $displayValue }}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"><i class="fas fa-clock"></i> Jam <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="time" class="form-control" name="jam" value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') }}" required>
                                </div>
                            </div>
                              <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Waktu Pemasakan (pukul)</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="aktual_waktu" placeholder="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Waktu Pengecekan (menit)</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="waktu_pemasakan" placeholder="menit">
                                </div>
                            </div>
                            <div class="form-group row d-none">
                                <label class="col-sm-2 col-form-label">Produk <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-control product-selector" id="p6_id_produk" name="id_produk" data-target-table="#p6-product-details-table" required>
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach($produk as $p)
                                            <option value="{{ $p->id }}">{{ $p->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="card card-primary card-outline mt-3">
                                <div class="card-header py-2">
                                    <h3 class="card-title text-sm">Detail Proses Roasting Fan</h3>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table id="p6-product-details-table" class="table table-bordered mb-0">
                                            <thead>
                                                <tr><th class="text-center">Pilih Produk Terlebih Dahulu Untuk Menampilkan Isi Tablenya</th></tr>
                                            </thead>
                                            <tbody>
                                                {{-- Rows will be populated by AJAX from app.blade.php --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <!-- <button type="button" class="btn btn-secondary prev-step" data-step="6"><i class="fas fa-arrow-left"></i> Kembali</button> -->
                        <button type="button" class="btn btn-primary next-step" data-step="6">Simpan & Lanjut <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 7: HASIL ROASTING -->
            <div class="wizard-step" id="step-7" style="display:none;">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-check-double mr-1"></i> Form Hasil Roasting (Langkah 7)</h3></div>
                    <div class="card-body">
                        <div class="alert alert-info py-2"><strong>Produk:</strong> <span class="info_produk">-</span></div>
                        <form id="form-step-7">
                            <input type="hidden" name="roasting_fan_uuid" id="p7_roasting_fan_uuid">
                            <div class="form-group"><label>Tanggal *</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ $displayValue }}" required>
                            </div>
                            <div class="form-group"><label>Jam *</label>
                                <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                            </div>
                            <div class="form-group"><label>Nama Produk *</label>
                                <select name="id_produk" id="p7_id_produk" class="form-control select2-produk" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($produk as $p) <option value="{{ $p->id }}">{{ $p->nama_produk }}</option> @endforeach
                                </select>
                            </div>
                            <div class="form-group"><label>Std Suhu Pusat *</label>
                                <select name="id_std_suhu_pusat" id="p7_id_std_suhu_pusat" class="form-control" required disabled>
                                    <option value="">Pilih</option>
                                </select>
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
                                @foreach(['kematangan','kenampakan','warna','rasa','bau','tekstur'] as $sen)
                                <div class="col-4 mb-2"><label class="small mb-1 text-capitalize">{{$sen}}</label>
                                    <select name="sensori_{{$sen}}" class="form-control form-control-sm"><option value="✔">✔</option><option value="✘">✘</option></select>
                                </div>
                                @endforeach
                            </div>
                            <div class="form-group mt-3">
                                <label>Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <!-- <button type="button" class="btn btn-secondary prev-step" data-step="7"><i class="fas fa-arrow-left"></i> Kembali</button> -->
                        <button type="button" class="btn btn-primary next-step" data-step="7">Simpan &amp; Lanjut <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 8: IQF ROASTING -->
            <div class="wizard-step" id="step-8" style="display:none;">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-snowflake mr-1"></i> Form IQF Roasting (Langkah 8)</h3></div>
                    <div class="card-body">
                        <div class="alert alert-info py-2"><strong>Produk:</strong> <span class="info_produk">-</span></div>
                        <form id="form-step-8">
                            <input type="hidden" name="hasil_roasting_uuid" id="p8_hasil_roasting_uuid">
                            <input type="hidden" name="frayer_uuid"    id="p8_frayer_uuid">
                            <input type="hidden" name="frayer2_uuid"   id="p8_frayer2_uuid">
                            <input type="hidden" name="frayer3_uuid"   id="p8_frayer3_uuid">
                            <input type="hidden" name="frayer4_uuid"   id="p8_frayer4_uuid">
                            <input type="hidden" name="frayer5_uuid"   id="p8_frayer5_uuid">
                            <input type="hidden" name="breader_uuid"   id="p8_breader_uuid">
                            <input type="hidden" name="battering_uuid" id="p8_battering_uuid">
                            <input type="hidden" name="predust_uuid"   id="p8_predust_uuid">
                            <div class="form-group"><label>Tanggal *</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ $displayValue }}" required>
                            </div>
                            <div class="form-group"><label>Jam *</label>
                                <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                            </div>
                            <div class="form-group"><label>Suhu Ruang IQF (°C) *</label>
                                <input type="text" name="suhu_ruang_iqf" class="form-control" required>
                            </div>
                            <div class="form-group"><label>Holding Time (Menit) *</label>
                                <input type="text" name="holding_time" class="form-control" required>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <!-- <button type="button" class="btn btn-secondary prev-step" data-step="8"><i class="fas fa-arrow-left"></i> Kembali</button> -->
                        <button type="button" class="btn btn-primary next-step" data-step="8"><i class="fas fa-check-circle mr-1"></i> Selesaikan Proses 2</button>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

{{-- Loading Overlay --}}
<div id="loadingOverlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.4);z-index:9999;align-items:center;justify-content:center;">
    <div class="text-center text-white">
        <div class="spinner-border mb-2" style="width:3rem;height:3rem;"></div>
        <div id="loadingText" style="font-size:1.1rem;">Menyimpan data...</div>
    </div>
</div>

<style>
    #loadingOverlay { display: none !important; }
    #loadingOverlay.show { display: flex !important; }
    .wizard-step-indicator.locked .step-circle    { background:#f8f9fa;  color:#ced4da; border-color:#dee2e6; }
    .wizard-step-indicator.active .step-circle    { background:#007bff;  color:#fff;    border-color:#007bff; box-shadow:0 0 0 3px rgba(0,123,255,.2); }
    .wizard-step-indicator.completed .step-circle { background:#28a745;  color:#fff;    border-color:#28a745; }
    .step-line { align-self:center; margin-top:-20px; }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof jQuery === 'undefined') { console.error('jQuery tidak ditemukan!'); return; }
    const $ = jQuery;

    // ========== STATE ==========
    const STORAGE_KEY = 'wizard_proses2_state_v1';
    let state = {
        current_step: 1, total_steps: 8,
        penggorengan_uuid: null, predust_uuid: null, battering_uuid: null,
        breader_uuid: null, frayer_uuids: {}, roasting_fan_uuid: null,
        hasil_roasting_uuid: null, id_produk: null, id_produk_text: null,
        user_id: "{{ Auth::id() }}"
    };

    try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
            const parsed = JSON.parse(saved);
            // VALIDASI USER: Jika user_id di draf tidak sama dengan user sekarang, abaikan draf.
            if (parsed && typeof parsed === 'object') {
                if (parsed.user_id && parsed.user_id !== state.user_id) {
                    localStorage.removeItem(STORAGE_KEY);
                } else {
                    Object.assign(state, parsed);
                }
            }
        }
    } catch (_) {}

    function persistState() {
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify(state)); } catch(_) {}
    }

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ========== LOADING ==========
    function showLoading(text) {
        document.getElementById('loadingText').textContent = text || 'Menyimpan data...';
        document.getElementById('loadingOverlay').classList.add('show');
    }
    function hideLoading() { document.getElementById('loadingOverlay').classList.remove('show'); }

    // ========== ALERT ==========
    function showAlert(type, title, message) {
        const el = document.getElementById('wizardAlert');
        el.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show"><strong>${title}!</strong> ${message}<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>`;
        el.classList.remove('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    function showErrors(errors) {
        let html = '<ul class="mb-0">';
        Object.values(errors).forEach(msgs => msgs.forEach(m => { html += `<li>${m}</li>`; }));
        showAlert('danger', 'Error Validasi', html + '</ul>');
    }

    // ========== STEPPER ==========
    function updateStepper(step) {
        for (let i = 1; i <= state.total_steps; i++) {
            const el = document.getElementById(`step-indicator-${i}`);
            if (!el) continue;
            el.classList.remove('active', 'completed', 'locked');
            if (i < step) el.classList.add('completed');
            else if (i === step) el.classList.add('active');
            else el.classList.add('locked');
        }
    }

    function initSelect2() {
        if (typeof $.fn.select2 === 'undefined') return;
        $('.select2-field').each(function() {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                const isMultiple = $(this).prop('multiple');
                $(this).select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: isMultiple ? 'Pilih satu atau lebih' : 'Pilih Opsi',
                    allowClear: true
                });
            }
        });
    }

    function goToStep(step) {
        if (step < 1 || step > state.total_steps) return;
        document.querySelectorAll('.wizard-step').forEach(el => el.style.display = 'none');
        const target = document.getElementById(`step-${step}`);
        if (target) target.style.display = 'block';
        state.current_step = step;
        updateStepper(step);
        persistState();
        
        // Re-init select2 hanya untuk field yang memang select2
        initSelect2();

        // Isi id_produk berdasarkan pilihan di step 1 jika form ini memiliki field id_produk
        const prodFields = target.querySelectorAll('[name="id_produk"], .select2-produk, .product-selector');
        if (prodFields.length > 0 && state.id_produk) {
            // Gunakan setTimeout agar trigger('change') diproses setelah semua script layout (app.blade) ter-load sepenuhnya.
            setTimeout(() => {
                prodFields.forEach(field => {
                    $(field).val(state.id_produk).trigger('change');
                });
            }, 100);
        }

        // Khusus untuk trigger load data tambahan jika perlu
        if(step === 2) { loadPredust(state.id_produk); }
        if(step === 3) { loadBetter(state.id_produk); }
        if(step === 4) { loadBreader(state.id_produk); }
        if(step === 7) { loadStdSuhuPusat(state.id_produk); }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ========== NATIVE FETCH (bukan jQuery $.ajax) ==========
    function ajaxPost(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(data)
        }).then(r => r.json());
    }

    // ========== STEP 1: PENGGORENGAN ==========
    document.querySelector('#step-1 .next-step')?.addEventListener('click', function() {
        const f = document.getElementById('form-step-1');
        const pEl = f.querySelector('[name="id_produk"]');
        const d = { id_produk: pEl.value, id_shift: f.querySelector('[name="id_shift"]').value, kode_produksi: f.querySelector('[name="kode_produksi"]').value, berat_produk: document.getElementById('nilai_select_berat').value, no_of_strokes: f.querySelector('[name="no_of_strokes"]').value, waktu_pemasakan: f.querySelector('[name="waktu_pemasakan"]').value, jam: f.querySelector('[name="jam"]').value, tanggal: f.querySelector('[name="tanggal"]').value, hasil_pencetakan: f.querySelector('[name="hasil_pencetakan"]').value };
        if (!d.id_produk || !d.id_shift || !d.kode_produksi || !d.berat_produk) return showAlert('warning', 'Peringatan', 'Harap lengkapi semua field wajib.');
        showLoading('Menyimpan data Penggorengan...');
        ajaxPost('{{ route("wizard.proses2.store-penggorengan") }}', d).then(res => {
            hideLoading();
            if (res.success) {
                state.penggorengan_uuid = res.uuid; state.id_produk = d.id_produk; state.id_produk_text = pEl.options[pEl.selectedIndex]?.text || '';
                document.querySelectorAll('.info_produk').forEach(el => el.textContent = state.id_produk_text);
                document.getElementById('p2_penggorengan_uuid').value = res.uuid;
                
                // Pre-select produk di step selanjutnya
                document.getElementById('id_produk_step2').value = state.id_produk;
                document.getElementById('id_produk_step3').value = state.id_produk;
                document.getElementById('id_produk_step4').value = state.id_produk;
                
                goToStep(2); showAlert('success', 'Berhasil', 'Data Penggorengan berhasil disimpan.');
            } else { res.errors ? showErrors(res.errors) : showAlert('danger', 'Gagal', res.message || 'Terjadi kesalahan.'); }
        }).catch(e => { hideLoading(); showAlert('danger', 'Error', e.message); });
    });

    // ========== STEP 2: PREDUST ==========
    document.querySelector('#step-2 .next-step')?.addEventListener('click', function() {
        const f = document.getElementById('form-step-2');
        const d = { id_produk: f.querySelector('[name="id_produk"]').value, id_jenis_predust: f.querySelector('[name="id_jenis_predust"]').value, kode_produksi: f.querySelector('[name="kode_produksi"]').value, hasil_predust: f.querySelector('[name="hasil_predust"]').value, tanggal: f.querySelector('[name="tanggal"]').value, jam: f.querySelector('[name="jam"]').value, penggorengan_uuid: state.penggorengan_uuid };
        if (!d.id_produk || !d.id_jenis_predust || !d.kode_produksi) return showAlert('warning', 'Peringatan', 'Harap lengkapi semua field wajib.');
        showLoading('Menyimpan data Predust...');
        ajaxPost('{{ route("wizard.proses2.store-predust") }}', d).then(res => {
            hideLoading();
            if (res.success) { state.predust_uuid = res.uuid; document.getElementById('p3_predust_uuid').value = res.uuid; goToStep(3); showAlert('success', 'Berhasil', 'Data Predust berhasil disimpan.'); }
            else { res.errors ? showErrors(res.errors) : showAlert('danger', 'Gagal', res.message || 'Terjadi kesalahan.'); }
        }).catch(e => { hideLoading(); showAlert('danger', 'Error', e.message); });
    });

    // SKIP STEP 2 logic
    document.querySelector('#step-2 .skip-step')?.addEventListener('click', function() {
        if(confirm('Apakah Anda yakin ingin melewati proses Predust ini?')) {
            state.predust_uuid = null;
            document.getElementById('p3_predust_uuid').value = '';
            goToStep(3);
            showAlert('info', 'Info', 'Langkah Predust dilewati.');
        }
    });

    // ========== STEP 3: BATTERING ==========
    document.querySelector('#step-3 .next-step')?.addEventListener('click', function() {
        const f = document.getElementById('form-step-3');
        const betterSelect = document.getElementById('id_jenis_better_step3');
        const selectedBetters = Array.from(betterSelect.selectedOptions).map(o => o.value);
        let isValid = true;

        // Validasi produk
        const produkEl = f.querySelector('[name="id_produk"]');
        if (!produkEl || !produkEl.value) { produkEl?.classList.add('is-invalid'); isValid = false; }
        else { produkEl?.classList.remove('is-invalid'); }

        // Validasi jenis better
        if (selectedBetters.length === 0) { betterSelect.classList.add('is-invalid'); isValid = false; }
        else { betterSelect.classList.remove('is-invalid'); }

        // Kumpulkan kode produksi better (array)
        const kodeProduksiBetter = {};
        let kodeProduksiValid = true;
        f.querySelectorAll('input[name^="kode_produksi_better["]').forEach(inp => {
            const match = inp.name.match(/\[(.*?)\]/);
            if (match) {
                const id = match[1];
                if (!inp.value.trim()) { inp.classList.add('is-invalid'); kodeProduksiValid = false; }
                else { inp.classList.remove('is-invalid'); }
                kodeProduksiBetter[id] = inp.value.trim();
            }
        });
        if (selectedBetters.length > 0 && Object.keys(kodeProduksiBetter).length === 0) kodeProduksiValid = false;
        if (!kodeProduksiValid) isValid = false;

        if (!isValid) return showAlert('warning', 'Peringatan', 'Harap lengkapi semua field wajib.');

        const d = {
            id_produk: produkEl.value,
            id_jenis_better: selectedBetters,
            kode_produksi_better: kodeProduksiBetter,
            hasil_better: f.querySelector('[name="hasil_better"]').value,
            tanggal: f.querySelector('[name="tanggal"]').value,
            jam: f.querySelector('[name="jam"]').value,
            predust_uuid: state.predust_uuid,
            penggorengan_uuid: state.penggorengan_uuid // Kirim ini juga
        };

        showLoading('Menyimpan data Battering...');
        ajaxPost('{{ route("wizard.proses2.store-battering") }}', d).then(res => {
            hideLoading();
            if (res.success) { state.battering_uuid = res.uuid; document.getElementById('p4_battering_uuid').value = res.uuid; goToStep(4); showAlert('success', 'Berhasil', 'Data Battering berhasil disimpan.'); }
            else { res.errors ? showErrors(res.errors) : showAlert('danger', 'Gagal', res.message || 'Terjadi kesalahan.'); }
        }).catch(e => { hideLoading(); showAlert('danger', 'Error', e.message); });
    });

    // ========== STEP 4: BREADER ==========
    document.querySelector('#step-4 .next-step')?.addEventListener('click', function() {
        const f = document.getElementById('form-step-4');
        const breaderSelect = f.querySelector('[name="id_jenis_breader[]"]');
        const selectedBreaders = Array.from(breaderSelect.selectedOptions).map(o => o.value);
        let isValid = true;

        // Validasi produk
        const produkEl = f.querySelector('[name="id_produk"]');
        if (!produkEl || !produkEl.value) { produkEl?.classList.add('is-invalid'); isValid = false; }
        else { produkEl?.classList.remove('is-invalid'); }

        // Validasi jenis breader
        if (selectedBreaders.length === 0) { breaderSelect.classList.add('is-invalid'); isValid = false; }
        else { breaderSelect.classList.remove('is-invalid'); }

        // Kumpulkan kode produksi breader (array)
        const kodeProduksiBreader = {};
        let kodeProduksiValid = true;
        f.querySelectorAll('input[name^="kode_produksi_breader["]').forEach(inp => {
            const match = inp.name.match(/\[(.*?)\]/);
            if (match) {
                const id = match[1];
                if (!inp.value.trim()) { inp.classList.add('is-invalid'); kodeProduksiValid = false; }
                else { inp.classList.remove('is-invalid'); }
                kodeProduksiBreader[id] = inp.value.trim();
            }
        });
        if (selectedBreaders.length > 0 && Object.keys(kodeProduksiBreader).length === 0) kodeProduksiValid = false;
        if (!kodeProduksiValid) isValid = false;

        if (!isValid) return showAlert('warning', 'Peringatan', 'Pilih minimal 1 Breader dan lengkapi semua field.');

        const d = {
            id_produk: produkEl.value,
            id_jenis_breader: selectedBreaders,
            kode_produksi_breader: kodeProduksiBreader,
            hasil_breader: f.querySelector('[name="hasil_breader"]').value,
            tanggal: f.querySelector('[name="tanggal"]').value,
            jam: f.querySelector('[name="jam"]').value,
            battering_uuid: state.battering_uuid,
            predust_uuid: state.predust_uuid, // TAMBAHKAN INI
            penggorengan_uuid: state.penggorengan_uuid // TAMBAHKAN INI
        };

        showLoading('Menyimpan data Breader...');
        ajaxPost('{{ route("wizard.proses2.store-breader") }}', d).then(res => {
            hideLoading();
            if (res.success) { 
                state.breader_uuid = res.uuid; 
                state.id_produk = d.id_produk;
                
                document.querySelectorAll('.p5-produk-select').forEach(sel => {
                    $(sel).val(d.id_produk).trigger('change');
                });
                
                const p6Prod = document.getElementById('p6_id_produk');
                if (p6Prod) {
                    $(p6Prod).val(d.id_produk).trigger('change');
                }
                
                const prodSelect = f.querySelector('[name="id_produk"]');
                document.getElementById('p5_info_produk').innerText = prodSelect.options[prodSelect.selectedIndex]?.text || '-';
                goToStep(5); 
                showAlert('success', 'Berhasil', 'Data Breader berhasil disimpan.'); 
            }
            else { res.errors ? showErrors(res.errors) : showAlert('danger', 'Gagal', res.message || 'Terjadi kesalahan.'); }
        }).catch(e => { hideLoading(); showAlert('danger', 'Error', e.message); });
    });

    // ========== STEP 5: FRAYER checkbox toggle ==========
    document.querySelectorAll('.frayer-checkbox').forEach(chk => {
        chk.addEventListener('change', function() {
            const fn = parseInt(this.value);
            const formEl = document.getElementById(`frayer-form-${fn}`);
            if (!formEl) return;
            if (this.checked) {
                formEl.classList.remove('d-none');
            } else {
                formEl.classList.add('d-none');
            }
        });
    });

    // ========== STEP 5: Simpan per-Fryer ==========
    document.querySelectorAll('.btn-save-f').forEach(btn => {
        btn.addEventListener('click', function() {
            const fn = parseInt(this.dataset.fn), card = this.closest('.card');
            const sid = card.querySelector('.f-suhu')?.value, wid = card.querySelector('.f-waktu')?.value;
            const aw = card.querySelector('.f-aktual-waktu')?.value, tpm = card.querySelector('.f-tpm')?.value;
            if (!sid || !wid || !aw || !tpm) return showAlert('warning', 'Peringatan', `Data Fryer ${fn} belum lengkap!`);
            const origHtml = this.innerHTML;
            this.disabled = true; this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>...';
            const dF = { 
                frayer_number: fn, 
                id_produk: state.id_produk, 
                tanggal: document.querySelector('#form-step-1 [name="tanggal"]')?.value, 
                jam: document.querySelector('#form-step-1 [name="jam"]')?.value, 
                id_suhu_frayer_1: sid, id_suhu_frayer_2: sid, id_suhu_frayer_3: sid, id_suhu_frayer_4: sid, id_suhu_frayer_5: sid, 
                id_waktu_penggorengan: wid, id_waktu_penggorengan_2: wid, id_waktu_penggorengan_3: wid, id_waktu_penggorengan_4: wid, id_waktu_penggorengan_5: wid, 
                aktual_suhu_penggorengan: card.querySelector('.f-aktual-suhu')?.value || null, 
                aktual_penggorengan: aw, tpm_minyak: tpm, 
                keterangan: card.querySelector('.f-keterangan')?.value || null, 
                breader_uuid: state.breader_uuid, 
                battering_uuid: state.battering_uuid, // TAMBAHKAN INI
                predust_uuid: state.predust_uuid,     // TAMBAHKAN INI
                penggorengan_uuid: state.penggorengan_uuid, // TAMBAHKAN INI
                prev_frayer_uuid: (fn > 1) ? (state.frayer_uuids[fn - 1] || null) : null 
            };
            showLoading(`Menyimpan Fryer ${fn}...`);
            ajaxPost('{{ route("wizard.proses2.store-frayer") }}', dF).then(res => {
                hideLoading();
                if (res.success) { state.frayer_uuids[fn] = res.uuid; document.getElementById(`f${fn}-status`)?.classList.remove('d-none'); this.innerHTML = '<i class="fas fa-check mr-1"></i> Tersimpan'; showAlert('success', 'Berhasil', `Fryer ${fn} berhasil disimpan.`); }
                else { this.disabled = false; this.innerHTML = origHtml; showAlert('danger', 'Gagal', res.message || 'Terjadi kesalahan.'); }
            }).catch(e => { hideLoading(); this.disabled = false; this.innerHTML = origHtml; showAlert('danger', 'Error', e.message); });
        });
    });

    // ========== STEP 5 → 6 ==========
    document.querySelector('#step-5 .next-step')?.addEventListener('click', function() {
        const checked = document.querySelectorAll('.frayer-checkbox:checked');
        if (checked.length === 0) return showAlert('warning', 'Peringatan', 'Pilih minimal satu Fryer!');
        let allSaved = true;
        checked.forEach(chk => { if (!state.frayer_uuids[chk.value]) allSaved = false; });
        if (!allSaved) return showAlert('warning', 'Peringatan', 'Ada Fryer terpilih yang belum disimpan!');
        
        // Update hidden inputs for step 8/final result if needed
        const p8_frayer_1 = document.getElementById('p8_frayer_uuid');
        if (p8_frayer_1) p8_frayer_1.value = state.frayer_uuids[1] || '';
        
        const p8_frayer_2 = document.getElementById('p8_frayer2_uuid');
        if (p8_frayer_2) p8_frayer_2.value = state.frayer_uuids[2] || '';
        
        const p8_frayer_3 = document.getElementById('p8_frayer3_uuid');
        if (p8_frayer_3) p8_frayer_3.value = state.frayer_uuids[3] || '';
        
        const p8_frayer_4 = document.getElementById('p8_frayer4_uuid');
        if (p8_frayer_4) p8_frayer_4.value = state.frayer_uuids[4] || '';
        
        const p8_frayer_5 = document.getElementById('p8_frayer5_uuid');
        if (p8_frayer_5) p8_frayer_5.value = state.frayer_uuids[5] || '';

        goToStep(6);
    });

    // ========== STEP 6: ROASTING FAN ==========
    document.querySelector('#step-6 .next-step')?.addEventListener('click', function() {
        const f = document.getElementById('form-step-6');
        
        // Helper function to get first non-empty value from an array of values
        const getFirstNonEmpty = (values) => {
            for (let v of values) {
                if (v && v.trim() !== '') return v;
            }
            return '';
        };

        // 1. Ambil data dasar
        const d = { 
            id_produk: state.id_produk, 
            tanggal: f.querySelector('[name="tanggal"]')?.value || '', 
            jam: f.querySelector('[name="jam"]')?.value || '',
            waktu_pemasakan: f.querySelector('[name="waktu_pemasakan"]')?.value || '',
            aktual_lama_proses: f.querySelector('[name="aktual_lama_proses"]')?.value || '', 
            
            // Relasi UUID
            penggorengan_uuid: state.penggorengan_uuid || null,
            predust_uuid: state.predust_uuid || null,
            battering_uuid: state.battering_uuid || null,
            breader_uuid: state.breader_uuid || null,
            frayer_uuid: state.frayer_uuids[1] || null,
            frayer2_uuid: state.frayer_uuids[2] || null,
            frayer3_uuid: state.frayer_uuids[3] || null,
            frayer4_uuid: state.frayer_uuids[4] || null,
            frayer5_uuid: state.frayer_uuids[5] || null,
            
            // Blok data (JSON format)
            id_suhu_blok: [],
            id_std_fan: [],
            block_number: [],
            suhu_roasting: [],
            fan_1: [],
            fan_2: [],
            fan_3: [],
            fan_4: [],
            aktual_humadity: [],
            infra_red: [],
            
            is_grouped: 1 
        };

        // 2. Ambil data dari tabel dinamis
        const rowInputs = (name) => Array.from(f.querySelectorAll(`[name="${name}"]`)).map(el => el.value);
        
        d.id_suhu_blok = rowInputs('id_suhu_blok[]');
        d.id_std_fan = rowInputs('id_std_fan[]');
        d.block_number = rowInputs('block_number[]');
        d.suhu_roasting = rowInputs('suhu_roasting[]');
        d.fan_1 = rowInputs('fan_1[]');
        d.fan_2 = rowInputs('fan_2[]');
        d.fan_3 = rowInputs('fan_3[]');
        d.fan_4 = rowInputs('fan_4[]');
        d.aktual_humadity = rowInputs('aktual_humadity[]');
        d.infra_red = rowInputs('infra_red[]');

        // 3. Tambahkan field tunggal untuk melewati validasi controller (Required fields)
        // Ambil nilai dari blok pertama yang diisi
        d.aktual_suhu_fan = getFirstNonEmpty(d.suhu_roasting);
        d.aktual_speed_fan = getFirstNonEmpty(d.fan_1);
        // aktual_waktu = ambil langsung dari field "Waktu Pemasakan (pukul)" (name="aktual_waktu")
        // Jangan timpa dengan aktual_lama_proses karena itu field berbeda
        d.aktual_waktu = f.querySelector('[name="aktual_waktu"]')?.value || '';

        console.log('DEBUG STEP 6 DATA BEFORE SEND:', d);

        if (!d.suhu_roasting.some(v => v !== '') && !d.fan_1.some(v => v !== '')) {
            return showAlert('warning', 'Peringatan', 'Harap isi setidaknya satu kolom data pada tabel Roasting.');
        }

        showLoading('Menyimpan data Roasting Fan...');
        ajaxPost('{{ route("wizard.proses2.store-roasting-fan") }}', d).then(res => {
            hideLoading();
            if (res.success) { 
                state.roasting_fan_uuid = res.uuid; 
                const p7Field = document.getElementById('p7_roasting_fan_uuid');
                if (p7Field) p7Field.value = res.uuid; 
                goToStep(7); 
                showAlert('success', 'Berhasil', 'Data Roasting Fan berhasil disimpan.'); 
            }
            else { res.errors ? showErrors(res.errors) : showAlert('danger', 'Gagal', res.message || 'Terjadi kesalahan.'); }
        }).catch(e => { hideLoading(); showAlert('danger', 'Error', e.message); });
    });

    // Dynamic Suhu Pusat Add/Remove (Vanilla JS Event Delegation)
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

    // ========== STEP 7: HASIL ROASTING ==========
    document.querySelector('#step-7 .next-step')?.addEventListener('click', function() {
        const f = document.getElementById('form-step-7');

        // Kumpulkan aktual_suhu_pusat sebagai array
        const suhuInputs = f.querySelectorAll('input[name="aktual_suhu_pusat[]"]');
        const suhuValues = Array.from(suhuInputs).map(inp => inp.value.trim()).filter(v => v !== '');

        const d = { 
            id_produk: f.querySelector('[name="id_produk"]').value, 
            id_std_suhu_pusat: f.querySelector('[name="id_std_suhu_pusat"]').value, 
            aktual_suhu_pusat: suhuValues, 
            keterangan: f.querySelector('[name="keterangan"]').value,
            tanggal: f.querySelector('[name="tanggal"]').value, 
            jam: f.querySelector('[name="jam"]').value, 
            sensori_kematangan: f.querySelector('[name="sensori_kematangan"]').value || null, 
            sensori_kenampakan: f.querySelector('[name="sensori_kenampakan"]').value || null, 
            sensori_warna: f.querySelector('[name="sensori_warna"]').value || null, 
            sensori_rasa: f.querySelector('[name="sensori_rasa"]').value || null, 
            sensori_bau: f.querySelector('[name="sensori_bau"]').value || null, 
            sensori_tekstur: f.querySelector('[name="sensori_tekstur"]').value || null, 
            
            // Relasi UUID Lengkap
            roasting_fan_uuid: state.roasting_fan_uuid, 
            frayer_uuid: state.frayer_uuids[1] || null, 
            frayer2_uuid: state.frayer_uuids[2] || null, 
            frayer3_uuid: state.frayer_uuids[3] || null, 
            frayer4_uuid: state.frayer_uuids[4] || null, 
            frayer5_uuid: state.frayer_uuids[5] || null,
            breader_uuid: state.breader_uuid || null,
            battering_uuid: state.battering_uuid || null,
            predust_uuid: state.predust_uuid || null,
            penggorengan_uuid: state.penggorengan_uuid || null
        };
        if (!d.id_produk || !d.id_std_suhu_pusat || suhuValues.length === 0) return showAlert('warning', 'Peringatan', 'Harap isi minimal 1 Aktual Suhu Pusat dan lengkapi field wajib.');
        showLoading('Menyimpan data Hasil Roasting...');
        ajaxPost('{{ route("wizard.proses2.store-hasil-roasting") }}', d).then(res => {
            hideLoading();
            if (res.success) { state.hasil_roasting_uuid = res.uuid; document.getElementById('p8_hasil_roasting_uuid').value = res.uuid; goToStep(8); showAlert('success', 'Berhasil', 'Data Hasil Roasting berhasil disimpan.'); }
            else { res.errors ? showErrors(res.errors) : showAlert('danger', 'Gagal', res.message || 'Terjadi kesalahan.'); }
        }).catch(e => { hideLoading(); showAlert('danger', 'Error', e.message); });
    });

    // ========== STEP 8: IQF ROASTING (FINISH) ==========
    document.querySelector('#step-8 .next-step')?.addEventListener('click', function() {
        const f = document.getElementById('form-step-8');
        const d = { 
            tanggal: f.querySelector('[name="tanggal"]').value, 
            jam: f.querySelector('[name="jam"]').value, 
            suhu_ruang_iqf: f.querySelector('[name="suhu_ruang_iqf"]').value, 
            holding_time: f.querySelector('[name="holding_time"]').value, 
            
            // Relasi UUID Lengkap
            hasil_roasting_uuid: state.hasil_roasting_uuid, 
            roasting_fan_uuid: state.roasting_fan_uuid, 
            penggorengan_uuid: state.penggorengan_uuid, 
            frayer_uuid: state.frayer_uuids[1] || null, 
            frayer2_uuid: state.frayer_uuids[2] || null, 
            frayer3_uuid: state.frayer_uuids[3] || null, 
            frayer4_uuid: state.frayer_uuids[4] || null, 
            frayer5_uuid: state.frayer_uuids[5] || null, 
            breader_uuid: state.breader_uuid || null, 
            battering_uuid: state.battering_uuid || null, 
            predust_uuid: state.predust_uuid || null 
        };
        if (!d.suhu_ruang_iqf || !d.holding_time) return showAlert('warning', 'Peringatan', 'Harap lengkapi Suhu Ruang IQF dan Holding Time.');
        showLoading('Menyelesaikan Proses 2 — Roasting...');
        ajaxPost('{{ route("wizard.proses2.store-iqf-roasting") }}', d).then(res => {
            hideLoading();
            if (res.success) { 
                try { localStorage.removeItem(STORAGE_KEY); } catch(_) {}
                window.location.href = res.redirect_url || '{{ route("wizard.proses2.index") }}'; 
            }
            else { res.errors ? showErrors(res.errors) : showAlert('danger', 'Gagal', res.message || 'Terjadi kesalahan.'); }
        }).catch(e => { hideLoading(); showAlert('danger', 'Error', e.message); });
    });

    document.querySelectorAll('.prev-step').forEach(btn => {
        btn.addEventListener('click', function() { goToStep(parseInt(this.dataset.step) - 1); });
    });

    // Inisialisasi Select2 untuk field yang memiliki class select2-field
    $('.select2-field').each(function() {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            const isMultiple = $(this).prop('multiple');
            $(this).select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: isMultiple ? 'Pilih satu atau lebih' : 'Pilih Opsi',
                allowClear: true,
                multiple: isMultiple
            });
        }
    });

    // ========== AJAX DEPENDENT DROPDOWNS ==========
    function loadPredust(idProduk) {
        const targetId = 'id_jenis_predust_step2';
        const sel = $('#' + targetId);
        if(!idProduk) return;
        
        sel.html('<option value="">Memuat...</option>');
        
        // Standardized absolute URL with qc-sistem prefix
        const url = `{{ url('qc-sistem/ajax/get-jenis-predust-by-produk') }}?id_produk=${idProduk}`;
        
        if (typeof getJenisPredustByProduk === "function") {
            getJenisPredustByProduk(idProduk, targetId);
        } else {
            $.get(url, function(data) {
                let html = '<option value="">Pilih Jenis Predust</option>';
                data.forEach(d => { html += `<option value="${d.id}">${d.nama_predust || d.jenis_predust}</option>`; });
                sel.html(html);
            }).fail(function() { 
                sel.html('<option value="">Pilih Jenis Predust</option>'); 
                console.error('Failed to load predust for product:', idProduk);
            });
        }
    }

    function loadBetter(idProduk) {
        const targetId = 'id_jenis_better_step3';
        const sel = $('#' + targetId);
        if(!idProduk) return;
        
        sel.html('<option value="">Memuat...</option>');
        $.get(`{{ url('/qc-sistem/ajax/better-by-produk-better') }}`, { id_produk: idProduk }, function(data) {
            sel.empty();
            data.forEach(b => {
                sel.append(new Option(b.nama_formula_better || b.nama_better || b.jenis_better, b.id, false, false));
            });
            sel.trigger('change');
        }).fail(function() {
            sel.empty().trigger('change');
        });
    }

    function loadBreader(idProduk) {
        const sel = document.getElementById('id_jenis_breader_breader');
        if (!sel || !idProduk) return;
        
        sel.innerHTML = '<option value="">Memuat...</option>';
        // Gunakan URL lengkap dengan prefix qc-sistem sesuai grup route di web.php
        fetch(`{{ url('/qc-sistem/ajax/jenis-breader-by-produk') }}/${idProduk}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(data => {
                sel.innerHTML = '';
                data.forEach(b => { 
                    sel.innerHTML += `<option value="${b.id}">${b.nama_breader || b.jenis_breader}</option>`; 
                });
                // Trigger change agar select2 tahu ada data baru
                $(sel).trigger('change');
            }).catch(() => {
                sel.innerHTML = '';
                $(sel).trigger('change');
            });
    }

    function loadStdSuhuPusat(idProduk) {
        const sel = $('#p7_id_std_suhu_pusat');
        if(!idProduk) {
            sel.html('<option value="">Pilih</option>').prop('disabled', true);
            return;
        }
        
        sel.html('<option value="">Memuat...</option>').prop('disabled', true);
        
        // Correct path as per web.php: /qc-sistem/get-std-suhu-pusat-roasting/{id_produk}
        $.get(`{{ url('qc-sistem/get-std-suhu-pusat-roasting') }}/${idProduk}`, function(data) {
            let html = '<option value="">Pilih</option>';
            if (data && data.length > 0) {
                data.forEach(d => { html += `<option value="${d.id}">${d.std_suhu_pusat_roasting} °C</option>`; });
                sel.html(html).prop('disabled', false);
            } else {
                // Fallback to general std suhu pusat: /qc-sistem/get-std-suhu-pusat-by-produk/{id_produk}
                $.get(`{{ url('qc-sistem/get-std-suhu-pusat-by-produk') }}/${idProduk}`, function(altData) {
                    let htmlAlt = '<option value="">Pilih</option>';
                    if (altData && altData.length > 0) {
                        altData.forEach(d => { htmlAlt += `<option value="${d.id}">${d.std_suhu_pusat} °C</option>`; });
                        sel.html(htmlAlt).prop('disabled', false);
                    } else {
                        sel.html('<option value="">Tidak Ada Data</option>').prop('disabled', true);
                    }
                }).fail(function() {
                    sel.html('<option value="">Tidak Ada Data</option>').prop('disabled', true);
                });
            }
        }).fail(function() {
             $.get(`{{ url('qc-sistem/get-std-suhu-pusat-by-produk') }}/${idProduk}`, function(altData) {
                 let htmlAlt = '<option value="">Pilih</option>';
                 if (altData && altData.length > 0) {
                     altData.forEach(d => { htmlAlt += `<option value="${d.id}">${d.std_suhu_pusat} °C</option>`; });
                     sel.html(htmlAlt).prop('disabled', false);
                 } else {
                     sel.html('<option value="">Tidak Ada Data</option>').prop('disabled', true);
                 }
             }).fail(function() {
                 sel.html('<option value="">Tidak Ada Data</option>').prop('disabled', true);
             });
        });
    }

    $(document).on('change', '#id_produk_step2', function() { loadPredust($(this).val()); });
    $(document).on('change', '#id_produk_step3', function() { loadBetter($(this).val()); });
    $(document).on('change', '#id_produk_step4', function() { loadBreader($(this).val()); });
    $(document).on('change', '#p7_id_produk', function() { loadStdSuhuPusat($(this).val()); });

    // Init Hidden Inputs from State
    if (state.penggorengan_uuid) $('#p2_penggorengan_uuid').val(state.penggorengan_uuid);
    if (state.predust_uuid) $('#p3_predust_uuid').val(state.predust_uuid);
    if (state.battering_uuid) $('#p4_battering_uuid').val(state.battering_uuid);
    if (state.breader_uuid) $('#p5_breader_uuid').val(state.breader_uuid);
    // (Frayer state.frayer_uuids diproses secara parsial jika diperlukan, tapi last Frayer akan menjadi parent Roasting Fan)
    if (state.roasting_fan_uuid) $('#p7_roasting_fan_uuid').val(state.roasting_fan_uuid);
    if (state.hasil_roasting_uuid) $('#p8_hasil_roasting_uuid').val(state.hasil_roasting_uuid);

    // Buka step sesuai state dan pulihkan produk
    if (state.current_step > 1 && state.current_step < state.total_steps) {
        goToStep(state.current_step);
        if (state.id_produk_text) {
             document.querySelectorAll('.info_produk').forEach(el => el.textContent = state.id_produk_text);
        }
        setTimeout(() => showAlert('info', 'Sesi Dipulihkan', 'Melanjutkan pengisian data yang belum selesai.'), 500);
    } else {
        updateStepper(1);
    }
});
</script>
@endsection