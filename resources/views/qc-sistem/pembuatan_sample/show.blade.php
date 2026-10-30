@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Verifikasi Pembuatan Sample</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pembuatan-sample.index') }}">Pembuatan Sample</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-vial mr-1"></i> Informasi Sample
                    </h3>
                    <!-- <div class="card-tools">
                        <a href="{{ route('pembuatan-sample.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        @if(auth()->user()->hasPermissionTo('edit-pembuatan-sample'))
                            <a href="{{ route('pembuatan-sample.edit', $item->uuid) }}" class="btn btn-warning btn-sm ml-1">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                        @endif
                    </div> -->
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Kolom Kiri --}}
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th style="width: 40%">Shift</th>
                                    <td>: {{ $item->shift->shift ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Produksi</th>
                                    <td>: {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y H:i:s') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Jam</th>
                                    <td>: {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</td>
                                </tr>
                            </table>
                        </div>

                        {{-- Kolom Kanan --}}
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th style="width: 40%">Kode Form</th>
                                    <td>: {{ $item->kode_form ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Oleh</th>
                                    <td>: {{ $item->createdBy->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Pada</th>
                                    <td>: {{ $item->created_at ? $item->created_at->format('d-M-Y H:i:s') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Terakhir Diubah</th>
                                    <td>: {{ $item->updated_at ? $item->updated_at->format('d-M-Y H:i:s') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Detail Items dari JSON --}}
                    @if($item->items_json)
                    <hr>
                    <h4 class="mb-3">Detail Sample</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>Kode Produksi</th>
                                    <th>Tanggal Expired</th>
                                    <th>Jumlah</th>
                                    <th>Berat Sampel (g)</th>
                                    <th>Gramase (g)</th>
                                    <th>Jenis Sample</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item->items_json as $idx => $sample)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ \App\Models\JenisProduk::find($sample['id_produk'])->nama_produk ?? '-' }}</td>
                                    <td><code>{{ $sample['kode_produksi'] ?? '-' }}</code></td>
                                    <td>{{ $sample['tanggal_expired'] ?? '-' }}</td>
                                    <td>{{ $sample['jumlah'] ?? '-' }}</td>
                                    <td>{{ $sample['berat'] ?? '-' }}</td>
                                    <td>{{ $sample['berat_sampling'] ?? '-' }}</td>
                                    <td><span class="badge badge-info text-uppercase">{{ $sample['jenis_sample'] ?? '-' }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

            <!-- {{-- Card Status Approval --}}
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-check-double mr-1"></i> Status Approval
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        {{-- QC --}}
                        <div class="col-md-4">
                            <div class="info-box shadow-sm">
                                <span class="info-box-icon {{ $item->approved_by_qc ? 'bg-success' : 'bg-secondary' }}">
                                    <i class="fas fa-{{ $item->approved_by_qc ? 'check' : 'clock' }}"></i>
                                </span>
                                <div class="info-box-content text-left">
                                    <span class="info-box-text font-weight-bold">QC</span>
                                    @if($item->approved_by_qc)
                                        <span class="info-box-number text-success" style="font-size:0.85rem">
                                            <i class="fas fa-check-circle"></i> Disetujui
                                        </span>
                                        <small class="d-block text-muted">{{ $item->qcApprover->name ?? '-' }}</small>
                                        <small class="d-block text-muted">{{ $item->qc_approved_at ? $item->qc_approved_at->format('d-M-Y H:i') : '-' }}</small>
                                    @else
                                        <span class="info-box-number text-secondary" style="font-size:0.85rem">
                                            <i class="fas fa-minus-circle"></i> Belum Disetujui
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Produksi --}}
                        <div class="col-md-4">
                            <div class="info-box shadow-sm">
                                <span class="info-box-icon {{ $item->approved_by_produksi ? 'bg-primary' : 'bg-secondary' }}">
                                    <i class="fas fa-{{ $item->approved_by_produksi ? 'check' : 'clock' }}"></i>
                                </span>
                                <div class="info-box-content text-left">
                                    <span class="info-box-text font-weight-bold">Produksi</span>
                                    @if($item->approved_by_produksi)
                                        <span class="info-box-number text-primary" style="font-size:0.85rem">
                                            <i class="fas fa-check-circle"></i> Disetujui
                                        </span>
                                        <small class="d-block text-muted">{{ $item->produksiApprover->name ?? '-' }}</small>
                                        <small class="d-block text-muted">{{ $item->produksi_approved_at ? $item->produksi_approved_at->format('d-M-Y H:i') : '-' }}</small>
                                    @else
                                        <span class="info-box-number text-secondary" style="font-size:0.85rem">
                                            <i class="fas fa-minus-circle"></i> Belum Disetujui
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- SPV --}}
                        <div class="col-md-4">
                            <div class="info-box shadow-sm">
                                <span class="info-box-icon {{ $item->approved_by_spv ? 'bg-dark' : 'bg-secondary' }}">
                                    <i class="fas fa-{{ $item->approved_by_spv ? 'check' : 'clock' }}"></i>
                                </span>
                                <div class="info-box-content text-left">
                                    <span class="info-box-text font-weight-bold">SPV</span>
                                    @if($item->approved_by_spv)
                                        <span class="info-box-number text-dark" style="font-size:0.85rem">
                                            <i class="fas fa-check-circle"></i> Disetujui
                                        </span>
                                        <small class="d-block text-muted">{{ $item->spvApprover->name ?? '-' }}</small>
                                        <small class="d-block text-muted">{{ $item->spv_approved_at ? $item->spv_approved_at->format('d-M-Y H:i') : '-' }}</small>
                                    @else
                                        <span class="info-box-number text-secondary" style="font-size:0.85rem">
                                            <i class="fas fa-minus-circle"></i> Belum Disetujui
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

            {{-- Tombol Aksi Bawah --}}
            <div class="">
                @if(auth()->user()->hasPermissionTo('edit-pembuatan-sample'))
                <a href="{{ route('pembuatan-sample.edit', $item->uuid) }}" class="btn btn-warning">
                    <i class="fas fa-edit mr-1"></i> Edit Data
                </a>
                @endif
                <a href="{{ route('pembuatan-sample.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>

        </div>
    </section>
</div>
@endsection
