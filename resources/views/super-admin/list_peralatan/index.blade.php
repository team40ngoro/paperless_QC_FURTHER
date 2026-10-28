@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><i class="fas fa-tools text-primary mr-2"></i>Master Data List Peralatan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item active">List Peralatan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <h3 class="card-title my-1">
                                    <i class="fas fa-tools text-primary mr-2"></i>
                                    Master Data List Peralatan
                                </h3>
                                <div class="btn-group my-1">
                                    <a href="{{ route('list-peralatan.download-template') }}" class="btn btn-sm btn-info" title="Download Template Excel dengan Area Terisi">
                                        <i class="fas fa-download mr-1"></i> Download Template
                                    </a>
                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#importExcelModal" title="Import Data dari Excel">
                                        <i class="fas fa-file-excel mr-1"></i> Import Excel
                                    </button>
                                    <!-- <a href="{{ route('list-peralatan.export-excel', request()->query()) }}" class="btn btn-sm btn-secondary" title="Export Data ke Excel">
                                        <i class="fas fa-file-export mr-1"></i> Export Excel
                                    </a> -->
                                    <a href="{{ route('list-peralatan.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form method="GET" action="{{ route('list-peralatan.index') }}" class="mb-4">
                                <div class="row align-items-end">
                                    @if(auth()->user()->role === 'superadmin')
                                    <div class="col-md-3 mb-2">
                                        <label class="small text-muted font-weight-bold">Filter Plan</label>
                                        <select name="id_plan" id="filter-plan" class="form-control select2" onchange="this.form.submit()" style="width:100%;">
                                            <option value="">Semua Plan</option>
                                            @foreach($plans as $plan)
                                                <option value="{{ $plan->id }}" {{ (string) $selectedPlanId === (string) $plan->id ? 'selected' : '' }}>
                                                    {{ $plan->nama_plan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                    <div class="col-md-4 mb-2">
                                        <label class="small text-muted font-weight-bold">Filter Area</label>
                                        <select name="id_area" id="filter-area" class="form-control select2" onchange="this.form.submit()" style="width:100%;">
                                            <option value="">Semua Area</option>
                                            @foreach($areas as $area)
                                                <option value="{{ $area->id }}" {{ (string) $selectedAreaId === (string) $area->id ? 'selected' : '' }}>
                                                    {{ $area->area }} @if($area->plan) ({{ $area->plan->nama_plan }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="small text-muted font-weight-bold">Cari Nama Peralatan / Alat</label>
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control" placeholder="Ketik nama peralatan..." value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    @if($selectedPlanId || $selectedAreaId || $search)
                                        <div class="col-md-1 mb-2">
                                            <a href="{{ route('list-peralatan.index') }}" class="btn btn-outline-secondary btn-block" title="Reset Filter">
                                                <i class="fas fa-undo"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </form>

                            @forelse($groupedData as $idArea => $areaItems)
                                @php $areaObj = $areaItems->first()->area; @endphp
                                <div class="card card-outline card-primary mb-3 shadow-sm">
                                    <div class="card-header bg-light d-flex align-items-center">
                                        <h5 class="card-title font-weight-bold mb-0 text-dark">
                                            <i class="fas fa-map-marker-alt text-danger mr-2"></i>{{ $areaObj->area ?? 'Area #' . $idArea }}
                                            <!-- @if($areaItems->first()->plan)
                                                <span class="badge badge-secondary ml-2 font-weight-normal">{{ $areaItems->first()->plan->nama_plan }}</span>
                                            @endif -->
                                        </h5>
                                        <!-- <div class="card-tools ml-auto">
                                            <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 13px;">{{ $areaItems->count() }} Peralatan</span>
                                        </div> -->
                                    </div>
                                    <div class="card-body p-0 table-responsive">
                                        <table class="table text-center table-bordered table-striped mb-0" style="white-space:nowrap;">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th style="width:60px;">No</th>
                                                    <th class="text-left">Nama Peralatan / Alat</th>
                                                    <th style="width:120px;">Jumlah Alat</th>
                                                    <!-- <th style="width:150px;">Dibuat Oleh</th> -->
                                                    <th style="width:140px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($areaItems as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td class="text-left font-weight-bold text-uppercase">{{ $item->nama_alat }}</td>
                                                    <td>
                                                        <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size:13px;">
                                                            {{ $item->jumlah_alat ?? 1 }}
                                                        </span>
                                                    </td>
                                                    <!-- <td>
                                                        <span class="badge badge-light border">
                                                            {{ $item->user->name ?? '-' }}
                                                        </span>
                                                    </td> -->
                                                    <td class="text-center">
                                                        <div class="btn-vertical">
                                                            <a href="{{ route('list-peralatan.edit', $item->uuid) }}" 
                                                               class="btn btn-warning btn-sm" title="Edit Peralatan">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            @if(auth()->user()->role === 'superadmin' || $item->id_plan === auth()->user()->id_plan)
                                                                <form action="{{ route('list-peralatan.destroy', $item->uuid) }}" 
                                                                      method="POST" style="display: inline-block;" 
                                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus peralatan ini?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info text-center py-4">
                                    <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                                    Belum ada data list peralatan yang tersimpan.
                                </div>
                            @endforelse

                            <div class="d-flex justify-content-center mt-4">
                                {{ $paginatedItems->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title font-weight-bold" id="importExcelModalLabel">
                    <i class="fas fa-file-excel mr-2"></i>Import List Peralatan dari Excel
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('list-peralatan.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info py-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Gunakan tombol <strong>Download Template</strong> untuk mengunduh format file Excel. 
                        Kolom <strong>Nama Area</strong> pada template sudah terisi secara otomatis, Anda hanya perlu mengisi kolom <strong>Nama Peralatan / Alat</strong>.
                    </div>
                    <div class="form-group mb-3">
                        <label for="file_excel" class="font-weight-bold">Pilih File Excel (.xlsx / .xls)</label>
                        <input type="file" name="file" id="file_excel" class="form-control-file border p-2 rounded" accept=".xlsx, .xls" required>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success btn-sm font-weight-bold">
                        <i class="fas fa-upload mr-1"></i> Unggah & Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
