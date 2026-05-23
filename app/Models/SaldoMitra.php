<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoMitra extends Model
{
    protected $fillable = [
        'mitra_id', 'pesanan_id', 'jenis', 'jumlah', 'keterangan', 'status',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
}
