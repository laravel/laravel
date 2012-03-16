<?php namespace Laravel\Database\Eloquent\Relationships;

class Has_Many extends Has_One_Or_Many {

	/**
	 * Get the properly hydrated results for the relationship.
	 *
	 * @return array
	 */
	public function results()
	{
		return parent::get();
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
			$parent->relationships[$relationship] = array();
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

		foreach ($children as $key => $child)
		{
			$parents[$child->$foreign]->relationships[$relationship][$child->get_key()] = $child;
		}
	}

}