@extends('layouts.app')

@php
    $currentUser = auth()->user();
    $userRole = (int) ($currentUser->id_role ?? 0);
    $userRoleName = strtolower(optional($currentUser->roleModel)->role ?? $currentUser->role ?? '');
    $isSuperadmin = $currentUser->role === 'superadmin' || in_array($userRole, [1, 5], true);
    $isQcInspector = $isSuperadmin || (method_exists($currentUser, 'isQcInspector') && $currentUser->isQcInspector()) || $userRole === 3 || (method_exists($currentUser, 'isQc') && $currentUser->isQc()) || str_contains($userRoleName, 'qc');
    $isSpvQc = $isSuperadmin || $userRole === 4 || str_contains($userRoleName, 'spv qc') || str_contains($userRoleName, 'supervisor qc');

    $kondisiDecoded = json_decode($data->kondisi, true);
    $suhuEmulsiVal = is_array($kondisiDecoded) ? ($kondisiDecoded[0] ?? '') : ($data->kondisi ?? '');

    $hasilDecoded = json_decode($data->hasil_emulsi, true);
    $hasilEmulsiVal = is_array($hasilDecoded) ? ($hasilDecoded[0] ?? '') : ($data->hasil_emulsi ?? '');

    $hasilEmulsiText = '-';
    if ($hasilEmulsiVal === '✔' || $hasilEmulsiVal === 'OK') {
        $hasilEmulsiText = '✔ - Oke';
    } elseif ($hasilEmulsiVal === '✘' || $hasilEmulsiVal === 'NOT_OK') {
        $hasilEmulsiText = '✘ - Tidak Oke';
    } elseif ($hasilEmulsiVal) {
        $hasilEmulsiText = $hasilEmulsiVal;
    } else {
        $hasilEmulsiText = 'Belum diisi (Verifikasi QC)';
    }
