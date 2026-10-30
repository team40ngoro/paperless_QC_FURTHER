<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Traits\HasAudit;
use App\Services\Excel\Contracts\ImportableModel;

class PemeriksaanProsesProduksi extends Model implements ImportableModel
{
    use HasFactory, HasAudit;

    protected $table = 'pemeriksaan_proses_produksi';

    protected $fillable = [
        'uuid',
        'is_audit',
        'source_uuid',
        'id_plan',
        'user_id',
        'id_area',
        'shift_id',
        'tanggal',
        'jam',
        'ketidaksesuaian',
        'uraian_permasalahan',
        'analisa_penyebab',
        'disposisi',
        'tindakan_koreksi',
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

    // Relationships
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function area()
    {
        return $this->belongsTo(InputArea::class, 'id_area');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
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

    // Scope untuk filter berdasarkan role
    public function scopeFilterByRole($query)
    {
        $user = Auth::user();
        
        if ($user->role !== 'superadmin') {
            return $query->where('id_plan', $user->id_plan);
        }
        
        return $query;
    }

    // Method untuk cek akses data
    public function canAccess($user = null)
    {
        $user = $user ?: Auth::user();
        
        if ($user->role === 'superadmin') {
            return true;
        }
        return $this->id_plan === $user->id_plan;
    }

    // Accessor untuk label ketidaksesuaian
    public function getKetidaksesuaianLabelAttribute()
    {
        $labels = [
            'bahan' => '1. Bahan',
            'produk' => '2. Produk',
            'proses' => '3. Proses'
        ];

        return $labels[$this->ketidaksesuaian] ?? $this->ketidaksesuaian;
    }

    // Accessor untuk label disposisi
    public function getDisposisiLabelAttribute()
    {
        $labels = [
            'reject_musnahkan' => '1. Di-reject/dimusnahkan',
            'rework' => '2. Di-rework',
            'rework_perlakuan' => '3. Di-rework/dengan perlakuan',
            'repack' => '4. Di-repack',
            'sortir' => '5. Di-sortir'
        ];

        return $labels[$this->disposisi] ?? $this->disposisi;
    }

    // Static method untuk mendapatkan options ketidaksesuaian
    public static function getKetidaksesuaianOptions()
    {
        return [
            'bahan' => '1. Bahan',
            'produk' => '2. Produk',
            'proses' => '3. Proses'
        ];
    }

    // Static method untuk mendapatkan options disposisi
    public static function getDisposisiOptions()
    {
        return [
            'reject_musnahkan' => '1. Di-reject/dimusnahkan',
            'rework' => '2. Di-rework',
            'rework_perlakuan' => '3. Di-rework/dengan perlakuan',
            'repack' => '4. Di-repack',
            'sortir' => '5. Di-sortir'
        ];
    }

    public function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    // ===== ImportableModel Interface Implementation =====

    public static function getImportMapping(): array
    {
        return [
            'Shift' => 'shift',
            'Area' => 'area_name',
            'Tanggal' => 'tanggal',
            'Jam' => 'jam',
            'Ketidaksesuaian' => 'ketidaksesuaian',
            'Uraian Permasalahan' => 'uraian_permasalahan',
            'Analisa Penyebab' => 'analisa_penyebab',
            'Disposisi' => 'disposisi',
            'Tindakan Koreksi' => 'tindakan_koreksi',
        ];
    }

    public static function getImportRules(): array
    {
        return [
            'shift' => 'required|string',
            'area_name' => 'required|string',
            'tanggal' => 'required|date_format:d-m-Y',
            'jam' => 'required|date_format:H:i',
            'ketidaksesuaian' => 'required|string',
            'uraian_permasalahan' => 'required|string',
            'analisa_penyebab' => 'required|string',
            'disposisi' => 'nullable|string',
            'tindakan_koreksi' => 'required|string',
        ];
    }

    public static function getMasterDataFields(): array
    {
        $idPlan = auth()->user()->id_plan ?? null;

        // Get shifts filtered by plant
        $shiftsQuery = DataShift::orderBy('shift');
        if ($idPlan) {
            $shiftsQuery->where('id_plan', $idPlan);
        }
        $shifts = $shiftsQuery->pluck('shift')->values()->toArray();

        // Get areas filtered by plant
        $areasQuery = InputArea::orderBy('area');
        if ($idPlan) {
            $areasQuery->where('id_plan', $idPlan);
        }
        $areas = $areasQuery->pluck('area')->values()->toArray();

        // Get ketidaksesuaian options
        $ketOptions = array_values(self::getKetidaksesuaianOptions());
        
        // Get disposisi options
        $dispOptions = array_values(self::getDisposisiOptions());

        return [
            'shift' => [
                'label' => 'Shift',
                'options' => $shifts,
                'list_source' => "='Master Data'!A:A",
            ],
            'area_name' => [
                'label' => 'Area',
                'options' => $areas,
                'list_source' => "='Master Data'!B:B",
            ],
            'ketidaksesuaian' => [
                'label' => 'Ketidaksesuaian',
                'options' => $ketOptions,
                'list_source' => "='Master Data'!C:C",
            ],
            'disposisi' => [
                'label' => 'Disposisi',
                'options' => $dispOptions,
                'list_source' => "='Master Data'!D:D",
            ],
        ];
    }

    public static function transformImportRow(array $row): array
    {
        // Parse tanggal
        if (isset($row['tanggal']) && !empty($row['tanggal'])) {
            try {
                $row['tanggal'] = Carbon::createFromFormat('d-m-Y', trim((string) $row['tanggal']))->format('Y-m-d');
            } catch (\Exception $e) {
                $row['tanggal'] = Carbon::now('Asia/Jakarta')->format('Y-m-d');
            }
        }

        // Parse jam
        if (isset($row['jam']) && !empty($row['jam'])) {
            try {
                $row['jam'] = Carbon::createFromFormat('H:i', trim((string) $row['jam']))->format('H:i');
            } catch (\Exception $e) {
                $row['jam'] = Carbon::now('Asia/Jakarta')->format('H:i');
            }
        } else {
            $row['jam'] = Carbon::now('Asia/Jakarta')->format('H:i');
        }

        // Lookup shift by name
        if (isset($row['shift'])) {
            $shiftValue = trim((string) $row['shift']);
            $shift = DataShift::where('shift', $shiftValue)->first();
            $row['shift_id'] = $shift ? (int) $shift->id : null;
            unset($row['shift']);
        }

        // Lookup area by name
        if (isset($row['area_name'])) {
            $areaValue = trim((string) $row['area_name']);
            $area = InputArea::where('area', $areaValue)->first();
            $row['id_area'] = $area ? (int) $area->id : null;
            unset($row['area_name']);
        }

        // Map ketidaksesuaian label to value
        if (isset($row['ketidaksesuaian'])) {
            $ketValue = trim((string) $row['ketidaksesuaian']);
            $ketOptions = self::getKetidaksesuaianOptions();
            $ketReverse = array_flip($ketOptions);
            $row['ketidaksesuaian'] = $ketReverse[$ketValue] ?? $ketValue;
        }

        // Map disposisi label to value
        if (isset($row['disposisi'])) {
            $dispValue = trim((string) $row['disposisi']);
            if (!empty($dispValue)) {
                $dispOptions = self::getDisposisiOptions();
                $dispReverse = array_flip($dispOptions);
                $row['disposisi'] = $dispReverse[$dispValue] ?? $dispValue;
            } else {
                $row['disposisi'] = null;
            }
        }

        // Add system fields
        $row['id_plan'] = auth()->user()->id_plan;
        $row['user_id'] = auth()->id();

        // Trim string fields
        if (isset($row['uraian_permasalahan'])) {
            $row['uraian_permasalahan'] = trim((string) $row['uraian_permasalahan']);
        }
        if (isset($row['analisa_penyebab'])) {
            $row['analisa_penyebab'] = trim((string) $row['analisa_penyebab']);
        }
        if (isset($row['tindakan_koreksi'])) {
            $row['tindakan_koreksi'] = trim((string) $row['tindakan_koreksi']);
        }

        return $row;
    }
}
