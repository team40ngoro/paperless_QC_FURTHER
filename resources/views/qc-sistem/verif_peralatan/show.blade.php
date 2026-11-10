@extends('layouts.app')

@section('title', 'Detail Verifikasi Peralatan')

@section('container')
@php
    $canVerify = auth()->user()->isQc() || auth()->user()->isQcInspector() || auth()->user()->role === 'superadmin' || in_array((int) auth()->user()->id_role, [1, 3, 5], true);
@endphp
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail Verifikasi Kebersihan Ruangan Mesin & Peralatan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('verif-peralatan.index') }}">Verifikasi Kebersihan Ruangan Mesin & Peralatan</a></li>
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
                                    <a href="{{ route('verif-peralatan.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    @if(auth()->user()->hasPermissionTo('edit-verif-peralatan'))
                                        <a href="{{ route('verif-peralatan.edit', $item->uuid) }}" class="btn btn-warning btn-sm">
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
                                    <div class="col-md-3"><strong>Tanggal</strong><br>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</div>
                                    <div class="col-md-3"><strong>Shift</strong><br>{{ $item->shift->shift ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        @if($canVerify)
                            <form action="{{ route('verif-peralatan.qc-verification', $item->uuid) }}" method="POST" id="form-qc-verification">
                                @csrf
                                @method('PUT')
                                <div class="card card-outline card-info shadow-sm mb-3">
                                    <div class="card-body py-2 d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-check text-info fa-lg mr-2"></i>
                                            <div>
                                                <strong>Verifikasi Hasil QC:</strong>
                                                <span class="text-muted small ml-1">Silakan tentukan hasil verifikasi (OK / Tidak OK) untuk setiap item/bagian, kemudian klik Simpan Verifikasi.</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                                            <button type="button" class="btn btn-outline-success btn-sm btn-set-all-ok" title="Set semua baris menjadi OK">
                                                <i class="fas fa-check-double mr-1"></i> Set Semua OK
                                            </button>
                                            {{-- <button type="submit" class="btn btn-primary btn-sm px-3">
                                                <i class="fas fa-save mr-1"></i> Simpan Verifikasi
                                            </button> --}}
                                        </div>
                                    </div>
                                </div>
                        @endif

                        @foreach($detailsByArea as $areaId => $details)
                            @php
                                $first = $details->first();
                                $areaNama = $first?->mesin?->area?->area;
                                $areaJam = $first?->jam ? \Carbon\Carbon::parse($first->jam)->format('H:i') : ($item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-');
                            @endphp

                            @if($areaNama)
                                <div class="card card-outline card-primary">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <h3 class="card-title m-0">Area: {{ $areaNama }}</h3>
                                        <div class="d-flex align-items-center" style="gap: 12px;">
                                            <span><i class="fas fa-clock mr-1"></i> Jam: {{ $areaJam }}</span>
                                            {{-- @if($canVerify)
                                                <button type="button" class="btn btn-outline-success btn-xs btn-set-area-ok" data-area-id="{{ $areaId }}" title="Set semua item pada area ini menjadi OK">
                                                    <i class="fas fa-check mr-1"></i> Semua OK
                                                </button>
                                            @endif --}}
                                        </div>
                                    </div>
                                    <div class="card-body table-responsive">
                                        <table class="table table-bordered table-striped" style="white-space:nowrap;">
                                            <thead>
                                                <tr>
                                                    <th>Jenis Item</th>
                                                    <th style="width:140px;" class="text-center">Kondisi</th>
                                                    <th>Keterangan</th>
                                                    <th>Tindakan Koreksi</th>
                                                    <th style="width:140px;" class="text-center">Verifikasi Hasil</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                 @foreach($details as $d)
                                                 @php
                                                     $savedBagianCount  = $d->bagianDetails->count();
                                                     $hasBagianMesin    = $d->mesin && $d->mesin->bagian && $d->mesin->bagian->count() > 0;
                                                     $rowIsEmpty = ($hasBagianMesin && $savedBagianCount === 0)
                                                                || (!$hasBagianMesin && $d->verifikasi === null);
                                                 @endphp
                                                 @if($rowIsEmpty) @continue @endif
                                                 <tr>
                                                          <td>
                                                              <strong>{{ $d->mesin->nama_mesin ?? '-' }}</strong>
                                                              @if($hasBagianMesin && $savedBagianCount > 0)
                                                                  <button type="button" class="btn btn-link btn-sm p-0 ml-2 text-primary toggle-bagian-show" data-mesin-id="{{ $d->id_mesin }}" style="font-size: 0.75rem;">
                                                                      <i class="fas fa-chevron-{{ $canVerify ? 'up' : 'down' }}"></i> <span>Bagian ({{ $savedBagianCount }})</span>
                                                                  </button>
                                                              @endif
                                                          </td>
                                                           @php $hasBagian = $hasBagianMesin; @endphp
                                                           <td class="text-center">
                                                               @if($hasBagian || $d->verifikasi === null)
                                                                   <span class="text-muted">-</span>
                                                               @elseif($d->verifikasi)
                                                                   <span class="badge badge-success">Bersih</span>
                                                               @else
                                                                   <span class="badge badge-danger">Kotor</span>
                                                               @endif
                                                           </td>
                                                           <td>
                                                               {{ $hasBagian || $d->verifikasi ? '-' : ($d->keterangan ?? '-') }}
                                                           </td>
                                                           <td>
                                                               {{ $hasBagian || $d->verifikasi ? '-' : ($d->tindakan_koreksi ?? '-') }}
                                                           </td>
                                                           <td class="text-center align-middle" style="width: 140px;">
                                                               @if($hasBagian)
                                                                   @if($d->verifikasi_hasil === null)
                                                                       <span class="text-muted" title="Dihitung dari sub-bagian">-</span>
                                                                   @elseif($d->verifikasi_hasil)
                                                                       <span class="badge badge-success" title="Dihitung dari sub-bagian">OK</span>
                                                                   @else
                                                                       <span class="badge badge-danger" title="Dihitung dari sub-bagian">Tidak OK</span>
                                                                   @endif
                                                               @else
                                                                   @if($canVerify)
                                                                       <select name="details[{{ $d->id_mesin }}][verifikasi_hasil]" class="form-control form-control-sm select-verif-hasil" data-area-id="{{ $areaId }}">
                                                                           <option value="" {{ $d->verifikasi_hasil === null ? 'selected' : '' }}>-</option>
                                                                           <option value="1" {{ $d->verifikasi_hasil === true ? 'selected' : '' }}>OK</option>
                                                                           <option value="0" {{ $d->verifikasi_hasil === false ? 'selected' : '' }}>Tidak OK</option>
                                                                       </select>
                                                                   @else
                                                                       @if($d->verifikasi_hasil === null)
                                                                           <span class="text-muted">-</span>
                                                                       @elseif($d->verifikasi_hasil)
                                                                           <span class="badge badge-success">OK</span>
                                                                       @else
                                                                           <span class="badge badge-danger">Tidak OK</span>
                                                                       @endif
                                                                   @endif
                                                               @endif
                                                           </td>
                                                       </tr>
                                                        @if($hasBagianMesin && $savedBagianCount > 0)
                                                           <tr class="bagian-row-show {{ $canVerify ? '' : 'd-none' }}" data-mesin-id="{{ $d->id_mesin }}">
                                                               <td colspan="5">
                                                                  <div class="bg-light p-3 ml-4" style="border-left: 3px solid #007bff;">
                                                                      <div class="d-flex justify-content-between align-items-center mb-2">
                                                                          <small class="text-muted font-weight-bold">Bagian/Sub Bagian:</small>
                                                                          {{-- @if($canVerify)
                                                                              <button type="button" class="btn btn-outline-success btn-xs btn-set-bagian-ok" data-mesin-id="{{ $d->id_mesin }}">
                                                                                  <i class="fas fa-check mr-1"></i> Semua Bagian OK
                                                                              </button>
                                                                          @endif --}}
                                                                      </div>
                                                                     <table class="table table-sm table-bordered mb-0" style="font-size: 0.8rem; background-color: #fff;">
                                                                          <thead class="thead-light">
                                                                              <tr>
                                                                                  <th>Bagian</th>
                                                                                   <th class="text-center" style="width: 100px;">Kondisi</th>
                                                                                   <th class="text-center" style="width: 140px;">Verifikasi Hasil</th>
                                                                              </tr>
                                                                         </thead>
                                                                         <tbody>
                                                                               @php
                                                                                   $bagianVerifMap = $d->bagianDetails->keyBy('id_bagian');
                                                                               @endphp
                                                                               @foreach($d->mesin->bagian as $bagian)
                                                                                   @php
                                                                                       $bv = $bagianVerifMap->get($bagian->id);
                                                                                   @endphp
                                                                                   @if(!$bv) @continue @endif
                                                                                     <tr>
                                                                                        <td>
                                                                                            <strong>{{ $bagian->nama_bagian }}</strong>
                                                                                            @if($bv)
                                                                                                <span class="float-right badge badge-{{ $bv->verifikasi ? 'success' : 'danger' }}">{{ $bv->verifikasi ? 'Bersih' : 'Kotor' }}</span>
                                                                                            @endif
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            @if($bv)
                                                                                                <span class="badge badge-{{ $bv->verifikasi ? 'success' : 'danger' }}">{{ $bv->verifikasi ? 'Bersih' : 'Kotor' }}</span>
                                                                                            @else
                                                                                                <span class="text-muted">-</span>
                                                                                            @endif
                                                                                        </td>
                                                                                        <td class="text-center align-middle">
                                                                                            @if($canVerify)
                                                                                                <select name="details[{{ $d->id_mesin }}][bagian][{{ $bagian->id }}][verifikasi_hasil]" class="form-control form-control-sm select-verif-hasil" data-area-id="{{ $areaId }}" data-mesin-id="{{ $d->id_mesin }}">
                                                                                                    <option value="" {{ ($bv && $bv->verifikasi_hasil === null) ? 'selected' : '' }}>-</option>
                                                                                                    <option value="1" {{ ($bv && $bv->verifikasi_hasil === true) ? 'selected' : '' }}>OK</option>
                                                                                                    <option value="0" {{ ($bv && $bv->verifikasi_hasil === false) ? 'selected' : '' }}>Tidak OK</option>
                                                                                                </select>
                                                                                            @else
                                                                                                @if($bv && $bv->verifikasi_hasil !== null)
                                                                                                    <span class="badge badge-{{ $bv->verifikasi_hasil ? 'success' : 'danger' }}">{{ $bv->verifikasi_hasil ? 'OK' : 'Tidak OK' }}</span>
                                                                                                @else
                                                                                                    <span class="text-muted">-</span>
                                                                                                @endif
                                                                                            @endif
                                                                                        </td>
                                                                                     </tr>
                                                                                   @if($bv && !$bv->verifikasi && ($bv->keterangan || $bv->tindakan_koreksi))
                                                                                       <tr>
                                                                                           <td colspan="3" class="pl-4 text-muted bg-white" style="font-size: 0.75rem;">
                                                                                               @if($bv->keterangan) <strong>Keterangan:</strong> {{ $bv->keterangan }} @endif
                                                                                               @if($bv->keterangan && $bv->tindakan_koreksi) | @endif
                                                                                               @if($bv->tindakan_koreksi) <strong>Tindakan:</strong> {{ $bv->tindakan_koreksi }} @endif
                                                                                           </td>
                                                                                       </tr>
                                                                                   @endif
                                                                               @endforeach
                                                                         </tbody>
                                                                     </table>
                                                                  </div>
                                                              </td>
                                                          </tr>
                                                      @endif
                                                  @endforeach
                                             </tbody>
                                         </table>
                                     </div>
                                 </div>
                             @endif
                         @endforeach

                         @if($canVerify)
                             <div class="card shadow-sm mb-4">
                                 <div class="card-body d-flex justify-content-between align-items-center py-3">
                                     <span class="text-muted small"><i class="fas fa-info-circle mr-1"></i> Pastikan semua verifikasi telah terisi dengan benar sebelum menyimpan.</span>
                                     <div>
                                         {{-- <button type="button" class="btn btn-outline-success mr-2 btn-set-all-ok">
                                             <i class="fas fa-check-double mr-1"></i> Set Semua OK
                                         </button> --}}
                                         <button type="submit" class="btn btn-primary px-4">
                                             <i class="fas fa-save mr-1"></i> Simpan Verifikasi Hasil
                                         </button>
                                     </div>
                                 </div>
                             </div>
                             </form>
                         @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle bagian row
    document.addEventListener('click', function(e) {
        const toggleBtn = e.target.closest('.toggle-bagian-show');
        if (toggleBtn) {
            const mesinId = toggleBtn.getAttribute('data-mesin-id');
            const bagianRow = document.querySelector(`.bagian-row-show[data-mesin-id="${mesinId}"]`);
            const icon = toggleBtn.querySelector('i');
            if (bagianRow) {
                bagianRow.classList.toggle('d-none');
                if (icon) icon.className = bagianRow.classList.contains('d-none') ? 'fas fa-chevron-down' : 'fas fa-chevron-up';
            }
        }
    });

    // Set semua OK keseluruhan
    document.querySelectorAll('.btn-set-all-ok').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.select-verif-hasil').forEach(function(sel) {
                sel.value = '1';
            });
        });
    });

    // Set semua OK per area
    document.querySelectorAll('.btn-set-area-ok').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const areaId = this.getAttribute('data-area-id');
            document.querySelectorAll(`.select-verif-hasil[data-area-id="${areaId}"]`).forEach(function(sel) {
                sel.value = '1';
            });
        });
    });

    // Set semua bagian OK per mesin
    document.querySelectorAll('.btn-set-bagian-ok').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const mesinId = this.getAttribute('data-mesin-id');
            document.querySelectorAll(`.select-verif-hasil[data-mesin-id="${mesinId}"]`).forEach(function(sel) {
                sel.value = '1';
            });
        });
    });
});
</script>
@endpush
