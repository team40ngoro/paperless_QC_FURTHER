<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use App\Models\Traits\HasAudit;

class VerifCip extends Model
{
    use HasFactory, HasAudit;

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $table = 'verif_cip';

    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'tanggal',
        'payload',
        'approved_by_qc',
        'approved_by_produksi',
        'approved_by_spv',
        'approved_by_qc_at',
        'approved_by_produksi_at',
        'approved_by_spv_at',
        'approved_by_qc_user_id',
        'approved_by_produksi_user_id',
        'approved_by_spv_user_id',
        'is_audit',
        'source_uuid',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'payload' => 'array',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'approved_by_qc_at' => 'datetime',
        'approved_by_produksi_at' => 'datetime',
        'approved_by_spv_at' => 'datetime',
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
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
