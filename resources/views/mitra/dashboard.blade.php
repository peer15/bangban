@extends('layouts.mitra')
@section('title', 'Dashboard')

@section('content')
@if($mitra && $mitra->status === 'pending')
    @php $sudahBayar = \App\Models\PembayaranMitra::where('mitra_id', $mitra->id)->where('jenis', 'pendaftaran')->where('status', 'lunas')->exists(); @endphp
    @if(!$sudahBayar)
        <div class="bg-orange-50 border border-orange-200 text-orange-700 text-sm rounded-lg p-4 mb-5 flex items-center gap-3">
            <i class="ti ti-credit-card text-xl"></i>
            <div class="flex-1">
                <p class="font-medium">Pembayaran pendaftaran belum selesai</p>
                <a href="/mitra/pembayaran" class="text-xs underline font-medium">Bayar sekarang →</a>
            </div>
        </div>
    @else
        <div class="bg-amber-50 border border-amber-200 text-amber-700 text-sm rounded-lg p-4 mb-5 flex items-center gap-3">
            <i class="ti ti-clock text-xl"></i>
            <p>Pembayaran lunas. Menunggu verifikasi admin.</p>
        </div>
    @endif
@elseif($mitra && $mitra->status === 'nonaktif')
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-4 mb-5 flex items-center gap-3">
        <i class="ti ti-x text-xl"></i>
        <p>Akun mitra kamu sedang nonaktif.</p>
    </div>
@endif

@if($mitra && $mitra->status === 'aktif')
<!-- Welcome Row -->
<div class="flex items-start justify-between mb-5">
    <div>
        <h2 class="text-lg font-medium text-gray-900">Selamat datang, {{ auth()->user()->name }}</h2>
        <p class="text-[13px] text-gray-500 mt-0.5">Ini ringkasan aktivitas toko kamu hari ini</p>
    </div>
    <form method="POST" action="/mitra/toggle-open">
        @csrf
        <button type="submit" class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-[13px] border transition {{ $mitra->is_open ? 'border-red-200 text-red-700 hover:bg-red-50' : 'border-[#1d9e75] text-[#0f6e56] bg-[#e1f5ee] hover:bg-[#d0f0e4]' }}">
            <i class="ti {{ $mitra->is_open ? 'ti-door-exit' : 'ti-door-enter' }} text-[15px]"></i>
            {{ $mitra->is_open ? 'Tutup toko' : 'Buka toko' }}
        </button>
    </form>
</div>

<!-- Saldo Panel -->
<div class="bg-[#1a5c3a] rounded-xl p-5 mb-5 flex items-center justify-between">
    <div>
        <p class="text-[11px] text-white/60 uppercase tracking-wider mb-1.5">Saldo tersedia</p>
        <p class="text-[28px] font-medium text-white tracking-tight">Rp {{ number_format($mitra->saldo, 0, ',', '.') }}</p>
        @php $pencairanPending = \App\Models\SaldoMitra::where('mitra_id', $mitra->id)->where('jenis', 'pencairan')->where('status', 'pending')->latest()->first(); @endphp
        @if($pencairanPending)
            <p class="text-[12px] text-white/50 mt-1">Pencairan Rp {{ number_format($pencairanPending->jumlah, 0, ',', '.') }} sedang diproses</p>
        @else
            <p class="text-[12px] text-white/50 mt-1">{{ $mitra->saldo > 0 ? 'Dari pesanan online' : 'Belum ada pendapatan masuk' }}</p>
        @endif
    </div>
    <div class="flex gap-2">
        @if($mitra->saldo >= 50000)
        <form method="POST" action="/mitra/saldo/cairkan" onsubmit="return confirm('Cairkan saldo?')">
            @csrf
            <button type="submit" class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-[13px] border border-white/25 bg-white/10 text-white/90 hover:bg-white/20 transition">
                <i class="ti ti-arrow-down text-sm"></i> Tarik dana
            </button>
        </form>
        @endif
        <a href="/mitra/saldo" class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-[13px] border border-white/25 bg-white/10 text-white/90 hover:bg-white/20 transition">
            <i class="ti ti-chart-line text-sm"></i> Detail saldo
        </a>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
    <div class="bg-white border border-gray-200/60 rounded-xl p-4 flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-[10px] bg-[#e1f5ee] flex items-center justify-center">
            <i class="ti ti-list-check text-[#0f6e56] text-xl"></i>
        </div>
        <div>
            <p class="text-[12px] text-gray-500">Total layanan</p>
            <p class="text-xl font-medium text-gray-900">{{ $totalSelesai }}</p>
            <p class="text-[11px] text-gray-400">Semua waktu</p>
        </div>
    </div>
    <div class="bg-white border border-gray-200/60 rounded-xl p-4 flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-[10px] bg-[#faeeda] flex items-center justify-center">
            <i class="ti ti-coins text-[#854f0b] text-xl"></i>
        </div>
        <div>
            <p class="text-[12px] text-gray-500">Pendapatan bulan ini</p>
            <p class="text-xl font-medium text-gray-900">Rp {{ number_format($pendapatanBulan, 0, ',', '.') }}</p>
            <p class="text-[11px] text-gray-400">{{ now()->locale('id')->isoFormat('MMMM Y') }}</p>
        </div>
    </div>
    <div class="bg-white border border-gray-200/60 rounded-xl p-4 flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-[10px] bg-[#eeedfe] flex items-center justify-center">
            <i class="ti ti-package text-[#534ab7] text-xl"></i>
        </div>
        <div>
            <p class="text-[12px] text-gray-500">Pesanan aktif</p>
            <p class="text-xl font-medium text-gray-900">{{ $pesananMasuk->count() }}</p>
            <p class="text-[11px] text-gray-400">Sedang berjalan</p>
        </div>
    </div>
