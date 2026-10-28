@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-history text-purple"></i> Release Produk Finish Good</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('release-produk.index') }}">Release Produk</a></li>
                        <!-- <li class="breadcrumb-item active">Audit</li> -->
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-purple card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-bold text-purple"><i class="fas fa-shield-alt mr-2"></i> Release Produk Finish Good</h3>
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
                                <a href="{{ route('release-produk.index') }}" class="btn btn-default btn-sm shadow-sm">
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

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <form action="{{ route('release-produk.audit') }}" method="GET">
                                        <div class="input-group shadow-sm" style="width: 350px; border-radius: 20px; overflow: hidden;">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                            </div>
                                            <input type="text" name="search" class="form-control border-left-0" placeholder="Cari Produk..." value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-purple" type="submit"><i class="fas fa-search"></i></button>
                                                @if(request('search'))
                                                    <a href="{{ route('release-produk.audit') }}" class="btn btn-outline-danger"><i class="fas fa-times"></i></a>
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
                                <table class="table table-hover border-bottom mb-0" style="white-space:nowrap;">
                                    <thead class="bg-light text-uppercase small font-weight-bold border-top">
                                        <tr>
                                            <th class="text-center py-3" style="width: 50px;">No</th>
                                            <th class="py-3 text-center">Shift</th>
                                            <th class="py-3 text-center">Tanggal</th>
                                            <th class="py-3 text-center">Jam</th>
                                            <th class="py-3 text-left">Produk</th>
                                            <th class="py-3 text-center">Suhu IQF / Produk</th>
                                            <th class="py-3 text-center">Status</th>
                                            <th class="py-3 text-center" style="width: 150px;">Status Appr</th>
                                            <th class="py-3 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($records as $item)
                                        <tr class="item-row">
                                            <td class="text-center align-middle">{{ $records->firstItem() + $loop->index }}</td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-outline-info">Shift {{ $item->shift->shift ?? '-' }}</span>
                                            </td>
                                            <td class="text-center align-middle">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</td>
                                            <td class="text-center align-middle">{{ $item->jam ?? '-' }}</td>
                                            <td class="align-middle">
                                                <div class="font-weight-bold">{{ $item->produk->nama_produk ?? '-' }}</div>
                                            </td>
                                            <td class="text-center align-middle">{{ $item->suhu_iqf }} ℃ / {{ $item->suhu_produk }} ℃</td>
                                            <td class="text-center align-middle">
                                                @if($item->status == 'Release')
                                                    <span class="badge badge-success">Release</span>
                                                @else
                                                    <span class="badge badge-danger">Tidak Release</span>
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
                                                <x-action-buttons :item="$item" route-prefix="release-produk" :show-print="true"/>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">Belum ada data audit. Silakan copy data dari halaman operasional.</p>
                                                </div>
                                            </td>
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
    .item-row td { vertical-align: middle !important; padding: 0.75rem 1rem !important; font-size: 0.85rem; }
</style>

<!-- Modal Filter Cetak PDF Audit -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('release-produk.bulk-export-pdf') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="exportPdfModalLabel"><i class="fas fa-file-pdf mr-2"></i> Cetak PDF Berdasarkan Filter</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" required value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih Shift</label>
                        <select name="shift_id" class="form-control">
                            <option value="">Semua Shift</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">Shift {{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-uppercase text-muted">Pilih Produk</label>
                        <select name="produk_id" class="form-control select2" style="width: 100%;">
                            <option>Pilih Produk</option>
                            @foreach($produks as $produk)
                                <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-uppercase text-muted">Kode Form</label>
                        <input type="text" name="kode_form" class="form-control" placeholder="Contoh: F-PRO-01-01" value="QF 37/00">
                    </div>
                    <input type="hidden" name="is_audit" value="1">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold"><i class="fas fa-print mr-2"></i> Cetak PDF</button>
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
    });
</script>
@endpush
@endsection