@endphp

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-eye text-primary"></i> Verifikasi Pembuatan Emulsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('persiapan-bahan-emulsi.index') }}">Persiapan Bahan Emulsi</a></li>
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
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <!-- <h3 class="card-title"><i class="fas fa-info-circle"></i> Informasi Detail</h3>
                            <div class="card-tools">
                                <a href="{{ route('persiapan-bahan-emulsi.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                @if(auth()->user()->hasPermissionTo('edit-persiapan-bahan-emulsi'))
                                    <a href="{{ route('persiapan-bahan-emulsi.edit', $data->uuid) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                @endif
                            </div> -->
                        </div>
                        <div class="card-body">
                            <!-- Informasi Umum -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar mr-1"></i>Tanggal</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->tanggal ? \Carbon\Carbon::parse($data->tanggal)->format('Y-m-d H:i:s') : '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-clock mr-1"></i>Shift</label>
                                        @php
                                            $sVal = (string)($data->shift->shift ?? ($data->shift_id ?? ''));
                                            $shiftText = $sVal;
                                            if ($sVal === '1' || strcasecmp($sVal, 'shift 1') === 0 || strcasecmp($sVal, 'pagi') === 0) {
                                                $shiftText = 'Pagi (Shift 1)';
                                            } elseif ($sVal === '2' || strcasecmp($sVal, 'shift 2') === 0 || strcasecmp($sVal, 'sore') === 0) {
                                                $shiftText = 'Sore (Shift 2)';
                                            } elseif ($sVal === '3' || strcasecmp($sVal, 'shift 3') === 0 || strcasecmp($sVal, 'malam') === 0) {
                                                $shiftText = 'Malam (Shift 3)';
                                            }
                                        @endphp
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $shiftText }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-box mr-1"></i>Nama Produk</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->produk->nama_produk ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-cube"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-code mr-1"></i>Kode Produksi Emulsi</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->kode_produksi_emulsi }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-qrcode"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-weight-hanging mr-1"></i>Gramase (gram)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->berat_sampling ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-balance-scale"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-clock mr-1"></i>Jam</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->jam }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-flask mr-1"></i>Nama Emulsi</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->nama_emulsi->nama_emulsi ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-vial"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-hashtag mr-1"></i>Jumlah Proses Emulsi</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->nomor_emulsi->nomor_emulsi ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-list-ol"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Detail Bahan Emulsi per Proses -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-primary"><i class="fas fa-thermometer-half mr-2"></i>Detail Bahan Emulsi per Proses</h5>
                                    <hr>
                                </div>
                            </div>

                            @if($data->suhuEmulsi && $data->suhuEmulsi->count() > 0)
                                @php
                                    // Group data by proses_ke
                                    $groupedData = $data->suhuEmulsi->groupBy('proses_ke');
                                    $kondisiArray = json_decode($data->kondisi, true) ?? [];
                                    $hasilArray = json_decode($data->hasil_emulsi, true) ?? [];
                                @endphp
                                
                                @foreach($groupedData as $prosesKe => $items)
                                    <div class="mt-4">
                                        <h5 class="text-primary">Proses Emulsi ke-{{ $prosesKe }}</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th width="5%" class="text-center">No</th>
                                                        <th width="40%">Nama RM</th>
                                                        <th width="15%" class="text-center">Berat (gram)</th>
                                                        <th width="20%" class="text-center">Kode Produksi Bahan</th>
                                                        <th width="20%" class="text-center">Kondisi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($items as $index => $suhu)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>{{ $suhu->bahanEmulsi->nama_rm ?? '-' }}</td>
                                                        <td class="text-center">
                                                            <span class="badge badge-secondary">{{ $suhu->berat_bahan ?? $suhu->bahanEmulsi->berat_rm ?? '-' }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $suhu->kode_produksi_bahan ?? '-' }}
                                                        </td>
                                                        <td class="text-center">
                                                            @if($suhu->suhu == '✔')
                                                                <span class="badge badge-success">✔ OK</span>
                                                            @elseif($suhu->suhu == '✘')
                                                                <span class="badge badge-danger">✘ Tidak OK</span>
                                                            @else
                                                                <span class="badge badge-info">{{ $suhu->suhu }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    
                                                    <!-- TAMBAHAN: Row untuk Suhu per Proses -->
                                                    <tr class="bg-light">
                                                        <td colspan="5">
                                                            <div class="row align-items-center">
                                                                <label class="col-sm-2 font-weight-bold mb-0"><i class="fas fa-thermometer-half mr-1"></i>Suhu</label>
                                                                <div class="col-sm-10">
                                                                    {{ $kondisiArray[$prosesKe - 1] ?? '-' }}°C
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    
                                                    <!-- TAMBAHAN: Row untuk Hasil Emulsi per Proses -->
                                                    <tr class="bg-light">
                                                        <td colspan="5">
                                                            <div class="row align-items-center">
                                                                <label class="col-sm-2 font-weight-bold mb-0"><i class="fas fa-check-circle mr-1"></i>Hasil Emulsi</label>
                                                                <div class="col-sm-10">
                                                                    @php $hasil = $hasilArray[$prosesKe - 1] ?? '-'; @endphp
                                                                    @if($hasil == '✔')
                                                                        <span class="badge badge-success">✔ OK</span>
                                                                    @elseif($hasil == '✘')
                                                                        <span class="badge badge-danger">✘ Tidak OK</span>
                                                                    @else
                                                                        <span class="badge badge-secondary">{{ $hasil }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Tidak ada data detail bahan emulsi yang tersedia.
                                </div>
                            @endif

                            <!-- Verifikasi QC Inspektor -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card card-success card-outline shadow-sm">
                                        <div class="card-header bg-white">
                                            <h5 class="card-title font-weight-bold mb-0 text-success">
                                                <i class="fas fa-clipboard-check mr-2"></i> Verifikasi QC Inspektor
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Ringkasan Status Verifikasi -->
                                            <div class="row mb-3">
                                                <div class="col-md-4 col-sm-6 mb-2">
                                                    <span class="text-muted d-block small">Status Verifikasi QC:</span>
                                                    @if($data->approved_by_qc)
                                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Terverifikasi QC</span>
                                                    @else
                                                        <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> Belum Diverifikasi</span>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 col-sm-6 mb-2">
                                                    <span class="text-muted d-block small">Verifikasi Hasil Emulsi:</span>
                                                    @if($hasilEmulsiVal === '✔' || $hasilEmulsiVal === 'OK')
                                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> ✔ - Oke</span>
                                                    @elseif($hasilEmulsiVal === '✘' || $hasilEmulsiVal === 'NOT_OK')
                                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-times mr-1"></i> ✘ - Tidak Oke</span>
                                                    @else
                                                        <span class="badge badge-light border text-muted px-2 py-1">Belum diisi</span>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 col-sm-6 mb-2">
                                                    <span class="text-muted d-block small">Suhu Emulsi:</span>
                                                    @if($suhuEmulsiVal !== '' && $suhuEmulsiVal !== null)
                                                        <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 0.95rem;">
                                                            {{ is_numeric($suhuEmulsiVal) ? number_format((float)$suhuEmulsiVal, 1) : $suhuEmulsiVal }} °C
                                                        </span>
                                                    @else
                                                        <span class="badge badge-light border text-muted px-2 py-1">Belum diisi</span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($isQcInspector)
                                                <form action="{{ route('persiapan-bahan-emulsi.qc-verification', $data->uuid) }}" method="POST">
                                                    @csrf
                                                    <div class="p-3 bg-light rounded border">
                                                        <h6 class="font-weight-bold text-dark mb-3">
                                                            <i class="fas fa-edit text-primary mr-1"></i> Form Input Verifikasi QC
                                                        </h6>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-md-0 mb-3">
                                                                    <label for="hasil_emulsi" class="font-weight-bold">
                                                                        <i class="fas fa-check-circle text-primary mr-1"></i> Verifikasi Hasil Emulsi <span class="text-danger">*</span>
                                                                    </label>
                                                                    <select name="hasil_emulsi" id="hasil_emulsi" class="form-control @error('hasil_emulsi') is-invalid @enderror" required>
                                                                        <option value="">-- Pilih Hasil Emulsi --</option>
                                                                        <option value="✔" {{ old('hasil_emulsi', $hasilEmulsiVal) === '✔' || old('hasil_emulsi', $hasilEmulsiVal) === 'OK' ? 'selected' : '' }}>
                                                                            ✔ - Oke
                                                                        </option>
                                                                        <option value="✘" {{ old('hasil_emulsi', $hasilEmulsiVal) === '✘' || old('hasil_emulsi', $hasilEmulsiVal) === 'NOT_OK' ? 'selected' : '' }}>
                                                                            ✘ - Tidak Oke
                                                                        </option>
                                                                    </select>
                                                                    @error('hasil_emulsi')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-md-0 mb-3">
                                                                    <label for="suhu_emulsi" class="font-weight-bold">
                                                                        <i class="fas fa-thermometer-half text-danger mr-1"></i> Suhu Emulsi (°C) <span class="text-danger">*</span>
                                                                    </label>
                                                                    <div class="input-group">
                                                                        <input type="number" step="0.1" name="suhu_emulsi" id="suhu_emulsi" 
                                                                               class="form-control @error('suhu_emulsi') is-invalid @enderror" 
                                                                               placeholder="Contoh: 4.5" 
                                                                               value="{{ old('suhu_emulsi', $suhuEmulsiVal) }}" required>
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text font-weight-bold">°C</span>
                                                                        </div>
                                                                        @error('suhu_emulsi')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                    <small class="form-text text-muted">Hanya 1 kolom input angka suhu emulsi.</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3 text-right">
                                                            <button type="submit" class="btn btn-success shadow-sm font-weight-bold px-4">
                                                                <i class="fas fa-save mr-1"></i> Simpan Verifikasi QC
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            @else
                                                <div class="alert alert-light border mb-0">
                                                    <i class="fas fa-lock mr-1 text-muted"></i> Hanya role <strong>QC Inspektor</strong> yang dapat mengisi dan mengubah verifikasi ini.
                                                </div>
                                            @endif

                                            @if($data->approved_by_qc)
                                                <div class="small text-muted mt-3 pt-2 border-top">
                                                    <i class="fas fa-user-check text-success mr-1"></i> Diverifikasi oleh: 
                                                    <strong>{{ $data->qcApprover->name ?? '-' }}</strong>
                                                    @if($data->qcApprover)
                                                        <span class="badge badge-light border">{{ $data->qcApprover->roleModel->role ?? $data->qcApprover->role ?? 'QC' }}</span>
                                                    @endif
                                                    pada {{ $data->qc_approved_at ? \Carbon\Carbon::parse($data->qc_approved_at)->format('d-m-Y H:i') : '-' }}.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 4-Stage Verification & Approval Status -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5 class="text-primary font-weight-bold">
                                        <i class="fas fa-tasks mr-2"></i>Status Alur Verifikasi & Persetujuan (4 Tahap)
                                    </h5>
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                {{-- 1. Dibuat Oleh = Produksi --}}
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card h-100 border-left border-primary shadow-sm" style="border-left-width: 4px !important;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge badge-primary">1. Dibuat oleh</span>
                                                <i class="fas fa-check-circle text-success"></i>
                                            </div>
                                            <div class="font-weight-bold text-dark mt-2">Produksi</div>
                                            <div class="text-muted small mt-1">
                                                <i class="fas fa-user mr-1"></i> {{ $data->user->name ?? '-' }}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-calendar-alt mr-1"></i> {{ $data->created_at ? $data->created_at->format('d-m-Y H:i') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. Verifikasi = QC Inspektor --}}
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card h-100 border-left {{ $data->approved_by_qc ? 'border-success' : 'border-secondary' }} shadow-sm" style="border-left-width: 4px !important;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge {{ $data->approved_by_qc ? 'badge-success' : 'badge-secondary' }}">2. Verifikasi</span>
                                                <i class="fas {{ $data->approved_by_qc ? 'fa-check-circle text-success' : 'fa-clock text-muted' }}"></i>
                                            </div>
                                            <div class="font-weight-bold text-dark mt-2">QC Inspektor</div>
                                            <div class="text-muted small mt-1">
                                                <i class="fas fa-user mr-1"></i> {{ $data->qcApprover->name ?? '-' }}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-calendar-alt mr-1"></i> {{ $data->qc_approved_at ? \Carbon\Carbon::parse($data->qc_approved_at)->format('d-m-Y H:i') : '-' }}
                                            </div>
                                            @if(!$data->approved_by_qc)
                                                @if($isQcInspector)
                                                    <span class="badge badge-warning text-dark mt-2 d-block py-1">
                                                        <i class="fas fa-exclamation-circle mr-1"></i> Perlu Verifikasi (Form di atas)
                                                    </span>
                                                @else
                                                    <span class="badge badge-light border text-muted mt-2 d-block py-1">
                                                        Menunggu QC
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- 3. Diketahui = SPV Produksi --}}
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card h-100 border-left {{ $data->approved_by_produksi ? 'border-primary' : 'border-secondary' }} shadow-sm" style="border-left-width: 4px !important;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge {{ $data->approved_by_produksi ? 'badge-primary' : 'badge-secondary' }}">3. Diketahui</span>
                                                <i class="fas {{ $data->approved_by_produksi ? 'fa-check-circle text-primary' : 'fa-clock text-muted' }}"></i>
                                            </div>
                                            <div class="font-weight-bold text-dark mt-2">SPV Produksi</div>
                                            <div class="text-muted small mt-1">
                                                <i class="fas fa-user mr-1"></i> {{ $data->produksiApprover->name ?? '-' }}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-calendar-alt mr-1"></i> {{ $data->produksi_approved_at ? \Carbon\Carbon::parse($data->produksi_approved_at)->format('d-m-Y H:i') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 4. Disetujui = SPV QC --}}
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card h-100 border-left {{ $data->approved_by_spv ? 'border-warning' : 'border-secondary' }} shadow-sm" style="border-left-width: 4px !important;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge {{ $data->approved_by_spv ? 'badge-warning text-dark' : 'badge-secondary' }}">4. Disetujui</span>
                                                <i class="fas {{ $data->approved_by_spv ? 'fa-check-circle text-warning' : 'fa-clock text-muted' }}"></i>
                                            </div>
                                            <div class="font-weight-bold text-dark mt-2">SPV QC</div>
                                            <div class="text-muted small mt-1">
                                                <i class="fas fa-user mr-1"></i> {{ $data->spvApprover->name ?? '-' }}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-calendar-alt mr-1"></i> {{ $data->spv_approved_at ? \Carbon\Carbon::parse($data->spv_approved_at)->format('d-m-Y H:i') : '-' }}
                                            </div>
                                            @if(!$data->approved_by_spv)
                                                @if($data->approved_by_qc)
                                                    @if($isSpvQc)
                                                        <button type="button" class="btn btn-warning btn-sm font-weight-bold text-dark btn-block mt-2 shadow-sm btn-approve-spv" data-uuid="{{ $data->uuid }}">
                                                            <i class="fas fa-check-circle mr-1"></i> Setujui (SPV QC)
                                                        </button>
                                                    @else
                                                        <span class="badge badge-light border text-muted mt-2 d-block py-1">
                                                            Menunggu SPV QC
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-light border text-muted mt-2 d-block py-1">
                                                        <i class="fas fa-clock mr-1"></i> Menunggu Verifikasi QC
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($isSpvQc && $data->approved_by_qc && !$data->approved_by_spv)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-warning border shadow-sm d-flex flex-wrap justify-content-between align-items-center mb-0">
                                        <div class="mb-2 mb-md-0">
                                            <h6 class="font-weight-bold mb-1"><i class="fas fa-check-double mr-2"></i> Persetujuan SPV QC Tersedia</h6>
                                            <span>QC Inspektor telah memverifikasi data ini. Anda dapat langsung memberikan persetujuan sebagai SPV QC.</span>
                                        </div>
                                        <button type="button" class="btn btn-warning text-dark font-weight-bold shadow-sm btn-approve-spv px-3 py-2" data-uuid="{{ $data->uuid }}">
                                            <i class="fas fa-check-circle mr-1"></i> Setujui Sekarang (SPV QC)
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('persiapan-bahan-emulsi.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    @if(auth()->user()->hasPermissionTo('edit-persiapan-bahan-emulsi'))
                                        <a href="{{ route('persiapan-bahan-emulsi.edit', $data->uuid) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Edit Data
                                        </a>
                                    @endif
                                    @if(auth()->user()->hasPermissionTo('view-persiapan-bahan-emulsi'))
                                        <a href="{{ route('persiapan-bahan-emulsi.logs', $data->uuid) }}" class="btn btn-info">
                                            <i class="fas fa-history"></i> Lihat Riwayat Perbaikan
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handler untuk persetujuan SPV QC langsung
    $(document).on('click', '.btn-approve-spv', function(e) {
        e.preventDefault();
        const btn = $(this);
        const uuid = btn.data('uuid') || '{{ $data->uuid }}';

        const doApprove = function() {
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');
            $.ajax({
                url: "{{ route('persiapan-bahan-emulsi.approve', $data->uuid) }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    type: 'spv'
                },
                success: function(response) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message || 'Data berhasil disetujui oleh SPV QC',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        alert(response.message || 'Data berhasil disetujui oleh SPV QC');
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Setujui (SPV QC)');
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: msg
                        });
                    } else {
                        alert('Gagal: ' + msg);
                    }
                }
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Persetujuan SPV QC',
                text: 'Apakah Anda yakin ingin menyetujui data persiapan bahan emulsi ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<span class="text-dark font-weight-bold"><i class="fas fa-check mr-1"></i> Ya, Setujui</span>',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    doApprove();
                }
            });
        } else {
            if (confirm('Apakah Anda yakin ingin menyetujui data persiapan bahan emulsi ini sebagai SPV QC?')) {
                doApprove();
            }
        }
    });
});
</script>
@endpush
