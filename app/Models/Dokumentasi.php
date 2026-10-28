<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Dokumentasi extends Model
{
    use HasFactory, \App\Models\Traits\HasAudit {
        \App\Models\Traits\HasAudit::copyToAudit as parentCopyToAudit;
    }

    protected function keepApprovalsOnAudit(): bool
    {
        return true;
    }

     protected $table = 'dokumentasi';

    protected $fillable = [
        'uuid',
        'is_audit',
        'source_uuid',
        'id_pengemasan_karton',
        'id_berat_produk_box',
        'id_berat_produk_bag',
        'id_pengemasan_plastik',
        'id_pengemasan_produk',
        'user_id',
        'id_plan',
        'id_shift',
        'foto_kode_produksi',
        'foto_kode_produksi_sekunder',
        'qr_code',
        'label_polyroll',
        'label_supplier',
        'keterangan',
        'tanggal',
         'jam', 
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
        'is_audit' => 'boolean',
        'tanggal' => 'datetime:Y-m-d H:i:s',
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
            $model->uuid = (string) Str::uuid();
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

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'id_shift', 'id');    
    }

        public function beratProdukBox()
    {
        return $this->belongsTo(BeratProdukBox::class, 'id_berat_produk_box');
    }
    public function beratProdukBag()
    {
        return $this->belongsTo(BeratProdukBag::class, 'id_berat_produk_bag');
    }
    public function pengemasanPlastik()
    {
        return $this->belongsTo(PengemasanPlastik::class, 'id_pengemasan_plastik');
    }
    public function pengemasanProduk()
    {
        return $this->belongsTo(PengemasanProduk::class, 'id_pengemasan_produk');
    }
    public function pengemasanKarton()
    {
        return $this->belongsTo(PengemasanKarton::class, 'id_pengemasan_karton');
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

    public function copyToAudit(): self
    {
        $clone = $this->parentCopyToAudit();

        // 1. Pengemasan Produk
        if ($this->pengemasanProduk) {
            $newPP = $this->pengemasanProduk->replicate(['uuid']);
            $newPP->save();
            $clone->id_pengemasan_produk = $newPP->id;
        }

        // 2. Pengemasan Plastik
        $newPPlId = null;
        if ($this->pengemasanPlastik) {
            $newPPl = $this->pengemasanPlastik->replicate(['uuid']);
            // Assign the new PP id if it exists
            if ($clone->id_pengemasan_produk) {
                $newPPl->id_pengemasan_produk = $clone->id_pengemasan_produk;
            }
            $newPPl->save();
            $newPPlId = $newPPl->id;
            $clone->id_pengemasan_plastik = $newPPlId;
        }

        // 3. Berat Produk Bag (Multiple records)
        $newFirstBagId = null;
        if ($this->id_pengemasan_plastik) {
            $bags = BeratProdukBag::where('id_pengemasan_plastik', $this->id_pengemasan_plastik)->get();
            foreach ($bags as $index => $bag) {
                $newBag = $bag->replicate(['uuid']);
                if ($clone->id_pengemasan_produk) {
                    $newBag->id_pengemasan_produk = $clone->id_pengemasan_produk;
                }
                if ($newPPlId) {
                    $newBag->id_pengemasan_plastik = $newPPlId;
                }
                $newBag->save();
                
                if ($index === 0) {
                    $newFirstBagId = $newBag->id;
                }
            }
        }
        if ($newFirstBagId) {
            $clone->id_berat_produk_bag = $newFirstBagId;
        }

        // 4. Berat Produk Box
        if ($this->beratProdukBox) {
            $newBox = $this->beratProdukBox->replicate(['uuid']);
            if ($clone->id_pengemasan_produk) {
                $newBox->id_pengemasan_produk = $clone->id_pengemasan_produk;
            }
            if ($newPPlId) {
                $newBox->id_pengemasan_plastik = $newPPlId;
            }
            if ($newFirstBagId) {
                $newBox->id_berat_produk_bag = $newFirstBagId;
            }
            $newBox->save();
            $clone->id_berat_produk_box = $newBox->id;
        }

        // 5. Pengemasan Karton
        if ($this->pengemasanKarton) {
            $newKarton = $this->pengemasanKarton->replicate(['uuid']);
            if ($clone->id_pengemasan_produk) {
                $newKarton->id_pengemasan_produk = $clone->id_pengemasan_produk;
            }
            if ($newPPlId) {
                $newKarton->id_pengemasan_plastik = $newPPlId;
            }
            if ($newFirstBagId) {
                $newKarton->id_berat_produk_bag = $newFirstBagId;
            }
            $newKarton->save();
            $clone->id_pengemasan_karton = $newKarton->id;
        }
        
        $clone->save();
        return $clone;
    }
}
