<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BahanForming extends Model
{
    protected $table = 'bahan_forming';
    protected $fillable = [
        'uuid', 'id_plan', 'id_produk', 'id_formula', 'nama_rm', 'berat_rm', 'user_id'
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

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function formula()
    {
        return $this->belongsTo(NomorFormula::class, 'id_formula');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
