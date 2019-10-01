<?php

namespace App\Providers;

use App\Doctrine\Validation\DoctrineInsensitivePresenceVerifier;
use Illuminate\Validation\ValidationServiceProvider;

class PresenceVerifierProvider extends ValidationServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

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
    protected function registerPresenceVerifier()
    {
        $this->app->singleton('validation.presence', function ($app) {
            return new DoctrineInsensitivePresenceVerifier($app['registry']);
        });
    }
}
