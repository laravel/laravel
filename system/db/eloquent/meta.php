<?php namespace System\DB\Eloquent;

class Meta {

	/**
	 * Get the table name for a model.
	 *
	 * @param  string  $class
	 * @return string
	 */
	public static function table($class)
	{
		// -----------------------------------------------------
		// Check for a table name override.
		// -----------------------------------------------------
		if (property_exists($class, 'table'))
		{
			return $class::$table;
		}

		return \System\Str::lower(\System\Inflector::plural($class));
	}

}