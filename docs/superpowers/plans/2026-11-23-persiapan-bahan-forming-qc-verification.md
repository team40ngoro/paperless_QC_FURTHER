# Persiapan Bahan Forming QC Verification Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Menambahkan form verifikasi QC ("Hasil Pencampuran" dropdown dan 1 kolom input number "Suhu Actual") serta mekanisme persetujuan langsung SPV QC pada halaman detail (`show.blade.php`) Persiapan Bahan Forming mengikuti pola modul Pemeriksaan Kedatangan.

**Architecture:**
- Tambahkan endpoint `storeQcVerification` pada `PersiapanBahanFormingController` dan daftarkan route POST `persiapan-bahan-forming/{uuid}/qc-verification`.
- Perbarui method `approve` pada `PersiapanBahanFormingController` agar SPV QC dapat langsung menyetujui segera setelah QC Inspector memverifikasi tanpa harus menunggu SPV Produksi.
- Ubah `resources/views/qc-sistem/persiapan_bahan_forming/show.blade.php` dengan menambahkan Card Verifikasi QC (form interaktif untuk QC Inspector / tampilan status jika sudah diverifikasi) dan Card/Action Persetujuan SPV QC.

**Tech Stack:** Laravel (Blade, Eloquent ORM, Controller), JavaScript (AJAX/Fetch & DOM)

**Spec:** Kebutuhan pengguna: pada `persiapan_bahan_forming/show.blade.php`, khusus QC Inspector dapat menginput Hasil Pencampuran (Oke/Tidak Oke) dan 1 kolom Suhu Actual (number), dan SPV QC langsung dapat menyetujui data tersebut.

## Global Constraints
- Menggunakan pola modul Pemeriksaan Kedatangan (Chillroom & Bahan Kemas).
- Hasil Pencampuran bernilai '✔' (Oke) atau '✘' (Tidak Oke), disimpan ke `kondisi_hasil_pencampuran`.
- Suhu Actual bernilai numeric, disimpan ke `AktualSuhuAdonan` (`aktual_suhu_1` dan `total_aktual_suhu`).
- Approval SPV QC dapat dilakukan langsung setelah QC Inspector melakukan verifikasi.

## Review Focus
- Validasi role: Hanya role QC Inspector (atau Superadmin/Admin) yang dapat submit Verifikasi QC.
- Validasi role SPV QC: Hanya role SPV QC (atau Superadmin/Admin) yang dapat melakukan persetujuan SPV QC.
- Validasi prasyarat SPV QC: SPV QC tidak dapat menyetujui jika data belum diverifikasi oleh QC Inspector.
- Feedback UI: Tampilan alert status yang jelas setelah verifikasi atau persetujuan berhasil.

---

### Task 1: Backend Controller & Route untuk Verifikasi QC & Relaksasi Approval SPV QC

**Files:**
- Modify: `routes/web.php`
- Modify: `app/Http/Controllers/PersiapanBahanFormingController.php`

**Interfaces:**
- Produces: Route `persiapan-bahan-forming.qc-verification` (`POST /qc-sistem/persiapan-bahan-forming/{uuid}/qc-verification`)
- Produces: Method `PersiapanBahanFormingController::storeQcVerification(Request $request, $uuid)`
- Modifies: Method `PersiapanBahanFormingController::approve(Request $request, $uuid)` untuk type `'spv'` agar mengecek `approved_by_qc` (bukan `approved_by_produksi`).

- [x] **Step 1: Daftarkan Route Verifikasi QC di `routes/web.php`**
  Tambahkan:
  ```php
  Route::post('persiapan-bahan-forming/{uuid}/qc-verification', [PersiapanBahanFormingController::class, 'storeQcVerification'])
      ->name('persiapan-bahan-forming.qc-verification');
  ```

- [x] **Step 2: Implementasikan `storeQcVerification` di `PersiapanBahanFormingController.php`**
  - Validasi request: `kondisi_hasil_pencampuran` (required, in:✔,✘,OK,NOT_OK), `suhu_actual` (required, numeric).
  - Cek permission user (QC Inspector / Superadmin / Admin).
  - Simpan `kondisi_hasil_pencampuran` ke `PersiapanBahanForming`.
  - Simpan/Update `AktualSuhuAdonan` dengan `aktual_suhu_1 = $request->suhu_actual` dan `total_aktual_suhu = $request->suhu_actual`.
  - Update approval QC: `approved_by_qc = true`, `qc_approved_by = $user->id`, `qc_approved_at = now()`.
  - Catat log ke `PersiapanBahanFormingLog`.
  - Redirect kembali dengan pesan sukses.

