# Analisis Arsitektur Paperless Futher

## 1. Ringkasan Proyek

Aplikasi Laravel untuk QC/QA sistem produksi pangan — mencatat **GMP karyawan**, **wizard produksi** (penggorengan → IQF / roasting → IQF / tumbling → aging), audit data, approval berjenjang, dan export PDF/Excel.

**Stack:** PHP 8.x + Laravel 10/11, MySQL, Blade + AdminLTE 3, jQuery + Select2 + SweetAlert2, DomPDF, Maatwebsite Excel.

---

## 2. Arsitektur Direktori

```
app/
├── Exports/                    # Export Excel (GmpKaryawanTemplateExport, dll)
├── Http/
│   ├── Controllers/
│   │   ├── GmpKaryawanController.php
│   │   ├── WizardProses{1..4}Controller.php
│   │   ├── WizardPackingController.php
│   │   ├── Traits/
│   │   │   └── HasAuditController.php    # Trait audit reusable
│   │   └── ... (40+ controller lain)
│   └── Middleware/
│       └── CheckQcPermission.php
├── Imports/                    # Import Excel (GmpKaryawanImport, dll)
├── Models/
│   ├── GmpKaryawan.php
│   ├── GmpKaryawanLog.php
│   ├── Penggorengan.php, ProsesBattering.php, ProsesBreader.php ...
│   ├── PembekuanIqfPenggorengan.php, PembekuanIqfRoasting.php
│   ├── BahanBakuTumbling.php, ProsesTumbling.php, ProsesAging.php
│   ├── Traits/
│   │   └── HasAudit.php        # Trait audit + scope operasional/audit
│   └── ... (30+ model lain)
├── Observers/
│   └── GmpKaryawanObserver.php # Logging perubahan via observer
└── ...
resources/views/
└── qc-sistem/
    ├── gmp_karyawan/           # CRUD + audit + export views
    ├── wizard_proses1/         # 6-step wizard
    ├── wizard_proses2/         # 8-step wizard
    ├── wizard_proses3/         # 4-step wizard
    ├── wizard_proses4/         # 3-step wizard
    ├── wizard/                 # Halaman pemilihan wizard
    └── ...
routes/
└── web.php                    # Semua route (no API routes)
database/
└── migrations/                # ~100+ migration files
```

---

## 3. Alur Request (Request Lifecycle)

```
Browser → routes/web.php → Middleware (auth, permission) → Controller → Model → DB
                                                                    ↓
                                                              View (Blade) → Response
```

### 3.1 Alur CRUD Standar (GMP Karyawan)
```
GET  /qc-sistem/gmp-karyawan          → index()     → menampilkan daftar
GET  /qc-sistem/gmp-karyawan/create   → create()    → form input
POST /qc-sistem/gmp-karyawan          → store()     → validasi → simpan → redirect
GET  /qc-sistem/gmp-karyawan/{uuid}   → show()      → detail
GET  /qc-sistem/gmp-karyawan/{uuid}/edit → edit()   → form edit
PUT  /qc-sistem/gmp-karyawan/{uuid}   → update()    → validasi → update → redirect
DELETE /qc-sistem/gmp-karyawan/{uuid} → destroy()   → hapus → redirect
```

### 3.2 Alur Wizard Produksi

Setiap wizard adalah **satu halaman create.blade.php** dengan multiple steps yang di-toggle via JavaScript (`$('.step').hide() / show()`).

#### Data Flow Antar Step (UUID Chain):
```
Step 1: POST /wizard/proses-1/store-penggorengan
        → Simpan ke tabel `penggorengan`
        → Return UUID

Step 2: POST /wizard/proses-1/store-battering
        → Validasi penggorengan_uuid ada
        → Simpan ke tabel `proses_battering` (dengan penggorengan_uuid)
        → Return UUID
        ...

Step N: POST /wizard/proses-1/store-iqf
        → Simpan ke tabel final `pembekuan_iqf_penggorengan`
        → Simpan SEMUA UUID dari step sebelumnya
        → Final record, no more steps
```

### 3.3 Alur Approval
```
POST /wizard/proses-1/{uuid}/approve { type: 'qc' }
  → Validasi role user (QC = role 1,3,5)
  → Cek sequential: QC → Produksi → SPV
  → Update approved_by_qc = true, approved_by_qc_at = now(), approved_by_qc_user_id = user.id
  → Return JSON response
```

---

## 4. Model-Model yang Terlibat

### 4.1 Model Final & Step Models per Wizard

| Wizard | Step Models | Final Model | Jumlah Step |
|--------|------------|-------------|-------------|
| **Proses 1** (Frying → IQF) | Penggorengan, ProsesBattering, ProsesBreader, ProsesFrayer/Frayer2-5, HasilPenggorengan | `PembekuanIqfPenggorengan` | 6 |
| **Proses 2** (Frying → Roast → IQF) | Penggorengan, PembuatanPredust, ProsesBattering, ProsesBreader, ProsesFrayer/Frayer2-5, ProsesRoastingFan, HasilProsesRoasting | `PembekuanIqfRoasting` | 8 |
| **Proses 3** (Roast → IQF) | InputRoasting, ProsesRoastingFan, HasilProsesRoasting | `PembekuanIqfRoasting` | 4 |
| **Proses 4** (Tumbl → Aging) | BahanBakuTumbling, ProsesTumbling | `ProsesAging` | 3 |
| **Packing** | PengemasanProduk, PengemasanPlastik, BeratProduk, PengemasanKarton | `Dokumentasi` | 5 |

