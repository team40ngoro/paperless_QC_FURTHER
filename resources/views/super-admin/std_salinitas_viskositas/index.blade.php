@extends('layouts.app')

@section('title', 'Data Std Salinitas & Viskositas')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">

        {{-- ===== PAGE HEADER ===== --}}
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Std Salinitas & Viskositas</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <li class="breadcrumb-item active">Std Salinitas & Viskositas</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        {{-- ===== END PAGE HEADER ===== --}}

        {{-- ===== MAIN CONTENT ===== --}}
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline">

                            {{-- Card Header --}}
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-flask mr-1"></i>
                                    Data Std Salinitas & Viskositas
                                </h3>
                                <div class="card-tools">
                                    <a href="{{ route('std-salinitas-viskositas.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a>
                                </div>
                            </div>
                            {{-- End Card Header --}}

                            <div class="card-body">

                                {{-- ===== ALERT MESSAGES ===== --}}
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h5><i class="icon fas fa-check"></i> Sukses!</h5>
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                        {{ session('error') }}
                                    </div>
                                @endif
                                {{-- ===== END ALERT MESSAGES ===== --}}

                                {{-- ===== DATA TABLE ===== --}}
                                <div class="table-responsive">
                                    <table id="myTable" class="table table-bordered table-striped text-center" style="white-space: nowrap;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 5%">No</th>
                                                <th>Plan</th>
                                                <th>Produk</th>
                                                <th>Better</th>
                                                <th>Std Viskositas (Detik)</th>
                                                <th>Std Salinitas (%)</th>
                                                <th>Std Suhu Air (°C)</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($data as $i => $item)
                                                <tr>
                                                    {{-- Nomor --}}
                                                    <td>{{ $i + 1 }}</td>

                                                    {{-- Plan --}}
                                                    <td>{{ $item->plan->nama_plan ?? '-' }}</td>

                                                    {{-- Produk --}}
                                                    <td>{{ $item->produk->nama_produk ?? '-' }}</td>

                                                    {{-- Better --}}
                                                    <td>{{ $item->better->nama_better ?? '-' }}</td>

                                                    {{-- Std Viskositas --}}
                                                    <td>{{ $item->std_viskositas ?? '-' }}</td>

                                                    {{-- Std Salinitas --}}
                                                    <td>{{ $item->std_salinitas ?? '-' }}</td>

                                                    {{-- Std Suhu Air --}}
                                                    <td>{{ $item->std_suhu_akhir ?? '-' }}</td>

                                                    {{-- Aksi --}}
                                                    <td>
                                                        <a href="{{ route('std-salinitas-viskositas.edit', $item->uuid) }}"
                                                           class="btn btn-warning btn-sm"
                                                           title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('std-salinitas-viskositas.destroy', $item->uuid) }}"
                                                              method="POST"
                                                              style="display: inline;"
                                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                    {{-- End Aksi --}}

                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">
                                                        <i class="fas fa-inbox"></i> Tidak ada data
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                {{-- ===== END DATA TABLE ===== --}}

                            </div>
                            {{-- /.card-body --}}
                        </div>
                        {{-- /.card --}}
                    </div>
                </div>
            </div>
        </section>
        {{-- ===== END MAIN CONTENT ===== --}}

    </div>
</div>
@endsection