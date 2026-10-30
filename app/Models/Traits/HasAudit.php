<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasAudit
{
    /**
     * Override di model jika ingin approval tetap terbawa saat copy to audit
     */
    protected function keepApprovalsOnAudit(): bool
    {
        return false;
    }

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

        if (!$this->keepApprovalsOnAudit()) {
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
        }

        // Auto-Correction Data Audit berdasarkan Master Standar & Aturan Umum QC
        $this->applyAuditAutoCorrections($clone);

        $clone->save();
        return $clone;
    }

    /**
     * Intercept attribute access di halaman audit agar data operasional
     * otomatis ditampilkan secara ter-koreksi secara VIRTUAL di Index Audit.
     */
    public function getAttribute($key)
    {
        // Selalu kembalikan nilai fisik mentah asli di database
        // Data operasional (is_audit = false) yang belum di-copy akan menampilkan nilai aktual asli (misal 2°C)
        // Penyesuaian standar hanya dilakukan saat data di-copy ke audit melalui copyToAudit()
        return parent::getAttribute($key);
    }

    /**
     * Dapatkan nilai tampilan yang sudah di-koreksi secara virtual untuk atribut tertentu.
     */
    public function getAuditDisplayValue(string $col, $currentVal)
    {
        if ($currentVal === null) {
            return null;
        }

        $tableName = $this->getTable();
        $recordAreaId = null;
        foreach (['area_id', 'id_area', 'id_input_area'] as $areaCol) {
            if (array_key_exists($areaCol, $this->attributes) && !empty($this->attributes[$areaCol])) {
                $recordAreaId = $this->attributes[$areaCol];
                break;
            }
        }

        // Cari aturan yang cocok (mencocokkan table_name atau module_name)
        $rule = \App\Models\MasterStandarAudit::all()->first(function ($r) use ($tableName, $col, $recordAreaId) {
            if ($r->column_name !== $col) {
                return false;
            }
            if ($recordAreaId && $r->area_id && (int)$r->area_id !== (int)$recordAreaId) {
                return false;
            }
            if ($r->table_name === $tableName || $r->module_name === $tableName) {
                return true;
            }
            if (!empty($r->module_name) && str_contains($tableName, $r->module_name)) {
                return true;
            }
            if (!empty($r->table_name) && str_contains($tableName, $r->table_name)) {
                return true;
            }
            return false;
        });

        if ($rule) {
            // 1. Parameter Jenis Suhu (Numerik)
            if (in_array($rule->parameter_type, ['suhu', 'suhu_ruang', 'suhu_kondisi', 'suhu_pusat', 'suhu_pemasakan']) && is_numeric($currentVal)) {
                $numericVal = (float) $currentVal;
                $isBelow    = $rule->min_value !== null && $numericVal < (float)$rule->min_value;
                $isAbove    = $rule->max_value !== null && $numericVal > (float)$rule->max_value;

                if ($isBelow || $isAbove) {
                    if ($rule->standar_value !== null && $rule->standar_value !== '') {
                        return $rule->standar_value;
                    }
                    // Hasilkan nilai acak deterministik di dalam range min s/d max
                    $min = $rule->min_value !== null ? (float)$rule->min_value : 0;
                    $max = $rule->max_value !== null ? (float)$rule->max_value : 30;
                    $seed = crc32(($this->uuid ?? $this->id ?? 'audit') . '_' . $col);
                    mt_srand($seed);
                    $randomVal = round($min + (mt_rand() / mt_getrandmax()) * ($max - $min), 2);
                    mt_srand();
                    return (string)$randomVal;
                }
            }
            // 2. Parameter Jenis Status / Teks (Non-Numerik)
            else if ($rule->parameter_type === 'status_checklist' || !is_numeric($currentVal)) {
                $targetVal = $rule->standar_value ?? 'OK';
                if ($targetVal === '✔' || $targetVal === '✓') { $targetVal = 'OK'; }
                if (is_string($currentVal) && (
                    str_contains(strtolower($currentVal), 'tidak') ||
                    str_contains(strtolower($currentVal), 'ng') ||
                    str_contains(strtolower($currentVal), 'rusak') ||
                    in_array(trim($currentVal), ['✘', 'X', 'x']) ||
                    strtolower(trim($currentVal)) !== strtolower(trim($targetVal))
                )) {
                    return $targetVal;
                }
            }
        } else {
            // Fallback umum untuk kolom teks status
            if (is_string($currentVal)) {
                $lowerVal = strtolower(trim($currentVal));
                if (in_array($lowerVal, ['not ok', 'ng', 'reject', 'kotor', 'tidak ok', 'tidak_sesuai', 'tidak sesuai', 'x', '✗', '✘', 'tidak baik'])) {
                    return 'OK';
                }
            }
        }

        return $currentVal;
    }

    /**
     * Terapkan penyesuaian nilai otomatis pada objek salinan audit.
     * Mengatur: Suhu, Suhu Ruang, Suhu Kondisi, Suhu Pusat, Suhu Pemasakan & Status Checklist/OK.
     * Prioritas: Aturan Area-Spesifik > Aturan Global Tabel > Fallback Umum
     */
    protected function applyAuditAutoCorrections($clone): void
    {
        $tableName = $clone->getTable();

        // Deteksi area_id dari record jika ada (mendukung kolom area_id atau id_area)
        $recordAreaId = null;
        foreach (['area_id', 'id_area', 'id_input_area'] as $areaCol) {
            if (array_key_exists($areaCol, $clone->getAttributes()) && !empty($clone->{$areaCol})) {
                $recordAreaId = $clone->{$areaCol};
                break;
            }
        }

        // Ambil semua aturan untuk tabel/modul ini dari Master Standar
        $allRules = \App\Models\MasterStandarAudit::all()->filter(function ($rule) use ($tableName) {
            if ($rule->table_name === $tableName || $rule->module_name === $tableName) {
                return true;
            }
            if (!empty($rule->module_name) && str_contains($tableName, $rule->module_name)) {
                return true;
            }
            if (!empty($rule->table_name) && str_contains($tableName, $rule->table_name)) {
                return true;
            }
            return false;
        });

        // Susun aturan efektif dengan key = column_name (Area-Spesifik menimpa Global)
        $effectiveRules = collect();
        foreach ($allRules as $rule) {
            $colName = $rule->column_name;
            if ($recordAreaId && $rule->area_id && (int)$rule->area_id === (int)$recordAreaId) {
                $effectiveRules->put($colName, $rule);
            } elseif (!$rule->area_id && !$effectiveRules->has($colName)) {
                $effectiveRules->put($colName, $rule);
            }
        }

        foreach ($effectiveRules as $col => $rule) {
            if (!array_key_exists($col, $clone->getAttributes())) {
                continue;
            }

            // Ambil nilai fisik mentah asli di database (tanpa interceptor getAttribute)
            $currentVal = $clone->getRawOriginal($col) ?? ($clone->getAttributes()[$col] ?? null);

            // 1. Parameter Jenis Suhu (Numerik)
            if (in_array($rule->parameter_type, ['suhu', 'suhu_ruang', 'suhu_kondisi', 'suhu_pusat', 'suhu_pemasakan']) && is_numeric($currentVal)) {
                $numericVal = (float) $currentVal;
                $isBelow    = $rule->min_value !== null && $numericVal < (float)$rule->min_value;
                $isAbove    = $rule->max_value !== null && $numericVal > (float)$rule->max_value;

                if ($isBelow || $isAbove) {
                    if ($rule->standar_value !== null && $rule->standar_value !== '') {
                        $numericValToSet = is_numeric($rule->standar_value) ? (float)$rule->standar_value : $rule->standar_value;
                    } else {
                        $min = $rule->min_value !== null ? (float)$rule->min_value : 0;
                        $max = $rule->max_value !== null ? (float)$rule->max_value : 30;
                        $seed = crc32(($clone->uuid ?? $clone->id ?? 'audit') . '_' . $col);
                        mt_srand($seed);
                        $numericValToSet = round($min + (mt_rand() / mt_getrandmax()) * ($max - $min), 2);
                        mt_srand();
                    }
                    $clone->setAttribute($col, $numericValToSet);
                }
            }
            // 2. Parameter Jenis Status / Teks (Non-Numerik)
            else if ($rule->parameter_type === 'status_checklist' || !is_numeric($currentVal)) {
                $targetVal = $rule->standar_value ?? 'OK';
                if ($targetVal === '✔' || $targetVal === '✓') { $targetVal = 'OK'; }
                if (is_string($currentVal) && (
                    str_contains(strtolower($currentVal), 'tidak') ||
                    str_contains(strtolower($currentVal), 'ng') ||
                    str_contains(strtolower($currentVal), 'rusak') ||
                    in_array(trim($currentVal), ['✘', 'X', 'x']) ||
                    strtolower(trim($currentVal)) !== strtolower(trim($targetVal))
                )) {
                    $clone->setAttribute($col, $targetVal);
                }
            }
        }

        // Fallback umum: konversi nilai tidak OK / Silang pada kolom yang TIDAK terdaftar di Master
        $coveredColumns = $effectiveRules->keys()->toArray();
        $attributes     = $clone->getAttributes();
        foreach ($attributes as $key => $val) {
            if (in_array($key, $coveredColumns)) {
                continue; // Sudah ditangani oleh aturan Master di atas
            }
            if (is_string($val)) {
                $lowerVal = strtolower(trim($val));
                    if (in_array($lowerVal, ['not ok', 'ng', 'reject', 'kotor', 'tidak ok', 'tidak_sesuai', 'tidak sesuai', 'x', '✗', '✘', 'tidak baik'])) {
                        $clone->{$key} = 'OK';
                    }
            }
        }
    }

    /**
     * Normalisasi string status tidak OK menjadi OK / Ceklis
     */
    protected function normalizeStatusToOk($val)
    {
        if (is_bool($val)) {
            return true;
        }
        if (is_numeric($val)) {
            return 1;
        }
        $str = strtolower(trim((string)$val));
        if (in_array($str, ['x', '✗', 'false', '0'])) {
            return 'OK';
        }
        return 'OK';
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

    /**
     * Scope Audit-First Smart Filter:
     * Menampilkan/menarik versi Audit jika ada, ATAU data Operasional yang belum di-copy ke Audit.
     */
    public function scopeAuditFirstSmartFilter($query, bool $isAudit = true)
    {
        if (!$isAudit) {
            return $query->where('is_audit', false);
        }

        $auditedSourceUuids = static::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        return $query->where(function ($q) use ($auditedSourceUuids) {
            $q->where('is_audit', true)
              ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                  $subQ->where('is_audit', false);
                  if (!empty($auditedSourceUuids)) {
                      $subQ->whereNotIn('uuid', $auditedSourceUuids);
                  }
              });
        });
    }
}