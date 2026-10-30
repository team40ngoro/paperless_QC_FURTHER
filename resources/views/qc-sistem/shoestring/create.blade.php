@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pemeriksaan Kedatangan Bahan Baku</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shoestring.index') }}">Shoestring</a></li>
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
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Form Input Data Shoestring</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('shoestring.store') }}" method="POST" id="main-shoestring-form" enctype="multipart/form-data">                                
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
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tanggal">Tanggal Produksi <span class="text-danger">*</span></label>
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
                                                <h3 class="card-title"><i class="fas fa-box"></i> Entry #1</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Nama Produsen <span class="text-danger">*</span></label>
                                                            <select class="form-control entry-field select-produsen" name="entries[0][nama_produsen]" id="nama_produsen_0" required>
                                                                <option value="">Pilih Produsen</option>
                                                                @foreach($produsens as $produsen)
                                                                    <option value="{{ $produsen }}">{{ $produsen }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Kode Produksi <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control entry-field" name="entries[0][kode_produksi]" 
                                                                   placeholder="Masukkan kode produksi" value="{{ old('entries.0.kode_produksi') }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Tanggal Expired <span class="text-danger">*</span></label>
                                                            <input type="date" class="form-control entry-field" name="entries[0][best_before]" value="{{ old('entries.0.best_before') }}" required>
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label for="berat_sampling_0">Gramase (gram)</label>
                                                            <select name="entries[0][berat_sampling]" id="berat_sampling_0" class="form-control entry-field select2-berat" data-nilai-berat data-selected="{{ old('entries.0.berat_sampling') }}"></select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card card-outline card-secondary defect-qty-card" style="display:none;" id="defect-card-0">
                                                            <div class="card-header bg-light py-2">
                                                                <h6 class="card-title text-primary m-0" style="font-size: 0.95rem; font-weight: 600;"><i class="fas fa-exclamation-triangle"></i> Daftar Item Defect</h6>
                                                            </div>
                                                            <div class="card-body py-2 px-3" id="defect-qty-container-0">
                                                                <!-- Kategori & Items input akan masuk di sini secara dinamis -->
                                                            </div>
                                                        </div>

                                                        <div class="form-group mt-3">
                                                            <label>Total Defect</label>
                                                            <input type="text" class="form-control entry-field" name="entries[0][total_defect]" id="total_defect_0" value="0" readonly>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Keterangan</label>
                                                            <textarea class="form-control entry-field" name="entries[0][catatan]" 
                                                                rows="4" placeholder="Masukkan keterangan tambahan"></textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Dokumentasi Label Supplier <span class="text-danger">*</span></label>
                                                            <input type="file" class="form-control-file dokumentasi-input" accept="image/*" capture="camera" multiple>
                                                            <div class="dokumentasi-preview mt-2" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="">
                                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                                <i class="fas fa-save"></i> Simpan <span id="submit-count">1</span> Data
                                            </button>
                                            <button type="button" class="btn btn-success ml-2" id="add-entry-btn">
                                                <i class="fas fa-plus"></i> Tambah Entry
                                            </button>
                                            <a href="{{ route('shoestring.index') }}" class="btn btn-secondary ml-2">
                                                <i class="fas fa-arrow-left"></i> Kembali
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@push('styles')
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let entryCounter = 0;

    function initBeratSelects() {
        var nilaiBerat = ['-', 55, 100, 200, 225, 250, 300, 315, 360, 400, 450, 500, 700, 900, 1000, 1100, 2000];
        $('[data-nilai-berat]').each(function() {
            var $select = $(this);
            if ($select.find('option').length === 0) {
                var selectedValue = $select.data('selected');
                $select.empty();
                $select.append('<option value="">Pilih Nilai</option>');
                $.each(nilaiBerat, function(i, val) {
                    $select.append('<option value="' + val + '">' + val + '</option>');
                });
                $select.select2({ placeholder: "Pilih Nilai Berat" });
                if (selectedValue !== undefined && selectedValue !== null && selectedValue !== '') {
                    $select.val(String(selectedValue)).trigger('change');
                }
            }
        });
    }

    initBeratSelects();
    
    // Function to update summary
    function updateSummary() {
        const totalEntries = $('.entry-card').length;
        let validEntries = 0;
        let incompleteEntries = 0;
        
        $('.entry-card').each(function() {
            const requiredFields = $(this).find('.entry-field[required]');
            let isComplete = true;
            
            requiredFields.each(function() {
                const $field = $(this);
                let fieldValue = $field.val();
                
                if (Array.isArray(fieldValue)) {
                    if (fieldValue.length === 0 || (fieldValue.length === 1 && fieldValue[0] === '')) {
                        isComplete = false;
                        return false;
                    }
                } else {
                    if (!fieldValue || fieldValue.trim() === '') {
                        isComplete = false;
                        return false;
                    }
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
            $(this).find('.card-title').html(`<i class="fas fa-box"></i> Entry #${index + 1}`);
            
            // Update name attributes
            $(this).find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                if (name && name.startsWith('entries[')) {
                    const match = name.match(/^entries\[\d+\](.+)$/);
                    if (match) {
                        const fieldPart = match[1];
                        $(this).attr('name', `entries[${entryNum}]${fieldPart}`);
                    }
                }
            });
            
            // Update IDs
            $(this).find('.select-produsen').attr('id', `nama_produsen_${entryNum}`);
            $(this).find('.defect-qty-card').attr('id', `defect-card-${entryNum}`);
            $(this).find('[id^="defect-qty-container-"]').attr('id', `defect-qty-container-${entryNum}`);
            $(this).find('[id^="total_defect_"]').attr('id', `total_defect_${entryNum}`);
            
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
        
        const newEntryHtml = `
        <div class="entry-card" data-entry="${newEntryNum + 1}">
            <div class="card card-primary card-outline">
                <div class="card-header border-bottom py-2">
                    <h3 class="card-title"><i class="fas fa-box"></i> Entry #${newEntryNum + 1}</h3>
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
                                <label>Nama Produsen <span class="text-danger">*</span></label>
                                <select class="form-control entry-field select-produsen" name="entries[${newEntryNum}][nama_produsen]" id="nama_produsen_${newEntryNum}" required>
                                    <option value="">Pilih Produsen</option>
                                    @foreach($produsens as $produsen)
                                        <option value="{{ $produsen }}">{{ $produsen }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Kode Produksi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control entry-field" name="entries[${newEntryNum}][kode_produksi]" 
                                       placeholder="Masukkan kode produksi" required>
                            </div>
                             <div class="form-group">
                                 <label>Tanggal Expired <span class="text-danger">*</span></label>
                                 <input type="date" class="form-control entry-field" name="entries[${newEntryNum}][best_before]" required>
                             </div>
                             <div class="form-group mb-2">
                                 <label for="berat_sampling_${newEntryNum}">Gramase (gram)</label>
                                 <select name="entries[${newEntryNum}][berat_sampling]" id="berat_sampling_${newEntryNum}" class="form-control entry-field select2-berat" data-nilai-berat></select>
                             </div>
                         </div>
                         <div class="col-md-6">
                            <div class="card card-outline card-secondary defect-qty-card" style="display:none;" id="defect-card-${newEntryNum}">
                                <div class="card-header bg-light py-2">
                                    <h6 class="card-title text-primary m-0" style="font-size: 0.95rem; font-weight: 600;"><i class="fas fa-exclamation-triangle"></i> Daftar Item Defect</h6>
                                </div>
                                <div class="card-body py-2 px-3" id="defect-qty-container-${newEntryNum}">
                                    <!-- Kategori & Items input akan masuk di sini secara dinamis -->
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label>Total Defect</label>
                                <input type="text" class="form-control entry-field" name="entries[${newEntryNum}][total_defect]" id="total_defect_${newEntryNum}" value="0" readonly>
                            </div>

                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea class="form-control entry-field" name="entries[${newEntryNum}][catatan]" 
                                    rows="4" placeholder="Masukkan keterangan tambahan"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Dokumentasi Label Supplier <span class="text-danger">*</span></label>
                                <input type="file" class="form-control-file dokumentasi-input" accept="image/*" capture="camera" multiple>
                                <div class="dokumentasi-preview mt-2" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;
        
        $('#entries-container').append(newEntryHtml);
        
        reindexEntries();
        initBeratSelects();
        updateSummary();
        
        if (typeof toastr !== 'undefined') {
            toastr.success('Entry baru berhasil ditambahkan');
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

    // Fetch and render defects when producer dropdown changes
    $(document).on('change', '.select-produsen', function() {
        const $entryCard = $(this).closest('.entry-card');
        const entryIndex = $entryCard.index();
        const produsenVal = $(this).val();
        const $defectCard = $entryCard.find(`.defect-qty-card`);
        const $qtyContainer = $entryCard.find(`#defect-qty-container-${entryIndex}`);

        if (!produsenVal) {
            $qtyContainer.html('');
            $defectCard.hide();
            $entryCard.find(`#total_defect_${entryIndex}`).val(0);
            updateSummary();
            return;
        }

        // Add loading indicator
        $qtyContainer.html('<div class="text-muted"><i class="fas fa-spinner fa-spin"></i> Memuat data defect...</div>');
        $defectCard.show();

        $.ajax({
            url: '{{ route("ajax.get-defect-by-produsen") }}',
            type: 'GET',
            data: { nama_produsen: produsenVal },
            success: function(response) {
                if (response.success && response.detail_defect && response.detail_defect.length > 0) {
                    let html = '';
                    response.detail_defect.forEach(cat => {
                        html += `
                            <div class="defect-category-section mb-3">
                                <h6 class="font-weight-bold text-secondary border-bottom pb-1 mb-2">
                                    <i class="fas fa-folder-open"></i> Kategori: ${cat.kategori}
                                </h6>
                                <div class="pl-1">
                        `;
                        if (cat.items && cat.items.length > 0) {
                            // Header row
                            html += `
                                <div class="row no-gutters mb-1 px-1">
                                    <div class="col-4" style="font-size:0.75rem;font-weight:600;color:#888;">Jenis</div>
                                    <div class="col-3" style="font-size:0.75rem;font-weight:600;color:#888;">Sub Jenis</div>
                                    <div class="col-3" style="font-size:0.75rem;font-weight:600;color:#888;">Spesifikasi</div>
                                    <div class="col-2 text-center" style="font-size:0.75rem;font-weight:600;color:#888;">Qty</div>
                                </div>
                            `;
                            cat.items.forEach(item => {
                                // Sertakan sub_jenis agar tiap sub defect punya key unik (sebelumnya saling menimpa)
                                const label_defect = [item.jenis, item.sub_jenis, item.spesifikasi]
                                    .filter(v => v && String(v).trim() !== '')
                                    .join(' - ')
                                    .replace(/[\[\]"]/g, '');
                                const sub_jenis_display = item.sub_jenis
                                    ? `<span class="badge badge-warning" style="font-size:0.75rem; white-space:normal; text-align:left;">${item.sub_jenis}</span>`
                                    : '<span class="text-muted">-</span>';
                                const spesifikasi_display = item.spesifikasi || '<span class="text-muted">-</span>';
                                html += `
                                    <div class="row no-gutters align-items-center mb-2 px-1">
                                        <div class="col-4 text-dark pr-1" style="font-size:0.85rem;line-height:1.3;">${item.jenis}</div>
                                        <div class="col-3 text-secondary pr-1" style="font-size:0.85rem;line-height:1.3;">${sub_jenis_display}</div>
                                        <div class="col-3 text-secondary pr-1" style="font-size:0.85rem;line-height:1.3;">${spesifikasi_display}</div>
                                        <div class="col-2">
                                            <input type="text" 
                                                   class="form-control form-control-sm defect-qty-input entry-field" 
                                                   name="entries[${entryIndex}][sampling_defect_qty][${label_defect}]" 
                                                   placeholder="0" 
                                                   value="" 
                                                   style="border-radius:4px;text-align:center;">
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            html += `<div class="text-muted font-italic pl-2" style="font-size: 0.85rem;">Tidak ada item defect.</div>`;
                        }
                        html += `
                                </div>
                            </div>
                        `;
                    });
                    $qtyContainer.html(html);
                } else {
                    $qtyContainer.html('<div class="text-warning"><i class="fas fa-exclamation-circle"></i> Tidak ada data defect untuk produsen ini.</div>');
                }
                updateTotalDefect(entryIndex);
                updateSummary();
            },
            error: function(xhr) {
                console.error(xhr);
                $qtyContainer.html('<div class="text-danger"><i class="fas fa-times-circle"></i> Gagal memuat data defect.</div>');
                updateTotalDefect(entryIndex);
                updateSummary();
            }
        });
    });
    
    // Update summary on field change
    $(document).on('input change', '.entry-field', function() {
        updateSummary();
    });

    function updateTotalDefect(entryIndex) {
        const $entryCard = $('.entry-card').eq(entryIndex);
        const $totalField = $entryCard.find(`#total_defect_${entryIndex}`);
        if ($totalField.length === 0) return;

        let total = 0;
        $entryCard.find('.defect-qty-input').each(function() {
            const v = $(this).val();
            if (v !== null && v !== '' && !isNaN(v)) {
                total += parseFloat(v);
            }
        });
        $totalField.val(total);
    }

    $(document).on('input', '.defect-qty-input', function() {
        const $entryCard = $(this).closest('.entry-card');
        const entryIndex = $entryCard.index();
        updateTotalDefect(entryIndex);
    });

    // Form submit - simple validation only
    $('#main-shoestring-form').on('submit', function(e) {
        e.preventDefault();
        
        const shift = $('#shift_id').val();
        if (!shift || shift === '') {
            if (typeof toastr !== 'undefined') {
                toastr.error('Shift harus dipilih');
            } else {
                alert('Shift harus dipilih');
            }
            $('#shift_id').addClass('is-invalid');
            return false;
        }
        
        let validationPass = true;
        $('.entry-card').each(function(index) {
            const namaProdusen = $(this).find(`.select-produsen`).val();
            const kodeProduksi = $(this).find(`input[name="entries[${index}][kode_produksi]"]`).val();
            const bestBefore = $(this).find(`input[name="entries[${index}][best_before]"]`).val();
            
            if (!namaProdusen || !kodeProduksi || !bestBefore) {
                alert(`Mohon lengkapi semua field yang required di Entry #${index + 1} (Nama Produsen, Kode Produksi, Tanggal Expired)`);
                validationPass = false;
                return false;
            }
        });
        
        if (validationPass) {
            this.submit();
        }
    });

    // Logic untuk kompresi dokumentasi (image upload)
    $(document).on('change', '.dokumentasi-input', function(e) {
        const $container = $(this).closest('.form-group');
        const $previewContainer = $container.find('.dokumentasi-preview');
        const entryNumAttr = $(this).closest('.entry-card').attr('data-entry');
        const entryIndex = parseInt(entryNumAttr) - 1;
        
        $previewContainer.empty();
        $container.find('input[type="hidden"].dokumentasi-base64').remove();
        
        const files = e.target.files;
        if (!files || files.length === 0) return;
        
        Array.from(files).forEach((file, fileIndex) => {
            if (!file.type.match('image.*')) return;
            
            const reader = new FileReader();
            reader.onload = function(readerEvent) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;
                    const MAX_WIDTH = 1200;
                    const MAX_HEIGHT = 1200;
                    
                    if (width > height) {
                        if (width > MAX_WIDTH) { height *= MAX_WIDTH / width; width = MAX_WIDTH; }
                    } else {
                        if (height > MAX_HEIGHT) { width *= MAX_HEIGHT / height; height = MAX_HEIGHT; }
                    }
                    
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    
                    const dataUrl = canvas.toDataURL('image/jpeg', 0.7);
                    
                    const imgElement = $(`<img src="${dataUrl}" class="img-thumbnail" style="height: 100px; width: 100px; object-fit: cover;">`);
                    $previewContainer.append(imgElement);
                    
                    const hiddenInput = $(`<input type="hidden" name="entries[${entryIndex}][dokumentasi_base64][]" class="dokumentasi-base64">`);
                    hiddenInput.val(dataUrl);
                    $container.append(hiddenInput);
                };
                img.src = readerEvent.target.result;
            };
            reader.readAsDataURL(file);
        });
    });

    // Initialize
    reindexEntries();
    updateSummary();
});
</script>
@endpush

@endsection