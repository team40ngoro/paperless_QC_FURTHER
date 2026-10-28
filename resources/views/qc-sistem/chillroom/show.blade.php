@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-snowflake text-info"></i> Detail Pemeriksaan Kedatangan Bahan Baku</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('chillroom.index') }}">Chillroom</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-bold"><i class="fas fa-info-circle text-info mr-2"></i> Informasi Detail</h3>
                            <div class="card-tools">
                                <a href="{{ route('chillroom.index') }}" class="btn btn-default btn-sm shadow-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-info border-bottom pb-2 mb-3"><i class="fas fa-calendar-alt mr-2"></i> Data Waktu & Shift</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th style="width: 40%">Tanggal</th>
                                            <td>: {{ $chillroom->tanggal ? \Carbon\Carbon::parse($chillroom->tanggal)->format('d F Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shift</th>
                                            <td>: <span class="badge badge-secondary">Shift {{ $chillroom->datashift->shift ?? '-' }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Jam Kedatangan</th>
                                            <td>: {{ $chillroom->jam_kedatangan ? \Carbon\Carbon::parse($chillroom->jam_kedatangan)->format('H:i') : '-' }} WIB</td>
                                        </tr>
                                    </table>

                                    <h5 class="text-info border-bottom pb-2 mb-3 mt-4"><i class="fas fa-box mr-2"></i> Data Raw Material</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th style="width: 40%">Nama RM</th>
                                            <td>: <strong>{{ $chillroom->nama_rm ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Kode Produksi</th>
                                            <td>: <code>{{ $chillroom->kode_produksi ?? '-' }}</code></td>
                                        </tr>
                                        <tr>
                                            <th>Berat / Jumlah</th>
                                            <td>: {{ $chillroom->berat ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Suhu</th>
                                            <td>
                                                @if($chillroom->suhu == 0 && $chillroom->berat == '-')
                                                    <span class="badge badge-warning"><i class="fas fa-snowflake"></i> FROZEN</span>
                                                @else
                                                    <span class="badge {{ $chillroom->suhu > 5 ? 'badge-danger' : 'badge-success' }}">{{ $chillroom->suhu ?? '0' }}°C</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="text-info border-bottom pb-2 mb-3"><i class="fas fa-check-circle mr-2"></i> Status Pemeriksaan</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th style="width: 40%">Sensori</th>
                                            <td>: 
                                                @if(in_array($chillroom->sensori, ['✔', '✓', 'OK', 'ok']))
                                                    <span class="badge badge-success">OK</span>
                                                @elseif(in_array($chillroom->sensori, ['✘', 'X', 'x', 'tidak ok', 'Tidak OK', 'NG']))
                                                    <span class="badge badge-danger">Tidak OK</span>
                                                @else
                                                    <span>{{ $chillroom->sensori ?? '-' }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Kemasan</th>
                                            <td>: 
                                                @if(in_array($chillroom->kemasan, ['✔', '✓', 'OK', 'ok']))
                                                    <span class="badge badge-success">OK</span>
                                                @elseif(in_array($chillroom->kemasan, ['✘', 'X', 'x', 'tidak ok', 'Tidak OK', 'NG']))
                                                    <span class="badge badge-danger">Tidak OK</span>
                                                @else
                                                    <span>{{ $chillroom->kemasan ?? '-' }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status RM</th>
                                            <td>: <span class="badge {{ $chillroom->status_rm == 'OK' ? 'badge-success' : 'badge-warning' }}">{{ $chillroom->status_rm ?? '-' }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Catatan RM</th>
                                            <td>: {{ $chillroom->catatan_rm ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Keterangan Lain</th>
                                            <td>: {{ $chillroom->keterangan ?? '-' }}</td>
                                        </tr>
                                    </table>

                                    <h5 class="text-info border-bottom pb-2 mb-3 mt-4"><i class="fas fa-user-check mr-2"></i> Verifikasi</h5>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="p-2 border rounded {{ $chillroom->approved_by_qc ? 'bg-success-light' : 'bg-light' }}">
                                                <small class="d-block text-muted">QC</small>
                                                <i class="fas {{ $chillroom->approved_by_qc ? 'fa-check-circle text-success' : 'fa-clock text-muted' }} fa-lg my-2"></i>
                                                <div class="small">{{ $chillroom->qcApprover->name ?? 'Belum' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 border rounded {{ $chillroom->approved_by_produksi ? 'bg-primary-light' : 'bg-light' }}">
                                                <small class="d-block text-muted">Produksi</small>
                                                <i class="fas {{ $chillroom->approved_by_produksi ? 'fa-check-circle text-primary' : 'fa-clock text-muted' }} fa-lg my-2"></i>
                                                <div class="small">{{ $chillroom->produksiApprover->name ?? 'Belum' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 border rounded {{ $chillroom->approved_by_spv ? 'bg-dark-light' : 'bg-light' }}">
                                                <small class="d-block text-muted">SPV</small>
                                                <i class="fas {{ $chillroom->approved_by_spv ? 'fa-check-circle text-dark' : 'fa-clock text-muted' }} fa-lg my-2"></i>
                                                <div class="small">{{ $chillroom->spvApprover->name ?? 'Belum' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php
                                $nilaiSampel = json_decode($chillroom->nilai_jumlah_rm, true);
                                $entryKey = array_key_first($nilaiSampel ?? []);
                                $samples = $entryKey ? ($nilaiSampel[$entryKey] ?? []) : [];
                            @endphp

                            @if(!empty($samples))
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5 class="text-info border-bottom pb-2 mb-3"><i class="fas fa-weight mr-2"></i> Detail Sampel Berat</h5>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($samples as $index => $val)
                                                <div class="sample-box p-2 border rounded text-center mr-2 mb-2" style="min-width: 80px; background: #f8f9fa;">
                                                    <small class="text-muted d-block">Sampel {{ $index + 1 }}</small>
                                                    <strong>{{ $val }}</strong>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="small text-muted">
                                    Input oleh: <strong>{{ $chillroom->user->name ?? '-' }}</strong> pada {{ $chillroom->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="btn-group">
                                    @if(auth()->user()->hasPermissionTo('edit-chillroom'))
                                        <a href="{{ route('chillroom.edit', $chillroom->uuid) }}" class="btn btn-warning shadow-sm">
                                            <i class="fas fa-edit mr-1"></i> Edit Data
                                        </a>
                                    @endif
                                    <a href="{{ route('chillroom.logs', $chillroom->uuid) }}" class="btn btn-info ml-2 shadow-sm">
                                        <i class="fas fa-history mr-1"></i> Lihat Log
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .bg-success-light { background-color: rgba(40, 167, 69, 0.1); border-color: rgba(40, 167, 69, 0.2) !important; }
    .bg-primary-light { background-color: rgba(0, 123, 255, 0.1); border-color: rgba(0, 123, 255, 0.2) !important; }
    .bg-dark-light { background-color: rgba(52, 58, 64, 0.1); border-color: rgba(52, 58, 64, 0.2) !important; }
</style>
@endsection
