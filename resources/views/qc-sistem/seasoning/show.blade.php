@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-pepper-hot text-primary"></i> Pemeriksaan Kedatangan Bahan Baku</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('seasoning.index') }}">Seasoning</a></li>
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
                                <a href="{{ route('seasoning.index') }}" class="btn btn-default btn-sm shadow-sm">
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
                                            <td>: {{ $seasoning->tanggal ? \Carbon\Carbon::parse($seasoning->tanggal)->format('d F Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shift</th>
                                            <td>: <span class="badge badge-primary">Shift {{ $seasoning->shift->shift ?? '-' }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Jam</th>
                                            <td>: {{ $seasoning->jam ? \Carbon\Carbon::parse($seasoning->jam)->format('H:i') : '-' }} WIB</td>
                                        </tr>
                                    </table>

                                    <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-mortar-pestle mr-2"></i> Data RM / Seasoning</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th style="width: 40%">Nama RM</th>
                                            <td>: <strong>{{ $seasoning->nama_rm ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Kode Produksi</th>
                                            <td>: <code>{{ $seasoning->kode_produksi ?? '-' }}</code></td>
                                        </tr>
                                        <tr>
                                            <th>Berat</th>
                                            <td>: {{ $seasoning->berat ?? '-' }} kg</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-check-circle mr-2"></i> Status Pemeriksaan</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th style="width: 40%">Sensori</th>
                                            <td>: 
                                                @if(in_array($seasoning->sensori, ['✔', '✓', 'OK', 'ok']))
                                                    <span class="badge badge-success">OK</span>
                                                @elseif(in_array($seasoning->sensori, ['✘', 'X', 'x', 'tidak ok', 'Tidak OK']))
                                                    <span class="badge badge-danger">Tidak OK</span>
                                                @else
                                                    {{ $seasoning->sensori ?? '-' }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Kemasan</th>
                                            <td>: 
                                                @if(in_array($seasoning->kemasan, ['✔', '✓', 'OK', 'ok']))
                                                    <span class="badge badge-success">OK</span>
                                                @elseif(in_array($seasoning->kemasan, ['✘', 'X', 'x', 'tidak ok', 'Tidak OK']))
                                                    <span class="badge badge-danger">Tidak OK</span>
                                                @else
                                                    {{ $seasoning->kemasan ?? '-' }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Keterangan</th>
                                            <td>: {{ $seasoning->keterangan ?? '-' }}</td>
                                        </tr>
                                    </table>

                                    <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-user-check mr-2"></i> Verifikasi</h5>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="p-2 border rounded {{ $seasoning->approved_by_qc ? 'bg-success-light' : 'bg-light' }}">
                                                <small class="d-block text-muted">QC</small>
                                                <i class="fas {{ $seasoning->approved_by_qc ? 'fa-check-circle text-success' : 'fa-clock text-muted' }} fa-lg my-2"></i>
                                                <div class="small">{{ $seasoning->qcApprover->name ?? 'Belum' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 border rounded {{ $seasoning->approved_by_produksi ? 'bg-primary-light' : 'bg-light' }}">
                                                <small class="d-block text-muted">Produksi</small>
                                                <i class="fas {{ $seasoning->approved_by_produksi ? 'fa-check-circle text-primary' : 'fa-clock text-muted' }} fa-lg my-2"></i>
                                                <div class="small">{{ $seasoning->produksiApprover->name ?? 'Belum' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 border rounded {{ $seasoning->approved_by_spv ? 'bg-dark-light' : 'bg-light' }}">
                                                <small class="d-block text-muted">SPV</small>
                                                <i class="fas {{ $seasoning->approved_by_spv ? 'fa-check-circle text-dark' : 'fa-clock text-muted' }} fa-lg my-2"></i>
                                                <div class="small">{{ $seasoning->spvApprover->name ?? 'Belum' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="small text-muted">
                                    Input oleh: <strong>{{ $seasoning->createdBy->name ?? '-' }}</strong> pada {{ $seasoning->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="btn-group">
                                    @if(auth()->user()->hasPermissionTo('edit-seasoning'))
                                        <a href="{{ route('seasoning.edit', $seasoning->uuid) }}" class="btn btn-warning shadow-sm">
                                            <i class="fas fa-edit mr-1"></i> Edit Data
                                        </a>
                                    @endif
                                    <a href="{{ route('seasoning.logs', $seasoning->uuid) }}" class="btn btn-info ml-2 shadow-sm">
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
