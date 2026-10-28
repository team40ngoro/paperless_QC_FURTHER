@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-eye text-primary"></i> Detail Pemeriksaan Metal Detector</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('input-metal-detector.index') }}">Metal Detector</a></li>
                        <li class="breadcrumb-item active">Detail Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Data</h3>
                        </div>
                        <div class="card-body">
                            <!-- Informasi Umum -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar mr-1"></i>Tanggal</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') : '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-clock mr-1"></i>Jam</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Shift</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $item->shift->shift ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Line</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $item->line ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nama Produk</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $item->produk->nama_produk ?? '-' }} {{ $item->berat_produk ?? '-' }} gram" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kode Produksi</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $item->kode_produksi ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Keterangan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $item->keterangan ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5 class="text-primary"><i class="fas fa-search mr-2"></i>Hasil Deteksi</h5>
                                    <hr>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="text-center table table-bordered table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center bg-info" colspan="3">Fe 1.5 mm</th>
                                            <th class="text-center bg-success" colspan="3">Non Fe 2 mm</th>
                                            <th class="text-center bg-warning" colspan="3">SUS 316 2.5 mm</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center bg-info">Depan</th>
                                            <th class="text-center bg-info">Tengah</th>
                                            <th class="text-center bg-info">Belakang</th>
                                            <th class="text-center bg-success">Depan</th>
                                            <th class="text-center bg-success">Tengah</th>
                                            <th class="text-center bg-success">Belakang</th>
                                            <th class="text-center bg-warning">Depan</th>
                                            <th class="text-center bg-warning">Tengah</th>
                                            <th class="text-center bg-warning">Belakang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center bg-info">{{ $item->fe_depan_aktual ?? '-' }}</td>
                                            <td class="text-center bg-info">{{ $item->fe_tengah_aktual ?? '-' }}</td>
                                            <td class="text-center bg-info">{{ $item->fe_belakang_aktual ?? '-' }}</td>
                                            <td class="text-center bg-success">{{ $item->non_fe_depan_aktual ?? '-' }}</td>
                                            <td class="text-center bg-success">{{ $item->non_fe_tengah_aktual ?? '-' }}</td>
                                            <td class="text-center bg-success">{{ $item->non_fe_belakang_aktual ?? '-' }}</td>
                                            <td class="text-center bg-warning">{{ $item->sus_depan_aktual ?? '-' }}</td>
                                            <td class="text-center bg-warning">{{ $item->sus_tengah_aktual ?? '-' }}</td>
                                            <td class="text-center bg-warning">{{ $item->sus_belakang_aktual ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('input-metal-detector.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    @if(auth()->user()->hasPermissionTo('edit-input-metal-detector'))
                                        <a href="{{ route('input-metal-detector.edit', $item->uuid) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Edit Data
                                        </a>
                                    @endif
                                    @if(auth()->user()->hasPermissionTo('view-input-metal-detector'))
                                        <a href="{{ route('input-metal-detector.logs', $item->uuid) }}" class="btn btn-info">
                                            <i class="fas fa-history"></i> Lihat Riwayat
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
