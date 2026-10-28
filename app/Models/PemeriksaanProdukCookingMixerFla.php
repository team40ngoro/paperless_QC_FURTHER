<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Traits\HasAudit;

class PemeriksaanProdukCookingMixerFla extends Model
{
    use HasAudit;

    protected $table = 'pemeriksaan_produk_cooking_mixer_fla';
    
    protected $fillable = [
        'uuid',
        'is_audit',
        'source_uuid',
        'id_plan',
        'user_id',
        'shift_id',
        'tanggal',
        'jam',
        'id_frm_fla',
        'id_stp_frm_fla',
        'id_nama_formula_fla',
        'kode_produksi_bahan',
        'kode_produksi',
        'berat',
        'waktu_start',
        'waktu_stop',
        'sensori_kondisi',
        'status_gas',
        'lama_proses',
        'speed',
        'temp_std_1',
        'temp_std_2',
        'temp_std_3',
        'organo_warna',
        'organo_aroma',
        'organo_tekstur',
        'organo_rasa',
        'catatan',
        'kode_form',
        'approved_by_qc',
        'approved_by_produksi',
        'approved_by_spv',
        'qc_approved_by',
        'produksi_approved_by',
        'spv_approved_by',
        'qc_approved_at',
        'produksi_approved_at',
        'spv_approved_at',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'status_gas' => 'boolean',
        'kode_produksi_bahan' => 'array',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
    ];

    public function getKodeProduksiBahanArray()
    {
        if (is_array($this->kode_produksi_bahan)) {
            return $this->kode_produksi_bahan;
        }
        if (is_string($this->kode_produksi_bahan)) {
            return json_decode($this->kode_produksi_bahan, true) ?? [];
        }
        return [];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
    
    public function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        date_default_timezone_set('Asia/Jakarta');
    }

    /**
     * Get route key name for model binding
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    public function bahanFormulaFla()
    {
        return $this->belongsTo(BahanFormulaFla::class, 'id_frm_fla');
    }

    public function nomorStepFormulaFla()
    {
        return $this->belongsTo(NomorStepFormulaFla::class, 'id_stp_frm_fla');
    }

    public function namaFormulaFla()
    {
        return $this->belongsTo(NamaFormulaFla::class, 'id_nama_formula_fla');
    }

    /**
     * Get status gas as text
     */
    public function getStatusGasTextAttribute()
    {
        return $this->status_gas ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Get organoleptic status icon
     */
    public function getOrganoIcon($value)
    {
        return $value === 'OK' ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>';
    }

    /**
     * Approval relationships
     */
    public function qcApprover()
    {
        return $this->belongsTo(User::class, 'qc_approved_by');
    }

    public function produksiApprover()
    {
        return $this->belongsTo(User::class, 'produksi_approved_by');
    }

    public function spvApprover()
    {
        return $this->belongsTo(User::class, 'spv_approved_by');
    }
}
