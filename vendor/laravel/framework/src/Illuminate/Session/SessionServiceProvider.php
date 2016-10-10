<?php namespace Illuminate\Session;

use Illuminate\Support\ServiceProvider;

class SessionServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->setupDefaultDriver();

		$this->registerSessionManager();

		$this->registerSessionDriver();
	}

	/**
	 * Setup the default session driver for the application.
	 *
	 * @return void
	 */
	protected function setupDefaultDriver()
	{
		if ($this->app->runningInConsole())
		{
			$this->app['config']['session.driver'] = 'array';
		}
	}

	/**
	 * Register the session manager instance.
	 *
	 * @return void
	 */
	protected function registerSessionManager()
	{
		$this->app->bindShared('session', function($app)
		{
			return new SessionManager($app);
		});
	}

	/**
	 * Register the session driver instance.
	 *
	 * @return void
	 */
	protected function registerSessionDriver()
	{
		$this->app->bindShared('session.store', function($app)
		{
			// First, we will create the session manager which is responsible for the
			// creation of the various session drivers when they are needed by the
			// application instance, and will resolve them on a lazy load basis.
			$manager = $app['session'];

			return $manager->driver();
		});
	}

	/**
	 * Get the session driver name.
	 *
	 * @return string
	 */
	protected function getDriver()
	{
		return $this->app['config']['session.driver'];
	}

}
