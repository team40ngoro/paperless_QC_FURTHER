@extends('layouts.app')

@section('title', 'Detail Verifikasi Peralatan')

@section('container')
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
                                    <!-- <a href="{{ route('verif-peralatan.export-pdf', $item->uuid) }}" class="btn btn-danger btn-sm" target="_blank">
                                        <i class="fas fa-file-pdf"></i> Cetak PDF
                                    </a> -->
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
                                    <!-- <div class="col-md-3"><strong>Jam</strong><br>{{ $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '-' }}</div> -->
                                    <div class="col-md-3"><strong>Shift</strong><br>{{ $item->shift->shift ?? '-' }}</div>
                                    <!-- <div class="col-md-3"><strong>Plan</strong><br>{{ $item->plan->nama_plan ?? '-' }}</div> -->
                                    <!-- <div class="col-md-3"><strong>Dibuat Oleh</strong><br>{{ $item->user->name ?? '-' }}</div> -->
                                </div>
                            </div>
                        </div>

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
                                        <span><i class="fas fa-clock mr-1"></i> Jam: {{ $areaJam }}</span>
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
                                                     // Skip baris mesin yang tidak ada data apapun:
                                                     // - punya bagian tapi tidak ada bagian tersimpan (semua tidak diisi)
                                                     // - tidak punya bagian dan verifikasi null (tidak diisi)
                                                     $rowIsEmpty = ($hasBagianMesin && $savedBagianCount === 0)
                                                                || (!$hasBagianMesin && $d->verifikasi === null);
                                                 @endphp
                                                 @if($rowIsEmpty) @continue @endif
<tr>
                                                          <td>
                                                              <strong>{{ $d->mesin->nama_mesin ?? '-' }}</strong>
                                                              @if($hasBagianMesin && $savedBagianCount > 0)
                                                                  <button type="button" class="btn btn-link btn-sm p-0 ml-2 text-primary toggle-bagian-show" data-mesin-id="{{ $d->id_mesin }}" style="font-size: 0.75rem;">
                                                                      <i class="fas fa-chevron-down"></i> <span>Bagian ({{ $savedBagianCount }})</span>
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
                                                           <td class="text-center">
                                                               @if($hasBagian || $d->verifikasi_hasil === null)
                                                                   <span class="text-muted">-</span>
                                                               @elseif($d->verifikasi_hasil)
                                                                   <span class="badge badge-success">OK</span>
                                                               @else
                                                                   <span class="badge badge-danger">Tidak OK</span>
                                                               @endif
                                                           </td>
                                                      </tr>
                                                       @if($hasBagianMesin && $savedBagianCount > 0)
                                                          <tr class="bagian-row-show d-none" data-mesin-id="{{ $d->id_mesin }}">
                                                              <td colspan="5">
                                                                 <div class="bg-light p-3 ml-4" style="border-left: 3px solid #007bff;">
                                                                     <small class="text-muted font-weight-bold">Bagian/Sub Bagian:</small>
                                                                    <table class="table table-sm table-bordered mb-0 mt-2" style="font-size: 0.8rem;">
                                                                         <thead class="thead-light">
                                                                             <tr>
                                                                                 <th>Bagian</th>
                                                                                  <th class="text-center" style="width: 80px;">Kondisi</th>
                                                                                  <th class="text-center" style="width: 80px;">Verifikasi Hasil</th>
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
                                                                                       <td class="text-center">
                                                                                           @if($bv && $bv->verifikasi_hasil !== null)
                                                                                               <span class="badge badge-{{ $bv->verifikasi_hasil ? 'success' : 'danger' }}">{{ $bv->verifikasi_hasil ? 'OK' : 'Tidak OK' }}</span>
                                                                                           @else
                                                                                               <span class="text-muted">-</span>
                                                                                           @endif
                                                                                       </td>
                                                                                    </tr>
                                                                                  @if($bv && !$bv->verifikasi && ($bv->keterangan || $bv->tindakan_koreksi))
                                                                                      <tr>
                                                                                          <td class="pl-4 text-muted" style="font-size: 0.75rem;">
                                                                                              @if($bv->keterangan) Keterangan: {{ $bv->keterangan }} @endif
                                                                                              @if($bv->keterangan && $bv->tindakan_koreksi) | @endif
                                                                                              @if($bv->tindakan_koreksi) Tindakan: {{ $bv->tindakan_koreksi }} @endif
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
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
</script>
@endpush
