@extends('layouts.app')

@section('title', 'Edit Data Verifikasi Berat Produk')

@section('container')
<!-- Content Header (Page header) -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Data Verifikasi Berat Produk</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('verifikasi-berat-produk.index') }}">Data Verifikasi Berat Produk</a></li>
                            <li class="breadcrumb-item active">Edit Data</li>
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
                                <h3 class="card-title">Edit Data Verifikasi Berat Produk</h3>
                                <div class="card-tools">
                                    <a href="{{ route('verifikasi-berat-produk.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>
                            <form action="{{ route('verifikasi-berat-produk.update', $verifikasiBeratProduk->uuid) }}" method="POST">
                                @csrf
                                @method('PUT')
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

                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="shift_id">Shift <span class="text-danger">*</span></label>
                                    <select name="shift_id" id="shift_id" class="form-control @error('shift_id') is-invalid @enderror" required>
                                        <option value="">Pilih Shift</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" 
                                                {{ (old('shift_id') ?? $verifikasiBeratProduk->shift_id) == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->shift }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shift_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                    <input type="datetime-local" 
                                           name="tanggal" 
                                           id="tanggal" 
                                           class="form-control @error('tanggal') is-invalid @enderror" 
                                           value="{{ old('tanggal') ?? $verifikasiBeratProduk->tanggal->format('Y-m-d\TH:i') }}" 
                                           required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="id_produk">Produk <span class="text-danger">*</span></label>
                                    <select name="id_produk" id="id_produk" class="form-control @error('id_produk') is-invalid @enderror" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}" 
                                                {{ (old('id_produk') ?? $verifikasiBeratProduk->id_produk) == $produk->id ? 'selected' : '' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_produk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kode_produksi">Kode Produksi <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="kode_produksi" 
                                           id="kode_produksi" 
                                           class="form-control @error('kode_produksi') is-invalid @enderror" 
                                           value="{{ old('kode_produksi') ?? $verifikasiBeratProduk->kode_produksi }}" 
                                           required>
                                    @error('kode_produksi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Jenis Produk KFC (Readonly) -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="jenis_produk_kfc">Jenis Produk <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="jenis_produk_kfc" 
                                           id="jenis_produk_kfc" 
                                           class="form-control" 
                                           value="{{ $verifikasiBeratProduk->jenis_produk_kfc }}" 
                                           readonly>
                                    <small class="text-muted">Jenis produk tidak dapat diubah saat edit</small>
                                </div>
                            </div>
                        </div>

                        <!-- Meta tags for JavaScript initialization -->
                        <meta name="current-jenis-produk" content="{{ $verifikasiBeratProduk->jenis_produk_kfc }}">
                        <meta name="current-breader-data" content="{{ json_encode($verifikasiBeratProduk->berat_breader ?? []) }}">
                        <meta name="current-after-forming-data" content="{{ json_encode($verifikasiBeratProduk->after_forming ?? []) }}">
                        <meta name="current-dry-kfc-data" content="{{ json_encode($verifikasiBeratProduk->berat_dry_kfc ?? []) }}">
                        <meta name="current-wet-kfc-data" content="{{ json_encode($verifikasiBeratProduk->berat_wet_kfc ?? []) }}">
                        <meta name="current-predusting-data" content="{{ json_encode($verifikasiBeratProduk->berat_predusting ?? []) }}">
                        <meta name="current-battering-data" content="{{ json_encode($verifikasiBeratProduk->berat_battering ?? []) }}">
                        <meta name="current-breadering-data" content="{{ json_encode($verifikasiBeratProduk->berat_breadering ?? []) }}">
                        <meta name="current-fryer-1-data" content="{{ json_encode($verifikasiBeratProduk->berat_fryer_1 ?? []) }}">
                        <meta name="current-fryer-2-data" content="{{ json_encode($verifikasiBeratProduk->berat_fryer_2 ?? []) }}">

                        <!-- Form Sections - Show based on existing data -->
                        
                        <!-- Breader Section (For KFC Products) -->
                        @if($verifikasiBeratProduk->jenis_produk_kfc === 'KFC')
                            <div class="card mt-3" id="breader-section">
                                <div class="card-header bg-primary text-white">
                                    <h3 class="card-title mb-0">Berat Breader</h3>
                                </div>
                                <div class="card-body">
                                    <div id="breader-container">
                                        @if($verifikasiBeratProduk->berat_breader)
                                            @foreach($verifikasiBeratProduk->berat_breader as $index => $berat)
                                                <div class="breader-entry mb-3">
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" name="berat_breader[]" class="form-control breader-input" placeholder="Masukkan berat breader" value="{{ $berat }}">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-danger remove-breader" {{ $index === 0 ? 'style=display:none;' : '' }}>
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
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
                                        @endif
                                    </div>
                                    
                                    <div class="text-center mb-3">
                                        <button type="button" class="btn btn-success add-breader">
                                            <i class="fas fa-plus"></i> Tambah Berat Breader
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="rata_rata_breader">Rata-rata Breader</label>
                                                <input type="number" step="0.01" name="rata_rata_breader" id="rata_rata_breader" class="form-control" value="{{ $verifikasiBeratProduk->rata_rata_breader }}" readonly>
                                                <small class="text-muted">Otomatis dihitung dari berat breader</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- After Forming Section -->
                        <div class="card mt-3" id="after-forming-section">
                            <div class="card-header bg-primary text-white">
                                <h3 class="card-title mb-0">After Forming</h3>
                            </div>
                            <div class="card-body">
                                @if($verifikasiBeratProduk->jenis_produk_kfc === 'KFC')
                                    <!-- KFC Dry/Wet Fields -->
                                    <div id="kfc-dry-wet-fields">
                                        <div class="row">
                                            <!-- Berat Dry KFC -->
                                            <div class="col-md-6">
                                                <h5>Berat Dry</h5>
                                                <div id="dry-kfc-container">
                                                    @if($verifikasiBeratProduk->berat_dry_kfc)
                                                        @foreach($verifikasiBeratProduk->berat_dry_kfc as $index => $berat)
                                                            <div class="dry-kfc-entry mb-3">
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01" name="berat_dry_kfc[]" class="form-control dry-kfc-input" placeholder="Masukkan berat dry" value="{{ $berat }}">
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-danger remove-dry-kfc" {{ $index === 0 ? 'style=display:none;' : '' }}>
                                                                            <i class="fas fa-minus"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
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
                                                    @endif
                                                </div>
                                                
                                                <div class="text-center mb-3">
                                                    <button type="button" class="btn btn-success add-dry-kfc">
                                                        <i class="fas fa-plus"></i> Tambah Berat Dry
                                                    </button>
                                                </div>

                                                <div class="form-group">
                                                    <label for="rata_rata_dry_kfc">Rata-rata Dry</label>
                                                    <input type="number" step="0.01" name="rata_rata_dry_kfc" id="rata_rata_dry_kfc" class="form-control" value="{{ $verifikasiBeratProduk->rata_rata_dry_kfc }}" readonly>
                                                    <small class="text-muted">Otomatis dihitung dari berat dry</small>
                                                </div>
                                            </div>

                                            <!-- Berat Wet KFC -->
                                            <div class="col-md-6">
                                                <h5>Berat Wet</h5>
                                                <div id="wet-kfc-container">
                                                    @if($verifikasiBeratProduk->berat_wet_kfc)
                                                        @foreach($verifikasiBeratProduk->berat_wet_kfc as $index => $berat)
                                                            <div class="wet-kfc-entry mb-3">
                                                                <div class="input-group">
                                                                    <input type="number" step="0.01" name="berat_wet_kfc[]" class="form-control wet-kfc-input" placeholder="Masukkan berat wet" value="{{ $berat }}">
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-danger remove-wet-kfc" {{ $index === 0 ? 'style=display:none;' : '' }}>
                                                                            <i class="fas fa-minus"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
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
                                                    @endif
                                                </div>
                                                
                                                <div class="text-center mb-3">
                                                    <button type="button" class="btn btn-success add-wet-kfc">
                                                        <i class="fas fa-plus"></i> Tambah Berat Wet
                                                    </button>
                                                </div>

                                                <div class="form-group">
                                                    <label for="rata_rata_wet_kfc">Rata-rata Wet</label>
                                                    <input type="number" step="0.01" name="rata_rata_wet_kfc" id="rata_rata_wet_kfc" class="form-control" value="{{ $verifikasiBeratProduk->rata_rata_wet_kfc }}" readonly>
                                                    <small class="text-muted">Otomatis dihitung dari berat wet</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Pickup After Forming KFC -->
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pickup_after_forming_kfc">Pickup After Forming KFC</label>
                                                    <input type="number" step="0.01" name="pickup_after_forming_kfc" id="pickup_after_forming_kfc" class="form-control" value="{{ $verifikasiBeratProduk->pickup_after_forming_kfc }}" readonly>
                                                    <small class="text-muted">
                                                        <strong>Rumus Pickup After Forming KFC:</strong><br>
                                                        (Rata-rata Berat Wet - Rata-rata Berat Dry) / Rata-rata Breader × 100
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Regular After Forming Fields (for non-KFC) -->
                                    <div id="regular-after-forming-fields">
                                        <div id="after-forming-container">
                                            @if($verifikasiBeratProduk->after_forming)
                                                @foreach($verifikasiBeratProduk->after_forming as $index => $berat)
                                                    <div class="after-forming-entry mb-3">
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" name="after_forming[]" class="form-control after-forming-input" placeholder="Masukkan berat after forming" value="{{ $berat }}">
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-danger remove-after-forming" {{ $index === 0 ? 'style=display:none;' : '' }}>
                                                                    <i class="fas fa-minus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="after-forming-entry mb-3">
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" name="after_forming[]" class="form-control after-forming-input" placeholder="Masukkan berat after forming">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-danger remove-after-forming" style="display: none;">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="text-center mb-3">
                                            <button type="button" class="btn btn-success add-after-forming">
                                                <i class="fas fa-plus"></i> Tambah After Forming
                                            </button>
                                        </div>

                                        <!-- Rata-rata After Forming -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="rata_rata_after_forming">Rata-rata After Forming</label>
                                                    <input type="number" step="0.01" name="rata_rata_after_forming" id="rata_rata_after_forming" class="form-control" value="{{ $verifikasiBeratProduk->rata_rata_after_forming }}" readonly>
                                                    <small class="text-muted">Otomatis dihitung dari berat after forming</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Additional sections based on existing data -->
                        @if($verifikasiBeratProduk->berat_predusting)
                            <!-- Predusting Section -->
                            <div class="card mt-3" id="predusting-section">
                                <div class="card-header bg-primary text-white">
                                    <h3 class="card-title mb-0">Berat Predusting</h3>
                                </div>
                                <div class="card-body">
                                    <div id="predusting-container">
                                        @foreach($verifikasiBeratProduk->berat_predusting as $index => $berat)
                                            <div class="predusting-entry mb-3">
                                                <div class="input-group">
                                                    <input type="number" step="0.01" name="berat_predusting[]" class="form-control predusting-input" placeholder="Masukkan berat predusting" value="{{ $berat }}">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-danger remove-predusting" {{ $index === 0 ? 'style=display:none;' : '' }}>
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="text-center mb-3">
                                        <button type="button" class="btn btn-success add-predusting">
                                            <i class="fas fa-plus"></i> Tambah Berat Predusting
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="rata_rata_predusting">Rata-rata Predusting</label>
                                                <input type="number" step="0.01" name="rata_rata_predusting" id="rata_rata_predusting" class="form-control" value="{{ $verifikasiBeratProduk->rata_rata_predusting }}" readonly>
                                                <small class="text-muted">Otomatis dihitung dari berat predusting</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pickup_after_forming_predusting">Pickup After Forming Predusting</label>
                                                <input type="number" step="0.01" name="pickup_after_forming_predusting" id="pickup_after_forming_predusting" class="form-control" value="{{ $verifikasiBeratProduk->pickup_after_forming_predusting }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($verifikasiBeratProduk->berat_battering)
                            <!-- Battering Section -->
                            <div class="card mt-3" id="battering-section">
                                <div class="card-header bg-primary text-white">
                                    <h3 class="card-title mb-0">Berat Battering</h3>
                                </div>
                                <div class="card-body">
                                    <div id="battering-container">
                                        @foreach($verifikasiBeratProduk->berat_battering as $index => $berat)
                                            <div class="battering-entry mb-3">
                                                <div class="input-group">
                                                    <input type="number" step="0.01" name="berat_battering[]" class="form-control battering-input" placeholder="Masukkan berat battering" value="{{ $berat }}">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-danger remove-battering" {{ $index === 0 ? 'style=display:none;' : '' }}>
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="text-center mb-3">
                                        <button type="button" class="btn btn-success add-battering">
                                            <i class="fas fa-plus"></i> Tambah Berat Battering
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="rata_rata_battering">Rata-rata Battering</label>
                                                <input type="number" step="0.01" name="rata_rata_battering" id="rata_rata_battering" class="form-control" value="{{ $verifikasiBeratProduk->rata_rata_battering }}" readonly>
                                                <small class="text-muted">Otomatis dihitung dari berat battering</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pickup_after_predusting_battering">Pickup After Predusting Battering</label>
                                                <input type="number" step="0.01" name="pickup_after_predusting_battering" id="pickup_after_predusting_battering" class="form-control" value="{{ $verifikasiBeratProduk->pickup_after_predusting_battering }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($verifikasiBeratProduk->berat_breadering)
                            <!-- Breadering Section -->
                            <div class="card mt-3" id="breadering-section">
                                <div class="card-header bg-dark text-white">
                                    <h3 class="card-title mb-0">Berat Breadering</h3>
                                </div>
                                <div class="card-body">
                                    <div id="breadering-container">
                                        @foreach($verifikasiBeratProduk->berat_breadering as $index => $berat)
                                            <div class="breadering-entry mb-3">
                                                <div class="input-group">
                                                    <input type="number" step="0.01" name="berat_breadering[]" class="form-control breadering-input" placeholder="Masukkan berat breadering" value="{{ $berat }}">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-danger remove-breadering" {{ $index === 0 ? 'style=display:none;' : '' }}>
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="text-center mb-3">
                                        <button type="button" class="btn btn-success add-breadering">
                                            <i class="fas fa-plus"></i> Tambah Berat Breadering
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="rata_rata_breadering">Rata-rata Breadering</label>
                                                <input type="number" step="0.01" name="rata_rata_breadering" id="rata_rata_breadering" class="form-control" value="{{ $verifikasiBeratProduk->rata_rata_breadering }}" readonly>
                                                <small class="text-muted">Otomatis dihitung dari berat breadering</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pickup_after_battering_breadering">Pickup After Battering Breadering</label>
                                                <input type="number" step="0.01" name="pickup_after_battering_breadering" id="pickup_after_battering_breadering" class="form-control" value="{{ $verifikasiBeratProduk->pickup_after_battering_breadering }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($verifikasiBeratProduk->berat_fryer_1)
                            <!-- Fryer 1 Section -->
                            <div class="card mt-3" id="fryer-1-section">
                                <div class="card-header bg-primary text-white">
                                    <h3 class="card-title mb-0">Berat Fryer 1</h3>
                                </div>
                                <div class="card-body">
                                    <div id="fryer-1-container">
                                        @foreach($verifikasiBeratProduk->berat_fryer_1 as $index => $berat)
                                            <div class="fryer-1-entry mb-3">
                                                <div class="input-group">
                                                    <input type="number" step="0.01" name="berat_fryer_1[]" class="form-control fryer-1-input" placeholder="Masukkan berat fryer 1" value="{{ $berat }}">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-danger remove-fryer-1" {{ $index === 0 ? 'style=display:none;' : '' }}>
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="text-center mb-3">
                                        <button type="button" class="btn btn-success add-fryer-1">
                                            <i class="fas fa-plus"></i> Tambah Berat Fryer 1
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="rata_rata_fryer_1">Rata-rata Fryer 1</label>
                                                <input type="number" step="0.01" name="rata_rata_fryer_1" id="rata_rata_fryer_1" class="form-control" value="{{ $verifikasiBeratProduk->rata_rata_fryer_1 }}" readonly>
                                                <small class="text-muted">Otomatis dihitung dari berat fryer 1</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="pickup_breadering_fryer_1">Pickup Breadering Fryer 1</label>
                                                <input type="number" step="0.01" name="pickup_breadering_fryer_1" id="pickup_breadering_fryer_1" class="form-control" value="{{ $verifikasiBeratProduk->pickup_breadering_fryer_1 }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="pickup_total">Pickup Total</label>
                                                <input type="number" step="0.01" name="pickup_total" id="pickup_total" class="form-control" value="{{ $verifikasiBeratProduk->pickup_total }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($verifikasiBeratProduk->berat_fryer_2)
                            <!-- Fryer 2 Section -->
                            <div class="card mt-3" id="fryer-2-section">
                                <div class="card-header bg-dark text-white">
                                    <h3 class="card-title mb-0">Berat Fryer 2</h3>
                                </div>
                                <div class="card-body">
                                    <div id="fryer-2-container">
                                        @foreach($verifikasiBeratProduk->berat_fryer_2 as $index => $berat)
                                            <div class="fryer-2-entry mb-3">
                                                <div class="input-group">
                                                    <input type="number" step="0.01" name="berat_fryer_2[]" class="form-control fryer-2-input" placeholder="Masukkan berat fryer 2" value="{{ $berat }}">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-danger remove-fryer-2" {{ $index === 0 ? 'style=display:none;' : '' }}>
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="text-center mb-3">
                                        <button type="button" class="btn btn-success add-fryer-2">
                                            <i class="fas fa-plus"></i> Tambah Berat Fryer 2
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="rata_rata_fryer_2">Rata-rata Fryer 2</label>
                                                <input type="number" step="0.01" name="rata_rata_fryer_2" id="rata_rata_fryer_2" class="form-control" value="{{ $verifikasiBeratProduk->rata_rata_fryer_2 }}" readonly>
                                                <small class="text-muted">Otomatis dihitung dari berat fryer 2</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="pickup_fryer_1_fryer_2">Pickup Fryer 1 Fryer 2</label>
                                                <input type="number" step="0.01" name="pickup_fryer_1_fryer_2" id="pickup_fryer_1_fryer_2" class="form-control" value="{{ $verifikasiBeratProduk->pickup_fryer_1_fryer_2 }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="pickup_total_fryer_2">Pickup Total Fryer 2</label>
                                                <input type="number" step="0.01" name="pickup_total_fryer_2" id="pickup_total_fryer_2" class="form-control" value="{{ $verifikasiBeratProduk->pickup_total_fryer_2 }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Update Data
                                    </button>
                                    <a href="{{ route('verifikasi-berat-produk.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Batal
                                    </a>
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
$(document).ready(function() {
    // Add/Remove functionality for dynamic forms
    
    // Add entry functions
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
            </div>`;
        container.append(newEntry);
        updateRemoveButtons('.breader-entry', '.remove-breader');
        calculateBreaderAverage();
    });

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
            </div>`;
        container.append(newEntry);
        updateRemoveButtons('.dry-kfc-entry', '.remove-dry-kfc');
        calculateDryKfcAverage();
    });

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
            </div>`;
        container.append(newEntry);
        updateRemoveButtons('.wet-kfc-entry', '.remove-wet-kfc');
        calculateWetKfcAverage();
    });

    $(document).on('click', '.add-after-forming', function() {
        const container = $('#after-forming-container');
        const newEntry = `
            <div class="after-forming-entry mb-3">
                <div class="input-group">
                    <input type="number" step="0.01" name="after_forming[]" class="form-control after-forming-input" placeholder="Masukkan berat after forming">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-after-forming">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>`;
        container.append(newEntry);
        updateRemoveButtons('.after-forming-entry', '.remove-after-forming');
        calculateAfterFormingAverage();
    });

    // Add similar functions for other sections
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
            </div>`;
        container.append(newEntry);
        updateRemoveButtons('.predusting-entry', '.remove-predusting');
        calculatePredustingAverage();
    });

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
            </div>`;
        container.append(newEntry);
        updateRemoveButtons('.battering-entry', '.remove-battering');
        calculateBatteringAverage();
    });

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
            </div>`;
        container.append(newEntry);
        updateRemoveButtons('.breadering-entry', '.remove-breadering');
        calculateBreaderingAverage();
    });

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
            </div>`;
        container.append(newEntry);
        updateRemoveButtons('.fryer-1-entry', '.remove-fryer-1');
        calculateFryer1Average();
    });

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
            </div>`;
        container.append(newEntry);
        updateRemoveButtons('.fryer-2-entry', '.remove-fryer-2');
        calculateFryer2Average();
    });

    // Remove entry functions
    $(document).on('click', '.remove-breader', function() {
        $(this).closest('.breader-entry').remove();
        updateRemoveButtons('.breader-entry', '.remove-breader');
        calculateBreaderAverage();
    });

    $(document).on('click', '.remove-dry-kfc', function() {
        $(this).closest('.dry-kfc-entry').remove();
        updateRemoveButtons('.dry-kfc-entry', '.remove-dry-kfc');
        calculateDryKfcAverage();
    });

    $(document).on('click', '.remove-wet-kfc', function() {
        $(this).closest('.wet-kfc-entry').remove();
        updateRemoveButtons('.wet-kfc-entry', '.remove-wet-kfc');
        calculateWetKfcAverage();
    });

    $(document).on('click', '.remove-after-forming', function() {
        $(this).closest('.after-forming-entry').remove();
        updateRemoveButtons('.after-forming-entry', '.remove-after-forming');
        calculateAfterFormingAverage();
    });

    $(document).on('click', '.remove-predusting', function() {
        $(this).closest('.predusting-entry').remove();
        updateRemoveButtons('.predusting-entry', '.remove-predusting');
        calculatePredustingAverage();
    });

    $(document).on('click', '.remove-battering', function() {
        $(this).closest('.battering-entry').remove();
        updateRemoveButtons('.battering-entry', '.remove-battering');
        calculateBatteringAverage();
    });

    $(document).on('click', '.remove-breadering', function() {
        $(this).closest('.breadering-entry').remove();
        updateRemoveButtons('.breadering-entry', '.remove-breadering');
        calculateBreaderingAverage();
    });

    $(document).on('click', '.remove-fryer-1', function() {
        $(this).closest('.fryer-1-entry').remove();
        updateRemoveButtons('.fryer-1-entry', '.remove-fryer-1');
        calculateFryer1Average();
    });

    $(document).on('click', '.remove-fryer-2', function() {
        $(this).closest('.fryer-2-entry').remove();
        updateRemoveButtons('.fryer-2-entry', '.remove-fryer-2');
        calculateFryer2Average();
    });

    // Input change events for calculations with immediate pickup recalculation
    $(document).on('input change keyup', '.breader-input', function() {
        calculateBreaderAverage();
        calculateAllPickups();
    });
    $(document).on('input change keyup', '.dry-kfc-input', function() {
        calculateDryKfcAverage();
        calculateAllPickups();
    });
    $(document).on('input change keyup', '.wet-kfc-input', function() {
        calculateWetKfcAverage();
        calculateAllPickups();
    });
    $(document).on('input change keyup', '.after-forming-input', function() {
        calculateAfterFormingAverage();
        calculateAllPickups();
    });
    $(document).on('input change keyup', '.predusting-input', function() {
        calculatePredustingAverage();
        calculateAllPickups();
    });
    $(document).on('input change keyup', '.battering-input', function() {
        calculateBatteringAverage();
        calculateAllPickups();
    });
    $(document).on('input change keyup', '.breadering-input', function() {
        calculateBreaderingAverage();
        calculateAllPickups();
    });
    $(document).on('input change keyup', '.fryer-1-input', function() {
        calculateFryer1Average();
        calculateAllPickups();
    });
    $(document).on('input change keyup', '.fryer-2-input', function() {
        calculateFryer2Average();
        calculateAllPickups();
    });

    // Also bind directly to existing inputs on page load with pickup calculations
    $('.after-forming-input').on('input change keyup', function() {
        calculateAfterFormingAverage();
        calculateAllPickups();
    });
    $('.breader-input').on('input change keyup', function() {
        calculateBreaderAverage();
        calculateAllPickups();
    });
    $('.dry-kfc-input').on('input change keyup', function() {
        calculateDryKfcAverage();
        calculateAllPickups();
    });
    $('.wet-kfc-input').on('input change keyup', function() {
        calculateWetKfcAverage();
        calculateAllPickups();
    });
    $('.predusting-input').on('input change keyup', function() {
        calculatePredustingAverage();
        calculateAllPickups();
    });
    $('.battering-input').on('input change keyup', function() {
        calculateBatteringAverage();
        calculateAllPickups();
    });
    $('.breadering-input').on('input change keyup', function() {
        calculateBreaderingAverage();
        calculateAllPickups();
    });
    $('.fryer-1-input').on('input change keyup', function() {
        calculateFryer1Average();
        calculateAllPickups();
    });
    $('.fryer-2-input').on('input change keyup', function() {
        calculateFryer2Average();
        calculateAllPickups();
    });

    // Utility functions
    function updateRemoveButtons(entrySelector, removeSelector) {
        const entries = $(entrySelector);
        entries.each(function(index) {
            const removeBtn = $(this).find(removeSelector);
            if (entries.length === 1) {
                removeBtn.hide();
            } else {
                removeBtn.show();
            }
        });
    }

    // Calculation functions
    function calculateBreaderAverage() {
        const values = $('.breader-input').map(function() {
            return parseFloat($(this).val()) || 0;
        }).get().filter(val => val > 0);
        
        const average = values.length > 0 ? (values.reduce((a, b) => a + b, 0) / values.length).toFixed(2) : '';
        $('#rata_rata_breader').val(average);
    }

    function calculateDryKfcAverage() {
        const values = $('.dry-kfc-input').map(function() {
            return parseFloat($(this).val()) || 0;
        }).get().filter(val => val > 0);
        
        const average = values.length > 0 ? (values.reduce((a, b) => a + b, 0) / values.length).toFixed(2) : '';
        $('#rata_rata_dry_kfc').val(average);
    }

    function calculateWetKfcAverage() {
        const values = $('.wet-kfc-input').map(function() {
            return parseFloat($(this).val()) || 0;
        }).get().filter(val => val > 0);
        
        const average = values.length > 0 ? (values.reduce((a, b) => a + b, 0) / values.length).toFixed(2) : '';
        $('#rata_rata_wet_kfc').val(average);
    }

    function calculateAfterFormingAverage() {
        const values = $('.after-forming-input').map(function() {
            return parseFloat($(this).val()) || 0;
        }).get().filter(val => val > 0);
        
        const average = values.length > 0 ? (values.reduce((a, b) => a + b, 0) / values.length).toFixed(2) : '';
        $('#rata_rata_after_forming').val(average);
    }

    function calculatePredustingAverage() {
        const values = $('.predusting-input').map(function() {
            return parseFloat($(this).val()) || 0;
        }).get().filter(val => val > 0);
        
        const average = values.length > 0 ? (values.reduce((a, b) => a + b, 0) / values.length).toFixed(2) : '';
        $('#rata_rata_predusting').val(average);
    }

    function calculateBatteringAverage() {
        const values = $('.battering-input').map(function() {
            return parseFloat($(this).val()) || 0;
        }).get().filter(val => val > 0);
        
        const average = values.length > 0 ? (values.reduce((a, b) => a + b, 0) / values.length).toFixed(2) : '';
        $('#rata_rata_battering').val(average);
    }

    function calculateBreaderingAverage() {
        const values = $('.breadering-input').map(function() {
            return parseFloat($(this).val()) || 0;
        }).get().filter(val => val > 0);
        
        const average = values.length > 0 ? (values.reduce((a, b) => a + b, 0) / values.length).toFixed(2) : '';
        $('#rata_rata_breadering').val(average);
    }

    function calculateFryer1Average() {
        const values = $('.fryer-1-input').map(function() {
            return parseFloat($(this).val()) || 0;
        }).get().filter(val => val > 0);
        
        const average = values.length > 0 ? (values.reduce((a, b) => a + b, 0) / values.length).toFixed(2) : '';
        $('#rata_rata_fryer_1').val(average);
    }

    function calculateFryer2Average() {
        const values = $('.fryer-2-input').map(function() {
            return parseFloat($(this).val()) || 0;
        }).get().filter(val => val > 0);
        
        const average = values.length > 0 ? (values.reduce((a, b) => a + b, 0) / values.length).toFixed(2) : '';
        $('#rata_rata_fryer_2').val(average);
    }

    // Pickup calculation functions
    function calculatePickupAfterFormingKfc() {
        const rataRataWet = parseFloat($('#rata_rata_wet_kfc').val()) || 0;
        const rataRataDry = parseFloat($('#rata_rata_dry_kfc').val()) || 0;
        const rataRataBreader = parseFloat($('#rata_rata_breader').val()) || 0;
        
        if (rataRataBreader > 0 && rataRataWet > 0 && rataRataDry > 0) {
            const pickup = (((rataRataWet - rataRataDry) / rataRataBreader) * 100).toFixed(2);
            $('#pickup_after_forming_kfc').val(pickup);
        } else {
            $('#pickup_after_forming_kfc').val('');
        }
    }

    function calculatePickupAfterFormingPredusting() {
        const rataRataPredusting = parseFloat($('#rata_rata_predusting').val()) || 0;
        const productType = getCurrentProductType();
        
        if (productType === 'KFC') {
            // KFC Formula: (rata-rata berat predust - rata berat wet forming) / rata-rata breader * 100
            const rataRataWetKfc = parseFloat($('#rata_rata_wet_kfc').val()) || 0;
            const rataRataBreader = parseFloat($('#rata_rata_breader').val()) || 0;
            
            if (rataRataPredusting > 0 && rataRataWetKfc > 0 && rataRataBreader > 0) {
                const pickup = (((rataRataPredusting - rataRataWetKfc) / rataRataBreader) * 100).toFixed(2);
                $('#pickup_after_forming_predusting').val(pickup);
            } else {
                $('#pickup_after_forming_predusting').val('');
            }
        } else {
            // Non-KFC Formula: (rata-rata predusting - rata-rata after forming) / rata-rata after forming * 100
            const rataRataAfterForming = parseFloat($('#rata_rata_after_forming').val()) || 0;
            
            if (rataRataPredusting > 0 && rataRataAfterForming > 0) {
                const pickup = (((rataRataPredusting - rataRataAfterForming) / rataRataAfterForming) * 100).toFixed(2);
                $('#pickup_after_forming_predusting').val(pickup);
            } else {
                $('#pickup_after_forming_predusting').val('');
            }
        }
    }

    function calculatePickupAfterPredustingBattering() {
        const rataRataBattering = parseFloat($('#rata_rata_battering').val()) || 0;
        const productType = getCurrentProductType();
        
        if (productType === 'KFC') {
            // KFC Formula: (rata-rata batter - rata-rata predust) / rata-rata breader * 100
            const rataRataPredusting = parseFloat($('#rata_rata_predusting').val()) || 0;
            const rataRataBreader = parseFloat($('#rata_rata_breader').val()) || 0;
            
            if (rataRataBattering > 0 && rataRataPredusting > 0 && rataRataBreader > 0) {
                const pickup = (((rataRataBattering - rataRataPredusting) / rataRataBreader) * 100).toFixed(2);
                $('#pickup_after_predusting_battering').val(pickup);
            } else {
                $('#pickup_after_predusting_battering').val('');
            }
        } else {
            // Check if direct after forming to battering (skip predusting)
            const rataRataAfterForming = parseFloat($('#rata_rata_after_forming').val()) || 0;
            const rataRataPredusting = parseFloat($('#rata_rata_predusting').val()) || 0;
            
            if (rataRataPredusting > 0) {
                // Normal predusting to battering calculation
                if (rataRataBattering > 0 && rataRataPredusting > 0) {
                    const pickup = (((rataRataBattering - rataRataPredusting) / rataRataPredusting) * 100).toFixed(2);
                    $('#pickup_after_predusting_battering').val(pickup);
                } else {
                    $('#pickup_after_predusting_battering').val('');
                }
            } else if (rataRataAfterForming > 0) {
                // Direct after forming to battering (skip predusting)
                if (rataRataBattering > 0 && rataRataAfterForming > 0) {
                    const pickup = (((rataRataBattering - rataRataAfterForming) / rataRataAfterForming) * 100).toFixed(2);
                    $('#pickup_after_predusting_battering').val(pickup);
                } else {
                    $('#pickup_after_predusting_battering').val('');
                }
            } else {
                $('#pickup_after_predusting_battering').val('');
            }
        }
    }

    function calculatePickupAfterBatteringBreadering() {
        const rataRataBreaderingValue = parseFloat($('#rata_rata_breadering').val()) || 0;
        const rataRataBattering = parseFloat($('#rata_rata_battering').val()) || 0;
        
        if (rataRataBreaderingValue > 0 && rataRataBattering > 0) {
            const pickup = (((rataRataBreaderingValue - rataRataBattering) / rataRataBattering) * 100).toFixed(2);
            $('#pickup_after_battering_breadering').val(pickup);
        } else {
            $('#pickup_after_battering_breadering').val('');
        }
    }

    function calculatePickupBreaderingFryer1() {
        const rataRataFryer1 = parseFloat($('#rata_rata_fryer_1').val()) || 0;
        const productType = getCurrentProductType();
        
        if (productType === 'KFC') {
            // KFC Formula: (rata-rata fryer 1 - rata-rata breader) / rata-rata breader * 100
            const rataRataBreader = parseFloat($('#rata_rata_breader').val()) || 0;
            
            if (rataRataFryer1 > 0 && rataRataBreader > 0) {
                const pickup = (((rataRataFryer1 - rataRataBreader) / rataRataBreader) * 100).toFixed(2);
                $('#pickup_breadering_fryer_1').val(pickup);
            } else {
                $('#pickup_breadering_fryer_1').val('');
            }
        } else {
            // Non-KFC Formula: (rata-rata fryer 1 - rata-rata breadering) / rata-rata breadering * 100
            const rataRataBreaderingValue = parseFloat($('#rata_rata_breadering').val()) || 0;
            
            if (rataRataFryer1 > 0 && rataRataBreaderingValue > 0) {
                const pickup = (((rataRataFryer1 - rataRataBreaderingValue) / rataRataBreaderingValue) * 100).toFixed(2);
                $('#pickup_breadering_fryer_1').val(pickup);
            } else {
                $('#pickup_breadering_fryer_1').val('');
            }
        }
    }

    function calculatePickupTotal() {
        const productType = getCurrentProductType();
        
        if (productType === 'KFC') {
            // KFC has two types of pickup total calculations
            // 1. Pickup Total Breader: (rata-rata breader - rata-rata dry kfc) / rata-rata breader * 100
            const rataRataBreader = parseFloat($('#rata_rata_breader').val()) || 0;
            const rataRataDryKfc = parseFloat($('#rata_rata_dry_kfc').val()) || 0;
            
            if (rataRataBreader > 0 && rataRataDryKfc > 0) {
                const pickupTotalBreader = (((rataRataBreader - rataRataDryKfc) / rataRataBreader) * 100).toFixed(2);
                // Use pickup_total field for KFC pickup total breader calculation
                $('#pickup_total').val(pickupTotalBreader);
            } else {
                // 2. Standard pickup total if breader/dry kfc not available: (rata-rata fryer 1 - rata-rata after forming) / rata-rata after forming * 100
                const rataRataFryer1 = parseFloat($('#rata_rata_fryer_1').val()) || 0;
                const rataRataAfterForming = parseFloat($('#rata_rata_after_forming').val()) || 0;
                
                if (rataRataFryer1 > 0 && rataRataAfterForming > 0) {
                    const pickup = (((rataRataFryer1 - rataRataAfterForming) / rataRataAfterForming) * 100).toFixed(2);
                    $('#pickup_total').val(pickup);
                } else {
                    $('#pickup_total').val('');
                }
            }
        } else {
            // Non-KFC Formula: (rata-rata fryer 1 - rata-rata after forming) / rata-rata after forming * 100
            const rataRataFryer1 = parseFloat($('#rata_rata_fryer_1').val()) || 0;
            const rataRataAfterForming = parseFloat($('#rata_rata_after_forming').val()) || 0;
            
            if (rataRataFryer1 > 0 && rataRataAfterForming > 0) {
                const pickup = (((rataRataFryer1 - rataRataAfterForming) / rataRataAfterForming) * 100).toFixed(2);
                $('#pickup_total').val(pickup);
            } else {
                $('#pickup_total').val('');
            }
        }
    }

    function calculatePickupFryer1Fryer2() {
        const rataRataFryer2 = parseFloat($('#rata_rata_fryer_2').val()) || 0;
        const rataRataFryer1 = parseFloat($('#rata_rata_fryer_1').val()) || 0;
        
        if (rataRataFryer2 > 0 && rataRataFryer1 > 0) {
            const pickup = (((rataRataFryer2 - rataRataFryer1) / rataRataFryer1) * 100).toFixed(2);
            $('#pickup_fryer_1_fryer_2').val(pickup);
        } else {
            $('#pickup_fryer_1_fryer_2').val('');
        }
    }

    function calculatePickupTotalFryer2() {
        const rataRataFryer2 = parseFloat($('#rata_rata_fryer_2').val()) || 0;
        const rataRataAfterForming = parseFloat($('#rata_rata_after_forming').val()) || 0;
        
        if (rataRataFryer2 > 0 && rataRataAfterForming > 0) {
            const pickup = (((rataRataFryer2 - rataRataAfterForming) / rataRataAfterForming) * 100).toFixed(2);
            $('#pickup_total_fryer_2').val(pickup);
        } else {
            $('#pickup_total_fryer_2').val('');
        }
    }

    // Function to get current product type
    function getCurrentProductType() {
        return $('#jenis_produk_kfc').val() || '';
    }

    // Function to calculate all pickups based on product type
    function calculateAllPickups() {
        const productType = getCurrentProductType();
        
        if (productType === 'KFC') {
            calculatePickupAfterFormingKfc();
        }
        
        // Calculate other pickups if sections exist
        if ($('#rata_rata_predusting').length) {
            calculatePickupAfterFormingPredusting();
        }
        if ($('#rata_rata_battering').length) {
            calculatePickupAfterPredustingBattering();
        }
        if ($('#rata_rata_breadering').length) {
            calculatePickupAfterBatteringBreadering();
        }
        if ($('#rata_rata_fryer_1').length) {
            calculatePickupBreaderingFryer1();
            calculatePickupTotal();
        }
        if ($('#rata_rata_fryer_2').length) {
            calculatePickupFryer1Fryer2();
            calculatePickupTotalFryer2();
        }
    }

    // Enhanced calculation trigger function
    function triggerAllCalculations() {
        const productType = getCurrentProductType();
        
        // Calculate averages first
        if ($('.breader-input').length) calculateBreaderAverage();
        if ($('.dry-kfc-input').length) calculateDryKfcAverage();
        if ($('.wet-kfc-input').length) calculateWetKfcAverage();
        if ($('.after-forming-input').length) calculateAfterFormingAverage();
        if ($('.predusting-input').length) calculatePredustingAverage();
        if ($('.battering-input').length) calculateBatteringAverage();
        if ($('.breadering-input').length) calculateBreaderingAverage();
        if ($('.fryer-1-input').length) calculateFryer1Average();
        if ($('.fryer-2-input').length) calculateFryer2Average();
        
        // Then calculate all pickups
        calculateAllPickups();
    }

    // Initialize calculations on page load with a small delay
    setTimeout(function() {
        // Re-bind events to existing inputs with pickup calculations
        $('.after-forming-input').off('input change keyup').on('input change keyup', function() {
            calculateAfterFormingAverage();
            calculateAllPickups();
        });
        $('.breader-input').off('input change keyup').on('input change keyup', function() {
            calculateBreaderAverage();
            calculateAllPickups();
        });
        $('.dry-kfc-input').off('input change keyup').on('input change keyup', function() {
            calculateDryKfcAverage();
            calculateAllPickups();
        });
        $('.wet-kfc-input').off('input change keyup').on('input change keyup', function() {
            calculateWetKfcAverage();
            calculateAllPickups();
        });
        $('.predusting-input').off('input change keyup').on('input change keyup', function() {
            calculatePredustingAverage();
            calculateAllPickups();
        });
        $('.battering-input').off('input change keyup').on('input change keyup', function() {
            calculateBatteringAverage();
            calculateAllPickups();
        });
        $('.breadering-input').off('input change keyup').on('input change keyup', function() {
            calculateBreaderingAverage();
            calculateAllPickups();
        });
        $('.fryer-1-input').off('input change keyup').on('input change keyup', function() {
            calculateFryer1Average();
            calculateAllPickups();
        });
        $('.fryer-2-input').off('input change keyup').on('input change keyup', function() {
            calculateFryer2Average();
            calculateAllPickups();
        });
        
        // Initialize all calculations
        triggerAllCalculations();
    }, 100);
});
</script>
@endpush
@endsection
