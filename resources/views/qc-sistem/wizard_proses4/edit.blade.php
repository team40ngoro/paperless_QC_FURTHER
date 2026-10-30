@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><i class="fas fa-edit text-warning"></i> Edit Verifikasi Proses Tumbling</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('wizard.proses4.index') }}">Proses Tumbling</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('wizard.proses4.show', $aging->uuid) }}">Detail</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible shadow-sm border-0">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible shadow-sm border-0">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fas fa-exclamation-triangle"></i> {{ session('error') }}
                    </div>
                @endif

                @php
                    // =====================================================
                    // RESOLVE DATA: $aging bisa ProsesAging atau stdClass
                    // Ambil semua nilai dengan fallback aman
                    // =====================================================

                    $isRealAging = ($aging instanceof \App\Models\ProsesAging);

                    // Resolve tanggal
                    $tanggalVal = null;
                    if (!empty($aging->tanggal)) {
                        $tanggalVal = \Carbon\Carbon::parse($aging->tanggal)->format('Y-m-d');
                    } elseif (!empty($aging->prosesTumbling->tanggal)) {
                        $tanggalVal = \Carbon\Carbon::parse($aging->prosesTumbling->tanggal)->format('Y-m-d');
                    } elseif (!empty($aging->bahanBaku->tanggal)) {
                        $tanggalVal = \Carbon\Carbon::parse($aging->bahanBaku->tanggal)->format('Y-m-d');
                    }

                    // Resolve jam
                    $jamVal = $aging->jam
                        ?? $aging->prosesTumbling->jam
                        ?? $aging->bahanBaku->jam
                        ?? '';

                    // Resolve produk name
                    $produkNama = $aging->produk->nama_produk
                        ?? $aging->prosesTumbling->produk->nama_produk
                        ?? 'N/A';

                    // Resolve kode produksi
                    $kodeProduksi = $aging->kode_produksi
                        ?? $aging->prosesTumbling->kode_produksi
                        ?? $aging->bahanBaku->kode_produksi
                        ?? '';

                    // Resolve shift
                    $bahanObj = $aging->bahanBaku ?? $aging->prosesTumbling->bahanBakuTumbling ?? null;
                    $shiftIdVal = $bahanObj->shift_id
                        ?? $aging->prosesTumbling->shift_id
                        ?? null;

                    // Resolve bahan baku uuid untuk query tumbling
                    $bahanUuid = $aging->bahan_baku_tumbling_uuid
                        ?? $aging->prosesTumbling->bahan_baku_tumbling_uuid
                        ?? null;
                @endphp

                <form action="{{ route('wizard.proses4.update', $aging->uuid) }}" method="POST" id="main-edit-form">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        {{-- ===== HEADER INFO ===== --}}
                        <div class="col-12 mb-3">
                            <div class="card bg-gradient-primary shadow-sm border-0">
                                <div class="card-body p-4">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h3 class="mb-1 font-weight-bold text-white">
                                                {{ $produkNama }}
                                            </h3>
                                            <div class="d-flex flex-wrap text-white" style="gap: 15px;">
                                                <div class="row w-100 g-2">
                                                    <div class="col-md-3">
                                                        <div class="form-group mb-0">
                                                            <label class="text-white small mb-1"><i class="fas fa-calendar mr-1"></i> Tanggal</label>
                                                            <input type="date" name="tanggal" class="form-control form-control-sm border-0 shadow-sm"
                                                                   value="{{ $tanggalVal }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group mb-0">
                                                            <label class="text-white small mb-1"><i class="fas fa-clock mr-1"></i> Jam</label>
                                                            <input type="time" name="jam" class="form-control form-control-sm border-0 shadow-sm"
                                                                   value="{{ $jamVal }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group mb-0">
                                                            <label class="text-white small mb-1"><i class="fas fa-layer-group mr-1"></i> Shift</label>
                                                            <select name="shift_id" class="form-control form-control-sm border-0 shadow-sm">
                                                                @foreach($shifts as $shift)
                                                                    <option value="{{ $shift->id }}" {{ $shiftIdVal == $shift->id ? 'selected' : '' }}>
                                                                        Shift {{ $shift->shift }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group mb-0">
                                                            <label class="text-white small mb-1"><i class="fas fa-barcode mr-1"></i> Kode Produksi</label>
                                                            <input type="text" name="kode_produksi" class="form-control form-control-sm border-0 shadow-sm text-uppercase"
                                                                   value="{{ $kodeProduksi }}" placeholder="KONTROL KODE">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">

                            {{-- LANGKAH 1: BAHAN BAKU --}}
                            @php
                                $bahan = $aging->bahanBaku ?? $aging->prosesTumbling->bahanBakuTumbling ?? null;
                                $manualData = $bahan ? $bahan->manual_bahan_data : null;
                            @endphp

                            @if($bahan)
                            <div class="card card-outline card-info mb-4 shadow-sm">
                                <div class="card-header bg-light">
                                    <h3 class="card-title"><i class="fas fa-boxes text-info mr-1"></i> Langkah 1: Bahan Baku Tumbling</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm text-center">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Bahan Baku</th>
                                                    <th>Kode Produksi</th>
                                                    <th>Jumlah (Kg)</th>
                                                    <th>Suhu (°C)</th>
                                                    <th>Kondisi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(!empty($manualData))
                                                    @foreach($manualData as $i => $material)
                                                    <tr>
                                                        <td>{{ $i + 1 }}</td>
                                                        <td class="text-left">
                                                            {{ $material['nama_bahan_baku'] ?? '-' }}
                                                            <input type="hidden" name="manual_bahan_data[{{ $i }}][nama_bahan_baku]" value="{{ $material['nama_bahan_baku'] ?? '' }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="manual_bahan_data[{{ $i }}][kode_produksi_bahan_baku]" class="form-control form-control-sm text-center" value="{{ $material['kode_produksi_bahan_baku'] ?? '' }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" step="any" name="manual_bahan_data[{{ $i }}][jumlah]" class="form-control form-control-sm text-center" value="{{ $material['jumlah'] ?? '' }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" step="any" name="manual_bahan_data[{{ $i }}][suhu]" class="form-control form-control-sm text-center" value="{{ $material['suhu'] ?? '' }}">
                                                        </td>
                                                        <td>
                                                            <select name="manual_bahan_data[{{ $i }}][kondisi_daging]" class="form-control form-control-sm">
                                                                <option value="✓" {{ ($material['kondisi_daging'] ?? '') == '✓' ? 'selected' : '' }}>✓ OK</option>
                                                                <option value="✘" {{ ($material['kondisi_daging'] ?? '') != '✓' ? 'selected' : '' }}>✘ NO</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr><td colspan="6" class="text-muted">Data bahan baku tidak ditemukan.</td></tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Gramase (g)</label>
                                                <input type="number" step="any" name="gramase" class="form-control" value="{{ $bahan->gramase ?? '' }}" placeholder="Gramase">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Salinity</label>
                                                <input type="text" name="salinity" class="form-control" value="{{ $bahan->salinity ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Brix</label>
                                                <input type="text" name="viskositas" class="form-control" value="{{ $bahan->viskositas ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Hasil Pencampuran</label>
                                                <select name="hasil_pencampuran" class="form-control select2-field">
                                                    <option value="✓" {{ ($bahan->hasil_pencampuran ?? '') == '✓' ? 'selected' : '' }}>✓ OK</option>
                                                    <option value="✘" {{ ($bahan->hasil_pencampuran ?? '') == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Keterangan</label>
                                                <textarea name="keterangan" class="form-control" rows="1">{{ $bahan->keterangan ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- LANGKAH 2: TUMBLING --}}
                            <div class="card card-outline card-primary mb-4 shadow-sm">
                                <div class="card-header bg-light">
                                    <h3 class="card-title"><i class="fas fa-sync text-primary mr-1"></i> Langkah 2: Proses Tumbling</h3>
                                </div>
                                <div class="card-body">
                                    @php
                                        $allTumbling = collect();
                                        if ($bahanUuid) {
                                            $allTumbling = \App\Models\ProsesTumbling::with(['dataTumbling'])
                                                ->where('bahan_baku_tumbling_uuid', $bahanUuid)
                                                ->get();
                                        }
                                    @endphp

                                    @if($allTumbling->count() > 0)
                                        @foreach($allTumbling as $tum)
                                        <div class="machine-group mb-5">
                                            <div class="bg-dark p-2 mb-3 rounded shadow-sm d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0 text-white font-weight-bold"><i class="fas fa-cog mr-2"></i> Mesin</h5>
                                            </div>

                                            <div class="row">
                                                {{-- TUMBLING VAKUM --}}
                                                <div class="col-md-6 mb-3">
                                                    <div class="card card-outline card-warning h-100 shadow-sm">
                                                        <div class="card-header"><h3 class="card-title font-weight-bold text-warning"><i class="fas fa-wind mr-1"></i> Tumbling Vakum</h3></div>
                                                        <div class="card-body p-0">
                                                            <table class="table table-sm table-striped mb-0 text-center">
                                                                <thead class="bg-light">
                                                                    <tr>
                                                                        <th>Parameter</th>
                                                                        <th>Standar</th>
                                                                        <th>Aktual</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="text-left font-weight-bold">Drum On</td>
                                                                        <td><input type="text" class="form-control form-control-sm text-center" value="{{ $tum->dataTumbling->drum_on ?? '-' }}" disabled></td>
                                                                        <td><input type="text" name="tumbling_data[{{ $tum->id }}][aktual_drum_on]" class="form-control form-control-sm text-center" value="{{ $tum->aktual_drum_on }}"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-left font-weight-bold">Drum Off</td>
                                                                        <td><input type="text" class="form-control form-control-sm text-center" value="{{ $tum->dataTumbling->drum_off ?? '-' }}" disabled></td>
                                                                        <td><input type="text" name="tumbling_data[{{ $tum->id }}][aktual_drum_off]" class="form-control form-control-sm text-center" value="{{ $tum->aktual_drum_off }}"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-left font-weight-bold">Drum Speed</td>
                                                                        <td><input type="text" class="form-control form-control-sm text-center" value="{{ $tum->dataTumbling->drum_speed ?? '-' }}" disabled></td>
                                                                        <td><input type="text" name="tumbling_data[{{ $tum->id }}][aktual_speed]" class="form-control form-control-sm text-center" value="{{ $tum->aktual_speed }}"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-left font-weight-bold">Total Waktu</td>
                                                                        <td><input type="text" class="form-control form-control-sm text-center" value="{{ $tum->dataTumbling->total_waktu ?? '-' }}" disabled></td>
                                                                        <td><input type="text" name="tumbling_data[{{ $tum->id }}][aktual_total_waktu]" class="form-control form-control-sm text-center" value="{{ $tum->aktual_total_waktu }}"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-left font-weight-bold">Tekanan Vakum</td>
                                                                        <td><input type="text" class="form-control form-control-sm text-center" value="{{ $tum->dataTumbling->tekanan_vakum ?? '-' }}" disabled></td>
                                                                        <td><input type="text" name="tumbling_data[{{ $tum->id }}][aktual_vakum]" class="form-control form-control-sm text-center" value="{{ $tum->aktual_vakum }}"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-left font-weight-bold">Mulai Tumbling</td>
                                                                        <td><span class="text-muted">-</span></td>
                                                                        <td><input type="text" name="tumbling_data[{{ $tum->id }}][waktu_mulai_tumbling]" class="form-control form-control-sm text-center" value="{{ $tum->waktu_mulai_tumbling }}"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-left font-weight-bold">Selesai Tumbling</td>
                                                                        <td><span class="text-muted">-</span></td>
                                                                        <td><input type="text" name="tumbling_data[{{ $tum->id }}][waktu_selesai_tumbling]" class="form-control form-control-sm text-center" value="{{ $tum->waktu_selesai_tumbling }}"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- TUMBLING NON VAKUM --}}
                                                <div class="col-md-6 mb-3">
                                                    <div class="card card-outline card-info h-100 shadow-sm">
                                                        <div class="card-header"><h3 class="card-title font-weight-bold text-info"><i class="fas fa-circle-notch mr-1"></i> Tumbling Non-Vakum</h3></div>
                                                        <div class="card-body p-0">
                                                            <table class="table table-sm table-striped mb-0 text-center">
                                                                <thead class="bg-light">
                                                                    <tr>
                                                                        <th>Parameter</th>
                                                                        <th>Standar</th>
                                                                        <th>Aktual</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="text-left font-weight-bold">Drum On</td>
                                                                        <td><input type="text" class="form-control form-control-sm text-center" value="{{ $tum->dataTumbling->drum_on_non_vakum ?? '-' }}" disabled></td>
                                                                        <td><input type="text" name="tumbling_data[{{ $tum->id }}][aktual_drum_on_non_vakum]" class="form-control form-control-sm text-center" value="{{ $tum->aktual_drum_on_non_vakum }}"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-left font-weight-bold">Drum Off</td>
                                                                        <td><input type="text" class="form-control form-control-sm text-center" value="{{ $tum->dataTumbling->drum_off_non_vakum ?? '-' }}" disabled></td>
                                                                        <td><input type="text" name="tumbling_data[{{ $tum->id }}][aktual_drum_off_non_vakum]" class="form-control form-control-sm text-center" value="{{ $tum->aktual_drum_off_non_vakum }}"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-left font-weight-bold">Drum Speed</td>
                                                                        <td><input type="text" class="form-control form-control-sm text-center" value="{{ $tum->dataTumbling->drum_speed_non_vakum ?? '-' }}" disabled></td>
                                                                        <td><input type="text" name="tumbling_data[{{ $tum->id }}][aktual_speed_non_vakum]" class="form-control form-control-sm text-center" value="{{ $tum->aktual_speed_non_vakum }}"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-left font-weight-bold">Total Waktu</td>
                                                                        <td><input type="text" class="form-control form-control-sm text-center" value="{{ $tum->dataTumbling->total_waktu_non_vakum ?? '-' }}" disabled></td>
                                                                        <td><input type="text" name="tumbling_data[{{ $tum->id }}][aktual_total_waktu_non_vakum]" class="form-control form-control-sm text-center" value="{{ $tum->aktual_total_waktu_non_vakum }}"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-left font-weight-bold">Tekanan</td>
                                                                        <td><input type="text" class="form-control form-control-sm text-center" value="{{ $tum->dataTumbling->tekanan_non_vakum ?? '-' }}" disabled></td>
                                                                        <td><input type="text" name="tumbling_data[{{ $tum->id }}][aktual_tekanan_non_vakum]" class="form-control form-control-sm text-center" value="{{ $tum->aktual_tekanan_non_vakum }}"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-left font-weight-bold">Mulai Tumbling</td>
                                                                        <td><span class="text-muted">-</span></td>
                                                                        <td><input type="text" name="tumbling_data[{{ $tum->id }}][waktu_mulai_tumbling_non_vakum]" class="form-control form-control-sm text-center" value="{{ $tum->waktu_mulai_tumbling_non_vakum }}"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-left font-weight-bold">Selesai Tumbling</td>
                                                                        <td><span class="text-muted">-</span></td>
                                                                        <td><input type="text" name="tumbling_data[{{ $tum->id }}][waktu_selesai_tumbling_non_vakum]" class="form-control form-control-sm text-center" value="{{ $tum->waktu_selesai_tumbling_non_vakum }}"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- HASIL AKHIR --}}
                                                <div class="col-12 mt-2">
                                                    <div class="card card-outline card-danger shadow-sm">
                                                        <div class="card-header"><h3 class="card-title font-weight-bold text-danger"><i class="fas fa-thermometer-half mr-1"></i> Hasil Akhir Tumbling</h3></div>
                                                        <div class="card-body py-3">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Suhu Hasil Tumbling (°C)</label>
                                                                        <input type="text" name="tumbling_data[{{ $tum->id }}][suhu_hasil_tumbling]" class="form-control" value="{{ $tum->suhu_hasil_tumbling }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Sensori Hasil Tumbling</label>
                                                                        <select name="tumbling_data[{{ $tum->id }}][sensori_hasil_tumbling]" class="form-control select2-field">
                                                                            <option value="oke" {{ ($tum->sensori_hasil_tumbling ?? '') == 'oke' ? 'selected' : '' }}>Oke</option>
                                                                            <option value="tidak ok" {{ ($tum->sensori_hasil_tumbling ?? '') == 'tidak ok' ? 'selected' : '' }}>Tidak Ok</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info text-center py-4">
                                            <i class="fas fa-info-circle fa-2x mb-3 d-block"></i>
                                            Data proses tumbling belum tersedia untuk produksi ini.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- LANGKAH 3: AGING --}}
                            <div class="card card-outline card-success mb-5 shadow-sm">
                                <div class="card-header bg-light">
                                    <h3 class="card-title"><i class="fas fa-clock text-success mr-1"></i> Langkah 3: Hasil Proses Aging</h3>
                                </div>
                                <div class="card-body">
                                    @php
                                        // Parse data aging jika ada
                                        $waktuMulaiArray = [];
                                        $suhuArray       = [];

                                        if (!empty($aging->waktu_mulai_aging)) {
                                            $waktuMulaiArray = array_map('trim', explode(',', $aging->waktu_mulai_aging));
                                        }
                                        if (!empty($aging->suhu_produk)) {
                                            $suhuArray = array_map('trim', explode(',', $aging->suhu_produk));
                                        }

                                        // Minimal 1 baris
                                        $maxCount = max(1, count($waktuMulaiArray), count($suhuArray));
                                    @endphp

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="font-weight-bold mb-3">Data Suhu, Kondisi & Waktu Mulai Aging </label>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm" id="suhu-kondisi-table">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th width="60px" class="text-center">No</th>
                                                                <th width="25%">Waktu Mulai Aging</th>
                                                                <th width="30%">Suhu Produk (°C)</th>
                                                                <th width="25%">Kondisi Produk</th>
                                                                <th width="100px" class="text-center">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @for($index = 0; $index < $maxCount; $index++)
                                                                @php
                                                                    $waktu      = $waktuMulaiArray[$index] ?? '';
                                                                    $suhuKondisi = $suhuArray[$index] ?? '';

                                                                    // Extract suhu dan kondisi dari format "5.5°C (✓)"
                                                                    preg_match('/^([\d.\-]+)°C\s*\((.*?)\)$/', $suhuKondisi, $matches);
                                                                    $suhu    = $matches[1] ?? '';
                                                                    $kondisi = trim($matches[2] ?? '✓');
                                                                @endphp
                                                                <tr class="suhu-kondisi-row">
                                                                    <td class="text-center row-number">{{ $index + 1 }}</td>
                                                                    <td>
                                                                        <input type="text" name="waktu_mulai_aging[]" class="form-control form-control-sm" placeholder="Contoh: 08:00" value="{{ $waktu }}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" step="any" name="suhu_produk[]" class="form-control form-control-sm" placeholder="Contoh: 5.5" value="{{ $suhu }}">
                                                                    </td>
                                                                    <td>
                                                                        <select name="kondisi_produk[]" class="form-control form-control-sm">
                                                                            <option value="✓" {{ $kondisi == '✓' ? 'selected' : '' }}>✓ OK</option>
                                                                            <option value="✘" {{ $kondisi == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                                                        </select>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if($index === 0)
                                                                            <button type="button" class="btn btn-success btn-sm btn-add-suhu">
                                                                                <i class="fas fa-plus"></i>
                                                                            </button>
                                                                        @else
                                                                            <button type="button" class="btn btn-danger btn-sm btn-remove-suhu">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endfor
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <small class="text-muted">Klik tombol <i class="fas fa-plus text-success"></i> untuk menambah data</small>

                                                <div class="col-md-6 mt-2 offset-md-3 mb-0">
                                                    <label class="font-weight-bold text-success"><i class="fas fa-clock"></i> Waktu Selesai Aging</label>
                                                    <input type="text" name="waktu_selesai_aging" class="form-control" placeholder="Contoh: 16:00" value="{{ $aging->waktu_selesai_aging ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="card bg-light shadow-sm border-success">
                                                <div class="card-body">
                                                    <div class="text-right">
                                                        <a href="{{ route('wizard.proses4.show', $aging->uuid) }}" class="btn btn-secondary">
                                                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                                                        </a>
                                                        <button type="submit" class="btn btn-primary ml-2">
                                                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
    $('.select2-field').select2({
        theme: 'bootstrap4',
        width: '100%',
        minimumResultsForSearch: Infinity
    });

    $('#main-edit-form').on('submit', function(e) {
        if (!confirm('Apakah Anda yakin ingin menyimpan semua perubahan data ini?')) {
            e.preventDefault();
        }
    });

    // Add Suhu Row
    $(document).on('click', '.btn-add-suhu', function() {
        const rowCount = $('#suhu-kondisi-table tbody tr').length + 1;
        const newRow = `
            <tr class="suhu-kondisi-row">
                <td class="text-center row-number">${rowCount}</td>
                <td>
                    <input type="text" name="waktu_mulai_aging[]" class="form-control form-control-sm" placeholder="Contoh: 08:00">
                </td>
                <td>
                    <input type="number" step="any" name="suhu_produk[]" class="form-control form-control-sm" placeholder="Contoh: 5.5">
                </td>
                <td>
                    <select name="kondisi_produk[]" class="form-control form-control-sm">
                        <option value="✓">✓ OK</option>
                        <option value="✘">✘ Tidak OK</option>
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-suhu">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#suhu-kondisi-table tbody').append(newRow);
        updateRowNumbers();
    });

    // Remove Suhu Row
    $(document).on('click', '.btn-remove-suhu', function() {
        const rowCount = $('#suhu-kondisi-table tbody tr').length;
        if (rowCount > 1) {
            $(this).closest('tr').remove();
            updateRowNumbers();
        } else {
            alert('Minimal harus ada 1 data');
        }
    });

    function updateRowNumbers() {
        $('#suhu-kondisi-table tbody tr').each(function(index) {
            $(this).find('.row-number').text(index + 1);
        });
    }
});
</script>
@endpush