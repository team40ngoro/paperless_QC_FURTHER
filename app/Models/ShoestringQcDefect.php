<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoestringQcDefect extends Model
{
    use HasFactory;

    protected $table = 'shoestring_qc_defects';

    protected $fillable = [
        'receiving_inspection_id',
        'defect_type',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function inspection()
    {
        return $this->belongsTo(ReceivingInspection::class, 'receiving_inspection_id');
    }
}
