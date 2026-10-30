<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ListPeralatan extends Model
{
    use HasFactory;

    protected $table = 'list_peralatan';

    protected $fillable = [
        'uuid',
        'id_plan',
        'id_area',
        'user_id',
        'nama_alat',
        'jumlah_alat',
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

    /**
     * Relationship to Plan model
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    /**
     * Relationship to InputArea model
     */
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
     * Accessor alias for list_alat attribute
     */
    public function getListAlatAttribute()
    {
        return $this->nama_alat;
    }

    /**
     * Scope for filtering by user role / plan
     */
    public function scopeForUser($query, $user)
    {
        if ($user->role === 'superadmin') {
            return $query;
        }

        return $query->where('id_plan', $user->id_plan);
    }
}
