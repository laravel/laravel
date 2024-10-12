<?php

namespace App\Http\Controllers\Api\V1;

use App\Core\Client\HttpClientInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookSearchFormRequest;
use App\Services\BookInterface;
use Illuminate\Http\Request;

class BookController extends Controller
{

    private BookInterface $client;

    public function __construct(BookInterface $client)
    {
        $this->client = $client;
    }

    public function bestSellers(BookSearchFormRequest $bookSearchFormRequest)
    {
        $author = $bookSearchFormRequest->validated('author');
        $isbn = $bookSearchFormRequest->validated('isbn');
        $title = $bookSearchFormRequest->validated('title');
        $offset = $bookSearchFormRequest->validated('offset');

        return response()->json($this->client->getBestsellers(
            $author,
            $isbn,
            $title,
            $offset
        )->json());
    }
}
