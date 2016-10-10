<?php namespace Illuminate\Support\Facades;

/**
 * @see \Illuminate\Foundation\Application
 */
class App extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'app'; }

}
