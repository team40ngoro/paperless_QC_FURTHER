
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-edit text-warning"></i> Edit Verifikasi Persiapan Cold Mixing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('persiapan-bahan-forming.index') }}">Persiapan Bahan</a></li>
                        <li class="breadcrumb-item active">Edit Non Forming</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <form method="POST" action="{{ route('persiapan-bahan-non-forming.update', $data->uuid) }}">
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Produk</label>
                                            <input type="text" class="form-control" value="{{ $data->formulaNonForming->produk->nama_produk ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nomor Formula</label>
                                            <input type="text" class="form-control" value="{{ $data->formulaNonForming->nomor_formula ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Shift</label>
                                            <select name="shift_id" class="form-control" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ old('shift_id', $data->shift_id) == $shift->id ? 'selected' : '' }}>{{ $shift->shift }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tanggal <span class="text-danger">*</span></label>
                                            @php
                                                $tanggalVal = $data->tanggal ? \Carbon\Carbon::parse($data->tanggal)->format('Y-m-d') : '';
                                            @endphp
                                            <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $tanggalVal) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Jam <span class="text-danger">*</span></label>
                                            @php
                                                $jamVal = $data->jam ? \Carbon\Carbon::parse($data->jam)->format('H:i') : '';
                                            @endphp
                                            <input type="time" name="jam" class="form-control" value="{{ old('jam', $jamVal) }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Kode Produksi</label>
                                            <input type="text" name="kode_produksi" class="form-control" value="{{ old('kode_produksi', $data->kode_produksi) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Gramase (gram)</label>
                                            <select name="berat_sampling" id="nilai_select_berat_sampling" class="form-control" data-nilai-berat data-selected="{{ old('berat_sampling', $data->berat_sampling) }}"></select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Kode Produksi Emulsi Oil</label>
                                    @php
                                        $kodeOilOld = old('kode_produksi_emulsi_oil');
                                        $kodeOil = is_array($kodeOilOld) ? $kodeOilOld : (is_array($data->kode_produksi_emulsi_oil) ? $data->kode_produksi_emulsi_oil : []);
                                        if (empty($kodeOil)) {
                                            $kodeOil = [''];
                                        }
                                    @endphp
                                    <div id="kode-emulsi-oil-container-non-forming">
                                        @foreach($kodeOil as $idx => $val)
                                            <div class="input-group mb-2 kode-emulsi-oil-item-non-forming">
                                                <input type="text" name="kode_produksi_emulsi_oil[]" class="form-control" placeholder="Masukkan kode produksi emulsi oil" value="{{ $val }}">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-success btn-sm add-kode-emulsi-oil-non-forming" {{ $idx === 0 ? '' : 'style=display:none;' }}>
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm remove-kode-emulsi-oil-non-forming" {{ count($kodeOil) <= 1 ? 'style=display:none;' : '' }}>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Waktu Mulai Mixing</label>
                                            <input type="time" name="waktu_mulai_mixing" class="form-control" value="{{ old('waktu_mulai_mixing', $data->waktu_mulai_mixing) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Waktu Selesai Mixing</label>
                                            <input type="time" name="waktu_selesai_mixing" class="form-control" value="{{ old('waktu_selesai_mixing', $data->waktu_selesai_mixing) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Suhu Adonan (STD)</label>
                                            <select name="id_suhu_adonan" class="form-control">
                                                <option value="">Pilih Suhu Adonan</option>
                                                @foreach($suhuAdonan as $sa)
                                                    <option value="{{ $sa->id }}" {{ old('id_suhu_adonan', $data->id_suhu_adonan) == $sa->id ? 'selected' : '' }}>STD: {{ $sa->std_suhu }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Kondisi</label>
                                            <select name="kondisi" class="form-control">
                                                <option value="✔" {{ old('kondisi', $data->kondisi) == '✔' ? 'selected' : '' }}>✔ OK</option>
                                                <option value="✘" {{ old('kondisi', $data->kondisi) == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Rework</label>
                                            <input type="text" name="rework" class="form-control" value="{{ old('rework', $data->rework) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="mt-2">Suhu Aktual</h5>
                                    </div>
                                    @for($i=1;$i<=5;$i++)
                                        <div class="form-group col-md-2">
                                            <label>Aktual {{ $i }}</label>
                                            <input type="number" step="0.1" name="aktual_suhu_{{ $i }}" class="form-control" placeholder="0.0" id="aktual_suhu_{{ $i }}" value="{{ old('aktual_suhu_'.$i, optional($data->aktualSuhuAdonan)->{'aktual_suhu_'.$i}) }}">
                                        </div>
                                    @endfor
                                    <div class="form-group col-md-2">
                                        <label>Hasil Suhu Aktual</label>
                                        <input type="number" step="0.01" name="total_aktual_suhu" class="form-control" id="total_aktual_suhu" placeholder="Otomatis terisi" readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Catatan</label>
                                    <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $data->catatan) }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Detail Bahan Non Forming</label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th class="text-center">No</th>
                                                    <th>Nama RM</th>
                                                    <th class="text-center">Berat RM</th>
                                                    <th class="text-center">Kode Produksi Bahan</th>
                                                    <th class="text-center">Suhu RM</th>
                                                    <th class="text-center">Sensori</th>
                                                    <th class="text-center">Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data->details as $i => $d)
                                                    <tr data-berat-rm="{{ $d->bahanNonForming->berat_rm ?? 0 }}">
                                                        <td class="text-center">{{ $i + 1 }}</td>
                                                        <td>{{ $d->bahanNonForming->nama_rm ?? '-' }}</td>
                                                        <td class="text-center">{{ $d->bahanNonForming->berat_rm ?? '-' }}</td>
                                                        <td>
                                                            <input type="hidden" name="id_bahan_non_forming[]" value="{{ $d->id_bahan_non_forming }}">
                                                            <input type="text" name="kode_produksi_bahan[]" class="form-control" value="{{ old('kode_produksi_bahan.' . $i, $d->kode_produksi_bahan) }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="suhu[]" class="form-control" value="{{ old('suhu.' . $i, $d->suhu) }}">
                                                        </td>
                                                        <td>
                                                            <select name="sensori[]" class="form-control">
                                                                <option value="OK" {{ old('sensori.'.$i, $d->sensori) == 'OK' ? 'selected' : '' }}>OK</option>
                                                                <option value="Tidak OK" {{ old('sensori.'.$i, $d->sensori) == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="keterangan_bahan[]" class="form-control" value="{{ old('keterangan_bahan.'.$i, $d->keterangan) }}" placeholder="Keterangan">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            @php
                                                $getFloatEditNF = function($val) {
                                                    if (is_null($val) || $val === '' || is_array($val)) return 0.0;
                                                    $clean = preg_replace('/[^\d\.\-]/', '', str_replace(',', '.', (string)$val));
                                                    return is_numeric($clean) ? (float)$clean : 0.0;
                                                };
                                                $beratListEditNF = collect($data->details)->map(fn($d) => $getFloatEditNF($d->bahanNonForming->berat_rm ?? 0));
                                                $totalBeratEditNF = $beratListEditNF->sum();
                                                $avgBeratEditNF   = $beratListEditNF->count() > 0 ? $beratListEditNF->avg() : 0;
                                            @endphp
                                            <tfoot class="bg-light font-weight-bold" id="tfoot-bahan-non-forming-edit">
                                                <tr>
                                                    <td colspan="2" class="text-right">Total Berat RM:</td>
                                                    <td class="text-center text-primary" id="total-berat-rm-non-forming-edit">{{ number_format($totalBeratEditNF, 3) }}</td>
                                                    <td colspan="4"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-right">Rata-Rata Berat RM:</td>
                                                    <td class="text-center text-success" id="avg-berat-rm-non-forming-edit">{{ number_format($avgBeratEditNF, 3) }}</td>
                                                    <td colspan="4"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update Data
                                </button>
                                <a href="{{ route('persiapan-bahan-non-forming.show', $data->uuid) }}" class="btn btn-secondary ml-2">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function hitungTotalSuhuAktual() {
        let total = 0;
        let count = 0;
        for (let i = 1; i <= 5; i++) {
            let input = document.getElementById('aktual_suhu_' + i);
            if (input && input.value !== '') {
                let val = parseFloat(input.value);
                if (!isNaN(val)) {
                    total += val;
                    count++;
                }
            }
        }
        let avg = count > 0 ? (total / count) : 0;
        const totalEl = document.getElementById('total_aktual_suhu');
        if (totalEl) {
            totalEl.value = avg.toFixed(2);
        }
    }

    for (let i = 1; i <= 5; i++) {
        let input = document.getElementById('aktual_suhu_' + i);
        if (input) {
            input.addEventListener('input', hitungTotalSuhuAktual);
        }
    }
    hitungTotalSuhuAktual();

    const kodeEmulsiOilContainer = document.getElementById('kode-emulsi-oil-container-non-forming');
    if (!kodeEmulsiOilContainer) return;

    kodeEmulsiOilContainer.addEventListener('click', function(e) {
        if (e.target.closest('.add-kode-emulsi-oil-non-forming')) {
            let firstItem = kodeEmulsiOilContainer.querySelector('.kode-emulsi-oil-item-non-forming');
            if (!firstItem) return;
            let newItem = firstItem.cloneNode(true);
            let input = newItem.querySelector('input');
            if (input) input.value = '';

            let addBtn = newItem.querySelector('.add-kode-emulsi-oil-non-forming');
            if (addBtn) addBtn.style.display = 'none';

            let removeBtn = newItem.querySelector('.remove-kode-emulsi-oil-non-forming');
            if (removeBtn) removeBtn.style.display = 'inline-block';

            kodeEmulsiOilContainer.appendChild(newItem);
            updateButtonsVisibility();
        }

        if (e.target.closest('.remove-kode-emulsi-oil-non-forming')) {
            let items = kodeEmulsiOilContainer.querySelectorAll('.kode-emulsi-oil-item-non-forming');
            if (items.length > 1) {
                e.target.closest('.kode-emulsi-oil-item-non-forming').remove();
                updateButtonsVisibility();
            }
        }
    });

    function updateButtonsVisibility() {
        let items = kodeEmulsiOilContainer.querySelectorAll('.kode-emulsi-oil-item-non-forming');
        items.forEach(function(item, index) {
            let addBtn = item.querySelector('.add-kode-emulsi-oil-non-forming');
            let removeBtn = item.querySelector('.remove-kode-emulsi-oil-non-forming');

            if (addBtn) {
                addBtn.style.display = (index === 0) ? 'inline-block' : 'none';
            }
            if (removeBtn) {
                removeBtn.style.display = (items.length <= 1) ? 'none' : 'inline-block';
            }
        });
    }
    updateButtonsVisibility();
});
</script>
@endpush
