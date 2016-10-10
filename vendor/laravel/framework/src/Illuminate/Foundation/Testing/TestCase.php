<?php namespace Illuminate\Foundation\Testing;

use Illuminate\View\View;
use Illuminate\Auth\UserInterface;

abstract class TestCase extends \PHPUnit_Framework_TestCase {

	/**
	 * The Illuminate application instance.
	 *
	 * @var \Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * The HttpKernel client instance.
	 *
	 * @var \Illuminate\Foundation\Testing\Client
	 */
	protected $client;

	/**
	 * Setup the test environment.
	 *
	 * @return void
	 */
	public function setUp()
	{
		if ( ! $this->app)
		{
			$this->refreshApplication();
		}
	}

	/**
	 * Refresh the application instance.
	 *
	 * @return void
	 */
	protected function refreshApplication()
	{
		$this->app = $this->createApplication();

		$this->client = $this->createClient();

		$this->app->setRequestForConsoleEnvironment();

		$this->app->boot();
	}

	/**
	 * Creates the application.
	 *
	 * Needs to be implemented by subclasses.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	abstract public function createApplication();

	/**
	 * Call the given URI and return the Response.
	 *
	 * @param  string  $method
	 * @param  string  $uri
	 * @param  array   $parameters
	 * @param  array   $files
	 * @param  array   $server
	 * @param  string  $content
	 * @param  bool    $changeHistory
	 * @return \Illuminate\Http\Response
	 */
	public function call()
	{
		call_user_func_array(array($this->client, 'request'), func_get_args());

		return $this->client->getResponse();
	}

	/**
	 * Call the given HTTPS URI and return the Response.
	 *
	 * @param  string  $method
	 * @param  string  $uri
	 * @param  array   $parameters
	 * @param  array   $files
	 * @param  array   $server
	 * @param  string  $content
	 * @param  bool    $changeHistory
	 * @return \Illuminate\Http\Response
	 */
	public function callSecure()
	{
		$parameters = func_get_args();

		$parameters[1] = 'https://localhost/'.ltrim($parameters[1], '/');

		return call_user_func_array(array($this, 'call'), $parameters);
	}

	/**
	 * Call a controller action and return the Response.
	 *
	 * @param  string  $method
	 * @param  string  $action
	 * @param  array   $wildcards
	 * @param  array   $parameters
	 * @param  array   $files
	 * @param  array   $server
	 * @param  string  $content
	 * @param  bool    $changeHistory
	 * @return \Illuminate\Http\Response
	 */
	public function action($method, $action, $wildcards = array(), $parameters = array(), $files = array(), $server = array(), $content = null, $changeHistory = true)
	{
		$uri = $this->app['url']->action($action, $wildcards, true);

		return $this->call($method, $uri, $parameters, $files, $server, $content, $changeHistory);
	}

	/**
	 * Call a named route and return the Response.
	 *
	 * @param  string  $method
	 * @param  string  $name
	 * @param  array   $routeParameters
	 * @param  array   $parameters
	 * @param  array   $files
	 * @param  array   $server
	 * @param  string  $content
	 * @param  bool    $changeHistory
	 * @return \Illuminate\Http\Response
	 */
	public function route($method, $name, $routeParameters = array(), $parameters = array(), $files = array(), $server = array(), $content = null, $changeHistory = true)
	{
		$uri = $this->app['url']->route($name, $routeParameters);

		return $this->call($method, $uri, $parameters, $files, $server, $content, $changeHistory);
	}

	/**
	 * Assert that the client response has an OK status code.
	 *
	 * @return void
	 */
	public function assertResponseOk()
	{
		$response = $this->client->getResponse();

		$actual = $response->getStatusCode();

		return $this->assertTrue($response->isOk(), 'Expected status code 200, got ' .$actual);
	}

	/**
	 * Assert that the client response has a given code.
	 *
	 * @param  int  $code
	 * @return void
	 */
	public function assertResponseStatus($code)
	{
		return $this->assertEquals($code, $this->client->getResponse()->getStatusCode());
	}

	/**
	 * Assert that the response view has a given piece of bound data.
	 *
	 * @param  string|array  $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function assertViewHas($key, $value = null)
	{
		if (is_array($key)) return $this->assertViewHasAll($key);

		$response = $this->client->getResponse();

		if ( ! isset($response->original) || ! $response->original instanceof View)
		{
			return $this->assertTrue(false, 'The response was not a view.');
		}

		if (is_null($value))
		{
			$this->assertArrayHasKey($key, $response->original->getData());
		}
		else
		{
			$this->assertEquals($value, $response->original->$key);
		}
	}

	/**
	 * Assert that the view has a given list of bound data.
	 *
	 * @param  array  $bindings
	 * @return void
	 */
	public function assertViewHasAll(array $bindings)
	{
		foreach ($bindings as $key => $value)
		{
			if (is_int($key))
			{
				$this->assertViewHas($value);
			}
			else
			{
				$this->assertViewHas($key, $value);
			}
		}
	}

