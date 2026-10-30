<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PembuatanPredust extends Model
{
    use HasFactory;

    protected $table = 'pembuatan_predust';

    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'penggorengan_uuid',
        'id_produk',
        'id_jenis_predust',
        'tanggal',
        'jam',
        'kondisi_predust',
        'hasil_pencetakan',
        'kode_produksi',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    // Relationships
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function jenisPredust()
    {
        return $this->belongsTo(JenisPredust::class, 'id_jenis_predust');
    }

    public function shift()
    {
        return $this->penggorengan ? $this->penggorengan->shift : null;
    }

    public function penggorengan()
    {
        return $this->belongsTo(Penggorengan::class, 'penggorengan_uuid', 'uuid');
    }
}
