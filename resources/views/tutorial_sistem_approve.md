# Tutorial Sistem Approval Checkbox-Based (Per-Row Selection)
# Sistem Paperless WH — QC Sistem

---

## Apa itu Approval Checkbox-Based?

Approval Checkbox-Based adalah sistem verifikasi yang memungkinkan user untuk:
- **Memilih data spesifik** menggunakan checkbox di setiap baris
- **Approve multiple data sekaligus** yang sudah dipilih
- **Flexible approval** tanpa perlu approve semua data dalam satu shift/range
- **Smart auto-skip** data yang sudah di-approve sebelumnya

Sistem ini menggantikan approval per-shift yang lama dengan pendekatan yang lebih fleksibel dan user-friendly.

---

## Siapa yang Bisa Menggunakan?

| Role | Aksi yang Bisa Dilakukan via Multi Approval |
|------|---------------------------------------------|
| **QC Inspector** | Mengirim semua data `pending` ke Produksi/Warehouse |
| **Produksi** | Menyetujui semua data `sent_to_produksi` |
| **Warehouse** | Menyetujui semua data `sent_to_produksi` |
| **Produksi/Warehouse** | Menyetujui semua data `sent_to_produksi` |
| **SPV QC** | Menyetujui semua data `approved_produksi` (verifikasi final) |
| **Superadmin** | Dapat melakukan semua tahap verifikasi massal |

> Sistem otomatis menentukan aksi berdasarkan role yang login — tidak perlu memilih manual.

---

## Cara Menggunakan Approval Checkbox-Based

### Langkah 1 — Buka Halaman Daftar Pemeriksaan

Masuk ke salah satu modul pemeriksaan, contoh:
- QC Sistem → Pemeriksaan Kedatangan Bahan Baku (Chillroom)
- QC Sistem → Verifikasi Proses Rebox
- QC Sistem → Pemeriksaan Seasoning
- *(dan modul lainnya yang sudah diupdate)*

---

### Langkah 2 — Filter/Search Data (Opsional)

Gunakan search box untuk menyaring data yang ingin di-approve:

```
┌─────────────────────────────────────────────┐
│  🔍 Cari data...              [Tampilkan: 25]│
│                                              │
│  ☐  No  Shift  Tanggal  Produk  Status  Aksi │
│  ☐  1   Shift1 14/09/26 Product A  ○○○  [👁]│
│  ☐  2   Shift1 14/09/26 Product B  ●○○  [👁]│
│  ☐  3   Shift2 14/09/26 Product C  ●●○  [👁]│
└─────────────────────────────────────────────┘
```

- Gunakan **search text** untuk filter berdasarkan nama produk, kode, dll
- Ubah **Tampilkan** (5, 10, 25, 50) untuk mengatur jumlah rows per halaman

---

### Langkah 3 — Pilih Data dengan Checkbox

**Cara Memilih Data:**

**Opsi A: Pilih Individual**
- Klik checkbox di setiap baris yang ingin di-approve
- Checkbox akan tercentang ✓

**Opsi B: Select All (Pilih Semua)**
- Klik checkbox di header tabel (paling atas)
- Semua data di halaman current akan terpilih

```
┌─────────────────────────────────────────────┐
│  ☑  No  Shift  Tanggal  Produk  Status  Aksi │← Click ini = Select All
│  ☑  1   Shift1 14/09/26 Product A  ○○○  [👁]│
│  ☑  2   Shift1 14/09/26 Product B  ●○○  [👁]│
│  ☑  3   Shift2 14/09/26 Product C  ●●○  [👁]│
└─────────────────────────────────────────────┘
```

---

### Langkah 4 — Tombol Approval Muncul Otomatis

Setelah minimal 1 checkbox dicentang, **tombol approval akan muncul** di bagian atas card (card-header):

```
┌─────────────────────────────────────────────────────────┐
│  📋 Pemeriksaan Kedatangan Bahan Baku                    │
│                     [📄 Export] [🕒 Audit] [➕ Tambah]   │
├─────────────────────────────────────────────────────────┤
│  ℹ️ 3 dipilih                                            │
│  [✓ Approve QC]  [✓ Approve Produksi]  [✓ Approve SPV] │← TOMBOL MUNCUL!
└─────────────────────────────────────────────────────────┘
```

**Tombol yang muncul tergantung role Anda:**
- **Role QC** → Hanya tombol "Approve QC"
- **Role Produksi** → Hanya tombol "Approve Produksi"
- **Role SPV** → Hanya tombol "Approve SPV"
- **Superadmin** → Semua tombol tampil

**Badge "X dipilih"** menunjukkan jumlah data yang sudah dicentang.

---

### Langkah 5 — Klik Tombol Approve Sesuai Role

1. Klik tombol approve sesuai role Anda (misalnya "Approve QC")
2. Konfirmasi muncul: *"Apakah Anda yakin ingin menyetujui 3 data sebagai QC?"*
3. Klik **OK** untuk proses

