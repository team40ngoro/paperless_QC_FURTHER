@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-history text-purple"></i> Pemeriksaan Kondisi Area Kerja</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('area-proses.index') }}">Area Kerja</a></li>
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
                            <h3 class="card-title text-bold text-purple"><i class="fas fa-shield-alt mr-2"></i> Pemeriksaan Kondisi Area Kerja</h3>

                            <div class="card-tools">
                                @if(auth()->user()->id_role !== 6)
                                    <span class="badge badge-purple p-2 mr-2">MODE AUDIT AKTIF</span>
                                    <a href="{{ route('area-proses.index') }}" class="btn btn-default btn-sm shadow-sm">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Operasional
                                    </a>
                                @endif
                                <button type="button" class="btn btn-danger btn-sm shadow-sm" data-toggle="modal" data-target="#exportPdfModal">
                                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible shadow-sm border-0">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fas fa-check-circle"></i> {{ session('success') }}
                                </div>
                            @endif

                            @if(request()->filled('group_uuid'))
                                <div class="alert alert-info border-0 shadow-sm d-flex justify-content-between align-items-center flex-wrap" style="gap: 0.5rem;">
                                    <span><i class="fas fa-history mr-2"></i> Menampilkan seluruh riwayat pemeriksaan per jam untuk grup ini.</span>
                                    <a href="{{ route('area-proses.audit') }}" class="btn btn-sm btn-default shadow-sm">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke semua data
                                    </a>
                                </div>
                            @endif

                            <!-- Search and Filter Controls -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                        <form action="{{ route('area-proses.audit') }}" method="GET" class="d-inline">
                                            @if(request('search'))
                                                <input type="hidden" name="search" value="{{ request('search') }}">
                                            @endif
                                            @if(request()->filled('group_uuid'))
                                                <input type="hidden" name="group_uuid" value="{{ request('group_uuid') }}">
                                            @endif
                                            <select id="perPageSelect" name="per_page" class="form-control form-control-sm shadow-sm" style="width: 80px;" onchange="this.form.submit()">
                                                @foreach([5, 10, 25, 50] as $n)
                                                    <option value="{{ $n }}" {{ (int)($perPage ?? 5) === $n ? 'selected' : '' }}>{{ $n }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-4 offset-md-4">
                                    <form action="{{ route('area-proses.audit') }}" method="GET">
                                        @if(request('per_page'))
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                        @endif
                                        @if(request()->filled('group_uuid'))
                                            <input type="hidden" name="group_uuid" value="{{ request('group_uuid') }}">
                                        @endif
                                        <div class="input-group shadow-sm">
                                            <select name="search" id="searchInput" class="form-control select2-area">
                                                <option value="">-- Semua Area --</option>
                                                @foreach($areas ?? [] as $area)
                                                    <option value="{{ $area->area }}" {{ ($search ?? request('search')) == $area->area ? 'selected' : '' }}>
                                                        {{ $area->area }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-purple text-white" style="background-color: #6f42c1;" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(request('search') || !empty($search))
                                                    <a href="{{ route('area-proses.audit', array_filter(['per_page' => request('per_page'), 'group_uuid' => request('group_uuid')])) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
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

                            <!-- Data Table -->
                            @include('partials.date-range-filter')

                            <div class="table-responsive">
                                <table id="mainTable" class="table table-hover border-bottom mb-0" style="white-space:nowrap;">
                                    <thead class="bg-light text-uppercase small font-weight-bold border-top">
                                        <tr>
                                            <th class="text-center py-3" style="width: 50px;">No</th>
                                            <th class="py-3 text-center">Tanggal</th>
                                            <th class="py-3 text-center">Jam</th>
                                            <th class="py-3 text-left">Area / Shift</th>
                                            <th class="py-3 text-center">Kebersihan</th>
                                            <th class="py-3 text-center">Suhu (°C)</th>
                                            <th class="py-3 text-center">Dibuat Oleh</th>
                                            <th class="py-3 text-center" style="width: 100px;">Status Appr</th>
                                            <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @php
                                            $rowNumber = ($records->currentPage() - 1) * $records->perPage();
                                            $isGroupView = request()->filled('group_uuid');
                                        @endphp
                                        @forelse($records as $item)
                                            @php
                                                $rowNumber++;
                                                $isVerified = $item->approved_by_qc && $item->approved_by_produksi && $item->approved_by_spv;
                                                $tanggalFormatted = $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-';
                                                $jamFormatted = $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-';

                                                // Riwayat per jam: record lain dalam grup yang sama (selain baris ini)
                                                $riwayat = (! $isGroupView && $item->groupRecords)
                                                    ? $item->groupRecords->where('uuid', '!=', $item->uuid)->sortBy('jam')->values()
                                                    : collect();
                                            @endphp
                                            <tr @class(['item-row', 'row-verified' => $isVerified]) data-uuid="{{ $item->uuid }}">
                                                <td class="text-center align-middle">{{ $rowNumber }}</td>
                                                <td class="text-center align-middle">{{ $tanggalFormatted }}</td>
                                                <td class="text-center align-middle">{{ $jamFormatted }}</td>
                                                <td class="align-middle">
                                                    <div class="font-weight-bold">{{ $item->area->area ?? '-' }}</div>
                                                    <div class="font-weight-bold">Shift {{ $item->shift->shift ?? '-' }}</div>
                                                    @if($riwayat->count() > 0)
                                                        <button type="button"
                                                                class="btn btn-xs btn-outline-info toggle-riwayat-btn mt-1"
                                                                data-target="riwayat-{{ $item->uuid }}"
                                                                title="Lihat riwayat pemeriksaan per jam">
                                                            <i class="fas fa-history mr-1"></i>
                                                            {{ $riwayat->count() }} Riwayat Per Jam
                                                            <i class="fas fa-chevron-down ml-1 toggle-icon"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="small">
                                                        <span class="badge {{ $item->kebersihan_ruangan === 'OK' ? 'badge-success' : 'badge-danger' }} mr-1">
                                                            <i class="fas {{ $item->kebersihan_ruangan === 'OK' ? 'fa-check' : 'fa-times' }}"></i> Ruangan
                                                        </span>
                                                        <span class="badge {{ $item->kebersihan_karyawan === 'OK' ? 'badge-success' : 'badge-danger' }}">
                                                            <i class="fas {{ $item->kebersihan_karyawan === 'OK' ? 'fa-check' : 'fa-times' }}"></i> Karyawan
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="font-weight-bold text-primary">{{ $item->pemeriksaan_suhu_ruang ?? '-' }}</span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="small text-muted">{{ $item->user->name ?? '-' }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="d-flex flex-column align-items-center" style="gap: 2px;">
                                                        <span class="badge {{ $item->approved_by_qc ? 'badge-success' : 'badge-light border' }}" style="font-size: 0.65rem; width: 50px;">
                                                            <i class="fas {{ $item->approved_by_qc ? 'fa-check' : 'fa-clock' }}"></i> QC
                                                        </span>
                                                        <span class="badge {{ $item->approved_by_produksi ? 'badge-primary' : 'badge-light border' }}" style="font-size: 0.65rem; width: 50px;">
                                                            <i class="fas {{ $item->approved_by_produksi ? 'fa-check' : 'fa-clock' }}"></i> PRD
                                                        </span>
                                                        <span class="badge {{ $item->approved_by_spv ? 'badge-dark' : 'badge-light border' }}" style="font-size: 0.65rem; width: 50px;">
                                                            <i class="fas {{ $item->approved_by_spv ? 'fa-check' : 'fa-clock' }}"></i> SPV
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle" style="white-space: nowrap;">
                                                    <x-area-proses-buttons :item="$item" :is-audit-page="true" />
                                                </td>
                                            </tr>

                                            {{-- Baris collapse: Riwayat pemeriksaan per jam --}}
                                            @if($riwayat->count() > 0)
                                                <tr class="riwayat-row" id="riwayat-{{ $item->uuid }}" style="display: none;">
                                                    <td colspan="9" class="p-0 border-top-0">
                                                        <div class="riwayat-box m-2 p-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <small class="text-muted font-weight-bold text-uppercase">
                                                                    <i class="fas fa-history mr-1"></i> Riwayat Pemeriksaan Per Jam
                                                                    &mdash; {{ $item->area->area ?? '-' }} (Shift {{ $item->shift->shift ?? '-' }})
                                                                </small>
                                                                <a href="{{ route('area-proses.audit', ['group_uuid' => $item->group_uuid ?: $item->uuid]) }}" class="btn btn-xs btn-outline-secondary">
                                                                    <i class="fas fa-external-link-alt mr-1"></i> Buka semua
                                                                </a>
                                                            </div>
                                                            <table class="table table-sm table-bordered mb-0 bg-white text-center" style="font-size: 0.8rem;">
                                                                <thead class="thead-light">
                                                                    <tr>
                                                                        <th style="width: 40px;">No</th>
                                                                        <th>Jam</th>
                                                                        <th>Kebersihan</th>
                                                                        <th>Suhu (°C)</th>
                                                                        <th>Dibuat Oleh</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($riwayat as $r)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td>
                                                                                <span class="badge badge-secondary">{{ $r->jam ? \Carbon\Carbon::parse($r->jam)->format('H:i') : '-' }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span class="badge {{ $r->kebersihan_ruangan === 'OK' ? 'badge-success' : 'badge-danger' }} mr-1">
                                                                                    <i class="fas {{ $r->kebersihan_ruangan === 'OK' ? 'fa-check' : 'fa-times' }}"></i> Ruangan
                                                                                </span>
                                                                                <span class="badge {{ $r->kebersihan_karyawan === 'OK' ? 'badge-success' : 'badge-danger' }}">
                                                                                    <i class="fas {{ $r->kebersihan_karyawan === 'OK' ? 'fa-check' : 'fa-times' }}"></i> Karyawan
                                                                                </span>
                                                                            </td>
                                                                            <td><strong class="text-primary">{{ $r->pemeriksaan_suhu_ruang ?? '-' }}</strong></td>
                                                                            <td class="text-muted">{{ $r->user->name ?? '-' }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-5">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted mb-0">Tidak ada data audit Pemeriksaan Kondisi Area Kerja ditemukan.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
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

<!-- Modal Filter Cetak PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('area-proses.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="exportPdfModalLabel"><i class="fas fa-file-pdf mr-2"></i> Cetak PDF Berdasarkan Filter Audit</h5>
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
                            <input type="date" name="tanggal_awal" class="form-control" required value="{{ date('Y-m-01') }}">
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
                    <div class="form-group">
                        <label>Shift (Opsional)</label>
                        <select class="form-control" name="shift">
                            <option value="">Semua Shift</option>
                            @foreach($shifts ?? [] as $shift)
                                <option value="{{ $shift->shift }}">{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Area (Opsional)</label>
                        <select class="form-control" name="area">
                            <option value="">Semua Area</option>
                            @foreach($areas ?? [] as $area)
                                <option value="{{ $area->area }}">{{ $area->area }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-uppercase text-muted">Kode Form</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                            </div>
                            <input type="text" name="kode_form" class="form-control" placeholder="Contoh: QF 25/00" value="QF 25/00">
                        </div>
                    </div>
                    <input type="hidden" name="is_audit" value="1">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" formaction="{{ route('area-proses.bulk-export-excel') }}" class="btn btn-success px-4 font-weight-bold">
                        <i class="fas fa-file-excel mr-2"></i> Export Excel
                    </button>
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold"><i class="fas fa-print mr-2"></i> Cetak PDF</button>
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
    .row-verified { background-color: rgba(40, 167, 69, 0.05) !important; }
    #mainTable {
        white-space: nowrap;
    }
    #mainTable thead th {
        border-bottom: 2px solid #e2e8f0;
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        padding: 0.75rem 1rem !important;
    }
    #mainTable tbody tr {
        border-bottom: 1px solid #e2e8f0;
    }
    .item-row td {
        vertical-align: middle !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.85rem;
    }
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Riwayat per jam */
    .btn-xs { padding: 0.15rem 0.4rem; font-size: 0.75rem; line-height: 1.4; border-radius: 0.2rem; }
    .riwayat-row, .riwayat-row:hover { background-color: transparent !important; }
    .riwayat-box {
        background-color: #f8fafc;
        border: 1px solid #cfe6f5;
        border-left: 3px solid #17a2b8;
        border-radius: 6px;
    }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle riwayat per jam
    $(document).on('click', '.toggle-riwayat-btn', function() {
        const $target = $('#' + $(this).data('target'));
        const $icon = $(this).find('.toggle-icon');

        $target.toggle();
        if ($target.is(':visible')) {
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        } else {
            $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        }
    });
});
</script>
@endpush
@endsection