@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Data GMP Karyawan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gmp-karyawan.index') }}">GMP Karyawan</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
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
                <div class="col-md-12">
                    <div class="card card-warning">
                        <div class="card-header p-2">
                            <h3 class="card-title mt-1 ml-2">
                                <i class="fas fa-edit mr-1"></i>
                                Form Edit GMP Karyawan
                            </h3>
                        </div>
                        <div class="card-body">
                            <!-- Card Informasi Keterangan GMP Karyawan -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card card-info card-outline collapsed-card">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-info-circle"></i> Keterangan Penilaian GMP Karyawan
                                            </h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="alert alert-warning">
                                                        <h5><i class="fas fa-clipboard-list"></i> Kriteria Penilaian:</h5>
                                                        <ol class="mb-0">
                                                            <li><strong>Perlengkapan</strong> (booth, seragam, masker, sarung tangan)</li>
                                                            <li><strong>Kuku</strong></li>
                                                            <li><strong>Perhiasan</strong></li>
                                                            <li><strong>Luka</strong></li>
                                                            <li><strong>Tidak ada temuan</strong></li>
                                                        </ol>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="info-box bg-success">
                                                        <span class="info-box-icon">
                                                            <i class="fas fa-check-circle" style="font-size: 2rem;"></i>
                                                        </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">✓ (Sesuai)</span>
                                                            <span class="info-box-number">Memenuhi Standar GMP</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-box bg-danger">
                                                        <span class="info-box-icon">
                                                            <i class="fas fa-times-circle" style="font-size: 2rem;"></i>
                                                        </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">✗ (Tidak Sesuai)</span>
                                                            <span class="info-box-number">Belum Memenuhi Standar</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('gmp-karyawan.update', $gmpKaryawan->uuid) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row mb-4">
                                    <!-- Tanggal -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal_only">Tanggal <span class="text-danger">*</span></label>
                                            <input type="date"
                                                name="tanggal_only"
                                                id="tanggal_only"
                                                class="form-control"
                                                value="{{ \Carbon\Carbon::parse($gmpKaryawan->tanggal)->format('Y-m-d') }}" required>
                                            <input type="hidden"
                                                name="tanggal"
                                                id="tanggal"
                                                value="{{ \Carbon\Carbon::parse($gmpKaryawan->tanggal)->format('d-m-Y H:i:s') }}">
                                        </div>
                                    </div>

                                    <!-- Shift -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="shift_id">Shift <span class="text-danger">*</span></label>
                                            <select class="form-control @error('shift_id') is-invalid @enderror" 
                                                    id="shift_id" name="shift_id" required>
                                                <option value="">-- Pilih Shift --</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" 
                                                        {{ (old('shift_id', $gmpKaryawan->shift_id) == $shift->id) ? 'selected' : '' }}>
                                                        {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('shift_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered" id="gmpTable" style="min-width: 1200px;">
                                        <thead class="bg-light text-center">
                                            <tr>
                                                <th style="width: 40px;">No</th>
                                                <th style="width: 100px;">Jam</th>
                                                <th style="width: 200px;">Area</th>
                                                <th style="width: 200px;">Nama Karyawan</th>
                                                <th style="width: 200px;">Temuan</th>
                                                <th style="width: 120px;">Verifikasi</th>
                                                <th>Keterangan & Tindakan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="gmpTableBody">
                                            @php
                                                $entries = is_array($gmpKaryawan->entries) && count($gmpKaryawan->entries) > 0 ? $gmpKaryawan->entries : [
                                                    [
                                                        'jam' => $gmpKaryawan->jam,
                                                        'id_area' => $gmpKaryawan->id_area,
                                                        'nama_karyawan' => $gmpKaryawan->nama_karyawan,
                                                        'temuan_ketidaksesuaian' => $gmpKaryawan->temuan_ketidaksesuaian,
                                                        'verifikasi' => $gmpKaryawan->verifikasi,
                                                        'keterangan' => $gmpKaryawan->keterangan,
                                                        'tindakan_koreksi' => $gmpKaryawan->tindakan_koreksi,
                                                        'koreksi_lanjutan' => $gmpKaryawan->koreksi_lanjutan,
                                                    ]
                                                ];
                                            @endphp

                                            @foreach($entries as $index => $entry)
                                            <tr data-index="{{ $index }}">
                                                <td class="text-center row-number">{{ $index + 1 }}</td>
                                                <td>
                                                    <input type="time" class="form-control" name="entries[{{ $index }}][jam]" value="{{ isset($entry['jam']) ? \Carbon\Carbon::parse($entry['jam'])->format('H:i') : '' }}" required>
                                                </td>
                                                <td>
                                                    <select class="form-control select2-area" name="entries[{{ $index }}][id_area]" required>
                                                        <option value="">Pilih Area</option>
                                                        @foreach($areas as $area)
                                                            <option value="{{ $area->id }}" {{ (isset($entry['id_area']) && $entry['id_area'] == $area->id) ? 'selected' : '' }}>{{ $area->area }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="entries[{{ $index }}][nama_karyawan]" value="{{ $entry['nama_karyawan'] ?? '' }}" placeholder="Nama Karyawan" required>
                                                </td>
                                                <td>
                                                    <select class="form-control" name="entries[{{ $index }}][temuan_ketidaksesuaian]" required>
                                                        <option value="">Pilih Temuan</option>
                                                        @foreach($temuanOptions as $key => $label)
                                                            <option value="{{ $key }}" {{ (isset($entry['temuan_ketidaksesuaian']) && $entry['temuan_ketidaksesuaian'] == $key) ? 'selected' : '' }}>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control verifikasi-select" name="entries[{{ $index }}][verifikasi]" required>
                                                        <option value="">-- Pilih --</option>
                                                        <option value="ok" {{ (isset($entry['verifikasi']) && $entry['verifikasi'] == 'ok') ? 'selected' : '' }}>OK</option>
                                                        <option value="tidak_ok" {{ (isset($entry['verifikasi']) && $entry['verifikasi'] == 'tidak_ok') ? 'selected' : '' }}>Tidak OK</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="mb-2">
                                                        <textarea class="form-control mb-1" name="entries[{{ $index }}][keterangan]" rows="1" placeholder="Keterangan">{{ $entry['keterangan'] ?? '' }}</textarea>
                                                        <textarea class="form-control" name="entries[{{ $index }}][tindakan_koreksi]" rows="1" placeholder="Tindakan Koreksi">{{ $entry['tindakan_koreksi'] ?? '' }}</textarea>
                                                    </div>
                                                    
                                                    <!-- Koreksi Lanjutan Section -->
                                                    <div class="koreksi-lanjutan-section p-2 bg-light border rounded" style="display: {{ (isset($entry['verifikasi']) && $entry['verifikasi'] == 'tidak_ok') ? 'block' : 'none' }}; font-size: 0.85rem;">
                                                        <div class="font-weight-bold text-warning mb-1"><i class="fas fa-exclamation-triangle"></i> Koreksi Lanjutan</div>
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <input type="text" class="form-control form-control-sm" name="entries[{{ $index }}][keterangan_koreksi_lanjutan]" value="{{ $entry['keterangan_koreksi_lanjutan'] ?? '' }}" placeholder="Catatan">
                                                            </div>
                                                            <div class="col-4">
                                                                <input type="text" class="form-control form-control-sm" name="entries[{{ $index }}][tindakan_koreksi_lanjutan]" value="{{ $entry['tindakan_koreksi_lanjutan'] ?? '' }}" placeholder="Tindakan">
                                                            </div>
                                                            <div class="col-4">
                                                                <select class="form-control form-control-sm" name="entries[{{ $index }}][koreksi_lanjutan]">
                                                                    <option value="">-- Status --</option>
                                                                    <option value="ok" {{ (isset($entry['koreksi_lanjutan']) && $entry['koreksi_lanjutan'] == 'ok') ? 'selected' : '' }}>OK</option>
                                                                    <option value="tidak_ok" {{ (isset($entry['koreksi_lanjutan']) && $entry['koreksi_lanjutan'] == 'tidak_ok') ? 'selected' : '' }}>Tidak OK</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm remove-row" style="{{ count($entries) > 1 ? '' : 'display: none;' }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3 mb-4">
                                    <button type="button" class="btn btn-success" id="add-row">
                                        <i class="fas fa-plus-circle"></i> Tambah Baris Karyawan
                                    </button>
                                </div>

                                <div class="card-footer px-0 bg-transparent border-top pt-3">
                                    <button type="submit" class="btn btn-warning btn-md">
                                        <i class="fas fa-save mr-1"></i> Update Data
                                    </button>
                                    <a href="{{ route('gmp-karyawan.index') }}" class="btn btn-secondary btn-md">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@push('scripts')
<script>
$(document).ready(function() {
    let rowIndex = $('#gmpTableBody tr').length - 1;

    // Initialize Select2
    function initSelect2(row) {
        row.find('.select2-area').select2({
            placeholder: "Pilih Area",
            allowClear: true,
            theme: 'bootstrap4',
            width: '100%'
        });
    }

    // Init existing rows
    $('#gmpTableBody tr').each(function() {
        initSelect2($(this));
    });

    function updateRowUI() {
        const rows = $('#gmpTableBody tr');
        rows.each(function(index) {
            $(this).find('.row-number').text(index + 1);
            const removeBtn = $(this).find('.remove-row');
            if (rows.length > 1) {
                removeBtn.show();
            } else {
                removeBtn.hide();
            }
        });
    }

    // Add New Row
    $('#add-row').on('click', function() {
        rowIndex++;
        const template = `
            <tr data-index="${rowIndex}">
                <td class="text-center row-number">${rowIndex + 1}</td>
                <td>
                    <input type="time" class="form-control" name="entries[${rowIndex}][jam]" value="{{ now()->format('H:i') }}" required>
                </td>
                <td>
                    <select class="form-control select2-area" name="entries[${rowIndex}][id_area]" required>
                        <option value="">Pilih Area</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->area }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control" name="entries[${rowIndex}][nama_karyawan]" placeholder="Nama Karyawan" required>
                </td>
                <td>
                    <select class="form-control" name="entries[${rowIndex}][temuan_ketidaksesuaian]" required>
                        <option value="">Pilih Temuan</option>
                        @foreach($temuanOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select class="form-control verifikasi-select" name="entries[${rowIndex}][verifikasi]" required>
                        <option value="">-- Pilih --</option>
                        <option value="ok">OK</option>
                        <option value="tidak_ok">Tidak OK</option>
                    </select>
                </td>
                <td>
                    <div class="mb-2">
                        <textarea class="form-control mb-1" name="entries[${rowIndex}][keterangan]" rows="1" placeholder="Keterangan"></textarea>
                        <textarea class="form-control" name="entries[${rowIndex}][tindakan_koreksi]" rows="1" placeholder="Tindakan Koreksi"></textarea>
                    </div>
                    <div class="koreksi-lanjutan-section p-2 bg-light border rounded" style="display: none; font-size: 0.85rem;">
                        <div class="font-weight-bold text-warning mb-1"><i class="fas fa-exclamation-triangle"></i> Koreksi Lanjutan</div>
                        <div class="row">
                            <div class="col-4">
                                <input type="text" class="form-control form-control-sm" name="entries[${rowIndex}][keterangan_koreksi_lanjutan]" placeholder="Catatan">
                            </div>
                            <div class="col-4">
                                <input type="text" class="form-control form-control-sm" name="entries[${rowIndex}][tindakan_koreksi_lanjutan]" placeholder="Tindakan">
                            </div>
                            <div class="col-4">
                                <select class="form-control form-control-sm" name="entries[${rowIndex}][koreksi_lanjutan]">
                                    <option value="">-- Status --</option>
                                    <option value="ok">OK</option>
                                    <option value="tidak_ok">Tidak OK</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        const newRow = $(template);
        $('#gmpTableBody').append(newRow);
        initSelect2(newRow);
        updateRowUI();
    });

    // Remove Row
    $(document).on('click', '.remove-row', function() {
        if ($('#gmpTableBody tr').length > 1) {
            $(this).closest('tr').remove();
            reindexRows();
            updateRowUI();
        }
    });

    function reindexRows() {
        $('#gmpTableBody tr').each(function(i) {
            $(this).attr('data-index', i);
            $(this).find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    const newName = name.replace(/entries\[\d+\]/, `entries[${i}]`);
                    $(this).attr('name', newName);
                }
            });
        });
        rowIndex = $('#gmpTableBody tr').length - 1;
    }

    // Handle Verifikasi Change
    $(document).on('change', '.verifikasi-select', function() {
        const section = $(this).closest('td').find('.koreksi-lanjutan-section');
        if ($(this).val() === 'tidak_ok') {
            section.fadeIn();
        } else {
            section.fadeOut();
        }
    });

    updateRowUI();
});
</script>
@endpush
@endsection