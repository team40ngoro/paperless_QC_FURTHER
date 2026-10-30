@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-thermometer-half text-success"></i> Tambah Standar Suhu Pusat</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('std-suhu-pusat.index') }}"><i class="fas fa-thermometer-half"></i> Standar Suhu Pusat</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
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
                            <h3 class="card-title"><i class="fas fa-plus-circle"></i> Form Tambah Standar Suhu Pusat</h3>
                            <div class="card-tools">
                                <!-- <a href="{{ route('std-suhu-pusat.index') }}" class="btn btn-danger btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a> -->
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
                            <form action="{{ route('std-suhu-pusat.store') }}" method="POST">
                                @csrf
                                <div class="card card-outline card-info shadow mb-4">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-database"></i> Data Utama</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><i class="fas fa-clipboard-list text-info"></i> Plan <span class="text-danger">*</span></label>
                                                    <select name="id_plan" id="id_plan_select" class="form-control @error('id_plan') is-invalid @enderror" required>
                                                        <option value="">Pilih Plan</option>
                                                        @foreach($plans as $plan)
                                                            <option value="{{ $plan->id }}" {{ old('id_plan') == $plan->id ? 'selected' : '' }}>{{ $plan->nama_plan }}</option>
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
                                                    <select name="id_produk" id="id_produk_select" class="form-control @error('id_produk') is-invalid @enderror" required>
                                                        <option value="">Pilih Produk</option>
                                                        @foreach($products as $produk)
                                                            <option value="{{ $produk->id }}" {{ old('id_produk') == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
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
                                                <label><i class="fas fa-thermometer-half text-info"></i> Standar Suhu Pusat °C <span class="text-danger">*</span></label>
                                                <small class="text-muted d-block mb-2">Masukkan suhu untuk setiap Fryer (Format: 76-80)</small>
                                            </div>
                                        </div>

                                        <div id="fryer-container">
                                            @php
                                                $oldSuhuPusat = old('std_suhu_pusat');
                                                $oldSuhuPusat = is_array($oldSuhuPusat) ? $oldSuhuPusat : [];
                                            @endphp

                                            @if(count($oldSuhuPusat) > 0)
                                                @foreach($oldSuhuPusat as $index => $suhu)
                                                    <div class="row fryer-row mb-2" data-index="{{ $index }}">
                                                        <div class="col-md-2">
                                                            <label>Fryer {{ $index + 1 }} @if($index == 0)<span class="text-danger">*</span>@endif</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" name="std_suhu_pusat[]" class="form-control" placeholder="76-80" value="{{ $suhu }}" {{ $index == 0 ? 'required' : '' }}>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger btn-sm remove-fryer" {{ $index == 0 ? 'disabled' : '' }}>
                                                                <i class="fas fa-trash"></i> Hapus
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <!-- Fryer pertama (default) -->
                                                <div class="row fryer-row mb-2" data-index="0">
                                                    <div class="col-md-2">
                                                        <label>Fryer 1 <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" name="std_suhu_pusat[]" class="form-control" placeholder="76-80" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger btn-sm remove-fryer" disabled>
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        @if($errors->has('std_suhu_pusat') || $errors->has('std_suhu_pusat.*'))
                                            <div class="text-danger mt-1">
                                                {{ $errors->first('std_suhu_pusat') ?: $errors->first('std_suhu_pusat.*') }}
                                            </div>
                                        @endif

                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-primary btn-sm" id="add-fryer">
                                                    <i class="fas fa-plus"></i> Tambah Fryer
                                                </button>
                                                <small class="text-muted ml-2">Maksimal 10 Fryer</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card card-outline card-secondary">
                                            <div class="card-body text-center">
                                                <button type="submit" class="btn btn-primary btn-md mr-3">
                                                    <i class="fas fa-save"></i> Simpan
                                                </button>
                                                <a href="{{ route('std-suhu-pusat.index') }}" class="btn btn-secondary btn-md">
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