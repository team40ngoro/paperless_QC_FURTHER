@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Barang</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Data Barang</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Barang</h3>
                            <a href="{{ route('data-barang.create') }}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Tambah Data</a>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <form method="GET" action="{{ route('data-barang.index') }}" class="mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label class="small text-muted">Filter Area</label>
                                            <select name="id_area" id="filter-area" class="form-control select2" onchange="this.form.submit()" style="width:100%;">
                                                <option value="">Semua Area</option>
                                                @foreach($areas as $area)
                                                    <option value="{{ $area->id }}" {{ (string) $selectedAreaId === (string) $area->id ? 'selected' : '' }}>
                                                        {{ $area->area }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label class="small text-muted">Cari Nama Barang</label>
                                            <div class="input-group">
                                                <input type="text" name="search" class="form-control" placeholder="Ketik nama barang..." value="{{ $search ?? '' }}">
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($selectedAreaId || $search)
                                        <div class="col-md-2 d-flex align-items-end">
                                            <a href="{{ route('data-barang.index') }}" class="btn btn-default btn-sm">
                                                <i class="fas fa-times"></i> Reset
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </form>

                            @forelse($groupedData as $idArea => $items)
                                @php $area = $items->first()->area; @endphp
                                <div class="card card-outline card-primary mb-3">
                                    <div class="card-header">
                                        <h4 class="card-title">
                                            <i class="fas fa-map-marker-alt mr-2"></i>{{ $area->area ?? 'Area #' . $idArea }}
                                        </h4>
                                        <div class="card-tools">
                                            <span class="badge badge-info">{{ $items->count() }} item</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-0 table-responsive">
                                        <table class="table text-center table-bordered table-striped mb-0" style="white-space:nowrap;">
                                            <thead>
                                                <tr>
                                                    <th style="width:60px;">No</th>
                                                    <th>Nama Barang</th>
                                                    <th style="width:120px;">Jumlah</th>
                                                    <th style="width:140px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($items as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td style="text-transform:uppercase">{{ Str::limit($item->nama_barang, 50) }}</td>
                                                    <td>
                                                        <span class="badge badge-primary">
                                                            {{ number_format($item->jumlah ?? 0) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-vertical" role="group">
                                                            <a href="{{ route('data-barang.edit', $item->uuid) }}" 
                                                               class="btn btn-warning btn-sm" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            @if(auth()->user()->role === 'superadmin' || $item->id_plan === auth()->user()->id_plan)
                                                                <form action="{{ route('data-barang.destroy', $item->uuid) }}" 
                                                                      method="POST" style="display: inline-block;" 
                                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info">Belum ada data.</div>
                            @endforelse

                            <div class="d-flex justify-content-center mt-3">
                                {{ $groupedData->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection