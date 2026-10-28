@extends('layouts.app')

@section('title', 'Detail Pemeriksaan Rheon Machine')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Pemeriksaan Rheon Machine</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-rheon-machine.index') }}">Data Pemeriksaan Rheon Machine</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-eye mr-2"></i>
                                Detail Data Pemeriksaan Rheon Machine
                            </h3>
                            <!-- <div class="card-tools">
@if(auth()->user()->hasPermissionTo('edit-pemeriksaan-rheon-machine'))
                                <a href="{{ route('pemeriksaan-rheon-machine.edit', $pemeriksaanRheonMachine->uuid) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                @endif
<a href="{{ route('pemeriksaan-rheon-machine.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                            </div> -->
                        </div>
                        
                        <div class="card-body">
                            <!-- Informasi Dasar -->
                            <div class="card card-outline card-info mb-4">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Informasi Dasar
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td width="30%"><strong><i class="fas fa-calendar-alt mr-2 text-primary"></i>Tanggal:</strong></td>
                                                    <td>{{ \Carbon\Carbon::parse($pemeriksaanRheonMachine->tanggal)->format('d/m/Y H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong><i class="fas fa-clock mr-2 text-success"></i>Shift:</strong></td>
                                                    <td>
                                                        <span class="badge badge-info">{{ $pemeriksaanRheonMachine->shift->shift ?? '-' }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><i class="fas fa-box mr-2 text-warning"></i>Produk:</strong></td>
                                                    <td>{{ $pemeriksaanRheonMachine->produk->nama_produk ?? '-' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td width="30%"><strong><i class="fas fa-tag mr-2 text-info"></i>Batch:</strong></td>
                                                    <td>
                                                        <span class="badge badge-primary">{{ $pemeriksaanRheonMachine->batch ?? '-' }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><i class="fas fa-clock mr-2 text-secondary"></i>Pukul:</strong></td>
                                                    <td>{{ $pemeriksaanRheonMachine->pukul ?? '-' }}</td>
                                                </tr>
                                               
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Setting Rheon Machine -->
                            <div class="card card-outline card-warning mb-4">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-cogs mr-2"></i>
                                        Setting Rheon Machine
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="info-box bg-primary">
                                                <span class="info-box-icon"><i class="fas fa-circle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Inner</span>
                                                    <span class="info-box-number">{{ $pemeriksaanRheonMachine->inner ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box bg-success">
                                                <span class="info-box-icon"><i class="fas fa-circle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Outer</span>
                                                    <span class="info-box-number">{{ $pemeriksaanRheonMachine->outer ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box bg-warning">
                                                <span class="info-box-icon"><i class="fas fa-link"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Belt</span>
                                                    <span class="info-box-number">{{ $pemeriksaanRheonMachine->belt ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="info-box bg-info">
                                                <span class="info-box-icon"><i class="fas fa-tachometer-alt"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Encrushed Speed</span>
                                                    <span class="info-box-number">{{ $pemeriksaanRheonMachine->extrusion_speed ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box bg-danger">
                                                <span class="info-box-icon"><i class="fas fa-shapes"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Jenis Cetakan</span>
                                                    <span class="info-box-number">{{ $pemeriksaanRheonMachine->jenis_cetakan ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hasil Perhitungan -->
                            <div class="card card-outline card-success mb-4">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-calculator mr-2"></i>
                                        Hasil Perhitungan Berat
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Dough/Adonan -->
                                        <div class="col-md-3">
                                            <div class="small-box bg-primary">
                                                <div class="inner">
                                                    <h3>{{ number_format($pemeriksaanRheonMachine->jumlah_dough, 0) }}</h3>
                                                    <p>Jumlah Dough</p>
                                                    <small>Rata-rata: {{ number_format($pemeriksaanRheonMachine->rata_rata_dough, 2) }}g</small>
                                                </div>
                                                <div class="icon">
                                                    <i class="fas fa-bread-slice"></i>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Filler -->
                                        <div class="col-md-3">
                                            <div class="small-box bg-success">
                                                <div class="inner">
                                                    <h3>{{ number_format($pemeriksaanRheonMachine->jumlah_filler, 0) }}</h3>
                                                    <p>Jumlah Filler</p>
                                                    <small>Rata-rata: {{ number_format($pemeriksaanRheonMachine->rata_rata_filler, 2) }}g</small>
                                                </div>
                                                <div class="icon">
                                                    <i class="fas fa-cookie-bite"></i>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- After Forming -->
                                        <div class="col-md-3">
                                            <div class="small-box bg-warning">
                                                <div class="inner">
                                                    <h3>{{ number_format($pemeriksaanRheonMachine->jumlah_after_forming, 0) }}</h3>
                                                    <p>Jumlah After Forming</p>
                                                    <small>Rata-rata: {{ number_format($pemeriksaanRheonMachine->rata_rata_after_forming, 2) }}g</small>
                                                </div>
                                                <div class="icon">
                                                    <i class="fas fa-shapes"></i>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- After Frying -->
                                        <div class="col-md-3">
                                            <div class="small-box bg-danger">
                                                <div class="inner">
                                                    <h3>{{ number_format($pemeriksaanRheonMachine->jumlah_after_frying, 0) }}</h3>
                                                    <p>Jumlah After Frying</p>
                                                    <small>Rata-rata: {{ number_format($pemeriksaanRheonMachine->rata_rata_after_frying, 2) }}g</small>
                                                </div>
                                                <div class="icon">
                                                    <i class="fas fa-fire"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Data Berat -->
                            <div class="row">
                                <!-- Dough/Adonan Data -->
                                <div class="col-md-6">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-bread-slice mr-2"></i>
                                                Data Dough/Adonan
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            @if(!empty($pemeriksaanRheonMachine->berat_dough_adonan_array))
                                                @foreach($pemeriksaanRheonMachine->berat_dough_adonan_array as $sectionIndex => $sectionData)
                                                    @if(is_array($sectionData) && !empty($sectionData))
                                                        <div class="mb-3">
                                                            <h6 class="text-primary">Section {{ $sectionIndex + 1 }}:</h6>
                                                            <div class="d-flex flex-wrap">
                                                                @foreach($sectionData as $value)
                                                                    <span class="badge badge-primary mr-1 mb-1">{{ $value }}g</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                <p class="text-muted text-center">Tidak ada data dough/adonan</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Filler Data -->
                                <div class="col-md-6">
                                    <div class="card card-outline card-success">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-cookie-bite mr-2"></i>
                                                Data Filler
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            @if(!empty($pemeriksaanRheonMachine->berat_filler_array))
                                                @foreach($pemeriksaanRheonMachine->berat_filler_array as $sectionIndex => $sectionData)
                                                    @if(is_array($sectionData) && !empty($sectionData))
                                                        <div class="mb-3">
                                                            <h6 class="text-success">Section {{ $sectionIndex + 1 }}:</h6>
                                                            <div class="d-flex flex-wrap">
                                                                @foreach($sectionData as $value)
                                                                    <span class="badge badge-success mr-1 mb-1">{{ $value }}g</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                <p class="text-muted text-center">Tidak ada data filler</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- After Forming Data -->
                                <div class="col-md-6">
                                    <div class="card card-outline card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-shapes mr-2"></i>
                                                Data After Forming
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            @if(!empty($pemeriksaanRheonMachine->berat_after_forming_array))
                                                @foreach($pemeriksaanRheonMachine->berat_after_forming_array as $sectionIndex => $sectionData)
                                                    @if(is_array($sectionData) && !empty($sectionData))
                                                        <div class="mb-3">
                                                            <h6 class="text-warning">Section {{ $sectionIndex + 1 }}:</h6>
                                                            <div class="d-flex flex-wrap">
                                                                @foreach($sectionData as $value)
                                                                    <span class="badge badge-warning mr-1 mb-1">{{ $value }}g</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                <p class="text-muted text-center">Tidak ada data after forming</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- After Frying Data -->
                                <div class="col-md-6">
                                    <div class="card card-outline card-danger">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-fire mr-2"></i>
                                                Data After Frying
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            @if(!empty($pemeriksaanRheonMachine->berat_after_frying_array))
                                                @foreach($pemeriksaanRheonMachine->berat_after_frying_array as $sectionIndex => $sectionData)
                                                    @if(is_array($sectionData) && !empty($sectionData))
                                                        <div class="mb-3">
                                                            <h6 class="text-danger">Section {{ $sectionIndex + 1 }}:</h6>
                                                            <div class="d-flex flex-wrap">
                                                                @foreach($sectionData as $value)
                                                                    <span class="badge badge-danger mr-1 mb-1">{{ $value }}g</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                <p class="text-muted text-center">Tidak ada data after frying</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Catatan -->
                            @if($pemeriksaanRheonMachine->catatan)
                                <div class="card card-outline card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-sticky-note mr-2"></i>
                                            Catatan
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $pemeriksaanRheonMachine->catatan }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="card-footer">
                            <!--
@if(auth()->user()->hasPermissionTo('edit-pemeriksaan-rheon-machine')) <a href="{{ route('pemeriksaan-rheon-machine.edit', $pemeriksaanRheonMachine->uuid) }}" class="btn btn-warning btn-md">
                                <i class="fas fa-edit mr-1"></i> Edit Data
                            </a> @endif
-->
                            <a href="{{ route('pemeriksaan-rheon-machine.index') }}" class="btn btn-secondary btn-md">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
