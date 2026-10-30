@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-fan text-primary"></i> Data Std Fan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active"><i class="fas fa-fan"></i> Std Fan</li>
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
                            <h3 class="card-title"><i class="fas fa-list"></i> Daftar Std Fan</h3>
                            <div class="card-tools">
                                <a href="{{ route('std-fan.create') }}" class="btn btn-primary btn-sm">
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
                                <table id="myTable" class="table table-striped table-hover" style="white-space: nowrap;">
                                    <thead  >
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Plan</th>
                                            <th class="text-center">Produk</th>
                                            <th class="text-center">Suhu Pemasakan</th>
                                            <th class="text-center">Std Fan 1 (%)</th>
                                            <th class="text-center">Std Fan 2 (%)</th>
                                            <th class="text-center">Std Fan 3 (%)</th>
                                            <th class="text-center">Std Fan 4 (%)</th>
                                            <th class="text-center">Std Humidity/Steam Valve</th>
                                            <th class="text-center">Std Lama Proses</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stdFan as $index => $item)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <span>{{ $item->plan->nama_plan ?? '-' }}</span>
                                                </td>
                                                <td>
                                                    <span>{{ $item->produk->nama_produk ?? '-' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span>{{ $item->suhuBlok->suhu_blok ?? '-' }}°C</span>
                                                </td>
                                                <td class="text-center">
                                                    <span>{{ $item->std_fan }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span>{{ $item->std_fan_2 }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span>{{ $item->fan_3 ?? '-' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span>{{ $item->fan_4 ?? '-' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span>{{ $item->std_humadity ?? '-' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span>{{ $item->std_lama_proses ?? '-' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <!-- <div class="btn-group" role="group"> -->
                                                        <a href="{{ route('std-fan.edit', $item->uuid) }}" 
                                                           class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('std-fan.destroy', $item->uuid) }}" 
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    <!-- </div> -->
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <br>
                                                    Belum ada data Std Fan
                                                </td>
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