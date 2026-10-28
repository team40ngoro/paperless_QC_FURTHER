<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProsesRoastingFanLog extends Model
{
    use HasFactory;

    protected $table = 'proses_roasting_fan_logs';

    protected $fillable = [
        'uuid',
        'proses_roasting_fan_id',
        'proses_roasting_fan_uuid',
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
    public function prosesRoastingFan()
    {
        return $this->belongsTo(ProsesRoastingFan::class, 'proses_roasting_fan_id');
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
            'block_numbers' => 'Nomor Blok',
            'aktual_lama_proses' => 'Aktual Lama Proses',
            'tanggal' => 'Tanggal'
        ];
    }

    public function getNamaFieldAttribute()
    {
        $fieldNames = $this->getFieldNames();
        $namaField = [];
        
        if (is_array($this->field_yang_diubah)) {
            foreach ($this->field_yang_diubah as $field) {
                // Ensure field is a string before using it
                if (is_string($field)) {
                    $namaField[] = $fieldNames[$field] ?? $field;
                }
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
                
                // Handle array values (for block_numbers)
                if (is_array($nilaiLama)) {
                    $nilaiLama = json_encode($nilaiLama);
                }
                if (is_array($nilaiBaru)) {
                    $nilaiBaru = json_encode($nilaiBaru);
                }
                
                $deskripsi[] = "{$namaField}: '{$nilaiLama}' → '{$nilaiBaru}'";
            }
        }
        
        return implode(' | ', $deskripsi);
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

        // Handle array values first
        if (is_array($value)) {
            return json_encode($value);
        }

        // Ensure value is a string for further processing
        if (!is_string($value) && !is_numeric($value)) {
            return (string) $value;
        }

        // Format specific fields
        switch ($field) {
            case 'tanggal':
                return $value ? \Carbon\Carbon::parse($value)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s') : 'NULL';
            case 'created_at':
            case 'updated_at':
                return $value ? \Carbon\Carbon::parse($value)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s') : 'NULL';
            case 'block_numbers':
                return $value;
            default:
                return (string) $value;
        }
    }
}
