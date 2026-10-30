@extends('layouts.app')
@section('title', 'Tambah Bahan Baku Rice Bites')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-plus-circle mr-2 text-primary"></i>Tambah Bahan Baku Rice Bites</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('bahan-baku-rice-bites.index') }}">Bahan Baku Rice Bites</a>
                        </li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 col-lg-12 mx-auto">

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <h6><i class="fas fa-exclamation-triangle mr-1"></i> Terdapat kesalahan:</h6>
                            <ul class="mb-0 pl-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('bahan-baku-rice-bites.store') }}" method="POST">
                        @csrf

                        {{-- Card: Info Produk --}}
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-box mr-2"></i>Informasi Produk
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Plan --}}
                                    @if(auth()->user()->role === 'superadmin')
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_plan">
                                                Nama Plan <span class="text-danger">*</span>
                                            </label>
                                            <select name="id_plan" id="id_plan"
                                                    class="form-control @error('id_plan') is-invalid @enderror" required>
                                                <option value="">-- Pilih Plan --</option>
                                                @foreach($plans as $plan)
                                                    <option value="{{ $plan->id }}"
                                                        {{ old('id_plan') == $plan->id ? 'selected' : '' }}>
                                                        {{ $plan->nama_plan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_plan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @else
                                        <input type="hidden" name="id_plan" value="{{ auth()->user()->id_plan }}">
                                    @endif

                                    {{-- Produk --}}
                                    <div class="col-md-{{ auth()->user()->role === 'superadmin' ? '6' : '12' }}">
                                        <div class="form-group">
                                            <label for="id_produk">
                                                Nama Produk <span class="text-danger">*</span>
                                            </label>
                                            <select name="id_produk" id="id_produk"
                                                    class="form-control @error('id_produk') is-invalid @enderror" required>
                                                <option value="">-- Pilih Produk --</option>
                                                @foreach($produks as $produk)
                                                    <option value="{{ $produk->id }}"
                                                        data-plan="{{ $produk->id_plan }}"
                                                        {{ old('id_produk') == $produk->id ? 'selected' : '' }}>
                                                        {{ $produk->nama_produk }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_produk')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Nomor Formula --}}
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="nomor_formula">
                                                Nomor Formula
                                            </label>
                                            <input type="text" name="nomor_formula" id="nomor_formula"
                                                   class="form-control @error('nomor_formula') is-invalid @enderror"
                                                   value="{{ old('nomor_formula') }}"
                                                   placeholder="Masukkan nomor formula">
                                            @error('nomor_formula')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card: Daftar Bahan Baku --}}
                        <div class="card card-primary card-outline">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">
                                    <i class="fas fa-seedling mr-2"></i>Daftar Bahan Baku
                                </h3>
                                <button type="button" class="btn btn-primary btn-sm" id="btn-add-bahan">
                                    <i class="fas fa-plus mr-1"></i> Tambah Baris
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info py-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <small>Anda bisa menambah beberapa bahan baku sekaligus. Klik <strong>"Tambah Baris"</strong> untuk menambah bahan baku baru.</small>
                                </div>

                                {{-- Header kolom --}}
                                <div class="row font-weight-bold mb-1 d-none d-md-flex">
                                    <div class="col-md-7 pl-3">Nama Bahan Baku <span class="text-danger">*</span></div>
                                    <div class="col-md-4">Berat Standar (kg)</div>
                                    <div class="col-md-1"></div>
                                </div>

                                <div id="bahan-container">
                                    {{-- Baris pertama --}}
                                    <div class="row bahan-row mb-2 align-items-center" data-index="0">
                                        <div class="col-md-7 mb-1">
                                            <input type="text" name="nama_bahan_baku[0]"
                                                   class="form-control @error('nama_bahan_baku.0') is-invalid @enderror"
                                                   value="{{ old('nama_bahan_baku.0') }}"
                                                   placeholder="Masukkan nama bahan baku" required>
                                            @error('nama_bahan_baku.0')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-1">
                                            <div class="input-group">
                                                <input type="text" name="berat[0]"
                                                       class="form-control"
                                                       value="{{ old('berat.0') }}"
                                                       placeholder="0">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">kg</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1 mb-1 text-center">
                                            <button type="button" class="btn btn-danger btn-sm btn-remove-bahan"
                                                    style="display:none;" title="Hapus baris">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-end">
                                <a href="{{ route('bahan-baku-rice-bites.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary ml-2">
                                    <i class="fas fa-save mr-1"></i> Simpan Data
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
$(document).ready(function () {
    let rowIndex = 1;

    // ====== Filter produk berdasarkan plan (superadmin) ======
    @if(auth()->user()->role === 'superadmin')
    const allProduks = @json($produks);

    $('#id_plan').on('change', function () {
        const planId = $(this).val();
        const $produkSelect = $('#id_produk');
        $produkSelect.html('<option value="">-- Pilih Produk --</option>');

        if (planId) {
            allProduks
                .filter(p => String(p.id_plan) === String(planId))
                .forEach(p => {
                    $produkSelect.append(`<option value="${p.id}">${p.nama_produk}</option>`);
                });
        }
    });
    @endif

    // ====== Tambah baris bahan ======
    $('#btn-add-bahan').on('click', function () {
        const newRow = `
        <div class="row bahan-row mb-2 align-items-center" data-index="${rowIndex}">
            <div class="col-md-7 mb-1">
                <input type="text" name="nama_bahan_baku[${rowIndex}]"
                       class="form-control"
                       placeholder="Masukkan nama bahan baku" required>
            </div>
            <div class="col-md-4 mb-1">
                <div class="input-group">
                    <input type="text" name="berat[${rowIndex}]"
                           class="form-control" placeholder="0">
                    <div class="input-group-append">
                        <span class="input-group-text">kg</span>
                    </div>
                </div>
            </div>
            <div class="col-md-1 mb-1 text-center">
                <button type="button" class="btn btn-danger btn-sm btn-remove-bahan" title="Hapus baris">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>`;
        $('#bahan-container').append(newRow);
        rowIndex++;
        updateRemoveButtons();
    });

    // ====== Hapus baris ======
    $(document).on('click', '.btn-remove-bahan', function () {
        $(this).closest('.bahan-row').remove();
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        const rows = $('#bahan-container .bahan-row');
        rows.each(function (i) {
            const btn = $(this).find('.btn-remove-bahan');
            btn.toggle(rows.length > 1);
        });
    }
});
</script>
@endpush
@endsection
