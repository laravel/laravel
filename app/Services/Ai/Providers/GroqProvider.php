<?php

namespace App\Services\Ai\Providers;

use Illuminate\Support\Facades\Http;

class GroqProvider implements AiProvider
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
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->config['api_key'],
        ])->post(
            rtrim((string) $this->config['base_url'], '/').'/chat/completions',
            array_merge([
                'model' => $this->config['model'],
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ], $options),
        )->throw()->json();

        return (string) ($response['choices'][0]['message']['content'] ?? '');
    }
}

