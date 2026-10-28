<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HasilProsesRoastingLog extends Model
{
    use HasFactory;

    protected $table = 'hasil_proses_roasting_logs';

    protected $fillable = [
        'uuid',
        'hasil_proses_roasting_id',
        'hasil_proses_roasting_uuid',
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
    public function hasilProsesRoasting()
    {
        return $this->belongsTo(HasilProsesRoasting::class, 'hasil_proses_roasting_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Field name mapping
    public function getFieldNames()
    {
        return [
            'id_plan' => 'Plan',
            'user_id' => 'User',
            'id_shift' => 'Shift',
            'id_produk' => 'Produk',
            'id_std_suhu_pusat' => 'STD Suhu Pusat',
            'aktual_suhu_pusat' => 'Aktual Suhu Pusat',
            'sensori' => 'Sensori',
            'tanggal' => 'Tanggal',
            'line_blok' => 'Line Blok',
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

    public function getUserName()
    {
        return $this->user ? $this->user->name : 'System';
    }

    public function getFieldDisplayName($field)
    {
        $fieldNames = $this->getFieldNames();
        return $fieldNames[$field] ?? $field;
    }

    public function formatFieldValue($field, $value)
    {
        if (is_null($value)) {
            return 'NULL';
        }

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
