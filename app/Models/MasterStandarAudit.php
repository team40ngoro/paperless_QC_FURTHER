<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MasterStandarAudit extends Model
{
    use HasFactory;

    protected $table = 'master_standar_audits';

    protected $fillable = [
        'uuid',
        'id_plan',
        'module_name',
        'area_id',
        'table_name',
        'column_name',
        'parameter_type',
        'min_value',
        'max_value',
        'standar_value',
        'auto_fix_status',
    ];

    /**
     * Relasi ke Master Area (InputArea)
     */
    public function area()
    {
        return $this->belongsTo(\App\Models\InputArea::class, 'area_id');
    }

    protected $casts = [
        'min_value' => 'float',
        'max_value' => 'float',
        'standar_value' => 'float',
        'auto_fix_status' => 'boolean',
    ];

    /**
     * Accessor untuk mendapatkan Label Kolom yang ramah pengguna dari config master_audit
     */
    public function getColumnLabelAttribute()
    {
        $configModules = config('master_audit.modules', []);
        $moduleConfig = $configModules[$this->module_name] ?? null;

        if ($moduleConfig) {
            // Cek main table fields
            if (isset($moduleConfig['fields'][$this->column_name]['label'])) {
                return $moduleConfig['fields'][$this->column_name]['label'];
            }

            // Cek relations table fields
            if (!empty($moduleConfig['relations'])) {
                foreach ($moduleConfig['relations'] as $rel) {
                    if (isset($rel['fields'][$this->column_name]['label'])) {
                        return $rel['fields'][$this->column_name]['label'];
                    }
                }
            }
        }

        return ucwords(str_replace('_', ' ', $this->column_name));
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
}
