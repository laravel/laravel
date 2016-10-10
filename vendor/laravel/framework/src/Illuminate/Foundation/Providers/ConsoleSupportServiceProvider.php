<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Support\ServiceProvider;

class ConsoleSupportServiceProvider extends ServiceProvider {

	/**
	 * The provider class names.
	 *
	 * @var array
	 */
	protected $providers = array(
		'Illuminate\Foundation\Providers\CommandCreatorServiceProvider',
		'Illuminate\Foundation\Providers\ComposerServiceProvider',
		'Illuminate\Foundation\Providers\KeyGeneratorServiceProvider',
		'Illuminate\Foundation\Providers\MaintenanceServiceProvider',
		'Illuminate\Foundation\Providers\OptimizeServiceProvider',
		'Illuminate\Foundation\Providers\PublisherServiceProvider',
		'Illuminate\Foundation\Providers\RouteListServiceProvider',
		'Illuminate\Foundation\Providers\ServerServiceProvider',
		'Illuminate\Foundation\Providers\TinkerServiceProvider',
		'Illuminate\Queue\FailConsoleServiceProvider',
	);

	/**
	 * An array of the service provider instances.
	 *
	 * @var array
	 */
	protected $instances = array();

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->instances = array();

		foreach ($this->providers as $provider)
		{
			$this->instances[] = $this->app->register($provider);
		}
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		$provides = array();

		foreach ($this->providers as $provider)
		{
			$instance = $this->app->resolveProviderClass($provider);

			$provides = array_merge($provides, $instance->provides());
		}

		return $provides;
	}

}
