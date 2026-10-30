@extends('layouts.app')

@section('title', 'Data Pemeriksaan Produk Cooking Mixer FLA')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Pemeriksaan Produk Cooking Mixer FLA</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Pemeriksaan Produk Cooking Mixer FLA</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Daftar Pemeriksaan Produk Cooking Mixer FLA</h3>
                            <div class="card-tools">
                                <div class="dropdown d-inline-block mr-1">
                                    <button class="btn btn-danger btn-sm dropdown-toggle" type="button" id="exportMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-file-pdf mr-1"></i> Cetak PDF Filter
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportMenuButton">
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#exportPdfModal">
                                            <i class="fas fa-file-export text-primary mr-2"></i> Export Data (PDF / Excel)
                                        </a>
                                    </div>
                                </div>
                                <a href="{{ route('pemeriksaan-produk-cooking-mixer-fla.audit') }}" class="btn btn-sm bg-purple shadow-sm mr-1" style="color: white">
                                    <i class="fas fa-history"></i>
                                </a>
                                @if(auth()->user()->hasPermissionTo('create-pemeriksaan-produk-cooking-mixer-fla'))
                                    <a href="{{ route('pemeriksaan-produk-cooking-mixer-fla.create') }}" class="btn btn-primary btn-sm">
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

                            <!-- Form Pencarian Server-Side -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <form action="{{ route('pemeriksaan-produk-cooking-mixer-fla.index') }}" method="GET" class="form-inline">
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
                                    <form action="{{ route('pemeriksaan-produk-cooking-mixer-fla.index') }}" method="GET">
                                        <div class="input-group shadow-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Cari nama produk atau Kode Produksi" value="{{ $search ?? '' }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(!empty($search))
                                                    <a href="{{ route('pemeriksaan-produk-cooking-mixer-fla.index', ['per_page' => $perPage ?? 10]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
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
                                            Data Pemeriksaan Produk Cooking Mixer FLA
                                        @endif
                                    </small>
                                </div>
                            </div>

                            @include('partials.date-range-filter')

                            <div class="table-responsive">
                                @include('partials.bulk-approval-selected-toolbar', ['bulkApproveRoute' => 'pemeriksaan-produk-cooking-mixer-fla.bulk-approve-selected', 'bulkApprovalRoleMap' => ['qc' => [1, 3, 5], 'produksi' => [2], 'spv' => [4]]])

                                <table class="table table-bordered text-center table-striped"  style="white-space: nowrap;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th><input type="checkbox" id="bulkApprovalSelectAll" aria-label="Pilih semua data"></th>
                                                <th>No</th>
                                            <th>Plan</th>
                                            <th>Shift</th>
                                            <th>Tanggal</th>
                                            <th>Jam</th>
                                            <th>Nama Produk</th>
                                            <th>Kode Produksi</th>
                                            <th>Waktu</th>
                                            <th>Status Gas</th>
                                            <th>Speed</th>
                                            <th>Organoleptic</th>
                                            <th>Status Approver</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $groupedItems = $items->groupBy(function ($item) {
                                                $dateStr = $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : 'no_date';
                                                return $dateStr . '_' . ($item->shift_id ?? 'no_shift');
                                            });
                                            $globalIndex = $items->firstItem();
                                        @endphp
                                        @forelse($groupedItems as $groupKey => $group)
                                            @php
                                                $firstItem = $group->first();
                                                $date = $firstItem->tanggal ? \Carbon\Carbon::parse($firstItem->tanggal)->format('Y-m-d') : null;
                                                $shiftId = $firstItem->shift_id;
                                                $shiftName = $firstItem->shift->shift ?? '-';
                                                $count = $group->count();

                                                $allApprovedQc = $group->every(fn($item) => $item->approved_by_qc);
                                                $allApprovedProduksi = $group->every(fn($item) => $item->approved_by_produksi);
                                                $allApprovedSpv = $group->every(fn($item) => $item->approved_by_spv);
                                            @endphp



                                            @foreach($group as $item)
                                            <tr>
                                                <td><input type="checkbox" class="bulk-approval-checkbox" data-uuid="{{ $item->uuid }}" aria-label="Pilih data"></td>
                                                <td class="align-middle">{{ $globalIndex++ }}</td>
                                                <td class="align-middle">{{ $item->plan->nama_plan ?? '-' }}</td>
                                                <td class="align-middle">
                                                    <span class="badge badge-info">
                                                      Shift  {{ $item->shift->shift ?? '-' }}
                                                    </span>
                                                </td>
                                               <td class="align-middle">
                                                    @if(auth()->user()->id_role == 2 || auth()->user()->id_role == 3)
                                                        <span class="badge badge-info">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</span>
                                                    @else
                                                        <span class="badge badge-info">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y H:i:s') }}</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge badge-secondary">{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</span>
                                                </td>
                                                <td class="align-middle text-left">{{ $item->namaFormulaFla->produk->nama_produk ?? '-' }} {{ $item->berat }} gram </td>
                                                <td class="align-middle">
                                                        <code>{{ $item->kode_produksi }}</code>
                                                </td>
                                                <td class="align-middle">
                                                    <small>
                                                        <strong>Start:</strong> {{ $item->waktu_start }}<br>
                                                        <strong>Stop:</strong> {{ $item->waktu_stop }}
                                                    </small>
                                                </td>
                                                <td class="text-center align-middle">
                                                    @if($item->status_gas)
                                                        <i class="fas fa-check text-success" title="Aktif"></i>
                                                    @else
                                                        <i class="fas fa-times text-danger" title="Tidak Aktif"></i>
                                                    @endif
                                                </td>
                                                <td class="align-middle">{{ $item->speed }} <small class="text-muted">RPM</small></td>
                                                <td class="align-middle text-left">
                                                    <small>
                                                        <strong>Warna:</strong> {!! $item->getOrganoIcon($item->organo_warna) !!}<br>
                                                        <strong>Aroma:</strong> {!! $item->getOrganoIcon($item->organo_aroma) !!}<br>
                                                        <strong>Tekstur:</strong> {!! $item->getOrganoIcon($item->organo_tekstur) !!}<br>
                                                        <strong>Rasa:</strong> {!! $item->getOrganoIcon($item->organo_rasa) !!}
                                                    </small>
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
                                                    <div class="btn-vertical">
                                                        <x-action-buttons :item="$item" route-prefix="pemeriksaan-produk-cooking-mixer-fla" :isAuditPage="false" />
                                                        <div class="mt-2 d-flex justify-content-center">
                                                            <x-audit-dropdown :item="$item" route-prefix="pemeriksaan-produk-cooking-mixer-fla" :uuid="$item->uuid" />
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="13" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">Tidak ada data ditemukan</p>
                                                </div>
                                            </td>
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
                <h5 class="modal-title" id="exportPdfModalLabel">Cetak PDF Berdasarkan Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('pemeriksaan-produk-cooking-mixer-fla.bulk-export-pdf') }}" method="POST">
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
                            @foreach($cachedShifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bulkKodeForm">Kode Form <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               id="bulkKodeForm"
                               name="kode_form"
                               maxlength="50"
                               value="QF 12/00"
                               required
                               placeholder="Masukkan kode form">
                        @error('kode_form')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi:</strong> PDF akan berisi data operasional sesuai filter tanggal dan shift yang dipilih.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                    <button type="submit" class="btn btn-danger" formaction="{{ route('pemeriksaan-produk-cooking-mixer-fla.bulk-export-pdf') }}"><i class="fas fa-file-pdf"></i> Export PDF</button>
                    <button type="submit" class="btn btn-success" formaction="{{ route('pemeriksaan-produk-cooking-mixer-fla.bulk-export-excel') }}"><i class="fas fa-file-excel"></i> Export Excel</button>
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
@endpush

@include('partials.bulk-approval-selected-script')

@endsection
