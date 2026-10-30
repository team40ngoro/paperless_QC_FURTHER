<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NomorStepFormulaFla extends Model
{
    use HasFactory;

    protected $table = 'nomor_step_formula_fla';
    
    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'id_nama_formula_fla',
        'proses',
        'nomor_step'
    ];

    /**
     * Boot method to generate UUID
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relationship with Plan model
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    /**
     * Relationship with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship with NamaFormulaFla model
     */
    public function namaFormulaFla()
    {
        return $this->belongsTo(NamaFormulaFla::class, 'id_nama_formula_fla');
    }

    /**
     * Get route key name for model binding
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

}
