@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-tasks text-info"></i> Pemeriksaan Kondisi Area Kerja</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Pemeriksaan Kondisi Area Kerja</li>
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
                                    <i class="fas fa-list text-info mr-2"></i> Data Pemeriksaan Kondisi Area Kerja
                                </h3>
                                <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                                    <!-- <div class="btn-group">
                                        <button type="button" class="btn btn-success btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                            <i class="fas fa-file-import mr-1"></i> Import Excel
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="{{ route('area-proses.download-template') }}" class="dropdown-item">
                                                <i class="fas fa-download text-success mr-2"></i> Download Template
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#importExcelModal">
                                                <i class="fas fa-upload text-primary mr-2"></i> Upload File
                                            </a>
                                        </div>
                                    </div> -->
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
                                    @if(auth()->user()->hasPermissionTo('create-area-proses'))
                                        <a href="{{ route('area-proses.audit') }}" class="btn btn-purple btn-sm shadow-sm" style="background-color:#6f42c1;color:#fff;" title="Riwayat Audit">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <a href="{{ route('area-proses.create') }}" class="btn btn-primary btn-sm shadow-sm">
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
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible shadow-sm border-0">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fas fa-exclamation-circle"></i> {{ session('error') }}
                                </div>
                            @endif

                            @if(request()->filled('group_uuid'))
                                <div class="alert alert-info border-0 shadow-sm d-flex justify-content-between align-items-center flex-wrap" style="gap: 0.5rem;">
                                    <span><i class="fas fa-history mr-2"></i> Menampilkan seluruh riwayat pemeriksaan per jam untuk grup ini.</span>
                                    <a href="{{ route('area-proses.index') }}" class="btn btn-sm btn-default shadow-sm">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke semua data
                                    </a>
                                </div>
                            @endif

                            <!-- Search and Filter Controls -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                        <form action="{{ route('area-proses.index') }}" method="GET" class="d-inline">
                                            @if(request('search'))
                                                <input type="hidden" name="search" value="{{ request('search') }}">
                                            @endif
                                            @if(request()->filled('group_uuid'))
                                                <input type="hidden" name="group_uuid" value="{{ request('group_uuid') }}">
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
                                    <form action="{{ route('area-proses.index') }}" method="GET">
                                        @if(request('per_page'))
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                        @endif
                                        @if(request()->filled('group_uuid'))
                                            <input type="hidden" name="group_uuid" value="{{ request('group_uuid') }}">
                                        @endif
                                        <div class="input-group shadow-sm">
                                            <select name="search" class="form-control" onchange="this.form.submit()">
                                                <option value="">-- Semua Area --</option>
                                                @foreach($areas ?? [] as $area)
                                                    <option value="{{ $area->area }}" {{ ($search ?? request('search')) == $area->area ? 'selected' : '' }}>
                                                        {{ $area->area }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(!empty($search ?? request('search')))
                                                    <a href="{{ route('area-proses.index', array_filter(['per_page' => request('per_page'), 'group_uuid' => request('group_uuid')])) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
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
                                <table id="mainTable" class="table table-hover border-bottom mb-0" style="white-space:nowrap;">
                                    <thead class="bg-light text-uppercase small font-weight-bold border-top">
                                        <tr>
                                            <th class="text-center align-middle py-3" style="width: 40px;">
                                                <input type="checkbox" id="selectAllCheckbox" title="Pilih semua">
                                            </th>
                                            <th class="text-center py-3" style="width: 50px;">No</th>
                                            <th class="py-3 text-center">Tanggal</th>
                                            <th class="py-3 text-center">Jam</th>
                                            <th class="py-3 text-left">Area / Shift</th>
                                            <th class="py-3 text-center">Kebersihan</th>
                                            <th class="py-3 text-center">Suhu (°C)</th>
                                            <th class="py-3 text-center">Dibuat Oleh</th>
                                            <th class="py-3 text-center" style="width: 100px;">Status Appr</th>
                                            <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @php
                                            $rowNumber = ($areaProses->currentPage() - 1) * $areaProses->perPage();
                                            $isGroupView = request()->filled('group_uuid');
                                        @endphp
                                        @forelse($areaProses as $item)
                                            @php
                                                $rowNumber++;
                                                $isVerified = $item->approved_by_qc && $item->approved_by_produksi && $item->approved_by_spv;
                                                $tanggalFormatted = $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-';
                                                $jamFormatted = $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-';

                                                // Riwayat per jam: record lain dalam grup yang sama (selain baris ini)
                                                $riwayat = (! $isGroupView && $item->groupRecords)
                                                    ? $item->groupRecords->where('uuid', '!=', $item->uuid)->sortBy('jam')->values()
                                                    : collect();
                                            @endphp
                                            <tr @class(['item-row', 'row-verified' => $isVerified]) data-uuid="{{ $item->uuid }}">
                                                <td class="text-center align-middle" style="width: 40px;">
                                                    <input type="checkbox" class="row-checkbox" data-uuid="{{ $item->uuid }}" title="Pilih baris ini">
                                                </td>
                                                <td class="text-center align-middle">{{ $rowNumber }}</td>
                                                <td class="text-center align-middle">{{ $tanggalFormatted }}</td>
                                                <td class="text-center align-middle">{{ $jamFormatted }}</td>
                                                <td class="align-middle">
                                                    <div class="font-weight-bold">{{ $item->area->area ?? '-' }}</div>
                                                    <div class="font-weight-bold">Shift {{ $item->shift->shift ?? '-' }}</div>
                                                    @if($riwayat->count() > 0)
                                                        <button type="button"
                                                                class="btn btn-xs btn-outline-info toggle-riwayat-btn mt-1"
                                                                data-target="riwayat-{{ $item->uuid }}"
                                                                title="Lihat riwayat pemeriksaan per jam">
                                                            <i class="fas fa-history mr-1"></i>
                                                            {{ $riwayat->count() }} Riwayat Per Jam
                                                            <i class="fas fa-chevron-down ml-1 toggle-icon"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="small">
                                                        <span class="badge {{ $item->kebersihan_ruangan === 'OK' ? 'badge-success' : 'badge-danger' }} mr-1">
                                                            <i class="fas {{ $item->kebersihan_ruangan === 'OK' ? 'fa-check' : 'fa-times' }}"></i> Ruangan
                                                        </span>
                                                        <span class="badge {{ $item->kebersihan_karyawan === 'OK' ? 'badge-success' : 'badge-danger' }}">
                                                            <i class="fas {{ $item->kebersihan_karyawan === 'OK' ? 'fa-check' : 'fa-times' }}"></i> Karyawan
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="font-weight-bold text-primary">{{ $item->pemeriksaan_suhu_ruang ?? '-' }}</span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="small text-muted">{{ $item->user->name ?? '-' }}</span>
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
                                                    <x-area-proses-buttons :item="$item" :is-audit-page="false" />
                                                    @if(!isset($isAuditPage) || !$isAuditPage)
                                                        <x-audit-dropdown :item="$item" route-prefix="area-proses" :uuid="$item->uuid" />
                                                    @endif
                                                </td>
                                            </tr>

                                            {{-- Baris collapse: Riwayat pemeriksaan per jam --}}
                                            @if($riwayat->count() > 0)
                                                <tr class="riwayat-row" id="riwayat-{{ $item->uuid }}" style="display: none;">
                                                    <td colspan="10" class="p-0 border-top-0">
                                                        <div class="riwayat-box m-2 p-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <small class="text-muted font-weight-bold text-uppercase">
                                                                    <i class="fas fa-history mr-1"></i> Riwayat Pemeriksaan Per Jam
                                                                    &mdash; {{ $item->area->area ?? '-' }} (Shift {{ $item->shift->shift ?? '-' }})
                                                                </small>
                                                                <a href="{{ route('area-proses.index', ['group_uuid' => $item->group_uuid ?: $item->uuid]) }}" class="btn btn-xs btn-outline-secondary">
                                                                    <i class="fas fa-external-link-alt mr-1"></i> Buka semua
                                                                </a>
                                                            </div>
                                                            <table class="table table-sm table-bordered mb-0 bg-white text-center" style="font-size: 0.8rem;">
                                                                <thead class="thead-light">
                                                                    <tr>
                                                                        <th style="width: 40px;">No</th>
                                                                        <th>Jam</th>
                                                                        <th>Kebersihan</th>
                                                                        <th>Suhu (°C)</th>
                                                                        <th>Dibuat Oleh</th>
                                                                        <!-- <th>Status Appr</th> -->
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($riwayat as $r)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td>
                                                                                <span class="badge badge-secondary">{{ $r->jam ? \Carbon\Carbon::parse($r->jam)->format('H:i') : '-' }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span class="badge {{ $r->kebersihan_ruangan === 'OK' ? 'badge-success' : 'badge-danger' }} mr-1">
                                                                                    <i class="fas {{ $r->kebersihan_ruangan === 'OK' ? 'fa-check' : 'fa-times' }}"></i> Ruangan
                                                                                </span>
                                                                                <span class="badge {{ $r->kebersihan_karyawan === 'OK' ? 'badge-success' : 'badge-danger' }}">
                                                                                    <i class="fas {{ $r->kebersihan_karyawan === 'OK' ? 'fa-check' : 'fa-times' }}"></i> Karyawan
                                                                                </span>
                                                                            </td>
                                                                            <td><strong class="text-primary">{{ $r->pemeriksaan_suhu_ruang ?? '-' }}</strong></td>
                                                                            <td class="text-muted">{{ $r->user->name ?? '-' }}</td>
                                                                            <!-- <td>
                                                                                <span class="badge {{ $r->approved_by_qc ? 'badge-success' : 'badge-light border' }}">QC</span>
                                                                                <span class="badge {{ $r->approved_by_produksi ? 'badge-primary' : 'badge-light border' }}">PRD</span>
                                                                                <span class="badge {{ $r->approved_by_spv ? 'badge-dark' : 'badge-light border' }}">SPV</span>
                                                                            </td> -->
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
                                                <td colspan="10" class="text-center py-5">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted mb-0">Tidak ada data Pemeriksaan Kondisi Area Kerja ditemukan.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Menampilkan Navigasi Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $areaProses->appends(request()->query())->links('pagination::bootstrap-4') }}
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
                    <i class="fas fa-file-pdf"></i> Export PDF Area Proses
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('area-proses.bulk-export-pdf') }}" method="POST" target="_blank">
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
                        <select class="form-control" name="shift">
                            <option value="">Semua Shift</option>
                            @foreach($shifts ?? [] as $shift)
                                <option value="{{ $shift->shift }}">{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Area (Opsional)</label>
                        <select class="form-control" name="area">
                            <option value="">Semua Area</option>
                            @foreach($areas ?? [] as $area)
                                <option value="{{ $area->area }}">{{ $area->area }}</option>
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
                               value="QF 25/00"
                               required
                               placeholder="Masukkan kode form">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" formaction="{{ route('area-proses.bulk-export-excel') }}" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('area-proses.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="importExcelModalLabel"><i class="fas fa-file-import mr-2"></i> Import Data Area Proses</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0">
                        <i class="fas fa-info-circle mr-2"></i> Gunakan template resmi untuk format data yang valid.
                        <a href="{{ route('area-proses.download-template') }}" class="alert-link" target="_blank">Download template di sini</a>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted mb-2">Pilih File Excel</label>
                        <div class="custom-file shadow-sm">
                            <input type="file" class="custom-file-input" id="excelFile" name="file" accept=".xlsx,.xls" required>
                            <label class="custom-file-label" for="excelFile">Pilih file...</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold"><i class="fas fa-upload mr-2"></i> Import Sekarang</button>
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

    /* Riwayat per jam */
    .btn-xs { padding: 0.15rem 0.4rem; font-size: 0.75rem; line-height: 1.4; border-radius: 0.2rem; }
    .riwayat-row, .riwayat-row:hover { background-color: transparent !important; }
    .riwayat-box {
        background-color: #f8fafc;
        border: 1px solid #cfe6f5;
        border-left: 3px solid #17a2b8;
        border-radius: 6px;
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
    // Toggle riwayat per jam
    $(document).on('click', '.toggle-riwayat-btn', function() {
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
                    url: '{{ route("area-proses.bulk-approve-selected") }}',
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

    // Initialize file input label
    $('#excelFile').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').text(fileName);
    });
});
</script>
@endpush