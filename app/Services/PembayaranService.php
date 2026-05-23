<?php

namespace App\Services;

use App\Models\Mitra;
use App\Models\PembayaranMitra;
use App\Models\User;

class PembayaranService
{
    public function __construct(
        private readonly DokuService $doku,
    ) {}

    /**
     * Buat pembayaran dan redirect ke DOKU.
     *
     * @return array{success: bool, payment_url: string|null, pembayaran: PembayaranMitra}
     */
    public function buatPembayaran(User $user, Mitra $mitra, string $jenis, int $jumlah): array
    {
        $pembayaran = PembayaranMitra::create([
            'mitra_id' => $mitra->id,
            'jenis' => $jenis,
            'jumlah' => $jumlah,
            'status' => 'pending',
        ]);

        $result = $this->doku->createPayment([
            'amount' => $jumlah,
            'customer_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone,
        ]);

        if ($result['success'] && $result['payment_url']) {
            $pembayaran->update(['invoice_number' => $result['invoice_number']]);

            return [
                'success' => true,
                'payment_url' => $result['payment_url'],
                'pembayaran' => $pembayaran,
            ];
        }

        $pembayaran->update(['status' => 'gagal']);

        return [
            'success' => false,
            'payment_url' => null,
            'pembayaran' => $pembayaran,
        ];
    }

    /**
     * Proses callback dari DOKU — cek status pembayaran.
     */
    public function prosesCallback(PembayaranMitra $pembayaran): void
    {
        if (!$pembayaran->invoice_number) {
            return;
        }

        $result = $this->doku->checkStatus($pembayaran->invoice_number);

        if (!$result['success']) {
            return;
        }

        $data = $result['data'];
        $transactionStatus = strtoupper($data['transaction']['status'] ?? '');
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

            $this->perpanjangSubscription($pembayaran);
        } elseif (in_array($transactionStatus, ['FAILED', 'EXPIRED', 'VOIDED'])) {
            $pembayaran->update([
                'status' => 'gagal',
                'metode_pembayaran' => $paymentMethod,
            ]);
        }
    }

    /**
     * Perpanjang subscription mitra jika pembayaran langganan.
     */
    private function perpanjangSubscription(PembayaranMitra $pembayaran): void
    {
        if ($pembayaran->jenis !== 'langganan') {
            return;
        }

        $mitra = $pembayaran->mitra;
        $currentEnd = ($mitra->subscription_sampai && $mitra->subscription_sampai >= now())
            ? $mitra->subscription_sampai
            : now();

        $mitra->update(['subscription_sampai' => $currentEnd->copy()->addMonth()]);
    }
}
