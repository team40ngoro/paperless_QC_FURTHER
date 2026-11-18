{{-- filepath: resources/views/qc-sistem/persiapan_bahan_forming/edit.blade.php --}}

@extends('layouts.app')

@section('container')

@php
    /*
    |--------------------------------------------------------------------------
    | IDENTIFIKASI ROLE & AKSES PENGGUNA
    |--------------------------------------------------------------------------
    */
    $currentUser = auth()->user();
    $isSuperadmin = $currentUser->role === 'superadmin' || in_array((int)($currentUser->id_role ?? 0), [1, 5], true);
    $isQcInspector = !$isSuperadmin && ($currentUser->isQcInspector() || (int)($currentUser->id_role ?? 0) === 3 || $currentUser->isQc());
    $isProduksi = !$isSuperadmin && !$isQcInspector;

    /*
    |--------------------------------------------------------------------------
    | PERHITUNGAN DATA BERAT RM
    |--------------------------------------------------------------------------
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
    $avgBeratEdit = $beratListEdit->count() > 0 ? $beratListEdit->avg() : 0;

    /*
    |--------------------------------------------------------------------------
    | PARSING DATA KODE EMULSI OIL & REWORK
    |--------------------------------------------------------------------------
    */
    $rawEmulsiOil = $item->kode_produksi_emulsi_oil;
    $emulsiOilList = [];
    if (is_array($rawEmulsiOil)) {
        $emulsiOilList = array_values(array_filter($rawEmulsiOil, fn($v) => !is_null($v) && trim((string)$v) !== ''));
    } elseif (!empty($rawEmulsiOil)) {
        $emulsiOilList = [(string)$rawEmulsiOil];
    }
    if (empty($emulsiOilList)) {
        $emulsiOilList = [''];
    }

    $reworkList = [];
    $rawRework = $item->rework;
    if (is_array($rawRework)) {
        $reworkList = array_values($rawRework);
    } elseif (!empty($rawRework)) {
        $reworkList = array_map('trim', explode(',', (string)$rawRework));
    }

    $produkReworkList = [];
    $rawProdukRework = $item->produk_rework_id;
    if (is_array($rawProdukRework)) {
        $produkReworkList = array_values($rawProdukRework);
    } elseif (!empty($rawProdukRework)) {
        $produkReworkList = [(int)$rawProdukRework];
    }
    $maxReworkRows = max(count($reworkList), count($produkReworkList), 1);
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
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('persiapan-bahan-forming.index') }}">
                                    Verifikasi Persiapan Cold Mixing
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Edit Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                {{-- ALERT SUCCESS --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- ALERT ERROR --}}
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- ROLE NOTIFICATION BANNER --}}
                @if($isQcInspector)
                    <div class="alert alert-info shadow-sm mb-4 border-left border-info" style="border-left-width: 5px !important;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-shield fa-2x mr-3 text-info"></i>
                            <div>
                                <h5 class="alert-heading font-weight-bold mb-1">
                                    <span class="badge badge-info mr-1">ROLE: QC INSPEKTOR</span> Mode Verifikasi QC
                                </h5>
                                <p class="mb-0">
                                    Sebagai QC Inspektor, hak akses edit Anda dikhususkan untuk mengisi & memverifikasi:
                                    <strong>1. Verifikasi hasil Persiapan Cold Mixing (✔ / ✘)</strong>,
                                    <strong>2. Hasil Pencampuran (✔ / ✘)</strong>, dan
                                    <strong>3. Suhu Actual (1 - 5)</strong>.
                                    Data persiapan milik Tim Produksi dikunci (read-only).
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($isProduksi)
                    <div class="alert alert-primary shadow-sm mb-4 border-left border-primary" style="border-left-width: 5px !important;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-industry fa-2x mr-3 text-primary"></i>
                            <div>
                                <h5 class="alert-heading font-weight-bold mb-1">
                                    <span class="badge badge-primary mr-1">ROLE: PRODUKSI</span> Mode Edit Data Persiapan
                                </h5>
                                <p class="mb-0">
                                    Anda dapat memperbarui 12 Detail Verifikasi Persiapan Cold Mixing dan Data Bahan Forming. Kolom verifikasi QC dikunci dan hanya dapat diisi oleh QC Inspektor.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-secondary shadow-sm mb-4 border-left border-dark" style="border-left-width: 5px !important;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-cog fa-2x mr-3 text-dark"></i>
                            <div>
                                <h5 class="alert-heading font-weight-bold mb-1">
                                    <span class="badge badge-dark mr-1">SUPERADMIN</span> Akses Penuh
                                </h5>
                                <p class="mb-0">
                                    Anda memiliki hak akses untuk mengubah seluruh kolom data Tim Produksi maupun Verifikasi Tim QC.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('persiapan-bahan-forming.update', $item->uuid) }}" method="POST" id="form-edit-cold-mixing">
                    @csrf
                    @method('PUT')

                    {{-- ========================================================
                         BAGIAN 1: DETAIL PERSIAPAN COLD MIXING (1. DIBUAT OLEH: PRODUKSI)
                         ======================================================== --}}
                    <div class="card card-outline {{ $isQcInspector ? 'card-secondary' : 'card-primary' }} shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold mb-0">
                                    <i class="fas fa-industry text-primary mr-2"></i>
                                    1. Detail Verifikasi Persiapan Cold Mixing (Produksi)
                                </h3>
                                @if($isQcInspector)
                                    <span class="badge badge-secondary"><i class="fas fa-lock mr-1"></i> Terkunci untuk QC</span>
                                @else
                                    <span class="badge badge-primary"><i class="fas fa-edit mr-1"></i> Dapat Diedit oleh Produksi</span>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                {{-- 1. INPUT TANGGAL PRODUKSI --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal" class="font-weight-bold">
                                            <i class="fas fa-calendar-alt text-success mr-1"></i>
                                            1. Input Tanggal Produksi <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                               id="tanggal"
                                               name="tanggal"
                                               class="form-control @error('tanggal') is-invalid @enderror"
                                               value="{{ old('tanggal', $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : '') }}"
                                               {{ $isQcInspector ? 'readonly disabled' : 'required' }}>
                                        @error('tanggal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- 2. INPUT SHIFT (PAGI, SORE, MALAM) --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="shift_id" class="font-weight-bold">
                                            <i class="fas fa-clock text-primary mr-1"></i>
                                            2. Input Shift <span class="text-danger">*</span>
                                        </label>
                                        <select name="shift_id"
                                                id="shift_id"
                                                class="form-control @error('shift_id') is-invalid @enderror"
                                                {{ $isQcInspector ? 'disabled' : 'required' }}>
                                            <option value="">-- Pilih Shift (Pagi, Sore, Malam) --</option>
                                            @foreach($shifts as $shift)
                                                @php
                                                    $sText = trim($shift->shift);
                                                    $displayShift = $sText;
                                                    if ($sText === '1' || strcasecmp($sText, 'shift 1') === 0) {
                                                        $displayShift = 'Pagi (Shift 1)';
                                                    } elseif ($sText === '2' || strcasecmp($sText, 'shift 2') === 0) {
                                                        $displayShift = 'Sore (Shift 2)';
                                                    } elseif ($sText === '3' || strcasecmp($sText, 'shift 3') === 0) {
                                                        $displayShift = 'Malam (Shift 3)';
                                                    }
                                                @endphp
                                                <option value="{{ $shift->id }}" {{ old('shift_id', $item->shift_id) == $shift->id ? 'selected' : '' }}>
                                                    {{ $displayShift }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('shift_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- 3. INPUT JAM PRODUKSI --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jam" class="font-weight-bold">
                                            <i class="fas fa-stopwatch text-info mr-1"></i>
                                            3. Input Jam Produksi <span class="text-danger">*</span>
                                        </label>
                                        @php
                                            $jamValue = old('jam', $item->jam);
                                            if ($jamValue) {
                                                try {
                                                    $jamValue = \Carbon\Carbon::parse($jamValue)->format('H:i');
                                                } catch (\Exception $e) {}
                                            }
                                        @endphp
                                        <input type="time"
                                               id="jam"
                                               name="jam"
                                               class="form-control @error('jam') is-invalid @enderror"
                                               value="{{ $jamValue }}"
                                               {{ $isQcInspector ? 'readonly disabled' : 'required' }}>
                                        @error('jam')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- 4. INPUT NAMA PRODUK --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_produk_select" class="font-weight-bold">
                                            <i class="fas fa-box text-primary mr-1"></i>
                                            4. Input Nama Produk <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_produk"
                                                id="id_produk_select"
                                                class="form-control @error('id_produk') is-invalid @enderror"
                                                {{ $isQcInspector ? 'disabled' : 'required' }}>
                                            <option value="">-- Pilih Nama Produk --</option>
                                            @foreach($jenis_produk as $produk)
                                                <option value="{{ $produk->id }}"
                                                    data-status-bahan="{{ strtolower($produk->status_bahan ?? '') }}"
                                                    {{ old('id_produk', $item->formula->id_produk ?? null) == $produk->id ? 'selected' : '' }}>
                                                    {{ $produk->nama_produk }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_produk')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- 5. INPUT NOMOR FORMULASI --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_formula_select" class="font-weight-bold">
                                            <i class="fas fa-list-ol text-info mr-1"></i>
                                            5. Input Nomor Formulasi <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_formula"
                                                id="id_formula_select"
                                                class="form-control @error('id_formula') is-invalid @enderror"
                                                {{ $isQcInspector ? 'disabled' : 'required' }}>
                                            <option value="{{ $item->id_formula }}" selected>
                                                {{ $item->formula->nomor_formula ?? '-' }}
                                            </option>
                                        </select>
                                        @error('id_formula')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- 6. INPUT GRAMASI --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nilai_select_berat_sampling" class="font-weight-bold">
                                            <i class="fas fa-weight mr-1"></i>
                                            6. Input Gramasi (gram)
                                        </label>
                                        <select name="berat_sampling"
                                                id="nilai_select_berat_sampling"
                                                class="form-control"
                                                data-nilai-berat
                                                data-selected="{{ old('berat_sampling', $item->berat_sampling) }}"
                                                {{ $isQcInspector ? 'disabled' : '' }}>
                                            <option value="">-- Pilih Gramasi --</option>
                                            @if($item->berat_sampling)
                                                <option value="{{ $item->berat_sampling }}" selected>{{ $item->berat_sampling }} g</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                {{-- 7. INPUT KODE PRODUKSI PRODUK --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kode_produksi_emulsi" class="font-weight-bold">
                                            <i class="fas fa-barcode text-primary mr-1"></i>
                                            7. Input Kode Produksi Produk
                                        </label>
                                        <input type="text"
                                               id="kode_produksi_emulsi"
                                               name="kode_produksi_emulsi"
                                               class="form-control @error('kode_produksi_emulsi') is-invalid @enderror"
                                               value="{{ old('kode_produksi_emulsi', $item->kode_produksi_emulsi) }}"
                                               placeholder="Masukkan kode produksi produk"
                                               {{ $isQcInspector ? 'readonly disabled' : '' }}>
                                        @error('kode_produksi_emulsi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- 8. INPUT KODE PRODUKSI EMULSI OIL --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">
                                            <i class="fas fa-oil-can text-warning mr-1"></i>
                                            8. Input Kode Produksi Emulsi Oil
                                        </label>
                                        <div id="kode-emulsi-oil-container">
                                            @foreach($emulsiOilList as $idx => $kodeOil)
                                                <div class="input-group mb-2 kode-emulsi-oil-item">
                                                    <input type="text"
                                                           name="kode_produksi_emulsi_oil[]"
                                                           class="form-control"
                                                           value="{{ old('kode_produksi_emulsi_oil.'.$idx, $kodeOil) }}"
                                                           placeholder="Masukkan kode produksi emulsi oil"
                                                           {{ $isQcInspector ? 'readonly disabled' : '' }}>
                                                    @if(!$isQcInspector)
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-success btn-sm add-kode-emulsi-oil" title="Tambah Baris">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm remove-kode-emulsi-oil"
                                                                    style="{{ count($emulsiOilList) <= 1 ? 'display: none;' : '' }}" title="Hapus Baris">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        @if(!$isQcInspector)
                                            <small class="text-muted">Klik tombol (+) untuk menambah baris kode produksi emulsi oil.</small>
                                        @endif
                                    </div>
                                </div>

                                {{-- 9. INPUT WAKTU MULAI & 10. INPUT WAKTU SELESAI MIXING --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="waktu_mulai_mixing" class="font-weight-bold">
                                            <i class="fas fa-play text-success mr-1"></i>
                                            9. Input Waktu Mulai
                                        </label>
                                        <input type="time"
                                               id="waktu_mulai_mixing"
                                               name="waktu_mulai_mixing"
                                               class="form-control @error('waktu_mulai_mixing') is-invalid @enderror"
                                               value="{{ old('waktu_mulai_mixing', $item->waktu_mulai_mixing) }}"
                                               {{ $isQcInspector ? 'readonly disabled' : '' }}>
                                        @error('waktu_mulai_mixing')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="waktu_selesai_mixing" class="font-weight-bold">
                                            <i class="fas fa-stop text-danger mr-1"></i>
                                            10. Input Waktu Selesai Mixing
                                        </label>
                                        <input type="time"
                                               id="waktu_selesai_mixing"
                                               name="waktu_selesai_mixing"
                                               class="form-control @error('waktu_selesai_mixing') is-invalid @enderror"
                                               value="{{ old('waktu_selesai_mixing', $item->waktu_selesai_mixing) }}"
                                               {{ $isQcInspector ? 'readonly disabled' : '' }}>
                                        @error('waktu_selesai_mixing')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- 11. INPUT REWORK (KOLOM DAPAT DITAMBAH) --}}
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold">
                                            <i class="fas fa-recycle text-success mr-1"></i>
                                            11. Input Rework <small class="text-muted font-weight-normal">(kolom dapat ditambah)</small>
                                        </label>
                                        <div id="rework-container">
                                            @for($r = 0; $r < $maxReworkRows; $r++)
                                                @php
                                                    $curProdukId = $produkReworkList[$r] ?? null;
                                                    $curReworkVal = $reworkList[$r] ?? null;
                                                @endphp
                                                <div class="row mb-2 rework-item align-items-center">
                                                    <div class="col-md-6 mb-1">
                                                        <label class="small text-muted mb-0">Pilihan Produk Rework</label>
                                                        <select name="produk_rework_id[]" class="form-control select-rework-item" {{ $isQcInspector ? 'disabled' : '' }}>
                                                            <option value="">- Pilih Produk Rework -</option>
                                                            @foreach($jenis_produk as $p)
                                                                <option value="{{ $p->id }}" {{ $curProdukId == $p->id ? 'selected' : '' }}>
                                                                    {{ $p->nama_produk }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 mb-1">
                                                        <label class="small text-muted mb-0">Jumlah Rework (kg)</label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" name="rework[]" class="form-control" placeholder="0.00"
                                                                   value="{{ old('rework.'.$r, $curReworkVal) }}"
                                                                   {{ $isQcInspector ? 'readonly disabled' : '' }}>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">kg</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if(!$isQcInspector)
                                                        <div class="col-md-2 mb-1 pt-3">
                                                            <button type="button" class="btn btn-success btn-sm add-rework" title="Tambah Baris">
                                                                <i class="fas fa-plus mr-1"></i> Tambah
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm remove-rework"
                                                                    style="{{ $maxReworkRows <= 1 ? 'display: none;' : '' }}" title="Hapus Baris">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endfor
                                        </div>
                                        @if(!$isQcInspector)
                                            <small class="text-muted">Klik tombol (+) Tambah untuk menambah baris rework jika terdapat lebih dari 1 produk rework.</small>
                                        @endif
                                    </div>
                                </div>

                                {{-- 12. INPUT KETERANGAN --}}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="catatan" class="font-weight-bold">
                                            <i class="fas fa-sticky-note text-warning mr-1"></i>
                                            12. Input Keterangan
                                        </label>
                                        <textarea name="catatan"
                                                  id="catatan"
                                                  class="form-control @error('catatan') is-invalid @enderror"
                                                  rows="3"
                                                  placeholder="Masukkan keterangan (opsional)"
                                                  {{ $isQcInspector ? 'readonly disabled' : '' }}>{{ old('catatan', $item->catatan) }}</textarea>
                                        @error('catatan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- SUHU ADONAN STANDAR (Hidden input) --}}
                                <div class="form-group" style="display: none;">
                                    <select name="id_suhu_adonan" id="id_suhu_adonan">
                                        <option value="{{ $item->id_suhu_adonan }}" selected></option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        {{-- TABEL BAHAN FORMING & SUHU --}}
                        <div class="card-footer bg-white border-top">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="mb-3 font-weight-bold text-dark">
                                        <i class="fas fa-thermometer-half text-danger mr-2"></i>
                                        Detail Bahan Forming & Suhu Berdasarkan Formula
                                    </h5>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="tabel-bahan-forming-edit">
                                            <thead class="bg-primary text-white">
                                                <tr>
                                                    <th width="5%" class="text-center"><i class="fas fa-hashtag mr-1"></i> No</th>
                                                    <th width="15%"><i class="fas fa-list-ol mr-1"></i> Nomor Formula</th>
                                                    <th width="18%"><i class="fas fa-flask mr-1"></i> Nama RM</th>
                                                    <th width="12%" class="text-center"><i class="fas fa-weight mr-1"></i> Berat RM</th>
                                                    <th width="16%" class="text-center"><i class="fas fa-barcode mr-1"></i> Kode Produksi Bahan</th>
                                                    <th width="12%" class="text-center"><i class="fas fa-thermometer-half mr-1"></i> Suhu RM (°C)</th>
                                                    <th width="10%" class="text-center">Sensori</th>
                                                    <th width="10%" class="text-center">Keterangan</th>
                                                    @if(!$isQcInspector)
                                                        <th width="4%" class="text-center">Aksi</th>
                                                    @endif
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
                                                        <td class="text-center align-middle nomor-bahan">
                                                            <span class="badge badge-secondary">{{ $idx + 1 }}</span>
                                                        </td>
                                                        <td class="align-middle">{{ $nomorFormula }}</td>
                                                        <td class="align-middle">
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-vial text-success mr-2"></i>
                                                                <strong>{{ $namaRm }}</strong>
                                                            </div>
                                                            <input type="hidden" name="id_suhu_forming[]" value="{{ $suhu->id }}">
                                                            <input type="hidden" name="id_bahan_forming[]" value="{{ $suhu->id_bahan_forming }}">
                                                        </td>
                                                        <td class="text-center align-middle berat-rm-val">{{ $beratRm }}</td>
                                                        <td>
                                                            <input type="text"
                                                                   name="kode_produksi_bahan[]"
                                                                   class="form-control text-center"
                                                                   value="{{ old('kode_produksi_bahan.' . $idx, $suhu->kode_produksi_bahan) }}"
                                                                   placeholder="Kode Produksi"
                                                                   {{ $isQcInspector ? 'readonly disabled' : 'required' }}>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="number"
                                                                       step="0.1"
                                                                       name="suhu[]"
                                                                       class="form-control text-center"
                                                                       value="{{ old('suhu.' . $idx, $suhu->suhu) }}"
                                                                       placeholder="0.0"
                                                                       {{ $isQcInspector ? 'readonly disabled' : 'required' }}>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">°C</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <select name="sensori[]" class="form-control" {{ $isQcInspector ? 'disabled' : '' }}>
                                                                <option value="OK" {{ old('sensori.'.$idx, $suhu->sensori) == 'OK' ? 'selected' : '' }}>OK</option>
                                                                <option value="Tidak OK" {{ old('sensori.'.$idx, $suhu->sensori) == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                   name="keterangan_bahan[]"
                                                                   class="form-control"
                                                                   value="{{ old('keterangan_bahan.'.$idx, $suhu->keterangan) }}"
                                                                   placeholder="Keterangan"
                                                                   {{ $isQcInspector ? 'readonly disabled' : '' }}>
                                                        </td>
                                                        @if(!$isQcInspector)
                                                            <td class="text-center align-middle">
                                                                <button type="button" class="btn btn-danger btn-sm hapus-baris-bahan" title="Hapus Bahan">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @empty
                                                    <tr class="empty-row">
                                                        <td colspan="{{ $isQcInspector ? 8 : 9 }}" class="text-center text-muted py-4">
                                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                                            <p class="mb-0">Tidak ada data bahan forming</p>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot class="bg-light font-weight-bold" id="tfoot-bahan-forming">
                                                <tr>
                                                    <td colspan="3" class="text-right">Total Berat RM:</td>
                                                    <td class="text-center text-primary" id="total-berat-rm-edit">
                                                        {{ number_format($totalBeratEdit, 3, '.', '') }}
                                                    </td>
                                                    <td colspan="{{ $isQcInspector ? 4 : 5 }}"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="text-right">Rata-Rata Berat RM:</td>
                                                    <td class="text-center text-success" id="avg-berat-rm-edit">
                                                        {{ number_format($avgBeratEdit, 3, '.', '') }}
                                                    </td>
                                                    <td colspan="{{ $isQcInspector ? 4 : 5 }}"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ========================================================
                         BAGIAN 2: VERIFIKASI QC (2. VERIFIKASI = QC INSPEKTOR)
                         ======================================================== --}}
                    <div class="card card-outline {{ $isQcInspector ? 'card-success' : 'card-secondary' }} shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold mb-0">
                                    <i class="fas fa-clipboard-check text-success mr-2"></i>
                                    2. Verifikasi Hasil Cold Mixing (QC Inspektor)
                                </h3>
                                @if($isQcInspector)
                                    <span class="badge badge-success"><i class="fas fa-edit mr-1"></i> Terbuka untuk QC Inspektor</span>
                                @else
                                    <span class="badge badge-secondary"><i class="fas fa-lock mr-1"></i> Terkunci untuk Produksi</span>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                {{-- VERIFIKASI DARI HASIL VERIFIKASI PERSIAPAN COLD MIXING : (✔ / ✘) --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kondisi" class="font-weight-bold">
                                            <i class="fas fa-check-double text-success mr-1"></i>
                                            Verifikasi dari hasil Verifikasi Persiapan Cold Mixing (✔ / ✘) <span class="text-danger">*</span>
                                        </label>
                                        <select name="kondisi"
                                                id="kondisi"
                                                class="form-control @error('kondisi') is-invalid @enderror"
                                                {{ $isProduksi ? 'disabled' : 'required' }}>
                                            <option value="">-- Pilih Kondisi (✔ / ✘) --</option>
                                            <option value="✔" {{ old('kondisi', $item->kondisi) == '✔' ? 'selected' : '' }}>
                                                ✔ OK
                                            </option>
                                            <option value="✘" {{ old('kondisi', $item->kondisi) == '✘' ? 'selected' : '' }}>
                                                ✘ Tidak OK
                                            </option>
                                        </select>
                                        @error('kondisi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Status kelayakan hasil persiapan cold mixing (gilingan).</small>
                                    </div>
                                </div>

                                {{-- HASIL PENCAMPURAN : (✔ / ✘) --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kondisi_hasil_pencampuran" class="font-weight-bold">
                                            <i class="fas fa-blender text-success mr-1"></i>
                                            Hasil Pencampuran (✔ / ✘) <span class="text-danger">*</span>
                                        </label>
                                        <select name="kondisi_hasil_pencampuran"
                                                id="kondisi_hasil_pencampuran"
                                                class="form-control @error('kondisi_hasil_pencampuran') is-invalid @enderror"
                                                {{ $isProduksi ? 'disabled' : 'required' }}>
                                            <option value="">-- Pilih Hasil Pencampuran (✔ / ✘) --</option>
                                            <option value="✔" {{ old('kondisi_hasil_pencampuran', $item->kondisi_hasil_pencampuran) == '✔' ? 'selected' : '' }}>
                                                ✔ OK
                                            </option>
                                            <option value="✘" {{ old('kondisi_hasil_pencampuran', $item->kondisi_hasil_pencampuran) == '✘' ? 'selected' : '' }}>
                                                ✘ Tidak OK
                                            </option>
                                        </select>
                                        @error('kondisi_hasil_pencampuran')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Kondisi kerataan dan homogenitas hasil pencampuran.</small>
                                    </div>
                                </div>

                                {{-- SUHU ACTUAL : (ANGKA 1 - 5) & RATA-RATA --}}
                                <div class="col-md-12 mt-2">
                                    <div class="card bg-light border-0">
                                        <div class="card-body p-3">
                                            <label class="font-weight-bold text-danger mb-2">
                                                <i class="fas fa-thermometer-three-quarters mr-1"></i>
                                                Input Suhu Actual (°C)
                                            </label>
                                            <p class="text-muted small mb-3">Masukkan nilai suhu actual adonan hasil mixing (angka 1 s/d 5). Rata-rata akan dihitung otomatis.</p>

                                            @php
                                                $akt = $item->aktualSuhuAdonan;
                                            @endphp

                                            <div class="row">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @php $field = 'aktual_suhu_' . $i; @endphp
                                                    <div class="col-md-2 col-6 mb-2">
                                                        <label class="small text-muted font-weight-bold mb-1">Suhu Actual {{ $i }}</label>
                                                        <div class="input-group">
                                                            <input type="number"
                                                                   step="0.1"
                                                                   name="aktual_suhu_{{ $i }}"
                                                                   class="form-control text-center suhu-actual-input"
                                                                   placeholder="0.0"
                                                                   value="{{ old('aktual_suhu_'.$i, $akt->$field ?? '') }}"
                                                                   id="aktual_suhu_{{ $i }}"
                                                                   {{ $isProduksi ? 'readonly disabled' : '' }}>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">°C</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endfor

                                                <div class="col-md-2 col-12 mb-2">
                                                    <label class="small text-muted font-weight-bold mb-1">Rata-Rata Suhu</label>
                                                    <div class="input-group">
                                                        <input type="number"
                                                               step="0.01"
                                                               name="total_aktual_suhu"
                                                               class="form-control text-center font-weight-bold bg-success text-white"
                                                               id="total_aktual_suhu"
                                                               placeholder="0.00"
                                                               value="{{ old('total_aktual_suhu', $akt->total_aktual_suhu ?? '') }}"
                                                               readonly>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text bg-success text-white">°C</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- ========================================================
                         TOMBOL AKSI SIMPAN & KEMBALI
                         ======================================================== --}}
                    <div class="card shadow-sm mb-5">
                        <div class="card-body text-center py-3">
                            @if($isQcInspector)
                                <button type="submit" class="btn btn-success btn-lg shadow-sm px-4">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Simpan Verifikasi QC (QC Inspektor)
                                </button>
                            @elseif($isProduksi)
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm px-4">
                                    <i class="fas fa-save mr-2"></i>
                                    Update Data Persiapan (Produksi)
                                </button>
                            @else
                                <button type="submit" class="btn btn-warning btn-lg shadow-sm px-4">
                                    <i class="fas fa-save mr-2"></i>
                                    Update Semua Data
                                </button>
                            @endif

                            <a href="{{ route('persiapan-bahan-forming.index') }}" class="btn btn-secondary btn-lg shadow-sm px-4 ml-2">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali
                            </a>
                        </div>
                    </div>

                </form>

            </div>
        </section>

    </div>
</div>

{{-- ============================================================
     JAVASCRIPT LOGIC
     ============================================================ --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const isQc = {{ $isQcInspector ? 'true' : 'false' }};

    function parseNumber(value) {
        if (value === null || value === undefined || value === '') return 0;
        let str = String(value).trim().replace(',', '.');
        let number = parseFloat(str);
        return isNaN(number) ? 0 : number;
    }

    /*
    |--------------------------------------------------------------------------
    | HITUNG RATA-RATA SUHU AKTUAL (QC)
    |--------------------------------------------------------------------------
    */
    function hitungTotalSuhuAktual() {
        let total = 0;
        let count = 0;

        for (let i = 1; i <= 5; i++) {
            const input = document.getElementById('aktual_suhu_' + i);
            if (input && input.value !== '') {
                const val = parseFloat(input.value);
                if (!isNaN(val)) {
                    total += val;
                    count++;
                }
            }
        }

        const avgInput = document.getElementById('total_aktual_suhu');
        if (avgInput) {
            avgInput.value = count > 0 ? (total / count).toFixed(2) : '';
        }
    }

    const actualInputs = document.querySelectorAll('.suhu-actual-input');
    actualInputs.forEach(input => {
        input.addEventListener('input', hitungTotalSuhuAktual);
        input.addEventListener('change', hitungTotalSuhuAktual);
    });

    /*
    |--------------------------------------------------------------------------
    | KODE PRODUKSI EMULSI OIL (DYNAMIC ROWS)
    |--------------------------------------------------------------------------
    */
    const oilContainer = document.getElementById('kode-emulsi-oil-container');
    if (oilContainer && !isQc) {
        oilContainer.addEventListener('click', function(e) {
            const addBtn = e.target.closest('.add-kode-emulsi-oil');
            if (addBtn) {
                const firstItem = oilContainer.querySelector('.kode-emulsi-oil-item');
                if (firstItem) {
                    const newItem = firstItem.cloneNode(true);
                    const inp = newItem.querySelector('input');
                    if (inp) inp.value = '';
                    oilContainer.appendChild(newItem);
                    updateOilRemoveButtons();
                }
            }

            const removeBtn = e.target.closest('.remove-kode-emulsi-oil');
            if (removeBtn) {
                const items = oilContainer.querySelectorAll('.kode-emulsi-oil-item');
                if (items.length > 1) {
                    removeBtn.closest('.kode-emulsi-oil-item').remove();
                    updateOilRemoveButtons();
                }
            }
        });

        function updateOilRemoveButtons() {
            const items = oilContainer.querySelectorAll('.kode-emulsi-oil-item');
            const btns = oilContainer.querySelectorAll('.remove-kode-emulsi-oil');
            btns.forEach(btn => {
                btn.style.display = items.length <= 1 ? 'none' : 'inline-block';
            });
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REWORK (DYNAMIC ROWS)
    |--------------------------------------------------------------------------
    */
    const reworkContainer = document.getElementById('rework-container');
    if (reworkContainer && !isQc) {
        reworkContainer.addEventListener('click', function(e) {
            const addBtn = e.target.closest('.add-rework');
            if (addBtn) {
                const firstItem = reworkContainer.querySelector('.rework-item');
                if (firstItem) {
                    const newItem = firstItem.cloneNode(true);
                    const sel = newItem.querySelector('select');
                    const inp = newItem.querySelector('input[type="number"]');
                    if (sel) sel.selectedIndex = 0;
                    if (inp) inp.value = '';
                    reworkContainer.appendChild(newItem);
                    updateReworkRemoveButtons();
                }
            }

            const removeBtn = e.target.closest('.remove-rework');
            if (removeBtn) {
                const items = reworkContainer.querySelectorAll('.rework-item');
                if (items.length > 1) {
                    removeBtn.closest('.rework-item').remove();
                    updateReworkRemoveButtons();
                }
            }
        });

        function updateReworkRemoveButtons() {
            const items = reworkContainer.querySelectorAll('.rework-item');
            const btns = reworkContainer.querySelectorAll('.remove-rework');
            btns.forEach(btn => {
                btn.style.display = items.length <= 1 ? 'none' : 'inline-block';
            });
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TABEL BAHAN FORMING: HITUNG TOTAL BERAT & HAPUS BARIS
    |--------------------------------------------------------------------------
    */
    const tabelBahanBody = document.getElementById('tabel-bahan-forming-body');

    function updateNomorBahan() {
        if (!tabelBahanBody) return;
        const rows = tabelBahanBody.querySelectorAll('tr:not(.empty-row)');
        rows.forEach((row, index) => {
            const nomor = row.querySelector('.nomor-bahan');
            if (nomor) {
                nomor.innerHTML = '<span class="badge badge-secondary">' + (index + 1) + '</span>';
            }
        });
    }

    function hitungTotalBeratRM() {
        if (!tabelBahanBody) return;
        let total = 0;
        let count = 0;
        const rows = tabelBahanBody.querySelectorAll('tr:not(.empty-row)');
        rows.forEach(row => {
            const beratCell = row.querySelector('.berat-rm-val');
            if (beratCell) {
                total += parseNumber(beratCell.textContent);
                count++;
            }
        });

        const totalEl = document.getElementById('total-berat-rm-edit');
        const avgEl = document.getElementById('avg-berat-rm-edit');
        if (totalEl) totalEl.textContent = total.toFixed(3);
        if (avgEl) avgEl.textContent = (count > 0 ? (total / count) : 0).toFixed(3);
    }

    if (tabelBahanBody && !isQc) {
        tabelBahanBody.addEventListener('click', function(e) {
            const hapusBtn = e.target.closest('.hapus-baris-bahan');
            if (hapusBtn) {
                const tr = hapusBtn.closest('tr');
                if (tr) {
                    tr.remove();
                    updateNomorBahan();
                    hitungTotalBeratRM();
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX: FORMULA, SUHU, GRAMASI BERDASARKAN PRODUK (JIKA PRODUKSI / SUPERADMIN)
    |--------------------------------------------------------------------------
    */
    const produkSelect = document.getElementById('id_produk_select');
    const formulaSelect = document.getElementById('id_formula_select');
    const gramasiSelect = document.getElementById('nilai_select_berat_sampling');

    if (produkSelect && !isQc) {
        produkSelect.addEventListener('change', async function() {
            const idProduk = this.value;
            if (!idProduk) return;

            // Load Formula
            try {
                const res = await fetch("{{ url('/qc-sistem/ajax/nomor-formula-by-produk') }}/" + encodeURIComponent(idProduk), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (formulaSelect) {
                        formulaSelect.innerHTML = '<option value="">-- Pilih Nomor Formulasi --</option>';
                        (data || []).forEach(f => {
                            const opt = document.createElement('option');
                            opt.value = f.id;
                            opt.textContent = f.nomor_formula;
                            formulaSelect.appendChild(opt);
                        });
                    }
                }
            } catch (e) {}

            // Load Suhu Adonan
            try {
                const resSuhu = await fetch("{{ url('/qc-sistem/get-suhu-adonan-by-produk') }}/" + encodeURIComponent(idProduk), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (resSuhu.ok) {
                    const dataSuhu = await resSuhu.json();
                    const saSelect = document.getElementById('id_suhu_adonan');
                    if (saSelect && dataSuhu.length > 0) {
                        saSelect.innerHTML = '<option value="' + dataSuhu[0].id + '" selected></option>';
                    }
                }
            } catch (e) {}
        });
    }

    if (formulaSelect && !isQc) {
        formulaSelect.addEventListener('change', async function() {
            const idFormula = this.value;
            if (!idFormula) return;

            try {
                const res = await fetch("{{ url('/qc-sistem/ajax/bahan-forming-by-formula') }}/" + encodeURIComponent(idFormula), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (tabelBahanBody) {
                        tabelBahanBody.innerHTML = '';
                        if (data && data.length > 0) {
                            data.forEach((b, idx) => {
                                const tr = document.createElement('tr');
                                tr.setAttribute('data-berat-rm', b.berat_rm || 0);
                                tr.innerHTML = `
                                    <td class="text-center align-middle nomor-bahan"><span class="badge badge-secondary">${idx + 1}</span></td>
                                    <td class="align-middle">${b.formula ? (b.formula.nomor_formula || '-') : '-'}</td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-vial text-success mr-2"></i>
                                            <strong>${b.nama_rm || '-'}</strong>
                                        </div>
                                        <input type="hidden" name="id_bahan_forming[]" value="${b.id}">
                                    </td>
                                    <td class="text-center align-middle berat-rm-val">${b.berat_rm || 0}</td>
                                    <td>
                                        <input type="text" name="kode_produksi_bahan[]" class="form-control text-center" placeholder="Kode Produksi" required>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" step="0.1" name="suhu[]" class="form-control text-center" placeholder="0.0" required>
                                            <div class="input-group-append"><span class="input-group-text">°C</span></div>
                                        </div>
                                    </td>
                                    <td>
                                        <select name="sensori[]" class="form-control">
                                            <option value="OK" selected>OK</option>
                                            <option value="Tidak OK">Tidak OK</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="keterangan_bahan[]" class="form-control" placeholder="Keterangan">
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-danger btn-sm hapus-baris-bahan" title="Hapus Bahan"><i class="fas fa-trash"></i></button>
                                    </td>
                                `;
                                tabelBahanBody.appendChild(tr);
                            });
                        } else {
                            tabelBahanBody.innerHTML = `
                                <tr class="empty-row">
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p class="mb-0">Tidak ada data bahan forming untuk formula ini</p>
                                    </td>
                                </tr>
                            `;
                        }
                        updateNomorBahan();
                        hitungTotalBeratRM();
                    }
                }
            } catch (e) {}
        });
    }

});
</script>

@endsection
