@extends('layouts.app')

@section('title', 'Detail Data Timbangan')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail Data Timbangan</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('timbangan.index') }}">Data Timbangan</a></li>
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
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-balance-scale mr-1"></i>
                                    Detail Data Timbangan
                                </h3>
                                <!-- <div class="card-tools">
                                    @if(auth()->user()->hasPermissionTo('edit-timbangan'))
                                        <a href="{{ route('timbangan.edit', $data->uuid) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit Data
                                        </a>
                                    @endif
                                    <a href="{{ route('timbangan.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div> -->
                            </div>
                            <!-- /.card-header -->
                            
                            <div class="card-body">
                                <!-- Common Fields Section -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card card-outline card-info">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-info-circle"></i>
                                                    Informasi Umum
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-clock"></i> Shift</label>
                                                            <div class="form-control-plaintext border-bottom">
                                                                <span class="badge badge-primary">{{ $data->shift->shift ?? '-' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-calendar-alt"></i> Tanggal</label>
                                                            <div class="form-control-plaintext border-bottom">
                                                                {{ $data->tanggal ? $data->tanggal->format('d-m-Y') : '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-clock"></i> Pukul</label>
                                                            <div class="form-control-plaintext border-bottom">
                                                                {{ $data->jam ? \Carbon\Carbon::parse($data->jam)->format('H:i') : '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-building"></i> Plan</label>
                                                            <div class="form-control-plaintext border-bottom">
                                                                {{ $data->plan->nama_plan ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-user"></i> Dibuat oleh</label>
                                                            <div class="form-control-plaintext border-bottom">
                                                                {{ $data->user->name ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-calendar-plus"></i> Dibuat pada</label>
                                                            <div class="form-control-plaintext border-bottom">
                                                                {{ $data->created_at ? $data->created_at->format('d/m/Y H:i:s') : '-' }}
                                                            </div>
                                                        </div>
                                                    </div> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Summary Cards -->
                                @php
                                    $entries = $data->entries ?? [];
                                    $totalEntries = count($entries);
                                    $okEntries = 0;
                                    foreach($entries as $entry) {
                                        if(($entry['hasil_pengecekan'] ?? 'ok') === 'ok') $okEntries++;
                                    }
                                    $notOkEntries = $totalEntries - $okEntries;
                                @endphp
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="info-box bg-info">
                                            <span class="info-box-icon"><i class="fas fa-list"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Entries</span>
                                                <span class="info-box-number">{{ $totalEntries }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Status OK</span>
                                                <span class="info-box-number">{{ $okEntries }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box bg-warning">
                                            <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Status Tidak OK</span>
                                                <span class="info-box-number">{{ $notOkEntries }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Repeater Section style -->
                                <ul class="nav nav-tabs" id="verifikasiTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="tab-timbangan" data-toggle="tab" href="#pane-timbangan" role="tab" aria-controls="pane-timbangan" aria-selected="true">Timbangan</a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="verifikasiTabsContent">
                                    <div class="tab-pane fade show active pt-3" id="pane-timbangan" role="tabpanel" aria-labelledby="tab-timbangan">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped text-center" style="white-space: nowrap;">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th style="width:5%">No</th>
                                                        <th>Jenis Timbangan</th>
                                                        <th>Kode Timbangan</th>
                                                        <th>Area</th>
                                                        <th>Target (Gram)</th>
                                                        <th>Hasil 500g</th>
                                                        <th>Hasil 1000g</th>
                                                        <th>Hasil 10000g</th>
                                                        <th style="width:10%">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $rowNo = 1; @endphp
                                                    @forelse($entries as $index => $entry)
                                                        @if(!is_numeric($index)) @continue @endif
                                                        <tr>
                                                            <td>{{ $rowNo++ }}</td>
                                                            <td>{{ $entry['jenis'] ?? '-' }}</td>
                                                            <td>{{ $entry['kode_timbangan'] ?? '-' }}</td>
                                                            <td>{{ $entry['area'] ?? '-' }}</td>
                                                            <td>{{ $entry['gram'] ?? '-' }}</td>
                                                            <td>{{ $entry['hasil_verifikasi_500'] ?? '-' }}</td>
                                                            <td>{{ $entry['hasil_verifikasi_1000'] ?? '-' }}</td>
                                                            <td>{{ $entry['hasil_verifikasi_10000'] ?? '-' }}</td>
                                                            <td>
                                                                <span class="badge {{ ($entry['hasil_pengecekan'] ?? 'ok') === 'ok' ? 'badge-success' : 'badge-danger' }}">
                                                                    {{ ($entry['hasil_pengecekan'] ?? 'ok') === 'ok' ? '✓ OK' : '✗ Tidak OK' }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="9">Tidak ada rincian data.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- CHECK WEIGHER BAG DISPLAY -->
                                @if(!empty($entries['checkweigher_bag']))
                                <div class="card card-outline card-primary mt-4">
                                    <div class="card-header">
                                        <h5 class="card-title font-weight-bold text-primary mb-0">
                                            <i class="fas fa-shopping-bag mr-1"></i> Verifikasi Penggunaan Check Weigher Bag
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($entries['checkweigher_bag'] as $bIdx => $bag)
                                            <div class="col-md-6 mb-3">
                                                <div class="card card-outline card-primary h-100">
                                                    <div class="card-header bg-light py-2">
                                                        <h6 class="font-weight-bold mb-0 text-primary">Data #{{ $bIdx + 1 }} - {{ $bag['line'] ?? 'Line -' }}</h6>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <div class="row mb-2">
                                                            <div class="col-6"><strong>Nama Produk:</strong> {{ $bag['nama_produk'] ?? '-' }}</div>
                                                            <div class="col-6"><strong>Gramase:</strong> {{ $bag['gramase'] ?? '-' }}</div>
                                                            <div class="col-12 mt-1"><strong>Waktu Pemeriksaan:</strong> {{ $bag['waktu_pemeriksaan'] ?? '-' }}</div>
                                                        </div>
                                                        <hr class="my-2">
                                                        <div class="row">
                                                            <div class="col-6 border-right">
                                                                <small class="font-weight-bold text-dark d-block">a. Perbandingan berat (g):</small>
                                                                <small class="d-block text-muted">Sample 1: <strong>{{ $bag['perbandingan_1'] ?? '-' }}</strong></small>
                                                                <small class="d-block text-muted">Sample 2: <strong>{{ $bag['perbandingan_2'] ?? '-' }}</strong></small>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="font-weight-bold text-dark d-block">b. Verifikasi Fungsi:</small>
                                                                <small class="d-block">Under: <span class="badge {{ ($bag['under'] ?? '') == 'OK' ? 'badge-success' : 'badge-danger' }}">{{ $bag['under'] ?? '-' }}</span></small>
                                                                <small class="d-block mt-2">Over: <span class="badge {{ ($bag['over'] ?? '') == 'OK' ? 'badge-success' : 'badge-danger' }}">{{ $bag['over'] ?? '-' }}</span></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- CHECK WEIGHER BOX DISPLAY -->
                                @if(!empty($entries['checkweigher_box']))
                                <div class="card card-outline card-info mt-4">
                                    <div class="card-header">
                                        <h5 class="card-title font-weight-bold text-info mb-0">
                                            <i class="fas fa-box mr-1"></i> Verifikasi Penggunaan Check Weigher Box
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($entries['checkweigher_box'] as $bxIdx => $box)
                                            <div class="col-md-6 mb-3">
                                                <div class="card card-outline card-info h-100">
                                                    <div class="card-header bg-light py-2">
                                                        <h6 class="font-weight-bold mb-0 text-info">Data #{{ $bxIdx + 1 }} - {{ $box['line'] ?? 'Line -' }}</h6>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <div class="row mb-2">
                                                            <div class="col-6"><strong>Nama Produk:</strong> {{ $box['nama_produk'] ?? '-' }}</div>
                                                            <div class="col-6"><strong>Gramase:</strong> {{ $box['gramase'] ?? '-' }}</div>
                                                            <div class="col-12 mt-1"><strong>Waktu Pemeriksaan:</strong> {{ $box['waktu_pemeriksaan'] ?? '-' }}</div>
                                                        </div>
                                                        <hr class="my-2">
                                                        <small class="font-weight-bold text-dark d-block mb-1">Hasil Pengecekan (Verifikasi):</small>
                                                        <div class="d-flex justify-content-between">
                                                            <small>Kurang 1 pack: <span class="badge {{ ($box['kurang_1_pack'] ?? '') == 'OK' ? 'badge-success' : 'badge-danger' }}">{{ $box['kurang_1_pack'] ?? '-' }}</span></small>
                                                            <small>Lebih 1 pack: <span class="badge {{ ($box['lebih_1_pack'] ?? '') == 'OK' ? 'badge-success' : 'badge-danger' }}">{{ $box['lebih_1_pack'] ?? '-' }}</span></small>
                                                            <small>Double item: <span class="badge {{ ($box['double_item'] ?? '') == 'OK' ? 'badge-success' : 'badge-danger' }}">{{ $box['double_item'] ?? '-' }}</span></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Metadata Footer -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card card-outline card-secondary">
                                            <div class="card-body py-2">
                                                <div class="row">
                                                    <!-- <div class="col-md-6">
                                                        <small class="text-muted text-uppercase">UUID Identifikasi</small>
                                                        <p class="mb-0"><code>{{ $data->uuid }}</code></p>
                                                    </div> -->
                                                    <!-- <div class="col-md-6 text-md-right">
                                                        <small class="text-muted text-uppercase">Terakhir Diupdate</small>
                                                        <p class="mb-0 text-dark">{{ $data->updated_at ? $data->updated_at->format('d/m/Y H:i:s') : '-' }}</p>
                                                    </div> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- @if(auth()->user()->hasPermissionTo('edit-timbangan'))
                                            <a href="{{ route('timbangan.edit', $data->uuid) }}" class="btn btn-warning">
                                                <i class="fas fa-edit"></i> Edit Data
                                            </a>
                                        @endif -->
                                        <!-- <a href="{{ route('timbangan.export-pdf', $data->uuid) }}" class="btn btn-danger ml-2" target="_blank">
                                            <i class="fas fa-file-pdf"></i> Cetak PDF
                                        </a> -->
                                        <a href="{{ route('timbangan.index') }}" class="btn btn-md btn-secondary ml-2">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        @if(auth()->user()->hasPermissionTo('delete-timbangan'))
                                            <form action="{{ route('timbangan.destroy', $data->uuid) }}" 
                                                  method="POST" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <!-- <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-trash"></i> Hapus Data
                                                </button> -->
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
</div>
@endsection
