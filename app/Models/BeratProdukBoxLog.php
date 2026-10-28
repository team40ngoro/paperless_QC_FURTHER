<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BeratProdukBoxLog extends Model
{
    use HasFactory;

    protected $table = 'berat_produk_box_logs';

    protected $fillable = [
        'uuid',
        'berat_produk_box_id',
        'berat_produk_box_uuid',
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
    public function beratProdukBox()
    {
        return $this->belongsTo(BeratProdukBox::class, 'berat_produk_box_id');
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
            'id_pengemasan_produk' => 'Pengemasan Produk',
            'id_pengemasan_plastik' => 'Pengemasan Plastik',
            'id_shift' => 'Shift',
            'id_data_box' => 'Data Box',
            'id_berat_produk_bag' => 'Berat Produk Bag',
            'user_id' => 'User',
            'line' => 'Line',
            'tanggal' => 'Tanggal',
            'berat_aktual_1' => 'Berat Aktual 1',
            'berat_aktual_2' => 'Berat Aktual 2',
            'berat_aktual_3' => 'Berat Aktual 3'
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

    public function getNamaField($field)
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
}
