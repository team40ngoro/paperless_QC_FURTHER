@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Total Pemakaian Emulsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('total-pemakaian-emulsi.index') }}">Total Pemakaian Emulsi</a></li>
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
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Edit Data Total Pemakaian Emulsi</h3>
                        </div>
                        <form action="{{ route('total-pemakaian-emulsi.update', $item->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="id_plan_select">Nama Plan</label>
                                    <select name="id_plan" id="id_plan_select" class="form-control @error('id_plan') is-invalid @enderror" required>
                                        <option selected disabled>Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ $item->id_plan == $plan->id ? 'selected' : '' }}>{{ $plan->nama_plan }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_plan')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="id_produk_select">Nama Produk</label>
                                    <select name="id_produk" id="id_produk_select" class="form-control @error('id_produk') is-invalid @enderror" required>
                                        <option selected disabled>Pilih Produk</option>
                                    </select>
                                    @error('id_produk')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="nama_emulsi_id_select">Nama Emulsi</label>
                                    <select name="nama_emulsi_id" id="nama_emulsi_id_select" class="form-control @error('nama_emulsi_id') is-invalid @enderror" required>
                                        @foreach($emulsis as $emulsi)
                                            <option value="{{ $emulsi->id }}" {{ $item->nama_emulsi_id == $emulsi->id ? 'selected' : '' }}>
                                                {{ $emulsi->nama_emulsi }}
                                            </option>
                                        @endforeach

                                    </select>
                                    @error('nama_emulsi_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="total_pemakaian">Total Pemakaian</label>
                                    <input type="text" name="total_pemakaian" id="total_pemakaian" class="form-control @error('total_pemakaian') is-invalid @enderror" value="{{ old('total_pemakaian', $item->total_pemakaian) }}" placeholder="Masukkan total pemakaian (contoh: 1 kali, 2 kali, 375)" required>
                                    @error('total_pemakaian')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
                                <a href="{{ route('total-pemakaian-emulsi.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection