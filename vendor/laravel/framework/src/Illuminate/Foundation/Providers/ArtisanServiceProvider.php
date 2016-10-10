<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Foundation\Artisan;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Console\TailCommand;
use Illuminate\Foundation\Console\ChangesCommand;
use Illuminate\Foundation\Console\EnvironmentCommand;

class ArtisanServiceProvider extends ServiceProvider {

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
		$this->app->bindShared('artisan', function($app)
		{
			return new Artisan($app);
		});

		$this->app->bindShared('command.tail', function($app)
		{
			return new TailCommand;
		});

		$this->app->bindShared('command.changes', function($app)
		{
			return new ChangesCommand;
		});

		$this->app->bindShared('command.environment', function($app)
		{
			return new EnvironmentCommand;
		});

		$this->commands('command.tail', 'command.changes', 'command.environment');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('artisan', 'command.changes', 'command.environment');
	}

}
