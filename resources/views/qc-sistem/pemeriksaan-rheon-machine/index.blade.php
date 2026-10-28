@extends('layouts.app')

@section('title', 'Data Pemeriksaan Rheon Machine')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Pemeriksaan Rheon Machine</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Pemeriksaan Rheon Machine</li>
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
                            <h3 class="card-title">
                                <i class="fas fa-cogs mr-2"></i>
                                Daftar Pemeriksaan Rheon Machine
                            </h3>
                            <div class="card-tools">
                                <div class="dropdown d-inline-block mr-1">
                                    <button class="btn btn-danger btn-sm dropdown-toggle" type="button" id="exportMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-file-pdf mr-1"></i> Cetak PDF Filter
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportMenuButton">
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#exportPdfModal">
                                            <i class="fas fa-file-export text-primary mr-2"></i> Export Data (PDF / Excel)
                                        </a>
                                    </div>
                                </div>
                                <a href="{{ route('pemeriksaan-rheon-machine.audit') }}" class="btn btn-sm bg-purple mr-1 shadow-sm" style="color: white">
                                    <i class="fas fa-history"></i>
                                </a>
                                @if(auth()->user()->hasPermissionTo('create-pemeriksaan-rheon-machine'))
                                    <a href="{{ route('pemeriksaan-rheon-machine.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus mr-1"></i> Tambah Data
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

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible shadow-sm border-0">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fas fa-exclamation-triangle"></i> {{ session('error') }}
                                </div>
                            @endif

                            <!-- Form Pencarian Server-Side -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <form action="{{ route('pemeriksaan-rheon-machine.index') }}" method="GET" class="form-inline">
                                        <label class="mr-2">Tampilkan:</label>
                                        <select name="per_page" class="form-control form-control-sm" onchange="this.form.submit()" style="width: 70px;">
                                            <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                                            <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                                        </select>
                                        <input type="hidden" name="search" value="{{ $search ?? '' }}">
                                    </form>
                                </div>
                                <div class="col-md-4 offset-md-4">
                                    <form action="{{ route('pemeriksaan-rheon-machine.index') }}" method="GET">
                                        <div class="input-group shadow-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Cari nama produk atau kode produksi" value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(!empty($search))
                                                    <a href="{{ route('pemeriksaan-rheon-machine.index', ['per_page' => $perPage ?? 10]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">
                                    </form>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        @if(!empty($search))
                                            Hasil pencarian: "<strong>{{ $search }}</strong>"
                                        @else
                                            Data Pemeriksaan Rheon Machine
                                        @endif
                                    </small>
                                </div>
                            </div>

                            @if($pemeriksaan->count() > 0)
                            @include('partials.date-range-filter')

                                <div class="table-responsive">
                                    @include('partials.bulk-approval-selected-toolbar', ['bulkApproveRoute' => 'pemeriksaan-rheon-machine.bulk-approve-selected', 'bulkApprovalRoleMap' => ['qc' => [1, 3, 5], 'produksi' => [2], 'spv' => [4]]])

                                    <table  class="text-center table table-bordered table-striped" style="white-space: nowrap;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><input type="checkbox" id="bulkApprovalSelectAll" aria-label="Pilih semua data"></th>
                                                <th>No</th>
                                                <th width="12%">Shift</th>
                                                <th width="12%">Tanggal</th>
                                                <th width="10%">Batch</th>
                                                <th width="8%">Waktu</th>
                                                <th width="15%">Produk</th>
                                                <th width="8%">Dough</th>
                                                <th width="8%">Filler</th>
                                                <th width="8%">Forming</th>
                                                <th width="8%">Frying</th>
                                                <th width="12%">Status Appr</th>
                                                <th width="10%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $groupedItems = $pemeriksaan->groupBy(function ($item) {
                                                    $dateStr = $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : 'no_date';
                                                    return $dateStr . '_' . ($item->shift_id ?? 'no_shift');
                                                });
                                                $globalIndex = $pemeriksaan->firstItem();
                                            @endphp
                                            @forelse($groupedItems as $groupKey => $group)
                                                @php
                                                    $firstItem = $group->first();
                                                    $date = $firstItem->tanggal ? \Carbon\Carbon::parse($firstItem->tanggal)->format('Y-m-d') : null;
                                                    $shiftId = $firstItem->shift_id;
                                                    $shiftName = $firstItem->shift->shift ?? '-';
                                                    $count = $group->count();

                                                    $allApprovedQc = $group->every(fn($item) => $item->approved_by_qc);
                                                    $allApprovedProduksi = $group->every(fn($item) => $item->approved_by_produksi);
                                                    $allApprovedSpv = $group->every(fn($item) => $item->approved_by_spv);
                                                @endphp



                                                @foreach($group as $item)
                                                    <tr>
                                                        <td><input type="checkbox" class="bulk-approval-checkbox" data-uuid="{{ $item->uuid }}" aria-label="Pilih data"></td>
                                                        <td class="text-center align-middle">{{ $globalIndex++ }}</td>
                                                        <td class="align-middle">
                                                            <span class="badge badge-secondary">Shift {{ $item->shift->shift ?? '-' }}</span>
                                                        </td>
                                                        <td class="align-middle">
                                                            @if(auth()->user()->id_role == 2 || auth()->user()->id_role == 3)
                                                                <span class="badge badge-secondary">{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</span>
                                                            @else
                                                                <span class="badge badge-secondary">{{ $item->tanggal ? $item->tanggal->format('d/m/Y H:i:s') : '-' }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle">
                                                            <span class="badge badge-info">{{ $item->batch }}</span>
                                                        </td>
                                                        <td class="align-middle">
                                                           {{ $item->pukul ? \Carbon\Carbon::parse($item->pukul)->format('H:i') : '-' }}
                                                        </td>
                                                        <td class="align-middle">
                                                            <span>{{ $item->produk->nama_produk ?? '-' }}</span>
                                                        </td>

                                                        <td class="text-center align-middle">
                                                            <span class="">
                                                                {{ number_format($item->rata_rata_dough, 2) }}gram
                                                            </span>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <span class="">
                                                                {{ number_format($item->rata_rata_filler, 2) }}gram
                                                            </span>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <span class="">
                                                                {{ number_format($item->rata_rata_after_forming, 2) }}gram
                                                            </span>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <span class="">
                                                                {{ number_format($item->rata_rata_after_frying, 2) }}gram
                                                            </span>
                                                        </td>

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
                                                        <td class="text-center align-middle">
                                                            <div class="btn-vertical">
                                                                <x-action-buttons :item="$item" route-prefix="pemeriksaan-rheon-machine" :isAuditPage="false" />
                                                                <div class="mt-2 d-flex justify-content-center">
                                                                    <x-audit-dropdown :item="$item" route-prefix="pemeriksaan-rheon-machine" :uuid="$item->uuid" />
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @empty
                                                <tr>
                                                    <td colspan="12" class="text-center py-5">
                                                        <div class="empty-state">
                                                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                                            <p class="text-muted">Tidak ada data ditemukan</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-center mt-3">
                                    {{ $pemeriksaan->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                            @else
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Belum ada data pemeriksaan</h5>
                                        <p class="text-muted">Silakan tambah data pemeriksaan rheon machine baru.</p>
                                        @if(auth()->user()->hasPermissionTo('create-pemeriksaan-rheon-machine'))
                                            <a href="{{ route('pemeriksaan-rheon-machine.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus mr-1"></i> Tambah Data Pertama
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
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
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Pemeriksaan Rheon Machine Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('pemeriksaan-rheon-machine.bulk-export-pdf') }}" method="POST">
                @csrf
                <input type="hidden" name="is_audit" value="0">
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
                            @foreach($cachedShifts as $shift)
                                    <option value="{{ $shift->shift }}">{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterProduk">Nama Produk</label>
                        <select class="form-control" id="id_produk" name="produk">
                            <option value="">Semua Produk</option>
                            @foreach($cachedProduk as $produk)
                                    <option value="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bulkKodeForm">Kode Form <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               id="bulkKodeForm"
                               name="kode_form"
                               maxlength="50"
                               value="QF 19/00"
                               required
                               placeholder="Masukkan kode form">
                        @error('kode_form')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi:</strong> PDF akan berisi data operasional sesuai filter tanggal, shift, dan produk yang dipilih.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                    <button type="submit" class="btn btn-danger" formaction="{{ route('pemeriksaan-rheon-machine.bulk-export-pdf') }}"><i class="fas fa-file-pdf"></i> Export PDF</button>
                    <button type="submit" class="btn btn-success" formaction="{{ route('pemeriksaan-rheon-machine.bulk-export-excel') }}"><i class="fas fa-file-excel"></i> Export Excel</button>
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
    .btn-approval.active-approval {
        cursor: pointer;
        background-color: #fff;
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
</style>
@push('scripts')
@endpush

@include('partials.bulk-approval-selected-script')

@endsection
