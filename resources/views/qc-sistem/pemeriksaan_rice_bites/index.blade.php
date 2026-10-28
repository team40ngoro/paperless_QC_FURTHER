@extends('layouts.app')

@section('title', 'Data Pemeriksaan Rice Bites')

@php
use Illuminate\Support\Facades\Schema;
@endphp

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Pemeriksaan Rice Bites</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pemeriksaan Rice Bites</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
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

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-2"></i>Daftar Pemeriksaan Rice Bites
                        </h3>
                        <div class="card-tools">
                            <div class="dropdown d-inline-block mr-1">
                                <button class="btn btn-danger btn-sm dropdown-toggle" type="button" id="exportMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF Filter
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportMenuButton">
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#pdfExportModal">
                                        <i class="fas fa-file-export text-primary mr-2"></i> Export Data (PDF / Excel)
                                    </a>
                                </div>
                            </div>
                            <a href="{{ route('pemeriksaan-rice-bites.audit') }}" class="btn btn-sm bg-purple mr-1 shadow-sm px-2-5" style="color: white; min-width: 34px; height: 31px; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="fas fa-history"></i>
                            </a>
                            @if(auth()->user()->hasPermissionTo('create-pemeriksaan-rice-bites'))
                                <a href="{{ route('pemeriksaan-rice-bites.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Form Pencarian Server-Side -->
                        <div class="row mb-3 mt-3">
                            <div class="col-md-4">
                                <form action="{{ route('pemeriksaan-rice-bites.index') }}" method="GET" class="form-inline">
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
                                <form action="{{ route('pemeriksaan-rice-bites.index') }}" method="GET">
                                    <div class="input-group shadow-sm">
                                        <input type="text" name="search" class="form-control" placeholder="Cari nama produk atau kode produksi" value="{{ $search ?? '' }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search mr-1"></i> Cari
                                            </button>
                                            @if(!empty($search))
                                                <a href="{{ route('pemeriksaan-rice-bites.index', ['per_page' => $perPage ?? 10]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
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
                                        Data Pemeriksaan Rice Bites
                                    @endif
                                </small>
                            </div>
                        </div>

                            @include('partials.date-range-filter')

                        <div class="table-responsive">
                            @include('partials.bulk-approval-selected-toolbar', ['bulkApproveRoute' => 'pemeriksaan-rice-bites.bulk-approve-selected'])

                            <table  class="table table-bordered text-center table-striped" style="white-space: nowrap;">
                                <thead class="thead-light">
                                    <tr>
                                        <th><input type="checkbox" id="bulkApprovalSelectAll" aria-label="Pilih semua data"></th>
                                                <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Shift</th>
                                        <th>Produk</th>
                                        <th>Nomor Formula</th>
                                        <th>Batch</th>
                                        <th>No Cooking Cycle</th>
                                        <th>Rata-rata Suhu</th>
                                        <th>Hasil Pencampuran</th>
                                        <th>Dibuat Oleh</th>
                                        <th>Status Approver</th>
                                        <th style="width: 150px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $userRole = auth()->user()->id_role ?? null;
                                    @endphp
                                    @forelse($groupedData as $groupKey => $groupItems)
                                        @php
                                            $firstItem = $groupItems->first();
                                            $tanggalGroup = \Carbon\Carbon::parse($firstItem->tanggal)->format('Y-m-d');
                                            $shiftIdGroup = $firstItem->shift_id;
                                            $shiftName = $firstItem->shift->shift ?? 'Unknown';

                                            $allApprovedQC = $groupItems->every(fn($i) => $i->approved_by_qc);
                                            $allApprovedProduksi = $groupItems->every(fn($i) => $i->approved_by_produksi);
                                            $allApprovedSPV = $groupItems->every(fn($i) => $i->approved_by_spv);

                                            $canApproveQC = in_array($userRole, [1, 3, 5]) && !$allApprovedQC;
                                            $canApproveProduksi = in_array($userRole, [1, 2, 5]) && $allApprovedQC && !$allApprovedProduksi;
                                            $canApproveSPV = in_array($userRole, [1, 4, 5]) && $allApprovedProduksi && !$allApprovedSPV;
                                        @endphp



                                        @foreach($groupItems as $item)
                                            <tr>
                                                <td><input type="checkbox" class="bulk-approval-checkbox" data-uuid="{{ $item->uuid }}" aria-label="Pilih data"></td>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="text-muted small">
                                                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-secondary">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted small">Shift {{ $item->shift->shift ?? '-' }}</span>
                                                </td>
                                                <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                                <td>
                                                    @if($item->nomor_formula)
                                                        <span class="badge badge-primary">{{ $item->nomor_formula }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->batch }}</td>
                                                <td>{{ $item->no_cooking_cycle }}</td>
                                                <td class="text-center">
                                                    {{ number_format($item->rata_rata_suhu, 2) }}°C
                                                </td>
                                                <td class="text-center">
                                                    {!! $item->hasil_pencampuran_icon !!} {{ $item->hasil_pencampuran ?? '-' }}
                                                </td>
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
                                                    <x-action-buttons :item="$item" class="tombol" route-prefix="pemeriksaan-rice-bites" :show-print="true" :isAuditPage="false" />
                                                    <div class="mt-2 d-flex justify-content-center">
                                                        <x-audit-dropdown :item="$item" route-prefix="pemeriksaan-rice-bites" :uuid="$item->uuid" />
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center">
                                                <div class="py-4">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">Belum ada data pemeriksaan rice bites</p>
                                                    @if(auth()->user()->hasPermissionTo('create-pemeriksaan-rice-bites'))
                                                        <a href="{{ route('pemeriksaan-rice-bites.create') }}" class="btn btn-primary">
                                                            <i class="fas fa-plus"></i> Tambah Data Pertama
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
</div>

<!-- PDF Export Modal -->
<div class="modal fade" id="pdfExportModal" tabindex="-1" role="dialog" aria-labelledby="pdfExportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfExportModalLabel">
                    <i class="fas fa-file-pdf text-danger"></i> Export PDF Pemeriksaan Rice Bites
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('pemeriksaan-rice-bites.bulk-export-pdf') }}" method="POST">
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
                        <label for="export_shift">Shift</label>
                        <select class="form-control" id="export_shift" name="shift">
                            <option value="">Semua Shift</option>
                            <option value="1">Shift 1</option>
                            <option value="2">Shift 2</option>
                            <option value="3">Shift 3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="export_produk">Produk</label>
                        <select class="form-control" id="id_produk" name="produk">
                            <option value="">Semua Produk</option>
                            @foreach($cachedProduk as $produk)
                                <option value="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="export_kode_form">Kode Form <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               id="export_kode_form"
                               name="kode_form"
                               maxlength="50"
                               value="QF 14/00"
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
                    <button type="submit" class="btn btn-danger" formaction="{{ route('pemeriksaan-rice-bites.bulk-export-pdf') }}"><i class="fas fa-file-pdf"></i> Export PDF</button>
                    <button type="submit" class="btn btn-success" formaction="{{ route('pemeriksaan-rice-bites.bulk-export-excel') }}"><i class="fas fa-file-excel"></i> Export Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('partials.bulk-approval-selected-script')

@endsection

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
@push('scripts')
<script>
$(document).ready(function() {    /**
     * Handle Single Approval
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
                url: '{{ route("pemeriksaan-rice-bites.approve", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                        button.prop('disabled', false).html('<i class="fas fa-check"></i> Approve');
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan');
                    button.prop('disabled', false).html('<i class="fas fa-check"></i> Approve');
                }
            });
        }
    });
});
</script>
@endpush
