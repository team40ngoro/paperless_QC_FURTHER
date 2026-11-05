@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-boxes text-primary"></i> Pengemasan Packing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pengemasan Packing</li>
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
                                1 => ['icon' => 'fa-id-card',       'label' => 'Identitas'],
                                2 => ['icon' => 'fa-snowflake',     'label' => 'Pembekuan'],
                                3 => ['icon' => 'fa-shopping-bag',  'label' => 'Plastik'],
                                4 => ['icon' => 'fa-weight-hanging','label' => 'Berat Pack'],
                                5 => ['icon' => 'fa-cube',          'label' => 'Berat Box'],
                                6 => ['icon' => 'fa-boxes',         'label' => 'Karton'],
                                7 => ['icon' => 'fa-camera',        'label' => 'Dokumentasi'],
                            ];
                        @endphp

                        @foreach($steps as $num => $s)
                        <div class="wizard-step-item wizard-step-indicator text-center {{ $num == 1 ? 'active' : '' }}" id="step-indicator-{{ $num }}" data-step="{{ $num }}">
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

            <div id="wizardAlert" class="d-none mb-3"></div>

            @php
                $now = \Carbon\Carbon::now('Asia/Jakarta');
                $submitValue = $now->format('d-m-Y H:i:s');
                $displayValue = $now->format('d-m-Y H:i:s');
            @endphp

            <!-- STEP 1: PENGEMASAN PRODUK -->
            <div class="wizard-step" id="step-1">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-id-card mr-1"></i> Identitas Produk (Langkah 1)</h3>
                    </div>
                    <div class="card-body">
                        <form id="form-step-1-identitas">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Tanggal <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Jam <span class="text-danger">*</span></label>
                                        <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Shift <span class="text-danger">*</span></label>
                                        <select name="id_shift" class="form-control select2" required>
                                            <option value="">Pilih Shift</option>
                                            @foreach($shifts as $shift)
                                                <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Produk <span class="text-danger">*</span></label>
                                        <select name="id_produk" id="id_produk_step_1" class="form-control select2-produk" required>
                                            <option value="">Pilih Produk</option>
                                            @foreach($produks as $produk)
                                                <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Berat Produk (gr) <span class="text-danger">*</span></label>
                                        <select id="nilai_select_berat" class="form-control" name="berat_produk" required>
                                            <option value="">-- Pilih produk terlebih dahulu --</option>
                                        </select>
                                        <small class="text-muted">Berat diambil dari data master bag sesuai produk yang dipilih</small>
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Expired</label>
                                        <input type="date" name="tanggal_expired" id="tanggal_expired" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Kode Produksi <span class="text-danger">*</span></label>
                                        <input type="text" name="kode_produksi" id="kode_produksi" class="form-control" required>
                                    </div>

                                    <hr>
                                    <div class="form-group mb-3">
                                        <div class="custom-control custom-switch custom-switch-off-secondary custom-switch-on-warning">
                                            <input type="checkbox" class="custom-control-input" id="is_kombinasi" name="is_kombinasi" value="1">
                                            <label class="custom-control-label font-weight-bold" for="is_kombinasi">
                                                <i class="fas fa-boxes text-warning mr-1"></i> Apakah 1 Box berisi 2 Produk? (Box Kombinasi / Dual-Product)
                                            </label>
                                        </div>
                                    </div>

                                    <div id="container-produk-2" style="display: none;" class="p-3 border border-warning rounded bg-light mb-3">
                                        <h5 class="text-warning font-weight-bold mb-3"><i class="fas fa-box-open mr-1"></i> Data Produk Ke-2 (Dalam 1 Box)</h5>
                                        <div class="form-group">
                                            <label>Nama Produk Ke-2 <span class="text-danger">*</span></label>
                                            <select name="id_produk_2" id="id_produk_step_1_2" class="form-control select2">
                                                <option value="">Pilih Produk Ke-2</option>
                                                @foreach($produks as $produk)
                                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Berat Produk Ke-2 (gr) <span class="text-danger">*</span></label>
                                            <select id="nilai_select_berat_2" class="form-control" name="berat_produk_2">
                                                <option value="">-- Pilih produk ke-2 terlebih dahulu --</option>
                                            </select>
                                            <small class="text-muted">Berat diambil dari data master bag produk ke-2</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Tanggal Expired Produk Ke-2</label>
                                            <input type="date" name="tanggal_expired_2" id="tanggal_expired_2" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Kode Produksi Ke-2 <span class="text-danger">*</span></label>
                                            <input type="text" name="kode_produksi_2" id="kode_produksi_2" class="form-control" placeholder="Masukkan kode produksi ke-2">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-right">
                        <button type="button" class="btn btn-primary next-step" data-step="1">Simpan & Lanjut <i class="fas fa-arrow-right ml-1"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 2: LANGKAH PEMBEKUAN PRODUK -->
            <div class="wizard-step" id="step-2" style="display:none;">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-snowflake mr-1"></i> Langkah Pembekuan Produk (Langkah 2)</h3>
                    </div>
                    <div class="card-body">
                        <form id="form-step-2-pembekuan">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Standar Suhu Produk IQF</label>
                                        <input type="text" name="std_suhu_produk_iqf" class="form-control" value="-18°C" readonly>
                                    </div>
                                    <div class="form-group" id="suhu-produk-container">
                                        <label>Aktual Suhu Produk (°C) <span class="text-danger">*</span></label>
                                        <div class="input-group mb-2 suhu-produk-group">
                                            <input type="text" name="aktual_suhu_produk[]" class="form-control" placeholder="Misal: -18.5" required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-success add-suhu"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Waktu Pengecekan After IQF</label>
                                        <input type="text" name="waktu_awal_packing" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary prev-step" data-step="1"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
                        <button type="button" class="btn btn-primary" id="btn-next-step2">Simpan &amp; Lanjut <i class="fas fa-arrow-right ml-1"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 3: PENGEMASAN PLASTIK -->
            <div class="wizard-step" id="step-3" style="display:none;">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-shopping-bag mr-1"></i> Pengemasan Plastik (Langkah 3)</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Produk:</strong> <span id="p2_info_produk">-</span>
                        </div>
                        <form id="form-step-2" enctype="multipart/form-data">
                            <input type="hidden" name="id_pengemasan_produk" id="p2_id_pengemasan_produk">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Jam <span class="text-danger">*</span></label>
                                        <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                                    </div>

                                    <div class="form-group" id="line-plastik-container">
                                        <label class="font-weight-bold">Line <span class="text-danger">*</span></label>
                                        <div class="input-group mb-2 line-plastik-group">
                                            <select name="line[]" class="form-control" required>
                                                <option value="">Pilih Line</option>
                                                @for ($i = 1; $i <= 8; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-success add-line-plastik"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Waktu Pengemasan</label>
                                        <input type="text" name="waktu_pengemasan" class="form-control" placeholder="Contoh: 08:00 - 10:00">
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Proses Penimbangan <span class="text-danger">*</span></label>
                                        <select name="proses_penimbangan" class="form-control" required>
                                            <option value="" disabled selected hidden>Pilih Proses Penimbangan</option>
                                            <option value="mhw">MHW</option>
                                            <option value="manual">Manual</option>
                                            <option value="korin">Korin</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Proses Sealing <span class="text-danger">*</span></label>
                                        <select name="proses_sealing" class="form-control" required>
                                            <option value="" disabled selected hidden>Pilih Proses Sealing</option>
                                            <option value="bag-sealer">Bag Sealer</option>
                                            <option value="manual">Manual</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Identitas Produk pada Plastik (tinta) <span class="text-danger">*</span></label>
                                        <select name="identitas_produk" class="form-control" required>
                                            <option value="" disabled selected hidden>Pilih Identitas Produk pada Plastik (tinta)</option>
                                            <option value="✔">✔ OK</option>
                                            <option value="✘">✘ Tidak Sesuai Spesifikasi</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Nomor MD</label>
                                        <input type="file" name="nomor_md" id="nomor_md" class="form-control-file" accept="image/*" capture="camera">
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Kode Kemasan Plastik <span class="text-danger">*</span></label>
                                        <input type="text" name="kode_kemasan_plastik" id="kode_kemasan_plastik" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Kesesuaian Label Plastik <span class="text-danger">*</span></label>
                                        <select name="kesesuaian_label_plastik" class="form-control" required>
                                            <option value="" disabled selected hidden>Pilih Kesesuaian Label</option>
                                            <option value="OK">OK</option>
                                            <option value="Not OK">Not OK</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Kekuatan Seal <span class="text-danger">*</span></label>
                                        <select name="kekuatan_seal" class="form-control" required>
                                            <option value="" disabled selected hidden>Kekuatan Seal</option>
                                            <option value="✔">✔ OK</option>
                                            <option value="✘">✘ Tidak Sesuai Spesifikasi</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary prev-step" data-step="2"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
                        <button type="button" class="btn btn-primary next-step" data-step="3">Simpan & Lanjut <i class="fas fa-arrow-right ml-1"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 4: BERAT PRODUK BAG -->
            <div class="wizard-step" id="step-4" style="display:none;">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-weight-hanging mr-1"></i> Berat Produk Bag (Langkah 4)</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Produk:</strong> <span id="p3_info_produk">-</span>
                        </div>
                        <button type="button" class="btn btn-success mb-3" id="addFormStep3">
                            <i class="fas fa-plus"></i> Tambah Form
                        </button>
                        <form id="form-step-4">
                            <input type="hidden" name="id_pengemasan_produk" id="p3_id_pengemasan_produk">
                            <input type="hidden" name="id_pengemasan_plastik" id="p3_id_pengemasan_plastik">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jam <span class="text-danger">*</span></label>
                                        <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div id="dynamicFormsStep3Container">
                                <div class="card mb-3 dynamic-form-item-step3 border border-warning" data-index="0">
                                    <div class="card-header bg-warning">
                                        <h5 class="mb-0 text-white">Data Berat Produk Pack #1</h5>
                                    </div>
                                    <div class="card-body row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Line <span class="text-danger">*</span></label>
                                                <select name="line[]" id="line_step3_0" class="form-control" required>
                                                    <option value="">Pilih Line</option>
                                                    @for ($i = 1; $i <= 8; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Nilai Standar Pack <span class="text-danger">*</span></label>
                                                <select name="id_data_bag[]" id="id_data_bag_step_3" class="form-control" required>
                                                    <option value="">Pilih Nilai Standar Pack</option>
                                                    @foreach($dataBags as $dataBag)
                                                        <option value="{{ $dataBag->id }}" class="opt-bag-{{ $dataBag->id_produk }}" data-berat="{{ $dataBag->berat }}">{{ $dataBag->std_bag }}</option>
                                                    @endforeach
                                                </select>
                                                @if(count($dataBags) == 0)
                                                    <small class="text-danger msg-empty-bag">data pack tidak ada, silahkan isi dari data master bag/pack</small>
                                                @else
                                                    <small class="text-danger msg-empty-bag" style="display:none;">data pack tidak ada, silahkan isi dari data master bag/pack</small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" id="container-berat-aktual-0">
                                                <label>Berat Aktual <span class="text-danger">*</span></label>
                                                <div class="input-group mb-2 group-berat-aktual-0">
                                                    <input type="number" step="0.01" name="berat_aktual[0][]" class="form-control p3_berat_0" required>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-success add-berat-aktual" data-index="0"><i class="fas fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Rata-rata Berat Aktual</label>
                                                <input type="number" step="0.01" name="rata_rata_berat[]" id="p3_rata_rata_0" class="form-control p3_rata_rata" readonly required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary prev-step" data-step="3"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
                        <button type="button" class="btn btn-primary next-step" data-step="4">Simpan & Lanjut <i class="fas fa-arrow-right ml-1"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 5: BERAT PRODUK BOX -->
            <div class="wizard-step" id="step-5" style="display:none;">
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-cube mr-1"></i> Berat Produk Box (Langkah 5)</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Produk:</strong> <span id="p4_info_produk">-</span>
                        </div>
                        <form id="form-step-5">
                            <input type="hidden" name="id_pengemasan_produk" id="p4_id_pengemasan_produk">
                            <input type="hidden" name="id_pengemasan_plastik" id="p4_id_pengemasan_plastik">
                            <input type="hidden" name="id_berat_produk_bag" id="p4_id_berat_produk_bag">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Jam <span class="text-danger">*</span></label>
                                        <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Data Box <span class="text-danger">*</span></label>
                                        <select name="id_data_box" id="id_data_box_step_4" class="form-control" required>
                                            <option value="">Pilih Data Box</option>
                                            @foreach($dataBoxes as $box)
                                                <option value="{{ $box->id }}" class="opt-box-{{ $box->id_produk }}" data-berat="{{ $box->berat }}" data-std-isi="{{ $box->std_isi_pack_karton }}">{{ $box->std_box }}</option>
                                            @endforeach
                                        </select>
                                        @if(count($dataBoxes) == 0)
                                            <small class="text-danger msg-empty-box">data box tidak ada, silahkan isi dari data master box</small>
                                        @else
                                            <small class="text-danger msg-empty-box" style="display:none;">data box tidak ada, silahkan isi dari data master box</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="berat-aktual-box-container">
                                        <label>Berat Aktual <span class="text-danger">*</span></label>
                                        <div class="input-group mb-2 berat-aktual-box-group">
                                            <input type="number" step="0.01" name="berat_aktual[]" id="p4_berat_0" class="form-control p4_berat" placeholder="Misal: 500.50" required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-success add-berat-box"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Rata-rata Berat Aktual</label>
                                        <input type="number" step="0.01" name="rata_rata_berat" id="p4_rata_rata" class="form-control" readonly required>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary prev-step" data-step="4"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
                        <button type="button" class="btn btn-primary next-step" data-step="5">Simpan & Lanjut <i class="fas fa-arrow-right ml-1"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 6: PENGEMASAN KARTON -->
            <div class="wizard-step" id="step-6" style="display:none;">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-boxes mr-1"></i> Pengemasan Karton (Langkah 6)</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Produk:</strong> <span id="p5_info_produk">-</span>
                        </div>
                        <form id="form-step-6">
                            <input type="hidden" name="id_pengemasan_produk" id="p5_id_pengemasan_produk">
                            <input type="hidden" name="id_pengemasan_plastik" id="p5_id_pengemasan_plastik">
                            <input type="hidden" name="id_berat_produk_bag" id="p5_id_berat_produk_bag">
                            <input type="hidden" name="id_berat_produk_box" id="p5_id_berat_produk_box">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Jam <span class="text-danger">*</span></label>
                                        <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kesesuaian Label Karton<span class="text-danger">*</span></label>
                                        <select name="identitas_produk_pada_karton" class="form-control" required>
                                            <option value="✔">✔</option>
                                            <option value="✘">✘</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Standar isi pack dalam karton <span class="text-danger">*</span></label>
                                        <input type="number" name="standar_jumlah_karton" id="p5_standar_jumlah_karton" class="form-control" readonly required>
                                        <small class="text-danger" id="msg-empty-std-isi" style="display:none;">silahkan isi standar isi pack per karton di data master standar box</small>
                                    </div>
                                    <div class="form-group">
                                        <label>Aktual isi pack dalam karton <span class="text-danger">*</span></label>
                                        <input type="text" name="aktual_jumlah_karton" class="form-control" placeholder="Pisahkan dengan koma jika lebih dari satu" required>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="font-weight-bold">Kesesuaian Standar Isi Pack dengan Aktual <span class="text-danger">*</span></label>
                                <div id="kesesuaian-isi-pack-container">
                                    <div class="input-group mb-2 kesesuaian-isi-pack-group">
                                        <select name="kesesuaian_isi_pack[]" class="form-control" required>
                                            <option value="" disabled selected hidden>Pilih Kesesuaian</option>
                                            <option value="✔">✔ Sesuai</option>
                                            <option value="✘">✘ Tidak Sesuai</option>
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-success add-kesesuaian-isi"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary prev-step" data-step="5"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
                        <button type="button" class="btn btn-primary next-step" data-step="6">Simpan & Lanjut <i class="fas fa-arrow-right ml-1"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 7: DOKUMENTASI -->
            <div class="wizard-step" id="step-7" style="display:none;">
                <div class="card card-dark card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-camera mr-1"></i> Dokumentasi (Langkah 7)</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Produk:</strong> <span id="p6_info_produk">-</span>
                        </div>
                        <form id="form-step-7" enctype="multipart/form-data">
                            <input type="hidden" name="id_pengemasan_produk" id="p6_id_pengemasan_produk">
                            <input type="hidden" name="id_pengemasan_plastik" id="p6_id_pengemasan_plastik">
                            <input type="hidden" name="id_berat_produk_bag" id="p6_id_berat_produk_bag">
                            <input type="hidden" name="id_berat_produk_box" id="p6_id_berat_produk_box">
                            <input type="hidden" name="id_pengemasan_karton" id="p6_id_pengemasan_karton">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Jam <span class="text-danger">*</span></label>
                                        <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Foto Kode Produksi Primer dan Best Before <span class="text-danger">*</span></label>
                                        <input type="file" name="foto_kode_produksi" class="form-control-file" accept="image/*" capture="camera" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Foto Kode Produksi Sekunder dan Best Before</label>

                                        {{-- Toggle pilihan mode --}}
                                        <div class="mb-2">
                                            <div class="d-flex flex-wrap" style="gap:8px;" id="toggle-sekunder">
                                                <button type="button" class="btn btn-outline-primary btn-sm active" id="btn-mode-foto-sekunder"
                                                    onclick="setModeSekunder('foto')">
                                                    <i class="fas fa-camera mr-1"></i> Upload Foto
                                                </button>
                                                <button type="button" class="btn btn-outline-primary btn-sm" id="btn-mode-status-sekunder"
                                                    onclick="setModeSekunder('status')">
                                                    <i class="fas fa-check-circle mr-1"></i> OK / Not OK
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Mode upload foto --}}
                                        <div id="mode-foto-sekunder">
                                            <input type="file" name="foto_kode_produksi_sekunder" id="foto_kode_produksi_sekunder"
                                                   class="form-control-file" accept="image/*" capture="camera">
                                        </div>

                                        {{-- Mode OK / Not OK --}}
                                        <div id="mode-status-sekunder" style="display:none;">
                                            <input type="hidden" name="foto_kode_produksi_sekunder_status" id="foto_kode_produksi_sekunder_status" value="">
                                            <div class="d-flex flex-wrap" style="gap:8px;">
                                                <button type="button" class="btn btn-outline-success btn-sm" id="btn-ok-sekunder"
                                                    onclick="setStatusSekunder('OK')">
                                                    <i class="fas fa-check mr-1"></i> OK
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm" id="btn-notok-sekunder"
                                                    onclick="setStatusSekunder('Not OK')">
                                                    <i class="fas fa-times mr-1"></i> Not OK
                                                </button>
                                            </div>
                                            <small class="d-block mt-1 text-muted" id="label-status-sekunder">Belum dipilih</small>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>QR Code</label>
                                        <input type="file" name="qr_code" class="form-control-file" accept="image/*" capture="camera">
                                    </div>
                                    <div class="form-group">
                                        <label>Foto Label Polyroll yang digunakan</label>
                                        <input type="file" name="label_polyroll" class="form-control-file" accept="image/*" capture="camera">
                                    </div>
                                    <div class="form-group">
                                        <label>Label Supplier</label>
                                        <input type="file" name="label_supplier" class="form-control-file" accept="image/*" capture="camera">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Keterangan</label>
                                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status Release Produk <span class="text-danger">*</span></label>
                                        <select name="status_release_produk" class="form-control" required>
                                            <option value="">Pilih Status</option>
                                            <option value="ya">Ya</option>
                                            <option value="tidak">Tidak</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tindakan Koreksi</label>
                                        <textarea name="tindakan_koreksi" class="form-control" rows="2" placeholder="Masukkan tindakan koreksi jika diperlukan..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary prev-step" data-step="6"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
                        <button type="button" class="btn btn-success" id="btn-save-final"><i class="fas fa-check-circle mr-1"></i> Simpan & Selesai</button>
                    </div>
                </div>
            </div>

            {{-- ===== STEP 7: SELESAI ===== --}}
            <div class="wizard-panel d-none text-center" id="panel-7">
                <div class="card card-outline card-success">
                    <div class="card-body py-5">
                        <i class="fas fa-check-circle text-success" style="font-size:5rem;"></i>
                        <h2 class="mt-3 text-success">Proses Packing Selesai!</h2>
                        <p class="text-muted">Semua data berhasil disimpan ke dalam sistem.</p>
                        <a href="{{ route('wizards.packing.index') }}" class="btn btn-success btn-lg mt-3">
                            <i class="fas fa-list mr-1"></i> Lihat Data Packing
                        </a>
                        <a href="{{ route('wizards.packing.create') }}" class="btn btn-primary btn-lg mt-3 ml-2">
                            <i class="fas fa-plus mr-1"></i> Input Packing Baru
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<div id="loadingOverlay" class="d-none" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.4);z-index:9999;align-items:center;justify-content:center;">
    <div class="text-center text-white">
        <div class="spinner-border mb-2" style="width:3rem;height:3rem;"></div>
        <div id="loadingText" style="font-size:1.1rem;">Menyimpan data...</div>
    </div>
