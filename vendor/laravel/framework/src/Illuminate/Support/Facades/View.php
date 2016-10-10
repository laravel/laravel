<?php namespace Illuminate\Support\Facades;

/**
 * @see \Illuminate\View\Environment
 */
class View extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'view'; }

}
