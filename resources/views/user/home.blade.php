@extends('layouts.user')
@section('title', 'Beranda')

@section('content')
<!-- Greeting + Location -->
<div class="mb-5">
    <h1 class="text-xl font-bold text-gray-900">Halo, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
    <div class="flex items-center gap-2 mt-1">
        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
        <p id="location-text" class="text-sm text-gray-500">Mendeteksi lokasi...</p>
    </div>
</div>

@if($pesananAktif)
<!-- Active Order Card (Grab-style) -->
<a href="/pesanan/{{ $pesananAktif->id }}/tracking" class="block bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-5 hover:shadow-md transition">
    <div class="flex items-center gap-3">
        <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-gray-900">Pesanan Aktif</p>
            <p class="text-xs text-gray-500">{{ $pesananAktif->nama_layanan }} • {{ ucfirst(str_replace('_', ' ', $pesananAktif->status)) }}</p>
        </div>
        <div class="flex items-center gap-1 text-emerald-600">
            <span class="text-xs font-medium">Lacak</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
    </div>
    <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
        @php $progress = match($pesananAktif->status) { 'mencari_mitra' => 25, 'mitra_menuju' => 50, 'dikerjakan' => 75, default => 10 }; @endphp
        <div class="h-full bg-emerald-500 rounded-full transition-all" style="width: {{ $progress }}%"></div>
    </div>
</a>
@endif

<!-- Services (Grab-style grid) -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
    <div class="grid grid-cols-3 gap-4">
        <a href="/pesan?layanan=tambal-ban" class="flex flex-col items-center gap-2 group">
            <div class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🔧</div>
            <div class="text-center">
                <p class="text-xs font-semibold text-gray-900">Tambal Ban</p>
                <p class="text-[10px] text-gray-400">Rp 35K</p>
            </div>
        </a>
        <a href="/pesan?layanan=isi-angin" class="flex flex-col items-center gap-2 group">
            <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">💨</div>
            <div class="text-center">
                <p class="text-xs font-semibold text-gray-900">Isi Angin</p>
                <p class="text-[10px] text-gray-400">Rp 5K</p>
            </div>
        </a>
        <a href="/pesan?layanan=ganti-ban" class="flex flex-col items-center gap-2 group">
            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🛞</div>
            <div class="text-center">
                <p class="text-xs font-semibold text-gray-900">Ganti Ban</p>
                <p class="text-[10px] text-gray-400">Rp 80K</p>
            </div>
        </a>
    </div>
</div>

<!-- Promo / Info Banner -->
<div class="bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-2xl p-5 mb-5 text-white relative overflow-hidden">
    <div class="absolute right-0 top-0 bottom-0 w-32 opacity-10">
        <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
    </div>
    <p class="text-xs font-medium text-emerald-200 uppercase tracking-wider">Layanan 24 Jam</p>
    <p class="text-lg font-bold mt-1">Mitra siap kapan pun kamu butuh</p>
    <p class="text-xs text-emerald-200 mt-1">Teknisi terdekat langsung ke lokasi kamu</p>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-3 gap-3 mb-5">
    <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center">
        <p class="text-xl font-bold text-gray-900">{{ $totalPesanan }}</p>
        <p class="text-[10px] text-gray-500 mt-0.5">Pesanan</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center">
        <p class="text-xl font-bold text-emerald-600">{{ $pesananSelesai }}</p>
        <p class="text-[10px] text-gray-500 mt-0.5">Selesai</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center">
        <p class="text-lg font-bold text-gray-900">{{ number_format($totalPengeluaran / 1000, 0) }}K</p>
        <p class="text-[10px] text-gray-500 mt-0.5">Pengeluaran</p>
    </div>
</div>

<!-- Recent Activity -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900">Aktivitas Terakhir</h3>
        <a href="/riwayat" class="text-xs text-emerald-600 font-medium">Lihat Semua</a>
    </div>
    @php
        $recentPesanan = \App\Models\Pesanan::where('user_id', auth()->id())->latest()->limit(3)->get();
    @endphp
    @forelse($recentPesanan as $p)
        <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm {{ match($p->status) { 'selesai' => 'bg-emerald-50 text-emerald-600', 'dibatalkan' => 'bg-red-50 text-red-500', default => 'bg-orange-50 text-orange-500' } }}">
                {{ match($p->status) { 'selesai' => '✅', 'dibatalkan' => '❌', default => '🔧' } }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ $p->nama_layanan }}</p>
                <p class="text-xs text-gray-400">{{ $p->created_at->diffForHumans() }}</p>
            </div>
            <span class="text-xs font-medium text-gray-600">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</span>
        </div>
    @empty
        <p class="text-sm text-gray-400 text-center py-4">Belum ada aktivitas</p>
    @endforelse
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-2 gap-3 mb-5">
    <a href="/sos" class="bg-red-50 border border-red-100 rounded-2xl p-4 flex items-center gap-3 hover:shadow-sm transition">
        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center text-lg">🆘</div>
        <div>
            <p class="text-sm font-semibold text-red-700">SOS</p>
            <p class="text-[10px] text-red-500">Darurat 24 jam</p>
        </div>
    </a>
    <a href="/daftar-mitra" class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 flex items-center gap-3 hover:shadow-sm transition">
        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-lg">🤝</div>
        <div>
            <p class="text-sm font-semibold text-emerald-700">Jadi Mitra</p>
            <p class="text-[10px] text-emerald-500">Gabung sekarang</p>
        </div>
    </a>
</div>

<script>
function detectLocation() {
    const text = document.getElementById('location-text');
    if (!navigator.geolocation) { text.textContent = 'GPS tidak tersedia'; return; }
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            localStorage.setItem('userLat', pos.coords.latitude);
            localStorage.setItem('userLng', pos.coords.longitude);
            // Reverse geocode
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${pos.coords.latitude}&lon=${pos.coords.longitude}`)
                .then(r=>r.json())
                .then(d=>{ text.textContent = d.address?.village || d.address?.suburb || d.address?.city || 'Lokasi terdeteksi'; })
                .catch(()=>{ text.textContent = 'Lokasi terdeteksi'; });
        },
        () => { text.textContent = 'Aktifkan GPS untuk lokasi akurat'; localStorage.setItem('userLat', '-6.5935'); localStorage.setItem('userLng', '110.6741'); },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 300000 }
    );
}
detectLocation();
</script>
@endsection
