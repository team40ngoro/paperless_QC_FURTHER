@extends('layouts.app')

@section('container')
@php
    $userRole = (int) (auth()->user()->id_role ?? 0);
    $isProduksiRole = $userRole === 2;
@endphp
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-edit text-primary"></i> Edit Verifikasi Proses Penggorengan/Pemasakan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('wizard.proses1.index') }}">Frying</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

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

            <form action="{{ route('wizard.proses1.update', $pembekuan->uuid) }}" method="POST" id="main-edit-form">
                @csrf
                @method('PUT')

                {{-- SECTION: INFO UTAMA --}}
                <div class="card card-dark card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-info-circle mr-1"></i> Info Utama Proses Penggorengan/Pemasakan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Produk <span class="text-danger">*</span></label>
                                    <select name="id_produk" class="form-control" required>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ optional($pembekuan->penggorenganData)->id_produk == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal" class="form-control" value="{{ \Carbon\Carbon::parse($pembekuan->tanggal)->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jam <span class="text-danger">*</span></label>
                                    <input type="time" name="jam" class="form-control" value="{{ $pembekuan->jam ? \Carbon\Carbon::parse($pembekuan->jam)->format('H:i') : '' }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 1: PENCETAKAN --}}
                @if($pembekuan->penggorenganData)
                <div class="card card-primary card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-fire mr-1 text-primary"></i> 1. Pencetakan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Shift <span class="text-danger">*</span></label>
                                    <select name="shift_id" class="form-control" required>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ $pembekuan->penggorenganData->shift_id == $shift->id ? 'selected' : '' }}>{{ $shift->shift }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kode Produksi <span class="text-danger">*</span></label>
                                    <input type="text" name="kode_produksi" class="form-control" value="{{ $pembekuan->penggorenganData->kode_produksi }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Berat Produk (gr)</label>
                                    <select name="berat_produk" id="nilai_select_berat_penggorengan" class="form-control select2" data-nilai-berat="true" data-selected="{{ $pembekuan->penggorenganData->berat_produk }}"></select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Speed Cetak / No. of Strokes</label>
                                    <input type="text" name="no_of_strokes" class="form-control" value="{{ $pembekuan->penggorenganData->no_of_strokes }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Waktu Pemasakan</label>
                                    <input type="text" name="waktu_pemasakan" class="form-control" value="{{ $pembekuan->penggorenganData->waktu_pemasakan }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Checklist Hasil Cetak</label>
                                    <select name="hasil_pencetakan" class="form-control">
                                        <option value="">-- Pilih Hasil Cetak --</option>
                                        <option value="✔" {{ $pembekuan->penggorenganData->hasil_pencetakan == '✔' ? 'selected' : '' }}>✔ OK</option>
                                        <option value="✘" {{ $pembekuan->penggorenganData->hasil_pencetakan == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- SECTION 2: PREDUST --}}
                @if($pembekuan->predust_uuid && $predustData)
                <div class="card card-info card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-mortar-pestle mr-1 text-info"></i> 2. Predusting</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jenis Predust</label>
                                    <select name="id_jenis_predust" class="form-control">
                                        <option value="">Pilih Jenis Predust</option>
                                        @foreach($jenisPredust as $jp)
                                            <option value="{{ $jp->id }}" {{ $predustData->id_jenis_predust == $jp->id ? 'selected' : '' }}>{{ $jp->jenis_predust }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Kode Produksi Predust</label>
                                    <input type="text" name="kode_produksi_predust" class="form-control" value="{{ $predustData->kode_produksi }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Checklist Hasil Predust</label>
                                    <select name="kondisi_predust" class="form-control">
                                        <option value="">-- Pilih Hasil Predust --</option>
                                        <option value="✔" {{ $predustData->kondisi_predust == '✔' ? 'selected' : '' }}>✔ OK</option>
                                        <option value="✘" {{ $predustData->kondisi_predust == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="card card-secondary card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-mortar-pestle mr-1 text-secondary"></i> 2. Predusting <span class="badge badge-secondary ml-2">(Tidak Menggunakan Line Predust / Dilewati)</span></h3>
                    </div>
                </div>
                @endif

                {{-- SECTION 3: BATTERING --}}
                @if($pembekuan->batteringData)
                <div class="card card-success card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-tint mr-1 text-success"></i> 3. Battering</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jenis Better</label>
                                    @php
                                        $selectedBetter = $pembekuan->batteringData->id_jenis_better 
                                            ? array_map('trim', explode(',', $pembekuan->batteringData->id_jenis_better)) 
                                            : [];
                                    @endphp
                                    <select name="id_jenis_better[]" class="form-control select2" multiple="multiple" data-placeholder="Pilih Jenis Better">
                                        @foreach($jenisBetters as $jb)
                                            <option value="{{ $jb->id }}" {{ in_array($jb->id, $selectedBetter) ? 'selected' : '' }}>{{ $jb->nama_formula_better ?? $jb->nama_better }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kode Produksi Better</label>
                                    <div id="kode_produksi_better_container">
                                        @php
                                            $codes = $pembekuan->batteringData->kode_produksi_better;
                                            $selectedBetterIds = array_map('trim', explode(',', $pembekuan->batteringData->id_jenis_better));
                                        @endphp
                                        @if(is_array($codes))
                                            @foreach($jenisBetters as $jb)
                                                @if(in_array($jb->id, $selectedBetterIds))
                                                    <div class="input-group mb-2">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" style="min-width: 150px;">{{ $jb->nama_formula_better ?? $jb->nama_better }}</span>
                                                        </div>
                                                        <input type="text" name="kode_produksi_better[{{ $jb->id }}]" class="form-control" value="{{ $codes[$jb->id] ?? '' }}" required>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <input type="text" name="kode_produksi_better" class="form-control" value="{{ $pembekuan->batteringData->kode_produksi_better }}">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Checklist Hasil Batter</label>
                                    <select name="hasil_better" class="form-control">
                                        <option value="">-- Pilih Hasil Batter --</option>
                                        <option value="✔" {{ $pembekuan->batteringData->hasil_better == '✔' ? 'selected' : '' }}>✔ OK</option>
                                        <option value="✘" {{ $pembekuan->batteringData->hasil_better == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- SECTION 4: BREADER --}}
                @if($pembekuan->breaderData)
                <div class="card card-warning card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-bread-slice mr-1 text-warning"></i> 4. Breader</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jenis Breader</label>
                                    @php
                                        $selectedBreader = $pembekuan->breaderData->id_jenis_breader 
                                            ? array_map('trim', explode(',', $pembekuan->breaderData->id_jenis_breader)) 
                                            : [];
                                    @endphp
                                    <select name="id_jenis_breader[]" class="form-control select2" multiple="multiple" data-placeholder="Pilih Jenis Breader">
                                        @foreach($jenisBreaders as $jbr)
                                            <option value="{{ $jbr->id }}" {{ in_array($jbr->id, $selectedBreader) ? 'selected' : '' }}>{{ $jbr->jenis_breader }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kode Produksi Breader</label>
                                    <div id="kode_produksi_breader_container">
                                        @php
                                            $codesB = $pembekuan->breaderData->kode_produksi;
                                            $selectedBreaderIds = array_map('trim', explode(',', $pembekuan->breaderData->id_jenis_breader));
                                        @endphp
                                        @if(is_array($codesB))
                                            @foreach($jenisBreaders as $jbr)
                                                @if(in_array($jbr->id, $selectedBreaderIds))
                                                    <div class="input-group mb-2">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" style="min-width: 150px;">{{ $jbr->jenis_breader }}</span>
                                                        </div>
                                                        <input type="text" name="kode_produksi_breader[{{ $jbr->id }}]" class="form-control" value="{{ $codesB[$jbr->id] ?? '' }}" required>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <input type="text" name="kode_produksi_breader" class="form-control" value="{{ $pembekuan->breaderData->kode_produksi }}">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Checklist Hasil Breader</label>
                                    <select name="hasil_breader" class="form-control">
                                        <option value="">-- Pilih Hasil Breader --</option>
                                        <option value="✔" {{ $pembekuan->breaderData->hasil_breader == '✔' ? 'selected' : '' }}>✔ OK</option>
                                        <option value="✘" {{ $pembekuan->breaderData->hasil_breader == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- SECTION 5: FRYING (PRODUKSI) --}}
                @php
                    $frayerList = [
                        1 => ['data' => $frayer1, 'label' => 'Fryer 1', 'suhu' => $suhuFrayer1, 'waktu' => $waktuPenggorengan, 'suhu_key' => 'id_suhu_frayer_1', 'waktu_key' => 'id_waktu_penggorengan'],
                        2 => ['data' => $frayer2, 'label' => 'Fryer 2', 'suhu' => $suhuFrayer2, 'waktu' => $waktuPenggorengan2, 'suhu_key' => 'id_suhu_frayer_2', 'waktu_key' => 'id_waktu_penggorengan_2'],
                        3 => ['data' => $frayer3, 'label' => 'Fryer 3', 'suhu' => $suhuFrayer1, 'waktu' => $waktuPenggorengan, 'suhu_key' => 'id_suhu_frayer', 'waktu_key' => 'id_waktu_penggorengan'],
                        4 => ['data' => $frayer4, 'label' => 'Fryer 4', 'suhu' => $suhuFrayer1, 'waktu' => $waktuPenggorengan, 'suhu_key' => 'id_suhu_frayer', 'waktu_key' => 'id_waktu_penggorengan'],
                        5 => ['data' => $frayer5, 'label' => 'Fryer 5', 'suhu' => $suhuFrayer1, 'waktu' => $waktuPenggorengan, 'suhu_key' => 'id_suhu_frayer', 'waktu_key' => 'id_waktu_penggorengan'],
                    ];
                    $hasAnyFryer = $frayer1 || $frayer2 || $frayer3 || $frayer4 || $frayer5;
                @endphp

                @if($hasAnyFryer)
                <div class="card card-danger card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-burn mr-1 text-danger"></i> 5. Frying</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="bg-light">
                                    <tr class="text-center">
                                        <th>Mesin</th>
                                        <th>Suhu Std</th>
                                        <th>Waktu Std</th>
                                        <th>Aktual Suhu</th>
                                        <th>Aktual Waktu</th>
                                        <th>TPM (%)</th>
                                        <th>FFA</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($frayerList as $num => $f)
                                    @if($f['data'])
                                    <tr>
                                        <td class="text-center align-middle font-weight-bold">{{ $f['label'] }}</td>
                                        <td>
                                            <select name="frayer_{{ $num }}_suhu" class="form-control form-control-sm" disabled>
                                                <option value="">- Pilih -</option>
                                                @foreach($f['suhu'] as $s)
                                                    @php
                                                        $valSuhu = $num == 1 ? $s->suhu_frayer : ($num == 2 ? $s->suhu_frayer_2 : $s->suhu_frayer_3 ?? $s->suhu_frayer_1 ?? $s->suhu_frayer);
                                                        $currSuhu = $f['data']->{$f['suhu_key']};
                                                    @endphp
                                                    <option value="{{ $s->id }}" {{ $currSuhu == $s->id ? 'selected' : '' }}>{{ $valSuhu }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="frayer_{{ $num }}_suhu" value="{{ $f['data']->{$f['suhu_key']} }}">
                                            <input type="hidden" name="frayer_{{ $num }}_jam" value="{{ $f['data']->jam }}">
                                        </td>
                                        <td>
                                            <select name="frayer_{{ $num }}_waktu" class="form-control form-control-sm" disabled>
                                                <option value="">- Pilih -</option>
                                                @foreach($f['waktu'] as $w)
                                                    @php
                                                        $valWaktu = $num == 2 ? $w->waktu_penggorengan_2 : ($num == 1 ? $w->waktu_penggorengan : $w->waktu_penggorengan_3 ?? $w->waktu_penggorengan_1 ?? $w->waktu_penggorengan);
                                                        $currWaktu = $f['data']->{$f['waktu_key']};
                                                    @endphp
                                                    <option value="{{ $w->id }}" {{ $currWaktu == $w->id ? 'selected' : '' }}>{{ $valWaktu }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="frayer_{{ $num }}_waktu" value="{{ $f['data']->{$f['waktu_key']} }}">
                                        </td>
                                        <td>
                                            <input type="text" name="frayer_{{ $num }}_aktual_suhu" class="form-control form-control-sm" value="{{ $f['data']->aktual_suhu_penggorengan }}">
                                        </td>
                                        <td>
                                            <input type="text" name="frayer_{{ $num }}_aktual_waktu" class="form-control form-control-sm" value="{{ $f['data']->aktual_penggorengan }}">
                                        </td>
                                        <td>
                                            <input type="text" name="frayer_{{ $num }}_tpm" class="form-control form-control-sm" value="{{ $f['data']->tpm_minyak }}">
                                        </td>
                                        <td>
                                            @if(in_array($num, [1, 2]))
                                            <input type="text" name="frayer_{{ $num }}_ffa" class="form-control form-control-sm" value="{{ $f['data']->ffa ?? '' }}">
                                            @else
                                            <span class="text-muted text-center d-block">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="text" name="frayer_{{ $num }}_keterangan" class="form-control form-control-sm" value="{{ $f['data']->keterangan ?? '' }}">
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                {{-- SECTION 6: HASIL PENGGORENGAN (HANYA UNTUK NON-PRODUKSI) --}}
                @if(!$isProduksiRole)
                    @if($pembekuan->hasilPenggorenganData)
                    <div class="card card-info card-outline shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-check-double mr-1 text-info"></i> 6. Hasil Penggorengan (QC)</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Std Suhu Pusat</label>
                                        <select name="id_std_suhu_pusat" class="form-control" disabled>
                                            @foreach($stdSuhuPusat as $std)
                                                @php
                                                    $stdArr = $std->std_suhu_pusat;
                                                    $finalStd = 'N/A';
                                                    
                                                    if (is_array($stdArr)) {
                                                        if ($frayer5) $finalStd = $stdArr[4] ?? 'N/A';
                                                        elseif ($frayer4) $finalStd = $stdArr[3] ?? 'N/A';
                                                        elseif ($frayer3) $finalStd = $stdArr[2] ?? 'N/A';
                                                        elseif ($frayer2) $finalStd = $stdArr[1] ?? 'N/A';
                                                        elseif ($frayer1) $finalStd = $stdArr[0] ?? 'N/A';
                                                    } else {
                                                        $finalStd = $stdArr;
                                                    }
                                                    
                                                    $label = "[" . $finalStd . " °C]";
                                                @endphp
                                                <option value="{{ $std->id }}" {{ $pembekuan->hasilPenggorenganData->id_std_suhu_pusat == $std->id ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="id_std_suhu_pusat" value="{{ $pembekuan->hasilPenggorenganData->id_std_suhu_pusat }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Aktual Suhu Pusat (°C)</label>
                                        <div id="edit_aktual_suhu_pusat_container">
                                            @php
                                                $suhuPusatRaw = $pembekuan->hasilPenggorenganData->aktual_suhu_pusat ?? '';
                                                $suhuPusatList = is_string($suhuPusatRaw) ? array_map('trim', explode(',', $suhuPusatRaw)) : (is_array($suhuPusatRaw) ? $suhuPusatRaw : []);
                                                $suhuPusatList = array_filter($suhuPusatList, fn($v) => $v !== '' && !is_null($v));
                                                if(empty($suhuPusatList)) { $suhuPusatList = ['']; }
                                            @endphp
                                            @foreach($suhuPusatList as $idx => $suhuVal)
                                                <div class="input-group mb-2">
                                                    <input type="text" name="aktual_suhu_pusat[]" class="form-control" value="{{ $suhuVal }}" placeholder="°C" required>
                                                    <div class="input-group-append">
                                                        @if($loop->first)
                                                            <button class="btn btn-outline-success btn-add-edit-suhu-pusat" type="button"><i class="fas fa-plus"></i></button>
                                                        @else
                                                            <button class="btn btn-outline-danger btn-remove-edit-suhu-pusat" type="button"><i class="fas fa-minus"></i></button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Keterangan</label>
                                        <textarea name="keterangan" class="form-control" rows="2">{{ $pembekuan->hasilPenggorenganData->keterangan }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row text-center mt-3">
                                @foreach(['kematangan', 'kenampakan', 'warna', 'rasa', 'bau', 'tekstur'] as $sen)
                                <div class="col-md-2 col-4">
                                    <div class="form-group">
                                        <label class="text-capitalize">{{ $sen }}</label>
                                        @php $valSen = $pembekuan->hasilPenggorenganData->{'sensori_'.$sen}; @endphp
                                        <select name="sensori_{{ $sen }}" class="form-control">
                                            <option value="✔" {{ $valSen == '✔' ? 'selected' : '' }}>✔</option>
                                            <option value="✘" {{ $valSen == '✘' ? 'selected' : '' }}>✘</option>
                                        </select>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                @endif

                <div class="card-footer text-right mb-5">
                    <a href="{{ route('wizard.proses1.show', $pembekuan->uuid) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary ml-2">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            allowClear: true
        });

        // Better Dynamic Logic
        const $selBetter = $('select[name="id_jenis_better[]"]');
        const $containerBetter = $('#kode_produksi_better_container');

        $selBetter.on('change', function() {
            const selectedValues = $(this).val() || [];
            
            if (selectedValues.length === 0) {
                $containerBetter.html('<input type="text" name="kode_produksi_better" class="form-control" placeholder="Pilih Jenis Better" readonly>');
                return;
            }

            // Simpan nilai lama
            const oldValues = {};
            $containerBetter.find('input').each(function() {
                const name = $(this).attr('name');
                if (name && name.includes('[') && name.includes(']')) {
                    const id = name.match(/\[(.*?)\]/)[1];
                    oldValues[id] = $(this).val();
                }
            });

            let html = '';
            $(this).find('option:selected').each(function() {
                const id = $(this).val();
                const text = $(this).text();
                const val = oldValues[id] || '';
                html += `
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="min-width: 150px;">${text}</span>
                        </div>
                        <input type="text" name="kode_produksi_better[${id}]" class="form-control" value="${val}" placeholder="Masukkan Kode Produksi" required>
                    </div>
                `;
            });
            $containerBetter.html(html);
        });

        // Breader Dynamic Logic
        const $selBreader = $('select[name="id_jenis_breader[]"]');
        const $containerBreader = $('#kode_produksi_breader_container');

        $selBreader.on('change', function() {
            const selectedValues = $(this).val() || [];
            
            if (selectedValues.length === 0) {
                $containerBreader.html('<input type="text" name="kode_produksi_breader" class="form-control" placeholder="Pilih Jenis Breader" readonly>');
                return;
            }

            const oldValues = {};
            $containerBreader.find('input').each(function() {
                const name = $(this).attr('name');
                if (name && name.includes('[') && name.includes(']')) {
                    const id = name.match(/\[(.*?)\]/)[1];
                    oldValues[id] = $(this).val();
                }
            });

            let html = '';
            $(this).find('option:selected').each(function() {
                const id = $(this).val();
                const text = $(this).text();
                const val = oldValues[id] || '';
                html += `
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="min-width: 150px;">${text}</span>
                        </div>
                        <input type="text" name="kode_produksi_breader[${id}]" class="form-control" value="${val}" placeholder="Masukkan Kode Produksi" required>
                    </div>
                `;
            });
            $containerBreader.html(html);
        });

        // Edit Dynamic Suhu Pusat Add/Remove
        $(document).on('click', '.btn-add-edit-suhu-pusat', function() {
            const html = `
                <div class="input-group mb-2">
                    <input type="text" name="aktual_suhu_pusat[]" class="form-control" placeholder="°C" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-danger btn-remove-edit-suhu-pusat" type="button"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
            `;
            $('#edit_aktual_suhu_pusat_container').append(html);
        });

        $(document).on('click', '.btn-remove-edit-suhu-pusat', function() {
            $(this).closest('.input-group').remove();
        });
    });
</script>
@endpush
@endsection
