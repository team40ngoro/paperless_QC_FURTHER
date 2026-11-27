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
                                        @if(isset($produks) && count($produks))
                                            @foreach($produks as $produk)
                                                <option value="{{ $produk->id }}" data-plan="{{ $produk->id_plan }}">{{ $produk->nama_produk }}</option>
                                            @endforeach
                                        @endif
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

    // Dynamic Produk by Plan
    const planSelect = document.getElementById('id_plan_select');
    const produkSelect = document.getElementById('id_produk_select');

    function loadProdukByPlan(planId) {
        if (!produkSelect) return;
        produkSelect.innerHTML = '<option value="">Memuat produk...</option>';
        if (!planId) {
            produkSelect.innerHTML = '<option value="">Pilih Produk</option>';
            return;
        }

        fetch("{{ url('/qc-sistem/ajax/produk-by-plan') }}/" + encodeURIComponent(planId), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            produkSelect.innerHTML = '<option value="">Pilih Produk</option>';
            (data || []).forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.nama_produk;
                produkSelect.appendChild(opt);
            });
            if (window.jQuery) {
                window.jQuery(produkSelect).trigger('change.select2');
            }
        })
        .catch(err => {
            console.error('Error loading produk by plan:', err);
            produkSelect.innerHTML = '<option value="">Gagal memuat produk</option>';
        });
    }

    if (planSelect) {
        if (window.jQuery) {
            window.jQuery(planSelect).on('change', function() {
                loadProdukByPlan(this.value);
            });
        } else {
            planSelect.addEventListener('change', function() {
                loadProdukByPlan(this.value);
            });
        }
        if (planSelect.value) {
            loadProdukByPlan(planSelect.value);
        }
    }
});
</script>
@endsection