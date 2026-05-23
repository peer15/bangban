<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
            'is_open' => 'boolean',
            'is_ready' => 'boolean',
        ];
    }

    // ─── Relationships ───────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pesanans(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(PembayaranMitra::class);
    }

    public function saldoHistori(): HasMany
    {
        return $this->hasMany(SaldoMitra::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'aktif')
            ->where('is_open', true)
            ->where('is_ready', true);
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function isAktif(): bool
    {
        return $this->status === 'aktif' && $this->subscription_sampai >= now();
    }
}
