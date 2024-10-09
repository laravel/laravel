<?php

namespace App\Core\Client;

use GuzzleHttp\Promise\PromiseInterface;

interface HttpClientInterface
{
    public function get(string $uri, array $options = []): PromiseInterface;
}
