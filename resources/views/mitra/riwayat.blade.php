@extends('layouts.mitra')
@section('title', 'Riwayat')

@section('content')
<div class="max-w-5xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">History</h1>
        <p class="text-sm text-gray-500 mt-1">Riwayat layanan yang sudah selesai</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        @forelse($pesanans as $pesanan)
            <div class="flex items-center justify-between p-5 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $pesanan->nama_layanan }}</p>
                        <p class="text-xs text-gray-500">{{ $pesanan->user->name }} • {{ $pesanan->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                <span class="text-sm font-semibold text-emerald-600">+Rp {{ number_format($pesanan->total_biaya, 0, ',', '.') }}</span>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-xl">📋</div>
                <p class="text-gray-500 text-sm">Belum ada riwayat layanan</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $pesanans->links() }}</div>
</div>
@endsection
