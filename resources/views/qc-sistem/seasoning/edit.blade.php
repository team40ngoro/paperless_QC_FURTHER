{{-- filepath: resources/views/qc-sistem/seasoning/edit.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Pemeriksaan Kedatangan Bahan Baku</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('seasoning.index') }}">Seasoning</a></li>
                            <li class="breadcrumb-item active">Edit Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Form Edit Penyimpanan Bahan Seasoning</h3>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon">
                                                <i class="fas fa-check" style="font-size: 2rem;"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Tanda Centang (✓)</span>
                                                <span class="info-box-number">Kemasan : tidak sobek.
                                                Sensori akan diverifikasi oleh QC pada halaman detail.
                                                </span>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                                </div>
                                                <!-- <span class="progress-description">
                                                    Kondisi baik dan memenuhi standar
                                                </span> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box bg-danger">
                                            <span class="info-box-icon">
                                                <i class="fas fa-times" style="font-size: 2rem;"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Tanda Silang (✗)</span>
                                                <span class="info-box-number">Parameter Tidak Sesuai</span>
                                                <div class="progress">
                                                    <div class="progress-bar bg-danger" style="width: 100%"></div>
                                                </div>
                                                <!-- <span class="progress-description">
                                                    Kondisi tidak sesuai standar
                                                </span> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-info mt-2">
                                    <i class="fas fa-lightbulb"></i>
                                    <strong>Petunjuk:</strong> Pilih tanda centang (✓) jika kondisi sesuai standar, dan tanda silang (✗) jika ditemukan ketidaksesuaian yang perlu diperbaiki.
                                </div>
                            </div>
                            <form class="form-horizontal mb-4" method="POST" action="{{ route('seasoning.update', $seasoning->uuid) }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- Informasi Dasar Card -->
                                            <div class="card card-primary card-outline">
                                                <div class="card-header">
                                                    <h3 class="card-title">Informasi Dasar</h3>
                                                </div>
                                                <div class="card-body">
                                                    {{-- dev_bagus_perbaikan_29_mei:remove disable form, hanya edit tanggal, jam di hide--}}
                                                    <div class="form-group row">
                                                        <label for="tanggal_seasoning_display" class="col-sm-3 col-form-label">
                                                            Tanggal <span class="text-danger">*</span>
                                                        </label>

                                                        <div class="col-sm-9">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                                </div>

                                                                <input type="date"
                                                                    id="tanggal_seasoning"
                                                                    name="tanggal_seasoning"
                                                                    class="form-control @error('tanggal_seasoning') is-invalid @enderror"
                                                                    value="{{ old('tanggal_seasoning', $seasoning->tanggal ? \Carbon\Carbon::parse($seasoning->tanggal)->format('Y-m-d') : '') }}"
                                                                    required>

                                                                @error('tanggal_seasoning')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="jam_seasoning">Jam <span class="text-danger">*</span></label>
                                                        <input type="time" class="form-control @error('jam_seasoning') is-invalid @enderror" id="jam_seasoning" name="jam_seasoning" value="{{ old('jam_seasoning', $seasoning->jam ? \Carbon\Carbon::parse($seasoning->jam)->format('H:i') : '') }}" required>
                                                        @error('jam_seasoning')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="shift_id" class="col-sm-3 col-form-label">Shift <span class="text-danger">*</span></label>
                                                        <div class="col-sm-9">
                                                            <select class="form-control select2" name="shift_id" required>
                                                                <option value="">Pilih Shift</option>
                                                                @foreach($shifts as $shift)
                                                                    <option value="{{ $shift->id }}" {{ $seasoning->shift_id == $shift->id ? 'selected' : '' }}>{{ $shift->shift }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Detail Bahan Card -->
                                            <div class="card card-primary card-outline mt-3">
                                                <div class="card-header">
                                                    <h3 class="card-title">Detail Bahan</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group row">
                                                        <label for="nama_rm" class="col-sm-3 col-form-label">Nama Seasoning <span class="text-danger">*</span></label>
                                                        <div class="col-sm-9">
                                                            <select name="nama_rm" id="id_produk_select" class="form-control select2" required>
                                                                <option value="">Pilih Nama Seasoning</option>
                                                                @foreach($dataSeasoning as $seasoning_data)
                                                                    <option value="{{ $seasoning_data->nama_seasoning }}" {{ $seasoning->nama_rm == $seasoning_data->nama_seasoning ? 'selected' : '' }}>{{ $seasoning_data->nama_seasoning }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="kode_produksi" class="col-sm-3 col-form-label">Kode Produksi <span class="text-danger">*</span></label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" name="kode_produksi" value="{{ old('kode_produksi', $seasoning->kode_produksi) }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="berat" class="col-sm-3 col-form-label">Berat Per Pack (kg) <span class="text-danger">*</span></label>
                                                        <div class="col-sm-9">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" name="berat" value="{{ old('berat', $seasoning->berat) }}" required>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">kg</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <!-- Pemeriksaan Kualitas Card -->
                                            <div class="card card-primary card-outline">
                                                <div class="card-header">
                                                    <h3 class="card-title">Pemeriksaan Kualitas</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group row">
                                                        <label for="kemasan" class="col-sm-3 col-form-label">Kemasan <span class="text-danger">*</span></label>
                                                        <div class="col-sm-9">
                                                            <select class="form-control" name="kemasan" required>
                                                                <option value="">Pilih Status Kemasan</option>
                                                                <option value="✔" {{ in_array($seasoning->kemasan, ['✔', '✓', 'OK', 'ok']) ? 'selected' : '' }}>✔ Baik</option>
                                                                <option value="✘" {{ in_array($seasoning->kemasan, ['✘', 'X', 'x', 'tidak ok', 'Tidak OK']) ? 'selected' : '' }}>✘ Tidak Baik</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="keterangan" class="col-sm-3 col-form-label">Keterangan</label>
                                                        <div class="col-sm-9">
                                                            <textarea class="form-control" name="keterangan" rows="4" placeholder="Masukkan keterangan tambahan">{{ old('keterangan', $seasoning->keterangan) }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Update Data
                                    </button>
                                    <a href="{{ route('seasoning.index') }}" class="btn btn-secondary ml-2">
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
</div>
@endsection