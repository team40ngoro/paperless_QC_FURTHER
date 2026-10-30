@extends('layouts.app')

@section('title', 'Detail Data Kontrol Sanitasi')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        {{-- ===== PAGE HEADER ===== --}}
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail Data Kontrol Sanitasi</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('kontrol-sanitasi.index') }}">Kontrol Sanitasi</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        {{-- ===== END PAGE HEADER ===== --}}

        {{-- ===== MAIN CONTENT ===== --}}
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline shadow-sm">
                            
                            {{-- Card Header --}}
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-search mr-1 text-primary"></i>
                                    Informasi Kontrol Sanitasi
                                </h3>
                                <div class="card-tools">
                                    <a href="{{ route('kontrol-sanitasi.export-pdf', $kontrolSanitasi->uuid) }}" class="btn btn-danger btn-sm shadow-sm" target="_blank">
                                        <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                                    </a>
                                </div>
                            </div>
                            {{-- End Card Header --}}

                            <div class="card-body">
                                
                                {{-- Master Info Section --}}
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-light shadow-xs mb-2">
                                            <small class="text-muted d-block mb-1 text-uppercase font-weight-bold" style="font-size: 0.7rem;">Tanggal Form</small>
                                            <span class="font-weight-bold text-dark h6 mb-0 text-info">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                {{ $kontrolSanitasi->tanggal ? $kontrolSanitasi->tanggal->format('d/m/Y') : '-' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-light shadow-xs mb-2 text-center text-md-left">
                                            <small class="text-muted d-block mb-1 text-uppercase font-weight-bold" style="font-size: 0.7rem;">Shift Kerja</small>
                                            @if(($kontrolSanitasi->shift->shift ?? '') == '1')
                                                <span class="badge badge-primary px-3 py-2 shadow-sm h6 mb-0">Shift 1</span>
                                            @elseif(($kontrolSanitasi->shift->shift ?? '') == '2')
                                                <span class="badge badge-success px-3 py-2 shadow-sm h6 mb-0">Shift 2</span>
                                            @else
                                                <span class="badge badge-dark px-3 py-2 shadow-sm h6 mb-0">Shift {{ $kontrolSanitasi->shift->shift ?? '-' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-4">
                                        <div class="p-3 border rounded bg-light shadow-xs mb-2">
                                            <small class="text-muted d-block mb-1 text-uppercase font-weight-bold" style="font-size: 0.7rem;">Pencatat</small>
                                            <span class="font-weight-bold text-dark h6 mb-0">{{ $kontrolSanitasi->user->name ?? '-' }}</span>
                                        </div>
                                    </div> -->
                                </div>

                                {{-- History / Records Table --}}
                                <div class="d-flex align-items-center mb-3">
                                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-list-alt mr-2 text-secondary"></i>Rincian Pemeriksaan Sanitasi</h5>
                                    <div class="ml-auto">
                                        <span class="badge badge-success shadow-sm">
                                            @php
                                                $entries = is_array($kontrolSanitasi->entries) ? $kontrolSanitasi->entries : [];
                                            @endphp
                                            Total: {{ count($entries) }} Entry(ies)
                                        </span>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover text-center shadow-xs">
                                        <thead class="bg-gradient-light border-top">
                                            <tr>
                                                <th style="width: 5%">No</th>
                                                <th>Jam</th>
                                                <th>Suhu Air (°C)</th>
                                                <th>Kadar Klorin Foot Basin</th>
                                                <th>Kadar Klorin Hand Basin</th>
                                                <th>Hasil Verifikasi</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($entries as $index => $entry)
                                            <tr>
                                                <td class="align-middle">{{ $index + 1 }}</td>
                                                <td class="align-middle">
                                                    <span class="badge badge-light border px-2 py-1 shadow-sm font-weight-bold">
                                                        <i class="fas fa-clock mr-1 text-primary"></i>
                                                        {{ $entry['jam'] ?? '-' }}
                                                    </span>
                                                </td>
                                                <td class="align-middle font-weight-bold h6 text-primary">{{ $entry['suhu_air'] ?? '-' }} °C</td>
                                                <td class="align-middle font-weight-bold">{{ $entry['kadar_klorin_food_basin'] ?? '-' }}</td>
                                                <td class="align-middle font-weight-bold">{{ $entry['kadar_klorin_hand_basin'] ?? '-' }}</td>
                                                <td class="text-left align-middle" style="min-width: 200px;">
                                                    <div class="p-1 px-2 border rounded bg-light small">{{ $entry['hasil_verifikasi'] ?? '-' }}</div>
                                                </td>
                                                <td class="text-left align-middle" style="min-width: 200px;">
                                                    <div class="p-1 px-2 border rounded bg-light small text-muted">{{ $entry['keterangan'] ?? '-' }}</div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    <i class="fas fa-info-circle mr-1"></i> Tidak ada rincian data (Format lama atau kosong)
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Legacy Data Section (Jika ada data lama yang belum pindah ke JSON) --}}
                                @if(!$entries && ($kontrolSanitasi->suhu_air || $kontrolSanitasi->kadar_klorin_food_basin))
                                <div class="mt-4 p-3 border rounded border-warning bg-light">
                                    <h6 class="font-weight-bold text-warning"><i class="fas fa-exclamation-triangle mr-2"></i>Data Format Lama</h6>
                                    <div class="row">
                                        <div class="col-md-3"><strong>Jam:</strong> {{ $kontrolSanitasi->jam ? \Carbon\Carbon::parse($kontrolSanitasi->jam)->format('H:i') : '-' }}</div>
                                        <div class="col-md-3"><strong>Suhu Air:</strong> {{ $kontrolSanitasi->suhu_air }} °C</div>
                                        <div class="col-md-3"><strong>Klorin FB:</strong> {{ $kontrolSanitasi->kadar_klorin_food_basin }}</div>
                                        <div class="col-md-3"><strong>Klorin HB:</strong> {{ $kontrolSanitasi->kadar_klorin_hand_basin }}</div>
                                    </div>
                                    <div class="mt-2"><strong>Hasil Verifikasi:</strong> {{ $kontrolSanitasi->hasil_verifikasi }}</div>
                                </div>
                                @endif

                                {{-- Unified Approval Status Section --}}
                                <!-- <hr class="my-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card card-outline {{ $kontrolSanitasi->approved_by_qc ? 'card-success' : 'card-secondary' }} shadow-xs mb-3">
                                            <div class="card-header py-2">
                                                <h3 class="card-title font-weight-bold small text-uppercase"><i class="fas fa-microscope mr-1"></i> Dicek Oleh (QC)</h3>
                                            </div>
                                            <div class="card-body text-center py-3">
                                                @if($kontrolSanitasi->approved_by_qc)
                                                    <div class="mb-2"><i class="fas fa-check-circle text-success fa-2x"></i></div>
                                                    <div class="font-weight-bold text-dark">{{ $kontrolSanitasi->qcApprover->name ?? '-' }}</div>
                                                    <small class="text-muted px-2 py-1 bg-light rounded shadow-xs" style="font-size: 0.7rem;">
                                                        <i class="fas fa-calendar-check mr-1"></i>{{ $kontrolSanitasi->qc_approved_at ? $kontrolSanitasi->qc_approved_at->format('d/m/Y H:i') : '' }}
                                                    </small>
                                                @else
                                                    <div class="py-3 text-muted opacity-50">
                                                        <i class="fas fa-clock fa-2x mb-2"></i>
                                                        <div class="small font-italic">Menunggu Approval QC</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card card-outline {{ $kontrolSanitasi->approved_by_produksi ? 'card-primary' : 'card-secondary' }} shadow-xs mb-3">
                                            <div class="card-header py-2">
                                                <h3 class="card-title font-weight-bold small text-uppercase"><i class="fas fa-user-check mr-1"></i> Disetujui Oleh (FM/FL)</h3>
                                            </div>
                                            <div class="card-body text-center py-3">
                                                @if($kontrolSanitasi->approved_by_produksi)
                                                    <div class="mb-2"><i class="fas fa-check-circle text-primary fa-2x"></i></div>
                                                    <div class="font-weight-bold text-dark">{{ $kontrolSanitasi->produksiApprover->name ?? '-' }}</div>
                                                    <small class="text-muted px-2 py-1 bg-light rounded shadow-xs" style="font-size: 0.7rem;">
                                                        <i class="fas fa-calendar-check mr-1"></i>{{ $kontrolSanitasi->produksi_approved_at ? $kontrolSanitasi->produksi_approved_at->format('d/m/Y H:i') : '' }}
                                                    </small>
                                                @else
                                                    <div class="py-3 text-muted opacity-50">
                                                        <i class="fas fa-clock fa-2x mb-2"></i>
                                                        <div class="small font-italic">Menunggu Approval Produksi</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card card-outline {{ $kontrolSanitasi->approved_by_spv ? 'card-dark' : 'card-secondary' }} shadow-xs mb-3">
                                            <div class="card-header py-2">
                                                <h3 class="card-title font-weight-bold small text-uppercase"><i class="fas fa-award mr-1"></i> Diketahui Oleh (SPV)</h3>
                                            </div>
                                            <div class="card-body text-center py-3">
                                                @if($kontrolSanitasi->approved_by_spv)
                                                    <div class="mb-2"><i class="fas fa-check-circle text-dark fa-2x"></i></div>
                                                    <div class="font-weight-bold text-dark">{{ $kontrolSanitasi->spvApprover->name ?? '-' }}</div>
                                                    <small class="text-muted px-2 py-1 bg-light rounded shadow-xs" style="font-size: 0.7rem;">
                                                        <i class="fas fa-calendar-check mr-1"></i>{{ $kontrolSanitasi->spv_approved_at ? $kontrolSanitasi->spv_approved_at->format('d/m/Y H:i') : '' }}
                                                    </small>
                                                @else
                                                    <div class="py-3 text-muted opacity-50">
                                                        <i class="fas fa-clock fa-2x mb-2"></i>
                                                        <div class="small font-italic">Menunggu Approval SPV</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                            {{-- End Card Body --}}

                            {{-- Card Footer --}}
                            <div class="card-footer bg-light py-3">
                                <div class="row align-items-center">
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <a href="{{ route('kontrol-sanitasi.index') }}" class="btn btn-outline-secondary shadow-sm">
                                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                                        </a>
                                        @if(Auth::user()->hasPermissionTo('edit-kontrol-sanitasi'))
                                            <a href="{{ route('kontrol-sanitasi.edit', $kontrolSanitasi->uuid) }}" class="btn btn-warning shadow-sm ml-1 text-dark">
                                                <i class="fas fa-edit mr-1"></i> Edit Data
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- End Card Footer --}}

                        </div>
                    </div>
                </div>
            </div>
        </section>
        {{-- ===== END MAIN CONTENT ===== --}}

    </div>
</div>
@endsection
