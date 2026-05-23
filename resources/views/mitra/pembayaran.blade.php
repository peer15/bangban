@extends('layouts.mitra')
@section('title', 'Pembayaran Pendaftaran')

@section('content')
<div class="max-w-2xl mx-auto">
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-3 mb-4">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-700 text-sm rounded-xl p-3 mb-4">{{ session('info') }}</div>
    @endif

    @php
        $sudahBayar = \App\Models\PembayaranMitra::where('mitra_id', $mitra->id)->where('jenis', 'pendaftaran')->where('status', 'lunas')->exists();
    @endphp

    @if($sudahBayar)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">✅</div>
            <h2 class="text-xl font-bold text-gray-900">Pembayaran Berhasil!</h2>
            <p class="text-sm text-gray-500 mt-1">Pembayaran pendaftaran kamu sudah lunas</p>
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mt-4">
                <p class="text-sm font-medium text-emerald-800">Rp 250.000 - Lunas</p>
                <p class="text-xs text-emerald-600">Menunggu verifikasi admin</p>
            </div>
            <a href="/mitra" class="inline-block mt-6 bg-red-600 hover:bg-red-700 text-white font-semibold px-8 py-3 rounded-xl transition">Kembali ke Dashboard</a>
        </div>
    @elseif($pendingPayment)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">⏳</div>
            <h2 class="text-xl font-bold text-gray-900">Menunggu Pembayaran</h2>
            <p class="text-sm text-gray-500 mt-1">Pembayaran kamu sedang diproses</p>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mt-4 text-left">
                <div class="text-sm text-amber-700 space-y-1">
                    <p>Invoice: {{ $pendingPayment->invoice_number ?? '-' }}</p>
                    <p>Jumlah: Rp 250.000</p>
                    <p>Dibuat: {{ $pendingPayment->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-4">Status akan diperbarui otomatis setelah pembayaran</p>
        </div>
    @elseif($gagalPayment)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">❌</div>
            <h2 class="text-xl font-bold text-gray-900">Pembayaran Gagal</h2>
            <p class="text-sm text-gray-500 mt-1">Pembayaran sebelumnya tidak berhasil</p>
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mt-4 text-left text-sm text-red-700">
                <p>Invoice: {{ $gagalPayment->invoice_number ?? '-' }}</p>
                <p>Tanggal: {{ $gagalPayment->created_at->format('d M Y, H:i') }}</p>
            </div>
            <form method="POST" action="/mitra/pembayaran/proses" class="mt-6">
                @csrf
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-xl transition">Bayar Ulang Rp 250.000</button>
            </form>
        </div>
    @else
        <div class="mb-6 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">💳</div>
            <h1 class="text-xl font-bold text-gray-900">Pembayaran Pendaftaran Mitra</h1>
            <p class="text-sm text-gray-500 mt-1">Selesaikan pembayaran untuk mengaktifkan akun</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-4">
            <h3 class="font-semibold text-gray-900 mb-3">Rincian Biaya</h3>
            <div class="flex justify-between py-2 border-b border-gray-50 text-sm"><span class="text-gray-600">Biaya Pendaftaran</span><span class="font-medium">Rp 250.000</span></div>
            <div class="flex justify-between py-2 border-b border-gray-50 text-sm"><span class="text-gray-600">Termasuk</span><span class="text-gray-600">Starter Kit + 1 Bulan</span></div>
            <div class="flex justify-between py-2 mt-1 text-sm"><span class="font-semibold text-gray-900">Total</span><span class="text-lg font-bold text-red-600">Rp 250.000</span></div>
        </div>

        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 mb-4">
            <h3 class="font-semibold text-emerald-800 text-sm mb-2">Yang kamu dapatkan:</h3>
            <ul class="text-sm text-emerald-700 space-y-1.5">
                <li>✓ Starter kit alat tambal ban portabel</li>
                <li>✓ Akses sistem & database pelanggan (1 bulan)</li>
                <li>✓ Profil mitra terverifikasi</li>
                <li>✓ GPS & pemetaan digital</li>
                <li>✓ Atribut resmi BANGBAN</li>
            </ul>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-4">
            <h3 class="font-semibold text-gray-900 mb-3">Metode Pembayaran</h3>
            <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-xl border border-blue-200">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center border border-gray-200 text-xs font-bold text-blue-600">DOKU</div>
                <div>
                    <p class="text-sm font-medium text-gray-800">DOKU Payment</p>
                    <p class="text-xs text-gray-500">Transfer Bank, E-Wallet, QRIS</p>
                </div>
            </div>
        </div>

        <form method="POST" action="/mitra/pembayaran/proses">
            @csrf
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3.5 rounded-xl transition shadow-sm">Bayar Rp 250.000</button>
        </form>
        <p class="text-xs text-gray-400 text-center mt-3">Kamu akan diarahkan ke halaman pembayaran DOKU</p>
    @endif
</div>
@endsection
