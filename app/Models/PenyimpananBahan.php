<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Plan;
use Carbon\Carbon;

class PenyimpananBahan extends Model
{
    protected $table = 'penyimpanan_bahan';
    protected $fillable = [
        'uuid', 'group_uuid', 'shift_id', 'id_plan', 'user_id', 'tanggal',
        'pemeriksaan_kondisi', 'pemeriksaan_kebersihan', 'kebersihan_ruang'
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
            if (empty($model->group_uuid)) {
                // default group to itself if not provided
                $model->group_uuid = $model->uuid;
            }
        });
    }
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
