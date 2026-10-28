@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Bahan Emulsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Bahan Emulsi</li>
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
                            <h3 class="card-title">Data Bahan Emulsi</h3>
                            <a href="{{ route('bahan-emulsi.create') }}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Tambah Data</a>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <form method="GET" action="{{ route('bahan-emulsi.index') }}" class="mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label class="small text-muted">Filter Produk</label>
                                            <select name="id_produk" id="filter-produk" class="form-control select2" onchange="this.form.submit()" style="width:100%;">
                                                <option value="">Semua Produk</option>
                                                @foreach($produks as $produk)
                                                    <option value="{{ $produk->id }}" {{ (string) $selectedProdukId === (string) $produk->id ? 'selected' : '' }}>
                                                        {{ $produk->nama_produk }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @if($selectedProdukId)
                                        <div class="col-md-2 d-flex align-items-end">
                                            <a href="{{ route('bahan-emulsi.index') }}" class="btn btn-default btn-sm">
                                                <i class="fas fa-times"></i> Reset
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </form>

                            @forelse($groupedData as $idProduk => $items)
                                @php $produk = $items->first()->produk; @endphp
                                <div class="card card-outline card-primary mb-3">
                                    <div class="card-header">
                                        <h4 class="card-title">
                                            <i class="fas fa-box mr-2"></i>{{ $produk->nama_produk ?? 'Produk #' . $idProduk }}
                                        </h4>
                                        <div class="card-tools">
                                            <span class="badge badge-info">{{ $items->count() }} data</span>
                                        </div>
                                    </div>
                                    <div class="card-body pt-3">
                                        @php
                                            $processGroups = $items->groupBy(function($item) {
                                                return $item->nomor_emulsi_id . '_' . $item->total_pemakaian_id . '_' . $item->nama_emulsi_id;
                                            });
                                        @endphp

                                        @foreach($processGroups as $items)
                                            @php $first = $items->first(); @endphp
                                            <div class="card card-outline card-secondary mb-3">
                                                <div class="card-header py-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>
                                                            <strong>No. Proses:</strong> {{ $first->nomor_emulsi->nomor_emulsi ?? '-' }} |
                                                            <strong>Total Pemakaian:</strong> {{ $first->total_pemakaian->total_pemakaian ?? '-' }} |
                                                            <strong>Emulsi:</strong> {{ $first->emulsi->nama_emulsi ?? '-' }}
                                                        </span>
                                                        <span class="badge badge-secondary">{{ $items->count() }} RM</span>
                                                    </div>
                                                </div>
                                                <div class="card-body p-0 table-responsive">
                                                    <table class="table text-center table-bordered table-striped mb-0" style="white-space:nowrap;">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:60px;">No</th>
                                                                <th>Nama RM</th>
                                                                <th>Berat RM (kg)</th>
                                                                <th style="width:140px;">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($items as $index => $item)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td><span style="text-transform:uppercase;">{{ $item->nama_rm }}</span></td>
                                                                <td>{{ $item->berat_rm }} kg</td>
                                                                <td class="text-center">
                                                                    <a href="{{ route('bahan-emulsi.edit', $item->uuid) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                                                    <form action="{{ route('bahan-emulsi.destroy', $item->uuid) }}" method="POST" style="display:inline;">
                                                                        @csrf @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data?')"><i class="fas fa-trash"></i></button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endforeach
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

@push('scripts')
<script>
$(document).ready(function() {
    $('#filter-produk').select2();
});
</script>
@endpush