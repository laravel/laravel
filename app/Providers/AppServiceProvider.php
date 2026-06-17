<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            [$maxAttempts, $decayMinutes] = array_pad(
                array_map('intval', explode(',', (string) config('api.rate_limit', '60,1'))),
                2,
                1,
            );

            return Limit::perMinute($maxAttempts, $decayMinutes)
                ->by($request->user()?->id ?: $request->ip());
        });
    }
}
