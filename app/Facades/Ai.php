<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Services\Ai\AiManager provider(?string $provider = null)
 * @method static \App\Services\Ai\AiManager using(string $provider)
 * @method static string ask(string $prompt, array $options = [])
 */
class Ai extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ai';
    }
}