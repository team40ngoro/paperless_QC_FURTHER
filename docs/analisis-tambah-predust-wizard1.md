# Analisis: Menambahkan Step Predust ke Wizard Proses 1

## 1. Latar Belakang

Wizard Proses 1 saat ini adalah **6-step wizard**: Penggorengan → Battering → Breader → Fryer (1-5) → Hasil Penggorengan → IQF.
Wizard Proses 2 memiliki step **Predust** di antara Penggorengan dan Battering.

Goal: Menambahkan Predust ke Wizard Proses 1, sehingga menjadi **7-step wizard**:
Penggorengan → **Predust** → Battering → Breader → Fryer → Hasil Penggorengan → IQF

---

## 2. Status Kesiapan Database — **SUDAH SIAP**

Mayoritas kolom `predust_uuid` sudah ada di tabel-tabel hilir Wizard 1:

| Tabel | Kolom `predust_uuid` | Status |
|-------|----------------------|--------|
| `pembuatan_predust` | `penggorengan_uuid` (exists), `uuid` (PK) | ✅ Siap |
| `proses_battering` | `predust_uuid` (exists) | ✅ Siap |
| `proses_breader` | `predust_uuid` (exists) | ✅ Siap |
| `proses_frayer` | `predust_uuid` (exists) | ✅ Siap |
| `frayer_2` | `predust_uuid` (exists) | ✅ Siap |
| `frayer_3` | `predust_uuid` (exists) | ✅ Siap |
| `frayer_4` | `predust_uuid` (exists) | ✅ Siap |
| `frayer_5` | `predust_uuid` (exists) | ✅ Siap |
| `hasil_penggorengan` | `predust_uuid` (exists) | ✅ Siap |
| `pembekuan_iqf_penggorengan` | `predust_uuid` (exists) | ✅ Siap |

**Tidak perlu migration baru.** Semua kolom UUID sudah ada dari pengembangan Wizard 2 sebelumnya.

---

## 3. File yang Perlu Diubah

### 3.1 Controller: `WizardProses1Controller.php`

#### a. Tambah method `storePredust()` — BARU

Copy dari `WizardProses2Controller@storePredust`, sesuaikan namespace dan route:

```php
public function storePredust(Request $request)
{
    $validator = Validator::make($request->all(), [
        'id_produk' => 'required|exists:jenis_produk,id',
        'id_jenis_predust' => 'required|exists:jenis_predust,id',
        'kode_produksi' => 'required|string|max:255',
        'hasil_predust' => 'required|in:✔,✘',
        'tanggal' => 'required|date',
        'jam' => 'required|date_format:H:i',
        'penggorengan_uuid' => 'required|exists:penggorengan,uuid',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
    }

    $penggorengan = Penggorengan::where('uuid', $request->penggorengan_uuid)->first();

    $predust = PembuatanPredust::create([
        'uuid' => (string) Str::ulid(),
        'id_plan' => auth()->user()->id_plan,
        'user_id' => auth()->id(),
        'penggorengan_uuid' => $request->penggorengan_uuid,
        'id_produk' => $request->id_produk,
        'id_jenis_predust' => $request->id_jenis_predust,
        'tanggal' => Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s'),
        'jam' => $request->jam,
        'kondisi_predust' => $request->hasil_predust,
        'hasil_pencetakan' => $penggorengan->hasil_pencetakan ?? null,
        'kode_produksi' => $request->kode_produksi,
    ]);

    return response()->json(['success' => true, 'message' => 'Data predust berhasil disimpan', 'uuid' => $predust->uuid]);
}
```

#### b. Modifikasi method yang meneruskan `predust_uuid`

Semua method store berikut perlu ditambahkan parameter/field `predust_uuid`:

| Method | Letak | Yang Diubah |
|--------|-------|-------------|
| `storeBattering()` | Terima `predust_uuid` dari request | Validasi opsional, simpan ke model |
| `storeBreader()` | Ambil dari battering atau request | Validasi opsional, simpan ke model |
| `storeFrayer()` | Ambil dari breader atau request | Validasi opsional, simpan ke model |
| `storeHasilPenggorengan()` | Ambil dari frayer/request | Validasi opsional, simpan ke model |
| `storePembekuanIqf()` | Ambil dari hasil/request | Validasi opsional, simpan ke model |

