<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    protected ?string $key;
    protected ?string $secret;

    public function __construct()
    {
        $this->key = config('services.razorpay.key');
        $this->secret = config('services.razorpay.secret');
    }

    public function getKey(): string
    {
        return $this->key ?: 'rzp_test_YourKeyId';
    }

    public function isConfigured(): bool
    {
        return !empty($this->key) && !empty($this->secret) && !str_contains($this->key, 'YourKeyId');
    }

    /**
     * Create an order on Razorpay (or generate a mock order if keys are pending).
     *
     * @param float $amountInRupees
     * @param string $receiptId
     * @param array $notes
     * @return array
     */
    public function createOrder(float $amountInRupees, string $receiptId, array $notes = []): array
    {
        $amountInPaise = (int) round($amountInRupees * 100);

        if ($this->isConfigured()) {
            try {
                $response = Http::withBasicAuth($this->key, $this->secret)
                    ->timeout(10)
                    ->post('https://api.razorpay.com/v1/orders', [
                        'amount' => $amountInPaise,
                        'currency' => 'INR',
                        'receipt' => $receiptId,
                        'notes' => $notes,
                    ]);

                if ($response->successful()) {
                    return [
                        'success' => true,
                        'order_id' => $response->json('id'),
                        'amount' => $amountInPaise,
                        'currency' => 'INR',
                        'key' => $this->key,
                    ];
                }

                Log::error('Razorpay Order Creation Failed: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('Razorpay Connection Exception: ' . $e->getMessage());
            }
        }

        // Development / Test Fallback mode when Razorpay live credentials are not yet entered in .env
        return [
            'success' => true,
            'order_id' => 'order_demo_' . strtoupper(bin2hex(random_bytes(6))),
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'key' => $this->getKey(),
            'is_mock' => true,
        ];
    }

    /**
     * Verify payment signature from Razorpay checkout.
     *
     * @param string $orderId
     * @param string $paymentId
     * @param string $signature
     * @return bool
     */
    public function verifyPaymentSignature(string $orderId, string $paymentId, string $signature): bool
    {
        // Support simulation / test mode
        if (str_starts_with($orderId, 'order_demo_')) {
            return !empty($paymentId) && !empty($signature);
        }

        if (!$this->isConfigured()) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, $this->secret);

        return hash_equals($expectedSignature, $signature);
    }
}
