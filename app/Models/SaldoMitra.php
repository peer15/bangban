<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaldoMitra extends Model
{
    protected $fillable = [
        'mitra_id', 'pesanan_id', 'jenis', 'jumlah', 'keterangan', 'status',
    ];

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }
}
