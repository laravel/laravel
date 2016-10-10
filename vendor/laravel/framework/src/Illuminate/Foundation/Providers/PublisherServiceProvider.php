<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\ViewPublisher;
use Illuminate\Foundation\AssetPublisher;
use Illuminate\Foundation\ConfigPublisher;
use Illuminate\Foundation\MigrationPublisher;
use Illuminate\Foundation\Console\ViewPublishCommand;
use Illuminate\Foundation\Console\AssetPublishCommand;
use Illuminate\Foundation\Console\ConfigPublishCommand;
use Illuminate\Foundation\Console\MigratePublishCommand;

class PublisherServiceProvider extends ServiceProvider {

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
		$this->registerAssetPublisher();

		$this->registerConfigPublisher();

		$this->registerViewPublisher();

		$this->registerMigrationPublisher();

		$this->commands(
			'command.asset.publish', 'command.config.publish',
			'command.view.publish', 'command.migrate.publish'
		);
	}

	/**
	 * Register the asset publisher service and command.
	 *
	 * @return void
	 */
	protected function registerAssetPublisher()
	{
		$this->registerAssetPublishCommand();

		$this->app->bindShared('asset.publisher', function($app)
		{
			$publicPath = $app['path.public'];

			// The asset "publisher" is responsible for moving package's assets into the
			// web accessible public directory of an application so they can actually
			// be served to the browser. Otherwise, they would be locked in vendor.
			$publisher = new AssetPublisher($app['files'], $publicPath);

			$publisher->setPackagePath($app['path.base'].'/vendor');

			return $publisher;
		});
	}

	/**
	 * Register the asset publish console command.
	 *
	 * @return void
	 */
	protected function registerAssetPublishCommand()
	{
		$this->app->bindShared('command.asset.publish', function($app)
		{
			return new AssetPublishCommand($app['asset.publisher']);
		});
	}

	/**
	 * Register the configuration publisher class and command.
	 *
	 * @return void
	 */
	protected function registerConfigPublisher()
	{
		$this->registerConfigPublishCommand();

		$this->app->bindShared('config.publisher', function($app)
		{
			$path = $app['path'].'/config';

			// Once we have created the configuration publisher, we will set the default
			// package path on the object so that it knows where to find the packages
			// that are installed for the application and can move them to the app.
			$publisher = new ConfigPublisher($app['files'], $path);

			$publisher->setPackagePath($app['path.base'].'/vendor');

			return $publisher;
		});
	}

	/**
	 * Register the configuration publish console command.
	 *
	 * @return void
	 */
	protected function registerConfigPublishCommand()
	{
		$this->app->bindShared('command.config.publish', function($app)
		{
			return new ConfigPublishCommand($app['config.publisher']);
		});
	}

	/**
	 * Register the view publisher class and command.
	 *
	 * @return void
	 */
	protected function registerViewPublisher()
	{
		$this->registerViewPublishCommand();

		$this->app->bindShared('view.publisher', function($app)
		{
			$viewPath = $app['path'].'/views';

			// Once we have created the view publisher, we will set the default packages
			// path on this object so that it knows where to find all of the packages
			// that are installed for the application and can move them to the app.
			$publisher = new ViewPublisher($app['files'], $viewPath);

			$publisher->setPackagePath($app['path.base'].'/vendor');

			return $publisher;
		});
	}

	/**
	 * Register the view publish console command.
	 *
	 * @return void
	 */
	protected function registerViewPublishCommand()
	{
		$this->app->bindShared('command.view.publish', function($app)
		{
			return new ViewPublishCommand($app['view.publisher']);
		});
	}

	/**
	 * Register the migration publisher class and command.
	 *
	 * @return void
	 */
	protected function registerMigrationPublisher()
	{
		$this->registerMigratePublishCommand();

		$this->app->bindShared('migration.publisher', function($app)
		{
			return new MigrationPublisher($app['files']);
		});
	}

	/**
	 * Register the migration publisher command.
	 *
	 * @return void
	 */
	protected function registerMigratePublishCommand()
	{
		$this->app->bindShared('command.migrate.publish', function($app)
		{
			return new MigratePublishCommand;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
			'asset.publisher',
			'command.asset.publish',
			'config.publisher',
			'command.config.publish',
			'view.publisher',
			'command.view.publish',
			'migration.publisher',
			'command.migrate.publish',
		);
	}

}
