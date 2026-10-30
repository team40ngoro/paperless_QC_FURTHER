<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasAudit;

class PersiapanBahanNonForming extends Model
{
    use HasAudit;

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $table = 'persiapan_bahan_non_forming';

    protected $fillable = [
        'uuid',
        'plan_id',
        'user_id',
        'id_no_formula_non_forming',
        'berat_sampling',
        'shift_id',
        'tanggal',
        'jam',
        'id_suhu_adonan',
        'kode_produksi',
        'kode_produksi_emulsi_oil',
        'waktu_mulai_mixing',
        'waktu_selesai_mixing',
        'kondisi',
        'rework',
        'catatan',
        'kode_form',
        // Approval fields
        'approved_by_qc',
        'qc_approved_by',
        'qc_approved_at',
        'approved_by_produksi',
        'produksi_approved_by',
        'produksi_approved_at',
        'approved_by_spv',
        'spv_approved_by',
        'spv_approved_at',
        // Audit fields
        'is_audit',
        'source_uuid',
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'kode_produksi_emulsi_oil' => 'array',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    public function formulaNonForming()
    {
        return $this->belongsTo(MasterProdukNonForming::class, 'id_no_formula_non_forming');
    }

    public function suhuAdonan()
    {
        return $this->belongsTo(SuhuAdonan::class, 'id_suhu_adonan');
    }

    public function details()
    {
        return $this->hasMany(PersiapanBahanNonFormingDetail::class, 'id_persiapan_bahan_non_forming');
    }

    public function aktuals()
    {
        return $this->morphMany(AktualSuhuAdonan::class, 'owner');
    }

    public function aktualSuhuAdonan()
    {
        return $this->morphOne(AktualSuhuAdonan::class, 'owner');
    }

    // Relasi ke User yang melakukan approval
    public function qcApprover()
    {
        return $this->belongsTo(\App\Models\User::class, 'qc_approved_by');
    }

    public function produksiApprover()
    {
        return $this->belongsTo(\App\Models\User::class, 'produksi_approved_by');
    }

    public function spvApprover()
    {
        return $this->belongsTo(\App\Models\User::class, 'spv_approved_by');
    }

    public function produk()
    {
        return $this->formulaNonForming->produk();
    }

    /**
     * Override copyToAudit to also copy child records
     */
    public function copyToAudit(): self
    {
        $clone = $this->replicate();
        $clone->uuid = (string) Str::uuid();
        $clone->is_audit = true;
        $clone->source_uuid = $this->uuid;

        if (!$this->keepApprovalsOnAudit()) {
            $booleanApprovalFields = ['approved_by_qc', 'approved_by_produksi', 'approved_by_spv'];
            $nullableApprovalFields = ['qc_approved_by', 'qc_approved_at', 'produksi_approved_by', 'produksi_approved_at', 'spv_approved_by', 'spv_approved_at'];
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

        $clone->save();

        // Copy details records
        foreach ($this->details as $detail) {
            $detailClone = $detail->replicate();
            $detailClone->uuid = (string) Str::uuid();
            $detailClone->id_persiapan_bahan_non_forming = $clone->id;
            $detailClone->save();
        }

        // Copy aktualSuhuAdonan records
        foreach ($this->aktuals as $aktual) {
            $aktualClone = $aktual->replicate();
            $aktualClone->uuid = (string) Str::uuid();
            $aktualClone->owner_id = $clone->id;
            $aktualClone->owner_type = get_class($clone);
            $aktualClone->save();
        }

        return $clone;
    }
}
