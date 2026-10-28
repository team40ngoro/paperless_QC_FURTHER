<?php
// app/Models/AktualBetter.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AktualBetter extends Model
{
    protected $table = 'aktual_better';
    protected $fillable = [
        'uuid', 'id_std_salinitas_viskositas', 'id_persiapan_bahan_better', 'aktual_vis', 'aktual_sal', 'aktual_suhu_air'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) $model->uuid = (string) Str::uuid();
        });
    }
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }

    public function std() { return $this->belongsTo(StdSalinitasViskositas::class, 'id_std_salinitas_viskositas'); }
    public function persiapan() { return $this->belongsTo(PersiapanBahanBetter::class, 'id_persiapan_bahan_better'); }
}
