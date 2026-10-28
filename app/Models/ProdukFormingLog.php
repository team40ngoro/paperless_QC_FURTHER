<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProdukFormingLog extends Model
{
    use HasFactory;

    protected $table = 'produk_forming_logs';

    protected $fillable = [
        'uuid',
        'produk_forming_id',
        'produk_forming_uuid',
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
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relationships
    public function produkForming()
    {
        return $this->belongsTo(ProdukForming::class, 'produk_forming_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Helper methods untuk field names
    public static function getFieldNames()
    {
        return [
            'id_produk' => 'Produk',
            'id_plan' => 'Plan',
            'id_shift' => 'Shift',
            'tanggal' => 'Tanggal',
            'bahan_baku' => 'Bahan Baku',
            'bahan_penunjang' => 'Bahan Penunjang',
            'kemasan_plastik' => 'Kemasan Plastik',
            'kemasan_karton' => 'Kemasan Karton',
            'labelisasi_plastik' => 'Labelisasi Plastik',
            'labelisasi_karton' => 'Labelisasi Karton',
            'autogrind' => 'Autogrind',
            'bowlcutter' => 'Bowl Cutter',
            'ayakan_seasoning' => 'Ayakan Seasoning',
            'unimix' => 'Unimix',
            'revoformer' => 'Revoformer',
            'better_mixer' => 'Better Mixer',
            'wet_coater' => 'Wet Coater',
            'breader' => 'Breader',
            'frayer_1' => 'Frayer 1',
            'frayer_2' => 'Frayer 2',
            'iqf_jbt' => 'IQF JBT',
            'keranjang' => 'Keranjang',
            'timbangan' => 'Timbangan',
            'mhw' => 'MHW',
            'foot_sealer' => 'Foot Sealer',
            'metal_detector' => 'Metal Detector',
            'rotary_table' => 'Rotary Table',
            'carton_sealer' => 'Carton Sealer',
            'meatcar' => 'Meatcar',
            'check_weigher_bag' => 'Check Weigher Bag',
            'check_weigher_box' => 'Check Weigher Box',
            'penilaian' => 'Penilaian',
            'tindakan_koreksi' => 'Tindakan Koreksi',
            'verifikasi' => 'Verifikasi'
        ];
    }

    public function getNamaFieldAttribute()
    {
        $fieldNames = self::getFieldNames();
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
        $fieldNames = self::getFieldNames();
        $deskripsi = [];
        
        if (is_array($this->field_yang_diubah)) {
            foreach ($this->field_yang_diubah as $index => $field) {
                $namaField = $fieldNames[$field] ?? $field;
                $nilaiLama = $this->nilai_lama[$index] ?? '-';
                $nilaiBaru = $this->nilai_baru[$index] ?? '-';
                
                // Handle array values (for bahan_baku, bahan_penunjang, penilaian)
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
}
