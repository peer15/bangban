<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin BANGBAN - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen font-sans antialiased">
    <!-- Top Nav -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 md:px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('admin-sidebar').classList.toggle('-translate-x-full')" class="md:hidden p-2 rounded-lg hover:bg-slate-100">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a href="/admin" class="flex items-center gap-2.5">
                    <img src="/logo.jpg" alt="BANGBAN" class="h-8 w-8 object-cover rounded-lg">
                    <span class="text-lg font-bold text-slate-900">BANGBAN</span>
                    <span class="hidden md:inline text-xs font-medium text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">Admin</span>
                </a>
            </div>
            <div class="flex items-center gap-3">
                @if(auth()->user()->foto_profil)
                    <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" class="w-8 h-8 rounded-full object-cover">
                @else
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center text-white text-xs font-semibold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <span class="hidden md:inline text-sm text-slate-600">{{ auth()->user()->name }}</span>
                <form method="POST" action="/logout">
                    @csrf
                    <button class="text-xs text-slate-400 hover:text-slate-700 transition">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 md:px-6 py-6 md:flex md:gap-6">
        <!-- Sidebar -->
        <aside id="admin-sidebar" class="fixed md:static inset-y-0 left-0 z-40 w-64 bg-white md:bg-transparent transform -translate-x-full md:translate-x-0 transition-transform md:w-56 md:shrink-0">
            <div class="h-full md:h-auto overflow-y-auto p-4 md:p-0">
                <div class="md:sticky md:top-20 bg-white rounded-2xl border border-slate-100 shadow-sm p-3">
                    <nav class="space-y-1">
                        @php
                            $navItems = [
                                ['url' => '/admin', 'match' => 'admin', 'icon' => '📊', 'label' => 'Dashboard', 'exact' => true],
                                ['url' => '/admin/mitra', 'match' => 'admin/mitra', 'icon' => '👨‍🔧', 'label' => 'Mitra Aktif', 'exact' => true],
                                ['url' => '/admin/mitra/pending', 'match' => 'admin/mitra/pending', 'icon' => '⏳', 'label' => 'Mitra Pending', 'exact' => true],
                                ['url' => '/admin/pesanan', 'match' => 'admin/pesanan*', 'icon' => '📋', 'label' => 'Pesanan'],
                                ['url' => '/admin/user', 'match' => 'admin/user*', 'icon' => '👥', 'label' => 'Pengguna'],
                                ['url' => '/admin/pembayaran', 'match' => 'admin/pembayaran*', 'icon' => '💰', 'label' => 'Pembayaran'],
                                ['url' => '/admin/pencairan', 'match' => 'admin/pencairan*', 'icon' => '💸', 'label' => 'Pencairan'],
                                ['url' => '/admin/peta', 'match' => 'admin/peta*', 'icon' => '🗺️', 'label' => 'Peta Mitra'],
                            ];
                        @endphp
                        @foreach($navItems as $item)
                            @php $active = isset($item['exact']) && $item['exact'] ? request()->is($item['match']) : request()->is($item['match']); @endphp
                            <a href="{{ $item['url'] }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition {{ $active ? 'bg-orange-50 text-orange-700 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                                <span class="text-base">{{ $item['icon'] }}</span>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </div>
        </aside>

        <!-- Content -->
        <main class="flex-1 min-w-0">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3 mb-6 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
