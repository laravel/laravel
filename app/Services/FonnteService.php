<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $token;
    protected string $baseUrl = 'https://api.fonnte.com';

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
    }

    public function sendMessage(string $phone, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->asForm()->post("{$this->baseUrl}/send", [
                'target' => $this->formatPhone($phone),
                'message' => $message,
                'delay' => '2',
            ]);

            $result = $response->json();

            Log::info('Fonnte send message', [
                'phone' => $phone,
                'status' => $result['status'] ?? 'unknown',
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Fonnte send failed', ['error' => $e->getMessage()]);
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }

    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }
        return $phone;
    }
}
