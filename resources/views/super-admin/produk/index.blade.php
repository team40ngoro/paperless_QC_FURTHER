{{-- filepath: resources/views/super-admin/produk/index.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Jenis Produk</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Data Jenis Produk</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <button type="button" class="btn btn-sm btn-success float-right ml-2" data-toggle="modal" data-target="#importExcelModal">
                              <i class="fas fa-file-excel"></i> Import Excel
                            </button>
                            <a href="{{ route('produk.create') }}" class="btn btn-sm btn-primary float-right">
                                <i class="fas fa-plus"></i> Tambah Produk
                            </a>
                        </div>

                        <!-- Modal Import Excel -->
                        <div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <form action="{{ route('produk.import-excel') }}" method="POST" enctype="multipart/form-data" class="w-100">
                              @csrf
                              <div class="modal-content shadow-lg border-0 rounded-3">
                                <div class="modal-header bg-success text-white rounded-top">
                                  <h5 class="modal-title" id="importExcelModalLabel">
                                    <i class="fas fa-file-excel"></i> Import Jenis Produk dari Excel
                                  </h5>
                                  <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:1;">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                  <div class="alert alert-info mb-3">
                                    <strong>Petunjuk:</strong> Download template Excel terlebih dahulu, isi data sesuai format, lalu upload kembali file tersebut untuk import data produk.
                                  </div>
                                  <div class="d-flex justify-content-center mb-3">
                                    <a href="{{ route('produk.download-template') }}" class="btn btn-outline-primary">
                                      <i class="fas fa-download"></i> Download Template
                                    </a>
                                  </div>
                                  <div class="form-group">
                                    <label for="file" class="font-weight-bold">Pilih File Excel (.xlsx)</label>
                                    <input type="file" name="file" class="form-control-file" accept=".xlsx,.xls" required>
                                  </div>
                                </div>
                                <div class="modal-footer bg-light rounded-bottom">
                                  <button type="submit" class="btn btn-success">
                                    <i class="fas fa-upload"></i> Import
                                  </button>
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times"></i> Batal
                                  </button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                        {{-- End Modal --}}
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            <table id="myTable" class="table text-center table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>Jenis Produk</th>
                                        <th>Nama Plan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jenis_produk as $item)
                                    <tr>
                                    <td>{{ $loop->iteration }}</td>
                                        <td><span style="text-transform:uppercase;">{{ $item->nama_produk }}</span></td>
                                        <td>{{ $item->status_bahan }}</td>
                                        <td>{{ $item->plan->nama_plan ?? '-' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('produk.edit', $item->uuid) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('produk.destroy', $item->uuid) }}" method="POST" style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus data?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection