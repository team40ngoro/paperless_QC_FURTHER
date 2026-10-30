@extends('layouts.app')

@section('title', 'Edit Data Produk YUM')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Data KPI Produk YUM</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('produk-yum.index') }}">KPI Produk YUM</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <form action="{{ route('produk-yum.update', $produkYum->uuid) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
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
                                            <i class="fas fa-box mr-1"></i>Produk YUM
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_produk" id="id_produk_bag" class="form-control @error('id_produk') is-invalid @enderror" required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($produks as $item)
                                                <option value="{{ $item->id }}" data-produk-id="{{ $item->id }}" {{ old('id_produk', $produkYum->id_produk) == $item->id ? 'selected' : '' }}>
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
                                        <label for="id_data_bag">
                                            <i class="fas fa-shopping-bag mr-1"></i>Data Pack
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_data_bag" id="id_data_bag" class="form-control @error('id_data_bag') is-invalid @enderror" required>
                                            <option value="">-- Pilih Data Pack --</option>
                                            @foreach($dataBags as $bag)
                                                <option value="{{ $bag->id }}" {{ old('id_data_bag', $produkYum->id_data_bag) == $bag->id ? 'selected' : '' }}>
                                                    {{ $bag->std_bag }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_data_bag')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="shift_id">
                                            <i class="fas fa-clock mr-1"></i>Shift
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="shift_id" id="shift_id" class="form-control @error('shift_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Shift --</option>
                                            @foreach($shifts as $shift)
                                                <option value="{{ $shift->id }}" {{ old('shift_id', $produkYum->shift_id) == $shift->id ? 'selected' : '' }}>
                                                    {{ $shift->shift }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('shift_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kode_produksi">
                                            <i class="fas fa-barcode mr-1"></i>Kode Produksi
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="kode_produksi" id="kode_produksi" 
                                               class="form-control @error('kode_produksi') is-invalid @enderror" 
                                               value="{{ old('kode_produksi', $produkYum->kode_produksi) }}" 
                                               placeholder="Masukkan kode produksi" required>
                                        @error('kode_produksi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="kode_exp">
                                    <i class="fas fa-barcode"></i> Best Before
                                </label>
                                <input type="date" 
                                    class="form-control @error('kode_exp') is-invalid @enderror" 
                                    id="kode_exp" name="kode_exp" 
                                    value="{{ old('kode_exp', $produkYum->kode_exp) }}" 
                                    placeholder="Masukkan Best Before"
                                    required>
                                @error('kode_exp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal">
                                            <i class="fas fa-calendar mr-1"></i>Tanggal
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" name="tanggal" id="tanggal" 
                                               class="form-control @error('tanggal') is-invalid @enderror" 
                                               value="{{ old('tanggal', $produkYum->tanggal->format('Y-m-d')) }}" required>
                                        @error('tanggal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6" style="display: none;">
                                    <div class="form-group">
                                        <label for="jam">Jam</label>
                                        <input type="time" name="jam" id="jam" 
                                               class="form-control" 
                                               value="{{ old('jam', $produkYum->tanggal->format('H:i')) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- A. Aktual Berat -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-balance-scale mr-2"></i>A. Aktual Berat Per Pack (gram)
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row" id="aktual-berat-container">
                                @php
                                    $aktualBeratData = $produkYum->aktual_berat;
                                    if (is_string($aktualBeratData)) {
                                        $decoded = json_decode($aktualBeratData, true);
                                        $aktualBeratData = is_array($decoded) ? $decoded : (trim($aktualBeratData) !== '' ? explode(',', $aktualBeratData) : []);
                                    }
                                    if (!is_array($aktualBeratData) || count($aktualBeratData) == 0) {
                                        $aktualBeratData = [''];
                                    }
                                @endphp
                                @foreach($aktualBeratData as $index => $aktualBerat)
                                    <div class="col-md-4 mb-3 aktual-berat-item">
                                        <label>Aktual Berat Per Pack (gram) {{ $index + 1 }} </label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   name="aktual_berat[]" 
                                                   class="form-control" 
                                                   placeholder="Aktual Berat"
                                                   value="{{ old('aktual_berat.'.$index, $aktualBerat) }}"
                                                   required>
                                            <div class="input-group-append">
                                                @if($index == 0)
                                                    <button type="button" class="btn btn-success btn-sm add-aktual-berat">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-danger btn-sm remove-aktual-berat">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- B. Jumlah PCS -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-calculator mr-2"></i>B. Jumlah Isi per Pack (pcs)
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row" id="jumlah-pcs-container">
                                @php
                                    $jumlahPcsData = $produkYum->jumlah_pcs;
                                    if (is_string($jumlahPcsData)) {
                                        $decoded = json_decode($jumlahPcsData, true);
                                        $jumlahPcsData = is_array($decoded) ? $decoded : (trim($jumlahPcsData) !== '' ? explode(',', $jumlahPcsData) : []);
                                    }
                                    if (!is_array($jumlahPcsData) || count($jumlahPcsData) == 0) {
                                        $jumlahPcsData = [''];
                                    }
                                @endphp
                                @foreach($jumlahPcsData as $index => $jumlahPcs)
                                    <div class="col-md-4 mb-3 jumlah-pcs-item">
                                        <label>Jumlah Isi per Pack (pcs) {{ $index + 1 }}</label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   name="jumlah_pcs[]" 
                                                   class="form-control" 
                                                   placeholder="Jumlah PCS"
                                                   value="{{ old('jumlah_pcs.'.$index, $jumlahPcs) }}"
                                                   required>
                                            <div class="input-group-append">
                                                @if($index == 0)
                                                    <button type="button" class="btn btn-success btn-sm add-jumlah-pcs">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-danger btn-sm remove-jumlah-pcs">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- C. Berat PCS -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-weight mr-2"></i>C. Berat Per Potong (gram)
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row" id="berat-pcs-container">
                                @php
                                    $beratPcsData = $produkYum->berat_pcs;
                                    if (is_string($beratPcsData)) {
                                        $decoded = json_decode($beratPcsData, true);
                                        $beratPcsData = is_array($decoded) ? $decoded : (trim($beratPcsData) !== '' ? explode(',', $beratPcsData) : []);
                                    }
                                    if (!is_array($beratPcsData) || count($beratPcsData) == 0) {
                                        $beratPcsData = [''];
                                    }
                                @endphp
                                @foreach($beratPcsData as $index => $beratPcs)
                                    <div class="col-md-4 mb-3 berat-pcs-item">
                                        <label>Berat Per Potong (gram) {{ $index + 1 }}</label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   name="berat_pcs[]" 
                                                   class="form-control" 
                                                   placeholder="Berat PCS"
                                                   value="{{ old('berat_pcs.'.$index, $beratPcs) }}"
                                                   required>
                                            <div class="input-group-append">
                                                @if($index == 0)
                                                    <button type="button" class="btn btn-success btn-sm add-berat-pcs">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-danger btn-sm remove-berat-pcs">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="card">
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update Data
                            </button>
                            <a href="{{ route('produk-yum.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let beratPcsIndex = {{ is_array($produkYum->berat_pcs) ? count($produkYum->berat_pcs) : 1 }};
    let jumlahPcsIndex = {{ is_array($produkYum->jumlah_pcs) ? count($produkYum->jumlah_pcs) : 1 }};
    let aktualBeratIndex = {{ is_array($produkYum->aktual_berat) ? count($produkYum->aktual_berat) : 1 }};

    // Add Berat PCS
    $(document).on('click', '.add-berat-pcs', function() {
        const newItem = `
            <div class="col-md-4 mb-3 berat-pcs-item">
                <label>Berat PCS ${beratPcsIndex + 1} (gram)</label>
                <div class="input-group">
                    <input type="text" 
                           name="berat_pcs[${beratPcsIndex}]" 
                           class="form-control" 
                           placeholder="Berat PCS"
                           required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-sm remove-berat-pcs">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#berat-pcs-container').append(newItem);
        beratPcsIndex++;
    });

    // Remove Berat PCS
    $(document).on('click', '.remove-berat-pcs', function() {
        if ($('.berat-pcs-item').length > 1) {
            $(this).closest('.berat-pcs-item').remove();
        } else {
            alert('Minimal harus ada satu data berat PCS');
        }
    });

    // Add new jumlah PCS input
    $(document).on('click', '.add-jumlah-pcs', function() {
        const newItem = `
            <div class="col-md-4 mb-3 jumlah-pcs-item">
                <label>Jumlah PCS ${jumlahPcsIndex + 1}</label>
                <div class="input-group">
                    <input type="text" 
                           name="jumlah_pcs[${jumlahPcsIndex}]" 
                           class="form-control" 
                           placeholder="Jumlah PCS"
                           required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-sm remove-jumlah-pcs">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#jumlah-pcs-container').append(newItem);
        jumlahPcsIndex++;
    });

    // Remove Jumlah PCS
    $(document).on('click', '.remove-jumlah-pcs', function() {
        if ($('.jumlah-pcs-item').length > 1) {
            $(this).closest('.jumlah-pcs-item').remove();
        } else {
            alert('Minimal harus ada satu data jumlah PCS');
        }
    });

    // Add Aktual Berat
    $(document).on('click', '.add-aktual-berat', function() {
        const newItem = `
            <div class="col-md-4 mb-3 aktual-berat-item">
                <label>Aktual Berat ${aktualBeratIndex + 1} (gram)</label>
                <div class="input-group">
                    <input type="text" 
                           name="aktual_berat[${aktualBeratIndex}]" 
                           class="form-control" 
                           placeholder="Aktual Berat"
                           required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-sm remove-aktual-berat">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#aktual-berat-container').append(newItem);
        aktualBeratIndex++;
    });

    // Remove Aktual Berat
    $(document).on('click', '.remove-aktual-berat', function() {
        if ($('.aktual-berat-item').length > 1) {
            $(this).closest('.aktual-berat-item').remove();
        } else {
            alert('Minimal harus ada satu data aktual berat');
        }
    });

    // Form validation before submit
    $('form').on('submit', function(e) {
        // Check if all sections have at least one item
        if ($('.berat-pcs-item').length === 0) {
            e.preventDefault();
            alert('Minimal harus ada satu data berat PCS');
            return false;
        }
        
        if ($('.jumlah-pcs-item').length === 0) {
            e.preventDefault();
            alert('Minimal harus ada satu data jumlah PCS');
            return false;
        }
        
        if ($('.aktual-berat-item').length === 0) {
            e.preventDefault();
            alert('Minimal harus ada satu data aktual berat');
            return false;
        }

        // Check if all required fields are filled
        let hasEmptyFields = false;
        $('.berat-pcs-item input, .jumlah-pcs-item input, .aktual-berat-item input').each(function() {
            if ($(this).val().trim() === '') {
                hasEmptyFields = true;
                return false;
            }
        });

        if (hasEmptyFields) {
            e.preventDefault();
            alert('Semua field harus diisi');
            return false;
        }
    });
});
</script>
@endpush
@endsection