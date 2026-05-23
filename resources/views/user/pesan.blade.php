@extends('layouts.user')
@section('title', 'Pesan Layanan')

@section('content')
@php
    $namaLayanan = match($layanan) {
        'tambal-ban' => 'Tambal Ban Motor',
        'isi-angin' => 'Isi Angin / Nitrogen',
        'ganti-ban' => 'Ganti Ban Motor',
        default => 'Tambal Ban Motor',
    };
    $hargaLayanan = match($layanan) {
        'tambal-ban' => 35000,
        'isi-angin' => 5000,
        'ganti-ban' => 80000,
        default => 35000,
    };
@endphp

<h2 class="font-semibold text-gray-800 text-lg mb-4">Konfirmasi Pesanan</h2>

<!-- Estimasi Harga -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-4">
    <h3 class="font-medium text-gray-700 mb-3">Estimasi Biaya</h3>
    <div class="flex justify-between py-2 border-b border-gray-100">
        <span class="text-sm text-gray-600">{{ $namaLayanan }}</span>
        <span class="text-sm font-medium">Rp {{ number_format($hargaLayanan, 0, ',', '.') }}</span>
    </div>
    <div class="flex justify-between py-2 border-b border-gray-100">
        <span class="text-sm text-gray-600">Biaya Panggil</span>
        <span class="text-sm font-medium" id="biaya-panggil-text">Rp 5.000/km (berdasarkan jarak)</span>
    </div>
    <div class="flex justify-between py-2 mt-1">
        <span class="font-semibold text-gray-800">Total Estimasi</span>
        <span class="font-bold text-orange-500" id="total-text">-</span>
    </div>
</div>

<!-- Mitra Terdekat (Haversine) -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-4">
    <h3 class="font-medium text-gray-700 mb-3">Mitra Terdekat (maks. 5 km)</h3>
    <div id="mitra-list">
        @forelse($mitras as $mitra)
            <div class="flex items-center gap-3 p-3 {{ $loop->first ? 'bg-orange-50 border border-orange-200' : 'bg-gray-50' }} rounded-lg {{ !$loop->last ? 'mb-2' : '' }}">
                @if($mitra->foto_usaha)
                    <img src="{{ asset('storage/' . $mitra->foto_usaha) }}" class="w-10 h-10 rounded-full object-cover">
                @else
                    <div class="w-10 h-10 bg-orange-200 rounded-full flex items-center justify-center text-lg">👨‍🔧</div>
                @endif
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">{{ $mitra->user->name }}</p>
                    <p class="text-xs text-gray-500">
                        ⭐ {{ number_format($mitra->rating, 1) }} • {{ $mitra->total_layanan }} layanan
                        • <span class="text-orange-600 font-medium">{{ number_format($mitra->jarak_km, 1) }} km</span>
                    </p>
                </div>
                @if($loop->first)
                    <span class="text-xs bg-orange-500 text-white px-2 py-1 rounded-full">Terdekat</span>
                @else
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Ready</span>
                @endif
            </div>
        @empty
            <div id="mitra-empty" class="text-center py-4">
                <p class="text-sm text-gray-500">Tidak ada mitra tersedia dalam radius 5 km</p>
                <p class="text-xs text-gray-400 mt-1">Semua mitra sedang tutup atau mengerjakan pesanan lain</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Form Pesan -->
<form method="POST" action="/pesan" id="form-pesan">
    @csrf
    <input type="hidden" name="layanan" value="{{ $layanan }}">
    <input type="hidden" name="latitude" id="input-lat" value="">
    <input type="hidden" name="longitude" id="input-lng" value="">

    <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Lokasi (opsional)</label>
        <input type="text" name="catatan_lokasi" placeholder="Contoh: depan minimarket, dekat lampu merah" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-300">
    </div>

    <!-- Pilih Metode Pembayaran -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-3">Metode Pembayaran</label>
        <div class="space-y-2">
            <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer has-[:checked]:border-orange-400 has-[:checked]:bg-orange-50 transition">
                <input type="radio" name="pembayaran" value="tunai" checked class="text-orange-500 focus:ring-orange-300">
                <span class="text-lg">💵</span>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">COD (Bayar di Tempat)</p>
                    <p class="text-xs text-gray-500">Bayar tunai ke teknisi setelah selesai</p>
                </div>
            </label>
            <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer has-[:checked]:border-orange-400 has-[:checked]:bg-orange-50 transition">
                <input type="radio" name="pembayaran" value="ewallet" class="text-orange-500 focus:ring-orange-300">
                <span class="text-lg">💳</span>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">Online Payment (DOKU)</p>
                    <p class="text-xs text-gray-500">QRIS, ShopeePay, DANA, OVO, Transfer Bank</p>
                </div>
            </label>
        </div>
    </div>

    <button type="submit" id="btn-pesan" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed" {{ $mitras->isEmpty() ? 'disabled' : '' }}>
        @if($mitras->isNotEmpty())
            Pesan Sekarang — Mitra {{ number_format($mitras->first()->jarak_km, 1) }} km
        @else
            Tidak ada mitra tersedia
        @endif
    </button>
</form>

<a href="/" class="block text-center text-sm text-gray-500 mt-3">← Kembali</a>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const lat = localStorage.getItem('userLat');
    const lng = localStorage.getItem('userLng');
    const urlParams = new URLSearchParams(window.location.search);
    const sudahAdaKoordinat = urlParams.has('lat') && urlParams.has('lng');

    if (lat && lng) {
        setCoords(lat, lng);
        // Hanya redirect jika URL belum punya koordinat (pertama kali load)
        if (!sudahAdaKoordinat) {
            window.location.href = '/pesan?layanan={{ $layanan }}&lat=' + lat + '&lng=' + lng;
            return;
        }
    } else {
        navigator.geolocation.getCurrentPosition(function(pos) {
            const userLat = pos.coords.latitude;
            const userLng = pos.coords.longitude;
            localStorage.setItem('userLat', userLat);
            localStorage.setItem('userLng', userLng);
            setCoords(userLat, userLng);
            if (!sudahAdaKoordinat) {
                window.location.href = '/pesan?layanan={{ $layanan }}&lat=' + userLat + '&lng=' + userLng;
                return;
            }
        }, function() {
            const emptyEl = document.getElementById('mitra-empty');
            if (emptyEl) emptyEl.innerHTML = '<p class="text-sm text-red-500">Gagal mendeteksi lokasi. Aktifkan GPS.</p>';
        });
    }

    function setCoords(lat, lng) {
        document.getElementById('input-lat').value = lat;
        document.getElementById('input-lng').value = lng;
    }
});
</script>
@endsection
