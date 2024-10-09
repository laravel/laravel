<?php

namespace App\Providers;


use App\Core\Client\HttpClientInterface;
use App\Core\Client\LaravelClientWrapper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public array $bindings = [
        HttpClientInterface::class => LaravelClientWrapper::class
    ];
}
