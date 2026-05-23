<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mitra BANGBAN - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @stack('head')
</head>
<body class="bg-[#f8f9fa] min-h-screen font-sans antialiased">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside id="mitra-sidebar" class="fixed inset-y-0 left-0 z-40 w-[220px] bg-white border-r border-gray-200/60 transform -translate-x-full md:translate-x-0 md:static transition-transform duration-200 flex flex-col">
        <!-- Brand -->
        <div class="flex items-center gap-2.5 px-5 pt-5 pb-5">
            <img src="/logo.jpg" alt="Bangban" class="w-9 h-9 rounded-[10px] object-cover">
            <div>
                <p class="text-[15px] font-medium text-gray-900">Bangban</p>
                <p class="text-[11px] font-medium text-[#1d9e75]">Portal Mitra</p>
            </div>
        </div>

        <!-- Menu Utama -->
        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider px-5 mb-2">Menu utama</p>
        <nav class="px-2 space-y-0.5">
            <a href="/mitra" class="flex items-center gap-2.5 px-3 py-2 text-[13px] rounded-lg border-l-2 transition {{ request()->is('mitra') ? 'bg-[#e1f5ee] text-[#0f6e56] font-medium border-[#1d9e75]' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="ti ti-layout-dashboard text-base"></i> Dashboard
            </a>
            <a href="/mitra/pesanan" class="flex items-center gap-2.5 px-3 py-2 text-[13px] rounded-lg border-l-2 transition {{ request()->is('mitra/pesanan*') ? 'bg-[#e1f5ee] text-[#0f6e56] font-medium border-[#1d9e75]' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="ti ti-package text-base"></i> Pesanan
            </a>
            <a href="/mitra/saldo" class="flex items-center gap-2.5 px-3 py-2 text-[13px] rounded-lg border-l-2 transition {{ request()->is('mitra/saldo*') ? 'bg-[#e1f5ee] text-[#0f6e56] font-medium border-[#1d9e75]' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="ti ti-wallet text-base"></i> Saldo
            </a>
            <a href="/mitra/riwayat" class="flex items-center gap-2.5 px-3 py-2 text-[13px] rounded-lg border-l-2 transition {{ request()->is('mitra/riwayat*') ? 'bg-[#e1f5ee] text-[#0f6e56] font-medium border-[#1d9e75]' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="ti ti-receipt text-base"></i> Riwayat
            </a>
        </nav>

        <!-- Akun -->
        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider px-5 mt-5 mb-2">Akun</p>
        <nav class="px-2 space-y-0.5">
            <a href="/mitra/langganan" class="flex items-center gap-2.5 px-3 py-2 text-[13px] rounded-lg border-l-2 transition {{ request()->is('mitra/langganan*') ? 'bg-[#e1f5ee] text-[#0f6e56] font-medium border-[#1d9e75]' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="ti ti-diamond text-base"></i> Langganan
            </a>
            <a href="/mitra/profil" class="flex items-center gap-2.5 px-3 py-2 text-[13px] rounded-lg border-l-2 transition {{ request()->is('mitra/profil*') ? 'bg-[#e1f5ee] text-[#0f6e56] font-medium border-[#1d9e75]' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="ti ti-user-circle text-base"></i> Profil
            </a>
            <a href="/mitra/chat" class="flex items-center gap-2.5 px-3 py-2 text-[13px] rounded-lg border-l-2 transition {{ request()->is('mitra/chat*') || request()->is('mitra/pesanan/*/chat') ? 'bg-[#e1f5ee] text-[#0f6e56] font-medium border-[#1d9e75]' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="ti ti-message-circle text-base"></i> Chat
                <span id="sidebar-chat-badge" class="hidden ml-auto w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center"></span>
            </a>
        </nav>

        <!-- Footer User -->
        <div class="mt-auto px-5 py-4 border-t border-gray-100">
            <div class="flex items-center gap-2.5">
                @if(auth()->user()->foto_profil)
                    <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" class="w-8 h-8 rounded-full object-cover">
                @else
                    <div class="w-8 h-8 rounded-full bg-[#e1f5ee] flex items-center justify-center text-[12px] font-medium text-[#0f6e56]">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-gray-400">Mitra aktif</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
        <!-- Topbar -->
        <header class="flex items-center justify-between px-6 h-[52px] bg-white border-b border-gray-200/60 sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button onclick="document.getElementById('mitra-sidebar').classList.toggle('-translate-x-full')" class="md:hidden p-1.5 rounded-lg hover:bg-gray-100">
                    <i class="ti ti-menu-2 text-gray-600 text-lg"></i>
                </button>
                <p class="text-sm text-gray-500">{{ now()->locale('id')->isoFormat('dddd') }}, <span class="text-gray-900 font-medium">{{ now()->format('d M Y') }}</span></p>
            </div>
            <div class="flex items-center gap-2">
                @if(isset($mitra) && $mitra && $mitra->status === 'aktif')
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#e1f5ee] text-[12px] font-medium text-[#0f6e56]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#1d9e75] {{ ($mitra->is_open ?? false) ? 'animate-pulse' : '' }}"></span>
                    {{ ($mitra->is_open ?? false) ? 'Toko buka' : 'Toko tutup' }}
                </div>
                @endif
                <a href="/mitra/chat" class="relative w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition" id="mitra-chat-link">
                    <i class="ti ti-message-circle text-gray-500 text-base"></i>
                    <span id="mitra-chat-badge" class="hidden absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center"></span>
                </a>
                <button class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition">
                    <i class="ti ti-bell text-gray-500 text-base"></i>
                </button>
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition">
                        <i class="ti ti-logout text-gray-500 text-base"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 p-6 overflow-y-auto">
            @if(session('success'))
                <div class="bg-[#e1f5ee] border border-[#1d9e75]/20 text-[#0f6e56] text-sm rounded-lg px-4 py-3 mb-5 flex items-center gap-2">
                    <i class="ti ti-check text-base"></i>
                    {{ session('success') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

<div id="sidebar-overlay" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden" onclick="document.getElementById('mitra-sidebar').classList.add('-translate-x-full');this.classList.add('hidden')"></div>

<!-- Incoming Order Popup -->
<div id="order-popup" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden animate-[slideUp_0.3s_ease-out]">
            <!-- Header -->
            <div class="bg-[#1a5c3a] px-5 py-4 text-white text-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full border-2 border-white/30"></div>
                    <div class="absolute -left-4 -bottom-4 w-20 h-20 rounded-full border-2 border-white/20"></div>
                </div>
                <div class="relative">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="ti ti-bell-ringing text-2xl animate-[ring_1s_ease-in-out_infinite]"></i>
                    </div>
                    <p class="text-lg font-semibold">Pesanan Masuk!</p>
                    <p class="text-sm text-white/70 mt-0.5">Ada pelanggan yang butuh bantuan</p>
                </div>
            </div>
            <!-- Body -->
            <div class="p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-11 h-11 bg-[#e1f5ee] rounded-xl flex items-center justify-center">
                        <i class="ti ti-user text-[#0f6e56] text-xl"></i>
                    </div>
                    <div>
                        <p id="popup-user" class="text-sm font-medium text-gray-900">-</p>
                        <p id="popup-time" class="text-xs text-gray-400">-</p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 space-y-2.5 mb-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Layanan</span>
                        <span id="popup-layanan" class="text-sm font-medium text-gray-900">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Biaya</span>
                        <span id="popup-biaya" class="text-sm font-semibold text-[#0f6e56]">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Pembayaran</span>
                        <span id="popup-bayar" class="text-xs font-medium px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">-</span>
                    </div>
                    <div id="popup-jarak-row" class="flex items-center justify-between hidden">
                        <span class="text-xs text-gray-500">Jarak</span>
                        <span id="popup-jarak" class="text-sm font-medium text-gray-900">-</span>
                    </div>
                    <div id="popup-catatan-row" class="hidden">
                        <span class="text-xs text-gray-500">Catatan</span>
                        <p id="popup-catatan" class="text-xs text-gray-700 mt-0.5">-</p>
                    </div>
                </div>
                <!-- Countdown -->
                <div class="flex items-center justify-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-full border-2 border-[#1d9e75] flex items-center justify-center">
                        <span id="popup-countdown" class="text-sm font-bold text-[#0f6e56]">30</span>
                    </div>
                    <span class="text-xs text-gray-500">detik untuk merespon</span>
                </div>
                <!-- Actions -->
                <div class="flex gap-3">
                    <button onclick="dismissPopup()" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                        Lewati
                    </button>
                    <form id="popup-accept-form" method="POST" action="" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-3 rounded-xl bg-[#1a5c3a] text-white text-sm font-semibold hover:bg-[#155230] transition flex items-center justify-center gap-2">
                            <i class="ti ti-check"></i> Terima
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
@keyframes ring { 0%,100% { transform: rotate(0); } 25% { transform: rotate(15deg); } 75% { transform: rotate(-15deg); } }
</style>

<script>
const sb=document.getElementById('mitra-sidebar'),ov=document.getElementById('sidebar-overlay');
new MutationObserver(()=>{if(!sb.classList.contains('-translate-x-full')&&window.innerWidth<768)ov.classList.remove('hidden');else ov.classList.add('hidden')}).observe(sb,{attributes:true,attributeFilter:['class']});

// Chat unread
function checkMitraChatUnread(){
    fetch('/mitra/chat/unread').then(r=>r.json()).then(d=>{
        const b1=document.getElementById('mitra-chat-badge');
        const b2=document.getElementById('sidebar-chat-badge');
        if(d.count>0){
            if(b1){b1.textContent=d.count;b1.classList.remove('hidden');}
            if(b2){b2.textContent=d.count;b2.classList.remove('hidden');}
        } else {
            if(b1)b1.classList.add('hidden');
            if(b2)b2.classList.add('hidden');
        }
    }).catch(()=>{});
}
checkMitraChatUnread();
setInterval(checkMitraChatUnread,5000);

// Incoming order popup
let popupShown = false;
let popupDismissedId = null;
let countdownInterval = null;

function checkIncomingOrder() {
    if (popupShown) return;
    fetch('/mitra/pesanan/incoming').then(r=>r.json()).then(d=>{
        if (d.has_order && d.pesanan.id !== popupDismissedId) {
            showOrderPopup(d.pesanan);
        }
    }).catch(()=>{});
}

function showOrderPopup(pesanan) {
    popupShown = true;
    const popup = document.getElementById('order-popup');
    document.getElementById('popup-user').textContent = pesanan.user_name;
    document.getElementById('popup-time').textContent = pesanan.created_at;
    document.getElementById('popup-layanan').textContent = pesanan.nama_layanan;
    document.getElementById('popup-biaya').textContent = 'Rp ' + Number(pesanan.total_biaya).toLocaleString('id-ID');
    document.getElementById('popup-bayar').textContent = pesanan.pembayaran === 'tunai' ? 'COD Tunai' : 'Online';
    document.getElementById('popup-bayar').className = pesanan.pembayaran === 'tunai'
        ? 'text-xs font-medium px-2 py-0.5 rounded-full bg-green-100 text-green-700'
        : 'text-xs font-medium px-2 py-0.5 rounded-full bg-blue-100 text-blue-700';

    if (pesanan.jarak_km) {
        document.getElementById('popup-jarak').textContent = pesanan.jarak_km + ' km';
        document.getElementById('popup-jarak-row').classList.remove('hidden');
    } else {
        document.getElementById('popup-jarak-row').classList.add('hidden');
    }

    if (pesanan.catatan_lokasi) {
        document.getElementById('popup-catatan').textContent = pesanan.catatan_lokasi;
        document.getElementById('popup-catatan-row').classList.remove('hidden');
    } else {
        document.getElementById('popup-catatan-row').classList.add('hidden');
    }

    document.getElementById('popup-accept-form').action = '/mitra/pesanan/' + pesanan.id + '/terima';
    popup.classList.remove('hidden');

    // Play sound
    try { new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH2JkZeXk4x/cGRcWF5ncHyGjpOTj4Z8cGZfXGBodH+Ij5OTj4V7b2VeXF9oc3+IjpKSjoR7b2ReW19ncn6Hjo+Pj4R7bmNdW19ncX2GjI6OjYN6bWJcWl5mcHyFi42NjIN5bGFbWV1lb3uEioyMi4J4a2BaWFxkbnqDiYuLioF3al9ZV1tjbXmCh4qKiYB2aV5YVlpibHiAhoiIh391aF1XVVlhaniAhYeHhn50Z1xWVFhga3d/hIaGhX1zZltVU1dfaXZ+g4WFhHxyZVpUUlZeaHV9gYODg3twZFhTUVVdZ3R8gIKCgnpvY1dSUFRcZnN7f4GBgXluYlZRUFNbZXJ6foCAf3htYVVQT1JaZHF5fX9/fndsYFRPTlFZY3B4fH5+fXZrX1NOTVBYYm94e31+fXVqXlNNTFBXYW53ent9fHRpXVJMTE9WYG12ent8e3NoXFFLS05VX2x1eXp7e3JnW1BKSkxUXmt0eHl6eXFmWk9JSUtTXWpzeHl5eHBkWU5IR0pSXGlyd3h4d29jV0xGRklRW2hxdnd3dm5iVktFRUhQWmdwdXZ2dW1hVUpEREdPWGZvdHV1dGxgU0lDQ0ZOV2VudHR0c2tfUkhCQkVNVmRtc3NzcmpeUEdBQURMVWNsc3JycWldT0ZAQENLVGJrcnFxcGhcTkU/P0JKU2FqcXBwb2dbTUQ+PkFJUmBpcG9vbmZaTEM9PUBJUV9ob25ubWVZS0I8PEBIU15nb21tbGRYSkE7Oz9HUF1mbWxsa2NYSUBbOz5GUFxlbGtramJXSEA6Oj5FT1tkamtqaWFWSEA5OT1ET1pja2ppaGBVR0A5OTxDTlliaWlpZ19UR0A4ODtCTVhhaGhoZl5TRj83NzpBTFdgZ2dnZV1SRT83NjlAS1ZfZmZmZFxRRD82NjhASVVeZWVlY1tQRD42NTc/SVReZGRkYlpPQz41NTY+SFNdY2NjYVlOQj01NDU9R1JcYmJiYFhNQT00NDQ8RlFbYWFhX1dMQDwzMzM7RVBaYGBgXlZLPzszMzI6RU9ZX19fXVVKPjoyMjE5RE5YXl5eXFRJPTkyMTE4Q01XX11dW1NIPTkxMTA3Qk1WXVxcWlJHPDgxMC82QUxVXFtbWVFGOzcwLy41QEtUW1paWFBFOjYvLi00P0pTWllZV09EOTUuLS0zPklSWVhYVk5DODQuLCwyPkhRWFdXVU1CODMtLCwxPUdQV1ZWVExBNzIsKysxPEZPVlVVU0tANjErKiowO0VOVVRUUko/NTAqKSkwOkRNVFNTUUk+NDApKCkvOUNMU1JSUEg9MzAoJygvOEJLUlFRTkc8Mi8nJicuN0FKUVBQTUc7MS4nJiYtNkBJUE9PTEUbMS4mJSUsNT9IT05OTEUbMC0lJCQsND5HT01NS0QaMC0lJCQrMz1GTUxMS0MaMCwkIyMrMjxFTEtLSkIZLywkIyIqMTtETEpKSUEZLiskIiIpMDpDS0lJSEAYLiskISEpLzlCSklIR0AYLiojISAoLjhBSUhHRj8XLSoiIB8nLTdASEZGRT4XLCkhHx8mLDY/R0VFRDwWLCkhHh4lKzU+RkREQzwWKyggHh4kKjQ9RURDRP8=').play(); } catch(e) {}

    // Countdown
    let seconds = 30;
    document.getElementById('popup-countdown').textContent = seconds;
    countdownInterval = setInterval(() => {
        seconds--;
        document.getElementById('popup-countdown').textContent = seconds;
        if (seconds <= 0) {
            dismissPopup();
        }
    }, 1000);
}

function dismissPopup() {
    const popup = document.getElementById('order-popup');
    popup.classList.add('hidden');
    popupShown = false;
    if (countdownInterval) clearInterval(countdownInterval);
    // Remember dismissed order to not show again immediately
    const userId = document.getElementById('popup-user').textContent;
    popupDismissedId = parseInt(document.getElementById('popup-accept-form').action.split('/').slice(-2)[0]);
    // Reset after 60 seconds (allow showing again if still available)
    setTimeout(() => { popupDismissedId = null; }, 60000);
}

checkIncomingOrder();
setInterval(checkIncomingOrder, 7000);
</script>
@stack('scripts')
</body>
</html>
