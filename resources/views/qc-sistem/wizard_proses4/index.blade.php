@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Verifikasi Proses Tumbling</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('wizard.proses4.index') }}">Wizard</a></li>
                        <li class="breadcrumb-item active">Proses Tumbling</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title text-bold"><i class="fas fa-list mr-1"></i> Verifikasi Proses Tumbling</h3>
                    <div class="card-tools">
                        <!-- <a href="{{ route('wizard.selection') }}" class="btn btn-sm btn-secondary mr-2">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a> -->
                        <div class="btn-group mr-1">
                            <button type="button" class="btn btn-danger btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                <i class="fas fa-file-pdf mr-1"></i> Export PDF
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="#" class="dropdown-item" data-toggle="modal" data-target="#exportPdfModal">
                                    <i class="fas fa-filter text-danger mr-2"></i> Berdasarkan Filter
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('wizard.proses4.audit') }}" class="btn btn-sm btn-purple mr-1">
                            <i class="fas fa-history"></i>
                        </a>
                        @if(auth()->user()->hasPermissionTo('create-wizard-proses4'))
                        <a href="{{ route('wizard.proses4.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus-circle mr-1"></i> Input Proses Baru
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

                    <!-- Search Form -->
                    <div class="row mb-3 mt-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                <form action="{{ route('wizard.proses4.index') }}" method="GET" class="d-inline">
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
                            <form action="{{ route('wizard.proses4.audit') }}" method="GET">
                                @if(request('per_page'))
                                    <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                @endif
                                <div class="input-group shadow-sm">
                                    <input type="text" name="search" class="form-control" placeholder="Cari Produk" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-purple" type="submit">
                                            <i class="fas fa-search mr-1"></i> Cari
                                        </button>
                                        @if(request('search'))
                                            <a href="{{ route('wizard.proses4.audit', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if($agingData->count() > 0)
                    @include('partials.bulk-approval-selected-toolbar', ['bulkApproveRoute' => 'wizard.proses4.bulk-approve-selected'])
                    @include('partials.date-range-filter')

                    <div class="table-responsive">
                        <table id="bulkApprovalTable" class="table table-bordered table-striped text-center" style="white-space:nowrap;">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="bulkApprovalSelectAll" title="Pilih semua"></th>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Shift</th>
                                    <th>Produk</th>
                                    <th>Kode Produksi</th>
                                    <th>Aging (Mulai - Selesai)</th>
                                    <th>Suhu Produk</th>
                                    <th>Kondisi</th>
                                    <th>Status Appr</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agingData->getCollection()->flatten(1) as $item)
                                    @php
                                        $aging = $item->prosesAging;
                                    @endphp
                                    <tr>
                                        <td class="align-middle">
                                            <input type="checkbox" class="bulk-approval-checkbox" data-uuid="{{ $item->uuid }}" title="Pilih baris ini">
                                        </td>
                                        <td>{{ $agingData->firstItem() + $loop->index }}</td>
                                        <td>{{ $item->tanggal ? $item->tanggal->format('d-m-Y') : '-' }}</td>
                                        <td>{{ $item->shift->shift ?? 'N/A' }}</td>
                                        <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                        <td>{{ $item->kode_produksi ?? '-' }}</td>
                                        <td>
                                            @if($aging)
                                                {{ $aging->waktu_mulai_aging }} - {{ $aging->waktu_selesai_aging }}
                                            @else
                                                <span class="badge badge-warning"><i class="fas fa-hourglass-half"></i> Belum Aging</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($aging)
                                                @php
                                                    // Parse suhu_produk dan remove (✓) atau (✘)
                                                    // Format: "3°C (✓), 23°C (✓), -0.3°C (✓)" → "3°C, 23°C, -0.3°C"
                                                    $suhuClean = preg_replace('/\s*\([✓✘]\)/u', '', $aging->suhu_produk);
                                                @endphp
                                                {{ $suhuClean }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($aging)
                                                @if($aging->kondisi_produk == '✓')
                                                    <span class="badge badge-success"><i class="fas fa-check"></i> OK</span>
                                                @else
                                                    <span class="badge badge-danger"><i class="fas fa-times"></i> Not OK</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @php
                                                // Sumber approval: aging jika ada, tumbling jika tidak
                                                $approvedQc   = $aging ? $aging->approved_by_qc   : $item->approved_by_qc;
                                                $approvedProd = $aging ? $aging->approved_by_produksi : $item->approved_by_produksi;
                                                $approvedSpv  = $aging ? $aging->approved_by_spv  : $item->approved_by_spv;
                                            @endphp
                                            <div class="status-badges d-flex flex-column align-items-center">
                                                <span class="badge {{ $approvedQc ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                                                    <i class="fas {{ $approvedQc ? 'fa-check' : 'fa-clock' }} mr-1"></i> QC
                                                </span>
                                                <span class="badge {{ $approvedProd ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                                                    <i class="fas {{ $approvedProd ? 'fa-check' : 'fa-clock' }} mr-1"></i> PROD
                                                </span>
                                                <span class="badge {{ $approvedSpv ? 'badge-success' : 'badge-light border' }} w-100" style="font-size: 0.7rem;">
                                                    <i class="fas {{ $approvedSpv ? 'fa-check' : 'fa-clock' }} mr-1"></i> SPV
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="d-flex flex-column align-items-center">
                                                @if($aging)
                                                {{-- Use component for aging data --}}
                                                <x-action-buttons
                                                    :item="$aging"
                                                    route-prefix="wizard.proses4"
                                                    permission-prefix="wizard-proses4"
                                                    :show-view="true"
                                                    :show-print="true"
                                                    :show-history="false"
                                                />
                                                @else
                                                    {{-- Manual buttons for non-aging (tumbling data) --}}
                                                    <div class="d-flex flex-nowrap justify-content-center align-items-center mb-2">
                                                        @if(auth()->user()->hasPermissionTo('view-wizard-proses4'))
                                                            <a href="{{ route('wizard.proses4.show', $item->uuid) }}" class="btn btn-info btn-sm" title="Lihat">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @endif

                                                        @if(auth()->user()->hasPermissionTo('edit-wizard-proses4'))
                                                            @php
                                                                $canEditWp4 = true;
                                                                $editTimeRemaining = null;
                                                                $restrictedRoles = config('edit_limit.restricted_role_ids', []);
                                                                if (in_array(auth()->user()->id_role, $restrictedRoles)) {
                                                                    $timeLimitHours = config('edit_limit.time_limit_hours', 48);
                                                                    $createdAt = \Carbon\Carbon::parse($item->created_at);
                                                                    $hoursElapsed = $createdAt->diffInHours(now(), true);
                                                                    $hoursRemaining = max(0, $timeLimitHours - $hoursElapsed);
                                                                    $editTimeRemaining = $hoursRemaining;
                                                                    $deadlineTime = $createdAt->copy()->addHours($timeLimitHours)->format('d/m/Y H:i');
                                                                    if ($hoursElapsed > $timeLimitHours) {
                                                                        $canEditWp4 = false;
                                                                    }
                                                                }
                                                            @endphp

                                                            @if($canEditWp4)
                                                                <a href="{{ route('wizard.proses4.edit', $item->uuid) }}"
                                                                class="btn btn-warning btn-sm ml-1"
                                                                data-toggle="tooltip"
                                                                title="Edit Tumbling">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="{{ route('wizard.proses4.create', ['continue_uuid' => $item->bahan_baku_tumbling_uuid]) }}"
                                                                class="btn btn-primary btn-sm ml-1"
                                                                data-toggle="tooltip"
                                                                title="Lanjutkan Aging">
                                                                    <i class="fas fa-plus"></i> Aging
                                                                </a>
                                                            @else
                                                                <button class="btn btn-secondary btn-sm ml-1" disabled
                                                                        data-toggle="tooltip"
                                                                        title="Waktu untuk membuat aging sudah habis">
                                                                    <i class="fas fa-lock"></i>
                                                                </button>
                                                            @endif {{-- end $canEditWp4 --}}

                                                        @endif {{-- end hasPermissionTo edit --}}

                                                        @if(auth()->user()->hasPermissionTo('print-wizard-proses4') || auth()->user()->hasPermissionTo('view-wizard-proses4'))
                                                            <a href="{{ route('wizard.proses4.pdf', $item->uuid) }}" target="_blank" class="btn btn-danger btn-sm ml-1" title="Print PDF">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </a>
                                                        @endif

                                                        @if(auth()->user()->hasPermissionTo('delete-wizard-proses4'))
                                                            <form action="{{ route('proses-tumbling.destroy', $item->uuid) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus data tumbling ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm ml-1" title="Hapus">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>

                                                    {{-- Time badge for non-aging --}}
                                                    @if(auth()->user()->hasPermissionTo('edit-wizard-proses4'))
                                                        @if($editTimeRemaining !== null)
                                                            <div class="mt-1 text-center">
                                                                <span class="badge badge-info" style="font-size: 0.7rem;">
                                                                    <i class="far fa-clock"></i> Waktu Edit Tersisa: {{ round($editTimeRemaining, 1) }}h
                                                                </span>
                                                            </div>
                                                        @elseif($canEditWp4)
                                                            <div class="mt-1 text-center">
                                                                <span class="badge badge-success" style="font-size: 0.7rem;">
                                                                    <i class="fas fa-infinity"></i> Unlimited
                                                                </span>
                                                            </div>
                                                        @else
                                                            <div class="mt-1 text-center">
                                                                <span class="badge badge-danger" style="font-size: 0.7rem;">
                                                                    <i class="fas fa-ban"></i> Expired
                                                                </span>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="mt-1">
                                                <x-audit-dropdown :item="$aging ?? $item" route-prefix="wizard.proses4" permission-prefix="wizard-proses4" :uuid="$aging->uuid ?? $item->uuid" />
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $agingData->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Belum ada data riwayat proses 4. Silakan input data baru.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>

<!-- CSS untuk Sequential Approval -->
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
.approval-pending {
    opacity: 0.6;
    cursor: not-allowed;
}
.approval-ready {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
    100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
}
.btn-vertical {
    display: flex;
    flex-direction: column;
    align-items: center;
}
.btn-vertical .mb-1 {
    margin-bottom: 4px;
}
.btn-purple {
    background-color: #6f42c1;
    color: white;
    border-color: #6f42c1;
}
.btn-purple:hover {
    background-color: #5a32a3;
    color: white;
    border-color: #5a32a3;
}
</style>

<!-- Modal Export PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('wizard.proses4.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-file-pdf mr-2"></i>Cetak PDF Proses Tumbling & Aging</h5>
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
                            <input type="date" name="tanggal_mulai" class="form-control" required value="{{ date('Y-m-01') }}">
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
                        <label class="font-weight-bold small text-uppercase text-muted">Shift</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            </div>
                            <select name="shift_id" class="form-control">
                                <option value="">Semua Shift</option>
                                @php
                                    $pdfShifts = auth()->user()->id_role == 1
                                        ? \App\Models\DataShift::orderBy('shift')->get()
                                        : \App\Models\DataShift::where('id_plan', auth()->user()->id_plan)->orderBy('shift')->get();
                                @endphp
                                @foreach($pdfShifts as $shift)
                                    <option value="{{ $shift->id }}">Shift {{ $shift->shift }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Produk</label>
                        <div class="input-group">
                            <select name="produk_id" class="form-control" id="pdf_produk_filter_4">
                                <option value="">Semua Produk</option>
                                @php
                                    $pdfProduks = auth()->user()->id_role == 1
                                        ? \App\Models\JenisProduk::orderBy('nama_produk')->get()
                                        : \App\Models\JenisProduk::where('id_plan', auth()->user()->id_plan)->orderBy('nama_produk')->get();
                                @endphp
                                @foreach($pdfProduks as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
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
                            <input type="text" name="kode_form" class="form-control" value="QF 07/00" required>
                        </div>
                    </div>
                    <input type="hidden" name="is_audit" value="0">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 font-weight-bold" formaction="{{ route('wizard.proses4.bulk-export-excel') }}"><i class="fas fa-file-excel mr-2"></i> Export Excel</button>
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold" formaction="{{ route('wizard.proses4.bulk-export-pdf') }}"><i class="fas fa-print mr-2"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
@include('partials.bulk-approval-selected-script')
<script>
$(document).ready(function() {
    // Tampilkan success message dari URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const successMsg = urlParams.get('success');
    if (successMsg) {
        // Tampilkan alert
        const alertHtml = `
            <div class="alert alert-success alert-dismissible shadow-sm border-0">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="icon fas fa-check-circle"></i> ${decodeURIComponent(successMsg)}
            </div>
        `;
        $('.card-body').prepend(alertHtml);

        // Bersihkan URL parameter
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }

    // Single Approval Handler
    $(document).on('click', '.approve-btn', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        const button = $(this);

        const typeNames = { 'qc': 'QC', 'produksi': 'Produksi', 'spv': 'SPV' };

        if (confirm(`Apakah Anda yakin ingin menyetujui data ini sebagai ${typeNames[type]}?`)) {
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: '{{ route("wizard.proses4.approve", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Gagal: ' + response.message);
                        button.prop('disabled', false).html('<i class="fas fa-check"></i> ' + typeNames[type]);
                    }
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan');
                    button.prop('disabled', false).html('<i class="fas fa-check"></i> ' + typeNames[type]);
                }
            });
        }
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#pdf_produk_filter_4').select2({
            dropdownParent: $('#exportPdfModal'),
            placeholder: 'Pilih Produk',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
@endsection