**Sistem akan:**
- ✅ Approve data yang dipilih dan **belum di-approve** sebelumnya
- ⏭️ **Skip otomatis** data yang sudah di-approve (TIDAK tertimpa)
- 📝 Catat semua approval ke audit log
- 🔄 Refresh tabel dan tampilkan notifikasi hasil

---

### Langkah 6 — Lihat Hasil

Notifikasi sukses akan muncul dengan **jumlah data yang BERHASIL di-approve** (bukan total yang dipilih):

```
✅ Berhasil verifikasi 2 data QC
```

**Contoh:** Anda pilih 5 data, tapi 3 data sudah di-approve sebelumnya → Hasil: "Berhasil 2 data"

**Checkbox akan otomatis uncheck** setelah approval berhasil, dan status approval di tabel akan update.

---

---

## ⚡ Keunggulan Sistem Checkbox-Based

### Dibandingkan Sistem Range-Date Lama:

| Aspek | Sistem Range-Date ❌ | Sistem Checkbox-Based ✅ |
|-------|---------------------|-------------------------|
| **Seleksi Data** | Harus approve SEMUA data dalam range | Pilih data spesifik yang diinginkan |
| **Fleksibilitas** | Kaku, berbasis tanggal | Sangat fleksibel, per-row |
| **UI/UX** | Modal popup terpisah | Langsung di halaman, inline |
| **Visual Feedback** | Tidak ada indicator selection | Checkbox visual + badge counter |
| **Speed** | Perlu buka modal, isi tanggal | Langsung centang & approve |
| **Mistake Prevention** | Bisa salah pilih range | Jelas lihat data mana yang dipilih |
| **Partial Approval** | Tidak bisa (all or nothing) | Bisa approve sebagian saja |

---

---

## Cara Kerja Sistem Checkbox-Based

### 1. Client-Side Selection (JavaScript)

```javascript
// Array untuk track UUID yang dipilih
let selectedUuids = [];

// Event: User centang checkbox
checkbox.onChange → add UUID to selectedUuids[]

// Event: User uncheck → remove UUID from array

// Update UI:
if (selectedUuids.length > 0) {
    - Tampilkan approval buttons
    - Update badge counter
} else {
    - Hide approval buttons
}
```

**Keuntungan:**
- ⚡ Instant feedback (tidak perlu AJAX)
- 🎯 User control penuh
- 👀 Visual jelas (lihat berapa dipilih)

---

### 2. Server-Side Smart Processing (PHP/Laravel)

Saat user klik tombol "Approve [Role]":

```php
POST /chillroom/bulk-approve-selected
{
    "type": "qc",
    "uuids": ["uuid1", "uuid2", "uuid3", ...]
}

// Backend Process:
1. Validasi: role, permission, UUID format
2. Query: WHERE uuid IN (...) AND approved_by_qc = false
3. Update: Hanya data yang approved_by_qc = false
4. Log: Catat setiap approval ke audit trail
5. Return: { success: true, message: "Berhasil 2 data" }
```

**Smart Auto-Skip:**
```php
// Hanya ambil yang BELUM approved
$query = Model::whereIn('uuid', $uuids)
    ->where('approved_by_' . $type, false); // ← KEY!
    
$itemsToUpdate = $query->get();

// Jika count = 0 → semua sudah approved
if ($count === 0) {
    return "Semua data sudah diverifikasi sebelumnya";
}
```

---

### 3. Approval Flow (Flexible, No Sequential Check)

```
USER ACTION:
├─ Select checkbox row 1, 2, 5, 8
├─ Click "Approve QC"
└─ Confirm dialog

BACKEND:
├─ Filter: WHERE uuid IN (1,2,5,8) AND approved_by_qc=false
├─ Result: row 1, 5 (row 2 & 8 sudah approved, di-skip)
├─ Update: row 1 & 5 → approved_by_qc=true
├─ Log: 2 entries untuk row 1 & 5
└─ Return: "Berhasil verifikasi 2 data QC"

UI REFRESH:
├─ Uncheck all checkboxes
├─ Hide approval buttons
├─ Show success notification
└─ Reload table (status badge update)
```

**Tidak Ada Sequential Check:**
- ✅ Produksi bisa approve SEBELUM QC lengkap
- ✅ SPV bisa approve SEBELUM Produksi lengkap
- ✅ Setiap role independent
- ⏭️ Data yang syarat belum terpenuhi → di-skip otomatis

---

## Contoh Skenario Real-World

### Contoh 1 — Approve Beberapa Data Saja (Partial Approval)

**Situasi:** Halaman menampilkan 10 data pemeriksaan

**User Action:**
1. Search "Chicken Nugget" → filter 10 data jadi 4 data
2. Centang checkbox row 1 dan 3 saja (2 data)
3. Klik "Approve QC"

**Hasil:**
```
✅ Row 1: approved_by_qc = true, qc_approved_by = UserA
✅ Row 3: approved_by_qc = true, qc_approved_by = UserA
⬜ Row 2: tetap belum approved
⬜ Row 4: tetap belum approved
```

**Notifikasi:** "Berhasil verifikasi 2 data QC"

