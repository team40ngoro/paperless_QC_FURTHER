<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProsesMarinadeModel extends Model
{
    use HasFactory;

    protected $table = 'proses_marinade';

    protected $fillable = [
        'uuid',
        'bahan_baku_tumbling_uuid',
        'bahan_baku_tumbling_id',
        'id_shift',
        'id_plan',
        'id_user',
        'id_jenis_marinade',
        'kode_produksi',
        'kode_form',
        'jumlah',
        'tanggal',
        'hasil_pencampuran'
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'jumlah' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
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
    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'id_shift');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function jenisMarinade()
    {
        return $this->belongsTo(JenisMarinade::class, 'id_jenis_marinade');
    }

    // Relasi ke Bahan Baku Tumbling
    public function bahanBakuTumbling()
    {
        return $this->belongsTo(BahanBakuTumbling::class, 'bahan_baku_tumbling_id');
    }

    // Relasi ke Proses Tumbling
    public function prosesTumblings()
    {
        return $this->hasMany(ProsesTumbling::class, 'proses_marinade_id');
    }
}
