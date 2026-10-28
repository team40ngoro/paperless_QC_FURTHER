<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use App\Models\Traits\HasAudit;

class VerifPeralatan extends Model
{
    use HasFactory, HasAudit {
        HasAudit::copyToAudit as parentCopyToAudit;
    }

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $table = 'verif_peralatan';

    protected $fillable = [
        'is_audit',
        'source_uuid',
        'uuid',
        'id_plan',
        'user_id',
        'id_shift',
        'tanggal',
        'jam',
        'kode_form',
        'approved_by_qc',
        'approved_by_produksi',
        'approved_by_spv',
        'approved_by_qc_at',
        'approved_by_produksi_at',
        'approved_by_spv_at',
        'approved_by_qc_user_id',
        'approved_by_produksi_user_id',
        'approved_by_spv_user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam' => 'string',
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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        date_default_timezone_set('Asia/Jakarta');
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

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'id_shift');
    }

    public function details()
    {
        return $this->hasMany(VerifPeralatanDetail::class, 'verif_peralatan_id');
    }

    public function copyToAudit(): self
    {
        // Panggil copyToAudit dari trait
        $clone = $this->parentCopyToAudit();
        
        // Copy relasi details
        foreach ($this->details as $detail) {
            $newDetail = $detail->replicate();
            $newDetail->verif_peralatan_id = $clone->id;
            $newDetail->save();
        }
        
        return $clone;
    }
}
