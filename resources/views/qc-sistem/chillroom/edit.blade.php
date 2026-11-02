{{-- filepath: resources/views/qc-sistem/chillroom/edit.blade.php --}}
@extends('layouts.app')

@section('container')
@php
    $isQc = auth()->check() && auth()->user()->isQc();
    // PERBAIKAN TOTAL: Safe JSON decode dengan proper error handling
    
    // 1. Decode jumlah_rm (agregasi data)
    $jumlahRmDecoded = null;
    $entryData = [
        'berat_atas' => 0,
        'berat_std' => 0,
        'berat_bawah' => 0
    ];
    
    try {
        if (!empty($chillroom->jumlah_rm)) {
            $jumlahRmDecoded = json_decode($chillroom->jumlah_rm, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($jumlahRmDecoded)) {
                // Cari entry yang valid
                foreach ($jumlahRmDecoded as $key => $value) {
                    if (is_array($value)) {
                        $entryData['berat_atas'] = $value['berat_atas'] ?? 0;
                        $entryData['berat_std'] = $value['berat_std'] ?? 0;
                        $entryData['berat_bawah'] = $value['berat_bawah'] ?? 0;
                        break;
                    }
                }
            }
        }
    } catch (\Exception $e) {
        \Log::error('Error decoding jumlah_rm: ' . $e->getMessage());
    }
    
    // 2. Decode nilai_jumlah_rm (sampel berat)
    $beratSamples = [];
    
    try {
        if (!empty($chillroom->nilai_jumlah_rm)) {
            $nilaiJumlahRmDecoded = json_decode($chillroom->nilai_jumlah_rm, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($nilaiJumlahRmDecoded)) {
                // Cari array sampel
                foreach ($nilaiJumlahRmDecoded as $key => $value) {
                    if (is_array($value)) {
                        // Filter hanya nilai numerik
                        foreach ($value as $sample) {
                            if (is_numeric($sample)) {
                                $beratSamples[] = floatval($sample);
                            }
                        }
                        break;
                    }
                }
            }
        }
    } catch (\Exception $e) {
        \Log::error('Error decoding nilai_jumlah_rm: ' . $e->getMessage());
    }
    
    // Debug log
    \Log::info('Edit View - Parsed Data:', [
        'entryData' => $entryData,
        'beratSamples' => $beratSamples,
        'beratSamples_count' => count($beratSamples)
    ]);
@endphp

