@extends('layouts.app')

@section('title', 'Tambah Data Produk YUM')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Data KPI Produk YUM</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('produk-yum.index') }}">KPI Produk YUM</a></li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <form action="{{ route('produk-yum.store') }}" method="POST">
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
                                            <i class="fas fa-box mr-1"></i>Produk YUM
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_produk" id="id_produk_bag" class="form-control @error('id_produk') is-invalid @enderror" required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($produks as $item)
                                                <option value="{{ $item->id }}" data-produk-id="{{ $item->id }}" {{ old('id_produk') == $item->id ? 'selected' : '' }}>
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
                                        <label for="data_bag">
                                            <i class="fas fa-shopping-bag mr-1"></i>Standar Berat/Pack
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_data_bag" id="id_data_bag" class="form-control @error('id_data_bag') is-invalid @enderror" required>
                                            <option value="">-- Pilih Data Pack --</option>
                                            @foreach($dataBags as $bag)
                                                <option value="{{ $bag->id }}" {{ old('id_data_bag') == $bag->id ? 'selected' : '' }}>
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kode_produksi">
                                            <i class="fas fa-barcode mr-1"></i>Kode Produksi
                                            <span class="text-danger">*</span>
                                        </label>
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
                                        <label for="jam">
                                            <i class="fas fa-clock mr-1"></i>Jam
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="time" name="jam" id="jam" 
                                            class="form-control @error('jam') is-invalid @enderror" 
                                            value="{{ old('jam', date('H:i')) }}" required>
                                        @error('jam')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kode_exp">
                                            <i class="fas fa-barcode mr-1"></i>Best Before
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" 
                                            name="kode_exp" 
                                            id="kode_exp" 
                                            class="form-control @error('kode_exp') is-invalid @enderror" 
                                            value="{{ old('kode_exp') }}" 
                                            placeholder="Masukkan Kode EXP"
                                            required>
                                        @error('kode_exp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- A. Aktual Berat -->
                    <div class="card card-info">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-balance-scale mr-2"></i>A. Aktual Berat Per Pack (gram)
                            </h3>
                            <button type="button" class="btn btn-sm btn-light ml-auto add-aktual-berat">
                                <i class="fas fa-plus mr-1"></i> Tambah Field
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row" id="aktual-berat-container">
                                @php
                                    $aktualBeratOld = old('aktual_berat', ['']);
                                    if (!is_array($aktualBeratOld) || count($aktualBeratOld) == 0) {
                                        $aktualBeratOld = [''];
                                    }
                                @endphp
                                @foreach ($aktualBeratOld as $i => $val)
                                    <div class="col-md-3 mb-3 aktual-berat-item">
                                        <label>Aktual Berat {{ $i + 1 }} (gram) @if($i == 0)<span class="text-danger">*</span>@endif</label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   name="aktual_berat[]" 
                                                   class="form-control" 
                                                   placeholder="Aktual Berat {{ $i + 1 }}"
                                                   value="{{ $val }}"
                                                   @if($i == 0) required @endif>
                                            @if($i > 0)
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-danger btn-sm remove-aktual-berat">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- B. Jumlah PCS -->
                    <div class="card card-info">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-calculator mr-2"></i>B. Jumlah Isi per Pack (pcs)
                            </h3>
                            <button type="button" class="btn btn-sm btn-light ml-auto add-jumlah-pcs">
                                <i class="fas fa-plus mr-1"></i> Tambah Field
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row" id="jumlah-pcs-container">
                                @php
                                    $jumlahPcsOld = old('jumlah_pcs', ['']);
                                    if (!is_array($jumlahPcsOld) || count($jumlahPcsOld) == 0) {
                                        $jumlahPcsOld = [''];
                                    }
                                @endphp
                                @foreach ($jumlahPcsOld as $i => $val)
                                    <div class="col-md-3 mb-3 jumlah-pcs-item">
                                        <label>Jumlah Isi {{ $i + 1 }} (pcs) @if($i == 0)<span class="text-danger">*</span>@endif</label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   name="jumlah_pcs[]" 
                                                   class="form-control" 
                                                   placeholder="Jumlah Isi {{ $i + 1 }}"
                                                   value="{{ $val }}"
                                                   @if($i == 0) required @endif>
                                            @if($i > 0)
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-danger btn-sm remove-jumlah-pcs">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- C. Berat PCS -->
                    <div class="card card-info">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-weight mr-2"></i>C. Berat Per Potong (gram)
                            </h3>
                            <button type="button" class="btn btn-sm btn-light ml-auto add-berat-pcs">
                                <i class="fas fa-plus mr-1"></i> Tambah Field
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row" id="berat-pcs-container">
                                @php
                                    $beratPcsOld = old('berat_pcs', ['']);
                                    if (!is_array($beratPcsOld) || count($beratPcsOld) == 0) {
                                        $beratPcsOld = [''];
                                    }
                                @endphp
                                @foreach ($beratPcsOld as $i => $val)
                                    <div class="col-md-3 mb-3 berat-pcs-item">
                                        <label>Berat Per Potong {{ $i + 1 }} (gram)</label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   name="berat_pcs[]" 
                                                   class="form-control" 
                                                   placeholder="Berat Potong {{ $i + 1 }}"
                                                   value="{{ $val }}">
                                            @if($i > 0)
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-danger btn-sm remove-berat-pcs">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="card">
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Data
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add Berat PCS
    $(document).on('click', '.add-berat-pcs', function() {
        const count = $('.berat-pcs-item').length + 1;
        const newItem = `
            <div class="col-md-3 mb-3 berat-pcs-item">
                <label>Berat Per Potong ${count} (gram)</label>
                <div class="input-group">
                    <input type="text" 
                           name="berat_pcs[]" 
                           class="form-control" 
                           placeholder="Berat Potong ${count}">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-sm remove-berat-pcs">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#berat-pcs-container').append(newItem);
    });

    // Remove Berat PCS
    $(document).on('click', '.remove-berat-pcs', function() {
        if ($('.berat-pcs-item').length > 1) {
            $(this).closest('.berat-pcs-item').remove();
        } else {
            alert('Minimal harus ada satu data berat PCS');
        }
    });

    // Add Jumlah PCS
    $(document).on('click', '.add-jumlah-pcs', function() {
        const count = $('.jumlah-pcs-item').length + 1;
        const newItem = `
            <div class="col-md-3 mb-3 jumlah-pcs-item">
                <label>Jumlah Isi ${count} (pcs)</label>
                <div class="input-group">
                    <input type="text" 
                           name="jumlah_pcs[]" 
                           class="form-control" 
                           placeholder="Jumlah Isi ${count}">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-sm remove-jumlah-pcs">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#jumlah-pcs-container').append(newItem);
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
        const count = $('.aktual-berat-item').length + 1;
        const newItem = `
            <div class="col-md-3 mb-3 aktual-berat-item">
                <label>Aktual Berat ${count} (gram)</label>
                <div class="input-group">
                    <input type="text" 
                           name="aktual_berat[]" 
                           class="form-control" 
                           placeholder="Aktual Berat ${count}">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-sm remove-aktual-berat">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#aktual-berat-container').append(newItem);
    });

    // Remove Aktual Berat
    $(document).on('click', '.remove-aktual-berat', function() {
        if ($('.aktual-berat-item').length > 1) {
            $(this).closest('.aktual-berat-item').remove();
        } else {
            alert('Minimal harus ada satu data aktual berat');
        }
    });
});
</script>
@endpush
