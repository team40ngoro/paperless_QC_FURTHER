@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Suhu Frayer 1,2,3,4,5</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Data Suhu Frayer 1,2,3,4,5</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Data Suhu Frayer 1,2,3,4,5</h3>
                            <div class="card-tools">
                                <a href="{{ route('suhu-frayer-1.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Tambah Data</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="myTable" class="table-responsive table-hover table text-center table-bordered table-striped" style="white-space:nowrap;">
                                <thead>
                                    <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>Plan</th>
                                    <th>Suhu Frayer 1</th>
                                    <th>Waktu Penggorengan 1</th>
                                    <th>Suhu Frayer 2</th>
                                    <th>Waktu Penggorengan 2</th>
                                    <th>Suhu Frayer 3</th>
                                    <th>Waktu Penggorengan 3</th>
                                    <th>Suhu Frayer 4</th>
                                    <th>Waktu Penggorengan 4</th>
                                    <th>Suhu Frayer 5</th>
                                    <th>Waktu Penggorengan 5</th>
                                    <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                                        <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                                        <td>{{ $item->suhu_frayer ?? '-' }}{!! $item->suhu_frayer ? ' <sup>o</sup>C' : '' !!}</td>
                                        <td>{{ $item->waktu_penggorengan_1 ? $item->waktu_penggorengan_1 . ' detik' : '-' }}</td>
                                        <td>{{ $item->suhu_frayer_2 ?? '-' }}{!! $item->suhu_frayer_2 ? ' <sup>o</sup>C' : '' !!}</td>
                                        <td>{{ $item->waktu_penggorengan_2 ? $item->waktu_penggorengan_2 . ' detik' : '-' }}</td>
                                        <td>{{ $item->suhu_frayer_3 ?? '-' }}{!! $item->suhu_frayer_3 ? ' <sup>o</sup>C' : '' !!}</td>
                                        <td>{{ $item->waktu_penggorengan_3 ? $item->waktu_penggorengan_3 . ' detik' : '-' }}</td>
                                        <td>{{ $item->suhu_frayer_4 ?? '-' }}{!! $item->suhu_frayer_4 ? ' <sup>o</sup>C' : '' !!}</td>
                                        <td>{{ $item->waktu_penggorengan_4 ? $item->waktu_penggorengan_4 . ' detik' : '-' }}</td>
                                        <td>{{ $item->suhu_frayer_5 ?? '-' }}{!! $item->suhu_frayer_5 ? ' <sup>o</sup>C' : '' !!}</td>
                                        <td>{{ $item->waktu_penggorengan_5 ? $item->waktu_penggorengan_5 . ' detik' : '-' }}</td>
                                        <td>
                                            <a href="{{ route('suhu-frayer-1.edit', $item->uuid) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('suhu-frayer-1.destroy', $item->uuid) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data?')"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection