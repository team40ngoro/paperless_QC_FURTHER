@extends('layouts.app')

@section('title', 'Data Pemasakan Nasi')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Pemasakan Nasi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Pemasakan Nasi</li>
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
                            <h3 class="card-title">Daftar Pemasakan Nasi</h3>
                            <div class="card-tools d-flex">
                                <form action="{{ route('pemasakan-nasi.index') }}" method="GET" class="mr-2 mt-1">
                                    <div class="input-group input-group-sm" style="width: 250px;">
                                        <input type="text" name="search" class="form-control" placeholder="Cari produk atau kode produksi" value="{{ $search ?? '' }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            @if($search)
                                                <a href="{{ route('pemasakan-nasi.index') }}" class="btn btn-default">
                                                    <i class="fas fa-times text-danger"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">
                                </form>
                                <div class="dropdown d-inline-block mr-1">
                                    <button class="btn btn-danger btn-sm dropdown-toggle mr-1" type="button" id="exportMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-file-pdf"></i> Cetak PDF Filter
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportMenuButton">
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#exportPdfModal">
                                            <i class="fas fa-file-export text-primary mr-2"></i> Export Data (PDF / Excel)
                                        </a>
                                    </div>
                                </div>
                                <a href="{{ route('pemasakan-nasi.audit') }}" class="btn btn-sm bg-purple mr-2 shadow-sm" style="color: white">
                                    <i class="fas fa-history"></i>
                                </a>
                                @if(auth()->user()->hasPermissionTo('create-pemasakan-nasi'))
                                    <a href="{{ route('pemasakan-nasi.create') }}" class="btn btn-primary btn-sm">
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

                            {{-- Per-page selector --}}
                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <form action="{{ route('pemasakan-nasi.index') }}" method="GET" class="form-inline">
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
                                    <form action="{{ route('pemasakan-nasi.index') }}" method="GET">
                                        <div class="input-group shadow-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Cari nama produk atau kode produksi" value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(!empty($search))
                                                    <a href="{{ route('pemasakan-nasi.index', ['per_page' => $perPage ?? 10]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">
                                    </form>
                                </div>
                            </div>

                            @include('partials.date-range-filter')
                            <div class="table-responsive">
                            @include('partials.bulk-approval-selected-toolbar', ['bulkApproveRoute' => 'pemasakan-nasi.bulk-approve-selected', 'bulkApprovalRoleMap' => ['qc' => [1, 3, 5], 'produksi' => [2], 'spv' => [4]]])

                                <table class="table table-bordered text-center table-striped" style="white-space: nowrap;">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="bulkApprovalSelectAll" aria-label="Pilih semua data"></th>
                                                <th>No</th>
                                            <th>Shift</th>
                                            <th>Tanggal</th>
                                            <th>Jam</th>
                                            <th>Nama Produk</th>
                                            <th>Kode Produksi</th>
                                            <th>Proses</th>
                                            <th class="d-none">Status Cooking</th>
                                            <th class="d-none">Sensori Kondisi</th>
                                            <th class="d-none">Organo Warna</th>
                                            <th class="d-none">Organo Aroma</th>
                                            <th class="d-none">Organo Rasa</th>
                                            <th class="d-none">Organo Tekstur</th>
                                            <th>Dibuat Oleh</th>
                                            <th>Status Approver</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $userRole = auth()->user()->id_role ?? null; @endphp

                                        @forelse($groupedData as $groupKey => $groupItems)
                                            @php
                                                $firstItem        = $groupItems->first();
                                                $tanggalGroup     = \Carbon\Carbon::parse($firstItem->tanggal)->format('Y-m-d');
                                                $shiftIdGroup     = $firstItem->shift_id;
                                                $shiftName        = $firstItem->shift->shift ?? '-';

                                                $allApprovedQC       = $groupItems->every(fn($i) => $i->approved_by_qc);
                                                $allApprovedProduksi = $groupItems->every(fn($i) => $i->approved_by_produksi);
                                                $allApprovedSPV      = $groupItems->every(fn($i) => $i->approved_by_spv);

                                                $canApproveQC       = in_array($userRole, [1, 3, 5]) && !$allApprovedQC;
                                                $canApproveProduksi = in_array($userRole, [1, 2, 5]) && $allApprovedQC && !$allApprovedProduksi;
                                                $canApproveSPV      = in_array($userRole, [1, 4, 5]) && $allApprovedProduksi && !$allApprovedSPV;
                                            @endphp

                                            {{-- Group Header --}}


                                            @foreach($groupItems as $item)
                                                <tr>
                                                    <td><input type="checkbox" class="bulk-approval-checkbox" data-uuid="{{ $item->uuid }}" aria-label="Pilih data"></td>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        @if($item->shift->shift == 1)
                                                            <span class="badge bg-primary">Shift 1</span>
                                                        @elseif($item->shift->shift == 2)
                                                            <span class="badge bg-success">Shift 2</span>
                                                        @else
                                                            <span class="badge bg-secondary">Shift {{ $item->shift->shift ?? '-' }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(auth()->user()->id_role == 2 || auth()->user()->id_role == 3)
                                                            <span class="badge badge-info">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</span>
                                                        @else
                                                            <span class="badge badge-info">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y H:i:s') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-secondary">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                                    </td>
                                                    <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                                    <td>{{ $item->kode_produksi }}</td>
                                                    <td>{{ $item->proses }}</td>
                                                    <td class="text-center d-none">
                                                        @if($item->status_cooking)
                                                            <i class="fas fa-check text-success"></i>
                                                        @else
                                                            <i class="fas fa-times text-danger"></i>
                                                        @endif
                                                    </td>
                                                    <td class="text-center d-none">{!! App\Models\PemasakanNasi::getOrganoIcon($item->sensori_kondisi) !!}</td>
                                                    <td class="text-center d-none">{!! App\Models\PemasakanNasi::getOrganoIcon($item->organo_warna) !!}</td>
                                                    <td class="text-center d-none">{!! App\Models\PemasakanNasi::getOrganoIcon($item->organo_aroma) !!}</td>
                                                    <td class="text-center d-none">{!! App\Models\PemasakanNasi::getOrganoIcon($item->organo_rasa) !!}</td>
                                                    <td class="text-center d-none">{!! App\Models\PemasakanNasi::getOrganoIcon($item->organo_tekstur) !!}</td>
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
                                                    <div class="btn-vertical">
                                                        <x-action-buttons :item="$item" route-prefix="pemasakan-nasi" :show-log="false" :isAuditPage="false" />
                                                        <div class="mt-2 d-flex justify-content-center">
                                                            <x-audit-dropdown :item="$item" route-prefix="pemasakan-nasi" :uuid="$item->uuid" />
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">Tidak ada data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
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
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Pemasakan Nasi Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('pemasakan-nasi.bulk-export-pdf') }}" method="POST">
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
                        <select class="form-control" id="filterShift" name="shift">
                            <option value="">Semua Shift</option>
                            @foreach($cachedShifts as $shift)
                                <option value="{{ $shift->shift }}">{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterProduk">Nama Produk</label>
                        <select class="form-control" id="id_produk" name="produk">
                            <option value="">Semua Produk</option>
                            @foreach($cachedProduk as $produk)
                                <option value="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</option>
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
                               value="QF 13/00"
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
                    <button type="submit" class="btn btn-danger" formaction="{{ route('pemasakan-nasi.bulk-export-pdf') }}"><i class="fas fa-file-pdf"></i> Export PDF</button>
                    <button type="submit" class="btn btn-success" formaction="{{ route('pemasakan-nasi.bulk-export-excel') }}"><i class="fas fa-file-excel"></i> Export Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
</style>
@push('scripts')
<script>
$(document).ready(function() {

    // Removed JS for PDF export

});
</script>
@endpush

@include('partials.bulk-approval-selected-script')

@endsection
