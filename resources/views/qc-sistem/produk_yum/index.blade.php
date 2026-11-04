@extends('layouts.app')

@section('title', 'Data Produk YUM')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">

        {{-- Header --}}
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data KPI Produk YUM</h1>
                    </div>

                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="#">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">
                                KPI Produk YUM
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <section class="content">
            <div class="container-fluid">

                <div class="card shadow-sm">

                    {{-- Card Header --}}
                    <div class="card-header">

                        {{-- Title --}}
                        <h3 class="card-title">
                            <i class="fas fa-list mr-2"></i>
                            Daftar KPI Produk YUM
                        </h3>

                        {{-- Button Position Tetap di Sudut --}}
                        <div class="card-tools">
                            <div class="btn-group mr-1">
                                <button type="button" class="btn btn-danger btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF Filter
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-filter text-danger mr-1"></i> Berdasarkan Filter
                                    </a>
                                </div>
                            </div>
                            <a href="{{ route('produk-yum.audit') }}" class="btn btn-sm bg-purple shadow-sm mr-1" style="color: white">
                                <i class="fas fa-history"></i>
                            </a>
                            @if(auth()->user()->hasPermissionTo('create-produk-yum'))
                                <a href="{{ route('produk-yum.create') }}"
                                class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i>
                                    Tambah Data
                                </a>
                            @endif

                        </div>

                    </div>

                    {{-- Card Body --}}
                    <div class="card-body">

                        {{-- Success Alert --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible shadow-sm border-0">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <i class="icon fas fa-check-circle"></i> {{ session('success') }}
                            </div>
                        @endif

                        {{-- Error Alert --}}
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible shadow-sm border-0">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <i class="icon fas fa-exclamation-triangle"></i> {{ session('error') }}
                            </div>
                        @endif

                        {{-- Filter & Search --}}
                        <div class="row mb-3 mt-3">
                            <div class="col-md-4">
                                <form action="{{ route('produk-yum.index') }}" method="GET" class="form-inline">
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
                                <form action="{{ route('produk-yum.index') }}" method="GET">
                                    <div class="input-group shadow-sm">
                                        <input type="text" name="search" class="form-control" placeholder="Cari nama produk atau kode produksi" value="{{ $search ?? '' }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search mr-1"></i> Cari
                                            </button>
                                            @if(!empty($search))
                                                <a href="{{ route('produk-yum.index', ['per_page' => $perPage ?? 10]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">
                                </form>
                            </div>
                        </div>

                        {{-- Search Result Info --}}
                        <div class="row mb-2">
                            <div class="col-md-6">

                                <small class="text-muted">

                                    @if(!empty($search))
                                        Hasil pencarian:
                                        "<strong>{{ $search }}</strong>"
                                    @else
                                        Data Produk YUM
                                    @endif

                                </small>

                            </div>
                        </div>

                        {{-- Table --}}
                            @include('partials.date-range-filter')

                        <div class="table-responsive">

                            @include('partials.bulk-approval-selected-toolbar', ['bulkApproveRoute' => 'produk-yum.bulk-approve-selected'])

<table class="table table-bordered table-striped text-center"
                                   style="white-space: nowrap;">

                                <thead class="thead-light">
                                    <tr>
                                        <th><input type="checkbox" id="bulkApprovalSelectAll" aria-label="Pilih semua data"></th>
                                                <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Shift</th>
                                        <th>Produk</th>
                                        <th>Kode Produksi</th>
                                        <th>Data Pack</th>
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

                                    @foreach($groupedData as $groupKey => $group)
                                        @php
                                            $first = $group->first();
                                            $itemDate = $first->tanggal ? $first->tanggal->format('Y-m-d') : 'no_date';
                                            $shiftId = $first->shift_id ?? 'no_shift';
                                            $shiftGroupKey = $itemDate . '_' . $shiftId;
                                        @endphp

                                        {{-- Header Grup Shift --}}
                                        @if($currentGroup !== $shiftGroupKey)

                                            @php $currentGroup = $shiftGroupKey; @endphp
                                        @endif

                                        @foreach($group as $item)
                                            <tr>

                                                {{-- No --}}
                                                <td><input type="checkbox" class="bulk-approval-checkbox" data-uuid="{{ $item->uuid }}" aria-label="Pilih data"></td>
<td>
                                                    {{ $loop->parent->index * $group->count() + $loop->iteration }}
                                                </td>

                                            {{-- Tanggal --}}
                                            <td>
                                                <span class="badge badge-secondary">

                                                    @if(auth()->user()->id_role == 2 || auth()->user()->id_role == 3)
                                                        {{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}
                                                    @else
                                                        {{ $item->tanggal ? $item->tanggal->format('d/m/Y H:i:s') : '-' }}
                                                    @endif

                                                </span>
                                            </td>

                                            {{-- Jam --}}
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                                </span>
                                            </td>

                                            {{-- Shift --}}
                                            <td>
                                                <span class="badge badge-primary">
                                                    Shift {{ $item->shift->shift ?? '-' }}
                                                </span>
                                            </td>

                                            {{-- Produk --}}
                                            <td>
                                                {{ $item->produk->nama_produk ?? '-' }}
                                            </td>

                                            {{-- Kode Produksi --}}
                                            <td>
                                                {{ $item->kode_produksi }}
                                            </td>

                                            {{-- Data Pack --}}
                                            <td>
                                                {{ $item->dataBag->std_bag ?? '-' }}
                                            </td>

                                            {{-- User --}}
                                            <td>
                                                {{ $item->user->name ?? '-' }}
                                            </td>

                                            {{-- Status Appr --}}
                                            <td class="align-middle">
                                                <div class="status-badges d-flex flex-column align-items-center">
                                                    <span class="badge {{ $item->approved_by_qc ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                                                        <i class="fas {{ $item->approved_by_qc ? 'fa-check' : 'fa-clock' }} mr-1"></i> QC
                                                    </span>
                                                    <span class="badge {{ $item->approved_by_produksi ? 'badge-success' : 'badge-light border' }} mb-1 w-100" style="font-size: 0.7rem;">
                                                        <i class="fas {{ $item->approved_by_produksi ? 'fa-check' : 'fa-clock' }} mr-1"></i> PROD
                                                    </span>
                                                    <span class="badge {{ $item->approved_by_spv ? 'badge-success' : 'badge-light border' }} w-100" style="font-size: 0.7rem;">
                                                        <i class="fas {{ $item->approved_by_spv ? 'fa-check' : 'fa-clock' }} mr-1"></i> SPV QC
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <x-action-buttons
                                                    :item="$item"
                                                    route-prefix="produk-yum"
                                                    :show-logs="false"
                                                    :show-print="true"
                                                />
                                                <div class="mt-2 d-flex justify-content-center">
                                                    <x-audit-dropdown :item="$item" route-prefix="produk-yum" :uuid="$item->uuid" />
                                                </div>
                                            </td>
                                            </tr>
                                        @endforeach

                                    @endforeach

                                    @if($groupedData->isEmpty())
                                        <tr>
                                            <td colspan="10" class="text-center">
                                                Tidak ada data
                                            </td>
                                        </tr>
                                    @endif

                                </tbody>

                            </table>

                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-center mt-3">
                            {{ $groupedData->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Modal Export PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('produk-yum.bulk-export-pdf') }}" method="POST">
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
                        <select class="form-control" id="filterShift" name="shift_id">
                            <option value="">Semua Shift</option>
                            <!-- Kita asumsikan shift standar 1, 2, 3 atau sesuai master data -->
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterProduk">Produk</label>
                        <select class="form-control" id="filterProduk" name="id_produk">
                            <option value="">Semua Produk</option>
                            @php
                                $produks = \App\Models\JenisProduk::all();
                            @endphp
                            @foreach($produks as $produk)
                                <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
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
                               value="QF 16/00"
                               required
                               placeholder="Masukkan kode form">
                        @error('kode_form')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi:</strong> Data yang di-export adalah data operasional sesuai filter yang dipilih.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                    <button type="submit" formaction="{{ route('produk-yum.bulk-export-pdf') }}" class="btn btn-danger"><i class="fas fa-file-pdf mr-1"></i> Export PDF</button>
                    <button type="submit" formaction="{{ route('produk-yum.bulk-export-excel') }}" class="btn btn-success"><i class="fas fa-file-excel mr-1"></i> Export Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {    /**
     * Handle Single Approval Button Click
     */
    $('.approve-btn').on('click', function () {
        var $btn = $(this);
        var id = $btn.data('id');
        var type = $btn.data('type');
        var originalHtml = $btn.html();

        if (!confirm('Apakah Anda yakin ingin menyetujui data ini?')) {
            return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '/qc-sistem/produk-yum/' + id + '/approve',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                type: type
            },
            success: function (response) {
                if (response.success) {
                    showNotif('success', response.message);
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    showNotif('error', response.message);
                    $btn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.';
                showNotif('error', msg);
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    /**
     * Helper: Tampilkan notifikasi
     */
    function showNotif(type, message) {
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            alert(message);
        }
    }

});
</script>
@endpush
@include('partials.bulk-approval-selected-script')

@endsection
