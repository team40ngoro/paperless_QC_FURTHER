<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DataShift extends Model
{
    use HasFactory;
    protected $table = 'data_shift';
    protected $fillable = ['uuid', 'id_plan', 'user_id', 'shift'];
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }
    public function plan() { return $this->belongsTo(Plan::class, 'id_plan'); }
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function persiapanBahanForming()
    {
        return $this->hasMany(\App\Models\PersiapanBahanForming::class, 'shift_id');
    }
}
