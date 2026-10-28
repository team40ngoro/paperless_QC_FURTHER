<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasAudit;

class ProsesAging extends Model
{
    use HasFactory, HasAudit;

    protected $table = 'proses_aging';

    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'id_produk',
        'proses_tumbling_id',
        'proses_tumbling_uuid',
        'bahan_baku_tumbling_uuid',
        'waktu_mulai_aging',
        'waktu_selesai_aging',
        'suhu_produk',
        'kondisi_produk',
        'jam',
        'tanggal',
        'kode_form',
        'is_audit',
        'source_uuid',
        'approved_by_qc',
        'approved_by_spv',
        'approved_by_produksi',
        'qc_approved_by',
        'spv_approved_by',
        'produksi_approved_by',
        'qc_approved_at',
        'spv_approved_at',
        'produksi_approved_at',
    ];

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'tanggal' => 'date',
        'approved_by_qc' => 'boolean',
        'approved_by_spv' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'is_audit' => 'boolean',
        'qc_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
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
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function prosesTumbling()
    {
        return $this->belongsTo(ProsesTumbling::class, 'proses_tumbling_uuid', 'uuid');
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBakuTumbling::class, 'bahan_baku_tumbling_uuid', 'uuid');
    }

    public function prosesTumblings()
    {
        return $this->hasManyThrough(
            ProsesTumbling::class,
            BahanBakuTumbling::class,
            'uuid', // Foreign key on bahan_baku_tumbling
            'bahan_baku_tumbling_uuid', // Foreign key on proses_tumbling
            'bahan_baku_tumbling_uuid', // Local key on proses_aging
            'uuid' // Local key on bahan_baku_tumbling
        );
    }

    public function copyToAudit(): self
    {
        $uuidMap = [];

        // 1. Clone BahanBakuTumbling
        if ($this->bahan_baku_tumbling_uuid && ($bahanBaku = $this->bahanBaku)) {
            $c = $bahanBaku->replicate();
            $c->uuid = (string) Str::uuid();
            $c->save();
            $uuidMap['bahan_baku_tumbling_uuid'] = $c->uuid;
        } else {
            $uuidMap['bahan_baku_tumbling_uuid'] = $this->bahan_baku_tumbling_uuid;
        }

        // 2. Clone all ProsesTumbling records related via bahan_baku_tumbling_uuid
        $newTumblingUuid = null;
        if ($this->bahan_baku_tumbling_uuid) {
            $tumblingRecords = ProsesTumbling::where('bahan_baku_tumbling_uuid', $this->bahan_baku_tumbling_uuid)->get();
            foreach ($tumblingRecords as $tumbling) {
                $tc = $tumbling->replicate();
                $tc->uuid = (string) Str::uuid();
                $tc->bahan_baku_tumbling_uuid = $uuidMap['bahan_baku_tumbling_uuid'];
                $tc->save();
                if (!$newTumblingUuid) {
                    $newTumblingUuid = $tc->uuid;
                }
            }
        }

        // 3. Clone ProsesAging itself
        $clone = $this->replicate();
        $clone->uuid = (string) Str::uuid();
        $clone->is_audit = true;
        $clone->source_uuid = $this->uuid;
        $clone->bahan_baku_tumbling_uuid = $uuidMap['bahan_baku_tumbling_uuid'];
        if ($newTumblingUuid) {
            $clone->proses_tumbling_uuid = $newTumblingUuid;
        }

        // Reset approval fields
        if (!$this->keepApprovalsOnAudit()) {
            $booleanFields = ['approved_by_qc', 'approved_by_produksi', 'approved_by_spv'];
            $nullableFields = [
                'qc_approved_by', 'qc_approved_at',
                'produksi_approved_by', 'produksi_approved_at',
                'spv_approved_by', 'spv_approved_at',
            ];
            foreach ($booleanFields as $field) {
                if (array_key_exists($field, $clone->getAttributes())) {
                    $clone->{$field} = false;
                }
            }
            foreach ($nullableFields as $field) {
                if (array_key_exists($field, $clone->getAttributes())) {
                    $clone->{$field} = null;
                }
            }
        }

        $clone->save();
        return $clone;
    }

    public function qcApprover()
    {
        return $this->belongsTo(User::class, 'qc_approved_by');
    }

    public function spvApprover()
    {
        return $this->belongsTo(User::class, 'spv_approved_by');
    }

    public function produksiApprover()
    {
        return $this->belongsTo(User::class, 'produksi_approved_by');
    }
}