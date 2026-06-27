<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TripayService
{
    protected string $apiKey;
    protected string $privateKey;
    protected string $merchantCode;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.tripay.api_key');
        $this->privateKey = config('services.tripay.private_key');
        $this->merchantCode = config('services.tripay.merchant_code');
        $this->baseUrl = config('services.tripay.env') === 'sandbox'
            ? 'https://tripay.co.id/api-sandbox'
            : 'https://tripay.co.id/api';
    }

    public function createTransaction(\App\Models\Order $order, \App\Models\Customer $customer, string $method = 'QRIS'): array
    {
        try {
            $merchantRef = 'ARC-' . $order->id . '-' . time();
            $amount = $order->amount;

            $signature = hash_hmac('sha256', $this->merchantCode . $merchantRef . $amount, $this->privateKey);

            $payload = [
                'method' => $method,
                'merchant_ref' => $merchantRef,
                'amount' => $amount,
                'customer_name' => $customer->name ?? 'ArcanePay Customer',
                'customer_email' => 'customer@arcanepay.biz.id',
                'customer_phone' => $customer->phone,
                'order_items' => [
                    [
                        'sku' => 'TOPUP-' . $order->id,
                        'name' => $order->product->name . ' - ' . $order->product->category->name,
                        'price' => $amount,
                        'quantity' => 1,
                    ]
                ],
                'callback_url' => config('app.url') . '/api/payment/callback',
                'return_url' => 'https://arcanepay.biz.id/status/' . $order->id,
                'expired_time' => now()->addMinutes(60)->timestamp,
                'signature' => $signature,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/transaction/create", $payload);

            $result = $response->json();

            Log::info('Tripay create transaction', [
                'order_id' => $order->id,
                'status' => $result['success'] ?? false,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Tripay create failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function verifyCallback(string $jsonPayload, string $callbackSignature): bool
    {
        $computed = hash_hmac('sha256', $jsonPayload, $this->privateKey);
        return hash_equals($computed, $callbackSignature);
    }
}
