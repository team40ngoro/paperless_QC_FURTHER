@extends('layouts.app')

@section('title', 'Detail Data Area Proses')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        {{-- ===== PAGE HEADER ===== --}}
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail Pemeriksaan Kondisi Area Kerja Selama Produksi</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('area-proses.index') }}">Area Proses</a></li>
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
                                    Informasi Pemeriksaan Area
                                </h3>
                                <div class="card-tools">
                                    <a href="{{ route('area-proses.export-pdf', $areaProses->uuid) }}" class="btn btn-danger btn-sm shadow-sm" target="_blank">
                                        <i class="fas fa-file-pdf mr-1"></i> Cetak PDF Group
                                    </a>
                                </div>
                            </div>
                            {{-- End Card Header --}}

                            <div class="card-body">
                                
                                {{-- Master Info Section --}}
                                <div class="row mb-4">
                                    <!-- <div class="col-md-3">
                                        <div class="p-3 border rounded bg-light shadow-xs mb-2">
                                            <small class="text-muted d-block mb-1 text-uppercase font-weight-bold" style="font-size: 0.7rem;">Grup UUID</small>
                                            <code class="text-primary">{{ substr($areaProses->group_uuid ?: $areaProses->uuid, 0, 13) }}...</code>
                                        </div>
                                    </div> -->
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-light shadow-xs mb-2">
                                            <small class="text-muted d-block mb-1 text-uppercase font-weight-bold" style="font-size: 0.7rem;">Area Pemeriksaan</small>
                                            <span class="font-weight-bold text-dark h6 mb-0">{{ $areaProses->area->area ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-light shadow-xs mb-2">
                                            <small class="text-muted d-block mb-1 text-uppercase font-weight-bold" style="font-size: 0.7rem;">Tanggal Form</small>
                                            <span class="font-weight-bold text-dark h6 mb-0 text-info">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                {{ $areaProses->tanggal ? $areaProses->tanggal->format('d/m/Y') : '-' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-light shadow-xs mb-2 text-center text-md-left">
                                            <small class="text-muted d-block mb-1 text-uppercase font-weight-bold" style="font-size: 0.7rem;">Shift Kerja</small>
                                            @if(($areaProses->shift->shift ?? '') == '1')
                                                <span class="badge badge-primary px-3 py-2 shadow-sm h6 mb-0">Shift 1</span>
                                            @elseif(($areaProses->shift->shift ?? '') == '2')
                                                <span class="badge badge-success px-3 py-2 shadow-sm h6 mb-0">Shift 2</span>
                                            @else
                                                <span class="badge badge-dark px-3 py-2 shadow-sm h6 mb-0">Shift {{ $areaProses->shift->shift ?? '-' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- History / Records Table --}}
                                <div class="d-flex align-items-center mb-3">
                                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-history mr-2 text-secondary"></i>Riwayat Pemeriksaan (Per 2 Jam)</h5>
                                    <div class="ml-auto">
                                        <span class="badge badge-warning shadow-sm">
                                            @php
                                                $records = !request('group_uuid') && isset($areaProses->groupRecords) && $areaProses->groupRecords->count() > 0 
                                                    ? $areaProses->groupRecords->where('is_audit', $areaProses->is_audit)->sortBy('jam') 
                                                    : collect([$areaProses]);
                                            @endphp
                                            Total: {{ $records->count() }} Data
                                        </span>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover text-center shadow-xs">
                                        <thead class="bg-gradient-light border-top">
                                            <tr>
                                                <th style="width: 5%">No</th>
                                                <th>Jam</th>
                                                <th>Kebersihan Ruangan</th>
                                                <th>Kebersihan Karyawan</th>
                                                <th>Suhu Ruang (°C)</th>
                                                <th>Suhu RM</th>
                                                <th>Keterangan & Koreksi</th>
                                                <th>Pencatat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($records as $index => $item)
                                            <tr>
                                                <td class="align-middle">{{ $index + 1 }}</td>
                                                <td class="align-middle">
                                                    <span class="badge badge-light border px-2 py-1 shadow-sm font-weight-bold">
                                                        <i class="fas fa-clock mr-1 text-primary"></i>
                                                        {{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge shadow-sm {{ $item->kebersihan_ruangan === 'OK' ? 'badge-success' : 'badge-danger' }} px-3 py-2" style="min-width: 80px;">
                                                        <i class="fas {{ $item->kebersihan_ruangan === 'OK' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                                        {{ $item->kebersihan_ruangan }}
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge shadow-sm {{ $item->kebersihan_karyawan === 'OK' ? 'badge-success' : 'badge-danger' }} px-3 py-2" style="min-width: 80px;">
                                                        <i class="fas {{ $item->kebersihan_karyawan === 'OK' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                                        {{ $item->kebersihan_karyawan }}
                                                    </span>
                                                </td>
                                                <td class="align-middle font-weight-bold h6 text-primary">{{ $item->pemeriksaan_suhu_ruang }} °C</td>
                                                <td class="align-middle text-center">
                                                    @if(!empty($item->suhu_rm))
                                                        @php
                                                            $suhuRmItems = array_map('trim', explode(',', $item->suhu_rm));
                                                        @endphp
                                                        <div class="d-flex flex-wrap justify-content-center align-items-center" style="gap: 4px;">
                                                            @foreach($suhuRmItems as $sRm)
                                                                <span class="badge badge-secondary px-2 py-1 shadow-xs font-weight-bold" style="font-size: 0.8rem;">
                                                                    <i class="fas fa-thermometer-half mr-1"></i>{{ $sRm }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-left align-middle" style="min-width: 250px;">
                                                    @if($item->ketidaksesuaian || $item->tindakan_koreksi)
                                                        <div class="mb-1">
                                                            <small class="text-danger font-weight-bold">Ketidaksesuaian:</small>
                                                            <div class="p-1 px-2 border rounded bg-light small">{{ $item->ketidaksesuaian ?: '-' }}</div>
                                                        </div>
                                                        <div>
                                                            <small class="text-success font-weight-bold">Tindakan Koreksi:</small>
                                                            <div class="p-1 px-2 border rounded bg-light small">{{ $item->tindakan_koreksi ?: '-' }}</div>
                                                        </div>
                                                    @else
                                                        <div class="text-center text-muted font-italic small py-2">
                                                            <i class="fas fa-minus mr-1"></i> Tidak ada ketidaksesuaian
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-muted">
                                                    <div class="font-weight-bold text-dark">{{ $item->user->name ?? '-' }}</div>
                                                    <small style="font-size: 0.65rem;">{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '' }}</small>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- {{-- Unified Approval Status Section --}}
                                <hr class="my-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card card-outline {{ $areaProses->approved_by_qc ? 'card-success' : 'card-secondary' }} shadow-xs mb-3">
                                            <div class="card-header py-2">
                                                <h3 class="card-title font-weight-bold small text-uppercase"><i class="fas fa-microscope mr-1"></i> Dicek Oleh (QC)</h3>
                                            </div>
                                            <div class="card-body text-center py-3">
                                                @if($areaProses->approved_by_qc)
                                                    <div class="mb-2"><i class="fas fa-check-circle text-success fa-2x"></i></div>
                                                    <div class="font-weight-bold text-dark">{{ $areaProses->qcApprover->name ?? '-' }}</div>
                                                    <small class="text-muted px-2 py-1 bg-light rounded shadow-xs" style="font-size: 0.7rem;">
                                                        <i class="fas fa-calendar-check mr-1"></i>{{ $areaProses->qc_approved_at ? $areaProses->qc_approved_at->format('d/m/Y H:i') : '' }}
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
                                        <div class="card card-outline {{ $areaProses->approved_by_produksi ? 'card-primary' : 'card-secondary' }} shadow-xs mb-3">
                                            <div class="card-header py-2">
                                                <h3 class="card-title font-weight-bold small text-uppercase"><i class="fas fa-user-check mr-1"></i> Disetujui Oleh (FM/FL)</h3>
                                            </div>
                                            <div class="card-body text-center py-3">
                                                @if($areaProses->approved_by_produksi)
                                                    <div class="mb-2"><i class="fas fa-check-circle text-primary fa-2x"></i></div>
                                                    <div class="font-weight-bold text-dark">{{ $areaProses->produksiApprover->name ?? '-' }}</div>
                                                    <small class="text-muted px-2 py-1 bg-light rounded shadow-xs" style="font-size: 0.7rem;">
                                                        <i class="fas fa-calendar-check mr-1"></i>{{ $areaProses->produksi_approved_at ? $areaProses->produksi_approved_at->format('d/m/Y H:i') : '' }}
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
                                        <div class="card card-outline {{ $areaProses->approved_by_spv ? 'card-dark' : 'card-secondary' }} shadow-xs mb-3">
                                            <div class="card-header py-2">
                                                <h3 class="card-title font-weight-bold small text-uppercase"><i class="fas fa-award mr-1"></i> Diketahui Oleh (SPV)</h3>
                                            </div>
                                            <div class="card-body text-center py-3">
                                                @if($areaProses->approved_by_spv)
                                                    <div class="mb-2"><i class="fas fa-check-circle text-dark fa-2x"></i></div>
                                                    <div class="font-weight-bold text-dark">{{ $areaProses->spvApprover->name ?? '-' }}</div>
                                                    <small class="text-muted px-2 py-1 bg-light rounded shadow-xs" style="font-size: 0.7rem;">
                                                        <i class="fas fa-calendar-check mr-1"></i>{{ $areaProses->spv_approved_at ? $areaProses->spv_approved_at->format('d/m/Y H:i') : '' }}
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
                                        <a href="{{ route('area-proses.index') }}" class="btn btn-secondary shadow-sm">
                                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                                        </a>
                                        @if(Auth::user()->hasPermissionTo('edit-area-proses'))
                                            <a href="{{ route('area-proses.edit', $areaProses->uuid) }}" class="btn btn-warning shadow-sm ml-1 text-dark">
                                                <i class="fas fa-edit mr-1"></i> Edit Data Utama
                                            </a>
                                        @endif
                                    </div>
                                    <!-- <div class="col-md-6 text-md-right text-center">
                                        <span class="text-muted small border-right pr-2 mr-2">Plan: {{ $areaProses->plan->nama_plan ?? '-' }}</span>
                                        <span class="text-muted small">Update Terakhir: {{ $areaProses->updated_at ? $areaProses->updated_at->format('d/m/Y H:i') : '-' }}</span>
                                    </div> -->
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
