@extends('layouts.app')

@php
    $currentUser = auth()->user();
    $userRole = (int) ($currentUser->id_role ?? 0);
    $userRoleName = strtolower(optional($currentUser->roleModel)->role ?? $currentUser->role ?? '');
    $isSuperadmin = $currentUser->role === 'superadmin' || in_array($userRole, [1, 5], true);
    $isQcInspector = $isSuperadmin || (method_exists($currentUser, 'isQcInspector') && $currentUser->isQcInspector()) || $userRole === 3 || (method_exists($currentUser, 'isQc') && $currentUser->isQc()) || str_contains($userRoleName, 'qc');
    $isSpvQc = $isSuperadmin || $userRole === 4 || str_contains($userRoleName, 'spv qc') || str_contains($userRoleName, 'supervisor qc');

    $aktualSuhuVal = optional($data->aktualSuhuAdonan)->total_aktual_suhu 
        ?? optional($data->aktualSuhuAdonan)->aktual_suhu_1 
        ?? optional($data->aktuals->first())->total_aktual_suhu
        ?? optional($data->aktuals->first())->aktual_suhu_1
        ?? '';

    $kondisiPencampuranText = '-';
    if ($data->kondisi_hasil_pencampuran === '✔' || $data->kondisi_hasil_pencampuran === 'OK') {
        $kondisiPencampuranText = '✔ - Oke';
    } elseif ($data->kondisi_hasil_pencampuran === '✘' || $data->kondisi_hasil_pencampuran === 'NOT_OK') {
        $kondisiPencampuranText = '✘ - Tidak Oke';
    } elseif ($data->kondisi_hasil_pencampuran) {
        $kondisiPencampuranText = $data->kondisi_hasil_pencampuran;
    } else {
        $kondisiPencampuranText = 'Belum diisi (Verifikasi QC)';
    }
