@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail GMP Karyawan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gmp-karyawan.index') }}">GMP Karyawan</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-1"></i>
                                Informasi Batch
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('gmp-karyawan.export-pdf', $gmpKaryawan->uuid) }}" class="btn btn-danger btn-sm">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF
                                </a>
                                <a href="{{ route('gmp-karyawan.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <strong><i class="fas fa-calendar-alt mr-1"></i> Tanggal</strong>
                                    <p class="text-muted">
                                        {{ $gmpKaryawan->tanggal ? \Carbon\Carbon::parse($gmpKaryawan->tanggal)->format('d F Y') : '-' }}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <strong><i class="fas fa-clock mr-1"></i> Shift</strong>
                                    <p class="text-muted">
                                        Shift {{ $gmpKaryawan->shift->shift ?? '-' }}
                                    </p>
                                </div>
                                <!-- <div class="col-md-4">
                                    <strong><i class="fas fa-file-alt mr-1"></i> Kode Form</strong>
                                    <p class="text-muted">
                                        {{ $gmpKaryawan->kode_form ?? '-' }}
                                    </p>
                                </div> -->
                            </div>

                            <hr>

                            <h4 class="mt-4 mb-3">Daftar Pemeriksaan Karyawan</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>Jam</th>
                                            <th>Area</th>
                                            <th>Nama Karyawan</th>
                                            <th>Temuan</th>
                                            <th>Verifikasi</th>
                                            <th>Keterangan & Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($gmpKaryawan->entries && is_array($gmpKaryawan->entries))
                                            @foreach($gmpKaryawan->entries as $index => $entry)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td class="text-center">{{ $entry['jam'] ?? '-' }}</td>
                                                    <td>{{ $areas[$entry['id_area']] ?? '-' }}</td>
                                                    <td>{{ $entry['nama_karyawan'] ?? '-' }}</td>
                                                    <td class="text-center">
                                                        @php
                                                            $temuanLabel = $temuanOptions[$entry['temuan_ketidaksesuaian']] ?? $entry['temuan_ketidaksesuaian'] ?? '-';
                                                        @endphp
                                                        {{ $temuanLabel }}
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $verif = $entry['verifikasi'] ?? null;
                                                            $badge = $verif === 'ok' ? 'success' : ($verif === 'tidak_ok' ? 'danger' : 'secondary');
                                                            $text = $verif === 'ok' ? 'OK' : ($verif === 'tidak_ok' ? 'Tidak OK' : '-');
                                                        @endphp
                                                        <span class="badge badge-{{ $badge }}">{{ $text }}</span>
                                                    </td>
                                                    <td>
                                                        @if(isset($entry['keterangan']))
                                                            <small class="d-block"><strong>Ket:</strong> {{ $entry['keterangan'] }}</small>
                                                        @endif
                                                        @if(isset($entry['tindakan_koreksi']))
                                                            <small class="d-block"><strong>Tindakan:</strong> {{ $entry['tindakan_koreksi'] }}</small>
                                                        @endif
                                                        @if(($entry['verifikasi'] ?? '') === 'tidak_ok')
                                                            <div class="mt-2 p-1 bg-light border-left border-warning">
                                                                <small class="font-weight-bold text-warning">Koreksi Lanjutan:</small><br>
                                                                <small><strong>Status:</strong> {{ strtoupper($entry['koreksi_lanjutan'] ?? '-') }}</small><br>
                                                                <small><strong>Catatan:</strong> {{ $entry['keterangan_koreksi_lanjutan'] ?? '-' }}</small><br>
                                                                <small><strong>Tindakan:</strong> {{ $entry['tindakan_koreksi_lanjutan'] ?? '-' }}</small>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            {{-- Backward compatibility for old single-row data --}}
                                            <tr>
                                                <td class="text-center">1</td>
                                                <td class="text-center">{{ $gmpKaryawan->jam ? \Carbon\Carbon::parse($gmpKaryawan->jam)->format('H:i') : '-' }}</td>
                                                <td>{{ $gmpKaryawan->area->area ?? '-' }}</td>
                                                <td>{{ $gmpKaryawan->nama_karyawan ?? '-' }}</td>
                                                <td class="text-center">{{ $gmpKaryawan->temuan_ketidaksesuaian_label ?? '-' }}</td>
                                                <td class="text-center">
                                                    @php
                                                        $verif = $gmpKaryawan->verifikasi ?? null;
                                                        $badge = $verif === 'ok' ? 'success' : ($verif === 'tidak_ok' ? 'danger' : 'secondary');
                                                        $text = $verif === 'ok' ? 'OK' : ($verif === 'tidak_ok' ? 'Tidak OK' : '-');
                                                    @endphp
                                                    <span class="badge badge-{{ $badge }}">{{ $text }}</span>
                                                </td>
                                                <td>
                                                    @if($gmpKaryawan->keterangan)
                                                        <small class="d-block"><strong>Ket:</strong> {{ $gmpKaryawan->keterangan }}</small>
                                                    @endif
                                                    @if($gmpKaryawan->tindakan_koreksi)
                                                        <small class="d-block"><strong>Tindakan:</strong> {{ $gmpKaryawan->tindakan_koreksi }}</small>
                                                    @endif
                                                    @if($gmpKaryawan->verifikasi === 'tidak_ok')
                                                        <div class="mt-2 p-1 bg-light border-left border-warning">
                                                            <small class="font-weight-bold text-warning">Koreksi Lanjutan:</small><br>
                                                            <small><strong>Status:</strong> {{ strtoupper($gmpKaryawan->koreksi_lanjutan ?? '-') }}</small><br>
                                                            <small><strong>Catatan:</strong> {{ $gmpKaryawan->keterangan_koreksi_lanjutan ?? '-' }}</small><br>
                                                            <small><strong>Tindakan:</strong> {{ $gmpKaryawan->tindakan_koreksi_lanjutan ?? '-' }}</small>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Approval Section --}}
            <!-- <div class="row">
                <div class="col-md-4">
                    <div class="card {{ $gmpKaryawan->approved_by_qc ? 'card-success' : 'card-secondary' }} card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Persetujuan QC</h3>
                        </div>
                        <div class="card-body text-center">
                            @if($gmpKaryawan->approved_by_qc)
                                <div class="text-success mb-2">
                                    <i class="fas fa-check-circle fa-3x"></i>
                                </div>
                                <strong>{{ $gmpKaryawan->qcApprover->name ?? 'QC' }}</strong><br>
                                <small>{{ $gmpKaryawan->approved_by_qc_at ? \Carbon\Carbon::parse($gmpKaryawan->approved_by_qc_at)->format('d/m/Y H:i') : '' }}</small>
                            @else
                                <div class="text-muted mb-2">
                                    <i class="fas fa-clock fa-3x"></i>
                                </div>
                                <span class="badge badge-secondary">Menunggu Persetujuan</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card {{ $gmpKaryawan->approved_by_produksi ? 'card-primary' : 'card-secondary' }} card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Persetujuan Produksi</h3>
                        </div>
                        <div class="card-body text-center">
                            @if($gmpKaryawan->approved_by_produksi)
                                <div class="text-primary mb-2">
                                    <i class="fas fa-check-circle fa-3x"></i>
                                </div>
                                <strong>{{ $gmpKaryawan->produksiApprover->name ?? 'Produksi' }}</strong><br>
                                <small>{{ $gmpKaryawan->approved_by_produksi_at ? \Carbon\Carbon::parse($gmpKaryawan->approved_by_produksi_at)->format('d/m/Y H:i') : '' }}</small>
                            @else
                                <div class="text-muted mb-2">
                                    <i class="fas fa-clock fa-3x"></i>
                                </div>
                                <span class="badge badge-secondary">Menunggu Persetujuan</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card {{ $gmpKaryawan->approved_by_spv ? 'card-dark' : 'card-secondary' }} card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Persetujuan SPV</h3>
                        </div>
                        <div class="card-body text-center">
                            @if($gmpKaryawan->approved_by_spv)
                                <div class="text-dark mb-2">
                                    <i class="fas fa-check-circle fa-3x"></i>
                                </div>
                                <strong>{{ $gmpKaryawan->spvApprover->name ?? 'SPV' }}</strong><br>
                                <small>{{ $gmpKaryawan->approved_by_spv_at ? \Carbon\Carbon::parse($gmpKaryawan->approved_by_spv_at)->format('d/m/Y H:i') : '' }}</small>
                            @else
                                <div class="text-muted mb-2">
                                    <i class="fas fa-clock fa-3x"></i>
                                </div>
                                <span class="badge badge-secondary">Menunggu Persetujuan</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </section>
</div>
@endsection
