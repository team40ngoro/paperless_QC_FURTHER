{{-- filepath: resources/views/super-admin/data-timbangan/edit.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Data Timbangan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('data-timbangan.index') }}">Data Timbangan</a></li>
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
                            <h3 class="card-title">Form Edit Data Timbangan</h3>
                        </div>
                        <form action="{{ route('data-timbangan.update', $item->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                
                                <div class="form-group">
                                    <label for="id_plan">Plan <span class="text-danger">*</span></label>
                                    <select name="id_plan" id="id_plan" class="form-control @error('id_plan') is-invalid @enderror" required>
                                        <option value="">Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ (old('id_plan', $item->id_plan) == $plan->id) ? 'selected' : '' }}>
                                                {{ $plan->nama_plan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="nama_timbangan">Nama Timbangan <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_timbangan" id="nama_timbangan" 
                                           class="form-control @error('nama_timbangan') is-invalid @enderror" 
                                           placeholder="Masukkan Nama Timbangan" 
                                           value="{{ old('nama_timbangan', $item->nama_timbangan) }}" required>
                                    @error('nama_timbangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="kode_timbangan">Kode Timbangan <span class="text-danger">*</span></label>
                                    <input type="text" name="kode_timbangan" id="kode_timbangan" 
                                           class="form-control @error('kode_timbangan') is-invalid @enderror" 
                                           placeholder="Masukkan Kode Timbangan" 
                                           value="{{ old('kode_timbangan', $item->kode_timbangan) }}" required>
                                    @error('kode_timbangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kode harus unik dan tidak boleh sama dengan yang lain</small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update
                                </button>
                                <a href="{{ route('data-timbangan.index') }}" class="btn btn-secondary">
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