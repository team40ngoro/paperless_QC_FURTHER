<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PengemasanPlastikLog extends Model
{
    protected $table = 'pengemasan_plastik_logs';
    
    protected $fillable = [
        'uuid',
        'pengemasan_plastik_id',
        'pengemasan_plastik_uuid',
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
     * Relasi ke tabel pengemasan_plastik
     */
    public function pengemasanPlastik()
    {
        return $this->belongsTo(PengemasanPlastik::class, 'pengemasan_plastik_id');
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
            'id_pengemasan_produk' => 'Pengemasan Produk',
            'berat' => 'Berat',
            'id_plan' => 'Plan',
            'id_shift' => 'Shift',
            'tanggal' => 'Tanggal',
            'proses_penimbangan' => 'Proses Penimbangan',
            'proses_sealing' => 'Proses Sealing',
            'identitas_produk' => 'Identitas Produk',
            'nomor_md' => 'Nomor MD',
            'kode_kemasan_plastik' => 'Kode Kemasan Plastik',
            'kekuatan_seal' => 'Kekuatan Seal'
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
            'id_pengemasan_produk' => 'Pengemasan Produk',
            'berat' => 'Berat',
            'id_plan' => 'Plan',
            'id_shift' => 'Shift',
            'tanggal' => 'Tanggal',
            'proses_penimbangan' => 'Proses Penimbangan',
            'proses_sealing' => 'Proses Sealing',
            'identitas_produk' => 'Identitas Produk',
            'nomor_md' => 'Nomor MD',
            'kode_kemasan_plastik' => 'Kode Kemasan Plastik',
            'kekuatan_seal' => 'Kekuatan Seal'
        ];

        return $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}
