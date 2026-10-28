@extends('layouts.app')

@section('title', 'Audit: Pemeriksaan Proses Produksi')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-tasks text-info"></i> Pemeriksaan Proses Produksi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-proses-produksi.index') }}">Pemeriksaan Proses Produksi</a></li>
                        <li class="breadcrumb-item active">Audit</li>
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
                                    <i class="fas fa-list text-info mr-2"></i> Data Audit Pemeriksaan Proses Produksi
                                </h3>
                                <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                                    <button type="button" class="btn btn-danger btn-sm shadow-sm" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-file-pdf mr-1"></i> Cetak PDF Filter
                                    </button>
                                    @if(auth()->user()->id_role != 6)
                                        <span class="badge badge-purple p-2">MODE AUDIT AKTIF</span>
                                        <a href="{{ route('pemeriksaan-proses-produksi.index') }}" class="btn btn-default btn-sm shadow-sm">
                                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Operasional
                                        </a>
                                    @endif
                                </div>
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

                            @if(auth()->user()->id_role != 6)
                                <div class="alert alert-info border-0 shadow-sm mb-4">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Info:</strong> Data di halaman ini adalah hasil copy dari data operasional untuk keperluan audit. Perubahan di sini <strong>tidak akan merubah</strong> data operasional asli.
                                </div>
                            @endif

                            <!-- Search and Filter Controls -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                        <form action="{{ route('pemeriksaan-proses-produksi.audit') }}" method="GET" class="d-inline">
                                            @if(request('search'))
                                                <input type="hidden" name="search" value="{{ request('search') }}">
                                            @endif
                                            <select name="per_page" class="form-control form-control-sm shadow-sm" style="width: 80px;" onchange="this.form.submit()">
                                                @foreach([5, 10, 25, 50] as $n)
                                                    <option value="{{ $n }}" {{ (int)($perPage ?? 5) === $n ? 'selected' : '' }}>{{ $n }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-4 offset-md-4">
                                    <form action="{{ route('pemeriksaan-proses-produksi.audit') }}" method="GET">
                                        @if(request('per_page'))
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                        @endif
                                        <div class="input-group shadow-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Cari uraian permasalahan..." value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(request('search'))
                                                    <a href="{{ route('pemeriksaan-proses-produksi.audit', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            @include('partials.date-range-filter')

                            <div class="table-responsive">
                                <table id="mainTable" class="table table-hover border-bottom mb-0" style="white-space: nowrap;">
                                    <thead class="bg-light text-uppercase small font-weight-bold border-top">
                                        <tr>
                                            <th class="text-center py-3" style="width: 50px;">No</th>
                                            <th class="py-3 text-center">Tanggal</th>
                                            <th class="py-3 text-center">Jam</th>
                                            <th class="py-3 text-left">Area / Shift</th>
                                            <th class="py-3 text-left">Ketidaksesuaian / Uraian Permasalahan</th>
                                            <th class="py-3 text-left">Disposisi / Koreksi</th>
                                            <th class="py-3 text-center">Dibuat Oleh</th>
                                            <th class="py-3 text-center" style="width: 100px;">Status Appr</th>
                                            <th class="py-3 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @php $rowNumber = ($records->currentPage() - 1) * $records->perPage(); @endphp
                                        @forelse($records as $item)
                                            @php
                                                $rowNumber++;
                                                $isVerified = $item->approved_by_qc && $item->approved_by_produksi && $item->approved_by_spv;
                                                $tanggalFormatted = $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-';
                                                $jamFormatted = $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-';
                                                $approvalSteps = [
                                                    'QC' => $item->approved_by_qc,
                                                    'PROD' => $item->approved_by_produksi,
                                                    'SPV' => $item->approved_by_spv,
                                                ];
                                            @endphp
                                            <tr @class(['item-row', 'row-verified' => $isVerified])>
                                                <td class="text-center align-middle">{{ $rowNumber }}</td>
                                                <td class="text-center align-middle">{{ $tanggalFormatted }}</td>
                                                <td class="text-center align-middle">{{ $jamFormatted }}</td>
                                                <td class="align-middle">
                                                    <div class="font-weight-bold">{{ $item->area->area ?? '-' }}</div>
                                                    <div class="font-weight-bold">Shift {{ $item->shift->shift ?? '-' }}</div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="small">{{ $item->ketidaksesuaian_label ?? '-' }}</div>
                                                    <div class="text-muted small">{{ Str::limit($item->uraian_permasalahan, 40) }}</div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="small">{{ $item->disposisi_label ?? '-' }}</div>
                                                    <div class="text-muted small">{{ Str::limit($item->tindakan_koreksi, 40) }}</div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="small text-muted">{{ $item->user->name ?? '-' }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="status-badges d-flex flex-column align-items-center">
                                                        @foreach($approvalSteps as $label => $isApproved)
                                                            <span @class([
                                                                    'badge',
                                                                    'w-100',
                                                                    'badge-success' => $isApproved,
                                                                    'badge-light border' => !$isApproved,
                                                                    'mb-1' => !$loop->last,
                                                                  ])
                                                                  style="font-size: 0.7rem;">
                                                                <i class="fas {{ $isApproved ? 'fa-check' : 'fa-clock' }} mr-1"></i> {{ $label }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <x-action-buttons
                                                        :item="$item"
                                                        route-prefix="pemeriksaan-proses-produksi"
                                                        :show-view="true"
                                                        :show-print="false"
                                                        :show-history="auth()->user()->id_role != 6"
                                                        layout="horizontal"
                                                        class="tombol"
                                                    />
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-5">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted mb-0">Tidak ada data audit Pemeriksaan Proses Produksi ditemukan.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Menampilkan Navigasi Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $records->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Filter Cetak PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPdfModalLabel">
                    <i class="fas fa-file-pdf"></i> Export PDF Pemeriksaan Proses (Audit)
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('pemeriksaan-proses-produksi.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="is_audit" value="1">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="tanggal_awal">Tanggal Awal</label>
                            <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" value="{{ date('Y-m-01') }}">
                            @error('tanggal_awal')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tanggal_akhir">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="{{ date('Y-m-d') }}">
                            @error('tanggal_akhir')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Shift (Opsional)</label>
                        @php
                            $authUser = auth()->user();
                            $shiftOptions = $cachedShifts
                                ?? ($authUser->id_role == 1
                                    ? \App\Models\DataShift::all()
                                    : \App\Models\DataShift::where('id_plan', $authUser->id_plan)->get());
                        @endphp
                        <select class="form-control" name="shift_id">
                            <option value="">Semua Shift</option>
                            @foreach($shiftOptions as $shift)
                                <option value="{{ $shift->id }}">Shift {{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Area (Opsional)</label>
                        <select class="form-control" name="id_area">
                            <option value="">Semua Area</option>
                            @foreach($cachedAreas ?? \App\Models\InputArea::all() as $area)
                                <option value="{{ $area->id }}">{{ $area->area }}</option>
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
                               value="QF 24/00"
                               required
                               placeholder="Masukkan kode form">
                        @error('kode_form')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi:</strong> PDF akan berisi data audit sesuai rentang tanggal, shift, dan area yang dipilih.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" formaction="{{ route('pemeriksaan-proses-produksi.bulk-export-excel') }}" class="btn btn-success">
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

<style>
    .badge-purple {
        background-color: #6f42c1;
        color: white;
    }
    .row-verified {
        background-color: rgba(40, 167, 69, 0.05) !important;
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
    #mainTable thead th {
        border-bottom: 2px solid #e2e8f0;
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        vertical-align: middle !important;
    }
    .item-row td {
        vertical-align: middle !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.85rem;
    }
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
@endsection