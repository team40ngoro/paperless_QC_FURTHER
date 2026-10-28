# Panduan Implementasi Fitur Audit (is_audit)

> **Referensi kode nyata dari project ini.**
> Gunakan `ShoestringController` dan modul `seasoning` sebagai template utama.

## Daftar Isi
1. [Overview & Konsep](#1-overview--konsep)
2. [Struktur File & Komponen Global](#2-struktur-file--komponen-global)
3. [Komponen Blade: `<x-action-buttons>`](#3-komponen-blade-x-action-buttons)
4. [Komponen Blade: `<x-audit-dropdown>`](#4-komponen-blade-x-audit-dropdown)
5. [Trait Model: `HasAudit`](#5-trait-model-hasaudit)
6. [Trait Controller: `HasAuditController`](#6-trait-controller-hasauditcontroller)
7. [Langkah Implementasi ke Modul Baru](#7-langkah-implementasi-ke-modul-baru)
8. [Referensi Modul Selesai](#8-referensi-modul-selesai)
9. [FAQ & Troubleshooting](#9-faq--troubleshooting)
10. [Checklist Implementasi](#10-checklist-implementasi)

---

## 1. Overview & Konsep

Fitur audit memungkinkan data operasional di-duplikasi menjadi **salinan audit terpisah**.
Salinan audit dapat diedit oleh auditor tanpa mengubah data operasional asli.

### Alur Data
```
[Data Operasional]  is_audit = false
        |
        | user klik "Copy ke Audit"
        v
[Data Audit]        is_audit = true, source_uuid = uuid_asli
        |
        | auditor edit di halaman index_audit
        v
[Data Audit Final]  tersimpan, data asli tidak berubah
```

### Kolom Tambahan di Tabel
| Kolom | Tipe | Default | Keterangan |
|-------|------|---------|------------|
| `is_audit` | `boolean` | `false` | Penanda data audit |
| `source_uuid` | `string\|null` | `null` | UUID data asli yang disalin |

### Role & Akses
| id_role | Nama | Akses Audit |
|---------|------|-------------|
| 1 | Admin | Copy ke audit, edit audit, lihat semua |
| 2 | Produksi (Fm/fl) | Lihat audit |
| 3 | QC Inspector | Copy ke audit, lihat audit |
| 4 | SPV QC | Lihat audit |
| 5 | Superadmin | Akses penuh |
| **6** | **AUDITOR** | **Redirect otomatis ke index_audit, view-only** |


---

## 2. Struktur File & Komponen Global

```
app/
├── Models/Traits/
│   └── HasAudit.php                          ← Trait untuk Model
├── Http/Controllers/Traits/
│   └── HasAuditController.php                ← Trait untuk Controller
├── View/Components/
│   ├── ActionButtons.php                     ← Class Blade Component action buttons
│   └── AuditDropdown.php                     ← Class Blade Component audit dropdown

resources/views/
├── components/
│   ├── action-buttons.blade.php              ← View action buttons
│   └── audit-dropdown.blade.php              ← View audit dropdown
└── qc-sistem/
    └── {modul}/
        ├── _table.blade.php                  ← Tabel (dipakai oleh index & index_audit)
        ├── index.blade.php                   ← Halaman operasional
        └── index_audit.blade.php             ← Halaman audit (buat baru)

config/
└── edit_limit.php                            ← Konfigurasi batas waktu edit per role
```

---

## 3. Komponen Blade: `<x-action-buttons>`

**File class:** `app/View/Components/ActionButtons.php`
**File view:** `resources/views/components/action-buttons.blade.php`

### Cara Penggunaan

```blade
{{-- Minimal --}}
<x-action-buttons :item="$item" route-prefix="seasoning" />

{{-- Lengkap dengan semua parameter --}}
<x-action-buttons
    :item="$item"
    route-prefix="seasoning"
    permission-prefix="seasoning"
    :show-history="true"
    :show-view="true"
    :show-print="false"
    layout="horizontal"
/>
```

### Parameter

| Parameter | Tipe | Default | Keterangan |
|-----------|------|---------|------------|
| `:item` | Model | *wajib* | Object Eloquent model dengan field `uuid` |
| `route-prefix` | string | *wajib* | Prefix route, contoh: `'seasoning'`, `'shoestring'` |
| `permission-prefix` | string | sama dengan route-prefix | Prefix permission jika berbeda dari route |
| `:show-history` | bool | `true` | Tampilkan tombol riwayat (route: `{prefix}.logs`) |
| `:show-view` | bool | `true` | Tampilkan tombol lihat detail (route: `{prefix}.show`) |
| `:show-print` | bool | `false` | Tampilkan tombol cetak PDF |
| `layout` | string | `'horizontal'` | `'horizontal'` atau `'vertical'` |

### Tombol yang Ditampilkan

| Tombol | Warna | Route | Permission Dibutuhkan |
|--------|-------|-------|-----------------------|
| 👁 Lihat | `btn-info` | `{prefix}.show` | `view-{prefix}` |
| ✏️ Edit | `btn-warning` | `{prefix}.edit` | `edit-{prefix}` |
| 🔒 Edit (expired) | `btn-secondary` disabled | - | `edit-{prefix}` (expired) |
| 📄 PDF | `btn-danger` | `{prefix}.export-pdf` atau `{prefix}.pdf` | `edit-{prefix}` |
| 📋 History | `btn-secondary` | `{prefix}.logs` | `view-{prefix}` |
| 🗑 Hapus | `btn-danger` | `{prefix}.destroy` | `delete-{prefix}` |

### Logic Akses (dari kode nyata)

```php
// canAccess() — menampilkan group tombol
Auth::user()->hasPermissionTo('view-' . $prefix)
|| Auth::user()->hasPermissionTo('edit-' . $prefix)
|| Auth::user()->hasPermissionTo('delete-' . $prefix)

// isViewOnlyRole() — hanya tampilkan tombol "Lihat"
hasPermissionTo('view-') && !hasPermissionTo('edit-') && !hasPermissionTo('delete-')

// canEdit() — cek batas waktu dari config/edit_limit.php
// Jika id_role ada di restricted_role_ids DAN modul ada di modules[]
// → cek apakah created_at + time_limit_hours masih future
```

### Badge Edit yang Ditampilkan di Bawah Tombol

- **"Waktu Edit Tersisa: Xh"** — jika masih bisa edit dan ada batas waktu (badge `badge-secondary`)
- **"Unlimited Edit"** — jika bisa edit tanpa batas waktu (badge `badge-success`)
- **"Expired To Edit"** — jika waktu edit habis (badge `badge-danger`)


---

## 4. Komponen Blade: `<x-audit-dropdown>`

**File class:** `app/View/Components/AuditDropdown.php`
**File view:** `resources/views/components/audit-dropdown.blade.php`

### Cara Penggunaan

```blade
{{-- Letakkan setelah x-action-buttons di _table.blade.php --}}
<x-audit-dropdown :item="$item" route-prefix="seasoning" />

{{-- Dengan permission prefix berbeda (opsional) --}}
<x-audit-dropdown :item="$item" route-prefix="seasoning" permission-prefix="seasoning" />
```

### Contoh Lengkap di `_table.blade.php`

```blade
<td class="text-center align-middle">
    <x-action-buttons :item="$item" route-prefix="seasoning" :show-history="true" :show-view="true" />
    {{-- Global Audit Dropdown --}}
    <x-audit-dropdown :item="$item" route-prefix="seasoning" />
</td>
```

### Parameter

| Parameter | Tipe | Default | Keterangan |
|-----------|------|---------|------------|
| `:item` | Model | *wajib* | Object dengan field `uuid`, `is_audit`, relasi `auditVersion`, `originalVersion` |
| `route-prefix` | string | *wajib* | Prefix route, contoh: `'seasoning'` |
| `permission-prefix` | string | sama dengan route-prefix | Override prefix permission |

### Routes yang Dibutuhkan

Komponen ini mengecek keberadaan route sebelum render:

```
{prefix}.copy-to-audit  → diperlukan agar dropdown muncul
{prefix}.show           → diperlukan agar dropdown muncul
{prefix}.edit           → opsional, untuk tombol edit audit
```

> **Jika salah satu dari `.copy-to-audit` atau `.show` tidak ada, dropdown tidak akan render sama sekali.**

### Isi Dropdown Berdasarkan Kondisi

**Kondisi 1: Data asli (`is_audit = false`), belum ada salinan audit**
```
┌─────────────────────────────┐
│  📋 Copy ke Audit           │  ← hanya muncul jika punya 'create-{prefix}'
└─────────────────────────────┘
```

**Kondisi 2: Data asli (`is_audit = false`), sudah ada salinan audit**
```
┌──────────────────────────────┐
│  🛡 Data Audit Tersedia      │  ← header (teks ungu)
│  🔍 Lihat Hasil Audit        │  ← link ke show audit
│  ✏️  Edit Data Audit          │  ← hanya jika punya 'edit-{prefix}'
└──────────────────────────────┘
```

**Kondisi 3: Data audit (`is_audit = true`) — di halaman index_audit**
```
┌──────────────────────────────┐
│  ⚠️  Mode Audit               │  ← header (teks kuning)
│  ✏️  Edit Audit Ini           │  ← hanya jika punya 'edit-{prefix}'
└──────────────────────────────┘
```

### Tampilan Visual

Tombol trigger dropdown:
```html
<button class="btn btn-default btn-sm dropdown-toggle shadow-sm">
    <i class="fas fa-cog text-purple"></i>  <!-- ikon gear warna ungu #6f42c1 -->
</button>
```


---

## 5. Trait Model: `HasAudit`

**File:** `app/Models/Traits/HasAudit.php`

> **File ini sudah ada, jangan diubah.** Cukup `use` di model Anda.

### Kode Lengkap File

```php
<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasAudit
{
    /**
     * Get the audit version of this record (if any)
     */
    public function auditVersion()
    {
        return $this->hasOne(static::class, 'source_uuid', 'uuid')->where('is_audit', true);
    }

    /**
     * Get the original version of this audit record
     */
    public function originalVersion()
    {
        return $this->belongsTo(static::class, 'source_uuid', 'uuid')->where('is_audit', false);
    }

    /**
     * Copy this record to audit version
     */
    public function copyToAudit(): self
    {
        $clone = $this->replicate();
        $clone->uuid = (string) Str::uuid();
        $clone->is_audit = true;
        $clone->source_uuid = $this->uuid;

        // Reset approval boolean fields to false (NOT NULL constraint)
        $booleanApprovalFields = [
            'approved_by_qc',
            'approved_by_produksi',
            'approved_by_spv',
        ];

        // Reset approval nullable fields to null
        $nullableApprovalFields = [
            'qc_approved_by', 'qc_approved_at',
            'produksi_approved_by', 'produksi_approved_at',
            'spv_approved_by', 'spv_approved_at',
        ];

        foreach ($booleanApprovalFields as $field) {
            if (array_key_exists($field, $clone->getAttributes())) {
                $clone->{$field} = false;
            }
        }

        foreach ($nullableApprovalFields as $field) {
            if (array_key_exists($field, $clone->getAttributes())) {
                $clone->{$field} = null;
            }
        }

        $clone->save();
        return $clone;
    }

    /**
     * Scope for operational data only
     */
    public function scopeOperasional($query)
    {
        return $query->where('is_audit', false);
    }

    /**
     * Scope for audit data only
     */
    public function scopeAudit($query)
    {
        return $query->where('is_audit', true);
    }
}
```

### Cara Penggunaan di Model

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasAudit;       // ← import

class Seasoning extends Model
{
    use HasFactory, HasAudit;          // ← tambah HasAudit

    protected $fillable = [
        'uuid',
        'is_audit',     // ← wajib ada
        'source_uuid',  // ← wajib ada
        // ... field lainnya
        'approved_by_qc',
        'qc_approved_by',
        'qc_approved_at',
        'approved_by_produksi',
        'produksi_approved_by',
        'produksi_approved_at',
        'approved_by_spv',
        'spv_approved_by',
        'spv_approved_at',
    ];

    protected $casts = [
        'is_audit'           => 'boolean',  // ← wajib ada
        'approved_by_qc'     => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv'    => 'boolean',
        // ...
    ];
}
```

### Method yang Tersedia Setelah `use HasAudit`

#### Relasi
```php
// Ambil salinan audit dari record ini
$item->auditVersion;           // null jika belum di-copy
$item->auditVersion->uuid;     // uuid salinan audit

// Ambil data asli dari salinan audit ini
$item->originalVersion;        // null jika bukan data audit
$item->originalVersion->uuid;  // uuid data asli
```

#### Method Copy
```php
// Buat salinan audit dari record ini
// Returns: instance model baru dengan is_audit=true
$clone = $item->copyToAudit();

// Apa yang dilakukan copyToAudit():
// 1. replicate() semua data
// 2. uuid = UUID baru
// 3. is_audit = true
// 4. source_uuid = uuid asli
// 5. approved_by_qc, approved_by_produksi, approved_by_spv → false
// 6. qc_approved_by, qc_approved_at, dll → null
// 7. save()
```

#### Query Scopes
```php
// Hanya data operasional (is_audit = false)
Seasoning::operasional()->get();
Seasoning::operasional()->where('id_plan', $id)->paginate(10);

// Hanya data audit (is_audit = true)
Seasoning::audit()->get();
```

> **Penting:** Selalu gunakan `->operasional()` di method `index()` dan `searchAjax()` controller agar data audit tidak tercampur dengan data operasional.

---

## 6. Trait Controller: `HasAuditController`

**File:** `app/Http/Controllers/Traits/HasAuditController.php`

> **File ini sudah ada, jangan diubah.** Cukup `use` di controller Anda.

### Kode Lengkap File

```php
<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait HasAuditController
{
    /**
     * Model class yang menggunakan HasAudit trait
     * @return string
     */
    abstract protected function auditModel(): string;

    /**
     * Route prefix untuk naming route (contoh: 'chillroom', 'seasoning')
     * @return string
     */
    abstract protected function auditRoutePrefix(): string;

    /**
     * View prefix untuk naming view (contoh: 'qc-sistem.chillroom', 'qc-sistem.seasoning')
     * @return string
     */
    abstract protected function auditViewPrefix(): string;

    /**
     * Relasi yang di-load untuk index audit
     * @return array
     */
    protected function auditRelations(): array
    {
        return [];
    }

    /**
     * Kolom yang bisa di-search
     * @return array
     */
    protected function auditSearchColumns(): array
    {
        return ['nama_rm', 'kode_produksi', 'keterangan'];
    }

    /**
     * Kolom tanggal untuk grouping (default: 'tanggal')
     * @return string
     */
    protected function auditDateColumn(): string
    {
        return 'tanggal';
    }

    /**
     * Kolom jam untuk ordering (default: 'jam')
     * ⚠️ SELALU override ke 'jam' di controller Anda!
     * @return string
     */
    protected function auditTimeColumn(): string
    {
        return 'jam';
    }

    /**
     * Relasi shift (default: 'shift')
     * @return string
     */
    protected function auditShiftRelation(): string
    {
        return 'shift';
    }

    /**
     * Kolom shift untuk grouping (default: 'shift_id')
     * @return string
     */
    protected function auditShiftColumn(): string
    {
        return 'shift_id';
    }

    /**
     * Copy record ke audit version
     */
    public function copyToAudit($uuid)
    {
        $model = $this->auditModel();
        $item = $model::where('uuid', $uuid)->firstOrFail();

        // Check permission
        if (!Auth::user()->hasPermissionTo('create-' . $this->auditRoutePrefix())) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk membuat data audit.');
        }

        $clone = $item->copyToAudit();

        return redirect()->route("{$this->auditRoutePrefix()}.audit")
            ->with('success', 'Data berhasil dicopy ke Audit. Silakan lakukan penyesuaian.');
    }

    /**
     * Index halaman audit
     */
    public function auditIndex(Request $request)
    {
        $model = $this->auditModel();
        $user = Auth::user();
        $search = $request->get('search');
        $perPage = $request->get('per_page', 5);

        $query = $model::with($this->auditRelations())
            ->where('is_audit', true)
            ->when($user->role !== 'superadmin', function ($q) use ($user) {
                $q->where('id_plan', $user->id_plan);
            });

        if ($search) {
            $query->where(function ($q) use ($search) {
                foreach ($this->auditSearchColumns() as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $search . '%');
                }
            });
        }

        $dateColumn = $this->auditDateColumn();
        $timeColumn = $this->auditTimeColumn();

        $records = $query->orderBy($dateColumn, 'desc')
            ->orderBy($timeColumn, 'desc')
            ->paginate($perPage);

        // Group by tanggal + shift
        $groupedData = $records->getCollection()->groupBy(function ($item) {
            $dateStr = $item->{$this->auditDateColumn()}
                ? (is_string($item->{$this->auditDateColumn()})
                    ? \Carbon\Carbon::parse($item->{$this->auditDateColumn()})->format('Y-m-d')
                    : $item->{$this->auditDateColumn()}->format('Y-m-d'))
                : 'no_date';
            return $dateStr . '_' . ($item->{$this->auditShiftColumn()} ?? $item->{$this->auditShiftRelation()} ?? 'no_shift');
        });

        $records->setCollection($groupedData);

        return view("{$this->auditViewPrefix()}.index_audit", compact('records', 'search', 'perPage'));
    }

    /**
     * AJAX search untuk audit (untuk datatable)
     */
    public function auditSearchAjax(Request $request)
    {
        try {
            $model = $this->auditModel();
            $user = Auth::user();
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 5);
            $page = (int) $request->get('page', 1);

            $query = $model::with($this->auditRelations())
                ->where('is_audit', true)
                ->when($user->role !== 'superadmin', function ($q) use ($user) {
                    $q->where('id_plan', $user->id_plan);
                });

            if ($search) {
                $query->where(function ($q) use ($search) {
                    foreach ($this->auditSearchColumns() as $column) {
                        $q->orWhere($column, 'LIKE', '%' . $search . '%');
                    }
                });
            }

            $dateColumn = $this->auditDateColumn();
            $timeColumn = $this->auditTimeColumn();

            $records = $query->orderBy($dateColumn, 'desc')
                ->orderBy($timeColumn, 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $total = $records->total();
            $groupedData = $records->getCollection()->groupBy(function ($item) {
                $dateStr = $item->{$this->auditDateColumn()}
                    ? (is_string($item->{$this->auditDateColumn()})
                        ? \Carbon\Carbon::parse($item->{$this->auditDateColumn()})->format('Y-m-d')
                        : $item->{$this->auditDateColumn()}->format('Y-m-d'))
                    : 'no_date';
                return $dateStr . '_' . ($item->{$this->auditShiftColumn()} ?? $item->{$this->auditShiftRelation()} ?? 'no_shift');
            });
            $records->setCollection($groupedData);

            return response()->json([
                'html' => view("{$this->auditViewPrefix()}._table", compact('records'))->render(),
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => (int) ceil($total / $perPage),
                    'per_page' => $perPage,
                    'total' => $total,
                    'from' => $records->firstItem(),
                    'to' => $records->lastItem(),
                    'has_pages' => $total > $perPage,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }
}
```

### Cara Penggunaan di Controller

```php
<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasAuditController;  // ← import

class ShoestringController extends Controller
{
    use HasAuditController;  // ← tambah trait

    // === 3 method WAJIB diimplementasikan ===

    protected function auditModel(): string
    {
        return Shoestring::class;  // ← nama Model
    }

    protected function auditRoutePrefix(): string
    {
        return 'shoestring';  // ← prefix route (tanpa titik)
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.shoestring';  // ← path folder view
    }

    // === method opsional (sudah ada default, override jika perlu) ===

    protected function auditRelations(): array
    {
        // Relasi yang di-eager load di halaman audit
        // WAJIB sertakan 'originalVersion' agar tombol "Lihat Data Asli" bisa muncul
        return ['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'createdBy', 'originalVersion'];
    }

    protected function auditSearchColumns(): array
    {
        // Kolom yang dicari di search bar halaman audit
        return ['nama_produsen', 'kode_produksi', 'catatan'];
        // Default: ['nama_rm', 'kode_produksi', 'keterangan']
    }

    protected function auditDateColumn(): string
    {
        return 'tanggal';  // Default: 'tanggal'
    }

    protected function auditTimeColumn(): string
    {
        return 'jam';  // Default: 'jam' — SELALU override ke 'jam'!
    }

    protected function auditShiftRelation(): string
    {
        return 'shift';  // Default: 'shift'
    }

    protected function auditShiftColumn(): string
    {
        return 'shift_id';  // Default: 'shift_id'
    }
}
```

### Method yang Otomatis Tersedia Setelah `use HasAuditController`

| Method | Route | Keterangan |
|--------|-------|------------|
| `copyToAudit($uuid)` | `GET {modul}/copy-to-audit/{uuid}` | Cek permission → panggil `$model->copyToAudit()` → redirect ke `.audit` |
| `auditIndex(Request $request)` | `GET {modul}/audit-data` | Tampilkan `index_audit.blade.php` dengan data `is_audit=true` |
| `auditSearchAjax(Request $request)` | `GET {modul}/audit-search` | AJAX search untuk `index_audit`, return JSON `{html, pagination}` |

### Permission yang Dicek di `copyToAudit()`

```php
// Dari HasAuditController::copyToAudit()
Auth::user()->hasPermissionTo('create-' . $this->auditRoutePrefix())
// Contoh: 'create-shoestring', 'create-seasoning'
```


---

## 7. Langkah Implementasi ke Modul Baru

Panduan lengkap berdasarkan kode nyata `ShoestringController` sebagai template.
Ganti `{modul}` → nama modul lowercase (contoh: `rebox`), `{Modul}` → nama class (contoh: `Rebox`), `{modul_table}` → nama tabel (contoh: `reboxes`).

---

### Langkah 1 — Migration

```bash
php artisan make:migration add_is_audit_to_{modul_table}_table --table={modul_table}
```

Isi file migration yang dibuat di `database/migrations/`:

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('{modul_table}', function (Blueprint $table) {
            $table->boolean('is_audit')->default(false)->after('uuid');
            $table->string('source_uuid')->nullable()->after('is_audit');
        });
    }

    public function down(): void
    {
        Schema::table('{modul_table}', function (Blueprint $table) {
            $table->dropColumn(['is_audit', 'source_uuid']);
        });
    }
};
```

```bash
php artisan migrate
```

---

### Langkah 2 — Update Model

File: `app/Models/{Modul}.php`

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasAudit;        // ← 1. tambah import

class {Modul} extends Model
{
    use HasFactory, HasAudit;           // ← 2. tambah HasAudit

    protected $fillable = [
        'uuid',
        'is_audit',                     // ← 3. tambah
        'source_uuid',                  // ← 4. tambah
        // ... field lainnya yang sudah ada ...
        'approved_by_qc',
        'qc_approved_by',
        'qc_approved_at',
        'approved_by_produksi',
        'produksi_approved_by',
        'produksi_approved_at',
        'approved_by_spv',
        'spv_approved_by',
        'spv_approved_at',
    ];

    protected $casts = [
        'is_audit'             => 'boolean',  // ← 5. tambah
        'approved_by_qc'       => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv'      => 'boolean',
        // ... cast lainnya ...
    ];

    // ... relasi dan method lainnya tidak perlu diubah ...
}
```

---

### Langkah 3 — Update Controller

File: `app/Http/Controllers/{Modul}Controller.php`

**A. Tambah import dan trait (di atas class):**

```php
use App\Http\Controllers\Traits\HasAuditController;  // ← tambah import

class {Modul}Controller extends Controller
{
    use HasAuditController;  // ← tambah trait

    // Implementasi 3 method wajib:
    protected function auditModel(): string { return {Modul}::class; }
    protected function auditRoutePrefix(): string { return '{modul}'; }
    protected function auditViewPrefix(): string { return 'qc-sistem.{modul}'; }

    // Override optional methods:
    protected function auditRelations(): array
    {
        return ['plan', 'shift', 'qcApprover', 'produksiApprover', 'spvApprover', 'createdBy', 'originalVersion'];
    }
    protected function auditSearchColumns(): array
    {
        return ['kode_produksi', 'nama_field_utama', 'keterangan']; // sesuaikan
    }
    protected function auditTimeColumn(): string { return 'jam'; } // WAJIB override
```

**B. Update method `index()` — tambah 3 baris:**

```php
public function index(Request $request)
{
    $user = auth()->user();

    // ← TAMBAH: redirect auditor ke halaman audit
    if ($user->id_role == 6) {
        return redirect()->route('{modul}.audit');
    }

    $search = $request->get('search');
    $perPage = $request->get('per_page', 5);

    $query = {Modul}::with(['plan', 'shift', ..., 'auditVersion'])  // ← tambah 'auditVersion'
        ->operasional()                                               // ← TAMBAH scope ini
        ->when($user->role !== 'superadmin', function($q) use ($user) {
            $q->where('id_plan', $user->id_plan);
        });

    // ... sisa method tidak berubah ...
}
```

**C. Update method `searchAjax()` — tambah 2 baris:**

```php
public function searchAjax(Request $request)
{
    $query = {Modul}::with(['plan', 'shift', ..., 'auditVersion'])  // ← tambah 'auditVersion'
        ->operasional()                                               // ← TAMBAH scope ini
        ->when($user->role !== 'superadmin', function($q) use ($user) {
            $q->where('id_plan', $user->id_plan);
        });

    // ... sisa method tidak berubah ...
}
```

---

### Langkah 4 — Tambah Routes

File: `routes/web.php`

Cari blok route modul Anda. Tambahkan **SEBELUM** `Route::resource()`:

```php
// Audit routes for {Modul}
Route::get('{modul}/copy-to-audit/{uuid}', [{Modul}Controller::class, 'copyToAudit'])->name('{modul}.copy-to-audit');
Route::get('{modul}/audit-data', [{Modul}Controller::class, 'auditIndex'])->name('{modul}.audit');
Route::get('{modul}/audit-search', [{Modul}Controller::class, 'auditSearchAjax'])->name('{modul}.audit-search');

Route::resource('{modul}', {Modul}Controller::class);  // sudah ada, jangan duplikasi
```

> **⚠️ Urutan penting!** Route spesifik HARUS sebelum `Route::resource()`.
> Jika terbalik, route `{modul}/{id}` dari resource akan "menelan" `{modul}/copy-to-audit/xxx`.

---

### Langkah 5 — Update `_table.blade.php`

File: `resources/views/qc-sistem/{modul}/_table.blade.php`

Cari kolom aksi dan tambahkan `<x-audit-dropdown>`:

```blade
{{-- SEBELUM --}}
<td class="text-center align-middle">
    <x-action-buttons :item="$item" route-prefix="{modul}" :show-history="true" :show-view="true" />
</td>

{{-- SESUDAH --}}
<td class="text-center align-middle">
    <x-action-buttons :item="$item" route-prefix="{modul}" :show-history="true" :show-view="true" />
    {{-- Global Audit Dropdown --}}
    <x-audit-dropdown :item="$item" route-prefix="{modul}" />
</td>
```

---

### Langkah 6 — Update `index.blade.php`

File: `resources/views/qc-sistem/{modul}/index.blade.php`

Cari bagian `card-tools` dan tambahkan tombol "Data Audit" **sebelum** tombol "Tambah Data":

```blade
{{-- Cari bagian ini --}}
@if(auth()->user()->hasPermissionTo('create-{modul}'))
    {{-- TAMBAH tombol audit DI SINI --}}
    <a href="{{ route('{modul}.audit') }}"
       class="btn btn-sm mr-2 shadow-sm"
       style="background-color:#6f42c1;color:#fff;">
        <i class="fas fa-history mr-1"></i> Data Audit
    </a>
    <a href="{{ route('{modul}.create') }}" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus-circle mr-1"></i> Tambah Data
    </a>
@endif
```

---

### Langkah 7 — Buat `index_audit.blade.php`

File baru: `resources/views/qc-sistem/{modul}/index_audit.blade.php`

Copy dari `resources/views/qc-sistem/seasoning/index_audit.blade.php` dan ganti:

| Teks Lama (seasoning) | Ganti Dengan |
|-----------------------|--------------|
| `Pusat Data Audit: Seasoning` | `Pusat Data Audit: {Nama Modul}` |
| `route('seasoning.index')` | `route('{modul}.index')` |
| `route('seasoning.audit-search')` | `route('{modul}.audit-search')` |
| `@include('qc-sistem.seasoning._table')` | `@include('qc-sistem.{modul}._table')` |
| `text-purple` pada `<i>` judul | tetap sama (warna ungu untuk semua halaman audit) |
| Kolom tabel di `<thead>` | Sesuaikan dengan field modul |

**Template header halaman audit (sama untuk semua modul):**

```blade
@extends('layouts.app')

@section('container')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-history text-purple"></i> Pusat Data Audit: {Nama Modul}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('{modul}.index') }}">{Nama Modul}</a></li>
                        <li class="breadcrumb-item active">Audit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="col-12">
                <div class="card card-purple card-outline shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h3 class="card-title text-bold text-purple">
                            <i class="fas fa-shield-alt mr-2"></i> Data Penyesuaian Audit
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-purple p-2 mr-2">MODE AUDIT AKTIF</span>
                            <a href="{{ route('{modul}.index') }}" class="btn btn-default btn-sm shadow-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Operasional
                            </a>
                        </div>
                    </div>
                    {{-- ... card-body dengan search, table, pagination ... --}}
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.card-purple.card-outline { border-top: 3px solid #6f42c1; }
.text-purple { color: #6f42c1 !important; }
.badge-purple { background-color: #6f42c1; color: white; }
.group-header { background-color: #f8f9fa !important; border-left: 5px solid #6f42c1 !important; }
</style>
```


---

## 8. Referensi Modul Selesai

| Modul | Model | Controller | Tabel | Status |
|-------|-------|------------|-------|--------|
| Chillroom | `PenerimaanChillroom` | `ChillroomController` | `penerimaan_chillroom` | ✅ |
| Seasoning | `Seasoning` | `SeasoningController` | `seasonings` | ✅ |
| Shoestring | `Shoestring` | `ShoestringController` | `shoestrings` | ✅ |

> Gunakan `ShoestringController` sebagai template paling bersih dan lengkap.

---

## 9. FAQ & Troubleshooting

### ❌ Error: `Column 'approved_by_qc' cannot be null`
**Penyebab:** `approved_by_qc` di DB bertipe `boolean NOT NULL`, tapi `copyToAudit()` mencoba set ke `null`.
**Solusi:** Sudah ditangani otomatis di `HasAudit::copyToAudit()` — pastikan model sudah `use HasAudit`. Field boolean di-reset ke `false`, bukan `null`.

### ❌ Error: `Class 'App\Http\Controllers\PembuatanBetter' not found`
**Penyebab:** Model yang digunakan di controller tidak diimport atau nama salah.
**Solusi:** Periksa `use App\Models\...` di bagian atas controller. Pastikan nama model benar.

### ❌ Dropdown audit tidak muncul sama sekali
**Penyebab:** Route `{modul}.copy-to-audit` atau `{modul}.show` tidak ditemukan.
**Solusi:** Cek `web.php`, pastikan route sudah ditambahkan. Komponen tidak render jika route tidak ada.

### ❌ Halaman operasional masih menampilkan data audit
**Penyebab:** Lupa menambahkan `.operasional()` di query `index()` atau `searchAjax()`.
**Solusi:**
```php
$query = {Modul}::with([...])->operasional()->when(...);
```

### ❌ Tombol "Lihat Hasil Audit" tidak muncul padahal sudah copy
**Penyebab:** `auditVersion` tidak di-eager load.
**Solusi:** Pastikan `'auditVersion'` ada di array `with()`:
```php
{Modul}::with([..., 'auditVersion'])->operasional()->...
```

### ❌ Tombol "Lihat Data Asli" di halaman audit tidak muncul
**Penyebab:** `originalVersion` tidak di `auditRelations()`.
**Solusi:**
```php
protected function auditRelations(): array
{
    return [..., 'originalVersion'];  // ← wajib ada
}
```

### ❌ Auditor bisa mengakses halaman operasional
**Penyebab:** Belum ada redirect di `index()`.
**Solusi:**
```php
public function index(Request $request)
{
    if (auth()->user()->id_role == 6) {
        return redirect()->route('{modul}.audit');
    }
    // ...
}
```

### ❌ Route conflict / `{modul}/copy-to-audit` tidak bisa diakses
**Penyebab:** `Route::resource()` dideklarasikan sebelum route spesifik.
**Solusi:** Pastikan urutan di `web.php`:
```php
// ✅ BENAR
Route::get('{modul}/copy-to-audit/{uuid}', [...])->name('{modul}.copy-to-audit');
Route::get('{modul}/audit-data', [...])->name('{modul}.audit');
Route::get('{modul}/audit-search', [...])->name('{modul}.audit-search');
Route::resource('{modul}', {Modul}Controller::class);  // ← setelah
```

### ❌ `auditTimeColumn` default tidak sesuai
**Penyebab:** Default di `HasAuditController` adalah `'jam'`, bukan `'jam'`.
**Solusi:** Selalu override di controller:
```php
protected function auditTimeColumn(): string { return 'jam'; }
```

### ❌ Data lama (`is_audit = NULL`) ikut tersaring
**Penyebab:** Data dibuat sebelum kolom `is_audit` ditambahkan, nilainya `NULL`.
**Solusi:** Jalankan SQL update:
```sql
UPDATE {modul_table} SET is_audit = false WHERE is_audit IS NULL;
```

---

## 10. Checklist Implementasi

Copy dan gunakan checklist ini untuk setiap modul baru:

```
MODUL: _______________
TANGGAL: _______________

[ ] 1. Migration: buat file dengan --table={modul_table}
[ ] 2. Migration: isi up() dengan is_audit (boolean, default false) dan source_uuid (nullable)
[ ] 3. Migration: jalankan php artisan migrate
[ ] 4. Model: tambah import HasAudit
[ ] 5. Model: tambah 'use HasAudit' di class
[ ] 6. Model: tambah 'is_audit' ke $fillable
[ ] 7. Model: tambah 'source_uuid' ke $fillable
[ ] 8. Model: tambah 'is_audit' => 'boolean' ke $casts
[ ] 9. Controller: tambah import HasAuditController
[ ] 10. Controller: tambah 'use HasAuditController' di class
[ ] 11. Controller: implementasi auditModel()
[ ] 12. Controller: implementasi auditRoutePrefix()
[ ] 13. Controller: implementasi auditViewPrefix()
[ ] 14. Controller: override auditRelations() — sertakan 'originalVersion'
[ ] 15. Controller: override auditSearchColumns() — sesuaikan kolom search
[ ] 16. Controller: override auditTimeColumn() — return 'jam'
[ ] 17. Controller: tambah redirect auditor (id_role == 6) di index()
[ ] 18. Controller: tambah ->operasional() di query index()
[ ] 19. Controller: tambah 'auditVersion' ke with() di index()
[ ] 20. Controller: tambah ->operasional() di query searchAjax()
[ ] 21. Controller: tambah 'auditVersion' ke with() di searchAjax()
[ ] 22. Routes: tambah route copy-to-audit SEBELUM Route::resource()
[ ] 23. Routes: tambah route audit-data SEBELUM Route::resource()
[ ] 24. Routes: tambah route audit-search SEBELUM Route::resource()
[ ] 25. View _table.blade.php: tambah <x-audit-dropdown>
[ ] 26. View index.blade.php: tambah tombol "Data Audit" (warna #6f42c1)
[ ] 27. View index_audit.blade.php: buat file baru (copy dari seasoning)
[ ] 28. Test: login sebagai role biasa, buka halaman index — data audit tidak muncul
[ ] 29. Test: klik "Copy ke Audit" — redirect ke index_audit, data muncul
[ ] 30. Test: di index_audit, dropdown gear muncul dengan "Lihat Data Asli"
[ ] 31. Test: di index, dropdown gear menampilkan "Lihat Hasil Audit" setelah di-copy
[ ] 32. Test: login sebagai AUDITOR (id_role=6), buka URL modul — redirect ke audit
```
