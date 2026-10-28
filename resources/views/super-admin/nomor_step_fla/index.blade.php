@extends('layouts.app')

@section('title', 'Data Nomor Step Formula FLA')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Nomor Step Formula FLA</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/super-admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Nomor Step Formula FLA</li>
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
                            <h3 class="card-title">Daftar Nomor Step Formula FLA</h3>
                            <div class="card-tools">
                                <a href="{{ route('nomor-step-formula-fla.create') }}" class="btn btn-primary btn-sm">
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
                                <table class=" table table-bordered table-striped" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Plan</th>
                                        
                                            <th>Nama Produk</th>
                                            <th>Nama Formula FLA</th>
                                            <th>Nomor Step</th>
                                            <th>Proses</th>
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
                                                <td>{{ $item->nomor_step }}</td>
                                                <td>
                                                    @php
                                                        $prosesArray = explode(',', $item->proses);
                                                    @endphp
                                                    @foreach($prosesArray as $proses)
                                                        <span class="badge badge-info">{{ trim($proses) }}</span>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        
                                                        <a href="{{ route('nomor-step-formula-fla.edit', $item->uuid) }}" 
                                                           class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('nomor-step-formula-fla.destroy', $item->uuid) }}" 
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
