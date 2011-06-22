<?php namespace System\DB\Eloquent;

use System\DB\Eloquent;

class Hydrator {

	/**
	 * Load the array of hydrated models.
	 *
	 * @param  object  $eloquent
	 * @return array
	 */
	public static function hydrate($eloquent)
	{
		// -----------------------------------------------------
		// Load the base / parent models from the query results.
		// -----------------------------------------------------
		$results = static::base(get_class($eloquent), $eloquent->query->get());

		// -----------------------------------------------------
		// Load all of the eager relationships.
		// -----------------------------------------------------
		if (count($results) > 0)
		{
			foreach ($eloquent->includes as $include)
			{
				if ( ! method_exists($eloquent, $include))
				{
					throw new \Exception("Attempting to eager load [$include], but the relationship is not defined.");
				}

				static::eagerly($eloquent, $results, $include);
			}
		}

		return $results;
	}

	/**
	 * Hydrate the base models for a query.
	 *
	 * @param  string  $class
	 * @param  array   $results
	 * @return array
	 */
	private static function base($class, $results)
	{
		$models = array();

		foreach ($results as $result)
		{
			$model = new $class;

			$model->attributes = (array) $result;
			$model->exists = true;

			// -----------------------------------------------------
			// The results are keyed by the ID on the record. This
			// will allow us to conveniently match them to child
			// models during eager loading.
			// -----------------------------------------------------
			$models[$model->id] = $model;
		}

		return $models;
	}

	/**
	 * Eagerly load a relationship.
	 *
	 * @param  object  $eloquent
	 * @param  array   $parents
	 * @param  string  $include
	 * @return void
	 */
	private static function eagerly($eloquent, &$parents, $include)
	{
		// -----------------------------------------------------
		// Get the relationship Eloquent model.
		//
		// We temporarily spoof the belongs_to key to allow the
		// query to be fetched without any problems, since the
		// belongs_to method actually gets the attribute.
		// -----------------------------------------------------
		$eloquent->attributes[$spoof = $include.'_id'] = 0;

		$relationship = $eloquent->$include();

		unset($eloquent->attributes[$spoof]);

		// -----------------------------------------------------
		// Reset the WHERE clause and bindings on the query.
		// We'll add our own WHERE clause soon.
		// -----------------------------------------------------
		$relationship->query->where = 'WHERE 1 = 1';
		$relationship->query->bindings = array();

		// -----------------------------------------------------
		// Initialize the relationship attribute on the parents.
		// As expected, "many" relationships are initialized to
		// an array and "one" relationships to null.
		// -----------------------------------------------------
		foreach ($parents as &$parent)
		{
			$parent->ignore[$include] = (strpos($eloquent->relating, 'has_many') === 0) ? array() : null;
		}

		// -----------------------------------------------------
		// Eagerly load the relationships. Phew, almost there!
		// -----------------------------------------------------
		if ($eloquent->relating == 'has_one')
		{
			static::eagerly_load_one($relationship, $parents, $eloquent->relating_key, $include);
		}
		elseif ($eloquent->relating == 'has_many')
		{
			static::eagerly_load_many($relationship, $parents, $eloquent->relating_key, $include);
		}
		elseif ($eloquent->relating == 'belongs_to')
		{
			static::eagerly_load_belonging($relationship, $parents, $eloquent->relating_key, $include);
		}
		else
		{
			static::eagerly_load_many_to_many($relationship, $parents, $eloquent->relating_key, $eloquent->relating_table, $include);
		}
	}

	/**
	 * Eagerly load a 1:1 relationship.
	 *
	 * @param  object  $relationship
	 * @param  array   $parents
	 * @param  string  $relating_key
	 * @param  string  $relating
	 * @param  string  $include
	 * @return void
	 */
	private static function eagerly_load_one($relationship, &$parents, $relating_key, $include)
	{
		// -----------------------------------------------------
		// Get the all of the related models by the parent IDs.
		//
		// Remember, the parent results are keyed by ID. So, we
		// can simply pass the keys of the array into the query.
		//
		// After getting the models, we'll match by ID.
		// -----------------------------------------------------
		foreach ($relationship->where_in($relating_key, array_keys($parents))->get() as $key => $child)
		{
			$parents[$child->$relating_key]->ignore[$include] = $child;
		}
	}

	/**
	 * Eagerly load a 1:* relationship.
	 *
	 * @param  object  $relationship
	 * @param  array   $parents
	 * @param  string  $relating_key
	 * @param  string  $relating
	 * @param  string  $include
	 * @return void
	 */
	private static function eagerly_load_many($relationship, &$parents, $relating_key, $include)
	{
		foreach ($relationship->where_in($relating_key, array_keys($parents))->get() as $key => $child)
		{
			$parents[$child->$relating_key]->ignore[$include][$child->id] = $child;
		}
	}

	/**
	 * Eagerly load a 1:1 belonging relationship.
	 *
	 * @param  object  $relationship
	 * @param  array   $parents
	 * @param  string  $relating_key
	 * @param  string  $include
	 * @return void
	 */
	private static function eagerly_load_belonging($relationship, &$parents, $relating_key, $include)
	{
		// -----------------------------------------------------
		// Gather the keys from the parent models. Since the
		// foreign key is on the parent model for this type of
		// relationship, we have to gather them individually.
		// -----------------------------------------------------
		$keys = array();

		foreach ($parents as &$parent)
		{
			$keys[] = $parent->$relating_key;
		}

		// -----------------------------------------------------
		// Get the related models.
		// -----------------------------------------------------
		$children = $relationship->where_in('id', array_unique($keys))->get();

		// -----------------------------------------------------
		// Match the child models with their parent by ID.
		// -----------------------------------------------------
		foreach ($parents as &$parent)
		{
			if (array_key_exists($parent->$relating_key, $children))
			{
				$parent->ignore[$include] = $children[$parent->$relating_key];
			}
		}
	}

	/**
	 * Eagerly load a many-to-many relationship.
	 *
	 * @param  object  $relationship
	 * @param  array   $parents
	 * @param  string  $relating_key
	 * @param  string  $relating_table
	 * @param  string  $include
	 *
	 * @return void	
	 */
	private static function eagerly_load_many_to_many($relationship, &$parents, $relating_key, $relating_table, $include)
	{
		$relationship->query->select = null;

		// -----------------------------------------------------
		// Retrieve the raw results as stdClasses.
		//
		// We also add the foreign key to the select which will allow us
		// to match the models back to their parents.
		// -----------------------------------------------------
		$children = $relationship->query
                                     ->where_in($relating_table.'.'.$relating_key, array_keys($parents))
                                     ->get(Eloquent::table(get_class($relationship)).'.*', $relating_table.'.'.$relating_key);

		$class = get_class($relationship);

		// -----------------------------------------------------
		// Create the related models.
		// -----------------------------------------------------
		foreach ($children as $child)
		{
			$related = new $class;

			$related->attributes = (array) $child;
			$related->exists = true;

			// -----------------------------------------------------
			// Remove the foreign key from the attributes since it
			// was added to the query to help us match the models.
			// -----------------------------------------------------
			unset($related->attributes[$relating_key]);

			// -----------------------------------------------------
			// Match the child model its parent by ID.
			// -----------------------------------------------------
			$parents[$child->$relating_key]->ignore[$include][$child->id] = $related;
		}
	}

}