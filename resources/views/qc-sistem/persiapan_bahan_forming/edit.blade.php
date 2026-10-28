{{-- filepath: resources/views/qc-sistem/persiapan_bahan_forming/edit.blade.php --}}

@extends('layouts.app')

@section('container')

@php
    /*
    |--------------------------------------------------------------------------
    | PERHITUNGAN DATA BERAT RM
    |--------------------------------------------------------------------------
    | Diletakkan di awal agar variabel selalu tersedia sebelum digunakan.
    */

    $getFloatEdit = function ($val) {
        if (is_null($val) || $val === '' || is_array($val)) {
            return 0.0;
        }

        $clean = preg_replace(
            '/[^\d\.\-]/',
            '',
            str_replace(',', '.', (string) $val)
        );

        return is_numeric($clean) ? (float) $clean : 0.0;
    };

    $suhuFormingList = collect($item->suhuForming ?? []);

    $beratListEdit = $suhuFormingList->map(function ($sf) use ($getFloatEdit) {
        return $getFloatEdit(
            optional($sf->bahanForming)->berat_rm ?? 0
        );
    });

    $totalBeratEdit = $beratListEdit->sum();

    $avgBeratEdit = $beratListEdit->count() > 0
        ? $beratListEdit->avg()
        : 0;
@endphp


