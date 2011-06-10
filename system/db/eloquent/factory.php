<?php namespace System\DB\Eloquent;

class Factory {

	/**
	 * Factory for creating new model instances.
	 *
	 * @param  string  $class
	 * @return object
	 */
	public static function make($class)
	{
		$model = new $class;
		
		// -----------------------------------------------------
		// Set the fluent query builder on the model.
		// -----------------------------------------------------
		$model->query = \System\DB\Query::table(Meta::table($class));

		return $model;
	}

}