@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class=""><i class="fas fa-edit text-warning mr-2"></i>Edit List Peralatan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('list-peralatan.index') }}">List Peralatan</a></li>
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
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-dark">
                                <i class="fas fa-pencil-alt text-warning mr-2"></i>
                                Form Edit Master Data List Peralatan
                            </h3>
                        </div>

                        <form action="{{ route('list-peralatan.update', $item->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                @if($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                        <h5 class="alert-heading "><i class="fas fa-exclamation-triangle mr-2"></i>Terdapat kesalahan input:</h5>
                                        <ul class="mb-0 pl-3">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if(auth()->user()->role === 'superadmin')
                                <div class="form-group mb-3">
                                    <label for="id_plan_select" class="">Plan <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="id_plan_select" name="id_plan">
                                        <option value="">-- Pilih Plan --</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ (old('id_plan', $item->id_plan) == $plan->id) ? 'selected' : '' }}>
                                                {{ $plan->nama_plan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                <div class="form-group mb-4">
                                    <label for="id_area" class="">Area <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('id_area') is-invalid @enderror" id="id_area" name="id_area" required>
                                        <option value="">-- Pilih Area --</option>
                                        @foreach($areas as $area)
                                            <option value="{{ $area->id }}" data-plan-id="{{ $area->id_plan }}" {{ (old('id_area', $item->id_area) == $area->id) ? 'selected' : '' }}>
                                                {{ $area->area }} @if($area->plan) ({{ $area->plan->nama_plan }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_area')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label for="list_alat" class="">Nama Peralatan / Alat <span class="text-danger">*</span></label>
                                    <input type="text" name="list_alat" id="list_alat" 
                                           class="form-control text-uppercase @error('list_alat') is-invalid @enderror" 
                                           placeholder="Masukkan nama peralatan / alat" 
                                           value="{{ old('list_alat', $item->nama_alat) }}" required>
                                    @error('list_alat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label for="jumlah_alat" class="">Jumlah Alat <span class="text-danger">*</span></label>
                                    <input type="number" name="jumlah_alat" id="jumlah_alat" 
                                           class="form-control @error('jumlah_alat') is-invalid @enderror" 
                                           placeholder="Masukkan jumlah alat" 
                                           value="{{ old('jumlah_alat', $item->jumlah_alat ?? 1) }}" min="1" required>
                                    @error('jumlah_alat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="card-footer d-flex justify-content-between py-3">
                                <a href="{{ route('list-peralatan.index') }}" class="btn btn-secondary px-4">
                                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-warning px-4 text-dark">
                                    <i class="fas fa-save mr-2"></i>Update Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
