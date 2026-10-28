<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InputMesinPeralatan extends Model
{
    use HasFactory;

    protected $table = 'input_mesin_peralatan';

    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'id_area',
        'nama_mesin',
        'jenis_produk',
    ];

    protected $casts = [
        'jenis_produk' => 'array',
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

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function area()
    {
        return $this->belongsTo(InputArea::class, 'id_area');
    }

    public function bagian()
    {
        return $this->hasMany(BagianMesin::class, 'id_mesin');
    }

    public function scopeForUser($query, $user)
    {
        if ($user->role === 'superadmin') {
            return $query;
        }

        return $query->where('id_plan', $user->id_plan);
    }
}
