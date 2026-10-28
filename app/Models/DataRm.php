<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class DataRm extends Model
{
    use HasFactory;

    protected $table = 'data_rm';

    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'nama_rm',
    ];

    /**
     * Boot function untuk auto-generate UUID
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    /**
     * Route key name untuk menggunakan UUID sebagai parameter
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Relationship ke Plan
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    /**
     * Relationship ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope untuk filter berdasarkan role user
     */
    public function scopeFilterByRole($query)
    {
        $user = Auth::user();
        
        if ($user && $user->role !== 'superadmin') {
            return $query->where('id_plan', $user->id_plan);
        }
        
        return $query;
    }

    /**
     * Cek apakah user dapat mengakses data ini
     */
    public function canAccess()
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        if ($user->role === 'superadmin') {
            return true;
        }
        
        return $this->id_plan == $user->id_plan;
    }
}