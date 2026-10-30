@extends('layouts.app')

@section('title', 'Edit Data Timbangan')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">

        <!-- Header -->
        <div class="content-header mb-3">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-balance-scale text-warning"></i> Edit Data Timbangan
                    </h4>
                    <ol class="breadcrumb mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('timbangan.index') }}">Data Timbangan</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <section class="content">
            <div class="container-fluid">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-edit"></i> Form Edit Data
                        </h5>
                    </div>

                    <form action="{{ route('timbangan.update', $data->uuid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-body">

                            <!-- Informasi Utama -->
                            <div class="card card-outline card-info mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-info-circle"></i> Informasi Utama
                                    </h6>
                                </div>

                                <div class="card-body">
                                    <div class="row g-3">

                                        <div class="col-md-4">
                                            <label class="font-weight-bold">Shift</label>
                                            <select name="shift_id" class="form-control select2" required>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ old('shift_id', $data->shift_id) == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="font-weight-bold">Tanggal</label>
                                            <input type="date" name="tanggal" class="form-control"
                                                value="{{ old('tanggal', $data->tanggal ? $data->tanggal->format('Y-m-d') : '') }}" required>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="font-weight-bold">Jam</label>
                                            <input type="time" name="jam" class="form-control"
                                                value="{{ old('jam', $data->jam ? \Carbon\Carbon::parse($data->jam)->format('H:i') : '') }}" required>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm" id="timbanganTable">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>Timbangan</th>
                                            <th>Area</th>
                                            <th>Hasil</th>
                                            <th>500g</th>
                                            <th>1000g</th>
                                            <th>10000g</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody id="timbanganTableBody">
                                        @php
                                            $bagEntries = $data->entries['checkweigher_bag'] ?? [];
                                            $boxEntries = $data->entries['checkweigher_box'] ?? [];
                                            $hasBag = !empty($bagEntries);
                                            $hasBox = !empty($boxEntries);
                                        @endphp
                                        @foreach($data->entries ?? [] as $index => $entry)
                                        @if(!is_numeric($index)) @continue @endif
                                        <tr data-index="{{ $index }}">
                                            <td class="text-center">{{ $index + 1 }}</td>

                                            <td>
                                                <select class="form-control select-timbangan" data-index="{{ $index }}">
                                                    <option value="">Pilih</option>
                                                    @foreach($dataTimbangan as $t)
                                                    <option value="{{ $t->id }}"
                                                        data-nama="{{ $t->nama_timbangan }}"
                                                        data-kode="{{ $t->kode_timbangan }}"
                                                        {{ (($entry['jenis'] ?? '') == $t->nama_timbangan && ($entry['kode_timbangan'] ?? '') == $t->kode_timbangan) ? 'selected' : '' }}>
                                                        {{ $t->nama_timbangan }} - {{ $t->kode_timbangan }}
                                                    </option>
                                                    @endforeach
                                                </select>

                                                <input type="hidden" name="entries[{{ $index }}][jenis]" id="jenis_{{ $index }}" value="{{ $entry['jenis'] ?? '' }}">
                                                <input type="hidden" name="entries[{{ $index }}][kode_timbangan]" id="kode_{{ $index }}" value="{{ $entry['kode_timbangan'] ?? '' }}">
                                                <input type="hidden" name="entries[{{ $index }}][gram]" value="{{ $entry['gram'] ?? '500' }}">
                                            </td>

                                            <td>
                                                <select name="entries[{{ $index }}][area]" class="form-control select-area">
                                                    <option value="">Pilih</option>
                                                    @foreach($inputAreas as $area)
                                                    <option value="{{ $area->area }}" {{ ($entry['area'] ?? '') == $area->area ? 'selected' : '' }}>
                                                        {{ $area->area }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td>
                                                <select name="entries[{{ $index }}][hasil_pengecekan]" class="form-control">
                                                    <option value="ok" {{ ($entry['hasil_pengecekan'] ?? '')=='ok'?'selected':'' }}>OK</option>
                                                    <option value="not_ok" {{ ($entry['hasil_pengecekan'] ?? '')=='not_ok'?'selected':'' }}>Tidak OK</option>
                                                </select>
                                            </td>

                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="entries[{{ $index }}][hasil_verifikasi_500]"
                                                    value="{{ $entry['hasil_verifikasi_500'] ?? '' }}">
                                            </td>

                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="entries[{{ $index }}][hasil_verifikasi_1000]"
                                                    value="{{ $entry['hasil_verifikasi_1000'] ?? '' }}">
                                            </td>

                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="entries[{{ $index }}][hasil_verifikasi_10000]"
                                                    value="{{ $entry['hasil_verifikasi_10000'] ?? '' }}">
                                            </td>

                                            <td class="text-center">
                                                <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <button type="button" class="btn btn-success btn-sm shadow-sm mt-2 mb-4" id="addRowBtn">
                                <i class="fas fa-plus"></i> Tambah Data Timbangan
                            </button>

                            <!-- SECTION: CENTANG UNTUK MEMBUKA FORM VERIFIKASI TAMBAHAN -->
                            <div class="card card-outline card-info my-4">
                                <div class="card-header">
                                    <h6 class="mb-0 font-weight-bold text-info"><i class="fas fa-tasks mr-1"></i> Form Verifikasi Check Weigher (Opsional)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="enable_checkweigher_bag" name="enable_checkweigher_bag" value="1" {{ old('enable_checkweigher_bag', $hasBag) ? 'checked' : '' }}>
                                                <label class="custom-control-label font-weight-bold text-primary" for="enable_checkweigher_bag">
                                                    <i class="fas fa-shopping-bag mr-1"></i> Verifikasi Penggunaan Check Weigher Bag
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="enable_checkweigher_box" name="enable_checkweigher_box" value="1" {{ old('enable_checkweigher_box', $hasBox) ? 'checked' : '' }}>
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
                                    <h6 class="font-weight-bold text-primary m-0"><i class="fas fa-shopping-bag mr-1"></i> Verifikasi Penggunaan Check Weigher Bag</h6>
                                </div>
                                <div class="card-body" id="bagContainer">
                                    @foreach($bagEntries as $bIdx => $bItem)
                                    <div class="card card-outline card-primary mb-3 bag-item-card" data-index="{{ $bIdx }}">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="font-weight-bold m-0 text-primary"><i class="fas fa-shopping-bag mr-1"></i> Data Check Weigher Bag #{{ $bIdx + 1 }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <label class="small font-weight-bold">Pilih Line</label>
                                                    <select class="form-control form-control-sm" name="checkweigher_bag_entries[{{ $bIdx }}][line]">
                                                        <option value="">-- Pilih Line --</option>
                                                        @for($i=1; $i<=10; $i++)
                                                            <option value="Line {{ $i }}" {{ ($bItem['line'] ?? '') == "Line $i" ? 'selected' : '' }}>Line {{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small font-weight-bold">Nama Produk</label>
                                                    <select class="form-control form-control-sm select2-produk" name="checkweigher_bag_entries[{{ $bIdx }}][nama_produk]">
                                                        <option value="">-- Pilih Produk --</option>
                                                        @foreach($produks as $p)
                                                            <option value="{{ $p->nama_produk }}" {{ ($bItem['nama_produk'] ?? '') == $p->nama_produk ? 'selected' : '' }}>{{ $p->nama_produk }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small font-weight-bold">Gramase</label>
                                                    <input type="text" class="form-control form-control-sm" name="checkweigher_bag_entries[{{ $bIdx }}][gramase]" value="{{ $bItem['gramase'] ?? '' }}" placeholder="Contoh: 500g">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small font-weight-bold">Waktu Pemeriksaan</label>
                                                    <input type="time" class="form-control form-control-sm" name="checkweigher_bag_entries[{{ $bIdx }}][waktu_pemeriksaan]" value="{{ $bItem['waktu_pemeriksaan'] ?? date('H:i') }}">
                                                </div>
                                            </div>

                                            <h6 class="font-weight-bold text-secondary border-bottom pb-1 mb-2 small">Hasil Pengecekan (Verifikasi)</h6>
                                            
                                            <div class="row">
                                                <div class="col-md-6 border-right">
                                                    <label class="font-weight-bold text-dark small mb-1">a. Perbandingan berat</label>
                                                    <div class="form-group mb-2">
                                                        <label class="small text-muted mb-1">Checkweigher/Manual (g) - Sample 1</label>
                                                        <input type="text" class="form-control form-control-sm mb-2" name="checkweigher_bag_entries[{{ $bIdx }}][perbandingan_1]" value="{{ $bItem['perbandingan_1'] ?? '' }}" placeholder="Hasil Sample 1">
                                                        <label class="small text-muted mb-1">Checkweigher/Manual (g) - Sample 2</label>
                                                        <input type="text" class="form-control form-control-sm" name="checkweigher_bag_entries[{{ $bIdx }}][perbandingan_2]" value="{{ $bItem['perbandingan_2'] ?? '' }}" placeholder="Hasil Sample 2">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="font-weight-bold text-dark small mb-1">b. Verifikasi Fungsi Checkweigher</label>
                                                    <div class="form-group mb-2">
                                                        <label class="small text-muted mb-1">Under</label>
                                                        <select class="form-control form-control-sm mb-2" name="checkweigher_bag_entries[{{ $bIdx }}][under]">
                                                            <option value="">(pilih hasil)</option>
                                                            <option value="OK" {{ ($bItem['under'] ?? '') == 'OK' ? 'selected' : '' }}>OK</option>
                                                            <option value="TIDAK OK" {{ ($bItem['under'] ?? '') == 'TIDAK OK' ? 'selected' : '' }}>TIDAK OK</option>
                                                        </select>
                                                        <label class="small text-muted mb-1">Over</label>
                                                        <select class="form-control form-control-sm" name="checkweigher_bag_entries[{{ $bIdx }}][over]">
                                                            <option value="">(pilih hasil)</option>
                                                            <option value="OK" {{ ($bItem['over'] ?? '') == 'OK' ? 'selected' : '' }}>OK</option>
                                                            <option value="TIDAK OK" {{ ($bItem['over'] ?? '') == 'TIDAK OK' ? 'selected' : '' }}>TIDAK OK</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light text-right py-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-remove-bag"><i class="fas fa-trash mr-1"></i> Hapus</button>
                                        </div>
                                    </div>
                                    @endforeach
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
                                    <h6 class="font-weight-bold text-info m-0"><i class="fas fa-box mr-1"></i> Verifikasi Penggunaan Check Weigher Box</h6>
                                </div>
                                <div class="card-body" id="boxContainer">
                                    @foreach($boxEntries as $bxIdx => $bxItem)
                                    <div class="card card-outline card-info mb-3 box-item-card" data-index="{{ $bxIdx }}">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="font-weight-bold m-0 text-info"><i class="fas fa-box mr-1"></i> Data Check Weigher Box #{{ $bxIdx + 1 }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <label class="small font-weight-bold">Pilih Line</label>
                                                    <select class="form-control form-control-sm" name="checkweigher_box_entries[{{ $bxIdx }}][line]">
                                                        <option value="">-- Pilih Line --</option>
                                                        @for($i=1; $i<=10; $i++)
                                                            <option value="Line {{ $i }}" {{ ($bxItem['line'] ?? '') == "Line $i" ? 'selected' : '' }}>Line {{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small font-weight-bold">Nama Produk</label>
                                                    <select class="form-control form-control-sm select2-produk" name="checkweigher_box_entries[{{ $bxIdx }}][nama_produk]">
                                                        <option value="">-- Pilih Produk --</option>
                                                        @foreach($produks as $p)
                                                            <option value="{{ $p->nama_produk }}" {{ ($bxItem['nama_produk'] ?? '') == $p->nama_produk ? 'selected' : '' }}>{{ $p->nama_produk }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small font-weight-bold">Gramase</label>
                                                    <input type="text" class="form-control form-control-sm" name="checkweigher_box_entries[{{ $bxIdx }}][gramase]" value="{{ $bxItem['gramase'] ?? '' }}" placeholder="Contoh: 10kg">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small font-weight-bold">Waktu Pemeriksaan</label>
                                                    <input type="time" class="form-control form-control-sm" name="checkweigher_box_entries[{{ $bxIdx }}][waktu_pemeriksaan]" value="{{ $bxItem['waktu_pemeriksaan'] ?? date('H:i') }}">
                                                </div>
                                            </div>

                                            <h6 class="font-weight-bold text-secondary border-bottom pb-1 mb-2 small">Hasil Pengecekan (Verifikasi)</h6>
                                            
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group mb-2">
                                                        <label class="small font-weight-bold mb-1">a. Kurang 1 pack</label>
                                                        <select class="form-control form-control-sm" name="checkweigher_box_entries[{{ $bxIdx }}][kurang_1_pack]">
                                                            <option value="">(pilih hasil)</option>
                                                            <option value="OK" {{ ($bxItem['kurang_1_pack'] ?? '') == 'OK' ? 'selected' : '' }}>OK</option>
                                                            <option value="TIDAK OK" {{ ($bxItem['kurang_1_pack'] ?? '') == 'TIDAK OK' ? 'selected' : '' }}>TIDAK OK</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-2">
                                                        <label class="small font-weight-bold mb-1">b. Lebih 1 pack</label>
                                                        <select class="form-control form-control-sm" name="checkweigher_box_entries[{{ $bxIdx }}][lebih_1_pack]">
                                                            <option value="">(pilih hasil)</option>
                                                            <option value="OK" {{ ($bxItem['lebih_1_pack'] ?? '') == 'OK' ? 'selected' : '' }}>OK</option>
                                                            <option value="TIDAK OK" {{ ($bxItem['lebih_1_pack'] ?? '') == 'TIDAK OK' ? 'selected' : '' }}>TIDAK OK</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-2">
                                                        <label class="small font-weight-bold mb-1">c. Double item</label>
                                                        <select class="form-control form-control-sm" name="checkweigher_box_entries[{{ $bxIdx }}][double_item]">
                                                            <option value="">(pilih hasil)</option>
                                                            <option value="OK" {{ ($bxItem['double_item'] ?? '') == 'OK' ? 'selected' : '' }}>OK</option>
                                                            <option value="TIDAK OK" {{ ($bxItem['double_item'] ?? '') == 'TIDAK OK' ? 'selected' : '' }}>TIDAK OK</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light text-right py-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-remove-box"><i class="fas fa-trash mr-1"></i> Hapus</button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="card-footer bg-light text-right">
                                    <button type="button" class="btn btn-sm btn-info text-white" id="addBoxRowBtn">
                                        <i class="fas fa-plus"></i> Tambah Data Box
                                    </button>
                                </div>
                            </div>

                        </div>

                        <!-- Footer -->
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-warning shadow-sm">
                                <i class="fas fa-save"></i> Update
                            </button>

                            <a href="{{ route('timbangan.index') }}" class="btn btn-outline-secondary shadow-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function(){
    let bagIndex = {{ count($bagEntries) }};
    let boxIndex = {{ count($boxEntries) }};

    function initSelect2(){
        $('.select-timbangan').select2({width:'100%'});
        $('.select-area').select2({width:'100%'});
        $('.select2-produk').select2({width:'100%', placeholder: '-- Pilih Produk --', allowClear: true});
    }

    initSelect2();

    // tambah row
    $('#addRowBtn').click(function(){
        let index = $('#timbanganTableBody tr').length;

        let row = `
        <tr data-index="${index}">
            <td class="text-center">${index+1}</td>

            <td>
                <select class="form-control select-timbangan" data-index="${index}">
                    <option value="">Pilih</option>
                    @foreach($dataTimbangan as $t)
                    <option value="{{ $t->id }}" data-nama="{{ $t->nama_timbangan }}" data-kode="{{ $t->kode_timbangan }}">
                        {{ $t->nama_timbangan }} - {{ $t->kode_timbangan }}
                    </option>
                    @endforeach
                </select>

                <input type="hidden" name="entries[${index}][jenis]" id="jenis_${index}">
                <input type="hidden" name="entries[${index}][kode_timbangan]" id="kode_${index}">
                <input type="hidden" name="entries[${index}][gram]" value="500">
            </td>

            <td>
                <select name="entries[${index}][area]" class="form-control select-area">
                    <option value="">Pilih</option>
                    @foreach($inputAreas as $area)
                    <option value="{{ $area->area }}">{{ $area->area }}</option>
                    @endforeach
                </select>
            </td>

            <td>
                <select name="entries[${index}][hasil_pengecekan]" class="form-control">
                    <option value="ok">OK</option>
                    <option value="not_ok">Tidak OK</option>
                </select>
            </td>

            <td><input type="text" class="form-control form-control-sm" name="entries[${index}][hasil_verifikasi_500]"></td>
            <td><input type="text" class="form-control form-control-sm" name="entries[${index}][hasil_verifikasi_1000]"></td>
            <td><input type="text" class="form-control form-control-sm" name="entries[${index}][hasil_verifikasi_10000]"></td>

            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;

        $('#timbanganTableBody').append(row);
        initSelect2();
    });

    // hapus row + reindex
    $(document).on('click','.btn-remove-row',function(){
        $(this).closest('tr').remove();

        $('#timbanganTableBody tr').each(function(i){
            $(this).attr('data-index', i);
            $(this).find('td:eq(0)').text(i+1);
        });
    });

    // set hidden value
    $(document).on('change','.select-timbangan',function(){
        let index = $(this).data('index');
        let opt = $(this).find(':selected');

        $('#jenis_'+index).val(opt.data('nama'));
        $('#kode_'+index).val(opt.data('kode'));
    });

    // ==========================================
    // CHECK WEIGHER BAG & BOX LOGIC FOR EDIT
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
        initSelect2();
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
        initSelect2();
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