<?php namespace Illuminate\Foundation;

use Illuminate\Console\Application as ConsoleApplication;

class Artisan {

	/**
	 * The application instance.
	 *
	 * @var \Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * The Artisan console instance.
	 *
	 * @var  \Illuminate\Console\Application
	 */
	protected $artisan;

	/**
	 * Create a new Artisan command runner instance.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * Get the Artisan console instance.
	 *
	 * @return \Illuminate\Console\Application
	 */
	protected function getArtisan()
	{
		if ( ! is_null($this->artisan)) return $this->artisan;

		$this->app->loadDeferredProviders();

		$this->artisan = ConsoleApplication::make($this->app);

		return $this->artisan->boot();
	}

	/**
	 * Dynamically pass all missing methods to console Artisan.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->getArtisan(), $method), $parameters);
	}

}
