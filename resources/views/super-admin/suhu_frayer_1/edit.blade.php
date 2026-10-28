@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Suhu Frayer</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('suhu-frayer-1.index') }}">Data Suhu Frayer</a></li>
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
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Data Suhu Frayer</h3>
                        </div>
                        <form action="{{ route('suhu-frayer-1.update', $data->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Plan</label>
                                    <select name="id_plan" id="id_plan_select" class="form-control" required>
                                        <option value="">Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ $data->id_plan == $plan->id ? 'selected' : '' }}>{{ $plan->nama_plan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Produk</label>
                                    <select name="id_produk" id="id_produk_select" class="form-control" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ $data->id_produk == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>STD Suhu Frayer 1</label>
                                    <input type="text" name="suhu_frayer" class="form-control" value="{{ $data->suhu_frayer }}" required>
                                </div>
                               <div class="form-group">
                                    <label>STD Waktu Penggorengan 1 (detik)</label>
                                    <input type="text" name="waktu_penggorengan_1" class="form-control" value="{{ $data->waktu_penggorengan_1 }}" placeholder="Masukkan waktu penggorengan 1">
                                </div>
                                <div class="form-group">
                                    <label>STD Suhu Frayer 2</label>
                                    <input type="text" name="suhu_frayer_2" class="form-control" value="{{ $data->suhu_frayer_2 }}" placeholder="Masukkan suhu frayer 2">
                                </div>
                                <div class="form-group">
                                    <label>STD Waktu Penggorengan 2 (detik)</label>
                                    <input type="text" name="waktu_penggorengan_2" class="form-control" value="{{ $data->waktu_penggorengan_2 }}" placeholder="Masukkan waktu penggorengan 2">
                                </div>
                                <div class="form-group">
                                    <label>STD Suhu Frayer 3</label>
                                    <input type="text" name="suhu_frayer_3" class="form-control" value="{{ $data->suhu_frayer_3 }}">
                                </div>
                                <div class="form-group">
                                    <label>STD Waktu Penggorengan 3 (detik)</label>
                                    <input type="text" name="waktu_penggorengan_3" class="form-control" value="{{ $data->waktu_penggorengan_3 }}" placeholder="Masukkan waktu penggorengan 3">
                                </div>
                                <div class="form-group">
                                    <label>STD Suhu Frayer 4</label>
                                    <input type="text" name="suhu_frayer_4" class="form-control" value="{{ $data->suhu_frayer_4 }}">
                                </div>
                                <div class="form-group">
                                    <label>STD Waktu Penggorengan 4 (detik)</label>
                                    <input type="text" name="waktu_penggorengan_4" class="form-control" value="{{ $data->waktu_penggorengan_4 }}" placeholder="Masukkan waktu penggorengan 4">
                                </div>
                                <div class="form-group">
                                    <label>STD Suhu Frayer 5</label>
                                    <input type="text" name="suhu_frayer_5" class="form-control" value="{{ $data->suhu_frayer_5 }}">
                                </div>
                                <div class="form-group">
                                    <label>STD Waktu Penggorengan 5 (detik)</label>
                                    <input type="text" name="waktu_penggorengan_5" class="form-control" value="{{ $data->waktu_penggorengan_5 }}" placeholder="Masukkan waktu penggorengan 5">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                                <a href="{{ route('suhu-frayer-1.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection