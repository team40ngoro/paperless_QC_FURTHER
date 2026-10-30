@extends('layouts.app')

@section('title', 'Edit Data Pemeriksaan Rice Bites')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Data Pemeriksaan Rice Bites</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-rice-bites.index') }}">Pemeriksaan Rice Bites</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form action="{{ route('pemeriksaan-rice-bites.update', $data->uuid) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Informasi Umum Section -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>Informasi Umum
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tanggal">
                                            <i class="fas fa-calendar mr-1"></i>Tanggal
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" name="tanggal" id="tanggal" 
                                               class="form-control @error('tanggal') is-invalid @enderror" 
                                               value="{{ old('tanggal', $data->tanggal ? $data->tanggal->format('Y-m-d') : '') }}" required>
                                        @error('tanggal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="jam">
                                            <i class="fas fa-clock mr-1"></i>Jam
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="time" name="jam" id="jam" 
                                               class="form-control @error('jam') is-invalid @enderror" 
                                               value="{{ old('jam', $data->jam ? \Carbon\Carbon::parse($data->jam)->format('H:i') : '') }}" required>
                                        @error('jam')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="shift_id">
                                            <i class="fas fa-user-clock mr-1"></i>Shift
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="shift_id" id="shift_id" class="form-control @error('shift_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Shift --</option>
                                            @foreach($shifts as $shift)
                                                <option value="{{ $shift->id }}" {{ old('shift_id', $data->shift_id) == $shift->id ? 'selected' : '' }}>
                                                    {{ $shift->shift }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('shift_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_produk">
                                            <i class="fas fa-box mr-1"></i>Nama Produk
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_produk" id="id_produk" class="form-control @error('id_produk') is-invalid @enderror" required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($produks as $item)
                                                <option value="{{ $item->id }}" {{ old('id_produk', $data->id_produk) == $item->id ? 'selected' : '' }}>
                                                    {{ $item->nama_produk }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_produk')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nomor_formula">
                                            <i class="fas fa-flask mr-1"></i>Nomor Formula
                                        </label>
                                        <input type="text" name="nomor_formula" id="nomor_formula"
                                               class="form-control @error('nomor_formula') is-invalid @enderror"
                                               value="{{ old('nomor_formula', $data->nomor_formula) }}"
                                               placeholder="Masukkan nomor formula">
                                        @error('nomor_formula')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="batch">
                                            <i class="fas fa-tags mr-1"></i>Batch No
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="batch" id="batch" 
                                               class="form-control @error('batch') is-invalid @enderror" 
                                               value="{{ old('batch', $data->batch) }}" 
                                               placeholder="Masukkan batch number" required>
                                        @error('batch')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="no_cooking_cycle">
                                            <i class="fas fa-recycle mr-1"></i>No Cooking Cycle
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="no_cooking_cycle" id="no_cooking_cycle" 
                                               class="form-control @error('no_cooking_cycle') is-invalid @enderror" 
                                               value="{{ old('no_cooking_cycle', $data->no_cooking_cycle) }}" 
                                               placeholder="Masukkan no cooking cycle" required>
                                        @error('no_cooking_cycle')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bahan Baku Section -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-seedling mr-2"></i>Bahan Baku
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="bahan-baku-container">
                                @php
                                    $bahanBakuArray = old('bahan_baku', $data->bahan_baku_array);
                                @endphp
                                @foreach($bahanBakuArray as $index => $bahanBaku)
                                <div class="row bahan-baku-item">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-wheat mr-1"></i>Bahan Baku
                                            </label>
                                            <input type="text" name="bahan_baku[{{ $index }}][nama]" 
                                                   class="form-control" 
                                                   placeholder="Input bahan" 
                                                   value="{{ $bahanBaku['nama'] ?? '' }}"
                                                   required readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Kode Produksi</label>
                                            <input type="text" name="bahan_baku[{{ $index }}][kode_produksi]" 
                                                   class="form-control" 
                                                   placeholder="-"
                                                   value="{{ $bahanBaku['kode_produksi'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Berat</label>
                                            <div class="input-group">
                                                <input type="text" name="bahan_baku[{{ $index }}][berat]" 
                                                       class="form-control" 
                                                       placeholder="0"
                                                       value="{{ $bahanBaku['berat'] ?? '' }}"
                                                       readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">kg</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Suhu</label>
                                            <div class="input-group">
                                                <input type="text" name="bahan_baku[{{ $index }}][suhu]" 
                                                       class="form-control" 
                                                       placeholder="0"
                                                       value="{{ $bahanBaku['suhu'] ?? '' }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">°C</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Kondisi</label>
                                            <select name="bahan_baku[{{ $index }}][kondisi]" class="form-control">
                                                <option value="" {{ ($bahanBaku['kondisi'] ?? '') === '' ? 'selected' : '' }}>-- Pilih Kondisi --</option>
                                                <option value="OK" {{ ($bahanBaku['kondisi'] ?? '') === 'OK' ? 'selected' : '' }}>OK</option>
                                                <option value="Not OK" {{ ($bahanBaku['kondisi'] ?? '') === 'Not OK' ? 'selected' : '' }}>Not OK</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div class="input-group">
                                                @if($index == 0)
                                                <button type="button" class="btn btn-success btn-sm add-bahan-baku">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                @else
                                                <button type="button" class="btn btn-danger btn-sm remove-bahan-baku">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Parameter Proses Section -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs mr-2"></i>Parameter Proses
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="parameter_nitrogen">
                                            <i class="fas fa-atom mr-1"></i>Parameter Nitrogen
                                        </label>
                                        <input type="text" name="parameter_nitrogen" id="parameter_nitrogen" 
                                               class="form-control @error('parameter_nitrogen') is-invalid @enderror" 
                                               value="{{ old('parameter_nitrogen', $data->parameter_nitrogen) }}" 
                                               placeholder="0">
                                        @error('parameter_nitrogen')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="jumlah_inject_nitrogen">
                                            <i class="fas fa-syringe mr-1"></i>Jumlah Inject Nitrogen
                                        </label>
                                        <input type="text" name="jumlah_inject_nitrogen" id="jumlah_inject_nitrogen" 
                                               class="form-control @error('jumlah_inject_nitrogen') is-invalid @enderror" 
                                               value="{{ old('jumlah_inject_nitrogen', $data->jumlah_inject_nitrogen) }}" 
                                               placeholder="0">
                                        @error('jumlah_inject_nitrogen')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="rpm_cooking_cattle">
                                            <i class="fas fa-tachometer-alt mr-1"></i>RPM Cooking Cattle
                                        </label>
                                        <div class="input-group">
                                            <input type="text" name="rpm_cooking_cattle" id="rpm_cooking_cattle" 
                                                   class="form-control @error('rpm_cooking_cattle') is-invalid @enderror" 
                                                   value="{{ old('rpm_cooking_cattle', $data->rpm_cooking_cattle) }}" 
                                                   placeholder="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text">RPM</span>
                                            </div>
                                        </div>
                                        @error('rpm_cooking_cattle')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cold Mixing Section -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-snowflake mr-2"></i>Cold Mixing
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="cold_mixing">
                                    <i class="fas fa-snowflake mr-1"></i>Cold Mixing
                                </label>
                                <input type="text" name="cold_mixing" id="cold_mixing" 
                                       class="form-control @error('cold_mixing') is-invalid @enderror" 
                                       value="{{ old('cold_mixing', $data->cold_mixing) }}" 
                                       placeholder="Masukkan data cold mixing">
                                @error('cold_mixing')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Suhu Aktual Adonan Section -->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-thermometer-half mr-2"></i>Suhu Aktual Adonan (3 Titik)
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="suhu-adonan-container">
                                @php
                                    $suhuAktualArray = old('suhu_aktual_adonan', $data->suhu_aktual_adonan_array);
                                @endphp
                                @if(empty($suhuAktualArray))
                                    @php $suhuAktualArray = ['']; @endphp
                                @endif
                                @foreach($suhuAktualArray as $index => $suhu)
                                <div class="row suhu-adonan-item">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-thermometer-half mr-1"></i>Suhu Aktual Adonan Titik {{ $index + 1 }}
                                            </label>
                                            <div class="input-group">
                                                <input type="text" name="suhu_aktual_adonan[{{ $index }}]" 
                                                       class="form-control" 
                                                       placeholder="0"
                                                       value="{{ $suhu }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">°C</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div class="input-group">
                                                @if($index == 0)
                                                <button type="button" class="btn btn-success btn-sm add-suhu-adonan">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                @else
                                                <button type="button" class="btn btn-danger btn-sm remove-suhu-adonan">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="alert alert-info mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                <small>Maksimal 3 titik pengukuran suhu adonan</small>
                            </div>
                        </div>
                    </div>

                    <!-- Suhu Adonan Setelah Pencampuran Section -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-temperature-high mr-2"></i>Suhu Adonan Setelah Pencampuran
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="suhu-pencampuran-container">
                                @php
                                    $suhuPencampuranArray = old('suhu_adonan_pencampuran', $data->suhu_adonan_pencampuran_array);
                                @endphp
                                @if(empty($suhuPencampuranArray))
                                    @php $suhuPencampuranArray = ['']; @endphp
                                @endif
                                @foreach($suhuPencampuranArray as $index => $suhu)
                                <div class="row suhu-pencampuran-item">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-temperature-high mr-1"></i>Suhu Adonan Setelah Pencampuran {{ $index + 1 }}
                                            </label>
                                            <div class="input-group">
                                                <input type="text" name="suhu_adonan_pencampuran[{{ $index }}]" 
                                                       class="form-control" 
                                                       placeholder="0"
                                                       value="{{ $suhu }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">°C</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div class="input-group">
                                                @if($index == 0)
                                                <button type="button" class="btn btn-success btn-sm add-suhu-pencampuran">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                @else
                                                <button type="button" class="btn btn-danger btn-sm remove-suhu-pencampuran">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="alert alert-info mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                <small>Maksimal 6 pengukuran suhu adonan setelah pencampuran</small>
                            </div>
                        </div>
                    </div>

                    <!-- Rata-rata Section -->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-calculator mr-2"></i>Rata-rata & Hasil Pencampuran
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="rata_rata_suhu">
                                            <i class="fas fa-chart-line mr-1"></i>Rata-rata Suhu Pencampuran
                                        </label>
                                        <div class="input-group">
                                            <input type="text" name="rata_rata_suhu" id="rata_rata_suhu" 
                                                   class="form-control @error('rata_rata_suhu') is-invalid @enderror" 
                                                   value="{{ old('rata_rata_suhu', $data->rata_rata_suhu) }}" 
                                                   placeholder="0" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">°C</span>
                                            </div>
                                        </div>
                                        @error('rata_rata_suhu')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Otomatis dihitung dari suhu adonan setelah pencampuran</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hasil_pencampuran">
                                            <i class="fas fa-check-circle mr-1"></i>Hasil Pencampuran
                                        </label>
                                        <select name="hasil_pencampuran" id="hasil_pencampuran" class="form-control @error('hasil_pencampuran') is-invalid @enderror">
                                            <option value="">-- Pilih Hasil --</option>
                                            <option value="OK" {{ old('hasil_pencampuran', $data->hasil_pencampuran) == 'OK' ? 'selected' : '' }}>
                                                <i class="fas fa-check"></i> OK
                                            </option>
                                            <option value="Tidak OK" {{ old('hasil_pencampuran', $data->hasil_pencampuran) == 'Tidak OK' ? 'selected' : '' }}>
                                                <i class="fas fa-times"></i> Tidak OK
                                            </option>
                                        </select>
                                        @error('hasil_pencampuran')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Section -->
                    <div class="card card-dark">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-sticky-note mr-2"></i>Catatan
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="catatan">
                                    <i class="fas fa-comment mr-1"></i>Catatan
                                </label>
                                <textarea name="catatan" id="catatan" rows="4" 
                                          class="form-control @error('catatan') is-invalid @enderror" 
                                          placeholder="Masukkan catatan...">{{ old('catatan', $data->catatan) }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status Verifikasi Section -->
                    <!-- <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clipboard-check mr-2"></i>Status Verifikasi
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="diverifikasi_qc">
                                            <i class="fas fa-user-check mr-1"></i>Diverifikasi oleh QC
                                        </label>
                                        <input type="text" name="diverifikasi_qc" id="diverifikasi_qc" 
                                               class="form-control @error('diverifikasi_qc') is-invalid @enderror" 
                                               value="{{ old('diverifikasi_qc', $data->diverifikasi_qc) }}" 
                                               readonly>
                                        <input type="hidden" name="diverifikasi_qc_status" value="{{ $data->diverifikasi_qc_status }}">
                                        @error('diverifikasi_qc')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="diketahui_produksi">
                                            <i class="fas fa-industry mr-1"></i>Diketahui oleh Produksi
                                        </label>
                                        <input type="text" name="diketahui_produksi" id="diketahui_produksi" 
                                               class="form-control @error('diketahui_produksi') is-invalid @enderror" 
                                               value="{{ old('diketahui_produksi', $data->diketahui_produksi) }}" 
                                               readonly>
                                        <input type="hidden" name="diketahui_produksi_status" value="{{ $data->diketahui_produksi_status }}">
                                        @error('diketahui_produksi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <!-- Submit Button -->
                    <div class="card">
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning btn-md">
                                <i class="fas fa-save"></i> Update Data
                            </button>
                            <a href="{{ route('pemeriksaan-rice-bites.index') }}" class="btn btn-secondary btn-md">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

@endsection
