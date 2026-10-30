@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-box"></i> Tambah Data Standart BAG PACK</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('data-bag.index') }}">Data Standart BAG PACK</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-box"></i> Form Tambah Data Standart BAG PACK</h3>
                </div>
                <form action="{{ route('data-bag.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="id_plan">Plan</label>
                            <select name="id_plan" id="id_plan_select" class="form-control" required>
                                <option value="">Pilih Plan</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->nama_plan }}</option>
                                @endforeach
                            </select>
                            @error('id_plan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="id_produk">Produk</label>
                            <select name="id_produk" id="id_produk_select" class="form-control" required>
                                <option value="">Pilih Produk</option>
                                @foreach($produks as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                @endforeach
                            </select>
                            @error('id_produk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                          <div class="form-group">
                              <label class="form-label">Berat Produk (gr) <span class="text-danger">*</span></label>
                              <select id="nilai_select_berat" class="form-control" name="berat_produk"></select>
                            
                          </div>
                        <div class="form-group">
                            <label for="std_bag">Standart BAG</label>
                            <input type="text" name="std_bag" id="std_bag" class="form-control" value="{{ old('std_bag') }}" placeholder="Masukkan Standart BAG">
                            @error('std_bag')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                        <a href="{{ route('data-bag.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection