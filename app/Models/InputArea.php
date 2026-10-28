<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InputArea extends Model
{
    use HasFactory;

    protected $table = 'input_area';
    
    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'area',
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
     * Relationship to Plan model
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    /**
     * Relationship to User model
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
      public function subarea()
    {
        return $this->hasMany(SubArea::class, 'id_input_area');
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
