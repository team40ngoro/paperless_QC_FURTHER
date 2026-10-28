@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-history text-purple"></i> Verifikasi Pembuatan Sample</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pembuatan-sample.index') }}">Pembuatan Sample</a></li>
                        <li class="breadcrumb-item active">Audit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-purple card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-bold text-purple"><i class="fas fa-shield-alt mr-2"></i> Verifikasi Pembuatan Sample</h3>
                            <div class="card-tools">
                                <div class="btn-group mr-2">
                                    <button type="button" class="btn btn-danger btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown">
                                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                                    </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#exportPdfModal">
                                        <i class="fas fa-filter text-danger mr-2"></i> Berdasarkan Filter
                                    </a>
                                </div>
                            </div>
                            @if(auth()->user()->id_role !== 6)
                            <span class="badge badge-purple p-2 mr-2">MODE AUDIT AKTIF</span>
                            <a href="{{ route('pembuatan-sample.index') }}" class="btn btn-default btn-sm shadow-sm">
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

                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-2 mb-0 text-muted small font-weight-bold">Tampilkan:</label>
                                        <form action="{{ route('pembuatan-sample.audit') }}" method="GET" class="d-inline">
                                            @if(request('search'))
                                                <input type="hidden" name="search" value="{{ request('search') }}">
                                            @endif
                                            <select name="per_page" class="form-control form-control-sm shadow-sm" style="width: 80px;" onchange="this.form.submit()">
                                                <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                                                <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                                <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                                                <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-4 offset-md-4">
                                    <form action="{{ route('pembuatan-sample.audit') }}" method="GET">
                                        @if(request('per_page'))
                                            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                                        @endif
                                        <div class="input-group shadow-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Cari Produk atau kode produksi..." value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search mr-1"></i> Cari
                                                </button>
                                                @if(request('search'))
                                                    <a href="{{ route('pembuatan-sample.audit', ['per_page' => request('per_page', 10)]) }}" class="btn btn-outline-danger" title="Hapus Pencarian">
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
                            @include('partials.date-range-filter')

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped text-center" style="white-space:nowrap;">
                                    <thead class="bg-light text-uppercase small font-weight-bold border-top">
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Rincian Sample</th>
                                            <th>Status Appr</th>
                                            <th style="width: 150px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($records as $item)
                                        <tr>
                                            <td>{{ $records->firstItem() + $loop->index }}</td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}
                                                </small>
                                            </td>
                                            <td class="text-left">
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <span class="badge badge-outline-secondary mr-2">
                                                            {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                                        </span>
                                                        <strong>{{ $item->produk->nama_produk ?? '-' }}</strong>
                                                        <span class="mx-2 text-muted">|</span>
                                                        <span class="text-info">{{ $item->jenis_sample }}</span>
                                                        <span class="mx-2 text-muted">|</span>
                                                        <small class="text-muted">Kode: {{ $item->kode_produksi }}</small>
                                                        <!-- <span class="mx-2 text-muted">|</span>
                                                        <small>Jumlah: <b>{{ $item->jumlah }}</b> ({{ rtrim(rtrim(number_format($item->berat, 2), '0'), '.') }}g)</small> -->
                                                        @if($item->items_json && count($item->items_json) > 0)
                                                            <span class="mx-2 text-muted">|</span>
                                                            <button type="button" 
                                                                class="btn btn-xs btn-outline-info toggle-kode-batch-btn" 
                                                                data-target="kode-batch-{{ $item->uuid }}"
                                                                title="Lihat Detail Kode Batch">
                                                                <i class="fas fa-barcode mr-1"></i> 
                                                                {{ count($item->items_json) }} Kode Batch
                                                                <i class="fas fa-chevron-down ml-1 toggle-icon"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                                {{-- Collapse: Detail Kode Batch --}}
                                                @if($item->items_json && count($item->items_json) > 0)
                                                <div class="kode-batch-collapse mt-2" id="kode-batch-{{ $item->uuid }}" style="display:none;">
                                                    <div class="card card-body p-2 bg-light border-info" style="border-left: 3px solid #17a2b8 !important;">
                                                        <small class="text-muted font-weight-bold mb-2 d-block">
                                                            <i class="fas fa-list mr-1"></i> Rincian Kode Batch
                                                        </small>
                                                        <table class="table table-sm table-bordered mb-0 bg-white" style="font-size: 0.8rem;">
                                                            <thead class="thead-dark">
                                                                <tr>
                                                                    <th class="text-center" style="width:35px;">No</th>
                                                                    <th>Produk</th>
                                                                    <th>Kode Produksi</th>
                                                                    <th>Expired</th>
                                                                    <th class="text-center">Jumlah</th>
                                                                    <th class="text-center">Berat Sampel (g)</th>
                                                                    <th>Jenis Sample</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($item->items_json as $batchIdx => $batch)
                                                                <tr>
                                                                    <td class="text-center">{{ $batchIdx + 1 }}</td>
                                                                    <td>{{ \App\Models\JenisProduk::find($batch['id_produk'])->nama_produk ?? '-' }}</td>
                                                                    <td><code class="text-danger">{{ $batch['kode_produksi'] ?? '-' }}</code></td>
                                                                    <td>{{ $batch['tanggal_expired'] ?? '-' }}</td>
                                                                    <td class="text-center"><b>{{ $batch['jumlah'] ?? '-' }}</b></td>
                                                                    <td class="text-center">{{ $batch['berat'] ?? '-' }}</td>
                                                                    <td><span class="badge badge-secondary text-uppercase">{{ $batch['jenis_sample'] ?? '-' }}</span></td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                @endif
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
                                                <x-action-buttons :item="$item" route-prefix="pembuatan-sample" :isAuditPage="true" />
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">Tidak ada data ditemukan</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $records->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                            @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                Belum ada data audit. Silakan copy data dari halaman operasional.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .card-purple.card-outline { border-top: 3px solid #6f42c1; }
    .text-purple { color: #6f42c1 !important; }
    .badge-purple { background-color: #6f42c1; color: white; }
    .btn-purple { background-color: #6f42c1; color: white; border-color: #6f42c1; }
    .btn-purple:hover { background-color: #5a32a3; color: white; border-color: #5a32a3; }
    .btn-xs { padding: 0.15rem 0.4rem; font-size: 0.75rem; line-height: 1.4; border-radius: 0.2rem; }
    .badge-outline-secondary { border: 1px solid #6c757d; color: #6c757d; background: transparent; }
    .kode-batch-collapse { white-space: normal; }
</style>

<!-- Modal Export PDF Audit -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('pembuatan-sample.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="exportPdfModalLabel"><i class="fas fa-file-pdf mr-2"></i> Cetak PDF Berdasarkan Filter</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" class="form-control" required value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih Shift</label>
                        <select name="id_shift" class="form-control">
                            <option value="">Semua Shift</option>
                            @foreach($cachedShifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih Produk</label>
                        <select name="id_produk" class="form-control select2" style="width: 100%;">
                            <option value="">Semua Produk</option>
                            @foreach($cachedProduk as $produk)
                                <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-uppercase text-muted">Kode Form</label>
                        <input type="text" name="kode_form" class="form-control" placeholder="Contoh: QF 17/00" value="QF 17/00">
                    </div>
                    <input type="hidden" name="is_audit" value="1">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" formaction="{{ route('pembuatan-sample.bulk-export-pdf') }}" class="btn btn-danger px-4 font-weight-bold"><i class="fas fa-file-pdf mr-2"></i> Export PDF</button>
                    <button type="submit" formaction="{{ route('pembuatan-sample.bulk-export-excel') }}" class="btn btn-success px-4 font-weight-bold"><i class="fas fa-file-excel mr-2"></i> Export Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            dropdownParent: $('#exportPdfModal')
        });

        // Toggle kode batch collapse
        $(document).on('click', '.toggle-kode-batch-btn', function() {
            const targetId = $(this).data('target');
            const $target = $('#' + targetId);
            const $icon = $(this).find('.toggle-icon');

            $target.slideToggle(200, function() {
                if ($target.is(':visible')) {
                    $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                } else {
                    $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                }
            });
        });
    });
</script>
@endpush
@endsection