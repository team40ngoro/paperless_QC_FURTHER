@extends('layouts.app')

@section('container')
@php
    $canFillTemuan = auth()->user()->isQc() || auth()->user()->role === 'superadmin';
@endphp
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Data Barang Mudah Pecah</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('barang-mudah-pecah.index') }}">Barang Mudah Pecah</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus text-primary mr-2"></i>
                                Form Tambah Data Barang Mudah Pecah
                            </h3>
                        </div>
                        <form action="{{ route('barang-mudah-pecah.store') }}" method="POST">
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

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="tanggal" class="font-weight-bold">Tanggal <span class="text-danger">*</span></label>
                                            <input type="date" name="tanggal" id="tanggal"
                                                class="form-control @error('tanggal') is-invalid @enderror"
                                                value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                                            @error('tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="jam" class="font-weight-bold">Jam <span class="text-danger">*</span></label>
                                            <input type="time" 
                                                   class="form-control @error('jam') is-invalid @enderror" 
                                                   id="jam" 
                                                   name="jam" 
                                                   value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" 
                                                   required>
                                            @error('jam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="shift_id" class="font-weight-bold">Shift <span class="text-danger">*</span></label>
                                            <select class="form-control @error('shift_id') is-invalid @enderror" 
                                                    id="shift_id" name="shift_id" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" 
                                                            data-plan-id="{{ $shift->id_plan }}"
                                                            {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
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
                                <!--  -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="barang-mudah-pecah-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 60px;">No</th>
                                                        <th>Nama Barang</th>
                                                        <th style="width: 120px;">Jumlah</th>
                                                        <th style="width: 170px;">Kondisi</th>
                                                        <th>Temuan Ketidaksesuaian</th>
                                                        <th>Tindakan Koreksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="barang-mudah-pecah-table-body">
                                                    @php $no = 1; $rowIndex = 0; @endphp
                                                    @if(isset($areas) && $areas->count())
                                                        @foreach($areas as $area)
                                                            @php $areaBarang = ($barangByArea[$area->id] ?? collect()); @endphp
                                                            @if($areaBarang->isEmpty())
                                                                @continue
                                                            @endif
                                                            <tr class="table-secondary">
                                                                <td colspan="6" class="font-weight-bold">
                                                                    {{ $area->area }}
                                                                    <div class="float-right">
                                                                        <div class="form-check form-check-inline mb-0">
                                                                            <input class="form-check-input check-all-kondisi" type="checkbox" data-area-id="{{ $area->id }}" id="check_all_kondisi_{{ $area->id }}">
                                                                            <label class="form-check-label" for="check_all_kondisi_{{ $area->id }}">Check All Kondisi OK</label>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            @foreach($areaBarang as $barang)
                                                                <tr data-area-id="{{ $area->id }}" data-barang-id="{{ $barang->id }}" data-index="{{ $rowIndex }}">
                                                                    <td>{{ $no++ }}</td>
                                                                    <td>
                                                                        {{ $barang->nama_barang }}
                                                                        <input type="hidden" name="items[{{ $rowIndex }}][id_area]" value="{{ $area->id }}">
                                                                        <input type="hidden" name="items[{{ $rowIndex }}][id_nama_barang]" value="{{ $barang->id }}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" class="form-control" name="items[{{ $rowIndex }}][jumlah]" value="{{ $barang->jumlah ?? 0 }}" min="0" required readonly>
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-control kondisi-select" name="items[{{ $rowIndex }}][kondisi]" data-area-id="{{ $area->id }}">
                                                                            <option value="" disabled selected>Pilih Kondisi</option>
                                                                            <option value="OK">OK</option>
                                                                            <option value="Tidak OK">Tidak OK</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <textarea class="form-control" name="items[{{ $rowIndex }}][temuan_ketidaksesuaian]" rows="1" placeholder="{{ $canFillTemuan ? 'Masukkan temuan (opsional)' : 'Hanya QC yang dapat mengisi' }}" @unless($canFillTemuan) readonly style="background:#f8f9fa;cursor:not-allowed;" @endunless></textarea>
                                                                    </td>
                                                                    <td>
                                                                        <textarea class="form-control" name="items[{{ $rowIndex }}][tindakan_koreksi]" rows="1" placeholder="Masukkan tindakan (opsional)"></textarea>
                                                                    </td>
                                                                </tr>
                                                                @php $rowIndex++; @endphp
                                                            @endforeach
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted">
                                                                Pilih shift terlebih dahulu untuk menampilkan data.
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_manual" name="is_manual" value="1" {{ old('is_manual') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_manual">
                                                    Input Manual Barang
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="manual-input-container" style="display: none;">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="id_area_manual" class="font-weight-bold">Area Manual <span class="text-danger">*</span></label>
                                            <select class="form-control @error('id_area_manual') is-invalid @enderror" id="id_area_manual" name="id_area_manual">
                                                <option value="">Pilih Area</option>
                                            </select>
                                            @error('id_area_manual')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="id_sub_area_manual" class="font-weight-bold">Sub Area Manual</label>
                                            <select class="form-control @error('id_sub_area_manual') is-invalid @enderror" id="id_sub_area_manual" name="id_sub_area_manual">
                                                <option value="">Pilih Sub Area</option>
                                            </select>
                                            @error('id_sub_area_manual')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="nama_barang_manual" class="font-weight-bold">Nama Barang Manual <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nama_barang_manual') is-invalid @enderror" id="nama_barang_manual" name="nama_barang_manual" value="{{ old('nama_barang_manual') }}">
                                            @error('nama_barang_manual')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="jumlah_manual" class="font-weight-bold">Jumlah Manual <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('jumlah_manual') is-invalid @enderror" id="jumlah_manual" name="jumlah_manual" value="{{ old('jumlah_manual') }}" min="0">
                                            @error('jumlah_manual')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="kondisi_manual" class="font-weight-bold">Kondisi Manual <span class="text-danger">*</span></label>
                                            <select class="form-control @error('kondisi_manual') is-invalid @enderror" id="kondisi_manual" name="kondisi_manual">
                                                <option value="">Pilih Kondisi</option>
                                                <option value="OK" {{ old('kondisi_manual') == 'OK' ? 'selected' : '' }}>OK</option>
                                                <option value="Tidak OK" {{ old('kondisi_manual') == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                            </select>
                                            @error('kondisi_manual')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="nama_karyawan" class="font-weight-bold">Nama Karyawan</label>
                                            <input type="text" class="form-control @error('nama_karyawan') is-invalid @enderror" id="nama_karyawan" name="nama_karyawan" value="{{ old('nama_karyawan') }}">
                                            @error('nama_karyawan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="temuan_ketidaksesuaian_manual" class="font-weight-bold">Temuan Ketidaksesuaian Manual</label>
                                            @unless($canFillTemuan)
                                                <div class="text-muted small mb-1"><i class="fas fa-lock"></i> Hanya QC yang dapat mengisi kolom ini</div>
                                            @endunless
                                            <textarea class="form-control @error('temuan_ketidaksesuaian_manual') is-invalid @enderror" id="temuan_ketidaksesuaian_manual" name="temuan_ketidaksesuaian_manual" rows="2" placeholder="{{ $canFillTemuan ? 'Masukkan temuan (opsional)' : 'Hanya QC yang dapat mengisi' }}" @unless($canFillTemuan) readonly style="background:#f8f9fa;cursor:not-allowed;" @endunless>{{ old('temuan_ketidaksesuaian_manual') }}</textarea>
                                            @error('temuan_ketidaksesuaian_manual')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="tindakan_koreksi_manual" class="font-weight-bold">Tindakan Koreksi Manual</label>
                                            <textarea class="form-control @error('tindakan_koreksi_manual') is-invalid @enderror" id="tindakan_koreksi_manual" name="tindakan_koreksi_manual" rows="2" placeholder="Masukkan tindakan (opsional)">{{ old('tindakan_koreksi_manual') }}</textarea>
                                            @error('tindakan_koreksi_manual')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>



                                <div class="">
                                    <div class="">
                                        <button type="submit" class="btn btn-primary btn-md mr-2">
                                            <i class="fas fa-save"></i> Simpan Data
                                        </button>
                                        <a href="{{ route('barang-mudah-pecah.index') }}" class="btn btn-md btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                </div>
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
    $(document).ready(function() {
        // Initialize Select2
        $('#id_area_manual, #id_sub_area_manual').select2({
            placeholder: "Pilih Opsi",
            allowClear: true
        });

        var canFillTemuan = {{ $canFillTemuan ? 'true' : 'false' }};
        var temuanAttr = canFillTemuan
            ? 'placeholder="Masukkan temuan (opsional)"'
            : 'placeholder="Hanya QC yang dapat mengisi" readonly style="background:#f8f9fa;cursor:not-allowed;"';

        function renderTableFromData(areas, barangByArea) {
            var tbody = $('#barang-mudah-pecah-table-body');
            tbody.empty();

            var index = 0;
            var no = 1;
            if (!Array.isArray(areas) || areas.length === 0) {
                tbody.append('<tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>');
                return;
            }

            areas.forEach(function(area) {
                var list = (barangByArea[String(area.id)] || []);
                if (!Array.isArray(list) || list.length === 0) {
                    return;
                }
                tbody.append(
                    '<tr class="table-secondary">' +
                        '<td colspan="6" class="font-weight-bold">' +
                            area.area +
                            '<div class="float-right">' +
                                '<div class="form-check form-check-inline mb-0">' +
                                    '<input class="form-check-input check-all-kondisi" type="checkbox" data-area-id="' + area.id + '" id="check_all_kondisi_' + area.id + '">' +
                                    '<label class="form-check-label" for="check_all_kondisi_' + area.id + '">Check All Kondisi OK</label>' +
                                '</div>' +
                            '</div>' +
                        '</td>' +
                    '</tr>'
                );

                list.forEach(function(barang) {
                    var jumlah = (barang.jumlah === null || typeof barang.jumlah === 'undefined') ? 0 : barang.jumlah;
                    tbody.append(
                        '<tr data-area-id="' + area.id + '" data-barang-id="' + barang.id + '" data-index="' + index + '">' +
                            '<td>' + (no++) + '</td>' +
                            '<td>' + barang.nama_barang + '</td>' +
                            '<td><input type="number" class="form-control" name="items[' + index + '][jumlah]" value="' + jumlah + '" min="0" readonly></td>' +
                            '<td>' +
                                '<select class="form-control kondisi-select" name="items[' + index + '][kondisi]">' +
                                    '<option value="" disabled selected>Pilih Kondisi</option>' +
                                    '<option value="OK">OK</option>' +
                                    '<option value="Tidak OK">Tidak OK</option>' +
                                '</select>' +
                            '</td>' +
                            '<td>' +
                                '<textarea class="form-control" name="items[' + index + '][temuan_ketidaksesuaian]" rows="1" ' + temuanAttr + '></textarea>' +
                            '</td>' +
                            '<td>' +
                                '<textarea class="form-control" name="items[' + index + '][tindakan_koreksi]" rows="1" placeholder="Masukkan tindakan (opsional)"></textarea>' +
                            '</td>' +
                        '</tr>'
                    );
                    index++;
                });
            });
        }

        function loadAreasByPlan(planId, selectedAreaId) {
            var areaSelect = $('#id_area_manual');
            areaSelect.empty().append('<option value="">Memuat...</option>');

            if (!planId) {
                areaSelect.empty().append('<option value="">Pilih Area</option>').trigger('change');
                return;
            }

            $.ajax({
                url: `{{ url('qc-sistem/ajax/area-by-plan') }}/${planId}`,
                type: 'GET',
                success: function (data) {
                    areaSelect.empty().append('<option value="">Pilih Area</option>');
                    if (Array.isArray(data)) {
                        data.forEach(function (it) {
                            var selected = (String(selectedAreaId) === String(it.id)) ? 'selected' : '';
                            areaSelect.append('<option value="' + it.id + '" ' + selected + '>' + it.area + '</option>');
                        });
                    }
                    areaSelect.trigger('change');
                },

                error: function () {
                    areaSelect.empty().append('<option value="">Gagal memuat area</option>').trigger('change');
                }
            });
        }

        var isSuperadmin = '{{ auth()->user()->role ?? '' }}' === 'superadmin';
        var userPlanId = '{{ auth()->user()->id_plan ?? '' }}';

        function buildTableForPlan(planId) {
            if (!planId) {
                renderTableFromData([], {});
                return;
            }

            $.ajax({
                url: `{{ url('qc-sistem/ajax/area-by-plan') }}/${planId}`,
                type: 'GET',
                success: function(areas) {
                    var requests = [];
                    var barangByArea = {};
                    if (Array.isArray(areas)) {
                        areas.forEach(function(area) {
                            requests.push(
                                $.ajax({
                                    url: `{{ url('qc-sistem/ajax/data-barang-by-area') }}/${area.id}`,
                                    type: 'GET',
                                    success: function(barang) {
                                        barangByArea[String(area.id)] = Array.isArray(barang) ? barang : [];
                                    }
                                })
                            );
                        });
                    }

                    $.when.apply($, requests).always(function() {
                        renderTableFromData(areas, barangByArea);
                    });

                    loadAreasByPlan(planId, '{{ old('id_area_manual') }}');
                },
                error: function() {
                    renderTableFromData([], {});
                }
            });
        }

        if (isSuperadmin) {
            $('#shift_id').on('change', function () {
                var planId = $(this).find('option:selected').data('plan-id');
                $('#id_sub_area_manual').empty().append('<option value="">Pilih Sub Area</option>').trigger('change');
                buildTableForPlan(planId);
            });
        } else {
            loadAreasByPlan(userPlanId, '{{ old('id_area_manual') }}');
        }

        // Event listener for Area Manual change (sub area manual)
        $('#id_area_manual').on('change', function () {
            var areaId = $(this).val();
            var subAreaSelect = $('#id_sub_area_manual');
            subAreaSelect.empty().append('<option value="">Pilih Sub Area</option>');

            if (areaId) {
                $.ajax({
                    url: '{{ route("get-sub-areas-by-area", [':areaId']) }}'.replace(':areaId', areaId),
                    type: 'GET',
                    success: function (data) {
                        $.each(data, function (key, value) {
                            subAreaSelect.append('<option value="' + value.id + '">' + value.lokasi_area + '</option>');
                        });
                        subAreaSelect.trigger('change');
                    }
                });
            }
        });

        // Trigger initial build table
        var initialPlan = isSuperadmin
            ? $('#shift_id').find('option:selected').data('plan-id')
            : userPlanId;
        if (isSuperadmin) {
            buildTableForPlan(initialPlan);
        }

        // Check all kondisi per area
        $(document).on('change', '.check-all-kondisi', function() {
            var areaId = $(this).data('area-id');
            var isChecked = $(this).is(':checked');
            var value = isChecked ? 'OK' : '';
            $('.kondisi-select[data-area-id="' + areaId + '"]').val(value);
        });

        // Manual input checkbox logic
        function toggleManualInput() {
            if ($('#is_manual').is(':checked')) {
                $('#manual-input-container').show();
                $('#id_area_manual, #id_sub_area_manual, #nama_barang_manual, #jumlah_manual, #kondisi_manual, #nama_karyawan, #temuan_ketidaksesuaian_manual, #tindakan_koreksi_manual').prop('disabled', false);
            } else {
                $('#manual-input-container').hide();
                $('#id_area_manual, #id_sub_area_manual, #nama_barang_manual, #jumlah_manual, #kondisi_manual, #nama_karyawan, #temuan_ketidaksesuaian_manual, #tindakan_koreksi_manual').prop('disabled', true);
            }
        }

        $('#is_manual').on('change', toggleManualInput);

        // Initial state on page load
        toggleManualInput();

        // Sebelum submit: simpan mapping area&barang ke 1 hidden field global
        // agar tiap baris hanya kirim 4 field (jumlah, kondisi, temuan, tindakan)
        // total 170x4 = 680 < max_input_vars 1000
        $('form').on('submit', function() {
            var mapping = {};
            $('tr[data-area-id]').each(function() {
                var $row = $(this);
                var kondisi = $row.find('.kondisi-select').val();
                var index = $row.data('index');

                if (kondisi && kondisi !== '') {
                    mapping[index] = {
                        id_area: $row.data('area-id'),
                        id_nama_barang: $row.data('barang-id')
                    };
                }
                // Jangan disable field apapun — biarkan semua items[] dikirim,
                // controller sudah mem-filter baris tanpa kondisi via items_mapping.
            });
            $('<input>').attr({type:'hidden', name:'items_mapping'}).val(JSON.stringify(mapping)).appendTo('form');
        });
    });
</script>
@endpush