@extends('layouts.app')

@section('title', 'Detail Data Pemasakan Nasi')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Data Pemasakan Nasi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemasakan-nasi.index') }}">Data Pemasakan Nasi</a></li>
                        <li class="breadcrumb-item active">Detail Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Detail Pemasakan Nasi</h3>
                            <div class="card-tools">
                                <!--
@if(auth()->user()->hasPermissionTo('edit-pemasakan-nasi')) <a href="{{ route('pemasakan-nasi.edit', $pemasakanNasi->uuid) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a> @endif
-->
                                <a href="{{ route('pemasakan-nasi.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-info-circle"></i> Informasi Dasar</h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <th width="40%">Nama Produk:</th>
                                                    <td>{{ $pemasakanNasi->produk->nama_produk ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Shift:</th>
                                                    <td>shift {{ $pemasakanNasi->shift->shift ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tanggal:</th>
                                                    <td>{{ $pemasakanNasi->tanggal->format('d/m/Y H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Kode Produksi:</th>
                                                    <td>{{ $pemasakanNasi->kode_produksi }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Waktu Start:</th>
                                                    <td>{{ $pemasakanNasi->waktu_start }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Waktu Stop:</th>
                                                    <td>{{ $pemasakanNasi->waktu_stop }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Proses:</th>
                                                    <td>{{ $pemasakanNasi->proses }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Waktu:</th>
                                                    <td>{{ $pemasakanNasi->waktu }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Information -->
                                <div class="col-md-6">
                                    <div class="card card-outline card-info">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-chart-line"></i> Status & Kondisi</h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <th width="40%">Status Cooking:</th>
                                                    <td>
                                                        @if($pemasakanNasi->status_cooking)
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-check"></i> Aktif
                                                            </span>
                                                        @else
                                                            <span class="badge badge-danger">
                                                                <i class="fas fa-times"></i> Tidak Aktif
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Sensori Kondisi:</th>
                                                    <td>
                                                        <span class="badge badge-{{ $pemasakanNasi->sensori_kondisi == 'OK' ? 'success' : 'danger' }}">
                                                            {!! App\Models\PemasakanNasi::getOrganoIcon($pemasakanNasi->sensori_kondisi) !!}
                                                            {{ $pemasakanNasi->sensori_kondisi }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Lama Proses:</th>
                                                    <td>{{ $pemasakanNasi->lama_proses }} menit</td>
                                                </tr>
                                                <tr>
                                                    <th>Temp Std 1:</th>
                                                    <td>{{ $pemasakanNasi->temp_std_1 }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Temp Std 2:</th>
                                                    <td>{{ $pemasakanNasi->temp_std_2 }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Temp Std 3:</th>
                                                    <td>{{ $pemasakanNasi->temp_std_3 }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Jenis Bahan & Jumlah -->
                                <div class="col-12">
                                    <div class="card card-outline card-success">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-list"></i> Jenis Bahan & Jumlah</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th width="10%">No</th>
                                                            <th width="45%">Jenis Bahan</th>
                                                            <th width="45%">Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $jenisBahan = $pemasakanNasi->jenis_bahan_array;
                                                            $jumlah = $pemasakanNasi->jumlah_array;
                                                            $maxCount = max(count($jenisBahan), count($jumlah));
                                                        @endphp
                                                        
                                                        @for($i = 0; $i < $maxCount; $i++)
                                                            <tr>
                                                                <td>{{ $i + 1 }}</td>
                                                                <td>{{ $jenisBahan[$i] ?? '-' }}</td>
                                                                <td>{{ ($jumlah[$i] ?? '-') }} kg</td>
                                                            </tr>
                                                        @endfor
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Organoleptic Tests -->
                                <div class="col-12">
                                    <div class="card card-outline card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-eye"></i> Uji Organoleptik</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="info-box">
                                                        <span class="info-box-icon bg-{{ $pemasakanNasi->organo_warna == 'OK' ? 'success' : 'danger' }}">
                                                            {!! App\Models\PemasakanNasi::getOrganoIcon($pemasakanNasi->organo_warna) !!}
                                                        </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Warna</span>
                                                            <span class="info-box-number">{{ $pemasakanNasi->organo_warna }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="info-box">
                                                        <span class="info-box-icon bg-{{ $pemasakanNasi->organo_aroma == 'OK' ? 'success' : 'danger' }}">
                                                            {!! App\Models\PemasakanNasi::getOrganoIcon($pemasakanNasi->organo_aroma) !!}
                                                        </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Aroma</span>
                                                            <span class="info-box-number">{{ $pemasakanNasi->organo_aroma }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="info-box">
                                                        <span class="info-box-icon bg-{{ $pemasakanNasi->organo_rasa == 'OK' ? 'success' : 'danger' }}">
                                                            {!! App\Models\PemasakanNasi::getOrganoIcon($pemasakanNasi->organo_rasa) !!}
                                                        </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Rasa</span>
                                                            <span class="info-box-number">{{ $pemasakanNasi->organo_rasa }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="info-box">
                                                        <span class="info-box-icon bg-{{ $pemasakanNasi->organo_tekstur == 'OK' ? 'success' : 'danger' }}">
                                                            {!! App\Models\PemasakanNasi::getOrganoIcon($pemasakanNasi->organo_tekstur) !!}
                                                        </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Tekstur</span>
                                                            <span class="info-box-number">{{ $pemasakanNasi->organo_tekstur }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Information -->
                                @if($pemasakanNasi->catatan)
                                <div class="col-12">
                                    <div class="card card-outline card-secondary">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-sticky-note"></i> Catatan</h3>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-0">{{ $pemasakanNasi->catatan }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Metadata -->
                                <!-- <div class="col-12">
                                    <div class="card card-outline card-dark">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-info"></i> Informasi Sistem</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                  
                                                </div>
                                                <div class="col-md-12">
                                                    <table class="table table-borderless">
                                                        <tr>
                                                            <th width="40%">Dibuat pada:</th>
                                                            <td>{{ $pemasakanNasi->created_at->format('d/m/Y H:i:s') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Diperbarui pada:</th>
                                                            <td>{{ $pemasakanNasi->updated_at->format('d/m/Y H:i:s') }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
