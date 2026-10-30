<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'uuid',
        'role'
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
     * Relationship ke User - satu role bisa dimiliki banyak user
     */
    public function users()
    {
        return $this->hasMany(User::class, 'id_role', 'id');
    }

    /**
     * Scope untuk mencari berdasarkan nama role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Relationship ke Permission dari Spatie
     */
    public function permissions()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }

    /**
     * Accessor untuk format nama role
     */
    public function getRoleNameAttribute()
    {
        return ucfirst($this->role);
    }
}
