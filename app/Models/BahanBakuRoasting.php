<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BahanBakuRoasting extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku_roasting';
    
    protected $fillable = [
        'uuid',
        'input_roasting_uuid',
        'id_plan',
        'user_id',
        'shift_id',
        'id_produk',
        'kode_produksi_rm',
        'standart_suhu_rm',
        'aktual_suhu_rm',
        'tanggal',
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
    // Relasi ke Input Roasting
    public function inputRoasting()
    {
        return $this->belongsTo(InputRoasting::class, 'input_roasting_uuid', 'uuid');
    }
    // Tambahkan accessor untuk mendapatkan shift dari InputRoasting
    public function getShiftDataAttribute()
    {
        // Cek shift dari Input Roasting (Alur Input Roasting)
        if ($this->input_roasting_uuid) {
            $inputRoasting = \App\Models\InputRoasting::where('uuid', $this->input_roasting_uuid)->first();
            if ($inputRoasting && $inputRoasting->shift) {
                return $inputRoasting->shift;
            }
        }
        
        // Fallback ke relationship shift lokal
        return $this->shift;
    }
}
