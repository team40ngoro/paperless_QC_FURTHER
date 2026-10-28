<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasAudit;

class PembekuanIqfPenggorengan extends Model
{
    use HasFactory, HasAudit;

    protected $table = 'pembekuan_iqf_penggorengan';
    
    protected $fillable = [
        'uuid', 'id_plan', 'user_id', 'tanggal', 'suhu_ruang_iqf', 'holding_time',
        'hasil_penggorengan_uuid', 'frayer_uuid', 'frayer2_uuid', 'frayer3_uuid', 'frayer4_uuid', 'frayer5_uuid',
        'breader_uuid', 'battering_uuid', 'predust_uuid', 'penggorengan_uuid', 'jam', 'kode_form',
        'is_audit', 'source_uuid',
        'approved_by_qc', 'approved_by_produksi', 'approved_by_spv',
        'qc_approved_by', 'produksi_approved_by', 'spv_approved_by',
        'qc_approved_at', 'produksi_approved_at', 'spv_approved_at'
    ];

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'is_audit' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        date_default_timezone_set('Asia/Jakarta');
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

    public function copyToAudit(): self
    {
        $uuidMap = [];

        // 1. Clone Penggorengan
        if ($this->penggorengan_uuid && ($child = $this->penggorenganData)) {
            $c = $child->replicate();
            $c->uuid = (string) Str::uuid();
            $c->save();
            $uuidMap['penggorengan_uuid'] = $c->uuid;
        } else {
            $uuidMap['penggorengan_uuid'] = $this->penggorengan_uuid;
        }

        // 2. Clone PembuatanPredust
        if ($this->predust_uuid && ($child = $this->predustData)) {
            $c = $child->replicate();
            $c->uuid = (string) Str::uuid();
            $c->penggorengan_uuid = $uuidMap['penggorengan_uuid'];
            $c->save();
            $uuidMap['predust_uuid'] = $c->uuid;
        } else {
            $uuidMap['predust_uuid'] = $this->predust_uuid;
        }

        // 3. Clone ProsesBattering (no auto-uuid in boot)
        if ($this->battering_uuid && ($child = $this->batteringData)) {
            $c = $child->replicate();
            $c->uuid = (string) Str::uuid();
            $c->penggorengan_uuid = $uuidMap['penggorengan_uuid'];
            $c->predust_uuid = $uuidMap['predust_uuid'];
            $c->save();
            $uuidMap['battering_uuid'] = $c->uuid;
        } else {
            $uuidMap['battering_uuid'] = $this->battering_uuid;
        }

        // 4. Clone ProsesBreader (no auto-uuid in boot)
        if ($this->breader_uuid && ($child = $this->breaderData)) {
            $c = $child->replicate();
            $c->uuid = (string) Str::uuid();
            $c->penggorengan_uuid = $uuidMap['penggorengan_uuid'];
            $c->predust_uuid = $uuidMap['predust_uuid'];
            $c->battering_uuid = $uuidMap['battering_uuid'];
            $c->save();
            $uuidMap['breader_uuid'] = $c->uuid;
        } else {
            $uuidMap['breader_uuid'] = $this->breader_uuid;
        }

        // 5. Clone ProsesFrayer (Frayer1)
        if ($this->frayer_uuid && ($child = $this->frayerData)) {
            $c = $child->replicate();
            $c->uuid = (string) Str::uuid();
            $c->penggorengan_uuid = $uuidMap['penggorengan_uuid'];
            $c->predust_uuid = $uuidMap['predust_uuid'];
            $c->battering_uuid = $uuidMap['battering_uuid'];
            $c->breader_uuid = $uuidMap['breader_uuid'];
            $c->save();
            $uuidMap['frayer_uuid'] = $c->uuid;
        } else {
            $uuidMap['frayer_uuid'] = $this->frayer_uuid;
        }

        // 6. Clone Frayer2
        if ($this->frayer2_uuid && ($child = $this->frayer2Data)) {
            $c = $child->replicate();
            $c->uuid = (string) Str::uuid();
            $c->frayer_uuid = $uuidMap['frayer_uuid'];
            $c->penggorengan_uuid = $uuidMap['penggorengan_uuid'];
            $c->predust_uuid = $uuidMap['predust_uuid'];
            $c->battering_uuid = $uuidMap['battering_uuid'];
            $c->breader_uuid = $uuidMap['breader_uuid'];
            $c->save();
            $uuidMap['frayer2_uuid'] = $c->uuid;
        } else {
            $uuidMap['frayer2_uuid'] = $this->frayer2_uuid;
        }

        // 7. Clone Frayer3
        if ($this->frayer3_uuid && ($child = $this->frayer3Data)) {
            $c = $child->replicate();
            $c->uuid = (string) Str::uuid();
            $c->penggorengan_uuid = $uuidMap['penggorengan_uuid'];
            $c->predust_uuid = $uuidMap['predust_uuid'];
            $c->battering_uuid = $uuidMap['battering_uuid'];
            $c->breader_uuid = $uuidMap['breader_uuid'];
            $c->save();
            $uuidMap['frayer3_uuid'] = $c->uuid;
        } else {
            $uuidMap['frayer3_uuid'] = $this->frayer3_uuid;
        }

        // 8. Clone Frayer4
        if ($this->frayer4_uuid && ($child = $this->frayer4Data)) {
            $c = $child->replicate();
            $c->uuid = (string) Str::uuid();
            $c->penggorengan_uuid = $uuidMap['penggorengan_uuid'];
            $c->predust_uuid = $uuidMap['predust_uuid'];
            $c->battering_uuid = $uuidMap['battering_uuid'];
            $c->breader_uuid = $uuidMap['breader_uuid'];
            $c->save();
            $uuidMap['frayer4_uuid'] = $c->uuid;
        } else {
            $uuidMap['frayer4_uuid'] = $this->frayer4_uuid;
        }

        // 9. Clone Frayer5
        if ($this->frayer5_uuid && ($child = $this->frayer5Data)) {
            $c = $child->replicate();
            $c->uuid = (string) Str::uuid();
            $c->penggorengan_uuid = $uuidMap['penggorengan_uuid'];
            $c->predust_uuid = $uuidMap['predust_uuid'];
            $c->battering_uuid = $uuidMap['battering_uuid'];
            $c->breader_uuid = $uuidMap['breader_uuid'];
            $c->save();
            $uuidMap['frayer5_uuid'] = $c->uuid;
        } else {
            $uuidMap['frayer5_uuid'] = $this->frayer5_uuid;
        }

        // 10. Clone HasilPenggorengan
        if ($this->hasil_penggorengan_uuid && ($child = $this->hasilPenggorenganData)) {
            $c = $child->replicate();
            $c->uuid = (string) Str::uuid();
            $c->penggorengan_uuid = $uuidMap['penggorengan_uuid'];
            $c->predust_uuid = $uuidMap['predust_uuid'];
            $c->battering_uuid = $uuidMap['battering_uuid'];
            $c->breader_uuid = $uuidMap['breader_uuid'];
            $c->frayer_uuid = $uuidMap['frayer_uuid'];
            $c->frayer2_uuid = $uuidMap['frayer2_uuid'];
            $c->frayer3_uuid = $uuidMap['frayer3_uuid'];
            $c->frayer4_uuid = $uuidMap['frayer4_uuid'];
            $c->frayer5_uuid = $uuidMap['frayer5_uuid'];
            $c->save();
            $uuidMap['hasil_penggorengan_uuid'] = $c->uuid;
        } else {
            $uuidMap['hasil_penggorengan_uuid'] = $this->hasil_penggorengan_uuid;
        }

        // 11. Clone PembekuanIqfPenggorengan itself
        $clone = $this->replicate();
        $clone->uuid = (string) Str::uuid();
        $clone->is_audit = true;
        $clone->source_uuid = $this->uuid;

        $clone->penggorengan_uuid = $uuidMap['penggorengan_uuid'];
        $clone->predust_uuid = $uuidMap['predust_uuid'];
        $clone->battering_uuid = $uuidMap['battering_uuid'];
        $clone->breader_uuid = $uuidMap['breader_uuid'];
        $clone->frayer_uuid = $uuidMap['frayer_uuid'];
        $clone->frayer2_uuid = $uuidMap['frayer2_uuid'];
        $clone->frayer3_uuid = $uuidMap['frayer3_uuid'];
        $clone->frayer4_uuid = $uuidMap['frayer4_uuid'];
        $clone->frayer5_uuid = $uuidMap['frayer5_uuid'];
        $clone->hasil_penggorengan_uuid = $uuidMap['hasil_penggorengan_uuid'];

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
        return $clone;
    }

