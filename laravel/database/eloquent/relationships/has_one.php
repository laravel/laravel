<?php namespace Laravel\Database\Eloquent\Relationships;

class Has_One extends Has_One_Or_Many {

	/**
	 * Get the properly hydrated results for the relationship.
	 *
	 * @return Model
	 */
	public function results()
	{
		return parent::first();
	}

	/**
	 * Initialize a relationship on an array of parent models.
	 *
	 * @param  array   $parents
	 * @param  string  $relationship
	 * @return void
	 */
	public function initialize(&$parents, $relationship)
	{
		foreach ($parents as &$parent)
		{
			$parent->relationships[$relationship] = null;
		}
	}

	/**
	 * Match eagerly loaded child models to their parent models.
	 *
	 * @param  array  $parents
	 * @param  array  $children
	 * @return void
	 */
	public function match($relationship, &$parents, $children)
	{
		$foreign = $this->foreign_key();

		$dictionary = array();

		foreach ($children as $child)
		{
			$dictionary[$child->$foreign] = $child;
		}

		foreach ($parents as $parent)
		{
			if (array_key_exists($key = $parent->get_key(), $dictionary))
			{
				$parent->relationships[$relationship] = $dictionary[$key];
			}
		}
	}

}