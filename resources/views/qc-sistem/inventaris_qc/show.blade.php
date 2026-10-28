@extends('layouts.app')

@section('title', 'Detail Checklist Inventaris Peralatan QC')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-eye text-info mr-2"></i>Detail Checklist Inventaris Peralatan QC</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('inventaris-qc.index') }}">Inventaris QC</a></li>
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
                    <!-- Header Info Card -->
                    <div class="card card-info card-outline shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-dark">
                                <i class="fas fa-info-circle text-info mr-2"></i>Informasi Pemeriksaan
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('inventaris-qc.edit', $header->uuid) }}" class="btn btn-warning btn-sm mr-1">
                                    <i class="fas fa-edit mr-1"></i> Edit Data
                                </a>
                                <a href="{{ route('inventaris-qc.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <small class="text-muted d-block font-weight-bold">PLAN</small>
                                    <span class="font-weight-bold text-dark">{{ $header->plan->nama_plan ?? '-' }}</span>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <small class="text-muted d-block font-weight-bold">TANGGAL PEMERIKSAAN</small>
                                    <span class="badge badge-light border text-dark font-weight-bold" style="font-size: 14px;">
                                        <i class="far fa-calendar-alt text-primary mr-1"></i>
                                        {{ \Carbon\Carbon::parse($header->tanggal)->format('d F Y') }}
                                    </span>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <small class="text-muted d-block font-weight-bold">SHIFT</small>
                                    <span class="badge badge-info px-3 py-1 font-weight-bold" style="font-size: 13px;">
                                        Shift {{ $header->shift->shift ?? '-' }}
                                    </span>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <small class="text-muted d-block font-weight-bold">WAKTU PEMERIKSAAN</small>
                                    <span class="badge {{ $header->waktu_pemeriksaan === 'AWAL SHIFT' ? 'badge-primary' : 'badge-warning text-dark' }} px-3 py-1 font-weight-bold" style="font-size: 13px;">
                                        {{ $header->waktu_pemeriksaan }}
                                    </span>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <small class="text-muted d-block font-weight-bold">DIBUAT OLEH</small>
                                    <span class="text-dark font-weight-bold">{{ $header->user->name ?? '-' }}</span>
                                </div>

                                @if($header->catatan_umum)
                                    <div class="col-md-12 mt-2">
                                        <small class="text-muted d-block font-weight-bold">CATATAN UMUM</small>
                                        <div class="p-2 bg-light rounded border text-dark">
                                            {{ $header->catatan_umum }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Per-Area Checklist Tables -->
                    @forelse($groupedDetails as $areaId => $details)
                        @php
                            $areaName = $details->first()->area->area ?? 'Area Tidak Diketahui';
                        @endphp
                        <div class="card card-outline card-primary shadow-sm mb-4">
                            <div class="card-header bg-light py-2">
                                <h3 class="card-title font-weight-bold text-dark">
                                    <i class="fas fa-warehouse text-primary mr-2"></i>Area: {{ $areaName }}
                                    <span class="badge badge-secondary ml-2">{{ $details->count() }} Item Peralatan</span>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped text-center mb-0" style="white-space: nowrap;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 50px;">No</th>
                                                <th class="text-left">Nama Alat</th>
                                                <th style="width: 120px;">Jumlah</th>
                                                <th style="min-width: 250px;">Kondisi</th>
                                                <th class="text-left" style="min-width: 250px;">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($details as $idx => $detail)
                                                <tr>
                                                    <td>{{ $idx + 1 }}</td>
                                                    <td class="text-left font-weight-bold text-dark">
                                                        {{ $detail->nama_alat }}
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light border px-3 py-1 font-weight-bold">
                                                            {{ $detail->jumlah }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $kondisiClass = 'badge-secondary';
                                                            if (str_contains($detail->kondisi, 'Baik') || str_contains($detail->kondisi, 'Bersih')) {
                                                                $kondisiClass = 'badge-success';
                                                            } elseif (str_contains($detail->kondisi, 'Rusak') || str_contains($detail->kondisi, 'Kotor') || str_contains($detail->kondisi, 'Hilang')) {
                                                                $kondisiClass = 'badge-danger';
                                                            } elseif (str_contains($detail->kondisi, 'Habis') || str_contains($detail->kondisi, 'Tidak Tersedia')) {
                                                                $kondisiClass = 'badge-warning text-dark';
                                                            }
                                                        @endphp
                                                        <span class="badge {{ $kondisiClass }} px-3 py-1" style="font-size: 13px;">
                                                            {{ $detail->kondisi ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-left">
                                                        {{ $detail->keterangan ?: '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning">Tidak ada detail peralatan tersimpan pada checklist ini.</div>
                    @endforelse

                </div>
            </div>
        </div>
    </section>
</div>
@endsection
