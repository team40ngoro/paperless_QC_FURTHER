<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasAudit;

class PembuatanEmulsi extends Model
{
    use HasAudit;

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $table = 'pembuatan_emulsi';
    protected $fillable = [
        'uuid', 'kode_produksi_emulsi', 'nomor_emulsi_id','nama_emulsi_id', 'hasil_emulsi', 'kondisi', 'kode_form',
        'total_pemakaian_id', 'id_produk', 'berat_sampling', 'id_plan', 'user_id', 'shift_id' , 'tanggal', 'jam',
        'approved_by_qc', 'approved_by_produksi', 'approved_by_spv',
        'qc_approved_by', 'produksi_approved_by', 'spv_approved_by',
        'qc_approved_at', 'produksi_approved_at', 'spv_approved_at',
        'is_audit', 'source_uuid'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) $model->uuid = (string) Str::uuid();
        });
    }

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
    ];

     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }

    public function nomor_emulsi() {
        return $this->belongsTo(\App\Models\NomorEmulsi::class, 'nomor_emulsi_id');
    }
    public function nama_emulsi() {
        return $this->belongsTo(\App\Models\JenisEmulsi::class, 'nama_emulsi_id');
    }
    public function total_pemakaian() {
        return $this->belongsTo(\App\Models\TotalPemakaianEmulsi::class, 'total_pemakaian_id');
    }
    public function produk() {
        return $this->belongsTo(\App\Models\JenisProduk::class, 'id_produk');
    }
    public function plan() {
        return $this->belongsTo(\App\Models\Plan::class, 'id_plan');
    }
    public function user() {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    public function bahan_emulsi()
    {
        return $this->hasMany(\App\Models\BahanEmulsi::class, 'nomor_emulsi_id', 'nomor_emulsi_id');
    }
    public function suhuEmulsi()
    {
        return $this->hasMany(SuhuEmulsi::class, 'pembuatan_emulsi_id');
    }
    public function shift()
    {
        return $this->belongsTo(\App\Models\DataShift::class, 'shift_id');
    }

    public function qcApprover()
    {
        return $this->belongsTo(\App\Models\User::class, 'qc_approved_by');
    }

    public function produksiApprover()
    {
        return $this->belongsTo(\App\Models\User::class, 'produksi_approved_by');
    }

    public function spvApprover()
    {
        return $this->belongsTo(\App\Models\User::class, 'spv_approved_by');
    }

    /**
     * Override copyToAudit to also copy child records
     */
    public function copyToAudit(): self
    {
        $clone = $this->replicate();
        $clone->uuid = (string) Str::uuid();
        $clone->is_audit = true;
        $clone->source_uuid = $this->uuid;

        if (!$this->keepApprovalsOnAudit()) {
            $booleanApprovalFields = ['approved_by_qc', 'approved_by_produksi', 'approved_by_spv'];
            $nullableApprovalFields = ['qc_approved_by', 'qc_approved_at', 'produksi_approved_by', 'produksi_approved_at', 'spv_approved_by', 'spv_approved_at'];
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

        // Copy suhuEmulsi records
        foreach ($this->suhuEmulsi as $suhuEmulsi) {
            $suhuEmulsiClone = $suhuEmulsi->replicate();
            $suhuEmulsiClone->pembuatan_emulsi_id = $clone->id;
            $suhuEmulsiClone->save();
        }

        return $clone;
    }
}