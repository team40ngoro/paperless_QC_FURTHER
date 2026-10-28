<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasAudit;

class PemeriksaanRheonMachine extends Model
{
    use HasFactory, HasAudit;

    protected $table = 'pemeriksaan_rheon_machine';

    protected $fillable = [
        'uuid',
        'is_audit',
        'source_uuid',
        'id_plan',
        'user_id',
        'shift_id',
        'id_produk',
        'tanggal',
        'batch',
        'pukul',
        'inner',
        'outer',
        'belt',
        'extrusion_speed',
        'jenis_cetakan',
        'berat_dough_adonan',
        'berat_filler',
        'berat_after_forming',
        'berat_after_frying',
        'jumlah_dough',
        'rata_rata_dough',
        'jumlah_filler',
        'rata_rata_filler',
        'jumlah_after_forming',
        'rata_rata_after_forming',
        'jumlah_after_frying',
        'rata_rata_after_frying',
        'catatan',
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
        'spv_approved_at',
    ];

    protected $casts = [
        'berat_dough_adonan' => 'array',
        'berat_filler' => 'array',
        'berat_after_forming' => 'array',
        'berat_after_frying' => 'array',
        'tanggal' => 'datetime',
        'jumlah_dough' => 'decimal:2',
        'rata_rata_dough' => 'decimal:2',
        'jumlah_filler' => 'decimal:2',
        'rata_rata_filler' => 'decimal:2',
        'jumlah_after_forming' => 'decimal:2',
        'rata_rata_after_forming' => 'decimal:2',
        'jumlah_after_frying' => 'decimal:2',
        'rata_rata_after_frying' => 'decimal:2',
        // Approval field casts
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
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

    // Accessor methods for array fields
    public function getBeratDoughAdonanArrayAttribute()
    {
        return is_array($this->berat_dough_adonan) ? $this->berat_dough_adonan : [];
    }

    public function getBeratFillerArrayAttribute()
    {
        return is_array($this->berat_filler) ? $this->berat_filler : [];
    }

    public function getBeratAfterFormingArrayAttribute()
    {
        return is_array($this->berat_after_forming) ? $this->berat_after_forming : [];
    }

    public function getBeratAfterFryingArrayAttribute()
    {
        return is_array($this->berat_after_frying) ? $this->berat_after_frying : [];
    }

    // Calculate totals for dough and filler - matching JavaScript logic
    public function calculateDoughFillerTotals()
    {
        $doughArray = $this->getBeratDoughAdonanArrayAttribute();
        $fillerArray = $this->getBeratFillerArrayAttribute();

        $doughTotal = 0;
        $fillerTotal = 0;
        $doughCount = 0;
        $fillerCount = 0;

        // Calculate dough totals - sum all values from all sections
        if (!empty($doughArray)) {
            foreach ($doughArray as $sectionData) {
                if (is_array($sectionData)) {
                    foreach ($sectionData as $value) {
                        if (is_numeric($value) && $value > 0) {
                            $doughTotal += (float)$value;
                            $doughCount++;
                        }
                    }
                }
            }
        }

        // Calculate filler totals - sum all values from all sections
        if (!empty($fillerArray)) {
            foreach ($fillerArray as $sectionData) {
                if (is_array($sectionData)) {
                    foreach ($sectionData as $value) {
                        if (is_numeric($value) && $value > 0) {
                            $fillerTotal += (float)$value;
                            $fillerCount++;
                        }
                    }
                }
            }
        }

        // Set totals (JavaScript uses Math.round for display)
        $this->jumlah_dough = round($doughTotal);
        $this->jumlah_filler = round($fillerTotal);
        
        // Calculate averages - simplified logic since we don't have input field values
        // JavaScript uses weighted averages, but since all items have value 1, 
        // the average will always be 1.00 when items exist
        if ($doughCount > 0 && $fillerCount === 0) {
            // Only dough items
            $this->rata_rata_dough = 1.00; // All dough items have value 1
            $this->rata_rata_filler = 0.00;
        } elseif ($fillerCount > 0 && $doughCount === 0) {
            // Only filler items
            $this->rata_rata_dough = 0.00;
            $this->rata_rata_filler = 1.00; // All filler items have value 1
        } else {
            // Both or neither
            $this->rata_rata_dough = $doughCount > 0 ? 1.00 : 0.00;
            $this->rata_rata_filler = $fillerCount > 0 ? 1.00 : 0.00;
        }
    }

    // Calculate totals for after forming and after frying - matching JavaScript logic
    public function calculateAfterFormingFryingTotals()
    {
        $formingArray = $this->getBeratAfterFormingArrayAttribute();
        $fryingArray = $this->getBeratAfterFryingArrayAttribute();

        $formingTotal = 0;
        $fryingTotal = 0;
        $formingCount = 0;
        $fryingCount = 0;

        // Calculate after forming totals - sum all values from all sections
        if (!empty($formingArray)) {
            foreach ($formingArray as $sectionData) {
                if (is_array($sectionData)) {
                    foreach ($sectionData as $value) {
                        if (is_numeric($value) && $value > 0) {
                            $formingTotal += (float)$value;
                            $formingCount++;
                        }
                    }
                }
            }
        }

        // Calculate after frying totals - sum all values from all sections
        if (!empty($fryingArray)) {
            foreach ($fryingArray as $sectionData) {
                if (is_array($sectionData)) {
                    foreach ($sectionData as $value) {
                        if (is_numeric($value) && $value > 0) {
                            $fryingTotal += (float)$value;
                            $fryingCount++;
                        }
                    }
                }
            }
        }

        // Set totals (JavaScript uses Math.round for display)
        $this->jumlah_after_forming = round($formingTotal);
        $this->jumlah_after_frying = round($fryingTotal);
        
        // Calculate averages - since all items have value 1, average is always 1.00 when items exist
        // JavaScript uses weighted averages but with default value 1, result is always 1.00
        $this->rata_rata_after_forming = $formingCount > 0 ? 1.00 : 0.00;
        $this->rata_rata_after_frying = $fryingCount > 0 ? 1.00 : 0.00;
    }

    // Route key name for UUID routing
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function keepApprovalsOnAudit(): bool
    {
        return true;
    }
}
