@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Input Mesin/Peralatan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('input-mesin-peralatan.index') }}">Input Mesin/Peralatan</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                            <h3 class="card-title">
                                <i class="fas fa-edit text-warning mr-2"></i>
                                Form Edit Input Mesin/Peralatan
                            </h3>
                        </div>

                        <form action="{{ route('input-mesin-peralatan.update', $item->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="id_area">Area <span class="text-danger">*</span></label>
                                            <select class="form-control @error('id_area') is-invalid @enderror" id="id_area" name="id_area" required>
                                                <option value="">Pilih Area</option>
                                                @foreach($areas as $area)
                                                    <option value="{{ $area->id }}" {{ old('id_area', $item->id_area) == $area->id ? 'selected' : '' }}>
                                                        {{ $area->area }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_area')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="card mb-3">
                                            <div class="card-header bg-light d-flex align-items-center">
                                                <span class="font-weight-bold">Jenis Item</span>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="nama_mesin" class="text-muted small">Nama Jenis Item <span class="text-danger">*</span></label>
                                                    <input type="text" id="nama_mesin" name="nama_mesin" class="form-control @error('nama_mesin') is-invalid @enderror" value="{{ old('nama_mesin', $item->nama_mesin) }}" required>
                                                    @error('nama_mesin')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <label class="text-muted small mt-2">Bagian/Detail Mesin</label>
                                                <div id="bagian-container">
                                                    @php
                                                        $oldBagian = old('bagian', []);
                                                        $bagianList = (!empty($oldBagian) && is_array($oldBagian)) ? $oldBagian : ($item->bagian->pluck('nama_bagian')->toArray() ?: ['']);
                                                    @endphp
                                                    @foreach($bagianList as $bVal)
                                                        <div class="input-group mb-2 bagian-row">
                                                            <input type="text" name="bagian[]" class="form-control" placeholder="Masukkan nama bagian/detail mesin" value="{{ $bVal }}">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-danger remove-bagian" type="button">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <button id="add-bagian" type="button" class="btn btn-success btn-sm mt-2">
                                                    <i class="fas fa-plus"></i> Tambah Bagian
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <span class="font-weight-bold">Jenis Produk</span>
                                            </div>
                                            <div class="card-body">
                                                @php
                                                    $selectedJenis = old('jenis_produk', $item->jenis_produk ?? []);
                                                    if (is_string($selectedJenis)) {
                                                        $selectedJenis = json_decode($selectedJenis, true) ?? [];
                                                    }
                                                    if (!is_array($selectedJenis)) $selectedJenis = [];
                                                @endphp
                                                <div class="form-group mb-0">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="jenis_forming" name="jenis_produk[]" value="Forming"
                                                            {{ in_array('Forming', $selectedJenis) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="jenis_forming">Forming</label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox mt-2">
                                                        <input type="checkbox" class="custom-control-input" id="jenis_non_forming" name="jenis_produk[]" value="Non-Forming"
                                                            {{ in_array('Non-Forming', $selectedJenis) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="jenis_non_forming">Non-Forming</label>
                                                    </div>
                                                    @error('jenis_produk')
                                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <a href="{{ route('input-mesin-peralatan.index') }}" class="btn btn-md btn-secondary">
                                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-md btn-primary">
                                    <i class="fas fa-save mr-2"></i>Update Data
                                </button>
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
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('bagian-container');
    const addBtn = document.getElementById('add-bagian');

    addBtn.addEventListener('click', function () {
        const div = document.createElement('div');
        div.classList.add('input-group', 'mb-2', 'bagian-row');
        div.innerHTML = `
            <input type="text" name="bagian[]" class="form-control" placeholder="Masukkan nama bagian/detail mesin">
            <div class="input-group-append">
                <button class="btn btn-danger remove-bagian" type="button">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(div);
    });

    container.addEventListener('click', function (e) {
        if (e.target.closest('.remove-bagian')) {
            const row = e.target.closest('.bagian-row');
            if (container.querySelectorAll('.bagian-row').length > 1) {
                row.remove();
            } else {
                const input = row.querySelector('input');
                if (input) input.value = '';
            }
        }
    });
});
</script>
@endpush