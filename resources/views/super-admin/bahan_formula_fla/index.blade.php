@extends('layouts.app')

@section('title', 'Data Bahan Formula FLA')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Bahan Formula FLA</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/super-admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Bahan Formula FLA</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Daftar Bahan Formula FLA</h3>
                            <div class="card-tools">
                                <a href="{{ route('bahan-formula-fla.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="example1">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Plan</th>
                                            <th>Nama Produk</th>
                                            <th>Nama Formula FLA</th>
                                            <!-- <th>Step Formula</th> -->
                                            <th>Bahan Formula FLA</th>
                                            <th>Berat Formula FLA</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($items as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                                                <td>{{ $item->namaFormulaFla->produk->nama_produk ?? '-' }}</td>
                                                <td>{{ $item->namaFormulaFla->nama_formula_fla ?? '-' }}</td>
                                                <!-- <td>
                                                    <span class="badge badge-info">
                                                        Step {{ $item->nomorStepFormulaFla->nomor_step ?? '-' }}
                                                    </span>
                                                </td> -->
                                                <td>
                                                    @if($item->getBahanFormulaArray())
                                                        <ul class="list-unstyled mb-0">
                                                            @foreach($item->getBahanFormulaArray() as $bahan)
                                                                <li><i class="fas fa-circle text-primary" style="font-size: 6px;"></i> {{ $bahan }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->getBeratFormulaArray())
                                                        <ul class="list-unstyled mb-0">
                                                            @foreach($item->getBeratFormulaArray() as $berat)
                                                                <li><i class="fas fa-circle text-success" style="font-size: 6px;"></i> {{ $berat }} kg</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('bahan-formula-fla.show', $item->uuid) }}" 
                                                           class="btn btn-info btn-sm" title="Lihat">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('bahan-formula-fla.edit', $item->uuid) }}" 
                                                           class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('bahan-formula-fla.destroy', $item->uuid) }}" 
                                                              method="POST" style="display: inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" 
                                                                    title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Tidak ada data</td>
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
