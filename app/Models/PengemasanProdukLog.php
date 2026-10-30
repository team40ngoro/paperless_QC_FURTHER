<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PengemasanProdukLog extends Model
{
    protected $table = 'pengemasan_produk_logs';
    
    protected $fillable = [
        'uuid',
        'pengemasan_produk_id',
        'pengemasan_produk_uuid',
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
     * Relasi ke tabel pengemasan_produk
     */
    public function pengemasanProduk()
    {
        return $this->belongsTo(PengemasanProduk::class, 'pengemasan_produk_id');
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
            'id_produk' => 'Produk',
            'id_shift' => 'Shift',
            'tanggal' => 'Tanggal',
            'berat' => 'Berat',
            'tanggal_expired' => 'Tanggal Expired',
            'kode_produksi' => 'Kode Produksi',
            'std_suhu_produk_iqf' => 'Standar Suhu Produk IQF',
            'aktual_suhu_produk' => 'Aktual Suhu Produk',
            'waktu_awal_packing' => 'Waktu Awal Packing',
            'waktu_selesai_packing' => 'Waktu Selesai Packing'
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
            'id_produk' => 'Produk',
            'id_shift' => 'Shift',
            'tanggal' => 'Tanggal',
            'berat' => 'Berat',
            'tanggal_expired' => 'Tanggal Expired',
            'kode_produksi' => 'Kode Produksi',
            'std_suhu_produk_iqf' => 'Standar Suhu Produk IQF',
            'aktual_suhu_produk' => 'Aktual Suhu Produk',
            'waktu_awal_packing' => 'Waktu Awal Packing',
            'waktu_selesai_packing' => 'Waktu Selesai Packing'
        ];

        return $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}