<div class="main-panel">
    <div class="content-wrapper">

        <div class="content-header">
            <div class="container-fluid">

                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            <i class="fas fa-edit text-warning mr-2"></i>
                            Edit Verifikasi Persiapan Cold Mixing
                        </h1>
                    </div>

                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">

                            <li class="breadcrumb-item">
                                <a href="{{ route('persiapan-bahan-forming.index') }}">
                                    <i class="fas fa-home"></i>
                                    Dashboard
                                </a>
                            </li>

                            <li class="breadcrumb-item">
                                <a href="{{ route('persiapan-bahan-forming.index') }}">
                                    Verifikasi Persiapan Cold Mixing
                                </a>
                            </li>

                            <li class="breadcrumb-item active">
                                Edit Data
                            </li>

                        </ol>
                    </div>

                </div>

            </div>
        </div>


        <section class="content">

            <div class="container-fluid">

                {{-- =====================================================
                     ALERT SUCCESS
                     ===================================================== --}}

                @if(session('success'))

                    <div class="alert alert-success alert-dismissible fade show" role="alert">

                        <i class="fas fa-check-circle mr-2"></i>

                        {{ session('success') }}

                        <button type="button"
                                class="close"
                                data-dismiss="alert">

                            <span aria-hidden="true">
                                &times;
                            </span>

                        </button>

                    </div>

                @endif


                {{-- =====================================================
                     ALERT ERROR
                     ===================================================== --}}

                @if($errors->any())

                    <div class="alert alert-danger alert-dismissible fade show"
                         role="alert">

                        <i class="fas fa-exclamation-triangle mr-2"></i>

                        <strong>Terjadi kesalahan:</strong>

                        <ul class="mb-0 mt-2">

                            @foreach($errors->all() as $error)

                                <li>
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                        <button type="button"
                                class="close"
                                data-dismiss="alert">

                            <span aria-hidden="true">
                                &times;
                            </span>

                        </button>

                    </div>

                @endif


                <div class="row">

                    <div class="col-md-12">

                        <div class="card card-primary card-outline">


                            {{-- =================================================
                                 CARD HEADER
                                 ================================================= --}}

                            <div class="card-header">

                                <h3 class="card-title">

                                    <i class="fas fa-clipboard-list mr-2"></i>

                                    Form Edit Data

                                </h3>

                            </div>


                            {{-- =================================================
                                 FORM
                                 ================================================= --}}

                            <form action="{{ route('persiapan-bahan-forming.update', $item->uuid) }}"
                                  method="POST">

                                @csrf

                                @method('PUT')


                                <div class="card-body">

                                    <div class="row">


                                        {{-- =================================================
                                             KOLOM KIRI
                                             ================================================= --}}

                                        <div class="col-md-6">


                                            {{-- KODE PRODUKSI EMULSI --}}

                                            <div class="form-group">

                                                <label for="kode_produksi_emulsi"
                                                       class="font-weight-bold">

                                                    <i class="fas fa-barcode text-primary mr-1"></i>

                                                    Kode Produksi Emulsi

                                                </label>

                                                <input type="text"
                                                       id="kode_produksi_emulsi"
                                                       name="kode_produksi_emulsi"
                                                       class="form-control @error('kode_produksi_emulsi') is-invalid @enderror"
                                                       value="{{ old('kode_produksi_emulsi', $item->kode_produksi_emulsi) }}"
                                                       placeholder="Masukkan kode produksi emulsi">

                                                @error('kode_produksi_emulsi')

                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>

                                                @enderror

                                            </div>


                                            {{-- NAMA PRODUK --}}

                                            <div class="form-group">

                                                <label for="id_produk_select"
                                                       class="font-weight-bold">

                                                    <i class="fas fa-box text-primary mr-1"></i>

                                                    Nama Produk

                                                    <span class="text-danger">
                                                        *
                                                    </span>

                                                </label>

                                                <select name="id_produk"
                                                        id="id_produk_select"
                                                        class="form-control"
                                                        required>

                                                    <option value="">
                                                        Pilih Produk
                                                    </option>

                                                    @foreach($jenis_produk as $produk)

                                                        <option value="{{ $produk->id }}"
                                                            {{ old('id_produk', $item->formula->id_produk ?? null) == $produk->id ? 'selected' : '' }}>

                                                            {{ $produk->nama_produk }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>


                                            {{-- NOMOR FORMULA --}}

                                            <div class="form-group">

                                                <label for="id_formula_select"
                                                       class="font-weight-bold">

                                                    <i class="fas fa-list-ol text-info mr-1"></i>

                                                    Nomor Formula

                                                    <span class="text-danger">
                                                        *
                                                    </span>

                                                </label>

                                                <select name="id_formula"
                                                        id="id_formula_select"
                                                        class="form-control"
                                                        required>

                                                    <option value="{{ $item->id_formula }}"
                                                            selected>

                                                        {{ $item->formula->nomor_formula ?? '-' }}

                                                    </option>

                                                </select>

                                            </div>


                                            {{-- SUHU ADONAN --}}

                                            <div class="form-group">

                                                <label for="id_suhu_adonan"
                                                       class="font-weight-bold">

                                                    <i class="fas fa-thermometer-half text-danger mr-1"></i>

                                                    Suhu Adonan (STD)

                                                    <span class="text-danger">
                                                        *
                                                    </span>

                                                </label>

                                                <select name="id_suhu_adonan"
                                                        id="id_suhu_adonan"
                                                        class="form-control @error('id_suhu_adonan') is-invalid @enderror">

                                                    <option value="">
                                                        -- Pilih Suhu Adonan --
                                                    </option>

                                                    @isset($suhu_adonan)

                                                        @foreach($suhu_adonan as $sa)

                                                            <option value="{{ $sa->id }}"
                                                                {{ old('id_suhu_adonan', $item->id_suhu_adonan) == $sa->id ? 'selected' : '' }}>

                                                                STD:
                                                                {{ $sa->std_suhu }}

                                                            </option>

                                                        @endforeach

                                                    @endisset

                                                </select>

                                                @error('id_suhu_adonan')

                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>

                                                @enderror

                                            </div>


                                            {{-- GRAMASE --}}

                                            <div class="form-group">

                                                <label for="berat_sampling"
                                                       class="font-weight-bold">

                                                    Gramase (gram)

                                                </label>

                                                <select name="berat_sampling"
                                                        id="nilai_select_berat_sampling"
                                                        class="form-control"
                                                        data-nilai-berat
                                                        data-selected="{{ old('berat_sampling', $item->berat_sampling) }}">

                                                </select>

                                            </div>


                                            {{-- WAKTU MIXING --}}

                                            <div class="form-row">

                                                <div class="form-group col-md-6">

                                                    <label for="waktu_mulai_mixing"
                                                           class="font-weight-bold">

                                                        <i class="fas fa-play text-success mr-1"></i>

                                                        Waktu Mulai Mixing

                                                    </label>

                                                    <input type="text"
                                                           id="waktu_mulai_mixing"
                                                           name="waktu_mulai_mixing"
                                                           class="form-control @error('waktu_mulai_mixing') is-invalid @enderror"
                                                           value="{{ old('waktu_mulai_mixing', $item->waktu_mulai_mixing) }}">

                                                    @error('waktu_mulai_mixing')

                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>

                                                    @enderror

                                                </div>


                                                <div class="form-group col-md-6">

                                                    <label for="waktu_selesai_mixing"
                                                           class="font-weight-bold">

                                                        <i class="fas fa-stop text-danger mr-1"></i>

                                                        Waktu Selesai Mixing

                                                    </label>

                                                    <input type="text"
                                                           id="waktu_selesai_mixing"
                                                           name="waktu_selesai_mixing"
                                                           class="form-control @error('waktu_selesai_mixing') is-invalid @enderror"
                                                           value="{{ old('waktu_selesai_mixing', $item->waktu_selesai_mixing) }}">

                                                    @error('waktu_selesai_mixing')

                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>

                                                    @enderror

                                                </div>

                                            </div>


                                            {{-- KONDISI HASIL PENGGILINGAN --}}

                                            <div class="form-group">

                                                <label for="kondisi"
                                                       class="font-weight-bold">

                                                    <i class="fas fa-check-circle text-success mr-1"></i>

                                                    Kondisi Hasil Penggilingan

                                                    <span class="text-danger">
                                                        *
                                                    </span>

                                                </label>

                                                <select name="kondisi"
                                                        id="kondisi"
                                                        class="form-control @error('kondisi') is-invalid @enderror"
                                                        required>

                                                    <option value="">
                                                        -- Pilih Kondisi --
                                                    </option>

                                                    <option value="✔"
                                                        {{ old('kondisi', $item->kondisi) == '✔' ? 'selected' : '' }}>

                                                        ✔ OK

                                                    </option>

                                                    <option value="✘"
                                                        {{ old('kondisi', $item->kondisi) == '✘' ? 'selected' : '' }}>

                                                        ✘ Tidak OK

                                                    </option>

                                                </select>

                                                @error('kondisi')

                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>

                                                @enderror

                                            </div>


                                            {{-- KONDISI HASIL PENCAMPURAN --}}

                                            <div class="form-group">

                                                <label for="kondisi_hasil_pencampuran"
                                                       class="font-weight-bold">

                                                    <i class="fas fa-check-circle text-success mr-1"></i>

                                                    Kondisi Hasil Pencampuran

                                                </label>

                                                <select name="kondisi_hasil_pencampuran"
                                                        id="kondisi_hasil_pencampuran"
                                                        class="form-control @error('kondisi_hasil_pencampuran') is-invalid @enderror">

                                                    <option value="">
                                                        -- Pilih Kondisi --
                                                    </option>

                                                    <option value="✔"
                                                        {{ old('kondisi_hasil_pencampuran', $item->kondisi_hasil_pencampuran) == '✔' ? 'selected' : '' }}>

                                                        ✔ OK

                                                    </option>

                                                    <option value="✘"
                                                        {{ old('kondisi_hasil_pencampuran', $item->kondisi_hasil_pencampuran) == '✘' ? 'selected' : '' }}>

                                                        ✘ Tidak OK

                                                    </option>

                                                </select>

                                                @error('kondisi_hasil_pencampuran')

                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>

                                                @enderror

                                            </div>


                                            {{-- KODE PRODUKSI EMULSI OIL --}}

                                            <div class="form-group">

                                                <label>
                                                    Kode Produksi Emulsi Oil
                                                </label>

                                                <div id="kode-emulsi-oil-container">

                                                    @if(
                                                        is_array($item->kode_produksi_emulsi_oil)
                                                        &&
                                                        count($item->kode_produksi_emulsi_oil) > 0
                                                    )

                                                        @foreach(
                                                            $item->kode_produksi_emulsi_oil
                                                            as $index => $kode
                                                        )

                                                            <div class="input-group mb-2 kode-emulsi-oil-item">

                                                                <input type="text"
                                                                       name="kode_produksi_emulsi_oil[]"
                                                                       class="form-control"
                                                                       value="{{ old('kode_produksi_emulsi_oil.'.$index, $kode) }}"
                                                                       placeholder="Masukkan kode produksi emulsi oil">

                                                                <div class="input-group-append">

                                                                    <button type="button"
                                                                            class="btn btn-success btn-sm add-kode-emulsi-oil">

                                                                        <i class="fas fa-plus"></i>

                                                                    </button>

                                                                    <button type="button"
                                                                            class="btn btn-danger btn-sm remove-kode-emulsi-oil">

                                                                        <i class="fas fa-minus"></i>

                                                                    </button>

                                                                </div>

                                                            </div>

                                                        @endforeach

                                                    @else

                                                        <div class="input-group mb-2 kode-emulsi-oil-item">

                                                            <input type="text"
                                                                   name="kode_produksi_emulsi_oil[]"
                                                                   class="form-control"
                                                                   value="{{ old(
                                                                       'kode_produksi_emulsi_oil.0',
                                                                       is_string($item->kode_produksi_emulsi_oil)
                                                                           ? $item->kode_produksi_emulsi_oil
                                                                           : ''
                                                                   ) }}"
                                                                   placeholder="Masukkan kode produksi emulsi oil">

                                                            <div class="input-group-append">

                                                                <button type="button"
                                                                        class="btn btn-success btn-sm add-kode-emulsi-oil">

                                                                    <i class="fas fa-plus"></i>

                                                                </button>

                                                                <button type="button"
                                                                        class="btn btn-danger btn-sm remove-kode-emulsi-oil">

                                                                    <i class="fas fa-minus"></i>

                                                                </button>

                                                            </div>

                                                        </div>

                                                    @endif

                                                </div>

                                            </div>

                                        </div>


                                        {{-- =================================================
                                             KOLOM KANAN
                                             ================================================= --}}

                                        <div class="col-md-6">


                                            {{-- PRODUK REWORK --}}

                                            <div class="form-group">

                                                <label for="produk_rework_id_select"
                                                       class="font-weight-bold">

                                                    <i class="fas fa-boxes text-info mr-1"></i>

                                                    Produk Rework <small class="text-muted font-weight-normal">(opsional)</small>

                                                </label>

                                                <select name="produk_rework_id"
                                                        id="produk_rework_id_select"
                                                        class="form-control"
                                                        data-selected="{{ old('produk_rework_id', $item->produk_rework_id ?? '') }}">
                                                    <option value="">- Pilih Produk Rework -</option>
                                                </select>

                                                <small class="text-muted">Opsi bergantung pada Nama Produk yang dipilih</small>

                                            </div>


                                            {{-- REWORK --}}

                                            <div class="form-group">

                                                <label for="rework"
                                                       class="font-weight-bold">

                                                    <i class="fas fa-recycle text-info mr-1"></i>

                                                    Rework

                                                </label>

                                                <input type="text"
                                                       id="rework"
                                                       name="rework"
                                                       class="form-control @error('rework') is-invalid @enderror"
                                                       value="{{ old('rework', $item->rework) }}"
                                                       placeholder="Masukkan rework">

                                                @error('rework')

                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>

                                                @enderror

                                            </div>


                                            {{-- SHIFT --}}

                                            <div class="form-group">

                                                <label for="shift_id"
                                                       class="font-weight-bold">

                                                    <i class="fas fa-clock text-primary mr-1"></i>

                                                    Shift

                                                    <span class="text-danger">
                                                        *
                                                    </span>

                                                </label>

                                                <select name="shift_id"
                                                        id="shift_id"
                                                        class="form-control @error('shift_id') is-invalid @enderror"
                                                        required>

                                                    <option value="">
                                                        -- Pilih Shift --
                                                    </option>

                                                    @foreach($shifts as $shift)

                                                        <option value="{{ $shift->id }}"
                                                            {{ old('shift_id', $item->shift_id) == $shift->id ? 'selected' : '' }}>

                                                            {{ $shift->shift }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                                @error('shift_id')

                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>

                                                @enderror

                                            </div>


                                            {{-- JAM --}}

                                            <div class="form-group">

                                                <label for="jam"
                                                       class="font-weight-bold">

                                                    <i class="fas fa-clock text-primary mr-1"></i>

                                                    Jam

                                                    <span class="text-danger">
                                                        *
                                                    </span>

                                                </label>

                                                @php

                                                    $jamValue = old('jam', $item->jam);

                                                    if ($jamValue) {

                                                        try {

                                                            $jamValue = \Carbon\Carbon::parse($jamValue)
                                                                ->format('H:i');

                                                        } catch (\Exception $e) {

                                                            // Biarkan nilai awal jika parsing gagal

                                                        }

                                                    }

                                                @endphp

                                                <input type="time"
                                                       class="form-control @error('jam') is-invalid @enderror"
                                                       name="jam"
                                                       value="{{ $jamValue }}"
                                                       required>

                                                @error('jam')

                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>

                                                @enderror

                                            </div>


                                            {{-- TANGGAL --}}

                                            <div class="form-group">

                                                <label for="tanggal"
                                                       class="font-weight-bold">

                                                    <i class="fas fa-calendar-alt text-success mr-1"></i>

                                                    Tanggal

                                                    <span class="text-danger">
                                                        *
                                                    </span>

                                                </label>

                                                <input type="date"
                                                       id="tanggal"
                                                       name="tanggal"
                                                       class="form-control @error('tanggal') is-invalid @enderror"
                                                       value="{{ old(
                                                           'tanggal',
                                                           $item->tanggal
                                                               ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d')
                                                               : ''
                                                       ) }}">

                                                @error('tanggal')

                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>

                                                @enderror

                                            </div>


                                            {{-- SUHU AKTUAL --}}

                                            <div class="form-group">

                                                <label class="font-weight-bold">

                                                    <i class="fas fa-thermometer-three-quarters text-danger mr-1"></i>

                                                    Suhu Aktual

                                                </label>

                                                <div class="row">

                                                    @php
                                                        $akt = $item->aktualSuhuAdonan;
                                                    @endphp

                                                    @for($i = 1; $i <= 5; $i++)

                                                        @php
                                                            $field = 'aktual_suhu_' . $i;
                                                        @endphp

                                                        <div class="col-md-4 mb-2">

                                                            <input type="number"
                                                                   step="0.1"
                                                                   name="aktual_suhu_{{ $i }}"
                                                                   class="form-control"
                                                                   placeholder="Aktual {{ $i }}"
                                                                   value="{{ old(
                                                                       'aktual_suhu_'.$i,
                                                                       $akt->$field ?? ''
                                                                   ) }}"
                                                                   id="aktual_suhu_{{ $i }}">

                                                        </div>

                                                    @endfor


                                                    <div class="col-md-4 mb-2">

                                                        <label>
                                                            Total/Rata-rata Suhu Aktual
                                                        </label>

                                                        <input type="number"
                                                               step="0.01"
                                                               name="total_aktual_suhu"
                                                               class="form-control"
                                                               id="total_aktual_suhu"
                                                               placeholder="Otomatis terisi"
                                                               value="{{ old(
                                                                   'total_aktual_suhu',
                                                                   $akt->total_aktual_suhu ?? ''
                                                               ) }}"
                                                               readonly>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>


                                    {{-- =================================================
                                         CATATAN
                                         ================================================= --}}

                                    <div class="row">

                                        <div class="col-md-12">

                                            <div class="form-group">

                                                <label for="catatan"
                                                       class="font-weight-bold">

                                                    <i class="fas fa-sticky-note text-warning mr-1"></i>

                                                    Catatan

                                                </label>

                                                <textarea name="catatan"
                                                          id="catatan"
                                                          class="form-control @error('catatan') is-invalid @enderror"
                                                          rows="3"
                                                          placeholder="Masukkan catatan tambahan...">{{ old('catatan', $item->catatan) }}</textarea>

                                                @error('catatan')

                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>

                                                @enderror

                                            </div>

                                        </div>

                                    </div>

                                </div>


                                {{-- =====================================================
                                     DETAIL BAHAN FORMING
                                     ===================================================== --}}

                                <div class="card-footer">

                                    <div class="row">

                                        <div class="col-md-12">

                                            <h5 class="mb-3">

                                                <i class="fas fa-thermometer-half text-danger mr-2"></i>

                                                Detail Bahan Forming & Suhu

                                            </h5>


                                            <div class="table-responsive">

                                                <table class="table table-bordered table-striped"
                                                       id="tabel-bahan-forming-edit">


                                                    <thead class="bg-primary text-white">

                                                        <tr>

                                                            <th width="5%"
                                                                class="text-center">

                                                                <i class="fas fa-hashtag mr-1"></i>
                                                                No

                                                            </th>

                                                            <th width="15%">

                                                                <i class="fas fa-list-ol mr-1"></i>
                                                                Nomor Formula

                                                            </th>

                                                            <th width="18%">

                                                                <i class="fas fa-flask mr-1"></i>
                                                                Nama RM

                                                            </th>

                                                            <th width="12%"
                                                                class="text-center">

                                                                <i class="fas fa-weight mr-1"></i>
                                                                Berat RM

                                                            </th>

                                                            <th width="16%"
                                                                class="text-center">

                                                                <i class="fas fa-barcode mr-1"></i>
                                                                Kode Produksi Bahan

                                                            </th>

                                                            <th width="12%"
                                                                class="text-center">

                                                                <i class="fas fa-thermometer-half mr-1"></i>
                                                                Suhu RM (°C)

                                                            </th>

                                                            <th width="8%"
                                                                class="text-center">

                                                                Sensori

                                                            </th>

                                                            <th width="10%"
                                                                class="text-center">

                                                                Keterangan

                                                            </th>

                                                            <th width="4%"
                                                                class="text-center">

                                                                Aksi

                                                            </th>

                                                        </tr>

                                                    </thead>


                                                    <tbody id="tabel-bahan-forming-body">

                                                        @forelse($item->suhuForming ?? [] as $idx => $suhu)

                                                            @php
                                                                $bahan = $suhu->bahanForming;
                                                                $beratRm = optional($bahan)->berat_rm ?? 0;
                                                                $namaRm = optional($bahan)->nama_rm ?? '-';
                                                                $nomorFormula = optional(optional($bahan)->formula)->nomor_formula ?? '-';
                                                            @endphp

                                                            <tr data-berat-rm="{{ $beratRm }}">

                                                                {{-- NO --}}

                                                                <td class="text-center align-middle nomor-bahan">

                                                                    <span class="badge badge-secondary">
                                                                        {{ $idx + 1 }}
                                                                    </span>

                                                                </td>


                                                                {{-- NOMOR FORMULA --}}

                                                                <td class="align-middle">

                                                                    {{ $nomorFormula }}

                                                                </td>


                                                                {{-- NAMA RM --}}

                                                                <td class="align-middle">

                                                                    <div class="d-flex align-items-center">

                                                                        <i class="fas fa-vial text-success mr-2"></i>

                                                                        <strong>
                                                                            {{ $namaRm }}
                                                                        </strong>

                                                                    </div>


                                                                    <input type="hidden"
                                                                           name="id_suhu_forming[]"
                                                                           value="{{ $suhu->id }}">


                                                                    <input type="hidden"
                                                                           name="id_bahan_forming[]"
                                                                           value="{{ $suhu->id_bahan_forming }}">

                                                                </td>


                                                                {{-- BERAT RM --}}

                                                                <td class="text-center align-middle berat-rm-val">

                                                                    {{ $beratRm }}

                                                                </td>


                                                                {{-- KODE PRODUKSI BAHAN --}}

                                                                <td>

                                                                    <div class="input-group">

                                                                        <input type="text"
                                                                               name="kode_produksi_bahan[]"
                                                                               class="form-control text-center @error('kode_produksi_bahan.' . $idx) is-invalid @enderror"
                                                                               value="{{ old(
                                                                                   'kode_produksi_bahan.' . $idx,
                                                                                   $suhu->kode_produksi_bahan
                                                                               ) }}"
                                                                               placeholder="Kode Produksi Bahan"
                                                                               required>

                                                                        @error('kode_produksi_bahan.' . $idx)

                                                                            <div class="invalid-feedback">
                                                                                {{ $message }}
                                                                            </div>

                                                                        @enderror

                                                                    </div>

                                                                </td>


                                                                {{-- SUHU RM --}}

                                                                <td>

                                                                    <div class="input-group">

                                                                        <input type="text"
                                                                               name="suhu[]"
                                                                               class="form-control text-center @error('suhu.' . $idx) is-invalid @enderror"
                                                                               value="{{ old(
                                                                                   'suhu.' . $idx,
                                                                                   $suhu->suhu
                                                                               ) }}"
                                                                               placeholder="0"
                                                                               step="0.1"
                                                                               required>

                                                                        <div class="input-group-append">

                                                                            <span class="input-group-text">
                                                                                °C
                                                                            </span>

                                                                        </div>

                                                                        @error('suhu.' . $idx)

                                                                            <div class="invalid-feedback">
                                                                                {{ $message }}
                                                                            </div>

                                                                        @enderror

                                                                    </div>

                                                                </td>


                                                                {{-- SENSORI --}}

                                                                <td>

                                                                    <select name="sensori[]"
                                                                            class="form-control">

                                                                        <option value="OK"
                                                                            {{ old(
                                                                                'sensori.'.$idx,
                                                                                $suhu->sensori
                                                                            ) == 'OK' ? 'selected' : '' }}>

                                                                            OK

                                                                        </option>

                                                                        <option value="Tidak OK"
                                                                            {{ old(
                                                                                'sensori.'.$idx,
                                                                                $suhu->sensori
                                                                            ) == 'Tidak OK' ? 'selected' : '' }}>

                                                                            Tidak OK

                                                                        </option>

                                                                    </select>

                                                                </td>


                                                                {{-- KETERANGAN --}}

                                                                <td>

                                                                    <input type="text"
                                                                           name="keterangan_bahan[]"
                                                                           class="form-control"
                                                                           value="{{ old(
                                                                               'keterangan_bahan.'.$idx,
                                                                               $suhu->keterangan
                                                                           ) }}"
                                                                           placeholder="Keterangan">

                                                                </td>


                                                                {{-- AKSI --}}

                                                                <td class="text-center align-middle">

                                                                    <button type="button"
                                                                            class="btn btn-danger btn-sm hapus-baris-bahan"
                                                                            title="Hapus Bahan">

                                                                        <i class="fas fa-trash"></i>

                                                                    </button>

                                                                </td>

                                                            </tr>

                                                        @empty

                                                            <tr class="empty-row">

                                                                <td colspan="9"
                                                                    class="text-center text-muted py-4">

                                                                    <i class="fas fa-inbox fa-2x mb-2"></i>

                                                                    <p class="mb-0">
                                                                        Tidak ada data bahan forming
                                                                    </p>

                                                                </td>

                                                            </tr>

                                                        @endforelse

                                                    </tbody>


                                                    {{-- =================================================
                                                         FOOTER TOTAL BERAT
                                                         ================================================= --}}

                                                    <tfoot class="bg-light font-weight-bold"
                                                           id="tfoot-bahan-forming">

                                                        <tr>

                                                            <td colspan="3"
                                                                class="text-right">

                                                                Total Berat RM:

                                                            </td>

                                                            <td class="text-center text-primary"
                                                                id="total-berat-rm-edit">

                                                                {{ number_format($totalBeratEdit, 3, '.', '') }}

                                                            </td>

                                                            <td colspan="5"></td>

                                                        </tr>


                                                        <tr>

                                                            <td colspan="3"
                                                                class="text-right">

                                                                Rata-Rata Berat RM:

                                                            </td>

                                                            <td class="text-center text-success"
                                                                id="avg-berat-rm-edit">

                                                                {{ number_format($avgBeratEdit, 3, '.', '') }}

                                                            </td>

                                                            <td colspan="5"></td>

                                                        </tr>

                                                    </tfoot>

                                                </table>

                                            </div>


                                            {{-- =================================================
                                                 BUTTON
                                                 ================================================= --}}

                                            <div class="mt-4 text-center">

                                                <button type="submit"
                                                        class="btn btn-warning btn-md">

                                                    <i class="fas fa-save mr-2"></i>

                                                    Update Data

                                                </button>


                                                <a href="{{ route('persiapan-bahan-forming.index') }}"
                                                   class="btn btn-secondary btn-md ml-2">

                                                    <i class="fas fa-arrow-left mr-2"></i>

                                                    Kembali

                                                </a>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </section>

    </div>
