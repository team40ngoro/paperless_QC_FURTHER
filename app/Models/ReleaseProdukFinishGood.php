<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasAudit;

class ReleaseProdukFinishGood extends Model
{
    use HasAudit;

    protected $table = 'release_produk_finish_goods';
    
    protected $fillable = [
        'uuid',
        'tanggal',
        'jam',
        'id_plan',
        'id_shift',
        'id_produk',
        'user_id',
        'suhu_iqf',
        'suhu_produk',
        'lama_pembekuan',
        'foto_kemasan_sekunder',
        'foto_kemasan_primer',
        'kondisi_kemasan_primer',
        'berat_packing_sekunder',
        'jumlah_box',
        'status',
        'keterangan',
        'tindakan_koreksi',
        'kode_form',
        'is_audit',
        'source_uuid',
        'approved_by_qc',
        'qc_approved_by',
        'qc_approved_at',
        'approved_by_produksi',
        'produksi_approved_by',
        'produksi_approved_at',
        'approved_by_spv',
        'spv_approved_by',
        'spv_approved_at',
    ];

    protected $casts = [
        'berat_packing_sekunder' => 'array',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'is_audit' => 'boolean',
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

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'id_shift');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

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