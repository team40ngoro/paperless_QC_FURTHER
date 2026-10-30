{{-- filepath: resources/views/super-admin/bahan_forming/edit.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Bahan Forming</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bahan-forming.index') }}">Data Bahan Forming</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Bahan Forming</h3>
                        </div>
                        <form action="{{ route('bahan-forming.update', $bahan->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="id_plan">Nama Plan</label>
                                    <select name="id_plan" id="id_plan" class="form-control" required>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ $bahan->id_plan == $plan->id ? 'selected' : '' }}>{{ $plan->nama_plan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="id_produk">Nama Produk</label>
                                    <select name="id_produk" id="id_produk" class="form-control" required>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ $bahan->id_produk == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="id_formula">Nomor Formula</label>
                                    <select name="id_formula" id="id_formula" class="form-control" required>
                                        @foreach($formulas as $formula)
                                            <option value="{{ $formula->id }}" {{ $bahan->id_formula == $formula->id ? 'selected' : '' }}>{{ $formula->nomor_formula }}</option>
                                        @endforeach
                                    </select>
                                    <small id="formula-warning" class="text-warning d-none">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Formula berubah — pastikan Nama RM dan Berat RM sudah diperbarui secara manual.
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="nama_rm">Nama RM</label>
                                    <input type="text" name="nama_rm" id="nama_rm" class="form-control" value="{{ old('nama_rm', $bahan->nama_rm) }}" placeholder="Masukkan Nama RM" required>
                                </div>
                                <div class="form-group">
                                    <label for="berat_rm">Berat RM</label>
                                    <input type="text" name="berat_rm" id="berat_rm" class="form-control" value="{{ old('berat_rm', $bahan->berat_rm) }}" placeholder="Masukkan Berat RM" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
                                <a href="{{ route('bahan-forming.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    const originalFormulaId = "{{ $bahan->id_formula }}";

    document.getElementById('id_formula').addEventListener('change', function () {
        const warning = document.getElementById('formula-warning');
        if (this.value !== originalFormulaId) {
            warning.classList.remove('d-none');
        } else {
            warning.classList.add('d-none');
        }
    });
</script>
@endpush

@endsection
