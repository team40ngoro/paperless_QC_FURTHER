@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-snowflake text-info"></i> Pemeriksaan Kedatangan Bahan Baku</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Chillroom</li>
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
                    <div class="card card-info card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <!-- Baris toolbar utama: judul kiri, aksi kanan -->
                            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 0.75rem;">
                                <h3 class="card-title text-bold mb-0">
                                    <i class="fas fa-list text-info mr-2"></i> Data Pemeriksaan Kedatangan Bahan Baku
                                </h3>
                                <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                                    <!-- <div class="btn-group">
                                        <button type="button" class="btn btn-success btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                            <i class="fas fa-file-import mr-1"></i> Import Excel
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="{{ route('chillroom.download-template') }}" class="dropdown-item">
                                                <i class="fas fa-download text-success mr-2"></i> Download Template
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#importExcelModal">
                                                <i class="fas fa-upload text-primary mr-2"></i> Upload File
                                            </a>
                                        </div>
                                    </div> -->
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-danger btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                            <i class="fas fa-file-pdf mr-1"></i> Export PDF
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#exportPdfModal">
                                                <i class="fas fa-filter text-danger mr-2"></i> Berdasarkan Filter
                                            </a>
                                        </div>
                                    </div>
                                    @if(auth()->user()->hasPermissionTo('create-chillroom'))
                                        <a href="{{ route('chillroom.audit') }}" class="btn btn-purple btn-sm shadow-sm" style="background-color:#6f42c1;color:#fff;" title="Riwayat Audit">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <a href="{{ route('chillroom.create') }}" class="btn btn-primary btn-sm shadow-sm">
                                            <i class="fas fa-plus-circle mr-1"></i> Tambah Data
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Bar aksi bulk approve: hanya muncul saat ada baris yang dicentang -->
                            <div class="approval-buttons-group d-none">
                                @php
                                    $userRole = auth()->user()->id_role;
                                    $canApproveQc = in_array($userRole, [1, 3, 5]);
                                    $canApproveSPV = in_array($userRole, [1, 4, 5]);
                                @endphp

                                <span class="badge badge-info"><span class="selected-count">0</span> dipilih</span>

                                @if($canApproveQc)
                                    <button type="button" class="btn btn-sm btn-success shadow-sm approve-selected-btn" data-type="qc">
                                        <i class="fas fa-check mr-1"></i> Approve QC
                                    </button>
                                @endif
                                @if($canApproveSPV)
                                    <button type="button" class="btn btn-sm btn-warning shadow-sm approve-selected-btn" data-type="spv" style="color: white;">
                                        <i class="fas fa-check mr-1"></i> Approve SPV
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible shadow-sm border-0">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fas fa-check-circle"></i> {{ session('success') }}
                                </div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible shadow-sm border-0">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fas fa-exclamation-circle"></i> {{ session('error') }}
                                </div>
                            @endif
                            
                            <!-- Search and Filter Controls -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                        <form action="{{ route('chillroom.index') }}" method="GET" class="d-inline">
                                            @if(request('search'))
                                                <input type="hidden" name="search" value="{{ request('search') }}">
                                            @endif
                                            <select name="per_page" class="form-control form-control-sm shadow-sm" style="width: 80px;" onchange="this.form.submit()">
                                                <option value="5" {{ ($perPage ?? 5) == 5 ? 'selected' : '' }}>5</option>
                                                <option value="10" {{ ($perPage ?? 5) == 10 ? 'selected' : '' }}>10</option>
                                                <option value="25" {{ ($perPage ?? 5) == 25 ? 'selected' : '' }}>25</option>
                                                <option value="50" {{ ($perPage ?? 5) == 50 ? 'selected' : '' }}>50</option>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-4 offset-md-4">
                                    <form action="{{ route('chillroom.index') }}" method="GET">
                                        @if(request('per_page'))
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                        @endif
                                        <div class="input-group shadow-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Cari Nama RM / Kode Produksi" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(request('search'))
                                                    <a href="{{ route('chillroom.index', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            @include('partials.date-range-filter')

                            @php
                                $showApprovalCheckbox = in_array((int) auth()->user()->id_role, [1, 3, 4, 5], true);
                            @endphp

                            <div class="table-responsive">
                                <table id="mainTable" class="table table-hover border-bottom mb-0" style="white-space: nowrap;">
                                    <thead class="bg-light text-uppercase small font-weight-bold border-top">
                                        <tr>
                                            @if($showApprovalCheckbox)
                                                <th class="text-center py-3" style="width: 40px;">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <input type="checkbox" id="selectAllCheckbox" class="form-check-input" title="Pilih semua">
                                                    </div>
                                                </th>
                                            @endif
                                            <th class="text-center py-3" style="width: 50px;">No</th>
                                            <th class="py-3 text-center">Shift</th>
                                            <th class="py-3 text-center">Tanggal</th>
                                            <th class="py-3 text-center">Jam</th>
                                            <th class="py-3 text-left">Nama RM</th>
                                            <th class="py-3 text-center">Kode Produksi</th>
                                            <th class="py-3 text-center">Berat (Kg)</th>
                                            <th class="py-3 text-center">Suhu (°C)</th>
                                            <th class="py-3 text-center">Dibuat Oleh</th>
                                            <th class="py-3 text-center" style="width: 100px;">Status Appr</th>
                                            <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @include('qc-sistem.chillroom._table')
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Menampilkan Navigasi Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $records->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Filter Cetak PDF -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('chillroom.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="importExcelModalLabel"><i class="fas fa-file-import mr-2"></i> Import Data Chillroom</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info alert-dismissible border-0">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Penting!</strong> Gunakan template yang telah disediakan untuk memastikan format data benar.
                        <a href="{{ route('chillroom.download-template') }}" class="alert-link" target="_blank">Download template di sini</a>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted mb-2">Pilih File Excel</label>
                        <div class="custom-file shadow-sm">
                            <input type="file" class="custom-file-input" id="excelFile" name="file" accept=".xlsx,.xls" required>
                            <label class="custom-file-label" for="excelFile">Pilih file...</label>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-file-excel text-success mr-1"></i>
                            Format: .xlsx atau .xls | Ukuran maksimal: 10MB
                        </small>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-uppercase text-muted mb-2">Preview:</label>
                        <div id="fileInfo" class="alert alert-light border">
                            <small class="text-muted">Belum ada file dipilih</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 font-weight-bold">
                        <i class="fas fa-upload mr-2"></i> Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Filter Cetak PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('chillroom.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="exportPdfModalLabel"><i class="fas fa-file-pdf mr-2"></i> Cetak PDF Berdasarkan Filter</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Tanggal Mulai</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                            <input type="date" name="tanggal_mulai" class="form-control" required value="{{ date('Y-m-01') }}">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Tanggal Akhir</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                            <input type="date" name="tanggal_akhir" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih Shift</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            </div>
                            <select name="shift_id" class="form-control">
                                <option value="">Semua Shift</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}">Shift {{ $shift->shift }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-uppercase text-muted">Kode Form</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                            </div>
                            <input type="text" name="kode_form" class="form-control" placeholder="Contoh: QF 01/00" value="QF 01/00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" formaction="{{ route('chillroom.bulk-export-excel') }}" class="btn btn-success px-4 font-weight-bold">
                        <i class="fas fa-file-excel mr-2"></i> Export Excel
                    </button>
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold"><i class="fas fa-print mr-2"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .group-header {
        background-color: #f1f5f9 !important;
        border-left: 5px solid #17a2b8 !important;
    }
    .group-header td {
        padding: 1rem !important;
    }
    .row-verified {
        background-color: rgba(40, 167, 69, 0.05) !important;
    }
    .bulk-approve-btn {
        transition: all 0.2s ease;
    }
    .bulk-approve-btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .status-badges .badge {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-bottom: 3px;
        padding: 5px 8px;
        border-radius: 4px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    #mainTable thead th {
        border-bottom: 2px solid #e2e8f0;
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        /* Bootstrap default-nya "vertical-align: bottom" untuk thead th,
           makanya checkbox "Pilih semua" terlihat nyangkut di garis bawah header.
           Ditimpa jadi middle supaya sejajar dengan isi kolom lain. */
        vertical-align: middle !important;
    }
    .item-row td {
        vertical-align: middle !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.85rem;
    }
    /* Ukuran checkbox disamakan (header & baris) supaya tidak ada selisih
       render antar-browser yang bikin posisinya kelihatan tidak rata */
    .row-checkbox,
    #selectAllCheckbox {
        width: 18px;
        height: 18px;
        margin: 0;
        cursor: pointer;
    }
    /* Bar bulk-approve: baris terpisah di bawah toolbar utama, rapi & jelas
       secara visual bahwa ini adalah aksi kontekstual untuk baris terpilih */
    .approval-buttons-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.75rem;
        padding: 0.6rem 0.9rem;
        background-color: #eef6fc;
        border: 1px solid #cfe6f5;
        border-radius: 6px;
    }
