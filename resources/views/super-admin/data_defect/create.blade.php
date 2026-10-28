@extends('layouts.app')

@section('container')
<style>
    .kategori-block {
        border-left: 4px solid #17a2b8;
        padding-left: 15px;
        background-color: #fcfcfc;
    }
    .form-card {
        border-top: 3px solid #007bff;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Data Defect</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('data-defect.index') }}">Data Defect</a></li>
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
                        <div class="card card-primary card-outline">
                            <div class="card-header bg-light">
                                <h3 class="card-title font-weight-bold text-primary">Form Tambah Data Defect</h3>
                                
                            </div>
                            <form action="{{ route('data-defect.store') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show">
                                            <h5><i class="icon fas fa-ban text-white"></i> Error!</h5>
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    @if(session('error'))
                                        <div class="alert alert-danger alert-dismissible fade show">
                                            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    <div class="form-group col-md-6 pl-0">
                                        <label for="id_plan" class="font-weight-bold text-muted small text-uppercase">Plan <span class="text-danger">*</span></label>
                                        <select name="id_plan" id="id_plan" class="form-control @error('id_plan') is-invalid @enderror select2" required>
                                            <option value="">Pilih Plan</option>
                                            @foreach($plans as $plan)
                                                <option value="{{ $plan->id }}" {{ old('id_plan') == $plan->id ? 'selected' : '' }}>
                                                    {{ $plan->nama_plan }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_plan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr>
                                    <h5 class="mb-4 text-secondary font-weight-bold"><i class="fas fa-list mr-2"></i>Daftar Defect per Produsen</h5>

                                    {{-- Container untuk dynamic forms --}}
                                    <div id="formContainer">
                                        {{-- Form pertama (default) --}}
                                        <div class="card card-outline card-info mb-4 form-card" data-form-index="0">
                                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                <h3 class="card-title font-weight-bold text-info mb-0">
                                                    <i class="fas fa-industry mr-2"></i>Produsen #1
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                {{-- Nama Produsen --}}
                                                <div class="form-group mb-4">
                                                    <label class="font-weight-bold text-muted small text-uppercase">Nama Produsen <span class="text-danger">*</span></label>
                                                    <input type="text" name="nama_produsen[0]"
                                                           class="form-control @error('nama_produsen.0') is-invalid @enderror"
                                                           placeholder="Contoh: Shoestring Farm Frites"
                                                           value="{{ old('nama_produsen.0') }}" required>
                                                    @error('nama_produsen.0')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- List Kategori --}}
                                                <div class="kategori-list" data-form-index="0">
                                                    {{-- Kategori #1 (default) --}}
                                                    <div class="card card-body mb-3 kategori-block border-left border-info" data-kategori-index="0">
                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                            <div class="form-group mb-0 flex-grow-1 mr-3">
                                                                <label class="font-weight-bold text-info small text-uppercase">Kategori Ketidaksesuaian <span class="text-danger">*</span></label>
                                                                <input type="text" name="kategori[0][0][nama]" class="form-control form-control-sm" placeholder="Contoh: Ukuran atau Defects" required>
                                                            </div>
                                                            <div>
                                                                <button type="button" class="btn btn-danger btn-xs btn-remove-kategori mt-4" disabled>
                                                                    <i class="fas fa-times-circle mr-1"></i> Hapus Kategori
                                                                </button>
                                                            </div>
                                                        </div>

                                                        {{-- Detail Items list di bawah Kategori --}}
                                                        <div class="items-list" data-form-index="0" data-kategori-index="0">
                                                            <div class="item-row row mb-2 align-items-center" data-item-index="0">
                                                                <div class="col-md-4">
                                                                    <label class="small text-muted mb-1 font-weight-bold">Jenis Ketidaksesuaian <span class="text-danger">*</span></label>
                                                                    <input type="text" name="kategori[0][0][items][0][jenis]" class="form-control form-control-sm" placeholder="Contoh: > 75 mm" required>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="small text-muted mb-1 font-weight-bold">Sub Jenis</label>
                                                                    <input type="text" name="kategori[0][0][items][0][sub_jenis]" class="form-control form-control-sm" placeholder="Contoh: Sub Jenis Defect">
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="small text-muted mb-1 font-weight-bold">Spesifikasi (Min/Max/toleransi)</label>
                                                                    <input type="text" name="kategori[0][0][items][0][spesifikasi]" class="form-control form-control-sm" placeholder="Contoh: Min 20% atau 0%">
                                                                </div>
                                                                <div class="col-md-2 text-right">
                                                                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove-item mt-4" disabled>
                                                                        <i class="fas fa-minus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-2">
                                                            <button type="button" class="btn btn-outline-info btn-xs btn-add-item" data-form-index="0" data-kategori-index="0">
                                                                <i class="fas fa-plus mr-1"></i> Tambah Keterangan Defect
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-3">
                                                    <button type="button" class="btn btn-info btn-sm btn-add-kategori" data-form-index="0">
                                                        <i class="fas fa-folder-plus mr-1"></i> Tambah Kategori Baru
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button type="button" class="btn btn-success px-4 shadow-sm" id="addFormBtn">
                                        <i class="fas fa-plus mr-1"></i> Tambah Produsen
                                    </button>
                                    <a href="{{ route('data-defect.index') }}" class="btn btn-secondary px-4">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                        <i class="fas fa-save mr-1"></i> Simpan Semua
                                    </button>
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
$(document).ready(function () {
    let formCounter = 0; // index form produsen (0-based)

    // Helper untuk update state disable tombol hapus
    function updateStateButtons(container) {
        // Update item row remove buttons
        container.find('.items-list').each(function() {
            const rows = $(this).find('.item-row');
            rows.find('.btn-remove-item').prop('disabled', rows.length <= 1);
        });

        // Update kategori remove buttons
        container.find('.kategori-list').each(function() {
            const blocks = $(this).find('.kategori-block');
            blocks.find('.btn-remove-kategori').prop('disabled', blocks.length <= 1);
        });
    }

    // Template item row baru
    function buildItemRow(formIdx, katIdx, itemIdx) {
        return `
            <div class="item-row row mb-2 align-items-center" data-item-index="${itemIdx}">
                <div class="col-md-4">
                    <label class="small text-muted mb-1 font-weight-bold">Jenis Ketidaksesuaian <span class="text-danger">*</span></label>
                    <input type="text" name="kategori[${formIdx}][${katIdx}][items][${itemIdx}][jenis]" class="form-control form-control-sm" placeholder="Contoh: > 75 mm" required>
                </div>
                <div class="col-md-3">
                    <label class="small text-muted mb-1 font-weight-bold">Sub Jenis</label>
                    <input type="text" name="kategori[${formIdx}][${katIdx}][items][${itemIdx}][sub_jenis]" class="form-control form-control-sm" placeholder="Contoh: Sub Jenis Defect">
                </div>
                <div class="col-md-3">
                    <label class="small text-muted mb-1 font-weight-bold">Spesifikasi (Min/Max/toleransi)</label>
                    <input type="text" name="kategori[${formIdx}][${katIdx}][items][${itemIdx}][spesifikasi]" class="form-control form-control-sm" placeholder="Contoh: Min 20% atau 0%">
                </div>
                <div class="col-md-2 text-right">
                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove-item mt-4">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>`;
    }

    // Template kategori block baru
    function buildKategoriBlock(formIdx, katIdx) {
        return `
            <div class="card card-body mb-3 kategori-block border-left border-info" data-kategori-index="${katIdx}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-group mb-0 flex-grow-1 mr-3">
                        <label class="font-weight-bold text-info small text-uppercase">Kategori Ketidaksesuaian <span class="text-danger">*</span></label>
                        <input type="text" name="kategori[${formIdx}][${katIdx}][nama]" class="form-control form-control-sm" placeholder="Contoh: Ukuran atau Defects" required>
                    </div>
                    <div>
                        <button type="button" class="btn btn-danger btn-xs btn-remove-kategori mt-4">
                            <i class="fas fa-times-circle mr-1"></i> Hapus Kategori
                        </button>
                    </div>
                </div>

                <div class="items-list" data-form-index="${formIdx}" data-kategori-index="${katIdx}">
                    <div class="item-row row mb-2 align-items-center" data-item-index="0">
                        <div class="col-md-4">
                            <label class="small text-muted mb-1 font-weight-bold">Jenis Ketidaksesuaian <span class="text-danger">*</span></label>
                            <input type="text" name="kategori[${formIdx}][${katIdx}][items][0][jenis]" class="form-control form-control-sm" placeholder="Contoh: > 75 mm" required>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted mb-1 font-weight-bold">Sub Jenis</label>
                            <input type="text" name="kategori[${formIdx}][${katIdx}][items][0][sub_jenis]" class="form-control form-control-sm" placeholder="Contoh: Sub Jenis Defect">
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted mb-1 font-weight-bold">Spesifikasi (Min/Max/toleransi)</label>
                            <input type="text" name="kategori[${formIdx}][${katIdx}][items][0][spesifikasi]" class="form-control form-control-sm" placeholder="Contoh: Min 20% atau 0%">
                        </div>
                        <div class="col-md-2 text-right">
                            <button type="button" class="btn btn-outline-danger btn-sm btn-remove-item mt-4" disabled>
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-outline-info btn-xs btn-add-item" data-form-index="${formIdx}" data-kategori-index="${katIdx}">
                        <i class="fas fa-plus mr-1"></i> Tambah Keterangan Defect
                    </button>
                </div>
            </div>`;
    }

    // Template Produsen Form Card baru
    function buildFormCard(formIdx) {
        return `
            <div class="card card-outline card-info mb-4 form-card" data-form-index="${formIdx}">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-info mb-0">
                        <i class="fas fa-industry mr-2"></i>Produsen #${formIdx + 1}
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-form">
                            <i class="fas fa-trash mr-1"></i> Hapus Produsen
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-muted small text-uppercase">Nama Produsen <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produsen[${formIdx}]" class="form-control" placeholder="Contoh: Shoestring Farm Frites" required>
                    </div>

                    <div class="kategori-list" data-form-index="${formIdx}">
                        <div class="card card-body mb-3 kategori-block border-left border-info" data-kategori-index="0">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-group mb-0 flex-grow-1 mr-3">
                                    <label class="font-weight-bold text-info small text-uppercase">Kategori Ketidaksesuaian <span class="text-danger">*</span></label>
                                    <input type="text" name="kategori[${formIdx}][0][nama]" class="form-control form-control-sm" placeholder="Contoh: Ukuran atau Defects" required>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-danger btn-xs btn-remove-kategori mt-4" disabled>
                                        <i class="fas fa-times-circle mr-1"></i> Hapus Kategori
                                    </button>
                                </div>
                            </div>

                            <div class="items-list" data-form-index="${formIdx}" data-kategori-index="0">
                                <div class="item-row row mb-2 align-items-center" data-item-index="0">
                                    <div class="col-md-4">
                                        <label class="small text-muted mb-1 font-weight-bold">Jenis Ketidaksesuaian <span class="text-danger">*</span></label>
                                        <input type="text" name="kategori[${formIdx}][0][items][0][jenis]" class="form-control form-control-sm" placeholder="Contoh: > 75 mm" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small text-muted mb-1 font-weight-bold">Sub Jenis</label>
                                        <input type="text" name="kategori[${formIdx}][0][items][0][sub_jenis]" class="form-control form-control-sm" placeholder="Contoh: Sub Jenis Defect">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small text-muted mb-1 font-weight-bold">Spesifikasi (Min/Max/toleransi)</label>
                                        <input type="text" name="kategori[${formIdx}][0][items][0][spesifikasi]" class="form-control form-control-sm" placeholder="Contoh: Min 20% atau 0%">
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-item mt-4" disabled>
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-outline-info btn-xs btn-add-item" data-form-index="${formIdx}" data-kategori-index="0">
                                    <i class="fas fa-plus mr-1"></i> Tambah Keterangan Defect
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-info btn-sm btn-add-kategori" data-form-index="${formIdx}">
                            <i class="fas fa-folder-plus mr-1"></i> Tambah Kategori Baru
                        </button>
                    </div>
                </div>
            </div>`;
    }

    // ───── Tambah Form Produsen ─────────────────────────────────────────────
    $('#addFormBtn').on('click', function () {
        formCounter++;
        $('#formContainer').append(buildFormCard(formCounter));
        if (typeof toastr !== 'undefined') {
            toastr.success('Form Produsen baru berhasil ditambahkan');
        }
        updateStateButtons($('#formContainer'));
    });

    // ───── Hapus Form Produsen ──────────────────────────────────────────────
    $(document).on('click', '.btn-remove-form', function () {
        const card = $(this).closest('.form-card');
        if (confirm('Hapus form Produsen ini?')) {
            card.remove();
            // Renumber form titles
            $('#formContainer .form-card').each(function(index) {
                $(this).find('.card-title').html('<i class="fas fa-industry mr-2"></i>Produsen #' + (index + 1));
            });
        }
    });

    // ───── Tambah Kategori ──────────────────────────────────────────────────
    $(document).on('click', '.btn-add-kategori', function () {
        const formIdx = $(this).data('form-index');
        const container = $(this).closest('.card-body').find('.kategori-list');
        const nextKatIdx = container.find('.kategori-block').length;

        container.append(buildKategoriBlock(formIdx, nextKatIdx));
        updateStateButtons($(this).closest('.form-card'));
    });

    // ───── Hapus Kategori ───────────────────────────────────────────────────
    $(document).on('click', '.btn-remove-kategori', function () {
        const block = $(this).closest('.kategori-block');
        const container = block.closest('.kategori-list');
        if (confirm('Hapus seluruh kategori ini beserta defect di dalamnya?')) {
            block.remove();
            
            // Renumber array indexes untuk kategori yang tersisa agar submitnya berurutan
            const formIdx = container.data('form-index');
            container.find('.kategori-block').each(function(katIdx) {
                $(this).attr('data-kategori-index', katIdx);
                $(this).find('input[name*="[nama]"]').attr('name', `kategori[${formIdx}][${katIdx}][nama]`);
                $(this).find('.btn-add-item').attr('data-kategori-index', katIdx);
                
                // Renumber items-list and it's items matching new katIdx
                const itemsList = $(this).find('.items-list');
                itemsList.attr('data-kategori-index', katIdx);
                itemsList.find('.item-row').each(function(itemIdx) {
                    $(this).find('input[name*="[jenis]"]').attr('name', `kategori[${formIdx}][${katIdx}][items][${itemIdx}][jenis]`);
                    $(this).find('input[name*="[sub_jenis]"]').attr('name', `kategori[${formIdx}][${katIdx}][items][${itemIdx}][sub_jenis]`);
                    $(this).find('input[name*="[spesifikasi]"]').attr('name', `kategori[${formIdx}][${katIdx}][items][${itemIdx}][spesifikasi]`);
                });
            });
            updateStateButtons(container.closest('.form-card'));
        }
    });

    // ───── Tambah Item Defect ────────────────────────────────────────────────
    $(document).on('click', '.btn-add-item', function () {
        const formIdx = $(this).data('form-index');
        const katIdx = $(this).data('kategori-index');
        const container = $(this).closest('.kategori-block').find('.items-list');
        const nextItemIdx = container.find('.item-row').length;

        container.append(buildItemRow(formIdx, katIdx, nextItemIdx));
        updateStateButtons($(this).closest('.kategori-block'));
    });

    // ───── Hapus Item Defect ─────────────────────────────────────────────────
    $(document).on('click', '.btn-remove-item', function () {
        const row = $(this).closest('.item-row');
        const container = row.closest('.items-list');
        row.remove();

        // Renumber item index agar index array tetap solid
        const formIdx = container.data('form-index');
        const katIdx = container.data('kategori-index');
        container.find('.item-row').each(function(itemIdx) {
            $(this).attr('data-item-index', itemIdx);
            $(this).find('input[name*="[jenis]"]').attr('name', `kategori[${formIdx}][${katIdx}][items][${itemIdx}][jenis]`);
            $(this).find('input[name*="[sub_jenis]"]').attr('name', `kategori[${formIdx}][${katIdx}][items][${itemIdx}][sub_jenis]`);
            $(this).find('input[name*="[spesifikasi]"]').attr('name', `kategori[${formIdx}][${katIdx}][items][${itemIdx}][spesifikasi]`);
        });

        updateStateButtons(container.closest('.kategori-block'));
    });

    // Initialize disable state buttons
    updateStateButtons($('#formContainer'));
});
</script>
@endpush
@endsection