<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProsesBreaderLog extends Model
{
    use HasFactory;

    protected $table = 'proses_breader_logs';

    protected $fillable = [
        'uuid',
        'proses_breader_id',
        'proses_breader_uuid',
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
    public function prosesBreader()
    {
        return $this->belongsTo(ProsesBreader::class, 'proses_breader_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Helper methods for field name mapping
    public function getFieldNames()
    {
        return [
            'battering_uuid' => 'Battering UUID',
            'predust_uuid' => 'Predust UUID',
            'penggorengan_uuid' => 'Penggorengan UUID',
            'id_produk' => 'Produk',
            'user_id' => 'User',
            'id_plan' => 'Plan',
            'id_jenis_breader' => 'Jenis Breader',
            'id_shift' => 'Shift',
            'kode_produksi' => 'Kode Produksi',
            'hasil_breader' => 'Hasil Breader',
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
                
                // Handle array values
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

    // Additional helper methods for logs view
    public function getUserNameAttribute()
    {
        return $this->user->name ?? 'System';
    }

    public function getUserRoleAttribute()
    {
        return $this->user->role ?? 'N/A';
    }
}
