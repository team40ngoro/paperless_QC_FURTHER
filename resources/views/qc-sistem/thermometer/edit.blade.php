@extends('layouts.app')

@section('title', 'Edit Data Thermometer')

@section('container')
<div class="main-panel">
    <div class="content-wrapper">

        <!-- Header -->
        <div class="content-header mb-3">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-thermometer-half text-warning"></i> Edit Data Thermometer
                    </h4>
                    <ol class="breadcrumb mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('thermometer.index') }}">Data Thermometer</a>
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

                    <form action="{{ route('thermometer.update', $data->uuid) }}" method="POST">
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
                                                value="{{ old('tanggal', $data->tanggal ? \Carbon\Carbon::parse($data->tanggal)->format('Y-m-d') : '') }}" required>
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
                                <table class="table table-hover table-bordered align-middle text-sm" id="thermoTable">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>Thermometer</th>
                                            <th>Area</th>
                                            <th>Hasil</th>
                                            <th>0°C</th>
                                            <!-- <th>100°C</th> -->
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody id="thermoTableBody">
                                        @foreach($data->entries ?? [] as $index => $entry)
                                        <tr data-index="{{ $index }}">
                                            <td class="text-center">{{ $index + 1 }}</td>

                                            <td>
                                                <select class="form-control select-thermo" data-index="{{ $index }}">
                                                    <option value="">Pilih</option>
                                                    @foreach($dataThermo as $t)
                                                    <option value="{{ $t->id }}"
                                                        data-jenis="{{ $t->nama_thermo }}"
                                                        data-kode="{{ $t->kode_thermo }}"
                                                        {{ ($entry['jenis'] == $t->nama_thermo && $entry['kode_thermometer'] == $t->kode_thermo) ? 'selected' : '' }}>
                                                        {{ $t->nama_thermo }} - {{ $t->kode_thermo }}
                                                    </option>
                                                    @endforeach
                                                </select>

                                                <input type="hidden" name="entries[{{ $index }}][jenis]" id="jenis_{{ $index }}" value="{{ $entry['jenis'] }}">
                                                <input type="hidden" name="entries[{{ $index }}][kode_thermometer]" id="kode_{{ $index }}" value="{{ $entry['kode_thermometer'] }}">
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
                                                    <option value="ok" {{ $entry['hasil_pengecekan']=='ok'?'selected':'' }}>OK</option>
                                                    <option value="not_ok" {{ $entry['hasil_pengecekan']=='not_ok'?'selected':'' }}>Tidak OK</option>
                                                </select>
                                            </td>



                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="entries[{{ $index }}][hasil_verifikasi_100]"
                                                    value="{{ $entry['hasil_verifikasi_100'] }}">
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

                            <button type="button" class="btn btn-success btn-sm shadow-sm mt-2" id="addRowBtn">
                                <i class="fas fa-plus"></i> Tambah Data
                            </button>

                        </div>

                        <!-- Footer -->
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-warning shadow-sm">
                                <i class="fas fa-save"></i> Update
                            </button>

                            <a href="{{ route('thermometer.index') }}" class="btn btn-outline-secondary shadow-sm">
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

    function initSelect2(){
        $('.select-area').select2({width:'100%'});
        $('.select-thermo').select2({width:'100%'});
    }

    initSelect2();

    // tambah row
    $('#addRowBtn').click(function(){
        let index = $('#thermoTableBody tr').length;

        let row = `
        <tr data-index="${index}">
            <td class="text-center">${index+1}</td>
            <td>
                <select class="form-control select-thermo" data-index="${index}">
                    <option value="">Pilih</option>
                    @foreach($dataThermo as $t)
                    <option value="{{ $t->id }}" data-jenis="{{ $t->nama_thermo }}" data-kode="{{ $t->kode_thermo }}">
                        {{ $t->nama_thermo }} - {{ $t->kode_thermo }}
                    </option>
                    @endforeach
                </select>
                <input type="hidden" name="entries[${index}][jenis]" id="jenis_${index}">
                <input type="hidden" name="entries[${index}][kode_thermometer]" id="kode_${index}">
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

            <td><input type="text" class="form-control form-control-sm" name="entries[${index}][hasil_verifikasi_100]"></td>

            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;

        $('#thermoTableBody').append(row);
        initSelect2();
    });

    // hapus row
    $(document).on('click','.btn-remove-row',function(){
        $(this).closest('tr').remove();
    });

    // set hidden input
    $(document).on('change','.select-thermo',function(){
        let index = $(this).data('index');
        let opt = $(this).find(':selected');

        $('#jenis_'+index).val(opt.data('jenis'));
        $('#kode_'+index).val(opt.data('kode'));
    });

});
</script>
@endpush

@endsection