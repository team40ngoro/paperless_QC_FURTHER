@extends('layouts.app')
@section('title', 'Edit Bahan Baku Rice Bites')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-edit mr-2 text-warning"></i>Edit Bahan Baku Rice Bites</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('bahan-baku-rice-bites.index') }}">Bahan Baku Rice Bites</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
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

                    <form action="{{ route('bahan-baku-rice-bites.update', $bahan->uuid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-seedling mr-2"></i>Form Edit Bahan Baku Rice Bites
                                </h3>
                            </div>
                            <div class="card-body">

                                {{-- Plan --}}
                                @if(auth()->user()->role === 'superadmin')
                                <div class="form-group">
                                    <label for="id_plan">
                                        Nama Plan <span class="text-danger">*</span>
                                    </label>
                                    <select name="id_plan" id="id_plan"
                                            class="form-control @error('id_plan') is-invalid @enderror" required>
                                        <option value="">-- Pilih Plan --</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}"
                                                {{ (old('id_plan', $bahan->id_plan) == $plan->id) ? 'selected' : '' }}>
                                                {{ $plan->nama_plan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @else
                                    <input type="hidden" name="id_plan" value="{{ $bahan->id_plan }}">
                                    <div class="form-group">
                                        <label>Plan</label>
                                        <input type="text" class="form-control" value="{{ $bahan->plan->nama_plan ?? '-' }}" readonly>
                                    </div>
                                @endif

                                {{-- Produk --}}
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
                                                {{ (old('id_produk', $bahan->id_produk) == $produk->id) ? 'selected' : '' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_produk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Nomor Formula --}}
                                <div class="form-group">
                                    <label for="nomor_formula">
                                        Nomor Formula
                                    </label>
                                    <input type="text" name="nomor_formula" id="nomor_formula"
                                           class="form-control @error('nomor_formula') is-invalid @enderror"
                                           value="{{ old('nomor_formula', $bahan->nomor_formula) }}"
                                           placeholder="Masukkan nomor formula" readonly>
                                    @error('nomor_formula')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Nama Bahan Baku --}}
                                <div class="form-group">
                                    <label for="nama_bahan_baku">
                                        Nama Bahan Baku <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="nama_bahan_baku" id="nama_bahan_baku"
                                           class="form-control @error('nama_bahan_baku') is-invalid @enderror"
                                           value="{{ old('nama_bahan_baku', $bahan->nama_bahan_baku) }}"
                                           placeholder="Masukkan nama bahan baku" required>
                                    @error('nama_bahan_baku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Berat --}}
                                <div class="form-group">
                                    <label for="berat">Berat Standar (kg)</label>
                                    <div class="input-group">
                                        <input type="text" name="berat" id="berat"
                                               class="form-control @error('berat') is-invalid @enderror"
                                               value="{{ old('berat', $bahan->berat) }}"
                                               placeholder="0">
                                        <div class="input-group-append">
                                            <span class="input-group-text">kg</span>
                                        </div>
                                    </div>
                                    @error('berat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Opsional — berat standar akan diisi otomatis di form pemeriksaan.</small>
                                </div>

                                {{-- Info --}}
                                <div class="alert alert-info py-2 mb-0">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <small>
                                        Data ini digunakan sebagai <strong>master data otomatis</strong> pada form Pemeriksaan Rice Bites
                                        ketika produk <strong>{{ $bahan->produk->nama_produk ?? '-' }}</strong> dipilih.
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-end">
                                <a href="{{ route('bahan-baku-rice-bites.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left ml-2"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-warning ml-2">
                                    <i class="fas fa-save ml-2"></i> Update Data
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
                    const selected = String(p.id) === '{{ $bahan->id_produk }}' ? 'selected' : '';
                    $produkSelect.append(`<option value="${p.id}" ${selected}>${p.nama_produk}</option>`);
                });
        }
    });
    @endif
});
</script>
@endpush
@endsection
