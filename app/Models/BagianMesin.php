<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BagianMesin extends Model
{
    use HasFactory;

    protected $table = 'bagian_mesin';

    protected $fillable = [
        'uuid',
        'id_mesin',
        'nama_bagian',
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

    public function mesin()
    {
        return $this->belongsTo(InputMesinPeralatan::class, 'id_mesin');
    }
}
