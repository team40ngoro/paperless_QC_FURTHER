@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-archive text-primary"></i> Detail Verifikasi Proses Rebox</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('rebox.index') }}">Rebox</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-bold"><i class="fas fa-info-circle text-primary mr-2"></i> Informasi Detail</h3>
                            <div class="card-tools">
                                <a href="{{ route('rebox.index') }}" class="btn btn-default btn-sm shadow-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-calendar-alt mr-2"></i> Data Waktu & Shift</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th style="width: 40%">Tanggal</th>
                                            <td>: {{ $rebox->tanggal ? \Carbon\Carbon::parse($rebox->tanggal)->format('d F Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shift</th>
                                            <td>: <span class="badge badge-primary">Shift {{ $rebox->shift->shift ?? '-' }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Jam</th>
                                            <td>: {{ $rebox->jam ? \Carbon\Carbon::parse($rebox->jam)->format('H:i') : '-' }} WIB</td>
                                        </tr>
                                    </table>

                                    <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-box mr-2"></i> Data Produk</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th style="width: 40%">Nama Produk</th>
                                            <td>: <strong>{{ $rebox->nama_produk ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Kode Produksi</th>
                                            <td>: <code>{{ $rebox->kode_produksi ?? '-' }}</code></td>
                                        </tr>
                                        <tr>
                                            <th>Best Before</th>
                                            <td>: {{ $rebox->best_before ? \Carbon\Carbon::parse($rebox->best_before)->format('d/m/Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Isi / Jumlah</th>
                                            <td>: {{ $rebox->isi_jumlah ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-check-circle mr-2"></i> Status Pemeriksaan</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th style="width: 40%">Labelisasi</th>
                                            <td>: <span class="badge {{ $rebox->labelisasi == 'OK' ? 'badge-success' : 'badge-warning' }}">{{ $rebox->labelisasi ?? '-' }}</span></td>
                                        </tr>
                                    </table>

                                    <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-user-check mr-2"></i> Verifikasi</h5>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="p-2 border rounded {{ $rebox->approved_by_qc ? 'bg-success-light' : 'bg-light' }}">
                                                <small class="d-block text-muted">QC</small>
                                                <i class="fas {{ $rebox->approved_by_qc ? 'fa-check-circle text-success' : 'fa-clock text-muted' }} fa-lg my-2"></i>
                                                <div class="small">{{ $rebox->qcApprover->name ?? 'Belum' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 border rounded {{ $rebox->approved_by_produksi ? 'bg-primary-light' : 'bg-light' }}">
                                                <small class="d-block text-muted">Produksi</small>
                                                <i class="fas {{ $rebox->approved_by_produksi ? 'fa-check-circle text-primary' : 'fa-clock text-muted' }} fa-lg my-2"></i>
                                                <div class="small">{{ $rebox->produksiApprover->name ?? 'Belum' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 border rounded {{ $rebox->approved_by_spv ? 'bg-dark-light' : 'bg-light' }}">
                                                <small class="d-block text-muted">SPV</small>
                                                <i class="fas {{ $rebox->approved_by_spv ? 'fa-check-circle text-dark' : 'fa-clock text-muted' }} fa-lg my-2"></i>
                                                <div class="small">{{ $rebox->spvApprover->name ?? 'Belum' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="small text-muted">
                                    <!-- Input oleh: <strong>{{ $rebox->createdBy->name ?? '-' }}</strong> pada {{ $rebox->created_at->format('d/m/Y H:i') }} -->
                                </div>
                                <div class="btn-group">
                                    @if(auth()->user()->hasPermissionTo('edit-rebox'))
                                        <a href="{{ route('rebox.edit', $rebox->uuid) }}" class="btn btn-warning shadow-sm">
                                            <i class="fas fa-edit mr-1"></i> Edit Data
                                        </a>
                                    @endif
                                    <a href="{{ route('rebox.logs', $rebox->uuid) }}" class="btn btn-info ml-2 shadow-sm">
                                        <i class="fas fa-history mr-1"></i> Lihat Log
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .bg-success-light { background-color: rgba(40, 167, 69, 0.1); border-color: rgba(40, 167, 69, 0.2) !important; }
    .bg-primary-light { background-color: rgba(0, 123, 255, 0.1); border-color: rgba(0, 123, 255, 0.2) !important; }
    .bg-dark-light { background-color: rgba(52, 58, 64, 0.1); border-color: rgba(52, 58, 64, 0.2) !important; }
</style>
@endsection
