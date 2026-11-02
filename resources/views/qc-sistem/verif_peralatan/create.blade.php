@extends('layouts.app')

@section('title', 'Tambah Verifikasi Kebersihan Mesin & Peralatan')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Verifikasi Kebersihan Ruangan Mesin & Peralatan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('verif-peralatan.index') }}">Verifikasi Kebersihan Ruangan Mesin & Peralatan</a></li>
                            <li class="breadcrumb-item active">Tambah</li>
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
                                <h3 class="card-title">Form Verifikasi</h3>
                                <!-- <div class="card-tools">
                                    <a href="{{ route('verif-peralatan.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div> -->
                            </div>

                            <form action="{{ route('verif-peralatan.store') }}" method="POST">
                                @csrf
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

                                    @php
                                        $keteranganOptions = [
                                            'Bersih, tidak ada kontaminan atau kotoran, tidak tercium bau menyimpang, bebas dari najis (material non halal)',
                                            'berdebu',
                                            'noda (karat, cat, atau sejenisnya)',
                                            'endapan kotoran',
                                            'pertumbuhan mikroorganisme (jamur, bau busuk)',
                                        ];
                                    @endphp

                                    <div class="row">
                                        @if(auth()->user()->role === 'superadmin')
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="id_plan">Plan <span class="text-danger">*</span></label>
                                                    <select name="id_plan" id="id_plan" class="form-control @error('id_plan') is-invalid @enderror" onchange="window.location='{{ route('verif-peralatan.create') }}?plan_id='+this.value" required>
                                                        @foreach($plans as $plan)
                                                            <option value="{{ $plan->id }}" {{ (int) $selectedPlanId === (int) $plan->id ? 'selected' : '' }}>
                                                                {{ $plan->nama_plan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('id_plan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif

                                        <div class="{{ auth()->user()->role === 'superadmin' ? 'col-md-4' : 'col-md-6' }}">
                                            <div class="form-group">
                                                <label for="id_shift">Shift <span class="text-danger">*</span></label>
                                                <select name="id_shift" id="id_shift" class="form-control @error('id_shift') is-invalid @enderror" required>
                                                    <option value="">Pilih Shift</option>
                                                    @foreach($shifts as $shift)
                                                        <option value="{{ $shift->id }}" {{ old('id_shift') == $shift->id ? 'selected' : '' }}>
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
                                                <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                                @error('tanggal')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Container for Dynamic Area Selectors -->
                                    <div id="dynamic_area_selectors" class="mb-4">
                                        <!-- Area select rows will be added here -->
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <button type="button" id="btn_tambah_area_dynamic" class="btn btn-success">
                                            <i class="fas fa-plus"></i> Tambah Area
                                        </button>
                                        <span id="input_vars_counter" class="badge badge-info ml-2 p-2" style="font-size: 0.85rem;">Total Variable Input: 0 / 1000</span>
                                    </div>
                                    @php
                                        $mesinsByArea = $mesins->groupBy('id_area');
                                    @endphp

                                    <div id="all_area_cards" class="mt-4">
                                        @forelse($areas as $area)
                                            @php
                                                $areaMesins = $mesinsByArea->get($area->id, collect());
                                            @endphp
                                            @if($areaMesins->count() > 0)
                                                <div class="card card-outline card-primary mb-4 verif-area-section d-none" data-area-id="{{ $area->id }}">
                                                    <div class="card-header">
                                                        <h3 class="card-title font-weight-bold m-0" style="font-size: 1.1rem; line-height: 1.8;"><i class="fas fa-map-marker-alt text-primary mr-1"></i> Area: {{ $area->area }}</h3>
                                                        <div class="card-tools d-flex align-items-center">
                                                            <div class="d-flex align-items-center mr-3">
                                                                <label class="mb-0 mr-2 font-weight-normal text-muted small"><i class="fas fa-clock mr-1"></i>Jam <span class="text-danger">*</span>:</label>
                                                                <input type="time" class="form-control form-control-sm area-jam-input" data-area-id="{{ $area->id }}" value="{{ old("area_jam.{$area->id}", now()->format('H:i')) }}" style="width: 110px;" disabled>
                                                            </div>
                                                            <div class="form-check form-check-inline mb-0 mr-2">
                                                                <input class="form-check-input check-all-ok" type="checkbox" data-area-id="{{ $area->id }}" id="check_all_ok_{{ $area->id }}" disabled>
                                                                <label class="form-check-label font-weight-bold text-success" for="check_all_ok_{{ $area->id }}">Check All Bersih</label>
                                                            </div>
                                                            <button type="button" class="btn btn-tool btn-close-section" data-area-id="{{ $area->id }}">
                                                                <i class="fas fa-times text-danger" style="font-size: 1.1rem;"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-striped" style="white-space:nowrap;">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Jenis Item</th>
                                                                        <th style="width:140px;" class="text-center">Kondisi</th>
                                                                        <th style="min-width:240px;">Keterangan</th>
                                                                        <th style="min-width:240px;">Tindakan Koreksi</th>
                                                                        <th style="width:140px;" class="text-center">Verifikasi Hasil</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                     @foreach($areaMesins as $mesin)
                                                                         @php
                                                                             $hasBagian = $mesin->bagian && $mesin->bagian->count() > 0;
                                                                         @endphp
                                                                         <tr>
                                                                             <td>
                                                                                 <strong>{{ $mesin->nama_mesin }}</strong>
                                                                                 @if($hasBagian)
                                                                                     <button type="button" class="btn btn-link btn-sm p-0 ml-2 text-primary toggle-bagian" data-mesin-id="{{ $mesin->id }}" style="font-size: 0.75rem;">
                                                                                         <i class="fas fa-chevron-down"></i> <span class="bagian-label">Tampilkan Bagian ({{ $mesin->bagian->count() }})</span>
                                                                                     </button>
                                                                                 @endif
                                                                             </td>
                                                                             @if(!$hasBagian)
                                                                                 <td class="text-center">
                                                                                     @php
                                                                                         $oldVerif = old("details.{$mesin->id}.verifikasi");
                                                                                         $verifVal = ($oldVerif === null) ? '' : (string) (int) (bool) $oldVerif;
                                                                                     @endphp
                                                                                     <select name="details[{{ $mesin->id }}][verifikasi]" class="form-control form-control-sm verifikasi-select" data-area-id="{{ $area->id }}" disabled>
                                                                                         <option value="" {{ $verifVal === '' ? 'selected' : '' }}>--</option>
                                                                                         <option value="1" {{ $verifVal === '1' ? 'selected' : '' }}>Bersih</option>
                                                                                         <option value="0" {{ $verifVal === '0' ? 'selected' : '' }}>Kotor</option>
                                                                                     </select>
                                                                                 </td>
                                                                                 <td>
                                                                                     <div class="verif-only-notok" data-mesin-id="{{ $mesin->id }}">
                                                                                         <select name="details[{{ $mesin->id }}][keterangan]" class="form-control form-control-sm keterangan-select" data-mesin-id="{{ $mesin->id }}" disabled>
                                                                                             <option value="">-- Pilih --</option>
                                                                                             @foreach($keteranganOptions as $opt)
                                                                                                 <option value="{{ $opt }}" {{ old("details.{$mesin->id}.keterangan") == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                                                                             @endforeach
                                                                                             <option value="__lainnya__" {{ old("details.{$mesin->id}.keterangan") == '__lainnya__' ? 'selected' : '' }}>Lainnya (ketik manual)</option>
                                                                                         </select>
                                                                                         <input type="text" name="details[{{ $mesin->id }}][keterangan_manual]" class="form-control form-control-sm mt-1 keterangan-manual-input" data-mesin-id="{{ $mesin->id }}" placeholder="Ketik manual..." style="display: none;" value="{{ old("details.{$mesin->id}.keterangan_manual") }}" disabled>
                                                                                     </div>
                                                                                 </td>
                                                                                 <td>
                                                                                     <div class="verif-only-notok" data-mesin-id="{{ $mesin->id }}">
                                                                                         <textarea name="details[{{ $mesin->id }}][tindakan_koreksi]" class="form-control" rows="2" disabled>{{ old("details.{$mesin->id}.tindakan_koreksi") }}</textarea>
                                                                                     </div>
                                                                                 </td>
                                                                                 <td class="text-center align-middle">
                                                                                     @if(auth()->user()->isProduksi())
                                                                                         <span class="text-muted font-italic">-</span>
                                                                                     @else
                                                                                         @php
                                                                                             $oldVerifHasil = old("details.{$mesin->id}.verifikasi_hasil");
                                                                                             $verifHasilVal = ($oldVerifHasil === null) ? '' : (string) (int) (bool) $oldVerifHasil;
                                                                                         @endphp
                                                                                         <select name="details[{{ $mesin->id }}][verifikasi_hasil]" class="form-control form-control-sm" disabled>
                                                                                             <option value="" {{ $verifHasilVal === '' ? 'selected' : '' }}>-</option>
                                                                                             <option value="1" {{ $verifHasilVal === '1' ? 'selected' : '' }}>OK</option>
                                                                                             <option value="0" {{ $verifHasilVal === '0' ? 'selected' : '' }}>Tidak OK</option>
                                                                                         </select>
                                                                                     @endif
                                                                                 </td>
                                                                             @else
                                                                                 <td class="text-center text-muted">-</td>
                                                                                 <td class="text-center text-muted">-</td>
                                                                                 <td class="text-center text-muted">-</td>
                                                                                 <td class="text-center text-muted">-</td>
                                                                             @endif
                                                                         </tr>
                                                                        @if($mesin->bagian && $mesin->bagian->count() > 0)
                                                                            <tr class="bagian-row d-none" data-mesin-id="{{ $mesin->id }}">
                                                                                <td colspan="5">
                                                                                    <div class="bg-light p-3 ml-4" style="border-left: 3px solid #007bff;">
                                                                                        <small class="text-muted font-weight-bold">Bagian/Sub Bagian:</small>
                                                                                        <table class="table table-sm table-bordered mb-0 mt-2" style="font-size: 0.8rem;">
                                                                                            <thead class="thead-light">
                                                                                                <tr>
                                                                                                    <th style="width: 120px;">Bagian</th>
                                                                                                    <th style="width: 80px; text-align: center;">Kondisi</th>
                                                                                                    <th>Keterangan</th>
                                                                                                    <th>Tindakan Koreksi</th>
                                                                                                    <th style="width: 80px; text-align: center;">Verifikasi Hasil</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                @foreach($mesin->bagian as $bagian)
                                                                                                    <tr>
                                                                                                        <td><strong>{{ $bagian->nama_bagian }}</strong></td>
                                                                                                        <td class="text-center">
                                                                                                             @php
                                                                                                                 $oldVerifBagian = old("details.{$mesin->id}.bagian.{$bagian->id}.verifikasi");
                                                                                                                 $verifBagianVal = ($oldVerifBagian === null) ? '' : (string) (int) (bool) $oldVerifBagian;
                                                                                                             @endphp
                                                                                                             <select name="details[{{ $mesin->id }}][bagian][{{ $bagian->id }}][verifikasi]" class="form-control form-control-sm verifikasi-bagian-select" data-area-id="{{ $area->id }}" data-mesin-id="{{ $mesin->id }}" disabled>
                                                                                                                 <option value="" {{ $verifBagianVal === '' ? 'selected' : '' }}>--</option>
                                                                                                                 <option value="1" {{ $verifBagianVal === '1' ? 'selected' : '' }}>Bersih</option>
                                                                                                                 <option value="0" {{ $verifBagianVal === '0' ? 'selected' : '' }}>Kotor</option>
                                                                                                             </select>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                             <div class="bagian-verif-only-notok" data-mesin-id="{{ $mesin->id }}" data-bagian-id="{{ $bagian->id }}" style="display: none;">
                                                                                                                 <select name="details[{{ $mesin->id }}][bagian][{{ $bagian->id }}][keterangan]" class="form-control form-control-sm bagian-keterangan-select" data-mesin-id="{{ $mesin->id }}" data-bagian-id="{{ $bagian->id }}" disabled>
                                                                                                                     <option value="">-- Pilih --</option>
                                                                                                                     @foreach($keteranganOptions as $opt)
                                                                                                                         <option value="{{ $opt }}">{{ $opt }}</option>
                                                                                                                     @endforeach
                                                                                                                     <option value="__lainnya__">Lainnya (ketik manual)</option>
                                                                                                                 </select>
                                                                                                                 <input type="text" name="details[{{ $mesin->id }}][bagian][{{ $bagian->id }}][keterangan_manual]" class="form-control form-control-sm mt-1 bagian-keterangan-manual-input" data-mesin-id="{{ $mesin->id }}" data-bagian-id="{{ $bagian->id }}" placeholder="Ketik manual..." style="display: none;" disabled>
                                                                                                             </div>
                                                                                                         </td>
                                                                                                        <td>
                                                                                                            <div class="bagian-verif-only-notok" data-mesin-id="{{ $mesin->id }}" data-bagian-id="{{ $bagian->id }}" style="display: none;">
                                                                                                                <textarea name="details[{{ $mesin->id }}][bagian][{{ $bagian->id }}][tindakan_koreksi]" class="form-control form-control-sm" rows="1" disabled></textarea>
                                                                                                            </div>
                                                                                                         </td>
                                                                                                         <td class="text-center align-middle">
                                                                                                              @if(auth()->user()->isProduksi())
                                                                                                                  <span class="text-muted font-italic">-</span>
                                                                                                              @else
                                                                                                                  <select name="details[{{ $mesin->id }}][bagian][{{ $bagian->id }}][verifikasi_hasil]" class="form-control form-control-sm" disabled>
                                                                                                                      <option value="" selected>-</option>
                                                                                                                      <option value="1">OK</option>
                                                                                                                      <option value="0">Tidak OK</option>
                                                                                                                  </select>
                                                                                                              @endif
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
                                                    </div>
                                                </div>
                                            @endif
                                        @empty
                                            <div class="alert alert-warning">Data area belum tersedia.</div>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan
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
    const areaSections = document.querySelectorAll('.verif-area-section');
    const MAX_INPUT_VARS = 1000;
    
    // Data Area from PHP for JS use
    const areasList = [
        @foreach($areas as $area)
            @php
                $areaMesins = $mesinsByArea->get($area->id, collect());
            @endphp
            @if($areaMesins->count() > 0)
                { id: "{{ $area->id }}", name: "{{ $area->area }}" },
            @endif
        @endforeach
    ];

    function getTotalInputVarsCount(proposedSelectedIds) {
        const baseInputs = document.querySelectorAll('form input[name]:not(.verif-area-section *), form select[name]:not(.verif-area-section *), form textarea[name]:not(.verif-area-section *)');
        let total = baseInputs.length;

        const uniqueAreaIds = new Set((proposedSelectedIds || []).filter(id => id !== ""));

        uniqueAreaIds.forEach(areaId => {
            const section = document.querySelector(`.verif-area-section[data-area-id="${areaId}"]`);
            if (section) {
                const inputs = section.querySelectorAll('input[name], select[name], textarea[name]');
                total += inputs.length;
            }
        });

        return total;
    }

    function showMaxInputWarning(totalInputs) {
        const msg = `Menambahkan area ini akan membuat total input data mencapai ${totalInputs} variabel, melebihi batas maksimal ${MAX_INPUT_VARS} input yang diizinkan server. Area baru tidak dapat ditambahkan. Silakan simpan area yang sudah dipilih terlebih dahulu.`;
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Batas Maksimal Input Terlampaui!',
                text: msg,
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#3085d6'
            });
        } else {
            alert(msg);
        }
    }

    function updateInputCounter() {
        const selectedIds = Array.from(document.querySelectorAll('.dynamic-area-select'))
            .map(s => s.value)
            .filter(id => id !== "");
        const total = getTotalInputVarsCount(selectedIds);
        const counterEl = document.getElementById('input_vars_counter');
        if (counterEl) {
            counterEl.textContent = `Total Variable Input: ${total} / ${MAX_INPUT_VARS}`;
            if (total > MAX_INPUT_VARS) {
                counterEl.className = 'badge badge-danger ml-2 p-2';
            } else if (total > 800) {
                counterEl.className = 'badge badge-warning ml-2 p-2';
            } else {
                counterEl.className = 'badge badge-info ml-2 p-2';
            }
        }
    }

    function addAreaSelectRow() {
        const rowId = 'row_' + Date.now();
        const div = document.createElement('div');
        div.className = 'row mb-3 area-dynamic-row animated fadeIn';
        div.id = rowId;
        
        let options = '<option value="">-- Pilih Area --</option>';
        areasList.forEach(area => {
            options += `<option value="${area.id}">${area.name}</option>`;
        });

        div.innerHTML = `
            <div class="col-md-6">
                <div class="form-group mb-0">
                    <label class="small text-muted">Pilih Area</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-info text-white"><i class="fas fa-map-marker-alt"></i></span>
                        </div>
                        <select class="form-control dynamic-area-select" data-previous="">
                            ${options}
                        </select>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-danger btn-remove-row"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        selectorContainer.appendChild(div);

        const select = div.querySelector('.dynamic-area-select');
        
        // Handle area selection change
        select.addEventListener('change', function() {
            const oldVal = this.getAttribute('data-previous') || '';
            const newVal = this.value;
            
            if (newVal !== "") {
                // Check if area already selected in another row
                const otherSelected = Array.from(document.querySelectorAll('.dynamic-area-select'))
                    .filter(s => s !== select && s.value === newVal);
                
                if (otherSelected.length > 0) {
                    const msg = 'Area ini sudah dipilih di baris lain. Silakan pilih area yang berbeda.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Area Sudah Dipilih!',
                            text: msg
                        });
                    } else {
                        alert(msg);
                    }
                    this.value = oldVal;
                    return;
                }

                // Check total input vars limit
                const allSelects = Array.from(document.querySelectorAll('.dynamic-area-select'));
                const proposedIds = allSelects.map(sel => sel === select ? newVal : sel.value).filter(id => id !== "");
                
                const total = getTotalInputVarsCount(proposedIds);
                if (total > MAX_INPUT_VARS) {
                    showMaxInputWarning(total);
                    this.value = oldVal;
                    return;
                }
            }
            
            // Update visibility
            refreshAllVisibility();
            this.setAttribute('data-previous', this.value);
        });

        // Handle row removal
        div.querySelector('.btn-remove-row').addEventListener('click', function() {
            div.remove();
            refreshAllVisibility();
        });
    }

    function refreshAllVisibility() {
        const selectedIds = Array.from(document.querySelectorAll('.dynamic-area-select'))
            .map(s => s.value)
            .filter(id => id !== "");

        areaSections.forEach(section => {
            const id = section.getAttribute('data-area-id');
            const inputs = section.querySelectorAll('select, input, textarea');
            const jamInput = section.querySelector('.area-jam-input');

            if (selectedIds.includes(id)) {
                section.classList.remove('d-none');
                inputs.forEach(input => input.removeAttribute('disabled'));
                if (jamInput) {
                    jamInput.name = `area_jam[${id}]`;
                    jamInput.setAttribute('required', 'required');
                }
            } else {
                section.classList.add('d-none');
                inputs.forEach(input => input.setAttribute('disabled', 'disabled'));
                if (jamInput) {
                    jamInput.removeAttribute('name');
                    jamInput.removeAttribute('required');
                }
            }
        });

        updateInputCounter();
    }

    function applyVerifRowVisibility(selectEl) {
        const name = selectEl.getAttribute('name') || '';
        const match = name.match(/details\[(\d+)\]\[verifikasi\]/);
        if (!match) return;
        const mesinId = match[1];
        const show = (selectEl.value === '0');
        document.querySelectorAll('.verif-only-notok[data-mesin-id="' + mesinId + '"]').forEach((el) => {
            el.style.display = show ? '' : 'none';
        });
    }

    function applyBagianVerifVisibility(selectEl) {
        const name = selectEl.getAttribute('name') || '';
        const match = name.match(/details\[(\d+)\]\[bagian\]\[(\d+)\]\[verifikasi\]/);
        if (!match) return;
        const mesinId = match[1];
        const bagianId = match[2];
        const show = (selectEl.value === '0');
        document.querySelectorAll('.bagian-verif-only-notok[data-mesin-id="' + mesinId + '"][data-bagian-id="' + bagianId + '"]').forEach((el) => {
            el.style.display = show ? '' : 'none';
        });
    }

    // Initial Row
    addAreaSelectRow();

    // Button Listener
    btnTambah.addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.dynamic-area-select'))
            .map(s => s.value)
            .filter(id => id !== "");
        const currentTotal = getTotalInputVarsCount(selectedIds);

        if (currentTotal >= MAX_INPUT_VARS) {
            const msg = `Total input data sudah mencapai ${currentTotal} variabel (maksimal ${MAX_INPUT_VARS} input). Area baru tidak dapat ditambahkan. Silakan simpan area yang ada terlebih dahulu.`;
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Batas Input Tercapai!',
                    text: msg,
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                alert(msg);
            }
            return;
        }

        addAreaSelectRow();
    });

    // Event delegation for dynamic elements
    document.addEventListener('change', function(e) {
        // Verifikasi Select
        if (e.target.classList.contains('verifikasi-select')) {
            applyVerifRowVisibility(e.target);
        }

        // Bagian Verifikasi Select
        if (e.target.classList.contains('verifikasi-bagian-select')) {
            applyBagianVerifVisibility(e.target);
        }
        
        // Check All Bersih
        if (e.target.classList.contains('check-all-ok')) {
            const areaId = e.target.getAttribute('data-area-id');
            const value = e.target.checked ? '1' : '0';
            document.querySelectorAll(`.verifikasi-select[data-area-id="${areaId}"]`).forEach(sel => {
                sel.value = value;
                applyVerifRowVisibility(sel);
            });
            document.querySelectorAll(`.verifikasi-bagian-select[data-area-id="${areaId}"]`).forEach(sel => {
                sel.value = value;
                applyBagianVerifVisibility(sel);
            });
        }
    });

    // Toggle Bagian Rows
    document.addEventListener('click', function(e) {
        const toggleBtn = e.target.closest('.toggle-bagian');
        if (toggleBtn) {
            const mesinId = toggleBtn.getAttribute('data-mesin-id');
            const bagianRow = document.querySelector(`.bagian-row[data-mesin-id="${mesinId}"]`);
            const icon = toggleBtn.querySelector('i');
            const label = toggleBtn.querySelector('.bagian-label');
            if (bagianRow) {
                bagianRow.classList.toggle('d-none');
                const isHidden = bagianRow.classList.contains('d-none');
                if (icon) icon.className = isHidden ? 'fas fa-chevron-down' : 'fas fa-chevron-up';
                if (label) label.textContent = isHidden ? 'Tampilkan Bagian' : 'Sembunyikan Bagian';
            }
        }
    });

    // Close section button
    document.addEventListener('click', function(e) {
        const btnClose = e.target.closest('.btn-close-section');
        if (btnClose) {
            const areaId = btnClose.getAttribute('data-area-id');
            // Remove the selector row that has this area selected
            document.querySelectorAll('.dynamic-area-select').forEach(sel => {
                if (sel.value === areaId) {
                    sel.closest('.area-dynamic-row').remove();
                }
            });
            refreshAllVisibility();
        }
    });

    // Run initial state check
    document.querySelectorAll('.verifikasi-select').forEach(sel => {
        applyVerifRowVisibility(sel);
    });
    document.querySelectorAll('.verifikasi-bagian-select').forEach(sel => {
        applyBagianVerifVisibility(sel);
    });

    // Keterangan select toggle manual input (mesin)
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('keterangan-select')) {
            const mesinId = e.target.dataset.mesinId;
            const manualInput = document.querySelector(`.keterangan-manual-input[data-mesin-id="${mesinId}"]`);
            if (manualInput) {
                manualInput.style.display = e.target.value === '__lainnya__' ? '' : 'none';
            }
        }
    });

    // Keterangan select toggle manual input (bagian)
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('bagian-keterangan-select')) {
            const mesinId = e.target.dataset.mesinId;
            const bagianId = e.target.dataset.bagianId;
            const manualInput = document.querySelector(`.bagian-keterangan-manual-input[data-mesin-id="${mesinId}"][data-bagian-id="${bagianId}"]`);
            if (manualInput) {
                manualInput.style.display = e.target.value === '__lainnya__' ? '' : 'none';
            }
        }
    });

    // ─── FIX: Validasi minimal 1 Area & Enable semua input di area yang aktif sebelum form di-submit ───
    document.querySelector('form').addEventListener('submit', function (e) {
        e.preventDefault(); // Tahan pengiriman form sementara

        const activeSections = document.querySelectorAll('.verif-area-section:not(.d-none)');
        const activeIds = Array.from(activeSections).map(sec => sec.getAttribute('data-area-id'));

        // Cek jika tidak ada area yang dipilih / aktif
        if (activeSections.length === 0 || activeIds.length === 0) {
            const msg = 'Silakan pilih minimal 1 Area terlebih dahulu sebelum menyimpan data.';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Area Belum Dipilih!',
                    text: msg,
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                alert(msg);
            }
            return;
        }

        const submitTotal = getTotalInputVarsCount(activeIds);

        if (submitTotal > MAX_INPUT_VARS) {
            const msg = `Total input data yang aktif (${submitTotal} input) melebihi batas maksimal ${MAX_INPUT_VARS} input. Area tidak dapat disimpan secara bersamaan. Silakan kurangi area yang dipilih.`;
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan!',
                    text: msg,
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#d33'
                });
            } else {
                alert(msg);
            }
            return;
        }

        // Enable semua input di area yang aktif/visible
        // agar browser pasti mengambil snapshot SETELAH semua disabled dihapus
        activeSections.forEach(function (section) {
            section.querySelectorAll('select, input, textarea').forEach(function (input) {
                input.removeAttribute('disabled');
            });
        });

        // Baru kirim form setelah DOM selesai dimodifikasi
        this.submit();
    });
});
</script>
@endpush
