@extends('layouts.app')

@section('title', 'Tambah Data Pemeriksaan Rice Bites')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Data Pemeriksaan Rice Bites</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-rice-bites.index') }}">Pemeriksaan Rice Bites</a></li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <form action="{{ route('pemeriksaan-rice-bites.store') }}" method="POST">
                    @csrf
                    
                    <!-- Basic Information -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>Informasi Dasar
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                        
                                        <input type="date" name="tanggal" id="tanggal" 
                                            class="form-control @error('tanggal') is-invalid @enderror" 
                                            value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                        @error('tanggal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                               <div class="col-md-6">
                                  <div class="form-group">
                                        <label for="jam">Jam <span class="text-danger">*</span></label>
                                        <input type="time" name="jam" id="jam" 
                                            class="form-control @error('jam') is-invalid @enderror" 
                                            value="{{ old('jam', date('H:i')) }}" required>
                                        @error('jam')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                               </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="shift_id">
                                            <i class="fas fa-clock mr-1"></i>Shift
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="shift_id" id="shift_id" class="form-control @error('shift_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Shift --</option>
                                            @foreach($shifts as $shift)
                                                <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                    {{ $shift->shift }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('shift_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                     <div class="form-group">
                                        <label for="id_produk">
                                            <i class="fas fa-box mr-1"></i>Nama Produk
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_produk" id="id_produk" class="form-control @error('id_produk') is-invalid @enderror" required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($produks as $item)
                                                <option value="{{ $item->id }}" {{ old('id_produk') == $item->id ? 'selected' : '' }}>
                                                    {{ $item->nama_produk }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_produk')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6" id="formula-group" style="display:none;">
                                     <div class="form-group">
                                        <label for="nomor_formula">
                                            <i class="fas fa-flask mr-1"></i>Nomor Formula
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="nomor_formula" id="nomor_formula" class="form-control @error('nomor_formula') is-invalid @enderror">
                                            <option value="">-- Pilih Formula --</option>
                                        </select>
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
                                               value="{{ old('batch') }}" 
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
                                               value="{{ old('no_cooking_cycle') }}" 
                                               placeholder="Masukkan no cooking cycle" required>
                                        @error('no_cooking_cycle')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                
                            </div>
                        </div>
                    </div>

                    <!-- Bahan Baku Section -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-seedling mr-2"></i>Bahan Baku
                            </h3>
                            <div class="card-tools">
                                <span id="bahan-baku-status" class="badge badge-secondary">
                                    <i class="fas fa-info-circle mr-1"></i>Pilih produk terlebih dahulu
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Alert info otomatis --}}
                            <div class="alert alert-info alert-dismissible py-2" id="bahan-baku-info-alert">
                                <i class="fas fa-magic mr-1"></i>
                                <strong>Otomatis dari Master Data:</strong> Bahan baku akan terisi otomatis berdasarkan produk yang dipilih. Anda dapat mengubah nilai <strong>Berat</strong>, <strong>Suhu</strong>, dan <strong>Kondisi</strong>.
                            </div>

                            <div id="bahan-baku-loading" class="text-center py-3" style="display:none;">
                                <i class="fas fa-spinner fa-spin fa-2x text-info"></i>
                                <p class="mt-2 text-muted">Memuat data bahan baku...</p>
                            </div>

                            <div id="bahan-baku-container">
                                {{-- Diisi oleh JavaScript berdasarkan produk yang dipilih --}}
                                @if(old('bahan_baku'))
                                    @foreach(old('bahan_baku') as $idx => $bb)
                                    <div class="row bahan-baku-item border-bottom pb-2 mb-2">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>
                                                    <i class="fas fa-wheat mr-1"></i>Bahan Baku
                                                </label>
                                                <select name="bahan_baku[{{ $idx }}][nama]"
                                                        class="form-control bahan-baku-nama" required>
                                                    <option value="">-- Pilih Bahan Baku --</option>
                                                    @foreach($bahanBakuMaster->flatten()->unique('nama_bahan_baku') as $master)
                                                        <option value="{{ $master->nama_bahan_baku }}"
                                                            {{ ($bb['nama'] ?? '') === $master->nama_bahan_baku ? 'selected' : '' }}>
                                                            {{ $master->nama_bahan_baku }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Kode Produksi</label>
                                                <input type="text" name="bahan_baku[{{ $idx }}][kode_produksi]"
                                                       class="form-control"
                                                       value="{{ $bb['kode_produksi'] ?? '' }}"
                                                       placeholder="-">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Berat</label>
                                                <div class="input-group">
                                                    <input type="text" name="bahan_baku[{{ $idx }}][berat]"
                                                           class="form-control"
                                                           value="{{ $bb['berat'] ?? '' }}"
                                                           placeholder="0">
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
                                                    <input type="text" name="bahan_baku[{{ $idx }}][suhu]"
                                                           class="form-control"
                                                           value="{{ $bb['suhu'] ?? '' }}"
                                                           placeholder="0">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">°C</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Kondisi</label>
                                                <div class="input-group">
                                                    <select name="bahan_baku[{{ $idx }}][kondisi]"
                                                            class="form-control">
                                                        <option value="" {{ ($bb['kondisi'] ?? '') === '' ? 'selected' : '' }}>-- Pilih Kondisi --</option>
                                                        <option value="OK" {{ ($bb['kondisi'] ?? '') === 'OK' ? 'selected' : '' }}>OK</option>
                                                        <option value="Not OK" {{ ($bb['kondisi'] ?? '') === 'Not OK' ? 'selected' : '' }}>Not OK</option>
                                                    </select>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-danger btn-sm remove-bahan-baku"
                                                            {{ $idx == 0 ? 'style=display:none' : '' }}>
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center">
                                            @if($idx == count(old('bahan_baku')) - 1)
                                            <button type="button" class="btn btn-success btn-sm add-bahan-baku-manual">
                                                <i class="fas fa-plus mr-1"></i> Tambah
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="row bahan-baku-empty text-center py-4">
                                        <div class="col-12 text-muted">
                                            <i class="fas fa-box-open fa-2x mb-2"></i>
                                            <p>Pilih produk untuk memuat daftar bahan baku secara otomatis</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-2" id="btn-tambah-bahan-baku-manual-wrap" style="display:none;">
                                <button type="button" class="btn btn-outline-success btn-sm add-bahan-baku-manual">
                                    <i class="fas fa-plus-circle mr-1"></i> Tambah Bahan Baku Manual
                                </button>
                            </div>
                        </div>
                    </div>


                    <!-- Parameter Proses Section -->
                    <div class="card card-warning">
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
                                               value="{{ old('parameter_nitrogen') }}" 
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
                                               value="{{ old('jumlah_inject_nitrogen') }}" 
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
                                                   value="{{ old('rpm_cooking_cattle') }}" 
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
                                       value="{{ old('cold_mixing') }}" 
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
                                <div class="row suhu-adonan-item">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-thermometer-half mr-1"></i>Suhu Aktual Adonan Titik 1
                                            </label>
                                            <div class="input-group">
                                                <input type="text" name="suhu_aktual_adonan[0]" 
                                                       class="form-control" 
                                                       placeholder="0">
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
                                                <button type="button" class="btn btn-success btn-sm add-suhu-adonan">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                <div class="row suhu-pencampuran-item">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-temperature-high mr-1"></i>Suhu Adonan Setelah Pencampuran 1
                                            </label>
                                            <div class="input-group">
                                                <input type="text" name="suhu_adonan_pencampuran[0]" 
                                                       class="form-control" 
                                                       placeholder="0">
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
                                                <button type="button" class="btn btn-success btn-sm add-suhu-pencampuran">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                                   value="{{ old('rata_rata_suhu') }}" 
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
                                            <option value="OK" {{ old('hasil_pencampuran') == 'OK' ? 'selected' : '' }}>
                                                <i class="fas fa-check"></i> OK
                                            </option>
                                            <option value="Tidak OK" {{ old('hasil_pencampuran') == 'Tidak OK' ? 'selected' : '' }}>
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
                                          placeholder="Masukkan catatan...">{{ old('catatan') }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status Verifikasi Section -->
                    <div class="card card-warning">
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
                                               value="Belum diverifikasi oleh QC" 
                                               readonly>
                                        <input type="hidden" name="diverifikasi_qc_status" value="0">
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
                                               value="Belum diketahui oleh produksi" 
                                               readonly>
                                        <input type="hidden" name="diketahui_produksi_status" value="0">
                                        @error('diketahui_produksi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="card">
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Data
                            </button>
                            <a href="{{ route('pemeriksaan-rice-bites.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>


@push('scripts')
<script>
$(document).ready(function () {

    // ============================================================
    // BAHAN BAKU AUTO-POPULATE dari Master Data
    // ============================================================

    const ajaxUrl = '{{ route("ajax.bahan-baku-rice-bites-by-produk", ["id_produk" => ":id"]) }}';
    const formulaUrl = '{{ route("ajax.formula-rice-bites-by-produk", ["id_produk" => ":id"]) }}';
    let bahanBakuIndex = 0;

    /**
     * Load Nomor Formula berdasarkan id_produk
     */
    function loadFormulas(idProduk) {
        const $formulaGroup = $('#formula-group');
        const $formulaSelect = $('#nomor_formula');
        
        if (!idProduk) {
            $formulaGroup.hide();
            $formulaSelect.html('<option value="">-- Pilih Formula --</option>').val('');
            return;
        }

        $.ajax({
            url: formulaUrl.replace(':id', idProduk),
            type: 'GET',
            success: function(data) {
                if (data.length > 0) {
                    let options = '<option value="">-- Pilih Formula --</option>';
                    data.forEach(function(formula) {
                        options += `<option value="${formula}">${formula}</option>`;
                    });
                    $formulaSelect.html(options);
                    $formulaGroup.show();
                    
                    // Jika hanya ada 1 formula, pilih otomatis
                    if (data.length === 1) {
                        $formulaSelect.val(data[0]).trigger('change');
                    }
                } else {
                    $formulaGroup.hide();
                    $formulaSelect.html('<option value="">-- Pilih Formula --</option>').val('');
                    // Jika tidak ada formula, langsung load bahan baku tanpa filter formula
                    loadBahanBaku(idProduk);
                }
            },
            error: function() {
                $formulaGroup.hide();
                loadBahanBaku(idProduk);
            }
        });
    }

    /**
     * Render satu baris bahan baku
     */
    function renderBahanBakuRow(idx, nama, berat, isFromMaster) {
        const namaField = isFromMaster ? `
            <input type="text" name="bahan_baku[${idx}][nama]"
                   class="form-control bahan-baku-nama"
                   value="${nama}" readonly
                   placeholder="Nama bahan baku" required>
            <small class="text-muted"><i class="fas fa-database mr-1"></i>Dari master data</small>
        ` : `
            <input type="text" name="bahan_baku[${idx}][nama]"
                   class="form-control bahan-baku-nama"
                   value="${nama}"
                   placeholder="Nama bahan baku" required>
        `;

        return `
        <div class="row bahan-baku-item border-bottom pb-2 mb-2" data-index="${idx}">
            <div class="col-md-3">
                <div class="form-group mb-1">
                    ${idx === 0 ? '<label><i class="fas fa-wheat mr-1"></i>Bahan Baku</label>' : ''}
                    ${namaField}
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-1">
                    ${idx === 0 ? '<label>Kode Produksi</label>' : ''}
                    <input type="text" name="bahan_baku[${idx}][kode_produksi]"
                           class="form-control" value="" placeholder="-">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-1">
                    ${idx === 0 ? '<label>Berat</label>' : ''}
                    <div class="input-group">
                        <input type="text" name="bahan_baku[${idx}][berat]"
                               class="form-control" value="${berat || ''}" placeholder="0" ${isFromMaster ? 'readonly' : ''}>
                        <div class="input-group-append">
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-1">
                    ${idx === 0 ? '<label>Suhu</label>' : ''}
                    <div class="input-group">
                        <input type="text" name="bahan_baku[${idx}][suhu]"
                               class="form-control" value="" placeholder="0">
                        <div class="input-group-append">
                            <span class="input-group-text">°C</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-1">
                    ${idx === 0 ? '<label>Kondisi</label>' : ''}
                    <div class="input-group">
                        <select name="bahan_baku[${idx}][kondisi]" class="form-control">
                            <option value="">-- Pilih Kondisi --</option>
                            <option value="OK">OK</option>
                            <option value="Not OK">Not OK</option>
                        </select>
                        <div class="input-group-append">
                            ${isFromMaster ? '' : `
                            <button type="button" class="btn btn-danger btn-sm remove-bahan-baku"
                                    ${idx === 0 ? 'style="display:none;"' : ''}>
                                <i class="fas fa-trash"></i>
                            </button>
                            `}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-start pt-1">
                ${!isFromMaster ? `<span class="badge badge-warning badge-sm"><i class="fas fa-edit mr-1"></i>Manual</span>` : ''}
            </div>
        </div>`;
    }

    /**
     * Load bahan baku dari server berdasarkan id_produk dan nomor_formula
     */
    function loadBahanBaku(idProduk, nomorFormula = '') {
        if (!idProduk) {
            $('#bahan-baku-container').html(`
                <div class="row bahan-baku-empty text-center py-4">
                    <div class="col-12 text-muted">
                        <i class="fas fa-box-open fa-2x mb-2"></i>
                        <p>Pilih produk untuk memuat daftar bahan baku secara otomatis</p>
                    </div>
                </div>
            `);
            $('#bahan-baku-status').removeClass('badge-success badge-warning badge-danger').addClass('badge-secondary')
                .html('<i class="fas fa-info-circle mr-1"></i>Pilih produk terlebih dahulu');
            $('#btn-tambah-bahan-baku-manual-wrap').hide();
            return;
        }

        // Show loading
        $('#bahan-baku-loading').show();
        $('#bahan-baku-container').html('');
        $('#btn-tambah-bahan-baku-manual-wrap').hide();

        let url = ajaxUrl.replace(':id', idProduk);
        if (nomorFormula) {
            url += `?nomor_formula=${encodeURIComponent(nomorFormula)}`;
        }

        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                $('#bahan-baku-loading').hide();

                if (data.length > 0) {
                    let html = '';
                    bahanBakuIndex = 0;
                    data.forEach(function (item) {
                        html += renderBahanBakuRow(bahanBakuIndex, item.nama_bahan_baku, item.berat, true);
                        bahanBakuIndex++;
                    });
                    $('#bahan-baku-container').html(html);
                    
                    const sourceText = nomorFormula ? `Formula ${nomorFormula}` : 'Master Data';
                    $('#bahan-baku-status').removeClass('badge-secondary badge-warning badge-danger').addClass('badge-success')
                        .html(`<i class="fas fa-check-circle mr-1"></i>${data.length} bahan baku dimuat dari ${sourceText}`);
                    $('#btn-tambah-bahan-baku-manual-wrap').hide();
                } else {
                    // Tidak ada data master - tampilkan 1 baris kosong untuk input manual
                    $('#bahan-baku-container').html(renderBahanBakuRow(0, '', '', false));
                    bahanBakuIndex = 1;
                    $('#bahan-baku-status').removeClass('badge-secondary badge-success badge-danger').addClass('badge-warning')
                        .html('<i class="fas fa-exclamation-triangle mr-1"></i>Tidak ada data master, input manual');
                    $('#btn-tambah-bahan-baku-manual-wrap').show();
                }
            },
            error: function () {
                $('#bahan-baku-loading').hide();
                $('#bahan-baku-container').html(renderBahanBakuRow(0, '', '', false));
                bahanBakuIndex = 1;
                $('#bahan-baku-status').removeClass('badge-secondary badge-success').addClass('badge-danger')
                    .html('<i class="fas fa-times-circle mr-1"></i>Gagal memuat, input manual');
                $('#btn-tambah-bahan-baku-manual-wrap').show();
            }
        });
    }

    // Trigger saat produk berubah
    $('#id_produk').on('change', function () {
        const idProduk = $(this).val();
        loadFormulas(idProduk);
    });

    // Trigger saat formula berubah
    $('#nomor_formula').on('change', function() {
        const idProduk = $('#id_produk').val();
        const nomorFormula = $(this).val();
        if (idProduk) {
            loadBahanBaku(idProduk, nomorFormula);
        }
    });

    // Trigger otomatis jika produk sudah dipilih (saat page load dengan old input)
    var initialProduk = $('#id_produk').val();
    if (initialProduk) {
        // Jika ada old input untuk nomor_formula, biarkan loadFormulas yang menangani atau panggil manual
        loadFormulas(initialProduk);
    }

    // Tombol tambah bahan baku manual
    $(document).on('click', '.add-bahan-baku-manual', function () {
        const newRow = renderBahanBakuRow(bahanBakuIndex, '', '', false);
        $('#bahan-baku-container').append(newRow);
        bahanBakuIndex++;
    });

    // Tombol hapus bahan baku
    $(document).on('click', '.remove-bahan-baku', function () {
        const container = $('#bahan-baku-container');
        if (container.find('.bahan-baku-item').length > 1) {
            $(this).closest('.bahan-baku-item').remove();
            // Re-index
            container.find('.bahan-baku-item').each(function (i) {
                $(this).attr('data-index', i);
                $(this).find('input[name^="bahan_baku"]').each(function () {
                    const name = $(this).attr('name');
                    $(this).attr('name', name.replace(/bahan_baku\[\d+\]/, `bahan_baku[${i}]`));
                });
                if (i === 0) {
                    $(this).find('.remove-bahan-baku').hide();
                } else {
                    $(this).find('.remove-bahan-baku').show();
                }
            });
            bahanBakuIndex = container.find('.bahan-baku-item').length;
        }
    });

    // ============================================================
    // Rata-rata Suhu Otomatis
    // ============================================================
    // Dihapus karena sudah ditangani secara global di app.blade.php


    // ============================================================
    // Logic Suhu dan Premix sudah ditangani secara global di app.blade.php
    // ============================================================

    // ============================================================
    // Default "-" untuk field kode_produksi yang kosong saat submit
    // ============================================================
    $('form').on('submit', function () {
        $('input[name*="[kode_produksi]"]').each(function () {
            if ($(this).val().trim() === '') {
                $(this).val('-');
            }
        });
    });

});
</script>
@endpush
@endsection

