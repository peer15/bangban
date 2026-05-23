@extends('layouts.mitra')
@section('title', 'Langganan')

@section('content')
<div class="max-w-5xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Subscription</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola langganan bulanan kamu</p>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-4 mb-6">{{ session('error') }}</div>
    @endif

    <!-- Status -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Status Langganan</p>
                @if($mitra->subscription_sampai && $mitra->subscription_sampai >= now())
                    <p class="text-xl font-bold text-emerald-600 mt-1">Aktif</p>
                    <p class="text-sm text-gray-500 mt-0.5">Berlaku sampai: <span class="font-medium text-gray-700">{{ $mitra->subscription_sampai->format('d M Y') }}</span></p>
                    @php $sisaHari = now()->diffInDays($mitra->subscription_sampai, false); @endphp
                    @if($sisaHari <= 7)
                        <p class="text-xs text-amber-600 font-medium mt-1">⚠️ Sisa {{ $sisaHari }} hari</p>
                    @endif
                @else
                    <p class="text-xl font-bold text-red-600 mt-1">Tidak Aktif</p>
                    <p class="text-sm text-gray-500 mt-0.5">Langganan sudah berakhir</p>
                @endif
            </div>
            <div class="w-14 h-14 rounded-2xl {{ ($mitra->subscription_sampai && $mitra->subscription_sampai >= now()) ? 'bg-emerald-50' : 'bg-red-50' }} flex items-center justify-center text-2xl">
                {{ ($mitra->subscription_sampai && $mitra->subscription_sampai >= now()) ? '✅' : '❌' }}
            </div>
        </div>
    </div>

    <!-- Paket -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-red-600 to-red-700 p-6 text-white">
            <h3 class="text-lg font-bold">Langganan Bulanan</h3>
            <p class="text-red-200 text-sm mt-1">Perpanjang akses ke sistem BANGBAN</p>
            <p class="text-3xl font-bold mt-3">Rp 150.000<span class="text-base font-normal text-red-200">/bulan</span></p>
        </div>
        <div class="p-6">
            <h4 class="font-medium text-gray-700 mb-3">Yang kamu dapatkan:</h4>
            <ul class="space-y-2.5">
                @foreach(['Akses database pelanggan BANGBAN', 'GPS & pemetaan digital real-time', 'Terima pesanan dari pengguna terdekat', 'Profil mitra terverifikasi di aplikasi', 'Pencairan saldo online'] as $benefit)
                <li class="flex items-center gap-3 text-sm text-gray-600">
                    <span class="w-5 h-5 bg-red-100 rounded-full flex items-center justify-center text-red-600 text-xs font-bold">✓</span>
                    {{ $benefit }}
                </li>
                @endforeach
            </ul>
            <form method="POST" action="/mitra/langganan/bayar" class="mt-6">
                @csrf
                @if($mitra->subscription_sampai && $mitra->subscription_sampai >= now())
                    <button type="button" disabled class="w-full bg-gray-200 text-gray-500 font-semibold py-3.5 rounded-xl cursor-not-allowed">Langganan Masih Aktif</button>
                    <p class="text-xs text-gray-400 text-center mt-3">Bisa diperpanjang setelah {{ $mitra->subscription_sampai->format('d M Y') }}</p>
                @else
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3.5 rounded-xl transition">Perpanjang — Rp 150.000</button>
                    <p class="text-xs text-gray-400 text-center mt-3">Pembayaran via DOKU (QRIS, E-Wallet, Transfer Bank)</p>
                @endif
            </form>
        </div>
    </div>

    <!-- Riwayat -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Riwayat Pembayaran</h3>
        @forelse($riwayatLangganan as $p)
            <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                <div>
                    <p class="text-sm font-medium text-gray-700">{{ ucfirst($p->jenis) }}</p>
                    <p class="text-xs text-gray-400">{{ $p->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-800">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</p>
                    <span class="text-xs {{ $p->status === 'lunas' ? 'text-emerald-600' : ($p->status === 'pending' ? 'text-amber-600' : 'text-red-600') }}">{{ ucfirst($p->status) }}</span>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400 text-center py-4">Belum ada riwayat</p>
        @endforelse
    </div>
</div>
@endsection
