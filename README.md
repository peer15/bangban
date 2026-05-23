# BangBan Online

Aplikasi pemesanan jasa tukang bangunan (bang ban) secara online. Menghubungkan pengguna yang membutuhkan jasa perbaikan/pembangunan dengan mitra tukang terdekat.

## Fitur

### User
- Pesan jasa mitra tukang berdasarkan lokasi
- Tracking pesanan secara real-time
- Chat langsung dengan mitra
- Rating & review setelah pesanan selesai
- Riwayat pesanan
- Tombol SOS darurat

### Mitra
- Dashboard pesanan masuk
- Terima/tolak pesanan
- Update status pengerjaan
- Sistem saldo & pencairan dana
- Pembayaran pendaftaran & langganan via DOKU
- Chat dengan pelanggan
- Toggle status buka/tutup

### Admin
- Dashboard statistik
- Verifikasi & manajemen mitra
- Monitoring pesanan
- Manajemen pembayaran & pencairan
- Peta lokasi mitra

## Tech Stack

- **Framework:** Laravel 13
- **PHP:** ^8.3
- **Database:** MySQL
- **Payment Gateway:** DOKU
- **Frontend:** Blade + Vite
- **Authentication:** Laravel built-in auth dengan role-based middleware

## Instalasi

### Prasyarat
- PHP >= 8.3
- Composer
- Node.js & NPM
- MySQL

### Setup

```bash
# Clone repository
git clone https://github.com/peer15/bangban.git
cd bangban

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Konfigurasi database di .env
# DB_DATABASE=bangban_online
# DB_USERNAME=root
# DB_PASSWORD=

# Jalankan migrasi
php artisan migrate

# Jalankan seeder (opsional)
php artisan db:seed

# Build assets
npm run build

# Jalankan server
php artisan serve
```

Atau gunakan shortcut:

```bash
composer setup
composer dev
```

## Struktur Role

| Role | Akses |
|------|-------|
| `user` | Pesan jasa, tracking, chat, rating |
| `mitra` | Kelola pesanan, saldo, profil |
| `admin` | Full akses manajemen platform |

## Payment Gateway

Menggunakan DOKU untuk:
- Pembayaran pendaftaran mitra
- Pembayaran langganan mitra

Konfigurasi di `.env`:
```
DOKU_CLIENT_ID=your_client_id
DOKU_SECRET_KEY=your_secret_key
DOKU_BASE_URL=https://api-sandbox.doku.com
```

## Lisensi

MIT