**Pola kode (contoh untuk storeBattering):**
```php
// Terima predust_uuid dari JS state
'predust_uuid' => 'nullable|exists:pembuatan_predust,uuid',

// Simpan ke model
'predust_uuid' => $request->predust_uuid,
```

#### c. Modifikasi `edit()` dan `update()`

- `edit()`: Load data `PembuatanPredust` via `pembekuan->predust_uuid`
- `update()`: Update record `PembuatanPredust` jika `predust_uuid` ada

#### d. Modifikasi `copyToAudit()`

Clone record `PembuatanPredust` sama seperti di Wizard 2, remap UUID-nya.

---

### 3.2 View: `create.blade.php` — Perubahan Terberat

#### a. Step baru (Predust) di antara step 1 dan 2 existing

**Step navigation cards** (perubahan index):
```
Step 1: Penggorengan    (sebelum: 1)  →  Step 1: Penggorengan
Step 2: Battering       (sebelum: 2)  →  Step 2: Predust        ← BARU
Step 3: Breader         (sebelum: 3)  →  Step 3: Battering
Step 4: Fryer           (sebelum: 4)  →  Step 4: Breader
Step 5: Hasil Pengg.    (sebelum: 5)  →  Step 5: Fryer
Step 6: IQF             (sebelum: 6)  →  Step 6: Hasil Penggorengan
                                      →  Step 7: IQF
```

#### b. Template HTML step Predust

Copy dari Wizard 2 create.blade.php (sekitar baris 129-169), sesuaikan:
- Route: `wizard.proses1.store-predust` (baru)
- Icon: `fa-mortar-pestle`
- State key: `wizard_proses1_state_v1.predust_uuid`

```html
<div class="step" id="step-2">
    <!-- Predust Form -->
    <input type="hidden" id="predust_penggorengan_uuid" name="penggorengan_uuid">
    <div class="form-group"><label>Tanggal</label><input type="date" ...></div>
    <div class="form-group"><label>Jam</label><input type="time" ...></div>
    <div class="form-group"><label>Jenis Predust</label><select id="jenis_predust" ...></select></div>
    <div class="form-group"><label>Kode Produksi</label><input type="text" ...></div>
    <div class="form-group"><label>Hasil Predust</label><select ...></select></div>
    <button onclick="skipPredust()">Lewati</button>
    <button onclick="submitPredust()">Simpan & Lanjut</button>
</div>
```

#### c. Update JavaScript `state` object

State object di sessionStorage perlu field baru:
```js
let state = {
    penggorengan_uuid: null,
    predust_uuid: null,        // BARU
    battering_uuid: null,
    breader_uuid: null,
    frayer_uuids: [],
    hasil_penggorengan_uuid: null,
    pembekuan_uuid: null,
    currentStep: 1
};
```

#### d. JS function `submitPredust()` (baru)

```js
function submitPredust() {
    let data = {
        penggorengan_uuid: state.penggorengan_uuid,
        id_produk: $('#id_produk').val(),
        id_jenis_predust: $('#jenis_predust').val(),
        kode_produksi: $('#kode_produksi_predust').val(),
        hasil_predust: $('#hasil_predust').val(),
        tanggal: $('#predust_tanggal').val(),
        jam: $('#predust_jam').val(),
        _token: csrfToken
    };
    $.post('{{ route("wizard.proses1.store-predust") }}', data)
        .done(res => { state.predust_uuid = res.uuid; goToStep(3); })
        .fail(err => { alert('Gagal: ' + err.responseJSON.message); });
}
```

#### e. Fungsi `skipPredust()` (baru)

```js
function skipPredust() {
    state.predust_uuid = null;
    goToStep(3);
}
```

#### f. Update validasi step

Validasi di tombol "Simpan & Lanjut" untuk step setelah Predust (Battering → ...) harus:
- Periksa `state.predust_uuid` dan kirim sebagai field request

---

### 3.3 Route: `routes/web.php`

Tambah route baru setelah route `store-battering`:

```php
Route::post('/wizard/proses-1/store-predust', [WizardProses1Controller::class, 'storePredust'])
    ->name('wizard.proses1.store-predust');
```

