@extends('layouts.app')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><i class="fas fa-magic text-primary"></i> Verifikasi Proses Roasted Produk</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('wizard.proses3.index') }}">Verifikasi Proses Roasted Produk</a></li>
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

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible shadow-sm border-0">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <strong><i class="fas fa-exclamation-triangle mr-1"></i> Validasi Gagal:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="row">
                    {{-- ===== HEADER INFO ===== --}}
                    <div class="col-12 mb-3">
                        <div class="card bg-gradient-primary shadow-sm border-0">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h3 class="mb-1 font-weight-bold">
                                            {{ $pembekuan->inputRoastingData->produk->nama_produk ?? ($pembekuan->prosesRoastingFanData->produk->nama_produk ?? ($pembekuan->hasilProsesRoastingData->produk->nama_produk ?? 'N/A')) }}
                                            <span class="h5">({{ $pembekuan->inputRoastingData->berat_produk ?? '-' }} gr)</span>
                                        </h3>
                                        <div class="d-flex flex-wrap" style="gap: 15px;">
                                            <span class="badge badge-light p-2"><i class="fas fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($pembekuan->tanggal)->format('d F Y') }}</span>
                                            <span class="badge badge-light p-2"><i class="fas fa-clock mr-1"></i> {{ $pembekuan->inputRoastingData->jam ?? $pembekuan->jam }}</span>
                                            <span class="badge badge-light p-2"><i class="fas fa-layer-group mr-1"></i>Shift {{ $pembekuan->shift_data->shift ?? ($pembekuan->shift->shift ?? 'N/A') }}</span>
                                            <span class="badge badge-light p-2"><i class="fas fa-user mr-1"></i>Dibuat Oleh (Produksi) : {{ $pembekuan->user->name ?? 'N/A' }}</span>
                                            @if($pembekuan->hasilProsesRoastingData)
                                                <span class="badge badge-success p-2"><i class="fas fa-check-circle mr-1"></i> Verifikasi QC Lengkap</span>
                                            @else
                                                <span class="badge badge-warning p-2"><i class="fas fa-hourglass-half mr-1"></i> Menunggu Input Verifikasi QC</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                                        @if(!empty($canEditQcVerification))
                                            <a href="#qc-verification-section" class="btn btn-warning btn-sm mr-2 font-weight-bold">
                                                <i class="fas fa-clipboard-check mr-1"></i> {{ $pembekuan->hasilProsesRoastingData ? 'Edit Verifikasi QC' : 'Input Verifikasi QC' }}
                                            </a>
                                        @endif
                                        @if(!$pembekuan->approved_by_qc && !$pembekuan->approved_by_produksi && !$pembekuan->approved_by_spv)
                                            @php
                                                $canEditWp3Show = true;
                                                $restrictedRoles = config('edit_limit.restricted_role_ids', []);
                                                if (in_array(auth()->user()->id_role, $restrictedRoles)) {
                                                    $timeLimitHours = config('edit_limit.time_limit_hours', 8);
                                                    $createdAt = \Carbon\Carbon::parse($pembekuan->created_at);
                                                    $hoursElapsed = $createdAt->diffInHours(now(), true);
                                                    if ($hoursElapsed > $timeLimitHours) {
                                                        $canEditWp3Show = false;
                                                    }
                                                }
                                            @endphp
                                            @if($canEditWp3Show)
                                                <a href="{{ route('wizard.proses3.edit', $pembekuan->uuid) }}" class="btn btn-info btn-sm mr-2 text-white">
                                                    <i class="fas fa-edit mr-1"></i> Edit Data
                                                </a>
                                            @else
                                                <span class="btn btn-secondary btn-sm mr-2 disabled" title="Waktu edit sudah habis (batas 8 jam dari pembuatan data)">
                                                    <i class="fas fa-lock mr-1"></i> Edit Data <small class="text-danger">(Expired)</small>
                                                </span>
                                            @endif
                                        @endif
                                        <a href="{{ route('wizard.proses3.index') }}" class="btn btn-light btn-sm mr-2">
                                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== STEP-BY-STEP DETAILS ===== --}}
                    <div class="col-12">
                        <div class="mb-2">
                            <h5 class="font-weight-bold text-secondary"><i class="fas fa-industry mr-1"></i> A. Detail Inputan Produksi (Langkah 1 – 2)</h5>
                        </div>
                        
                        {{-- STEP 1: INPUT ROASTING --}}
                        @if($pembekuan->inputRoastingData)
                        <div class="card card-outline card-primary mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-sign-in-alt text-primary mr-1"></i> Langkah 1: Input Roasting</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- <div class="col-md-3"><strong>Produk:</strong><p>{{ $pembekuan->inputRoastingData->produk->nama_produk ?? 'N/A' }}</p></div> -->
                                    <div class="col-md-3"><strong>Kode Produksi:</strong><p>{{ $pembekuan->inputRoastingData->kode_produksi }}</p></div>
                                    <!-- <div class="col-md-3"><strong>Berat Produk:</strong><p>{{ $pembekuan->inputRoastingData->berat_produk }} gr</p></div> -->
                                    <!-- <div class="col-md-3"><strong>Waktu:</strong><p>{{ \Carbon\Carbon::parse($pembekuan->inputRoastingData->tanggal)->format('d-m-Y') }} {{ $pembekuan->inputRoastingData->jam }}</p></div> -->
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3"><strong>Standard Suhu Sebelum Pemasakan:</strong><p>{{ $pembekuan->inputRoastingData->std_suhu_sebelum }} °C</p></div>
                                    <div class="col-md-3"><strong>Aktual Suhu Sebelum Pemasakan:</strong><p>{{ $pembekuan->inputRoastingData->aktual_suhu_sesudah }} °C</p></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- STEP 2: ROASTING FAN --}}
                        @if($pembekuan->prosesRoastingFanData)
                        <div class="card card-outline card-primary mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-fan text-primary mr-1"></i> Langkah 2: Roasting Fan</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <!-- <div class="col-md-3"><strong>Waktu:</strong><p>{{ \Carbon\Carbon::parse($pembekuan->prosesRoastingFanData->tanggal)->format('d-m-Y') }} {{ $pembekuan->prosesRoastingFanData->jam }}</p></div> -->
                                    <!-- <div class="col-md-3"><strong>Produk:</strong><p>{{ $pembekuan->prosesRoastingFanData->produk->nama_produk ?? 'N/A' }}</p></div> -->
                                    <div class="col-md-3"><strong>Waktu Pemasakan (pukul):</strong><p>{{ $pembekuan->prosesRoastingFanData->aktual_waktu ?? '-' }} </p></div>
                                    <div class="col-md-3"><strong>Waktu Pengecekan (menit):</strong><p>{{ $pembekuan->prosesRoastingFanData->waktu_pemasakan ?? '-' }} </p></div>
                                </div>

                                @if($pembekuan->prosesRoastingFanData->is_grouped && $pembekuan->prosesRoastingFanData->blok_data)
                                    @php
                                        $blockData = $pembekuan->prosesRoastingFanData->blok_data;
                                        if (is_string($blockData)) {
                                            $blockData = json_decode($blockData, true) ?: [];
                                        }
                                        $sortedBlok = collect($blockData)->sortBy('block_number');
                                    @endphp
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm text-center">
                                            <thead class="bg-dark text-white">
                                                <tr>
                                                    <th style="width: 200px;" class="text-left">Parameter</th>
                                                    @foreach($sortedBlok as $blok)
                                                        <th>Blok {{ $blok['block_number'] ?? ($loop->index + 1) }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Standar Suhu Pemasakan --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Standar Suhu Pemasakan (°C)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        @php
                                                            $stdSuhu = \App\Models\SuhuBlok::find($blok['id_suhu_blok']);
                                                        @endphp
                                                        <td><span class="badge badge-secondary">{{ $stdSuhu->suhu_blok ?? '-' }} °C</span></td>
                                                    @endforeach
                                                </tr>
                                                {{-- Aktual Suhu Pemasakan --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Suhu Pemasakan (°C)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        <td><span class="badge badge-primary">{{ $blok['suhu_roasting'] ?? '-' }} °C</span></td>
                                                    @endforeach
                                                </tr>
                                                
                                                {{-- Standar Fan 1 --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Standar Fan 1 (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        @php
                                                            $stdFan = \App\Models\StdFan::find($blok['id_std_fan']);
                                                        @endphp
                                                        <td><span class="badge badge-secondary">{{ $stdFan->std_fan ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>
                                                {{-- Aktual Fan 1 --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Fan 1 (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        <td><span class="badge badge-success">{{ $blok['fan_1'] ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>

                                                {{-- Standar Fan 2 --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Standar Fan 2 (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        @php
                                                            $stdFan = \App\Models\StdFan::find($blok['id_std_fan']);
                                                        @endphp
                                                        <td><span class="badge badge-secondary">{{ $stdFan->std_fan_2 ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>
                                                {{-- Aktual Fan 2 --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Fan 2 (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        <td><span class="badge badge-success">{{ $blok['fan_2'] ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>

                                                {{-- Standar Fan 3 --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Standar Fan 3 (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        @php
                                                            $stdFan = \App\Models\StdFan::find($blok['id_std_fan']);
                                                        @endphp
                                                        <td><span class="badge badge-secondary">{{ $stdFan->fan_3 ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>
                                                {{-- Aktual Fan 3 --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Fan 3 (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        <td><span class="badge badge-success">{{ $blok['fan_3'] ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>

                                                {{-- Standar Fan 4 --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Standar Fan 4 (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        @php
                                                            $stdFan = \App\Models\StdFan::find($blok['id_std_fan']);
                                                        @endphp
                                                        <td><span class="badge badge-secondary">{{ $stdFan->fan_4 ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>
                                                {{-- Aktual Fan 4 --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Fan 4 (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        <td><span class="badge badge-success">{{ $blok['fan_4'] ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>

                                                {{-- Standar Humidity --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Standar Humidity (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        @php
                                                            $stdFan = \App\Models\StdFan::find($blok['id_std_fan']);
                                                        @endphp
                                                        <td><span class="badge badge-secondary">{{ $stdFan->std_humadity ?? '-' }} %</span></td>
                                                    @endforeach
                                                </tr>
                                                {{-- Aktual Humidity - Merged Infeed & Outfeed --}}
                                                @php
                                                    $mergedHum3 = null; $mergedIr3 = null;
                                                    foreach($sortedBlok as $b3) {
                                                        if(in_array($b3['block_number'] ?? '', ['INFEED','OUTFEED'])) {
                                                            if(is_null($mergedHum3)) $mergedHum3 = $b3['aktual_humadity'] ?? null;
                                                            if(is_null($mergedIr3)) $mergedIr3 = $b3['infra_red'] ?? null;
                                                        }
                                                    }
                                                    $sk3H = false; $sk3I = false;
                                                @endphp
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Humidity/Steam (%)</td>
                                                    @foreach($sortedBlok as $blok)
                                                        @if(($blok['block_number'] ?? '') === 'INFEED')
                                                            <td colspan="2">{{ $mergedHum3 ?? '-' }} %</td>
                                                            @php $sk3H = true; @endphp
                                                        @elseif(($blok['block_number'] ?? '') === 'OUTFEED' && $sk3H)
                                                            {{-- merged --}}
                                                        @else
                                                            <td>{{ $blok['aktual_humadity'] ?? '-' }} %</td>
                                                        @endif
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Infra Red</td>
                                                    @foreach($sortedBlok as $blok)
                                                        @if(($blok['block_number'] ?? '') === 'INFEED')
                                                            <td colspan="2">{{ $mergedIr3 ?? '-' }}</td>
                                                            @php $sk3I = true; @endphp
                                                        @elseif(($blok['block_number'] ?? '') === 'OUTFEED' && $sk3I)
                                                            {{-- merged --}}
                                                        @else
                                                            <td>{{ $blok['infra_red'] ?? '-' }}</td>
                                                        @endif
                                                    @endforeach
                                                </tr>
                                                {{-- Standar Lama Proses --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Standar Lama Proses (Menit)</td>
                                                    @php
                                                        $firstBlok = $sortedBlok->first();
                                                        $stdLama = $firstBlok ? \App\Models\StdFan::find($firstBlok['id_std_fan']) : null;
                                                    @endphp
                                                    <td colspan="{{ count($sortedBlok) }}">
                                                        <span class="badge badge-secondary">{{ $stdLama->std_lama_proses ?? '-' }} Menit</span>
                                                    </td>
                                                </tr>
                                                {{-- Aktual Lama Proses --}}
                                                <tr>
                                                    <td class="text-left font-weight-bold bg-light">Lama Proses (Display Aktual; Menit)</td>
                                                    <td colspan="{{ count($sortedBlok) }}">
                                                        <span class="badge badge-primary">{{ $pembekuan->prosesRoastingFanData->aktual_lama_proses ?? '-' }} Menit</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    {{-- Format Lama (Single) --}}
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm text-center">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Parameter</th>
                                                    <th>Aktual</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-left font-weight-bold">Suhu Pemasakan (°C)</td>
                                                    <td><span class="badge badge-primary">{{ $pembekuan->prosesRoastingFanData->aktual_suhu_fan }} °C</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left font-weight-bold">Fan 1 (%)</td>
                                                    <td><span class="badge badge-success">{{ $pembekuan->prosesRoastingFanData->aktual_speed_fan }} %</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left font-weight-bold">Humidity/Steam Valve (%)</td>
                                                    <td>{{ $pembekuan->prosesRoastingFanData->aktual_humadity ?? '-' }} %</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left font-weight-bold">Infra Red</td>
                                                    <td>{{ $pembekuan->prosesRoastingFanData->infra_red ?? '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <div class="mt-4 mb-2" id="qc-verification-section">
                            <h5 class="font-weight-bold text-primary"><i class="fas fa-user-check mr-1"></i> B. Verifikasi Hasil Akhir Roasting (Inputan QC Inspector)</h5>
                        </div>

                        {{-- STEP 3: RINGKASAN HASIL ROASTING --}}
                        @php
                            $hasilRoasting = $pembekuan->hasilProsesRoastingData;
                            $suhuPusatRaw = $hasilRoasting->aktual_suhu_pusat ?? [];
                            $suhuPusatList = [];
                            if (is_array($suhuPusatRaw)) {
                                foreach ($suhuPusatRaw as $val) {
                                    if (is_string($val) && $val !== '') {
                                        preg_match_all('/\d+[.,]\d+|\d+/', $val, $matches);
                                        foreach ($matches[0] as $match) {
                                            $suhuPusatList[] = str_replace(',', '.', $match);
                                        }
                                    } elseif (is_numeric($val)) {
                                        $suhuPusatList[] = str_replace(',', '.', (string) $val);
                                    }
                                }
                            } elseif (is_string($suhuPusatRaw) && $suhuPusatRaw !== '') {
                                preg_match_all('/\d+[.,]\d+|\d+/', $suhuPusatRaw, $matches);
                                $suhuPusatList = array_map(fn($v) => str_replace(',', '.', $v), $matches[0]);
                            }
                            $sensori = is_array($hasilRoasting?->sensori) ? $hasilRoasting->sensori : [];
                        @endphp

                        @if($hasilRoasting)
                        <div class="card card-outline card-primary mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="fas fa-check-double text-primary mr-1"></i> Ringkasan Verifikasi Hasil Akhir Roasting (QC)</h3>
                                <div class="card-tools">
                                    <span class="badge badge-success mr-2"><i class="fas fa-check mr-1"></i> Sudah Diinput QC</span>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 text-center">
                                    <div class="col-md-4"><strong>Suhu Pusat Std:</strong><p>{{ $hasilRoasting->stdSuhuPusat->std_suhu_pusat_roasting ?? 'N/A' }} °C</p></div>
                                    <div class="col-md-4">
                                        <strong>Suhu Produk Aktual:</strong>
                                        <p>
                                            @if(!empty($suhuPusatList))
                                                @foreach($suhuPusatList as $suhuVal)
                                                    <span class="badge badge-success mr-1">{{ $suhuVal }} °C</span>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-4"><strong>Keterangan:</strong><p>{{ $hasilRoasting->keterangan ?? '-' }}</p></div>
                                </div>
                                <hr>
                                <h6><strong>Uji Sensori Produk:</strong></h6>
                                <div class="row text-center mt-2">
                                    @php
                                        $sensories = [
                                            'Kematangan'  => $sensori['sensori_kematangan'] ?? ($hasilRoasting->sensori_kematangan ?? '-'),
                                            'Rasa'        => $sensori['sensori_rasa']       ?? ($hasilRoasting->sensori_rasa       ?? '-'),
                                            'Aroma (Bau)' => $sensori['sensori_bau']        ?? ($hasilRoasting->sensori_bau        ?? '-'),
                                            'Tekstur'     => $sensori['sensori_tekstur']    ?? ($hasilRoasting->sensori_tekstur    ?? '-'),
                                            'Kenampakan'  => $sensori['sensori_kenampakan'] ?? ($hasilRoasting->sensori_kenampakan ?? '-'),
                                            'Warna'       => $sensori['sensori_warna']      ?? ($hasilRoasting->sensori_warna      ?? '-'),
                                        ];
                                    @endphp
                                    @foreach($sensories as $label => $val)
                                    <div class="col-4 col-md-2 mb-3">
                                        <div class="small text-muted">{{ $label }}</div>
                                        <div class="font-weight-bold h5">
                                            <span class="badge badge-{{ $val == '✔' ? 'success' : ($val == '✘' ? 'danger' : 'secondary') }}">
                                                {{ $val == '✔' ? '✔ OK' : ($val == '✘' ? '✘ Tidak OK' : ($val ?? '-')) }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-warning shadow-sm mb-4">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <strong>Belum Ada Verifikasi Hasil Akhir Roasting dari QC Inspector.</strong>
                            @if(empty($canEditQcVerification))
                                Silakan menunggu QC Inspector menginput hasil verifikasi suhu produk dan uji sensori pada halaman ini.
                            @endif
                        </div>
                        @endif

                        {{-- FORM INPUT / EDIT VERIFIKASI HASIL AKHIR ROASTING (KHUSUS QC INSPECTOR / ADMIN / SUPERADMIN) --}}
                        @if(!empty($canEditQcVerification))
                        <div class="card card-primary shadow-sm mb-5">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-clipboard-check mr-1"></i>
                                    {{ $hasilRoasting ? 'Perbarui Verifikasi Hasil Akhir Roasting (QC Inspector)' : 'Form Input Verifikasi Hasil Akhir Roasting (QC Inspector)' }}
                                </h3>
                            </div>
                            <form action="{{ route('wizard.proses3.qc-verification', $pembekuan->uuid) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label>Jam Verifikasi QC</label>
                                            <input type="time" name="jam_hasil" class="form-control" value="{{ old('jam_hasil', $hasilRoasting->jam ?? \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}">
                                        </div>
                                        <div class="col-md-8 form-group">
                                            <label>Standar Suhu Pusat Produk <span class="text-danger">*</span></label>
                                            <select name="id_std_suhu_pusat" class="form-control" required>
                                                <option value="">-- Pilih Standar Suhu Pusat --</option>
                                                @foreach($stdSuhuPusatOptions ?? [] as $std)
                                                    <option value="{{ $std->id }}" {{ old('id_std_suhu_pusat', $hasilRoasting->id_std_suhu_pusat ?? '') == $std->id ? 'selected' : '' }}>
                                                        {{ $std->std_suhu_pusat_roasting }} °C
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Input Suhu Produk (°C) <span class="text-danger">*</span></label>
                                        <div id="qc_suhu_pusat_container_p3">
                                            @php
                                                $existingSuhuInputs = old('aktual_suhu_pusat', !empty($suhuPusatList) ? $suhuPusatList : ['']);
                                            @endphp
                                            @foreach($existingSuhuInputs as $idx => $sVal)
                                                <div class="input-group mb-2">
                                                    <input type="number" step="0.1" name="aktual_suhu_pusat[]" class="form-control" value="{{ $sVal }}" placeholder="Contoh: 75.5" required>
                                                    <div class="input-group-append">
                                                        @if($idx === 0)
                                                            <button type="button" class="btn btn-outline-success btn-add-qc-suhu-p3"><i class="fas fa-plus"></i> Tambah Pengukuran</button>
                                                        @else
                                                            <button type="button" class="btn btn-outline-danger btn-remove-qc-suhu-p3"><i class="fas fa-minus"></i></button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <hr>
                                    <h6 class="font-weight-bold mb-3"><i class="fas fa-check-circle text-primary mr-1"></i> Input Sensori Produk (Dropdown OK / Tidak OK)</h6>
                                    @php
                                        $sensoriFields = [
                                            'sensori_kematangan' => 'Kematangan',
                                            'sensori_rasa'       => 'Rasa',
                                            'sensori_bau'        => 'Aroma (Bau)',
                                            'sensori_tekstur'    => 'Tekstur',
                                            'sensori_kenampakan' => 'Kenampakan',
                                            'sensori_warna'      => 'Warna',
                                        ];
                                    @endphp
                                    <div class="row">
                                        @foreach($sensoriFields as $fieldKey => $fieldLabel)
                                            @php
                                                $currentVal = old($fieldKey, $sensori[$fieldKey] ?? ($hasilRoasting->{$fieldKey} ?? '✔'));
                                            @endphp
                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label>{{ $fieldLabel }} <span class="text-danger">*</span></label>
                                                <select name="{{ $fieldKey }}" class="form-control" required>
                                                    <option value="✔" {{ $currentVal == '✔' ? 'selected' : '' }}>✔ OK</option>
                                                    <option value="✘" {{ $currentVal == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="form-group mt-2">
                                        <label>Keterangan / Catatan QC</label>
                                        <textarea name="keterangan_hasil" class="form-control" rows="2" placeholder="Catatan hasil verifikasi roasting (opsional)">{{ old('keterangan_hasil', $hasilRoasting->keterangan ?? '') }}</textarea>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i> Simpan Verifikasi QC
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
document.addEventListener('click', function(e) {
    const addBtn = e.target.closest('.btn-add-qc-suhu-p3');
    if (addBtn) {
        e.preventDefault();
        const container = document.getElementById('qc_suhu_pusat_container_p3');
        if (container) {
            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `
                <input type="number" step="0.1" name="aktual_suhu_pusat[]" class="form-control" placeholder="Contoh: 75.5" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger btn-remove-qc-suhu-p3"><i class="fas fa-minus"></i></button>
                </div>
            `;
            container.appendChild(div);
        }
    }

    const removeBtn = e.target.closest('.btn-remove-qc-suhu-p3');
    if (removeBtn) {
        e.preventDefault();
        const group = removeBtn.closest('.input-group');
        if (group) group.remove();
    }
});
</script>

<style>
    @media print {
        .main-sidebar, .main-header, .card-tools, .btn, .breadcrumb { display: none !important; }
        .content-wrapper { margin-left: 0 !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
        .bg-gradient-primary { background: #fff !important; color: #000 !important; border: 1px solid #000 !important; }
        .badge { border: 1px solid #000 !important; color: #000 !important; background: transparent !important; }
    }
</style>
@endsection
