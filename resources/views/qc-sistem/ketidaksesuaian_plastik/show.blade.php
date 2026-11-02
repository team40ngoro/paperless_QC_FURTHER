@extends('layouts.app')
@php
    $assetPath = (isset($_SERVER['DOCUMENT_ROOT']) && realpath($_SERVER['DOCUMENT_ROOT']) === realpath(public_path())) ? '' : 'public/';
@endphp
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Ketidaksesuaian Plastik</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ketidaksesuaian-plastik.index') }}">Ketidaksesuaian Plastik</a></li>
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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Ketidaksesuaian Plastik</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <!-- <tr>
                                            <th width="30%">UUID</th>
                                            <td>{{ $ketidaksesuaianPlastik->uuid }}</td>
                                        </tr> -->
                                        <tr>
                                            <th>Tanggal</th>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $ketidaksesuaianPlastik->tanggal ? $ketidaksesuaianPlastik->tanggal->format('d-m-Y H:i:s') : '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Shift</th>
                                            <td>
                                                <span class="badge badge-secondary">
                                                    {{ $ketidaksesuaianPlastik->shift->shift ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <!-- <tr>
                                            <th>Plan</th>
                                            <td>{{ $ketidaksesuaianPlastik->plan->nama_plan ?? '-' }}</td>
                                        </tr> -->
                                        <!-- <tr>
                                            <th>User</th>
                                            <td>{{ $ketidaksesuaianPlastik->user->name ?? '-' }}</td>
                                        </tr> -->
                                        <tr>
                                            <th>Nama Plastik</th>
                                            <td><strong>{{ $ketidaksesuaianPlastik->nama_plastik }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Ketidaksesuaian (Kg)</th>
                                            <td>{{ $ketidaksesuaianPlastik->jumlah_ketidaksesuaian ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Alasan Hold:</label>
                                        <div class="border p-3 bg-light">
                                            {{ $ketidaksesuaianPlastik->alasan_hold }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Hold Data:</label>
                                        <div class="border p-3 bg-light">
                                            {{ $ketidaksesuaianPlastik->hold_data }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Dokumentasi Tagging:</strong><br>
                                    @if($ketidaksesuaianPlastik->dokumentasi_tagging)
                                        <a href="#" data-toggle="modal" data-target="#modalTagging">
                                            <img src="{{ asset($assetPath . 'storage/' . $ketidaksesuaianPlastik->dokumentasi_tagging) }}" alt="Dokumentasi Tagging" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;">
                                        </a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="modalTagging" tabindex="-1" role="dialog" aria-labelledby="modalTaggingLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalTaggingLabel">Dokumentasi Tagging</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="{{ asset($assetPath . 'storage/' . $ketidaksesuaianPlastik->dokumentasi_tagging) }}" alt="Dokumentasi Tagging" style="max-width:100%;max-height:70vh;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <strong>Dokumentasi Penyimpangan Plastik:</strong><br>
                                    @if($ketidaksesuaianPlastik->dokumentasi_penyimpangan_plastik)
                                        <a href="#" data-toggle="modal" data-target="#modalPenyimpangan">
                                            <img src="{{ asset($assetPath . 'storage/' . $ketidaksesuaianPlastik->dokumentasi_penyimpangan_plastik) }}" alt="Dokumentasi Penyimpangan" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;">
                                        </a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="modalPenyimpangan" tabindex="-1" role="dialog" aria-labelledby="modalPenyimpanganLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalPenyimpanganLabel">Dokumentasi Penyimpangan Plastik</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="{{ asset($assetPath . 'storage/' . $ketidaksesuaianPlastik->dokumentasi_penyimpangan_plastik) }}" alt="Dokumentasi Penyimpangan" style="max-width:100%;max-height:70vh;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                            </div>

                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    @if(auth()->user()->hasPermissionTo('edit-ketidaksesuaian-plastik'))
                                    <a href="{{ route('ketidaksesuaian-plastik.edit', $ketidaksesuaianPlastik->uuid) }}" class="btn btn-warning btn-md">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    @endif

                                    <a href="{{ route('ketidaksesuaian-plastik.export-pdf', $ketidaksesuaianPlastik->uuid) }}" class="btn btn-danger btn-md ml-2" target="_blank">
                                        <i class="fas fa-file-pdf"></i> Cetak PDF
                                    </a>

                                    <a href="{{ route('ketidaksesuaian-plastik.index') }}" class="btn btn-secondary btn-md ml-2">
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
