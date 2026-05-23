@extends('layouts.mitra')
@section('title', 'Chat')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Chat</h1>
        <p class="text-sm text-gray-500 mt-1">Percakapan dengan pelanggan</p>
    </div>

    <div class="space-y-2">
        @forelse($pesanans as $pesanan)
            <a href="/mitra/pesanan/{{ $pesanan->id }}/chat" class="block bg-white rounded-2xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <!-- Avatar -->
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold shrink-0 {{ $pesanan->unread_count > 0 ? 'ring-2 ring-red-400 ring-offset-2' : '' }}">
                        {{ strtoupper(substr($pesanan->user->name, 0, 1)) }}
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-gray-900 text-sm truncate">{{ $pesanan->user->name }}</p>
                            @if($pesanan->last_chat)
                                <span class="text-[10px] text-gray-400 shrink-0">{{ $pesanan->last_chat->created_at->format('H:i') }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <p class="text-xs text-gray-500 truncate">{{ $pesanan->nama_layanan }}</p>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full shrink-0 {{ match($pesanan->status) { 'mencari_mitra' => 'bg-yellow-100 text-yellow-700', 'mitra_menuju' => 'bg-blue-100 text-blue-700', 'dikerjakan' => 'bg-purple-100 text-purple-700', 'selesai' => 'bg-green-100 text-green-700', default => 'bg-gray-100 text-gray-600' } }}">
                                {{ ucfirst(str_replace('_', ' ', $pesanan->status)) }}
                            </span>
                        </div>
                        @if($pesanan->last_chat)
                            <p class="text-xs text-gray-500 truncate mt-1 {{ $pesanan->unread_count > 0 ? 'font-semibold text-gray-800' : '' }}">
                                @if($pesanan->last_chat->sender_role === 'mitra') <span class="text-gray-400">Kamu:</span> @endif
                                {{ Str::limit($pesanan->last_chat->message, 40) }}
                            </p>
                        @else
                            <p class="text-xs text-gray-400 mt-1 italic">Belum ada pesan — mulai chat</p>
                        @endif
                    </div>

                    <!-- Unread badge -->
                    @if($pesanan->unread_count > 0)
                        <div class="w-6 h-6 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center shrink-0">
                            {{ $pesanan->unread_count }}
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">💬</div>
                <p class="text-gray-500 text-sm font-medium">Belum ada percakapan</p>
                <p class="text-gray-400 text-xs mt-1">Chat akan muncul setelah kamu menerima pesanan</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
