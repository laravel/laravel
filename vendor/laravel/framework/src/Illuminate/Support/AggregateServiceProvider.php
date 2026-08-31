<?php

namespace Illuminate\Support;

class AggregateServiceProvider extends ServiceProvider
{
    /**
     * The provider class names.
     *
     * @var array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected $providers = [];

    /**
     * An array of the service provider instances.
     *
     * @var array<int, \Illuminate\Support\ServiceProvider>
     */
    protected $instances = [];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->instances = [];

        foreach ($this->providers as $provider) {
            $this->instances[] = $this->app->register($provider);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides()
    {
        $provides = [];

        foreach ($this->providers as $provider) {
            $instance = $this->app->resolveProvider($provider);

            $provides = array_merge($provides, $instance->provides());
        }

        return $provides;
    }
}