	/**
	 * Assert that the response view is missing a piece of bound data.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function assertViewMissing($key)
	{
		$response = $this->client->getResponse();

		if ( ! isset($response->original) || ! $response->original instanceof View)
		{
			return $this->assertTrue(false, 'The response was not a view.');
		}

		$this->assertArrayNotHasKey($key, $response->original->getData());
	}

	/**
	 * Assert whether the client was redirected to a given URI.
	 *
	 * @param  string  $uri
	 * @param  array   $with
	 * @return void
	 */
	public function assertRedirectedTo($uri, $with = array())
	{
		$response = $this->client->getResponse();

		$this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);

		$this->assertEquals($this->app['url']->to($uri), $response->headers->get('Location'));

		$this->assertSessionHasAll($with);
	}

	/**
	 * Assert whether the client was redirected to a given route.
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @param  array   $with
	 * @return void
	 */
	public function assertRedirectedToRoute($name, $parameters = array(), $with = array())
	{
		$this->assertRedirectedTo($this->app['url']->route($name, $parameters), $with);
	}

	/**
	 * Assert whether the client was redirected to a given action.
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @param  array   $with
	 * @return void
	 */
	public function assertRedirectedToAction($name, $parameters = array(), $with = array())
	{
		$this->assertRedirectedTo($this->app['url']->action($name, $parameters), $with);
	}

	/**
	 * Assert that the session has a given list of values.
	 *
	 * @param  string|array  $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function assertSessionHas($key, $value = null)
	{
		if (is_array($key)) return $this->assertSessionHasAll($key);

		if (is_null($value))
		{
			$this->assertTrue($this->app['session.store']->has($key), "Session missing key: $key");
		}
		else
		{
			$this->assertEquals($value, $this->app['session.store']->get($key));
		}
	}

	/**
	 * Assert that the session has a given list of values.
	 *
	 * @param  array  $bindings
	 * @return void
	 */
	public function assertSessionHasAll(array $bindings)
	{
		foreach ($bindings as $key => $value)
		{
			if (is_int($key))
			{
				$this->assertSessionHas($value);
			}
			else
			{
				$this->assertSessionHas($key, $value);
			}
		}
	}

	/**
	 * Assert that the session has errors bound.
	 *
	 * @param  string|array  $bindings
	 * @param  mixed  $format
	 * @return void
	 */
	public function assertSessionHasErrors($bindings = array(), $format = null)
	{
		$this->assertSessionHas('errors');

		$bindings = (array)$bindings;

		$errors = $this->app['session.store']->get('errors');

		foreach ($bindings as $key => $value)
		{
			if (is_int($key))
			{
				$this->assertTrue($errors->has($value), "Session missing error: $value");
			}
			else
			{
				$this->assertContains($value, $errors->get($key, $format));
			}
		}
	}

	/**
	 * Assert that the session has old input.
	 *
	 * @return void
	 */
	public function assertHasOldInput()
	{
		$this->assertSessionHas('_old_input');
	}

	/**
	 * Set the session to the given array.
	 *
	 * @param  array  $data
	 * @return void
	 */
	public function session(array $data)
	{
		$this->startSession();

		foreach ($data as $key => $value)
		{
			$this->app['session']->put($key, $value);
		}
	}

	/**
	 * Flush all of the current session data.
	 *
	 * @return void
	 */
	public function flushSession()
	{
		$this->startSession();

		$this->app['session']->flush();
	}

	/**
	 * Start the session for the application.
	 *
	 * @return void
	 */
	protected function startSession()
	{
		if ( ! $this->app['session']->isStarted())
		{
			$this->app['session']->start();
		}
	}

	/**
	 * Set the currently logged in user for the application.
	 *
	 * @param  \Illuminate\Auth\UserInterface  $user
	 * @param  string  $driver
	 * @return void
	 */
	public function be(UserInterface $user, $driver = null)
	{
		$this->app['auth']->driver($driver)->setUser($user);
	}

	/**
	 * Seed a given database connection.
	 *
	 * @param  string  $class
	 * @return void
	 */
	public function seed($class = 'DatabaseSeeder')
	{
		$this->app['artisan']->call('db:seed', array('--class' => $class));
	}

	/**
	 * Create a new HttpKernel client instance.
	 *
	 * @param  array  $server
	 * @return \Symfony\Component\HttpKernel\Client
	 */
	protected function createClient(array $server = array())
	{
		return new Client($this->app, $server);
	}

}