**Keuntungan:** User bisa approve secara bertahap, tidak harus semua sekaligus!

---

### Contoh 2 — Select All dengan Data Mixed Status

**Situasi:** Halaman menampilkan 25 data dengan status mixed:
- 10 data: belum approved sama sekali (○○○)
- 8 data: sudah approved QC (●○○)
- 7 data: sudah approved QC + Produksi (●●○)

**User (Role QC) Action:**
1. Klik checkbox "Select All" di header
2. Semua 25 checkbox tercentang ✓
3. Badge menunjukkan "25 dipilih"
4. Klik "Approve QC"

**Backend Process:**
```php
// Query: WHERE uuid IN (25 uuids) AND approved_by_qc = false
// Result: Hanya 10 data yang approved_by_qc = false

// Update 10 data tersebut
// 15 data lainnya di-SKIP (sudah approved)
```

**Hasil:**
```
✅ 10 data: approved_by_qc = true (baru di-approve)
⏭️ 8 data: SKIP (sudah approved QC oleh user lain, TIDAK tertimpa)
⏭️ 7 data: SKIP (sudah approved QC oleh user lain, TIDAK tertimpa)
```

**Notifikasi:** "Berhasil verifikasi 10 data QC"

**Poin Penting:** Meskipun user select 25 data, hanya 10 yang di-update. Nama approver lama (8+7 data) **TIDAK tertimpa**!

---

### Contoh 3 — Multi-Role Approval Parallel

**Situasi:** 50 data dalam database

**Timeline:**
```
09:00 - User QC (Budi):
        ├─ Select 20 data
        ├─ Approve QC
        └─ ✅ 20 data: qc_approved_by = Budi

10:00 - User Produksi (Ani):
        ├─ Search & select 15 data (yang sudah approved QC)
        ├─ Approve Produksi
        └─ ✅ 15 data: produksi_approved_by = Ani
            ⏭️ 5 data di-skip (QC belum approve)

11:00 - User SPV (Siti):
        ├─ Select All 50 data
        ├─ Approve SPV
        └─ ✅ 12 data: spv_approved_by = Siti (yang Produksi sudah approve)
            ⏭️ 38 data di-skip (Produksi belum approve)

14:00 - User QC lain (Roni):
        ├─ Select 30 data yang belum approved QC
        ├─ Approve QC
        └─ ✅ 30 data: qc_approved_by = Roni
```

**Final State:**
- QC: 50/50 (20 by Budi + 30 by Roni)
- Produksi: 15/50 (15 by Ani)
- SPV: 12/50 (12 by Siti)

**Keuntungan:**
- ✅ Tidak perlu koordinasi antar role
- ✅ Bisa bekerja parallel
- ✅ Tidak ada conflict/overwrite
- ✅ Audit trail jelas siapa approve apa

---

### Contoh 4 — Pagination & Cross-Page Selection

**Situasi:** 100 data total, tampilkan 25 per page (4 halaman)

**User Action:**
```
Page 1: Select 10 data → selectedUuids = [uuid1...uuid10]
Page 2: Select 5 data  → selectedUuids = [uuid1...uuid10, uuid11...uuid15]
Page 3: Select 8 data  → selectedUuids = [uuid1...uuid10, uuid11...uuid15, uuid16...uuid23]
Page 1: Back, klik "Approve QC"
```

**Hasil:**
✅ Sistem approve semua 23 UUID yang ter-track di `selectedUuids[]`
✅ Cross-page selection berfungsi (tidak hilang saat pindah page)

**Implementasi:**
```javascript
// selectedUuids disimpan di memory JavaScript
// Tidak hilang saat AJAX reload table
// Checkbox state di-restore setelah pagination
```

---

## Hal-hal yang Perlu Diperhatikan

### ✅ Approval Behavior

> **Data yang sudah approved OTOMATIS DI-SKIP.**  
> Sistem smart mendeteksi dan skip data yang sudah approved. Nama approver original **TIDAK TERTIMPA**.

> **Badge counter menunjukkan jumlah SELECTED, bukan APPROVED.**  
> Badge "5 dipilih" = Anda centang 5 checkbox, BUKAN 5 data akan di-approve. Jumlah yang benar-benar di-approve bisa lebih kecil (karena auto-skip).

> **Notifikasi menunjukkan jumlah BERHASIL di-approve.**  
> "Berhasil 3 data" = Hanya 3 data yang benar-benar di-update (yang lain di-skip karena sudah approved).

> **Checkbox otomatis uncheck setelah approval.**  
> Setelah approval berhasil, semua checkbox akan otomatis uncheck dan tombol approval akan hilang lagi.

### ⚙️ Permission & Access Control

> **Tombol approval hanya muncul untuk role yang berhak.**  
> Role-based visibility:
> - **QC** (Role: 1, 3, 5) → Tombol "Approve QC"
> - **Produksi** (Role: 1, 2, 5) → Tombol "Approve Produksi"
> - **SPV** (Role: 1, 4, 5) → Tombol "Approve SPV"
> - **Superadmin** (Role: 1) → Semua tombol