### 4.2 Shared Models
- **Penggorengan** — Wizard 1 & 2
- **ProsesBattering, ProsesBreader, ProsesFrayer/Frayer2-5** — Wizard 1 & 2
- **ProsesRoastingFan, HasilProsesRoasting** — Wizard 2 & 3
- **PembekuanIqfRoasting** — Wizard 2 & 3 (dibedakan via `penggorengan_uuid` vs `input_roasting_uuid`)

### 4.3 Model Penunjang
- **GmpKaryawan, GmpKaryawanLog** — inspeksi karyawan + log perubahan
- **Plan, DataShift, InputArea** — master data plan/shift/area
- **User** — autentikasi + role-based access

---

## 5. Risiko Menambahkan Step Baru pada Wizard Produksi

Berdasarkan analisis kode, berikut risiko yang harus diantisipasi:

### Risiko 5.1 — UUID Chain Putus (HIGH)
Setiap step menyimpan UUID dari step sebelumnya sebagai foreign key. Jika step baru **disisipkan di tengah**, semua model step setelahnya perlu ditambahi kolom UUID untuk step baru tersebut.

**Contoh nyata:** `pembekuan_iqf_penggorengan` menyimpan `penggorengan_uuid`, `battering_uuid`, `breader_uuid`, `frayer_uuid`, `frayer2_uuid`–`frayer5_uuid`, `hasil_penggorengan_uuid`. Setiap penambahan step di tengah berarti migration untuk update **semua model hilir**.

### Risiko 5.2 — View create.blade.js Perlu Direstruktur (HIGH)
Semua step di-render dalam **satu file** `create.blade.php` yang panjangnya bisa >1000 baris. JS toggle step (`$('.step-1').hide()`, dsb) harus diperbarui. Rentan human error — lupa update index atau salah referensi data attribute.

### Risiko 5.3 — Controller Method & Route Baru (MEDIUM)
Setiap step punya method `storeNamaStep()` sendiri di controller. Perlu:
- Method baru di controller
- Route baru di `web.php`
- Validasi data dari step sebelumnya (`previous_uuid` required)

### Risiko 5.4 — Migration & Rollback (MEDIUM)
Harus buat migration baru untuk:
- Tabel step baru (jika belum ada)
- Kolom UUID di model final (dan semua model hilir)
- Rollback `down()` harus drop kolom-kolom tersebut secara urut

### Risiko 5.5 — Approval Flow Tidak Terpengaruh (LOW)
Approval bekerja di **model final** (bukan per-step). Selama UUID chain tetap utuh ke final model, approval tidak perlu diubah.

### Risiko 5.6 — Export PDF Perlu Update (MEDIUM)
View `export_pdf.blade.php` menampilkan semua data dari chain UUID. Step baru harus ditambahkan ke PDF view agar muncul di laporan. Jika lupa, data step baru tidak akan tercetak.

### Risiko 5.7 — Audit Copy (MEDIUM)
Trait `HasAudit::copyToAudit()` melakukan `replicate()` pada model final. Jika step baru tidak di-replicate (misalnya step baru adalah relasi belongsTo yang tidak otomatis ikut), data audit akan kehilangan data step baru.

### Risiko 5.8 — Edit Flow Kompleksitas (MEDIUM)
Method `edit()` dan `update()` di wizard controller sudah kompleks. Menambahkan step baru berarti:
- Load data step baru di edit form
- Validasi update untuk step baru
- Handling partial update (beberapa step sudah diisi, beberapa belum)

### Risiko 5.9 — Test Coverage (HIGH — Tidak Ada)
Project ini **tidak memiliki test** (tidak ditemukan file test untuk wizard). Setiap perubahan hanya bisa diuji manual. Risiko regresi sangat tinggi.

### Risiko 5.10 — Migrasi Data Lama (LOW-MEDIUM)
Jika step baru bersifat wajib, data lama yang sudah lengkap (sebelum step baru ada) menjadi tidak lengkap. Perlu strategi: default value, nullable, atau skip untuk data lama.

---

## 6. Rekomendasi

1. **Tambahkan step di AKHIR chain** (sebelum final model) — risiko paling rendah.
2. **Buat kolom baru sebagai nullable** di model final — tidak memaksa data lama diisi.
3. **Gunakan migration terpisah** untuk setiap perubahan — tidak campur aduk.
4. **Update view PDF bersamaan** — jangan terpisah sprint.
5. **Prioritaskan test manual** untuk regresi wizard setelah perubahan.
6. **Buat dokumentasi UUID chain** setiap wizard — agar developer berikutnya paham relasi.
