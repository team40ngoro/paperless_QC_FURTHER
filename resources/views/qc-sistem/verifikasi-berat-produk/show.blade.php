@extends('layouts.app')

@section('title', 'Detail Verifikasi Berat Produk')

@section('container')
<!-- Content Header (Page header) -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail Verifikasi Berat Produk Per Tahapan</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('verifikasi-berat-produk.index') }}">Data Verifikasi Berat Produk</a></li>
                            <li class="breadcrumb-item active">Detail Data</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Detail Verifikasi Berat Produk</h3>
                               
                            </div>
                            <div class="card-body">
                    <!-- Basic Information -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-calendar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Tanggal</span>
                                    <span class="info-box-number">{{ $verifikasiBeratProduk->tanggal ? $verifikasiBeratProduk->tanggal->format('d/m/Y H:i') : '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Shift</span>
                                    <span class="info-box-number">{{ $verifikasiBeratProduk->shift->shift ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-box"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Produk</span>
                                    <span class="info-box-number">{{ $verifikasiBeratProduk->produk->nama_produk ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-barcode"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Kode Produksi</span>
                                    <span class="info-box-number">{{ $verifikasiBeratProduk->kode_produksi }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-secondary"><i class="fas fa-weight"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Gramase</span>
                                    <span class="info-box-number">{{ $verifikasiBeratProduk->gramase ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon {{ $verifikasiBeratProduk->jenis_produk_kfc === 'KFC' ? 'bg-primary' : 'bg-secondary' }}">
                                    <i class="fas fa-tag"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Jenis Produk</span>
                                    <span class="info-box-number">{{ $verifikasiBeratProduk->jenis_produk_kfc }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-building"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Plan</span>
                                    <span class="info-box-number">{{ $verifikasiBeratProduk->plan->nama_plan ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                      
                    </div>

                    <!-- Data Sections based on Product Type -->
                    @if($verifikasiBeratProduk->jenis_produk_kfc === 'KFC')
                        <!-- KFC Product Data -->
                        @if($verifikasiBeratProduk->berat_breader)
                            <div class="card mt-3">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="card-title mb-0">Data Breader</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6>Berat Breader:</h6>
                                            <div class="row">
                                                @foreach($verifikasiBeratProduk->berat_breader ?? [] as $index => $berat)
                                                    @if($berat)
                                                        <div class="col-md-3 mb-2">
                                                            <span class="badge badge-info">{{ $berat }} g</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success"><i class="fas fa-calculator"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Rata-rata</span>
                                                    <span class="info-box-number">{{ $verifikasiBeratProduk->rata_rata_breader ?? '-' }} g</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($verifikasiBeratProduk->pickup_breader)
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <strong>Pickup Breader:</strong> {{ $verifikasiBeratProduk->pickup_breader }}%
                                            </div>
                                        </div>
                                    @endif
                                    @if($verifikasiBeratProduk->pickup_total_breader)
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <strong>Pickup Total Breader:</strong> {{ $verifikasiBeratProduk->pickup_total_breader }}%
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($verifikasiBeratProduk->berat_dry_kfc || $verifikasiBeratProduk->berat_wet_kfc)
                            <div class="card mt-3">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="card-title mb-0">Data KFC After Forming</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @if($verifikasiBeratProduk->berat_dry_kfc)
                                            <div class="col-md-6">
                                                <h6>Berat Dry KFC:</h6>
                                                <div class="row">
                                                    @foreach($verifikasiBeratProduk->berat_dry_kfc ?? [] as $berat)
                                                        @if($berat)
                                                            <div class="col-md-6 mb-2">
                                                                <span class="badge badge-warning">{{ $berat }} g</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <p><strong>Rata-rata:</strong> {{ $verifikasiBeratProduk->rata_rata_dry_kfc ?? '-' }} g</p>
                                            </div>
                                        @endif
                                        @if($verifikasiBeratProduk->berat_wet_kfc)
                                            <div class="col-md-6">
                                                <h6>Berat Wet KFC:</h6>
                                                <div class="row">
                                                    @foreach($verifikasiBeratProduk->berat_wet_kfc ?? [] as $berat)
                                                        @if($berat)
                                                            <div class="col-md-6 mb-2">
                                                                <span class="badge badge-primary">{{ $berat }} g</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <p><strong>Rata-rata:</strong> {{ $verifikasiBeratProduk->rata_rata_wet_kfc ?? '-' }} g</p>
                                            </div>
                                        @endif
                                    </div>
                                    @if($verifikasiBeratProduk->pickup_after_forming_kfc)
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <div class="alert alert-primary">
                                                    <strong>Pickup After Forming KFC:</strong> {{ $verifikasiBeratProduk->pickup_after_forming_kfc }}%
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Non-KFC Product Data -->
                        @if($verifikasiBeratProduk->after_forming)
                            <div class="card mt-3">
                                <div class="card-header bg-info text-white">
                                    <h4 class="card-title mb-0">Data After Forming</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6>Berat After Forming:</h6>
                                            <div class="row">
                                                @foreach($verifikasiBeratProduk->after_forming ?? [] as $berat)
                                                    @if($berat)
                                                        <div class="col-md-3 mb-2">
                                                            <span class="badge badge-info">{{ $berat }} g</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success"><i class="fas fa-calculator"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Rata-rata</span>
                                                    <span class="info-box-number">{{ $verifikasiBeratProduk->rata_rata_after_forming ?? '-' }} g</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($verifikasiBeratProduk->hasil_adonan || $verifikasiBeratProduk->suhu_adonan)
                                        <div class="row mt-3">
                                            @if($verifikasiBeratProduk->hasil_adonan)
                                                <div class="col-md-6">
                                                    <div class="alert alert-info">
                                                        <strong>Hasil Pencetakan:</strong> {{ $verifikasiBeratProduk->hasil_adonan }}
                                                    </div>
                                                </div>
                                            @endif
                                            @if($verifikasiBeratProduk->suhu_adonan)
                                                <div class="col-md-6">
                                                    <div class="alert alert-info">
                                                        <strong>Suhu Adonan:</strong> {{ $verifikasiBeratProduk->suhu_adonan }} °C
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- Common sections for both KFC and non-KFC -->
                    @if($verifikasiBeratProduk->berat_predusting)
                        <div class="card mt-3">
                            <div class="card-header bg-primary text-white">
                                <h4 class="card-title mb-0">Data Predusting</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6>Berat Predusting:</h6>
                                        <div class="row">
                                            @foreach($verifikasiBeratProduk->berat_predusting ?? [] as $berat)
                                                @if($berat)
                                                    <div class="col-md-3 mb-2">
                                                        <span class="badge badge-warning">{{ $berat }} g</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-calculator"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Rata-rata</span>
                                                <span class="info-box-number">{{ $verifikasiBeratProduk->rata_rata_predusting ?? '-' }} g</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($verifikasiBeratProduk->pickup_after_forming_predusting)
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <strong>Pickup After Forming Predusting:</strong> {{ $verifikasiBeratProduk->pickup_after_forming_predusting }}%
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($verifikasiBeratProduk->berat_battering)
                        <div class="card mt-3">
                            <div class="card-header bg-primary text-white">
                                <h4 class="card-title mb-0">Data Battering</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6>Berat Battering:</h6>
                                        <div class="row">
                                            @foreach($verifikasiBeratProduk->berat_battering ?? [] as $berat)
                                                @if($berat)
                                                    <div class="col-md-3 mb-2">
                                                        <span class="badge badge-secondary">{{ $berat }} g</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-calculator"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Rata-rata</span>
                                                <span class="info-box-number">{{ $verifikasiBeratProduk->rata_rata_battering ?? '-' }} g</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($verifikasiBeratProduk->pickup_after_predusting_battering)
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <strong>Pickup After Predusting Battering:</strong> {{ $verifikasiBeratProduk->pickup_after_predusting_battering }}%
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($verifikasiBeratProduk->berat_breadering)
                        <div class="card mt-3">
                            <div class="card-header bg-dark text-white">
                                <h4 class="card-title mb-0">Data Breadering</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6>Berat Breadering:</h6>
                                        <div class="row">
                                            @foreach($verifikasiBeratProduk->berat_breadering ?? [] as $berat)
                                                @if($berat)
                                                    <div class="col-md-3 mb-2">
                                                        <span class="badge badge-dark">{{ $berat }} g</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-calculator"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Rata-rata</span>
                                                <span class="info-box-number">{{ $verifikasiBeratProduk->rata_rata_breadering ?? '-' }} g</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($verifikasiBeratProduk->pickup_after_battering_breadering)
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <strong>Pickup After Battering-Breadering:</strong> {{ $verifikasiBeratProduk->pickup_after_battering_breadering }}%
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($verifikasiBeratProduk->berat_fryer_1)
                        <div class="card mt-3">
                            <div class="card-header bg-primary text-white">
                                <h4 class="card-title mb-0">Data Fryer 1</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6>Berat Fryer 1:</h6>
                                        <div class="row">
                                            @foreach($verifikasiBeratProduk->berat_fryer_1 ?? [] as $berat)
                                                @if($berat)
                                                    <div class="col-md-3 mb-2">
                                                        <span class="badge badge-danger">{{ $berat }} g</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-calculator"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Rata-rata</span>
                                                <span class="info-box-number">{{ $verifikasiBeratProduk->rata_rata_fryer_1 ?? '-' }} g</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($verifikasiBeratProduk->pickup_total)
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <strong>Pickup Total:</strong> {{ $verifikasiBeratProduk->pickup_total }}%
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($verifikasiBeratProduk->berat_fryer_2)
                        <div class="card mt-3">
                            <div class="card-header bg-dark text-white">
                                <h4 class="card-title mb-0">Data Fryer 2</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6>Berat Fryer 2:</h6>
                                        <div class="row">
                                            @foreach($verifikasiBeratProduk->berat_fryer_2 ?? [] as $berat)
                                                @if($berat)
                                                    <div class="col-md-3 mb-2">
                                                        <span class="badge badge-dark">{{ $berat }} g</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-calculator"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Rata-rata</span>
                                                <span class="info-box-number">{{ $verifikasiBeratProduk->rata_rata_fryer_2 ?? '-' }} g</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($verifikasiBeratProduk->pickup_total_fryer_2)
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <strong>Pickup Total Fryer 2:</strong> {{ $verifikasiBeratProduk->pickup_total_fryer_2 }}%
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($verifikasiBeratProduk->berat_roasting)
                        <div class="card mt-3">
                            <div class="card-header bg-primary text-white">
                                <h4 class="card-title mb-0">Data Roasting</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6>Berat Roasting:</h6>
                                        <div class="row">
                                            @foreach($verifikasiBeratProduk->berat_roasting ?? [] as $berat)
                                                @if($berat)
                                                    <div class="col-md-3 mb-2">
                                                        <span class="badge badge-primary">{{ $berat }} g</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-calculator"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Rata-rata</span>
                                                <span class="info-box-number">{{ $verifikasiBeratProduk->rata_rata_roasting ?? '-' }} g</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($verifikasiBeratProduk->pickup_after_breadering_roasting)
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <strong>Pickup After Breadering/Fryer 1 - Roasting:</strong> {{ $verifikasiBeratProduk->pickup_after_breadering_roasting }}%
                                        </div>
                                    </div>
                                @endif
                                @if($verifikasiBeratProduk->pickup_total_roasting)
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <strong>Pickup Total Roasting:</strong> {{ $verifikasiBeratProduk->pickup_total_roasting }}%
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($verifikasiBeratProduk->catatan)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Catatan</h4>
                            </div>
                            <div class="card-body">
                                <p>{{ $verifikasiBeratProduk->catatan }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-12">
                            <div class="card-tools">
                                <a href="{{ route('verifikasi-berat-produk.index') }}" class="btn btn-secondary btn-md">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                @if(auth()->user()->hasPermissionTo('edit-verifikasi-berat-produk'))
                                @php
                                    $canEditVbp = true;
                                    $restrictedRoles = config('edit_limit.restricted_role_ids', []);
                                    if (in_array(auth()->user()->id_role, $restrictedRoles)) {
                                        $timeLimitHours = config('edit_limit.time_limit_hours', 8);
                                        $createdAt = \Carbon\Carbon::parse($verifikasiBeratProduk->created_at);
                                        $hoursElapsed = $createdAt->diffInHours(now(), true);
                                        if ($hoursElapsed > $timeLimitHours) {
                                            $canEditVbp = false;
                                        }
                                    }
                                @endphp
                                @if($canEditVbp)
                                <a href="{{ route('verifikasi-berat-produk.edit', $verifikasiBeratProduk->uuid) }}" class="btn btn-warning btn-md">
                                    <i class="fas fa-edit"></i> Edit Data
                                </a>
                                @else
                                <span class="btn btn-secondary btn-md disabled" title="Waktu edit sudah habis (batas 8 jam dari pembuatan data)">
                                    <i class="fas fa-lock"></i> Edit Data <small class="text-danger">(Expired)</small>
                                </span>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
    </div>
    <!-- /.main-panel -->
@endsection
