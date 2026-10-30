{{-- filepath: resources/views/super-admin/nomor_emulsi/edit.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Nomor Emulsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('nomor-emulsi.index') }}">Nomor Emulsi</a></li>
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
                            <h3 class="card-title">Edit Data Nomor Emulsi</h3>
                        </div>
                        <form action="{{ route('nomor-emulsi.update', $item->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Nomor Emulsi</label>
                                    <input type="text" name="nomor_emulsi" class="form-control" value="{{ $item->nomor_emulsi }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Total Pemakaian</label>
                                    <select name="total_pemakaian_id" class="form-control" required>
                                        @foreach($totalPemakaians as $pemakaian)
                                            <option value="{{ $pemakaian->id }}" {{ $item->total_pemakaian_id == $pemakaian->id ? 'selected' : '' }}>
                                               {{ $pemakaian->total_pemakaian }} - {{ $pemakaian->produk->nama_produk ?? 'Produk Tidak Ditemukan' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Produk</label>
                                    <select name="id_produk" class="form-control" required>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ $item->id_produk == $produk->id ? 'selected' : '' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Nama Emulsi</label>
                                    <select name="nama_emulsi_id" class="form-control" required>
                                        @foreach($emulsis as $emulsi)
                                            <option value="{{ $emulsi->id }}" {{ $item->nama_emulsi_id == $emulsi->id ? 'selected' : '' }}>
                                                    {{ $emulsi->nama_emulsi }} - {{ $emulsi->produk->nama_produk ?? 'Produk Tidak Ditemukan' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Plan</label>
                                    <select name="id_plan" class="form-control" required>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ $item->id_plan == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->nama_plan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
                                <a href="{{ route('nomor-emulsi.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection