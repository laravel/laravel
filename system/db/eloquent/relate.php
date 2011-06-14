<?php namespace System\DB\Eloquent;

class Relate {

	/**
	 * Retrieve the query for a 1:1 relationship.
	 *
	 * @param  string  $model
	 * @param  string  $foreign_key
	 * @param  object  $eloquent
	 * @return mixed
	 */
	public static function has_one($model, $foreign_key, $eloquent)
	{
		$eloquent->relating = __FUNCTION__;
		return static::has_one_or_many($model, $foreign_key, $eloquent);
	}

	/**
	 * Retrieve the query for a 1:* relationship.
	 *
	 * @param  string  $model
	 * @param  string  $foreign_key
	 * @param  object  $eloquent
	 * @return mixed
	 */
	public static function has_many($model, $foreign_key, $eloquent)
	{
		$eloquent->relating = __FUNCTION__;
		return static::has_one_or_many($model, $foreign_key, $eloquent);
	}

	/**
	 * Retrieve the query for a 1:1 or 1:* relationship.
	 *
	 * @param  string  $model
	 * @param  string  $foreign_key
	 * @param  object  $eloquent
	 * @return mixed
	 */
	private static function has_one_or_many($model, $foreign_key, $eloquent)
	{
		$eloquent->relating_key = (is_null($foreign_key)) ? \System\Str::lower(get_class($eloquent)).'_id' : $foreign_key;
		return Factory::make($model)->where($eloquent->relating_key, '=', $eloquent->id);
	}

	/**
	 * Retrieve the query for a 1:1 belonging relationship.
	 *
	 * @param  array   $caller
	 * @param  string  $model
	 * @param  object  $eloquent
	 * @return mixed
	 */
	public static function belongs_to($caller, $model, $eloquent)
	{
		$eloquent->relating = __FUNCTION__;
		$eloquent->relating_key = $caller['function'].'_id';

		return Factory::make($model)->where('id', '=', $eloquent->attributes[$eloquent->relating_key]);
	}

	/**
	 * Retrieve the query for a *:* relationship.
	 *
	 * @param  string  $model
	 * @param  string  $table
	 * @param  object  $eloquent
	 * @return mixed
	 */
	public static function has_many_and_belongs_to($model, $table, $eloquent)
	{
		// -----------------------------------------------------
		// Figure out the intermediate table name.
		// -----------------------------------------------------
		if (is_null($table))
		{
			$models = array(\System\Str::lower($model), \System\Str::lower(get_class($eloquent)));
			sort($models);

			$eloquent->relating_table = implode('_', $models);
		}
		else
		{
			$eloquent->relating_table = $table;
		}

		$eloquent->relating = __FUNCTION__;
		$eloquent->relating_key = $eloquent->relating_table.'.'.\System\Str::lower(get_class($eloquent)).'_id';

		return Factory::make($model)
							->select(Meta::table($model).'.*')
							->join($eloquent->relating_table, Meta::table($model).'.id', '=', $eloquent->relating_table.'.'.\System\Str::lower($model).'_id')
							->where($eloquent->relating_key, '=', $eloquent->id);
	}

}