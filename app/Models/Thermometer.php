<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Services\Excel\Contracts\ImportableModel;
use App\Models\DataShift;
use App\Models\DataThermo;
use App\Models\InputArea;

class Thermometer extends Model implements ImportableModel
{
    use HasFactory, \App\Models\Traits\HasAudit;

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

    protected $table = 'thermometer';

    protected $fillable = [
        'uuid',
        'is_audit',
        'source_uuid',
        'kode_form',
        'id_plan',
        'user_id',
        'shift_id',
        'tanggal',
        'jam',
        'area',
        'hasil_verifikasi_0',
        'hasil_verifikasi_100',
        'jenis',
        'kode_thermometer',
        'hasil_pengecekan',
        'entries',
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
        'is_audit' => 'boolean',
        'tanggal' => 'datetime',
        'entries' => 'array',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
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

    // Accessor untuk label hasil pengecekan
    public function getHasilPengecekanLabelAttribute()
    {
        $labels = [
            'ok' => '✓ OK',
            'tidak_ok' => '✗ Tidak OK'
        ];

        return $labels[$this->hasil_pengecekan] ?? $this->hasil_pengecekan;
    }

    // Static method untuk mendapatkan options hasil pengecekan
    public static function getHasilPengecekanOptions()
    {
        return [
            'ok' => '✓ OK',
            'tidak_ok' => '✗ Tidak OK'
        ];
    }

    /**
     * Return mapping untuk Excel import columns
     */
    public static function getImportMapping(): array
    {
        return [
            'Shift' => 'shift_id',
            'Tanggal' => 'tanggal',
            'Jam' => 'jam',
            'Nama Thermometer' => 'jenis',
            'Area' => 'area',
            'Hasil Verifikasi 0' => 'hasil_verifikasi_100',
        ];
    }

    /**
     * Return validation rules untuk Excel import
     */
    public static function getImportRules(): array
    {
        return [
            'shift_id' => 'required|exists:data_shift,id',
            'tanggal' => 'required|date_format:Y-m-d',
            'jam' => 'required|date_format:H:i',
            'area' => 'nullable|string',
            'jenis' => 'nullable|string',
            'kode_thermometer' => 'nullable|string',
            'hasil_pengecekan' => 'required|in:ok,tidak_ok',
            'hasil_verifikasi_0' => 'nullable|in:ok,tidak_ok',
            'hasil_verifikasi_100' => 'nullable|in:ok,tidak_ok',
        ];
    }

    /**
     * Return master data fields untuk dropdown di template Excel
     */
    public static function getMasterDataFields(): array
    {
        return [
            'shift_id' => [
                'source' => DataShift::class,
                'label_column' => 'shift',
                'value_column' => 'shift',
                'label' => 'Shift',
                'filter' => ['id_plan' => auth()->user()->id_plan ?? 0],
            ],
            'jenis' => [
                'source' => DataThermo::class,
                'label_column' => 'nama_thermo',
                'value_column' => 'nama_thermo',
                'label' => 'Nama Thermometer',
                'filter' => ['id_plan' => auth()->user()->id_plan ?? 0],
            ],
            'area' => [
                'source' => InputArea::class,
                'label_column' => 'area',
                'value_column' => 'area',
                'label' => 'Area',
                'filter' => ['id_plan' => auth()->user()->id_plan ?? 0],
            ],
        ];
    }

    /**
     * Transform import row data sebelum insert
     */
    public static function transformImportRow(array $row): array
    {
        return [
            'shift_id' => $row['shift_id'] ?? null,
            'tanggal' => $row['tanggal'] ?? null,
            'jam' => $row['jam'] ?? null,
            'area' => $row['area'] ?? null,
            'jenis' => $row['jenis'] ?? null,
            'kode_thermometer' => $row['kode_thermometer'] ?? null,
            'hasil_pengecekan' => $row['hasil_pengecekan'] ?? null,
            'hasil_verifikasi_0' => $row['hasil_verifikasi_0'] ?? null,
            'hasil_verifikasi_100' => $row['hasil_verifikasi_100'] ?? null,
            'id_plan' => auth()->user()->id_plan,
            'user_id' => auth()->id(),
        ];
    }
}
