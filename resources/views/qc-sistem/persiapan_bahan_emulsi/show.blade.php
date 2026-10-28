@extends('layouts.app')

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
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->shift->shift ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    @if($data->shift_id == 1)
                                                        <span class="badge bg-primary">Shift 1</span>
                                                    @elseif($data->shift_id == 2)
                                                        <span class="badge bg-success">Shift 2</span>
                                                    @else
                                                        <span class="badge bg-secondary">Shift {{ $data->shift_id }}</span>
                                                    @endif
                                                </span>
                                            </div>
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