</div>

<style>
#loadingOverlay { display: none !important; }
#loadingOverlay.show { display: flex !important; }
</style>

@push('scripts')
<script>
(function() {
    'use strict';
    
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ===== PEMBAGIAN ROLE (E-QF 15) =====
    // produksi : hanya mengisi bagian Produksi (Langkah 1, 2, 3, 6). Verifikasi QC (suhu, label/seal,
    //            berat bag & box, kesesuaian karton, dokumentasi, release) diisi QC lewat menu Edit.
    // all      : Admin/Superadmin mengisi seluruh langkah.
    const ROLE_MODE = @json($roleMode ?? 'all');
    const IS_PRODUKSI = ROLE_MODE === 'produksi';

    (function applyRoleLock() {
        if (!IS_PRODUKSI) return;
        ['aktual_suhu_produk[]', 'identitas_produk', 'kesesuaian_label_plastik', 'kekuatan_seal',
         'identitas_produk_pada_karton', 'standar_jumlah_karton', 'kesesuaian_isi_pack[]'].forEach(function(name) {
            document.querySelectorAll('[name="' + name + '"]').forEach(function(el) {
                el.required = false;
                el.disabled = true;
                const group = el.closest('.form-group');
                if (group) group.style.display = 'none';
            });
        });
        [4, 5, 7].forEach(function(i) {
            const ind = document.getElementById('step-indicator-' + i);
            if (ind) ind.style.display = 'none';
        });
        const alertEl = document.getElementById('wizardAlert');
        if (alertEl) {
            alertEl.insertAdjacentHTML('beforebegin',
                '<div class="alert alert-info"><i class="fas fa-user-lock mr-1"></i> ' +
                'Anda login sebagai <strong>Produksi</strong>. Anda hanya mengisi bagian Produksi; ' +
                'bagian verifikasi <strong>QC</strong> (suhu produk, label/seal plastik, berat pack &amp; box, kesesuaian karton, dokumentasi, status release) akan dilengkapi oleh QC.</div>');
        }
        const btnKarton = document.querySelector('#step-6 .next-step');
        if (btnKarton) btnKarton.innerHTML = 'Simpan &amp; Selesai <i class="fas fa-check ml-1"></i>';
    })();
    
    const state = {
        currentStep: 1,
        id_pengemasan_produk: null,
        id_pengemasan_plastik: null,
        id_berat_produk_bag: null,
        id_berat_produk_box: null,
        id_pengemasan_karton: null,
        id_produk: null,
        id_produk_text: null,
        berat_produk: null,
    };

    const STORAGE_KEY = 'wizard_packing_state_v1';
    const FORM_DATA_KEY = 'wizard_packing_form_data_v1';
    try {
        const saved = sessionStorage.getItem(STORAGE_KEY);
        if (saved) {
            const parsed = JSON.parse(saved);
            if (parsed && typeof parsed === 'object') {
                Object.assign(state, parsed);
            }
        }
    } catch (_) {}

    function persistState() {
        try {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        } catch (_) {}
    }

    function persistFormData() {
        try {
            const formData = {};
            document.querySelectorAll('input:not([type="file"]):not([type="hidden"]), select, textarea').forEach(el => {
                const key = el.name || el.id;
                if (key) {
                    if (el.type === 'checkbox' || el.type === 'radio') {
                        formData[key] = el.checked;
                    } else {
                        formData[key] = el.value;
                    }
                }
            });
            sessionStorage.setItem(FORM_DATA_KEY, JSON.stringify(formData));
        } catch (e) {
            console.error('Error persisting form data:', e);
        }
    }

    function restoreFormData() {
        try {
            const savedForms = sessionStorage.getItem(FORM_DATA_KEY);
            if (savedForms) {
                const formData = JSON.parse(savedForms);
                document.querySelectorAll('input:not([type="file"]):not([type="hidden"]), select, textarea').forEach(el => {
                    const key = el.name || el.id;
                    if (key && formData[key] !== undefined) {
                        if (el.type === 'checkbox' || el.type === 'radio') {
                            el.checked = formData[key];
                        } else {
                            el.value = formData[key];
                        }
                    }
                });
                
                // Trigger change on Data Box to pull fresh data-std-isi from HTML
                const dataBox = document.getElementById('id_data_box_step_4');
                if (dataBox && dataBox.value) {
                    dataBox.dispatchEvent(new Event('change'));
                }
            }
        } catch (e) {
            console.error('Error restoring form data:', e);
        }
    }

    // Trigger save on change/input
    document.addEventListener('input', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
            persistFormData();
        }
    });
    document.addEventListener('change', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
            persistFormData();
        }
    });

    function showLoading(text) {
        document.getElementById('loadingText').textContent = text || 'Menyimpan data...';
        document.getElementById('loadingOverlay').classList.add('show');
    }
    function hideLoading() {
        document.getElementById('loadingOverlay').classList.remove('show');
    }
    function showAlert(type, msg) {
        const el = document.getElementById('wizardAlert');
        if (!el) {
            console.error('wizardAlert element not found');
            alert(msg);
            return;
        }
        el.className = `alert alert-${type} alert-dismissible fade show`;
        el.innerHTML = `${msg}<button type="button" class="close" data-dismiss="alert">&times;</button>`;
        el.classList.remove('d-none');
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => { el.classList.add('d-none'); }, 5000);
    }
    function goToStep(step) {
        try {
            document.querySelectorAll('.wizard-step').forEach(p => p.style.display = 'none');
            const panel = document.getElementById(`step-${step}`);
            if(panel) { panel.style.display = 'block'; panel.scrollIntoView({ behavior: 'smooth' }); }
            
            for (let i = 1; i <= 7; i++) {
                const el = document.getElementById(`step-indicator-${i}`);
                if(!el) continue;
                el.classList.remove('active', 'completed');
                if (i < step)  el.classList.add('completed');
                else if (i === step) el.classList.add('active');
            }
            
            state.currentStep = step;
            if(step <= 6) persistState();
        } catch (error) {
            console.error("Error in goToStep:", error);
            showAlert('danger', 'Terjadi kesalahan pada sistem saat berpindah langkah. Mohon refresh halaman.');
        }
    }

    async function compressImage(file, maxWidth = 1200) {
        if (!file || !file.type.match(/image.*/)) return file;
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = event => {
                const img = new window.Image();
                img.src = event.target.result;
                img.onload = () => {
                    let width = img.width;
                    let height = img.height;
                    if (width > maxWidth) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    }
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    canvas.toBlob(blob => {
                        resolve(new File([blob], file.name, { type: 'image/jpeg', lastModified: Date.now() }));
                    }, 'image/jpeg', 0.8);
                };
                img.onerror = () => resolve(file);
            };
            reader.onerror = () => resolve(file);
        });
    }

    function ajaxPost(url, formData) {
        return fetch(url, {
            method: 'POST',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest', 
                'X-CSRF-TOKEN': CSRF, 
                'Accept': 'application/json' 
            },
            body: formData
        }).then(r => r.json());
    }

    function attachCalculationStep3() {
        document.querySelectorAll('.dynamic-form-item-step3').forEach((form, formIndex) => {
            const inputs = form.querySelectorAll('input[name^="berat_aktual"]');
            const avg = form.querySelector('.p3_rata_rata');
            
            inputs.forEach(el => {
                if(el && !el.dataset.bound) {
                    el.addEventListener('input', () => {
                        let sum = 0, count = 0;
                        form.querySelectorAll('input[name^="berat_aktual"]').forEach(inp => {
                            let val = parseFloat(inp.value);
                            if (!isNaN(val)) {
                                sum += val;
                                count++;
                            }
                        });
                        if (count > 0) avg.value = (sum / count).toFixed(2);
                        else avg.value = '';
                    });
                    el.dataset.bound = '1';
                }
            });
        });
    }
    
    attachCalculationStep3();

    let formIndexStep3 = 1;
    document.getElementById('addFormStep3').addEventListener('click', function() {
        const container = document.getElementById('dynamicFormsStep3Container');
        const firstForm = container.querySelector('.dynamic-form-item-step3');
        const newForm = firstForm.cloneNode(true);
        
        newForm.setAttribute('data-index', formIndexStep3);
        const header = newForm.querySelector('.card-header');
        header.innerHTML = `
            <h5 class="mb-0 text-white d-inline-block">Data Berat Produk Pack #${formIndexStep3 + 1}</h5>
            <button type="button" class="btn btn-danger btn-sm float-right remove-form-btn-step3">
                <i class="fas fa-trash"></i> Hapus
            </button>
        `;
        
        // Reset berat aktual dynamic rows to just one
        const beratContainer = newForm.querySelector('div[id^="container-berat-aktual-"]');
        beratContainer.id = 'container-berat-aktual-' + formIndexStep3;
        const beratGroups = beratContainer.querySelectorAll('div[class*="group-berat-aktual-"]');
        for (let i = 1; i < beratGroups.length; i++) {
            beratGroups[i].remove();
        }
        
        const firstBeratGroup = beratContainer.querySelector('div[class*="group-berat-aktual-"]');
        firstBeratGroup.className = 'input-group mb-2 group-berat-aktual-' + formIndexStep3;
        firstBeratGroup.innerHTML = `
            <input type="number" step="0.01" name="berat_aktual[${formIndexStep3}][]" class="form-control" required>
            <div class="input-group-append">
                <button type="button" class="btn btn-success add-berat-aktual" data-index="${formIndexStep3}"><i class="fas fa-plus"></i></button>
            </div>
        `;

        // Reset all inputs
        newForm.querySelectorAll('input[type="number"]').forEach(input => input.value = '');
        newForm.querySelectorAll('select').forEach(select => select.value = '');
        
        // Update rata_rata ID
        const rataRata = newForm.querySelector('input[name="rata_rata_berat[]"]');
        rataRata.id = 'p3_rata_rata_' + formIndexStep3;
        rataRata.removeAttribute('data-bound');
        
        container.appendChild(newForm);
        attachCalculationStep3();
        formIndexStep3++;
    });

    document.addEventListener('click', function(e) {
        if (e.target && (e.target.classList.contains('add-berat-aktual') || e.target.closest('.add-berat-aktual'))) {
            const btn = e.target.closest('.add-berat-aktual');
            const index = btn.getAttribute('data-index');
            const container = document.getElementById('container-berat-aktual-' + index);
            const newDiv = document.createElement('div');
            newDiv.className = 'input-group mb-2 group-berat-aktual-' + index;
            newDiv.innerHTML = `
                <input type="number" step="0.01" name="berat_aktual[${index}][]" class="form-control" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-berat-aktual"><i class="fas fa-minus"></i></button>
                </div>
            `;
            // Insert before rata-rata which is in the next sibling? No, the container only contains berat_aktual fields.
            container.appendChild(newDiv);
            attachCalculationStep3();
        }
        if (e.target && (e.target.classList.contains('remove-berat-aktual') || e.target.closest('.remove-berat-aktual'))) {
            const group = e.target.closest('.input-group');
            group.remove();
            attachCalculationStep3();
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('.remove-form-btn-step3')) {
            const forms = document.querySelectorAll('.dynamic-form-item-step3');
            if (forms.length > 1) {
                e.target.closest('.dynamic-form-item-step3').remove();
                document.querySelectorAll('.dynamic-form-item-step3').forEach((form, index) => {
                    const header = form.querySelector('.card-header h5');
                    if (header) header.textContent = `Data Berat Produk Pack #${index + 1}`;
                });
            } else {
                alert('Minimal harus ada 1 form!');
            }
        }
    });

    function filterDataBagByProduk(idProduk) {
        const selectedBerat = document.getElementById('nilai_select_berat').value;
        document.querySelectorAll('.dynamic-form-item-step3 select[name="id_data_bag[]"]').forEach(select => {
            const currentValue = select.value;
            let visibleCount = 0;
            let isCurrentValueVisible = false;
            
            Array.from(select.options).forEach(opt => {
                if (opt.value === '') return;
                const isProdukMatch = opt.classList.contains('opt-bag-' + idProduk);
                const optBerat = parseFloat(opt.getAttribute('data-berat'));
                const selBerat = parseFloat(selectedBerat);
                const isBeratMatch = (!isNaN(optBerat) && !isNaN(selBerat) && optBerat === selBerat) || opt.getAttribute('data-berat') == selectedBerat;
                
                if (isProdukMatch && isBeratMatch) {
                    opt.style.display = '';
                    visibleCount++;
                    if (opt.value === currentValue) isCurrentValueVisible = true;
                } else {
                    opt.style.display = 'none';
                }
            });
            
            if (!isCurrentValueVisible) {
                select.value = '';
            }

            const formGroup = select.closest('.form-group');
            if (formGroup) {
                const msg = formGroup.querySelector('.msg-empty-bag');
                if (msg) {
                    msg.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            }
        });
    }

    // Dynamic rata-rata for step 5 (Berat Produk Box)
    function recalcBeratBox() {
        const inputs = document.querySelectorAll('#berat-aktual-box-container input.p4_berat');
        let sum = 0, count = 0;
        inputs.forEach(inp => {
            const v = parseFloat(inp.value);
            if (!isNaN(v)) { sum += v; count++; }
        });
        const out = document.getElementById('p4_rata_rata');
        if (out) out.value = count > 0 ? (sum / count).toFixed(2) : '';
    }

    // Attach recalc on existing input
    document.getElementById('p4_berat_0')?.addEventListener('input', recalcBeratBox);

    // Dynamic Suhu Produk
    document.addEventListener('click', function(e) {
        if (e.target && (e.target.classList.contains('add-suhu') || e.target.closest('.add-suhu'))) {
            const container = document.getElementById('suhu-produk-container');
            const newDiv = document.createElement('div');
            newDiv.className = 'input-group mb-2 suhu-produk-group';
            newDiv.innerHTML = `
                <input type="text" name="aktual_suhu_produk[]" class="form-control" placeholder="Misal: -18.5" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-suhu"><i class="fas fa-minus"></i></button>
                </div>
            `;
            container.appendChild(newDiv);
        }
        if (e.target && (e.target.classList.contains('remove-suhu') || e.target.closest('.remove-suhu'))) {
            e.target.closest('.suhu-produk-group').remove();
        }

        // Dynamic Kesesuaian Isi Pack
        if (e.target && (e.target.classList.contains('add-kesesuaian-isi') || e.target.closest('.add-kesesuaian-isi'))) {
            const container = document.getElementById('kesesuaian-isi-pack-container');
            const newDiv = document.createElement('div');
            newDiv.className = 'input-group mb-2 kesesuaian-isi-pack-group';
            newDiv.innerHTML = `
                <select name="kesesuaian_isi_pack[]" class="form-control" required>
                    <option value="" disabled selected hidden>Pilih Kesesuaian</option>
                    <option value="✔">✔ Sesuai</option>
                    <option value="✘">✘ Tidak Sesuai</option>
                </select>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-kesesuaian-isi"><i class="fas fa-minus"></i></button>
                </div>
            `;
            container.appendChild(newDiv);
        }
        if (e.target && (e.target.classList.contains('remove-kesesuaian-isi') || e.target.closest('.remove-kesesuaian-isi'))) {
            e.target.closest('.kesesuaian-isi-pack-group').remove();
        }

        // Dynamic Line Plastik
        if (e.target && (e.target.classList.contains('add-line-plastik') || e.target.closest('.add-line-plastik'))) {
            const container = document.getElementById('line-plastik-container');
            const newDiv = document.createElement('div');
            newDiv.className = 'input-group mb-2 line-plastik-group';
            newDiv.innerHTML = `
                <select name="line[]" class="form-control" required>
                    <option value="">Pilih Line</option>
                    ${[1,2,3,4,5,6,7,8].map(i => `<option value="${i}">${i}</option>`).join('')}
                </select>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-line-plastik"><i class="fas fa-minus"></i></button>
                </div>
            `;
            container.appendChild(newDiv);
        }
        if (e.target && (e.target.classList.contains('remove-line-plastik') || e.target.closest('.remove-line-plastik'))) {
            e.target.closest('.line-plastik-group').remove();
        }

        // Dynamic Berat Aktual Box (step 5)
        if (e.target && (e.target.classList.contains('add-berat-box') || e.target.closest('.add-berat-box'))) {
            const container = document.getElementById('berat-aktual-box-container');
            const newDiv = document.createElement('div');
            newDiv.className = 'input-group mb-2 berat-aktual-box-group';
            newDiv.innerHTML = `
                <input type="number" step="0.01" name="berat_aktual[]" class="form-control p4_berat" placeholder="Misal: 500.50" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-berat-box"><i class="fas fa-minus"></i></button>
                </div>
            `;
            container.appendChild(newDiv);
            newDiv.querySelector('input').addEventListener('input', recalcBeratBox);
        }
        if (e.target && (e.target.classList.contains('remove-berat-box') || e.target.closest('.remove-berat-box'))) {
            e.target.closest('.berat-aktual-box-group').remove();
            recalcBeratBox();
        }
    });

    function filterDataBoxByProduk(idProduk) {
        const select = document.getElementById('id_data_box_step_4');
        const selectedBerat = document.getElementById('nilai_select_berat').value;
        if (!select) return;
        
        const currentValue = select.value;
        let visibleCount = 0;
        let isCurrentValueVisible = false;
        
        Array.from(select.options).forEach(opt => {
            if (opt.value === '') return;
            const isProdukMatch = opt.classList.contains('opt-box-' + idProduk);
            const optBerat = parseFloat(opt.getAttribute('data-berat'));
            const selBerat = parseFloat(selectedBerat);
            const isBeratMatch = (!isNaN(optBerat) && !isNaN(selBerat) && optBerat === selBerat) || opt.getAttribute('data-berat') == selectedBerat;
            
            if (isProdukMatch && isBeratMatch) {
                opt.style.display = '';
                visibleCount++;
                if (opt.value === currentValue) isCurrentValueVisible = true;
            } else {
                opt.style.display = 'none';
            }
        });
        
        if (!isCurrentValueVisible) {
            select.value = '';
        }
        
        const formGroup = select.closest('.form-group');
        if (formGroup) {
            const msg = formGroup.querySelector('.msg-empty-box');
            if (msg) {
                msg.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }
    }

    // Auto-populate Standar Isi Pack Karton in Step 6 from Data Box selection
    const dataBoxSelect = document.getElementById('id_data_box_step_4');
    if (dataBoxSelect) {
        dataBoxSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const stdIsi = selectedOption.getAttribute('data-std-isi');
            const stdIsiInput = document.getElementById('p5_standar_jumlah_karton');
            const msgEmpty = document.getElementById('msg-empty-std-isi');
            if (stdIsiInput) {
                stdIsiInput.value = stdIsi ? stdIsi : '';
                if (msgEmpty) {
                    msgEmpty.style.display = (!stdIsi || stdIsi.trim() === '') && this.value !== '' ? 'block' : 'none';
                }
            }
        });
    }

    function showErrors(errors) {
        let html = '<ul class="mb-0">';
        Object.values(errors).forEach(msgs => msgs.forEach(m => { html += `<li>${m}</li>`; }));
        html += '</ul>';
        showAlert('danger', html);
    }
    
    function validateForm(formId) {
        const form = document.getElementById(formId);
        let isValid = true;
        form.querySelectorAll('[required]').forEach(el => {
            try {
                if (!el.value) {
                    el.classList.add('is-invalid');
                    if (el.classList.contains('select2-hidden-accessible')) {
                        const nextContainer = el.nextElementSibling;
                        if (nextContainer && nextContainer.classList.contains('select2-container')) {
                            const selection = nextContainer.querySelector('.select2-selection') || nextContainer.querySelector('.select2-selection--single');
                            if (selection) selection.style.borderColor = '#dc3545';
                        }
                    }
                    isValid = false;
                } else {
                    el.classList.remove('is-invalid');
                    if (el.classList.contains('select2-hidden-accessible')) {
                        const nextContainer = el.nextElementSibling;
                        if (nextContainer && nextContainer.classList.contains('select2-container')) {
                            const selection = nextContainer.querySelector('.select2-selection') || nextContainer.querySelector('.select2-selection--single');
                            if (selection) selection.style.borderColor = '';
                        }
                    }
                }
            } catch (error) {
                console.error("Validation error on element:", el, error);
                if (!el.value) isValid = false;
            }
        });
        if (!isValid) {
            showAlert('warning', 'Tolong Lengkapi Pengisian Datanya (bertanda bintang *). Periksa juga pilihan Dropdown.');
        }
        return isValid;
    }

    function copyProdukToStep(idProduk, namaProduk) {
        for(let step = 2; step <= 6; step++) {
            const badge = document.getElementById(`p${step}_info_produk`);
            if(badge) badge.textContent = namaProduk;
        }
    }

    // Step 1 - Identitas Produk (hanya validasi, lanjut ke step 2)
    document.querySelector('#step-1 .next-step').addEventListener('click', function() {
        if (!validateForm('form-step-1-identitas')) return;
        goToStep(2);
    });

    // Step 2 - Langkah Pembekuan Produk (AJAX simpan produk + pembekuan, lanjut ke step 3)
    document.getElementById('btn-next-step2').addEventListener('click', function() {
        if (!validateForm('form-step-2-pembekuan')) return;
        
        showLoading('Menyimpan Pembekuan Produk...');
        const formData = new FormData(document.getElementById('form-step-1-identitas'));
        for (let [key, val] of new FormData(document.getElementById('form-step-2-pembekuan')).entries()) {
            formData.append(key, val);
        }

        ajaxPost('{{ route("wizards.packing.store-pengemasan-produk") }}', formData).then(res => {
            hideLoading();
            if (res.success) {
                state.id_pengemasan_produk = res.id;
                document.getElementById('p2_id_pengemasan_produk').value = res.id;
                document.getElementById('p3_id_pengemasan_produk').value = res.id;
                document.getElementById('p4_id_pengemasan_produk').value = res.id;
                document.getElementById('p5_id_pengemasan_produk').value = res.id;
                document.getElementById('p6_id_pengemasan_produk').value = res.id;
                
                const selectProduk = document.getElementById('id_produk_step_1');
                const namaProduk = selectProduk.options[selectProduk.selectedIndex].text;
                state.id_produk = res.id_produk || selectProduk.value;
                state.id_produk_text = namaProduk;
                state.berat_produk = document.getElementById('nilai_select_berat').value;
                
                copyProdukToStep(state.id_produk, namaProduk);
                filterDataBagByProduk(state.id_produk);
                filterDataBoxByProduk(state.id_produk);
                
                goToStep(3);
            } else {
                if (res.errors) showErrors(res.errors);
                else showAlert('danger', res.message || 'Tolong Lengkapi Pengisian Datanya.');
            }
        }).catch(e => { hideLoading(); showAlert('danger', 'Tolong Lengkapi Pengisian Datanya.'); });
    });

    // Step 3 - Pengemasan Plastik
    document.querySelector('#step-3 .next-step').addEventListener('click', async function() {
        if (!validateForm('form-step-2')) return;
        showLoading('Menyimpan Pengemasan Plastik...');
        
        const form = document.getElementById('form-step-2');
        const formData = new FormData(form);
        
        const fileInput = document.getElementById('nomor_md');
        if (fileInput && fileInput.files.length > 0) {
            const compressedFile = await compressImage(fileInput.files[0]);
            formData.set('nomor_md', compressedFile);
        }

        ajaxPost('{{ route("wizards.packing.store-pengemasan-plastik") }}', formData).then(res => {
            hideLoading();
            if (res.success) {
                state.id_pengemasan_plastik = res.id;
                document.getElementById('p3_id_pengemasan_plastik').value = res.id;
                document.getElementById('p4_id_pengemasan_plastik').value = res.id;
                document.getElementById('p5_id_pengemasan_plastik').value = res.id;
                document.getElementById('p6_id_pengemasan_plastik').value = res.id;
                goToStep(IS_PRODUKSI ? 6 : 4);
            } else {
                if (res.errors) showErrors(res.errors);
                else showAlert('danger', res.message || 'Tolong Lengkapi Pengisian Datanya.');
            }
        }).catch(e => { hideLoading(); showAlert('danger', 'Tolong Lengkapi Pengisian Datanya.'); });
    });

    // Step 4 - Berat Produk Bag
    document.querySelector('#step-4 .next-step').addEventListener('click', function(e) {
        e.stopPropagation();
        if (document.getElementById('step-4').style.display === 'none') return;
        if (!validateForm('form-step-4')) return;
        showLoading('Menyimpan Berat Produk Bag...');
        ajaxPost('{{ route("wizards.packing.store-berat-produk-bag") }}', new FormData(document.getElementById('form-step-4'))).then(res => {
            hideLoading();
            if (res.success) {
                state.id_berat_produk_bag = res.id;
                document.getElementById('p4_id_berat_produk_bag').value = res.id;
                document.getElementById('p5_id_berat_produk_bag').value = res.id;
                document.getElementById('p6_id_berat_produk_bag').value = res.id;
                goToStep(5);
            } else {
                if (res.errors) showErrors(res.errors);
                else showAlert('danger', res.message || 'Tolong Lengkapi Pengisian Datanya.');
            }
        }).catch(e => { hideLoading(); showAlert('danger', 'Tolong Lengkapi Pengisian Datanya.'); });
    });

    // Step 5 - Berat Produk Box
    document.querySelector('#step-5 .next-step').addEventListener('click', function(e) {
        e.stopPropagation();
        if (document.getElementById('step-5').style.display === 'none') return;
        if (!validateForm('form-step-5')) return;
        showLoading('Menyimpan Berat Produk Box...');
        ajaxPost('{{ route("wizards.packing.store-berat-produk-box") }}', new FormData(document.getElementById('form-step-5'))).then(res => {
            hideLoading();
            if (res.success) {
                state.id_berat_produk_box = res.id;
                document.getElementById('p5_id_berat_produk_box').value = res.id;
                document.getElementById('p6_id_berat_produk_box').value = res.id;
                goToStep(6);
            } else {
                if (res.errors) showErrors(res.errors);
                else showAlert('danger', res.message || 'Tolong Lengkapi Pengisian Datanya.');
            }
        }).catch(e => { hideLoading(); showAlert('danger', 'Tolong Lengkapi Pengisian Datanya.'); });
    });

    // Step 6 - Pengemasan Karton
    document.querySelector('#step-6 .next-step').addEventListener('click', function() {
        if (!validateForm('form-step-6')) return;
        showLoading('Menyimpan Pengemasan Karton...');
        ajaxPost('{{ route("wizards.packing.store-pengemasan-karton") }}', new FormData(document.getElementById('form-step-6'))).then(res => {
            hideLoading();
            if (res.success) {
                state.id_pengemasan_karton = res.id;
                document.getElementById('p6_id_pengemasan_karton').value = res.id;
                if (IS_PRODUKSI) { finalizeProduksi(); } else { goToStep(7); }
            } else {
                if (res.errors) showErrors(res.errors);
                else showAlert('danger', res.message || 'Tolong Lengkapi Pengisian Datanya.');
            }
        }).catch(e => { hideLoading(); showAlert('danger', 'Tolong Lengkapi Pengisian Datanya.'); });
    });

    // Produksi: simpan bagiannya, buat record induk agar muncul di Index untuk diverifikasi QC
    function finalizeProduksi() {
        showLoading('Menyimpan Data Produksi...');
        const src = new FormData(document.getElementById('form-step-6'));
        const fd = new FormData();
        fd.append('id_pengemasan_produk', state.id_pengemasan_produk || '');
        fd.append('id_pengemasan_plastik', state.id_pengemasan_plastik || '');
        fd.append('id_pengemasan_karton', state.id_pengemasan_karton || '');
        fd.append('tanggal', src.get('tanggal') || '');
        fd.append('jam', src.get('jam') || '');

        ajaxPost('{{ route("wizards.packing.store-dokumentasi") }}', fd).then(res => {
            hideLoading();
            if (res.success) {
                document.querySelectorAll('.wizard-step').forEach(p => p.style.display = 'none');
                const panel = document.getElementById('panel-7');
                panel.classList.remove('d-none');
                const msg = panel.querySelector('p.text-muted');
                if (msg) msg.textContent = 'Data Produksi tersimpan. Menunggu verifikasi QC.';
                panel.scrollIntoView({ behavior: 'smooth' });
                for (let i = 1; i <= 7; i++) {
                    const el = document.getElementById('step-indicator-' + i);
                    if (el) { el.classList.remove('active'); el.classList.add('completed'); }
                }
                sessionStorage.removeItem(STORAGE_KEY);
                sessionStorage.removeItem(FORM_DATA_KEY);
            } else {
                showAlert('danger', res.message || 'Gagal menyimpan data.');
            }
        }).catch(() => { hideLoading(); showAlert('danger', 'Terjadi kesalahan saat menyimpan data.'); });
    }

    // Previous Step Button handler
    document.addEventListener('click', function(e) {
        if (e.target && (e.target.classList.contains('prev-step') || e.target.closest('.prev-step'))) {
            const btn = e.target.closest('.prev-step');
            let step = parseInt(btn.getAttribute('data-step'));
            if (IS_PRODUKSI && (step === 4 || step === 5)) step = 3;
            goToStep(step);
        }
    });

    // Step 7 - Final Save (attached after DOM is ready)
    document.addEventListener('DOMContentLoaded', function() {
        const btnSaveFinal = document.getElementById('btn-save-final');
        if (btnSaveFinal) {
            btnSaveFinal.addEventListener('click', async function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (!validateForm('form-step-7')) return;
                
                showLoading('Menyimpan Dokumentasi...');
                const form = document.getElementById('form-step-7');
                const formData = new FormData(form);
                
                const inputs = ['foto_kode_produksi', 'foto_kode_produksi_sekunder', 'qr_code', 'label_polyroll', 'label_supplier'];
                for (let name of inputs) {
                    const fileInput = form.querySelector(`input[name="${name}"]`);
                    if (fileInput && fileInput.files.length > 0) {
                        const compressedFile = await compressImage(fileInput.files[0]);
                        formData.set(name, compressedFile);
                    } else if (name !== 'foto_kode_produksi' && name !== 'foto_kode_produksi_sekunder') {
                        formData.set(name, '-');
                    }
                }
                
                ajaxPost('{{ route("wizards.packing.store-dokumentasi") }}', formData).then(res => {
                    hideLoading();
                    if (res.success) {
                        // Show final success panel
                        document.querySelectorAll('.wizard-step').forEach(p => p.style.display = 'none');
                        document.getElementById('panel-7').classList.remove('d-none');
                        document.getElementById('panel-7').scrollIntoView({ behavior: 'smooth' });
                        for (let i = 1; i <= 7; i++) {
                            const el = document.getElementById('step-indicator-' + i);
                            if(el) {
                                el.classList.remove('active');
                                el.classList.add('completed');
                            }
                        }
                        sessionStorage.removeItem(STORAGE_KEY);
                        sessionStorage.removeItem(FORM_DATA_KEY);
                    } else {
                        if (res.errors) showErrors(res.errors);
                        else showAlert('danger', res.message || 'Tolong Lengkapi Pengisian Datanya.');
                    }
                }).catch(e => { 
                    hideLoading(); 
                    showAlert('danger', 'Terjadi kesalahan saat menyimpan data.'); 
                });
            });
        }
    });

    // ========== RESTORE STATE ON LOAD ==========
    if (state.id_pengemasan_produk) {
        document.getElementById('p2_id_pengemasan_produk').value = state.id_pengemasan_produk;
        document.getElementById('p3_id_pengemasan_produk').value = state.id_pengemasan_produk;
        document.getElementById('p4_id_pengemasan_produk').value = state.id_pengemasan_produk;
        document.getElementById('p5_id_pengemasan_produk').value = state.id_pengemasan_produk;
        document.getElementById('p6_id_pengemasan_produk').value = state.id_pengemasan_produk;
    }
    if (state.id_pengemasan_plastik) {
        document.getElementById('p3_id_pengemasan_plastik').value = state.id_pengemasan_plastik;
        document.getElementById('p4_id_pengemasan_plastik').value = state.id_pengemasan_plastik;
        document.getElementById('p5_id_pengemasan_plastik').value = state.id_pengemasan_plastik;
        document.getElementById('p6_id_pengemasan_plastik').value = state.id_pengemasan_plastik;
    }
    if (state.id_berat_produk_bag) {
        document.getElementById('p4_id_berat_produk_bag').value = state.id_berat_produk_bag;
        document.getElementById('p5_id_berat_produk_bag').value = state.id_berat_produk_bag;
        document.getElementById('p6_id_berat_produk_bag').value = state.id_berat_produk_bag;
    }
    if (state.id_berat_produk_box) {
        document.getElementById('p5_id_berat_produk_box').value = state.id_berat_produk_box;
        document.getElementById('p6_id_berat_produk_box').value = state.id_berat_produk_box;
    }
    if (state.id_pengemasan_karton) {
        document.getElementById('p6_id_pengemasan_karton').value = state.id_pengemasan_karton;
    }

    if (state.currentStep > 1 && state.currentStep <= 7) {
        setTimeout(() => {
            goToStep(state.currentStep);
            
            if (state.id_produk) {
                const mainProd = document.getElementById('id_produk_step_1');
                if (mainProd) mainProd.value = state.id_produk;
                
                if (state.berat_produk) {
                    const selBerat = document.getElementById('nilai_select_berat');
                    if (selBerat) {
                        // Populate dulu dari data bag, lalu set nilai tersimpan
                        populateBeratByProduk(state.id_produk);
                        selBerat.value = state.berat_produk;
                    }
                }
                
                copyProdukToStep(state.id_produk, state.id_produk_text || '-');
                filterDataBagByProduk(state.id_produk);
                filterDataBoxByProduk(state.id_produk);
                
                restoreFormData();
            } else {
                restoreFormData();
            }
            showAlert('info', '<i class="fas fa-history mr-1"></i> Sesi berhasil dipulihkan! Melanjutkan pengisian data yang belum selesai.');
        }, 150);
    }

    // ========== POPULATE BERAT DARI DATA BAG BERDASARKAN PRODUK ==========
    const dataBagsJson = @json($dataBags->map(fn($b) => ['id_produk' => $b->id_produk, 'berat' => $b->berat, 'std_bag' => $b->std_bag]));

    function populateBeratByProduk(idProduk) {
        const sel = document.getElementById('nilai_select_berat');
        sel.innerHTML = '<option value="">-- Pilih Berat --</option>';
        if (!idProduk) {
            sel.innerHTML = '<option value="">-- Pilih produk terlebih dahulu --</option>';
            return;
        }
        // Ambil entri unik (berat+std_bag) untuk produk ini
        const seen = new Set();
        const entries = [];
        dataBagsJson.forEach(b => {
            if (String(b.id_produk) === String(idProduk) && b.berat) {
                const key = String(b.berat);
                if (!seen.has(key)) {
                    seen.add(key);
                    entries.push({ berat: b.berat, label: b.berat });
                }
            }
        });
        if (entries.length === 0) {
            sel.innerHTML = '<option value="">-- Tidak ada data bag untuk produk ini --</option>';
            return;
        }
        // Sort numerik
        entries.sort((a, b) => parseFloat(a.berat) - parseFloat(b.berat));
        sel.innerHTML = '<option value="">-- Pilih Berat --</option>';
        entries.forEach(e => {
            const opt = document.createElement('option');
            opt.value = e.berat;
            opt.textContent = e.label;
            sel.appendChild(opt);
        });
        // Jika hanya 1 pilihan, auto-select
        if (entries.length === 1) {
            sel.value = entries[0].berat;
            sel.dispatchEvent(new Event('change'));
        }
    }

    document.getElementById('id_produk_step_1').addEventListener('change', function() {
        populateBeratByProduk(this.value);
        // Reset filter data bag & box juga
        filterDataBagByProduk(this.value);
        filterDataBoxByProduk(this.value);
    });

    function populateBeratByProduk2(idProduk) {
        const sel = document.getElementById('nilai_select_berat_2');
        if (!sel) return;
        sel.innerHTML = '<option value="">-- Pilih Berat --</option>';
        if (!idProduk) {
            sel.innerHTML = '<option value="">-- Pilih produk ke-2 terlebih dahulu --</option>';
            return;
        }
        const seen = new Set();
        const entries = [];
        dataBagsJson.forEach(b => {
            if (String(b.id_produk) === String(idProduk) && b.berat) {
                const key = String(b.berat);
                if (!seen.has(key)) {
                    seen.add(key);
                    entries.push({ berat: b.berat, label: b.berat });
                }
            }
        });
        if (entries.length === 0) {
            sel.innerHTML = '<option value="">-- Tidak ada data bag untuk produk ini --</option>';
            return;
        }
        entries.sort((a, b) => parseFloat(a.berat) - parseFloat(b.berat));
        sel.innerHTML = '<option value="">-- Pilih Berat --</option>';
        entries.forEach(e => {
            const opt = document.createElement('option');
            opt.value = e.berat;
            opt.textContent = e.label + ' gr';
            sel.appendChild(opt);
        });
        if (entries.length === 1) {
            sel.value = entries[0].berat;
            sel.dispatchEvent(new Event('change'));
        }
    }

    document.getElementById('id_produk_step_1_2')?.addEventListener('change', function() {
        populateBeratByProduk2(this.value);
    });

    document.getElementById('is_kombinasi')?.addEventListener('change', function() {
        const container = document.getElementById('container-produk-2');
        const prod2 = document.getElementById('id_produk_step_1_2');
        const berat2 = document.getElementById('nilai_select_berat_2');
        const kode2 = document.getElementById('kode_produksi_2');
        if (this.checked) {
            if (container) container.style.display = 'block';
            if (prod2) prod2.required = true;
            if (berat2) berat2.required = true;
            if (kode2) kode2.required = true;
        } else {
            if (container) container.style.display = 'none';
            if (prod2) { prod2.required = false; prod2.value = ''; $(prod2).trigger('change'); }
            if (berat2) { berat2.required = false; berat2.value = ''; }
            if (kode2) { kode2.required = false; kode2.value = ''; }
            const exp2 = document.getElementById('tanggal_expired_2');
            if (exp2) exp2.value = '';
        }
    });

    // Expose ke global scope agar jQuery ready handler bisa mengakses
    window.populateBeratByProduk = populateBeratByProduk;
    window.populateBeratByProduk2 = populateBeratByProduk2;
    window.filterDataBagByProduk = filterDataBagByProduk;
    window.filterDataBoxByProduk = filterDataBoxByProduk;
})();

