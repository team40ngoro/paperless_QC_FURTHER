{{-- filepath: resources/views/super-admin/nomor_formula/create.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Nomor Formula</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('nomor-formula.index') }}">Data Nomor Formula</a></li>
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
                            <h3 class="card-title">Form Tambah Nomor Formula</h3>
                        </div>
                        <form action="{{ route('nomor-formula.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="id_plan_select">Nama Plan</label>
                                    <select name="id_plan" id="id_plan_select" class="form-control" required>
                                        <option value="">Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}">{{ $plan->nama_plan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="id_produk_select">Nama Produk</label>
                                    <select name="id_produk" id="id_produk_select" class="form-control" required>
                                        <option value="">Pilih Produk</option>
                                        {{-- Akan diisi AJAX --}}
                                    </select>
                                </div>
                                <!-- Dynamic Form untuk Nomor Formula -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Data Nomor Formula</h5>
                                        <button type="button" class="btn btn-success btn-sm float-right" id="addFormula">
                                            <i class="fas fa-plus"></i> Tambah Nomor Formula
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="formula-container">
                                            <div class="formula-item mb-3" data-index="0">
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <label>Nomor Formula</label>
                                                        <input type="text" name="nomor_formula[]" class="form-control" placeholder="Masukkan Nomor Formula" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>&nbsp;</label>
                                                        <button type="button" class="btn btn-danger btn-block remove-formula" disabled>
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                <a href="{{ route('nomor-formula.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let formulaIndex = 1;
    
    // Tambah Formula baru
    document.getElementById('addFormula').addEventListener('click', function() {
        const container = document.getElementById('formula-container');
        const newFormula = `
            <div class="formula-item mb-3" data-index="${formulaIndex}">
                <div class="row">
                    <div class="col-md-10">
                        <label>Nomor Formula</label>
                        <input type="text" name="nomor_formula[]" class="form-control" placeholder="Masukkan Nomor Formula" required>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block remove-formula">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newFormula);
        formulaIndex++;
        updateRemoveButtons();
    });
    
    // Hapus Formula
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-formula') || e.target.closest('.remove-formula')) {
            const formulaItem = e.target.closest('.formula-item');
            formulaItem.remove();
            updateRemoveButtons();
        }
    });
    
    // Update status tombol hapus
    function updateRemoveButtons() {
        const formulaItems = document.querySelectorAll('.formula-item');
        const removeButtons = document.querySelectorAll('.remove-formula');
        
        removeButtons.forEach(button => {
            button.disabled = formulaItems.length <= 1;
        });
    }
});
</script>
@endsection