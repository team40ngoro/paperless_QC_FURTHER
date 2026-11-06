@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-eye text-primary mr-2"></i> Detail Pemeriksaan Metal Detector</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('input-metal-detector.index') }}"><i class="fas fa-search"></i> Metal Detector</a></li>
                        <li class="breadcrumb-item active">Detail Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="icon fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="icon fas fa-info-circle mr-1"></i> {{ session('info') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="icon fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- CARD UTAMA: DETAIL DATA & HASIL -->
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-info-circle mr-1"></i> Informasi Pemeriksaan Metal Detector
                    </h3>
                    <div class="card-tools ml-auto">
                        @if($canEditQcVerification ?? false)
                            <a href="#qc-verification-section" class="btn btn-warning btn-sm font-weight-bold mr-2">
                                <i class="fas fa-clipboard-check mr-1"></i> Form Verifikasi QC
                            </a>
                        @endif
                        <a href="{{ route('input-metal-detector.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- BAGIAN A: DETAIL PEMERIKSAAN -->
                    <div class="card card-info card-outline mb-4">
                        <div class="card-header py-2">
                            <h5 class="card-title font-weight-bold text-info mb-0">
                                <i class="fas fa-clipboard-list mr-1"></i> A. Detail Pemeriksaan Metal Detector
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 20%;" class="bg-light">Tanggal Kode Produksi</th>
                                        <td style="width: 30%;">
                                            <i class="fas fa-calendar-alt text-primary mr-1"></i>
                                            {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') : '-' }}
                                        </td>
                                        <th style="width: 20%;" class="bg-light">Jam</th>
                                        <td style="width: 30%;">
                                            <i class="fas fa-clock text-primary mr-1"></i>
                                            {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Shift</th>
                                        <td><span class="badge badge-info font-weight-normal px-2 py-1">Shift {{ $item->shift->shift ?? '-' }}</span></td>
                                        <th class="bg-light">Line</th>
                                        <td><span class="badge badge-warning font-weight-normal px-2 py-1">Line {{ $item->line ?? '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Nama Produk</th>
                                        <td><strong>{{ $item->produk->nama_produk ?? '-' }}</strong></td>
                                        <th class="bg-light">Kode Produksi</th>
                                        <td><span class="badge badge-secondary px-2 py-1">{{ $item->kode_produksi ?? '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Berat Produk</th>
                                        <td>{{ $item->berat_produk ? $item->berat_produk . ' gram' : ($item->gramasi ? $item->gramasi . ' gram' : '-') }}</td>
                                        <th class="bg-light">Keterangan (Bagian A)</th>
                                        <td>{{ $item->keterangan ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Dibuat Oleh</th>
                                        <td>{{ $item->user->name ?? '-' }}</td>
                                        <th class="bg-light">Plant</th>
                                        <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- BAGIAN B: HASIL PENGECEKAN (TAMBAHAN BARU) -->
                    <div class="card card-success card-outline mb-4">
                        <div class="card-header py-2">
                            <h5 class="card-title font-weight-bold text-success mb-0">
                                <i class="fas fa-check-double mr-1"></i> B. Hasil Pengecekan
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 20%;" class="bg-light">Merk Mesin MD</th>
                                        <td style="width: 30%;"><strong>{{ $item->merk ?: '-' }}</strong></td>
                                        <th style="width: 20%;" class="bg-light">Waktu Proses</th>
                                        <td style="width: 30%;">
                                            @if($item->waktu_mulai || $item->waktu_selesai)
                                                {{ $item->waktu_mulai ? \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') : '-' }} s/d {{ $item->waktu_selesai ? \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') : '-' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Gramasi (gr)</th>
                                        <td>{{ $item->gramasi ? $item->gramasi . ' gram' : ($item->berat_produk ? $item->berat_produk . ' gram' : '-') }}</td>
                                        <th class="bg-light">Jumlah Reject Kontaminasi Logam</th>
                                        <td>
                                            @if(($item->jumlah_reject ?? 0) > 0)
                                                <span class="badge badge-danger font-weight-bold px-2 py-1">{{ $item->jumlah_reject }} pcs</span>
                                            @else
                                                <span class="badge badge-success px-2 py-1">0 pcs</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Nama Operator</th>
                                        <td>{{ $item->nama_operator ?: '-' }}</td>
                                        <th class="bg-light">Foreman / Forelady / SPV</th>
                                        <td>{{ $item->spv_foreman ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Kolom Keterangan (Bagian B)</th>
                                        <td colspan="3">{{ $item->keterangan_b ?: '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- HASIL DETEKSI SPESIMEN & STATUS VERIFIKASI QC -->
                    <div class="card card-warning card-outline mb-0">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                            <h5 class="card-title font-weight-bold text-dark mb-0">
                                <i class="fas fa-microchip mr-1"></i> Hasil Deteksi Menggunakan Spesimen & Verifikasi QC
                            </h5>
                            @if($item->verifikasi_detail && $item->verifikasi_hasil_proses)
                                <span class="badge badge-success font-weight-bold ml-auto px-2 py-1">
                                    <i class="fas fa-check-circle mr-1"></i> Terverifikasi QC
                                </span>
                            @else
                                <span class="badge badge-warning font-weight-bold ml-auto px-2 py-1">
                                    <i class="fas fa-hourglass-half mr-1"></i> Belum Diverifikasi QC
                                </span>
                            @endif
                        </div>
                        <div class="card-body">
                            <!-- Tabel Spesimen -->
                            <div class="table-responsive mb-4">
                                <table class="text-center table table-bordered mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center bg-info text-white" colspan="3">Fe 1.5 mm</th>
                                            <th class="text-center bg-success text-white" colspan="3">Non Fe 2.0 mm</th>
                                            <th class="text-center bg-warning text-dark" colspan="3">SUS 316 2.5 mm</th>
                                        </tr>
                                        <tr class="small font-weight-bold bg-light">
                                            <th style="width: 11%;">Depan</th>
                                            <th style="width: 11%;">Tengah</th>
                                            <th style="width: 11%;">Belakang</th>
                                            <th style="width: 11%;">Depan</th>
                                            <th style="width: 11%;">Tengah</th>
                                            <th style="width: 11%;">Belakang</th>
                                            <th style="width: 11%;">Depan</th>
                                            <th style="width: 11%;">Tengah</th>
                                            <th style="width: 11%;">Belakang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            @php
                                                $renderSpecimenBadge = function($val) {
                                                    if ($val === '✔') {
                                                        return '<span class="badge badge-success font-weight-bold px-2 py-1"><i class="fas fa-check mr-1"></i> Terdeteksi (✔)</span>';
                                                    } elseif ($val === '✘') {
                                                        return '<span class="badge badge-danger font-weight-bold px-2 py-1"><i class="fas fa-times mr-1"></i> Tidak (✘)</span>';
                                                    }
                                                    return '<span class="text-muted">-</span>';
                                                };
                                            @endphp
                                            <td>{!! $renderSpecimenBadge($item->fe_depan_aktual) !!}</td>
                                            <td>{!! $renderSpecimenBadge($item->fe_tengah_aktual) !!}</td>
                                            <td>{!! $renderSpecimenBadge($item->fe_belakang_aktual) !!}</td>
                                            <td>{!! $renderSpecimenBadge($item->non_fe_depan_aktual) !!}</td>
                                            <td>{!! $renderSpecimenBadge($item->non_fe_tengah_aktual) !!}</td>
                                            <td>{!! $renderSpecimenBadge($item->non_fe_belakang_aktual) !!}</td>
                                            <td>{!! $renderSpecimenBadge($item->sus_depan_aktual) !!}</td>
                                            <td>{!! $renderSpecimenBadge($item->sus_tengah_aktual) !!}</td>
                                            <td>{!! $renderSpecimenBadge($item->sus_belakang_aktual) !!}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Baris Verifikasi QC -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box bg-light border">
                                        <span class="info-box-icon {{ $item->verifikasi_detail === '✔' ? 'bg-success' : ($item->verifikasi_detail === '✘' ? 'bg-danger' : 'bg-secondary') }}">
                                            <i class="fas {{ $item->verifikasi_detail === '✔' ? 'fa-check' : ($item->verifikasi_detail === '✘' ? 'fa-times' : 'fa-question') }}"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text text-muted">Verifikasi Detail Pemeriksaan Metal Detector</span>
                                            <span class="info-box-number font-weight-bold">
                                                @if($item->verifikasi_detail === '✔')
                                                    <span class="text-success"><i class="fas fa-check-circle mr-1"></i> OK (✔)</span>
                                                @elseif($item->verifikasi_detail === '✘')
                                                    <span class="text-danger"><i class="fas fa-times-circle mr-1"></i> Tidak OK (✘)</span>
                                                @else
                                                    <span class="text-muted">Belum Diverifikasi</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box bg-light border">
                                        <span class="info-box-icon {{ $item->verifikasi_hasil_proses === '✔' ? 'bg-success' : ($item->verifikasi_hasil_proses === '✘' ? 'bg-danger' : 'bg-secondary') }}">
                                            <i class="fas {{ $item->verifikasi_hasil_proses === '✔' ? 'fa-check' : ($item->verifikasi_hasil_proses === '✘' ? 'fa-times' : 'fa-question') }}"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text text-muted">Verifikasi Hasil Proses Metal Detector</span>
                                            <span class="info-box-number font-weight-bold">
                                                @if($item->verifikasi_hasil_proses === '✔')
                                                    <span class="text-success"><i class="fas fa-check-circle mr-1"></i> OK (✔)</span>
                                                @elseif($item->verifikasi_hasil_proses === '✘')
                                                    <span class="text-danger"><i class="fas fa-times-circle mr-1"></i> Tidak OK (✘)</span>
                                                @else
                                                    <span class="text-muted">Belum Diverifikasi</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($item->nama_qc || $item->qc_verified_at)
                                <div class="text-right text-muted small mt-2">
                                    <i class="fas fa-user-check mr-1"></i> Diverifikasi oleh: <strong>{{ $item->nama_qc ?? '-' }}</strong> 
                                    pada {{ $item->qc_verified_at ? \Carbon\Carbon::parse($item->qc_verified_at)->translatedFormat('d F Y H:i') : '-' }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                    <a href="{{ route('input-metal-detector.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                    </a>
                    <div>
                        @if(auth()->user()->hasPermissionTo('edit-input-metal-detector') && (int)(auth()->user()->id_role ?? 0) !== 3)
                            <a href="{{ route('input-metal-detector.edit', $item->uuid) }}" class="btn btn-warning mr-1">
                                <i class="fas fa-edit mr-1"></i> Edit Data Produksi
                            </a>
                        @endif
                        @if(auth()->user()->hasPermissionTo('view-input-metal-detector'))
                            <a href="{{ route('input-metal-detector.logs', $item->uuid) }}" class="btn btn-info">
                                <i class="fas fa-history mr-1"></i> Lihat Riwayat
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- FORM INPUT UJI SPESIMEN & VERIFIKASI QC INSPECTOR (KHUSUS ROLE QC / ADMIN / SUPERADMIN) -->
            @if($canEditQcVerification ?? false)
            <div class="card card-warning card-outline mt-4 shadow-sm mb-5" id="qc-verification-section">
                <div class="card-header bg-warning py-2 d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-clipboard-check mr-2"></i> Form Input Uji Spesimen & Verifikasi QC Inspector
                    </h3>
                    <button type="button" class="btn btn-dark btn-sm font-weight-bold ml-auto" id="btn-quick-fill-all-ok">
                        <i class="fas fa-check-double mr-1"></i> Set Semua Terdeteksi & OK (✔)
                    </button>
                </div>

                <form action="{{ route('input-metal-detector.qc-verification', $item->uuid) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="alert alert-info py-2 mb-3">
                            <i class="fas fa-info-circle mr-1"></i> Periksa kesesuaian Detail Pemeriksaan dan Hasil Pengecekan di atas. Lakukan uji 3 spesimen logam (Fe, Non-Fe, SUS) pada posisi Depan, Tengah, Belakang, lalu tentukan <strong>Hasil Verifikasi QC</strong>.
                        </div>

                        <!-- 1. INPUT HASIL DETEKSI SPESIMEN -->
                        <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-vial mr-1 text-primary"></i> 1. Hasil Deteksi Menggunakan Spesimen</h6>
                        <div class="row">
                            <!-- FE 1.5 MM -->
                            <div class="col-md-4 mb-3">
                                <div class="card card-outline card-info shadow-none border">
                                    <div class="card-header bg-info py-1 text-white font-weight-bold text-center">
                                        Fe 1.5 mm
                                    </div>
                                    <div class="card-body py-2 px-3">
                                        <div class="form-group mb-2">
                                            <label class="small font-weight-bold mb-1">Depan <span class="text-danger">*</span></label>
                                            <select class="form-control form-control-sm specimen-select" name="fe_depan_aktual" required>
                                                <option value="" disabled {{ old('fe_depan_aktual', $item->fe_depan_aktual) ? '' : 'selected' }}>Pilih Hasil</option>
                                                <option value="✔" {{ old('fe_depan_aktual', $item->fe_depan_aktual) === '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                <option value="✘" {{ old('fe_depan_aktual', $item->fe_depan_aktual) === '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label class="small font-weight-bold mb-1">Tengah <span class="text-danger">*</span></label>
                                            <select class="form-control form-control-sm specimen-select" name="fe_tengah_aktual" required>
                                                <option value="" disabled {{ old('fe_tengah_aktual', $item->fe_tengah_aktual) ? '' : 'selected' }}>Pilih Hasil</option>
                                                <option value="✔" {{ old('fe_tengah_aktual', $item->fe_tengah_aktual) === '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                <option value="✘" {{ old('fe_tengah_aktual', $item->fe_tengah_aktual) === '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-1">
                                            <label class="small font-weight-bold mb-1">Belakang <span class="text-danger">*</span></label>
                                            <select class="form-control form-control-sm specimen-select" name="fe_belakang_aktual" required>
                                                <option value="" disabled {{ old('fe_belakang_aktual', $item->fe_belakang_aktual) ? '' : 'selected' }}>Pilih Hasil</option>
                                                <option value="✔" {{ old('fe_belakang_aktual', $item->fe_belakang_aktual) === '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                <option value="✘" {{ old('fe_belakang_aktual', $item->fe_belakang_aktual) === '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- NON FE 2.0 MM -->
                            <div class="col-md-4 mb-3">
                                <div class="card card-outline card-success shadow-none border">
                                    <div class="card-header bg-success py-1 text-white font-weight-bold text-center">
                                        Non Fe 2.0 mm
                                    </div>
                                    <div class="card-body py-2 px-3">
                                        <div class="form-group mb-2">
                                            <label class="small font-weight-bold mb-1">Depan <span class="text-danger">*</span></label>
                                            <select class="form-control form-control-sm specimen-select" name="non_fe_depan_aktual" required>
                                                <option value="" disabled {{ old('non_fe_depan_aktual', $item->non_fe_depan_aktual) ? '' : 'selected' }}>Pilih Hasil</option>
                                                <option value="✔" {{ old('non_fe_depan_aktual', $item->non_fe_depan_aktual) === '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                <option value="✘" {{ old('non_fe_depan_aktual', $item->non_fe_depan_aktual) === '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label class="small font-weight-bold mb-1">Tengah <span class="text-danger">*</span></label>
                                            <select class="form-control form-control-sm specimen-select" name="non_fe_tengah_aktual" required>
                                                <option value="" disabled {{ old('non_fe_tengah_aktual', $item->non_fe_tengah_aktual) ? '' : 'selected' }}>Pilih Hasil</option>
                                                <option value="✔" {{ old('non_fe_tengah_aktual', $item->non_fe_tengah_aktual) === '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                <option value="✘" {{ old('non_fe_tengah_aktual', $item->non_fe_tengah_aktual) === '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-1">
                                            <label class="small font-weight-bold mb-1">Belakang <span class="text-danger">*</span></label>
                                            <select class="form-control form-control-sm specimen-select" name="non_fe_belakang_aktual" required>
                                                <option value="" disabled {{ old('non_fe_belakang_aktual', $item->non_fe_belakang_aktual) ? '' : 'selected' }}>Pilih Hasil</option>
                                                <option value="✔" {{ old('non_fe_belakang_aktual', $item->non_fe_belakang_aktual) === '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                <option value="✘" {{ old('non_fe_belakang_aktual', $item->non_fe_belakang_aktual) === '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SUS 316 2.5 MM -->
                            <div class="col-md-4 mb-3">
                                <div class="card card-outline card-warning shadow-none border">
                                    <div class="card-header bg-warning py-1 text-dark font-weight-bold text-center">
                                        SUS 316 2.5 mm
                                    </div>
                                    <div class="card-body py-2 px-3">
                                        <div class="form-group mb-2">
                                            <label class="small font-weight-bold mb-1">Depan <span class="text-danger">*</span></label>
                                            <select class="form-control form-control-sm specimen-select" name="sus_depan_aktual" required>
                                                <option value="" disabled {{ old('sus_depan_aktual', $item->sus_depan_aktual) ? '' : 'selected' }}>Pilih Hasil</option>
                                                <option value="✔" {{ old('sus_depan_aktual', $item->sus_depan_aktual) === '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                <option value="✘" {{ old('sus_depan_aktual', $item->sus_depan_aktual) === '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label class="small font-weight-bold mb-1">Tengah <span class="text-danger">*</span></label>
                                            <select class="form-control form-control-sm specimen-select" name="sus_tengah_aktual" required>
                                                <option value="" disabled {{ old('sus_tengah_aktual', $item->sus_tengah_aktual) ? '' : 'selected' }}>Pilih Hasil</option>
                                                <option value="✔" {{ old('sus_tengah_aktual', $item->sus_tengah_aktual) === '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                <option value="✘" {{ old('sus_tengah_aktual', $item->sus_tengah_aktual) === '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-1">
                                            <label class="small font-weight-bold mb-1">Belakang <span class="text-danger">*</span></label>
                                            <select class="form-control form-control-sm specimen-select" name="sus_belakang_aktual" required>
                                                <option value="" disabled {{ old('sus_belakang_aktual', $item->sus_belakang_aktual) ? '' : 'selected' }}>Pilih Hasil</option>
                                                <option value="✔" {{ old('sus_belakang_aktual', $item->sus_belakang_aktual) === '✔' ? 'selected' : '' }}>✔ Terdeteksi</option>
                                                <option value="✘" {{ old('sus_belakang_aktual', $item->sus_belakang_aktual) === '✘' ? 'selected' : '' }}>✘ Tidak Terdeteksi</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. TAMBAHAN BARU: VERIFIKASI QC -->
                        <h6 class="font-weight-bold text-dark mb-3 mt-2"><i class="fas fa-check-circle mr-1 text-success"></i> 2. Verifikasi Hasil QC</h6>
                        <div class="card card-outline card-secondary shadow-none border">
                            <div class="card-body py-3 px-3">
                                <div class="row">
                                    <div class="col-md-6 border-right">
                                        <label class="font-weight-bold text-dark d-block mb-2">
                                            Verifikasi Detail Pemeriksaan Metal Detector <span class="text-danger">*</span>
                                        </label>
                                        <div class="d-flex align-items-center" style="gap: 2rem;">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="verifikasi_detail_ok" name="verifikasi_detail" value="✔" class="custom-control-input radio-detail-ok" {{ old('verifikasi_detail', $item->verifikasi_detail) === '✔' ? 'checked' : '' }} required>
                                                <label class="custom-control-label text-success font-weight-bold" for="verifikasi_detail_ok" style="cursor: pointer;">
                                                    <i class="fas fa-check-circle mr-1"></i> OK (✔)
                                                </label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="verifikasi_detail_not_ok" name="verifikasi_detail" value="✘" class="custom-control-input" {{ old('verifikasi_detail', $item->verifikasi_detail) === '✘' ? 'checked' : '' }} required>
                                                <label class="custom-control-label text-danger font-weight-bold" for="verifikasi_detail_not_ok" style="cursor: pointer;">
                                                    <i class="fas fa-times-circle mr-1"></i> Tidak OK (✘)
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="font-weight-bold text-dark d-block mb-2">
                                            Verifikasi Hasil Proses Metal Detector <span class="text-danger">*</span>
                                        </label>
                                        <div class="d-flex align-items-center" style="gap: 2rem;">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="verifikasi_proses_ok" name="verifikasi_hasil_proses" value="✔" class="custom-control-input radio-proses-ok" {{ old('verifikasi_hasil_proses', $item->verifikasi_hasil_proses) === '✔' ? 'checked' : '' }} required>
                                                <label class="custom-control-label text-success font-weight-bold" for="verifikasi_proses_ok" style="cursor: pointer;">
                                                    <i class="fas fa-check-circle mr-1"></i> OK (✔)
                                                </label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="verifikasi_proses_not_ok" name="verifikasi_hasil_proses" value="✘" class="custom-control-input" {{ old('verifikasi_hasil_proses', $item->verifikasi_hasil_proses) === '✘' ? 'checked' : '' }} required>
                                                <label class="custom-control-label text-danger font-weight-bold" for="verifikasi_proses_not_ok" style="cursor: pointer;">
                                                    <i class="fas fa-times-circle mr-1"></i> Tidak OK (✘)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light text-right py-2">
                        <button type="submit" class="btn btn-success shadow-sm font-weight-bold px-4">
                            <i class="fas fa-save mr-1"></i> Simpan Uji Spesimen & Verifikasi QC
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#btn-quick-fill-all-ok').on('click', function() {
        $('.specimen-select').val('✔');
        $('.radio-detail-ok').prop('checked', true);
        $('.radio-proses-ok').prop('checked', true);
        if (typeof toastr !== 'undefined') {
            toastr.success('Semua spesimen dan verifikasi telah diatur ke Terdeteksi / OK (✔)');
        }
    });
});
</script>
@endpush
