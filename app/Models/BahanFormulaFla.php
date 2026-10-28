<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BahanFormulaFla extends Model
{
    protected $table = 'bahan_formula_fla';
    
    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'id_nama_formula_fla',
        'id_nomor_step_formula_fla',
        'bahan_formula_fla',
        'berat_formula_fla',
    ];

    protected $casts = [
        'bahan_formula_fla' => 'array',
        'berat_formula_fla' => 'array',
    ];

    /**
     * Get proses from related step formula
     */
    public function getProsesArrayAttribute()
    {
        if ($this->nomorStepFormulaFla && $this->nomorStepFormulaFla->proses) {
            return explode(',', $this->nomorStepFormulaFla->proses);
        }
        return [];
    }

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
     * Get route key name for model binding
     */
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

    public function namaFormulaFla()
    {
        return $this->belongsTo(NamaFormulaFla::class, 'id_nama_formula_fla');
    }

    public function nomorStepFormulaFla()
    {
        return $this->belongsTo(NomorStepFormulaFla::class, 'id_nomor_step_formula_fla');
    }

    /**
     * Get bahan formula as array
     */
    public function getBahanFormulaArray()
    {
        return is_array($this->bahan_formula_fla) ? $this->bahan_formula_fla : [];
    }

    /**
     * Get berat formula as array
     */
    public function getBeratFormulaArray()
    {
        return is_array($this->berat_formula_fla) ? $this->berat_formula_fla : [];
    }
}
