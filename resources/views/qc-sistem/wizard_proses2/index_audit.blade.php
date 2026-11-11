@extends('layouts.app')

@section('title', 'Wizard Proses 2 - Audit')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-history text-purple"></i> Verifikasi Proses Frying Roasted Produk</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Wizard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('wizard.proses2.index') }}">Proses 2</a></li>
                        <li class="breadcrumb-item active">Audit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-purple card-outline shadow-sm border-purple">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-bold text-purple"><i class="fas fa-shield-alt mr-2"></i> Data Audit Verifikasi Proses Frying Roasted Produk</h3>

                            <div class="card-tools">
                                @if(auth()->user()->id_role !== 6)
                                    <span class="badge p-2 mr-2" style="background-color: #6f42c1; color: white;">MODE AUDIT AKTIF</span>
                                    <a href="{{ route('wizard.proses2.index') }}" class="btn btn-default btn-sm shadow-sm">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Operasional
                                    </a>
                                @endif
                                <button type="button" class="btn btn-danger btn-sm shadow-sm" data-toggle="modal" data-target="#exportPdfModal">
                                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                                </button>
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
                                        <form action="{{ route('wizard.proses2.audit') }}" method="GET" class="d-inline">
                                            @if(request('search'))
                                                <input type="hidden" name="search" value="{{ request('search') }}">
                                            @endif
                                            <select id="perPageSelect" name="per_page" class="form-control form-control-sm shadow-sm" style="width: 80px;" onchange="this.form.submit()">
                                                @foreach([5, 10, 25, 50] as $n)
                                                    <option value="{{ $n }}" {{ (int)($perPage ?? 5) === $n ? 'selected' : '' }}>{{ $n }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-4 offset-md-4">
                                    <form action="{{ route('wizard.proses2.audit') }}" method="GET">
                                        @if(request('per_page'))
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                        @endif
                                        <div class="input-group shadow-sm">
                                            <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari data audit..." value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-purple text-white" style="background-color: #6f42c1;" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(request('search'))
                                                    <a href="{{ route('wizard.proses2.audit', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
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

                            <!-- Data Table -->
                            @include('partials.date-range-filter')

                            <div class="table-responsive">
                                <table id="mainTable" class="table table-hover border-bottom mb-0" style="white-space: nowrap;">
                                    <thead class="bg-light text-uppercase small font-weight-bold border-top">
                                        <tr>
                                            <th class="text-center py-3" style="width: 50px;">No</th>
                                            <th class="py-3 text-center">Tanggal</th>
                                            <th class="py-3 text-center">Shift</th>
                                            <th class="py-3 text-left">Kode Produksi</th>
                                            <th class="py-3 text-left">Nama Produk</th>
                                            <th class="py-3 text-center">Dibuat Oleh</th>
                                            <th class="py-3 text-center" style="width: 100px;">Status Appr</th>
                                            <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @php
                                            $no = 1;
                                        @endphp

                                        @forelse($records as $groupKey => $items)
                                            @forelse($items as $item)
                                            <tr class="item-row">
                                                <td class="text-center align-middle">{{ $no }}</td>
                                                <td class="text-center align-middle">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y') : '-' }}</td>
                                                <td class="text-center align-middle">{{ $item->shift->shift ?? '-' }}</td>
                                                <td class="text-left align-middle">{{ $item->penggorenganData->kode_produksi ?? '-' }}</td>
                                                <td class="text-left align-middle">
                                                    <div style="font-size: 0.85rem;">{{ $item->penggorenganData->produk->nama_produk ?? ($item->prosesRoastingFanData->produk->nama_produk ?? ($item->hasilProsesRoastingData->produk->nama_produk ?? '-')) }}</div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div style="font-size: 0.8rem;">{{ $item->user->name ?? '-' }}</div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="d-flex flex-column align-items-center" style="gap: 2px;">
                                                        @if($item->approved_by_qc)
                                                            <span class="badge badge-success w-100" style="font-size: 0.65rem;"><i class="fas fa-check"></i> QC</span>
                                                        @else
                                                            <span class="badge badge-light w-100" style="font-size: 0.65rem; color: #999;">QC</span>
                                                        @endif
                                                        @if($item->approved_by_produksi)
                                                            <span class="badge badge-primary w-100" style="font-size: 0.65rem;"><i class="fas fa-check"></i> PRD</span>
                                                        @else
                                                            <span class="badge badge-light w-100" style="font-size: 0.65rem; color: #999;">PRD</span>
                                                        @endif
                                                        @if($item->approved_by_spv)
                                                            <span class="badge badge-dark w-100" style="font-size: 0.65rem;"><i class="fas fa-check"></i> SPV</span>
                                                        @else
                                                            <span class="badge badge-light w-100" style="font-size: 0.65rem; color: #999;">SPV</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                        <x-action-buttons
                                                            :item="$item"
                                                            route-prefix="wizard.proses2"
                                                            permission-prefix="wizard-proses2"
                                                            :show-history="false"
                                                            :show-view="true"
                                                            :show-print="true"
                                                        >
                                                            <!-- {{-- Tombol Collapse Rincian --}}
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-info btn-rincian-toggle"
                                                                data-target="#rincian-{{ $item->uuid }}"
                                                                title="Lihat Rincian Langkah"
                                                                style="border-radius:6px;">
                                                                <i class="fas fa-list-ul"></i>
                                                            </button> -->
                                                        </x-action-buttons>
                                                        <!-- <x-audit-dropdown :item="$item" route-prefix="wizard.proses1" permission-prefix="wizard-proses1" :uuid="$item->uuid" /> -->
                                                    </td>
                                            </tr>
                                            @php
                                                $no++;
                                            @endphp
                                            @empty
                                            @endforelse
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5 text-muted">
                                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                                    <p>Tidak ada data audit</p>
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 d-flex justify-content-between align-items-center" id="paginationWrapper">
                                <div id="paginationInfo" class="text-muted small">
                                    Menampilkan {{ $records->firstItem() ?? 0 }} sampai {{ $records->lastItem() ?? 0 }} dari {{ $records->total() }} data
                                </div>
                                <div id="paginationLinks">
                                    {{ $records->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Filter Cetak PDF / Excel -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('wizard.proses2.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="exportPdfModalLabel"><i class="fas fa-file-pdf mr-2"></i> Cetak PDF / Excel Audit Proses 2 Roasting</h5>
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
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih Shift</label>
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
                            <select name="produk_id" class="form-control" id="pdf_produk_filter_audit">
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
                            <input type="text" name="kode_form" class="form-control" value="QF 10/00" required>
                        </div>
                    </div>
                    <input type="hidden" name="is_audit" value="1">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" formaction="{{ route('wizard.proses2.bulk-export-excel') }}" class="btn btn-success px-4 font-weight-bold"><i class="fas fa-file-excel mr-2"></i> Export Excel</button>
                    <button type="submit" formaction="{{ route('wizard.proses2.bulk-export-pdf') }}" class="btn btn-danger px-4 font-weight-bold"><i class="fas fa-print mr-2"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
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
