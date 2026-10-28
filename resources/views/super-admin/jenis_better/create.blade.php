{{-- filepath: resources/views/super-admin/jenis_better/create.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Jenis Better</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('jenis-better.index') }}">Jenis Better</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Tambah Data Jenis Better</h3>
                        </div>
                        <form action="{{ route('jenis-better.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group mb-2">
                                    <label>Nama Plan</label>
                                        <select name="id_plan" id="id_plan_select" class="form-control" required>
                                            <option value="">Pilih Plan</option>
                                            @foreach($plans as $plan)
                                                <option value="{{ $plan->id }}">{{ $plan->nama_plan }}</option>
                                            @endforeach
                                        </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Nama Produk</label>
                                        <select name="id_produk" id="id_produk_select" class="form-control" required>
                                            <option value="">Pilih Produk</option>
                                            {{-- Akan diisi AJAX --}}
                                        </select>
                                </div>
                                <div class="mb-3">
                                    <label>Nama Better</label>
                                    <input type="text" name="nama_better" class="form-control" value="{{ old('nama_better') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Nama Formula Better & Berat Better</label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="betterItemsTable">
                                            <thead>
                                                <tr>
                                                    <th>Nama Formula Better</th>
                                                    <th style="width: 30%;">Berat (kg)</th>
                                                    <th style="width: 10%;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="text" name="nama_formula_better[]" class="form-control" required>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="berat[]" class="form-control">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-danger btn-sm" data-remove-better-row="true">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm" id="addBetterRowBtn">
                                        <i class="fas fa-plus"></i> Tambah Baris
                                    </button>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                <a href="{{ route('jenis-better.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
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
    $(document).ready(function() {
        $('#addBetterRowBtn').on('click', function() {
            const row = `
                <tr>
                    <td><input type="text" name="nama_formula_better[]" class="form-control" required></td>
                    <td><input type="text" name="berat[]" class="form-control"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm" data-remove-better-row="true">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#betterItemsTable tbody').append(row);
        });

        $(document).on('click', '[data-remove-better-row="true"]', function() {
            const tbody = $('#betterItemsTable tbody');
            if (tbody.find('tr').length <= 1) {
                return;
            }
            $(this).closest('tr').remove();
        });
    });
</script>
@endpush
