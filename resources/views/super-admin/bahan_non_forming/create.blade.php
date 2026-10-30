@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Bahan Non Forming</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bahan-non-forming.index') }}">Data Bahan Non Forming</a></li>
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
                            <h3 class="card-title">Form Tambah Bahan Non Forming</h3>
                        </div>
                           @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        <form action="{{ route('bahan-non-forming.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group mb-2">
                                    <label for="id_plan_non_forming_select">Nama Plan</label>
                                    <select name="id_plan" id="id_plan_non_forming_select" class="form-control" required>
                                        <option value="">Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}">{{ $plan->nama_plan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Nama Produk</label>
                                   <select name="id_produk" id="id_produk_non_forming_select" class="form-control" required>
                                    <option value="">-- Pilih Plan Terlebih Dahulu --</option>
                                </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Nomor Formula</label>
                                <input type="text" name="nomor_formula" id="nomor_formula_non_forming" class="form-control" placeholder="Masukkan Nomor Formula" required>
                                </div>
                                
                                <!-- Dynamic Form untuk Data RM -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Data Raw Material (RM)</h5>
                                        <button type="button" class="btn btn-success btn-sm float-right" id="addRM">
                                            <i class="fas fa-plus"></i> Tambah RM
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="rm-container">
                                            <div class="rm-item mb-3" data-index="0">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <label>Nama RM</label>
                                                        <input type="text" name="nama_rm[]" class="form-control" placeholder="Masukkan Nama RM" required>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label>Berat RM (kg)</label>
                                                        <input type="text" name="berat_rm[]" class="form-control" placeholder="000" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>&nbsp;</label>
                                                        <button type="button" class="btn btn-danger btn-block remove-rm" disabled>
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
                                <a href="{{ route('bahan-non-forming.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
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
document.addEventListener('DOMContentLoaded', function() {
    let rmIndex = 1;
  // AJAX untuk load produk berdasarkan plan
document.getElementById('id_plan_non_forming_select').addEventListener('change', function() {
    const planId = this.value;
   const produkSelect = document.getElementById('id_produk_non_forming_select');
    
    // Reset select produk dengan loading state
    produkSelect.innerHTML = '<option value="">-- Loading Produk --</option>';
    produkSelect.disabled = true;
    
    if (planId) {
        fetch(`{{ url('/super-admin/ajax/produk-non-forming-by-plan') }}/${planId}`)
            .then(response => response.json())
            .then(data => {
                produkSelect.innerHTML = '<option value="">-- Pilih Produk --</option>';
                
                if (data.length > 0) {
                    data.forEach(produk => {
                        const option = document.createElement('option');
                        option.value = produk.id;
                        option.textContent = produk.nama_produk;
                        produkSelect.appendChild(option);
                    });
                } else {
                    produkSelect.innerHTML = '<option value="">-- Tidak Ada Produk --</option>';
                }
                
                produkSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                produkSelect.innerHTML = '<option value="">-- Error Loading Produk --</option>';
                produkSelect.disabled = false;
            });
    } else {
        produkSelect.innerHTML = '<option value="">-- Pilih Plan Terlebih Dahulu --</option>';
        produkSelect.disabled = false;
    }
});
    
    // Tambah RM baru
    document.getElementById('addRM').addEventListener('click', function() {
        const container = document.getElementById('rm-container');
        const newRM = `
            <div class="rm-item mb-3" data-index="${rmIndex}">
                <div class="row">
                    <div class="col-md-5">
                        <label>Nama RM</label>
                        <input type="text" name="nama_rm[]" class="form-control" placeholder="Masukkan Nama RM" required>
                    </div>
                    <div class="col-md-5">
                        <label>Berat RM (kg)</label>
                        <input type="text" name="berat_rm[]" class="form-control" placeholder="000" required>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block remove-rm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newRM);
        rmIndex++;
        updateRemoveButtons();
    });
    
    // Hapus RM
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-rm') || e.target.closest('.remove-rm')) {
            const rmItem = e.target.closest('.rm-item');
            rmItem.remove();
            updateRemoveButtons();
        }
    });
    
    // Update status tombol hapus
    function updateRemoveButtons() {
        const rmItems = document.querySelectorAll('.rm-item');
        const removeButtons = document.querySelectorAll('.remove-rm');
        
        removeButtons.forEach(button => {
            button.disabled = rmItems.length <= 1;
        });
    }
});
</script>
@endpush

@endsection