<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\DataShift;
use App\Models\JenisProduk;
use App\Models\Plan;
use App\Models\ProsesTwahingDetail;
use App\Models\User;
use App\Models\Traits\HasAudit;

class ProsesTwahing extends Model
{
    use HasFactory, HasAudit;

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $table = 'proses_twahing';

    protected $fillable = [
        'uuid',
        'group_uuid',
        'id_plan',
        'user_id',
        'id_shift',
        'id_produk',
        'tanggal',
        'jam',
        'waktu_thawing_awal',
        'waktu_thawing_akhir',
        'kondisi_kemasan_rm',
        'total_waktu_thawing_jam',
        'catatan',
        'kode_form',
        'approved_by_qc',
        'approved_by_produksi',
        'approved_by_spv',
        'approved_by_qc_at',
        'approved_by_produksi_at',
        'approved_by_spv_at',
        'approved_by_qc_user_id',
        'approved_by_produksi_user_id',
        'approved_by_spv_user_id',
        'is_audit',
        'source_uuid',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'jam' => 'string',
        'total_waktu_thawing_jam' => 'decimal:2',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'approved_by_qc_at' => 'datetime',
        'approved_by_produksi_at' => 'datetime',
        'approved_by_spv_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->group_uuid)) {
                $model->group_uuid = $model->uuid;
            }
        });
    }

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
        return $this->belongsTo(DataShift::class, 'id_shift');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function details()
    {
        return $this->hasMany(ProsesTwahingDetail::class, 'proses_twahing_id');
    }

    /**
     * Semua record dalam grup riwayat edit (diurutkan terbaru dulu)
     */
    public function groupRecords()
    {
        return $this->hasMany(ProsesTwahing::class, 'group_uuid', 'group_uuid')
                    ->orderByDesc('created_at');
    }

    /**
     * Record terbaru dalam grup (alias latestInGroup)
     */
    public function latestGroupRecord()
    {
        return $this->hasOne(ProsesTwahing::class, 'group_uuid', 'group_uuid')
                    ->latestOfMany('created_at');
    }

    public function copyToAudit(): self
    {
        $clone = $this->replicate();
        $clone->uuid = (string) Str::uuid();
        $clone->is_audit = true;
        $clone->source_uuid = $this->uuid;

        if (!$this->keepApprovalsOnAudit()) {
            $booleanApprovalFields = ['approved_by_qc', 'approved_by_produksi', 'approved_by_spv'];
            $nullableApprovalFields = ['approved_by_qc_user_id', 'approved_by_qc_at', 'approved_by_produksi_user_id', 'approved_by_produksi_at', 'approved_by_spv_user_id', 'approved_by_spv_at'];
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

        foreach ($this->details as $detail) {
            $detailClone = $detail->replicate();
            $detailClone->uuid = (string) Str::uuid();
            $detailClone->proses_twahing_id = $clone->id;
            $detailClone->save();
        }

        return $clone;
    }
}
