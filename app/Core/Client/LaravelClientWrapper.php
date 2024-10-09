<?php

namespace App\Core\Client;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Http;

class LaravelClientWrapper implements HttpClientInterface
{
    private \Illuminate\Http\Client\PendingRequest $client;

    public function __construct()
    {
        $this->client = Http::withOptions([
            'base_uri' => env('API_BASE_URI'),
            'timeout' => 10,
            'connect_timeout' => 2
        ])->withToken(env('API_KEY'));
    }

    public function get(string $uri, array $options = []): PromiseInterface
    {
        return $this->client->get($uri, $options);
    }
}