</style>

@push('scripts')
<script>
// Handle file selection preview
$('#excelFile').on('change', function() {
    const file = this.files[0];
    const label = $(this).next('.custom-file-label');
    const info = $('#fileInfo');
    
    if (file) {
        label.text(file.name);
        const sizeMB = (file.size / 1024 / 1024).toFixed(2);
        info.html(`
            <div>
                <i class="fas fa-check-circle text-success mr-2"></i>
                <strong>${file.name}</strong><br>
                <small class="text-muted">Ukuran: ${sizeMB}MB | Tipe: ${file.type}</small>
            </div>
        `);
    } else {
        label.text('Pilih file...');
        info.html('<small class="text-muted">Belum ada file dipilih</small>');
    }
});

// Show import success/error messages
$(document).ready(function() {
    @if(session('import_errors'))
        const errors = @json(session('import_errors'));
        if (errors && errors.length > 0) {
            let errorHtml = '<div class="alert alert-warning alert-dismissible border-0 mb-3">' +
                            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                            '<strong><i class="fas fa-exclamation-triangle mr-2"></i>Perhatian:</strong> Ada beberapa baris yang gagal diimport:<ul class="mb-0 mt-2">';
            
            errors.forEach(err => {
                errorHtml += '<li><small>' + err + '</small></li>';
            });
            
            errorHtml += '</ul></div>';
            
            $('.card-body').prepend(errorHtml);
        }
    @endif
});
</script>
<script>
$(document).ready(function() {
    let searchTimeout;
    let currentSearch = '{{ $search ?? "" }}';
    let currentPerPage = {{ $perPage ?? 5 }};
    let selectedUuids = [];

    function performSearch(page = 1) {
        $('#tableBody').html(`<tr><td colspan="12" class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-info"></i><p class="mt-2 text-muted">Sedang mencari data...</p></td></tr>`);
        
        $.ajax({
            url: "{{ route('chillroom.search') }}",
            method: 'GET',
            data: {
                search: currentSearch,
                per_page: currentPerPage,
                page: page
            },
            success: function(response) {
                $('#tableBody').html(response.html);
                updatePagination(response.pagination);
                updateCheckboxStates();
            },
            error: function() {
                $('#tableBody').html(`<tr><td colspan="12" class="text-center py-5 text-danger">Gagal memuat data. Silakan coba lagi.</td></tr>`);
            }
        });
    }

    function updatePagination(pagination) {
        if (pagination.total > 0) {
            $('#paginationInfo').html(`Menampilkan ${pagination.from} sampai ${pagination.to} dari ${pagination.total} data`);
        } else {
            $('#paginationInfo').html('Menampilkan 0 dari 0 data');
        }
    }

    function updateCheckboxStates() {
        // Restore checkbox states after AJAX reload
        selectedUuids.forEach(uuid => {
            $(`input[data-uuid="${uuid}"]`).prop('checked', true);
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = selectedUuids.length;
        $('.selected-count').text(count);

        if (count > 0) {
            $('.approval-buttons-group').removeClass('d-none');
        } else {
            $('.approval-buttons-group').addClass('d-none');
        }
    }

    // Handle individual checkbox click
    $(document).on('change', '.row-checkbox', function() {
        const uuid = $(this).data('uuid');
        
        if ($(this).is(':checked')) {
            if (!selectedUuids.includes(uuid)) {
                selectedUuids.push(uuid);
            }
        } else {
            selectedUuids = selectedUuids.filter(id => id !== uuid);
        }
        
        updateSelectedCount();
    });

    // Handle select all checkbox
    $('#selectAllCheckbox').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);
        
        if (isChecked) {
            $('.row-checkbox').each(function() {
                const uuid = $(this).data('uuid');
                if (!selectedUuids.includes(uuid)) {
                    selectedUuids.push(uuid);
                }
            });
        } else {
            selectedUuids = [];
        }
        
        updateSelectedCount();
    });

    // Handle search
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        currentSearch = $(this).val();
        if (currentSearch) $('#clearBtn').show(); else $('#clearBtn').hide();
        searchTimeout = setTimeout(() => performSearch(1), 500);
    });

    $('#clearBtn').on('click', function() {
        $('#searchInput').val('');
        $(this).hide();
        currentSearch = '';
        performSearch(1);
    });

    $('#perPageSelect').on('change', function() {
        currentPerPage = $(this).val();
        performSearch(1);
    });

    $(document).on('click', '#paginationLinks a', function(e) {
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        performSearch(page);
    });

    // Bulk Approval Handler for Selected Rows
    $(document).on('click', '.approve-selected-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const type = btn.data('type');

        if (selectedUuids.length === 0) {
            alert('Silakan pilih minimal 1 data untuk disetujui.');
            return;
        }

        if (!confirm('Apakah Anda yakin ingin menyetujui ' + selectedUuids.length + ' data sebagai ' + type.toUpperCase() + '?')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: "{{ route('chillroom.bulk-approve-selected') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                type: type,
                uuids: selectedUuids
            },
            success: function(response) {
                if (response.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            selectedUuids = [];
                            performSearch();
                        });
                    } else {
                        alert(response.message);
                        selectedUuids = [];
                        performSearch();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Gagal!', response.message, 'error');
                    } else {
                        alert('Gagal: ' + response.message);
                    }
                    btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Approve ' + type.charAt(0).toUpperCase() + type.slice(1));
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error!', msg, 'error');
                } else {
                    alert('Error: ' + msg);
                }
                btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Approve ' + type.charAt(0).toUpperCase() + type.slice(1));
            }
        });
    });

    // Old bulk approve handler (still supported for backward compatibility)
    $(document).off('click', '.bulk-approve-btn').on('click', '.bulk-approve-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const type = btn.data('type');
        const tanggal = btn.data('tanggal');
        const shift_id = btn.data('shift-id');

        if (!confirm('Apakah Anda yakin ingin menyetujui seluruh data pada shift ini sebagai ' + type.toUpperCase() + '?')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: "{{ route('chillroom.bulk-approve') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                type: type,
                tanggal: tanggal,
                shift_id: shift_id
            },
            success: function(response) {
                if (response.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => performSearch());
                    } else {
                        alert(response.message);
                        performSearch();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Gagal!', response.message, 'error');
                    } else {
                        alert('Gagal: ' + response.message);
                    }
                    performSearch();
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error!', msg, 'error');
                } else {
                    alert('Error: ' + msg);
                }
                performSearch();
            }
        });
    });
});
</script>
@endpush
@endsection