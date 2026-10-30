<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StdSalinitasViskositas extends Model
{
    protected $table = 'std_salinitas_viskositas';
    protected $fillable = [
        'uuid', 'user_id', 'id_plan', 'id_produk', 'id_better',
        'std_viskositas', 'std_salinitas', 'std_suhu_akhir'
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

    public function user() { return $this->belongsTo(User::class); }
    public function plan() { return $this->belongsTo(Plan::class, 'id_plan'); }
    public function produk() { return $this->belongsTo(JenisProduk::class, 'id_produk'); }
    public function better() { return $this->belongsTo(JenisBetter::class, 'id_better'); }
}
