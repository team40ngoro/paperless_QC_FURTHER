@extends('layouts.app')

@php
    $assetPath = (isset($_SERVER['DOCUMENT_ROOT']) && realpath($_SERVER['DOCUMENT_ROOT']) === realpath(public_path())) ? '' : 'public/';
@endphp

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Data Shoestring</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shoestring.index') }}">Shoestring</a></li>
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
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Data Utama</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 35%">Shift</th>
                                            <td>{{ $shoestring->shift->shift ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Produksi</th>
                                            <td>
                                                @php
                                                    $userRole = auth()->user()->id_role ?? null;
                                                    $showTime = in_array($userRole, [1, 2, 5]);
                                                    $format = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                                                @endphp
                                                {{ $shoestring->tanggal ? \Carbon\Carbon::parse($shoestring->tanggal)->format($format) : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Jam</th>
                                            <td>{{ $shoestring->jam ? \Carbon\Carbon::parse($shoestring->jam)->format('H:i') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Produsen</th>
                                            <td>{{ $shoestring->nama_produsen ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 35%">Kode Produksi</th>
                                            <td>{{ $shoestring->kode_produksi ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Expired</th>
                                            <td>{{ $shoestring->best_before ? \Carbon\Carbon::parse($shoestring->best_before)->format('d-m-Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Gramase (gram)</th>
                                            <td>{{ $shoestring->berat_sampling ? $shoestring->berat_sampling . ' gram' : '-' }}</td>
                                        </tr>
                                        <!-- <tr>
                                            <th>Pemeriksaan Defect</th>
                                            <td>{{ $shoestring->sampling_defect ?? '-' }}</td>
                                        </tr> -->
                                        <tr>
                                            <th>Keterangan</th>
                                            <td>{{ $shoestring->catatan ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-12">
                                <h5 class="mb-3 font-weight-bold text-dark"><i class="fas fa-exclamation-circle text-warning mr-1"></i> Rincian Temuan Defect</h5>
                                     @if(!empty($defectRows))
                                         <div class="table-responsive">
                                             <table class="table table-bordered table-hover text-center" style="min-width: 600px;">
                                                 <thead class="bg-light text-uppercase small font-weight-bold">
                                                     <tr>
                                                         <th class="text-left py-3" style="width: 25%;">Kategori Ketidaksesuaian</th>
                                                         <th class="text-left py-3" style="width: 25%;">Jenis Ketidaksesuaian</th>
                                                         <th class="text-left py-3" style="width: 20%;">Sub Jenis</th>
                                                         <th class="text-left py-3" style="width: 18%;">Spesifikasi</th>
                                                         <th class="py-3" style="width: 12%;">Jumlah (Qty)</th>
                                                     </tr>
                                                 </thead>
                                                 <tbody>
                                                     @foreach($defectRows as $row)
                                                         <tr>
                                                             <td class="text-left align-middle font-weight-bold text-secondary">
                                                                 <i class="fas fa-folder mr-1 text-info"></i> {{ $row['kategori'] }}
                                                             </td>
                                                             <td class="text-left align-middle text-dark">
                                                                 {{ $row['jenis'] }}
                                                             </td>
                                                             <td class="text-left align-middle">
                                                                 @if(!empty($row['sub_jenis']) && $row['sub_jenis'] !== '-')
                                                                     <span class="badge badge-warning px-2 py-1" style="font-size: 0.8rem; font-weight: 500; white-space: normal; text-align: left;">{{ $row['sub_jenis'] }}</span>
                                                                 @else
                                                                     <span class="text-muted font-italic">-</span>
                                                                 @endif
                                                             </td>
                                                             <td class="text-left align-middle">
                                                                 @if(!empty($row['spesifikasi']) && $row['spesifikasi'] !== '-')
                                                                     <span class="badge badge-success px-2 py-1" style="font-size: 0.8rem; font-weight: 500;">{{ $row['spesifikasi'] }}</span>
                                                                 @else
                                                                     <span class="text-muted font-italic">-</span>
                                                                 @endif
                                                             </td>
                                                             <td class="align-middle">
                                                                 <strong class="text-primary" style="font-size: 1rem;">{{ $row['qty'] }}</strong>
                                                             </td>
                                                         </tr>
                                                         @endforeach
                                                         <tr>
                                                             <th>Total Defect</th>
                                                             <td colspan="4">{{ $shoestring->total_defect ?? '-' }}</td>
                                                         </tr>
                                                 </tbody>
                                             </table>
                                         </div>
                                     @else
                                         <div class="alert alert-light border text-center py-4">
                                             <i class="fas fa-info-circle text-muted mr-1"></i> Tidak ada rincian defect ditemukan.
                                         </div>
                                     @endif

                                    @if($shoestring->dokumentasi && is_array($shoestring->dokumentasi) && count($shoestring->dokumentasi) > 0)
                                        <h5 class="mt-4">Dokumentasi Label Supplier</h5>
                                        <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
                                            @foreach($shoestring->dokumentasi as $doc)
                                                <a href="{{ asset($assetPath . 'storage/' . $doc) }}" target="_blank">
                                                    <img src="{{ asset($assetPath . 'storage/' . $doc) }}" class="img-thumbnail" style="height: 150px; width: 150px; object-fit: cover;">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                    <a href="{{ route('shoestring.index') }}" class="btn btn-secondary btn-md ml-2">
                                        <i class="fas fa-arrow-left"></i> Kembali
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
@endsection