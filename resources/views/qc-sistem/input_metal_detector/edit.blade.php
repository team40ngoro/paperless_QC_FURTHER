@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Pemeriksaan Metal Detector</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('input-metal-detector.index') }}"><i class="fas fa-warehouse"></i> Penyimpanan Bahan</a></li>
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
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-edit"></i> Form Edit Pemeriksaan Metal Detector</h3>
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

                            <form class="form-horizontal" id="main-metal-detector-form" method="POST" action="{{ route('input-metal-detector.update', $metalDetector->uuid) }}">
                                @csrf
                                @method('PUT')
                                <div class="card card-outline card-info shadow-sm">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-clipboard-check"></i> Edit Pemeriksaan Metal Detector</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="text-info mb-3"><i class="fas fa-search"></i> Data Pemeriksaan</h5>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Shift <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" name="id_shift" required>
                                                        <option value="">Pilih Shift</option>
                                                        @foreach($shifts as $shift)
                                                            <option value="{{ $shift->id }}" {{ old('id_shift', $metalDetector->id_shift) == $shift->id ? 'selected' : '' }}>{{ $shift->shift }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Line <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" name="line" required>
                                                        <option value="">Pilih Line</option>
                                                        @for($i = 1; $i <= 8; $i++)
                                                            <option value="{{ $i }}" {{ old('line', $metalDetector->line) == $i ? 'selected' : '' }}>Line {{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                                        id="tanggal" name="tanggal" 
                                                        value="{{ old('tanggal', $metalDetector->tanggal ? \Carbon\Carbon::parse($metalDetector->tanggal)->format('Y-m-d') : '') }}" required>
                                                    @error('tanggal')
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">
                                                        <i class="fas fa-clock"></i> Jam <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="time" class="form-control" 
                                                        name="jam" 
                                                        value="{{ old('jam', $metalDetector->jam ? \Carbon\Carbon::parse($metalDetector->jam)->format('H:i') : '') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" name="id_produk" required>
                                                        <option value="">Pilih Produk</option>
                                                        @foreach($produks as $produk)
                                                            <option value="{{ $produk->id }}" {{ old('id_produk', $metalDetector->id_produk) == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label">Kode Produksi <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="kode_produksi" value="{{ old('kode_produksi', $metalDetector->kode_produksi) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label">Berat Produk (gr) <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="berat_produk" id="berat_produk_edit" required>
                                                        <option value="">Pilih Nilai</option>
                                                        @foreach([55, 100, 200, 225, 250, 300, 315, 400, 450, 500, 900, 1000,1100, 2000] as $berat)
                                                            <option value="{{ $berat }}" {{ old('berat_produk', $metalDetector->berat_produk) == $berat ? 'selected' : '' }}>{{ $berat }} gram</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12"><hr></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="font-weight-bold text-info">Fe 1.5 mm</label>
                                                    <select class="form-control mb-2 @error('fe_depan_aktual') is-invalid @enderror" name="fe_depan_aktual" required>
                                                        <option value="">Depan</option>
                                                        <option value="✔" {{ old('fe_depan_aktual', $metalDetector->fe_depan_aktual) == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                        <option value="✘" {{ old('fe_depan_aktual', $metalDetector->fe_depan_aktual) == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                                    </select>
                                                    @error('fe_depan_aktual')
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror

                                                    <select class="form-control mb-2 @error('fe_tengah_aktual') is-invalid @enderror" name="fe_tengah_aktual" required>
                                                        <option value="">Tengah</option>
                                                        <option value="✔" {{ old('fe_tengah_aktual', $metalDetector->fe_tengah_aktual) == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                        <option value="✘" {{ old('fe_tengah_aktual', $metalDetector->fe_tengah_aktual) == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                                    </select>
                                                    @error('fe_tengah_aktual')
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror

                                                    <select class="form-control @error('fe_belakang_aktual') is-invalid @enderror" name="fe_belakang_aktual" required>
                                                        <option value="">Belakang</option>
                                                        <option value="✔" {{ old('fe_belakang_aktual', $metalDetector->fe_belakang_aktual) == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                        <option value="✘" {{ old('fe_belakang_aktual', $metalDetector->fe_belakang_aktual) == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                                    </select>
                                                    @error('fe_belakang_aktual')
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="font-weight-bold text-success">Non Fe 2 mm</label>
                                                <div class="form-group">
                                                    <select class="form-control mb-2 @error('non_fe_depan_aktual') is-invalid @enderror" name="non_fe_depan_aktual" required>
                                                        <option value="">Depan</option>
                                                        <option value="✔" {{ old('non_fe_depan_aktual', $metalDetector->non_fe_depan_aktual) == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                        <option value="✘" {{ old('non_fe_depan_aktual', $metalDetector->non_fe_depan_aktual) == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                                    </select>
                                                    @error('non_fe_depan_aktual')
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror

                                                    <select class="form-control mb-2 @error('non_fe_tengah_aktual') is-invalid @enderror" name="non_fe_tengah_aktual" required>
                                                        <option value="">Tengah</option>
                                                        <option value="✔" {{ old('non_fe_tengah_aktual', $metalDetector->non_fe_tengah_aktual) == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                        <option value="✘" {{ old('non_fe_tengah_aktual', $metalDetector->non_fe_tengah_aktual) == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                                    </select>
                                                    @error('non_fe_tengah_aktual')
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror

                                                    <select class="form-control @error('non_fe_belakang_aktual') is-invalid @enderror" name="non_fe_belakang_aktual" required>
                                                        <option value="">Belakang</option>
                                                        <option value="✔" {{ old('non_fe_belakang_aktual', $metalDetector->non_fe_belakang_aktual) == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                        <option value="✘" {{ old('non_fe_belakang_aktual', $metalDetector->non_fe_belakang_aktual) == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                                    </select>
                                                    @error('non_fe_belakang_aktual')
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="font-weight-bold text-warning">SUS 316 2.5 mm</label>
                                                <div class="form-group">
                                                    <select class="form-control mb-2 @error('sus_depan_aktual') is-invalid @enderror" name="sus_depan_aktual" required>
                                                        <option value="">Depan</option>
                                                        <option value="✔" {{ old('sus_depan_aktual', $metalDetector->sus_depan_aktual) == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                        <option value="✘" {{ old('sus_depan_aktual', $metalDetector->sus_depan_aktual) == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                                    </select>
                                                    @error('sus_depan_aktual')
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror

                                                    <select class="form-control mb-2 @error('sus_tengah_aktual') is-invalid @enderror" name="sus_tengah_aktual" required>
                                                        <option value="">Tengah</option>
                                                        <option value="✔" {{ old('sus_tengah_aktual', $metalDetector->sus_tengah_aktual) == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                        <option value="✘" {{ old('sus_tengah_aktual', $metalDetector->sus_tengah_aktual) == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                                    </select>
                                                    @error('sus_tengah_aktual')
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror

                                                    <select class="form-control @error('sus_belakang_aktual') is-invalid @enderror" name="sus_belakang_aktual" required>
                                                        <option value="">Belakang</option>
                                                        <option value="✔" {{ old('sus_belakang_aktual', $metalDetector->sus_belakang_aktual) == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                        <option value="✘" {{ old('sus_belakang_aktual', $metalDetector->sus_belakang_aktual) == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                                    </select>
                                                    @error('sus_belakang_aktual')
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="form-label">Keterangan</label>
                                                    <textarea class="form-control" name="keterangan" rows="2" placeholder="Keterangan tambahan">{{ old('keterangan', $metalDetector->keterangan) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card card-outline card-secondary">
                                            <div class="card-body text-center">
                                                <button type="submit" class="btn btn-warning btn-md mr-2">
                                                    <i class="fas fa-save"></i> Update Data
                                                </button>
                                                <a href="{{ route('input-metal-detector.index') }}" class="btn btn-secondary btn-md">
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