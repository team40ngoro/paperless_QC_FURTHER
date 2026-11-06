@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Pemeriksaan Labelisasi Start Up</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('label-pemeriksaan.index') }}">Pemeriksaan Labelisasi Start Up</a></li>
                        <li class="breadcrumb-item active">Tambah Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <form action="{{ route('label-pemeriksaan.store') }}" method="POST" id="main-form">
        @csrf
        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <!-- Header Card -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Dasar</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Hari/Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Shift</label>
                                    <select name="id_shift" class="form-control select2" required>
                                        <option value="">Pilih Shift</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Plant</label>
                                    <input type="text" class="form-control" value="{{ $user->plan->nama_plan ?? '-' }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Table Card -->
                <div class="card card-info card-outline">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Data Pemeriksaan</h3>
                        <div class="ml-auto">
                            <button type="button" class="btn btn-success btn-sm" id="btn-add-row">
                                <i class="fas fa-plus"></i> Tambah Baris
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="table-pemeriksaan">
                                <thead class="bg-light">
                                    <tr class="text-center">
                                        <th style="width: 50px;">No</th>
                                        <th style="width: 220px;">Nama Produk</th>
                                        <th style="width: 90px;">Berat (gram)</th>
                                        <th style="width: 100px;">Label</th>
                                        <th style="width: 120px;">Pukul</th>
                                        <th style="width: 150px;">Foto Kode Produksi</th>
                                        <th style="width: 260px;">PIC Produksi</th>
                                        <th>Tindakan Koreksi</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="row-container">
                                    <!-- Row Template (Initial row) -->
                                    <tr class="inspection-row" data-index="0">
                                        <td class="text-center row-number">1</td>
                                        <td>
                                            <select name="id_produk[]" class="form-control select2" required style="width: 100%;">
                                                <option value="">Pilih Produk</option>
                                                @foreach($produks as $produk)
                                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="berat[]" class="form-control" data-nilai-berat></select>
                                        </td>
                                        <td>
                                            <select name="label_produk[]" class="form-control" required style="width: 100%;">
                                                <option value="PL">PL</option>
                                                <option value="MC">MC</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="time" name="jam[]" class="form-control" required value="{{ date('H:i') }}">
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex flex-column align-items-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-camera" data-target="#preview_foto_0" data-input="#foto_input_0">
                                                    <i class="fas fa-camera"></i> Ambil Foto
                                                </button>
                                                <div class="mt-2 preview-container" style="width: 100px; height: 80px; border: 1px dashed #ccc; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                    <img id="preview_foto_0" src="#" style="max-width: 100%; max-height: 100%; display: none;">
                                                    <i class="fas fa-image text-muted" id="icon_foto_0"></i>
                                                </div>
                                                <input type="hidden" name="foto_kode_produksi[]" id="foto_input_0">
                                                <input type="file" class="hidden-camera-input" accept="image/*" capture="environment" style="display: none;">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group mb-1">
                                                <label class="small mb-1">Operator</label>
                                                <input type="text" name="nama_operator[]" class="form-control form-control-sm mb-1" placeholder="Nama Operator" required>
                                                <button type="button" class="btn btn-xs btn-block btn-outline-info btn-signature" data-type="operator" data-target="#sig_input_op_0" data-preview="#sig_preview_op_0">
                                                    <i class="fas fa-signature"></i> TTD Operator
                                                </button>
                                                <img id="sig_preview_op_0" src="#" style="max-width: 100%; height: 40px; display: none;" class="mt-1 border">
                                                <input type="hidden" name="ttd_operator[]" id="sig_input_op_0" required>
                                            </div>
                                        </td>
                                        <td>
                                            <textarea name="tindakan_koreksi[]" class="form-control" rows="3" placeholder="Opsional"></textarea>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger btn-remove-row" style="display: none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('label-pemeriksaan.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary ml-2">Simpan Data</button>
                </div>
            </div>
        </section>
    </form>

    <!-- Signature Modal -->
    <div class="modal fade" id="signatureModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="signatureTitle">Tanda Tangan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div style="border: 2px dashed #ccc; background: #fff;">
                       <canvas id="signature-pad" width="450" height="250" style="width: 100%; height: 250px;"></canvas>
                    </div>
                    <p class="text-muted mt-2">Gunakan jari atau mouse untuk tanda tangan di kotak di atas.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary mr-auto" id="btn-clear-sig">Hapus</button>
                    <button type="button" class="btn btn-primary" id="btn-save-sig">Simpan Tanda Tangan</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .inspection-row td { vertical-align: middle !important; }
    #signature-pad { cursor: crosshair; }
    .hidden-camera-input { position: absolute; left: -9999px; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
$(document).ready(function() {
    let rowIndex = 0;
    
    // Initialize Select2
    $('.select2').select2();

    // --- Dynamic Row Logic ---
    $('#btn-add-row').on('click', function() {
        rowIndex++;
        const newRow = $('.inspection-row').first().clone();
        
        // Update IDs and clear values
        newRow.attr('data-index', rowIndex);
        newRow.find('.row-number').text(rowIndex + 1);
        
        // Reset Inputs
        newRow.find('input').val('');
        newRow.find('textarea').val('');
        newRow.find('img').hide().attr('src', '#');
        newRow.find('.preview-container i').show();
        
        // Update unique IDs for camera and signatures
        newRow.find('.btn-camera').attr('data-target', '#preview_foto_' + rowIndex).attr('data-input', '#foto_input_' + rowIndex);
        newRow.find('img[id^="preview_foto_"]').attr('id', 'preview_foto_' + rowIndex);
        newRow.find('i[id^="icon_foto_"]').attr('id', 'icon_foto_' + rowIndex);
        newRow.find('input[name="foto_kode_produksi[]"]').attr('id', 'foto_input_' + rowIndex);
        
        newRow.find('.btn-signature[data-type="operator"]').attr('data-target', '#sig_input_op_' + rowIndex).attr('data-preview', '#sig_preview_op_' + rowIndex);
        newRow.find('img[id^="sig_preview_op_"]').attr('id', 'sig_preview_op_' + rowIndex);
        newRow.find('input[name="ttd_operator[]"]').attr('id', 'sig_input_op_' + rowIndex);



        // Show remove button
        newRow.find('.btn-remove-row').show();
        
        // Re-init Select2 for the new row
        newRow.find('.select2-container').remove();
        $('#row-container').append(newRow);
        newRow.find('.select2').select2();
    });

    $(document).on('click', '.btn-remove-row', function() {
        $(this).closest('tr').remove();
        updateRowNumbers();
    });

    function updateRowNumbers() {
        $('.inspection-row').each(function(idx) {
            $(this).find('.row-number').text(idx + 1);
        });
    }

    // --- Camera & Compression Logic ---
    let currentCameraInput = null;
    let currentPreviewImg = null;
    let currentHiddenInput = null;
    let currentIcon = null;

    $(document).on('click', '.btn-camera', function() {
        const row = $(this).closest('tr');
        currentPreviewImg = $($(this).data('target'));
        currentHiddenInput = $($(this).data('input'));
        currentIcon = row.find('.preview-container i');
        row.find('.hidden-camera-input').trigger('click');
    });

    $(document).on('change', '.hidden-camera-input', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(event) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;

                // Max size 1200px
                if (width > height) {
                    if (width > 1200) {
                        height *= 1200 / width;
                        width = 1200;
                    }
                } else {
                    if (height > 1200) {
                        width *= 1200 / height;
                        height = 1200;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
                currentHiddenInput.val(dataUrl);
                currentPreviewImg.attr('src', dataUrl).show();
                currentIcon.hide();
            };
            img.src = event.target.result;
        };
        reader.readAsDataURL(file);
    });

    // --- Signature Pad Logic ---
    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)'
    });

    let activeSigInput = null;
    let activeSigPreview = null;

    $(document).on('click', '.btn-signature', function() {
        activeSigInput = $($(this).data('target'));
        activeSigPreview = $($(this).data('preview'));
        const type = $(this).data('type').toUpperCase();
        const rowNum = $(this).closest('tr').find('.row-number').text();
        
        $('#signatureTitle').text('Tanda Tangan ' + type + ' - Baris ' + rowNum);
        signaturePad.clear();
        $('#signatureModal').modal('show');
        
        // Resize canvas correctly for the modal
        setTimeout(resizeCanvas, 200);
    });

    $('#btn-clear-sig').on('click', function() {
        signaturePad.clear();
    });

    $('#btn-save-sig').on('click', function() {
        if (signaturePad.isEmpty()) {
            alert('Harap tanda tangan terlebih dahulu.');
            return;
        }

        const dataUrl = signaturePad.toDataURL('image/png');
        activeSigInput.val(dataUrl);
        activeSigPreview.attr('src', dataUrl).show();
        $('#signatureModal').modal('show'); // Ensure it stays until closed
        $('#signatureModal').modal('hide');
    });

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear();
    }

    window.onresize = resizeCanvas;
});
</script>
@endpush
