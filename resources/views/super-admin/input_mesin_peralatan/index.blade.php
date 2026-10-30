@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Input Mesin/Peralatan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Input Mesin/Peralatan</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title">
                                    <i class="fas fa-tools text-primary mr-2"></i>
                                    Data Input Mesin/Peralatan
                                </h3>
                                <a href="{{ route('input-mesin-peralatan.create') }}" class="btn btn-sm btn-primary">
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

                            <form method="GET" action="{{ route('input-mesin-peralatan.index') }}" class="mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label for="area_id" class="mb-1">Filter Area</label>
                                            <select name="area_id" id="area_id" class="form-control" onchange="this.form.submit()">
                                                <option value="">Semua Area</option>
                                                @foreach(($areas ?? collect()) as $area)
                                                    <option value="{{ $area->id }}" {{ (string) $selectedAreaId === (string) $area->id ? 'selected' : '' }}>
                                                        {{ $area->area }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            @if($items->isEmpty())
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle"></i>
                                    Belum ada data input mesin/peralatan.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table id="myTable" class="table table-bordered table-striped" style="white-space:nowrap;">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Area</th>
                                                <th class="text-center" style="width:80px;">No</th>
                                                <th class="text-center">Jenis Item</th>
                                                <th class="text-center">Jenis Produk</th>
                                                <th class="text-center">Bagian/Detail</th>
                                                <th class="text-center" style="width:140px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items as $index => $item)
                                                <tr>
                                                    <td class="text-center">{{ $item->area->area ?? '-' }}</td>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td class="text-center">{{ $item->nama_mesin }}</td>
                                                    <td class="text-center">
                                                        @php
                                                            $jenisList = $item->jenis_produk ?? [];
                                                            if (is_string($jenisList)) {
                                                                $jenisList = json_decode($jenisList, true) ?? [];
                                                            }
                                                        @endphp
                                                        @if(!empty($jenisList) && is_array($jenisList))
                                                            @foreach($jenisList as $jenis)
                                                                <span class="badge {{ $jenis == 'Forming' ? 'badge-primary' : 'badge-warning' }} mr-1">
                                                                    {{ $jenis }}
                                                                </span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                       <td>
                                                        @if($item->bagian->isNotEmpty())
                                                            <ul class="mb-0 pl-3">
                                                                @foreach($item->bagian as $bagian)
                                                                    <li>{{ $bagian->nama_bagian }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('input-mesin-peralatan.edit', $item->uuid) }}" class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('input-mesin-peralatan.destroy', $item->uuid) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $items->appends(request()->query())->links('pagination::bootstrap-4') }}
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

@push('scripts')
<script>
$(document).ready(function () {
    if ($.fn.dataTable.isDataTable('#myTable')) {
        $('#myTable').DataTable().destroy();
    }

    $('#myTable').DataTable({
        paging: false,
        searching: false,
        order: [[0, 'asc'], [2, 'asc']],
        rowGroup: {
            dataSrc: 0
        },
        columnDefs: [
            { targets: 0, visible: false }
        ]
    });
});
</script>
@endpush