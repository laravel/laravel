<?php namespace App\Http\Filters;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;

class MaintenanceFilter {

	/**
	 * The application implementation.
	 *
	 * @var Application
	 */
	protected $app;

    /**
     * The response factory implementation.
     *
     * @var ResponseFactory
     */
    protected $response;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Application  $app
	 * @return void
	 */
	public function __construct(Application $app, ResponseFactory $response)
	{
		$this->app = $app;
		$this->response = $response;
	}

	/**
	 * Run the request filter.
	 *
	 * @return mixed
	 */
	public function filter()
	{
		if ($this->app->isDownForMaintenance())
		{
			return $this->response->make('Be right back!', 503);
		}
	}

}
