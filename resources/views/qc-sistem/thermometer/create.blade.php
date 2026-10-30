@extends('layouts.app')

@section('title', 'Tambah Data Thermometer')

@section('container')
<!-- Content Header (Page header) -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Data Thermometer</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <!-- <li class="breadcrumb-item"><a href="#">QC Sistem</a></li> -->
                            <li class="breadcrumb-item"><a href="{{ route('thermometer.index') }}">Data Thermometer</a></li>
                            <li class="breadcrumb-item active">Tambah Data</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-thermometer-half mr-1"></i>
                                    Form Tambah Data Thermometer
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            
                            <form action="{{ route('thermometer.store') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    @if(session('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    @if(session('error'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <h5 class="mb-2"><i class="fas fa-ban mr-1"></i> Terjadi Kesalahan:</h5>
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    <!-- Common Fields Section -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="card card-outline card-info">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-info-circle"></i>
                                                        Informasi Umum
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="shift_id">
                                                                    <i class="fas fa-clock"></i>
                                                                    Shift <span class="text-danger">*</span>
                                                                </label>
                                                                <select name="shift_id" id="shift_id" class="form-control @error('shift_id') is-invalid @enderror" required>
                                                                    <option value="">-- Pilih Shift --</option>
                                                                    @foreach($shifts as $shift)
                                                                        <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                                            {{ $shift->shift }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('shift_id')
                                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                             <div class="form-group">
                                                                    <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                                                    <input type="date" name="tanggal" id="tanggal"
                                                                        class="form-control @error('tanggal') is-invalid @enderror"
                                                                        value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                                                    @error('tanggal')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                        </div>
                                                      
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="jam">Pukul <span class="text-danger">*</span></label>
                                                                <input type="time" name="jam" id="jam" class="form-control @error('jam') is-invalid @enderror" value="{{ old('jam', date('H:i')) }}" required>
                                                                @error('jam')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Summary Cards -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="info-box bg-info">
                                                <span class="info-box-icon"><i class="fas fa-list"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Entries</span>
                                                    <span class="info-box-number" id="total-entries">1</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box bg-success">
                                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Valid Entries</span>
                                                    <span class="info-box-number" id="valid-entries">0</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box bg-warning">
                                                <span class="info-box-icon"><i class="fas fa-exclamation"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Incomplete</span>
                                                    <span class="info-box-number" id="incomplete-entries">1</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Repeater Section -->
                                    <ul class="nav nav-tabs" id="verifikasiTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="tab-thermometer" data-toggle="tab" href="#pane-thermometer" role="tab" aria-controls="pane-thermometer" aria-selected="true">Thermometer</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content" id="verifikasiTabsContent">
                                        <div class="tab-pane fade show active pt-3" id="pane-thermometer" role="tabpanel" aria-labelledby="tab-thermometer">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="thermometerTable" style="white-space: nowrap;">
                                                    <thead class="bg-light text-center">
                                                        <tr>
                                                            <th style="width:5%">No</th>
                                                            <th>Jenis &amp; Kode Thermometer</th>
                                                            <th>Area</th>
                                                            <th style="width:20%">0&deg;C</th>
                                                            <!-- <th style="width:20%">100&deg;C</th> -->
                                                            <th style="width:10%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="thermometerTableBody">
                                                        <tr data-index="0">
                                                            <td class="text-center">1</td>
                                                            <td>
                                                                <select class="form-control select-thermo select2-init" id="id_thermo_select_0" data-index="0" required>
                                                                    <option value="">-- Pilih Thermometer --</option>
                                                                    @foreach($dataThermo as $thermo)
                                                                        <option value="{{ $thermo->id }}" data-jenis="{{ $thermo->nama_thermo }}" data-kode="{{ $thermo->kode_thermo }}">
                                                                            {{ $thermo->nama_thermo }} - {{ $thermo->kode_thermo }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <input type="hidden" name="entries[0][jenis]" id="jenis_0" value="{{ old('entries.0.jenis') }}" required>
                                                                <input type="hidden" name="entries[0][kode_thermometer]" id="kode_thermometer_0" value="{{ old('entries.0.kode_thermometer') }}" required>
                                                                <input type="hidden" name="entries[0][hasil_pengecekan]" value="ok">
                                                            </td>
                                                            <td>
                                                                <select name="entries[0][area]" class="form-control select-area select2-init" id="area_0">
                                                                    <option value="">-- Pilih Area --</option>
                                                                    @foreach($inputAreas as $area)
                                                                        <option value="{{ $area->area }}">{{ $area->area }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <!-- <td>
                                                                <input type="text" class="form-control entry-field" name="entries[0][hasil_verifikasi_0]" value="{{ old('entries.0.hasil_verifikasi_0') }}" placeholder="Isi hasil">
                                                            </td> -->
                                                            <td>
                                                                <input type="text" class="form-control entry-field" name="entries[0][hasil_verifikasi_100]" value="{{ old('entries.0.hasil_verifikasi_100') }}" placeholder="Isi hasil">
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-danger btn-sm btn-remove-row">Hapus</button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <button type="button" class="btn btn-success" id="addRowBtn">Tambah Detail Thermometer</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                                <i class="fas fa-save"></i> Simpan <span id="submit-count">1</span> Entry
                                            </button>
                                            <a href="{{ route('thermometer.index') }}" class="btn btn-secondary ml-2">
                                                <i class="fas fa-arrow-left"></i> Kembali
                                            </a>
                                        </div>
                                        <!-- <div class="col-md-6 text-right">
                                            <small class="text-muted">
                                                <i class="fas fa-asterisk text-danger"></i> 
                                                Field yang wajib diisi
                                            </small>
                                        </div> -->
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
</div>
@push('scripts')
<script>
$(document).ready(function() {
    let rowIndex = 0;

    function initSelect2(element) {
        $(element).find('.select2-init').select2({
            placeholder: "Pilih...",
            allowClear: true,
            width: '100%'
        });
    }

    initSelect2(document);

    function updateRemoveButtons() {
        const total = $('#thermometerTableBody tr').length;
        if (total > 1) {
            $('.btn-remove-row').show();
        } else {
            $('.btn-remove-row').hide();
        }
    }

    function reindexRows() {
        $('#thermometerTableBody tr').each(function(i) {
            $(this).attr('data-index', i);
            $(this).find('td').eq(0).text(i + 1);

            // Re-id/name and re-init select2 if necessary
            $(this).find('.select-thermo').attr('data-index', i).attr('id', `id_thermo_select_${i}`);
            $(this).find('.select-area').attr('id', `area_${i}`).attr('name', `entries[${i}][area]`);
            $(this).find('input[name^="entries["][name$="][jenis]"]').attr('name', `entries[${i}][jenis]`).attr('id', `jenis_${i}`);
            $(this).find('input[name^="entries["][name$="][kode_thermometer]"]').attr('name', `entries[${i}][kode_thermometer]`).attr('id', `kode_thermometer_${i}`);
            $(this).find('input[name^="entries["][name$="][hasil_pengecekan]"]').attr('name', `entries[${i}][hasil_pengecekan]`);
            $(this).find('input[name^="entries["][name$="][hasil_verifikasi_100]"]').attr('name', `entries[${i}][hasil_verifikasi_100]`);
        });
        rowIndex = $('#thermometerTableBody tr').length - 1;
        updateRemoveButtons();
    }

    function updateSummary() {
        const totalRows = $('#thermometerTableBody tr').length;
        let validRows = 0;
        let incompleteRows = 0;

        $('#thermometerTableBody tr').each(function() {
            const jenis = $(this).find('input[name*="[jenis]"]').val();
            const kode = $(this).find('input[name*="[kode_thermometer]"]').val();

            if (jenis && kode) {
                validRows++;
            } else {
                incompleteRows++;
            }
        });

        $('#total-entries').text(totalRows);
        $('#valid-entries').text(validRows);
        $('#incomplete-entries').text(incompleteRows);
        $('#submit-count').text(totalRows);
    }

    $('#addRowBtn').on('click', function() {
        rowIndex++;
        const newRow = `
            <tr data-index="${rowIndex}">
                <td class="text-center">${rowIndex + 1}</td>
                <td>
                    <select class="form-control select-thermo select2-init" id="id_thermo_select_${rowIndex}" data-index="${rowIndex}" required>
                        <option value="">-- Pilih Thermometer --</option>
                        @foreach($dataThermo as $thermo)
                            <option value="{{ $thermo->id }}" data-jenis="{{ $thermo->nama_thermo }}" data-kode="{{ $thermo->kode_thermo }}">
                                {{ $thermo->nama_thermo }} - {{ $thermo->kode_thermo }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="entries[${rowIndex}][jenis]" id="jenis_${rowIndex}" required>
                    <input type="hidden" name="entries[${rowIndex}][kode_thermometer]" id="kode_thermometer_${rowIndex}" required>
                    <input type="hidden" name="entries[${rowIndex}][hasil_pengecekan]" value="ok">
                </td>
                <td>
                    <select name="entries[${rowIndex}][area]" class="form-control select-area select2-init" id="area_${rowIndex}">
                        <option value="">-- Pilih Area --</option>
                        @foreach($inputAreas as $area)
                            <option value="{{ $area->area }}">{{ $area->area }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control entry-field" name="entries[${rowIndex}][hasil_verifikasi_100]" placeholder="Isi hasil">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-row">Hapus</button>
                </td>
            </tr>
        `;

        $('#thermometerTableBody').append(newRow);
        initSelect2($(`#thermometerTableBody tr[data-index="${rowIndex}"]`));
        updateRemoveButtons();
        updateSummary();
    });

    $(document).on('click', '.btn-remove-row', function() {
        $(this).closest('tr').remove();
        reindexRows();
        updateSummary();
    });

    $(document).on('change', '.select-thermo', function() {
        const index = $(this).data('index');
        const opt = $(this).find('option:selected');
        const jenis = opt.data('jenis') || '';
        const kode = opt.data('kode') || '';

        $(`#jenis_${index}`).val(jenis);
        $(`#kode_thermometer_${index}`).val(kode);
        updateSummary();
    });

    $(document).on('input change', '.entry-field', function() {
        updateSummary();
    });

    $('form').on('submit', function(e) {
        const rows = $('#thermometerTableBody tr');
        const jamHeader = $('#jam').val();
        if (rows.length === 0) {
            alert('Minimal harus ada 1 entry thermometer!');
            e.preventDefault();
            return false;
        }
        if (!jamHeader) {
            alert('Pukul harus diisi!');
            e.preventDefault();
            return false;
        }

        let hasError = false;
        rows.each(function() {
            const jenis = $(this).find('input[name*="[jenis]"]').val();
            const kode = $(this).find('input[name*="[kode_thermometer]"]').val();
            if (!jenis || !kode) {
                hasError = true;
            }
        });

        if (hasError) {
            alert('Semua field pada setiap entry harus diisi!');
            e.preventDefault();
            return false;
        }
    });

    updateSummary();
    updateRemoveButtons();
});
</script>
@endpush
@endsection