<?php namespace Illuminate\Log;

use Monolog\Logger;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider {

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
		$logger = new Writer(
			new Logger($this->app['env']), $this->app['events']
		);

		$this->app->instance('log', $logger);

		// If the setup Closure has been bound in the container, we will resolve it
		// and pass in the logger instance. This allows this to defer all of the
		// logger class setup until the last possible second, improving speed.
		if (isset($this->app['log.setup']))
		{
			call_user_func($this->app['log.setup'], $logger);
		}
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('log');
	}

}
