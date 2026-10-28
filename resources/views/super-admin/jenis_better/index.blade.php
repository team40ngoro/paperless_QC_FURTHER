@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Jenis Better</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Data Jenis Better</li>
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
                            <div class="d-flex justify-content-between align-items-center">
                                <form action="{{ route('jenis-better.index') }}" method="GET" class="form-inline">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="search" class="form-control" placeholder="Cari Nama Produk..." value="{{ $search }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                            @if($search)
                                                <a href="{{ route('jenis-better.index') }}" class="btn btn-default"><i class="fas fa-times"></i></a>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                                <a href="{{ route('jenis-better.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Tambah Data</a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            @forelse($paginated_data as $produkId => $items)
                                @php $produk = $items[0]->produk; @endphp
                                <div class="card card-outline card-primary mb-3">
                                    <div class="card-header">
                                        <h4 class="card-title">
                                            <i class="fas fa-box mr-2"></i>{{ $produk->nama_produk ?? 'Produk #' . $produkId }}
                                        </h4>
                                        <div class="card-tools">
                                            <span class="badge badge-info">{{ count($items) }} jenis</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-0 table-responsive">
                                        <table class="table text-center table-bordered table-striped mb-0" style="white-space:nowrap;">
                                            <thead>
                                                <tr>
                                                    <th style="width:60px;">No</th>
                                                    <th>Nama Better</th>
                                                    <th>Nama Bahan</th>
                                                    <th>Berat (kg)</th>
                                                    <th style="width:140px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($items as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td class="text-uppercase font-weight-bold">{{ $item->nama_better ?? $item->nama_formula_better ?? '-' }}</td>
                                                    <td class="text-left">
                                                        @php $betterItems = collect($item->better_items); @endphp
                                                        @if($betterItems->isNotEmpty())
                                                            <ul class="mb-0 pl-3">
                                                                @foreach($betterItems as $bi)
                                                                    @if(!empty($bi['nama_formula_better']))
                                                                        <li>{{ $bi['nama_formula_better'] }}</li>
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            {{ $item->nama_formula_better ?? '-' }}
                                                        @endif
                                                    </td>
                                                    <td class="text-left">
                                                        @if($betterItems->isNotEmpty())
                                                            <ul class="mb-0 pl-3">
                                                                @foreach($betterItems as $bi)
                                                                    @if(!empty($bi['berat']))
                                                                        <li>{{ $bi['berat'] }} kg</li>
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            {{ $item->berat ? $item->berat . ' kg' : '-' }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('jenis-better.edit', $item->uuid) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                        <form action="{{ route('jenis-better.destroy', $item->uuid) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info">Data tidak ditemukan.</div>
                            @endforelse

                            <div class="d-flex justify-content-center mt-3">
                                {{ $paginated_data->appends(['search' => $search])->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection