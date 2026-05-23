@extends('layouts.user')
@section('title', 'Chat')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Chat</h1>
    <p class="text-sm text-gray-500 mt-1">Percakapan dengan mitra</p>
</div>

<div class="space-y-2">
    @forelse($pesanans as $pesanan)
        <a href="/pesanan/{{ $pesanan->id }}/chat" class="block bg-white rounded-2xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition">
            <div class="flex items-center gap-3">
                <!-- Avatar -->
                @if($pesanan->mitra->foto_usaha)
                    <img src="{{ asset('storage/' . $pesanan->mitra->foto_usaha) }}" class="w-12 h-12 rounded-full object-cover border-2 {{ $pesanan->unread_count > 0 ? 'border-orange-400' : 'border-gray-200' }}">
                @else
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($pesanan->mitra->user->name, 0, 1)) }}
                    </div>
                @endif

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $pesanan->mitra->user->name }}</p>
                        @if($pesanan->last_chat)
                            <span class="text-[10px] text-gray-400 shrink-0">{{ $pesanan->last_chat->created_at->format('H:i') }}</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ $pesanan->nama_layanan }} • {{ ucfirst(str_replace('_', ' ', $pesanan->status)) }}</p>
                    @if($pesanan->last_chat)
                        <p class="text-xs text-gray-500 truncate mt-1 {{ $pesanan->unread_count > 0 ? 'font-semibold text-gray-800' : '' }}">
                            @if($pesanan->last_chat->sender_role === 'user') <span class="text-gray-400">Kamu:</span> @endif
                            {{ Str::limit($pesanan->last_chat->message, 40) }}
                        </p>
                    @else
                        <p class="text-xs text-gray-400 mt-1 italic">Belum ada pesan</p>
                    @endif
                </div>

                <!-- Unread badge -->
                @if($pesanan->unread_count > 0)
                    <div class="w-6 h-6 bg-orange-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center shrink-0">
                        {{ $pesanan->unread_count }}
                    </div>
                @endif
            </div>
        </a>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">💬</div>
            <p class="text-gray-500 text-sm font-medium">Belum ada percakapan</p>
            <p class="text-gray-400 text-xs mt-1">Chat akan muncul setelah mitra menerima pesanan kamu</p>
        </div>
    @endforelse
</div>
@endsection
