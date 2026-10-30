{{-- filepath: d:\laragon\www\paperless_futher\resources\views\qc-sistem\rebox\edit.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Data Verifikasi Proses Rebox</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('rebox.index') }}">Rebox</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
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
                            <h3 class="card-title">Form Edit Data Rebox</h3>
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

                            <form action="{{ route('rebox.update', $rebox->uuid) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Data Utama</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Nama Produk <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" name="nama_produk" class="form-control" 
                                                       value="{{ old('nama_produk', $rebox->nama_produk) }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Kode Produksi <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" name="kode_produksi" class="form-control" 
                                                       value="{{ old('kode_produksi', $rebox->kode_produksi) }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Shift <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="shift_id" required>
                                                    <option value="">Pilih Shift</option>
                                                    @foreach($shifts as $shift)
                                                        <option value="{{ $shift->id }}" {{ old('shift_id', $rebox->shift_id) == $shift->id ? 'selected' : '' }}>
                                                            {{ $shift->shift }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        {{-- dev_bagus_perbaikan_29_mei:remove disable form + jam--}}
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">
                                                Tanggal Produksi <span class="text-danger">*</span>
                                            </label>

                                            <div class="col-sm-9">
                                                <input type="date"
                                                    id="tanggal_rebox_display"
                                                    class="form-control"
                                                    value="{{ old('tanggal_rebox', $rebox->tanggal ? \Carbon\Carbon::parse($rebox->tanggal)->format('Y-m-d') : '') }}"
                                                    onchange="
                                                        const t = document.getElementById('tanggal_rebox').value.split(' ')[1];
                                                        const [y,m,d] = this.value.split('-');
                                                        document.getElementById('tanggal_rebox').value = `${d}-${m}-${y} ${t}`;
                                                    "
                                                    required>

                                                <input type="hidden"
                                                    id="tanggal_rebox"
                                                    name="tanggal_rebox"
                                                    value="{{ old('tanggal_rebox', $rebox->tanggal ? \Carbon\Carbon::parse($rebox->tanggal)->format('d-m-Y H:i:s') : '') }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">
                                                Jam <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-9">
                                                <input type="time"
                                                    name="jam"
                                                    class="form-control"
                                                    value="{{ old('jam', $rebox->jam ? \Carbon\Carbon::parse($rebox->jam)->format('H:i') : '') }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Best Before <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="date" name="best_before" class="form-control"
                                                    value="{{ old('best_before', $rebox->best_before ? \Carbon\Carbon::parse($rebox->best_before)->format('Y-m-d') : '') }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Kesesuaian Isi & Jumlah <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control" name="isi_jumlah" required>
                                                    <option value="">Pilih Status</option>
                                                    <option value="✔" {{ old('isi_jumlah', $rebox->isi_jumlah) == '✔' ? 'selected' : '' }}>✔</option>
                                                    <option value="✘" {{ old('isi_jumlah', $rebox->isi_jumlah) == '✘' ? 'selected' : '' }}>✘</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Kesesuaian Labelisasi <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control" name="labelisasi" required>
                                                    <option value="">Pilih Status</option>
                                                    <option value="✔" {{ old('labelisasi', $rebox->labelisasi) == '✔' ? 'selected' : '' }}>✔</option>
                                                    <option value="✘" {{ old('labelisasi', $rebox->labelisasi) == '✘' ? 'selected' : '' }}>✘</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="float-left">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-save"></i> Update Data
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
@endsection