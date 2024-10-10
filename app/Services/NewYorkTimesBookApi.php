<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Psr\Http\Client\ClientInterface;

class NewYorkTimesBookApi implements BookInterface
{
    private PendingRequest $client;

    public function __construct()
    {
        $this->client = Http::withOptions([
            'base_uri' => env('API_BASE_URI'),
            'timeout' => 10,
            'connect_timeout' => 2,
            'query' => [
                'api-key' => env('API_KEY')
            ]
        ]);
    }

    public function getBestsellers(array $options = []): Response
    {
        return $this->client->get('svc/books/v3/lists/best-sellers/history.json', $options);

    }
}
