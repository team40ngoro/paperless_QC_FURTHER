@extends('layouts.app')

@section('title', 'Edit Bahan Formula FLA')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Bahan Formula FLA</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/super-admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bahan-formula-fla.index') }}">Data Bahan Formula FLA</a></li>
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
                            <h3 class="card-title">Form Edit Bahan Formula FLA</h3>
                        </div>
                        
                        <form action="{{ route('bahan-formula-fla.update', $item->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Meta tags for JavaScript initialization -->
                            <meta name="current-product-id" content="{{ $item->namaFormulaFla->id_produk ?? '' }}">
                            <meta name="current-formula-id" content="{{ $item->id_nama_formula_fla ?? '' }}">
                            <meta name="current-step-id" content="{{ $item->id_nomor_step_formula_fla ?? '' }}">
                            
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
                                                    <option value="{{ $product->id }}" 
                                                            {{ old('id_produk', $item->namaFormulaFla->id_produk ?? '') == $product->id ? 'selected' : '' }}>
                                                        {{ $product->nama_produk }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_produk')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Nama Formula FLA -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="id_nama_formula_fla">Nama Formula FLA <span class="text-danger">*</span></label>
                                            <select name="id_nama_formula_fla" id="id_nama_formula_fla" 
                                                    class="form-control @error('id_nama_formula_fla') is-invalid @enderror" required>
                                                <option value="">Pilih Nama Formula FLA</option>
                                            </select>
                                            @error('id_nama_formula_fla')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Hidden input for Step Formula FLA -->
                                    <input type="hidden" name="id_nomor_step_formula_fla" id="id_nomor_step_formula_fla" value="{{ old('id_nomor_step_formula_fla', $item->id_nomor_step_formula_fla) }}">
                                </div>

                                <hr>

                                <!-- Dynamic Bahan Formula FLA -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Bahan Formula FLA <span class="text-danger">*</span></label>
                                            <div id="bahan-container">
                                                @php
                                                    $bahanArray = old('bahan_formula_fla', $item->getBahanFormulaArray());
                                                @endphp
                                                @if(!empty($bahanArray))
                                                    @foreach($bahanArray as $index => $bahan)
                                                        <div class="input-group mb-2 bahan-item">
                                                            <input type="text" name="bahan_formula_fla[]" 
                                                                   class="form-control @error('bahan_formula_fla.*') is-invalid @enderror" 
                                                                   placeholder="Masukkan bahan formula FLA" 
                                                                   value="{{ $bahan }}" required>
                                                            <div class="input-group-append">
                                                                @if($index == 0)
                                                                    <button type="button" class="btn btn-success add-bahan">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                @else
                                                                    <button type="button" class="btn btn-danger remove-bahan">
                                                                        <i class="fas fa-minus"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
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
                                                @endif
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
                                                @php
                                                    $beratArray = old('berat_formula_fla', $item->getBeratFormulaArray());
                                                @endphp
                                                @if(!empty($beratArray))
                                                    @foreach($beratArray as $index => $berat)
                                                        <div class="input-group mb-2 berat-item">
                                                            <input type="text" name="berat_formula_fla[]" 
                                                                   class="form-control @error('berat_formula_fla.*') is-invalid @enderror" 
                                                                   placeholder="Masukkan berat formula FLA" 
                                                                   value="{{ $berat }}" required>
                                                            <div class="input-group-append">
                                                                @if($index == 0)
                                                                    <button type="button" class="btn btn-success add-berat">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                @else
                                                                    <button type="button" class="btn btn-danger remove-berat">
                                                                        <i class="fas fa-minus"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
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
                                                @endif
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
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update
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
