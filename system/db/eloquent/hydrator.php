<?php namespace System\DB\Eloquent;

use System\DB\Eloquent;

class Hydrator {

	/**
	 * Load the array of hydrated models and their eager relationships.
	 *
	 * @param  object  $eloquent
	 * @return array
	 */
	public static function hydrate($eloquent)
	{
		$results = static::base(get_class($eloquent), $eloquent->query->get());

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
	 * The resulting model array is keyed by the primary keys of the models.
	 * This allows the models to easily be matched to their children.
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
		// Get the relationship Eloquent model.
		//
		// We temporarily spoof the belongs_to key to allow the query to be fetched without
		// any problems, since the belongs_to method actually gets the attribute.
		$eloquent->attributes[$spoof = $include.'_id'] = 0;

		$relationship = $eloquent->$include();

		unset($eloquent->attributes[$spoof]);

		// Reset the WHERE clause and bindings on the query. We'll add our own WHERE clause soon.
		$relationship->query->where = 'WHERE 1 = 1';

		$relationship->query->bindings = array();

		// Initialize the relationship attribute on the parents. As expected, "many" relationships
		// are initialized to an array and "one" relationships are initialized to null.
		foreach ($parents as &$parent)
		{
			$parent->ignore[$include] = (in_array($eloquent->relating, array('has_many', 'has_and_belongs_to_many'))) ? array() : null;
		}

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
		$keys = array();

		foreach ($parents as &$parent)
		{
			$keys[] = $parent->$relating_key;
		}

		$children = $relationship->where_in('id', array_unique($keys))->get();

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

		$relationship->query->where_in($relating_table.'.'.$relating_key, array_keys($parents));

		// The foreign key is added to the select to allow us to easily match the models back to their parents.
		$children = $relationship->query->get(array(Eloquent::table(get_class($relationship)).'.*', $relating_table.'.'.$relating_key));

		$class = get_class($relationship);

		foreach ($children as $child)
		{
			$related = new $class;

			$related->attributes = (array) $child;

			$related->exists = true;

			// Remove the foreign key since it was added to the query to help match to the children.
			unset($related->attributes[$relating_key]);

			$parents[$child->$relating_key]->ignore[$include][$child->id] = $related;
		}
	}

}