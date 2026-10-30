{{-- filepath: resources/views/qc-sistem/persiapan_bahan_emulsi/edit.blade.php --}}
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-edit text-primary"></i>
                        Edit Verifikasi Pembuatan Emulsi
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('persiapan-bahan-emulsi.index') }}">Verifikasi Pembuatan Emulsi</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('persiapan-bahan-emulsi.update', $item->uuid) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Card for Basic Information -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            Informasi Dasar
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="id_produk_emulsi">
                                        <i class="fas fa-box text-info"></i>
                                        Nama Produk <span class="text-danger">*</span>
                                    </label>
                                    <select name="id_produk" id="id_produk_emulsi" class="form-control @error('id_produk') is-invalid @enderror" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ old('id_produk', $item->id_produk) == $produk->id ? 'selected' : '' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_produk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nama_emulsi_id">
                                        <i class="fas fa-flask text-success"></i>
                                        Nama Emulsi <span class="text-danger">*</span>
                                    </label>
                                    <select name="nama_emulsi_id" id="nama_emulsi_id_emulsi" class="form-control @error('nama_emulsi_id') is-invalid @enderror" required>
                                        <option value="">Pilih Nama Emulsi</option>
                                        @foreach($nama_emulsis as $emulsi)
                                            <option value="{{ $emulsi->id }}" {{ old('nama_emulsi_id', $item->nama_emulsi_id) == $emulsi->id ? 'selected' : '' }}>
                                                {{ $emulsi->nama_emulsi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="total_pemakaian_id">
                                        <i class="fas fa-balance-scale text-info"></i>
                                        Total Pemakaian <span class="text-danger">*</span>
                                    </label>
                                    <select name="total_pemakaian_id" id="total_pemakaian_id_emulsi" class="form-control @error('total_pemakaian_id') is-invalid @enderror" required>
                                        <option value="">Pilih Total Pemakaian</option>
                                        @foreach($total_pemakaians as $tp)
                                            <option value="{{ $tp->id }}" {{ old('total_pemakaian_id', $item->total_pemakaian_id) == $tp->id ? 'selected' : '' }}>
                                                {{ $tp->total_pemakaian }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nomor_emulsi_id">
                                        <i class="fas fa-list-ol text-warning"></i>
                                        Jumlah Proses Emulsi <span class="text-danger">*</span>
                                    </label>
                                    <select name="nomor_emulsi_id" id="proses_emulsi_id_emulsi" class="form-control @error('nomor_emulsi_id') is-invalid @enderror" required>
                                        <option value="">Pilih Jumlah Proses Emulsi</option>
                                        @foreach($nomor_emulsis as $no)
                                            <option value="{{ $no->id }}" {{ old('nomor_emulsi_id', $item->nomor_emulsi_id) == $no->id ? 'selected' : '' }}>
                                                {{ $no->nomor_emulsi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kode_produksi_emulsi">
                                        <i class="fas fa-barcode text-warning"></i>
                                        Kode Produksi Emulsi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="kode_produksi_emulsi" id="kode_produksi_emulsi" 
                                           class="form-control @error('kode_produksi_emulsi') is-invalid @enderror" 
                                           value="{{ old('kode_produksi_emulsi', $item->kode_produksi_emulsi) }}" 
                                           placeholder="Masukkan kode produksi emulsi" required>
                                    @error('kode_produksi_emulsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="berat_sampling">
                                        <i class="fas fa-weight-hanging text-success"></i>
                                        Gramase (gram)
                                    </label>
                                    <select name="berat_sampling" id="nilai_select_berat_sampling_edit" class="form-control" data-nilai-berat data-selected="{{ old('berat_sampling', $item->berat_sampling) }}"></select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="shift_id">
                                        <i class="fas fa-clock text-primary"></i>
                                        Shift <span class="text-danger">*</span>
                                    </label>
                                    <select name="shift_id" id="shift_id" class="form-control @error('shift_id') is-invalid @enderror" required>
                                        <option value="">Pilih Shift</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ $item->shift_id == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->shift }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shift_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                           {{-- dev_bagus_perbaikan_29_mei:remove disable form --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tanggal">
                                        <i class="fas fa-calendar text-danger"></i>
                                        Tanggal <span class="text-danger">*</span>
                                    </label>

                                    <input type="date"
                                        id="tanggal"
                                        name="tanggal"
                                        class="form-control @error('tanggal') is-invalid @enderror"
                                        value="{{ old('tanggal', $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : '') }}"
                                        @if(isset($readonly) && $readonly) readonly @endif required>

                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jam">
                                        <i class="fas fa-clock text-info"></i>
                                        Jam <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" name="jam" id="jam" 
                                           class="form-control @error('jam') is-invalid @enderror" 
                                           value="{{ old('jam', $item->jam ? \Carbon\Carbon::parse($item->jam)->format('H:i') : '') }}" required>
                                    @error('jam')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card for Bahan Emulsi & Suhu -->
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-thermometer-half"></i>
                            Bahan Emulsi & Suhu
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" id="multiple-tables-container">
                        @if($item->suhuEmulsi && $item->suhuEmulsi->count() > 0)
                            @php
                                // Group data by proses_ke
                                $groupedData = $item->suhuEmulsi->groupBy('proses_ke');
                                $kondisiArray = json_decode($item->kondisi, true) ?? [];
                                $hasilArray = json_decode($item->hasil_emulsi, true) ?? [];
                            @endphp
                            
                            @foreach($groupedData as $prosesKe => $items)
                                <div class="mt-4">
                                    <h5 class="text-primary">Proses Emulsi ke-{{ $prosesKe }}</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover">
                                            <thead class="bg-gradient-success">
                                                <tr class="text-white">
                                                    <th class="text-center" width="5%">No</th>
                                                    <th class="text-center" width="30%">
                                                        <i class="fas fa-flask"></i> Nama RM
                                                    </th>
                                                    <th class="text-center" width="15%">
                                                        <i class="fas fa-weight"></i> Berat (gram)
                                                    </th>
                                                    <th class="text-center" width="25%">
                                                        <i class="fas fa-barcode"></i> Kode Produksi Bahan
                                                    </th>
                                                    <th class="text-center" width="25%">
                                                        <i class="fas fa-thermometer-half"></i> Kondisi
                                                    </th>
                                                </tr>
                                            </thead>
                                            
                                            <tbody>
                                            @foreach($items as $index => $suhu)
                                            <tr>
                                                <td class="text-center align-middle">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td class="align-middle">
                                                    <strong>{{ $suhu->bahanEmulsi->nama_rm ?? '-' }}</strong>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="input-group input-group-sm">
                                                        <select class="form-control berat-source-select-edit" data-index="{{ $index }}" data-proses="{{ $prosesKe }}" data-db-value="{{ $suhu->bahanEmulsi->berat_rm ?? '' }}">
                                                            <option value="db">Dari DB ({{ $suhu->bahanEmulsi->berat_rm ?? '-' }})</option>
                                                            <option value="manual" {{ $suhu->berat_bahan ? 'selected' : '' }}>Manual Input</option>
                                                        </select>
                                                        <input type="number" name="berat_rm_edit[]" class="form-control berat-input-edit" 
                                                            value="{{ $suhu->berat_bahan ?? $suhu->bahanEmulsi->berat_rm ?? '' }}" 
                                                            step="0.1" placeholder="Berat" 
                                                            {{ !$suhu->berat_bahan ? 'disabled' : '' }}>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" name="kode_produksi_bahan[]" class="form-control" 
                                                        value="{{ $suhu->kode_produksi_bahan ?? '' }}" 
                                                        placeholder="Masukkan Kode Produksi">
                                                </td>
                                                <td>
                                                    <input type="hidden" name="suhu_emulsi_id[]" value="{{ $suhu->id }}">
                                                    <select name="suhu[]" class="form-control" required>
                                                        <option value="">Pilih Status</option>
                                                        <option value="✔" {{ $suhu->suhu == '✔' ? 'selected' : '' }}>✔ OK</option>
                                                        <option value="✘" {{ $suhu->suhu == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            @endforeach
                                            
                                            <!-- TAMBAHAN: Row untuk Suhu per Proses -->
                                            <tr class="bg-light">
                                                <td colspan="5">
                                                    <div class="row align-items-center">
                                                        <label class="col-sm-2 font-weight-bold mb-0"><i class="fas fa-thermometer-half mr-1"></i>Suhu</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="kondisi_proses[{{ $prosesKe - 1 }}]" class="form-control" 
                                                                   value="{{ $kondisiArray[$prosesKe - 1] ?? '' }}" 
                                                                   placeholder="Masukkan Suhu" required>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- TAMBAHAN: Row untuk Hasil Emulsi per Proses -->
                                            <tr class="bg-light">
                                                <td colspan="5">
                                                    <div class="row align-items-center">
                                                        <label class="col-sm-2 font-weight-bold mb-0"><i class="fas fa-check-circle mr-1"></i>Hasil Emulsi</label>
                                                        <div class="col-sm-10">
                                                            <select name="hasil_emulsi_proses[{{ $prosesKe - 1 }}]" class="form-control" required>
                                                                <option value="">Pilih Hasil</option>
                                                                <option value="✔" {{ ($hasilArray[$prosesKe - 1] ?? '') == '✔' ? 'selected' : '' }}>✔ OK</option>
                                                                <option value="✘" {{ ($hasilArray[$prosesKe - 1] ?? '') == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                Tidak ada data detail bahan emulsi yang tersedia.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-warning btn-md mr-3">
                                    <i class="fas fa-save"></i>
                                    Update Data
                                </button>
                                <a href="{{ route('persiapan-bahan-emulsi.index') }}" class="btn btn-secondary btn-md">
                                    <i class="fas fa-arrow-left"></i>
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
            </form>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Flag to prevent AJAX reload on initial load
    let isInitialized = {
        emulsi: true,
        pemakaian: true,
        proses: true
    };

    // Event handler untuk toggle berat source di halaman edit
    $(document).on('change', '.berat-source-select-edit', function() {
        const index = $(this).data('index');
        const proses = $(this).data('proses');
        const dbValue = $(this).data('db-value');
        const sourceValue = $(this).val();
        const inputField = $(this).closest('td').find('.berat-input-edit');
        
        if (sourceValue === 'db') {
            inputField.val(dbValue).prop('disabled', true);
        } else {
            inputField.val('').prop('disabled', false).focus();
        }
    });

    // 1. Nama Emulsi -> Total Pemakaian
    $('#nama_emulsi_id_emulsi').on('change', function() {
        if (isInitialized.emulsi) {
            isInitialized.emulsi = false;
            return;
        }

        var produkId = $('#id_produk_emulsi').val();
        var emulsiId = $(this).val();
        
        if (!confirm('Mengubah Nama Emulsi akan mereset data tabel di bawah. Lanjutkan?')) {
            location.reload(); // Quick way to reset to DB state
            return;
        }

        $('#total_pemakaian_id_emulsi, #proses_emulsi_id_emulsi').empty().append('<option value="">Pilih Opsi</option>');
        $('#multiple-tables-container').empty();

        if (produkId && emulsiId) {
            var url = "{{ route('get-total-pemakaian-by-emulsi-produk', ['id_produk' => ':id_produk', 'nama_emulsi_id' => ':nama_emulsi_id']) }}";
            url = url.replace(':id_produk', produkId).replace(':nama_emulsi_id', emulsiId);
            $.getJSON(url, function(data) {
                $.each(data, function(key, value) {
                    $('#total_pemakaian_id_emulsi').append('<option value="' + value.id + '">' + value.total_pemakaian + '</option>');
                });
            });
        }
    });

    // 2. Total Pemakaian -> Proses Emulsi
    $('#total_pemakaian_id_emulsi').on('change', function() {
        if (isInitialized.pemakaian) {
            isInitialized.pemakaian = false;
            return;
        }

        var totalPemakaianId = $(this).val();
        
        if (!confirm('Mengubah Total Pemakaian akan mereset data tabel di bawah. Lanjutkan?')) {
            location.reload();
            return;
        }

        $('#proses_emulsi_id_emulsi').empty().append('<option value="">Pilih Opsi</option>');
        $('#multiple-tables-container').empty();

        if (totalPemakaianId) {
            var url = "{{ route('get-nomor-emulsi-by-total-pemakaian', ['total_pemakaian_id' => ':id']) }}".replace(':id', totalPemakaianId);
            $.getJSON(url, function(data) {
                $.each(data, function(key, value) {
                    $('#proses_emulsi_id_emulsi').append('<option value="' + value.id + '" data-jumlah="' + value.nomor_emulsi + '">' + value.nomor_emulsi + '</option>');
                });
            });
        }
    });

    // 3. Proses Emulsi -> Re-generate Table
    $('#proses_emulsi_id_emulsi').on('change', function() {
        if (isInitialized.proses) {
            isInitialized.proses = false;
            return;
        }

        var nomorEmulsiId = $(this).val();
        var selectedOption = $(this).find('option:selected');
        var jumlahProses = parseInt(selectedOption.data('jumlah')) || parseInt(selectedOption.text()) || 1;
        var container = $('#multiple-tables-container');

        if (!confirm('Mengubah Jumlah Proses akan mereset data tabel di bawah. Lanjutkan?')) {
            location.reload();
            return;
        }

        container.empty().append('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Memuat data bahan...</p></div>');

        if (nomorEmulsiId && jumlahProses) {
            var url = "{{ route('get-bahan-emulsi-by-nomor-emulsi', ['nomor_emulsi_id' => ':id']) }}".replace(':id', nomorEmulsiId);
            
            $.getJSON(url, function(data) {
                container.empty();
                if (data.length > 0) {
                    var html = '<div class="multiple-tables-container">';
                    for (let pk = 1; pk <= jumlahProses; pk++) {
                        html += `
                            <div class="mt-4">
                                <h5 class="text-primary font-weight-bold">Proses Emulsi ke-${pk} (Data Baru)</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead class="bg-gradient-success">
                                            <tr class="text-white">
                                                <th class="text-center" width="5%">No</th>
                                                <th class="text-center" width="30%"><i class="fas fa-flask"></i> Nama RM</th>
                                                <th class="text-center" width="15%"><i class="fas fa-weight"></i> Berat (gram)</th>
                                                <th class="text-center" width="25%"><i class="fas fa-barcode"></i> Kode Produksi Bahan</th>
                                                <th class="text-center" width="25%"><i class="fas fa-thermometer-half"></i> Kondisi</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                        
                        $.each(data, function(idx, b) {
                            const uniqueId = `berat_edit_${pk}_${idx}`;
                            html += `
                                <tr>
                                    <td class="text-center align-middle">${idx + 1}</td>
                                    <td class="align-middle">
                                        <strong>${b.nama_rm}</strong>
                                        <input type="hidden" name="bahan_emulsi_id[${pk-1}][]" value="${b.id}">
                                        <input type="hidden" name="proses_ke[${pk-1}][]" value="${pk}">
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <select class="form-control berat-source-select-dynamic" data-target="#${uniqueId}" data-db-value="${b.berat_rm}">
                                                <option value="db">Master (${b.berat_rm})</option>
                                                <option value="manual">Manual</option>
                                            </select>
                                            <input type="number" id="${uniqueId}" name="berat_rm[${pk-1}][]" class="form-control" value="${b.berat_rm}" step="0.1" disabled>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="kode_produksi_bahan[${pk-1}][]" class="form-control" placeholder="Kode Produksi">
                                    </td>
                                    <td>
                                        <select name="suhu[${pk-1}][]" class="form-control" required>
                                            <option value="✔">✔ OK</option>
                                            <option value="✘">✘ Tidak OK</option>
                                        </select>
                                    </td>
                                </tr>`;
                        });

                        html += `
                                <tr class="bg-light">
                                    <td colspan="5">
                                        <div class="row align-items-center">
                                            <label class="col-sm-2 font-weight-bold mb-0">Suhu</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="kondisi_proses[${pk-1}]" class="form-control" placeholder="Masukkan Suhu" required>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="bg-light">
                                    <td colspan="5">
                                        <div class="row align-items-center">
                                            <label class="col-sm-2 font-weight-bold mb-0">Hasil Emulsi</label>
                                            <div class="col-sm-10">
                                                <select name="hasil_emulsi_proses[${pk-1}]" class="form-control" required>
                                                    <option value="">Pilih Hasil</option>
                                                    <option value="✔">✔ OK</option>
                                                    <option value="✘">✘ Tidak OK</option>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>`;
                    }
                    html += '</div>';
                    container.html(html);
                }
            });
        }
    });

    $(document).on('change', '.berat-source-select-dynamic', function() {
        const target = $(this).data('target');
        const dbValue = $(this).data('db-value');
        if ($(this).val() === 'db') {
            $(target).val(dbValue).prop('disabled', true);
        } else {
            $(target).val('').prop('disabled', false).focus();
        }
    });
});
</script>
@endsection