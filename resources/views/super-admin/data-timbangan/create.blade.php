{{-- filepath: resources/views/super-admin/data-timbangan/create.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Data Timbangan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('data-timbangan.index') }}">Data Timbangan</a></li>
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
                            <h3 class="card-title">Form Tambah Data Timbangan</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-success btn-sm" id="addFormBtn">
                                    <i class="fas fa-plus"></i> Tambah Form
                                </button>
                            </div>
                        </div>
                        <form action="{{ route('data-timbangan.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                
                                <div class="form-group">
                                    <label for="id_plan">Plan <span class="text-danger">*</span></label>
                                    <select name="id_plan" id="id_plan" class="form-control @error('id_plan') is-invalid @enderror" required>
                                        <option value="">Pilih Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ old('id_plan') == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->nama_plan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>
                                <h5 class="mb-3"><i class="fas fa-list"></i> Daftar Data Timbangan</h5>

                                <!-- Container untuk dynamic forms -->
                                <div id="formContainer">
                                    <!-- Form pertama (default) -->
                                    <div class="card card-outline card-info mb-3" data-form-index="1">
                                        <div class="card-header">
                                            <h3 class="card-title">Form #1</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="nama_timbangan_1">Nama Timbangan <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_timbangan[]" id="nama_timbangan_1" 
                                                       class="form-control @error('nama_timbangan.*') is-invalid @enderror" 
                                                       placeholder="Masukkan Nama Timbangan" 
                                                       value="{{ old('nama_timbangan.0') }}" required>
                                                @error('nama_timbangan.*')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="kode_timbangan_1">Kode Timbangan <span class="text-danger">*</span></label>
                                                <input type="text" name="kode_timbangan[]" id="kode_timbangan_1" 
                                                       class="form-control @error('kode_timbangan.*') is-invalid @enderror" 
                                                       placeholder="Masukkan Kode Timbangan" 
                                                       value="{{ old('kode_timbangan.0') }}" required>
                                                @error('kode_timbangan.*')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Kode harus unik dan tidak boleh sama dengan yang lain</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Semua
                                </button>
                                <a href="{{ route('data-timbangan.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
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
$(document).ready(function() {
    let formCounter = 1;

    // Tambah form baru
    $('#addFormBtn').click(function() {
        formCounter++;
        
        const newForm = `
            <div class="card card-outline card-info mb-3" data-form-index="${formCounter}">
                <div class="card-header">
                    <h3 class="card-title">Form #${formCounter}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-danger btn-sm remove-form-btn">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="nama_timbangan_${formCounter}">Nama Timbangan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_timbangan[]" id="nama_timbangan_${formCounter}" 
                               class="form-control" 
                               placeholder="Masukkan Nama Timbangan" 
                               required>
                    </div>

                    <div class="form-group">
                        <label for="kode_timbangan_${formCounter}">Kode Timbangan <span class="text-danger">*</span></label>
                        <input type="text" name="kode_timbangan[]" id="kode_timbangan_${formCounter}" 
                               class="form-control" 
                               placeholder="Masukkan Kode Timbangan" 
                               required>
                        <small class="form-text text-muted">Kode harus unik dan tidak boleh sama dengan yang lain</small>
                    </div>
                </div>
            </div>
        `;
        
        $('#formContainer').append(newForm);
        
        // Tampilkan notifikasi
        if (typeof toastr !== 'undefined') {
            toastr.success('Form baru #' + formCounter + ' berhasil ditambahkan');
        }
    });

    // Hapus form (delegated event)
    $(document).on('click', '.remove-form-btn', function() {
        const formCard = $(this).closest('.card');
        const formIndex = formCard.data('form-index');
        
        if (confirm('Apakah Anda yakin ingin menghapus form #' + formIndex + '?')) {
            formCard.remove();
            
            // Update nomor form yang tersisa
            updateFormNumbers();
            
            if (typeof toastr !== 'undefined') {
                toastr.info('Form #' + formIndex + ' berhasil dihapus');
            }
        }
    });

    // Update nomor form setelah ada yang dihapus
    function updateFormNumbers() {
        $('#formContainer .card').each(function(index) {
            const newNumber = index + 1;
            $(this).attr('data-form-index', newNumber);
            $(this).find('.card-title').text('Form #' + newNumber);
        });
        formCounter = $('#formContainer .card').length;
    }
});
</script>
@endpush
@endsection