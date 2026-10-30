@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah STD Waktu Penggorengan Frayer 1,3,4,5</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('waktu-penggorengan.index') }}">Data STD Waktu Penggorengan Frayer 1,3,4,5</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
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
                            <h3 class="card-title">Tambah STD Data Waktu Penggorengan Frayer 1,3,4,5</h3>
                        </div>
                        <form action="{{ route('waktu-penggorengan.store') }}" method="POST">
                            @csrf
                            <div class="form-group mb-2">
                                <label>Nama Plan</label>
                                <select name="id_plan" id="id_plan_select" class="form-control" required>
                                    <option value="">Pilih Plan</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->nama_plan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label>Nama Produk</label>
                                <select name="id_produk" id="id_produk_select" class="form-control" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($produks as $produk)
                                        <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Suhu Frayer</label>
                                <select name="id_suhu_frayer_1" id="id_suhu_frayer_select" class="form-control" required>
                                    <option value="">Pilih Suhu Frayer</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Waktu Penggorengan</label>
                                <input type="text" name="waktu_penggorengan" class="form-control" required>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                <a href="{{ route('waktu-penggorengan.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection