@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-box-open text-warning"></i> Pemeriksaan Bahan Kemas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Bahan Kemas</li>
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
                    <div class="card card-warning card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 0.75rem;">
                                <h3 class="card-title text-bold mb-0"><i class="fas fa-list text-warning mr-2"></i> Data Pemeriksaan Bahan Kemas</h3>
                                <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                                    <!-- <div class="btn-group">
                                        <button type="button" class="btn btn-success btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                            <i class="fas fa-file-import mr-1"></i> Import Excel
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="{{ route('pemeriksaan-bahan-kemas.download-template') }}" class="dropdown-item">
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
                                    @if(auth()->user()->hasPermissionTo('create-pemeriksaan-bahan-kemas'))
                                        <a href="{{ route('pemeriksaan-bahan-kemas.audit') }}" class="btn btn-purple btn-sm shadow-sm" style="background-color:#6f42c1;color:#fff;">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <a href="{{ route('pemeriksaan-bahan-kemas.create') }}" class="btn btn-primary btn-sm shadow-sm">
                                            <i class="fas fa-plus-circle mr-1"></i> Tambah Data
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Approval Buttons Row: hidden by default, muncul saat ada checkbox yang dicentang -->
                            <div class="approval-buttons-group d-none">
                                @php
                                    $userRole = auth()->user()->id_role;
                                    $canApproveQc = in_array($userRole, [1, 3, 5]);
                                    $canApproveProduksi = in_array($userRole, [1, 2, 5]);
                                    $canApproveSpv = in_array($userRole, [1, 4, 5]);
                                @endphp

                                <span class="badge badge-info"><span class="selected-count">0</span> dipilih</span>

                                @if($canApproveQc)
                                    <button type="button" class="btn btn-sm btn-success shadow-sm approve-selected-btn" data-type="qc">
                                        <i class="fas fa-check mr-1"></i> Approve QC
                                    </button>
                                @endif
                                @if($canApproveProduksi)
                                    <button type="button" class="btn btn-sm btn-info shadow-sm approve-selected-btn" data-type="produksi">
                                        <i class="fas fa-check mr-1"></i> Approve Produksi
                                    </button>
                                @endif
                                @if($canApproveSpv)
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
                                        <form action="{{ route('pemeriksaan-bahan-kemas.index') }}" method="GET" class="d-inline">
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
                                    <form action="{{ route('pemeriksaan-bahan-kemas.index') }}" method="GET">
                                        @if(request('per_page'))
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                        @endif
                                        <div class="input-group shadow-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Cari data..." value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(request('search'))
                                                    <a href="{{ route('pemeriksaan-bahan-kemas.index', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            @include('partials.date-range-filter')

                            <div class="table-responsive">
                                <table id="mainTable" class="table table-hover border-bottom mb-0" style="white-space:nowrap;">
                                    <thead class="bg-light text-uppercase small font-weight-bold border-top">
                                        <tr>
                                            <th class="text-center py-3" style="width: 40px;">
                                                <input type="checkbox" id="selectAllCheckbox" title="Pilih semua">
                                            </th>
                                            <th class="text-center py-3" style="width: 50px;">No</th>
                                            <th class="py-3 text-center">Shift</th>
                                            <th class="py-3 text-center">Tanggal</th>
                                            <th class="py-3 text-center">Jam</th>
                                            <th class="py-3 text-left">Nama Kemasan</th>
                                            <th class="py-3 text-center">Kode Produksi</th>
                                            <th class="py-3 text-center">Kondisi</th>
                                            <th class="py-3 text-center">Dibuat Oleh</th>
                                            <th class="py-3 text-center" style="width: 100px;">Status Appr</th>
                                            <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @include('qc-sistem.pemeriksaan_bahan_kemas._table')
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Menampilkan Navigasi Pagination -->
                            <!-- <div class="mt-4 d-flex justify-content-between align-items-center" id="paginationWrapper">
                                <div id="paginationInfo" class="text-muted small">
                                    Menampilkan {{ $records->firstItem() ?? 0 }} sampai {{ $records->lastItem() ?? 0 }} dari {{ $records->total() }} data
                                </div>
                                <div id="paginationLinks">
                                    {{ $records->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                            </div> -->
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
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('pemeriksaan-bahan-kemas.bulk-export-pdf') }}" method="POST" target="_blank">
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
                                @php
                                    $shifts = \App\Models\DataShift::all();
                                @endphp
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
                            <input type="text" name="kode_form" class="form-control" placeholder="Contoh: QF 03/01" value="QF 03/01">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 font-weight-bold" formaction="{{ route('pemeriksaan-bahan-kemas.bulk-export-excel') }}"><i class="fas fa-file-excel mr-2"></i> Export Excel</button>
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold" formaction="{{ route('pemeriksaan-bahan-kemas.bulk-export-pdf') }}"><i class="fas fa-print mr-2"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold" id="importExcelModalLabel"><i class="fas fa-file-excel mr-2"></i> Import Excel Bahan Kemas</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('pemeriksaan-bahan-kemas.import-excel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-4">
                        <label class="font-weight-bold small text-uppercase text-muted">Download Template</label>
                        <a href="{{ route('pemeriksaan-bahan-kemas.download-template') }}" class="btn btn-block btn-outline-success">
                            <i class="fas fa-download mr-1"></i> Download Template Excel
                        </a>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih File</label>
                        <div class="custom-file">
                            <input type="file" name="file" class="custom-file-input" id="customFile" required>
                            <label class="custom-file-label" for="customFile">Pilih file .xlsx / .xls</label>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="button" class="btn btn-secondary px-4 mr-2" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success px-4 font-weight-bold"><i class="fas fa-upload mr-2"></i> Import Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .row-verified {
        background-color: rgba(40, 167, 69, 0.05) !important;
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
    }
    .item-row td {
        vertical-align: middle !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.85rem;
    }
    .row-checkbox,
    #selectAllCheckbox {
        width: 18px;
        height: 18px;
        margin: 0 !important;
        cursor: pointer;
        position: static !important;
    }
    .approval-buttons-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.75rem;
        padding: 0.6rem 0.9rem;
        background-color: #fef9c3;
        border: 1px solid #fef08a;
        border-radius: 6px;
    }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    let searchTimeout;
    let currentSearch = '{{ $search ?? "" }}';
    let currentPerPage = {{ $perPage ?? 5 }};

    // Array untuk menyimpan UUID data yang dicentang (bertahan lintas halaman & search)
    let selectedUuids = [];

    function performSearch(page = 1) {
        $('#tableBody').html(`<tr><td colspan="11" class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-warning"></i><p class="mt-2 text-muted">Sedang mencari data...</p></td></tr>`);
        
        $.ajax({
            url: "{{ route('pemeriksaan-bahan-kemas.search') }}",
            method: 'GET',
            data: {
                search: currentSearch,
                per_page: currentPerPage,
                page: page
            },
            success: function(response) {
                $('#tableBody').html(response.html);
                updatePagination(response.pagination);
                restoreCheckboxStates();
            },
            error: function() {
                $('#tableBody').html(`<tr><td colspan="11" class="text-center py-5 text-danger">Gagal memuat data. Silakan coba lagi.</td></tr>`);
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

    // ===== Checkbox Selection Logic =====

    function updateSelectedCount() {
        const count = selectedUuids.length;
        $('.selected-count').text(count);

        if (count > 0) {
            $('.approval-buttons-group').removeClass('d-none');
        } else {
            $('.approval-buttons-group').addClass('d-none');
        }
    }

    // Restore checkbox tercentang setelah tabel di-reload via AJAX
    function restoreCheckboxStates() {
        $('.row-checkbox').each(function() {
            const uuid = $(this).data('uuid');
            $(this).prop('checked', selectedUuids.includes(uuid));
        });

        const totalOnPage = $('.row-checkbox').length;
        const checkedOnPage = $('.row-checkbox:checked').length;
        $('#selectAllCheckbox').prop('checked', totalOnPage > 0 && totalOnPage === checkedOnPage);

        updateSelectedCount();
    }

    // Handle checkbox per baris
    $(document).on('change', '.row-checkbox', function() {
        const uuid = $(this).data('uuid');

        if ($(this).is(':checked')) {
            if (!selectedUuids.includes(uuid)) {
                selectedUuids.push(uuid);
            }
        } else {
            selectedUuids = selectedUuids.filter(id => id !== uuid);
        }

        const totalOnPage = $('.row-checkbox').length;
        const checkedOnPage = $('.row-checkbox:checked').length;
        $('#selectAllCheckbox').prop('checked', totalOnPage > 0 && totalOnPage === checkedOnPage);

        updateSelectedCount();
    });

    // Handle select-all checkbox (hanya untuk data di halaman saat ini)
    $(document).on('change', '#selectAllCheckbox', function() {
        const isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);

        $('.row-checkbox').each(function() {
            const uuid = $(this).data('uuid');
            if (isChecked) {
                if (!selectedUuids.includes(uuid)) {
                    selectedUuids.push(uuid);
                }
            } else {
                selectedUuids = selectedUuids.filter(id => id !== uuid);
            }
        });

        updateSelectedCount();
    });

    // ===== Approve Selected Handler =====
    $(document).on('click', '.approve-selected-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const type = btn.data('type');
        const typeLabels = { qc: 'Approve QC', produksi: 'Approve Produksi', spv: 'Approve SPV' };

        if (selectedUuids.length === 0) {
            alert('Silakan pilih minimal 1 data untuk disetujui.');
            return;
        }

        if (!confirm(`Apakah Anda yakin ingin menyetujui ${selectedUuids.length} data sebagai ${type.toUpperCase()}?`)) {
            return;
        }

        $('.approve-selected-btn').prop('disabled', true);
        btn.html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ route('pemeriksaan-bahan-kemas.bulk-approve-selected') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                type: type,
                uuids: selectedUuids
            },
            success: function(response) {
                const finish = function() {
                    selectedUuids = [];
                    $('#selectAllCheckbox').prop('checked', false);
                    updateSelectedCount();
                    performSearch();
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: response.success ? 'success' : 'warning',
                        title: response.success ? 'Berhasil!' : 'Perhatian',
                        text: response.message,
                        timer: response.success ? 1500 : undefined,
                        showConfirmButton: !response.success
                    }).then(finish);
                } else {
                    alert(response.message);
                    finish();
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error!', msg, 'error');
                } else {
                    alert('Error: ' + msg);
                }
            },
            complete: function() {
                $('.approve-selected-btn').prop('disabled', false);
                $('.approve-selected-btn').each(function() {
                    const t = $(this).data('type');
                    $(this).html('<i class="fas fa-check mr-1"></i> ' + typeLabels[t]);
                });
            }
        });
    });

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

    // Custom file input label
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Initialize tooltips for dropdown items (including dynamically loaded content)
    $(document).on('mouseenter', '[data-toggle="tooltip"]', function() {
        $(this).tooltip('show');
    });
    
    // Initialize tooltips on page load
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush
@endsection
