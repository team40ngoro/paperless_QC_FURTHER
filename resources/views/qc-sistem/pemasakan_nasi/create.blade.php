@extends('layouts.app')

@section('title', 'Tambah Data Pemasakan Nasi')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Data Pemasakan Nasi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemasakan-nasi.index') }}">Data Pemasakan Nasi</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Form Tambah Data Pemasakan Nasi</h3>
                        </div>
                        <form action="{{ route('pemasakan-nasi.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                
                                <!-- SECTION 1: Informasi Umum -->
                                <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-info-circle mr-1"></i> Informasi Umum</h5>
                                <div class="row">
                                    <!-- Nama Produk -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="id_produk">Nama Produk <span class="text-danger">*</span></label>
                                            <select class="form-control @error('id_produk') is-invalid @enderror" 
                                                    id="id_produk" name="id_produk" required>
                                                <option value="">Pilih Nama Produk</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" {{ old('id_produk') == $product->id ? 'selected' : '' }}>
                                                        {{ $product->nama_produk }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_produk')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Shift -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="shift_id">Shift <span class="text-danger">*</span></label>
                                            <select class="form-control @error('shift_id') is-invalid @enderror" 
                                                    id="shift_id" name="shift_id" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('shift_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Kode Produksi -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="kode_produksi">Kode Produksi <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('kode_produksi') is-invalid @enderror" 
                                                   id="kode_produksi" name="kode_produksi" value="{{ old('kode_produksi') }}" required>
                                            @error('kode_produksi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Tanggal -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                            
                                            <input type="date" name="tanggal" id="tanggal" 
                                                class="form-control @error('tanggal') is-invalid @enderror" 
                                                value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                            @error('tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Jam -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jam">Jam <span class="text-danger">*</span></label>
                                            <input type="time" name="jam" id="jam" 
                                                class="form-control @error('jam') is-invalid @enderror" 
                                                value="{{ old('jam', date('H:i')) }}" required>
                                            @error('jam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- SECTION 2: Detail Proses Pemasakan -->
                                <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-fire mr-1"></i> Detail Proses Pemasakan</h5>
                                <div class="row">
                                    <!-- Proses -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="proses">Proses <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('proses') is-invalid @enderror" 
                                                   id="proses" name="proses" value="{{ old('proses') }}" required>
                                            @error('proses')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Waktu -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="waktu">Waktu <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('waktu') is-invalid @enderror" 
                                                   id="waktu" name="waktu" value="{{ old('waktu') }}" required>
                                            @error('waktu')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Lama Proses -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="lama_proses">Lama Proses <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control @error('lama_proses') is-invalid @enderror" 
                                                       id="lama_proses" name="lama_proses" value="{{ old('lama_proses') }}" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">menit</span>
                                                </div>
                                            </div>
                                            @error('lama_proses')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Waktu Start -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="waktu_start">Waktu Start <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control @error('waktu_start') is-invalid @enderror" 
                                                   id="waktu_start" name="waktu_start" value="{{ old('waktu_start') }}" 
                                                   required>
                                            @error('waktu_start')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Waktu Stop -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="waktu_stop">Waktu Stop <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control @error('waktu_stop') is-invalid @enderror" 
                                                   id="waktu_stop" name="waktu_stop" value="{{ old('waktu_stop') }}" 
                                                   required>
                                            @error('waktu_stop')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Status Cooking -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status_cooking">Status Cooking <span class="text-danger">*</span></label>
                                            <div class="custom-control custom-switch mt-1">
                                                <input type="checkbox" class="custom-control-input" id="status_cooking" 
                                                       name="status_cooking" value="1" {{ old('status_cooking') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="status_cooking" id="status_cooking_label">
                                                    {{ old('status_cooking') ? 'Aktif' : 'Tidak Aktif' }}
                                                </label>
                                            </div>
                                            @error('status_cooking')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- SECTION 3: Jenis Bahan & Jumlah -->
                                <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-cubes mr-1"></i> Jenis Bahan & Jumlah</h5>
                                <div class="row">
                                    <div class="col-12">
                                        <div id="bahan-container">
                                            <div class="row bahan-row mb-2">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>Jenis Bahan <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('jenis_bahan.0') is-invalid @enderror" 
                                                               name="jenis_bahan[]" value="{{ old('jenis_bahan.0') }}" required>
                                                        @error('jenis_bahan.0')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>Jumlah <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control @error('jumlah.0') is-invalid @enderror" 
                                                                   name="jumlah[]" value="{{ old('jumlah.0') }}" required>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">kg</span>
                                                            </div>
                                                        </div>
                                                        @error('jumlah.0')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label>
                                                        <div>
                                                            <button type="button" class="btn btn-success btn-sm add-bahan">
                                                                <i class="fas fa-plus"></i> Tambah
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- SECTION 4: Parameter Suhu & Kondisi -->
                                <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-thermometer-half mr-1"></i> Parameter Suhu & Kondisi</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="temp_std_1">Temp Std 1 <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('temp_std_1') is-invalid @enderror" 
                                                   id="temp_std_1" name="temp_std_1" value="{{ old('temp_std_1') }}" required>
                                            @error('temp_std_1')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="temp_std_2">Temp Std 2 <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('temp_std_2') is-invalid @enderror" 
                                                   id="temp_std_2" name="temp_std_2" value="{{ old('temp_std_2') }}" required>
                                            @error('temp_std_2')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="temp_std_3">Temp Std 3 <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('temp_std_3') is-invalid @enderror" 
                                                   id="temp_std_3" name="temp_std_3" value="{{ old('temp_std_3') }}" required>
                                            @error('temp_std_3')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sensori_kondisi">Sensori Kondisi <span class="text-danger">*</span></label>
                                            <select class="form-control @error('sensori_kondisi') is-invalid @enderror" 
                                                    id="sensori_kondisi" name="sensori_kondisi" required>
                                                <option value="">Pilih Kondisi</option>
                                                <option value="OK" {{ old('sensori_kondisi') == 'OK' ? 'selected' : '' }}>OK</option>
                                                <option value="Tidak OK" {{ old('sensori_kondisi') == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                            </select>
                                            @error('sensori_kondisi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- SECTION 5: Uji Organoleptik -->
                                <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-eye mr-1"></i> Uji Organoleptik</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="organo_warna">Organo Warna <span class="text-danger">*</span></label>
                                            <select class="form-control @error('organo_warna') is-invalid @enderror" 
                                                    id="organo_warna" name="organo_warna" required>
                                                <option value="">Pilih</option>
                                                <option value="OK" {{ old('organo_warna') == 'OK' ? 'selected' : '' }}>OK</option>
                                                <option value="Tidak OK" {{ old('organo_warna') == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                            </select>
                                            @error('organo_warna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="organo_aroma">Organo Aroma <span class="text-danger">*</span></label>
                                            <select class="form-control @error('organo_aroma') is-invalid @enderror" 
                                                    id="organo_aroma" name="organo_aroma" required>
                                                <option value="">Pilih</option>
                                                <option value="OK" {{ old('organo_aroma') == 'OK' ? 'selected' : '' }}>OK</option>
                                                <option value="Tidak OK" {{ old('organo_aroma') == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                            </select>
                                            @error('organo_aroma')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="organo_rasa">Organo Rasa <span class="text-danger">*</span></label>
                                            <select class="form-control @error('organo_rasa') is-invalid @enderror" 
                                                    id="organo_rasa" name="organo_rasa" required>
                                                <option value="">Pilih</option>
                                                <option value="OK" {{ old('organo_rasa') == 'OK' ? 'selected' : '' }}>OK</option>
                                                <option value="Tidak OK" {{ old('organo_rasa') == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                            </select>
                                            @error('organo_rasa')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="organo_tekstur">Organo Tekstur <span class="text-danger">*</span></label>
                                            <select class="form-control @error('organo_tekstur') is-invalid @enderror" 
                                                    id="organo_tekstur" name="organo_tekstur" required>
                                                <option value="">Pilih</option>
                                                <option value="OK" {{ old('organo_tekstur') == 'OK' ? 'selected' : '' }}>OK</option>
                                                <option value="Tidak OK" {{ old('organo_tekstur') == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                            </select>
                                            @error('organo_tekstur')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- SECTION 6: Keterangan Tambahan -->
                                <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-sticky-note mr-1"></i> Keterangan Tambahan</h5>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="catatan">Catatan</label>
                                            <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                                      id="catatan" name="catatan" rows="3" placeholder="Masukkan catatan opsional di sini...">{{ old('catatan') }}</textarea>
                                            @error('catatan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <a href="{{ route('pemasakan-nasi.index') }}" class="btn btn-secondary">
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
@endsection
