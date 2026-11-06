@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-edit text-primary mr-2"></i> Edit Pemeriksaan Metal Detector</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('input-metal-detector.index') }}"><i class="fas fa-search"></i> Metal Detector</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Form Edit Pemeriksaan Metal Detector (Produksi)</h3>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-ban"></i> Terjadi Kesalahan!</h5>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="alert alert-info py-2 mb-3">
                                <i class="fas fa-info-circle mr-1"></i> <strong>Peran Produksi:</strong> Edit data <strong>A. Detail Pemeriksaan Metal Detector</strong> dan <strong>B. Hasil Pengecekan</strong>. Uji spesimen dan verifikasi dilakukan oleh <strong>QC Inspector</strong> melalui menu <strong>Lihat Detail (icon mata)</strong>.
                            </div>

                            <form class="form-horizontal" id="main-metal-detector-form" method="POST" action="{{ route('input-metal-detector.update', $metalDetector->uuid) }}">
                                @csrf
                                @method('PUT')

                                <!-- BAGIAN A: DETAIL PEMERIKSAAN METAL DETECTOR -->
                                <div class="card card-outline card-info shadow-sm mb-4">
                                    <div class="card-header py-2">
                                        <h5 class="card-title font-weight-bold text-info mb-0">
                                            <i class="fas fa-clipboard-list mr-1"></i> A. Detail Pemeriksaan Metal Detector
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-calendar-alt mr-1"></i> Tanggal Kode Produksi <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                                        id="tanggal" name="tanggal" 
                                                        value="{{ old('tanggal', $metalDetector->tanggal ? \Carbon\Carbon::parse($metalDetector->tanggal)->format('Y-m-d') : '') }}" required>
                                                    @error('tanggal')
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-clock mr-1"></i> Jam <span class="text-danger">*</span></label>
                                                    <input type="time" class="form-control @error('jam') is-invalid @enderror" 
                                                        name="jam" 
                                                        value="{{ old('jam', $metalDetector->jam ? \Carbon\Carbon::parse($metalDetector->jam)->format('H:i') : '') }}" required>
                                                    @error('jam')
                                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Shift <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" name="id_shift" required>
                                                        <option value="">Pilih Shift</option>
                                                        @foreach($shifts as $shift)
                                                            <option value="{{ $shift->id }}" {{ old('id_shift', $metalDetector->id_shift) == $shift->id ? 'selected' : '' }}>{{ $shift->shift }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Line <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" name="line" required>
                                                        <option value="">Pilih Line</option>
                                                        @for($i = 1; $i <= 8; $i++)
                                                            <option value="{{ $i }}" {{ old('line', $metalDetector->line) == $i ? 'selected' : '' }}>Line {{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                                    <select class="form-control select2 @error('id_produk') is-invalid @enderror" id="id_produk" name="id_produk" required>
                                                        <option value="">Pilih Produk</option>
                                                        @foreach($produks as $produk)
                                                            <option value="{{ $produk->id }}" {{ old('id_produk', $metalDetector->id_produk) == $produk->id ? 'selected' : '' }}>
                                                                {{ $produk->nama_produk }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label">Kode Produksi <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('kode_produksi') is-invalid @enderror" 
                                                        name="kode_produksi" value="{{ old('kode_produksi', $metalDetector->kode_produksi) }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label">Keterangan (Bagian A)</label>
                                                    <input type="text" class="form-control" name="keterangan" value="{{ old('keterangan', $metalDetector->keterangan) }}" placeholder="Keterangan Bagian A">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BAGIAN B: HASIL PENGECEKAN (TAMBAHAN BARU) -->
                                <div class="card card-outline card-success shadow-sm mb-4">
                                    <div class="card-header py-2">
                                        <h5 class="card-title font-weight-bold text-success mb-0">
                                            <i class="fas fa-check-double mr-1"></i> B. Hasil Pengecekan
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Merk (Mesin MD) <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="merk" value="{{ old('merk', $metalDetector->merk) }}" placeholder="Contoh: Anritsu / Ishida" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-hourglass-start mr-1"></i> Waktu Mulai <span class="text-danger">*</span></label>
                                                    <input type="time" class="form-control" name="waktu_mulai" value="{{ old('waktu_mulai', $metalDetector->waktu_mulai ? \Carbon\Carbon::parse($metalDetector->waktu_mulai)->format('H:i') : '') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label"><i class="fas fa-hourglass-end mr-1"></i> Waktu Selesai <span class="text-danger">*</span></label>
                                                    <input type="time" class="form-control" name="waktu_selesai" value="{{ old('waktu_selesai', $metalDetector->waktu_selesai ? \Carbon\Carbon::parse($metalDetector->waktu_selesai)->format('H:i') : '') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Gramasi (gr)</label>
                                                    <select class="form-control" id="nilai_select_berat_sampling" name="berat_produk">
                                                        @if($metalDetector->berat_produk)
                                                            <option value="{{ $metalDetector->berat_produk }}" selected>{{ $metalDetector->berat_produk }} gram</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Jumlah Reject Logam <span class="text-danger">*</span></label>
                                                    <input type="number" min="0" class="form-control" name="jumlah_reject" value="{{ old('jumlah_reject', $metalDetector->jumlah_reject ?? 0) }}" required>
                                                    <small class="text-muted">Isikan angka (0 jika tidak ada)</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Nama Operator <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="nama_operator" value="{{ old('nama_operator', $metalDetector->nama_operator) }}" placeholder="Nama operator yang bertugas" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Foreman / Forelady / SPV <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="spv_foreman" value="{{ old('spv_foreman', $metalDetector->spv_foreman) }}" placeholder="Nama SPV / Foreman" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Kolom Keterangan (Bagian B)</label>
                                                    <input type="text" class="form-control" name="keterangan_b" value="{{ old('keterangan_b', $metalDetector->keterangan_b) }}" placeholder="Keterangan proses / reject">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-primary btn-md mr-2 font-weight-bold">
                                            <i class="fas fa-save mr-1"></i> Perbarui Data
                                        </button>
                                        <a href="{{ route('input-metal-detector.index') }}" class="btn btn-secondary btn-md">
                                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
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
    function loadBeratOptions(selectedVal) {
        var beratSelect = $('#nilai_select_berat_sampling');
        beratSelect.empty();
        beratSelect.append('<option value="">Pilih Berat Produk</option>');
        if (typeof nilaiBerat !== 'undefined' && nilaiBerat.length > 0) {
            $.each(nilaiBerat, function(i, val) {
                var isSelected = (val == selectedVal) ? 'selected' : '';
                beratSelect.append('<option value="' + val + '" ' + isSelected + '>' + val + ' gram</option>');
            });
        }
    }

    var currentBerat = '{{ old("berat_produk", $metalDetector->berat_produk) }}';
    if ($('#id_produk').val()) {
        loadBeratOptions(currentBerat);
    }

    $('#id_produk').on('change', function() {
        loadBeratOptions('');
    });
});
</script>
@endpush
