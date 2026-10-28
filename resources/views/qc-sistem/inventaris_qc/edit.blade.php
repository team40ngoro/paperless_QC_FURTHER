@extends('layouts.app')

@section('title', 'Edit Checklist Inventaris Peralatan QC')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-edit text-warning mr-2"></i>Edit Checklist Inventaris Peralatan QC</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('inventaris-qc.index') }}">Inventaris QC</a></li>
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
                    <div class="card card-warning card-outline shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-dark">
                                <i class="fas fa-pencil-alt text-warning mr-2"></i>Form Edit Data Checklist Inventaris QC
                            </h3>
                        </div>

                        <form action="{{ route('inventaris-qc.update', $header->uuid) }}" method="POST" id="formChecklistInventarisEdit">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                @if($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle mr-2"></i>Terdapat kesalahan input:</h5>
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

                                <!-- Header Fields -->
                                <div class="row bg-light p-3 rounded mb-4 border">
                                    @if(auth()->user()->role === 'superadmin')
                                        <div class="col-md-3 mb-3">
                                            <div class="form-group mb-0">
                                                <label for="id_plan">Plan</label>
                                                <input type="text" class="form-control bg-white" value="{{ $header->plan->nama_plan ?? '-' }}" readonly>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-md-3 mb-3">
                                        <div class="form-group mb-0">
                                            <label for="id_shift">Shift <span class="text-danger">*</span></label>
                                            <select name="id_shift" id="id_shift" class="form-control @error('id_shift') is-invalid @enderror" required>
                                                <option value="">-- Pilih Shift --</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ old('id_shift', $header->id_shift) == $shift->id ? 'selected' : '' }}>
                                                        Shift {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <div class="form-group mb-0">
                                            <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                            <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', $header->tanggal) }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <div class="form-group mb-0">
                                            <label for="waktu_pemeriksaan">Waktu Pemeriksaan <span class="text-danger">*</span></label>
                                            <select name="waktu_pemeriksaan" id="waktu_pemeriksaan" class="form-control @error('waktu_pemeriksaan') is-invalid @enderror" required>
                                                <option value="">-- Pilih Waktu --</option>
                                                <option value="AWAL SHIFT" {{ old('waktu_pemeriksaan', $header->waktu_pemeriksaan) == 'AWAL SHIFT' ? 'selected' : '' }}>AWAL SHIFT</option>
                                                <option value="AKHIR SHIFT" {{ old('waktu_pemeriksaan', $header->waktu_pemeriksaan) == 'AKHIR SHIFT' ? 'selected' : '' }}>AKHIR SHIFT</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group mb-0">
                                            <label for="catatan_umum">Catatan Umum (Opsional)</label>
                                            <input type="text" name="catatan_umum" id="catatan_umum" class="form-control" placeholder="Masukkan catatan umum jika ada..." value="{{ old('catatan_umum', $header->catatan_umum) }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic Area Selectors Header -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0 text-dark ">
                                        <i class="fas fa-map-marker-alt text-danger mr-2"></i>Pilih Area Pemeriksaan
                                    </h5>
                                </div>

                                <div id="dynamic_area_selectors" class="mb-3">
                                    <!-- Area dynamic dropdown rows inserted via JS -->
                                </div>

                                <div class="mb-4">
                                    <button type="button" id="btn_tambah_area_dynamic" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-plus mr-1"></i> Tambah Area Lain
                                    </button>
                                </div>

                                <!-- Render Area Equipment Cards -->
                                @php
                                    $peralatanByArea = $peralatanList->groupBy('id_area');
                                    $activeAreaIds = array_keys($existingDetailsMap);
                                @endphp

                                <div id="all_area_cards" class="mt-4">
                                    @forelse($areas as $area)
                                        @php
                                            $areaTools = $peralatanByArea->get($area->id, collect());
                                            $isActive = in_array($area->id, $activeAreaIds);
                                        @endphp
                                        <div class="card card-outline card-warning mb-4 area-checklist-section {{ $isActive ? '' : 'd-none' }}" data-area-id="{{ $area->id }}">
                                            <div class="card-header bg-light py-2">
                                                <h3 class="card-title text-dark ">
                                                    <i class="fas fa-warehouse text-warning mr-2"></i>Area: {{ $area->area }}
                                                    <span class="badge badge-warning ml-2 text-dark">{{ $areaTools->count() }} Alat Terdaftar</span>
                                                </h3>
                                                <div class="card-tools d-flex align-items-center">
                                                    <div class="input-group input-group-sm mr-3" style="width: 260px;">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text bg-light text-muted">Set Semua:</span>
                                                        </div>
                                                        <select class="form-control select-all-kondisi" data-area-id="{{ $area->id }}">
                                                            <option value="">-- Pilih Kondisi --</option>
                                                            @foreach($kondisiOptions as $opt)
                                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <button type="button" class="btn btn-tool text-danger btn-close-area-section" data-area-id="{{ $area->id }}" title="Tutup Area Ini">
                                                        <i class="fas fa-times fa-lg"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-0">
                                                @if($areaTools->count() > 0)
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped mb-0 text-center" style="white-space: nowrap;">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th style="width: 50px;">No</th>
                                                                    <th class="text-left" style="min-width: 220px;">Nama Alat</th>
                                                                    <th style="width: 120px;">Jumlah</th>
                                                                    <th style="min-width: 250px;">Kondisi <span class="text-danger">*</span></th>
                                                                    <th style="min-width: 250px;">Keterangan</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($areaTools as $index => $tool)
                                                                    @php
                                                                        $savedDetail = $existingDetailsMap[$area->id][$tool->id] ?? ($existingDetailsMap[$area->id][$tool->nama_alat] ?? null);
                                                                        $savedJumlah = $savedDetail->jumlah ?? ($tool->jumlah_alat ?? 1);
                                                                        $savedKondisi = $savedDetail->kondisi ?? '';
                                                                        $savedKet = $savedDetail->keterangan ?? '';
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $index + 1 }}</td>
                                                                        <td class="text-left">
                                                                            <strong>{{ $tool->nama_alat }}</strong>
                                                                            <input type="hidden" name="details[{{ $area->id }}][{{ $tool->id }}][nama_alat]" value="{{ $tool->nama_alat }}" {{ $isActive ? '' : 'disabled' }}>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="details[{{ $area->id }}][{{ $tool->id }}][jumlah]" class="form-control form-control-sm text-center" value="{{ old("details.{$area->id}.{$tool->id}.jumlah", $savedJumlah) }}" min="1" {{ $isActive ? '' : 'disabled' }} required>
                                                                        </td>
                                                                        <td>
                                                                            <select name="details[{{ $area->id }}][{{ $tool->id }}][kondisi]" class="form-control form-control-sm kondisi-select" data-area-id="{{ $area->id }}" {{ $isActive ? '' : 'disabled' }} required>
                                                                                <option value="">-- Pilih Kondisi --</option>
                                                                                @foreach($kondisiOptions as $opt)
                                                                                    <option value="{{ $opt }}" {{ old("details.{$area->id}.{$tool->id}.kondisi", $savedKondisi) == $opt ? 'selected' : '' }}>
                                                                                        {{ $opt }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="details[{{ $area->id }}][{{ $tool->id }}][keterangan]" class="form-control form-control-sm" placeholder="Catatan kondisi/lokasi..." value="{{ old("details.{$area->id}.{$tool->id}.keterangan", $savedKet) }}" {{ $isActive ? '' : 'disabled' }}>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="p-3 text-center text-muted">
                                                        <i class="fas fa-exclamation-circle text-warning mr-1"></i>
                                                        Belum ada master data alat terdaftar untuk area ini pada Master Data List Peralatan.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="alert alert-warning">Belum ada data area tersedia untuk plan ini.</div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="card-footer py-3 d-flex justify-content-between">
                                <a href="{{ route('inventaris-qc.index') }}" class="btn btn-secondary px-4">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-warning px-4 text-dark ">
                                    <i class="fas fa-save mr-1"></i> Update Data
                                </button>
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
    const selectorContainer = document.getElementById('dynamic_area_selectors');
    const btnTambah = document.getElementById('btn_tambah_area_dynamic');
    const areaSections = document.querySelectorAll('.area-checklist-section');

    const areasList = [
        @foreach($areas as $area)
            { id: "{{ $area->id }}", name: "{{ $area->area }}" },
        @endforeach
    ];

    const initialActiveAreaIds = [
        @foreach($activeAreaIds as $aId)
            "{{ $aId }}",
        @endforeach
    ];

    function addAreaSelectRow(selectedAreaId = '') {
        const div = document.createElement('div');
        div.className = 'row mb-2 area-dynamic-row align-items-center';

        let options = '<option value="">-- Pilih Area --</option>';
        areasList.forEach(area => {
            const isSel = (String(area.id) === String(selectedAreaId)) ? 'selected' : '';
            options += `<option value="${area.id}" ${isSel}>${area.name}</option>`;
        });

        div.innerHTML = `
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-warning text-dark"><i class="fas fa-map-marker-alt"></i></span>
                    </div>
                    <select class="form-control dynamic-area-select" data-previous="${selectedAreaId}">
                        ${options}
                    </select>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-remove-row" title="Hapus Baris Area"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        `;

        selectorContainer.appendChild(div);
        const select = div.querySelector('.dynamic-area-select');

        select.addEventListener('change', function() {
            const oldVal = this.getAttribute('data-previous') || '';
            const newVal = this.value;

            if (newVal !== "") {
                const otherSelected = Array.from(document.querySelectorAll('.dynamic-area-select'))
                    .filter(s => s !== select && s.value === newVal);

                if (otherSelected.length > 0) {
                    alert('Area ini sudah dipilih di baris lain.');
                    this.value = oldVal;
                    return;
                }
            }

            refreshAllVisibility();
            this.setAttribute('data-previous', this.value);
        });

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
            if (selectedIds.includes(id)) {
                section.classList.remove('d-none');
                inputs.forEach(input => input.removeAttribute('disabled'));
            } else {
                section.classList.add('d-none');
                inputs.forEach(input => input.setAttribute('disabled', 'disabled'));
            }
        });
    }

    // Populate initial saved area rows
    if (initialActiveAreaIds.length > 0) {
        initialActiveAreaIds.forEach(id => {
            addAreaSelectRow(id);
        });
    } else {
        addAreaSelectRow();
    }

    btnTambah.addEventListener('click', function() {
        addAreaSelectRow();
    });

    // Close area card listener
    document.addEventListener('click', function(e) {
        const btnClose = e.target.closest('.btn-close-area-section');
        if (btnClose) {
            const areaId = btnClose.getAttribute('data-area-id');
            document.querySelectorAll('.dynamic-area-select').forEach(sel => {
                if (sel.value === areaId) {
                    sel.closest('.area-dynamic-row').remove();
                }
            });
            refreshAllVisibility();
        }
    });

    // Bulk set condition select for an entire area
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('select-all-kondisi')) {
            const areaId = e.target.getAttribute('data-area-id');
            const selectedVal = e.target.value;
            if (selectedVal) {
                document.querySelectorAll(`.kondisi-select[data-area-id="${areaId}"]`).forEach(sel => {
                    sel.value = selectedVal;
                });
            }
        }
    });

    // Form submit validation to enable visible fields
    document.getElementById('formChecklistInventarisEdit').addEventListener('submit', function (e) {
        const activeSections = document.querySelectorAll('.area-checklist-section:not(.d-none)');
        if (activeSections.length === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal 1 Area terlebih dahulu sebelum menyimpan data.');
            return;
        }

        activeSections.forEach(function (section) {
            section.querySelectorAll('select, input, textarea').forEach(function (input) {
                input.removeAttribute('disabled');
            });
        });
    });
});
</script>
@endpush
