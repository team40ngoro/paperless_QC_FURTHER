<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Traits\HasAudit;

class PersiapanBahanBetter extends Model
{
    use HasFactory, HasAudit;

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $table = 'persiapan_bahan_better';
    protected $fillable = [
        'uuid', 'user_id', 'id_plan', 'id_produk', 'id_better', 
        'berat_better', 'gramase', 'suhu_air', 'sensori', 
        'better_rows',
        'kode_produksi_produk', 'kode_produksi_better', 'kode_form', 'shift_id', 'tanggal', 'jam',
        // Approval fields
        'approved_by_qc', 'approved_by_produksi', 'approved_by_spv',
        'qc_approved_by', 'produksi_approved_by', 'spv_approved_by',
        'qc_approved_at', 'produksi_approved_at', 'spv_approved_at',
        // Audit fields
        'is_audit', 'source_uuid'
    ];
     protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'better_rows' => 'array',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean', 
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) $model->uuid = (string) Str::uuid();
        });
    }
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }

    public function copyToAudit(): self
    {
        $clone = $this->replicate();
        $clone->uuid = (string) Str::uuid();
        $clone->is_audit = true;
        $clone->source_uuid = $this->uuid;

        $booleanApprovalFields = ['approved_by_qc', 'approved_by_produksi', 'approved_by_spv'];
        $nullableApprovalFields = [
            'qc_approved_by', 'qc_approved_at',
            'produksi_approved_by', 'produksi_approved_at',
            'spv_approved_by', 'spv_approved_at',
        ];

        if (!$this->keepApprovalsOnAudit()) {
            foreach ($booleanApprovalFields as $field) {
                if (array_key_exists($field, $clone->getAttributes())) {
                    $clone->{$field} = false;
                }
            }
            foreach ($nullableApprovalFields as $field) {
                if (array_key_exists($field, $clone->getAttributes())) {
                    $clone->{$field} = null;
                }
            }
        }

        $clone->save();

        foreach ($this->aktuals as $aktual) {
            $aClone = $aktual->replicate();
            $aClone->id_persiapan_bahan_better = $clone->id;
            $aClone->uuid = (string) Str::uuid();
            $aClone->save();
        }

        return $clone;
    }

    public function user() { return $this->belongsTo(User::class); }
    public function plan() { return $this->belongsTo(Plan::class, 'id_plan'); }
    public function produk() { return $this->belongsTo(JenisProduk::class, 'id_produk'); }
    public function better() { return $this->belongsTo(JenisBetter::class, 'id_better'); }
    public function aktuals() { return $this->hasMany(AktualBetter::class, 'id_persiapan_bahan_better'); }
    public function shift() { return $this->belongsTo(\App\Models\DataShift::class, 'shift_id'); }
    
    public function qcApprover() { return $this->belongsTo(User::class, 'qc_approved_by'); }
    public function produksiApprover() { return $this->belongsTo(User::class, 'produksi_approved_by'); }
    public function spvApprover() { return $this->belongsTo(User::class, 'spv_approved_by'); }
}