@extends('layouts.app')

@php
    $assetPath = (isset($_SERVER['DOCUMENT_ROOT']) && realpath($_SERVER['DOCUMENT_ROOT']) === realpath(public_path())) ? '' : 'public/';
@endphp

@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> Pemeriksaan Kedatangan Bahan Baku Kentang</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shoestring.index') }}">Shoestring</a></li>
                        <li class="breadcrumb-item active">Edit Data</li>
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
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Data Shoestring</h3>
                        </div>
                        <!-- /.card-header -->
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

                            <form action="{{ route('shoestring.update', $shoestring->uuid) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Data Utama</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Nama Produsen <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <select name="nama_produsen" id="nama_produsen" class="form-control" required>
                                                    <option value="">Pilih Produsen</option>
                                                    @foreach($produsens as $produsen)
                                                        <option value="{{ $produsen }}" {{ old('nama_produsen', $shoestring->nama_produsen) == $produsen ? 'selected' : '' }}>
                                                            {{ $produsen }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Kode Produksi <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" name="kode_produksi" class="form-control" 
                                                       value="{{ old('kode_produksi', $shoestring->kode_produksi) }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Shift <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="shift_id" required>
                                                    <option value="">Pilih Shift</option>
                                                    @foreach($shifts as $shift)
                                                        <option value="{{ $shift->id }}" {{ old('shift_id', $shoestring->shift_id) == $shift->id ? 'selected' : '' }}>
                                                            {{ $shift->shift }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        {{-- dev_bagus_perbaikan_29_mei:remove disable form--}}
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">
                                                Tanggal <span class="text-danger">*</span>
                                            </label>

                                            <div class="col-sm-9">
                                                <input type="date"
                                                    id="tanggal_display"
                                                    class="form-control"
                                                    value="{{ old('tanggal', $shoestring->tanggal ? \Carbon\Carbon::parse($shoestring->tanggal)->format('Y-m-d') : '') }}"
                                                    onchange="
                                                        const t = document.getElementById('tanggal').value.split(' ')[1];
                                                        const [y,m,d] = this.value.split('-');
                                                        document.getElementById('tanggal').value = `${d}-${m}-${y} ${t}`;
                                                    "
                                                    required>

                                                <input type="hidden"
                                                    id="tanggal"
                                                    name="tanggal"
                                                    value="{{ old('tanggal', $shoestring->tanggal ? \Carbon\Carbon::parse($shoestring->tanggal)->format('d-m-Y H:i:s') : '') }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Jam <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="time" name="jam" class="form-control @error('jam') is-invalid @enderror" 
                                                       value="{{ old('jam', $shoestring->jam ? \Carbon\Carbon::parse($shoestring->jam)->format('H:i') : '') }}" required>
                                                @error('jam')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Tanggal Expired <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="date" name="tgl_exp" class="form-control" 
                                                       value="{{ old('tgl_exp', $shoestring->best_before ? \Carbon\Carbon::parse($shoestring->best_before)->format('Y-m-d') : '') }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Gramase (gram)</label>
                                            <div class="col-sm-9">
                                                <select name="berat_sampling" id="nilai_select_berat_sampling" class="form-control" data-nilai-berat data-selected="{{ old('berat_sampling', $shoestring->berat_sampling) }}"></select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Catatan</label>
                                            <div class="col-sm-9">
                                                <textarea name="catatan" class="form-control" rows="3" 
                                                    placeholder="Masukkan catatan tambahan">{{ old('catatan', $shoestring->catatan) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Dokumentasi Label Supplier <span class="text-danger">* max 1mb/file (auto compress)</span></label>
                                            <div class="col-sm-9">
                                                @if($shoestring->dokumentasi && is_array($shoestring->dokumentasi) && count($shoestring->dokumentasi) > 0)
                                                    <div class="mb-3">
                                                        <label>Foto Saat Ini:</label>
                                                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                                            @foreach($shoestring->dokumentasi as $doc)
                                                                <div class="position-relative">
                                                                    <a href="{{ asset($assetPath . 'storage/' . $doc) }}" target="_blank">
                                                                        <img src="{{ asset($assetPath . 'storage/' . $doc) }}" class="img-thumbnail" style="height: 100px; width: 100px; object-fit: cover;">
                                                                    </a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                                <input type="file" id="dokumentasi-input" class="form-control-file" accept="image/*" capture="camera" multiple>
                                                <div id="dokumentasi-preview" class="mt-2" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
                                                <div id="dokumentasi-base64-container"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="float-left">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-save"></i> Update Data
                                            </button>
                                            <a href="{{ route('shoestring.index') }}" class="btn btn-secondary ml-2">
                                                <i class="fas fa-arrow-left"></i> Kembali
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // ─── Dokumentasi upload & compress ────────────────────────────────────
    $('#dokumentasi-input').on('change', function(e) {
        const $previewContainer = $('#dokumentasi-preview');
        const $base64Container  = $('#dokumentasi-base64-container');
        $previewContainer.empty();
        $base64Container.empty();

        const files = e.target.files;
        if (!files || files.length === 0) return;

        Array.from(files).forEach(file => {
            if (!file.type.match('image.*')) return;
            const reader = new FileReader();
            reader.onload = function(readerEvent) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    let w = img.width, h = img.height;
                    const MAX = 1200;
                    if (w > h) { if (w > MAX) { h *= MAX/w; w = MAX; } }
                    else       { if (h > MAX) { w *= MAX/h; h = MAX; } }
                    canvas.width = w; canvas.height = h;
                    canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                    const dataUrl = canvas.toDataURL('image/jpeg', 0.7);
                    $previewContainer.append(`<img src="${dataUrl}" class="img-thumbnail" style="height:100px;width:100px;object-fit:cover;">`);
                    const hidden = $(`<input type="hidden" name="dokumentasi_base64[]">`);
                    hidden.val(dataUrl);
                    $base64Container.append(hidden);
                };
                img.src = readerEvent.target.result;
            };
            reader.readAsDataURL(file);
        });
    });
});
</script>
@endpush