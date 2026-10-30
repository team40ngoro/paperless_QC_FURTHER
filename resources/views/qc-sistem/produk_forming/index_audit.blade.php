@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    @if(auth()->user()->id_role != 6)
                        <h1 class="m-0">Verifikasi Produk Forming</h1>
                    @else
                        <h1 class="m-0">Data Produk Forming</h1>
                    @endif
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('produk-forming.index') }}">Verifikasi Produk Forming</a></li>
                        <li class="breadcrumb-item active">Audit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            @if(auth()->user()->id_role != 6)
                                <h3 class="card-title">Data Audit Produk Forming</h3>
                            @else
                                <h3 class="card-title">Data Produk Forming</h3>
                            @endif
                            <div class="card-tools d-flex align-items-center">
                                <button type="button" class="btn btn-danger btn-sm mr-1" data-toggle="modal" data-target="#exportPdfModal">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                </button>
                                @if(auth()->user()->id_role != 6)
                                    <span class="badge badge-purple p-2 mr-1">MODE AUDIT AKTIF</span>
                                    <a href="{{ route('produk-forming.index') }}" class="btn btn-default btn-sm shadow-sm">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Operasional
                                    </a>
                                @endif
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if(auth()->user()->id_role != 6)
                                <div class="alert alert-info border-0 shadow-sm mb-4">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Info:</strong> Data di halaman ini adalah hasil copy dari data operasional untuk keperluan audit. Perubahan di sini <strong>tidak akan merubah</strong> data operasional asli.
                                </div>
                            @endif

                            @include('partials.date-range-filter')

                            <div class="table-responsive">
                                <!-- Form Pencarian & Per Page -->
                                <div class="row mb-3 mt-3">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                            <form action="{{ route('produk-forming.audit') }}" method="GET" class="d-inline">
                                                @if(request('search'))
                                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                                @endif
                                                <select name="per_page" class="form-control form-control-sm shadow-sm" style="width: 80px;" onchange="this.form.submit()">
                                                    @foreach([5, 10, 25, 50, 100] as $pageSize)
                                                        <option value="{{ $pageSize }}" {{ (request('per_page') ?? $perPage ?? 10) == $pageSize ? 'selected' : '' }}>{{ $pageSize }}</option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-4 offset-md-4">
                                        <form action="{{ route('produk-forming.audit') }}" method="GET">
                                            @if(request('per_page'))
                                                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                                            @endif
                                            <div class="input-group shadow-sm">
                                                <input type="text" name="search" class="form-control" placeholder="Cari Nama Produk" value="{{ request('search') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="submit">
                                                        <i class="fas fa-search mr-1"></i> Cari
                                                    </button>
                                                    @if(request('search'))
                                                        <a href="{{ route('produk-forming.audit', ['per_page' => request('per_page', 10)]) }}" class="btn btn-outline-danger" title="Clear Search">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <table id="mainTable" class="table table-hover border-bottom mb-0" style="white-space: nowrap;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Jam</th>
                                            <th>Produk</th>
                                            <th>Shift</th>
                                            <th>Dibuat Oleh</th>
                                            <th>Status Appr</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $rowNumber = ($records->currentPage() - 1) * $records->perPage();
                                        @endphp

                                        @forelse($records as $item)
                                            @php
                                                $rowNumber++;
                                            @endphp
                                            <tr>
                                                <td>{{ $rowNumber }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                                <td>
                                                    <span class="badge badge-secondary">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                                </td>
                                                <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                                <td>{{ $item->shift->shift ?? '-' }}</td>
                                                <td>{{ $item->user->name ?? '-' }}</td>
                                                <td class="align-middle">
                                                    <div class="status-badges d-flex flex-column align-items-center">
                                                        <span class="badge {{ $item->approved_by_qc ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                                                            <i class="fas {{ $item->approved_by_qc ? 'fa-check' : 'fa-clock' }} mr-1"></i> QC
                                                        </span>
                                                        <span class="badge {{ $item->approved_by_produksi ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                                                            <i class="fas {{ $item->approved_by_produksi ? 'fa-check' : 'fa-clock' }} mr-1"></i> PROD
                                                        </span>
                                                        <span class="badge {{ $item->approved_by_spv ? 'badge-success' : 'badge-light border' }} w-100" style="font-size: 0.7rem;">
                                                            <i class="fas {{ $item->approved_by_spv ? 'fa-check' : 'fa-clock' }} mr-1"></i> SPV
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <x-action-buttons :item="$item" route-prefix="produk-forming" :show-history="auth()->user()->id_role != 6" />
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5 text-muted">Tidak ada data ditemukan</td>
                                            </tr>
                                        @endempty
                                    </tbody>
                                </table>
                            </div>

                            <!-- Menampilkan Navigasi Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $records->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<!-- Modal Export PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Produk Forming Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('produk-forming.bulk-export-pdf') }}" method="POST">
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
                        <select class="form-control" id="filterShift" name="shift">
                            <option value="">Semua Shift</option>
                            @if(isset($cachedShifts))
                                @foreach($cachedShifts as $shift)
                                    <option value="{{ $shift->shift }}">{{ $shift->shift }}</option>
                                @endforeach
                            @else
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_produk">Nama Produk</label>
                        @if(isset($cachedProduk))
                            <select class="form-control" id="id_produk" name="produk">
                                <option value="">Semua Produk</option>
                                @foreach($cachedProduk->where('status_bahan', 'forming') as $produk)
                                    <option value="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" class="form-control" id="id_produk" name="produk" placeholder="Semua Produk">
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="kode_form">Kode Form <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               id="kode_form"
                               name="kode_form"
                               maxlength="50"
                               value="QF 20/00"
                               required
                               placeholder="Masukkan kode form">
                        @error('kode_form')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi:</strong> PDF akan berisi data audit sesuai filter tanggal, shift, dan produk yang dipilih.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                    <button type="submit" formaction="{{ route('produk-forming.bulk-export-excel') }}" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-download"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<style>
    .badge-purple {
        background-color: #6f42c1;
        color: white;
    }
    #mainTable thead th {
        border-bottom: 2px solid #e2e8f0;
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        vertical-align: middle !important;
    }
</style>
@endpush

@endsection