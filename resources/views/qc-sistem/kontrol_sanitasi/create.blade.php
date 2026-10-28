@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Data Kontrol Sanitasi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kontrol-sanitasi.index') }}">Kontrol Sanitasi</a></li>
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
                                Form Tambah Data Kontrol Sanitasi
                            </h3>
                        </div>
                        <form action="{{ route('kontrol-sanitasi.store') }}" method="POST">
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
                                            <label for="shift_id" class="font-weight-bold">Shift <span class="text-danger">*</span></label>
                                            <select class="form-control @error('shift_id') is-invalid @enderror" id="shift_id" name="shift_id" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->shift }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-3 mt-4">
                                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-list-alt mr-2 text-primary"></i>Rincian Pemeriksaan Sanitasi</h5>
                                    <button type="button" class="btn btn-success btn-sm ml-auto" id="addRowBtn">
                                        <i class="fas fa-plus"></i> Tambah Baris
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="entriesTable">
                                        <thead class="bg-light text-center">
                                            <tr>
                                                <th style="width: 5%">No</th>
                                                <th style="width: 12%">Jam</th>
                                                <th style="width: 12%">Suhu Air (°C)</th>
                                                <th style="width: 18%">Kadar Klorin Foot Basin</th>
                                                <th style="width: 18%">Kadar Klorin Hand Basin</th>
                                                <th>Hasil Verifikasi</th>
                                                <th style="width: 12%">Keterangan</th>
                                                <th style="width: 7%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="entriesTableBody">
                                            @php $oldEntries = old('entries', [[]]); @endphp
                                            @foreach($oldEntries as $index => $entry)
                                            <tr data-index="{{ $index }}">
                                                <td class="text-center align-middle font-weight-bold">{{ $index + 1 }}</td>
                                                <td>
                                                    <input type="time" name="entries[{{ $index }}][jam]" class="form-control" value="{{ $entry['jam'] ?? \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') }}" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="entries[{{ $index }}][suhu_air]" class="form-control" value="{{ $entry['suhu_air'] ?? '' }}" placeholder="0" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="entries[{{ $index }}][kadar_klorin_food_basin]" class="form-control" value="{{ $entry['kadar_klorin_food_basin'] ?? '' }}" placeholder="Isi..." required>
                                                </td>
                                                <td>
                                                    <input type="text" name="entries[{{ $index }}][kadar_klorin_hand_basin]" class="form-control" value="{{ $entry['kadar_klorin_hand_basin'] ?? '' }}" placeholder="Isi..." required>
                                                </td>
                                                <td>
                                                    <textarea name="entries[{{ $index }}][hasil_verifikasi]" class="form-control" rows="1" placeholder="Catatan hasil..." required>{{ $entry['hasil_verifikasi'] ?? '' }}</textarea>
                                                </td>
                                                <td>
                                                    <input type="text" name="entries[{{ $index }}][keterangan]" class="form-control" value="{{ $entry['keterangan'] ?? '' }}" placeholder="Catatan..." required>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <button type="button" class="btn btn-danger btn-sm btn-remove-row" {{ count($oldEntries) <= 1 ? 'style=display:none' : '' }}>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-4 border-top pt-3">
                                    <button type="submit" class="btn btn-primary shadow-sm px-4">
                                        <i class="fas fa-save mr-1"></i> Simpan Laporan
                                    </button>
                                    <a href="{{ route('kontrol-sanitasi.index') }}" class="btn btn-secondary shadow-sm px-4 ml-2">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let rowIndex = {{ count($oldEntries) - 1 }};

    function updateNumbers() {
        $('#entriesTableBody tr').each(function(i) {
            $(this).find('td:first').text(i + 1);
            $(this).attr('data-index', i);
            
            // Re-index all inputs
            $(this).find('input, textarea').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    const newName = name.replace(/entries\[\d+\]/, `entries[${i}]`);
                    $(this).attr('name', newName);
                }
            });
        });
        
        // Hide remove button if only one row
        const rowCount = $('#entriesTableBody tr').length;
        if (rowCount <= 1) {
            $('.btn-remove-row').hide();
        } else {
            $('.btn-remove-row').show();
        }
    }

    $('#addRowBtn').click(function() {
        rowIndex++;
        const nowTime = moment().format('HH:mm');
        const newRow = `
            <tr data-index="${rowIndex}">
                <td class="text-center align-middle font-weight-bold">${rowIndex + 1}</td>
                <td>
                    <input type="time" name="entries[${rowIndex}][jam]" class="form-control" value="${nowTime}" required>
                </td>
                <td>
                    <input type="text" name="entries[${rowIndex}][suhu_air]" class="form-control" placeholder="0" required>
                </td>
                <td>
                    <input type="text" name="entries[${rowIndex}][kadar_klorin_food_basin]" class="form-control" placeholder="Isi..." required>
                </td>
                <td>
                    <input type="text" name="entries[${rowIndex}][kadar_klorin_hand_basin]" class="form-control" placeholder="Isi..." required>
                </td>
                <td>
                    <textarea name="entries[${rowIndex}][hasil_verifikasi]" class="form-control" rows="1" placeholder="Catatan hasil..." required></textarea>
                </td>
                <td>
                    <input type="text" name="entries[${rowIndex}][keterangan]" class="form-control" placeholder="Catatan..." required>
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#entriesTableBody').append(newRow);
        updateNumbers();
    });

    $(document).on('click', '.btn-remove-row', function() {
        if ($('#entriesTableBody tr').length > 1) {
            $(this).closest('tr').remove();
            updateNumbers();
        }
    });

    // Initial check
    updateNumbers();
});
</script>
@endpush

@endsection