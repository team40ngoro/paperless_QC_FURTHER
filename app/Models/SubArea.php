<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubArea extends Model
{
    use HasFactory;
    protected $table = 'sub_area';
    protected $fillable = [
        'uuid',
        'id_input_area',
        'lokasi_area'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }
      public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function inputArea()
    {
        return $this->belongsTo(InputArea::class, 'id_input_area');
    }

    protected function serializeDate($date)
    {
        return $date->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
    }

    
}
