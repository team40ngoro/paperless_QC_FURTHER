@extends('layouts.app')

@section('title', 'Tambah Pemeriksaan Rheon Machine')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Pemeriksaan Rheon Machine</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-rheon-machine.index') }}">Data Pemeriksaan Rheon Machine</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Data Pemeriksaan Rheon Machine
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('pemeriksaan-rheon-machine.index') }}" class="btn btn-tool">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                <form action="{{ route('pemeriksaan-rheon-machine.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Informasi Dasar -->
                        <div class="card card-outline card-info mb-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Informasi Dasar
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tanggal">
                                                <i class="fas fa-calendar-alt mr-1"></i>Tanggal
                                                <span class="text-danger">*</span>
                                            </label>
                                            
                                            <input type="date" name="tanggal" id="tanggal" 
                                                class="form-control @error('tanggal') is-invalid @enderror" 
                                                value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                            @error('tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="shift_id">
                                                <i class="fas fa-clock mr-1"></i>
                                                Shift <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control @error('shift_id') is-invalid @enderror" 
                                                    id="shift_id" name="shift_id" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('shift_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="id_produk">
                                                <i class="fas fa-box mr-1"></i>
                                                Nama Produk <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control @error('id_produk') is-invalid @enderror" 
                                                    id="id_produk" name="id_produk" required>
                                                <option value="">Pilih Produk</option>
                                                @foreach($produk as $item)
                                                    <option value="{{ $item->id }}" {{ old('id_produk') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama_produk }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_produk')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                       
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="batch">
                                                <i class="fas fa-tag mr-1"></i>
                                                Batch
                                            </label>
                                            <input type="text" class="form-control @error('batch') is-invalid @enderror" 
                                                   id="batch" name="batch" value="{{ old('batch') }}" placeholder="Masukkan batch">
                                            @error('batch')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pukul">
                                                <i class="fas fa-clock mr-1"></i>
                                                Pukul
                                            </label>
                                            <input type="time" class="form-control @error('pukul') is-invalid @enderror" 
                                                   id="pukul" name="pukul" value="{{ old('pukul') }}" placeholder="Contoh: 08:00">
                                            @error('pukul')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Setting Rheon Machine Section -->
                        <div class="card card-outline card-warning mb-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-cogs mr-2"></i>
                                    Setting Rheon Machine
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="inner">
                                                <i class="fas fa-circle mr-1 text-primary"></i>
                                                Inner
                                            </label>
                                            <input type="text" class="form-control @error('inner') is-invalid @enderror" 
                                                   id="inner" name="inner" value="{{ old('inner') }}" placeholder="Masukkan nilai inner">
                                            @error('inner')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="outer">
                                                <i class="fas fa-circle mr-1 text-success"></i>
                                                Outer
                                            </label>
                                            <input type="text" class="form-control @error('outer') is-invalid @enderror" 
                                                   id="outer" name="outer" value="{{ old('outer') }}" placeholder="Masukkan nilai outer">
                                            @error('outer')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="belt">
                                                <i class="fas fa-link mr-1 text-warning"></i>
                                                Belt
                                            </label>
                                            <input type="text" class="form-control @error('belt') is-invalid @enderror" 
                                                   id="belt" name="belt" value="{{ old('belt') }}" placeholder="Masukkan nilai belt">
                                            @error('belt')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="extrusion_speed">
                                                <i class="fas fa-tachometer-alt mr-1 text-info"></i>
                                                Encrushed Speed
                                            </label>
                                            <input type="text" class="form-control @error('extrusion_speed') is-invalid @enderror" 
                                                   id="extrusion_speed" name="extrusion_speed" value="{{ old('extrusion_speed') }}" placeholder="Masukkan kecepatan">
                                            @error('extrusion_speed')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="jenis_cetakan">
                                                <i class="fas fa-shapes mr-1 text-danger"></i>
                                                Jenis Cetakan (cutlet/dove)
                                            </label>
                                            <input type="text" class="form-control @error('jenis_cetakan') is-invalid @enderror" 
                                                   id="jenis_cetakan" name="jenis_cetakan" value="{{ old('jenis_cetakan') }}" placeholder="Masukkan jenis cetakan">
                                            @error('jenis_cetakan')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Berat Section -->
                        <div class="card card-outline card-success mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-weight mr-2"></i>
                                    berat
                                </h3>
                               
                                <button type="button" class="btn btn-primary btn-sm" id="add-berat-section">
                                    <i class="fas fa-plus mr-1"></i> Tambah Berat
                                </button>
                            </div>
                            
                            <!-- Main Berat Form -->
                            <div class="card-body">
                                <small id="std-berat-rheon-info" class="text-muted d-block mb-2" style="display:none;"></small>

                                <div id="berat-sections-container">
                                    <!-- Berat Section 1 -->
                                    <div class="berat-section mb-4" data-section="1">
                                        <div class="row">
                                            
                                            <!-- Dough/Adonan Column -->
                                            <div class="col-md-6 mb-4">
                                                
                                                <!-- Input and Controls Row -->
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <div class="flex-fill mr-3">
                                                        <small class="text-muted d-block mb-1">berat</small>
                                                        <input type="text" class="form-control form-control-sm input-dough-berat" 
                                                               placeholder="opsional: isi berat (default: 1)" style="border-radius: 20px; font-size: 12px;">
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-center mr-4">
                                                            <h6 class="font-weight-bold text-primary mb-1">dough/adonan</h6>
                                                            <small class="text-muted">jumlah item : <span class="count-dough font-weight-bold text-primary">0</span></small>
                                                        </div>
                                                        <div class="d-flex">
                                                            <button type="button" class="btn btn-success btn-sm rounded-circle mr-1 add-dough" 
                                                                    style="width: 28px; height: 28px; font-size: 12px;">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm rounded-circle remove-dough" 
                                                                    style="width: 28px; height: 28px; font-size: 12px;">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="dough-items-container">
                                                    <!-- Items will be added dynamically -->
                                                </div>
                                            </div>

                                            <!-- Filler Column -->
                                            <div class="col-md-6 mb-4">
                                                
                                                
                                                <!-- Controls Row (No Input) -->
                                                <div class="d-flex align-items-center justify-content-center mb-3">
                                                        <div class="text-center mr-4">
                                                            <h6 class="font-weight-bold text-success mb-1">filler isi</h6>
                                                            <small class="text-muted">jumlah item : <span class="count-filler font-weight-bold text-success">0</span></small>
                                                        </div>
                                                        <div class="d-flex">
                                                            <button type="button" class="btn btn-success btn-sm rounded-circle mr-1 add-filler" 
                                                                    style="width: 28px; height: 28px; font-size: 12px;">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm rounded-circle remove-filler" 
                                                                    style="width: 28px; height: 28px; font-size: 12px;">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="filler-items-container">
                                                    <!-- Items will be added dynamically -->
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Remove Section Button -->
                                        <div class="text-right mb-3">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-berat-section" style="display: none;">
                                                <i class="fas fa-trash mr-1"></i> Hapus Section
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pause Button (Right Side)
                                <div class="text-right mb-3">
                                    <button type="button" class="btn btn-secondary btn-sm rounded-circle" id="pause-berat" 
                                            style="width: 35px; height: 35px;">
                                        <i class="fas fa-pause"></i>
                                    </button>
                                </div> -->

                                <!-- Summary Row for Dough/Filler -->
                                <div class="row">
                                    <!-- Dough/Adonan Summary -->
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0">
                                            <div class="card-body py-2 px-3">
                                                <h6 class="text-primary mb-2 text-center">Dough/Adonan</h6>
                                                <div class="row text-center">
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Jumlah</small>
                                                        <span id="jumlah-dough" class="font-weight-bold text-primary h6 mb-0">0</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Rata-rata</small>
                                                        <span id="rata-rata-dough" class="font-weight-bold text-info h6 mb-0">0.00 g</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Filler Isi Summary -->
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0">
                                            <div class="card-body py-2 px-3">
                                                <h6 class="text-success mb-2 text-center">Filler Isi</h6>
                                                <div class="row text-center">
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Jumlah</small>
                                                        <span id="jumlah-filler" class="font-weight-bold text-success h6 mb-0">0</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Rata-rata</small>
                                                        <span id="rata-rata-filler" class="font-weight-bold text-warning h6 mb-0">0.00 g</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Berat After Forming & After Frying Section -->
                        <div class="card card-outline card-danger mb-3">
                            <div class="card-body">
                                <div class="after-forming-frying-section mb-4" data-section="1">
                                    <div class="row">
                                        <!-- After Forming Column -->
                                        <div class="col-md-6 mb-4">
                                            <!-- Input and Controls Row -->
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <div class="flex-fill mr-3">
                                                    <small class="text-muted d-block mb-1">berat</small>
                                                    <input type="text" class="form-control form-control-sm input-after-forming-berat" 
                                                           placeholder="opsional: isi berat (default: 1)" style="border-radius: 20px; font-size: 12px;">
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="text-center mr-4">
                                                        <h6 class="font-weight-bold text-warning mb-1">after forming</h6>
                                                        <small class="text-muted">jumlah item : <span class="count-after-forming font-weight-bold text-warning">0</span></small>
                                                    </div>
                                                    <div class="d-flex">
                                                        <button type="button" class="btn btn-success btn-sm rounded-circle mr-1 add-after-forming" 
                                                                style="width: 28px; height: 28px; font-size: 12px;">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm rounded-circle remove-after-forming" 
                                                                style="width: 28px; height: 28px; font-size: 12px;">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="after-forming-items-container">
                                                <!-- Items will be added dynamically -->
                                            </div>
                                        </div>

                                        <!-- After Frying Column -->
                                        <div class="col-md-6 mb-4">
                                            <!-- <div class="text-center mb-4">
                                                <h6 class="font-weight-bold text-danger mb-0">after frying</h6>
                                            </div> -->
                                            
                                            <!-- Controls Row (No Input) -->
                                            <div class="d-flex align-items-center justify-content-center mb-3">
                                                    <div class="text-center mr-4">
                                                        <h6 class="font-weight-bold text-danger mb-1">after frying</h6>
                                                        <small class="text-muted">jumlah item : <span class="count-after-frying font-weight-bold text-danger">0</span></small>
                                                    </div>
                                                    <div class="d-flex">
                                                        <button type="button" class="btn btn-success btn-sm rounded-circle mr-1 add-after-frying" 
                                                                style="width: 28px; height: 28px; font-size: 12px;">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm rounded-circle remove-after-frying" 
                                                                style="width: 28px; height: 28px; font-size: 12px;">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="after-frying-items-container">
                                                <!-- Items will be added dynamically -->
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Remove Section Button -->
                                    <div class="text-right mb-3">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-after-berat-section" style="display: none;">
                                            <i class="fas fa-trash mr-1"></i> Hapus Section
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Add Berat Section Button -->
                                <div class="text-center mb-3">
                                    <button type="button" class="btn btn-outline-info btn-sm" id="add-after-berat-section">
                                        <i class="fas fa-plus mr-1"></i> Tambah Berat
                                    </button>
                                </div>
                                
                                <!-- Summary Row for After Forming/Frying -->
                                <div class="row">
                                    <!-- After Forming Summary -->
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0">
                                            <div class="card-body py-2 px-3">
                                                <h6 class="text-warning mb-2 text-center">After Forming</h6>
                                                <div class="row text-center">
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Jumlah</small>
                                                        <span id="jumlah-after-forming" class="font-weight-bold text-warning h6 mb-0">0</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Rata-rata</small>
                                                        <span id="rata-rata-after-forming" class="font-weight-bold text-info h6 mb-0">0.00 g</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- After Frying Summary -->
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0">
                                            <div class="card-body py-2 px-3">
                                                <h6 class="text-danger mb-2 text-center">After Frying</h6>
                                                <div class="row text-center">
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Jumlah</small>
                                                        <span id="jumlah-after-frying" class="font-weight-bold text-danger h6 mb-0">0</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Rata-rata</small>
                                                        <span id="rata-rata-after-frying" class="font-weight-bold text-warning h6 mb-0">0.00 g</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan Section -->
                        <div class="card card-outline card-secondary mb-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-sticky-note mr-2"></i>
                                    Catatan
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-0">
                                    <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                              id="catatan" name="catatan" rows="4" 
                                              placeholder="Masukkan catatan tambahan jika diperlukan...">{{ old('catatan') }}</textarea>
                                    @error('catatan')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Calculated Values Section -->
                    
                    <input type="hidden" class="form-control" 
                            id="rata_rata_dough" name="rata_rata_dough" readonly
                            placeholder="0.00" style="background-color: #f8f9fa;">
                
                    <input type="hidden" class="form-control" 
                            id="new_jumlah_dough" name="new_jumlah_dough" readonly
                            placeholder="0.00" style="background-color: #f8f9fa;">
                
                    <input type="hidden" class="form-control" 
                            id="rata_rata_filler" name="rata_rata_filler" readonly
                            placeholder="0.00" style="background-color: #f8f9fa;">
                        
                    <input type="hidden" class="form-control" 
                            id="new_jumlah_filler" name="new_jumlah_filler" readonly
                            placeholder="0.00" style="background-color: #f8f9fa;">
                
                    <input type="hidden" class="form-control" 
                            id="rata_rata_after_forming" name="rata_rata_after_forming" readonly
                            placeholder="0.00"   style="background-color: #f8f9fa;">
                
                    <input type="hidden" class="form-control" 
                            id="new_jumlah_after_forming" name="new_jumlah_after_forming" readonly
                            placeholder="0.00"   style="background-color: #f8f9fa;">
            
                    <input type="hidden" class="form-control" 
                            id="rata_rata_after_frying" name="rata_rata_after_frying" readonly
                            placeholder="0.00" style="background-color: #f8f9fa;">
                    
                    <input type="hidden" class="form-control" 
                            id="new_jumlah_after_frying" name="new_jumlah_after_frying" readonly
                            placeholder="0.00" style="background-color: #f8f9fa;">
                                       
                    <div class="card-footer bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary btn-md">
                                    <i class="fas fa-save mr-2"></i> Simpan Data
                                </button>
                                <button type="reset" class="btn btn-warning btn-md ml-2">
                                    <i class="fas fa-undo mr-2"></i> Reset
                                </button>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('pemeriksaan-rheon-machine.index') }}" class="btn btn-secondary btn-md">
                                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
{{-- JavaScript untuk Pemeriksaan Rheon Machine sudah dipindahkan ke app.blade.php --}}
@endsection
