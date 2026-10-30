<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasAudit;
use App\Services\Excel\Contracts\ImportableModel;

class KetidaksesuaianPlastik extends Model implements ImportableModel
{
    use HasFactory, HasAudit;

    protected $table = 'ketidaksesuaian_plastik';

    protected $fillable = [
        'uuid',
        'is_audit',
        'source_uuid',
        'user_id',
        'id_plan',
        'id_shift',
        'tanggal',
        'jam',
        'nama_plastik',
        'jumlah_ketidaksesuaian',
        'alasan_hold',
        'hold_data',
        'dokumentasi_tagging',
        'dokumentasi_penyimpangan_plastik',
        'kode_form',
        // Approval fields
        'approved_by_qc',
        'approved_by_produksi',
        'approved_by_spv',
        'qc_approved_by',
        'produksi_approved_by',
        'spv_approved_by',
        'qc_approved_at',
        'produksi_approved_at',
        'spv_approved_at'
    ];

    protected $casts = [
        'tanggal' => 'datetime',
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
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
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

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'id_shift');
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

    public function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    // ===== ImportableModel Interface Implementation =====

    public static function getImportMapping(): array
    {
        return [
            'TANGGAL'      => 'tanggal',
            'JAM'          => 'jam',
            'SHIFT'        => 'id_shift',
            'NAMA PLASTIK' => 'nama_plastik',
            'JUMLAH (KG)'  => 'jumlah_ketidaksesuaian',
            'ALASAN HOLD'  => 'alasan_hold',
            'HOLD DATA'    => 'hold_data',
        ];
    }

    public static function getImportRules(): array
    {
        return [
            'tanggal'                => 'required|date',
            'jam'                    => 'nullable|string',
            'id_shift'               => 'required|integer',
            'nama_plastik'           => 'required|string|max:255',
            'jumlah_ketidaksesuaian' => 'required|numeric|min:0',
            'alasan_hold'            => 'required|string|max:500',
            'hold_data'              => 'nullable|string|max:500',
        ];
    }

    public static function getMasterDataFields(): array
    {
        $idPlan = auth()->user()->id_plan ?? null;

        return [
            'id_shift' => [
                'label'        => 'Shift',
                'source'       => DataShift::class,
                'label_column' => 'shift',
                'value_column' => 'shift',
                'filter'       => $idPlan ? ['id_plan' => $idPlan] : [],
                'list_source'  => "='Master Data'!\$A\$2:\$A\$100",
            ],
        ];
    }

    public static function transformImportRow(array $row): array
    {
        $idPlan = auth()->user()->id_plan ?? null;

        // Parse tanggal — toleran format Y-m-d atau d-m-Y
        if (!empty($row['tanggal'])) {
            try {
                $row['tanggal'] = \Carbon\Carbon::parse($row['tanggal'])->format('Y-m-d');
            } catch (\Exception $e) {
                $row['tanggal'] = now()->format('Y-m-d');
            }
        }

        // Parse jam
        if (!empty($row['jam'])) {
            try {
                $row['jam'] = \Carbon\Carbon::parse($row['jam'])->format('H:i');
            } catch (\Exception $e) {
                $row['jam'] = now()->format('H:i');
            }
        } else {
            $row['jam'] = now()->format('H:i');
        }

        // Resolve id_shift: nama shift -> id (robust: exact, strip-space, LIKE)
        if (isset($row['id_shift'])) {
            $shiftVal = trim((string) $row['id_shift']);

            $shift = DataShift::when($idPlan, fn($q) => $q->where('id_plan', $idPlan))
                              ->where('shift', $shiftVal)->first();

            if (!$shift && str_contains($shiftVal, ' ')) {
                $first = trim(explode(' ', $shiftVal)[0]);
                $shift = DataShift::when($idPlan, fn($q) => $q->where('id_plan', $idPlan))
                                  ->where('shift', $first)->first();
            }

            if (!$shift) {
                $shift = DataShift::when($idPlan, fn($q) => $q->where('id_plan', $idPlan))
                                  ->where('shift', 'like', "%{$shiftVal}%")->first();
            }

            $row['id_shift'] = $shift ? $shift->id : null;
        }

        $row['id_plan'] = $idPlan;
        $row['user_id'] = auth()->id();

        return $row;
    }
}
