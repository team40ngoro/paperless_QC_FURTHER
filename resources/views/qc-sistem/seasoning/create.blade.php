@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Data Pemeriksaan Kedatangan Bahan Baku</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('seasoning.index') }}">Seasoning</a></li>
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
                                <h3 class="card-title">Form Penyimpanan Bahan Seasoning</h3>
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
                            <form class="form-horizontal mb-4" id="main-seasoning-form" method="POST" action="{{ route('seasoning.store') }}">
                                @csrf
                                <div class="card-body">
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
                                                <div class="col-md-6">
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
                                                    <div class="form-group">
                                                        <label for="jam">Jam <span class="text-danger">*</span></label>
                                                        <input type="time" class="form-control @error('jam') is-invalid @enderror" id="jam" name="jam" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                                        @error('jam')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="shift_id">Shift <span class="text-danger">*</span></label>
                                                        <select class="form-control select2" name="shift_id" id="shift_id" required>
                                                            <option value="">Pilih Shift</option>
                                                            @foreach($shifts as $shift)
                                                                <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->shift }}</option>
                                                            @endforeach
                                                        </select>
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
                                                    <h3 class="card-title"><i class="fas fa-flask"></i> Entry #1</h3>
                                                </div>
                                                <div class="card-body">

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Nama Seasoning <span class="text-danger">*</span></label>
                                                                <select name="entries[0][nama_rm]" id="nama_rm_0" class="form-control entry-field select-nama-rm" required>
                                                                    <option value="">Pilih Nama Seasoning</option>
                                                                    @foreach($dataSeasoning as $seasoning)
                                                                        <option value="{{ $seasoning->nama_seasoning }}">{{ $seasoning->nama_seasoning }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Kode Produksi <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control entry-field" name="entries[0][kode_produksi]" 
                                                                       placeholder="Masukkan kode produksi" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Berat Per Pack (kg) <span class="text-danger">*</span></label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control entry-field" name="entries[0][berat]" 
                                                                           placeholder="Masukkan berat" required>
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">kg</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Kemasan <span class="text-danger">*</span></label>
                                                                <select class="form-control entry-field" name="entries[0][kemasan]" required>
                                                                    <option value="">Pilih Status Kemasan</option>
                                                                    <option value="✔">✔ Baik</option>
                                                                    <option value="✘">✘ Tidak Baik</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Keterangan</label>
                                                                <textarea class="form-control entry-field" name="entries[0][keterangan]" 
                                                                          rows="3" placeholder="Masukkan keterangan tambahan"></textarea>
                                                            </div>
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
@push('scripts')
<script>
$(document).ready(function() {
    let entryCounter = 0;
    
    // Initialize Select2 untuk form pertama
    $('.select-nama-rm').select2({
        placeholder: "Pilih Nama RM",
        allowClear: true,
        width: '100%'
    });
    
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
    
    // Function to reindex entries
    function reindexEntries() {
        $('.entry-card').each(function(index) {
            const entryNum = index;
            $(this).attr('data-entry', index + 1);
            $(this).find('.card-title').html(`<i class="fas fa-flask"></i> Entry #${index + 1}`);
            
            // Update name attributes
            $(this).find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                if (name && name.startsWith('entries[')) {
                    const fieldName = name.split('][')[1];
                    $(this).attr('name', `entries[${entryNum}][${fieldName}`);
                    
                    // Update ID untuk select nama_rm
                    if (fieldName === 'nama_rm]') {
                        $(this).attr('id', `nama_rm_${entryNum}`);
                    }
                }
            });
            
            // Show/hide remove button
            const removeBtn = $(this).find('.remove-entry-btn');
            if ($('.entry-card').length > 1) {
                removeBtn.show();
            } else {
                removeBtn.hide();
            }
        });
    }
    
    // Add new entry
    $('#add-entry-btn').click(function() {
        entryCounter++;
        const newEntryNum = $('.entry-card').length;
        
        // Build options untuk dropdown Nama Seasoning
        let namaRmOptions = '<option value="">Pilih Nama Seasoning</option>';
        @foreach($dataSeasoning as $seasoning)
            namaRmOptions += '<option value="{{ $seasoning->nama_seasoning }}">{{ $seasoning->nama_seasoning }}</option>';
        @endforeach
        
        const newEntryHtml = `
        <div class="entry-card" data-entry="${newEntryNum + 1}">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-flask"></i> Entry #${newEntryNum + 1}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-danger btn-sm remove-entry-btn">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Seasoning <span class="text-danger">*</span></label>
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
                                <label>Berat Per Pack (kg) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control entry-field" name="entries[${newEntryNum}][berat]" 
                                           placeholder="Masukkan berat" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kemasan <span class="text-danger">*</span></label>
                                <select class="form-control entry-field" name="entries[${newEntryNum}][kemasan]" required>
                                    <option value="">Pilih Status Kemasan</option>
                                    <option value="✔">✔ Baik</option>
                                    <option value="✘">✘ Tidak Baik</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea class="form-control entry-field" name="entries[${newEntryNum}][keterangan]" 
                                          rows="3" placeholder="Masukkan keterangan tambahan"></textarea>
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
            width: '100%'
        });
        
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
    
    // Monitor changes in entry fields
    $(document).on('change keyup', '.entry-field', function() {
        updateSummary();
    });
    
    // Initial summary update
    updateSummary();
    reindexEntries();
    
    // Debug form submission
    $('#main-seasoning-form').on('submit', function(e) {
        e.preventDefault(); // Prevent submission temporarily for debugging
        
        const formData = new FormData(this);
        const jamValue = formData.get('jam');
        const tanggalValue = formData.get('tanggal');
        const shiftValue = formData.get('shift_id');
        
        console.log('=== FORM SUBMISSION DEBUG ===');
        console.log('Tanggal:', tanggalValue);
        console.log('Jam:', jamValue);
        console.log('Shift ID:', shiftValue);
        console.log('All form data:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        console.log('=== END DEBUG ===');
        
        // Show alert with jam value
        // alert('DEBUG INFO:\n\nTanggal: ' + tanggalValue + '\nJam: ' + jamValue + '\nShift ID: ' + shiftValue + '\n\nCek console untuk detail lengkap!');
        
        // ENABLE ACTUAL SUBMISSION
        this.submit();
    });
});
</script>
@endpush

@endsection