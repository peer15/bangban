@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl md:text-3xl font-bold text-slate-900">Dashboard</h1>
    <p class="text-sm text-slate-500 mt-1">Ringkasan aktivitas BANGBAN hari ini</p>
</div>

<!-- Saldo DOKU Card -->
<div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-blue-900 rounded-2xl p-6 md:p-8 mb-8 text-white shadow-xl">
    <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 80% 20%, rgba(59,130,246,0.5) 0%, transparent 50%);"></div>
    <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-sm text-blue-200 font-medium">Total Saldo dari Transaksi DOKU</p>
            <p class="text-3xl md:text-4xl font-bold mt-1 tracking-tight">Rp {{ number_format($totalSaldoDoku, 0, ',', '.') }}</p>
        </div>
        <div class="flex gap-6 text-sm">
            <div class="text-center">
                <p class="text-blue-300">Dicairkan</p>
                <p class="font-semibold text-lg">Rp {{ number_format($totalDicairkan, 0, ',', '.') }}</p>
            </div>
            <div class="text-center">
                <p class="text-blue-300">Sisa</p>
                <p class="font-semibold text-lg text-emerald-300">Rp {{ number_format($saldoBangban, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Pengajuan Pencairan -->
@if($pencairanPending->isNotEmpty())
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-8">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
            <h3 class="font-semibold text-amber-900">Pengajuan Pencairan ({{ $pencairanPending->count() }})</h3>
        </div>
        <a href="/admin/pencairan" class="text-xs text-amber-700 font-medium hover:underline">Lihat semua →</a>
    </div>
    <div class="space-y-2">
        @foreach($pencairanPending->take(3) as $p)
            <div class="flex items-center justify-between bg-white rounded-xl p-4 border border-amber-100">
                <div>
                    <p class="text-sm font-semibold text-slate-800">{{ $p->mitra->user->name }}</p>
                    <p class="text-xs text-slate-500">{{ $p->mitra->jenis_rekening ?? '-' }} • {{ $p->mitra->nomor_rekening ?? '-' }} • {{ $p->created_at->diffForHumans() }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-bold text-amber-700">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</span>
                    <form method="POST" action="/admin/pencairan/{{ $p->id }}/selesai" onsubmit="return confirm('Tandai sudah transfer?')">
                        @csrf
                        <button class="text-xs bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg font-medium transition">✓ Transfer</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Stats Grid -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 hover:shadow-md transition">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center text-base">👥</div>
        </div>
        <p class="text-2xl font-bold text-slate-900">{{ $totalUser }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Pengguna</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 hover:shadow-md transition">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-9 h-9 bg-emerald-50 rounded-xl flex items-center justify-center text-base">👨‍🔧</div>
        </div>
        <p class="text-2xl font-bold text-emerald-600">{{ $mitraAktif }}</p>
        <p class="text-xs text-slate-500 mt-1">Mitra Aktif</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 hover:shadow-md transition">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-9 h-9 bg-amber-50 rounded-xl flex items-center justify-center text-base">⏳</div>
        </div>
        <p class="text-2xl font-bold text-amber-600">{{ $mitraPending }}</p>
        <p class="text-xs text-slate-500 mt-1">Mitra Pending</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 hover:shadow-md transition">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-9 h-9 bg-orange-50 rounded-xl flex items-center justify-center text-base">💰</div>
        </div>
        <p class="text-lg font-bold text-slate-900">Rp {{ number_format($pendapatanBulan / 1000, 0) }}K</p>
        <p class="text-xs text-slate-500 mt-1">Pendapatan Bulan Ini</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 font-medium">Total Pesanan</p>
                <p class="text-3xl font-bold text-slate-900 mt-1">{{ $totalPesanan }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-500">Hari ini</p>
                <p class="text-lg font-bold text-orange-500">{{ $pesananHariIni }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 font-medium">Total Mitra</p>
                <p class="text-3xl font-bold text-slate-900 mt-1">{{ $totalMitra }}</p>
            </div>
            <a href="/admin/mitra" class="text-xs text-orange-600 font-medium hover:underline">Kelola →</a>
        </div>
    </div>
</div>
@endsection
