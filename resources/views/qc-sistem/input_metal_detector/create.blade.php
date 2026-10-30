@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> Tambah Pemeriksaan Metal Detector</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('input-metal-detector.index') }}"><i class="fas fa-warehouse"></i> List Metal Detector</a></li>
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
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-edit"></i> Form Input Pemeriksaan Metal Detector</h3>
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
                            <!-- Card Informasi Simbol Metal Detector -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card card-warning card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-info-circle"></i> Keterangan Simbol Metal Detector
                                            </h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="info-box bg-success">
                                                        <span class="info-box-icon">
                                                            <i class="fas fa-check-circle" style="font-size: 2rem;"></i>
                                                        </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">✓ (Centang)</span>
                                                            <span class="info-box-number">Specimen Terdeteksi</span>
                                                            <div class="progress">
                                                                <div class="progress-bar bg-success" style="width: 100%"></div>
                                                            </div>
                                                            <span class="progress-description">
                                                                Metal detector berhasil mendeteksi specimen logam
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-box bg-danger">
                                                        <span class="info-box-icon">
                                                            <i class="fas fa-times-circle" style="font-size: 2rem;"></i>
                                                        </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">✗ (Silang)</span>
                                                            <span class="info-box-number">Tidak Terdeteksi</span>
                                                            <div class="progress">
                                                                <div class="progress-bar bg-danger" style="width: 100%"></div>
                                                            </div>
                                                            <span class="progress-description">
                                                                Metal detector tidak mendeteksi specimen logam
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="alert alert-info mt-2">
                                                <i class="fas fa-lightbulb"></i>
                                                <strong>Catatan:</strong> Gunakan tanda centang (✓) jika specimen terdeteksi dengan baik, dan tanda silang (✗) jika specimen tidak terdeteksi oleh metal detector.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <form class="form-horizontal" id="main-metal-detector-form" method="POST" action="{{ route('input-metal-detector.store') }}">
                                @csrf
                                <div class="card card-outline card-info shadow-sm">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-clipboard-check"></i> Data Pemeriksaan Metal Detector</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-success btn-sm" id="add-form-btn">
                                                <i class="fas fa-plus"></i> Tambah Form
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body" id="forms-container">
                                        <!-- Dynamic forms will be added here -->
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card card-outline card-secondary">
                                            <div class="card-body text-center">
                                                <button type="submit" class="btn btn-primary btn-md mr-2">
                                                    <i class="fas fa-save"></i> Simpan Semua Data
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

