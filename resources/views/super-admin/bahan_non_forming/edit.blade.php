@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Bahan Non Forming</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bahan-non-forming.index') }}">Data Bahan Non Forming</a></li>
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
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Bahan Non Forming</h3>
                        </div>
                        <form action="{{ route('bahan-non-forming.update', $bahanNonForming->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group mb-2">
                                    <label>Nama Plan</label>
                                    <select name="id_plan" id="id_plan_select" class="form-control" required>
                                        <option value="">Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ $bahanNonForming->id_plan == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->nama_plan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group mb-2">
                                    <label>Produk</label>
                                    <input type="text" class="form-control" readonly
                                           value="{{ $bahanNonForming->produkNonForming->produk->nama_produk ?? '' }}">
                                    <input type="hidden" name="id_produk" value="{{ $bahanNonForming->produkNonForming->id_produk ?? '' }}">
                                    <small class="form-text text-muted">Produk tidak dapat diubah</small>
                                </div>
                                
                                <div class="form-group mb-2">
                                    <label>Nomor Formula</label>
                                    <input type="text" name="nomor_formula" class="form-control" 
                                           value="{{ old('nomor_formula', $bahanNonForming->produkNonForming->nomor_formula ?? '') }}">
                                </div>
                                
                                <!-- Single RM Form -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Data Raw Material (RM)</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Nama RM</label>
                                                    <input type="text" name="nama_rm" class="form-control" 
                                                           placeholder="Masukkan Nama RM" 
                                                           value="{{ old('nama_rm', $bahanNonForming->nama_rm) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Berat RM (kg)</label>
                                                    <input type="text" name="berat_rm" class="form-control" 
                                                           placeholder="000" 
                                                           value="{{ old('berat_rm', $bahanNonForming->berat_rm) }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update
                                </button>
                                <a href="{{ route('bahan-non-forming.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="button" class="btn btn-danger float-right" 
                                        onclick="if(confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                                            window.location.href='{{ route('bahan-non-forming.destroy', $bahanNonForming->uuid) }}'
                                        }">
                                    <i class="fas fa-trash"></i> Hapus
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