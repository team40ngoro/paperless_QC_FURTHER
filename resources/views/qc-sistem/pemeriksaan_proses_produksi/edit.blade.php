@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Pemeriksaan Proses Produksi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pemeriksaan-proses-produksi.index') }}">Pemeriksaan Proses Produksi</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
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
                            <h3 class="card-title">Form Edit Pemeriksaan Proses Produksi</h3>
                        </div>
                        <form action="{{ route('pemeriksaan-proses-produksi.update', $data->uuid) }}" method="POST">
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
                                    <!-- Area -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_area">Area <span class="text-danger">*</span></label>
                                            <select class="form-control @error('id_area') is-invalid @enderror" 
                                                    id="id_area" 
                                                    name="id_area" 
                                                    required>
                                                <option value="">Pilih Area</option>
                                                @foreach($areas as $area)
                                                    <option value="{{ $area->id }}" {{ (old('id_area') ?? $data->id_area) == $area->id ? 'selected' : '' }}>
                                                        {{ $area->area }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_area')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Shift -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="shift_id">Shift <span class="text-danger">*</span></label>
                                            <select class="form-control @error('shift_id') is-invalid @enderror" 
                                                    id="shift_id" 
                                                    name="shift_id" 
                                                    required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ (old('shift_id') ?? $data->shift_id) == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('shift_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Tanggal -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal & Waktu <span class="text-danger">*</span></label>
                                            <input type="datetime-local" 
                                                   class="form-control @error('tanggal') is-invalid @enderror" 
                                                   id="tanggal" 
                                                   name="tanggal" 
                                                   value="{{ old('tanggal', $data->tanggal->format('Y-m-d\TH:i')) }}" 
                                                   required>
                                            @error('tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Ketidaksesuaian -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ketidaksesuaian">Ketidaksesuaian <span class="text-danger">*</span></label>
                                            <select class="form-control @error('ketidaksesuaian') is-invalid @enderror" 
                                                    id="ketidaksesuaian" 
                                                    name="ketidaksesuaian" 
                                                    required>
                                                <option value="">Pilih Ketidaksesuaian</option>
                                                @foreach($ketidaksesuaianOptions as $key => $label)
                                                    <option value="{{ $key }}" {{ (old('ketidaksesuaian') ?? $data->ketidaksesuaian) == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('ketidaksesuaian')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Uraian Permasalahan -->
                                <div class="form-group">
                                    <label for="uraian_permasalahan">Uraian Permasalahan <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('uraian_permasalahan') is-invalid @enderror" 
                                              id="uraian_permasalahan" 
                                              name="uraian_permasalahan" 
                                              rows="3" 
                                              placeholder="Masukkan uraian permasalahan..."
                                              required>{{ old('uraian_permasalahan', $data->uraian_permasalahan) }}</textarea>
                                    @error('uraian_permasalahan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Analisa Penyebab -->
                                <div class="form-group">
                                    <label for="analisa_penyebab">Analisa Penyebab <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('analisa_penyebab') is-invalid @enderror" 
                                              id="analisa_penyebab" 
                                              name="analisa_penyebab" 
                                              rows="3" 
                                              placeholder="Masukkan analisa penyebab..."
                                              required>{{ old('analisa_penyebab', $data->analisa_penyebab) }}</textarea>
                                    @error('analisa_penyebab')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Disposisi -->
                                <div class="form-group">
                                    <label for="disposisi">Disposisi</label>
                                    @php
                                        $isManual = !array_key_exists($data->disposisi, $disposisiOptions) && !empty($data->disposisi);
                                    @endphp
                                    <div class="input-group">
                                        <select class="form-control @error('disposisi') is-invalid @enderror" 
                                                id="disposisi" 
                                                name="{{ $isManual ? '' : 'disposisi' }}"
                                                {{ $isManual ? 'disabled' : '' }}>
                                            <option value="">Pilih Disposisi</option>
                                            @foreach($disposisiOptions as $key => $label)
                                                <option value="{{ $key }}" {{ (old('disposisi') ?? $data->disposisi) == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" onclick="toggleManualDisposisi()">
                                                <i class="fas fa-plus"></i> Manual
                                            </button>
                                        </div>
                                    </div>
                                    <input type="text" 
                                        class="form-control mt-2 @error('disposisi') is-invalid @enderror" 
                                        id="disposisi_manual" 
                                        name="{{ $isManual ? 'disposisi' : 'disposisi_manual' }}" 
                                        placeholder="Masukkan disposisi manual" 
                                        style="{{ $isManual ? 'display: block;' : 'display: none;' }}" 
                                        value="{{ old('disposisi', $isManual ? $data->disposisi : '') }}"
                                        {{ $isManual ? 'required' : '' }}>
                                    @error('disposisi')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <script>
                                function toggleManualDisposisi() {
                                    var select = document.getElementById('disposisi');
                                    var input = document.getElementById('disposisi_manual');
                                    if (input.style.display === 'none') {
                                        input.style.display = 'block';
                                        input.required = true;
                                        select.disabled = true;
                                        select.name = '';
                                        input.name = 'disposisi';
                                    } else {
                                        input.style.display = 'none';
                                        input.required = false;
                                        select.disabled = false;
                                        select.name = 'disposisi';
                                        input.name = 'disposisi_manual';
                                        input.value = '';
                                    }
                                }
                                </script>
                                <!-- Tindakan Koreksi -->
                                <div class="form-group">
                                    <label for="tindakan_koreksi">Tindakan Koreksi <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('tindakan_koreksi') is-invalid @enderror" 
                                              id="tindakan_koreksi" 
                                              name="tindakan_koreksi" 
                                              rows="3" 
                                              placeholder="Masukkan tindakan koreksi..."
                                              required>{{ old('tindakan_koreksi', $data->tindakan_koreksi) }}</textarea>
                                    @error('tindakan_koreksi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update Data
                                </button>
                                <a href="{{ route('pemeriksaan-proses-produksi.index') }}" class="btn btn-secondary">
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