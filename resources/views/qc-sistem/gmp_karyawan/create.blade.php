@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Data GMP Karyawan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gmp-karyawan.index') }}">GMP Karyawan</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
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
                    <div class="card card-primary">
                        <div class="card-header p-2">
                            <h3 class="card-title mt-1 ml-2">
                                <i class="fas fa-user-check mr-1"></i>
                                Form Laporan GMP Karyawan
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

                            <form action="{{ route('gmp-karyawan.store') }}" method="POST">
                                @csrf

                                <div class="row mb-4">
                                    <!-- Tanggal -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal<span class="text-danger">*</span></label>
                                            <input type="date" name="tanggal" id="tanggal"
                                                class="form-control @error('tanggal') is-invalid @enderror"
                                                value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                            @error('tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Shift -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="shift_id">Shift <span class="text-danger">*</span></label>
                                            <select class="form-control @error('shift_id') is-invalid @enderror" id="shift_id" name="shift_id" required>
                                                <option value="">-- Pilih Shift --</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
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
                                                <th style="width: 50px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="gmpTableBody">
                                            <tr data-index="0">
                                                <td class="text-center row-number">1</td>
                                                <td>
                                                    <input type="time" class="form-control" name="entries[0][jam]" value="{{ now()->format('H:i') }}" required>
                                                </td>
                                                <td>
                                                    <select class="form-control select2-area" name="entries[0][id_area]" required>
                                                        <option value="">Pilih Area</option>
                                                        @foreach($areas as $area)
                                                            <option value="{{ $area->id }}">{{ $area->area }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="entries[0][nama_karyawan]" placeholder="Nama Karyawan" required>
                                                </td>
                                                <td>
                                                    <select class="form-control" name="entries[0][temuan_ketidaksesuaian]" required>
                                                        <option value="">Pilih Temuan</option>
                                                        @foreach($temuanOptions as $key => $label)
                                                            <option value="{{ $key }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control verifikasi-select" name="entries[0][verifikasi]" required>
                                                        <option value="">-- Pilih --</option>
                                                        <option value="ok">OK</option>
                                                        <option value="tidak_ok">Tidak OK</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="mb-2">
                                                        <textarea class="form-control mb-1" name="entries[0][keterangan]" rows="1" placeholder="Keterangan"></textarea>
                                                        <textarea class="form-control" name="entries[0][tindakan_koreksi]" rows="1" placeholder="Tindakan Koreksi"></textarea>
                                                    </div>
                                                    
                                                    <!-- Koreksi Lanjutan Section -->
                                                    <div class="koreksi-lanjutan-section p-2 bg-light border rounded" style="display: none; font-size: 0.85rem;">
                                                        <div class="font-weight-bold text-warning mb-1"><i class="fas fa-exclamation-triangle"></i> Koreksi Lanjutan</div>
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <input type="text" class="form-control form-control-sm" name="entries[0][keterangan_koreksi_lanjutan]" placeholder="Catatan">
                                                            </div>
                                                            <div class="col-4">
                                                                <input type="text" class="form-control form-control-sm" name="entries[0][tindakan_koreksi_lanjutan]" placeholder="Tindakan">
                                                            </div>
                                                            <div class="col-4">
                                                                <select class="form-control form-control-sm" name="entries[0][koreksi_lanjutan]">
                                                                    <option value="">-- Status --</option>
                                                                    <option value="ok">OK</option>
                                                                    <option value="tidak_ok">Tidak OK</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm remove-row" style="display: none;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3 mb-4">
                                    <button type="button" class="btn btn-success" id="add-row">
                                        <i class="fas fa-plus-circle"></i> Tambah Baris Karyawan
                                    </button>
                                </div>

                                <div class="card-footer px-0 bg-transparent border-top pt-3">
                                    <button type="submit" class="btn btn-primary btn-md">
                                        <i class="fas fa-save mr-1"></i> Simpan Data
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
    let rowIndex = 0;

    // Initialize Select2
    function initSelect2(row) {
        row.find('.select2-area').select2({
            placeholder: "Pilih Area",
            allowClear: true,
            theme: 'bootstrap4',
            width: '100%'
        });
    }

    initSelect2($('#gmpTableBody tr[data-index="0"]'));

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