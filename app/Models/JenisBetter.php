<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JenisBetter extends Model
{
    use HasFactory;

    protected $table = 'jenis_better';
    protected $fillable = [
        'uuid', 'user_id', 'id_plan', 'id_produk', 'nama_better', 'berat', 'nama_formula_better', 'better_items'
    ];

    protected $casts = [
        'better_items' => 'array',
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
}
