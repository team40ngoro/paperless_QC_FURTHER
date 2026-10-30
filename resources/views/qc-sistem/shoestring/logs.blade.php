@extends('layouts.app')

@section('title', 'Riwayat Perubahan Data - Shoestring')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-history text-warning"></i> Riwayat Perubahan Data</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shoestring.index') }}">Shoestring</a></li>
                        <li class="breadcrumb-item active">Riwayat Perubahan</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Info Data -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i> Informasi Data
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Nama Produsen:</strong><br>
                                    <span class="badge badge-info">{{ $item->nama_produsen ?? '-' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Kode Produksi:</strong><br>
                                    <span class="badge badge-secondary">{{ $item->kode_produksi ?? '-' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Shift:</strong><br>
                                    <span class="badge badge-primary">{{ $item->shift->shift ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Log -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history"></i> Riwayat Perubahan Data
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('shoestring.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(isset($logs) && $logs->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 5%;">No</th>
                                                <th style="width: 15%;">Tanggal & Waktu Update</th>
                                                <th style="width: 10%;">Role</th>
                                                <th style="width: 10%;">Aksi</th>
                                                <th style="width: 20%;">Field yang Diubah</th>
                                                <th style="width: 25%;">Detail Perubahan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($logs as $index => $log)
                                            <tr>
                                                <td>{{ $logs->firstItem() + $index }}</td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $log->created_at->format('d/m/Y') }}<br>
                                                        {{ $log->created_at->format('H:i:s') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">{{ $log->user_role }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-edit"></i> {{ ucfirst($log->aksi) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($log->field_yang_diubah && count($log->field_yang_diubah) > 0)
                                                        @foreach($log->field_yang_diubah as $field)
                                                            <span class="text-primary">{{ $log->getNamaFieldSingle($field) }}</span>
                                                            @if(!$loop->last)<br>@endif
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="change-details">
                                                        @if($log->nilai_lama && $log->nilai_baru)
                                                            @foreach($log->field_yang_diubah as $field)
                                                                @php
                                                                    $nilaiLama = $log->nilai_lama[$field] ?? 'Kosong';
                                                                    $nilaiBaru = $log->nilai_baru[$field] ?? 'Kosong';
                                                                    $namaField = $log->getNamaFieldSingle($field);
                                                                @endphp
                                                                <div class="mb-1">
                                                                    <strong>{{ $namaField }}:</strong><br>
                                                                    <span class="text-danger">{{ $nilaiLama }}</span> 
                                                                    <i class="fas fa-arrow-right text-muted"></i> 
                                                                    <span class="text-success">{{ $nilaiBaru }}</span>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Pagination -->
                                <div class="d-flex justify-content-center">
                                    {{ $logs->links('pagination.simple') }}
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum Ada Riwayat Perubahan</h5>
                                    <p class="text-muted">Data ini belum pernah diubah sejak dibuat.</p>
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
