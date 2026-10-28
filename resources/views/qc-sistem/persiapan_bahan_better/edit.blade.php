{{-- filepath: resources/views/qc-sistem/persiapan_bahan_better/edit.blade.php --}}
@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-edit text-primary"></i>
                        Edit Data Verifikasi Pembuatan Better
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('persiapan-bahan-better.index') }}">Persiapan Bahan Better</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('persiapan-bahan-better.update', $item->uuid) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Card for Basic Information -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            Informasi Dasar
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
                                <div class="form-group">
                                    <label for="id_produk">
                                        <i class="fas fa-box text-info"></i>
                                        Nama Produk <span class="text-danger">*</span>
                                    </label>
                                    <select name="id_produk" id="id_produk" class="form-control select2" disabled>
                                        <option value="">Pilih Produk</option>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ $item->id_produk == $produk->id ? 'selected' : '' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {{-- Hidden field agar nilai tetap dikirim saat disabled --}}
                                    <input type="hidden" name="id_produk" value="{{ $item->id_produk }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_better">
                                        <i class="fas fa-flask text-success"></i>
                                        Jenis Better <span class="text-danger">*</span>
                                    </label>
                                    <select name="id_better" id="id_better" class="form-control" disabled>
                                        <option value="">Pilih Better</option>
                                        @foreach($betters as $better)
                                            <option value="{{ $better->id }}" {{ $item->id_better == $better->id ? 'selected' : '' }}>
                                                {{ $better->nama_better }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {{-- Hidden field agar nilai tetap dikirim saat disabled --}}
                                    <input type="hidden" name="id_better" value="{{ $item->id_better }}">
                                </div>
                            </div>
                        </div>
                        
                        {{-- //edit tanggal enable + jam --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kode_produksi_produk">
                                        <i class="fas fa-barcode text-warning"></i>
                                        Kode Produksi Produk <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        name="kode_produksi_produk"
                                        id="kode_produksi_produk"
                                        class="form-control"
                                        value="{{ $item->kode_produksi_produk }}"
                                        placeholder="Masukkan kode produksi produk"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tanggal">
                                        <i class="fas fa-calendar text-danger"></i>
                                        Tanggal <span class="text-danger">*</span>
                                    </label>

                                    <input type="date"
                                        id="tanggal"
                                        name="tanggal"
                                        class="form-control @error('tanggal') is-invalid @enderror"
                                        value="{{ old('tanggal', $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : '') }}" required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jam">
                                        <i class="fas fa-clock text-primary"></i>
                                        Jam <span class="text-danger">*</span>
                                    </label>

                                    <input type="time"
                                        name="jam"
                                        id="jam"
                                        class="form-control"
                                        value="{{ old('jam', $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '') }}"
                                        required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shift_id">
                                        <i class="fas fa-clock text-primary"></i>
                                        Shift <span class="text-danger">*</span>
                                    </label>
                                    <select name="shift_id" id="shift_id" class="form-control" required>
                                        <option value="">Pilih Shift</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ $item->shift_id == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->shift }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gramase">
                                        <i class="fas fa-weight text-secondary"></i>
                                        Gram <span class="text-danger">*</span> <small class="text-muted">(gram)</small>
                                    </label>
                                    <select name="gramase" id="gramase" class="form-control select2" required>
                                        <option value="">Pilih Gram</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="suhu_air">
                                        <i class="fas fa-thermometer-half text-info"></i>
                                        Suhu Air (0-10) (°C) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" name="suhu_air_global" id="global_suhu_air" 
                                           class="form-control" value="{{ $item->suhu_air ?? $item->better_rows[0]['suhu_air'] ?? '' }}" 
                                           placeholder="Masukkan Suhu Air" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card for Production Data -->
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line"></i>
                            Data Produksi
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $rows = old('better_rows', $item->better_rows ?? []);
                            if (!is_array($rows)) $rows = [];
                            if (count($rows) === 0) {
                                $rows = [[
                                    'master_nama_formula_better' => $item->better->nama_formula_better ?? null,
                                    'master_berat' => $item->better->berat ?? null,
                                    'kode_produksi_better' => $item->kode_produksi_better ?? null,
                                    'suhu_air' => $item->suhu_air ?? null,
                                    'sensori' => $item->sensori ?? null,
                                ]];
                            }
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="bg-gradient-success">
                                    <tr class="text-white">
                                        <th class="text-center" style="width: 40%">Nama Bahan</th>
                                        <th class="text-center" style="width: 30%">Kode Produksi <small><i>(tidak wajib isi)</i></small></th>
                                        <th class="text-center" style="width: 30%">Berat (kg)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $i => $r)
                                        @php
                                            $formulaVal = $r['master_nama_formula_better'] ?? '';
                                            $beratVal = $r['master_berat'] ?? '';
                                            $modeVal = $r['berat_mode'] ?? 'master';
                                            $beratLabel = ($beratVal !== '' && $beratVal !== null) ? ('Master (' . $beratVal . ')') : 'Master';
                                        @endphp
                                        <tr>
                                            <td>
                                                <textarea class="form-control form-control-sm" rows="1" style="resize:none; overflow:hidden;" readonly>{{ $formulaVal }}</textarea>
                                                <input type="hidden" name="better_rows[{{ $i }}][master_nama_formula_better]" value="{{ $formulaVal }}">
                                                <input type="hidden" name="better_rows[{{ $i }}][suhu_air]" class="row_suhu_air" value="{{ $item->suhu_air ?? $r['suhu_air'] ?? '' }}">
                                                <input type="hidden" name="better_rows[{{ $i }}][sensori]" class="row_sensori" value="{{ $item->sensori ?? $r['sensori'] ?? '✔' }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm" name="better_rows[{{ $i }}][kode_produksi_better]" value="{{ $r['kode_produksi_better'] ?? '' }}">
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend" style="min-width: 120px;">
                                                        <select class="form-control better-berat-mode" name="better_rows[{{ $i }}][berat_mode]">
                                                            <option value="master" {{ $modeVal === 'master' ? 'selected' : '' }}>{{ $beratLabel }}</option>
                                                            <option value="manual" {{ $modeVal === 'manual' ? 'selected' : '' }}>Manual</option>
                                                        </select>
                                                    </div>
                                                    <input type="number" step="0.01" class="form-control form-control-sm better-berat-input" name="better_rows[{{ $i }}][master_berat]" value="{{ $beratVal }}" data-master="{{ $beratVal }}" {{ $modeVal === 'manual' ? '' : 'readonly' }} required>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Tampilkan data STD & Aktual --}}
                @php
                    $aktualsList = $item->aktuals && $item->aktuals->count() ? $item->aktuals : collect([null]);
                @endphp
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i>
                            Detail STD & Aktual
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @foreach($aktualsList as $idx => $aktual)
                            <div class="row align-items-center mb-3">
                                <div class="col-md-3"><label class="mb-0">Standar Viscositas (s)</label></div>
                                <div class="col-md-3"><div class="d-flex align-items-center"><span class="mr-2 text-muted" style="font-size:0.85rem"><i>(otomatis)</i></span><input type="text" class="form-control form-control-sm bg-light" value="{{ $aktual->std->std_viskositas ?? '-' }}" readonly></div></div>
                                <div class="col-md-3"><label class="mb-0">Aktual Viscositas (s)</label></div>
                                <div class="col-md-3">
                                    <input type="hidden" name="id_std_salinitas_viskositas[]" value="{{ $aktual->id_std_salinitas_viskositas ?? '' }}">
                                    <input type="text" name="aktual_vis[]" class="form-control form-control-sm" value="{{ $aktual->aktual_vis ?? '' }}" {{ $aktual ? 'required' : '' }}>
                                </div>
                            </div>
                            
                            <div class="row align-items-center mb-3">
                                <div class="col-md-3"><label class="mb-0">Standar Salinity (%)</label></div>
                                <div class="col-md-3"><div class="d-flex align-items-center"><span class="mr-2 text-muted" style="font-size:0.85rem"><i>(otomatis)</i></span><input type="text" class="form-control form-control-sm bg-light" value="{{ $aktual->std->std_salinitas ?? '-' }}" readonly></div></div>
                                <div class="col-md-3"><label class="mb-0">Aktual Salinity (%)</label></div>
                                <div class="col-md-3"><input type="text" name="aktual_sal[]" class="form-control form-control-sm" value="{{ $aktual->aktual_sal ?? '' }}" {{ $aktual ? 'required' : '' }}></div>
                            </div>
                                
                            <div class="row align-items-center mb-3">
                                <div class="col-md-3"><label class="mb-0">Suhu Akhir (°C)</label></div>
                                <div class="col-md-9"><input type="text" name="aktual_suhu_air[]" class="form-control form-control-sm" value="{{ $aktual->aktual_suhu_air ?? '' }}" {{ $aktual ? 'required' : '' }}></div>
                            </div>
                            @if (!$loop->last)
                                <hr>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Card for Sensori -->
                <div class="card card-warning card-outline mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label class="mb-0 font-weight-bold">Sensori</label>
                            </div>
                            <div class="col-md-9">
                                @php $globalSens = $item->sensori ?? $item->better_rows[0]['sensori'] ?? '✔'; @endphp
                                <select class="form-control form-control-sm" id="global_sensori">
                                    <option value="✔" {{ $globalSens == '✔' ? 'selected' : '' }}>✔ OK</option>
                                    <option value="✘" {{ $globalSens == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-warning btn-md mr-3">
                                    <i class="fas fa-save"></i>
                                    Update Data
                                </button>
                                <a href="{{ route('persiapan-bahan-better.index') }}" class="btn btn-secondary btn-md">
                                    <i class="fas fa-arrow-left"></i>
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
            </form>
        </div>
    </section>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize select2 for gramase field
        $('#gramase').select2({
            placeholder: 'Pilih Gramase',
            allowClear: true
        });
        
        // Populate gramase dropdown on page load
        const $gramaseSelect = $('#gramase');
        const selectedGramase = {{ $item->gramase ?? 0 }};
        
        $gramaseSelect.empty();
        $gramaseSelect.append('<option value="">Pilih Gramase</option>');
        
        if (typeof nilaiBerat !== 'undefined' && nilaiBerat.length > 0) {
            $.each(nilaiBerat, function(i, val) {
                if (val !== '-') { // Skip the '-' value
                    const isSelected = (selectedGramase == val) ? 'selected' : '';
                    $gramaseSelect.append('<option value="' + val + '" ' + isSelected + '>' + val + ' gram</option>');
                }
            });
            
            // Trigger select2 to refresh with the selected value
            $gramaseSelect.trigger('change');
        }
        
        $(document).on('input', '#global_suhu_air', function() {
            $('.row_suhu_air').val($(this).val());
        });
        
        $(document).on('change', '#global_sensori', function() {
            $('.row_sensori').val($(this).val());
        });
    });
</script>
@endpush
@endsection