<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BahanBakuTumblingLog extends Model
{
    protected $table = 'bahan_baku_tumbling_logs';
    
    protected $fillable = [
        'uuid',
        'bahan_baku_tumbling_id',
        'bahan_baku_tumbling_uuid',
        'user_id',
        'user_name',
        'user_role',
        'aksi',
        'field_yang_diubah',
        'nilai_lama',
        'nilai_baru',
        'ip_address',
        'user_agent',
        'keterangan'
    ];

    protected $casts = [
        'field_yang_diubah' => 'array',
        'nilai_lama' => 'array',
        'nilai_baru' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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

    /**
     * Relasi ke tabel bahan_baku_tumbling
     */
    public function bahanBakuTumbling()
    {
        return $this->belongsTo(BahanBakuTumbling::class, 'bahan_baku_tumbling_id');
    }

    /**
     * Relasi ke tabel users
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Method untuk mendapatkan nama field yang user-friendly
     */
    public function getNamaFieldAttribute()
    {
        $fieldNames = [
            'id_plan' => 'Plan',
            // 'user_id' => 'User', // Removed to hide from logs
            'shift_id' => 'Shift',
            'tanggal' => 'Tanggal',
            'id_produk' => 'Produk',
            'kode_produksi' => 'Kode Produksi',
            'nama_bahan_baku' => 'Nama Bahan Baku',
            'kode_produksi_bahan_baku' => 'Kode Produksi Bahan Baku',
            'jumlah' => 'Jumlah',
            'suhu' => 'Suhu',
            'kondisi_daging' => 'Kondisi Daging'
        ];

        $namaField = [];
        foreach ($this->field_yang_diubah as $field) {
            // Skip user_id field from display
            if ($field === 'user_id') {
                continue;
            }
            $namaField[] = $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
        }

        return implode(', ', $namaField);
    }

     /**
     * Method untuk mendapatkan deskripsi perubahan yang mudah dibaca
     */
    public function getDeskripsiPerubahanAttribute()
    {
        $deskripsi = [];
        if ($this->nilai_lama && $this->nilai_baru) {
            foreach ($this->field_yang_diubah as $field) {
                // Skip user_id field from display
                if ($field === 'user_id') {
                    continue;
                }
                
                $nilaiLama = $this->nilai_lama[$field] ?? 'Kosong';
                $nilaiBaru = $this->nilai_baru[$field] ?? 'Kosong';
                
                $namaField = $this->getNamaFieldSingle($field);
                $deskripsi[] = "{$namaField}: '{$nilaiLama}' → '{$nilaiBaru}'";
            }
        }
        return implode('; ', $deskripsi);
    }
    /**
     * Helper method untuk mendapatkan nama field tunggal
     */
    public function getNamaFieldSingle($field)
    {
        $fieldNames = [
            'id_plan' => 'Plan',
            // 'user_id' => 'User',
            'shift_id' => 'Shift',
            'tanggal' => 'Tanggal',
            'id_produk' => 'Produk',
            'kode_produksi' => 'Kode Produksi',
            'nama_bahan_baku' => 'Nama Bahan Baku',
            'kode_produksi_bahan_baku' => 'Kode Produksi Bahan Baku',
            'jumlah' => 'Jumlah',
            'suhu' => 'Suhu',
            'kondisi_daging' => 'Kondisi Daging'
        ];

        return $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
    
}
