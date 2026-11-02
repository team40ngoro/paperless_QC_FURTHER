@extends('layouts.app')

<<<<<<< HEAD
=======
@php
    $assetPath = (isset($_SERVER['DOCUMENT_ROOT']) && realpath($_SERVER['DOCUMENT_ROOT']) === realpath(public_path())) ? '' : 'public/';
@endphp

>>>>>>> ac8f49ac640e5f509c42554777698a697f7fbb9e
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
                                            <label class="col-sm-3 col-form-label">Pemeriksaan Defect</label>
                                            <div class="col-sm-9">
                                                @php
                                                    $existingQty = old('sampling_defect_qty', $shoestring->sampling_defect_qty ?? []);
                                                    if (!is_array($existingQty)) { $existingQty = []; }
                                                @endphp

                                                <div class="card card-outline card-secondary" id="defect-qty-card" {{ $shoestring->nama_produsen ? '' : 'style="display:none;"' }}>
                                                    <div class="card-header bg-light py-2">
                                                        <h6 class="card-title text-primary m-0" style="font-size:0.95rem; font-weight:600;">
                                                            <i class="fas fa-exclamation-triangle"></i> Daftar Item Defect
                                                        </h6>
                                                    </div>
                                                    <div class="card-body py-2 px-3" id="defect-qty-container">
                                                        @if(!empty($existingQty))
                                                            @php
                                                                // Load DataDefect record untuk produsen ini sekali saja
                                                                $produsenDefectRecord = \App\Models\DataDefect::where('nama_produsen', $shoestring->nama_produsen)->first();
                                                                $detailDefectAll = $produsenDefectRecord ? ($produsenDefectRecord->detail_defect ?? []) : [];
                                                            @endphp
                                                            {{-- Header kolom --}}
                                                            <div class="row no-gutters mb-2 px-1 border-bottom pb-1">
                                                                <div class="col-3" style="font-size:0.75rem;font-weight:700;color:#555;">
                                                                    <i class="fas fa-folder mr-1 text-info"></i> Kategori
                                                                </div>
                                                                <div class="col-3" style="font-size:0.75rem;font-weight:700;color:#555;">Jenis</div>
                                                                <div class="col-2" style="font-size:0.75rem;font-weight:700;color:#555;">Sub Jenis</div>
                                                                <div class="col-2" style="font-size:0.75rem;font-weight:700;color:#555;">Spesifikasi</div>
                                                                <div class="col-2 text-center" style="font-size:0.75rem;font-weight:700;color:#555;">Qty</div>
                                                            </div>
                                                            @foreach($existingQty as $defectKey => $defectVal)
                                                                @php
                                                                    $dKategori = '-';
                                                                    $dJenis    = $defectKey;
                                                                    $dSubJenis = '-';
                                                                    $dSpec     = '-';

                                                                    if (is_numeric($defectKey)) {
                                                                        // Data lama (ID numeric)
                                                                        $oldDefect = \App\Models\DataDefect::find($defectKey);
                                                                        if ($oldDefect) {
                                                                            foreach ($oldDefect->detail_defect ?? [] as $katItem) {
                                                                                $dKategori = $katItem['kategori'] ?? '-';
                                                                                if (!empty($katItem['items'])) {
                                                                                    $dJenis    = $katItem['items'][0]['jenis'] ?? $defectKey;
                                                                                    $dSubJenis = $katItem['items'][0]['sub_jenis'] ?? '-';
                                                                                    $dSpec     = $katItem['items'][0]['spesifikasi'] ?? '-';
                                                                                }
                                                                            }
                                                                        }
                                                                    } else {
                                                                        // Data baru (string "Jenis - Spesifikasi")
                                                                        $parts     = explode(' - ', $defectKey, 2);
                                                                        $jenisPart = trim($parts[0]);
                                                                        $specPart  = isset($parts[1]) ? trim($parts[1]) : null;
                                                                        $dJenis    = $jenisPart;
                                                                        $dSpec     = $specPart ?? '-';

                                                                        // Cari kategorinya dari detail_defect
                                                                        foreach ($detailDefectAll as $katItem) {
                                                                            foreach ($katItem['items'] ?? [] as $dItem) {
                                                                                if (strtolower(trim($dItem['jenis'] ?? '')) === strtolower($jenisPart)) {
                                                                                    if ($specPart === null || strtolower(trim($dItem['spesifikasi'] ?? '')) === strtolower($specPart)) {
                                                                                        $dKategori = $katItem['kategori'] ?? '-';
                                                                                        $dSubJenis = $dItem['sub_jenis'] ?? '-';
                                                                                        break 2;
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                @endphp
                                                                <div class="row no-gutters align-items-center mb-2 px-1">
                                                                    <div class="col-3 text-secondary pr-1" style="font-size:0.85rem;line-height:1.3;">
                                                                        <strong>{{ $dKategori }}</strong>
                                                                    </div>
                                                                    <div class="col-3 text-dark pr-1" style="font-size:0.85rem;line-height:1.3;">{{ $dJenis }}</div>
                                                                    <div class="col-2 text-secondary pr-1" style="font-size:0.85rem;line-height:1.3;">{{ $dSubJenis !== '-' && $dSubJenis ? $dSubJenis : '-' }}</div>
                                                                    <div class="col-2 pr-1" style="font-size:0.85rem;line-height:1.3;">
                                                                        @if($dSpec && $dSpec !== '-')
                                                                            <span class="badge badge-success px-1" style="font-size:0.75rem;">{{ $dSpec }}</span>
                                                                        @else
                                                                            <span class="text-muted">-</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <input type="text"
                                                                               class="form-control form-control-sm defect-qty-input"
                                                                               name="sampling_defect_qty[{{ $defectKey }}]"
                                                                               placeholder="0"
                                                                               value="{{ $defectVal }}"
                                                                               style="border-radius:4px;text-align:center;">
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="text-muted"><i class="fas fa-info-circle"></i> Pilih produsen untuk memuat daftar defect.</div>
                                                        @endif
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

    // ─── AJAX load defect on producer change ───────────────────────────────
    function loadDefectByProdusen(produsenVal, existingQty) {
        const $container = $('#defect-qty-container');
        const $card = $('#defect-qty-card');

        if (!produsenVal) {
            $container.html('<div class="text-muted"><i class="fas fa-info-circle"></i> Pilih produsen untuk memuat daftar defect.</div>');
            $card.hide();
            updateTotalDefect();
            return;
        }

        $container.html('<div class="text-muted"><i class="fas fa-spinner fa-spin"></i> Memuat data defect...</div>');
        $card.show();

        $.ajax({
            url: '{{ route("ajax.get-defect-by-produsen") }}',
            type: 'GET',
            data: { nama_produsen: produsenVal },
            success: function(response) {
                if (response.success && response.detail_defect && response.detail_defect.length > 0) {
                    let html = '';
                    response.detail_defect.forEach(cat => {
                        html += `<div class="defect-category-section mb-3">
                            <h6 class="font-weight-bold text-secondary border-bottom pb-1 mb-2">
                                <i class="fas fa-folder-open"></i> Kategori: ${cat.kategori}
                            </h6>
                            <div class="pl-1">`;
                        if (cat.items && cat.items.length > 0) {
                            // Header row
                            html += `
                                <div class="row no-gutters mb-1 px-1">
                                    <div class="col-4" style="font-size:0.75rem;font-weight:600;color:#888;">Jenis</div>
                                    <div class="col-3" style="font-size:0.75rem;font-weight:600;color:#888;">Sub Jenis</div>
                                    <div class="col-3" style="font-size:0.75rem;font-weight:600;color:#888;">Spesifikasi</div>
                                    <div class="col-2 text-center" style="font-size:0.75rem;font-weight:600;color:#888;">Qty</div>
                                </div>
                            `;
                            cat.items.forEach(item => {
                                const label = item.spesifikasi ? `${item.jenis} - ${item.spesifikasi}` : `${item.jenis}`;
                                const savedVal = (existingQty && existingQty[label] !== undefined) ? existingQty[label] : '';
                                const sub_jenis_display = item.sub_jenis || '-';
                                const spesifikasi_display = item.spesifikasi || '-';
                                html += `<div class="row no-gutters align-items-center mb-2 px-1">
                                    <div class="col-4 text-dark pr-1" style="font-size:0.85rem;line-height:1.3;">${item.jenis}</div>
                                    <div class="col-3 text-secondary pr-1" style="font-size:0.85rem;line-height:1.3;">${sub_jenis_display}</div>
                                    <div class="col-3 text-secondary pr-1" style="font-size:0.85rem;line-height:1.3;">${spesifikasi_display}</div>
                                     <div class="col-2">
                                         <input type="text"
                                                class="form-control form-control-sm defect-qty-input"
                                                name="sampling_defect_qty[${label}]"
                                                placeholder="0"
                                                value="${savedVal}"
                                                style="border-radius:4px;text-align:center;">
                                     </div>
                                </div>`;
                            });
                        } else {
                            html += `<div class="text-muted font-italic pl-2" style="font-size:0.85rem;">Tidak ada item defect.</div>`;
                        }
                        html += `</div></div>`;
                    });
                    $container.html(html);
                } else {
                    $container.html('<div class="text-warning"><i class="fas fa-exclamation-circle"></i> Tidak ada data defect untuk produsen ini.</div>');
                }
                updateTotalDefect();
            },
            error: function() {
                $container.html('<div class="text-danger"><i class="fas fa-times-circle"></i> Gagal memuat data defect.</div>');
                updateTotalDefect();
            }
        });
    }

    // Existing qty data from PHP (untuk pre-fill saat produsen dipilih ulang)
    const existingQty = @json($existingQty ?? []);

    // ─── On page load — auto-load if produsen already selected ─────────────
    const initProdusen = $('#nama_produsen').val();
    if (initProdusen) {
        // Jika sudah ada data lama di container, cukup tampilkan card tanpa ajax
        const hasExistingData = Object.keys(existingQty).length > 0;
        if (hasExistingData) {
            $('#defect-qty-card').show();
            updateTotalDefect();
        } else {
            loadDefectByProdusen(initProdusen, existingQty);
        }
    }

    // ─── When producer changes ─────────────────────────────────────────────
    $('#nama_produsen').on('change', function() {
        loadDefectByProdusen($(this).val(), {});
    });

    // ─── Total defect calculation ──────────────────────────────────────────
    function updateTotalDefect() {
        let total = 0;
        $('.defect-qty-input').each(function() {
            const v = $(this).val();
            if (v !== null && v !== '' && !isNaN(v)) {
                total += parseFloat(v);
            }
        });
        $('#total_defect').val(total);
    }

    $(document).on('input', '.defect-qty-input', function() {
        updateTotalDefect();
    });

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