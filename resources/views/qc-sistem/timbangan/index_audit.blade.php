@extends('layouts.app')

@section('title', 'Data Timbangan - Audit')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-history text-purple"></i> Data Timbangan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('timbangan.index') }}">Timbangan</a></li>
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
                    <div class="card card-purple card-outline shadow-sm border-purple">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-bold text-purple"><i class="fas fa-shield-alt mr-2"></i> Data Timbangan</h3>

                            <div class="card-tools">
                                @if(auth()->user()->id_role !== 6)
                                    <span class="badge p-2 mr-2" style="background-color: #6f42c1; color: white;">MODE AUDIT AKTIF</span>
                                    <a href="{{ route('timbangan.index') }}" class="btn btn-default btn-sm shadow-sm">
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
                                        <form action="{{ route('timbangan.audit') }}" method="GET" class="d-inline">
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
                                    <form action="{{ route('timbangan.audit') }}" method="GET">
                                        @if(request('per_page'))
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 5) }}">
                                        @endif
                                        <div class="input-group shadow-sm">
                                            <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari Timbangan" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-purple text-white" style="background-color: #6f42c1;" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(request('search'))
                                                    <a href="{{ route('timbangan.audit', ['per_page' => request('per_page', 5)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
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
                                            <th class="py-3 text-center">Shift</th>
                                            <th class="py-3 text-center">Tanggal</th>
                                            <th class="py-3 text-center">Jam</th>
                                            <th class="py-3 text-center">Nama &amp; Kode Timbangan</th>
                                            <!-- <th class="py-3 text-center">Berat (Kg)</th>
                                            <th class="py-3 text-center">Target (Kg)</th> -->
                                            <th class="py-3 text-center">Dibuat Oleh</th>
                                            <th class="py-3 text-center" style="width: 100px;">Status Appr</th>
                                            <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @php
                                            $no = 1;
                                        @endphp

                                        @forelse($records as $item)
                                            <tr class="item-row">
                                                <td class="text-center align-middle">{{ $no }}</td>
                                                <td class="text-center align-middle">{{ $item->shift->shift ?? '-' }}</td>
                                                <td class="text-center align-middle">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y') : '-' }}</td>
                                                <td class="text-center align-middle">{{ $item->jam ?? '-' }}</td>
                                                <td class="text-center align-middle">
                                                    @php
                                                        $rowEntries = is_array($item->entries) ? $item->entries : [];
                                                        $scaleEntries = collect($rowEntries)->filter(fn ($entry, $key) => is_numeric($key) && is_array($entry) && (!empty($entry['jenis']) || !empty($entry['kode_timbangan'])));
                                                        if ($scaleEntries->isEmpty() && (!empty($item->jenis) || !empty($item->kode_timbangan))) {
                                                            $scaleEntries = collect([['jenis' => $item->jenis, 'kode_timbangan' => $item->kode_timbangan]]);
                                                        }
                                                        $bagEntries = collect($rowEntries['checkweigher_bag'] ?? [])->filter(fn ($entry) => is_array($entry) && array_filter($entry, fn ($value) => $value !== null && $value !== ''));
                                                        $boxEntries = collect($rowEntries['checkweigher_box'] ?? [])->filter(fn ($entry) => is_array($entry) && array_filter($entry, fn ($value) => $value !== null && $value !== ''));
                                                    @endphp
                                                    @if($scaleEntries->isNotEmpty() || $bagEntries->isNotEmpty() || $boxEntries->isNotEmpty())
                                                        <button type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#timbangan-detail-{{ $item->uuid }}">
                                                            <i class="fas fa-eye mr-1"></i> Detail
                                                        </button>
                                                        <div class="mt-1">
                                                            @foreach($scaleEntries as $entry)
                                                                <span class="badge badge-primary">{{ $entry['jenis'] ?? 'Timbangan' }}</span>
                                                            @endforeach
                                                            @if($bagEntries->isNotEmpty())<span class="badge badge-info">Checkweigher Bag</span>@endif
                                                            @if($boxEntries->isNotEmpty())<span class="badge badge-secondary">Checkweigher Box</span>@endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <!-- <td class="text-center align-middle">{{ $item->berat_akhir ?? '-' }}</td> -->
                                                <!-- <td class="text-center align-middle">{{ $item->target_berat ?? '-' }}</td> -->
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
                                                <!-- <td class="text-center align-middle" style="white-space: nowrap;">
                                                    <a href="{{ route('timbangan.show', $item->uuid) }}" class="btn btn-sm btn-info shadow-sm tombol" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td> -->
                                                <td class="text-center align-middle" style="white-space: nowrap;">
                                                    <x-action-buttons :item="$item" route-prefix="timbangan" :show-history="auth()->user()->id_role != 6" />
                                                </td>
                                            </tr>
                                            @php
                                                $no++;
                                            @endphp
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5 text-muted">
                                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                                    <p>Tidak ada data audit</p>
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>

                            @foreach($records as $detailItem)
                                @php
                                    $detailEntries = is_array($detailItem->entries) ? $detailItem->entries : [];
                                    $detailScales = collect($detailEntries)->filter(fn ($entry, $key) => is_numeric($key) && is_array($entry) && (!empty($entry['jenis']) || !empty($entry['kode_timbangan'])));
                                    if ($detailScales->isEmpty() && (!empty($detailItem->jenis) || !empty($detailItem->kode_timbangan))) {
                                        $detailScales = collect([['jenis' => $detailItem->jenis, 'kode_timbangan' => $detailItem->kode_timbangan, 'area' => $detailItem->area, 'gram' => $detailItem->gram, 'hasil_pengecekan' => $detailItem->hasil_pengecekan, 'hasil_verifikasi_500' => $detailItem->hasil_verifikasi_500, 'hasil_verifikasi_1000' => $detailItem->hasil_verifikasi_1000, 'hasil_verifikasi_10000' => $detailItem->hasil_verifikasi_10000]]);
                                    }
                                    $detailBags = collect($detailEntries['checkweigher_bag'] ?? [])->filter(fn ($entry) => is_array($entry) && array_filter($entry, fn ($value) => $value !== null && $value !== ''));
                                    $detailBoxes = collect($detailEntries['checkweigher_box'] ?? [])->filter(fn ($entry) => is_array($entry) && array_filter($entry, fn ($value) => $value !== null && $value !== ''));
                                @endphp
                                @if($detailScales->isNotEmpty() || $detailBags->isNotEmpty() || $detailBoxes->isNotEmpty())
                                    <div class="modal fade" id="timbangan-detail-{{ $detailItem->uuid }}" tabindex="-1" role="dialog" aria-labelledby="timbangan-detail-title-{{ $detailItem->uuid }}" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title" id="timbangan-detail-title-{{ $detailItem->uuid }}">Rincian Pemeriksaan Timbangan</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    @if($detailScales->isNotEmpty())
                                                        <h6 class="font-weight-bold text-primary">Timbangan</h6>
                                                        <div class="table-responsive mb-4"><table class="table table-sm table-bordered"><thead class="thead-light"><tr><th>Nama Timbangan</th><th>Kode</th><th>Area</th><th>Target (g)</th><th>Hasil 500g</th><th>Hasil 1000g</th><th>Hasil 10000g</th></tr></thead><tbody>@foreach($detailScales as $entry)<tr><td>{{ $entry['jenis'] ?? '-' }}</td><td>{{ $entry['kode_timbangan'] ?? '-' }}</td><td>{{ $entry['area'] ?? '-' }}</td><td>{{ $entry['gram'] ?? '-' }}</td><td>{{ $entry['hasil_verifikasi_500'] ?? '-' }}</td><td>{{ $entry['hasil_verifikasi_1000'] ?? '-' }}</td><td>{{ $entry['hasil_verifikasi_10000'] ?? '-' }}</td></tr>@endforeach</tbody></table></div>
                                                    @endif
                                                    @if($detailBags->isNotEmpty())
                                                        <h6 class="font-weight-bold text-info">Checkweigher Bag</h6>
                                                        <div class="table-responsive mb-4"><table class="table table-sm table-bordered"><thead class="thead-light"><tr><th>Line</th><th>Nama Produk</th><th>Gramase</th><th>Waktu</th><th>Sample 1</th><th>Sample 2</th><th>Under</th><th>Over</th></tr></thead><tbody>@foreach($detailBags as $entry)<tr><td>{{ $entry['line'] ?? '-' }}</td><td>{{ $entry['nama_produk'] ?? '-' }}</td><td>{{ $entry['gramase'] ?? '-' }}</td><td>{{ $entry['waktu_pemeriksaan'] ?? '-' }}</td><td>{{ $entry['perbandingan_1'] ?? '-' }}</td><td>{{ $entry['perbandingan_2'] ?? '-' }}</td><td>{{ $entry['under'] ?? '-' }}</td><td>{{ $entry['over'] ?? '-' }}</td></tr>@endforeach</tbody></table></div>
                                                    @endif
                                                    @if($detailBoxes->isNotEmpty())
                                                        <h6 class="font-weight-bold text-secondary">Checkweigher Box</h6>
                                                        <div class="table-responsive"><table class="table table-sm table-bordered"><thead class="thead-light"><tr><th>Line</th><th>Nama Produk</th><th>Gramase</th><th>Waktu</th><th>Kurang 1 Pack</th><th>Lebih 1 Pack</th><th>Double Item</th></tr></thead><tbody>@foreach($detailBoxes as $entry)<tr><td>{{ $entry['line'] ?? '-' }}</td><td>{{ $entry['nama_produk'] ?? '-' }}</td><td>{{ $entry['gramase'] ?? '-' }}</td><td>{{ $entry['waktu_pemeriksaan'] ?? '-' }}</td><td>{{ $entry['kurang_1_pack'] ?? '-' }}</td><td>{{ $entry['lebih_1_pack'] ?? '-' }}</td><td>{{ $entry['double_item'] ?? '-' }}</td></tr>@endforeach</tbody></table></div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

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

<!-- Modal Filter Cetak PDF -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('timbangan.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="is_audit" value="1">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="exportPdfModalLabel"><i class="fas fa-file-pdf mr-2"></i> Cetak PDF Data Audit</h5>
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
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}">Shift {{ $shift->shift }}</option>
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
                            <input type="text" name="kode_form" class="form-control" placeholder="Contoh: QF 07/00" value="QF 07/00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold"><i class="fas fa-print mr-2"></i> Cetak PDF</button>
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
