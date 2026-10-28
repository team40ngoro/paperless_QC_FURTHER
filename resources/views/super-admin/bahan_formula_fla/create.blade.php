@extends('layouts.app')

@section('title', 'Tambah Bahan Formula FLA')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Bahan Formula FLA</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/super-admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bahan-formula-fla.index') }}">Data Bahan Formula FLA</a></li>
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
                            <h3 class="card-title">Form Tambah Bahan Formula FLA</h3>
                        </div>
                        
                        <form action="{{ route('bahan-formula-fla.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <!-- Nama Produk -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="id_produk">Nama Produk <span class="text-danger">*</span></label>
                                            <select name="id_produk" id="id_produk" 
                                                    class="form-control @error('id_produk') is-invalid @enderror">
                                                <option value="">Pilih Nama Produk</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" {{ old('id_produk') == $product->id ? 'selected' : '' }}>
                                                        {{ $product->nama_produk }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_produk')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            
                                            @if($products->isEmpty())
                                                <small class="text-danger">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Input nama formula FLA terlebih dahulu
                                                </small>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Nama Formula FLA -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="id_nama_formula_fla">Nama Formula FLA <span class="text-danger">*</span></label>
                                            <select name="id_nama_formula_fla" id="id_nama_formula_fla" 
                                                    class="form-control @error('id_nama_formula_fla') is-invalid @enderror" required disabled>
                                                <option value="">Pilih Nama Formula FLA</option>
                                            </select>
                                            @error('id_nama_formula_fla')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Hidden input for step formula (null) -->
                                    <input type="hidden" name="id_nomor_step_formula_fla" value="">
                                </div>

                                <div class="row">
                                    <!-- Proses -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="proses">Proses <small class="text-muted">(Pilih maksimal 2 proses)</small></label>
                                            <div class="row">
                                                @php
                                                    $prosesOptions = ['sauted', 'mixing', 'stirring'];
                                                @endphp
                                                @foreach($prosesOptions as $option)
                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input proses-checkbox" type="checkbox" 
                                                                   name="proses[]" value="{{ $option }}" id="proses_{{ $option }}"
                                                                   {{ in_array($option, old('proses', [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="proses_{{ $option }}">
                                                                {{ ucfirst($option) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @error('proses')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            @error('proses.*')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Dynamic Bahan Formula FLA -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Bahan Formula FLA <span class="text-danger">*</span></label>
                                            <div id="bahan-container">
                                                <div class="input-group mb-2 bahan-item">
                                                    <input type="text" name="bahan_formula_fla[]" 
                                                           class="form-control @error('bahan_formula_fla.*') is-invalid @enderror" 
                                                           placeholder="Masukkan bahan formula FLA" required>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-success add-bahan">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            @error('bahan_formula_fla')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            @error('bahan_formula_fla.*')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Dynamic Berat Formula FLA -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Berat Formula FLA <span class="text-danger">*</span></label>
                                            <div id="berat-container">
                                                <div class="input-group mb-2 berat-item">
                                                    <input type="text" name="berat_formula_fla[]" 
                                                           class="form-control @error('berat_formula_fla.*') is-invalid @enderror" 
                                                           placeholder="Masukkan berat formula FLA" required>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-success add-berat">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            @error('berat_formula_fla')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            @error('berat_formula_fla.*')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <a href="{{ route('bahan-formula-fla.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