<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Pemeriksaan Kedatangan Bahan Baku</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('chillroom.index') }}">Chillroom</a></li>
                            <li class="breadcrumb-item active">Edit Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Form Edit Pemeriksaan Chillroom</h3>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger m-3">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('chillroom.update', $chillroom->uuid) }}" class="form-horizontal">
                                @csrf
                                @method('PUT')
                                
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- Informasi Dasar Card -->
                                            <div class="card card-primary card-outline">
                                                <div class="card-header">
                                                    <h3 class="card-title">Informasi Dasar</h3>
                                                </div>
                                                <div class="card-body">

                                                    <div class="form-group row">
                                                        <label for="tanggal" class="col-sm-3 col-form-label">
                                                            Tanggal <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="col-sm-9">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                                </div>
                                                                <input type="date"
                                                                    id="tanggal"
                                                                    name="tanggal"
                                                                    class="form-control @error('tanggal') is-invalid @enderror"
                                                                    value="{{ old('tanggal', $chillroom->tanggal ? \Carbon\Carbon::parse($chillroom->tanggal)->format('Y-m-d') : '') }}"
                                                                    required>
                                                            </div>
                                                            @error('tanggal')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="shift_id" class="col-sm-3 col-form-label">Shift <span class="text-danger">*</span></label>
                                                        <div class="col-sm-9">
                                                            <select class="form-control select2 @error('shift_id') is-invalid @enderror" 
                                                                    id="shift_id" name="shift_id" required>
                                                                <option value="">Pilih Shift</option>
                                                                @foreach($shifts as $shift)
                                                                    <option value="{{ $shift->id }}" {{ old('shift_id', $chillroom->shift_id) == $shift->id ? 'selected' : '' }}>
                                                                        {{ $shift->shift }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('shift_id')
                                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="jam_kedatangan" class="col-sm-3 col-form-label">Jam Kedatangan <span class="text-danger">*</span></label>
                                                        <div class="col-sm-9">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                                </div>
                                                                <input type="time" class="form-control @error('jam_kedatangan') is-invalid @enderror" 
                                                                       id="jam_kedatangan" name="jam_kedatangan" 
                                                                       value="{{ old('jam_kedatangan', $chillroom->jam_kedatangan ? \Carbon\Carbon::parse($chillroom->jam_kedatangan)->format('H:i') : '') }}" required>
                                                            </div>
                                                            @error('jam_kedatangan')
                                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Detail Bahan Baku Card -->
                                            <div class="card card-primary card-outline mt-3">
                                                <div class="card-header">
                                                    <h3 class="card-title">Detail Bahan Baku</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group row">
                                                        <label for="nama_rm" class="col-sm-3 col-form-label">Nama RM <span class="text-danger">*</span></label>
                                                        <div class="col-sm-9">
                                                            <select name="nama_rm" id="nama_rm" class="form-control select2 @error('nama_rm') is-invalid @enderror" required>
                                                                <option value="">Pilih Nama RM</option>
                                                                @foreach($dataRm as $rm)
                                                                    <option value="{{ $rm->nama_rm }}" {{ old('nama_rm', $chillroom->nama_rm) == $rm->nama_rm ? 'selected' : '' }}>{{ $rm->nama_rm }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('nama_rm')
                                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="kode_produksi" class="col-sm-3 col-form-label">Kode Produksi <span class="text-danger">*</span></label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control @error('kode_produksi') is-invalid @enderror" 
                                                                   id="kode_produksi" name="kode_produksi" 
                                                                   value="{{ old('kode_produksi', $chillroom->kode_produksi) }}" required>
                                                            @error('kode_produksi')
                                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    @if($chillroom->suhu == 0 && $chillroom->berat == '-')
                                                        {{-- Fields for Frozen: Only show RM Name & Prod Code --}}
                                                        <div class="alert alert-warning">
                                                            <i class="fas fa-info-circle"></i> <strong>Mode Sampling Frozen:</strong> Detail fisik (suhu, kemasan, dll) disembunyikan karena data ini adalah sampling frozen.
                                                        </div>
                                                        <input type="hidden" name="berat" value="-">
<<<<<<< HEAD
                                                        <input type="hidden" name="suhu" value="0">
                                                        <input type="hidden" name="sensori" value="✔">
=======
>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
                                                        <input type="hidden" name="kemasan" value="✔">
                                                    @else
                                                        <div class="form-group row">
                                                            <label for="berat" class="col-sm-3 col-form-label">Berat Perkemasan <span class="text-danger">*</span></label>
                                                            <div class="col-sm-9">
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control @error('berat') is-invalid @enderror" 
                                                                           id="berat" name="berat" 
                                                                           value="{{ old('berat', $chillroom->berat) }}" required>
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">kg</span>
                                                                    </div>
                                                                </div>
                                                                @error('berat')
                                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
<<<<<<< HEAD
                                                            <label for="suhu" class="col-sm-3 col-form-label">Suhu (°C) <span class="text-danger">*</span></label>
                                                            <div class="col-sm-9">
                                                                <div class="input-group">
                                                                    <input type="number" step="0.1" class="form-control @error('suhu') is-invalid @enderror" 
                                                                           id="suhu" name="suhu" 
                                                                           value="{{ old('suhu', $chillroom->suhu) }}" required>
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">°C</span>
                                                                    </div>
                                                                </div>
                                                                @error('suhu')
                                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="sensori" class="col-sm-3 col-form-label">Sensori <span class="text-danger">*</span></label>
                                                            <div class="col-sm-9">
                                                                <select class="form-control @error('sensori') is-invalid @enderror" 
                                                                        id="sensori" name="sensori" required>
                                                                    <option value="">Pilih Status Sensori</option>
                                                                    <option value="✔" {{ in_array(old('sensori', $chillroom->sensori), ['✔', '✓', 'OK', 'ok']) ? 'selected' : '' }}>✓ Oke</option>
                                                                    <option value="✘" {{ in_array(old('sensori', $chillroom->sensori), ['✘', 'X', 'x', 'tidak ok', 'Tidak OK', 'NG']) ? 'selected' : '' }}>✘ Tidak Oke</option>
                                                                </select>
                                                                @error('sensori')
                                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
=======
>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
                                                            <label for="kemasan" class="col-sm-3 col-form-label">Kemasan <span class="text-danger">*</span></label>
                                                            <div class="col-sm-9">
                                                                <select class="form-control @error('kemasan') is-invalid @enderror" 
                                                                        id="kemasan" name="kemasan" required>
                                                                    <option value="">Pilih Status Kemasan</option>
                                                                    <option value="✔" {{ in_array(old('kemasan', $chillroom->kemasan), ['✔', '✓', 'OK', 'ok']) ? 'selected' : '' }}>✓ Oke</option>
                                                                    <option value="✘" {{ in_array(old('kemasan', $chillroom->kemasan), ['✘', 'X', 'x', 'tidak ok', 'Tidak OK', 'NG']) ? 'selected' : '' }}>✘ Tidak Oke</option>
                                                                </select>
                                                                @error('kemasan')
                                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="form-group row">
                                                        <label for="keterangan" class="col-sm-3 col-form-label">Keterangan</label>
                                                        <div class="col-sm-9">
                                                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                                                      id="keterangan" name="keterangan" 
                                                                      rows="2">{{ old('keterangan', $chillroom->keterangan) }}</textarea>
                                                            @error('keterangan')
                                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <!-- Pemeriksaan RM Daging SPO Card -->
<<<<<<< HEAD
                                            <div class="card card-primary card-outline">
                                                <div class="card-header">
                                                    <h3 class="card-title">Sampling Berat RM Daging SPO</h3>
                                                </div>
                                                <div class="card-body">
=======
                                            <div class="card {{ $isQc ? 'card-secondary' : 'card-primary' }} card-outline">
                                                <div class="card-header">
                                                    <h3 class="card-title {{ $isQc ? 'text-muted' : '' }}">
                                                        <i class="fas fa-clipboard-check"></i> Sampling Berat RM Daging SPO
                                                        @if($isQc)
                                                            <span class="badge badge-secondary ml-2"><i class="fas fa-lock"></i> Readonly (Role QC)</span>
                                                        @endif
                                                    </h3>
                                                </div>
                                                <div class="card-body {{ $isQc ? 'sampling-spo-muted' : '' }}">
                                                    @if($isQc)
                                                        <div class="alert alert-light border small text-muted mb-3 py-1 px-2">
                                                            <i class="fas fa-info-circle mr-1"></i> Card sampling berat daging SPO dalam mode <strong>readonly</strong> untuk Role QC.
                                                        </div>
                                                    @endif

>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
                                                    <div class="form-group row">
                                                        <label for="standar_berat" class="col-sm-4 col-form-label">Standar Berat Per PCS</label>
                                                        <div class="col-sm-8">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control @error('standar_berat') is-invalid @enderror" 
                                                                       id="standar_berat" name="standar_berat" 
                                                                       value="{{ old('standar_berat', $chillroom->standar_berat) }}" 
<<<<<<< HEAD
                                                                       placeholder="Masukkan standar berat">
=======
                                                                       placeholder="{{ $isQc ? 'Readonly untuk Role QC' : 'Masukkan standar berat' }}"
                                                                       {{ $isQc ? 'readonly tabindex=-1 style=background-color:#e9ecef;cursor:not-allowed;' : '' }}>
>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">gr</span>
                                                                </div>
                                                            </div>
                                                            @error('standar_berat')
                                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="card card-light card-outline mt-3">
                                                        <div class="card-header">
                                                            <h6 class="card-title mb-0">
                                                                <i class="fas fa-balance-scale"></i> Hasil Aktual Berat Per PCS (gr)
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div id="berat-container-edit">
                                                                @if(count($beratSamples) > 0)
                                                                    @foreach($beratSamples as $idx => $sampleValue)
                                                                        <div class="form-group">
                                                                            <label>Berat Sample {{ $idx + 1 }} (gr)</label>
                                                                            <div class="input-group">
                                                                                <input type="number" step="0.01" class="form-control" 
                                                                                       name="berat_samples[]" 
                                                                                       value="{{ $sampleValue }}" 
<<<<<<< HEAD
                                                                                       placeholder="Masukkan berat sample" min="0">
=======
                                                                                       placeholder="Masukkan berat sample" min="0"
                                                                                       {{ $isQc ? 'readonly tabindex=-1 style=background-color:#e9ecef;cursor:not-allowed;' : '' }}>
                                                                                @if(!$isQc)
>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
                                                                                <div class="input-group-append">
                                                                                    @if($idx === 0)
                                                                                        <button type="button" class="btn btn-success btn-sm add-berat-btn">
                                                                                            <i class="fas fa-plus"></i>
                                                                                        </button>
                                                                                    @else
                                                                                        <button type="button" class="btn btn-danger btn-sm remove-berat-btn">
                                                                                            <i class="fas fa-minus"></i>
                                                                                        </button>
                                                                                    @endif
                                                                                </div>
<<<<<<< HEAD
=======
                                                                                @endif
>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    <div class="form-group">
                                                                        <label>Berat Sample 1 (gr)</label>
                                                                        <div class="input-group">
                                                                            <input type="number" step="0.01" class="form-control" 
                                                                                   name="berat_samples[]" 
<<<<<<< HEAD
                                                                                   placeholder="Masukkan berat sample" min="0">
=======
                                                                                   placeholder="{{ $isQc ? '-' : 'Masukkan berat sample' }}" min="0"
                                                                                   {{ $isQc ? 'readonly tabindex=-1 style=background-color:#e9ecef;cursor:not-allowed;' : '' }}>
                                                                            @if(!$isQc)
>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
                                                                            <div class="input-group-append">
                                                                                <button type="button" class="btn btn-success btn-sm add-berat-btn">
                                                                                    <i class="fas fa-plus"></i>
                                                                                </button>
                                                                            </div>
<<<<<<< HEAD
=======
                                                                            @endif
>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
<<<<<<< HEAD
                                                            <small class="form-text text-muted">Tambahkan sample berat sesuai kebutuhan</small>
=======
                                                            <small class="form-text text-muted">{{ $isQc ? 'Sampling berat dikelola oleh tim terkait' : 'Tambahkan sample berat sesuai kebutuhan' }}</small>
>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
                                                        </div>
                                                    </div>

                                                    <div class="card card-light card-outline mt-3">
                                                        <div class="card-header">
                                                            <h6 class="card-title mb-0">
                                                                <i class="fas fa-weight"></i> Jumlah yang Disampling
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="form-group row">
                                                                <label for="berat_atas" class="col-sm-5 col-form-label">Di Atas Standar (pcs)</label>
                                                                <div class="col-sm-7">
                                                                    <input type="number" class="form-control @error('berat_atas') is-invalid @enderror" 
                                                                           id="berat_atas" name="berat_atas" 
                                                                           value="{{ old('berat_atas', $entryData['berat_atas']) }}" 
<<<<<<< HEAD
                                                                           placeholder="Jumlah pcs" min="0">
=======
                                                                           placeholder="Jumlah pcs" min="0"
                                                                           {{ $isQc ? 'readonly tabindex=-1 style=background-color:#e9ecef;cursor:not-allowed;' : '' }}>
>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
                                                                    @error('berat_atas')
                                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label for="berat_std" class="col-sm-5 col-form-label">Sesuai Standar (pcs)</label>
                                                                <div class="col-sm-7">
                                                                    <input type="number" class="form-control @error('berat_std') is-invalid @enderror" 
                                                                           id="berat_std" name="berat_std" 
                                                                           value="{{ old('berat_std', $entryData['berat_std']) }}" 
<<<<<<< HEAD
                                                                           placeholder="Jumlah pcs" min="0">
=======
                                                                           placeholder="Jumlah pcs" min="0"
                                                                           {{ $isQc ? 'readonly tabindex=-1 style=background-color:#e9ecef;cursor:not-allowed;' : '' }}>
>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
                                                                    @error('berat_std')
                                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label for="berat_bawah" class="col-sm-5 col-form-label">Bawah Standar (pcs)</label>
                                                                <div class="col-sm-7">
                                                                    <input type="number" class="form-control @error('berat_bawah') is-invalid @enderror" 
                                                                           id="berat_bawah" name="berat_bawah" 
                                                                           value="{{ old('berat_bawah', $entryData['berat_bawah']) }}" 
<<<<<<< HEAD
                                                                           placeholder="Jumlah pcs" min="0">
=======
                                                                           placeholder="Jumlah pcs" min="0"
                                                                           {{ $isQc ? 'readonly tabindex=-1 style=background-color:#e9ecef;cursor:not-allowed;' : '' }}>
>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
                                                                    @error('berat_bawah')
                                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row mt-3">
                                                        <label for="status_rm" class="col-sm-3 col-form-label">Status</label>
                                                        <div class="col-sm-9">
<<<<<<< HEAD
                                                            <select class="form-control @error('status_rm') is-invalid @enderror" 
                                                                    id="status_rm" name="status_rm">
                                                                <option value="">Pilih Status</option>
                                                                <option value="diterima" {{ old('status_rm', $chillroom->status_rm) == 'diterima' ? 'selected' : '' }}>Diterima</option>
                                                                <option value="diretur" {{ old('status_rm', $chillroom->status_rm) == 'diretur' ? 'selected' : '' }}>Diretur</option>
                                                            </select>
                                                            @error('status_rm')
                                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                            @enderror
=======
                                                            @if($isQc)
                                                                <select class="form-control" id="status_rm" disabled tabindex="-1" style="background-color:#e9ecef;cursor:not-allowed;">
                                                                    <option value="">{{ $chillroom->status_rm ?: '-' }}</option>
                                                                </select>
                                                                <input type="hidden" name="status_rm" value="{{ old('status_rm', $chillroom->status_rm) }}">
                                                            @else
                                                                <select class="form-control @error('status_rm') is-invalid @enderror" 
                                                                        id="status_rm" name="status_rm">
                                                                    <option value="">Pilih Status</option>
                                                                    <option value="diterima" {{ old('status_rm', $chillroom->status_rm) == 'diterima' ? 'selected' : '' }}>Diterima</option>
                                                                    <option value="diretur" {{ old('status_rm', $chillroom->status_rm) == 'diretur' ? 'selected' : '' }}>Diretur</option>
                                                                </select>
                                                                @error('status_rm')
                                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                                @enderror
                                                            @endif
>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="catatan_rm" class="col-sm-3 col-form-label">Catatan</label>
                                                        <div class="col-sm-9">
                                                            <textarea class="form-control @error('catatan_rm') is-invalid @enderror" 
                                                                      id="catatan_rm" name="catatan_rm" 
<<<<<<< HEAD
                                                                      rows="3" placeholder="Masukkan catatan tambahan">{{ old('catatan_rm', $chillroom->catatan_rm) }}</textarea>
=======
                                                                      rows="3" placeholder="{{ $isQc ? 'Readonly untuk Role QC' : 'Masukkan catatan tambahan' }}"
                                                                      {{ $isQc ? 'readonly tabindex=-1 style=background-color:#e9ecef;cursor:not-allowed;' : '' }}>{{ old('catatan_rm', $chillroom->catatan_rm) }}</textarea>
>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
                                                            @error('catatan_rm')
                                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Update Data
                                    </button>
                                    <a href="{{ route('chillroom.index') }}" class="btn btn-secondary ml-2">
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
</div>

<<<<<<< HEAD
=======
@push('styles')
<style>
    .sampling-spo-muted {
        background-color: #f8f9fa;
        border-radius: 6px;
        opacity: 0.85;
    }
    .sampling-spo-muted input, 
    .sampling-spo-muted select, 
    .sampling-spo-muted textarea {
        background-color: #e9ecef !important;
        cursor: not-allowed !important;
        color: #6c757d !important;
    }
    .sampling-spo-muted .add-berat-btn,
    .sampling-spo-muted .remove-berat-btn {
        display: none !important;
    }
</style>
@endpush

>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
@push('scripts')
<script>
$(document).ready(function() {
    let sampleCounter = $('#berat-container-edit .form-group').length;
<<<<<<< HEAD
=======
    const isUserQc = {{ $isQc ? 'true' : 'false' }};
>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
    
    // Initialize Select2
    if ($.fn.select2) {
        $('.select2').select2({
            width: '100%'
        });
    }
    
    // Add new berat sample
    $(document).on('click', '.add-berat-btn', function() {
<<<<<<< HEAD
=======
        if (isUserQc) return;
>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
        sampleCounter++;
        
        const newSampleHtml = `
            <div class="form-group">
                <label>Berat Sample ${sampleCounter} (gr)</label>
                <div class="input-group">
                    <input type="number" step="0.01" class="form-control" name="berat_samples[]" 
                           placeholder="Masukkan berat sample" min="0">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-sm remove-berat-btn">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        $('#berat-container-edit').append(newSampleHtml);
    });
    
    // Remove berat sample
    $(document).on('click', '.remove-berat-btn', function() {
<<<<<<< HEAD
=======
        if (isUserQc) return;
>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
        const totalSamples = $('#berat-container-edit .form-group').length;
        
        if (totalSamples > 1) {
            $(this).closest('.form-group').remove();
            
            // Re-label all samples
            $('#berat-container-edit .form-group').each(function(index) {
                $(this).find('label').text(`Berat Sample ${index + 1} (gr)`);
            });
            
            sampleCounter = $('#berat-container-edit .form-group').length;
        } else {
            alert('Minimal harus ada 1 sample berat');
        }
    });
});
</script>
@endpush

@endsection