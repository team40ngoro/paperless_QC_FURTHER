@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-pepper-hot text-primary"></i> Data Pemeriksaan Kedatangan Bahan Baku</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Seasoning</li>
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
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 0.75rem;">
                                <h3 class="card-title text-bold mb-0"><i class="fas fa-list text-primary mr-2"></i> Data Pemeriksaan Kedatangan Bahan Baku</h3>
                                <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                                    <!-- <div class="btn-group">
                                        <button type="button" class="btn btn-success btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                            <i class="fas fa-file-import mr-1"></i> Import Excel
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="{{ route('seasoning.download-template') }}" class="dropdown-item">
                                                <i class="fas fa-download text-success mr-2"></i> Download Template
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#importExcelModal">
                                                <i class="fas fa-upload text-primary mr-2"></i> Upload File
                                            </a>
                                        </div>
                                    </div> -->
                                    <button type="button" class="btn btn-danger btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item" data-toggle="modal" data-target="#exportPdfModal">
                                            <i class="fas fa-filter text-danger mr-2"></i> Berdasarkan Filter
                                        </a>
                                    </div>
                                    @if(auth()->user()->hasPermissionTo('create-seasoning'))
                                        <a href="{{ route('seasoning.audit') }}" class="btn btn-purple btn-sm shadow-sm" style="background-color:#6f42c1;color:#fff;">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <a href="{{ route('seasoning.create') }}" class="btn btn-primary btn-sm shadow-sm">
                                            <i class="fas fa-plus-circle mr-1"></i> Tambah Data
                                        </a>
                                    @endif
                                </div>
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
                                        <form action="{{ route('seasoning.index') }}" method="GET" class="d-inline">
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
                                    <form action="{{ route('seasoning.index') }}" method="GET">
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
                                                    <a href="{{ route('seasoning.index', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
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
                                <table id="mainTable" class="table table-hover border-bottom mb-0" style="white-space: nowrap;">
                                    <thead class="bg-light text-uppercase small font-weight-bold border-top">
                                        <tr>
                                            <th class="text-center py-3" style="width: 50px;">No</th>
                                            <th class="py-3 text-center">Shift</th>
                                            <th class="py-3 text-center">Tanggal</th>
                                            <th class="py-3 text-center">Jam</th>
                                            <th class="py-3 text-center">Kode Produksi</th>
                                            <th class="py-3 text-left">Produk</th>
                                            <th class="py-3 text-center">Berat</th>
                                            <th class="py-3 text-center">Dibuat Oleh</th>
                                            <th class="py-3 text-center" style="width: 100px;">Status Appr</th>
                                            <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @include('qc-sistem.seasoning._table')
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Menampilkan Navigasi Pagination
                            <div class="mt-4 d-flex justify-content-between align-items-center" id="paginationWrapper">
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
            <form action="{{ route('seasoning.bulk-export-pdf') }}" method="POST" target="_blank">
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
                    <button type="submit" formaction="{{ route('seasoning.bulk-export-excel') }}" class="btn btn-success px-4 font-weight-bold">
                        <i class="fas fa-file-excel mr-2"></i> Export Excel
                    </button>
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold"><i class="fas fa-print mr-2"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('seasoning.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="importExcelModalLabel"><i class="fas fa-file-import mr-2"></i> Import Data Seasoning</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info alert-dismissible border-0">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Penting!</strong> Gunakan template yang telah disediakan untuk memastikan format data benar.
                        <a href="{{ route('seasoning.download-template') }}" class="alert-link" target="_blank">Download template di sini</a>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold"><i class="fas fa-upload mr-2"></i> Import Sekarang</button>
                </div>
            </form>
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
</style>

@push('scripts')
<script>
$(document).ready(function() {
    let searchTimeout;
    let currentSearch = '{{ $search ?? "" }}';
    let currentPerPage = {{ $perPage ?? 5 }};

    function performSearch(page = 1) {
        $('#tableBody').html(`<tr><td colspan="10" class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 text-muted">Sedang mencari data...</p></td></tr>`);
        
        $.ajax({
            url: "{{ route('seasoning.search') }}",
            method: 'GET',
            data: {
                search: currentSearch,
                per_page: currentPerPage,
                page: page
            },
            success: function(response) {
                $('#tableBody').html(response.html);
                updatePagination(response.pagination);
            },
            error: function() {
                $('#tableBody').html(`<tr><td colspan="10" class="text-center py-5 text-danger">Gagal memuat data. Silakan coba lagi.</td></tr>`);
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
});
</script>
@endpush
@endsection