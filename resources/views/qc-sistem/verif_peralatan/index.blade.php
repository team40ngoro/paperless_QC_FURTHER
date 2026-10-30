@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-tasks text-info"></i> Verifikasi Peralatan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Verifikasi Peralatan</li>
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
                    <div class="card card-info card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <!-- Baris toolbar utama: judul kiri, aksi kanan -->
                            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 0.75rem;">
                                <h3 class="card-title text-bold mb-0">
                                    <i class="fas fa-list text-info mr-2"></i> Data Verifikasi Peralatan
                                </h3>
                                <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-danger btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                            <i class="fas fa-file-pdf mr-1"></i> Export PDF
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#exportPdfModal">
                                                <i class="fas fa-filter text-danger mr-2"></i> Berdasarkan Filter
                                            </a>
                                        </div>
                                    </div>
                                    @if(auth()->user()->hasPermissionTo('create-verif-peralatan'))
                                        <a href="{{ route('verif-peralatan.audit') }}" class="btn btn-purple btn-sm shadow-sm" style="background-color:#6f42c1;color:#fff;" title="Riwayat Audit">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <a href="{{ route('verif-peralatan.create') }}" class="btn btn-primary btn-sm shadow-sm">
                                            <i class="fas fa-plus-circle mr-1"></i> Tambah Data
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Bar aksi bulk approve: hanya muncul saat ada baris yang dicentang -->
                            <div class="approval-buttons-group d-none">
                                @php
                                    $userRole = auth()->user()->id_role;
                                    $canApproveQc = in_array($userRole, [1, 3, 5]);
                                    $canApproveProduksi = in_array($userRole, [1, 2, 5]);
                                    $canApproveSPV = in_array($userRole, [1, 4, 5]);
                                @endphp

                                <span class="badge badge-info"><span class="selected-count">0</span> dipilih</span>

                                @if($canApproveQc)
                                    <button type="button" class="btn btn-sm btn-success shadow-sm approve-selected-btn" data-type="qc">
                                        <i class="fas fa-check mr-1"></i> Approve QC
                                    </button>
                                @endif
                                @if($canApproveProduksi)
                                    <button type="button" class="btn btn-sm btn-info shadow-sm approve-selected-btn" data-type="produksi">
                                        <i class="fas fa-check mr-1"></i> Approve Produksi
                                    </button>
                                @endif
                                @if($canApproveSPV)
                                    <button type="button" class="btn btn-sm btn-warning shadow-sm approve-selected-btn" data-type="spv" style="color: white;">
                                        <i class="fas fa-check mr-1"></i> Approve SPV
                                    </button>
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

                            <!-- Search and Filter Controls -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                        <form action="{{ route('verif-peralatan.index') }}" method="GET" class="d-inline">
                                            @if(request('search'))
                                                <input type="hidden" name="search" value="{{ request('search') }}">
                                            @endif
                                            <select name="per_page" class="form-control form-control-sm shadow-sm" style="width: 80px;" onchange="this.form.submit()">
                                                <option value="5" {{ ($perPage ?? 5) == 5 ? 'selected' : '' }}>5</option>
                                                <option value="10" {{ ($perPage ?? 5) == 10 ? 'selected' : '' }}>10</option>
                                                <option value="25" {{ ($perPage ?? 5) == 25 ? 'selected' : '' }}>25</option>
                                                <option value="50" {{ ($perPage ?? 5) == 50 ? 'selected' : '' }}>50</option>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-4 offset-md-4">
                                    <form action="{{ route('verif-peralatan.index') }}" method="GET">
                                        @if(request('per_page'))
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                        @endif
                                        <div class="input-group shadow-sm">
                                            <input type="date" name="search" class="form-control" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(request('search'))
                                                    <a href="{{ route('verif-peralatan.index', request('per_page') ? ['per_page' => request('per_page')] : []) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Data Table -->
                            @include('partials.date-range-filter')

                            <div class="table-responsive">
                                <table id="mainTable" class="table table-hover border-bottom mb-0">
                                    <thead class="bg-light text-uppercase small font-weight-bold border-top">
                                        <tr>
                                            <th class="text-center align-middle py-3" style="width: 40px;">
                                                <input type="checkbox" id="selectAllCheckbox" title="Pilih semua">
                                            </th>
                                            <th class="text-center py-3" style="width: 50px;">No</th>
                                            <th class="py-3 text-left">Pemeriksaan</th>
                                            <th class="py-3 text-center" style="width: 170px;">Hasil</th>
                                            <th class="py-3 text-center" style="width: 100px;">Status Appr</th>
                                            <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @php
                                            $rowNumber = ($items->currentPage() - 1) * $items->perPage();
                                            $prevGroup = null;

                                            // Jumlah data per grup (Tanggal + Shift) pada halaman ini
                                            $groupCounts = $items->getCollection()
                                                ->groupBy(fn ($i) => \Carbon\Carbon::parse($i->tanggal)->format('Y-m-d') . '_' . $i->id_shift)
                                                ->map->count();
                                        @endphp
                                        @forelse($items as $item)
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
                                                    <td colspan="6" class="py-2 px-3">
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
                                                <td class="text-center align-middle" style="width: 40px;">
                                                    <input type="checkbox" class="row-checkbox" data-uuid="{{ $item->uuid }}" title="Pilih baris ini">
                                                </td>
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
                                                    <x-action-buttons :item="$item" class="tombol" route-prefix="verif-peralatan" />
                                                    <div class="mt-2 d-flex justify-content-center">
                                                        <x-audit-dropdown :item="$item" route-prefix="verif-peralatan" />
                                                    </div>
                                                </td>
                                            </tr>

                                            {{-- Baris collapse: rincian temuan --}}
                                            @if($temuan->isNotEmpty())
                                                <tr class="temuan-row" id="temuan-{{ $item->uuid }}" style="display: none;">
                                                    <td colspan="6" class="p-0 border-top-0">
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
                                                <td colspan="6" class="text-center py-5">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted mb-0">Tidak ada data Verifikasi Peralatan ditemukan.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Menampilkan Navigasi Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $items->appends(request()->query())->links('pagination::bootstrap-4') }}
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
                <h5 class="modal-title" id="exportPdfModalLabel">
                    <i class="fas fa-file-pdf"></i> Export PDF Verifikasi Peralatan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('verif-peralatan.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="is_audit" value="0">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="tanggal_awal">Tanggal Awal</label>
                            <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tanggal_akhir">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Shift (Opsional)</label>
                        <select class="form-control" name="shift_id">
                            <option value="">Semua Shift</option>
                            @foreach($shifts ?? [] as $shift)
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
                               value="QF 10/00"
                               required
                               placeholder="Masukkan kode form">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
    .card-info.card-outline { border-top: 3px solid #17a2b8; }
    .text-info { color: #17a2b8 !important; }
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

    /* Checkbox (header & baris data) */
    .row-checkbox,
    #selectAllCheckbox {
        position: static;
        display: inline-block;
        vertical-align: middle;
        width: 18px;
        height: 18px;
        margin: 0;
        cursor: pointer;
        accent-color: #007bff;
    }

    .approval-buttons-group {
        margin-top: 0.75rem;
        padding: 0.75rem;
        background-color: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .approval-buttons-group.d-none { display: none !important; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    // Handle Select All Checkbox
    $('#selectAllCheckbox').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked).trigger('change');
    });

    // Handle Individual Checkbox Changes
    $(document).on('change', '.row-checkbox', function() {
        updateApprovalButtons();

        // Update Select All checkbox state
        const totalCheckboxes = $('.row-checkbox').length;
        const checkedCheckboxes = $('.row-checkbox:checked').length;
        $('#selectAllCheckbox').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
    });

    // Update Approval Buttons Visibility and Count
    function updateApprovalButtons() {
        const selectedUuids = $('.row-checkbox:checked').map((_, el) => $(el).data('uuid')).get();
        const $buttonsGroup = $('.approval-buttons-group');
        const $selectedCount = $buttonsGroup.find('.selected-count');

        if (selectedUuids.length > 0) {
            $buttonsGroup.removeClass('d-none');
            $selectedCount.text(selectedUuids.length);
        } else {
            $buttonsGroup.addClass('d-none');
            $selectedCount.text('0');
        }
    }

    // Handle Bulk Approve Button Click
    $('.approve-selected-btn').on('click', function() {
        const type = $(this).data('type');
        const selectedUuids = $('.row-checkbox:checked').map((_, el) => $(el).data('uuid')).get();

        if (selectedUuids.length === 0) {
            Swal.fire('Peringatan', 'Pilih minimal 1 data terlebih dahulu', 'warning');
            return;
        }

        const typeLabels = { qc: 'QC', produksi: 'Produksi', spv: 'SPV' };

        Swal.fire({
            title: 'Konfirmasi Approval',
            html: `Apakah Anda yakin ingin menyetujui <strong>${selectedUuids.length} data</strong> untuk ${typeLabels[type] || type}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("verif-peralatan.bulk-approve-selected") }}',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        type: type,
                        uuids: selectedUuids
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Terjadi kesalahan sistem';
                        Swal.fire('Error!', message, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush