<?php

namespace App\Providers;


use App\Core\Client\HttpClientInterface;
use App\Core\Client\LaravelClientWrapper;
use App\Services\BookInterface;
use App\Services\NewYorkTimesBookApi;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public array $bindings = [
        BookInterface::class => NewYorkTimesBookApi::class
    ];
}
