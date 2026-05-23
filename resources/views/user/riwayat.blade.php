@extends('layouts.user')
@section('title', 'Riwayat Pesanan')

@section('content')
<h2 class="font-semibold text-gray-800 text-lg mb-4">Riwayat Pesanan</h2>

@forelse($pesanans as $pesanan)
    <a href="/pesanan/{{ $pesanan->id }}/tracking" class="block bg-white rounded-xl shadow-sm p-4 mb-3 hover:ring-2 hover:ring-orange-200 transition">
        <div class="flex justify-between items-start">
            <div>
                <p class="font-medium text-gray-800">{{ $pesanan->nama_layanan }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $pesanan->created_at->format('d M Y, H:i') }}</p>
            </div>
            <span class="text-xs px-2 py-1 rounded-full {{ match($pesanan->status) {
                'selesai' => 'bg-green-100 text-green-700',
                'dibatalkan' => 'bg-red-100 text-red-700',
                default => 'bg-orange-100 text-orange-700',
            } }}">
                {{ ucfirst(str_replace('_', ' ', $pesanan->status)) }}
            </span>
        </div>
        <p class="text-sm font-medium text-orange-500 mt-2">Rp {{ number_format($pesanan->total_biaya, 0, ',', '.') }}</p>
    </a>
@empty
    <div class="text-center py-12">
        <p class="text-gray-400">Belum ada pesanan</p>
        <a href="/" class="text-orange-500 text-sm mt-2 inline-block">Pesan sekarang →</a>
    </div>
@endforelse

{{ $pesanans->links() }}
@endsection
