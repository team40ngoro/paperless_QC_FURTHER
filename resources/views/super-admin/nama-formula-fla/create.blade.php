{{-- filepath: resources/views/super-admin/nama-formula-fla/create.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Formula FLA</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('nama-formula-fla.index') }}">Data Formula FLA</a></li>
                        <li class="breadcrumb-item active">Tambah Formula FLA</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Form Tambah Formula FLA</h3>
                        </div>

                        <form action="{{ route('nama-formula-fla.store') }}" method="POST">
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

                              

                                <div class="form-group">
                                    <label for="id_produk">Produk <span class="text-danger">*</span></label>
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
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="nama_formula_fla">Nama Formula FLA <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nama_formula_fla') is-invalid @enderror" 
                                           id="nama_formula_fla" 
                                           name="nama_formula_fla" 
                                           value="{{ old('nama_formula_fla') }}" 
                                           placeholder="Masukkan nama formula FLA"
                                           required>
                                    @error('nama_formula_fla')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <a href="{{ route('nama-formula-fla.index') }}" class="btn btn-secondary">
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
