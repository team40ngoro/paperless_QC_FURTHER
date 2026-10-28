{{-- filepath: resources/views/qc-sistem/persiapan_bahan_better/show.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-eye text-info"></i>
                        Detail Verifikasi Pembuatan Better
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('persiapan-bahan-better.index') }}">Persiapan Bahan Better</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible shadow-sm border-0">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="icon fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif
            <!-- Card for Basic Information -->
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        Informasi Dasar
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-box text-info"></i>
                                    Nama Produk
                                </label>
                                <p>{{ $item->produk->nama_produk ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-flask text-success"></i>
                                    Nama Better
                                </label>
                                <p>{{ $item->better->nama_better ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-barcode text-warning"></i>
                                    Kode Produksi Larutan Better
                                </label>
                                <p>{{ $item->kode_produksi_produk }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-calendar text-danger"></i>
                                    Tanggal
                                </label>
                                <p>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i:s') : '-' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-clock text-primary"></i>
                                    Shift
                                </label>
                                <p>
                                    @if($item->shift_id == 1)
                                        <span class="badge bg-primary">Shift 1</span>
                                    @elseif($item->shift_id == 2)
                                        <span class="badge bg-success">Shift 2</span>
                                    @elseif($item->shift_id == 3)
                                        <span class="badge bg-secondary">Shift 3</span>
                                    @else
                                        <span class="badge bg-info">{{ $item->shift->shift ?? '-' }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-weight text-secondary"></i>
                                    Gram
                                </label>
                                <p>{{ $item->gramase ? $item->gramase . ' gram' : '-' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-thermometer-half text-info"></i>
                                    Suhu Air (0-10) (°C)
                                </label>
                                <p>{{ $item->suhu_air ?? $item->better_rows[0]['suhu_air'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card for Production Data -->
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i>
                        Data Produksi
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        $rows = $item->better_rows ?? [];
                        if (!is_array($rows)) $rows = [];
                        if (count($rows) === 0) {
                            $rows = [[
                                'master_nama_formula_better' => $item->better->nama_formula_better ?? null,
                                'master_berat' => $item->better->berat ?? null,
                                'kode_produksi_better' => $item->kode_produksi_better ?? null,
                                'suhu_air' => $item->suhu_air ?? null,
                                'sensori' => $item->sensori ?? null,
                            ]];
                        }
                    @endphp
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="bg-gradient-success">
                                <tr class="text-white">
                                    <th class="text-center">Nama Bahan</th>
                                    <th class="text-center">Kode Produksi <small><i>(tidak wajib isi)</i></small></th>
                                    <th class="text-center">Berat (kg)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $r)
                                    <tr>
                                        <td style="white-space: pre-line;">{{ $r['master_nama_formula_better'] ?? '-' }}</td>
                                        <td class="text-center">{{ $r['kode_produksi_better'] ?? '-' }}</td>
                                        <td class="text-center">{{ $r['master_berat'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Tampilkan data STD & Aktual --}}
            @php
                $aktualsList = $item->aktuals && $item->aktuals->count() ? $item->aktuals : collect([null]);
            @endphp
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i>
                        Detail STD & Aktual
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @foreach($aktualsList as $idx => $aktual)
                        <div class="row align-items-center mb-3">
                            <div class="col-md-3"><label class="mb-0">Standar Viscositas (s)</label></div>
                            <div class="col-md-3"><span class="mr-2 text-muted" style="font-size:0.85rem"><i>(otomatis)</i></span><span class="font-weight-bold">{{ $aktual->std->std_viskositas ?? '-' }}</span></div>
                            <div class="col-md-3"><label class="mb-0">Aktual Viscositas (s)</label></div>
                            <div class="col-md-3"><span class="font-weight-bold">{{ $aktual->aktual_vis ?? '-' }}</span></div>
                        </div>
                        
                        <div class="row align-items-center mb-3">
                            <div class="col-md-3"><label class="mb-0">Standar Salinity (%)</label></div>
                            <div class="col-md-3"><span class="mr-2 text-muted" style="font-size:0.85rem"><i>(otomatis)</i></span><span class="font-weight-bold">{{ $aktual->std->std_salinitas ?? '-' }}</span></div>
                            <div class="col-md-3"><label class="mb-0">Aktual Salinity (%)</label></div>
                            <div class="col-md-3"><span class="font-weight-bold">{{ $aktual->aktual_sal ?? '-' }}</span></div>
                        </div>
                            
                        <div class="row align-items-center mb-3">
                            <div class="col-md-3"><label class="mb-0">Suhu Akhir (°C)</label></div>
                            <div class="col-md-9"><span class="font-weight-bold">{{ $aktual->aktual_suhu_air ?? '-' }}</span></div>
                        </div>
                        @if (!$loop->last)
                            <hr>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Card for Sensori -->
            <div class="card card-warning card-outline mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label class="mb-0 font-weight-bold">Sensori</label>
                        </div>
                        <div class="col-md-9">
                            @php $globalSens = $item->sensori ?? $item->better_rows[0]['sensori'] ?? '✔'; @endphp
                            @if($globalSens == '✔')
                                <span class="badge bg-success">✔ OK</span>
                            @elseif($globalSens == '✘')
                                <span class="badge bg-danger">✘ Tidak OK</span>
                            @else
                                {{ $globalSens ?? '-' }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 text-center">
@if(auth()->user()->hasPermissionTo('edit-persiapan-bahan-better'))
                            @php
                                $canEditBetter = true;
                                $restrictedRoles = config('edit_limit.restricted_role_ids', []);
                                if (in_array(auth()->user()->id_role, $restrictedRoles)) {
                                    $timeLimitHours = config('edit_limit.time_limit_hours', 8);
                                    $createdAt = \Carbon\Carbon::parse($item->created_at);
                                    $hoursElapsed = $createdAt->diffInHours(now(), true);
                                    if ($hoursElapsed > $timeLimitHours) {
                                        $canEditBetter = false;
                                    }
                                }
                            @endphp
                            @if($canEditBetter)
                                <a href="{{ route('persiapan-bahan-better.edit', $item->uuid) }}" class="btn btn-warning btn-md mr-3">
                                    <i class="fas fa-edit"></i>
                                    Edit Data
                                </a>
                            @else
                                <span class="btn btn-secondary btn-md mr-3 disabled" title="Waktu edit sudah habis (batas 8 jam dari pembuatan data)">
                                    <i class="fas fa-lock"></i> Edit Data <small class="text-danger">(Expired)</small>
                                </span>
                            @endif
                            @endif
<a href="{{ route('persiapan-bahan-better.index') }}" class="btn btn-secondary btn-md">
                                <i class="fas fa-arrow-left"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </section>
</div>
@endsection