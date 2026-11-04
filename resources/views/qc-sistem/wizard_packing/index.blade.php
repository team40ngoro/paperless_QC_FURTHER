@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Riwayat Pengemasan Packing</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('wizard.selection') }}">Back</a></li>
                            <li class="breadcrumb-item active">Packing</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-list"></i> Riwayat Packing
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-sm btn-danger mr-1" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-file-pdf mr-1"></i> Cetak PDF Filter
                                    </button>
                                    <a href="{{ route('wizards.packing.audit') }}" class="btn btn-sm btn-purple mr-1" style="background-color: #6f42c1; color: white;">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    @php
                                        $userRole = (int) (auth()->user()->id_role ?? 0);
                                        $canCreatePacking = $userRole !== 3 && (in_array($userRole, [1, 2, 5], true) || auth()->user()->hasPermissionTo('create-wizard-packing'));
                                    @endphp
                                    @if($canCreatePacking)
                                    <a href="{{ route('wizards.packing.create') }}" class="btn btn-sm btn-primary shadow-sm">
                                        <i class="fas fa-plus-circle mr-1"></i> Input Proses Baru (Produksi)
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

                                <!-- Form Pencarian -->
                                <div class="row mb-3 mt-3">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                            <form action="{{ route('wizards.packing.index') }}" method="GET" class="d-inline">
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
                                        <form action="{{ route('wizards.packing.index') }}" method="GET">
                                            @if(request('per_page'))
                                                <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                            @endif
                                            <div class="input-group shadow-sm">
                                                <input type="text" name="search" class="form-control" placeholder="Cari Produk" value="{{ request('search') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="submit">
                                                        <i class="fas fa-search mr-1"></i> Cari
                                                    </button>
                                                    @if(request('search'))
                                                        <a href="{{ route('wizards.packing.index', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                @if($records->count() > 0)
                                @include('partials.bulk-approval-selected-toolbar', ['bulkApproveRoute' => 'wizards.packing.bulk-approve-selected'])
                            @include('partials.date-range-filter')

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped text-center" style="white-space:nowrap;">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="bulkApprovalSelectAll" title="Pilih semua"></th>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Jam</th>
                                                <th>Shift</th>
                                                <th>Produk</th>
                                                <th>Kode Produksi</th>
                                                <th>Verifikasi QC</th>
                                                <th>Status Appr</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($records as $item)
                                                <tr>
                                                <td><input type="checkbox" class="bulk-approval-checkbox" data-uuid="{{ $item->uuid }}" title="Pilih baris ini"></td>
                                                <td>{{ $records->firstItem() + $loop->index }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                                                <td>{{ $item->jam }}</td>
                                                <td>{{ $item->shift->shift ?? '-' }}</td>
                                                <td>{{ $item->pengemasanProduk->produk->nama_produk ?? '-' }} {{ $item->pengemasanProduk->berat ?? '' }} gram</td>
                                                <td>{{ $item->pengemasanProduk->kode_produksi ?? '-' }}</td>
                                                <td class="align-middle">
                                                    @php
                                                        $releaseStatus = $item->pengemasanProduk->status_release_produk ?? null;
                                                    @endphp
                                                    @if(is_null($releaseStatus) || $releaseStatus === '')
                                                        <span class="badge badge-warning" title="Menunggu verifikasi dan release QC"><i class="fas fa-clock mr-1"></i> Menunggu QC</span>
                                                    @elseif($releaseStatus === 'ya')
                                                        <span class="badge badge-success" title="Produk Di-release QC"><i class="fas fa-check-circle mr-1"></i> Release: Ya</span>
                                                    @else
                                                        <span class="badge badge-danger" title="Produk Tidak Di-release QC"><i class="fas fa-times-circle mr-1"></i> Release: Tidak</span>
                                                    @endif
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
                                                    <x-action-buttons :item="$item" route-prefix="wizards.packing" permission-prefix="wizard-packing" :show-view="true" :show-print="true" />
                                                    <x-audit-dropdown :item="$item" route-prefix="wizards.packing" permission-prefix="wizard-packing" />
                                                </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $records->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                                @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Belum ada data Packing. Silakan input data baru.
                                </div>
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
                    <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Wizard Packing</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('wizards.packing.bulk-export-pdf') }}" method="POST" id="bulkExportForm">
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
                            <label for="id_shift">Pilih Shift</label>
                            <select name="id_shift" id="id_shift" class="form-control">
                                <option value="">Semua Shift</option>
                                @foreach($cachedShifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kode_form">Kode Form</label>
                            <input type="text" class="form-control" id="kode_form" name="kode_form" placeholder="Contoh: QF 15/00" value="QF 15/00">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" formaction="{{ route('wizards.packing.bulk-export-pdf') }}" class="btn btn-danger"><i class="fas fa-file-pdf mr-1"></i> Export PDF</button>
                        <button type="submit" formaction="{{ route('wizards.packing.bulk-export-excel') }}" class="btn btn-success"><i class="fas fa-file-excel mr-1"></i> Export Excel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.btn-group-vertical .btn {
    margin-bottom: 2px;
}
.btn-outline-success:hover {
    background-color: #28a745;
    border-color: #28a745;
}
.btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
}
.btn-outline-dark:hover {
    background-color: #343a40;
    border-color: #343a40;
}
.btn-vertical {
    display: flex;
    flex-direction: column;
    align-items: center;
}
.btn-vertical .mb-1 {
    margin-bottom: 4px;
}
</style>

@push('scripts')
@include('partials.bulk-approval-selected-script')
<script>
$(document).ready(function() {
    /**
     * Handle Single Approval Button Click
     */
    $('.approve-btn').click(function() {
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
                url: '{{ route("wizards.packing.approve", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        button.removeClass('btn-outline-success btn-outline-primary btn-outline-dark')
                              .addClass('btn-success')
                              .html('<i class="fas fa-check-circle"></i> Approved');

                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        alert('Gagal menyetujui data: ' + response.message);
                        button.prop('disabled', false);
                        const originalText = {
                            'qc': '<i class="fas fa-check"></i> Verifikasi QC',
                            'produksi': '<i class="fas fa-check"></i> PRODUKSI',
                            'spv': '<i class="fas fa-check"></i> SPV'
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
                    button.prop('disabled', false);
                    const originalText = {
                        'qc': '<i class="fas fa-check"></i> Verifikasi QC',
                        'produksi': '<i class="fas fa-check"></i> PRODUKSI',
                        'spv': '<i class="fas fa-check"></i> SPV'
                    };
                    button.html(originalText[type]);
                }
            });
        }
    });

    /**
     * Export PDF Filter
     */
    $('[data-bulk-export]').click(function() {
        const button = $(this);
        const form = $('#bulkExportForm');

        button.prop('disabled', true);
        form.trigger('submit');

        setTimeout(function() {
            button.prop('disabled', false);
        }, 1500);
    });
});
</script>
@endpush
@endsection