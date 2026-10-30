@extends('layouts.app')

@section('title', 'Detail KPI Produk YUM')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail KPI Produk YUM</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('produk-yum.index') }}">KPI Produk YUM</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- Basic Information -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-2"></i>Informasi Dasar
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Produk YUM</strong></td>
                                        <td>: {{ $produkYum->produk->nama_produk ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Data Pack</strong></td>
                                        <td>: {{ $produkYum->dataBag->std_bag ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kode Produksi</strong></td>
                                        <td>: {{ $produkYum->kode_produksi }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Best Before</strong></td>
                                        <td>: {{ $produkYum->kode_exp }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Shift</strong></td>
                                        <td>: {{ $produkYum->shift->shift ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal</strong></td>
                                        <td>: {{ $produkYum->tanggal ? $produkYum->tanggal->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <!-- <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Plan</strong></td>
                                        <td>: {{ $produkYum->plan->nama_plan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>User</strong></td>
                                        <td>: {{ $produkYum->user->name ?? '-' }}</td>
                                    </tr>
                                    
                                    <tr>
                                        <td><strong>Dibuat</strong></td>
                                        <td>: {{ $produkYum->created_at ? $produkYum->created_at->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                </table>
                            </div> -->
                        </div>
                    </div>
                </div>

                <!-- A. Aktual Berat -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-balance-scale mr-2"></i>A. Aktual Berat Per Pack (gram)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $aktualBeratList = $produkYum->aktual_berat;
                                if (is_string($aktualBeratList)) {
                                    $decoded = json_decode($aktualBeratList, true);
                                    $aktualBeratList = is_array($decoded) ? $decoded : (trim($aktualBeratList) !== '' ? explode(',', $aktualBeratList) : []);
                                }
                                if (!is_array($aktualBeratList)) {
                                    $aktualBeratList = [];
                                }
                            @endphp
                            @if(count($aktualBeratList) > 0)
                                @foreach($aktualBeratList as $index => $aktual)
                                    <div class="col-md-4 mb-3">
                                        <label>Aktual Berat Per Pack (gram) {{ $index + 1 }} </label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ trim($aktual) }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-success text-white">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <p class="text-center text-muted">Tidak ada data aktual berat</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- B. Jumlah PCS -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calculator mr-2"></i>B. Jumlah Isi Per Pack (pcs)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $jumlahPcsList = $produkYum->jumlah_pcs;
                                if (is_string($jumlahPcsList)) {
                                    $decoded = json_decode($jumlahPcsList, true);
                                    $jumlahPcsList = is_array($decoded) ? $decoded : (trim($jumlahPcsList) !== '' ? explode(',', $jumlahPcsList) : []);
                                }
                                if (!is_array($jumlahPcsList)) {
                                    $jumlahPcsList = [];
                                }
                            @endphp
                            @if(count($jumlahPcsList) > 0)
                                @foreach($jumlahPcsList as $index => $jumlah)
                                    <div class="col-md-4 mb-3">
                                        <label> Jumlah Isi per Pack (pcs) {{ $index + 1 }}</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ trim($jumlah) }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-success text-white">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <p class="text-center text-muted">Tidak ada data jumlah PCS</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- C. Berat PCS -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-weight mr-2"></i>C. Berat Per Potong (gram)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $beratPcsList = $produkYum->berat_pcs;
                                if (is_string($beratPcsList)) {
                                    $decoded = json_decode($beratPcsList, true);
                                    $beratPcsList = is_array($decoded) ? $decoded : (trim($beratPcsList) !== '' ? explode(',', $beratPcsList) : []);
                                }
                                if (!is_array($beratPcsList)) {
                                    $beratPcsList = [];
                                }
                            @endphp
                            @if(count($beratPcsList) > 0)
                                @foreach($beratPcsList as $index => $berat)
                                    <div class="col-md-4 mb-3">
                                        <label>Berat Per Potong (gram) {{ $index + 1 }}</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ trim($berat) }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-success text-white">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <p class="text-center text-muted">Tidak ada data berat PCS</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-footer">
                        <!--
@if(auth()->user()->hasPermissionTo('edit-produk-yum')) <a href="{{ route('produk-yum.edit', $produkYum->uuid) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Data
                        </a> @endif
-->
                        <a href="{{ route('produk-yum.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <!--
@if(auth()->user()->hasPermissionTo('delete-produk-yum')) <form action="{{ route('produk-yum.destroy', $produkYum->uuid) }}" 
                              method="POST" style="display: inline-block;"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Hapus Data
                            </button>
                        </form> @endif
-->
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
