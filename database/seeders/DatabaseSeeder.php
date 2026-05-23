<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin BANGBAN',
            'email' => 'admin@bangban.id',
            'phone' => '081234567890',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // User demo
        User::create([
            'name' => 'Pengguna Demo',
            'email' => 'user@bangban.id',
            'phone' => '081234567891',
            'role' => 'user',
            'password' => Hash::make('password'),
        ]);

        // Mitra demo
        $mitraUser = User::create([
            'name' => 'Pak Slamet',
            'email' => 'mitra@bangban.id',
            'phone' => '081234567892',
            'role' => 'mitra',
            'password' => Hash::make('password'),
        ]);

        \App\Models\Mitra::create([
            'user_id' => $mitraUser->id,
            'nama_usaha' => 'Tambal Ban Pak Slamet',
            'alamat' => 'Jl. Raya Jepara No. 45',
            'latitude' => -6.5935,
            'longitude' => 110.6741,
            'layanan' => ['tambal-ban', 'isi-angin', 'ganti-ban'],
            'jam_buka' => '06:00',
            'jam_tutup' => '22:00',
            'status' => 'aktif',
            'is_open' => true,
            'is_ready' => true,
            'subscription_sampai' => now()->addMonth(),
            'rating' => 4.8,
            'total_layanan' => 230,
        ]);
    }
}
