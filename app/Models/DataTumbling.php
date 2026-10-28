<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DataTumbling extends Model
{
    use HasFactory;

    protected $table = 'data_tumbling';

    protected $fillable = [
        'uuid',
        'id_plan',
        'id_produk',
        'user_id',
        'drum_on',
        'drum_off',
        'drum_speed',
        'total_waktu',
        'tekanan_vakum',
        'drum_on_non_vakum',
        'drum_off_non_vakum',
        'drum_speed_non_vakum',
        'total_waktu_non_vakum',
        'tekanan_non_vakum'
    ];

    protected $casts = [
        'drum_speed' => 'string',
        'tekanan_vakum' => 'string',
        'drum_on' => 'string',
        'drum_off' => 'string',
        'total_waktu' => 'string',
        'drum_speed_non_vakum' => 'string',
        'tekanan_non_vakum' => 'string',
        'drum_on_non_vakum' => 'string',
        'drum_off_non_vakum' => 'string',
        'total_waktu_non_vakum' => 'string'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
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
}
