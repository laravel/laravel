<?php

use Laravel\Routing\Route;

class RouteTest extends PHPUnit_Framework_TestCase {

	/**
	 * Tear down the testing environment.
	 */
	public static function tearDownAfterClass()
	{
		unset($_SERVER['REQUEST_METHOD']);
		unset(Filter::$filters['test-after']);
		unset(Filter::$filters['test-before']);
		unset(Filter::$filters['test-params']);
		unset(Filter::$filters['test-multi-1']);
		unset(Filter::$filters['test-multi-2']);
	}

	/**
	 * Destroy the testing environment.
	 */
	public function tearDown()
	{
		Request::$route = null;
	}

	/**
	 * Tests the Route::is method.
	 *
	 * @group laravel
	 */
	public function testIsMethodIndicatesIfTheRouteHasAGivenName()
	{
		$route = new Route('GET', '/', array('as' => 'profile'));
		$this->assertTrue($route->is('profile'));
		$this->assertFalse($route->is('something'));
	}

	/**
	 * Test the basic execution of a route.
	 *
	 * @group laravel
	 */
	public function testBasicRoutesCanBeExecutedProperly()
	{
		$route = new Route('GET', '', array(function() { return 'Route!'; }));

		$this->assertEquals('Route!', $route->call()->content);
		$this->assertInstanceOf('Laravel\\Response', $route->call());
	}

	/**
	 * Test that route parameters are passed into the handlers.
	 *
	 * @group laravel
	 */
	public function testRouteParametersArePassedIntoTheHandler()
	{
		$route = new Route('GET', '', array(function($var) { return $var; }), array('Taylor'));

		$this->assertEquals('Taylor', $route->call()->content);
		$this->assertInstanceOf('Laravel\\Response', $route->call());
	}

	/**
	 * Test that calling a route calls the global before and after filters.
	 *
	 * @group laravel
	 */
	public function testCallingARouteCallsTheBeforeAndAfterFilters()
	{
		$route = new Route('GET', '', array(function() { return 'Hi!'; }));

		$_SERVER['before'] = false;
		$_SERVER['after'] = false;

		$route->call();

		$this->assertTrue($_SERVER['before']);
		$this->assertTrue($_SERVER['after']);
	}

	/**
	 * Test that before filters override the route response.
	 *
	 * @group laravel
	 */
	public function testBeforeFiltersOverrideTheRouteResponse()
	{
		Filter::register('test-before', function()
		{
			return 'Filtered!';
		});

		$route = new Route('GET', '', array('before' => 'test-before', function() {
			return 'Route!';
		}));

		$this->assertEquals('Filtered!', $route->call()->content);
	}

	/**
	 * Test that after filters do not affect the route response.
	 *
	 * @group laravel
	 */
	public function testAfterFilterDoesNotAffectTheResponse()
	{
		$_SERVER['test-after'] = false;

		Filter::register('test-after', function()
		{
			$_SERVER['test-after'] = true;
			return 'Filtered!';
		});

		$route = new Route('GET', '', array('after' => 'test-after', function()
		{
			return 'Route!';
		}));

		$this->assertEquals('Route!', $route->call()->content);
		$this->assertTrue($_SERVER['test-after']);
	}

	/**
	 * Test that the route calls the appropriate controller method when delegating.
	 *
	 * @group laravel
	 */
	public function testControllerActionCalledWhenDelegating()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$route = new Route('GET', '', array('uses' => 'auth@index'));

		$this->assertEquals('action_index', $route->call()->content);
	}

	/**
	 * Test that filter parameters are passed to the filter.
	 *
	 * @group laravel
	 */
	public function testFilterParametersArePassedToFilter()
	{
		Filter::register('test-params', function($var1, $var2)
		{
			return $var1.$var2;
		});

		$route = new Route('GET', '', array('before' => 'test-params:1,2'));

		$this->assertEquals('12', $route->call()->content);
	}

	/**
	 * Test that multiple filters can be assigned to a route.
	 *
	 * @group laravel
	 */
	public function testMultipleFiltersCanBeAssignedToARoute()
	{
		$_SERVER['test-multi-1'] = false;
		$_SERVER['test-multi-2'] = false;

		Filter::register('test-multi-1', function() { $_SERVER['test-multi-1'] = true; });
		Filter::register('test-multi-2', function() { $_SERVER['test-multi-2'] = true; });

		$route = new Route('GET', '', array('before' => 'test-multi-1|test-multi-2'));

		$route->call();

		$this->assertTrue($_SERVER['test-multi-1']);
		$this->assertTrue($_SERVER['test-multi-2']);
	}

}