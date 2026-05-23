<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-5">
        <div class="flex items-start gap-4">
            <!-- Foto Usaha (klik untuk lihat besar) -->
            <div class="shrink-0">
                @if($mitra->foto_usaha)
                    <img src="{{ asset('storage/' . $mitra->foto_usaha) }}"
                         class="w-24 h-24 object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-80 transition"
                         alt="Foto usaha"
                         onclick="openFoto('{{ asset('storage/' . $mitra->foto_usaha) }}')">
                @else
                    <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-2xl border border-gray-200">🏪</div>
                @endif
            </div>

            <!-- Info Utama -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="font-semibold text-gray-800 text-base">{{ $mitra->user->name }}</h3>
                    <span class="text-xs px-2 py-1 rounded-full {{ match($mitra->status) {
                        'aktif' => 'bg-green-100 text-green-700',
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'nonaktif' => 'bg-red-100 text-red-700',
                    } }}">{{ ucfirst($mitra->status) }}</span>
                </div>

                @if($mitra->nama_usaha)
                    <p class="text-sm text-gray-600 font-medium">{{ $mitra->nama_usaha }}</p>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-1 mt-2 text-sm">
                    <div class="flex items-center gap-1 text-gray-600">
                        <span class="text-gray-400">📧</span> {{ $mitra->user->email }}
                    </div>
                    <div class="flex items-center gap-1 text-gray-600">
                        <span class="text-gray-400">📱</span> {{ $mitra->user->phone ?? '-' }}
                    </div>
                    <div class="flex items-start gap-1 text-gray-600 md:col-span-2">
                        <span class="text-gray-400">📍</span> {{ $mitra->alamat }}
                    </div>
                    @if($mitra->latitude && $mitra->longitude)
                    <div class="flex items-center gap-1 text-gray-500 text-xs">
                        <span class="text-gray-400">🗺️</span> {{ $mitra->latitude }}, {{ $mitra->longitude }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Detail Tambahan -->
        <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
            <div>
                <p class="text-xs text-gray-400">Layanan</p>
                <div class="flex flex-wrap gap-1 mt-1">
                    @foreach($mitra->layanan ?? [] as $layanan)
                        <span class="text-xs bg-orange-50 text-orange-700 px-1.5 py-0.5 rounded">
                            {{ match($layanan) { 'tambal-ban' => 'Tambal Ban', 'isi-angin' => 'Isi Angin', 'ganti-ban' => 'Ganti Ban', default => $layanan } }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-400">Jam Operasional</p>
                <p class="font-medium text-gray-700 mt-1">{{ $mitra->jam_buka ?? '-' }} - {{ $mitra->jam_tutup ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Rating</p>
                <p class="font-medium text-gray-700 mt-1">⭐ {{ number_format($mitra->rating, 1) }} ({{ $mitra->total_layanan }} layanan)</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Langganan s/d</p>
                <p class="font-medium text-gray-700 mt-1">{{ $mitra->subscription_sampai?->format('d M Y') ?? '-' }}</p>
            </div>
        </div>

        <!-- Info Pembayaran -->
        @php
            $pembayaranTerakhir = $mitra->pembayarans()->latest()->first();
        @endphp
        @if($pembayaranTerakhir)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400 mb-2">💰 Pembayaran Terakhir</p>
            <div class="flex items-center gap-4 text-sm">
                <span class="px-2 py-1 rounded-full text-xs {{ match($pembayaranTerakhir->status) {
                    'lunas' => 'bg-green-100 text-green-700',
                    'pending' => 'bg-yellow-100 text-yellow-700',
                    'gagal' => 'bg-red-100 text-red-700',
                } }}">{{ ucfirst($pembayaranTerakhir->status) }}</span>
                <span class="text-gray-700 font-medium">Rp {{ number_format($pembayaranTerakhir->jumlah, 0, ',', '.') }}</span>
                <span class="text-gray-500">{{ ucfirst($pembayaranTerakhir->jenis) }}</span>
                @if($pembayaranTerakhir->metode_pembayaran)
                    <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded">{{ $pembayaranTerakhir->metode_pembayaran }}</span>
                @else
                    <span class="text-xs text-gray-400">Metode: -</span>
                @endif
                <span class="text-xs text-gray-400 ml-auto">{{ $pembayaranTerakhir->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
        @else
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs text-red-500">⚠️ Belum ada pembayaran</p>
        </div>
        @endif

        <!-- Aksi -->
        <div class="mt-4 pt-3 border-t border-gray-100 flex gap-2">
            @if($mitra->status === 'pending')
                <form method="POST" action="/admin/mitra/{{ $mitra->id }}/verifikasi">
                    @csrf
                    <button class="text-sm bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 rounded-lg transition">✓ Verifikasi</button>
                </form>
            @endif
            @if($mitra->status === 'aktif')
                <form method="POST" action="/admin/mitra/{{ $mitra->id }}/nonaktif">
                    @csrf
                    <button class="text-sm bg-red-500 hover:bg-red-600 text-white px-4 py-1.5 rounded-lg transition">✗ Nonaktifkan</button>
                </form>
            @endif
            <p class="text-xs text-gray-400 self-center ml-auto">Terdaftar: {{ $mitra->created_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
</div>
