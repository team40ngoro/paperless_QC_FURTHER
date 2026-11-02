<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InspectionAuditLog extends Model
{
    use HasFactory;

    protected $table = 'inspection_audit_logs';

    protected $fillable = [
        'uuid',
        'receiving_inspection_id',
        'area',
        'section',
        'action',
        'field_key',
        'old_value',
        'new_value',
        'actor_user_id',
        'actor_role',
        'reason',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
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

    public function inspection()
    {
        return $this->belongsTo(ReceivingInspection::class, 'receiving_inspection_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
