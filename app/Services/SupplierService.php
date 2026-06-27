<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupplierService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $username;

    public function __construct()
    {
        $this->apiKey = config('services.supplier.key');
        $this->baseUrl = config('services.supplier.url');
        $this->username = config('services.supplier.username');
    }

    public function order(string $productCode, string $targetId, ?string $targetZone = null, string $refId = ''): array
    {
        try {
            $sign = md5($this->username . $this->apiKey . $refId);

            $payload = [
                'username' => $this->username,
                'buyer_sku_code' => $productCode,
                'customer_no' => $targetZone ? $targetId . $targetZone : $targetId,
                'ref_id' => $refId,
                'sign' => $sign,
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/transaction", $payload);

            $result = $response->json();

            Log::info('Supplier order', [
                'ref_id' => $refId,
                'status' => $result['data']['status'] ?? 'unknown',
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Supplier order failed', ['error' => $e->getMessage()]);
            return ['data' => ['status' => 'Gagal', 'message' => $e->getMessage()]];
        }
    }

    public function checkStatus(string $refId): array
    {
        try {
            $sign = md5($this->username . $this->apiKey . $refId);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/transaction", [
                'username' => $this->username,
                'ref_id' => $refId,
                'sign' => $sign,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Supplier check failed', ['error' => $e->getMessage()]);
            return ['data' => ['status' => 'Gagal']];
        }
    }
}
