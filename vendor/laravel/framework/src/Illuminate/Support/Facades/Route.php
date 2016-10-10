<?php namespace Illuminate\Support\Facades;

/**
 * @see \Illuminate\Routing\Router
 */
class Route extends Facade {

	/**
	 * Determine if the current route matches a given name.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public static function is($name)
	{
		return static::$app['router']->currentRouteNamed($name);
	}

	/**
	 * Determine if the current route uses a given controller action.
	 *
	 * @param  string  $action
	 * @return bool
	 */
	public static function uses($action)
	{
		return static::$app['router']->currentRouteUses($action);
	}

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'router'; }

}
