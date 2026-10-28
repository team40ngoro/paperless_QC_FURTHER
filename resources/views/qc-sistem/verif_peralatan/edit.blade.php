@extends('layouts.app')

@section('title', 'Edit Verifikasi Kebersihan Mesin & Peralatan')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Verifikasi Kebersihan Ruangan Mesin & Peralatan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('verif-peralatan.index') }}">Verifikasi Peralatan</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Form Edit</h3>
                                <div class="card-tools">
                                    <a href="{{ route('verif-peralatan.show', $item->uuid) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <a href="{{ route('verif-peralatan.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>

                            @php
                                $keteranganOptions = [
                                    'Bersih, tidak ada kontaminan atau kotoran, tidak tercium bau menyimpang, bebas dari najis (material non halal)',
                                    'berdebu',
                                    'noda (karat, cat, atau sejenisnya)',
                                    'endapan kotoran',
                                    'pertumbuhan mikroorganisme (jamur, bau busuk)',
                                ];

                                // Jam per area (dari data tersimpan)
                                $existingAreaJamsMap = $item->details->groupBy('id_area')->map(function ($grp) use ($item) {
                                    $firstJam = $grp->first()?->jam;
                                    if ($firstJam) {
                                        return \Carbon\Carbon::parse($firstJam)->format('H:i');
                                    }
                                    return $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : now()->format('H:i');
                                });

                                // Area yang tampil saat halaman dibuka (old input jika validasi gagal)
                                $oldAreaJam = old('area_jam');
                                $initialAreaIds = is_array($oldAreaJam)
                                    ? array_map('strval', array_keys($oldAreaJam))
                                    : $item->details->pluck('id_area')->unique()->map(fn($v) => (string) $v)->values()->all();

                                $initialAreaJams = is_array($oldAreaJam)
                                    ? $oldAreaJam
                                    : $existingAreaJamsMap->toArray();

                                $areasForJs = $areas->map(fn($a) => ['id' => (string) $a->id, 'name' => $a->area])->values();
                                $mesinsByArea = $mesins->groupBy('id_area');
                            @endphp

                            <form action="{{ route('verif-peralatan.update', $item->uuid) }}" method="POST" id="form_verif_edit">
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
                                        @if(auth()->user()->role === 'superadmin')
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Plan</label>
                                                    <input type="text" class="form-control" value="{{ optional($plans->firstWhere('id', $selectedPlanId))->nama_plan ?? ($item->plan->nama_plan ?? '-') }}" readonly>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="{{ auth()->user()->role === 'superadmin' ? 'col-md-4' : 'col-md-6' }}">
                                            <div class="form-group">
                                                <label for="id_shift">Shift <span class="text-danger">*</span></label>
                                                <select name="id_shift" id="id_shift" class="form-control @error('id_shift') is-invalid @enderror" required>
                                                    <option value="">Pilih Shift</option>
                                                    @foreach($shifts as $shift)
                                                        <option value="{{ $shift->id }}" {{ (old('id_shift', $item->id_shift) == $shift->id) ? 'selected' : '' }}>
                                                            {{ $shift->shift }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('id_shift')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="{{ auth()->user()->role === 'superadmin' ? 'col-md-4' : 'col-md-6' }}">
                                            <div class="form-group">
                                                <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                                <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d')) }}" required>
                                                @error('tanggal')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Container pemilih area dinamis --}}
                                    <div id="dynamic_area_selectors" class="mb-4"></div>

                                    <div class="d-flex align-items-center mb-4">
                                        <button type="button" id="btn_tambah_area_dynamic" class="btn btn-success">
                                            <i class="fas fa-plus"></i> Tambah Area
                                        </button>
                                        <span id="input_vars_counter" class="badge badge-info ml-2 p-2" style="font-size: 0.85rem;">Total Variable Input: 0 / 1000</span>
                                    </div>

                                    {{-- Section tabel per area --}}
                                    @foreach($areas as $area)
                                        @php
                                            $areaMesins = $mesinsByArea->get($area->id, collect());
                                        @endphp

                                        @if($areaMesins->count() > 0)
                                            <div class="card card-outline card-primary mb-3 verif-area-section d-none" data-area-id="{{ $area->id }}">
                                                <div class="card-header">
                                                    <h3 class="card-title font-weight-bold m-0" style="font-size: 1.1rem; line-height: 1.8;">
                                                        <i class="fas fa-map-marker-alt text-primary mr-1"></i> Area: {{ $area->area }}
                                                    </h3>
                                                    <div class="card-tools d-flex align-items-center">
                                                        <div class="form-check form-check-inline mb-0 mr-2">
                                                            <input class="form-check-input check-all-ok" type="checkbox" data-area-id="{{ $area->id }}" id="check_all_ok_{{ $area->id }}" disabled>
                                                            <label class="form-check-label font-weight-bold text-success" for="check_all_ok_{{ $area->id }}">Check All Bersih</label>
                                                        </div>
                                                        <button type="button" class="btn btn-tool btn-close-section" data-area-id="{{ $area->id }}">
                                                            <i class="fas fa-times text-danger" style="font-size: 1.1rem;"></i>
                                                        </button>
                                                    </div>
                                                </div>{{-- /card-header --}}

                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped" style="white-space:nowrap;">
                                                            <thead>
                                                                <tr>
                                                                    <th>Mesin/Peralatan</th>
                                                                    <th style="width:140px;" class="text-center">Kondisi</th>
                                                                    <th style="min-width:240px;">Keterangan</th>
                                                                    <th style="min-width:240px;">Tindakan Koreksi</th>
                                                                    <th style="width:140px;" class="text-center">Verifikasi Hasil</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($areaMesins as $mesin)
                                                                    @php
                                                                        $row = $existing->get($mesin->id);
                                                                        $hasBagian = $mesin->bagian && $mesin->bagian->count() > 0;
                                                                        $bagianExisting = $row ? $row->bagianDetails->keyBy('id_bagian') : collect();
                                                                    @endphp
                                                                    <tr>
                                                                        <td>
                                                                            <strong>{{ $mesin->nama_mesin }}</strong>
                                                                            @if($hasBagian)
                                                                                <button type="button" class="btn btn-link btn-sm p-0 ml-2 text-primary toggle-bagian" data-mesin-id="{{ $mesin->id }}" data-count="{{ $mesin->bagian->count() }}" style="font-size: 0.75rem;">
                                                                                    <i class="fas fa-chevron-down"></i> <span class="bagian-label">Tampilkan Bagian ({{ $mesin->bagian->count() }})</span>
                                                                                </button>
                                                                            @endif
                                                                        </td>

                                                                        @if(!$hasBagian)
                                                                            @php
                                                                                $oldVerif = old("details.{$mesin->id}.verifikasi");
                                                                                $verifVal = ($oldVerif === null)
                                                                                    ? ($row && $row->verifikasi !== null ? (string) (int) (bool) $row->verifikasi : '')
                                                                                    : (string) $oldVerif;

                                                                                $ketVal = old("details.{$mesin->id}.keterangan", $row?->keterangan ?? '');
                                                                                $isCustomKet = $ketVal && $ketVal !== '__lainnya__' && !in_array($ketVal, $keteranganOptions);
                                                                                $showManual = $isCustomKet || $ketVal === '__lainnya__';

                                                                                $oldVerifHasil = old("details.{$mesin->id}.verifikasi_hasil");
                                                                                $verifHasilVal = ($oldVerifHasil === null)
                                                                                    ? ($row && $row->verifikasi_hasil !== null ? (string) (int) (bool) $row->verifikasi_hasil : '')
                                                                                    : (string) $oldVerifHasil;
                                                                            @endphp
                                                                            <td class="text-center">
                                                                                <select name="details[{{ $mesin->id }}][verifikasi]" class="form-control form-control-sm verifikasi-select" data-area-id="{{ $area->id }}" disabled>
                                                                                    <option value="" {{ $verifVal === '' ? 'selected' : '' }}>--</option>
                                                                                    <option value="1" {{ $verifVal === '1' ? 'selected' : '' }}>Bersih</option>
                                                                                    <option value="0" {{ $verifVal === '0' ? 'selected' : '' }}>Kotor</option>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <div class="verif-only-notok" data-mesin-id="{{ $mesin->id }}" style="display: none;">
                                                                                    <select name="details[{{ $mesin->id }}][keterangan]" class="form-control form-control-sm keterangan-select" data-mesin-id="{{ $mesin->id }}" disabled>
                                                                                        <option value="">-- Pilih --</option>
                                                                                        @foreach($keteranganOptions as $opt)
                                                                                            <option value="{{ $opt }}" {{ $ketVal === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                                                                        @endforeach
                                                                                        <option value="__lainnya__" {{ $showManual ? 'selected' : '' }}>Lainnya (ketik manual)</option>
                                                                                    </select>
                                                                                    <input type="text" name="details[{{ $mesin->id }}][keterangan_manual]" class="form-control form-control-sm mt-1 keterangan-manual-input" data-mesin-id="{{ $mesin->id }}" placeholder="Ketik manual..." style="{{ $showManual ? '' : 'display: none;' }}" value="{{ old("details.{$mesin->id}.keterangan_manual", $isCustomKet ? $ketVal : '') }}" disabled>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="verif-only-notok" data-mesin-id="{{ $mesin->id }}" style="display: none;">
                                                                                    <textarea name="details[{{ $mesin->id }}][tindakan_koreksi]" class="form-control" rows="2" disabled>{{ old("details.{$mesin->id}.tindakan_koreksi", $row?->tindakan_koreksi) }}</textarea>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <select name="details[{{ $mesin->id }}][verifikasi_hasil]" class="form-control form-control-sm" disabled>
                                                                                    <option value="" {{ $verifHasilVal === '' ? 'selected' : '' }}>-</option>
                                                                                    <option value="1" {{ $verifHasilVal === '1' ? 'selected' : '' }}>OK</option>
                                                                                    <option value="0" {{ $verifHasilVal === '0' ? 'selected' : '' }}>Tidak OK</option>
                                                                                </select>
                                                                            </td>
                                                                        @else
                                                                            <td class="text-center text-muted">-</td>
                                                                            <td class="text-center text-muted">-</td>
                                                                            <td class="text-center text-muted">-</td>
                                                                            <td class="text-center text-muted">-</td>
                                                                        @endif
                                                                    </tr>

                                                                    @if($hasBagian)
                                                                        <tr class="bagian-row d-none" data-mesin-id="{{ $mesin->id }}">
                                                                            <td colspan="5">
                                                                                <div class="bg-light p-3 ml-4" style="border-left: 3px solid #007bff;">
                                                                                    <small class="text-muted font-weight-bold">Bagian/Sub Bagian:</small>
                                                                                    <table class="table table-sm table-bordered mb-0 mt-2" style="font-size: 0.8rem;">
                                                                                        <thead class="thead-light">
                                                                                            <tr>
                                                                                                <th style="width: 120px;">Bagian</th>
                                                                                                <th style="width: 100px; text-align: center;">Kondisi</th>
                                                                                                <th>Keterangan</th>
                                                                                                <th>Tindakan Koreksi</th>
                                                                                                <th style="width: 100px; text-align: center;">Verifikasi Hasil</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            @foreach($mesin->bagian as $bagian)
                                                                                                @php
                                                                                                    $bv = $bagianExisting->get($bagian->id);

                                                                                                    $oldBagVerif = old("details.{$mesin->id}.bagian.{$bagian->id}.verifikasi");
                                                                                                    $bagVerifVal = ($oldBagVerif === null)
                                                                                                        ? ($bv && $bv->verifikasi !== null ? (string) (int) (bool) $bv->verifikasi : '')
                                                                                                        : (string) $oldBagVerif;

                                                                                                    $bagKetVal = old("details.{$mesin->id}.bagian.{$bagian->id}.keterangan", $bv?->keterangan ?? '');
                                                                                                    $bagIsCustomKet = $bagKetVal && $bagKetVal !== '__lainnya__' && !in_array($bagKetVal, $keteranganOptions);
                                                                                                    $bagShowManual = $bagIsCustomKet || $bagKetVal === '__lainnya__';

                                                                                                    $oldBagHasil = old("details.{$mesin->id}.bagian.{$bagian->id}.verifikasi_hasil");
                                                                                                    $bagHasilVal = ($oldBagHasil === null)
                                                                                                        ? ($bv && $bv->verifikasi_hasil !== null ? (string) (int) (bool) $bv->verifikasi_hasil : '')
                                                                                                        : (string) $oldBagHasil;
                                                                                                @endphp
                                                                                                <tr>
                                                                                                    <td><strong>{{ $bagian->nama_bagian }}</strong></td>
                                                                                                    <td class="text-center">
                                                                                                        <select name="details[{{ $mesin->id }}][bagian][{{ $bagian->id }}][verifikasi]" class="form-control form-control-sm verifikasi-bagian-select" data-area-id="{{ $area->id }}" data-mesin-id="{{ $mesin->id }}" disabled>
                                                                                                            <option value="" {{ $bagVerifVal === '' ? 'selected' : '' }}>--</option>
                                                                                                            <option value="1" {{ $bagVerifVal === '1' ? 'selected' : '' }}>Bersih</option>
                                                                                                            <option value="0" {{ $bagVerifVal === '0' ? 'selected' : '' }}>Kotor</option>
                                                                                                        </select>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <div class="bagian-verif-only-notok" data-mesin-id="{{ $mesin->id }}" data-bagian-id="{{ $bagian->id }}" style="display: none;">
                                                                                                            <select name="details[{{ $mesin->id }}][bagian][{{ $bagian->id }}][keterangan]" class="form-control form-control-sm bagian-keterangan-select" data-mesin-id="{{ $mesin->id }}" data-bagian-id="{{ $bagian->id }}" disabled>
                                                                                                                <option value="">-- Pilih --</option>
                                                                                                                @foreach($keteranganOptions as $opt)
                                                                                                                    <option value="{{ $opt }}" {{ $bagKetVal === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                                                                                                @endforeach
                                                                                                                <option value="__lainnya__" {{ $bagShowManual ? 'selected' : '' }}>Lainnya (ketik manual)</option>
                                                                                                            </select>
                                                                                                            <input type="text" name="details[{{ $mesin->id }}][bagian][{{ $bagian->id }}][keterangan_manual]" class="form-control form-control-sm mt-1 bagian-keterangan-manual-input" data-mesin-id="{{ $mesin->id }}" data-bagian-id="{{ $bagian->id }}" placeholder="Ketik manual..." style="{{ $bagShowManual ? '' : 'display: none;' }}" value="{{ old("details.{$mesin->id}.bagian.{$bagian->id}.keterangan_manual", $bagIsCustomKet ? $bagKetVal : '') }}" disabled>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <div class="bagian-verif-only-notok" data-mesin-id="{{ $mesin->id }}" data-bagian-id="{{ $bagian->id }}" style="display: none;">
                                                                                                            <textarea name="details[{{ $mesin->id }}][bagian][{{ $bagian->id }}][tindakan_koreksi]" class="form-control form-control-sm" rows="1" disabled>{{ old("details.{$mesin->id}.bagian.{$bagian->id}.tindakan_koreksi", $bv?->tindakan_koreksi) }}</textarea>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td class="text-center">
                                                                                                        <select name="details[{{ $mesin->id }}][bagian][{{ $bagian->id }}][verifikasi_hasil]" class="form-control form-control-sm" disabled>
                                                                                                            <option value="" {{ $bagHasilVal === '' ? 'selected' : '' }}>-</option>
                                                                                                            <option value="1" {{ $bagHasilVal === '1' ? 'selected' : '' }}>OK</option>
                                                                                                            <option value="0" {{ $bagHasilVal === '0' ? 'selected' : '' }}>Tidak OK</option>
                                                                                                        </select>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>{{-- /card-body --}}
                                            </div>{{-- /card verif-area-section --}}
                                        @endif
                                    @endforeach
                                </div>{{-- /card-body form --}}

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update
                                    </button>
                                    <a href="{{ route('verif-peralatan.index') }}" class="btn btn-secondary">
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectorContainer = document.getElementById('dynamic_area_selectors');
    const btnTambah = document.getElementById('btn_tambah_area_dynamic');
    const counterEl = document.getElementById('input_vars_counter');
    const areaSections = document.querySelectorAll('.verif-area-section');
    const MAX_INPUT_VARS = 1000;
    const WARN_INPUT_VARS = 800;

    const initialAreaIds = @json($initialAreaIds);
    const initialAreaJams = @json($initialAreaJams);
    const areasList = @json($areasForJs);

    // ---------- Helper ----------
    function showAlert(icon, title, text, color) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: icon, title: title, text: text, confirmButtonText: 'Mengerti', confirmButtonColor: color || '#3085d6' });
        } else {
            alert(text);
        }
    }

    function nowHHMM() {
        return new Date().toTimeString().substring(0, 5);
    }

    // Jumlah variabel input untuk sebuah area = semua field ber-name di section + 1 (jam)
    function countAreaInputVars(areaId) {
        const section = document.querySelector('.verif-area-section[data-area-id="' + areaId + '"]');
        if (!section) return 0;
        return section.querySelectorAll('select[name], input[name], textarea[name]').length + 1;
    }

    function getTotalInputVarsCount(areaIds) {
        // +4 untuk field dasar (_token, _method, id_shift, tanggal)
        let total = 4;
        areaIds.forEach(id => { total += countAreaInputVars(id); });
        return total;
    }

    function showMaxInputWarning(total) {
        showAlert(
            'warning',
            'Batas Input Tercapai!',
            'Total input data akan mencapai ' + total + ' variabel (maksimal ' + MAX_INPUT_VARS + '). Silakan simpan area yang ada terlebih dahulu, lalu tambahkan area lainnya.'
        );
    }

    function getSelectedAreaIds() {
        return Array.from(document.querySelectorAll('.dynamic-area-select'))
            .map(s => s.value)
            .filter(v => v !== '');
    }

    function updateInputCounter() {
        const total = getTotalInputVarsCount(getSelectedAreaIds());
        counterEl.textContent = 'Total Variable Input: ' + total + ' / ' + MAX_INPUT_VARS;
        counterEl.classList.remove('badge-info', 'badge-warning', 'badge-danger');
        if (total > MAX_INPUT_VARS) {
            counterEl.classList.add('badge-danger');
        } else if (total >= WARN_INPUT_VARS) {
            counterEl.classList.add('badge-warning');
        } else {
            counterEl.classList.add('badge-info');
        }
    }

    // ---------- Baris pemilih area ----------
    function addAreaSelectRow(selectedAreaId = '', selectedJam = '') {
        selectedAreaId = selectedAreaId === null ? '' : String(selectedAreaId);

        const div = document.createElement('div');
        div.className = 'row mb-3 area-dynamic-row';

        let options = '<option value="">-- Pilih Area --</option>';
        areasList.forEach(area => {
            options += '<option value="' + area.id + '"' + (selectedAreaId === area.id ? ' selected' : '') + '>' + area.name + '</option>';
        });

        const defaultJam = selectedJam || nowHHMM();

        div.innerHTML = `
            <div class="col-md-5">
                <div class="form-group mb-0">
                    <label class="small text-muted">Pilih Area</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-info text-white"><i class="fas fa-map-marker-alt"></i></span>
                        </div>
                        <select class="form-control dynamic-area-select" data-previous="${selectedAreaId}">
                            ${options}
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-0">
                    <label class="small text-muted">Jam <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-secondary text-white"><i class="fas fa-clock"></i></span>
                        </div>
                        <input type="time" class="form-control dynamic-area-jam" value="${defaultJam}" disabled>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-remove-row w-100"><i class="fas fa-trash"></i> Hapus</button>
            </div>
        `;

        selectorContainer.appendChild(div);

        const select = div.querySelector('.dynamic-area-select');

        select.addEventListener('change', function () {
            const oldVal = this.getAttribute('data-previous') || '';
            const newVal = this.value;

            if (newVal !== '') {
                const duplicate = Array.from(document.querySelectorAll('.dynamic-area-select'))
                    .some(s => s !== select && s.value === newVal);

                if (duplicate) {
                    showAlert('warning', 'Area Sudah Dipilih!', 'Area ini sudah dipilih di baris lain. Silakan pilih area yang berbeda.');
                    this.value = oldVal;
                    return;
                }

                const proposedIds = Array.from(document.querySelectorAll('.dynamic-area-select'))
                    .map(s => s === select ? newVal : s.value)
                    .filter(v => v !== '');
                const total = getTotalInputVarsCount(proposedIds);
                if (total > MAX_INPUT_VARS) {
                    showMaxInputWarning(total);
                    this.value = oldVal;
                    return;
                }

                // Isi jam dari data tersimpan bila area ini sudah pernah diisi
                const jamInput = div.querySelector('.dynamic-area-jam');
                if (jamInput && initialAreaJams[newVal]) {
                    jamInput.value = initialAreaJams[newVal];
                }
            }

            this.setAttribute('data-previous', this.value);
            refreshAllVisibility();
        });

        div.querySelector('.btn-remove-row').addEventListener('click', function () {
            div.remove();
            refreshAllVisibility();
        });
    }

    // ---------- Tampilkan / sembunyikan section area ----------
    function refreshAllVisibility() {
        const selectedIds = [];

        document.querySelectorAll('.area-dynamic-row').forEach(row => {
            const select = row.querySelector('.dynamic-area-select');
            const jamInput = row.querySelector('.dynamic-area-jam');
            const val = select ? select.value : '';

            if (val !== '') {
                selectedIds.push(val);
                if (jamInput) {
                    jamInput.name = 'area_jam[' + val + ']';
                    jamInput.removeAttribute('disabled');
                    jamInput.setAttribute('required', 'required');
                }
            } else if (jamInput) {
                jamInput.removeAttribute('name');
                jamInput.setAttribute('disabled', 'disabled');
                jamInput.removeAttribute('required');
            }
        });

        areaSections.forEach(section => {
            const id = String(section.getAttribute('data-area-id'));
            const inputs = section.querySelectorAll('select, input, textarea');
            if (selectedIds.includes(id)) {
                section.classList.remove('d-none');
                inputs.forEach(input => input.removeAttribute('disabled'));
            } else {
                section.classList.add('d-none');
                inputs.forEach(input => input.setAttribute('disabled', 'disabled'));
            }
        });

        updateInputCounter();
    }

    // ---------- Tampilkan field Keterangan/Tindakan hanya jika Kotor ----------
    function applyVerifRowVisibility(selectEl) {
        const match = (selectEl.getAttribute('name') || '').match(/^details\[(\d+)\]\[verifikasi\]$/);
        if (!match) return;
        const show = selectEl.value === '0';
        document.querySelectorAll('.verif-only-notok[data-mesin-id="' + match[1] + '"]').forEach(el => {
            el.style.display = show ? '' : 'none';
        });
    }

    function applyBagianVerifVisibility(selectEl) {
        const match = (selectEl.getAttribute('name') || '').match(/^details\[(\d+)\]\[bagian\]\[(\d+)\]\[verifikasi\]$/);
        if (!match) return;
        const show = selectEl.value === '0';
        document.querySelectorAll('.bagian-verif-only-notok[data-mesin-id="' + match[1] + '"][data-bagian-id="' + match[2] + '"]').forEach(el => {
            el.style.display = show ? '' : 'none';
        });
    }

    // ---------- Inisialisasi ----------
    if (initialAreaIds.length > 0) {
        initialAreaIds.forEach(id => addAreaSelectRow(id, initialAreaJams[id] || ''));
    } else {
        addAreaSelectRow();
    }
    refreshAllVisibility();

    document.querySelectorAll('.verifikasi-select').forEach(applyVerifRowVisibility);
    document.querySelectorAll('.verifikasi-bagian-select').forEach(applyBagianVerifVisibility);

    // ---------- Tombol Tambah Area ----------
    btnTambah.addEventListener('click', function () {
        const currentTotal = getTotalInputVarsCount(getSelectedAreaIds());
        if (currentTotal >= MAX_INPUT_VARS) {
            showAlert(
                'warning',
                'Batas Input Tercapai!',
                'Total input data sudah mencapai ' + currentTotal + ' variabel (maksimal ' + MAX_INPUT_VARS + '). Area baru tidak dapat ditambahkan. Silakan simpan area yang ada terlebih dahulu.'
            );
            return;
        }
        addAreaSelectRow();
    });

    // ---------- Event delegation: change ----------
    document.addEventListener('change', function (e) {
        const t = e.target;

        if (t.classList.contains('verifikasi-select')) {
            applyVerifRowVisibility(t);
        }

        if (t.classList.contains('verifikasi-bagian-select')) {
            applyBagianVerifVisibility(t);
        }

        if (t.classList.contains('check-all-ok')) {
            const areaId = t.getAttribute('data-area-id');
            // Sama seperti halaman create: centang = Bersih (1), hilangkan centang = Kotor (0)
            const value = t.checked ? '1' : '0';
            document.querySelectorAll('.verifikasi-select[data-area-id="' + areaId + '"]').forEach(sel => {
                sel.value = value;
                applyVerifRowVisibility(sel);
            });
            document.querySelectorAll('.verifikasi-bagian-select[data-area-id="' + areaId + '"]').forEach(sel => {
                sel.value = value;
                applyBagianVerifVisibility(sel);
            });
        }

        if (t.classList.contains('keterangan-select')) {
            const manual = document.querySelector('.keterangan-manual-input[data-mesin-id="' + t.dataset.mesinId + '"]');
            if (manual) manual.style.display = t.value === '__lainnya__' ? '' : 'none';
        }

        if (t.classList.contains('bagian-keterangan-select')) {
            const manual = document.querySelector('.bagian-keterangan-manual-input[data-mesin-id="' + t.dataset.mesinId + '"][data-bagian-id="' + t.dataset.bagianId + '"]');
            if (manual) manual.style.display = t.value === '__lainnya__' ? '' : 'none';
        }
    });

    // ---------- Event delegation: click ----------
    document.addEventListener('click', function (e) {
        // Toggle bagian
        const toggleBtn = e.target.closest('.toggle-bagian');
        if (toggleBtn) {
            const mesinId = toggleBtn.getAttribute('data-mesin-id');
            const count = toggleBtn.getAttribute('data-count') || '';
            const bagianRow = document.querySelector('.bagian-row[data-mesin-id="' + mesinId + '"]');
            const icon = toggleBtn.querySelector('i');
            const label = toggleBtn.querySelector('.bagian-label');
            if (bagianRow) {
                bagianRow.classList.toggle('d-none');
                const isHidden = bagianRow.classList.contains('d-none');
                if (icon) icon.className = isHidden ? 'fas fa-chevron-down' : 'fas fa-chevron-up';
                if (label) label.textContent = (isHidden ? 'Tampilkan Bagian' : 'Sembunyikan Bagian') + ' (' + count + ')';
            }
        }

        // Tutup section area
        const btnClose = e.target.closest('.btn-close-section');
        if (btnClose) {
            const areaId = String(btnClose.getAttribute('data-area-id'));
            document.querySelectorAll('.dynamic-area-select').forEach(sel => {
                if (String(sel.value) === areaId) {
                    const row = sel.closest('.area-dynamic-row');
                    if (row) row.remove();
                }
            });
            refreshAllVisibility();
        }
    });

    // ---------- Submit ----------
    document.getElementById('form_verif_edit').addEventListener('submit', function (e) {
        e.preventDefault();

        const activeSections = document.querySelectorAll('.verif-area-section:not(.d-none)');
        const activeIds = Array.from(activeSections).map(sec => sec.getAttribute('data-area-id'));

        if (activeIds.length === 0) {
            showAlert('warning', 'Area Belum Dipilih!', 'Silakan pilih minimal 1 Area terlebih dahulu sebelum menyimpan data.');
            return;
        }

        const submitTotal = getTotalInputVarsCount(activeIds);
        if (submitTotal > MAX_INPUT_VARS) {
            showAlert(
                'error',
                'Gagal Menyimpan!',
                'Total input data yang aktif (' + submitTotal + ' input) melebihi batas maksimal ' + MAX_INPUT_VARS + ' input. Silakan kurangi area yang dipilih.',
                '#d33'
            );
            return;
        }

        // Pastikan semua field pada section aktif terkirim
        activeSections.forEach(function (section) {
            section.querySelectorAll('select, input, textarea').forEach(function (input) {
                input.removeAttribute('disabled');
            });
        });

        this.submit();
    });
});
</script>
@endpush