<!-- Template for dynamic form -->
<template id="form-template">
    <div class="form-item card card-outline card-secondary mb-3">
        <div class="card-header">
            <h5 class="card-title"><i class="fas fa-search"></i> Data Pemeriksaan <span class="form-number"></span></h5>
            <div class="card-tools">
                <button type="button" class="btn btn-danger btn-sm remove-form-btn">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Shift <span class="text-danger">*</span></label>
                        <select class="form-control select2 shift-select" name="id_shift[]" required>
                            <option value="">Pilih Shift</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Line <span class="text-danger">*</span></label>
                        <select class="form-control select2 line-select" name="line[]" required>
                            <option value="">Pilih Line</option>
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ old('line') == $i ? 'selected' : '' }}>Line {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt"></i> Tanggal <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tanggal[]" class="form-control tanggal-input" 
                            value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-clock"></i> Jam <span class="text-danger">*</span>
                        </label>
                        <input type="time" class="form-control jam-input" 
                            name="jam[]" 
                            value="{{ old('jam', date('H:i')) }}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <select class="form-control select2 produk-select" name="id_produk[]" required>
                            <option value="">Pilih Produk</option>
                            @foreach($produks as $produk)
                                <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Kode Produksi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control kode-produksi-input" name="kode_produksi[]" placeholder="Masukkan kode produksi" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Berat Produk (gr) <span class="text-danger">*</span></label>
                        <select class="form-control berat-produk-select" id="nilai_select_berat_sampling" name="berat_produk[]" required></select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12"><hr></div>
            </div>
            <div class="row">
                <div class="col-md-4">
                
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input auto-terdeteksi-checkbox">
                            <label class="custom-control-label" for="auto_terdeteksi_checkbox">
                                <small class="text-muted">Centang untuk mengatur semua menjadi terdeteksi</small>
                            </label>
                        </div>
                        <label class="font-weight-bold text-info">Fe 1.5 mm</label>
                        <select class="form-control mb-2 @error('fe_depan_aktual') is-invalid @enderror" name="fe_depan_aktual[]" required>
                            <option value="" disabled selected hidden>Depan</option>
                            <option value="✔" {{ old('fe_depan_aktual') == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                            <option value="✘" {{ old('fe_depan_aktual') == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                        </select>
                        @error('fe_depan_aktual')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror

                        <select class="form-control mb-2 @error('fe_tengah_aktual') is-invalid @enderror" name="fe_tengah_aktual[]" required>
                            <option value="" disabled selected hidden>Tengah</option>
                            <option value="✔" {{ old('fe_tengah_aktual') == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                            <option value="✘" {{ old('fe_tengah_aktual') == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                        </select>
                        @error('fe_tengah_aktual')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror

                        <select class="form-control @error('fe_belakang_aktual') is-invalid @enderror" name="fe_belakang_aktual[]" required>
                            <option value="" disabled selected hidden>Belakang</option>
                            <option value="✔" {{ old('fe_belakang_aktual') == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                            <option value="✘" {{ old('fe_belakang_aktual') == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                        </select>
                        @error('fe_belakang_aktual')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                
                <div class="form-group">
                        <!-- Spacer to align with checkbox in first column -->
                        <div style="height: 38px; margin-bottom: 8px;"></div>
                        <label class="font-weight-bold text-success">Non Fe 2 mm</label>
                    <select class="form-control mb-2 @error('non_fe_depan_aktual') is-invalid @enderror" name="non_fe_depan_aktual[]" required>
                        <option value="" disabled selected hidden>Depan</option>
                        <option value="✔" {{ old('non_fe_depan_aktual') == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                        <option value="✘" {{ old('non_fe_depan_aktual') == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                    </select>
                    @error('non_fe_depan_aktual')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror

                    <select class="form-control mb-2 @error('non_fe_tengah_aktual') is-invalid @enderror" name="non_fe_tengah_aktual[]" required>
                        <option value="" disabled selected hidden>Tengah</option>
                        <option value="✔" {{ old('non_fe_tengah_aktual') == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                        <option value="✘" {{ old('non_fe_tengah_aktual') == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                    </select>
                    @error('non_fe_tengah_aktual')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror

                    <select class="form-control @error('non_fe_belakang_aktual') is-invalid @enderror" name="non_fe_belakang_aktual[]" required>
                        <option value="" disabled selected hidden>Belakang</option>
                        <option value="✔" {{ old('non_fe_belakang_aktual') == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                        <option value="✘" {{ old('non_fe_belakang_aktual') == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                    </select>
                    @error('non_fe_belakang_aktual')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">  
                <div class="form-group">
                    <!-- Spacer to align with checkbox in first column -->
                    <div style="height: 38px; margin-bottom: 8px;"></div>
                    <label class="font-weight-bold text-warning">SUS 316 2.5 mm</label>
                    <select class="form-control mb-2 @error('sus_depan_aktual') is-invalid @enderror" name="sus_depan_aktual[]" required>
                        <option value="" disabled selected hidden>Depan</option>
                        <option value="✔" {{ old('sus_depan_aktual') == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                        <option value="✘" {{ old('sus_depan_aktual') == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                    </select>
                    @error('sus_depan_aktual')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror

                    <select class="form-control mb-2 @error('sus_tengah_aktual') is-invalid @enderror" name="sus_tengah_aktual[]" required>
                        <option value="" disabled selected hidden>Tengah</option>
                        <option value="✔"  {{ old('sus_tengah_aktual') == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                        <option value="✘" {{ old('sus_tengah_aktual') == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                    </select>
                    @error('sus_tengah_aktual')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror

                    <select class="form-control @error('sus_belakang_aktual') is-invalid @enderror" name="sus_belakang_aktual[]" required>
                        <option value="" disabled selected hidden>Belakang</option>
                        <option value="✔" {{ old('sus_belakang_aktual') == '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                        <option value="✘" {{ old('sus_belakang_aktual') == '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
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
                            <textarea class="form-control keterangan-input" name="keterangan[]" rows="2" placeholder="Keterangan tambahan"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
let formCount = 0;

$(document).ready(function() {
    // Add first form on page load
    addNewForm();
    
    // Add form button click
    $('#add-form-btn').on('click', function() {
        addNewForm();
    });
    
    // Remove form button click (delegated event)
    $(document).on('click', '.remove-form-btn', function() {
        if ($('.form-item').length > 1) {
            $(this).closest('.form-item').remove();
            updateFormNumbers();
        } else {
            toastr.warning('Minimal harus ada 1 form');
        }
    });
    
    // Handle produk change to load berat produk (delegated event)
    $(document).on('change', '.produk-select', function() {
        const formItem = $(this).closest('.form-item');
        const beratSelect = formItem.find('.berat-produk-select');
        const produkId = $(this).val();
        
        if (produkId) {
            // Use nilaiBerat from app.blade.php
            beratSelect.empty();
            beratSelect.append('<option value="">Pilih Berat Produk</option>');
            if (typeof nilaiBerat !== 'undefined' && nilaiBerat.length > 0) {
                $.each(nilaiBerat, function(i, val) {
                    beratSelect.append('<option value="' + val + '">' + val + ' gram</option>');
                });
            }
        } else {
            beratSelect.empty();
            beratSelect.append('<option value="">Pilih Produk terlebih dahulu</option>');
        }
    });
    
    // Handle auto terdeteksi checkbox (delegated event)
    $(document).on('change', '.auto-terdeteksi-checkbox', function() {
        const formItem = $(this).closest('.form-item');
        if ($(this).is(':checked')) {
            // Set all dropdown options in this form to "✔" (terdeteksi)
            formItem.find('select[name="fe_depan_aktual[]"]').val('✔');
            formItem.find('select[name="fe_tengah_aktual[]"]').val('✔');
            formItem.find('select[name="fe_belakang_aktual[]"]').val('✔');
            formItem.find('select[name="non_fe_depan_aktual[]"]').val('✔');
            formItem.find('select[name="non_fe_tengah_aktual[]"]').val('✔');
            formItem.find('select[name="non_fe_belakang_aktual[]"]').val('✔');
            formItem.find('select[name="sus_depan_aktual[]"]').val('✔');
            formItem.find('select[name="sus_tengah_aktual[]"]').val('✔');
            formItem.find('select[name="sus_belakang_aktual[]"]').val('✔');
            
            toastr.success('Semua pilihan telah diatur menjadi "Terdeteksi"');
        } else {
            // Reset all dropdown options in this form to default (empty)
            formItem.find('select[name="fe_depan_aktual[]"]').val('');
            formItem.find('select[name="fe_tengah_aktual[]"]').val('');
            formItem.find('select[name="fe_belakang_aktual[]"]').val('');
            formItem.find('select[name="non_fe_depan_aktual[]"]').val('');
            formItem.find('select[name="non_fe_tengah_aktual[]"]').val('');
            formItem.find('select[name="non_fe_belakang_aktual[]"]').val('');
            formItem.find('select[name="sus_depan_aktual[]"]').val('');
            formItem.find('select[name="sus_tengah_aktual[]"]').val('');
            formItem.find('select[name="sus_belakang_aktual[]"]').val('');
            
            toastr.info('Semua pilihan telah direset');
        }
    });
});

function addNewForm() {
    formCount++;
    const template = document.getElementById('form-template');
    const clone = template.content.cloneNode(true);
    
    // Set unique ID for checkbox
    const checkbox = clone.querySelector('.auto-terdeteksi-checkbox');
    const checkboxId = 'auto_terdeteksi_checkbox_' + formCount;
    checkbox.id = checkboxId;
    clone.querySelector('.custom-control-label').setAttribute('for', checkboxId);
    
    // Update form number
    clone.querySelector('.form-number').textContent = '#' + formCount;
    
    // Append to container
    document.getElementById('forms-container').appendChild(clone);
    
    // Initialize berat produk options for new form
    const newForm = $('.form-item').last();
    const beratSelect = newForm.find('.berat-produk-select');
    beratSelect.empty();
    beratSelect.append('<option value="">Pilih Produk terlebih dahulu</option>');
    
    // Reinitialize select2 for new form
    $('.select2').select2();
    
    updateFormNumbers();
}

function updateFormNumbers() {
    $('.form-item').each(function(index) {
        $(this).find('.form-number').text('#' + (index + 1));
    });
}
</script>
@endpush
@endsection