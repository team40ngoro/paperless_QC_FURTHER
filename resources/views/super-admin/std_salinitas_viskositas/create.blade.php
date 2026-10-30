{{-- filepath: resources/views/super-admin/std_salinitas_viskositas/create.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Std Salinitas & Viskositas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('std-salinitas-viskositas.index') }}">Std Salinitas & Viskositas</a></li>
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
                            <h3 class="card-title">Tambah Data Std Salinitas & Viskositas</h3>
                        </div>
                        <form action="{{ route('std-salinitas-viskositas.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
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
                                            {{-- Akan diisi AJAX --}}
                                        </select>
                                </div>
                                <div class="mb-3">
                                    <label>Better</label>
                                    <select name="id_better" id="id_better_select"class=" form-control" required>
                                        <option value="">Pilih Better</option>
                                        @foreach($betters as $better)
                                            <option value="{{ $better->id }}">{{ $better->nama_better }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>Std Viskositas</label>
                                    <input type="text" name="std_viskositas" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Std Salinitas</label>
                                    <input type="text" name="std_salinitas" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Std Suhu Air</label>
                                    <input type="text" name="std_suhu_akhir" class="form-control" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                <a href="{{ route('std-salinitas-viskositas.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection