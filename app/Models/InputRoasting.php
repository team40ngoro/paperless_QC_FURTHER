<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InputRoasting extends Model
{
    use HasFactory;

    protected $table = 'input_roasting';
    
    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'shift_id',
        'id_produk',
        'kode_produksi',
        'berat_produk',
        'std_suhu_sebelum',
        'aktual_suhu_sesudah',
        'tanggal',
        'jam',
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
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
    /**
     * Get the route key for the model.
     */
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

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function pembekuanIqfRoastingData()
    {
        return $this->hasOne(PembekuanIqfRoasting::class, 'input_roasting_uuid', 'uuid');
    }
}
