<?php

namespace App\Providers;

use App\Infrastructure\Doctrine\Validation\DoctrineInsensitivePresenceVerifier;
use Illuminate\Validation\PresenceVerifierInterface;
use Illuminate\Validation\ValidationServiceProvider;

class PresenceVerifierProvider extends ValidationServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     */
    protected bool $defer = true;

    /**
     * @return string[]
     */
    public function provides()
    {
        return [
            'validator',
            'validation.presence',
        ];
    }

    /**
     * Register the database presence verifier.
     */
    protected function registerPresenceVerifier(): void
    {
        $this->app->singleton('validation.presence', function ($app): PresenceVerifierInterface {
            return new DoctrineInsensitivePresenceVerifier($app['registry']);
        });
    }
}
