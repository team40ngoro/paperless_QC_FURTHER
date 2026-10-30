<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasAudit;

class PembekuanIqfRoasting extends Model
{
    use HasFactory, HasAudit;

    protected $table = 'pembekuan_iqf_roasting';

    protected $fillable = [
        'uuid',
        'id_plan',
        'shift_id',
        'user_id',
        'tanggal',
        'jam',
        'suhu_ruang_iqf',
        'holding_time',
        'kode_form',
        'is_audit',
        'source_uuid',
        // Approval fields
        'approved_by_qc',
        'approved_by_produksi',
        'approved_by_spv',
        'qc_approved_by',
        'produksi_approved_by',
        'spv_approved_by',
        'qc_approved_at',
        'produksi_approved_at',
        'spv_approved_at',
        // UUID fields untuk relasi ke proses sebelumnya
        'input_roasting_uuid',
        'hasil_proses_roasting_uuid',
        'proses_roasting_fan_uuid',
        'frayer_uuid',
        'frayer2_uuid',
        'frayer3_uuid',
        'frayer4_uuid',
        'frayer5_uuid',
        'breader_uuid',
        'battering_uuid',
        'predust_uuid',
        'penggorengan_uuid'
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
        'frayer_uuid' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        date_default_timezone_set('Asia/Jakarta');
    }

