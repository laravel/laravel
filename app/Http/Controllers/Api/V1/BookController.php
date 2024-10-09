<?php

namespace App\Http\Controllers\Api\V1;

use App\Core\Client\HttpClientInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookSearchFormRequest;
use Illuminate\Http\Request;

class BookController extends Controller
{

    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function search(BookSearchFormRequest $test)
    {
        dd($test);
    }
}
