<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasAudit;

class PemeriksaanRiceBites extends Model
{
    use HasFactory, HasAudit;

    protected $table = 'pemeriksaan_rice_bites';

    protected $fillable = [
        'uuid',
        'is_audit',
        'source_uuid',
        'id_plan',
        'user_id',
        'shift_id',
        'id_produk',
        'nomor_formula',
        'tanggal',
         'jam',
        'batch',
        'no_cooking_cycle',
        'bahan_baku',
        'premix',
        'parameter_nitrogen',
        'jumlah_inject_nitrogen',
        'rpm_cooking_cattle',
        'cold_mixing',
        'suhu_aktual_adonan',
        'suhu_adonan_pencampuran',
        'rata_rata_suhu',
        'hasil_pencampuran',
        'catatan',
        // Approval fields
        'approved_by_qc',
        'approved_by_produksi',
        'approved_by_spv',
        'qc_approved_by',
        'produksi_approved_by',
        'spv_approved_by',
        'qc_approved_at',
        'produksi_approved_at',
        'spv_approved_at',
        'kode_form',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'bahan_baku' => 'array',
        'premix' => 'array',
        'suhu_aktual_adonan' => 'array',
        'suhu_adonan_pencampuran' => 'array',
        'rata_rata_suhu' => 'decimal:2',
        // Approval casts
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Relationship to Plan
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship to DataShift
     */
    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    /**
     * Relationship to JenisProduk
     */
    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    /**
     * Relationship to QC Approver
     */
    public function qcApprover()
    {
        return $this->belongsTo(User::class, 'qc_approved_by');
    }

    /**
     * Relationship to Produksi Approver
     */
    public function produksiApprover()
    {
        return $this->belongsTo(User::class, 'produksi_approved_by');
    }

    /**
     * Relationship to SPV Approver
     */
    public function spvApprover()
    {
        return $this->belongsTo(User::class, 'spv_approved_by');
    }

    /**
     * Get hasil pencampuran icon
     */
    public function getHasilPencampuranIconAttribute()
    {
        if ($this->hasil_pencampuran === 'OK') {
            return '<i class="fas fa-check-circle text-success"></i>';
        } elseif ($this->hasil_pencampuran === 'Tidak OK') {
            return '<i class="fas fa-times-circle text-danger"></i>';
        }
        return '<i class="fas fa-minus-circle text-muted"></i>';
    }


    /**
     * Get bahan baku array
     */
    public function getBahanBakuArrayAttribute()
    {
        return $this->bahan_baku ?? [];
    }

    /**
     * Get premix array
     */
    public function getPremixArrayAttribute()
    {
        return $this->premix ?? [];
    }

    /**
     * Get suhu aktual adonan array
     */
    public function getSuhuAktualAdonanArrayAttribute()
    {
        return $this->suhu_aktual_adonan ?? [];
    }

    /**
     * Get suhu adonan pencampuran array
     */
    public function getSuhuAdonanPencampuranArrayAttribute()
    {
        return $this->suhu_adonan_pencampuran ?? [];
    }

    public function keepApprovalsOnAudit(): bool
    {
        return true;
    }
}
