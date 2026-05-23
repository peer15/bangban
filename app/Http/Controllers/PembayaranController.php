<?php

namespace App\Http\Controllers;

use App\Models\PembayaranMitra;
use App\Services\DokuService;
use App\Services\PembayaranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function __construct(
        private readonly PembayaranService $pembayaranService,
        private readonly DokuService $dokuService,
    ) {}

    public function pendaftaran(): View
    {
        $mitra = auth()->user()->mitra;

        $pendingPayment = PembayaranMitra::where('mitra_id', $mitra->id)
            ->where('jenis', 'pendaftaran')
            ->where('status', 'pending')
            ->latest()
            ->first();

        $gagalPayment = PembayaranMitra::where('mitra_id', $mitra->id)
            ->where('jenis', 'pendaftaran')
            ->where('status', 'gagal')
            ->latest()
            ->first();

        return view('mitra.pembayaran', compact('mitra', 'pendingPayment', 'gagalPayment'));
    }

    public function proses(): RedirectResponse
    {
        $user = auth()->user();
        $mitra = $user->mitra;

        $result = $this->pembayaranService->buatPembayaran($user, $mitra, 'pendaftaran', 250000);

        if ($result['success']) {
            return redirect($result['payment_url']);
        }

        return back()->with('error', 'Gagal membuat pembayaran. Silakan coba lagi.');
    }

    public function callback(): RedirectResponse
    {
        if (!auth()->check() || auth()->user()->role !== 'mitra') {
            return redirect('/login');
        }

        $mitra = auth()->user()->mitra;

        $pembayaran = PembayaranMitra::where('mitra_id', $mitra->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($pembayaran) {
            $this->pembayaranService->prosesCallback($pembayaran);
        }

        $redirectPath = ($pembayaran?->jenis === 'langganan') ? '/mitra/langganan' : '/mitra/pembayaran';

        return redirect($redirectPath);
    }

    public function notify(Request $request): JsonResponse
    {
        $body = $request->getContent();

        $clientId = $request->header('Client-Id');
        $requestId = $request->header('Request-Id');
        $requestTimestamp = $request->header('Request-Timestamp');
        $signatureHeader = $request->header('Signature');

        if (!$clientId || !$requestId || !$requestTimestamp || !$signatureHeader) {
            return response()->json(['message' => 'Missing headers'], 401);
        }

        if ($clientId !== config('doku.client_id')) {
            return response()->json(['message' => 'Invalid client'], 401);
        }

        $signature = str_replace('HMACSHA256=', '', $signatureHeader);
        if (!$this->dokuService->verifyNotification($requestId, $requestTimestamp, $signature, $body)) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $data = json_decode($body, true);
        $invoiceNumber = $data['order']['invoice_number'] ?? null;

        if (!$invoiceNumber) {
            return response()->json(['message' => 'Invalid'], 400);
        }

        $pembayaran = PembayaranMitra::where('invoice_number', $invoiceNumber)->first();

        if (!$pembayaran) {
            return response()->json(['message' => 'Not found'], 404);
        }

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

    public function halamanLangganan(): View
    {
        $mitra = auth()->user()->mitra;
        $riwayatLangganan = PembayaranMitra::where('mitra_id', $mitra->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('mitra.langganan', compact('mitra', 'riwayatLangganan'));
    }

    public function langganan(): RedirectResponse
    {
        $user = auth()->user();
        $mitra = $user->mitra;

        $result = $this->pembayaranService->buatPembayaran($user, $mitra, 'langganan', 150000);

        if ($result['success']) {
            return redirect($result['payment_url']);
        }

        return back()->with('error', 'Gagal membuat pembayaran. Silakan coba lagi.');
    }
}
