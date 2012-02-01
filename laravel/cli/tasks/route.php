<?php namespace Laravel\CLI\Tasks;

use Laravel\URI;
use Laravel\Request;
use Laravel\Routing\Router;

class Route extends Task {

	/**
	 * Execute a route and dump the result.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function run($arguments = array())
	{
		if ( ! count($arguments) == 2)
		{
			throw new \Exception("Please specify a request method and URI.");
		}

		// First we'll set the request method and URI in the $_SERVER array,
		// which will allow the framework to retrieve the proper method
		// and URI using the normal URI and Request classes.
		$_SERVER['REQUEST_METHOD'] = strtoupper($arguments[0]);

		$_SERVER['REQUEST_URI'] = $arguments[1];

		$this->route();

		echo PHP_EOL;
	}

	/**
	 * Dump the results of the currently established route.
	 *
	 * @return void
	 */
	protected function route()
	{
		// We'll call the router using the method and URI specified by
		// the developer on the CLI. If a route is found, we will not
		// run the filters, but simply dump the result.
		$route = Router::route(Request::method(), URI::current());

		if ( ! is_null($route))
		{
			var_dump($route->response());
		}
		else
		{
			echo '404: Not Found';
		}
	}

}