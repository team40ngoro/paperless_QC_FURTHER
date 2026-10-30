@extends('layouts.app')

@section('title', 'Tambah Data Produk Non Forming')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Data Produk Non Forming</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="">Produk Non Forming</a></li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <form action="{{ route('produk-non-forming.store') }}" method="POST">
                    @csrf
                    <!-- Basic Information -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>Informasi Dasar
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_produk">
                                            <i class="fas fa-box mr-1"></i>Produk Non Forming
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_produk" id="id_produk" class="form-control @error('id_produk') is-invalid @enderror" required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($produks as $item)
                                                <option value="{{ $item->id }}" {{ old('id_produk') == $item->id ? 'selected' : '' }}>
                                                    {{ $item->nama_produk }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_produk')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_shift">
                                            <i class="fas fa-clock mr-1"></i>Shift
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_shift" id="id_shift" class="form-control @error('id_shift') is-invalid @enderror" required>
                                            <option value="">-- Pilih Shift --</option>
                                            @foreach($shifts as $shift)
                                                <option value="{{ $shift->id }}" {{ old('id_shift') == $shift->id ? 'selected' : '' }}>
                                                    {{ $shift->shift }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_shift')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
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
                        </div>
                    </div>
                    <!-- Card Informasi Keterangan -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card card-info card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-info-circle"></i> Keterangan Pengecekan & Kriteria Penilaian
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5 class="text-primary"><i class="fas fa-clipboard-check"></i> Keterangan Pengecekan:</h5>
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                <i class="fas fa-dot-circle text-info"></i>
                                                <strong> Pengecekan Kondisi Sisa Bahan/Kemasan (1 - 5):</strong> nomer 1-8
                                            </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-dot-circle text-info"></i>
                                                    <strong>Pengecekan Kondisi Mesin dan Peralatan :</strong> nomer 3-8
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-dot-circle text-info"></i>
                                                    <strong>Pengecekan Kondisi Ruangan:</strong> nomer 3-8
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h5 class="text-success"><i class="fas fa-star"></i> Kriteria Penilaian:</h5>
                                            <div class="row">
                                                <div class="col-12">
                                                    <small class="text-muted">
                                                        <ol class="mb-0" style="font-size: 1.0em;">
                                                            <li>Sesuai spesifikasi</li>
                                                            <li>Tidak sesuai spesifikasi</li>
                                                            <li>Bebas dari kontaminan dan bahan sebelumnya</li>
                                                            <li>Ada kontaminan atau sisa bahan sebelumnya</li>
                                                            <li>Bebas dari potensi kontaminasi allergen</li>
                                                            <li>Ada potensi kontaminasi allergen</li>
                                                            <li>Bersih, tidak ada kontaminan atau kotoran, tidak tercium bau menyimpang</li>
                                                            <li>Tidak bersih, ada kontaminan atau kotoran, tercium bau menyimpang</li>
                                                        </ol>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Petunjuk:</strong> Gunakan nomor kriteria penilaian di atas untuk mengisi form penilaian pada setiap aspek pemeriksaan.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- A. Bahan Baku dan Bahan Penunjang -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cubes mr-2"></i>A. Bahan Baku dan Bahan Penunjang
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center" style="width:50px;">No</th>
                                            <th>Nama Item</th>
                                            <th class="text-center" style="width:250px;">Penilaian</th>
                                            <th>Tindakan Koreksi</th>
                                            <th class="text-center" style="width:130px;">Verifikasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $defaultBahan = ['Sisa Bahan Baku'];
                                        @endphp
                                        @foreach($defaultBahan as $i => $namaBahan)
                                            <input type="hidden" name="bahan_baku[{{ $i }}][nama]" value="{{ $namaBahan }}">
                                            <tr>
                                                <td class="text-center align-middle">{{ $i + 1 }}</td>
                                                <td class="align-middle">
                                                    <strong>{{ $namaBahan }}</strong>
                                                </td>
                                                <td class="align-middle" style="min-width: 250px;">
                                                    <div class="btn-group-toggle d-flex flex-column" data-toggle="buttons">
                                                        @foreach(App\Models\ProdukForming::getBahanOptions() as $key => $value)
                                                            @php
                                                                $isChecked = (is_array(old('bahan_baku.'.$i.'.penilaian')) && in_array($key, old('bahan_baku.'.$i.'.penilaian')));
                                                            @endphp
                                                            <label class="btn btn-outline-primary btn-sm mb-1 text-left {{ $isChecked ? 'active' : '' }}" style="white-space: normal; line-height: 1.2;">
                                                                <input type="checkbox" 
                                                                       name="bahan_baku[{{ $i }}][penilaian][]" 
                                                                       value="{{ $key }}"
                                                                       {{ $isChecked ? 'checked' : '' }}
                                                                       autocomplete="off"> {{ $value }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="bahan_baku[{{ $i }}][tindakan_koreksi]"
                                                        value="{{ old('bahan_baku.'.$i.'.tindakan_koreksi') }}"
                                                        placeholder="Opsional">
                                                </td>
                                                <td class="text-center align-middle">
                                                    <select class="form-control form-control-sm" name="bahan_baku[{{ $i }}][verifikasi]">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="✓" {{ old('bahan_baku.'.$i.'.verifikasi') == '✓' ? 'selected' : '' }}>Ok (✓)</option>
                                                        <option value="✗" {{ old('bahan_baku.'.$i.'.verifikasi') == '✗' ? 'selected' : '' }}>Tidak Ok (✗)</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <!-- B. Kemasan -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-box-open mr-2"></i>B. Sisa Kemasan dan Labelisasi
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center" style="width:50px;">No</th>
                                            <th>Nama Item</th>
                                            <th class="text-center" style="width:250px;">Penilaian</th>
                                            <th>Tindakan Koreksi</th>
                                            <th class="text-center" style="width:130px;">Verifikasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $kemasanItems = [
                                                ['field' => 'kemasan_plastik',    'label' => 'Kemasan Plastik'],
                                                ['field' => 'kemasan_karton',     'label' => 'Kemasan Karton'],
                                                ['field' => 'labelisasi_plastik', 'label' => 'Labelisasi Plastik'],
                                                ['field' => 'labelisasi_karton',  'label' => 'Labelisasi Karton'],
                                            ];
                                        @endphp
                                        @foreach($kemasanItems as $ki => $kItem)
                                            <tr>
                                                <td class="text-center align-middle">{{ $ki + 1 }}</td>
                                                <td class="align-middle"><strong>{{ $kItem['label'] }}</strong></td>
                                                <td class="align-middle" style="min-width: 250px;">
                                                    <div class="btn-group-toggle d-flex flex-column" data-toggle="buttons">
                                                        @foreach(App\Models\ProdukForming::getKemasanOptions() as $key => $value)
                                                            @php
                                                                $isChecked = is_array(old($kItem['field'])) && in_array($key, old($kItem['field']));
                                                            @endphp
                                                            <label class="btn btn-outline-primary btn-sm mb-1 text-left {{ $isChecked ? 'active' : '' }}" style="white-space: normal; line-height: 1.2;">
                                                                <input type="checkbox"
                                                                       name="{{ $kItem['field'] }}[]"
                                                                       value="{{ $key }}"
                                                                       {{ $isChecked ? 'checked' : '' }}
                                                                       autocomplete="off"> {{ $value }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="{{ $kItem['field'] }}_tindakan"
                                                        value="{{ old($kItem['field'].'_tindakan') }}"
                                                        placeholder="Opsional">
                                                </td>
                                                <td class="text-center align-middle">
                                                    <select class="form-control form-control-sm" name="{{ $kItem['field'] }}_verifikasi">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="✓" {{ old($kItem['field'].'_verifikasi') == '✓' ? 'selected' : '' }}>Ok (✓)</option>
                                                        <option value="✗" {{ old($kItem['field'].'_verifikasi') == '✗' ? 'selected' : '' }}>Tidak Ok (✗)</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- C. Mesin Dan Peralatan -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs mr-2"></i>C. Mesin Dan Peralatan
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center" style="width:50px;">No</th>
                                            <th>Nama Mesin/Peralatan</th>
                                            <th class="text-center" style="width:150px;">Kondisi</th>
                                            <th>Tindakan Koreksi</th>
                                            <th class="text-center" style="width:130px;">Verifikasi</th>
                                            <th class="text-center" style="width:100px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($masterMesin) && $masterMesin->count())
                                            @foreach($masterMesin as $i => $mesin)
                                                <input type="hidden" name="mesin_peralatan[{{ $i }}][nama_mesin]" value="{{ $mesin->nama_mesin }}">
                                                <input type="hidden" class="input-dihapus" name="mesin_peralatan[{{ $i }}][dihapus]" value="0">
                                                <tr class="mesin-row" id="mesin-row-{{ $i }}">
                                                    <td class="text-center align-middle">{{ $i + 1 }}</td>
                                                    <td class="align-middle">
                                                        <strong>{{ $mesin->nama_mesin }}</strong>
                                                        @if($mesin->area)
                                                            <br><small class="text-muted">{{ $mesin->area->area }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle" style="min-width: 250px;">
                                                        <div class="btn-group-toggle d-flex flex-column" data-toggle="buttons">
                                                            @foreach(App\Models\ProdukForming::getMesinOptions() as $key => $value)
                                                                @php
                                                                    $isChecked = (is_array(old('mesin_peralatan.'.$i.'.kondisi')) && in_array($key, old('mesin_peralatan.'.$i.'.kondisi')));
                                                                @endphp
                                                                <label class="btn btn-outline-primary btn-sm mb-1 text-left {{ $isChecked ? 'active' : '' }}" style="white-space: normal; line-height: 1.2;">
                                                                    <input type="checkbox" 
                                                                           name="mesin_peralatan[{{ $i }}][kondisi][]" 
                                                                           value="{{ $key }}"
                                                                           {{ $isChecked ? 'checked' : '' }}
                                                                           autocomplete="off"> {{ $value }}
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                        @error('mesin_peralatan.'.$i.'.kondisi')
                                                            <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle"></i> Penilaian kondisi wajib dipilih</div>
                                                        @enderror
                                                    </td>
                                                    <td class="align-middle">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="mesin_peralatan[{{ $i }}][tindakan_koreksi]"
                                                            value="{{ old('mesin_peralatan.'.$i.'.tindakan_koreksi') }}"
                                                            placeholder="Opsional">
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <select class="form-control form-control-sm" name="mesin_peralatan[{{ $i }}][verifikasi]">
                                                            <option value="">-- Pilih --</option>
                                                            <option value="✓" {{ old('mesin_peralatan.'.$i.'.verifikasi') == '✓' ? 'selected' : '' }}>Ok (✓)</option>
                                                            <option value="✗" {{ old('mesin_peralatan.'.$i.'.verifikasi') == '✗' ? 'selected' : '' }}>Tidak Ok (✗)</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <button type="button" class="btn btn-outline-danger btn-sm btn-hapus-baris"
                                                            data-target="mesin-row-{{ $i }}" data-nama="{{ $mesin->nama_mesin }}">
                                                            <i class="fas fa-trash-alt"></i> Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-3">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Belum ada data mesin/peralatan. Isi Jenis Produk di Master Input Verifikasi Kebersihan Ruangan Mesin dan Peralatan terlebih dahulu.
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 border-top" id="mesin-deleted-list-wrapper" style="display:none;">
                                <strong><i class="fas fa-trash-alt text-muted mr-1"></i> Item yang tidak dipakai (dihapus dari form ini):</strong>
                                <div class="mt-2 d-flex flex-wrap" id="mesin-deleted-list"></div>
                            </div>
                        </div>
                    </div>

                    <!-- D. Ruangan -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-door-open mr-2"></i>D. Ruangan
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center" style="width:50px;">No</th>
                                            <th>Nama Ruangan</th>
                                            <th class="text-center" style="width:250px;">Kondisi</th>
                                            <th>Tindakan Koreksi</th>
                                            <th class="text-center" style="width:130px;">Verifikasi</th>
                                            <th class="text-center" style="width:100px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($inputAreas) && $inputAreas->count())
                                            @foreach($inputAreas as $i => $area)
                                                <input type="hidden" name="ruangan[{{ $i }}][nama_ruangan]" value="{{ $area->area }}">
                                                <input type="hidden" class="input-dihapus" name="ruangan[{{ $i }}][dihapus]" value="0">
                                                <tr id="ruangan-row-{{ $i }}">
                                                    <td class="text-center align-middle">{{ $i + 1 }}</td>
                                                    <td class="align-middle"><strong>{{ $area->area }}</strong></td>
                                                    <td class="align-middle" style="min-width: 250px;">
                                                        <div class="btn-group-toggle d-flex flex-column" data-toggle="buttons">
                                                            @foreach(App\Models\ProdukForming::getMesinOptions() as $key => $value)
                                                                @php
                                                                    $isChecked = (is_array(old('ruangan.'.$i.'.kondisi')) && in_array($key, old('ruangan.'.$i.'.kondisi')));
                                                                @endphp
                                                                <label class="btn btn-outline-secondary btn-sm mb-1 text-left {{ $isChecked ? 'active' : '' }}" style="white-space: normal; line-height: 1.2;">
                                                                    <input type="checkbox"
                                                                           name="ruangan[{{ $i }}][kondisi][]"
                                                                           value="{{ $key }}"
                                                                           {{ $isChecked ? 'checked' : '' }}
                                                                           autocomplete="off"> {{ $value }}
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                    <td class="align-middle">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="ruangan[{{ $i }}][tindakan_koreksi]"
                                                            value="{{ old('ruangan.'.$i.'.tindakan_koreksi') }}"
                                                            placeholder="Opsional">
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <select class="form-control form-control-sm" name="ruangan[{{ $i }}][verifikasi]">
                                                            <option value="">-- Pilih --</option>
                                                            <option value="✓" {{ old('ruangan.'.$i.'.verifikasi') == '✓' ? 'selected' : '' }}>Ok (✓)</option>
                                                            <option value="✗" {{ old('ruangan.'.$i.'.verifikasi') == '✗' ? 'selected' : '' }}>Tidak Ok (✗)</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <button type="button" class="btn btn-outline-danger btn-sm btn-hapus-baris"
                                                            data-target="ruangan-row-{{ $i }}" data-nama="{{ $area->area }}">
                                                            <i class="fas fa-trash-alt"></i> Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-3">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Belum ada data ruangan. Tambahkan data ruangan di Master Input Area terlebih dahulu.
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 border-top" id="ruangan-deleted-list-wrapper" style="display:none;">
                                <strong><i class="fas fa-trash-alt text-muted mr-1"></i> Item yang tidak dipakai (dihapus dari form ini):</strong>
                                <div class="mt-2 d-flex flex-wrap" id="ruangan-deleted-list"></div>
                            </div>
                        </div>
                    </div>

                    <!-- E. Keterangan -->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-sticky-note mr-2"></i>E. Keterangan / Catatan
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3" placeholder="Masukkan keterangan atau catatan tambahan">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>Simpan Data
                                    </button>
                                    <a href="{{ route('produk-non-forming.index') }}" class="btn btn-secondary ml-2">
                                        <i class="fas fa-arrow-left mr-1"></i>Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Validasi form — mesin tidak wajib diisi

    // ==== Fitur Hapus / Batal Hapus baris (Mesin & Peralatan, Ruangan) ====
    // "prefix" diambil dari id baris: "mesin-row-0" -> "mesin", "ruangan-row-0" -> "ruangan"
    function toggleDeletedWrapper(prefix) {
        var $wrapper = $('#' + prefix + '-deleted-list-wrapper');
        var hasItems = $('#' + prefix + '-deleted-list').children().length > 0;
        $wrapper.toggle(hasItems);
    }

    // Klik "Hapus" pada baris
    $(document).on('click', '.btn-hapus-baris', function() {
        var $btn = $(this);
        var targetId = $btn.data('target');
        var nama = $btn.data('nama');
        var prefix = targetId.split('-')[0];
        var $row = $('#' + targetId);

        // Sembunyikan baris & tandai dihapus
        $row.addClass('d-none');
        $row.find('.input-dihapus').val('1');
        // Nonaktifkan input di baris itu supaya TIDAK ikut terkirim saat submit,
        // kecuali input .input-dihapus yang memang harus tetap terkirim
        $row.find('input:not(.input-dihapus), select').prop('disabled', true);

        // Tambahkan badge ke daftar "item yang dihapus"
        var $badge = $(
            '<span class="badge badge-secondary p-2 mr-2 mb-2" data-row-id="' + targetId + '">' +
                $('<div>').text(nama).html() +
                ' <button type="button" class="btn btn-link btn-sm text-white p-0 ml-2 btn-batal-hapus" style="text-decoration:none;">' +
                    '<i class="fas fa-undo"></i> Batal' +
                '</button>' +
            '</span>'
        );
        $('#' + prefix + '-deleted-list').append($badge);
        toggleDeletedWrapper(prefix);
    });

    // Klik "Batal" pada badge item yang dihapus -> kembalikan baris
    $(document).on('click', '.btn-batal-hapus', function() {
        var $badge = $(this).closest('[data-row-id]');
        var targetId = $badge.data('row-id');
        var prefix = targetId.split('-')[0];
        var $row = $('#' + targetId);

        $row.removeClass('d-none');
        $row.find('.input-dihapus').val('0');
        $row.find('input:not(.input-dihapus), select').prop('disabled', false);

        $badge.remove();
        toggleDeletedWrapper(prefix);
    });
});
</script>
@endpush