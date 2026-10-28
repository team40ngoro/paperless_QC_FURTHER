<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DataBarang extends Model
{
    use HasFactory;

    protected $table = 'data_barang';
    
    protected $fillable = [
        'uuid',
        'id_plan',
        'id_area',
        'user_id',
        'nama_barang',
        'jumlah',
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

    /**
     * Relationship to Plan model
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function area()
    {
        return $this->belongsTo(InputArea::class, 'id_area');
    }

    /**
     * Relationship to User model
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope for filtering by plan based on user role
     */
    public function scopeForUser($query, $user)
    {
        if ($user->role === 'superadmin') {
            return $query;
        }
        
        return $query->where('id_plan', $user->id_plan);
    }
}
