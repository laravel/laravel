<?php

namespace App\Services\Suppliers;

use App\Contracts\SupplierDriverInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * VIP Reseller Supplier Driver
 *
 * Dashboard: https://vip-reseller.co.id
 * Aktifkan dengan: VIPRESELLER_ACTIVE=true di .env
 *
 * NOTE: Verifikasi format response dengan API docs VIP Reseller
 *       setelah mendapatkan akun. Field names bisa berbeda.
 */
class VipResellerDriver implements SupplierDriverInterface
{
    protected string $apiKey;
    protected string $apiId;
    protected string $baseUrl = 'https://vip-reseller.co.id/api';
    protected bool   $active;

    public function __construct()
    {
        $this->apiKey = config('services.vipreseller.api_key', '');
        $this->apiId  = config('services.vipreseller.api_id', '');
        $this->active = config('services.vipreseller.active', false)
                     && !empty($this->apiKey)
                     && !empty($this->apiId);
    }

    public function getName(): string
    {
        return 'vipreseller';
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function order(string $sku, string $target, ?string $zone, string $refId): array
    {
        try {
            $customerNo = $zone ? "{$target}|{$zone}" : $target;
            // VIP Reseller sign: md5(apiKey + apiId + refId)
            $sign = md5($this->apiKey . $this->apiId . $refId);

            $response = Http::timeout(30)
                ->retry(2, 500)
                ->post("{$this->baseUrl}/game-feature", [
                    'key'    => $this->apiKey,
                    'sign'   => $sign,
                    'type'   => 'order',
                    'code'   => $sku,
                    'target' => $customerNo,
                    'ref_id' => $refId,
                ]);

            $data = $response->json();
            Log::info('[VIPReseller] Order response', [
                'ref_id' => $refId,
                'result' => $data,
            ]);

            // Adjust based on actual VIP Reseller API docs
            $success = ($data['result'] ?? false) === true
                    || strtolower($data['status'] ?? '') === 'success';
            $pending = !$success && strtolower($data['status'] ?? '') === 'pending';

            return [
                'success' => $success,
                'pending' => $pending,
                'status'  => $data['status'] ?? 'Gagal',
                'sn'      => $data['sn'] ?? $data['data']['sn'] ?? null,
                'message' => $data['message'] ?? 'No message',
                'driver'  => $this->getName(),
                'raw'     => $data,
            ];
        } catch (\Exception $e) {
            Log::error('[VIPReseller] Order exception', ['ref_id' => $refId, 'error' => $e->getMessage()]);
            return $this->errorResult($e->getMessage());
        }
    }

    public function checkStatus(string $refId): array
    {
        try {
            $sign = md5($this->apiKey . $this->apiId . $refId);

            $response = Http::timeout(15)
                ->post("{$this->baseUrl}/game-feature", [
                    'key'    => $this->apiKey,
                    'sign'   => $sign,
                    'type'   => 'inquiry',
                    'ref_id' => $refId,
                ]);

            $data    = $response->json();
            $success = ($data['result'] ?? false) === true
                    || strtolower($data['status'] ?? '') === 'success';

            return [
                'success' => $success,
                'pending' => !$success,
                'status'  => $data['status'] ?? 'Gagal',
                'sn'      => $data['sn'] ?? null,
                'driver'  => $this->getName(),
                'raw'     => $data,
            ];
        } catch (\Exception $e) {
            Log::error('[VIPReseller] CheckStatus exception', ['ref_id' => $refId, 'error' => $e->getMessage()]);
            return $this->errorResult($e->getMessage());
        }
    }

    private function errorResult(string $message): array
    {
        return [
            'success' => false,
            'pending' => false,
            'status'  => 'Gagal',
            'sn'      => null,
            'message' => $message,
            'driver'  => $this->getName(),
            'raw'     => [],
        ];
    }
}
