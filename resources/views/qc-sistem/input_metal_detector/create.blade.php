@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-clipboard-check text-primary mr-2"></i> Tambah Pemeriksaan Metal Detector</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('input-metal-detector.index') }}"><i class="fas fa-search"></i> List Metal Detector</a></li>
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
                            <h3 class="card-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Form Input Pemeriksaan Metal Detector (Produksi)</h3>
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

                            <div class="alert alert-info py-2 mb-3">
                                <i class="fas fa-info-circle mr-1"></i> <strong>Peran Produksi:</strong> Formulir ini digunakan oleh <strong>Bagian Produksi</strong> untuk mencatat <strong>A. Detail Pemeriksaan Metal Detector</strong> dan <strong>B. Hasil Pengecekan</strong> per Line dan Produk. Pengujian spesimen (Fe, Non-Fe, SUS) serta verifikasi akan dilakukan oleh <strong>QC Inspector</strong> melalui menu <strong>Lihat Detail (icon mata)</strong>.
                            </div>

                            <form class="form-horizontal" id="main-metal-detector-form" method="POST" action="{{ route('input-metal-detector.store') }}">
                                @csrf
                                <div class="card card-outline card-info shadow-sm">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h3 class="card-title font-weight-bold"><i class="fas fa-layer-group mr-1"></i> Daftar Form Pemeriksaan Metal Detector</h3>
                                        <div class="card-tools ml-auto">
                                            <button type="button" class="btn btn-success btn-sm font-weight-bold" id="add-form-btn">
                                                <i class="fas fa-plus mr-1"></i> Tambah Baris/Form
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
                                                <button type="submit" class="btn btn-primary btn-md mr-2 font-weight-bold">
                                                    <i class="fas fa-save mr-1"></i> Simpan Semua Data
                                                </button>
                                                <a href="{{ route('input-metal-detector.index') }}" class="btn btn-secondary btn-md">
                                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
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
    <div class="form-item card card-outline card-secondary mb-4 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="card-title font-weight-bold mb-0">
                <i class="fas fa-microchip text-primary mr-1"></i> Data Pemeriksaan & Hasil MD <span class="form-number text-primary"></span>
            </h5>
            <div class="card-tools ml-auto">
                <button type="button" class="btn btn-danger btn-sm remove-form-btn">
                    <i class="fas fa-trash mr-1"></i> Hapus Form
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- BAGIAN A: DETAIL PEMERIKSAAN METAL DETECTOR -->
            <div class="card card-info card-outline mb-3">
                <div class="card-header py-2">
                    <h6 class="card-title font-weight-bold text-info mb-0">
                        <i class="fas fa-clipboard-list mr-1"></i> A. Detail Pemeriksaan Metal Detector
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-calendar-alt mr-1"></i> Tanggal Kode Produksi <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal[]" class="form-control tanggal-input" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-clock mr-1"></i> Jam <span class="text-danger">*</span></label>
                                <input type="time" class="form-control jam-input" name="jam[]" value="{{ old('jam', date('H:i')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                                <input type="text" class="form-control kode-produksi-input" name="kode_produksi[]" placeholder="Contoh: 261105 A1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Keterangan (Bagian A)</label>
                                <input type="text" class="form-control keterangan-input" name="keterangan[]" placeholder="Keterangan tambahan Bagian A">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BAGIAN B: HASIL PENGECEKAN (TAMBAHAN BARU) -->
            <div class="card card-success card-outline mb-0">
                <div class="card-header py-2">
                    <h6 class="card-title font-weight-bold text-success mb-0">
                        <i class="fas fa-check-double mr-1"></i> B. Hasil Pengecekan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Merk (Mesin MD) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control merk-input" name="merk[]" placeholder="Contoh: Anritsu / Ishida" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-hourglass-start mr-1"></i> Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="waktu_mulai[]" value="{{ old('waktu_mulai', date('H:i')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-hourglass-end mr-1"></i> Waktu Selesai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="waktu_selesai[]" value="{{ old('waktu_selesai', date('H:i', strtotime('+1 hour'))) }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Gramasi (gr)</label>
                                <select class="form-control berat-produk-select" name="berat_produk[]"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Jumlah Reject Logam <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" name="jumlah_reject[]" value="0" placeholder="0" required>
                                <small class="text-muted">Isikan angka (0 jika tidak ada)</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Nama Operator <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_operator[]" placeholder="Nama operator yang bertugas" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Foreman / Forelady / SPV <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="spv_foreman[]" placeholder="Nama SPV / Foreman" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Kolom Keterangan (Bagian B)</label>
                                <input type="text" class="form-control" name="keterangan_b[]" placeholder="Keterangan proses / reject">
                            </div>
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
});

function addNewForm() {
    formCount++;
    const template = document.getElementById('form-template');
    const clone = template.content.cloneNode(true);
    
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
    if ($.fn.select2) {
        newForm.find('.select2').select2();
    }
    
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
