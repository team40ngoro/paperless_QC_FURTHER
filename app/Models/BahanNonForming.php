<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BahanNonForming extends Model
{
    use HasFactory;
    protected $table = 'bahan_rm_non_forming';
     protected $fillable = [
       'id_no_formula_non_forming',
        'nama_rm',
        'berat_rm',
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

    public function produkNonForming(): BelongsTo
    {
        return $this->belongsTo(MasterProdukNonForming::class,  'id_no_formula_non_forming');
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