> **Plant-based filtering otomatis.**  
> User hanya bisa approve data dari plant/id_plan yang sama. Superadmin bisa approve data dari semua plant.

### 🔍 Search & Filter

> **Search filter TIDAK mempengaruhi selected UUIDs.**  
> Jika Anda select 10 data, lalu search & filter menampilkan 3 data saja, selected UUIDs tetap 10. Saat approve, semua 10 UUID akan diproses.

> **Pagination TIDAK menghapus selection.**  
> Selected UUIDs disimpan di memory JavaScript. Anda bisa select di page 1, pindah ke page 2, select lagi, lalu approve semua sekaligus.

### 🚫 Limitations

> **Tidak ada undo.**  
> Setelah approval diproses, tidak ada fitur batal/undo. Pastikan data yang dipilih sudah benar sebelum klik approve.

> **Select All hanya untuk halaman current.**  
> Checkbox "Select All" di header hanya memilih data yang tampil di halaman current, bukan seluruh database.

> **Maksimal data yang bisa diproses tergantung timeout server.**  
> Jika terlalu banyak data dipilih (misalnya ribuan), proses bisa timeout. Disarankan approve dalam batch (beberapa puluh data per kali).

### 🔐 Audit Trail

> **Setiap approval tercatat di log.**  
> Setiap kali data di-approve, sistem mencatat:
> - UUID data yang di-approve
> - User yang melakukan approval
> - Role user
> - Timestamp approval
> - IP address & user agent
> - Field yang diubah (nilai lama → nilai baru)

> **Log dapat diakses via menu Audit/History.**  
> Klik tombol 🕒 History di pojok kanan untuk melihat semua perubahan data.

---

## Modul yang Sudah Menggunakan Sistem Checkbox-Based

| Modul | Status | Keterangan |
|-------|--------|------------|
| Pemeriksaan Kedatangan Bahan Baku (Chillroom) | ✅ AKTIF | Implementasi reference, fully tested |
| Verifikasi Proses Rebox | ✅ AKTIF | Same pattern as Chillroom |
| Pemeriksaan Seasoning | ✅ AKTIF | Same pattern as Chillroom |
| Pemeriksaan Shoestring | ✅ AKTIF | Same pattern as Chillroom |
| Pemeriksaan Suhu Ruang (CS) | 📋 Planned | Akan diupdate |
| Pemeriksaan Suhu Ruang (CS Meat) | 📋 Planned | Akan diupdate |
| Pemeriksaan Suhu Ruang (Gudang Dry) | 📋 Planned | Akan diupdate |
| Pemeriksaan Kedatangan Kemasan | 📋 Planned | Akan diupdate |
| Pemeriksaan Bahan Kemas | ✅ AKTIF | Same pattern as Chillroom |
| Pemeriksaan Kedatangan Bahan Baku Penunjang | 📋 Planned | Akan diupdate |
| Pemeriksaan Kedatangan Chemical | 📋 Planned | Akan diupdate |
| Pemeriksaan Produk Finish Good | 📋 Planned | Akan diupdate |
| Pemeriksaan Loading Produk | 📋 Planned | Akan diupdate |
| Pemeriksaan Loading Kendaraan | 📋 Planned | Akan diupdate |
| Pemeriksaan Return Barang Customer | 📋 Planned | Akan diupdate |
| Pemeriksaan Kebersihan Area | 📋 Planned | Akan diupdate |
| Pemeriksaan Barang Mudah Pecah | 📋 Planned | Akan diupdate |

**Catatan:** Modul yang belum diupdate masih menggunakan sistem Multi Approval (range-date) lama.

---
## Perbandingan: Sistem Range-Date vs Checkbox-Based

| Aspek | Range-Date (Lama) ❌ | Checkbox-Based (Baru) ✅ |
|-------|---|---|
| **User Experience** | Modal popup, isi tanggal | Langsung centang di tabel |
| **Seleksi Data** | All data dalam range (tidak fleksibel) | Pilih data spesifik yang diinginkan |
| **Visual Feedback** | Tidak ada indicator | Checkbox ✓ + badge counter |
| **Speed** | 3-4 klik (buka modal, isi, submit) | 2 klik (centang, approve) |
| **Partial Approval** | Tidak bisa, harus all or nothing | Bisa approve sebagian saja |
| **Mistake Prevention** | Bisa salah range | Jelas lihat data yang dipilih |
| **Cross-Page Selection** | N/A (approve per range) | Bisa select di berbeda page |
| **Auto-Skip Logic** | Ya (smart) | Ya (smart, sama) |
| **Audit Trail Safety** | Aman (tidak tertimpa) | Aman (tidak tertimpa) |
| **Mobile Friendly** | Kurang (modal kecil) | Lebih baik (checkbox besar) |
| **Sequential Check** | Tidak ada (flexible) | Tidak ada (flexible) |
| **Button Visibility** | Always visible | Hidden until selection |

### Backward Compatibility

**Modul yang sudah diupdate:**
- Sistem lama (range-date) DIHAPUS
- Hanya ada checkbox-based
- Database structure sama (tidak ada migration)
- Data approval lama tetap utuh

