<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PenggorenganLog extends Model
{
    use HasFactory;

    protected $table = 'penggorengan_logs';

    protected $fillable = [
        'uuid',
        'penggorengan_id',
        'penggorengan_uuid',
        'user_id',
        'user_name',
        'field_yang_diubah',
        'nilai_lama',
        'nilai_baru',
        'aksi',
        'keterangan',
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
    public function penggorengan()
    {
        return $this->belongsTo(Penggorengan::class, 'penggorengan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Helper methods for field name mapping
    public function getFieldNames()
    {
        return [
            'id_produk' => 'Produk',
            'shift_id' => 'Shift',
            'id_plan' => 'Plan',
            'user_id' => 'User',
            'kode_produksi' => 'Kode Produksi',
            'no_of_strokes' => 'No of Strokes',
            'tanggal' => 'Tanggal',
            'hasil_pencetakan' => 'Hasil Pencetakan',
            'waktu_pemasakan' => 'Waktu Pemasakan',
            'waktu_selesai_pemasakan' => 'Waktu Selesai Pemasakan'
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

    public function getNamaFieldSingle($field)
    {
        $fieldNames = $this->getFieldNames();
        return $fieldNames[$field] ?? $field;
    }
}
