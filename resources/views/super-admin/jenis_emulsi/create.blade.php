{{-- filepath: c:\xampp\htdocs\paperless_futher\resources\views\super-admin\jenis_emulsi\create.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Jenis Emulsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('jenis-emulsi.index') }}">Jenis Emulsi</a></li>
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
                            <h3 class="card-title">Tambah Data Jenis Emulsi</h3>
                        </div>
                        <form action="{{ route('jenis-emulsi.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="id_plan_select">Nama Plan</label>
                                    <select name="id_plan" id="id_plan_select" class="form-control @error('id_plan') is-invalid @enderror" required>
                                        <option selected disabled>Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}">{{ $plan->nama_plan }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_plan')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="id_produk_select">Nama Produk</label>
                                    <select name="id_produk" id="id_produk_select" class="form-control @error('id_produk') is-invalid @enderror" required>
                                        <option selected disabled>Pilih Produk</option>
                                    </select>
                                    @error('id_produk')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <!-- Dynamic Form untuk Nama Emulsi -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Data Nama Emulsi</h5>
                                        <button type="button" class="btn btn-success btn-sm float-right" id="addEmulsi">
                                            <i class="fas fa-plus"></i> Tambah Emulsi
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="emulsi-container">
                                            <div class="emulsi-item mb-3" data-index="0">
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <label>Nama Emulsi</label>
                                                        <input type="text" name="nama_emulsi[]" class="form-control" placeholder="Masukkan nama emulsi" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>&nbsp;</label>
                                                        <button type="button" class="btn btn-danger btn-block remove-emulsi" disabled>
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
                                <a href="{{ route('jenis-emulsi.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
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
    let emulsiIndex = 1;
    
    // Tambah Emulsi baru
    document.getElementById('addEmulsi').addEventListener('click', function() {
        const container = document.getElementById('emulsi-container');
        const newEmulsi = `
            <div class="emulsi-item mb-3" data-index="${emulsiIndex}">
                <div class="row">
                    <div class="col-md-10">
                        <label>Nama Emulsi</label>
                        <input type="text" name="nama_emulsi[]" class="form-control" placeholder="Masukkan nama emulsi" required>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block remove-emulsi">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newEmulsi);
        emulsiIndex++;
        updateRemoveButtons();
    });
    
    // Hapus Emulsi
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-emulsi') || e.target.closest('.remove-emulsi')) {
            const emulsiItem = e.target.closest('.emulsi-item');
            emulsiItem.remove();
            updateRemoveButtons();
        }
    });
    
    // Update status tombol hapus
    function updateRemoveButtons() {
        const emulsiItems = document.querySelectorAll('.emulsi-item');
        const removeButtons = document.querySelectorAll('.remove-emulsi');
        
        removeButtons.forEach(button => {
            button.disabled = emulsiItems.length <= 1;
        });
    }
});
</script>

@endsection