**Modul yang belum diupdate:**
- Tetap pakai range-date
- Akan diupdate bertahap
- Tidak ada breaking changes

---

## Troubleshooting

### Q: Checkbox tidak muncul di tabel?
**A:** Pastikan modul sudah diupdate ke sistem checkbox-based. Lihat tabel "Modul yang Sudah Menggunakan Sistem Checkbox-Based" di atas.

### Q: Tombol approval tidak muncul padahal sudah centang checkbox?
**A:** Periksa:
1. Apakah badge counter "X dipilih" muncul? Jika ya, tombol seharusnya muncul di bawahnya
2. Apakah role Anda memiliki permission untuk approve? (QC, Produksi, SPV)
3. Coba refresh halaman dan centang ulang

### Q: Badge menunjukkan "5 dipilih", tapi hasil hanya "2 data berhasil"?
**A:** Ini normal! Artinya dari 5 data yang Anda pilih:
- 2 data belum approved → berhasil di-approve ✅
- 3 data sudah approved sebelumnya → di-skip otomatis ⏭️

### Q: Saya approve data, tapi hasilnya "0 data"?
**A:** Semua data yang Anda pilih sudah di-approve sebelumnya. Sistem auto-skip dan tidak ada yang baru di-approve.

### Q: Checkbox hilang setelah search/filter?
**A:** Checkbox state akan di-restore setelah AJAX reload. Jika tidak, berarti ada bug JavaScript. Report ke developer.

### Q: Select All tidak memilih semua data di database?
**A:** Select All hanya memilih data yang **tampil di halaman current**, bukan seluruh database. Jika ingin approve semua data, gunakan search kosong + per_page maksimal, lalu Select All.

### Q: Nama approver lama tidak muncul lagi, apakah tertimpa?
**A:** Tidak! Data yang sudah approve akan di-skip. Periksa:
1. Filter search mungkin menyembunyikan data tersebut
2. Lihat di halaman Audit untuk konfirmasi approver original

### Q: Bagaimana jika ingin ubah approver yang sudah approved?
**A:** Sistem tidak support "re-approve" untuk protect audit trail. Jika perlu ubah:
1. Hubungi Superadmin
2. Manual edit via database (harus ada justifikasi kuat)
3. Alternatif: Buat data baru dengan catatan "revisi"

### Q: Approval gagal dengan error 403 "Tidak memiliki otoritas"?
**A:** Role Anda tidak match dengan tipe approval. Contoh:
- Anda role Produksi, tapi klik "Approve QC" → Error
- Pastikan klik tombol yang sesuai dengan role Anda

### Q: Approval gagal dengan error 500?
**A:** Server error. Kemungkinan:
1. Timeout (terlalu banyak data dipilih)
2. Database connection issue
3. Bug di backend
**Solusi:** Coba lagi dengan jumlah data lebih sedikit. Jika masih error, report ke developer.

---

## Fitur-Fitur Utama Sistem Checkbox-Based

✅ **Checkbox Per-Row** - Setiap baris data memiliki checkbox untuk selection granular  
✅ **Smart Auto-Skip** - Data yang sudah approved otomatis di-skip (tidak tertimpa)  
✅ **Preserve Original Approver** - Nama & timestamp approver original tetap aman  
✅ **Flexible Order** - Bisa approve dalam urutan apa saja (no sequential check)  
✅ **Role-Based Visibility** - Tombol approval hanya muncul untuk role yang berhak  
✅ **Dynamic UI** - Tombol approval hidden until selection (clean interface)  
✅ **Badge Counter** - Real-time feedback jumlah data yang dipilih  
✅ **Cross-Page Selection** - Selection tidak hilang saat pagination  
✅ **Complete Logging** - Setiap approval tercatat di audit trail  
✅ **Accurate Reporting** - Notifikasi menunjukkan jumlah yang BENAR-BENAR di-approve  
✅ **Plant-Based Access** - Auto-filter berdasarkan plant user (security)  
✅ **Mobile Friendly** - Checkbox cukup besar untuk touch screen  
✅ **No Sequential Blocking** - Produksi & SPV bisa approve tanpa tunggu role sebelumnya  

---

## Implementasi Teknis untuk Developer

### 1. Frontend Structure (Blade Template)

**Card Header dengan Approval Buttons:**

