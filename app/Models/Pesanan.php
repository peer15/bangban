<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'mitra_id', 'layanan', 'latitude', 'longitude',
        'catatan_lokasi', 'biaya_layanan', 'biaya_panggil', 'total_biaya',
        'jarak_km', 'status', 'pembayaran', 'sudah_bayar',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['selesai', 'dibatalkan']);
    }

    public function scopeSelesai(Builder $query): Builder
    {
        return $query->where('status', 'selesai');
    }

    public function scopeMencariMitra(Builder $query): Builder
    {
        return $query->where('status', 'mencari_mitra')->whereNull('mitra_id');
    }

    // ─── Accessors ───────────────────────────────────────────

    public function getNamaLayananAttribute(): string
    {
        return match ($this->layanan) {
            'tambal-ban' => 'Tambal Ban Motor',
            'isi-angin' => 'Isi Angin / Nitrogen',
            'ganti-ban' => 'Ganti Ban Motor',
            default => $this->layanan,
        };
    }
}