---

### 3.4 View: `export_pdf.blade.php`

Tambahkan section baru antara section A (Penggorengan) dan B (Battering):

```blade
@if($item->predust_uuid && $item->predustData)
<tr><td colspan="4"><strong>B. PREDUST</strong></td></tr>
<tr>
    <td>Jenis Predust</td>
    <td>{{ $item->predustData->jenisPredust->jenis_predust ?? '-' }}</td>
    <td>Kode Produksi</td>
    <td>{{ $item->predustData->kode_produksi ?? '-' }}</td>
</tr>
<tr>
    <td>Hasil Predust</td>
    <td colspan="3">{{ $item->predustData->kondisi_predust ?? '-' }}</td>
</tr>
@endif
```

**Renumber** section B → C, C → D, D → E, E → F, F → G.

---

### 3.5 View: `show.blade.php`

Tambah card Predust antara Penggorengan dan Battering:

```blade
@if($pembekuan->predust_uuid && $pembekuan->predustData)
<div class="card card-info">
    <div class="card-header"><h3>Predust</h3></div>
    <div class="card-body">
        <p>Jenis Predust: {{ $pembekuan->predustData->jenisPredust->jenis_predust ?? '-' }}</p>
        <p>Kode Produksi: {{ $pembekuan->predustData->kode_produksi ?? '-' }}</p>
        <p>Hasil: {{ $pembekuan->predustData->kondisi_predust ?? '-' }}</p>
    </div>
</div>
@else
<div class="card card-secondary">
    <div class="card-header"><h3>Predust (Dilewati)</h3></div>
</div>
@endif
```

---

### 3.6 View: `edit.blade.php`

Tambah section Predust (copy dari Wizard 2 edit view). Sama seperti show: jika `predust_uuid` ada, tampilkan form edit; jika tidak, tampilkan "(Dilewati)".
Update juga logika update di controller untuk menyimpan perubahan Predust.

---

## 4. Ringkasan Perubahan per File

| File | Tipe Perubahan |
|------|---------------|
| `app/Http/Controllers/WizardProses1Controller.php` | Tambah method `storePredust()`, modif 5 store methods lain, modif `edit()`/`update()`/`copyToAudit()` |
| `resources/views/qc-sistem/wizard_proses1/create.blade.php` | Tambah step 2 HTML + JS, renumber step 3-7, update state object, update step navigation |
| `routes/web.php` | Tambah 1 route POST |
| `resources/views/qc-sistem/wizard_proses1/export_pdf.blade.php` | Tambah section Predust, renumber sections B-G |
| `resources/views/qc-sistem/wizard_proses1/show.blade.php` | Tambah card Predust |
| `resources/views/qc-sistem/wizard_proses1/edit.blade.php` | Tambah form Predust |

**Database migration: TIADA** — semua kolom `predust_uuid` sudah ada.

---

## 5. Risiko Spesifik

| Risiko | Dampak | Mitigasi |
|--------|--------|----------|
| **Step index mismatch** | Step navigation broken, data salah simpan | Hitung ulang semua index step 1-7, update JS `step-${n}` selector |
| **State object tidak kompatibel** | Data existing session storage corrupt | Gunakan key `wizard_proses1_state_v2` (beda versi) |
| **Skip predust ≠ data null** | Query downstream filter salah | Pastikan `predust_uuid` dikirim sebagai `null` ketika dilewati |
| **Lupa update export PDF** | Predust tidak muncul di cetakan | Tambah section bersamaan dengan create view |
| **Lupa update audit copy** | Data audit kehilangan predust | Update `copyToAudit()` clone logic |

---

## 6. Estimasi Waktu Pengerjaan

| Area | Estimasi |
|------|----------|
| Controller: method `storePredust()` | 30 menit |
| Controller: modif 5 store methods | 30 menit |
| Controller: edit/update/copyToAudit | 30 menit |
| View create: step HTML + JS | 2 jam |
| View show/edit/export PDF | 1 jam |
| Route | 5 menit |
| **Total** | **~4.5 jam** |

Waktu terbesar ada di view `create.blade.php` karena harus renumber semua step dan update JS logic.
