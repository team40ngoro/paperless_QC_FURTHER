
@extends('layouts.app')

@section('title', 'Edit Verif CIP')

@section('container')
<div class="main-panel">
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Edit Verif CIP</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('verif-cip.index') }}">Verif CIP</a></li>
              <li class="breadcrumb-item active">Edit</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-warning">
              <div class="card-header">
                <h3 class="card-title">Edit data Verif CIP</h3>
                <div class="card-tools">
                  <a href="{{ route('verif-cip.show', $item->uuid) }}" class="btn btn-secondary text-white btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                  </a>
                </div>
              </div>
              <div class="card-body">
                <div class="verif-cip">
                  @if($errors->any())
                    <div class="alert alert-danger">
                      <ul class="mb-0">
                        @foreach($errors->all() as $error)
                          <li>{{ $error }}</li>
                        @endforeach
                      </ul>
                    </div>
                  @endif

                  <div class="callout callout-warning mb-3">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <h5 class="mb-1">Ubah data monitoring</h5>
                        <p class="mb-0">Perubahan akan menggantikan payload data sebelumnya.</p>
                      </div>
                      <div class="text-right">
                        <button type="button" class="btn btn-success" onclick="addFormBlock()">
                          <i class="fas fa-plus"></i> Tambah Form
                        </button>
                      </div>
                    </div>
                  </div>

                  <form id="main-form" method="POST" action="{{ route('verif-cip.update', $item->uuid) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <label>Plan</label>
                          <div class="form-control" style="background:#f8f9fa;">{{ $item->plan->nama_plan ?? '-' }}</div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label>Dibuat Oleh</label>
                          <div class="form-control" style="background:#f8f9fa;">{{ $item->user->name ?? '-' }}</div>
                        </div>
                      </div>
                    </div>

                    <input type="hidden" name="payload" id="payload">
                    <input type="hidden" name="tanggal" id="tanggal">

                    <div id="form-blocks-container"></div>

                    <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3">
                      <div class="text-muted small">Pastikan semua kolom wajib terisi sebelum simpan.</div>
                      <div>
                        <button type="submit" class="btn btn-warning">
                          <i class="fas fa-save"></i> Update Data
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
  let formBlockCount = 0;
  const detailCounters = {};

  const css = document.createElement('style');
  css.textContent = `
    .verif-cip .callout { border-left-width: 4px; }
    .verif-cip .card.card-outline > .card-header { background: #f8f9fa; }
    .verif-cip .card.card-outline > .card-header .badge { font-size: 12px; }
    .verif-cip label { font-weight: 600; }
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

  function stepsHTML(prefix) {
    return STEPS.map(s => `
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="card card-outline card-secondary mb-2">
          <div class="card-header py-1">
            <small><strong>${s.label}</strong></small>
          </div>
          <div class="card-body py-2">
            <div class="form-group mb-1">
              <label class="mb-1">Suhu Aktual (°C)</label>
              <div class="input-group input-group-sm">
                <input type="number" class="form-control" name="${prefix}_${s.key}_suhu" step="0.1" placeholder="0">
                <div class="input-group-append">
                  <span class="input-group-text">°C</span>
                </div>
              </div>
            </div>
            <div class="form-group mb-1">
              <label class="mb-1">Suhu Display (°C)</label>
              <div class="input-group input-group-sm">
                <input type="number" class="form-control" name="${prefix}_${s.key}_suhu_display" step="0.1" placeholder="0">
                <div class="input-group-append">
                  <span class="input-group-text">°C</span>
                </div>
              </div>
            </div>
            <div class="form-group mb-0">
              <label class="mb-1">Waktu (dtk)</label>
              <div class="input-group input-group-sm">
                <input type="number" class="form-control" name="${prefix}_${s.key}_waktu" placeholder="0" readonly>
                <div class="input-group-append">
                  <span class="input-group-text">dtk</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>`).join('');
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

        <div class="mt-3 mb-2"><strong>Parameter Akhir & Keterangan</strong></div>
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
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addDetail(${formIdx})">
            <i class="fas fa-plus"></i> Tambah Detail
          </button>
        </div>
      </div>
    </div>`;
  }

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
  }

  function addDetail(formIdx) {
    detailCounters[formIdx] = (detailCounters[formIdx] || 0) + 1;
    const detailIdx = detailCounters[formIdx];
    const container = document.getElementById(`details-container-${formIdx}`);
    const div = document.createElement('div');
    div.innerHTML = detailBlockHTML(formIdx, detailIdx);
    container.appendChild(div.firstElementChild);
  }

  function removeEl(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.transition = 'opacity 0.2s, transform 0.2s';
    el.style.opacity = '0';
    el.style.transform = 'translateY(-8px)';
    setTimeout(() => el.remove(), 200);
  }

  function buildPayload() {
    const forms = [];
    const formCards = document.querySelectorAll('#form-blocks-container .card[id^="form-block-"]');

    formCards.forEach((formCard) => {
      const formId = formCard.getAttribute('id') || '';
      const formIdx = (formId.match(/^form-block-(\d+)$/) || [])[1];
      if (!formIdx) {
        return;
      }

      const tanggalInput = formCard.querySelector(`input[name="f${formIdx}_tanggal"]`);
      const tanggal = tanggalInput ? tanggalInput.value : null;

      const details = [];
      const detailCards = formCard.querySelectorAll(`.card[id^="detail-f${formIdx}_d"]`);

      detailCards.forEach((detailCard) => {
        const detailId = detailCard.getAttribute('id') || '';
        const detailIdx = (detailId.match(new RegExp(`^detail-f${formIdx}_d(\\d+)$`)) || [])[1];
        if (!detailIdx) {
          return;
        }

        const p = `f${formIdx}_d${detailIdx}`;

        const steps = {};
        STEPS.forEach(s => {
          const suhuEl = detailCard.querySelector(`input[name="${p}_${s.key}_suhu"]`);
          const suhuDisplayEl = detailCard.querySelector(`input[name="${p}_${s.key}_suhu_display"]`);
          const waktuEl = detailCard.querySelector(`input[name="${p}_${s.key}_waktu"]`);
          steps[s.key] = {
            suhu: suhuEl ? suhuEl.value : null,
            suhu_display: suhuDisplayEl ? suhuDisplayEl.value : null,
            waktu: waktuEl ? waktuEl.value : null,
          };
        });

        details.push({
          jenis_mouldrum: detailCard.querySelector(`input[name="${p}_jenis_mouldrum"]`)?.value ?? null,
          kondisi_sebelum: detailCard.querySelector(`input[name="${p}_kondisi_sebelum"]`)?.value ?? null,
          kondisi_sesudah: detailCard.querySelector(`input[name="${p}_kondisi_sesudah"]`)?.value ?? null,
          ph_air: detailCard.querySelector(`input[name="${p}_ph_air"]`)?.value ?? null,
          pressure: detailCard.querySelector(`input[name="${p}_pressure"]`)?.value ?? null,
          keterangan: detailCard.querySelector(`textarea[name="${p}_keterangan"]`)?.value ?? null,
          tindakan_koreksi: detailCard.querySelector(`textarea[name="${p}_tindakan_koreksi"]`)?.value ?? null,
          steps,
        });
      });

      forms.push({
        tanggal,
        details,
      });
    });

    return { forms };
  }

  function initFromExistingPayload() {
    const existing = @json($item->payload);
    const forms = existing?.forms || [];
    const container = document.getElementById('form-blocks-container');
    container.innerHTML = '';

    formBlockCount = 0;

    forms.forEach((form) => {
      addFormBlock();

      const currentFormIdx = formBlockCount;
      const tanggalInput = document.querySelector(`input[name="f${currentFormIdx}_tanggal"]`);
      if (tanggalInput && form?.tanggal) {
        tanggalInput.value = form.tanggal;
      }

      const details = form?.details || [];
      if (Array.isArray(details)) {
        const detailsContainer = document.getElementById(`details-container-${currentFormIdx}`);
        if (detailsContainer) {
          detailsContainer.innerHTML = '';
        }
        detailCounters[currentFormIdx] = 0;

        details.forEach((d) => {
          addDetail(currentFormIdx);
          const detailIdx = detailCounters[currentFormIdx];
          const p = `f${currentFormIdx}_d${detailIdx}`;

          document.querySelector(`input[name="${p}_jenis_mouldrum"]`) && (document.querySelector(`input[name="${p}_jenis_mouldrum"]`).value = d?.jenis_mouldrum ?? '');
          document.querySelector(`input[name="${p}_kondisi_sebelum"]`) && (document.querySelector(`input[name="${p}_kondisi_sebelum"]`).value = d?.kondisi_sebelum ?? '');
          document.querySelector(`input[name="${p}_kondisi_sesudah"]`) && (document.querySelector(`input[name="${p}_kondisi_sesudah"]`).value = d?.kondisi_sesudah ?? '');
          document.querySelector(`input[name="${p}_ph_air"]`) && (document.querySelector(`input[name="${p}_ph_air"]`).value = d?.ph_air ?? '');
          document.querySelector(`input[name="${p}_pressure"]`) && (document.querySelector(`input[name="${p}_pressure"]`).value = d?.pressure ?? '');
          document.querySelector(`textarea[name="${p}_keterangan"]`) && (document.querySelector(`textarea[name="${p}_keterangan"]`).value = d?.keterangan ?? '');
          document.querySelector(`textarea[name="${p}_tindakan_koreksi"]`) && (document.querySelector(`textarea[name="${p}_tindakan_koreksi"]`).value = d?.tindakan_koreksi ?? '');

          const steps = d?.steps || {};
          STEPS.forEach(s => {
            const key = s.key;
            const suhuEl = document.querySelector(`input[name="${p}_${key}_suhu"]`);
            const suhuDisplayEl = document.querySelector(`input[name="${p}_${key}_suhu_display"]`);
            const waktuEl = document.querySelector(`input[name="${p}_${key}_waktu"]`);
            if (suhuEl) suhuEl.value = steps?.[key]?.suhu ?? '';
            if (suhuDisplayEl) suhuDisplayEl.value = steps?.[key]?.suhu_display ?? '';
            if (waktuEl) waktuEl.value = steps?.[key]?.waktu ?? '';
          });
        });
      }
    });
  }

  document.getElementById('main-form').addEventListener('submit', function() {
    const payload = buildPayload();
    document.getElementById('payload').value = JSON.stringify(payload);
    const firstTanggal = payload?.forms?.[0]?.tanggal || '';
    document.getElementById('tanggal').value = firstTanggal;
  });

  initFromExistingPayload();
</script>
@endpush

@endsection

