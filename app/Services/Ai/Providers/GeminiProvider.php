<?php

namespace App\Services\Ai\Providers;

use Illuminate\Support\Facades\Http;

class GeminiProvider implements AiProvider
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        protected array $config,
    ) {
    }

    public function ask(string $prompt, array $options = []): string
    {
        $baseUrl = rtrim((string) $this->config['base_url'], '/');
        $model = $this->config['model'];
        $apiKey = $this->config['api_key'];

        $url = "{$baseUrl}/models/{$model}:generateContent?key={$apiKey}";

        $response = Http::post(
            $url,
            array_merge([
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ], $options),
        )->throw()->json();

        return (string) ($response['candidates'][0]['content']['parts'][0]['text'] ?? '');
    }
}

