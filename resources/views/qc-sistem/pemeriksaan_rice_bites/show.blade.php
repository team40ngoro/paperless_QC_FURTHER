@extends('layouts.app')

@section('title', 'Detail Pemeriksaan Rice Bites')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail Pemeriksaan Rice Bites</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-rice-bites.index') }}">Pemeriksaan Rice Bites</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- Informasi Umum Section -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-2"></i>Informasi Umum
                        </h3>
                        <div class="card-tools">
                           
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong><i class="fas fa-calendar mr-1"></i>Tanggal</strong></td>
                                        <td>: {{ $data->tanggal->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-clock mr-1"></i>Jam</strong></td>
                                        <td>: {{ $data->jam ? \Carbon\Carbon::parse($data->jam)->format('H:i') : '-' }} WIB</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-user-clock mr-1"></i>Shift</strong></td>
                                        <td>: <span class="badge badge-info">{{ $data->shift->shift ?? '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-box mr-1"></i>Produk</strong></td>
                                        <td>: {{ $data->produk->nama_produk ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-flask mr-1"></i>Nomor Formula</strong></td>
                                        <td>:
                                            @if($data->nomor_formula)
                                                <span class="badge badge-primary">{{ $data->nomor_formula }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong><i class="fas fa-tags mr-1"></i>Batch No</strong></td>
                                        <td>: <span class="badge badge-secondary">{{ $data->batch }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-recycle mr-1"></i>No Cooking Cycle</strong></td>
                                        <td>: {{ $data->no_cooking_cycle }}</td>
                                    </tr>
                                    
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bahan Baku Section -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-seedling mr-2"></i>Bahan Baku
                        </h3>
                    </div>
                    <div class="card-body">
                        @if(!empty($data->bahan_baku_array))
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama Bahan Baku</th>
                                            <th width="15%">Kode Produksi</th>
                                            <th width="12%">Berat</th>
                                            <th width="12%">Suhu</th>
                                            <th width="15%">Kondisi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data->bahan_baku_array as $index => $bahan)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $bahan['nama'] ?? '-' }}</td>
                                            <td>{{ $bahan['kode_produksi'] ?? '-' }}</td>
                                            <td>{{ $bahan['berat'] ?? '-' }} @if(!empty($bahan['berat'])) kg @endif</td>
                                            <td>{{ $bahan['suhu'] ?? '-' }} @if(!empty($bahan['suhu'])) °C @endif</td>
                                            <td>{{ $bahan['kondisi'] ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i>
                                Tidak ada data bahan baku
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Premix Section -->
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-vials mr-2"></i>Premix
                        </h3>
                    </div>
                    <div class="card-body">
                        @if(!empty($data->premix_array))
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama Premix</th>
                                            <th width="20%">Berat</th>
                                            <th width="25%">Kondisi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data->premix_array as $index => $premix)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $premix['nama'] ?? '-' }}</td>
                                            <td>{{ $premix['berat'] ?? '-' }} @if($premix['berat']) kg @endif</td>
                                            <td>{{ $premix['kondisi'] ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i>
                                Tidak ada data premix
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Parameter Proses Section -->
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cogs mr-2"></i>Parameter Proses
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary">
                                        <i class="fas fa-atom"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Parameter Nitrogen</span>
                                        <span class="info-box-number">{{ $data->parameter_nitrogen ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info">
                                        <i class="fas fa-syringe"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Jumlah Inject Nitrogen</span>
                                        <span class="info-box-number">{{ $data->jumlah_inject_nitrogen ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success">
                                        <i class="fas fa-tachometer-alt"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">RPM Cooking Cattle</span>
                                        <span class="info-box-number">{{ $data->rpm_cooking_cattle ?? '-' }} @if($data->rpm_cooking_cattle) RPM @endif</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cold Mixing Section -->
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-snowflake mr-2"></i>Cold Mixing
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-light">
                            <i class="fas fa-snowflake mr-2"></i>
                            <strong>Cold Mixing:</strong> {{ $data->cold_mixing ?? 'Tidak ada data' }}
                        </div>
                    </div>
                </div>

                <!-- Suhu Aktual Adonan Section -->
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-thermometer-half mr-2"></i>Suhu Aktual Adonan (3 Titik)
                        </h3>
                    </div>
                    <div class="card-body">
                        @if(!empty($data->suhu_aktual_adonan_array))
                            <div class="row">
                                @foreach($data->suhu_aktual_adonan_array as $index => $suhu)
                                <div class="col-md-4">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-secondary">
                                            <i class="fas fa-thermometer-half"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Titik {{ $index + 1 }}</span>
                                            <span class="info-box-number">{{ $suhu ?? '-' }} @if($suhu) °C @endif</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i>
                                Tidak ada data suhu aktual adonan
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Suhu Adonan Setelah Pencampuran Section -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-temperature-high mr-2"></i>Suhu Adonan Setelah Pencampuran
                        </h3>
                    </div>
                    <div class="card-body">
                        @if(!empty($data->suhu_adonan_pencampuran_array))
                            <div class="row">
                                @foreach($data->suhu_adonan_pencampuran_array as $index => $suhu)
                                <div class="col-md-4 mb-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info">
                                            <i class="fas fa-temperature-high"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Pengukuran {{ $index + 1 }}</span>
                                            <span class="info-box-number">{{ $suhu ?? '-' }} @if($suhu) °C @endif</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i>
                                Tidak ada data suhu adonan setelah pencampuran
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Rata-rata & Hasil Pencampuran Section -->
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calculator mr-2"></i>Rata-rata & Hasil Pencampuran
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-secondary">
                                        <i class="fas fa-chart-line"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Rata-rata Suhu Pencampuran</span>
                                        <span class="info-box-number">
                                            @if($data->rata_rata_suhu)
                                                {{ number_format($data->rata_rata_suhu, 2) }} °C
                                            @else
                                                -
                                            @endif
                                        </span>
                                        <span class="progress-description">Otomatis dihitung dari suhu pencampuran</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon {{ $data->hasil_pencampuran == 'OK' ? 'bg-success' : ($data->hasil_pencampuran == 'Tidak OK' ? 'bg-danger' : 'bg-secondary') }}">
                                        <i class="fas {{ $data->hasil_pencampuran == 'OK' ? 'fa-check' : ($data->hasil_pencampuran == 'Tidak OK' ? 'fa-times' : 'fa-question') }}"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Hasil Pencampuran</span>
                                        <span class="info-box-number">{{ $data->hasil_pencampuran ?? 'Belum ditentukan' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan Section -->
                @if($data->catatan)
                <div class="card card-dark">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-sticky-note mr-2"></i>Catatan
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-light">
                            <i class="fas fa-comment mr-2"></i>
                            {{ $data->catatan }}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Status Verifikasi Section -->
                <!-- <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-check mr-2"></i>Status Verifikasi
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon {{ $data->diverifikasi_qc_status == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {!! $data->diverifikasi_qc_icon !!}
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Status QC</span>
                                        <span class="info-box-number">{{ $data->diverifikasi_qc }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon {{ $data->diketahui_produksi_status == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {!! $data->diketahui_produksi_icon !!}
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Status Produksi</span>
                                        <span class="info-box-number">{{ $data->diketahui_produksi }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-footer">
                        <!--
@if(auth()->user()->hasPermissionTo('edit-pemeriksaan-rice-bites')) <a href="{{ route('pemeriksaan-rice-bites.edit', $data->uuid) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Data
                        </a> @endif
-->
                        <a href="{{ route('pemeriksaan-rice-bites.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                      
                    </div>
                </div>

                <!-- Hidden Delete Form -->
@if(auth()->user()->hasPermissionTo('delete-pemeriksaan-rice-bites'))
                <form id="delete-form" action="{{ route('pemeriksaan-rice-bites.destroy', $data->uuid) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
</div>
        </section>
    </div>
</div>

@endsection
