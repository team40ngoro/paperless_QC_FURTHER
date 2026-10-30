@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Produk Forming</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="">Produk Forming</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Terjadi Kesalahan!</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
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
            </div>
                
            <form action="{{ route('produk-forming.update', $produkForming->uuid) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Informasi Dasar -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>Informasi Dasar
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                    <!-- Tanggal -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('tanggal') is-invalid @enderror" 
                                    id="tanggal" name="tanggal" 
                                    value="{{ old('tanggal', \Carbon\Carbon::parse($produkForming->tanggal)->format('Y-m-d\TH:i')) }}" required>
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
                                            {{ old('id_produk', $produkForming->id_produk) == $produk->id ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_produk')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Shift -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_shift">Shift <span class="text-danger">*</span></label>
                            <select class="form-control @error('id_shift') is-invalid @enderror" 
                                    id="id_shift" name="id_shift" required>
                                <option value="">Pilih Shift</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}" 
                                            {{ old('id_shift', $produkForming->id_shift) == $shift->id ? 'selected' : '' }}>
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
                                        $allBahan = array_merge(
                                            $produkForming->bahan_baku ?? [],
                                            $produkForming->bahan_penunjang ?? []
                                        );
                                        
                                        if (count($allBahan) == 0) {
                                            $allBahan = [
                                                ['nama' => 'Sisa Bahan Baku']
                                            ];
                                        }
                                    @endphp
                                    @foreach($allBahan as $i => $bahan)
                                        <input type="hidden" name="bahan_baku[{{ $i }}][nama]" value="{{ $bahan['nama'] ?? '' }}">
                                        <tr>
                                            <td class="text-center align-middle">{{ $i + 1 }}</td>
                                            <td class="align-middle">
                                                <strong>{{ $bahan['nama'] ?? '-' }}</strong>
                                            </td>
                                            <td class="align-middle" style="min-width: 250px;">
                                                <div class="btn-group-toggle d-flex flex-column" data-toggle="buttons">
                                                    @foreach(App\Models\ProdukForming::getBahanOptions() as $key => $value)
                                                        @php
                                                            // Get existing data array
                                                            $oldPenilaian = old('bahan_baku.'.$i.'.penilaian', $bahan['penilaian'] ?? []);
                                                            if (!is_array($oldPenilaian)) {
                                                                $oldPenilaian = [$oldPenilaian];
                                                            }
                                                            $isChecked = in_array($key, $oldPenilaian);
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
                                                    value="{{ old('bahan_baku.'.$i.'.tindakan_koreksi', $bahan['tindakan_koreksi'] ?? '') }}"
                                                    placeholder="Opsional">
                                            </td>
                                            <td class="text-center align-middle">
                                                <select class="form-control form-control-sm" name="bahan_baku[{{ $i }}][verifikasi]">
                                                    <option value="">-- Pilih --</option>
                                                    <option value="✓" {{ old('bahan_baku.'.$i.'.verifikasi', $bahan['verifikasi'] ?? '') == '✓' ? 'selected' : '' }}>Ok (✓)</option>
                                                    <option value="✗" {{ old('bahan_baku.'.$i.'.verifikasi', $bahan['verifikasi'] ?? '') == '✗' ? 'selected' : '' }}>Tidak Ok (✗)</option>
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
                                            $savedKem = old($kItem['field'], $produkForming->{$kItem['field']} ?? []);
                                            if (!is_array($savedKem)) $savedKem = $savedKem ? [$savedKem] : [];
                                            $savedKemTindakan = old($kItem['field'].'_tindakan', $produkForming->{$kItem['field'].'_tindakan'} ?? '');
                                            $savedKemVerif = old($kItem['field'].'_verifikasi', $produkForming->{$kItem['field'].'_verifikasi'} ?? '');
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
                            if (!empty(old('mesin_peralatan', $produkForming->mesin_peralatan))) {
                                $mesinData = old('mesin_peralatan', $produkForming->mesin_peralatan);
                                $isNewFormat = true;
                            } elseif (!empty($produkForming->penilaian) && is_array($produkForming->penilaian)) {
                                $mesinData = $produkForming->penilaian;
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
                                            @endphp
                                            <input type="hidden" name="mesin_peralatan[{{ $i }}][nama_mesin]" value="{{ $namaMesin }}">
                                            <input type="hidden" class="input-dihapus" name="mesin_peralatan[{{ $i }}][dihapus]" value="0">
                                            <tr class="mesin-row" id="mesin-row-{{ $i }}">
                                                <td class="text-center align-middle">{{ $i + 1 }}</td>
                                                <td class="align-middle">
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
                                                        placeholder="Opsional">
                                                </td>
                                                <td class="text-center align-middle">
                                                    <select class="form-control form-control-sm" name="mesin_peralatan[{{ $i }}][verifikasi]">
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
                                            'autogrind' => 'Autogrind',
                                            'bowlcutter' => 'Bowlcutter',
                                            'ayakan_seasoning' => 'Ayakan Seasoning',
                                            'unimix' => 'Unimix',
                                            'revoformer' => 'Revoformer',
                                            'better_mixer' => 'Better Mixer',
                                            'wet_coater' => 'Wet Coater',
                                            'breader' => 'Breader',
                                            'frayer_1' => 'Frayer 1',
                                            'frayer_2' => 'Frayer 2',
                                            'iqf_jbt' => 'IQF JBT',
                                            'keranjang' => 'Keranjang',
                                            'timbangan' => 'Timbangan',
                                            'mhw' => 'MHW',
                                            'foot_sealer' => 'Foot Sealer',
                                            'metal_detector' => 'Metal Detector',
                                            'rotary_table' => 'Rotary Table',
                                            'carton_sealer' => 'Carton Sealer',
                                            'meatcar' => 'Meatcar',
                                            'check_weigher_bag' => 'Check Weigher Bag',
                                            'check_weigher_box' => 'Check Weigher Box'
                                        ];
                                    @endphp
                                    
                                    @foreach($mesinFields as $field => $label)
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="{{ $field }}">{{ $label }} (3-8)</label>
                                                <select name="{{ $field }}" id="{{ $field }}" class="form-control">
                                                    <option value="">-- Pilih Penilaian --</option>
                                                    @foreach(App\Models\ProdukForming::getMesinOptions() as $key => $value)
                                                        <option value="{{ $key }}" {{ old($field, $produkForming->$field) == $key ? 'selected' : '' }}>{{ $value }}</option>
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
                                            $savedRuangan = is_array($produkForming->ruangan) ? collect($produkForming->ruangan)->keyBy('nama_ruangan') : collect();
                                        @endphp
                                        @foreach($inputAreas as $i => $area)
                                            @php
                                                $savedR    = $savedRuangan->get($area->area, []);
                                                $savedKond = old('ruangan.'.$i.'.kondisi', $savedR['kondisi'] ?? []);
                                                if (!is_array($savedKond)) $savedKond = [$savedKond];
                                                $savedTind = old('ruangan.'.$i.'.tindakan_koreksi', $savedR['tindakan_koreksi'] ?? '');
                                                $savedVerif = old('ruangan.'.$i.'.verifikasi', $savedR['verifikasi'] ?? '');
                                            @endphp
                                            <input type="hidden" name="ruangan[{{ $i }}][nama_ruangan]" value="{{ $area->area }}">
                                            <input type="hidden" class="input-dihapus" name="ruangan[{{ $i }}][dihapus]" value="0">
                                            <tr id="ruangan-row-{{ $i }}">
                                                <td class="text-center align-middle">{{ $i + 1 }}</td>
                                                <td class="align-middle"><strong>{{ $area->area }}</strong></td>
                                                <td class="align-middle" style="min-width: 250px;">
                                                    <div class="btn-group-toggle d-flex flex-column" data-toggle="buttons">
                                                        @foreach(App\Models\ProdukForming::getMesinOptions() as $key => $value)
                                                            @php $isChecked = in_array($key, $savedKond); @endphp
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
                                                        value="{{ $savedTind }}"
                                                        placeholder="Opsional">
                                                </td>
                                                <td class="text-center align-middle">
                                                    <select class="form-control form-control-sm" name="ruangan[{{ $i }}][verifikasi]">
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
                            <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3" placeholder="Masukkan keterangan atau catatan tambahan">{{ old('keterangan', $produkForming->keterangan) }}</textarea>
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
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save mr-1"></i>Update Data
                                </button>
                                <a href="{{ route('produk-forming.index') }}" class="btn btn-secondary ml-2">
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Validasi form sebelum submit
    $('form').on('submit', function(e) {
        let valid = true;
        let firstInvalid = null;

        // Cek semua baris mesin
        $('.mesin-row').each(function() {
            // Lewati baris yang sudah ditandai "Hapus" oleh user — tidak perlu diisi kondisinya
            if ($(this).hasClass('d-none')) {
                return;
            }
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
        $row.css('background-color', ''); // bersihkan highlight merah dari validasi lama, jika ada
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