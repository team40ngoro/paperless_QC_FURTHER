@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Data Suhu Frayer 2</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('suhu-frayer-2.index') }}">Suhu Frayer 2</a></li>
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
                <div class="col-md-12">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-edit"></i> Form Edit Suhu Frayer 2
                            </h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="POST" action="{{ route('suhu-frayer-2.update', $item->uuid) }}">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                @if($errors->any())
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="id_produk">
                                        <i class="fas fa-box"></i> Nama Produk <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('id_produk') is-invalid @enderror" id="id_produk" name="id_produk" required>
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" 
                                                {{ (old('id_produk', $item->id_produk) == $product->id) ? 'selected' : '' }}>
                                                {{ $product->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_produk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="suhu_frayer_2">Suhu Frayer 2</label>
                                    <input type="text" 
                                           name="suhu_frayer_2" 
                                           class="form-control @error('suhu_frayer_2') is-invalid @enderror" 
                                           id="suhu_frayer_2" 
                                           value="{{ old('suhu_frayer_2', $item->getRawOriginal('suhu_frayer_2') ?? '') }}" 
                                           required>
                                    @error('suhu_frayer_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                              <div class="form-group">
                                    <label for="waktu_penggorengan_2">
                                        <i class="fas fa-hourglass-half"></i> Waktu Penggorengan 2 (detik) <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                        name="waktu_penggorengan_2" 
                                        class="form-control @error('waktu_penggorengan_2') is-invalid @enderror" 
                                        id="waktu_penggorengan_2" 
                                        value="{{ old('waktu_penggorengan_2', $item->waktu_penggorengan_2 ?? '') }}" 
                                        placeholder="Masukkan waktu penggorengan 2" 
                                        required>
                                    @error('waktu_penggorengan_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update Data
                                </button>
                                <a href="{{ route('suhu-frayer-2.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection