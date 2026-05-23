@extends('layouts.user')
@section('title', 'Tracking Pesanan')

@section('content')
@if($pesanan->status === 'mencari_mitra')
<!-- SEARCHING STATE - Animated Matching -->
<div id="searching-state">
    <div class="relative flex flex-col items-center justify-center py-8">
        <!-- Radar Animation -->
        <div class="relative w-48 h-48 mb-6">
            <!-- Pulse rings -->
            <div class="absolute inset-0 rounded-full border-2 border-orange-200 animate-ping opacity-20"></div>
            <div class="absolute inset-4 rounded-full border-2 border-orange-300 animate-ping opacity-30" style="animation-delay: 0.5s"></div>
            <div class="absolute inset-8 rounded-full border-2 border-orange-400 animate-ping opacity-40" style="animation-delay: 1s"></div>
            <!-- Static rings -->
            <div class="absolute inset-0 rounded-full border border-gray-200"></div>
            <div class="absolute inset-6 rounded-full border border-gray-200"></div>
            <div class="absolute inset-12 rounded-full border border-gray-200"></div>
            <!-- Center icon -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-red-500 rounded-full flex items-center justify-center shadow-lg shadow-orange-500/30">
                    <svg class="w-10 h-10 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            <!-- Floating dots (simulating mitra positions) -->
            <div class="absolute top-4 right-8 w-3 h-3 bg-orange-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            <div class="absolute bottom-8 left-4 w-2.5 h-2.5 bg-orange-300 rounded-full animate-bounce" style="animation-delay: 0.8s"></div>
            <div class="absolute top-12 left-6 w-2 h-2 bg-orange-200 rounded-full animate-bounce" style="animation-delay: 1.2s"></div>
        </div>

        <h2 class="text-xl font-bold text-gray-900 mb-1">Mencari Mitra Terdekat</h2>
        <p class="text-sm text-gray-500 mb-4">Menghubungkan kamu dengan teknisi terbaik...</p>

        <!-- Animated dots -->
        <div class="flex gap-1.5 mb-6">
            <div class="w-2 h-2 bg-orange-500 rounded-full animate-bounce" style="animation-delay: 0s"></div>
            <div class="w-2 h-2 bg-orange-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            <div class="w-2 h-2 bg-orange-500 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
        </div>

        <!-- Info card -->
        <div class="w-full bg-orange-50 border border-orange-100 rounded-2xl p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center text-lg">🔧</div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">{{ $pesanan->nama_layanan }}</p>
                    <p class="text-xs text-gray-500">Rp {{ number_format($pesanan->total_biaya, 0, ',', '.') }} • {{ $pesanan->pembayaran === 'tunai' ? 'COD' : 'Online' }}</p>
                </div>
            </div>
        </div>

        <!-- Tips -->
        <div class="mt-4 text-center">
            <p class="text-xs text-gray-400">💡 Biasanya mitra merespon dalam 1-3 menit</p>
        </div>
    </div>
</div>

