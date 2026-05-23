<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'foto_profil', 'role', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ─── Relationships ───────────────────────────────────────

    public function mitra(): HasOne
    {
        return $this->hasOne(Mitra::class);
    }

    public function pesanans(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class, 'sender_id');
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMitra(): bool
    {
        return $this->role === 'mitra';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}
