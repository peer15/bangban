<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Pendaftaran Mitra (publik)
Route::get('/daftar-mitra', [MitraController::class, 'showDaftar']);
Route::post('/daftar-mitra', [MitraController::class, 'daftar']);

// User Dashboard
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/', [UserController::class, 'home']);
    Route::get('/pesan', [UserController::class, 'pesan']);
    Route::post('/pesan', [UserController::class, 'konfirmasiPesanan']);
    Route::get('/pesanan/{pesanan}/tracking', [UserController::class, 'tracking']);
    Route::post('/pesanan/{pesanan}/cancel', [UserController::class, 'cancelPesanan']);
    Route::get('/riwayat', [UserController::class, 'riwayat']);
    Route::get('/pesanan/{pesanan}/rating', [UserController::class, 'showRating']);
    Route::post('/pesanan/{pesanan}/rating', [UserController::class, 'simpanRating']);
    Route::get('/sos', [UserController::class, 'sos']);
    Route::get('/profil', [UserController::class, 'profil']);
    Route::put('/profil', [UserController::class, 'updateProfil']);
    // Chat
    Route::get('/chat', [ChatController::class, 'userInbox']);
    Route::get('/pesanan/{pesanan}/chat', [ChatController::class, 'userChat']);
    Route::post('/pesanan/{pesanan}/chat', [ChatController::class, 'userSend']);
    Route::get('/pesanan/{pesanan}/chat/messages', [ChatController::class, 'getMessages']);
    Route::get('/pesanan/{pesanan}/chat/unread', [ChatController::class, 'unreadCount']);
    Route::get('/chat/unread', [ChatController::class, 'globalUnread']);
});

// Mitra Dashboard
Route::middleware(['auth', 'role:mitra'])->prefix('mitra')->group(function () {
    Route::get('/', [MitraController::class, 'dashboard']);
    Route::get('/pembayaran', [PembayaranController::class, 'pendaftaran']);
    Route::post('/pembayaran/proses', [PembayaranController::class, 'proses']);
    Route::get('/langganan', [PembayaranController::class, 'halamanLangganan']);
    Route::post('/langganan/bayar', [PembayaranController::class, 'langganan']);
    Route::get('/pesanan', [MitraController::class, 'pesananMasuk']);
    Route::post('/pesanan/{pesanan}/terima', [MitraController::class, 'terimaPesanan']);
    Route::patch('/pesanan/{pesanan}/status', [MitraController::class, 'updateStatus']);
    Route::post('/pesanan/{pesanan}/cancel', [MitraController::class, 'cancelPesanan']);
    Route::get('/saldo', [MitraController::class, 'saldo']);
    Route::post('/saldo/cairkan', [MitraController::class, 'cairkanSaldo']);
    Route::post('/toggle-open', [MitraController::class, 'toggleOpen']);
    Route::get('/riwayat', [MitraController::class, 'riwayat']);
    Route::get('/profil', [MitraController::class, 'profil']);
    Route::put('/profil', [MitraController::class, 'updateProfil']);
    Route::post('/profil/foto', [MitraController::class, 'updateFotoProfil']);
    // Chat
    Route::get('/chat', [ChatController::class, 'mitraInbox']);
    Route::get('/pesanan/{pesanan}/chat', [ChatController::class, 'mitraChat']);
    Route::post('/pesanan/{pesanan}/chat', [ChatController::class, 'mitraSend']);
    Route::get('/pesanan/{pesanan}/chat/messages', [ChatController::class, 'getMessages']);
    Route::get('/pesanan/{pesanan}/chat/unread', [ChatController::class, 'unreadCount']);
    Route::get('/chat/unread', [ChatController::class, 'globalUnread']);
    // Incoming order check (polling)
    Route::get('/pesanan/incoming', [MitraController::class, 'checkIncoming']);
});

// DOKU Callback & Notification (tanpa auth)
Route::get('/pembayaran/callback', [PembayaranController::class, 'callback']);
Route::post('/pembayaran/notify', [PembayaranController::class, 'notify']);

// Admin Dashboard
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);
    Route::get('/mitra', [AdminController::class, 'mitraList']);
    Route::get('/mitra/pending', [AdminController::class, 'mitraPending']);
    Route::post('/mitra/{mitra}/verifikasi', [AdminController::class, 'mitraVerifikasi']);
    Route::post('/mitra/{mitra}/nonaktif', [AdminController::class, 'mitraNonaktif']);
    Route::get('/pesanan', [AdminController::class, 'pesananList']);
    Route::get('/user', [AdminController::class, 'userList']);
    Route::get('/pembayaran', [AdminController::class, 'pembayaranList']);
    Route::get('/pencairan', [AdminController::class, 'pencairanList']);
    Route::post('/pencairan/{pencairan}/selesai', [AdminController::class, 'pencairanSelesai']);
    Route::get('/peta', [AdminController::class, 'petaMitra']);
});
