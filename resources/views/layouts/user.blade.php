<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BANGBAN - @yield('title', 'Tambal Ban Online')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased pb-16 md:pb-0">
    <!-- Top Bar (minimal like Grab) -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <img src="/logo.jpg" alt="BANGBAN" class="h-8 w-8 object-cover rounded-xl">
                <span class="text-lg font-bold text-gray-900">BANGBAN</span>
            </a>
            <div class="flex items-center gap-2">
                <!-- Chat -->
                <a href="/chat" id="nav-chat-link" class="relative p-2 rounded-xl hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span id="nav-chat-badge" class="hidden absolute -top-0.5 -right-0.5 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center"></span>
                </a>
                <!-- Profile -->
                <a href="/profil" class="flex items-center gap-2 pl-2">
                    @if(auth()->user()->foto_profil)
                        <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" class="w-8 h-8 rounded-full object-cover border-2 border-gray-200">
                    @else
                        <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white text-xs font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    @endif
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 py-5">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @yield('content')
    </main>

    <!-- Bottom Navigation (Mobile - Grab style) -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 md:hidden">
        <div class="flex items-center justify-around py-2">
            <a href="/" class="flex flex-col items-center gap-0.5 px-3 py-1 {{ request()->is('/') ? 'text-emerald-600' : 'text-gray-400' }}">
                <svg class="w-5 h-5" fill="{{ request()->is('/') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-[10px] font-medium">Home</span>
            </a>
            <a href="/riwayat" class="flex flex-col items-center gap-0.5 px-3 py-1 {{ request()->is('riwayat*') ? 'text-emerald-600' : 'text-gray-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="text-[10px] font-medium">Activity</span>
            </a>
            <a href="/chat" class="flex flex-col items-center gap-0.5 px-3 py-1 relative {{ request()->is('*chat*') ? 'text-emerald-600' : 'text-gray-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span class="text-[10px] font-medium">Chat</span>
                <span id="nav-chat-badge-mobile" class="hidden absolute top-0 right-1 w-4 h-4 bg-red-500 text-white text-[8px] font-bold rounded-full flex items-center justify-center"></span>
            </a>
            <a href="/profil" class="flex flex-col items-center gap-0.5 px-3 py-1 {{ request()->is('profil*') ? 'text-emerald-600' : 'text-gray-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[10px] font-medium">Account</span>
            </a>
        </div>
    </nav>

    <script>
    function checkChatUnread() {
        fetch('/chat/unread').then(r=>r.json()).then(d=>{
            const badge = document.getElementById('nav-chat-badge');
            const badgeMobile = document.getElementById('nav-chat-badge-mobile');
            if(d.count > 0) {
                if(badge) { badge.textContent = d.count; badge.classList.remove('hidden'); }
                if(badgeMobile) { badgeMobile.textContent = d.count; badgeMobile.classList.remove('hidden'); }
            } else {
                if(badge) badge.classList.add('hidden');
                if(badgeMobile) badgeMobile.classList.add('hidden');
            }
        }).catch(()=>{});
    }
    checkChatUnread();
    setInterval(checkChatUnread, 5000);
    </script>
    @stack('scripts')
</body>
</html>
