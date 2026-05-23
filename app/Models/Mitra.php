<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nama_usaha', 'alamat', 'latitude', 'longitude',
        'layanan', 'jam_buka', 'jam_tutup', 'status', 'subscription_sampai',
        'foto_usaha', 'is_open', 'is_ready', 'jenis_rekening', 'nomor_rekening',
        'nama_rekening', 'rating', 'total_layanan', 'saldo',
    ];

    protected function casts(): array
    {
        return [
            'layanan' => 'array',
            'subscription_sampai' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(PembayaranMitra::class);
    }

    public function saldoHistori()
    {
        return $this->hasMany(SaldoMitra::class);
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif' && $this->subscription_sampai >= now();
    }
}
