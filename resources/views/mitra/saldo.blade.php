@extends('layouts.mitra')
@section('title', 'Saldo')

@section('content')
<h2 class="font-semibold text-gray-800 text-lg mb-4">💰 Saldo Kamu</h2>

<!-- Saldo -->
<div class="bg-white rounded-xl shadow-sm p-5 mb-4 text-center">
    <p class="text-sm text-gray-500">Saldo Tersedia</p>
    <p class="text-3xl font-bold text-green-600 mt-1">Rp {{ number_format($mitra->saldo, 0, ',', '.') }}</p>
    <p class="text-xs text-gray-400 mt-1">Dari pesanan online yang sudah selesai</p>
</div>

<!-- Request Pencairan -->
@if($mitra->saldo >= 50000)
    <form method="POST" action="/mitra/saldo/cairkan" class="mb-6" onsubmit="return confirm('Cairkan saldo Rp {{ number_format($mitra->saldo, 0, ',', '.') }}?\n\nPotongan 1%: Rp {{ number_format(ceil($mitra->saldo * 0.01), 0, ',', '.') }}\nYang diterima: Rp {{ number_format($mitra->saldo - ceil($mitra->saldo * 0.01), 0, ',', '.') }}')">
        @csrf
        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition">
            Cairkan Saldo — Rp {{ number_format($mitra->saldo, 0, ',', '.') }}
        </button>
        <p class="text-xs text-gray-400 text-center mt-2">Potongan 1% (Rp {{ number_format(ceil($mitra->saldo * 0.01), 0, ',', '.') }}) • Diterima Rp {{ number_format($mitra->saldo - ceil($mitra->saldo * 0.01), 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 text-center">Admin akan transfer dalam 1x24 jam</p>
    </form>
@else
    <div class="bg-gray-50 rounded-xl p-4 mb-6 text-center">
        <p class="text-sm text-gray-500">Minimum pencairan Rp 50.000</p>
    </div>
@endif

<!-- Riwayat Saldo -->
<h3 class="font-medium text-gray-700 mb-3">Riwayat</h3>
@forelse($riwayat as $item)
    <div class="bg-white rounded-lg shadow-sm p-3 mb-2 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-800">
                {{ $item->jenis === 'masuk' ? '➕' : '➖' }}
                {{ $item->keterangan ?? ucfirst($item->jenis) }}
            </p>
            <p class="text-xs text-gray-500">{{ $item->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm font-medium {{ $item->jenis === 'masuk' ? 'text-green-600' : 'text-red-600' }}">
                {{ $item->jenis === 'masuk' ? '+' : '-' }}Rp {{ number_format($item->jumlah, 0, ',', '.') }}
            </p>
            @if($item->jenis === 'pencairan')
                <span class="text-xs {{ $item->status === 'selesai' ? 'text-green-500' : 'text-yellow-500' }}">
                    {{ $item->status === 'selesai' ? '✓ Ditransfer' : '⏳ Proses' }}
                </span>
            @endif
        </div>
    </div>
@empty
    <div class="text-center py-8 text-gray-400 text-sm">Belum ada riwayat saldo</div>
@endforelse

{{ $riwayat->links() }}
@endsection
