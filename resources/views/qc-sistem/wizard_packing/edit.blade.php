@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><i class="fas fa-edit text-warning"></i> Edit Pengemasan Packing</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route($pembekuan->is_audit ? 'wizards.packing.audit' : 'wizards.packing.index') }}">Pengemasan Packing</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                <form action="{{ route('wizards.packing.update', $pembekuan->uuid) }}" method="POST" enctype="multipart/form-data" id="form-edit-packing">
                    @csrf
                    @method('PUT')

                    <div class="row">


                        {{-- ===== STEP-BY-STEP DETAILS ===== --}}
                        <div class="col-12">

                            {{-- STEP 1: IDENTITAS PRODUK --}}
                            @if($pembekuan->pengemasanProduk)
                            <div class="card card-outline card-primary mb-4 shadow-sm">
                                <div class="card-header bg-light">
                                    <h3 class="card-title"><i class="fas fa-id-card text-primary mr-1"></i> Identitas Produk (Langkah 1)</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Tanggal <span class="text-danger">*</span></label>
                                                <input type="date" name="step1[tanggal]" class="form-control" value="{{ $pembekuan->pengemasanProduk->tanggal ? \Carbon\Carbon::parse($pembekuan->pengemasanProduk->tanggal)->format('Y-m-d') : '' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Jam <span class="text-danger">*</span></label>
                                                <input type="time" name="step1[jam]" class="form-control" value="{{ $pembekuan->pengemasanProduk->jam ? \Carbon\Carbon::parse($pembekuan->pengemasanProduk->jam)->format('H:i') : '' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Shift <span class="text-danger">*</span></label>
                                                <select name="step1[id_shift]" class="form-control" required>
                                                    <option value="">Pilih Shift</option>
                                                    @foreach($shifts as $sh)
                                                        <option value="{{ $sh->id }}" {{ ($pembekuan->pengemasanProduk->id_shift == $sh->id) ? 'selected' : '' }}>
                                                            {{ $sh->shift }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Produk <span class="text-danger">*</span></label>
                                                <select name="step1[id_produk]" class="form-control select2" required>
                                                    <option value="">Pilih Produk</option>
                                                    @foreach($produks as $produk)
                                                        <option value="{{ $produk->id }}" {{ $pembekuan->pengemasanProduk->id_produk == $produk->id ? 'selected' : '' }}>
                                                            {{ $produk->nama_produk }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Kode Produksi <span class="text-danger">*</span></label>
                                                <input type="text" name="step1[kode_produksi]" class="form-control" value="{{ $pembekuan->pengemasanProduk->kode_produksi }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Berat Produk (gr)</label>
                                                <select name="step1[berat]" class="form-control select2" data-nilai-berat="true" data-selected="{{ $pembekuan->pengemasanProduk->berat }}"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Tanggal Expired</label>
                                                <input type="date" name="step1[tanggal_expired]" class="form-control" value="{{ $pembekuan->pengemasanProduk->tanggal_expired ? \Carbon\Carbon::parse($pembekuan->pengemasanProduk->tanggal_expired)->format('Y-m-d') : '' }}">
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <hr>
                                            <div class="form-group mb-3">
                                                <div class="custom-control custom-switch custom-switch-off-secondary custom-switch-on-warning">
                                                    <input type="checkbox" class="custom-control-input" id="is_kombinasi_edit" name="step1[is_kombinasi]" value="1" {{ $pembekuan->pengemasanProduk->is_kombinasi ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold" for="is_kombinasi_edit">
                                                        <i class="fas fa-boxes text-warning mr-1"></i> Apakah 1 Box berisi 2 Produk? (Box Kombinasi / Dual-Product)
                                                    </label>
                                                </div>
                                            </div>

                                            <div id="container-produk-2-edit" style="{{ $pembekuan->pengemasanProduk->is_kombinasi ? '' : 'display: none;' }}" class="p-3 border border-warning rounded bg-light mb-3">
                                                <h5 class="text-warning font-weight-bold mb-3"><i class="fas fa-box-open mr-1"></i> Data Produk Ke-2 (Dalam 1 Box)</h5>
                                                <div class="form-group">
                                                    <label>Nama Produk Ke-2 <span class="text-danger">*</span></label>
                                                    <select name="step1[id_produk_2]" id="id_produk_step_1_2_edit" class="form-control select2">
                                                        <option value="">Pilih Produk Ke-2</option>
                                                        @foreach($produks as $produk)
                                                            <option value="{{ $produk->id }}" {{ $pembekuan->pengemasanProduk->id_produk_2 == $produk->id ? 'selected' : '' }}>
                                                                {{ $produk->nama_produk }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Berat Produk Ke-2 (gr) <span class="text-danger">*</span></label>
                                                    <select id="nilai_select_berat_2_edit" class="form-control" name="step1[berat_produk_2]" data-selected="{{ $pembekuan->pengemasanProduk->berat_produk_2 }}">
                                                        <option value="">-- Pilih produk ke-2 terlebih dahulu --</option>
                                                    </select>
                                                    <small class="text-muted">Berat diambil dari data master bag produk ke-2</small>
                                                </div>
                                                <div class="form-group">
                                                    <label>Tanggal Expired Produk Ke-2</label>
                                                    <input type="date" name="step1[tanggal_expired_2]" id="tanggal_expired_2_edit" class="form-control" value="{{ $pembekuan->pengemasanProduk->tanggal_expired_2 ? \Carbon\Carbon::parse($pembekuan->pengemasanProduk->tanggal_expired_2)->format('Y-m-d') : '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>Kode Produksi Ke-2 <span class="text-danger">*</span></label>
                                                    <input type="text" name="step1[kode_produksi_2]" id="kode_produksi_2_edit" class="form-control" placeholder="Masukkan kode produksi ke-2" value="{{ $pembekuan->pengemasanProduk->kode_produksi_2 }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- STEP 2: PEMBEKUAN PRODUK --}}
                            <div class="card card-outline card-info mb-4 shadow-sm">
                                <div class="card-header bg-light">
                                    <h3 class="card-title"><i class="fas fa-snowflake text-info mr-1"></i> Langkah Pembekuan Produk (Langkah 2)</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Standar Suhu Produk Keluar IQF (ºC)</label>
                                                <input type="text" name="step1[std_suhu_produk_iqf]" class="form-control" value="{{ $pembekuan->pengemasanProduk->std_suhu_produk_iqf }}" placeholder="Misal: Max -18 ºC" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Waktu Pengecekan After IQF</label>
                                                <input type="text" name="step1[waktu_awal_packing]" class="form-control" value="{{ $pembekuan->pengemasanProduk->waktu_awal_packing }}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group" id="edit-suhu-produk-container">
                                                <label>Aktual Suhu (°C)</label>
                                                @php
                                                    $suhuRaw = $pembekuan->pengemasanProduk->aktual_suhu_produk;
                                                    $suhuArray = explode(',', $suhuRaw ?? '');
                                                    $suhuArray = array_map('trim', is_array($suhuArray) ? $suhuArray : []);
                                                    $suhuArray = array_filter($suhuArray, function($val) {
                                                        return !is_null($val) && $val !== '';
                                                    });
                                                    if(empty($suhuArray)) {
                                                        $suhuArray = [''];
                                                    }
                                                @endphp
                                                @foreach($suhuArray as $index => $suhu)
                                                <div class="input-group mb-2 edit-suhu-produk-group">
                                                    <input type="text" name="step1[aktual_suhu_produk][]" class="form-control" value="{{ $suhu }}" placeholder="Misal: -18.5" required>
                                                    <div class="input-group-append">
                                                        @if($index === 0)
                                                            <button type="button" class="btn btn-success add-edit-suhu"><i class="fas fa-plus"></i></button>
                                                        @else
                                                            <button type="button" class="btn btn-danger remove-edit-suhu"><i class="fas fa-minus"></i></button>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- STEP 3: PENGEMASAN PLASTIK --}}
                            @if($pembekuan->pengemasanPlastik)
                            <div class="card card-outline card-success mb-4 shadow-sm">
                                <div class="card-header bg-light">
                                    <h3 class="card-title"><i class="fas fa-shopping-bag text-success mr-1"></i> Pengemasan Plastik (Langkah 3)</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Identitas Produk</label>
                                                <select name="step2[identitas_produk]" class="form-control">
                                                    <option value="✔" {{ $pembekuan->pengemasanPlastik->identitas_produk == '✔' ? 'selected' : '' }}>✔ OK</option>
                                                    <option value="✘" {{ $pembekuan->pengemasanPlastik->identitas_produk == '✘' ? 'selected' : '' }}>✘ Tidak Sesuai Spesifikasi</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Kode Kemasan</label>
                                                <input type="text" name="step2[kode_kemasan_plastik]" class="form-control" value="{{ $pembekuan->pengemasanPlastik->kode_kemasan_plastik }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" id="line-plastik-edit-container">
                                                <label>Line</label>
                                                @php
                                                    $lineRaw = $pembekuan->pengemasanPlastik->line;
                                                    $lineArray = explode(',', $lineRaw ?? '');
                                                    $lineArray = array_map('trim', is_array($lineArray) ? $lineArray : []);
                                                    $lineArray = array_filter($lineArray, function($val) {
                                                        return !is_null($val) && $val !== '';
                                                    });
                                                    if(empty($lineArray)) {
                                                        $lineArray = [''];
                                                    }
                                                @endphp
                                                @foreach($lineArray as $index => $lineVal)
                                                <div class="input-group mb-2 line-plastik-edit-group">
                                                    <select name="step2[line][]" class="form-control">
                                                        <option value="">Pilih Line</option>
                                                        @for($i=1; $i<=8; $i++)
                                                            <option value="{{ $i }}" {{ $lineVal == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                    <div class="input-group-append">
                                                        @if($index === 0)
                                                            <button type="button" class="btn btn-success add-line-plastik-edit"><i class="fas fa-plus"></i></button>
                                                        @else
                                                            <button type="button" class="btn btn-danger remove-line-plastik-edit"><i class="fas fa-minus"></i></button>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Waktu Pengemasan</label>
                                                <input type="text" name="step2[waktu_pengemasan]" class="form-control" value="{{ $pembekuan->pengemasanPlastik->waktu_pengemasan }}" placeholder="Misal: 08:00 - 10:00">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Kesesuaian Label Plastik</label>
                                                <select name="step2[kesesuaian_label_plastik]" class="form-control">
                                                    <option value="OK" {{ $pembekuan->pengemasanPlastik->kesesuaian_label_plastik == 'OK' ? 'selected' : '' }}>OK</option>
                                                    <option value="Not OK" {{ $pembekuan->pengemasanPlastik->kesesuaian_label_plastik == 'Not OK' ? 'selected' : '' }}>Not OK</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Kekuatan Seal</label>
                                                <select name="step2[kekuatan_seal]" class="form-control">
                                                    <option value="✔" {{ $pembekuan->pengemasanPlastik->kekuatan_seal == '✔' ? 'selected' : '' }}>✔ OK</option>
                                                    <option value="✘" {{ $pembekuan->pengemasanPlastik->kekuatan_seal == '✘' ? 'selected' : '' }}>✘ Tidak Sesuai Spesifikasi</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Proses Penimbangan</label>
                                                <select name="step2[proses_penimbangan]" class="form-control">
                                                    <option value="mhw" {{ $pembekuan->pengemasanPlastik->proses_penimbangan == 'mhw' ? 'selected' : '' }}>MHW</option>
                                                    <option value="manual" {{ $pembekuan->pengemasanPlastik->proses_penimbangan == 'manual' ? 'selected' : '' }}>Manual</option>
                                                    <option value="korin" {{ $pembekuan->pengemasanPlastik->proses_penimbangan == 'korin' ? 'selected' : '' }}>Korin</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Proses Sealing</label>
                                                <select name="step2[proses_sealing]" class="form-control">
                                                    <option value="bag-sealer" {{ $pembekuan->pengemasanPlastik->proses_sealing == 'bag-sealer' ? 'selected' : '' }}>Bag Sealer</option>
                                                    <option value="manual" {{ $pembekuan->pengemasanPlastik->proses_sealing == 'manual' ? 'selected' : '' }}>Manual</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Foto Nomor MD</label>
                                                @if($pembekuan->pengemasanPlastik->nomor_md)
                                                    <div class="mb-2">
                                                        <a href="#" data-toggle="modal" data-target="#modalNomorMdEdit{{ $pembekuan->pengemasanPlastik->id }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-image mr-1"></i> Lihat Foto Sebelumnya
                                                        </a>
                                                        <!-- Modal Preview Foto Nomor MD -->
                                                        <div class="modal fade" id="modalNomorMdEdit{{ $pembekuan->pengemasanPlastik->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Foto Nomor MD</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body text-center">
                                                                        <img src="{{ asset($assetPath . 'storage/' . $pembekuan->pengemasanPlastik->nomor_md) }}" alt="Foto Nomor MD" style="max-width:100%;max-height:70vh;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <input type="file" name="step2[nomor_md]" class="form-control-file mt-2" accept="image/*" capture="camera">
                                                <small class="text-muted">Upload foto baru untuk mengganti.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- STEP 4: BERAT PRODUK BAG --}}
                            @if($semuaBeratProdukBag && count($semuaBeratProdukBag) > 0)
                            <div class="card card-outline card-warning mb-4 shadow-sm">
                                <div class="card-header bg-light">
                                    <h3 class="card-title"><i class="fas fa-weight-hanging text-warning mr-1"></i> Berat Produk Bag (Langkah 4)</h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-bordered table-striped m-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Line</th>
                                                <th>Nilai Standar Pack</th>
                                                <th>Aktual (gr)</th>
                                                <th>Rata-rata (gr)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($semuaBeratProdukBag as $bag)
                                            <tr>
                                                <td>
                                                    <select name="step3[{{ $bag->id }}][line]" class="form-control form-control-sm">
                                                        <option value="">Pilih Line</option>
                                                        @for ($i = 1; $i <= 8; $i++)
                                                            <option value="{{ $i }}" {{ $bag->line == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="step3[{{ $bag->id }}][id_data_bag]" class="form-control form-control-sm edit-id-data-bag">
                                                        <option value="">Pilih Nilai Standar Pack</option>
                                                        @foreach($dataBags as $dataBag)
                                                            <option value="{{ $dataBag->id }}" class="opt-bag-{{ $dataBag->id_produk }}" data-berat="{{ $dataBag->berat }}" {{ $bag->id_data_bag == $dataBag->id ? 'selected' : '' }}>{{ $dataBag->std_bag }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    @php
                                                        $berats = is_string($bag->berat_aktual) ? json_decode($bag->berat_aktual, true) : $bag->berat_aktual;
                                                        if (!is_array($berats) || empty($berats)) $berats = [''];
                                                    @endphp
                                                    <div id="container-berat-aktual-edit-{{ $bag->id }}">
                                                    @foreach($berats as $idx => $b)
                                                        <div class="input-group mb-1">
                                                            <input type="number" step="0.1" name="step3[{{ $bag->id }}][berat_aktual][]" class="form-control form-control-sm aktual-bag" data-id="{{ $bag->id }}" value="{{ $b }}">
                                                            <div class="input-group-append">
                                                                @if($idx === 0)
                                                                    <button type="button" class="btn btn-success btn-sm add-berat-aktual-edit" data-id="{{ $bag->id }}"><i class="fas fa-plus"></i></button>
                                                                @else
                                                                    <button type="button" class="btn btn-danger btn-sm remove-berat-aktual-edit"><i class="fas fa-minus"></i></button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    </div>
                                                </td>
                                                <td><input type="number" step="0.1" name="step3[{{ $bag->id }}][rata_rata_berat]" id="rata-bag-{{ $bag->id }}" class="form-control form-control-sm" value="{{ $bag->rata_rata_berat }}" readonly></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            {{-- STEP 5: BERAT PRODUK BOX --}}
                            @if($pembekuan->beratProdukBox)
                            <div class="card card-outline card-danger mb-4 shadow-sm">
                                <div class="card-header bg-light">
                                    <h3 class="card-title"><i class="fas fa-cube text-danger mr-1"></i> Berat Produk Box (Langkah 5)</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group" id="edit-berat-aktual-box-container">
                                                <label>Berat Aktual (gr)</label>
                                                @php
                                                    $beratsBox = is_string($pembekuan->beratProdukBox->berat_aktual) ? json_decode($pembekuan->beratProdukBox->berat_aktual, true) : $pembekuan->beratProdukBox->berat_aktual;
                                                    if (!is_array($beratsBox) || empty($beratsBox)) $beratsBox = [''];
                                                @endphp
                                                @foreach($beratsBox as $idx => $b)
                                                <div class="input-group mb-2 edit-berat-aktual-box-group">
                                                    <input type="number" step="0.1" name="step4[berat_aktual][]" class="form-control aktual-box" value="{{ $b }}" required>
                                                    <div class="input-group-append">
                                                        @if($idx === 0)
                                                            <button type="button" class="btn btn-success add-edit-berat-box"><i class="fas fa-plus"></i></button>
                                                        @else
                                                            <button type="button" class="btn btn-danger remove-edit-berat-box"><i class="fas fa-minus"></i></button>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nilai Standar Box</label>
                                                <select name="step4[id_data_box]" id="id_data_box_edit" class="form-control">
                                                    <option value="">Pilih Nilai Standar Box</option>
                                                    @foreach($dataBoxes as $dataBox)
                                                        <option value="{{ $dataBox->id }}" class="opt-box-{{ $dataBox->id_produk }}" data-berat="{{ $dataBox->berat }}" data-std-isi="{{ $dataBox->std_isi_pack_karton }}" {{ $pembekuan->beratProdukBox->id_data_box == $dataBox->id ? 'selected' : '' }}>{{ $dataBox->std_box }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Rata-rata (gr)</label>
                                                <input type="number" step="0.1" name="step4[rata_rata_berat]" id="box-rata" class="form-control" value="{{ $pembekuan->beratProdukBox->rata_rata_berat }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- STEP 6: PENGEMASAN KARTON --}}
                            @if($pembekuan->pengemasanKarton)
                            <div class="card card-outline card-info mb-4 shadow-sm">
                                <div class="card-header bg-light">
                                    <h3 class="card-title"><i class="fas fa-boxes text-info mr-1"></i> Pengemasan Karton (Langkah 6)</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Kesesuaian Label Karton</label>
                                                <select name="step5[identitas_produk_pada_karton]" class="form-control">
                                                    <option value="✔" {{ $pembekuan->pengemasanKarton->identitas_produk_pada_karton == '✔' ? 'selected' : '' }}>✔ OK</option>
                                                    <option value="✘" {{ $pembekuan->pengemasanKarton->identitas_produk_pada_karton == '✘' ? 'selected' : '' }}>✘ Tidak Sesuai Spesifikasi</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Standar isi pack dalam karton</label>
                                                <input type="number" name="step5[standar_jumlah_karton]" id="standar_jumlah_karton_edit" class="form-control" value="{{ $pembekuan->pengemasanKarton->standar_jumlah_karton }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Aktual isi pack dalam karton</label>
                                                <input type="text" name="step5[aktual_jumlah_karton]" class="form-control" value="{{ $pembekuan->pengemasanKarton->aktual_jumlah_karton }}" placeholder="Pisahkan dengan koma jika lebih dari satu">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Kesesuaian Standar Isi Pack dengan Aktual</label>
                                        @php
                                            $isiPack = $pembekuan->pengemasanKarton->kesesuaian_isi_pack;
                                            if (is_string($isiPack)) $isiPack = json_decode($isiPack, true);
                                            if (!is_array($isiPack) || empty($isiPack)) $isiPack = [''];
                                        @endphp
                                        <div id="edit-kesesuaian-isi-pack-container">
                                            @foreach($isiPack as $idx => $val)
                                            <div class="input-group mb-2 edit-kesesuaian-isi-pack-group">
                                                <select name="step5[kesesuaian_isi_pack][]" class="form-control">
                                                    <option value="" {{ $val == '' ? 'selected' : '' }}>Pilih Kesesuaian</option>
                                                    <option value="✔" {{ $val == '✔' ? 'selected' : '' }}>✔ Sesuai</option>
                                                    <option value="✘" {{ $val == '✘' ? 'selected' : '' }}>✘ Tidak Sesuai</option>
                                                </select>
                                                <div class="input-group-append">
                                                    @if($idx === 0)
                                                        <button type="button" class="btn btn-success add-edit-kesesuaian-isi"><i class="fas fa-plus"></i></button>
                                                    @else
                                                        <button type="button" class="btn btn-danger remove-edit-kesesuaian-isi"><i class="fas fa-minus"></i></button>
                                                    @endif
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- STEP 7: DOKUMENTASI --}}
                            <div class="card card-outline card-dark mb-4 shadow-sm">
                                <div class="card-header bg-light">
                                    <h3 class="card-title"><i class="fas fa-camera text-dark mr-1"></i> Dokumentasi (Langkah 7)</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Foto Kode Produksi Primer dan Best Before</label>
                                                @if($pembekuan->foto_kode_produksi)
                                                    <div class="mb-2">
                                                        <a href="#" data-toggle="modal" data-target="#modalKodeProduksi{{ $pembekuan->id }}" class="btn btn-sm btn-info"><i class="fas fa-image mr-1"></i> Lihat Foto Sebelumnya</a>
                                                        <!-- Modal -->
                                                        <div class="modal fade" id="modalKodeProduksi{{ $pembekuan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Foto Kode Produksi Primer</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body text-center">
                                                                        <img src="{{ asset($assetPath . 'storage/' . $pembekuan->foto_kode_produksi) }}" alt="Kode Produksi" style="max-width:100%;max-height:70vh;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <input type="file" name="step6[foto_kode_produksi]" class="form-control-file mt-2" accept="image/*">
                                                <small class="text-muted">Upload foto baru untuk mengganti.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Foto Kode Produksi Sekunder dan Best Before</label>

                                                @php
                                                    $sekunderIsStatus = in_array($pembekuan->foto_kode_produksi_sekunder, ['OK', 'Not OK']);
                                                    $sekunderInitMode = $sekunderIsStatus ? 'status' : 'foto';
                                                    $sekunderInitStatus = $sekunderIsStatus ? $pembekuan->foto_kode_produksi_sekunder : '';
                                                @endphp

                                                {{-- Toggle pilihan mode --}}
                                                <div class="mb-2">
                                                    <div class="d-flex flex-wrap" style="gap:8px;">
                                                        <button type="button"
                                                            class="btn btn-outline-primary btn-sm {{ $sekunderInitMode === 'foto' ? 'active' : '' }}"
                                                            id="edit-btn-mode-foto-sekunder"
                                                            onclick="editSetModeSekunder('foto')">
                                                            <i class="fas fa-camera mr-1"></i> Upload Foto
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-outline-primary btn-sm {{ $sekunderInitMode === 'status' ? 'active' : '' }}"
                                                            id="edit-btn-mode-status-sekunder"
                                                            onclick="editSetModeSekunder('status')">
                                                            <i class="fas fa-check-circle mr-1"></i> OK / Not OK
                                                        </button>
                                                    </div>
                                                </div>

                                                {{-- Mode upload foto --}}
                                                <div id="edit-mode-foto-sekunder" style="{{ $sekunderInitMode === 'foto' ? '' : 'display:none;' }}">
                                                    @if($pembekuan->foto_kode_produksi_sekunder && !$sekunderIsStatus)
                                                        <div class="mb-2">
                                                            <a href="#" data-toggle="modal" data-target="#modalKodeProduksiSekunder{{ $pembekuan->id }}" class="btn btn-sm btn-info">
                                                                <i class="fas fa-image mr-1"></i> Lihat Foto Sebelumnya
                                                            </a>
                                                            <div class="modal fade" id="modalKodeProduksiSekunder{{ $pembekuan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Foto Kode Produksi Sekunder</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body text-center">
                                                                            <img src="{{ asset($assetPath . 'storage/' . $pembekuan->foto_kode_produksi_sekunder) }}" alt="Kode Produksi Sekunder" style="max-width:100%;max-height:70vh;">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <input type="file" name="step6[foto_kode_produksi_sekunder]" id="edit-foto-kode-produksi-sekunder" class="form-control-file mt-2" accept="image/*">
                                                    <small class="text-muted">Upload foto baru untuk mengganti.</small>
                                                </div>

                                                {{-- Mode OK / Not OK --}}
                                                <div id="edit-mode-status-sekunder" style="{{ $sekunderInitMode === 'status' ? '' : 'display:none;' }}">
                                                    <input type="hidden" name="step6[foto_kode_produksi_sekunder_status]" id="edit-foto-kode-produksi-sekunder-status" value="{{ $sekunderInitStatus }}">
                                                    <div class="d-flex flex-wrap" style="gap:8px;">
                                                        <button type="button"
                                                            class="btn btn-sm {{ $sekunderInitStatus === 'OK' ? 'btn-success' : 'btn-outline-success' }}"
                                                            id="edit-btn-ok-sekunder"
                                                            onclick="editSetStatusSekunder('OK')">
                                                            <i class="fas fa-check mr-1"></i> OK
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-sm {{ $sekunderInitStatus === 'Not OK' ? 'btn-danger' : 'btn-outline-danger' }}"
                                                            id="edit-btn-notok-sekunder"
                                                            onclick="editSetStatusSekunder('Not OK')">
                                                            <i class="fas fa-times mr-1"></i> Not OK
                                                        </button>
                                                    </div>
                                                    <small class="d-block mt-1 {{ $sekunderInitStatus ? ($sekunderInitStatus === 'OK' ? 'text-success font-weight-bold' : 'text-danger font-weight-bold') : 'text-muted' }}"
                                                           id="edit-label-status-sekunder">
                                                        {{ $sekunderInitStatus ? 'Dipilih: ' . $sekunderInitStatus : 'Belum dipilih' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>QR Code</label>
                                                @if($pembekuan->qr_code)
                                                    <div class="mb-2">
                                                        <a href="#" data-toggle="modal" data-target="#modalQrCode{{ $pembekuan->id }}" class="btn btn-sm btn-info"><i class="fas fa-image mr-1"></i> Lihat Foto Sebelumnya</a>
                                                        <!-- Modal -->
                                                        <div class="modal fade" id="modalQrCode{{ $pembekuan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">QR Code</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body text-center">
                                                                        <img src="{{ asset($assetPath . 'storage/' . $pembekuan->qr_code) }}" alt="QR Code" style="max-width:100%;max-height:70vh;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <input type="file" name="step6[qr_code]" class="form-control-file mt-2" accept="image/*">
                                                <small class="text-muted">Upload foto baru untuk mengganti.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Foto Label Polyroll</label>
                                                @if($pembekuan->label_polyroll)
                                                    <div class="mb-2">
                                                        <a href="#" data-toggle="modal" data-target="#modalPolyroll{{ $pembekuan->id }}" class="btn btn-sm btn-info"><i class="fas fa-image mr-1"></i> Lihat Foto Sebelumnya</a>
                                                        <!-- Modal -->
                                                        <div class="modal fade" id="modalPolyroll{{ $pembekuan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Label Polyroll</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body text-center">
                                                                        <img src="{{ asset($assetPath . 'storage/' . $pembekuan->label_polyroll) }}" alt="Label Polyroll" style="max-width:100%;max-height:70vh;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <input type="file" name="step6[label_polyroll]" class="form-control-file mt-2" accept="image/*">
                                                <small class="text-muted">Upload foto baru untuk mengganti.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Label Supplier</label>
                                                @if($pembekuan->label_supplier)
                                                    <div class="mb-2">
                                                        <a href="#" data-toggle="modal" data-target="#modalSupplier{{ $pembekuan->id }}" class="btn btn-sm btn-info"><i class="fas fa-image mr-1"></i> Lihat Foto Sebelumnya</a>
                                                        <!-- Modal -->
                                                        <div class="modal fade" id="modalSupplier{{ $pembekuan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Label Supplier</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body text-center">
                                                                        <img src="{{ asset($assetPath . 'storage/' . $pembekuan->label_supplier) }}" alt="Label Supplier" style="max-width:100%;max-height:70vh;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <input type="file" name="step6[label_supplier]" class="form-control-file mt-2" accept="image/*">
                                                <small class="text-muted">Upload foto baru untuk mengganti.</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Keterangan</label>
                                                <textarea name="step6[keterangan]" class="form-control" rows="3" placeholder="Tambahkan keterangan jika diperlukan...">{{ $pembekuan->keterangan ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Status Release Produk <span class="text-danger">*</span></label>
                                                <select name="step6[status_release_produk]" class="form-control" required>
                                                    <option value="">Pilih Status</option>
                                                    <option value="ya" {{ ($pembekuan->pengemasanProduk->status_release_produk ?? '') == 'ya' ? 'selected' : '' }}>Ya</option>
                                                    <option value="tidak" {{ ($pembekuan->pengemasanProduk->status_release_produk ?? '') == 'tidak' ? 'selected' : '' }}>Tidak</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Tindakan Koreksi</label>
                                                <textarea name="step6[tindakan_koreksi]" class="form-control" rows="2" placeholder="Masukkan tindakan koreksi jika diperlukan...">{{ $pembekuan->pengemasanProduk->tindakan_koreksi ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="card mb-4 shadow-sm">
                            <div class="card-body text-right">
                                <a href="{{ route($pembekuan->is_audit ? 'wizards.packing.audit' : 'wizards.packing.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times mr-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-warning ml-2">
                                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Hitung Rata-rata Bag (Step 3)
        $(document).on('input', '.aktual-bag', function() {
            let id = $(this).data('id');
            let inputs = $('input[name="step3['+id+'][berat_aktual][]"]');
            
            let count = 0;
            let total = 0;
            
            inputs.each(function() {
                let val = parseFloat($(this).val()) || 0;
                if (val > 0) {
                    total += val;
                    count++;
                }
            });
            
            let rata = count > 0 ? (total / count).toFixed(2) : 0;
            $('#rata-bag-' + id).val(rata);
        });

        // Dynamic Berat Aktual Edit
        $(document).on('click', '.add-berat-aktual-edit', function() {
            let id = $(this).data('id');
            let container = $('#container-berat-aktual-edit-' + id);
            let newDiv = `
                <div class="input-group mb-1">
                    <input type="number" step="0.1" name="step3[${id}][berat_aktual][]" class="form-control form-control-sm aktual-bag" data-id="${id}" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-sm remove-berat-aktual-edit"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
            `;
            container.append(newDiv);
        });

        $(document).on('click', '.remove-berat-aktual-edit', function() {
            let group = $(this).closest('.input-group');
            let input = group.find('.aktual-bag');
            let id = input.data('id');
            group.remove();
            
            // Trigger input to recalculate average
            if(id) {
                let firstInput = $('input[name="step3['+id+'][berat_aktual][]"]').first();
                if(firstInput.length) {
                    firstInput.trigger('input');
                } else {
                    $('#rata-bag-' + id).val(0);
                }
            }
        });

        // Hitung Rata-rata Box (Step 4)
        $(document).on('input', '.aktual-box', function() {
            let inputs = $('.aktual-box');
            let count = 0;
            let total = 0;
            
            inputs.each(function() {
                let val = parseFloat($(this).val()) || 0;
                if (val > 0) {
                    total += val;
                    count++;
                }
            });
            
            let rata = count > 0 ? (total / count).toFixed(2) : 0;
            $('#box-rata').val(rata);
        });

        // Dynamic Edit Berat Box
        $(document).on('click', '.add-edit-berat-box', function() {
            let container = $('#edit-berat-aktual-box-container');
            let newDiv = `
                <div class="input-group mb-2 edit-berat-aktual-box-group">
                    <input type="number" step="0.1" name="step4[berat_aktual][]" class="form-control aktual-box" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-edit-berat-box"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
            `;
            container.append(newDiv);
        });

        $(document).on('click', '.remove-edit-berat-box', function() {
            $(this).closest('.input-group').remove();
            $('.aktual-box').first().trigger('input');
        });
    });

    // Dynamic Edit Suhu Produk
    document.addEventListener('click', function(e) {
        if (e.target && (e.target.classList.contains('add-edit-suhu') || e.target.closest('.add-edit-suhu'))) {
            const container = document.getElementById('edit-suhu-produk-container');
            const newDiv = document.createElement('div');
            newDiv.className = 'input-group mb-2 edit-suhu-produk-group';
            newDiv.innerHTML = `
                <input type="text" name="step1[aktual_suhu_produk][]" class="form-control" placeholder="Misal: -18.5" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-edit-suhu"><i class="fas fa-minus"></i></button>
                </div>
            `;
            container.appendChild(newDiv);
        }
        if (e.target && (e.target.classList.contains('remove-edit-suhu') || e.target.closest('.remove-edit-suhu'))) {
            e.target.closest('.edit-suhu-produk-group').remove();
        }

        // Dynamic Line Plastik Edit
        if (e.target && (e.target.classList.contains('add-line-plastik-edit') || e.target.closest('.add-line-plastik-edit'))) {
            const container = document.getElementById('line-plastik-edit-container');
            const newDiv = document.createElement('div');
            newDiv.className = 'input-group mb-2 line-plastik-edit-group';
            newDiv.innerHTML = `
                <select name="step2[line][]" class="form-control">
                    <option value="">Pilih Line</option>
                    @for($i=1; $i<=8; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-line-plastik-edit"><i class="fas fa-minus"></i></button>
                </div>
            `;
            container.appendChild(newDiv);
        }
        if (e.target && (e.target.classList.contains('remove-line-plastik-edit') || e.target.closest('.remove-line-plastik-edit'))) {
            e.target.closest('.line-plastik-edit-group').remove();
        }

        // Dynamic Kesesuaian Isi Pack Edit
        if (e.target && (e.target.classList.contains('add-edit-kesesuaian-isi') || e.target.closest('.add-edit-kesesuaian-isi'))) {
            const container = document.getElementById('edit-kesesuaian-isi-pack-container');
            const newDiv = document.createElement('div');
            newDiv.className = 'input-group mb-2 edit-kesesuaian-isi-pack-group';
            newDiv.innerHTML = `
                <select name="step5[kesesuaian_isi_pack][]" class="form-control">
                    <option value="">Pilih Kesesuaian</option>
                    <option value="✔">✔ Sesuai</option>
                    <option value="✘">✘ Tidak Sesuai</option>
                </select>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-edit-kesesuaian-isi"><i class="fas fa-minus"></i></button>
                </div>
            `;
            container.appendChild(newDiv);
        }
        if (e.target && (e.target.classList.contains('remove-edit-kesesuaian-isi') || e.target.closest('.remove-edit-kesesuaian-isi'))) {
            e.target.closest('.edit-kesesuaian-isi-pack-group').remove();
        }
    });

    // Hitung Rata-rata Bag (Step 3)
    $(document).on('input', '.aktual-bag', function() {
        let id = $(this).data('id');
        let inputs = $('input[name="step3['+id+'][berat_aktual][]"]');
        
        let count = 0;
        let total = 0;
        
        inputs.each(function() {
            let val = parseFloat($(this).val());
            if (!isNaN(val)) {
                total += val;
                count++;
            }
        });
        
        let avg = count > 0 ? (total / count).toFixed(2) : '';
        $('#rata-bag-' + id).val(avg);
    });

    // Filter Options dynamically based on Produk and Berat
    function filterDataBags(idProduk, beratProduk) {
        $('.edit-id-data-bag').each(function() {
            let select = $(this);
            let currentVal = select.val();
            let hasMatch = false;
            
            select.find('option').each(function() {
                let opt = $(this);
                if (opt.val() === "") return;
                
                let isProdukMatch = opt.hasClass('opt-bag-' + idProduk);
                let optBerat = parseFloat(opt.attr('data-berat'));
                let selBerat = parseFloat(beratProduk);
                let isBeratMatch = (!isNaN(optBerat) && !isNaN(selBerat) && optBerat === selBerat) || opt.attr('data-berat') == beratProduk;
                
                if (isProdukMatch && isBeratMatch) {
                    opt.show();
                    if (opt.val() === currentVal) hasMatch = true;
                } else {
                    opt.hide();
                }
            });
            
            if (!hasMatch) select.val("");
        });
    }
    
    function filterDataBoxes(idProduk, beratProduk) {
        let select = $('#id_data_box_edit');
        if (select.length === 0) return;
        
        let currentVal = select.val();
        let hasMatch = false;
        let firstValidOption = null;
        
        select.find('option').each(function() {
            let opt = $(this);
            if (opt.val() === "") return;
            
            let isProdukMatch = opt.hasClass('opt-box-' + idProduk);
            let optBerat = parseFloat(opt.attr('data-berat'));
            let selBerat = parseFloat(beratProduk);
            let isBeratMatch = (!isNaN(optBerat) && !isNaN(selBerat) && optBerat === selBerat) || opt.attr('data-berat') == beratProduk;
            
            if (isProdukMatch && isBeratMatch) {
                opt.show();
                if (firstValidOption === null) firstValidOption = opt.val();
                if (opt.val() === currentVal) hasMatch = true;
            } else {
                opt.hide();
            }
        });
        
        if (!hasMatch) {
            if (firstValidOption !== null) {
                select.val(firstValidOption).trigger('change');
            } else {
                select.val("").trigger('change');
                $('#standar_jumlah_karton_edit').val("");
            }
        } else {
            select.trigger('change');
        }
    }
    
    function checkStandarIsiPackKarton(idProduk) {
        if (!idProduk) return;
        let inputStandar = $('#standar_jumlah_karton_edit');
        // Hanya cek jika elemennya ada di DOM
        if (inputStandar.length > 0) {
            let standar = inputStandar.val();
            if (!standar || standar.trim() === '') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Standar isi pack per karton kosong, silahkan isi di data master standar box'
                    });
                } else {
                    alert('Standar isi pack per karton kosong, silahkan isi di data master standar box');
                }
            }
        }
    }

    $('select[name="step1[id_produk]"]').on('change', function() {
        let idProduk = $(this).val();
        let beratProduk = $('select[name="step1[berat]"]').val();
        filterDataBags(idProduk, beratProduk);
        filterDataBoxes(idProduk, beratProduk);
        checkStandarIsiPackKarton(idProduk);
    });
    
    $('select[name="step1[berat]"]').on('change', function() {
        let idProduk = $('select[name="step1[id_produk]"]').val();
        let beratProduk = $(this).val();
        filterDataBags(idProduk, beratProduk);
        filterDataBoxes(idProduk, beratProduk);
        checkStandarIsiPackKarton(idProduk);
    });
    
    $('#id_data_box_edit').on('change', function() {
        let selectedOption = $(this).find('option:selected');
        let stdIsi = selectedOption.attr('data-std-isi');
        if (stdIsi !== undefined && stdIsi !== '') {
            $('#standar_jumlah_karton_edit').val(stdIsi);
        } else {
            $('#standar_jumlah_karton_edit').val('');
        }
    });
    
    const dataBagsJsonEdit = @json($dataBags);

    function populateBeratByProduk2Edit(idProduk, selectedBerat) {
        const sel = document.getElementById('nilai_select_berat_2_edit');
        if (!sel) return;
        sel.innerHTML = '<option value="">-- Pilih Berat --</option>';
        if (!idProduk) {
            sel.innerHTML = '<option value="">-- Pilih produk ke-2 terlebih dahulu --</option>';
            return;
        }
        const seen = new Set();
        const entries = [];
        dataBagsJsonEdit.forEach(b => {
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
            if (selectedBerat && String(selectedBerat) === String(e.berat)) {
                opt.selected = true;
            }
            sel.appendChild(opt);
        });
        if (selectedBerat) {
            sel.value = selectedBerat;
        }
    }

    $('#id_produk_step_1_2_edit').on('change', function() {
        populateBeratByProduk2Edit($(this).val(), null);
    });

    $('#is_kombinasi_edit').on('change', function() {
        const container = document.getElementById('container-produk-2-edit');
        const prod2 = document.getElementById('id_produk_step_1_2_edit');
        const berat2 = document.getElementById('nilai_select_berat_2_edit');
        const kode2 = document.getElementById('kode_produksi_2_edit');
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
            const exp2 = document.getElementById('tanggal_expired_2_edit');
            if (exp2) exp2.value = '';
        }
    });

    // Initial filtering & initialization for Produk 2
    setTimeout(function() {
        let idProduk = $('select[name="step1[id_produk]"]').val();
        let beratProduk = $('select[name="step1[berat]"]').val();
        filterDataBags(idProduk, beratProduk);
        filterDataBoxes(idProduk, beratProduk);

        let idProduk2 = $('#id_produk_step_1_2_edit').val();
        let selectedBerat2 = $('#nilai_select_berat_2_edit').attr('data-selected');
        if (idProduk2) {
            populateBeratByProduk2Edit(idProduk2, selectedBerat2);
        }
    }, 500);

// ============================================================
// Toggle mode Foto/Status untuk Foto Kode Produksi Sekunder (Edit)
// ============================================================
function editSetModeSekunder(mode) {
    if (mode === 'foto') {
        document.getElementById('edit-mode-foto-sekunder').style.display = '';
        document.getElementById('edit-mode-status-sekunder').style.display = 'none';
        document.getElementById('edit-btn-mode-foto-sekunder').classList.add('active');
        document.getElementById('edit-btn-mode-status-sekunder').classList.remove('active');
        document.getElementById('edit-foto-kode-produksi-sekunder-status').value = '';
    } else {
        document.getElementById('edit-mode-foto-sekunder').style.display = 'none';
        document.getElementById('edit-mode-status-sekunder').style.display = '';
        document.getElementById('edit-btn-mode-foto-sekunder').classList.remove('active');
        document.getElementById('edit-btn-mode-status-sekunder').classList.add('active');
        var fileInput = document.getElementById('edit-foto-kode-produksi-sekunder');
        if (fileInput) fileInput.value = '';
    }
}

function editSetStatusSekunder(value) {
    document.getElementById('edit-foto-kode-produksi-sekunder-status').value = value;
    var btnOk    = document.getElementById('edit-btn-ok-sekunder');
    var btnNotOk = document.getElementById('edit-btn-notok-sekunder');
    var label    = document.getElementById('edit-label-status-sekunder');

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

</script>
@endpush
@endsection