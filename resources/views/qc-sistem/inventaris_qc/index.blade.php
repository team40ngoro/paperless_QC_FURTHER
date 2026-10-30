@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-clipboard-check text-primary mr-2"></i>Checklist Inventaris Peralatan QC</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Inventaris Peralatan QC</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
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

            @if(session('import_errors'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i><strong>Detail baris gagal import (maks 20):</strong>
                    <ul class="mb-0 mt-2">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-dark">
                                <i class="fas fa-list-alt text-primary mr-2"></i> Data Checklist Inventaris QC
                            </h3>
                            <div class="card-tools">
                                <div class="btn-group mr-1" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-file-import mr-1"></i> Import Excel
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        <a class="dropdown-item" href="{{ route('inventaris-qc.download-template') }}">
                                            <i class="fas fa-download text-success mr-2"></i> Download Template
                                        </a>
                                        <a class="dropdown-item" data-toggle="modal" data-target="#importExcelModal" href="javascript:void(0);">
                                            <i class="fas fa-upload text-primary mr-2"></i> Upload File
                                        </a>
                                    </div>
                                </div>
                                <!-- <a href="{{ route('inventaris-qc.export', request()->query()) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                                </a> -->
                                <a href="{{ route('inventaris-qc.create') }}" class="btn btn-primary btn-sm ml-1">
                                    <i class="fas fa-plus mr-1"></i> Tambah Data
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Filter Section -->
                            <form method="GET" action="{{ route('inventaris-qc.index') }}" class="mb-4">
                                <div class="form-row align-items-end">
                                    @if(auth()->user()->role === 'superadmin')
                                        <div class="col-md-2 mb-2">
                                            <label class="small text-muted font-weight-bold">Plan</label>
                                            <select name="id_plan" class="form-control form-control-sm">
                                                <option value="">Semua Plan</option>
                                                @foreach($plans as $plan)
                                                    <option value="{{ $plan->id }}" {{ $selectedPlanId == $plan->id ? 'selected' : '' }}>
                                                        {{ $plan->nama_plan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="col-md-2 mb-2">
                                        <label class="small text-muted font-weight-bold">Tanggal</label>
                                        <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ $tanggal ?? '' }}">
                                    </div>

                                    <div class="col-md-2 mb-2">
                                        <label class="small text-muted font-weight-bold">Shift</label>
                                        <select name="id_shift" class="form-control form-control-sm">
                                            <option value="">Semua Shift</option>
                                            @foreach($shifts as $shift)
                                                <option value="{{ $shift->id }}" {{ $selectedShiftId == $shift->id ? 'selected' : '' }}>
                                                    Shift {{ $shift->shift }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2 mb-2">
                                        <label class="small text-muted font-weight-bold">Waktu Pemeriksaan</label>
                                        <select name="waktu_pemeriksaan" class="form-control form-control-sm">
                                            <option value="">Semua Waktu</option>
                                            <option value="AWAL SHIFT" {{ $waktuPemeriksaan === 'AWAL SHIFT' ? 'selected' : '' }}>AWAL SHIFT</option>
                                            <option value="AKHIR SHIFT" {{ $waktuPemeriksaan === 'AKHIR SHIFT' ? 'selected' : '' }}>AKHIR SHIFT</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-2">
                                        <label class="small text-muted font-weight-bold">Cari Peralatan / Ket</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Ketik kata kunci..." value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    @if($selectedPlanId || $selectedShiftId || $waktuPemeriksaan || $tanggal || $search)
                                        <div class="col-md-1 mb-2">
                                            <a href="{{ route('inventaris-qc.index') }}" class="btn btn-outline-secondary btn-sm btn-block" title="Reset Filter">
                                                <i class="fas fa-undo"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </form>

                            @include('partials.date-range-filter')

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover text-center mb-0" style="white-space: nowrap;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th>Tanggal</th>
                                            <th>Shift</th>
                                            <th>Waktu Pemeriksaan</th>
                                            <th class="text-left">Area Diperiksa</th>
                                            <th>Total Peralatan</th>
                                            <th>Dibuat Oleh</th>
                                            <th style="width: 130px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($items as $index => $item)
                                            @php
                                                $areaList = $item->details->pluck('area.area')->filter()->unique()->implode(', ');
                                            @endphp
                                            <tr>
                                                <td>{{ $items->firstItem() + $index }}</td>
                                                <td>
                                                    <span class="badge badge-light border">
                                                        <!-- <i class="far fa-calendar-alt mr-1 text-primary"></i> -->
                                                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-primary">
                                                        Shift {{ $item->shift->shift ?? '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $item->waktu_pemeriksaan === 'AWAL SHIFT' ? 'badge-primary' : 'badge-warning text-dark' }} font-weight-bold">
                                                        {{ $item->waktu_pemeriksaan }}
                                                    </span>
                                                </td>
                                                <td class="text-left">
                                                    <strong>{{ $areaList ?: '-' }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge badge-secondary px-2 py-1" style="font-size: 12px;">
                                                        {{ $item->details->count() }} Item
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-dark">{{ $item->user->name ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    <!-- <div class="btn-group" role="group">
                                                        <a href="{{ route('inventaris-qc.show', $item->uuid) }}" class="btn btn-info btn-sm" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('inventaris-qc.edit', $item->uuid) }}" class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @if(auth()->user()->role === 'superadmin' || $item->id_plan === auth()->user()->id_plan)
                                                            <form action="{{ route('inventaris-qc.destroy', $item->uuid) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data checklist ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div> -->
                                                    <x-action-buttons :item="$item" class="tombol" route-prefix="inventaris-qc" :show-view="true" :show-print="false" :show-history="true" />

                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">
                                                    <i class="fas fa-info-circle fa-2x mb-2 d-block text-secondary"></i>
                                                    Belum ada data checklist inventaris QC tersimpan.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer d-flex justify-content-center bg-white">
                            {{ $items->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection


{{-- Import Excel Modal --}}
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importExcelModalLabel">
                    <i class="fas fa-file-excel"></i> Import Data Inventaris QC
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('inventaris-qc.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group text-center mb-3">
                        <a href="{{ route('inventaris-qc.download-template') }}" class="btn btn-outline-success">
                            <i class="fas fa-download"></i> Download Template Excel
                        </a>
                    </div>
                    <div class="form-group">
                        <label>Pilih File Excel</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls" required>
                        <small class="form-text text-muted">
                            Format: Shift | Tanggal | Waktu Pemeriksaan | Catatan Umum
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-import"></i> Import Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    /* Memastikan tombol aksi berbentuk kotak dan tidak gepeng */
    .tombol {
        width: 32px !important;
        height: 32px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 4px !important;
    }
    .tombol i {
        font-size: 0.85rem !important;
    }
</style>
