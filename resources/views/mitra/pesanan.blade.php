@extends('layouts.mitra')
@section('title', 'Pesanan Masuk')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')
<div class="max-w-5xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Pesanan Tersedia</h1>
        <p class="text-sm text-gray-500 mt-1">Pesanan baru yang bisa kamu ambil</p>
    </div>

    @forelse($pesanans as $pesanan)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-4">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <p class="font-semibold text-gray-900">{{ $pesanan->nama_layanan }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $pesanan->user->name }} • {{ $pesanan->created_at->diffForHumans() }}</p>
                </div>
                <span class="text-sm font-bold text-red-600">Rp {{ number_format($pesanan->total_biaya, 0, ',', '.') }}</span>
            </div>

            <div class="flex items-center gap-2 mb-3 p-2.5 rounded-lg {{ $pesanan->pembayaran === 'tunai' ? 'bg-green-50' : 'bg-blue-50' }}">
                <span class="text-sm">{{ $pesanan->pembayaran === 'tunai' ? '💵' : '💳' }}</span>
                <span class="text-xs font-medium {{ $pesanan->pembayaran === 'tunai' ? 'text-green-700' : 'text-blue-700' }}">
                    {{ $pesanan->pembayaran === 'tunai' ? 'COD — Tagih tunai ke pelanggan' : 'Online Payment — Sudah dibayar' }}
                </span>
            </div>

            @if($pesanan->catatan_lokasi)
                <p class="text-xs text-gray-500 mb-2">📝 {{ $pesanan->catatan_lokasi }}</p>
            @endif
            @if($pesanan->jarak_km)
                <p class="text-xs text-gray-500 mb-3">📏 Jarak: {{ number_format($pesanan->jarak_km, 1) }} km</p>
            @endif

            <div id="map-pesanan-{{ $pesanan->id }}" class="w-full h-40 rounded-xl border border-gray-200 mb-3"></div>
            <div class="flex gap-2 mb-4">
                <a href="https://www.google.com/maps?q={{ $pesanan->latitude }},{{ $pesanan->longitude }}" target="_blank" class="flex-1 text-center text-xs text-blue-600 font-medium bg-blue-50 py-2.5 rounded-lg hover:bg-blue-100 transition">📍 Lihat Lokasi</a>
                <a href="https://www.google.com/maps/dir/{{ $mitra->latitude ?? '' }},{{ $mitra->longitude ?? '' }}/{{ $pesanan->latitude }},{{ $pesanan->longitude }}" target="_blank" class="flex-1 text-center text-xs text-green-600 font-medium bg-green-50 py-2.5 rounded-lg hover:bg-green-100 transition">🧭 Navigasi</a>
            </div>

            <form method="POST" action="/mitra/pesanan/{{ $pesanan->id }}/terima">
                @csrf
                <button class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-3 rounded-xl transition shadow-sm">✓ Terima & Berangkat</button>
            </form>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">📋</div>
            <p class="text-gray-500 text-sm">Belum ada pesanan baru</p>
            <p class="text-gray-400 text-xs mt-1">Pesanan baru akan muncul di sini</p>
        </div>
    @endforelse
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @foreach($pesanans as $pesanan)
    (function(){
        const map = L.map('map-pesanan-{{ $pesanan->id }}', {zoomControl:false,attributionControl:false}).setView([{{ $pesanan->latitude }},{{ $pesanan->longitude }}],15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);
        L.marker([{{ $pesanan->latitude }},{{ $pesanan->longitude }}],{icon:L.divIcon({className:'',html:'<div style="background:#ef4444;width:24px;height:24px;border-radius:50%;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3)"></div>',iconSize:[24,24],iconAnchor:[12,12]})}).addTo(map);
    })();
    @endforeach
});
</script>
@endpush
@endsection
