@extends('layouts.app')

@section('container')
<style>
    .defect-badge {
        font-size: 0.8rem;
        background-color: #f1f3f5;
        border-left: 3px solid #17a2b8;
        padding: 4px 8px;
        margin-bottom: 4px;
        border-radius: 0 4px 4px 0;
    }
    .kategori-title {
        color: #17a2b8;
        font-weight: bold;
        font-size: 0.85rem;
        margin-top: 5px;
        margin-bottom: 3px;
        display: block;
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Defect</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                            <li class="breadcrumb-item active">Data Defect</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header bg-light">
                                <h3 class="card-title font-weight-bold text-primary">Daftar Data Defect</h3>
                                <div class="card-tools">
                                    <a href="{{ route('data-defect.create') }}" class="btn btn-primary btn-sm shadow-sm">
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

                                <div class="table-responsive">
                                    <table id="myTable" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="text-center" style="width: 50px;">No</th>
                                               
                                                <th style="width: 200px;">Nama Produsen</th>
                                                <th style="min-width: 450px;">Detail Defect (Kategori > Jenis > Sub Jenis [Spesifikasi])</th>
                                                <th class="text-center" style="width: 100px; white-space: nowrap;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($data as $index => $item)
                                                <tr>
                                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                                   
                                                    <td class="align-middle font-weight-bold">{{ $item->nama_produsen ?? '-' }}</td>
                                                    <td class="align-middle" style="white-space: normal;">
                                                        @php
                                                            $detail = $item->detail_defect ?? [];
                                                        @endphp
                                                        @if(is_array($detail) && count($detail) > 0)
                                                            <div class="row">
                                                                @foreach($detail as $cat)
                                                                    <div class="col-md-6 mb-2">
                                                                        <span class="kategori-title">
                                                                            <i class="fas fa-folder mr-1"></i>{{ $cat['kategori'] ?? 'Tanpa Kategori' }}
                                                                        </span>
                                                                        @if(isset($cat['items']) && is_array($cat['items']))
                                                                            @foreach($cat['items'] as $it)
                                                                                <div class="defect-badge d-flex justify-content-between align-items-center mb-1 p-2" style="white-space: normal; background-color: #f8f9fa; border-left: 3px solid #17a2b8; border-radius: 4px;">
                                                                                    <div class="mr-2" style="word-break: break-word;">
                                                                                        <div>
                                                                                            <i class="fas fa-caret-right text-info mr-1"></i>
                                                                                            <span class="text-muted" style="font-size:0.75rem;">Jenis:</span>
                                                                                            <strong style="font-size:0.85rem;">{{ $it['jenis'] ?? '-' }}</strong>
                                                                                        </div>
                                                                                        @if(!empty($it['sub_jenis']))
                                                                                            <div class="mt-1">
                                                                                                <i class="fas fa-caret-right text-info mr-1"></i>
                                                                                                <span class="text-muted" style="font-size:0.75rem;">Sub Jenis:</span>
                                                                                                <span class="badge badge-info" style="font-size:0.75rem; white-space: normal; text-align: left;">{{ $it['sub_jenis'] }}</span>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                    @if(!empty($it['spesifikasi']))
                                                                                        <div class="text-right ml-2 flex-shrink-0">
                                                                                            <span class="text-muted mr-1" style="font-size:0.75rem;">Spec:</span>
                                                                                            <span class="badge badge-success" style="font-size:0.75rem; white-space: normal; text-align: left; max-width: 160px; display: inline-block; vertical-align: middle;">{{ $it['spesifikasi'] }}</span>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center align-middle" style="white-space: nowrap;">
                                                        <div class="d-flex justify-content-center align-items-center" style="gap: 6px;">
                                                            <a href="{{ route('data-defect.edit', $item->uuid) }}"
                                                               class="btn btn-warning btn-sm shadow-sm" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('data-defect.destroy', $item->uuid) }}"
                                                                  method="POST" class="d-inline"
                                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data defect ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm shadow-sm" title="Hapus">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4">
                                                        <div class="py-4">
                                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                            <p class="text-muted">Belum ada data defect</p>
                                                            <a href="{{ route('data-defect.create') }}" class="btn btn-primary btn-sm shadow-sm">
                                                                <i class="fas fa-plus"></i> Tambah Data Pertama
                                                            </a>
                                                        </div>
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
</div>
@endsection