</div>


{{-- ============================================================
     JAVASCRIPT
     ============================================================ --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    function parseNumber(value) {

        if (value === null || value === undefined || value === '') {
            return 0;
        }

        let str = String(value)
            .trim()
            .replace(',', '.');

        let number = parseFloat(str);

        return isNaN(number) ? 0 : number;
    }


    /*
    |--------------------------------------------------------------------------
    | HITUNG RATA-RATA SUHU AKTUAL
    |--------------------------------------------------------------------------
    */

    function hitungTotalSuhuAktual() {

        let total = 0;
        let count = 0;


        for (let i = 1; i <= 5; i++) {

            const inputElement =
                document.getElementById('aktual_suhu_' + i);


            if (
                inputElement &&
                inputElement.value !== ''
            ) {

                const val =
                    parseFloat(inputElement.value);


                if (!isNaN(val)) {

                    total += val;

                    count++;

                }

            }

        }


        const avg =
            count > 0
                ? total / count
                : 0;


        const totalElement =
            document.getElementById('total_aktual_suhu');


        if (totalElement) {

            totalElement.value =
                avg.toFixed(2);

        }

    }


    /*
    |--------------------------------------------------------------------------
    | EVENT SUHU AKTUAL
    |--------------------------------------------------------------------------
    */

    for (let i = 1; i <= 5; i++) {

        const inputElement =
            document.getElementById('aktual_suhu_' + i);


        if (inputElement) {

            inputElement.addEventListener(
                'input',
                hitungTotalSuhuAktual
            );

            inputElement.addEventListener(
                'change',
                hitungTotalSuhuAktual
            );

        }

    }


    hitungTotalSuhuAktual();


    /*
    |--------------------------------------------------------------------------
    | KODE PRODUKSI EMULSI OIL
    |--------------------------------------------------------------------------
    */

    const kodeEmulsiOilContainer =
        document.getElementById(
            'kode-emulsi-oil-container'
        );


    if (kodeEmulsiOilContainer) {


        kodeEmulsiOilContainer.addEventListener(
            'click',
            function (e) {


                /*
                | ADD
                */

                const addButton =
                    e.target.closest(
                        '.add-kode-emulsi-oil'
                    );


                if (addButton) {

                    const firstItem =
                        kodeEmulsiOilContainer.querySelector(
                            '.kode-emulsi-oil-item'
                        );


                    if (!firstItem) {
                        return;
                    }


                    const newItem =
                        firstItem.cloneNode(true);


                    const input =
                        newItem.querySelector('input');


                    if (input) {
                        input.value = '';
                    }


                    kodeEmulsiOilContainer.appendChild(
                        newItem
                    );


                    updateRemoveButtonVisibility();

                }


                /*
                | REMOVE
                */

                const removeButton =
                    e.target.closest(
                        '.remove-kode-emulsi-oil'
                    );


                if (removeButton) {

                    const items =
                        kodeEmulsiOilContainer.querySelectorAll(
                            '.kode-emulsi-oil-item'
                        );


                    if (items.length > 1) {

                        const item =
                            removeButton.closest(
                                '.kode-emulsi-oil-item'
                            );


                        if (item) {
                            item.remove();
                        }

                    }


                    updateRemoveButtonVisibility();

                }

            }
        );


        function updateRemoveButtonVisibility() {

            const items =
                kodeEmulsiOilContainer.querySelectorAll(
                    '.kode-emulsi-oil-item'
                );


            const removeButtons =
                kodeEmulsiOilContainer.querySelectorAll(
                    '.remove-kode-emulsi-oil'
                );


            removeButtons.forEach(function (btn) {

                if (items.length <= 1) {

                    btn.style.display = 'none';

                } else {

                    btn.style.display = 'inline-block';

                }

            });

        }


        updateRemoveButtonVisibility();

    }


    /*
    |--------------------------------------------------------------------------
    | ELEMENT BAHAN FORMING
    |--------------------------------------------------------------------------
    */

    const produkSelect =
        document.getElementById(
            'id_produk_select'
        );


    const formulaSelect =
        document.getElementById(
            'id_formula_select'
        );


    const suhuSelect =
        document.getElementById(
            'id_suhu_adonan'
        );


    const tabelBahanBody =
        document.getElementById(
            'tabel-bahan-forming-body'
        );


    /*
    |--------------------------------------------------------------------------
    | UPDATE NOMOR URUT BAHAN
    |--------------------------------------------------------------------------
    */

    function updateNomorBahan() {

        if (!tabelBahanBody) {
            return;
        }


        const rows =
            tabelBahanBody.querySelectorAll(
                'tr:not(.empty-row)'
            );


        rows.forEach(function (row, index) {

            const nomor =
                row.querySelector(
                    '.nomor-bahan'
                );


            if (nomor) {

                nomor.innerHTML =
                    '<span class="badge badge-secondary">' +
                    (index + 1) +
                    '</span>';

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | HITUNG TOTAL BERAT RM
    |--------------------------------------------------------------------------
    */

    function hitungTotalBeratRM() {

        if (!tabelBahanBody) {
            return;
        }


        let total = 0;
        let count = 0;


        const rows =
            tabelBahanBody.querySelectorAll(
                'tr:not(.empty-row)'
            );


        rows.forEach(function (row) {

            const beratCell =
                row.querySelector(
                    '.berat-rm-val'
                );


            if (!beratCell) {
                return;
            }


            const berat =
                parseNumber(
                    beratCell.textContent
                );


            total += berat;

            count++;

        });


        const average =
            count > 0
                ? total / count
                : 0;


        const totalElement =
            document.getElementById(
                'total-berat-rm-edit'
            );


        const averageElement =
            document.getElementById(
                'avg-berat-rm-edit'
            );


        if (totalElement) {

            totalElement.textContent =
                total.toFixed(3);

        }


        if (averageElement) {

            averageElement.textContent =
                average.toFixed(3);

        }

    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS BARIS BAHAN
    |--------------------------------------------------------------------------
    */

    if (tabelBahanBody) {

        tabelBahanBody.addEventListener(
            'click',
            function (e) {

                const button =
                    e.target.closest(
                        '.hapus-baris-bahan'
                    );


                if (!button) {
                    return;
                }


                const row =
                    button.closest('tr');


                if (row) {

                    row.remove();

                }


                /*
                | Jika tidak ada data
                */

                const rows =
                    tabelBahanBody.querySelectorAll(
                        'tr:not(.empty-row)'
                    );


                if (rows.length === 0) {

                    tabelBahanBody.innerHTML = `
                        <tr class="empty-row">
                            <td colspan="9"
                                class="text-center text-muted py-4">

                                <i class="fas fa-inbox fa-2x mb-2"></i>

                                <p class="mb-0">
                                    Tidak ada data bahan forming
                                </p>

                            </td>
                        </tr>
                    `;

                }


                updateNomorBahan();

                hitungTotalBeratRM();

            }

        );

    }


    /*
    |--------------------------------------------------------------------------
    | LOAD FORMULA BERDASARKAN PRODUK
    |--------------------------------------------------------------------------
    */

    async function loadFormulaByProduk(idProduk) {

        if (!formulaSelect) {
            return;
        }


        const currentFormulaId =
            "{{ $item->id_formula }}";


        const oldFormulaId =
            "{{ old('id_formula') }}";


        const $select =
            window.jQuery
                ? window.jQuery(formulaSelect)
                : null;


        const previousValue =
            $select
                ? $select.val()
                : formulaSelect.value;


        try {

            const res =
                await fetch(
                    "{{ url('/qc-sistem/get-formula-by-produk') }}/" +
                    encodeURIComponent(idProduk),
                    {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                );


            if (!res.ok) {

                throw new Error(
                    'HTTP error ' + res.status
                );

            }


            const data =
                await res.json();


            let html =
                '<option value="">Pilih Nomor Formula</option>';


            let targetId =
                oldFormulaId ||
                currentFormulaId ||
                previousValue;


            (data || []).forEach(function (item) {

                const isSelected =
                    String(item.id) ===
                    String(targetId);


                html +=
                    '<option value="' +
                    item.id +
                    '"' +
                    (isSelected
                        ? ' selected'
                        : '') +
                    '>' +
                    (item.nomor_formula ?? item.id) +
                    '</option>';

            });


            if (
                $select &&
                $select.data('select2')
            ) {

                $select
                    .html(html)
                    .trigger('change');

            } else {

                formulaSelect.innerHTML =
                    html;

                formulaSelect.dispatchEvent(
                    new Event('change')
                );

            }


        } catch (e) {

            console.error(
                'Gagal memuat formula:',
                e
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | LOAD SUHU ADONAN
    |--------------------------------------------------------------------------
    */

    async function loadSuhuAdonanByProduk(idProduk) {

        if (!suhuSelect) {
            return;
        }


        const currentSuhuId =
            "{{ $item->id_suhu_adonan }}";


        const oldSuhuId =
            "{{ old('id_suhu_adonan') }}";


        const $select =
            window.jQuery
                ? window.jQuery(suhuSelect)
                : null;


        const previousValue =
            $select
                ? $select.val()
                : suhuSelect.value;


        try {

            const res =
                await fetch(
                    "{{ url('/qc-sistem/get-suhu-adonan-by-produk') }}/" +
                    encodeURIComponent(idProduk),
                    {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                );


            if (!res.ok) {

                throw new Error(
                    'HTTP error ' + res.status
                );

            }


            const data =
                await res.json();


            let html =
                '<option value="">-- Pilih Suhu Adonan --</option>';


            let targetId =
                oldSuhuId ||
                currentSuhuId ||
                previousValue;


            (data || []).forEach(function (item) {

                const isSelected =
                    String(item.id) ===
                    String(targetId);


                const text =
                    'STD: ' +
                    (
                        item.std_suhu ??
                        item.suhu ??
                        item.id
                    );


                html +=
                    '<option value="' +
                    item.id +
                    '"' +
                    (isSelected
                        ? ' selected'
                        : '') +
                    '>' +
                    text +
                    '</option>';

            });


            if (
                $select &&
                $select.data('select2')
            ) {

                $select
                    .html(html)
                    .trigger('change');

            } else {

                suhuSelect.innerHTML =
                    html;

                suhuSelect.dispatchEvent(
                    new Event('change')
                );

            }


        } catch (e) {

            console.error(
                'Gagal memuat suhu adonan:',
                e
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | LOAD BAHAN BERDASARKAN FORMULA
    |--------------------------------------------------------------------------
    */

    async function loadBahanByFormula(idFormula) {

        if (!tabelBahanBody) {
            return;
        }


        tabelBahanBody.innerHTML = `
            <tr class="loading-row">
                <td colspan="9"
                    class="text-center">

                    <i class="fas fa-spinner fa-spin mr-2"></i>

                    Memuat data bahan...

                </td>
            </tr>
        `;


        if (!idFormula) {

            tabelBahanBody.innerHTML = `
                <tr class="empty-row">
                    <td colspan="9"
                        class="text-center text-muted">

                        Pilih Nomor Formula terlebih dahulu

                    </td>
                </tr>
            `;


            hitungTotalBeratRM();

            return;

        }


        try {

            const res =
                await fetch(
                    "{{ url('/qc-sistem/ajax/bahan-forming-by-formula') }}/" +
                    encodeURIComponent(idFormula),
                    {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                );


            if (!res.ok) {

                throw new Error(
                    'HTTP error ' + res.status
                );

            }


            const data =
                await res.json();


            if (
                !data ||
                data.length === 0
            ) {

                tabelBahanBody.innerHTML = `
                    <tr class="empty-row">
                        <td colspan="9"
                            class="text-center text-muted">

                            Tidak ada data bahan
                            untuk formula ini

                        </td>
                    </tr>
                `;


                hitungTotalBeratRM();

                return;

            }


            tabelBahanBody.innerHTML = '';


            data.forEach(function (b, idx) {

                /*
                | Ambil berat RM dari response AJAX.
                | Mendukung beberapa kemungkinan nama field.
                */

                const beratRm =
                    b.berat_rm ??
                    b.beratRM ??
                    b.berat ??
                    0;


                const namaRm =
                    b.nama_rm ??
                    b.namaRM ??
                    '-';


                const nomorFormula =
                    b.nomor_formula ??
                    b.formula?.nomor_formula ??
                    '';


                const idBahan =
                    b.id ??
                    b.id_bahan_forming ??
                    '';


                const row = `

                    <tr data-berat-rm="${beratRm}">

                        <td class="text-center align-middle nomor-bahan">

                            <span class="badge badge-secondary">
                                ${idx + 1}
                            </span>

                        </td>


                        <td class="align-middle">

                            ${nomorFormula}

                        </td>


                        <td class="align-middle">

                            <div class="d-flex align-items-center">

                                <i class="fas fa-vial text-success mr-2"></i>

                                <strong>
                                    ${namaRm}
                                </strong>

                            </div>


                            <input type="hidden"
                                   name="id_bahan_forming[]"
                                   value="${idBahan}">

                        </td>


                        <td class="text-center align-middle berat-rm-val">

                            ${beratRm}

                        </td>


                        <td>

                            <input type="text"
                                   name="kode_produksi_bahan[]"
                                   class="form-control text-center"
                                   placeholder="Kode Produksi Bahan"
                                   required>

                        </td>


                        <td>

                            <div class="input-group">

                                <input type="text"
                                       name="suhu[]"
                                       class="form-control text-center"
                                       placeholder="0"
                                       step="0.1"
                                       required>

                                <div class="input-group-append">

                                    <span class="input-group-text">
                                        °C
                                    </span>

                                </div>

                            </div>

                        </td>


                        <td>

                            <select name="sensori[]"
                                    class="form-control">

                                <option value="OK">
                                    OK
                                </option>

                                <option value="Tidak OK">
                                    Tidak OK
                                </option>

                            </select>

                        </td>


                        <td>

                            <input type="text"
                                   name="keterangan_bahan[]"
                                   class="form-control"
                                   placeholder="Keterangan">

                        </td>


                        <td class="text-center align-middle">

                            <button type="button"
                                    class="btn btn-danger btn-sm hapus-baris-bahan"
                                    title="Hapus Bahan">

                                <i class="fas fa-trash"></i>

                            </button>

                        </td>

                    </tr>

                `;


                tabelBahanBody.insertAdjacentHTML(
                    'beforeend',
                    row
                );

            });


            updateNomorBahan();

            hitungTotalBeratRM();


        } catch (e) {

            console.error(
                'Gagal memuat data bahan:',
                e
            );


            tabelBahanBody.innerHTML = `
                <tr class="empty-row">
                    <td colspan="9"
                        class="text-center text-danger">

                        Gagal memuat data bahan

                    </td>
                </tr>
            `;


            hitungTotalBeratRM();

        }

    }


    /*
    |--------------------------------------------------------------------------
    | CHANGE PRODUK
    |--------------------------------------------------------------------------
    */

    if (produkSelect) {

        produkSelect.addEventListener(
            'change',
            function () {

                const idProduk =
                    this.value;


                if (!idProduk) {

                    return;

                }


                loadFormulaByProduk(
                    idProduk
                );


                loadSuhuAdonanByProduk(
                    idProduk
                );

                loadReworkProdukByProduk(
                    idProduk
                );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | CHANGE FORMULA
    |--------------------------------------------------------------------------
    */

    if (formulaSelect) {

        formulaSelect.addEventListener(
            'change',
            function () {

                const idFormula =
                    this.value;


                const originalFormulaId =
                    "{{ $item->id_formula }}";


                const isInitialLoad =
                    !this.dataset.initialized;


                /*
                | Jangan reload bahan pada initial load
                | jika formula masih formula lama.
                */

                if (
                    !isInitialLoad ||
                    (
                        idFormula &&
                        String(idFormula) !==
                        String(originalFormulaId)
                    )
                ) {

                    loadBahanByFormula(
                        idFormula
                    );

                }


                this.dataset.initialized =
                    "true";

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL LOAD
    |--------------------------------------------------------------------------
    */

    updateNomorBahan();

    hitungTotalBeratRM();


    /*
    |--------------------------------------------------------------------------
    | LOAD DATA PRODUK
    |--------------------------------------------------------------------------
    */

    if (
        produkSelect &&
        produkSelect.value
    ) {

        setTimeout(
            function () {

                Promise.all([
                    loadFormulaByProduk(
                        produkSelect.value
                    ),

                    loadSuhuAdonanByProduk(
                        produkSelect.value
                    ),

                    loadReworkProdukByProduk(
                        produkSelect.value
                    )
                ])
                .then(
                    function () {

                        setTimeout(
                            function () {

                                if (formulaSelect) {

                                    formulaSelect.dataset.initialized =
                                        "true";

                                }

                            },
                            1000
                        );

                    }
                );

            },
            800
        );

    }

    // --- Produk Rework: AJAX load & Select2 (single select) ---
    async function loadReworkProdukByProduk(idProduk) {
        if (!window.jQuery) return;
        const $ = window.jQuery;
        const $el = $('#produk_rework_id_select');
        if (!$el.length) return;

        // Baca nilai yang sudah tersimpan (single integer dari server)
        const previousSelected = parseInt($el.attr('data-selected') || '0', 10) || 0;

        // Reset opsi, selalu ada opsi kosong di awal
        $el.empty().append(new Option('- Pilih Produk Rework -', '', false, false));

        if (!idProduk) {
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.trigger('change');
            }
            return;
        }

        try {
            const res = await fetch(
                "{{ url('/qc-sistem/ajax/rework-produk-by-produk') }}/" + encodeURIComponent(idProduk),
                { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
            );
            if (!res.ok) return;
            const data = await res.json();

            (data || []).forEach(function(p) {
                const isSelected = previousSelected > 0 && Number(p.id) === previousSelected;
                $el.append(new Option(p.nama_produk, p.id, isSelected, isSelected));
            });
        } catch (e) { /* ignore */ }

        // Notify Select2 agar re-render dengan opsi baru
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.trigger('change');
        }
    }

    // Init Select2 untuk produk rework (single select with search)
    if (window.jQuery) {
        window.jQuery('#produk_rework_id_select').select2({
            theme: 'bootstrap4',
            placeholder: '- Pilih Produk Rework -',
            allowClear: true,
            width: '100%',
        });
    }

});

</script>


@endsection