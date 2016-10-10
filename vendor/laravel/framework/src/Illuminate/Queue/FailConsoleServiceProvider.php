<?php namespace Illuminate\Queue;

use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Console\RetryCommand;
use Illuminate\Queue\Console\ListFailedCommand;
use Illuminate\Queue\Console\FlushFailedCommand;
use Illuminate\Queue\Console\FailedTableCommand;
use Illuminate\Queue\Console\ForgetFailedCommand;

class FailConsoleServiceProvider extends ServiceProvider {

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
		$this->app->bindShared('command.queue.failed', function($app)
		{
			return new ListFailedCommand;
		});

		$this->app->bindShared('command.queue.retry', function($app)
		{
			return new RetryCommand;
		});

		$this->app->bindShared('command.queue.forget', function($app)
		{
			return new ForgetFailedCommand;
		});

		$this->app->bindShared('command.queue.flush', function($app)
		{
			return new FlushFailedCommand;
		});

		$this->app->bindShared('command.queue.failed-table', function($app)
		{
			return new FailedTableCommand($app['files']);
		});

		$this->commands(
			'command.queue.failed', 'command.queue.retry', 'command.queue.forget',
			'command.queue.flush', 'command.queue.failed-table'
		);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
			'command.queue.failed', 'command.queue.retry', 'command.queue.forget', 'command.queue.flush', 'command.queue.failed-table',
		);
	}

}
