{{-- filepath: resources/views/super-admin/nomor_formula/edit.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Nomor Formula</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('nomor-formula.index') }}">Data Nomor Formula</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Nomor Formula</h3>
                        </div>
                        <form action="{{ route('nomor-formula.update', $formula->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="id_plan">Nama Plan</label>
                                    <select name="id_plan" id="id_plan" class="form-control" required>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ $formula->id_plan == $plan->id ? 'selected' : '' }}>{{ $plan->nama_plan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="id_produk">Nama Produk</label>
                                    <select name="id_produk" id="id_produk" class="form-control" required>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ $formula->id_produk == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="nomor_formula">Nomor Formula</label>
                                    <input type="text" name="nomor_formula" id="nomor_formula" class="form-control" value="{{ $formula->nomor_formula }}" placeholder="Masukkan Nomor Formula" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
                                <a href="{{ route('nomor-formula.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection