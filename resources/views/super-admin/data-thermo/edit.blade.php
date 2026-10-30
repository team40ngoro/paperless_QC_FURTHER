@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Data Thermometer</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('data-thermo.index') }}">Data Thermometer</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Edit Data Thermometer</h3>
                </div>
                <form action="{{ route('data-thermo.update', $item->uuid) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="id_plan">Plan <span class="text-danger">*</span></label>
                            <select name="id_plan" id="id_plan" class="form-control @error('id_plan') is-invalid @enderror" required>
                                <option value="">-- Pilih Plan --</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ (old('id_plan', $item->id_plan) == $plan->id) ? 'selected' : '' }}>
                                        {{ $plan->nama_plan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_plan')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nama_thermo">Nama Thermometer <span class="text-danger">*</span></label>
                            <input type="text" 
                                name="nama_thermo" 
                                id="nama_thermo" 
                                class="form-control @error('nama_thermo') is-invalid @enderror" 
                                value="{{ old('nama_thermo', $item->nama_thermo) }}" 
                                required>
                            @error('nama_thermo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="kode_thermo">Kode Thermometer <span class="text-danger">*</span></label>
                            <input type="text" 
                                name="kode_thermo" 
                                id="kode_thermo" 
                                class="form-control @error('kode_thermo') is-invalid @enderror" 
                                value="{{ old('kode_thermo', $item->kode_thermo) }}" 
                                required>
                            @error('kode_thermo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Kode harus unik dan tidak boleh sama dengan yang lain</small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                        <a href="{{ route('data-thermo.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection