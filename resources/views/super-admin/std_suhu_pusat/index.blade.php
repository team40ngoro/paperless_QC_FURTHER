@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-thermometer-half text-primary"></i> Data Standar Suhu Pusat</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active"><i class="fas fa-thermometer-half"></i> Standar Suhu Pusat</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline shadow">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-list"></i> Daftar Standar Suhu Pusat</h3>
                            <div class="card-tools">
                                <a href="{{ route('std-suhu-pusat.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fas fa-check"></i> {{ session('success') }}
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table id="myTable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Nama Produk</th>
                                            <th class="text-center">Plan</th>
                                            <th class="text-center">Standar Suhu Pusat °C</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($stdSuhuPusat as $item)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $item->produk?->nama_produk ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $item->plan?->nama_plan ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                @php
                                                    $suhuArray = is_array($item->std_suhu_pusat) ? $item->std_suhu_pusat : json_decode($item->std_suhu_pusat, true) ?? [];
                                                @endphp
                                                @if(count($suhuArray) > 0)
                                                    @foreach($suhuArray as $index => $suhu)
                                                        <span class="badge badge-info mr-1">F{{ $index + 1 }}: {{ $suhu }}°C</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('std-suhu-pusat.edit', $item->uuid) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                                <form action="{{ route('std-suhu-pusat.destroy', $item->uuid) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" title="Hapus"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Data tidak ditemukan</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection