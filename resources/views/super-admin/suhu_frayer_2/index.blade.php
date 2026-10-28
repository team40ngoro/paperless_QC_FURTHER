@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Suhu Frayer 2</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Suhu Frayer 2</li>
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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Suhu Frayer 2</h3>
                            <div class="card-tools">
                                <a href="{{ route('suhu-frayer-2.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if($data->count() > 0)
                                <table id="myTable" class="table text-center table-bordered table-striped" style="white-space:nowrap;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Produk</th>
                                            <th>Plan</th>
                                            <th>Suhu Frayer 2</th>
                                              <th>Waktu Penggorengan 2</th>
                                            <!-- <th>User</th> -->
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                            <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                                            <td>{{ is_numeric($item->getRawOriginal('suhu_frayer_2')) ? floatval($item->suhu_frayer_2) : ($item->getRawOriginal('suhu_frayer_2') ?? '-') }} °C</td>
                                             <td>{{ $item->waktu_penggorengan_2 ?? '-' }} detik</td>
                                            <td>
                                                <a href="{{ route('suhu-frayer-2.edit', $item->uuid) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('suhu-frayer-2.destroy', $item->uuid) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Tidak ada data yang tersedia.
                                </div>
                            @endif
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection