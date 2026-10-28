@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Release Produk Finish Good</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('release-produk.index') }}">Release Produk</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('release-produk.update', $item->uuid) }}" method="POST" enctype="multipart/form-data" id="releaseForm">
                @csrf
                @method('PUT')
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Informasi Umum</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $item->tanggal }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Jam</label>
                                    <input type="time" name="jam" id="jam" class="form-control" value="{{ $item->jam }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Shift</label>
                                    <select name="id_shift" id="id_shift" class="form-control select2" required>
                                        <option value="">Pilih Shift</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ $item->id_shift == $shift->id ? 'selected' : '' }}>{{ $shift->shift }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Plan</label>
                                    <input type="text" class="form-control" value="{{ $item->plan->nama_plan ?? 'Semua Plan' }}" readonly disabled>
                                    <input type="hidden" name="id_plan" value="{{ $item->id_plan }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Produk</label>
                                    <select name="id_produk" id="id_produk" class="form-control select2" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ $item->id_produk == $produk->id ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-thermometer-half"></i> Parameter & Fisik</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Suhu IQF (℃)</label>
                                    <input type="number" step="0.1" name="suhu_iqf" id="suhu_iqf" class="form-control" value="{{ $item->suhu_iqf }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Suhu Produk (℃)</label>
                                    <input type="text" name="suhu_produk" id="suhu_produk" class="form-control" value="{{ $item->suhu_produk }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Lama Pembekuan (Menit)</label>
                                    <input type="number" name="lama_pembekuan" id="lama_pembekuan" class="form-control" value="{{ $item->lama_pembekuan }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Kondisi Kemasan Primer</label>
                                    <select name="kondisi_kemasan_primer" id="kondisi_kemasan_primer" class="form-control" required>
                                        <option value="OK" {{ $item->kondisi_kemasan_primer == 'OK' ? 'selected' : '' }}>OK</option>
                                        <option value="Tidak OK" {{ $item->kondisi_kemasan_primer == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Jumlah Box</label>
                                    <input type="number" name="jumlah_box" id="jumlah_box" class="form-control" value="{{ $item->jumlah_box }}" required>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <label>Data Berat Packing Sekunder (Kg)</label>
                                <table class="table table-bordered table-sm" id="dynamicTable">
                                    <thead>
                                        <tr class="bg-light text-center">
                                            <th>Berat (Kg)</th>
                                            <th style="width: 50px">
                                                <button type="button" class="btn btn-success btn-xs" id="addRow">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @foreach(is_array($item->berat_packing_sekunder) ? $item->berat_packing_sekunder : [$item->berat_packing_sekunder] as $berat)
                                        <tr>
                                            <td><input type="number" step="0.01" name="berat_packing_sekunder[]" class="form-control form-control-sm" value="{{ $berat }}" required></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-xs remove-row"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-camera"></i> Dokumentasi Foto</h3>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-6 mb-3">
                                <label>Foto Kemasan Sekunder</label>
                                <div class="d-flex flex-column align-items-center">
                                    <div class="preview-container mb-2" style="width: 100%; max-width: 300px; height: 200px; border: 2px dashed #ccc; border-radius: 10px; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                                        @php
                                            $pathS = storage_path('app/public/' . $item->foto_kemasan_sekunder);
                                            $dataS = ($item->foto_kemasan_sekunder && file_exists($pathS)) ? base64_encode(file_get_contents($pathS)) : null;
                                        @endphp
                                        @if($dataS)
                                            <img id="preview_sekunder" src="data:image/jpeg;base64,{{ $dataS }}" alt="Preview" style="max-width: 100%; max-height: 100%;">
                                            <i class="fas fa-box-open fa-3x text-muted" id="icon_sekunder" style="display: none;"></i>
                                        @else
                                            <img id="preview_sekunder" src="#" alt="Preview" style="max-width: 100%; max-height: 100%; display: none;">
                                            <i class="fas fa-box-open fa-3x text-muted" id="icon_sekunder"></i>
                                        @endif
                                    </div>
                                    <input type="file" name="foto_kemasan_sekunder" id="foto_kemasan_sekunder" class="d-none image-input" accept="image/*" capture="environment">
                                    <button type="button" class="btn btn-warning" onclick="document.getElementById('foto_kemasan_sekunder').click()">
                                        <i class="fas fa-camera"></i> Ubah Foto Sekunder
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Foto Kemasan Primer</label>
                                <div class="d-flex flex-column align-items-center">
                                    <div class="preview-container mb-2" style="width: 100%; max-width: 300px; height: 200px; border: 2px dashed #ccc; border-radius: 10px; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                                        @php
                                            $pathP = storage_path('app/public/' . $item->foto_kemasan_primer);
                                            $dataP = ($item->foto_kemasan_primer && file_exists($pathP)) ? base64_encode(file_get_contents($pathP)) : null;
                                        @endphp
                                        @if($dataP)
                                            <img id="preview_primer" src="data:image/jpeg;base64,{{ $dataP }}" alt="Preview" style="max-width: 100%; max-height: 100%;">
                                            <i class="fas fa-box fa-3x text-muted" id="icon_primer" style="display: none;"></i>
                                        @else
                                            <img id="preview_primer" src="#" alt="Preview" style="max-width: 100%; max-height: 100%; display: none;">
                                            <i class="fas fa-box fa-3x text-muted" id="icon_primer"></i>
                                        @endif
                                    </div>
                                    <input type="file" name="foto_kemasan_primer" id="foto_kemasan_primer" class="d-none image-input" accept="image/*" capture="environment">
                                    <button type="button" class="btn btn-warning" onclick="document.getElementById('foto_kemasan_primer').click()">
                                        <i class="fas fa-camera"></i> Ubah Foto Primer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-check-double"></i> Kesimpulan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="Release" {{ $item->status == 'Release' ? 'selected' : '' }}>Release</option>
                                        <option value="Tidak Release" {{ $item->status == 'Tidak Release' ? 'selected' : '' }}>Tidak Release</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Keterangan</label>
                                    <textarea name="keterangan" id="keterangan" class="form-control" rows="2">{{ $item->keterangan }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tindakan Koreksi</label>
                                    <textarea name="tindakan_koreksi" id="tindakan_koreksi" class="form-control" rows="2">{{ $item->tindakan_koreksi }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('release-produk.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="fas fa-save"></i> Update Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2({ theme: 'bootstrap4' });

    // Dynamic Table Row logic
    $('#addRow').on('click', function() {
        const newRow = `
            <tr>
                <td><input type="number" step="0.01" name="berat_packing_sekunder[]" class="form-control form-control-sm" placeholder="10.00" required></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-xs remove-row"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`;
        $('#tableBody').append(newRow);
    });

    $(document).on('click', '.remove-row', function() {
        if ($('#tableBody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            toastr.warning('Minimal harus ada satu baris!');
        }
    });

    // Image Compression & Preview
    $('.image-input').on('change', function(e) {
        const file = e.target.files[0];
        const id = $(this).attr('id');
        const previewId = id.replace('foto_kemasan_', 'preview_');
        const iconId = id.replace('foto_kemasan_', 'icon_');

        if (!file) return;

        // Preview
        const reader = new FileReader();
        reader.onload = function(e) {
            $(`#${previewId}`).attr('src', e.target.result).show();
            $(`#${iconId}`).hide();
        }
        reader.readAsDataURL(file);

        // Compression (if > 2MB)
        if (file.size > 2 * 1024 * 1024) {
            toastr.info('Mengompres gambar...');
            compressImage(file, $(this));
        }
    });

    function compressImage(file, $input) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function(event) {
            const img = new Image();
            img.src = event.target.result;
            img.onload = function() {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;

                const MAX_WIDTH = 1280;
                const MAX_HEIGHT = 1280;

                if (width > height) {
                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }
                } else {
                    if (height > MAX_HEIGHT) {
                        width *= MAX_HEIGHT / height;
                        height = MAX_HEIGHT;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob((blob) => {
                    const compressedFile = new File([blob], file.name, {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(compressedFile);
                    $input[0].files = dataTransfer.files;
                    
                    toastr.success('Gambar berhasil dikompres');
                }, 'image/jpeg', 0.7);
            }
        }
    }
});
</script>
@endpush
