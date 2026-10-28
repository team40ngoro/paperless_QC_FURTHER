@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-edit text-primary"></i> Edit Verifikasi Proses Roasted Produk</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('wizard.proses3.index') }}">Roasted Product</a></li>
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

            <form action="{{ route('wizard.proses3.update', $pembekuan->uuid) }}" method="POST" id="main-edit-form">
                @csrf
                @method('PUT')

                {{-- SECTION: INFO UTAMA & IQF --}}
                <div class="card card-dark card-outline shadow-sm mb-4">
                    <div class="card-header bg-dark">
                        <h3 class="card-title font-weight-bold text-white"><i class="fas fa-snowflake mr-1"></i> Info Utama & IQF</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nama Produk <span class="text-danger">*</span></label>
                                    <select name="id_produk" class="form-control select2-field" required>
                                        @foreach($produks as $p)
                                            <option value="{{ $p->id }}" {{ ($pembekuan->hasilProsesRoastingData->id_produk ?? '') == $p->id ? 'selected' : '' }}>{{ $p->nama_produk }}</option>
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
                            <div class="col-md-4" style="display: none;">
                                <div class="form-group">
                                    <label>Jam <span class="text-danger">*</span></label>
                                    <input type="time" name="jam" class="form-control" value="{{ $pembekuan->jam }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Shift <span class="text-danger">*</span></label>
                                    <select name="id_shift" class="form-control" required>
                                        @foreach($shifts as $s)
                                            <option value="{{ $s->id }}" {{ $pembekuan->inputRoastingData->shift_id == $s->id ? 'selected' : '' }}>{{ $s->shift }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Suhu Ruang IQF (°C) <span class="text-danger">*</span></label>
                                    <input type="text" name="suhu_ruang_iqf" class="form-control" value="{{ $pembekuan->suhu_ruang_iqf }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Holding Time (Menit) <span class="text-danger">*</span></label>
                                    <input type="text" name="holding_time" class="form-control" value="{{ $pembekuan->holding_time }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 1: INPUT ROASTING (Bahan Baku) --}}
                @if($pembekuan->inputRoastingData)
                <div class="card card-info card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold text-info"><i class="fas fa-boxes mr-1"></i> 1. Input Roasting (Bahan Baku)</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Kode Produksi <span class="text-danger">*</span></label>
                                    <input type="text" name="kode_produksi" class="form-control" value="{{ $pembekuan->inputRoastingData->kode_produksi }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Berat Bahan (gr) <span class="text-danger">*</span></label>
                                    <select name="berat_bahan" id="edit_nilai_select_berat_bahan" class="form-control select2" data-nilai-berat="true" data-selected="{{ $pembekuan->inputRoastingData->berat_produk }}"></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Std Suhu Sebelum (°C) <span class="text-danger">*</span></label>
                                    <input type="text" name="std_suhu_sebelum" class="form-control" value="{{ $pembekuan->inputRoastingData->std_suhu_sebelum }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Aktual Suhu Sesudah (°C) <span class="text-danger">*</span></label>
                                    <input type="text" name="aktual_suhu_sesudah" class="form-control" value="{{ $pembekuan->inputRoastingData->aktual_suhu_sesudah }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- SECTION 2: ROASTING FAN --}}
                @if($pembekuan->prosesRoastingFanData)
                <div class="card card-primary card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold text-primary"><i class="fas fa-fan mr-1"></i> 2. Roasting Fan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Waktu Pemasakan (pukul)</label>
                                    <input type="text" name="aktual_waktu" class="form-control" value="{{ $pembekuan->prosesRoastingFanData->aktual_waktu }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Waktu Pengecekan (menit)</label>
                                    <input type="text" name="waktu_pemasakan_roasting" class="form-control" value="{{ $pembekuan->prosesRoastingFanData->waktu_pemasakan }}">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm text-center">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th style="width: 250px;" class="text-left">Parameter</th>
                                        @php
                                            $blockData = $pembekuan->prosesRoastingFanData->blok_data;
                                            if (is_string($blockData)) {
                                                $blockData = json_decode($blockData, true) ?: [];
                                            }
                                            $sortedBlok = collect($blockData)->sortBy('block_number');
                                        @endphp
                                        @foreach($sortedBlok as $blok)
                                            <th>
                                                Blok {{ $blok['block_number'] ?? ($loop->index + 1) }}
                                                <input type="hidden" name="block_number[]" value="{{ $blok['block_number'] ?? ($loop->index + 1) }}">
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-left font-weight-bold bg-light">Standar Suhu Pemasakan (°C)</td>
                                        @foreach($sortedBlok as $blok)
                                        <td>
                                            <select class="form-control form-control-sm text-center" disabled>
                                                <option value="">Pilih</option>
                                                @foreach($suhuBloks as $sb)
                                                    <option value="{{ $sb->id }}" {{ ($blok['id_suhu_blok'] ?? '') == $sb->id ? 'selected' : '' }}>{{ $sb->suhu_blok }} °C</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="id_suhu_blok[]" value="{{ $blok['id_suhu_blok'] ?? '' }}">
                                            <input type="hidden" name="id_std_fan[]" value="{{ $blok['id_std_fan'] ?? '' }}">
                                        </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td class="text-left font-weight-bold bg-light">Suhu Pemasakan (Aktual) (°C)</td>
                                        @foreach($sortedBlok as $blok)
                                        <td>
                                            <input type="text" name="suhu_roasting[]" class="form-control form-control-sm text-center" value="{{ $blok['suhu_roasting'] ?? '' }}">
                                        </td>
                                        @endforeach
                                    </tr>

                                    {{-- INDIVIDUAL FAN ROWS --}}
                                    @php
                                        $fanParams = [
                                            ['label' => 'Fan 1', 'field' => 'fan_1', 'std' => 'std_fan'],
                                            ['label' => 'Fan 2', 'field' => 'fan_2', 'std' => 'std_fan_2'],
                                            ['label' => 'Fan 3', 'field' => 'fan_3', 'std' => 'fan_3'],
                                            ['label' => 'Fan 4', 'field' => 'fan_4', 'std' => 'fan_4'],
                                        ];
                                    @endphp

                                    @foreach($fanParams as $param)
                                    <tr class="table-info-light">
                                        <td class="text-left font-weight-bold">Standar {{ $param['label'] }} (%)</td>
                                        @foreach($sortedBlok as $blok)
                                            @php
                                                $stdVal = '-';
                                                if(!empty($blok['id_std_fan'])) {
                                                    $sfRecord = $stdFans->firstWhere('id', $blok['id_std_fan']);
                                                    $stdVal = $sfRecord ? ($sfRecord->{$param['std']} . ' %') : '-';
                                                }
                                            @endphp
                                            <td class="bg-light text-center"><code>{{ $stdVal }}</code></td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td class="text-left font-weight-bold">{{ $param['label'] }} (Aktual) (%)</td>
                                        @foreach($sortedBlok as $blok)
                                        <td>
                                            <input type="text" name="{{ $param['field'] }}[]" class="form-control form-control-sm text-center" value="{{ $blok[$param['field']] ?? '' }}">
                                            <input type="hidden" name="id_std_fan[]" value="{{ $blok['id_std_fan'] ?? '' }}">
                                        </td>
                                        @endforeach
                                    </tr>
                                    @endforeach

                                    {{-- HUMIDITY --}}
                                    <tr class="table-info-light">
                                        <td class="text-left font-weight-bold">Standar Humidity (%)</td>
                                        @foreach($sortedBlok as $blok)
                                            @php
                                                $stdVal = '-';
                                                if(!empty($blok['id_std_fan'])) {
                                                    $sfRecord = $stdFans->firstWhere('id', $blok['id_std_fan']);
                                                    $stdVal = $sfRecord ? ($sfRecord->std_humadity . ' %') : '-';
                                                }
                                            @endphp
                                            <td class="bg-light text-center"><code>{{ $stdVal }}</code></td>
                                        @endforeach
                                    </tr>
                                    @php
                                        $mergedHumEdit3 = null;
                                        $mergedIrEdit3 = null;
                                        foreach($sortedBlok as $b) {
                                            if(in_array($b['block_number'] ?? '', ['INFEED','OUTFEED'])) {
                                                if(is_null($mergedHumEdit3)) $mergedHumEdit3 = $b['aktual_humadity'] ?? '';
                                                if(is_null($mergedIrEdit3)) $mergedIrEdit3 = $b['infra_red'] ?? '';
                                            }
                                        }
                                        $edit3SkipH = false;
                                        $edit3SkipI = false;
                                    @endphp
                                    <tr>
                                        <td class="text-left font-weight-bold">Aktual Humidity (%)</td>
                                        @foreach($sortedBlok as $blok)
                                            @if(($blok['block_number'] ?? '') === 'INFEED')
                                                <td colspan="2">
                                                    <input type="text" name="aktual_humadity_infeed_outfeed" class="form-control form-control-sm text-center" value="{{ $mergedHumEdit3 }}">
                                                </td>
                                                @php $edit3SkipH = true; @endphp
                                            @elseif(($blok['block_number'] ?? '') === 'OUTFEED' && $edit3SkipH)
                                                {{-- merged into INFEED --}}
                                            @else
                                                <td>
                                                    <input type="text" name="aktual_humadity[]" class="form-control form-control-sm text-center" value="{{ $blok['aktual_humadity'] ?? '' }}">
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>

                                    {{-- INFRA RED --}}
                                    <tr>
                                        <td class="text-left font-weight-bold bg-light">Infra Red</td>
                                        @foreach($sortedBlok as $blok)
                                            @if(($blok['block_number'] ?? '') === 'INFEED')
                                                <td colspan="2">
                                                    <select name="infra_red_infeed_outfeed" class="form-control form-control-sm text-center">
                                                        <option value="">Pilih</option>
                                                        @foreach([1, 2, 3] as $irVal)
                                                            <option value="{{ $irVal }}" {{ (string)($mergedIrEdit3) === (string)$irVal ? 'selected' : '' }}>{{ $irVal }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                @php $edit3SkipI = true; @endphp
                                            @elseif(($blok['block_number'] ?? '') === 'OUTFEED' && $edit3SkipI)
                                                {{-- merged into INFEED --}}
                                            @else
                                                <td>
                                                    <select name="infra_red[]" class="form-control form-control-sm text-center">
                                                        <option value="">Pilih</option>
                                                        @foreach([1, 2, 3] as $irVal)
                                                            <option value="{{ $irVal }}" {{ (string)($blok['infra_red'] ?? '') === (string)$irVal ? 'selected' : '' }}>{{ $irVal }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>

                                    {{-- LAMA PROSES --}}
                                    <tr>
                                        <td class="text-left font-weight-bold bg-light">Standar Lama Proses (Menit)</td>
                                        @php
                                            $firstBlok = $sortedBlok->first();
                                            $stdLama = $firstBlok ? $stdFans->firstWhere('id', $firstBlok['id_std_fan']) : null;
                                        @endphp
                                        <td colspan="{{ count($sortedBlok) }}">
                                            <input type="text" class="form-control form-control-sm text-center" value="{{ $stdLama->std_lama_proses ?? '-' }}" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left font-weight-bold bg-light">Lama Proses (Display Aktual; Menit)</td>
                                        <td colspan="{{ count($sortedBlok) }}">
                                            <input type="text" name="aktual_lama_proses_roasting" class="form-control form-control-sm text-center" value="{{ $pembekuan->prosesRoastingFanData->aktual_lama_proses ?? '' }}">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                {{-- SECTION 3: HASIL ROASTING --}}
                @if($pembekuan->hasilProsesRoastingData)
                <div class="card card-success card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold text-success"><i class="fas fa-check-double mr-1"></i> 3. Hasil Roasting</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jam<span class="text-danger">*</span></label>
                                    <input type="time" name="jam_hasil" class="form-control" value="{{ $pembekuan->hasilProsesRoastingData->jam }}" required>
                                </div>
                            </div> --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Std Suhu Pusat <span class="text-danger">*</span></label>
                                    <select name="id_std_suhu_pusat_disabled" class="form-control select2-field" disabled>
                                        <option value="">Pilih Std</option>
                                        @foreach($stdSuhuPusat as $s)
                                            <option value="{{ $s->id }}" {{ $pembekuan->hasilProsesRoastingData->id_std_suhu_pusat == $s->id ? 'selected' : '' }}>{{ $s->std_suhu_pusat_roasting }} °C</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="id_std_suhu_pusat" value="{{ $pembekuan->hasilProsesRoastingData->id_std_suhu_pusat }}">
                                </div>
                            </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Aktual Suhu Pusat (°C) <span class="text-danger">*</span></label>
                                        <div id="aktual_suhu_pusat_container">
                                            @php
                                                $suhuPusatRaw = $pembekuan->hasilProsesRoastingData->aktual_suhu_pusat ?? '';
                                                $suhuPusatList = is_array($suhuPusatRaw) ? $suhuPusatRaw : (is_string($suhuPusatRaw) ? array_map('trim', preg_split('/[\/,;]+/', $suhuPusatRaw)) : []);
                                                $suhuPusatList = array_values(array_filter($suhuPusatList, fn($v) => $v !== '' && !is_null($v)));
                                                if (empty($suhuPusatList)) { $suhuPusatList = ['']; }
                                            @endphp
                                            @foreach($suhuPusatList as $idx => $suhuVal)
                                                <div class="input-group mb-2">
                                                    <input type="text" name="aktual_suhu_pusat[]" class="form-control" value="{{ $suhuVal }}" placeholder="Masukkan Aktual Suhu Pusat" required>
                                                    <div class="input-group-append">
                                                        @if($loop->first)
                                                            <button type="button" class="btn btn-success btn-add-suhu-pusat"><i class="fas fa-plus"></i></button>
                                                        @else
                                                            <button type="button" class="btn btn-danger btn-remove-suhu-pusat"><i class="fas fa-minus"></i></button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Keterangan</label>
                                        <textarea name="keterangan" class="form-control" rows="2">{{ $pembekuan->hasilProsesRoastingData->keterangan }}</textarea>
                                    </div>
                                </div>
                            </div>

                        <div class="row mt-3">
                            @php
                                $sensoriData = $pembekuan->hasilProsesRoastingData->sensori;
                            @endphp
                            @foreach(['kematangan','kenampakan','warna','rasa','bau','tekstur'] as $sen)
                            @php 
                                $field = 'sensori_'.$sen; 
                                $val = $sensoriData[$field] ?? ($pembekuan->hasilProsesRoastingData->$field ?? '');
                            @endphp
                            <div class="col-md-2 col-6 mb-2 text-center">
                                <label class="text-capitalize small font-weight-bold">{{ $sen }}</label>
                                <select name="sensori_{{ $sen }}" class="form-control form-control-sm">
                                    <option value="✔" {{ $val == '✔' ? 'selected' : '' }}>✔ OK</option>
                                    <option value="✘" {{ $val == '✘' ? 'selected' : '' }}>✘ NO</option>
                                </select>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- ACTION BUTTONS --}}
                <div class="card-footer text-right mb-5 shadow-sm">
                    <a href="{{ route('wizard.proses3.show', $pembekuan->uuid) }}" class="btn btn-secondary">
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
// Dynamic Suhu Pusat Add/Remove (Global Event Handler)
$(document).on('click', '.btn-add-suhu-pusat', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var container = $('#aktual_suhu_pusat_container');
    if (container.length) {
        var newRow = `
            <div class="input-group mb-2">
                <input type="text" name="aktual_suhu_pusat[]" class="form-control" placeholder="Masukkan Aktual Suhu Pusat" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger btn-remove-suhu-pusat"><i class="fas fa-minus"></i></button>
                </div>
            </div>
        `;
        container.append(newRow);
    }
});

$(document).on('click', '.btn-remove-suhu-pusat', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).closest('.input-group').remove();
});

$(document).ready(function() {
    $('.select2-field').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Pilih Opsi',
        allowClear: true
    });

    // Inisialisasi Berat Produk/Bahan
    if (typeof initSelectBeratEdit === 'function') {
        const beratVal = $('#edit_nilai_select_berat_bahan').data('selected');
        initSelectBeratEdit(beratVal, '#edit_nilai_select_berat_bahan');
    }

    $('#main-edit-form').on('submit', function(e) {
        if (!confirm('Apakah Anda yakin ingin menyimpan semua perubahan data ini?')) {
            e.preventDefault();
        }
    });
});
</script>

@endpush
@endsection
