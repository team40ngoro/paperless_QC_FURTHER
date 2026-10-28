@extends('layouts.app')

@section('title', 'Tambah Data Timbangan')

@section('container')
<!-- Content Header (Page header) -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Buat Laporan Verifikasi Timbangan</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <!-- <li class="breadcrumb-item"><a href="#">QC Sistem</a></li> -->
                            <li class="breadcrumb-item"><a href="{{ route('timbangan.index') }}">Data Timbangan</a></li>
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
                                <i class="fas fa-balance-scale mr-1"></i>
                                Buat Laporan Verifikasi Timbangan 
                            </h3>
                        </div>
                        <!-- /.card-header -->
                        
                        <form action="{{ route('timbangan.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @php
                                    $user = auth()->user();
                                    $roleId = $user->id_role ?? $user->role ?? 0;
                                @endphp

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal</label>
                                            <input type="date" name="tanggal" id="tanggal"
                                                class="form-control @error('tanggal') is-invalid @enderror"
                                                value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                            @error('tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="shift_id">Shift</label>
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
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jam">Jam</label>
                                            <input type="time" name="jam" id="jam" class="form-control @error('jam') is-invalid @enderror" value="{{ old('jam', date('H:i')) }}" required>
                                            @error('jam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <ul class="nav nav-tabs" id="verifikasiTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="tab-timbangan" data-toggle="tab" href="#pane-timbangan" role="tab" aria-controls="pane-timbangan" aria-selected="true">Timbangan</a>
                                    </li>
                                   
                                </ul>

                                <div class="tab-content" id="verifikasiTabsContent">
                                    <div class="tab-pane fade show active pt-3" id="pane-timbangan" role="tabpanel" aria-labelledby="tab-timbangan">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="timbanganTable" style="white-space: nowrap;">
                                                <thead class="bg-light text-center">
                                                    <tr>
                                                        <th style="width:5%">No</th>
                                                        <th>Jenis dan Kode Timbangan</th>
                                                        <th style="width:15%">Area</th>
                                                        <th style="width:15%">Hasil Verifikasi 500 Gr</th>
                                                        <th style="width:15%">Hasil Verifikasi 1000 Gr</th>
                                                        <th style="width:15%">Hasil Verifikasi 10000 Gr</th>
                                                        <th style="width:8%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="timbanganTableBody">
                                                    <tr data-index="0">
                                                        <td class="text-center">1</td>
                                                        <td>
                                                            <select class="form-control select-timbangan" data-index="0">
                                                                <option value="">-- Pilih Timbangan --</option>
                                                                @foreach($dataTimbangan as $timbangan)
                                                                    <option value="{{ $timbangan->id }}" data-nama="{{ $timbangan->nama_timbangan }}" data-kode="{{ $timbangan->kode_timbangan }}">
                                                                        {{ $timbangan->nama_timbangan }} - {{ $timbangan->kode_timbangan }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <input type="hidden" name="entries[0][jenis]" id="jenis_0" value="{{ old('entries.0.jenis') }}" required>
                                                            <input type="hidden" name="entries[0][kode_timbangan]" id="kode_timbangan_0" value="{{ old('entries.0.kode_timbangan') }}" required>
                                                            <input type="hidden" name="entries[0][hasil_pengecekan]" value="ok">
                                                            <input type="hidden" name="entries[0][gram]" value="500">
                                                        </td>
                                                        <td>
                                                            <select class="form-control select-area" name="entries[0][area]">
                                                                <option value="">-- Pilih Area --</option>
                                                                @foreach($inputAreas as $area)
                                                                    <option value="{{ $area->area }}" {{ old('entries.0.area') == $area->area ? 'selected' : '' }}>{{ $area->area }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="entries[0][hasil_verifikasi_500]" value="{{ old('entries.0.hasil_verifikasi_500') }}" placeholder="Isi hasil">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="entries[0][hasil_verifikasi_1000]" value="{{ old('entries.0.hasil_verifikasi_1000') }}" placeholder="Isi hasil">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="entries[0][hasil_verifikasi_10000]" value="{{ old('entries.0.hasil_verifikasi_10000') }}" placeholder="Isi hasil">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger btn-sm btn-remove-row" style="display:none;">Hapus</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <button type="button" class="btn btn-success mb-3" id="addRowBtn">Tambah Detail Timbangan</button>

                                        <!-- SECTION: CENTANG UNTUK MEMBUKA FORM VERIFIKASI TAMBAHAN -->
                                        <div class="card card-outline card-info my-4">
                                            <div class="card-header">
                                                <h3 class="card-title font-weight-bold text-info"><i class="fas fa-tasks mr-1"></i> Form Verifikasi Check Weigher (Opsional)</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-2">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="enable_checkweigher_bag" name="enable_checkweigher_bag" value="1" {{ old('enable_checkweigher_bag') ? 'checked' : '' }}>
                                                            <label class="custom-control-label font-weight-bold text-primary" for="enable_checkweigher_bag">
                                                                <i class="fas fa-shopping-bag mr-1"></i> Verifikasi Penggunaan Check Weigher Bag
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="enable_checkweigher_box" name="enable_checkweigher_box" value="1" {{ old('enable_checkweigher_box') ? 'checked' : '' }}>
                                                            <label class="custom-control-label font-weight-bold text-info" for="enable_checkweigher_box">
                                                                <i class="fas fa-box mr-1"></i> Verifikasi Penggunaan Check Weigher Box
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SECTION: VERIFIKASI CHECK WEIGHER BAG -->
                                        <div id="section_checkweigher_bag" class="card card-outline card-primary mb-4 d-none">
                                            <div class="card-header">
                                                <h3 class="card-title font-weight-bold text-primary m-0"><i class="fas fa-shopping-bag mr-1"></i> Verifikasi Penggunaan Check Weigher Bag</h3>
                                            </div>
                                            <div class="card-body" id="bagContainer">
                                                <!-- Dynamic Items inserted via JS -->
                                            </div>
                                            <div class="card-footer bg-light text-right">
                                                <button type="button" class="btn btn-sm btn-primary" id="addBagRowBtn">
                                                    <i class="fas fa-plus"></i> Tambah Data Bag
                                                </button>
                                            </div>
                                        </div>

                                        <!-- SECTION: VERIFIKASI CHECK WEIGHER BOX -->
                                        <div id="section_checkweigher_box" class="card card-outline card-info mb-4 d-none">
                                            <div class="card-header">
                                                <h3 class="card-title font-weight-bold text-info m-0"><i class="fas fa-box mr-1"></i> Verifikasi Penggunaan Check Weigher Box</h3>
                                            </div>
                                            <div class="card-body" id="boxContainer">
                                                <!-- Dynamic Items inserted via JS -->
                                            </div>
                                            <div class="card-footer bg-light text-right">
                                                <button type="button" class="btn btn-sm btn-info text-white" id="addBoxRowBtn">
                                                    <i class="fas fa-plus"></i> Tambah Data Box
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                 
                                </div>
                             
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Data
                                </button>
                                <a href="{{ route('timbangan.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
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
    let bagIndex = 0;
    let boxIndex = 0;

    // Initialize Select2 on the first row
    $('#timbanganTableBody tr[data-index="0"] .select-timbangan').select2({
        placeholder: "Pilih Timbangan",
        allowClear: true,
        width: '100%'
    });
    $('#timbanganTableBody tr[data-index="0"] .select-area').select2({
        placeholder: "Pilih Area",
        allowClear: true,
        width: '100%'
    });

    function updateRemoveButtons() {
        const total = $('#timbanganTableBody tr').length;
        if (total > 1) {
            $('.btn-remove-row').show();
        } else {
            $('.btn-remove-row').hide();
        }
    }

    function reindexRows() {
        $('#timbanganTableBody tr').each(function(i) {
            $(this).attr('data-index', i);
            $(this).find('td').eq(0).text(i + 1);

            $(this).find('.select-timbangan').attr('data-index', i);
            $(this).find('input[name^="entries["][name$="][jenis]"]').attr('name', `entries[${i}][jenis]`).attr('id', `jenis_${i}`);
            $(this).find('input[name^="entries["][name$="][kode_timbangan]"]').attr('name', `entries[${i}][kode_timbangan]`).attr('id', `kode_timbangan_${i}`);
            $(this).find('input[name^="entries["][name$="][hasil_pengecekan]"]').attr('name', `entries[${i}][hasil_pengecekan]`);
            $(this).find('input[name^="entries["][name$="][gram]"]').attr('name', `entries[${i}][gram]`);
            $(this).find('select[name^="entries["][name$="][area]"]').attr('name', `entries[${i}][area]`);
            $(this).find('input[name^="entries["][name$="][hasil_verifikasi_500]"]').attr('name', `entries[${i}][hasil_verifikasi_500]`);
            $(this).find('input[name^="entries["][name$="][hasil_verifikasi_1000]"]').attr('name', `entries[${i}][hasil_verifikasi_1000]`);
            $(this).find('input[name^="entries["][name$="][hasil_verifikasi_10000]"]').attr('name', `entries[${i}][hasil_verifikasi_10000]`);
        });
        rowIndex = $('#timbanganTableBody tr').length - 1;
        updateRemoveButtons();
    }

    $('#addRowBtn').on('click', function() {
        rowIndex++;
        const newRow = `
            <tr data-index="${rowIndex}">
                <td class="text-center">${rowIndex + 1}</td>
                <td>
                    <select class="form-control select-timbangan" data-index="${rowIndex}" required>
                        <option value="">-- Pilih Timbangan --</option>
                        @foreach($dataTimbangan as $timbangan)
                            <option value="{{ $timbangan->id }}" data-nama="{{ $timbangan->nama_timbangan }}" data-kode="{{ $timbangan->kode_timbangan }}">
                                {{ $timbangan->nama_timbangan }} - {{ $timbangan->kode_timbangan }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="entries[${rowIndex}][jenis]" id="jenis_${rowIndex}" required>
                    <input type="hidden" name="entries[${rowIndex}][kode_timbangan]" id="kode_timbangan_${rowIndex}" required>
                    <input type="hidden" name="entries[${rowIndex}][hasil_pengecekan]" value="ok">
                    <input type="hidden" name="entries[${rowIndex}][gram]" value="500">
                </td>
                <td>
                    <select class="form-control select-area" name="entries[${rowIndex}][area]">
                        <option value="">-- Pilih Area --</option>
                        @foreach($inputAreas as $area)
                            <option value="{{ $area->area }}">{{ $area->area }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control" name="entries[${rowIndex}][hasil_verifikasi_500]" placeholder="Isi hasil">
                </td>
                <td>
                    <input type="text" class="form-control" name="entries[${rowIndex}][hasil_verifikasi_1000]" placeholder="Isi hasil">
                </td>
                <td>
                    <input type="text" class="form-control" name="entries[${rowIndex}][hasil_verifikasi_10000]" placeholder="Isi hasil">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-row">Hapus</button>
                </td>
            </tr>
        `;

        $('#timbanganTableBody').append(newRow);

        // Initialize Select2 on the new row
        const $newRow = $(`#timbanganTableBody tr[data-index="${rowIndex}"]`);
        $newRow.find('.select-timbangan').select2({
            placeholder: "Pilih Timbangan",
            allowClear: true,
            width: '100%'
        });
        $newRow.find('.select-area').select2({
            placeholder: "Pilih Area",
            allowClear: true,
            width: '100%'
        });

        updateRemoveButtons();
    });

    $(document).on('click', '.btn-remove-row', function() {
        const $row = $(this).closest('tr');
        $row.find('.select-timbangan').select2('destroy');
        $row.find('.select-area').select2('destroy');
        $row.remove();
        reindexRows();
    });

    $(document).on('change', '.select-timbangan', function() {
        const index = $(this).data('index');
        const opt = $(this).find('option:selected');
        const nama = opt.data('nama') || '';
        const kode = opt.data('kode') || '';

        $(`#jenis_${index}`).val(nama);
        $(`#kode_timbangan_${index}`).val(kode);
    });

    updateRemoveButtons();

    // ==========================================
    // CHECK WEIGHER BAG & BOX LOGIC
    // ==========================================

    function toggleCheckWeigherBagSection() {
        if ($('#enable_checkweigher_bag').is(':checked')) {
            $('#section_checkweigher_bag').removeClass('d-none');
            if ($('#bagContainer .bag-item-card').length === 0) {
                addBagItem();
            }
        } else {
            $('#section_checkweigher_bag').addClass('d-none');
        }
    }

    function toggleCheckWeigherBoxSection() {
        if ($('#enable_checkweigher_box').is(':checked')) {
            $('#section_checkweigher_box').removeClass('d-none');
            if ($('#boxContainer .box-item-card').length === 0) {
                addBoxItem();
            }
        } else {
            $('#section_checkweigher_box').addClass('d-none');
        }
    }

    $('#enable_checkweigher_bag').on('change', toggleCheckWeigherBagSection);
    $('#enable_checkweigher_box').on('change', toggleCheckWeigherBoxSection);

    // Initial check
    toggleCheckWeigherBagSection();
    toggleCheckWeigherBoxSection();

    // Function to Add Bag Item
    function addBagItem() {
        const idx = bagIndex++;
        const itemHtml = `
            <div class="card card-outline card-primary mb-3 bag-item-card" data-index="${idx}">
                <div class="card-header bg-light py-2">
                    <h6 class="font-weight-bold m-0 text-primary"><i class="fas fa-shopping-bag mr-1"></i> Data Check Weigher Bag #${idx + 1}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="small font-weight-bold">Pilih Line</label>
                            <select class="form-control form-control-sm" name="checkweigher_bag_entries[${idx}][line]">
                                <option value="">-- Pilih Line --</option>
                                <option value="Line 1">Line 1</option>
                                <option value="Line 2">Line 2</option>
                                <option value="Line 3">Line 3</option>
                                <option value="Line 4">Line 4</option>
                                <option value="Line 5">Line 5</option>
                                <option value="Line 6">Line 6</option>
                                <option value="Line 7">Line 7</option>
                                <option value="Line 8">Line 8</option>
                                <option value="Line 9">Line 9</option>
                                <option value="Line 10">Line 10</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small font-weight-bold">Nama Produk</label>
                            <select class="form-control form-control-sm select2-produk" name="checkweigher_bag_entries[${idx}][nama_produk]">
                                <option value="">-- Pilih Produk --</option>
                                @foreach($produks as $p)
                                    <option value="{{ $p->nama_produk }}">{{ $p->nama_produk }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small font-weight-bold">Gramase</label>
                            <input type="text" class="form-control form-control-sm" name="checkweigher_bag_entries[${idx}][gramase]" placeholder="Contoh: 500g">
                        </div>
                        <div class="col-md-3">
                            <label class="small font-weight-bold">Waktu Pemeriksaan</label>
                            <input type="time" class="form-control form-control-sm" name="checkweigher_bag_entries[${idx}][waktu_pemeriksaan]" value="{{ date('H:i') }}">
                        </div>
                    </div>

                    <h6 class="font-weight-bold text-secondary border-bottom pb-1 mb-2 small">Hasil Pengecekan (Verifikasi)</h6>
                    
                    <div class="row">
                        <div class="col-md-6 border-right">
                            <label class="font-weight-bold text-dark small mb-1">a. Perbandingan berat</label>
                            <div class="form-group mb-2">
                                <label class="small text-muted mb-1">Checkweigher/Manual (g) - Sample 1</label>
                                <input type="text" class="form-control form-control-sm mb-2" name="checkweigher_bag_entries[${idx}][perbandingan_1]" placeholder="Hasil Sample 1">
                                <label class="small text-muted mb-1">Checkweigher/Manual (g) - Sample 2</label>
                                <input type="text" class="form-control form-control-sm" name="checkweigher_bag_entries[${idx}][perbandingan_2]" placeholder="Hasil Sample 2">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="font-weight-bold text-dark small mb-1">b. Verifikasi Fungsi Checkweigher</label>
                            <div class="form-group mb-2">
                                <label class="small text-muted mb-1">Under</label>
                                <select class="form-control form-control-sm mb-2" name="checkweigher_bag_entries[${idx}][under]">
                                    <option value="">(pilih hasil)</option>
                                    <option value="OK">OK</option>
                                    <option value="TIDAK OK">TIDAK OK</option>
                                </select>
                                <label class="small text-muted mb-1">Over</label>
                                <select class="form-control form-control-sm" name="checkweigher_bag_entries[${idx}][over]">
                                    <option value="">(pilih hasil)</option>
                                    <option value="OK">OK</option>
                                    <option value="TIDAK OK">TIDAK OK</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light text-right py-2">
                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove-bag"><i class="fas fa-trash mr-1"></i> Hapus</button>
                </div>
            </div>
        `;
        $('#bagContainer').append(itemHtml);
        $('#bagContainer').find(`.bag-item-card[data-index="${idx}"] .select2-produk`).select2({
            placeholder: "-- Pilih Produk --",
            allowClear: true,
            width: '100%'
        });
    }

    // Function to Add Box Item
    function addBoxItem() {
        const idx = boxIndex++;
        const itemHtml = `
            <div class="card card-outline card-info mb-3 box-item-card" data-index="${idx}">
                <div class="card-header bg-light py-2">
                    <h6 class="font-weight-bold m-0 text-info"><i class="fas fa-box mr-1"></i> Data Check Weigher Box #${idx + 1}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="small font-weight-bold">Pilih Line</label>
                            <select class="form-control form-control-sm" name="checkweigher_box_entries[${idx}][line]">
                                <option value="">-- Pilih Line --</option>
                                <option value="Line 1">Line 1</option>
                                <option value="Line 2">Line 2</option>
                                <option value="Line 3">Line 3</option>
                                <option value="Line 4">Line 4</option>
                                <option value="Line 5">Line 5</option>
                                <option value="Line 6">Line 6</option>
                                <option value="Line 7">Line 7</option>
                                <option value="Line 8">Line 8</option>
                                <option value="Line 9">Line 9</option>
                                <option value="Line 10">Line 10</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small font-weight-bold">Nama Produk</label>
                            <select class="form-control form-control-sm select2-produk" name="checkweigher_box_entries[${idx}][nama_produk]">
                                <option value="">-- Pilih Produk --</option>
                                @foreach($produks as $p)
                                    <option value="{{ $p->nama_produk }}">{{ $p->nama_produk }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small font-weight-bold">Gramase</label>
                            <input type="text" class="form-control form-control-sm" name="checkweigher_box_entries[${idx}][gramase]" placeholder="Contoh: 10kg">
                        </div>
                        <div class="col-md-3">
                            <label class="small font-weight-bold">Waktu Pemeriksaan</label>
                            <input type="time" class="form-control form-control-sm" name="checkweigher_box_entries[${idx}][waktu_pemeriksaan]" value="{{ date('H:i') }}">
                        </div>
                    </div>

                    <h6 class="font-weight-bold text-secondary border-bottom pb-1 mb-2 small">Hasil Pengecekan (Verifikasi)</h6>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold mb-1">a. Kurang 1 pack</label>
                                <select class="form-control form-control-sm" name="checkweigher_box_entries[${idx}][kurang_1_pack]">
                                    <option value="">(pilih hasil)</option>
                                    <option value="OK">OK</option>
                                    <option value="TIDAK OK">TIDAK OK</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold mb-1">b. Lebih 1 pack</label>
                                <select class="form-control form-control-sm" name="checkweigher_box_entries[${idx}][lebih_1_pack]">
                                    <option value="">(pilih hasil)</option>
                                    <option value="OK">OK</option>
                                    <option value="TIDAK OK">TIDAK OK</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold mb-1">c. Double item</label>
                                <select class="form-control form-control-sm" name="checkweigher_box_entries[${idx}][double_item]">
                                    <option value="">(pilih hasil)</option>
                                    <option value="OK">OK</option>
                                    <option value="TIDAK OK">TIDAK OK</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light text-right py-2">
                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove-box"><i class="fas fa-trash mr-1"></i> Hapus</button>
                </div>
            </div>
        `;
        $('#boxContainer').append(itemHtml);
        $('#boxContainer').find(`.box-item-card[data-index="${idx}"] .select2-produk`).select2({
            placeholder: "-- Pilih Produk --",
            allowClear: true,
            width: '100%'
        });
    }

    $('#addBagRowBtn').on('click', addBagItem);
    $('#addBoxRowBtn').on('click', addBoxItem);

    $(document).on('click', '.btn-remove-bag', function() {
        $(this).closest('.bag-item-card').remove();
    });

    $(document).on('click', '.btn-remove-box', function() {
        $(this).closest('.box-item-card').remove();
    });
});
</script>
@endpush

@endsection