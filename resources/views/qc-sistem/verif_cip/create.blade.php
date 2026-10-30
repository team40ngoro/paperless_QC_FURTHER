@extends('layouts.app')

@section('title', 'Form Pencatatan Cleaning Mouldrum')

@section('container')
<div class="main-panel">
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Form Pengecekan Hasil PH Cleaning CIP</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="">Home</a></li>
              <li class="breadcrumb-item active">Verif CIP</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Pengecekan Hasil PH Cleaning CIP</h3>
              </div>
              <div class="card-body">
                <div class="verif-cip">
                  @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                      {{ session('success') }}
                    </div>
                  @endif
                  @if($errors->any())
                    <div class="alert alert-danger">
                      <ul class="mb-0">
                        @foreach($errors->all() as $error)
                          <li>{{ $error }}</li>
                        @endforeach
                      </ul>
                    </div>
                  @endif

                  <form id="main-form" method="POST" action="{{ route('verif-cip.store') }}">
                    @csrf

                    @if(!empty($plans))
                      <div class="form-group">
                        <label>Plan <span class="text-danger">*</span></label>
                        <select name="id_plan" id="id_plan" class="form-control" required>
                          @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ (int) $selectedPlanId === (int) $plan->id ? 'selected' : '' }}>
                              {{ $plan->nama_plan }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                    @endif

                    {{-- Info: link ke master CIP --}}
                    <div class="callout callout-info mb-3">
                      <p class="mb-0">
                        <i class="fas fa-info-circle"></i>
                        Nilai <strong>Std. Suhu</strong> dan <strong>Std. Waktu</strong> setiap tahap diambil dari
                        <a href="{{ route('master-cip.index') }}" target="_blank"><strong>Data Master CIP</strong></a>.
                        Jika belum ada, silakan isi terlebih dahulu.
                      </p>
                    </div>

                    <input type="hidden" name="payload" id="payload">
                    <input type="hidden" name="tanggal" id="tanggal">

                    <div id="form-blocks-container"></div>

                    <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3">
                      <div class="text-muted small"></div>
                      <div>
                        <button type="reset" class="btn btn-outline-secondary">Reset Semua</button>
                        <button type="submit" class="btn btn-primary">
                          <i class="fas fa-save"></i> Simpan Data
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

@push('scripts')
<script>
  // =====================================================================
  // CONFIG
  // =====================================================================
  let formBlockCount = 0;
  const detailCounters = {};

  // Data master CIP (diisi via AJAX setelah page load)
  let masterCip = {};

  const css = document.createElement('style');
  css.textContent = `
    .verif-cip .callout { border-left-width: 4px; }
    .verif-cip .card.card-outline > .card-header { background: #f8f9fa; }
    .verif-cip .card.card-outline > .card-header .badge { font-size: 12px; }
    .verif-cip label { font-weight: 600; }
    .master-badge {
      font-size: 10px;
      background: #e3f2fd;
      color: #1565c0;
      border: 1px solid #90caf9;
      border-radius: 4px;
      padding: 1px 5px;
      margin-left: 4px;
      vertical-align: middle;
    }
    .std-ref {
      font-size: 11px;
      color: #6c757d;
      margin-top: 2px;
    }
    .std-ref strong { color: #388e3c; }
    .suhu-display-badge {
      font-size: 11px;
      background: #fff3e0;
      color: #e65100;
      border: 1px solid #ffcc80;
      border-radius: 4px;
      padding: 1px 6px;
    }
  `;
  document.head.appendChild(css);

  const STEPS = [
    { key: 'ro1',  label: 'Rinse Outside 1' },
    { key: 'ri1',  label: 'Rinse Inside 1' },
    { key: 'ro2',  label: 'Rinse Outside 2' },
    { key: 'ri2',  label: 'Rinse Inside 2' },
    { key: 'hc',   label: 'Hot Clean Inside' },
    { key: 'hci',  label: 'Hot Clean Outside' },
    { key: 'ro3',  label: 'Rinse Outside 3' },
    { key: 'ri3',  label: 'Rinse Inside 3' },
    { key: 'dis',  label: 'Disinfection' },
    { key: 'diso', label: 'Disinfection Out' },
    { key: 'ro4',  label: 'Rinse Outside 4' },
    { key: 'ri4',  label: 'Rinse Inside 4' },
  ];

  // =====================================================================
  // AJAX: Load master CIP berdasarkan plan
  // =====================================================================
  function loadMasterCip(planId, callback) {
    const url = `{{ route('ajax.master-cip-by-plan') }}?plan_id=${planId}`;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(data => {
        masterCip = data || {};
        if (callback) callback();
      })
      .catch(() => {
        masterCip = {};
        if (callback) callback();
      });
  }

  // =====================================================================
  // Helpers
  // =====================================================================
  function getStd(stepKey, field) {
    return masterCip[stepKey] ? masterCip[stepKey][field] : null;
  }

  function stdWaktuText(stepKey) {
    const v = getStd(stepKey, 'std_waktu');
    return v !== null && v !== undefined ? `${parseInt(v)} dtk` : '—';
  }

  // =====================================================================
  // HTML Builders
  // =====================================================================
  function stepCardHTML(prefix, s) {
    const stdWaktu = getStd(s.key, 'std_waktu');
    const waktuVal = stdWaktu !== null && stdWaktu !== undefined ? stdWaktu : '';
    const hasMaster = stdWaktu !== null;
    const masterTag = hasMaster ? `<span class="master-badge">Std Waktu</span>` : '';

    return `
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="card card-outline card-secondary mb-2">
          <div class="card-header py-1">
            <small><strong>${s.label}</strong>${masterTag}</small>
          </div>
          <div class="card-body py-2">

            <div class="form-group mb-1">
              <label class="mb-1">Suhu Aktual (°C)</label>
              <div class="input-group input-group-sm">
                <input type="number" class="form-control"
                       name="${prefix}_${s.key}_suhu"
                       step="0.1" placeholder="0">
                <div class="input-group-append">
                  <span class="input-group-text">°C</span>
                </div>
              </div>
            </div>

            <div class="form-group mb-1">
              <label class="mb-1">Suhu Display (°C) <span class="suhu-display-badge">Display</span></label>
              <div class="input-group input-group-sm">
                <input type="number" class="form-control"
                       name="${prefix}_${s.key}_suhu_display"
                       step="0.1" placeholder="0">
                <div class="input-group-append">
                  <span class="input-group-text">°C</span>
                </div>
              </div>
            </div>

            <div class="form-group mb-0">
              <label class="mb-1">Waktu (dtk)</label>
              <div class="std-ref mb-1">Std: <strong>${stdWaktuText(s.key)}</strong></div>
              <div class="input-group input-group-sm">
                <input type="number" class="form-control"
                       name="${prefix}_${s.key}_waktu"
                       placeholder="0"
                       value="${waktuVal}" readonly>
                <div class="input-group-append">
                  <span class="input-group-text">dtk</span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>`;
  }

  function stepsHTML(prefix) {
    return STEPS.map(s => stepCardHTML(prefix, s)).join('');
  }

  function detailBlockHTML(formIdx, detailIdx) {
    const p = `f${formIdx}_d${detailIdx}`;
    return `
    <div class="card card-outline card-info mb-3" id="detail-${p}">
      <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <div>
          <strong>Detail Mouldrum</strong>
          <span class="badge badge-info ml-2">#${detailIdx}</span>
        </div>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse" aria-label="Collapse">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="btn btn-danger btn-sm" onclick="removeEl('detail-${p}')">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>
      <div class="card-body">

        <div class="form-group">
          <label>Jenis Mouldrum <span class="text-danger">*</span></label>
          <input type="text" class="form-control" name="${p}_jenis_mouldrum" placeholder="Masukkan jenis mouldrum" required>
        </div>

        <div class="mt-3 mb-2"><strong>Step Proses Cleaning</strong></div>
        <div class="row">${stepsHTML(p)}</div>

        <div class="mt-3 mb-2"><strong>Kondisi Mouldrum</strong></div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Sebelum Cleaning</label>
              <input type="text" class="form-control" name="${p}_kondisi_sebelum" placeholder="Contoh: Kotor, Bersih, dll...">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Sesudah Cleaning</label>
              <input type="text" class="form-control" name="${p}_kondisi_sesudah" placeholder="Contoh: Bersih, Perlu Ulang, dll...">
            </div>
          </div>
        </div>

        <div class="mt-3 mb-2"><strong>Parameter Akhir &amp; Keterangan</strong></div>
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label>pH Air Bilasan Terakhir <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="number" class="form-control" name="${p}_ph_air" step="0.1" min="0" max="14" placeholder="6.0" required>
                <div class="input-group-append">
                  <span class="input-group-text">pH</span>
                </div>
              </div>
              <small class="text-muted">Normal: 6.0 - 7.0</small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Pressure</label>
              <input type="text" class="form-control" name="${p}_pressure" placeholder="Contoh: 2.5 bar">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Keterangan</label>
              <textarea class="form-control" name="${p}_keterangan" placeholder="Catatan tambahan..."></textarea>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group mb-0">
              <label>Tindakan Koreksi</label>
              <textarea class="form-control" name="${p}_tindakan_koreksi" placeholder="Tindakan perbaikan jika ada penyimpangan..."></textarea>
            </div>
          </div>
        </div>

      </div>
    </div>`;
  }

  function formBlockHTML(formIdx) {
    return `
    <div class="card card-outline card-primary mb-4" id="form-block-${formIdx}">
      <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <div>
          <strong>Form</strong>
          <span class="badge badge-primary ml-2">#${formIdx}</span>
        </div>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse" aria-label="Collapse">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="btn btn-danger btn-sm" onclick="removeEl('form-block-${formIdx}')">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label>Tanggal <span class="text-danger">*</span></label>
              <input type="date" class="form-control" name="f${formIdx}_tanggal" required>
            </div>
          </div>
        </div>
        <div id="details-container-${formIdx}"></div>
        <div class="d-flex justify-content-between align-items-center mt-2">
          <div class="text-muted small">Tambahkan detail mouldrum untuk tanggal ini.</div>
          <div class="d-flex align-items-center">
            <button type="button" class="btn btn-secondary btn-sm mr-2" onclick="addDetail(${formIdx})">
              <i class="fas fa-plus"></i> Tambah Detail
            </button>
            <button type="button" class="btn btn-success btn-sm" onclick="addFormBlock()">
              <i class="fas fa-plus"></i> Tambah Form
            </button>
          </div>
        </div>
      </div>
    </div>`;
  }

  // =====================================================================
  // Actions
  // =====================================================================
  function addFormBlock() {
    formBlockCount++;
    const idx = formBlockCount;
    detailCounters[idx] = 0;
    const container = document.getElementById('form-blocks-container');
    const div = document.createElement('div');
    div.innerHTML = formBlockHTML(idx);
    container.appendChild(div.firstElementChild);
    document.querySelector(`input[name="f${idx}_tanggal"]`).valueAsDate = new Date();
    addDetail(idx);
    renumberForms();
  }

  function renumberForms() {
    const container = document.getElementById('form-blocks-container');
    if (!container) return;
    const formCards = container.querySelectorAll('.card[id^="form-block-"]');
    formCards.forEach((card, idx) => {
      const badge = card.querySelector('.card-header .badge');
      if (badge) badge.textContent = `#${idx + 1}`;
    });
  }

  function addDetail(formIdx) {
    detailCounters[formIdx] = (detailCounters[formIdx] || 0) + 1;
    const detailIdx = detailCounters[formIdx];
    const container = document.getElementById(`details-container-${formIdx}`);
    const div = document.createElement('div');
    div.innerHTML = detailBlockHTML(formIdx, detailIdx);
    container.appendChild(div.firstElementChild);
    renumberDetails(formIdx);
  }

  function renumberDetails(formIdx) {
    const container = document.getElementById(`details-container-${formIdx}`);
    if (!container) return;
    const detailCards = container.querySelectorAll(`.card[id^="detail-f${formIdx}_d"]`);
    detailCards.forEach((card, idx) => {
      const badge = card.querySelector('.card-header .badge');
      if (badge) badge.textContent = `#${idx + 1}`;
    });
  }

  function removeEl(id) {
    const el = document.getElementById(id);
    if (!el) return;
    const match = id.match(/^detail-f(\d+)_d\d+$/);
    const formIdx = match ? match[1] : null;
    const matchFormBlock = id.match(/^form-block-(\d+)$/);
    el.style.transition = 'opacity 0.2s, transform 0.2s';
    el.style.opacity = '0';
    el.style.transform = 'translateY(-8px)';
    setTimeout(() => {
      el.remove();
      if (formIdx) renumberDetails(formIdx);
      if (matchFormBlock) renumberForms();
    }, 200);
  }

  // =====================================================================
  // Payload builder
  // =====================================================================
  function buildPayload() {
    const forms = [];
    const formCards = document.querySelectorAll('#form-blocks-container .card[id^="form-block-"]');

    formCards.forEach((formCard) => {
      const formId  = formCard.getAttribute('id') || '';
      const formIdx = (formId.match(/^form-block-(\d+)$/) || [])[1];
      if (!formIdx) return;

      const tanggalInput = formCard.querySelector(`input[name="f${formIdx}_tanggal"]`);
      const tanggal = tanggalInput ? tanggalInput.value : null;

      const details = [];
      const detailCards = formCard.querySelectorAll(`.card[id^="detail-f${formIdx}_d"]`);

      detailCards.forEach((detailCard) => {
        const detailId  = detailCard.getAttribute('id') || '';
        const detailIdx = (detailId.match(new RegExp(`^detail-f${formIdx}_d(\\d+)$`)) || [])[1];
        if (!detailIdx) return;

        const p = `f${formIdx}_d${detailIdx}`;

        const steps = {};
        STEPS.forEach(s => {
          const suhuEl        = detailCard.querySelector(`input[name="${p}_${s.key}_suhu"]`);
          const suhuDisplayEl = detailCard.querySelector(`input[name="${p}_${s.key}_suhu_display"]`);
          const waktuEl       = detailCard.querySelector(`input[name="${p}_${s.key}_waktu"]`);
          steps[s.key] = {
            suhu:         suhuEl        ? suhuEl.value        : null,
            suhu_display: suhuDisplayEl ? suhuDisplayEl.value : null,
            waktu:        waktuEl       ? waktuEl.value       : null,
            // Sertakan nilai std waktu dari master (untuk referensi di show/PDF)
            std_waktu: masterCip[s.key] ? masterCip[s.key].std_waktu : null,
          };
        });

        details.push({
          jenis_mouldrum:  detailCard.querySelector(`input[name="${p}_jenis_mouldrum"]`)?.value ?? null,
          kondisi_sebelum: detailCard.querySelector(`input[name="${p}_kondisi_sebelum"]`)?.value ?? null,
          kondisi_sesudah: detailCard.querySelector(`input[name="${p}_kondisi_sesudah"]`)?.value ?? null,
          ph_air:          detailCard.querySelector(`input[name="${p}_ph_air"]`)?.value ?? null,
          pressure:        detailCard.querySelector(`input[name="${p}_pressure"]`)?.value ?? null,
          keterangan:      detailCard.querySelector(`textarea[name="${p}_keterangan"]`)?.value ?? null,
          tindakan_koreksi: detailCard.querySelector(`textarea[name="${p}_tindakan_koreksi"]`)?.value ?? null,
          steps,
        });
      });

      forms.push({ tanggal, details });
    });

    return { forms };
  }

  // =====================================================================
  // Submit
  // =====================================================================
  document.getElementById('main-form').addEventListener('submit', function() {
    const payload = buildPayload();
    document.getElementById('payload').value = JSON.stringify(payload);
    const firstTanggal = payload?.forms?.[0]?.tanggal || '';
    document.getElementById('tanggal').value = firstTanggal;
  });

  // =====================================================================
  // Init: load master CIP lalu render form pertama
  // =====================================================================
  function getCurrentPlanId() {
    const sel = document.getElementById('id_plan');
    return sel ? sel.value : '{{ $selectedPlanId }}';
  }

  function initForm() {
    loadMasterCip(getCurrentPlanId(), function() {
      addFormBlock();
    });
  }

  // Jika ada dropdown plan (superadmin), reload master saat plan berubah
  const planSel = document.getElementById('id_plan');
  if (planSel) {
    planSel.addEventListener('change', function() {
      // Kosongkan form blocks, reload master, buat ulang form pertama
      document.getElementById('form-blocks-container').innerHTML = '';
      formBlockCount = 0;
      loadMasterCip(this.value, function() {
        addFormBlock();
      });
    });
  }

  initForm();
</script>
@endpush

@endsection