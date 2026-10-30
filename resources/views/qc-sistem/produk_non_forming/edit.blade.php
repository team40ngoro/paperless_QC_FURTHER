@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Produk Non Forming</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('produk-non-forming.index') }}">Produk Non Forming</a></li>
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
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Produk Non Forming</h3>
                        </div>
                        <!-- /.card-header -->
                        <form action="{{ route('produk-non-forming.update', $produkNonForming->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
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

                                <div class="row">
                                    <!-- Tanggal -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control @error('tanggal') is-invalid @enderror" 
                                                   id="tanggal" name="tanggal" 
                                                   value="{{ old('tanggal', \Carbon\Carbon::parse($produkNonForming->tanggal)->format('Y-m-d\TH:i')) }}" required>
                                            @error('tanggal')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Produk -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_produk">Produk <span class="text-danger">*</span></label>
                                            <select class="form-control @error('id_produk') is-invalid @enderror" 
                                                    id="id_produk" name="id_produk" required>
                                                <option value="">Pilih Produk</option>
                                                @foreach($produks as $produk)
                                                    <option value="{{ $produk->id }}" 
                                                            {{ old('id_produk', $produkNonForming->id_produk) == $produk->id ? 'selected' : '' }}>
                                                        {{ $produk->nama_produk }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_produk')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Shift -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_shift">Shift <span class="text-danger">*</span></label>
                                            <select class="form-control @error('id_shift') is-invalid @enderror" 
                                                    id="id_shift" name="id_shift" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" 
                                                            {{ old('id_shift', $produkNonForming->id_shift) == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_shift')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
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
                                                                <strong>Pengecekan Kondisi Bahan baku, bahan penunjang, dan kemasan (1 dan 2):</strong> nomer 1-6
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="fas fa-dot-circle text-info"></i>
                                                                <strong>Pengecekan Kemasan (3 dan 4):</strong> nomer 1-2
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="fas fa-dot-circle text-info"></i>
                                                                <strong>Pengecekan Kondisi Mesin dan Peralatan:</strong> nomer 3-8
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h5 class="text-success"><i class="fas fa-star"></i> Kriteria Penilaian:</h5>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <small class="text-muted">
                                                                    <ol class="mb-0" style="font-size: 0.9em;">
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
                                                        $existingBahan = is_array($produkNonForming->bahan_baku) ? $produkNonForming->bahan_baku : [];
                                                    @endphp
                                                    @foreach($defaultBahan as $i => $namaBahan)
                                                        @php
                                                            $currentBahan = $existingBahan[$i] ?? ['nama' => $namaBahan];
                                                            $currentNama = $currentBahan['nama'] ?? $namaBahan;
                                                            $currentPenilaian = $currentBahan['penilaian'] ?? [];
                                                            if (!is_array($currentPenilaian)) $currentPenilaian = [$currentPenilaian];
                                                            $currentTindakan = $currentBahan['tindakan_koreksi'] ?? '';
                                                            $currentVerifikasi = $currentBahan['verifikasi'] ?? '';
                                                            
                                                            $oldData = old('bahan_baku.'.$i);
                                                            if ($oldData) {
                                                                $currentNama = $oldData['nama'] ?? $currentNama;
                                                                $currentPenilaian = $oldData['penilaian'] ?? $currentPenilaian;
                                                                if (!is_array($currentPenilaian)) $currentPenilaian = [$currentPenilaian];
                                                                $currentTindakan = $oldData['tindakan_koreksi'] ?? $currentTindakan;
                                                                $currentVerifikasi = $oldData['verifikasi'] ?? $currentVerifikasi;
                                                            }
                                                        @endphp
                                                        <input type="hidden" name="bahan_baku[{{ $i }}][nama]" value="{{ $currentNama }}">
                                                        <tr>
                                                            <td class="text-center align-middle">{{ $i + 1 }}</td>
                                                            <td class="align-middle">
                                                                <strong>{{ $currentNama }}</strong>
                                                            </td>
                                                            <td class="align-middle" style="min-width: 250px;">
                                                                <div class="btn-group-toggle d-flex flex-column" data-toggle="buttons">
                                                                    @foreach(App\Models\ProdukForming::getBahanOptions() as $key => $value)
                                                                        @php
                                                                            $isChecked = in_array($key, $currentPenilaian);
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
                                                                    value="{{ $currentTindakan }}"
                                                                    placeholder="Opsional">
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <select class="form-control form-control-sm" name="bahan_baku[{{ $i }}][verifikasi]">
                                                                    <option value="">-- Pilih --</option>
                                                                    <option value="✓" {{ $currentVerifikasi == '✓' ? 'selected' : '' }}>Ok (✓)</option>
                                                                    <option value="✗" {{ $currentVerifikasi == '✗' ? 'selected' : '' }}>Tidak Ok (✗)</option>
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
                                                        @php
                                                            $savedKem = old($kItem['field'], $produkNonForming->{$kItem['field']} ?? []);
                                                            if (!is_array($savedKem)) $savedKem = $savedKem ? [$savedKem] : [];
                                                            $savedKemTindakan = old($kItem['field'].'_tindakan', $produkNonForming->{$kItem['field'].'_tindakan'} ?? '');
                                                            $savedKemVerif = old($kItem['field'].'_verifikasi', $produkNonForming->{$kItem['field'].'_verifikasi'} ?? '');
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center align-middle">{{ $ki + 1 }}</td>
                                                            <td class="align-middle"><strong>{{ $kItem['label'] }}</strong></td>
                                                            <td class="align-middle" style="min-width: 250px;">
                                                                <div class="btn-group-toggle d-flex flex-column" data-toggle="buttons">
                                                                    @foreach(App\Models\ProdukForming::getKemasanOptions() as $key => $value)
                                                                        @php $isChecked = in_array($key, $savedKem); @endphp
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
                                                                    value="{{ $savedKemTindakan }}"
                                                                    placeholder="Opsional">
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <select class="form-control form-control-sm" name="{{ $kItem['field'] }}_verifikasi">
                                                                    <option value="">-- Pilih --</option>
                                                                    <option value="✓" {{ $savedKemVerif == '✓' ? 'selected' : '' }}>Ok (✓)</option>
                                                                    <option value="✗" {{ $savedKemVerif == '✗' ? 'selected' : '' }}>Tidak Ok (✗)</option>
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
                                        @php
                                            $mesinData = [];
                                            $isNewFormat = false;
                                            if (!empty(old('mesin_peralatan', $produkNonForming->mesin_peralatan))) {
                                                $mesinData = old('mesin_peralatan', $produkNonForming->mesin_peralatan);
                                                $isNewFormat = true;
                                            } elseif (!empty($produkNonForming->penilaian) && is_array($produkNonForming->penilaian)) {
                                                $mesinData = $produkNonForming->penilaian;
                                                $isNewFormat = false; // Note: 'penilaian' format used 'aspek' and 'nilai'
                                            }
                                        @endphp

                                        @if(count($mesinData) > 0)
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
                                                        @foreach($mesinData as $i => $mesin)
                                                            @php
                                                                $namaMesin = $isNewFormat ? ($mesin['nama_mesin'] ?? '-') : ($mesin['aspek'] ?? '-');
                                                                $kondisi = $isNewFormat ? ($mesin['kondisi'] ?? []) : ($mesin['nilai'] ?? []);
                                                                if (!is_array($kondisi)) $kondisi = [$kondisi];
                                                                $isDihapus = (($mesin['dihapus'] ?? '0') == '1');
                                                            @endphp
                                                            <tr class="mesin-row {{ $isDihapus ? 'd-none' : '' }}" id="mesin-row-{{ $i }}" data-nama="{{ $namaMesin }}">
                                                                <td class="text-center align-middle">{{ $i + 1 }}</td>
                                                                <td class="align-middle">
                                                                    <input type="hidden" name="mesin_peralatan[{{ $i }}][nama_mesin]" value="{{ $namaMesin }}">
                                                                    <input type="hidden" class="input-dihapus" name="mesin_peralatan[{{ $i }}][dihapus]" value="{{ $isDihapus ? '1' : '0' }}">
                                                                    <strong>{{ $namaMesin }}</strong>
                                                                </td>
                                                                <td class="align-middle" style="min-width: 250px;">
                                                                    <div class="btn-group-toggle d-flex flex-column" data-toggle="buttons">
                                                                        @foreach(App\Models\ProdukForming::getMesinOptions() as $key => $value)
                                                                            @php
                                                                                $isChecked = in_array($key, $kondisi);
                                                                            @endphp
                                                                            <label class="btn btn-outline-primary btn-sm mb-1 text-left {{ $isChecked ? 'active' : '' }}" style="white-space: normal; line-height: 1.2;">
                                                                                <input type="checkbox" 
                                                                                       name="mesin_peralatan[{{ $i }}][kondisi][]" 
                                                                                       value="{{ $key }}"
                                                                                       {{ $isChecked ? 'checked' : '' }}
                                                                                       {{ $isDihapus ? 'disabled' : '' }}
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
                                                                        value="{{ $mesin['tindakan_koreksi'] ?? '' }}"
                                                                        {{ $isDihapus ? 'disabled' : '' }}
                                                                        placeholder="Opsional">
                                                                </td>
                                                                <td class="text-center align-middle">
                                                                    <select class="form-control form-control-sm" name="mesin_peralatan[{{ $i }}][verifikasi]" {{ $isDihapus ? 'disabled' : '' }}>
                                                                        <option value="">-- Pilih --</option>
                                                                        <option value="✓" {{ ($mesin['verifikasi'] ?? '') == '✓' ? 'selected' : '' }}>Ok (✓)</option>
                                                                        <option value="✗" {{ ($mesin['verifikasi'] ?? '') == '✗' ? 'selected' : '' }}>Tidak Ok (✗)</option>
                                                                    </select>
                                                                </td>
                                                                <td class="text-center align-middle">
                                                                    <button type="button" class="btn btn-outline-danger btn-sm btn-hapus-baris"
                                                                        data-target="mesin-row-{{ $i }}" data-nama="{{ $namaMesin }}">
                                                                        <i class="fas fa-trash-alt"></i> Hapus
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="p-3 border-top" id="mesin-deleted-list-wrapper" style="display:none;">
                                                <strong><i class="fas fa-trash-alt text-muted mr-1"></i> Item yang tidak dipakai (dihapus dari form ini):</strong>
                                                <div class="mt-2 d-flex flex-wrap" id="mesin-deleted-list"></div>
                                            </div>
                                        @else
                                            <div class="p-3">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle mr-2"></i> Mode Edit Data Lama. Anda sedang mengedit data dengan format lama.
                                                </div>
                                                <div class="row">
                                                    @php
                                                        $mesinFields = [
                                                            'tumbler' => 'Tumbler',
                                                            'frayer' => 'Frayer',
                                                            'hicook' => 'Hicook',
                                                            'iqf_advance_1' => 'IQF Advance 1',
                                                            'iqf_advance_2' => 'IQF Advance 2',
                                                            'keranjang' => 'Keranjang',
                                                            'palet' => 'Palet',
                                                            'meatcar' => 'Meatcar',
                                                            'timbangan' => 'Timbangan',
                                                            'mhw' => 'MHW',
                                                            'foot_sealer' => 'Foot Sealer',
                                                            'metal_detector' => 'Metal Detector',
                                                            'check_weigher_bag' => 'Check Weigher Bag',
                                                            'check_weigher_box' => 'Check Weigher Box',
                                                            'karton_sealer' => 'Karton Sealer'
                                                        ];
                                                    @endphp
                                                    
                                                    @foreach($mesinFields as $field => $label)
                                                        <div class="col-md-4 mb-3">
                                                            <div class="form-group">
                                                                <label for="{{ $field }}">{{ $label }}</label>
                                                                <select name="{{ $field }}" id="{{ $field }}" class="form-control">
                                                                    <option value="">-- Pilih --</option>
                                                                    @foreach(\App\Models\ProdukNonForming::getMesinOptions() as $key => $value)
                                                                        <option value="{{ $key }}" {{ old($field, $produkNonForming->$field) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
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
                                                        @php
                                                            $savedRuangan = is_array($produkNonForming->ruangan) ? collect($produkNonForming->ruangan)->keyBy('nama_ruangan') : collect();
                                                        @endphp
                                                        @foreach($inputAreas as $i => $area)
                                                            @php
                                                                $savedR    = $savedRuangan->get($area->area, []);
                                                                $savedKond = old('ruangan.'.$i.'.kondisi', $savedR['kondisi'] ?? []);
                                                                if (!is_array($savedKond)) $savedKond = [$savedKond];
                                                                $savedTind = old('ruangan.'.$i.'.tindakan_koreksi', $savedR['tindakan_koreksi'] ?? '');
                                                                $savedVerif = old('ruangan.'.$i.'.verifikasi', $savedR['verifikasi'] ?? '');
                                                                $isDihapus = (old('ruangan.'.$i.'.dihapus', $savedR['dihapus'] ?? '0') == '1');
                                                            @endphp
                                                            <tr class="{{ $isDihapus ? 'd-none' : '' }}" id="ruangan-row-{{ $i }}" data-nama="{{ $area->area }}">
                                                                <td class="text-center align-middle">{{ $i + 1 }}</td>
                                                                <td class="align-middle">
                                                                    <input type="hidden" name="ruangan[{{ $i }}][nama_ruangan]" value="{{ $area->area }}">
                                                                    <input type="hidden" class="input-dihapus" name="ruangan[{{ $i }}][dihapus]" value="{{ $isDihapus ? '1' : '0' }}">
                                                                    <strong>{{ $area->area }}</strong>
                                                                </td>
                                                                <td class="align-middle" style="min-width: 250px;">
                                                                    <div class="btn-group-toggle d-flex flex-column" data-toggle="buttons">
                                                                        @foreach(App\Models\ProdukForming::getMesinOptions() as $key => $value)
                                                                            @php $isChecked = in_array($key, $savedKond); @endphp
                                                                            <label class="btn btn-outline-secondary btn-sm mb-1 text-left {{ $isChecked ? 'active' : '' }}" style="white-space: normal; line-height: 1.2;">
                                                                                <input type="checkbox"
                                                                                       name="ruangan[{{ $i }}][kondisi][]"
                                                                                       value="{{ $key }}"
                                                                                       {{ $isChecked ? 'checked' : '' }}
                                                                                       {{ $isDihapus ? 'disabled' : '' }}
                                                                                       autocomplete="off"> {{ $value }}
                                                                            </label>
                                                                        @endforeach
                                                                    </div>
                                                                </td>
                                                                <td class="align-middle">
                                                                    <input type="text" class="form-control form-control-sm"
                                                                        name="ruangan[{{ $i }}][tindakan_koreksi]"
                                                                        value="{{ $savedTind }}"
                                                                        {{ $isDihapus ? 'disabled' : '' }}
                                                                        placeholder="Opsional">
                                                                </td>
                                                                <td class="text-center align-middle">
                                                                    <select class="form-control form-control-sm" name="ruangan[{{ $i }}][verifikasi]" {{ $isDihapus ? 'disabled' : '' }}>
                                                                        <option value="">-- Pilih --</option>
                                                                        <option value="✓" {{ $savedVerif == '✓' ? 'selected' : '' }}>Ok (✓)</option>
                                                                        <option value="✗" {{ $savedVerif == '✗' ? 'selected' : '' }}>Tidak Ok (✗)</option>
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
                                            <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3" placeholder="Masukkan keterangan atau catatan tambahan">{{ old('keterangan', $produkNonForming->keterangan) }}</textarea>
                                            @error('keterangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning btn-md">
                                    <i class="fas fa-save"></i> Update Data
                                </button>
                                <a href="{{ route('produk-non-forming.index') }}" class="btn btn-secondary btn-md">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let bahanPenunjangIndex = {{ $produkNonForming->bahan_penunjang ? count($produkNonForming->bahan_penunjang) : 1 }};

    // Function to load formulas
    async function loadFormulas(idProduk, selectedFormulaId = null) {
        const formulaSelect = $('#id_no_formula_non_forming');
        formulaSelect.html('<option value="">Pilih Nomor Formula</option>');
        
        if (!idProduk) return;

        try {
            const res = await fetch("{{ url('/qc-sistem/ajax/nomor-formula-non-forming-by-produk') }}/" + encodeURIComponent(idProduk), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            
            if (Array.isArray(data)) {
                data.forEach(function(item) {
                    const option = new Option(item.nomor_formula, item.id);
                    if (selectedFormulaId && item.id == selectedFormulaId) {
                        option.selected = true;
                    }
                    formulaSelect.append(option);
                });
            }
        } catch (error) {
            console.error("Gagal memuat formula:", error);
        }
    }

    // ==== Fitur Hapus / Batal Hapus baris (Mesin & Peralatan, Ruangan) ====
    // "prefix" diambil dari id baris: "mesin-row-0" -> "mesin", "ruangan-row-0" -> "ruangan"
    function toggleDeletedWrapper(prefix) {
        var $wrapper = $('#' + prefix + '-deleted-list-wrapper');
        var hasItems = $('#' + prefix + '-deleted-list').children().length > 0;
        $wrapper.toggle(hasItems);
    }

    function addDeletedBadge(prefix, rowId, nama) {
        var $badge = $(
            '<span class="badge badge-secondary p-2 mr-2 mb-2" data-row-id="' + rowId + '">' +
                $('<div>').text(nama).html() +
                ' <button type="button" class="btn btn-link btn-sm text-white p-0 ml-2 btn-batal-hapus" style="text-decoration:none;">' +
                    '<i class="fas fa-undo"></i> Batal' +
                '</button>' +
            '</span>'
        );
        $('#' + prefix + '-deleted-list').append($badge);
    }

    // Inisialisasi badge untuk baris yang sudah ditandai "dihapus" sejak sebelumnya (dari data tersimpan)
    $('tr.d-none[id^="mesin-row-"], tr.d-none[id^="ruangan-row-"]').each(function() {
        var $row = $(this);
        var rowId = $row.attr('id');
        var prefix = rowId.split('-')[0];
        var nama = $row.data('nama');
        addDeletedBadge(prefix, rowId, nama);
    });
    toggleDeletedWrapper('mesin');
    toggleDeletedWrapper('ruangan');

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

        addDeletedBadge(prefix, targetId, nama);
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

    // Validasi form sebelum submit
    $('form').on('submit', function(e) {
        let valid = true;
        let firstInvalid = null;

        // Cek semua baris mesin (lewati baris yang sudah ditandai "Hapus")
        $('.mesin-row').not('.d-none').each(function() {
            let checkboxes = $(this).find('input[type="checkbox"][name*="[kondisi]"]');
            if (checkboxes.length > 0) {
                if (checkboxes.filter(':checked').length === 0) {
                    valid = false;
                    // Highlight row
                    $(this).css('background-color', '#ffdddd');
                    if (!firstInvalid) {
                        firstInvalid = $(this);
                    }
                } else {
                    $(this).css('background-color', '');
                }
            }
        });

        if (!valid) {
            e.preventDefault(); // Hentikan submit form
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Mohon pilih minimal 1 kriteria penilaian (Kondisi) untuk setiap Mesin dan Peralatan.',
                });
            } else {
                alert('Peringatan: Mohon pilih minimal 1 kriteria penilaian (Kondisi) untuk setiap Mesin dan Peralatan.');
            }
            
            // Scroll ke error pertama
            if (firstInvalid) {
                $('html, body').animate({
                    scrollTop: firstInvalid.offset().top - 150
                }, 500);
            }
        }
    });
});
</script>
@endpush