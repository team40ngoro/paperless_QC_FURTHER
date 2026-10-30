@extends('layouts.app')

@section('title', 'Pusat Data Audit: Pemeriksaan Rheon Machine')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    @if(auth()->user()->id_role != 6)
                        <h1 class="m-0"><i class="fas fa-history text-purple"></i> Pemeriksaan Rheon Machine</h1>
                    @else
                        <h1 class="m-0">Data Pemeriksaan Rheon Machine</h1>
                    @endif
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-rheon-machine.index') }}">Rheon Machine</a></li>
                        <!-- <li class="breadcrumb-item active">Audit</li> -->
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-purple card-outline shadow-sm border-purple">
                        @if(auth()->user()->id_role != 6)
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-bold text-purple"><i class="fas fa-shield-alt mr-2"></i> Pemeriksaan Rheon Machine</h3>
                            <div class="card-tools d-flex align-items-center">
                                <div class="dropdown d-inline-block mr-2">
                                    <button class="btn btn-danger btn-sm dropdown-toggle" type="button" id="exportMenuButtonAudit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-download"></i> Export Data
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportMenuButtonAudit">
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#exportPdfModal">
                                            <i class="fas fa-file-export text-primary mr-2"></i> Export Data (PDF / Excel)
                                        </a>
                                    </div>
                                </div>
                                <span class="badge badge-purple p-2 mr-2">MODE AUDIT AKTIF</span>
                                <a href="{{ route('pemeriksaan-rheon-machine.index') }}" class="btn btn-default btn-sm shadow-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Operasional
                                </a>
                            </div>
                        </div>
                        @else
                        <div class="card-header bg-white py-3">
                            <div class="card-tools d-flex align-items-center">
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-danger btn-sm dropdown-toggle" type="button" id="exportMenuButtonAudit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-download"></i> Export Data
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportMenuButtonAudit">
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#exportPdfModal">
                                            <i class="fas fa-file-export text-primary mr-2"></i> Export Data (PDF / Excel)
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
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

                            @if(auth()->user()->id_role != 6)
                            <div class="alert alert-info border-0 shadow-sm mb-4">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Info:</strong> Data di halaman ini adalah hasil copy dari data operasional untuk keperluan audit. Perubahan di sini <strong>tidak akan merubah</strong> data operasional asli.
                            </div>
                            @endif

                            <div class="table-responsive text-center">
                                <div class="row mb-3 mt-3">
                                    <div class="col-md-4">
                                        <form action="{{ route('pemeriksaan-rheon-machine.audit') }}" method="GET" class="form-inline">
                                            <label class="mr-2">Tampilkan:</label>
                                            <select name="per_page" class="form-control form-control-sm" onchange="this.form.submit()" style="width: 70px;">
                                                <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5</option>
                                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                                <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                                                <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                                            </select>
                                            <input type="hidden" name="search" value="{{ request('search') }}">
                                        </form>
                                    </div>
                                    <div class="col-md-4 offset-md-4">
                                        <form action="{{ route('pemeriksaan-rheon-machine.audit') }}" method="GET">
                                            <div class="input-group shadow-sm">
                                                <input type="text" name="search" class="form-control" placeholder="Cari nama produk atau kode produksi" value="{{ request('search') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-purple" type="submit">
                                                        <i class="fas fa-search mr-1"></i> Cari
                                                    </button>
                                                    @if(request('search'))
                                                        <a href="{{ route('pemeriksaan-rheon-machine.audit', ['per_page' => request('per_page', 10)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                                        </form>
                                    </div>
                                </div>

                                @include('partials.bulk-approval-selected-toolbar', ['bulkApproveRoute' => 'pemeriksaan-rheon-machine.bulk-approve-selected', 'bulkApprovalRoleMap' => ['qc' => [1, 3, 5], 'produksi' => [2], 'spv' => [4]]])
                                @include('partials.date-range-filter')

                                <table class="table table-bordered table-striped" style="white-space:nowrap;">
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
                                            $currentGroup = null;
                                            $userRole = auth()->user()->id_role ?? null;
                                        @endphp

                                        @foreach($records as $groupKey => $group)
                                            @php
                                                $first = $group->first();
                                                $itemDate = $first->tanggal ? \Carbon\Carbon::parse($first->tanggal)->format('Y-m-d') : 'no_date';
                                                $shiftId = $first->shift_id ?? 'no_shift';
                                                $shiftGroupKey = $itemDate . '_' . $shiftId;
                                            @endphp

                                            {{-- Header Grup Shift --}}
                                            @if($currentGroup !== $shiftGroupKey)

                                                @php $currentGroup = $shiftGroupKey; @endphp
                                            @endif

                                            @foreach($group as $item)
                                            <tr>
                                                <td><input type="checkbox" class="bulk-approval-checkbox" data-uuid="{{ $item->uuid }}" aria-label="Pilih data"></td>
                                                <td class="text-center align-middle">{{ ($records->currentPage() - 1) * $records->perPage() + $loop->parent->index * $group->count() + $loop->iteration }}</td>
                                                <td class="align-middle">
                                                    <span class="badge badge-secondary">Shift {{ $item->shift->shift ?? '-' }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    @if(auth()->user()->id_role == 2 || auth()->user()->id_role == 3)
                                                        <span class="badge badge-secondary">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y H:i:s') : '-' }}</span>
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
                                                        <x-action-buttons :item="$item" route-prefix="pemeriksaan-rheon-machine" :show-print="false" :show-logs="false" :show-history="auth()->user()->id_role != 6" :isAuditPage="true" />
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3 d-flex justify-content-center">
                                {{ $records->appends(request()->query())->links('pagination::bootstrap-4') }}
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
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Pemeriksaan Rheon Machine Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('pemeriksaan-rheon-machine.bulk-export-pdf') }}" method="POST">
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
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterProduk">Nama Produk</label>
                        <input type="text" class="form-control" id="filterProduk" name="produk" placeholder="Semua Produk">
                    </div>
                    <div class="form-group">
                        <label for="kode_form">Kode Form <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               id="kode_form"
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
                        <strong>Informasi:</strong> PDF akan berisi data audit sesuai filter tanggal, shift, dan produk yang dipilih.
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

@include('partials.bulk-approval-selected-script')

@endsection

@push('scripts')
<style>
    .card-purple.card-outline { border-top: 3px solid #6f42c1; }
    .text-purple { color: #6f42c1 !important; }
    .badge-purple { background-color: #6f42c1; color: white; }
    .group-header { background-color: #f1f5f9 !important; border-left: 5px solid #6f42c1 !important; }
    .status-badges .badge {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 5px 8px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
</style>
@endpush
