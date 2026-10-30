@extends('layouts.app')

@section('title', 'Edit Pemeriksaan Produk Cooking Mixer FLA')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Pemeriksaan Produk Cooking Mixer FLA</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-produk-cooking-mixer-fla.index') }}">Data Pemeriksaan Produk Cooking Mixer FLA</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Pemeriksaan Produk Cooking Mixer FLA</h3>
                        </div>
                        
                        <form action="{{ route('pemeriksaan-produk-cooking-mixer-fla.update', $item->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Meta tags for JavaScript initialization -->
                            <meta name="current-product-id" content="{{ $item->namaFormulaFla->id_produk ?? '' }}">
                            <meta name="current-formula-id" content="{{ $item->id_nama_formula_fla ?? '' }}">
                            <meta name="current-step-id" content="{{ $item->id_stp_frm_fla ?? '' }}">
                            <meta name="current-bahan-id" content="{{ $item->id_frm_fla ?? '' }}">
                            <script>
                                window.existingKodeProduksiBahan = @json(old('kode_produksi_bahan', $item->getKodeProduksiBahanArray()));
                            </script>
                            
                            <div class="card-body">
                                <div class="row">
                                    <!-- Shift -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="shift_id">Shift <span class="text-danger">*</span></label>
                                            <select name="shift_id" id="shift_id" 
                                                    class="form-control @error('shift_id') is-invalid @enderror" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" 
                                                            {{ old('shift_id', $item->shift_id) == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('shift_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Tanggal -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                            <input type="date" name="tanggal" id="tanggal"
                                                   class="form-control @error('tanggal') is-invalid @enderror"
                                                   value="{{ old('tanggal', $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : '') }}" required>
                                            @error('tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="display: none;">
                                        <div class="form-group">
                                            <label for="jam">Jam</label>
                                            <input type="time" name="jam" id="jam" 
                                                   class="form-control" 
                                                   value="{{ old('jam', $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '') }}">
                                        </div>
                                    </div>

                                    <!-- Kode Produksi -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="kode_produksi">Kode Produksi <span class="text-danger">*</span></label>
                                            <input type="text" name="kode_produksi" id="kode_produksi" 
                                                   class="form-control @error('kode_produksi') is-invalid @enderror" 
                                                   value="{{ old('kode_produksi', $item->kode_produksi) }}" 
                                                   placeholder="Masukkan kode produksi" required>
                                            @error('kode_produksi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h5><i class="fas fa-layer-group text-primary"></i> Cascading Selection</h5>

                                <div class="row">
                                    <!-- Nama Produk -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="id_produk">Nama Produk <span class="text-danger">*</span></label>
                                            <select name="id_produk" id="id_produk" 
                                                    class="form-control @error('id_produk') is-invalid @enderror">
                                                <option value="">Pilih Nama Produk</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" 
                                                            {{ old('id_produk', $item->namaFormulaFla->id_produk ?? '') == $product->id ? 'selected' : '' }}>
                                                        {{ $product->nama_produk }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_produk')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-2">
                                            <label class="form-label">Berat Produk (gr) <span class="text-danger">*</span></label>
                                            <select id="edit_nilai_select_berat_for_pemeriksaan_produk_cooking_mixer_fla" class="form-control" name="berat">
                                               
                                            </select>
                                            @error('berat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Nama Formula FLA -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="id_nama_formula_fla">Nama Formula FLA <span class="text-danger">*</span></label>
                                            <select name="id_nama_formula_fla" id="id_nama_formula_fla" 
                                                    class="form-control @error('id_nama_formula_fla') is-invalid @enderror" required>
                                                <option value="">Pilih Nama Formula FLA</option>
                                            </select>
                                            @error('id_nama_formula_fla')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Hidden input for Step Formula -->
                                    <input type="hidden" name="id_stp_frm_fla" id="id_stp_frm_fla" value="{{ old('id_stp_frm_fla', $item->id_stp_frm_fla) }}">
                                </div>

                                <!-- Detail Table (Hidden initially) -->
                                <div id="detail-table-container" style="display: none;">
                                    <hr>
                                    <h5><i class="fas fa-table text-success"></i> Detail Formula</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Bahan Formula FLA</th>
                                                    <th>Berat Formula FLA</th>
                                                    <th>Kode Produksi Bahan</th>
                                                    <!-- <th>Step</th> -->
                                                    <th>Proses</th>
                                                    <th>Nama Formula</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detail-table-body">
                                                <!-- Data will be populated via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Hidden field for bahan formula fla ID -->
                                    <input type="hidden" name="id_frm_fla" id="id_frm_fla" value="{{ old('id_frm_fla', $item->id_frm_fla) }}">
                                </div>

                                <hr>
                                <h5><i class="fas fa-cogs text-warning"></i> Parameter Proses</h5>

                                <div class="row">
                                    <!-- Berat Produk -->
                               

                                    <!-- Waktu Start -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="waktu_start">Waktu Start <span class="text-danger">*</span></label>
                                            <input type="time" name="waktu_start" id="waktu_start" 
                                                   class="form-control @error('waktu_start') is-invalid @enderror" 
                                                   value="{{ old('waktu_start', $item->waktu_start) }}" required>
                                            @error('waktu_start')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Waktu Stop -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="waktu_stop">Waktu Stop <span class="text-danger">*</span></label>
                                            <input type="time" name="waktu_stop" id="waktu_stop" 
                                                   class="form-control @error('waktu_stop') is-invalid @enderror" 
                                                   value="{{ old('waktu_stop', $item->waktu_stop) }}" required>
                                            @error('waktu_stop')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Lama Proses -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="lama_proses">Lama Proses <span class="text-danger">*</span></label>
                                            <input type="text" name="lama_proses" id="lama_proses" 
                                                   class="form-control @error('lama_proses') is-invalid @enderror" 
                                                   value="{{ old('lama_proses', $item->lama_proses) }}" 
                                                   placeholder="Masukkan lama proses" required>
                                            @error('lama_proses')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Speed -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="speed">Speed <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" name="speed" id="speed" 
                                                       class="form-control @error('speed') is-invalid @enderror" 
                                                       value="{{ old('speed', $item->speed) }}" 
                                                       placeholder="Masukkan speed" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">RPM</span>
                                                </div>
                                            </div>
                                            @error('speed')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Sensori Kondisi -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="sensori_kondisi">Sensori Kondisi <span class="text-danger">*</span></label>
                                            <input type="text" name="sensori_kondisi" id="sensori_kondisi" 
                                                   class="form-control @error('sensori_kondisi') is-invalid @enderror" 
                                                   value="{{ old('sensori_kondisi', $item->sensori_kondisi) }}" 
                                                   placeholder="Masukkan sensori kondisi" required>
                                            @error('sensori_kondisi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Status Gas -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status_gas">Status Gas <span class="text-danger">*</span></label>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="status_gas" name="status_gas" value="1" 
                                                       {{ old('status_gas', $item->status_gas) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="status_gas">
                                                    <span id="status_gas_label">{{ old('status_gas', $item->status_gas) ? 'Aktif' : 'Tidak Aktif' }}</span>
                                                </label>
                                            </div>
                                            @error('status_gas')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h5><i class="fas fa-thermometer-half text-info"></i> Temperature Standards</h5>

                                <div class="row">
                                    <!-- Temperature Standard 1 -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="temp_std_1">Temperature Standard 1 <span class="text-danger">*</span></label>
                                            <input type="text" name="temp_std_1" id="temp_std_1" 
                                                   class="form-control @error('temp_std_1') is-invalid @enderror" 
                                                   value="{{ old('temp_std_1', $item->temp_std_1) }}" 
                                                   placeholder="Masukkan temperature std 1" required>
                                            @error('temp_std_1')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Temperature Standard 2 -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="temp_std_2">Temperature Standard 2 <span class="text-danger">*</span></label>
                                            <input type="text" name="temp_std_2" id="temp_std_2" 
                                                   class="form-control @error('temp_std_2') is-invalid @enderror" 
                                                   value="{{ old('temp_std_2', $item->temp_std_2) }}" 
                                                   placeholder="Masukkan temperature std 2" required>
                                            @error('temp_std_2')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Temperature Standard 3 -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="temp_std_3">Temperature Standard 3 <span class="text-danger">*</span></label>
                                            <input type="text" name="temp_std_3" id="temp_std_3" 
                                                   class="form-control @error('temp_std_3') is-invalid @enderror" 
                                                   value="{{ old('temp_std_3', $item->temp_std_3) }}" 
                                                   placeholder="Masukkan temperature std 3" required>
                                            @error('temp_std_3')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h5><i class="fas fa-eye text-success"></i> Organoleptic Tests</h5>

                                <div class="row">
                                    <!-- Organoleptic Warna -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="organo_warna">Organoleptic Warna <span class="text-danger">*</span></label>
                                            <select name="organo_warna" id="organo_warna" 
                                                    class="form-control @error('organo_warna') is-invalid @enderror" required>
                                                <option value="">Pilih Status</option>
                                                <option value="OK" {{ old('organo_warna', $item->organo_warna) == 'OK' ? 'selected' : '' }}>✓ OK</option>
                                                <option value="Tidak OK" {{ old('organo_warna', $item->organo_warna) == 'Tidak OK' ? 'selected' : '' }}>✗ Tidak OK</option>
                                            </select>
                                            @error('organo_warna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Organoleptic Aroma -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="organo_aroma">Organoleptic Aroma <span class="text-danger">*</span></label>
                                            <select name="organo_aroma" id="organo_aroma" 
                                                    class="form-control @error('organo_aroma') is-invalid @enderror" required>
                                                <option value="">Pilih Status</option>
                                                <option value="OK" {{ old('organo_aroma', $item->organo_aroma) == 'OK' ? 'selected' : '' }}>✓ OK</option>
                                                <option value="Tidak OK" {{ old('organo_aroma', $item->organo_aroma) == 'Tidak OK' ? 'selected' : '' }}>✗ Tidak OK</option>
                                            </select>
                                            @error('organo_aroma')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Organoleptic Tekstur -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="organo_tekstur">Organoleptic Tekstur <span class="text-danger">*</span></label>
                                            <select name="organo_tekstur" id="organo_tekstur" 
                                                    class="form-control @error('organo_tekstur') is-invalid @enderror" required>
                                                <option value="">Pilih Status</option>
                                                <option value="OK" {{ old('organo_tekstur', $item->organo_tekstur) == 'OK' ? 'selected' : '' }}>✓ OK</option>
                                                <option value="Tidak OK" {{ old('organo_tekstur', $item->organo_tekstur) == 'Tidak OK' ? 'selected' : '' }}>✗ Tidak OK</option>
                                            </select>
                                            @error('organo_tekstur')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Organoleptic Rasa -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="organo_rasa">Organoleptic Rasa <span class="text-danger">*</span></label>
                                            <select name="organo_rasa" id="organo_rasa" 
                                                    class="form-control @error('organo_rasa') is-invalid @enderror" required>
                                                <option value="">Pilih Status</option>
                                                <option value="OK" {{ old('organo_rasa', $item->organo_rasa) == 'OK' ? 'selected' : '' }}>✓ OK</option>
                                                <option value="Tidak OK" {{ old('organo_rasa', $item->organo_rasa) == 'Tidak OK' ? 'selected' : '' }}>✗ Tidak OK</option>
                                            </select>
                                            @error('organo_rasa')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Catatan -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="catatan">Catatan</label>
                                            <textarea name="catatan" id="catatan" 
                                                      class="form-control @error('catatan') is-invalid @enderror" 
                                                      rows="3" placeholder="Masukkan catatan (opsional)">{{ old('catatan', $item->catatan) }}</textarea>
                                            @error('catatan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update
                                </button>
                                <a href="{{ route('pemeriksaan-produk-cooking-mixer-fla.index') }}" class="btn btn-secondary">
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
