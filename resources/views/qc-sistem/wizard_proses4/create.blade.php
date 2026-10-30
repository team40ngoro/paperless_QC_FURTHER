@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-magic text-primary mr-2"></i>Verifikasi Proses Tumbling</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tumbling & Aging</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible shadow-sm border-0">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="icon fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible shadow-sm border-0">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="icon fas fa-exclamation-triangle"></i> {{ session('error') }}
                </div>
            @endif

            <div class="wizard-navigation mb-4">
                <div class="wizard-progress-bar"></div>
                <div class="d-flex justify-content-between position-relative" style="z-index: 2;">
                    @php
                        $steps = [
                            1 => ['icon' => 'fa-seedling', 'label' => 'Bahan Baku'],
                            2 => ['icon' => 'fa-sync', 'label' => 'Tumbling'],
                            3 => ['icon' => 'fa-hourglass-half', 'label' => 'Aging']
                        ];
                    @endphp
                    @foreach($steps as $num => $s)
                        <div class="step-item text-center {{ $num == 1 ? 'active' : '' }}" id="indicator-{{ $num }}">
                            <div class="step-icon shadow-sm mx-auto mb-2">
                                <i class="fas {{ $s['icon'] }}"></i>
                            </div>
                            <span class="step-label d-none d-md-block font-weight-bold">{{ $s['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="wizardAlert" class="alert d-none shadow-sm"></div>

            @php
                $now = \Carbon\Carbon::now('Asia/Jakarta');
                $fullTimestamp = $now->format('d-m-Y H:i:s');
            @endphp

            {{-- ======================== STEP 1 ======================== --}}
            <div class="wizard-step active" id="step-1">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">Langkah 1: Input Bahan Baku</h3>
                    </div>
                    <form id="form-step-1">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Tanggal *</label>
                                    @php $dateValue = $now->format('Y-m-d'); @endphp
                                    <input type="date" name="tanggal" class="form-control"
                                        value="{{ isset($draftData) ? \Carbon\Carbon::parse($draftData->tanggal)->format('Y-m-d') : $dateValue }}"
                                        required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Jam *</label>
                                    <input type="time" name="jam" class="form-control"
                                        value="{{ $draftData->jam ?? $now->format('H:i') }}" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Shift *</label>
                                    <select name="shift_id" class="form-control select2-custom" required>
                                        <option value="">Pilih Shift</option>
                                        @foreach($shifts as $s)
                                            <option value="{{ $s->id }}"
                                                {{ (isset($draftData) && $draftData->shift_id == $s->id) ? 'selected' : '' }}>
                                                {{ $s->shift }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Nama Produk *</label>
                                    <select name="id_produk" id="id_produk" class="form-control select2-custom" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach($produks as $p)
                                            <option value="{{ $p->id }}"
                                                {{ (isset($draftData) && $draftData->id_produk == $p->id) ? 'selected' : '' }}>
                                                {{ $p->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Gramase (gram)</label>
                                    <select name="gramase" id="nilai_select_berat_sampling" class="form-control" data-nilai-berat data-selected="{{ old('gramase', $draftData->gramase ?? '') }}"></select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Kode Produksi *</label>
                                    <input type="text" name="kode_produksi" id="kode_produksi" class="form-control"
                                        placeholder="BN123xx" value="{{ $draftData->kode_produksi ?? '' }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Nomor Formula *</label>
                                    <select name="nomor_formula_non_forming" id="nomor_formula_non_forming"
                                        class="form-control" required disabled>
                                        <option value="">Pilih Produk Dahulu</option>
                                    </select>
                                </div>
                            </div>

                            <div id="bahanContainer" class="mt-4" style="display:none;">
                                <h6 class="font-weight-bold border-bottom pb-2 mb-3">
                                    <i class="fas fa-list mr-2"></i>Daftar Bahan Non-Forming
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm table-hover" id="tableBahan">
                                        <thead class="bg-light text-center">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Nama RM</th>
                                                <th width="25%">Berat (kg)</th>
                                                <th width="20%">Kode Produksi</th>
                                                <th width="12%">Suhu (°C)</th>
                                                <th width="10%">Kondisi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bahanBody"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="form-group col-md-3">
                                    <label>Salinity</label>
                                    <input type="text" name="salinity" class="form-control"
                                        value="{{ $draftData->salinity ?? '' }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Brix</label>
                                    <input type="text" name="viskositas" class="form-control"
                                        value="{{ $draftData->viskositas ?? '' }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Hasil Pencampuran</label>
                                    <select name="hasil_pencampuran" class="form-control">
                                        <option value="✓"
                                            {{ (isset($draftData) && $draftData->hasil_pencampuran == '✓') ? 'selected' : '' }}>
                                            ✓ OK
                                        </option>
                                        <option value="✘"
                                            {{ (isset($draftData) && $draftData->hasil_pencampuran == '✘') ? 'selected' : '' }}>
                                            ✘ Tidak OK
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Keterangan</label>
                                    <textarea name="keterangan" class="form-control" rows="1">{{ $draftData->keterangan ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="button" class="btn btn-primary next-step" data-step="1">
                                Simpan & Lanjut <i class="fas fa-chevron-right ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ======================== STEP 2 ======================== --}}
            <div class="wizard-step" id="step-2">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold">Langkah 2: Parameter Tumbling</h3>
                        <span class="badge badge-info info-badge"></span>
                    </div>
                    <form id="form-step-2">
                        <input type="hidden" name="bahan_baku_uuid" id="p2_bahan_uuid">
                        <input type="hidden" id="p2_id_produk" name="id_produk">
                        <input type="hidden" id="p2_shift_id" name="shift_id">
                        <input type="hidden" id="p2_kode_produksi" name="kode_produksi">
                        <input type="hidden" id="p2_tanggal" name="tanggal">
                        <input type="hidden" id="p2_jam" name="jam">
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <label class="font-weight-bold mb-3">
                                                <i class="fas fa-cog mr-2"></i>Pilih Jenis Mesin Tumbling yang Digunakan
                                            </label>
                                            <div class="row" id="tumbling-type-selector">
                                                {{-- populated dynamically --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="tumblingInputs">
                                <div class="text-center py-5">
                                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-default prev-step" data-step="2">
                                <i class="fas fa-chevron-left mr-2"></i>Kembali
                            </button>
                            <div>
                                <button type="button" class="btn btn-success save-finish-step-2 mr-2">
                                    <i class="fas fa-check-double mr-2"></i>Simpan & Selesai
                                </button>
                                <button type="button" class="btn btn-primary next-step" data-step="2">
                                    Simpan & Lanjut Aging <i class="fas fa-chevron-right ml-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ======================== STEP 3 ======================== --}}
            <div class="wizard-step" id="step-3">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">Langkah 3: Proses Aging</h3>
                    </div>
                    <form id="form-step-3">
                        <input type="hidden" name="proses_tumbling_uuid" id="p3_tumbling_uuid">
                        <input type="hidden" name="id_produk" id="p3_id_produk">
                        <input type="hidden" name="jam" id="p3_jam">
                        <input type="hidden" name="tanggal" id="p3_tanggal">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <label class="font-weight-bold mb-3">Data Suhu, Kondisi & Waktu Mulai Aging</label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm" id="table-suhu-kondisi">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th width="30%">Waktu Mulai Aging</th>
                                                    <th width="30%">Suhu Produk (°C)</th>
                                                    <th width="20%">Kondisi Produk</th>
                                                    <th width="15%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-suhu-kondisi">
                                                <tr class="suhu-kondisi-row">
                                                    <td class="text-center row-number">1</td>
                                                    <td>
                                                        <input type="text" name="waktu_mulai_aging[]"
                                                            class="form-control form-control-sm"
                                                            placeholder="Contoh: 08:00" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="any" name="suhu_produk[]"
                                                            class="form-control form-control-sm"
                                                            placeholder="Contoh: 5.5" required>
                                                    </td>
                                                    <td>
                                                        <select name="kondisi_produk[]"
                                                            class="form-control form-control-sm" required>
                                                            <option value="✓">✓ OK</option>
                                                            <option value="✘">✘ Tidak OK</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-success btn-sm btn-add-suhu">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card bg-light shadow-sm border-success">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="form-group col-md-6 offset-md-3 mb-0">
                                                    <label class="font-weight-bold text-success">
                                                        <i class="fas fa-clock"></i> Waktu Selesai Aging
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="waktu_selesai_aging"
                                                        class="form-control" placeholder="Contoh: 16:00" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-default prev-step" data-step="3">
                                <i class="fas fa-chevron-left mr-2"></i>Kembali
                            </button>
                            <button type="button" class="btn btn-success next-step" data-step="3">
                                Selesai & Simpan <i class="fas fa-check-double ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section>
</div>

<style>
    .wizard-step { display: none; }
    .wizard-step.active { display: block; animation: fadeIn 0.4s ease-in-out; }

    .wizard-navigation { position: relative; padding: 20px 0; }
    .wizard-progress-bar {
        position: absolute; top: 45px; left: 5%; right: 5%;
        height: 3px; background: #e9ecef; z-index: 1;
    }

    .step-icon {
        width: 50px; height: 50px; border-radius: 50%;
        background: #fff; border: 2px solid #dee2e6;
        display: flex; align-items: center; justify-content: center;
        color: #ced4da; transition: all 0.3s;
    }
    .step-item.active .step-icon  { background: #007bff; border-color: #007bff; color: #fff; transform: scale(1.15); }
    .step-item.completed .step-icon { background: #28a745; border-color: #28a745; color: #fff; }
    .step-label { font-size: 13px; color: #6c757d; }
    .step-item.active .step-label { color: #007bff; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .table-sm td { vertical-align: middle !important; }
    .select2-custom { width: 100% !important; }
</style>

<div id="loadingOverlay"
    style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;
           background:rgba(0,0,0,.4);z-index:9999;align-items:center;justify-content:center;">
    <div class="text-center text-white">
        <div class="spinner-border text-primary mb-2" role="status"></div>
        <h5 id="loadingMessage">Mohon Tunggu...</h5>
    </div>
</div>
<style>
    #loadingOverlay.show { display: flex !important; }
</style>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // Set CSRF token globally for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const STORAGE_KEY = 'wizard_proses4_state_v1';

    // ── Satu-satunya deklarasi variabel global wizard ──
    let tumblingDataGlobal = [];
    let selectedMachines  = {};

    const state = {
        current_step          : 1,
        produk_id             : null,
        produk_nama           : '',
        kode_produksi         : '',
        bahan_uuid            : null,
        proses_tumbling_uuid  : null,
        shift_id              : null,
        tanggal               : null,
        jam                   : null,
        step1_form            : null,
        step2_form            : null,
        step3_form            : null,
        user_id               : "{{ Auth::id() }}"
    };

    // Restore session
    try {
        const saved = sessionStorage.getItem(STORAGE_KEY);
        if (saved) {
            const parsed = JSON.parse(saved);
            if (parsed && typeof parsed === 'object') {
                if (parsed.user_id && parsed.user_id !== state.user_id) {
                    sessionStorage.removeItem(STORAGE_KEY);
                } else {
                    Object.assign(state, parsed);
                }
            }
        }
    } catch (_) {}

    // Restore dari server-side draft
    @if(isset($draftData) && $draftData)
        state.bahan_uuid    = "{{ $draftData->uuid }}";
        state.produk_id     = "{{ $draftData->id_produk }}";
        state.produk_nama   = "{{ $draftData->produk->nama_produk ?? '' }}";
        state.kode_produksi = "{{ $draftData->kode_produksi }}";
        state.shift_id      = "{{ $draftData->shift_id }}";
        state.tanggal       = "{{ \Carbon\Carbon::parse($draftData->tanggal)->format('d-m-Y H:i:s') }}";
        state.jam           = "{{ $draftData->jam }}";

        @php
            $tumbling = \App\Models\ProsesTumbling::where('bahan_baku_tumbling_uuid', $draftData->uuid)->first();
            $aging    = $tumbling ? \App\Models\ProsesAging::where('proses_tumbling_uuid', $tumbling->uuid)->first() : null;
        @endphp

        @if($aging || $tumbling)
            state.proses_tumbling_uuid = "{{ $tumbling->uuid }}";
            state.current_step = 3;
        @else
            state.current_step = 2;
        @endif

        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    @endif

    // ── Helpers ──────────────────────────────────────────────────────────────

    function persistState() {
        try { sessionStorage.setItem(STORAGE_KEY, JSON.stringify(state)); } catch (_) {}
    }

    function clearState() {
        try { sessionStorage.removeItem(STORAGE_KEY); } catch (_) {}
    }

    function showLoading(msg) {
        $('#loadingMessage').text(msg || 'Mohon Tunggu...');
        $('#loadingOverlay').addClass('show');
    }

    function hideLoading() {
        $('#loadingOverlay').removeClass('show');
    }

    function showAlertV3(type, title, msg) {
        const alertClass = (type === 'danger' || type === 'error')
            ? 'alert-danger'
            : (type === 'warning' ? 'alert-warning' : (type === 'info' ? 'alert-info' : 'alert-success'));
        const iconClass = (type === 'danger' || type === 'error')
            ? 'fa-exclamation-triangle'
            : (type === 'warning' ? 'fa-exclamation-circle' : (type === 'info' ? 'fa-info-circle' : 'fa-check-circle'));

        $('#wizardAlert')
            .attr('class', `alert ${alertClass} alert-dismissible shadow-sm border-0`)
            .html(`
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="icon fas ${iconClass}"></i> ${msg}
            `)
            .removeClass('d-none');

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function updateWizardUI(step) {
        $('.wizard-step').removeClass('active');
        $(`#step-${step}`).addClass('active');
        $('.step-item').removeClass('active completed');
        for (let i = 1; i <= step; i++) {
            $(`#indicator-${i}`).addClass(i === step ? 'active' : 'completed');
        }
        state.current_step = step;

        if (step >= 2) {
            $('#p2_id_produk').val(state.produk_id);
            $('#p2_bahan_uuid').val(state.bahan_uuid);
            $('#p2_shift_id').val(state.shift_id);
            $('#p2_kode_produksi').val(state.kode_produksi);
            $('#p2_tanggal').val(state.tanggal);
            $('#p2_jam').val(state.jam);
        }
        if (step >= 3) {
            $('#p3_id_produk').val(state.produk_id);
            $('#p3_tanggal').val(state.tanggal);
            $('#p3_jam').val(state.jam);
            $('#p3_tumbling_uuid').val(state.proses_tumbling_uuid);
        }

        persistState();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateRequired(step) {
        const $scope = $(`#step-${step}`);
        let ok = true;

        $scope.find('.is-invalid').removeClass('is-invalid');

        $scope.find('input[required], select[required], textarea[required]').each(function () {
            const $el = $(this);
            if ($el.is(':disabled')) return;
            const val = $el.val();
            if (val === null || val === undefined || String(val).trim() === '') {
                $el.addClass('is-invalid');
                ok = false;
            }
        });

        if (step === 1) {
            if ($('#bahanBody tr').length === 0) {
                showAlertV3('warning', 'Peringatan', 'Harap lengkapi daftar bahan baku terlebih dahulu.');
                return false;
            }
        }

        if (step === 2) {
            if ($('#tumblingInputs .card').length === 0) {
                showAlertV3('warning', 'Peringatan', 'Data parameter tumbling belum dimuat. Silakan refresh halaman.');
                return false;
            }
            if (!$('#p2_bahan_uuid').val()) {
                showAlertV3('warning', 'Peringatan', 'Data bahan baku tidak ditemukan. Silakan kembali ke Step 1.');
                return false;
            }
        }

        if (!ok) {
            showAlertV3('warning', 'Peringatan', 'Harap isi semua field bertanda bintang (*) atau yang diperlukan.');
        }

        return ok;
    }

    function restoreForm($form, dataArr) {
        if (!dataArr || !Array.isArray(dataArr)) return;
        dataArr.forEach(item => {
            const $field = $form.find(`[name="${item.name}"]`);
            if (!$field.length) return;
            if ($field.attr('type') === 'radio') {
                $form.find(`[name="${item.name}"][value="${item.value}"]`).prop('checked', true);
                return;
            }
            $field.val(item.value);
        });
    }

    // ── Step 1: Product & Formula ─────────────────────────────────────────────

    $('#id_produk').on('change', function () {
        const id = $(this).val();
        const $formula = $('#nomor_formula_non_forming');

        if (!id) {
            $formula.prop('disabled', true).html('<option value="">Pilih Produk Dahulu</option>');
            return;
        }

        state.produk_id   = id;
        state.produk_nama = $(this).find('option:selected').text();

        $formula.prop('disabled', false).html('<option value="">Loading...</option>');

        $.get(`{{ url("/qc-sistem/ajax/formula-non-forming") }}/${id}`)
            .done(data => {
                let html = '<option value="">Pilih Formula</option>';
                data.forEach(f => html += `<option value="${f.id}">${f.nomor_formula || f.id}</option>`);
                $formula.html(html);
            });
    });

    $('#nomor_formula_non_forming').on('change', function () {
        const fId = $(this).val();
        if (!fId) return $('#bahanContainer').hide();

        $.get(`{{ url("/qc-sistem/ajax/bahan-non-forming-list") }}/${fId}`)
            .done(data => {
                const $body = $('#bahanBody').empty();
                data.forEach((b, i) => {
                    $body.append(`
                        <tr class="row-bahan" data-nama="${b.nama_rm}">
                            <td class="text-center">${i + 1}</td>
                            <td>${b.nama_rm}</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <select class="form-control col-4 source-select" data-db="${b.berat_rm}">
                                        <option value="db">Master</option>
                                        <option value="manual">Manual</option>
                                    </select>
                                    <input type="number" step="any" class="form-control weight-input"
                                        value="${b.berat_rm}" readonly required>
                                </div>
                            </td>
                            <td><input type="text" class="form-control form-control-sm batch-input"
                                placeholder="Kode Produksi" required></td>
                            <td><input type="number" step="any" class="form-control form-control-sm temp-input"
                                placeholder="°C"></td>
                            <td>
                                <select class="form-control form-control-sm cond-input">
                                    <option value="✓">✓</option>
                                    <option value="✘">✘</option>
                                </select>
                            </td>
                        </tr>
                    `);
                });
                $('#bahanContainer').fadeIn();
            });
    });

    $(document).on('change', '.source-select', function () {
        const $row     = $(this).closest('tr');
        const isManual = $(this).val() === 'manual';
        $row.find('.weight-input')
            .prop('readonly', !isManual)
            .val(isManual ? '' : $(this).data('db'))
            .focus();
    });

    // ── Step 2: Load Tumbling Fields ──────────────────────────────────────────

    function loadTumblingFields() {
        showLoading('Memuat Parameter Tumbling...');

        return $.ajax({
            url      : `{{ url('qc-sistem/get-tumbling-by-product') }}/${state.produk_id}`,
            type     : 'GET',
            dataType : 'json'
        })
        .done(data => {
            tumblingDataGlobal = data;

            if (data.length > 0) {
                renderTumblingTypeSelector(data);
                renderTumblingForms();
            } else {
                $('#tumblingInputs').html(`
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Tidak ada data master parameter tumbling untuk produk ini.
                    </div>`);
            }
        })
        .fail(() => {
            $('#tumblingInputs').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Gagal memuat parameter tumbling.
                </div>`);
        })
        .always(() => hideLoading());
    }

    function renderTumblingTypeSelector(data) {
        const $selector = $('#tumbling-type-selector').empty();

        data.forEach((machine, index) => {
            if (!selectedMachines[index]) selectedMachines[index] = 'both';

            $selector.append(`
                <div class="col-md-12 mb-3">
                    <div class="card border-primary">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 font-weight-bold text-primary">
                                    <i class="fas fa-cogs mr-2"></i>${machine.mesin || 'Mesin ' + (index + 1)}
                                </h6>
                                <div style="min-width: 220px;">
                                    <select class="form-control tumbling-type-select" data-index="${index}">
                                        <option value="vakum"     ${selectedMachines[index] === 'vakum'     ? 'selected' : ''}>🌀 Vakum</option>
                                        <option value="non_vakum" ${selectedMachines[index] === 'non_vakum' ? 'selected' : ''}>💨 Non Vakum</option>
                                        <option value="both"      ${selectedMachines[index] === 'both'      ? 'selected' : ''}>✅ All</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        });

        $selector.find('.tumbling-type-select').on('change', function () {
            const machineIndex = parseInt($(this).data('index'));
            selectedMachines[machineIndex] = $(this).val();
            renderTumblingForms();
        });
    }

    function renderTumblingForms() {
        const $cont = $('#tumblingInputs').empty();
        let html = '<div class="row">';

        tumblingDataGlobal.forEach((v, k) => {
            const type  = selectedMachines[k] || 'both';
            const showV  = type === 'vakum'     || type === 'both';
            const showNV = type === 'non_vakum' || type === 'both';
            const col    = type === 'both' ? 'col-md-6' : 'col-md-12';

            html += `<div class="col-12 mb-4"><div class="row">`;

            // ── Vakum card ──
            if (showV) {
                html += `
                <div class="${col}">
                    <div class="card card-outline card-warning h-100">
                        <div class="card-header bg-warning">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-wind mr-2"></i>Tumbling Vakum - ${v.mesin || '-'}
                            </h3>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="tumbling_data[${k}][id_tumbling]" value="${v.id}">
                            <input type="hidden" name="tumbling_data[${k}][use_vakum]" value="1">
                            <div class="row mb-2">
                                <div class="col-6 text-center">
                                    <span class="badge badge-secondary w-100 py-2">STANDART</span>
                                </div>
                                <div class="col-6 text-center">
                                    <span class="badge badge-success w-100 py-2">AKTUAL</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Drum On</label>
                                        <div class="row">
                                            <div class="col-6"><input type="text" class="form-control bg-light" value="${v.drum_on || '-'}" readonly></div>
                                            <div class="col-6"><input type="text" class="form-control border-primary" name="tumbling_data[${k}][aktual_drum_on]" placeholder="Aktual *" required></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Drum Off</label>
                                        <div class="row">
                                            <div class="col-6"><input type="text" class="form-control bg-light" value="${v.drum_off || '-'}" readonly></div>
                                            <div class="col-6"><input type="text" class="form-control border-primary" name="tumbling_data[${k}][aktual_drum_off]" placeholder="Aktual *" required></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Speed</label>
                                        <div class="row">
                                            <div class="col-6"><input type="text" class="form-control bg-light" value="${v.drum_speed || '-'}" readonly></div>
                                            <div class="col-6"><input type="text" class="form-control border-primary" name="tumbling_data[${k}][aktual_speed]" placeholder="Aktual *" required></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Total Waktu</label>
                                        <div class="row">
                                            <div class="col-6"><input type="text" class="form-control bg-light" value="${v.total_waktu || '-'}" readonly></div>
                                            <div class="col-6"><input type="text" class="form-control border-primary" name="tumbling_data[${k}][aktual_total_waktu]" placeholder="Aktual *" required></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Tekanan Vakum</label>
                                        <div class="row">
                                            <div class="col-6"><input type="text" class="form-control bg-light" value="${v.tekanan_vakum || '-'}" readonly></div>
                                            <div class="col-6"><input type="text" class="form-control border-primary" name="tumbling_data[${k}][aktual_vakum]" placeholder="Aktual *" required></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mulai Tumbling *</label>
                                        <input type="time" class="form-control border-primary" name="tumbling_data[${k}][waktu_mulai_tumbling]" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Selesai Tumbling *</label>
                                        <input type="time" class="form-control border-primary" name="tumbling_data[${k}][waktu_selesai_tumbling]" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            }

            // ── Non-Vakum card ──
            if (showNV) {
                html += `
                <div class="${col}">
                    <div class="card card-outline card-info h-100">
                        <div class="card-header bg-info">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-fan mr-2"></i>Tumbling Non Vakum - ${v.mesin || '-'}
                            </h3>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="tumbling_data[${k}][use_non_vakum]" value="1">
                            <div class="row mb-2">
                                <div class="col-6 text-center">
                                    <span class="badge badge-secondary w-100 py-2">STANDART</span>
                                </div>
                                <div class="col-6 text-center">
                                    <span class="badge badge-success w-100 py-2">AKTUAL</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Drum On</label>
                                        <div class="row">
                                            <div class="col-6"><input type="text" class="form-control bg-light" value="${v.drum_on_non_vakum || '-'}" readonly></div>
                                            <div class="col-6"><input type="text" class="form-control border-info" name="tumbling_data[${k}][aktual_drum_on_non_vakum]" placeholder="Aktual *" required></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Drum Off</label>
                                        <div class="row">
                                            <div class="col-6"><input type="text" class="form-control bg-light" value="${v.drum_off_non_vakum || '-'}" readonly></div>
                                            <div class="col-6"><input type="text" class="form-control border-info" name="tumbling_data[${k}][aktual_drum_off_non_vakum]" placeholder="Aktual *" required></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Speed</label>
                                        <div class="row">
                                            <div class="col-6"><input type="text" class="form-control bg-light" value="${v.drum_speed_non_vakum || '-'}" readonly></div>
                                            <div class="col-6"><input type="text" class="form-control border-info" name="tumbling_data[${k}][aktual_speed_non_vakum]" placeholder="Aktual *" required></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Total Waktu</label>
                                        <div class="row">
                                            <div class="col-6"><input type="text" class="form-control bg-light" value="${v.total_waktu_non_vakum || '-'}" readonly></div>
                                            <div class="col-6"><input type="text" class="form-control border-info" name="tumbling_data[${k}][aktual_total_waktu_non_vakum]" placeholder="Aktual *" required></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Tekanan Non Vakum</label>
                                        <div class="row">
                                            <div class="col-6"><input type="text" class="form-control bg-light" value="${v.tekanan_non_vakum || '-'}" readonly></div>
                                            <div class="col-6"><input type="text" class="form-control border-info" name="tumbling_data[${k}][aktual_tekanan_non_vakum]" placeholder="Aktual *" required></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mulai Tumbling *</label>
                                        <input type="time" class="form-control border-info" name="tumbling_data[${k}][waktu_mulai_tumbling_non_vakum]" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Selesai Tumbling *</label>
                                        <input type="time" class="form-control border-info" name="tumbling_data[${k}][waktu_selesai_tumbling_non_vakum]" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            }

            // ── Hasil Akhir card ──
            html += `</div>
            <div class="card card-outline card-success mt-3">
                <div class="card-header bg-success">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-clipboard-check mr-2"></i>Hasil Akhir - ${v.mesin || '-'}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Suhu Hasil Tumbling *</label>
                                <input type="text" class="form-control"
                                    name="tumbling_data[${k}][suhu_hasil_tumbling]" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sensori Hasil *</label>
                                <div class="d-flex mt-2">
                                    <div class="custom-control custom-radio mr-3">
                                        <input class="custom-control-input" type="radio"
                                            id="so_${k}" name="tumbling_data[${k}][sensori_hasil_tumbling]"
                                            value="oke" checked>
                                        <label for="so_${k}" class="custom-control-label text-success">✓ Oke</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio"
                                            id="sn_${k}" name="tumbling_data[${k}][sensori_hasil_tumbling]"
                                            value="tidak ok">
                                        <label for="sn_${k}" class="custom-control-label text-danger">✗ Tidak Ok</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>`;
        });

        html += '</div>';
        $cont.html(html);
    }

    // ── Navigation ────────────────────────────────────────────────────────────

    $('.next-step').on('click', function () {
        const current = parseInt($(this).data('step'));
        if (current === 1) { if (!validateRequired(1)) return; submitStep1(); }
        else if (current === 2) { if (!validateRequired(2)) return; submitStep2(); }
        else if (current === 3) { if (!validateRequired(3)) return; submitStep3(); }
    });

    $('.save-finish-step-2').on('click', function () {
        if (!validateRequired(2)) return;
        submitStep2AndFinish();
    });

    $('.prev-step').on('click', function () {
        updateWizardUI(parseInt($(this).data('step')) - 1);
    });

    // ── Submit Step 1 ─────────────────────────────────────────────────────────

    function submitStep1() {
        const materials = [];
        $('.row-bahan').each(function () {
            materials.push({
                nama_bahan_baku          : $(this).data('nama'),
                jumlah                   : $(this).find('.weight-input').val(),
                kode_produksi_bahan_baku : $(this).find('.batch-input').val(),
                suhu                     : $(this).find('.temp-input').val(),
                kondisi_daging           : $(this).find('.cond-input').val()
            });
        });

        const formData = $('#form-step-1').serializeArray();
        formData.push({ name: 'manual_bahan_json', value: JSON.stringify(materials) });

        state.step1_form = $('#form-step-1').serializeArray();
        persistState();

        showLoading('Saving Bahan Baku Tumbling...');
        // Remove any existing _token entries (form already has @csrf, avoid duplicates)
        const cleanedFormData = formData.filter(item => item.name !== '_token');
        $.post(
            '{{ route("wizard.proses4.store-bahan-baku") }}',
            $.param(cleanedFormData)
        )
        .done(res => {
            hideLoading();
            if (res.success) {
                state.bahan_uuid    = res.uuid;
                state.kode_produksi = $('#kode_produksi').val();
                state.shift_id      = $('#form-step-1').find('[name="shift_id"]').val();
                state.tanggal       = $('#form-step-1').find('[name="tanggal"]').val();
                state.jam           = $('#form-step-1').find('[name="jam"]').val();
                persistState();

                $('.info-badge').text(`${state.produk_nama} | ${state.kode_produksi}`);
                $('#p2_bahan_uuid').val(res.uuid);
                $('#p2_id_produk').val(state.produk_id);
                $('#p2_shift_id').val(state.shift_id);
                $('#p2_kode_produksi').val(state.kode_produksi);
                $('#p2_tanggal').val(state.tanggal);
                $('#p2_jam').val(state.jam);
                $('#p3_id_produk').val(state.produk_id);
                $('#p3_tanggal').val(state.tanggal);
                $('#p3_jam').val(state.jam);

                loadTumblingFields().then(() => {
                    updateWizardUI(2);
                    showAlertV3('success', 'Berhasil', res.message || 'Data berhasil disimpan.');
                });
            }
        })
        .fail(xhr => {
            hideLoading();
            let msg = 'Tolong Lengkapi Pengisian Datanya.';
            if (xhr.status === 422 && xhr.responseJSON) {
                msg = xhr.responseJSON.message || msg;
                if (xhr.responseJSON.errors) {
                    msg += '\n- ' + Object.values(xhr.responseJSON.errors).flat().join('\n- ');
                }
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            showAlertV3('danger', 'Error', msg);
        });
    }

    // ── Helper: build filtered payload untuk Step 2 ──────────────────────────
    //
    // Masalah: ketika hanya memilih "vakum" atau "non_vakum", field lawan
    // tetap ada di DOM (tidak render) ATAU ter-disable → id_tumbling ikut
    // hilang. Solusi: kumpulkan semua field dari form, lalu FILTER secara
    // manual berdasarkan selectedMachines, tanpa menyentuh disabled sama sekali.
    //
    function buildStep2Payload(extraParams) {
        // Ambil field-field non-tumbling_data dari form (hidden inputs header)
        const base = {};
        $('#form-step-2').find('input[type="hidden"]').each(function () {
            const name = $(this).attr('name');
            // Abaikan field tumbling_data — kita bangun manual
            if (name && !name.startsWith('tumbling_data')) {
                base[name] = $(this).val();
            }
        });

        // Token CSRF — sent via X-CSRF-TOKEN header (set globally via $.ajaxSetup)

        // Tambahkan extra params (misal skip_aging=1)
        if (extraParams) {
            Object.assign(base, extraParams);
        }

        // Bangun array params ala serializeArray
        const params = [];
        Object.keys(base).forEach(k => params.push({ name: k, value: base[k] }));

        // Iterasi tiap mesin dan filter berdasarkan selectedMachines
        tumblingDataGlobal.forEach((v, k) => {
            const type    = selectedMachines[k] || 'both';
            const useV    = type === 'vakum'     || type === 'both';
            const useNV   = type === 'non_vakum' || type === 'both';
            const prefix  = `tumbling_data[${k}]`;
            const $scope  = $(`#tumblingInputs`);

            // Selalu sertakan id_tumbling (wajib, dari tumblingDataGlobal)
            const idVal = v.id;
            if (idVal) params.push({ name: `${prefix}[id_tumbling]`, value: idVal });

            // Hasil akhir (selalu disertakan)
            const suhu    = $scope.find(`[name="${prefix}[suhu_hasil_tumbling]"]`).val();
            const sensori = $scope.find(`[name="${prefix}[sensori_hasil_tumbling]"]:checked`).val()
                         || $scope.find(`[name="${prefix}[sensori_hasil_tumbling]"]`).first().val();
            params.push({ name: `${prefix}[suhu_hasil_tumbling]`,    value: suhu    || '' });
            params.push({ name: `${prefix}[sensori_hasil_tumbling]`, value: sensori || 'oke' });

            if (useV) {
                params.push({ name: `${prefix}[use_vakum]`, value: '1' });
                const vakumFields = [
                    'aktual_drum_on', 'aktual_drum_off', 'aktual_speed',
                    'aktual_total_waktu', 'aktual_vakum',
                    'waktu_mulai_tumbling', 'waktu_selesai_tumbling'
                ];
                vakumFields.forEach(f => {
                    const $field = $scope.find(`[name="${prefix}[${f}]"]`);
                    const val = $field.length ? $field.val() : '';
                    params.push({ name: `${prefix}[${f}]`, value: val || '' });
                });
            }

            if (useNV) {
                params.push({ name: `${prefix}[use_non_vakum]`, value: '1' });
                const nvFields = [
                    'aktual_drum_on_non_vakum', 'aktual_drum_off_non_vakum',
                    'aktual_speed_non_vakum',   'aktual_total_waktu_non_vakum',
                    'aktual_tekanan_non_vakum',
                    'waktu_mulai_tumbling_non_vakum', 'waktu_selesai_tumbling_non_vakum'
                ];
                nvFields.forEach(f => {
                    const $field = $scope.find(`[name="${prefix}[${f}]"]`);
                    const val = $field.length ? $field.val() : '';
                    params.push({ name: `${prefix}[${f}]`, value: val || '' });
                });
            }
        });

        const payload = $.param(params);
        console.log('Payload being sent:', payload);
        return payload;
    }

    function handleStep2Error(xhr) {
        hideLoading();
        console.log('Error Response:', xhr.responseJSON);
        let msg = 'Gagal menyimpan data. Silakan lengkapi semua field yang diperlukan.';
        if (xhr.status === 422 && xhr.responseJSON) {
            msg = xhr.responseJSON.message || msg;
            if (xhr.responseJSON.errors) {
                msg += '<br><ul class="text-left mt-2">';
                Object.keys(xhr.responseJSON.errors).forEach(key => {
                    xhr.responseJSON.errors[key].forEach(e => { msg += `<li>${e}</li>`; });
                });
                msg += '</ul>';
            }
        } else if (xhr.status === 500) {
            msg = 'Terjadi kesalahan server. Silakan coba lagi atau hubungi administrator.';
            if (xhr.responseJSON && xhr.responseJSON.message) msg += `<br><small>${xhr.responseJSON.message}</small>`;
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
        }
        showAlertV3('danger', 'Error', msg);
    }

    // ── Submit Step 2 (lanjut ke Aging) ──────────────────────────────────────

    function submitStep2() {
        state.step2_form = $('#form-step-2').serializeArray();
        persistState();

        showLoading('Menyimpan Data Proses Tumbling...');

        $.ajax({
            url      : '{{ route("wizard.proses4.store-proses-tumbling") }}',
            type     : 'POST',
            data     : buildStep2Payload(),
            dataType : 'json'
        })
        .done(res => {
            hideLoading();
            if (res.success) {
                state.proses_tumbling_uuid = res.uuid;
                $('#p3_tumbling_uuid').val(res.uuid);
                persistState();
                updateWizardUI(3);
                showAlertV3('success', 'Berhasil', res.message || 'Data Proses Tumbling berhasil disimpan.');
            } else {
                showAlertV3('danger', 'Gagal', res.message || 'Gagal menyimpan data.');
            }
        })
        .fail(xhr => handleStep2Error(xhr));
    }

    // ── Submit Step 2 (selesai, tanpa Aging) ─────────────────────────────────

    function submitStep2AndFinish() {
        state.step2_form = $('#form-step-2').serializeArray();
        persistState();

        showLoading('Menyimpan Data Proses Tumbling...');

        $.ajax({
            url      : '{{ route("wizard.proses4.store-proses-tumbling") }}',
            type     : 'POST',
            data     : buildStep2Payload({ skip_aging: 1 }),
            dataType : 'json'
        })
        .done(res => {
            hideLoading();
            if (res.success) {
                clearState();
                window.location.href = '{{ route("wizard.proses4.index") }}?success='
                    + encodeURIComponent('Data Proses Tumbling berhasil disimpan. Proses Aging dapat dilakukan nanti.');
            } else {
                showAlertV3('danger', 'Gagal', res.message || 'Gagal menyimpan data.');
            }
        })
        .fail(xhr => handleStep2Error(xhr));
    }

    // ── Submit Step 3 ─────────────────────────────────────────────────────────

    function submitStep3() {
        state.step3_form = $('#form-step-3').serializeArray();
        persistState();

        showLoading('Saving Proses Aging...');
        $.post(
            '{{ route("wizard.proses4.store-proses-aging") }}',
            $('#form-step-3').serialize()
        )
        .done(res => {
            hideLoading();
            if (res.success) {
                clearState();
                window.location.href = res.redirect_url;
            }
        })
        .fail(xhr => {
            hideLoading();
            let msg = 'Tolong Lengkapi Pengisian Datanya.';
            if (xhr.status === 422 && xhr.responseJSON) {
                msg = xhr.responseJSON.message || msg;
                if (xhr.responseJSON.errors) {
                    msg += '<br><ul class="text-left mt-2">';
                    Object.keys(xhr.responseJSON.errors).forEach(key => {
                        xhr.responseJSON.errors[key].forEach(e => { msg += `<li>${e}</li>`; });
                    });
                    msg += '</ul>';
                }
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            showAlertV3('danger', 'Error', msg);
        });
    }

    // ── Step 3: Dynamic aging rows ────────────────────────────────────────────

    $(document).on('click', '.btn-add-suhu', function () {
        const rowCount = $('#tbody-suhu-kondisi tr').length + 1;
        $('#tbody-suhu-kondisi').append(`
            <tr class="suhu-kondisi-row">
                <td class="text-center row-number">${rowCount}</td>
                <td>
                    <input type="text" name="waktu_mulai_aging[]"
                        class="form-control form-control-sm" placeholder="Contoh: 08:00" required>
                </td>
                <td>
                    <input type="number" step="any" name="suhu_produk[]"
                        class="form-control form-control-sm" placeholder="Contoh: 5.5" required>
                </td>
                <td>
                    <select name="kondisi_produk[]" class="form-control form-control-sm" required>
                        <option value="✓">✓ OK</option>
                        <option value="✘">✘ Tidak OK</option>
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-suhu">
                        <i class="fas fa-minus"></i>
                    </button>
                </td>
            </tr>
        `);
        updateRowNumbers();
    });

    $(document).on('click', '.btn-remove-suhu', function () {
        if ($('#tbody-suhu-kondisi tr').length > 1) {
            $(this).closest('tr').remove();
            updateRowNumbers();
        } else {
            alert('Minimal harus ada 1 data');
        }
    });

    function updateRowNumbers() {
        $('#tbody-suhu-kondisi tr').each(function (index) {
            $(this).find('.row-number').text(index + 1);
        });
    }

    // ── Restore session state on page load ───────────────────────────────────

    if (state.current_step && state.current_step > 1) {
        restoreForm($('#form-step-1'), state.step1_form);

        if (state.produk_id) {
            $('#id_produk').val(state.produk_id);
            state.produk_nama = $('#id_produk').find('option:selected').text() || state.produk_nama;

            const $formula = $('#nomor_formula_non_forming');
            $formula.prop('disabled', false).html('<option value="">Loading...</option>');
            $.get(`{{ url("/qc-sistem/ajax/formula-non-forming") }}/${state.produk_id}`)
                .done(data => {
                    let html = '<option value="">Pilih Formula</option>';
                    data.forEach(f => html += `<option value="${f.id}">${f.nomor_formula || f.id}</option>`);
                    $formula.html(html);
                    if (state.step1_form) {
                        const fi = state.step1_form.find(i => i.name === 'nomor_formula_non_forming');
                        if (fi) $formula.val(fi.value);
                    }
                });
        }

        if (state.bahan_uuid)    $('#p2_bahan_uuid').val(state.bahan_uuid);
        if (state.shift_id)      $('#p2_shift_id').val(state.shift_id);
        if (state.kode_produksi) $('#p2_kode_produksi').val(state.kode_produksi);
        if (state.tanggal)  { $('#p2_tanggal').val(state.tanggal); $('#p3_tanggal').val(state.tanggal); }
        if (state.jam)      { $('#p2_jam').val(state.jam);         $('#p3_jam').val(state.jam); }
        if (state.produk_id){ $('#p2_id_produk').val(state.produk_id); $('#p3_id_produk').val(state.produk_id); }

        $('.info-badge').text(`${state.produk_nama} | ${state.kode_produksi}`);

        if (state.current_step >= 2) {
            loadTumblingFields().then(() => {
                restoreForm($('#form-step-2'), state.step2_form);
            });
        }
        if (state.current_step >= 3) {
            if (state.proses_tumbling_uuid) $('#p3_tumbling_uuid').val(state.proses_tumbling_uuid);
            restoreForm($('#form-step-3'), state.step3_form);
        }

        updateWizardUI(state.current_step);
        setTimeout(() => showAlertV3('info', 'Sesi Dipulihkan', 'Melanjutkan pengisian data yang belum selesai.'), 500);
    }

}); // end document.ready
</script>
@endpush