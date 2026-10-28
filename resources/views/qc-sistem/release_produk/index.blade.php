@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-box-open text-primary"></i> Data Release Produk Finish Good</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Release Produk</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-bold"><i class="fas fa-list text-primary mr-2"></i> List Pemeriksaan</h3>
                            <div class="card-tools">
                                <div class="btn-group mr-2">
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item" data-toggle="modal" data-target="#bulkPrintModal">
                                            <i class="fas fa-filter text-danger mr-2"></i> Berdasarkan Filter
                                        </a>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                        <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                                    </button>
                                </div>
                                <a href="{{ route('release-produk.create') }}" class="btn btn-primary btn-sm shadow-sm">
                                    <i class="fas fa-plus-circle mr-1"></i> Tambah Data
                                </a>
                                <a href="{{ route('release-produk.audit') }}" class="btn btn-sm btn-purple shadow-sm ml-2">
                                    <i class="fas fa-history"></i> Data Audit
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible shadow-sm border-0">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fas fa-check-circle"></i> {{ session('success') }}
                                </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="input-group shadow-sm" style="width: 350px; border-radius: 20px; overflow: hidden;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                        </div>
                                        <input type="text" id="searchInput" class="form-control border-left-0" 
                                               placeholder="Cari Produk/Status..." value="{{ $search ?? '' }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-danger border-left-0" type="button" id="clearBtn" style="{{ ($search ?? '') ? '' : 'display: none;' }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="float-right d-flex align-items-center">
                                        <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                        <select id="perPageSelect" class="form-control form-control-sm shadow-sm" style="width: 80px;">
                                            <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                                            <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            @include('partials.date-range-filter')

                            <div class="table-responsive">
                                <table id="mainTable" class="table table-hover border-bottom mb-0" style="white-space:nowrap;">
                                    <thead class="bg-light text-uppercase small font-weight-bold border-top">
                                        <tr>
                                            <th class="text-center py-3" style="width: 50px;">No</th>
                                            <th class="py-3 text-center">Shift</th>
                                            <th class="py-3 text-center">Tanggal</th>
                                            <th class="py-3 text-center">Jam</th>
                                            <th class="py-3 text-left">Produk</th>
                                            <th class="py-3 text-center">Suhu IQF / Produk</th>
                                            <th class="py-3 text-center">Status</th>
                                            <th class="py-3 text-center" style="width: 150px;">Status Appr</th>
                                            <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @include('qc-sistem.release_produk._table')
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div id="paginationInfo" class="text-muted small"></div>
                                <div id="paginationLinks">
                                    {{ $records->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Filter Cetak PDF -->
<div class="modal fade" id="bulkPrintModal" tabindex="-1" role="dialog" aria-labelledby="bulkPrintModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('release-produk.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="bulkPrintModalLabel"><i class="fas fa-file-pdf mr-2"></i> Cetak PDF Berdasarkan Filter</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" required value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih Shift</label>
                        <select name="shift_id" class="form-control">
                            <option value="">Semua Shift</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">Shift {{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih Produk</label>
                        <select name="produk_id" class="form-control select2" style="width: 100%;">
                            <option>Pilih Produk</option>
                            @foreach($produks as $produk)
                                <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-uppercase text-muted">Kode Form</label>
                        <input type="text" name="kode_form" class="form-control" placeholder="Contoh: F-PRO-01-01" value="QF 37/00">
                    </div>
                    <input type="hidden" name="is_audit" value="0">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold"><i class="fas fa-print mr-2"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .group-header {
        background-color: #f1f5f9 !important;
        border-left: 5px solid #007bff !important;
    }
    .group-header td {
        padding: 1rem !important;
    }
    .row-verified {
        background-color: rgba(40, 167, 69, 0.05) !important;
    }
    .btn-approval {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 700 !important;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 0.7rem !important;
        border-radius: 4px;
    }
    .btn-approval:not(:disabled):hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
    .btn-purple { background-color: #6f42c1; color: white; border-color: #6f42c1; }
    .btn-purple:hover { background-color: #5a32a3; color: white; border-color: #5a32a3; }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2({
        dropdownParent: $('#bulkPrintModal')
    });

    let searchTimeout;
    let currentSearch = '{{ $search ?? "" }}';
    let currentPerPage = {{ $perPage ?? 10 }};

    function performSearch(page = 1) {
        $('#tableBody').html(`<tr><td colspan="9" class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 text-muted">Sedang mencari data...</p></td></tr>`);
        
        $.ajax({
            url: "{{ route('release-produk.search') }}",
            method: 'GET',
            data: {
                search: currentSearch,
                per_page: currentPerPage,
                page: page
            },
            success: function(response) {
                $('#tableBody').html(response.html);
                updatePagination(response.pagination);
                if(response.pagination) {
                    $('#paginationLinks').html(`
                        <nav>
                            <ul class="pagination pagination-sm m-0">
                                ${Array.from({length: response.pagination.last_page}, (_, i) => i + 1).map(p => `
                                    <li class="page-item ${p === response.pagination.current_page ? 'active' : ''}">
                                        <a class="page-link" href="?page=${p}">${p}</a>
                                    </li>
                                `).join('')}
                            </ul>
                        </nav>
                    `);
                }
            },
            error: function() {
                $('#tableBody').html(`<tr><td colspan="9" class="text-center py-5 text-danger">Gagal memuat data. Silakan coba lagi.</td></tr>`);
            }
        });
    }

    function updatePagination(pagination) {
        if (pagination && pagination.total > 0) {
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
        let pageUrl = $(this).attr('href');
        if (pageUrl) {
            let page = pageUrl.split('page=')[1];
            if (page) {
                performSearch(page);
            }
        }
    });

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
            url: "{{ route('release-produk.bulk-approve') }}",
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