```blade
<div class="card-header bg-white py-3">
    <!-- Toolbar Row: Title + Action Buttons -->
    <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 0.75rem;">
        <h3 class="card-title text-bold mb-0">
            <i class="fas fa-list text-info mr-2"></i> Data Pemeriksaan
        </h3>
        <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
            <!-- Export, Audit, Create buttons -->
        </div>
    </div>

    <!-- Approval Buttons Row: Hidden by default, show when selection > 0 -->
    <div class="approval-buttons-group d-none">
        @php
            $userRole = auth()->user()->id_role;
            $canApproveQc = in_array($userRole, [1, 3, 5]);
            $canApproveProduksi = in_array($userRole, [1, 2, 5]);
            $canApproveSPV = in_array($userRole, [1, 4, 5]);
        @endphp

        <span class="badge badge-info"><span class="selected-count">0</span> dipilih</span>

        @if($canApproveQc)
            <button type="button" class="btn btn-sm btn-success shadow-sm approve-selected-btn" data-type="qc">
                <i class="fas fa-check mr-1"></i> Approve QC
            </button>
        @endif
        @if($canApproveProduksi)
            <button type="button" class="btn btn-sm btn-info shadow-sm approve-selected-btn" data-type="produksi">
                <i class="fas fa-check mr-1"></i> Approve Produksi
            </button>
        @endif
        @if($canApproveSPV)
            <button type="button" class="btn btn-sm btn-warning shadow-sm approve-selected-btn" data-type="spv" style="color: white;">
                <i class="fas fa-check mr-1"></i> Approve SPV
            </button>
        @endif
    </div>
</div>
```

**Table Header dengan Select-All Checkbox:**

```blade
<thead class="bg-light text-uppercase small font-weight-bold border-top">
    <tr>
        <th class="text-center py-3" style="width: 40px;">
            <input type="checkbox" id="selectAllCheckbox" class="form-check-input" title="Pilih semua">
        </th>
        <th class="text-center py-3" style="width: 50px;">No</th>
        <!-- Other columns -->
    </tr>
</thead>
```

**Table Body dengan Per-Row Checkbox:**

```blade
<tbody id="tableBody">
    @forelse($records as $groupKey => $groupItems)
        @foreach($groupItems as $index => $item)
            <tr class="item-row">
                <td class="text-center">
                    <input type="checkbox" class="row-checkbox" data-uuid="{{ $item->uuid }}">
                </td>
                <td class="text-center">{{ $loop->iteration }}</td>
                <!-- Other columns -->
            </tr>
        @endforeach
    @empty
        <tr><td colspan="12" class="text-center">Tidak ada data</td></tr>
    @endforelse
</tbody>
```

**CSS Styling:**

```css
.row-checkbox {
    width: 18px;
    height: 18px;
    margin: 0;
    cursor: pointer;
}

.approval-buttons-group {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.75rem;
    padding: 0.6rem 0.9rem;
    background-color: #eef6fc;
    border: 1px solid #cfe6f5;
    border-radius: 6px;
}
```

---

### 2. JavaScript Logic (jQuery)

```javascript
$(document).ready(function() {
    let selectedUuids = [];

    // Update UI counter & button visibility
    function updateSelectedCount() {
        const count = selectedUuids.length;
        $('.selected-count').text(count);
        
        if (count > 0) {
            $('.approval-buttons-group').removeClass('d-none');
        } else {
            $('.approval-buttons-group').addClass('d-none');
        }
    }

    // Handle individual checkbox
    $(document).on('change', '.row-checkbox', function() {
        const uuid = $(this).data('uuid');
        
        if ($(this).is(':checked')) {
            if (!selectedUuids.includes(uuid)) {
                selectedUuids.push(uuid);
            }
        } else {
            selectedUuids = selectedUuids.filter(id => id !== uuid);
        }
        
        updateSelectedCount();
    });

    // Handle select-all checkbox
    $('#selectAllCheckbox').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);
        
        if (isChecked) {
            $('.row-checkbox').each(function() {
                const uuid = $(this).data('uuid');
                if (!selectedUuids.includes(uuid)) {
                    selectedUuids.push(uuid);
                }
            });
        } else {
            selectedUuids = [];
        }
        
        updateSelectedCount();
    });

    // Restore checkbox states after AJAX reload
    function updateCheckboxStates() {
        selectedUuids.forEach(uuid => {
            $(`input[data-uuid="${uuid}"]`).prop('checked', true);
        });
        updateSelectedCount();
    }

    // Handle approval button click
    $(document).on('click', '.approve-selected-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const type = btn.data('type');

        if (selectedUuids.length === 0) {
            alert('Silakan pilih minimal 1 data untuk disetujui.');
            return;
        }

        if (!confirm(`Apakah Anda yakin ingin menyetujui ${selectedUuids.length} data sebagai ${type.toUpperCase()}?`)) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: "{{ route('module.bulk-approve-selected') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                type: type,
                uuids: selectedUuids
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        selectedUuids = [];
                        performSearch(); // Reload table
                    });
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Approve ' + type);
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan';
                Swal.fire('Error!', msg, 'error');
                btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Approve ' + type);
            }
        });
    });
});
```

---

### 3. Backend Controller Method

