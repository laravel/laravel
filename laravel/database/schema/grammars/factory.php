<?php namespace Laravel\Database\Schema\Grammars;

class Factory {

	/**
	 * Create the appropriate schema grammar for the driver.
	 *
	 * @param  string   $driver
	 * @return Grammar
	 */
	public static function make($driver)
	{
		switch ($driver)
		{
			case 'mysql':
				return new MySQL;

			default:
				throw new \Exception("Schema operations not supported for [$driver].");
		}
	}

}