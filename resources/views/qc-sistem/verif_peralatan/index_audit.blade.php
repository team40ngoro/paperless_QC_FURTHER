@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-history text-purple"></i> Verifikasi Peralatan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('verif-peralatan.index') }}">Verif Peralatan</a></li>
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
                            <h3 class="card-title text-bold text-purple"><i class="fas fa-shield-alt mr-2"></i> Verifikasi Peralatan</h3>

                            <div class="card-tools">
                                @if(auth()->user()->id_role !== 6)
                                    <span class="badge badge-purple p-2 mr-2">MODE AUDIT AKTIF</span>
                                    <a href="{{ route('verif-peralatan.index') }}" class="btn btn-default btn-sm shadow-sm">
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

                            <!-- Search and Filter Controls -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                        <form action="{{ route('verif-peralatan.audit') }}" method="GET" class="d-inline">
                                            @if(request('search'))
                                                <input type="hidden" name="search" value="{{ request('search') }}">
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
                                    <form action="{{ route('verif-peralatan.audit') }}" method="GET">
                                        @if(request('per_page'))
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                        @endif
                                        <div class="input-group shadow-sm">
                                            <input type="date" id="searchInput" name="search" class="form-control" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-purple text-white" style="background-color: #6f42c1;" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(request('search'))
                                                    <a href="{{ route('verif-peralatan.audit', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
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
                                <table id="mainTable" class="table table-hover border-bottom mb-0">
                                    <thead class="bg-light text-uppercase small font-weight-bold border-top">
                                        <tr>
                                            <th class="text-center py-3" style="width: 50px;">No</th>
                                            <th class="py-3 text-left">Pemeriksaan</th>
                                            <th class="py-3 text-center" style="width: 170px;">Hasil</th>
                                            <th class="py-3 text-center" style="width: 100px;">Status Appr</th>
                                            <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @php
                                            $rowNumber = ($records->currentPage() - 1) * $records->perPage();
                                            $prevGroup = null;

                                            // Jika controller mengirim data yang sudah di-group (tiap elemen = Collection),
                                            // ratakan dulu supaya tiap elemen menjadi satu record.
                                            $flatRecords = $records->getCollection()->flatten(1)->values();

                                            // Jumlah data per grup (Tanggal + Shift) pada halaman ini
                                            $groupCounts = $flatRecords
                                                ->groupBy(fn ($i) => \Carbon\Carbon::parse($i->tanggal)->format('Y-m-d') . '_' . $i->id_shift)
                                                ->map->count();
                                        @endphp
                                        @forelse($flatRecords as $item)
                                            @php
                                                $rowNumber++;
                                                $isVerified = $item->approved_by_qc && $item->approved_by_produksi && $item->approved_by_spv;
                                                $tanggalFormatted = $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y') : '-';
                                                $jamFormatted = $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-';
                                                $groupKey = ($item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : '-') . '_' . $item->id_shift;

                                                // Ringkasan area (unik) dan temuan (kondisi kotor / hasil verifikasi tidak OK)
                                                $areaList = $item->details
                                                    ->map(fn ($d) => $d->mesin->area->area ?? null)
                                                    ->filter()->unique()->values();
                                                $temuan = $item->details
                                                    ->filter(fn ($d) => ! $d->verifikasi || ! $d->verifikasi_hasil)
                                                    ->values();
                                            @endphp

                                            {{-- Header grup: Tanggal + Shift --}}
                                            @if($groupKey !== $prevGroup)
                                                <tr class="group-header-row">
                                                    <td colspan="5" class="py-2 px-3">
                                                        <span class="badge badge-primary badge-pill px-3 py-2 mr-1">
                                                            <i class="fas fa-calendar-alt mr-1"></i> {{ $tanggalFormatted }}
                                                        </span>
                                                        <span class="badge badge-primary badge-pill px-3 py-2 mr-2">
                                                            <i class="fas fa-clock mr-1"></i> Shift {{ $item->shift->shift ?? '-' }}
                                                        </span>
                                                        <span class="text-muted small">
                                                            <i class="fas fa-layer-group mr-1"></i> {{ $groupCounts[$groupKey] ?? 1 }} Data
                                                        </span>
                                                    </td>
                                                </tr>
                                                @php $prevGroup = $groupKey; @endphp
                                            @endif

                                            <tr @class(['item-row', 'row-verified' => $isVerified]) data-uuid="{{ $item->uuid }}">
                                                <td class="text-center align-middle">{{ $rowNumber }}</td>
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                                                        <span class="badge badge-secondary badge-pill px-2 py-1">
                                                            <i class="far fa-clock mr-1"></i> {{ $jamFormatted }}
                                                        </span>
                                                        <span class="font-weight-bold">{{ $item->user->name ?? '-' }}</span>
                                                    </div>
                                                    <div class="text-muted small mt-1">
                                                        {{ $areaList->isNotEmpty() ? $areaList->implode(', ') : '-' }}
                                                        <span class="text-muted">&middot; {{ $item->details->count() }} item</span>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    @if($temuan->isEmpty())
                                                        <span class="badge badge-success badge-pill px-3 py-2">
                                                            <i class="fas fa-check-circle mr-1"></i> Clean
                                                        </span>
                                                    @else
                                                        <button type="button"
                                                                class="badge badge-danger badge-pill px-3 py-2 border-0 toggle-temuan-btn"
                                                                data-target="temuan-{{ $item->uuid }}"
                                                                title="Klik untuk melihat rincian temuan">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i> Warning: {{ $temuan->count() }} Temuan
                                                            <i class="fas fa-chevron-down ml-1 toggle-icon"></i>
                                                        </button>
                                                    @endif
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
                                                    <x-action-buttons :item="$item" route-prefix="verif-peralatan" :show-history="auth()->user()->id_role != 6" />
                                                </td>
                                            </tr>

                                            {{-- Baris collapse: rincian temuan --}}
                                            @if($temuan->isNotEmpty())
                                                <tr class="temuan-row" id="temuan-{{ $item->uuid }}" style="display: none;">
                                                    <td colspan="5" class="p-0 border-top-0">
                                                        <div class="temuan-box m-2 p-3">
                                                            <small class="text-danger font-weight-bold text-uppercase d-block mb-2">
                                                                <i class="fas fa-exclamation-triangle mr-1"></i> Rincian Temuan
                                                            </small>
                                                            <table class="table table-sm table-bordered mb-0 bg-white" style="font-size: 0.8rem;">
                                                                <thead class="thead-light text-center">
                                                                    <tr>
                                                                        <th style="width: 40px;">No</th>
                                                                        <th class="text-left">Area / Mesin</th>
                                                                        <th>Kondisi</th>
                                                                        <th class="text-left">Keterangan</th>
                                                                        <th class="text-left">Tindakan Koreksi</th>
                                                                        <th>Hasil Verifikasi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($temuan as $d)
                                                                        <tr>
                                                                            <td class="text-center">{{ $loop->iteration }}</td>
                                                                            <td>{{ $d->mesin->area->area ?? '-' }} / {{ $d->mesin->nama_mesin ?? '-' }}</td>
                                                                            <td class="text-center">
                                                                                <span class="badge {{ $d->verifikasi ? 'badge-success' : 'badge-danger' }}">
                                                                                    {{ $d->verifikasi ? 'Bersih' : 'Kotor' }}
                                                                                </span>
                                                                            </td>
                                                                            <td>{{ $d->keterangan ?: '-' }}</td>
                                                                            <td>{{ $d->tindakan_koreksi ?: '-' }}</td>
                                                                            <td class="text-center">
                                                                                <span class="badge {{ $d->verifikasi_hasil ? 'badge-success' : 'badge-danger' }}">
                                                                                    {{ $d->verifikasi_hasil ? 'OK' : 'Tidak OK' }}
                                                                                </span>
                                                                            </td>
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
                                                <td colspan="5" class="text-center py-5">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted mb-0">Tidak ada data audit Verifikasi Peralatan ditemukan.</p>
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
            <form action="{{ route('verif-peralatan.bulk-export-pdf') }}" method="POST" target="_blank">
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
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih Shift</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            </div>
                            <select name="id_shift" class="form-control">
                                <option value="">Semua Shift</option>
                                @foreach($shifts ?? \App\Models\DataShift::all() as $shift)
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
                            <input type="text" name="kode_form" class="form-control" placeholder="Contoh: QF 10/00" value="QF 10/00">
                        </div>
                    </div>
                    <input type="hidden" name="is_audit" value="1">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" formaction="{{ route('verif-peralatan.bulk-export-excel') }}" class="btn btn-success px-4 font-weight-bold">
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

    /* Header grup Tanggal + Shift */
    .group-header-row,
    .group-header-row:hover { background-color: #f1f5f9 !important; }
    .group-header-row td { border-top: 1px solid #e2e8f0; }
    .group-header-row .badge { font-size: 0.75rem; }

    /* Badge Clean / Warning */
    .toggle-temuan-btn { cursor: pointer; font-size: 0.75rem; line-height: 1.2; }
    .toggle-temuan-btn:focus { outline: none; box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.3); }
    .badge-pill.badge-success,
    .badge-pill.badge-danger { font-size: 0.75rem; }

    /* Rincian temuan */
    .temuan-row, .temuan-row:hover { background-color: transparent !important; }
    .temuan-box {
        background-color: #fff5f5;
        border: 1px solid #f5c6cb;
        border-left: 3px solid #dc3545;
        border-radius: 6px;
    }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle rincian temuan
    $(document).on('click', '.toggle-temuan-btn', function() {
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