</div>

<!-- Two Column: Pesanan Aktif + Rating -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    <!-- Pesanan Aktif -->
    <div class="bg-white border border-gray-200/60 rounded-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-900 flex items-center gap-2"><i class="ti ti-clock text-gray-400"></i> Pesanan aktif</h3>
            <a href="/mitra/pesanan" class="text-[12px] text-[#1d9e75] font-medium">Lihat semua</a>
        </div>
        @forelse($pesananMasuk as $pesanan)
            <div class="border border-gray-100 rounded-lg p-3 mb-2 last:mb-0">
                <div class="flex items-start justify-between mb-1.5">
                    <div>
                        <p class="text-[13px] font-medium text-gray-900">{{ $pesanan->nama_layanan }}</p>
                        <p class="text-[11px] text-gray-500">{{ $pesanan->user->name }} • {{ $pesanan->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="text-[11px] px-2 py-0.5 rounded-full font-medium {{ match($pesanan->status) { 'mencari_mitra' => 'bg-yellow-100 text-yellow-700', 'mitra_menuju' => 'bg-blue-100 text-blue-700', 'dikerjakan' => 'bg-purple-100 text-purple-700', default => 'bg-gray-100 text-gray-600' } }}">
                        {{ ucfirst(str_replace('_', ' ', $pesanan->status)) }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[12px] font-medium text-gray-700">Rp {{ number_format($pesanan->total_biaya, 0, ',', '.') }}</span>
                    <div class="flex gap-1.5">
                        <a href="/mitra/pesanan/{{ $pesanan->id }}/chat" class="w-7 h-7 rounded-md border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition">
                            <i class="ti ti-message-circle text-gray-400 text-sm"></i>
                        </a>
                        @if($pesanan->status === 'mencari_mitra')
                            <form method="POST" action="/mitra/pesanan/{{ $pesanan->id }}/status">@csrf @method('PATCH')<input type="hidden" name="status" value="mitra_menuju">
                                <button class="h-7 px-2.5 rounded-md bg-[#1d9e75] text-white text-[11px] font-medium hover:bg-[#178a65] transition">Berangkat</button>
                            </form>
                        @elseif($pesanan->status === 'mitra_menuju')
                            <form method="POST" action="/mitra/pesanan/{{ $pesanan->id }}/status">@csrf @method('PATCH')<input type="hidden" name="status" value="dikerjakan">
                                <button class="h-7 px-2.5 rounded-md bg-purple-600 text-white text-[11px] font-medium hover:bg-purple-700 transition">Kerjakan</button>
                            </form>
                        @elseif($pesanan->status === 'dikerjakan')
                            <form method="POST" action="/mitra/pesanan/{{ $pesanan->id }}/status">@csrf @method('PATCH')<input type="hidden" name="status" value="selesai">
                                <button class="h-7 px-2.5 rounded-md bg-emerald-600 text-white text-[11px] font-medium hover:bg-emerald-700 transition">Selesai</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <i class="ti ti-package-off text-3xl text-gray-300 block mb-2"></i>
                <p class="text-[13px] text-gray-500">Belum ada pesanan aktif</p>
                <p class="text-[12px] text-gray-400">Pesanan baru akan muncul di sini</p>
            </div>
        @endforelse
    </div>

    <!-- Rating -->
    <div class="bg-white border border-gray-200/60 rounded-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-900 flex items-center gap-2"><i class="ti ti-star text-gray-400"></i> Rating toko</h3>
            <span class="text-[12px] text-[#1d9e75] font-medium">Lihat ulasan</span>
        </div>
        <div class="flex items-center gap-4 mb-4">
            <p class="text-4xl font-medium text-gray-900">{{ number_format($mitra->rating, 1) }}</p>
            <div>
                <div class="flex gap-0.5 mb-1">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($mitra->rating))
                            <i class="ti ti-star-filled text-[#ba7517] text-base"></i>
                        @elseif($i - $mitra->rating < 1)
                            <i class="ti ti-star-half-filled text-[#ba7517] text-base"></i>
                        @else
                            <i class="ti ti-star text-gray-300 text-base"></i>
                        @endif
                    @endfor
                </div>
                <p class="text-[12px] text-gray-400">dari {{ $mitra->total_layanan }} layanan</p>
            </div>
        </div>
        <!-- Rating bars -->
        <div class="space-y-1.5">
            @for($i = 5; $i >= 1; $i--)
                <div class="flex items-center gap-2">
                    <span class="text-[12px] text-gray-500 w-3 text-right">{{ $i }}</span>
                    <div class="flex-1 h-[5px] bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-[#1d9e75] rounded-full" style="width: {{ $i === 5 ? '80' : ($i === 4 ? '15' : '5') }}%"></div>
                    </div>
                    <span class="text-[11px] text-gray-400 w-3">0</span>
                </div>
            @endfor
        </div>
    </div>
</div>
@endif
@endsection