- [x] **Step 3: Sesuaikan syarat approval SPV QC pada method `approve` di `PersiapanBahanFormingController.php`**
  Ubah validasi sequential untuk type `'spv'` agar hanya membutuhkan `!$persiapanBahan->approved_by_qc`:
  ```php
  if ($type === 'spv' && !$persiapanBahan->approved_by_qc) {
      return response()->json([
          'success' => false,
          'message' => 'Menunggu verifikasi QC Inspektor terlebih dahulu'
      ], 400);
  }
  ```

- [x] **Step 4: Verifikasi sintaks PHP**
  Jalankan: `php -l routes/web.php && php -l app/Http/Controllers/PersiapanBahanFormingController.php`

---

### Task 2: Implementasi Card Verifikasi QC & Approval SPV QC di `show.blade.php`

**Files:**
- Modify: `resources/views/qc-sistem/persiapan_bahan_forming/show.blade.php`

**Interfaces:**
- Consumes: `$data` (instance `PersiapanBahanForming`), route `persiapan-bahan-forming.qc-verification`, route `persiapan-bahan-forming.approve`.

- [x] **Step 1: Identifikasi Akses Role di Bagian Atas `show.blade.php`**
  ```php
  @php
      $currentUser = auth()->user();
      $isSuperadmin = $currentUser->role === 'superadmin' || in_array((int)($currentUser->id_role ?? 0), [1, 5], true);
      $isQcInspector = $isSuperadmin || $currentUser->isQcInspector() || (int)($currentUser->id_role ?? 0) === 3 || $currentUser->isQc();
      $isSpvQc = $isSuperadmin || (int)($currentUser->id_role ?? 0) === 4 || str_contains(strtolower($currentUser->roleModel->role ?? $currentUser->role ?? ''), 'spv qc');
      $aktualSuhuVal = optional($data->aktualSuhuAdonan)->total_aktual_suhu ?? optional($data->aktualSuhuAdonan)->aktual_suhu_1 ?? '';
  @endphp
  ```

- [x] **Step 2: Tambahkan Card "Verifikasi QC"**
  - Letakkan di bawah tabel suhu forming / sebelum atau di dalam status alur verifikasi.
  - Jika `$data->approved_by_qc` sudah bernilai `true`:
    Tampilkan banner status verifikasi:
    - Status: Terverifikasi oleh QC
    - Hasil Pencampuran: badge Oke (✔) / Tidak Oke (✘)
    - Suhu Actual: `{{ $aktualSuhuVal }} °C`
    - Waktu & Verifikator: `{{ $data->qcApprover->name }} pada {{ $data->qc_approved_at }}`
  - Jika belum diverifikasi dan user memiliki role QC:
    Tampilkan form verifikasi:
    - Input `kondisi_hasil_pencampuran`: dropdown dengan opsi `✔ - Oke` dan `✘ - Tidak Oke`.
    - Input `suhu_actual`: input type `number` step `0.1` placeholder `Masukkan suhu actual (°C)`.
    - Tombol: `<button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i> Simpan Verifikasi QC</button>`.
  - Jika belum diverifikasi dan user bukan QC:
    Tampilkan alert info: "Data ini belum diverifikasi oleh QC Inspektor."

- [x] **Step 3: Tambahkan Tombol Persetujuan Langsung SPV QC**
  - Pada bagian status alur persetujuan tahap 4 (Disetujui = SPV QC) atau pada card tersendiri:
  - Jika `$data->approved_by_spv` sudah true: tampilkan badge disetujui beserta nama & tanggal.
  - Jika belum disetujui, dan `$data->approved_by_qc` sudah true:
    - Jika user memiliki role SPV QC (`$isSpvQc`): tampilkan tombol "Setujui sebagai SPV QC" yang memanggil endpoint `approve` type `'spv'`.
    - Jika user bukan SPV QC: tampilkan teks "Menunggu persetujuan SPV QC".
  - Jika `$data->approved_by_qc` belum true:
    Tampilkan teks "Menunggu verifikasi QC Inspektor sebelum dapat disetujui SPV QC".

- [x] **Step 4: Tambahkan JavaScript Handler untuk Tombol Approve SPV QC**
  Gunakan fetch/AJAX untuk memanggil `{{ route('persiapan-bahan-forming.approve', $data->uuid) }}` dengan parameter `{ type: 'spv', _token: '...' }` dan lakukan reload halaman saat sukses.

---

### Task 3: Verifikasi Fungsionalitas & Alur Keseluruhan

- [x] **Step 1: Periksa sintaks Blade & PHP**
  Pastikan tidak ada syntax error pada controller, route, maupun view.
- [x] **Step 2: Uji konsistensi data**
  Pastikan nilai `kondisi_hasil_pencampuran` dan `total_aktual_suhu` tersinkronisasi di tampilan read-only dan card status verifikasi.
