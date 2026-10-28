@extends('layouts.app')
@php
    $isLocalhost = in_array(request()->getHost(), ['localhost', '127.0.0.1', '10.68.1.37']);
    $assetPath = $isLocalhost ? 'public/' : '';
@endphp
@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Kontaminasi Benda Asing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-benda-asing.index') }}">Kontaminasi Benda Asing</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Data Utama -->
                <div class="col-md-7">
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title text-primary"><i class="fas fa-info-circle mr-2"></i>Informasi Temuan</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-hover mb-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 35%;" class="text-muted"><i class="fas fa-calendar-alt mr-2"></i>Tanggal & Waktu</th>
                                        <td>
                                            <strong>{{ $pemeriksaan->tanggal ? \Carbon\Carbon::parse($pemeriksaan->tanggal)->format('d F Y') : '-' }}</strong> 
                                            <span class="mx-1">|</span> 
                                            <span class="badge badge-secondary">{{ $pemeriksaan->jam ? \Carbon\Carbon::parse($pemeriksaan->jam)->format('H:i') : '-' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted"><i class="fas fa-clock mr-2"></i>Shift</th>
                                        <td><strong>{{ $pemeriksaan->shift->shift ?? '-' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted"><i class="fas fa-box-open mr-2"></i>Produk</th>
                                        <td>
                                            <div class="font-weight-bold">{{ $pemeriksaan->produk->nama_produk ?? '-' }}</div>
                                            <small class="text-muted">Berat: {{ $pemeriksaan->berat }} gram</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted"><i class="fas fa-cube mr-2"></i>RM</th>
                                        <td>{{ $pemeriksaan->rm ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted"><i class="fas fa-barcode mr-2"></i>Kode Produksi</th>
                                        <td><span class="badge badge-info px-2 py-1">{{ $pemeriksaan->kode_produksi }}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted"><i class="fas fa-bug mr-2"></i>Jenis Kontaminasi</th>
                                        <td>
                                            <span class="text-danger font-weight-bold">{{ $pemeriksaan->jenis_kontaminasi }}</span><br>
                                            <small class="text-muted">Ukuran: {{ $pemeriksaan->ukuran_kontaminasi }}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted"><i class="fas fa-search-location mr-2"></i>Lokasi Temuan</th>
                                        <td>{{ $pemeriksaan->ditemukan }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted"><i class="fas fa-weight-hanging mr-2"></i>Jumlah Produk/RM Terdampak</th>
                                        <td>{{ $pemeriksaan->jumlah_terdampak ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted"><i class="fas fa-user-check mr-2"></i>Diketahui Oleh</th>
                                        <td>{{ $pemeriksaan->diketahui ?: '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Bukti Foto & Status Approval -->
                <div class="col-md-5">
                    <div class="card card-warning card-outline shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title text-warning"><i class="fas fa-camera mr-2"></i>Bukti Foto</h3>
                        </div>
                        <div class="card-body text-center bg-light">
                            @if($pemeriksaan->bukti)
                                <a href="{{ asset($assetPath . 'storage/' . $pemeriksaan->bukti) }}" target="_blank">
                                    <img src="{{ asset($assetPath . 'storage/' . $pemeriksaan->bukti) }}" 
                                         alt="Bukti foto" 
                                         class="img-fluid rounded shadow-sm" 
                                         style="max-height: 250px; object-fit: contain;">
                                </a>
                                <div class="mt-2 text-muted small">Klik gambar untuk memperbesar</div>
                            @else
                                <div class="py-5 text-muted">
                                    <i class="fas fa-image fa-4x mb-3 text-light"></i>
                                    <h5>Tidak ada foto bukti</h5>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card card-success card-outline shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title text-success"><i class="fas fa-check-circle mr-2"></i>Status Persetujuan</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-around text-center">
                                <!-- QC -->
                                <div>
                                    <h6 class="font-weight-bold mb-1">QC</h6>
                                    @if($pemeriksaan->approved_by_qc)
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                        <div class="small mt-1 text-muted">{{ $pemeriksaan->qcApprover->name ?? 'System' }}</div>
                                    @else
                                        <i class="fas fa-clock text-warning fa-2x"></i>
                                        <div class="small mt-1 text-muted">Pending</div>
                                    @endif
                                </div>
                                
                                <!-- Produksi -->
                                <div>
                                    <h6 class="font-weight-bold mb-1">Produksi</h6>
                                    @if($pemeriksaan->approved_by_produksi)
                                        <i class="fas fa-check-circle text-primary fa-2x"></i>
                                        <div class="small mt-1 text-muted">{{ $pemeriksaan->produksiApprover->name ?? 'System' }}</div>
                                    @else
                                        <i class="fas fa-clock text-warning fa-2x"></i>
                                        <div class="small mt-1 text-muted">Pending</div>
                                    @endif
                                </div>
                                
                                <!-- SPV -->
                                <div>
                                    <h6 class="font-weight-bold mb-1">SPV</h6>
                                    @if($pemeriksaan->approved_by_spv)
                                        <i class="fas fa-check-circle text-dark fa-2x"></i>
                                        <div class="small mt-1 text-muted">{{ $pemeriksaan->spvApprover->name ?? 'System' }}</div>
                                    @else
                                        <i class="fas fa-clock text-warning fa-2x"></i>
                                        <div class="small mt-1 text-muted">Pending</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analisa & Tindakan -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-danger card-outline shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title text-danger"><i class="fas fa-tools mr-2"></i>Tindak Lanjut & Analisa</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded h-100 border">
                                        <h6 class="font-weight-bold text-muted border-bottom pb-2 mb-3"><i class="fas fa-microscope mr-1"></i> Analisa Masalah</h6>
                                        <p class="mb-0 {{ !$pemeriksaan->analisa_masalah ? 'text-muted font-italic' : '' }}">
                                            {{ $pemeriksaan->analisa_masalah ?: 'Belum ada analisa masalah yang diisi.' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded h-100 border">
                                        <h6 class="font-weight-bold text-muted border-bottom pb-2 mb-3"><i class="fas fa-wrench mr-1"></i> Koreksi</h6>
                                        <p class="mb-0 {{ !$pemeriksaan->koreksi ? 'text-muted font-italic' : '' }}">
                                            {{ $pemeriksaan->koreksi ?: 'Belum ada koreksi yang diisi.' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded h-100 border">
                                        <h6 class="font-weight-bold text-muted border-bottom pb-2 mb-3"><i class="fas fa-shield-alt mr-1"></i> Tindakan Korektif</h6>
                                        <p class="mb-0 {{ !$pemeriksaan->tindak_korektif ? 'text-muted font-italic' : '' }}">
                                            {{ $pemeriksaan->tindak_korektif ?: 'Belum ada tindakan korektif yang diisi.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ $pemeriksaan->is_audit ? route('pemeriksaan-benda-asing.audit') : route('pemeriksaan-benda-asing.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
