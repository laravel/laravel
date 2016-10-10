<?php namespace Illuminate\Support\Facades;

/**
 * @see \Illuminate\Pagination\Environment
 */
class Paginator extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'paginator'; }

}
