<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarisQcDetail extends Model
{
    use HasFactory;

    protected $table = 'inventaris_qc_detail';

    protected $fillable = [
        'id_inventaris_qc',
        'id_area',
        'id_list_peralatan',
        'nama_alat',
        'jumlah',
        'kondisi',
        'keterangan',
    ];

    public function header()
    {
        return $this->belongsTo(InventarisQc::class, 'id_inventaris_qc');
    }

    public function area()
    {
        return $this->belongsTo(InputArea::class, 'id_area');
    }

    public function peralatan()
    {
        return $this->belongsTo(ListPeralatan::class, 'id_list_peralatan');
    }
}
