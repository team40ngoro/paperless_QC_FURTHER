@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Pemeriksaan Kedatangan Bahan Baku</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('chillroom.index') }}">Chillroom</a></li>
                            <li class="breadcrumb-item active">Tambah Data</li>
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
                                <h3 class="card-title">Form Pemeriksaan Chillroom</h3>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        @php
                            $isQc = auth()->check() && auth()->user()->isQc();
                        @endphp
                        <div class="card-body">
                            <form action="{{ route('chillroom.store') }}" method="POST" id="main-chillroom-form">
                                @csrf
                                
                                <!-- Informasi Umum (Shared Fields) -->
                                <div class="card card-info card-outline mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Informasi Umum</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> <strong>Informasi:</strong> Tanggal dan Shift akan digunakan untuk semua data yang diinput.
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                        </div>
                                                        <input type="date" class="form-control" name="tanggal" id="tanggal" 
                                                               value="{{ old('tanggal', \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d')) }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="shift_id">Shift <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="shift_id" id="shift_id" required>
                                                        <option value="">Pilih Shift</option>
                                                        @foreach($shifts as $shift)
                                                            <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->shift }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="jam_kedatangan">Jam Kedatangan <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                        </div>
                                                        <input type="time" class="form-control" name="jam_kedatangan" id="jam_kedatangan"
                                                               value="{{ old('jam_kedatangan', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Summary Cards -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="info-box bg-info">
                                            <span class="info-box-icon"><i class="fas fa-list"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Entries</span>
                                                <span class="info-box-number" id="total-entries">1</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Valid Entries</span>
                                                <span class="info-box-number" id="valid-entries">0</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box bg-warning">
                                            <span class="info-box-icon"><i class="fas fa-exclamation"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Incomplete</span>
                                                <span class="info-box-number" id="incomplete-entries">1</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic Entries Container -->
                                <div id="entries-container">
                                    <div class="entry-card" data-entry="1">
                                        <div class="card card-primary card-outline">
                                            <div class="card-header">
                                                <h3 class="card-title"><i class="fas fa-snowflake"></i> Entry #1</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h5 class="text-primary mb-3"><i class="fas fa-box"></i> Detail Bahan Baku</h5>
                                                        <div class="form-group">
                                                            <label>Nama RM <span class="text-danger">*</span></label>
                                                            <select name="entries[0][nama_rm]" id="nama_rm_0" class="form-control entry-field select-nama-rm" required>
                                                                <option value="">Pilih Nama RM</option>
                                                                @foreach($dataRm as $rm)
                                                                    <option value="{{ $rm->nama_rm }}">{{ $rm->nama_rm }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Kode Produksi <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control entry-field" name="entries[0][kode_produksi]" 
                                                                placeholder="Masukkan kode produksi" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Berat Perkemasan <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control entry-field" name="entries[0][berat]" 
                                                                    placeholder="Masukkan berat" required>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">kg</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Suhu (°C) <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.1" class="form-control entry-field" name="entries[0][suhu]" 
                                                                    placeholder="Masukkan suhu" required>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">°C</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Sensori <span class="text-danger">*</span></label>
                                                            <select class="form-control entry-field" name="entries[0][sensori]" required>
                                                                <option value="">Pilih Status Sensori</option>
                                                                <option value="✔">✓ Oke</option>
                                                                <option value="✘">✘ Tidak Oke</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Kemasan <span class="text-danger">*</span></label>
                                                            <select class="form-control entry-field" name="entries[0][kemasan]" required>
                                                                <option value="">Pilih Status Kemasan</option>
                                                                <option value="✔">✓ Oke</option>
                                                                <option value="✘">✘ Tidak Oke</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Keterangan</label>
                                                            <textarea class="form-control entry-field" name="entries[0][keterangan]" 
                                                                rows="2" placeholder="Masukkan keterangan tambahan"></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 sampling-spo-card-section {{ $isQc ? 'sampling-spo-muted' : '' }}">
                                                        <h5 class="{{ $isQc ? 'text-secondary' : 'text-primary' }} mb-3">
                                                            <i class="fas fa-clipboard-check"></i> Sampling Berat RM Daging SPO
                                                            @if($isQc)
                                                                <span class="badge badge-secondary ml-1" title="Card ini readonly untuk Role QC"><i class="fas fa-lock"></i> Readonly (Role QC)</span>
                                                            @endif
                                                        </h5>
                                                        @if($isQc)
                                                            <div class="alert alert-light border small text-muted mb-3 py-1 px-2">
                                                                <i class="fas fa-info-circle mr-1"></i> Card sampling berat daging SPO dalam mode <strong>readonly</strong> untuk Role QC.
                                                            </div>
                                                        @endif
                                                        <div class="form-group">
                                                            <label>Standar Berat Per PCS (g)</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control entry-field" name="entries[0][standar_berat]" 
                                                                    placeholder="{{ $isQc ? 'Readonly untuk Role QC' : 'Masukkan standar berat' }}"
                                                                    {{ $isQc ? 'readonly tabindex=-1 style=background-color:#e9ecef;cursor:not-allowed;' : '' }}>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">gr</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="card card-light card-outline">
                                                            <div class="card-header">
                                                                <h6 class="card-title mb-0">
                                                                    <i class="fas fa-balance-scale"></i> Hasil Aktual Berat Per PCS (gr)
                                                                </h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div id="berat-container-0">
                                                                    <div class="form-group">
                                                                        <label>Berat Sample 1 (gr)</label>
                                                                        <div class="input-group">
                                                                            <input type="number" step="0.01" class="form-control entry-field" name="entries[0][jumlah_rm_value][]" 
                                                                                placeholder="{{ $isQc ? '-' : 'Masukkan berat sample' }}" min="0"
                                                                                {{ $isQc ? 'readonly tabindex=-1 style=background-color:#e9ecef;cursor:not-allowed;' : '' }}>
                                                                            @if(!$isQc)
                                                                            <div class="input-group-append">
                                                                                <button type="button" class="btn btn-success btn-sm add-berat-btn" data-target="berat-container-0">
                                                                                    <i class="fas fa-plus"></i>
                                                                                </button>
                                                                            </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <small class="form-text text-muted">{{ $isQc ? 'Sampling berat dikelola oleh tim terkait' : 'Tambahkan sample berat sesuai kebutuhan' }}</small>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="card card-light card-outline">
                                                            <div class="card-header">
                                                                <h6 class="card-title mb-0">
                                                                    <i class="fas fa-weight"></i> Jumlah yang Disampling
                                                                </h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Di Atas Standar (pcs)</label>
                                                                            <input type="number" class="form-control entry-field" name="entries[0][berat_atas]" 
                                                                                placeholder="{{ $isQc ? '-' : 'Masukkan jumlah pcs' }}" min="0"
                                                                                {{ $isQc ? 'readonly tabindex=-1 style=background-color:#e9ecef;cursor:not-allowed;' : '' }}>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Sesuai Standar (pcs)</label>
                                                                            <input type="number" class="form-control entry-field" name="entries[0][berat_std]" 
                                                                                placeholder="{{ $isQc ? '-' : 'Masukkan jumlah pcs' }}" min="0"
                                                                                {{ $isQc ? 'readonly tabindex=-1 style=background-color:#e9ecef;cursor:not-allowed;' : '' }}>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Bawah Standar (pcs)</label>
                                                                            <input type="number" class="form-control entry-field" name="entries[0][berat_bawah]" 
                                                                                placeholder="{{ $isQc ? '-' : 'Masukkan jumlah pcs' }}" min="0"
                                                                                {{ $isQc ? 'readonly tabindex=-1 style=background-color:#e9ecef;cursor:not-allowed;' : '' }}>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="form-group">
                                                            <label>Status</label>
                                                            @if($isQc)
                                                                <select class="form-control entry-field" disabled tabindex="-1" style="background-color:#e9ecef;cursor:not-allowed;">
                                                                    <option value="">-</option>
                                                                </select>
                                                            @else
                                                                <select class="form-control entry-field" name="entries[0][status_rm]">
                                                                    <option value="">Pilih Status</option>
                                                                    <option value="diterima">Diterima</option>
                                                                    <option value="diretur">Diretur</option>
                                                                </select>
                                                            @endif
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Catatan</label>
                                                            <textarea class="form-control entry-field" name="entries[0][catatan_rm]" 
                                                                    rows="3" placeholder="{{ $isQc ? 'Readonly untuk Role QC' : 'Masukkan catatan tambahan' }}"
                                                                    {{ $isQc ? 'readonly tabindex=-1 style=background-color:#e9ecef;cursor:not-allowed;' : '' }}></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary" id="submit-btn">
                                        <i class="fas fa-save"></i> Simpan <span id="submit-count">1</span> Data
                                    </button>
                                    <button type="button" class="btn btn-success ml-2" id="add-entry-btn">
                                        <i class="fas fa-plus"></i> Tambah Entry
                                    </button>
                                    @if(!$isQc)
                                    <button type="button" class="btn btn-warning ml-2" id="add-sampling-frozen-btn">
                                        <i class="fas fa-snowflake"></i> Tambah Sampling Berat (Frozen)
                                    </button>
                                    @endif
                                    <a href="{{ route('chillroom.index') }}" class="btn btn-secondary ml-2">
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

@push('styles')
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .sampling-spo-muted {
        background-color: #f8f9fa;
        border-radius: 6px;
        padding: 12px;
        opacity: 0.85;
        border: 1px dashed #ced4da;
    }
    .sampling-spo-muted input, 
    .sampling-spo-muted select, 
    .sampling-spo-muted textarea {
        background-color: #e9ecef !important;
        cursor: not-allowed !important;
        color: #6c757d !important;
    }
    .sampling-spo-muted .add-berat-btn,
    .sampling-spo-muted .remove-berat-btn {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let entryCounter = 0;
    const isUserQc = {{ $isQc ? 'true' : 'false' }};
    const qcLockBadge = @json($isQc ? '<span class="badge badge-secondary ml-1" title="Card ini readonly untuk Role QC"><i class="fas fa-lock"></i> Readonly (Role QC)</span>' : '');
    const qcNoticeHtml = @json($isQc ? '<div class="alert alert-light border small text-muted mb-3 py-1 px-2"><i class="fas fa-info-circle mr-1"></i> Card sampling berat daging SPO dalam mode <strong>readonly</strong> untuk Role QC.</div>' : '');
    
    // Function to update summary
    function updateSummary() {
        const totalEntries = $('.entry-card').length;
        let validEntries = 0;
        let incompleteEntries = 0;
        
        $('.entry-card').each(function() {
            const requiredFields = $(this).find('.entry-field[required]');
            let isComplete = true;
            
            requiredFields.each(function() {
                if (!$(this).val() || $(this).val().trim() === '') {
                    isComplete = false;
                    return false;
                }
            });
            
            if (isComplete) {
                validEntries++;
            } else {
                incompleteEntries++;
            }
        });
        
        $('#total-entries').text(totalEntries);
        $('#valid-entries').text(validEntries);
        $('#incomplete-entries').text(incompleteEntries);
        $('#submit-count').text(totalEntries);
    }
    
    // Function to reindex entries - PERBAIKAN LEBIH AMAN
    function reindexEntries() {
        $('.entry-card').each(function(index) {
            $(this).attr('data-entry', index + 1);
            $(this).find('.card-title').html(`<i class="fas fa-snowflake"></i> Entry #${index + 1}`);
            
            // Update ALL name attributes dengan index yang benar
            $(this).find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                if (name && name.startsWith('entries[')) {
                    // PERBAIKAN: Handle array fields correctly
                    if (name.includes('jumlah_rm_value[]')) {
                        // For array fields like jumlah_rm_value[]
                        $(this).attr('name', `entries[${index}][jumlah_rm_value][]`);
                    } else {
                        // For regular fields
                        const match = name.match(/entries\[\d+\](\[.+\])/);
                        if (match) {
                            const fieldPart = match[1];
                            $(this).attr('name', `entries[${index}]${fieldPart}`);
                        }
                    }
                }
            });
            
            // Update ID attributes untuk select
            $(this).find('.select-nama-rm').attr('id', `nama_rm_${index}`);
            
            // Update berat container ID
            const beratContainer = $(this).find('[id^="berat-container-"]');
            if (beratContainer.length) {
                beratContainer.attr('id', `berat-container-${index}`);
                // Update button data-target
                beratContainer.find('.add-berat-btn').first().attr('data-target', `berat-container-${index}`);
            }
            
            // Update data-entry attributes
            $(this).find('.jumlah-rm-select').attr('data-entry', index);
            $(this).find('.jumlah-rm-value-container').attr('data-entry', index);
            
            // Show/hide remove button
            const removeBtn = $(this).find('.remove-entry-btn');
            if ($('.entry-card').length > 1) {
                removeBtn.show();
            } else {
                removeBtn.hide();
            }

            // Update placeholder for frozen types
            if ($(this).hasClass('frozen-sampling-card')) {
                $(this).find('.card-title').html(`<i class="fas fa-snowflake text-warning"></i> Entry #${index + 1} (Sampling Frozen)`);
            }
        });
    }
    
    // Add new entry
    $('#add-entry-btn').click(function() {
        entryCounter++;
        const newEntryNum = $('.entry-card').length;
        
        // Build options untuk dropdown Nama RM
        let namaRmOptions = '<option value="">Pilih Nama RM</option>';
        @foreach($dataRm as $rm)
            namaRmOptions += '<option value="{{ $rm->nama_rm }}">{{ $rm->nama_rm }}</option>';
        @endforeach
        
        const newEntryHtml = `
        <div class="entry-card" data-entry="${newEntryNum + 1}">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-snowflake"></i> Entry #${newEntryNum + 1}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-danger btn-sm remove-entry-btn">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3"><i class="fas fa-box"></i> Detail Bahan Baku</h5>
                            <div class="form-group">
                                <label>Nama RM <span class="text-danger">*</span></label>
                                <select name="entries[${newEntryNum}][nama_rm]" id="nama_rm_${newEntryNum}" class="form-control entry-field select-nama-rm" required>
                                    ${namaRmOptions}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Kode Produksi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control entry-field" name="entries[${newEntryNum}][kode_produksi]" 
                                       placeholder="Masukkan kode produksi" required>
                            </div>
                            <div class="form-group">
                                <label>Berat Perkemasan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control entry-field" name="entries[${newEntryNum}][berat]" 
                                           placeholder="Masukkan berat" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Suhu (°C) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.1" class="form-control entry-field" name="entries[${newEntryNum}][suhu]" 
                                           placeholder="Masukkan suhu" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">°C</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Sensori <span class="text-danger">*</span></label>
                                <select class="form-control entry-field" name="entries[${newEntryNum}][sensori]" required>
                                    <option value="">Pilih Status Sensori</option>
                                    <option value="✔">✓ Oke</option>
                                    <option value="✘">✘ Tidak Oke</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Kemasan <span class="text-danger">*</span></label>
                                <select class="form-control entry-field" name="entries[${newEntryNum}][kemasan]" required>
                                    <option value="">Pilih Status Kemasan</option>
                                    <option value="✔">✓ Oke</option>
                                    <option value="✘">✘ Tidak Oke</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea class="form-control entry-field" name="entries[${newEntryNum}][keterangan]" 
                                          rows="2" placeholder="Masukkan keterangan tambahan"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6 sampling-spo-card-section ${isUserQc ? 'sampling-spo-muted' : ''}">
                            <h5 class="${isUserQc ? 'text-secondary' : 'text-primary'} mb-3">
                                <i class="fas fa-clipboard-check"></i> Sampling Berat RM Daging SPO
                                ${qcLockBadge}
                            </h5>
                            ${qcNoticeHtml}
                            <div class="form-group">
                                <label>Standar Berat Per PCS (g)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control entry-field" name="entries[${newEntryNum}][standar_berat]" 
                                           placeholder="${isUserQc ? 'Readonly untuk Role QC' : 'Masukkan standar berat'}"
                                           ${isUserQc ? 'readonly tabindex="-1" style="background-color:#e9ecef;cursor:not-allowed;"' : ''}>
                                    <div class="input-group-append">
                                        <span class="input-group-text">gr</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card card-light card-outline">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-balance-scale"></i> Hasil Aktual Berat Per PCS (gr)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="berat-container-${newEntryNum}">
                                        <div class="form-group">
                                            <label>Berat Sample 1 (gr)</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control entry-field" name="entries[${newEntryNum}][jumlah_rm_value][]" 
                                                       placeholder="${isUserQc ? '-' : 'Masukkan berat sample'}" min="0"
                                                       ${isUserQc ? 'readonly tabindex="-1" style="background-color:#e9ecef;cursor:not-allowed;"' : ''}>
                                                ${!isUserQc ? `
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-success btn-sm add-berat-btn" data-target="berat-container-${newEntryNum}">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>` : ''}
                                            </div>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">${isUserQc ? 'Sampling berat dikelola oleh tim terkait' : 'Tambahkan sample berat sesuai kebutuhan'}</small>
                                </div>
                            </div>
                            
                            <div class="card card-light card-outline">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-weight"></i> Jumlah yang Disampling
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Di Atas Standar (pcs)</label>
                                                <input type="number" class="form-control entry-field" name="entries[${newEntryNum}][berat_atas]" 
                                                       placeholder="${isUserQc ? '-' : 'Masukkan jumlah pcs'}" min="0"
                                                       ${isUserQc ? 'readonly tabindex="-1" style="background-color:#e9ecef;cursor:not-allowed;"' : ''}>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Sesuai Standar (pcs)</label>
                                                <input type="number" class="form-control entry-field" name="entries[${newEntryNum}][berat_std]" 
                                                       placeholder="${isUserQc ? '-' : 'Masukkan jumlah pcs'}" min="0"
                                                       ${isUserQc ? 'readonly tabindex="-1" style="background-color:#e9ecef;cursor:not-allowed;"' : ''}>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Bawah Standar (pcs)</label>
                                                <input type="number" class="form-control entry-field" name="entries[${newEntryNum}][berat_bawah]" 
                                                       placeholder="${isUserQc ? '-' : 'Masukkan jumlah pcs'}" min="0"
                                                       ${isUserQc ? 'readonly tabindex="-1" style="background-color:#e9ecef;cursor:not-allowed;"' : ''}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          
                            <div class="form-group">
                                <label>Status</label>
                                ${isUserQc ? `
                                <select class="form-control entry-field" disabled tabindex="-1" style="background-color:#e9ecef;cursor:not-allowed;">
                                    <option value="">-</option>
                                </select>` : `
                                <select class="form-control entry-field" name="entries[${newEntryNum}][status_rm]">
                                    <option value="">Pilih Status</option>
                                    <option value="diterima">Diterima</option>
                                    <option value="diretur">Diretur</option>
                                </select>`}
                            </div>
                            <div class="form-group">
                                <label>Catatan</label>
                                <textarea class="form-control entry-field" name="entries[${newEntryNum}][catatan_rm]" 
                                          rows="3" placeholder="${isUserQc ? 'Readonly untuk Role QC' : 'Masukkan catatan tambahan'}"
                                          ${isUserQc ? 'readonly tabindex="-1" style="background-color:#e9ecef;cursor:not-allowed;"' : ''}></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;
        
        $('#entries-container').append(newEntryHtml);
        
        // Initialize Select2 untuk dropdown yang baru ditambahkan
        $(`#nama_rm_${newEntryNum}`).select2({
            placeholder: "Pilih Nama RM",
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Data tidak ditemukan";
                }
            }
        });
        
        // Reindex setelah menambah entry baru
        reindexEntries();
        updateSummary();
        
        // Show notification
        if (typeof toastr !== 'undefined') {
            toastr.success('Entry baru berhasil ditambahkan');
        }
        
        // Scroll to new entry
        const newEntry = $('.entry-card').last();
        $('html, body').animate({
            scrollTop: newEntry.offset().top - 100
        }, 500);
    });

    // Add new sampling frozen entry
    $('#add-sampling-frozen-btn').click(function() {
        if (isUserQc) return;
        // Smart check: jika hanya ada 1 entry dan masih kosong, hapus dulu form standard defaultnya
        if ($('.entry-card').length === 1) {
            const firstCard = $('.entry-card').first();
            const namaRm = firstCard.find('select[name*="[nama_rm]"]').val();
            const kodeProd = firstCard.find('input[name*="[kode_produksi]"]').val();
            
            // Jika Nama RM belum dipilih dan Kode Produksi belum diisi, kita anggap form kosong
            if (!namaRm && !kodeProd) {
                firstCard.remove();
            }
        }

        entryCounter++;
        const newEntryNum = $('.entry-card').length;
        
        let namaRmOptions = '<option value="">Pilih Nama RM</option>';
        @foreach($dataRm as $rm)
            namaRmOptions += '<option value="{{ $rm->nama_rm }}">{{ $rm->nama_rm }}</option>';
        @endforeach
        
        const newEntryHtml = `
        <div class="entry-card frozen-sampling-card" data-entry="${newEntryNum + 1}">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-snowflake"></i> Entry #${newEntryNum + 1} (Sampling Frozen)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-danger btn-sm remove-entry-btn">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Identitas Frozen RM -->
                        <div class="col-md-5">
                            <h5 class="text-warning mb-3"><i class="fas fa-info-circle"></i> Identitas RM Frozen</h5>
                            
                            <!-- Hidden inputs to satisfy standard validation -->
                            <input type="hidden" name="entries[${newEntryNum}][berat]" value="-">
                            <input type="hidden" name="entries[${newEntryNum}][suhu]" value="0">
                            <input type="hidden" name="entries[${newEntryNum}][sensori]" value="✔">
                            <input type="hidden" name="entries[${newEntryNum}][kemasan]" value="✔">
                            
                            <div class="form-group">
                                <label>Nama RM <span class="text-danger">*</span></label>
                                <select name="entries[${newEntryNum}][nama_rm]" id="nama_rm_${newEntryNum}" class="form-control entry-field select-nama-rm" required>
                                    ${namaRmOptions}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Kode Produksi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control entry-field" name="entries[${newEntryNum}][kode_produksi]" 
                                       placeholder="Masukkan kode produksi" required>
                            </div>
                            <div class="alert alert-warning small">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> Form ini khusus untuk sampling berat RM dalam kondisi Frozen. Data suhu dan kondisi fisik lainnya akan menggunakan nilai default.
                            </div>
                        </div>

                        <!-- Sampling Section -->
                        <div class="col-md-7 border-left">
                            <h5 class="text-primary mb-3"><i class="fas fa-clipboard-check"></i> Sampling Berat RM Daging SPO</h5>
                            <div class="form-group">
                                <label>Standar Berat Per PCS (g)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control entry-field" name="entries[${newEntryNum}][standar_berat]" 
                                           placeholder="Masukkan standar berat">
                                    <div class="input-group-append">
                                        <span class="input-group-text">gr</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-light card-outline">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0 small font-weight-bold">Hasil Aktual Berat (gr)</h6>
                                        </div>
                                        <div class="card-body p-2">
                                            <div id="berat-container-${newEntryNum}">
                                                <div class="form-group mb-2">
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" step="0.01" class="form-control entry-field" name="entries[${newEntryNum}][jumlah_rm_value][]" 
                                                               placeholder="Sample 1" min="0">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-success add-berat-btn" data-target="berat-container-${newEntryNum}">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-light card-outline">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0 small font-weight-bold">Summary Sampling</h6>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="form-group mb-2">
                                                <label class="small mb-1">Atas Std (pcs)</label>
                                                <input type="number" class="form-control form-control-sm entry-field" name="entries[${newEntryNum}][berat_atas]" min="0">
                                            </div>
                                            <div class="form-group mb-2">
                                                <label class="small mb-1">Sesuai Std (pcs)</label>
                                                <input type="number" class="form-control form-control-sm entry-field" name="entries[${newEntryNum}][berat_std]" min="0">
                                            </div>
                                            <div class="form-group mb-0">
                                                <label class="small mb-1">Bawah Std (pcs)</label>
                                                <input type="number" class="form-control form-control-sm entry-field" name="entries[${newEntryNum}][berat_bawah]" min="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="small">Status</label>
                                        <select class="form-control form-control-sm entry-field" name="entries[${newEntryNum}][status_rm]">
                                            <option value="">Pilih Status</option>
                                            <option value="diterima">Diterima</option>
                                            <option value="diretur">Diretur</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="small">Catatan</label>
                                        <textarea class="form-control form-control-sm entry-field" name="entries[${newEntryNum}][catatan_rm]" 
                                                rows="2" placeholder="Catatan..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;
        
        $('#entries-container').append(newEntryHtml);
        
        // Initialize Select2
        $(`#nama_rm_${newEntryNum}`).select2({
            placeholder: "Pilih Nama RM",
            allowClear: true,
            width: '100%'
        });
        
        reindexEntries();
        updateSummary();
        
        if (typeof toastr !== 'undefined') {
            toastr.warning('Form Sampling Frozen ditambahkan');
        }
        
        const newEntry = $('.entry-card').last();
        $('html, body').animate({
            scrollTop: newEntry.offset().top - 100
        }, 500);
    });
    
    // Remove entry
    $(document).on('click', '.remove-entry-btn', function() {
        if ($('.entry-card').length > 1) {
            if (confirm('Apakah Anda yakin ingin menghapus entry ini?')) {
                $(this).closest('.entry-card').remove();
                reindexEntries();
                updateSummary();
                
                if (typeof toastr !== 'undefined') {
                    toastr.info('Entry berhasil dihapus');
                }
            }
        } else {
            if (typeof toastr !== 'undefined') {
                toastr.warning('Minimal harus ada 1 entry');
            }
        }
    });
    
    // Handle jumlah_rm dropdown changes
    $(document).on('change', '.jumlah-rm-select', function() {
        const entryNum = $(this).data('entry');
        const container = $(`.jumlah-rm-value-container[data-entry="${entryNum}"]`);
        
        if ($(this).val() === 'proper') {
            container.hide();
            container.find('input').val('');
        } else {
            container.show();
        }
    });
    
    // Update summary on field change
    $(document).on('input change', '.entry-field', function() {
        updateSummary();
    });
    
    // Form validation before submit
    $('#main-chillroom-form').on('submit', function(e) {
        const totalEntries = $('.entry-card').length;
        let validEntries = 0;
        let hasError = false;
        
        $('.entry-card').each(function() {
            const requiredFields = $(this).find('.entry-field[required]');
            let isComplete = true;
            
            requiredFields.each(function() {
                if (!$(this).val() || $(this).val().trim() === '') {
                    isComplete = false;
                    $(this).addClass('is-invalid');
                    hasError = true;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            if (isComplete) {
                validEntries++;
            }
        });
        
        if (validEntries === 0) {
            e.preventDefault();
            if (typeof toastr !== 'undefined') {
                toastr.error('Harap lengkapi minimal 1 entry yang valid');
            }
            
            // Scroll to first invalid field
            if (hasError) {
                $('html, body').animate({
                    scrollTop: $('.is-invalid').first().offset().top - 100
                }, 500);
            }
            return false;
        }
        
        // Check common fields
        if (!$('#tanggal').val() || !$('#shift_id').val()) {
            e.preventDefault();
            if (typeof toastr !== 'undefined') {
                toastr.error('Harap lengkapi tanggal dan shift');
            }
            
            // Scroll to top
            $('html, body').animate({
                scrollTop: 0
            }, 500);
            return false;
        }
        
        // DEBUG: Log form data before submit
        console.log('=== FORM VALIDATION DEBUG ===');
        $('.entry-card').each(function(index) {
            const entryNum = index;
            const beratSamples = $(this).find(`input[name="entries[${entryNum}][jumlah_rm_value][]"]`);
            console.log(`Entry ${entryNum}:`);
            console.log(`  Total berat samples: ${beratSamples.length}`);
            beratSamples.each(function(i) {
                console.log(`  Sample ${i + 1}: ${$(this).val()}`);
            });
        });
    });
    
    // Initialize - HANYA UPDATE SUMMARY, JANGAN REINDEX
    updateSummary();
    
    // Initialize select2 untuk shift dan field lain (exclude select-nama-rm)
    if ($.fn.select2) {
        $('.select2').not('.select-nama-rm').select2();
    }
    
    // Initialize Select2 untuk Nama RM dengan konfigurasi khusus
    $('.select-nama-rm').select2({
        placeholder: "Pilih Nama RM",
        allowClear: true,
        width: '100%',
        dropdownAutoWidth: true,
        language: {
            noResults: function() {
                return "Data tidak ditemukan";
            }
        }
    });
    
    // Check initial state for original form
    $('.jumlah-rm-select').each(function() {
        const entryNum = $(this).data('entry');
        const container = $(`.jumlah-rm-value-container[data-entry="${entryNum}"]`);
        
        if ($(this).val() === 'proper') {
            container.hide();
        }
    });
    
    // Dynamic berat samples functionality
    let sampleCounters = {};
    
    // Add new berat sample
    $(document).on('click', '.add-berat-btn', function() {
        if (isUserQc) return;
        const targetContainer = $(this).data('target');
        const container = $('#' + targetContainer);
        
        if (!sampleCounters[targetContainer]) {
            sampleCounters[targetContainer] = container.find('.form-group').length;
        }
        
        sampleCounters[targetContainer]++;
        
        // PERBAIKAN: Extract entry number dari container ID
        const entryNum = parseInt(targetContainer.replace('berat-container-', ''));
        
        const newSampleHtml = `
            <div class="form-group">
                <label>Berat Sample ${sampleCounters[targetContainer]} (gr)</label>
                <div class="input-group">
                    <input type="number" step="0.01" class="form-control entry-field" name="entries[${entryNum}][jumlah_rm_value][]"
                           placeholder="Masukkan berat sample" min="0">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-sm remove-berat-btn">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        container.append(newSampleHtml);
        
        // DEBUG: Log setelah menambah sample
        console.log(`Added sample to entry ${entryNum}, total samples now: ${container.find('.form-group').length}`);
    });
    
    // Remove berat sample
    $(document).on('click', '.remove-berat-btn', function() {
        if (isUserQc) return;
        const container = $(this).closest('.form-group').parent();
        const targetContainer = container.attr('id');
        
        if (container.find('.form-group').length > 1) {
            $(this).closest('.form-group').remove();
            
            // Re-label all samples
            if (targetContainer && sampleCounters[targetContainer]) {
                container.find('.form-group').each(function(index) {
                    $(this).find('label').text(`Berat Sample ${index + 1} (gr)`);
                });
                sampleCounters[targetContainer] = container.find('.form-group').length;
            }
            
            // DEBUG: Log setelah menghapus sample
            const entryNum = targetContainer.replace('berat-container-', '');
            console.log(`Removed sample from entry ${entryNum}, total samples now: ${container.find('.form-group').length}`);
        } else {
            if (typeof toastr !== 'undefined') {
                toastr.warning('Minimal harus ada 1 sample berat');
            }
        }
    });
});
</script>
@endpush
@endsection