```php
public function bulkApproveSelected(Request $request)
{
    try {
        $user = auth()->user();
        $type = $request->input('type'); // qc, produksi, spv
        $uuids = $request->input('uuids', []); // array of UUIDs

        // Validate
        $request->validate([
            'type' => 'required|in:qc,produksi,spv',
            'uuids' => 'required|array|min:1',
            'uuids.*' => 'required|uuid'
        ]);

        // Role check
        $userRole = $user->id_role;
        $allowedRoles = [
            'qc' => [1, 3, 5],
            'produksi' => [1, 2, 5],
            'spv' => [1, 4, 5]
        ];

        if (!in_array($userRole, $allowedRoles[$type] ?? [])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki otoritas untuk verifikasi ini.'
            ], 403);
        }

        // Find records - ONLY unapproved ones (smart auto-skip)
        $query = YourModel::whereIn('uuid', $uuids)
            ->where('approved_by_' . $type, false)
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

        $itemsToUpdate = $query->get();
        $count = $itemsToUpdate->count();

        if ($count === 0) {
            return response()->json([
                'success' => true,
                'message' => 'Semua data sudah diverifikasi sebelumnya.'
            ]);
        }

        // Update
        $updateData = [
            'approved_by_' . $type => true,
            $type . '_approved_by' => $user->id,
            $type . '_approved_at' => now(),
        ];

        YourModel::whereIn('uuid', $uuids)
            ->where('approved_by_' . $type, false)
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            })
            ->update($updateData);

        // Log each approval
        foreach ($itemsToUpdate as $item) {
            YourModelLog::create([
                'uuid' => (string) Str::uuid(),
                'your_model_id' => $item->id,
                'your_model_uuid' => $item->uuid,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role ?? 'Unknown',
                'aksi' => 'bulk_approval_selected',
                'field_yang_diubah' => ['approved_by_' . $type],
                'nilai_lama' => ['approved_by_' . $type => false],
                'nilai_baru' => ['approved_by_' . $type => true],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'keterangan' => 'Bulk Approved Selected by ' . strtoupper($type)
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil verifikasi ' . $count . ' data ' . strtoupper($type)
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
```

---

### 4. Route Definition

```php
// routes/web.php
Route::post('/module/bulk-approve-selected', [YourController::class, 'bulkApproveSelected'])
    ->name('module.bulk-approve-selected')
    ->middleware(['auth', 'permission:approve-module']);
```

---

### 5. Audit Page (No Checkbox)

Untuk halaman audit, sembunyikan checkbox dengan conditional:

```blade
<thead>
    <tr>
        @unless(isset($isAuditPage) && $isAuditPage)
            <th class="text-center" style="width: 40px;">
                <input type="checkbox" id="selectAllCheckbox">
            </th>
        @endunless
        <th>No</th>
        <!-- Other columns -->
    </tr>
</thead>

<tbody>
    @foreach($records as $item)
        <tr>
            @unless(isset($isAuditPage) && $isAuditPage)
                <td class="text-center">
                    <input type="checkbox" class="row-checkbox" data-uuid="{{ $item->uuid }}">
                </td>
            @endunless
            <td>{{ $loop->iteration }}</td>
            <!-- Other columns -->
        </tr>
    @endforeach
</tbody>
```

Pass `$isAuditPage = true` dari controller untuk halaman audit.

---

*Tutorial ini dibuat berdasarkan implementasi aktual sistem Paperless WH.*  
*Terakhir diperbarui: September 2026 (Sistem Checkbox-Based Approval)*  
*Versi: 3.0 (Major Update - Checkbox-Based Selection with Smart Auto-Skip)*  
*Reference Implementation: Chillroom & Rebox Modules*


## UI Design Guidelines untuk Implementasi

### Consistency Requirements

Untuk memastikan UI konsisten di semua modul, ikuti guideline ini:

#### 1. **Card Header Structure** (2 baris terpisah)

```
Baris 1: Title (kiri) | Action Buttons (kanan)
Baris 2: Approval Buttons Group (hidden by default)
```

**BENAR ✅:**
```html
<div class="card-header">
    <div class="d-flex justify-content-between">
        <h3>Title</h3>
        <div class="card-tools">Export/Audit/Create</div>
    </div>
    <div class="approval-buttons-group d-none">
        <!-- Approval buttons here -->
    </div>
</div>
```

**SALAH ❌:**
```html
<div class="card-header">
    <div style="flex...">
        <div>
            <h3>Title</h3>
            <div class="approval-buttons">...</div> <!-- ❌ Nested in same flex -->
        </div>
        <div class="card-tools">...</div>
    </div>
</div>
```

#### 2. **Checkbox Column** (First column, 40px width)

```html
<th class="text-center py-3" style="width: 40px;">
    <input type="checkbox" id="selectAllCheckbox" class="form-check-input" title="Pilih semua">
</th>
```

- ✅ Width: 40px
- ✅ Class: `form-check-input`
- ✅ ID: `selectAllCheckbox` (for select-all)
- ✅ Class: `row-checkbox` (for individual rows)
- ✅ Attribute: `data-uuid="{{ $item->uuid }}"`

#### 3. **Checkbox Size** (18x18px, consistent)

```css
.row-checkbox {
    width: 18px;
    height: 18px;
    margin: 0 !important;
    cursor: pointer;
}
```

#### 4. **Approval Buttons Group** (Grey box with border)

```css
.approval-buttons-group {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.75rem;
    padding: 0.6rem 0.9rem;
    background-color: #eef6fc;
    border: 1px solid #cfe6f5;
    border-radius: 6px;
}
```

