@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Input Area</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Input Area</li>
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
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title">
                                    <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                    Data Input Area
                                </h3>
                                <a href="{{ route('input-area.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if($inputAreas->isEmpty())
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle"></i>
                                    Belum ada data input area yang tersedia.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table id="myTable" class="table table-bordered table-striped" style="white-space:nowrap;">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <!-- <th class="text-center">Plan</th> -->
                                                <th class="text-center">Area</th>
                                                <th class="text-center">Sub Area</th>
                                                <!-- <th class="text-center">User Input</th> -->
                                                <!-- <th class="text-center">Tanggal Dibuat</th> -->
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inputAreas as $index => $item)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <!-- <td class="text-center">
                                                        <span class="badge badge-primary">
                                                            {{ $item->plan->nama_plan ?? '-' }}
                                                        </span>
                                                    </td> -->
                                                    <td class="text-center">
                                                        <span class="text-truncate" style="max-width: 200px; display: inline-block;" title="{{ $item->area }}">
                                                            {{ Str::limit($item->area, 50) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($item->subarea->isNotEmpty())
                                                            @foreach($item->subarea as $sub)
                                                                <span class="badge badge-success">{{ $sub->lokasi_area }}</span>
                                                            @endforeach
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <!-- <td class="text-center">
                                                        <span class="badge badge-info">
                                                            {{ $item->user->name ?? '-' }}
                                                        </span>
                                                    </td> -->
                                                    <!-- <td class="text-center">
                                                        <span class="badge badge-secondary">
                                                            {{ $item->created_at ? $item->created_at->format('d-m-Y H:i:s') : '-' }}
                                                        </span>
                                                    </td> -->
                                                    <td class="text-center">
                                                        <div class="btn-vertical" role="group">
                                                            <!-- <a href="{{ route('input-area.show', $item->uuid) }}" 
                                                               class="btn btn-info btn-sm" title="Lihat Detail">
                                                                <i class="fas fa-eye"></i>
                                                            </a> -->
                                                            <a href="{{ route('input-area.edit', $item->uuid) }}" 
                                                               class="btn btn-warning btn-sm" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('input-area.destroy', $item->uuid) }}" 
                                                                  method="POST" style="display: inline-block;" 
                                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection