<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'mitra_id', 'layanan', 'latitude', 'longitude',
        'catatan_lokasi', 'biaya_layanan', 'biaya_panggil', 'total_biaya',
        'jarak_km', 'status', 'pembayaran', 'sudah_bayar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    public function getNamaLayananAttribute(): string
    {
        return match($this->layanan) {
            'tambal-ban' => 'Tambal Ban Motor',
            'isi-angin' => 'Isi Angin / Nitrogen',
            'ganti-ban' => 'Ganti Ban Motor',
            default => $this->layanan,
        };
    }
}