// ============================================================
// Toggle mode Foto/Status untuk Foto Kode Produksi Sekunder
// ============================================================
function setModeSekunder(mode) {
    if (mode === 'foto') {
        document.getElementById('mode-foto-sekunder').style.display = '';
        document.getElementById('mode-status-sekunder').style.display = 'none';
        document.getElementById('btn-mode-foto-sekunder').classList.add('active');
        document.getElementById('btn-mode-status-sekunder').classList.remove('active');
        // Kosongkan status hidden jika pindah ke mode foto
        document.getElementById('foto_kode_produksi_sekunder_status').value = '';
    } else {
        document.getElementById('mode-foto-sekunder').style.display = 'none';
        document.getElementById('mode-status-sekunder').style.display = '';
        document.getElementById('btn-mode-foto-sekunder').classList.remove('active');
        document.getElementById('btn-mode-status-sekunder').classList.add('active');
        // Reset file input jika pindah ke mode status
        var fileInput = document.getElementById('foto_kode_produksi_sekunder');
        if (fileInput) fileInput.value = '';
    }
}

function setStatusSekunder(value) {
    document.getElementById('foto_kode_produksi_sekunder_status').value = value;
    var btnOk    = document.getElementById('btn-ok-sekunder');
    var btnNotOk = document.getElementById('btn-notok-sekunder');
    var label    = document.getElementById('label-status-sekunder');

    if (value === 'OK') {
        btnOk.classList.replace('btn-outline-success', 'btn-success');
        btnNotOk.classList.replace('btn-danger', 'btn-outline-danger');
        label.textContent = 'Dipilih: OK';
        label.className = 'd-block mt-1 text-success font-weight-bold';
    } else {
        btnNotOk.classList.replace('btn-outline-danger', 'btn-danger');
        btnOk.classList.replace('btn-success', 'btn-outline-success');
        label.textContent = 'Dipilih: Not OK';
        label.className = 'd-block mt-1 text-danger font-weight-bold';
    }
}

// jQuery ready: handle Select2 change untuk produk di step 1
$(document).ready(function() {
    $('#id_produk_step_1').on('change', function() {
        if (typeof populateBeratByProduk === 'function') {
            populateBeratByProduk(this.value);
        }
        if (typeof filterDataBagByProduk === 'function') {
            filterDataBagByProduk(this.value);
        }
        if (typeof filterDataBoxByProduk === 'function') {
            filterDataBoxByProduk(this.value);
        }
    });

    $('#id_produk_step_1_2').on('change', function() {
        if (typeof populateBeratByProduk2 === 'function') {
            populateBeratByProduk2(this.value);
        }
    });
});
</script>
@endpush
@endsection
