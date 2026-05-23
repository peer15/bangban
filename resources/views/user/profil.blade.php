@extends('layouts.user')
@section('title', 'Profil')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl md:text-3xl font-bold text-slate-900">Pengaturan Profil</h1>
    <p class="text-sm text-slate-500 mt-1">Kelola informasi akun dan preferensi kamu</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Sidebar Profil -->
    <div class="md:col-span-1 space-y-4">
        <!-- Profile Card -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 h-24"></div>
            <div class="px-6 pb-6 -mt-12">
                <div class="w-24 h-24 rounded-2xl bg-white p-1.5 shadow-lg">
                    <div class="w-full h-full rounded-xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-3xl font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mt-4">{{ auth()->user()->name }}</h3>
                <p class="text-sm text-slate-500 truncate">{{ auth()->user()->email }}</p>
                <div class="mt-3 inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-medium rounded-full">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                    Akun Aktif
                </div>
                <p class="text-xs text-slate-400 mt-3">Bergabung sejak {{ auth()->user()->created_at->format('M Y') }}</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-2">
            <a href="/riwayat" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition group">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-lg group-hover:scale-110 transition">📋</div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-800">Riwayat Pesanan</p>
                    <p class="text-xs text-slate-500">Lihat semua pesanan kamu</p>
                </div>
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <a href="/sos" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition group">
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-lg group-hover:scale-110 transition">🆘</div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-800">SOS Darurat</p>
                    <p class="text-xs text-slate-500">Bantuan darurat 24 jam</p>
                </div>
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="flex items-center gap-3 p-3 rounded-xl hover:bg-red-50 transition w-full text-left group">
                    <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-lg group-hover:scale-110 transition">🚪</div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-red-600">Keluar Akun</p>
                        <p class="text-xs text-red-400">Logout dari aplikasi</p>
                    </div>
                </button>
            </form>
        </div>
    </div>

    <!-- Form -->
    <div class="md:col-span-2">
        <form method="POST" action="/profil" class="space-y-6">
            @csrf @method('PUT')

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-4">
                    <p class="font-semibold mb-1">Terjadi kesalahan:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Info Pribadi -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 md:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="font-bold text-slate-900 text-lg">Informasi Pribadi</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Update data profil kamu</p>
                    </div>
                    <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-300 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-300 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">No. HP / WhatsApp</label>
                        <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" placeholder="08xxxxxxxxxx"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-300 transition">
                    </div>
                </div>
            </div>

            <!-- Password -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 md:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="font-bold text-slate-900 text-lg">Keamanan</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Ubah password akun kamu (opsional)</p>
                    </div>
                    <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Password Baru</label>
                        <input type="password" name="password" placeholder="Min. 6 karakter"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-300 transition">
                        <p class="text-xs text-slate-400 mt-1.5">Kosongkan jika tidak ingin mengubah password</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-300 transition">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-3.5 rounded-xl shadow-lg shadow-slate-900/10 transition">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection
