@extends('layouts.app')

@section('title', 'Edit Nomor Step Formula FLA')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Nomor Step Formula FLA</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/super-admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('nomor-step-formula-fla.index') }}">Data Nomor Step Formula FLA</a></li>
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
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Nomor Step Formula FLA</h3>
                        </div>
                        
                        <form action="{{ route('nomor-step-formula-fla.update', $item->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <!-- Nama Produk -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_produk">Nama Produk <span class="text-danger">*</span></label>
                                            <select name="id_produk" id="id_produk" 
                                                    class="form-control @error('id_produk') is-invalid @enderror">
                                                <option value="">Pilih Nama Produk</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" 
                                                            {{ old('id_produk', $item->namaFormulaFla->id_produk ?? '') == $product->id ? 'selected' : '' }}>
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_nama_formula_fla">Nama Formula FLA <span class="text-danger">*</span></label>
                                            <select name="id_nama_formula_fla" id="id_nama_formula_fla" 
                                                    class="form-control @error('id_nama_formula_fla') is-invalid @enderror" required>
                                                <option value="">Pilih Nama Formula FLA</option>
                                                @foreach($namaFormulaFlas as $namaFormula)
                                                    <option value="{{ $namaFormula->id }}" 
                                                            {{ old('id_nama_formula_fla', $item->id_nama_formula_fla) == $namaFormula->id ? 'selected' : '' }}>
                                                        {{ $namaFormula->nama_formula_fla }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_nama_formula_fla')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            
                                            @if($namaFormulaFlas->isEmpty())
                                                <small class="text-danger">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Input nama formula FLA terlebih dahulu
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Nomor Step -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nomor_step">Nomor Step <span class="text-danger">*</span></label>
                                            <input type="number" name="nomor_step" id="nomor_step" 
                                                   class="form-control @error('nomor_step') is-invalid @enderror" 
                                                   value="{{ old('nomor_step', $item->nomor_step) }}" 
                                                   placeholder="Masukkan nomor step" required min="1">
                                            @error('nomor_step')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update
                                </button>
                                <a href="{{ route('nomor-step-formula-fla.index') }}" class="btn btn-secondary">
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
