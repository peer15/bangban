<?php

namespace App\Services;

use App\Models\Mitra;
use App\Models\Pesanan;
use App\Models\SaldoMitra;
use Illuminate\Support\Facades\DB;

class PesananService
{
    private const BIAYA_PER_KM = 5000;
    private const BIAYA_MINIMUM = 5000;
    private const BIAYA_DEFAULT = 10000;

    /**
     * Harga layanan berdasarkan jenis.
     */
    public function getBiayaLayanan(string $layanan): int
    {
        return match ($layanan) {
            'tambal-ban' => 35000,
            'isi-angin' => 5000,
            'ganti-ban' => 80000,
            default => 35000,
        };
    }

    /**
     * Hitung biaya panggil berdasarkan jarak.
     */
    public function getBiayaPanggil(?float $jarakKm): int
    {
        if (!$jarakKm) {
            return self::BIAYA_DEFAULT;
        }

        return max(self::BIAYA_MINIMUM, (int) ceil($jarakKm) * self::BIAYA_PER_KM);
    }

    /**
     * Buat pesanan baru.
     */
    public function buatPesanan(array $data): Pesanan
    {
        $biayaLayanan = $this->getBiayaLayanan($data['layanan']);

        $mitraTerdekat = $this->getMitrasTerdekat(
            $data['latitude'],
            $data['longitude'],
            5,
            $data['layanan']
        )->first();

        $jarakKm = $mitraTerdekat?->jarak_km;
        $biayaPanggil = $this->getBiayaPanggil($jarakKm);
        $totalBiaya = $biayaLayanan + $biayaPanggil;

        return Pesanan::create([
            'user_id' => $data['user_id'],
            'mitra_id' => null,
            'layanan' => $data['layanan'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'catatan_lokasi' => $data['catatan_lokasi'] ?? null,
            'biaya_layanan' => $biayaLayanan,
            'biaya_panggil' => $biayaPanggil,
            'total_biaya' => $totalBiaya,
            'jarak_km' => $jarakKm,
            'pembayaran' => $data['pembayaran'],
            'status' => 'mencari_mitra',
        ]);
    }

    /**
     * Selesaikan pesanan dan proses saldo.
     */
    public function selesaikanPesanan(Pesanan $pesanan, Mitra $mitra): void
    {
        $pesanan->update([
            'status' => 'selesai',
            'sudah_bayar' => true,
        ]);

        $mitra->increment('total_layanan');

        if ($pesanan->pembayaran === 'ewallet') {
            SaldoMitra::create([
                'mitra_id' => $mitra->id,
                'pesanan_id' => $pesanan->id,
                'jenis' => 'masuk',
                'jumlah' => $pesanan->total_biaya,
                'keterangan' => "Pesanan #{$pesanan->id} - {$pesanan->nama_layanan}",
            ]);
            $mitra->increment('saldo', $pesanan->total_biaya);
        }
    }

    /**
     * Cari mitra terdekat menggunakan Haversine Formula.
     */
    public function getMitrasTerdekat(float $lat, float $lng, float $radiusKm = 5, ?string $layanan = null)
    {
        $haversine = "(6371 * acos(
            cos(radians(?)) *
            cos(radians(latitude)) *
            cos(radians(longitude) - radians(?)) +
            sin(radians(?)) *
            sin(radians(latitude))
        ))";

        $query = Mitra::select('mitras.*')
            ->selectRaw("{$haversine} AS jarak_km", [$lat, $lng, $lat])
            ->where('status', 'aktif')
            ->where('is_open', true)
            ->where('is_ready', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('jarak_km', '<=', $radiusKm)
            ->orderBy('jarak_km', 'asc');

        if ($layanan) {
            $query->whereRaw("JSON_CONTAINS(layanan, ?)", [json_encode($layanan)]);
        }

        return $query->with('user')->limit(5)->get();
    }

    /**
     * Hitung jarak antara dua koordinat (km).
     */
    public function hitungJarak(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        return round(6371 * acos(
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            cos(deg2rad($lng2) - deg2rad($lng1)) +
            sin(deg2rad($lat1)) *
            sin(deg2rad($lat2))
        ), 1);
    }
}
