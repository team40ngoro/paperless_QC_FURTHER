@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Suhu Pemasakan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active">Suhu Pemasakan</li>
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
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-list"></i> Daftar Suhu Pemasakan</h3>
                            <div class="card-tools">
                                <a href="{{ route('suhu-blok.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(isset($suhuBlok) && count($suhuBlok))
                            <div class="table-responsive">
                                <table id="myTable" class="text-center table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Plan</th>
                                            <th class="text-center">Produk</th>
                                            <th class="text-center">Suhu Pemasakan</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($suhuBlok as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <span>{{ $item->plan->nama_plan ?? '-' }}</span>
                                            </td>
                                            <td>
                                                <span>{{ $item->produk->nama_produk ?? '-' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span>{{ $item->suhu_blok }}°C</span>
                                            </td>
                                            <td class="text-center">
                                                <!-- <div class="btn-group" role="group"> -->
                                                    <a href="{{ route('suhu-blok.edit', $item->uuid) }}" class="btn btn-warning btn-sm" title="Edit Data">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('suhu-blok.destroy', $item->uuid) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-danger btn-sm" title="Hapus Data">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <h5>Belum ada data Suhu Blok</h5>
                                <p class="mb-0">Silakan tambah data baru dengan mengklik tombol "Tambah Data" di atas.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection