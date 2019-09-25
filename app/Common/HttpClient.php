<?php

namespace App\Common;

use \GuzzleHttp\Client;

class HttpClient
{
    /**
     * The instance of guzzle client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Create a new HttpClient instance.
     *
     * @param  \GuzzleHttp\Client  $client
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Execute a POST request to the URL sending the params array as body variables.
     *
     * @param  string  $url
     * @param  null|array  $params
     * @return \GuzzleHttp\Psr\Http\Message\ResponseInterface
     */
    public function post($url, $params = [])
    {
        return $this->client->post($url, $params);
    }
}
