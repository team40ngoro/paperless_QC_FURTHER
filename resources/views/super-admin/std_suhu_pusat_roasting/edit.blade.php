@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-thermometer-half text-warning"></i> Edit Standar Suhu Pusat Roasting</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('std-suhu-pusat-roasting.index') }}"><i class="fas fa-thermometer-half"></i> Standar Suhu Pusat Roasting</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline shadow">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-edit"></i> Form Edit Standar Suhu Pusat Roasting</h3>
                            <div class="card-tools">
                                <!-- <a href="{{ route('std-suhu-pusat-roasting.index') }}" class="btn btn-danger btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a> -->
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-ban"></i> Terjadi Kesalahan!</h5>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{ route('std-suhu-pusat-roasting.update', $item->uuid) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card card-outline card-info shadow mb-4">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-database"></i> Data Utama</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><i class="fas fa-clipboard-list text-info"></i> Plan <span class="text-danger">*</span></label>
                                                    <select name="id_plan" id="id_plan_select" class="form-control" required>
                                                        <option value="">Pilih Plan</option>
                                                        @foreach($plans as $plan)
                                                            <option value="{{ $plan->id }}" 
                                                                {{ (old('id_plan') ?? $item->id_plan) == $plan->id ? 'selected' : '' }}>
                                                                {{ $plan->nama_plan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('id_plan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><i class="fas fa-box text-info"></i> Nama Produk <span class="text-danger">*</span></label>
                                                    <select name="id_produk" id="id_produk_select" class="form-control" required>
                                                        <option value="">Pilih Produk</option>
                                                        @foreach($products as $produk)
                                                            <option value="{{ $produk->id }}" 
                                                                {{ (old('id_produk') ?? $item->id_produk) == $produk->id ? 'selected' : '' }}>
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
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label><i class="fas fa-thermometer-half text-info"></i> Standar Suhu Pusat Roasting °C<span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" name="std_suhu_pusat_roasting" 
                                                               class="form-control @error('std_suhu_pusat_roasting') is-invalid @enderror" 
                                                               placeholder="Masukan Standar Suhu Pusat Roasting" 
                                                               value="{{ old('std_suhu_pusat_roasting') ?? $item->std_suhu_pusat_roasting }}" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">°C</span>
                                                        </div>
                                                    </div>
                                                    @error('std_suhu_pusat_roasting')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card card-outline card-secondary">
                                            <div class="card-body text-center">
                                                <button type="submit" class="btn btn-warning btn-md mr-3">
                                                    <i class="fas fa-save"></i> Update
                                                </button>
                                                <a href="{{ route('std-suhu-pusat-roasting.index') }}" class="btn btn-secondary btn-md">
                                                    <i class="fas fa-arrow-left"></i> Kembali
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
        </div>
    </section>
</div>
@endsection