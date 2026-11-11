@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-magic text-primary"></i> Verifikasi Proses Roasted Produk</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('wizard.proses3.index') }}">Verifikasi Proses Roasted Produk</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- WIZARD STEPS INDICATOR -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="wizard-steps-container d-flex justify-content-between position-relative">
                        <div class="wizard-line"></div>
                        @php
                            $steps = [
                                1 => ['icon' => 'fa-file-import',   'label' => 'Input Roasting'],
                                2 => ['icon' => 'fa-fan',           'label' => 'Roasting Fan'],
                            ];
                        @endphp

                        @foreach($steps as $num => $s)
                        <div class="wizard-step-item text-center {{ $num == 1 ? 'active' : '' }}" id="step-indicator-{{ $num }}">
                            <div class="step-icon-circle mx-auto mb-2">
                                <i class="fas {{ $s['icon'] }}"></i>
                            </div>
                            <span class="step-label d-none d-md-block">{{ $s['label'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @php
                $userRole = auth()->user()->id_role ?? null;
                $showTime = in_array($userRole, [1, 2, 5]);
                $displayFormat = $showTime ? 'd-m-Y H:i:s' : 'd-m-Y';
                $submitValue = date('d-m-Y H:i:s');
                $displayValue = date($displayFormat);
                $now = \Carbon\Carbon::now('Asia/Jakarta');
            @endphp

            <!-- STEP 1: INPUT ROASTING -->
            {{-- ===== ALERT CONTAINER ===== --}}
            <div id="wizardAlert" class="d-none mb-3"></div>

            <div class="wizard-step" id="step-1">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-file-import mr-1"></i> Form Input Roasting (Langkah 1)</h3></div>
                    <div class="card-body">
                        <form id="form-step-1">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group"><label>Shift *</label>
                                        <select name="id_shift" id="p1_id_shift" class="form-control" required>
                                            <option value="">Pilih Shift</option>
                                            @foreach($shift as $s) 
                                                <option value="{{ $s->id }}" {{ (isset($draftData) && $draftData->shift_id == $s->id) ? 'selected' : '' }}>{{ $s->shift }}</option> 
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group"><label>Tanggal *</label>
                                        @php
                                            $dateValue = $now->format('Y-m-d');
                                        @endphp
                                        <input type="date" name="tanggal" class="form-control" value="{{ isset($draftData) ? \Carbon\Carbon::parse($draftData->tanggal)->format('Y-m-d') : $dateValue }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group"><label><i class="fas fa-clock"></i> Jam *</label>
                                        <input type="time" name="jam" class="form-control" value="{{ $draftData->jam ?? $now->format('H:i') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group"><label>Produk *</label>
                                        <select name="id_produk" id="id_produk_p1" class="form-control product-selector select2-field" data-target-table="#p2-product-details-table" required>
                                            <option value="">Pilih Produk</option>
                                            @foreach($produk as $p) 
                                                <option value="{{ $p->id }}" {{ (isset($draftData) && $draftData->id_produk == $p->id) ? 'selected' : '' }}>{{ $p->nama_produk }}</option> 
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group"><label>Berat Produk (gr) *</label>
                                        <select id="nilai_select_berat" name="berat_bahan" class="form-control" data-nilai-berat="true"></select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group"><label>Kode Produksi *</label>
                                        <input type="text" name="kode_produksi" class="form-control" placeholder="Masukkan kode produksi" value="{{ $draftData->kode_produksi ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group"><label>Standard Suhu Sebelum Pemasakan: *</label>
                                        <input type="text" name="std_suhu_sebelum" id="p1_std_suhu_sebelum" class="form-control" placeholder="Masukkan std suhu sebelum" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group"><label>Aktual Suhu Sebelum Pemasakan: *</label>
                                        <input type="text" name="aktual_suhu_sesudah" id="p1_aktual_suhu_sesudah" class="form-control" placeholder="Masukkan aktual suhu sebelum" required>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-right">
                        <button type="button" class="btn btn-primary next-step" data-step="1">Simpan & Lanjut <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- STEP 2: ROASTING FAN -->
            <div class="wizard-step" id="step-2" style="display:none;">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-fan mr-1"></i> Form Roasting Fan (Langkah 2)</h3></div>
                    <div class="card-body">
                        <div class="alert alert-info py-2"><strong>Produk:</strong> <span class="info_produk">-</span></div>
                        <form id="form-step-2">
                            <input type="hidden" name="input_roasting_uuid" id="p2_input_roasting_uuid">
                            <input type="hidden" name="id_produk" id="id_produk_p2">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Tanggal *</label>
                                <div class="col-sm-10">
                                    <input type="date" name="tanggal" class="form-control" value="{{ $dateValue }}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"><i class="fas fa-clock"></i> Jam *</label>
                                <div class="col-sm-10">
                                    <input type="time" name="jam" class="form-control" value="{{ $now->format('H:i') }}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Waktu Pemasakan (pukul)</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="aktual_waktu" placeholder="pukul">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Waktu Pengecekan (menit)</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="waktu_pemasakan" placeholder="menit">
                                </div>
                            </div>

                            <div class="card card-primary card-outline mt-3">
                                <div class="card-header py-2 text-sm"><h3 class="card-title text-sm">Detail Proses Roasting Fan</h3></div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table id="p2-product-details-table" class="table table-bordered mb-0">
                                            <thead><tr class="thead-dark"><th class="text-center">Pilih Produk Terlebih Dahulu Untuk Menampilkan Isi Tablenya</th></tr></thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <!-- <button type="button" class="btn btn-secondary prev-step" data-step="2"><i class="fas fa-arrow-left"></i> Kembali</button> -->
                        <button type="button" class="btn btn-success next-step" data-step="2"><i class="fas fa-check-circle mr-1"></i> Simpan &amp; Selesaikan Input Produksi</button>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<style>
    .wizard-steps-container { margin: 20px 0 40px; }
    .wizard-line { position: absolute; top: 25px; left: 5%; right: 5%; height: 3px; background: #e0e0e0; z-index: 1; }
    .wizard-step-item { position: relative; z-index: 2; width: 50%; }
    .step-icon-circle { width: 50px; height: 50px; border-radius: 50%; background: #fff; border: 3px solid #e0e0e0; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #adb5bd; transition: 0.3s; }
    .wizard-step-item.active .step-icon-circle { border-color: #007bff; background: #007bff; color: #fff; transform: scale(1.1); box-shadow: 0 0 10px rgba(0,123,255,0.3); }
    .wizard-step-item.completed .step-icon-circle { border-color: #28a745; background: #28a745; color: #fff; }
    .step-label { font-size: 13px; font-weight: 600; color: #6c757d; }
    .wizard-step-item.active .step-label { color: #007bff; }
</style>

</div> {{-- End content --}}

{{-- ===== LOADING OVERLAY ===== --}}
<div id="loadingOverlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.4);z-index:9999;align-items:center;justify-content:center;">
    <div class="text-center text-white">
        <div class="spinner-border text-primary mb-2" role="status"></div>
        <h5 id="loadingMessage">Mohon Tunggu...</h5>
    </div>
</div>

<style>
    #loadingOverlay.show { display: flex !important; }
    .wizard-step-indicator.locked .step-circle    { background:#f8f9fa;  color:#ced4da; border-color:#dee2e6; }
    .wizard-step-indicator.active .step-circle    { background:#007bff;  color:#fff;    border-color:#007bff; box-shadow:0 0 0 3px rgba(0,123,255,.2); }
    .wizard-step-indicator.completed .step-circle { background:#28a745;  color:#fff;    border-color:#28a745; }
    .step-line { align-self:center; margin-top:-20px; }
</style>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // ========== STATE ==========
        const STORAGE_KEY = 'wizard_proses3_state_v2';



        let state = {
            current_step: 1,
            input_roasting_uuid: null,
            roasting_fan_uuid: null,
            hasil_roasting_uuid: null,
            id_produk: null,
            nama_produk: null,
            user_id: "{{ Auth::id() }}"
        };

        try {
            const saved = sessionStorage.getItem(STORAGE_KEY);
            if (saved) {
                const parsed = JSON.parse(saved);
                // VALIDASI USER: Jika user_id di draf tidak sama dengan user sekarang, abaikan draf.
                if (parsed && typeof parsed === 'object') {
                    if (parsed.user_id && parsed.user_id !== state.user_id) {
                        sessionStorage.removeItem(STORAGE_KEY);
                    } else {
                        Object.assign(state, parsed);
                    }
                }
            }
        } catch (_) {}

        // NEW BLOCK: RESTORE FROM SERVER-SIDE DRAFT (via continue_uuid)
        @if(isset($draftData) && $draftData)
            state.input_roasting_uuid = "{{ $draftData->uuid }}";
            state.id_produk = "{{ $draftData->id_produk }}";
            state.nama_produk = "{{ $draftData->produk->nama_produk ?? '' }}";
            state.current_step = 2;
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        @endif

        function persistState() {
            try { sessionStorage.setItem(STORAGE_KEY, JSON.stringify(state)); } catch(_) {}
        }

        // ========== INITIALIZATION ==========
        $('.select2-field').select2({ theme: 'bootstrap4' });

        // Trigger load initial data if any
        if ($('#id_produk_p1').val()) {
            $('#id_produk_p1').trigger('change');
        }

        // Pulihkan sesi jika ada
        if (state.current_step > 1) {
            if (state.input_roasting_uuid) $('#p2_input_roasting_uuid').val(state.input_roasting_uuid);
            if (state.roasting_fan_uuid) $('#p3_roasting_fan_uuid').val(state.roasting_fan_uuid);
            if (state.hasil_roasting_uuid) $('#p4_hasil_roasting_uuid').val(state.hasil_roasting_uuid);
            
            // Gunakan setTimeout agar trigger('change') diproses setelah semua script layout (app.blade) ter-load sepenuhnya.
            setTimeout(() => {
                const target = document.getElementById(`step-${state.current_step}`);
                // Pilih ulang produk agar trigger change terpanggil pada step yang aktif atau step 1
                if (state.id_produk) {
                    // Update field di step 1
                    $('#id_produk_p1').val(state.id_produk).trigger('change');
                    
                    // Update field di step yang sedang aktif jika ada (misal step 2 untuk Roasting Fan)
                    if (target) {
                        const stepProdFields = target.querySelectorAll('[name="id_produk"], .product-selector');
                        stepProdFields.forEach(field => {
                            $(field).val(state.id_produk).trigger('change');
                        });
                    }
                }
                goToStep(state.current_step);
            }, 100);

            if (state.nama_produk) {
                 $('.info_produk').text(state.nama_produk);
            }
            setTimeout(() => showAlert('info', 'Sesi Dipulihkan', 'Melanjutkan pengisian data yang belum selesai.'), 500);
        }

        // ========== DYNAMIC SUHU PUSAT ==========
        $(document).on('click', '.btn-add-suhu-pusat', function() {
            var newRow = `
                <div class="input-group mb-2">
                    <input type="text" name="aktual_suhu_pusat[]" class="form-control" placeholder="Masukkan Aktual Suhu Pusat">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-remove-suhu-pusat"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
            `;
            $('#aktual_suhu_pusat_container').append(newRow);
        });

        $(document).on('click', '.btn-remove-suhu-pusat', function() {
            $(this).closest('.input-group').remove();
        });

        // ========== EVENTS ==========
        $('#id_produk_p1').on('change', function() {
            state.id_produk = $(this).val();
            state.nama_produk = $(this).find('option:selected').text();
            $('.info_produk').text(state.nama_produk || '-');
            $('#id_produk_p2, #p3_id_produk, #p4_id_produk').val(state.id_produk);
            
            // ATM: Load Std Suhu Pusat
            loadStdSuhuPusat(state.id_produk);
        });

        function goToStep(step) {
            $('.wizard-step').hide();
            $('#step-' + step).fadeIn();
            
            $('.wizard-step-item').removeClass('active completed');
            $('.wizard-step-item').each(function(idx) {
                if (idx + 1 < step) $(this).addClass('completed');
                if (idx + 1 === step) $(this).addClass('active');
            });
            state.current_step = step;
            persistState();
            window.scrollTo(0, 0);
        }

        $('.prev-step').on('click', function() { goToStep(parseInt($(this).data('step')) - 1); });

        // ========== STEP 1: INPUT ROASTING ==========
        $('#step-1 .next-step').on('click', function() {
            const f = $('#form-step-1');
            
            if (!$('#id_produk_p1').val() || !$('#p1_id_shift').val() || !$('#nilai_select_berat').val()) {
                return showAlert('warning', 'Peringatan', 'Harap isi semua field bertanda bintang (*).');
            }

            state.id_produk = $('#id_produk_p1').val();
            state.nama_produk = $('#id_produk_p1 option:selected').text();
            $('.info_produk').text(state.nama_produk);

            showLoading('Saving Input Roasting...');
            $.ajax({
                url: '{{ route("wizard.proses3.store-input-roasting") }}',
                type: 'POST',
                data: f.serialize(),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(res) {
                    hideLoading();
                    if (res.success) {
                        state.input_roasting_uuid = res.uuid;
                        $('#p2_input_roasting_uuid').val(res.uuid);
                        goToStep(2);
                        showAlert('success', 'Berhasil', res.message);
                    } else { showAlert('error', 'Gagal', res.message); }
                },
                                error: function(xhr) {
                    hideLoading();
                    let msg = 'Tolong Lengkapi Pengisian Datanya.';
                    if (xhr.status === 422 && xhr.responseJSON) {
                        msg = xhr.responseJSON.message || 'Validasi Gagal: Periksa kembali data Anda.';
                        if (xhr.responseJSON.errors) {
                            msg += '\n- ' + Object.values(xhr.responseJSON.errors).flat().join('\n- ');
                        }
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    showAlert('error', 'Error', msg);
                }
            });
        });

        // ========== STEP 2: ROASTING FAN ==========
        $('#step-2 .next-step').on('click', function() {
            const f = $('#form-step-2');

            showLoading('Menyimpan Roasting Fan & Menyelesaikan Input Produksi...');
            $.ajax({
                url: '{{ route("wizard.proses3.store-roasting-fan") }}',
                type: 'POST',
                data: f.serialize() + '&id_produk=' + state.id_produk,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(res) {
                    hideLoading();
                    if (res.success) {
                        state.roasting_fan_uuid = res.uuid;
                        try { sessionStorage.removeItem(STORAGE_KEY); } catch(_) {}
                        window.location.href = res.redirect_url || '{{ route("wizard.proses3.index") }}';
                    } else { showAlert('error', 'Gagal', res.message); }
                },
                                error: function(xhr) {
                    hideLoading();
                    let msg = 'Tolong Lengkapi Pengisian Datanya.';
                    if (xhr.status === 422 && xhr.responseJSON) {
                        msg = xhr.responseJSON.message || 'Validasi Gagal: Periksa kembali data Anda.';
                        if (xhr.responseJSON.errors) {
                            msg += '\n- ' + Object.values(xhr.responseJSON.errors).flat().join('\n- ');
                        }
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    showAlert('error', 'Error', msg);
                }
            });
        });

        // ========== STEP 3: HASIL ROASTING ==========
        $('#step-3 .next-step').on('click', function() {
            const f = $('#form-step-3');

            showLoading('Saving Hasil Roasting...');
            $.ajax({
                url: '{{ route("wizard.proses3.store-hasil-roasting") }}',
                type: 'POST',
                data: f.serialize() + '&id_produk=' + state.id_produk + '&roasting_fan_uuid=' + state.roasting_fan_uuid,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(res) {
                    hideLoading();
                    if (res.success) {
                        state.hasil_roasting_uuid = res.uuid;
                        $('#p4_hasil_roasting_uuid').val(res.uuid);
                        goToStep(4);
                        showAlert('success', 'Berhasil', res.message);
                    } else { showAlert('error', 'Gagal', res.message); }
                },
                                error: function(xhr) {
                    hideLoading();
                    let msg = 'Tolong Lengkapi Pengisian Datanya.';
                    if (xhr.status === 422 && xhr.responseJSON) {
                        msg = xhr.responseJSON.message || 'Validasi Gagal: Periksa kembali data Anda.';
                        if (xhr.responseJSON.errors) {
                            msg += '\n- ' + Object.values(xhr.responseJSON.errors).flat().join('\n- ');
                        }
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    showAlert('error', 'Error', msg);
                }
            });
        });

        // ========== STEP 4: IQF ROASTING ==========
        $('#step-4 .next-step').on('click', function() {
            const f = $('#form-step-4');

            showLoading('Saving IQF Roasting...');
            $.ajax({
                url: '{{ route("wizard.proses3.store-iqf-roasting") }}',
                type: 'POST',
                data: f.serialize() + '&hasil_roasting_uuid=' + state.hasil_roasting_uuid,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(res) {
                    hideLoading();
                    if (res.success) {
                        try { sessionStorage.removeItem(STORAGE_KEY); } catch(_) {}
                        showAlert('success', 'Berhasil', res.message);
                        setTimeout(() => { window.location.href = res.redirect_url; }, 1500);
                    } else { showAlert('error', 'Gagal', res.message); }
                },
                error: function() { hideLoading(); showAlert('error', 'Error', 'Tolong Lengkapi Pengisian Datanya.'); }
            });
        });

        $('.prev-step').on('click', function() { goToStep(parseInt($(this).data('step')) - 1); });

        // ========== HELPERS ==========
        function showLoading(msg) {
            $('#loadingMessage').text(msg || 'Mohon Tunggu...');
            $('#loadingOverlay').addClass('show');
        }

        function hideLoading() {
            $('#loadingOverlay').removeClass('show');
        }

        function loadStdSuhuPusat(idProduk) {
            const sel = $('#p3_id_std_suhu_pusat');
            if(!idProduk) {
                sel.html('<option value="">Pilih</option>').prop('disabled', true);
                return;
            }
            
            sel.html('<option value="">Memuat...</option>').prop('disabled', true);
            
            // Correct path as per web.php: /qc-sistem/get-std-suhu-pusat-roasting/{id_produk}
            $.get(`{{ url('qc-sistem/get-std-suhu-pusat-roasting') }}/${idProduk}`, function(data) {
                let html = '<option value="">Pilih</option>';
                if (data && data.length > 0) {
                    data.forEach(d => { html += `<option value="${d.id}">${d.std_suhu_pusat_roasting} °C</option>`; });
                    sel.html(html).prop('disabled', false);
                } else {
                    // Fallback to general std suhu pusat: /qc-sistem/get-std-suhu-pusat-by-produk/{id_produk}
                    $.get(`{{ url('qc-sistem/get-std-suhu-pusat-by-produk') }}/${idProduk}`, function(altData) {
                        let htmlAlt = '<option value="">Pilih</option>';
                        if (altData && altData.length > 0) {
                            altData.forEach(d => { htmlAlt += `<option value="${d.id}">${d.std_suhu_pusat} °C</option>`; });
                            sel.html(htmlAlt).prop('disabled', false);
                        } else {
                            sel.html('<option value="">Tidak Ada Data</option>').prop('disabled', true);
                        }
                    }).fail(function() {
                        sel.html('<option value="">Tidak Ada Data</option>').prop('disabled', true);
                    });
                }
            }).fail(function() {
                $.get(`{{ url('qc-sistem/get-std-suhu-pusat-by-produk') }}/${idProduk}`, function(altData) {
                    let htmlAlt = '<option value="">Pilih</option>';
                    if (altData && altData.length > 0) {
                        altData.forEach(d => { htmlAlt += `<option value="${d.id}">${d.std_suhu_pusat} °C</option>`; });
                        sel.html(htmlAlt).prop('disabled', false);
                    } else {
                        sel.html('<option value="">Tidak Ada Data</option>').prop('disabled', true);
                    }
                }).fail(function() {
                    sel.html('<option value="">Tidak Ada Data</option>').prop('disabled', true);
                });
            });
        }

        function showAlert(type, title, msg) {
            const el = $('#wizardAlert');
            const alertType = (type === 'danger' || type === 'error') ? 'danger' : type;
            const icon = alertType === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
            el.html(`<div class="alert alert-${alertType} alert-dismissible shadow-sm border-0 fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="icon fas ${icon}"></i> ${msg}
            </div>`);
            el.removeClass('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
</script>
@endpush

