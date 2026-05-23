<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['pesanan_id', 'sender_id', 'sender_role', 'message', 'is_read'];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
