<?php namespace System\DB\Eloquent;

class Hydrate {

	/**
	 * Load the array of hydrated models.
	 *
	 * @param  object  $eloquent
	 * @return array
	 */
	public static function from($eloquent)
	{
		// -----------------------------------------------------
		// Load the base models.
		// -----------------------------------------------------
		$results = static::base(get_class($eloquent), $eloquent->query->get());

		// -----------------------------------------------------
		// Load all of the eager relationships.
		// -----------------------------------------------------
		if (count($results) > 0)
		{
			foreach ($eloquent->includes as $include)
			{
				// -----------------------------------------------------
				// Verify the relationship is defined.
				// -----------------------------------------------------
				if ( ! method_exists($eloquent, $include))
				{
					throw new \Exception("Attempting to eager load [$include], but the relationship is not defined.");
				}

				// -----------------------------------------------------
				// Eagerly load the relationship.
				// -----------------------------------------------------
				static::eagerly($eloquent, $include, $results);
			}
		}

		return $results;
	}

	/**
	 * Hydrate the base models for a query.
	 *
	 * @param  string  $class
	 * @param  array   $models
	 * @return array
	 */
	private static function base($class, $models)
	{
		// -----------------------------------------------------
		// Initialize the hydrated model array.
		// -----------------------------------------------------
		$results = array();

		// -----------------------------------------------------
		// Hydrate the models from the results.
		// -----------------------------------------------------
		foreach ($models as $model)
		{
			// -----------------------------------------------------
			// Instantiate a new model instance.
			// -----------------------------------------------------
			$result = new $class;

			// -----------------------------------------------------
			// Set the model's attributes.
			// -----------------------------------------------------
			$result->attributes = (array) $model;

			// -----------------------------------------------------
			// Indicate that the model already exists.
			// -----------------------------------------------------
			$result->exists = true;

			// -----------------------------------------------------
			// Add the hydrated model to the array of models.
			// The array is keyed by the primary keys of the models.
			// -----------------------------------------------------
			$results[$result->id] = $result;
		}

		return $results;
	}

	/**
	 * Eagerly load a relationship.
	 *
	 * @param  object  $eloquent
	 * @param  string  $include
	 * @param  array   $results
	 * @return void
	 */
	private static function eagerly($eloquent, $include, &$results)
	{
		// -----------------------------------------------------
		// Get the relationship Eloquent model.
		//
		// We spoof the "belongs_to" key to allow the query
		// to be fetched without any problems.
		// -----------------------------------------------------
		$eloquent->attributes[$spoof = $include.'_id'] = 0;

		$model = $eloquent->$include();

		unset($eloquent->attributes[$spoof]);

		// -----------------------------------------------------
		// Reset the WHERE clause on the query.
		// -----------------------------------------------------
		$model->query->where = 'WHERE 1 = 1';

		// -----------------------------------------------------
		// Reset the bindings on the query.
		// -----------------------------------------------------
		$model->query->bindings = array();

		// -----------------------------------------------------
		// Initialize the relationship on the parent models.
		// -----------------------------------------------------
		foreach ($results as &$result)
		{
			$result->ignore[$include] = (strpos($eloquent->relating, 'has_many') === 0) ? array() : null;
		}

		// -----------------------------------------------------
		// Eagerly load a 1:1 or 1:* relationship.
		// -----------------------------------------------------
		if ($eloquent->relating == 'has_one' or $eloquent->relating == 'has_many')
		{
			static::eagerly_load_one_or_many($eloquent->relating_key, $eloquent->relating, $include, $model, $results);
		}
		// -----------------------------------------------------
		// Eagerly load a 1:1 (belonging) relationship.
		// -----------------------------------------------------
		elseif ($eloquent->relating == 'belongs_to')
		{
			static::eagerly_load_belonging($eloquent->relating_key, $include, $model, $results);
		}
		// -----------------------------------------------------
		// Eagerly load a *:* relationship.
		// -----------------------------------------------------
		else
		{
			static::eagerly_load_many_to_many($eloquent->relating_key, $eloquent->relating_table, strtolower(get_class($eloquent)).'_id', $include, $model, $results);
		}
	}

	/**
	 * Eagerly load a 1:1 or 1:* relationship.
	 *
	 * @param  string  $relating_key
	 * @param  string  $relating
	 * @param  string  $include
	 * @param  object  $model
	 * @param  array   $results
	 * @return void
	 */
	private static function eagerly_load_one_or_many($relating_key, $relating, $include, $model, &$results)
	{
		// -----------------------------------------------------
		// Get the related models.
		// -----------------------------------------------------
		$inclusions = $model->where_in($relating_key, array_keys($results))->get();

		// -----------------------------------------------------
		// Match the child models with their parent.
		// -----------------------------------------------------
		foreach ($inclusions as $key => $inclusion)
		{
			if ($relating == 'has_one')
			{
				$results[$inclusion->$relating_key]->ignore[$include] = $inclusion;
			}
			else
			{
				$results[$inclusion->$relating_key]->ignore[$include][$inclusion->id] = $inclusion;
			}
		}
	}

	/**
	 * Eagerly load a 1:1 belonging relationship.
	 *
	 * @param  string  $relating_key
	 * @param  string  $include
	 * @param  object  $model
	 * @param  array   $results
	 * @return void
	 */
	private static function eagerly_load_belonging($relating_key, $include, $model, &$results)
	{
		// -----------------------------------------------------
		// Gather the keys from the parent models.
		// -----------------------------------------------------
		$keys = array();

		foreach ($results as &$result)
		{
			$keys[] = $result->$relating_key;
		}

		// -----------------------------------------------------
		// Get the related models.
		// -----------------------------------------------------
		$inclusions = $model->where_in('id', array_unique($keys))->get();

		// -----------------------------------------------------
		// Match the child models with their parent.
		// -----------------------------------------------------
		foreach ($results as &$result)
		{
			$result->ignore[$include] = $inclusions[$result->$relating_key];
		}
	}

	/**
	 * Eagerly load a many-to-many relationship.
	 *
	 * @param  string  $relating_key
	 * @param  string  $relating_table
	 * @param  string  $foreign_key
	 * @param  string  $include
	 * @param  object  $model
	 * @param  array   $results
	 * @return void	
	 */
	private static function eagerly_load_many_to_many($relating_key, $relating_table, $foreign_key, $include, $model, &$results)
	{
		// -----------------------------------------------------
		// Reset the SELECT clause.
		// -----------------------------------------------------
		$model->query->select = null;

		// -----------------------------------------------------
		// Retrieve the raw results as stdClasses.
		//
		// We also add the foreign key to the select which will allow us
		// to match the models back to their parents.
		// -----------------------------------------------------
		$inclusions = $model->query->where_in($relating_key, array_keys($results))->get(Meta::table(get_class($model)).'.*', $relating_table.'.'.$foreign_key);

		// -----------------------------------------------------
		// Get the class name of the related model.
		// -----------------------------------------------------
		$class = get_class($model);

		// -----------------------------------------------------
		// Create the related models.
		// -----------------------------------------------------
		foreach ($inclusions as $inclusion)
		{
			$related = new $class;

			$related->exists = true;
			$related->attributes = (array) $inclusion;

			// -----------------------------------------------------
			// Remove the foreign key from the attributes since it
			// was only added to the query to help us match the models.
			// -----------------------------------------------------
			unset($related->attributes[$foreign_key]);

			// -----------------------------------------------------
			// Add the related model to the parent model's array.
			// -----------------------------------------------------
			$results[$inclusion->$foreign_key]->ignore[$include][$inclusion->id] = $related;
		}
	}

}