@endphp

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-eye text-primary"></i> Detail Verifikasi Persiapan Cold Mixing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('persiapan-bahan-forming.index') }}">Verifikasi Persiapan Cold Mixing</a></li>
                        <li class="breadcrumb-item active">Detail Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                        </div>
                        <div class="card-body">
                            <!-- Informasi Umum -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar mr-1"></i>Tanggal</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->tanggal ? \Carbon\Carbon::parse($data->tanggal)->format('Y-m-d H:i:s') : '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-clock mr-1"></i>Shift</label>
                                        @php
                                            $sVal = (string)($data->shift->shift ?? ($data->shift_id ?? ''));
                                            $shiftText = $sVal;
                                            if ($sVal === '1' || strcasecmp($sVal, 'shift 1') === 0 || strcasecmp($sVal, 'pagi') === 0) {
                                                $shiftText = 'Pagi (Shift 1)';
                                            } elseif ($sVal === '2' || strcasecmp($sVal, 'shift 2') === 0 || strcasecmp($sVal, 'sore') === 0) {
                                                $shiftText = 'Sore (Shift 2)';
                                            } elseif ($sVal === '3' || strcasecmp($sVal, 'shift 3') === 0 || strcasecmp($sVal, 'malam') === 0) {
                                                $shiftText = 'Malam (Shift 3)';
                                            }
                                        @endphp
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $shiftText }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jam</label>
                                        <input type="text" class="form-control" value="{{ $data->jam ? \Carbon\Carbon::parse($data->jam)->format('H:i') : '-' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-box mr-1"></i>Nama Produk</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->formula->produk->nama_produk ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-cube"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-barcode mr-1"></i>Nomor Formula</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->formula->nomor_formula ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-weight mr-1"></i>Gramase (gram)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->berat_sampling ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">g</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-code mr-1"></i>Kode Produksi</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->kode_produksi_emulsi }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-qrcode"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-code mr-1"></i>Kode Produksi Emulsi Oil</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ is_array($data->kode_produksi_emulsi_oil) ? implode(', ', $data->kode_produksi_emulsi_oil) : $data->kode_produksi_emulsi_oil }}" readonly>                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-qrcode"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Suhu Adonan -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-primary"><i class="fas fa-thermometer-half mr-2"></i>Suhu Adonan</h5>
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-thermometer-half mr-1"></i>Suhu Adonan (STD)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->suhuAdonan->std_suhu ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">°C</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Suhu Forming -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-primary"><i class="fas fa-fire mr-2"></i>Suhu Forming</h5>
                                    <hr>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th>Bahan Forming</th>
                                            <th class="text-center">Berat RM</th>
                                            <th class="text-center">Kode Produksi Bahan</th>
                                            <th class="text-center">Suhu RM (°C)</th>
                                            <th class="text-center">Sensori</th>
                                            <th class="text-center">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($data->suhuForming as $index => $suhu)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $suhu->bahanForming->nama_rm ?? '-' }}</td>
                                            <td class="text-center">{{ $suhu->bahanForming->berat_rm ?? '-' }}</td>
                                            <td class="text-center">{{ $suhu->kode_produksi_bahan }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-info">{{ $suhu->suhu }}°C</span>
                                            </td>
                                            <td class="text-center">{{ $suhu->sensori ?? '-' }}</td>
                                            <td class="text-center">{{ $suhu->keterangan ?? '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Tidak ada data suhu forming</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    @if($data->suhuForming->count() > 0)
                                    @php
                                        $getFloat = function($val) {
                                            if (is_null($val) || $val === '' || is_array($val)) return 0.0;
                                            $clean = preg_replace('/[^\d\.\-]/', '', str_replace(',', '.', (string)$val));
                                            return is_numeric($clean) ? (float)$clean : 0.0;
                                        };

                                        $beratList = $data->suhuForming->map(fn($s) => $getFloat($s->bahanForming->berat_rm ?? 0));

                                        $totalBerat = $beratList->sum();
                                        $avgBerat   = $beratList->count() > 0 ? $beratList->avg() : 0;
                                    @endphp
                                    <tfoot class="bg-light font-weight-bold">
                                        <tr>
                                            <td colspan="2" class="text-right">Total Berat RM:</td>
                                            <td class="text-center text-primary">{{ number_format($totalBerat, 3) }}</td>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="text-right">Rata-Rata Berat RM:</td>
                                            <td class="text-center text-success">{{ number_format($avgBerat, 3) }}</td>
                                            <td colspan="4"></td>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                            </div>

                            <!-- Suhu Aktual -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="text-success"><i class="fas fa-temperature-high mr-2"></i>Suhu Aktual</h6>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Suhu 1</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ optional($data->aktualSuhuAdonan)->aktual_suhu_1 ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">°C</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Suhu 2</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ optional($data->aktualSuhuAdonan)->aktual_suhu_2 ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">°C</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Suhu 3</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ optional($data->aktualSuhuAdonan)->aktual_suhu_3 ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">°C</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Suhu 4</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ optional($data->aktualSuhuAdonan)->aktual_suhu_4 ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">°C</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Suhu 5</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ optional($data->aktualSuhuAdonan)->aktual_suhu_5 ?? '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">°C</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Total (Rata-rata)</label>
                                        <div class="input-group">
                                            @php
                                                $avg = optional($data->aktualSuhuAdonan)->total_aktual_suhu;
                                            @endphp
                                            <input type="text" class="form-control bg-success text-white" value="{{ $avg !== null ? number_format($avg, 1) : '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">°C</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Waktu dan Informasi Lainnya -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-primary"><i class="fas fa-clock mr-2"></i>Waktu dan Informasi Lainnya</h5>
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-play mr-1"></i>Waktu Mulai Mixing</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->waktu_mulai_mixing ?: '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-stop mr-1"></i>Waktu Selesai Mixing</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->waktu_selesai_mixing ?: '-' }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-boxes mr-1"></i>Produk Rework</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $produkReworkNama }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-recycle mr-1"></i>Rework</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->rework }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">kg</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-info-circle mr-1"></i>Kondisi Hasil Penggilingan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $data->kondisi }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    @if(strpos((string)($data->kondisi ?? ''), '✔') !== false)
                                                        <i class="fas fa-check-circle text-success"></i>
                                                    @else
                                                        <i class="fas fa-exclamation-circle text-warning"></i>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-info-circle mr-1"></i>Kondisi Hasil Pencampuran</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $kondisiPencampuranText }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    @if(strpos((string)($data->kondisi_hasil_pencampuran ?? ''), '✔') !== false || $data->kondisi_hasil_pencampuran === 'OK')
                                                        <i class="fas fa-check-circle text-success"></i>
                                                    @elseif(strpos((string)($data->kondisi_hasil_pencampuran ?? ''), '✘') !== false || $data->kondisi_hasil_pencampuran === 'NOT_OK')
                                                        <i class="fas fa-times-circle text-danger"></i>
                                                    @else
                                                        <i class="fas fa-clock text-muted"></i>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-sticky-note mr-1"></i>Catatan</label>
                                        <textarea class="form-control" rows="3" readonly>{{ $data->catatan ?? '-' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Verifikasi QC Inspektor -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card card-success card-outline shadow-sm">
                                        <div class="card-header bg-white">
                                            <h5 class="card-title font-weight-bold mb-0 text-success">
                                                <i class="fas fa-clipboard-check mr-2"></i> Verifikasi QC Inspektor
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Ringkasan Status Verifikasi -->
                                            <div class="row mb-3">
                                                <div class="col-md-4 col-sm-6 mb-2">
                                                    <span class="text-muted d-block small">Status Verifikasi QC:</span>
                                                    @if($data->approved_by_qc)
                                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Terverifikasi QC</span>
                                                    @else
                                                        <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> Belum Diverifikasi</span>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 col-sm-6 mb-2">
                                                    <span class="text-muted d-block small">Hasil Pencampuran:</span>
                                                    @if($data->kondisi_hasil_pencampuran === '✔' || $data->kondisi_hasil_pencampuran === 'OK')
                                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> ✔ - Oke</span>
                                                    @elseif($data->kondisi_hasil_pencampuran === '✘' || $data->kondisi_hasil_pencampuran === 'NOT_OK')
                                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-times mr-1"></i> ✘ - Tidak Oke</span>
                                                    @else
                                                        <span class="badge badge-light border text-muted px-2 py-1">Belum diisi</span>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 col-sm-6 mb-2">
                                                    <span class="text-muted d-block small">Suhu Actual:</span>
                                                    @if($aktualSuhuVal !== '' && $aktualSuhuVal !== null)
                                                        <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 0.95rem;">
                                                            {{ is_numeric($aktualSuhuVal) ? number_format((float)$aktualSuhuVal, 1) : $aktualSuhuVal }} °C
                                                        </span>
                                                    @else
                                                        <span class="badge badge-light border text-muted px-2 py-1">Belum diisi</span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($isQcInspector)
                                                <form action="{{ route('persiapan-bahan-forming.qc-verification', $data->uuid) }}" method="POST">
                                                    @csrf
                                                    <div class="p-3 bg-light rounded border">
                                                        <h6 class="font-weight-bold text-dark mb-3">
                                                            <i class="fas fa-edit text-primary mr-1"></i> Form Input Verifikasi QC
                                                        </h6>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-md-0 mb-3">
                                                                    <label for="kondisi_hasil_pencampuran" class="font-weight-bold">
                                                                        <i class="fas fa-blender text-primary mr-1"></i> Hasil Pencampuran <span class="text-danger">*</span>
                                                                    </label>
                                                                    <select name="kondisi_hasil_pencampuran" id="kondisi_hasil_pencampuran" class="form-control @error('kondisi_hasil_pencampuran') is-invalid @enderror" required>
                                                                        <option value="">-- Pilih Hasil Pencampuran --</option>
                                                                        <option value="✔" {{ old('kondisi_hasil_pencampuran', $data->kondisi_hasil_pencampuran) === '✔' || old('kondisi_hasil_pencampuran', $data->kondisi_hasil_pencampuran) === 'OK' ? 'selected' : '' }}>
                                                                            ✔ - Oke
                                                                        </option>
                                                                        <option value="✘" {{ old('kondisi_hasil_pencampuran', $data->kondisi_hasil_pencampuran) === '✘' || old('kondisi_hasil_pencampuran', $data->kondisi_hasil_pencampuran) === 'NOT_OK' ? 'selected' : '' }}>
                                                                            ✘ - Tidak Oke
                                                                        </option>
                                                                    </select>
                                                                    @error('kondisi_hasil_pencampuran')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-md-0 mb-3">
                                                                    <label for="suhu_actual" class="font-weight-bold">
                                                                        <i class="fas fa-thermometer-half text-danger mr-1"></i> Suhu Actual (°C) <span class="text-danger">*</span>
                                                                    </label>
                                                                    <div class="input-group">
                                                                        <input type="number" step="0.1" name="suhu_actual" id="suhu_actual" 
                                                                               class="form-control @error('suhu_actual') is-invalid @enderror" 
                                                                               placeholder="Contoh: 4.5" 
                                                                               value="{{ old('suhu_actual', $aktualSuhuVal) }}" required>
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text font-weight-bold">°C</span>
                                                                        </div>
                                                                        @error('suhu_actual')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                    <small class="form-text text-muted">Hanya 1 kolom input angka suhu actual.</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3 text-right">
                                                            <button type="submit" class="btn btn-success shadow-sm font-weight-bold px-4">
                                                                <i class="fas fa-save mr-1"></i> Simpan Verifikasi QC
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            @else
                                                <div class="alert alert-light border mb-0">
                                                    <i class="fas fa-lock mr-1 text-muted"></i> Hanya role <strong>QC Inspektor</strong> yang dapat mengisi dan mengubah verifikasi ini.
                                                </div>
                                            @endif

                                            @if($data->approved_by_qc)
                                                <div class="small text-muted mt-3 pt-2 border-top">
                                                    <i class="fas fa-user-check text-success mr-1"></i> Diverifikasi oleh: 
                                                    <strong>{{ $data->qcApprover->name ?? '-' }}</strong>
                                                    @if($data->qcApprover)
                                                        <span class="badge badge-light border">{{ $data->qcApprover->roleModel->role ?? $data->qcApprover->role ?? 'QC' }}</span>
                                                    @endif
                                                    pada {{ $data->qc_approved_at ? \Carbon\Carbon::parse($data->qc_approved_at)->format('d-m-Y H:i') : '-' }}.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 4-Stage Verification & Approval Status -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5 class="text-primary font-weight-bold">
                                        <i class="fas fa-tasks mr-2"></i>Status Alur Verifikasi & Persetujuan (4 Tahap)
                                    </h5>
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                {{-- 1. Dibuat Oleh = Produksi --}}
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card h-100 border-left border-primary shadow-sm" style="border-left-width: 4px !important;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge badge-primary">1. Dibuat oleh</span>
                                                <i class="fas fa-check-circle text-success"></i>
                                            </div>
                                            <div class="font-weight-bold text-dark mt-2">Produksi</div>
                                            <div class="text-muted small mt-1">
                                                <i class="fas fa-user mr-1"></i> {{ $data->user->name ?? $data->dibuat_oleh ?? '-' }}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-calendar-alt mr-1"></i> {{ $data->created_at ? $data->created_at->format('d-m-Y H:i') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. Verifikasi = QC Inspektor --}}
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card h-100 border-left {{ $data->approved_by_qc ? 'border-success' : 'border-secondary' }} shadow-sm" style="border-left-width: 4px !important;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge {{ $data->approved_by_qc ? 'badge-success' : 'badge-secondary' }}">2. Verifikasi</span>
                                                <i class="fas {{ $data->approved_by_qc ? 'fa-check-circle text-success' : 'fa-clock text-muted' }}"></i>
                                            </div>
                                            <div class="font-weight-bold text-dark mt-2">QC Inspektor</div>
                                            <div class="text-muted small mt-1">
                                                <i class="fas fa-user mr-1"></i> {{ $data->qcApprover->name ?? '-' }}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-calendar-alt mr-1"></i> {{ $data->qc_approved_at ? \Carbon\Carbon::parse($data->qc_approved_at)->format('d-m-Y H:i') : '-' }}
                                            </div>
                                            @if(!$data->approved_by_qc)
                                                @if($isQcInspector)
                                                    <span class="badge badge-warning text-dark mt-2 d-block py-1">
                                                        <i class="fas fa-exclamation-circle mr-1"></i> Perlu Verifikasi (Form di atas)
                                                    </span>
                                                @else
                                                    <span class="badge badge-light border text-muted mt-2 d-block py-1">
                                                        Menunggu QC
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- 3. Diketahui = SPV Produksi --}}
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card h-100 border-left {{ $data->approved_by_produksi ? 'border-primary' : 'border-secondary' }} shadow-sm" style="border-left-width: 4px !important;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge {{ $data->approved_by_produksi ? 'badge-primary' : 'badge-secondary' }}">3. Diketahui</span>
                                                <i class="fas {{ $data->approved_by_produksi ? 'fa-check-circle text-primary' : 'fa-clock text-muted' }}"></i>
                                            </div>
                                            <div class="font-weight-bold text-dark mt-2">SPV Produksi</div>
                                            <div class="text-muted small mt-1">
                                                <i class="fas fa-user mr-1"></i> {{ $data->produksiApprover->name ?? '-' }}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-calendar-alt mr-1"></i> {{ $data->produksi_approved_at ? \Carbon\Carbon::parse($data->produksi_approved_at)->format('d-m-Y H:i') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 4. Disetujui = SPV QC --}}
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card h-100 border-left {{ $data->approved_by_spv ? 'border-warning' : 'border-secondary' }} shadow-sm" style="border-left-width: 4px !important;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge {{ $data->approved_by_spv ? 'badge-warning text-dark' : 'badge-secondary' }}">4. Disetujui</span>
                                                <i class="fas {{ $data->approved_by_spv ? 'fa-check-circle text-warning' : 'fa-clock text-muted' }}"></i>
                                            </div>
                                            <div class="font-weight-bold text-dark mt-2">SPV QC</div>
                                            <div class="text-muted small mt-1">
                                                <i class="fas fa-user mr-1"></i> {{ $data->spvApprover->name ?? '-' }}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-calendar-alt mr-1"></i> {{ $data->spv_approved_at ? \Carbon\Carbon::parse($data->spv_approved_at)->format('d-m-Y H:i') : '-' }}
                                            </div>
                                            @if(!$data->approved_by_spv)
                                                @if($data->approved_by_qc)
                                                    @if($isSpvQc)
                                                        <button type="button" class="btn btn-warning btn-sm font-weight-bold text-dark btn-block mt-2 shadow-sm btn-approve-spv" data-uuid="{{ $data->uuid }}">
                                                            <i class="fas fa-check-circle mr-1"></i> Setujui (SPV QC)
                                                        </button>
                                                    @else
                                                        <span class="badge badge-light border text-muted mt-2 d-block py-1">
                                                            Menunggu SPV QC
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-light border text-muted mt-2 d-block py-1">
                                                        <i class="fas fa-clock mr-1"></i> Menunggu Verifikasi QC
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($isSpvQc && $data->approved_by_qc && !$data->approved_by_spv)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-warning border shadow-sm d-flex flex-wrap justify-content-between align-items-center mb-0">
                                        <div class="mb-2 mb-md-0">
                                            <h6 class="font-weight-bold mb-1"><i class="fas fa-check-double mr-2"></i> Persetujuan SPV QC Tersedia</h6>
                                            <span>QC Inspektor telah memverifikasi data ini. Anda dapat langsung memberikan persetujuan sebagai SPV QC.</span>
                                        </div>
                                        <button type="button" class="btn btn-warning text-dark font-weight-bold shadow-sm btn-approve-spv px-3 py-2" data-uuid="{{ $data->uuid }}">
                                            <i class="fas fa-check-circle mr-1"></i> Setujui Sekarang (SPV QC)
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('persiapan-bahan-forming.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    @if(auth()->user()->hasPermissionTo('edit-persiapan-bahan-forming'))
                                    <a href="{{ route('persiapan-bahan-forming.edit', $data->uuid) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit Data
                                    </a>
                                    @endif
                                    @if(auth()->user()->hasPermissionTo('view-persiapan-bahan-forming'))
                                    <a href="{{ route('persiapan-bahan-forming.logs', $data->uuid) }}" class="btn btn-info">
                                        <i class="fas fa-history"></i> Lihat Riwayat
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handler untuk persetujuan SPV QC langsung
    $(document).on('click', '.btn-approve-spv', function(e) {
        e.preventDefault();
        const btn = $(this);
        const uuid = btn.data('uuid') || '{{ $data->uuid }}';

        const doApprove = function() {
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');
            $.ajax({
                url: "{{ route('persiapan-bahan-forming.approve', $data->uuid) }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    type: 'spv'
                },
                success: function(response) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message || 'Data berhasil disetujui oleh SPV QC',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        alert(response.message || 'Data berhasil disetujui oleh SPV QC');
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Setujui (SPV QC)');
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: msg
                        });
                    } else {
                        alert('Gagal: ' + msg);
                    }
                }
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Persetujuan SPV QC',
                text: 'Apakah Anda yakin ingin menyetujui data persiapan cold mixing ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<span class="text-dark font-weight-bold"><i class="fas fa-check mr-1"></i> Ya, Setujui</span>',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    doApprove();
                }
            });
        } else {
            if (confirm('Apakah Anda yakin ingin menyetujui data persiapan cold mixing ini sebagai SPV QC?')) {
                doApprove();
            }
        }
    });
});
</script>
@endpush
