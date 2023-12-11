<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Middleware\TrimStrings;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class MemoryLeakTest extends TestCase
{
    private const int NUMBER_OF_ITERATIONS = 100000;

    public function testItDoesntRunOutOfMemory(): void
    {
        for ($i = 0; $i < self::NUMBER_OF_ITERATIONS; $i++) {
            $kernel = $this->app->make(HttpKernel::class);

            TrimStrings::skipWhen(fn (): bool => false);
            ConvertEmptyStringsToNull::skipWhen(fn (): bool => false);

            $kernel->terminate(new Request(), new Response());
        }
    }
}
