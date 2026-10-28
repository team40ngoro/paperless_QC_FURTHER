
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Standar Berat Rheon</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('std-berat-rheon.index') }}">Standar Berat Rheon</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Standar Berat Rheon</h3>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('std-berat-rheon.update', $item->uuid) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Plan <span class="text-danger">*</span></label>
                                            <select name="id_plan" class="form-control @error('id_plan') is-invalid @enderror" required>
                                                <option value="">Pilih Plan</option>
                                                @foreach($plans as $plan)
                                                    <option value="{{ $plan->id }}" {{ old('id_plan', $item->id_plan) == $plan->id ? 'selected' : '' }}>{{ $plan->nama_plan }}</option>
                                                @endforeach
                                            </select>
                                            @error('id_plan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Produk <span class="text-danger">*</span></label>
                                            <select name="id_produk" class="form-control @error('id_produk') is-invalid @enderror" required>
                                                <option value="">Pilih Produk</option>
                                                @foreach($products as $produk)
                                                    <option value="{{ $produk->id }}" {{ old('id_produk', $item->id_produk) == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                                @endforeach
                                            </select>
                                            @error('id_produk')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Std Adonan <span class="text-danger">*</span></label>
                                            <input type="text" name="std_adonan" class="form-control @error('std_adonan') is-invalid @enderror" value="{{ old('std_adonan', $item->std_adonan) }}" required>
                                            @error('std_adonan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Std Filler</label>
                                            <input type="text" name="std_filler" class="form-control @error('std_filler') is-invalid @enderror" value="{{ old('std_filler', $item->std_filler) }}">
                                            @error('std_filler')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Std After Forming</label>
                                            <input type="text" name="std_after_forming" class="form-control @error('std_after_forming') is-invalid @enderror" value="{{ old('std_after_forming', $item->std_after_forming) }}">
                                            @error('std_after_forming')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Std After Frying</label>
                                            <input type="text" name="std_after_frying" class="form-control @error('std_after_frying') is-invalid @enderror" value="{{ old('std_after_frying', $item->std_after_frying) }}">
                                            @error('std_after_frying')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-warning mr-2">
                                        <i class="fas fa-save"></i> Update
                                    </button>
                                    <a href="{{ route('std-berat-rheon.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
