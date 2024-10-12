<?php

namespace App\Services;

use App\Exception\ConnectionInterruptionException;
use App\Traits\ApiResponses;
use App\Traits\FormatQueryString;
use GuzzleHttp\Psr7\Query;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Psr\Http\Client\ClientInterface;
use Throwable;

class NewYorkTimesBookApi implements BookInterface
{
    use ApiResponses;
    use FormatQueryString;
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

    public function getBestsellers(?string $author = null, ?array $isbn = null, ?string $title = null, ?int $offset = null): Response
    {
        // Per NYTimes API documentation, this formats the isbn by semicolon separator
        if(!empty($isbn)) {
            $isbn = implode(';', $isbn);
        }

        try{
            return $this->client->get('svc/books/v3/lists/best-sellers/history.json', [
                'author' => $author,
                'isbn' => $isbn,
                'title' => $title,
                'offset' => $offset
            ]);
        } catch(ConnectionException $exception) {
            throw new ConnectionInterruptionException();
        }
    }
}
