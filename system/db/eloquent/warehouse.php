<?php namespace System\DB\Eloquent;

class Warehouse {

	/**
	 * Save an Eloquent model to the database.
	 *
	 * @param  object  $eloquent
	 * @return void
	 */
	public static function store($eloquent)
	{
		// -----------------------------------------------------
		// Get the model name.
		// -----------------------------------------------------
		$model = get_class($eloquent);

		// -----------------------------------------------------
		// Get a fresh query instance for the model.
		// -----------------------------------------------------
		$eloquent->query = \System\DB\Query::table(Meta::table($model));

		// -----------------------------------------------------
		// Set the activity timestamps.
		// -----------------------------------------------------
		if (property_exists($model, 'timestamps') and $model::$timestamps)
		{
			static::timestamp($eloquent);
		}

		// -----------------------------------------------------
		// If the model exists in the database, update it.
		// Otherwise, insert the model and set the ID.
		// -----------------------------------------------------
		if ($eloquent->exists)
		{
			return $eloquent->query->where('id', '=', $eloquent->attributes['id'])->update($eloquent->dirty);
		}
		else
		{
			$eloquent->attributes['id'] =  $eloquent->query->insert_get_id($eloquent->attributes);
		}

		// -----------------------------------------------------
		// Set the existence flag to true.
		// -----------------------------------------------------
		$eloquent->exists = true;
	}

	/**
	 * Set the activity timestamps on a model.
	 *
	 * @param  object  $eloquent
	 * @return void
	 */
	private static function timestamp($eloquent)
	{
		$eloquent->updated_at = date('Y-m-d H:i:s');

		if ( ! $eloquent->exists)
		{
			$eloquent->created_at = $eloquent->updated_at;
		}
	}

}