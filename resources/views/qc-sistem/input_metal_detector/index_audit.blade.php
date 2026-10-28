@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-history text-purple"></i> Pemeriksaan Metal Detector</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('input-metal-detector.index') }}">Metal Detector</a></li>
                        <li class="breadcrumb-item active">Audit</li>
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
                    <div class="card card-purple card-outline shadow-sm border-purple">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-bold text-purple"><i class="fas fa-shield-alt mr-2"></i> Pemeriksaan Metal Detector</h3>
                            <div class="card-tools d-flex align-items-center">
                                <button type="button" class="btn btn-danger btn-sm mr-2" data-toggle="modal" data-target="#exportPdfModal">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                </button>
                                @if(auth()->user()->id_role !== 6)
                                <span class="badge badge-purple p-2 mr-2">MODE AUDIT AKTIF</span>
                                <a href="{{ route('input-metal-detector.index') }}" class="btn btn-default btn-sm shadow-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Operasional
                                </a>
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
                            
                            <!-- Form Pencarian dengan Pagination Dropdown -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                        <form action="{{ route('input-metal-detector.audit') }}" method="GET" class="d-inline">
                                            @if(request('search'))
                                                <input type="hidden" name="search" value="{{ request('search') }}">
                                            @endif
                                            @if(request()->filled('line'))
                                                <input type="hidden" name="line" value="{{ request('line') }}">
                                            @endif
                                            <select name="per_page" class="form-control form-control-sm shadow-sm" style="width: 80px;" onchange="this.form.submit()">
                                                <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                                                <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                                <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                                                <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                                                <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100</option>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-4 offset-md-4">
                                    <form action="{{ route('input-metal-detector.audit') }}" method="GET">
                                        @if(request('per_page'))
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                                        @endif
                                        @if(request()->filled('line'))
                                            <input type="hidden" name="line" value="{{ request('line') }}">
                                        @endif
                                        <div class="input-group shadow-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Cari data audit..." value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(request('search'))
                                                    <a href="{{ route('input-metal-detector.audit', request()->except(['search', 'page'])) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @if(auth()->user()->id_role !== 6)
                            <div class="alert alert-info border-0 shadow-sm mb-4">
                                <i class="fas fa-info-circle mr-2"></i> 
                                <strong>Info:</strong> Data di halaman ini adalah hasil copy dari data operasional untuk keperluan audit. Perubahan di sini <strong>tidak akan merubah</strong> data operasional asli.
                            </div>
                            @endif

                            @include('qc-sistem.input_metal_detector._line_filter')
                            @include('partials.date-range-filter')
                            <div class="table-responsive" id="tableBody">
                                @include('qc-sistem.input_metal_detector._table', ['isAuditPage' => true, 'metalDetector' => $records])
                            </div>
                            
                            <div class="mt-4 d-flex justify-content-between align-items-center" id="paginationWrapper">
                                <div id="paginationInfo" class="text-muted small">
                                    Menampilkan {{ $records->firstItem() ?? 0 }} sampai {{ $records->lastItem() ?? 0 }} dari {{ $records->total() }} data
                                </div>
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

<!-- Modal Export PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF / Excel Metal Detector Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('input-metal-detector.bulk-export-pdf') }}" method="POST">
                @csrf
                <input type="hidden" name="is_audit" value="1">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="tanggal_awal">Tanggal Awal</label>
                            <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal">
                            @error('tanggal_awal')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tanggal_akhir">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir">
                            @error('tanggal_akhir')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="filterShift">Shift</label>
                        <select class="form-control" id="filterShift" name="shift_id">
                            <option value="">Semua Shift</option>
                            <!-- Kita asumsikan shift standar 1, 2, 3 atau sesuai master data -->
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterLine">Line</label>
                        <select class="form-control" id="filterLine" name="line">
                            <option value="">Semua Line</option>
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}">Line {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterProduk">Nama Produk</label>
                        <select class="form-control" id="id_produk" name="id_produk">
                            <option value="">Semua Produk</option>
                            @php
                                $produks = \App\Models\JenisProduk::all();
                            @endphp
                            @foreach($produks as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kode_form">Kode Form <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               id="kode_form"
                               name="kode_form"
                               maxlength="50"
                               value="QF 18/00"
                               required
                               placeholder="Masukkan kode form">
                        @error('kode_form')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi:</strong> PDF akan berisi data audit sesuai filter yang dipilih.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                    <button type="submit" formaction="{{ route('input-metal-detector.bulk-export-excel') }}" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-download"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card-purple.card-outline {
        border-top: 3px solid #6f42c1;
    }
    .text-purple {
        color: #6f42c1 !important;
    }
    .badge-purple {
        background-color: #6f42c1;
        color: white;
    }
    .group-header {
        background-color: #f8f9fa !important;
        border-left: 5px solid #6f42c1 !important;
    }
    .group-header td {
        padding: 1rem !important;
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
// Script removed - using standard form-based search/pagination now
</script>
@endpush
@endsection
