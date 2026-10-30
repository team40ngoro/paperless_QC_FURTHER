<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BahanBakuRoastingLog extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku_roasting_logs';

    protected $fillable = [
        'uuid',
        'bahan_baku_roasting_id',
        'bahan_baku_roasting_uuid',
        'user_id',
        'field_yang_diubah',
        'nilai_lama',
        'nilai_baru',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'field_yang_diubah' => 'array',
        'nilai_lama' => 'array',
        'nilai_baru' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relationships
    public function bahanBakuRoasting()
    {
        return $this->belongsTo(BahanBakuRoasting::class, 'bahan_baku_roasting_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Helper methods for field name mapping
    public function getFieldNames()
    {
        return [
            'id_plan' => 'Plan',
            'user_id' => 'User',
            'shift_id' => 'Shift',
            'id_produk' => 'Produk',
            'kode_produksi_rm' => 'Kode Produksi RM',
            'standart_suhu_rm' => 'Standar Suhu RM',
            'aktual_suhu_rm' => 'Aktual Suhu RM',
            'tanggal' => 'Tanggal'
        ];
    }

    public function getNamaFieldAttribute()
    {
        $fieldNames = $this->getFieldNames();
        $namaField = [];
        
        if (is_array($this->field_yang_diubah)) {
            foreach ($this->field_yang_diubah as $field) {
                $namaField[] = $fieldNames[$field] ?? $field;
            }
        }
        
        return implode(', ', $namaField);
    }

    public function getNamaFieldSingle($field)
    {
        $fieldNames = $this->getFieldNames();
        return $fieldNames[$field] ?? $field;
    }

    public function getDeskripsiPerubahanAttribute()
    {
        $fieldNames = $this->getFieldNames();
        $deskripsi = [];
        
        if (is_array($this->field_yang_diubah) && is_array($this->nilai_lama) && is_array($this->nilai_baru)) {
            foreach ($this->field_yang_diubah as $index => $field) {
                $namaField = $fieldNames[$field] ?? $field;
                $nilaiLama = $this->nilai_lama[$index] ?? '-';
                $nilaiBaru = $this->nilai_baru[$index] ?? '-';
                
                $deskripsi[] = "{$namaField}: '{$nilaiLama}' → '{$nilaiBaru}'";
            }
        }
        
        return implode(' | ', $deskripsi);
    }

    // Get user name for display
    public function getUserNameAttribute()
    {
        return $this->user ? $this->user->name : 'System';
    }

    /**
     * Get user name method for compatibility
     */
    public function getUserName()
    {
        return $this->user ? $this->user->name : 'System';
    }

    /**
     * Get field display name
     */
    public function getFieldDisplayName($field)
    {
        $fieldNames = $this->getFieldNames();
        return $fieldNames[$field] ?? $field;
    }

    /**
     * Format field value for display
     */
    public function formatFieldValue($field, $value)
    {
        if (is_null($value)) {
            return 'NULL';
        }

        // Format specific fields
        switch ($field) {
            case 'tanggal':
                return $value ? \Carbon\Carbon::parse($value)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s') : 'NULL';
            case 'created_at':
            case 'updated_at':
                return $value ? \Carbon\Carbon::parse($value)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s') : 'NULL';
            default:
                return $value;
        }
    }
}
