@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Data Kontaminasi Benda Asing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-benda-asing.index') }}">Kontaminasi Benda Asing</a></li>
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
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#pemeriksaan" data-toggle="tab">Kontaminasi Benda Asing</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="pemeriksaan">
                                    <form action="{{ route('pemeriksaan-benda-asing.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                   <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                                    
                                                    @php
                                                        $user = auth()->user();
                                                        $roleId = $user->id_role ?? $user->role ?? 0;
                                                    @endphp

                                                    @if($roleId == 2 || $roleId == 3)
                                                        <input type="text" name="tanggal" id="tanggal" 
                                                            class="form-control @error('tanggal') is-invalid @enderror" 
                                                            value="{{ old('tanggal', now()->format('d-m-Y')) }}" readonly required>
                                                    @else
                                                        <input type="text" name="tanggal" id="tanggal" 
                                                            class="form-control @error('tanggal') is-invalid @enderror" 
                                                            value="{{ old('tanggal', now()->format('d-m-Y H:i:s')) }}" readonly required>
                                                    @endif
                                                    @error('tanggal')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="jam" class="font-weight-bold">Jam <span class="text-danger">*</span></label>
                                                    <input type="time" name="jam" id="jam" 
                                                           class="form-control @error('jam') is-invalid @enderror" 
                                                           value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                                    @error('jam')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="shift_id" class="font-weight-bold">Shift <span class="text-danger">*</span></label>
                                                    <select name="shift_id" id="shift_id" class="form-control @error('shift_id') is-invalid @enderror" required>
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
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="id_produk" class="font-weight-bold">Produk</label>
                                                    <select name="id_produk" id="id_produk" class="form-control @error('id_produk') is-invalid @enderror">
                                                        <option value="">Pilih Produk (opsional)</option>
                                                        @foreach($produks as $produk)
                                                            <option value="{{ $produk->id }}" {{ old('id_produk') == $produk->id ? 'selected' : '' }}>
                                                                {{ $produk->nama_produk }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-muted">Kosongkan jika untuk ketidaksesuaian non-produk</small>
                                                    @error('id_produk')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="rm" class="font-weight-bold">RM</label>
                                                    <input type="text" name="rm" id="rm" 
                                                           class="form-control @error('rm') is-invalid @enderror" 
                                                           value="{{ old('rm') }}" 
                                                           placeholder="Masukkan RM (opsional)">
                                                    @error('rm')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="berat" class="font-weight-bold">Berat (gram)</label>
                                                    <select name="berat" id="nilai_select_berat" class="form-control @error('berat') is-invalid @enderror"></select>
                                                    @error('berat')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="jenis_kontaminasi" class="font-weight-bold">Jenis Kontaminasi <span class="text-danger">*</span></label>
                                                    <select name="jenis_kontaminasi" id="jenis_kontaminasi" class="form-control select2-tags @error('jenis_kontaminasi') is-invalid @enderror" required>
                                                        <option value="">Pilih atau ketik manual</option>
                                                        <option value="Biologi">Biologi</option>
                                                        <option value="Fisik">Fisik</option>
                                                        <option value="Kimia">Kimia</option>
                                                        <option value="Allergen">Allergen</option>
                                                        <option value="Radiologi">Radiologi</option>
                                                    </select>
                                                    <small class="text-muted">Jenis Kontaminasi (Biologi, Fisik, Kimia, Allergen, Radiologi)</small>
                                                    @error('jenis_kontaminasi')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="kode_produksi" class="font-weight-bold">Kode Produksi <span class="text-danger">*</span></label>
                                                    <input type="text" name="kode_produksi" id="kode_produksi" 
                                                           class="form-control @error('kode_produksi') is-invalid @enderror" 
                                                           value="{{ old('kode_produksi') }}" 
                                                           placeholder="Masukkan kode produksi" required>
                                                    @error('kode_produksi')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="ukuran_kontaminasi" class="font-weight-bold">Ukuran Kontaminasi <span class="text-danger">*</span></label>
                                                    <input type="text" name="ukuran_kontaminasi" id="ukuran_kontaminasi" 
                                                           class="form-control @error('ukuran_kontaminasi') is-invalid @enderror" 
                                                           value="{{ old('ukuran_kontaminasi') }}" 
                                                           placeholder="Masukkan ukuran kontaminasi" required>
                                                    @error('ukuran_kontaminasi')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="ditemukan" class="font-weight-bold">Ditemukan <span class="text-danger">*</span></label>
                                                    <input type="text" name="ditemukan" id="ditemukan" 
                                                           class="form-control @error('ditemukan') is-invalid @enderror" 
                                                           value="{{ old('ditemukan') }}" 
                                                           placeholder="Lokasi/cara ditemukan" required>
                                                    @error('ditemukan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="jumlah_terdampak" class="font-weight-bold">Jumlah Produk/RM Terdampak</label>
                                                    <input type="text" name="jumlah_terdampak" id="jumlah_terdampak" 
                                                           class="form-control @error('jumlah_terdampak') is-invalid @enderror" 
                                                           value="{{ old('jumlah_terdampak') }}" 
                                                           placeholder="Jumlah produk/RM terdampak">
                                                    @error('jumlah_terdampak')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="bukti" class="font-weight-bold">Bukti Foto</label>
                                                    <input type="file" name="bukti" id="bukti" 
                                                           class="form-control-file @error('bukti') is-invalid @enderror" 
                                                           accept="image/*" capture="camera" onchange="compressImage(this)">
                                                    <small class="form-text text-muted">Upload foto bukti kontaminasi (opsional)</small>
                                                    <small id="compress-info-bukti" class="form-text font-weight-bold mt-1" style="display: none;"></small>
                                                    @error('bukti')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="diketahui" class="font-weight-bold">Diketahui Oleh</label>
                                                    <input type="text" name="diketahui" id="diketahui" 
                                                           class="form-control @error('diketahui') is-invalid @enderror" 
                                                           value="{{ old('diketahui') }}" 
                                                           placeholder="Nama yang mengetahui">
                                                    @error('diketahui')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="analisa_masalah" class="font-weight-bold">Analisa Masalah</label>
                                                    <textarea name="analisa_masalah" id="analisa_masalah" 
                                                              class="form-control @error('analisa_masalah') is-invalid @enderror" 
                                                              rows="3" placeholder="Masukkan analisa masalah">{{ old('analisa_masalah') }}</textarea>
                                                    @error('analisa_masalah')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="koreksi" class="font-weight-bold">Koreksi</label>
                                                    <textarea name="koreksi" id="koreksi" 
                                                              class="form-control @error('koreksi') is-invalid @enderror" 
                                                              rows="3" placeholder="Masukkan koreksi yang dilakukan">{{ old('koreksi') }}</textarea>
                                                    @error('koreksi')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="tindak_korektif" class="font-weight-bold">Tindakan Korektif</label>
                                                    <textarea name="tindak_korektif" id="tindak_korektif" 
                                                              class="form-control @error('tindak_korektif') is-invalid @enderror" 
                                                              rows="3" placeholder="Masukkan tindakan korektif">{{ old('tindak_korektif') }}</textarea>
                                                    @error('tindak_korektif')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <a href="{{ route('pemeriksaan-benda-asing.index') }}" class="btn btn-md btn-secondary">
                                                <i class="fas fa-arrow-left"></i> Kembali
                                            </a>
                                            <button type="submit" class="btn btn-md btn-primary ml-2">
                                                <i class="fas fa-save"></i> Simpan Data
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        var oldBerat = @json(old('berat'));
        if (oldBerat) {
            $('#nilai_select_berat').val(oldBerat).trigger('change');
        }

        var kontaminasiVal = @json(old('jenis_kontaminasi'));
        var hasOption = kontaminasiVal && $('#jenis_kontaminasi option[value="' + kontaminasiVal.replace(/"/g, '&quot;') + '"]').length > 0;

        $('.select2-tags').select2({
            tags: true,
            theme: 'bootstrap4',
            placeholder: 'Pilih atau ketik manual',
            allowClear: true,
            width: '100%'
        });

        if (!hasOption && kontaminasiVal) {
            var newOption = new Option(kontaminasiVal, kontaminasiVal, true, true);
            $('#jenis_kontaminasi').append(newOption).trigger('change');
        }
    });

    function compressImage(input) {
        var file = input.files[0];
        if (!file) return;
        if (!file.type.match(/image.*/)) return;

        var infoBox = $('#compress-info-bukti');
        infoBox.removeClass('text-danger text-success').addClass('text-info')
               .html('<i class="fas fa-spinner fa-spin"></i> Mengompres gambar...').show();

        var reader = new FileReader();
        reader.onload = function(e) {
            var img = new Image();
            img.onload = function() {
                var canvas = document.createElement('canvas');
                var maxW = 1280;
                var maxH = 1280;
                var w = img.width;
                var h = img.height;

                if (w > maxW || h > maxH) {
                    if (w > h) {
                        if (w > maxW) {
                            h = Math.round(h * (maxW / w));
                            w = maxW;
                        }
                    } else {
                        if (h > maxH) {
                            w = Math.round(w * (maxH / h));
                            h = maxH;
                        }
                    }
                }

                canvas.width = w;
                canvas.height = h;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, w, h);

                var quality = 0.7;

                canvas.toBlob(function(blob) {
                    if (!blob) {
                        infoBox.hide();
                        return;
                    }

                    var newFile = new File([blob], file.name, {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });

                    var dt = new DataTransfer();
                    dt.items.add(newFile);
                    input.files = dt.files;

                    var origSize = file.size > 1024 * 1024 
                        ? (file.size / (1024 * 1024)).toFixed(2) + ' MB'
                        : (file.size / 1024).toFixed(1) + ' KB';
                    var compSize = blob.size > 1024 * 1024 
                        ? (blob.size / (1024 * 1024)).toFixed(2) + ' MB'
                        : (blob.size / 1024).toFixed(1) + ' KB';

                    infoBox.removeClass('text-info text-danger').addClass('text-success')
                        .html('<i class="fas fa-check-circle"></i> Berhasil dikompres: ' + origSize + ' → ' + compSize).show();
                }, 'image/jpeg', quality);
            };
            img.onerror = function() {
                infoBox.hide();
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush
@endsection