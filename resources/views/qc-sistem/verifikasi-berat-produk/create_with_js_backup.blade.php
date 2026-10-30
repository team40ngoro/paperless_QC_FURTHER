@extends('layouts.app')

@section('title', 'Tambah Data Verifikasi Berat Produk')

@section('container')
<!-- Content Header (Page header) -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Data Verifikasi Berat Produk</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('verifikasi-berat-produk.index') }}">Data Verifikasi Berat Produk</a></li>
                            <li class="breadcrumb-item active">Tambah Data</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Tambah Data Verifikasi Berat Produk</h3>
                                <div class="card-tools">
                                    <a href="{{ route('verifikasi-berat-produk.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>
                            <form action="{{ route('verifikasi-berat-produk.store') }}" method="POST">
                                @csrf
                                <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="shift_id">Shift <span class="text-danger">*</span></label>
                                    <select name="shift_id" id="shift_id" class="form-control @error('shift_id') is-invalid @enderror" required>
                                        <option value="">Pilih Shift</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->shift }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shift_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                    <input type="datetime-local" 
                                           name="tanggal" 
                                           id="tanggal" 
                                           class="form-control @error('tanggal') is-invalid @enderror" 
                                           value="{{ old('tanggal') }}" 
                                           required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="id_produk">Produk <span class="text-danger">*</span></label>
                                    <select name="id_produk" id="id_produk" class="form-control @error('id_produk') is-invalid @enderror" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ old('id_produk') == $produk->id ? 'selected' : '' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_produk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kode_produksi">Kode Produksi <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="kode_produksi" 
                                           id="kode_produksi" 
                                           class="form-control @error('kode_produksi') is-invalid @enderror" 
                                           value="{{ old('kode_produksi') }}" 
                                           required>
                                    @error('kode_produksi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis_produk_kfc">Jenis Produk <span class="text-danger">*</span></label>
                                    <select name="jenis_produk_kfc" id="jenis_produk_kfc" class="form-control @error('jenis_produk_kfc') is-invalid @enderror" required>
                                        <option value="">Pilih Jenis Produk</option>
                                        <option value="KFC" {{ old('jenis_produk_kfc') == 'KFC' ? 'selected' : '' }}>KFC</option>
                                        <option value="non-KFC" {{ old('jenis_produk_kfc') == 'non-KFC' ? 'selected' : '' }}>non-KFC</option>
                                    </select>
                                    @error('jenis_produk_kfc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Breader Section (For KFC Products) -->
                        <div class="row mt-4" id="breader-section" style="display: none;">
                            <div class="col-12">
                                <div class="card card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">Berat Breader</h3>
                                    </div>
                                    <div class="card-body">
                                        <div id="breader-container">
                                            <div class="breader-entry mb-3">
                                                <div class="input-group">
                                                    <input type="number" step="0.01" name="berat_breader[]" class="form-control breader-input" placeholder="Masukkan berat breader">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-danger remove-breader" style="display: none;">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center mb-3">
                                            <button type="button" class="btn btn-success add-breader">
                                                <i class="fas fa-plus"></i> Tambah Berat Breader
                                            </button>
                                        </div>

                                        <!-- Rata-rata Breader -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="rata_rata_breader">Rata-rata Breader</label>
                                                    <input type="number" step="0.01" name="rata_rata_breader" id="rata_rata_breader" class="form-control" readonly>
                                                    <small class="text-muted">Otomatis dihitung dari berat breader</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <!-- Pickup Breader Field (Hidden by default, shown for KFC after breadering) -->
                                                <div class="form-group" id="pickup-breader-field" style="display: none;">
                                                    <label for="pickup_breader">Pickup Breader</label>
                                                    <input type="number" step="0.01" name="pickup_breader" id="pickup_breader" class="form-control" readonly>
                                                    <small class="text-muted">
                                                        <strong>Rumus Pickup Breader:</strong><br>
                                                        (rata-rata breader-rata-rata batter)/rata-rata breader * 100
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Hitung Pick Up Total Button (Hidden by default, shown for KFC after breadering) -->
                                        <div class="row mt-3" id="pickup-total-breader-section" style="display: none;">
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-warning btn-block" id="btn-hitung-pickup-total-breader">
                                                    <i class="fas fa-calculator"></i> Hitung Pick Up Total
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <!-- Pickup Total Field -->
                                                <div class="form-group" id="pickup-total-breader-field" style="display: none;">
                                                    <label for="pickup_total_breader">Pick Up Total Breader</label>
                                                    <input type="number" step="0.01" name="pickup_total_breader" id="pickup_total_breader" class="form-control" readonly>
                                                    <small class="text-muted">
                                                        <strong>Rumus Pick Up Total Breader:</strong><br>
                                                        (rata-rata breader-rata-rata berat dry)/rata-rata breader*100
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Buttons Ke Fryer 1 dan Ke After Forming -->
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-secondary btn-block" id="btn-breader-ke-fryer-1" disabled>
                                                    <i class="fas fa-arrow-right"></i> Ke Fryer 1
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-info btn-block" id="btn-breader-ke-after-forming">
                                                    <i class="fas fa-arrow-right"></i> Ke After Forming
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- After Forming Section -->
                        <div class="row mt-4" id="after-forming-section" style="display: none;">
                            <div class="col-12">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Berat Sebelum Pemasakan</h3>
                                    </div>
                                    <div class="card-body">
                                        <!-- Regular After Forming Fields (for non-KFC) -->
                                        <div id="regular-after-forming-fields">
                                            <div id="after-forming-container">
                                                <div class="after-forming-entry mb-3">
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" name="after_forming[]" class="form-control after-forming-input" placeholder="Masukkan data after forming">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-danger remove-after-forming" style="display: none;">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="text-center mb-3">
                                                <button type="button" class="btn btn-success add-after-forming">
                                                    <i class="fas fa-plus"></i> Tambah Data After Forming
                                                </button>
                                            </div>

                                            <!-- Rata-rata After Forming -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="rata_rata_after_forming">Rata-rata After Forming</label>
                                                        <input type="number" step="0.01" name="rata_rata_after_forming" id="rata_rata_after_forming" class="form-control" readonly>
                                                        <small class="text-muted">Otomatis dihitung dari data after forming</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- KFC Dry/Wet Fields (for KFC products) -->
                                        <div id="kfc-dry-wet-fields" style="display: none;">
                                            <div class="row">
                                                <!-- Berat Dry KFC -->
                                                <div class="col-md-6">
                                                    <h5>Berat Dry</h5>
                                                    <div id="dry-kfc-container">
                                                        <div class="dry-kfc-entry mb-3">
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" name="berat_dry_kfc[]" class="form-control dry-kfc-input" placeholder="Masukkan berat dry">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-danger remove-dry-kfc" style="display: none;">
                                                                        <i class="fas fa-minus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="text-center mb-3">
                                                        <button type="button" class="btn btn-success add-dry-kfc">
                                                            <i class="fas fa-plus"></i> Tambah Berat Dry
                                                        </button>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="rata_rata_dry_kfc">Rata-rata Dry</label>
                                                        <input type="number" step="0.01" name="rata_rata_dry_kfc" id="rata_rata_dry_kfc" class="form-control" readonly>
                                                        <small class="text-muted">Otomatis dihitung dari berat dry</small>
                                                    </div>
                                                </div>

                                                <!-- Berat Wet KFC -->
                                                <div class="col-md-6">
                                                    <h5>Berat Wet</h5>
                                                    <div id="wet-kfc-container">
                                                        <div class="wet-kfc-entry mb-3">
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" name="berat_wet_kfc[]" class="form-control wet-kfc-input" placeholder="Masukkan berat wet">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-danger remove-wet-kfc" style="display: none;">
                                                                        <i class="fas fa-minus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="text-center mb-3">
                                                        <button type="button" class="btn btn-success add-wet-kfc">
                                                            <i class="fas fa-plus"></i> Tambah Berat Wet
                                                        </button>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="rata_rata_wet_kfc">Rata-rata Wet</label>
                                                        <input type="number" step="0.01" name="rata_rata_wet_kfc" id="rata_rata_wet_kfc" class="form-control" readonly>
                                                        <small class="text-muted">Otomatis dihitung dari berat wet</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Pickup After Forming KFC (Only for KFC products) -->
                                        <div class="row mt-3" id="pickup-after-forming-kfc-field">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pickup_after_forming_kfc">Pickup After Forming KFC</label>
                                                    <input type="number" step="0.01" name="pickup_after_forming_kfc" id="pickup_after_forming_kfc" class="form-control" readonly>
                                                    <small class="text-muted">
                                                        <strong>Rumus Pickup After Forming KFC:</strong><br>
                                                        (Rata-rata Berat Wet - Rata-rata Berat Dry) / Rata-rata Breader × 100
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-warning btn-block" id="btn-ke-predusting">
                                                    <i class="fas fa-arrow-right"></i> Ke Predusting
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-info btn-block" id="btn-ke-battering-after-forming">
                                                    <i class="fas fa-arrow-right"></i> Ke Battering
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Predusting Section (Initially Hidden) -->
                        <div class="row mt-4" id="predusting-section" style="display: none;">
                            <div class="col-12">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">Berat Predusting</h3>
                                    </div>
                                    <div class="card-body">
                                        <div id="predusting-container">
                                            <div class="predusting-entry mb-3">
                                                <div class="input-group">
                                                    <input type="number" step="0.01" name="berat_predusting[]" class="form-control predusting-input" placeholder="Masukkan berat predusting">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-danger remove-predusting" style="display: none;">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center mb-3">
                                            <button type="button" class="btn btn-success add-predusting">
                                                <i class="fas fa-plus"></i> Tambah Berat Predusting
                                            </button>
                                        </div>

                                        <!-- Rata-rata Predusting -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="rata_rata_predusting">Rata-rata Predusting</label>
                                                    <input type="number" step="0.01" name="rata_rata_predusting" id="rata_rata_predusting" class="form-control" readonly>
                                                    <small class="text-muted">Otomatis dihitung dari berat predusting</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pickup_after_forming_predusting">Pickup Predust</label>
                                                    <input type="number" step="0.01" name="pickup_after_forming_predusting" id="pickup_after_forming_predusting" class="form-control" readonly>
                                                    <small class="text-muted">
                                                        <strong>Rumus Pickup Predust:</strong><br>
                                                        <span id="pickup-predust-formula">(Rata-rata Predusting - Rata-rata After Forming) / Rata-rata After Forming × 100</span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Button Ke Battering -->
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-info btn-block" id="btn-ke-battering">
                                                    <i class="fas fa-arrow-right"></i> Ke Battering
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Battering Section (Initially Hidden) -->
                        <div class="row mt-4" id="battering-section" style="display: none;">
                            <div class="col-12">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">Berat Battering</h3>
                                    </div>
                                    <div class="card-body">
                                        <div id="battering-container">
                                            <div class="battering-entry mb-3">
                                                <div class="input-group">
                                                    <input type="number" step="0.01" name="berat_battering[]" class="form-control battering-input" placeholder="Masukkan berat battering">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-danger remove-battering" style="display: none;">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center mb-3">
                                            <button type="button" class="btn btn-success add-battering">
                                                <i class="fas fa-plus"></i> Tambah Berat Battering
                                            </button>
                                        </div>

                                        <!-- Rata-rata Battering -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="rata_rata_battering">Rata-rata Battering</label>
                                                    <input type="number" step="0.01" name="rata_rata_battering" id="rata_rata_battering" class="form-control" readonly>
                                                    <small class="text-muted">Otomatis dihitung dari berat battering</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pickup_after_predusting_battering" id="pickup-battering-label">Pickup After Predusting-Battering</label>
                                                    <input type="number" step="0.01" name="pickup_after_predusting_battering" id="pickup_after_predusting_battering" class="form-control" readonly>
                                                    <small class="text-muted">
                                                        <strong id="pickup-battering-formula-title">Rumus Pickup After Predusting-Battering:</strong><br>
                                                        <span id="pickup-battering-formula">(Rata-rata Battering - Rata-rata Predusting) / Rata-rata Predusting × 100</span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Button Ke Breadering -->
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-primary btn-block" id="btn-ke-breadering">
                                                    <i class="fas fa-arrow-right"></i> Ke Breadering
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Breadering Section (Initially Hidden) -->
                        <div class="row mt-4" id="breadering-section" style="display: none;">
                            <div class="col-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Berat Breadering</h3>
                                    </div>
                                    <div class="card-body">
                                        <div id="breadering-container">
                                            <div class="breadering-entry mb-3">
                                                <div class="input-group">
                                                    <input type="number" step="0.01" name="berat_breadering[]" class="form-control breadering-input" placeholder="Masukkan berat breadering">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-danger remove-breadering" style="display: none;">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center mb-3">
                                            <button type="button" class="btn btn-success add-breadering">
                                                <i class="fas fa-plus"></i> Tambah Berat Breadering
                                            </button>
                                        </div>

                                        <!-- Rata-rata Breadering -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="rata_rata_breadering">Rata-rata Breadering</label>
                                                    <input type="number" step="0.01" name="rata_rata_breadering" id="rata_rata_breadering" class="form-control" readonly>
                                                    <small class="text-muted">Otomatis dihitung dari berat breadering</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pickup_after_battering_breadering">Pickup After Battering-Breadering</label>
                                                    <input type="number" step="0.01" name="pickup_after_battering_breadering" id="pickup_after_battering_breadering" class="form-control" readonly>
                                                    <small class="text-muted">
                                                        <strong>Rumus Pickup After Battering-Breadering:</strong><br>
                                                        (Rata-rata Breadering - Rata-rata Battering) / Rata-rata Battering × 100
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Button Ke Fryer 1 -->
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-danger btn-block" id="btn-ke-fryer-1">
                                                    <i class="fas fa-arrow-right"></i> Ke Fryer 1
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fryer 1 Section (Initially Hidden) -->
                        <div class="row mt-4" id="fryer-1-section" style="display: none;">
                            <div class="col-12">
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <h3 class="card-title">Berat Fryer 1</h3>
                                    </div>
                                    <div class="card-body">
                                        <div id="fryer-1-container">
                                            <div class="fryer-1-entry mb-3">
                                                <div class="input-group">
                                                    <input type="number" step="0.01" name="berat_fryer_1[]" class="form-control fryer-1-input" placeholder="Masukkan berat fryer 1">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-danger remove-fryer-1" style="display: none;">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center mb-3">
                                            <button type="button" class="btn btn-success add-fryer-1">
                                                <i class="fas fa-plus"></i> Tambah Berat Fryer 1
                                            </button>
                                        </div>

                                        <!-- Rata-rata Fryer 1 -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="rata_rata_fryer_1">Rata-rata Fryer 1</label>
                                                    <input type="number" step="0.01" name="rata_rata_fryer_1" id="rata_rata_fryer_1" class="form-control" readonly>
                                                    <small class="text-muted">Otomatis dihitung dari berat fryer 1</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pickup_breadering_fryer_1">Pickup Breadering-Fryer 1</label>
                                                    <input type="number" step="0.01" name="pickup_breadering_fryer_1" id="pickup_breadering_fryer_1" class="form-control" readonly>
                                                    <small class="text-muted">
                                                        <strong>Rumus Pickup Breadering-Fryer 1:</strong><br>
                                                        (Rata-rata Fryer 1 - Rata-rata Breadering) / Rata-rata Breadering × 100
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Buttons Ke Fryer 2 dan Hitung Pick Up Total (Hidden for KFC products) -->
                                        <div class="row mt-3" id="fryer-1-buttons-non-kfc">
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-warning btn-sm btn-block" id="btn-ke-fryer-2">
                                                    <i class="fas fa-arrow-right"></i> Ke Fryer 2
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-info btn-sm btn-block" id="btn-hitung-pickup-total">
                                                    <i class="fas fa-calculator"></i> Hitung Pick Up Total
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Pick Up Total Field (Initially Hidden) -->
                                        <div class="row mt-3" id="pickup-total-field" style="display: none;">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="pickup_total">Pick Up Total</label>
                                                    <input type="number" step="0.01" name="pickup_total" id="pickup_total" class="form-control" readonly>
                                                    <small class="text-muted">
                                                        <strong>Rumus Pick Up Total:</strong><br>
                                                        (Rata-rata Fryer 1 - Rata-rata After Forming) / Rata-rata After Forming × 100
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fryer 2 Section (Initially Hidden) -->
                        <div class="row mt-4" id="fryer-2-section" style="display: none;">
                            <div class="col-12">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">Berat Fryer 2</h3>
                                    </div>
                                    <div class="card-body">
                                        <div id="fryer-2-container">
                                            <div class="fryer-2-entry mb-3">
                                                <div class="input-group">
                                                    <input type="number" step="0.01" name="berat_fryer_2[]" class="form-control fryer-2-input" placeholder="Masukkan berat fryer 2">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-danger remove-fryer-2" style="display: none;">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center mb-3">
                                            <button type="button" class="btn btn-success add-fryer-2">
                                                <i class="fas fa-plus"></i> Tambah Berat Fryer 2
                                            </button>
                                        </div>

                                        <!-- Rata-rata Fryer 2 -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="rata_rata_fryer_2">Rata-rata Fryer 2</label>
                                                    <input type="number" step="0.01" name="rata_rata_fryer_2" id="rata_rata_fryer_2" class="form-control" readonly>
                                                    <small class="text-muted">Otomatis dihitung dari berat fryer 2</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pickup_fryer_1_fryer_2">Pickup Fryer 1-Fryer 2</label>
                                                    <input type="number" step="0.01" name="pickup_fryer_1_fryer_2" id="pickup_fryer_1_fryer_2" class="form-control" readonly>
                                                    <small class="text-muted">
                                                        <strong>Rumus Pickup Fryer 1-Fryer 2:</strong><br>
                                                        (Rata-rata Fryer 2 - Rata-rata Fryer 1) / Rata-rata Fryer 1 × 100
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Button Hitung Pick Up Total Fryer 2 -->
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-info btn-sm btn-block" id="btn-hitung-pickup-total-fryer-2">
                                                    <i class="fas fa-calculator"></i> Hitung Pick Up Total
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Pick Up Total Fryer 2 Field (Initially Hidden) -->
                                        <div class="row mt-3" id="pickup-total-fryer-2-field" style="display: none;">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="pickup_total_fryer_2">Pick Up Total Fryer 2</label>
                                                    <input type="number" step="0.01" name="pickup_total_fryer_2" id="pickup_total_fryer_2" class="form-control" readonly>
                                                    <small class="text-muted">
                                                        <strong>Rumus Pick Up Total Fryer 2:</strong><br>
                                                        (Rata-rata Fryer 2 - Rata-rata After Forming) / Rata-rata After Forming × 100
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
                                    <a href="{{ route('verifikasi-berat-produk.index') }}" class="btn btn-secondary ml-2"><i class="fas fa-arrow-left"></i> Kembali</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
    </div>
    <!-- /.main-panel -->

{{-- JavaScript moved to app.blade.php under "Verifikasi Berat Produk Per Tahapan" section --}}

@endsection
    $(document).on('click', '.add-after-forming', function() {
        const container = $('#after-forming-container');
        const newEntry = `
            <div class="after-forming-entry mb-3">
                <div class="input-group">
                    <input type="number" step="0.01" name="after_forming[]" class="form-control after-forming-input" placeholder="Masukkan data after forming">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-after-forming">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.append(newEntry);
        updateRemoveButtons();
        calculateAfterFormingAverage();
    });

    // Remove After Forming Entry
    $(document).on('click', '.remove-after-forming', function() {
        $(this).closest('.after-forming-entry').remove();
        updateRemoveButtons();
        calculateAfterFormingAverage();
    });

    // Calculate average when input changes
    $(document).on('input', '.after-forming-input', function() {
        calculateAfterFormingAverage();
    });

    // Update remove button visibility
    function updateRemoveButtons() {
        const entries = $('.after-forming-entry');
        if (entries.length <= 1) {
            // Hide all remove buttons if only one entry
            $('.remove-after-forming').hide();
        } else {
            // Show remove buttons for all entries except hide first one if needed
            $('.remove-after-forming').show();
        }
    }

    // Calculate After Forming Average
    function calculateAfterFormingAverage() {
        const inputs = $('.after-forming-input');
        let total = 0;
        let count = 0;

        inputs.each(function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value) && value !== '') {
                total += value;
                count++;
            }
        });

        const average = count > 0 ? (total / count).toFixed(2) : '';
        $('#rata_rata_after_forming').val(average);
        
        // Recalculate pickup after updating after forming average
        calculatePickupAfterFormingPredusting();
        
        // Recalculate pickup for direct after forming to battering if active
        if (window.directAfterFormingToBattering && $('#battering-section').is(':visible')) {
            calculatePickupAfterPredustingBattering();
        }
        
        // Recalculate pickup total if it's active
        if ($('#pickup-total-field').is(':visible')) {
            calculatePickupTotal();
        }
        
        // Recalculate pickup total fryer 2 if it's active
        if ($('#pickup-total-fryer-2-field').is(':visible')) {
            calculatePickupTotalFryer2();
        }
    }

    // Show Predusting Section
    $('#btn-ke-predusting').click(function() {
        $('#predusting-section').slideDown();
        $(this).prop('disabled', true).text('Predusting Aktif');
        
        // Disable the "Ke Battering" button in after forming section
        $('#btn-ke-battering-after-forming').prop('disabled', true).removeClass('btn-info').addClass('btn-secondary').text('Battering (Predusting Aktif)');
    });

    // Show Battering Section from After Forming (Skip Predusting)
    $('#btn-ke-battering-after-forming').click(function() {
        $('#battering-section').slideDown();
        $(this).prop('disabled', true).text('Battering Aktif');
        
        // Disable the "Ke Predusting" button
        $('#btn-ke-predusting').prop('disabled', true).removeClass('btn-warning').addClass('btn-secondary').text('Predusting (Battering Aktif)');
        
        // Change pickup label and formula for direct after forming to battering
        $('#pickup_after_predusting_battering').closest('.form-group').find('label').text('Pickup After Forming-Battering');
        $('#pickup_after_predusting_battering').closest('.form-group').find('small').html('<strong>Rumus Pickup After Forming-Battering:</strong><br>(Rata-rata Battering - Rata-rata After Forming) / Rata-rata After Forming × 100');
        
        // Set flag to indicate direct after forming to battering
        window.directAfterFormingToBattering = true;
    });

    // Add Predusting Entry
    $(document).on('click', '.add-predusting', function() {
        const container = $('#predusting-container');
        const newEntry = `
            <div class="predusting-entry mb-3">
                <div class="input-group">
                    <input type="number" step="0.01" name="berat_predusting[]" class="form-control predusting-input" placeholder="Masukkan berat predusting">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-predusting">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.append(newEntry);
        updatePredustingRemoveButtons();
        calculatePredustingAverage();
    });

    // Remove Predusting Entry
    $(document).on('click', '.remove-predusting', function() {
        $(this).closest('.predusting-entry').remove();
        updatePredustingRemoveButtons();
        calculatePredustingAverage();
    });

    // Calculate predusting average when input changes
    $(document).on('input', '.predusting-input', function() {
        calculatePredustingAverage();
    });

    // Update predusting remove button visibility
    function updatePredustingRemoveButtons() {
        const entries = $('.predusting-entry');
        if (entries.length <= 1) {
            $('.remove-predusting').hide();
        } else {
            $('.remove-predusting').show();
        }
    }

    // Calculate Predusting Average
    function calculatePredustingAverage() {
        const inputs = $('.predusting-input');
        let total = 0;
        let count = 0;

        inputs.each(function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value) && value !== '') {
                total += value;
                count++;
            }
        });

        const average = count > 0 ? (total / count).toFixed(2) : '';
        $('#rata_rata_predusting').val(average);
        
        // Recalculate pickup after updating predusting average
        calculatePickupAfterFormingPredusting();
        
        // Recalculate pickup battering if battering section is visible and KFC workflow is active
        if ($('#battering-section').is(':visible') && window.kfcToAfterForming && $('#kfc-dry-wet-fields').is(':visible')) {
            calculatePickupAfterPredustingBattering();
        }
    }

    // Calculate Pickup After Forming-Predusting
    function calculatePickupAfterFormingPredusting() {
        const rataRataPredusting = parseFloat($('#rata_rata_predusting').val());
        
        // Check if KFC workflow is active
        if (window.kfcToAfterForming && $('#kfc-dry-wet-fields').is(':visible')) {
            // KFC Formula: (rata-rata berat predust - rata berat wet forming) / rata-rata breader * 100
            const rataRataWetKfc = parseFloat($('#rata_rata_wet_kfc').val());
            const rataRataBreader = parseFloat($('#rata_rata_breader').val());
            
            if (!isNaN(rataRataPredusting) && !isNaN(rataRataWetKfc) && !isNaN(rataRataBreader) && rataRataBreader > 0) {
                const pickup = ((rataRataPredusting - rataRataWetKfc) / rataRataBreader * 100).toFixed(2);
                $('#pickup_after_forming_predusting').val(pickup);
                
                // Update formula text for KFC
                $('#pickup-predust-formula').text('(rata-rata berat predust-rata berat wet forming) / rata-rata breader * 100');
            } else {
                $('#pickup_after_forming_predusting').val('');
            }
        } else {
            // Non-KFC Formula: (rata-rata predusting - rata-rata after forming) / rata-rata after forming * 100
            const rataRataAfterForming = parseFloat($('#rata_rata_after_forming').val());
            
            if (!isNaN(rataRataPredusting) && !isNaN(rataRataAfterForming) && rataRataAfterForming > 0) {
                const pickup = ((rataRataPredusting - rataRataAfterForming) / rataRataAfterForming * 100).toFixed(2);
                $('#pickup_after_forming_predusting').val(pickup);
                
                // Update formula text for non-KFC
                $('#pickup-predust-formula').text('(Rata-rata Predusting - Rata-rata After Forming) / Rata-rata After Forming × 100');
            } else {
                $('#pickup_after_forming_predusting').val('');
            }
        }
    }

    // Show Battering Section
    $(document).on('click', '#btn-ke-battering', function() {
        $('#battering-section').slideDown();
        $(this).prop('disabled', true).text('Battering Aktif');
    });

    // Add Battering Entry
    $(document).on('click', '.add-battering', function() {
        const container = $('#battering-container');
        const newEntry = `
            <div class="battering-entry mb-3">
                <div class="input-group">
                    <input type="number" step="0.01" name="berat_battering[]" class="form-control battering-input" placeholder="Masukkan berat battering">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-battering">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.append(newEntry);
        updateBatteringRemoveButtons();
        calculateBatteringAverage();
    });

    // Remove Battering Entry
    $(document).on('click', '.remove-battering', function() {
        $(this).closest('.battering-entry').remove();
        updateBatteringRemoveButtons();
        calculateBatteringAverage();
    });

    // Calculate battering average when input changes
    $(document).on('input', '.battering-input', function() {
        calculateBatteringAverage();
    });

    // Update battering remove button visibility
    function updateBatteringRemoveButtons() {
        const entries = $('.battering-entry');
        if (entries.length <= 1) {
            $('.remove-battering').hide();
        } else {
            $('.remove-battering').show();
        }
    }

    // Calculate Battering Average
    function calculateBatteringAverage() {
        const inputs = $('.battering-input');
        let total = 0;
        let count = 0;

        inputs.each(function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value) && value !== '') {
                total += value;
                count++;
            }
        });

        const average = count > 0 ? (total / count).toFixed(2) : '';
        $('#rata_rata_battering').val(average);
        
        // Recalculate pickup after updating battering average
        calculatePickupAfterPredustingBattering();
        
        // Recalculate pickup breader if pickup breader field is visible (KFC breadering active)
        if ($('#pickup-breader-field').is(':visible')) {
            calculatePickupBreader();
        }
    }

    // Calculate Pickup After Predusting-Battering or After Forming-Battering
    function calculatePickupAfterPredustingBattering() {
        const rataRataBattering = parseFloat($('#rata_rata_battering').val());
        
        // Check if KFC workflow is active
        if (window.kfcToAfterForming && $('#kfc-dry-wet-fields').is(':visible')) {
            // KFC Formula: (rata-rata batter - rata-rata predust) / rata-rata breader * 100
            const rataRataPredusting = parseFloat($('#rata_rata_predusting').val());
            const rataRataBreader = parseFloat($('#rata_rata_breader').val());
            
            if (!isNaN(rataRataBattering) && !isNaN(rataRataPredusting) && !isNaN(rataRataBreader) && rataRataBreader > 0) {
                const pickup = ((rataRataBattering - rataRataPredusting) / rataRataBreader * 100).toFixed(2);
                $('#pickup_after_predusting_battering').val(pickup);
                
                // Update label and formula text for KFC
                $('#pickup-battering-label').text('Pickup Batter');
                $('#pickup-battering-formula-title').text('Rumus Pickup Batter:');
                $('#pickup-battering-formula').text('(rata-rata batter-rata-rata predust)/rata-rata breader * 100');
            } else {
                $('#pickup_after_predusting_battering').val('');
            }
        } else if (window.directAfterFormingToBattering) {
            // Direct after forming to battering (skip predusting)
            const rataRataAfterForming = parseFloat($('#rata_rata_after_forming').val());
            
            if (!isNaN(rataRataBattering) && !isNaN(rataRataAfterForming) && rataRataAfterForming > 0) {
                const pickup = ((rataRataBattering - rataRataAfterForming) / rataRataAfterForming * 100).toFixed(2);
                $('#pickup_after_predusting_battering').val(pickup);
            } else {
                $('#pickup_after_predusting_battering').val('');
            }
        } else {
            // Normal predusting to battering calculation
            const rataRataPredusting = parseFloat($('#rata_rata_predusting').val());
            
            if (!isNaN(rataRataBattering) && !isNaN(rataRataPredusting) && rataRataPredusting > 0) {
                const pickup = ((rataRataBattering - rataRataPredusting) / rataRataPredusting * 100).toFixed(2);
                $('#pickup_after_predusting_battering').val(pickup);
                
                // Update label and formula text for non-KFC
                $('#pickup-battering-label').text('Pickup After Predusting-Battering');
                $('#pickup-battering-formula-title').text('Rumus Pickup After Predusting-Battering:');
                $('#pickup-battering-formula').text('(Rata-rata Battering - Rata-rata Predusting) / Rata-rata Predusting × 100');
            } else {
                $('#pickup_after_predusting_battering').val('');
            }
        }
    }

    // Show Breadering Section
    $(document).on('click', '#btn-ke-breadering', function() {
        // Check if KFC workflow is active
        if (window.kfcToAfterForming && $('#kfc-dry-wet-fields').is(':visible')) {
            // For KFC: Don't show breadering section, just scroll to breader section
            $(this).prop('disabled', true).text('Breadering Aktif (KFC)');
            
            // Show pickup breader field and calculate its value
            $('#pickup-breader-field').slideDown();
            calculatePickupBreader();
            
            // Show pickup total breader section
            $('#pickup-total-breader-section').slideDown();
            
            // Scroll to breader section
            $('html, body').animate({
                scrollTop: $('#breader-section').offset().top - 100
            }, 800);
            
            // Disable "Ke After Forming" button and enable "Ke Fryer 1" button
            $('#btn-breader-ke-after-forming').prop('disabled', true).removeClass('btn-info').addClass('btn-secondary').text('After Forming (Breadering Aktif)');
            $('#btn-breader-ke-fryer-1').prop('disabled', false).removeClass('btn-secondary').addClass('btn-danger').text('Ke Fryer 1');
        } else {
            // For non-KFC: Show breadering section as normal
            $('#breadering-section').slideDown();
            $(this).prop('disabled', true).text('Breadering Aktif');
        }
    });

    // Add Breadering Entry
    $(document).on('click', '.add-breadering', function() {
        const container = $('#breadering-container');
        const newEntry = `
            <div class="breadering-entry mb-3">
                <div class="input-group">
                    <input type="number" step="0.01" name="berat_breadering[]" class="form-control breadering-input" placeholder="Masukkan berat breadering">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-breadering">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.append(newEntry);
        updateBreaderingRemoveButtons();
        calculateBreaderingAverage();
    });

    // Remove Breadering Entry
    $(document).on('click', '.remove-breadering', function() {
        $(this).closest('.breadering-entry').remove();
        updateBreaderingRemoveButtons();
        calculateBreaderingAverage();
    });

    // Calculate breadering average when input changes
    $(document).on('input', '.breadering-input', function() {
        calculateBreaderingAverage();
    });

    // Update breadering remove button visibility
    function updateBreaderingRemoveButtons() {
        const entries = $('.breadering-entry');
        if (entries.length <= 1) {
            $('.remove-breadering').hide();
        } else {
            $('.remove-breadering').show();
        }
    }

    // Calculate Breadering Average
    function calculateBreaderingAverage() {
        const inputs = $('.breadering-input');
        let total = 0;
        let count = 0;

        inputs.each(function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value) && value !== '') {
                total += value;
                count++;
            }
        });

        const average = count > 0 ? (total / count).toFixed(2) : '';
        $('#rata_rata_breadering').val(average);
        
        // Recalculate pickup after updating breadering average
        calculatePickupAfterBatteringBreadering();
    }

    // Calculate Pickup After Battering-Breadering
    function calculatePickupAfterBatteringBreadering() {
        const rataRataBreaderingValue = parseFloat($('#rata_rata_breadering').val());
        const rataRataBattering = parseFloat($('#rata_rata_battering').val());

        if (!isNaN(rataRataBreaderingValue) && !isNaN(rataRataBattering) && rataRataBattering > 0) {
            const pickup = ((rataRataBreaderingValue - rataRataBattering) / rataRataBattering * 100).toFixed(2);
            $('#pickup_after_battering_breadering').val(pickup);
        } else {
            $('#pickup_after_battering_breadering').val('');
        }
    }

    // Show Fryer 1 Section
    $(document).on('click', '#btn-ke-fryer-1', function() {
        $('#fryer-1-section').slideDown();
        $(this).prop('disabled', true).text('Fryer 1 Aktif');
    });

    // Add Fryer 1 Entry
    $(document).on('click', '.add-fryer-1', function() {
        const container = $('#fryer-1-container');
        const newEntry = `
            <div class="fryer-1-entry mb-3">
                <div class="input-group">
                    <input type="number" step="0.01" name="berat_fryer_1[]" class="form-control fryer-1-input" placeholder="Masukkan berat fryer 1">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-fryer-1">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.append(newEntry);
        updateFryer1RemoveButtons();
        calculateFryer1Average();
    });

    // Remove Fryer 1 Entry
    $(document).on('click', '.remove-fryer-1', function() {
        $(this).closest('.fryer-1-entry').remove();
        updateFryer1RemoveButtons();
        calculateFryer1Average();
    });

    // Calculate fryer 1 average when input changes
    $(document).on('input', '.fryer-1-input', function() {
        calculateFryer1Average();
    });

    // Update fryer 1 remove button visibility
    function updateFryer1RemoveButtons() {
        const entries = $('.fryer-1-entry');
        if (entries.length <= 1) {
            $('.remove-fryer-1').hide();
        } else {
            $('.remove-fryer-1').show();
        }
    }

    // Calculate Fryer 1 Average
    function calculateFryer1Average() {
        const inputs = $('.fryer-1-input');
        let total = 0;
        let count = 0;

        inputs.each(function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value) && value !== '') {
                total += value;
                count++;
            }
        });

        const average = count > 0 ? (total / count).toFixed(2) : '';
        $('#rata_rata_fryer_1').val(average);
        
        // Recalculate pickup after updating fryer 1 average
        calculatePickupBreaderingFryer1();
        
        // Recalculate pickup total if it's active
        if ($('#pickup-total-field').is(':visible')) {
            calculatePickupTotal();
        }
    }

    // Calculate Pickup Breadering-Fryer 1 (for non-KFC) or Breader-Fryer 1 (for KFC)
    function calculatePickupBreaderingFryer1() {
        const rataRataFryer1 = parseFloat($('#rata_rata_fryer_1').val());
        
        // Check if KFC direct workflow
        if (window.kfcDirectToFryer1) {
            const rataRataBreader = parseFloat($('#rata_rata_breader').val());
            
            if (!isNaN(rataRataFryer1) && !isNaN(rataRataBreader) && rataRataBreader > 0) {
                const pickup = ((rataRataFryer1 - rataRataBreader) / rataRataBreader * 100).toFixed(2);
                $('#pickup_breadering_fryer_1').val(pickup);
                
                // Update label for KFC workflow
                $('#pickup_breadering_fryer_1').closest('.form-group').find('label').text('Pickup Breader-Fryer 1 (KFC)');
                $('#pickup_breadering_fryer_1').closest('.form-group').find('small').html('<strong>Rumus Pickup Breader-Fryer 1 (KFC):</strong><br>(Rata-rata Fryer 1 - Rata-rata Breader) / Rata-rata Breader × 100');
            } else {
                $('#pickup_breadering_fryer_1').val('');
            }
        } else {
            // Normal non-KFC workflow
            const rataRataBreaderingValue = parseFloat($('#rata_rata_breadering').val());
            
            if (!isNaN(rataRataFryer1) && !isNaN(rataRataBreaderingValue) && rataRataBreaderingValue > 0) {
                const pickup = ((rataRataFryer1 - rataRataBreaderingValue) / rataRataBreaderingValue * 100).toFixed(2);
                $('#pickup_breadering_fryer_1').val(pickup);
                
                // Reset label for non-KFC workflow
                $('#pickup_breadering_fryer_1').closest('.form-group').find('label').text('Pickup Breadering-Fryer 1');
                $('#pickup_breadering_fryer_1').closest('.form-group').find('small').html('<strong>Rumus Pickup Breadering-Fryer 1:</strong><br>(Rata-rata Fryer 1 - Rata-rata Breadering) / Rata-rata Breadering × 100');
            } else {
                $('#pickup_breadering_fryer_1').val('');
            }
        }
    }

    // Show Pickup Total and Disable Fryer 2 Button
    $(document).on('click', '#btn-hitung-pickup-total', function() {
        // Show pickup total field
        $('#pickup-total-field').slideDown();
        
        // Disable Ke Fryer 2 button
        $('#btn-ke-fryer-2').prop('disabled', true).removeClass('btn-warning').addClass('btn-secondary').text('Fryer 2 (Total Aktif)');
        
        // Disable this button and change text
        $(this).prop('disabled', true).text('Pick Up Total Aktif');
        
        // Calculate pickup total
        calculatePickupTotal();
    });

    // Calculate Pickup Total
    function calculatePickupTotal() {
        const rataRataFryer1 = parseFloat($('#rata_rata_fryer_1').val());
        const rataRataAfterForming = parseFloat($('#rata_rata_after_forming').val());

        if (!isNaN(rataRataFryer1) && !isNaN(rataRataAfterForming) && rataRataAfterForming > 0) {
            const pickupTotal = ((rataRataFryer1 - rataRataAfterForming) / rataRataAfterForming * 100).toFixed(2);
            $('#pickup_total').val(pickupTotal);
        } else {
            $('#pickup_total').val('');
        }
    }

    // Show Fryer 2 Section and Disable Pickup Total Button
    $(document).on('click', '#btn-ke-fryer-2', function() {
        // Show fryer 2 section
        $('#fryer-2-section').slideDown();
        
        // Disable Hitung Pick Up Total button
        $('#btn-hitung-pickup-total').prop('disabled', true).removeClass('btn-info').addClass('btn-secondary').text('Pick Up Total (Fryer 2 Aktif)');
        
        // Disable this button and change text
        $(this).prop('disabled', true).text('Fryer 2 Aktif');
    });

    // Add Fryer 2 Entry
    $(document).on('click', '.add-fryer-2', function() {
        const container = $('#fryer-2-container');
        const newEntry = `
            <div class="fryer-2-entry mb-3">
                <div class="input-group">
                    <input type="number" step="0.01" name="berat_fryer_2[]" class="form-control fryer-2-input" placeholder="Masukkan berat fryer 2">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-fryer-2">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.append(newEntry);
        updateFryer2RemoveButtons();
        calculateFryer2Average();
    });

    // Remove Fryer 2 Entry
    $(document).on('click', '.remove-fryer-2', function() {
        $(this).closest('.fryer-2-entry').remove();
        updateFryer2RemoveButtons();
        calculateFryer2Average();
    });

    // Calculate fryer 2 average when input changes
    $(document).on('input', '.fryer-2-input', function() {
        calculateFryer2Average();
    });

    // Update fryer 2 remove button visibility
    function updateFryer2RemoveButtons() {
        const entries = $('.fryer-2-entry');
        if (entries.length <= 1) {
            $('.remove-fryer-2').hide();
        } else {
            $('.remove-fryer-2').show();
        }
    }

    // Calculate Fryer 2 Average
    function calculateFryer2Average() {
        const inputs = $('.fryer-2-input');
        let total = 0;
        let count = 0;

        inputs.each(function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value) && value !== '') {
                total += value;
                count++;
            }
        });

        const average = count > 0 ? (total / count).toFixed(2) : '';
        $('#rata_rata_fryer_2').val(average);
        
        // Recalculate pickup after updating fryer 2 average
        calculatePickupFryer1Fryer2();
        
        // Recalculate pickup total fryer 2 if it's active
        if ($('#pickup-total-fryer-2-field').is(':visible')) {
            calculatePickupTotalFryer2();
        }
    }

    // Calculate Pickup Fryer 1-Fryer 2
    function calculatePickupFryer1Fryer2() {
        const rataRataFryer2 = parseFloat($('#rata_rata_fryer_2').val());
        const rataRataFryer1 = parseFloat($('#rata_rata_fryer_1').val());

        if (!isNaN(rataRataFryer2) && !isNaN(rataRataFryer1) && rataRataFryer1 > 0) {
            const pickup = ((rataRataFryer2 - rataRataFryer1) / rataRataFryer1 * 100).toFixed(2);
            $('#pickup_fryer_1_fryer_2').val(pickup);
        } else {
            $('#pickup_fryer_1_fryer_2').val('');
        }
    }

    // Show Pickup Total Fryer 2 Field
    $(document).on('click', '#btn-hitung-pickup-total-fryer-2', function() {
        // Show pickup total fryer 2 field
        $('#pickup-total-fryer-2-field').slideDown();
        
        // Disable this button and change text
        $(this).prop('disabled', true).text('Pick Up Total Fryer 2 Aktif');
        
        // Calculate pickup total fryer 2
        calculatePickupTotalFryer2();
    });

    // Calculate Pickup Total Fryer 2
    function calculatePickupTotalFryer2() {
        const rataRataFryer2 = parseFloat($('#rata_rata_fryer_2').val());
        const rataRataAfterForming = parseFloat($('#rata_rata_after_forming').val());

        if (!isNaN(rataRataFryer2) && !isNaN(rataRataAfterForming) && rataRataAfterForming > 0) {
            const pickupTotalFryer2 = ((rataRataFryer2 - rataRataAfterForming) / rataRataAfterForming * 100).toFixed(2);
            $('#pickup_total_fryer_2').val(pickupTotalFryer2);
        } else {
            $('#pickup_total_fryer_2').val('');
        }
    }

    // Handle KFC/non-KFC selection
    $('#jenis_produk_kfc').change(function() {
        const selectedValue = $(this).val();
        
        // Reset all workflow flags
        window.kfcWorkflowActive = false;
        window.kfcDirectToFryer1 = false;
        window.kfcToAfterForming = false;
        window.directAfterFormingToBattering = false;
        
        if (selectedValue === 'non-KFC') {
            // Hide KFC sections and show non-KFC workflow
            $('#breader-section').slideUp();
            $('#after-forming-section').slideDown();
            $('#pickup-after-forming-kfc-field').hide();
            
            // Reset pickup predust formula text to non-KFC
            $('#pickup-predust-formula').text('(Rata-rata Predusting - Rata-rata After Forming) / Rata-rata After Forming × 100');
            
            // Reset pickup battering label and formula text to non-KFC
            $('#pickup-battering-label').text('Pickup After Predusting-Battering');
            $('#pickup-battering-formula-title').text('Rumus Pickup After Predusting-Battering:');
            $('#pickup-battering-formula').text('(Rata-rata Battering - Rata-rata Predusting) / Rata-rata Predusting × 100');
            
            // Reset all sections to non-KFC state
            resetToNonKFCWorkflow();
            
        } else if (selectedValue === 'KFC') {
            // Hide non-KFC sections and show KFC workflow
            $('#after-forming-section').slideUp();
            $('#predusting-section').slideUp();
            $('#battering-section').slideUp();
            $('#breadering-section').slideUp();
            $('#fryer-1-section').slideUp();
            $('#fryer-2-section').slideUp();
            $('#pickup-total-field').slideUp();
            $('#pickup-total-fryer-2-field').slideUp();
            $('#pickup-after-forming-kfc-field').show();
            $('#breader-section').slideDown();
            
            // Update pickup predust formula text for KFC
            $('#pickup-predust-formula').text('(rata-rata berat predust-rata berat wet forming) / rata-rata breader * 100');
            
            // Update pickup battering label and formula text for KFC
            $('#pickup-battering-label').text('Pickup Batter');
            $('#pickup-battering-formula-title').text('Rumus Pickup Batter:');
            $('#pickup-battering-formula').text('(rata-rata batter-rata-rata predust)/rata-rata breader * 100');
            
            // Reset all sections to default state
            resetToDefaultState();
            
        } else {
            // Hide all sections if no selection
            $('#breader-section').slideUp();
            $('#after-forming-section').slideUp();
            $('#predusting-section').slideUp();
            $('#battering-section').slideUp();
            $('#breadering-section').slideUp();
            $('#fryer-1-section').slideUp();
            $('#fryer-2-section').slideUp();
            $('#pickup-total-field').slideUp();
            $('#pickup-total-fryer-2-field').slideUp();
            $('#pickup-after-forming-kfc-field').hide();
            
            // Reset pickup predust formula text to default
            $('#pickup-predust-formula').text('(Rata-rata Predusting - Rata-rata After Forming) / Rata-rata After Forming × 100');
            
            // Reset pickup battering label and formula text to default
            $('#pickup-battering-label').text('Pickup After Predusting-Battering');
            $('#pickup-battering-formula-title').text('Rumus Pickup After Predusting-Battering:');
            $('#pickup-battering-formula').text('(Rata-rata Battering - Rata-rata Predusting) / Rata-rata Predusting × 100');
            
            // Reset all sections to default state
            resetToDefaultState();
        }
    });

    // Reset to Non-KFC Workflow
    function resetToNonKFCWorkflow() {
        // Reset section titles
        $('#after-forming-section .card-header h3').text('After Forming');
        $('#fryer-1-section .card-header h3').text('Berat Fryer 1');
        
        // Show regular after forming fields and hide KFC fields
        $('#regular-after-forming-fields').show();
        $('#kfc-dry-wet-fields').hide();
        $('#pickup-after-forming-kfc-field').hide();
        
        // Reset button states and texts
        $('#btn-ke-predusting').prop('disabled', false).removeClass('btn-secondary').addClass('btn-warning').text('Ke Predusting');
        $('#btn-ke-battering-after-forming').prop('disabled', false).removeClass('btn-secondary').addClass('btn-info').text('Ke Battering').show();
        $('#btn-hitung-pickup-total').prop('disabled', false).removeClass('btn-secondary').addClass('btn-info').text('Hitung Pick Up Total');
        $('#btn-ke-fryer-2').prop('disabled', false).removeClass('btn-secondary').addClass('btn-warning').text('Ke Fryer 2');
        
        // Show fryer 1 buttons for non-KFC products
        $('#fryer-1-buttons-non-kfc').show();
    }

    // Reset to Default State
    function resetToDefaultState() {
        // Show regular after forming fields and hide KFC fields
        $('#regular-after-forming-fields').show();
        $('#kfc-dry-wet-fields').hide();
        $('#pickup-after-forming-kfc-field').hide();
        
        // Reset all button states
        $('#btn-ke-predusting').prop('disabled', false).removeClass('btn-secondary').addClass('btn-warning').text('Ke Predusting');
        $('#btn-ke-battering-after-forming').prop('disabled', false).removeClass('btn-secondary').addClass('btn-info').text('Ke Battering').show();
        $('#btn-ke-breadering').prop('disabled', false).text('Ke Breadering');
        $('#btn-hitung-pickup-total').prop('disabled', false).removeClass('btn-secondary').addClass('btn-info').text('Hitung Pick Up Total');
        $('#btn-ke-fryer-2').prop('disabled', false).removeClass('btn-secondary').addClass('btn-warning').text('Ke Fryer 2');
        $('#btn-hitung-pickup-total-fryer-2').prop('disabled', false).text('Hitung Pick Up Total');
        
        // Show fryer 1 buttons for non-KFC products
        $('#fryer-1-buttons-non-kfc').show();
        
        // Reset breader buttons: Fryer 1 disabled, After Forming enabled
        $('#btn-breader-ke-fryer-1').prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary').text('Ke Fryer 1');
        $('#btn-breader-ke-after-forming').prop('disabled', false).removeClass('btn-secondary').addClass('btn-info').text('Ke After Forming');
        
        // Reset section titles
        $('#after-forming-section .card-header h3').text('After Forming');
        $('#fryer-1-section .card-header h3').text('Berat Fryer 1');
        
        // Update breader button states based on current data
        updateBreaderButtonStates();
    }

    // Show appropriate section if value is selected by default
    if ($('#jenis_produk_kfc').val() === 'non-KFC') {
        $('#after-forming-section').show();
    } else if ($('#jenis_produk_kfc').val() === 'KFC') {
        $('#breader-section').show();
    }

    // Add Breader Entry
    $(document).on('click', '.add-breader', function() {
        const container = $('#breader-container');
        const newEntry = `
            <div class="breader-entry mb-3">
                <div class="input-group">
                    <input type="number" step="0.01" name="berat_breader[]" class="form-control breader-input" placeholder="Masukkan berat breader">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-breader">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.append(newEntry);
        updateBreaderRemoveButtons();
        calculateBreaderAverage();
    });

    // Remove Breader Entry
    $(document).on('click', '.remove-breader', function() {
        $(this).closest('.breader-entry').remove();
        updateBreaderRemoveButtons();
        calculateBreaderAverage();
    });

    // Calculate breader average when input changes
    $(document).on('input', '.breader-input', function() {
        calculateBreaderAverage();
    });

    // Update breader remove button visibility
    function updateBreaderRemoveButtons() {
        const entries = $('.breader-entry');
        if (entries.length <= 1) {
            $('.remove-breader').hide();
        } else {
            $('.remove-breader').show();
        }
    }

    // Calculate Breader Average
    function calculateBreaderAverage() {
        const inputs = $('.breader-input');
        let total = 0;
        let count = 0;

        inputs.each(function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value) && value !== '') {
                total += value;
                count++;
            }
        });

        const average = count > 0 ? (total / count).toFixed(2) : '';
        $('#rata_rata_breader').val(average);
        
        // Enable/disable breader buttons based on data availability
        updateBreaderButtonStates();
        
        // Recalculate KFC pickup if fryer 1 is active and in KFC direct mode
        if (window.kfcDirectToFryer1 && $('#fryer-1-section').is(':visible')) {
            calculatePickupBreaderingFryer1();
        }
        
        // Recalculate pickup after forming KFC if KFC dry/wet fields are visible
        if ($('#kfc-dry-wet-fields').is(':visible')) {
            calculatePickupAfterFormingKfc();
            
            // Also recalculate pickup predusting if predusting section is visible
            if ($('#predusting-section').is(':visible') && window.kfcToAfterForming) {
                calculatePickupAfterFormingPredusting();
            }
            
            // Also recalculate pickup battering if battering section is visible
            if ($('#battering-section').is(':visible') && window.kfcToAfterForming) {
                calculatePickupAfterPredustingBattering();
            }
            
            // Also recalculate pickup total breader if pickup total field is visible
            if ($('#pickup-total-breader-field').is(':visible')) {
                calculatePickupTotalBreader();
            }
            
            // Also recalculate pickup breader if pickup breader field is visible (KFC breadering active)
            if ($('#pickup-breader-field').is(':visible')) {
                calculatePickupBreader();
            }
        }
    }

    // Update Breader Button States
    function updateBreaderButtonStates() {
        // Check if KFC breadering is active (pickup breader field is visible)
        if (window.kfcToAfterForming && $('#pickup-breader-field').is(':visible')) {
            // Keep breadering state: Fryer 1 enabled, After Forming disabled
            $('#btn-breader-ke-fryer-1').prop('disabled', false).removeClass('btn-secondary').addClass('btn-danger').text('Ke Fryer 1');
            $('#btn-breader-ke-after-forming').prop('disabled', true).removeClass('btn-info').addClass('btn-secondary').text('After Forming (Breadering Aktif)');
        } else {
            // Default state: Fryer 1 disabled, After Forming enabled
            $('#btn-breader-ke-fryer-1').prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary').text('Ke Fryer 1');
            $('#btn-breader-ke-after-forming').prop('disabled', false).removeClass('btn-secondary').addClass('btn-info').text('Ke After Forming');
        }
    }

    // KFC Workflow: Breader to Fryer 1 Button
    $(document).on('click', '#btn-breader-ke-fryer-1', function() {
        // Show fryer 1 section for KFC workflow
        $('#fryer-1-section').slideDown();
        $(this).prop('disabled', true).text('Fryer 1 Aktif (KFC)');
        $('#btn-breader-ke-after-forming').prop('disabled', true).removeClass('btn-info').addClass('btn-secondary').text('After Forming (Fryer 1 Aktif)');
        
        // Set KFC workflow flag
        window.kfcWorkflowActive = true;
        window.kfcDirectToFryer1 = true;
        
        // Update fryer 1 section for KFC workflow
        updateFryer1ForKFCWorkflow();
        
        // Scroll to fryer 1 section for KFC products
        $('html, body').animate({
            scrollTop: $('#fryer-1-section').offset().top - 100
        }, 800);
    });

    // KFC Workflow: Breader to After Forming Button
    $(document).on('click', '#btn-breader-ke-after-forming', function() {
        // Show after forming section for KFC workflow
        $('#after-forming-section').slideDown();
        $(this).prop('disabled', true).text('After Forming Aktif (KFC)');
        $('#btn-breader-ke-fryer-1').prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary').text('Fryer 1 (After Forming Aktif)');
        
        // Set KFC workflow flag
        window.kfcWorkflowActive = true;
        window.kfcToAfterForming = true;
        
        // Update after forming section for KFC workflow
        updateAfterFormingForKFCWorkflow();
    });

    // Update Fryer 1 Section for KFC Workflow
    function updateFryer1ForKFCWorkflow() {
        // Change fryer 1 card title to indicate KFC workflow
        $('#fryer-1-section .card-header h3').text('Berat Fryer 1 (KFC)');
        
        // Hide fryer 1 buttons for KFC products
        $('#fryer-1-buttons-non-kfc').hide();
    }

    // Update After Forming Section for KFC Workflow
    function updateAfterFormingForKFCWorkflow() {
        // Change after forming card title to indicate KFC workflow
        $('#after-forming-section .card-header h3').text('After Forming (KFC)');
        
        // Show KFC dry/wet fields and hide regular after forming fields
        $('#regular-after-forming-fields').hide();
        $('#kfc-dry-wet-fields').show();
        
        // Show KFC pickup field
        $('#pickup-after-forming-kfc-field').show();
        
        // Update pickup predust formula text for KFC
        $('#pickup-predust-formula').text('(rata-rata berat predust-rata berat wet forming) / rata-rata breader * 100');
        
        // Update button text for KFC workflow and hide battering button
        $('#btn-ke-predusting').text('Ke Predusting (KFC)');
        $('#btn-ke-battering-after-forming').hide();
    }

    // Initialize remove button visibility and calculation
    updateRemoveButtons();
    calculateAfterFormingAverage();
    updatePredustingRemoveButtons();
    calculatePredustingAverage();
    updateBatteringRemoveButtons();
    calculateBatteringAverage();
    updateBreaderingRemoveButtons();
    calculateBreaderingAverage();
    updateFryer1RemoveButtons();
    calculateFryer1Average();
    updateFryer2RemoveButtons();
    calculateFryer2Average();
    updateBreaderRemoveButtons();
    calculateBreaderAverage();
    updateBreaderButtonStates();
    updateDryKfcRemoveButtons();
    calculateDryKfcAverage();
    updateWetKfcRemoveButtons();
    calculateWetKfcAverage();

    // Add Dry KFC Entry
    $(document).on('click', '.add-dry-kfc', function() {
        const container = $('#dry-kfc-container');
        const newEntry = `
            <div class="dry-kfc-entry mb-3">
                <div class="input-group">
                    <input type="number" step="0.01" name="berat_dry_kfc[]" class="form-control dry-kfc-input" placeholder="Masukkan berat dry">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-dry-kfc">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.append(newEntry);
        updateDryKfcRemoveButtons();
        calculateDryKfcAverage();
    });

    // Remove Dry KFC Entry
    $(document).on('click', '.remove-dry-kfc', function() {
        $(this).closest('.dry-kfc-entry').remove();
        updateDryKfcRemoveButtons();
        calculateDryKfcAverage();
    });

    // Calculate dry KFC average when input changes
    $(document).on('input', '.dry-kfc-input', function() {
        calculateDryKfcAverage();
    });

    // Update dry KFC remove button visibility
    function updateDryKfcRemoveButtons() {
        const entries = $('.dry-kfc-entry');
        if (entries.length <= 1) {
            $('.remove-dry-kfc').hide();
        } else {
            $('.remove-dry-kfc').show();
        }
    }

    // Calculate Dry KFC Average
    function calculateDryKfcAverage() {
        const inputs = $('.dry-kfc-input');
        let total = 0;
        let count = 0;

        inputs.each(function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value) && value !== '') {
                total += value;
                count++;
            }
        });

        const average = count > 0 ? (total / count).toFixed(2) : '';
        $('#rata_rata_dry_kfc').val(average);
        
        // Recalculate pickup after forming KFC
        calculatePickupAfterFormingKfc();
        
        // Recalculate pickup total breader if pickup total field is visible
        if ($('#pickup-total-breader-field').is(':visible')) {
            calculatePickupTotalBreader();
        }
    }

    // Add Wet KFC Entry
    $(document).on('click', '.add-wet-kfc', function() {
        const container = $('#wet-kfc-container');
        const newEntry = `
            <div class="wet-kfc-entry mb-3">
                <div class="input-group">
                    <input type="number" step="0.01" name="berat_wet_kfc[]" class="form-control wet-kfc-input" placeholder="Masukkan berat wet">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-wet-kfc">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.append(newEntry);
        updateWetKfcRemoveButtons();
        calculateWetKfcAverage();
    });

    // Remove Wet KFC Entry
    $(document).on('click', '.remove-wet-kfc', function() {
        $(this).closest('.wet-kfc-entry').remove();
        updateWetKfcRemoveButtons();
        calculateWetKfcAverage();
    });

    // Calculate wet KFC average when input changes
    $(document).on('input', '.wet-kfc-input', function() {
        calculateWetKfcAverage();
    });

    // Update wet KFC remove button visibility
    function updateWetKfcRemoveButtons() {
        const entries = $('.wet-kfc-entry');
        if (entries.length <= 1) {
            $('.remove-wet-kfc').hide();
        } else {
            $('.remove-wet-kfc').show();
        }
    }

    // Calculate Wet KFC Average
    function calculateWetKfcAverage() {
        const inputs = $('.wet-kfc-input');
        let total = 0;
        let count = 0;

        inputs.each(function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value) && value !== '') {
                total += value;
                count++;
            }
        });

        const average = count > 0 ? (total / count).toFixed(2) : '';
        $('#rata_rata_wet_kfc').val(average);
        
        // Recalculate pickup after forming KFC
        calculatePickupAfterFormingKfc();
        
        // Recalculate pickup predusting if predusting section is visible
        if ($('#predusting-section').is(':visible')) {
            calculatePickupAfterFormingPredusting();
        }
        
        // Recalculate pickup breader if pickup breader field is visible (KFC breadering active)
        if ($('#pickup-breader-field').is(':visible')) {
            calculatePickupBreader();
        }
    }

    // Calculate Pickup After Forming KFC
    function calculatePickupAfterFormingKfc() {
        const rataRataWet = parseFloat($('#rata_rata_wet_kfc').val());
        const rataRataDry = parseFloat($('#rata_rata_dry_kfc').val());
        const rataRataBreader = parseFloat($('#rata_rata_breader').val());

        if (!isNaN(rataRataWet) && !isNaN(rataRataDry) && !isNaN(rataRataBreader) && rataRataBreader > 0) {
            const pickup = ((rataRataWet - rataRataDry) / rataRataBreader * 100).toFixed(2);
            $('#pickup_after_forming_kfc').val(pickup);
        } else {
            $('#pickup_after_forming_kfc').val('');
        }
    }

    // Calculate Pickup Breader
    function calculatePickupBreader() {
        const rataRataBreader = parseFloat($('#rata_rata_breader').val());
        const rataRataBattering = parseFloat($('#rata_rata_battering').val());
      
        // For both KFC and non-KFC: Use battering average from input field
        if (!isNaN(rataRataBreader) && !isNaN(rataRataBattering) && rataRataBreader > 0) {
            const pickup = ((rataRataBreader - rataRataBattering) / rataRataBreader * 100).toFixed(2);
        
            $('#pickup_breader').val(pickup);
        } else {
            $('#pickup_breader').val('');
        }
    }

    // Hitung Pick Up Total Breader Button Click
    $(document).on('click', '#btn-hitung-pickup-total-breader', function() {
        // Show pickup total field and calculate its value
        $('#pickup-total-breader-field').slideDown();
        calculatePickupTotalBreader();
        
        // Disable the button after clicking
        $(this).prop('disabled', true).text('Pick Up Total Aktif');
    });

    // Calculate Pickup Total Breader
    function calculatePickupTotalBreader() {
        const rataRataBreader = parseFloat($('#rata_rata_breader').val());
        const rataRataDryKfc = parseFloat($('#rata_rata_dry_kfc').val());

        if (!isNaN(rataRataBreader) && !isNaN(rataRataDryKfc) && rataRataBreader > 0) {
            const pickup = ((rataRataBreader - rataRataDryKfc) / rataRataBreader * 100).toFixed(2);
            $('#pickup_total_breader').val(pickup);
        } else {
            $('#pickup_total_breader').val('');
        }
    }
});
</script>
@endsection


