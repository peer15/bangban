<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranMitra extends Model
{
    use HasFactory;

    protected $fillable = [
        'mitra_id', 'jenis', 'jumlah', 'invoice_number',
        'metode_pembayaran', 'status', 'periode_mulai', 'periode_selesai',
    ];

    protected function casts(): array
    {
        return [
            'periode_mulai' => 'date',
            'periode_selesai' => 'date',
        ];
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }
}
