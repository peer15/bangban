<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DokuService
{
    private string $clientId;
    private string $secretKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->clientId = config('doku.client_id');
        $this->secretKey = config('doku.secret_key');
        $this->baseUrl = config('doku.base_url');
    }

    /**
     * Generate DOKU Checkout payment
     */
    public function createPayment(array $data): array
    {
        $invoiceNumber = 'BANGBAN-' . strtoupper(Str::random(8)) . '-' . time();
        $requestId = Str::uuid()->toString();

        $body = [
            'order' => [
                'amount' => $data['amount'],
                'invoice_number' => $invoiceNumber,
                'currency' => 'IDR',
                'callback_url' => url('/pembayaran/callback'),
                'auto_redirect' => true,
            ],
            'payment' => [
                'payment_due_date' => 60, // menit
            ],
            'customer' => [
                'id' => (string) $data['customer_id'],
                'name' => $data['customer_name'],
                'email' => $data['customer_email'],
                'phone' => $data['customer_phone'] ?? '',
            ],
        ];

        $jsonBody = json_encode($body);
        $requestDate = gmdate('Y-m-d\TH:i:s\Z');
        $targetPath = '/checkout/v1/payment';
        $digest = base64_encode(hash('sha256', $jsonBody, true));
        $signature = $this->generateSignature('POST', $targetPath, $requestId, $requestDate, $digest);

        $response = Http::withHeaders([
            'Client-Id' => $this->clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $requestDate,
            'Signature' => 'HMACSHA256=' . $signature,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . $targetPath, $body);

        if ($response->successful()) {
            $result = $response->json();
            return [
                'success' => true,
                'invoice_number' => $invoiceNumber,
                'payment_url' => $result['response']['payment']['url'] ?? null,
                'response' => $result,
            ];
        }

        return [
            'success' => false,
            'error' => $response->body(),
        ];
    }

    /**
     * Verify notification signature from DOKU
     */
    public function verifyNotification(string $requestId, string $requestTimestamp, string $signature, string $body): bool
    {
        $targetPath = '/pembayaran/notify';
        $digest = base64_encode(hash('sha256', $body, true));

        $componentSignature = "Client-Id:" . $this->clientId . "\n"
            . "Request-Id:" . $requestId . "\n"
            . "Request-Timestamp:" . $requestTimestamp . "\n"
            . "Request-Target:" . $targetPath . "\n"
            . "Digest:" . $digest;

        $expectedSignature = base64_encode(hash_hmac('sha256', $componentSignature, $this->secretKey, true));

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Check payment status from DOKU
     */
    public function checkStatus(string $invoiceNumber): array
    {
        $requestId = Str::uuid()->toString();
        $requestDate = gmdate('Y-m-d\TH:i:s\Z');
        $targetPath = '/orders/v1/status/' . $invoiceNumber;

        // GET request signature - tanpa Digest
        $componentSignature = "Client-Id:" . $this->clientId . "\n"
            . "Request-Id:" . $requestId . "\n"
            . "Request-Timestamp:" . $requestDate . "\n"
            . "Request-Target:" . $targetPath;

        $signature = base64_encode(hash_hmac('sha256', $componentSignature, $this->secretKey, true));

        $response = Http::withHeaders([
            'Client-Id' => $this->clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $requestDate,
            'Signature' => 'HMACSHA256=' . $signature,
        ])->get($this->baseUrl . $targetPath);

        if ($response->successful()) {
            return [
                'success' => true,
                'data' => $response->json(),
            ];
        }

        return [
            'success' => false,
            'error' => $response->body(),
        ];
    }

    private function generateSignature(string $method, string $path, string $requestId, string $requestDate, string $digest): string
    {
        $componentSignature = "Client-Id:" . $this->clientId . "\n"
            . "Request-Id:" . $requestId . "\n"
            . "Request-Timestamp:" . $requestDate . "\n"
            . "Request-Target:" . $path . "\n"
            . "Digest:" . $digest;

        return base64_encode(hash_hmac('sha256', $componentSignature, $this->secretKey, true));
    }
}
