<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShoestringQcVerification extends Model
{
    use HasFactory;

    protected $table = 'shoestring_qc_verifications';

    protected $fillable = [
        'uuid',
        'shoestring_id',
        'shoestring_uuid',
        'verifikasi',
        'sampling_defect',
        'sampling_defect_qty',
        'total_defect',
        'note',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'sampling_defect_qty' => 'array',
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

    public function shoestring()
    {
        return $this->belongsTo(Shoestring::class, 'shoestring_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getVerifikasiLabelAttribute(): string
    {
        return $this->verifikasi === 'OK' ? 'Oke' : 'Tidak Oke';
    }
}
