<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChillroomQcVerification extends Model
{
    use HasFactory;

    protected $table = 'chillroom_qc_verifications';

    protected $fillable = [
        'uuid',
        'penerimaan_chillroom_id',
        'penerimaan_chillroom_uuid',
        'status',
        'note',
        'suhu_rm',
        'sensori',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'suhu_rm' => 'float',
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

    public function penerimaanChillroom()
    {
        return $this->belongsTo(PenerimaanChillroom::class, 'penerimaan_chillroom_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'OK' ? 'Oke' : 'Tidak Oke';
    }
}
