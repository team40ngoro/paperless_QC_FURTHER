@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class=" text-success"></i> Tambah Data Suhu Pemasakan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('suhu-blok.index') }}"><i class="fas fa-thermometer-half"></i> Suhu Pemasakan</a></li>
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
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-edit"></i> Form Input Suhu Pemasakan</h3>
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

                            <form action="{{ route('suhu-blok.store') }}" method="POST">
                                @csrf
                                
                                <div class="card card-outline card-info shadow-sm">
                                    <div class="card-header bg-gradient-info">
                                        <h3 class="card-title"><i class="fas fa-database"></i> Data Utama</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-clipboard-list text-info"></i> Plan <span class="text-danger">*</span></label>
                                                    <select name="id_plan" id="id_plan_select" class="form-control" required>
                                                        <option value="">Pilih Plan</option>
                                                        @foreach($plans as $plan)
                                                            <option value="{{ $plan->id }}">{{ $plan->nama_plan }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-box text-primary"></i> Produk <span class="text-danger">*</span></label>
                                                    <select name="id_produk" id="id_produk_select" class="form-control" required>
                                                        <option value="">Pilih Produk</option>
                                                        {{-- Akan diisi AJAX --}}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-thermometer-half text-warning"></i> Suhu Pemasakan <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" name="suhu_blok" class="form-control form-control-border" 
                                                               value="{{ old('suhu_blok') }}" placeholder="Masukkan suhu pemasakan" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">°C</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card card-outline card-secondary">
                                            <div class="card-body text-center">
                                                <button type="submit" class="btn btn-primary btn-md mr-3">
                                                    <i class="fas fa-save"></i> Simpan Data
                                                </button>
                                                <a href="{{ route('suhu-blok.index') }}" class="btn btn-secondary btn-md">
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