@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Data Verifikasi Proses Rebox</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('rebox.index') }}">Rebox</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Form Input Data Rebox</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="form-horizontal" id="main-rebox-form" method="POST" action="{{ route('rebox.store') }}">
                                @csrf
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Data Utama</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Shift <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="shift_id" required>
                                                    <option value="">Pilih Shift</option>
                                                    @foreach($shifts as $shift)
                                                        <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Tanggal <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="date" class="form-control" name="tanggal_rebox" id="tanggal_rebox" 
                                                    value="{{ old('tanggal_rebox', \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d')) }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Jam <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="time" class="form-control @error('jam') is-invalid @enderror" name="jam" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                                @error('jam')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="form-container-rebox">
                                    <div class="rebox-form form-entry" id="form-1">
                                        <div class="card card-outline card-info">
                                            <div class="card-header">
                                                <h3 class="card-title">Detail Produk</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool remove-rebox-form" style="display: none;">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <!-- <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">Nama Produk <span class="text-danger">*</span></label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" name="nama_produk[]" required>
                                                    </div>
                                                </div> -->
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">Nama Produk <span class="text-danger">*</span></label>
                                                    <div class="col-sm-9">
                                                    <select class="form-control" name="nama_produk[]" id="id_produk_select" required>
                                                        <option value="">Pilih Produk</option>
                                                        @foreach($jenis_produk as $produk)
                                                            <option value="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</option>
                                                        @endforeach
                                                    </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">Kode Produksi <span class="text-danger">*</span></label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" name="kode_produksi[]" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">Best Before <span class="text-danger">*</span></label>
                                                    <div class="col-sm-9">
                                                        <input type="date" class="form-control" name="best_before[]" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">Kesesuaian Isi & Jumlah <span class="text-danger">*</span></label>
                                                    <div class="col-sm-9">
                                                        <select class="form-control" name="isi_jumlah[]">
                                                            <option value="">Pilih Status Isi & Jumlah</option>
                                                            <option value="✔">✔</option>
                                                            <option value="✘">✘</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">Kesesuaian Labelisasi <span class="text-danger">*</span></label>
                                                    <div class="col-sm-9">
                                                        <select class="form-control" name="labelisasi[]">
                                                            <option value="">Pilih Status Labelisasi</option>
                                                            <option value="✔">✔</option>
                                                            <option value="✘">✘</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="float-left">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Simpan Data
                                            </button>
                                            <button type="button" class="btn btn-success ml-2" id="addNewReboxFormBtn">
                                                <i class="fas fa-plus"></i> Tambah Form Baru
                                            </button>
                                            <a href="{{ route('rebox.index') }}" class="btn btn-secondary ml-2">
                                                <i class="fas fa-arrow-left"></i> Kembali
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@push('styles')
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let formCounter = 1;
    
    // Ambil data produk dari server untuk digunakan di form dinamis
    var produkOptions = `
    <option value="">Pilih Produk</option>
        @foreach($jenis_produk as $produk)
            <option value="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</option>
        @endforeach
    `;
    
    // Tombol tambah form baru
    $('#addNewReboxFormBtn').click(function() {
        formCounter++;
        
        // Template HTML untuk form baru
        var newFormHtml = `
        <div class="rebox-form form-entry" id="form-${formCounter}">
            <div class="card card-outline card-info mt-3">
                <div class="card-header">
                    <h3 class="card-title">Detail Produk #${formCounter}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-danger btn-sm delete-form">
                            <i class="fas fa-trash"></i> Hapus Form
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Nama Produk <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-control" name="nama_produk[]" id="id_produk_${formCounter}" required>
                                ${produkOptions}
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Kode Produksi <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" 
                                   id="kode_produksi_${formCounter}" name="kode_produksi[]" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Best Before <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" 
                                   id="best_before_${formCounter}" name="best_before[]" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Kesesuaian Isi & Jumlah <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-control" 
                                    id="isi_jumlah_${formCounter}" name="isi_jumlah[]" required>
                                <option value="">Pilih Status Isi & Jumlah</option>
                                <option value="✔">✔</option>
                                <option value="✘">✘</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;
        
        // Insert form baru setelah form terakhir
        $('.form-entry').last().after(newFormHtml);
        
        // Re-inisialisasi Select2 untuk dropdown yang baru ditambahkan
        $(`#id_produk_${formCounter}`).select2({
            placeholder: "Pilih Produk",
            allowClear: true,
        });

        // Tampilkan notifikasi
        if (typeof toastr !== 'undefined') {
            toastr.success('Form baru #' + formCounter + ' berhasil ditambahkan');
        }
        // Tampilkan notifikasi
        if (typeof toastr !== 'undefined') {
            toastr.success('Form baru #' + formCounter + ' berhasil ditambahkan');
        }
        
        // Scroll ke form baru
        $('html, body').animate({
            scrollTop: $(`#form-${formCounter}`).offset().top - 100
        }, 500);
    });
    
    // Handle delete form
    $(document).on('click', '.delete-form', function() {
        if (confirm('Apakah Anda yakin ingin menghapus form ini?')) {
            $(this).closest('.rebox-form').remove();
            if (typeof toastr !== 'undefined') {
                toastr.info('Form berhasil dihapus');
            }
        }
    });
});
</script>
@endpush

@endsection
