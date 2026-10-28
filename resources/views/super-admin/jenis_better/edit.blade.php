@extends('layouts.app')

@section('container')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Jenis Better</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('jenis-better.index') }}">Jenis Better</a></li>
                            <li class="breadcrumb-item active">Edit Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">Edit Data Jenis Better</h3>
                            </div>
                            <form action="{{ route('jenis-better.update', $item->uuid) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Plan</label>
                                        <select name="id_plan" class="form-control" required>
                                            @foreach($plans as $plan)
                                                <option value="{{ $plan->id }}" {{ $item->id_plan == $plan->id ? 'selected' : '' }}>{{ $plan->nama_plan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Produk</label>
                                        <select name="id_produk" class="form-control" required>
                                            @foreach($produks as $produk)
                                                <option value="{{ $produk->id }}" {{ $item->id_produk == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Nama Better</label>
                                        <input type="text" name="nama_better" class="form-control @error('nama_better') is-invalid @enderror" value="{{ old('nama_better', $item->nama_better) }}" required>
                                        @error('nama_better')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Nama Formula Better & Berat Better</label>
                                        @php
                                            $betterItems = old('nama_formula_better') !== null ? null : ($item->better_items ?? []);
                                            if (is_array(old('nama_formula_better'))) {
                                                $betterItems = [];
                                                foreach (old('nama_formula_better') as $i => $nf) {
                                                    $betterItems[] = [
                                                        'nama_formula_better' => $nf,
                                                        'berat' => is_array(old('berat')) ? (old('berat')[$i] ?? null) : null,
                                                    ];
                                                }
                                            }
                                            if (empty($betterItems)) {
                                                $betterItems = [[
                                                    'nama_formula_better' => old('nama_formula_better', $item->nama_formula_better),
                                                    'berat' => old('berat', $item->berat),
                                                ]];
                                            }
                                        @endphp
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
                                                    @foreach($betterItems as $bi)
                                                        <tr>
                                                            <td>
                                                                <input type="text" name="nama_formula_better[]" class="form-control @error('nama_formula_better') is-invalid @enderror" value="{{ $bi['nama_formula_better'] ?? '' }}" required>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="berat[]" class="form-control @error('berat') is-invalid @enderror" value="{{ $bi['berat'] ?? '' }}">
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-danger btn-sm" data-remove-better-row="true">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <button type="button" class="btn btn-success btn-sm" id="addBetterRowBtn">
                                            <i class="fas fa-plus"></i> Tambah Baris
                                        </button>
                                        @error('nama_formula_better')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
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