    // Relationships
    public function plan() { return $this->belongsTo(Plan::class, 'id_plan', 'id'); }
    public function shift() { return $this->belongsTo(DataShift::class, 'shift_id'); }
    public function getShiftDataAttribute()
    {
        if ($this->shift) return $this->shift;
        if ($this->penggorengan_uuid) {
            $penggorengan = \App\Models\Penggorengan::where('uuid', $this->penggorengan_uuid)->first();
            if ($penggorengan && $penggorengan->shift) return $penggorengan->shift;
        }
        if ($this->hasil_penggorengan_uuid) {
            $hasil = \App\Models\HasilPenggorengan::where('uuid', $this->hasil_penggorengan_uuid)->first();
            if ($hasil && $hasil->shift) return $hasil->shift;
        }
        return null;
    }
    public function user() { return $this->belongsTo(User::class, 'user_id', 'id'); }
    public function hasilPenggorenganData() { return $this->belongsTo(HasilPenggorengan::class, 'hasil_penggorengan_uuid', 'uuid'); }
    public function frayerData() { return $this->belongsTo(ProsesFrayer::class, 'frayer_uuid', 'uuid'); }
    public function frayer2Data() { return $this->belongsTo(Frayer2::class, 'frayer2_uuid', 'uuid'); }
    public function frayer3Data() { return $this->belongsTo(Frayer3::class, 'frayer3_uuid', 'uuid'); }
    public function frayer4Data() { return $this->belongsTo(Frayer4::class, 'frayer4_uuid', 'uuid'); }
    public function frayer5Data() { return $this->belongsTo(Frayer5::class, 'frayer5_uuid', 'uuid'); }
    public function breaderData() { return $this->belongsTo(ProsesBreader::class, 'breader_uuid', 'uuid'); }
    public function batteringData() { return $this->belongsTo(ProsesBattering::class, 'battering_uuid', 'uuid'); }
    public function predustData() { return $this->belongsTo(PembuatanPredust::class, 'predust_uuid', 'uuid'); }
    public function penggorenganData() { return $this->belongsTo(Penggorengan::class, 'penggorengan_uuid', 'uuid'); }
    public function qcApprover() { return $this->belongsTo(User::class, 'qc_approved_by', 'id'); }
    public function produksiApprover() { return $this->belongsTo(User::class, 'produksi_approved_by', 'id'); }
    public function spvApprover() { return $this->belongsTo(User::class, 'spv_approved_by', 'id'); }
}
