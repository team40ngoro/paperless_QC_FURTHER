@extends('layouts.app')

@section('title', 'Detail Pemeriksaan Proses Thawing')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail Pemeriksaan Proses Thawing</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('proses-twahing.index') }}">Proses Thawing</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Informasi</h3>
                                <div class="card-tools">
                                    <a href="{{ route('proses-twahing.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
@if(auth()->user()->hasPermissionTo('edit-proses-twahing'))
                                    <a href="{{ route('proses-twahing.edit', $item->uuid) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                @endif
</div>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-3"><strong>Nama Produk</strong><br>{{ $item->produk->nama_produk ?? '-' }}</div>
                                    <div class="col-md-3"><strong>Tanggal</strong><br>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</div>
                                    <div class="col-md-3"><strong>Jam</strong><br>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</div>
                                    <div class="col-md-3"><strong>Shift</strong><br>{{ $item->shift->shift ?? '-' }}</div>
                                    <div class="col-md-3"><strong>Plan</strong><br>{{ $item->plan->nama_plan ?? '-' }}</div>
                                    <div class="col-md-3"><strong>Dibuat Oleh</strong><br>{{ $item->user->name ?? '-' }}</div>
                                    <div class="col-md-3"><strong>Waktu Thawing</strong><br>
                                        {{ $item->waktu_thawing_awal ? \Carbon\Carbon::parse($item->waktu_thawing_awal)->format('H:i') : '-' }}
                                        -
                                        {{ $item->waktu_thawing_akhir ? \Carbon\Carbon::parse($item->waktu_thawing_akhir)->format('H:i') : '-' }}
                                    </div>
                                    <div class="col-md-3"><strong>Total Waktu (Jam)</strong><br>{{ $item->total_waktu_thawing_jam !== null ? (int)$item->total_waktu_thawing_jam : '-' }}</div>
                                </div>

                                <div class="mt-3">
                                    <strong>Catatan</strong><br>
                                    {{ $item->catatan ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Detail Pemeriksaan</h3>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-bordered table-striped" style="white-space:nowrap;">
                                    <thead>
                                        <tr class="text-center">
                                            <th style="width:60px;">No</th>
                                            <th>Nama RM</th>
                                            <th>Kode Produksi</th>
                                            <th>Kondisi Ruang</th>
                                            <th>Waktu Pemeriksaan</th>
                                            <th>Suhu RM (°C)</th>
                                            <th>Suhu Ruang (°C)</th>
                                            <th>Suhu Air Thawing (°C)</th>
                                            <th>Suhu Produk (°C)</th>
                                            <th>Kondisi Produk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($item->details as $d)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>{{ $d->rm->nama_rm ?? '-' }}</td>
                                                <td>{{ $d->kode_produksi ?? '-' }}</td>
                                                <td>{{ $d->kondisi_ruang ?? '-' }}</td>
                                                <td>{{ $d->waktu_pemeriksaan ? \Carbon\Carbon::parse($d->waktu_pemeriksaan)->format('H:i') : '-' }}</td>
                                                <td>{{ $d->suhu_rm ?? '-' }}</td>
                                                <td>{{ $d->suhu_ruang ?? '-' }}</td>
                                                <td>{{ $d->suhu_air_thawing ?? '-' }}</td>
                                                <td>{{ $d->suhu_produk ?? '-' }}</td>
                                                <td>{{ $d->kondisi_produk ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">Tidak ada detail</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
