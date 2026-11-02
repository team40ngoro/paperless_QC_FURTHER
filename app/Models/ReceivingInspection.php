<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ReceivingInspection extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'receiving_inspections';

    protected $fillable = [
        'uuid',
        'area',
        'inspection_date',
        'qc_form_code',
        'prod_form_code',
        'prod_status',
        'qc_status',
        'prod_filled_by',
        'prod_submitted_at',
        'qc_filled_by',
        'qc_submitted_at',
        'production_data',
        'qc_data',
        'has_qc_finding',
        'created_by',
        'legacy_source',
        'legacy_source_id',
        'legacy_unlinked',
    ];

    protected $casts = [
        'inspection_date' => 'date:Y-m-d',
        'production_data' => 'array',
        'qc_data' => 'array',
        'prod_submitted_at' => 'datetime',
        'qc_submitted_at' => 'datetime',
        'has_qc_finding' => 'boolean',
        'legacy_unlinked' => 'boolean',
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

    public function defects()
    {
        return $this->hasMany(ShoestringQcDefect::class, 'receiving_inspection_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(InspectionAuditLog::class, 'receiving_inspection_id')->latest();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function prodFilledBy()
    {
        return $this->belongsTo(User::class, 'prod_filled_by');
    }

    public function qcFilledBy()
    {
        return $this->belongsTo(User::class, 'qc_filled_by');
    }

    public function getIsCompleteAttribute(): bool
    {
        return $this->prod_status === 'SUBMITTED' && $this->qc_status === 'SUBMITTED';
    }

    public function getStatusAttribute(): string
    {
        return $this->is_complete ? 'COMPLETE' : 'IN_PROGRESS';
    }
}
