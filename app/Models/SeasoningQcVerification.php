<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SeasoningQcVerification extends Model
{
    use HasFactory;

    protected $table = 'seasoning_qc_verifications';

    protected $fillable = [
        'uuid',
        'seasoning_id',
        'seasoning_uuid',
        'data_status',
        'sensori_status',
        'kemasan_status',
        'kondisi_fisik_status',
        'note',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
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

    public function seasoning()
    {
        return $this->belongsTo(Seasoning::class, 'seasoning_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getOverallStatusAttribute(): string
    {
        foreach (['data_status', 'sensori_status', 'kemasan_status', 'kondisi_fisik_status'] as $field) {
            if ($this->{$field} !== null && $this->{$field} !== 'OK') {
                return 'NOT_OK';
            }
        }

        return 'OK';
    }

    public function getOverallStatusLabelAttribute(): string
    {
        return $this->overall_status === 'OK' ? 'Oke' : 'Tidak Oke';
    }
}
