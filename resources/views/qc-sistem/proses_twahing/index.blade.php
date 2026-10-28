@extends('layouts.app')

@section('title', 'Pemeriksaan Proses Thawing')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Pemeriksaan Proses Thawing</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Proses Thawing</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Data Pemeriksaan Proses Thawing</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-danger btn-sm mr-1" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                    </button>
                                    <a href="{{ route('proses-twahing.audit') }}" class="btn btn-sm bg-purple mr-1 shadow-sm px-2-5" style="color: white; min-width: 34px; height: 31px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    @if(auth()->user()->hasPermissionTo('create-proses-twahing'))
                                    <a href="{{ route('proses-twahing.create') }}" class="btn btn-primary btn-sm mr-1">
                                        <i class="fas fa-plus"></i> Tambah Data
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

                                <div class="table-responsive text-center">
                                    <!-- Form Pencarian Server-Side -->
                                    <div class="row mb-3 mt-3">
                                        <div class="col-md-4">
                                            <form action="{{ route('proses-twahing.index') }}" method="GET" class="form-inline">
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
                                            <form action="{{ route('proses-twahing.index') }}" method="GET">
                                                <div class="input-group shadow-sm">
                                                    <input type="text" name="search" class="form-control" placeholder="Cari Nama Produk atau Kode Produksi" value="{{ request('search') }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary" type="submit">
                                                            <i class="fas fa-search mr-1"></i> Cari
                                                        </button>
                                                        @if(request('search'))
                                                            <a href="{{ route('proses-twahing.index', ['per_page' => request('per_page', 10)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                                <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                                            </form>
                                        </div>
                                    </div>
                                    
                                    @include('partials.bulk-approval-selected-toolbar', ['bulkApproveRoute' => 'proses-twahing.bulk-approve-selected'])
                                    @include('partials.date-range-filter')

                                    <table class="table table-bordered table-striped" style="white-space:nowrap;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><input type="checkbox" id="bulkApprovalSelectAll" aria-label="Pilih semua data"></th>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Jam <small class="text-muted"></small></th>
                                                <th>Shift</th>
                                                <th>Nama Produk</th>
                                                <th>Waktu Thawing <small class="text-muted"></small></th>
                                                <th>Nama RM</th>
                                                <th>Kode Produksi</th>
                                                <th>Total (Jam)</th>
                                                <th class="d-none">Plan</th>
                                                <th>Dibuat Oleh</th>
                                                <th>Status Appr</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $currentGroup = null;
                                                $userRole = auth()->user()->id_role ?? null;
                                            @endphp

                                            @foreach($items as $groupKey => $group)
                                                @php
                                                    $first = $group->first();
                                                    $itemDate = $first->tanggal ? \Carbon\Carbon::parse($first->tanggal)->format('Y-m-d') : 'no_date';
                                                    $shiftId = $first->id_shift ?? 'no_shift';
                                                    $shiftGroupKey = $itemDate . '_' . $shiftId;
                                                @endphp

                                                {{-- Header Grup Shift --}}
                                                @if($currentGroup !== $shiftGroupKey)

                                                    @php $currentGroup = $shiftGroupKey; @endphp
                                                @endif

                                                @foreach($group as $item)
                                                @php
                                                    // Ambil record terbaru dalam grup untuk tampilan jam & waktu thawing
                                                    $latest = $item->latestGroupRecord ?? $item;
                                                    $groupUuid = $item->group_uuid ?? $item->uuid;
                                                    $groupCount = $item->groupRecords ? $item->groupRecords->count() : 1;
                                                    $collapseId = 'collapseRiwayat_' . str_replace('-', '_', $item->uuid);
                                                @endphp
                                                <tr>
                                                    <td><input type="checkbox" class="bulk-approval-checkbox" data-uuid="{{ $item->uuid }}" aria-label="Pilih data"></td>
                                                    <td>{{ ($items->currentPage() - 1) * $items->perPage() + $loop->parent->index * $group->count() + $loop->iteration }}</td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{ $latest->jam ? \Carbon\Carbon::parse($latest->jam)->format('H:i') : '-' }}
                                                        @if($groupCount > 1)
                                                            <br><small class="text-muted"><i class="fas fa-history mr-1"></i>{{ $groupCount }}x edit</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->shift->shift ?? '-' }}</td>
                                                    <td style="white-space:normal;min-width:150px;">{{ $item->produk->nama_produk ?? '-' }}</td>
                                                    <td>
                                                        {{ $latest->waktu_thawing_awal ? \Carbon\Carbon::parse($latest->waktu_thawing_awal)->format('H:i') : '-' }}
                                                        -
                                                        {{ $latest->waktu_thawing_akhir ? \Carbon\Carbon::parse($latest->waktu_thawing_akhir)->format('H:i') : '-' }}
                                                    </td>
                                                    <td style="white-space:normal;min-width:150px;">
                                                        @forelse($item->details as $d)
                                                        <div>{{ $d->rm->nama_rm ?? '-' }}</div>
                                                        @empty
                                                        -
                                                        @endforelse
                                                    </td>
                                                    <td style="white-space:normal;min-width:130px;">
                                                        @forelse($item->details as $d)
                                                            <div>{{ $d->kode_produksi ?? '-' }}</div>
                                                        @empty
                                                            -
                                                            @endforelse
                                                        </td>
                                                        <td>{{ $latest->total_waktu_thawing_jam !== null ? (int) $latest->total_waktu_thawing_jam : '-' }}</td>
                                                    <td class="d-none">{{ $item->plan->nama_plan ?? '-' }}</td>
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
                                                    <td class="text-center align-middle">
                                                        <div class="d-flex flex-column align-items-center mb-1">
                                                            {{-- Tombol Edit Per 2 Jam --}}
                                                            @if(Auth::user()->hasPermissionTo('edit-proses-twahing'))
                                                                <a href="{{ route('proses-twahing.twohour.edit', $item->uuid) }}"
                                                                   class="btn btn-sm btn-info text-nowrap mb-1"
                                                                   title="Edit Per 2 Jam (Tambah Record Jam Baru)">
                                                                    <i class="fas fa-clock mr-1"></i> Edit Per 2 Jam
                                                                </a>
                                                            @endif

                                                            {{-- Tombol Rincian Jam Pengecekan --}}
                                                            @if($groupCount > 1)
                                                                <button class="btn btn-sm btn-outline-info text-nowrap mb-1" type="button"
                                                                    data-toggle="collapse"
                                                                    data-target="#{{ $collapseId }}"
                                                                    aria-expanded="false"
                                                                    title="Rincian Jam Pengecekan">
                                                                    <i class="fas fa-eye"></i> Rincian Jam
                                                                </button>
                                                            @endif
                                                        </div>

                                                        <x-action-buttons :item="$item" class="tombol" route-prefix="proses-twahing" />
                                                        <div class="mt-2 d-flex justify-content-center">
                                                            <x-audit-dropdown :item="$item" route-prefix="proses-twahing" :uuid="$item->uuid" />
                                                        </div>
                                                    </td>
                                                </tr>
                                                {{-- Collapse Rincian Jam Pengecekan --}}
                                                @if($groupCount > 1)
                                                <tr>
                                                    <td colspan="13" class="p-0 border-0">
                                                        <div class="collapse" id="{{ $collapseId }}">
                                                            <div class="card card-body shadow-none border-bottom p-3 bg-light">
                                                                <h6 class="font-weight-bold text-secondary mb-2 text-left">
                                                                    <i class="fas fa-clock mr-1"></i> Rincian Pengecekan (Per 2 Jam) — {{ $groupCount }} record
                                                                </h6>
                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered table-striped table-sm text-center bg-white mb-0">
                                                                        <thead class="thead-light">
                                                                            <tr>
                                                                                <th style="width:5%">No</th>
                                                                                <th>Jam</th>
                                                                                <th>Waktu Thawing Awal</th>
                                                                                <th>Waktu Thawing Akhir</th>

                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($item->groupRecords->sortBy('created_at')->values() as $idx => $rec)
                                                                            <tr class="{{ $idx === $item->groupRecords->count() - 1 ? 'table-success font-weight-bold' : '' }}">
                                                                                <td>{{ $idx + 1 }}</td>
                                                                                <td>
                                                                                    <span class="badge badge-light border px-2 py-1">
                                                                                        <i class="fas fa-clock mr-1 text-primary"></i>
                                                                                        {{ $rec->jam ? \Carbon\Carbon::parse($rec->jam)->format('H:i') : '-' }}
                                                                                    </span>
                                                                                    @if($idx === $item->groupRecords->count() - 1)
                                                                                        <small class="badge badge-success ml-1">Terbaru</small>
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ $rec->waktu_thawing_awal ? \Carbon\Carbon::parse($rec->waktu_thawing_awal)->format('H:i') : '-' }}</td>
                                                                                <td>{{ $rec->waktu_thawing_akhir ? \Carbon\Carbon::parse($rec->waktu_thawing_akhir)->format('H:i') : '-' }}</td>

                                                                            </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endif
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3 d-flex justify-content-center">
                                    {{ $items->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@include('partials.bulk-approval-selected-script')

@endsection

@push('scripts')
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
<script>
$(document).ready(function() {    /**
     * Handle Single Approval Button Click
     */
    $(document).on('click', '.approve-btn', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        const button = $(this);

        const typeNames = {
            'qc': 'QC',
            'produksi': 'Produksi',
            'spv': 'SPV'
        };

        if (confirm(`Apakah Anda yakin ingin menyetujui data ini sebagai ${typeNames[type]}?`)) {
            button.prop('disabled', true);
            button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: '{{ route("proses-twahing.approve", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        setTimeout(function() {
                            location.reload();
                        }, 800);
                    } else {
                        alert('Gagal menyetujui data: ' + response.message);
                        button.prop('disabled', false);
                        const originalText = {
                            'qc': '<i class="fas fa-check"></i> QC',
                            'produksi': '<i class="fas fa-check"></i> Disetujui oleh Produksi',
                            'spv': '<i class="fas fa-check"></i> Disetujui oleh SPV'
                        };
                        button.html(originalText[type]);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat menyetujui data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    location.reload();
                }
            });
        }
    });

    $('[data-bulk-export]').click(function() {
        const button = $(this);
        const form = $('#bulkExportForm');
        const kodeForm = $('#bulkKodeForm').val();

        if (!kodeForm.trim()) {
            alert('Kode Form harus diisi!');
            return;
        }

        button.prop('disabled', true);
        form.trigger('submit');

        setTimeout(function() {
            button.prop('disabled', false);
        }, 1500);
    });
});
</script>
@endpush

<!-- Modal Export PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Proses Thawing Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('proses-twahing.bulk-export-pdf') }}" method="POST">
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
                        <select class="form-control" id="filterShift" name="id_shift">
                            <option value="">Semua Shift</option>
                            @foreach($cachedShifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
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
                               value="QF 33/00"
                               required
                               placeholder="Masukkan kode form">
                        @error('kode_form')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi:</strong> Export akan berisi data operasional sesuai filter yang dipilih.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                    <button type="submit" formaction="{{ route('proses-twahing.bulk-export-pdf') }}" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Export PDF</button>
                    <button type="submit" formaction="{{ route('proses-twahing.bulk-export-excel') }}" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>
