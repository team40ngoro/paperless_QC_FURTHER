@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Data Std Fan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('std-fan.index') }}"><i class="fas fa-fan"></i> Std Fan</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline shadow">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-plus"></i> Form Tambah Std Fan</h3>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-ban"></i> Terjadi Kesalahan!</h5>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('std-fan.store') }}" method="POST">
                                @csrf
                                
                                <div class="card card-outline card-info shadow">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-database"></i> Data Utama</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-clipboard-list text-info"></i> Plan <span class="text-danger">*</span></label>
                                                    <select name="id_plan" id="id_plan_std_fan" class="form-control" required>
                                                        <option value="">Pilih Plan</option>
                                                        @foreach($plans as $plan)
                                                            <option value="{{ $plan->id }}">{{ $plan->nama_plan }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-box text-primary"></i> Produk <span class="text-danger">*</span></label>
                                                    <select name="id_produk" id="id_produk_std_fan" class="form-control" required>
                                                        <option value="">Pilih Plan terlebih dahulu</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-thermometer-half text-warning"></i> Suhu Pemasakan <span class="text-danger">*</span></label>
                                                    <select name="id_suhu_blok" id="id_suhu_blok_std_fan" class="form-control" required>
                                                        <option value="">Pilih Produk terlebih dahulu</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-fan text-success"></i> Std Fan 1<span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" name="std_fan" class="form-control @error('std_fan') is-invalid @enderror" 
                                                               value="{{ old('std_fan') }}" placeholder="Masukkan std fan" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                        @error('std_fan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-fan text-primary"></i> Std Fan 2 <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" name="std_fan_2" class="form-control @error('std_fan_2') is-invalid @enderror" 
                                                               value="{{ old('std_fan_2') }}" placeholder="Masukkan std fan 2" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                        @error('std_fan_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-clock text-info"></i> Std Lama Proses <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" name="std_lama_proses" class="form-control @error('std_lama_proses') is-invalid @enderror" 
                                                               value="{{ old('std_lama_proses') }}" placeholder="Masukkan std lama proses" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">menit</span>
                                                        </div>
                                                        @error('std_lama_proses')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-fan text-secondary"></i> Std Fan 3</label>
                                                    <div class="input-group">
                                                        <input type="text" name="fan_3" class="form-control @error('fan_3') is-invalid @enderror" 
                                                               value="{{ old('fan_3') }}" placeholder="Masukkan std fan 3">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                        @error('fan_3')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
            	                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-fan text-secondary"></i> Std Fan 4</label>
                                                    <div class="input-group">
                                                        <input type="text" name="fan_4" class="form-control @error('fan_4') is-invalid @enderror" 
                                                               value="{{ old('fan_4') }}" placeholder="Masukkan std fan 4">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                        @error('fan_4')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-water text-teal"></i> Std Humidity/Steam Valve</label>
                                                    <div class="input-group">
                                                        <input type="text" name="std_humadity" class="form-control @error('std_humadity') is-invalid @enderror" 
                                                               value="{{ old('std_humadity') }}" placeholder="Masukkan std humidity/steam valve">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                        @error('std_humadity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card card-outline card-secondary">
                                            <div class="card-body text-center">
                                                <button type="submit" class="btn btn-primary btn-md mr-3">
                                                    <i class="fas fa-save"></i> Simpan Data
                                                </button>
                                                <a href="{{ route('std-fan.index') }}" class="btn btn-secondary btn-md">
                                                    <i class="fas fa-arrow-left"></i> Kembali
                                                </a>
                                            </div>
                                        </div>
                                    </div>
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