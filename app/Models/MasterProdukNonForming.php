<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
class MasterProdukNonForming extends Model
{
    protected $table = 'no_formula_non_forming';
    protected $fillable = [
        'id_produk',
        'nomor_formula',
        'user_id',
        'id_plan',
        'uuid',
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
public function bahanNonForming(): HasMany
{
    return $this->hasMany(BahanNonForming::class, 'id_no_formula_non_forming');
}

    public function produk(): BelongsTo
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }
}
