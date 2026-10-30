@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-box"></i> Tambah Data Standart BOX</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('data-box.index') }}">Data Standart BOX</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-box"></i> Form Tambah Data Standart BOX</h3>
                    </div>
                    <form action="{{ route('data-box.store') }}" method="POST">
                        @csrf
                    <div class="card-body"> 
                        <div class="form-group">
                            <label for="id_plan">Plan</label>
                            <select name="id_plan" id="id_plan_select" class="form-control" required>
                                <option value="">Pilih Plan</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ old('id_plan') == $plan->id ? 'selected' : '' }}>{{ $plan->nama_plan }}</option>
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
                                        <option value="{{ $produk->id }}" {{ old('id_produk') == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
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
                                <label for="std_box">Standart BOX</label>
                                <input type="text" name="std_box" id="std_box" class="form-control @error('std_box') is-invalid @enderror" value="{{ old('std_box') }}" required>
                                @error('std_box')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="std_isi_pack_karton">Standar Isi Pack dalam Karton</label>
                                <input type="number" name="std_isi_pack_karton" id="std_isi_pack_karton" class="form-control @error('std_isi_pack_karton') is-invalid @enderror" value="{{ old('std_isi_pack_karton') }}">
                                @error('std_isi_pack_karton')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                            <a href="{{ route('data-box.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection