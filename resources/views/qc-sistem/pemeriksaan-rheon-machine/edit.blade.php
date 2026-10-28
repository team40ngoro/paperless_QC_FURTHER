@extends('layouts.app')

@section('title', 'Edit Pemeriksaan Rheon Machine')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Pemeriksaan Rheon Machine</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-rheon-machine.index') }}">Data Pemeriksaan Rheon Machine</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                            <i class="fas fa-edit mr-2"></i>
                            Edit Data Pemeriksaan Rheon Machine
                        </h3>
                        <!-- <div class="card-tools">
                            <a href="{{ route('pemeriksaan-rheon-machine.index') }}" class="btn btn-tool">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div> -->
                    </div>
                <form action="{{ route('pemeriksaan-rheon-machine.update', $pemeriksaanRheonMachine->uuid) }}" method="POST">
                    @csrf
                    @method('PUT')
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
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                Tanggal <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                                   id="tanggal" name="tanggal" value="{{ old('tanggal', $pemeriksaanRheonMachine->tanggal ? $pemeriksaanRheonMachine->tanggal->format('Y-m-d') : '') }}" required>
                                            @error('tanggal')
                                                <span class="invalid-feedback">{{ $message }}</span>
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
                                                    <option value="{{ $shift->id }}" {{ old('shift_id', $pemeriksaanRheonMachine->shift_id) == $shift->id ? 'selected' : '' }}>
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
                                                    <option value="{{ $item->id }}" {{ old('id_produk', $pemeriksaanRheonMachine->id_produk) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama_produk }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_produk')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <small id="std-berat-rheon-info" class="text-muted d-block" style="display:none;"></small>
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
                                                   id="batch" name="batch" value="{{ old('batch', $pemeriksaanRheonMachine->batch) }}" placeholder="Masukkan batch">
                                            @error('batch')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="display: none;">
                                        <div class="form-group">
                                            <label for="pukul">
                                                <i class="fas fa-clock mr-1"></i>
                                                Pukul
                                            </label>
                                            <input type="text" class="form-control @error('pukul') is-invalid @enderror" 
                                                   id="pukul" name="pukul" value="{{ old('pukul', $pemeriksaanRheonMachine->pukul) }}" placeholder="Contoh: 08:00">
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
                                                   id="inner" name="inner" value="{{ old('inner', $pemeriksaanRheonMachine->inner) }}" placeholder="Masukkan nilai inner">
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
                                                   id="outer" name="outer" value="{{ old('outer', $pemeriksaanRheonMachine->outer) }}" placeholder="Masukkan nilai outer">
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
                                                   id="belt" name="belt" value="{{ old('belt', $pemeriksaanRheonMachine->belt) }}" placeholder="Masukkan nilai belt">
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
                                                   id="extrusion_speed" name="extrusion_speed" value="{{ old('extrusion_speed', $pemeriksaanRheonMachine->extrusion_speed) }}" placeholder="Masukkan kecepatan">
                                            @error('extrusion_speed')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="jenis_cetakan">
                                                <i class="fas fa-shapes mr-1 text-danger"></i>
                                                Jenis Cetakan
                                            </label>
                                            <input type="text" class="form-control @error('jenis_cetakan') is-invalid @enderror" 
                                                   id="jenis_cetakan" name="jenis_cetakan" value="{{ old('jenis_cetakan', $pemeriksaanRheonMachine->jenis_cetakan) }}" placeholder="Masukkan jenis cetakan">
                                            @error('jenis_cetakan')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
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
                                              placeholder="Masukkan catatan tambahan jika diperlukan...">{{ old('catatan', $pemeriksaanRheonMachine->catatan) }}</textarea>
                                    @error('catatan')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Calculated Values Section -->
                        <div class="card card-outline card-info mb-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-calculator mr-2"></i>
                                    Nilai Rata-rata Terhitung
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Data Dough/Adonan -->
                                    <div class="col-md-6">
                                        <div class="card card-outline card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-bread-slice mr-2"></i>
                                                    Data Dough/Adonan
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                @if(!empty($pemeriksaanRheonMachine->berat_dough_adonan_array))
                                                    @foreach($pemeriksaanRheonMachine->berat_dough_adonan_array as $sectionIndex => $sectionData)
                                                        @if(is_array($sectionData) && !empty($sectionData))
                                                            <div class="mb-3">
                                                                <h6 class="text-primary">Section {{ $sectionIndex + 1 }}:</h6>
                                                                <div class="d-flex flex-wrap">
                                                                    @foreach($sectionData as $valueIndex => $value)
                                                                        <div class="mr-1 mb-1">
                                                                            <input type="number" 
                                                                                   name="input_dough_berat[{{ $sectionIndex }}][]" 
                                                                                   class="form-control form-control-sm editable-badge badge-primary" 
                                                                                   value="{{ $value }}" 
                                                                                   style="width: 60px; height: 30px; border-radius: 15px; text-align: center; color: white; background-color: #007bff; border: 1px solid #007bff;"
                                                                                   step="0.01">
                                                                        </div>
                                                                    @endforeach
                                                                    <button type="button" class="btn btn-sm btn-outline-primary add-dough-value" 
                                                                            data-section="{{ $sectionIndex }}" style="height: 30px; border-radius: 15px;">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <p class="text-muted text-center">Tidak ada data dough/adonan</p>
                                                @endif
                                                <!-- <div class="text-center mt-3">
                                                    <button type="button" class="btn btn-sm btn-primary add-dough-section">
                                                        <i class="fas fa-plus mr-1"></i> Tambah Section
                                                    </button>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Data Filler -->
                                    <div class="col-md-6">
                                        <div class="card card-outline card-success">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-cookie-bite mr-2"></i>
                                                    Data Filler
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                @if(!empty($pemeriksaanRheonMachine->berat_filler_array))
                                                    @foreach($pemeriksaanRheonMachine->berat_filler_array as $sectionIndex => $sectionData)
                                                        @if(is_array($sectionData) && !empty($sectionData))
                                                            <div class="mb-3">
                                                                <h6 class="text-success">Section {{ $sectionIndex + 1 }}:</h6>
                                                                <div class="d-flex flex-wrap">
                                                                    @foreach($sectionData as $valueIndex => $value)
                                                                        <div class="mr-1 mb-1">
                                                                            <input type="number" 
                                                                                   name="input_filler_berat[{{ $sectionIndex }}][]" 
                                                                                   class="form-control form-control-sm editable-badge badge-success" 
                                                                                   value="{{ $value }}" 
                                                                                   style="width: 60px; height: 30px; border-radius: 15px; text-align: center; color: white; background-color: #28a745; border: 1px solid #28a745;"
                                                                                   step="0.01">
                                                                        </div>
                                                                    @endforeach
                                                                    <button type="button" class="btn btn-sm btn-outline-success add-filler-value" 
                                                                            data-section="{{ $sectionIndex }}" style="height: 30px; border-radius: 15px;">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <p class="text-muted text-center">Tidak ada data filler</p>
                                                @endif
                                                <!-- <div class="text-center mt-3">
                                                    <button type="button" class="btn btn-sm btn-success add-filler-section">
                                                        <i class="fas fa-plus mr-1"></i> Tambah Section
                                                    </button>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- After Forming & After Frying Row -->
                                <div class="row mt-4">
                                    <!-- Data After Forming -->
                                    <div class="col-md-6">
                                        <div class="card card-outline card-warning">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-shapes mr-2"></i>
                                                    Data After Forming
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                @if(!empty($pemeriksaanRheonMachine->berat_after_forming_array))
                                                    @foreach($pemeriksaanRheonMachine->berat_after_forming_array as $sectionIndex => $sectionData)
                                                        @if(is_array($sectionData) && !empty($sectionData))
                                                            <div class="mb-3">
                                                                <h6 class="text-warning">Section {{ $sectionIndex + 1 }}:</h6>
                                                                <div class="d-flex flex-wrap">
                                                                    @foreach($sectionData as $valueIndex => $value)
                                                                        <div class="mr-1 mb-1">
                                                                            <input type="number" 
                                                                                   name="input_after_forming_berat[{{ $sectionIndex }}][]" 
                                                                                   class="form-control form-control-sm editable-badge badge-warning" 
                                                                                   value="{{ $value }}" 
                                                                                   style="width: 60px; height: 30px; border-radius: 15px; text-align: center; color: white; background-color: #ffc107; border: 1px solid #ffc107;"
                                                                                   step="0.01">
                                                                        </div>
                                                                    @endforeach
                                                                    <button type="button" class="btn btn-sm btn-outline-warning add-forming-value" 
                                                                            data-section="{{ $sectionIndex }}" style="height: 30px; border-radius: 15px;">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <p class="text-muted text-center">Tidak ada data after forming</p>
                                                @endif
                                                <!-- <div class="text-center mt-3">
                                                    <button type="button" class="btn btn-sm btn-warning add-forming-section">
                                                        <i class="fas fa-plus mr-1"></i> Tambah Section
                                                    </button>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Data After Frying -->
                                    <div class="col-md-6">
                                        <div class="card card-outline card-danger">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-fire mr-2"></i>
                                                    Data After Frying
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                @if(!empty($pemeriksaanRheonMachine->berat_after_frying_array))
                                                    @foreach($pemeriksaanRheonMachine->berat_after_frying_array as $sectionIndex => $sectionData)
                                                        @if(is_array($sectionData) && !empty($sectionData))
                                                            <div class="mb-3">
                                                                <h6 class="text-danger">Section {{ $sectionIndex + 1 }}:</h6>
                                                                <div class="d-flex flex-wrap">
                                                                    @foreach($sectionData as $valueIndex => $value)
                                                                        <div class="mr-1 mb-1">
                                                                            <input type="number" 
                                                                                   name="input_after_frying_berat[{{ $sectionIndex }}][]" 
                                                                                   class="form-control form-control-sm editable-badge badge-danger" 
                                                                                   value="{{ $value }}" 
                                                                                   style="width: 60px; height: 30px; border-radius: 15px; text-align: center; color: white; background-color: #dc3545; border: 1px solid #dc3545;"
                                                                                   step="0.01">
                                                                        </div>
                                                                    @endforeach
                                                                    <button type="button" class="btn btn-sm btn-outline-danger add-frying-value" 
                                                                            data-section="{{ $sectionIndex }}" style="height: 30px; border-radius: 15px;">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <p class="text-muted text-center">Tidak ada data after frying</p>
                                                @endif
                                                <!-- <div class="text-center mt-3">
                                                    <button type="button" class="btn btn-sm btn-danger add-frying-section">
                                                        <i class="fas fa-plus mr-1"></i> Tambah Section
                                                    </button>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Summary Section -->
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="card card-info">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-calculator mr-2"></i>
                                                    Ringkasan Perhitungan
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="rata_rata_dough">
                                                                <i class="fas fa-bread-slice mr-1 text-primary"></i>
                                                                Rata-rata Dough/Adonan
                                                            </label>
                                                            <input type="text" class="form-control" 
                                                                   id="edit_rata_rata_dough" name="rata_rata_dough" readonly
                                                                   value="{{ old('rata_rata_dough', $pemeriksaanRheonMachine->rata_rata_dough ?? '0.00') }}"
                                                                   style="background-color: #f8f9fa;">
                                                            <label for="new_jumlah_dough">
                                                                <i class="fas fa-bread-slice mr-1 text-primary"></i>
                                                               Jumlah Dough Adonan
                                                            </label>
                                                            <input type="text" class="form-control" 
                                                                   id="edit_new_jumlah_dough" name="new_jumlah_dough" readonly
                                                                   value="{{ old('new_jumlah_dough', $pemeriksaanRheonMachine->jumlah_dough ?? '0.00') }}"
                                                                   style="background-color: #f8f9fa;">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="rata_rata_filler">
                                                                <i class="fas fa-fill mr-1 text-success"></i>
                                                                Rata-rata Filler
                                                            </label>
                                                            <input type="text" class="form-control" 
                                                                   id="edit_rata_rata_filler" name="rata_rata_filler" readonly
                                                                   value="{{ old('rata_rata_filler', $pemeriksaanRheonMachine->rata_rata_filler ?? '0.00') }}"
                                                                   style="background-color: #f8f9fa;">
                                                                   <label for="new_jumlah_filler">
                                                                <i class="fas fa-bread-slice mr-1 text-primary"></i>
                                                               Jumlah Filler
                                                            </label>
                                                            <input type="text" class="form-control" 
                                                                   id="edit_new_jumlah_filler" name="new_jumlah_filler" readonly
                                                                   value="{{ old('new_jumlah_filler', $pemeriksaanRheonMachine->jumlah_filler ?? '0.00') }}"
                                                                   style="background-color: #f8f9fa;">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="rata_rata_after_forming">
                                                                <i class="fas fa-shapes mr-1 text-warning"></i>
                                                                Rata-rata After Forming
                                                            </label>
                                                            <input type="text" class="form-control" 
                                                                   id="edit_rata_rata_after_forming" name="rata_rata_after_forming" readonly
                                                                   value="{{ old('rata_rata_after_forming', $pemeriksaanRheonMachine->rata_rata_after_forming ?? '0.00') }}"
                                                                   style="background-color: #f8f9fa;">
                                                            <label for="new_jumlah_after_forming">
                                                                <i class="fas fa-shapes mr-1 text-warning"></i>
                                                              Jumlah After Forming
                                                            </label>
                                                            <input type="text" class="form-control" 
                                                                   id="edit_new_jumlah_after_forming" name="new_jumlah_after_forming" readonly
                                                                   value="{{ old('new_jumlah_after_forming', $pemeriksaanRheonMachine->jumlah_after_forming ?? '0.00') }}"
                                                                   style="background-color: #f8f9fa;">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="rata_rata_after_frying">
                                                                <i class="fas fa-fire mr-1 text-danger"></i>
                                                                Rata-rata After Frying
                                                            </label>
                                                            <input type="text" class="form-control" 
                                                                   id="edit_rata_rata_after_frying" name="rata_rata_after_frying" readonly
                                                                   value="{{ old('rata_rata_after_frying', $pemeriksaanRheonMachine->rata_rata_after_frying ?? '0.00') }}"
                                                                   style="background-color: #f8f9fa;">
                                                            <label for="new_jumlah_after_frying">
                                                                <i class="fas fa-fire mr-1 text-danger"></i>
                                                               Jumlah After Frying
                                                            </label>
                                                            <input type="text" class="form-control" 
                                                                   id="edit_new_jumlah_after_frying" name="new_jumlah_after_frying" readonly
                                                                   value="{{ old('new_jumlah_after_frying', $pemeriksaanRheonMachine->jumlah_after_frying ?? '0.00') }}"
                                                                   style="background-color: #f8f9fa;">
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
                    <div class="card-footer bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-warning btn-md">
                                    <i class="fas fa-save mr-2"></i> Update Data
                                </button>
                                <button type="reset" class="btn btn-danger ml-2 btn-md">
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

<!-- Meta data for edit form -->
<meta name="edit-mode" content="true">
<meta name="existing-dough-data" content="{{ json_encode($pemeriksaanRheonMachine->berat_dough_adonan_array) }}">
<meta name="existing-filler-data" content="{{ json_encode($pemeriksaanRheonMachine->berat_filler_array) }}">
<meta name="existing-after-forming-data" content="{{ json_encode($pemeriksaanRheonMachine->berat_after_forming_array) }}">
<meta name="existing-after-frying-data" content="{{ json_encode($pemeriksaanRheonMachine->berat_after_frying_array) }}">
@endsection

@section('scripts')
{{-- JavaScript untuk Pemeriksaan Rheon Machine sudah dipindahkan ke app.blade.php --}}
@endsection
