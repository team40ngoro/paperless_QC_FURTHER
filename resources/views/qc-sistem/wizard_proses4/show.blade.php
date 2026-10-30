@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><i class="fas fa-magic text-primary"></i> Detail Verifikasi Proses Tumbling</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('wizard.proses4.index') }}">Proses Tumbling</a></li>
                            <li class="breadcrumb-item active">Detail</li>
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

                <div class="row">

                    {{-- ===== HEADER INFO ===== --}}
                    <div class="col-12 mb-3">
                        <div class="card bg-gradient-primary shadow-sm border-0">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h3 class="mb-1 font-weight-bold text-white">
                                            {{ $aging->produk->nama_produk ?? 'N/A' }}
                                        </h3>
                                        <div class="d-flex flex-wrap" style="gap: 15px;">
                                            @php
                                                $bahanObj = $aging->bahanBaku;
                                                if (!$bahanObj && $aging->prosesTumbling) {
                                                    $bahanObj = $aging->prosesTumbling->bahanBakuTumbling;
                                                }
                                                $shiftVal = $bahanObj->shift->shift ?? ($aging->prosesTumbling->shift->shift ?? 'N/A');
                                            @endphp
                                            <span class="badge badge-light p-2">
                                                <i class="fas fa-calendar mr-1"></i>
                                                {{ \Carbon\Carbon::parse($aging->tanggal)->format('d F Y') }}
                                            </span>
                                            <span class="badge badge-light p-2">
                                                <i class="fas fa-clock mr-1"></i> {{ $aging->jam }}
                                            </span>
                                            <span class="badge badge-light p-2">
                                                <i class="fas fa-layer-group mr-1"></i>Shift {{ $shiftVal }}
                                            </span>
                                            <span class="badge badge-light p-2">
                                                <i class="fas fa-barcode mr-1"></i>
                                                Kode Produksi: {{ $aging->kode_produksi ?? ($aging->prosesTumbling->kode_produksi ?? '-') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                                        {{-- Tombol Edit hanya muncul jika aging sudah ada (bukan dummy) --}}
                                        @if(!($aging->is_dummy_aging ?? false) && !$aging->approved_by_qc && !$aging->approved_by_produksi && !$aging->approved_by_spv)
                                            @php
                                                $canEditWp4Show = true;
                                                $restrictedRoles = config('edit_limit.restricted_role_ids', []);
                                                if (in_array(auth()->user()->id_role, $restrictedRoles)) {
                                                    $timeLimitHours = config('edit_limit.time_limit_hours', 8);
                                                    $createdAt = \Carbon\Carbon::parse($aging->created_at);
                                                    $hoursElapsed = $createdAt->diffInHours(now(), true);
                                                    if ($hoursElapsed > $timeLimitHours) {
                                                        $canEditWp4Show = false;
                                                    }
                                                }
                                            @endphp
                                            @if($canEditWp4Show)
                                                <a href="{{ route('wizard.proses4.edit', $aging->uuid) }}"
                                                    class="btn btn-warning btn-sm mr-2 text-white">
                                                    <i class="fas fa-edit mr-1"></i> Edit Data
                                                </a>
                                            @else
                                                <span class="btn btn-secondary btn-sm mr-2 disabled"
                                                    title="Waktu edit sudah habis (batas 8 jam dari pembuatan data)">
                                                    <i class="fas fa-lock mr-1"></i> Edit Data
                                                    <small class="text-danger">(Expired)</small>
                                                </span>
                                            @endif
                                        @endif
                                        <a href="{{ route('wizard.proses4.index') }}" class="btn btn-light btn-sm mr-2">
                                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">

                        {{-- ===== LANGKAH 1: BAHAN BAKU ===== --}}
                        <div class="card card-outline card-info mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title">
                                    <i class="fas fa-boxes text-info mr-1"></i> Langkah 1: Bahan Baku Tumbling
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                @php
                                    $bahan = $aging->bahanBaku;
                                    if (!$bahan && $aging->prosesTumbling) {
                                        $bahan = $aging->prosesTumbling->bahanBakuTumbling;
                                    }
                                    $manualData = $bahan ? $bahan->manual_bahan_data : null;
                                @endphp
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
                                            @if($bahan && !empty($manualData))
                                                @foreach($manualData as $i => $material)
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td class="text-left">{{ $material['nama_bahan_baku'] ?? '-' }}</td>
                                                    <td>{{ $material['kode_produksi_bahan_baku'] ?? '-' }}</td>
                                                    <td>{{ $material['jumlah'] ?? '-' }}</td>
                                                    <td>{{ $material['suhu'] ?? '-' }} °C</td>
                                                    <td>
                                                        @if(($material['kondisi_daging'] ?? '') == '✓')
                                                            <span class="text-success font-weight-bold">✓</span>
                                                        @else
                                                            <span class="text-danger font-weight-bold">
                                                                {{ $material['kondisi_daging'] ?? '✘' }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-muted">Data bahan baku tidak ditemukan.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <strong>Gramase:</strong>
                                        <span class="badge badge-secondary ml-2">{{ $bahan->gramase ? $bahan->gramase . ' g' : '-' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Salinity:</strong>
                                        <span class="badge badge-secondary ml-2">{{ $bahan->salinity ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Brix:</strong>
                                        <span class="badge badge-secondary ml-2">{{ $bahan->viskositas ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Hasil Pencampuran:</strong>
                                        <span class="badge badge-secondary ml-2">{{ $bahan->hasil_pencampuran ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ===== LANGKAH 2: PROSES TUMBLING ===== --}}
                        <div class="card card-outline card-primary mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title">
                                    <i class="fas fa-sync text-primary mr-1"></i> Langkah 2: Proses Tumbling
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                @php
                                    $bahanUuid = $aging->bahan_baku_tumbling_uuid
                                        ?? ($aging->prosesTumbling->bahan_baku_tumbling_uuid ?? null);

                                    $allTumbling = \App\Models\ProsesTumbling::with(['dataTumbling'])
                                        ->where('bahan_baku_tumbling_uuid', $bahanUuid)
                                        ->get();
                                @endphp

                                @if($allTumbling->count() > 0)
                                    @foreach($allTumbling as $tum)
                                        @php
                                            /*
                                             * Deteksi mode berdasarkan data yang tersimpan:
                                             *   - hasVakum    = salah satu field vakum terisi
                                             *   - hasNonVakum = salah satu field non-vakum terisi
                                             * Jika keduanya terisi  → tampilkan keduanya (col-md-6 | col-md-6)
                                             * Jika hanya satu terisi → tampilkan full width (col-md-12)
                                             */
                                            $hasVakum = !empty($tum->aktual_drum_on)
                                                || !empty($tum->aktual_drum_off)
                                                || !empty($tum->aktual_speed)
                                                || !empty($tum->aktual_total_waktu)
                                                || !empty($tum->aktual_vakum)
                                                || !empty($tum->waktu_mulai_tumbling)
                                                || !empty($tum->waktu_selesai_tumbling);

                                            $hasNonVakum = !empty($tum->aktual_drum_on_non_vakum)
                                                || !empty($tum->aktual_drum_off_non_vakum)
                                                || !empty($tum->aktual_speed_non_vakum)
                                                || !empty($tum->aktual_total_waktu_non_vakum)
                                                || !empty($tum->aktual_tekanan_non_vakum)
                                                || !empty($tum->waktu_mulai_tumbling_non_vakum)
                                                || !empty($tum->waktu_selesai_tumbling_non_vakum);

                                            $colClass = ($hasVakum && $hasNonVakum) ? 'col-md-6' : 'col-md-12';

                                            // Label badge untuk header mesin
                                            if ($hasVakum && $hasNonVakum) {
                                                $modeBadge = '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-double mr-1"></i>Keduanya</span>';
                                            } elseif ($hasVakum) {
                                                $modeBadge = '<span class="badge badge-warning px-2 py-1 text-dark"><i class="fas fa-wind mr-1"></i>Vakum</span>';
                                            } else {
                                                $modeBadge = '<span class="badge badge-primary px-2 py-1"><i class="fas fa-fan mr-1"></i>Non Vakum</span>';
                                            }
                                        @endphp

                                        <div class="machine-group mb-5">
                                            {{-- Header mesin --}}
                                            <div class="bg-dark p-2 mb-3 rounded shadow-sm d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0 text-white font-weight-bold">
                                                    <i class="fas fa-cog mr-2"></i>
                                                    {{ $tum->dataTumbling->mesin ?? 'Mesin' }}
                                                </h5>
                                                {!! $modeBadge !!}
                                            </div>

                                            <div class="row">

                                                {{-- TUMBLING VAKUM --}}
                                                @if($hasVakum)
                                                <div class="{{ $colClass }} mb-3">
                                                    <div class="card card-outline card-warning h-100 shadow-sm">
                                                        <div class="card-header">
                                                            <h3 class="card-title font-weight-bold text-warning">
                                                                <i class="fas fa-wind mr-1"></i> Tumbling Vakum
                                                            </h3>
                                                        </div>
                                                        <div class="card-body p-0">
                                                            <table class="table table-sm table-striped mb-0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td width="50%" class="pl-3">
                                                                            <strong>Drum On (Std)</strong><br>
                                                                            <span class="text-muted">{{ $tum->dataTumbling->drum_on ?? '-' }}</span>
                                                                        </td>
                                                                        <td width="50%">
                                                                            <strong>Aktual Drum On</strong><br>
                                                                            <span class="text-primary font-weight-bold">{{ $tum->aktual_drum_on ?? '-' }}</span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="pl-3">
                                                                            <strong>Drum Off (Std)</strong><br>
                                                                            <span class="text-muted">{{ $tum->dataTumbling->drum_off ?? '-' }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <strong>Aktual Drum Off</strong><br>
                                                                            <span class="text-primary font-weight-bold">{{ $tum->aktual_drum_off ?? '-' }}</span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="pl-3">
                                                                            <strong>Drum Speed (Std)</strong><br>
                                                                            <span class="text-muted">{{ $tum->dataTumbling->drum_speed ?? '-' }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <strong>Aktual Speed</strong><br>
                                                                            <span class="text-primary font-weight-bold">{{ $tum->aktual_speed ?? '-' }}</span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="pl-3">
                                                                            <strong>Total Waktu (Std)</strong><br>
                                                                            <span class="text-muted">{{ $tum->dataTumbling->total_waktu ?? '-' }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <strong>Aktual Total Waktu</strong><br>
                                                                            <span class="text-primary font-weight-bold">{{ $tum->aktual_total_waktu ?? '-' }}</span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="pl-3">
                                                                            <strong>Tekanan Vakum (Std)</strong><br>
                                                                            <span class="text-muted">{{ $tum->dataTumbling->tekanan_vakum ?? '-' }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <strong>Aktual Vakum</strong><br>
                                                                            <span class="text-primary font-weight-bold">{{ $tum->aktual_vakum ?? '-' }}</span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="pl-3">
                                                                            <strong>Waktu Mulai</strong><br>
                                                                            <span class="text-success font-weight-bold">{{ $tum->waktu_mulai_tumbling ?? '-' }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <strong>Waktu Selesai</strong><br>
                                                                            <span class="text-success font-weight-bold">{{ $tum->waktu_selesai_tumbling ?? '-' }}</span>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                {{-- TUMBLING NON VAKUM --}}
                                                @if($hasNonVakum)
                                                <div class="{{ $colClass }} mb-3">
                                                    <div class="card card-outline card-info h-100 shadow-sm">
                                                        <div class="card-header">
                                                            <h3 class="card-title font-weight-bold text-info">
                                                                <i class="fas fa-circle-notch mr-1"></i> Tumbling Non-Vakum
                                                            </h3>
                                                        </div>
                                                        <div class="card-body p-0">
                                                            <table class="table table-sm table-striped mb-0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td width="50%" class="pl-3">
                                                                            <strong>Drum On (Std)</strong><br>
                                                                            <span class="text-muted">{{ $tum->dataTumbling->drum_on_non_vakum ?? '-' }}</span>
                                                                        </td>
                                                                        <td width="50%">
                                                                            <strong>Aktual Drum On</strong><br>
                                                                            <span class="text-primary font-weight-bold">{{ $tum->aktual_drum_on_non_vakum ?? '-' }}</span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="pl-3">
                                                                            <strong>Drum Off (Std)</strong><br>
                                                                            <span class="text-muted">{{ $tum->dataTumbling->drum_off_non_vakum ?? '-' }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <strong>Aktual Drum Off</strong><br>
                                                                            <span class="text-primary font-weight-bold">{{ $tum->aktual_drum_off_non_vakum ?? '-' }}</span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="pl-3">
                                                                            <strong>Drum Speed (Std)</strong><br>
                                                                            <span class="text-muted">{{ $tum->dataTumbling->drum_speed_non_vakum ?? '-' }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <strong>Aktual Speed</strong><br>
                                                                            <span class="text-primary font-weight-bold">{{ $tum->aktual_speed_non_vakum ?? '-' }}</span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="pl-3">
                                                                            <strong>Total Waktu (Std)</strong><br>
                                                                            <span class="text-muted">{{ $tum->dataTumbling->total_waktu_non_vakum ?? '-' }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <strong>Aktual Total Waktu</strong><br>
                                                                            <span class="text-primary font-weight-bold">{{ $tum->aktual_total_waktu_non_vakum ?? '-' }}</span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="pl-3">
                                                                            <strong>Tekanan (Std)</strong><br>
                                                                            <span class="text-muted">{{ $tum->dataTumbling->tekanan_non_vakum ?? '-' }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <strong>Aktual Tekanan</strong><br>
                                                                            <span class="text-primary font-weight-bold">{{ $tum->aktual_tekanan_non_vakum ?? '-' }}</span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="pl-3">
                                                                            <strong>Waktu Mulai</strong><br>
                                                                            <span class="text-success font-weight-bold">{{ $tum->waktu_mulai_tumbling_non_vakum ?? '-' }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <strong>Waktu Selesai</strong><br>
                                                                            <span class="text-success font-weight-bold">{{ $tum->waktu_selesai_tumbling_non_vakum ?? '-' }}</span>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                {{-- HASIL AKHIR --}}
                                                <div class="col-12 mt-2">
                                                    <div class="card card-outline card-danger shadow-sm">
                                                        <div class="card-header">
                                                            <h3 class="card-title font-weight-bold text-danger">
                                                                <i class="fas fa-thermometer-half mr-1"></i> Hasil Akhir Tumbling
                                                            </h3>
                                                        </div>
                                                        <div class="card-body py-2">
                                                            <div class="row">
                                                                <div class="col-md-6 border-right">
                                                                    <strong>Suhu Hasil Tumbling:</strong>
                                                                    <span class="text-danger font-weight-bold ml-2">
                                                                        {{ $tum->suhu_hasil_tumbling ?? '-' }} °C
                                                                    </span>
                                                                </div>
                                                                <div class="col-md-6 pl-md-4">
                                                                    <strong>Sensori:</strong>
                                                                    @if(($tum->sensori_hasil_tumbling ?? '') == 'oke')
                                                                        <span class="badge badge-success px-3 ml-2">
                                                                            <i class="fas fa-check-circle mr-1"></i> OKE
                                                                        </span>
                                                                    @else
                                                                        <span class="badge badge-danger px-3 ml-2">
                                                                            <i class="fas fa-times-circle mr-1"></i>
                                                                            {{ strtoupper($tum->sensori_hasil_tumbling ?? '-') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>{{-- end .row --}}
                                        </div>{{-- end .machine-group --}}
                                    @endforeach
                                @else
                                    <div class="alert alert-info text-center py-4">
                                        <i class="fas fa-info-circle fa-2x mb-3 d-block"></i>
                                        Data proses tumbling belum tersedia untuk produksi ini.
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- ===== LANGKAH 3: AGING ===== --}}
                        <div class="card card-outline card-success mb-5 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title">
                                    <i class="fas fa-clock text-success mr-1"></i> Langkah 3: Hasil Proses Aging
                                </h3>
                            </div>
                            <div class="card-body">
                                @php
                                    $waktuMulaiArray  = array_map('trim', explode(',', $aging->waktu_mulai_aging ?? ''));
                                    $suhuKondisiArray = array_map('trim', explode(',', $aging->suhu_produk ?? ''));
                                    $maxCount = max(count($waktuMulaiArray), count($suhuKondisiArray));
                                @endphp

                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="font-weight-bold mb-3">
                                            <i class="fas fa-thermometer-half text-success mr-2"></i>
                                            Data Suhu, Kondisi & Waktu Mulai Aging
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="bg-success text-white text-center">
                                                    <tr>
                                                        <th width="10%">No</th>
                                                        <th width="30%">Waktu Mulai Aging</th>
                                                        <th width="30%">Suhu Produk</th>
                                                        <th width="30%">Kondisi Produk</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for($index = 0; $index < $maxCount; $index++)
                                                        @php
                                                            $waktu      = $waktuMulaiArray[$index]  ?? '';
                                                            $suhuKondisi = $suhuKondisiArray[$index] ?? '';

                                                            if (preg_match('/^(.+?)\s*\((.+?)\)$/', $suhuKondisi, $matches)) {
                                                                $suhu    = trim($matches[1]);
                                                                $kondisi = trim($matches[2]);
                                                            } else {
                                                                $suhu    = $suhuKondisi;
                                                                $kondisi = $aging->kondisi_produk ?? '✓';
                                                            }
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center font-weight-bold">{{ $index + 1 }}</td>
                                                            <td class="text-center">
                                                                <span class="badge badge-primary px-4 py-2" style="font-size:1rem;">
                                                                    <i class="far fa-clock mr-2"></i>{{ $waktu }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge badge-success px-4 py-2" style="font-size:1rem;">
                                                                    <i class="fas fa-temperature-low mr-2"></i>{{ $suhu }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                @if($kondisi == '✓')
                                                                    <span class="badge badge-success px-4 py-2" style="font-size:1rem;">
                                                                        <i class="fas fa-check-circle mr-2"></i>OK
                                                                    </span>
                                                                @else
                                                                    <span class="badge badge-danger px-4 py-2" style="font-size:1rem;">
                                                                        <i class="fas fa-times-circle mr-2"></i>Tidak OK
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card bg-light shadow-sm border-success">
                                            <div class="card-body text-center">
                                                <h6 class="font-weight-bold text-success mb-3">
                                                    <i class="fas fa-stop-circle mr-2"></i>Waktu Selesai Aging
                                                </h6>
                                                <span class="badge badge-success px-5 py-3" style="font-size:1.3rem;">
                                                    <i class="far fa-clock mr-2"></i> {{ $aging->waktu_selesai_aging }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

<style>
    .bg-gray-light { background-color: #f8f9fa; }
    @media print {
        .main-sidebar, .main-header, .card-tools, .btn, .breadcrumb { display: none !important; }
        .content-wrapper { margin-left: 0 !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
        .bg-gradient-primary { background: #fff !important; color: #000 !important; border: 1px solid #000 !important; }
        .badge { border: 1px solid #000 !important; color: #000 !important; background: transparent !important; }
    }
</style>

@push('scripts')
<script>
$(document).ready(function () {
    // scripts tambahan jika diperlukan
});
</script>
@endpush