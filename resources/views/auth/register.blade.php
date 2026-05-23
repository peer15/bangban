<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - BANGBAN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <img src="/logo.jpg" alt="BANGBAN" class="h-14 w-14 object-cover mx-auto mb-3 rounded-xl">
                <h1 class="text-2xl font-bold text-slate-900">Buat Akun Baru</h1>
                <p class="text-slate-500 text-sm mt-1">Daftar untuk mulai menggunakan BANGBAN</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-4 mb-4">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/register" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 md:p-8 space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-300 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-300 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">No. HP</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-300 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-300 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-300 transition">
                </div>
                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-3 rounded-xl shadow-lg shadow-slate-900/10 transition">
                    Daftar Sekarang
                </button>
            </form>

            <p class="text-center text-sm text-slate-500 mt-4">Sudah punya akun? <a href="/login" class="text-orange-600 font-semibold hover:text-orange-700">Masuk</a></p>
        </div>
    </div>
</body>
</html>
