<?php namespace System\DB\Eloquent;

class Warehouse {

	/**
	 * Save an Eloquent model to the database.
	 *
	 * @param  object  $eloquent
	 * @return bool
	 */
	public static function put($eloquent)
	{
		$model = get_class($eloquent);

		// -----------------------------------------------------
		// Get a fresh query instance for the model.
		// -----------------------------------------------------
		$eloquent->query = \System\DB\Query::table(Meta::table($model));

		// -----------------------------------------------------
		// Set the creation and update timestamps.
		// -----------------------------------------------------
		if (property_exists($model, 'timestamps') and $model::$timestamps)
		{
			static::timestamp($eloquent);
		}

		if ($eloquent->exists)
		{
			return ($eloquent->query->where('id', '=', $eloquent->attributes['id'])->update($eloquent->dirty) == 1) ? true : false;
		}
		else
		{
			$eloquent->attributes['id'] =  $eloquent->query->insert_get_id($eloquent->attributes);
		}

		$eloquent->exists = true;

		return true;
	}

	/**
	 * Delete an Eloquent model from the database.
	 *
	 * @param  object  $eloquent
	 * @return bool
	 */
	public static function forget($eloquent)
	{
		return \System\DB::table(Meta::table(get_class($eloquent)))->where('id', '=', $eloquent->id)->delete() == 1;
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