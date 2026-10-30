<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PemeriksaanBahanKemasLog extends Model
{
    protected $table = 'pemeriksaan_bahan_kemas_logs';

    protected $fillable = [
        'uuid',
        'pemeriksaan_bahan_kemas_id',
        'pemeriksaan_bahan_kemas_uuid',
        'user_id',
        'user_name',
        'user_role',
        'aksi',
        'field_yang_diubah',
        'nilai_lama',
        'nilai_baru',
        'ip_address',
        'user_agent',
        'keterangan',
    ];

    protected $casts = [
        'field_yang_diubah' => 'array',
        'nilai_lama' => 'array',
        'nilai_baru' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    public function pemeriksaanBahanKemas()
    {
        return $this->belongsTo(PemeriksaanBahanKemas::class, 'pemeriksaan_bahan_kemas_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getFieldNames()
    {
        return [
            'id_plan' => 'Plan',
            'shift_id' => 'Shift',
            'tanggal' => 'Tanggal',
            'jam' => 'Jam',
            'nama_kemasan' => 'Nama Kemasan',
            'kode_produksi' => 'Kode Produksi',
            'kondisi_bahan_kemasan' => 'Kondisi Bahan Kemasan',
            'keterangan' => 'Keterangan',
        ];
    }

    public function getNamaFieldSingle($field)
    {
        $fieldNames = self::getFieldNames();

        return $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}
