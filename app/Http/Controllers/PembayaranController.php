<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\PembayaranMitra;
use App\Services\DokuService;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    /**
     * Halaman pembayaran pendaftaran mitra
     */
    public function pendaftaran()
    {
        $mitra = auth()->user()->mitra;

        // Cek apakah ada pembayaran pending
        $pendingPayment = PembayaranMitra::where('mitra_id', $mitra->id)
            ->where('jenis', 'pendaftaran')
            ->where('status', 'pending')
            ->latest()
            ->first();

        // Cek apakah ada pembayaran gagal (dan belum ada yang lunas)
        $gagalPayment = PembayaranMitra::where('mitra_id', $mitra->id)
            ->where('jenis', 'pendaftaran')
            ->where('status', 'gagal')
            ->latest()
            ->first();

        return view('mitra.pembayaran', compact('mitra', 'pendingPayment', 'gagalPayment'));
    }

    /**
     * Proses pembayaran via DOKU
     */
    public function proses(DokuService $doku)
    {
        $user = auth()->user();
        $mitra = $user->mitra;

        // Buat record pembayaran
        $pembayaran = PembayaranMitra::create([
            'mitra_id' => $mitra->id,
            'jenis' => 'pendaftaran',
            'jumlah' => 250000,
            'status' => 'pending',
        ]);

        // Request ke DOKU
        $result = $doku->createPayment([
            'amount' => 250000,
            'customer_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone,
        ]);

        if ($result['success'] && $result['payment_url']) {
            // Simpan invoice number
            $pembayaran->update([
                'invoice_number' => $result['invoice_number'],
            ]);

            return redirect($result['payment_url']);
        }

        // Jika gagal, redirect ke halaman pembayaran dengan error
        $pembayaran->update(['status' => 'gagal']);

        return back()->with('error', 'Gagal membuat pembayaran. Silakan coba lagi.');
    }

    /**
     * Callback dari DOKU setelah user selesai bayar
     */
    public function callback(Request $request, DokuService $doku)
    {
        if (!auth()->check() || auth()->user()->role !== 'mitra') {
            return redirect('/login');
        }

        $mitra = auth()->user()->mitra;

        // Ambil pembayaran pending terakhir
        $pembayaran = PembayaranMitra::where('mitra_id', $mitra->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($pembayaran && $pembayaran->invoice_number) {
            // Cek status ke DOKU
            $result = $doku->checkStatus($pembayaran->invoice_number);

            if ($result['success']) {
                $data = $result['data'];

                $transactionStatus = $data['transaction']['status'] ?? '';
                $paymentMethod = $data['acquirer']['name']
                    ?? $data['acquirer']['id']
                    ?? $data['channel']['id']
                    ?? null;

                if (strtoupper($transactionStatus) === 'SUCCESS') {
                    $pembayaran->update([
                        'status' => 'lunas',
                        'metode_pembayaran' => $paymentMethod,
                        'periode_mulai' => now(),
                        'periode_selesai' => now()->addMonth(),
                    ]);

                    // Perpanjang subscription mitra
                    if ($pembayaran->jenis === 'langganan') {
                        $mitraRecord = $pembayaran->mitra;
                        $currentEnd = $mitraRecord->subscription_sampai && $mitraRecord->subscription_sampai >= now()
                            ? $mitraRecord->subscription_sampai
                            : now();
                        $mitraRecord->update(['subscription_sampai' => $currentEnd->copy()->addMonth()]);
                    }
                } elseif (in_array(strtoupper($transactionStatus), ['FAILED', 'EXPIRED', 'VOIDED'])) {
                    $pembayaran->update([
                        'status' => 'gagal',
                        'metode_pembayaran' => $paymentMethod,
                    ]);
                }
            }
        }

        return redirect($pembayaran && $pembayaran->jenis === 'langganan' ? '/mitra/langganan' : '/mitra/pembayaran');
    }

    /**
     * Notification endpoint dari DOKU (server-to-server)
     */
    public function notify(Request $request, DokuService $doku)
    {
        $body = $request->getContent();

        // Verifikasi signature dari DOKU
        $clientId = $request->header('Client-Id');
        $requestId = $request->header('Request-Id');
        $requestTimestamp = $request->header('Request-Timestamp');
        $signatureHeader = $request->header('Signature');

        // Validasi header wajib ada
        if (!$clientId || !$requestId || !$requestTimestamp || !$signatureHeader) {
            return response()->json(['message' => 'Missing headers'], 401);
        }

        // Validasi Client-Id harus sama dengan milik kita
        if ($clientId !== config('doku.client_id')) {
            return response()->json(['message' => 'Invalid client'], 401);
        }

        // Verifikasi signature
        $signature = str_replace('HMACSHA256=', '', $signatureHeader);
        if (!$doku->verifyNotification($requestId, $requestTimestamp, $signature, $body)) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $data = json_decode($body, true);

        // Cari pembayaran berdasarkan invoice
        $invoiceNumber = $data['order']['invoice_number'] ?? null;
        if (!$invoiceNumber) {
            return response()->json(['message' => 'Invalid'], 400);
        }

        $pembayaran = PembayaranMitra::where('invoice_number', $invoiceNumber)->first();
        if (!$pembayaran) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // Cek status transaksi dari DOKU
        $transactionStatus = $data['transaction']['status'] ?? '';
        $paymentMethod = $data['acquirer']['name']
            ?? $data['acquirer']['id']
            ?? $data['channel']['id']
            ?? null;

        if ($transactionStatus === 'SUCCESS') {
            $pembayaran->update([
                'status' => 'lunas',
                'metode_pembayaran' => $paymentMethod,
                'periode_mulai' => now(),
                'periode_selesai' => now()->addMonth(),
            ]);
        } elseif (in_array($transactionStatus, ['FAILED', 'EXPIRED'])) {
            $pembayaran->update([
                'status' => 'gagal',
                'metode_pembayaran' => $paymentMethod,
            ]);
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * Halaman langganan mitra
     */
    public function halamanLangganan()
    {
        $mitra = auth()->user()->mitra;
        $riwayatLangganan = PembayaranMitra::where('mitra_id', $mitra->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('mitra.langganan', compact('mitra', 'riwayatLangganan'));
    }

    /**
     * Langganan bulanan (Rp 150.000)
     */
    public function langganan(DokuService $doku)
    {
        $user = auth()->user();
        $mitra = $user->mitra;

        $pembayaran = PembayaranMitra::create([
            'mitra_id' => $mitra->id,
            'jenis' => 'langganan',
            'jumlah' => 150000,
            'status' => 'pending',
        ]);

        $result = $doku->createPayment([
            'amount' => 150000,
            'customer_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone,
        ]);

        if ($result['success'] && $result['payment_url']) {
            $pembayaran->update(['invoice_number' => $result['invoice_number']]);
            return redirect($result['payment_url']);
        }

        $pembayaran->update(['status' => 'gagal']);
        return back()->with('error', 'Gagal membuat pembayaran. Silakan coba lagi.');
    }
}