    public function getRouteKeyName()
    {
        return 'uuid';
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

        // 3. Clone ProsesBattering
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

        // 4. Clone ProsesBreader
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

        // 5. Clone Frayer records (frayer_uuid is an array)
        $newFrayerUuids = [];
        $oldFrayerUuids = is_array($this->frayer_uuid) ? $this->frayer_uuid : [$this->frayer_uuid];
        foreach ($oldFrayerUuids as $fuuid) {
            if (!$fuuid) continue;
            $cloned = false;
            foreach ([
                'ProsesFrayer' => ['proses_frayer', 'penggorengan_uuid,predust_uuid,battering_uuid,breader_uuid'],
                'Frayer2' => ['frayer_2', 'penggorengan_uuid,predust_uuid,battering_uuid,breader_uuid'],
                'Frayer3' => ['frayer_3', 'penggorengan_uuid,predust_uuid,battering_uuid,breader_uuid'],
                'Frayer4' => ['frayer_4', 'penggorengan_uuid,predust_uuid,battering_uuid,breader_uuid'],
                'Frayer5' => ['frayer_5', 'penggorengan_uuid,predust_uuid,battering_uuid,breader_uuid'],
            ] as $fClass => [$fTable, $fFields]) {
                $fqcn = "App\\Models\\$fClass";
                $fChild = $fqcn::where('uuid', $fuuid)->first();
                if ($fChild) {
                    $fc = $fChild->replicate();
                    $fc->uuid = (string) Str::uuid();
                    $fc->penggorengan_uuid = $uuidMap['penggorengan_uuid'];
                    $fc->predust_uuid = $uuidMap['predust_uuid'];
                    $fc->battering_uuid = $uuidMap['battering_uuid'];
                    $fc->breader_uuid = $uuidMap['breader_uuid'];
                    $fc->save();
                    $newFrayerUuids[] = $fc->uuid;
                    if ($fClass === 'Frayer2') $uuidMap['frayer2_uuid'] = $fc->uuid;
                    if ($fClass === 'Frayer3') $uuidMap['frayer3_uuid'] = $fc->uuid;
                    if ($fClass === 'Frayer4') $uuidMap['frayer4_uuid'] = $fc->uuid;
                    if ($fClass === 'Frayer5') $uuidMap['frayer5_uuid'] = $fc->uuid;
                    $cloned = true;
                    break;
                }
            }
            if (!$cloned) $newFrayerUuids[] = $fuuid;
        }

        // 6. Clone ProsesRoastingFan
        if ($this->proses_roasting_fan_uuid && ($child = $this->prosesRoastingFanData)) {
            $c = $child->replicate();
            $c->uuid = (string) Str::uuid();
            $c->penggorengan_uuid = $uuidMap['penggorengan_uuid'];
            $c->save();
            $uuidMap['proses_roasting_fan_uuid'] = $c->uuid;
        } else {
            $uuidMap['proses_roasting_fan_uuid'] = $this->proses_roasting_fan_uuid;
        }

        // 7. Clone HasilProsesRoasting
        if ($this->hasil_proses_roasting_uuid && ($child = $this->hasilProsesRoastingData)) {
            $c = $child->replicate();
            $c->uuid = (string) Str::uuid();
            $c->save();
            $uuidMap['hasil_proses_roasting_uuid'] = $c->uuid;
        } else {
            $uuidMap['hasil_proses_roasting_uuid'] = $this->hasil_proses_roasting_uuid;
        }

        // 8. Clone PembekuanIqfRoasting itself
        $clone = $this->replicate();
        $clone->uuid = (string) Str::uuid();
        $clone->is_audit = true;
        $clone->source_uuid = $this->uuid;

        $clone->penggorengan_uuid = $uuidMap['penggorengan_uuid'];
        $clone->predust_uuid = $uuidMap['predust_uuid'];
        $clone->battering_uuid = $uuidMap['battering_uuid'];
        $clone->breader_uuid = $uuidMap['breader_uuid'];
        $clone->frayer_uuid = $newFrayerUuids;
        if (isset($uuidMap['frayer2_uuid'])) $clone->frayer2_uuid = $uuidMap['frayer2_uuid'];
        if (isset($uuidMap['frayer3_uuid'])) $clone->frayer3_uuid = $uuidMap['frayer3_uuid'];
        if (isset($uuidMap['frayer4_uuid'])) $clone->frayer4_uuid = $uuidMap['frayer4_uuid'];
        if (isset($uuidMap['frayer5_uuid'])) $clone->frayer5_uuid = $uuidMap['frayer5_uuid'];
        $clone->proses_roasting_fan_uuid = $uuidMap['proses_roasting_fan_uuid'];
        $clone->hasil_proses_roasting_uuid = $uuidMap['hasil_proses_roasting_uuid'];
        // input_roasting_uuid stays the same (InputRoasting is shared)

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

    // Relasi ke proses sebelumnya
    public function inputRoasting()
    {
        return $this->belongsTo(InputRoasting::class, 'input_roasting_uuid', 'uuid');
    }

    public function bahanBakuRoasting()
    {
        return $this->belongsTo(BahanBakuRoasting::class, 'bahan_baku_roasting_uuid', 'uuid');
    }

    public function hasilProsesRoasting()
    {
        return $this->belongsTo(HasilProsesRoasting::class, 'hasil_proses_roasting_uuid', 'uuid');
    }

    public function prosesRoastingFan()
    {
        return $this->belongsTo(ProsesRoastingFan::class, 'proses_roasting_fan_uuid', 'uuid');
    }

    public function frayer()
    {
        // Tetap gunakan logic array jika kolom frayer_uuid berisi array
        return $this->belongsTo(ProsesFrayer::class, 'frayer_uuid', 'uuid');
    }

    public function frayer2()
    {
        return $this->belongsTo(Frayer2::class, 'frayer2_uuid', 'uuid');
    }

    public function frayer3()
    {
        return $this->belongsTo(Frayer3::class, 'frayer3_uuid', 'uuid');
    }

    public function frayer4()
    {
        return $this->belongsTo(Frayer4::class, 'frayer4_uuid', 'uuid');
    }

    public function frayer5()
    {
        return $this->belongsTo(Frayer5::class, 'frayer5_uuid', 'uuid');
    }

    public function breader()
    {
        return $this->belongsTo(ProsesBreader::class, 'breader_uuid', 'uuid');
    }

    public function battering()
    {
        return $this->belongsTo(ProsesBattering::class, 'battering_uuid', 'uuid');
    }

    public function predust()
    {
        return $this->belongsTo(PembuatanPredust::class, 'predust_uuid', 'uuid');
    }

    public function penggorengan()
    {
        return $this->belongsTo(Penggorengan::class, 'penggorengan_uuid', 'uuid');
    }

    // Alias relasi untuk compatibility dengan controller
    public function penggorenganData()
    {
        return $this->penggorengan();
    }

    public function inputRoastingData()
    {
        return $this->inputRoasting();
    }

    public function bahanBakuRoastingData()
    {
        return $this->bahanBakuRoasting();
    }

    public function hasilProsesRoastingData()
    {
        return $this->hasilProsesRoasting();
    }

    public function prosesRoastingFanData()
    {
        return $this->prosesRoastingFan();
    }

    public function getFrayerListDataAttribute()
    {
        $uuids = $this->frayer_uuid;
        if (empty($uuids)) return collect([]);
        if (!is_array($uuids)) $uuids = [$uuids];

        // Ambil data dari berbagai tabel frayer dengan relasi standarnya
        $frayer1 = \App\Models\ProsesFrayer::with(['suhuFrayer', 'waktuPenggorengan'])->whereIn('uuid', $uuids)->get();
        $frayer2 = \App\Models\Frayer2::with(['suhuFrayer', 'waktuPenggorengan'])->whereIn('uuid', $uuids)->get();
        $frayer3 = \App\Models\Frayer3::with(['suhuFrayer', 'waktuPenggorengan'])->whereIn('uuid', $uuids)->get();
        $frayer4 = \App\Models\Frayer4::with(['suhuFrayer', 'waktuPenggorengan'])->whereIn('uuid', $uuids)->get();
        $frayer5 = \App\Models\Frayer5::with(['suhuFrayer', 'waktuPenggorengan'])->whereIn('uuid', $uuids)->get();

        // Gabungkan dan urutkan berdasarkan jam atau ID untuk menjaga urutan logis
        return $frayer1->concat($frayer2)->concat($frayer3)->concat($frayer4)->concat($frayer5)->sortBy('jam');
    }

    public function breaderData()
    {
        return $this->breader();
    }

    public function batteringData()
    {
        return $this->battering();
    }

    public function predustData()
    {
        return $this->predust();
    }

    // Tambahkan accessor untuk mendapatkan shift dari berbagai sumber (Dual Flow)
    public function getShiftDataAttribute()
    {
        // 1. Cek relationship shift lokal (PembekuanIqfRoasting -> shift_id)
        if ($this->shift) {
            return $this->shift;
        }

        // 2. Cek via Input Roasting (Alur Wizard 3)
        if ($this->input_roasting_uuid) {
            $input = \App\Models\InputRoasting::where('uuid', $this->input_roasting_uuid)->first();
            if ($input && $input->shift) {
                return $input->shift;
            }
        }

        // 3. Cek via Penggorengan (Alur Wizard 2)
        if ($this->penggorengan_uuid) {
            $penggorengan = \App\Models\Penggorengan::where('uuid', $this->penggorengan_uuid)->first();
            if ($penggorengan && $penggorengan->shift) {
                return $penggorengan->shift;
            }
        }
        
        // 4. Cek via HasilProsesRoasting
        if ($this->hasil_proses_roasting_uuid) {
            $hasilRoasting = \App\Models\HasilProsesRoasting::where('uuid', $this->hasil_proses_roasting_uuid)->first();
            if ($hasilRoasting && $hasilRoasting->shift) {
                return $hasilRoasting->shift;
            }
        }
        
        return null;
    }

    // Approval relationships
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
