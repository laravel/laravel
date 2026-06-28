<?php

namespace App\Services\Suppliers;

use App\Contracts\SupplierDriverInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Digiflazz Supplier Driver
 *
 * API Docs: https://documenter.getpostman.com/view/3755090/RzfmESgP
 * Dashboard: https://digiflazz.com/member
 */
class DigiflazzDriver implements SupplierDriverInterface
{
    protected string $username;
    protected string $apiKey;
    protected string $baseUrl = 'https://api.digiflazz.com/v1';
    protected bool   $active;

    public function __construct()
    {
        $env           = config('services.digiflazz.env', 'dev');
        $this->username = config('services.digiflazz.username', '');
        $this->apiKey   = $env === 'prod'
            ? config('services.digiflazz.api_key_prod', '')
            : config('services.digiflazz.api_key_dev', '');

        $this->active = !empty($this->username) && !empty($this->apiKey);
    }

    public function getName(): string
    {
        return 'digiflazz';
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function order(string $sku, string $target, ?string $zone, string $refId): array
    {
        try {
            // Digiflazz customer_no format: userId|zoneId (pipe separator)
            $customerNo = $zone ? "{$target}|{$zone}" : $target;
            $sign = md5($this->username . $this->apiKey . $refId);

            $response = Http::timeout(30)
                ->retry(2, 500)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}/transaction", [
                    'username'       => $this->username,
                    'buyer_sku_code' => $sku,
                    'customer_no'    => $customerNo,
                    'ref_id'         => $refId,
                    'sign'           => $sign,
                ]);

            $data = $response->json();
            Log::info('[Digiflazz] Order response', [
                'ref_id' => $refId,
                'sku'    => $sku,
                'status' => $data['data']['status'] ?? 'n/a',
            ]);

            $status  = strtolower($data['data']['status'] ?? '');
            $success = in_array($status, ['sukses', 'success']);
            $pending = $status === 'pending';

            return [
                'success' => $success,
                'pending' => $pending,
                'status'  => $data['data']['status'] ?? 'Gagal',
                'sn'      => $data['data']['sn'] ?? null,
                'message' => $data['data']['message'] ?? ($data['message'] ?? 'No message'),
                'driver'  => $this->getName(),
                'raw'     => $data,
            ];
        } catch (\Exception $e) {
            Log::error('[Digiflazz] Order exception', ['ref_id' => $refId, 'error' => $e->getMessage()]);
            return $this->errorResult($e->getMessage());
        }
    }

    public function checkStatus(string $refId): array
    {
        try {
            $sign = md5($this->username . $this->apiKey . $refId);

            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}/transaction", [
                    'username' => $this->username,
                    'ref_id'   => $refId,
                    'sign'     => $sign,
                ]);

            $data    = $response->json();
            $status  = strtolower($data['data']['status'] ?? '');
            $success = in_array($status, ['sukses', 'success']);

            return [
                'success' => $success,
                'pending' => !$success && $status === 'pending',
                'status'  => $data['data']['status'] ?? 'Gagal',
                'sn'      => $data['data']['sn'] ?? null,
                'driver'  => $this->getName(),
                'raw'     => $data,
            ];
        } catch (\Exception $e) {
            Log::error('[Digiflazz] CheckStatus exception', ['ref_id' => $refId, 'error' => $e->getMessage()]);
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