@elseif($pesanan->mitra)
<!-- FOUND STATE - Mitra ditemukan -->
<div id="found-state">
    <!-- Success header -->
    <div class="text-center mb-6">
        <div class="relative inline-block mb-3">
            <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/30 animate-[bounceIn_0.6s_ease-out]">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <!-- Confetti dots -->
            <div class="absolute -top-2 -left-2 w-3 h-3 bg-yellow-400 rounded-full animate-ping"></div>
            <div class="absolute -top-1 -right-3 w-2 h-2 bg-red-400 rounded-full animate-ping" style="animation-delay: 0.3s"></div>
            <div class="absolute -bottom-1 -left-3 w-2.5 h-2.5 bg-blue-400 rounded-full animate-ping" style="animation-delay: 0.6s"></div>
        </div>
        <h2 class="text-xl font-bold text-gray-900">Mitra Ditemukan! 🎉</h2>
        <p class="text-sm text-gray-500 mt-1">{{ ucfirst(str_replace('_', ' ', $pesanan->status)) }}</p>
    </div>

    <!-- Progress Steps -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
        @php
            $steps = ['mencari_mitra', 'mitra_menuju', 'dikerjakan', 'selesai'];
            $currentIndex = array_search($pesanan->status, $steps);
            $labels = ['Mencari Mitra', 'Mitra Menuju Lokasi', 'Sedang Dikerjakan', 'Selesai'];
            $icons = ['🔍', '🏍️', '🔧', '✅'];
        @endphp
        <div class="relative">
            @foreach($labels as $i => $label)
                <div class="flex items-center gap-4 {{ $i < count($labels) - 1 ? 'pb-5' : '' }} relative">
                    @if($i < count($labels) - 1)
                        <div class="absolute left-[18px] top-10 w-0.5 h-[calc(100%-24px)] {{ $i < $currentIndex ? 'bg-emerald-400' : 'bg-gray-200' }}"></div>
                    @endif
                    <div class="relative z-10 w-9 h-9 rounded-full flex items-center justify-center text-sm shrink-0
                        {{ $i < $currentIndex ? 'bg-emerald-500 text-white shadow-sm' : ($i === $currentIndex ? 'bg-orange-500 text-white shadow-md shadow-orange-500/30 animate-pulse' : 'bg-gray-100 text-gray-400') }}">
                        @if($i < $currentIndex) ✓ @elseif($i === $currentIndex) {{ $icons[$i] }} @else {{ $i + 1 }} @endif
                    </div>
                    <div>
                        <p class="text-sm {{ $i <= $currentIndex ? 'font-semibold text-gray-900' : 'text-gray-400' }}">{{ $label }}</p>
                        @if($i === $currentIndex)
                            <p class="text-xs text-orange-600 mt-0.5">Sedang berlangsung</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Mitra Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
        <div class="flex items-center gap-4 mb-4">
            @if($pesanan->mitra->foto_usaha)
                <img src="{{ asset('storage/' . $pesanan->mitra->foto_usaha) }}" class="w-16 h-16 rounded-2xl object-cover border-2 border-orange-200 shadow-sm">
            @else
                <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-600 rounded-2xl flex items-center justify-center text-2xl shadow-sm">👨‍🔧</div>
            @endif
            <div class="flex-1">
                <p class="font-bold text-gray-900 text-lg">{{ $pesanan->mitra->user->name }}</p>
                @if($pesanan->mitra->nama_usaha)
                    <p class="text-xs text-gray-500">{{ $pesanan->mitra->nama_usaha }}</p>
                @endif
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium">⭐ {{ number_format($pesanan->mitra->rating, 1) }}</span>
                    <span class="text-xs text-gray-400">{{ $pesanan->mitra->total_layanan }} layanan</span>
                </div>
            </div>
        </div>

        <!-- Detail -->
        <div class="space-y-2 text-sm text-gray-600 border-t border-gray-50 pt-3">
            <p class="flex items-center gap-2"><span>📍</span> {{ $pesanan->mitra->alamat }}</p>
            @if($pesanan->mitra->layanan)
                <p class="flex items-center gap-2"><span>🔧</span> {{ implode(', ', array_map(fn($l) => match($l) { 'tambal-ban' => 'Tambal Ban', 'isi-angin' => 'Isi Angin', 'ganti-ban' => 'Ganti Ban', default => $l }, $pesanan->mitra->layanan)) }}</p>
            @endif
            @if($pesanan->jarak_km)
                <p class="flex items-center gap-2"><span>📏</span> {{ number_format($pesanan->jarak_km, 1) }} km dari kamu</p>
            @endif
        </div>

        <!-- Contact buttons -->
        <div class="flex gap-2 mt-4">
            <a href="/pesanan/{{ $pesanan->id }}/chat" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium py-2.5 rounded-xl text-center transition flex items-center justify-center gap-2 relative">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Chat
                <span id="chat-badge" class="hidden absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center"></span>
            </a>
            @if($pesanan->mitra->user->phone)
            <a href="tel:{{ $pesanan->mitra->user->phone }}" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium py-2.5 rounded-xl text-center transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                Telepon
            </a>
            <a href="https://wa.me/{{ preg_replace('/^0/', '62', $pesanan->mitra->user->phone) }}" class="flex-1 bg-green-500 hover:bg-green-600 text-white text-sm font-medium py-2.5 rounded-xl text-center transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                WA
            </a>
            @endif
        </div>

        <script>
        // Check unread messages
        setInterval(function() {
            fetch('/pesanan/{{ $pesanan->id }}/chat/unread').then(r=>r.json()).then(d=>{
                const badge = document.getElementById('chat-badge');
                if(d.count > 0) { badge.textContent = d.count; badge.classList.remove('hidden'); }
                else { badge.classList.add('hidden'); }
            }).catch(()=>{});
        }, 5000);
        </script>
    </div>
@endif

<!-- Detail Biaya -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
    <h3 class="font-semibold text-gray-900 mb-3">Detail Biaya</h3>
    <div class="space-y-2">
        <div class="flex justify-between text-sm"><span class="text-gray-600">Layanan</span><span class="font-medium">Rp {{ number_format($pesanan->biaya_layanan, 0, ',', '.') }}</span></div>
        <div class="flex justify-between text-sm"><span class="text-gray-600">Biaya Panggil</span><span class="font-medium">Rp {{ number_format($pesanan->biaya_panggil, 0, ',', '.') }}</span></div>
        <div class="flex justify-between text-sm pt-2 border-t border-gray-100 font-semibold"><span>Total</span><span class="text-orange-600">Rp {{ number_format($pesanan->total_biaya, 0, ',', '.') }}</span></div>
        <div class="flex justify-between text-xs pt-1"><span class="text-gray-400">Pembayaran</span><span class="font-medium {{ $pesanan->pembayaran === 'tunai' ? 'text-emerald-600' : 'text-blue-600' }}">{{ $pesanan->pembayaran === 'tunai' ? '💵 COD' : '💳 Online' }}</span></div>
    </div>
</div>

@if($pesanan->status === 'selesai' && !$pesanan->rating)
    <a href="/pesanan/{{ $pesanan->id }}/rating" class="block w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3.5 rounded-xl text-center transition shadow-sm">⭐ Beri Rating</a>
@endif

@if(!in_array($pesanan->status, ['selesai', 'dibatalkan']))
    <form method="POST" action="/pesanan/{{ $pesanan->id }}/cancel" class="mt-3" onsubmit="return confirm('Yakin ingin membatalkan pesanan?')">
        @csrf
        <button type="submit" class="w-full border border-red-200 text-red-500 hover:bg-red-50 font-medium py-2.5 rounded-xl text-sm transition">Batalkan Pesanan</button>
    </form>
@endif

<!-- Auto-refresh saat mencari mitra -->
@if($pesanan->status === 'mencari_mitra')
<script>
    // Auto refresh setiap 5 detik untuk cek apakah mitra sudah ditemukan
    setTimeout(function() {
        window.location.reload();
    }, 5000);
</script>
@elseif(!in_array($pesanan->status, ['selesai', 'dibatalkan']))
<script>
    // Refresh setiap 10 detik untuk update status
    setTimeout(function() {
        window.location.reload();
    }, 10000);
</script>
@endif

<style>
@keyframes bounceIn {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); opacity: 1; }
}
</style>
@endsection
