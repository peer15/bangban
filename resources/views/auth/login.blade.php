<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - BANGBAN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Left Panel (Desktop) -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-slate-900 via-slate-800 to-orange-900 relative overflow-hidden items-center justify-center p-12">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 30% 40%, rgba(251,146,60,0.5) 0%, transparent 50%), radial-gradient(circle at 70% 80%, rgba(251,146,60,0.3) 0%, transparent 50%);"></div>
            <div class="relative z-10 text-center">
                <img src="/logo.jpg" alt="BANGBAN" class="h-24 w-24 object-cover mx-auto mb-6 rounded-2xl shadow-2xl">
                <h2 class="text-4xl font-bold text-white tracking-tight">BANGBAN</h2>
                <p class="text-slate-300 mt-3 text-lg">Tambal Ban Online Jepara</p>
                <div class="mt-8 space-y-3 text-left max-w-xs mx-auto">
                    <div class="flex items-center gap-3 text-slate-300">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-sm">🔧</div>
                        <span class="text-sm">Teknisi datang ke lokasi kamu</span>
                    </div>
                    <div class="flex items-center gap-3 text-slate-300">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-sm">📍</div>
                        <span class="text-sm">GPS tracking real-time</span>
                    </div>
                    <div class="flex items-center gap-3 text-slate-300">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-sm">💳</div>
                        <span class="text-sm">Harga transparan, bayar mudah</span>
                    </div>
                </div>
            </div>
            <div class="absolute -bottom-20 -right-20 text-[20rem] opacity-5 select-none">🛞</div>
        </div>

        <!-- Right Panel (Form) -->
        <div class="flex-1 flex items-center justify-center p-6 md:p-12">
            <div class="w-full max-w-md">
                <div class="lg:hidden text-center mb-8">
                    <img src="/logo.jpg" alt="BANGBAN" class="h-14 w-14 object-cover mx-auto mb-3 rounded-xl">
                    <h1 class="text-2xl font-bold text-slate-900">BANGBAN</h1>
                    <p class="text-slate-500 text-sm">Tambal Ban Online Jepara</p>
                </div>

                <div class="lg:mb-8">
                    <h2 class="text-2xl font-bold text-slate-900">Masuk ke akun</h2>
                    <p class="text-slate-500 text-sm mt-1">Selamat datang kembali! Masukkan data kamu.</p>
                </div>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-4 mt-4 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="/login" class="mt-6 space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-300 transition"
                            placeholder="nama@email.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                        <input type="password" name="password" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-300 transition"
                            placeholder="••••••••">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-orange-500 focus:ring-orange-300">
                        Ingat saya
                    </label>
                    <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-3 rounded-xl shadow-lg shadow-slate-900/10 transition">
                        Masuk
                    </button>
                </form>

                <div class="mt-6 text-center space-y-2">
                    <p class="text-sm text-slate-500">Belum punya akun? <a href="/register" class="text-orange-600 font-semibold hover:text-orange-700">Daftar</a></p>
                    <p class="text-sm text-slate-500">Ingin jadi mitra? <a href="/daftar-mitra" class="text-emerald-600 font-semibold hover:text-emerald-700">Daftar Mitra</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
