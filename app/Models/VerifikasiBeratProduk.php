<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasAudit;

class VerifikasiBeratProduk extends Model
{
    use HasFactory, HasAudit;

    protected $table = 'verifikasi_berat_produk';

    protected $fillable = [
        'uuid',
        'kode_form',
        'is_audit',
        'source_uuid',
        'id_plan',
        'user_id',
        'shift_id',
        'id_produk',
        'tanggal',
        'jam',
        'kode_produksi',
        'gramase',
        'catatan',
        'jenis_produk_kfc',
        'berat_breader',
        'rata_rata_breader',
        'pickup_breader',
        'pickup_total_breader',
        'after_forming',
        'rata_rata_after_forming',
        'berat_dry_kfc',
        'rata_rata_dry_kfc',
        'berat_wet_kfc',
        'rata_rata_wet_kfc',
        'pickup_after_forming_kfc',
        'berat_predusting',
        'rata_rata_predusting',
        'pickup_after_forming_predusting',
        'berat_battering',
        'rata_rata_battering',
        'pickup_after_predusting_battering',
        'berat_breadering',
        'rata_rata_breadering',
        'pickup_after_battering_breadering',
        'berat_fryer_1',
        'rata_rata_fryer_1',
        'pickup_breadering_fryer_1',
        'berat_fryer_2',
        'rata_rata_fryer_2',
        'pickup_fryer_1_fryer_2',
        'pickup_total',
        'pickup_total_fryer_2',
        'berat_roasting',
        'rata_rata_roasting',
        'pickup_after_breadering_roasting',
        'pickup_total_roasting',
        'hasil_adonan',
        'suhu_adonan',
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
        'tanggal' => 'datetime',
        'is_audit' => 'boolean',
        'berat_breader' => 'array',
        'after_forming' => 'array',
        'berat_dry_kfc' => 'array',
        'berat_wet_kfc' => 'array',
        'berat_predusting' => 'array',
        'berat_battering' => 'array',
        'berat_breadering' => 'array',
        'berat_fryer_1' => 'array',
        'berat_fryer_2' => 'array',
        'berat_roasting' => 'array',
        'approved_by_qc' => 'boolean',
    'approved_by_spv' => 'boolean',
    'approved_by_produksi' => 'boolean',
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

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    // Helper method for berat breader array
    public function getBeratBreaderArrayAttribute()
    {
        return $this->berat_breader ?? [];
    }

    // Calculate average from breader values
    public function calculateBreaderAverage($breaderArray)
    {
        if (empty($breaderArray)) {
            return null;
        }

        // Filter out empty values and convert to numbers
        $numericValues = array_filter($breaderArray, function($value) {
            return is_numeric($value) && $value !== '' && $value !== null;
        });

        if (empty($numericValues)) {
            return null;
        }

        // Calculate average
        $sum = array_sum($numericValues);
        $count = count($numericValues);
        
        return round($sum / $count, 2);
    }

    // Helper method for after forming array
    public function getAfterFormingArrayAttribute()
    {
        return $this->after_forming ?? [];
    }

    // Helper method for berat dry KFC array
    public function getBeratDryKfcArrayAttribute()
    {
        return $this->berat_dry_kfc ?? [];
    }

    // Helper method for berat wet KFC array
    public function getBeratWetKfcArrayAttribute()
    {
        return $this->berat_wet_kfc ?? [];
    }

    // Calculate average from dry KFC values
    public function calculateDryKfcAverage($dryKfcArray)
    {
        if (empty($dryKfcArray)) {
            return null;
        }

        // Filter out empty values and convert to numbers
        $numericValues = array_filter($dryKfcArray, function($value) {
            return is_numeric($value) && $value !== '' && $value !== null;
        });

        if (empty($numericValues)) {
            return null;
        }

        // Calculate average
        $sum = array_sum($numericValues);
        $count = count($numericValues);
        
        return round($sum / $count, 2);
    }

    // Calculate average from wet KFC values
    public function calculateWetKfcAverage($wetKfcArray)
    {
        if (empty($wetKfcArray)) {
            return null;
        }

        // Filter out empty values and convert to numbers
        $numericValues = array_filter($wetKfcArray, function($value) {
            return is_numeric($value) && $value !== '' && $value !== null;
        });

        if (empty($numericValues)) {
            return null;
        }

        // Calculate average
        $sum = array_sum($numericValues);
        $count = count($numericValues);
        
        return round($sum / $count, 2);
    }

    // Calculate average from after forming values
    public function calculateAfterFormingAverage($afterFormingArray)
    {
        if (empty($afterFormingArray)) {
            return null;
        }

        // Filter out empty values and convert to numbers
        $numericValues = array_filter($afterFormingArray, function($value) {
            return is_numeric($value) && $value !== '' && $value !== null;
        });

        if (empty($numericValues)) {
            return null;
        }

        // Calculate average
        $sum = array_sum($numericValues);
        $count = count($numericValues);
        
        return round($sum / $count, 2);
    }

    // Helper method for berat predusting array
    public function getBeratPredustingArrayAttribute()
    {
        return $this->berat_predusting ?? [];
    }

    // Calculate average from predusting values
    public function calculatePredustingAverage($predustingArray)
    {
        if (empty($predustingArray)) {
            return null;
        }

        // Filter out empty values and convert to numbers
        $numericValues = array_filter($predustingArray, function($value) {
            return is_numeric($value) && $value !== '' && $value !== null;
        });

        if (empty($numericValues)) {
            return null;
        }

        // Calculate average
        $sum = array_sum($numericValues);
        $count = count($numericValues);
        
        return round($sum / $count, 2);
    }

    // Helper method for berat battering array
    public function getBeratBatteringArrayAttribute()
    {
        return $this->berat_battering ?? [];
    }

    // Calculate average from battering values
    public function calculateBatteringAverage($batteringArray)
    {
        if (empty($batteringArray)) {
            return null;
        }

        // Filter out empty values and convert to numbers
        $numericValues = array_filter($batteringArray, function($value) {
            return is_numeric($value) && $value !== '' && $value !== null;
        });

        if (empty($numericValues)) {
            return null;
        }

        // Calculate average
        $sum = array_sum($numericValues);
        $count = count($numericValues);
        
        return round($sum / $count, 2);
    }

    // Helper method for berat breadering array
    public function getBeratBreaderingArrayAttribute()
    {
        return $this->berat_breadering ?? [];
    }

    // Calculate average from breadering values
    public function calculateBreaderingAverage($breaderingArray)
    {
        if (empty($breaderingArray)) {
            return null;
        }

        // Filter out empty values and convert to numbers
        $numericValues = array_filter($breaderingArray, function($value) {
            return is_numeric($value) && $value !== '' && $value !== null;
        });

        if (empty($numericValues)) {
            return null;
        }

        // Calculate average
        $sum = array_sum($numericValues);
        $count = count($numericValues);
        
        return round($sum / $count, 2);
    }

    // Helper method for berat fryer 1 array
    public function getBeratFryer1ArrayAttribute()
    {
        return $this->berat_fryer_1 ?? [];
    }

    // Calculate average from fryer 1 values
    public function calculateFryer1Average($fryer1Array)
    {
        if (empty($fryer1Array)) {
            return null;
        }

        // Filter out empty values and convert to numbers
        $numericValues = array_filter($fryer1Array, function($value) {
            return is_numeric($value) && $value !== '' && $value !== null;
        });

        if (empty($numericValues)) {
            return null;
        }

        // Calculate average
        $sum = array_sum($numericValues);
        $count = count($numericValues);
        
        return round($sum / $count, 2);
    }

    // Helper method for berat fryer 2 array
    public function getBeratFryer2ArrayAttribute()
    {
        return $this->berat_fryer_2 ?? [];
    }

    // Helper method for berat roasting array
    public function getBeratRoastingArrayAttribute()
    {
        return $this->berat_roasting ?? [];
    }

    // Calculate average from fryer 2 values
    public function calculateFryer2Average($fryer2Array)
    {
        if (empty($fryer2Array)) {
            return null;
        }

        // Filter out empty values and convert to numbers
        $numericValues = array_filter($fryer2Array, function($value) {
            return is_numeric($value) && $value !== '' && $value !== null;
        });

        if (empty($numericValues)) {
            return null;
        }

        // Calculate average
        $sum = array_sum($numericValues);
        $count = count($numericValues);
        
        return round($sum / $count, 2);
    }

    // Calculate average from roasting values
    public function calculateRoastingAverage($roastingArray)
    {
        if (empty($roastingArray)) {
            return null;
        }

        // Filter out empty values and convert to numbers
        $numericValues = array_filter($roastingArray, function($value) {
            return is_numeric($value) && $value !== '' && $value !== null;
        });

        if (empty($numericValues)) {
            return null;
        }

        // Calculate average
        $sum = array_sum($numericValues);
        $count = count($numericValues);
        
        return round($sum / $count, 2);
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
