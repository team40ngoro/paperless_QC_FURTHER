@extends('layouts.app')
@php
    $isLocalhost = in_array(request()->getHost(), ['localhost', '127.0.0.1', '10.68.1.37']);
    $assetPath = $isLocalhost ? 'public/' : '';
@endphp
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Data Kontaminasi Benda Asing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-benda-asing.index') }}">Kontaminasi Benda Asing</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
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
                                <li class="nav-item"><a class="nav-link active" href="#pemeriksaan" data-toggle="tab">Edit Kontaminasi Benda Asing</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="pemeriksaan">
                                    <form action="{{ route('pemeriksaan-benda-asing.update', $pemeriksaan->uuid) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
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
                                                    <label for="shift_id" class="font-weight-bold">Shift <span class="text-danger">*</span></label>
                                                    <select name="shift_id" id="shift_id" class="form-control @error('shift_id') is-invalid @enderror" required>
                                                        <option value="">Pilih Shift</option>
                                                        @foreach($shifts as $shift)
                                                            <option value="{{ $shift->id }}" {{ old('shift_id', $pemeriksaan->shift_id) == $shift->id ? 'selected' : '' }}>
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
                                                            <option value="{{ $produk->id }}" {{ old('id_produk', $pemeriksaan->id_produk) == $produk->id ? 'selected' : '' }}>
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
                                                           value="{{ old('rm', $pemeriksaan->rm) }}" 
                                                           placeholder="Masukkan RM (opsional)">
                                                    @error('rm')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="berat" class="font-weight-bold">Berat (gram)</label>
                                                    <select name="berat" id="edit_nilai_select_berat_for_pemeriksaan_benda_asing" data-nilai-berat data-selected="{{ old('berat', $pemeriksaan->berat) }}" class="form-control @error('berat') is-invalid @enderror"></select>
                                                    @error('berat')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        {{-- //edit tanggal  --}}
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="tanggal_display">Tanggal</label>

                                                    <input type="date"
                                                        id="tanggal_display"
                                                        class="form-control"
                                                        value="{{ old('tanggal_display', $pemeriksaan->tanggal ? \Carbon\Carbon::parse($pemeriksaan->tanggal)->format('Y-m-d') : '') }}"
                                                        onchange="
                                                            const hidden = document.getElementById('tanggal');
                                                            const time = hidden.value ? hidden.value.split(' ')[1] : '00:00:00';
                                                            const [y,m,d] = this.value.split('-');

                                                            hidden.value = `${y}-${m}-${d} ${time}`;
                                                        ">

                                                    <input type="hidden"
                                                        id="tanggal"
                                                        name="tanggal"
                                                        value="{{ old('tanggal', $pemeriksaan->tanggal ? \Carbon\Carbon::parse($pemeriksaan->tanggal)->format('Y-m-d H:i:s') : '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="jam" class="font-weight-bold">Jam <span class="text-danger">*</span></label>
                                                    <input type="time" name="jam" id="jam" 
                                                           class="form-control @error('jam') is-invalid @enderror" 
                                                           value="{{ old('jam', $pemeriksaan->jam ? \Carbon\Carbon::parse($pemeriksaan->jam)->format('H:i') : '') }}" required>
                                                    @error('jam')
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
                                                        <option value="Biologi" {{ old('jenis_kontaminasi', $pemeriksaan->jenis_kontaminasi) == 'Biologi' ? 'selected' : '' }}>Biologi</option>
                                                        <option value="Fisik" {{ old('jenis_kontaminasi', $pemeriksaan->jenis_kontaminasi) == 'Fisik' ? 'selected' : '' }}>Fisik</option>
                                                        <option value="Kimia" {{ old('jenis_kontaminasi', $pemeriksaan->jenis_kontaminasi) == 'Kimia' ? 'selected' : '' }}>Kimia</option>
                                                        <option value="Allergen" {{ old('jenis_kontaminasi', $pemeriksaan->jenis_kontaminasi) == 'Allergen' ? 'selected' : '' }}>Allergen</option>
                                                        <option value="Radiologi" {{ old('jenis_kontaminasi', $pemeriksaan->jenis_kontaminasi) == 'Radiologi' ? 'selected' : '' }}>Radiologi</option>
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
                                                           value="{{ old('kode_produksi', $pemeriksaan->kode_produksi) }}" 
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
                                                           value="{{ old('ukuran_kontaminasi', $pemeriksaan->ukuran_kontaminasi) }}" 
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
                                                           value="{{ old('ditemukan', $pemeriksaan->ditemukan) }}" 
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
                                                           value="{{ old('jumlah_terdampak', $pemeriksaan->jumlah_terdampak) }}" 
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
                                                    @if($pemeriksaan->bukti)
                                                        <div class="mb-2">
                                                            <img src="{{ asset($assetPath . 'storage/' . $pemeriksaan->bukti) }}" 
                                                                 alt="Bukti foto" 
                                                                 class="img-thumbnail" 
                                                                 style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                                            <p class="text-muted small mt-1">Foto saat ini</p>
                                                        </div>
                                                    @else
                                                        <div class="mb-2">
                                                            <p class="text-muted">Belum ada foto yang diupload</p>
                                                        </div>
                                                    @endif
                                                    <input type="file" name="bukti" id="bukti" 
                                                           class="form-control-file @error('bukti') is-invalid @enderror" 
                                                           accept="image/*" capture="camera">
                                                    <small class="form-text text-muted">Upload foto bukti kontaminasi baru (opsional)</small>
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
                                                           value="{{ old('diketahui', $pemeriksaan->diketahui) }}" 
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
                                                              rows="3" placeholder="Masukkan analisa masalah">{{ old('analisa_masalah', $pemeriksaan->analisa_masalah) }}</textarea>
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
                                                              rows="3" placeholder="Masukkan koreksi yang dilakukan">{{ old('koreksi', $pemeriksaan->koreksi) }}</textarea>
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
                                                              rows="3" placeholder="Masukkan tindakan korektif">{{ old('tindak_korektif', $pemeriksaan->tindak_korektif) }}</textarea>
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
                                            <button type="submit" class="btn btn-md btn-warning ml-2">
                                                <i class="fas fa-save"></i> Update Data
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
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        initSelectBeratEdit(@json(old('berat', $pemeriksaan->berat)), '#edit_nilai_select_berat_for_pemeriksaan_benda_asing');

        var kontaminasiVal = @json(old('jenis_kontaminasi', $pemeriksaan->jenis_kontaminasi));
        var hasOption = $('#jenis_kontaminasi option[value="' + kontaminasiVal.replace(/"/g, '&quot;') + '"]').length > 0;

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
</script>
@endpush