{{-- filepath: resources/views/super-admin/nomor_emulsi/create.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Nomor Emulsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('nomor-emulsi.index') }}">Nomor Emulsi</a></li>
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
                            <h3 class="card-title">Tambah Data Nomor Emulsi</h3>
                        </div>
                        <form action="{{ route('nomor-emulsi.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <!-- Error Alert -->
                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <h5><i class="fas fa-exclamation-triangle"></i> Terdapat Kesalahan!</h5>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
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
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Nama Emulsi</label>
                                    <select name="nama_emulsi_id" id="nama_emulsi_id" class="form-control" required>
                                        <option value="">Pilih Emulsi</option>
                                        @foreach($emulsis as $emulsi)
                                            <option value="{{ $emulsi->id }}">{{ $emulsi->nama_emulsi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Total Pemakaian</label>
                                    <select name="total_pemakaian_id" id="total_pemakaian_id" class="form-control" required>
                                        <option value="">Pilih Total Pemakaian</option>
                                        {{-- Akan diisi otomatis --}}
                                        </select>
                                    </div>
                                    <!-- Dynamic Form untuk Nomor Proses Emulsi -->
                                    <div class="card mt-3">
                                        <div class="card-header">
                                            <h5 class="card-title">Data Nomor Proses Emulsi</h5>
                                            <button type="button" class="btn btn-success btn-sm float-right" id="addEmulsi">
                                                <i class="fas fa-plus"></i> Tambah Nomor Emulsi
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div id="emulsi-container">
                                                <div class="emulsi-item mb-3" data-index="0">
                                                    <div class="row">
                                                        <div class="col-md-10">
                                                            <label>Nomor Proses Emulsi</label>
                                                            <input type="text" name="nomor_emulsi[]" class="form-control" placeholder="Masukkan Nomor Proses Emulsi" required>
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
                                    <a href="{{ route('nomor-emulsi.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                                </div>
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
                        <label>Nomor Proses Emulsi</label>
                        <input type="text" name="nomor_emulsi[]" class="form-control" placeholder="Masukkan Nomor Proses Emulsi" required>
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
                