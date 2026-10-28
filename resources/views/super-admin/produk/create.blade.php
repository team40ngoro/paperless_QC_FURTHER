{{-- filepath: resources/views/super-admin/produk/create.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Jenis Produk</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('produk.index') }}">Data Jenis Produk</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Form Tambah Jenis Produk</h3>
                        </div>
                        <form action="{{ route('produk.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="id_plan">Nama Plan</label>
                                    <select name="id_plan" id="id_plan" class="form-control" required>
                                        <option value="">Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ old('id_plan') == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->nama_plan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="nama_produk">Nama Produk</label>
                                    <input type="text" name="nama_produk" id="nama_produk" class="form-control"
                                           placeholder="Masukkan Nama Produk" value="{{ old('nama_produk') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="status_bahan">Jenis Produk</label>
                                    <select name="status_bahan" id="status_bahan" class="form-control" required>
                                        <option value="" selected disabled hidden>Pilih Jenis Produk</option>
                                        <option value="forming" {{ old('status_bahan') == 'forming' ? 'selected' : '' }}>Forming</option>
                                        <option value="non-forming" {{ old('status_bahan') == 'non-forming' ? 'selected' : '' }}>Non Forming</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_produk_rework">Produk Rework</label>
                                    <small class="text-muted d-block mb-1">Pilih produk yang bisa dijadikan rework (semua plan)</small>
                                    <select name="id_produk_rework[]" id="id_produk_rework"
                                            class="form-control select2-produk-rework"
                                            multiple
                                            data-placeholder="Cari & pilih produk rework...">
                                        @foreach($allProduk as $p)
                                            <option value="{{ $p->id }}"
                                                {{ is_array(old('id_produk_rework')) && in_array($p->id, old('id_produk_rework')) ? 'selected' : '' }}>
                                                [{{ $p->plan->nama_plan ?? '-' }}] {{ $p->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                <a href="{{ route('produk.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
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
    $('.select2-produk-rework').select2({
        theme: 'bootstrap4',
        placeholder: 'Cari & pilih produk rework...',
        allowClear: true,
        width: '100%',
    });
});
</script>
@endpush
