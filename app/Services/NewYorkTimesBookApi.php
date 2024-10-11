<?php

namespace App\Services;

use App\Exception\ConnectionInterruptionException;
use App\Traits\ApiResponses;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Psr\Http\Client\ClientInterface;
use Throwable;

class NewYorkTimesBookApi implements BookInterface
{
    use ApiResponses;
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
        try{
            return $this->client->get('svc/books/v3/lists/best-sellers/history.json', $options);
        } catch(ConnectionException $exception) {
            throw new ConnectionInterruptionException();
        }
    }
}
