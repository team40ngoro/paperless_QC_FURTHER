@extends('layouts.app')

@section('title', 'Log Verifikasi QC - Seasoning')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-clipboard-check text-success"></i> Log Verifikasi QC</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('seasoning.index') }}">Seasoning</a></li>
                        <li class="breadcrumb-item active">Log Verifikasi QC</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle"></i> Informasi Data</h3>
                            <div class="card-tools">
                                <a href="{{ route('seasoning.show', $item->uuid) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Detail
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Nama RM:</strong><br>
                                    <span class="badge badge-info">{{ $item->nama_rm ?? '-' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Kode Produksi:</strong><br>
                                    <code>{{ $item->kode_produksi ?? '-' }}</code>
                                </div>
                                <div class="col-md-4">
                                    <strong>Tanggal / Shift:</strong><br>
                                    <span class="badge badge-secondary">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</span>
                                    <span class="badge badge-primary">Shift {{ $item->shift->shift ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-history"></i> Riwayat Verifikasi QC</h3>
                        </div>
                        <div class="card-body">
                            @if($verifications->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 5%;">No</th>
                                                <th style="width: 13%;">Tanggal & Waktu</th>
                                                <th style="width: 10%;">Status</th>
                                                <th style="width: 14%;">Data (Waktu/RM/Kode/Berat)</th>
                                                <th style="width: 8%;">Sensori</th>
                                                <th style="width: 8%;">Kemasan</th>
                                                <th style="width: 8%;">Kondisi Fisik</th>
                                                <th style="width: 16%;">Keterangan</th>
                                                <th style="width: 12%;">Diverifikasi Oleh</th>
                                                <th style="width: 10%;">Role</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($verifications as $index => $verification)
                                                <tr>
                                                    <td>{{ $verifications->firstItem() + $index }}</td>
                                                    <td>
                                                        <small class="text-muted">
                                                            {{ $verification->verified_at ? $verification->verified_at->format('d/m/Y') : '-' }}<br>
                                                            {{ $verification->verified_at ? $verification->verified_at->format('H:i:s') : '-' }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        @if($verification->overall_status === 'OK')
                                                            <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Oke</span>
                                                        @else
                                                            <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> Tidak Oke</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $verification->data_status === 'OK' ? 'badge-success' : 'badge-danger' }}">
                                                            {{ $verification->data_status === 'OK' ? 'Oke' : 'Tidak Oke' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $verification->sensori_status === 'OK' ? 'badge-success' : 'badge-danger' }}">
                                                            {{ $verification->sensori_status === 'OK' ? 'Oke' : 'Tidak Oke' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $verification->kemasan_status === 'OK' ? 'badge-success' : 'badge-danger' }}">
                                                            {{ $verification->kemasan_status === 'OK' ? 'Oke' : 'Tidak Oke' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($verification->kondisi_fisik_status === null)
                                                            <span class="badge badge-light border">-</span>
                                                        @else
                                                            <span class="badge {{ $verification->kondisi_fisik_status === 'OK' ? 'badge-success' : 'badge-danger' }}">
                                                                {{ $verification->kondisi_fisik_status === 'OK' ? 'Oke' : 'Tidak Oke' }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $verification->note ?: '-' }}</td>
                                                    <td>{{ $verification->verifiedBy->name ?? '-' }}</td>
                                                    <td><span class="badge badge-info">{{ $verification->verifiedBy->role ?? 'QC' }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center">
                                    {{ $verifications->links('pagination::bootstrap-4') }}
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum Ada Verifikasi QC</h5>
                                    <p class="text-muted">Data ini belum pernah diverifikasi oleh QC.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
