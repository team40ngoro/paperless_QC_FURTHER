@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Jenis Marinade</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('jenis-marinade.index') }}">Data Jenis Marinade</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Data Jenis Marinade</h3>
                        </div>
                        <form action="{{ route('jenis-marinade.update', $jenis_marinade->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group mb-2">
                                    <label>Nama Plan</label>
                                    <select name="id_plan" class="form-control" required>
                                        <option value="">Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ $jenis_marinade->id_plan == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->nama_plan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Nama Produk</label>
                                    <select name="id_produk" class="form-control" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach($jenis_produk as $produk)
                                            <option value="{{ $produk->id }}" {{ $jenis_marinade->id_produk == $produk->id ? 'selected' : '' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Jenis Marinade</label>
                                    <input type="text" name="jenis_marinade" class="form-control" 
                                           value="{{ old('jenis_marinade', $jenis_marinade->jenis_marinade) }}" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                                <a href="{{ route('jenis-marinade.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection