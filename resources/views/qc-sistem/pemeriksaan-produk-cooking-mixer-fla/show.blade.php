@extends('layouts.app')

@section('title', 'Detail Pemeriksaan Produk Cooking Mixer FLA')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Pemeriksaan Produk Cooking Mixer FLA</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-produk-cooking-mixer-fla.index') }}">Data Pemeriksaan Produk Cooking Mixer FLA</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Pemeriksaan Produk Cooking Mixer FLA</h3>
                        </div>
                        
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="30%">Plan</th>
                                            <td>: {{ $item->plan->nama_plan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>User</th>
                                            <td>: {{ $item->user->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shift</th>
                                            <td>: 
                                                <span class="badge badge-info">
                                                    {{ $item->shift->shift ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal</th>
                                            <td>: {{ $item->tanggal->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Produk</th>
                                            <td>: {{ $item->namaFormulaFla->produk->nama_produk ?? '-' }} {{ $item->berat ?? '-' }} gram</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Formula FLA</th>
                                            <td>: {{ $item->namaFormulaFla->nama_formula_fla ?? '-' }}</td>
                                        </tr>
                                        <!-- <tr>
                                            <th>Step Formula</th>
                                            <td>: 
                                                <span class="badge badge-primary">
                                                    Step {{ $item->nomorStepFormulaFla->nomor_step ?? '-' }}
                                                </span>
                                            </td>
                                        </tr> -->
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="30%">Kode Produksi</th>
                                            <td>: 
                                                <span class="badge badge-secondary">
                                                    {{ $item->kode_produksi }}
                                                </span>
                                            </td>
                                        </tr>
                                  
                                        <tr>
                                            <th>Waktu Start</th>
                                            <td>: {{ $item->waktu_start }}</td>
                                        </tr>
                                        <tr>
                                            <th>Waktu Stop</th>
                                            <td>: {{ $item->waktu_stop }}</td>
                                        </tr>
                                        <tr>
                                            <th>Lama Proses</th>
                                            <td>: {{ $item->lama_proses }}</td>
                                        </tr>
                                        <tr>
                                            <th>Speed</th>
                                            <td>: {{ $item->speed }} <small class="text-muted">RPM</small></td>
                                        </tr>
                                        <tr>
                                            <th>Sensori Kondisi</th>
                                            <td>: {{ $item->sensori_kondisi }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <!-- Status Gas -->
                                <div class="col-md-3">
                                    <h5><i class="fas fa-fire text-warning"></i> Status Gas</h5>
                                    <div class="card">
                                        <div class="card-body text-center">
                                            @if($item->status_gas)
                                                <i class="fas fa-check-circle text-success fa-3x"></i>
                                                <h6 class="mt-2 text-success">Aktif</h6>
                                            @else
                                                <i class="fas fa-times-circle text-danger fa-3x"></i>
                                                <h6 class="mt-2 text-danger">Tidak Aktif</h6>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Temperature Standards -->
                                <div class="col-md-9">
                                    <h5><i class="fas fa-thermometer-half text-info"></i> Temperature Standards</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <h6 class="text-info">Temperature Std 1</h6>
                                                    <h4 class="text-primary">{{ $item->temp_std_1 }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <h6 class="text-info">Temperature Std 2</h6>
                                                    <h4 class="text-primary">{{ $item->temp_std_2 }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <h6 class="text-info">Temperature Std 3</h6>
                                                    <h4 class="text-primary">{{ $item->temp_std_3 }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Organoleptic Tests -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h5><i class="fas fa-eye text-success"></i> Organoleptic Tests</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <h6>Warna</h6>
                                                    @if($item->organo_warna == 'OK')
                                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                                        <p class="mt-2 text-success">OK</p>
                                                    @else
                                                        <i class="fas fa-times-circle text-danger fa-2x"></i>
                                                        <p class="mt-2 text-danger">Tidak OK</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <h6>Aroma</h6>
                                                    @if($item->organo_aroma == 'OK')
                                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                                        <p class="mt-2 text-success">OK</p>
                                                    @else
                                                        <i class="fas fa-times-circle text-danger fa-2x"></i>
                                                        <p class="mt-2 text-danger">Tidak OK</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <h6>Tekstur</h6>
                                                    @if($item->organo_tekstur == 'OK')
                                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                                        <p class="mt-2 text-success">OK</p>
                                                    @else
                                                        <i class="fas fa-times-circle text-danger fa-2x"></i>
                                                        <p class="mt-2 text-danger">Tidak OK</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <h6>Rasa</h6>
                                                    @if($item->organo_rasa == 'OK')
                                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                                        <p class="mt-2 text-success">OK</p>
                                                    @else
                                                        <i class="fas fa-times-circle text-danger fa-2x"></i>
                                                        <p class="mt-2 text-danger">Tidak OK</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Bahan Formula FLA Details -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h5><i class="fas fa-list text-primary"></i> Detail Bahan Formula FLA</h5>
                                    @if($item->bahanFormulaFla)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                 <thead class="thead-light">
                                                     <tr>
                                                         <th>Nama Formula</th>
                                                         <th>Bahan Formula FLA</th>
                                                         <th>Berat Formula FLA</th>
                                                         <th>Kode Produksi Bahan</th>
                                                         <!-- <th>Step</th> -->
                                                         <th>Proses</th>
                                                     </tr>
                                                 </thead>
                                                 <tbody>
                                                     <tr>
                                                     <td>{{ $item->namaFormulaFla->nama_formula_fla ?? '-' }}</td>
                                                         <td>
                                                             @if($item->bahanFormulaFla && $item->bahanFormulaFla->getBahanFormulaArray())
                                                                 <ul class="list-unstyled mb-0">
                                                                     @foreach($item->bahanFormulaFla->getBahanFormulaArray() as $bahan)
                                                                         <li><i class="fas fa-circle text-primary" style="font-size: 6px;"></i> {{ $bahan }}</li>
                                                                     @endforeach
                                                                 </ul>
                                                             @else
                                                                 <span class="text-muted">-</span>
                                                             @endif
                                                         </td>
                                                         <td>
                                                             @if($item->bahanFormulaFla && $item->bahanFormulaFla->getBeratFormulaArray())
                                                                 <ul class="list-unstyled mb-0">
                                                                     @foreach($item->bahanFormulaFla->getBeratFormulaArray() as $berat)
                                                                         <li><i class="fas fa-circle text-success" style="font-size: 6px;"></i> {{ $berat }} kg</li>
                                                                     @endforeach
                                                                 </ul>
                                                             @else
                                                                 <span class="text-muted">-</span>
                                                             @endif
                                                         </td>
                                                         <td>
                                                             @if(!empty($item->getKodeProduksiBahanArray()))
                                                                 <ul class="list-unstyled mb-0">
                                                                     @foreach($item->getKodeProduksiBahanArray() as $kodeBahan)
                                                                         <li><i class="fas fa-circle text-info" style="font-size: 6px;"></i> {{ $kodeBahan ?: '-' }}</li>
                                                                     @endforeach
                                                                 </ul>
                                                             @else
                                                                 <span class="text-muted">-</span>
                                                             @endif
                                                         </td>
                                                         <!-- <td>
                                                             <span class="badge badge-info">
                                                                 Step {{ $item->nomorStepFormulaFla->nomor_step ?? '-' }}
                                                             </span>
                                                         </td> -->
                                                         <td>
                                                             @if($item->nomorStepFormulaFla && $item->nomorStepFormulaFla->proses)
                                                                 @php
                                                                     $prosesArray = explode(',', $item->nomorStepFormulaFla->proses);
                                                                 @endphp
                                                                 @foreach($prosesArray as $proses)
                                                                     <span class="badge badge-secondary mr-1">{{ trim($proses) }}</span>
                                                                 @endforeach
                                                             @else
                                                                 <span class="text-muted">-</span>
                                                             @endif
                                                         </td>
                                                        
                                                     </tr>
                                                 </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> Tidak ada data bahan formula FLA
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Catatan -->
                            @if($item->catatan)
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5><i class="fas fa-sticky-note text-warning"></i> Catatan</h5>
                                        <div class="card">
                                            <div class="card-body">
                                                <p class="mb-0">{{ $item->catatan }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Timestamps -->
                            <hr>
                            <!-- <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <strong>Dibuat:</strong> {{ $item->created_at->format('d/m/Y H:i:s') }}
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <strong>Diupdate:</strong> {{ $item->updated_at->format('d/m/Y H:i:s') }}
                                    </small>
                                </div>
                            </div> -->
                        </div>

                        <div class="card-footer">
                            <!--
@if(auth()->user()->hasPermissionTo('edit-pemeriksaan-produk-cooking-mixer-fla')) <a href="{{ route('pemeriksaan-produk-cooking-mixer-fla.edit', $item->uuid) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a> @endif
-->
                            <a href="{{ route('pemeriksaan-produk-cooking-mixer-fla.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <!--
@if(auth()->user()->hasPermissionTo('delete-pemeriksaan-produk-cooking-mixer-fla')) <form action="{{ route('pemeriksaan-produk-cooking-mixer-fla.destroy', $item->uuid) }}" 
                                  method="POST" style="display: inline-block;" class="ml-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form> @endif
-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
