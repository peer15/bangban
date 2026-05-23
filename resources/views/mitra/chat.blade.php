@extends('layouts.mitra')
@section('title', 'Chat')

@section('content')
<div class="max-w-3xl">
    <div class="flex flex-col h-[calc(100vh-160px)] max-h-[650px] bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <!-- Chat Header -->
        <div class="bg-white border-b border-gray-100 px-5 py-3 flex items-center gap-3 shrink-0">
            <a href="/mitra" class="p-1.5 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                {{ strtoupper(substr($pesanan->user->name, 0, 1)) }}
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900 text-sm">{{ $pesanan->user->name }}</p>
                <p class="text-xs text-gray-500">{{ $pesanan->nama_layanan }} • #{{ $pesanan->id }}</p>
            </div>
            @if($pesanan->user->phone)
            <a href="tel:{{ $pesanan->user->phone }}" class="p-2 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            </a>
            @endif
        </div>

        <!-- Chat Messages -->
        <div id="chat-messages" class="flex-1 overflow-y-auto px-5 py-4 space-y-3 bg-gray-50">
            <!-- Order info -->
            <div class="flex justify-center mb-4">
                <div class="bg-white border border-gray-200 rounded-xl px-4 py-2 text-xs text-gray-500 text-center shadow-sm">
                    🔧 {{ $pesanan->nama_layanan }} • Rp {{ number_format($pesanan->total_biaya, 0, ',', '.') }} • {{ $pesanan->pembayaran === 'tunai' ? 'COD' : 'Online' }}
                </div>
            </div>

            @foreach($chats as $chat)
                @if($chat->sender_role === 'mitra')
                    <!-- My message (right) -->
                    <div class="flex justify-end">
                        <div class="max-w-[75%]">
                            <div class="bg-red-600 text-white px-4 py-2.5 rounded-2xl rounded-br-md shadow-sm">
                                <p class="text-sm">{{ $chat->message }}</p>
                            </div>
                            <p class="text-[10px] text-gray-400 text-right mt-1 mr-1">{{ $chat->created_at->format('H:i') }} @if($chat->is_read) ✓✓ @endif</p>
                        </div>
                    </div>
                @else
                    <!-- Their message (left) -->
                    <div class="flex justify-start">
                        <div class="max-w-[75%]">
                            <div class="bg-white border border-gray-200 px-4 py-2.5 rounded-2xl rounded-bl-md shadow-sm">
                                <p class="text-sm text-gray-800">{{ $chat->message }}</p>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 ml-1">{{ $chat->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Chat Input -->
        <div class="bg-white border-t border-gray-100 px-5 py-3 shrink-0">
            <form method="POST" action="/mitra/pesanan/{{ $pesanan->id }}/chat" id="chat-form" class="flex items-center gap-2">
                @csrf
                <input type="text" name="message" id="chat-input" placeholder="Ketik pesan..." autocomplete="off" required
                    class="flex-1 border border-gray-200 rounded-full px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
                <button type="submit" class="w-10 h-10 bg-red-600 hover:bg-red-700 text-white rounded-full flex items-center justify-center transition shrink-0 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const chatMessages = document.getElementById('chat-messages');
chatMessages.scrollTop = chatMessages.scrollHeight;

let lastCount = {{ $chats->count() }};
setInterval(function() {
    fetch('/mitra/pesanan/{{ $pesanan->id }}/chat/messages')
        .then(r => r.json())
        .then(messages => {
            if (messages.length > lastCount) {
                lastCount = messages.length;
                renderMessages(messages);
            }
        }).catch(() => {});
}, 3000);

function renderMessages(messages) {
    let html = '<div class="flex justify-center mb-4"><div class="bg-white border border-gray-200 rounded-xl px-4 py-2 text-xs text-gray-500 text-center shadow-sm">🔧 {{ $pesanan->nama_layanan }} • Rp {{ number_format($pesanan->total_biaya, 0, ",", ".") }} • {{ $pesanan->pembayaran === "tunai" ? "COD" : "Online" }}</div></div>';
    messages.forEach(msg => {
        if (msg.sender_role === 'mitra') {
            html += `<div class="flex justify-end"><div class="max-w-[75%]"><div class="bg-red-600 text-white px-4 py-2.5 rounded-2xl rounded-br-md shadow-sm"><p class="text-sm">${escHtml(msg.message)}</p></div><p class="text-[10px] text-gray-400 text-right mt-1 mr-1">${msg.time} ${msg.is_read ? '✓✓' : ''}</p></div></div>`;
        } else {
            html += `<div class="flex justify-start"><div class="max-w-[75%]"><div class="bg-white border border-gray-200 px-4 py-2.5 rounded-2xl rounded-bl-md shadow-sm"><p class="text-sm text-gray-800">${escHtml(msg.message)}</p></div><p class="text-[10px] text-gray-400 mt-1 ml-1">${msg.time}</p></div></div>`;
        }
    });
    chatMessages.innerHTML = html;
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function escHtml(t) { const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }

document.getElementById('chat-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('chat-input');
    const msg = input.value.trim();
    if (!msg) return;

    const now = new Date();
    const timeStr = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');
    const bubble = `<div class="flex justify-end"><div class="max-w-[75%]"><div class="bg-red-600 text-white px-4 py-2.5 rounded-2xl rounded-br-md shadow-sm"><p class="text-sm">${escHtml(msg)}</p></div><p class="text-[10px] text-gray-400 text-right mt-1 mr-1">${timeStr}</p></div></div>`;
    chatMessages.insertAdjacentHTML('beforeend', bubble);
    chatMessages.scrollTop = chatMessages.scrollHeight;
    input.value = '';
    lastCount++;

    fetch(this.action, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ message: msg })
    }).catch(() => {});
});
</script>
@endpush
@endsection
