@extends('layouts.app')

@section('title', 'Detail Pemeriksaan Proses Produksi')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-clipboard-check text-primary mr-2"></i>Detail Pemeriksaan Proses Produksi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-proses-produksi.index') }}">Pemeriksaan Proses Produksi</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            {{-- Badge Audit --}}
            @if($data->is_audit)
            <div class="alert alert-purple border-0 shadow-sm mb-4" style="background-color:#f3effe; border-left: 5px solid #6f42c1 !important;">
                <i class="fas fa-shield-alt text-purple mr-2"></i>
                <strong class="text-purple">Data Audit</strong> — Catatan ini adalah salinan audit dari data operasional. Data asli tidak terpengaruh oleh perubahan di sini.
            </div>
            @endif

            <div class="row">
                <!-- Kolom Kiri: Info Utama -->
                <div class="col-md-7">
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Informasi Utama
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-hover mb-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 35%;" class="text-muted pl-3"><i class="fas fa-calendar-alt mr-2"></i>Tanggal</th>
                                        <td>
                                            <strong>{{ $data->tanggal ? \Carbon\Carbon::parse($data->tanggal)->format('d F Y') : '-' }}</strong>
                                            @if($data->jam)
                                                <span class="mx-1">|</span>
                                                <span class="badge badge-secondary">
                                                    <i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($data->jam)->format('H:i') }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted pl-3"><i class="fas fa-layer-group mr-2"></i>Shift</th>
                                        <td><strong>{{ $data->shift->shift ?? '-' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted pl-3"><i class="fas fa-map-marker-alt mr-2"></i>Area</th>
                                        <td><span class="badge badge-info px-2 py-1">{{ $data->area->area ?? '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted pl-3"><i class="fas fa-exclamation-circle mr-2"></i>Jenis Ketidaksesuaian</th>
                                        <td><span class="badge badge-warning px-2 py-1">{{ $data->ketidaksesuaian_label }}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted pl-3"><i class="fas fa-random mr-2"></i>Disposisi</th>
                                        <td><span class="badge badge-secondary px-2 py-1">{{ $data->disposisi_label }}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted pl-3"><i class="fas fa-user mr-2"></i>Dibuat Oleh</th>
                                        <td>{{ $data->user->name ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Status Persetujuan -->
                <div class="col-md-5">
                    <div class="card card-success card-outline shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title text-success">
                                <i class="fas fa-check-circle mr-2"></i>Status Persetujuan
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-around text-center py-2">
                                <!-- QC -->
                                <div class="px-2">
                                    <h6 class="font-weight-bold text-muted mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">QC</h6>
                                    @if($data->approved_by_qc)
                                        <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                        <div class="small text-muted">{{ $data->qcApprover->name ?? 'System' }}</div>
                                        @if($data->qc_approved_at)
                                            <div class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($data->qc_approved_at)->format('d/m/Y H:i') }}</div>
                                        @endif
                                    @else
                                        <i class="fas fa-clock text-warning fa-2x mb-2"></i>
                                        <div class="small text-muted font-italic">Menunggu</div>
                                    @endif
                                </div>

                                <div class="border-left mx-2"></div>

                                <!-- Produksi -->
                                <div class="px-2">
                                    <h6 class="font-weight-bold text-muted mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Produksi</h6>
                                    @if($data->approved_by_produksi)
                                        <i class="fas fa-check-circle text-primary fa-2x mb-2"></i>
                                        <div class="small text-muted">{{ $data->produksiApprover->name ?? 'System' }}</div>
                                        @if($data->produksi_approved_at)
                                            <div class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($data->produksi_approved_at)->format('d/m/Y H:i') }}</div>
                                        @endif
                                    @else
                                        <i class="fas fa-clock text-warning fa-2x mb-2"></i>
                                        <div class="small text-muted font-italic">Menunggu</div>
                                    @endif
                                </div>

                                <div class="border-left mx-2"></div>

                                <!-- SPV -->
                                <div class="px-2">
                                    <h6 class="font-weight-bold text-muted mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">SPV</h6>
                                    @if($data->approved_by_spv)
                                        <i class="fas fa-check-circle text-dark fa-2x mb-2"></i>
                                        <div class="small text-muted">{{ $data->spvApprover->name ?? 'System' }}</div>
                                        @if($data->spv_approved_at)
                                            <div class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($data->spv_approved_at)->format('d/m/Y H:i') }}</div>
                                        @endif
                                    @else
                                        <i class="fas fa-clock text-warning fa-2x mb-2"></i>
                                        <div class="small text-muted font-italic">Menunggu</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Progress Bar --}}
                            @php
                                $approvedCount = (int)$data->approved_by_qc + (int)$data->approved_by_produksi + (int)$data->approved_by_spv;
                                $progress = ($approvedCount / 3) * 100;
                                $progressClass = $progress == 100 ? 'bg-success' : ($progress > 0 ? 'bg-warning' : 'bg-danger');
                            @endphp
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Progress Verifikasi</small>
                                    <small class="font-weight-bold">{{ $approvedCount }}/3 Disetujui</small>
                                </div>
                                <div class="progress" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $progress }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uraian Permasalahan & Tindak Lanjut -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-warning card-outline shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title text-warning">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Detail Permasalahan & Tindak Lanjut
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="bg-light border rounded p-3 h-100">
                                        <h6 class="font-weight-bold text-muted border-bottom pb-2 mb-3">
                                            <i class="fas fa-comment-dots mr-1 text-danger"></i> Uraian Permasalahan
                                        </h6>
                                        <p class="mb-0 {{ !$data->uraian_permasalahan ? 'text-muted font-italic' : '' }}">
                                            {{ $data->uraian_permasalahan ?: 'Tidak ada uraian permasalahan.' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light border rounded p-3 h-100">
                                        <h6 class="font-weight-bold text-muted border-bottom pb-2 mb-3">
                                            <i class="fas fa-microscope mr-1 text-info"></i> Analisa Penyebab
                                        </h6>
                                        <p class="mb-0 {{ !$data->analisa_penyebab ? 'text-muted font-italic' : '' }}">
                                            {{ $data->analisa_penyebab ?: 'Tidak ada analisa penyebab.' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light border rounded p-3 h-100">
                                        <h6 class="font-weight-bold text-muted border-bottom pb-2 mb-3">
                                            <i class="fas fa-tools mr-1 text-success"></i> Tindakan Koreksi
                                        </h6>
                                        <p class="mb-0 {{ !$data->tindakan_koreksi ? 'text-muted font-italic' : '' }}">
                                            {{ $data->tindakan_koreksi ?: 'Tidak ada tindakan koreksi.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            @if($data->is_audit)
                                <a href="{{ route('pemeriksaan-proses-produksi.audit') }}" class="btn btn-secondary shadow-sm mr-2">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Audit
                                </a>
                            @else
                                <a href="{{ route('pemeriksaan-proses-produksi.index') }}" class="btn btn-secondary shadow-sm mr-2">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                                </a>
                            @endif
                            @if(auth()->user()->hasPermissionTo('edit-pemeriksaan-proses-produksi'))
                                <a href="{{ route('pemeriksaan-proses-produksi.edit', $data->uuid) }}" class="btn btn-warning shadow-sm">
                                    <i class="fas fa-edit mr-1"></i> Edit Data
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection

@push('scripts')
<style>
    .text-purple { color: #6f42c1 !important; }
    .card-outline { border-top-width: 3px; }
    .table th, .table td { vertical-align: middle; }
</style>
@endpush
