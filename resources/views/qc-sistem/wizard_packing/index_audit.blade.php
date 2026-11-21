@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Pengemasan Packing</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('wizards.packing.index') }}">Back</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('wizards.packing.index') }}">Packing</a></li>
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
                        <div class="card card-purple card-outline border-purple shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title text-bold text-purple">
                                    <i class="fas fa-shield-alt mr-2"></i>
                                    Pengemasan Packing
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-sm btn-danger mr-2" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                                    </button>
                                    @if(auth()->user()->id_role !== 6)
                                    <span class="badge badge-purple p-2 mr-2">MODE AUDIT AKTIF</span>
                                    <a href="{{ route('wizards.packing.index') }}" class="btn btn-default btn-sm shadow-sm">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Operasional
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
                                            <form action="{{ route('wizards.packing.audit') }}" method="GET" class="d-inline">
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
                                        <form action="{{ route('wizards.packing.audit') }}" method="GET">
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
                                                        <a href="{{ route('wizards.packing.audit', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
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
                                                <!-- <th>Waktu Pengemasan</th> -->
                                                <th>Shift</th>
                                                <th>Produk</th>
                                                <th>Kode Produksi</th>
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
                                                    <!-- <td>{{ $item->pengemasanPlastik->waktu_pengemasan ?? '-' }}</td> -->
                                                    <td>{{ $item->shift->shift ?? '-' }}</td>
                                                    <td>{{ $item->pengemasanProduk->produk->nama_produk ?? '-' }} {{ $item->pengemasanProduk->berat ?? '' }} gram</td>
                                                    <td>{{ $item->pengemasanProduk->kode_produksi ?? '-' }}</td>
                                                    <td class="align-middle">
                                                        <div class="status-badges d-flex flex-column align-items-center">
                                                            <span class="badge {{ $item->approved_by_qc ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;" title="2. Diverifikasi Oleh: QC Inspektor">
                                                                <i class="fas {{ $item->approved_by_qc ? 'fa-check' : 'fa-clock' }} mr-1"></i> 2. QC
                                                            </span>
                                                            <span class="badge {{ $item->approved_by_produksi ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;" title="3. Diketahui Oleh: Foreman / SPV Produksi">
                                                                <i class="fas {{ $item->approved_by_produksi ? 'fa-check' : 'fa-clock' }} mr-1"></i> 3. PROD
                                                            </span>
                                                            <span class="badge {{ $item->approved_by_spv ? 'badge-success' : 'badge-light border' }} w-100" style="font-size: 0.7rem;" title="4. Disetujui Oleh: SPV QC">
                                                                <i class="fas {{ $item->approved_by_spv ? 'fa-check' : 'fa-clock' }} mr-1"></i> 4. SPV QC
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <x-action-buttons :item="$item" route-prefix="wizards.packing" permission-prefix="wizard-packing" :show-view="true" :show-print="true" :show-history="true" />
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
                <div class="modal-header bg-purple text-white">
                    <h5 class="modal-title" id="exportPdfModalLabel"><i class="fas fa-file-pdf mr-1"></i> Cetak PDF Audit Packing</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('wizards.packing.bulk-export-pdf') }}" method="POST" id="bulkExportForm">
                    @csrf
                    <input type="hidden" name="is_audit" value="1">
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
.border-purple {
    border-color: #6f42c1 !important;
}
</style>

@push('scripts')
@include('partials.bulk-approval-selected-script')
<script>
    $(document).ready(function() {
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