#### 5. **Button Order** (Badge first, then buttons by role)

```
ℹ️ [X dipilih] [✓ Approve QC] [✓ Approve Produksi] [✓ Approve SPV]
```

**Order yang benar:**
1. Badge info dengan counter
2. Approve QC (hijau, `btn-success`)
3. Approve Produksi (biru, `btn-info`)
4. Approve SPV (kuning, `btn-warning`)

#### 6. **Thead Colspan** (Adjust based on audit mode)

```blade
{{-- Index page: with checkbox --}}
<tr><td colspan="12">...</td></tr>

{{-- Audit page: without checkbox --}}
<tr><td colspan="11">...</td></tr>
```

**Formula:** `total_columns = base_columns + (has_checkbox ? 1 : 0)`

#### 7. **Table Structure** (Keep grouped loop, remove headers)

```blade
@forelse($records as $groupKey => $groupItems)
    {{-- ❌ TIDAK PERLU group header --}}
    @foreach($groupItems as $item)
        <tr class="item-row">
            <td>...</td>
        </tr>
    @endforeach
@empty
    <tr><td colspan="12">No data</td></tr>
@endforelse
```

**Catatan:** Tetap pakai grouped loop (karena searchAjax return grouped), tapi JANGAN render group headers.

---

### Color Scheme Standards

| Role | Button Class | Color Hex | Icon |
|------|-------------|-----------|------|
| QC | `btn-success` | #28a745 | `fa-check` |
| Produksi | `btn-info` | #17a2b8 | `fa-check` |
| SPV | `btn-warning` | #ffc107 | `fa-check` |
| Badge | `badge-info` | #17a2b8 | - |

### Spacing Standards

| Element | Property | Value |
|---------|----------|-------|
| Approval buttons group | `margin-top` | 0.75rem |
| Approval buttons group | `padding` | 0.6rem 0.9rem |
| Buttons gap | `gap` | 0.5rem |
| Card-tools gap | `gap` | 0.5rem |
| Checkbox size | `width/height` | 18px |
| Checkbox column width | `width` | 40px |

---

### JavaScript Pattern (Standard Template)

```javascript
// Global variables
let selectedUuids = [];

// Functions (exact naming)
function updateSelectedCount() { ... }
function updateCheckboxStates() { ... }
function performSearch(page = 1) { ... }

// Event handlers (exact selectors)
$(document).on('change', '.row-checkbox', ...)
$('#selectAllCheckbox').on('change', ...)
$(document).on('click', '.approve-selected-btn', ...)
```

**Critical:** Gunakan **exact same** class names dan IDs untuk JavaScript compatibility!

---

### Backend Pattern (Standard Template)

```php
// Method name: bulkApproveSelected (exact)
public function bulkApproveSelected(Request $request)

// Parameters:
'type' => 'required|in:qc,produksi,spv'
'uuids' => 'required|array|min:1'
'uuids.*' => 'required|uuid'

// Role mapping (exact):
$allowedRoles = [
    'qc' => [1, 3, 5],
    'produksi' => [1, 2, 5],
    'spv' => [1, 4, 5]
];

// Query pattern (exact):
->where('approved_by_' . $type, false) // ← MUST HAVE for auto-skip

// Response format:
return response()->json([
    'success' => true,
    'message' => 'Berhasil verifikasi X data TYPE'
]);
```

---

### Route Naming Convention

```php
Route::post('/module-name/bulk-approve-selected', ...)
    ->name('module-name.bulk-approve-selected');
```

**Examples:**
- `chillroom.bulk-approve-selected`
- `rebox.bulk-approve-selected`
- `seasoning.bulk-approve-selected`
- `shoestring.bulk-approve-selected`

---

### Testing Checklist

Sebelum deploy ke production, test scenario berikut:

- [ ] Checkbox muncul di kolom pertama (40px width)
- [ ] Select-all checkbox berfungsi (centang semua di page current)
- [ ] Individual checkbox berfungsi (centang per row)
- [ ] Badge counter update real-time saat checkbox change
- [ ] Approval buttons muncul saat selectedCount > 0
- [ ] Approval buttons hilang saat selectedCount = 0
- [ ] Tombol approval hanya muncul untuk role yang berhak
- [ ] Konfirmasi dialog muncul saat klik approve
- [ ] Success notification muncul setelah approval
- [ ] Table reload setelah approval (status update)
- [ ] Checkbox auto-uncheck setelah approval
- [ ] Auto-skip data yang sudah approved (tidak tertimpa)
- [ ] Count result sesuai (hanya yang baru di-approve)
- [ ] Cross-page selection berfungsi (tidak hilang saat pagination)
- [ ] Search/filter tidak menghapus selectedUuids
- [ ] Role-based permission berfungsi (403 error jika tidak authorized)
- [ ] Plant-based filtering berfungsi (hanya approve data di plant sendiri)
- [ ] Audit page TIDAK menampilkan checkbox
- [ ] Thead colspan sesuai (index vs audit)
- [ ] Mobile responsive (checkbox cukup besar untuk touch)

---
